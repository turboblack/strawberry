<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Adepto Fastload
Plugin URI: 	http://cutenews.ru/
Description:	Плагин позволяет прикреплять файлы к посту.
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('new-advanced-options', 'adepto_list');
add_filter('edit-advanced-options', 'adepto_list');

function adepto_list($location){
global $id, $config;

    $xfields = new PluginSettings('Adepto_Fastload');

    if (!$xfields->settings['delete_files']){
       $xfields->settings['delete_files'] = '0';
       $xfields->save();
    }

    if (!$xfields->settings['path_upload']){
       $xfields->settings['path_upload'] = $config['http_script_dir'].'/data/attach';
       $xfields->save();
    }

    if (!$xfields->settings['deny_files']){
       $xfields->settings['deny_files'] = '.cgi .pl .shtml .shtm .php .php3 .php4 .php5 .phtml .phtm .phps';
       $xfields->save();
    }

    $attach_directory = cute_parse_url($xfields->settings['path_upload']);
    $attach_directory = $attach_directory['abs'];

	if ($id and is_dir($attach_directory.'/'.$id)){
        echo '<div id="adepto_fastload_p_'.$location.'"><a href="javascript:ShowOrHide(\'adepto_fastload_'.$location.'\', \'adepto_fastload_p_'.$location.'\')">'.t('Прикреплённые файлы').'</a></div>';
		echo '<div id="adepto_fastload_'.$location.'" class="adepto_fastload" style="display: none;">';

        $handle = opendir($attach_directory.'/'.$id);
        while ($file = readdir($handle)){
            if ($file != '.' and $file != '..'){
?>

<a href="javascript:insertext('<a href=&quot;<?=$xfields->settings['path_upload']; ?>/<?=$id; ?>/<?=$file; ?>&quot;><?=$file; ?></a> (<?=formatsize(filesize($attach_directory.'/'.$id.'/'.$file)); ?>)', '', '<?=$location; ?>')"><?=$file; ?></a>

<?
            }
        }

        echo '</div>';
	}

return $location;
}

add_action('new-advanced-options', 'adepto_uploader');
add_action('edit-advanced-options', 'adepto_uploader');

function adepto_uploader(){

	ob_start();
?>

<fieldset id="adepto_fastload"><legend><?=t('Прикрепить файлы'); ?></legend>
<script language="javascript">
f = 0
function file_uploader(which){
if (which < f) return
    f ++
    d = document.getElementById('file_'+f)
    d.innerHTML = '<input type="file" name="file['+f+']" id="file_'+f+'" value="" onchange="file_uploader('+f+');" /><br /><span id="file_'+(f+1)+'">'
}
document.writeln('<input type="file" name="file[0]" value="" onchange="file_uploader(0);" /><br />')
document.writeln('<span id="file_1"></span>')
</script>

<label for="pack"><input id="pack" name="pack" type="checkbox" value="on">&nbsp;<?=t('Упаковывать простые файлы?'); ?></label><br />
<!--<label for="unpack"><input id="unpack" name="unpack" type="checkbox" value="on">&nbsp;<?=t('Распаковывать архивы?'); ?></label>-->
</fieldset>

<?
return ob_get_clean();
}

add_action('new-save-entry', 'adepto_save');
add_action('edit-save-entry', 'adepto_save');

function adepto_save(){
global $id;

	include_once includes_directory.'/zipbackup.inc.php';

    $xfields = new PluginSettings('Adepto_Fastload');

    $attach_directory = cute_parse_url($xfields->settings['path_upload']);
    $attach_directory = $attach_directory['abs'];

    if (reset($_FILES['file']['name'])){
    	if (!@mkdir($attach_directory.'/'.$id, 0777)){
    		return;
    	}

    	$zipfile = new zipfile();

	    for ($i = 0; $i < count($_FILES['file']['name']); $i++){
	    	$filename = $attach_directory.'/'.$id.'/'.$_FILES['file']['name'][$i];
	    	$ext = preg_quote(end($ext = explode('.', $filename)), '/');

	    	if (!$_FILES['file']['error'][$i]){	    		move_uploaded_file($_FILES['file']['tmp_name'][$i], $filename);

	            if ($_POST['pack'] and $_FILES['file']['type'][$i] != 'application/x-zip-compressed'){
	            	$zipfile->add_file(file_read($filename), $_FILES['file']['name'][$i]);
	            	unlink($filename);
	            }

	            if ($_POST['unpack'] and $_FILES['file']['type'][$i] == 'application/x-zip-compressed'){
	            }
	    	}
	    }

	    if ($_POST['pack']){
	        file_write($attach_directory.'/'.$id.'/'.$_FILES['file']['name'][0].'.zip', $zipfile->file());
	    }
	}
}

add_filter('news-show-generic', 'adepto_parse');

function adepto_parse($tpl){
global $config, $adepto_xfields, $attach_directory;

    if (!is_object($adepto_xfields)){
    	$adepto_xfields = new PluginSettings('Adepto_Fastload');
    	$attach_directory = cute_parse_url($adepto_xfields->settings['path_upload']);
    	$attach_directory = $attach_directory['abs'];
    }

	if ($tpl['id'] and is_dir($attach_directory.'/'.$tpl['id'])){
		$tpl['attachment'] = '<ul class="adepto_fastload">';

        $handle = opendir($attach_directory.'/'.$tpl['id']);
        while ($file = readdir($handle)){
            if ($file != '.' and $file != '..'){
            	$tpl['attachment'] .= '<li><a href="'.$adepto_xfields->settings['path_upload'].'/'.$tpl['id'].'/'.$file.'">'.$file.'</a> ('.formatsize(filesize($attach_directory.'/'.$tpl['id'].'/'.$file)).')</li>';
            }
        }

        $tpl['attachment'] .= '</ul>';
	}

return $tpl;
}

add_filter('template-short', 'adepto_vars');
add_filter('template-full', 'adepto_vars');

function adepto_vars($variables){

	$variables['attachment'] = '';

return $variables;
}

add_filter('options', 'adepto_AddToOptions');

function adepto_AddToOptions($options){

	$options[] = array(
			     'name'     => t('Adepto Fastload'),
			     'url'      => 'plugin=adepto_fastload',
			     'category' => 'files'
			     );

return $options;
}

add_action('plugins', 'adepto_CheckAdminOptions');

function adepto_CheckAdminOptions(){

	if ($_GET['plugin'] == 'adepto_fastload'){
		adepto_AdminOptions();
	}
}

function adepto_AdminOptions(){
global $config, $PHP_SELF;

    $xfields = new PluginSettings('Adepto_Fastload');
    $content = "Order Deny,Allow\r\nAllow from all\r\n\r\nAddType text/plain ";

    if ($_POST['save_con']){
    	$htaccess = cute_parse_url($_POST['save_con']['path_upload']);

    	if ($htaccess['abs']){
	    	$xfields->settings = $_POST['save_con'];
	    	$xfields->save();
	    	file_write($htaccess['abs'].'/.htaccess', $content.$_POST['save_con']['deny_files']);
	    }

    	header('Location: '.$PHP_SELF.'?plugin=adepto_fastload');
    }

    if (!$xfields->settings['delete_files']){
       $xfields->settings['delete_files'] = '0';
       $xfields->save();
    }

    if (!$xfields->settings['path_upload']){
       $xfields->settings['path_upload'] = $config['http_script_dir'].'/data/attach';
       $xfields->save();
    }

    if (!$xfields->settings['deny_files']){
       $xfields->settings['deny_files'] = '.cgi .pl .shtml .shtm .php .php3 .php4 .php5 .phtml .phtm .phps';
       $xfields->save();
    }

    $htaccess = cute_parse_url($xfields->settings['path_upload']);
    $htaccess = $htaccess['abs'].'/.htaccess';

	echoheader('options', t('Adepto Fastload'));
    echo '<form action="'.$PHP_SELF.'?plugin=adepto_fastload" method="post">';
    echo '<table cellspacing="0" cellpadding="0" width="100%" border="0">';
    showRow(t('Путь к директории загрузки файлов'), t('например: http://example.com/news/data/attach'), '<input type="text" name="save_con[path_upload]" value="'.$xfields->settings['path_upload'].'" size="40">');
    showRow(t('Удаление'), t('удалять файлы при удалении новости'), makeDropDown(array(t('Нет'), t('Да')), 'save_con[delete_files]', $xfields->settings['delete_files']));
    showRow(t('Запрещённые расширения'), t('эти файлы будут интрепритироваться сервером, как обычные текстовые файлы (text/plain); проверьте, есть ли у файла <b><small>%htaccess</small></b> права 0666 или 0777', array('htaccess' => $htaccess)), '<input type="text" name="save_con[deny_files]" value="'.$xfields->settings['deny_files'].'" size="40">');
    echo '</table>';
    echo '<br /><input type="submit" value="'.t('Сохранить').'"></form>';
	echofooter();
}

add_action('mass-deleted', 'adepto_delete');

function adepto_delete(){
global $selected_news;

	$xfields = new PluginSettings('Adepto_Fastload');
    $attach_directory = cute_parse_url($xfields->settings['path_upload']);
    $attach_directory = $attach_directory['abs'];

	if ($xfields->settings['delete_files']){
		foreach ($selected_news as $select){
			if (is_dir($attach_directory.'/'.$select)){
	            $handle = opendir($attach_directory.'/'.$select);
	            while ($file = readdir($handle)){
	                if ($file != '.' and $file != '..'){
	                    @unlink($attach_directory.'/'.$select.'/'.$file);
	                }
	            }
	        }

	        @rmdir($attach_directory.'/'.$select);
		}
	}
}
?>