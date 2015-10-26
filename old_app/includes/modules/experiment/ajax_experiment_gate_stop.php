<?php
	
	require_once('../../config.php');
	require_once('../../db_tables.php');
	require_once('../../classes/mysql.php');
	$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
	require_once('../../functions/general.php');
	session_start();
	
	
	if(!$plant = get_plant($_POST['plant_id'])){
		echo "Cannot find this plant with id:'".$_POST['plant_id']."' in database!";
		return;
	}

	$ch = curl_init();
	
	$data = array('gate_session' => $_POST['gate_session']);
	
	//curl_setopt($ch, CURLOPT_URL, "http://".$plant->ip."/udaq/scripts/ajax_experiment_stop.php");
	curl_setopt($ch, CURLOPT_URL, "http://".$plant['ip']."/udaq/models/experiment_stop.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_exec($ch);
?>