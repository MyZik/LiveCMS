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
 * Старт сессий
 */
session_name('sid');
session_start();

/**
 * Подключаем основной файл для работы с системой
 */
require_once 'includes/core.php';

/**
 * Подключаем модули
 */
if (isset($_GET['url'])) {
	$module_url = $_GET['url'];

	if (preg_match('/\.php$/i', $module_url))
		$module_file = true;
	else
		$module_file = false;
}


if (!empty($module_url)) // Проверяем наличие адреса модуля
{
	if (file_exists(HOME .'/modules/'. $module_url) && $module_file == true) // Проверяем, есть ли модуль с таким названием
	{
		require_once(HOME .'/modules/'. $module_url); // Подключаем
	}
	elseif (file_exists(HOME .'/modules/'. $module_url) && $module_file == false) // Если есть модуль, но нет такого файла
	{
		if (file_exists(HOME .'/modules/'. $module_url .'/index.php')) // Проверяем, есть ли главная страница модуля
		{
			require_once(HOME .'/modules/'. $module_url .'/index.php'); // Если есть, подключаем
		}
		else // Если нет модуля
		{
			header("Location: /404.php"); // Перенаправляем на страницу ошибки 404
			exit;
		}
	}
	else // Если модуля с таким названием нет
	{
		header("Location: /404.php"); // Перенаправляем на страницу ошибки 404
		exit;
	}
}
else // Если адрес модуля неверен
{
	require_once(HOME .'/modules/index.php');
}
