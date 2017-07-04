<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends WebPage
{

	public function adv()
	{
		global $_W;
		$uniacid = $_W['uniacid'];
		$list = pdo_getall('xg_agent_adv', array('uniacid' => $uniacid));
		include $this->template();
	}

	public function advpost()
	{
		global $_W, $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if ($_W['ispost']) {
			$data = array(
				'uniacid' => $uniacid,
				'url' => $_GPC['url'],
				'img' => $_GPC['img'],
				'title' => $_GPC['title'],
				'displayorder' => $_GPC['displayorder'],
				'status' => $_GPC['status']
			);
			if (!empty($id)) {
				plog('goods.adv.edit', "修改幻灯片，幻灯片ID:" . $id);
				pdo_update('xg_agent_adv', $data, array('id' => $id));
			} else {
				plog('goods.adv.add', "新增幻灯片。");
				pdo_insert('xg_agent_adv', $data);
			}
			show_json(1, array('url' => webUrl('goods/adv')));
		}
		if (!empty($id)) {
			$adv = pdo_get('xg_agent_adv', array('id' => $id));
		}

		include $this->template();
	}

	public function deladv()
	{
		global $_GPC;
		$id = $_GPC['id'];
		plog('goods.adv.delete', "删除幻灯片，幻灯片ID:" . $id);
		pdo_delete('xg_agent_adv', array('id' => $id));
		show_json(1);
	}


	public function loupan()
	{
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 25;
		$condition = ' WHERE uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['sort'])) {
			$sort = $_GPC['sort'];
			$condition .= " and title like '%" . $sort . "%'";
		}
		if ($_GPC['search_p'] != '请选择省份..' && $_GPC['search_p'] != '') {
			$condition .= " and location_p = '" . $_GPC['search_p'] . "'";
		}
		if ($_GPC['search_c'] != '请选择市/县..' && $_GPC['search_c'] != '') {
			$condition .= " and location_c = '" . $_GPC['search_c'] . "'";
		}
		if ($_GPC['search_a'] != '请选择区..' && $_GPC['search_a'] != '') {
			$condition .= " and location_a = '" . $_GPC['search_a'] . "'";
		}
		$list = pdo_fetchall("select * from " . tablename('xg_agent_loupan') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_loupan') . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		if (!empty($list)) {
			foreach ($list as &$row) {
				$row['total'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('xg_agent_photo') . " WHERE lpid = :lpid", array(':lpid' => $row['id']));
			}
		}
		unset($row);

		include $this->template();
	}

	public function loupan_sence()
	{
		global $_GPC, $_W;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['lpid']);
		$loupan = pdo_fetch("SELECT id, type FROM " . tablename('xg_agent_loupan') . " WHERE id = :id", array(':id' => $id));
		if (empty($loupan)) {
			show_json(0, "产品不存在或是已经被删除！");
		}
		if (checksubmit('submit')) {
			if (!empty($_GPC['item'])) {
				if (!empty($_GPC['id'])) {
					$data = array(
						'uniacid' => $uniacid,
						'lpid' => $id,
						'photoid' => intval($_GPC['photoid']),
						'type' => $_GPC['type'],
						'item' => $_GPC['item'],
						'url' => $_GPC['url'],
						'x' => $_GPC['x'],
						'y' => $_GPC['y'],
						'animation' => $_GPC['animation'],
					);
					pdo_update('xg_agent_item', $data, array('id' => $_GPC['id']));
				} else {
					$data = array(
						'uniacid' => $uniacid,
						'lpid' => $id,
						'photoid' => intval($_GPC['photoid']),
						'type' => $_GPC['type'],
						'item' => $_GPC['item'],
						'url' => $_GPC['url'],
						'x' => $_GPC['x'],
						'y' => $_GPC['y'],
						'animation' => $_GPC['animation'],
					);
					pdo_insert('xg_agent_item', $data);
				}
			}
			if (!empty($_GPC['attachment-new'])) {
				foreach ($_GPC['attachment-new'] as $index => $row) {
					if (empty($row)) {
						continue;
					}
					$data = array(
						'uniacid' => $uniacid,
						'lpid' => $id,
						'title' => $_GPC['title-new'][$index],
						'url' => $_GPC['url-new'][$index],
						'attachment' => $_GPC['attachment-new'][$index],
						'displayorder' => $_GPC['displayorder-new'][$index],
					);
					plog('goods.photo.add','添加场景，项目ID:'.$id);
					pdo_insert('xg_agent_photo', $data);
				}
			}
			if (!empty($_GPC['attachment'])) {
				foreach ($_GPC['attachment'] as $index => $row) {
					if (empty($row)) {
						continue;
					}
					$data = array(
						'uniacid' => $uniacid,
						'lpid' => $id,
						'title' => $_GPC['title'][$index],
						'url' => $_GPC['url'][$index],
						'attachment' => $_GPC['attachment'][$index],
						'displayorder' => $_GPC['displayorder'][$index],
					);
					plog('goods.photo.edit','修改场景，项目ID:'.$id);
					pdo_update('xg_agent_photo', $data, array('id' => $index));
				}
			}
			show_json(1,array('url'=>webUrl('goods.loupan')));
		}
		$photos = pdo_fetchall("SELECT * FROM " . tablename('xg_agent_photo') . " WHERE lpid = :lpid ORDER BY displayorder DESC", array(':lpid' => $loupan['id']));
		foreach ($photos as &$photo1) {
			$photo1['items'] = pdo_fetchall("SELECT * FROM " . tablename('xg_agent_item') . " WHERE photoid = :photoid", array(':photoid' => $photo1['id']));
		}

		include $this->template();
	}

	public function photo_delete()
	{
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM " . tablename('xg_agent_photo') . " WHERE id = :id", array(':id' => $id));
		if (empty($item)) {
			show_message('图片不存在或是已经被删除!');
		}
		plog('goods.photo.delete','删除场景，项目ID:'.$id);
		pdo_delete('xg_agent_photo', array('id' => $id));
		header("location:" . webUrl('goods/loupan_sence',array('lpid'=>intval($_GPC['lpid']))));
	}

	public function item()
	{
		global $_W, $_GPC;
		$uniacid = $_W['uniacid'];
		checklogin();
		$lpid = intval($_GPC['lpid']);
		$photoid = intval($_GPC['photoid']);
		$id = intval($_GPC['id']);
		$photo = pdo_fetch("SELECT * FROM " . tablename('xg_agent_photo') . " WHERE id = :id", array(':id' => $photoid));
		if (empty($photo)) {
			show_message('场景不存在或是已经被删除!');
		}
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename('xg_agent_item') . " WHERE id = :id", array(':id' => $id));
		}
		include $this->template();
	}

	public function loupan_post()
	{
		global $_GPC, $_W;
		$id = $_GPC['id'];
		$uniacid = $_W['uniacid'];
		if ($_W['ispost']) {

			if (empty($_GPC['title'])) {
				show_json(0, "请填写必填项！");
			}
			$idviews = $_GPC['idviews'];
			$id_view = '';
			if (!empty($idviews)) {
				foreach ($idviews as $i) {
					$id_view = $id_view . $i . ',';
				}
				$id_view = ',' . $id_view;
			}
			$data = array(
				'uniacid' => $uniacid,
				'title' => $_GPC['title'],
				'thumb' => $_GPC['thumb'],
				'music' => $_GPC['music'],
				'sh_title' => $_GPC['sh_title'],
				'sh_cont' => $_GPC['sh_cont'],
				'icon' => $_GPC['icon'],
				'share' => $_GPC['share'],
				'content' => $_GPC['content'],
				'tel' => $_GPC['tel'],
				'location_p' => $_GPC['location_p'],
				'location_c' => $_GPC['location_c'],
				'location_a' => $_GPC['location_a'],
				'addr' => $_GPC['addr'],
				'lng' => $_GPC['lng'],
				'lat' => $_GPC['lat'],
				'starttime' => strtotime($_GPC['starttime']),
				'endtime' => strtotime($_GPC['endtime']),
				'l_mianji' => $_GPC['l_mianji'],
				'l_wuye' => $_GPC['l_wuye'],
				'l_biaozhun' => $_GPC['l_biaozhun'],
				'l_hushu' => $_GPC['l_hushu'],
				'is_farme' => $_GPC['is_farme'],
				'frame' => $_GPC['frame'],
				'l_guize' => htmlspecialchars_decode($_GPC['l_guize']),
				'l_xcyh' => $_GPC['l_xcyh'],
				'l_lpmd' => htmlspecialchars_decode($_GPC['l_lpmd']),
				'l_mbkh' => $_GPC['l_mbkh'],
				'jw_addr' => $_GPC['jw_addr'],
				'displayorder' => intval($_GPC['displayorder']),
				'price' => $_GPC['price'],
				'price_type' => $_GPC['price_type'],
				'recnum' => intval($_GPC['recnum']),
				'sucnum' => intval($_GPC['sucnum']),
				'isview' => intval($_GPC['isview']),
				'iscity' => intval($_GPC['iscity']),
//				'isautoallot' => intval($_GPC['isautoallot']),
				'type' => intval($_GPC['type']),
				'typekind' => intval($_GPC['typekind']),
				'l_youhui' => $_GPC['l_youhui'],
				'l_biaoqian' => $_GPC['l_biaoqian'],
				'id_view' => $id_view,
				'createtime' => TIMESTAMP,
				'commis' => $_GPC['commis'],
			);
			if ($_GPC['mset'][1]) {
				$data['mloop'] = 1;
			} else {
				$data['mloop'] = 0;
			}
			if ($id == '') {
				plog('goods.add','新增项目');
				pdo_insert('xg_agent_loupan', $data);
			} else {
				plog('goods.edit','修改项目，项目ID:'.$id);
				pdo_update('xg_agent_loupan', $data, array('id' => $id));
			}

			show_json(1, array('url' => webUrl('goods/loupan')));
		}
		$item = pdo_get('xg_agent_loupan', array('uniacid' => $uniacid, 'id' => $id));
		$idens=pdo_getall('xg_agent_identity',array('uniacid'=>$uniacid));
		$idviews = explode(',', $item['id_view']);

		include $this->template();
	}


	public function loupan_delete()
	{
		global $_GPC;
		$id[0] = intval($_GPC['id']);

		if (empty($id[0])) {
			$id = $_GPC['ids'];
		}
		foreach ($id as $i) {
			pdo_delete('xg_agent_loupan', array('id' => $i));
			plog('goods.delete', '删除项目，项目ID：' . $i);

			pdo_delete('xg_agent_jjrrule',array('lid'=>$i));

			pdo_delete('xg_agent_houselayer',array('loupanid'=>$i));

			$qi=pdo_getall('xg_agent_fytype',array('lid'=>$i));
			foreach($qi as $v){

				$qu=pdo_getall('xg_agent_fytype',array('did'=>$v['id']));
				foreach($qu as $v2){

					$hao=pdo_getall('xg_agent_fytype',array('did'=>$v2['id']));
					foreach($hao as $v3){

						$dan=pdo_getall('xg_agent_fytype',array('did'=>$v3['id']));
						foreach($dan as $v4){

							$ceng=pdo_getall('xg_agent_fytype',array('did'=>$v4['id']));
							foreach($ceng as $v5){

								$hu=pdo_getall('xg_agent_fytype',array('did'=>$v5['id']));
								foreach($hu as $v6){
									pdo_delete('xg_agent_fytype',array('id'=>$v6['id']));
									pdo_delete('xg_agent_fangyuan',array('huid'=>$v6['id']));
								}
								pdo_delete('xg_agent_fytype',array('id'=>$v5['id']));

							}
							pdo_delete('xg_agent_fytype',array('id'=>$v4['id']));

						}
						pdo_delete('xg_agent_fytype',array('id'=>$v3['id']));

					}
					pdo_delete('xg_agent_fytype',array('id'=>$v2['id']));

				}
				pdo_delete('xg_agent_fytype',array('id'=>$v['id']));

			}

		}

		show_json(1);

		include $this->template();
	}

	public function hximg()
	{
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 25;
		$condition = ' WHERE uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['sort'])) {
			$sort = $_GPC['sort'];
			$condition .= " and l_name like '%" . $sort . "%'";
		}
		$huxing = pdo_fetchall("select * from " . tablename('xg_agent_houselayer') . $condition . " limit " . (($pindex - 1) * $psize) . "," . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_houselayer') . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		$count = count($huxing);
		for ($i = 0; $i < $count; $i++) {
			$loupan = pdo_get('xg_agent_loupan', array('id' => $huxing[$i]['loupanid']));
			$huxing[$i]['louname'] = $loupan['title'];
		}

		include $this->template();
	}

	public function posthxt()
	{
		global $_GPC, $_W;

		$id = $_GPC['id'];
		$uniacid = $_W['uniacid'];
		if ($_W['ispost']) {
			if ($_GPC['loupanid'] == '' || $_GPC['loupanid'] == -1) {
				show_json(0,'请选择楼盘！');
			}
			$cusstat = array(
				'displayorder' => intval($_GPC['displayorder']),
				'l_name' => $_GPC['l_name'],
				'l_shi' => intval($_GPC['l_shi']),
				'l_ting' => intval($_GPC['l_ting']),
				'l_wei' => intval($_GPC['l_wei']),
				'l_pingmi' => $_GPC['l_pingmi'],
				'l_biaoqian' => $_GPC['l_biaoqian'],
				'l_content' => $_GPC['l_content'],
				'loupanid' => $_GPC['loupanid'],
				'l_url' => $_GPC['l_url'],
				'uniacid' => $_W['uniacid'],
				'bj_zc' => $_GPC['bj_zc'],
				'bj_dj' => $_GPC['bj_dj'],
				'bj_zc_type' => $_GPC['bj_zc_type'],
				'bj_dj_type' => $_GPC['bj_dj_type'],
				'status' => $_GPC['status'],
			);
			if ($_GPC['id'] != '') {
				plog('goods.hximg.edit','修改户型图，户型图ID:'.$id);
				pdo_update('xg_agent_houselayer', $cusstat, array('id' => $id));
				show_json(1,array('url'=>webUrl('goods/hximg')));
			} else {
				plog('goods.hximg.add','新增户型图');
				pdo_insert('xg_agent_houselayer', $cusstat);
				show_json(1,array('url'=>webUrl('goods/hximg')));
			}
		}
		$huxing = pdo_get('xg_agent_houselayer', array('id' => $id));
		$loupan = pdo_getall('xg_agent_loupan', array('uniacid' => $uniacid));

		include $this->template();
	}

	public function delhxt()
	{
		global $_GPC;
		$id[0] = intval($_GPC['id']);

		if (empty($id[0])) {
			$id = $_GPC['ids'];
		}
		foreach ($id as $i) {
			pdo_delete('xg_agent_houselayer', array('id' => $i));
			plog('goods.hximg.delete', '删除户型图，户型图ID：' . $i);
		}

		show_json(1);
	}

	public function fangyuan(){

		global $_W, $_GPC;
		$uniacid = $_W['uniacid'];
		$opp = $_GPC['opp'];
		$oppp = $_GPC['oppp'];
		$loupan = pdo_getall('xg_agent_loupan', array('uniacid' => $uniacid));

		if ($opp == 'qi') {
			if ($oppp == 'list') {
				$list = pdo_getall('xg_agent_fytype', array('type' => 0, 'uid' => $uniacid));
			}
			if (isset($_GPC['name'])) {
				if ($_GPC['loupan'] == -1) {
					show_json(0,'请选择楼盘');
				}
				if (empty($_GPC['name'])) {
					show_json(0,'请填写完整信息');
				}
				$data = array(
					'uid' => $uniacid,
					'type' => 0,
					'name' => $_GPC['name'],
					'did' => -1,
					'lid' => $_GPC['loupan']
				);
				pdo_insert('xg_agent_fytype', $data);
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'qi','oppp'=>'list'))));
			}
			if ($oppp == 'ajax') {
				$qi = pdo_getall('xg_agent_fytype', array('lid' => $_GPC['id']));
				echo json_encode($qi);
				die;
			}
		}
		if ($opp == 'qu') {
			if ($oppp == 'list') {
				$list = pdo_getall('xg_agent_fytype', array('type' => 1, 'uid' => $uniacid));
			}
			if (isset($_GPC['name'])) {
				if ($_GPC['qi'] == -1) {
					show_json(0,'请选择期');
				}
				if (empty($_GPC['name'])) {
					show_json(0,'请填写完整信息');
				}
				$data = array(
					'uid' => $uniacid,
					'type' => 1,
					'name' => $_GPC['name'],
					'did' => $_GPC['qi']
				);
				pdo_insert('xg_agent_fytype', $data);
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'qu','oppp'=>'list'))));
			}
			if ($oppp == 'ajax') {
				$qu = pdo_getall('xg_agent_fytype', array('did' => $_GPC['id']));
				echo json_encode($qu);
				die;
			}
		}
		if ($opp == 'hao') {
			if ($oppp == 'list') {
				$list = pdo_getall('xg_agent_fytype', array('type' => 2, 'uid' => $uniacid));
			}
			if (isset($_GPC['name'])) {
				if ($_GPC['qu'] == -1) {
					show_json(0,'请选择区');
				}
				if (empty($_GPC['name'])) {
					show_json(0,'请填写完整信息');
				}
				$data = array(
					'uid' => $uniacid,
					'type' => 2,
					'name' => $_GPC['name'],
					'did' => $_GPC['qu']
				);
				pdo_insert('xg_agent_fytype', $data);
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'hao','oppp'=>'list'))));
			}
			if ($oppp == 'ajax') {
				$hao = pdo_getall('xg_agent_fytype', array('did' => $_GPC['id']));
				echo json_encode($hao);
				die;
			}
		}
		if ($opp == 'danyuan') {
			if ($oppp == 'list') {
				$list = pdo_getall('xg_agent_fytype', array('type' => 3, 'uid' => $uniacid));
			}
			if (isset($_GPC['name'])) {
				if ($_GPC['hao'] == -1) {
					show_json(0,'请选择楼号');
				}
				if (empty($_GPC['name'])) {
					show_json(0,'请填写完整信息');
				}
				$data = array(
					'uid' => $uniacid,
					'type' => 3,
					'name' => $_GPC['name'],
					'did' => $_GPC['hao']
				);
				pdo_insert('xg_agent_fytype', $data);
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'fangyuan','oppp'=>'list'))));
			}
			if ($oppp == 'ajax') {
				$danyuan = pdo_getall('xg_agent_fytype', array('did' => $_GPC['id']));
				echo json_encode($danyuan);
				die;
			}
		}
		if ($opp == 'ceng') {
			if ($oppp == 'list') {
				$list = pdo_getall('xg_agent_fytype', array('type' => 4, 'uid' => $uniacid));
			}
			if (isset($_GPC['cengnum'])) {
				if ($_GPC['danyuan'] == -1) {
					show_json(0,'请选择单元');
				}
				if (empty($_GPC['cengnum']) || empty($_GPC['hunum'])) {
					show_json(0,'请填写完整信息');
				}
				$cnum = $_GPC['cengnum'];
				$hnum = $_GPC['hunum'];
				for ($c = 1; $c <= $cnum; $c++) {
					$data = array(
						'uid' => $uniacid,
						'type' => 4,
						'name' => $c,
						'did' => $_GPC['danyuan']
					);
					pdo_insert('xg_agent_fytype', $data);
					$cengid = pdo_insertid();
					for ($h = 1; $h <= $hnum; $h++) {
						$data2 = array(
							'uid' => $uniacid,
							'type' => 5,
							'name' => $h,
							'did' => $cengid
						);
						pdo_insert('xg_agent_fytype', $data2);
						$huid = pdo_insertid();
						$data3 = array(
							'uid' => $uniacid,
							'huid' => $huid,
							'huxing' => $_GPC['huxing'],
							'jiegou' => $_GPC['jiegou'],
							'chaoxiang' => $_GPC['chaoxiang'],
							'jiegou' => $_GPC['jiegou'],
							'jz_mj' => $_GPC['jz_mj'],
							'tn_mj' => $_GPC['tn_mj'],
							'jz_dj' => $_GPC['jz_dj'],
							'tn_dj' => $_GPC['tn_dj'],
							'zongjia' => $_GPC['zongjia'],
						);
						pdo_insert('xg_agent_fangyuan', $data3);
					}
				}
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'ceng','oppp'=>'list'))));
			}
			if ($oppp == 'ajax') {
				$ceng = pdo_getall('xg_agent_fytype', array('did' => $_GPC['id']));
				echo json_encode($ceng);
				die;
			}
		}
		if ($opp == 'hu') {
			if ($oppp == 'list') {
				$list = pdo_getall('xg_agent_fytype', array('type' => 5, 'uid' => $uniacid));
			}
			if (isset($_GPC['name'])) {
				if ($_GPC['ceng'] == -1) {
					show_json(0,'请选择层');
				}
				if (empty($_GPC['name'])) {
					show_json(0,'请填写完整信息');
				}
				$data = array(
					'uid' => $uniacid,
					'type' => 5,
					'name' => $_GPC['name'],
					'did' => $_GPC['ceng']
				);
				pdo_insert('xg_agent_fytype', $data);
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'hu','oppp'=>'list'))));
			}
			if ($oppp == 'ajax') {
				$hu = pdo_getall('xg_agent_fytype', array('did' => $_GPC['id']));
				echo json_encode($hu);
				die;
			}
		}
		if ($opp == 'fangyuan') {
			$where = '';
			if ($oppp == 'list') {
				if ($_GPC['submit'] == 'sort') {


					$idstr = '';

					if ($_GPC['hao'] != -1) {

						$danyuan = pdo_getall('xg_agent_fytype', array('did' => $_GPC['hao']));
						for ($i = 0; $i < count($danyuan); $i++) {
							$ceng = pdo_getall('xg_agent_fytype', array('did' => $danyuan[$i]['id']));
							for ($j = 0; $j < count($ceng); $j++) {
								$hu = pdo_getall('xg_agent_fytype', array('did' => $ceng[$j]['id']));
								for ($k = 0; $k < count($hu); $k++) {
									$idstr.=$hu[$k]['id'] . ',';
								}
							}
						}
					} elseif ($_GPC['qu'] != -1) {

						$qu = pdo_getall('xg_agent_fytype', array('did' => $_GPC['qu']));
						for ($f1 = 0; $f1 < count($qu); $f1++) {
							$danyuan = pdo_getall('xg_agent_fytype', array('did' => $qu[$f1]['id']));
							for ($i = 0; $i < count($danyuan); $i++) {
								$ceng = pdo_getall('xg_agent_fytype', array('did' => $danyuan[$i]['id']));
								for ($j = 0; $j < count($ceng); $j++) {
									$hu = pdo_getall('xg_agent_fytype', array('did' => $ceng[$j]['id']));
									for ($k = 0; $k < count($hu); $k++) {
										$idstr.=$hu[$k]['id'] . ',';
									}
								}
							}
						}
					} elseif ($_GPC['qi'] != -1) {

						$qi = pdo_getall('xg_agent_fytype', array('did' => $_GPC['qi']));
						for ($f2 = 0; $f2 < count($qi); $f2++) {
							$qu = pdo_getall('xg_agent_fytype', array('did' => $qi[$f2]['id']));
							for ($f1 = 0; $f1 < count($qu); $f1++) {
								$danyuan = pdo_getall('xg_agent_fytype', array('did' => $qu[$f1]['id']));
								for ($i = 0; $i < count($danyuan); $i++) {
									$ceng = pdo_getall('xg_agent_fytype', array('did' => $danyuan[$i]['id']));
									for ($j = 0; $j < count($ceng); $j++) {
										$hu = pdo_getall('xg_agent_fytype', array('did' => $ceng[$j]['id']));
										for ($k = 0; $k < count($hu); $k++) {
											$idstr.=$hu[$k]['id'] . ',';
										}
									}
								}
							}
						}
					} elseif ($_GPC['loupan'] != -1) {

						$loupan = pdo_getall('xg_agent_fytype', array('did' => $_GPC['loupan']));
						for ($f3 = 0; $f3 < count($loupan); $f3++) {
							$qi = pdo_getall('xg_agent_fytype', array('did' => $loupan[$f3]['id']));
							for ($f2 = 0; $f2 < count($qi); $f2++) {
								$qu = pdo_getall('xg_agent_fytype', array('did' => $qi[$f2]['id']));
								for ($f1 = 0; $f1 < count($qu); $f1++) {
									$danyuan = pdo_getall('xg_agent_fytype', array('did' => $qu[$f1]['id']));
									for ($i = 0; $i < count($danyuan); $i++) {
										$ceng = pdo_getall('xg_agent_fytype', array('did' => $danyuan[$i]['id']));
										for ($j = 0; $j < count($ceng); $j++) {
											$hu = pdo_getall('xg_agent_fytype', array('did' => $ceng[$j]['id']));
											for ($k = 0; $k < count($hu); $k++) {
												$idstr.=$hu[$k]['id'] . ',';
											}
										}
									}
								}
							}
						}
					}

					$idstr = substr($idstr, 0, -1);
					if ($idstr != '') {
						$where.=" and huid in($idstr)";
					}
				}

				$pindex = max(1, intval($_GPC['page']));
				$psize = 25;


				$list = pdo_fetchall("select * from " . tablename('xg_agent_fangyuan') . " where uid=:uid $where limit " . (($pindex - 1) * $psize) . "," . $psize, array(':uid' => $uniacid));
				$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('xg_agent_fangyuan') ." where uid=:uid". $where, array(':uid' => $uniacid));
				$pager = pagination($total, $pindex, $psize);

				for ($i = 0; $i < count($list); $i++) {
					$hu = pdo_get('xg_agent_fytype', array('id' => $list[$i]['huid']));
					$ceng = pdo_get('xg_agent_fytype', array('id' => $hu['did']));
					$danyuan = pdo_get('xg_agent_fytype', array('id' => $ceng['did']));
					$hao = pdo_get('xg_agent_fytype', array('id' => $danyuan['did']));
					$qu = pdo_get('xg_agent_fytype', array('id' => $hao['did']));
					$qi = pdo_get('xg_agent_fytype', array('id' => $qu['did']));
					$loupan2 = pdo_get('xg_agent_loupan', array('id' => $qi['lid']));
					$list[$i]['name'] = $loupan2['title'] . $qi['name'] . $qu['name'] . $hao['name'] . $danyuan['name'] . $ceng['name'] . '层' . $hu['name'] . '户';
				}
			}
			if (isset($_GPC['huxing'])) {
				if ($_GPC['hu'] == -1) {
					show_json(0,'请选择户');
				}
				if (empty($_GPC['huxing']) || empty($_GPC['jiegou']) || empty($_GPC['chaoxiang']) || empty($_GPC['jiegou']) || empty($_GPC['jz_mj']) || empty($_GPC['tn_mj']) || empty($_GPC['jz_dj']) || empty($_GPC['tn_dj']) || empty($_GPC['zongjia'])) {
					show_json(0,'请填写完整信息');
				}
				$data = array(
					'uid' => $uniacid,
					'huid' => $_GPC['hu'],
					'huxing' => $_GPC['huxing'],
					'jiegou' => $_GPC['jiegou'],
					'chaoxiang' => $_GPC['chaoxiang'],
					'jiegou' => $_GPC['jiegou'],
					'jz_mj' => $_GPC['jz_mj'],
					'tn_mj' => $_GPC['tn_mj'],
					'jz_dj' => $_GPC['jz_dj'],
					'tn_dj' => $_GPC['tn_dj'],
					'zongjia' => $_GPC['zongjia'],
				);
				pdo_insert('xg_agent_fangyuan', $data);
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>'fangyuan','oppp'=>'list'))));
			}
		}
		if ($opp == 'upd') {
			$opp = $_GPC['biaoshi'];
			if (isset($_GPC['name'])) {
				$data = array(
					'name' => $_GPC['name']
				);
				pdo_update('xg_agent_fytype', $data, array('id' => $_GPC['id']));
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>$opp,'oppp'=>'list'))));
			} elseif (isset($_GPC['huxing'])) {
				$data = array(
					'huxing' => $_GPC['huxing'],
					'jiegou' => $_GPC['jiegou'],
					'chaoxiang' => $_GPC['chaoxiang'],
					'jiegou' => $_GPC['jiegou'],
					'jz_mj' => $_GPC['jz_mj'],
					'tn_mj' => $_GPC['tn_mj'],
					'jz_dj' => $_GPC['jz_dj'],
					'tn_dj' => $_GPC['tn_dj'],
					'zongjia' => $_GPC['zongjia'],
					'status' => $_GPC['status'],
					'cid' => $_GPC['cid'],
					'cname' => $_GPC['cname'],
					'ctel' => $_GPC['ctel'],
				);
				pdo_update('xg_agent_fangyuan', $data, array('id' => $_GPC['id']));
				show_json(1,array('url'=>webUrl('goods/fangyuan',array('opp'=>$opp,'oppp'=>'list'))));
			} else {
				if ($opp == 'fangyuan') {
					$data = pdo_get('xg_agent_fangyuan', array('id' => $_GPC['id']));
					$customer = pdo_get('xg_agent_customer', array('id' => $data['cid']));
					$data['name'] = $customer['realname'];
//					$list = pdo_getall('xg_agent_user', array('uniacid' => $uniacid, 'flag' => 0, 'status' => 0, 'is_review' => 1));
					$list= pdo_fetchall("select * from ".tablename('xg_agent_customer')." where uniacid=:uniacid limit 0,10", array(':uniacid' => $uniacid));
				} else {
					$data = pdo_get('xg_agent_fytype', array('id' => $_GPC['id']));
				}
			}
			include $this->template('goods/fangyuanupd');
			die;
		}
		if ($opp == 'del') {
			$fid = $_GPC['id'];
			pdo_delete('xg_agent_fytype', array('id' => $fid));
			die;
		}
		if ($opp == 'delinfo') {
			$fid = $_GPC['id'];
			$hu = pdo_get('xg_agent_fangyuan', array('id' => $fid));
			pdo_delete('xg_agent_fytype', array('id' => $hu['huid']));
			pdo_delete('xg_agent_fangyuan', array('id' => $fid));
			die;
		}

		include $this->template();
	}

	public function getpagecustomer(){
		global $_W,$_GPC;
		$uniacid=$_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$list= pdo_fetchall("select * from ".tablename('xg_agent_customer')." where uniacid=:uniacid  limit " . (($pindex - 1) * $psize) . "," . $psize, array(':uniacid' => $uniacid));
		echo json_encode($list);
	}

}

?>
