<?php 
//nacitanie konfiguracneho subora pre konkretny modul
require_once('config.php');

//nacitanie jazyka
require_once('languages/'.$_SESSION['language'].'.php');

//vytvorenie instancie pre pracu so sablonou pre modul
$users_tpl = new rFastTemplate(MODUL_PATH);

//definovanie nazvu sablony v danom priecinku 
$users_tpl->define(array("users" => "users.html"));

//inrepolacia globalnej konstanty ROOT_PATH do sablonovacej premennej ROOT_PATH
//je to dobre napriklad, ked potrebujeme zadefinovat uplnu cestu k css resp. js suborom
$users_tpl->assign(array("ROOT_PATH"            => ROOT_PATH,
                        "USR_LOGIN"             => USR_LOGIN,
                        "USR_NAME"              => USR_NAME,
                        "USR_MAIL"              => USR_MAIL,
                        "USR_ROLE"              => USR_ROLE,
                        "NO_USRS"               => NO_USRS,
                        "USER_ROLE_TITLE_ADMIN"     => USER_ROLE_TITLE_ADMIN,
                        "USER_ROLE_TITLE_TEACHER"   => USER_ROLE_TITLE_TEACHER,
                        "USER_ROLE_TITLE_DEVELOPER" => USER_ROLE_TITLE_DEVELOPER,
                        "USER_ROLE_TITLE_STUDENT"   => USER_ROLE_TITLE_STUDENT));

//interpolacia celej sablony
$users_tpl->parse("USERS", ".users");

//obsah celej sablony sme ulozily do php premennej
$fetch_module = $users_tpl->fetch("USERS");

//standardna funckia pre modul
function get_users(){
	global $fetch_module;
	return $fetch_module; 
}

//standardna funckia pre obsah widget modulu
function get_users_for_widget(){
	global $fetch_module;
	return $fetch_module; 
}
?>
