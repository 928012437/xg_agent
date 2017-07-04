<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
define('TM_CREDITSHOP_LOTTERY', 'TM_CREDITSHOP_LOTTERY');
define('TM_CREDITSHOP_EXCHANGE', 'TM_CREDITSHOP_EXCHANGE');
define('TM_CREDITSHOP_WIN', 'TM_CREDITSHOP_WIN');
if (!(class_exists('CreditshopModel'))) 
{
	class CreditshopModel extends PluginModel 
	{
		public function getGoods($id, $member, $optionid = 0) 
		{
			global $_W;
			$credit = $member['credit1'];
			$money = $member['credit2'];
			$optionid = intval($optionid);
			if (empty($id)) 
			{
				return;
			}
			$goods = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
			if (empty($goods)) 
			{
				return array('canbuy' => false, 'buymsg' => '已下架');
			}
			$goods = set_medias($goods, 'thumb');
			if ((0 < $goods['credit']) && (0 < $goods['money'])) 
			{
				$goods['acttype'] = 0;
			}
			else if (0 < $goods['credit']) 
			{
				$goods['acttype'] = 1;
			}
			else if (0 < $goods['money']) 
			{
				$goods['acttype'] = 2;
			}
			else 
			{
				$goods['acttype'] = 3;
			}
			$goods['endtime_str'] = date('Y-m-d H:i', $goods['endtime']);
			$goods['timestart_str'] = date('Y-m-d H:i', $goods['timestart']);
			$goods['timeend_str'] = date('Y-m-d H:i', $goods['timeend']);
			$goods['timestate'] = '';
			$goods['canbuy'] = !(empty($goods['status'])) && empty($goods['deleted']);
			if (empty($goods['canbuy'])) 
			{
				$goods['buymsg'] = '已下架';
			}
			else 
			{
				if (0 < $goods['total']) 
				{
					$logcount = pdo_fetchcolumn('select count(*) from ' . tablename('xg_agent_creditshop_log') . '  where goodsid=:goodsid and status>=2  and uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));
					$goods['logcount'] = $logcount;
					if ($goods['joins'] < $logcount) 
					{
						pdo_update('xg_agent_creditshop_goods', array('joins' => $logcount), array('id' => $id));
					}
				}
				else 
				{
					$goods['canbuy'] = false;
					$goods['buymsg'] = ((empty($goods['type']) ? '已兑完' : '已抽完'));
				}
				if ($goods['hasoption'] && $optionid) 
				{
					$option = pdo_fetch('select total,credit,money,title as optiontitle from ' . tablename('xg_agent_creditshop_option') . ' where uniacid = ' . $_W['uniacid'] . ' and id = ' . $optionid . ' and goodsid = ' . $id . ' ');
					$goods['credit'] = $option['credit'];
					$goods['money'] = $option['money'];
					$goods['optiontitle'] = $option['optiontitle'];
					if ($option['total'] <= 0) 
					{
						$goods['canbuy'] = false;
						$goods['buymsg'] = ((empty($goods['type']) ? '已兑完' : '已抽完'));
					}
				}
				if ($goods['isverify'] == 0) 
				{
					if ($goods['dispatchtype'] == 1) 
					{
						if (empty($goods['dispatchid'])) 
						{
							$dispatch = m('dispatch')->getDefaultDispatch($goods['merchid']);
						}
						else 
						{
							$dispatch = m('dispatch')->getOneDispatch($goods['dispatchid']);
						}
						if (empty($dispatch)) 
						{
							$dispatch = m('dispatch')->getNewDispatch($goods['merchid']);
						}
						$areas = iunserializer($dispatch['areas']);
						if (!(empty($areas)) && is_array($areas)) 
						{
							$firstprice = array();
							foreach ($areas as $val ) 
							{
								$firstprice[] = $val['firstprice'];
							}
							array_push($firstprice, m('dispatch')->getDispatchPrice(1, $dispatch));
							$ret = array('min' => round(min($firstprice), 2), 'max' => round(max($firstprice), 2));
						}
						else 
						{
							$ret = m('dispatch')->getDispatchPrice(1, $dispatch);
						}
						$goods['dispatch'] = $ret;
					}
				}
				if ($goods['canbuy']) 
				{
					if ((0 < $goods['totalday']) && ($goods['type'] == 1)) 
					{
						$logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('xg_agent_creditshop_log') . '  where goodsid=:goodsid and status>=2 and  date_format(from_UNIXTIME(`createtime`),\'%Y-%m-%d\') = date_format(now(),\'%Y-%m-%d\') and uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));
						if ($goods['totalday'] <= $logcount) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = ((empty($goods['type']) ? '今日已兑完' : '今日已抽完'));
						}
					}
				}
				if ($goods['canbuy']) 
				{
					if ((0 < $goods['chanceday']) && ($goods['type'] == 1)) 
					{
						$logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('xg_agent_creditshop_log') . '  where goodsid=:goodsid and openid=:openid and status>0 and  date_format(from_UNIXTIME(`createtime`),\'%Y-%m-%d\') = date_format(now(),\'%Y-%m-%d\') and uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
						if ($goods['chanceday'] <= $logcount) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = ((empty($goods['type']) ? '今日已兑换' : '今日已抽奖'));
						}
					}
				}
				if ($goods['canbuy']) 
				{
					if ((0 < $goods['chance']) && ($goods['type'] == 1)) 
					{
						$logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('xg_agent_creditshop_log') . '  where goodsid=:goodsid and openid=:openid and status>0 and  uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
						if ($goods['chance'] <= $logcount) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = ((empty($goods['type']) ? '已兑换' : '已抽奖'));
						}
					}
				}
				if ($goods['canbuy']) 
				{
					if ((0 < $goods['usermaxbuy']) && ($goods['type'] == 1)) 
					{
						$logcount = pdo_fetchcolumn('select ifnull(sum(total),0)  from ' . tablename('xg_agent_creditshop_log') . '  where goodsid=:goodsid and openid=:openid  and uniacid=:uniacid ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
						if ($goods['chance'] <= $logcount) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = '已参加';
						}
					}
				}
				if ($goods['canbuy']) 
				{
					if (($credit < $goods['credit']) && (0 < $goods['credit'])) 
					{
						$goods['canbuy'] = false;
						$goods['buymsg'] = '积分不足';
					}
				}
				if ($goods['canbuy']) 
				{
					if ($goods['istime'] == 1) 
					{
						if (time() < $goods['timestart']) 
						{
							$goods['canbuy'] = false;
							$goods['timestate'] = 'before';
							$goods['buymsg'] = '活动未开始';
						}
						else if ($goods['timeend'] < time()) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = '活动已结束';
						}
						else 
						{
							$goods['timestate'] = 'after';
						}
					}
				}
				if ($goods['canbuy']) 
				{
					if (($goods['isendtime'] == 1) && $goods['isverify']) 
					{
						if ($goods['endtime'] < time()) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = '活动已结束(超出兑换期)';
						}
					}
				}
				$levelid = $member['level'];
				$groupid = $member['groupid'];
				if ($goods['canbuy']) 
				{
					if ($goods['buylevels'] != '') 
					{
						$buylevels = explode(',', $goods['buylevels']);
						if (!(in_array($levelid, $buylevels))) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = '无会员特权';
						}
					}
				}
				if ($goods['canbuy']) 
				{
					if ($goods['buygroups'] != '') 
					{
						$buygroups = explode(',', $goods['buygroups']);
						if (!(in_array($groupid, $buygroups))) 
						{
							$goods['canbuy'] = false;
							$goods['buymsg'] = '无会员特权';
						}
					}
				}
			}
			$goods['followtext'] = ((empty($goods['followtext']) ? '您必须关注我们的公众帐号，才能参加活动哦!' : $goods['followtext']));
			$set = $this->getSet();
			$goods['followurl'] = $set['followurl'];
			if (empty($goods['followurl'])) 
			{
				$share = m('common')->getSysset('share');
				$goods['followurl'] = $share['followurl'];
			}
			if ((intval($goods['money']) - $goods['money']) == 0) 
			{
				$goods['money'] = intval($goods['money']);
			}
			if ((intval($goods['minmoney']) - $goods['minmoney']) == 0) 
			{
				$goods['minmoney'] = intval($goods['minmoney']);
			}
			if ((intval($goods['minmoney']) - $goods['minmoney']) == 0) 
			{
				$goods['minmoney'] = intval($goods['minmoney']);
			}
			return $goods;
		}
		public function createENO() 
		{
			global $_W;
			$ecount = pdo_fetchcolumn('select count(*) from ' . tablename('xg_agent_creditshop_log') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			if ($ecount < 99999999) 
			{
				$ecount = 8;
			}
			else 
			{
				$ecount = strlen($ecount . '');
			}
			$eno = rand(pow(10, $ecount), pow(10, $ecount + 1) - 1);
			while (1) 
			{
				$c = pdo_fetchcolumn('select count(*) from ' . tablename('xg_agent_creditshop_log') . ' where uniacid=:uniacid and eno=:eno limit 1', array(':uniacid' => $_W['uniacid'], ':eno' => $eno));
				if ($c <= 0) 
				{
					break;
				}
				$eno = rand(pow(10, $ecount), pow(10, $ecount + 1) - 1);
			}
			return $eno;
		}
		public function sendMessage($id = 0) 
		{
			global $_W;
			if (empty($id)) 
			{
				return;
			}
			$log = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
			if (empty($log)) 
			{
				return;
			}
			$member = m('member')->getMember($log['openid']);
			if (empty($member)) 
			{
				return;
			}
			$credit = intval($member['credit1']);
			$goods = $this->getGoods($log['goodsid'], $member);
			if (empty($goods['id'])) 
			{
				return;
			}
			$type = $goods['type'];
			$credits = '';
			if ((0 < $goods['credit']) & (0 < $goods['money'])) 
			{
				$credits = $goods['credit'] . '积分+' . $goods['money'] . '元';
			}
			else if (0 < $goods['credit']) 
			{
				$credits = $goods['credit'] . '积分';
			}
			else if (0 < $goods['money']) 
			{
				$credits = $goods['money'] . '元';
			}
			else 
			{
				$credits = '0';
			}
			$shop = m('common')->getSysset('shop');
			$set = $this->getSet();
			$tm = $set['tm'];
			$detailurl = mobileUrl('creditshop/log/detail', array('id' => $id), true);
			if (strexists($detailurl, '/addons/xg_agent/'))
			{
				$detailurl = str_replace('/addons/xg_agent/', '/', $detailurl);
			}
			if ($log['status'] == 2) 
			{
				if (!(empty($type))) 
				{
					if ($log['dispatchstatus'] != 1) 
					{
						$remark = "\r\n" . ' 【' . $shop['name'] . '】期待您再次光顾！';
						if ($log['dispatchstatus'] != -1) 
						{
							if (0 < $goods['dispatch']) 
							{
								$remark = "\r\n" . ' 请您点击支付邮费后, 我们会尽快发货，【' . $shop['name'] . '】期待您再次光顾！';
							}
							else 
							{
								$remark = "\r\n" . ' 请您点击选择邮寄地址后, 我们会尽快发货，【' . $shop['name'] . '】期待您再次光顾！';
							}
						}
						$msg = array( 'first' => array('value' => '恭喜您，您中奖啦~', 'color' => '#4a5077'), 'keyword1' => array('title' => '活动', 'value' => '积分商城抽奖', 'color' => '#4a5077'), 'keyword2' => array('title' => '奖品', 'value' => $goods['title'], 'color' => '#4a5077'), 'remark' => array('value' => $remark, 'color' => '#4a5077') );
						if (!(empty($tm['award']))) 
						{
							m('message')->sendTplNotice($log['openid'], $tm['award'], $msg, $detailurl);
						}
						else 
						{
							m('message')->sendCustomNotice($log['openid'], $msg, $detailurl);
						}
					}
				}
				else if ($log['dispatchstatus'] != 1) 
				{
					$remark = "\r\n" . ' 【' . $shop['name'] . '】期待您再次光顾！';
					if ($log['dispatchstatus'] != -1) 
					{
						if (0 < $goods['dispatch']) 
						{
							$remark = "\r\n" . ' 请您点击支付邮费后, 我们会尽快发货，【' . $shop['name'] . '】期待您再次光顾！';
						}
						else 
						{
							$remark = "\r\n" . ' 请您点击选择邮寄地址后, 我们会尽快发货，【' . $shop['name'] . '】期待您再次光顾！';
						}
					}
					$msg = array( 'first' => array('value' => '恭喜您，商品兑换成功~', 'color' => '#4a5077'), 'keyword1' => array('title' => '奖品名称', 'value' => $goods['title'], 'color' => '#4a5077'), 'keyword2' => array('title' => '消耗积分', 'value' => $credits, 'color' => '#4a5077'), 'keyword3' => array('title' => '剩余积分', 'value' => $credit, 'color' => '#4a5077'), 'keyword4' => array('title' => '兑换时间', 'value' => date('Y-m-d', time()), 'color' => '#4a5077'), 'remark' => array('value' => $remark, 'color' => '#4a5077') );
					if (!(empty($tm['exchange']))) 
					{
						m('message')->sendTplNotice($log['openid'], $tm['exchange'], $msg, $detailurl);
					}
					else 
					{
						m('message')->sendCustomNotice($log['openid'], $msg, $detailurl);
					}
				}
				if (($log['dispatchstatus'] == 1) || ($log['dispatchstatus'] == -1)) 
				{
					$remark = '收货信息:  无需物流';
					if (!(empty($log['addressid']))) 
					{
						$address = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('xg_agent_member_address') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $log['addressid'], ':uniacid' => $_W['uniacid']));
						if (!(empty($address))) 
						{
							$remark = '收件人: ' . $address['realname'] . ' 联系电话: ' . $address['mobile'] . ' 收货地址: ' . $address['province'] . $address['city'] . $address['area'] . ' ' . $address['address'];
						}
						$remark .= ', 请及时备货,谢谢!';
					}
					$msg = array( 'first' => array('value' => '积分商城商品兑换成功~', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单编号', 'value' => $log['logno'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $goods['title'], 'color' => '#4a5077'), 'keyword3' => array('title' => '商品数量', 'value' => 1, 'color' => '#4a5077'), 'keyword4' => array('title' => '兑换时间', 'value' => date('Y-m-d', $log['createtime']), 'color' => '#4a5077'), 'remark' => array('value' => $remark, 'color' => '#4a5077') );
					$noticeopenids = explode(',', $goods['noticeopenid']);
					if (empty($goods['noticeopenid'])) 
					{
						$noticeopenids = explode(',', $set['tm']['openids']);
					}
					if (!(empty($noticeopenids))) 
					{
						foreach ($noticeopenids as $noticeopenid ) 
						{
							if (!(empty($tm['new']))) 
							{
								m('message')->sendTplNotice($noticeopenid, $tm['new'], $msg);
							}
							else 
							{
								m('message')->sendCustomNotice($noticeopenid, $msg);
							}
						}
						return;
					}
				}
			}
			else if ($log['status'] == 3) 
			{
				$info = '无需物流';
				if (!(empty($log['addressid']))) 
				{
					$address = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('xg_agent_member_address') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $log['addressid'], ':uniacid' => $_W['uniacid']));
					if (!(empty($address))) 
					{
						$info = ' 收件人: ' . $address['realname'] . ' 联系电话: ' . $address['mobile'] . ' 收货地址: ' . $address['province'] . $address['city'] . $address['area'] . ' ' . $address['address'];
					}
				}
				$msg = array( 'first' => array('value' => '您的积分兑换奖品已发货~', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单金额', 'value' => '使用 ' . $credits, 'color' => '#4a5077'), 'keyword2' => array('title' => '商品详情', 'value' => $goods['title'], 'color' => '#4a5077'), 'keyword3' => array('title' => '收货信息', 'value' => $info, 'color' => '#4a5077'), 'remark' => array('value' => $remark, 'color' => '#4a5077') );
				if (!(empty($tm['send']))) 
				{
					m('message')->sendTplNotice($log['openid'], $tm['send'], $msg, $detailurl);
				}
				else 
				{
					m('message')->sendCustomNotice($log['openid'], $msg, $detailurl);
				}
				$detailurl1 = mobileUrl('creditshop/detail', array('id' => $log['goodsid']), true);
				if (strexists($detailurl1, '/addons/xg_agent/'))
				{
					$detailurl1 = str_replace('/addons/xg_agent/', '/', $detailurl1);
				}
				$msg_saler = array( 'first' => array('value' => '用户奖品兑换成功~', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单金额', 'value' => '使用 ' . $credits, 'color' => '#4a5077'), 'keyword2' => array('title' => '商品详情', 'value' => $goods['title'], 'color' => '#4a5077'), 'keyword3' => array('title' => '收货信息', 'value' => $info, 'color' => '#4a5077'), 'remark' => array('value' => $remark, 'color' => '#4a5077') );
				if (!(empty($tm['send']))) 
				{
					m('message')->sendTplNotice($log['verifyopenid'], $tm['send'], $msg_saler, $detailurl1);
					return;
				}
				m('message')->sendCustomNotice($log['verifyopenid'], $msg_saler, $detailurl1);
			}
		}
		public function createQrcode($logid = 0) 
		{
			global $_W;
			global $_GPC;
			$path = IA_ROOT . '/addons/xg_agent/data/creditshop/' . $_W['uniacid'];
			if (!(is_dir($path))) 
			{
				load()->func('file');
				mkdirs($path);
			}
			$url = mobileUrl('creditshop/exchange', array('id' => $logid), true);
			$file = 'exchange_qrcode_' . $logid . '.png';
			$qrcode_file = $path . '/' . $file;
			if (!(is_file($qrcode_file))) 
			{
				require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
				QRcode::png($url, $qrcode_file, QR_ECLEVEL_H, 4);
			}
			return $_W['siteroot'] . '/addons/xg_agent/data/creditshop/' . $_W['uniacid'] . '/' . $file;
		}
		public function perms() 
		{
			return array( 'creditshop' => array( 'text' => $this->getName(), 'isplugin' => true, 'child' => array( 'cover' => array('text' => '入口设置'), 'goods' => array('text' => '商品', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'category' => array('text' => '分类', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'adv' => array('text' => '幻灯片', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'log' => array('text' => '兑换记录', 'view0' => '浏览兑换记录', 'view1' => '浏览抽奖记录', 'exchange' => '确认兑换-log', 'export0' => '导出兑换记录-log', 'export1' => '导出抽奖记录-log'), 'notice' => array('text' => '通知设置', 'view' => '查看', 'save' => '修改-log'), 'set' => array('text' => '基础设置', 'view' => '查看', 'save' => '修改-log') ) ) );
		}
		public function allow($logid, $times = 0, $verifycode = '', $openid = '') 
		{
			global $_W;
			global $_GPC;
			if (empty($openid)) 
			{
				$openid = $_W['openid'];
			}
			$uniacid = $_W['uniacid'];
			$store = false;
			$lastverifys = 0;
			$verifyinfo = false;
			if ($times <= 0) 
			{
				$times = 1;
			}
			$saler = pdo_fetch('select * from ' . tablename('xg_agent_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			if (empty($saler)) 
			{
				return error(-1, '无操作权限!');
			}
			$log = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_log') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $logid, ':uniacid' => $uniacid));
			$goods = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_goods') . ' where uniacid=:uniacid and id = :goodsid ', array(':uniacid' => $uniacid, ':goodsid' => $log['goodsid']));
			if (empty($log)) 
			{
				return error(-1, '该记录不存在!');
			}
			if ($log['verifytime'] < time()) 
			{
				return error(-1, '该记录已失效，兑换期限已过!');
			}
			if (empty($goods)) 
			{
				return error(-1, '订单异常!');
			}
			if (empty($goods['isverify'])) 
			{
				return error(-1, '订单无需核销!');
			}
			$storeids = array();
			if (!(empty($goods['storeids']))) 
			{
				$storeids = explode(',', $goods['storeids']);
			}
			if (!(empty($storeids))) 
			{
				if (!(empty($saler['storeid']))) 
				{
					if (!(in_array($saler['storeid'], $storeids))) 
					{
						return error(-1, '您无此门店的操作权限!');
					}
				}
			}
			if ($goods['verifytype'] == 0) 
			{
				$verifynum = pdo_fetchcolumn('select COUNT(1) from ' . tablename('xg_agent_creditshop_verify') . ' where uniacid = :uniacid and logid = :logid ', array(':uniacid' => $uniacid, ':logid' => $logid));
				if (!(empty($verifynum))) 
				{
					return error(-1, '此订单已完成核销！');
					if ($goods['verifytype'] == 1) 
					{
						$verifynum = pdo_fetchcolumn('select COUNT(1) from ' . tablename('xg_agent_creditshop_verify') . ' where uniacid = :uniacid and logid = :logid ', array(':uniacid' => $uniacid, ':logid' => $logid));
						if ($goods['verifynum'] <= $verifynum) 
						{
							return error(-1, '此订单已完成核销！');
						}
						$lastverifys = $goods['verifynum'] - $verifynum;
						if (($lastverifys < 0) && !(empty($goods['verifytype']))) 
						{
							return error(-1, '此订单最多核销 ' . $goods['verifynum'] . ' 次!');
						}
					}
				}
			}
			else 
			{
				$verifynum = pdo_fetchcolumn('select COUNT(1) from ' . tablename('xg_agent_creditshop_verify') . ' where uniacid = :uniacid and logid = :logid ', array(':uniacid' => $uniacid, ':logid' => $logid));
				return error(-1, '此订单已完成核销！');
				$lastverifys = $goods['verifynum'] - $verifynum;
				return error(-1, '此订单最多核销 ' . $goods['verifynum'] . ' 次!');
			}
			if (!(empty($saler['storeid']))) 
			{
				$store = pdo_fetch('select * from ' . tablename('xg_agent_store') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $saler['storeid'], ':uniacid' => $_W['uniacid']));
			}
			$carrier = unserialize($log['carrier']);
			return array('log' => $log, 'store' => $store, 'saler' => $saler, 'lastverifys' => $lastverifys, 'goods' => $goods, 'verifyinfo' => $verifyinfo, 'carrier' => $carrier);
		}
		public function verify($logid = 0, $times = 0, $verifycode = '', $openid = '') 
		{
			global $_W;
			global $_GPC;
			$uniacid = $_W['uniacid'];
			$current_time = time();
			if (empty($openid)) 
			{
				$openid = $_W['openid'];
			}
			$data = $this->allow($logid, $times, $openid);
			if (is_error($data)) 
			{
				return;
			}
			extract($data);
			$saler = pdo_fetch('select * from ' . tablename('xg_agent_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			$log = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_log') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $logid, ':uniacid' => $uniacid));
			$goods = pdo_fetch('select * from ' . tablename('xg_agent_creditshop_goods') . ' where uniacid=:uniacid and id = :goodsid ', array(':uniacid' => $uniacid, ':goodsid' => $log['goodsid']));
			if ($goods['isverify']) 
			{
				if ($goods['verifytype'] == 0) 
				{
					pdo_update('xg_agent_creditshop_log', array('status' => 3, 'usetime' => time(), 'verifyopenid' => $openid, 'time_finish' => time()), array('id' => $logid));
					$data = array('uniacid' => $uniacid, 'openid' => $log['openid'], 'logid' => $logid, 'verifycode' => $log['eno'], 'storeid' => $saler['storeid'], 'verifier' => $openid, 'isverify' => 1, 'verifytime' => time());
					pdo_insert('xg_agent_creditshop_verify', $data);
				}
				else if ($goods['verifytype'] == 1) 
				{
					if ($log['status'] != 3) 
					{
						pdo_update('xg_agent_creditshop_log', array('status' => 3, 'usetime' => time(), 'verifyopenid' => $openid, 'time_finish' => time()), array('id' => $logid));
					}
					$i = 1;
					while ($i <= $times) 
					{
						$data = array('uniacid' => $uniacid, 'openid' => $log['openid'], 'logid' => $logid, 'verifycode' => $log['eno'], 'storeid' => $saler['storeid'], 'verifier' => $openid, 'isverify' => 1, 'verifytime' => time());
						pdo_insert('xg_agent_creditshop_verify', $data);
						++$i;
					}
				}
			}
			return true;
		}
	}
}
?>