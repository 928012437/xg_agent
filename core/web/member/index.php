<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends WebPage
{
	public function main()
	{
		global $_W;
		include $this->template();
	}

	protected function selectMemberCreate($day = 0)
	{
		global $_W;
		$day = (int) $day;

		if ($day != 0) {
			$createtime1 = strtotime(date('Y-m-d', time() - ($day * 3600 * 24)));
			$createtime2 = strtotime(date('Y-m-d', time()));
		}
		else {
			$createtime1 = strtotime(date('Y-m-d', time()));
			$createtime2 = strtotime(date('Y-m-d', time() + (3600 * 24)));
		}

		$sql = 'select count(*) from ' . tablename('xg_agent_member') . ' where uniacid = :uniacid and createtime between :createtime1 and :createtime2';
		$param = array(':uniacid' => $_W['uniacid'], ':createtime1' => $createtime1, ':createtime2' => $createtime2);
		return pdo_fetchcolumn($sql, $param);
	}

	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND (`realname` LIKE :keyword or `nickname` LIKE :keyword or `mobile` LIKE :keyword)';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall('SELECT * FROM ' . tablename('xg_agent_member') . ' WHERE 1 ' . $condition . ' order by id asc', $params);

		foreach($ds as &$v){
			$v['avatar']=$v['headurl'];
		}
		unset($v);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->template('member/query');
		exit();
	}

	public function ajaxnewmember()
	{
		global $_GPC;
		global $_W;
		$day = (int) $_GPC['day'];
		$param = array(':uniacid' => $_W['uniacid']);
		$member_count = pdo_fetchcolumn('select count(*) from ' . tablename('xg_agent_member') . ' where uniacid=:uniacid', $param);
		$newmember = $this->selectMemberCreate($day);
		echo json_encode(array('count' => (int) $newmember, 'rate' => empty($member_count) ? 0 : (int) number_format(round($newmember / $member_count, 3) * 100)));
	}

	public function ajaxmembergender()
	{
		global $_W;
		$gender_array = array(0, 0, 0);
		$sql_member = 'select gender,count(gender) as gender_num from ' . tablename('xg_agent_member') . ' where uniacid = :uniacid group by gender';
		$param_member = array(':uniacid' => $_W['uniacid']);
		$member = pdo_fetchall($sql_member, $param_member);

		foreach ($member as $key => $val) {
			if ($val['gender'] == -1) {
				$gender_array[0] += (int) $val['gender_num'];
			}
			else {
				$gender_array[$val['gender']] += (int) $val['gender_num'];
			}
		}

		echo json_encode($gender_array);
	}

	public function ajaxmemberlevel()
	{
		global $_W;
		$levels = pdo_fetchall('select * from ' . tablename('xg_agent_member_level') . ' where uniacid=:uniacid order by level asc', array(':uniacid' => $_W['uniacid']), 'id');
		$levelname = array_map(function($val) {
			return $val['levelname'];
		}, $levels);
		$levelname[0] = '普通等级';
		ksort($levelname);
		$sql_level = 'select level,count(level) as level_num from ' . tablename('xg_agent_member') . ' where uniacid = :uniacid group by level';
		$param_level = array(':uniacid' => $_W['uniacid']);
		$member_level = pdo_fetchall($sql_level, $param_level);
		$levels_array = array();
		array_walk($levelname, function($value, $key) use(&$levels_array) {
			$levels_array[$key] = 0;
		});

		foreach ($member_level as $key => $val) {
			if (array_key_exists($val['level'], $levelname)) {
				$levels_array[$val['level']] = $val['level_num'];
			}
			else {
				$levels_array[0] += $val['level_num'];
			}
		}

		if (!array_key_exists(0, $levels_array)) {
			$levels_array[0] = 0;
		}

		$count = array_values($levels_array);
		$name = array_values($levelname);
		$res = array();

		foreach ($count as $key => $value) {
			$res[$key]['value'] = $value;
			$res[$key]['name'] = $name[$key];
		}

		echo json_encode(array('count' => $count, 'name' => $name, 'data' => $res));
	}

	public function ajaxprovince()
	{
		global $_W;
		$province = pdo_fetchall('select province,count(province) as province_num from ' . tablename('xg_agent_member') . ' where uniacid = :uniacid group by province', array(':uniacid' => $_W['uniacid']));
		$res = array_map(function($array) {
			$array['province'] = preg_replace('/(市|省)(.*)/', '', $array['province']);
			$res = array('name' => $array['province'], 'value' => (int) $array['province_num']);
			return $res;
		}, $province);
		echo json_encode($res);
	}
}

?>
