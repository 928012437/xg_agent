<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class List_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and dm.uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['mid'])) {
			$condition .= ' and dm.id=:mid';
			$params[':mid'] = intval($_GPC['mid']);
		}

		if (!empty($_GPC['realname'])) {
			$_GPC['realname'] = trim($_GPC['realname']);
			$condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.mobile like :realname)';
			$params[':realname'] = '%' . $_GPC['realname'] . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND dm.createtime >= :starttime AND dm.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if ($_GPC['level'] != '') {
			$condition .= ' and dm.level=' . intval($_GPC['level']);
		}

		if ($_GPC['groupid'] != '') {
			$condition .= ' and dm.groupid=' . intval($_GPC['groupid']);
		}

		if ($_GPC['followed'] != '') {
			if ($_GPC['followed'] == 2) {
				$condition .= ' and f.follow=0 and dm.uid<>0';
			}
			else {
				$condition .= ' and f.follow=' . intval($_GPC['followed']);
			}
		}

		if ($_GPC['isblack'] != '') {
			$condition .= ' and dm.isblack=' . intval($_GPC['isblack']);
		}

		$sql = 'select dm.*,l.levelname,g.groupname,a.nickname as agentnickname,a.avatar as agentavatar from ' . tablename('ewei_shop_member') . ' dm ' . ' left join ' . tablename('ewei_shop_member_group') . ' g on dm.groupid=g.id' . ' left join ' . tablename('ewei_shop_member') . ' a on a.id=dm.agentid' . ' left join ' . tablename('ewei_shop_member_level') . ' l on dm.level =l.id' . ' left join ' . tablename('mc_mapping_fans') . 'f on f.openid=dm.openid  and f.uniacid=' . $_W['uniacid'] . ' where 1 ' . $condition . '  ORDER BY dm.id DESC';

		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}

		$list = pdo_fetchall($sql, $params);

		foreach ($list as &$row) {
			$row['levelname'] = empty($row['levelname']) ? (empty($shop['levelname']) ? '普通会员' : $shop['levelname']) : $row['levelname'];
			$row['ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
			$row['ordermoney'] = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
			$row['credit1'] = m('member')->getCredit($row['openid'], 'credit1');
			$row['credit2'] = m('member')->getCredit($row['openid'], 'credit2');
			$row['followed'] = m('user')->followed($row['openid']);
		}

		unset($row);

		if ($_GPC['export'] == '1') {
			plog('member.list', '导出会员数据');

			foreach ($list as &$row) {
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
				$row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];
			}

			unset($row);
			m('excel')->export($list, array(
	'title'   => '会员数据-' . date('Y-m-d-H-i', time()),
	'columns' => array(
		array('title' => '昵称', 'field' => 'nickname', 'width' => 12),
		array('title' => '姓名', 'field' => 'realname', 'width' => 12),
		array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
		array('title' => 'openid', 'field' => 'openid', 'width' => 24),
		array('title' => '会员等级', 'field' => 'levelname', 'width' => 12),
		array('title' => '会员分组', 'field' => 'groupname', 'width' => 12),
		array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
		array('title' => '积分', 'field' => 'credit1', 'width' => 12),
		array('title' => '余额', 'field' => 'credit2', 'width' => 12),
		array('title' => '成交订单数', 'field' => 'ordercount', 'width' => 12),
		array('title' => '成交总金额', 'field' => 'ordermoney', 'width' => 12)
		)
	));
		}

		$total = pdo_fetchcolumn('select count(*) from' . tablename('ewei_shop_member') . ' dm ' . ' left join ' . tablename('ewei_shop_member_group') . ' g on dm.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on dm.level =l.id' . ' left join ' . tablename('mc_mapping_fans') . 'f on f.openid=dm.openid' . ' where 1 ' . $condition . ' ', $params);
		$pager = pagination($total, $pindex, $psize);
		$opencommission = false;
		$plug_commission = p('commission');

		if ($plug_commission) {
			$comset = $plug_commission->getSet();

			if (!empty($comset)) {
				$opencommission = true;
			}
		}

		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();
		include $this->template();
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$hascommission = false;
		$plugin_com = p('commission');

		if ($plugin_com) {
			$plugin_com_set = $plugin_com->getSet();
			$hascommission = !empty($plugin_com_set['level']);
		}

		$id = intval($_GPC['id']);

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			pdo_update('ewei_shop_member', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			$member = m('member')->getMember($id);
			plog('member.list.edit', '修改会员资料  ID: ' . $member['id'] . ' <br/> 会员信息:  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);

			if ($hascommission) {
				if (cv('commission.agent.changeagent')) {
					$adata = (is_array($_GPC['adata']) ? $_GPC['adata'] : array());

					if (!empty($adata)) {
						if (empty($_GPC['oldstatus']) && ($adata['status'] == 1)) {
							$time = time();
							$adata['agenttime'] = time();
							$plugin_com->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
							plog('commission.agent.check', '审核分销商 <br/>分销商信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
						}

						plog('commission.agent.edit', '修改分销商 <br/>分销商信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
						pdo_update('ewei_shop_member', $adata, array('id' => $id, 'uniacid' => $_W['uniacid']));
						if (empty($_GPC['oldstatus']) && ($adata['status'] == 1)) {
							if (!empty($member['agentid'])) {
								$plugin_com->upgradeLevelByAgent($member['agentid']);
							}
						}
					}
				}
			}

			show_json(1);
		}

		if ($hascommission) {
			$agentlevels = $plugin_com->getLevels();
		}

		$member = m('member')->getMember($id);

		if ($hascommission) {
			$member = $plugin_com->getInfo($id, array('total', 'pay'));
		}

		$member['self_ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		$member['self_ordermoney'] = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));

		if (!empty($member['agentid'])) {
			$parentagent = m('member')->getMember($member['agentid']);
		}

		$order = pdo_fetch('select finishtime from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status>=1 Limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		$member['last_ordertime'] = $order['finishtime'];
		$diyform_flag = 0;
		$diyform_flag_commission = 0;
		$diyform_plugin = p('diyform');

		if ($diyform_plugin) {
			if (!empty($member['diymemberdata'])) {
				$diyform_flag = 1;
				$fields = iunserializer($member['diymemberfields']);
			}

			if (!empty($member['diycommissiondata'])) {
				$diyform_flag_commission = 1;
				$cfields = iunserializer($member['diycommissionfields']);
			}
		}

		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();
		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$members = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($members as $member) {
			pdo_delete('ewei_shop_member', array('id' => $member['id']));
			plog('member.list.delete', '删除会员  ID: ' . $member['id'] . ' <br/>会员信息: ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		}

		show_json(1, array('url' => referer()));
	}

	public function setblack()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$members = pdo_fetchall('select id,openid,nickname,realname,mobile from ' . tablename('ewei_shop_member') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$black = intval($_GPC['isblack']);

		foreach ($members as $member) {
			if (!empty($black)) {
				pdo_update('ewei_shop_member', array('isblack' => 1), array('id' => $member['id']));
				plog('member.list.edit', '设置黑名单 <br/>用户信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			}
			else {
				pdo_update('ewei_shop_member', array('isblack' => 0), array('id' => $member['id']));
				plog('member.list.edit', '取消黑名单 <br/>用户信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			}
		}

		show_json(1);
	}

	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$wechatid = intval($_GPC['wechatid']);

		if (empty($wechatid)) {
			$wechatid = $_W['uniacid'];
		}

		$params = array();
		$params[':uniacid'] = $wechatid;
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND ( `nickname` LIKE :keyword or `realname` LIKE :keyword or `mobile` LIKE :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall('SELECT id,avatar,nickname,openid,realname,mobile FROM ' . tablename('ewei_shop_member') . ' WHERE 1 ' . $condition . ' order by createtime desc', $params);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->template();
	}
}

?>
