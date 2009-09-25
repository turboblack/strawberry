<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

// выносим ссылки в скобки
add_filter('news-entry-content', 'link_to_text');

function link_to_text($output){

	$output = preg_replace('/<a href=(\\\"|"|\'{0,1})(.*?)(\\1)(.*?)>(.*?)<\/a>/i', '\\5 ( <span class="link">\\2</span> )', $output);

return $output;
}

// запрещаем менять шаблон кроме как через переменную $template
add_filter('unset-template', 'unset_template');

function unset_template($files){

	$files[] = basename($_SERVER['PHP_SELF']);

return $files;
}

$template = 'Print';
$number = 1;
include $cutepath.'/show_news.php';
?>