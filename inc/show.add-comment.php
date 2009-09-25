<?php
/**
 * @package Show
 * @access private
 */

include_once substr(dirname(__FILE__), 0, -4).'/head.php';

foreach ($_POST as $k => $v){
	$$k = iconv('utf-8', $config['charset'], $v);
}

header('Content-type: text/html; charset='.$config['charset']);

$name = htmlspecialchars(trim($name));
$mail = trim($mail);
$error_message = array();
$allow_add_comment = true;
$allow_add_comment = run_filters('allow-add-comment', $allow_add_comment);

//----------------------------------
// Check the lenght of comment, include name + mail
//----------------------------------

if (strlen($name) > 50){
	$error_message[] = t('�� ����� ������� ������� ���.');
}

if (strlen($mail) > 50){
	$error_message[] = t('�� ����� ������� ������� e-mail.');
}

if (strlen($comments) > $config['comment_max_long'] and $config['comment_max_long']){
	$error_message[] = t('�� ����� ������� ������� �����������.');
}

if ($homepage == 'http://'){
	$homepage = '';
}

// Check Flood Protection
if (!cute_get_rights('full') and $config['flood_time'] and flooder($_SERVER['REMOTE_ADDR'], $id)){
	$error_message[] = t('�������� ������ �� �����! ��������� <b>%time</b> ������ ����� ����� ��������� ����������.', array('time' => $config['flood_time']));
}

// Check if IP is banned
$blockip = false;

if ($query = $sql->select(array('table' => 'ipban', 'where' => array("ip = $_SERVER[REMOTE_ADDR]")))){
    $blockip = true;

    foreach ($query as $row){
        $sql->update(array(
        'table'  => 'ipban',
        'where'  => array("ip = $_SERVER[REMOTE_ADDR]"),
        'values' => array('count' => ($row['count'] + 1))
        ));
    }
}

if ($blockip or !cute_get_rights('comments') and $config['only_registered_comment']){
	$error_message[] = t('��������, �� ��� ��������� ����������� �����������.');
}

if ($config['only_registered_comment'] and !$is_logged_in){
	$error_message[] = t('��������, ������ ������������������ ������������ ����� ��������� �����������.');
}

$comments = replace_comment('add', $comments);
$name     = replace_comment('add', preg_replace("/\n/", '', $name));
$mail     = replace_comment('add', preg_replace("/\n/", '', $mail));
$homepage = replace_comment('add', preg_replace("/\n/", '', $homepage));

if (!$is_logged_in){
	foreach ($users as $row){
	    if ($name and (strtolower($row['username']) == strtolower($name) or strtolower($row['name']) == strtolower($name)) or $mail and strtolower($row['mail']) == strtolower($mail)){
	    	$error_message[] = t('�� ����������� ������ ������������������� ������������, �� �� ����� � �������.');
	    }
	}

	if (!$name){
	    $error_message[] = t('������� ���� ���.');
	}

    if ($config['need_mail'] and !$mail){
    	$error_message[] = t('������� e-mail.');
	}

	if ($mail and !preg_match('/^[\.A-z0-9_\-]+[@][\.A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/', $mail)){
		$error_message[] = t('��������, ���� e-mail ������������.');
 	}

	if ($homepage and !preg_match('/^(http|https|ftp)+\:\/\/([\.A-z0-9_\-]+)\.([A-z]{1,4})$/', $homepage)){
		$error_message[] = t('��������, ���� ����� ������������.');
 	}
}

if (!$comments){
	$error_message[] = t('��������� ���� "�����������".');
}

if (reset($error_message)){
    foreach ($error_message as $k => $v){
        $error_message[$k] = $v;
    }

    $allow_add_comment = false;
    header('HTTP/1.0 500 Internal Server Error');
    echo join('<br />', $error_message);
}

if (!$allow_add_comment){
	return;
}

$time = (time() + $config['date_adjust'] * 60);

// Add the Comment
if ($parent){
	$reply_row = $sql->select(array('table' => 'comments', 'where' => array("id = $parent")));
	$reply_row = $reply_row[0];
}

$sql->insert(array(
'table'  => 'comments',
'values' => array(
            'post_id'  => $id,
            'user_id'  => ($is_logged_in ? $member['id'] : 0),
            'date'     => $time,
            'author'   => ($is_logged_in ? $member['username'] : $name),
            'mail'     => $mail,
            'homepage' => $homepage,
            'parent'   => $parent,
            'level'    => ($parent ? ($reply_row['level'] + 1) : 0),
            'ip'       => $_SERVER['REMOTE_ADDR'],
            'comment'  => $comments,
            )
));

$sql->update(array(
'table'  => 'news',
'where'  => array("id = $id"),
'values' => array('comments' => sizeof($sql->select(array('table' => 'comments', 'where' => array("post_id = $id")))))
));

if ($config['flood_time']){
    $sql->insert(array(
    'table'  => 'flood',
    'values' => array(
                'post_id' => $id,
                'date'    => $time,
                'ip'      => $_SERVER['REMOTE_ADDR']
                )
    ));
}

if ($rememberme == 'on'){
	$now = (time() + 3600 * 24 * 365);
	cute_setcookie('commentname', urlencode($name), $now, '/');
	cute_setcookie('commentmail', $mail, $now, '/');
	cute_setcookie('commenthomepage', $homepage, $now, '/');
} else {
	$now = (time() - 3600 * 24 * 365);
	cute_setcookie('commentname', '', $now, '/');
    cute_setcookie('commentmail', '', $now, '/');
    cute_setcookie('commenthomepage', '', $now, '/');
}

if ($is_logged_in){
	$name     = $member['name'];
	$mail     = $member['mail'];
	$homepage = $member['homepage'];
}

if ($parent){
	if ($users[$reply_row['author']]){
		$reply_row['author'] = $users[$reply_row['author']]['name'];
		$reply_row['mail']   = $users[$reply_row['author']]['mail'];
	}

	if ($reply_row['mail'] and $reply_row['mail'] != 'none' and $reply_row['author'] != $name and $reply_row['mail'] != $config['admin_mail']){
	    foreach ($sql->select(array('table' => 'news', 'where' => array("id = $id"))) as $row){
            ob_start();
            include mails_directory.'/reply.tpl';
            $tpl['body'] = ob_get_clean();

            preg_match('/Subject:(.*)/i', $tpl['body'], $tpl['subject']);
            preg_match('/Attachment:(.*)/i', $tpl['body'], $tpl['attachment']);

            $tpl['body']       = preg_replace('/Subject:(.*)/i', '', $tpl['body']);
            $tpl['body']       = preg_replace('/Attachment:(.*)/i', '', $tpl['body']);
            $tpl['body']       = trim($tpl['body']);
            $tpl['subject']    = trim($tpl['subject'][1]);
            $tpl['attachment'] = trim($tpl['attachment'][1]);

	    	cute_mail($reply_row['mail'], $tpl['subject'], $tpl['body'], $tpl['attachment']);
	    }
	}
}

if ($config['admin_mail'] and $config['admin_mail'] != $reply_row['mail'] and $config['admin_mail'] != $mail and $config['send_mail_upon_posting']){
	$comid = $sql->last_insert_id('comments', '', 'id');

    foreach ($sql->select(array('table' => 'news', 'where' => array("id = $id"))) as $row){
        ob_start();
        include mails_directory.'/new_comment.tpl';
        $tpl['body'] = ob_get_clean();

        preg_match('/Subject:(.*)/i', $tpl['body'], $tpl['subject']);
        preg_match('/Attachment:(.*)/i', $tpl['body'], $tpl['attachment']);

        $tpl['body']       = preg_replace('/Subject:(.*)/i', '', $tpl['body']);
        $tpl['body']       = preg_replace('/Attachment:(.*)/i', '', $tpl['body']);
        $tpl['body']       = str_replace("\r\n", "\n", trim($tpl['body']));
        $tpl['subject']    = trim($tpl['subject'][1]);
        $tpl['attachment'] = trim($tpl['attachment'][1]);

        cute_mail($config['admin_mail'], $tpl['subject'], $tpl['body'], $tpl['attachment']);
    }
}

$tpl['template'] = $template;
$post['id'] = $id;
$commid = $sql->last_insert_id('comments', '', 'id');
include dirname(__FILE__).'/show.comments.php';
?>