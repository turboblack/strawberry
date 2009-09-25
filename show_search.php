<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

$search = replace_all_sucks($search, false);
$search = htmlspecialchars($search);

$first = $sql->select(array('table' => 'news', 'orderby' => array('date', 'ASC'), 'limit' => array(0, 1)));
$last = $sql->select(array('table' => 'news', 'orderby' => array('date', 'DESC'), 'limit' => array(0, 1)));

$sday[] = '';
for ($i = 1; $i < 32; $i++){
	$sday[$i] = $i;
}

$smonth[] = '';
for ($i = 1; $i < 13; $i++){
	$smonth[$i] = $i;
}

$syear[] = '';
for ($i = date('Y', $first[0]['date']); $i <= date('Y', $last[0]['date']); $i++){
	$syear[$i] = $i;
}
?>

<!-- ��� ������ / ������ / ����� ������ ��������� ��� ����������� -->
<form method="get" action="<?=$_SERVER['PHP_SELF']; ?>">
<input name="do" type="hidden" value="search">
<table width="400" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td width="1">�����
  <td width="99%"><nobr><input type="text" name="search" size="20" value="<?=$search; ?>"> (�� ����� 3-� ��������)</nobr>
 <tr>
  <td>� ���������
  <td><select size="1" name="category"><option value="">�� ����</option><?=category_get_tree('&nbsp;', '<option value="{id}"[php]search_this_cat({id})[/php]>{prefix}{name}</option>'); ?></select>
 <tr>
  <td><nobr>�� ���� (���/�����/����)</nobr>&nbsp;
  <td><?=makeDropDown($syear, 'year', $year); ?>/<?=makeDropDown($smonth, 'month', $month); ?>/<?=makeDropDown($sday, 'day', $day); ?>
 <tr>
  <td colspan="2"><input type="submit" value=" ����� ">
</table>
</form>
<!-- ��� ������ / ����� / ����� ��������� ��������� � ���������� -->

<?
@ignore_user_abort(true);
@set_time_limit(600);

$file = data_directory.'/search.txt';

if (!@filesize($file) or (time() - @filemtime($file)) > (3600 * 24)){
	$index = '';
	$query = $sql->select(array('table' => 'news', 'join' => array('table' => 'story', 'where' => 'id = post_id')));

	foreach ($query as $row){
	    $words = replace_all_sucks($row['title'].' '.$row['short'].' '.$row['full']);
	    $words = array_unique($words);

	    foreach ($words as $word){
	        if ($word){
	            $index .= $row['id'].'|'.$word."\r\n";
	        }
	    }
	}

	file_write($file, $index);
} else {
    if (strlen($search) < 3){
    	if ($search){
        	echo '<div class="error_message">'.t('������ ������ �������� ������� �� 3 (���) ������.').'</div>';
        }
	} else {
		$index  = file($file);
		$result = array();

		for ($i = 0; $i < sizeof($index); $i++){
			list($post_id, $word) = explode('|', $index[$i]);

			if (in_array(trim($word), explode(' ', $search))){
				$result[] = $post_id;
			}
		}

        if (!$result){
            echo '<div class="error_message">'.t('��� �� ����-������� ������ �� �������.').'</div>';
        } else {
	        $result = array_unique($result);

	        add_filter('news-entry-content', 'highlight_search');
	        function highlight_search($output){
	        	global $search;
	            $output = formattext($search, $output);
	        	return $output;
	        }

            add_filter('news-where', 'show_search');

			function show_search($where){
				global $result;
				$where[] = 'id ? ['.join('|', $result).']';
				$where[] = 'and';
				return $where;
			}

	        $template = 'Search';
	        include rootpath.'/show_news.php';
        }
	}
}

#-------------------------------------------------------------------------------

function need_mooore(&$item1, $key){

	if (strlen($item1) < 3){
		$item1 = '';
	}
}

/**
 * ������������ $whatfind � $text
 *
 * @link http://forum.dklab.ru/php/heap/AllocationOfResultInNaydenomAPieceOfTheText.html
 *
 * @param string $whatfind ������� �����
 * @param string $text �����, � ������� ���������� �����
 * @return string
 */
function formattext($whatfind, $text){

	$pos    = @strpos(strtoupper($text), strtoupper($whatfind));
	$otstup = 200; // ���-�� �������� ��� ������ ����������
	$result = '';

	if ($pos !== false){ //���� ������� ���������
	    if ($pos < $otstup){ //���� ����������� ������ ��� ������ N ��������
	        $result = substr($text, 0, $otstup * 2); //�� ��������� ��������� �� ������ � �� N-�� �������
	        $result = eregi_replace($whatfind, '<span class="hilite">'.$whatfind.'</span>', $result);
	    } else {
	        $start = $pos-$otstup;
	        //�� ��������� N ��������  �� ���������� � N �������� �����
	        $result = '...'.substr($text, $pos-$otstup, $otstup * 2).'...';
	        // ��������
	        $result = eregi_replace($whatfind, '<span class="hilite">'.$whatfind.'</span>', $result);
	    }
	} else {
	    $result = substr($text, 0, $otstup * 2);
	}

return $result;
}

function replace_all_sucks($text, $array_walk = true){

    $text = strip_tags($text);
    $text = str_replace("\r\n", '', $text);
	$text = preg_replace('/\W/', ' ', $text);
	$text = strtolower($text);

	if ($array_walk){
		$text = explode(' ', $text);
		array_walk($text, 'need_mooore');
	}

return $text;
}

function search_this_cat($id){
global $category;

return ($id == $category ? ' selected' : '');
}
?>