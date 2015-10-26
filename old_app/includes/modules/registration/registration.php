<?php

require('config.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');
 
function get_register_form(){
	
	//$currentDirectory = array_pop(explode("/", getcwd()));
	
	$currentSrc = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
	$currentSrc = ltrim(preg_replace('/\\\\/', '/', $currentSrc), '/');
	$currentSrcArray = explode("/",$currentSrc); 
	array_pop($currentSrcArray);
	$currentSrc = '/'.implode("/",$currentSrcArray);
	//var_dump($currentSrc);
	
	$html .= '<link href="'.FULL_MODULES_PATH.REGISTRATION_MODUL_NAME.'/css/style.css" rel="stylesheet" type="text/css" />';	
	$html .= '<script  type="text/javascript" src="'.FULL_MODULES_PATH.REGISTRATION_MODUL_NAME.'/js/default.js"></script>';	
	$html .= '<form id="registration-form" action="" method="post" enctype="multipart/form-data" onsubmit="register();return false;">
				<h2>'. REGISTER_TITLE .'</h2>
				<p id="empty-fields-message-holder" class="info">'.MARKED_FIELDS.'</p>
				<p>
					<label for="registerName">'. USER_NAME .'</label>
					<input type="text" name="name" id="registerName" />
				</p>
				<p>
					<label for="registerSurname">'. USER_SRUNAME .'</label>
					<input type="text" name="surname" id="registerSurname" />
				</p>
				<p>
					<label for="registerEmail">'. USER_EMAIL .'</label>
					<input type="text" onblur="check_email();" name="email" id="registerEmail" />
					<p id="email-check-message-holder" class="info">'. EMAIL_EXIST .'</p>
				</p>
				<p>
					<label for="registerLogin">'. USER_LOGIN .'</label>
					<input type="text" onblur="check_login();" name="login" id="registerLogin" />
					<p id="login-check-message-holder" class="info">'. USERNAME_EXIST .'</p>
				</p>
				<p>
					<label for="registerPassword">'.USER_PASS.'</label>
					<input type="password" name="pass" id="registerPassword" />
					<p id="passwords-check-message-holder" class="info">'.PASS_NO_MATCH.'</p>
				</p>
				<p>
					<label for="registerPassword2">'.USER_PASS2.'</label>
					<input type="password" name="pass2" id="registerPassword2" />
				</p>
				<p style="float:left;">
					<input type="submit" value="'. BTN_REGISTER .'" />
					<input type="hidden" value="1" name="registration-atempt" />
				</p>
				<p class="reg-loader" >
					<img src="'.ROOT_PATH.'includes/modules/registration/images/3.gif" width="25" alt="ajax-loader" />
				</p>
			</form>';
	
	$html .= '<div class="reg-success-holder">
			  	<h2 class="reg-succes-title" >'.REG_SUCCES.'</h2>
			  	<input type="button"  value="'.BTN_CONITNUE.'" onclick="window.location.reload();" />
			  </div>';
	
	return $html;
}

?>
