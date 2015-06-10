<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

class Core {
	public static $timeGen; // Генерация
	public static $ip; // IP-адрес
	public static $browser; // Браузер
	public static $lang_iso; // Двухбуквенный код языка
	public static $lang_list = array(); // Список языков
	public static $lang = array(); // Язык сайта
	
	function __construct() {
		global $db;
		self::$timeGen = microtime(1);
		$ip = $db->quote($_SERVER['REMOTE_ADDR']);
		$browser = $db->quote($_SERVER['HTTP_USER_AGENT']);
	}
	
	/**
	 * Функция шифровки MD5 + BASE64
	 */
	public static function encrypt($var) {
		return md5(base64_encode($var) . '_LiveCMS');
	}
	
	/**
	 * Функция вывода ошибок
	 */
	 public static function display_error($var = '', $backLink = '') {
	 	global $lang;
	 	if (!empty($var)) {
	 		return '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"><b>' . $lang['error'] . '!</b><br /> ' .
	 		(is_array($var) ? implode('<br />', $var) : $var) . '' .
	 		(!empty($link) ? '<p>' . $link . '</p>' : '') . '</div>';
	 	} else {
	 		return FALSE;
	 	}
	 }
	 
	/**
	 * Фильтрация числовых данных перед записью в БД
	 */
	public static function num($var) {
		return abs(intval($var));
	}
	
	/**
	 * Безопасный вывод текстовой информации
	 */
	public static function textFilter($var) {
		return htmlspecialchars(trim($var));
	}
	
	/**
	 * Фильтрация текстовых данных перед записью в БД
	 **/
	public static function inputDB($var) {
		global $db;
		return $db->quote($var);
	}
	
	/**
	 * Функция подключения языкового пакета
	 * Аналогичная функция взята с CMS JohnCMS 4.x.x (http://johncms.com)
	 */
	public static function load_lang($module = '_core') {
		global $cms_set, $set_user;
		$lang_iso = $set_user['language'];
		if (!is_dir(HOME . '/includes/languages/' . $lang_iso))
			$lang_iso = $cms_set['language'];
		$lang_file = HOME . '/includes/languages/' . $lang_iso . '/' . $module . '.lang';
		if (file_exists($lang_file)) {
			$out = parse_ini_file($lang_file) or die('ОШИБКА! Файл языкового пакета не найден');
			return $out;
		}
		echo '<div class="alert alert-danger">Language file <b>' . HOME . '/incfiles/languages/' . $lang_iso . '/' . $module . '.lang</b> is missing</div>';
		return FALSE;
	}
	
	public static function onlyUsers($backLink = '') {
		global $lang;
			echo '<div class="alert alert-danger">' . $lang['only_users'] . '</div>';
			echo '<div class="list-group">' .
				 '<a class = "list-group-item" href="' . $backLink . '"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
				 '</div>';
	}
}
