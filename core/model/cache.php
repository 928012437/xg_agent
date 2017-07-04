<?php

class Cache_XgAgentModel
{
	public function get_key($key = '', $uniacid = '')
	{
		global $_W;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}

		return XG_AGENT_PREFIX . md5($uniacid . '_new_' . $key);
	}

	public function getArray($key = '', $uniacid = '')
	{
		return $this->get($key, $uniacid);
	}

	public function getString($key = '', $uniacid = '')
	{
		return $this->get($key, $uniacid);
	}

	public function get($key = '', $uniacid = '')
	{
		return cache_read($this->get_key($key, $uniacid));
	}

	public function set($key = '', $value = NULL, $uniacid = '')
	{
		cache_write($this->get_key($key, $uniacid), $value);
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
