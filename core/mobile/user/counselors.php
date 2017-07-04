<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Counselors_XgAgentPage extends MobilePage
{
    function __construct()
    {
        global $_W, $_GPC;
        parent::__construct();
        $columnname = pdo_fetchall("select COLUMN_NAME from information_schema.COLUMNS where table_name = " . str_replace("`", "'", tablename('xg_agent_user')));
        $strcolumn = '';
        foreach ($columnname as &$v) {
            $strcolumn .= $v['COLUMN_NAME'] . ',';
        }
        unset($v);
        $strcolumn = str_replace("accounts,", "", $strcolumn);
        $strcolumn = str_replace("password,", "", $strcolumn);
        $this->strcolumn = substr($strcolumn, 0, -1);
        $this->user = pdo_fetch("select $this->strcolumn from " . tablename('xg_agent_user') . " where uniacid=:uniacid and token=:token ", array(':uniacid' => $_W['uniacid'], ':token' => $_GPC['token']));
        $this->sess_id = $this->user['id'];
        if (empty($this->user) || $_GPC['token'] == '') {
            echo json_encode(array('code' => 0, 'message' => '您还未登录'));
            die;
        }
        if ($this->user['flag'] != 0) {
            echo json_encode(array('code' => 0, 'message' => '无执行权限'));
            die;
        }
    }

    public function loginok()
    {
        echo json_encode(array('code' => 1));
    }

    public function main()
    {
        global $_W, $_GPC;
        $cid = $this->sess_id;
        $uniacid = $_W['uniacid'];
        $status = $_GPC['status'];
        $sort = $_GPC['sort'];
        $ordertype = $_GPC['ordertype'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $condition = ' WHERE uniacid = :uniacid';
        $params = array(':uniacid' => $uniacid);
        $condition .= " and cid = " . $cid;

        if ($sort != '') {
            $condition .= " and (name like '%" . $sort . "%' or tel like '%" . $sort . "%' )";
        }

        if ($status != '') {
            if ($status == 0) {
                $condition .= " and is_gz = 1";
            } else {
                $condition .= " and status = " . $status;
            }
        }

        if ($ordertype == 1) {
            $condition .= " order by updatetime desc";
        } elseif ($ordertype == 2) {
            $condition .= " order by updatetime asc";
        } elseif ($ordertype == 3) {
            $condition .= " order by createtime desc";
        } elseif ($ordertype == 4) {
            $condition .= " order by createtime asc";
        }

        $customerlist = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_customer') . $condition, $params);
        foreach ($customerlist as &$v) {
            $log = pdo_fetch("select * from " . tablename('xg_agent_genjinlog') . " where uniacid=:uniacid and cid=:cid order by createtime desc", array(':uniacid' => $uniacid, ':cid' => $v['id']));
            if ($log == '') {
                $v['gj_nr'] = '暂无跟进';
                $v['gj_jb'] = '';
                $v['gj_fs'] = '';
                $v['gj_xc'] = '';
            } else {
                $v['gj_nr'] = $log['gj_nr'];
                $v['gj_jb'] = $log['gj_jb'];
                $v['gj_fs'] = $log['gj_fs'];
                $v['gj_xc'] = date('Y-m-d', $log['gj_xc']);
            }
            $v['is_gz'] = $v['is_gz'] ? true : false;
        }
        unset($v);


        echo json_encode(array('code' => 3, 'data' => $customerlist, 'count' => $total));
    }

    public function getstatus()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $status = pdo_fetchall("select id,name from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder", array(':uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $status));
    }

    public function changegz()
    {
        global $_GPC;
        $id = $_GPC['id'];
        $is_gz = $_GPC['is_gz'] == 'false' ? 1 : 0;
        pdo_update('xg_agent_customer', array('is_gz' => $is_gz), array('id' => $id));

        echo json_encode(array('code' => 4, 'status' => $is_gz));
    }

    public function getdetail()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        $detail = pdo_get('xg_agent_customer', array('id' => $id));
        $genjinlog = pdo_fetchall("select * from " . tablename('xg_agent_genjinlog') . " where cid=:cid order by createtime desc", array(':cid' => $id));
        $fplog = pdo_fetchall("select * from " . tablename('xg_agent_fplog') . " where cid=:cid order by createtime desc", array(':cid' => $id));
        $status = pdo_get('xg_agent_status', array('id' => $detail['status']));
        $detail['statusname'] = $status['name'];
        $loupan = pdo_get('xg_agent_loupan', array('id' => $detail['lid']));
        $detail['loupan'] = $loupan['title'];
        $laidian = 0;
        $laifang = 0;
        if ($detail['sex'] == 1) {
            $detail['headurl'] = "../../src/img/man.png";
        } else {
            $detail['headurl'] = "../../src/img/woman.png";
        }
        if ($detail['option'] == '') {
            $num1 = 0;
            $op0 = 1;
        } else {
            $detail['option'] = substr($detail['option'], 0, -1);
            $arr = explode(',', $detail['option']);
            $op0 = pdo_getall('xg_agent_option', array("uniacid" => $uniacid, 'level' => 0));
            $num1 = count($arr);
        }
        $detail['baifenbi'] = round($num1 / count($op0) * 100, 1);

        $tempgjfs = pdo_getall('xg_agent_gjfs', array('uniacid' => $uniacid));
        foreach ($genjinlog as &$v) {
            $v['time'] = date('Y-m-d', $v['createtime']) . $v['gj_fs'];
            $v['active'] = $v['gj_jb'] . ':' . $v['gj_nr'];
            $tempuser = pdo_get('xg_agent_user', array('id' => $v['userid']));
            $v['content'] = '跟进人：' . $tempuser['realname'];
            $v['xiaci'] = date('Y-m-d', $v['gj_xc']) . '需跟进';
            if ($v['gj_fs'] == $tempgjfs[0]['name']) {
                $laidian++;
            } elseif ($v['gj_fs'] == $tempgjfs[1]['name']) {
                $laifang++;
            }
        }
        unset($v);

        foreach ($fplog as &$v) {
            $v['time'] = date('Y-m-d', $v['createtime']);
            if ($v['fpid'] == '0') {
                $zhuzai = '系统后台';
            } else {
                $arrf = explode(':', $v['fpid']);
                if ($arrf[0] == 'j') {
                    $tempm = pdo_get('xg_agent_member', array('id' => $arrf[1]));
                    $zhuzai = '经纪人:' . $tempm['realname'];
                    $tel = $tempm['mobile'];
                } else {
                    $tempu = pdo_get('xg_agent_user', array('id' => $arrf[1]));
                    if ($tempu['flag'] == 0) {
                        $tstr = '置业顾问';
                    } else {
                        $tstr = '经理';
                    }
                    $zhuzai = $tstr . ':' . $tempu['realname'];
                    $tel = $tempu['mobile'];
                }
            }

            if ($v['userid'] == 0) {
                $v['active'] = '新增';
                $v['content'] = html_entity_decode("由" . $zhuzai . "(<a href='tel:$tel'>" . $tel . "</a>)新增，客户描述：" . $detail['mark']);
            } else {
                $v['active'] = '分配';
                $xiaji = pdo_get('xg_agent_user', array('id' => $v['userid']));
                $v['content'] = "由" . $zhuzai . "分配给" . $xiaji['realname'];
            }
            $v['xiaci'] = '';
        }
        unset($v);

        $option = pdo_getall('xg_agent_option', array('uniacid' => $uniacid, 'level' => 0, 'status' => 0));
        foreach ($option as &$v) {
            $option2 = pdo_getall('xg_agent_option', array('uniacid' => $uniacid, 'level' => 1, 'status' => 0, 'oid' => $v['id']));
            foreach ($option2 as $v2) {
                if (strstr($detail['option'] . ',', $v2['id'] . ',')) {
                    if (isset($v['value'])) {
                        $v['value'] .= ',' . $v2['name'];
                    } else {
                        $v['value'] = $v2['name'];
                    }
                }
            }
        }
        unset($v);

        $arrtid = explode(':', $detail['tid']);
        if ($arrtid[0] == 'j') {
            $tempjjr = pdo_get('xg_agent_member', array('id' => $arrtid[1]));
            $jjren = '经纪人：' . $tempjjr['realname'] . ':' . $tempjjr['mobile'];
        } else {
            $jjren = '';
        }

        echo json_encode(array('code' => 3, 'data' => $detail, 'genjinlog' => $genjinlog, 'fplog' => $fplog, 'message' => $option, 'num' => $tempgjfs[0]['name'] . '：' . $laidian . " " . $tempgjfs[1]['name'] . "：" . $laifang, 'jjren' => $jjren));
    }

    public function getoption()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        if ($id != '') {
            $detail = pdo_get('xg_agent_customer', array('id' => $id));
        }
//        单选
        $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($radios as $k => $v) {
            $radio = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            $radios[$k]['opt'] = $radio;
            if ($v['must'] == 1) {
                $radios[$k]['must'] = true;
            } else {
                $radios[$k]['must'] = false;
            }
            foreach ($radio as $v2) {
                if (isset($detail) && strstr($detail['option'], $v2['id'] . ',')) {
                    if (strlen($v2['id']) == 1) {
                        $qstr = '0';
                    } else {
                        $qstr = '';
                    }
                    $radios[$k]['value'] = $qstr . $v2['id'] . ':' . $v2['name'];
                }
            }
        }

//        多选
        $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($checks as $k => $v) {
            $check = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            if ($v['must'] == 1) {
                $checks[$k]['must'] = true;
            } else {
                $checks[$k]['must'] = false;
            }
            foreach ($check as &$v2) {
                if (isset($detail) && strstr($detail['option'], $v2['id'] . ',')) {
                    $v2['checked'] = 1;
                } else {
                    $v2['checked'] = 0;
                }
            }
            $checks[$k]['opt'] = $check;
            unset($v2);
        }
//        下拉
        $selects = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and type=2 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($selects as $k => $v) {
            $select = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            $selects[$k]['opt'] = $select;
            if ($v['must'] == 1) {
                $selects[$k]['must'] = true;
            } else {
                $selects[$k]['must'] = false;
            }
            foreach ($select as $v2) {
                if (isset($detail) && strstr($detail['option'], $v2['id'] . ',')) {
                    if (strlen($v2['id']) == 1) {
                        $qstr = '0';
                    } else {
                        $qstr = '';
                    }
                    $selects[$k]['value'] = $qstr . $v2['id'] . ':' . $v2['name'];
                }
            }
        }

        $data = array('radios' => $radios, 'checks' => $checks, 'selects' => $selects);

        echo json_encode(array('code' => 3, 'data' => $data));
    }

    public function insertcustomer()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $userid = $this->sess_id;
        $time = time();
        if ($_GPC['lid'] == '' || $_GPC['name'] == '' || $_GPC['tel'] == '' || $_GPC['sex'] == '' || $_GPC['label'] == '' || $_GPC['mark'] == '' || $_GPC['gjfs'] == '' || $_GPC['yxjb'] == '' || $_GPC['gj_xc'] == '') {
            echo json_encode(array('code' => 0, 'message' => '信息不完整！'));
            die;
        }
        $status = pdo_fetch("select id from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder", array(':uniacid' => $uniacid));

        $data1 = array(
            'uniacid' => $uniacid,
            'tid' => "x:" . $userid,
            'lid' => explode(':', $_GPC['lid'])[0],
            'name' => $_GPC['name'],
            'tel' => $_GPC['tel'],
            'sex' => $_GPC['sex'],
            'status' => $status['id'],
            'createtime' => $time,
            'updatetime' => $time,
            'isvalid' => 1,
            'is_gz' => 0,
            'label' => $_GPC['label'],
            'mark' => $_GPC['mark']
        );
        $option = "";
        $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($radios as $k => $v) {
            if (!empty($_GPC['rad_' . $v['id']])) {
                $option .= explode(':', $_GPC['rad_' . $v['id']])[0] . ',';
            } elseif ($v['must'] == 1) {
                echo json_encode(array('code' => 0, 'message' => $v['name'] . '为必填项！'));
                die;
            }
        }
        $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($checks as $k => $v) {
            if (!empty($_GPC['che_' . $v['id']])) {
                foreach ($_GPC['che_' . $v['id']] as $v2) {
                    $option .= $v2 . ',';
                }
            } elseif ($v['must'] == 1) {
                echo json_encode(array('code' => 0, 'message' => $v['name'] . '为必填项！'));
                die;
            }
        }
        $selects = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=2 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($selects as $k => $v) {
            if (!empty($_GPC['sel_' . $v['id']])) {
                $option .= explode(':', $_GPC['sel_' . $v['id']])[0] . ',';
            } elseif ($v['must'] == 1) {
                echo json_encode(array('code' => 0, 'message' => $v['name'] . '为必填项！'));
                die;
            }
        }

        $data1['option'] = $option;

        pdo_insert('xg_agent_customer', $data1);

        $id = pdo_insertid();

        $data2 = array(
            'uniacid' => $uniacid,
            'cid' => $id,
            'createtime' => $time,
            'gj_fs' => $_GPC['gjfs'],
            'gj_jb' => $_GPC['yxjb'],
            'gj_nr' => $_GPC['gjnr'],
            'userid' => $userid,
            'gj_xc' => strtotime($_GPC['gj_xc']),
            'gj_stuta' => $status['id'],
        );
        pdo_insert('xg_agent_genjinlog', $data2);

        $data3 = array(
            'uniacid' => $uniacid,
            'cid' => $id,
            'userid' => 0,
            'createtime' => $time,
            'fpid' => 'x:' . $userid
        );
        pdo_insert('xg_agent_fplog', $data3);

        //自动分配
        $rule = pdo_get('xg_agent_rule', array('uniacid' => $uniacid));
        if ($rule['is_auto'] == 1) {
            pdo_update('xg_agent_customer', array('cid' => $userid), array('id' => $id));
            $data3 = array(
                'uniacid' => $uniacid,
                'cid' => $id,
                'userid' => $userid,
                'createtime' => $time + 1,
                'fpid' => 0
            );
            pdo_insert('xg_agent_fplog', $data3);
            $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('user.opensource.cover'), 2);
            if ($this->user['openid'] != '') {
                sendCustomerFP($this->user['openid'], $_GPC['name'], $_GPC['tel'], '系统自动分配', $url);
            }
        }

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function updatecustomer()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        $data = array(
            'uniacid' => $uniacid,
            'name' => $_GPC['name'],
            'tel' => $_GPC['tel'],
            'sex' => $_GPC['sex'],
            'label' => $_GPC['label'],
            'mark' => $_GPC['mark']
        );
        $option = "";
        $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($radios as $k => $v) {
            if (!empty($_GPC['rad_' . $v['id']])) {
                $option .= explode(':', $_GPC['rad_' . $v['id']])[0] . ',';
            } elseif ($v['must'] == 1) {
                echo json_encode(array('code' => 0, 'message' => $v['name'] . '为必填项！'));
                die;
            }
        }
        $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($checks as $k => $v) {
            if (!empty($_GPC['che_' . $v['id']])) {
                foreach ($_GPC['che_' . $v['id']] as $v2) {
                    $option .= $v2 . ',';
                }
            } elseif ($v['must'] == 1) {
                echo json_encode(array('code' => 0, 'message' => $v['name'] . '为必填项！'));
                die;
            }
        }
        $selects = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=2 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($selects as $k => $v) {
            if (!empty($_GPC['sel_' . $v['id']])) {
                $option .= explode(':', $_GPC['sel_' . $v['id']])[0] . ',';
            } elseif ($v['must'] == 1) {
                echo json_encode(array('code' => 0, 'message' => $v['name'] . '为必填项！'));
                die;
            }
        }

        $data['option'] = $option;
        pdo_update('xg_agent_customer', $data, array('id' => $id));

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function execgenjin()
    {
        global $_W, $_GPC;

        if ($_GPC['gj_fs'] == '' || $_GPC['gj_jb'] == '' || $_GPC['gj_xc'] == '' || $_GPC['gj_stuta'] == '') {
            echo json_encode(array('code' => 0, 'message' => '信息不完整！'));
            die;
        }

        $id = $_GPC['id'];
        $userid = $this->sess_id;
        $uniacid = $_W['uniacid'];
        $time = time();
        $arrs = explode(':', $_GPC['gj_stuta']);
        $status = $arrs[0];
        $data1 = array(
            'updatetime' => $time,
            'status' => $status
        );
        $data2 = array(
            'uniacid' => $uniacid,
            'cid' => $id,
            'createtime' => $time,
            'gj_fs' => $_GPC['gj_fs'],
            'gj_jb' => $_GPC['gj_jb'],
            'gj_nr' => $_GPC['gj_nr'],
            'userid' => $userid,
            'gj_xc' => strtotime($_GPC['gj_xc']),
            'gj_stuta' => $status,
        );
        pdo_update('xg_agent_customer', $data1, array('id' => $id));
        pdo_insert('xg_agent_genjinlog', $data2);

        $customer = pdo_get('xg_agent_customer', array('id' => $id));
        $tarr = explode(':', $customer['tid']);
        if ($tarr[0] == 'j') {
            $member = pdo_get('xg_agent_member', array('id' => $tarr[1]));
            $jjrrule = pdo_get('xg_agent_jjrrule', array('uniacid' => $uniacid, 'lid' => $customer['lid'], 'sfid' => $member['sfid'], 'sid' => $customer['status']));
            if ($jjrrule != '') {
                //有此佣金积分规则且为此客户第一次此状态
                $tempcommis = pdo_get('xg_agent_commission', array('cid' => $id, 'status' => $customer['status']));
                if ($tempcommis == '') {

                    $comissdata = array(
                        'uniacid' => $uniacid,
                        'mid' => $tarr[1],
                        'lid' => $customer['lid'],
                        'cid' => $id,
                        'commis' => $jjrrule['commis'],
                        'credit' => $jjrrule['credit'],
                        'status' => $customer['status'],
                        'createtime' => $time,
                        'issh' => 0,
                        'isdk' => 0,
                        'issq' => 1
                    );
                    if ($jjrrule['commis'] == 0) {
                        $comissdata['issq'] == 0;
                    }
                    pdo_insert('xg_agent_commission', $comissdata);
                    $comid = pdo_insertid();
                    pdo_update('xg_agent_customer', array('commision' => ($customer['commision'] + $jjrrule['commis'])), array('id' => $id));
                    pdo_update('xg_agent_member', array('commission' => ($member['commission'] + $jjrrule['commis'])), array('id' => $tarr[1]));
                    if ($jjrrule['commis'] == 0) {
                        $rule = pdo_get('xg_agent_rule', array('uniacid' => $uniacid));
                        $comissapplicdata = array(
                            'uniacid' => $uniacid,
                            'ordernum' => 'TX' . date('Ymd') . time(),
                            'mid' => $tarr[1],
                            'time' => time(),
                            'yid' => $comid,
                            'status' => 0,
                            'txfs' => $rule['txfs']
                        );
                        pdo_update('xg_agent_commission', array('issq' => 0, 'issh' => 1, 'checktime' => time()), array('id' => $comid));
                        pdo_insert('xg_agent_commisapplic', $comissapplicdata);
                    }
                }
            }

            $pan = pdo_get('xg_agent_loupan', array('id' => $customer['lid']));
            $status = pdo_get('xg_agent_status', array('id' => $customer['status']));
            $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('qmjjr.opensource.cover'), 2);
            sendStatusChange($member['openid'], $customer['name'], $customer['tel'], $pan['title'], date('Y-m-d H:i:s', $customer['createtime']), $status['name'], date('Y-m-d H:i:s', time()), $_GPC['gj_nr'], $url);

        }

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function valid()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $userid = $this->sess_id;
        $uniacid = $_W['uniacid'];
        $time = time();
        $data = array(
            'uniacid' => $uniacid,
            'wxid' => $userid,
            'cid' => $id,
            'createtime' => $time,
            'content' => $_GPC['content']
        );
        pdo_update('xg_agent_customer', array('isvalid' => 0), array('id' => $id));
        pdo_insert('xg_agent_yuxiaolog', $data);

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function getgenjincategory()
    {
        global $_W, $_GPC;
        $userid = $this->sess_id;
        $uniacid = $_W['uniacid'];
        $time = time();
        $condition = " where uniacid=:uniacid and cid = :cid";
        $parmas = array(':uniacid' => $uniacid, ':cid' => $userid);

        $list = array();

        $conditiontrue = $condition . " and fentime >= " . ($time - 3 * 24 * 60 * 60);
        $count = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . $conditiontrue, $parmas);

        //type0新分配1逾期2逾期提醒
        $list[] = array('title' => "新分配客户" . $count . "人", 'type' => 0, 'status' => 0);

        $status = pdo_fetchall("select * from " . tablename('xg_agent_status') . " where uniacid=:uniacid and type = 0 order by displayorder", array(':uniacid' => $uniacid));
        foreach ($status as $v) {
            $conditiontrue = $condition . " and status =" . $v['id'] . " and updatetime < " . strtotime("-" . $v['baohuqi'] . " day");
            $count = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . $conditiontrue, $parmas);
            $list[] = array('title' => "逾期未" . $v['name'] . "客户" . $count . "人", 'type' => 1, 'status' => $v['id']);
            $conditiontrue = $condition . " and status =" . $v['id'] . " and updatetime > " . strtotime("-" . $v['baohuqi'] . " day") . " and updatetime < " . strtotime("-" . ($v['baohuqi'] - $v['tixing']) . " day");
            $count = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . $conditiontrue, $parmas);
            $list[] = array('title' => $v['tixing'] . "日内逾期" . $v['name'] . "客户" . $count . "人", 'type' => 2, 'status' => $v['id']);
        }

        echo json_encode(array('code' => 3, 'data' => $list));
    }

    public function getgenjinlist()
    {
        global $_W, $_GPC;
        $userid = $this->sess_id;
        $uniacid = $_W['uniacid'];
        $type = $_GPC['type'];
        $status = $_GPC['status'];
        $time = time();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 999;
        $condition = " where uniacid=:uniacid and cid = :cid";
        $parmas = array(':uniacid' => $uniacid, ':cid' => $userid);
        $tempstatus = pdo_get('xg_agent_status', array('id' => $status));

        if ($type == 0) {
            $conditiontrue = $condition . " and fentime >= " . ($time - 3 * 24 * 60 * 60);
        } elseif ($type == 1) {
            $conditiontrue = $condition . " and status =" . $status . " and updatetime < " . strtotime("-" . $tempstatus['baohuqi'] . " day");
        } elseif ($type == 2) {
            $conditiontrue = $condition . " and status =" . $status . " and updatetime > " . strtotime("-" . $tempstatus['baohuqi'] . " day") . " and updatetime < " . strtotime("-" . ($tempstatus['baohuqi'] - $tempstatus['tixing']) . " day");
        }
        $list = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $conditiontrue . " order by createtime desc limit " . (($pindex - 1) * $psize) . "," . $psize, $parmas);
        foreach ($list as &$v) {
            $log = pdo_fetch("select * from " . tablename('xg_agent_genjinlog') . " where uniacid=:uniacid and cid=:cid order by createtime desc", array(':uniacid' => $uniacid, ':cid' => $v['id']));
            if ($log == '') {
                $v['gj_jb'] = '';
            } else {
                $v['gj_jb'] = $log['gj_jb'];
                $v['gj_nr'] = $log['gj_nr'];
                $v['gj_time'] = date('Y-m-d H:i:s', $log['createtime']);
            }
            $v['createtime'] = date('Y-m-d H:i:s', $v['createtime']);

        }
        unset($v);

        echo json_encode(array('code' => 3, 'data' => $list));
    }

    public function getcuibancategory()
    {
        global $_W, $_GPC;
        $userid = $this->sess_id;
        $uniacid = $_W['uniacid'];
        $condition = " where uniacid=:uniacid and cid = :cid";
        $parmas = array(':uniacid' => $uniacid, ':cid' => $userid);

        $list = array();
        $status = pdo_fetchall("select * from " . tablename('xg_agent_status') . " where uniacid=:uniacid and type = 1 order by displayorder", array(':uniacid' => $uniacid));
        foreach ($status as $v) {
            $conditiontrue = $condition . " and status =" . $v['id'] . " and updatetime < " . strtotime("-" . $v['baohuqi'] . " day");
            $count = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . $conditiontrue, $parmas);
            $list[] = array('title' => "逾期未" . $v['name'] . "客户" . $count . "人", 'type' => 1, 'status' => $v['id']);
            $conditiontrue = $condition . " and status =" . $v['id'] . " and updatetime > " . strtotime("-" . $v['baohuqi'] . " day") . " and updatetime < " . strtotime("-" . ($v['baohuqi'] - $v['tixing']) . " day");
            $count = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . $conditiontrue, $parmas);
            $list[] = array('title' => $v['tixing'] . "日内逾期" . $v['name'] . "客户" . $count . "人", 'type' => 2, 'status' => $v['id']);
        }

        echo json_encode(array('code' => 3, 'data' => $list));
    }

    public function getcblog()
    {
        global $_W, $_GPC;
        $userid = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        $condition = " where uniacid=:uniacid and cid = :cid order by createtime desc";
        $parmas = array(':uniacid' => $uniacid, ':cid' => $userid);
        $list = pdo_fetch("select * from " . tablename('xg_agent_cblog') . $condition, $parmas);
        $list['createtime'] = date('Y-m-d H:i:s', $list['createtime']);
        $list['xiacitime'] = date('Y-m-d H:i:s', $list['xiacitime']);
        $fangyuan = pdo_get('xg_agent_fangyuan', array('cid' => $userid));
        if ($fangyuan != '') {
            $hu = pdo_get('xg_agent_fytype', array('id' => $fangyuan['huid']));
            $ceng = pdo_get('xg_agent_fytype', array('id' => $hu['did']));
            $danyuan = pdo_get('xg_agent_fytype', array('id' => $ceng['did']));
            $lou = pdo_get('xg_agent_fytype', array('id' => $danyuan['did']));
            $qu = pdo_get('xg_agent_fytype', array('id' => $lou['did']));
            $qi = pdo_get('xg_agent_fytype', array('id' => $qu['did']));
            $loupan = pdo_get('xg_agent_loupan', array('id' => $qi['lid']));
            $fname = $loupan['title'] . '-' . $qi['name'] . '-' . $qu['name'] . '-' . $lou['name'] . '-' . $danyuan['name'] . '-' . $ceng['name'] . '层' . $hu['name'] . '户';
        } else {
            $fname = '待分配中';
        }
        $customer = pdo_get('xg_agent_customer', array('id' => $userid));
        echo json_encode(array('code' => 3, 'data' => $list, 'fname' => $fname, 'tel' => $customer['tel']));
    }

    public function setcblog()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $data = array(
            'uniacid' => $uniacid,
            'cid' => $_GPC['id'],
            'createtime' => time(),
            'xiacitime' => strtotime($_GPC['xiacitime']),
            'content' => $_GPC['content'],
            'result' => $_GPC['result'],
        );
        pdo_insert('xg_agent_cblog', $data);
        pdo_update('xg_agent_customer', array('updatetime' => time()), array('id' => $_GPC['id']));

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function getpool()
    {
        global $_W;
        global $_GPC;
        $page = $_GPC['page'];
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
                        $pools[$k]['createtime'] = date('Y-m-d', $v['createtime']);
                    }
                }
            }
        }
        unset($v);
        if ($page != '') {
            $page = 1;
        }
        $pools = array_slice($pools, ($page - 1) * 8, 8);

        echo json_encode(array('code' => 3, 'data' => $pools, 'count' => count($pools)));
    }

    public function getgjfs()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $data = pdo_getall('xg_agent_gjfs', array('uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $data));
    }

    public function getyxjb()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $data = pdo_getall('xg_agent_yxjb', array('uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $data));
    }

    public function getgjnr()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $data = pdo_getall('xg_agent_gjnr', array('uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $data));
    }

    public function getloupan()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $data = pdo_fetchall("select * from " . tablename('xg_agent_loupan') . " where uniacid=:uniacid and isview=1", array(':uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $data));
    }

    public function getinfo(){
        echo json_encode(array('code' => 3, 'data' => $this->user));
    }

    public function updateinfo()
    {
        global  $_GPC;
        $mobile=intval($_GPC['mobile']);
        if($_GPC['realname']==''||!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile)){
            echo json_encode(array('code' => 0, 'message' => '格式不正确！'));die;
        }
        $id = $this->sess_id;
        $data = array(
            'realname' => $_GPC['realname'],
            'mobile' => $_GPC['mobile'],
            'content' => $_GPC['content'],
        );
        pdo_update('xg_agent_user',$data,array('id'=>$id));

        echo json_encode(array('code' => 2, 'message' => "修改成功！"));
    }

}