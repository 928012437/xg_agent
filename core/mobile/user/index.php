<?php


if (!defined('IN_IA')) {

    exit('Access Denied');

}


class Index_XgAgentPage extends MobilePage

{


    function __construct()

    {

        // 指定允许其他域名访问

        header('Access-Control-Allow-Origin:*');

// 响应类型

        header('Access-Control-Allow-Methods:POST');

// 响应头设置

        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        parent::__construct();

        $this->sess_id = 7;

        $columnname = pdo_fetchall("select COLUMN_NAME from information_schema.COLUMNS where table_name = " . str_replace("`", "'", tablename('xg_agent_user')));

        $strcolumn = '';

        foreach ($columnname as &$v) {

            $strcolumn .= $v['COLUMN_NAME'] . ',';

        }

        unset($v);

        $strcolumn = str_replace("accounts,", "", $strcolumn);

        $strcolumn = str_replace("password,", "", $strcolumn);

        $this->strcolumn = substr($strcolumn, 0, -1);

        $this->user = pdo_fetch("select $this->strcolumn from " . tablename('xg_agent_user') . " where id=:id", array(':id' => $this->sess_id));

//        if ($user==''||$user['flag'] == 0) {

//            echo json_encode(array('code' => 0, 'message' => '无执行权限'));

//            die;

//        }

    }


    public function getfournum()

    {

        global $_W;

        $uniacid = $_W['uniacid'];

        $condition = ' WHERE uniacid = :uniacid';

        $params = array(':uniacid' => $uniacid);

        $time = time();


        $tempcustomer = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_customer') . $condition, $params);

        $status = pdo_getall('xg_agent_status', array('uniacid' => $uniacid));


        $yqnum = 0;

        $ggnum = 0;

        $wxnum = 0;

        $gjnum = 0;


        foreach ($tempcustomer as $c) {

            //逾期,跟进

            foreach ($status as $s) {

                if ($c['status'] == $s['id']) {

                    if ($c['updatetime'] + $s['baohuqi'] * 86400 < $time) {

                        $yqnum++;

                    } else {

                        $gjnum++;

                    }

                }

            }

            //公共无效

            if ($c['cid'] == 0) {

                $ggnum++;

            }

            if ($c['isvalid'] == 0) {

                $wxnum++;

            }


        }


        echo json_encode(array(

            'code' => 3,

            'data' => array(

                'yqnum' => $yqnum,

                'ggnum' => $ggnum,

                'wxnum' => $wxnum,

                'gjnum' => $gjnum,

            )

        ));


    }


    public function getuclist()

    {

        global $_W;

        global $_GPC;

        $uniacid = $_W['uniacid'];

        $type = $_GPC['type'];

        $status = $_GPC['status'];

        $pindex = max(1, intval($_GPC['page']));

        $psize = 10;

        $condition = ' WHERE uniacid = :uniacid';

        $params = array(':uniacid' => $uniacid);

        $conditionu = $condition . " and flag = 0";

        $list = pdo_fetchall("select $this->strcolumn from " . tablename('xg_agent_user') . $conditionu . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);

        $statuss = pdo_getall('xg_agent_status', array('uniacid' => $uniacid));

        $time = time();

        foreach ($list as $k => $v) {

            $conditionc = $condition . " and cid = " . $v['id'];
            $num=0;

            if ($status != '') {

                $conditionc .= " and status = " . $status;

            }

            $tempcustomer = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_customer') . $conditionc, $params);


            foreach ($tempcustomer as $k2 => $c) {

                //逾期,跟进

                foreach ($statuss as $s) {

                    if ($c['status'] == $s['id']) {

                        if ($c['updatetime'] + $s['baohuqi'] * 86400 < $time) {

                            if ($type == 1) {


                                $num++;

                            }

                        } else {

                            if ($type == 2) {


                                $num++;

                            }

                        }

                    }

                }

            }


            if ($num == 0) {

                unset($list[$k]);

            } else {

                $list[$k]['num'] = $num;

            }


        }


        echo json_encode(array('code' => 3, 'data' => $list));

    }


    public function getcustomerlist()

    {

        global $_W;

        global $_GPC;

        $uniacid = $_W['uniacid'];

        $time = time();

        $type = $_GPC['type'];

        $status = $_GPC['status'];

        $cid = $_GPC['cid'];

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $condition = ' WHERE uniacid = :uniacid';

        if ($status != '' && $status != 0) {

            $condition .= " and status = " . $status;

        }

        if ($cid != '') {

            $condition .= " and cid = " . $cid;

        }

        $params = array(':uniacid' => $uniacid);

//        $list = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);

        $list = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $condition, $params);

        $tempstatus = pdo_getall('xg_agent_status', array('uniacid' => $uniacid));

        $truelist = array();

        foreach ($list as $k => $v) {

            //逾期,跟进

            foreach ($tempstatus as $s) {

                if ($v['status'] == $s['id']) {

                    if ($v['updatetime'] + $s['baohuqi'] * 86400 < $time) {

                        if ($type == 1) {

                            $v['statusname'] = $s['name'];
                            $v['createtime'] = date('Y-m-d H:i', $v['createtime']);
                            $v['text'] = "逾期：" . sprintf("%.2f", (($time - $v['updatetime'] - $s['baohuqi'] * 86400) / 86400)) . "天";

                            $truelist[] = $v;

                        }

                    } else {

                        if ($type == 2) {

                            $v['statusname'] = $s['name'];
                            $v['createtime'] = date('Y-m-d H:i', $v['createtime']);
                            $tempu = pdo_fetch("select * from " . tablename('xg_agent_genjinlog') . " where cid=:cid order by createtime desc ", array(':cid' => $v['id']));
                            if ($tempu == '') {
                                $v['text']="未跟进";
                            } else {
                                $v['text'] = $tempu['gj_fs'] . ":" . date('Y-m-d H:i', $tempu['createtime']);
                            }
                            $truelist[] = $v;

                        }

                    }


                    //公共无效

                    if ($v['cid'] == 0) {

                        if ($type == 3) {

                            $v['statusname'] = $s['name'];
                            $v['createtime'] = date('Y-m-d H:i', $v['createtime']);
                            $arr = explode(':', $v['tid']);
                            if ($arr[0] == 'j') {
                                $tempj = pdo_get('xg_agent_member', array('id' => $arr[1]));
                                $v['tname'] = '经纪人-' . $tempj['realname'];
                            } elseif ($arr[0] == 'x') {
                                $tempx = pdo_get('xg_agent_user', array('id' => $arr[1]));
                                $v['tname'] = '销售-' . $tempx['realname'];
                            }

                            $truelist[] = $v;

                        }

                    }

                    if ($v['isvalid'] == 0) {

                        if ($type == 4) {

                            $v['statusname'] = $s['name'];
                            $v['createtime'] = date('Y-m-d H:i', $v['createtime']);
                            $arr = explode(':', $v['tid']);
                            if ($arr[0] == 'j') {
                                $tempj = pdo_get('xg_agent_member', array('id' => $arr[1]));
                                $v['tname'] = '经纪人-' . $tempj['realname'];
                            } elseif ($arr[0] == 'x') {
                                $tempx = pdo_get('xg_agent_user', array('id' => $arr[1]));
                                $v['tname'] = '销售-' . $tempx['realname'];
                            }

                            $truelist[] = $v;

                        }

                    }


                }

            }


        }


        echo json_encode(array('code' => 3, 'data' => $truelist));

    }


    public function getstatus()

    {

        global $_W;

        $uniacid = $_W['uniacid'];

        $time = time();

        $status = pdo_fetchall("select id,name,baohuqi from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder", array(':uniacid' => $uniacid));

        foreach ($status as $k => $s) {

            $status[$k]['num'] = 0;

        }

        $tempcustomers = pdo_fetchall("select * from " . tablename('xg_agent_customer') . " where uniacid=:uniacid", array(':uniacid' => $uniacid));

        foreach ($tempcustomers as $k => $v) {

            //逾期,跟进

            foreach ($status as $k => $s) {

                if ($v['status'] == $s['id']) {

                    if ($v['updatetime'] + $s['baohuqi'] * 86400 < $time) {

                        $status[$k]['num']++;

                    }

                }

            }

        }


        echo json_encode(array('code' => 3, 'data' => $status));

    }


    public function getoption1()
    {

        global $_W;

        $uniacid = $_W['uniacid'];

        $option1 = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where level=0 and status=0 and uniacid=:uniacid order by displayorder limit 0,3", array(':uniacid' => $uniacid));


        echo json_encode(array('code' => 3, 'data' => $option1));

    }


    public function getoption2()
    {

        global $_W, $_GPC;

        $uniacid = $_W['uniacid'];

        $stime = $_GPC['stime'];

        $etime = $_GPC['etime'];

        $op1 = $_GPC['op1'];

        $status = $_GPC['status'];

        $condition = ' where uniacid = :uniacid';

        $params = array(':uniacid' => $uniacid);

        if ($stime != '' && $etime != '') {

            $condition .= " and updatetime >= " . $stime . " and updatetime < " . $etime;

        }

        if ($status != '') {

            $condition .= " and status = " . $status;

        }


        $op2 = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where level=1 and status=0 and uniacid=:uniacid and oid=:oid  ", array(':uniacid' => $uniacid, ':oid' => $op1));

        $bili = [];

        foreach ($op2 as $v) {

            $tempnum = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . $condition . " and (`option` like '%" . ($v['id'] . ',') . "%' or `option` like '%" . (',' . $v['id']) . "%') ", $params);

            $bili[] = array('value' => $tempnum, 'name' => $v['name']);

        }


        echo json_encode(array('code' => 3, 'data' => $bili));

    }

    public function gettuandui()

    {
        global $_W;

        $uniacid = $_W['uniacid'];

        $tuandui = pdo_getall('xg_agent_tuandui', array('uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $tuandui));
    }

    public function getalluser()

    {

        global $_W;

        $uniacid = $_W['uniacid'];

        $condition = " where uniacid = :uniacid and is_review = 1 and status = 0 and flag=0 ";

        $params = array(':uniacid' => $uniacid);

        $userlist = pdo_fetchall("select id,realname,tid from " . tablename('xg_agent_user') . $condition, $params);

        $tuanduilistdefault = array(

            'id' => 0,

            'name' => '无团队'

        );

        $tuanduilist = pdo_getall('xg_agent_tuandui', array('uniacid' => $uniacid));


        array_unshift($tuanduilist, $tuanduilistdefault);


        foreach ($tuanduilist as $k => $v2) {

            foreach ($userlist as &$v) {

                if ($v['tid'] == $v2['id']) {

                    $tuanduilist[$k]['xiaji'][] = $v;

                }

            }

            if (!isset($tuanduilist[$k]['xiaji'])) {

                unset($tuanduilist[$k]);

            }

        }

        unset($v);

        echo json_encode(array('code' => 3, 'data' => $tuanduilist));

    }


    //分配客户

    public function allot()

    {

        global $_W;

        global $_GPC;

        $uniacid = $_W['uniacid'];

        $condition = " where uniacid=:uniacid ";

        $parmas = array(':uniacid' => $uniacid);

        $time = time();

        $type_all = $_GPC['type_all'];

        $type_w = $_GPC['type_w'];

        $selectid = $_GPC['selectid'];

        $allid = $_GPC['allid'];

        $id = $_GPC['id'];

        if ($id == "0") {
            echo json_encode(array('code' => 0, 'message' => '未选择分配人！'));
            die;
        }

//        if ($type_all == 0) {

//            $fpid = $selectid;

//            if ($type_w == 1) {

//                $conditionc = $condition . " and (id in(" . $fpid . ") or id not in(" . $allid . "))";

//            } else {

//                $conditionc = $condition . " and id in(" . $fpid . ")";

//            }

//

//        }


        $mid = $_GPC['mid'];

        $cid = $_GPC['cid'];

        if ($mid != '' && $cid != '') {

            $conditionc = $condition . " and (cid in(" . $mid . ") or id in(" . $cid . "))";

        } elseif ($mid != '' && $cid == '') {

            $conditionc = $condition . " and cid in(" . $mid . ")";

        } elseif ($mid == '' && $cid != '') {

            $conditionc = $condition . " and  id in(" . $cid . ")";

        } else {

            echo json_encode(array('code' => 0, 'message' => '未选择客户！'));
            die;

        }

        $tempcustomers = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $conditionc, $parmas);

        $tempstatus = pdo_fetchall("select * from " . tablename('xg_agent_status') . $condition, $parmas);


        $data = array('cid' => $id, 'isvalid' => 1, 'fentime' => $time);

        $data2 = array(

            'uniacid' => $uniacid,

            'userid' => $id,

            'createtime' => $time,

            'fpid' => $this->sess_id

        );


        foreach ($tempcustomers as $v) {

            pdo_update('xg_agent_customer', $data, array('id' => $v['id']));

            $data2['cid'] = $v['id'];

            pdo_insert('xg_agent_fplog', $data2);

        }

        echo json_encode(array('code' => 2, 'message' => '分配成功！'));

    }


    //回收客户

    public function unallot()

    {

        global $_W;

        global $_GPC;

        $uniacid = $_W['uniacid'];

        $condition = " where uniacid=:uniacid ";

        $parmas = array(':uniacid' => $uniacid);

        $time = time();

        //1逾期，2跟进，3公共，4无效

        $type = $_GPC['type'];

        $type_all = $_GPC['type_all'];

        $type_w = $_GPC['type_w'];

        $selectid = $_GPC['selectid'];

        $allid = $_GPC['allid'];

        $cid = $_GPC['cid'];

        $content = $_GPC['content'];


        if ($type_all == 0) {

            $fpid = $selectid;

            if ($type_w == 1) {

                $conditionc = $condition . " and (id in(" . $fpid . ") or id not in(" . $allid . "))";

            } else {

                $conditionc = $condition . " and id in(" . $fpid . ")";

            }


        }


        $tempcustomers = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $conditionc, $parmas);

        $tempstatus = pdo_fetchall("select * from " . tablename('xg_agent_status') . $condition, $parmas);

        $data = array(

            'uniacid' => $uniacid,

            'createtime' => $time,

            'userid' => $cid,

            'hsid' => $this->sess_id,

            'content' => $content

        );


        foreach ($tempcustomers as $v) {

            //逾期,跟进

            foreach ($tempstatus as $s) {

                if ($v['status'] == $s['id']) {

                    if ($v['updatetime'] + $s['baohuqi'] * 86400 < $time) {

                        if ($type == 1) {

                            pdo_update('xg_agent_customer', array('cid' => 0), array('id' => $v['id']));

                            $data['cid'] = $v['id'];

                            pdo_insert('xg_agent_huishoulog', $data);

                        }

                    } else {

                        if ($type == 2) {

                            pdo_update('xg_agent_customer', array('cid' => 0), array('id' => $v['id']));

                            $data['cid'] = $v['id'];

                            pdo_insert('xg_agent_huishoulog', $data);

                        }

                    }

                }

            }

            //公共无效

            if ($v['cid'] == 0) {

                if ($type == 3) {

                    pdo_update('xg_agent_customer', array('cid' => 0), array('id' => $v['id']));

                    $data['cid'] = $v['id'];

                    pdo_insert('xg_agent_huishoulog', $data);

                }

            }

            if ($v['isvalid'] == 0) {

                if ($type == 4) {

                    pdo_update('xg_agent_customer', array('cid' => 0), array('id' => $v['id']));

                    $data['cid'] = $v['id'];

                    pdo_insert('xg_agent_huishoulog', $data);

                }

            }

        }


        echo json_encode(array('code' => 2, 'message' => '回收成功！'));

    }


    //无效化客户

    public function valid()

    {

        global $_W;

        global $_GPC;

        $uniacid = $_W['uniacid'];

        $condition = " where uniacid=:uniacid ";

        $parmas = array(':uniacid' => $uniacid);

        $time = time();

        //1逾期，2跟进，3公共，4无效

        $type = $_GPC['type'];

        $type_all = $_GPC['type_all'];

        $type_w = $_GPC['type_w'];

        $selectid = $_GPC['selectid'];

        $allid = $_GPC['allid'];


        if ($type_all == 0) {

            $fpid = $selectid;

            if ($type_w == 1) {

                $conditionc = $condition . " and (id in(" . $fpid . ") or id not in(" . $allid . "))";

            } else {

                $conditionc = $condition . " and id in(" . $fpid . ")";

            }


        }


        $tempcustomers = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $conditionc, $parmas);

        $tempstatus = pdo_fetchall("select * from " . tablename('xg_agent_status') . $condition, $parmas);

        $data = array(

            'uniacid' => $uniacid,

            'wxid' => $this->sess_id,

            'createtime' => $time,

            'content' => $_GPC['content']

        );


        foreach ($tempcustomers as $v) {

            //逾期,跟进

            foreach ($tempstatus as $s) {

                if ($v['status'] == $s['id']) {

                    if ($v['updatetime'] + $s['baohuqi'] * 86400 < $time) {

                        if ($type == 1) {

                            pdo_update('xg_agent_customer', array('isvalid' => 0), array('id' => $v['id']));

                            $data['cid'] = $v['id'];

                            pdo_insert('xg_agent_wuxiaolog', $data);

                        }

                    } else {

                        if ($type == 2) {

                            pdo_update('xg_agent_customer', array('isvalid' => 0), array('id' => $v['id']));

                            $data['cid'] = $v['id'];

                            pdo_insert('xg_agent_wuxiaolog', $data);

                        }

                    }

                }

            }

            //公共无效

            if ($v['cid'] == 0) {

                if ($type == 3) {

                    pdo_update('xg_agent_customer', array('isvalid' => 0), array('id' => $v['id']));

                    $data['cid'] = $v['id'];

                    pdo_insert('xg_agent_wuxiaolog', $data);

                }

            }

            if ($v['isvalid'] == 0) {

                if ($type == 4) {

                    pdo_update('xg_agent_customer', array('isvalid' => 0), array('id' => $v['id']));

                    $data['cid'] = $v['id'];

                    pdo_insert('xg_agent_wuxiaolog', $data);

                }

            }

        }


        echo json_encode(array('code' => 2, 'message' => '置于无效成功！'));

    }


}