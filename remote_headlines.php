<?php
/**
 * @package Show
 * @access private
 */

/*
��� ������������ ���� ������.
�������� ���� ���:
<script language="javascript" src="http://example.com/path/to/remote_headlines.php"></script>
����� ������������ ������ ���-�� ��������:
http://example.com/path/to/remote_headlines.php?number=NUMBER_OF_NEWS
����� ������������ � ���������:
http://example.com/path/to/remote_headlines.php?number=NUMBER_OF_NEWS&category=CAT_ID
������ ��� ���� �� ��������� remote_headlines
*/


include_once 'head.php';

add_filter('news-where', 'hidden_news');

function hidden_news($where){

	$where[] = 'hidden = 0';
	$where[] = 'and';

return $where;
}

$template = 'remote_headlines';
$number = ($number ? $number : 7);
include root_directory.'/show_news.php';
?>