<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends WebPage
{
	public function main()
	{
		header('location:' . webUrl('report'));
		exit();
	}
}

?>
