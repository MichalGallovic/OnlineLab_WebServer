<?php 

require_once('config.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

$experimentinterface_tpl = new rFastTemplate('includes/modules/experimentinterface/');
$experimentinterface_tpl->define(array("experimentinterface" => "experimentinterface.htm"));

$experimentinterface_tpl->assign(array("ROOT_PATH" => ROOT_PATH));

$equipment_id = 3;
//prioritne reg.
$permission = 0;
$controllers = get_ctrl($permission,$equipment_id);
foreach($controllers as $id => $ctr){
	$experimentinterface_tpl->assign(array("CTRL_ID" => $ctr['id'],"CTRL_NAME" => $ctr['name'] ));
	$experimentinterface_tpl->parse("PRIORITNE",".ctrl_priorit");
}

//verejne reg.
$permission = 1;
$controllers = get_ctrl($permission,$equipment_id);
foreach($controllers as $id => $ctr){
	$experimentinterface_tpl->assign(array("CTRL_ID" => $ctr['id'],"CTRL_NAME" => $ctr['name'] ));
	$experimentinterface_tpl->parse("PRIORITNE",".ctrl_public");
}

//vlastne reg.
$controllers = get_own_ctrl($_SESSION['user_id'],$equipment_id);
foreach($controllers as $id => $ctr){
	$experimentinterface_tpl->assign(array("CTRL_ID" => $ctr['id'],"CTRL_NAME" => $ctr['name'] ));
	$experimentinterface_tpl->parse("PRIORITNE",".ctrl_own");
}


$experimentinterface_tpl->parse("EXP_INTERFACE", ".experimentinterface");
$fetch_module = $experimentinterface_tpl->fetch("EXP_INTERFACE");

function get_experimentinterface_widget(){
	global $fetch_module;
	return $fetch_module;
	
}

?>