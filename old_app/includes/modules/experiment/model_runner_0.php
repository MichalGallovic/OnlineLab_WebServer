<?php
require_once('../../config.php');
require_once('../../db_tables.php');
// initialize mysql class
require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
require_once('../../functions/general.php');

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

set_time_limit(0);

session_start();


$error = false;	
$P	= ((isset($_POST['P'])		&& $_POST['P']!="")? $_POST['P']		: "undef");
$I	= ((isset($_POST['I'])		&& $_POST['I']!="")? $_POST['I']		: "undef");
$D	= ((isset($_POST['D'])		&& $_POST['D']!="")? $_POST['D']		: "undef");
$time	= ((isset($_POST['time'])	&& $_POST['time']!="")? $_POST['time']		: "undef");
$ts	= ((isset($_POST['ts'])		&& $_POST['ts']!="")? $_POST['ts']		: "undef");
$vstup	= ((isset($_POST['vstup'])	&& $_POST['vstup']!="")? $_POST['vstup']	: "undef");
$in_sw	= ((isset($_POST['in_sw'])	&& $_POST['in_sw']!="")? $_POST['in_sw']	: "undef");
$out_sw	= ((isset($_POST['out_sw'])	&& $_POST['out_sw']!="")? $_POST['out_sw']	: "undef");
$c_lamp	= ((isset($_POST['c_lamp'])	&& $_POST['c_lamp']!="")? $_POST['c_lamp']	: "undef");
$c_led	= ((isset($_POST['c_led'])	&& $_POST['c_led']!="")? $_POST['c_led']	: "undef");
$c_fan	= ((isset($_POST['c_fan'])	&& $_POST['c_fan']!="")? $_POST['c_fan']	: "undef");
$OWN	=((isset($_POST['OWN'])		&& $_POST['OWN']!="")? $_POST['OWN']		: "undef");
$scifun = ((isset($_POST['scifun'])	&& $_POST['scifun']!="")? $_POST['scifun']	: "undef");
$num_of_tanks = ((isset($_POST['num_of_tanks'])	&& $_POST['num_of_tanks']!="")? $_POST['num_of_tanks']	: "undef");

//test 1
	if(	$time 		== "undef" ||
		$ts 		== "undef" || 
		$vstup 		== "undef" || 
		$out_sw 	== "undef" || 
		$own_ctrl	== "undef" || 
		$scifun 	== "undef" || 
		$P 		== "undef" || 
		$I 		== "undef" || 
		$D 		== "undef"     )
	{
		$error = true;
	}
    
	//termo
	if($_POST['plant_id'] == 3)
	{
		if(	$in_sw	== "undef" ||
			$c_lamp == "undef" || 
			$c_led 	== "undef" || 
			$c_fan 	== "undef"   )
		{
			$error = true;
		}
	}
	
	//hydro
	if($_POST['plant_id'] == 1)
	{
		if(	$time 		== "undef" ||
			$ts 		== "undef" || 
			$vstup 		== "undef" || 
			$out_sw 	== "undef" || 
			$own_ctrl	== "undef" || 
			$scifun 	== "undef"     )
		{
			$error = true;
		}
	}
	if($error){
		echo json_encode(array('error' => 1,'msg' => 'All input data fields must be set!'));
		exit();
		return;
	}
	
	//kontrola zariadeniea
	if(!$plant = get_plant($_POST['plant_id'])){
		echo json_encode(array('error' => 1,"msg" => "Cannot find this plant with id:".$_POST['plant_id']." in database!"));
		exit();
		return;
	}
	
	
	
	//ci mam rezervaciu , resp. ci mam rezeravaciu pre dane zariadenie
	if( $currentReservation = get_current_reservation($_POST['plant_id'],$_SESSION['user_id']) ){
		
		if($plant['id'] != $currentReservation['equipment']){
			echo json_encode(array('error' => 1,'msg' => 'You have no reservation on this plant!'));
			exit();
			
		}
	}else{
		echo json_encode(array('error' => 1,'msg' => 'You have no reservation!'));
		exit();
		
	}
	
	
	$_SESSION['terminate'] = 0;
	/*
	if( $report->isReportOpenedByUser($_SESSION['user']->id) ){
		echo "<script type=\"text/javascript\">error_accoured=true;</script>";
		echo "Please wait until your current experiment ends!";
		return;
	}*/
	
	session_commit();

	$ch = curl_init();
	foreach ( $_POST as $key => $value){
		$data[$key] = $value;
	}
	
	
	$currentReservation = get_current_reservation($_POST['plant_id'],$_SESSION['user_id']);
	$now			= strtotime(date("Y-m-d H:i:s"));
	$end_of_reservation	= strtotime(date($currentReservation['end']));
	$data["count_down"] = ($end_of_reservation - $now);
	if($data["count_down"] < 0)
		$data["count_down"] = 0;
	
	
        curl_setopt($ch, CURLOPT_URL, "http://".$plant->ip."/udaq/models/experiment_change_input.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$output = curl_exec($ch);
	
	
	if(strstr($output,"404 Not Found")!=false){
		echo "<script type=\"text/javascript\">error_accoured=true;</script>";
		echo "Cannot find the server of the plant!<br />Contact the system administrator!";
		$output = array("","Experiment failed!");
	}else{
		$output = explode("output_data:",$output);
	}
	
	session_start();
	
	echo json_encode(array('error' => 0, 'output' => $output[0]." koniec experimentu...<br />" ));

?>