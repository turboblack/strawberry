<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	��������
Plugin URI:		http://spectator.ru/technology/php/quotation_marks_stike_back
Description:	����������� �������.
Version:		1.0
Application: 	Strawberry
Author:			������� �������
Author URI:		http://nudnik.ru
*/

add_filter('news-entry-content','kavychker', 2000);
add_filter('news-comment-content','kavychker', 2000);

function kavychker($content){ // ����������� ���������
// Copyright (c) Spectator.ru

	$content = stripslashes($content);

	// ������ ������� � html-����� �� ������ "�"
	$content=preg_replace ( "/<([^>]*)>/es", "'<'.str_replace ('\\\"', '�','\\1').'>'", $content);

	// ������ ������� ������ <code> �� ������ "�"
	$content=preg_replace ( "/<code>(.*?)<\/code>/es", "'<code>'.str_replace ('\\\"', '�','\\1').'</code>'", $content);
	$content=preg_replace ( "/<code>(.*?)<\/code>/es", "'<code>'.str_replace ('\\\'', '*�','\\1').'</code>'", $content);

	// ����������� �������: �������, ����� ������� ���� ( ��� > ��� ������ = ������ �����,
	// �������, ����� ������� �� ���� ������ = ��� ����� �����.
	$content=preg_replace ( "[&quot;]", '"', $content);
	$content=preg_replace ( "/([>(\s])(\")([^\"]*)([^\s\"(])(\")/", "\\1�\\3\\4�", $content);

	// ���, �������� � ������ �������������� �������? ������ ���� ���������!
	if (stristr ($content, '"')):

	// ����������� ���������� ������� (��� ���).
	$content=preg_replace ( "/([>(\s])(\")([^\"]*)([^\s\"(])(\")/", "\\1�\\3\\4�", $content);

	// ����������� ��������� �������
	// �����: ���������� �� ������ ���� ������ ������������� ������� ��� �����������
	// ������, ������ ������� - ���������. ������ �� � ������ ����� ���, �� ��������� (132 � 147)
	 while (preg_match ("/(�)([^�]*)(�)/", $content)) $content=preg_replace ( "/(�)([^�]*)(�)([^�]*)(�)/", "\\1\\2&#132;\\4&#147;", $content);

	// ����� ��������� ��������
	endif;

	// ������� �������
	$content = preg_replace("/\<a\s+href([^>]*)\>\s*\�([^<^�^�]*)\�\s*\<\/a\>/", "&#171;<a href\\1>\\2</a>&#187;", $content);

	// ��������������� ���������� ����, ���� � ����������

	$trans = array
	(
		"\xAB" => '&laquo;',
		"\xBB" => '&raquo;',
		"\x93" => '&bdquo;',
		"\x94" => '&ldquo;',
		'...'  => '&hellip;',
		'(c)'  => '&copy;',
		'(C)'  => '&copy;',
		'(r)'  => '&reg;',
		'(R)'  => '&reg;',
		'(tm)' => '&trade;',
		'(TM)' => '&trade;',
		"'"    => '&#146;' #��������
	);
	$content = strtr ($content, $trans);

	// ���� � ������ ������ (�������)
	$content = preg_replace ('/([>|\s])- /',"\\1&#151; ", $content);

	// ������  "�" ������� �� �������
	$content = str_replace ('*�','\'', $content);
	$content = str_replace ('�','"', $content);

	// �������� ������ �� ������ (����� �� ����������� �� ������ ������ �������� �� ��������)
	 $content = preg_replace("/ (.) (.)/", " \\1&nbsp;\\2", $content);

	// ������
	$content = preg_replace("/(\s[^- >]*)-([^ - >]*\s)/", "\\1-\\2", $content);

	$content = addslashes($content);

return $content;
}
?>
