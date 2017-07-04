<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
class Page_XgAgentPage extends MobilePage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacd = $_W['uniacid'];
		$merchid = 0;
		$merch_plugin = p('merch');
		$saler = pdo_fetch('select * from ' . tablename('xg_agent_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if (empty($saler) && $merch_plugin) 
		{
			$saler = pdo_fetch('select * from ' . tablename('xg_agent_merch_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		}
		if (empty($saler)) 
		{
			$this->message('您无核销权限!');
		}
		else 
		{
			$merchid = $saler['merchid'];
		}
		$member = m('member')->getMember($saler['openid']);
		$store = false;
		if (!empty($saler['storeid'])) 
		{
			if (0 < $merchid) 
			{
				$store = pdo_fetch('select * from ' . tablename('xg_agent_merch_store') . ' where id=:id and uniacid=:uniacid and merchid = :merchid limit 1', array(':id' => $saler['storeid'], ':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
			}
			else 
			{
				$store = pdo_fetch('select * from ' . tablename('xg_agent_store') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $saler['storeid'], ':uniacid' => $_W['uniacid']));
			}
		}
		include $this->template();
	}
	public function search() 
	{
		global $_W;
		global $_GPC;
		$verifycode = trim($_GPC['verifycode']);
		if (empty($verifycode)) 
		{
			show_json(0, '请填写消费码或自提码');
		}

		$orderid = pdo_fetch('select id,status from ' . tablename('xg_agent_creditshop_log') . ' where uniacid=:uniacid and eno=:eno  limit 1 ', array(':uniacid' => $_W['uniacid'], ':eno' => $verifycode));
		if (empty($orderid['id']))
		{
			show_json(0, '未查询到订单,请核对');
		}
		$allow = com('verify')->allow($orderid['id']);
		if (is_error($allow))
		{
			show_json(0, $allow['message']);
		}
					if ($orderid['status']==3)
					{
						show_json(0, '此消费码已经使用!');
					}

		show_json(1, array('orderid' => $orderid['id']));
	}
	public function complete() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		$times = intval($_GPC['times']);
		com('verify')->verify($orderid, $times);
		show_json(1);
	}
}
?>