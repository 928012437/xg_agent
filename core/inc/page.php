<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Page extends WeModuleSite
{
	public function runTasks()
	{
		global $_W;
		load()->func('communication');
		$lasttime = strtotime(m('cache')->getString('receive', 'global'));
		$interval = intval(m('cache')->getString('receive_time', 'global'));

		if (empty($interval)) {
			$interval = 60;
		}

		$interval *= 60;
		$current = time();

		if (($lasttime + $interval) <= $current) {
			m('cache')->set('receive', date('Y-m-d H:i:s', $current), 'global');
			ihttp_request(XG_AGENT_TASK_URL . 'order/receive.php', NULL, NULL, 1);
		}

		$lasttime = strtotime(m('cache')->getString('closeorder', 'global'));
		$interval = intval(m('cache')->getString('closeorder_time', 'global'));

		if (empty($interval)) {
			$interval = 60;
		}

		$interval *= 60;
		$current = time();

		if (($lasttime + $interval) <= $current) {
			m('cache')->set('closeorder', date('Y-m-d H:i:s', $current), 'global');
			ihttp_request(XG_AGENT_TASK_URL . 'order/close.php', NULL, NULL, 1);
		}

		if (com('coupon')) {
			$lasttime = strtotime(m('cache')->getString('couponback', 'global'));
			$interval = intval(m('cache')->getString('couponback_time', 'global'));

			if (empty($interval)) {
				$interval = 60;
			}

			$interval *= 60;
			$current = time();

			if (($lasttime + $interval) <= $current) {
				m('cache')->set('couponback', date('Y-m-d H:i:s', $current), 'global');
				ihttp_request(XG_AGENT_TASK_URL . 'coupon/back.php', NULL, NULL, 1);
			}
		}

		exit('run finished.');
	}

	public function template($filename = '', $type = TEMPLATE_INCLUDEPATH)
	{
		global $_W;
		global $_GPC;

		if (empty($filename)) {
			$filename = str_replace('.', '/', $_W['routes']);
		}

		if ($_GPC['do'] == 'web') {
			$filename = str_replace('/add', '/post', $filename);
			$filename = str_replace('/edit', '/post', $filename);
			$filename = 'web/' . $filename;
		}
		else {
			if ($_GPC['do'] == 'mobile') {
			}
		}

		$name = 'xg_agent';
		$moduleroot = IA_ROOT . '/addons/xg_agent';

		if (defined('IN_SYS')) {
			$compile = IA_ROOT . '/data/tpl/web/' . $_W['template'] . '/' . $name . '/' . $filename . '.tpl.php';
			$source = $moduleroot . '/template/' . $filename . '.html';

			if (!is_file($source)) {
				$source = $moduleroot . '/template/' . $filename . '/index.html';
			}

			if (!is_file($source)) {
				$explode = array_slice(explode('/', $filename), 1);
				$temp = array_slice($explode, 1);
				$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web/' . implode('/', $temp) . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web/' . implode('/', $temp) . '/index.html';
				}
			}
		}
		else {
			$template = m('cache')->getString('template_shop');

			if (empty($template)) {
				$template = 'default';
			}

			if (!is_dir($moduleroot . '/template/mobile/' . $template)) {
				$template = 'default';
			}

			$compile = IA_ROOT . '/data/tpl/app/' . $name . '/' . $template . '/mobile/' . $filename . '.tpl.php';
			$source = IA_ROOT . '/addons/' . $name . '/template/mobile/' . $template . '/' . $filename . '.html';

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/mobile/default/' . $filename . '.html';
			}

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/mobile/default/' . $filename . '/index.html';
			}

			if (!is_file($source)) {
				$names = explode('/', $filename);
				$pluginname = $names[0];
				$ptemplate = m('cache')->getString('template_' . $pluginname);

				if (empty($ptemplate)) {
					$ptemplate = 'default';
				}

				if (!is_dir($moduleroot . '/plugin/' . $pluginname . '/template/mobile/' . $ptemplate)) {
					$ptemplate = 'default';
				}

				unset($names[0]);
				$pfilename = implode('/', $names);
				$source = $moduleroot . '/plugin/' . $pluginname . '/template/mobile/' . $ptemplate . '/' . $pfilename . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/plugin/' . $pluginname . '/template/mobile/' . $ptemplate . '/' . $pfilename . '/index.html';
				}
			}
		}

		if (!is_file($source)) {
			exit('Error: template source \'' . $filename . '\' is not exist!');
		}

		if (DEVELOPMENT || !is_file($compile) || (filemtime($compile) < filemtime($source))) {
			shop_template_compile($source, $compile, true);
		}

		return $compile;
	}

	public function message($msg, $redirect = '', $type = '')
	{
		global $_W;
		$title = '';
		$buttontext = '';
		$message = $msg;

		if (is_array($msg)) {
			$message = (isset($msg['message']) ? $msg['message'] : '');
			$title = (isset($msg['title']) ? $msg['title'] : '');
			$buttontext = (isset($msg['buttontext']) ? $msg['buttontext'] : '');
		}

		include $this->template('_message');
		exit();
	}

}

//客户分配通知
function sendCustomerFP($openid, $keynote1, $keynote2, $keynote3, $url) {
	global $_W;
	$template_id = pdo_fetchcolumn("select CustomerFP from " . tablename('xg_agent_templatenews') . " where uniacid = " . $_W['uniacid']);
	//$template_id = 'ehEiKp0YM82-HeuxS7UnDN-XH0M4U9O4dAeo8f8vQew';
	if (!empty($template_id)) {
		$datas = array(
			'first' => array('value' => '你好：您有一条新的分配信息,分配时间：' . date('Y-m-d', time()) . '。', 'color' => '#173177'),
			'keyword1' => array('value' => $keynote1, 'color' => '#173177'),
			'keyword2' => array('value' => $keynote2, 'color' => '#173177'),
			'remark' => array('value' => '分配人：' . $keynote3, 'color' => '#173177'),
		);
		$data = json_encode($datas); //发送的消息模板数据
	}

	if (!empty($template_id)) {
		$accountid = pdo_fetch("select * from " . tablename('account_wechats') . " where uniacid = " . $_W['uniacid']);
		$appid = $accountid['key'];
		$appSecret = $accountid['secret'];
		if (empty($url)) {
			$url = '';
		} else {
			$url = $url;
		}
		$sendopenid = $openid;
		$topcolor = "#FF0000";
		tempmsg($template_id, $url, $data, $topcolor, $sendopenid, $appid, $appSecret);
	}
}

//客户状态变动通知
function sendStatusChange($openid, $customName, $customPhone, $reportBuilding, $reportTime, $change, $changeTime, $remark,$url) {
	global $_W;
	$template_id = pdo_fetchcolumn("select StatusChange from " . tablename('xg_agent_templatenews') . " where uniacid = " . $_W['uniacid']);
	//$template_id = 'VNdaLqyV47z-gpI6kq76x43j-l3cyqvi_FNqgTD0ZbU';
	if (!empty($template_id)) {
		$datas = array(
			'first' => array('value' => '客户状态变动', 'color' => '#173177'),
			'customName' => array('value' => $customName, 'color' => '#173177'),
			'customPhone' => array('value' => $customPhone, 'color' => '#173177'),
			'reportBuilding' => array('value' => $reportBuilding, 'color' => '#173177'),
			'reportTime' => array('value' => $reportTime, 'color' => '#173177'),
			'change' => array('value' => $change, 'color' => '#173177'),
			'changeTime' => array('value' => $changeTime, 'color' => '#173177'),
			'remark' => array('value' => $remark, 'color' => '#173177'),
		);
		$data = json_encode($datas); //发送的消息模板数据
	}

	if (!empty($template_id)) {
		$accountid = pdo_fetch("select * from " . tablename('account_wechats') . " where uniacid = " . $_W['uniacid']);
		$appid = $accountid['key'];
		$appSecret = $accountid['secret'];
		if (empty($url)) {
			$url = '';
		} else {
			$url = $url;
		}
		$sendopenid = $openid;
		$topcolor = "#FF0000";
		tempmsg($template_id, $url, $data, $topcolor, $sendopenid, $appid, $appSecret);
	}
}

//佣金发放通知
function sendCommission($openid, $customName, $customPhone, $reportBuilding, $reportTime, $signAmount, $signTime, $commissionAmount, $commissionTime,$url) {
	global $_W;
	$template_id = pdo_fetchcolumn("select Commission from " . tablename('xg_agent_templatenews') . " where uniacid = " . $_W['uniacid']);
	//$template_id = 'EPvEnsdqqtUru3E6zDDgF0s_AoR7DOnK76jZsE2wdTE';
	if (!empty($template_id)) {
		$datas = array(
			'first' => array('value' => '有一笔客户成交佣金已发放至您的账户！', 'color' => '#173177'),
			'customName' => array('value' => $customName, 'color' => '#173177'),
			'customPhone' => array('value' => $customPhone, 'color' => '#173177'),
			'reportBuilding' => array('value' => $reportBuilding, 'color' => '#173177'),
			'reportTime' => array('value' => $reportTime, 'color' => '#173177'),
			'signAmount' => array('value' => $signAmount, 'color' => '#173177'),
			'signTime' => array('value' => $signTime, 'color' => '#173177'),
			'commissionAmount' => array('value' => $commissionAmount, 'color' => '#173177'),
			'commissionTime' => array('value' => $commissionTime, 'color' => '#173177'),
			'remark' => array('value' => '恭喜您，请继续推荐客户！', 'color' => '#173177'),
		);
		$data = json_encode($datas); //发送的消息模板数据
	}

	if (!empty($template_id)) {
		$accountid = pdo_fetch("select * from " . tablename('account_wechats') . " where uniacid = " . $_W['uniacid']);
		$appid = $accountid['key'];
		$appSecret = $accountid['secret'];
		if (empty($url)) {
			$url = '';
		} else {
			$url = $url;
		}
		$sendopenid = $openid;
		$topcolor = "#FF0000";
		tempmsg($template_id, $url, $data, $topcolor, $sendopenid, $appid, $appSecret);
	}
}

//积分变更通知
function sendCreditChange($openid, $keyword1, $keyword2, $keyword3, $url) {
	global $_W;
	$template_id = pdo_fetchcolumn("select CreditChange from " . tablename('xg_agent_templatenews') . " where uniacid = " . $_W['uniacid']);
	//$template_id = 'WWsBXT0buME6JE_1c1EI7CwO-Dys5UigOv_HisioeWE';
	if (!empty($template_id)) {
		$datas = array(
			'first' => array('value' => '您的会员账户增加' . $keyword3 . '积分', 'color' => '#173177'),
			'keyword1' => array('value' => $keyword1, 'color' => '#173177'),
			'keyword2' => array('value' => $keyword2, 'color' => '#173177'),
			'keyword3' => array('value' => $keyword3 . '分', 'color' => '#173177'),
			'remark' => array('value' => '详情请点击查看！', 'color' => '#173177'),
		);
		$data = json_encode($datas); //发送的消息模板数据
	}

	if (!empty($template_id)) {
		$accountid = pdo_fetch("select * from " . tablename('account_wechats') . " where uniacid = " . $_W['uniacid']);
		$appid = $accountid['key'];
		$appSecret = $accountid['secret'];
		if (empty($url)) {
			$url = '';
		} else {
			$url = $url;
		}
		$sendopenid = $openid;
		$topcolor = "#FF0000";
		tempmsg($template_id, $url, $data, $topcolor, $sendopenid, $appid, $appSecret);
	}
}

function tempmsg($template_id, $url, $data, $topcolor, $sendopenid, $appid, $appSecret) {
	load()->func('communication');
	if ($data->expire_time < time()) {
		$url1 = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appSecret . "";
		$res = json_decode(httpGet($url1));
		$tokens = $res->access_token;
		if (empty($tokens)) {
			return;
		}
		$postarr = '{"touser":"' . $sendopenid . '","template_id":"' . $template_id . '","url":"' . $url . '","topcolor":"' . $topcolor . '","data":' . $data . '}';
		$res = ihttp_post('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $tokens, $postarr);
	}
}

function httpGet($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $url);
	$res = curl_exec($curl);
	curl_close($curl);

	return $res;
}


?>
