<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends ComWebPage
{
	public function __construct()
	{
		if (!com('perm')->check_com('sale')) {
			if (cv('sale.coupon')) {
				header('location: ' . webUrl('sale/coupon'));
				return NULL;
			}

			$this->message('你没有相应的权限查看');
		}
	}

	public function main()
	{
		if (cv('sale.deduct')) {
			header('location: ' . webUrl('sale/deduct'));
			return NULL;
		}

		if (cv('sale.enough')) {
			header('location: ' . webUrl('sale/enough'));
			return NULL;
		}

		if (cv('sale.enoughfree')) {
			header('location: ' . webUrl('sale/enoughfree'));
			return NULL;
		}

		if (cv('sale.recharge')) {
			header('location: ' . webUrl('sale/recharge'));
			return NULL;
		}

		if (cv('sale.coupon')) {
			header('location: ' . webUrl('sale/coupon'));
			return NULL;
		}

		header('location: ' . webUrl());
	}

	public function deduct()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$post = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['creditdeduct'] = intval($post['creditdeduct']);
			$data['credit'] = 1;
			$data['money'] = round(floatval($post['money']), 2);
			$data['moneydeduct'] = intval($post['moneydeduct']);
			$data['dispatchnodeduct'] = intval($post['dispatchnodeduct']);
			plog('sale.deduct', '修改抵扣设置');
			m('common')->updatePluginset(array('sale' => $data));
			show_json(1);
		}

		$data = m('common')->getPluginset('sale');
		load()->func('tpl');
		include $this->template('sale/index');
	}

	public function enough()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['enoughmoney'] = round(floatval($data['enoughmoney']), 2);
			$data['enoughdeduct'] = round(floatval($data['enoughdeduct']), 2);
			$enoughs = array();
			$postenoughs = (is_array($_GPC['enough']) ? $_GPC['enough'] : array());

			foreach ($postenoughs as $key => $value) {
				$enough = floatval($value);

				if (0 < $enough) {
					$enoughs[] = array('enough' => floatval($_GPC['enough'][$key]), 'give' => floatval($_GPC['give'][$key]));
				}
			}

			$data['enoughs'] = $enoughs;
			plog('sale.enough', '修改满额立减优惠');
			m('common')->updatePluginset(array('sale' => $data));
			show_json(1);
		}

		$areas = m('common')->getAreas();
		$data = m('common')->getPluginset('sale');
		load()->func('tpl');
		include $this->template();
	}

	public function enoughfree()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['enoughfree'] = intval($data['enoughfree']);
			$data['enoughorder'] = round(floatval($data['enoughorder']), 2);
			plog('sale.enough', '修改满额包邮优惠');
			m('common')->updatePluginset(array('sale' => $data));
			show_json(1);
		}

		$data = m('common')->getPluginset('sale');
		$areas = m('common')->getAreas();
		include $this->template();
	}

	public function recharge()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$recharges = array();
			$datas = (is_array($_GPC['enough']) ? $_GPC['enough'] : array());

			foreach ($datas as $key => $value) {
				$enough = trim($value);

				if (!empty($enough)) {
					$recharges[] = array('enough' => trim($_GPC['enough'][$key]), 'give' => trim($_GPC['give'][$key]));
				}
			}

			$data['recharges'] = iserializer($recharges);
			m('common')->updatePluginset(array('sale' => $data));
			plog('sale.recharge', '修改充值优惠设置');
			show_json(1);
		}

		$data = m('common')->getPluginset('sale');
		$recharges = iunserializer($data['recharges']);
		include $this->template();
	}
}

?>
