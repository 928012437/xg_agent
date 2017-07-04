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
        $uniacid=$_W['uniacid'];
        $condition = ' WHERE uniacid = :uniacid';
        $condition2 = '';
        $params = array(':uniacid' => $uniacid);

        if (!empty($_GPC['id'])) {
            $condition .= " and tid = 'j:" . $_GPC['id'] . "'";
        }

        if (!empty($_GPC['keyword'])) {
            $keyword = trim($_GPC['keyword']);
            $condition .= " and (name like '%" . $keyword . "%' or tel like '%" . $keyword . "%' or id like '%" . $keyword . "%')";
        }

        if(!empty($_GPC['cid'])){
            $condition.=" and cid=".$_GPC['cid'];
        }

        if(!empty($_GPC['lid'])){
            $condition.=" and lid=".$_GPC['lid'];
        }

        if(!empty($_GPC['gjstatu'])){
            $condition.=" and status=".$_GPC['gjstatu'];
        }

        if($_GPC['is_review']!=''){
            $condition.=" and is_review=".$_GPC['is_review'];
        }

        if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
            $condition .= " and createtime >=" . strtotime($_GPC['time']['start']) . " and createtime <=" . strtotime($_GPC['time']['end']);
        }

        if (empty($_GPC['export'])) {
            $condition2 = " ORDER BY updatetime ASC limit " . (($pindex - 1) * $psize) . "," . $psize;
        }

        $list = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $condition . $condition2, $params);

        foreach ($list as $key=>&$row) {
            $tempstatus = pdo_get('xg_agent_status', array('id' => $row['status']));
            $row['status'] = $tempstatus['name'];
            $tempuser = pdo_get('xg_agent_user', array('id' => $row['cid']));
            $row['cid'] = $tempuser['realname'];
            $row['isvalid'] = $row['isvalid'] == 0 ? '无效' : '有效';
            $row['is_gz'] = $row['is_gz'] == 1 ? '否' : '是';
            $log = pdo_fetch("select * from " . tablename('xg_agent_genjinlog') . " where uniacid=:uniacid and cid=:cid order by createtime desc", array(':uniacid' => $uniacid, ':cid' => $row['id']));
            if(!empty($_GPC['yxjb'])){
                if($log['gj_jb']!=$_GPC['yxjb']){
                    unset($list[$key]);
                }
            }
        }
        unset($row);

        if ($_GPC['export'] == '1') {
            plog('customer', '导出客户数据');

            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
            }

            unset($row);
            m('excel')->export($list, array(
                'title' => '客户数据-' . date('Y-m-d H点i分', time()),
                'columns' => array(
                    array('title' => '姓名', 'field' => 'name', 'width' => 12),
                    array('title' => '手机号', 'field' => 'tel', 'width' => 12),
                    array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
                    array('title' => '是否有效', 'field' => 'isvalid', 'width' => 12),
                    array('title' => '是否关注', 'field' => 'is_gz', 'width' => 12),
                    array('title' => '标签', 'field' => 'label', 'width' => 12),
                    array('title' => '备注', 'field' => 'mark', 'width' => 12)
                )
            ));
        }

        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_customer') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $loupan=pdo_getall('xg_agent_loupan',array('uniacid'=>$uniacid));
        $yxjb=pdo_getall('xg_agent_yxjb',array('uniacid'=>$uniacid));
        $status=pdo_getall('xg_agent_status',array('uniacid'=>$uniacid));

        include $this->template();
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
            pdo_update('xg_agent_customer', array('is_review' => $_GPC['review'],'shtime'=>time()), array('id' => $i));
            plog('qmjjr.review', '审核客户-' . $str . '，客户ID：' . $i);
        }

        show_json(1);
    }

    public function allot()
    {
        global $_W;
        global $_GPC;
        if ($_W['ispost']) {
            $selects = explode(',', trim($_GPC['selected']));
            $id = trim($_GPC['allot']);
            $tempuser = pdo_get('xg_agent_user', array('id' => $id));

            foreach ($selects as $v) {
                pdo_update('xg_agent_customer', array('cid' => $id, 'isvalid' => 1,'fentime'=>time()), array('id' => $v));
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'cid' => $v,
                    'userid' => $id,
                    'createtime' => time(),
                    'fpid' => 0
                );
                pdo_insert('xg_agent_fplog', $data);
                $cusromer = pdo_get('xg_agent_customer', array('id' => $v));
                $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('user.opensource.cover'), 2);
                if($tempuser['openid']!='') {
                    sendCustomerFP($tempuser['openid'], $cusromer['name'], $cusromer['tel'], '后台', $url);
                }
                plog('customer.allot', '分配客户给' . $tempuser['realname'] . '，客户ID：' . $v);
            }
            show_json(1, array('url' => webUrl('customer')));
        } else {
            $selects = trim($_GPC['selected']);
            $pindex = max(1, intval($_GPC['page']));
            $psize = 25;
            $condition = ' WHERE uniacid = :uniacid and flag = 0 ';
            $params = array(':uniacid' => $_W['uniacid']);
            $list = pdo_fetchall("select * from " . tablename('xg_agent_user') . $condition . " ORDER BY createtime DESC limit " . (($pindex - 1) * $psize) . "," . $psize, $params);

            $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_user') . $condition, $params);
            $pager = pagination($total, $pindex, $psize);
            include $this->template();
        }
    }

    public function isvalid()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        foreach ($id as $i) {
            pdo_update('xg_agent_customer', array('isvalid' => 0), array('id' => $i));
            plog('customer.isvalid', '客户置于无效，客户ID：' . $i);
        }
        show_json(1, array('url' => referer()));
    }

    public function delete()
    {
        global $_GPC;
        $id[0] = intval($_GPC['id']);

        if (empty($id[0])) {
            $id = $_GPC['ids'];
        }
        foreach ($id as $i) {
            pdo_delete('xg_agent_customer', array('id' => $i));
        }
        plog('customer.delete', '删除客户，客户ID：' . $id);
        show_json(1, array('url' => referer()));
    }

    public function detail()
    {
        global $_W;
        global $_GPC;
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];

        if ($_W['ispost']) {
            $optionstr = '';
            $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
            foreach ($radios as $k => $v) {
                if (!empty($_GPC['rad_' . $v['id']])) {
                    $optionstr .= $_GPC['rad_' . $v['id']] . ',';
                }elseif($v['must']==1){
                    show_json(0,$v['name'].'为必填项！');
                }
            }
            $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
            foreach ($checks as $k => $v) {
                if (!empty($_GPC['che_' . $v['id']])) {
                    foreach ($_GPC['che_' . $v['id']] as $v2) {
                        $optionstr .= $v2 . ',';
                    }
                }elseif($v['must']==1){
                    show_json(0,$v['name'].'为必填项！');
                }
            }
            $selects = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=2 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
            foreach ($selects as $k => $v) {
                if (!empty($_GPC['sel_' . $v['id']])) {
                    $optionstr .= $_GPC['sel_' . $v['id']] . ',';
                }elseif($v['must']==1){
                    show_json(0,$v['name'].'为必填项！');
                }
            }
            $detail = array(
                'status' => $_GPC['status'],
                'sex' => $_GPC['sex'],
                'option' => $optionstr
            );
            pdo_update('xg_agent_customer', $detail, array('id' => $id));
            plog('customer.detail', '修改客户信息，客户ID：' . $id);
            show_json(1);
        }
        $user = pdo_get('xg_agent_customer', array('uniacid' => $uniacid, 'id' => $id));
        $loupan=pdo_get('xg_agent_loupan',array('id'=>$user['lid']));
        $user['louname']=$loupan['title'];

//        单选
        $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($radios as $k => $v) {
            $radio = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            $radios[$k]['sub'] = $radio;
        }
//        多选
        $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($checks as $k => $v) {
            $check = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            $checks[$k]['sub'] = $check;
        }
//        下拉
        $selects = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=2 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($selects as $k => $v) {
            $select = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            $selects[$k]['sub'] = $select;
        }
        $status = pdo_fetchall("select * from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder desc", array(':uniacid' => $uniacid));

        $tarr = explode(':', $user['tid']);
        if (count($tarr) == 2 && $tarr[0] == 'j') {
            $tempuser = pdo_get('xg_agent_member', array('id' => $tarr[1]));
            $name = $tempuser['realname'];
        }
        $genjin = pdo_fetchall("select * from " . tablename('xg_agent_genjinlog') . " where uniacid=:uniacid and cid = :cid order by createtime desc ", array(':uniacid' => $uniacid, ':cid' => $id));

        foreach ($genjin as &$v) {
            $tempstatus = pdo_get('xg_agent_status', array('id' => $v['gj_stuta']));
            $v['gj_stuta'] = $tempstatus['name'];
            $tempuser = pdo_get('xg_agent_user', array('id' => $v['userid']));
            $v['gjren'] = $tempuser['realname'];
        }
        unset($v);

        $zhuanjie = pdo_fetchall("select * from " . tablename('xg_agent_fplog') . " where uniacid=:uniacid and cid = :cid order by createtime desc ", array(':uniacid' => $uniacid, ':cid' => $id));

        foreach ($zhuanjie as &$v) {
            if($v['userid']==0){
                $arr=explode(':',$v['fpid']);
                if($arr[0]=='j'){
                    $tempjjr=pdo_get('xg_agent_member',array('id'=>$arr[1]));
                    $v['text']="由经纪人：<a href='".webUrl('qmjjr.detail',array('id'=>$arr[1]))."'>".$tempjjr['realname']."</a>新增。";
                }else{
                    $tempuser=pdo_get('xg_agent_user',array('id'=>$arr[1]));
                    $v['text']="由置业顾问：<a href='".webUrl('business.detail',array('id'=>$arr[1]))."'>".$tempuser['realname']."</a>新增。";
                }
            }else{
                $tempuser = pdo_get('xg_agent_user', array('id' => $v['userid']));
                if($v['fpid']==0){
                    $v['text'] = "由后台分配给置业顾问：<a href='".webUrl('business.detail',array('id'=>$v['userid']))."'>" .$tempuser['realname']."</a>";
                }else {
                    $tempjl = pdo_get('xg_agent_user', array('id' => $v['fpid']));
                    $v['text'] = "由<a href='".webUrl('business.detail',array('id'=>$v['fpid']))."'>" . $tempjl['realname'] . "</a>分配给置业顾问：<a href='".webUrl('business.detail',array('id'=>$v['userid']))."'>" .$tempuser['realname']."</a>";
                }
            }
        }
        unset($v);

        include $this->template();
    }

    public function pool()
    {
        global $_W;
        global $_GPC;
        $condition = ' WHERE uniacid = :uniacid';
        $params = array(':uniacid' => $_W['uniacid']);
        $pools = pdo_fetchall('select * from ' . tablename('xg_agent_customer') . $condition . " ORDER BY createtime DESC ", $params);
        $statuss = pdo_fetchall('select * from ' . tablename('xg_agent_status') . $condition, $params);
        foreach ($pools as $k => $v) {
            foreach ($statuss as $v2) {
                if ($v['status'] == $v2['id']) {
                    if ($v['isvalid'] == 1 || $v2['baohuqi'] * 86400 + $v['updatetime'] < time()) {
                        unset($pools[$k]);
                    } else {
                        $pools[$k]['status'] = $v2['name'];
                        $pools[$k]['chendian'] = $v2['baohuqi'] * 86400 + $v['updatetime'];
                    }
                }
            }
        }
        unset($v);

        include $this->template();
    }

    public function gjset()
    {
        global $_W;
        global $_GPC;
        $uniacid = $_W['uniacid'];
        if ($_W['ispost']) {
            if (!empty($_GPC['name'])) {
                foreach ($_GPC['name'] as $key => $v) {
                    $cusstat = array(
                        'displayorder' => intval($_GPC['displayorder'][$key]),
                        'uniacid' => $uniacid,
                        'name' => $v,
                        'name2' => $_GPC['jjrname'][$key],
                        'baohuqi' => $_GPC['baohuqi'][$key],
                        'tixing' => $_GPC['tixing'][$key],
                        'type' => intval($_GPC['type'][$key]),
                    );
                    if (intval($_GPC['csid'][$key])) {
                        pdo_update('xg_agent_status', $cusstat, array('id' => $_GPC['csid'][$key]));
                    } else {
                        pdo_insert('xg_agent_status', $cusstat);
                    }
                }
            }
            if (!empty($_GPC['name2'])) {
                foreach ($_GPC['name2'] as $key => $v) {
                    $cusstat = array(
                        'uniacid' => $uniacid,
                        'name' => $v
                    );
                    if (intval($_GPC['csid2'][$key])) {
                        pdo_update('xg_agent_gjfs', $cusstat, array('id' => $_GPC['csid2'][$key]));
                    } else {
                        pdo_insert('xg_agent_gjfs', $cusstat);
                    }
                }
            }
            if (!empty($_GPC['name3'])) {
                foreach ($_GPC['name3'] as $key => $v) {
                    $cusstat = array(
                        'uniacid' => $uniacid,
                        'name' => $v
                    );
                    if (intval($_GPC['csid3'][$key])) {
                        pdo_update('xg_agent_yxjb', $cusstat, array('id' => $_GPC['csid3'][$key]));
                    } else {
                        pdo_insert('xg_agent_yxjb', $cusstat);
                    }
                }
            }
            if (!empty($_GPC['name4'])) {
                foreach ($_GPC['name4'] as $key => $v) {
                    $cusstat = array(
                        'uniacid' => $uniacid,
                        'cont' => $v
                    );
                    if (intval($_GPC['csid4'][$key])) {
                        pdo_update('xg_agent_gjnr', $cusstat, array('id' => $_GPC['csid4'][$key]));
                    } else {
                        pdo_insert('xg_agent_gjnr', $cusstat);
                    }
                }
            }
            plog('customer.gjset', '修改客户跟进设置');
            show_json(1);
        }
        $custsts = pdo_getall('xg_agent_status', array('uniacid' => $uniacid));
        $gjfs = pdo_getall('xg_agent_gjfs', array('uniacid' => $uniacid));
        $yxjb = pdo_getall('xg_agent_yxjb', array('uniacid' => $uniacid));
        $gjnr = pdo_getall('xg_agent_gjnr', array('uniacid' => $uniacid));

        include $this->template();
    }


}

?>
