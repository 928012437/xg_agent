<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require MODULE_ROOT . '/defines.php';
class ComProcessor extends WeModuleProcessor
{
	public $model;
	public $modulename;
	public $message;

	public function __construct($name = '')
	{
		$this->modulename = 'xg_agent';
		$this->pluginname = $name;
		$this->loadModel();
	}

	private function loadModel()
	{
		$modelfile = IA_ROOT . '/addons/' . $this->modulename . '/core/com/' . $this->pluginname . '.php';

		if (is_file($modelfile)) {
			$classname = ucfirst($this->pluginname) . '_XgAgentComModel';
			require $modelfile;
			$this->model = new $classname($this->pluginname);
		}
	}

	public function respond()
	{
		$this->message = $this->message;
	}
}

?>