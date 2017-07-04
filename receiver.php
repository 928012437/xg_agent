<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Xg_agentModuleReceiver extends WeModuleReceiver
{
	public function receive()
	{
		$type = $this->message['type'];
	}
}

?>
