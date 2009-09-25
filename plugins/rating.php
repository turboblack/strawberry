<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	News rating
Plugin URI: 	http://cutenews.ru/
Description:	������� ��������.
Version: 		0.1
Application: 	Strawberry
Author: 		˸�� zloy � ��������
Author URI:     http://lexa.cutenews.ru
*/

add_action('head', 'rating_update');

function rating_update(){
global $sql;

	if ($_POST['rating']){
	    $sql->altertable(array(
	    'table'  => 'news',
	    'action' => 'insert',
	    'name'   => 'rating',
	    'values' => array('type' => 'int', 'default' => 0)
	    ));

	    $sql->altertable(array(
	    'table'  => 'news',
	    'action' => 'insert',
	    'name'   => 'votes',
	    'values' => array('type' => 'int', 'default', 'default' => 0)
	    ));

        foreach ($_POST['rating'] as $pid => $rid){
        	if (!$_COOKIE['cnpostrating'.$pid]){
        		cute_setcookie('cnpostrating'.$pid, 'voted', (time() + 3600 * 24), '/');

	            $row = reset($sql->select(array('table' => 'news', 'where' => array("id = $pid"))));
	            $row['rating'] += $rid;
	            $row['votes']  += 1;

	            $sql->update(array(
	            'table'  => 'news',
	            'where'  => array("id = $pid"),
	            'values' => array('rating' => $row['rating'], 'votes' => $row['votes'])
	            ));
	        }
        }

        header('Location: '.$_SERVER['REQUEST_URI']);
        exit;
	}
}

add_filter('template-active', 'rating_macros');
add_filter('template-full', 'rating_macros');

function rating_macros($output){

    $output['rating']   = t('������� ����� ������� �������');
    $output['votes']    = t('������� ���������� ������� �� �������');
	$output['rating()'] = t('������� "����������" ������� (���������� ������� ���������� �� ����� �������) ������� ��� ����� �����������. ������ ���������� ����� ������� check ��� �������� ������� ���������� � ���� �� ��� ���������, �� ������� ������������� �����');

return $output;
}

#-------------------------------------------------------------------------------

function rating($what = ''){
global $tpl;

	if ($what == 'check'){
		return ($_COOKIE['cnpostrating'.$tpl['post']['id']] ? true : false);
	} else {
		return @round(($tpl['post']['rating'] / $tpl['post']['votes']), 0);
	}
}
?>