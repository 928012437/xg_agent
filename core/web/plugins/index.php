<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$category = m('plugin')->getList(1);
		include $this->template();
	}
}

?>
