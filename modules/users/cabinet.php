<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$title = $lang['my_cabinet']; // Заголовок страницы
$module = 'cabinet'; // Модуль

/**
 * Проверка наличия авторизации
 */
if (!isset($user)) {
	require_once(HOME .'/includes/header.php');
	echo Core::onlyUsers();
    require_once(HOME .'/includes/footer.php');
}

require_once(HOME .'/includes/header.php'); // Подключаем шапку

/**
 * Счетчики
 */
$contacts = $db->query("SELECT COUNT(*) FROM `cms_contacts` WHERE `user_id` = '" . $user['id'] . "'")->fetchColumn(); // Контакты
$mails = $db->query("SELECT COUNT(*) FROM `cms_mail` WHERE `user_id` = '" . $user['id'] . "'")->fetchColumn(); // Кол-во сообщений
$journal = $db->query("SELECT COUNT(*) FROM `journal` WHERE `user_id` = '" . $user['id'] . "'")->fetchColumn(); // Кол-во событий в журнале


/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li class="active">' . $lang['my_cabinet'] . '</li>' .
	 '</ul>';

/**
 * Приветствие
 */
echo '<div class="panel panel-primary"><div class="panel-heading">' . $lang['hello'] . ', <b>' . Core::textFilter($user['login']) . '</b>!</div>' .
	 '<div class="panel-body">' .
	 '<b>' . $lang['now'] . ':</b> ' . date("H:i | d.m.Y"). '<br />' .
	 '<b>' . $lang['user_id'] . ':</b> ' . $user['id'] . '<br />' .
	 '<b>' . $lang['user_balls'] . ':</b> ' . $user['balls'] . '</b>' .
	 '</div>' .
	 '</div>';

/**
 * Отображаем ссылки
 */
echo '<div class="panel panel-info"><div class="panel-heading"><b>' . $lang['private_info'] . '</b></div>' .
	 '<div class="list-group">' .
	 '<a class="list-group-item" href="profile.php"><span class="glyphicon glyphicon-user"></span> ' . $lang['my_profile'] . '</a>' .
	 '<a class="list-group-item" href="edit.php"><span class="glyphicon glyphicon-edit"></span> ' . $lang['my_profile_edit'] . '</a>' .
	 '<a class="list-group-item" href="avatar.php"><span class="glyphicon glyphicon-adjust"></span> ' . $lang['my_avatar'] . '</a>' .
	 '<a class="list-group-item" href="photo.php"><span class="glyphicon glyphicon-picture"></span> ' . $lang['photo_profile'] . '</a>' .
	 '</div></div>' .
	 
	 '<div class="panel panel-success"><div class="panel-heading"><b>' . $lang['modules'] . '</b></div>' .
	 '<div class="list-group">' .
	 '<a class="list-group-item" href="/mail/"><span class="glyphicon glyphicon-envelope"></span> ' . $lang['my_mails'] . ' <span class="badge">' . $contacts . ' / ' . $mails . '</span></a>' .
	 '<a class="list-group-item" href="journal.php"><span class="glyphicon glyphicon-list-alt"></span> ' . $lang['journal'] . ' <span class="badge">' . $journal . '</span></a>' .
	 '</div></div>' .
	 
	 '<div class="panel panel-warning"><div class="panel-heading"><b>' . $lang['parameters'] . '</b></div>' .
	 '<div class="list-group">' .
	 '<a class="list-group-item" href="settings.php"><span class="glyphicon glyphicon-cog"></span> ' . $lang['my_settings'] . '</a>' .
	 '<a class="list-group-item" href="change_password.php"><span class="glyphicon glyphicon-lock"></span> ' . $lang['change_password'] . '</a>' .
	 '</div></div>' .
	 
	 '<div class="panel panel-danger"><div class="panel-heading"><b>' . $lang['other'] . '</b></div>' .
	 '<div class="list-group">' .
	 ($user['rights'] >= 1 ? '<a class="list-group-item" href="/dpanel/"><span class="glyphicon glyphicon-briefcase"></span> <b>' . $lang['direct_panel'] . '</b></a>' : '') .
	 '<a class="list-group-item" href="exit.php"><span class="glyphicon glyphicon-log-out"></span> ' . $lang['end_session'] . ' "' . $user['login'] . '"</a>' .
	 '</div></div>';

require_once(HOME .'/includes/footer.php'); // подключаем ноги
