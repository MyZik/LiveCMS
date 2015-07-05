<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$lang_pe = $core->load_lang('profile_edit'); // Подключаем файл языка

$title = $lang_pe['change_password']; // Заголовок страницы
$module = 'edit'; // Модуль

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
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li><a href="/users/cabinet.php">' . $lang['my_cabinet'] . '</a></li>' .
	 '<li class="active">' . $lang_pe['change_password'] . '</li>' .
	 '</ul>';
/**
 * Сохраняем данные
 */
if (isset($_POST['save'])) {
	$old_password = $_POST['old_password'];
	$new_password = $_POST['new_password'];

	/**
 	 * Проверяем ввод старого пароля
 	 */
	if (empty($old_password))
		$err[] = $lang_pe['empty_old_password'];

	/**
 	 * Проверяем, верен ли старый пароль
 	 */
	if (Core::encrypt($old_password) != $user['password'])
		$err[] = $lang_pe['invalid_old_password'];

    /**
     * Проверяем длину нового пароля
     */
	if (empty($new_password) || mb_strlen($new_password) < 5)
    	$err[] = $lang_pe['empty_new_password'];

    /**
     * Проверяем совпадение паролей
     */
	if ($new_password != $_POST['new_password2'])
		$err[] = $lang_pe['invalid_passwords'];

    /**
     * Если нет ошибок, заносим данные
     */
	if (!isset($err)) {
		/**
		 * Подготавливаем запрос
		 */
		$st = $db->prepare("UPDATE `users` SET `password` = :new_password
					WHERE `id` = :user_id LIMIT 1");
		 
		/**
		 * Обновляем данные в БД
		 */
		$st->execute(array('new_password' => Core::encrypt($new_password),
				'user_id' => $user['id']
		));
		
		/**
         * Оповещение в журнал
         */
		$message = $lang['journal_change_password'];
		journal_add($user['id'], $message);
      
		echo Functions::display_message($lang_pe['change_password_success'] . ' <b>' . Core::textFilter($new_password) . '</b>');
		require_once(HOME .'/includes/footer.php'); // Подключаем ноги
	} else {
		echo Core::display_error($err); // Выводим ошибки
    }
}

/**
 * Форма
 */
echo '<div class="list-group"><div class="list-group-item">' .
	 '<form method="post" action="change_password.php">' .
	 '<b>' . $lang_pe['old_password'] . '</b><br />' .
	 '<div class="input-group">' . 
	 '<input type="text" class="form-control" name="old_password" value="" />' .
	 '</div>' .
	 '<b>' . $lang_pe['new_password'] . '</b><br />' .
	 '<div class="input-group">' .
	 '<input type="password" class="form-control" name="new_password" value="" />' .
	 '</div>' .
	 '<b>' . $lang_pe['new_password2'] . '</b><br />' .
	 '<div class="input-group">' .
	 '<input type="password" class="form-control" name="new_password2" value="" />' .
	 '</div>' .
	 '<input type="submit" class="btn btn-primary" name="save" value="' . $lang['save'] . '" />' .
	 '</form></div></div>';

/**
 * Нижняя панель навигации
 */
echo '<div class="list-group">' .
	 '<a class = "list-group-item" href="cabinet.php"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
	 '</div>';
  
require_once(HOME .'/includes/footer.php'); // Подключаем ноги
