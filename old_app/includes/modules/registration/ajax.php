<?php
session_start();

require_once('../../config.php'); 
require_once('config.php');
require_once('../../db_tables.php');


function register_create_pass($pass, $salt) {
  	return md5(md5($pass).$salt);
}

function is_uniq_login($login){
	global $DBconnection;
	
	
	$sql = "SELECT login FROM ".TABLE_ADMIN_USERS." WHERE login LIKE '". mysql_real_escape_string($login) ."' ";
	
	$queryRSL = mysql_query($sql, $DBconnection);
	$find =  mysql_num_rows($queryRSL);
	if($find > 0)
		return false;
	else 
		return true;	
}

function is_uniq_email($email){
	global $DBconnection;
	
	$sql = "SELECT login FROM ".TABLE_ADMIN_USERS." WHERE email LIKE '". mysql_real_escape_string($email) ."' ";
	$queryRSL = mysql_query($sql, $DBconnection);
	$find =  mysql_num_rows($queryRSL);
	if($find > 0)
		return false;
	else 
		return true;	
}

if(isset($_POST['registration-atempt'])){
	usleep(1200000);
	$empty_fields = array();
	$post_variables = array();
	
	foreach($_POST as $variableName => $value){
		if(empty($value))
			$empty_fields[] = $variableName;
		$post_variables[$variableName] = trim($value);	
	}
	
	
	//neboli vyplnene vsetky polia
	if(!empty($empty_fields)){
		echo json_encode(array('status'=> -1, 'msg' => PROFILE_EMTPY_FILEDS,'empty' => $empty_fields ));
		exit();
	}
	
	//ak sa nezhoduju hesla
	if($post_variables['pass'] != $post_variables['pass2']){
		echo json_encode(array('status'=> -2, 'msg' => 'Zadané hesla sa nezhodujú.'));
		exit();
	}
	
	//unikatny mail
	if(!is_uniq_email($post_variables['email'])){
		echo json_encode(array('status'=> -4, 'msg' => 'Takýto email už existuje.'));
		exit();
	}
	
	//unikatny login
	if(!is_uniq_login($post_variables['login'])){
		echo json_encode(array('status'=> -3, 'msg' => 'Takýto login už existuje.'));
		exit();
	}
	
	
	$pass = register_create_pass($post_variables['pass'], $post_variables['login']);
	
	//vsetko ok , ulozime do db noveho uzivatela
	mysql_query("INSERT INTO ". TABLE_ADMIN_USERS." (name,surname,login,pass,email, language_code) 
						VALUES ('".mysql_real_escape_string($post_variables['name'])."',
								'".mysql_real_escape_string($post_variables['surname'])."',
								'".mysql_real_escape_string($post_variables['login'])."',
								'".$pass."',
								'".mysql_real_escape_string($post_variables['email'])."',
								'".$_SESSION['language']."') 
				");
					
	
	//zisitime id noveho uzivatela
	$newUserId = mysql_insert_id();
	$_SESSION['user_id'] = $newUserId;
	$_SESSION['username'] = $post_variables['login'];
	
	//nastavime mu pracovnu plochu
	$modulesQuery = mysql_query("SELECT * FROM ".TABLE_MODULES."  ");
	$user_modules = array();
	while($modul = mysql_fetch_assoc($modulesQuery)){
		$user_modules[$modul['modul']] = $modul;
	}
	
	$userModulesJson =  json_encode($user_modules);
	

	mysql_query("INSERT INTO ".TABLE_USER_SETTINGS." (user_id, admin_modules,profile_1,profile_2,profile_3) 
							VALUES('".$newUserId."','".$userModulesJson."','".$userModulesJson."','".$userModulesJson."','".$userModulesJson."')  ");			
	
	
	echo json_encode(array('status'=> 1));
}


if(isset($_POST['check-login'] )){
	$login = trim($_POST['login']);
	
	
	if(!is_uniq_login($login))
		echo json_encode(array('status' => 1,  'msg' => 'Takýto užívateľ už existuje.'));
	else
		echo json_encode(array('status' => 0, 'msg' => 'Ok'));	
	exit();
}

if(isset($_POST['check-email'] )){
	$email = trim($_POST['email']);
	$sql = "SELECT login FROM ".TABLE_ADMIN_USERS." WHERE email LIKE '". mysql_real_escape_string($email) ."' ";
	$queryRSL = mysql_query($sql, $DBconnection);
	$find =  mysql_num_rows($queryRSL);
	if($find > 0)
		echo json_encode(array('status' => 1,  'msg' => 'Takýto email už existuje.'));
	else
		echo json_encode(array('status' => 0, 'msg' => 'Takýto email už existuje.'));	
	exit();
}


?>
