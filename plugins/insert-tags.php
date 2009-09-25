<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Insert Tags
Plugin URI:     http://cutenews.ru
Description:	Быстрые теги для добавления и редактирования новости.
Version:		1.0
Application: 	Strawberry
Author:			SwiZZeR
*/

add_filter('new-advanced-options', 'insert_tags', 1);
add_filter('edit-advanced-options', 'insert_tags', 1);

function insert_tags($location){
global $config;
?>

<div class="tags">
<a href="javascript:insertext('<br />','','<?=$location; ?>')" title='Перенос строки (Line break)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/br.gif border=0 align=middle></a>
<a href="javascript:insertext('<hr />','','<?=$location; ?>')" title='Линия (Line)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/hr.gif border=0 align=middle></a>
<a href="javascript:insertext('<p>','</p>','<?=$location; ?>')" title='Параграф (Paragraph)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/p.gif border=0 align=middle></a>
<a href="javascript:insertext('<b>','</b>','<?=$location; ?>')" title='Жирный (Bold)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/b.gif border=0 align=middle></a>
<a href="javascript:insertext('<i>','</i>','<?=$location; ?>')" title='Курсив (Italic)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/i.gif border=0 align=middle></a>
<a href="javascript:insertext('<u>','</u>','<?=$location; ?>')" title='Подчеркнутый (Underline)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/u.gif border=0 align=middle></a>
<a href="javascript:insertext('<s>','</s>','<?=$location; ?>')" title='Зачеркнутый (Strikethrough)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/s.gif border=0 align=middle></a>
<a href="javascript:insertext('<sub>','</sub>','<?=$location; ?>')" title='Подстрочный (Subscript)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/sub.gif border=0 align=middle></a>
<a href="javascript:insertext('<sup>','</sup>','<?=$location; ?>')" title='Надстрочный (Superscript)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/sup.gif border=0 align=middle></a>
<a href="javascript:insertext('<font color=&quot;&quot;>','</font>','<?=$location; ?>')" title='Цвет текста (Text color)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/color.gif border=0 align=middle></a>
<a href="javascript:insertext('<font size=&quot;&quot;>','</font>','<?=$location; ?>')" title='Размер шрифта (Font size)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/size.gif border=0 align=middle></a>
<a href="javascript:insertext('<ul>','</ul>','<?=$location; ?>')" title='Cписок ()'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/ul.gif border=0 align=middle></a>
<a href="javascript:insertext('<li>','</li>','<?=$location; ?>')" title='Элемент списка ()'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/li.gif border=0 align=middle></a>
<a href="javascript:insertext('<a href=&quot;&quot;>','</a>','<?=$location; ?>')" title='Ссылка (Link)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/url.gif border=0 align=middle></a>
<a href="javascript:insertext('<a href=&quot;mailto:&quot;>','</a>','<?=$location; ?>')" title='Email'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/mailto.gif border=0 align=middle></a>
<a href="#" onclick="window.open('<?=$PHP_SELF; ?>?mod=images&area=<?=$location; ?>', '_Addimage', 'height=450,resizable=yes,scrollbars=yes,width=500');return false;" target="_Addimage"><img src=<?=$config['http_script_dir']; ?>/plugins/tags/img.gif border=0 align=middle></a>
<a href="javascript:insertext('<div align=&quot;&quot;>','</div>','<?=$location; ?>')" title='Выравнивание (Align)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/align.gif border=0 align=middle></a>
<a href="#" onclick="window.open('<?=$config['http_script_dir']; ?>/plugins/tags/charmap.php?area=<?=$location; ?>', '_Charmap', 'height=240,resizable=yes,scrollbars=yes,width=570');return false;" target="_Charmap"><img src=<?=$config['http_script_dir']; ?>/plugins/tags/charmap.gif border=0 align=middle></a>
</div>

<?
return $location;
}
?>