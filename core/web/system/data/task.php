<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Task_XgAgentPage extends SystemPage
{
	public function main()
	{
		$receive_time = m('cache')->getString('receive_time', 'global');
		$closeorder_time = m('cache')->getString('closeorder_time', 'global');
		$couponback_time = m('cache')->getString('couponback_time', 'global');

		if ($_W['ispost']) {
			m('cache')->set('receive_time', intval($_GPC['receive_time']), 'global');
			m('cache')->set('closeorder_time', intval($_GPC['closeorder_time']), 'global');
			m('cache')->set('couponback_time', intval($_GPC['couponback_time']), 'global');
			show_json(1);
		}

		include $this->template();
	}
}

?>
