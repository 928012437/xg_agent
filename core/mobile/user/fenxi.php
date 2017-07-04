<?php



if (!defined('IN_IA')) {

    exit('Access Denied');

}



class Fenxi_XgAgentPage extends MobilePage

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

        $this->sess_id=7;

        $this->user = pdo_get('xg_agent_user', array('id' => $this->sess_id));

//        if($user==''||$user['flag']==0){

//            echo json_encode(array('code' => 0, 'message' => '无执行权限'));

//        }

    }



    public function main()

    {

        global $_W, $_GPC;

        $uniacid = $_W['uniacid'];

        $status = $_GPC['status'];

        $tuandui = $_GPC['tuandui'];

        $type = $_GPC['type'];

        $time = time();

        $y = date("Y");

        $m = date("m");

        $d = date("d");

        $todayTime = mktime(0, 0, 0, $m, $d, $y);

        $condition = " where uniacid=:uniacid";

        $params = array(':uniacid' => $uniacid);

        if ($status != '') {

            $condition .= " and status = " . $status;

        }

        if ($tuandui != '') {

            $tempuser = pdo_getall('xg_agent_user', array('tid' => $tuandui, 'flag' => 0, 'uniacid' => $uniacid));

            $cids = "";

            foreach ($tempuser as $v) {

                $cids .= $v['id'] . ",";

            }

            if ($cids != '') {

                $cids = substr($cids, 0, -1);

            } else {

                $cids = "-1";

            }

            $condition .= " and cid in(" . $cids . ")";

        }



        $min = pdo_fetch("select min(updatetime) from " . tablename('xg_agent_customer') . $condition, $params);

        $min = $min['min(updatetime)'];

        if ($min == '') {

            $min = $time;

        }

        $list = array();



        if ($type == 1) {

            //日

            $interval = "1 day";

            $thiscycle = $todayTime;

            if ($min < ($time - 365 * 24 * 60 * 60)) {

                $min = $time - 365 * 24 * 60 * 60;

            }

            $cyclestr = "本日";

        } elseif ($type == 2) {

            //周

            $interval = "1 week";

            $weeknum = date('w');

            if ($weeknum == 0) {

                $thiscycle = strtotime("-6 day", $todayTime);

            } else {

                $thiscycle = strtotime("-" . ($weeknum - 1) . " day", $todayTime);

            }

            if ($min < ($time - 5 * 365 * 24 * 60 * 60)) {

                $min = $time - 5 * 365 * 24 * 60 * 60;

            }

            $cyclestr = "本周";

        } elseif ($type == 3) {

            //月

            $interval = "1 month";

            $thiscycle = mktime(0, 0, 0, $m, 1, $y);

            if ($min < ($time - 10 * 365 * 24 * 60 * 60)) {

                $min = $time - 10 * 365 * 24 * 60 * 60;

            }

            $cyclestr = "本月";

        } elseif ($type == 4) {

            //季度

            $interval = "3 month";

            $nowmonth = mktime(0, 0, 0, $m, 1, $y);

            if ($m > 0 && $m < 4) {

                $thiscycle = strtotime("- " . ($m - 1) . " month", $nowmonth);

            }

            if ($m > 3 && $m < 7) {

                $thiscycle = strtotime("- " . ($m - 4) . " month", $nowmonth);

            }

            if ($m > 6 && $m < 10) {

                $thiscycle = strtotime("- " . ($m - 7) . " month", $nowmonth);

            }

            if ($m > 9 && $m < 13) {

                $thiscycle = strtotime("- " . ($m - 10) . " month", $nowmonth);

            }

            if ($min < ($time - 30 * 365 * 24 * 60 * 60)) {

                $min = $time - 30 * 365 * 24 * 60 * 60;

            }

            $cyclestr = "本季度";

        } elseif ($type == 5) {

            //年

            $interval = "1 year";

            $thiscycle = mktime(0, 0, 0, 1, 1, $y);

            if ($min < ($time - 50 * 365 * 24 * 60 * 60)) {

                $min = $time - 50 * 365 * 24 * 60 * 60;

            }

            $cyclestr = "本年";

        }

        $conditiontrue = $condition . " and updatetime >= " . $thiscycle;

        $nowcount = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('xg_agent_customer') . $conditiontrue, $params);

        $list[] = array('data' => $nowcount, 'date' => $cyclestr, 'stime' => $thiscycle, 'etime' => $time);



        while ($thiscycle >= $min) {

            $etime = $thiscycle;

            $stime = strtotime("-" . $interval, $thiscycle);

            $conditiontrue = $condition . " and updatetime < " . $etime . " and updatetime >= " . $stime;

            $nowcount = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('xg_agent_customer') . $conditiontrue, $params);

            $thiscycle = $stime;

            $date = date('Y-m-d', $thiscycle);

            $list[] = array('data' => $nowcount, 'date' => $date, 'stime' => $stime, 'etime' => $etime);

        }



        $list = array_reverse($list);



        echo json_encode(array('code' => 3, 'data' => $list));



    }



    public function gettongji()

    {

        global $_W, $_GPC;

        $uniacid = $_W['uniacid'];

        $stime = $_GPC['stime'];

        $etime = $_GPC['etime'];

        $tuandui = $_GPC['tuandui'];

        $condition = " where uniacid=:uniacid";

        $params = array(':uniacid' => $uniacid);

        if ($tuandui != '') {

            $tempuser = pdo_getall('xg_agent_user', array('tid' => $tuandui, 'flag' => 0, 'uniacid' => $uniacid));

            $cids = "";

            foreach ($tempuser as $v) {

                $cids .= $v['id'] . ",";

            }

            if ($cids != '') {

                $cids = substr($cids, 0, -1);

            } else {

                $cids = "-1";

            }

            $condition .= " and cid in(" . $cids . ")";

        }

        $condition .= " and updatetime >= " . $stime . " and updatetime < " . $etime;

        $cusomerlist = pdo_fetchall("select * from " . tablename('xg_agent_customer') . $condition, $params);

        $cuiban=pdo_getall('xg_agent_status',array('uniacid'=>$uniacid,'type'=>1));

        foreach($cuiban as $k=>$v2) {

            $cuiban[$k]['num'] = 0;

        }

        $tempgjfs=pdo_fetchall("select * from ".tablename('xg_agent_gjfs')." where uniacid=:uniacid limit 0,4",array(':uniacid'=>$uniacid));

        foreach($tempgjfs as &$v2){

            $v2['num']=0;

        }
        unset($v2);

        foreach ($cusomerlist as $v) {

            $templog = pdo_fetchall("select * from " . tablename('xg_agent_genjinlog') . " where uniacid=:uniacid and cid=:cid order by createtime desc ", array(':uniacid' => $uniacid, ':cid' => $v['id']));


            foreach($tempgjfs as &$v2){

                if ($templog[0]['gj_fs'] == $v2['name']) {
                    $v2['num']++;
                }

            }
            unset($v2);



            foreach($cuiban as $k=>$v3){

                if($v['status']==$v3['id']){

                    $tempcount=pdo_fetchcolumn("select count(*) from ".tablename('xg_agent_fangyuan')." where cid = ".$v['id']);

                    $cuiban[$k]['num']+=$tempcount;

                }

            }


        }


        echo json_encode(array('code' => 3,'data' => $tempgjfs,'chengjiao'=>$cuiban));

    }

    public function getrate(){
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $stime=$_GPC['stime'];
        $etime=$_GPC['etime'];
        $condition = " where uniacid=:uniacid";

        $params = array(':uniacid' => $uniacid);

        $rate_old=pdo_fetchall("select * from ".tablename('xg_agent_status').$condition." order by displayorder limit 0,3",$params);
        $rate_new=pdo_fetchall("select * from ".tablename('xg_agent_status').$condition." order by displayorder limit 1,3",$params);
        if($stime!=''&&$etime!=''){
            $condition.=" and updatetime >= ".$stime." and updatetime < ".$etime;
        }

        $rate=[];
        $tempcustomer=pdo_fetchall("select * from ".tablename('xg_agent_customer').$condition,$params);
        for($i=0;$i<3;$i++){
            $rate[$i]['text']=$rate_old[$i]['name'].'转'.$rate_new[$i]['name'];
            $rate[$i]['num']=0;

            foreach($tempcustomer as $v){
                $conditionold=" where uniacid=:uniacid and gj_stuta = ".$rate_old[$i]['id']." and cid = ".$v['id'];
                $conditionnew=" where uniacid=:uniacid and gj_stuta = ".$rate_new[$i]['id']." and cid = ".$v['id'];
                $log_new=pdo_fetch("select * from ".tablename('xg_agent_genjinlog').$conditionnew." order by createtime desc ",$params);

                if($log_new!=''){
                    $log_old=pdo_fetch("select * from ".tablename('xg_agent_genjinlog').$conditionold." and createtime < ".$log_new['createtime'],$params);

                    if($log_old!=''){
                        $rate[$i]['num']++;
                    }
                }
            }

        }

        $tempcustomer=pdo_fetchall("select * from ".tablename('xg_agent_customer').$condition,$params);
        foreach($tempcustomer as $v){
            foreach($rate_new as $k=>$v2){
                $conditionold=$condition." and gj_stuta = ".$v2['id']." and cid = ".$rate_old[$k]['id'];
                $conditionnew=$condition." and gj_stuta = ".$v2['id']." and cid = ".$v['id'];
                $log_new=pdo_get("select * from ".tablename('xg_agent_genjinlog').$conditionnew." order by createtime desc ",$params);
                if($log_new!=''){
                    $log_old=pdo_get("select * from ".tablename('xg_agent_genjinlog').$conditionold." and createtime < ".$log_new['createtime'],$params);
                    if($log_old!=''){
                        $rate[$k]['num']++;
                    }
                }

            }
        }

        $count=count($tempcustomer);
        foreach($rate as &$v){
            $v['num']= sprintf("%.2f", ($v['num']/$count)*100);
        }
        unset($v);

        echo json_encode(array('code' => 3,'data' => $rate));
    }





}