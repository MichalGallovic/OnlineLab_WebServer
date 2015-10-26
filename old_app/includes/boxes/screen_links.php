<?php


$tmp->define(array("screen_links" => "boxes/screen_links.htm"));
$tmp->assign(array("ROOT_PATH" => ROOT_PATH,
				   'BUTTON_DASHBOARD_SCREEN' => BUTTON_DASHBOARD_SCREEN,
 				   "BUTTON_DASHBOARD_HELPER" =>BUTTON_DASHBOARD_HELPER,
				   "HELP_MAIN_HEADER" => HELP_MAIN_HEADER));


//zisitme si nastavenie modulov na ploche
$userSettingsQuery = $mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".(int)$_SESSION['user_id']."'  ");
$loggedUser = $mysql->fetch_array($userSettingsQuery);
$selectedUserDashboardProfile = $loggedUser['selected_profile'];
$selectedProfileField = 'profile_'.$selectedUserDashboardProfile;
$dashboardModulesSettings = json_decode($loggedUser[$selectedProfileField],true);

//var_dump($dashboardModulesSettings);

$mysql->query("SELECT profile_1,profile_2,profile_3,selected_profile FROM ".TABLE_USER_SETTINGS."  WHERE user_id = '".(int)$_SESSION['user_id']."' ");
$profiles[1] = $mysql->result(0,'profile_1');
$profiles[2] = $mysql->result(0,'profile_2');
$profiles[3] = $mysql->result(0,'profile_3');
$selected_profile = $mysql->result(0,'selected_profile');
//$profiles = array(1 => 'profile_1',2 => 'profile_2',3 => 'profile_3');

foreach($profiles as $id => $profile){
    //$user_profile = get_object_vars(json_decode($profile));
	
	$tmp->assign(array('CHECKED_PROFILE' => ($selected_profile == $id) ? 'checked="checked"' : '',
					   'PROFILE_ID' => $id ));
	$tmp->parse("PROFILE_ROW",".profile_row");
}

foreach($profiles as $id => $profile){
	$encodedProfile = json_decode($profile);
	//var_dump($encodedProfile);
	foreach($encodedProfile as $modulName => $modul){
		$modul = get_object_vars($modul);
		if(!$modul['widget'])
			continue;	
		
		$tmp->assign(array("MODUL_TITLE" => constant($modul['modul_title_constant']),
					   "MODUL_CHECKBOX_NAME" => $modul['modul'].'-modul_'.$id,
					   "MODUL_CHECKBOX_ID" => $modul['modul'].'-modul_checkbox',
					   "MODUL_SHOW_CHECKBOX" => $modul['show'] == 1 ? 'checked="checked"' : '', 
					   "MODUL_ID" => $modul['modul'].'-modul' ));	
		$tmp->parse("MODULES_ROW",".modules_row");
	}
	$tmp->assign(array("BLOCK_MODULES_DISPLAY" => $selected_profile == $id ? '' : 'nodisplay',
					   "BLOCK_MODULES_PROFILE_NAME" => "modules-profile-".$id ));
	$tmp->parse("MODULES_BLOCK",".modules_block");
	$tmp->clear_dynamic("modules_row");
}

/*foreach($dashboardModulesSettings as $modul){
	if(!$modul['widget'])
		continue;
	$tmp->assign(array("MODUL_TITLE" => constant($modul['modul_title_constant']),
					   "MODUL_CHECKBOX_NAME" => $modul['modul'].'-modul',
					   "MODUL_CHECKBOX_ID" => $modul['modul'].'-modul_checkbox',
					   "MODUL_SHOW_CHECKBOX" => $modul['show'] == 1 ? 'checked="checked"' : '', 
					   "MODUL_ID" => $modul['modul'].'-modul' ));	
	$tmp->parse("MODULES_ROW",".modules_row");
}*/



$tmp->parse("SCREEN_LINKS",".screen_links"); 
$fetch_box = $tmp->fetch("SCREEN_LINKS");

?>