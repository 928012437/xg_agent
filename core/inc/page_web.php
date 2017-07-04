<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class WebPage extends Page
{
	public function __construct()
	{
		$this->init();
	}

	private function init()
	{
		global $_W;
		if (($_W['role'] != 'manager') && ($_W['role'] != 'founder') && ($_W['routes'] != 'shop')) {
			$perm = cv($_W['routes']);
			$perm_type = com('perm')->getLogTypes(true);
			$perm_type_value = array_map(function($val) {
				return $val['value'];
			}, $perm_type);
			$is_xxx = com('perm')->check_xxx($_W['routes']);

			if ($is_xxx) {
				if (!$perm) {
					foreach ($is_xxx as $item) {
						if (in_array($item, $perm_type_value)) {
							$this->message('你没有相应的权限查看');
						}
					}
				}
			}
			else {
				if (strexists($_W['routes'], 'edit')) {
					if (!cv($_W['routes'])) {
						$view = str_replace('edit', 'view', $_W['routes']);
						$perm_view = cv($view);
					}
				}
				else {
					$main = $_W['routes'] . '.main';
					$perm_main = cv($main);
					if (!$perm_main && in_array($main, $perm_type_value)) {
						$this->message('你没有相应的权限查看');
					}
					else {
						if (!$perm && in_array($_W['routes'], $perm_type_value)) {
							$this->message('你没有相应的权限查看');
						}
					}
				}

				if (isset($perm_view) && !$perm_view) {
					$this->message('你没有相应的权限查看');
				}
			}
		}

		if ($_W['ispost']) {
			rc();
		}
	}

	public function frame_menus()
	{
		global $_GPC;
		global $_W;

		if ($_W['plugin']) {
			include $this->template($_W['plugin'] . '/tabs');
			return NULL;
		}

		if ($_W['controller'] == 'system') {
			$routes = explode('.', $_W['routes']);
			$tabs = $routes[0] . (isset($routes[1]) ? '/' . $routes[1] : '') . '/tabs';
			include $this->template($tabs);
			return NULL;
		}

		include $this->template($_W['controller'] . '/tabs');
	}
}

?>
