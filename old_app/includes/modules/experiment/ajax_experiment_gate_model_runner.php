<?php
	/*include_once "../config.php";
	include_once "classes/class.plant.php";
	include_once "classes/class.user.php";
	include_once "classes/class.report.php";
	include_once "classes/class.reservations.php";*/
	
	require_once('../../config.php');
	require_once('../../db_tables.php');
	// initialize mysql class
	require_once('../../classes/mysql.php');
	$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
	require_once('../../functions/general.php');

	set_time_limit(0);

	session_start();
	
	$experiment_settings = array();
						  
	

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
	
	$experiment_settings['time'] = $time;
	$experiment_settings['input'] = $vstup;
	$experiment_settings['ts'] = $ts;
	$experiment_settings['out_value'] = $out_sw;
	$experiment_settings['in_value'] = $in_sw;
	$experiment_settings['c_lamp'] = $c_lamp;
	$experiment_settings['c_led'] = $c_led;
	$experiment_settings['c_fan'] = $c_fan;
	if($in_sw == 1) $experiment_settings['c_lamp'] = false;
	if($in_sw == 2) $experiment_settings['c_led'] = false;
	if($in_sw == 3) $experiment_settings['c_fan'] = false;
	
	//$experiment_settings['in_value'] =mb_detect_encoding($in_value_labels[1], "UTF-8") == "UTF-8" ? : $in_value_labels[1] = utf8_encode($in_value_labels[1]);
	
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


	$validate = security_eval($scifun);
	if($validate != "valid"){
		echo json_encode(array('error' => 1,'msg' => "Tha instruction <b>".$validate."</b> is blocked!"));
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
			return;
		}
	}else{
		echo json_encode(array('error' => 1,'msg' => 'You have no reservation!'));
		exit();
		return;
	}


	$_SESSION['terminate'] = 0;
	
	//$report = new Report();

	if( isReportOpenedByUser($_SESSION['user_id']) ){
		echo json_encode(array('error' => 1,'msg' => 'Please wait until your current experiment ends!'));
		exit();
		return;
	}

	
	
	$currentReportId = add_report(
						$_SESSION['user_id'],
						$_POST['plant_id'],
						'',
						$_POST['ctrl_typ'],
						($_POST['OWN'] == 0 ? "P = ".($_POST["P"]."; I = ".$_POST["I"]."; D = ".$_POST["D"]).";" : $_POST["scifun"]),
						$_SERVER['REMOTE_ADDR'],
						json_encode($experiment_settings)
						);
	
	$_SESSION['currentReportId'] = $currentReportId;

	session_commit();

	$ch = curl_init();
	foreach ( $_POST as $key => $value){
		$data[$key] = $value;
	}
	
	//tuto treba vypocitat zostavajuci cas z rezervacie
	$currentReservation = get_current_reservation($_POST['plant_id'],$_SESSION['user_id']);
	$now			= strtotime(date("Y-m-d H:i:s"));
	$end_of_reservation	= strtotime(date($currentReservation['end']));
	$data["count_down"] = ($end_of_reservation - $now);
	if($data["count_down"] < 0)
		$data["count_down"] = 0;
	
	//print_r($data);
	//curl_setopt($ch, CURLOPT_URL, "http://".$plant->ip.'/udaq/scripts/model_'.$plant->id.'.php');
	curl_setopt($ch, CURLOPT_URL, "http://".$plant['ip']."/udaq/models/model_runner_0.php");
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
	/*
	$report->setReport(
		$_SESSION['user']->id,
		$_POST['plant_id'],
		$_POST['OWN'],
		$_POST['own_ctrl_id'],
		($_POST['OWN'] == 0 ? "P = ".($_POST["P"]."; I = ".$_POST["I"]."; D = ".$_POST["D"]).";" : $_POST["scifun"]),
		$output[1],
		"simulation ended",
		"0"
	);
	*/
	
	end_report($currentReportId);
	
	echo json_encode(array('error' => 0, 'output' => "Koniec experimentu..." ));

// functions **********************************************************************************************************************

function security_eval($comm){
	if(strstr($comm, "system")){return "system";}
	if(strstr($comm, "host")){return "host";}
	if(strstr($comm, "unix")){return "unix";}
	if(strstr($comm, "unix_g")){return "unix_g";}
	if(strstr($comm, "unix_s")){return "unix_s";}
	if(strstr($comm, "unix_w")){return "unix_w";}
	if(strstr($comm, "unix_x")){return "unix_x";}
	if(strstr($comm, "addhistory")){return "addhistory";}
	if(strstr($comm, "gethistoryfile")){return "gethistoryfile";}
	if(strstr($comm, "historymanager")){return "historymanager";}
	if(strstr($comm, "removelinehistory")){return "removelinehistory";}
	if(strstr($comm, "loadhistory")){return "loadhistory";}
	if(strstr($comm, "resethistory")){return "resethistory";}
	if(strstr($comm, "saveafterncommands")){return "saveafterncommands";}
	if(strstr($comm, "saveconsecutivecommands")){return "saveconsecutivecommands";}
	if(strstr($comm, "savehistory")){return "savehistory";}
	if(strstr($comm, "sethistoryfile")){return "sethistoryfile";}
	if(strstr($comm, "call")){return "call";}
	if(strstr($comm, "fort")){return "fort";}
	if(strstr($comm, "exec")){return "exec";}
	if(strstr($comm, "evstr")){return "evstr";}
	if(strstr($comm, "execstr")){return "execstr";}
	if(strstr($comm, "feval")){return "feval";}
	if(strstr($comm, "chdir")){return "chdir";}
	if(strstr($comm, "copyfile")){return "copyfile";}
	if(strstr($comm, "deletedir")){return "deletedir";}
	if(strstr($comm, "createdir")){return "createdir";}
	if(strstr($comm, "dirname")){return "dirname";}
	if(strstr($comm, "dir")){return "dir";}
	if(strstr($comm, "listfiles")){return "listfiles";}
	if(strstr($comm, "dispfiles")){return "dispfiles";}
	if(strstr($comm, "findfiles")){return "findfiles";}
	if(strstr($comm, "ls ")){return "ls ";}
	if(strstr($comm, "mdelete")){return "mdelete";}
	if(strstr($comm, "mkdir")){return "mkdir";}
	if(strstr($comm, "mopen")){return "mopen";}
	if(strstr($comm, "movefile")){return "movefile";}
	if(strstr($comm, "pwd")){return "pwd";}
	if(strstr($comm, "rmdir")){return "rmdir";}
	if(strstr($comm, "TCL_EvalStr")){return "TCL_EvalStr";}
	if(strstr($comm, "TCL_EvalFile")){return "TCL_EvalFile";}
	if(strstr($comm, "bench_run")){return "bench_run";}
	if(strstr($comm, "tbx_build")){return "tbx_build";}
	if(strstr($comm, "pvm_")){return "pvm_";}
	if(strstr($comm, "manedit")){return "manedit";}
	return "valid";
}
?>