<?php

class Route_XgAgentModel
{
	public function run($isweb = true)
	{
		global $_GPC;
		global $_W;

		require_once IA_ROOT . '/addons/xg_agent/core/inc/page.php';

		if ($isweb) {
			require_once XG_AGENT_CORE . 'inc/page_web.php';
			require_once XG_AGENT_CORE . 'inc/page_web_com.php';
		}
		else {
			require_once XG_AGENT_CORE . 'inc/page_mobile.php';
			require_once XG_AGENT_CORE . 'inc/page_mobile_login.php';
		}

		$r = str_replace('//', '/', trim($_GPC['r'], '/'));
		$routes = explode('.', $r);

		$segs = count($routes);
		$method = 'main';
		$root = ($isweb ? XG_AGENT_CORE_WEB : XG_AGENT_CORE_MOBILE);
		$isplugin = !empty($_GPC['r']) && is_dir(XG_AGENT_PLUGIN . $routes[0]);

		if ($isplugin) {
			if ($isweb) {
				require_once XG_AGENT_CORE . 'inc/page_web_plugin.php';
			}
			else {
				require_once XG_AGENT_CORE . 'inc/page_mobile_plugin.php';
				require_once XG_AGENT_CORE . 'inc/page_mobile_plugin_login.php';
				require_once XG_AGENT_CORE . 'inc/page_mobile_plugin_pf.php';
			}

			$_W['plugin'] = $routes[0];
			$root = XG_AGENT_PLUGIN . $routes[0] . '/core/' . ($isweb ? 'web' : 'mobile') . '/';
			$routes = array_slice($routes, 1);
			$segs = count($routes);
		}
		else {
			if ($routes[0] == 'system') {
				require_once XG_AGENT_CORE . 'inc/page_system.php';
			}
		}

		switch ($segs) {
		case 0:
			$file = $root . 'index.php';
			$class = 'Index';
		case 1:
			$file = $root . $routes[0] . '.php';

			if (is_file($file)) {
				$class = ucfirst($routes[0]);
			}
			else if (is_dir($root . $routes[0])) {
				$file = $root . $routes[0] . '/index.php';
				$class = 'Index';
			}
			else {
				$method = $routes[0];
				$file = $root . 'index.php';
				$class = 'Index';
			}

			$_W['action'] = $routes[0];
			break;

		case 2:
			$_W['action'] = $routes[0] . '.' . $routes[1];
			$file = $root . $routes[0] . '/' . $routes[1] . '.php';

			if (is_file($file)) {
				$class = ucfirst($routes[1]);
			}
			else if (is_dir($root . $routes[0] . '/' . $routes[1])) {
				$file = $root . $routes[0] . '/' . $routes[1] . '/index.php';
				$class = 'Index';
			}
			else {
				$file = $root . $routes[0] . '.php';

				if (is_file($file)) {
					$method = $routes[1];
					$class = ucfirst($routes[0]);
				}
				else if (is_dir($root . $routes[0])) {
					$method = $routes[1];
					$file = $root . $routes[0] . '/index.php';
					$class = 'Index';
				}
				else {
					$file = $root . 'index.php';
					$class = 'Index';
				}
			}

			$_W['action'] = $routes[0] . '.' . $routes[1];
			break;

		case 3:
			$_W['action'] = $routes[0] . '.' . $routes[1] . '.' . $routes[2];
			$file = $root . $routes[0] . '/' . $routes[1] . '/' . $routes[2] . '.php';

			if (is_file($file)) {
				$class = ucfirst($routes[2]);
			}
			else if (is_dir($root . $routes[0] . '/' . $routes[1] . '/' . $routes[2])) {
				$file = $root . $routes[0] . '/' . $routes[1] . '/' . $routes[2] . '/index.php';
				$class = 'Index';
			}
			else {
				$method = $routes[2];
				$file = $root . $routes[0] . '/' . $routes[1] . '.php';

				if (is_file($file)) {
					$class = ucfirst($routes[1]);
				}
				else {
					if (is_dir($root . $routes[0] . '/' . $routes[1])) {
						$file = $root . $routes[0] . '/' . $routes[1] . '/index.php';
						$class = 'Index';
					}
				}

				$_W['action'] = $routes[0] . '.' . $routes[1];
			}

			break;

		case 4:
			$_W['action'] = $routes[0] . '.' . $routes[1] . '.' . $routes[2];
			$method = $routes[3];
			$class = ucfirst($routes[2]);
			$file = $root . $routes[0] . '/' . $routes[1] . '/' . $routes[2] . '.php';
			break;
		}

		if (!is_file($file)) {
			show_message('未找到控制器 ' . $r);
		}

		$_W['routes'] = $r;
		$_W['isplugin'] = $isplugin;
		$_W['controller'] = $routes[0];

		$global_set = m('cache')->getArray('globalset', 'global');

		if (empty($global_set)) {
			$global_set = m('common')->setGlobalSet();
		}

		if (!is_array($global_set)) {
			$global_set = array();
		}

		empty($global_set['trade']['credittext']) && $global_set['trade']['credittext'] = '积分';
		empty($global_set['trade']['moneytext']) && $global_set['trade']['moneytext'] = '余额';
		$GLOBALS['_S'] = $_W['shopset'] = $global_set;
		include $file;

		$class = ucfirst($class) . '_XgAgentPage';

		$instance = new $class();

		if (!method_exists($instance, $method)) {
			show_message('控制器 ' . $_W['controller'] . ' 方法 ' . $method . ' 未找到!');
		}

		$instance->$method();
		exit();
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
