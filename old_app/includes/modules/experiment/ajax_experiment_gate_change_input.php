<?php
	#define('HOME_LINK', "http://".$_SERVER['HTTP_HOST']."/~jenis/udaq/index.php");
	#define('ROOT_LINK', "http://".$_SERVER['HTTP_HOST']."/~jenis/udaq");
        
        #define('HOME_LINK', "http://".$_SERVER['HTTP_HOST']."/~jenis/udaq/index.php");
	#define('ROOT_LINK', "http://".$_SERVER['HTTP_HOST']."/~jenis/udaq");
        
        echo "<b>HOME_LINK=".HOME_LINK."<br />ROOT_LINK=".ROOT_LINK."</b>";
	
	require_once('../../config.php');
	require_once('../../db_tables.php');
	require_once('../../classes/mysql.php');
	$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
	require_once('../../functions/general.php');

	session_start();

	if( !is_numeric($_POST['zmena']) ){
		echo "The given value is not a number!";
		return;
	}

	
	if(!$plant = get_plant($_POST['plant_id'])){
		echo "Cannot find this plant with id:'".$_POST['plant_id']."' in database!";
		return;
	}

	$ch = curl_init();
	
	$data = array(
		'gate_session'	=> $_POST['gate_session'],
		'zmena'		=> $_POST['zmena']
	);
	
	
	curl_setopt($ch, CURLOPT_URL, "http://".$plant->ip."/udaq/models/experiment_change_input.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_exec($ch);
?>
