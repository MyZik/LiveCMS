<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.textFilter
 * @license See attached file LICENSE.textFilter
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$title = $lang['exit']; // Заголовок страницы
$module = 'exit'; // Модуль

/**
 * Проверка наличия авторизации
 */
if (!isset($user)) {
    require_once(HOME .'/includes/header.php');
    Core::onlyUsers('/');
    require_once(HOME .'/includes/footer.php');
}

require_once(HOME . '/includes/header.php'); // подключаем шапку

/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li class="active">' . $lang['exit'] . '</li>' .
	 '</ul>';

if (isset($_GET['yes'])) {
	/**
     * Удаление личных данных
     */

	setcookie("user_id", "", time() - 3600, '/');
	setcookie("password", "", time() - 3600, '/');
	session_destroy();
	unset($user);

	echo Functions::display_message($lang['log_out_successful'], '/');
	require_once(HOME . '/includes/footer.php');
} else {
	echo '<div class="alert alert-danger">' . $lang['exit_info'] . '<br />
	<p><a href="?yes"><button type="button" class="btn btn-primary btn-sm" name="exit">' . $lang['exit_yes'] . '</button></a> <a href="/"><button type="button" class="btn btn-default btn-sm" name="homepage">' . $lang['exit_no'] . '</button></a></p></div>';
}

require_once(HOME .'/includes/footer.php');
