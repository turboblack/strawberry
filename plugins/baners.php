<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Baners
Plugin URI: 	http://cutenews.ru/
Description:	Реклама между постами.
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('options', 'baners_AddToOptions');

function baners_AddToOptions($options){

	$options[] = array(
			     'name'     => t('Банеры'),
			     'url'      => 'plugin=baners',
			     'category' => 'tools'
			     );

return $options;
}

add_action('plugins', 'baners_CheckAdminOptions');

function baners_CheckAdminOptions(){

	if ($_GET['plugin'] == 'baners'){
		baners_AdminOptions();
	}
}

function baners_AdminOptions(){
global $categories, $PHP_SELF;

	$baners = new PluginSettings('Baners');

	$handle = opendir(templates_directory);
	while ($file = readdir($handle)){
	    if ($file != '.' and $file != '..' and is_dir(templates_directory.'/'.$file)){
	        $templates[$file] = $file;
	    }
	}

	if ($_POST){
		$_POST['posts'] = chicken_dick($_POST['posts'], ',');

		if (!$_POST['template'][0]){
			unset($_POST['template']);
		}

		if (!$_POST['category'][0]){
			unset($_POST['category']);
		}

		if ($_POST['baner']){
	    	$baners->settings[$_POST['baner']] = $_POST;
	    } elseif (!count($baners->settings)) {
	    	$baners->settings[1] = $_POST;
	    } else {
	    	$baners->settings[] = $_POST;
	    }

	    $baners->save();
	    header('Location: '.$PHP_SELF.'?plugin=baners'.($_POST['baner'] ? '&baner='.$_POST['baner'] : ''));
	}

	if ($_GET['remove']){
		$baners->settings[$_GET['remove']] = null;
		$baners->save();

		header('Location: '.$PHP_SELF.'?plugin=baners');
	}

	if ($baners->settings[$_GET['baner']]){
		$name     = $baners->settings[$_GET['baner']]['name'];
		$posts    = $baners->settings[$_GET['baner']]['posts'];
		$category = $baners->settings[$_GET['baner']]['category'];
		$template = $baners->settings[$_GET['baner']]['template'];
		$text     = $baners->settings[$_GET['baner']]['text'];
	}

	echoheader('options', t('Банеры'));
?>

<a href="<?=$PHP_SELF; ?>?plugin=baners"><?=t('Создать новый'); ?></a>
<br />
<? if ($baners->settings){ ?>
<br />
<?=t('Имеющиеся банеры'); ?><br />
<? foreach ($baners->settings as $k => $row){ ?>
<? if ($row){ ?>
<a href="<?=$PHP_SELF; ?>?plugin=baners&baner=<?=$k; ?>"><?=$row['name']; ?></a>
<small>(<a href="<?=$PHP_SELF; ?>?plugin=baners&remove=<?=$k; ?>"><?=t('удалить'); ?></a>)</small><br />
<? } ?>
<? } ?>
<? } ?>
<br />
<form action="<?=$PHP_SELF; ?>?plugin=baners" method="post">
<?=t('Название'); ?><br />
<input name="name" type="text" value="<?=$name; ?>"><br />

<?=t('Каким постом (или постами - через запятую) будет выводиться банер'); ?><br />
<input name="posts" type="text" value="<?=$posts; ?>"><br />

<?=t('Категории в которой будет отображаться банер'); ?><br />
<select name="category[]" size="7" multiple="multiple">
<option value="">...</option>
<?=category_get_tree('&nbsp;', '<option value="{id}"[php]baners_category_selected({id}, '.($category ? join(',', $category) : 0).')[/php]>{prefix}{name}</option>'); ?>
</select><br />

<?=t('Шаблоны для которых будет отображаться банер'); ?><br />
<select name="template[]" size="7" multiple="multiple">
<option value="">...</option>
<? foreach ($templates as $k => $v){ ?>
<option value="<?=$k; ?>"<?=(@in_array($k, $template) ? ' selected' : ''); ?>><?=$v; ?></option>
<? } ?>
</select>
<br />

<?=t('Код банера'); ?><br />
<textarea name="text" style="width: 100%;height: 200px;"><?=$text; ?></textarea>
<br />
<input type="submit" value="<?=($_GET['baner'] ? t('Редактировать') : t('Создать')); ?>">
<input name="baner" type="hidden" value="<?=$_GET['baner']; ?>">
</form>

<?
 	echofooter();
}

add_action('head', 'baners_make_array');

function baners_make_array(){
global $baners_array;

	$baners = new PluginSettings('Baners');
	$baners_array = $baners->settings;
}

add_filter('news-show-generic', 'baner_after_news');

function baner_after_news($g_tpl){
global $baners_array, $tpl;
static $i;

	$i++;

    if ($baners_array){
	    foreach ($baners_array as $row){
	        $row['posts'] = explode(',', $row['posts']);

	        if (in_array($i, $row['posts']) and (!$row['template'] or ($row['template'] and in_array($tpl['template'], $row['template'])))){
	            if ($row['category']){
	                if (in_array(category_get_id($_GET['category']), $row['category'])){
	                    echo $row['text'];
	                }
	            } else {
	                echo $row['text'];
	            }
	        }
	    }
	}

return $g_tpl;
}

#-------------------------------------------------------------------------------

function baners_category_selected($id, $select){

	if (@in_array($id, explode(',', $select))){
		return ' selected';
	}
}
?>