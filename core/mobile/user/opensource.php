<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Opensource_XgAgentPage
{

    public function cover(){
        header('location:'.vueurl('app'));
        die;
    }

    public function getheadimg(){
        global $_W;
        $uid = $_W['member']['uid'];
        if($uid!=''){
            $navi = pdo_fetch("select avatar from ".tablename('mc_members')." where uid = ".$uid);
        }
        if(!isset($navi)&&$navi==''){
            $navi['avatar']="../src/img/user.png";
        }
        $rule=pdo_get('xg_agent_rule',array('uniacid'=>$_W['uniacid']));
        $teams=htmlspecialchars_decode($rule['teams']);

        echo json_encode(array('code'=>3,'data'=>$navi['avatar'],'rule'=>$teams));
    }

    public function gettuandui(){
        global $_W;
        $identity=pdo_fetchall("select * from ".tablename('xg_agent_tuandui')." where uniacid=:uniacid ",array(':uniacid'=>$_W['uniacid']));

        echo json_encode(array('code'=>3,'data'=>$identity));
    }

    public function getnotice1(){
    global $_W;
    $notice=pdo_fetch("select * from ".tablename('xg_agent_system_copyright_notice')." where status = 1 and `identity` = 2 and uniacid = :uniacid and starttime < :nowtime and endtime > :nowtime ",array(':uniacid'=>$_W['uniacid'],':nowtime'=>time()));
        if($notice==''){
            echo json_encode(array('code' => 0, 'message' => ''));die;
        }
    $notice['imgurl']=tomedia($notice['imgurl']);
    echo json_encode(array('code' => 3, 'data' => $notice));
    }
    public function getnotice2(){
        global $_W;
        $notice=pdo_fetch("select * from ".tablename('xg_agent_system_copyright_notice')." where status = 1 and `identity` = 3 and uniacid = :uniacid and starttime < :nowtime and endtime > :nowtime ",array(':uniacid'=>$_W['uniacid'],':nowtime'=>time()));
        if($notice==''){
            echo json_encode(array('code' => 0, 'message' => ''));die;
        }
        $notice['imgurl']=tomedia($notice['imgurl']);
        echo json_encode(array('code' => 3, 'data' => $notice));
    }

}