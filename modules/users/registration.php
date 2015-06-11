<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.textFilter
 * @license See attached file LICENSE.textFilter
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

/**
  * Проверяем наличие авторизации
**/
if (isset($user)) {
	header('Location: /'); // Перенапрвляем на главную
}

$title = $lang['registration']; // Заголовок страницы
$module = 'registration'; // Модуль

require_once(HOME .'/includes/header.php'); // Подключаем шапку

/**
 * Обрабатываем запрос на регистрацию
 */
if (isset($_POST['reg_submit'])) {
	/**
	 * Читаем данные с формы
	 */
    $reg_login = $_POST['reg_login']; // Логин
    $reg_sex = ($_POST['sex'] == 'm' ? 'm' : 'w');
    $reg_name = (!empty($_POST['name']) ? $_POST['name'] : ''); // Имя
    $reg_about = (!empty($_POST['about']) ? $_POST['about'] : ''); // Доп. Информация

    /**
     * Проверка ввода логина
     */
    if (empty($_POST['reg_login']))
    	$err[] = $lang['login_empty'];

    /**
     * Проверка длины логина
     */
    if (!empty($_POST['reg_login']) && (mb_strlen($_POST['reg_login']) < 3 || mb_strlen($_POST['reg_login']) > 15))
      	$err[] = $lang['invalid_login_length'];

    /**
     * Проверяем логин на занятость
     */
    if ($db->query("SELECT COUNT(*) FROM `users` WHERE `login` = '" . $_POST['reg_login'] . "'")->fetchColumn() != 0)
      	$err[] = $lang['login_name'] . ' ' . Core::textFilter($_POST['reg_login']) . ' ' . $lang['login_is_busy'];

    $reg_password = $_POST['reg_password'];

    /**
     * Проверка ввода пароля
     */
    if (empty($_POST['reg_password']))
      	$err[] = $lang['empty_password'];

    /**
     * Проверка длины пароля
     */
    if (!empty($_POST['reg_password']) && (strlen($_POST['reg_password']) < 5 || strlen($_POST['reg_password']) > 64))
      	$err[] = $lang['invalid_password_length'];

    $reg_password2 = $_POST['reg_password2'];

  	/**
   	 * Проверка ввода повторного пароля
  	 */
	if (empty($_POST['reg_password2']))
      	$err[] = $lang['empty_password2'];

	/**
     * Проверка совпадения паролей
     */
    if (!empty($_POST['reg_password2']) && $_POST['reg_password'] != $_POST['reg_password2'])
      	$err[] = $lang['invalid_passwords'];

    /**
     * Проверка ввода каптчи
     */
    $reg_code = $_POST['reg_code'];
    if ($reg_code != $_SESSION['code'])
      	$err[] = 'Неверный проверочный код';

    /**
     * Если нет ошибок, регистрируем юзера
     */
    if (!isset($err)) {
        $reg_password = Core::encrypt($reg_password); // Шифровка пароля

        /**
         * Подготавливаем запрос
         */
        $st = $db->prepare("INSERT INTO `users` SET
        	`login` = :login,
			`password` = :password,
       		`sex` = :sex,
			`date_reg` = :date_reg,
			`date_last_entry` = :date_last_entry,
   		 	`name` = :name,
   		 	`about` = :about");
        /**
         * Заносим данные пользователя в БД
         */
        $st->execute(array('login' => $reg_login,
        				'password' => $reg_password,
        				'sex' => $reg_sex,
        				'date_reg' => time(),
        				'date_last_entry' => time(),
        				'name' => $reg_name,
        				'about' => $reg_about
        ));

        /**
         * Создаем личные настройки пользователя
         */
        $db->query("INSERT INTO `user_settings` SET
        	`language` = '" . $cms_set['language'] . "',
			`num_pages` = '" . $cms_set['num_pages'] . "',
       		`sex_view` = '" . $cms_set['sex_view'] . "',
            `show_avatars` = '" . $cms_set['show_avatars'] . "',
            `theme` = '" . $cms_set['theme'] . "'");

        /**
         * Уведомление об окончании регистрации
         */
        echo Functions::display_message('<b>' . $lang['registration_end'] . '</b><br />' .
        $lang['registration_successful'] . '<br />' .
        '<b>' . $lang['registration_login'] . ':</b> ' . Core::textFilter($reg_login) . '<br />' .
        '<b>' . $lang['registration_password'] . ':</b> ' . Core::textFilter($_POST['reg_password']));
        require_once(HOME .'/includes/footer.php');
	} else {
    	echo Core::display_error($err); // Если есть ошибки, выводим
	}
}
	
/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li class="active">' . $lang['registration'] . '</li>' .
	 '</ul>';
	
/**
 * Форма
 */
echo '<div class="list-group-item">' .
	 '<form method="post" name="registration" action="/users/registration.php">' .
  	 $lang['login_name'] . ' [max. 15] <span class="red">*</span><br />' .
  	 '<div class="input-group">' .
  	 '<input type="text" class="form-control" name="reg_login" value="' . (isset($_POST['reg_login']) ? Functions::textFilter($_POST['reg_login']) : '') . '" />' .
  	 '</div>' .
  	 $lang['name'] . ':<br />' .
  	 '<div class="input-group">' .
  	 '<input type="text" class="form-control" name="name" value="' . (isset($_POST['name']) ? Functions::textFilter($_POST['name']) : '') . '" />' .
  	 '</div>' .
  	 $lang['sex'] . ': <span class="red">*</span><br />' . 
  	 '<div class="input-group">' .
  	 '<select class="form-control" name="sex">' .
  	 '<option value="m">' . $lang['sex_m'] . '</option>' .
  	 '<option value="w">' . $lang['sex_w'] . '</option>' .
  	 '</select></div>' .
  	 $lang['password'] . ': [max. 64] <span class="red">*</span><br />' .
  	 '<div class="input-group">' .
  	 '<input type="password" class="form-control" name="reg_password" />' .
  	 '</div>' . 
  	 $lang['password2'] . ': <span class="red">*</span><br />' .
  	 '<div class="input-group">' .
  	 '<input type="password" class="form-control" name="reg_password2" />' .
  	 '</div>' .
  	 $lang['about_yourself'] . ':<br />' .
  	 bb_panel('registration', 'about') .
  	 '<textarea class="form-control" name="about">' . (isset($_POST['about']) ? Functions::textFilter($_POST['about']) : '') . '</textarea>' .
  	 $lang['captcha_code'] . ':<br />';
  	 echo captcha(); // Показываем каптчу
  	 echo '<div class="input-group">' .
  	 	  '<input type="text" class="form-control" name="reg_code" size="5"/>' .
  		  '</div>' .
  		  '<input type="submit" class="btn btn-default" name="reg_submit" value="' . $lang['register_me'] . '" />' .
  		  '</form></div>';

echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> ' . $lang['required_fields'] . '</div>';

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
