<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Qiniu_XgAgentPage extends SystemPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());

			if ($data['upload']) {
				$check = com('qiniu')->save('addons/ewei_shopv2/static/images/nopic100.jpg', $data);

				if (empty($check)) {
					show_json(0, '设置错误，请检查!');
				}
			}

			m('cache')->set('qiniu', $data, 'global');
			show_json(1);
		}

		$data = m('cache')->getArray('qiniu', 'global');
		include $this->template();
	}
}

?>
