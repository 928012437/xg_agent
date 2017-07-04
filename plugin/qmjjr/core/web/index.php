<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Index_XgAgentPage extends PluginWebPage
{

    public function getordernum($num)
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        //提现订单数量
        $num_s = pdo_fetch("select COUNT(*) from " . tablename('xg_agent_commisapplic') . " where uniacid=:uniacid and status =0", array(':uniacid' => $uniacid));
        $num_s = $num_s['COUNT(*)'];

        $num_d = pdo_fetch("select COUNT(*) from " . tablename('xg_agent_commisapplic') . " where uniacid=:uniacid and status =1", array(':uniacid' => $uniacid));
        $num_d = $num_d['COUNT(*)'];

        $num_y = pdo_fetch("select COUNT(*) from " . tablename('xg_agent_commisapplic') . " where uniacid=:uniacid and status =2", array(':uniacid' => $uniacid));
        $num_y = $num_y['COUNT(*)'];

        $num_w = pdo_fetch("select COUNT(*) from " . tablename('xg_agent_commisapplic') . " where uniacid=:uniacid and status =3", array(':uniacid' => $uniacid));
        $num_w = $num_w['COUNT(*)'];
        if ($num == 1) {
            return $num_s;
        } elseif ($num == 2) {
            return $num_d;
        } elseif ($num == 3) {
            return $num_y;
        } elseif ($num == 4) {
            return $num_w;
        } elseif ($num == 5) {
            return ($num_s + $num_d + $num_y + $num_w);
        }
    }


    public function main()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid';
        $condition2 = "";
        $params = array(':uniacid' => $uniacid);

        if (!empty($_GPC['id'])) {
            $condition .= " and tjid = " . $_GPC['id'];
        }

        if (!empty($_GPC['sort'])) {
            $sort = $_GPC['sort'];
            $condition .= " and (realname like '%" . $sort . "%' or mobile like '%" . $sort . "%' or id like '%" . $sort . "%')";
        }
        if ($_GPC['review'] != '') {
            $condition .= " and review = " . $_GPC['review'];
        }
        if ($_GPC['sfid'] != '') {
            $condition .= " and sfid = " . $_GPC['sfid'];
        }
        if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
            $condition .= " and createtime >=" . strtotime($_GPC['time']['start']) . " and createtime <=" . strtotime($_GPC['time']['end']);
        }
        if (empty($_GPC['export'])) {
            $condition2 = " ORDER BY createtime DESC limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $list = pdo_fetchall("select * from " . tablename('xg_agent_member') . $condition . $condition2, $params);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_member') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $idens = pdo_getall('xg_agent_identity', array('uniacid' => $uniacid));
        foreach ($list as &$v) {
            foreach ($idens as $b) {
                if ($v['sfid'] == $b['id']) {
                    $v['sfid'] = $b['name'];
                }
            }
            $fans = pdo_get('mc_mapping_fans', array('openid' => $v['openid'], 'uniacid' => $_W['uniacid']));
            $v['is_gz'] = $fans['follow'];
        }
        unset($v);

        if ($_GPC['export'] == '1') {
            plog('qmjjr', '导出经纪人数据');

            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
                $row['sex'] = $row['sex'] == 0 ? '女' : '男';
                $row['review'] = $row['review'] == 0 ? '未审核' : '已审核';
                $row['status'] = $row['status'] == 0 ? '黑名单' : '正常';
                $row['is_gz'] = $row['is_gz'] == 1 ? '否' : '是';
            }

            unset($row);
            m('excel')->export($list, array(
                'title' => '经纪人数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '姓名', 'field' => 'realname', 'width' => 12),
                    array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
                    array('title' => '性别', 'field' => 'sex', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
                    array('title' => '是否审核', 'field' => 'review', 'width' => 12),
                    array('title' => '是否关注', 'field' => 'is_gz', 'width' => 12),
                    array('title' => '状态', 'field' => 'status', 'width' => 12),
                    array('title' => '总佣金', 'field' => 'commission', 'width' => 12),
                    array('title' => '已打款佣金', 'field' => 'ok_commis', 'width' => 12),
                    array('title' => '积分', 'field' => 'credit', 'width' => 12),
                    array('title' => '身份', 'field' => 'sfid', 'width' => 12),
                    array('title' => '备注', 'field' => 'content', 'width' => 12)
                )
            ));
        }

        include $this->template();
    }

    public function status()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        if ($_GPC['status'] == 0) {
            $str = "置于黑名单";
        } else {
            $str = "取消黑名单";
        }
        foreach ($id as $i) {
            pdo_update('xg_agent_member', array('status' => $_GPC['status']), array('id' => $i));
            plog('qmjjr.status', '更改经纪人状态-' . $str . '，经纪人ID：' . $i);
        }

        show_json(1);
    }

    public function review()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        if ($_GPC['review'] == 0) {
            $str = "取消";
        } else {
            $str = "通过";
        }
        foreach ($id as $i) {
            pdo_update('xg_agent_member', array('review' => $_GPC['review'], 'shtime' => time()), array('id' => $i));
            plog('qmjjr.review', '审核经纪人-' . $str . '，经纪人ID：' . $i);
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

            $tempmember=pdo_get('xg_agent_member', array('id' => $i));

            load()->model('mc');
            $uid = mc_openid2uid($tempmember['openid']);
            if($uid!=''){
                mc_credit_update($uid, 'credit1', 0);
                mc_credit_update($uid, 'credit2', 0);
            }

            pdo_delete('xg_agent_creditshop_log', array('openid' => $tempmember['openid']));

            pdo_delete('xg_agent_sign_records',array('openid' => $tempmember['openid']));

            pdo_delete('xg_agent_member', array('id' => $i));

            pdo_delete('xg_agent_commisapplic',array('mid'=>$i));

            pdo_delete('xg_agent_commission',array('mid'=>$i));

            pdo_delete('xg_agent_bankcard',array('mid'=>$i));

            plog('qmjjr.delete', '删除经纪人，经纪人ID：' . $i);
        }

        show_json(1);
    }

    public function detail()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        if ($_W['ispost']) {
            $data = array(
                'status' => $_GPC['status'],
                'headurl' => $_GPC['headurl'],
                'sfid' => intval($_GPC['sfid']),
                'idcardurl1' => trim($_GPC['idcardurl1']),
                'idcardurl2' => trim($_GPC['idcardurl2']),
                'wechanum' => $_GPC['wechanum'],
                'content' => $_GPC['content']
            );
            plog('qmjjr.edit', '修改经纪人信息，经纪人ID：' . $_GPC['id']);

            pdo_update('xg_agent_member', $data, array('id' => $_GPC['id']));

            show_json(1);
        }
        $user = pdo_fetch("select * from" . tablename('xg_agent_member') . "where id =" . $_GPC['id']);
        $bcard = pdo_get('xg_agent_bankcard', array('id' => $user['bcid']));
        $identitys = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_identity') . " WHERE `uniacid` = :uniacid and status = 1", array(':uniacid' => $uniacid));
        $count = pdo_fetchcolumn("select count(id) from" . tablename('xg_agent_customer') . "where tid ='j:" . $user['id'] . "'");
        $fans = pdo_get('mc_mapping_fans', array('openid' => $user['openid'], 'uniacid' => $uniacid));
        $user['is_gz'] = $fans['follow'];

        include $this->template();
    }

    public function identity()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        if ($_W['ispost']) {
            $data = array(
                'uniacid' => $uniacid,
                'name' => $_GPC['name'],
                'credit' => $_GPC['credit'],
                'credit2' => $_GPC['credit2'],
                'createtime' => time(),
                'displayorder' => $_GPC['displayorder'],
                'status' => $_GPC['status']
            );
            if (empty($id)) {
                plog('qmjjr.identity.add', "新增身份");
                pdo_insert('xg_agent_identity', $data);
            } else {
                plog('qmjjr.identity.edit', "修改身份，身份ID:" . $id);
                pdo_update('xg_agent_identity', $data, array('id' => $id));
            }
            show_json(1);
        }

        $idens = pdo_getall('xg_agent_identity', array('uniacid' => $uniacid));

        include $this->template();
    }

    public function delidentity()
    {
        global $_GPC;
        $id = $_GPC['id'];
        plog('qmjjr.identity,delete', "删除身份，身份ID:" . $id);
        pdo_delete('xg_agent_identity', array('id' => $id));
        show_json(1);
    }

    public function pxidentity()
    {
        global $_GPC;
        foreach ($_GPC['displayorder'] as $key => $val) {
            pdo_update('xg_agent_identity', array('displayorder' => intval($val)), array('id' => intval($key)));
        }
        show_json(1);
    }


    public function question()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $questions = pdo_getall('xg_agent_question', array('uniacid' => $uniacid));
        include $this->template();
    }

    public function questionpost()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        if ($_W['ispost']) {
            $data = array(
                'uniacid' => $uniacid,
                'title' => $_GPC['title'],
                'content' => $_GPC['content'],
                'displayorder' => $_GPC['displayorder'],
                'status' => $_GPC['status']
            );
            if (!empty($id)) {
                plog('qmjjr.question.edit', "修改问题，问题ID:" . $id);
                pdo_update('xg_agent_question', $data, array('id' => $id));
            } else {
                plog('qmjjr.question.add', "新增问题。");
                pdo_insert('xg_agent_question', $data);
            }
            show_json(1, array('url' => webUrl('qmjjr/question')));
        }
        if (!empty($id)) {
            $question = pdo_get('xg_agent_question', array('id' => $id));
        }

        include $this->template();
    }

    public function delquestion()
    {
        global $_GPC;
        $id = $_GPC['id'];
        plog('qmjjr.question.delete', "删除问题，问题ID:" . $id);
        pdo_delete('xg_agent_question', array('id' => $id));
        show_json(1);
    }

    public function pxquestion()
    {
        global $_GPC;
        foreach ($_GPC['displayorder'] as $key => $val) {
            pdo_update('xg_agent_question', array('displayorder' => intval($val)), array('id' => intval($key)));
        }
        show_json(1);
    }

    public function memberposter()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $item = pdo_fetch('SELECT * FROM ' . tablename('xg_agent_poster') . ' WHERE uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));
        $id = $item['id'];
        if ($_W['ispost']) {
            $data = array(
                'uniacid' => $uniacid,
                'title' => $_GPC['title'],
                'keyword' => $_GPC['keyword'],
                'bg' => $_GPC['bg'],
                'data' => htmlspecialchars_decode($_GPC['data']),
                'waittext' => $_GPC['waittext'],
                'createtime' => time(),
            );
//            if ($item['data'] != htmlspecialchars_decode($_GPC['data']) || $item['bg'] != $_GPC['bg']) {
//                $members = pdo_fetchall('SELECT id FROM ' . tablename('xg_agent_member') . ' WHERE uniacid=:uniacid', array(':uniacid' => $uniacid));
//                foreach ($members as $m) {
//                    pdo_update('xg_agent_member', array('ischange' => 1), array('id' => $m['id']));
//                }
//            }
            if (!empty($id)) {
                pdo_update('xg_agent_poster', $data, array('id' => $id, 'uniacid' => $uniacid));
            } else {
                pdo_insert('xg_agent_poster', $data);
                $id = pdo_insertid();
            }
            $rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name limit 1', array(':uniacid' => $uniacid, ':module' => 'xg_agent', ':name' => $data['title']));
            if (empty($rule)) {
                $rule_data = array('uniacid' => $uniacid, 'name' => $data['title'], 'module' => 'xg_agent', 'displayorder' => 0, 'status' => 1);
                pdo_insert('rule', $rule_data);
                $rid = pdo_insertid();
                $keyword_data = array('uniacid' => $uniacid, 'rid' => $rid, 'module' => 'xg_agent', 'content' => $data['keyword'], 'type' => 1, 'displayorder' => 0, 'status' => 1);
                pdo_insert('rule_keyword', $keyword_data);
            } else {
                pdo_update('rule_keyword', array('content' => $data['keyword']), array('rid' => $rule['id']));
                pdo_update('rule', array('name' => $data['title']), array('id' => $rule['id']));
            }
            show_json(1);
        }
        if (!empty($item)) {
            $data = json_decode(str_replace('&quot;', '\'', $item['data']), true);
        }
        include $this->template();
    }

    public function complain()
    {
        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 30;
        $complains = pdo_fetchall("SELECT * FROM " . tablename('xg_agent_complain') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC limit " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('xg_agent_complain') . " WHERE uniacid = '{$_W['uniacid']}'");
        $pager = pagination($total, $pindex, $psize);

        include $this->template();
    }

    public function complainpost()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);

        if (!empty($id)) {
            $complain = pdo_fetch("SELECT * FROM " . tablename('xg_agent_complain') . " WHERE id = '$id'");
            if ($_W['ispost']) {
                $complain = array(
                    'realname' => $_GPC['realname'],
                    'mobile' => $_GPC['mobile'],
                    'complain' => trim($_GPC['complain']),
                );
                plog('qmjjr.complain.edit', "修改投诉与建议，投诉与建议ID:" . $id);
                pdo_update('xg_agent_complain', $complain, array('id' => $id));
                show_json(1, array('url' => webUrl('qmjjr/complain')));
            }
        }
        include $this->template();
    }

    public function delcomplain()
    {
        global $_GPC;
        $id = $_GPC['id'];
        plog('qmjjr.complain.delete', "删除投诉与建议，投诉与建议ID:" . $id);
        pdo_delete('xg_agent_complain', array('id' => $id));
        show_json(1);
    }

    public function templatenews()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $theone = pdo_fetch('SELECT * FROM ' . tablename('xg_agent_templatenews') . " WHERE  uniacid = :uniacid", array(':uniacid' => $uniacid));
        if ($_W['ispost']) {
            $insert = array(
                'uniacid' => $uniacid,
                'StatusChange' => trim($_GPC['StatusChange']),
                'Commission' => trim($_GPC['Commission']),
                'CreditChange' => trim($_GPC['CreditChange']),
                'CustomerFP' => trim($_GPC['CustomerFP']),
                'createtime' => TIMESTAMP
            );
            if (empty($theone)) {
                pdo_insert('xg_agent_templatenews', $insert);
            } else {
                if (pdo_update('xg_agent_templatenews', $insert, array('id' => $theone['id'])) === false) {
                }
            }
            show_json(1);
        }
        include $this->template();
    }

    public function jjrrule()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid';
        $params = array(':uniacid' => $uniacid);

        if ($_GPC['sortlid'] != '') {
            $sortlid = $_GPC['sortlid'];
            $condition .= ' and lid=' . $sortlid;
        }

        if ($_GPC['sortsfid'] != '') {
            $sortsfid = $_GPC['sortsfid'];
            $condition .= ' and sfid=' . $sortsfid;
        }

        if ($_GPC['sortsid'] != '') {
            $sortsid = $_GPC['sortsid'];
            $condition .= ' and sid=' . $sortsid;
        }

        $rules = pdo_fetchall("select * from " . tablename('xg_agent_jjrrule') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_jjrrule') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        foreach ($rules as &$v) {
            $temploupan = pdo_get('xg_agent_loupan', array('id' => $v['lid']));
            $v['lid2'] = $temploupan['title'];
            $tempiden = pdo_get('xg_agent_identity', array('id' => $v['sfid']));
            $v['sfid2'] = $tempiden['name'];
            $tempstatus = pdo_get('xg_agent_status', array('id' => $v['sid']));
            $v['sid2'] = $tempstatus['name'];
        }
        unset($v);
        $loupans = pdo_getall('xg_agent_loupan', array('uniacid' => $uniacid));
        $idens = pdo_getall('xg_agent_identity', array('uniacid' => $uniacid));
        $statuss = pdo_getall('xg_agent_status', array('uniacid' => $uniacid));

        include $this->template();
    }

    public function jjrrulepost()
    {
        global $_GPC, $_W;
        $temprule=pdo_get('xg_agent_jjrrule',array('uniacid'=>$_W['uniacid'],'lid' => $_GPC['lid'],'sfid' => $_GPC['sfid'],'sid' => $_GPC['sid']));
        if($temprule!=''){
            show_json(0,'规则不可重复！');
        }
        $data = array(
            'uniacid' => $_W['uniacid'],
            'lid' => $_GPC['lid'],
            'sfid' => $_GPC['sfid'],
            'sid' => $_GPC['sid'],
            'commis' => $_GPC['commis'],
            'credit' => $_GPC['credit'],
        );
        if (empty($_GPC['id'])) {
            plog('qmjjr.jjrrule.add', "新增佣金积分规则。");
            pdo_insert('xg_agent_jjrrule', $data);
        } else {
            plog('qmjjr.jjrrule.edit', "修改佣金积分规则，规则ID：" . $_GPC['id']);
            pdo_update('xg_agent_jjrrule', $data, array('id' => $_GPC['id']));
        }
        show_json(1);
    }

    public function deljjrrule()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        foreach ($id as $i) {
            pdo_delete('xg_agent_jjrrule', array('id' => $i));
            plog('qmjjr.jjrrule.delete', '删除佣金积分规则，规则ID：' . $i);
        }

        show_json(1);
    }

    public function quanbu()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $mid = $_GPC['id'];
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid ';
        $params = array(':uniacid' => $uniacid);
        $condition2 = "";

        if ($mid != '') {
            $condition .= " and mid = " . $mid;
            $tempmeb = pdo_get('xg_agent_member', array('id' => $mid));
        }

        if ($_GPC['timetype'] == 'applytime') {
            $stime = strtotime($_GPC['time']['start']);
            $etime = strtotime($_GPC['time']['end']);
            $condition .= " and time > " . $stime . " and time < " . $etime;
        }
        if ($_GPC['keyword'] != '') {
            $sort = $_GPC['keyword'];
            $condition2 .= " and ( nickname like '%$sort%' or realname like '%$sort%' or mobile like '%$sort%')";
        }

        if ($_GPC['export'] != 1) {
            $condition .= " limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $commislist = pdo_fetchall("select * from " . tablename('xg_agent_commisapplic') . $condition, $params);

        foreach ($commislist as $i=>$v) {
            $jjr = pdo_fetch("select * from " . tablename('xg_agent_member') . "where id=:id $condition2", array(':id' => $commislist[$i]['mid']));
            if ($jjr != '') {
                $commislist[$i]['jjr'] = $jjr;
                $yongjin = explode(',', $commislist[$i]['yid']);
                $comm_st = 0;
                $credit_st = 0;
                for ($j = 0; $j < count($yongjin); $j++) {
                    $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
                    $comm_st += $comm_zu['commis'];
                    $credit_st += $comm_zu['credit'];
                }
                $commislist[$i]['commission'] = $comm_st;
                $commislist[$i]['credit'] = $credit_st;
            } else {
                unset($commislist[$i]);
            }
        }
        $count = count($commislist);
        if ($_GPC['export'] == 1) {
            plog('qmjjr.commis.sh_no', '导出订单exl');

            foreach ($commislist as &$row) {
                $row['time'] = date('Y-m-d H:i', $row['time']);
                $row['jjr'] = $row['jjr']['realname'];
            }

            unset($row);
            m('excel')->export($commislist, array(
                'title' => '佣金订单数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '订单号', 'field' => 'ordernum', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'time', 'width' => 12),
                    array('title' => '佣金', 'field' => 'commission', 'width' => 12),
                    array('title' => '经纪人', 'field' => 'jjr', 'width' => 12),
                    array('title' => '提现方式', 'field' => 'txfs', 'width' => 12),
                )
            ));
        }

        include $this->template();
    }

    public function sh_no()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid  and status=0 ';
        $params = array(':uniacid' => $uniacid);
        $condition2 = "";

        if ($_GPC['timetype'] == 'applytime') {
            $stime = strtotime($_GPC['time']['start']);
            $etime = strtotime($_GPC['time']['end']);
            $condition .= " and time > " . $stime . " and time < " . $etime;
        }
        if ($_GPC['keyword'] != '') {
            $sort = $_GPC['keyword'];
            $condition2 .= " and ( nickname like '%$sort%' or realname like '%$sort%' or mobile like '%$sort%')";
        }

        if ($_GPC['export'] != 1) {
            $condition .= " limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $commislist = pdo_fetchall("select * from " . tablename('xg_agent_commisapplic') . $condition, $params);

        foreach ($commislist as $i=>$v) {
            $jjr = pdo_fetch("select * from " . tablename('xg_agent_member') . "where id=:id $condition2", array(':id' => $commislist[$i]['mid']));

            if ($jjr != '') {
                $commislist[$i]['jjr'] = $jjr;
                $yongjin = explode(',', $commislist[$i]['yid']);
                $comm_st = 0;
                $credit_st = 0;
                for ($j = 0; $j < count($yongjin); $j++) {
                    $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
                    $comm_st += $comm_zu['commis'];
                    $credit_st += $comm_zu['credit'];
                }
                $commislist[$i]['commission'] = $comm_st;
                $commislist[$i]['credit'] = $credit_st;
            } else {
                unset($commislist[$i]);
            }
        }
        $count = count($commislist);
        if ($_GPC['export'] == 1) {
            plog('qmjjr.commis.sh_no', '导出订单exl');

            foreach ($commislist as &$row) {
                $row['time'] = date('Y-m-d H:i', $row['time']);
                $row['jjr'] = $row['jjr']['realname'];
            }

            unset($row);
            m('excel')->export($commislist, array(
                'title' => '佣金订单数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '订单号', 'field' => 'ordernum', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'time', 'width' => 12),
                    array('title' => '佣金', 'field' => 'commission', 'width' => 12),
                    array('title' => '经纪人', 'field' => 'jjr', 'width' => 12),
                    array('title' => '提现方式', 'field' => 'txfs', 'width' => 12),
                )
            ));
        }

        include $this->template();
    }

    public function dk_no()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid  and status=1 ';
        $params = array(':uniacid' => $uniacid);
        $condition2 = "";

        if ($_GPC['timetype'] == 'applytime') {
            $stime = strtotime($_GPC['time']['start']);
            $etime = strtotime($_GPC['time']['end']);
            $condition .= " and time > " . $stime . " and time < " . $etime;
        }
        if ($_GPC['keyword'] != '') {
            $sort = $_GPC['keyword'];
            $condition2 .= " and ( nickname like '%$sort%' or realname like '%$sort%' or mobile like '%$sort%')";
        }

        if ($_GPC['export'] != 1) {
            $condition .= " limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $commislist = pdo_fetchall("select * from " . tablename('xg_agent_commisapplic') . $condition, $params);

        foreach ($commislist as $i=>$v) {
            $jjr = pdo_fetch("select * from " . tablename('xg_agent_member') . "where id=:id $condition2", array(':id' => $commislist[$i]['mid']));
            if ($jjr != '') {
                $commislist[$i]['jjr'] = $jjr;
                $yongjin = explode(',', $commislist[$i]['yid']);
                $comm_st = 0;
                $credit_st = 0;
                for ($j = 0; $j < count($yongjin); $j++) {
                    $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
                    $comm_st += $comm_zu['commis'];
                    $credit_st += $comm_zu['credit'];
                }
                $commislist[$i]['commission'] = $comm_st;
                $commislist[$i]['credit'] = $credit_st;
            } else {
                unset($commislist[$i]);
            }
        }
        $count = count($commislist);
        if ($_GPC['export'] == 1) {
            plog('qmjjr.commis.dk_no', '导出订单exl');

            foreach ($commislist as &$row) {
                $row['time'] = date('Y-m-d H:i', $row['time']);
                $row['jjr'] = $row['jjr']['realname'];
            }

            unset($row);
            m('excel')->export($commislist, array(
                'title' => '佣金订单数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '订单号', 'field' => 'ordernum', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'time', 'width' => 12),
                    array('title' => '审核时间', 'field' => 'shtime', 'width' => 12),
                    array('title' => '佣金', 'field' => 'commission', 'width' => 12),
                    array('title' => '经纪人', 'field' => 'jjr', 'width' => 12),
                    array('title' => '提现方式', 'field' => 'txfs', 'width' => 12),
                )
            ));
        }

        include $this->template();
    }

    public function dk_ok()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid  and status=2 ';
        $params = array(':uniacid' => $uniacid);
        $condition2 = "";

        if ($_GPC['timetype'] == 'applytime') {
            $stime = strtotime($_GPC['time']['start']);
            $etime = strtotime($_GPC['time']['end']);
            $condition .= " and time > " . $stime . " and time < " . $etime;
        }
        if ($_GPC['keyword'] != '') {
            $sort = $_GPC['keyword'];
            $condition2 .= " and ( nickname like '%$sort%' or realname like '%$sort%' or mobile like '%$sort%')";
        }

        if ($_GPC['export'] != 1) {
            $condition .= " limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $commislist = pdo_fetchall("select * from " . tablename('xg_agent_commisapplic') . $condition, $params);

        foreach ($commislist as $i=>$v) {
            $jjr = pdo_fetch("select * from " . tablename('xg_agent_member') . "where id=:id $condition2", array(':id' => $commislist[$i]['mid']));
            if ($jjr != '') {
                $commislist[$i]['jjr'] = $jjr;
                $yongjin = explode(',', $commislist[$i]['yid']);
                $comm_st = 0;
                $credit_st = 0;
                for ($j = 0; $j < count($yongjin); $j++) {
                    $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
                    $comm_st += $comm_zu['commis'];
                    $credit_st += $comm_zu['credit'];
                }
                $commislist[$i]['commission'] = $comm_st;
                $commislist[$i]['credit'] = $credit_st;
            } else {
                unset($commislist[$i]);
            }
        }
        $count = count($commislist);
        if ($_GPC['export'] == 1) {
            plog('qmjjr.commis.dk_ok', '导出订单exl');

            foreach ($commislist as &$row) {
                $row['time'] = date('Y-m-d H:i', $row['time']);
                $row['jjr'] = $row['jjr']['realname'];
            }

            unset($row);
            m('excel')->export($commislist, array(
                'title' => '佣金订单数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '订单号', 'field' => 'ordernum', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'time', 'width' => 12),
                    array('title' => '打款时间', 'field' => 'dktime', 'width' => 12),
                    array('title' => '佣金', 'field' => 'commission', 'width' => 12),
                    array('title' => '经纪人', 'field' => 'jjr', 'width' => 12),
                    array('title' => '提现方式', 'field' => 'txfs', 'width' => 12),
                )
            ));
        }

        include $this->template();
    }

    public function wuxiao()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 25;
        $condition = ' WHERE uniacid = :uniacid  and status=3 ';
        $params = array(':uniacid' => $uniacid);
        $condition2 = "";

        if ($_GPC['timetype'] == 'applytime') {
            $stime = strtotime($_GPC['time']['start']);
            $etime = strtotime($_GPC['time']['end']);
            $condition .= " and time > " . $stime . " and time < " . $etime;
        }
        if ($_GPC['keyword'] != '') {
            $sort = $_GPC['keyword'];
            $condition2 .= " and ( nickname like '%$sort%' or realname like '%$sort%' or mobile like '%$sort%')";
        }

        if ($_GPC['export'] != 1) {
            $condition .= " limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $commislist = pdo_fetchall("select * from " . tablename('xg_agent_commisapplic') . $condition, $params);

        foreach ($commislist as $i=>$v) {
            $jjr = pdo_fetch("select * from " . tablename('xg_agent_member') . "where id=:id $condition2", array(':id' => $commislist[$i]['mid']));
            if ($jjr != '') {
                $commislist[$i]['jjr'] = $jjr;
                $yongjin = explode(',', $commislist[$i]['yid']);
                $comm_st = 0;
                $credit_st = 0;
                for ($j = 0; $j < count($yongjin); $j++) {
                    $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
                    $comm_st += $comm_zu['commis'];
                    $credit_st += $comm_zu['credit'];
                }
                $commislist[$i]['commission'] = $comm_st;
                $commislist[$i]['credit'] = $credit_st;
            } else {
                unset($commislist[$i]);
            }
        }
        $count = count($commislist);
        if ($_GPC['export'] == 1) {
            plog('qmjjr.commis.wuxiao', '导出订单exl');

            foreach ($commislist as &$row) {
                $row['time'] = date('Y-m-d H:i', $row['time']);
                $row['jjr'] = $row['jjr']['realname'];
            }

            unset($row);
            m('excel')->export($commislist, array(
                'title' => '佣金订单数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '订单号', 'field' => 'ordernum', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'time', 'width' => 12),
                    array('title' => '无效时间', 'field' => 'wxtime', 'width' => 12),
                    array('title' => '佣金', 'field' => 'commission', 'width' => 12),
                    array('title' => '经纪人', 'field' => 'jjr', 'width' => 12),
                    array('title' => '提现方式', 'field' => 'txfs', 'width' => 12),
                )
            ));
        }

        include $this->template();
    }

    public function sh_no_detail()
    {
        global $_GPC, $_W;

        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        if ($_W['ispost']) {
            $comm_applic = pdo_get('xg_agent_commisapplic', array('id' => $id));
            $comm_arr = explode(',', $comm_applic['yid']);
            if ($_GPC['submit_check'] != '') {
                //审核通过

                $tongguo = 0;
                for ($i = 0; $i < count($comm_arr); $i++) {
                    if ($_GPC['status'][$comm_arr[$i]] == 1) {
                        $tongguo = 1;
                        $shenhe = array(
                            'issh' => 1,
                            'checktime' => time()
                        );
                    } else {
                        $shenhe = array(
                            'issh' => 2,
                            'checktime' => time()
                        );
                    }
                    pdo_update('xg_agent_commission', $shenhe, array('id' => $comm_arr[$i]));
                }
                if ($tongguo == 0) {
                    //无效
                    $shenhe2 = array(
                        'status' => 3,
                        'wxtime' => time()
                    );
                    $str = '无效';
                } else {
                    //待打款
                    $shenhe2 = array(
                        'status' => 1,
                        'shtime' => time()
                    );
                    $str = '通过';
                }
                plog('qmjjr.commis', '审核订单，结果：' . $str . '订单ID：' . $id);
                pdo_update('xg_agent_commisapplic', $shenhe2, array('id' => $id));
            } elseif ($_GPC['submit_refuse'] != '') {
                //驳回审核
                for ($i = 0; $i < count($comm_arr); $i++) {
                    pdo_update('xg_agent_commission', array('issq' => 1), array('id' => $comm_arr[$i]));
                }
                pdo_delete('xg_agent_commisapplic', array('id' => $id));
            }
            show_json(1, array('url' => webUrl('qmjjr/sh_no')));
        }
        $commis = pdo_get('xg_agent_commisapplic', array('id' => $id));
        $yongjin = explode(',', $commis['yid']);
        $count = count($yongjin);
        $cost = 0;
        $cost2 = 0;
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
            $cost += $comm_zu['commis'];
            $cost2 += $comm_zu['credit'];
        }
        $member = pdo_get('xg_agent_member', array('id' => $commis['mid']));

        $commission = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission == '') {
            $commission = 0;
        }
        //待审核
        $commission_s = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_s == '') {
            $commission_s = 0;
        }
        //待打款
        $commission_d = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_d == '') {
            $commission_d = 0;
        }
        //已打款
        $commission_y = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=1 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_y == '') {
            $commission_y = 0;
        }

        $identity = pdo_get('xg_agent_identity', array('id' => $member['sfid']));

        $commis_s = pdo_fetchall("select * from " . tablename('xg_agent_commission') . " where id in (" . $commis['yid'] . ")");
        for ($i = 0; $i < count($commis_s); $i++) {
            if($commis_s[$i]['cid']==-1){
                $commis_s[$i]['cname'] = '邀请奖励';
            }elseif($commis_s[$i]['cid']==-2){
                $commis_s[$i]['cname'] = '注册奖励';
            }else{
                $custm = pdo_get('xg_agent_customer', array('id' => $commis_s[$i]['cid']));
                $commis_s[$i]['cname'] = $custm['name'];
            }
        }

        include $this->template();
    }

    public function dk_no_detail()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        if ($_W['ispost']) {
            $comm_applic = pdo_get('xg_agent_commisapplic', array('id' => $id));
            $comm_arr = explode(',', $comm_applic['yid']);
            $member = pdo_get('xg_agent_member', array('id' => $comm_applic['mid']));
            if ($_GPC['submit_pay'] != '') {
                //打款通过
                $str = '通过';
                $dakuan = 0;
                $credit = 0;
                for ($i = 0; $i < count($comm_arr); $i++) {
                    $comm_yz = pdo_get('xg_agent_commission', array('id' => $comm_arr[$i]));
                    if ($comm_yz['issh'] == 1) {
                        pdo_update('xg_agent_commission', array('isdk' => 1), array('id' => $comm_arr[$i]));
                        $dakuan += $comm_yz['commis'];
                        $credit += $comm_yz['credit'];
                        $tempcommis=pdo_get('xg_agent_commission',array('id'=>$comm_arr[$i]));
                        $tempcust=pdo_get('xg_agent_customer',array('id'=>$tempcommis['cid']));
                        $temploupan=pdo_get('xg_agent_loupan',array('id'=>$tempcust['lid']));
                        $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('qmjjr.opensource.cover'), 2);
                        if($tempcommis['cid']==0){
                            $customName=$tempcommis['remark'];
                        }elseif($tempcommis['cid']==-1){
                            $customName='邀请奖励';
                        }elseif($tempcommis['cid']==-2){
                            $customName='注册奖励';
                        }else{
                            $customName=$tempcust['name'];
                        }
                        if($comm_yz['commis']>0){

                            sendCommission($member['openid'],$customName,$tempcust['tel'],$temploupan['title'],date('Y-m-d H:i:s', $tempcust['createtime']),$comm_yz['commis'],date('Y-m-d H:i:s', $tempcommis['createtime']),$member['commission'],date('Y-m-d H:i:s', time()),$url);
                        }
                        if($comm_yz['credit']>0){

                            sendCreditChange($member['openid'],'推荐客户奖励','客户名：'.$customName,$comm_yz['credit'],$url);
                        }
                    }
                }
                if($member['uid']!=''){
                    $mcme=pdo_get('mc_members',array('uid'=>$member['uid']));
                    $member['credit1']=$mcme['credit1'];
                }
                $dakuan += $member['credit2'];
                $credit += $member['credit1'];
                pdo_update('mc_members', array('credit1' => $credit), array('uid' => $member['uid']));
                pdo_update('xg_agent_member', array('credit2' => $dakuan, 'credit1' => $credit), array('id' => $comm_applic['mid']));

                $shenhe = array(
                    'status' => 2,
                    'dktime' => time()
                );
                pdo_update('xg_agent_commisapplic', $shenhe, array('id' => $id));

            } elseif ($_GPC['submit_cancel'] != '') {
                //重新审核
                $str = '重新审核';
                for ($i = 0; $i < count($comm_arr); $i++) {
                    pdo_update('xg_agent_commission', array('issh' => 0), array('id' => $comm_arr[$i]));
                }
                pdo_update('xg_agent_commisapplic', array('status' => 0), array('id' => $id));
            }
            plog('qmjjr.commis', '订单打款，结果：' . $str . '订单ID：' . $id);
            show_json(1, array('url' => webUrl('qmjjr/dk_no')));
        }
        $commis = pdo_get('xg_agent_commisapplic', array('id' => $id));
        $yongjin = explode(',', $commis['yid']);
        $count = count($yongjin);
        $cost = 0;
        $cost2 = 0;
        $cost3 = 0;
        $cost4 = 0;
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
            $cost += $comm_zu['commis'];
            $cost3 += $comm_zu['credit'];
        }
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j], 'issh' => 1));
            $cost2 += $comm_zu['commis'];
            $cost4 += $comm_zu['credit'];
        }
        $member = pdo_get('xg_agent_member', array('id' => $commis['mid']));

        $commission = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission == '') {
            $commission = 0;
        }
        //待审核
        $commission_s = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_s == '') {
            $commission_s = 0;
        }
        //待打款
        $commission_d = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_d == '') {
            $commission_d = 0;
        }
        //已打款
        $commission_y = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=1 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_y == '') {
            $commission_y = 0;
        }

        $identity = pdo_get('xg_agent_identity', array('id' => $member['identity']));

        $commis_s = pdo_fetchall("select * from " . tablename('xg_agent_commission') . " where id in (" . $commis['yid'] . ")");
        for ($i = 0; $i < count($commis_s); $i++) {
            if($commis_s[$i]['cid']==-1){
                $commis_s[$i]['cname'] = '邀请奖励';
            }elseif($commis_s[$i]['cid']==-2){
                $commis_s[$i]['cname'] = '注册奖励';
            }else{
                $custm = pdo_get('xg_agent_customer', array('id' => $commis_s[$i]['cid']));
                $commis_s[$i]['cname'] = $custm['name'];
            }
        }

        include $this->template();
    }

    public function dk_ok_detail()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];

        $commis = pdo_get('xg_agent_commisapplic', array('id' => $id));
        $yongjin = explode(',', $commis['yid']);
        $count = count($yongjin);
        $cost = 0;
        $cost2 = 0;
        $cost3 = 0;
        $cost4 = 0;
        $cost5 = 0;
        $cost6 = 0;
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
            $cost += $comm_zu['commis'];
            $cost4 += $comm_zu['credit'];
        }
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j], 'issh' => 1));
            $cost2 += $comm_zu['commis'];
            $cost5 += $comm_zu['credit'];
        }
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j], 'isdk' => 1));
            $cost3 += $comm_zu['commis'];
            $cost6 += $comm_zu['credit'];
        }
        $member = pdo_get('xg_agent_member', array('id' => $commis['mid']));

        $commission = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission == '') {
            $commission = 0;
        }
        //待审核
        $commission_s = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_s == '') {
            $commission_s = 0;
        }
        //待打款
        $commission_d = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_d == '') {
            $commission_d = 0;
        }
        //已打款
        $commission_y = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=1 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_y == '') {
            $commission_y = 0;
        }

        $identity = pdo_get('xg_agent_identity', array('id' => $member['identity']));

        $commis_s = pdo_fetchall("select * from " . tablename('xg_agent_commission') . " where id in (" . $commis['yid'] . ")");
        for ($i = 0; $i < count($commis_s); $i++) {
            if($commis_s[$i]['cid']==-1){
                $commis_s[$i]['cname'] = '邀请奖励';
            }elseif($commis_s[$i]['cid']==-2){
                $commis_s[$i]['cname'] = '注册奖励';
            }else{
                $custm = pdo_get('xg_agent_customer', array('id' => $commis_s[$i]['cid']));
                $commis_s[$i]['cname'] = $custm['name'];
            }
        }

        include $this->template();
    }

    public function wuxiao_detail()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        if ($_W['ispost']) {
            $comm_applic = pdo_get('xg_agent_commisapplic', array('id' => $id));
            $comm_arr = explode(',', $comm_applic['yid']);
            if ($_GPC['submit_cancel'] != '') {
                //重新审核
                for ($i = 0; $i < count($comm_arr); $i++) {
                    pdo_update('xg_agent_commission', array('issh' => 0), array('id' => $comm_arr[$i]));
                }
                pdo_update('xg_agent_commisapplic', array('status' => 0), array('id' => $id));
            }
            show_json(1, array('url' => webUrl('qmjjr/wuxiao')));
        }
        $commis = pdo_get('xg_agent_commisapplic', array('id' => $id));
        $yongjin = explode(',', $commis['yid']);
        $count = count($yongjin);
        $cost = 0;
        $cost2 = 0;
        for ($j = 0; $j < count($yongjin); $j++) {
            $comm_zu = pdo_get('xg_agent_commission', array('id' => $yongjin[$j]));
            $cost += $comm_zu['commis'];
            $cost2 += $comm_zu['credit'];
        }
        $member = pdo_get('xg_agent_member', array('id' => $commis['mid']));

        $commission = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission == '') {
            $commission = 0;
        }
        //待审核
        $commission_s = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_s == '') {
            $commission_s = 0;
        }
        //待打款
        $commission_d = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=0 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_d == '') {
            $commission_d = 0;
        }
        //已打款
        $commission_y = pdo_fetchcolumn("select sum(commis) from" . tablename('xg_agent_commission') . " where issh= 1 and isdk=1 and mid =" . $member['id'] . " and uniacid =" . $uniacid);
        if ($commission_y == '') {
            $commission_y = 0;
        }

        $identity = pdo_get('xg_agent_identity', array('id' => $member['identity']));

        $commis_s = pdo_fetchall("select * from " . tablename('xg_agent_commission') . " where id in (" . $commis['yid'] . ")");
        for ($i = 0; $i < count($commis_s); $i++) {
            if($commis_s[$i]['cid']==-1){
                $commis_s[$i]['cname'] = '邀请奖励';
            }elseif($commis_s[$i]['cid']==-2){
                $commis_s[$i]['cname'] = '注册奖励';
            }else{
                $custm = pdo_get('xg_agent_customer', array('id' => $commis_s[$i]['cid']));
                $commis_s[$i]['cname'] = $custm['name'];
            }
        }

        include $this->template();
    }

    public function rule()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $qrule = pdo_get('xg_agent_qmjjr_rule', array('uniacid' => $uniacid));
        if ($_W['ispost']) {
            if ($_GPC['is_credit'] == '') {
                $is_credit = 0;
            } else {
                $is_credit = 1;
            }
            if ($_GPC['is_commis'] == '') {
                $is_commis = 0;
            } else {
                $is_commis = 1;
            }
            $data = array(
                'uniacid' => $uniacid,
                'regimg' => tomedia($_GPC['regimg']),
                'is_credit' => $is_credit,
                'is_commis' => $is_commis,
                'defined1'=>$_GPC['defined1'],
                'defined2'=>$_GPC['defined2'],
                'defined3'=>$_GPC['defined3'],
                'defined4'=>$_GPC['defined4'],
                'defined5'=>$_GPC['defined5'],
            );
            if (empty($qrule)) {
                pdo_insert('xg_agent_qmjjr_rule', $data);
            } else {
                pdo_update('xg_agent_qmjjr_rule', $data, array('uniacid' => $uniacid));
            }
            show_json(1);
        }

        include $this->template();
    }

    public function cover()
    {
        global $_W, $_GPC;
        $rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'cover', ':name' => '经纪人入口_XG'));

        if (!empty($rule)) {
            $keyword = pdo_fetch('select * from ' . tablename('rule_keyword') . ' where uniacid=:uniacid and rid=:rid limit 1', array(':uniacid' => $_W['uniacid'], ':rid' => $rule['id']));
            $cover = pdo_fetch('select * from ' . tablename('cover_reply') . ' where uniacid=:uniacid and rid=:rid limit 1', array(':uniacid' => $_W['uniacid'], ':rid' => $rule['id']));
        }

        if ($_W['ispost']) {
            //修改经纪人入口
            $data = (is_array($_GPC['cover']) ? $_GPC['cover'] : array());
            $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('qmjjr.opensource.cover'), 2);
            if (!empty($rule)) {
                pdo_delete('rule', array('id' => $rule['id'], 'uniacid' => $_W['uniacid']));
                pdo_delete('rule_keyword', array('rid' => $rule['id'], 'uniacid' => $_W['uniacid']));
                pdo_delete('cover_reply', array('rid' => $rule['id'], 'uniacid' => $_W['uniacid']));
            }

            $rule_data = array('uniacid' => $_W['uniacid'], 'name' => '经纪人入口_XG', 'module' => 'cover', 'displayorder' => 0, 'status' => intval($data['status']));
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

    public function batcredit(){
        global $_GPC,$_W;

        $selectid=$_GPC['selected'];
        if($_W['ispost']){
            $arr=explode(',',$selectid);
            $type='credit1';
            foreach($arr as $id){
                $profile = m('member')->getMember($id, true);
                $typestr = ($type == 'credit1' ? '积分' : '余额');
                $num = floatval($_GPC['num']);

                if ($num <= 0) {
                    show_json(0, array('message' => '请填写大于0的数字!'));
                }

                $changetype = intval($_GPC['changetype']);

                if ($changetype == 2) {
                    $num -= $profile[$type];
                }
                else {
                    if ($changetype == 1) {
                        $num = 0 - $num;
                    }
                }

                m('member')->setCredit($profile['openid'], $type, $num, array($_W['uid'], '后台会员充值' . $typestr));

                $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('qmjjr.opensource.cover'), 2);

                if($type=='credit1') {
                    $comissdata = array(
                        'uniacid' => $_W['uniacid'],
                        'mid' => $id,
                        'lid' => 0,
                        'cid' => 0,
                        'commis' => 0,
                        'credit' => $num,
                        'status' => 0,
                        'createtime' => time(),
                        'issh' => 1,
                        'isdk' => 1,
                        'issq' => 0,
                        'remark' => $_GPC['remark']
                    );
                    pdo_insert('xg_agent_commission', $comissdata);
                    sendCreditChange($profile['openid'],'后台修改',$_GPC['remark'],$num,$url);
                }else{
                    sendCommission($profile['openid'],'后台修改','','',date('Y-m-d H:i:s', time()),$num,date('Y-m-d H:i:s', time()),$profile['credit2'],date('Y-m-d H:i:s', time()),$url);
                }

                plog('qmjjr.recharge.' . $type, '充值' . $typestr . ': ' . $_GPC['num'] . ' <br/>会员信息: ID: ' . $profile['id'] . ' /  ' . $profile['openid'] . '/' . $profile['nickname'] . '/' . $profile['realname'] . '/' . $profile['mobile']."备注：".$_GPC['remark']);
            }
            show_json(1,array('url'=>webUrl('qmjjr')));
        }
        include $this->template();
    }


}