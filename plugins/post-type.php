<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Post Type
Plugin URI: 	http://cutenews.ru/
Description:	Позволяет выбрать тип сообщения: опрос (<code>$type = 'poll';</code>), страница (<code>$type = 'page';</code>)(отличается от обычной новости многоуровневостью), чистый PHP, запароленный пост.
Version: 		0.2
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_action('edit-advanced-options', 'postType_AddEdit');
add_action('new-advanced-options', 'postType_AddEdit');

function postType_AddEdit(){
global $sql, $id, $row;

    $sql->altertable(array(
    'table'  => 'news',
    'action' => 'insert',
    'name'   => 'type',
    'values' => array('type' => 'string')
    ));

    $sql->altertable(array(
    'table'  => 'news',
    'action' => 'insert',
    'name'   => 'parent',
    'values' => array('type' => 'int', 'default' => 0)
    ));

    $sql->altertable(array(
    'table'  => 'news',
    'action' => 'insert',
    'name'   => 'level',
    'values' => array('type' => 'int', 'default' => 0)
    ));

    $sql->altertable(array(
    'table'  => 'news',
    'action' => 'insert',
    'name'   => 'password',
    'values' => array('type' => 'string')
    ));

    $types = array('' => '...', 'poll' => t('Опрос'), 'page' => t('Страница'), 'private' => t('Запароленый'));

    if (cute_get_rights('full')){
    	$types['php'] = t('PHP');
    }

	$buffer  = '<fieldset id="post_type"><legend>'.t('Тип поста').'</legend>';
	$buffer .= makeDropDown($types, 'type"  onChange="this.value == \'page\' ? _getElementById(\'postparent\').style.display = \'\' : _getElementById(\'postparent\').style.display = \'none\'; this.value == \'private\' ? _getElementById(\'postpassword\').style.display = \'\' : _getElementById(\'postpassword\').style.display = \'none\';', $row['type']);
    $buffer .= '<br /><select size="1" id="postparent" name="postparent" style="display: '.($row['type'] != 'page' ? 'none' : '').';" title="'.t('Родитель').'"><option value="0">...</option>';

    if ($row['parent']){
		$buffer .= page_get_tree('-&nbsp;', '<option value="{id}"[php]tmp_page_selected({id}, '.$row['parent'].')[/php]>{prefix}{title}</option>', false);
	} else {
		$buffer .= page_get_tree('-&nbsp;', '<option value="{id}">{prefix}{title}</option>', false);
	}

	$buffer .= '</select>';

    if ($row['type'] == 'private'){
    	$buffer .= '<input id="password" name="postpassword" type="text" value="'.$row['password'].'" style="display: ;" title="'.t('Пароль').'">';
    } else {
    	$buffer .= '<input id="password" name="postpassword" type="text" value="" style="display: none;" title="'.t('Пароль').'">';
    }

	$buffer .= '</fieldset>';

return $buffer;
}

add_action('new-save-entry', 'postType_saveType');
add_action('edit-save-entry', 'postType_saveType');

function postType_saveType(){
global $sql, $id;

	$values = array('type' => $_POST['type'], 'password' => '', 'parent' => '', 'level' => '');

    if ($_POST['type'] == 'page'){
    	$query = end($sql->select(array('table' => 'news', 'where' => array('id = '.$_POST['postparent']))));
    	$values['parent'] = $_POST['postparent'];
    	$values['level']  = ($query['level'] + 1);
	} elseif ($_POST['type'] == 'private'){
		$values['password'] = $_POST['postpassword'];
	}

    $sql->update(array(
    'table'  => 'news',
    'where'  => array("id = $id"),
    'values' => $values
    ));
}

add_filter('news-where', 'postType_where');

function postType_where($where){
global $type;

    if ($type == 'page'){
		$where[] = 'type = page';
		$where[] = 'and';
    } elseif ($type == 'poll'){
		$where[] = 'type = poll';
		$where[] = 'and';
    } elseif ($type == 'news'){
		$where[] = 'type != page';
		$where[] = 'and';
		$where[] = 'type != poll';
		$where[] = 'and';
    } else {
		$where[] = 'type != page';
		$where[] = 'and';
	}

return $where;
}

add_filter('constructor-variables', 'postType_constructor');

function postType_constructor($variables){

	$variables['type'] = array('string', makeDropDown(array(t('новости и опросы'), 'news' => t('новости'), 'poll' => t('опросы'), 'page' => t('страницы')), 'type'));

return $variables;
}

add_action('head', 'postType_define');

function postType_define(){
global $cache, $type, $_pages, $sql;

    if (!$_pages = $cache->unserialize('_pages')){
	    if ($query = $sql->select(array('table' => 'news', 'where' => array('type = page')))){
	        foreach ($query as $row){
	            $_pages[$row['id']] = $row;
	        }
	    }

	    $_pages = $cache->serialize($_pages);
	}

	unset($type, $_GET['type']);
}

add_filter('unset', 'postType_typeUnset');

function postType_typeUnset($unset){

	$unset[] = 'type';

return $unset;
}

add_filter('cute-get-link', 'postType_put_link');

function postType_put_link($output){

    if ($output['arr']['parent']){
    	$output['link'] = str_replace('{title}', page_get_link($output['arr']['id']), $output['link']);
    }

return $output;
}

add_filter('htaccess-rules-replace', 'postType_rules_replace');

function postType_rules_replace($output){
global $config, $_pages;

    if ($config['mod_rewrite'] and $_pages){
        foreach ($_pages as $row){
        	if ($row['parent']){
            	$page[] = page_get_link($row['id']);
            }
        }

        if ($page){
            $output = str_replace('{title}', '('.join('|', $page).'|[_0-9a-z-]+)', $output);
        }
    }

return $output;
}

add_action('head', 'postType_idClean');

function postType_idClean(){
global $id;

	if (!is_numeric($id) and chicken_dick(strstr($id, '/'))){
		$id = $_GET['id'] = end($id = explode('/', chicken_dick($id)));
	}
}

add_action('head', 'postType_updatePoll');

function postType_updatePoll(){
global $sql;

    if ($_POST['poll']){
        foreach ($_POST['poll'] as $pid => $vid){
        	if (!$_COOKIE['cnpostpoll'.$pid]){
        		cute_setcookie('cnpostpoll'.$pid, 'voted', (time() + 3600 * 24), '/');

	            $row = reset($sql->select(array('table' => 'story', 'where' => array("post_id = $pid"))));

	            foreach (explode('{nl}', $row['short']) as $k => $v){
	                $v_arr    = explode('{', $v);
	                $vote[$k] = (int)$v_arr[1];
	                $poll[$k] = $v_arr[0];
	            }

	            foreach ($poll as $k => $v){
	                if ($k == $vid){
	                    $vote[$k] = ($vote[$k] + 1);
	                }

	                $story[] = $poll[$k].'{'.$vote[$k].'}';
	            }

	            $sql->update(array(
	            'table'  => 'story',
	            'where'  => array("post_id = $pid"),
	            'values' => array('short' => join('{nl}', $story))
	            ));
	        }
        }

        header('Location: '.$_SERVER['REQUEST_URI']);
        exit;
    }
}

add_filter('news-show-generic', 'postType_makePoll');

function postType_makePoll($tpl){

	if ($tpl['_']['type'] == 'poll'){
		foreach (explode('{nl}', $tpl['_']['short']) as $k => $v){
            $v_arr     = explode('{', $v);
            $vote[$k]  = (int)$v_arr[1];
            $poll[$k]  = $v_arr[0];
			$votes    += $vote[$k];
	    }

		foreach ($poll as $k => $v){
			$short .= '<label for="poll['.$tpl['id'].']['.$k.']"><input name="poll['.$tpl['id'].']" type="radio" id="poll['.$tpl['id'].']['.$k.']" value="'.$k.'">'.$poll[$k].'</label><br />';
			$full  .= '<div><b>'.$poll[$k].'</b></div><div style="width: '.@round($vote[$k] / ($votes / 100)).'%;" class="cute_poll">'.@round($vote[$k] / ($votes / 100)).'%</div>';
		}

        $tpl['short-story'] = ($_COOKIE['cnpostpoll'.$tpl['id']] ? $full : '<form name="cnpostpoll" method="post">'.$short.'<input type="submit" value="  ok  "></form>');
		$tpl['full-story']  = $full;
	}

return $tpl;
}

add_filter('news-show-generic', 'postType_parsePHP');

function postType_parsePHP($tpl){

	if ($tpl['_']['type'] == 'php'){
	    $evalshort = replace_news('admin', str_replace('<br />', '', $tpl['_']['short']));
	    $evalfull  = replace_news('admin', str_replace('<br />', '', $tpl['_']['full']));

        if ($evalshort){
	        ob_start();
	        eval($evalshort);
	        $tpl['short-story'] = ob_get_clean();
	    }

        if ($evalfull){
	        ob_start();
	        eval($evalfull);
	        $tpl['full-story'] = ob_get_clean();
	    }
	}

return $tpl;
}

add_action('head', 'postType_updatePrivate');

function postType_updatePrivate(){

    if ($_POST['cnpostpassword']){
    	cute_setcookie('cnpostpassword'.$_POST['passtopost'], $_POST['cnpostpassword'], (time() + 3600 * 24 * 365), '/');
        header('Location: '.$_SERVER['REQUEST_URI']);
        exit;
    }
}

add_filter('news-show-generic', 'postType_makePrivate');

function postType_makePrivate($tpl){

	if ($tpl['_']['type'] == 'private' and $_COOKIE['cnpostpassword'.$tpl['id']] != $tpl['_']['password']){
        $tpl['short-story'] = '<form method="post"><input name="cnpostpassword" type="password" value=""><input name="passtopost" type="hidden" value="'.$tpl['id'].'"><input type="submit" value="  ok  "></form>';
        $tpl['full-story']  =  ($tpl['full-story'] ? $tpl['short-story'] : '');
	}

return $tpl;
}

#-------------------------------------------------------------------------------

function page_get_link($id, $link = ''){
global $_pages;

    if ($_pages[$id]['url']){
        $link = $_pages[$id]['url'].($link ? '/'.$link : '');
    }

    if ($_pages[$id]['parent']){
        $link = page_get_link($_pages[$id]['parent'], $link);
    }

return chicken_dick($link);
}

function page_get_title($id, $separator = ' &raquo; ', $title = ''){
global $_pages;

    if ($_pages[$id]['title']){
        $title = $_pages[$id]['title'].($title ? $separator.$title : '');
    }

    if ($_pages[$id]['parent']){
        $title = page_get_title($_pages[$id]['parent'], $separator, $title);
    }

return chicken_dick($title);
}

function page_get_tree($prefix = '', $tpl = '{name}', $no_prefix = true, $id = 0, $level = 0, $johnny_left_teat = ''){
global $sql;

    $level++;

	foreach ($sql->select(array('table' => 'news', 'where' => array("parent = $id", 'and', 'type = page'), 'orderby' => array('id', 'DESC'))) as $row){
		$pref = ($prefix ? ($no_prefix ? preg_replace('/('.preg_quote($prefix, '/').'{1})$/i', '', str_repeat($prefix, $level)) : str_repeat($prefix, $level)) : '');
		$find = array('/{id}/i', '/{title}/i', '/{parent}/i', '/{url}/i', '/{prefix}/i', '/\[php\](.*?)\[\/php\]/ie');
		$repl = array($row['id'], $row['title'], $row['parent'], $row['url'], $pref, '\\1');
		$johnny_left_teat .= $pref.preg_replace($find, $repl, $tpl);
		$johnny_left_teat  = page_get_tree($prefix, $tpl, $no_prefix, $row['id'], $level, $johnny_left_teat);
	}

return $johnny_left_teat;
}

function tmp_page_selected($id, $parent){

	if ($id == $parent){
		return ' selected';
	}
}
?>