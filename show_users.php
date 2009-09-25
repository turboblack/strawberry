<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

if ($money){
	if (($money == 'give' or $money == 'take') and is_array($users[$user_id]) and $is_logged_in){
	    if ($_POST['motivation']){
	        $sql->insert(array(
	        'table'  => 'money',
	        'values' => array(
	                    'to'         => $user_id,
	                    'from'       => $member['username'],
	                    'motivation' => replace_comment('add', $_POST['motivation']),
	                    'money'      => ($money == 'give' ? '+' : '-'),
	                    'date'       => time
	                    )
	        ));
	    } else {
	        include templates_directory.'/Users/motivation.tpl';
	    }
	} elseif ($money == 'show'){
	    $query = $sql->select(array('table' => 'money', 'where' => array('to = '.$user_id)));

	    foreach ($query as $row){
	    	$tpl['money']['to']         = $users[$row['to']]['name'];
	    	$tpl['money']['from']       = $users[$row['from']]['name'];
	    	$tpl['money']['action']     = $row['money'];
	    	$tpl['money']['date']       = langdate('d M Y H:i:s', $row['date']);
	    	$tpl['money']['motivation'] = replace_comment('show', $row['motivation']);
	    	$tpl['money']['_']          = $row;
	    	include templates_directory.'/Users/operations.tpl';
	  	}
	}

	exit('<a href="#" onClick="window.close();">'.t('Закрыть окно').'</a>');
}

if ($user){
	$money_plus = $sql->select(array(
		     	  'table' => 'money',
		     	  'where' => array(
		     	  			 'to = '.$user,
		     	  			 'and',
		     	  			 'money = +'
		     	  			 )
		     	  ));

	$money_minus = $sql->select(array(
		     	   'table' => 'money',
		     	   'where' => array(
		     	    		  'to = '.$user,
		     	  			  'and',
		     	  			  'money = -'
		     	  			  )
		     	   ));

	ob_start();
	$number = '5';
	$template = 'Headlines';
	include root_directory.'/show_news.php';
	$tpl['user']['headlines'] = ob_get_clean();
}

$user = $_GET['user'];
$self = $PHP_SELF.(strstr($PHP_SELF, '?') ? '&' : '?');
?>

<script>
function giveMoney(user_id){
	window.open('<?=$self; ?>money=give&user_id=' + user_id, '_Motivation', 'height=420,resizable=yes,scrollbars=yes,width=410');
return false;
}

function takeMoney(user_id){
	window.open('<?=$self; ?>money=take&user_id=' + user_id, '_Motivation', 'height=420,resizable=yes,scrollbars=yes,width=410');
return false;
}

function showMoney(user_id){
	window.open('<?=$self; ?>money=show&user_id=' + user_id, '_Motivation', 'height=420,resizable=yes,scrollbars=yes,width=410');
return false;
}
</script>

<table>

<?
foreach ($users as $row){
	$tpl['template'] = templates_directory.'/Users/';

	if ($row['id'] == $user or $row['username'] == $user or $row['name'] == $user){
        $allow_full_story = true;
	} else {
		$allow_full_story = false;
	}

	if ($user and !$allow_full_story){
		continue;
	}

	if (!$output = $cache->get($row['id'], '', ($allow_full_story ? 'show' : 'list'))){
		if (!$rufus_file){
			$rufus_file = parse_ini_file(rufus_file, true);
		}

	    foreach ($rufus_file as $type_k => $type_v){
	        if (is_array($type_v)){
	            foreach ($type_v as $k => $v){
	                if ($type_k == 'home'){
	                    $tpl['user']['link'][$k] = cute_get_link($row, $k);
	                }

	                $tpl['user']['link'][$type_k.'/'.$k] = cute_get_link($row, $k, $type_k);
	            }
	        }
	    }

	    $tpl['user']['homepage']       = ($row['homepage'] ? '<a href="'.$row['homepage'].'">'.$row['homepage'].'</a>' : '');
	    $tpl['user']['avatar']         = $config['path_userpic_upload'].'/'.$row['author'].'.'.$row['avatar'];
	    $tpl['user']['icq']            = ($row['icq'] ? '<img src="'.$config['http_script_dir'].'/skins/images/icq.gif" align="absmiddle" alt="" border="0">'.$row['icq'] : '');
	    $tpl['user']['location']       = $row['location'];
	    $tpl['user']['about']          = run_filters('news-entry-content', $row['about']);
	    $tpl['user']['lj-username']    = ($row['lj_username'] ? '<a href="http://'.$row['lj_username'].'.livejournal.com/profile"><img src="'.$config['http_script_dir'].'/skins/images/user.gif" alt="[info]" align="absmiddle" border="0"></a><a href="http://'.$row['lj_username'].'.livejournal.com">'.$row['lj_username'].'</a>' : '');
	    $tpl['user']['name']           = $row['name'];
	    $tpl['user']['username']       = $row['username'];
	    $tpl['user']['usergroup']      = $usergroups[$row['usergroup']]['name'];
	    $tpl['user']['id']             = $row['id'];
	    $tpl['user']['date']           = langdate($config['timestamp_active'], $row['date']);
	    $tpl['user']['mail']           = (!$row['hide_mail'] ? $row['mail'] : '');
	    $tpl['user']['last_visit']     = ($row['last_visit'] ? langdate($config['timestamp_active'], $row['last_visit']) : '');
	    $tpl['user']['about']          = run_filters('news-entry-content', $row['about']);
	    $tpl['user']['alternating']    = cute_that('cn_users_odd', 'cn_users_even');
	    $tpl['user']['publications']   = $row['publications'];
	    $tpl['user']['money']['plus']  = sizeof($money_plus);
	    $tpl['user']['money']['minus'] = sizeof($money_minus);
	    $tpl['user']['_']              = $row;

	    ob_start();
	    include $tpl['template'].'/'.($allow_full_story ? 'show' : 'list').'.tpl';
	    $output = ob_get_clean();
	    $output = run_filters('news-entry', $output);
	    $output = replace_news('show', $output);
	    $output = $cache->put($output);
	}

	echo $output;
}

echo '</table>';
?>