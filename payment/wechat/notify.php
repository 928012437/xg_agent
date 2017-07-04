<?php

error_reporting(0);
define('IN_MOBILE', true);
$input = file_get_contents('php://input');
if (!empty($input) && empty($_GET['out_trade_no'])) {
	$obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);

	if (empty($data)) {
		exit('fail');
	}

	if (($data['result_code'] != 'SUCCESS') || ($data['return_code'] != 'SUCCESS')) {
		$result = array('return_code' => 'FAIL', 'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']);
		echo array2xml($result);
		exit();
	}

	$get = $data;
}
else {
	$get = $_GET;
}

require '../../../../framework/bootstrap.inc.php';
require '../../../../addons/ewei_shopv2/defines.php';
require '../../../../addons/ewei_shopv2/core/inc/functions.php';
require '../../../../addons/ewei_shopv2/core/inc/plugin_model.php';
require '../../../../addons/ewei_shopv2/core/inc/com_model.php';
$strs = explode(':', $get['attach']);
$_W['uniacid'] = $_W['weid'] = intval($strs[0]);
$type = intval($strs[1]);
$total_fee = $get['total_fee'] / 100;
$setting = uni_setting($_W['uniacid'], array('payment'));

if (is_array($setting['payment'])) {
	$wechat = $setting['payment']['wechat'];

	if (!empty($wechat)) {
		ksort($get);
		$string1 = '';

		foreach ($get as $k => $v) {
			if (($v != '') && ($k != 'sign')) {
				$string1 .= $k . '=' . $v . '&';
			}
		}

		$wechat['signkey'] = $wechat['version'] == 1 ? $wechat['key'] : $wechat['signkey'];
		$sign = strtoupper(md5($string1 . 'key=' . $wechat['signkey']));

		if ($sign == $get['sign']) {
			if (empty($type)) {
				$tid = $get['out_trade_no'];

				if (strexists($tid, 'GJ')) {
					$tids = explode('GJ', $tid);
					$tid = $tids[0];
				}

				$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `module`=:module AND `tid`=:tid  limit 1';
				$params = array();
				$params[':tid'] = $tid;
				$params[':module'] = 'ewei_shopv2';
				$log = pdo_fetch($sql, $params);
				if (!empty($log) && ($log['status'] == '0') && ($log['fee'] == $total_fee)) {
					$site = WeUtility::createModuleSite($log['module']);

					if (!is_error($site)) {
						$method = 'payResult';

						if (method_exists($site, $method)) {
							$ret = array();
							$ret['weid'] = $log['weid'];
							$ret['uniacid'] = $log['uniacid'];
							$ret['result'] = 'success';
							$ret['type'] = $log['type'];
							$ret['from'] = 'return';
							$ret['tid'] = $log['tid'];
							$ret['user'] = $log['openid'];
							$ret['fee'] = $log['fee'];
							$ret['tag'] = $log['tag'];
							$result = $site->$method($ret);

							if ($result) {
								$log['tag'] = iunserializer($log['tag']);
								$log['tag']['transaction_id'] = $get['transaction_id'];
								$record = array();
								$record['status'] = '1';
								$record['tag'] = iserializer($log['tag']);
								pdo_update('core_paylog', $record, array('plid' => $log['plid']));
								pdo_update('ewei_shop_order', array('paytype' => 21), array('ordersn' => $log['tid'], 'uniacid' => $log['uniacid']));
							}
						}
					}
				}
			}
			else if ($type == 1) {
				$logno = trim($get['out_trade_no']);

				if (empty($logno)) {
					exit();
				}

				$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_log') . ' WHERE `uniacid`=:uniacid and `logno`=:logno limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
				if (!empty($log) && empty($log['status']) && ($log['openid'] == $get['openid']) && ($log['money'] == $total_fee)) {
					pdo_update('ewei_shop_member_log', array('status' => 1, 'rechargetype' => 'wechat'), array('id' => $log['id']));
					m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, '人人商城会员充值:wechatnotify:credit2:' . $log['money']));
					m('member')->setRechargeCredit($log['openid'], $log['money']);
					com_run('sale::setRechargeActivity', $log);
					com_run('coupon::useRechargeCoupon', $log);
					m('notice')->sendMemberLogMessage($log['id']);
				}
			}
			else if ($type == 2) {
				$logno = trim($get['out_trade_no']);

				if (empty($logno)) {
					exit();
				}

				$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_creditshop_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
				if (!empty($log) && empty($log['status'])) {
					pdo_update('ewei_shop_creditshop_log', array('paystatus' => 1, 'paytype' => 1), array('id' => $log['id']));
				}
			}
			else if ($type == 3) {
				$dispatchno = trim($get['out_trade_no']);

				if (empty($dispatchno)) {
					exit();
				}

				$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_creditshop_log') . ' WHERE `dispatchno`=:dispatchno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':dispatchno' => $dispatchno));
				if (!empty($log) && empty($log['dispatchstatus'])) {
					pdo_update('ewei_shop_creditshop_log', array('dispatchstatus' => 1), array('id' => $log['id']));
				}
			}
			else if ($type == 4) {
				com_run('coupon::payResult', trim($get['out_trade_no']));
			}
			else {
				if ($type == 5) {
					p('groups')->payResult(trim($get['out_trade_no']));
				}
			}

			$result = array('return_code' => 'SUCCESS', 'return_msg' => 'OK');
			echo array2xml($result);
			exit();
		}
	}
}

exit('fail');

?>
