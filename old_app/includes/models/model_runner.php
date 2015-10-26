<?php

include_once "config_experiments.php";

set_time_limit(0);

$gate_session = $_POST['gate_session'];
session_id($gate_session);
session_start();

if($_POST['count_down'] == 0){
	$_SESSION['data_backup'] = "\nsimulate end";
	echo "\n<script type='text/javascript'>alert('You have no reservation currently!');</script>\n";
	echo "output_data:".$_SESSION['data_backup'];
	return 0;
}

$_SESSION['terminate'] = 0;

$params=
	"P=".$_POST["P"].
	";D=".$_POST["D"].
	";I=".$_POST["I"].
	";time=".$_POST["time"].
	";ts=".$_POST["ts"]."/1000".
	";vstup=".$_POST["vstup"].
	";in_sw=".$_POST["in_sw"].
	";out_sw=".$_POST["out_sw"].
	";c_lamp=".$_POST["c_lamp"].
	";c_led=".$_POST["c_led"].
	";c_fan=".$_POST["c_fan"].
	";own_ctrl=".$_POST["OWN"].
	";function y1=user_reg_func(u1),".$_POST["scifun"].";endfunction".
	";get_shm_command='cat /dev/shm/sess_".session_id()."_shm'".
	";exec ".DOC_ROOT."/termo/runner.sce".
	";";	// !!!nezabudnut na ";"


$descriptorspec = array(
	   0 => array("pipe", "r"),  					// stdin nepotrebujeme presmerovat do pipe
	   1 => array("pipe", "w"),  					// stdout presmerujeme do pipe
	   2 => array("file", DOC_ROOT."/log/error-output.log", "w") 	// stderr tiez presmerujeme do suboru
	);
$process = proc_open(DOC_ROOT.'/start_sci "'.$params.'" "'.SCI_HOME.'"', $descriptorspec, $pipes);



shell_exec("echo 0 > /dev/shm/sess_".session_id()."_shm");	// vytvorime subor v zdielanom pamati
$_SESSION["step"]=($_POST["ts"] * 1000);
//$_SESSION["step"]=100000;

$now = strtotime(date("Y-m-d H:i:s"));
$res_time_left = $now + $_POST['count_down'];

$_SESSION['data']="";
$_SESSION['data_backup']="";
$reply="";
if (is_resource($process)) {

	fclose($pipes[0]);
	while(($reply = fgets($pipes[1])) !== false){
	//while(strstr($reply,"simulate end")==false && strstr($reply,"Simulation problem")==false){
		//$reply = fgets($pipes[1]);
		$_SESSION['data'] = $reply;
		$_SESSION['data_backup'] .= $reply;
		$now = strtotime(date("Y-m-d H:i:s"));
		$time_left = $res_time_left - $now;
		if(strstr($reply,"error: plant not connected") != false){
			echo "<script type=\"text/javascript\">error_accoured=true;</script>".
			     "<p style='color:red; padding-left:20px;'>Plant is not connected or access to plant is denied!<br />Contact the system administrator!</p>";
			$_SESSION['data_backup'] .= "\nExperiment failed!";
			proc_terminate($process);
			shell_exec("killall -9 scilab-bin");
			return;
		}
		if($_SESSION['terminate'] || $time_left < 0){
			$_SESSION['data_backup'] .= "\nsimulate end";
			proc_terminate($process);
			shell_exec("killall -9 scilab-bin");
			sleep(2);
			echo shell_exec(DOC_ROOT."/termo/termo_set_default");
			break;
		}
		//echo $reply;
		session_commit();
		usleep($_SESSION["step"]);
		session_id($gate_session);
		session_start();
	}
	fclose($pipes[1]);
if($time_left < 0){
	echo "\n<script type='text/javascript'>alert('Time of your reservation ended!');</script>\n";
}
echo "output_data:".$_SESSION['data_backup'];

}
?>