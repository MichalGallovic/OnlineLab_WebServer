<?php	
if(isset($_COOKIE[COOKIE_NAME_AUTOLOGIN]) and $_COOKIE[COOKIE_NAME_AUTOLOGIN] != ""){
	parse_str($_COOKIE[COOKIE_NAME_AUTOLOGIN]);
	
	//var_dump($_COOKIE[COOKIE_NAME_AUTOLOGIN]);
	//exit;
	
	$db_handler->query("SELECT * FROM ". TABLE_ADMIN_USERS ." WHERE login = '".$usr."'");
	$user = $db_handler->fetch_array();
	
	if($db_handler->num_rows() &&  $hash == $user['pass']) {
	
		$_SESSION[$sessionUsernameKey] = $user['login'];
		$_SESSION[$sessionUseridKey] = $user['id'];
		/*$_SESSION['hash'] = $user['hash'];*/
		header("Location: ".SECRET_AREA_PAGE." ");
		exit();
	}
	
}

?>