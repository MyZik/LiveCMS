<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

/**
 * Системные настройки
 */
ini_set("display_errors","1"); // Показ ошибок
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
mb_internal_encoding('UTF-8'); // Кодировка по умолчанию

/**
 * Инициализация PDO, подключение к БД
 */
include '/includes/db_ini.php';
$db = new PDO('mysql:host=' . $mysql['host'] .';dbname=' . $mysql['base'], $mysql['user'], $mysql['pass'], array(
		PDO::ATTR_PERSISTENT => true
)) or die('Cannot connect to MySQL server :(');
$db->query("SET NAMES utf8");

/**
 * Загружаем классы
 */
spl_autoload_register(function($name) {
	$file = dirname(__DIR__) . '/includes/classes/' . $name . '.php';
	if(!file_exists($file)) {
		throw new Exception('Autoload class: File '.$file.' not found');
	}

	require $file;
});


/**
 * Системные переменные и константы
 */
define('HOME', dirname(__DIR__)); // Серверный путь к сайту
define('URL', 'http://' . $_SERVER['HTTP_HOST']); // URL-путь к сайту
define('LiveCMS_VERSION', '2.0 Alpha'); // Версия движка (!НЕ МЕНЯТЬ!)

$core = new Core();
$functions = new Functions();
$timeGen = Core::$timeGen;
$ip = Core::$ip;
$browser = Core::$browser;

/**
 * Получаем файлы из /includes/autoload/
 **/
$dir = opendir(HOME .'/includes/autoload/'); // Папка с нашими доп. функциями
while ($file = readdir($dir)) {
	if (preg_match('/\.php$/i', $file))
		require_once(HOME . '/includes/autoload/'. $file);
}


$cms_set = $db->query("SELECT * FROM `cms_settings` WHERE `id` = '1'")->fetch();
if (isset($user)) {
	// Получаем настройки пользователя
	$set_user = $db->query("SELECT * FROM `user_settings` WHERE `user_id` = '" . $user['id'] . "'")->fetch();
	$countMess = (int)$set_user['num_pages']; // Число сообщений на страницу
} else {
	// Системные настройки для гостей
	$set_user['num_pages'] = $cms_set['num_pages']; // Кол-во пунктов на страницу
	$set_user['sex_view'] = $cms_set['sex_view']; // Показ пола иконками (icons) или текстом (text)
	$set_user['theme'] = $cms_set['theme']; // Тема оформления
	$set_user['language'] = $cms_set['language']; // Язык
	$countMess = $set_user['num_pages'];
}

$lang_iso = $set_user['language']; // Двухбуквенный код языка
$lang_list = array(); // Список языков
$lang = array(); // Фразы языка

$lang = $core->load_lang(); // Загружаем установленный язык

$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
$start = isset($_REQUEST['page']) ? $page * $countMess - $countMess : (isset($_GET['start']) ? abs(intval($_GET['start'])) : 0);
