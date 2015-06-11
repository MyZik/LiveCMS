<?php
/****
 * @package LiveCMS
 * @link livecms.org
 * @author MyZik
 * @version See attached file VERSION.txt
 * @license See attached file LICENSE.txt
 * @copyright Copyright (C) LiveCMS Development Team
 ****/
class Functions extends Core {
	/**
	 * Функция вывода успешной информации
	 */
	public static function display_message($var = '', $backLink = '') {
		global $lang;
		if (empty($backLink))
		 return '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;' . $var . '</div>';
		else
			return '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;' . $var . '</div>' .
				   '<div class="list-group">' .
				   '<a class = "list-group-item" href="' . $backLink . '"><span class="glyphicon glyphicon-chevron-left"></span> ' . $lang['back'] . '</a>' .
				   '</div>';
	}

	/**
	 * Функция постраничной навигации, изначально взятая с форума SMF, и доработанная авторами JohnCMS
	 */
	public static function display_pagination($url, $start, $total, $countMess) {
		global $countMess;
		$out[] = '<ul class="pagination">';
		$neighbors = 2;
		if ($start >= $total)
			$start = max(0, $total - (($total % $countMess) == 0 ? $countMess : ($total % $countMess)));
		else
			$start = max(0, (int)$start - ((int)$start % (int)$countMess));
		$base_link = '<li><a href="' . strtr($url, array('%' => '%%')) . 'page=%d' . '">%s</a></li>';
		$out[] = $start == 0 ? '' : sprintf($base_link, $start / $countMess, '&laquo;');
		if ($start > $countMess * $neighbors)
			$out[] = sprintf($base_link, 1, '1');
		if ($start > $countMess * ($neighbors + 1))
			$out[] = '<li class="disabled"><span style="font-weight: bold;">...</span></li>';
		for ($nCont = $neighbors; $nCont >= 1; $nCont--)
			if ($start >= $countMess * $nCont) {
				$tmpStart = $start - $countMess * $nCont;
				$out[] = sprintf($base_link, $tmpStart / $countMess + 1, $tmpStart / $countMess + 1);
			}
		$out[] = '<li class="active"><span style="font-weight: bold;">' . ($start / $countMess + 1) . '</span></li>';
		$tmpMaxPages = (int)(($total - 1) / $countMess) * $countMess;
		for ($nCont = 1; $nCont <= $neighbors; $nCont++)
			if ($start + $countMess * $nCont <= $tmpMaxPages) {
				$tmpStart = $start + $countMess * $nCont;
				$out[] = sprintf($base_link, $tmpStart / $countMess + 1, $tmpStart / $countMess + 1);
			}
		if ($start + $countMess * ($neighbors + 1) < $tmpMaxPages)
			$out[] = '<li class="active"><span style="font-weight: bold;">...</span></li>';
			if ($start + $countMess * $neighbors < $tmpMaxPages)
				$out[] = sprintf($base_link, $tmpMaxPages / $countMess + 1, $tmpMaxPages / $countMess + 1);
			if ($start + $countMess < $total) {
				$display_page = ($start + $countMess) > $total ? $total : ($start / $countMess + 2);
				$out[] = sprintf($base_link, $display_page, '&raquo;');
			}
			$out[] = '</ul>';
		return implode(' ', $out);
	}

	/**
	 * Функция обработки смайликов
	 */
	public static function smileys($text) {
		$smileys_dir = '/design/smileys/'; // папка со смайликами
		$smileys_array = array(
				':-)' => '<img src="' . $smileys_dir . 'smile.png" alt="Smile" />',
				':)' => '<img src="' . $smileys_dir . 'smile.png" alt="Smile" />',
				'=)' => '<img src="' . $smileys_dir . 'smile.png" alt="Smile" />',
				':-(' => '<img src="' . $smileys_dir . 'sad.png" alt="Smile" />',
				':(' => '<img src="' . $smileys_dir . 'sad.png" alt="Smile" />',
				'=(' => '<img src="' . $smileys_dir . 'sad.png" alt="Smile" />',
				':-D' => '<img src="' . $smileys_dir . 'biggrin.png" alt="Smile" />',
				':D' => '<img src="' . $smileys_dir . 'biggrin.png" alt="Smile" />',
				'=D' => '<img src="' . $smileys_dir . 'biggrin.png" alt="Smile" />',
				':-P' => '<img src="' . $smileys_dir . 'togue.png" alt="Smile" />',
				':P' => '<img src="' . $smileys_dir . 'togue.png" alt="Smile" />',
				'=P' => '<img src="' . $smileys_dir . 'togue.png" alt="Smile" />',
				':-O' => '<img src="' . $smileys_dir . 'shock.png" alt="Smile" />',
				'=O' => '<img src="' . $smileys_dir . 'shock.png" alt="Smile" />',
				'o_O' => '<img src="' . $smileys_dir . 'shock.png" alt="Smile" />',
				';-(' => '<img src="' . $smileys_dir . 'cry.png" alt="Smile" />',
				';(' => '<img src="' . $smileys_dir . 'cry.png" alt="Smile" />',
				';-)' => '<img src="' . $smileys_dir . 'wink.png" alt="Smile" />',
				';)' => '<img src="' . $smileys_dir . 'wink.png" alt="Smile" />',
				':-[' => '<img src="' . $smileys_dir . 'hesitate.png" alt="Smile" />',
				':[' => '<img src="' . $smileys_dir . 'hesitate.png" alt="Smile" />',
				'=[' => '<img src="' . $smileys_dir . 'hesitate.png" alt="Smile" />',
				':-*' => '<img src="' . $smileys_dir . 'kiss.png" alt="Smile" />',
				'=*' => '<img src="' . $smileys_dir . 'kiss.png" alt="Smile" />',
				':kiss:' => '<img src="' . $smileys_dir . 'kiss.png" alt="Smile" />',
				'B-)' => '<img src="' . $smileys_dir . 'cool.png" alt="Smile" />',
				'B)' => '<img src="' . $smileys_dir . 'cool.png" alt="Smile" />',
				':cool:' => '<img src="' . $smileys_dir . 'cool.png" alt="Smile" />',
				':@' => '<img src="' . $smileys_dir . 'fu.png" alt="Smile" />',
				':fu:' => '<img src="' . $smileys_dir . 'fu.png" alt="Smile" />',
				'|:>' => '<img src="' . $smileys_dir . 'angry.png" alt="Smile" />',
				':angry:' => '<img src="' . $smileys_dir . 'angry.png" alt="Smile" />',
				':-Z' => '<img src="' . $smileys_dir . 'sleep.png" alt="Smile" />',
						':sleep:' => '<img src="' . $smileys_dir . 'sleep.png" alt="Smile" />',
						':bravo:' => '<img src="' . $smileys_dir . 'bravo.png" alt="Smile" />',
						':angel:' => '<img src="' . $smileys_dir . 'angel.png" alt="Smile" />',
				':crazy:' => '<img src="' . $smileys_dir . 'crazy.png" alt="Smile" />',
				':lol:' => '<img src="' . $smileys_dir . 'lol.png" alt="Smile" />');
		return strtr($text, $smileys_array);
	}

	/**
	 * Функция обработки BB-тегов
	 */
	public static function bb_codes($text) {
		$text = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $text); // Жирный текст
		$text = preg_replace('#\[u\](.*?)\[/u\]#si', '<span style="text-decoration: underline;">\1</span>', $text); // Подчеркивание
		$text = preg_replace('#\[i\](.*?)\[/i\]#si', '<span style="font-style: italic;">\1</span>', $text); // Курсив
		$text = preg_replace('#\[s\](.*?)\[/s\]#si', '<strike>\1</strike>', $text); // Перечеркивание
		$text = preg_replace('#\[q\](.*?)\[/q\]#si', '<div class="cit">\1</div>', $text); // Цитата
		$text = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color:red">\1</span>', $text); // Красный текст
		$text = preg_replace('#\[green\](.*?)\[/green\]#si', '<span style="color: green">\1</span>', $text); // Зеленый текст
		$text = preg_replace('#\[blue\](.*?)\[/blue\]#si', '<span style="color:blue">\1</span>', $text); // Синий текст
		$text = preg_replace('!\[color=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/color]!is', '<span style="color:\1">\2</span>', $text); // Цвет шрифта
		$text = preg_replace('!\[bg=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/bg]!is', '<span style="background-color:\1">\2</span>', $text); // Цвет фона
		$text = preg_replace("/(https?|ftps?)\:\/\/([a-z0-9\.\/\-\?\_\=&;]*)\b/i", "<a href=\"$1://$2\" target=\"_blank\">$1://$2</a>", $text); // Ссылки
		$text = preg_replace("/\[url=(.+)\](.+)\[\/url\]/isU", "<a href='$1'>$2</a>", $text); // Ссылки с названием
		return $text;
	}

	/**
	 * Данная функция используется для вывода текста с BB-тегами и смайликами.
	 */
	public static function output_text($var) {
		return self::smileys(self::bb_codes(nl2br($var)));
	}
	
	/**
	 * Проверка длины символов. Включая русские
	 */
	public static function strlen_rus($var) {
		$rus_symbols = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
		return strlen(str_replace($rus_symbols, '0', $var));
	}
	
	/**
	 * Функция обработки времени.
	 * Если выбрано $type = 1, выведет "Сегодня в 10:25"
	 * Если выбрано $type = 2, выведет "4 часа назад"
	 */
	public static function display_time($var, $type = 1) {
		global $lang;
		if ($type = 1) {
	 		// Если время не задано берем текущее
	 		if ($var == NULL)
	 			$var = time();

	 		// Время + Дата
	 		$full_time = date('d.m.Y в H:i', $var);

	 		// Дата
	 		$date = date('d.m.Y', $var);

	 		// Время
	 		$time = date('H:i', $var);

	 		// Если текущаяя дата совпадает с заданной
	 		if ($date == date('d.m.Y'))
	 			$full_time = $lang['today'] . ' ' . date('H:i', $var);

	 		// Вчерашняя дата
	 		if ($date == date('d.m.Y', time()-60*60*24))
	 			$full_time = $lang['yesterday'] . ' ' . date('H:i', $var);

	 		return $full_time;
		}
	}
	
	/**
	 * Расширенный вывод данных пользователя
	 */
	public static function display_user($user_id = 0, $info = '', $body = '', $end = '') {
		global $db, $cms_set, $set_user, $lang, $user;
		$profile = $db->query("SELECT * FROM `users` WHERE `id` = '" . $user_id . "'")->fetch();
		// Показываем аватары
		if ($set_user['show_avatars'] == 'yes') {
			echo '<table cellpadding="0" cellspacing="0"><tr><td>';
			if (file_exists(HOME . '/files/avatar/' . $profile['id'] . '.png'))
				echo '<img src="/files/avatar/' . $profile['id'] . '.png" width="32" height="32" alt="' . $profile['login'] . '" />&#160;';
			else
				echo '<img src="/design/avatar_default.png" width="32" height="32" alt="' . $profile['login'] . '" />&#160;';
			echo '</td><td>';
		}
		// В зависимости от настроек юзера показываем его пол иконкой / текстом
		if ($set_user['sex_view'] == 'icons')
			$sex_view = '<img src="/design/themes/' . $set_user['theme'] . '/images/user_' . ($profile['sex'] == 'm' ? 'm.png' : 'w.png') . '" alt="" />';
		elseif ($set_user['sex_view'] == 'text')
			$sex_view = ($profile['sex'] == 'm' ? $lang['sex_m_text'] : $lang['sex_w_text']);
		else
			$sex_view = '<img src="/design/themes/' . $set_user['theme'] . '/images/delete_user.png" alt="" />';
		$rights = array (
				0 => '',
				2 => '(CMod)',
				3 => '(FMod)',
				7 => '(Smd)',
				8 => '(Adm)',
				10 => '(SV!)'
		);
		$uRights =  ' <span style="color:blue">' . $rights[$profile['rights']] . '</span>';
	
		echo $sex_view . '&nbsp;' . (isset($user) ? '<a href="/user/profile.php?id=' . $profile['id'] . '"><b>' . htmlspecialchars($profile['login']) . '</b></a>' : '<b>' . $profile['login'] . '</b>') . (time() > $profile['date_last_entry'] + 600 ? ' <span style="color:red">[Off]</span>' : ' <span style="color:green">[On]</span>') . $uRights;
	
		if ($info)
			echo $info;
	
		if (!empty($profile['status']))
			echo '<br /><span class="status"><img src="/design/themes/' . $set_user['theme'] . '/images/status.png" alt="Status" align="middle" /> ' . $profile['status'] . '</span>';
	
		if ($set_user['show_avatars'] == 'yes')
			echo '</td></tr></table>';
		else
			echo '<br />';
	
		if ($body)
			echo $body;
		if ($end)
			echo $end;
	}
	
	/**
	 * Основной вывод данных пользователя
	 */
	 public static function _display_user($user_id = 0, $info = '') {
	 	global $db, $set_user, $lang, $user;
	 	$profile = $db->query("SELECT * FROM `users` WHERE `id` = '" . $user_id . "'")->fetch();
	 	// В зависимости от настроек юзера показываем его пол иконкой / текстом
		 if ($set_user['sex_view'] == 'icons')
	 		$sex_view = '<img src="/design/themes/' . $set_user['theme'] . '/images/user_' . ($profile['sex'] == 'm' ? 'm.png' : 'w.png') . '" alt="" />';
	 	elseif ($set_user['sex_view'] == 'text')
	 		$sex_view = ($profile['sex'] == 'm' ? $lang['sex_m_text'] : $lang['sex_w_text']);
	 	else
	 		$sex_view = '<img src="/design/themes/' . $set_user['theme'] . '/images/delete_user.png" alt="" />';
	 	$rights = array (
	 			0 => '',
	 			2 => '(CMod)',
	 			3 => '(FMod)',
	 			7 => '(Smd)',
	 			8 => '(Adm)',
	 			10 => '(SV!)'
	 			);
	 	$uRights =  ' <span style="color:blue">' . $rights[$profile['rights']] . '</span>';
	
	 	echo $sex_view . '&nbsp;' . (isset($user) ? '<a href="/user/profile.php?id=' . $profile['id'] . '"><b>' . htmlspecialchars($profile['login']) . '</b></a>' : '<b>' . $profile['login'] . '</b>') . (time() > $profile['date_last_entry'] + 600 ? ' <span style="color:red">[Off]</span>' : ' <span style="color:green">[On]</span>') . $uRights;
	
	 	if ($info)
	 		echo $info;
	 }
}
