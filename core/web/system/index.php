<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends SystemPage
{
	public function main()
	{
		header('Location:' . webUrl('system/plugin'));
		exit();
		include $this->template();
	}
}

?>
