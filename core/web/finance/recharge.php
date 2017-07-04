<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Recharge_XgAgentPage extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);

		if (!cv('finance.recharge.' . $type)) {
			$this->message('你没有相应的权限查看');
		}

		$id = intval($_GPC['id']);
		$profile = m('member')->getMember($id, true);

		if ($_W['ispost']) {
			$typestr = ($type == 'credit1' ? '积分' : '余额');
			$num = floatval($_GPC['num']);

			if ($num <= 0) {
				show_json(0, array('message' => '请填写大于0的数字!'));
			}

			$changetype = intval($_GPC['changetype']);

			if ($changetype == 2) {
				$num -= $profile[$type];
			}
			else {
				if ($changetype == 1) {
					$num = 0 - $num;
				}
			}

			m('member')->setCredit($profile['openid'], $type, $num, array($_W['uid'], '后台会员充值' . $typestr));

			$url = $_W['siteroot'] . 'app/' . substr(mobileUrl('qmjjr.opensource.cover'), 2);

			if($type=='credit1') {
				$comissdata = array(
					'uniacid' => $_W['uniacid'],
					'mid' => $id,
					'lid' => 0,
					'cid' => 0,
					'commis' => 0,
					'credit' => $num,
					'status' => 0,
					'createtime' => time(),
					'issh' => 1,
					'isdk' => 1,
					'issq' => 0,
					'remark' => $_GPC['remark']
				);
				pdo_insert('xg_agent_commission', $comissdata);
				sendCreditChange($profile['openid'],'后台修改',$_GPC['remark'],$num,$url);
			}else{
				sendCommission($profile['openid'],'后台修改','','',date('Y-m-d H:i:s', time()),$num,date('Y-m-d H:i:s', time()),$profile['credit2'],date('Y-m-d H:i:s', time()),$url);
			}

			plog('qmjjr.recharge.' . $type, '充值' . $typestr . ': ' . $_GPC['num'] . ' <br/>会员信息: ID: ' . $profile['id'] . ' /  ' . $profile['openid'] . '/' . $profile['nickname'] . '/' . $profile['realname'] . '/' . $profile['mobile']."备注：".$_GPC['remark']);
			show_json(1, array('url' => referer()));
		}

		include $this->template();
	}
}

?>
