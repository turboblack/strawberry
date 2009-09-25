<?php
$skin_prefix = '';

if (!function_exists('options_submenu')){
	function options_submenu(){
	global $member;

	    ob_start();
	    include modules_directory.'/options.mdu';
	    $options = ob_get_contents();
	    ob_get_clean();

	    $options = strip_tags($options, '<a>');
	    $options = str_replace('&nbsp;', '', $options);
	    $options = explode("\r\n", $options);

	    foreach ($options as $option){
	        if ($option){
	            $result[] = $option;
	        }
	    }

	return @join('<br />', $result);
	}
}

ob_start();
?>

<td><a class="nav" href="<?=$PHP_SELF; ?>?mod=main"><?=t('Статистика'); ?></a></td>
<td>|</td>
<td><a class="nav" href="<?=$PHP_SELF; ?>?mod=addnews"><?=t('Добавить'); ?></a></td>
<td>|</td>
<td><a class="nav" href="<?=$PHP_SELF; ?>?mod=editnews"><?=t('Редактировать'); ?></a></td>
<td>|</td>
<td><a class="nav" href="<?=$PHP_SELF; ?>?mod=options"><?=t('Настройки'); ?></a> <?=makePlusMinus('options-submenu'); ?></td>
<td>|</td>
<td><a class="nav" href="<?=$PHP_SELF; ?>?mod=help"><?=t('Помощь'); ?></a></td>
<td>|</td>
<? /*if ($config['cache']){*/ ?>
<td><a class="nav" href="<?=$PHP_SELF; ?>?action=clearcache"><?=t('Очистить кэш'); ?></a></td>
<td>|</td>
<? /*}*/ ?>
<td><a class="nav" href="<?=$PHP_SELF; ?>?mod=logout"><?=t('Выход'); ?></a></td>
<td>|</td>
<td><a class="nav" href="<?=$config['http_home_url']; ?>"><?=t('На сайт'); ?></a></td>
<tr id="options-submenu" style="display: none;">
<td colspan="7"></td>
<td colspan="7"><?=options_submenu(); ?></td>

<?
$skin_menu = ob_get_clean();

ob_start();
?>

<!-- Зайцы инсайд -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
<meta http-equiv="content-type" content="text/html; charset=<?=$config['charset']; ?>">
<title><?=$config['home_title']; ?></title>
<link href="skins/default.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="skins/cute.js"></script>
<script type="text/javascript" src="skins/prototype.js"></script>
</head>

<body>

<table border="0" align="center" cellpadding="2" cellspacing="0">
<tr>
<td class="bborder">
<table border="0" cellpadding="0" cellspacing="0" width="700">
<tr>
<td align="center" height="24" class="main">
<table border="0" cellspacing="0" cellpadding="5">
<tr>
<td>{menu}</td>
</tr>
</table>
<tr>
<td height="19">
<table border="0" cellpading="0"cellspacing="15" width="100%" height="100%">
<tr>
<td><div class="header"><img border="0" src="skins/images/default/{image-name}.gif" align="absmiddle"> {header-text}</div></td>
<tr>
<td width="100%" height="100%">

<?
$skin_header = ob_get_clean();

ob_start();
?>

</td></tr>
</table>
</td>
</tr>
<tr>
<td height="24" align="center" class="copyrights">{copyrights}</td>
</tr>
</table>
</td>
</tr>
</table>

</body>
</html>

<?
$skin_footer = ob_get_clean();
?>