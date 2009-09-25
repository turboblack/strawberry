<?php
/**
 * @package Defined
 * @access public
 */

/*
���� � ��� ������ CuteNews.RU 029 ��� ����, AJ-Fork ��� ������������ CuteNews,
�� ������� �������� �������, �����, ���� ������, �������.

��� ������� define() ����� �������� �� http://php.net/define

�� ������ �������� ����� ����� � ������ �������, � ����� ����������� ��� ���������
���, ����� ����� ��������� ������ (�������� �� �����).
��� ������� ���� �� ������� �������, � �������.

�� �������� �����, ����� ������ �����/������������ ������ ����� �����.
*/

define('chmod', 0777);
define('cookie', true);
define('session', false);
define('check_referer', false);

// �����
define('data_directory', root_directory.'/data');
define('cache_directory', root_directory.'/cache');
define('backup_directory', root_directory.'/backup');

// � "S" �� ����� ��� ��������� ����������
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

// ����� �������, ������, ���� � ������� ����, �� �� �������� �� �����
// �� �������, ������ � ��� �� ���������
define('plugin_framework_version', '2.0');
// ��������� �������� ��� ���������� ��������
define('plugin_default_priority', 50);
?>