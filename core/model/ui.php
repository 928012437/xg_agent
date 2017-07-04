<?php

class Ui_XgAgentModel
{
	public function lazy($html = '')
	{
		$html = preg_replace_callback('/<img.*?src=[\\\\\'| \\"](.*?(?:[\\.gif|\\.jpg|\\.png|\\.jpeg]?))[\\\\\'|\\"].*?[\\/]?>/', function($matches) {
			return preg_replace('/src\\=/', 'data-lazy=', $matches[0]);
		}, $html);
		return $html;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
