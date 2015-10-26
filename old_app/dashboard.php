<?php 
require_once 'includes/application_top.php';

if(!isset($_SESSION['user_id'])) {
  header('Location: '.ROOT_PATH.'login.php?msg=1');
  exit();
}



//var_dump( file_exists("/jail/home/jenis/public_html/includes/models/start_sci") );


$main_tpl->define(array("dashboard" => "dashboard.htm"));
$main_tpl->assign(array("ROOT_PATH" => ROOT_PATH,'BUTTON_DASHBOARD_SCREEN' => BUTTON_DASHBOARD_SCREEN,"ID_CONTENT" => "content"));

$g_menuStyle = get_menu_style();
if($g_menuStyle == 1){
	$main_tpl->assign(array("DASHBOARD_MAINVIEW_MARGIN" => '',"DASHBOARD_LEFT_NAVIG_WIDTH" => ''));
	
}else{
	$main_tpl->assign(array("DASHBOARD_MAINVIEW_MARGIN" => 'margin-left:51px;',"DASHBOARD_LEFT_NAVIG_WIDTH" => 'width:50px;'));
}

require_once DIR_WS_INCLUDES . 'content.php';

//lava navigacia
require_once DIR_WS_BOXES . 'left_navig.php';
$main_tpl->assign(array('LEFT_NAVIG' => $fetch_box));


//horna lista dashboardu
require_once DIR_WS_BOXES . 'dashboard_header.php';
$main_tpl->assign(array('HEADER' => $fetch_box));




//require_once DIR_WS_MODULES.'livechart/livechart.php';
//$main_tpl->assign(array("LIVECHART" => getLiveChart() ));


$main_tpl->parse("DASHBOARD",'.dashboard');
$main_tpl->xprint(); 

?>
