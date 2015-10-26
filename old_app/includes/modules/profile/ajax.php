<?php 

session_start();

require_once('../../config.php');
require_once('../../db_tables.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);


if(isset($_POST['attempt_profile_update']) and $_POST['attempt_profile_update'] ){
	usleep(1200000);
	$empty_fields = array();
	$post_variables = array();

	foreach($_POST as $variableName => $value){
		if($variableName == 'pass' or $variableName == 'pass2')
			continue;
			
		if(empty($value)){
			$empty_fields[] = $variableName;
		}
		$post_variables[$variableName] = $mysql->escape(trim($value));	
	}
	
	
	//neboli vyplnene vsetky polia
	if(!empty($empty_fields)){
		echo json_encode(array('status'=> -1, 'msg' => PROFILE_EMTPY_FILEDS,'empty' => $empty_fields ));
		exit();
	}
	
	//ak zadal heslo bez potvrdernia
	if($_POST['pass'] != '' and $_POST['pass2'] == '' ){
	    $empty_fields = array();
	    $empty_fields[] = 'pass2';
		echo json_encode(array('status'=> -2, 'msg' => PROFILE_CONFIIRM_PASS_EMPTY , 'empty' => $empty_fields ));
		exit();
	}
	
	//ak zadal potvrdenie hesla bez prveho jesla
	if($_POST['pass'] == '' and $_POST['pass2'] != '' ){
	    $empty_fields[] = 'pass';
		echo json_encode(array('status'=> -3, 'msg' => PROFILE_PASS_EMPTY , 'empty' => $empty_fields ));
		exit();
	}
	
	//ak zadal potvrdenie hesla bez prveho jesla
	if($_POST['pass'] != $_POST['pass2']){
	    $empty_fields[] = 'pass';
		$empty_fields[] = 'pass2';
		echo json_encode(array('status'=> -4, 'msg' => PROFILE_PASS_NOMATCH , 'empty' => $empty_fields ));
		exit();
	}
	
	if(!is_email_uniq($post_variables['email'])){
		$empty_fields[] = 'email';
		echo json_encode(array('status'=> -5, 'msg' => PRFILE_NO_UNIQ_EMAIL , 'empty' => $empty_fields ));
		exit();
	}
	
	if(isset($_POST['pass']) and $_POST['pass'] != ''){
		$pass = trim($_POST['pass']);
		$pass = create_pass($pass, $post_variables['login']);
		$mysql->query("UPDATE ". TABLE_ADMIN_USERS." SET pass = '".$pass."', email = '".$post_variables['email']."', name = '".$post_variables['name']."', surname = '".$post_variables['surname']."', language_code = '".$post_variables['language_code']."'   WHERE id=".$_SESSION['user_id']." ");

	}else{
		//vsetko ok , ulozime do db noveho uzivatela
		$mysql->query("UPDATE ". TABLE_ADMIN_USERS." SET email = '".$post_variables['email']."', name = '".$post_variables['name']."', surname = '".$post_variables['surname']."', language_code = '".$post_variables['language_code']."'   WHERE id=".$_SESSION['user_id']." ");
	}
	
	echo json_encode(array('status'=> 1, 'msg' => PROFILE_UPDATE_OK  ));
	exit();
	
}

 function is_email_uniq($email){
	global $mysql;
	
	$sql = "SELECT login FROM ".TABLE_ADMIN_USERS." WHERE email LIKE '". mysql_real_escape_string($email) ."' AND id != ".$_SESSION['user_id']."  ";
	$mysql->query($sql);
	$find =  $mysql->num_rows();
	if($find > 0)
		return false;
	else 
		return true;	
  }
  
  function create_pass($pass, $salt) {
  	return md5(md5($pass).$salt);
}

?>
