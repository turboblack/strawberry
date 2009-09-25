<?php
/**
 * @package Private
 * @access private
 */

include_once 'head.php';

$timer = new microTimer;
$timer->start();

if ($config['skin'] and file_exists(skins_directory.'/'.$config['skin'].'.skin.php')){
    require_once skins_directory.'/'.$config['skin'].'.skin.php';
} else {
    $using_safe_skin = true;
    require_once skins_directory.'/default.skin.php';
}

// If User is Not Logged In, Display The Login Page
if (!$is_logged_in){
	require modules_directory.'/login.mdu';
} elseif ($is_logged_in){
	if (check_referer){
		$self = $_SERVER['SCRIPT_NAME'];

	    if (!$self){
	    	$self = $_SERVER['REDIRECT_URL'];
	    }

	    if (!$self){
	    	$self = $PHP_SELF;
	    }

	    if (!eregi($self, $HTTP_REFERER) and $HTTP_REFERER){
	    	echo t('<h3>��������, �� ������ � ������� ��������!</h3>���������� ������� <a href="%url">�����</a> �� ������� � ����� ����� �����!<br />��������� �������� ������������, �������� <b>check_referer</b> � ����� %file �� �������� <i>false</i>.', array('url' => $PHP_SELF.'?action=logout', 'file' => 'inc/defined.inc.php'));
	        exit;
	    }
	}

// ********************************************************************************
// Include System Module
// ********************************************************************************
    $bad_keys = false;
    foreach (array('remove', 'delete', 'enable', 'disable', 'rename') as $k => $v){
	    if ($_GET['action'] == $v or $_GET['subaction'] == $v or $_GET['enabled'] or $_GET['disable'] or $_GET[$v]){
	        $bad_keys = true;
	    }
    }

    if ($plugin){
        if (!cute_get_rights($plugin, 'read') and $plugin != 'ajax'){
            msg('error', t('������'), t('��� �������� ������ � ����� ������!'));
        } else {
        	if (!cute_get_rights($plugin, 'write') and ($_POST or $_GET and $bad_keys)){
        		$_POST = $_GET = array();
        		msg('error', t('������'), t('��� �������� ������ � ����� ������!'));
        	} else {
        		run_actions('plugins');
        	}
        }
    } else {
	    $mod = ($mod ? $mod : 'main');

	    if (file_exists(modules_directory.'/'.$mod.'.mdu')){
	        if (!cute_get_rights($mod, 'read') and $mod != 'logout' and $mod != 'options'){
	            msg('error', t('������'), t('��� �������� ������ � ����� ������!'));
	        } else {
	        	if (!cute_get_rights($mod, 'write') and ($_POST or $_GET and $bad_keys)){
	        		$_POST = $_GET = array();
	        		msg('error', t('������'), t('��� �������� ������ � ����� ������!'));
	        	} else {
	        		include modules_directory.'/'.$mod.'.mdu';
	        	}
	       }
	    } else {
	        msg('error', t('������'), t('�������� ������.'));
	    }
    }
}
?>

<!-- �������� ������������� �� <?=$timer->stop(); ?> ���. ���� ������� �������� � ���� ������ <?=$sql->query_count(); ?> -->