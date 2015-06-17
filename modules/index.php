<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/

$title = 'Добро пожаловать'; // Заголовок страницы
$module = 'homepage'; // Название модуля
require HOME .'/includes/header.php'; // Подключаем шапку

$icons_dir = '/design/icons/mainmenu/'; // папка и иконками главной страницы
$Read = (isset($user) ? $user['read_news'] = $user['read_news'] : $user['read_news'] = 'no');

// счетчики
$l_news = $db->query("SELECT * FROM `cms_news` ORDER BY `id` DESC LIMIT 1")->fetch();
$news = $db->query("SELECT * FROM `cms_news`")->rowCount();
$mini_chat_new = $db->query("SELECT COUNT(*) FROM `mini_chat` WHERE `time` > '" . (time() - 86400) . "'")->fetchColumn(); // новые сообщения в мини чате
$mini_chat = $db->query("SELECT COUNT(*) FROM `mini_chat`")->fetchColumn() . ($mini_chat_new == 0 ? '' : ' <span style="color:red">+' . $mini_chat_new . '</span>'); // мини чат
$forum = $db->query("SELECT COUNT(*) FROM `cms_forum_topics`")->fetchColumn() . ' / ' . $db->query("SELECT COUNT(*) FROM `cms_forum_messages`")->fetchColumn(); // форум
$days = 2; // число дней
$time = $days * 60 * 60 * 24;
$forum_upd = $db->query("SELECT COUNT(*) FROM `cms_forum_topics` WHERE `time_update` > '" . (time() - $time) . "'")->fetchColumn();
$forum_update = ($forum_upd > 0 ? ' / <a href="/forum/update.php"><span style="color:red">+' . $forum_upd . '</span></a>' : ''); // обновленные темы на форуме
$users_new = $db->query("SELECT COUNT(*) FROM `users` WHERE `date_reg` > '" . (time() - 86400) . "'")->fetchColumn(); // новые пользователи
$users = $db->query("SELECT COUNT(*) FROM `users`")->fetchColumn() . ($users_new == 0 ? '' : ' <span style="color:red">+' . $users_new . '</span>'); // пользователи
$online_u = $db->query("SELECT COUNT(*) FROM `users` WHERE `date_last_entry` > '" . (time() - 600) . "'")->fetchColumn(); // OnLine пользователи

if (isset($_GET['read']) && isset($user)) {
	$db->query("UPDATE `users` SET `read_news` = 'yes' WHERE `id` = '" . $user['id'] . "'");
}

/**
 * Вывод ссылок
 */
echo '<div class="list-group">'.
	 '<div class="list-group-item list-group-item-success"><span class="glyphicon glyphicon-info-sign"></span> ' . $lang['section_useful'] . '</div>';
echo '<a class="list-group-item" href="news/"><b>' . $lang['news'] . '</b> <span class="badge">' . $news . '</span>' .
	 ($l_news['time'] > (time() - 86400) ? ' | <span class="glyphicon glyphicon-pushpin"></span> ' . $l_news['name'] . ' <span class="label label-warning">!new</span>' : '');
echo '<a class="list-group-item" href="pages/faq.php"> ' . $lang['faq'] . '</a>'.
	 '</div>';

echo '<div class="list-group">' .
	 '<div class="list-group-item list-group-item-info"><span class="glyphicon glyphicon-comment"></span> ' . $lang['section_comm'] . '</div>';
echo '<a class="list-group-item" href="mini_chat/">' . $lang['mini_chat'] . ' <span class="badge">' . $mini_chat . '</span></a>';
echo '<a class="list-group-item" href="forum/">' . $lang['forum'] . ' <span class="badge">' . $forum . $forum_update . '</span></a>';
echo '</div>';
echo '<div class="list-group">'.
	 '<div class="list-group-item list-group-item-warning"><span class="glyphicon glyphicon-bookmark"></span> ' . $lang['section_users'] . '</div>';
echo '<a class="list-group-item" href="users/users.php">' . $lang['all_users'] . ' <span class="badge">' . $users . '</span></a>';
echo '<a class="list-group-item" href="users/online.php">' . $lang['on_u'] . ' <span class="badge">' . $online_u . '</span></a>';
echo '</div>';
echo '</div>';

require_once(HOME .'/includes/footer.php'); // Подключаем ноги
