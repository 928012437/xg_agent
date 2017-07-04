<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Massproduction_XgAgentPage extends PluginWebPage
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

    public function main(){
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];

        if($_W['ispost']){
            $time=time();
            $memberlist=m('excel')->import('massproduction');

            $identity=pdo_get('xg_agent_identity',array('id'=>$_GPC['sfid']));

            foreach($memberlist as $k=>$v){
                if(strstr($v[1],'、')){
                    $name=explode('、',$v[1])[0];
                }else{
                    $name=$v[1];
                }

                $name=str_replace(' ','',$name);
                $password=str_replace(' ','',$v[2]);

                $data=array(
                    'uniacid'=>$uniacid,

                    'openid'=>'massopenid'.$time.'-'.$k,

                    'accounts'=>$name,

                    'password'=>$password,

                    'realname'=>$name,

                    'createtime'=>$time,

                    'sfid'=>$_GPC['sfid'],

                    'credit1'=>$identity['credit2'],

                    'review'=>1,

                    'status'=>1
                );

                pdo_insert('xg_agent_member',$data);

                $id=pdo_insertid();

                if($identity['credit2']!=0){

                    $comissdata = array(

                        'uniacid' => $uniacid,

                        'mid' => $id,

                        'lid' => 0,

                        'cid' => -2,

                        'commis' => 0,

                        'credit' => $identity['credit2'],

                        'status' => 0,

                        'createtime' => $time,

                        'checktime'=>$time,

                        'issh' => 1,

                        'isdk' => 1,

                        'issq' => 0

                    );

                    pdo_insert('xg_agent_commission', $comissdata);

                }

            }

            show_json(1);
        }

        $identity=pdo_getall('xg_agent_identity',array('uniacid'=>$uniacid));


        include $this->template();
    }

}