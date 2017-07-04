<?php

class Goods_XgAgentModel
{
	public function getOption($goodsid = 0, $optionid = 0)
	{
		global $_W;
		return pdo_fetch('select * from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid Limit 1', array(':id' => $optionid, ':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid));
	}

	public function getOptionPirce($goodsid = 0, $optionid = 0)
	{
		global $_W;
		return pdo_fetchcolumn('select marketprice from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid', array(':id' => $optionid, ':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid));
	}

	public function getList($args = array())
	{
		global $_W;
		$openid = $_W['openid'];
		$page = (!empty($args['page']) ? intval($args['page']) : 1);
		$pagesize = (!empty($args['pagesize']) ? intval($args['pagesize']) : 10);
		$random = (!empty($args['random']) ? $args['random'] : false);
		$order = (!empty($args['order']) ? $args['order'] : ' displayorder desc,createtime desc');
		$orderby = (empty($args['order']) ? '' : (!empty($args['by']) ? $args['by'] : ''));
		$condition = ' and `uniacid` = :uniacid AND `deleted` = 0 and status=1';
		$params = array(':uniacid' => $_W['uniacid']);
		$ids = (!empty($args['ids']) ? trim($args['ids']) : '');

		if (!empty($ids)) {
			$condition .= ' and id in ( ' . $ids . ')';
		}

		$isnew = (!empty($args['isnew']) ? 1 : 0);

		if (!empty($isnew)) {
			$condition .= ' and isnew=1';
		}

		$ishot = (!empty($args['ishot']) ? 1 : 0);

		if (!empty($ishot)) {
			$condition .= ' and ishot=1';
		}

		$isrecommand = (!empty($args['isrecommand']) ? 1 : 0);

		if (!empty($isrecommand)) {
			$condition .= ' and isrecommand=1';
		}

		$isdiscount = (!empty($args['isdiscount']) ? 1 : 0);

		if (!empty($isdiscount)) {
			$condition .= ' and isdiscount=1';
		}

		$issendfree = (!empty($args['issendfree']) ? 1 : 0);

		if (!empty($issendfree)) {
			$condition .= ' and issendfree=1';
		}

		$istime = (!empty($args['istime']) ? 1 : 0);

		if (!empty($istime)) {
			$condition .= ' and istime=1 and ' . time() . '>=timestart and ' . time() . '<=timeend';
		}

		if (isset($args['nocommission'])) {
			$condition .= ' AND `nocommission`=' . intval($args['nocommission']);
		}

		$keywords = (!empty($args['keywords']) ? $args['keywords'] : '');

		if (!empty($keywords)) {
			$condition .= ' AND `title` LIKE :title';
			$params[':title'] = '%' . trim($keywords) . '%';
		}

		if (!empty($args['cate'])) {
			$condition .= ' AND ( FIND_IN_SET(' . $args['cate'] . ',cates)<>0 )';
		}

		$member = m('member')->getMember($openid);

		if (!empty($member)) {
			$levelid = intval($member['level']);
			$groupid = intval($member['groupid']);
			$condition .= ' and ( ifnull(showlevels,\'\')=\'\' or FIND_IN_SET( ' . $levelid . ',showlevels)<>0 ) ';
			$condition .= ' and ( ifnull(showgroups,\'\')=\'\' or FIND_IN_SET( ' . $groupid . ',showgroups)<>0 ) ';
		}
		else {
			$condition .= ' and ifnull(showlevels,\'\')=\'\' ';
			$condition .= ' and   ifnull(showgroups,\'\')=\'\' ';
		}

		$total = '';

		if (!$random) {
			$sql = 'SELECT id,title,thumb,marketprice,productprice,minprice,sales,total,description FROM ' . tablename('ewei_shop_goods') . ' where 1 ' . $condition . ' ORDER BY ' . $order . ' ' . $orderby . ' LIMIT ' . (($page - 1) * $pagesize) . ',' . $pagesize;
			$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_goods') . ' where 1 ' . $condition . ' ', $params);
		}
		else {
			$sql = 'SELECT id,title,thumb,marketprice,productprice,minprice,sales,total,description FROM ' . tablename('ewei_shop_goods') . ' where 1 ' . $condition . ' ORDER BY rand() LIMIT ' . $pagesize;
			$total = $pagesize;
		}

		$list = pdo_fetchall($sql, $params);
		$list = set_medias($list, 'thumb');
		return array('list' => $list, 'total' => $total);
	}

	public function getTotals()
	{
		global $_W;
		return array('sale' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where status=1 and deleted=0 and total>0 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'])), 'out' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where status=1 and deleted=0 and total=0 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'])), 'stock' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where status=0 and deleted=0 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'])), 'cycle' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where deleted=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'])));
	}

	public function getComments($goodsid = '0', $args = array())
	{
		global $_W;
		$page = (!empty($args['page']) ? intval($args['page']) : 1);
		$pagesize = (!empty($args['pagesize']) ? intval($args['pagesize']) : 10);
		$condition = ' and `uniacid` = :uniacid AND `goodsid` = :goodsid and deleted=0';
		$params = array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid);
		$sql = 'SELECT id,nickname,headimgurl,content,images FROM ' . tablename('ewei_shop_goods_comment') . ' where 1 ' . $condition . ' ORDER BY createtime desc LIMIT ' . (($page - 1) * $pagesize) . ',' . $pagesize;
		$list = pdo_fetchall($sql, $params);

		foreach ($list as &$row) {
			$row['images'] = set_medias(unserialize($row['images']));
		}

		unset($row);
		return $list;
	}

	public function isFavorite($id = '')
	{
		global $_W;
		$count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_favorite') . ' where goodsid=:goodsid and deleted=0 and openid=:openid and uniacid=:uniacid limit 1', array(':goodsid' => $id, ':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']));
		return 0 < $count;
	}

	public function addHistory($goodsid = 0)
	{
		global $_W;
		pdo_query('update ' . tablename('ewei_shop_goods') . ' set viewcount=viewcount+1 where id=:id and uniacid=\'' . $_W[uniacid] . '\' ', array(':id' => $goodsid));
		$history = pdo_fetch('select id,times from ' . tablename('ewei_shop_member_history') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid limit 1', array(':goodsid' => $goodsid, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));

		if (empty($history)) {
			$history = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'goodsid' => $goodsid, 'deleted' => 0, 'createtime' => time(), 'times' => 1);
			pdo_insert('ewei_shop_member_history', $history);
			return NULL;
		}

		pdo_update('ewei_shop_member_history', array('deleted' => 0, 'times' => $history['times'] + 1), array('id' => $history['id']));
	}

	public function getCartCount()
	{
		global $_W;
		global $_GPC;
		$count = pdo_fetchcolumn('select sum(total) from ' . tablename('ewei_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 limit 1', array(':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']));
		return $count;
	}

	public function getSpecThumb($specs)
	{
		global $_W;
		$thumb = '';
		$cartspecs = explode('_', $specs);
		$specid = $cartspecs[0];

		if (!empty($specid)) {
			$spec = pdo_fetch('select thumb from ' . tablename('ewei_shop_goods_spec_item') . ' ' . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $specid, ':uniacid' => $_W['uniacid']));

			if (!empty($spec)) {
				if (!empty($spec['thumb'])) {
					$thumb = $spec['thumb'];
				}
			}
		}

		return $thumb;
	}

	public function getOptionThumb($goodsid = 0, $optionid = 0)
	{
		global $_W;
		$thumb = '';
		$option = $this->getOption($goodsid, $optionid);

		if (!empty($option)) {
			$specs = $option['specs'];
			$thumb = $this->getSpecThumb($specs);
		}

		return $thumb;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
