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
	$modules = json_decode($userSettings['admin_modules'],true);
	$modules[$selectedModul]['top'] = $top;
	$modules[$selectedModul]['left_'] = $left;
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET admin_modules = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");
}

if($action == "updateChartDimenstions"){
	$width = $_POST['width'];
	$height = $_POST['height'];
	$element = explode("-",$_POST['element']);
	$selectedModul = $element[0]; 
	
	$mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	$userSettings = $mysql->fetch_array();
	$modules = json_decode($userSettings['admin_modules'],true);
	$modules[$selectedModul]['width'] = $width;
	$modules[$selectedModul]['height'] = $height;
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET admin_modules = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");
}

if($action == "hideDashboardSettingsInfo"){
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET show_dashboard_settings_help = 0  WHERE  user_id = '".$_SESSION['user_id']."'  ");
}

if($action == "setDefayltDashboardSettings"){
	sleep(2);
	$returnArray = array();
	$modules = get_modules_default_settings();
	
	$mysql->query("UPDATE ".TABLE_USER_SETTINGS." SET admin_modules = '".json_encode($modules)."'  WHERE user_id = '".$_SESSION['user_id']."' ");
	foreach($modules as $modul => $modulSettings){
		$returnArray[] = 	$modulSettings;
	}
	
	echo json_encode(array('modules' => $returnArray));
}

?>
