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
$data = json_decode($data);
$data = (array) $data;
$equipment_name = $mysql->result(0,'equipment_name');

$vystup = "intenzita;f_intenzita;vstup;teplota;f_teplota;prud;RPM;time\x0D\x0A";
foreach($data as $krivka => $hodnoty){
	$$krivka = $hodnoty;
}

foreach($intenzita as $id => $suradnica){
	$vystup .= $intenzita[$id][1].';'
			  .$f_intenzita[$id][1].';'
			  .$input[$id][1].';'
			  .$temperature[$id][1].';'
			  .$f_temperature[$id][1].';'
			  .$current[$id][1].';'
			  .$rotaion[$id][1].';'
			  .$input[$id][0]."\r\n";
}


header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header('Content-Encoding: UTF-8');
header("Content-type:application/csv;charset=UTF-8");
header("Content-Disposition: attachment; filename=export_".$equipment_name."-".$reportId.".csv;");

echo $vystup;

?>