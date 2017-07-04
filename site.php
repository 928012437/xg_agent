<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once IA_ROOT . '/addons/xg_agent/version.php';

require_once IA_ROOT . '/addons/xg_agent/defines.php';

require_once XG_AGENT_INC . 'functions.php';

class Xg_agentModuleSite extends WeModuleSite {

	public function getMenus(){
		global $_W;
		return array(
				array(
					'title' => '管理后台',
					'icon'=>'fa fa-shopping-cart',
					'url'=> webUrl()
				)
		);
	}
	public function doWebWeb() {

		m('route')->run();
	}
	public function doMobileMobile() {
		m('route')->run(false);
	}
	public function payResult($params) {
		return m('order')->payResult($params);
	}
}
