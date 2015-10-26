<?php 

$tmp->define(array("header" => "boxes/dashboard_header.htm"));

$section = get_main_section($g_selectedSectionId);

$tmp->assign(array("ROOT_PATH" => ROOT_PATH, 
				   'LOGGED_AS' => LOGGED_AS,
				   'USER_HASH' => $_SESSION['hash'],
				   'SECTION_TITLE' => constant($section['title']),
				   'SECTION_TITLE_CLASS' => $section['title_class'] ));

//langugaes
$langugaes = get_languages();

foreach($langugaes as $lang){
	$tmp->assign(array('LANG_IMAGE_SRC' => ROOT_PATH.DIR_WS_IMAGES.$lang['image'],
					   'LANG_NAME' => $lang['name'],
					   'LANG_URL' => $g_url.'&lang='.$lang['code'],
					   'LANG_SELECTED' => $lang['languages_id'] == $_SESSION['languages_id'] ? 'selected' : ''  ));
	$tmp->parse("LANG_ROW",".lang_row"); 	
}

if($_SESSION['user_id'] == 'google'){
	$tmp->assign(array("ADMIN_NAME" => $_SESSION['username']));	
}elseif($user = get_user_info($_SESSION['user_id'])){
	$tmp->assign(array("ADMIN_NAME" => $user['login']));
};


$tmp->parse("D_HEADER",".header"); 
$fetch_box = $tmp->fetch("D_HEADER");

?>