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

$title = $lang_pe['upload_avatar']; // Заголовок страницы
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
	echo Core::display_error($lang['error_parameter']);
	require_once(HOME .'/includes/footer.php');
}

/**
 * Проверяем существование пользователя
 */
 if (isset($_GET['id']) && is_numeric($_GET['id']) && ($_GET['id'] != $user['id'] && $user['rights'] >= 8) && $db->query("SELECT COUNT(*) FROM `users` WHERE `id` = '" . Core::num($_GET['id']) . "' LIMIT 1")->fetchColumn() == 0) {
	require_once(HOME .'/includes/header.php');
	echo Core::display_error($lang_pe['user_not_exists']);
	require_once(HOME .'/includes/footer.php');
}

/**
 * Если задан верный параметр, определяем ИД юзера, в противном случае — свой ИД
 */
if (isset($_GET['id']) && is_numeric($_GET['id']))
	$ID = Core::num($_GET['id']);
else
    $ID = $user['id'];

$profile = $db->query("SELECT * FROM `users` WHERE `id` = '$ID'")->fetch(); // Получаем данные пользователя

/**
 * Если пытаемся редактировать чужой профиль, проверяем права доступа
 */
if (($ID != $user['id']) && ($user['rights'] < 8) || ($user['rights'] >= 8 && $user['rights'] < $profile['rights'])) {
	require_once(HOME .'/includes/header.php');
	echo Core::display_error($lang_pe['error_rights']);
	require_once(HOME .'/includes/footer.php');
}

/**
 * Загрузка аватара
 */
if (isset($_POST['upload'])) {
	require_once(HOME .'/includes/header.php'); // Подключаем шапку
	
	/**
     * Небольшая панель навигации
	 */
	echo '<ul class="breadcrumb">' .
		 ($user['id'] != $ID ? '<li><a href="/dpanel/">' . $lang['direct_panel'] . '</a></li>' : '<li><a href="/users/cabinet.php">' . $lang['my_cabinet'] . '</a></li>') .
		 ($user['id'] != $ID ? '<li><a href="edit.php&amp;id=' . $ID . '">' . $lang_pe['edit_profile'] . ' "' . $profile['login'] . '"</a></li>' : '<li><a href="/users/edit.php">' . $lang_pe['edit_profile'] . '</a></li>') .
		 '<li class="active">' . $lang_pe['upload_avatar'] . '</li>' .
		 '</ul>';

	/**
 	 * Работаем с Class.Upload
 	 */
	$file = new upload($_FILES['avatar']);
	if ($file->uploaded) {
		$file->file_new_name_body = $ID; // Новое название файла
		$file->allowed = array('image/jpeg', 'image/gif', 'image/png'); // Допустимые расширения
		$file->file_max_size = 1024 * 3000; // Максимальный размер файла
		$file->file_overwrite = true; // Перезапись существующего изображения (НЕ МЕНЯТЬ)
		$file->image_resize = true; // Изменение размера изображения (НЕ МЕНЯТЬ)
		$file->image_x = 32; // Ширина аватара
		$file->image_y = 32; // Высота аватара
		$file->image_convert = 'png'; // Конвертируемый формат (желательно .png)
		$file->process(HOME . "/files/avatars/"); // Папка для сохранения
		
		/**
		 * Если нет ошибок, обновляем аватар
		 */
		if ($file->processed) {	
			echo Functions::display_message($lang_pe['avatar_upload_success'], ($ID != $user['id'] ? 'avatar.php?id=' . $ID : 'avatar.php'));
			$file->clean();
		} else {
			echo Core::display_error($file->error); // Выводим ошибки (если есть)
		}
		require_once(HOME .'/includes/footer.php'); // Подключаем ноги
	}
} else {
	require_once(HOME .'/includes/header.php'); // Подключаем шапку
	
	/**
	 * Небольшая панель навигации
	 */
	echo '<ul class="breadcrumb">' .
			($user['id'] != $ID ? '<li><a href="/dpanel/">' . $lang['direct_panel'] . '</a></li>' : '<li><a href="/users/cabinet.php">' . $lang['my_cabinet'] . '</a></li>') .
			($user['id'] != $ID ? '<li><a href="edit.php&amp;id=' . $ID . '">' . $lang_pe['edit_profile'] . ' "' . $profile['login'] . '"</a></li>' : '<li><a href="/users/edit.php">' . $lang_pe['edit_profile'] . '</a></li>') .
			'<li class="active">' . $lang_pe['upload_avatar'] . '</li>' .
			'</ul>';

	/**
	 * Если аватар уже есть, показываем
	 */
	if (file_exists(HOME . '/files/avatars/' . $ID . '.png')) {
		echo '<div class="list-group">' .
			 '<div class="list-group-item"><b>' . $lang_pe['actual_avatar'] . '</b><br />' .
			 '<a href="/files/avatars/' . $ID . '.png"><img src="/files/avatars/' . $ID . '.png" alt="LiveCMS" /></a>' .
			 '</div></div>';
	}

	/**
	 * Форма
	 */
	echo '<div class="list-group"><div class="list-group-item">' .
		 '<form method="post" action="avatar.php' . ($ID != $user['id'] ? '?id=' . $ID . '&amp;upload' : '?upload') . '" enctype="multipart/form-data">' .
		 '<b>' . $lang_pe['select_avatar_image'] . '</b>' .
		 '<input type="file" name="avatar" />' .
		 '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * 3000) . '" />' .
		 '<input type="submit" class="btn btn-primary" name="upload" value="' . $lang['upload'] . '" />' .
		 '<span class="help-block">* ' . $lang_pe['max_filesize_info'] . '<br />' .
		 '*' . $lang_pe['avatar_convert_info'] . '</span>' .
		 '</form></div></div>';
	
	/**
	 * Нижняя панель навигации
	 */
	echo '<div class="list-group">' .
		 '<a class = "list-group-item" href="' . ($ID != $user['id'] ? 'edit.php?id=' . $ID : 'cabinet.php') . '"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
		 '</div>';
	
	require_once(HOME .'/includes/footer.php'); // Подключаем ноги
} 
