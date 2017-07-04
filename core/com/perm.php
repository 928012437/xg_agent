<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Perm_XgAgentComModel extends ComModel
{
    public function allPerms()
    {
        $perms = array('goods' => $this->perm_goods(), 'customer' => $this->perm_customer(), 'business' => $this->perm_business(), 'qmjjr' => $this->perm_qmjjr(), 'creditshop' => $this->perm_creditshop(), 'sign' => $this->perm_sign(), 'postera' => $this->perm_postera(), 'rule' => $this->perm_rule(), 'sysset' => $this->perm_sysset(), 'perm' => $this->perm_perm());
        return $perms;
    }

    protected function perm_goods()
    {
        return array(
            'text' => '项目管理',
            'main' => '查看',
            'add' => '添加-log',
            'edit' => '修改-log',
            'delete' => '删除-log',

            'photo' => array(
                'text' => '楼盘相册管理',
                'main' => '查看',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'hximg' => array(
                'text' => '户型图管理',
                'main' => '查看',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'fangyuan' => array(
                'text' => '房源管理',
                'main' => '查看',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'adv' => array(
                'text' => '幻灯片管理',
                'main' => '查看',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
        );
    }

    protected function perm_customer()
    {
        return array(
            'text' => '客户管理',
            'detail' => array(
                'text' => '客户详情',
                'main' => '查看客户详情',
                'edit' => '修改客户详情'
            ),
            'allot' => '分配客户',
            'isreview' => '审核客户',
            'isvalid' => '客户置于无效',
            'delete' => '删除客户',
            'export' => '导出客户',
            'gjset' => '跟进设置',
            'pool' => '客户池'
        );
    }

    protected function perm_business()
    {
        return array(
            'text' => '团队管理',
            'main' => '浏览团队列表',
            'userlist' => array(
                'text' => '团队人员管理',
                'main' => '查看业务员列表',
                'detail' => '业务员详情',
                'custo' => '查看业务员客户',
                'isreview' => '审核业务员-log',
                'edit' => '修改业务员-log',
                'delete' => '删除业务员-log'
            ),
            'add' => '添加团队-log',
            'edit' => '修改团队-log',
            'delete' => '删除团队-log',
            'cover' => '入口设置',
        );
    }

    protected function perm_qmjjr()
    {
        return array(
            'text' => '全民经纪人',
            'main' => '查看经纪人',
            'status' => '设置黑名单-log',
            'review' => '设置审核-log',
            'detail' => '经纪人详情',
            'jcustomer' => '查看客户',
            'jxiaji' => '查看下级',
            'recharge'=>array(
                'text'=>'充值',
                'credit1'=>'充值积分-log',
                'credit2'=>'充值余额-log',
            ),
            'mcommis' => '查看提现订单',
            'edit' => '修改经纪人-log',
            'delete' => '删除经纪人-log',
            'identity' => array(
                'text' => '身份管理',
                'main' => '查看',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'question' => array(
                'text' => '问题管理',
                'main' => '查看',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'templatenews' => '模板消息设置',
            'jjrrule' => array(
                'text' => '佣金积分规则',
                'main' => '查看',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'commis' => array(
                'text' => '佣金管理',
                'quanbu' => '全部',
                'sh_no' => '待审核',
                'dk_no' => '待打款',
                'dk_ok' => '已打款',
                'wuxiao' => '无效的',
            ),
            'rule' => '基础设置',
            'cover' => '入口设置'
        );
    }

    protected function perm_creditshop()
    {
        return ($this->isopen('creditshop') && $this->is_perm_plugin('creditshop') ? array('text' => m('plugin')->getName('creditshop'), 'goods' => array('text' => '商品', 'main' => '查看列表', 'view' => '查看详细', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'xxx' => array('property' => 'edit')), 'category' => array('text' => '分类', 'main' => '查看列表', 'view' => '查看详细', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'xxx' => array('enabled' => 'edit', 'displayorder' => 'edit')), 'adv' => array('text' => '幻灯片', 'main' => '查看列表', 'add' => '添加-log', 'edit' => '修改-log', 'view' => '查看详细', 'delete' => '删除-log', 'xxx' => array('displayorder' => 'edit', 'enabled' => 'edit')), 'log' => array('text' => '订单/记录', 'exchange' => '兑换记录', 'draw' => '抽奖记录', 'order' => '待发货', 'convey' => '待收货', 'finish' => '已完成', 'verifying' => '待核销', 'verifyover' => '已核销', 'verify' => '全部核销', 'detail' => '详情', 'doexchange' => '确认兑换-log', 'export' => '导出明细-log'), 'comment' => array('text' => '评价管理', 'edit' => '回复评价', 'check' => '审核评价'), 'cover' => array('text' => '入口设置', 'main' => '查看', 'edit' => '修改-log'), 'notice' => array('text' => '通知设置', 'main' => '查看', 'edit' => '修改-log'), 'set' => array('text' => '基础设置', 'main' => '查看', 'edit' => '修改-log')) : array());
    }

    protected function perm_sign()
    {
        return ($this->isopen('sign') && $this->is_perm_plugin('sign') ? array('text' => m('plugin')->getName('sign'), 'rule' => array('text' => '签到规则', 'main' => '查看', 'edit' => '编辑-log'), 'set' => array('text' => '签到入口', 'main' => '查看', 'edit' => '编辑-log'), 'records' => array('text' => '签到记录', 'main' => '查看')) : array());
    }

    protected function perm_postera()
    {
        return ($this->isopen('postera') && $this->is_perm_plugin('postera') ? array('text' => m('plugin')->getName('postera'), 'main' => '查看列表', 'view' => '查看', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'clear' => '清除缓存-log', 'xxx' => array('setdefault' => 'edit'), 'log' => array('text' => '关注记录', 'main' => '查看')) : array());
    }

    protected function perm_rule()
    {
        return array(
            'text' => '设置',
            'edit' => '修改基本设置-log',
            'option' => array(
                'text' => '选项设置',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'option2' => array(
                'text' => '二级选项设置',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'notice'=>array(
                'text'=>'公告管理',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
            ),
            'verify' => ($this->isopen('verify', true) && $this->is_perm_plugin('verify', true) ? array( 'text' => 'O2O核销', 'saler' => array( 'text' => '店员管理', 'main' => '查看列表', 'view' => '查看内容', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'xxx' => array('status' => 'edit') ), 'store' => array( 'text' => '门店管理', 'main' => '查看列表', 'view' => '查看内容', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'xxx' => array('displayorder' => 'edit', 'status' => 'edit') ), 'set' => array('text' => '关键词设置', 'main' => '查看', 'edit' => '编辑-log') ) : array())
        );
    }

    protected function perm_sysset()
    {
        return array('text' => '高级设置', 'shop' => array('text' => '商城设置', 'main' => '查看', 'edit' => '修改-log'), 'trade' => array('text' => '交易设置', 'main' => '查看', 'edit' => '修改-log'), 'payset' => array('text' => '支付方式', 'edit' => '修改-log'), 'cover' => array('shop' => array('text' => '用户入口', 'main' => '查看', 'edit' => '修改-log')));
    }

    protected function perm_perm()
    {
        return array(
            'text' => '权限系统',
            'log' => array('text' => '操作日志', 'main' => '查看列表'),
            'role' => array(
                'text' => '角色管理',
                'main' => '查看列表',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
                'xxx' => array('status' => 'edit', 'query' => 'main')
            ),
            'user' => array(
                'text' => '操作员管理',
                'main' => '查看列表',
                'add' => '添加-log',
                'edit' => '修改-log',
                'delete' => '删除-log',
                'xxx' => array('status' => 'edit')
            )
        );
    }

    public function isopen($pluginname = '', $iscom = false)
    {
        if (empty($pluginname)) {
            return false;
        }

        $plugins = m('plugin')->getAll($iscom);
        $plugins_name = array_map(function ($val) {
            return $val['identity'];
        }, $plugins);

        if (in_array($pluginname, $plugins_name)) {
            foreach ($plugins as $plugin) {
                if ($plugin['identity'] == strtolower($pluginname)) {
                    if (empty($plugin['status'])) {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public function is_perm_plugin($pluginname = '', $iscom = false)
    {
        global $_W;

        if (empty($pluginname)) {
            return false;
        }

        $perm_plugin = pdo_fetch('SELECT * FROM ' . tablename('xg_agent_perm_plugin') . ' WHERE acid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));

        if (!empty($perm_plugin)) {
            $plugins = explode(',', $perm_plugin['plugins']);
            $coms = explode(',', $perm_plugin['coms']);

            if ($iscom) {
                return in_array($pluginname, $coms);
            }

            return in_array($pluginname, $plugins);
        }

        return true;
    }

    public function check_edit($permtype = '', $item = array())
    {
        if (empty($permtype)) {
            return false;
        }

        if (!$this->check_perm($permtype)) {
            return false;
        }

        if (empty($item['id'])) {
            $add_perm = $permtype . '.add';

            if (!$this->check($add_perm)) {
                return false;
            }

            return true;
        }

        $edit_perm = $permtype . '.edit';

        if (!$this->check($edit_perm)) {
            return false;
        }

        return true;
    }

    public function check_perm($permtypes = '')
    {
        global $_W;
        $check = true;

        if (empty($permtypes)) {
            return false;
        }

        if (!strexists($permtypes, '&') && !strexists($permtypes, '|')) {
            $check = $this->check($permtypes);
        } else if (strexists($permtypes, '&')) {
            $pts = explode('&', $permtypes);

            foreach ($pts as $pt) {
                $check = $this->check($pt);

                if (!$check) {
                    break;
                }
            }
        } else {
            if (strexists($permtypes, '|')) {
                $pts = explode('|', $permtypes);

                foreach ($pts as $pt) {
                    $check = $this->check($pt);

                    if ($check) {
                        break;
                    }
                }
            }
        }

        return $check;
    }

    private function check($permtype = '')
    {
        global $_W;
        global $_GPC;
        if (($_W['role'] == 'manager') || ($_W['role'] == 'founder')) {
            return true;
        }

        $uid = $_W['uid'];

        if (empty($permtype)) {
            return false;
        }

        $user = pdo_fetch('select u.status as userstatus,r.status as rolestatus,u.perms2 as userperms,r.perms2 as roleperms,u.roleid from ' . tablename('xg_agent_perm_user') . ' u ' . ' left join ' . tablename('xg_agent_perm_role') . ' r on u.roleid = r.id ' . ' where u.uid=:uid limit 1 ', array(':uid' => $uid));
        if (empty($user) || empty($user['userstatus'])) {
            return false;
        }

        if (!empty($user['role']) && empty($user['rolestatus'])) {
            return false;
        }

        $role_perms = explode(',', $user['roleperms']);
        $user_perms = explode(',', $user['userperms']);
        $perms = array_merge($role_perms, $user_perms);

        if (empty($perms)) {
            return false;
        }

        $is_xxx = $this->check_xxx($permtype);

        if ($is_xxx) {
            if (!in_array($is_xxx, $perms)) {
                return false;
            }
        } else {
            if (!in_array($permtype, $perms)) {
                return false;
            }
        }

        return true;
    }

    public function check_xxx($permtype)
    {
        if ($permtype) {
            $allPerm = $this->allPerms();
            $permarr = explode('.', $permtype);

            if (isset($permarr[3])) {
                $is_xxx = (isset($allPerm[$permarr[0]][$permarr[1]][$permarr[2]]['xxx'][$permarr[3]]) ? $allPerm[$permarr[0]][$permarr[1]][$permarr[2]]['xxx'][$permarr[3]] : false);
            } else if (isset($permarr[2])) {
                $is_xxx = (isset($allPerm[$permarr[0]][$permarr[1]]['xxx'][$permarr[2]]) ? $allPerm[$permarr[0]][$permarr[1]]['xxx'][$permarr[2]] : false);
            } else if (isset($permarr[1])) {
                $is_xxx = (isset($allPerm[$permarr[0]]['xxx'][$permarr[1]]) ? $allPerm[$permarr[0]]['xxx'][$permarr[1]] : false);
            } else {
                $is_xxx = false;
            }

            if ($is_xxx) {
                $permarr = explode('.', $permtype);
                array_pop($permarr);
                $is_xxx = implode('.', $permarr) . '.' . $is_xxx;
            }

            return $is_xxx;
        }

        return false;
    }

    public function check_plugin($pluginname = '')
    {
        global $_W;
        global $_GPC;
        $permset = $this->getPermset();

        if (empty($permset)) {
            return true;
        }

        if (($_W['role'] == 'founder') || empty($_W['role'])) {
            return true;
        }

        $isopen = $this->isopen($pluginname);

        if (!$isopen) {
            return false;
        }

        $allow = true;
        $acid = pdo_fetchcolumn('SELECT acid FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid LIMIT 1', array(':uniacid' => $_W['uniacid']));
        $ac_perm = pdo_fetch('select  plugins from ' . tablename('xg_agent_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $acid));

        if (!empty($ac_perm)) {
            $allow_plugins = explode(',', $ac_perm['plugins']);

            if (!in_array($pluginname, $allow_plugins)) {
                $allow = false;
            }
        } else {
            load()->model('account');
            $accounts = uni_owned($_W['founder']);

            if (in_array($_W['uniacid'], array_keys($accounts))) {
                $allow = true;
            } else {
                $allow = false;
            }
        }

        if (!$allow) {
            return false;
        }

        return true;
    }

    public function getPermset()
    {
        $set = m('cache')->getString('permset2', 'global');

        if ($set == '') {
            m('cache')->set('permset2', 'false', 'global');
            return false;
        }

        return $set;
    }

    public function check_com($comname = '')
    {
        global $_W;
        global $_GPC;
        $permset = $this->getPermset();

        if (empty($permset)) {
            return true;
        }

        if (($_W['role'] == 'founder') || empty($_W['role'])) {
            return true;
        }

        $isopen = $this->isopen($comname, true);

        if (!$isopen) {
            return false;
        }

        $allow = true;
        $acid = pdo_fetchcolumn('SELECT acid FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid LIMIT 1', array(':uniacid' => $_W['uniacid']));
        $ac_perm = pdo_fetch('select  coms from ' . tablename('xg_agent_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $acid));

        if (!empty($ac_perm)) {
            $allow_coms = explode(',', $ac_perm['coms']);

            if (!in_array($comname, $allow_coms)) {
                $allow = false;
            }
        } else {
            load()->model('account');
            $accounts = uni_owned($_W['founder']);

            if (in_array($_W['uniacid'], array_keys($accounts))) {
                $allow = true;
            } else {
                $allow = false;
            }
        }

        if (!$allow) {
            return false;
        }

        return true;
    }

    public function getLogName($type = '', $logtypes = NULL)
    {
        if (!$logtypes) {
            $logtypes = $this->getLogTypes();
        }

        foreach ($logtypes as $t) {
            if ($t['value'] == $type) {
                return $t['text'];
            }
        }

        return '';
    }

    public function getLogTypes($all = false)
    {
        $perms = $this->allPerms();
        $array = array();
        array_walk($perms, function ($value, $key) use (&$array, $all) {
            if (is_array($value)) {
                array_walk($value, function ($val, $ke) use (&$array, $value, $key, $all) {
                    if (!is_array($val)) {
                        if ($all) {
                            if ($ke == 'text') {
                                $text = str_replace('-log', '', $value['text']);
                            } else {
                                $text = str_replace('-log', '', $value['text'] . '-' . $val);
                            }

                            $array[] = array('text' => $text, 'value' => str_replace('.text', '', $key . '.' . $ke));
                        } else {
                            if (strexists($val, '-log')) {
                                $text = str_replace('-log', '', $value['text'] . '-' . $val);

                                if ($ke == 'text') {
                                    $text = str_replace('-log', '', $value['text']);
                                }

                                $array[] = array('text' => $text, 'value' => str_replace('.text', '', $key . '.' . $ke));
                            }
                        }
                    }

                    if (is_array($val) && ($ke != 'xxx')) {
                        array_walk($val, function ($v, $k) use (&$array, $value, $key, $val, $ke, $all) {
                            if (!is_array($v)) {
                                if ($all) {
                                    if ($ke == 'text') {
                                        $text = str_replace('-log', '', $value['text'] . '-' . $val['text']);
                                    } else {
                                        $text = str_replace('-log', '', $value['text'] . '-' . $val['text'] . '-' . $v);
                                    }

                                    $array[] = array('text' => $text, 'value' => str_replace('.text', '', $key . '.' . $ke . '.' . $k));
                                } else {
                                    if (strexists($v, '-log')) {
                                        $text = str_replace('-log', '', $value['text'] . '-' . $val['text'] . '-' . $v);

                                        if ($k == 'text') {
                                            $text = str_replace('-log', '', $value['text'] . '-' . $val['text']);
                                        }

                                        $array[] = array('text' => $text, 'value' => str_replace('.text', '', $key . '.' . $ke . '.' . $k));
                                    }
                                }
                            }

                            if (is_array($v) && ($k != 'xxx')) {
                                array_walk($v, function ($vv, $kk) use (&$array, $value, $key, $val, $ke, $v, $k, $all) {
                                    if (!is_array($vv)) {
                                        if ($all) {
                                            if ($ke == 'text') {
                                                $text = str_replace('-log', '', $value['text'] . '-' . $val['text'] . '-' . $v['text']);
                                            } else {
                                                $text = str_replace('-log', '', $value['text'] . '-' . $val['text'] . '-' . $v['text'] . '-' . $vv);
                                            }

                                            $array[] = array('text' => $text, 'value' => str_replace('.text', '', $key . '.' . $ke . '.' . $k . '.' . $kk));
                                            return NULL;
                                        }

                                        if (strexists($vv, '-log')) {
                                            if (empty($val['text'])) {
                                                $text = str_replace('-log', '', $value['text'] . '-' . $v['text'] . '-' . $vv);
                                            } else {
                                                $text = str_replace('-log', '', $value['text'] . '-' . $val['text'] . '-' . $v['text'] . '-' . $vv);
                                            }

                                            if ($kk == 'text') {
                                                $text = str_replace('-log', '', $value['text'] . '-' . $val['text'] . '-' . $v['text']);
                                            }

                                            $array[] = array('text' => $text, 'value' => str_replace('.text', '', $key . '.' . $ke . '.' . $k . '.' . $kk));
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
        return $array;
    }

    public function log($type = '', $op = '')
    {
        global $_W;
        $this->check_xxx($type);

        if ($is_xxx = $this->check_xxx($type)) {
            $type = $is_xxx;
        }

        static $_logtypes;

        if (!$_logtypes) {
            $_logtypes = $this->getLogTypes();
        }

        $log = array('uniacid' => $_W['uniacid'], 'uid' => $_W['uid'], 'name' => $this->getLogName($type, $_logtypes), 'type' => $type, 'op' => $op, 'ip' => CLIENT_IP, 'createtime' => time());
        pdo_insert('xg_agent_perm_log', $log);
    }

    public function formatPerms()
    {
        $perms = $this->allPerms();
        $array = array();
        array_walk($perms, function ($value, $key) use (&$array) {
            if (is_array($value)) {
                array_walk($value, function ($val, $ke) use (&$array, $key) {
                    if (!is_array($val)) {
                        $array['parent'][$key][$ke] = $val;
                    }

                    if (is_array($val) && ($ke != 'xxx')) {
                        array_walk($val, function ($v, $k) use (&$array, $key, $ke) {
                            if (!is_array($v)) {
                                $array['son'][$key][$ke][$k] = $v;
                            }

                            if (is_array($v) && ($k != 'xxx')) {
                                array_walk($v, function ($vv, $kk) use (&$array, $key, $ke, $k) {
                                    if (!is_array($vv)) {
                                        $array['grandson'][$key][$ke][$k][$kk] = $vv;
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
        return $array;
    }
}

?>
