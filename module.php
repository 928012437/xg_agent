<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once IA_ROOT . '/addons/xg_agent/version.php';
require_once IA_ROOT . '/addons/xg_agent/defines.php';
require_once XG_AGENT_INC . 'functions.php';
class Xg_agentModule extends WeModule
{
	public function welcomeDisplay()
	{
		header('location: ' . webUrl());
		exit();
	}
}

?>
