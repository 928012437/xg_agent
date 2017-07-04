<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Selecturl_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$full = intval($_GPC['full']);
		$syscate = m('common')->getSysset('category');

		if (0 < $syscate['level']) {
			$categorys = pdo_fetchall('SELECT id,name,parentid FROM ' . tablename('ewei_shop_category') . ' WHERE enabled=:enabled and uniacid= :uniacid  ', array(':uniacid' => $_W['uniacid'], ':enabled' => '1'));
		}

		include $this->template();
	}

	public function querygoods()
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);
		$kw = trim($_GPC['kw']);
		$full = intval($_GPC['full']);
		if (!empty($kw) && !empty($type)) {
			if ($type == 'good') {
				$goods = pdo_fetchall('SELECT id,title,productprice,marketprice,thumb,sales,unit FROM ' . tablename('ewei_shop_goods') . ' WHERE uniacid= :uniacid and status=:status and deleted=0 AND title LIKE :title ', array(':title' => '%' . $kw . '%', ':uniacid' => $_W['uniacid'], ':status' => '1'));
				$goods = set_medias($goods, 'thumb');
			}
			else if ($type == 'article') {
				$articles = pdo_fetchall('select id,article_title from ' . tablename('ewei_shop_article') . ' where article_title LIKE :title and article_state=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
			}
			else {
				if ($type == 'coupon') {
					$coupons = pdo_fetchall('select id,couponname,coupontype from ' . tablename('ewei_shop_coupon') . ' where couponname LIKE :title and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
				}
			}
		}

		include $this->template('util/selecturl_tpl');
	}
}

?>
