<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once IA_ROOT . '/addons/xg_agent/version.php';
require_once IA_ROOT . '/addons/xg_agent/defines.php';
require_once XG_AGENT_INC . 'functions.php';
require_once XG_AGENT_INC . 'processor.php';
require_once XG_AGENT_INC . 'plugin_model.php';
require_once XG_AGENT_INC . 'com_model.php';
class Xg_agentModuleProcessor extends Processor
{
	public function respond()
	{
		return parent::respond();
	}
}

?>
