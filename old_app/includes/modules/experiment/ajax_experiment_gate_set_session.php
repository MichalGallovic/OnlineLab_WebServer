<?php
	
	require_once('../../config.php');
	require_once('../../db_tables.php');
	require_once('../../classes/mysql.php');
	$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
	require_once('../../functions/general.php');
	
	session_start();

	if(!$plant = get_plant($_GET['plant_id'])){
		echo "Cannot find this plant with id:'".$_GET['plant_id']."' in database!";
		return;
	}


	//$_SESSION['gate_session_id'] = file_get_contents("http://".$plant->ip.'/udaq/models/experiment_get_session.php');
	$_SESSION['gate_session_id'] = file_get_contents("http://".$plant['ip']."/udaq/models/experiment_get_session.php");
	echo $_SESSION['gate_session_id'];
?>