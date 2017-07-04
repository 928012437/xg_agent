<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Coupon_XgAgentComModel extends ComModel
{
	public function get_last_count($couponid = 0)
	{
		global $_W;
		$coupon = pdo_fetch('SELECT id,total FROM ' . tablename('ewei_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid ', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));

		if (empty($coupon)) {
			return 0;
		}

		if ($coupon['total'] == -1) {
			return -1;
		}

		$gettotal = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_coupon_data') . ' where couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $couponid, ':uniacid' => $_W['uniacid']));
		return $coupon['total'] - $gettotal;
	}

	public function creditshop($logid = 0)
	{
		global $_W;
		global $_GPC;
		$pcreditshop = p('creditshop');

		if (!$pcreditshop) {
			return NULL;
		}

		$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_creditshop_log') . ' WHERE `id`=:id and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $logid));

		if (!empty($log)) {
			$member = m('member')->getMember($log['openid']);
			$goods = $pcreditshop->getGoods($log['couponid'], $member);
			$couponlog = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $log['couponid'], 'status' => 1, 'paystatus' => 0 < $goods['money'] ? 0 : -1, 'creditstatus' => 0 < $goods['credit'] ? 0 : -1, 'createtime' => time(), 'getfrom' => 2);
			pdo_insert('ewei_shop_coupon_log', $couponlog);
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'couponid' => $log['couponid'], 'gettype' => 2, 'gettime' => time());
			pdo_insert('ewei_shop_coupon_data', $data);
			$coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
			$coupon = $this->setCoupon($coupon, time());
			$this->sendMessage($coupon, 1, $member, $_W['shopset']['creditshop']['templateid']);
			pdo_update('ewei_shop_creditshop_log', array('status' => 3), array('id' => $logid));
		}
	}

	public function poster($member, $couponid, $couponnum)
	{
		global $_W;
		global $_GPC;
		$pposter = p('poster');

		if (!$pposter) {
			return NULL;
		}

		$coupon = $this->getCoupon($couponid);

		if (empty($coupon)) {
			return NULL;
		}

		$i = 1;

		while ($i <= $couponnum) {
			$couponlog = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $couponid, 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => time(), 'getfrom' => 3);
			pdo_insert('ewei_shop_coupon_log', $couponlog);
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'couponid' => $couponid, 'gettype' => 3, 'gettime' => time());
			pdo_insert('ewei_shop_coupon_data', $data);
			++$i;
		}

		$set = m('common')->getPluginset('coupon');
		$this->sendMessage($coupon, $couponnum, $member, $set['templateid']);
	}

	public function getAvailableCoupons($type, $money = 0)
	{
		global $_W;
		global $_GPC;
		$time = time();
		$sql = 'select d.id,d.couponid,d.gettime,c.timelimit,c.timedays,c.timestart,c.timeend,c.thumb,c.couponname,c.enough,c.backtype,c.deduct,c.discount,c.backmoney,c.backcredit,c.backredpack,c.bgcolor,c.thumb from ' . tablename('ewei_shop_coupon_data') . ' d';
		$sql .= ' left join ' . tablename('ewei_shop_coupon') . ' c on d.couponid = c.id';
		$sql .= ' where d.openid=:openid and d.uniacid=:uniacid and  c.coupontype=' . $type . ' and ' . $money . '>=c.enough and d.used=0 ';
		$sql .= ' and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<=' . $time . ' && c.timeend>=' . $time . ')) order by d.gettime desc';
		$list = set_medias(pdo_fetchall($sql, array(':openid' => $_W['openid'], ':uniacid' => $_W['uniacid'])), 'thumb');

		foreach ($list as &$row) {
			$row['thumb'] = tomedia($row['thumb']);
			$row['timestr'] = '永久有效';

			if (empty($row['timelimit'])) {
				if (!empty($row['timedays'])) {
					$row['timestr'] = date('Y-m-d H:i', $row['gettime'] + ($row['timedays'] * 86400));
				}
			}
			else if ($time <= $row['timestart']) {
				$row['timestr'] = date('Y-m-d H:i', $row['timestart']) . '-' . date('Y-m-d H:i', $row['timeend']);
			}
			else {
				$row['timestr'] = date('Y-m-d H:i', $row['timeend']);
			}

			if ($row['backtype'] == 0) {
				$row['backstr'] = '立减';
				$row['css'] = 'deduct';
				$row['backmoney'] = $row['deduct'];
				$row['backpre'] = true;
			}
			else if ($row['backtype'] == 1) {
				$row['backstr'] = '折';
				$row['css'] = 'discount';
				$row['backmoney'] = $row['discount'];
			}
			else {
				if ($row['backtype'] == 2) {
					if (0 < $row['backredpack']) {
						$row['backstr'] = '返现';
						$row['css'] = 'redpack';
						$row['backmoney'] = $row['backredpack'];
						$row['backpre'] = true;
					}
					else if (0 < $row['backmoney']) {
						$row['backstr'] = '返利';
						$row['css'] = 'money';
						$row['backmoney'] = $row['backmoney'];
						$row['backpre'] = true;
					}
					else {
						if (!empty($row['backcredit'])) {
							$row['backstr'] = '返积分';
							$row['css'] = 'credit';
							$row['backmoney'] = $row['backcredit'];
						}
					}
				}
			}
		}

		unset($row);
		return $list;
	}

	public function payResult($logno)
	{
		global $_W;

		if (empty($logno)) {
			return error(-1);
		}

		$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_coupon_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));

		if (empty($log)) {
			return error(-1, '服务器错误!');
		}

		if (1 <= $log['status']) {
			return true;
		}

		$coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
		$coupon = $this->setCoupon($coupon, time());

		if (empty($coupon['gettype'])) {
			return error(-1, '无法领取');
		}

		if ($coupon['total'] != -1) {
			if ($coupon['total'] <= 0) {
				return error(-1, '优惠券数量不足');
			}
		}

		if (!$coupon['canget']) {
			return error(-1, '您已超出领取次数限制');
		}

		if (empty($log['status'])) {
			$update = array();
			if ((0 < $coupon['credit']) && empty($log['creditstatus'])) {
				m('member')->setCredit($log['openid'], 'credit1', 0 - $coupon['credit'], '购买优惠券扣除积分 ' . $coupon['credit']);
				$update['creditstatus'] = 1;
			}

			if ((0 < $coupon['money']) && empty($log['paystatus'])) {
				if ($coupon['paytype'] == 0) {
					m('member')->setCredit($log['openid'], 'credit2', 0 - $coupon['money'], '购买优惠券扣除余额 ' . $coupon['money']);
				}

				$update['paystatus'] = 1;
			}

			$update['status'] = 1;
			pdo_update('ewei_shop_coupon_log', $update, array('id' => $log['id']));
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'couponid' => $log['couponid'], 'gettype' => $log['getfrom'], 'gettime' => time());
			pdo_insert('ewei_shop_coupon_data', $data);
			$member = m('member')->getMember($log['openid']);
			$set = m('common')->getPluginset('coupon');
			$this->sendMessage($coupon, 1, $member, $set['templateid']);
		}

		$url = mobileUrl('member', NULL, true);

		if ($coupon['coupontype'] == 0) {
			$coupon['url'] = mobileUrl('goods', NULL, true);
		}
		else {
			$coupon['url'] = mobileUrl('member/recharge', NULL, true);
		}

		return $coupon;
	}

	public function sendMessage($coupon, $send_total, $member, $templateid = '', $account = NULL)
	{
		global $_W;
		$articles = array();
		$title = str_replace('[nickname]', $member['nickname'], $coupon['resptitle']);
		$desc = str_replace('[nickname]', $member['nickname'], $coupon['respdesc']);
		$title = str_replace('[total]', $send_total, $title);
		$desc = str_replace('[total]', $send_total, $desc);
		$url = (empty($coupon['respurl']) ? mobileUrl('sale/coupon/my', NULL, true) : $coupon['respurl']);

		if (!empty($coupon['resptitle'])) {
			$articles[] = array('title' => urlencode($title), 'description' => urlencode($desc), 'url' => $url, 'picurl' => tomedia($coupon['respthumb']));
		}

		if (!empty($articles)) {
			$resp = m('message')->sendNews($member['openid'], $articles, $account);

			if (is_error($resp)) {
				$msg = array(
					'keyword1' => array('value' => $title, 'color' => '#73a68d'),
					'keyword2' => array('value' => $desc, 'color' => '#73a68d')
					);

				if (!empty($templateid)) {
					m('message')->sendTplNotice($member['openid'], $templateid, $msg, $url);
				}
			}
		}
	}

	public function sendBackMessage($openid, $coupon, $gives)
	{
		global $_W;

		if (empty($gives)) {
			return NULL;
		}

		$set = m('common')->getPluginset('coupon');
		$templateid = $set['templateid'];
		$content = '您的优惠券【' . $coupon['couponname'] . '】已返利 ';
		$givestr = '';

		if (isset($gives['credit'])) {
			$givestr .= ' ' . $gives['credit'] . '个积分';
		}

		if (isset($gives['money'])) {
			if (!empty($givestr)) {
				$givestr .= '，';
			}

			$givestr .= $gives['money'] . '元余额';
		}

		if (isset($gives['redpack'])) {
			if (!empty($givestr)) {
				$givestr .= '，';
			}

			$givestr .= $gives['redpack'] . '元现金';
		}

		$content .= $givestr;
		$content .= '，请查看您的账户，谢谢!';
		$msg = array(
			'keyword1' => array('value' => '优惠券返利', 'color' => '#73a68d'),
			'keyword2' => array('value' => $content, 'color' => '#73a68d')
			);
		$url = mobileUrl('member', NULL, true);

		if (!empty($templateid)) {
			m('message')->sendTplNotice($openid, $templateid, $msg, $url);
			return NULL;
		}

		m('message')->sendCustomNotice($openid, $msg, $url);
	}

	public function sendReturnMessage($openid, $coupon)
	{
		global $_W;
		$set = m('common')->getPluginset('coupon');
		$templateid = $set['templateid'];
		$msg = array(
			'keyword1' => array('value' => '优惠券退回', 'color' => '#73a68d'),
			'keyword2' => array('value' => '您的优惠券【' . $coupon['couponname'] . '】已退回您的账户，您可以再次使用, 谢谢!', 'color' => '#73a68d')
			);
		$url = mobileUrl('sale/coupon/my', NULL, true);

		if (!empty($templateid)) {
			m('message')->sendTplNotice($openid, $templateid, $msg, $url);
			return NULL;
		}

		m('message')->sendCustomNotice($openid, $msg, $url);
	}

	public function useRechargeCoupon($log)
	{
		global $_W;

		if (empty($log['couponid'])) {
			return NULL;
		}

		$data = pdo_fetch('select id,openid,couponid,used from ' . tablename('ewei_shop_coupon_data') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $log['couponid'], ':uniacid' => $_W['uniacid']));

		if (empty($data)) {
			return NULL;
		}

		if (!empty($data['used'])) {
			return NULL;
		}

		$coupon = pdo_fetch('select enough,backcredit,backmoney,backredpack,couponname from ' . tablename('ewei_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $data['couponid'], ':uniacid' => $_W['uniacid']));

		if (empty($coupon)) {
			return NULL;
		}

		if ((0 < $coupon['enough']) && ($log['money'] < $coupon['enough'])) {
			return NULL;
		}

		$gives = array();
		$backcredit = $coupon['backcredit'];

		if (!empty($backcredit)) {
			if (strexists($backcredit, '%')) {
				$backcredit = intval((floatval(str_replace('%', '', $backcredit)) / 100) * $log['money']);
			}
			else {
				$backcredit = intval($backcredit);
			}

			if (0 < $backcredit) {
				$gives['credit'] = $backcredit;
				m('member')->setCredit($data['openid'], 'credit1', $backcredit, array(0, '充值优惠券返积分'));
			}
		}

		$backmoney = $coupon['backmoney'];

		if (!empty($backmoney)) {
			if (strexists($backmoney, '%')) {
				$backmoney = round(floatval((floatval(str_replace('%', '', $backmoney)) / 100) * $log['money']), 2);
			}
			else {
				$backmoney = round(floatval($backmoney), 2);
			}

			if (0 < $backmoney) {
				$gives['money'] = $backmoney;
				m('member')->setCredit($data['openid'], 'credit2', $backmoney, array(0, '充值优惠券返利'));
			}
		}

		$backredpack = $coupon['backredpack'];

		if (!empty($backredpack)) {
			if (strexists($backredpack, '%')) {
				$backredpack = round(floatval((floatval(str_replace('%', '', $backredpack)) / 100) * $log['money']), 2);
			}
			else {
				$backredpack = round(floatval($backredpack), 2);
			}

			if (0 < $backredpack) {
				$gives['redpack'] = $backredpack;
				$backredpack = intval($backredpack * 100);
				m('finance')->pay($data['openid'], 1, $backredpack, '', '充值优惠券-返现金', false);
			}
		}

		pdo_update('ewei_shop_coupon_data', array('used' => 1, 'usetime' => time(), 'ordersn' => $log['logno']), array('id' => $data['id']));
		$this->sendBackMessage($log['openid'], $coupon, $gives);
	}

	public function consumeCouponCount($openid, $money = 0)
	{
		global $_W;
		global $_GPC;
		$time = time();
		$sql = 'select count(*) from ' . tablename('ewei_shop_coupon_data') . ' d ' . '  left join ' . tablename('ewei_shop_coupon') . ' c on d.couponid = c.id ' . '  where d.openid=:openid and d.uniacid=:uniacid and  c.coupontype=0 and ' . $money . '>=c.enough and d.used=0 ' . ' and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<=' . $time . ' && c.timeend>=' . $time . '))';
		return pdo_fetchcolumn($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
	}

	public function rechargeCouponCount($openid, $money = 0)
	{
		global $_W;
		global $_GPC;
		$time = time();
		$sql = 'select count(*) from ' . tablename('ewei_shop_coupon_data') . ' d ' . '  left join ' . tablename('ewei_shop_coupon') . ' c on d.couponid = c.id ' . '  where d.openid=:openid and d.uniacid=:uniacid and  c.coupontype=1 and ' . $money . '>=c.enough and d.used=0 ' . ' and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<=' . $time . ' && c.timeend>=' . $time . '))';
		return pdo_fetchcolumn($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
	}

	public function useConsumeCoupon($orderid = 0)
	{
		global $_W;
		global $_GPC;

		if (empty($orderid)) {
			return NULL;
		}

		$order = pdo_fetch('select ordersn,createtime,couponid from ' . tablename('ewei_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));

		if (empty($order)) {
			return NULL;
		}

		$coupon = false;

		if (!empty($order['couponid'])) {
			$coupon = $this->getCouponByDataID($order['couponid']);
		}

		if (empty($coupon)) {
			return NULL;
		}

		pdo_update('ewei_shop_coupon_data', array('used' => 1, 'usetime' => $order['createtime'], 'ordersn' => $order['ordersn']), array('id' => $order['couponid']));
	}

	public function returnConsumeCoupon($order)
	{
		global $_W;

		if (!is_array($order)) {
			$order = pdo_fetch('select id,openid,ordersn,createtime,couponid,status,finishtime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => intval($order), ':uniacid' => $_W['uniacid']));
		}

		if (empty($order)) {
			return NULL;
		}

		$coupon = $this->getCouponByDataID($order['couponid']);

		if (empty($coupon)) {
			return NULL;
		}

		if (!empty($coupon['returntype'])) {
			if (!empty($coupon['used'])) {
				pdo_update('ewei_shop_coupon_data', array('used' => 0, 'usetime' => 0, 'ordersn' => ''), array('id' => $order['couponid']));
				$this->sendReturnMessage($order['openid'], $coupon);
			}
		}
	}

	public function backConsumeCoupon($order)
	{
		global $_W;

		if (!is_array($order)) {
			$order = pdo_fetch('select id,openid,ordersn,createtime,couponid,status,finishtime,virtual from ' . tablename('ewei_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => intval($order), ':uniacid' => $_W['uniacid']));
		}

		if (empty($order)) {
			return NULL;
		}

		$couponid = $order['couponid'];

		if (empty($couponid)) {
			return NULL;
		}

		$coupon = $this->getCouponByDataID($order['couponid']);

		if (empty($coupon)) {
			return NULL;
		}

		if (!empty($coupon['back'])) {
			return NULL;
		}

		$gives = array();
		$canback = false;
		if (($order['status'] == 1) && ($coupon['backwhen'] == 2)) {
			$canback = true;
		}
		else {
			if ($order['status'] == 3) {
				if (!empty($order['virtual'])) {
					$canback = true;
				}
				else if ($coupon['backwhen'] == 1) {
					$canback = true;
				}
				else {
					if ($coupon['backwhen'] == 0) {
						$canback = true;
						$tradeset = m('common')->getSysset('trade');
						$refunddays = intval($tradeset['refunddays']);

						if (0 < $refunddays) {
							$days = intval((time() - $order['finishtime']) / 3600 / 24);

							if ($days <= $refunddays) {
								$canback = false;
							}
						}
					}
				}
			}
		}

		if ($canback) {
			$ordermoney = pdo_fetchcolumn('select ifnull( sum(og.realprice),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid ' . ' where o.id=:orderid and o.openid=:openid and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $order['openid'], ':orderid' => $order['id']));
			$backcredit = $coupon['backcredit'];

			if (!empty($backcredit)) {
				if (strexists($backcredit, '%')) {
					$backcredit = intval((floatval(str_replace('%', '', $backcredit)) / 100) * $ordermoney);
				}
				else {
					$backcredit = intval($backcredit);
				}

				if (0 < $backcredit) {
					$gives['credit'] = $backcredit;
					m('member')->setCredit($order['openid'], 'credit1', $backcredit, array(0, '充值优惠券返积分'));
				}
			}

			$backmoney = $coupon['backmoney'];

			if (!empty($backmoney)) {
				if (strexists($backmoney, '%')) {
					$backmoney = round(floatval((floatval(str_replace('%', '', $backmoney)) / 100) * $ordermoney), 2);
				}
				else {
					$backmoney = round(floatval($backmoney), 2);
				}

				if (0 < $backmoney) {
					$gives['money'] = $backmoney;
					m('member')->setCredit($order['openid'], 'credit2', $backmoney, array(0, '购物优惠券返利'));
				}
			}

			$backredpack = $coupon['backredpack'];

			if (!empty($backredpack)) {
				if (strexists($backredpack, '%')) {
					$backredpack = round(floatval((floatval(str_replace('%', '', $backredpack)) / 100) * $ordermoney), 2);
				}
				else {
					$backredpack = round(floatval($backredpack), 2);
				}

				if (0 < $backredpack) {
					$gives['redpack'] = $backredpack;
					$backredpack = intval($backredpack * 100);
					m('finance')->pay($order['openid'], 1, $backredpack, '', '购物优惠券-返现金', false);
				}
			}

			pdo_update('ewei_shop_coupon_data', array('back' => 1, 'backtime' => time()), array('id' => $order['couponid']));
			$this->sendBackMessage($order['openid'], $coupon, $gives);
		}
	}

	public function getCoupon($couponid = 0)
	{
		global $_W;
		return pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
	}

	public function getCouponByDataID($dataid = 0)
	{
		global $_W;
		$data = pdo_fetch('select id,openid,couponid,used,back,backtime from ' . tablename('ewei_shop_coupon_data') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dataid, ':uniacid' => $_W['uniacid']));

		if (empty($data)) {
			return false;
		}

		$coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $data['couponid'], ':uniacid' => $_W['uniacid']));

		if (empty($coupon)) {
			return false;
		}

		$coupon['back'] = $data['back'];
		$coupon['backtime'] = $data['backtime'];
		$coupon['used'] = $data['used'];
		$coupon['usetime'] = $data['usetime'];
		return $coupon;
	}

	public function setCoupon($row, $time, $withOpenid = true)
	{
		global $_W;

		if ($withOpenid) {
			$openid = $_W['openid'];
		}

		$row['free'] = false;
		$row['past'] = false;
		$row['thumb'] = tomedia($row['thumb']);
		if ((0 < $row['money']) && (0 < $row['credit'])) {
			$row['getstatus'] = 0;
			$row['gettypestr'] = '购买';
		}
		else if (0 < $row['money']) {
			$row['getstatus'] = 1;
			$row['gettypestr'] = '购买';
		}
		else if (0 < $row['credit']) {
			$row['getstatus'] = 2;
			$row['gettypestr'] = '兑换';
		}
		else {
			$row['getstatus'] = 3;
			$row['gettypestr'] = '领取';
		}

		$row['timestr'] = '0';

		if (empty($row['timelimit'])) {
			if (!empty($row['timedays'])) {
				$row['timestr'] = 1;
			}
		}
		else if ($time <= $row['timestart']) {
			$row['timestr'] = date('Y-m-d', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
		}
		else {
			$row['timestr'] = date('Y-m-d', $row['timeend']);
		}

		$row['css'] = 'deduct';

		if ($row['backtype'] == 0) {
			$row['backstr'] = '立减';
			$row['css'] = 'deduct';
			$row['backpre'] = true;
			$row['_backmoney'] = $row['deduct'];
		}
		else if ($row['backtype'] == 1) {
			$row['backstr'] = '折';
			$row['css'] = 'discount';
			$row['_backmoney'] = $row['discount'];
		}
		else {
			if ($row['backtype'] == 2) {
				if (!empty($row['backredpack'])) {
					$row['backstr'] = '返现';
					$row['css'] = 'redpack';
					$row['backpre'] = true;
					$row['_backmoney'] = $row['backredpack'];
				}
				else if (!empty($row['backmoney'])) {
					$row['backstr'] = '返利';
					$row['css'] = 'money';
					$row['backpre'] = true;
					$row['_backmoney'] = $row['backmoney'];
				}
				else {
					if (!empty($row['backcredit'])) {
						$row['backstr'] = '返积分';
						$row['css'] = 'credit';
						$row['_backmoney'] = $row['backcredit'];
					}
				}
			}
		}

		if ($withOpenid) {
			$row['cangetmax'] = -1;
			$row['canget'] = true;

			if (0 < $row['getmax']) {
				$gets = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_coupon_data') . ' where couponid=:couponid and openid=:openid and uniacid=:uniacid and gettype=1 limit 1', array(':couponid' => $row['id'], ':openid' => $openid, ':uniacid' => $_W['uniacid']));
				$row['cangetmax'] = $row['getmax'] - $gets;

				if ($row['cangetmax'] <= 0) {
					$row['cangetmax'] = 0;
					$row['canget'] = false;
				}
			}
		}

		return $row;
	}

	public function setMyCoupon($row, $time)
	{
		global $_W;
		$row['past'] = false;
		$row['thumb'] = tomedia($row['thumb']);
		$row['timestr'] = '';

		if (empty($row['timelimit'])) {
			if (!empty($row['timedays'])) {
				$row['timestr'] = date('Y-m-d', $row['gettime'] + ($row['timedays'] * 86400));

				if (($row['gettime'] + ($row['timedays'] * 86400)) < $time) {
					$row['past'] = true;
				}
			}
		}
		else {
			if ($time <= $row['timestart']) {
				$row['timestr'] = date('Y-m-d H:i', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
			}
			else {
				$row['timestr'] = date('Y-m-d H:i', $row['timeend']);
			}

			if ($row['timeend'] < $time) {
				$row['past'] = true;
			}
		}

		$row['css'] = 'deduct';

		if ($row['backtype'] == 0) {
			$row['backstr'] = '立减';
			$row['css'] = 'deduct';
			$row['backpre'] = true;
			$row['_backmoney'] = $row['deduct'];
		}
		else if ($row['backtype'] == 1) {
			$row['backstr'] = '折';
			$row['css'] = 'discount';
			$row['_backmoney'] = $row['discount'];
		}
		else {
			if ($row['backtype'] == 2) {
				if (!empty($row['backredpack'])) {
					$row['backstr'] = '返现';
					$row['css'] = 'redpack';
					$row['backpre'] = true;
					$row['_backmoney'] = $row['backredpack'];
				}
				else if (!empty($row['backmoney'])) {
					$row['backstr'] = '返利';
					$row['css'] = 'money';
					$row['backpre'] = true;
					$row['_backmoney'] = $row['backmoney'];
				}
				else {
					if (!empty($row['backcredit'])) {
						$row['backstr'] = '返积分';
						$row['css'] = 'credit';
						$row['_backmoney'] = $row['backcredit'];
					}
				}
			}
		}

		if ($row['past']) {
			$row['css'] = 'past';
		}

		return $row;
	}

	public function setShare()
	{
		global $_W;
		global $_GPC;
		$set = m('common')->getPluginset('coupon');
		$openid = $_W['openid'];
		$url = mobileUrl('sale/coupon', NULL, true);
		$_W['shopshare'] = array('title' => $set['title'], 'imgUrl' => tomedia($set['icon']), 'desc' => $set['desc'], 'link' => $url);

		if (p('commission')) {
			$pset = p('commission')->getSet();

			if (!empty($pset['level'])) {
				$member = m('member')->getMember($openid);
				if (!empty($member) && ($member['status'] == 1) && ($member['isagent'] == 1)) {
					$_W['shopshare']['link'] = $url . '&mid=' . $member['id'];
					if (empty($pset['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) {
						$trigger = true;
						return NULL;
					}
				}
				else {
					if (!empty($_GPC['mid'])) {
						$_W['shopshare']['link'] = $url . '&mid=' . $_GPC['id'];
					}
				}
			}
		}
	}

	public function perms()
	{
		return array(
	'coupon' => array(
		'text' => $this->getName(),
		'isplugin'                => true,
		'child'                   => array(
			'coupon'   => array('text' => '优惠券', 'view' => '查看', 'add' => '添加优惠券-log', 'edit' => '编辑优惠券-log', 'delete' => '删除优惠券-log', 'send' => '发放优惠券-log'),
			'category' => array('text' => '分类', 'view' => '查看', 'add' => '添加分类-log', 'edit' => '编辑分类-log', 'delete' => '删除分类-log'),
			'log'      => array('text' => '优惠券记录', 'view' => '查看', 'export' => '导出-log'),
			'center'   => array('text' => '领券中心设置', 'view' => '查看设置', 'save' => '保存设置-log'),
			'set'      => array('text' => '基础设置', 'view' => '查看设置', 'save' => '保存设置-log')
			)
		)
	);
	}
}

?>
