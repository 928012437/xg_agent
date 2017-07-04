<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Verify_XgAgentComModel extends ComModel
{
	public function createQrcode($orderid = 0)
	{
		global $_W;
		global $_GPC;
		$path = IA_ROOT . '/addons/xg_agent/data/qrcode/' . $_W['uniacid'];

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$url = mobileUrl('verify/detai', array('id' => $orderid));
		$file = 'order_verify_qrcode_' . $orderid . '.png';
		$qrcode_file = $path . '/' . $file;

		if (!is_file($qrcode_file)) {
			require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
			QRcode::png($url, $qrcode_file, QR_ECLEVEL_H, 4);
		}

		return $_W['siteroot'] . '/addons/xg_agent/data/qrcode/' . $_W['uniacid'] . '/' . $file;
	}

	public function allow($orderid, $times = 0, $verifycode = '', $openid = '')
	{
		global $_W;
		global $_GPC;

		if (empty($openid)) {
			$openid = $_W['openid'];
		}

		$uniacid = $_W['uniacid'];
		$store = false;
		$lastverifys = 0;
		$verifyinfo = false;

		if ($times <= 0) {
			$times = 1;
		}

		$saler = pdo_fetch('select * from ' . tablename('xg_agent_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));

		if (empty($saler)) {
			return error(-1, '无核销权限!');
		}

		$order = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_log') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $orderid, ':uniacid' => $uniacid));

		if (empty($order)) {
			return error(-1, '订单不存在!');
		}


		$allgoods = pdo_fetchall('select * from ' . tablename('xg_agent_creditshop_goods') . " where uniacid=:uniacid and id=:id", array(':uniacid' => $uniacid, ':id' => $order['goodsid']));

		if (empty($allgoods)) {
			return error(-1, '订单异常!');
		}

		$goods = $allgoods[0];

		if ($goods['isverify']==0 && $goods['dispatchtype']==0) {
			return error(-1, '订单无需核销!');
		}

		if ($goods['isverify']) {
			if (count($allgoods) != 1) {
				return error(-1, '核销单异常!');
			}

			$storeids = array();

			if (!empty($goods['storeids'])) {
				$storeids = explode(',', $goods['storeids']);
			}

			if (!empty($storeids)) {
				if (!empty($saler['storeid'])) {
					if (!in_array($saler['storeid'], $storeids)) {
						return error(-1, '您无此门店的核销权限!');
					}
				}
			}

			if ($order['verifytype'] == 0) {
				if (!empty($order['verified'])) {
					return error(-1, '此订单已核销!');
				}
			}
			else if ($order['verifytype'] == 1) {
				$verifyinfo = iunserializer($order['verifyinfo']);

				if (!is_array($verifyinfo)) {
					$verifyinfo = array();
				}

				$lastverifys = $goods['total'] - count($verifyinfo);

				if ($lastverifys <= 0) {
					return error(-1, '此订单已全部使用!');
				}

				if ($lastverifys < $times) {
					return error(-1, '最多核销 ' . $lastverifys . ' 次!');
				}
			}
			else {
				if ($order['verifytype'] == 2) {
					$verifyinfo = iunserializer($order['verifyinfo']);
					$verifys = 0;

					foreach ($verifyinfo as $v) {
						if (!empty($verifycode) && ($v['verifycode'] == $verifycode)) {
							if ($v['verified']) {
								return error(-1, '消费码 ' . $verifycode . ' 已经使用!');
							}
						}

						if ($v['verified']) {
							++$verifys;
						}
					}

					$lastverifys = count($verifyinfo) - $verifys;

					if (count($verifyinfo) <= $verifys) {
						return error(-1, '消费码都已经使用过了!');
					}
				}
			}

			if (!empty($saler['storeid'])) {
				$store = pdo_fetch('select * from ' . tablename('xg_agent_store') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $saler['storeid'], ':uniacid' => $_W['uniacid']));
			}
		}
		else {
			if ($order['dispatchtype'] == 1) {
				if (3 <= $order['status']) {
					return error(-1, '订单已经完成，无法进行自提!');
				}

				if (!empty($order['storeid'])) {
					$store = pdo_fetch('select * from ' . tablename('xg_agent_store') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $order['storeid'], ':uniacid' => $_W['uniacid']));
				}

				if (empty($store)) {
					return error(-1, '订单未选择自提门店!');
				}

				if (!empty($saler['storeid'])) {
					if ($saler['storeid'] != $order['storeid']) {
						return error(-1, '您无此门店的自提权限!');
					}
				}
			}
		}

		$carrier = unserialize($order['carrier']);
		$rule=pdo_get('xg_agent_rule',array('uniacid'=>$uniacid));
		return array('order' => $order, 'store' => $store, 'saler' => $saler, 'lastverifys' => $lastverifys, 'allgoods' => $allgoods, 'goods' => $goods, 'verifyinfo' => $verifyinfo, 'carrier' => $carrier,'mtitle'=>$rule['rirle']);
	}

	public function verify($orderid = 0, $times = 0, $verifycode = '', $openid = '')
	{
		global $_W;
		global $_GPC;
		$current_time = time();

		if (empty($openid)) {
			$openid = $_W['openid'];
		}

		$data = $this->allow($orderid, $times, $openid);

		if (is_error($data)) {
			return NULL;
		}

		extract($data);

		if ($goods['isverify']>0 && $order['status']==2) {
			$id = intval($order['id']);
			$verifynum = 1;
			$log = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
			$verifytotal = pdo_fetchcolumn('select count(1) from ' . tablename('xg_agent_creditshop_verify') . ' where logid=:logid and uniacid=:uniacid', array(':logid' => $id, ':uniacid' => $_W['uniacid']));
			if (empty($log))
			{
				show_json(0, '兑换记录不存在!');
			}
			if (empty($log['status']))
			{
				show_json(0, '无效兑换记录!');
			}
			$log['verifynum'] = (($log['verifynum'] - $verifytotal ? $log['verifynum'] - $verifytotal : 0));
			if (($log['verifynum'] - $verifytotal) < $verifynum)
			{
				show_json(0, '此记录剩余核销 ' . $log['verifynum'] . ' 次');
			}
			$member = m('member')->getMember($log['openid']);
			$goods = p('creditshop')->getGoods($log['goodsid'], $member);
			if (empty($goods['id']))
			{
				show_json(0, '商品记录不存在!');
			}
			if (!(empty($goods['type'])))
			{
				if ($log['status'] <= 1)
				{
					show_json(0, '未中奖，不能兑换!');
				}
			}
			$store = false;
			$address = false;
			if ($goods['isverify'])
			{

			}
			else
			{
				$address = iunserializer($log['address']);
				if (!(is_array($address)))
				{
					$address = pdo_fetch('select realname,mobile,address,province,city,area from ' . tablename('xg_agent_member_address') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $log['addressid'], ':uniacid' => $_W['uniacid']));
				}
			}
			if ((0 < $goods['money']) && empty($log['paystatus']))
			{
				show_json(0, '未支付，无法进行兑换!');
			}
			if ((0 < $goods['dispatch']) && empty($log['dispatchstatus']))
			{
				show_json(0, '未支付运费，无法进行兑换!');
			}

				if ($goods['goodstype'] == 0)
				{
					if ((0 < $log['verifynum']) && ($verifynum <= 0) && (0 < $goods['isverify']))
					{
						show_json(0, '最少兑换1次!');
					}
					if ((($log['verifynum'] - $verifytotal) <= 0) && (0 < $goods['isverify']))
					{
						show_json(0, '此记录已兑换过了!');
					}
					pdo_update('xg_agent_creditshop_log', array('status' => 3, 'usetime' => time(), 'time_send' => time(), 'expresscom' => $_GPC['expresscom'], 'expresssn' => $_GPC['expresssn'], 'express' => $_GPC['express']), array('id' => $id));
					if ($goods['verifytype'] == 0)
					{
						pdo_update('xg_agent_creditshop_log', array('status' => 3, 'usetime' => time(), 'verifyopenid' => 'system', 'time_finish' => time()), array('id' => $id));
						$data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'logid' => $id, 'verifycode' => $log['eno'], 'storeid' => $log['storeid'], 'verifier' => 'system', 'isverify' => 1, 'verifytime' => time());
						pdo_insert('xg_agent_creditshop_verify', $data);
					}
					else if ($goods['verifytype'] == 1)
					{
						if ($log['status'] != 3)
						{
							pdo_update('xg_agent_creditshop_log', array('status' => 3, 'usetime' => time(), 'verifyopenid' => 'system', 'time_finish' => time()), array('id' => $id));
						}
						$i = 1;
						if ($i <= $verifynum)
						{
							$data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'logid' => $id, 'verifycode' => $log['eno'], 'storeid' => $log['storeid'], 'verifier' => 'system', 'isverify' => 1, 'verifytime' => time());
							pdo_insert('xg_agent_creditshop_verify', $data);
							++$i;
							if ($goods['goodstype'] == 3)
							{
								$money = abs($goods['grant2']);
								$setting = uni_setting($_W['uniacid'], array('payment'));
								if (!(is_array($setting['payment'])))
								{
									show_json(0, '没有设定支付参数!');
								}
								$sec = m('common')->getSec();
								$sec = iunserializer($sec['sec']);
								$certs = $sec;
								$wechat = $setting['payment']['wechat'];
								$sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
								$row = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
								$params = array('openid' => $log['openid'], 'tid' => $log['logno'] . rand(1, 100), 'send_name' => '积分商城红包兑换', 'money' => $money, 'wishing' => '红包领到手抽筋，别人加班你加薪!', 'act_name' => '积分商城红包兑换', 'remark' => '积分商城红包兑换');
								$wechat = array('appid' => $row['key'], 'mchid' => $wechat['mchid'], 'apikey' => $wechat['apikey'], 'certs' => $certs);
								$err = m('common')->sendredpack($params, $wechat);
								if (is_error($err))
								{
									show_json(0, $err['message']);
								}
								else
								{
									$status = 3;
									$update['time_finish'] = time();
								}
								$update['status'] = $status;
								pdo_update('xg_agent_creditshop_log', $update, array('id' => $id));
							}
						}
					}
				}
				else
				{
					$money = abs($goods['grant2']);
					$setting = uni_setting($_W['uniacid'], array('payment'));
					show_json(0, '没有设定支付参数!');
					$sec = m('common')->getSec();
					$sec = iunserializer($sec['sec']);
					$certs = $sec;
					$wechat = $setting['payment']['wechat'];
					$sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
					$row = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
					$params = array('openid' => $log['openid'], 'tid' => $log['logno'] . rand(1, 100), 'send_name' => '积分商城红包兑换', 'money' => $money, 'wishing' => '红包领到手抽筋，别人加班你加薪!', 'act_name' => '积分商城红包兑换', 'remark' => '积分商城红包兑换');
					$wechat = array('appid' => $row['key'], 'mchid' => $wechat['mchid'], 'apikey' => $wechat['apikey'], 'certs' => $certs);
					$err = m('common')->sendredpack($params, $wechat);
					show_json(0, $err['message']);
					$status = 3;
					$update['time_finish'] = time();
					$update['status'] = $status;
					pdo_update('xg_agent_creditshop_log', $update, array('id' => $id));
				}
				$this->model->sendMessage($id);
				plog('creditshop.log.doexchange', '积分商城兑换 兑换记录ID: ' . $id);

		}
		else {
			return false;
		}

		return true;
	}

	public function perms()
	{
		return array(
	'verify' => array(
		'text'     => $this->getName(),
		'isplugin' => true,
		'child'    => array(
			'keyword' => array('text' => '关键词设置-log'),
			'store'   => array('text' => '门店', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'),
			'saler'   => array('text' => '核销员', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log')
			)
		)
	);
	}
}

?>
