<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Fangyuan_XgAgentPage extends MobilePage
{
    function __construct()
    {
        global $_W,$_GPC;
        parent::__construct();
        $this->user = pdo_get('xg_agent_user', array('uniacid'=>$_W['uniacid'],'token' => $_GPC['token']));

        if ($this->user == '') {
            echo json_encode(array('code' => 0, 'message' => '无执行权限'));
            die;
        }
    }

    public function getloulist()
    {
        global $_W;
        $uniacid = $_W['uniacid'];

        $loupan = pdo_getall('xg_agent_loupan', array('uniacid' => $uniacid));
        $list = array();
        for ($i = 0; $i < count($loupan); $i++) {
            $qi = pdo_getall('xg_agent_fytype', array('lid' => $loupan[$i]['id']));
            if ($qi != '') {
                for ($j = 0; $j < count($qi); $j++) {
                    $list[] = $qi[$j];
                }
            }
        }
        for ($i = 0; $i < count($list); $i++) {
            $qu = pdo_getall('xg_agent_fytype', array('type' => 1, 'did' => $list[$i]['id']));
            if ($qu != '') {
                for ($j = 0; $j < count($qu); $j++) {
                    $hao = pdo_getall('xg_agent_fytype', array('type' => 2, 'did' => $qu[$j]['id']));
                    for ($x1 = 0; $x1 < count($hao); $x1++) {
                        $surpl = 0;
                        $danyuan = pdo_getall('xg_agent_fytype', array('type' => 3, 'did' => $hao[$x1]['id']));
                        for ($x2 = 0; $x2 < count($danyuan); $x2++) {
                            $ceng = pdo_getall('xg_agent_fytype', array('type' => 4, 'did' => $danyuan[$x2]['id']));
                            for ($x3 = 0; $x3 < count($ceng); $x3++) {
                                $hu = pdo_getall('xg_agent_fytype', array('type' => 5, 'did' => $ceng[$x3]['id']));
                                for ($x4 = 0; $x4 < count($hu); $x4++) {
                                    $fy = pdo_get('xg_agent_fangyuan', array('huid' => $hu[$x4]['id']));
                                    if ($fy['status'] == 0) {
                                        $surpl++;
                                    }
                                }
                            }
                        }

                        $hao[$x1]['surpl'] = $surpl;
                    }
                    if ($hao != '') {
                        $qu[$j]['hao'] = $hao;
                    }
                }
            }
            $loup = pdo_get('xg_agent_loupan', array('id' => $list[$i]['lid']));
            $list[$i]['louname'] = $loup['title'];
            $list[$i]['qu'] = $qu;
        }

        echo json_encode(array('code' => 3, 'data' => $list));
    }

    public function getdanyuanlist()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];

        if ($id != 0) {
            $hao = pdo_get('xg_agent_fytype', array('type' => 2, 'uid' => $uniacid, 'id' => $id));
        } else {
            $hao = pdo_get('xg_agent_fytype', array('type' => 2, 'uid' => $uniacid));
        }
        $qu = pdo_get('xg_agent_fytype', array('type' => 1, 'id' => $hao['did']));
        $qi = pdo_get('xg_agent_fytype', array('type' => 0, 'id' => $qu['did']));
        $loupan = pdo_get('xg_agent_loupan', array('id' => $qi['lid']));
        $title = $loupan['title'] . "-" . $qi['name'] . "-" . $qu['name'] . "-" . $hao['name'];

        //所包含单元,层，户
        $danyuan = pdo_fetchall("SELECT id,name FROM " . tablename('xg_agent_fytype') . " WHERE type=3 AND did=:did ORDER BY id desc", array(':did' => $hao['id']));

        echo json_encode(array('code' => 3, 'data' => $danyuan, 'title' => $title));

    }

    public function getcenghulist()
    {
        global $_W, $_GPC;

        $danyuanid = $_GPC['id'];
        $condition = "";
        $sale_no=0;
        $sale_ok=0;
        $ceng = pdo_fetchall("SELECT id,name FROM " . tablename('xg_agent_fytype') . " WHERE type=4 AND did=:did ORDER BY (name +0) desc", array(':did' => $danyuanid));
        for ($j = 0; $j < count($ceng); $j++) {
            $hu = pdo_fetchall("SELECT id,name FROM " . tablename('xg_agent_fytype') . " WHERE type=5 AND did=:did ORDER BY ( name +0) asc", array(':did' => $ceng[$j]['id']));
            for ($l = 0; $l < count($hu); $l++) {

                //添加where条件
                $info = pdo_fetch("select * from " . tablename('xg_agent_fangyuan') . " where huid=:huid $condition", array(':huid' => $hu[$l]['id']));

                if ($info != '') {

                    $hu[$l]['status'] = $info['status'];
                    if($info['status']==0){
                        $sale_no++;
                    }else{
                        $sale_ok++;
                    }

                    $ceng[$j]['hu'][$l] = $hu[$l];
                }
            }
            if (!isset($ceng[$j]['hu'])) {
                $ceng[$j] = '';
            }
        }

        $znum=$sale_no+$sale_ok;
        $bili=array('saleo_num'=>$sale_ok,'saleo_prop'=>round($sale_ok/$znum*100),'salen_num'=>$sale_no,'salen_prop'=>round($sale_no/$znum*100));

        echo json_encode(array('code' => 3, 'data' => $ceng,'bili'=>$bili));
    }

    public function fangyuaninfo()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $info = pdo_get('xg_agent_fangyuan', array('huid' => $id));
        $hu = pdo_get('xg_agent_fytype', array('id' => $info['huid']));
        $ceng = pdo_get('xg_agent_fytype', array('id' => $hu['did']));
        $danyuan = pdo_get('xg_agent_fytype', array('id' => $ceng['did']));
        $hao = pdo_get('xg_agent_fytype', array('id' => $danyuan['did']));
        $qu = pdo_get('xg_agent_fytype', array('id' => $hao['did']));
        $qi = pdo_get('xg_agent_fytype', array('id' => $qu['did']));
        $loupan = pdo_get('xg_agent_loupan', array('id' => $qi['lid']));
        $info['cengnum'] = $ceng['name'];
        $info['title'] = $loupan['title'] . '-' . $qi['name'] . '-' . $qu['name'] . '-' . $hao['name'] . '-' . $danyuan['name'] . '-' . $ceng['name'] . '层' . $hu['name'].'户';

        echo json_encode(array('code' => 3, 'data' => $info));
    }

    public function getcholou(){
        global $_W;
        $uniacid=$_W['uniacid'];
        $loupan = pdo_getall('xg_agent_loupan', array('uniacid' => $uniacid));
        $list = array();
        for ($i = 0; $i < count($loupan); $i++) {
            $qi = pdo_getall('xg_agent_fytype', array('lid' => $loupan[$i]['id']));
            if ($qi != '') {
                for ($j = 0; $j < count($qi); $j++) {
                    $list[] = $qi[$j];
                }
            }
        }
        for ($i = 0; $i < count($list); $i++) {
            $qu = pdo_getall('xg_agent_fytype', array('type' => 1, 'did' => $list[$i]['id']));
            if ($qu != '') {
                for ($j = 0; $j < count($qu); $j++) {
                    $hao = pdo_getall('xg_agent_fytype', array('type' => 2, 'did' => $qu[$j]['id']));
                    for ($x1 = 0; $x1 < count($hao); $x1++) {
                        $surpl = 0;
                        $danyuan = pdo_getall('xg_agent_fytype', array('type' => 3, 'did' => $hao[$x1]['id']));
                        for ($x2 = 0; $x2 < count($danyuan); $x2++) {
                            $ceng = pdo_getall('xg_agent_fytype', array('type' => 4, 'did' => $danyuan[$x2]['id']));
                            for ($x3 = 0; $x3 < count($ceng); $x3++) {
                                $hu = pdo_getall('xg_agent_fytype', array('type' => 5, 'did' => $ceng[$x3]['id']));
                                for ($x4 = 0; $x4 < count($hu); $x4++) {
                                    $fy = pdo_get('xg_agent_fangyuan', array('huid' => $hu[$x4]['id']));
                                    if ($fy['status'] == 0) {
                                        $surpl++;
                                    }
                                }
                            }
                        }

                        $hao[$x1]['surpl'] = $surpl;
                    }
                    if ($hao != '') {
                        $qu[$j]['hao'] = $hao;
                    }
                }
            }
            $loup = pdo_get('xg_agent_loupan', array('id' => $list[$i]['lid']));
            $list[$i]['louname'] = $loup['title'];
            $list[$i]['qu'] = $qu;
        }

        echo json_encode(array('code' => 3, 'data' => $list));
    }

}