<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends ComWebPage
{
	public function __construct($_com = 'coupon')
	{
		parent::__construct($_com);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' AND couponname LIKE :couponname';
			$params[':couponname'] = '%' . trim($_GPC['keyword']) . '%';
		}

		if (!empty($_GPC['catid'])) {
			$_GPC['catid'] = trim($_GPC['catid']);
			$condition .= ' AND catid = :catid';
			$params[':catid'] = (int) $_GPC['catid'];
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);

			if (!empty($starttime)) {
				$condition .= ' AND createtime >= :starttime';
				$params[':starttime'] = $starttime;
			}

			if (!empty($endtime)) {
				$condition .= ' AND createtime <= :endtime';
				$params[':endtime'] = $endtime;
			}
		}

		if ($_GPC['gettype'] != '') {
			$condition .= ' AND gettype = :gettype';
			$params[':gettype'] = intval($_GPC['gettype']);
		}

		if ($_GPC['type'] != '') {
			$condition .= ' AND coupontype = :coupontype';
			$params[':coupontype'] = intval($_GPC['type']);
		}

		$sql = 'SELECT * FROM ' . tablename('ewei_shop_coupon') . ' ' . ' where  1 and ' . $condition . ' ORDER BY displayorder DESC,id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);

		foreach ($list as &$row) {
			$row['gettotal'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_coupon_data') . ' where couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
			$row['usetotal'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_coupon_data') . ' where used = 1 and couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
			$row['pwdjoins'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_coupon_guess') . ' where couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
			$row['pwdoks'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_coupon_guess') . ' where couponid=:couponid and uniacid=:uniacid and ok=1 limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
		}

		unset($row);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_coupon') . ' where 1 and ' . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		$category = pdo_fetchall('select * from ' . tablename('ewei_shop_coupon_category') . ' where uniacid=:uniacid order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$type = intval($_GPC['type']);

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'couponname' => trim($_GPC['couponname']), 'coupontype' => intval($_GPC['coupontype']), 'catid' => intval($_GPC['catid']), 'timelimit' => intval($_GPC['timelimit']), 'usetype' => intval($_GPC['usetype']), 'returntype' => intval($_GPC['returntype']), 'enough' => trim($_GPC['enough']), 'timedays' => intval($_GPC['timedays']), 'timestart' => strtotime($_GPC['time']['start']), 'timeend' => strtotime($_GPC['time']['end']), 'backtype' => intval($_GPC['backtype']), 'deduct' => trim($_GPC['deduct']), 'discount' => trim($_GPC['discount']), 'backmoney' => trim($_GPC['backmoney']), 'backcredit' => trim($_GPC['backcredit']), 'backredpack' => trim($_GPC['backredpack']), 'backwhen' => intval($_GPC['backwhen']), 'gettype' => intval($_GPC['gettype']), 'getmax' => intval($_GPC['getmax']), 'credit' => intval($_GPC['credit']), 'money' => trim($_GPC['money']), 'usecredit2' => intval($_GPC['usecredit2']), 'total' => intval($_GPC['total']), 'bgcolor' => trim($_GPC['bgcolor']), 'thumb' => save_media($_GPC['thumb']), 'remark' => trim($_GPC['remark']), 'desc' => m('common')->html_images($_GPC['desc']), 'descnoset' => intval($_GPC['descnoset']), 'status' => intval($_GPC['status']), 'resptitle' => trim($_GPC['resptitle']), 'respthumb' => save_media($_GPC['respthumb']), 'respdesc' => trim($_GPC['respdesc']), 'respurl' => trim($_GPC['respurl']), 'pwdkey2' => trim($_GPC['pwdkey2']), 'pwdwords' => trim($_GPC['pwdwords']), 'pwdask' => trim($_GPC['pwdask']), 'pwdsuc' => trim($_GPC['pwdsuc']), 'pwdfail' => trim($_GPC['pwdfail']), 'pwdfull' => trim($_GPC['pwdfull']), 'pwdurl' => trim($_GPC['pwdurl']), 'pwdtimes' => intval($_GPC['pwdtimes']), 'pwdopen' => intval($_GPC['pwdopen']), 'pwdown' => trim($_GPC['pwdown']), 'pwdexit' => trim($_GPC['pwdexit']), 'pwdexitstr' => trim($_GPC['pwdexitstr']));

			if (!empty($id)) {
				if (!empty($data['pwdkey2'])) {
					$pwdkey2 = pdo_fetch('SELECT pwdkey2 FROM ' . tablename('ewei_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));

					if ($pwdkey2['pwdkey2'] != $data['pwdkey2']) {
						$keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and uniacid=:uniacid and id<>:id  limit 1 ', array(':content' => $data['pwdkey2'], ':uniacid' => $_W['uniacid'], ':id' => $id));

						if (!empty($keyword)) {
							show_json(0, array('url' => webUrl('sale/coupon/edit', array('id' => $id, 'tab' => str_replace('#tab_', '', $_GPC['tab']))), 'message' => '口令关键词已存在!'));
						}
					}
				}

				pdo_update('ewei_shop_coupon', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
				plog('sale.coupon.edit', '编辑优惠券 ID: ' . $id . ' <br/>优惠券名称: ' . $data['couponname']);
			}
			else {
				if (!empty($data['pwdkey2'])) {
					$keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and uniacid=:uniacid limit 1 ', array(':content' => $data['pwdkey2'], ':uniacid' => $_W['uniacid']));

					if (!empty($keyword)) {
						show_json(0, array('url' => webUrl('sale/coupon/edit', array('id' => $id, 'tab' => str_replace('#tab_', '', $_GPC['tab']))), 'message' => '口令关键词已存在!'));
					}
				}

				$data['createtime'] = time();
				pdo_insert('ewei_shop_coupon', $data);
				$id = pdo_insertid();
				plog('sale.coupon.add', '添加优惠券 ID: ' . $id . '  <br/>优惠券名称: ' . $data['couponname']);
			}

			$key = 'ewei_shopv2:com:coupon:' . $id;
			$rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name  limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'ewei_shopv2', ':name' => $key));

			if (!empty($data['pwdkey2'])) {
				if (empty($rule)) {
					$rule_data = array('uniacid' => $_W['uniacid'], 'name' => $key, 'module' => 'ewei_shopv2', 'displayorder' => 0, 'status' => $data['pwdopen']);
					pdo_insert('rule', $rule_data);
					$rid = pdo_insertid();
					$keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'ewei_shopv2', 'content' => $data['pwdkey2'], 'type' => 1, 'displayorder' => 0, 'status' => $data['pwdopen']);
					pdo_insert('rule_keyword', $keyword_data);
				}
				else {
					pdo_update('rule_keyword', array('content' => $data['pwdkey2'], 'status' => $data['pwdopen']), array('rid' => $rule['id']));
				}
			}
			else {
				if (!empty($rule)) {
					pdo_delete('rule_keyword', array('rid' => $rule['id']));
					pdo_delete('rule', array('id' => $rule['id']));
				}
			}

			show_json(1, array('url' => webUrl('sale/coupon/edit', array('id' => $id, 'tab' => str_replace('#tab_', '', $_GPC['tab'])))));
		}

		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));

		if (empty($item)) {
			$starttime = time();
			$endtime = strtotime(date('Y-m-d H:i:s', $starttime) . '+7 days');
		}
		else {
			$type = $item['coupontype'];
			$starttime = $item['timestart'];
			$endtime = $item['timeend'];
		}

		$category = pdo_fetchall('select * from ' . tablename('ewei_shop_coupon_category') . ' where uniacid=:uniacid order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
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

		$items = pdo_fetchall('SELECT id,couponname FROM ' . tablename('ewei_shop_coupon') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('ewei_shop_coupon', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('ewei_shop_coupon_data', array('couponid' => $item['id'], 'uniacid' => $_W['uniacid']));
			plog('sale.coupon.delete', '删除优惠券 ID: ' . $id . '  <br/>优惠券名称: ' . $item['couponname'] . ' ');
		}

		show_json(1, array('url' => webUrl('sale/coupon')));
	}

	public function displayorder()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$displayorder = intval($_GPC['value']);
		$items = pdo_fetchall('SELECT id,couponname FROM ' . tablename('ewei_shop_coupon') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('ewei_shop_coupon', array('displayorder' => $displayorder), array('id' => $id));
			plog('sale.coupon.displayorder', '修改优惠券排序 ID: ' . $item['id'] . ' 名称: ' . $item['couponname'] . ' 排序: ' . $displayorder . ' ');
		}

		show_json(1);
	}

	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND couponname like :couponname';
			$params[':couponname'] = '%' . $kwd . '%';
		}

		$time = time();
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_coupon') . '  WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($ds as &$d) {
			$d = com('coupon')->setCoupon($d, $time, false);
			$d['last'] = com('coupon')->get_last_count($d['id']);

			if ($d['last'] == -1) {
				$d['last'] = '不限';
			}
		}

		unset($d);
		include $this->template();
	}

	public function send()
	{
		global $_W;
		global $_GPC;
		$type = intval($_GPC['type']);
		$id = intval($_GPC['couponid']);

		if (!empty($id)) {
			$coupon = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid ', array(':id' => $id, ':uniacid' => $_W['uniacid']));

			if (empty($coupon)) {
				$this->message('未找到优惠券!', '', 'error');
			}
		}

		if ($_W['ispost']) {
			$class1 = intval($_GPC['send1']);
			$plog = '';

			if ($class1 == 1) {
				$openids = explode(',', trim($_GPC['send_openid']));
				$plog = '发放优惠券 ID: ' . $id . ' 方式: 指定 OPENID 人数: ' . count($openids);
			}
			else if ($class1 == 2) {
				$where = '';

				if (!empty($_GPC['send_level'])) {
					$where .= ' and level =' . intval($_GPC['send_level']);
				}

				$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\'' . $where, array(), 'openid');

				if (!empty($_GPC['send_level'])) {
					$levelname = pdo_fetchcolumn('select levelname from ' . tablename('ewei_shop_member_level') . ' where id=:id limit 1', array(':id' => $_GPC['send_level']));
				}
				else {
					$levelname = '全部';
				}

				$openids = array_keys($members);
				$plog = '发放优惠券 ID: ' . $id . ' 方式: 等级-' . $levelname . ' 人数: ' . count($members);
			}
			else if ($class1 == 3) {
				$where = '';

				if (!empty($_GPC['send_group'])) {
					$where .= ' and groupid =' . intval($_GPC['send_group']);
				}

				$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\'' . $where, array(), 'openid');

				if (!empty($_GPC['send_group'])) {
					$groupname = pdo_fetchcolumn('select groupname from ' . tablename('ewei_shop_member_group') . ' where id=:id limit 1', array(':id' => $_GPC['send_group']));
				}
				else {
					$groupname = '全部分组';
				}

				$openids = array_keys($members);
				$plog = '发放优惠券 ID: ' . $id . '  方式: 分组-' . $groupname . ' 人数: ' . count($members);
			}
			else if ($class1 == 4) {
				$where = '';
				$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\'' . $where, array(), 'openid');
				$openids = array_keys($members);
				$plog = '发放优惠券 ID: ' . $id . '  方式: 全部会员 人数: ' . count($members);
			}
			else {
				if ($class1 == 5) {
					$where = '';

					if (!empty($_GPC['send_agentlevel'])) {
						$where .= ' and agentlevel =' . intval($_GPC['send_agentlevel']);
					}

					$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and isagent=1 and status=1 ' . $where, array(), 'openid');

					if ($_GPC['send_agentlevel'] != '') {
						$levelname = pdo_fetchcolumn('select levelname from ' . tablename('ewei_shop_commission_level') . ' where id=:id limit 1', array(':id' => $_GPC['send_agentlevel']));
					}
					else {
						$levelname = '全部';
					}

					$openids = array_keys($members);
					$plog = '发放优惠券 ID: ' . $id . '  方式: 分销商-' . $levelname . ' 人数: ' . count($members);
				}
			}

			$mopenids = array();

			foreach ($openids as $openid) {
				$mopenids[] = '\'' . str_replace('\'', '\'\'', $openid) . '\'';
			}

			if (empty($mopenids)) {
				show_json(0, '未找到发送的会员!');
			}

			$members = pdo_fetchall('select id,openid,nickname from ' . tablename('ewei_shop_member') . ' where openid in (' . implode(',', $mopenids) . ') and uniacid=' . $_W['uniacid']);

			if (empty($members)) {
				show_json(0, '未找到发送的会员!');
			}

			if ($coupon['total'] != -1) {
				$last = com('coupon')->get_last_count($id);

				if ($last <= 0) {
					show_json(0, '优惠券数量不足,无法发放!');
				}

				$need = count($members) - $last;

				if (0 < $need) {
					show_json(0, '优惠券数量不足,请补充 ' . $need . ' 张优惠券才能发放!');
				}
			}

			$upgrade = array('resptitle' => trim($_GPC['send_title']), 'respthumb' => trim($_GPC['send_thumb']), 'respdesc' => trim($_GPC['send_desc']), 'respurl' => trim($_GPC['send_url']));
			pdo_update('ewei_shop_coupon', $upgrade, array('id' => $coupon['id']));
			$send_total = intval($_GPC['send_total']);
			($send_total <= 0) && ($send_total = 1);
			$account = m('common')->getAccount();
			$set = $_W['shopset']['coupon'];
			$time = time();

			foreach ($members as $m) {
				$i = 1;

				while ($i <= $send_total) {
					$log = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $id, 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $time, 'getfrom' => 0);
					pdo_insert('ewei_shop_coupon_log', $log);
					$logid = pdo_insertid();
					$data = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'couponid' => $id, 'gettype' => 0, 'gettime' => $time, 'senduid' => $_W['uid']);
					pdo_insert('ewei_shop_coupon_data', $data);
					++$i;
				}

				com('coupon')->sendMessage($coupon, $send_total, $m, $set['templateid'], $account);
			}

			plog('sale.coupon.send', $plog);
			show_json(1, array('message' => '优惠券发放成功!', 'url' => webUrl('sale/coupon')));
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY level asc');
		$list2 = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member_group') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY id asc');
		$list3 = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY id asc');
		load()->func('tpl');
		include $this->template();
	}

	public function set()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['consumedesc'] = m('common')->html_images($data['consumedesc']);
			$data['rechargedesc'] = m('common')->html_images($data['rechargedesc']);
			$imgs = $_GPC['adv_img'];
			$urls = $_GPC['adv_url'];
			$advs = array();

			if (is_array($imgs)) {
				foreach ($imgs as $key => $img) {
					$advs[] = array('img' => save_media($img), 'url' => trim($urls[$key]));
				}
			}

			$data['advs'] = $advs;
			m('common')->updatePluginset(array('coupon' => $data));
			plog('sale.coupon.set', '修改基本设置');
			show_json(1, array('url' => webUrl('sale/coupon/set', array('tab' => str_replace('#tab_', '', $_GPC['tab'])))));
		}

		$data = m('common')->getPluginset('coupon');
		$advs = (is_array($data['advs']) ? $data['advs'] : array());
		include $this->template();
	}
}

?>
