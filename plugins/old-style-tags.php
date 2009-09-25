<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Old Style Tags
Plugin URI: 	http://cutenews.ru/
Description:	Старые теги типа {imagepath}, [if-logged] и [not-logged].
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('news-show-generic', 'old_style_tags');

function old_style_tags($tpl){
global $config, $is_logged_in;

	$tpl['short-story'] = str_replace('{imagepath}', $config['path_image_upload'], $tpl['short-story']);
	$tpl['full-story']  = str_replace('{imagepath}', $config['path_image_upload'], $tpl['full-story']);

	if ($is_logged_in){
		$tpl['short-story'] = str_replace('[if-logged]', '', $tpl['short-story']);
		$tpl['short-story'] = str_replace('[/if-logged]', '', $tpl['short-story']);
		$tpl['full-story']  = str_replace('[if-logged]', '', $tpl['full-story']);
		$tpl['full-story']  = str_replace('[/if-logged]', '', $tpl['full-story']);
		$tpl['short-story'] = preg_replace('/\[not-logged\](.*?)\[\/not-logged\]/is', '', $tpl['short-story']);
		$tpl['full-story']  = preg_replace('/\[not-logged\](.*?)\[\/not-logged\]/is', '', $tpl['full-story']);
	} else {
		$tpl['short-story'] = str_replace('[not-logged]', '', $tpl['short-story']);
		$tpl['short-story'] = str_replace('[/not-logged]', '', $tpl['short-story']);
		$tpl['full-story']  = str_replace('[not-logged]', '', $tpl['full-story']);
		$tpl['full-story']  = str_replace('[/not-logged]', '', $tpl['full-story']);
		$tpl['short-story'] = preg_replace('/\[if-logged\](.*?)\[\/if-logged\]/is', '', $tpl['short-story']);
		$tpl['full-story']  = preg_replace('/\[if-logged\](.*?)\[\/if-logged\]/is', '', $tpl['full-story']);
	}

return $tpl;
}
?>