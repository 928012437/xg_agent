<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends SystemPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$wechatid = intval($_GPC['wechatid']);
		if (!empty($wechatid) && ($wechatid != -1)) {
			$copyrights = pdo_fetch('select * from ' . tablename('xg_agent_system_copyright') . ' where uniacid=' . $wechatid . ' and ismanage=0 limit 1');
		}

		if (empty($copyrights)) {
			$copyrights = pdo_fetch('select * from ' . tablename('xg_agent_system_copyright') . ' where uniacid=-1  and ismanage=0  limit 1');
		}

		if (empty($copyrights['bgcolor'])) {
			$copyrights['bgcolor'] = '#fff';
		}

		if ($_W['ispost']) {
			$condition = '';
			$acid = 0;
			$where = array();
			$sets = pdo_fetchall('select uniacid from ' . tablename('xg_agent_sysset'));
			$post = htmlspecialchars_decode($_GPC['copyright']);

			foreach ($sets as $set) {
				$uniacid = $set['uniacid'];
				if (($wechatid == $uniacid) || ($wechatid == -1)) {
					$cs = pdo_fetch('select * from ' . tablename('xg_agent_system_copyright') . ' where uniacid=:uniacid and ismanage=0 limit 1', array(':uniacid' => $uniacid));

					if (empty($cs)) {
						pdo_insert('xg_agent_system_copyright', array('uniacid' => $uniacid, 'copyright' => $post, 'bgcolor' => $_GPC['bgcolor'], 'ismanage' => 0));
					}
					else {
						pdo_update('xg_agent_system_copyright', array('copyright' => $post, 'bgcolor' => $_GPC['bgcolor']), array('uniacid' => $uniacid, 'ismanage' => 0));
					}
				}
			}

			if ($wechatid == -1) {
				$global_copyrights = pdo_fetch('select * from ' . tablename('xg_agent_system_copyright') . ' where uniacid=-1 and ismanage=0 limit 1');

				if (empty($global_copyrights['id'])) {
					pdo_insert('xg_agent_system_copyright', array('uniacid' => -1, 'copyright' => $post, 'bgcolor' => $_GPC['bgcolor'], 'ismanage' => 0));
				}
				else {
					pdo_update('xg_agent_system_copyright', array('copyright' => $post, 'bgcolor' => $_GPC['bgcolor'], 'ismanage' => 0), array('uniacid' => -1, 'ismanage' => 0));
				}
			}

			$copyrights = pdo_fetchall('select *  from ' . tablename('xg_agent_system_copyright'));
			m('cache')->set('systemcopyright', $copyrights, 'global');
			show_json(1);
		}

		$wechats = m('common')->getWechats();
		include $this->template();
	}
}

?>
