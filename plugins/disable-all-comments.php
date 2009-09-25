<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Disable All Comments
Plugin URI:		http://cutenews.ru/cat/plugins/
Description:	Полное отключение комментариев.
Version:		1.0
Application: 	Strawberry
Author: 		David Carrington
Author URI: 	http://www.brandedthoughts.co.uk
*/

add_filter('allow-comment-form', 'disable_all_comments');

function disable_all_comments($allow){
return false;
}
?>
