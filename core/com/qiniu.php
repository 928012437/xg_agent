<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Qiniu_XgAgentComModel extends ComModel
{
	public function save($url, $config = NULL, $enforce = false)
	{
		global $_W;
		global $_GPC;
		set_time_limit(0);

		if (empty($url)) {
			return '';
		}

		$ext = strrchr($url, '.');
		if (($ext != '.jpeg') && ($ext != '.gif') && ($ext != '.jpg') && ($ext != '.png')) {
			return '';
		}

		if (!$config) {
			$config = $this->getConfig();
		}

		if (empty($config)) {
			return $url;
		}

		if (strexists($url, $config['url'])) {
			return $url;
		}

		if (!strexists($url, 'addons/ewei_shopv2')) {
			$url = tomedia($url);
		}

		$outlinkEnforce = false;

		if (!strexists($url, $_W['siteroot'])) {
			if (strexists($url, 'http:') || strexists($url, 'https:')) {
				if (!$enforce) {
					return $url;
				}

				$outlinkEnforce = true;
			}
		}

		if (!$outlinkEnforce) {
			if (!strexists($url, 'addons/ewei_shopv2')) {
				$url = ATTACHMENT_ROOT . str_replace($_W['siteroot'] . 'attachment/', '', str_replace($_W['attachurl'], '', $url));
			}
			else {
				$url = IA_ROOT . '/' . $url;
			}
		}

		$key = md5_file($url) . $ext;

		if ($outlinkEnforce) {
			$local = ATTACHMENT_ROOT . 'ewei_shopv2_temp/';
			load()->func('file');

			if (!is_dir($local)) {
				mkdirs($local);
			}

			$filename = $local . $key;
			file_put_contents($filename, file_get_contents($url));
			$url = $filename;
		}

		require_once IA_ROOT . '/framework/library/qiniu/autoload.php';
		$auth = new \Qiniu\Auth($config['access_key'], $config['secret_key']);
		$uploadmgr = new \Qiniu\Storage\UploadManager();
		$putpolicy = \Qiniu\base64_urlSafeEncode(json_encode(array('scope' => $config['bucket'] . ':' . $url)));
		$uploadtoken = $auth->uploadToken($config['bucket'], $key, 3600, $putpolicy);
		list($ret, $err) = $uploadmgr->putFile($uploadtoken, $key, $url);

		if ($err !== NULL) {
			return '';
		}

		if ($outlinkEnforce) {
			@unlink($url);
		}

		return 'http://' . trim($config['url']) . '/' . $ret['key'];
	}

	public function getConfig()
	{
		global $_W;
		global $_GPC;
		$config = false;
		$set = m('common')->getSysset('qiniu');
		if (isset($set['user']) && is_array($set['user']) && !empty($set['user']['upload']) && !empty($set['user']['access_key']) && !empty($set['user']['secret_key']) && !empty($set['user']['bucket']) && !empty($set['user']['url'])) {
			$config = $set['user'];
		}
		else {
			$admin = m('cache')->getArray('qiniu', 'global');
			if (is_array($admin) && !empty($admin['upload']) && !empty($admin['access_key']) && !empty($admin['secret_key']) && !empty($admin['bucket']) && !empty($admin['url'])) {
				$config = $admin;
			}
		}

		return $config;
	}
}

?>
