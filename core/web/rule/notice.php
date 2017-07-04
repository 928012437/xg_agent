<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}


class Notice_XgAgentPage extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		$params = array();

		if ($_GPC['enabled'] != '') {
			$condition .= ' and status=' . intval($_GPC['enabled']);
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and title like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_system_copyright_notice') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_system_copyright_notice') . ' WHERE 1 ' . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if ($_W['ispost']) {
			empty($_GPC['title']) && show_json(0, array('message' => '公告标题不能为空', 'url' => referer()));
			empty($_GPC['identity']) && show_json(0, array('message' => '身份不能为空', 'url' => referer()));
			empty($_GPC['content']) && show_json(0, array('message' => '公告内容不能为空', 'url' => referer()));
			$data = array(
				'uniacid' => $_W['uniacid'],
				'title' => trim($_GPC['title']),
				'author' => trim($_GPC['author']),
				'content' => trim($_GPC['content']),
				'displayorder' => intval($_GPC['displayorder']),
				'identity' => intval($_GPC['identity']),
				'status' => trim($_GPC['status']),
				'imgurl' => trim($_GPC['imgurl']),
				'starttime' => strtotime($_GPC['time']['start']),
				'endtime' => strtotime($_GPC['time']['end']),
				'link' => trim($_GPC['link']),
			);

			if (!empty($id)) {
				pdo_update('xg_agent_system_copyright_notice', $data, array('id' => $id));
				plog('rule.notice.edit', '修改公告 ID: ' . $id);
			}
			else {
				$data['createtime'] = TIMESTAMP;
				pdo_insert('xg_agent_system_copyright_notice', $data);
				$id = pdo_insertid();
				plog('rule.notice.add', '添加公告 ID: ' . $id);
			}

			show_json(1);
		}

		$item = pdo_fetch('select * from ' . tablename('xg_agent_system_copyright_notice') . ' where id=:id limit 1', array(':id' => $id));
		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('xg_agent_system_copyright_notice') . ' WHERE id in( ' . $id . ' )');

		foreach ($items as $item) {
			pdo_delete('xg_agent_system_copyright_notice', array('id' => $item['id']));
			plog('rule.notice.delete', '删除公告 ID: ' . $item['id'] . ' 标题: ' . $item['title'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}

	public function displayorder()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$displayorder = intval($_GPC['value']);
		$item = pdo_fetchall('SELECT id,title FROM ' . tablename('xg_agent_system_copyright_notice') . ' WHERE id in( ' . $id . ' )');

		if (!empty($item)) {
			pdo_update('xg_agent_system_copyright_notice', array('displayorder' => $displayorder), array('id' => $id));
			plog('rule.notice.delete', '修改公告排序 ID: ' . $item['id'] . ' 标题: ' . $item['title'] . ' 排序: ' . $displayorder . ' ');
		}

		show_json(1);
	}

	public function status()
	{
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('xg_agent_system_copyright_notice') . ' WHERE id in( ' . $id . ' )');

		foreach ($items as $item) {
			pdo_update('xg_agent_system_copyright_notice', array('status' => intval($_GPC['status'])), array('id' => $item['id']));
			plog('rule.notice.edit', ('修改公告状态<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['title'] . '<br/>状态: ' . $_GPC['enabled']) == 1 ? '显示' : '隐藏');
		}

		show_json(1, array('url' => referer()));
	}
}

?>
