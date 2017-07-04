<?php

class Plugin_XgAgentModel
{
	public function exists($pluginName = '')
	{
		$dbplugin = pdo_fetchall('select * from ' . tablename('xg_agent_plugin') . ' where identity=:identyty limit  1', array(':identity' => $pluginName));

		if (empty($dbplugin)) {
			return false;
		}

		return true;
	}

	public function getAll($iscom = false, $status = '')
	{
		global $_W;
		$plugins = '';

		if ($status !== '') {
			$status = 'and status = ' . intval($status);
		}

		if ($iscom) {
			$plugins = m('cache')->getArray('coms_xg', 'global');

			if (empty($plugins)) {
				$plugins = pdo_fetchall('select * from ' . tablename('xg_agent_plugin') . ' where iscom=1 and deprecated=0 ' . $status . ' order by displayorder asc');
				m('cache')->set('coms_xg', $plugins, 'global');
			}
		}
		else {
			$plugins = m('cache')->getArray('plugins_xg', 'global');

			if (empty($plugins)) {
				$plugins = pdo_fetchall('select * from ' . tablename('xg_agent_plugin') . ' where iscom=0 and deprecated=0 ' . $status . ' order by displayorder asc');
				m('cache')->set('plugins_xg', $plugins, 'global');
			}
		}

		return $plugins;
	}

	public function refreshCache($status = '', $iscom = false)
	{
		if ($status !== '') {
			$status = 'and status = ' . intval($status);
		}

		$com = pdo_fetchall('select * from ' . tablename('xg_agent_plugin') . ' where iscom=1 and deprecated=0 ' . $status . ' order by displayorder asc');
		m('cache')->set('coms_xg', $com, 'global');
		$plugins = pdo_fetchall('select * from ' . tablename('xg_agent_plugin') . ' where iscom=0 and deprecated=0 ' . $status . ' order by displayorder asc');
		m('cache')->set('plugins_xg', $plugins, 'global');

		if ($iscom) {
			return $com;
		}

		return $plugins;
	}

	public function getList($status = '')
	{
		$list = $this->getCategory();
		$plugins = $this->getAll(false, $status);

		foreach ($list as $ck => &$cv) {
			$ps = array();

			foreach ($plugins as $p) {
				if ($p['category'] == $ck) {
					$ps[] = $p;
				}
			}

			$cv['plugins'] = $ps;
		}

		unset($cv);
		return $list;
	}

	public function getName($identity = '')
	{
		$plugins = $this->getAll();

		foreach ($plugins as $p) {
			if ($p['identity'] == $identity) {
				return $p['name'];
			}
		}

		return '';
	}

	public function loadModel($pluginname = '')
	{
		static $_model;

		if (!$_model) {
			$modelfile = IA_ROOT . '/addons/xg_agent/plugin/' . $pluginname . '/core/model.php';

			if (is_file($modelfile)) {
				$classname = ucfirst($pluginname) . 'Model';
				require_once XG_AGENT_CORE . 'inc/plugin_model.php';
				require_once $modelfile;
				$_model = new $classname($pluginname);
			}
		}

		return $_model;
	}

	public function getCategory()
	{
		return array(
	'tool'  => array('name' => '工具类'),
	'activity' => array('name' => '活动类')
	);
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
