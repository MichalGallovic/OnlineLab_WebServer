<?php
	require_once('../../config.php');
	require_once('../../db_tables.php');
	require_once('../../classes/mysql.php');
	$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
	require_once('../../functions/general.php');
	session_start();
		
	
	$ctrl = get_own_ctrl($_POST['ctrl_id']);

	if($ctrl['user_id'] == $_SESSION['user_id'] || $ctrl['permissions'] == 1 || $ctrl['permissions'] == 0){
		echo $ctrl['body'];
	}else{
		echo "permission denied";
	}
	
	function get_own_ctrl($ctr_id){
		global $mysql;
		
		$mysql->query("SELECT * FROM ".TABLE_COTROLLERS." WHERE id=".$ctr_id." "); 
		return $mysql->fetch_array();
	}
	
?>