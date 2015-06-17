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

$title = $lang_news['news']; // Заголовок страницы
$module = 'news'; // Модуль
require_once(HOME . '/includes/header.php'); // Подключаем шапку
  
/**
 * Удаление новости
 */
if (isset($_GET['delete'])) {
	/**
	 * Проверяем права доступа
	 */
	if ($user['rights'] < 7) {
		echo '<div class="alert alert-danger">' . $lang['error_rights'] . '</div>';
		echo '<div class="list-group">' .
			 '<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
			 '</div>';
		require_once(HOME .'/includes/footer.php');
	}

	/**
     * Проверяем, верен ли введенный параметр
     */
	if (!is_numeric($_GET['delete']))
    	$err[] = $lang['error_parameter'];

    /**
     * Проверяем наличие новости
     */
    if ($db->query("SELECT COUNT(*) FROM `cms_news` WHERE `id` = '" . Core::num($_GET['delete']) . "' LIMIT 1") -> rowCount() == 0)
      	$err[] = $lang_news['undefined_post'];
    
    $ID = Core::num($_GET['delete']);
    $post = $db->query("SELECT * FROM `cms_news` WHERE `id` = '$ID'")->fetch();
    
    /**
     * Небольшая панель навигации
     */
    echo '<ul class="breadcrumb">' .
    	 '<li><a href="/news/">' . $lang['news'] . '</a></li>' .
    	 '<li class="active">' . $lang_news['delete_post'] . ' "' . Core::textFilter($post['name']) . '"</li>' .
    	 '</ul>';
	
    if (isset($_GET['yes'])) {
    	/**
    	 * Если не было ошибок, удаляем новость
    	 */
    	if (!isset($err)) {
    		$db->query("DELETE FROM `cms_news` WHERE `id` = '$ID' LIMIT 1");
        	echo Functions::display_message($lang_news['delete_post_success']);
    		header("Location: /news/");
    		exit;
    	} else {
    		echo Core::display_error($err);
    		require_once(HOME .'/includes/footer.php'); // подключаем ноги
    	}
    } else {
    	echo '<div class="alert alert-danger">' . $lang_news['delete_post_info'] . '<br />' .
    	'<a class="btn btn-default" href="/news/?delete=' . $ID . '&amp;yes">' . $lang['delete'] . '</a> | <a href="/news/">' . $lang['cancel'] . '</a></div>';
    
    	/**
    	 * Нижняя панель навигации
    	 */
    	echo '<div class="list-group">' .
    		 '<a class="list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
    		 '</div>';
    	require_once(HOME .'/includes/footer.php'); // подключаем ноги
    }
}

/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li class="active">' . $lang['news'] . '</li>' .
	 '</ul>';

/**
 * Админские функции
 */
echo (isset($user) && $user['rights'] >= 7 ? '<div class="list-group"><a class="list-group-item" href="add.php"><span class="glyphicon glyphicon-chevron-right"></span> ' . $lang_news['add_post'] . '</a></div>' : '');

/**
 * Настраиваем пагинацию
 */
$total = $db->query("SELECT * FROM `cms_news`") -> rowCount();
$req = $db->query("SELECT * FROM `cms_news` ORDER BY `id` DESC LIMIT $start, $countMess"); 

/**
 * Если нет новостей, выводим уведомление
 */
if ($total < 1) {
	echo '<div class="alert alert-danger">' . $lang_news['no_news'] . '</div>';
}

$i = 0;
while ($res = $req->fetch()) {
    echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
    $add = $db->query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "'") -> fetch();
    echo '<b>' . $lang_news['post'] . '</b>: ' . Core::textFilter($res['name']) . '<br />' . 
    Functions::output_text(Core::textFilter($res['text'])) . '<br />' .
    '<b>' . $lang_news['add_by'] . '</b>: ' . $add['login'] . ' (' . Functions::display_time($res['time']) . ')' .
    '<br /><a href="comments.php?id=' . $res['id'] . '">' . $lang['comments'] . '</a>: ' . $db->query("SELECT * FROM `news_comments` WHERE `news_id` = '" . $res['id'] . "'") -> rowCount() . 
    (isset($user) && $user['rights'] >= 7 ? '<br />[<a href="edit.php?id=' . $res['id'] . '">' . $lang['edit'] . '</a> | <a href="?delete=' . $res['id'] . '">' . $lang['delete'] . '</a>]' : '') . '</div>';
    $i++;
} 

/**
 * Пагинация
 */
if ($total > $countMess) {
	echo Functions::display_pagination('?', $start, $total, $countMess);
}

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
