<?php
/**
 * @package Defined
 * @access public
 */

/*
Если у вас версия CuteNews.RU 029 или ниже, AJ-Fork или оригинальный CuteNews,
то сначала сделайте конверт, потом, если хотите, меняйте.

Про функцию define() можно почитать на http://php.net/define

Вы можете изменять имена папок и файлов скрипта, а потом фиксировать эти изменения
тут, иначе будет фатальная ошибка (работать не будет).
Это полезно если вы боитесь хакеров, к примеру.

По большому счёту, стоит менять имена/расположение только общих папок.
*/

define('chmod', 0777);
define('cookie', true);
define('session', false);
define('check_referer', false);

// общии
define('data_directory', root_directory.'/data');
define('cache_directory', root_directory.'/cache');
define('backup_directory', root_directory.'/backup');

// с "S" на конце это системные директории
define('includes_directory', root_directory.'/inc');
define('updates_directory', includes_directory.'/upd');
define('databases_directory', includes_directory.'/db');
define('skins_directory', root_directory.'/skins');
define('modules_directory', includes_directory.'/mod');
define('plugins_directory', root_directory.'/plugins');
define('languages_directory', root_directory.'/lang');
define('templates_directory', data_directory.'/tpl');
define('mails_directory', data_directory.'/tpl/mail');

define('active_plugins_file', data_directory.'/plugins.php');
define('settings_file', data_directory.'/settings.php');
define('xfields_file', data_directory.'/xfields-data.php');
define('config_file', data_directory.'/config.php');
define('rufus_file', data_directory.'/urls.ini');

// набор функций, версия, если в плагине ниже, то он работать не будет
// не понятно, почему я это не использую
define('plugin_framework_version', '2.0');
// дефолтное значение для сортировки плагинов
define('plugin_default_priority', 50);
?>