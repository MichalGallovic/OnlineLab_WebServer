<?php
	$gate_session = $_POST['gate_session'];
        
        
        
	session_id($gate_session);
	@session_start();
	//echo session_id();
	//phpinfo();
	shell_exec("echo ".$_POST["zmena"]." > /dev/shm/sess_".session_id()."_shm");
	//echo shell_exec("cat /dev/shm/sess_".session_id()."_shm");

	echo "vykonaná";
?>