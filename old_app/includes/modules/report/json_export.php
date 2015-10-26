<?php
// vystup databazy
error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', 1);

require_once('../../config.php');
require_once('../../db_tables.php');
require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);

$reportId = $_POST['reportId'];


$mysql->query('SELECT r.output,console,r.regulator_settings,r.regulator,e.equipment_name,r.report_date FROM '.TABLE_REPORTS.' r INNER JOIN '.TABLE_EQUIPMENT.' e ON (e.id = r.equipment_id) WHERE r.id=' . $reportId . ' ');
$data = $mysql->result(0,'output');
$equipment_name = $mysql->result(0,'equipment_name');

header('Content-Encoding: UTF-8');
header("Content-Type: plain/text");
header("Content-Disposition: Attachment; filename=json_export_".$equipment_name."-".$reportId.".txt");
header("Pragma: no-cache");


echo $data;

?>