<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Rank_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$rank = array('status' => intval($_GPC['status']), 'order_status' => intval($_GPC['order_status']), 'num' => empty($_GPC['num']) ? 50 : intval($_GPC['num']), 'order_num' => empty($_GPC['order_num']) ? 50 : intval($_GPC['order_num']));
			m('common')->updateSysset(array('rank' => $rank));
			plog('member.rank.edit', '修改积分排名设置');
			$result = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = :uniacid ORDER BY credit1 DESC LIMIT 100', array(':uniacid' => $_W['uniacid']));
			$result = array_map(function($value) {
				if ($value['openid'] != 'fromUser') {
					$value['credit1'] = intval(m('member')->getCredit($value['openid']));
				}

				return $value;
			}, $result);
			usort($result, function($a, $b) {
				return $b['credit1'] < $a['credit1'] ? -1 : 1;
			});
			$num = $rank['num'];
			$result = array_slice($result, 0, $num);
			m('cache')->set('member_rank', array('time' => TIMESTAMP + 3600, 'result' => $result));
			show_json(1);
		}

		$item = $_W['shopset']['rank'];
		$item['num'] = intval($item['num']);
		$item['order_num'] = intval($item['order_num']);
		include $this->template();
	}
}

?>
