<?php 
session_start();

require_once('../../config.php');
require_once('../../db_tables.php');
require_once('../../functions/general.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

//textova podoba stupna prava prioritny/verejny/privatny
$accessibility = array(0 => ACCES_PRIORITNY, 1 => ACCES_VEREJNY, 2 => ACCES_PRIVATNY );

require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);

/*if( isset($_SESSION['user_id'])){
	//var_dump(ROOT_PATH);
	header('Location: '.ROOT_PATH.' ');
	//exit;
}*/

//nahlad
if(isset($_POST['ctrlPreview']) and $_POST['ctrlPreview'] == 1){
	$ctrl_id = $_POST['regId'];
	
	$mysql->query("SELECT e.equipment_name, c.name,u.name as uname,u.surname,c.date,c.permissions ,c.body FROM  ".TABLE_COTROLLERS." c
				   INNER JOIN ".TABLE_ADMIN_USERS." u ON (c.user_id = u.id)
				   INNER JOIN ".TABLE_EQUIPMENT." e ON (c.equipment_id = e.id)  
				   WHERE c.id = ".$ctrl_id." ");
	
	$reg_name = $mysql->result(0,'name');
	$reg_author = $mysql->result(0,'uname').' '.$mysql->result(0,'surname');
	$reg_date = $mysql->result(0,'date');
	$reg_equipment_name = $mysql->result(0,'equipment_name');
	$permissions = $mysql->result(0,'permissions');
	$reg_permissions = $permissions == 1 ? YES : NO;
	$reg_body = $mysql->result(0,'body');
	
	echo json_encode(array(
							'ctrl_id'  				=> $ctrl_id,
							'reg_name' 				=> $reg_name,
							'reg_author' 			=> $reg_author,
							'reg_date' 				=> date('j.n.Y H:i:s',strtotime($reg_date)),
							'reg_equipment_name' 	=> $reg_equipment_name,
							'reg_permissions' 		=> $reg_permissions,
							'reg_body' 				=> $reg_body
							));
}

//formilar pre nastanie
if(isset($_POST['ctr_settings']) and $_POST['ctr_settings'] == 1){
	$ctrl_id = $_POST['regId'];
	
	$mysql->query("SELECT e.equipment_name,e.id as reg_equipment_id, c.name,u.name as uname,u.surname,c.date,c.permissions ,c.body FROM  ".TABLE_COTROLLERS." c
				   INNER JOIN ".TABLE_ADMIN_USERS." u ON (c.user_id = u.id)
				   INNER JOIN ".TABLE_EQUIPMENT." e ON (c.equipment_id = e.id)  
				   WHERE c.id = ".$ctrl_id." ");
	
	$reg_name = $mysql->result(0,'name');
	$reg_author = $mysql->result(0,'uname').' '.$mysql->result(0,'surname');
	$reg_date = $mysql->result(0,'date');
	$reg_equipment_name = $mysql->result(0,'equipment_name');
	$reg_equipment_id = $mysql->result(0,'reg_equipment_id');
	$permissions = $mysql->result(0,'permissions');
	$reg_permissions = $permissions;
	$reg_body = $mysql->result(0,'body');
	
	echo json_encode(array(
							'ctrl_id'  				=> $ctrl_id,
							'reg_name' 				=> $reg_name,
							'reg_author' 			=> $reg_author,
							'reg_date' 				=> date('j.n.Y H:i:s',strtotime($reg_date)),
							'reg_equipment_name' 	=> $reg_equipment_name,
							'reg_equipment_id' 		=> $reg_equipment_id,
							'reg_permissions' 		=> $reg_permissions,
							'reg_body' 				=> $reg_body
							));
}

//pridavanie regulatora
if(isset($_POST['add_regulator']) and $_POST['add_regulator'] == 1 ){
	$name = $mysql->escape($_POST['name']);
	$body = $mysql->escape($_POST['body']);
	$equipment_id = (int)$_POST['equipment_id'];
	$permissions = (int)$_POST['public'];
	
	foreach($_POST as $variableName => $value){
		if(empty($value))
			$empty_fields[] = $variableName;
		$post_variables[$variableName] = trim($value);	
	}
	
	if(!empty($empty_fields)){
		echo json_encode(array('status' => -1,'empty' => $empty_fields,'msg' => 'Vyplňte porsím vyznačené polia.'  ));
		exit();
	}
	
	$result = $mysql->query("INSERT INTO ".TABLE_COTROLLERS." (user_id,equipment_id,name,permissions,body,date) 
						VALUES(".$_SESSION['user_id'].",".$equipment_id.",'".$name."',".$permissions.",'".$body."',NOW()) ");
	$newRegId = $mysql->insert_id();
	$newReg = get_regulator($newRegId);
		
	
	if($result){
		echo json_encode(array('status' => 1,'reg' => $newReg,'msg' => 'Nový regulátor bol úspešne pridaný.'));
	}
}

//mazanie regulatora
if(isset($_POST['delete_reg']) and $_POST['delete_reg'] == 1){
	$ctrl_id = $_POST['regId'];
	$mysql->query("DELETE FROM ".TABLE_COTROLLERS." WHERE id = ".$ctrl_id." ");
}

//zmena nastaveni regulatora
if(isset($_POST['ctr_change_settings']) and $_POST['ctr_change_settings'] == 1){
	sleep(2);
	$ctrl_id 		= $_POST['ctrl_id'];
	$equipment_id 	= $_POST['equipment_id'];
	$permissions  	= $_POST['public'];
	$name  			= $mysql->escape($_POST['settings_reg_name']);
	$body  			= $mysql->escape($_POST['settings_reg_body']);
	
	$mysql->query("UPDATE ".TABLE_COTROLLERS." SET name='".$name."', body = '".$body."',equipment_id = '".$equipment_id."',permissions = '".$permissions."'  WHERE id= ".$ctrl_id." ");
	//var_dump("UPDATE ".TABLE_COTROLLERS." SET name='".$name."', body = '".$body."',equipment_id = '".$equipment_id."',permissions = '".$permissions."'  WHERE id= ".$ctrl_id." ");
        
	echo json_encode(array('status' => 1,'msg' => 'Nastavenia boli uložené.'));
}


?>