<?php 

require_once('config.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

$controller_tpl = new rFastTemplate(MODUL_PATH);
$controller_tpl->define(array("controller" => "controller.htm"));
$controller_tpl->assign(array("ROOT_PATH" 					=> ROOT_PATH,
                                "ROWS_PER_PAGE" 				=> ROWS_PER_PAGE,
                                "NUMBER_OF_PAGE_DISPLAY" 		=> NUMBER_OF_PAGE_DISPLAY,
                                "CTRL_NAME" 					=> CTRL_NAME,
                                "CTRL_EQUIPMENT" 				=> CTRL_EQUIPMENT,
                                "CTRL_ACCESSIBILITY" 			=> CTRL_ACCESSIBILITY,
                                "CTRL_AUTHOR" 				=> CTRL_AUTHOR,
                                "TRASH_TITLE" 				=> TRASH_TITLE,
                                "PREVIEW_TITLE" 				=> PREVIEW_TITLE,
                                "NEW_CONTROLLER_TITLE" 		=> NEW_CONTROLLER_TITLE,
                                'CLOSE_WINDOW_TITLE' 			=> CLOSE_WINDOW_TITLE,
                                "DELETE_CONTROLLER_QUESTION"  => DELETE_CONTROLLER_QUESTION,
                                "BACK_TO_CONTROLLERS" 		=> BACK_TO_CONTROLLERS,
                                "CHANGE_CONTROLLER_SETTINGS"  => CHANGE_CONTROLLER_SETTINGS,
                                "LABEL_NAME_REGULATOR" 		=> LABEL_NAME_REGULATOR,
                                "LABEL_BODY_REGULATOR" 		=> LABEL_BODY_REGULATOR,
                                "LABEL_SYSTEM" 				=> LABEL_SYSTEM));

//get equipments
$equipments = get_plants();

foreach($equipments as $e){
	$controller_tpl->assign(array('PLANT_ID' => $e['id'],'PLANT_NAME' => $e['equipment_name']));	
	$controller_tpl->parse("PLATN_ROW", ".plant_row");	
}

$controller_tpl->parse("CONTROLLER", ".controller");
$fetch_module = $controller_tpl->fetch("CONTROLLER");

function get_controller(){
	global $fetch_module;
	return $fetch_module; 
}

?>