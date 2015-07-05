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

$title = $lang_news['comments_news']; // Заголовок страницы
$module = 'news'; // Модуль
require_once(HOME .'/includes/header.php'); // Подключаем шапку

/**
 * Проверяем, верен ли введенный параметр
 */
if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
	$ID = Core::num($_GET['id']);
    $news = $db->query("SELECT * FROM `cms_news` WHERE `id` = '$ID'")->fetch();
} else {
    require_once(HOME . '/includes/header.php');
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
    echo '<div class="alert	 alert-danger">' . $lang_news['undefined_post'] . '</div>';
    echo '<div class="list-group">' .
    '<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
    '</div>';
    require_once(HOME .'/includes/footer.php');
}

/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li><a href="/news/">' . $lang['news'] . '</a></li>' .
	 '<li class="active">' . $lang_news['post'] . ' "' . Core::textFilter($news['name']) . '"</li>' .
	 '</ul>';

/**
 * Добавление комментария
 */
if (isset($_POST['send']) && isset($user)) {
	$message = mb_substr($_POST['message'], 0, 1024); // фильтруем данные и обрезаем текст

    /**
     * Проверяем длину комментария
     */
    if (empty($_POST['message']) || mb_strlen($_POST['message']) < 3)
    	$err[] = $lang_news['error_comment_length'];

    /**
     * Проверка на флуд
     */
    if (($user['lastpost'] + $cms_set['antiflood_time']) >= time())
    	$err[] = $lang['error_antiflood'];

    /**
     * Если нет ошибок, заносим данные
     */
    if (!isset($err)) {
    	/**
    	 * Подготавливаем запрос
    	 */
    	$st = $db->prepare("INSERT INTO `news_comments` (`news_id`, `user_id`, `time`, `message`)
    				VALUES (:news_id, :user_id, :time, :message)");
    	 
    	/**
    	 * Заносим сообщение в БД
    	 */
    	$st->execute(array('news_id' => $ID,
    			'user_id' => $user['id'],
    			'time' => time(),
    			'message' => $message
    	    	));
    	
    	/**
         * Начисление баллов и обновление счетчика сообщений
         */
    	$db->query("UPDATE `users` SET `balls` = `balls` + 1 WHERE `id` = '" . $user['id'] . "' LIMIT 1");
    	$db->query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = '" . $user['id'] . "' LIMIT 1");
    	echo Functions::display_message($lang_news['add_comment_success']);
	} else {
        echo Core::display_error($err); // выводим ошибки
    }
}

/**
 * Удаление комментария
 */
if (isset($_GET['delete']) && ($user['rights'] >= 7)) {
	/**
     * Проверяем, верен ли введенный параметр
     */
	if (!is_numeric($_GET['delete']))
		$err[] = $lang['error_parameter'];

	/**
 	 * Проверяем наличие комментария
 	 */
    if (is_numeric($_GET['delete']) && $db->query("SELECT * FROM `news_comments` WHERE `id` = '" . Core::num($_GET['delete']) . "' LIMIT 1")->rowCount() == 0)
    	$err[] = $lang_news['undefined_message'];

    /**
     * Если нет ошибок, удаляем комментарий
     */
    if (!isset($err)) {
    	$db->query("DELETE FROM `news_comments` WHERE `id` = '" . Core::num($_GET['delete']) . "' LIMIT 1");
    	echo Functions::display_message($lang_news['delete_message_success']);
    } else {
    	echo Core::display_error($err); // Выводим ошибки
    }
}

/**
 * Форма
 */
if (isset($user)) {
	echo '<div class="list-group"><div class="list-group-item">' .
		 '<form name="message" action="comments.php?id=' . $ID . '" method="post">' .
		 '<b>' . $lang['enter_message'] . '</b><br />' . 
		 bb_panel('message', 'message') .
		 '<textarea class="form-control" name="message"></textarea>' .
		 '<input type="submit" class="btn btn-primary" name="send" value="' . $lang['send'] . '" />' .
		 '</form></div></div>';
}

/**
 * Настраиваем пагинацию
 */
$total = $db->query("SELECT * FROM `news_comments` WHERE `news_id` = '$ID'") -> rowCount();
$req = $db->query("SELECT * FROM `news_comments` WHERE `news_id` = '$ID' ORDER BY `id` DESC LIMIT $start, $countMess"); 

/**
 * Если нет результатов, выводим уведомление
 */
if ($total < 1) {
	echo '<div class="alert alert-danger">' . $lang_news['no_comments'] . '</div>';
}

$i = 0;
while ($res = $req -> fetch()) {
    echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
    $info = '&nbsp;[' . Functions::display_time($res['time']) . ']';
    $body = Functions::output_text(Core::textFilter($res['message']));
    $end = ($user['rights'] >= 8 ? '<br />[<a href="comments.php?id=' . $ID . '&amp;delete=' . $res['id'] . '">' . $lang['delete'] . '</a>]' : '');
    echo Functions::display_user($res['user_id'], $info, $body, $end);
    echo '</div>';
	$i++;
} 

/**
 * Пагинация
 */

if ($total > $countMess)
	echo Functions::display_pagination('comments.php?id=' . $ID . '&amp;', $start, $total, $countMess);

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
