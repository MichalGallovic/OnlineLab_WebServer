<?php 
session_start();

require_once('../../config.php');
require_once('../../db_tables.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);


if(isset($_POST['getChartSetting'])){

$html = '<div class="panel">
			<div class="title-bar">
				<h3>Nastavenie vlastností grafu</h3>
				<a class="close-panel-btn" title="zavrieť okno">X</a>
				<a href="#" onclick="open_menu()" title="otvoriť do nového okna" class="new-window-btn" ></a>
			</div>
		 </div>'; 

echo json_encode(array("html"=>$html)); 
exit();
}


if(isset($_POST['action']) and $_POST['action'] == "getLastReport"){
	
	
	$mysql->query('SELECT r.output,rs.console_box,rs.input_experiment_settings_box,rs.personal_notes_box , console,r.notes,r.experiment_settings,r.report_simulation_time,r.regulator_settings,r.regulator,e.equipment_name,r.report_date 
						FROM '.TABLE_REPORTS.' r 
						INNER JOIN '.TABLE_EQUIPMENT.' e ON (e.id = r.equipment_id) 
						INNER JOIN '.TABLE_REPORTS_SETTINGS.' rs ON (rs.user_id = r.user_id)
					WHERE r.user_id = '.$_SESSION['user_id'].' ORDER BY r.id DESC ');
	
	$data = $mysql->result(0,'output');
		
	$data = json_decode($data);
	$data = (array) $data;
		
	echo json_encode(array('chartData' => $data,
						   'title' => LAST_MEASURMENT_APPEND,  
						   'chartIntro' => LIVECHART_YOUR_MEASURMENTS ));
	
	//echo $data;
	
}

?>