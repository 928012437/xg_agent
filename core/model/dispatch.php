<?php

class Dispatch_EweiShopV2Model
{
	public function getDispatchPrice($param, $d, $calculatetype = -1)
	{
		if (empty($d)) {
			return 0;
		}

		$price = 0;

		if ($calculatetype == -1) {
			$calculatetype = $d['calculatetype'];
		}

		if ($calculatetype == 1) {
			if ($param <= $d['firstnum']) {
				$price = floatval($d['firstnumprice']);
			}
			else {
				$price = floatval($d['firstnumprice']);
				$secondweight = $param - floatval($d['firstnum']);
				$dsecondweight = (floatval($d['secondnum']) <= 0 ? 1 : floatval($d['secondnum']));
				$secondprice = 0;

				if (($secondweight % $dsecondweight) == 0) {
					$secondprice = ($secondweight / $dsecondweight) * floatval($d['secondnumprice']);
				}
				else {
					$secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($d['secondnumprice']);
				}

				$price += $secondprice;
			}
		}
		else if ($param <= $d['firstweight']) {
			$price = floatval($d['firstprice']);
		}
		else {
			$price = floatval($d['firstprice']);
			$secondweight = $param - floatval($d['firstweight']);
			$dsecondweight = (floatval($d['secondweight']) <= 0 ? 1 : floatval($d['secondweight']));
			$secondprice = 0;

			if (($secondweight % $dsecondweight) == 0) {
				$secondprice = ($secondweight / $dsecondweight) * floatval($d['secondprice']);
			}
			else {
				$secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($d['secondprice']);
			}

			$price += $secondprice;
		}

		return $price;
	}

	public function getCityDispatchPrice($areas, $city, $param, $d)
	{
		if (is_array($areas) && (0 < count($areas))) {
			foreach ($areas as $area) {
				$citys = explode(';', $area['citys']);
				if (in_array($city, $citys) && !empty($citys)) {
					return $this->getDispatchPrice($param, $area, $d['calculatetype']);
				}
			}
		}

		return $this->getDispatchPrice($param, $d);
	}

	public function getDefaultDispatch()
	{
		global $_W;
		$sql = 'select * from ' . tablename('ewei_shop_dispatch') . ' where isdefault=1 and uniacid=:uniacid and enabled=1 Limit 1';
		$params = array(':uniacid' => $_W['uniacid']);
		$data = pdo_fetch($sql, $params);
		return $data;
	}

	public function getNewDispatch()
	{
		global $_W;
		$sql = 'select * from ' . tablename('ewei_shop_dispatch') . ' where uniacid=:uniacid and enabled=1 order by id desc Limit 1';
		$params = array(':uniacid' => $_W['uniacid']);
		$data = pdo_fetch($sql, $params);
		return $data;
	}

	public function getOneDispatch($id)
	{
		global $_W;
		$params = array(':uniacid' => $_W['uniacid']);

		if ($id == 0) {
			$sql = 'select * from ' . tablename('ewei_shop_dispatch') . ' where isdefault=1 and uniacid=:uniacid and enabled=1 Limit 1';
		}
		else {
			$sql = 'select * from ' . tablename('ewei_shop_dispatch') . ' where id=:id and uniacid=:uniacid and enabled=1 Limit 1';
			$params[':id'] = $id;
		}

		$data = pdo_fetch($sql, $params);
		return $data;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
