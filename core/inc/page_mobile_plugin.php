<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class PluginMobilePage extends MobilePage
{
	public $model;
	public $set;

	public function __construct()
	{
		parent::__construct();
		$this->model = m('plugin')->loadModel($GLOBALS['_W']['plugin']);
		$this->set = $this->model->getSet();
	}

	public function getSet()
	{
		return $this->set;
	}
}

?>
