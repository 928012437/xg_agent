<?php

class Member_XgAgentModel
{
	public function getInfo($openid = '')
	{
		global $_W;
		$uid = intval($openid);

		if ($uid == 0) {
			$info = pdo_fetch('select * from ' . tablename('xg_agent_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		}
		else {
			$info = pdo_fetch('select * from ' . tablename('xg_agent_member') . ' where id=:id  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $uid));
		}

		if (!empty($info['uid'])) {
			load()->model('mc');
			$uid = mc_openid2uid($info['openid']);
			$fans = mc_fetch($uid, array('credit1', 'credit2', 'birthyear', 'birthmonth', 'birthday', 'gender', 'avatar', 'resideprovince', 'residecity', 'nickname'));
			$info['credit1'] = $fans['credit1'];
			$info['credit2'] = $fans['credit2'];
			$info['birthyear'] = empty($info['birthyear']) ? $fans['birthyear'] : $info['birthyear'];
			$info['birthmonth'] = empty($info['birthmonth']) ? $fans['birthmonth'] : $info['birthmonth'];
			$info['birthday'] = empty($info['birthday']) ? $fans['birthday'] : $info['birthday'];
			$info['nickname'] = empty($info['nickname']) ? $fans['nickname'] : $info['nickname'];
			$info['gender'] = empty($info['gender']) ? $fans['gender'] : $info['gender'];
			$info['sex'] = $info['gender'];
			$info['avatar'] = empty($info['avatar']) ? $fans['avatar'] : $info['avatar'];
			$info['headimgurl'] = $info['avatar'];
			$info['province'] = empty($info['province']) ? $fans['resideprovince'] : $info['province'];
			$info['city'] = empty($info['city']) ? $fans['residecity'] : $info['city'];
		}

		if (!empty($info['birthyear']) && !empty($info['birthmonth']) && !empty($info['birthday'])) {
			$info['birthday'] = $info['birthyear'] . '-' . (strlen($info['birthmonth']) <= 1 ? '0' . $info['birthmonth'] : $info['birthmonth']) . '-' . (strlen($info['birthday']) <= 1 ? '0' . $info['birthday'] : $info['birthday']);
		}

		if (empty($info['birthday'])) {
			$info['birthday'] = '';
		}

		return $info;
	}

	public function getMember($openid = '')
	{
		global $_W;
		$uid = intval($openid);

		if (empty($uid)) {
			$info = pdo_fetch('select * from ' . tablename('xg_agent_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		}
		else {
			$info = pdo_fetch('select * from ' . tablename('xg_agent_member') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $uid));
		}

		if (!empty($info)) {
			$openid = $info['openid'];

			if (empty($info['uid'])) {
				$followed = m('user')->followed($openid);

				if ($followed) {
					load()->model('mc');
					$uid = mc_openid2uid($openid);

					if (!empty($uid)) {
						$info['uid'] = $uid;
						$upgrade = array('uid' => $uid);

						if (0 < $info['credit1']) {
							mc_credit_update($uid, 'credit1', $info['credit1']);
							$upgrade['credit1'] = 0;
						}

						if (0 < $info['credit2']) {
							mc_credit_update($uid, 'credit2', $info['credit2']);
							$upgrade['credit2'] = 0;
						}

						if (!empty($upgrade)) {
							pdo_update('xg_agent_member', $upgrade, array('id' => $info['id']));
						}
					}
				}
			}

			$credits = $this->getCredits($openid);
			$info['credit1'] = $credits['credit1'];
			$info['credit2'] = $credits['credit2'];
		}

		return $info;
	}

	public function getMid()
	{
		global $_W;
		$openid = $_W['openid'];
		$member = $this->getMember($openid);
		return $member['id'];
	}

	public function setCredit($openid = '', $credittype = 'credit1', $credits = 0, $log = array())
	{
		global $_W;
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		if (!empty($uid)) {
			$value = pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('mc_members') . ' WHERE `uid` = :uid', array(':uid' => $uid));

			$newcredit = $credits + $value;

			if ($newcredit <= 0) {
				$newcredit = 0;
			}
			pdo_update('mc_members', array($credittype => $newcredit), array('uid' => $uid));
			pdo_update('xg_agent_member', array($credittype => $newcredit), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
			if (empty($log) || !is_array($log)) {
				$log = array($uid, '未记录');
			}

			$data = array('uid' => $uid, 'credittype' => $credittype, 'uniacid' => $_W['uniacid'], 'num' => $credits, 'createtime' => TIMESTAMP, 'module' => 'xg_agent', 'operator' => intval($log[0]), 'remark' => $log[1]);
			pdo_insert('mc_credits_record', $data);
			return NULL;
		}

		$value = pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('xg_agent_member') . ' WHERE  uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		$newcredit = $credits + $value;

		if ($newcredit <= 0) {
			$newcredit = 0;
		}

		pdo_update('xg_agent_member', array($credittype => $newcredit), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
	}

	public function getCredit($openid = '', $credittype = 'credit1')
	{
		global $_W;
		load()->model('mc');
		$uid = mc_openid2uid($openid);

		if (!empty($uid)) {
			return pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('mc_members') . ' WHERE `uid` = :uid', array(':uid' => $uid));
		}

		return pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('xg_agent_member') . ' WHERE  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
	}

	public function getCredits($openid = '', $credittypes = array('credit1', 'credit2'))
	{
		global $_W;
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		$types = implode(',', $credittypes);

		if (!empty($uid)) {
			return pdo_fetch('SELECT ' . $types . ' FROM ' . tablename('mc_members') . ' WHERE `uid` = :uid limit 1', array(':uid' => $uid));
		}

		return pdo_fetch('SELECT ' . $types . ' FROM ' . tablename('xg_agent_member') . ' WHERE  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
	}

	public function checkMember()
	{
		global $_W;
		global $_GPC;
		$member = array();
		$shopset = m('common')->getSysset('shop');
		$openid = $_W['openid'];
//		if (empty($openid) && !XG_AGENT_DEBUG) {
//			exit("<!DOCTYPE html>\r\n                <html>\r\n                    <head>\r\n                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>\r\n                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>\r\n                    </head>\r\n                    <body>\r\n                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>\r\n                    </body>\r\n                </html>");
//		}

		$member = m('member')->getMember($openid);
		$followed = m('user')->followed($openid);
		$uid = 0;
		$mc = array();
		load()->model('mc');

		if ($followed) {
			$uid = mc_openid2uid($openid);
			$mc = mc_fetch($uid, array('nickname', 'realname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist', 'gender'));
		}
		else {
			if (!empty($shopset['getinfo'])) {
				$userinfo = mc_oauth_userinfo();
				$mc = array();
				$mc['nickname'] = $userinfo['nickname'];
				$mc['avatar'] = $userinfo['headimgurl'];
				$mc['gender'] = $userinfo['sex'];
				$mc['resideprovince'] = $userinfo['province'];
				$mc['residecity'] = $userinfo['city'];
			}
		}

		if (p('commission')) {
			p('commission')->checkAgent($openid);
		}

		if (p('poster')) {
			p('poster')->checkScan($openid);
		}

		if (empty($member)) {
			return false;
		}

		return array('id' => $member['id'], 'openid' => $member['openid']);
	}

	public function getLevels()
	{
		global $_W;
		return pdo_fetchall('select * from ' . tablename('xg_agent_member_level') . ' where uniacid=:uniacid order by level asc', array(':uniacid' => $_W['uniacid']));
	}

	public function getLevel($openid)
	{
		global $_W;
		global $_S;

		if (empty($openid)) {
			return false;
		}

		$member = m('member')->getMember($openid);
		if (!empty($member) && !empty($member['level'])) {
			$level = pdo_fetch('select * from ' . tablename('xg_agent_member_level') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $member['level'], ':uniacid' => $_W['uniacid']));

			if (!empty($level)) {
				return $level;
			}
		}

		return array('levelname' => empty($_S['shop']['levelname']) ? '普通会员' : $_S['shop']['levelname'], 'discount' => empty($_S['shop']['leveldiscount']) ? 10 : $_S['shop']['leveldiscount']);
	}

	public function upgradeLevel($openid)
	{
		global $_W;

		if (empty($openid)) {
			return NULL;
		}

		$shopset = m('common')->getSysset('shop');
		$leveltype = intval($shopset['leveltype']);
		$member = m('member')->getMember($openid);

		if (empty($member)) {
			return NULL;
		}

		$level = false;

		if (empty($leveltype)) {
			$ordermoney = pdo_fetchcolumn('select ifnull( sum(og.realprice),0) from ' . tablename('xg_agent_order_goods') . ' og ' . ' left join ' . tablename('xg_agent_order') . ' o on o.id=og.orderid ' . ' where o.openid=:openid and o.status=3 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('xg_agent_member_level') . ' where uniacid=:uniacid  and enabled=1 and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
		}
		else {
			if ($leveltype == 1) {
				$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('xg_agent_order') . ' where openid=:openid and status=3 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
				$level = pdo_fetch('select * from ' . tablename('xg_agent_member_level') . ' where uniacid=:uniacid and enabled=1 and ' . $ordercount . ' >= ordercount and ordercount>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
			}
		}

		if (empty($level)) {
			return NULL;
		}

		if ($level['id'] == $member['level']) {
			return NULL;
		}

		$oldlevel = $this->getLevel($openid);
		$canupgrade = false;

		if (empty($oldlevel['id'])) {
			$canupgrade = true;
		}
		else {
			if ($oldlevel['level'] < $level['level']) {
				$canupgrade = true;
			}
		}

		if ($canupgrade) {
			pdo_update('xg_agent_member', array('level' => $level['id']), array('id' => $member['id']));
			m('notice')->sendMemberUpgradeMessage($openid, $oldlevel, $level);
		}
	}

	public function getGroups()
	{
		global $_W;
		return pdo_fetchall('select * from ' . tablename('xg_agent_member_group') . ' where uniacid=:uniacid order by id asc', array(':uniacid' => $_W['uniacid']));
	}

	public function getGroup($openid)
	{
		if (empty($openid)) {
			return false;
		}

		$member = m('member')->getMember($openid);
		return $member['groupid'];
	}

	public function setRechargeCredit($openid = '', $money = 0)
	{
		if (empty($openid)) {
			return NULL;
		}

		global $_W;
		$credit = 0;
		$set = m('common')->getSysset(array('trade', 'shop'));

		if ($set['trade']) {
			$tmoney = floatval($set['trade']['money']);
			$tcredit = intval($set['trade']['credit']);

			if (0 < $tmoney) {
				if (($money % $tmoney) == 0) {
					$credit = intval($money / $tmoney) * $tcredit;
				}
				else {
					$credit = (intval($money / $tmoney) + 1) * $tcredit;
				}
			}
		}

		if (0 < $credit) {
			$this->setCredit($openid, 'credit1', $credit, array(0, $set['shop']['name'] . '会员充值积分:credit2:' . $credit));
		}
	}

	public function getCalculateMoney($money, $set_array)
	{
		$charge = $set_array['charge'];
		$begin = $set_array['begin'];
		$end = $set_array['end'];
		$array = array();
		$array['deductionmoney'] = round(($money * $charge) / 100, 2);
		if (($begin <= $array['deductionmoney']) && ($array['deductionmoney'] <= $end)) {
			$array['deductionmoney'] = 0;
		}

		$array['realmoney'] = round($money - $array['deductionmoney'], 2);

		if ($money == $array['realmoney']) {
			$array['flag'] = 0;
		}
		else {
			$array['flag'] = 1;
		}

		return $array;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
