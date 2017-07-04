<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Task_XgAgentPage extends WebPage
{
	public function main()
	{
		$this->runTasks();
	}
}

?>
