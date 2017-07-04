<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Opensource_XgAgentPage
{

//    public function testtempmsg(){
//        global $_W, $_GPC;
//        $this->sess_id=20;
//        $this->user=pdo_get('xg_agent_member',array('id'=>$this->sess_id));
//
//    }

    public function cover(){
        global $_GPC;
        $url=vueurl('qmjjr');

        if(isset($_GPC['tjid'])){
            $url.='&tjid='.$_GPC['tjid'];
        }

        header('location:'.$url);die;

    }

    public function getadv()
    {
        global $_W;
        $adv=pdo_fetchall("select * from ".tablename('xg_agent_adv')." where uniacid=:uniacid and status = 1 order by displayorder",array(':uniacid'=>$_W['uniacid']));
        foreach($adv as &$v){
            $v['img']=tomedia($v['img']);
        }
        unset($v);

        echo json_encode(array('code'=>3,'data'=>$adv));
    }

    public function getloupanlist(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $id=$_SESSION['user'];
        $condition = " WHERE uniacid = :uniacid and isview=1";
        $member=pdo_get('xg_agent_member',array('id'=>$id));
        if($member!=''){
            $condition.=" and (id_view = '' || id_view like '%,".$member['sfid'].",%')";
        }

        $params = array(':uniacid' => $uniacid);
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $list=pdo_fetchall("select * from ".tablename('xg_agent_loupan').$condition." order by displayorder desc limit " . (($pindex - 1) * $psize) . "," . $psize,$params);

        foreach($list as &$v){
            $v['l_biaoqian']=explode(' ',$v['l_biaoqian']);
            $v['thumb']=tomedia($v['thumb']);
        }
        unset($v);

        echo json_encode(array('code'=>3,'data'=>$list));
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

    public function getheaderimg(){
        global $_W;
        $data=pdo_get('xg_agent_qmjjr_rule',array('uniacid'=>$_W['uniacid']));
        echo json_encode(array('code'=>3,'data'=>$data['regimg']));
    }

    public function getidentity(){
        global $_W;
        $identity=pdo_fetchall("select * from ".tablename('xg_agent_identity')." where uniacid=:uniacid and status = 1 order by displayorder ",array(':uniacid'=>$_W['uniacid']));

        echo json_encode(array('code'=>3,'data'=>$identity));
    }

    public function getrule(){
        global $_W;
        $uniacid = $_W['uniacid'];
        $rule=pdo_get('xg_agent_rule',array('uniacid'=>$uniacid));
        $rule['sha_img']=tomedia($rule['sha_img']);

        echo json_encode(array('code' => 3, 'data' => $rule));die;
    }

    public function getnotice(){
        global $_W;
        $notice=pdo_fetch("select * from ".tablename('xg_agent_system_copyright_notice')." where status = 1 and `identity` = 1 and uniacid = :uniacid and starttime < :nowtime and endtime > :nowtime ",array(':uniacid'=>$_W['uniacid'],':nowtime'=>time()));
        if($notice==''){
            echo json_encode(array('code' => 0, 'message' => ''));die;
        }
        $notice['imgurl']=tomedia($notice['imgurl']);
        echo json_encode(array('code' => 3, 'data' => $notice));
    }

}