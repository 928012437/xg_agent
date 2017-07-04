<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Option_XgAgentPage extends WebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = ' where uniacid=:uniacid and level=0';
        $params = array(':uniacid' => $_W['uniacid']);
        $list = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_option') . $condition . '  ORDER BY displayorder DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_option') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);

        include $this->template();
    }

    public function post()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $data = array(
            'uniacid' => $_W['uniacid'],
            'name' => trim($_GPC['name']),
            'level' => 0,
            'must' => intval($_GPC['must']),
            'displayorder' => intval($_GPC['displayorder']),
        );
        if (!empty($id)) {
            $data['status'] = intval($_GPC['status']);
            plog('rule.option.edit','修改选项，选项ID:'.$id);
            pdo_update('xg_agent_option', $data, array('id' => $id));
        } else {
            $data['status'] = 0;
            $data['type'] = intval($_GPC['type']);
            plog('rule.option.add','添加选项');
            pdo_insert('xg_agent_option', $data);
        }
        show_json(1);
    }

    public function option2()
    {
        global $_W;
        global $_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $oid = intval($_GPC['id']);
        $condition = ' and uniacid=:uniacid and level=1 and oid=:oid';
        $params = array(':uniacid' => $_W['uniacid'], ':oid' => $oid);
        $list = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_option') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_option') . ' WHERE 1 ' . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template();
    }

    public function post2()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $data = array(
            'uniacid' => $_W['uniacid'],
            'name' => trim($_GPC['name']),
            'level' => 1,
            'displayorder' => intval($_GPC['displayorder']),
            'oid' => intval($_GPC['oid'])
        );
        if (!empty($id)) {
            $data['status'] = intval($_GPC['status']);
            plog('rule.option2.edit','修改二级选项，选项ID:'.$id);
            pdo_update('xg_agent_option', $data, array('id' => $id));
        } else {
            $data['status'] = 0;
            plog('rule.option2.add','添加二级选项');
            pdo_insert('xg_agent_option', $data);
        }
        show_json(1);
    }

    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        plog('rule.option.delete','删除选项');
            pdo_delete('xg_agent_option', array('id' => $id));

        show_json(1, array('url' => referer()));
    }

}

?>
