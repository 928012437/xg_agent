<?php

class Notice_XgAgentModel
{
	protected function getUrl($do, $query = NULL)
	{
		$url = mobileUrl($do, $query, true);

		if (strexists($url, '/addons/xg_agent/')) {
			$url = str_replace('/addons/xg_agent/', '/', $url);
		}

		if (strexists($url, '/core/mobile/order/')) {
			$url = str_replace('/core/mobile/order/', '/', $url);
		}

		return $url;
	}

	public function sendOrderMessage($orderid = '0', $delRefund = false)
	{
		global $_W;

		if (empty($orderid)) {
			return NULL;
		}

		$order = pdo_fetch('select * from ' . tablename('xg_agent_order') . ' where id=:id limit 1', array(':id' => $orderid));

		if (empty($order)) {
			return NULL;
		}

		$openid = $order['openid'];
		$url = $this->getUrl('order/detail', array('id' => $orderid));
		$order_goods = pdo_fetchall('select g.id,g.title,og.realprice,og.total,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype from ' . tablename('xg_agent_order_goods') . ' og ' . ' left join ' . tablename('xg_agent_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$goods = '';

		foreach ($order_goods as $og) {
			$goods .= '' . $og['title'] . '( ';

			if (!empty($og['optiontitle'])) {
				$goods .= ' 规格: ' . $og['optiontitle'];
			}

			$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
		}

		$orderpricestr = ' 订单总价: ' . $order['price'] . '(包含运费:' . $order['dispatchprice'] . ')';
		$member = m('member')->getMember($openid);
		$carrier = false;
		$store = false;

		if (!empty($order['storeid'])) {
			$store = pdo_fetch('select * from ' . tablename('xg_agent_store') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $order['storeid'], ':uniacid' => $_W['uniacid']));
		}

		$buyerinfo = '';
		$buyerinfo_name = '';
		$buyerinfo_mobile = '';
		$addressinfo = '';

		if (!empty($order['address'])) {
			$address = iunserializer($order['address_send']);

			if (!is_array($address)) {
				$address = iunserializer($order['address']);

				if (!is_array($address)) {
					$address = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('xg_agent_member_address') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $order['addressid'], ':uniacid' => $_W['uniacid']));
				}
			}

			if (!empty($address)) {
				$addressinfo = $address['province'] . $address['city'] . $address['area'] . ' ' . $address['address'];
				$buyerinfo = '收件人: ' . $address['realname'] . "\n联系电话: " . $address['mobile'] . "\n收货地址: " . $addressinfo;
				$buyerinfo_name = $address['realname'];
				$buyerinfo_mobile = $address['mobile'];
			}
		}
		else {
			$carrier = iunserializer($order['carrier']);

			if (is_array($carrier)) {
				$buyerinfo = '联系人: ' . $carrier['carrier_realname'] . "\n联系电话: " . $carrier['carrier_mobile'];
				$buyerinfo_name = $carrier['carrier_realname'];
				$buyerinfo_mobile = $carrier['carrier_mobile'];
			}
		}

		$datas = array(
			array('name' => '商城名称', 'value' => $_W['shopset']['shop']['name']),
			array('name' => '粉丝昵称', 'value' => $member['nickname']),
			array('name' => '订单号', 'value' => $order['ordersn']),
			array('name' => '订单金额', 'value' => $order['price']),
			array('name' => '运费', 'value' => $order['dispatchprice']),
			array('name' => '商品详情', 'value' => $goods),
			array('name' => '快递公司', 'value' => $order['expresscom']),
			array('name' => '快递单号', 'value' => $order['expresssn']),
			array('name' => '购买者姓名', 'value' => $buyerinfo_name),
			array('name' => '购买者电话', 'value' => $buyerinfo_mobile),
			array('name' => '收货地址', 'value' => $addressinfo),
			array('name' => '下单时间', 'value' => date('Y-m-d H:i', $order['createtime'])),
			array('name' => '支付时间', 'value' => date('Y-m-d H:i', $order['paytime'])),
			array('name' => '发货时间', 'value' => date('Y-m-d H:i', $order['sendtime'])),
			array('name' => '收货时间', 'value' => date('Y-m-d H:i', $order['finishtime'])),
			array('name' => '门店', 'value' => !empty($store) ? $store['storename'] : ''),
			array('name' => '门店地址', 'value' => !empty($store) ? $store['address'] : ''),
			array('name' => '门店联系人', 'value' => !empty($store) ? $store['realname'] . '/' . $store['mobile'] : ''),
			array('name' => '门店营业时间', 'value' => !empty($store) ? (empty($store['saletime']) ? '全天' : $store['saletime']) : ''),
			array('name' => '虚拟物品自动发货内容', 'value' => $order['virtualsend_info']),
			array('name' => '虚拟卡密自动发货内容', 'value' => $order['virtual_str']),
			array('name' => '自提码', 'value' => $order['verifycode'])
			);
		$usernotice = unserialize($member['noticeset']);

		if (!is_array($usernotice)) {
			$usernotice = array();
		}

		$set = m('common')->getSysset();
		$shop = $set['shop'];
		$tm = $set['notice'];

		if ($delRefund) {
			$r_type = array('退款', '退货退款', '换货');

			if (!empty($order['refundid'])) {
				$refund = pdo_fetch('select * from ' . tablename('xg_agent_order_refund') . ' where id=:id limit 1', array(':id' => $order['refundid']));

				if (empty($refund)) {
					return NULL;
				}

				$datas[] = array('name' => '售后类型', 'value' => $r_type[$refund['rtype']]);
				$datas[] = array('name' => '申请金额', 'value' => $refund['rtype'] == 3 ? '-' : $refund['applyprice']);
				$datas[] = array('name' => '退款金额', 'value' => $refund['price']);
				$datas[] = array('name' => '换货快递公司', 'value' => $refund['rexpresscom']);
				$datas[] = array('name' => '换货快递单号', 'value' => $refund['rexpresssn']);

				if (empty($refund['status'])) {
					if (!empty($usernotice['refund'])) {
						return NULL;
					}

					$msg = array(
						'first'             => array('value' => '您的' . $r_type[$refund['rtype']] . '申请已经提交！', 'color' => '#4a5077'),
						'orderProductPrice' => array('title' => '退款金额', 'value' => $refund['rtype'] == 3 ? '-' : '¥' . $refund['applyprice'] . '元', 'color' => '#4a5077'),
						'orderProductName'  => array('title' => '商品详情', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
						'orderName'         => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
						'remark'            => array('value' => "\r\n等待商家确认" . $r_type[$refund['rtype']] . '信息！', 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $openid, 'tag' => 'refund', 'default' => $msg, 'url' => $url, 'datas' => $datas));
					return NULL;
				}

				if ($refund['status'] == 3) {
					if (!empty($usernotice['refunding'])) {
						return NULL;
					}

					$refundaddress = iunserializer($refund['refundaddress']);
					$refundaddressinfo = $refundaddress['province'] . ' ' . $refundaddress['city'] . ' ' . $refundaddress['area'] . ' ' . $refundaddress['address'] . ' 收件人: ' . $refundaddress['name'] . ' (' . $refundaddress['mobile'] . ')(' . $refundaddress['tel'] . ') ';
					$refund_address = '退货地址: ' . $refundaddressinfo;
					$datas[] = array('name' => '退货地址', 'value' => $refundaddressinfo);
					$msg = array(
						'first'    => array('value' => '您的' . $r_type[$refund['rtype']] . '申请已经通过！', 'color' => '#4a5077'),
						'keyword1' => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
						'keyword2' => array('title' => '当前进度', 'value' => '您的' . $r_type[$refund['rtype']] . '申请已经通过！', 'color' => '#4a5077'),
						'keyword3' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
						'keyword4' => array('title' => '退款金额', 'value' => $refund['rtype'] == 3 ? '-' : '¥' . $refund['applyprice'] . '元', 'color' => '#4a5077'),
						'remark'   => array('value' => "\r\n请您根据商家提供的退货地址将商品寄回！" . $refund_address . '', 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $openid, 'tag' => 'refunding', 'default' => $msg, 'url' => $url, 'datas' => $datas));
					return NULL;
				}

				if ($refund['status'] == 5) {
					if (!empty($usernotice['refunding'])) {
						return NULL;
					}

					if (!empty($order['address'])) {
						$address = iunserializer($order['address_send']);

						if (!is_array($address)) {
							$address = iunserializer($order['address']);

							if (!is_array($address)) {
								$address = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('xg_agent_member_address') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $order['addressid'], ':uniacid' => $_W['uniacid']));
							}
						}
					}

					if (empty($address)) {
						return NULL;
					}

					$msg = array(
						'first'    => array('value' => '您的换货物品已经发货！', 'color' => '#4a5077'),
						'keyword1' => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
						'keyword2' => array('title' => '当前进度', 'value' => '您的换货物品已经发货！快递公司: ' . $refund['rexpresscom'] . ' 快递单号: ' . $refund['rexpresssn'], 'color' => '#4a5077'),
						'keyword3' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
						'keyword4' => array('title' => '退款金额', 'value' => $refund['rtype'] == 3 ? '-' : '¥' . $refund['applyprice'] . '元', 'color' => '#4a5077'),
						'remark'   => array('value' => "\r\n我们正加速送到您的手上，请您耐心等候。", 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $openid, 'tag' => 'refunding', 'default' => $msg, 'url' => $url, 'datas' => $datas));
					return NULL;
				}

				if ($refund['status'] == 1) {
					if (!empty($usernotice['refund1'])) {
						return NULL;
					}

					if ($refund['rtype'] == 2) {
						$msg = array(
							'first'             => array('value' => '您的订单已经完成换货！', 'color' => '#4a5077'),
							'orderProductPrice' => array('title' => '退款金额', 'value' => '-', 'color' => '#4a5077'),
							'orderProductName'  => array('title' => '商品详情', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
							'orderName'         => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
							'remark'            => array('value' => "\r\n 换货成功！【" . $shop['name'] . '】期待您再次购物！', 'color' => '#4a5077')
							);
					}
					else {
						$refundtype = '';

						if (empty($refund['refundtype'])) {
							$refundtype = ', 已经退回您的余额账户，请留意查收！';
						}
						else if ($refund['refundtype'] == 1) {
							$refundtype = ', 已经退回您的对应支付渠道（如银行卡，微信钱包等, 具体到账时间请您查看微信支付通知)，请留意查收！';
						}
						else {
							$refundtype = ', 请联系客服进行退款事项！';
						}

						$msg = array(
							'first'             => array('value' => '您的订单已经完成退款！', 'color' => '#4a5077'),
							'orderProductPrice' => array('title' => '退款金额', 'value' => '¥' . $refund['price'] . '元', 'color' => '#4a5077'),
							'orderProductName'  => array('title' => '商品详情', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
							'orderName'         => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
							'remark'            => array('value' => "\r\n 退款金额 ¥" . $refund['price'] . $refundtype . "\r\n 【" . $shop['name'] . '】期待您再次购物！', 'color' => '#4a5077')
							);
					}

					$this->sendNotice(array('openid' => $openid, 'tag' => 'refund1', 'default' => $msg, 'url' => $url, 'datas' => $datas));
					return NULL;
				}

				if ($refund['status'] == -1) {
					if (!empty($usernotice['refund2'])) {
						return NULL;
					}

					$remark = "\n驳回原因: " . $refund['reply'];

					if (!empty($shop['phone'])) {
						$remark .= "\n客服电话:  " . $shop['phone'];
					}

					$msg = array(
						'first'             => array('value' => '您的' . $r_type[$refund['rtype']] . '申请被商家驳回，可与商家协商沟通！', 'color' => '#4a5077'),
						'orderProductPrice' => array('title' => '退款金额', 'value' => '¥' . $refund['price'] . '元', 'color' => '#4a5077'),
						'orderProductName'  => array('title' => '商品详情', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
						'orderName'         => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
						'remark'            => array('value' => $remark, 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $openid, 'tag' => 'refund2', 'default' => $msg, 'url' => $url, 'datas' => $datas));
				}
			}

			return NULL;
		}

		if ($order['status'] == -1) {
			if (!empty($usernotice['cancel'])) {
				return NULL;
			}

			if (!empty($order['addressid'])) {
				$orderAddress = array('title' => '收货信息', 'value' => '收货地址: ' . $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address'] . ' 收件人: ' . $address['realname'] . ' 联系电话: ' . $address['mobile'], 'color' => '#4a5077');
			}
			else if (!empty($order['dispatchtype'])) {
				$orderAddress = array('title' => '收货信息', 'value' => '自提地点: ' . $store['address'] . ' 联系人: ' . $store['realname'] . ' 联系电话: ' . $store['mobile'], 'color' => '#4a5077');
			}
			else {
				$orderAddress = array('title' => '收货信息', 'value' => ' 联系人: ' . $carrier['carrier_realname'] . ' 联系电话: ' . $carrier['carrier_mobile'], 'color' => '#4a5077');
			}

			$msg = array(
				'first'             => array('value' => '您的订单已取消!', 'color' => '#4a5077'),
				'orderProductPrice' => array('title' => '订单金额', 'value' => '¥' . $order['price'] . '元(含运费' . $order['dispatchprice'] . '元)', 'color' => '#4a5077'),
				'orderProductName'  => array('title' => '商品详情', 'value' => $goods, 'color' => '#4a5077'),
				'orderAddress'      => $orderAddress,
				'orderName'         => array('title' => '订单编号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
				'remark'            => array('value' => "\r\n【" . $shop['name'] . '】欢迎您的再次购物！', 'color' => '#4a5077')
				);
			$this->sendNotice(array('openid' => $openid, 'tag' => 'cancel', 'default' => $msg, 'url' => $url, 'datas' => $datas));
			return NULL;
		}

		if ($order['status'] == 0) {
			$newtype = explode(',', $tm['newtype']);
			if (empty($tm['newtype']) || (is_array($newtype) && in_array(0, $newtype))) {
				$remark = "\n订单下单成功,请到后台查看!";

				if (!empty($buyerinfo)) {
					$remark .= "\r\n下单者信息:\n" . $buyerinfo;
				}

				$msg = array(
					'first'    => array('value' => '订单下单通知!', 'color' => '#4a5077'),
					'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
					'keyword2' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
					'keyword3' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
					'remark'   => array('value' => $remark, 'color' => '#4a5077')
					);
				$account = m('common')->getAccount();

				if (!empty($tm['openid'])) {
					$openids = explode(',', $tm['openid']);

					foreach ($openids as $tmopenid) {
						if (empty($tmopenid)) {
							continue;
						}

						$this->sendNotice(array('openid' => $tmopenid, 'tag' => 'new', 'default' => $msg, 'datas' => $datas));
					}
				}
			}

			$remark = "\r\n商品已经下单，请及时备货，谢谢!";

			if (!empty($buyerinfo)) {
				$remark .= "\r\n下单者信息:\n" . $buyerinfo;
			}

			foreach ($order_goods as $og) {
				if (!empty($og['noticeopenid'])) {
					$noticetype = explode(',', $og['noticetype']);
					if (empty($og['noticetype']) || (is_array($noticetype) && in_array(0, $noticetype))) {
						$goodstr = $og['title'] . '( ';

						if (!empty($og['optiontitle'])) {
							$goodstr .= ' 规格: ' . $og['optiontitle'];
						}

						$goodstr .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
						$msg = array(
							'first'    => array('value' => '商品下单通知!', 'color' => '#4a5077'),
							'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
							'keyword2' => array('title' => '商品名称', 'value' => $goodstr, 'color' => '#4a5077'),
							'keyword3' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
							'remark'   => array('value' => $remark, 'color' => '#4a5077')
							);
						$datas[] = array('name' => '单品详情', 'value' => $goodstr);
						$this->sendNotice(array('openid' => $og['noticeopenid'], 'tag' => 'new', 'default' => $msg, 'datas' => $datas));
					}
				}
			}

			if (empty($usernotice['submit'])) {
				if (!empty($order['addressid'])) {
					$remark = "\r\n您的订单我们已经收到，支付后我们将尽快配送~~";
				}
				else if (!empty($order['isverify'])) {
					$remark = "\r\n您的订单我们已经收到，支付后您就可以到店使用了~~";
				}
				else if (!empty($order['virtual'])) {
					$remark = "\r\n您的订单我们已经收到，支付后系统将会自动发货~~";
				}
				else if (!empty($order['dispatchtype'])) {
					$remark = "\r\n您的订单我们已经收到，支付后您就可以到自提点提货物了~~";
				}
				else if (!empty($order['isvirtualsend'])) {
					$remark = "\r\n您的订单我们已经收到，支付后系统会自动发货~~";
				}
				else {
					$remark = "\r\n您的订单我们已经收到~~";
				}

				$msg = array(
					'first'    => array('value' => '您的订单已提交成功！', 'color' => '#4a5077'),
					'keyword1' => array('title' => '店铺', 'value' => $shop['name'], 'color' => '#4a5077'),
					'keyword2' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
					'keyword3' => array('title' => '商品', 'value' => $goods, 'color' => '#4a5077'),
					'keyword4' => array('title' => '金额', 'value' => '¥' . $order['price'] . '元(含运费' . $order['dispatchprice'] . '元)', 'color' => '#4a5077'),
					'remark'   => array('value' => $remark, 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $openid, 'tag' => 'submit', 'default' => $msg, 'url' => $url, 'datas' => $datas));
				return NULL;
			}
		}
		else if ($order['status'] == 1) {
			$newtype = explode(',', $tm['newtype']);
			if (($tm['newtype'] == 1) || (is_array($newtype) && in_array(1, $newtype))) {
				$remark = "\n订单已经下单支付，请及时备货，谢谢!";

				if (!empty($buyerinfo)) {
					$remark .= "\r\n购买者信息:\n" . $buyerinfo;
				}

				$msg = array(
					'first'    => array('value' => '订单下单支付通知!', 'color' => '#4a5077'),
					'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
					'keyword2' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
					'keyword3' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
					'remark'   => array('value' => $remark, 'color' => '#4a5077')
					);
				$account = m('common')->getAccount();

				if (!empty($tm['openid'])) {
					$openids = explode(',', $tm['openid']);

					foreach ($openids as $tmopenid) {
						if (empty($tmopenid)) {
							continue;
						}

						$this->sendNotice(array('openid' => $tmopenid, 'tag' => 'new', 'default' => $msg, 'datas' => $datas));
					}
				}
			}

			$remark = "\r\n商品已经下单支付，请及时备货，谢谢!";

			if (!empty($buyerinfo)) {
				$remark .= "\r\n购买者信息:\n" . $buyerinfo;
			}

			foreach ($order_goods as $og) {
				$noticetype = explode(',', $og['noticetype']);
				if (($og['noticetype'] == '1') || (is_array($noticetype) && in_array(1, $noticetype))) {
					$goodstr = $og['title'] . '( ';

					if (!empty($og['optiontitle'])) {
						$goodstr .= ' 规格: ' . $og['optiontitle'];
					}

					$goodstr .= ' 单价: ' . ($og['price'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['price'] . '); ';
					$msg = array(
						'first'    => array('value' => '商品下单支付通知!', 'color' => '#4a5077'),
						'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
						'keyword2' => array('title' => '商品名称', 'value' => $goodstr, 'color' => '#4a5077'),
						'keyword3' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
						'remark'   => array('value' => $remark, 'color' => '#4a5077')
						);
					$datas[] = array('name' => '单品详情', 'value' => $goodstr);
					$this->sendNotice(array('openid' => $og['noticeopenid'], 'tag' => 'new', 'default' => $msg, 'datas' => $datas));
				}
			}

			if (empty($usernotice['pay'])) {
				$remark = "\r\n【" . $shop['name'] . '】欢迎您的再次购物！';

				if ($order['isverify']) {
					$remark = "\r\n点击订单详情查看可消费门店, 【" . $shop['name'] . '】欢迎您的再次购物！';
				}
				else {
					if ($order['dispatchtype']) {
						$remark = "\r\n您可以到选择的自提点进行取货了,【" . $shop['name'] . '】欢迎您的再次购物！';
					}
				}

				$msg = array(
					'first'    => array('value' => '您已支付成功订单！', 'color' => '#4a5077'),
					'keyword1' => array('title' => '订单', 'value' => $order['ordersn'], 'color' => '#4a5077'),
					'keyword2' => array('title' => '支付状态', 'value' => '支付成功', 'color' => '#4a5077'),
					'keyword3' => array('title' => '支付日期', 'value' => date('Y-m-d H:i:s', $order['paytime']), 'color' => '#4a5077'),
					'keyword4' => array('title' => '商户', 'value' => $shop['name'], 'color' => '#4a5077'),
					'keyword5' => array('title' => '金额', 'value' => '¥' . $order['price'] . '元(含运费' . $order['dispatchprice'] . '元)', 'color' => '#4a5077'),
					'remark'   => array('value' => $remark, 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $openid, 'tag' => 'pay', 'default' => $msg, 'datas' => $datas));
			}

			if (($order['dispatchtype'] == 1) && empty($order['isverify'])) {
				if (!empty($usernotice['carrier'])) {
					return NULL;
				}

				if (!$carrier || !$store) {
					return NULL;
				}

				$msg = array(
					'first'    => array('value' => '自提订单提交成功!', 'color' => '#4a5077'),
					'keyword1' => array('title' => '自提码', 'value' => $order['verifycode'], 'color' => '#4a5077'),
					'keyword2' => array('title' => '商品详情', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
					'keyword3' => array('title' => '提货地址', 'value' => $store['address'], 'color' => '#4a5077'),
					'keyword4' => array('title' => '提货时间', 'value' => $store['saletime'], 'color' => '#4a5077'),
					'remark'   => array('value' => "\r\n请您到选择的自提点进行取货, 自提联系人: " . $store['realname'] . ' 联系电话: ' . $store['mobile'], 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $openid, 'tag' => 'carrier', 'default' => $msg, 'datas' => $datas));
				return NULL;
			}
		}
		else if ($order['status'] == 2) {
			if (empty($order['dispatchtype'])) {
				if (!empty($usernotice['send'])) {
					return NULL;
				}

				if (empty($address)) {
					return NULL;
				}

				$msg = array(
					'first'    => array('value' => '您的宝贝已经发货！', 'color' => '#4a5077'),
					'keyword1' => array('title' => '订单内容', 'value' => '【' . $order['ordersn'] . '】' . $goods . $orderpricestr, 'color' => '#4a5077'),
					'keyword2' => array('title' => '物流服务', 'value' => $order['expresscom'], 'color' => '#4a5077'),
					'keyword3' => array('title' => '快递单号', 'value' => $order['expresssn'], 'color' => '#4a5077'),
					'keyword4' => array('title' => '收货信息', 'value' => '地址: ' . $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address'] . '收件人: ' . $address['realname'] . ' (' . $address['mobile'] . ') ', 'color' => '#4a5077'),
					'remark'   => array('value' => "\r\n我们正加速送到您的手上，请您耐心等候。", 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $openid, 'tag' => 'send', 'default' => $msg, 'datas' => $datas));
				return NULL;
			}
		}
		else {
			if ($order['status'] == 3) {
				$pv = com('virtual');
				if ($pv && !empty($order['virtual'])) {
					if (empty($usernotice['virtualsend'])) {
						$virtual_str = "\n" . $buyerinfo . "\n" . $order['virtual_str'];
						$msg = array(
							'first'    => array('value' => '您购物的物品已自动发货!', 'color' => '#4a5077'),
							'keyword1' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
							'keyword2' => array('title' => '订单号', 'value' => '【' . $order['ordersn'] . '】', 'color' => '#4a5077'),
							'keyword3' => array('title' => '订单金额', 'value' => '¥' . $order['price'] . '元', 'color' => '#4a5077'),
							'keyword4' => array('title' => '卡密信息', 'value' => $virtual_str, 'color' => '#4a5077'),
							'remark'   => array('title' => '', 'value' => "\r\n【" . $shop['name'] . '】感谢您的支持与厚爱，欢迎您的再次购物！', 'color' => '#4a5077')
							);
						$this->sendNotice(array('openid' => $openid, 'tag' => 'virtualsend', 'default' => $msg, 'datas' => $datas));
					}

					$first = '买家购买的商品已经自动发货!';
					$remark = "\r\n发货信息:" . $virtual_str;
					$newtype = explode(',', $tm['newtype']);
					if (($tm['newtype'] == 2) || (is_array($newtype) && in_array(2, $newtype))) {
						$msg = array(
							'first'    => array('value' => $first, 'color' => '#4a5077'),
							'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
							'keyword2' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
							'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
							'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
							'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
							'remark'   => array('title' => '', 'value' => $remark, 'color' => '#4a5077')
							);
						$account = m('common')->getAccount();

						if (!empty($tm['openid'])) {
							$openids = explode(',', $tm['openid']);

							foreach ($openids as $tmopenid) {
								if (empty($tmopenid)) {
									continue;
								}

								$this->sendNotice(array('openid' => $tmopenid, 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
							}
						}
					}

					foreach ($order_goods as $og) {
						$noticetype = explode(',', $og['noticetype']);
						if (($og['noticetype'] == '2') || (is_array($noticetype) && in_array(2, $noticetype))) {
							$goodstr = $og['title'] . '( ';

							if (!empty($og['optiontitle'])) {
								$goodstr .= ' 规格: ' . $og['optiontitle'];
							}

							$goodstr .= ' 单价: ' . ($og['price'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['price'] . '); ';
							$msg = array(
								'first'    => array('value' => $first, 'color' => '#4a5077'),
								'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
								'keyword2' => array('title' => '商品名称', 'value' => $goodstr, 'color' => '#4a5077'),
								'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
								'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
								'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
								'remark'   => array('title' => '', 'value' => $remark, 'color' => '#4a5077')
								);
							$datas[] = array('name' => '单品详情', 'value' => $goodstr);
							$this->sendNotice(array('openid' => $og['noticeopenid'], 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
						}
					}

					return NULL;
				}

				if ($order['isvirtualsend']) {
					if (empty($usernotice['virtualsend'])) {
						$virtual_str = "\n" . $buyerinfo . "\n" . $order['virtual_str'];
						$msg = array(
							'first'    => array('value' => '您购物的物品已自动发货!', 'color' => '#4a5077'),
							'keyword1' => array('title' => '订单金额', 'value' => '¥' . $order['price'] . '元', 'color' => '#4a5077'),
							'keyword2' => array('title' => '商品详情', 'value' => $goods, 'color' => '#4a5077'),
							'keyword3' => array('title' => '收货信息', 'value' => $order['virtualsend_info'], 'color' => '#4a5077'),
							'remark'   => array('title' => '', 'value' => "\r\n【" . $shop['name'] . '】感谢您的支持与厚爱，欢迎您的再次购物！', 'color' => '#4a5077')
							);
						$this->sendNotice(array('openid' => $openid, 'tag' => 'virtualsend', 'default' => $msg, 'datas' => $datas));
					}

					$first = '买家购买的商品已经自动发货!';
					$remark = "\r\n发货信息:" . $virtual_str;
					$newtype = explode(',', $tm['newtype']);
					if (($tm['newtype'] == 2) || (is_array($newtype) && in_array(2, $newtype))) {
						$msg = array(
							'first'    => array('value' => $first, 'color' => '#4a5077'),
							'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
							'keyword2' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
							'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
							'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
							'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
							'remark'   => array('title' => '', 'value' => $remark, 'color' => '#4a5077')
							);
						$account = m('common')->getAccount();

						if (!empty($tm['openid'])) {
							$openids = explode(',', $tm['openid']);

							foreach ($openids as $tmopenid) {
								if (empty($tmopenid)) {
									continue;
								}

								$this->sendNotice(array('openid' => $tmopenid, 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
							}
						}
					}

					foreach ($order_goods as $og) {
						$noticetype = explode(',', $og['noticetype']);
						if (($og['noticetype'] == '2') || (is_array($noticetype) && in_array(2, $noticetype))) {
							$goodstr = $og['title'] . '( ';

							if (!empty($og['optiontitle'])) {
								$goodstr .= ' 规格: ' . $og['optiontitle'];
							}

							$goodstr .= ' 单价: ' . ($og['price'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['price'] . '); ';
							$msg = array(
								'first'    => array('value' => $first, 'color' => '#4a5077'),
								'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
								'keyword2' => array('title' => '商品名称', 'value' => $goodstr, 'color' => '#4a5077'),
								'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
								'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
								'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
								'remark'   => array('title' => '', 'value' => $remark, 'color' => '#4a5077')
								);
							$datas[] = array('name' => '单品详情', 'value' => $goodstr);
							$this->sendNotice(array('openid' => $og['noticeopenid'], 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
						}
					}

					return NULL;
				}

				if (!empty($usernotice['finish'])) {
					return NULL;
				}

				$first = '亲, 您购买的宝贝已经确认收货!';

				if ($order['isverify'] == 1) {
					$first = '亲, 您购买的宝贝已经确认使用!';
				}

				$msg = array(
					'first'    => array('value' => $first, 'color' => '#4a5077'),
					'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
					'keyword2' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
					'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
					'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
					'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
					'remark'   => array('title' => '', 'value' => "\r\n【" . $shop['name'] . '】感谢您的支持与厚爱，欢迎您的再次购物！', 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $openid, 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
				$first = '买家购买的商品已经确认收货!';

				if ($order['isverify'] == 1) {
					$first = '买家购买的商品已经确认核销!';
				}

				$remark = '';

				if (!empty($buyerinfo)) {
					$remark = "\r\n购买者信息:\n" . $buyerinfo;
				}

				$newtype = explode(',', $tm['newtype']);
				if (($tm['newtype'] == 2) || (is_array($newtype) && in_array(2, $newtype))) {
					$msg = array(
						'first'    => array('value' => $first, 'color' => '#4a5077'),
						'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
						'keyword2' => array('title' => '商品名称', 'value' => $goods . $orderpricestr, 'color' => '#4a5077'),
						'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
						'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
						'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
						'remark'   => array('title' => '', 'value' => $remark, 'color' => '#4a5077')
						);
					$account = m('common')->getAccount();

					if (!empty($tm['openid'])) {
						$openids = explode(',', $tm['openid']);

						foreach ($openids as $tmopenid) {
							if (empty($tmopenid)) {
								continue;
							}

							$this->sendNotice(array('openid' => $tmopenid, 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
						}
					}
				}

				foreach ($order_goods as $og) {
					$noticetype = explode(',', $og['noticetype']);
					if (($og['noticetype'] == '2') || (is_array($noticetype) && in_array(2, $noticetype))) {
						$goodstr = $og['title'] . '( ';

						if (!empty($og['optiontitle'])) {
							$goodstr .= ' 规格: ' . $og['optiontitle'];
						}

						$goodstr .= ' 单价: ' . ($og['price'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['price'] . '); ';
						$msg = array(
							'first'    => array('value' => $first, 'color' => '#4a5077'),
							'keyword1' => array('title' => '订单号', 'value' => $order['ordersn'], 'color' => '#4a5077'),
							'keyword2' => array('title' => '商品名称', 'value' => $goodstr, 'color' => '#4a5077'),
							'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $order['createtime']), 'color' => '#4a5077'),
							'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $order['sendtime']), 'color' => '#4a5077'),
							'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $order['finishtime']), 'color' => '#4a5077'),
							'remark'   => array('title' => '', 'value' => $remark, 'color' => '#4a5077')
							);
						$datas[] = array('name' => '单品详情', 'value' => $goodstr);
						$this->sendNotice(array('openid' => $og['noticeopenid'], 'tag' => 'finish', 'default' => $msg, 'datas' => $datas));
					}
				}
			}
		}
	}

	public function sendMemberUpgradeMessage($openid = '', $oldlevel = NULL, $level = NULL)
	{
		global $_W;
		global $_GPC;
		$member = m('member')->getMember($openid);
		$detailurl = $this->getUrl('member');
		$usernotice = unserialize($member['noticeset']);

		if (!is_array($usernotice)) {
			$usernotice = array();
		}

		if (!empty($usernotice['upgrade'])) {
			return NULL;
		}

		if (!$level) {
			$level = m('member')->getLevel($openid);
		}

		$oldlevelname = (empty($oldlevel['levelname']) ? '普通会员' : $oldlevel['levelname']);
		$message = array(
			'first'    => array('value' => '亲爱的' . $member['nickname'] . ', 恭喜您成功升级！', 'color' => '#4a5077'),
			'keyword1' => array('title' => '任务名称', 'value' => '会员升级', 'color' => '#4a5077'),
			'keyword2' => array('title' => '通知类型', 'value' => '您会员等级从 ' . $oldlevelname . ' 升级为 ' . $level['levelname'] . ', 特此通知!', 'color' => '#4a5077'),
			'remark'   => array('value' => "\r\n您即可享有" . $level['levelname'] . '的专属优惠及服务！', 'color' => '#4a5077')
			);
		$this->sendNotice(array(
	'openid'  => $openid,
	'tag'     => 'upgrade',
	'default' => $message,
	'url'     => $detailurl,
	'datas'   => array(
		array('name' => '商城名称', 'value' => $_W['shopset']['shop']['name']),
		array('name' => '粉丝昵称', 'value' => $member['nickname']),
		array('name' => '旧等级', 'value' => $oldlevelname),
		array('name' => '新等级', 'value' => $level['levelname'])
		)
	));
	}

	public function sendMemberLogMessage($log_id = '')
	{
		global $_W;
		global $_GPC;
		$log_info = pdo_fetch('select * from ' . tablename('xg_agent_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $log_id, ':uniacid' => $_W['uniacid']));
		$member = m('member')->getMember($log_info['openid']);
		$usernotice = unserialize($member['noticeset']);

		if (!is_array($usernotice)) {
			$usernotice = array();
		}

		$account = m('common')->getAccount();

		if (!$account) {
			return NULL;
		}

		$datas = array(
			array('name' => '商城名称', 'value' => $_W['shopset']['shop']['name']),
			array('name' => '粉丝昵称', 'value' => $member['nickname'])
			);

		if ($log_info['type'] == 0) {
			$type = '后台充值';

			if ($log_info['rechargetype'] == 'wechat') {
				$type = '微信支付';
			}
			else {
				if ($log_info['rechargetype'] == 'alipay') {
					$type = '支付宝';
				}
			}

			$datas[] = array('name' => '支付方式', 'value' => $type);
			$datas[] = array('name' => '充值金额', 'value' => $log_info['money']);
			$datas[] = array('name' => '充值时间', 'value' => date('Y-m-d H:i', $log_info['createtime']));
			$datas[] = array('name' => '赠送金额', 'value' => $log_info['gives']);
			$datas[] = array('name' => '到帐金额', 'value' => $log_info['money'] + $log_info['gives']);
			$datas[] = array('name' => '退款金额', 'value' => $log_info['money'] + $log_info['gives']);

			if ($log_info['status'] == 1) {
				if (!empty($usernotice['recharge_ok'])) {
					return NULL;
				}

				$money = '¥' . $log_info['money'] . '元';

				if (0 < $log_info['gives']) {
					$totalmoney = $log_info['money'] + $log_info['gives'];
					$money .= '，系统赠送' . $log_info['gives'] . '元，合计:' . $totalmoney . '元';
				}

				$message = array(
					'first'   => array('value' => '恭喜您充值成功!', 'color' => '#4a5077'),
					'money'   => array('title' => '充值金额', 'value' => '¥' . $log_info['money'] . '元', 'color' => '#4a5077'),
					'product' => array('title' => '充值方式', 'value' => $type, 'color' => '#4a5077'),
					'remark'  => array('value' => "\r\n谢谢您对我们的支持！", 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $log_info['openid'], 'tag' => 'recharge_ok', 'default' => $message, 'url' => $this->getUrl('member'), 'datas' => $datas));
				return NULL;
			}

			if ($log_info['status'] == 3) {
				if (!empty($usernotice['recharge_fund'])) {
					return NULL;
				}

				$message = array(
					'first'  => array('value' => '充值退款成功!', 'color' => '#4a5077'),
					'reason' => array('title' => '退款原因', 'value' => '【' . $_W['shopset']['shop']['name'] . '】充值退款', 'color' => '#4a5077'),
					'refund' => array('title' => '退款金额', 'value' => '¥' . $log_info['money'] . '元', 'color' => '#4a5077'),
					'remark' => array('value' => "\r\n退款成功，请注意查收! 谢谢您对我们的支持！", 'color' => '#4a5077')
					);
				$this->sendNotice(array('openid' => $log_info['openid'], 'tag' => 'recharge_refund', 'default' => $message, 'url' => $this->getUrl('member'), 'datas' => $datas));
				return NULL;
			}
		}
		else {
			if ($log_info['type'] == 1) {
				$datas[] = array('name' => '提现金额', 'value' => $log_info['money']);
				$datas[] = array('name' => '提现时间', 'value' => date('Y-m-d H:i', $log_info['createtime']));

				if ($log_info['deductionmoney'] == 0) {
					$realmoeny = $log_info['money'];
				}
				else {
					$realmoeny = $log_info['realmoney'];
				}

				if ($log_info['status'] == 0) {
					if (!empty($usernotice['withdraw'])) {
						return NULL;
					}

					$message = array(
						'first'  => array('value' => '提现申请已经成功提交!', 'color' => '#4a5077'),
						'money'  => array('title' => '提现金额/到账金额', 'value' => '¥' . $log_info['money'] . '元/¥' . $realmoeny . '元', 'color' => '#4a5077'),
						'timet'  => array('title' => '提现时间', 'value' => date('Y-m-d H:i:s', $log_info['createtime']), 'color' => '#4a5077'),
						'remark' => array('value' => "\r\n请等待我们的审核并打款！", 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $log_info['openid'], 'tag' => 'withdraw', 'default' => $message, 'url' => $this->getUrl('member/log', array('type' => 1)), 'datas' => $datas));
					return NULL;
				}

				if ($log_info['status'] == 1) {
					if (!empty($usernotice['withdraw_ok'])) {
						return NULL;
					}

					$message = array(
						'first'  => array('value' => '恭喜您成功提现!', 'color' => '#4a5077'),
						'money'  => array('title' => '提现金额/到账金额', 'value' => '¥' . $log_info['money'] . '元/¥' . $realmoeny . '元', 'color' => '#4a5077'),
						'timet'  => array('title' => '提现时间', 'value' => date('Y-m-d H:i:s', $log_info['createtime']), 'color' => '#4a5077'),
						'remark' => array('value' => "\r\n感谢您的支持！", 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $log_info['openid'], 'tag' => 'withdraw_ok', 'default' => $message, 'url' => $this->getUrl('member/log', array('type' => 1)), 'datas' => $datas));
					return NULL;
				}

				if ($log_info['status'] == -1) {
					if (!empty($usernotice['withdraw_fail'])) {
						return NULL;
					}

					$message = array(
						'first'  => array('value' => '抱歉，提现申请审核失败!', 'color' => '#4a5077'),
						'money'  => array('title' => '提现金额/到账金额', 'value' => '¥' . $log_info['money'] . '元/¥' . $realmoeny . '元', 'color' => '#4a5077'),
						'timet'  => array('title' => '提现时间', 'value' => date('Y-m-d H:i:s', $log_info['createtime']), 'color' => '#4a5077'),
						'remark' => array('value' => "\r\n有疑问请联系客服，谢谢您的支持！", 'color' => '#4a5077')
						);
					$this->sendNotice(array('openid' => $log_info['openid'], 'tag' => 'withdraw_fail', 'default' => $message, 'url' => $this->getUrl('member/log', array('type' => 1)), 'datas' => $datas));
				}
			}
		}
	}

	public function sendNotice(array $params)
	{
		global $_W;
		global $_GPC;
		$tag = (isset($params['tag']) ? $params['tag'] : '');
		$touser = (isset($params['openid']) ? $params['openid'] : '');

		if (empty($touser)) {
			return NULL;
		}

		$tm = $_W['shopset']['notice'];

		if (empty($tm)) {
			$tm = m('common')->getSysset('notice');
		}

		$templateid = ($tm['is_advanced'] ? $tm[$tag . '_template'] : $tm[$tag]);
		$default_message = (isset($params['default']) ? $params['default'] : array());
		$url = (isset($params['url']) ? $params['url'] : '');
		$account = (isset($params['account']) ? $params['account'] : m('common')->getAccount());
		$datas = (isset($params['datas']) ? $params['datas'] : array());
		$advanced_message = false;

		if ($tm['is_advanced']) {
			if (!empty($tm[$tag . '_close_advanced'])) {
				return NULL;
			}

			if (!empty($templateid)) {
				$advanced_template = pdo_fetch('select * from ' . tablename('xg_agent_member_message_template') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $templateid, ':uniacid' => $_W['uniacid']));

				if (!empty($advanced_template)) {
					$advanced_message = array(
						'first'  => array('value' => $this->replaceTemplate($advanced_template['first'], $datas), 'color' => $advanced_template['firstcolor']),
						'remark' => array('value' => $this->replaceTemplate($advanced_template['remark'], $datas), 'color' => $advanced_template['remarkcolor'])
						);
					$data = iunserializer($advanced_template['data']);

					foreach ($data as $d) {
						$advanced_message[$d['keywords']] = array('value' => $this->replaceTemplate($d['value'], $datas), 'color' => $d['color']);
					}

					$ret = m('message')->sendTplNotice($touser, $advanced_template['template_id'], $advanced_message, $url, $account);

					if (is_error($ret)) {
						$ret = m('message')->sendCustomNotice($touser, $advanced_message, $url, $account);

						if (is_error($ret)) {
							$ret = m('message')->sendCustomNotice($touser, $advanced_message, $url, $account);
							return NULL;
						}
					}
				}
				else {
					m('message')->sendCustomNotice($touser, $default_message, $url, $account);
					return NULL;
				}
			}
			else {
				m('message')->sendCustomNotice($touser, $default_message, $url, $account);
				return NULL;
			}
		}
		else {
			if (!empty($tm[$tag . '_close_normal'])) {
				return NULL;
			}

			$ret = m('message')->sendTplNotice($touser, $templateid, $default_message, $url, $account);

			if (is_error($ret)) {
				m('message')->sendCustomNotice($touser, $default_message, $url, $account);
			}
		}
	}

	protected function replaceTemplate($str, $datas = array())
	{
		foreach ($datas as $d) {
			$str = str_replace('[' . $d['name'] . ']', $d['value'], $str);
		}

		return $str;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
