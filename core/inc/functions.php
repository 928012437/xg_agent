<?php

function m($name = '')
{
	static $_modules = array();

	if (isset($_modules[$name])) {
		return $_modules[$name];
	}

	$model = XG_AGENT_CORE . 'model/' . strtolower($name) . '.php';

	if (!is_file($model)) {
		exit(' Model ' . $name . ' Not Found!');
	}

	require_once $model;
	$class_name = ucfirst($name) . '_XgAgentModel';
	$_modules[$name] = new $class_name();
	return $_modules[$name];
}

function d($name = '')
{
	static $_modules = array();

	if (isset($_modules[$name])) {
		return $_modules[$name];
	}

	$model = XG_AGENT_CORE . 'data/' . strtolower($name) . '.php';

	if (!is_file($model)) {
		exit(' Data Model ' . $name . ' Not Found!');
	}

	require_once XG_AGENT_INC . 'data_model.php';
	require_once $model;
	$class_name = ucfirst($name) . '_XgAgentDataModel';
	$_modules[$name] = new $class_name();
	return $_modules[$name];
}

function plugin_run($name = '')
{
	$names = explode('::', $name);
	$plugin = p($names[0]);

	if (!$plugin) {
		return false;
	}

	if (!method_exists($plugin, $names[1])) {
		return false;
	}

	$func_args = func_get_args();
	$args = array_splice($func_args, 1);
	return call_user_func_array(array($plugin, $names[1]), $args);
}

function p($name = '')
{
	static $_plugins = array();

	if (isset($_plugins[$name])) {
		return $_plugins[$name];
	}

	$model = XG_AGENT_PLUGIN . strtolower($name) . '/core/model.php';

	if (!is_file($model)) {
		return false;
	}

	require_once XG_AGENT_CORE . 'inc/plugin_model.php';
	require_once $model;
	$class_name = ucfirst($name) . 'Model';
	$_plugins[$name] = new $class_name($name);

	if (com_run('perm::check_plugin', $name)) {
		return $_plugins[$name];
	}

	return false;
}

function com($name = '')
{
	static $_coms = array();

	if (isset($_coms[$name])) {
		return $_coms[$name];
	}

	$model = XG_AGENT_CORE . 'com/' . strtolower($name) . '.php';

	if (!is_file($model)) {
		return false;
	}

	require_once XG_AGENT_CORE . 'inc/com_model.php';
	require_once $model;
	$class_name = ucfirst($name) . '_XgAgentComModel';
	$_coms[$name] = new $class_name($name);

	if ($name == 'perm') {
		return $_coms[$name];
	}

	if (com('perm')->check_com($name)) {
		return $_coms[$name];
	}

	return false;
}

function com_run($name = '')
{
	$names = explode('::', $name);
	$com = com($names[0]);

	if (!$com) {
		return false;
	}

	if (!method_exists($com, $names[1])) {
		return false;
	}

	$func_args = func_get_args();
	$args = array_splice($func_args, 1);
	return call_user_func_array(array($com, $names[1]), $args);
}

function byte_format($input, $dec = 0)
{
	$prefix_arr = array(' B', 'K', 'M', 'G', 'T');
	$value = round($input, $dec);
	$i = 0;

	while (1024 < $value) {
		$value /= 1024;
		++$i;
	}

	$return_str = round($value, $dec) . $prefix_arr[$i];
	return $return_str;
}

function is_array2($array)
{
	if (is_array($array)) {
		foreach ($array as $k => $v) {
			return is_array($v);
		}

		return false;
	}

	return false;
}

function set_medias($list = array(), $fields = NULL)
{
	if (empty($fields)) {
		foreach ($list as &$row) {
			$row = tomedia($row);
		}

		return $list;
	}

	if (!is_array($fields)) {
		$fields = explode(',', $fields);
	}

	if (is_array2($list)) {
		foreach ($list as $key => &$value) {
			foreach ($fields as $field) {
				if (isset($list[$field])) {
					$list[$field] = tomedia($list[$field]);
				}

				if (is_array($value) && isset($value[$field])) {
					$value[$field] = tomedia($value[$field]);
				}
			}
		}

		return $list;
	}

	foreach ($fields as $field) {
		if (isset($list[$field])) {
			$list[$field] = tomedia($list[$field]);
		}
	}

	return $list;
}

function get_last_day($year, $month)
{
	return date('t', strtotime($year . '-' . $month . ' -1'));
}

function show_message($msg = '', $url = '', $type = '')
{
	$site = new Page();
	$site->message($msg, $url, $type);
	exit();
}

function show_json($status = 1, $return = NULL)
{
	$ret = array('status' => $status, 'result' => $status == 1 ? array('url' => referer()) : array());

	if (!is_array($return)) {
		if ($return) {
			$ret['result']['message'] = $return;
		}

		exit(json_encode($ret));
	}
	else {
		$ret['result'] = $return;
	}

	if (isset($return['url'])) {
		$ret['result']['url'] = $return['url'];
	}
	else {
		if ($status == 1) {
			$ret['result']['url'] = referer();
		}
	}

	exit(json_encode($ret));
}

function is_weixin()
{
	if (empty($_SERVER['HTTP_USER_AGENT']) || ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false))) {
		return false;
	}

	return true;
}

function b64_encode($obj)
{
	if (is_array($obj)) {
		return urlencode(base64_encode(json_encode($obj)));
	}

	return urlencode(base64_encode($obj));
}

function b64_decode($str, $is_array = true)
{
	$str = base64_decode(urldecode($str));

	if ($is_array) {
		return json_decode($str, true);
	}

	return $str;
}

function create_image($img)
{
	$ext = strtolower(substr($img, strrpos($img, '.')));

	if ($ext == '.png') {
		$thumb = imagecreatefrompng($img);
	}
	else if ($ext == '.gif') {
		$thumb = imagecreatefromgif($img);
	}
	else {
		$thumb = imagecreatefromjpeg($img);
	}

	return $thumb;
}

function get_authcode()
{
	$auth = get_auth();
	return empty($auth['code']) ? '' : $auth['code'];
}

function get_auth()
{
	global $_W;
	$set = pdo_fetch('select sets from ' . tablename('xg_agent_sysset') . ' order by id asc limit 1');
	$sets = iunserializer($set['sets']);

	if (is_array($sets)) {
		return is_array($sets['auth']) ? $sets['auth'] : array();
	}

	return array();
}

function rc($plugin = '')
{
	global $_W;
	global $_GPC;
	$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
	$ip = gethostbyname($_SERVER['HTTP_HOST']);
	$setting = setting_load('site');
	$id = (isset($setting['site']['key']) ? $setting['site']['key'] : '0');
	$auth = get_auth();
	load()->func('communication');
	$resp = ihttp_request(XG_AGENT_AUTH_URL, array('ip' => $ip, 'id' => $id, 'code' => $auth['code'], 'domain' => $domain, 'plugin' => $plugin), NULL, 1);
	$result = @json_decode($resp['content'], true);

	if (!empty($result['status'])) {
		return true;
	}

	return false;
}

function shop_template_compile($from, $to, $inmodule = false)
{
	$path = dirname($to);

	if (!is_dir($path)) {
		load()->func('file');
		mkdirs($path);
	}

	$content = shop_template_parse(file_get_contents($from), $inmodule);
	if ((IMS_FAMILY == 'x') && !preg_match('/(footer|header|account\\/welcome|login|register)+/', $from)) {
		$content = str_replace('微擎', '系统', $content);
	}

	file_put_contents($to, $content);
}

function shop_template_parse($str, $inmodule = false)
{
	global $_W;
	$str = template_parse($str, $inmodule);

	if (strexists($_W['siteurl'], 'merchant.php')) {
		if (p('merch')) {
			$str = preg_replace('/{ifp\\s+(.+?)}/', '<?php if(im_check($1)) { ?>', $str);
			$str = preg_replace('/{ifpp\\s+(.+?)}/', '<?php if(mcp($1)) { ?>', $str);
			$str = preg_replace('/{ife\\s+(\\S+)\\s+(\\S+)}/', '<?php if( mce($1 ,$2) ) { ?>', $str);
			return $str;
		}
	}

	$str = preg_replace('/{ifp\\s+(.+?)}/', '<?php if(cv($1)) { ?>', $str);
	$str = preg_replace('/{ifpp\\s+(.+?)}/', '<?php if(cp($1)) { ?>', $str);
	$str = preg_replace('/{ife\\s+(\\S+)\\s+(\\S+)}/', '<?php if( ce($1 ,$2) ) { ?>', $str);
	return $str;
}

function ce($permtype = '', $item = NULL)
{
	$perm = com_run('perm::check_edit', $permtype, $item);
	return $perm;
}

function cv($permtypes = '')
{
	$perm = com_run('perm::check_perm', $permtypes);
	return $perm;
}

function im_check($perm = '')
{
	global $_W;

	if(($_W['role'] != 'manager') && ($_W['role'] != 'founder')){
		$user=pdo_get('xg_agent_perm_user',array('username'=>($_W['username'])));
		$role=pdo_get('xg_agent_perm_role',array('id'=>$user['roleid']));
		if($role['status']==0||$user['status']==0){
			return false;
		}
		$u_perm=explode(',',$user['perms2']);
		$u_perm=array_merge($u_perm,explode(',',$role['perms2']));
		if(in_array($perm,$u_perm)){
			return true;
		}else{
			return false;
		}

	}else{
		return true;
	}

}

function ca($permtypes = '')
{
	global $_W;
	$err = '您没有权限操作，请联系管理员!';

	if (!cv($permtypes)) {
		if ($_W['isajax']) {
			show_json(0, $err);
		}

		show_message($err, '', 'error');
	}
}

function cp($pluginname = '')
{
	$perm = p('perm');

	if ($perm) {
		return $perm->check_plugin($pluginname);
	}

	return true;
}

function cpa($pluginname = '')
{
	if (!cp($pluginname)) {
		show_message('您没有权限操作，请联系管理员!', '', 'error');
	}
}

function plog($type = '', $op = '')
{
	com_run('perm::log', $type, $op);
}

function tpl_selector($name, $options = array())
{
	$options['multi'] = intval($options['multi']);
	$options['buttontext'] = isset($options['buttontext']) ? $options['buttontext'] : '请选择';
	$options['items'] = isset($options['items']) && $options['items'] ? $options['items'] : array();
	$options['readonly'] = isset($options['readonly']) ? $options['readonly'] : true;
	$options['callback'] = isset($options['callback']) ? $options['callback'] : '';
	$options['key'] = isset($options['key']) ? $options['key'] : 'id';
	$options['text'] = isset($options['text']) ? $options['text'] : 'title';
	$options['thumb'] = isset($options['thumb']) ? $options['thumb'] : 'thumb';
	$options['preview'] = isset($options['preview']) ? $options['preview'] : true;
	$options['type'] = isset($options['type']) ? $options['type'] : 'image';
	$options['input'] = isset($options['input']) ? $options['input'] : true;
	$options['required'] = isset($options['required']) ? $options['required'] : false;
	$options['placeholder'] = isset($options['placeholder']) ? $options['placeholder'] : '请输入关键词';

	if (empty($options['items'])) {
		$options['items'] = array();
	}
	else {
		if (!is_array2($options['items'])) {
			$options['items'] = array($options['items']);
		}
	}

	$options['name'] = $name;
	$titles = '';

	foreach ($options['items'] as $item) {
		$titles .= $item[$options['text']];

		if (1 < count($options['items'])) {
			$titles .= '; ';
		}
	}

	$options['value'] = isset($options['value']) ? $options['value'] : $titles;
	$readonly = ($options['readonly'] ? 'readonly' : '');
	$required = ($options['required'] ? ' data-rule-required="true"' : '');
	$callback = (!empty($options['callback']) ? ', ' . $options['callback'] : '');
	$id = ($options['multi'] ? $name . '[]' : $name);
	$html = '<div id=\'' . $name . "_selector' class='selector' \r\n                     data-type=\"" . $options['type'] . "\"\r\n                     data-key=\"" . $options['key'] . "\"\r\n                     data-text=\"" . $options['text'] . "\"\r\n                     data-thumb=\"" . $options['thumb'] . "\"\r\n                     data-multi=\"" . $options['multi'] . "\"\r\n                     data-callback=\"" . $options['callback'] . "\"\r\n                     data-url=\"" . $options['url'] . "\"\r\n                 >";

	if ($options['input']) {
		$html .= '<div class=\'input-group\'>' . '<input type=\'text\' id=\'' . $name . '_text\' name=\'' . $name . '_text\'  value=\'' . $options['value'] . '\' class=\'form-control text\'  ' . $readonly . '  ' . $required . '/>' . '<div class=\'input-group-btn\'>';
	}

	$html .= '<button class=\'btn btn-default\' type=\'button\' onclick=\'biz.selector.select(' . json_encode($options) . ');\'>' . $options['buttontext'] . '</button>';

	if ($options['input']) {
		$html .= '</div>';
		$html .= '</div>';
	}

	$show = ($options['preview'] ? '' : ' style=\'display:none\'');

	if ($options['type'] == 'image') {
		$html .= '<div class=\'input-group multi-img-details container\' ' . $show . '>';
	}
	else {
		$html .= '<div class=\'input-group multi-audio-details container\' ' . $show . '>';
	}

	foreach ($options['items'] as $item) {
		if ($options['type'] == 'image') {
			$html .= '<div class=\'multi-item\' data-' . $options['key'] . '=\'' . $item[$options['key']] . '\' data-name=\'' . $name . "'>\r\n                                      <img class='img-responsive img-thumbnail' src='" . tomedia($item[$options['thumb']]) . "'>\r\n                                      <div class='img-nickname'>" . $item[$options['text']] . "</div>\r\n                                     <input type='hidden' value='" . $item[$options['key']] . '\' name=\'' . $id . "'>\r\n                                     <em onclick='biz.selector.remove(this,\"" . $name . "\")'  class='close'>×</em>\r\n                         </div>";
		}
		else {
			$html .= '<div class=\'multi-audio-item \' data-' . $options['key'] . '=\'' . $item[$options['key']] . "' >\r\n                       <div class='input-group'>\r\n                       <input type='text' class='form-control img-textname' readonly='' value='" . $item[$options['text']] . "'>\r\n                       <input type='hidden'  value='" . $item[$options['key']] . '\' name=\'' . $id . "'> \r\n                       <div class='input-group-btn'><button class='btn btn-default' onclick='biz.selector.remove(this,\"" . $name . "\")' type='button'><i class='fa fa-remove'></i></button>\r\n                       </div></div></div>";
		}
	}

	$html .= '</div></div>';
	return $html;
}

function tpl_daterange($name, $value = array(), $time = false)
{
	global $_GPC;
	$placeholder = (isset($value['placeholder']) ? $value['placeholder'] : '');
	$s = '';
	if (empty($time) && !defined('TPL_INIT_DATERANGE_DATE')) {
		$s = "\r\n<script type=\"text/javascript\">\r\n\trequire([\"daterangepicker\"], function(\$){\r\n\t\t\$(function(){\r\n\t\t\t\$(\".daterange.daterange-date\").each(function(){\r\n         \r\n\t\t\t\tvar elm = this;\r\n                                        var container =\$(elm).parent().prev(); \r\n\t\t\t\t\$(this).daterangepicker({\r\n\t\t\t\t\t \r\n\t\t\t\t\tformat: \"YYYY-MM-DD\"\r\n\t\t\t\t}, function(start, end){\r\n\t\t\t\t\t\$(elm).find(\".date-title\").html(start.toDateStr() + \" 至 \" + end.toDateStr());\r\n\t\t\t\t\tcontainer.find(\":input:first\").val(start.toDateTimeStr());\r\n\t\t\t\t\tcontainer.find(\":input:last\").val(end.toDateTimeStr());\r\n\t\t\t\t});\r\n\t\t\t});\r\n\t\t});\r\n\t});\r\n</script> \r\n";
		define('TPL_INIT_DATERANGE_DATE', true);
	}

	if (!empty($time) && !defined('TPL_INIT_DATERANGE_TIME')) {
		$s = "\r\n<script type=\"text/javascript\">\r\n\trequire([\"daterangepicker\"], function(\$){\r\n\t\t\$(function(){\r\n\t\t\t\$(\".daterange.daterange-time\").each(function(){\r\n               \r\n\t\t\t\tvar elm = this;\r\n                                       var container =\$(elm).parent().prev(); \r\n\t\t\t\t\$(this).daterangepicker({\r\n\t\t\t\t\r\n\t\t\t\t\tformat: \"YYYY-MM-DD HH:mm\",\r\n\t\t\t\t\ttimePicker: true,\r\n\t\t\t\t\ttimePicker12Hour : false,\r\n\t\t\t\t\ttimePickerIncrement: 1,\r\n\t\t\t\t\tminuteStep: 1\r\n\t\t\t\t}, function(start, end){\r\n\t\t\t\t\t\$(elm).find(\".date-title\").html(start.toDateTimeStr() + \" 至 \" + end.toDateTimeStr());\r\n\t\t\t\t\tcontainer.find(\":input:first\").val(start.toDateTimeStr());\r\n\t\t\t\t\tcontainer.find(\":input:last\").val(end.toDateTimeStr());\r\n\t\t\t\t});\r\n\t\t\t});\r\n\t\t});\r\n\t});\r\n    function clearTime(obj){\r\n         \r\n              \$(obj).prev().html(\"<span class=date-title>\" + \$(obj).attr(\"placeholder\") + \"</span>\");\r\n              \$(obj).parent().prev().find(\"input\").val(\"\");\r\n    }\r\n</script>\r\n";
		define('TPL_INIT_DATERANGE_TIME', true);
	}

	$str = $placeholder;
	if ($_GPC[$name]['start'] && $_GPC[$name]['end']) {
		if (empty($time)) {
			$str = date('Y-m-d', strtotime($_GPC[$name]['start'])) . '至 ' . date('Y-m-d', strtotime($_GPC[$name]['end']));
		}
		else {
			$str = date('Y-m-d H:i', strtotime($_GPC[$name]['start'])) . ' 至 ' . date('Y-m-d  H:i', strtotime($_GPC[$name]['end']));
		}
	}

	$small = (isset($value['sm']) ? $value['sm'] : true);
	$value['starttime'] = isset($value['starttime']) ? $value['starttime'] : ($_GPC[$name]['start'] ? $_GPC[$name]['start'] : '');
	$value['endtime'] = isset($value['endtime']) ? $value['endtime'] : ($_GPC[$name]['end'] ? $_GPC[$name]['end'] : '');
	$s .= "<div style=\"float:left\">\r\n\t<input name=\"" . $name . '[start]' . '" type="hidden" value="' . $value['starttime'] . "\" />\r\n\t<input name=\"" . $name . '[end]' . '" type="hidden" value="' . $value['endtime'] . "\" />\r\n           </div>\r\n          <div class=\"btn-group " . ($small ? 'btn-group-sm' : '') . "\" style=\"padding-right:0;\"  >\r\n          \r\n\t<button style=\"width:240px\" class=\"btn btn-default daterange " . (!empty($time) ? 'daterange-time' : 'daterange-date') . '"  type="button"><span class="date-title">' . $str . "</span></button>\r\n        <button class=\"btn btn-default " . ($small ? 'btn-sm' : '') . '" " type="button" onclick="clearTime(this)" placeholder="' . $placeholder . "\"><i class=\"fa fa-remove\"></i></button>\r\n         </div>\r\n\t";
	return $s;
}

function mobileUrl($do = '', $query = NULL, $full = false)
{
	global $_W;
	global $_GPC;
	!$query && ($query = array());
	$dos = explode('/', trim($do));
	$routes = array();
	$routes[] = $dos[0];

	if (isset($dos[1])) {
		$routes[] = $dos[1];
	}

	if (isset($dos[2])) {
		$routes[] = $dos[2];
	}

	if (isset($dos[3])) {
		$routes[] = $dos[3];
	}

	$r = implode('.', $routes);

	if (!empty($r)) {
		$query = array_merge(array('r' => $r), $query);
	}

	$query = array_merge(array('do' => 'mobile'), $query);
	$query = array_merge(array('m' => 'xg_agent'), $query);

	if (empty($query['mid'])) {
		$mid = intval($_GPC['mid']);

		if (!empty($mid)) {
			$query['mid'] = $mid;
		}
	}

	if ($full) {
		return $_W['siteroot'] . 'app/' . substr(murl('entry', $query, true), 2);
	}

	return murl('entry', $query, true);
}

function webUrl($do = '', $query = array(), $full = true)
{
	global $_W;
	global $_GPC;
	$dos = explode('/', trim($do));
	$routes = array();
	$routes[] = $dos[0];

	if (isset($dos[1])) {
		$routes[] = $dos[1];
	}

	if (isset($dos[2])) {
		$routes[] = $dos[2];
	}

	if (isset($dos[3])) {
		$routes[] = $dos[3];
	}

	$r = implode('.', $routes);

	if (!empty($r)) {
		$query = array_merge(array('r' => $r), $query);
	}

	$query = array_merge(array('do' => 'web'), $query);
	$query = array_merge(array('m' => 'xg_agent'), $query);

	if ($full) {
		return $_W['siteroot'] . 'web/' . substr(wurl('site/entry', $query), 2);
	}

	return wurl('site/entry', $query);
}

function vueurl($dir){
	global $_W;
	return $_W['siteroot'] .'addons/xg_agent/'.$dir.'?i='.$_W['uniacid'];
}

function my_scandir($dir)
{
	global $my_scenfiles;

	if ($handle = opendir($dir)) {
		while (($file = readdir($handle)) !== false) {
			if (($file != '..') && ($file != '.')) {
				if (is_dir($dir . '/' . $file)) {
					my_scandir($dir . '/' . $file);
				}
				else {
					$my_scenfiles[] = $dir . '/' . $file;
				}
			}
		}

		closedir($handle);
	}
}

function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
	if ($code == 'UTF-8') {
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $string, $t_string);

		if ($sublen < (count($t_string[0]) - $start)) {
			return join('', array_slice($t_string[0], $start, $sublen));
		}

		return join('', array_slice($t_string[0], $start, $sublen));
	}

	$start = $start * 2;
	$sublen = $sublen * 2;
	$strlen = strlen($string);
	$tmpstr = '';
	$i = 0;

	while ($i < $strlen) {
		if (($start <= $i) && ($i < ($start + $sublen))) {
			if (129 < ord(substr($string, $i, 1))) {
				$tmpstr .= substr($string, $i, 2);
			}
			else {
				$tmpstr .= substr($string, $i, 1);
			}
		}

		if (129 < ord(substr($string, $i, 1))) {
			++$i;
		}

		++$i;
	}

	return $tmpstr;
}

function save_media($url, $enforceQiniu = false)
{
	static $com;

	if (!$com) {
		$com = com('qiniu');
	}

	if ($com) {
		$qiniu_url = $com->save($url, NULL, $enforceQiniu);

		if (empty($qiniu_url)) {
			return $url;
		}

		return $qiniu_url;
	}

	return $url;
}

function tpl_form_field_category_3level($name, $parents, $children, $parentid, $childid, $thirdid)
{
	$html = "\r\n<script type=\"text/javascript\">\r\n\twindow._" . $name . ' = ' . json_encode($children) . ";\r\n</script>";

	if (!defined('TPL_INIT_CATEGORY_THIRD')) {
		$html .= "\t\r\n<script type=\"text/javascript\">\r\n\tfunction renderCategoryThird(obj, name){\r\n\t\tvar index = obj.options[obj.selectedIndex].value;\r\n\t\trequire(['jquery', 'util'], function(\$, u){\r\n\t\t\t\$selectChild = \$('#'+name+'_child');\r\n                                                      \$selectThird = \$('#'+name+'_third');\r\n\t\t\tvar html = '<option value=\"0\">请选择二级分类</option>';\r\n                                                      var html1 = '<option value=\"0\">请选择三级分类</option>';\r\n\t\t\tif (!window['_'+name] || !window['_'+name][index]) {\r\n\t\t\t\t\$selectChild.html(html); \r\n                                                                        \$selectThird.html(html1);\r\n\t\t\t\treturn false;\r\n\t\t\t}\r\n\t\t\tfor(var i=0; i< window['_'+name][index].length; i++){\r\n\t\t\t\thtml += '<option value=\"'+window['_'+name][index][i]['id']+'\">'+window['_'+name][index][i]['name']+'</option>';\r\n\t\t\t}\r\n\t\t\t\$selectChild.html(html);\r\n                                                    \$selectThird.html(html1);\r\n\t\t});\r\n\t}\r\n        function renderCategoryThird1(obj, name){\r\n\t\tvar index = obj.options[obj.selectedIndex].value;\r\n\t\trequire(['jquery', 'util'], function(\$, u){\r\n\t\t\t\$selectChild = \$('#'+name+'_third');\r\n\t\t\tvar html = '<option value=\"0\">请选择三级分类</option>';\r\n\t\t\tif (!window['_'+name] || !window['_'+name][index]) {\r\n\t\t\t\t\$selectChild.html(html);\r\n\t\t\t\treturn false;\r\n\t\t\t}\r\n\t\t\tfor(var i=0; i< window['_'+name][index].length; i++){\r\n\t\t\t\thtml += '<option value=\"'+window['_'+name][index][i]['id']+'\">'+window['_'+name][index][i]['name']+'</option>';\r\n\t\t\t}\r\n\t\t\t\$selectChild.html(html);\r\n\t\t});\r\n\t}\r\n</script>\r\n\t\t\t";
		define('TPL_INIT_CATEGORY_THIRD', true);
	}

	$html .= "<div class=\"row row-fix tpl-category-container\">\r\n\t<div class=\"col-xs-12 col-sm-4 col-md-4 col-lg-4\">\r\n\t\t<select class=\"form-control tpl-category-parent\" id=\"" . $name . '_parent" name="' . $name . '[parentid]" onchange="renderCategoryThird(this,\'' . $name . "')\">\r\n\t\t\t<option value=\"0\">请选择一级分类</option>";
	$ops = '';

	foreach ($parents as $row) {
		$html .= "\r\n\t\t\t<option value=\"" . $row['id'] . '" ' . ($row['id'] == $parentid ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
	}

	$html .= "\r\n\t\t</select>\r\n\t</div>\r\n\t<div class=\"col-xs-12 col-sm-4 col-md-4 col-lg-4\">\r\n\t\t<select class=\"form-control tpl-category-child\" id=\"" . $name . '_child" name="' . $name . '[childid]" onchange="renderCategoryThird1(this,\'' . $name . "')\">\r\n\t\t\t<option value=\"0\">请选择二级分类</option>";
	if (!empty($parentid) && !empty($children[$parentid])) {
		foreach ($children[$parentid] as $row) {
			$html .= "\r\n\t\t\t<option value=\"" . $row['id'] . '"' . ($row['id'] == $childid ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
		}
	}

	$html .= "\r\n\t\t</select> \r\n\t</div> \r\n                  <div class=\"col-xs-12 col-sm-4 col-md-4 col-lg-4\">\r\n\t\t<select class=\"form-control tpl-category-child\" id=\"" . $name . '_third" name="' . $name . "[thirdid]\">\r\n\t\t\t<option value=\"0\">请选择三级分类</option>";
	if (!empty($childid) && !empty($children[$childid])) {
		foreach ($children[$childid] as $row) {
			$html .= "\r\n\t\t\t<option value=\"" . $row['id'] . '"' . ($row['id'] == $thirdid ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
		}
	}

	$html .= "</select>\r\n\t</div>\r\n</div>";
	return $html;
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!function_exists('dump')) {
	function dump()
	{
		$args = func_get_args();

		foreach ($args as $val) {
			echo '<pre style="color: red">';
			var_dump($val);
			echo '</pre>';
		}
	}
}

$my_scenfiles = array();

?>
