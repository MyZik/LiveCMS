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
 * Проверка наличия авторизации
 */
if (isset($user)) {
    header('Location: /');
}

$title = $lang['authorization']; // Заголовок страницы
$module = 'authorization'; // Модуль
require_once(HOME .'/includes/header.php'); // Подключаем шапку

if (isset($err))
	echo Core::display_error($err);

/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li class="active">' . $lang['authorization'] . '</li>' .
	 '</ul>';

/**
 * Форма
 */
echo '<div class="list-group">' .
	 '<div class="list-group-item"><form method="post" action="/" class="form-horizontal" role="form">' .
	 '<div class="form-group">' .
     '<label for="login" class="col-sm-1 control-label">' . $lang['login_name'] . '</label>' .
     '<div class="col-sm-10">' .
     '<input type="text" class="form-control" name="login" id="login" placeholder="Login">' .
     '</div>' .
  	 '</div>' .
  	 '<div class="form-group">' .
     '<label for="password" class="col-sm-1 control-label">' . $lang['password'] . '</label>' .
     '<div class="col-sm-10">' .
     '<input type="password" class="form-control" name="password" id="password" placeholder="Password">' .
     '</div>' .
  	 '</div>' .
  	 '<div class="form-group">' .
     '<div class="col-sm-offset-1 col-sm-10">' .
     '<div class="checkbox">' .
     '<label>' .
     '<input type="checkbox" name="save_entry" value="1" checked="checked"> ' . $lang['remember_me'] .
     '</label>' .
     '</div>' .
     '</div>' .
  	 '</div>' .
  	 '<div class="form-group">' .
     '<div class="col-sm-offset-1 col-sm-10">' .
     '<button type="submit" class="btn btn-primary">' . $lang['log_in'] . '</button>' .
     '</div>' .
     '</div>' .
     '</form></div></div>';

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
