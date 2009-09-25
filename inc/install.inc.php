<?php
/**
 * @package Install
 * @access private
 */

$handle = opendir(languages_directory);
while ($file = readdir($handle)){
    if ($file != '.' and $file != '..' and is_dir(languages_directory.'/'.$file)){
        $sys_con_lang_arr[$file] = strtoupper($file);
    }
}

$handle = opendir(databases_directory);
while ($file = readdir($handle)){
    if (substr($file, -3) != 'php' and is_file(databases_directory.'/'.$file)){
        $sys_con_database_arr[$file] = file_read(databases_directory.'/'.$file);
    }
}

$sys_con_charset_arr = array('X-MAC-ARABIC' => 'Arabic (Macintosh)', 'windows-1256' => 'Arabic (Windows)', 'iso-8859-2' => 'Central European (ISO-8859-2)', 'X-MAC-CENTRALEURROMAN' => 'Central European (MacCE)', 'windows-1250' => 'Central European (Windows-1250)', 'iso-8859-5' => 'Cyrillic (ISO-8859-5)', 'KOI8-R' => 'Cyrillic (KOI8-R)', 'x-mac-cyrillic' => 'Cyrillic (MacCyrillic)', 'windows-1251' => 'Cyrillic (Windows-1251)', 'iso-8859-7' => 'Greek (ISO-8859-7)', 'x-mac-greek' => 'Greek (MacGreek)', 'windows-1253' => 'Greek (Windows-1253)', 'X-MAC-HEBREW' => 'Hebrew (Macintosh)', 'windows-1255' => 'Hebrew (Windows)', 'Shift_JIS' => 'Japanese (Shift_JIS)', 'EUC-JP' => 'Japanese (EUC)', 'ISO-2022-JP' => 'Japanese (JIS)', 'EUC-KR' => 'Korean (EUC-KR)', 'gb2312' => 'Simplified Chinese (gb2312)', 'big5' => 'Traditional Chinese (big5)', 'X-MAC-THAI' => 'Thai (Macintosh)', 'Windows' => 'Thai (Windows)', 'iso-8859-5' => 'Turkish (Latin5)', 'X-MAC-TURKISH' => 'Turkish (Macintosh)', 'windows-1254' => 'Turkish (Windows)', 'utf-8' => 'UTF-8', 'iso-8859-1' => 'Western (Latin1)', 'macintosh' => 'Western (Macintosh)', 'windows-1252' => 'Western (Windows 1252)');

function check_writable($dir){

	$handle = opendir(root_directory.'/'.$dir);
	while (false !== ($file = readdir($handle))){
	    if ($file != '.' and $file != '..' and $file != '.htaccess' and substr($file, -3) != 'gif' and $file != 'tpl'){
	    	$path = $dir.'/'.$file;

	    	if (is_file($path)){
	    		echo '<font color="'.(is_writable($path) ? 'green' : 'red').'">'.$path.'</font><br />';
	    	} else {
	    		echo '<font color="'.(is_writable($path) ? 'green' : 'red').'">'.$path.'/</font><br />';
	    		check_writable($path);
	    	}
	    }
	}
}

$config['database']   = strtolower($_POST['database']);
$config['lang']       = $_POST['lang'];
$config['charset']    = $_POST['charset'];
$config['dbname']     = $_POST['dbname'];
$config['dbuser']     = $_POST['dbuser'];
$config['dbpassword'] = $_POST['dbpassword'];
$config['dbprefix']   = $_POST['dbprefix'];
$config['dbserver']   = $_POST['dbserver'];
$step                 = ($_GET['step'] ? $_GET['step'] : 1);
$url                  = preg_replace('/\/index.php$/i', '', reset($url = explode('?', $_SERVER['HTTP_REFERER'])));

include_once skins_directory.'/default.skin.php';

echoheader('options', t('Инсталяция Strawberry'));
?>

<table width="200" border="0" cellspacing="0" cellpadding="0">
<form action="<?=$_SERVER['PHP_SELF']; ?>?step=<?=($step + 1); ?>" method="post">
<input name="lang" type="hidden" value="<?=$config['lang']; ?>">
<input name="charset" type="hidden" value="<?=$config['charset']; ?>">
<input name="database" type="hidden" value="<?=$config['database']; ?>">

<?
if ($step == 1){
?>

 <tr>
  <td><?=t('Язык'); ?>
  <td><?=makeDropDown($sys_con_lang_arr, 'lang', 'ru'); ?>

 <tr>
  <td><?=t('Кодировка'); ?>
  <td><?=makeDropDown($sys_con_charset_arr, 'charset', 'windows-1251'); ?>

 <tr>
  <td><?=t('База данных'); ?>
  <td><?=makeDropDown($sys_con_database_arr, 'database', 'txtsql'); ?>

<?
} elseif ($step == 2){
	echo t('Проверка на права CHMOD (если какой-то фаил будет выделен красным, то нужно зайти по FTP и проставить права как написано в ридми; или нажать &quot;Обновить&quot; у браузера, в этом случае скрипт <i>попробует</i> сам всё наладить):').'<br /><br />';
	echo '<font color="'.(is_writable('cache') ? 'green' : 'red').'">cache/</font><br />';
	echo '<font color="'.(is_writable('lang/'.$config['lang']) ? 'green' : 'red').'">lang/'.$config['lang'].'/</font><br />';
	echo check_writable('data');
} elseif ($step == 3){
	if ($config['database'] == 'txtsql'){
		$disabled = ' disabled';
	}
?>

 <tr>
  <td><?=t('Логин'); ?>
  <td><input name="login" type="text" value="">
 <tr>
  <td><?=t('Пароль'); ?>
  <td><input name="password" type="text" value="">
 <tr>
  <td colspan="2"><br /><br /><b><?=t('База данных'); ?></b>:
 <tr>
  <td><?=t('Имя пользователя'); ?>
  <td><input name="dbuser" type="text" value=""<?=$disabled; ?>>
 <tr>
  <td><?=t('Пароль'); ?>
  <td><input name="dbpassword" type="text" value=""<?=$disabled; ?>>
 <tr>
  <td><?=t('Сервер базы данных'); ?>
  <td><input name="dbserver" type="text" value="localhost"<?=$disabled; ?>>
 <tr>
  <td><?=t('Имя базы'); ?>
  <td><input name="dbname" type="text" value=""<?=$disabled; ?>>
 <tr>
  <td><?=t('Префикс таблиц'); ?>
  <td><input name="dbprefix" type="text" value="cute_"<?=$disabled; ?>>

<?
} elseif ($step == 4){
	include databases_directory.'/'.$config['database'].'.inc.php';
	include databases_directory.'/database.inc.php';

	$config = array(
	'version_name' => $version_name,
	'version_id' => $version_id,
	'http_script_dir' => $url,
	'http_home_url' => $url.'/example/index.php',
	'path_image_upload' => $url.'/data/upimages',
	'home_title' => 'CuteNews.RU Homepage',
	'skin' => 'default',
	'lang' => $config['lang'],
	'cache' => '0',
	'database' => $config['database'],
	'dbname' => $config['dbname'],
	'dbuser' => $config['dbuser'],
	'dbpassword' => $config['dbpassword'],
	'dbprefix' => $config['dbprefix'],
	'dbserver' => $config['dbserver'],
	'date_adjust' => '0',
	'mod_rewrite' => '0',
	'pages_section' => '3',
	'pages_break' => '10',
	'cpages_section' => '3',
	'cpages_break' => '10',
	'timestamp_active' => 'j M Y',
	'use_avatar' => '1',
	'date_header' => '1',
	'date_headerformat' => 'l, j M Y',
	'send_mail_upon_new' => '1',
	'send_mail_upon_posting' => '1',
	'admin_mail' => $mail,
	'auto_wrap' => '70',
	'flood_time' => '30',
	'smilies' => 'angry, evil, grin, laugh, sad, smile, wink',
	'smilies_line' => '0',
	'reverse_comments' => '0',
	'cnumber' => '0',
	'only_registered_comment' => '0',
	'timestamp_comment' => 'j M Y - H:i',
	'user_avatar' => '1',
	'path_userpic_upload' => $url.'/data/userpics',
	'use_images_uf' => '0',
	'avatar_w' => '100',
	'avatar_h' => '100',
	'gmtoffset' => '180', // московское (GMT +03:00 - Москва, Питер, Волгоград)
	'charset' => $config['charset']
	);

	if ($config['database'] == 'txtsql'){
	    if (!$sql->db_exists('base')){
	        $sql->createdb(array('db' => 'base'));
	    }

	    $sql->selectdb('base');
	} else {
	    $sql->selectdb($config['dbname'], $config['dbprefix']);
	}

	foreach ($database as $k => $v){
	    if (!$sql->table_exists($k)){
	        $sql->createtable(array('table' => $k, 'columns' => $v));
	    }

	    if ($sql->table_exists($k)){
	        echo '<br /><font color="green">'.t('Таблица "%table" создана', array('table' => $k)).'</font>';
	    }
	}

	save_config($config);

	$sql->insert(array(
	'table'  => 'users',
	'values' => array(
	            'date'      => time,
	            'usergroup' => 1,
	            'username'  => $_POST['login'],
	            'password'  => md5x($_POST['password'])
	            )
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Администраторы',
				'access'      => 'full',
				'permissions' => ''
				)
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Редакторы',
				'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}s:4:"read";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}}',
				'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"1";s:10:"delete_all";s:1:"1";s:8:"comments";s:1:"1";}'
				)
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Журналисты',
				'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
				'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
				)
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Комментаторы',
				'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
				'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"0";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
				)
	));
}
?>

 <tr>
  <td colspan="2"><br /><br /><input type="submit" value="<?=t('Далее (шаг %step) &raquo;&raquo;', array('step' => (($step + 1) == 5 ? t('последний') : ($step + 1)))); ?>">
</form>
</table>

<?
echofooter();
exit;
?>