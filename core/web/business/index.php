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

        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid';
        $params = array(':uniacid' => $_W['uniacid']);

        if (!empty($_GPC['sort'])) {
            $sort = $_GPC['sort'];
            $condition .= " and name like '%" . $sort . "%'";
        }
        $tuandui = pdo_fetchall("select * from " . tablename('xg_agent_tuandui') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_tuandui') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $tempuser = pdo_getall('xg_agent_user', array('uniacid' => $_W['uniacid']));
        $wutuan = 0;
        foreach ($tempuser as $v) {
            $tempmark = 0;
            foreach ($tuandui as &$v2) {
                if (empty($v2['count'])) {
                    $v2['count'] = 0;
                }
                if ($v['tid'] == $v2['id']) {
                    $tempmark = 1;
                    $v2['count']++;
                }
            }
            if ($tempmark == 0) {
                $wutuan++;
            }
        }

        include $this->template();
    }

    public function post()
    {
        global $_W;
        global $_GPC;
        $data = array(
            'uniacid' => $_W['uniacid'],
            'name' => $_GPC['name']
        );
        if (empty($_GPC['id'])) {
            $id = pdo_insert('xg_agent_tuandui', $data);
            plog('business.add', '新增团队，团队ID：' . $id);
        } else {
            pdo_update('xg_agent_tuandui', $data, array('id' => $_GPC['id']));
            plog('business.edit', '修改团队，团队ID：' . $_GPC['id']);
        }

        show_json(1);
    }

    public function delete()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        foreach ($id as $i) {
            pdo_delete('xg_agent_tuandui', array('id' => $i));
            plog('business.delete', '删除团队，团队ID：' . $i);
        }

        show_json(1);
    }

    public function userlist()
    {
        global $_W;
        global $_GPC;

        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid';
        $params = array(':uniacid' => $_W['uniacid']);

        if (!empty($_GPC['sort'])) {
            $sort = $_GPC['sort'];
            $condition .= " and (realname like '%" . $sort . "%' or mobile like '%" . $sort . "%' or id like '%" . $sort . "%')";
        }

        if (!empty($_GPC['status'])) {
            $condition .= " and status = " . $_GPC['status'];
        }

        if (!empty($_GPC['flag'])) {
            $condition .= " and flag = " . $_GPC['flag'];
        }

        if (!empty($_GPC['is_review'])) {
            $condition .= " and is_review = " . $_GPC['is_review'];
        }

        if (!empty($_GPC['id'])) {
            $condition .= " and tid =" . $_GPC['id'];
        }

        if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
            $condition .= " and createtime >=" . strtotime($_GPC['time']['start']) . " and createtime <=" . strtotime($_GPC['time']['end']);
        }

        $list = pdo_fetchall("select * from " . tablename('xg_agent_user') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);

        foreach ($list as &$v) {
            $fans = pdo_get('mc_mapping_fans', array('openid' => $v['openid'], 'uniacid' => $_W['uniacid']));
            $v['is_gz'] = $fans['follow'];
        }
        unset($v);

        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_user') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);


        include $this->template();
    }

    public function isreview()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        if ($_GPC['is_review'] == 0) {
            $str = "取消";
        } else {
            $str = "通过";
        }
        foreach ($id as $i) {
            pdo_update('xg_agent_user', array('is_review' => $_GPC['is_review']), array('id' => $i));
            plog('business.userlist.isreview', '审核业务员-' . $str . '，业务员ID：' . $i);
        }

        show_json(1);
    }

    public function delete2()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        foreach ($id as $i) {
            pdo_delete('xg_agent_user', array('id' => $i));
            plog('business.userlist.delete', '删除业务员，业务员ID：' . $i);
        }

        show_json(1);
    }

    public function detail()
    {
        global $_GPC, $_W;
        $id = $_GPC['id'];
        if ($_W['ispost']) {

            $data = array(
                'sex' => $_GPC['sex'],
                'tid' => $_GPC['tid'],
                'flag' => $_GPC['flag'],
                'realname' => $_GPC['realname'],
                'mobile' => $_GPC['mobile'],
                'wechanum' => $_GPC['wechanum'],
                'status' => $_GPC['status'],
                'content' => $_GPC['content'],
            );
            pdo_update('xg_agent_user', $data, array('id' => $id));
            plog('business.userlist.edit','修改业务员信息，业务员ID：' . $id);
            show_json(1, array('url' => webUrl('business/userlist')));
        }
        $user = pdo_get('xg_agent_user', array('id' => $id));
        $fans = pdo_get('mc_mapping_fans', array('openid' => $user['openid'], 'uniacid' => $_W['uniacid']));
        $user['is_gz'] = $fans['follow'];
        $tdlist = pdo_getall('xg_agent_tuandui', array('uniacid' => $_W['uniacid']));

        include $this->template();
    }

    public function cover(){
        global $_W;
        global $_GPC;
        $rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'cover', ':name' => '用户入口_XG'));

        if (!empty($rule)) {
            $keyword = pdo_fetch('select * from ' . tablename('rule_keyword') . ' where uniacid=:uniacid and rid=:rid limit 1', array(':uniacid' => $_W['uniacid'], ':rid' => $rule['id']));
            $cover = pdo_fetch('select * from ' . tablename('cover_reply') . ' where uniacid=:uniacid and rid=:rid limit 1', array(':uniacid' => $_W['uniacid'], ':rid' => $rule['id']));
        }

        if($_W['ispost']){
            $data = (is_array($_GPC['cover']) ? $_GPC['cover'] : array());
            $url = $_W['siteroot'].'app/'.substr(mobileUrl('user.opensource.cover'), 2);
            if (!empty($rule)) {
                pdo_delete('rule', array('id' => $rule['id'], 'uniacid' => $_W['uniacid']));
                pdo_delete('rule_keyword', array('rid' => $rule['id'], 'uniacid' => $_W['uniacid']));
                pdo_delete('cover_reply', array('rid' => $rule['id'], 'uniacid' => $_W['uniacid']));
            }

            $rule_data = array('uniacid' => $_W['uniacid'], 'name' => '用户入口_XG', 'module' => 'cover', 'displayorder' => 0, 'status' => intval($data['status']));
            pdo_insert('rule', $rule_data);
            $rid = pdo_insertid();
            $keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'cover', 'content' => trim($data['keyword']), 'type' => 1, 'displayorder' => 0, 'status' => intval($data['status']));
            pdo_insert('rule_keyword', $keyword_data);

            $cover_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'xg_jjr', 'title' => trim($data['title']), 'description' => trim($data['desc']), 'thumb' => $data['thumb'], 'url' => $url);

            pdo_insert('cover_reply', $cover_data);
            show_json(1);
        }

        include $this->template();
    }

}