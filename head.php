<?php
/**
 * @package Private
 * @access private
 */

error_reporting(E_ALL & ~E_NOTICE);

$vars    = array('skip', 'page', 'cpage', 'action', 'id', 'ucat', 'category', 'number', 'template', 'static', 'year', 'month', 'day', 'title', 'sort', 'user', 'author', 'time', 'link', 'tpl');
$default = array('cutepath' => dirname(__FILE__), 'phpversion' => @phpversion(), 'HTTP_REFERER' => $_SERVER['HTTP_REFERER'], 'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'], 'PHP_SELF' => $_SERVER['PHP_SELF'], 'QUERY_STRING' => $_SERVER['QUERY_STRING'], 'is_logged_in' => false, 'cache_uniq' => 0, 'config' => array(), 'categories' => array(), 'users' => array(), 'usergroups' => array(), 'member' => array(), 'tpl' => array(), 'post' => array(), 'gettext' => array(), 'version_name' => 'Strawberry', 'version_id' => '1.1.2');

foreach ($vars as $k){
	if ($_POST[$k]){
	    $$k = @htmlspecialchars($_POST[$k]);
	} elseif ($_GET[$k]){
	    $$k = @htmlspecialchars($_GET[$k]);
    } else {
        $$k = @htmlspecialchars($$k);
    }
}

foreach ($default as $k => $v){
	unset($_GET[$k], $_POST[$k], $_SESSION[$k], $_COOKIE[$k], $_ENV[$k]);
	$$k = $v;
}

define('root_directory', dirname(__FILE__));
define('rootpath', root_directory);

include_once root_directory.'/inc/defined.inc.php';
include_once includes_directory.'/functions.inc.php';
include_once includes_directory.'/plugins.inc.php';
include_once includes_directory.'/plugins.default.php';
include_once includes_directory.'/cache.inc.php';
include config_file;

define('time', (time() + ($config['gmtoffset'] - (date('Z') / 60)) * 60));

if (@chmod(cache_directory, 0777)){
	@chmoddir(data_directory, 0777);
	@chmod(data_directory, 0755);
	@chmod(backup_directory, 0777);
}

if (!@filesize(data_directory.'/config.php')){
    include includes_directory.'/install.inc.php';
} elseif (!$config['version_name']){
	$config['database'] = ($config['database'] ? $config['database'] : 'txtsql');
	$config['lang']     = ($config['lang'] ? $config['lang'] : 'en');
	$config['dbname']   = ($config['database'] == 'txtsql' ? 'base' : $config['dbname']);
	file_write(config_file, preg_replace('/\$configt([a-z0-9_]+)/i', '$config[\'\\1\']', str_replace('path_userpiс_upload', 'path_userpic_upload', file_read(config_file))));
}

include config_file;
include_once languages_directory.'/'.$config['lang'].'/functions.php';
include_once databases_directory.'/'.$config['database'].'.inc.php';

$sql->strict(false);
$cache = new cache($config['cache'], cache_directory, 3600);
$_GET  = cute_stripslashes($_GET);
$_GET  = cute_htmlspecialchars($_GET);
$_POST = cute_stripslashes($_POST);

@extract($_GET, EXTR_SKIP);
@extract($_POST, EXTR_SKIP);
@extract($_COOKIE, EXTR_SKIP);
@extract($_SESSION, EXTR_SKIP);
@extract($_ENV, EXTR_SKIP);

/*version_compare($version_id, $config['version_id'], '>')*/
if ($version_id != $config['version_id'] or $version_name != $config['version_name']){
	include updates_directory.'/update.inc.php';
}

if ($id and !$post = $cache->unserialize('post', $id)){
    $post  = $sql->select(array(
    		 'table' => 'news',
    		 'join' => array('table' => 'story', 'where' => 'id = post_id'),
    		 'where' => array("id = $id", 'or', "url = $id")
    		 ));
	$post = $cache->serialize(reset($post));
}

if (!$categories = $cache->unserialize('categories')){	$categories = array();

	foreach ($sql->select(array('table' => 'categories', 'orderby' => array('id', 'ASC'))) as $row){
	    $categories[$row['id']] = $row;
	}

	$categories = $cache->serialize($categories);
}

if (!$users = $cache->unserialize('users')){
	foreach ($sql->select(array('table' => 'users', 'orderby' => array('id', 'ASC'), 'where' => array('deleted != 1'))) as $row){
	    $row['name'] = ($row['name'] ? $row['name'] : $row['username']);
	    $users[$row['username']] = $row;
	}

	$users = $cache->serialize($users);
}

if (!$usergroups = $cache->unserialize('usergroups')){
	foreach ($sql->select(array('table' => 'usergroups', 'orderby' => array('id', 'ASC'))) as $row){
	    $row['access'] = ($row['access'] == 'full' ? $row['access'] : unserialize($row['access']));
	    $row['permissions'] = unserialize($row['permissions']);
	    $usergroups[$row['id']] = $row;
	}

	$usergroups = $cache->serialize($usergroups);
}

if (substr($HTTP_REFERER, -1) == '/'){
	$HTTP_REFERER .= $PHP_SELF;
}

if ($username){
    if ($_COOKIE[$config['cookie_prefix'].'md5_password']){
        $cmd5_password = $_COOKIE[$config['cookie_prefix'].'md5_password'];
    } else {
        $cmd5_password = md5x($password);
    }

    if (check_login($username, $cmd5_password)){
        $is_logged_in = true;

        cute_setcookie('lastusername', $username, (time + 1012324305), '/');
        cute_setcookie('username', $username, (time + 3600 * 24 * 365), '/');
        cute_setcookie('md5_password', $cmd5_password, (time + 3600 * 24 * 365), '/');

    } else {
        $result = '<font color="red">'.t('Неправильное имя пользователя или пароль!').'</font>';
        $is_logged_in = false;

        cute_setcookie('username', '', (time - 3600 * 24 * 365), '/');
        cute_setcookie('md5_password', '', (time - 3600 * 24 * 365), '/');
   }
}

if ($is_logged_in){
	/* надо доделать нормально
    $sql->update(array(
    'table'  => 'users',
    'where'  => array("username = $member[username]"),
    'values' => array('last_visit' => time)
    ));
    */
}

LoadActivePlugins();
run_actions('head');
?>