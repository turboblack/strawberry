<?php
/**
 * @package Plugins
 * @access private
 */

// XFields self-cleaning

add_action('deleted-single-entry', 'clean_single_xfields');
add_action('deleted-multiple-entries', 'clean_multiple_xfields');

function clean_single_xfields($hook){
global $row, $id;

	$xfields = new XfieldsData();
	$xfields->delete(($id ? $id : $row['id']));
	$xfields->save();
}

function clean_multiple_xfields($hook) {
global $selected_news;

	$xfields = new XfieldsData();

	foreach ($selected_news as $news_id){
		$xfields->delete($news_id);
	}

	$xfields->save();
}

// sticky

add_action('new-advanced-options', 'sticky_AddEdit');
add_action('edit-advanced-options', 'sticky_AddEdit');

function sticky_AddEdit(){
global $row, $config;

	if ($config['database'] != 'txtsql'){
		return '<fieldset id="sticky"><legend>'.t('��������� �������').'</legend><label for="sticky_post"><input type="checkbox" id="sticky_post" name="sticky_post" value="on"'.($row['sticky'] ? ' checked' : '').'>&nbsp;'.t('��������� ��� �������?').'</label></fieldset>';
	}
}

add_action('new-save-entry', 'sticky_Save');
add_action('edit-save-entry', 'sticky_Save');

function sticky_Save(){
global $sql;

    $sql->update(array(
    'table'  => 'news',
    'where'  => array("id = $_POST[id]"),
    'values' => array('sticky' => ($_POST['sticky_post'] ? 1 : 0))
    ));
}

// date

add_action('new-advanced-options', 'date_AddEdit', 1);
add_action('edit-advanced-options', 'date_AddEdit', 1);

function date_AddEdit(){
global $row, $config;

    for ($i = 1; $i <= 12; $i++){
        $months[date('M', mktime(0, 0, 0, $i))] = ucfirst(langdate('M', mktime(0, 0, 0, $i)));
    }

    $time    = ($row['date'] ? $row['date'] : time);
	$result  = '<fieldset id="date"><legend>'.t('����').'</legend>';
	$result .= '<input type="text" name="day" maxlength="2" style="width: 19px;" value="'.langdate('d', $time).'" title="'.t('����').'">';
    $result .= makeDropDown($months, 'month', date('M', $time));
    $result .= '<input type="text" name="year" maxlength="4" style="width: 32px;" value="'.langdate('Y', $time).'" title="'.t('���').'">';
    $result .= '@<input type="text" name="hour" maxlength="2" style="width: 19px;" value="'.langdate('H', $time).'" title="'.t('���').'">';
    $result .= ':<input type="text" name="minute" maxlength="2" style="width: 19px;" value="'.langdate('i', $time).'" title="'.t('������').'">';
    $result .= ':<input type="text" name="second" maxlength="2" style="width: 19px;" value="'.langdate('s', $time).'" title="'.t('�������').'">';
    $result .= '</fieldset>';

return $result;
}

// usergroups

add_action('new-advanced-options', 'AddEdit_usergroups_check_fields', 1000000);
add_action('edit-advanced-options', 'AddEdit_usergroups_check_fields', 1000000);

function AddEdit_usergroups_check_fields(){
global $usergroups_check_fields;

return $usergroups_check_fields;
}

add_action('head', 'head_usergroups_check_fields');

function head_usergroups_check_fields(){
global $mod, $usergroups_check_fields;

	if ($mod and cute_get_rights($mod, 'read') and ($mod == 'addnews' or $mod == 'editnews')){
		preg_match_all('/fieldset id="(.*?)"><legend>(.*?)<\/legend>/i', run_actions('new-advanced-options'), $fields['new']);
		preg_match_all('/fieldset id="(.*?)"><legend>(.*?)<\/legend>/i', run_actions('edit-advanced-options'), $fields['edit']);

		$fields[1] = array_merge($fields['new'][1], $fields['edit'][1]);
		$fields[1] = array_unique($fields[1]);

		unset($fields[0], $fields['new'], $fields['edit']);

		ob_start();
?>

<script>
<? foreach ($fields[1] as $k => $v){ ?>
	<? if (!cute_get_rights($v, 'fields')){ ?>
		_getElementById('<?=$v; ?>').style.display = 'none';
	<? } ?>
<? } ?>
</script>

<?
		$usergroups_check_fields = ob_get_clean();
	} else {
		$usergroups_check_fields = '';
	}
}

// multicats

function multicats($that){
global $sql, $id, $usergroups, $member, $config;

    if ($usergroups[$member['usergroup']]['permissions']['categories'] and !in_array($that, explode(',', $usergroups[$member['usergroup']]['permissions']['categories']))){
        return 'disabled';
    }

    if ($id){
	    foreach ($sql->select(array('table' => 'news', 'where' => array("id = $id"))) as $row){
	        if (in_array($that, explode(',', $row['category']))){
	            return 'checked';
	        }
	    }
	}
}

add_action('new-advanced-options', 'multicats_AddEdit', 2);
add_action('edit-advanced-options', 'multicats_AddEdit', 2);

function multicats_AddEdit(){
global $id, $mod, $categories;

	if (count($categories) > 50){
		$style = ' style="overflow: scroll;width: 100%;height: 200px;"';
	}

	if ($category = category_get_tree('&nbsp;', '<label for="cat{id}"><input type="checkbox" [php]multicats({id})[/php] name="cat[{id}]" id="cat{id}")">&nbsp;{name}</label><br />')){
		return '<fieldset id="category"><legend>'.t('���������').'</legend>'.$category.'</fieldset>';
	}
}

add_action('new-save-entry', 'multicats_Save', 1);
add_action('edit-save-entry', 'multicats_Save', 1);

function multicats_Save(){
global $cat, $category;

    if ($cat){
		foreach ($cat as $k => $v){
			$category_tmp[] = $k;
		}

		$category = join(',', $category_tmp);
	}
}

// cache_remover

add_action('head', 'cache_remover');

function cache_remover(){
global $cache, $id, $is_logged_in, $action, $member;

    if ($is_logged_in and $action == 'clearcache'){
    	$cache->delete();
    } elseif ($_POST){
		$cache->delete($id);
	}
}

// rufus

add_action('head', 'rufus');

function rufus(){
global $is_logged_in, $mod, $config;

	if (!$config['mod_rewrite'] and !$mod){
		$urls = parse_ini_file(rufus_file, true);
	    foreach ($urls as $url_k => $url_v){
	        foreach ($url_v as $k => $v){
	            @preg_match_all('/'.@str_replace('/', '\/', htaccess_rules_replace($v)).'/i', $_SERVER['REQUEST_URI'], $query);
	            for ($i = 0; $i < count($query); $i++){
	                if ($query[$i][0]){
	                    if ($clear = preg_replace('/(.*?)=\$([0-9]+)/i', '', str_replace('$'.$i, $query[$i][0], str_replace('?', '', htaccess_rules_format($v))))){
	                        $str[] = $clear;
	                    }
	                }
	            }
	        }
	    }

	    if ($str){
	        $str = preg_replace('/([\&]+)/i', '&', join('&', array_reverse($str)));
	        parse_str($str, $_CUTE);

	        foreach ($_CUTE as $k => $v){
	            $GLOBALS[$k] = $_GET[$k] = @htmlspecialchars($v);
	        }
	    }
	}
}

add_action('new-advanced-options', 'rufus_AddEdit', 3);
add_action('edit-advanced-options', 'rufus_AddEdit', 3);

function rufus_AddEdit(){
global $row;

return '<fieldset id="url"><legend>'.t('��� (��� �������)').'</legend><input type="text" size="42" name="url" value="'.$row['url'].'"></fieldset>';
}

add_action('head', 'make_htaccess');

function make_htaccess(){
global $mod, $PHP_SELF, $config;

	$settings         = cute_parse_url($config['http_home_url']);
	$configs          = cute_parse_url($config['http_script_dir']);
	$types            = parse_ini_file(rufus_file, true);
	$settings['path'] = ($settings['path'] ? '/'.$settings['path'].'/' : '/');
	$configs['path']  = ($configs['path'] ? '/'.$configs['path'].'/' : '/');
	$uhtaccess        = new	PluginSettings('uhtaccess');
	$htaccess         = array();

	if ($mod and $_POST and $settings['file'] and $config['mod_rewrite']){
	    $htaccess[] = '#DirectoryIndex '.$settings['file'];
	    $htaccess[] = '# [user htaccess] '.$uhtaccess->settings;
	    $htaccess[] = '<IfModule mod_rewrite.c>';
	    $htaccess[] = 'RewriteEngine On';
	    $htaccess[] = '#Options +FollowSymlinks';
	    $htaccess[] = '#RewriteBase '.$settings['path'];

	    foreach ($types as $type_k => $type_v){
	        foreach ($type_v as $k => $v){
	            $v = preg_replace('/\{(.*?)\:(.*?)\}/i', '{\\1|>|\\2}', $v);
	            $v = parse_url($v);
	            $v = preg_replace('/\{(.*?)\|>\|(.*?)\}/i', '{\\1:\\2}', $v['path']);

	            $htaccess[] = '# ['.$type_k.'] '.$k;
	            $htaccess[] = (!$v ? '# [wrong rule] ' : '');
	            $htaccess[] = 'RewriteRule ^'.(($type_k == 'home' or substr($type_k, 0, 5) == 'home/') ? '' : '').htaccess_rules_replace($v).'(/?)+$ '.htaccess_rules_format($v, ($type_k == 'home' ? $settings['file'] : (substr($type_k, 0, 5) == 'home/' ? substr($type_k, 5).'/' : $configs['path'].$type_k))).' [QSA,L]';
	        }
	    }

	    $htaccess[] = '</IfModule>';

		if (!is_writable($settings['abs'].'/.htaccess')){
			@chmod($settings['abs'].'/.htaccess', 0777);
		}

		file_write($settings['abs'].'/.htaccess', join("\r\n", $htaccess));
	}
}

add_filter('options', 'rufus_AddToOptions');

function rufus_AddToOptions($options){

	$options[] = array(
			     'name'     => t('���������� ������'),
			     'url'	    => 'plugin=rufus',
			     'category' => 'options'
			     );

return $options;
}

add_action('plugins', 'rufus_CheckAdminOptions');

function rufus_CheckAdminOptions(){

	if ($_GET['plugin'] == 'rufus'){
		rufus_AdminOptions();
	}
}

function rufus_AdminOptions(){
global $PHP_SELF, $config;

	if ($_POST){
		header('Location: '.$PHP_SELF.'?plugin=rufus');
	}

	$settings         = cute_parse_url($config['http_home_url']);
	$configs          = cute_parse_url($config['http_script_dir']);
	$types            = parse_ini_file(rufus_file, true);
	$settings['path'] = ($settings['path'] ? '/'.$settings['path'].'/' : '/');
	$configs['path']  = ($configs['path'] ? '/'.$configs['path'].'/' : '/');
	$uhtaccess        = new	PluginSettings('uhtaccess');
	$htaccess         = array();

	if (!$settings['file']){
		msg('error', t('���������� ������'), t('��������, �� �� �� ������� �����, � ������� ����� ������������ ������� ��� ������� �������. �������� ��� � ��������� �������.'));
	}

	echoheader('user', t('���������� ������'));

    if (!is_writable($settings['abs'].'/.htaccess')){
        @chmod($settings['abs'].'/.htaccess', 0777);
    }

	if (ini_get('safe_mode') and $config['mod_rewrite'] and !is_writable($settings['abs'].'/.htaccess')){
		echo '<div class="panel">'.t('<b style="color: red;">�������� ������</b><br />�� ����� ������� ������� Safe Mode. ��������, �� ������� ������� ���� .htaccess. �� ������ ������, �������� ���� .htaccess � ���������� <b>%directory</b> � ��������� ��� ����� <b>0666</b><br /><br />����� �������� ����������� �� ����� �� ������ ��� ����� <b>data/urls.ini</b>.', array('directory' => $settings['abs'])).'</div><br />';
	}

	$htaccess[] = '#DirectoryIndex '.$settings['file'];
	$htaccess[] = '# [user htaccess] '.$uhtaccess->settings;
	$htaccess[] = '<IfModule mod_rewrite.c>';
	$htaccess[] = 'RewriteEngine On';
	$htaccess[] = '#Options +FollowSymlinks';
	$htaccess[] = '#RewriteBase '.$settings['path'];

    foreach ($types as $type_k => $type_v){
        foreach ($type_v as $k => $v){
        	$v = preg_replace('/\{(.*?)\:(.*?)\}/i', '{\\1|>|\\2}', $v);
	    	$v = parse_url($v);
	    	$v = preg_replace('/\{(.*?)\|>\|(.*?)\}/i', '{\\1:\\2}', $v['path']);

            $htaccess[] = '# ['.$type_k.'] '.$k;
            $htaccess[] = (!$v ? '# [wrong rule] ' : '');
            $htaccess[] = 'RewriteRule ^'.(($type_k == 'home' or substr($type_k, 0, 5) == 'home/') ? '' : '').htaccess_rules_replace($v).'(/?)+$ '.htaccess_rules_format($v, ($type_k == 'home' ? $settings['file'] : (substr($type_k, 0, 5) == 'home/' ? substr($type_k, 5).'/' : $configs['path'].$type_k))).' [QSA,L]';
        }
    }

    $htaccess[] = '</IfModule>';

	echo '<div class="panel">'.t('���� "urls.ini" ���������� � ��� ����������� ��������� ��� �����. <a onClick="javascript:Help(\'rufus\')" href="#">� �����, ��������� � ���� ������� :) �������� � ���</a>. ����� �������������� ������� "%save".', array('save' => t('��������� urls.ini'), 'make' => t('������� .htaccess'))).'</div>';
?>

<form action="<?=$PHP_SELF; ?>?plugin=rufus" method="post">
<h3>.htaccess:</h3>
<textarea name="uhtaccess" rows="5" cols="20" onkeydown="_getElementById('urls').disabled = false;_getElementById('htaccess').disabled = true;"><?=$uhtaccess->settings; ?></textarea>
<h3>urls.ini:</h3>
<textarea name="urls_content" rows="5" cols="20" onkeydown="_getElementById('urls').disabled = false;_getElementById('htaccess').disabled = true;"><?=file_read(rufus_file); ?></textarea>
<br /><br />
<input type="submit" name="urls" id="urls" value="  <?=t('��������� urls.ini'); ?>  " disabled>
<input type="submit" name="htaccess" id="htaccess" value="   <?=t('������� .htaccess'); ?>">
</form>

<?
	if ($_POST['urls']){
		if (!is_writable(rufus_file)){
			@chmod(rufus_file, 0777);
		}

        $uhtaccess->settings = $_POST['uhtaccess'];
        $uhtaccess->save();
		file_write(rufus_file, replace_news('admin', $_POST['urls_content']));
	}

	if ($_POST['htaccess']){
		if (!is_writable($settings['abs'].'/.htaccess')){
			@chmod($settings['abs'].'/.htaccess', 0777);
		}

		file_write($settings['abs'].'/.htaccess', join("\r\n", $htaccess));
	}

	echofooter();
}

function htaccess_rules_replace($output){
global $categories, $config;

    if ($_POST['catid'] and $_POST['name']){
		$categories[$_POST['catid']]['url'] = ($_POST['url'] ? $_POST['url'] : totranslit($_POST['name']));
		$categories[$_POST['catid']]['parent'] = $_POST['parent'];
	}

	if ($categories and $config['mod_rewrite']){
	    foreach ($categories as $k => $row){
	    	$cat[] = $row['url'];

	        if (!$row['parent']){
	            $cats[] = $row['url'];
	        } else {
	            $cats[] = category_get_link($k);
	        }
	    }

	    if ($cats){
	        $cats = join('|', $cats);
	        $cats = '(none|'.$cats.')';
	    }

	    if ($cat){
	        $cat = join('|', $cat);
	        $cat = '(none|'.$cat.')';
	    }
	} else {
		$cat  = '([_0-9a-z-]+)';
		$cats = '([/_0-9a-z-]+)';
	}

    $output = preg_replace('/{(.*?):(.*?)}/i', '{\\1}', $output);
    $output = run_filters('htaccess-rules-replace', $output);
	$output = strtr($output, array(
	          '{id}'          => '([0-9]+)',
	          '{year}'        => '([0-9]{4})',
	          '{month}'       => '([0-9]{2})',
	          '{Month}'       => '([0-9a-z]{2,3})',
	          '{day}'         => '([0-9]{2})',
	          '{title}'       => '([_0-9a-z-]+)',
	          '{url}'         => '([_0-9a-z-]+)',
	          '{user}'        => /*'([_0-9a-zA-Z-]+)'*/'(.*)',
	          '{user-id}'     => '([0-9]+)',
	          '{category-id}' => '([0-9]+)',
	          '{category}'    => $cat,
	          '{categories}'  => $cats,
	          '{skip}'        => '([0-9]+)',
	          '{page}'        => '([0-9]+)',
	          '{cpage}'       => '([0-9]+)',
	          '{add}'		  => ''
	          ));

return $output;
}

function htaccess_rules_format($output, $result = false){

    $output = run_filters('htaccess-rules-format', $output);
	$output = str_replace('{Month', '{month', $output);
	//$output = str_replace('{id}', '{id:rewrite_rule=id}', $output);
	$output = str_replace('{title}', '{id}', $output);
	$output = str_replace('{url}', '{id}', $output);
	$output = str_replace('{categories', '{category', $output);
	$output = str_replace('{category-id', '{category', $output);
	$output = preg_replace('/{(.*?):(.*?)}/i', '{\\1}{\\2}', $output);
	$output = str_replace('{add}', '', $output);

	preg_match_all('/\{(.*?)\}/i', $output, $array);

	for ($i = 0; $i < count($array[1]); $i++){
		$result .= ($i ? '&' : '?').(!eregi('=', $array[1][$i]) ? $array[1][$i].'=$'.($i + 1) : $array[1][$i]);
	}

return $result;
}

// etc

add_filter('new-advanced-options', 'advanced_options_empty');
add_filter('edit-advanced-options', 'advanced_options_empty');

function advanced_options_empty($story){

	if ($story != 'short' and $story != 'full'){
		return $story;
	}
}
?>
