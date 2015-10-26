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

foreach($data as $krivka => $hodnoty){
	$$krivka = $hodnoty;
}

$vystup  = '<?xml version="1.0"?>';
$vystup .= "<data>";
foreach($intenzita as $id => $suradnica){
	$vystup .= '<row>';
	$vystup .= '<input>'.$input[$id][1].'</input>';
	$vystup .= '<temp>'. $temperature[$id][1].'</temp>';
	$vystup .= '<ftemp>'.$f_temperature[$id][1].'</ftemp>';
	$vystup .= '<int>'.$intenzita[$id][1].'</int>';
	$vystup .= '<fint>'.$f_temperature[$id][1].'</fint>';
	$vystup .= '<current>'.$current[$id][1].'</current>';
	$vystup .= '<RPM>'.$rotaion[$id][1].'</RPM>';
	$vystup .= '<time>'.$input[$id][0].'</time>';
	/*$vystup .= $intenzita[$id][1].';'
			  .$f_intenzita[$id][1].';'
			  .$input[$id][1].';'
			  .$temperature[$id][1].';'
			  .$f_temperature[$id][1].';'
			  .$current[$id][1].';'
			  .$rotaion[$id][1].';'
			  .$input[$id][0];*/
	$vystup .= '</row>';
}
$vystup .= "</data>";

header ("content-type: text/xml");
echo $vystup;

?>