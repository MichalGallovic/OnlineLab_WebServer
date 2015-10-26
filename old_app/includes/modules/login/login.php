<?php 
//nacianie potrebnych suborov
require_once('config.php');
//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');
require_once('mysql.php');
$db_handler = new dbMysql($dbServer, $dbServerUsername, $dbServerPassword, $dbName, true, $dbEncoding);				
/*****************************************/

$return = '';
$warning = '';
$info = '';

// MODULE SPECIFIC FUNCTIONS

function add_warning($txt){
	global $warning;
	$warning .= '<div class="login_error">'. $txt . '</div>';
	
}

function add_info($txt){
	global $info;
	$info .= '<div class="login_info">'. $txt . '</div>';
}

function create_pass($pass, $salt) {
  	return md5(md5($pass).$salt);
}

function getRandomHash(){
	return md5(time().rand(1,100));
}


function get_auth_form(){
	global $warning, $info;
	
	
	do_authentification();
	
	if(isset($_GET['loggedout']) and $_GET['loggedout'] ){
		add_info(LOGOUTED);
	}
	
	if(isset($_GET['emptyfields']) and $_GET['emptyfields'] ){
		add_warning(EMPTY_USERNMAE_OR_PASSWORD);
	}
	
	if(isset($_GET['wrongfields']) and $_GET['wrongfields'] ){
		add_warning(WRONG_USERNMAE_OR_PASSWORD);
	}
	
	$currentDirectory = array_pop(explode("/", getcwd()));
	
	$currentSrc = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
	$currentSrc = ltrim(preg_replace('/\\\\/', '/', $currentSrc), '/');
	$currentSrcArray = explode("/",$currentSrc); 
	array_pop($currentSrcArray);
	$currentSrc = '/'.implode("/",$currentSrcArray);
	
	
	$return .= '<link href="'.FULL_MODULES_PATH.LOGIN_MODUL_NAME.'/style.css" rel="stylesheet" type="text/css" />';	
	$return .= '<script  type="text/javascript" src="'.FULL_MODULES_PATH.LOGIN_MODUL_NAME.'/default.js"></script>';	
	
	
	$return .= ($warning != '') ? $warning : '';
	$return .= ($info != '') ? $info : ''; 
	$return .=  '<form id="" enctype="multipart/form-data" method="post" name=""  action=""  >';
	$return .=	'<p>';
	$return .=  '<label>' . LOGIN_LABEL . '<br><input type="text"   value="" class="input" id="user_login" name="login"></label>';
	$return .=	'</p>';
	$return .=	'<p>';
	$return .=	'<label>' . PASSWORD_LABEL . '<br><input type="password"   value="" class="input" id="user_pass" name="pass"></label>';
	$return .=	'</p>';
	$return .=	'<p>';
	$return .=	'<input type="checkbox" name="autologin" id="autologin" value="1"><label for="autologin" > '.AUTOLIGIN_LABEL.'</label>';
	$return .= '<a style="float:right;font-size:14px;" href="'.ROOT_PATH.'login.php?forgotpass=1" title="'.FORGOT_PASSWORD_LABEL.'">'. FORGOT_PASSWORD_LABEL .'</a>';
	$return .=	'</p>';
	$return .=	'<p>';
	$return .=	'';
	$return .=	'</p>';
	$return .=	'<p>';
	if(GOOGLE_AUTH or STU_LDAP_AUTH ){	
	
	$return .=  '<table class="auth_types">
					<tr>
						<td style="padding-right:5px;" ><label >'. AUTH_TYPES_LABEL .'</label></td>
						<td><label for="local_auth" class="local_auth_label"></label></td>
						<td><input id="local_auth" type="radio" name="account_type" value="local" checked /></td>';
	
	if(GOOGLE_AUTH)					
			$return .= '<td><label for="google_auth" class="google_auth_label" ></label></td>
						<td><input id="google_auth" type="radio" name="account_type" value="google"  /></td>';
	
	if(STU_LDAP_AUTH)
			$return .= '<td><label for="stuldap_auth" class="stuldap_auth_label" ></label></td>
						<td><input id="stuldap_auth" type="radio" name="account_type" value="stuldap"  /></td>';
						
	$return .=	'	</tr>';
	$return .=	'</table>';
	$return .= '<div id="local-label" class="login_info center infolabel"  >' . LOCAL_AUTH_LABEL . '</div> 
				<div id="google-label" class="login_info center infolabel" style="display:none">' . GOOGLE_AUTH_LABEL . '</div>
				<div id="stuldap-label" class="login_info center infolabel" style="display:none">' . STULDAP_AUTH_LABEL . '</div> ';	
	
	}
	$return .=	'</p>';
	$return .=	'<p class="t_center" style="margin-top:10px;">';
	$return .=	'<input type="submit"  value="'.SING_IN_BTN.'" id="" name="submit"><input type="hidden" value="1" name="login-atempt">';
	$return .= '</p>';
	$return .= '</form>';
	
	return $return;
}

function do_authentification(){
	global $sessionUsernameKey, $sessionUseridKey, $sessionUserHashKey ,$db_handler, $auth_table_fields;
	
		
	//if user is not logged , we try to check if user has cokkies for autlogin
	if(AUTOLOGIN){
		if(!$_SESSION[$sessionUsernameKey] or !$_SESSION[$sessionUseridKey] ){
			require_once dirname(__FILE__).'/autologin.php';
		}
	}
	
	if(GOOGLE_AUTH){
		require_once dirname(__FILE__).'/google_auth.php';
	}
	
	if(STU_LDAP_AUTH){
		require_once dirname(__FILE__).'/stu_ldap.php';
	}
	
	//atempt for authetification
	if(isset($_POST['login-atempt']) and $_POST['login-atempt'] == 1 ){
		
		$login = trim($_POST['login']);
		$pass = trim($_POST['pass']);
		
		if(!strlen($login) or !strlen($pass)){
			header("Location: ".$_SERVER["SCRIPT_NAME"]."?emptyfields=true ");
			exit();
		}
		
		$post_autologin = (AUTOLOGIN) ? $_POST['autologin'] : 0;
		$post_google_auth =  (GOOGLE_AUTH and $_POST['account_type'] == 'google') ? 1 : 0;
		$post_stuldap_auth =  (STU_LDAP_AUTH and $_POST['account_type'] == 'stuldap') ? 1 : 0;
		
		$hashPass = create_pass($pass, $login);
		
		
		//google auth.
		if($post_google_auth == 1){
			if(googleAuthenticate($login,$pass )){
				
				$email = $login.'@gmail.com';
				$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$lang = ($lang == 'sk') ? 'sk' : 'en';
				$names = explode('.',$login);
				$name = $names[0];
				$surname = $names[1];
				
				if($post_autologin == 1){
					setcookie (COOKIE_NAME_AUTOLOGIN, 'usr='.$login.'&hash='.$hashPass, time() + COOKIE_NAME_AUTOLOGIN_TIME);	
				}
				
				
				
				$db_handler->query("SELECT * FROM " . TABLE_ADMIN_USERS . " WHERE ". $auth_table_fields['login'] ." = '".$db_handler->escape($login)."'");
				
				//vytovrime mu novu ucet ke je tu prvy krat
				if($db_handler->num_rows() < 1){
					$_SESSION[$sessionUserHashKey] = getRandomHash();
					
					$db_handler->query("INSERT INTO " . TABLE_ADMIN_USERS . " (account_type,login,hash,email,language_code,name, surname) 
						VALUES (2,'".$login."','".$_SESSION[$sessionUserHashKey]."','".$email."','".$lang."', '".$name."', '".$surname."') ");	
					
					$new_user_id = $db_handler->insert_id();
					
					$_SESSION[$sessionUsernameKey] = $login;
					$_SESSION[$sessionUseridKey] = $new_user_id; 
					
				}else{
					//mesetujeme session
					$valid_user = $db_handler->fetch_array();
					$_SESSION[$sessionUserHashKey] = $valid_user['hash'];
					$_SESSION[$sessionUsernameKey] = $valid_user['login'];
					$_SESSION[$sessionUseridKey] =   $valid_user['user_id'];
				}
				
				//nastavime mu pracovnu plochu
				$modulesQuery = $db_handler->query("SELECT * FROM ".TABLE_MODULES."  ");
				$user_modules = array();
				while($modul = $db_handler->fetch_array($modulesQuery)){
					$user_modules[$modul['modul']] = $modul;
				}
				
				$userModulesJson =  json_encode($user_modules);
				
			
				$db_handler->query("INSERT INTO ".TABLE_USER_SETTINGS." (user_id, admin_modules,profile_1,profile_2,profile_3) 
										VALUES('".$new_user_id."','".$userModulesJson."','".$userModulesJson."','".$userModulesJson."','".$userModulesJson."')  ");			
				
				
				
				
				header("Location: ".SECRET_AREA_PAGE." ");
				exit();
			}else{
				header("Location: ".$_SERVER["SCRIPT_NAME"]."?wrongfields=true ");
				exit();
			}
		}
		
		//STU ldap auth
		if($post_stuldap_auth == 1){
			if(stuLdapAuth($login,$pass)){
				$_SESSION[$sessionUsernameKey] = $login;
				$_SESSION[$sessionUseridKey] = 'stulap';
				
				$name = $login;
				$surname = '';
				$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$lang = ($lang == 'sk') ? 'sk' : 'en';
				$email = $login.'@stuba.sk';
				
				/*$_SESSION[$sessionUserHashKey] = $valid_user['hash'];
					$_SESSION[$sessionUsernameKey] = $valid_user['login'];
					$_SESSION[$sessionUseridKey] =   $valid_user['user_id'];*/
				
				//pozrme cookie pre prohalsenie
				if($post_autologin == 1){
					setcookie (COOKIE_NAME_AUTOLOGIN, 'usr='.$login.'&hash='.$hashPass, time() + COOKIE_NAME_AUTOLOGIN_TIME);	
				}
				
				$db_handler->query("SELECT * FROM " . TABLE_ADMIN_USERS . " WHERE ". $auth_table_fields['login'] ." = '".$db_handler->escape($login)."'");
				
				
				//vytovrime mu novu ucet ke je tu prvy krat
				if($db_handler->num_rows() < 1){
					$_SESSION[$sessionUserHashKey] = getRandomHash();
					
					$db_handler->query("INSERT INTO " . TABLE_ADMIN_USERS . " (account_type,login,hash,email,language_code,name, surname) 
						VALUES (3,'".$login."','".$_SESSION[$sessionUserHashKey]."','".$email."','".$lang."', '".$name."', '".$surname."') ");	
					
					$new_user_id = $db_handler->insert_id();
					
					$_SESSION[$sessionUsernameKey] = $login;
					$_SESSION[$sessionUseridKey] = $new_user_id; 
					
				}else{
					//mesetujeme session
					$valid_user = $db_handler->fetch_array();
					$_SESSION[$sessionUserHashKey] = $valid_user['hash'];
					$_SESSION[$sessionUsernameKey] = $valid_user['login'];
					$_SESSION[$sessionUseridKey] =   $valid_user['user_id'];
				}
				
				//nastavime mu pracovnu plochu
				$modulesQuery = $db_handler->query("SELECT * FROM ".TABLE_MODULES."  ");
				$user_modules = array();
				while($modul = $db_handler->fetch_array($modulesQuery)){
					$user_modules[$modul['modul']] = $modul;
				}
				
				$userModulesJson =  json_encode($user_modules);
				
			
				$db_handler->query("INSERT INTO ".TABLE_USER_SETTINGS." (user_id, admin_modules,profile_1,profile_2,profile_3) 
										VALUES('".$new_user_id."','".$userModulesJson."','".$userModulesJson."','".$userModulesJson."','".$userModulesJson."')  ");			
				
				
				
				
				header("Location: ".SECRET_AREA_PAGE." ");
				exit();
			}else{
				header("Location: ".$_SERVER["SCRIPT_NAME"]."?wrongfields=true ");
				exit();
			}
		}
		
		//autentifikacia pomocou lokalneho prihlasenia
		
		$db_handler->query("SELECT * FROM " . TABLE_ADMIN_USERS . " WHERE ". $auth_table_fields['login'] ." = '".$db_handler->escape($login)."'");
		$user = $db_handler->fetch_array();
		
		if(!$db_handler->num_rows() or  $hashPass != $user[$auth_table_fields['password']]) {
			header("Location: ".$_SERVER["SCRIPT_NAME"]."?wrongfields=true ");
			exit();
		 }else{
			$_SESSION[$sessionUsernameKey] = $user[$auth_table_fields['login']];
			$_SESSION[$sessionUseridKey] = $user[$auth_table_fields['user_id']]; 
			
			if(!empty($user['hash'])){
				$_SESSION[$sessionUserHashKey] = $user['hash'];
			}else{
				$_SESSION[$sessionUserHashKey] = getRandomHash();
				$db_handler->query("UPDATE ". TABLE_ADMIN_USERS ." SET hash = '". $_SESSION[$sessionUserHashKey] ."' WHERE id = '".$user['id']."'  ");	
			}
					 
			if($post_autologin == 1){
				setcookie (COOKIE_NAME_AUTOLOGIN, 'usr='.$user[$auth_table_fields['login']].'&hash='.$hashPass, time() + COOKIE_NAME_AUTOLOGIN_TIME);	
			}
				
			header("Location: ".SECRET_AREA_PAGE." ");
			exit();
		 }
	}
}
?>