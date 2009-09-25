<?php
/**
 * @package Plugins
 */

/*
Plugin Name: 	Keywords
Plugin URI:     http://cutenews.ru
Description: 	Ключевые слова новости. Выводить так: <code>&lt;?=cn_keywords(); ?&gt;</code>.
Version: 		1.0
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_action('head', 'keywords');

/**
 * @access private
 */
function keywords(){
global $sql, $cache, $_keywords, $config;

	if (!$sql->table_exists('keywords')){
		$rufus = parse_ini_file(rufus_file, true);
		$rufus['home']['keywords'] = '?keywords={keywords-id}';
		write_ini_file(rufus_file, $rufus);

	    $sql->createtable(array(
	    'table'   => 'keywords',
	    'columns' => array(
	             	 'id'   => array(
	             	 		   'type'           => 'int',
	             	 		   'auto_increment' => 1,
	             	 		   'primary'        => 1
	             	 		   ),
	             	 'name' => array('type' => 'string'),
	             	 'url'  => array('type' => 'string')
	             	 )
	    ));

        $sql->altertable(array(
        'table'  => 'news',
        'action' => 'insert',
        'name'   => 'keywords',
        'values' => array('type' => 'string')
        ));

        return false;
    }

	if (!$_keywords = $cache->unserialize('_keywords')){
	    foreach ($sql->select(array('table' => 'keywords', 'orderby' => array('id', 'ASC'))) as $row){
	        $_keywords['id'][$row['name']] = $row['id'];
	        $_keywords['id'][$row['url']]  = $row['id'];
	        $_keywords['id'][$row['id']]   = $row['id'];
	        $_keywords['name'][$row['id']] = $row['name'];
	        $_keywords['url'][$row['id']]  = $row['url'];
	    }

	    $_keywords = $cache->serialize($_keywords);
	}
}

add_filter('news-where', 'keywords_where');

/**
 * @access private
 */
function keywords_where($where){
global $keywords, $_keywords;

    if ($keywords and $keywords != 'none'){
        foreach (explode(',', $keywords) as $key){
            $keywords_tmp .= $_keywords['id'][trim($key)].',';
        }

	    if (chicken_dick($keywords_tmp, ',')){
	        $where[]  = 'keywords ? ['.str_replace(',', '|', chicken_dick($keywords_tmp, ',')).']';
	        $where[] = 'and';
	    } else {
    		$where = array('id = 0', 'and');
    	}
    }

return $where;
}

add_filter('unset', 'keywords_unset');

/**
 * @access private
 */
function keywords_unset($var){

	$var[] = 'keywords';

return $var;
}

add_filter('constructor-functions', 'keywords_constructor_functions');

/**
 * @access private
 */
function keywords_constructor_functions($functions){

	$functions['cn_keywords'] = array('string');
	$functions['cute_get_keywords'] = array('string');

return $functions;
}

add_filter('constructor-variables', 'keywords_constructor_variables');

/**
 * @access private
 */
function keywords_constructor_variables($variables){
global $sql;

	ob_start();
?>

<select name="keywords[]" size="5" multiple="multiple">
<option value=""<?=(!$_POST['keywords'][0] ? 'selected' : ''); ?>><?=t('- все -'); ?></option>
<? foreach ($sql->select(array('table' => 'keywords', 'orderby' => array('id', 'ASC'))) as $row){ ?>
<option value="<?=$row['id']; ?>"<?=(@in_array($row['id'], $_POST['keywords']) ? 'selected' : ''); ?>><?=$row['name']; ?></option>
<? } ?>
</select>

<?
	$variables['keywords'] = array('string', ob_get_clean());

return $variables;
}

add_action('head', 'keywords_remove');

/**
 * @access private
 */
function keywords_remove(){
global $PHP_SELF, $sql;

	if ($_GET['keywordremove']){
		$sql->delete(array('table' => 'keywords', 'where' => array('id = '.$_GET['keywordremove'])));
		header('Location: '.$PHP_SELF.'?mod=editnews&id='.$_GET['id']);
	}
}

add_action('new-advanced-options', 'keywords_AddEdit');
add_action('edit-advanced-options', 'keywords_AddEdit');

/**
 * @access private
 */
function keywords_AddEdit(){
global $id, $config, $PHP_SELF;

	$keywords = cute_get_keywords('<label for="key{id}"><input type="checkbox" [php]keywords_select({id})[/php] name="key[{id}]" id="key{id}")">&nbsp;{name}</label> <sup><a href="'.$PHP_SELF.'?plugin=keywords&action=remove&id={id}" title="'.t('Удалить кейворд "%keyword"', array('keyword' => '{name}')).'" onclick="remove_keywords_call_ajax({id});return false;">x</a></sup><br />');

	ob_start();
?>

<script>
function keywords_complete(request){
    if (request.status == 200){
        $('keywordslist').innerHTML = request.responseText;
        $('keywords_add').innerHTML = '';
    } else {
        keywords_failure(request);
    }
}

function keywords_failure(request){
    //Element.show('error_message');
    //$('error_message').innerHTML = request.responseText;
}

function add_keywords_call_ajax(that){
	new Ajax.Updater(
	    {success: 'keywordslist'},
	    '<?=$PHP_SELF; ?>?plugin=keywords&action=add&id=<?=$id?>',
	    {
	        insertion: Insertion.Top,
	        onComplete: function(request){keywords_complete(request)},
	        onFailure: function(request){keywords_failure(request)},
	        parameters: Form.Element.serialize(that),
	        evalScripts: true
	    }
	);
}

function remove_keywords_call_ajax(that){
	new Ajax.Updater(
	    {success: 'keywordslist'},
	    '<?=$PHP_SELF; ?>?plugin=keywords&action=remove&id=<?=$id?>&keyid=' + that,
	    {
	        insertion: Insertion.Top,
	        onComplete: function(request){keywords_complete(request)},
	        onFailure: function(request){keywords_failure(request)},
	        evalScripts: true
	    }
	);
}
</script>

<fieldset id="keywords"><legend><?=t('Ключевые слова'); ?></legend>
<div id="keywordslist"><?=$keywords; ?></div>
</fieldset>

<fieldset id="add_keywords"><legend><?=t('Добавить ключевые слова'); ?></legend>
<textarea id="keywords_add" name="keywords_add" title="<?=t('Дабавить кейвордов, одна строка - один кейворд'); ?>"></textarea>
<br />
<input type="button" value="<?=t('Добавить'); ?>" onclick="add_keywords_call_ajax('keywords_add');return false;">
</fieldset>

<?
return ob_get_clean();
}

add_action('plugins', 'keywords_CheckAdminOptions');

/**
 * @access private
 */
function keywords_CheckAdminOptions(){

	if ($_GET['plugin'] == 'keywords'){
		if ($_GET['action'] == 'add'){
			keywords_ajax_add();
		} elseif ($_GET['action'] == 'remove'){
			keywords_ajax_remove();
		}
	}
}

/**
 * @access private
 */
function keywords_ajax_add(){
global $sql, $PHP_SELF, $config;

    if ($_POST['keywords_add']){
    	header('Content-type: text/html; charset='.$config['charset']);

	    foreach ($_POST as $k => $v){
	        $_POST[$k] = iconv('utf-8', $config['charset'], $v);
	    }

    	$keywords_array = explode("\n", trim($_POST['keywords_add']));
    	$keywords_array = array_unique($keywords_array);
    	$keyword_exist  = array();

    	foreach ($sql->select(array('table' => 'keywords')) as $row){
    		$keyword_exist[] = strtolower($row['name']);
    	}

	    foreach ($keywords_array as $v){
	    	if ($v and !in_array(strtolower($v), $keyword_exist)){
	            $sql->insert(array(
	            'table'  => 'keywords',
	            'values' => array('name' => $v, 'url' => (totranslit($v) ? totranslit($v) : 'keyword_'.($sql->last_insert_id('keywords', '', 'id') + 1)))
	            ));
	    	}
	    }

	    echo cute_get_keywords('<label for="key{id}"><input type="checkbox" [php]keywords_select({id})[/php] name="key[{id}]" id="key{id}")">&nbsp;{name}</label> <sup><a href="'.$PHP_SELF.'?plugin=keywords&action=remove&id={id}" title="'.t('Удалить кейворд "%keyword"', array('keyword' => '{name}')).'" onclick="remove_keywords_call_ajax({id});return false;">x</a></sup><br />');
	}
}

/**
 * @access private
 */
function keywords_ajax_remove(){
global $sql, $PHP_SELF, $config;

    if ($_GET['keyid']){
    	header('Content-type: text/html; charset='.$config['charset']);

        $sql->delete(array(
        'table' => 'keywords',
        'where' => array("id = $_GET[keyid]"),
        ));

        echo cute_get_keywords('<label for="key{id}"><input type="checkbox" [php]keywords_select({id})[/php] name="key[{id}]" id="key{id}")">&nbsp;{name}</label> <sup><a href="'.$PHP_SELF.'?plugin=keywords&action=remove&id={id}" title="'.t('Удалить кейворд "%keyword"', array('keyword' => '{name}')).'" onclick="remove_keywords_call_ajax({id});return false;">x</a></sup><br />');
	}
}

add_action('new-save-entry', 'keywords_save', 1);
add_action('edit-save-entry', 'keywords_save', 1);

/**
 * @access private
 */
function keywords_save(){
global $id, $sql;

	if ($_POST['key']){
		foreach ($_POST['key'] as $k => $v){
			$keywords_tmp[] = $k;
		}

		$sql->update(array(
		'table'  => 'news',
		'where'  => array("id = $id"),
		'values' => array('keywords' => join(',', $keywords_tmp))
		));
	}
}

add_filter('news-show-generic', 'keywords_template');

/**
 * @access private
 */
function keywords_template($tpl){
global $row, $_keywords;

    if ($key_arr = explode(',', $row['keywords'])){
        $key = array();

        foreach ($key_arr as $v){
            $key['id'][]   = $v;
            $key['name'][] = ($_keywords['name'][$v] ? '<a href="'.cute_get_link(array('id' => $v, 'url' => $_keywords['url'][$v]), 'keywords').'">'.$_keywords['name'][$v].'</a>' : '');
        }
    }

    $tpl['keywords'] = array('name' => join(', ', $key['name']), 'id' => join(', ', $key['id']));

return $tpl;
}

add_filter('cute-get-link', 'keywords_put_link');

/**
 * @access private
 */
function keywords_put_link($output){
global $QUERY_STRING;

	$QUERY_STRING   = cute_query_string($QUERY_STRING, array('keywords'));
    $output['link'] = str_replace('{keywords}', $output['arr']['url'], $output['link']);
    $output['link'] = str_replace('{keywords-id}', $output['arr']['id'], $output['link']);

return $output;
}

add_filter('htaccess-rules-replace', 'keywords_rules_replace');

/**
 * @access private
 */
function keywords_rules_replace($output){
global $_keywords, $config;

    if ($_POST['keywords_add']){
    	$keywords_array = explode("\r\n", $_POST['keywords_add']);
    	$keywords_array = array_unique($keywords_array);
    	$keyword_exist  = array();

        if ($_keywords['url']){
	        foreach ($_keywords['url'] as $k => $v){
	            $keyword_exist[] = strtolower($v);
	        }
    	}

	    foreach ($keywords_array as $v){
	    	if ($v and !in_array(strtolower($v), $keyword_exist)){
	    		$_keywords['url'][] = $v;
	    	}
	    }
	}

    if ($config['mod_rewrite'] and $_keywords['url']){
        foreach ($_keywords['url'] as $v){
            $keywords[] = $v;
        }

        if ($keywords){
            $keywords = join('|', $keywords);
            $keywords = '(none|'.$keywords.')';
        }
    } else {
        $keywords = '([_0-9a-z-]+)';
    }

    $output = str_replace('{keywords}', $keywords, $output);
    $output = str_replace('{keywords-id}', '([0-9]+)', $output);

return $output;
}

add_filter('htaccess-rules-format', 'keywords_rules_format');

/**
 * @access private
 */
function keywords_rules_format($output){

    $output = str_replace('{keywords-id', '{keywords', $output);

return $output;
}

add_filter('template-active', 'keywords_macros_variables');
add_filter('template-full', 'keywords_macros_variables');

/**
 * @access private
 */
function keywords_macros_variables($output){

	$output['keywords'] = t('Кейворды, к которым относится новость. Например: $tpl[\'post\'][\'keywords\'][\'name\'] выведет название кейвордов, а $tpl[\'post\'][\'keywords\'][\'id\'] - их ID');

return $output;
}

#-------------------------------------------------------------------------------

/**
 * @access private
 */
function count_keywords_entry($keyid){
global $sql;

	$result = $sql->count(array('table' => 'news', 'where' => array('keywords ? ['.$keyid.']')));

return $result;
}

/**
 * @access private
 */
function keywords_select($that){
global $sql;

    if ($_GET['id']){
	    foreach ($sql->select(array('table' => 'news', 'where' => array("id = $_GET[id]"))) as $row){
	        if (in_array($that, explode(',', $row['keywords']))){
	            return 'checked';
	        }
	    }
	}
}

/**
 * @see cute_get_keywords()
 *
 * @param string $tpl Шаблон
 * @return string Список кейвордов по шаблону
 */
function cn_keywords($tpl = '<a href="[php]cute_get_link($row, keywords)[/php]">{name} ([php]count_keywords_entry({id})[/php])</a><br />'){
global $cache;
static $uniqid;

    if (!$output = $cache->get('keywords', $uniqid++)){
        $output = $cache->put(cute_get_keywords($tpl));
    }

return $output;
}

/**
 * Возвращает список кейвордов.
 *
 * Возвращает список кейвордов, используя шаблон $tpl.
 *
 * Теги для использования в шаблоне вывода:
 * {name} - название кейворда,
 * {url} - УРЛ кейворда,
 * {id} - ID кейворда,
 * [php] и [/php] - между этими тегами указывается php-код, который будет выполнен (например: [php]function({id})[/php]).
 *
 * @param string $tpl
 * @return string
 */
function cute_get_keywords($tpl = '{name}'){
global $sql, $PHP_SELF;

	foreach ($sql->select(array('table' => 'keywords', 'orderby' => array('id', 'ASC'))) as $row){
		$find = array('/{id}/i', '/{name}/i', '/{url}/i', '/\[php\](.*?)\[\/php\]/ie');
		$repl = array($row['id'], $row['name'], $row['url'], '\\1');
		$johnny_left_teat .= preg_replace($find, $repl, $tpl);
	}

return $johnny_left_teat;
}
?>