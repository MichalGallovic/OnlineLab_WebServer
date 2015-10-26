<?php
session_start();

require_once('../../config.php');
require_once('../../db_tables.php');
require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
require_once('../../functions/general.php');



if(isset($_POST['saveReport']) and $_POST['saveReport'] ==1 ){
	$report_simulation_time = $_POST['report_simulation_time'];
	$consoleOutput = str_replace('&nbsp;','',$_POST['consoleOutput']);

	set_report_data($_SESSION['currentReportId'],$_POST['output'],$consoleOutput,$report_simulation_time);
}

if(isset($_POST['startReport']) and $_POST['startReport'] == 1 ){
	start_report($_SESSION['currentReportId']);
}

//var_dump($_SESSION['currentReportId']);



?>