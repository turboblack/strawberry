<?php
/**
 * @package Private
 * @access private
 */

include '../../head.php';

$step = $_GET['step'];
$step = ($step ? $step : 1);

if ($step == 2){
	$config['database']   = 'mysql';
	$config['dbname']     = $_POST['dbname'];
	$config['dbuser']     = $_POST['dbuser'];
	$config['dbpassword'] = $_POST['dbpassword'];
	$config['dbprefix']   = $_POST['dbprefix'];
	$config['dbserver']   = $_POST['dbserver'];
}

if ($step == 4){
	header('Location: '.$config['http_script_dir'], true);
	exit;
}

if ($step != 1){
	include_once databases_directory.'/txtsql.class.php';
	$txtSQl = new txtSQL(data_directory.'/db');
	$txtSQl->connect('root', '');
	$txtSQl->selectdb('base');

    include_once databases_directory.'/mysql.class.php';
	$MySQL = new MySQL();
	$MySQL->connect($config['dbuser'], $config['dbpassword'], $config['dbserver']);
	$MySQL->selectdb($config['dbname'], $config['dbprefix']);
}

function insert($table){
global $txtSQl, $MySQL;

    if ($select = $txtSQl->select(array('table' => $table))){
	    foreach ($select as $row){
	        foreach ($row as $k => $v){
	            $values[$k] = $v;
	        }

	        $MySQL->insert(array(
	        'table'  => $table,
	        'values' => $values
	        ));
		}
	}
}
?>

<table width="200" border="0" cellspacing="0" cellpadding="0">
<form action="<?=$_SERVER['PHP_SELF']; ?>?step=<?=($step + 1); ?>" method="post">

<?
if ($step == 1){
?>

 <tr>
  <td colspan="2"><br /><br /><b><?=t('База данных'); ?></b>:
 <tr>
  <td><?=t('Логин БД'); ?>
  <td><input name="dbuser" type="text" value="">
 <tr>
  <td><?=t('Пароль БД'); ?>
  <td><input name="dbpassword" type="text" value="">
 <tr>
  <td><?=t('Сервер БД'); ?>
  <td><input name="dbserver" type="text" value="localhost">
 <tr>
  <td><?=t('Имя БД'); ?>
  <td><input name="dbname" type="text" value="">
 <tr>
  <td><?=t('Префикс таблиц'); ?>
  <td><input name="dbprefix" type="text" value="cute_">

<?
} elseif ($step == 2){
	include databases_directory.'/database.inc.php';

	foreach ($database as $k => $v){
	    if (!$MySQL->table_exists($k)){
	        $MySQL->createtable(array('table' => $k, 'columns' => $v));
	    }

	    if ($MySQL->table_exists($k)){
	        echo '<br><font color="green">'.t('Таблица "%table" создана.', array('table' => $k)).'</font>';
	    }
	}

	$save_config  = "<?\r\n";
	$save_config .= '$config = ';
	$save_config .= var_export($config, true);
	$save_config .= ";\r\n";
	$save_config .= '$allowed_extensions = array(\'gif\', \'jpg\', \'png\', \'bmp\', \'jpe\', \'jpeg\');';
	$save_config .= "\r\n?>";

	file_write(config_file, $save_config);
} elseif ($step == 3){
	include databases_directory.'/database.inc.php';

	foreach ($database as $k => $v){
		insert($k);
	}
}
?>

 <tr>
  <td colspan="2"><br /><br /><input type="submit" value="<?=t('Далее (шаг %step) &raquo;&raquo;', array('step' => (($step + 1) == 3 ? t('последний') : $step + 1))); ?>">
</form>
</table>