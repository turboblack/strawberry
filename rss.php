<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

// ������� �����
add_filter('allow-comment-form', 'comment_form');

function comment_form(){return false;}

// ��������� ������ ������ ����� ��� ����� ���������� $template
add_filter('unset-template', 'unset_template');

function unset_template($files){

	$files[] = basename($_SERVER['PHP_SELF']);

return $files;
}

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="'.$config['charset'].'" ?>';
?>

<rss version="2.0"
xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:wfw="http://wellformedweb.org/CommentAPI/"
xmlns:dc="http://purl.org/dc/elements/1.1/"
>
<channel>

<? if (!$id){ ?>
<title><?=$config['home_title']; ?></title>
<link><?=$config['http_home_url']; ?></link>
<description><?=$config['home_title']; ?></description>
<? } ?>
<language>ru</language>
<generator><?=$config['version_name'].' '.$config['version_id']; ?></generator>

<?
$template = 'RSS';
$number = ($number ? $number : 12);
$config['cnumber'] = 0;
include root_directory.'/show_news.php';
?>

</channel>
</rss>