<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
class Util_XgAgentModel
{
	public function getExpressList($express, $expresssn)
	{
		$url = 'http://wap.kuaidi100.com/wap_result.jsp?rand=' . time() . '&id=' . $express . '&fromWeb=null&postid=' . $expresssn;
		load()->func('communication');
		$resp = ihttp_request($url);
		$content = $resp['content'];

		if (empty($content)) {
			return array();
		}

		preg_match_all('/\\<p\\>&middot;(.*)\\<\\/p\\>/U', $content, $arr);

		if (!isset($arr[1])) {
			return false;
		}

		$arr = $arr[1];
		$list = array();

		if ($arr) {
			$len = count($arr);
			$step1 = explode('<br />', str_replace('&middot;', '', $arr[0]));
			$step2 = explode('<br />', str_replace('&middot;', '', $arr[$len - 1]));
			$i = 0;

			while ($i < $len) {
				if (strtotime(trim($step2[0])) < strtotime(trim($step1[0]))) {
					$row = $arr[$i];
				}
				else {
					$row = $arr[$len - $i - 1];
				}

				$step = explode('<br />', str_replace('&middot;', '', $row));
				$list[] = array('time' => trim($step[0]), 'step' => trim($step[1]), 'ts' => strtotime(trim($step[0])));
				++$i;
			}
		}

		return $list;
	}

	public function getIpAddress()
	{
		$ipContent = file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js');
		$jsonData = explode('=', $ipContent);
		$jsonAddress = substr($jsonData[1], 0, -1);
		return $jsonAddress;
	}

	public function checkRemoteFileExists($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		$result = curl_exec($curl);
		$found = false;

		if ($result !== false) {
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if ($statusCode == 200) {
				$found = true;
			}
		}

		curl_close($curl);
		return $found;
	}
}



