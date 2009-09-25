<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Bookmarks
Plugin URI:     http://cutenews.ru
Description: 	��������� ������� � ��������. ����������� <code>$bookmark = true;</code> ����� �������� show_news.php.
Version: 		2.0
Application: 	Strawberry
Author: 		˸�� zloy � ��������
Author URI:     http://lexa.cutenews.ru
*/

add_action('head', 'bookmark');

function bookmark(){
global $xfields;

    // ���������� "����������" ��������� ��������
    // ����� ������� ��� � � bookmark_check(),
    // �� �������� ������� ���� ������
    // ���� ��� ����� �������� ��� ������� �����
    // � ��� ���������: ���� ���-�� ������ �� ������ -
    // �� ������������
    if (!is_object($xfields)){
    	$xfields = new XfieldsData();
    }

    // ���� � ���� ���-�� ��������� bookmark=���-��,
    // �� �������� ����� ��� �����
    if ($_GET['bookmark']){
    	$_GET['bookmark'] = '';
    }
}

// ��������� � "�����������"
add_filter('constructor-variables', 'bookmark_constructor');

function bookmark_constructor($variables){

	$variables['bookmark'] = array('bool', makeDropDown(array(t('���'), t('��')), 'bookmark'));

return $variables;
}

// ��������� ����� ������
add_filter('news-where', 'bookmark_check');

function bookmark_check($where){
global $bookmark, $sql, $xfields;

    if ($bookmark){
	    foreach ($xfields->data as $k => $v){
	        if ($v['bookmark'] == 'on'){
	        	$found   = true;
	            $where[] = "id = $k";
	            $where[] = 'or';
	        }
	    }

	    if ($found){
	    	$where[sizeof($where) - 1] = 'and';
	    } else {	    	$where[] = 'id = -1';
	    	$where[] = 'and';	    }
	}

return $where;
}

// ��� �������� ���������� ���
// ��������� ������� � �������
// ��������� show_news.php
add_filter('unset', 'bookmark_unset');

function bookmark_unset($var){

    // ��� ���������� ��� ����� ������� ($),
    // ��� ������ ������!
	$var[] = 'bookmark';

return $var;
}

// ��������� ����� � ���������� � �������������� ������
add_action('new-advanced-options', 'bookmark_AddEdit', 3);
add_action('edit-advanced-options', 'bookmark_AddEdit', 3);

function bookmark_AddEdit(){
global $id;

    $xfields = new XfieldsData();

return '<fieldset id="bookmark"><legend>'.t('��������').'</legend><label for="bookmark"><input type="checkbox" id="bookmark" name="bookmark" value="on"'.($xfields->fetch($id, 'bookmark') == 'on' ? ' checked="checked"' : '').'>&nbsp;'.t('�������� � ��������').'</label></fieldset>';
}

// ���������� ���������
add_action('new-save-entry', 'add2bookmark');
add_action('edit-save-entry', 'add2bookmark');

function add2bookmark(){
global $id;

    // ��������� ���������
	$xfields = new XfieldsData();

	if ($_POST['bookmark']){ // ���� $_POST['bookmark'] �� ������ - ����������
		$xfields->set($_POST['bookmark'], $id, 'bookmark');
	} else { // ���� ������, �� ������� �����
		$xfields->deletefield($id, 'bookmark');
	}

	$xfields->save();
}

// ��� ����� ������ �� ��������, � �������� � ��������� ����
?>