<?php

class Order_XgAgentModel
{
	public function payResult($params)
	{
		global $_W;
		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		$ordersn = $params['tid'];
		$order = pdo_fetch('select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,virtual,isvirtual,couponid,isvirtualsend from ' . tablename('ewei_shop_order') . ' where  ordersn=:ordersn and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':ordersn' => $ordersn));
		$orderid = $order['id'];

		if ($params['from'] == 'return') {
			$address = false;

			if (empty($order['dispatchtype'])) {
				$address = pdo_fetch('select realname,mobile,address from ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
			}

			$carrier = false;
			if (($order['dispatchtype'] == 1) || ($order['isvirtual'] == 1)) {
				$carrier = unserialize($order['carrier']);
			}

			if ($params['type'] == 'cash') {
				return true;
			}

			if ($order['status'] == 0) {
				if (!empty($order['virtual']) && com('virtual')) {
					return com('virtual')->pay($order);
				}

				if ($order['isvirtualsend']) {
					return $this->payVirtualSend($order['id']);
				}

				pdo_update('ewei_shop_order', array('status' => 1, 'paytime' => time()), array('id' => $orderid));
				$this->setStocksAndCredits($orderid, 1);
				if (com('coupon') && !empty($order['couponid'])) {
					com('coupon')->backConsumeCoupon($order['id']);
				}

				m('notice')->sendOrderMessage($orderid);

				if (p('commission')) {
					p('commission')->checkOrderPay($order['id']);
				}
			}

			return true;
		}

		return false;
	}

	public function payVirtualSend($orderid = 0)
	{
		global $_W;
		global $_GPC;
		$order = pdo_fetch('select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,virtual,isvirtual,couponid from ' . tablename('ewei_shop_order') . ' where  id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $orderid));
		$order_goods = pdo_fetch('select g.virtualsend,g.virtualsendcontent from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$time = time();
		pdo_update('ewei_shop_order', array('virtualsend_info' => $order_goods['virtualsendcontent'], 'status' => '3', 'paytime' => $time, 'sendtime' => $time, 'finishtime' => $time), array('id' => $orderid));
		$this->setStocksAndCredits($orderid, 1);
		m('member')->upgradeLevel($order['openid']);
		m('order')->setGiveBalance($orderid, 1);
		if (com('coupon') && !empty($order['couponid'])) {
			com('coupon')->backConsumeCoupon($order['id']);
		}

		m('notice')->sendOrderMessage($orderid);

		if (p('commission')) {
			p('commission')->checkOrderPay($order['id']);
			p('commission')->checkOrderFinish($order['id']);
		}

		return true;
	}

	public function getGoodsCredit($goods)
	{
		global $_W;
		$credits = 0;

		foreach ($goods as $g) {
			$gcredit = trim($g['credit']);

			if (!empty($gcredit)) {
				if (strexists($gcredit, '%')) {
					$credits += intval((floatval(str_replace('%', '', $gcredit)) / 100) * $g['realprice']);
				}
				else {
					$credits += intval($g['credit']) * $g['total'];
				}
			}
		}

		return $credits;
	}

	public function setDeductCredit2($order)
	{
		global $_W;

		if (0 < $order['deductcredit2']) {
			m('member')->setCredit($order['openid'], 'credit2', $order['deductcredit2'], array('0', $_W['shopset']['shop']['name'] . '购物返还抵扣余额 余额: ' . $order['deductcredit2'] . ' 订单号: ' . $order['ordersn']));
		}
	}

	public function setGiveBalance($orderid = '', $type = 0)
	{
		global $_W;
		$order = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
		$goods = pdo_fetchall('select og.goodsid,og.total,g.totalcnf,og.realprice,g.money,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$balance = 0;

		foreach ($goods as $g) {
			$gbalance = trim($g['money']);

			if (!empty($gbalance)) {
				if (strexists($gbalance, '%')) {
					$balance += intval((floatval(str_replace('%', '', $gbalance)) / 100) * $g['realprice']);
				}
				else {
					$balance += intval($g['money']) * $g['total'];
				}
			}
		}

		if (0 < $balance) {
			$shopset = m('common')->getSysset('shop');

			if ($type == 1) {
				if ($order['status'] == 3) {
					m('member')->setCredit($order['openid'], 'credit2', $balance, array(0, $shopset['name'] . '购物赠送余额 订单号: ' . $order['ordersn']));
					return NULL;
				}
			}
			else {
				if ($type == 2) {
					if (1 <= $order['status']) {
						m('member')->setCredit($order['openid'], 'credit2', 0 - $balance, array(0, $shopset['name'] . '购物取消订单扣除赠送余额 订单号: ' . $order['ordersn']));
					}
				}
			}
		}
	}

	public function setStocksAndCredits($orderid = '', $type = 0)
	{
		global $_W;
		$order = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
		$goods = pdo_fetchall('select og.goodsid,og.total,g.totalcnf,og.realprice,g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$credits = 0;

		foreach ($goods as $g) {
			$stocktype = 0;

			if ($type == 0) {
				if ($g['totalcnf'] == 0) {
					$stocktype = -1;
				}
			}
			else if ($type == 1) {
				if ($g['totalcnf'] == 1) {
					$stocktype = -1;
				}
			}
			else {
				if ($type == 2) {
					if (1 <= $order['status']) {
						if ($g['totalcnf'] == 1) {
							$stocktype = 1;
						}
					}
					else {
						if ($g['totalcnf'] == 0) {
							$stocktype = 1;
						}
					}
				}
			}

			if (!empty($stocktype)) {
				if (!empty($g['optionid'])) {
					$option = m('goods')->getOption($g['goodsid'], $g['optionid']);
					if (!empty($option) && ($option['stock'] != -1)) {
						$stock = -1;

						if ($stocktype == 1) {
							$stock = $option['stock'] + $g['total'];
						}
						else {
							if ($stocktype == -1) {
								$stock = $option['stock'] - $g['total'];
								($stock <= 0) && ($stock = 0);
							}
						}

						if ($stock != -1) {
							pdo_update('ewei_shop_goods_option', array('stock' => $stock), array('uniacid' => $_W['uniacid'], 'goodsid' => $g['goodsid'], 'id' => $g['optionid']));
						}
					}
				}

				if (!empty($g['goodstotal']) && ($g['goodstotal'] != -1)) {
					$totalstock = -1;

					if ($stocktype == 1) {
						$totalstock = $g['goodstotal'] + $g['total'];
					}
					else {
						if ($stocktype == -1) {
							$totalstock = $g['goodstotal'] - $g['total'];
							($totalstock <= 0) && ($totalstock = 0);
						}
					}

					if ($totalstock != -1) {
						pdo_update('ewei_shop_goods', array('total' => $totalstock), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
					}
				}
			}

			$gcredit = trim($g['credit']);

			if (!empty($gcredit)) {
				if (strexists($gcredit, '%')) {
					$credits += intval((floatval(str_replace('%', '', $gcredit)) / 100) * $g['realprice']);
				}
				else {
					$credits += intval($g['credit']) * $g['total'];
				}
			}

			if ($type == 0) {
				if ($g['totalcnf'] != 1) {
					pdo_update('ewei_shop_goods', array('sales' => $g['sales'] + $g['total']), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
				}
			}
			else {
				if ($type == 1) {
					if (1 <= $order['status']) {
						$salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $g['goodsid'], ':uniacid' => $_W['uniacid']));
						pdo_update('ewei_shop_goods', array('salesreal' => $salesreal), array('id' => $g['goodsid']));
					}
				}
			}
		}

		if (0 < $credits) {
			$shopset = m('common')->getSysset('shop');

			if ($type == 1) {
				m('member')->setCredit($order['openid'], 'credit1', $credits, array(0, $shopset['name'] . '购物积分 订单号: ' . $order['ordersn']));
				return NULL;
			}

			if ($type == 2) {
				if (1 <= $order['status']) {
					m('member')->setCredit($order['openid'], 'credit1', 0 - $credits, array(0, $shopset['name'] . '购物取消订单扣除积分 订单号: ' . $order['ordersn']));
				}
			}
		}
	}

	public function getTotals()
	{
		global $_W;
		$paras = array(':uniacid' => $_W['uniacid']);
		$sqlcondition = ' left join ' . tablename('ewei_shop_order_refund') . ' r on r.id =o.refundid ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('ewei_shop_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('ewei_shop_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . ' left join ' . tablename('ewei_shop_member') . ' sm on sm.openid = s.openid and sm.uniacid=s.uniacid';
		$totals['all'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid and o.deleted=0', $paras);
		$totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid and o.status=-1 and o.refundtime=0', $paras);
		$totals['status0'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid  and o.status=0 and o.paytype<>3', $paras);
		$totals['status1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid  and ( o.status=1 or ( o.status=0 and o.paytype=3) )', $paras);
		$totals['status2'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid  and o.status=2', $paras);
		$totals['status3'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid  and o.status=3', $paras);
		$totals['status4'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid  and o.refundstate>0 and o.refundid<>0', $paras);
		$totals['status5'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order') . ' o ' . $sqlcondition . ' WHERE o.uniacid = :uniacid  and o.refundtime<>0', $paras);
		return $totals;
	}

	public function getFormartDiscountPrice($isd, $gprice, $gtotal = 1)
	{
		if (!empty($isd)) {
			if (strexists($isd, '%')) {
				$dd = floatval(str_replace('%', '', $isd));
				if ((0 < $dd) && ($dd < 100)) {
					$price = round(($dd / 100) * $gprice, 2);
				}
			}
			else {
				if (0 < floatval($isd)) {
					$price = round(floatval($isd * $gtotal), 2);
				}
			}
		}

		return $price;
	}

	public function getGoodsDiscountPrice($g, $level)
	{
		$gprice = $g['marketprice'] * $g['total'];
		$price = $gprice;
		$discountprice = 0;
		$isdiscountprice = 0;
		$isd = false;
		@$isdiscount_discounts = json_decode($g['isdiscount_discounts'], true);
		if ($g['isdiscount'] && (time() <= $g['isdiscount_time'])) {
			if (is_array($isdiscount_discounts)) {
				$key = (!empty($level['id']) ? 'level' . $level['id'] : 'default');
				if (!isset($isdiscount_discounts['type']) || empty($isdiscount_discounts['type'])) {
					$isd = trim($isdiscount_discounts[$key]['option0']);
					$price = $this->getFormartDiscountPrice($isd, $gprice, $g['total']);
				}
				else {
					$isd = trim($isdiscount_discounts[$key]['option' . $g['optionid']]);
					$price = $this->getFormartDiscountPrice($isd, $gprice, $g['total']);
				}
			}

			$isdiscountprice = abs($price - $gprice);
		}
		else {
			if (empty($g['isnodiscount'])) {
				$discounts = json_decode($g['discounts'], true);

				if (is_array($discounts)) {
					$key = (!empty($level['id']) ? 'level' . $level['id'] : 'default');
					if (!isset($discounts['type']) || empty($discounts['type'])) {
						$dd = floatval($discounts[$key]);
						$md = floatval($level['discount']);
						if ((0 < $dd) && ($dd < 10)) {
							$price = round(($dd / 10) * $gprice, 2);
						}
						else {
							if ((0 < $md) && ($md < 10)) {
								$price = round(($md / 10) * $gprice, 2);
							}
						}
					}
					else {
						$isd = trim($discounts[$key]['option' . $g['optionid']]);

						if (!empty($isd)) {
							if (strexists($isd, '%')) {
								$dd = floatval(str_replace('%', '', $isd));
								if ((0 < $dd) && ($dd < 100)) {
									$price = round(($dd / 100) * $gprice, 2);
								}
							}
							else {
								if (0 < floatval($isd)) {
									$price = round(floatval($isd), 2);
								}
							}
						}
					}
				}

				$discountprice = abs($price - $gprice);
			}
		}

		return array('price' => $price, 'isdiscountprice' => $isdiscountprice, 'discountprice' => $discountprice);
	}

	public function getOrderDispatchPrice($goods, $member, $address, $saleset = false, $t)
	{
		$dispatch_price = 0;
		$realprice = 0;

		foreach ($goods as $g) {
			$realprice += $g['ggprice'];
		}

		$dispatch_array = array();
		$total_array = array();
		$totalprice_array = array();

		foreach ($goods as $g) {
			$total_array[$g['goodsid']] += $g['total'];
			$totalprice_array[$g['goodsid']] += $g['ggprice'];
		}

		foreach ($goods as $g) {
			$sendfree = false;

			if (!empty($g['issendfree'])) {
				$sendfree = true;
			}
			else {
				if (($g['ednum'] <= $total_array[$g['goodsid']]) && (0 < $g['ednum'])) {
					$gareas = explode(';', $g['edareas']);

					if (empty($gareas)) {
						$sendfree = true;
					}
					else if (!empty($address)) {
						if (!in_array($address['city'], $gareas)) {
							$sendfree = true;
						}
					}
					else if (!empty($member['city'])) {
						if (!in_array($member['city'], $gareas)) {
							$sendfree = true;
						}
					}
					else {
						$sendfree = true;
					}
				}

				if ((floatval($g['edmoney']) <= $totalprice_array[$g['goodsid']]) && (0 < floatval($g['edmoney']))) {
					$gareas = explode(';', $g['edareas']);

					if (empty($gareas)) {
						$sendfree = true;
					}
					else if (!empty($address)) {
						if (!in_array($address['city'], $gareas)) {
							$sendfree = true;
						}
					}
					else if (!empty($member['city'])) {
						if (!in_array($member['city'], $gareas)) {
							$sendfree = true;
						}
					}
					else {
						$sendfree = true;
					}
				}
			}

			if (!$sendfree) {
				if ($g['dispatchtype'] == 1) {
					if (0 < $g['dispatchprice']) {
						$dispatch_price += $g['dispatchprice'];
					}
				}
				else {
					if ($g['dispatchtype'] == 0) {
						if (empty($g['dispatchid'])) {
							$dispatch_data = m('dispatch')->getDefaultDispatch();
						}
						else {
							$dispatch_data = m('dispatch')->getOneDispatch($g['dispatchid']);
						}

						if (empty($dispatch_data)) {
							$dispatch_data = m('dispatch')->getNewDispatch();
						}

						if (!empty($dispatch_data)) {
							$areas = unserialize($dispatch_data['areas']);

							if ($dispatch_data['calculatetype'] == 1) {
								$param = $g['total'];
							}
							else {
								$param = $g['weight'] * $g['total'];
							}

							$dkey = $dispatch_data['id'];

							if (array_key_exists($dkey, $dispatch_array)) {
								$dispatch_array[$dkey]['param'] += $param;
							}
							else {
								$dispatch_array[$dkey]['data'] = $dispatch_data;
								$dispatch_array[$dkey]['param'] = $param;
							}
						}
					}
				}
			}
		}

		if (!empty($dispatch_array)) {
			foreach ($dispatch_array as $k => $v) {
				$dispatch_data = $dispatch_array[$k]['data'];
				$param = $dispatch_array[$k]['param'];
				$areas = unserialize($dispatch_data['areas']);

				if (!empty($address)) {
					$dispatch_price += m('dispatch')->getCityDispatchPrice($areas, $address['city'], $param, $dispatch_data);
				}
				else if (!empty($member['city'])) {
					$dispatch_price = 0 + m('dispatch')->getCityDispatchPrice($areas, $member['city'], $param, $dispatch_data);
				}
				else {
					$dispatch_price = 0 + m('dispatch')->getDispatchPrice($param, $dispatch_data);
				}
			}
		}

		if ($saleset) {
			if (!empty($saleset['enoughfree'])) {
				if (floatval($saleset['enoughorder']) <= 0) {
					$dispatch_price = 0;
				}
				else {
					if (floatval($saleset['enoughorder']) <= $realprice) {
						if (empty($saleset['enoughareas'])) {
							$dispatch_price = 0;
						}
						else {
							$areas = explode(';', $saleset['enoughareas']);

							if (!empty($address)) {
								if (!in_array($address['city'], $areas)) {
									$dispatch_price = 0;
								}
							}
							else if (!empty($member['city'])) {
								if (!in_array($member['city'], $areas)) {
									$dispatch_price = 0;
								}
							}
							else {
								if (empty($member['city'])) {
									$dispatch_price = 0;
								}
							}
						}
					}
				}
			}
		}

		return $dispatch_price;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
