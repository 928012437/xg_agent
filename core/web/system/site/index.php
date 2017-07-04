<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends SystemPage
{
	protected $type = 'set';

	public function main()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = array();
			$data['type'] = $this->type;
			$_GPC['data']['logo'] = save_media($_GPC['data']['logo']);
			$data['content'] = json_encode($_GPC['data']);
			$res = pdo_fetch('select id from ' . tablename('xg_agent_system_site') . ' where `type`=:type', array(':type' => $this->type));

			if (empty($res)) {
				$ok = pdo_insert('xg_agent_system_site', $data);
				$ok ? show_json(1) : show_json(0);
			}
			else {
				$ok = pdo_update('xg_agent_system_site', $data, array('id' => $res['id']));
				show_json(1);
			}
		}

		$res = pdo_fetch('select * from ' . tablename('xg_agent_system_site') . ' where `type`=:type', array(':type' => $this->type));
		$data = json_decode($res['content'], true);
		include $this->template();
	}
}

?>
