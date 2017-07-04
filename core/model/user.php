<?php

class User_XgAgentModel
{
	private $sessionid;

	public function __construct()
	{
		global $_W;
		$this->sessionid = '__cookie_xg_agent_201507200000_' . $_W['uniacid'];
	}

	public function getOpenid()
	{
		$userinfo = $this->getInfo(false, true);
		return $userinfo['openid'];
	}

	public function getInfo($base64 = false, $debug = false)
	{
		global $_W;
		global $_GPC;
		$userinfo = array();

		if (XG_AGENT_DEBUG) {
			$userinfo = array('openid' => 'oE_kaw3OPo2yHcLiy0riogJXePDk', 'nickname' => 'xg', 'headimgurl' => 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4EW795ibnrG53Al9Ar8wxORcIqTh24g32J4bFdgy0jXWzZhhaXVfdmlp9gibvDMKW6soyx2JUkNK3D5ibdAFP5ELVmVuUamfqVB8/132', 'province' => '山东', 'city' => '淄博');
		}
		else {
			load()->model('mc');
			$userinfo = mc_oauth_userinfo();
			$need_openid = true;

			if ($_W['container'] != 'wechat') {
				if (($_GPC['do'] == 'order') && ($_GPC['p'] == 'pay')) {
					$need_openid = false;
				}

				if (($_GPC['do'] == 'member') && ($_GPC['p'] == 'recharge')) {
					$need_openid = false;
				}

				if (($_GPC['do'] == 'plugin') && ($_GPC['p'] == 'article') && ($_GPC['preview'] == '1')) {
					$need_openid = false;
				}
			}
		}

		if ($base64) {
			return urlencode(base64_encode(json_encode($userinfo)));
		}

		return $userinfo;
	}

	public function followed($openid = '')
	{
		global $_W;
		$followed = !empty($openid);

		if ($followed) {
			$mf = pdo_fetch('select follow from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
			$followed = $mf['follow'] == 1;
		}

		return $followed;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
