<?php
/**
 * @package Plugins
 */

/*
Plugin Name: 	Meta tags
Plugin URI:     http://cutenews.ru
Description: 	Выводит мета теги keywords и description.<br />Выводить так:<br />title - <code>&lt;title&gt;&lt;?=cn_meta('title'); ?&gt;&lt;/title&gt;</code><br />keywords - <code>&lt;meta name="keywords" content="&lt;?=cn_meta('keywords'); ?&gt;"&gt;</code><br />description - <code>&lt;meta name="description" content="&lt;?=cn_meta('description'); ?&gt;"&gt;</code>
Version: 		1.0
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_action('new-advanced-options', 'metatag_AddEdit');
add_action('edit-advanced-options', 'metatag_AddEdit');

/**
 * @access private
 */
function metatag_AddEdit(){
global $id;

    $xfields = new XfieldsData();
    $return  = '<fieldset id="meta_title"><legend>Meta title</legend><input name="meta_title" type="text" value="'.$xfields->fetch($id, 'meta_title').'"></fieldset><fieldset id="meta_keywords"><legend>Meta keywords</legend><textarea name="meta_keywords">'.$xfields->fetch($id, 'meta_keywords').'</textarea></fieldset><fieldset id="meta_description"><legend>Meta description</legend><textarea name="meta_description">'.$xfields->fetch($id, 'meta_description').'</textarea></fieldset>';

return $return;
}

add_action('new-save-entry', 'metatag_save');
add_action('edit-save-entry', 'metatag_save');

/**
 * @access private
 */
function metatag_save(){
global $id;

	$xfields = new XfieldsData();
	$xfields->set($_POST['meta_title'], $id, 'meta_title');
	$xfields->set($_POST['meta_keywords'], $id, 'meta_keywords');
	$xfields->set($_POST['meta_description'], $id, 'meta_description');
	$xfields->save();
}

/**
 * Выводим мета: кейворды или описание. Зависит от того, что указано в $meta: keywords или description.
 *
 * @param string $meta
 * @return string
 */
function cn_meta($meta = 'keywords'){
global $xfields, $id, $cache, $post;
static $uniqid;

	if (!$cn_meta = $cache->get($meta.'-'.str_replace(array('/', '?', '&', '='), '-', chicken_dick($_SERVER['REQUEST_URI'])), $uniqid++)){
	    if ($id){
	        if (!is_object($xfields)){
	            $xfields = new XfieldsData();
	        }

	        $cn_meta = $cache->put($xfields->fetch($post['id'], 'meta_'.$meta));
        }
	}

return $cn_meta;
}
?>