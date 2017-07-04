<?php

error_reporting(0);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/xg_agent/defines.php';
require '../../../../../addons/xg_agent/core/inc/functions.php';
require '../../../../../addons/xg_agent/core/inc/plugin_model.php';
global $_W;
global $_GPC;
ignore_user_abort();
set_time_limit(0);
$sets = pdo_fetchall('select uniacid from ' . tablename('xg_agent_sysset'));

foreach ($sets as $set) {
	$_W['uniacid'] = $set['uniacid'];

	if (empty($_W['uniacid'])) {
		continue;
	}

	$trade = m('common')->getSysset('trade', $_W['uniacid']);
	$days = intval($trade['receive']);

	if ($days <= 0) {
		continue;
	}

	$daytimes = 86400 * $days;
	$p = p('commission');
	$pcoupon = com('coupon');
	$orders = pdo_fetchall('select id,couponid,openid from ' . tablename('xg_agent_order') . ' where uniacid=' . $_W['uniacid'] . ' and status=2 and sendtime + ' . $daytimes . ' <=unix_timestamp() ', array(), 'id');

	if (!empty($orders)) {
		$orderkeys = array_keys($orders);
		$orderids = implode(',', $orderkeys);

		if (!empty($orderids)) {
			pdo_query('update ' . tablename('xg_agent_order') . ' set status=3,finishtime=' . time() . ' where id in (' . $orderids . ')');

			foreach ($orders as $orderid => $o) {
				m('member')->upgradeLevel($o['openid']);
				m('order')->setGiveBalance($orderid, 1);
				m('notice')->sendOrderMessage($orderid);

				if ($pcoupon) {
					if (!empty($o['couponid'])) {
						$pcoupon->backConsumeCoupon($o['id']);
					}
				}

				if ($p) {
					$p->checkOrderFinish($orderid);
				}
			}
		}
	}
}

?>
