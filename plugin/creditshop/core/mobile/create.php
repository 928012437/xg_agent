<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require XG_AGENT_PLUGIN . 'creditshop/core/page_mobile.php';
class Create_XgAgentPage extends CreditshopMobilePage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);
		$optionid = intval($_GPC['optionid']);
		$shop = m('common')->getSysset('shop');
		$member = m('member')->getMember($openid);
		$goods = $this->model->getGoods($id, $member, $optionid);
		if (empty($goods)) 
		{
			$this->message('商品已下架或被删除!', mobileUrl('creditshop'), 'error');
		}
		$pay = m('common')->getSysset('pay');
		$set = m('common')->getPluginset('creditshop');
		$goods['followed'] = m('user')->followed($openid);
		if ($goods['goodstype'] == 0) 
		{
			$stores = array();
			if (!(empty($goods['isverify']))) 
			{
				$storeids = array();
				if (!(empty($goods['storeids']))) 
				{
					$storeids = array_merge(explode(',', $goods['storeids']), $storeids);
				}
				if (empty($storeids))
				{
					$stores = pdo_fetchall('select * from ' . tablename('xg_agent_store') . ' where  uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
				}
				else
				{
					$stores = pdo_fetchall('select * from ' . tablename('xg_agent_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
				}
			}
		}
		include $this->template();
	}
}
?>