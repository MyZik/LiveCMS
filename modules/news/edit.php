	<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$lang_news = $core->load_lang('news'); // Подключаем файл языка
$title = $lang_news['news_edit']; // Заголовок страницы
$module = 'news'; // Модуль

/**
 * Проверка наличия авторизации
 */
if (!isset($user)) {
	require_once(HOME .'/includes/header.php');
	echo Core::onlyUsers('/news/');
	require_once(HOME .'/includes/footer.php');
}

/**
 * Проверяем наличие прав доступа
 */
if ($user['rights'] < 7) {
	require_once(HOME .'/includes/header.php');
	echo '<div class="alert alert-danger">' . $lang['error_rights'] . '</div>';
	echo '<div class="list-group">' .
			'<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
			'</div>';
	require_once(HOME .'/includes/footer.php');
}

/**
 * Проверяем, верен ли заданный параметр
 */
if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
	$ID = $core->num($_GET['id']);
    $news = $db->query("SELECT * FROM `cms_news` WHERE `id` = '$ID'")->fetch();
} else {
    require_once(HOME .'/includes/header.php');
    echo '<div class="alert alert-danger">' . $lang['error_parameter'] . '</div>';
    echo '<div class="list-group">' .
    '<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
    '</div>';
    require_once(HOME .'/includes/footer.php');
}

/**
 * Проверяем наличие новости
 */
if (empty($ID) || ($db->query("SELECT * FROM `cms_news` WHERE `id` = '$ID'")->rowCount() == 0)) {
    require_once(HOME .'/includes/header.php');
    echo '<div class="alert alert-danger">' . $lang_news['undefined_post'] . '</div>';
    echo '<div class="list-group">' .
    '<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
    '</div>';
    require_once(HOME .'/includes/footer.php');
}

require_once(HOME .'/includes/header.php'); // Подключаем шапку

/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li><a href="/news/">' . $lang['news'] . '</a></li>' .
	 '<li class="active">' . $lang_news['news_edit'] . ' "' . Functions::textFilter($news['name']) . '"</li>' .
	 '</ul>';

/**
 * Редактирование новости
 */
if (isset($_POST['save'])) {
	$news['name'] = mb_substr($_POST['name'], 0, 64);
	$news['text'] = mb_substr($_POST['text'], 0, 5000);

    /**
     * Проверяем длину заголовка новости
     */
    if (mb_strlen($news['name']) < 3)
    	$err[] = $lang_news['short_name'];
    
    /**
     * Проверяем длину текста новости
     */
    if (mb_strlen($news['text']) < 3)
    	$err[] = $lang_news['short_text'];

    /**
     * Если нет ошибок, заносим данные
     */
    if (!isset($err)) {
    	/**
    	 * Подготавливаем запрос
    	 */
    	$st = $db->prepare("UPDATE `cms_news` SET
    				`name` = :name, `text` = :text WHERE `id` = :id");
    	
    	/**
    	 * Обновляем новость в БД
    	 */
    	$st->execute(array('name' => $news['name'],
    			'text' => $news['text'],
    			'id' => $ID
    	));
    	
    	echo Functions::display_message($lang_news['edit_post_success']);
    } else {
    	echo Core::display_error($err); // выводим ошибки
    }
}

/**
 * Форма
 */
echo '<div class="list-group"><div class="list-group-item">' .
	 '<form method="post" action="edit.php?id=' . $ID . '">' . 
	 '<b>' . $lang_news['post_name'] . '</b><br />' .
	 '<div class="input-group">' .
	 '<input type="text" class="form-control" name="name" value="' . $news['name'] . '" />' .
	 '</div>' .
	 '<b>' . $lang_news['post_text'] . '</b><br />' .
	 bb_panel('message', 'text') .
	 '<textarea class="form-control" name="text">' . $news['text'] . '</textarea>' .
	 '<span class="help-block">' . $lang_news['tags_info'] . '</span>' .
	 '<input type="submit" class="btn btn-primary" name="save" value="' . $lang['edit'] . '" />' .
	 '</form></div></div>';

/**
 * Нижняя панель навигации
 */
echo '<div class="list-group">' .
     '<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
     '</div>';

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
