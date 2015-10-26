<?php 
@session_start(); 


require_once('config.php');


//mysql
require_once('mysql.php');
$db_handler = new dbMysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);	


//
require_once('updateChartSettings.php');

?>
