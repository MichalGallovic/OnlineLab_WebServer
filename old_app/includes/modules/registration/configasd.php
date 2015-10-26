<?php 


/*DATABES SETTINGS */
$dbServer           = DB_SERVER;
$dbName             = DB_DATABASE;
$dbServerUsername   = DB_SERVER_USERNAME;
$dbServerPassword   = DB_SERVER_PASSWORD;
$dbEncoding         = DB_ENCODING;

$DBconnection = mysql_connect($dbServer, $dbServerUsername, $dbServerPassword);
mysql_select_db($dbName, $DBconnection);
mysql_query("SET NAMES '".$dbEncoding."' COLLATE '".$dbEncoding."_general_ci'");


define('REGISTRATION_MODUL_NAME','registration');



?>
