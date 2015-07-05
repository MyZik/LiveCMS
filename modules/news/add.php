<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$lang_news = $core->load_lang('news');
$title = $lang_news['news']; // Заголовок страницы
$module = 'news'; // Модуль

/**
 * Проверка наличия авторизации
 */
if (!isset($user)) {
    require_once(HOME .'/includes/header.php');
    echo Core::onlyUsers('/news/');
    require_once(HOME .'/includes/footer.php');
	exit;
}

/**
 * Проверка прав доступа
 */
if ($user['rights'] < 7) {
	require_once(HOME .'/includes/header.php');
    echo '<div class="alert alert-danger">' . $lang['error_rights'] . '</div>';
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
	 '<li class="active">' . $lang_news['add_news'] . '</li>' .
	 '</ul>';

/**
 * Добавление новости
 */
if (isset($_POST['add'])) {
	$name = mb_substr($_POST['name'], 0, 64);
	$text = mb_substr($_POST['text'], 0, 5000);
	$days_homepage = (is_numeric($_POST['days_homepage']) ? $_POST['days_homepage'] : 1);
	$main_time = time() + $days_homepage * 60 * 60 * 24;

    /**
     * Проверяем длину названия новости
     */
    if (mb_strlen($name) < 3)
      $err[] = $lang_news['short_name'];
    
    /**
     * Проверяем длину текста новости
     */
    if (mb_strlen($text) < 3)
      $err[] = $lang_news['short_text'];

    /**
     * Проверяем правильность ввода кол-во дней на главной
     */
    if (!is_numeric($days_homepage) || $days_homepage < 0)
      $err[] = $lang_news['numeric_days'];

    /**
     * Если не было ошибок, заносим данные
     */
    if (!isset($err)) {
    	/**
    	 * Подготавливаем запрос
    	 */
    	$st = $db->prepare("INSERT INTO `cms_news` (`name`, `text`, `time`, `user_id`, `days_homepage`)
    				      VALUES (:name, :text, :time, :user_id, :days_homepage)");
    	
    	/**
    	 * Заносим сообщение в БД
    	 */
    	$st->execute(array('name' => $name,
    			'text' => $text,
    			'time' => time(),
    			'user_id' => $user['id'],
    			'days_homepage' => Core::num($main_time)
    	));
    	
      	$db->query("UPDATE `users` SET `read_news` = 'no'");
      	echo Functions::display_message($lang_news['add_news_success']);
    } else {
      	echo Core::display_error($err);
    }
}

/**
 * Форма
 */
echo '<div class="list-group-item">' .
  	 '<form name="message" method="post" action="add.php">' . 
  	 '<b>' . $lang_news['post_name'] . '</b><br />' .
  	 '<div class="input-group">' .
  	 '<input type="text" class="form-control" name="name">' .
  	 '</div>' .
  	 '<b>' . $lang_news['post_text'] . '</b><br />' .
  	 bb_panel('message', 'text') .
  	 '<textarea class="form-control" name="text"></textarea>' .
  	 '<span class="help-block">' . $lang_news['tags_info'] . '</span>' .
  	 '<b>' .$lang_news['days_homepage'] . '</b><br />' .
  	 '<div class="input-group">' .
  	 '<input type="text" class="form-control" name="days_homepage" size="1" value="1" />' .
  	 '</div>' .
  	 '<input type="submit" class="btn btn-primary" name="add" value="' . $lang_news['send_news'] . '" />' .
  	 '</form></div>';
  
/**
 * Нижняя панель навигации
 */
echo '<div class="list-group">' .
     '<a class = "list-group-item" href="/news/"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
     '</div>';

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
