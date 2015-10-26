<?php 

// cesta z rootu do priecinku modulu
define("MODUL_PATH", "includes/modules/experiment/");
define('EXPERIMENT_MODUL_NAME','experiment');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

$experiment_tpl = new rFastTemplate(MODUL_PATH);
$experiment_tpl->define(array("product" => "experiment.htm"));

$experiment_tpl->assign(array("EXPERIMENT_EQUIPMENT" => EXPERIMENT_EQUIPMENT,
			   				  "EXP_RESERVATION" => EXP_RESERVATION,
			   				  "EXP_ACCESSIBILITY" => EXP_ACCESSIBILITY));

//nasjkor si vytiahnem samotne zariadenia
$equipmentQuery = $mysql->query("SELECT * FROM ".TABLE_EQUIPMENT." ORDER BY equipment_name ");

$rowCounter = 1;
while($equpiment = $mysql->fetch_array($equipmentQuery)){
	
	//vytihname najblizsiu rezervaciu pre kazde zaraidenie
	$reservationForEquipQuery = $mysql->query("SELECT * FROM ".TABLE_RESERVATION." WHERE equipment = '".$equpiment['id']."' AND user_id = '".(int) $_SESSION['user_id']."' ORDER BY start ASC LIMIT 1  ");
	
	//ak nemam zadinu rezervaciu pre dane zariadenie
	if($mysql->num_rows($reservationForEquipQuery) < 1){
		$experiment_tpl->assign(array("EXPERIMENT_AVAIBILITY" =>  "<a href='".ROOT_PATH.$g_modules_array['reservation']['access_key']."' title='Pridať rezerváciu pre toto zariadenie'>Pridať rezerváciu </a>"));
		
	}else{
		$reservation = $mysql->fetch_array($reservationForEquipQuery);
		$experiment_tpl->assign(array("EXPERIMENT_AVAIBILITY" => date("j.n Y G:i", strtotime($reservation['start']))." - ".date("j.n Y G:i", strtotime($reservation['end'])) ));
	
	}
	
	$experiment_tpl->assign(array("EQUIPMENT_NAME" => $equpiment['equipment_name'],
								  "EXP_ROW_CLASS" => ($rowCounter % 2 == 0)  ? 'uneven' : 'even',
								  "AVAIBILITY_CLASS" => check_avaibility($equpiment['id']) ? '<a class="avaible" rel="avaible-for-'.$equpiment['equipment_name'].'" title="" href="#">Spusti experiment</a>' : '<a title="" href="#" class="unavaible"></a>' ));
	
	$experiment_tpl->parse("EQUIP_ROW",".equipment_row");
	$rowCounter++;	
}

$equipment_id = 3;
//prioritne reg.
$permission = 0;
$controllers = get_ctrl($permission,$equipment_id);
foreach($controllers as $id => $ctr){
	$experiment_tpl->assign(array("CTRL_ID" => $ctr['id'],"CTRL_NAME" => $ctr['name'] ));
	$experiment_tpl->parse("PRIORITNE",".ctrl_priorit");
}

//verejne reg.
$permission = 1;
$controllers = get_ctrl($permission,$equipment_id);
foreach($controllers as $id => $ctr){
	$experiment_tpl->assign(array("CTRL_ID" => $ctr['id'],"CTRL_NAME" => $ctr['name'] ));
	$experiment_tpl->parse("PRIORITNE",".ctrl_public");
}

//vlastne reg.
$controllers = get_own_ctrl($_SESSION['user_id'],$equipment_id);
foreach($controllers as $id => $ctr){
	$experiment_tpl->assign(array("CTRL_ID" => $ctr['id'],"CTRL_NAME" => $ctr['name'] ));
	$experiment_tpl->parse("PRIORITNE",".ctrl_own");
}

$experiment_tpl->parse("PRODUKT", ".product");
$fetch_module = $experiment_tpl->fetch("PRODUKT");


/*functions */

//zisti ci mozme spusti experiemnt na danom zariadeni
function check_avaibility($id_equip){
	global $mysql;
	
	$mysql->query("SELECT * FROM ".TABLE_RESERVATION." WHERE equipment = '".$id_equip."' AND user_id = '".(int) $_SESSION['user_id']."' AND start <= NOW() AND end >= NOW() ORDER BY start ASC LIMIT 1  ");
	if($mysql->num_rows() < 1){
		return false;
	}else
		return true;
}

function get_ctrl($permissions = 0,$equipment_id = 1){
	global $mysql;
	$controllers  = array();
	
	$mysql->query("SELECT * FROM ".TABLE_COTROLLERS." WHERE permissions = ".$permissions." and equipment_id = ".$equipment_id." ");
	while($row = $mysql->fetch_array()){
		$controllers[] = $row;
	}
    
	return $controllers; 
}

function get_own_ctrl($user_id,$equipment_id = 1){
	global $mysql;
	$controllers = array();
	
	$mysql->query("SELECT * FROM ".TABLE_COTROLLERS." WHERE permissions = 2 AND user_id = ".$user_id." and equipment_id = ".$equipment_id." ");
	while($row = $mysql->fetch_array()){
		$controllers[] = $row;
	}
	
	return $controllers;
}

/*
-finalna funkcia modulu pre widget get_/nazovamodulu/_widget
*/
function get_experiment_widget(){
	global $fetch_module;
	$HTMLresponse = '';
	
	//$currentDirectory = array_pop(explode("/", getcwd()));
	$currentSrc = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
	$currentSrc = ltrim(preg_replace('/\\\\/', '/', $currentSrc), '/');
	$currentSrcArray = explode("/",$currentSrc); 
	array_pop($currentSrcArray);
	$currentSrc = '/'.implode("/",$currentSrcArray);
	
	$HTMLresponse =  '<link href="'.FULL_MODULES_PATH.EXPERIMENT_MODUL_NAME.'/css/default.css" rel="stylesheet" type="text/css" />';	
	/*$HTMLresponse .= '<script src="'.FULL_MODULES_PATH.EXPERIMENT_MODUL_NAME.'/js/default.js" type="text/javascript" /></script>';*/	
	$HTMLresponse .= '<script src="'.FULL_MODULES_PATH.EXPERIMENT_MODUL_NAME.'/js/experiment_0.js" type="text/javascript" /></script>';	
	
	
	$HTMLresponse .= $fetch_module;
	
	return $HTMLresponse;
}

?>
