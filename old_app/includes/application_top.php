<?php 
session_start();
//unset($_SESSION);

//var_dump($_SESSION);

//includuje vsetky funkcie aj nastavenia, prvy subor na natiahnutie

//vseobecne nastavanie db,atd..
require_once('includes/config.php');

// include server parameters
require_once('includes/configure.php');

// define how the session functions will be used
require_once(DIR_WS_FUNCTIONS . 'sessions.php');
//tep_session_start();
//$session_started = true;


// initialize mysql class
require_once(DIR_WS_CLASSES . 'mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);


// templates
require_once(DIR_WS_CLASSES . 'fast_templates.php');
$main_tpl = new rFastTemplate('templates/');
$tmp = new rFastTemplate('templates/');	


// 	
require_once(DIR_WS_INCLUDES . 'db_tables.php');

// define general functions used application-wide
require_once(DIR_WS_FUNCTIONS . 'general.php');

//globalna premenna pre handlpvanie sekcii
$g_selectedSection = trim($_GET['section']);
$g_selectedSectionId = trim($_GET['section_id']);

// presmerovanie na prvu podsekciu
// HOME presmerovanie
if($g_selectedSectionId == 5){
	header('Location: '.ROOT_PATH.'dashboard.php?section_id=13 ');
	exit;
}

// presmerovanie na druhu podsekciu
// Ak klikneme na tlacitka virtualizacia, tak nas to presmeruje 
// na virtualizaciu hlavneho servera
if($g_selectedSectionId == 17){
	header('Location: '.ROOT_PATH.'dashboard.php?section_id=23 ');
	exit;
}

//vyskladavenie url
$g_url = ROOT_PATH.'dashboard.php?section_id='.$g_selectedSectionId;

//jazyky
$g_langs = get_languages_code();
//unset($_SESSION['user_id']);
//rozhodvanioe jazyka podla browsera iba ked nie je prihalsen
if((!isset($_SESSION['language']) or !($_SESSION['languages_id'])) and !isset($_SESSION['user_id']) ){
	//browser lang
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	if(!empty($g_langs[$lang])){
		$_SESSION['languages_id'] =$g_langs[$lang]['languages_id'];
		$_SESSION['language'] = $lang;
	}else{
		$_SESSION['languages_id'] = 4;	
		$_SESSION['language'] = 'sk';	
	}		
}

if(isset($_SESSION['user_id'])){
	$admin = get_user_info($_SESSION['user_id']);
	$_SESSION['language'] = $admin['language_code'];
	$_SESSION['languages_id'] = $g_langs[$admin['language_code']]['languages_id'];
}

//vyber jazyka cez vlajku
if(isset($_GET['lang']) and isset($g_langs[$_GET['lang']]) and !empty($g_langs[$_GET['lang']])){
	$_SESSION['languages_id'] = $g_langs[$_GET['lang']]['languages_id'];
	$_SESSION['language'] = $_GET['lang'];
	$mysql->query("UPDATE ".TABLE_ADMIN_USERS." SET language_code = '". $mysql->escape($_GET['lang']) ."' WHERE id = ".$_SESSION['user_id']." ");
}

require_once 'includes/languages/'.$_SESSION['language'].'.php';
?>