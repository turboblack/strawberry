<?php
/**
 * @package Plugins
 */

/*
Plugin Name: 	CN functions
Plugin URI:     http://cutenews.ru
Description: 	<code>&lt;?=cn_calendar(); ?&gt;</code> - календарь.<br /><code>&lt;?=cn_archives(); ?&gt;</code> - список месяцев.<br /><code>&lt;?=cn_category(); ?&gt;</code> - список категорий.<br /><code>&lt;?=cn_title(); ?&gt;</code> - заголовки.
Version: 		2.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('constructor-functions', 'etc_constructor_functions');

/**
 * @access private
 */
function etc_constructor_functions($functions){

	$functions['cn_calendar'] = '';
	$functions['cn_archives'] = array('string', 'array');
	$functions['cn_category'] = array('string', 'string', 'bool', 'int');
	$functions['cn_title'] = array('string', 'bool', 'string');

return $functions;
}

/**
 * Возвращает таблицу месяца
 *
 * @return string
 */
function cn_calendar(){
global $cache, $year, $month, $day, $PHP_SELF, $sql, $config;

    $year  = ($year ? $year : $_GET['year']);
    $month = ($month ? $month : $_GET['month']);
    $day   = ($day ? $day : $_GET['day']);

    if (!$post_arr = $cache->unserialize('calendar', ($day ? $day.'.' : date('d', time)).($month ? $month : date('m', time)).'.'.($year ? $year : date('Y', time)))){
    	$time = ($month ? $month : date('m', time)).'/01/'.($year ? $year : date('Y', time));
    	$fday = strtotime(date('m/d/Y 00:00:01', strtotime($time)));
    	$lday = strtotime(date('m/t/Y 23:59:59', strtotime($time)));

    	$tmonth = $sql->select(array(
    			 'table'   => 'news',
    			 'where'   => array('date > '.$fday, 'and', 'date < '.$lday),
    			 'orderby' => array('date', 'ASC'),
    			 'select'  => array('date')
    			 ));
        $pmonth = $sql->select(array(
                  'table'   => 'news',
                  'where'   => array('date < '.$tmonth[0]['date']),
                  'orderby' => array('date', 'DESC'),
                  'limit'   => array(0, 1),
                  'select'  => array('date')
                  ));

        $nmonth = $sql->select(array(
                  'table' => 'news',
                  'where' => array('date > '.$tmonth[(count($tmonth) - 1)]['date']),
                  'orderby' => array('date', 'ASC'),
                  'limit'   => array(0, 1),
                  'select'  => array('date')
                  ));


    	foreach (array_merge($pmonth, $tmonth, $nmonth) as $row){
    		$post_arr[] = $row['date'];
    	}


    	@rsort($post_arr);
    	$post_arr = $cache->serialize($post_arr);
    }

    if ($year and $month){
        $_this['month'] = $month;
        $_this['year']  = $year;
    } else {
        $_this['month'] = date('m', $post_arr[0]);
        $_this['year']  = date('Y', $post_arr[0]);
    }

    if (!$calendar = $cache->get(($day ? $day.'.' : '').$_this['month'].'.'.$_this['year'])){
        foreach ($post_arr as $date){
            if ($_this['year'] == date('Y', $date) and $_this['month'] == date('m', $date)){
            	$events[date('j', $date)] = $date;
            }

            if ($_this['month'].$_this['year'] != date('mY', $date)){
            	$prev_next[] = $date;
            }
        }

        $calendar = $cache->put(calendar($_this['month'], $_this['year'], $events, $prev_next));
    }

return $calendar;
}

/**
 * $tpl это шаблон, в котором
 * {link} это ссылка,
 * {date} - дата,
 * {count} - количество постов в категории
 *
 * @param string $tpl
 * @param array $sort
 * @return string
 */
function cn_archives($tpl = '<a href="{link}">{date} ({count})</a><br />', $sort = array('date', 'DESC')){
global $PHP_SELF, $sql, $cache;
static $uniqid;

    if (!$archives = $cache->get('archives', $uniqid++)){
		foreach ($sql->select(array('table' => 'news', 'select' => array('date'), 'orderby' => $sort)) as $row){
			if ($arch != date('Y/m', $row['date'])){
	            $arch = date('Y/m', $row['date']);
				$find = array('{date}', '{link}', '{count}');
				$repl = array(_etc_lang(date('n', $row['date']), 'month').date(' Y', $row['date']), cute_get_link($row, 'month'), count_month_entry($row['date']));
				$archives .= str_replace($find, $repl, $tpl);
	        }
	    }

		$archives = $cache->put($archives);
   }

return $archives;
}

/**
 * @see category_get_tree()
 *
 * @param string $prefix Префикс
 * @param string $tpl Шаблон
 * @param bool $no_prefix Не использовать префикс для категорий, чей родитель 0 (верхний уровень)
 * @param int $level ID категории детей которой показывать
 * @return string Список категорий по шаблону
 */
function cn_category($prefix = '&nbsp;', $tpl = '<a href="[php]cute_get_link($row, category)[/php]">{name} ([php]count_category_entry({id})[/php])</a><br />', $no_prefix = true, $level = 0){
global $PHP_SELF, $cache;
static $uniqid;

    if (!$category = $cache->get('category', $uniqid++)){
    	$category = $cache->put(category_get_tree($prefix, $tpl, $no_prefix, $level));
    }

return $category;
}

/**
 *
 *
 * @param string $separator Разделитель
 * @param bool $reverse Показывать в обратном порядке
 * @return string Заголовки в указаном порядке
 */
function cn_title($separator = ' &raquo; ', $reverse = false, $type = 'title'){
global $sql, $_SERVER, $config, $cache, $users, $post;
static $uniqid;

	if (!$cn_title = $cache->get($type.'-'.str_replace(array('/', '?', '&', '='), '-', chicken_dick($_SERVER['REQUEST_URI'])), $uniqid++)){
        foreach ($_GET as $k => $v){
            $$k = @htmlspecialchars($v);
        }

	    $result[] = '<a href="'.$config['http_home_url'].'">'.$config['home_title'].'</a>';

        if ($category){
            if (!strstr($category, ',') and !is_numeric($category)){
                $category = category_get_id($category);
            }

            $title['category'] = explode($separator, category_get_title($category, $separator));
        }

        if ($user or $author){
            $user = ($user ? $user : $author);

            if (is_numeric($user)){
                foreach ($users as $row){
                    if ($row['id'] == $user){
                        $title['user'][]   = $row['name'];
                        $title['author'][] = $row['name'];
                    }
                }
            } else {
                $title['user'][]   = $users[$user]['name'];
                $title['author'][] = $users[$user]['name'];
            }
        }

        if ($year){
            $title['year'][] = $year;
        }

        if ($month){
            $f_num  = array('01', '02', '03', '04', '05', '06', '07', '07', '09', '10', '11', '12');
            $f_name = array('jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec');
            $replace = array(t('Январь'), t('Февраль'), t('Март'), t('Апрель'), t('Май'), t('Июнь'), t('Июль'), t('Август'), t('Сентябрь'), t('Октябрь'), t('Ноябрь'), t('Декабрь'));
            $title['month'][] = (is_numeric($month) ? str_replace($f_num, $replace, $month) : str_replace($f_name, $replace, $month));
        }

        if ($day){
            $title['day'][] = $day;
        }

	    if ($id){
	        $title['id'][] = replace_news('show', $post['title']);
	    }

	    foreach ($_GET as $k => $v){
	    	if (eregi('/', $v)){
	    		$v_arr = explode('/', $v);

	            for ($i = 0; $i < count($v_arr); $i++){
	            	$uri_tmp  .= $v_arr[$i].'/';
	                $uri[$k][] = chicken_dick($uri_tmp);
	            }
	    	}

	        if (eregi('&', $_SERVER['REQUEST_URI'])){
                $v_arr2 = explode('&', $v);

                for ($i = 0; $i < count($v_arr2); $i++){
                	$uri_tmp  .= $k.'='.$v_arr2[$i].'&';
                	$uri[$k][] = chicken_dick($uri_tmp, '&');
                }
	    	}
	    }

	    foreach ($_GET as $k => $row){
	    	foreach ((array)$title[$k] as $v){
	    		$array['title'][] = $v;
	    	}

	    	foreach ((array)$uri[$k] as $v){
	    		$array['uri'][] = $v;
	    	}
	    }

	    $home = cute_parse_url($config['http_home_url']);
	    $home = $home['scheme'].'://'.$home['host'].($home['port'] ? ':'.$home['port'] : '').($home['path'] ? '/'.$home['path'] : '').'/';
	     // eregi заменить потом нужно на что-нибудь быстрое
	    $home = $home.(eregi('&', $_SERVER['REQUEST_URI']) ? '?' : '');

	    for ($i = 0; $i < count($array['title']); $i++){
	    	$result[] = '<a href="'.$home.$array['uri'][$i].'">'.$array['title'][$i].'</a>';
	    }

	    $result[(count($result) - 1)] = strip_tags($result[(count($result) - 1)]);

	    $cn_title = join($separator, ($reverse ? array_reverse($result) : $result));
        $cn_title = $cache->put(($type == 'title' ? strip_tags($cn_title) : $cn_title));
	}

return $cn_title;
}

#-------------------------------------------------------------------------------

/**
 * @access private
 */
function count_month_entry($time){
global $sql;

	$fday   = strtotime(date('m/01/Y 00:00:01', $time));
	$lday   = strtotime(date('m/t/Y 23:59:59', $time));
    $result = $sql->count(array('table' => 'news', 'where' => array('date > '.$fday, 'and', 'date < '.$lday)));

return $result;
}

/**
 * @access private
 */
function count_category_entry($catid){
global $sql;

	$result = $sql->count(array('table' => 'news', 'where' => array('category ? ['.$catid.']')));

return $result;
}

/**
 * @access private
 */
function calendar($cal_month, $cal_year, $events, $prev_next){
global $year, $month, $day, $PHP_SELF;

    $first_of_month  = mktime(0, 0, 0, $cal_month, 7, $cal_year);
    $maxdays         = date('t', $first_of_month) + 1; // 28-31
    $cal_day         = 1;
    $weekday         = date('w', $first_of_month); // 0-6

    if (is_array($prev_next)){
	    sort($prev_next);

	    foreach ($prev_next as $key => $value){
	        if ($value < $first_of_month){
	        	$prev_of_month = $prev_next[$key];
	        }
	    }

	    rsort($prev_next);

	    foreach ($prev_next as $key => $value){
	        if ($value > $first_of_month){
	        	$next_of_month = $prev_next[$key];
	        }
	    }
    }

    if ($prev_of_month){
    	$tomonth['prev'] = '<a href="'.cute_get_link(array('date' => $prev_of_month), 'month').'" title="'._etc_lang(date('n', $prev_of_month), 'month').date(' Y', $prev_of_month).'">&laquo;</a> ';
    }

    if ($next_of_month){
    	$tomonth['next'] = ' <a href="'.cute_get_link(array('date' => $next_of_month), 'month').'" title="'._etc_lang(date('n', $next_of_month), 'month').date(' Y', $next_of_month).'">&raquo;</a>';
    }

    $buffer = '<table id="calendar">
    <tr>
     <td colspan="7" class="month">'.$tomonth['prev'].'<a href="'.cute_get_link(array('date' => $first_of_month), 'month').'" title="'._etc_lang(date('n', $first_of_month), 'month').date(' Y', $first_of_month).'">'._etc_lang(date('n', $first_of_month), 'month').' '.$cal_year.$tomonth['next'].'</a>
    <tr>
     <th class="weekday">'._etc_lang(1, 'weekday').'
     <th class="weekday">'._etc_lang(2, 'weekday').'
     <th class="weekday">'._etc_lang(3, 'weekday').'
     <th class="weekday">'._etc_lang(4, 'weekday').'
     <th class="weekday">'._etc_lang(5, 'weekday').'
     <th class="weekend">'._etc_lang(6, 'weekday').'
     <th class="weekend">'._etc_lang(7, 'weekday').'
    <tr>';

    if ($weekday > 0){
    	$buffer .= '<td colspan="'.$weekday.'">&nbsp;';
    }

    while ($maxdays > $cal_day){
        if ($weekday == 7){
            $buffer .= '<tr>';
            $weekday = 0;
        }

        # В данный день есть новость
        if ($events[$cal_day]){
            $date['title'] = langdate('l, d M Y', $events[$cal_day]);
            $link = cute_get_link(array('date' => $events[$cal_day]), 'day');

            if ($weekday == '5' or $weekday == '6'){ // Если суббота и воскресенье. Слава КПСС!!!
				if ($day == $cal_day){
					$buffer .= '<td class="weekend"><a href="'.$link.'" title="'.$date['title'].'"><b>'.$cal_day.'</b></a>';
               	} else {
               		$buffer .= '<td class="endday"><a href="'.$link.'" title="'.$date['title'].'">'.$cal_day.'</a>';
               	}
            } else { // Рабочии дни. Вперёд, стахановцы!!!
				if ($day == $cal_day){ // активный
					$buffer .= '<td class="weekday"><a href="'.$link.'" title="'.$date['title'].'"><b>'.$cal_day.'</b></a>';
				} else {  // пассивный, дурашка
					$buffer .= '<td class="day"><a href="'.$link.'" title="'.$date['title'].'">'.$cal_day.'</a>';
				}
            }
        } else { // В данный день новостей нет. Хуйовый день...
	        if ($weekday == '5' or $weekday == '6'){ // дни, когда по телеку нихуя нет :(
	            $buffer .= '<td class="endday">'.$cal_day;
	        } else { // работяги хлещат водку после труда
	        	$buffer .= '<td class="day">'.$cal_day;
	        }
        }

        $cal_day++;
        $weekday++;
    }

    if ($weekday != 7){
    	$buffer .= '<td colspan="'.(7 - $weekday).'">&nbsp;';
    }

return $buffer.'</table>';
}

/**
 * @access private
 */
function _etc_lang($num, $set){

    $lang = array(
    		'month'   => array(t('Январь'), t('Февраль'), t('Март'), t('Апрель'), t('Май'), t('Июнь'), t('Июль'), t('Август'), t('Сентябрь'), t('Октябрь'), t('Ноябрь'), t('Декабрь')),
    		'weekday' => array(t('Пн'), t('Вт'), t('Ср'), t('Чт'), t('Пт'), t('Сб'), t('Вс'))
    		);

return $lang[$set][($num - 1)];
}
?>