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
        $uniacid=$_W['uniacid'];

        if($_W['ispost']){
            $tixian=implode(',',$_GPC['tixian']);
            if($_GPC['is_pwlogin']==''){
                $is_pwlogin=2;
            }else{
                $is_pwlogin=1;
            }

            if($_GPC['is_review']==''){
                $is_review=0;
            }else{
                $is_review=1;
            }
            if($_GPC['is_auto']==''){
                $is_auto=0;
            }else{
                $is_auto=1;
            }
            $data=array(
                'uniacid'=>$uniacid,
              'is_pwlogin'=>$is_pwlogin,
                'is_review'=>$is_review,
                'is_auto'=>$is_auto,
                'txfs'=>$tixian,
                'title'=>$_GPC['title'],
                'mobile'=>$_GPC['mobile'],
                'tplid'=>$_GPC['tplid'],
                'rule'=>$_GPC['rule'],
                'teams'=>$_GPC['teams'],
                'sha_title'=>$_GPC['sha_title'],
                'sha_dec'=>$_GPC['sha_dec'],
                'sha_img'=>$_GPC['sha_img'],
            );
            if($_GPC['id']==''){
                plog('rule.edit','初始化基本设置');
                pdo_insert('xg_agent_rule',$data);
            }else{
                plog('rule.edit','修改基本设置');
                pdo_update('xg_agent_rule',$data,array('uniacid'=>$uniacid));
            }
            show_json(1);
        }

        $rule=pdo_get('xg_agent_rule',array('uniacid'=>$uniacid));

        $tplid=pdo_getall('xg_agent_sms',array('uniacid'=>$uniacid,'status'=>1));

        include $this->template();
    }


}

?>
