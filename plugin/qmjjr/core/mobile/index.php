<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Index_XgAgentPage extends MobilePage
{
    function __construct()
    {
        global $_W,$_GPC;
        parent::__construct();
        $columnname=pdo_fetchall("select COLUMN_NAME from information_schema.COLUMNS where table_name = ". str_replace("`","'",tablename('xg_agent_member')));
        $strcolumn='';
        foreach($columnname as &$v){
            $strcolumn.=$v['COLUMN_NAME'].',';
        }
        unset($v);
        $strcolumn=str_replace("accounts,","",$strcolumn);
        $strcolumn=str_replace("password,","",$strcolumn);
        $strcolumn=substr($strcolumn,0,-1);
        $this->user = pdo_fetch("select $strcolumn from ".tablename('xg_agent_member')." where uniacid=:uniacid and token=:token", array(':uniacid'=>$_W['uniacid'],':token' => $_GPC['token']));
        $this->sess_id=$this->user['id'];

//        $this->sess_id=1;
//        $this->user = pdo_get('xg_agent_member', array('id' => $this->sess_id));


        if (empty($this->user)||$_GPC['token']=='') {
            echo json_encode(array('code' => 0, 'message' => '您还未登录'));
            die;
        }
        if ($this->user['status'] == 0) {
            echo json_encode(array('code' => 0, 'message' => '帐号已禁用'));
            die;
        }
        if ($this->user['review'] == 0) {
            echo json_encode(array('code' => 0, 'message' => '帐号未审核'));
            die;
        }
    }


    public function main()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $status = $_GPC['status'];
        $sort = $_GPC['sort'];
        $condition = ' WHERE uniacid = :uniacid and tid = :tid ';
        if ($status != '') {
            if ($status == 'valid') {
                $condition .= " and isvalid = 0";
            }elseif ($status == 'review'){
                $condition .= " and is_review = 0";
            } else {
                $condition .= " and status = " . $status;
            }
        }

        if ($sort != '') {
            $condition .= " and name like '%" . $sort . "%'";
        }

        $params = array(':uniacid' => $uniacid, ':tid' => "j:" . $this->sess_id);
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $list = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
        foreach ($list as &$v) {
            $vstatus = pdo_get('xg_agent_status', array('id' => $v['status']));
            if ($v['updatetime'] + $vstatus['baohuqi'] * 86400 > time()) {
                $v['yuqi'] = "剩余" . sprintf("%.1f", (($v['updatetime'] + $vstatus['baohuqi'] * 86400 - time()) / 86400)) . "天";
            } else {
                $v['yuqi'] = "逾期" . sprintf("%.1f", ((time() - $v['updatetime'] - $vstatus['baohuqi'] * 86400) / 86400)) . "天";
            }
            $v['status'] = $vstatus['name'];
        }
        unset($v);

        echo json_encode(array('code' => 3, 'data' => $list));
    }

    public function getoption()
    {
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $id=$_GPC['id'];
        if($id!=''){
            $detail=pdo_get('xg_agent_customer',array('id'=>$id));
        }
//        单选
        $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($radios as $k => $v) {
            $radio = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            if($v['must']==1){
                $radios[$k]['must']=true;
            }else{
                $radios[$k]['must']=false;
            }
            $radios[$k]['opt'] = $radio;
            foreach($radio as $v2){
                if(isset($detail)&&strstr($detail['option'],$v2['id'].',')){
                    $radios[$k]['value']=$v2['id'].':'.$v2['name'];
                }
            }
        }

//        多选
        $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($checks as $k => $v) {
            $check = pdo_fetchall("select * from " . tablename('xg_agent_option') . " where uniacid=:uniacid and status=0 and level=1 and oid=:oid order by displayorder desc", array(':uniacid' => $uniacid, ':oid' => $v['id']));
            if($v['must']==1){
                $checks[$k]['must']=true;
            }else{
                $checks[$k]['must']=false;
            }
            foreach($check as &$v2){
                if(isset($detail)&&strstr($detail['option'],$v2['id'].',')){
                    $v2['checked']=1;
                }else{
                    $v2['checked']=0;
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
            if($v['must']==1){
                $selects[$k]['must']=true;
            }else{
                $selects[$k]['must']=false;
            }
            foreach($select as $v2){
                if(isset($detail)&&strstr($detail['option'],$v2['id'].',')){
                    $selects[$k]['value']=$v2['id'].':'.$v2['name'];
                }
            }
        }

        $data = array('radios' => $radios, 'checks'=>$checks, 'selects' => $selects);

        echo json_encode(array('code' => 3, 'data' => $data));
    }

    public function getcustomerdetail()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        $data = pdo_get('xg_agent_customer', array('id' => $id));
        $status = pdo_get('xg_agent_status', array('id' => $data['status']));
        $data['status_name'] = $status['name'];
        $user = pdo_get('xg_agent_user', array('id' => $data['cid']));
        if($user==''){
            $data['user_headurl'] = "../static/images/userhead.png";
            $data['user_name'] = '未分配';
        }else{
            $data['user_headurl'] = $user['headimgurl'];
            $data['user_name'] = $user['realname'];
        }
        $data['user_tel'] = $user['mobile'];
        $loupan = pdo_get('xg_agent_loupan', array('id' => $data['lid']));
        $data['louname'] = $loupan['title'];
        if ($data['sex'] == 1) {
            $data['headurl'] = "../qmjjr/src/img/man.png";
        } else {
            $data['headurl'] = "../qmjjr/src/img/woman.png";
        }
        $log = pdo_fetchall("select * from " . tablename('xg_agent_fplog') . " where uniacid=:uniacid and cid = :cid order by createtime desc ", array(':uniacid' => $uniacid, ':cid' => $id));
        foreach ($log as &$v) {
            if ($v['userid'] == 0) {
                $arr = explode(':', $v['fpid']);
                if ($arr[0] == 'j') {
                    $tempjjr = pdo_get('xg_agent_member', array('id' => $arr[1]));
                    $v['text'] = "由经纪人：" . $tempjjr['realname'] . "新增。";
                } else {
                    $tempuser = pdo_get('xg_agent_user', array('id' => $arr[1]));
                    $v['text'] = "由置业顾问：" . $tempuser['realname'] . "新增。";
                }
            } else {
                $tempuser = pdo_get('xg_agent_user', array('id' => $v['userid']));
                if ($v['fpid'] == 0) {
                    $v['text'] = "由后台分配给置业顾问：" . $tempuser['realname'];
                } else {
                    $tempjl = pdo_get('xg_agent_user', array('id' => $v['fpid']));
                    $v['text'] = "由" . $tempjl['realname'] . "分配给置业顾问：" . $tempuser['realname'];
                }
            }
            $v['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
        }
        unset($v);

        echo json_encode(array('code' => 3, 'data' => $data, 'log' => $log));
    }

    public function getloupandetail()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        $data = pdo_get('xg_agent_loupan', array('id' => $id));
        $sence = pdo_fetchall("select * from " . tablename('xg_agent_photo') . " where lpid=:lpid order by displayorder desc", array(':lpid' => $id));
        $houselayer = pdo_fetchall("select * from " . tablename('xg_agent_houselayer') . " where loupanid=:lpid and status=0 order by displayorder desc", array(':lpid' => $id));
        foreach ($sence as &$v) {
            $v['img'] = tomedia($v['attachment']);
        }
        unset($v);
        foreach ($houselayer as &$v) {
            $v['l_url'] = tomedia($v['l_url']);
            $v['l_biaoqian'] = explode(' ', $v['l_biaoqian']);
        }
        unset($v);
        $temprecnum = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where uniacid=:uniacid and lid = :lid", array(':uniacid' => $uniacid, ':lid' => $id));
//        $tempsucnum=pdo_fetchcolumn("select count(*) from ".tablename('')." where ");
        $data['recnum'] += $temprecnum;
//        $data['sucnum']+=
//        $data['l_guize']=$data['l_guize'];
        $data['starttime'] = date('Y-m-d', $data['starttime']);
        $data['endtime'] = date('Y-m-d', $data['endtime']);
        if($data['is_farme']==1){
            $data['is_farme']=true;
        }else{
            $data['is_farme']=false;
        }
        if($data['l_xcyh']==1){
            $data['l_xcyh']=true;
        }else{
            $data['l_xcyh']=false;
        }

        echo json_encode(array('code' => 3, 'data' => $data, 'sence' => $sence, 'houselayer' => $houselayer));
    }

    public function gethouselayer(){
        global $_GPC;
        $id=$_GPC['id'];
        $houselayer = pdo_get('xg_agent_houselayer', array('id' => $id));
        $houselayer['l_url'] = tomedia($houselayer['l_url']);
        $houselayer['l_biaoqian'] = explode(' ', $houselayer['l_biaoqian']);

        echo json_encode(array('code' => 3, 'data' => $houselayer));
    }

    public function getloupanoption()
    {
        global $_W;
        $list = pdo_fetchall("select id,title from " . tablename('xg_agent_loupan') . " where uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));

        unset($v);

        echo json_encode(array('code' => 3, 'data' => $list));
    }

    public function getinfo()
    {
        global $_W;
        $info = $this->user;
        $uniacid = $_W['uniacid'];
        $allnum = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where tid = 'j:" . $this->sess_id . "'");
        $info['allnum'] = $allnum;
        $identity = pdo_get('xg_agent_identity', array('id' => $info['sfid']));
        $info['sfname'] = $identity['name'];
        $info['ketixian'] = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . " where issq= 1 and mid =" . $this->sess_id . " and uniacid =" . $uniacid);
        $info['headurl'] = tomedia($info['headurl']);
        if ($info['allnum'] == '') {
            $info['allnum'] = 0;
        }
        if ($info['ketixian'] == '') {
            $info['ketixian'] = 0;
        }
        if ($info['idcardtime1'] != '') {
            $info['idcardtime1'] = date('Y-m-d H:i:s', $info['idcardtime1']);
        }
        if ($info['idcardtime2'] != '') {
            $info['idcardtime2'] = date('Y-m-d H:i:s', $info['idcardtime2']);
        }
        $rule=pdo_get('xg_agent_rule',array('uniacid'=>$uniacid));
        $rule['sha_img']=tomedia($rule['sha_img']);
        $jrule=pdo_get('xg_agent_qmjjr_rule',array('uniacid'=>$uniacid));

        if($info['uid']!=''){
            $mcme=pdo_get('mc_members',array('uid'=>$info['uid']));
            $info['credit1']=$mcme['credit1'];
        }

        if($info['headurl']==''){
            $info['headurl']="../static/images/userhead.png";
        }

        echo json_encode(array('code' => 3, 'data' => $info,'rule'=>$rule,'jrule'=>$jrule));die;
    }

    public function getfournum()
    {
        global $_W;
        $num1=0;
        $num2=0;
        $num3=0;
        $num4=0;
//        $allnum = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where tid = 'j:" . $this->sess_id . "'");
        $uniacid = $_W['uniacid'];
        $status = pdo_fetchall("select id from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder", array(':uniacid' => $uniacid));
        foreach ($status as $k => $v) {
            if ($k == 0) {
                $num1 = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where status=" . $v['id'] . " and tid = 'j:" . $this->sess_id . "'");
            } elseif ($k == 1) {
                $num2 = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where status=" . $v['id'] . " and tid = 'j:" . $this->sess_id . "'");
            } elseif ($k == 2) {
                $num3 = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where status=" . $v['id'] . " and tid = 'j:" . $this->sess_id . "'");
            }elseif ($k == 4) {
                $num4 = pdo_fetchcolumn("select count(*) from " . tablename('xg_agent_customer') . " where status=" . $v['id'] . " and tid = 'j:" . $this->sess_id . "'");
            }
        }

        echo json_encode(array(
            'code' => 3,
            'data' => array(
                'allnum' => $num1,
                'num1' => $num2,
                'num2' => $num3,
                'num3' => $num4,
            )
        ));
    }

    public function getstatus()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $status = pdo_fetchall("select id,name,baohuqi from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder", array(':uniacid' => $uniacid));

        echo json_encode(array('code' => 3, 'data' => $status));
    }

    public function insertcustomer()
    {
        global $_W, $_GPC;
        if ($_GPC['name'] == '' || $_GPC['tel'] == '' || $_GPC['sex'] == '' || $_GPC['lid'] == '' || $_GPC['mark'] == '') {
            echo json_encode(array('status' => 0, 'message' => '填写信息不完整！'));
            die;
        }
        $tempcustomer = pdo_get('xg_agent_customer', array('tel' => $_GPC['tel']));
        if ($tempcustomer != '') {
            echo json_encode(array('status' => 0, 'message' => '此客户电话号码已存在！'));
            die;
        }
        $uniacid = $_W['uniacid'];
        $time = time();
        $arr = explode(':', $_GPC['lid']);
        $_GPC['lid'] = $arr[0];
        $status = pdo_fetch("select id from " . tablename('xg_agent_status') . " where uniacid=:uniacid order by displayorder", array(':uniacid' => $uniacid));
        $data = array(
            'uniacid' => $uniacid,
            'tid' => "j:" . $this->sess_id,
            'name' => $_GPC['name'],
            'tel' => $_GPC['tel'],
            'sex' => $_GPC['sex'],
            'lid' => $_GPC['lid'],
            'mark' => $_GPC['mark'],
            'status' => $status['id'],
            'createtime' => $time,
            'updatetime' => $time,
            'isvalid' => 1,
            'is_gz' => 0,
        );
        $option = "";
        $radios = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=0 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($radios as $k => $v) {
            if (!empty($_GPC['rad_' . $v['id']])) {
                $option .= explode(':',$_GPC['rad_' . $v['id']])[0] . ',';
            }elseif($v['must']==1){
                echo json_encode(array('status' => 0, 'message' => $v['name'].'为必填项！'));die;
            }
        }
        $checks = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=1 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($checks as $k => $v) {
            if (!empty($_GPC['che_' . $v['id']])) {
                foreach ($_GPC['che_' . $v['id']] as $v2) {
                    $option .= $v2 . ',';
                }
            }elseif($v['must']==1){
                echo json_encode(array('status' => 0, 'message' => $v['name'].'为必填项！'));die;
            }
        }
        $selects = pdo_fetchall("select * from " . tablename('xg_agent_option') . "where uniacid=:uniacid and type=2 and status=0 and level=0 order by displayorder desc", array(':uniacid' => $uniacid));
        foreach ($selects as $k => $v) {
            if (!empty($_GPC['sel_' . $v['id']])) {
                $option .= explode(':',$_GPC['sel_' . $v['id']])[0] . ',';
            }elseif($v['must']==1){
                echo json_encode(array('status' => 0, 'message' => $v['name'].'为必填项！'));die;
            }
        }

        $data['option'] = $option;
        pdo_insert('xg_agent_customer', $data);
        $cid = pdo_insertid();

        $data3 = array(
            'uniacid' => $uniacid,
            'cid' => $cid,
            'userid' => 0,
            'createtime' => $time,
            'fpid' => 'j:' . $this->sess_id
        );
        pdo_insert('xg_agent_fplog', $data3);

        $jjrrule = pdo_get('xg_agent_jjrrule', array('uniacid' => $uniacid, 'lid' => $_GPC['lid'], 'sfid' => $this->user['sfid'], 'sid' => $status['id']));
        if ($jjrrule != '') {
            //有此佣金积分规则且为此客户第一次此状态
            $tempcommis = pdo_get('xg_agent_commission', array('cid' => $cid, 'status' => $status['id']));
            if ($tempcommis == '') {
                $comissdata = array(
                    'uniacid' => $uniacid,
                    'mid' => $this->sess_id,
                    'lid' => $_GPC['lid'],
                    'cid' => $cid,
                    'commis' => $jjrrule['commis'],
                    'credit' => $jjrrule['credit'],
                    'status' => $status['id'],
                    'createtime' => $time,
                    'issh' => 0,
                    'isdk' => 0,
                    'issq' => 1
                );
                if($jjrrule['commis']==0){
                    $comissdata['issq']==0;
                }
                pdo_insert('xg_agent_commission', $comissdata);
                $comid=pdo_insertid();
                pdo_update('xg_agent_customer', array('commision' => $jjrrule['commis']), array('id' => $cid));
                pdo_update('xg_agent_member', array('commission' => ($this->user['commission'] + $jjrrule['commis'])), array('id' => $this->sess_id));
                if($jjrrule['commis']==0){
                    $rule = pdo_get('xg_agent_rule', array('uniacid' => $uniacid));
                    $comissapplicdata=array(
                        'uniacid' => $uniacid,
                        'ordernum' => 'TX' . date('Ymd') . time(),
                        'mid' => $this->sess_id,
                        'time' => time(),
                        'yid' => $comid,
                        'status' => 0,
                        'txfs' => $rule['txfs']
                    );
                    pdo_update('xg_agent_commission',array('issq'=>0,'issh'=>1,'checktime' => time()),array('id'=>$comid));
                    pdo_insert('xg_agent_commisapplic', $comissapplicdata);
                }

            }
        }

        //自动分配
        $rule = pdo_get('xg_agent_rule', array('uniacid' => $uniacid));
        if ($rule['is_auto'] == 1) {

            $autouser = pdo_fetch("select id from" . tablename('xg_agent_user') . "where uniacid = :uniacid and id > " . $rule['autoid'], array(':uniacid' => $uniacid));

            if ($autouser == '') {
                $autouser = pdo_fetch("select min(id) as id from" . tablename('xg_agent_user') . "where uniacid = :uniacid ", array(':uniacid' => $uniacid));
            }

            pdo_update('xg_agent_customer', array('cid' => $autouser['id']), array('id' => $cid));
            $data3 = array(
                'uniacid' => $uniacid,
                'cid' => $cid,
                'userid' => $autouser['id'],
                'createtime' => $time + 1,
                'fpid' => 0
            );
            pdo_insert('xg_agent_fplog', $data3);

            pdo_update('xg_agent_rule', array('autoid' => $autouser['id']), array('uniacid' => $uniacid));
            $url = $_W['siteroot'] . 'app/' . substr(mobileUrl('user.opensource.cover'), 2);
            $user = pdo_get('xg_agent_user', array('id' => $autouser['id']));
            $cusromer = pdo_get('xg_agent_customer', array('id' => $cid));
            if($user['openid']!='') {
                sendCustomerFP($user['openid'], $cusromer['name'], $cusromer['tel'], '系统自动分配', $url);
            }
        }

        echo json_encode(array('status' => 1, 'message' => '推荐成功'));die;
    }

    public function infopost()
    {
        global $_GPC;
        $mobile=intval($_GPC['mobile']);
        if($_GPC['realname']==''||!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile)){
            echo json_encode(array('code' => 0, 'message' => '格式不正确！'));die;
        }
        $data = array(
            'realname' => $_GPC['realname'],
            'mobile' => $mobile
        );
        pdo_update('xg_agent_member', $data, array('id' => $this->sess_id));

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function idcardpost()
    {
        global $_GPC;
        $type = $_GPC['type'];
        $imgurl = $_GPC['imgurl'];
        if ($type == 1) {
            $data = array(
                'idcardurl1' => $imgurl,
                'idcardtime1' => time()
            );
        } elseif ($type == 2) {
            $data = array(
                'idcardurl2' => $imgurl,
                'idcardtime2' => time()
            );
        }
        pdo_update('xg_agent_member', $data, array('id' => $this->sess_id));

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function getbankcardlist()
    {
        $cards = pdo_getall('xg_agent_bankcard', array('mid' => $this->sess_id));

        foreach ($cards as &$v) {
            if ($v['bankname'] == '农业银行') {
                $v['color'] = 'linear-gradient(to right, #e65a65, #e74f7c)';
            } elseif ($v['bankname'] == '建设银行') {
                $v['color'] = 'linear-gradient(to right, #e65a65, #e74f7c)';
            }
            if ($v['isdef']) {
                $v['isdef'] = true;
            } else {
                $v['isdef'] = false;
            }
        }
        unset($v);
        echo json_encode(array('code' => 3, 'data' => $cards));
    }

    public function bankcardpost()
    {
        global $_GPC;
        $id = $_GPC['id'];

        $data = array(
            'mid' => $this->sess_id,
            'name' => $_GPC['name'],
            'num' => $_GPC['num'],
            'bankname' => $_GPC['bankname'],
            'zhihang' => $_GPC['zhihang'],
            'city' => $_GPC['city'],
            'imgurl' => $_GPC['imgurl'],
            'isdef' => $_GPC['isdef']
        );
        if ($_GPC['isdef'] == 1) {
            pdo_update('xg_agent_bankcard', array('isdef' => 0), array('mid' => $this->sess_id));
        }
        if ($id != '') {
            pdo_update('xg_agent_bankcard', $data, array('id' => $id));
        } else {
            pdo_insert('xg_agent_bankcard', $data);
        }

        echo 1;
    }

    public function changedef()
    {
        global $_GPC;
        $id = $_GPC['id'];
        pdo_update('xg_agent_bankcard', array('isdef' => 0), array('mid' => $this->sess_id));
        pdo_update('xg_agent_bankcard', array('isdef' => 1), array('id' => $id));
        pdo_update('xg_agent_member', array('bcid' => $id), array('id' => $this->sess_id));

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function delbankcard()
    {
        global $_GPC;
        $id = $_GPC['id'];
        $card = pdo_get('xg_agent_bankcard', array('id' => $id));
        if ($card['isdef'] == 1) {
            echo json_encode(array('code' => 4, 'status' => 0));
            die;
        }
        pdo_delete('xg_agent_bankcard', array('id' => $id));

        echo json_encode(array('code' => 4, 'status' => 1));
        die;
    }

    public function complain()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $complain = array(
            'uniacid' => $uniacid,
            'mid' => $this->sess_id,
            'realname' => $this->user['realname'],
            'mobile' => $_GPC['mobile'],
            'complain' => trim($_GPC['complain']),
            'createtime' => time()
        );

        pdo_insert('xg_agent_complain', $complain);

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function getquestion()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $questions = pdo_fetchall("select * from " . tablename('xg_agent_question') . " where uniacid = " . $uniacid . " and status = 1 order by displayorder desc");
        $jjrrule=pdo_get('xg_agent_qmjjr_rule',array('uniacid'=>$uniacid));

        echo json_encode(array('code' => 3, 'data' => $questions,'routename'=>$jjrrule['defined3']));
    }

    public function getxuzhi()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $rule = pdo_fetch("select * from " . tablename('xg_agent_rule') . " where uniacid = " . $uniacid);
        $data['rule']=htmlspecialchars_decode($rule['rule']);
        $jjrrule=pdo_get('xg_agent_qmjjr_rule',array('uniacid'=>$uniacid));
        $data['defined4']=$jjrrule['defined4'];

        echo json_encode(array('code' => 3, 'data' => $data ));
    }

    public function getcommislist()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $type = $_GPC['type'];
        $kind=$_GPC['kind'];
        $condition = ' WHERE uniacid = :uniacid and mid = ' . $this->sess_id;
        $params = array(':uniacid' => $uniacid);
        $pindex = max(1, intval($_GPC['page']));
        $psize = 999999;

        if ($type == 1) {
            $condition .= " and issh= 0";
        } elseif ($type == 2) {
            $condition .= " and issh= 1 and isdk=0";
        } elseif ($type == 3) {
            $condition .= " and issh= 1 and isdk=1";
        }

        if($kind==1){
            $condition .=" and credit !=0";
        }else{
            $condition .=" and commis !=0";
        }

        $list = pdo_fetchall("select * from " . tablename('xg_agent_commission') . $condition . " order by createtime desc limit " . (($pindex - 1) * $psize) . "," . $psize, $params);

        foreach ($list as &$v) {

            if($v['credit']>0){
                $v['credit']='+'.$v['credit'];
            }

            if($v['cid']==0){
                $v['text']=$v['remark'];
            }elseif($v['cid']==-1){
                $v['text']='邀请奖励';
            }elseif($v['cid']==-2){
                $v['text']='注册奖励';
            }else {
                $templ = pdo_get('xg_agent_loupan', array('id' => $v['lid']));
                $tempc = pdo_get('xg_agent_customer', array('id' => $v['cid']));
                $temps = pdo_get('xg_agent_status', array('id' => $v['status']));
                $v['text'] = $templ['title'] . '-' . $tempc['name'] . '-' . $temps['name'];
            }

            $v['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
            if ($v['issq'] == 1) {
                $v['state'] = "可申请";
            } elseif ($v['issh'] == 0) {
                $v['state'] = "待审核";
            } elseif ($v['issh'] == 1 && $v['isdk'] == 0) {
                $v['state'] = "待付款";
            } elseif ($v['issh'] == 1 && $v['isdk'] == 1) {
                $v['state'] = "已完成";
            }

        }
        unset($v);

        if($kind==1&&$type!=1){
            $leiji = pdo_fetchcolumn("select sum(credit) from " . tablename('xg_agent_commission') . $condition, $params);
            $openid=$this->user['openid'];
            $credit = intval(m('member')->getCredit($openid, 'credit1'));
            $condition = ' and log.openid=:openid and log.uniacid = :uniacid';
            $params = array(':uniacid' => $_W['uniacid'], ':openid' => $openid);
            $sql = 'SELECT COUNT(*) FROM ' . tablename('xg_agent_creditshop_log') . ' log where 1 ' . $condition;
            $total = pdo_fetchcolumn($sql, $params);
            $list2 = array();
            if (!(empty($total)))
            {
                $sql = 'SELECT log.id,log.goodsid,g.title,g.thumb,g.credit,g.type,g.money,log.createtime, log.status, g.thumb FROM ' . tablename('xg_agent_creditshop_log') . ' log ' . ' left join ' . tablename('xg_agent_creditshop_goods') . ' g on log.goodsid = g.id ' . ' where 1 ' . $condition . ' ORDER BY log.createtime DESC ';
                $list2 = pdo_fetchall($sql, $params);
                $list2 = set_medias($list2, 'thumb');
                foreach ($list2 as &$row )
                {
                    if ((0 < $row['credit']) & (0 < $row['money']))
                    {
                        $row['acttype'] = 0;
                    }
                    else if (0 < $row['credit'])
                    {
                        $row['acttype'] = 1;
                    }
                    else if (0 < $row['money'])
                    {
                        $row['acttype'] = 2;
                    }
                    $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
                    $row['text']='积分商城消费';
                    $leiji-=$row['credit'];
                    $row['commis']=0;
                    $row['credit']=-$row['credit'];
                    $row['state']='已完成';
                }
                unset($row);
            }

            $condition = ' and openid=:openid and uniacid = :uniacid ';
            $params = array(':uniacid' => $_W['uniacid'], ':openid' => $openid);
            $sql = 'SELECT COUNT(*) FROM ' . tablename('xg_agent_sign_records') . ' log where 1 ' . $condition;
            $total = pdo_fetchcolumn($sql, $params);
            $list3 = array();
            if (!(empty($total)))
            {
                $sql = 'SELECT * FROM ' . tablename('xg_agent_sign_records') . ' where 1 ' . $condition . ' ORDER BY `time` DESC ';
                $list3 = pdo_fetchall($sql, $params);
                if (!(empty($list3)))
                {
                    foreach ($list3 as &$item )
                    {
                        $item['createtime'] = date('Y-m-d H:i:s', $item['time']);

                        $item['text']=$item['log'];
                        $leiji+=$item['credit'];
                        $item['commis']=0;
                        $item['credit']='+'.$item['credit'];
                        if($item['type']==0){
                            $item['state']='日常签到';
                        }elseif($item['type']==1){
                            $item['state']='连续签到奖励';
                        }elseif($item['type']==2){
                            $item['state']='总签到奖励';
                        }

                    }
                    unset($item);
                }
            }

            $list=array_merge($list,$list2,$list3);
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'createtime',       //排序字段
            );
            $arrSort = array();
            foreach($list AS $uniqid => $row){
                foreach($row AS $key=>$value){
                    $arrSort[$key][$uniqid] = $value;
                }
            }
            if($sort['direction']){
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $list);
            }
        }else {
            $leiji = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . $condition, $params);
        }
        if ($leiji == '') {
            $leiji = 0;
        }

        echo json_encode(array('code' => 3, 'data' => $list, 'leiji' => $leiji));
    }

    public function gettixianinfo()
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $leiji = $this->user['commission'];
        $dakuan = $this->user['credit2'];
        $ketixian = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . " where issq= 1 and mid =" . $this->sess_id . " and uniacid =" . $uniacid);
        $shenqing = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . " where issq= 0 and mid =" . $this->sess_id . " and uniacid =" . $uniacid);
        $daidakuan = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . " where issh= 1 and isdk=0 and mid =" . $this->sess_id . " and uniacid =" . $uniacid);
        $wuxiao = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . " where issh= 2 and mid =" . $this->sess_id . " and uniacid =" . $uniacid);
        $credit = pdo_fetchcolumn("select sum(credit) from " . tablename('xg_agent_commission') . " where issq= 1 and mid =" . $this->sess_id . " and uniacid =" . $uniacid);

        echo json_encode(array('code' => 3, 'data' => array(
            'num1' => $leiji ? $leiji : 0,
            'num2' => $ketixian ? $ketixian : 0,
            'num3' => $shenqing ? $shenqing : 0,
            'num4' => $daidakuan ? $daidakuan : 0,
            'num5' => $wuxiao ? $wuxiao : 0,
            'num6' => $dakuan ? $dakuan : 0,
            'credit' => $credit ? $credit : 0,
        )));
    }

    public function gettixianlist()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $status = $_GPC['status'];
        $condition = ' WHERE uniacid = :uniacid and mid = ' . $this->sess_id;
        $params = array(':uniacid' => $uniacid);
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $leiji = 0;

        if ($status != '' && $status != -1) {
            $condition .= " and status= " . $status;
        }

        $list = pdo_fetchall("select * from " . tablename('xg_agent_commisapplic') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
        foreach ($list as &$v) {
            $conditionc = " where id in(" . $v['yid'] . ")";
            if ($status == 2) {
                $conditionc .= " and issh = 1";
            }
            $tempnum = pdo_fetchcolumn("select sum(commis) from " . tablename('xg_agent_commission') . $conditionc, $params);
            $tempnum2 = pdo_fetchcolumn("select sum(credit) from " . tablename('xg_agent_commission') . $conditionc, $params);
            $leiji += $tempnum;
            $v['text'] = $v['ordernum'];
            $v['commis'] = $tempnum;
            $v['credit'] = $tempnum2;
            $v['createtime'] = date('Y-m-d H:i:s', $v['time']);
            if ($v['status'] == 0) {
                $v['state'] = "待审核";
            } elseif ($v['status'] == 1) {
                $v['state'] = "待打款";
            } elseif ($v['status'] == 2) {
                $v['state'] = "已打款";
            } elseif ($v['status'] == 3) {
                $v['state'] = "无效";
            }
        }
        unset($v);

        echo json_encode(array('code' => 3, 'data' => $list, 'leiji' => $leiji));
    }

    public function tixianpost()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $commis_ks = pdo_getall('xg_agent_commission', array('issq' => 1, 'mid' => $this->sess_id));
        $rule = pdo_get('xg_agent_rule', array('uniacid' => $uniacid));
        $str = '';
        for ($i = 0; $i < count($commis_ks); $i++) {
            $str .= $commis_ks[$i]['id'] . ',';
            pdo_update('xg_agent_commission', array('issq' => 0), array('id' => $commis_ks[$i]['id']));
        }
        $str = substr($str, 0, -1);

        $data = array(
            'uniacid' => $uniacid,
            'ordernum' => 'TX' . date('Ymd') . time(),
            'mid' => $this->sess_id,
            'time' => time(),
            'yid' => $str,
            'status' => 0,
            'txfs' => $rule['txfs']
        );
        pdo_insert('xg_agent_commisapplic', $data);

        echo json_encode(array('code' => 4, 'status' => 1));
    }

    public function uploadimg()
    {
        $file = $_FILES['fileimg'];
        $name = time() . dechex(rand(0, 10000)) . ".jpg";
        $trstr = "../attachment/images/xg_agent/" . $name;
        move_uploaded_file($file['tmp_name'], $trstr);
        $str = tomedia("images/xg_agent/" . $name);
        echo $str;
    }


}