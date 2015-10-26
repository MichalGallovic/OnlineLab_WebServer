<?php 
@session_start(); 
require_once('config.php');

date_default_timezone_set('UTC');


//mysql
require_once('mysql.php');
$reservation_mysql = new dbMysqlClass(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);	



?>