<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Insert Tags
Plugin URI:     http://cutenews.ru
Description:	������� ���� ��� ���������� � �������������� �������.
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
<a href="javascript:insertext('<br />','','<?=$location; ?>')" title='������� ������ (Line break)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/br.gif border=0 align=middle></a>
<a href="javascript:insertext('<hr />','','<?=$location; ?>')" title='����� (Line)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/hr.gif border=0 align=middle></a>
<a href="javascript:insertext('<p>','</p>','<?=$location; ?>')" title='�������� (Paragraph)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/p.gif border=0 align=middle></a>
<a href="javascript:insertext('<b>','</b>','<?=$location; ?>')" title='������ (Bold)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/b.gif border=0 align=middle></a>
<a href="javascript:insertext('<i>','</i>','<?=$location; ?>')" title='������ (Italic)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/i.gif border=0 align=middle></a>
<a href="javascript:insertext('<u>','</u>','<?=$location; ?>')" title='������������ (Underline)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/u.gif border=0 align=middle></a>
<a href="javascript:insertext('<s>','</s>','<?=$location; ?>')" title='����������� (Strikethrough)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/s.gif border=0 align=middle></a>
<a href="javascript:insertext('<sub>','</sub>','<?=$location; ?>')" title='����������� (Subscript)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/sub.gif border=0 align=middle></a>
<a href="javascript:insertext('<sup>','</sup>','<?=$location; ?>')" title='����������� (Superscript)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/sup.gif border=0 align=middle></a>
<a href="javascript:insertext('<font color=&quot;&quot;>','</font>','<?=$location; ?>')" title='���� ������ (Text color)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/color.gif border=0 align=middle></a>
<a href="javascript:insertext('<font size=&quot;&quot;>','</font>','<?=$location; ?>')" title='������ ������ (Font size)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/size.gif border=0 align=middle></a>
<a href="javascript:insertext('<ul>','</ul>','<?=$location; ?>')" title='C����� ()'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/ul.gif border=0 align=middle></a>
<a href="javascript:insertext('<li>','</li>','<?=$location; ?>')" title='������� ������ ()'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/li.gif border=0 align=middle></a>
<a href="javascript:insertext('<a href=&quot;&quot;>','</a>','<?=$location; ?>')" title='������ (Link)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/url.gif border=0 align=middle></a>
<a href="javascript:insertext('<a href=&quot;mailto:&quot;>','</a>','<?=$location; ?>')" title='Email'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/mailto.gif border=0 align=middle></a>
<a href="#" onclick="window.open('<?=$PHP_SELF; ?>?mod=images&area=<?=$location; ?>', '_Addimage', 'height=450,resizable=yes,scrollbars=yes,width=500');return false;" target="_Addimage"><img src=<?=$config['http_script_dir']; ?>/plugins/tags/img.gif border=0 align=middle></a>
<a href="javascript:insertext('<div align=&quot;&quot;>','</div>','<?=$location; ?>')" title='������������ (Align)'><img src=<?=$config['http_script_dir']; ?>/plugins/tags/align.gif border=0 align=middle></a>
<a href="#" onclick="window.open('<?=$config['http_script_dir']; ?>/plugins/tags/charmap.php?area=<?=$location; ?>', '_Charmap', 'height=240,resizable=yes,scrollbars=yes,width=570');return false;" target="_Charmap"><img src=<?=$config['http_script_dir']; ?>/plugins/tags/charmap.gif border=0 align=middle></a>
</div>

<?
return $location;
}
?>