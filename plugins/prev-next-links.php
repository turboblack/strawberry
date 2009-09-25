<?php
/*
Plugin Name:	Prev-next-links
Plugin URI:     http://english.cutenews.ru/forum
Description:	Ññûëêè íà ïğåäûäóùèé è ñëåäóşùèé ïîñò â ïîëíîé íîâîñòè.
Version:		1.1
Application: 	Strawberry
Author:			FI-DD
Author URI:		http://english.cutenews.ru/forum/profile.php?mode=viewprofile&u=2
*/

add_action('head', 'prev_next');

function prev_next(){
global $sql, $cache, $post, $prev_next_links;

	if ($post){
	    if (!$prev_next_links = $cache->unserialize('prev-next-links', $post['id'])){
	        $prev_next_links['prev'] = $sql->select(array(
	        						   'table'   => 'news',
	        						   'where'   => array('id < '.$post['id']),
	        						   'orderby' => array('date', 'DESC'),
	        						   'limit'   => array(0, 1)
	        						   ));

	        $prev_next_links['next'] = $sql->select(array(
	                        		   'table' => 'news',
	                        		   'where' => array('id > '.$post['id']),
	                        		   'orderby' => array('date', 'ASC'),
	                        		   'limit'   => array(0, 1)
	                        		   ));

	        $prev_next_links = $cache->serialize($prev_next_links);
	    }
	} else {
		$prev_next_links = array();
	}

return $tpl;
}

add_filter('news-show-generic', 'prev_next_generic');

function prev_next_generic($tpl){
global $prev_next_links;

	if ($prev_next_links){
	    $tpl['prev-next']['prev']['link']  = cute_get_link($prev_next_links['prev'][0]);
	    $tpl['prev-next']['prev']['title'] = $prev_next_links['prev'][0]['title'];
	    $tpl['prev-next']['next']['link']  = cute_get_link($prev_next_links['next'][0]);
	    $tpl['prev-next']['next']['title'] = $prev_next_links['next'][0]['title'];
	}

return $tpl;
}

add_filter('template-full', 'template_prev_next');

function template_prev_next($template){

	$template['prev-next'] = t('Îòîáğàæàåò ññûëêè íà ïğåäûäóùóş è ñëåäóşùóş íîâîñòè: $tpl[\'post\'][\'prev-next\'][\'prev\'][\'link\'] è $tpl[\'post\'][\'prev-next\'][\'next\'][\'link\']. À òàêæå çàãîëîâêè: $tpl[\'post\'][\'prev-next\'][\'prev\'][\'title\'] è $tpl[\'post\'][\'prev-next\'][\'next\'][\'title\']');

return $template;
}

?>