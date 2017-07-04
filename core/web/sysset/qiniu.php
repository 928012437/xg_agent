<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Qiniu_XgAgentPage extends ComWebPage
{
	public function __construct($_com = 'qiniu')
	{
		parent::__construct($_com);
	}

	public function main()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());

			if ($data['upload']) {
				$check = com('qiniu')->save('addons/xg_agentv2/static/images/nopic100.jpg', $data);

				if (empty($check)) {
					show_json(0, '设置错误，请检查!');
				}
			}

			m('common')->updateSysset(array(
	'qiniu' => array('user' => $data)
	));
			plog('sysset.qiniu.edit', '保存七牛设置');
			show_json(1);
		}

		$qiniu = m('common')->getSysset('qiniu');
		$data = $qiniu['user'];
		include $this->template();
	}
}

?>
