<?php 

require_once('config.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

$experiment_tpl = new rFastTemplate(MODUL_PATH);
$experiment_tpl->define(array("report" => "report.htm"));
$experiment_tpl->assign(array("ROOT_PATH" 					=> ROOT_PATH,
							  "ROWS_PER_PAGE" 				=> ROWS_PER_PAGE,
							  "NUMBER_OF_PAGE_DISPLAY" 		=> NUMBER_OF_PAGE_DISPLAY,
							  "MODUL_PATH" 					=> MODUL_PATH,
							  "ZOOM_BUTTON" 				=> ZOOM_BUTTON,
							  "STOP_BUTTON" 				=> STOP_BUTTON,
							  "CONTINUE_BUTTON" 			=> CONTINUE_BUTTON,
							  "BUTTON_REPORT_SETTINGS" 		=> BUTTON_REPORT_SETTINGS,
							  "MENU_BUTTON_TITLE" 			=> MENU_BUTTON_TITLE,
							  "GRAPH_SETTINGS" 				=> GRAPH_SETTINGS, 
							  "WINDOW_DIMENSIONS" 			=> WINDOW_DIMENSIONS, 
							  "GRAPH_WIDTH" 				=> GRAPH_WIDTH, 
							  "GRAPH_HEIGHT" 				=> GRAPH_HEIGHT,
							  "X_AXIS" 						=> X_AXIS, 
							  "Y_AXIS"						=> Y_AXIS, 
							  "X_AXIS_SHOW_TITLE" 			=> X_AXIS_SHOW_TITLE, 
							  "Y_AXIS_SHOW_TITLE" 			=> Y_AXIS_SHOW_TITLE,
							  "MAIN_TITLE" 					=> MAIN_TITLE, 
							  "MAIN_TITLE_SHOW" 			=> MAIN_TITLE_SHOW, 
							  "SUB_TITLE" 					=> SUB_TITLE, 
							  "SUB_TITLE_SHOW" 				=> SUB_TITLE_SHOW,
							  "MENU_AND_LEGEND_TITLE" 		=> MENU_AND_LEGEND_TITLE, 
							  "SHOW_LEGEND" 				=> SHOW_LEGEND, 
							  "SHOW_MENU" 					=> SHOW_MENU,
							  "SAVE_CHANGES_BUTTON" 		=> SAVE_CHANGES_BUTTON,
							  "BACK_TO_REPORTS_BUTTON" 		=> BACK_TO_REPORTS_BUTTON,
							  "PREVIOUS_REPORT_BUTTON" 		=> PREVIOUS_REPORT_BUTTON,
							  "NEXT_REPORT_BUTTON" 			=> NEXT_REPORT_BUTTON,
							  "REPORT_SETTINGS_SHOW_BOXES" 	=> REPORT_SETTINGS_SHOW_BOXES,
							  "OUTPUT_BOX" 					=> OUTPUT_BOX,
							  "EXPERIMENT_SETTINGS_BOX" 	=> EXPERIMENT_SETTINGS_BOX,
							  "PERSONAL_NOTES_BOX" 			=> PERSONAL_NOTES_BOX,
							  "RT_SYSTEM" 					=> RT_SYSTEM,
							  "RT_REGULATOR" 				=> RT_REGULATOR,
							  "RT_DATE" 					=> RT_DATE,
							  "RT_SHOW_REPORT_ICO" 			=> RT_SHOW_REPORT_ICO,
							  "INTENZITA_SERIE_LABEL" 		=> INTENZITA_SERIE_LABEL,
							  "INPUT_SERIE_LABEL" 			=> INPUT_SERIE_LABEL,
							  "TEMP_SERIE_LABEL" 			=> TEMP_SERIE_LABEL,
							  "FIL_TEMP_SERIE_LABEL" 		=> FIL_TEMP_SERIE_LABEL,
							  "FIL_LIGHT_INT_LABEL" 		=> FIL_LIGHT_INT_LABEL,
							  "CUR_SERIE_LABEL" 			=> CUR_SERIE_LABEL,
							  "RPM_SERIE_LABEL" 			=> RPM_SERIE_LABEL,
							  "EXP_EQUPMENT" 				=> EXP_EQUPMENT,
							  "EXP_BEGINING" 				=> EXP_BEGINING,
							  "EXP_REG" 					=> EXP_REG,
							  "EXP_REG_SETTINGS" 			=> EXP_REG_SETTINGS,
							  "EXP_REQUEST_VALUE" 			=> EXP_REQUEST_VALUE,
							  "EXP_SIMULATION_TIME" 		=> EXP_SIMULATION_TIME,
							  "EXP_SAMPLING_TIME" 			=> EXP_SAMPLING_TIME,
							  "EXP_PROCESS_VAR" 			=> EXP_PROCESS_VAR,
							  "EXP_REGULATORY_VAR" 			=> EXP_REGULATORY_VAR,
							  "EXP_VOLTAGE_LED" 			=> EXP_VOLTAGE_LED,
							  "EXP_VOLTAGE_MOTOR" 			=> EXP_VOLTAGE_MOTOR,
							  "EXP_VOLTAGE_LAMP" 			=> EXP_VOLTAGE_LAMP,
							  "PEROSNAL_NOTES" 				=> PEROSNAL_NOTES));

if(isset($_SESSION['update'] )){
	$experiment_tpl->parse("CHART_INFO_BOX",".chart_info_box");	
}


//ak je tu prvy krat vytvorime mu osobne nastavnie pre reporty => TABLE_REPORTS_SETTINGS
$mysql->query("SELECT COUNT(id) as count,chart_width,console_box,input_experiment_settings_box,personal_notes_box, chart_main_title_text, chart_subtitle_text, chart_height, chart_x_title, chart_y_title, chat_main_title, chart_subtitle, chart_legend,chart_menu
				FROM ".TABLE_REPORTS_SETTINGS." 
			   WHERE user_id = '".$_SESSION['user_id']."' GROUP BY id LIMIT 1   ");

$count = $mysql->result(0,'count');
$console_box = $mysql->result(0,'console_box');
$input_experiment_settings_box = $mysql->result(0,'input_experiment_settings_box');
$personal_notes_box = $mysql->result(0,'personal_notes_box');
$chat_main_title = $mysql->result(0,'chat_main_title');
$chart_main_title_text = $mysql->result(0,'chart_main_title_text');
$chart_subtitle = $mysql->result(0,'chart_subtitle');
$chart_subtitle_text = $mysql->result(0,'chart_subtitle_text');
$chart_height = $mysql->result(0,'chart_height');
$chart_width = $mysql->result(0,'chart_width');
$chart_x_title = $mysql->result(0,'chart_x_title');
$chart_y_title = $mysql->result(0,'chart_y_title');
$chart_legend = $mysql->result(0,'chart_legend');
$chart_menu = $mysql->result(0,'chart_menu');


if($count < 1){
	$mysql->query("INSERT INTO ".TABLE_REPORTS_SETTINGS." (user_id) VALUES ('".$_SESSION['user_id']."') ");
}else{
	//ak uz ma nastavnie, predvyplnime mu jeho checkboxy
	$experiment_tpl->assign(array(
								"CHECKBOX_CONSOLE" => $console_box == 1 ? 'checked="checked"' : '',
								"CHECKBOX_EXP_SETTINGS" => $input_experiment_settings_box == 1 ? 'checked="checked"' : '',
								"CHECKBOX_NOTES" => $personal_notes_box == 1 ? 'checked="checked"' : '',
								"CHART_MAIN_TITLE_VALUE" => $chart_main_title_text,
								"CHART_SUBTITLE_VALUE" => $chart_subtitle_text,
								"CHART_HEIGHT" => $chart_height,
								"CHART_WIDTH" => $chart_width,
								"CHART_CONTAINER_HEIGHT" => ($chart_menu == 1) ? $chart_height - 30 - 50  : $chart_height -30 ,
								"CHART_X_TITLE_CHECKBOX" => $chart_x_title == 1 ? 'checked="checked"' : '',
								"CHART_Y_TITLE_CHECKBOX" => $chart_y_title == 1 ? 'checked="checked"' : '',
								"CHART_MAIN_TITLE_CHECKBOX" => $chat_main_title == 1 ? 'checked="checked"' : '',
								"CHART_SUB_TITLE_CHECKBOX" => $chart_subtitle == 1 ? 'checked="checked"' : '',
								"CHART_LEGEND_CHECKBOX" => $chart_legend == 1 ? 'checked="checked"' : '',
								'CAHRT_LEGEND' => $chart_legend ,
								"CHART_MENU_CHECKBOX" => $chart_menu == 1 ? 'checked="checked"' : '',
								"CHART_MAIN_TITLE_SHOW" => $chat_main_title == 1 ? $chart_main_title_text : '',
								"CHART_SUBTITLE_SHOW" => $chart_subtitle == 1 ? $chart_subtitle_text : '',
								"CHART_X_TITLE_SHOW" => $chart_x_title == 1 ? 'Časová os' : '',
								"CHART_Y_TITLE_SHOW" => $chart_y_title == 1 ? 'Namerané hodnot' : '' ));
	if($chart_menu == 1)
		$experiment_tpl->parse("CHART_MENU",".chart_menu");
		
}


//akcie

if(isset($_POST['updateChartSettings'])){
	
	$chartWitdth = $mysql->escape(trim($_POST['chartWidth']));
	$chartHeight = $mysql->escape(trim($_POST['chartHeight']));
	$chartXdisplay = $mysql->escape(trim($_POST['chartXdisplay']));
	$chartYdisplay = $mysql->escape(trim($_POST['chartYdisplay']));
	$mainTitleShow = $mysql->escape(trim($_POST['mainTitleShow']));
	$mainTitleText = $mysql->escape(trim($_POST['mainTitleText']));
	$subTitleShow = $mysql->escape(trim($_POST['subTitleShow']));
	$subTitleText = $mysql->escape(trim($_POST['subTitleText']));
	$showLegend = $mysql->escape(trim($_POST['showLegend']));
	$showMenu = $mysql->escape(trim($_POST['showMenu']));
	
	$result = $mysql->query("UPDATE ".TABLE_REPORTS_SETTINGS." 
									SET
										chart_height =	    	'".$chartHeight."',
										chart_width = 			'".$chartWitdth."',
										chart_x_title =			'".$chartXdisplay."',
										chart_y_title = 		'".$chartYdisplay."', 
										chat_main_title = 		'".$mainTitleShow."',
										chart_main_title_text = '".$mainTitleText."',
										chart_subtitle = 		'".$subTitleShow."',
										chart_subtitle_text = 	'".$subTitleText."',
										chart_legend =	 		'".$showLegend."',
										chart_menu = 			'".$showMenu."'
										
								WHERE user_id = '".$_SESSION['user_id']."' ");
	
	if($result){
		$_SESSION['update'] = 1;
		header('Location: ?section_id=14');
		exit();
	}
}


if(isset($_SESSION['update'] ))
unset($_SESSION['update'] );

$experiment_tpl->parse("REPORT", ".report");
$fetch_module = $experiment_tpl->fetch("REPORT");

function get_report(){
	global $fetch_module;
	return $fetch_module; 
}

?>