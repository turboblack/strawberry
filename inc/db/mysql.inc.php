<?php
/**
 * @package Private
 * @access private
 */

include_once databases_directory.'/mysql.class.php';
$sql = new MySQL();
$sql->connect($config['dbuser'], $config['dbpassword'], $config['dbserver'], $config['dbcharset']);
$sql->selectdb($config['dbname'], $config['dbprefix']);
?>