<?php 
session_start();

require_once('../../config.php');
require_once('../../db_tables.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);

	$out_value_labels = array(1 => OUT_VAL_TEMP,
							  2 => OUT_VAL_FIL_TEMP,
							  3 => OUT_VAL_LIGHT_INT,
							  4 => OUT_VAL_FIL_LIGHT_INT,
							  5 => OUT_VAL_CURRENT,
							  6 => OUT_VAL_FAN);
	
	$in_value_labels = array( 1 => IN_VAL_VOLTAGE_LED,
							  2 => IN_VAL_VOLTAGE_LED,
							  3 => IN_VAL_VOLTAGE_LAMP);	

if(isset($_POST['getReport']) and $_POST['getReport'] == 1){
	$reportId = (int)$_POST['reportId'];
	$reporIdNext = $reportId+1;
	$mysql->query('SELECT id FROM '.TABLE_REPORTS.' WHERE id < '.$reportId.' AND user_id = '.$_SESSION['user_id'].' ORDER BY id DESC LIMIT 1 ');
	$previousReportId = $mysql->result(0,'id');
	
	$mysql->query('SELECT id FROM '.TABLE_REPORTS.' WHERE id > '.$reportId.' AND user_id = '.$_SESSION['user_id'].' ORDER BY id ASC LIMIT 1 ');
	$nextReportId = $mysql->result(0,'id');
	
	$mysql->query('SELECT r.output,rs.console_box,rs.input_experiment_settings_box,rs.personal_notes_box , console,r.notes,r.experiment_settings,r.report_simulation_time,r.regulator_settings,r.regulator,e.equipment_name,r.report_date 
						FROM '.TABLE_REPORTS.' r 
						INNER JOIN '.TABLE_EQUIPMENT.' e ON (e.id = r.equipment_id) 
						INNER JOIN '.TABLE_REPORTS_SETTINGS.' rs ON (rs.user_id = r.user_id)
					WHERE r.id=' . $reportId . ' AND r.user_id = '.$_SESSION['user_id'].' ');
	
	$data = $mysql->result(0,'output');
	$console = $mysql->result(0,'console');
	$regulator = $mysql->result(0,'regulator');
	$regulator_settings = $mysql->result(0,'regulator_settings');
	$equipment_name = $mysql->result(0,'equipment_name');
	$report_simulation_time = $mysql->result(0,'report_simulation_time');
	$notes = str_replace('<br />','' ,$mysql->result(0,'notes'));
	
	$report_date = $mysql->result(0,'report_date');
	$report_date = date('j.n.Y H:i:s',strtotime($report_date) );
	
	$experiment_settings = $mysql->result(0,'experiment_settings');
	$experiment_settings = json_decode($experiment_settings);
	$experiment_settings = (array)$experiment_settings;
	
	$experiment_settings['in_value'] = $in_value_labels[$experiment_settings['in_value']];
	$experiment_settings['out_value'] = $out_value_labels[$experiment_settings['out_value']];
	
	$data = json_decode($data);
	$data = (array) $data;
	
	if($regulator == "PID"){
		$regSettings = explode(';',$regulator_settings);
		$formattedRegSettings = implode('<br />',$regSettings);
	}else{
		$formattedRegSettings = $regulator_settings;
	}
	
	$console_box  = $mysql->result(0,'console_box');
	$input_experiment_settings_box   = $mysql->result(0,'input_experiment_settings_box');
	$personal_notes_box = $mysql->result(0,'personal_notes_box');
	$box_settings = array("console_box" => $console_box,
						  "input_experiment_settings_box" => $input_experiment_settings_box,
						  "personal_notes_box" => $personal_notes_box);						  
	
	echo json_encode(array('chartData' => $data,
						   'reportId' => $reportId,
						   'console' => $console,
						   'regulator' => $regulator, 
						   'regulator_settings' => $formattedRegSettings,
						   'equipment_name' => $equipment_name,
						   'report_date' => $report_date,
						   'report_simulation_time' => $report_simulation_time,
						   'notes' => $notes,
						   'nextReport' => $nextReportId,
						   'previousReport' => $previousReportId,
						   'experiment_settings' => $experiment_settings,
						   'box_settings' => $box_settings));
	
	//echo $data;
}

if(isset($_POST['showReport']) and $_POST['showReport'] == 1){
	$reportId = (int)$_POST['reportId'];
	$reporIdNext = $reportId+1;
	$mysql->query('SELECT id FROM '.TABLE_REPORTS.' WHERE id < '.$reportId.' AND user_id = '.$_SESSION['user_id'].' ORDER BY id DESC LIMIT 1 ');
	$previousReportId = $mysql->result(0,'id');
	
	$mysql->query('SELECT id FROM '.TABLE_REPORTS.' WHERE id > '.$reportId.' AND user_id = '.$_SESSION['user_id'].' ORDER BY id ASC LIMIT 1 ');
	$nextReportId = $mysql->result(0,'id');
	
	$mysql->query('SELECT r.output,rs.console_box,rs.input_experiment_settings_box,rs.personal_notes_box , console,r.notes,r.experiment_settings,r.report_simulation_time,r.regulator_settings,r.regulator,e.equipment_name,r.report_date 
						FROM '.TABLE_REPORTS.' r 
						INNER JOIN '.TABLE_EQUIPMENT.' e ON (e.id = r.equipment_id) 
						INNER JOIN '.TABLE_REPORTS_SETTINGS.' rs ON (rs.user_id = r.user_id)
					WHERE r.id=' . $reportId . ' AND r.user_id = '.$_SESSION['user_id'].' ');
	
	$data = $mysql->result(0,'output');
	$console = $mysql->result(0,'console');
	$regulator = $mysql->result(0,'regulator');
	$regulator_settings = $mysql->result(0,'regulator_settings');
	$equipment_name = $mysql->result(0,'equipment_name');
	$report_simulation_time = $mysql->result(0,'report_simulation_time');
	$notes = str_replace('<br />','' ,$mysql->result(0,'notes'));
	
	$report_date = $mysql->result(0,'report_date');
	$report_date = date('j.n.Y H:i:s',strtotime($report_date) );
	
	$experiment_settings = $mysql->result(0,'experiment_settings');
	$experiment_settings = json_decode($experiment_settings);
	$experiment_settings = (array)$experiment_settings;
	
	$experiment_settings['in_value'] = $in_value_labels[$experiment_settings['in_value']];
	$experiment_settings['out_value'] = $out_value_labels[$experiment_settings['out_value']];
	
	$data = json_decode($data);
	$data = (array) $data;
	
	if($regulator == "PID"){
		$regSettings = explode(';',$regulator_settings);
		$formattedRegSettings = implode('<br />',$regSettings);
	}else{
		$formattedRegSettings = $regulator_settings;
	}
	
	$console_box  = $mysql->result(0,'console_box');
	$input_experiment_settings_box   = $mysql->result(0,'input_experiment_settings_box');
	$personal_notes_box = $mysql->result(0,'personal_notes_box');
	$box_settings = array("console_box" => $console_box,
						  "input_experiment_settings_box" => $input_experiment_settings_box,
						  "personal_notes_box" => $personal_notes_box);				
							  
	usleep(300000);
	echo json_encode(array('chartData' => $data,
						   'reportId' => $reportId,
						   'console' => $console,
						   'regulator' => $regulator, 
						   'regulator_settings' => $formattedRegSettings,
						   'equipment_name' => $equipment_name,
						   'report_date' => $report_date,
						   'report_simulation_time' => $report_simulation_time,
						   'notes' => $notes,
						   'nextReport' => $nextReportId,
						   'previousReport' => $previousReportId,
						   'experiment_settings' => $experiment_settings,
						   'box_settings' => $box_settings));
	
	//echo $data;
}


if(isset($_POST['saveNotes']) and $_POST['saveNotes'] == 1){
	$report_id = (int)$_POST['report_id'];
	$mysql->query("UPDATE ".TABLE_REPORTS." SET notes = '".nl2br($_POST['notes'])."' WHERE id = ".$report_id." ");
}

if(isset($_POST['setReportBox']) and $_POST['setReportBox'] == 1){
	$boxField = $_POST['box'];
	$mysql->query('UPDATE '.TABLE_REPORTS_SETTINGS.' SET '.$boxField.' = 1 WHERE user_id = "'. $_SESSION['user_id'] .'"  ');
}

if(isset($_POST['unsetReportBox']) and $_POST['unsetReportBox'] == 1){
	$boxField = $_POST['box'];
	$mysql->query('UPDATE '.TABLE_REPORTS_SETTINGS.' SET '.$boxField.' = 0 WHERE user_id = "'. $_SESSION['user_id'] .'"  ');

}

?>