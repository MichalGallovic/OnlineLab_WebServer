<?php 
require_once 'includes/application_top.php';

if(!isset($_POST['action'])){
	exit();
}

$action = $_POST['action'];

if($action == "updateChartPosition"){
	$left = $_POST['left'];
	$top = $_POST['top'];
	$element = explode("-",$_POST['element']);
	
	$selectedModul = $element[0]; 
	
	$mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	
	$selectedProfile = $userSettings['selected_profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	$modules = json_decode($userSettings[$selectedProfileField],true);
	
	$modules[$selectedModul]['top'] = $top;
	$modules[$selectedModul]['left_'] = $left;
	$modules[$selectedModul]['zindex'] = $zindex;
	
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET ".$selectedProfileField." = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");
}

if($action == "updateZindex"){
	$zindexObj = $_POST['zindex'];
	$zindexArr = json_decode( $zindexObj, true);
	$modules = array();
	
	$mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	
	$selectedProfile = $userSettings['selected_profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	$modules = json_decode($userSettings[$selectedProfileField],true);
	
	foreach($zindexArr as $modul => $zindex){
		$element = explode("-",$modul);
		$selectedModul = $element[0];
		$modules[$selectedModul]['zindex'] =  $zindex;	
	}
	
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET ".$selectedProfileField." = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");

	
}

if($action == "updateChartDimenstions"){
	$width = $_POST['width'];
	$height = $_POST['height'];
	$element = explode("-",$_POST['element']);
	$selectedModul = $element[0]; 
	
	$mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	
	$selectedProfile = $userSettings['selected_profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	$modules = json_decode($userSettings[$selectedProfileField],true);
	
	$modules[$selectedModul]['width'] = $width;
	$modules[$selectedModul]['height'] = $height;
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET ".$selectedProfileField." = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");
}

if($action == "hideDashboardSettingsInfo"){
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET show_dashboard_settings_help = 0  WHERE  user_id = '".$_SESSION['user_id']."'  ");
}

if($action == "cahngeMenu"){
	$val = $_POST['value'];
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET left_menu = ".$val."  WHERE  user_id = '".$_SESSION['user_id']."'  ");
}

if($action == "getMenu"){
	$mysql->query("SELECT left_menu FROM ".TABLE_USER_SETTINGS."  WHERE  user_id = '".$_SESSION['user_id']."'  ");
	echo json_encode(array('menu' => $mysql->result(0,'left_menu')));
}

if($action == "setDefayltDashboardSettings"){
	sleep(2);
	$returnArray = array();
	$modules = get_modules_default_settings();
	$selectedProfile = $_POST['profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	
	
	$mysql->query("UPDATE ".TABLE_ADMIN_USER_SETTINGS." SET width=800,height=320 WHERE user_id = '".$_SESSION['user_id']."' ");
	
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET ".$selectedProfileField." = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");
	foreach($modules as $modul => $modulSettings){
		$returnArray[] = 	$modulSettings;
	}
	
	echo json_encode(array('modules' => $returnArray));
}

if($action == "selectProfile"){
	usleep(75000);
	$returnArray = array();
	
	$selectedProfile = $_POST['profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	
	
	
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET selected_profile = '".$selectedProfile."' WHERE user_id = '".$_SESSION['user_id']."' ");
	
	$mysql->query("SELECT ".$selectedProfileField." FROM ".TABLE_USER_SETTINGS."  WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	
	
	
	$modules = json_decode($userSettings[$selectedProfileField],true);
	
	foreach($modules as $modul => $modulSettings){
		$returnArray[] = 	$modulSettings;
	}
	
	echo json_encode(array('modules' => $returnArray));
}

if($action == "setDashboardBox"){
	$element = explode("-",$_POST['box']);
	$selectedModul = $element[0]; 
	
	$mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	$selectedProfile = $userSettings['selected_profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	$modules = json_decode($userSettings[$selectedProfileField],true);
	$modules[$selectedModul]['show'] = 1;
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET ".$selectedProfileField." = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");

	
}

if($action == "unsetDashboardBox"){
	$element = explode("-",$_POST['box']);
	$selectedModul = $element[0]; 
	
	$mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	$selectedProfile = $userSettings['selected_profile'];
	$selectedProfileField = 'profile_'.$selectedProfile;
	$modules = json_decode($userSettings[$selectedProfileField],true);
	$modules[$selectedModul]['show'] = 0;
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET ".$selectedProfileField." = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");

}


?>
