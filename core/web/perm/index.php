<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends WebPage
{
	public function main()
	{

		if (im_check('perm.role')) {

			header('location: ' . webUrl('perm/role'));
			exit();
			return NULL;
		}

		if (im_check('perm.user')) {
			header('location: ' . webUrl('perm/user'));
			exit();
			return NULL;
		}

		if (im_check('perm.log')) {
			header('location: ' . webUrl('perm/log'));
			exit();
		}
	}
}

?>
