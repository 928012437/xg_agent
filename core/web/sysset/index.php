<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_XgAgentPage extends WebPage
{

//	function __construct(){
//	die;
//	}

	public function main()
	{
//		if (cv('sysset.shop')) {
//			header('location: ' . webUrl('sysset/shop'));
//			return NULL;
//		}

//		if (cv('sysset.follow')) {
//			header('location: ' . webUrl('sysset/follow'));
//			return NULL;
//		}
//

		if (cv('sysset.trade')) {
			header('location: ' . webUrl('sysset/trade'));
			return NULL;
		}

		if (cv('sysset.payset')) {
			header('location: ' . webUrl('sysset/payset'));
			return NULL;
		}

		if (cv('sysset.notice')) {
			header('location: ' . webUrl('sysset/notice'));
			return NULL;
		}

//		if (cv('sysset.templat')) {
//			header('location: ' . webUrl('sysset/templat'));
//			return NULL;
//		}
//
//		if (cv('sysset.member')) {
//			header('location: ' . webUrl('sysset/member'));
//			return NULL;
//		}

//		if (cv('sysset.category')) {
//			header('location: ' . webUrl('sysset/category'));
//			return NULL;
//		}
//
//		if (cv('sysset.contact')) {
//			header('location: ' . webUrl('sysset/contact'));
//			return NULL;
//		}

//		if (cv('sysset.qiniu')) {
//			header('location: ' . webUrl('sysset/qiniu'));
//			return NULL;
//		}
//
//		if (cv('sysset.close')) {
//			header('location: ' . webUrl('sysset/close'));
//			return NULL;
//		}
//
		if (cv('sysset.tmessage')) {
			header('location: ' . webUrl('sysset/tmessage'));
			return NULL;
		}

		if (cv('sysset.cover')) {
			header('location: ' . webUrl('sysset/cover'));
			return NULL;
		}

		header('location: ' . webUrl());
	}

	public function shop()
	{
		global $_W;
		global $_GPC;
		$data = m('common')->getSysset('shop');

		if ($_W['ispost']) {
			ca('sysset.shop.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['name'] = trim($data['name']);
			$data['img'] = save_media($data['img']);
			$data['logo'] = save_media($data['logo']);
			$data['signimg'] = save_media($data['signimg']);
			$data['diycode'] = $_POST['data']['diycode'];
			m('common')->updateSysset(array('shop' => $data));
			plog('sysset.shop.edit', '修改系统设置-商城设置');
			show_json(1);
		}

		include $this->template('sysset/index');
	}

	public function follow()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.follow.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['logo'] = save_media($data['icon']);
			m('common')->updateSysset(array('share' => $data));
			plog('sysset.follow.edit', '修改系统设置-分享及关注设置');
			show_json(1);
		}

		$data = m('common')->getSysset('share');
		include $this->template();
	}

	public function notice()
	{
		global $_W;
		global $_GPC;
		$data = m('common')->getSysset('notice', false);
		$salers = array();

		if (isset($data['openid'])) {
			if (!empty($data['openid'])) {
				$openids = array();
				$strsopenids = explode(',', $data['openid']);

				foreach ($strsopenids as $openid) {
					$openids[] = '\'' . $openid . '\'';
				}

				$salers = pdo_fetchall('select id,nickname,avatar,openid from ' . tablename('xg_agent_member') . ' where openid in (' . implode(',', $openids) . ') and uniacid=' . $_W['uniacid']);
			}
		}

		$newtype = explode(',', $data['newtype']);

		if ($_W['ispost']) {
			ca('sysset.notice.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());

			if (is_array($_GPC['openids'])) {
				$data['openid'] = implode(',', $_GPC['openids']);
			}

			if (is_array($data['newtype'])) {
				$data['newtype'] = implode(',', $data['newtype']);
			}

			m('common')->updateSysset(array('notice' => $data));
			plog('sysset.notice.edit', '修改系统设置-模板消息通知设置');
			show_json(1);
		}

		$template_list = pdo_fetchall('SELECT id,title FROM ' . tablename('xg_agent_member_message_template') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
		include $this->template();
	}

	public function notice_user()
	{
		global $_W;
		global $_GPC;
		$data = m('common')->getSysset('notice', false);
		$salers = array();

		if (isset($data['openid'])) {
			if (!empty($data['openid'])) {
				$openids = array();
				$strsopenids = explode(',', $data['openid']);

				foreach ($strsopenids as $openid) {
					$openids[] = '\'' . $openid . '\'';
				}

				$salers = pdo_fetchall('select id,nickname,avatar,openid from ' . tablename('xg_agent_member') . ' where openid in (' . implode(',', $openids) . ') and uniacid=' . $_W['uniacid']);
			}
		}

		$newtype = explode(',', $data['newtype']);
		include $this->template('sysset/notice/user');
	}

	public function trade()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.trade.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());

			if (!empty($data['withdrawcharge'])) {
				$data['withdrawcharge'] = trim($data['withdrawcharge']);
				$data['withdrawcharge'] = floatval(trim($data['withdrawcharge'], '%'));
			}

			$data['withdrawbegin'] = floatval(trim($data['withdrawbegin']));
			$data['withdrawend'] = floatval(trim($data['withdrawend']));
			m('common')->updateSysset(array('trade' => $data));
			plog('sysset.trade.edit', '修改系统设置-交易设置');
			show_json(1);
		}

		$data = m('common')->getSysset('trade');
		include $this->template();
	}

	protected function upload_cert($fileinput)
	{
		global $_W;
		$path = IA_ROOT . '/addons/xg_agent/cert';
		load()->func('file');
		mkdirs($path);
		$f = $fileinput . '_' . $_W['uniacid'] . '.pem';
		$outfilename = $path . '/' . $f;
		$filename = $_FILES[$fileinput]['name'];
		$tmp_name = $_FILES[$fileinput]['tmp_name'];
		if (!empty($filename) && !empty($tmp_name)) {
			$ext = strtolower(substr($filename, strrpos($filename, '.')));

			if ($ext != '.pem') {
				$errinput = '';

				if ($fileinput == 'weixin_cert_file') {
					$errinput = 'CERT文件格式错误';
				}
				else if ($fileinput == 'weixin_key_file') {
					$errinput = 'KEY文件格式错误';
				}
				else {
					if ($fileinput == 'weixin_root_file') {
						$errinput = 'ROOT文件格式错误';
					}
				}

				show_json(0, $errinput . ',请重新上传!');
			}

			return file_get_contents($tmp_name);
		}

		return '';
	}

	public function payset()
	{
		global $_W;
		global $_GPC;
		$data = m('common')->getSysset('pay');
		$sec = m('common')->getSec();
		$sec = iunserializer($sec['sec']);

		if ($_W['ispost']) {
			ca('sysset.payset.edit');

			if ($_FILES['weixin_cert_file']['name']) {
				$sec['cert'] = $this->upload_cert('weixin_cert_file');
			}

			if ($_FILES['weixin_key_file']['name']) {
				$sec['key'] = $this->upload_cert('weixin_key_file');
			}

			if ($_FILES['weixin_root_file']['name']) {
				$sec['root'] = $this->upload_cert('weixin_root_file');
			}

			pdo_update('xg_agent_sysset', array('sec' => iserializer($sec)), array('uniacid' => $_W['uniacid']));
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['weixin'] = intval($data['weixin']);
			$data['alipay'] = intval($data['alipay']);
			$data['credit'] = intval($data['credit']);
			$data['cash'] = intval($data['cash']);
			m('common')->updateSysset(array('pay' => $data));
			plog('sysset.payset.edit', '修改系统设置-支付设置');
			show_json(1);
		}

		$url = $_W['siteroot'] . 'addons/xg_agent/payment/wechat/notify.php';
		load()->func('communication');
		$resp = ihttp_request($url);
		include $this->template();
		exit();
	}

	public function member()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.member.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['levelname'] = trim($data['levelname']);
			$data['levelurl'] = trim($data['levelurl']);
			$data['leveltype'] = intval($data['leveltype']);
			m('common')->updateSysset(array('member' => $data));
			$shop = m('common')->getSysset('shop');
			$shop['levelname'] = $data['levelname'];
			$shop['levelurl'] = $data['levelurl'];
			$shop['leveltype'] = $data['leveltype'];
			m('common')->updateSysset(array('shop' => $shop));
			plog('sysset.member.edit', '修改系统设置-会员设置');
			show_json(1);
		}

		$data = m('common')->getSysset('member');

		if (!isset($data['levelname'])) {
			$shop = m('common')->getSysset('shop');
			$data['levelname'] = $shop['levelname'];
			$data['levelurl'] = $shop['levelurl'];
			$data['leveltype'] = $shop['leveltype'];
		}

		include $this->template();
	}

	public function category()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.category.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$shop = m('common')->getSysset('shop');
			$shop['level'] = intval($data['level']);
			$shop['show'] = intval($data['show']);
			$shop['advimg'] = save_media($data['advimg']);
			$shop['advurl'] = trim($data['advurl']);
			m('common')->updateSysset(array('category' => $data));
			$shop = m('common')->getSysset('shop');
			$shop['catlevel'] = $data['level'];
			$shop['catshow'] = $data['show'];
			$shop['catadvimg'] = save_media($data['advimg']);
			$shop['catadvurl'] = $data['advurl'];
			m('common')->updateSysset(array('shop' => $shop));
			plog('sysset.category.edit', '修改系统设置-分类层级设置');
			m('shop')->getCategory(true);
			show_json(1);
		}

		$data = m('common')->getSysset('category');

		if (empty($data)) {
			$shop = m('common')->getSysset('shop');
			$data['level'] = $shop['catlevel'];
			$data['show'] = $shop['catshow'];
			$data['advimg'] = $shop['catadvimg'];
			$data['advurl'] = $shop['catadvurl'];
		}

		include $this->template();
	}

	public function contact()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.contact.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['qq'] = trim($data['qq']);
			$data['address'] = trim($data['address']);
			$data['phone'] = trim($data['phone']);
			m('common')->updateSysset(array('contact' => $data));
			$shop = m('common')->getSysset('shop');
			$shop['qq'] = $data['qq'];
			$shop['address'] = $data['address'];
			$shop['phone'] = $data['phone'];
			m('common')->updateSysset(array('shop' => $shop));
			plog('sysset.contact.edit', '修改系统设置-联系方式设置');
			show_json(1);
		}

		$data = m('common')->getSysset('contact');

		if (empty($data)) {
			$shop = m('common')->getSysset('shop');
			$data['qq'] = $shop['qq'];
			$data['address'] = $shop['address'];
			$data['phone'] = $shop['phone'];
		}

		include $this->template();
	}

	public function close()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.close.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['flag'] = intval($data['flag']);
			$data['detail'] = m('common')->html_images($data['detail']);
			$data['url'] = trim($data['url']);
			m('common')->updateSysset(array('close' => $data));
			$shop = m('common')->getSysset('shop');
			$shop['close'] = $data['flag'];
			$shop['closedetail'] = $data['detail'];
			$shop['closeurl'] = $data['url'];
			m('common')->updateSysset(array('shop' => $shop));
			plog('sysset.close.edit', '修改系统设置-商城关闭设置');
			show_json(1);
		}

		$data = m('common')->getSysset('close');

		if (empty($data)) {
			$shop = m('common')->getSysset('shop');
			$data['flag'] = $shop['close'];
			$data['detail'] = $shop['closedetail'];
			$data['url'] = $shop['closeurl'];
		}

		include $this->template();
	}

	public function templat()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			ca('sysset.templat.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			m('common')->updateSysset(array('template' => $data));
			$shop = m('common')->getSysset('shop');
			$shop['style'] = $data['style'];
			m('common')->updateSysset(array('shop' => $shop));
			m('cache')->set('template_shop', $data['style']);
			plog('sysset.templat.edit', '修改系统设置-模板设置');
			show_json(1);
		}

		$styles = array();
		$dir = IA_ROOT . '/addons/xg_agent/template/mobile/';

		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if (is_dir($dir . '/' . $file)) {
					$styles[] = $file;
				}
			}

			closedir($handle);
		}

		$data = m('common')->getSysset('template', false);
		include $this->template();
	}
}

?>
