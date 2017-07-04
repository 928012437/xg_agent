<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Log_EweiShopV2Page extends WebPage
{
	protected function main($type = 0)
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and log.uniacid=:uniacid and m.uniacid=:uniacid and log.type=:type and log.money<>0';
		$params = array(':uniacid' => $_W['uniacid'], ':type' => $type);

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);

			if ($_GPC['searchfield'] == 'logno') {
				$condition .= ' and log.logno like :keyword';
			}
			else {
				if ($_GPC['searchfield'] == 'member') {
					$condition .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword)';
				}
			}

			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND log.createtime >= :starttime AND log.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if (!empty($_GPC['level'])) {
			$condition .= ' and m.level=' . intval($_GPC['level']);
		}

		if (!empty($_GPC['groupid'])) {
			$condition .= ' and m.groupid=' . intval($_GPC['groupid']);
		}

		if (!empty($_GPC['rechargetype'])) {
			$_GPC['rechargetype'] = trim($_GPC['rechargetype']);

			if ($_GPC['rechargetype'] == 'system1') {
				$condition .= ' AND log.rechargetype=\'system\' and log.money<0';
			}
			else {
				$condition .= ' AND log.rechargetype=:rechargetype';
				$params[':rechargetype'] = $_GPC['rechargetype'];
			}
		}

		if ($_GPC['status'] != '') {
			$condition .= ' and log.status=' . intval($_GPC['status']);
		}

		$sql = 'select log.id,m.id as mid, m.realname,m.avatar,m.weixin,log.logno,log.type,log.status,log.rechargetype,m.nickname,m.mobile,g.groupname,log.money,log.createtime,l.levelname,log.realmoney,log.deductionmoney,log.charge from ' . tablename('ewei_shop_member_log') . ' log ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=log.openid' . ' left join ' . tablename('ewei_shop_member_group') . ' g on m.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on m.level =l.id' . ' where 1 ' . $condition . ' ORDER BY log.createtime DESC ';

		if (empty($_GPC['export'])) {
			$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}

		$list = pdo_fetchall($sql, $params);

		if (!empty($list)) {
			foreach ($list as $key => $value) {
				if ($value['deductionmoney'] == 0) {
					$list[$key]['realmoney'] = $value['money'];
				}
			}
		}

		if ($_GPC['export'] == 1) {
			if ($_GPC['type'] == 1) {
				plog('finance.log.withdraw.export', '导出提现记录');
			}
			else {
				plog('finance.log.recharge.export', '导出充值记录');
			}

			foreach ($list as &$row) {
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
				$row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];

				if ($row['status'] == 0) {
					if ($row['type'] == 0) {
						$row['status'] = '未充值';
					}
					else {
						$row['status'] = '申请中';
					}
				}
				else if ($row['status'] == 1) {
					if ($row['type'] == 0) {
						$row['status'] = '充值成功';
					}
					else {
						$row['status'] = '完成';
					}
				}
				else {
					if ($row['status'] == -1) {
						if ($row['type'] == 0) {
							$row['status'] = '';
						}
						else {
							$row['status'] = '失败';
						}
					}
				}

				if ($row['rechargetype'] == 'system') {
					$row['rechargetype'] = '后台';
				}
				else if ($row['rechargetype'] == 'wechat') {
					$row['rechargetype'] = '微信';
				}
				else {
					if ($row['rechargetype'] == 'alipay') {
						$row['rechargetype'] = '支付宝';
					}
				}
			}

			unset($row);
			$columns = array();
			$columns[] = array('title' => '昵称', 'field' => 'nickname', 'width' => 12);
			$columns[] = array('title' => '姓名', 'field' => 'realname', 'width' => 12);
			$columns[] = array('title' => '手机号', 'field' => 'mobile', 'width' => 12);
			$columns[] = array('title' => '会员等级', 'field' => 'levelname', 'width' => 12);
			$columns[] = array('title' => '会员分组', 'field' => 'groupname', 'width' => 12);
			$columns[] = array('title' => empty($type) ? '充值金额' : '提现金额', 'field' => 'money', 'width' => 12);

			if (!empty($type)) {
				$columns[] = array('title' => '到账金额', 'field' => 'realmoney', 'width' => 12);
				$columns[] = array('title' => '手续费金额', 'field' => 'deductionmoney', 'width' => 12);
			}

			$columns[] = array('title' => empty($type) ? '充值时间' : '提现申请时间', 'field' => 'createtime', 'width' => 12);

			if (empty($type)) {
				$columns[] = array('title' => '充值方式', 'field' => 'rechargetype', 'width' => 12);
			}

			m('excel')->export($list, array('title' => (empty($type) ? '会员充值数据-' : '会员提现记录') . date('Y-m-d-H-i', time()), 'columns' => $columns));
		}

		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_log') . ' log ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=log.openid and m.uniacid= log.uniacid' . ' left join ' . tablename('ewei_shop_member_group') . ' g on m.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on m.level =l.id' . ' where 1 ' . $condition . ' ', $params);
		$pager = pagination($total, $pindex, $psize);
		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();
		include $this->template();
	}

	public function refund()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($log)) {
			show_json(0, '未找到记录!');
		}

		if (!empty($log['type'])) {
			show_json(0, '非充值记录!');
		}

		if ($log['rechargetype'] == 'system') {
			show_json(0, '后台充值无法退款!');
		}

		$current_credit = m('member')->getCredit($log['openid'], 'credit2');

		if ($current_credit < $log['money']) {
			show_json(0, '会员账户余额不足，无法进行退款!');
		}

		$out_refund_no = 'RR' . substr($log['logno'], 2);

		if ($log['rechargetype'] == 'wechat') {
			$result = m('finance')->refund($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100);
		}
		else {
			$result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $out_refund_no, $set['name'] . '充值退款');
		}

		if (is_error($result)) {
			show_json(0, $result['message']);
		}

		pdo_update('ewei_shop_member_log', array('status' => 3), array('id' => $id, 'uniacid' => $_W['uniacid']));
		$refundmoney = $log['money'] + $log['gives'];
		m('member')->setCredit($log['openid'], 'credit2', 0 - $refundmoney, array(0, $set['name'] . '充值退款'));
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.refund', '充值退款 ID: ' . $log['id'] . ' 金额: ' . $log['money'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1, array('url' => referer()));
	}

	public function wechat()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($log)) {
			show_json(0, '未找到记录!');
		}

		if ($log['deductionmoney'] == 0) {
			$realmoeny = $log['money'];
		}
		else {
			$realmoeny = $log['realmoney'];
		}

		$result = m('finance')->pay($log['openid'], 1, $realmoeny * 100, $log['logno'], $set['name'] . '余额提现');

		if (is_error($result)) {
			show_json(0, array('message' => $result['message']));
		}

		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.wechat', '余额提现 ID: ' . $log['id'] . ' 方式: 微信 提现金额: ' . $log['money'] . ' ,到账金额: ' . $realmoeny . ' ,手续费金额 : ' . $log['deductionmoney'] . '<br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}

	public function manual()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($log)) {
			show_json(0, '未找到记录!');
		}

		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.manual', '余额提现 方式: 手动 ID: ' . $log['id'] . ' <br/>会员信息: ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}

	public function refuse()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($log)) {
			show_json(0, '未找到记录!');
		}

		pdo_update('ewei_shop_member_log', array('status' => -1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $set['name'] . '余额提现退回'));
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.refuse', '拒绝余额度提现 ID: ' . $log['id'] . ' 金额: ' . $log['money'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}

	public function recharge()
	{
		$this->main(0);
	}

	public function withdraw()
	{
		$this->main(1);
	}
}

?>
