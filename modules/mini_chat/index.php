<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$lang_mchat = $core->load_lang('mini_chat'); // Подключаем файл языка

$title = $lang_mchat['mini_chat']; // Заголовок страницы
$module = 'mini_chat'; // Модуль
require_once(HOME .'/includes/header.php'); // Подключаем шапку

/**
 * Небольшая панель навигации
 */
echo '<ul class="breadcrumb">' .
	 '<li class="active">' . $lang['mini_chat'] . '</li>' .
	 '</ul>';

/**
 * Ответ на сообщение
 */
if (isset($_GET['reply']) && $_GET['reply'] != $user['id']) {
	/**
     * Проверяем, верен ли введенный параметр
     */
	if (!is_numeric($_GET['reply']))
    	$err[] = $lang['error_parameter'];

    /**
     * Проверяем, существует ли пользователь
     */
    if (is_numeric($_GET['reply']) && $db->query("SELECT COUNT(*) FROM `users` WHERE `id` = '" . Core::num($_GET['reply']) . "' LIMIT 1")->fetchColumn() == 0)
      	$err[] = $lang_mchat['undefined_user'];

    /**
     * Если не было ошибок, определяем данные пользователя
     */
    if (!isset($err)) {
      	$reply = $db->query("SELECT * FROM `users` WHERE `id` = '" . Core::num($_GET['reply']) . "'")->fetch();
      	$reply_user = '[url=/users/profile.php?id=' . $reply['id'] . ']@' . $reply['login'] . '[/url], ';
        } else {
        	echo Core::display_error($err);
      		$reply_user = '';
        }
    }

  /**
   * Удаление сообщения
   */
if (isset($_GET['delete']) && ($user['rights'] == 2 || $user['rights'] >= 7)) {
	/**
     * Проверяем, верен ли введенный параметр
     */
    if (!is_numeric($_GET['delete']))
    	$err[] = $lang['error_parameter'];

    /**
     * Проверяем, существует ли сообщение
     */
    if (is_numeric($_GET['delete']) && $db->query("SELECT * FROM `mini_chat` WHERE `id` = '" . Core::num($_GET['delete']) . "' LIMIT 1") -> rowCount() == 0)
      	$err[] = $lang_mchat['undefined_message'];

    /**
     * Если не было ошибок, удаляем сообщение
     */
	if (!isset($err)) {
    	$del_user = $db->query("SELECT `user_id` FROM `mini_chat` WHERE `id` = '" . Core::num($_GET['delete']) . "'") -> fetch();
        $db->query("UPDATE `users` SET `postsmchat` = `postsmchat` - 1 WHERE `id` = '" . $del_user['user_id'] . "'");
        $db->query("UPDATE `users` SET `balls` = `balls` - 1 WHERE `id` = '" . $del_user['user_id'] . "' LIMIT 1");
        $db->query("DELETE FROM `mini_chat` WHERE `id` = '" . Core::num($_GET['delete']) . "' LIMIT 1");
        echo Functions::display_message($lang_mchat['delete_message_success']);
	} else {
    	echo Core::display_error($err); // выводим ошибки
    }
}

  /**
   * Добавление сообщения
   */
if (isset($_POST['send']) && isset($user)) {
	$message = mb_substr($_POST['message'], 0, 1024);

    /**
     * Проверяем, пустое ли сообщение
     */
    if (empty($_POST['message']))
      	$err[] = $lang_mchat['error_message_length'];

    /**
     * Проверка на флуд
     */
    if (($user['lastpost'] + $cms_set['antiflood_time']) >= time())
      	$err[] = $lang['error_antiflood'];

    /**
     * Если не было ошибок, заносим данные
     */
    if (!isset($err)) {
    	/**
    	 * Оповещение в журнал (уведомление об ответе, если есть)
    	 */
    	if (!empty($reply_user)) {
    		$journalMessage = $lang['journal_user'] . ' [url=/users/profile.php?id=' . $user['id'] . ']' . $user['login'] . '[/url] ' . $lang['journal_mini_chat_answer'];
    		journal_add($reply['id'], $journalMessage);
    		echo $reply['id'] . ' ' . $journalMessage;
    	}
    	
    	/**
    	 * Подготавливаем запрос
    	 */
    	$st = $db->prepare("INSERT INTO `mini_chat` (`user_id`, `time`, `message`)
    			VALUES (:user_id, :time, :message)");
    	/**
    	 * Заносим сообщение в БД
    	 */
    	$st->execute(array('user_id' => $user['id'],
    			'time' => time(),
    			'message' => $message
    	));
      	/**
         * Начисление баллов и обновление счетчика сообщений
      	 */
      	$db->query("UPDATE `users` SET `balls` = `balls` + 1,
      			`postsmchat` = `postsmchat` + 1,
      			`lastpost` = '" . time() . "'
      			WHERE `id` = '" . $user['id'] . "' LIMIT 1");
      	
      	echo Functions::display_message($lang_mchat['add_success']);
     } else {
        echo Core::display_error($err); // выводим ошибки
     }
}

/**
 * Форма (для зарегистрированных)
 */
if (isset($user)) {
	echo '<div class="list-group-item"><form name="message" action="/mini_chat/" method="post">' .
  	$lang['enter_message'] . '<br />' . 
  	bb_panel('message', 'message') .
  	'<textarea class="form-control" name="message">' . (!empty($reply_user) ? $reply_user : '') . '</textarea>' .
  	'<input type="submit" class="btn btn-primary" name="send" value="' . $lang['send'] . '" />' .
  	'</form></div>';
}

/**
 * Настраиваем пагинацию
 */
$total = $db->query("SELECT * FROM `mini_chat`")->rowCount();
$req = $db->query("SELECT * FROM `mini_chat` ORDER BY `id` DESC LIMIT $start, $countMess");

/**
 * Если нет результатов, выводим уведомление
 */

if ($total < 1)
	echo '<div class="alert alert-danger">' . $lang_mchat['no_messages'] . '</div>';

/**
 * Вывод сообщений
 */
$i = 0;
while ($res = $req->fetch()) {
	echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
    $info = ' [' . $functions->display_time($res['time']) . ']' . (isset($user) && $user['id'] != $res['user_id'] ? ' <a href="?reply=' . $res['user_id'] . '">' . $lang_mchat['reply'] . '</a>' : '');
    $body = Functions::output_text(Core::textFilter($res['message']));
    $end = ((isset($user)) && ($user['rights'] == 2 || $user['rights'] >= 7) ? '<br />[<a href="?delete=' . $res['id'] . '">' . $lang['delete'] . '</a>]' : '');
    echo Functions::display_user($res['user_id'], $info, $body, $end) . '</div>';
    $i++;
} 

/**
 * Пагинация
 */
if ($total > $countMess)
	echo Functions::display_pagination('?', $start, $total, $countMess);

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
