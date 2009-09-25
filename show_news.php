<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

//----------------------------------
// Восставшие из Зада
//----------------------------------
foreach ($_GET as $k => $v){
	$$k = (!$v ? $$k : @htmlspecialchars($v));
}

foreach ($_POST as $k => $v){
	$$k = (!$v ? $$k : @htmlspecialchars($v));
}

if (is_array($static)){
	foreach ($vars as $k => $v){
		if ($v != 'static' and $v != 'id'){
	    	unset($$v);
	    }
	}

	foreach ($static as $k => $v){
	    $$k = $v;
	}
}

if (!$sort[0] or !strstr($sort[1], 'SC')){
	$sort = array('date', 'DESC');
}

if ($category){
	if (!strstr($category, ',') and !is_numeric($category)){
		$category = category_get_id($category);
	}

	foreach (explode(',', str_replace(' ', '', $category)) as $cat){
	    $category_tmp .= category_get_children($cat).',';
	}

	$category_tmp = chicken_dick($category_tmp, ',');
	$category	  = ($category_tmp ? $category_tmp : $category);
}

if (!$number){
	$number = $sql->table_count('news');
}

if (!$link){
	$link = 'home';
}

if (!$template or strtolower($template) == 'default' or is_file(templates_directory.'/'.$template) or !is_dir(templates_directory.'/'.$template)){
	$template = 'Default';
}

$cache_uniq = md5($cache->touch_this().$_SERVER['REQUEST_URI'].$member['usergroup'].$post['id']);

$allow_comment_form = false;
$allow_full_story   = false;
$allow_active_news  = true;
$allow_comments     = false;

if (!$static and $id){
	$allow_comment_form = true;
	$allow_full_story   = true;
	$allow_active_news  = false;
	$allow_comments     = true;
}

if ($post['id'] and !$page){
    $sql->update(array(
    'table'  => 'news',
    'where'  => array("id = $post[id]"),
    'values' => array('views' => $post['views'] + 1)
    ));
}

if (!$output = $cache->get('show', $cache_uniq)){
	ob_start();
	include includes_directory.'/show.inc.php';
	$output = $cache->put(ob_get_clean());
}

echo $output;

if ($vars = run_filters('unset', $vars)){
	foreach ($vars as $var){
		unset($$var);
	}
}

unset($category_tmp, $parent, $no_prev, $no_next, $prev, $var);
?>

<!-- Powered by Strawberry | http://Strawberry.GoodGirl.ru -->