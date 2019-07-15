<?php

class Tools {

	const FLAG_NUMERIC = 1;
	const FLAG_NO_NUMERIC = 2;
	const FLAG_ALPHANUMERIC = 3;

	/**
	 * 生成随机密码
	 *
	 * @param integer $length Desired length (optional)
	 * @param string  $flag   Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC)
	 *
	 * @return string Password
	 */
	public static function passwdGen($length = 8, $flag = self::FLAG_NO_NUMERIC) {
		switch ($flag)
		{
			case self::FLAG_NUMERIC:
				$str = '0123456789';
				break;
			case self::FLAG_NO_NUMERIC:
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case self::FLAG_ALPHANUMERIC:
			default:
				$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}

		for ($i = 0, $passwd = ''; $i < $length; $i++)
			$passwd .= Tools::substr($str, mt_rand(0, Tools::strlen($str) - 1), 1);

		return $passwd;
	}

	/**
	 * 替换第一次出现的字符串
	 *
	 * @param     $search
	 * @param     $replace
	 * @param     $subject
	 * @param int $cur
	 *
	 * @return mixed
	 */
	public static function strReplaceFirst($search, $replace, $subject, $cur = 0) {
		return (strpos($subject, $search, $cur)) ? substr_replace($subject, $replace, (int)strpos($subject, $search, $cur), strlen($search)) : $subject;
	}

	/**
	 * 跳转
	 *
	 * @param      $url
	 * @param null $headers
	 */
	public static function redirect($url, $headers = null) {
		if (!empty($url))
		{
			if ($headers)
			{
				if (!is_array($headers))
					$headers = array($headers);

				foreach ($headers as $header)
					header($header);
			}

			header('Location: ' . $url);
			exit;
		}
	}


	public static function getRandNums($lenth){
		$char = '0123456789';
		$return ="";
		for($i = 1 ;$i <= $lenth ; ++ $i){
			$return .= $char[rand(0,9)];
		}
		return $return;
	}

	/**
	 * 清理URL中的http头
	 *
	 * @param      $url
	 * @param bool $cleanall
	 *
	 * @return mixed|string
	 */
	public static function cleanUrl($url, $cleanall = true) {
		if (strpos($url, 'http://') !== false)
		{
			if ($cleanall)
			{
				return '/';
			}
			else
			{
				return str_replace('http://', '', $url);
			}
		}

		return $url;
	}

	/**
	 * 获取当前域名
	 *
	 * @param bool $http
	 * @param bool $entities
	 *
	 * @return string
	 */
	public static function getHttpHost($http = false, $entities = false) {
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($entities)
			$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http)
		{
			$host = self::getCurrentUrlProtocolPrefix() . $host;
		}

		return $host;
	}

	/**
	 * 获取当前服务器名
	 *
	 * @return mixed
	 */
	public static function getServerName() {
		if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
			return $_SERVER['HTTP_X_FORWARDED_SERVER'];

		return $_SERVER['SERVER_NAME'];
	}

	/**
	 * 获取用户IP地址
	 *
	 * @return mixed
	 */
	public static function getRemoteAddr() {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR']))))
		{
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ','))
			{
				$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				return $ips[0];
			}
			else
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * 获取用户来源地址
	 *
	 * @return null
	 */
	public static function getReferer() {
		if (isset($_SERVER['HTTP_REFERER']))
		{
			return $_SERVER['HTTP_REFERER'];
		}
		else
		{
			return null;
		}
	}

	/**
	 * 判断是否使用了HTTPS
	 *
	 * @return bool
	 */
	public static function usingSecureMode() {
		if (isset($_SERVER['HTTPS']))
			return ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
		if (isset($_SERVER['SSL']))
			return ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');

		return false;
	}

	/**
	 * 获取当前URL协议
	 *
	 * @return string
	 */
	public static function getCurrentUrlProtocolPrefix() {
		if (Tools::usingSecureMode())
			return 'https://';
		else
			return 'http://';
	}

	/**
	 * 判断是否本站链接
	 *
	 * @param $referrer
	 *
	 * @return string
	 */
	public static function secureReferrer($referrer) {
		if (preg_match('/^http[s]?:\/\/' . Tools::getServerName() . '(:443)?\/.*$/Ui', $referrer))
			return $referrer;

		return '/';
	}

	/**
	 * 获取POST或GET的指定字段内容
	 *
	 * @param      $key
	 * @param bool $default_value
	 *
	 * @return bool|string
	 */
	public static function getValue($key, $default_value = false) {
		if (!isset($key) || empty($key) || !is_string($key))
			return false;
		$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

		if (is_string($ret) === true)
			$ret = trim(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));

		return !is_string($ret) ? $ret : stripslashes($ret);
	}

	/**
	 * 判断POST或GET中是否包含指定字段
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public static function getIsset($key) {
		if (!isset($key) || empty($key) || !is_string($key))
			return false;

		return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
	}

	/**
	 * 判断是否为提交操作
	 *
	 * @param $submit
	 *
	 * @return bool
	 */
	public static function isSubmit($submit) {
		return (isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y']) || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y']));
	}

	/**
	 * 过滤HTML内容后返回
	 *
	 * @param      $string
	 * @param bool $html
	 *
	 * @return array|string
	 */
	public static function safeOutput($string, $html = false) {
		if (!$html)
			$string = strip_tags($string);

		return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
	}

	public static function htmlentitiesUTF8($string, $type = ENT_QUOTES) {
		if (is_array($string))
			return array_map(array('Tools', 'htmlentitiesUTF8'), $string);

		return htmlentities((string)$string, $type, 'utf-8');
	}

	public static function htmlentitiesDecodeUTF8($string) {
		if (is_array($string))
			return array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $string);

		return html_entity_decode((string)$string, ENT_QUOTES, 'utf-8');
	}

	/**
	 * 对POST内容进行处理
	 *
	 * @return array
	 */
	public static function safePostVars() {
		if (!is_array($_POST))
			return array();
		$_POST = array_map(array('Tools', 'htmlentitiesUTF8'), $_POST);
	}

	/**
	 * 删除文件夹
	 *
	 * @param      $dirname
	 * @param bool $delete_self
	 */
	public static function deleteDirectory($dirname, $delete_self = true) {
		$dirname = rtrim($dirname, '/') . '/';
		if (is_dir($dirname))
		{
			$files = scandir($dirname);
			foreach ($files as $file)
				if ($file != '.' && $file != '..' && $file != '.svn')
				{
					if (is_dir($dirname . $file))
						Tools::deleteDirectory($dirname . $file, true);
					elseif (file_exists($dirname . $file))
						unlink($dirname . $file);
				}
			if ($delete_self)
				rmdir($dirname);
		}
	}

	/**
	 * 显示错误信息
	 *
	 * @param string $string
	 * @param array  $error
	 * @param bool   $htmlentities
	 *
	 * @return mixed|string
	 */
	public static function displayError($string = 'Fatal error', $error = array(), $htmlentities = true) {
		if (DEBUG_MODE)
		{
			if (!is_array($error) || empty($error))
				return str_replace('"', '&quot;', $string) . ('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
			$key = md5(str_replace('\'', '\\\'', $string));
			$str = (isset($error) AND is_array($error) AND key_exists($key, $error)) ? ($htmlentities ? htmlentities($error[$key], ENT_COMPAT, 'UTF-8') : $error[$key]) : $string;

			return str_replace('"', '&quot;', stripslashes($str));
		}
		else
		{
			return str_replace('"', '&quot;', $string);
		}
	}

	/**
	 * 打印出对象的内容
	 *
	 * @param      $object
	 * @param bool $kill
	 *
	 * @return mixed
	 */
	public static function dieObject($object, $kill = true) {
		echo '<pre style="text-align: left;">';
		print_r($object);
		echo '</pre><br />';
		if ($kill)
			die('END');

		return ($object);
	}

	public static function encrypt($passwd) {
		return md5(_COOKIE_KEY_ . $passwd);
	}

	public static function getToken($string) {
		return !empty($string) ? Tools::encrypt($string) : false;
	}

	/**
	 * 截取字符串，支持中文
	 *
	 * @param        $str
	 * @param        $max_length
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function truncate($str, $max_length, $suffix = '...') {
		if (Tools::strlen($str) <= $max_length)
			return $str;
		$str = utf8_decode($str);

		return (utf8_encode(substr($str, 0, $max_length - Tools::strlen($suffix)) . $suffix));
	}

	public static function replaceAccentedChars($str) {
		$patterns = array( /* Lowercase */
						   '/[\x{0105}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u',
						   '/[\x{00E7}\x{010D}\x{0107}]/u',
						   '/[\x{010F}]/u',
						   '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}]/u',
						   '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u',
						   '/[\x{0142}\x{013E}\x{013A}]/u',
						   '/[\x{00F1}\x{0148}]/u',
						   '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u',
						   '/[\x{0159}\x{0155}]/u',
						   '/[\x{015B}\x{0161}]/u',
						   '/[\x{00DF}]/u',
						   '/[\x{0165}]/u',
						   '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u',
						   '/[\x{00FD}\x{00FF}]/u',
						   '/[\x{017C}\x{017A}\x{017E}]/u',
						   '/[\x{00E6}]/u',
						   '/[\x{0153}]/u',
						   /* Uppercase */
						   '/[\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u',
						   '/[\x{00C7}\x{010C}\x{0106}]/u',
						   '/[\x{010E}]/u',
						   '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{011A}\x{0118}]/u',
						   '/[\x{0141}\x{013D}\x{0139}]/u',
						   '/[\x{00D1}\x{0147}]/u',
						   '/[\x{00D3}]/u',
						   '/[\x{0158}\x{0154}]/u',
						   '/[\x{015A}\x{0160}]/u',
						   '/[\x{0164}]/u',
						   '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}]/u',
						   '/[\x{017B}\x{0179}\x{017D}]/u',
						   '/[\x{00C6}]/u',
						   '/[\x{0152}]/u'
		);

		$replacements = array(
				'a',
				'c',
				'd',
				'e',
				'i',
				'l',
				'n',
				'o',
				'r',
				's',
				'ss',
				't',
				'u',
				'y',
				'z',
				'ae',
				'oe',
				'A',
				'C',
				'D',
				'E',
				'L',
				'N',
				'O',
				'R',
				'S',
				'T',
				'U',
				'Z',
				'AE',
				'OE'
		);

		return preg_replace($patterns, $replacements, $str);
	}

	public static function cleanNonUnicodeSupport($pattern) {
		if (!defined('PREG_BAD_UTF8_OFFSET'))
			return $pattern;

		return preg_replace('/\\\[px]\{[a-z]\}{1,2}|(\/[a-z]*)u([a-z]*)$/i', "$1$2", $pattern);
	}

	/**
	 * 生成年份
	 *
	 * @return array
	 */
	public static function dateYears() {
		$tab = array();

		for ($i = date('Y') - 10; $i >= 1900; $i--)
			$tab[] = $i;

		return $tab;
	}

	/**
	 * 生成日
	 *
	 * @return array
	 */
	public static function dateDays() {
		$tab = array();

		for ($i = 1; $i != 32; $i++)
			$tab[] = $i;

		return $tab;
	}

	/**
	 * 生成月
	 *
	 * @return array
	 */
	public static function dateMonths() {
		$tab = array();

		for ($i = 1; $i != 13; $i++)
			$tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));

		return $tab;
	}

	/**
	 * 根据时分秒生成时间字符串
	 *
	 * @param $hours
	 * @param $minutes
	 * @param $seconds
	 *
	 * @return string
	 */
	public static function hourGenerate($hours, $minutes, $seconds) {
		return implode(':', array($hours, $minutes, $seconds));
	}

	/**
	 * 一日之初
	 *
	 * @param $date
	 *
	 * @return string
	 */
	public static function dateFrom($date) {
		$tab = explode(' ', $date);
		if (!isset($tab[1]))
			$date .= ' ' . self::hourGenerate(0, 0, 0);

		return $date;
	}

	/**
	 * 一日之终
	 *
	 * @param $date
	 *
	 * @return string
	 */
	public static function dateTo($date) {
		$tab = explode(' ', $date);
		if (!isset($tab[1]))
			$date .= ' ' . self::hourGenerate(23, 59, 59);

		return $date;
	}

	/**
	 * 获取精准的时间
	 *
	 * @return int
	 */
	public static function getExactTime() {
		return microtime(true);
	}

	/**
	 * 转换成小写字符，支持中文
	 *
	 * @param $str
	 *
	 * @return bool|string
	 */
	public static function strtolower($str) {
		if (is_array($str))
			return false;
		if (function_exists('mb_strtolower'))
			return mb_strtolower($str, 'utf-8');

		return strtolower($str);
	}

	/**
	 * 转换为int类型
	 *
	 * @param $val
	 *
	 * @return int
	 */
	public static function intval($val) {
		if (is_int($val))
			return $val;
		if (is_string($val))
			return (int)$val;

		return (int)(string)$val;
	}

	/**
	 * 计算字符串长度
	 *
	 * @param        $str
	 * @param string $encoding
	 *
	 * @return bool|int
	 */
	public static function strlen($str, $encoding = 'UTF-8') {
		if (is_array($str) || is_object($str))
			return false;
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		if (function_exists('mb_strlen'))
			return mb_strlen($str, $encoding);

		return strlen($str);
	}

	public static function stripslashes($string) {
		if (get_magic_quotes_gpc())
			$string = stripslashes($string);

		return $string;
	}

	/**
	 * 转换成大写字符串
	 *
	 * @param $str
	 *
	 * @return bool|string
	 */
	public static function strtoupper($str) {
		if (is_array($str))
			return false;
		if (function_exists('mb_strtoupper'))
			return mb_strtoupper($str, 'utf-8');

		return strtoupper($str);
	}

	/**
	 * 截取字符串
	 *
	 * @param        $str
	 * @param        $start
	 * @param bool   $length
	 * @param string $encoding
	 *
	 * @return bool|string
	 */
	public static function substr($str, $start, $length = false, $encoding = 'utf-8') {
		if (is_array($str) || is_object($str))
			return false;
		if (function_exists('mb_substr'))
			return mb_substr($str, intval($start), ($length === false ? self::strlen($str) : intval($length)), $encoding);

		return substr($str, $start, ($length === false ? Tools::strlen($str) : intval($length)));
	}

	/**首字母大写
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public static function ucfirst($str) {
		return self::strtoupper(self::substr($str, 0, 1)) . self::substr($str, 1);
	}

	public static function nl2br($str) {
		return preg_replace("/((<br ?\/?>)+)/i", "<br />", str_replace(array("\r\n", "\r", "\n"), "<br />", $str));
	}

	public static function br2nl($str) {
		return str_replace("<br />", "\n", $str);
	}

	/**
	 * 判断是否真为空
	 *
	 * @param $field
	 *
	 * @return bool
	 */
	public static function isEmpty($field) {
		return ($field === '' || $field === null);
	}

	public static function ceilf($value, $precision = 0) {
		$precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precisionFactor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;

		return ceil($tmp) / $precisionFactor;
	}

	public static function floorf($value, $precision = 0) {
		$precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precisionFactor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;

		return floor($tmp) / $precisionFactor;
	}

	public static function replaceSpace($url) {
		return urlencode(strtolower(preg_replace('/[ ]+/', '-', trim($url, ' -/,.?'))));
	}

	/**
	 * 获取日期
	 *
	 * @param null $timestamp
	 *
	 * @return bool|string
	 */
	public static function getSimpleDate($timestamp = null) {
		if ($timestamp == null)
		{
			return date('Y-m-d');
		}
		else
		{
			return date('Y-m-d', $timestamp);
		}
	}

	/**
	 * 获取完整时间
	 *
	 * @param null $timestamp
	 *
	 * @return bool|string
	 */
	public static function getFullDate($timestamp = null) {
		if ($timestamp == null)
		{
			return date('Y-m-d H:i:s');
		}
		else
		{
			return date('Y-m-d H:i:s', $timestamp);
		}
	}

	/**
	 * 判断是否64位架构
	 *
	 * @return bool
	 */
	public static function isX86_64arch() {
		return (PHP_INT_MAX == '9223372036854775807');
	}

	/**
	 * 获取服务器配置允许最大上传文件大小
	 *
	 * @param int $max_size
	 *
	 * @return mixed
	 */
	public static function getMaxUploadSize($max_size = 0) {
		$post_max_size = Tools::convertBytes(ini_get('post_max_size'));
		$upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));
		if ($max_size > 0)
			$result = min($post_max_size, $upload_max_filesize, $max_size);
		else
			$result = min($post_max_size, $upload_max_filesize);

		return $result;
	}

	public static function convertBytes($value) {
		if (is_numeric($value))
			return $value;
		else
		{
			$value_length = strlen($value);
			$qty = (int)substr($value, 0, $value_length - 1);
			$unit = strtolower(substr($value, $value_length - 1));
			switch ($unit)
			{
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}

			return $qty;
		}
	}

	/**
	 * 获取内存限制
	 *
	 * @return int
	 */
	public static function getMemoryLimit() {
		$memory_limit = @ini_get('memory_limit');

		return Tools::getOctets($memory_limit);
	}

	public static function getOctets($option) {
		if (preg_match('/[0-9]+k/i', $option))
			return 1024 * (int)$option;

		if (preg_match('/[0-9]+m/i', $option))
			return 1024 * 1024 * (int)$option;

		if (preg_match('/[0-9]+g/i', $option))
			return 1024 * 1024 * 1024 * (int)$option;

		return $option;
	}

	/**
	 * 从array中取出指定字段
	 *
	 * @param $array
	 * @param $key
	 *
	 * @return array|null
	 */
	public static function simpleArray($array, $key) {
		if (!empty($array) && is_array($array))
		{
			$result = array();
			foreach ($array as $k => $item)
			{
				$result[$k] = $item[$key];
			}

			return $result;
		}

		return null;
	}

	public static function object2array(&$object) {
		return json_decode(json_encode($object), true);
	}

	public static function getmicrotime() {
		list($usec, $sec) = explode(" ", microtime());

		return floor($sec + $usec * 1000000);
	}

	/**
	 * 根据时间生成图片名
	 *
	 * @param string $image_type
	 *
	 * @return float|string
	 */
	public static function getTimeImageName($image_type = "image/jpeg") {
		if ($image_type == "image/jpeg" || $image_type == "image/pjpeg")
		{
			return self::getmicrotime() . ".jpg";
		}
		elseif ($image_type == "image/gif")
		{
			return self::getmicrotime() . ".gif";
		}
		elseif ($image_type == "image/png")
		{
			return self::getmicrotime() . ".png";
		}
		else
		{
			return self::getmicrotime();
		}
	}

	/**
	 * 日期计算
	 *
	 * @param $interval
	 * @param $step
	 * @param $date
	 *
	 * @return bool|string
	 */
	public static function dateadd($interval, $step, $date) {
		list($year, $month, $day) = explode('-', $date);
		if (strtolower($interval) == 'y')
		{
			return date('Y-m-d', mktime(0, 0, 0, $month, $day, intval($year) + intval($step)));
		}
		elseif (strtolower($interval) == 'm')
		{
			return date('Y-m-d', mktime(0, 0, 0, intval($month) + intval($step), $day, $year));
		}
		elseif (strtolower($interval) == 'd')
		{
			return date('Y-m-d', mktime(0, 0, 0, $month, intval($day) + intval($step), $year));
		}

		return date('Y-m-d');
	}

	public static function echo_microtime($tag) {
		list($usec, $sec) = explode(' ', microtime());
		echo $tag . ':' . ((float)$usec + (float)$sec) . "\n";
	}

	public static function redirectTo($link) {
		if (strpos($link, 'http') !== false)
		{
			header('Location: ' . $link);
		}
		else
		{
			header('Location: ' . Tools::getHttpHost(true) . '/' . $link);
		}
		exit;
	}

	public static function returnAjaxJson($array) {
		if (!headers_sent())
		{
			header("Content-Type: application/json; charset=utf-8");
		}
		echo(json_encode($array));
		ob_end_flush();
		exit;
	}

	public static function cmpWord($a, $b) {
		if ($a['word'] > $b['word'])
		{
			return 1;
		}
		elseif ($a['word'] == $b['word'])
		{
			return 0;
		}
		else
		{
			return -1;
		}
	}

	/**
	 * HackNews热度计算公式
	 *
	 * @param $time
	 * @param $viewcount
	 *
	 * @return float|int
	 */
	public static function getGravity($time, $viewcount) {
		$timegap = ($_SERVER['REQUEST_TIME'] - strtotime($time)) / 3600;
		if ($timegap <= 24)
		{
			return 999999;
		}

		return round((pow($viewcount, 0.8) / pow(($timegap + 24), 1.2)), 3) * 1000;
	}

	public static function getGravityS($stime, $viewcount) {
		$timegap = ($_SERVER['REQUEST_TIME'] - $stime) / 3600;
		if ($timegap <= 24)
		{
			return 999999;
		}

		return round((pow($viewcount, 0.8) / pow(($timegap + 24), 1.2)), 3) * 1000;
	}

	/**
	 * 优化的file_get_contents操作，超时关闭
	 *
	 * @param      $url
	 * @param bool $use_include_path
	 * @param null $stream_context
	 * @param int  $curl_timeout
	 *
	 * @return bool|mixed|string
	 */
	public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 8) {
		if ($stream_context == null && preg_match('/^https?:\/\//', $url))
			$stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout)));
		if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url))
			return @file_get_contents($url, $use_include_path, $stream_context);
		elseif (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			$opts = stream_context_get_options($stream_context);
			if (isset($opts['http']['method']) && Tools::strtolower($opts['http']['method']) == 'post')
			{
				curl_setopt($curl, CURLOPT_POST, true);
				if (isset($opts['http']['content']))
				{
					parse_str($opts['http']['content'], $datas);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
				}
			}
			$content = curl_exec($curl);
			curl_close($curl);

			return $content;
		}
		else
			return false;
	}

	public static function ZipTest($from_file) {
		$zip = new PclZip($from_file);

		return ($zip->privCheckFormat() === true);
		/*
		if (class_exists('ZipArchive', false)) {
			$zip = new ZipArchive();
			return ($zip->open($from_file, ZIPARCHIVE::CHECKCONS) === true);
		}
		else {
			$zip = new PclZip($from_file);
			return ($zip->privCheckFormat() === true);
		}
		*/
	}

	public static function ZipExtract($from_file, $to_dir) {
		if (!file_exists($to_dir))
			mkdir($to_dir, 0777);
		$zip = new PclZip($from_file);
		$list = $zip->extract(PCLZIP_OPT_PATH, $to_dir);

		return $list;
		/*
		if (class_exists('ZipArchive', false)) {
			$zip = new ZipArchive();
			if ($zip->open($from_file) === true && $zip->extractTo($to_dir) && $zip->close())
				return true;
			return false;
		}
		else {
			$zip = new PclZip($from_file);
			$list = $zip->extract(PCLZIP_OPT_PATH, $to_dir);
			foreach ($list as $file)
				if ($file['status'] != 'ok' && $file['status'] != 'already_a_directory')
					return false;
			return true;
		}
		*/
	}

	/**
	 * 获取文件扩展名
	 *
	 * @param $file
	 *
	 * @return mixed|string
	 */
	public static function getFileExtension($file) {
		if (is_uploaded_file($file))
		{
			return "unknown";
		}

		return pathinfo($file, PATHINFO_EXTENSION);
	}

	/**
	 * 以固定格式将数据及状态码返回手机端
	 *
	 * @param      $code
	 * @param      $data
	 * @param bool $native
	 */
	public static function returnMobileJson($code, $data, $native = false) {
		if (!headers_sent())
		{
			header("Content-Type: application/json; charset=utf-8");
		}
		if (is_array($data) && $native)
		{
			self::walkArray($data, 'urlencode', true);
			echo(urldecode(json_encode(array('code' => $code, 'data' => $data))));
		}
		elseif (is_string($data) && $native)
		{
			echo(urldecode(json_encode(array('code' => $code, 'data' => urlencode($data)))));
		}
		else
		{
			echo(json_encode(array('code' => $code, 'data' => $data)));
		}
		ob_end_flush();
		exit;
	}

	/**
	 * 遍历数组
	 *
	 * @param      $array
	 * @param      $function
	 * @param bool $keys
	 */
	public static function walkArray(&$array, $function, $keys = false) {
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				self::walkArray($array[$key], $function, $keys);
			}
			elseif (is_string($value))
			{
				$array[$key] = $function($value);
			}

			if ($keys && is_string($key))
			{
				$newkey = $function($key);
				if ($newkey != $key)
				{
					$array[$newkey] = $array[$key];
					unset($array[$key]);
				}
			}
		}
	}

	/**
	 * 遍历路径
	 *
	 * @param        $path
	 * @param string $ext
	 * @param string $dir
	 * @param bool   $recursive
	 *
	 * @return array
	 */
	public static function scandir($path, $ext = 'php', $dir = '', $recursive = false) {
		$path = rtrim(rtrim($path, '\\'), '/') . '/';
		$real_path = rtrim(rtrim($path . $dir, '\\'), '/') . '/';
		$files = scandir($real_path);
		if (!$files)
			return array();

		$filtered_files = array();

		$real_ext = false;
		if (!empty($ext))
			$real_ext = '.' . $ext;
		$real_ext_length = strlen($real_ext);

		$subdir = ($dir) ? $dir . '/' : '';
		foreach ($files as $file)
		{
			if (!$real_ext || (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length)))
				$filtered_files[] = $subdir . $file;

			if ($recursive && $file[0] != '.' && is_dir($real_path . $file))
				foreach (Tools::scandir($path, $ext, $subdir . $file, $recursive) as $subfile)
					$filtered_files[] = $subfile;
		}

		return $filtered_files;
	}

	public static function arrayUnique($array) {
		if (version_compare(phpversion(), '5.2.9', '<'))
			return array_unique($array);
		else
			return array_unique($array, SORT_REGULAR);
	}

	public static function arrayUnique2d($array, $keepkeys = true) {
		$output = array();
		if (!empty($array) && is_array($array))
		{
			$stArr = array_keys($array);
			$ndArr = array_keys(end($array));

			$tmp = array();
			foreach ($array as $i)
			{
				$i = join("¤", $i);
				$tmp[] = $i;
			}

			$tmp = array_unique($tmp);

			foreach ($tmp as $k => $v)
			{
				if ($keepkeys)
					$k = $stArr[$k];
				if ($keepkeys)
				{
					$tmpArr = explode("¤", $v);
					foreach ($tmpArr as $ndk => $ndv)
					{
						$output[$k][$ndArr[$ndk]] = $ndv;
					}
				}
				else
				{
					$output[$k] = explode("¤", $v);
				}
			}
		}

		return $output;
	}

	public static function sys_get_temp_dir() {
		if (function_exists('sys_get_temp_dir'))
		{
			return sys_get_temp_dir();
		}
		if ($temp = getenv('TMP'))
		{
			return $temp;
		}
		if ($temp = getenv('TEMP'))
		{
			return $temp;
		}
		if ($temp = getenv('TMPDIR'))
		{
			return $temp;
		}
		$temp = tempnam(__FILE__, '');
		if (file_exists($temp))
		{
			unlink($temp);

			return dirname($temp);
		}

		return null;
	}

	/**
	 * XSS
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function removeXSS($str) {
		$str = str_replace('<!--  -->', '', $str);
		$str = preg_replace('~/\*[ ]+\*/~i', '', $str);
		$str = preg_replace('/\\\0{0,4}4[0-9a-f]/is', '', $str);
		$str = preg_replace('/\\\0{0,4}5[0-9a]/is', '', $str);
		$str = preg_replace('/\\\0{0,4}6[0-9a-f]/is', '', $str);
		$str = preg_replace('/\\\0{0,4}7[0-9a]/is', '', $str);
		$str = preg_replace('/&#x0{0,8}[0-9a-f]{2};/is', '', $str);
		$str = preg_replace('/&#0{0,8}[0-9]{2,3};/is', '', $str);
		$str = preg_replace('/&#0{0,8}[0-9]{2,3};/is', '', $str);

		$str = htmlspecialchars($str);
		//$str = preg_replace('/&lt;/i', '<', $str);
		//$str = preg_replace('/&gt;/i', '>', $str);

		// 非成对标签
		$lone_tags = array("img", "param", "br", "hr");
		foreach ($lone_tags as $key => $val)
		{
			$val = preg_quote($val);
			$str = preg_replace('/&lt;' . $val . '(.*)(\/?)&gt;/isU', '<' . $val . "\\1\\2>", $str);
			$str = self::transCase($str);
			$str = preg_replace_callback('/<' . $val . '(.+?)>/i', create_function('$temp', 'return str_replace("&quot;","\"",$temp[0]);'), $str);
		}
		$str = preg_replace('/&amp;/i', '&', $str);

		// 成对标签
		$double_tags = array("table", "tr", "td", "font", "a", "object", "embed", "p", "strong", "em", "u", "ol", "ul", "li", "div", "tbody", "span", "blockquote", "pre", "b", "font");
		foreach ($double_tags as $key => $val)
		{
			$val = preg_quote($val);
			$str = preg_replace('/&lt;' . $val . '(.*)&gt;/isU', '<' . $val . "\\1>", $str);
			$str = self::transCase($str);
			$str = preg_replace_callback('/<' . $val . '(.+?)>/i', create_function('$temp', 'return str_replace("&quot;","\"",$temp[0]);'), $str);
			$str = preg_replace('/&lt;\/' . $val . '&gt;/is', '</' . $val . ">", $str);
		}
		// 清理js
		$tags = Array(
				'javascript',
				'vbscript',
				'expression',
				'applet',
				'meta',
				'xml',
				'behaviour',
				'blink',
				'link',
				'style',
				'script',
				'embed',
				'object',
				'iframe',
				'frame',
				'frameset',
				'ilayer',
				'layer',
				'bgsound',
				'title',
				'base',
				'font'
		);

		foreach ($tags as $tag)
		{
			$tag = preg_quote($tag);
			$str = preg_replace('/' . $tag . '\(.*\)/isU', '\\1', $str);
			$str = preg_replace('/' . $tag . '\s*:/isU', $tag . '\:', $str);
		}

		$str = preg_replace('/[\s]+on[\w]+[\s]*=/is', '', $str);

		Return $str;
	}

	public static function transCase($str) {
		$str = preg_replace('/(e|ｅ|Ｅ)(x|ｘ|Ｘ)(p|ｐ|Ｐ)(r|ｒ|Ｒ)(e|ｅ|Ｅ)(s|ｓ|Ｓ)(s|ｓ|Ｓ)(i|ｉ|Ｉ)(o|ｏ|Ｏ)(n|ｎ|Ｎ)/is', 'expression', $str);

		Return $str;
	}

	/**
	 * @param        $url
	 * @param string $method
	 * @param null   $postFields
	 * @param null   $header
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function curl($url, $method = 'GET', $postFields = null, $header = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https")
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		switch ($method)
		{
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				if (!empty($postFields))
				{
					if (is_array($postFields) || is_object($postFields))
					{
						if (is_object($postFields))
							$postFields = Tools::object2array($postFields);
						$postBodyString = "";
						$postMultipart = false;
						foreach ($postFields as $k => $v)
						{
							if ("@" != substr($v, 0, 1))
							{ //判断是不是文件上传
								$postBodyString .= "$k=" . urlencode($v) . "&";
							}
							else
							{ //文件上传用multipart/form-data，否则用www-form-urlencoded
								$postMultipart = true;
							}
						}
						unset($k, $v);
						if ($postMultipart)
						{
							curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
						}
						else
						{
							curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
						}
					}
					else
					{
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
					}

				}
				break;
			default:
				if (!empty($postFields) && is_array($postFields))
					$url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($postFields);
				break;
		}
		curl_setopt($ch, CURLOPT_URL, $url);

		if (!empty($header) && is_array($header))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		$response = curl_exec($ch);
		if (curl_errno($ch))
		{
			throw new Exception(curl_error($ch), 0);
		}
		curl_close($ch);

		return $response;
	}

	/**
	 * 下载文件保存到指定位置
	 *
	 * @param $url
	 * @param $filepath
	 *
	 * @return bool
	 */
	public static function saveFile($url, $filepath) {
		if (Validate::isAbsoluteUrl($url) && !empty($filepath))
		{
			$file = self::file_get_contents($url);
			$fp = @fopen($filepath, 'w');
			if ($fp)
			{
				@fwrite($fp, $file);
				@fclose($fp);

				return $filepath;
			}
		}

		return false;
	}

	/**
	 * 文件复制
	 *
	 * @param $source
	 * @param $dest
	 *
	 * @return bool
	 */
	public static function copyFile($source, $dest) {
		if (file_exists($dest) || is_dir($dest))
		{
			return false;
		}

		return copy($source, $dest);
	}

	/**
	 * 判断是否爬虫，范围略大
	 *
	 * @return bool
	 */
	public static function isSpider() {
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
			$spiders = array('spider', 'bot');
			foreach ($spiders as $spider)
			{
				if (strpos($ua, $spider) !== false)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * 判断是否命令行执行
	 *
	 * @return bool
	 */
	public static function isCli() {
		if (isset($_SERVER['SHELL']) && !isset($_SERVER['HTTP_HOST']))
		{
			return true;
		}

		return false;
	}

    public static function checkPassword($password){
        //1、密码不能有空格
        if(preg_match("/ /",$password)){
            return false;
        }
        $len = strlen($password);
        //2、长度在6-16之间
        if($len<6||$len>16){
            return false;
        }
        return true;
    }


    /**
     * 判断输入的字符串是否是一个合法的手机号(仅限中国大陆)
     *
     * @param string $string
     * @return boolean
     */
    public static function isMobile($string)
    {
        return ctype_digit($string) && (11 == strlen($string)) && ($string[0] == 1);
    }

    /**
     * 向客户端发送一段Javascript消息
     *
     * @param string $message
     */
    public static function  echoJs($message){
                echo <<<EOF
            <script type='text/javascript'>
            {$message}
            </script>
EOF;

    }
    /**
     * 向客户端发送一段Js之后终止
     *
     * @param string $message
     */
    public static  function dieJs($message)
    {
      Tools:: echoJs($message);
        die;
    }






	public static function sendToBrowser($file, $delaftersend = true, $exitaftersend = true) {
		if (file_exists($file) && is_readable($file))
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment;filename = ' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			if ($delaftersend)
			{
				unlink($file);
			}
			if ($exitaftersend)
			{
				exit;
			}
		}
	}

    /**
     * 对一张图片进行缩放
     *
     * @param string $srcImageFile
     * @param string $destImageFile
     * @param integer $width
     * @param integer $height
     * @return boolean
     */
    public static function resizeImage($srcImageFile, $destImageFile, $width, $height)
    {
        $srcImage   = self::imageCreateFromFile($srcImageFile);
        if (empty($srcImage))   return false;

        $extName    = self::getExtFileName($destImageFile);
        if (empty($extName))    return false;

        $extName    = strtolower($extName);
        if ($extName === 'jpg' || $extName === 'jpeg')
        {
            $type   = IMAGETYPE_JPEG;
        } else if ($extName === 'gif') {
            $type   = IMAGETYPE_GIF;
        } else if ($extName === 'png') {
            $type   = IMAGETYPE_PNG;
        } else  return false;

        $destImage  = imagecreatetruecolor($width, $height);
        imagefilledrectangle($destImage, 0, 0, $width, $height, imagecolorallocate($destImage, 255, 255, 255));
        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $width, $height, imagesx($srcImage), imagesy($srcImage));
        switch($type)
        {
            case IMAGETYPE_PNG:
                return imagepng($destImage, $destImageFile, 2);
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                return imagejpeg($destImage, $destImageFile, 98);
            case IMAGETYPE_GIF:
                return imagegif($destImage, $destImageFile);
        }
        return false;
    }

    /**
     * 通过文件创建GD图片
     *
     * @param string $imgFile
     * @return resource
     */
    public static function imageCreateFromFile($imgFile)
    {
        if (!file_exists($imgFile) || !is_readable($imgFile))
        {
            return null;
        }

        $imgInfo    = getimagesize($imgFile);
        if (empty($imgFile))    return null;
        switch ($imgInfo[2])
        {
            case IMAGETYPE_GIF:
                return imagecreatefromgif($imgFile);
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                return imagecreatefromjpeg($imgFile);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($imgFile);
        }
        return null;
    }

    /**
     * 获取文件的扩展名
     *
     * @param string $fileName
     * @return string
     */
    public static function getExtFileName($fileName)
    {
        $lastDotPos = strrpos($fileName, '.');
        if ($lastDotPos === false)
        {
            return false;
        }
        return substr($fileName, $lastDotPos + 1);
    }


    /**
     * 首字母
     * @param $string
     * @return string
     */
    public static function shouZiMu($string){
        $result="";
        preg_match_all("/./u", $string, $arr);
        foreach($arr as $row){
            foreach($row as $char){
                $result.=substr(self::Pinyin($char,1),0,1);
            }
        }
        return strtoupper($result);
    }


    /**
     * 拼音
     * @param $_String
     * @param string $_Code
     * @return mixed
     */
    public static function Pinyin($_String, $_Code='gb2312')
    {
        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
            "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
            "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
            "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
            "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
            "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
            "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
            "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
            "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
            "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
            "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
            "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
            "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
            "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
            "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
            "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
            "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
            "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
            "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
            "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
            "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
            "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
            "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
            "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
            "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
            "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
            "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
            "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
            "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
            "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
            "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
            "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
            "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
            "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
            "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
            "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
            "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
            "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
            "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
            "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
            "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
            "|-10270|-10262|-10260|-10256|-10254";
        $_TDataKey = explode('|', $_DataKey);
        $_TDataValue = explode('|', $_DataValue);
        $_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : _Array_Combine($_TDataKey, $_TDataValue);
        arsort($_Data);
        reset($_Data);
        if($_Code != 'gb2312') $_String = self::_U2_Utf8_Gb($_String);
        $_Res = '';
        for($i=0; $i<strlen($_String); $i++)
        {
            $_P = ord(substr($_String, $i, 1));
            if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
            $_Res .= self::_Pinyin($_P, $_Data);
        }
        return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }
    public static function _Pinyin($_Num, $_Data)
    {
        if ($_Num>0 && $_Num<160 ) return chr($_Num);
        elseif($_Num<-20319 || $_Num>-10247) return '';
        else {
            foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
            return $k;
        }
    }
    public static  function _U2_Utf8_Gb($_C)
    {
        $_String = '';
        if($_C < 0x80) $_String .= $_C;
        elseif($_C < 0x800)
        {
            $_String .= chr(0xC0 | $_C>>6);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x10000){
            $_String .= chr(0xE0 | $_C>>12);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        } elseif($_C < 0x200000) {
            $_String .= chr(0xF0 | $_C>>18);
            $_String .= chr(0x80 | $_C>>12 & 0x3F);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }
        return iconv('UTF-8', 'GBK', $_String);
    }

    public static  function _Array_Combine($_Arr1, $_Arr2)
    {
        for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
        return $_Res;
    }

//将字符串转json
    public static function getJsonString($arr){
        $json = json_encode($arr);
        return  $json;
    }
    //将字符串转json
    public static function getArray($arr){
        $arr = json_decode($arr);
        return  $arr;
    }
    /**
     * 获取一个时间戳加随机数的文件名
     *
     * @param string $fileName
     * @return string 返回时间戳加随机数的文件名
     */
    public static function getUnixMtRandPath($fileName){
        $lastDotPos     = strrpos($fileName, '.');//最后一个 . 的位置
        if ($lastDotPos === false)
        {
            $extName        = '';
        } else {
            $extName        = substr($fileName, $lastDotPos);
        }
        $fileBasePath   = time() . '_'.mt_rand(0, 10000);
        return $fileBasePath . $extName;
    }
    /**
     *验证图片的格式
     *
     *
    */
    public static  function isValidFile($fileName)
    {
        $ext    = explode('.', $fileName);
        $ext_seg_num    = count($ext);
        if ($ext_seg_num <= 1)  return false;
        $ext    = strtolower($ext[$ext_seg_num - 1]);
        return in_array($ext, array( 'jpg', 'png', 'gif','pdf','zip','rar'));
    }

    /**
     * 判断一个字符串是否是一个Email地址
     *
     * @param string $string
     * @return boolean
     */
    public static function isEmail($string)
    {
        return preg_match('/^[a-z0-9.\-_]{2,64}@[a-z0-9]{1,32}(\.[a-z0-9]{2,5})+$/i', $string);
    }

    /**
     * 判断输入的字符串是否是一个合法的电话号码（仅限中国大陆）
     *
     * @param string $string
     * @return boolean
     */
    public static  function isPhone($string)
    {
        if (preg_match('/^0\d{2,3}-\d{7,8}$/', $string)||preg_match('/^0\d{2,3}-\d{7,8}-\d{1,6}$/', $string)) {
            return true;
        }
        return false;
    }
    /**
     * 判断一个字符串是否合法的邮编
     *
     * @param string $string
     * @return boolean
     */
    function isZip($string)
    {
        return strlen($string) === 6 && ctype_digit($string);
    }



    public static function array_search_re($needle, array $haystack, $a=0, $nodes_temp=array()){
        global $nodes_found;
        $a++;
        foreach ($haystack as $key1=>$value1) {
            $nodes_temp[$a] = $key1;
            if (is_array($value1)){
                self::array_search_re($needle, $value1, $a, $nodes_temp);
            }
            else if ($value1 === $needle){
                $nodes_found[] = $nodes_temp;
            }
        }
        return $nodes_found;
    }


    static function  ParseNumber($money = 0, $is_round = true, $int_unit = '圆') {
        $chs     = array (0, '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        $uni     = array ('', '拾', '佰', '仟' );
        $dec_uni = array ('角', '分' );
        $exp     = array ('','万','亿');
        $res     = '';
        // 以 元为单位分割
        $parts   = explode ( '.', $money, 2 );
        $int     = isset ( $parts [0] ) ? strval ( $parts [0] ) : 0;
        $dec     = isset ( $parts [1] ) ? strval ( $parts [1] ) : '';
        // 处理小数点
        $dec_len = strlen ( $dec );
        if (isset ( $parts [1] ) && $dec_len > 2) {
            $dec = $is_round ? substr ( strrchr ( strval ( round ( floatval ( "0." . $dec ), 2 ) ), '.' ), 1 ) : substr ( $parts [1], 0, 2 );
        }
        // number= 0.00时，直接返回 0
        if (empty ( $int ) && empty ( $dec )) {
            return '零';
        }

        // 整数部分 从右向左
        for($i = strlen ( $int ) - 1, $t = 0; $i >= 0; $t++) {
            $str = '';
            // 每4字为一段进行转化
            for($j = 0; $j < 4 && $i >= 0; $j ++, $i --) {
                $u   = $int{$i} > 0 ? $uni [$j] : '';
                $str = $chs [$int {$i}] . $u . $str;
            }
            $str = rtrim ( $str, '0' );
            $str = preg_replace ( "/0+/", "零", $str );
            $u2  = $str != '' ? $exp [$t] : '';
            $res = $str . $u2 . $res;
        }
        $dec = rtrim ( $dec, '0' );
        // 小数部分 从左向右
        if (!empty ( $dec )) {
            $res .= $int_unit;
            $cnt =  strlen ( $dec );
            for($i = 0; $i < $cnt; $i ++) {
                $u = $dec {$i} > 0 ? $dec_uni [$i] : ''; // 非0的数字后面添加单位
                $res .= $chs [$dec {$i}] . $u;
            }
            if ($cnt == 1) $res .= '整';
            $res = rtrim ( $res, '0' ); // 去掉末尾的0
            $res = preg_replace ( "/0+/", "零", $res ); // 替换多个连续的0
        } else {
            $res .= $int_unit . '整';
        }
        return $res;
    }

    public static function https_request($url,$data=NULL){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $post_data = http_build_query($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$post_data);
        curl_setopt($curl, CURLOPT_URL,$url);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


	public static function http_url_request($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}


	//对输入的内容预处理
    public static function inputPreparate($input){
        if(!isset($input)){
            return "";
        }else{
         return   htmlspecialchars(trim($input) , ENT_QUOTES, 'UTF-8');
        }
    }
    /**
     * 安全过滤类-过滤javascript,css,iframes,object等不安全参数 过滤级别高
     *  Controller中使用方法：$this->controller->fliter_script($value)
     * @param  string $value 需要过滤的值
     * @return string
     */
    public static function fliter_script($value) {
        $value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$value);
        $value = preg_replace("/(.*?)<\/script>/si","",$value);
        $value = preg_replace("/(.*?)<\/iframe>/si","",$value);
        $value = preg_replace ("//iesU", '', $value);
        return $value;
    }


    /**
     * 时间差计算
     *传入一个毫秒值  然后转换为当前时间
     */
    public static function  time2Units ($time)
    {

        $year   = floor($time / 60 / 60 / 24 / 365);
        $time  -= $year * 60 * 60 * 24 * 365;
        $month  = floor($time / 60 / 60 / 24 / 30);
        $time  -= $month * 60 * 60 * 24 * 30;
        $week   = floor($time / 60 / 60 / 24 / 7);
        $time  -= $week * 60 * 60 * 24 * 7;
        $day    = floor($time / 60 / 60 / 24);
        $time  -= $day * 60 * 60 * 24;
        $hour   = floor($time / 60 / 60);
        $time  -= $hour * 60 * 60;
        $minute = floor($time / 60);
        $time  -= $minute * 60;
        $second = $time;
        $elapse = '';

        $unitArr = array('年'  =>'year', '个月'=>'month',  '周'=>'week', '天'=>'day',
            '小时'=>'hour', '分钟'=>'minute', '秒'=>'second'
        );

        foreach ( $unitArr as $cn => $u )
        {
            if ( $$u > 0 )
            {
                $elapse = $$u . $cn;
                break;
            }
        }

        return $elapse;
    }

    public static function  quoteTime($now_time,$the_time,$this_time)
    {
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $this_time = strtotime($this_time);

        $dur = $now_time - $show_time;
        $result="";
        $s="";
        if($this_time == $show_time){
            $s =  '(本次)';
        }
        if($dur < 0){
            return '';
        }else{
            if($dur < 60){
                $result =  $dur.'秒前';
            }else{
                if($dur < 3600){
                    $result =  floor($dur/60).'分钟前';
                }else{
                    if($dur < 86400){
                        $result =  floor($dur/3600).'小时前';
                    }else{
                        $result =  date('m/d H:i',$show_time);
                    }
                }
            }
        }
        return $result.$s;
    }



    /**
     * 分页
     * @param $url
     * @param $perPage
     * @param $currentPage
     * @param $totalItems
     * @param int $delta
     * @param string $target
     * @param bool $isSearchIpt
     * @return string
     */
    public static function createPage($url, $perPage, $currentPage, $totalItems,$delta = 2, $target = '_self',$isSearchIpt = false)
    {
		$t_high = ceil($totalItems / $perPage) == 0 ? 1 :
			ceil($totalItems / $perPage);
		$high = $currentPage + $delta;
		$low = $currentPage - $delta;
		if ($high > $t_high)	{
			$high = $t_high;
			$low = $t_high - 2 * $delta;
		}
		if ($low < 1) {
			$low = 1;
			$high = $low + 2 * $delta;
			if($high > $t_high) $high = $t_high;
		}
		$offset = ($currentPage - 1) * $perPage + 1;
		if ($offset < 0) $offset = 0;
		$end = $offset + $perPage - 1;
		if($end > $totalItems) $end = $totalItems;

        $ret_string = ' <p class="totalitems tac gray fs14p">共找到'.$totalItems.'条信息</p><p class="page-wrap tac">';
        if($currentPage > 1)
        {
            $ret_string .= '<a class=\'blue linka\' href=\'' . str_replace('%d', 1, $url) . "' target='{$target}'>首页</a>";
            $ret_string .= '<a class=\'blue linka\' href=\'' . str_replace('%d', $currentPage - 1, $url) . "' target='{$target}' style='margin-right: -2px;'>上一页</a>";
        }
        else {
            $ret_string .= '<a class="disabled">首页</a>';
            $ret_string .= "<a class='disabled' style='margin-right: -2px;'>上一页</a>";
        }


		$links = array();
		for (;$low <= $high; $low++)
		{
			if($low != $currentPage) $links[] = '<a href=\'' . str_replace('%d', $low, $url) . '\' target=\'' . $target . '\'>' . $low . '</a>';
			else $links[] = "<span class='current'>{$low}</span>";
		}
		$links = implode('', $links);
		$ret_string .= "\r\n" . $links;



        if($currentPage < $t_high){
            $ret_string .= '<a href=\'' . str_replace('%d', $currentPage + 1, $url) . "' target='{$target}' class='blue linka' >下一页</a>";
            $ret_string .= '<a href=\'' . str_replace('%d', $t_high, $url) . '\' target=\'' . $target . '\' class=\'blue linka\' >尾页</a>';
        }
        else{
            $ret_string .= '<a class="disabled">下一页</a>';
            $ret_string .= '<a class="disabled">尾页</a>';
        }
        return $ret_string . '</p>';
    }

	function __toString()
	{
		return "";
	}

}





?>
