<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Wacko formatter
Plugin URI:     http://cutenews.ru
Description: 	Wacko форматирование.
Version: 		1.0
Application: 	Strawberry
*/

add_filter('new-advanced-options', 'wacko_help');
add_filter('edit-advanced-options', 'wacko_help');

function wacko_help($location){
?>

<div class="wacko_help"><a onclick="window.open('plugins/wacko/docs/english/format.html', '_WikiHelp', 'height=420,resizable=yes,scrollbars=yes,width=410');return false;" href="plugins/wacko/docs/english/format.html"><?=t('Помощь по wacko разметки'); ?></a></div>

<?
return $location;
}

add_action('head', 'wacko');

function wacko($output){
global $wacko;

    include plugins_directory.'/wacko/classes/WackoFormatter.php';
    $wacko = new WackoFormatter();
}

add_filter('news-entry-content', 'wacko_formater');
add_filter('news-comment-content', 'wacko_formater');

function wacko_formater($output){
global $wacko;

	$output = $wacko->format($output);
	$output = trim($output);

return $output;
}
?>