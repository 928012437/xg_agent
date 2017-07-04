<?php



if (!defined('IN_IA')) {

    exit('Access Denied');

}



class Login_XgAgentPage

{



    public function loginwecha()

    {

        global $_W;

        global $_GPC;

        if (!is_weixin()) {

            echo json_encode(array('code' => 2, 'message' => '不在微信端，请使用帐号密码登录'));

            die;

        } else {

            $user = pdo_get('xg_agent_user', array('openid' => $_W['openid']));

            if ($user == '') {

                echo json_encode(array('code' => 2, 'message' => '该微信号未注册'));

                die;

            }



            $token=$this->getRandChar(24);



            pdo_update('xg_agent_user',array('token'=>$token),array('id'=>$user['id']));



            echo json_encode(array('code' => 2, 'message' => $token));

            die;

        }



    }



    public function loginweb()

    {

        global $_W;

        global $_GPC;

        $rule = pdo_get('xg_agent_rule', array('uniacid' => $_W['uniacid']));

        if ($rule['is_pwlogin'] == 2) {

            echo json_encode(array('code' => 0, 'message' => '当前设置不允许帐号密码登录'));

            die;

        }



        $user = pdo_get('xg_agent_user', array('accounts' => $_GPC['accounts'], 'password' => $_GPC['password']));

        if ($user == '') {

            echo json_encode(array('code' => 1, 'message' => '帐号或密码不正确'));

            die;

        }



        $token=$this->getRandChar(24);



        pdo_update('xg_agent_user',array('token'=>$token),array('id'=>$user['id']));



        echo json_encode(array('code' => 2, 'message' => $token));

        die;



    }



    function getRandChar($length){

        $str = null;

        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";

        $max = strlen($strPol)-1;



        for($i=0;$i<$length;$i++){

            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数

        }



        return $str;

    }

    public function verifycode()
    {
        global $_W;
        global $_GPC;
        $uniacid=$_W['uniacid'];

        $set = pdo_get('xg_agent_rule',array('uniacid'=>$uniacid));

        $mobile = trim($_GPC['mobile']);
        if (empty($mobile))
        {
            echo json_encode(array('code' => 0, 'message' => '请输入手机号'));die;
        }

        $sms_id = $set['tplid'];
        if (empty($sms_id))
        {
            echo json_encode(array('code' => 0, 'message' => '短信发送失败(NOSMSID)'));die;
        }
        $key = 'xg_agent_user_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        @session_start();
        $code = random(4, true);
        $shopname = $_W['shopset']['shop']['name'];
        $ret = com('sms')->send($mobile, $sms_id, array('验证码' => $code, '商城名称' => (!(empty($shopname)) ? $shopname : '商城名称')));
        if ($ret['status'])
        {
            $_SESSION[$key] = $code;
            $_SESSION['verifycodesendtime'] = time();
            echo json_encode(array('code' => 1, 'message' => '短信发送成功'));die;
        }
        echo json_encode(array('code' => 0, 'message' => $ret['message']));die;
    }

    public function register(){

        global $_W;

        global $_GPC;

        $uniacid=$_W['uniacid'];

        $time=time();

        @session_start();
        $key = 'xg_agent_user_verifycodesession_' . $_W['uniacid'] . '_' . $_GPC['tel'];

        if(($time-$_SESSION['verifycodesendtime'])>180){
            echo json_encode(array('code' => 0, 'message' => '验证码超时'));die;
        }

        if($_GPC['code']==''||$_GPC['code']!=$_SESSION[$key]){
            echo json_encode(array('code' => 0, 'message' => '验证码不正确'));die;
        }

        $sfarr=explode(':',$_GPC['type']);

        $rule = pdo_get('xg_agent_rule', array('uniacid' => $uniacid));

        $data=array(

            'uniacid'=>$uniacid,

            'accounts'=>$_GPC['username'],

            'password'=>$_GPC['pwd'],

            'realname'=>$_GPC['rname'],

            'mobile'=>$_GPC['tel'],

            'createtime'=>$time,

            'flag'=>0,

            'tid'=>$sfarr[0],

            'status'=>0

        );

        if($rule['is_review']==1){

            $data['is_review']=0;

        }else{

            $data['is_review']=1;

        }



        $tempu=pdo_get('xg_agent_user',array('accounts'=>$_GPC['username']));



        if($tempu!=''){

            echo json_encode(array('code' => 0, 'message' => '已注册置业顾问或经理'));

            die;

        }



        $uid = $_W['member']['uid'];



        if($uid!=''){

            $navi = pdo_fetch("select nickname,avatar from ".tablename('mc_members')." where uid = ".$uid);

            $openid = $_W['openid'];

            if($navi==''){
                mc_oauth_userinfo();
                $navi['nickname']=$_W['fans']['tag']['nickname'];
                $navi['avatar']=$_W['fans']['tag']['avatar'];
            }

            $tempu=pdo_get('xg_agent_member',array('openid'=>$openid));

            if($tempu!=''){

                echo json_encode(array('code' => 0, 'message' => '已注册经纪人'));

                die;

            }



            $tempu=pdo_get('xg_agent_user',array('openid'=>$openid));

            if($tempu!=''){

                echo json_encode(array('code' => 0, 'message' => '已注册置业顾问或经理'));

                die;

            }



            $data['openid']=$openid;

            $data['nickname']=$navi['nickname'];

            $data['headimgurl']=$navi['avatar'];



        }



        pdo_insert('xg_agent_user',$data);



        echo json_encode(array('code' => 2, 'message' => '注册成功跳转登录页'));

        die;

    }



    public function logout()

    {

        global $_GPC;

        if($_GPC['token']!=''){

            $user=pdo_get('xg_agent_user', array('token' => $_GPC['token']));

            pdo_update('xg_agent_user',array('token'=>''),array('id'=>$user['id']));

        }



        echo json_encode(array('code' => 0, 'message' => '已成功登出'));

        die;

    }



}
