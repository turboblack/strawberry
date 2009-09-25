<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	TrackBack
Plugin URI:     http://cutenews.ru
Description: 	Если вы не знаете, что это такое используйте <a href="http://www.yandex.ru/yandsearch?rpt=rad&text=TrackBack">Яндекс</a>.
Version: 		1.0
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_action('new-advanced-options', 'trackback_AddEdit');
add_action('edit-advanced-options', 'trackback_AddEdit');

function trackback_AddEdit(){
global $id;

    $xfields = new XfieldsData();
    $return  = '<fieldset id="trackbacks"><legend>'.t('TrackBacks').'</legend><textarea name="ping" title="'.t('Одна строка - один УРЛ').'">'.$xfields->fetch($id, 'ping').'</textarea></fieldset>'.($xfields->fetch($id, 'pinged') ? '<fieldset><legend>'.t('Отпинговано').'</legend><textarea disabled>'.replace_news('admin', $xfields->fetch($id, 'pinged')).'</textarea></fieldset>' : '');

return $return;
}

add_action('new-save-entry', 'trackback_send');
add_action('edit-save-entry', 'trackback_send');

function trackback_send(){
global $id, $added_time, $member_db, $title, $category, $url, $short_story, $ping, $PHP_SELF, $config;

	include config_file;

	$sendfrom = parse_url($config['http_script_dir']);

    foreach (explode("\r\n", $ping) as $sendto){
		trackback_request($sendfrom['host'], $sendto, 'blog_name='.$config['home_title'].'&url='.cute_get_link(array('id' => $id, 'date' => $added_time, 'author' => $member['username'], 'title' => $title, 'category' => $category, 'url' => $url)).'&title='.$title.'&excerpt='.replace_news('show', $short_story).'&charset='.$config['charset']);
	}

	$xfields = new XfieldsData();
	$pinged  = $xfields->fetch($id, 'pinged');
	$xfields->set(replace_news('add', ($ping ? $pinged."\r\n".$ping : $pinged)), $id, 'pinged');
	$xfields->deletefield($id, 'ping');
	$xfields->save();
}

add_filter('options', 'trackback_AddToOptions');

function trackback_AddToOptions($options) {

	include xfields_file;

	if ($array){
	    foreach ($array as $arr){
	        if (count($arr['trackback'])){
	            $count .= count($arr['trackback']);
	        }
	    }
	}

	$options[] = array(
				 'name'     => t('TrackBacks (%count)', array('count' => ($count ? $count : 0))),
				 'url'      => 'plugin=trackback',
				 'category' => 'tools'
				 );

return $options;
}

add_action('plugins','trackback_CheckAdminOptions');

function trackback_CheckAdminOptions(){

	if ($_GET['plugin'] == 'trackback'){
		trackback_AdminOptions();
	}
}

function trackback_AdminOptions(){
global $sql, $PHP_SELF;

	$xfields = new XFieldsData();

	if ($_POST['select_trackbacks']){
		foreach ($_POST['select_trackbacks'] as $time => $id){
			if ($_POST['add']){
				$trackback = $xfields->fetch($id, 'trackback');
				$trackback = $trackback[$time];

	            $sql->insert(array(
	            'table'  => 'comments',
	            'values' => array(
	                        'post_id'  => $id,
	                        'date'     => $time,
	                        'author'   => $trackback['blog_name'],
	                        'homepage' => $trackback['url'],
	                        'ip'       => $trackback['host'],
	                        'comment'  => ($trackback['title'] ? '[b]'.$trackback['title'].'[/b]<br />' : '').$trackback['excerpt']
	                        )
	            ));

	            $sql->update(array(
	            'table'  => 'news',
	            'where'  => array("id = $id"),
	            'values' => array('comments' => count($sql->select(array('table' => 'comments', 'where' => array("post_id = $id")))))
	            ));
	        }

	        $xfields->deletevalue($id, 'trackback', $time);
	        $xfields->save();
		}
?>

<script type="text/javascript">self.location.href="<?=$_SERVER['REQUEST_URI']; ?>";</script>

<?
	}

	echoheader('options', t('TrackBacks'));
?>

<form name="trackbacks" action="<?=$PHP_SELF; ?>?plugin=trackback" method="post">

<?
	include root_directory.'/data/xfields-data.php';
    foreach ((array)$array as $k => $v){
        if ($v['trackback']){
            foreach ($v['trackback'] as $time => $info){
?>

<h3><a href="<?=$info['url']; ?>" title="<?=$info['host']; ?>"><?=$info['blog_name']; ?></a></h3>
<div align="justify">
<small><?=langdate('d M Y H:i', $time); ?> /</small> <b><?=$info['title']; ?></b>
<br />
<?=$info['excerpt']; ?>
<input name="select_trackbacks[<?=$time; ?>]" type="checkbox" value="<?=$k; ?>">
</div>

<?
            }
        }
    }

    if ($info){
?>

<p>
<input type="submit" value="  <?=t('Опубликовать выбранные'); ?>  " name="add">
<input type="submit" value="  <?=t('Удалить выбранные'); ?>  " name="delete">
</p>
</form>

<?
	} else {
?>

<p><?=t('Посланных трэкбэков не обнаружено.'); ?></p>

<?
	}

	echofooter();
}

#-------------------------------------------------------------------------------

function trackback_request($site, $location, $send, $user_agent = ''){

	list($site, $port) = explode(':', $site);

	$fp = fsockopen($site, (is_numeric($port) ? $port : 80));
	$fo = "POST $location HTTP/1.0\r\n".
	      "Host: $site\r\n".
	      ($user_agent ? "User-Agent: $user_agent\r\n" : '').
	      "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n".
	      "Content-Length: ".strlen($send)."\r\n\r\n".
	      $send;
	fputs($fp, $fo);
}
?>