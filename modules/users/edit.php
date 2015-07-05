<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

ob_start();

$lang_pe = $core->load_lang('profile_edit'); // Подключаем файл языка

$title = $lang_pe['edit_profile']; // Заголовок страницы
$module = 'edit'; // Модуль

/**
 * Проверка наличия авторизации
 */
if (!isset($user)) {
	require_once(HOME .'/includes/header.php');
	echo Core::onlyUsers();
    require_once(HOME .'/includes/footer.php');
}

/**
 * Проверяем, верен ли заданный параметр
 */
if (isset($_GET['id']) && !is_numeric($_GET['id'])) {
	require_once(HOME .'/includes/header.php');
	echo Core::display_error($lang_pe['error_parameter']);
	require_once(HOME .'/includes/footer.php');
}

/**
 * Если задан верный параметр, определяем ИД юзера, в противном случае — свой ИД
 */
if (isset($_GET['id']) && is_numeric($_GET['id']))
	$ID = Core::num($_GET['id']);
else
	$ID = $user['id'];

$profile = $db->query("SELECT * FROM `users` WHERE `id` = '$ID'")->fetch(); // получаем данные пользователя

/**
 * Если пытаемся редактировать чужой профиль, проверяем права доступа
 */
if (($ID != $user['id']) && ($user['rights'] < 8) || ($user['rights'] >= 8 && $user['rights'] < $profile['rights'])) {
	require_once(HOME .'/includes/header.php');
	echo Core::display_error($lang_pe['error_rights']);
	require_once(HOME .'/includes/footer.php');
}

/**
 * Показываем разделы для редактирования
 */
$act = (isset($_GET['act']) ? $_GET['act'] : '');
switch ($act) {
	case 'personal':
		require_once(HOME .'/includes/header.php'); // подключаем шапку
		
		/**
		 * Небольшая панель навигации
		 */
		echo '<ul class="breadcrumb">' .
			 ($user['id'] != $ID ? '<li><a href="/dpanel/">' . $lang['direct_panel'] . '</a></li>' : '<li><a href="/users/cabinet.php">' . $lang['my_cabinet'] . '</a></li>') .
			 ($user['id'] != $ID ? '<li><a href="edit.php&amp;id=' . $ID . '">' . $lang_pe['edit_profile'] . ' "' . Core::textFilter($profile['login']) . '"</a></li>' : '<li><a href="/users/edit.php">' . $lang_pe['edit_profile'] . '</a></li>') .
			 '<li class="active">' . $lang_pe['personal_data'] . '</li>' .
			 '</ul>';
		
		/**
		 * Читаем данные с формы
		 */
		if (isset($_POST['submit'])) {
			$profile['name'] = isset($_POST['name']) ? mb_substr($_POST['name'], 0, 25) : ''; // Имя
			$profile['lastname'] = isset($_POST['lastname']) ? mb_substr($_POST['lastname'], 0, 25) : ''; // Фамилия
			$profile['live'] = isset($_POST['live']) ? mb_substr($_POST['live'], 0, 30) : ''; // Место жительства
			$profile['dbirth'] = isset($_POST['dbirth']) ? $_POST['dbirth'] : ''; // День рождения
			$profile['mbirth'] = isset($_POST['mbirth']) ? $_POST['mbirth'] : ''; // Месяц рождения
			$profile['ybirth'] = isset($_POST['ybirth']) ? $_POST['ybirth'] : ''; // Год рождения
			$profile['about'] = isset($_POST['about']) ? mb_substr($_POST['about'], 0, 600) : ''; // Доп. информация
		
			/**
			 * Проверяем формат даты рождения
			 */
			if ($profile['dbirth'] || $profile['mbirth'] || $profile['ybirth']) {
				if (($profile['dbirth'] > 31 || $profile['dbirth'] < 1) || ($profile['mbirth'] > 12 || $profile['mbirth'] < 1))
					$err[] = $lang_pe['error_birthday'];
			}
		
			/**
			 * Минимальная длина имени
			 */
			if ($profile['name'] && mb_strlen($profile['name']) < 2)
				$err[] = $lang_pe['error_strlen_name'];
		
			/**
			 * Минимальная длина фамилии
			 */
			if ($profile['lastname'] && mb_strlen($profile['lastname']) < 2)
				$err[] = $lang_pe['error_strlen_lastname'];
		
			/**
			 * Минимальная длина места жительства
			 */
			if ($profile['live'] && mb_strlen($profile['live']) < 4)
				$err[] = $lang_pe['error_strlen_live'];
		
			/**
			 * Минимальная длина доп. информации
			 */
			if ($profile['about'] && mb_strlen($profile['about']) < 6)
				$err[] = $lang_pe['error_strlen_about'];
		
			/**
			 * Если нет ошибок, заносим данные
			 */
			if (!isset($err)) {			
				/**
				 * Подготавливаем запрос
				 */
				$st = $db->prepare("UPDATE `users` SET
           			`name` = :name,
					`lastname` = :lastname,
					`dbirth` = :dbirth,
					`mbirth` = :mbirth,
					`ybirth` = :ybirth,
					`live` = :live,
					`about` = :about
					WHERE `id` = :id");
				
				/**
				 * Обновляем данные пользователя в БД
				 */
				$st->execute(array('name' => $profile['name'],
						'lastname' => $profile['lastname'],
						'dbirth' => $profile['dbirth'],
						'mbirth' => $profile['mbirth'],
						'ybirth' => $profile['ybirth'],
						'live' => $profile['live'],
						'about' => $profile['about'],
						'id' => $ID
				));
				
				echo Functions::display_message($lang_pe['edit_success']);
			} else {
				echo Core::display_error($err); // Выводим ошибки
			}
		}

		
		/**
		 * Форма
		 */
		echo '<div class="list-group"><div class="list-group-item">' .
			 '<form name="edit" method="post" action="edit.php?act=personal' . ($ID != $user['id'] ? "&amp;id=" . $ID . "" : "") . '">' .
			 '<b>' . $lang_pe['your_name'] . '</b><br />' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="name" value="' . $profile['name'] . '" />' .
			 '</div>' .
			 '<b>' . $lang_pe['your_lastname'] . '</b><br />' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="lastname" value="' . $profile['lastname'] . '" />' .
			 '</div>' .
			 '<b>' . $lang_pe['your_live'] . '</b><br />' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="live" value="' . $profile['live'] . '" />' .
			 '</div>' .
			 '<b>' . $lang_pe['your_birthday'] . '</b><br />' .
			 '<div class="row"><div class="col-xs-1">' .
			 '<input type="text" class="form-control" name="dbirth" value="' . $profile['dbirth'] . '" />' .
			 '</div>' .
			 '<div class="col-xs-1">' .
			 '<input type="text" class="form-control" name="mbirth" value="' . $profile['mbirth'] . '" />' .
			 '</div>' .
			 '<div class="col-xs-2">' .
			 '<input type="text" class="form-control" name="ybirth" value="' . $profile['ybirth'] . '" />' .
			 '</div></div>' .
			 '<span class="help-block">' . $lang_pe['formate_birthday'] . '</span>' .
			 '<b>' . $lang_pe['about'] . '</b><br />' .
			 bb_panel('edit', 'about') .
			 '<textarea class="form-control" name="about">' . $profile['about'] . '</textarea>' .
			 '<span class="help-block">' . $lang_pe['about_info'] . '</span>' .
			 '<input type="submit" class="btn btn-primary" name="submit" value="' . $lang['save'] . '" />' .
			 '</form></div></div>';
		
		/**
		 * Нижняя панель навигации
		 */
		echo '<div class="list-group">' .
			 '<a class = "list-group-item" href="edit.php' . ($ID != $user['id'] ? '?id=' . $ID : '') . '"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
			 '</div>';
		
		require_once(HOME .'/includes/footer.php'); // подключаем ноги
	break;
	
	/**
	 * Редактирование контактных данных
	 */
	case 'contacts':
		require_once(HOME .'/includes/header.php'); // Подключаем шапку
		
		/**
		 * Небольшая панель навигации
		 */
		echo '<ul class="breadcrumb">' .
			 ($user['id'] != $ID ? '<li><a href="/dpanel/">' . $lang['direct_panel'] . '</a></li>' : '<li><a href="/users/cabinet.php">' . $lang['my_cabinet'] . '</a></li>') .
			 ($user['id'] != $ID ? '<li><a href="edit.php&amp;id=' . $ID . '">' . $lang_pe['edit_profile'] . ' "' . Core::textFilter($profile['login']) . '"</a></li>' : '<li><a href="/users/edit.php">' . $lang_pe['edit_profile'] . '</a></li>') .
			 '<li class="active">' . $lang_pe['contacts'] . '</li>' .
			 '</ul>';
	
		/**
		 * Читаем данные с формы
		 */
		if (isset($_POST['submit'])) {
			$profile['email'] = isset($_POST['email']) ? mb_substr($_POST['email'], 0, 45) : ''; // Электронная почта
			$profile['icq'] = isset($_POST['icq']) ? (int)$_POST['icq'] : 0; // ICQ
			$profile['skype'] = isset($_POST['skype']) ? mb_substr($_POST['skype'], 0, 40) : ''; // Skype
			$profile['jabber'] = isset($_POST['jabber']) ? mb_substr($_POST['jabber'], 0, 35) : ''; // Jabber
			$profile['site'] = isset($_POST['site']) ? mb_substr($_POST['site'], 0, 35) : ''; // Личный сайт
	
			/**
		 	 * Проверяем формат ICQ
		 	 */
			if (($profile['icq'] && ($profile['icq'] > 999999999 || $profile['icq'] < 10000)) || !is_numeric($profile['icq']))
				$err[] = $lang_pe['error_icq'];
	
			/**
		 	 * Проверяем формат E-Mail
		 	 */
			if ($profile['email'] && !preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui', $profile['email']))
				$err[] = $lang_pe['error_email'];
	
			/**
		 	 * Если нет ошибок, заносим данные
		 	 */
			if (!isset($err)) {
				/**
			 	 * Подготавливаем запрос
			 	 */
				$st = $db->prepare("UPDATE `users` SET
					`email` = :email,
					`icq` = :icq,
					`skype` = :skype,
					`jabber` = :jabber,
					`site` = :site
					WHERE `id` = :user_id LIMIT 1");
			
				/**
			 	 * Обновляем данные пользователя в БД
				 */
				$st->execute(array('email' => $profile['email'],
					'icq' => $profile['icq'],
					'skype' => $profile['skype'],
					'jabber' => $profile['jabber'],
					'site' => $profile['site'],
					'user_id' => $ID
				));
			
				echo Functions::display_message($lang_pe['edit_success']);
			} else {
				echo Core::display_error($err); // Выводим ошибки
			}
		
		}
	
		/**
	 	 * Форма
	 	 */
		echo '<div class="list-group"><div class="list-group-item">' .
			 '<form method="post" action="edit.php?act=contacts' . ($ID != $user['id'] ? "&amp;id=" . $ID . "" : "") . '">' .
			 '<b>' . $lang_pe['your_email'] . '</b>' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="email" value="' . $profile['email'] . '" />' . 
			 '<span class="help-block">' . $lang_pe['email_info'] . '</span>' .
			 '</div>' .
			 '<b>' . $lang_pe['your_icq'] . '</b>' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="icq" value="' . $profile['icq'] . '" />' .
			 '</div>' . 
			 '<b>' . $lang_pe['your_skype'] . '</b>' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="skype" value="' . $profile['skype'] . '" />' .
			 '</div>' .
			 '<b>' . $lang_pe['your_jabber'] . '</b>' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="jabber" value="' . $profile['jabber'] . '" />' .
			 '</div>' .
			 '<b>' . $lang_pe['your_site'] . '</b>' .
			 '<div class="input-group">' .
			 '<input type="text" class="form-control" name="site" value="' . $profile['site'] . '" />' .
			 '<span class="help-block">' . $lang_pe['site_info'] . '</span>' .
			 '</div>' .
			 '<input type="submit" class="btn btn-primary" name="submit" value="' . $lang['save'] . '" />' .
			 '</form></div></div>';
	
		/**
	 	 * Нижняя панель навигации
	 	 */
		echo '<div class="list-group">' .
			 '<a class = "list-group-item" href="edit.php' . ($ID != $user['id'] ? '?id=' . $ID : '') . '"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
			 '</div>';
	
		require_once(HOME .'/includes/footer.php'); // Подключаем ноги
	break;
	
	/**
	 * Показываем разделы для редактирования
	 */
	default:
		require_once(HOME .'/includes/header.php'); // подключаем шапку

    	/**
      	 * Небольшая панель навигации
    	 */
		echo '<ul class="breadcrumb">' .
			 ($user['id'] != $ID ? '<li><a href="/dpanel/">' . $lang['direct_panel'] . '</a></li>' : '<li><a href="/users/cabinet.php">' . $lang['my_cabinet'] . '</a></li>') .
			 ($user['id'] != $ID ? '<li class="active">' . $lang_pe['edit_profile'] . ' "' . Core::textFilter($profile['login']) . '"</li>' : '<li class="active">' . $lang_pe['edit_profile'] . '</li>') .
			 '</ul>';

    	echo '<div class="list-group">' .
    		 '<a class="list-group-item" href="?act=personal' . ($ID != $user['id'] ? '&amp;id=' . $ID . '' : '') . '"><span class="glyphicon glyphicon-user"></span> ' . $lang_pe['personal_data'] . '</a>' .
    		 '<a class="list-group-item" href="?act=contacts' . ($ID != $user['id'] ? '&amp;id=' . $ID . '' : ''). '"><span class="glyphicon glyphicon-link"></span> ' . $lang_pe['contacts'] . '</a>' .
    		 (($user['rights'] >= 8) && $user['id'] != $ID && $user['rights'] > $profile['rights'] ? '<a class="list-group-item" href="avatar.php?id=' . $ID . '"><span class="glyphicon glyphicon-adjust"></span> ' . $lang_pe['avatar'] . '</a>' : '') .
    		 (($user['rights'] >= 8) && $user['id'] != $ID && $user['rights'] > $profile['rights'] ? '<a class="list-group-item" href="photo.php?id=' . $ID . '"><span class="glyphicon glyphicon-picture"></span> ' . $lang_pe['photo_profile'] . '</a>' : '') .
    		 (($user['rights'] >= 8) && $user['id'] != $ID && $user['rights'] > $profile['rights'] ? '<a class="list-group-item" href="rank.php?id=' . $ID . '"><span class="glyphicon glyphicon-briefcase"></span> ' . $lang_pe['rank'] . '</a>' : '') .
    		 (($user['rights'] >= 8) && $user['id'] != $ID && $user['rights'] > $profile['rights'] ? '<a class="list-group-item" href="settings.php?id=' . $ID . '"><span class="glyphicon glyphicon-cog"></span> ' . $lang_pe['settings'] . '</a>' : '') .
    		 (($user['id'] == $ID) || ($user['rights'] >= 7) && $user['rights'] > $profile['rights'] ? '<a class="list-group-item" href="status.php?id=' . $ID . '"><span class="glyphicon glyphicon-star"></span> ' . $lang_pe['status'] . '</a>' : '') .
    	'</div>';

		require_once(HOME .'/includes/footer.php'); // подключаем ноги
	break;
  }

ob_end_flush();
