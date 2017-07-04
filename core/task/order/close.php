<?php

error_reporting(0);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/xg_agent/defines.php';
require '../../../../../addons/xg_agent/core/inc/functions.php';
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
	$days = intval($trade['closeorder']);

	if ($days <= 0) {
		continue;
	}

	$daytimes = 86400 * $days;
	$orders = pdo_fetchall('select id,openid,deductcredit2,ordersn from ' . tablename('xg_agent_order') . ' where  uniacid=' . $_W['uniacid'] . ' and status=0 and paytype<>3  and createtime + ' . $daytimes . ' <=unix_timestamp() ');
	$p = com('coupon');

	foreach ($orders as $o) {
		$onew = pdo_fetch('select status from ' . tablename('xg_agent_order') . ' where id=:id and status=0 and paytype<>3  and createtime + ' . $daytimes . ' <=unix_timestamp()  limit 1', array(':id' => $o['id']));
		if (!empty($onew) && ($onew['status'] == 0)) {
			if ($p) {
				if (!empty($o['couponid'])) {
					$p->returnConsumeCoupon($o['id']);
				}
			}

			m('order')->setStocksAndCredits($o['id'], 2);
			m('order')->setDeductCredit2($o);
			pdo_query('update ' . tablename('xg_agent_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);
		}
	}
}

?>
