<?php 

$content_tpl = new rFastTemplate('templates/');	
$content_tpl->define(array("content" => "content.htm"));

//ziskame vsetky informacie o aktualnej sekcii
$mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE id = '".$mysql->escape($g_selectedSectionId)."' ");
$section = $mysql->fetch_array();



if(!$section){
	header('Location: '.ROOT_PATH.' ');
	exit();
}

//5,13 pracovna plocha
//11 rozlozenie praocvnej plochy
if($section['id'] == 13 or $section['id'] == 11){
	
	//SCREEN LINKS
	require_once DIR_WS_BOXES . 'screen_links.php';
	$content_tpl->assign(array('SCREEN_LINKS' => $fetch_box));
	
	$userSettingsQuery = $mysql->query("SELECT * FROM ".TABLE_USER_SETTINGS." WHERE user_id = '".(int)$_SESSION['user_id']."'  ");
	$loggedUser = $mysql->fetch_array($userSettingsQuery);
	$selectedUserDashboardProfile = $loggedUser['selected_profile'];
	$selectedProfileField = 'profile_'.$selectedUserDashboardProfile;
	$dashboardModulesSettings = json_decode($loggedUser[$selectedProfileField],true);
	
	/*var_dump($dashboardModulesSettings);
	exit;*/
	
	foreach($dashboardModulesSettings as $modul){
		//var_dump($modul);
		//exit();
		$modul_name = strtolower(trim($modul['modul']));
		$modul_template_name = strtoupper(trim($modul['modul']));
		$modul_callback_function = 'get_'.$modul_name;
		
		//ak ma modul widgget tak vytiahneeme ten.
		if($modul['widget']){
			require_once DIR_WS_MODULES.$modul_name.'/'.$modul_name.'.php';
			$modul_callback_function .= '_widget';
			$content_tpl->assign(array("MODUL" => call_user_func($modul_callback_function) ));
		}else{
			require_once DIR_WS_MODULES.$modul_name.'/'.$modul_name.'.php';
			$content_tpl->assign(array("MODUL" => call_user_func($modul_callback_function) ));
		}
		
		$content_tpl->assign(array("MODUL_ID" => $modul_name."-modul",
								   "MODUL_TOP_POSITION" => $modul['top'],
								   "MODUL_LEFT_POSITION" => $modul['left_'],
								   "MODUL_ZINDEX" => $modul['zindex'],
								   "MODUL_WIDTH" => ($modul_name == 'livechart') ? 'auto' : $modul['width'].'px',
								   "MODUL_HEIGHT" => ($modul_name == 'livechart') ? 'auto' : $modul['height'].'px',
								   "MODUL_SHOW_CLASS" => $modul['show'] == 1 ? '' : 'nodisplay' ,
								   "MODUL_TITLE" => constant($modul['modul_title_constant']) ));
		if($section['id'] == 11){
			$content_tpl->parse('DASHBOARD_MODUL_BLOCK','.dashboard_dragable_modul_block');	
			
		}else
			$content_tpl->parse('DASHBOARD_MODUL_BLOCK','.dashboard_modul_block');	
		
	}
	
	//ak sme na nastaveni pracovnej plchy pridame selector pomocou ktroeho budu okna dragabble
	$content_tpl->assign(array("DRAGABLE_SELECTOR" => ( $section['id'] == 11) ? 'dashboard-settings' : 'dashboard' ));
	
	//ci zobrazime dialogove okno s napovedou pre nastaveni pracovnej plochy
	if($loggedUser['show_dashboard_settings_help'] && $section['id'] == 11)
		$content_tpl->parse('DASHBOARD_HELP_BLOCK','.dashboard_settings_help_block');
	
	$content_tpl->parse('DASHBOARD_MAIN','.dashboard_main');	
  
// ----------------------------------------------------------------------------
//17,23,24,26 virtualizacia
// ----------------------------------------------------------------------------
// 26 virtualizacia manazovania balickov
} else if($section['id'] == 26){
    require_once DIR_WS_MODULES.$section['modul'].'/'.$section['modul'].'.php';	
    // nebudeme volat standardnu fciu, ale upravenu
    // $modul_callback_function =  'get_'.$section['modul'];
    $modul_callback_function =  'get_packages';
    $content_tpl->assign(array("SECTION_MODUL" => call_user_func($modul_callback_function) ));
    $content_tpl->parse('SECTION_BLOCK','.section_block');
//23 virtualizacia hlavneho servera
} else if($section['id'] == 23){
    require_once DIR_WS_MODULES.$section['modul'].'/'.$section['modul'].'.php';	
    // nebudeme volat standardnu fciu, ale upravenu
    // $modul_callback_function =  'get_'.$section['modul'];
    $modul_callback_function =  'get_main_server_virtualization';
    $content_tpl->assign(array("SECTION_MODUL" => call_user_func($modul_callback_function) ));
    $content_tpl->parse('SECTION_BLOCK','.section_block');
//24 virtualizacia realnych zariadeni
} else if($section['id'] == 24){
    require_once DIR_WS_MODULES.$section['modul'].'/'.$section['modul'].'.php';	
    // nebudeme volat standardnu fciu, ale upravenu
    // $modul_callback_function =  'get_'.$section['modul'];
    $modul_callback_function =  'get_equipments_virtualization';
    $content_tpl->assign(array("SECTION_MODUL" => call_user_func($modul_callback_function) ));
    $content_tpl->parse('SECTION_BLOCK','.section_block');
}else{
    
	if($section['modul']){
		require_once DIR_WS_MODULES.$section['modul'].'/'.$section['modul'].'.php';	
		$modul_callback_function =  'get_'.$section['modul'];
		$content_tpl->assign(array("SECTION_MODUL" => call_user_func($modul_callback_function) ));
		$content_tpl->parse('SECTION_BLOCK','.section_block');	
	}else{
		$content_tpl->assign(array("SECTION_MODUL" => "<div>Bad modul settings, contact system administrator !</div>" ));
		$content_tpl->parse('SECTION_BLOCK','.section_block');	
	}
	
	//switch $section['id']
}

//experiemnty
/*if($section['id'] == 9){
	
	require_once DIR_WS_MODULES.'livechart/livechart.php';
	$content_tpl->assign(array("LIVECHART" => get_livechart() ));
	$content_tpl->parse('DASHBOARD_MAIN','.experiments_block');	
}


//rezervacie
if($section['id'] == 3){
	
	require_once DIR_WS_MODULES.'reservation/reservation.php';
	$content_tpl->assign(array("RESERVATION_MODUL" => get_reservation() ));
	$content_tpl->parse('RESERVATION BLOCK','.reservation_block');	
}

//reporty
if($section['id'] == 14){
	require_once DIR_WS_MODULES.'report/report.php';
	$content_tpl->assign(array("REPORT_MODUL" => get_report() ));
	$content_tpl->parse('REPORT_BLOCK','.report_block');	
}

//regulatory
if($section['id'] == 15){
	require_once DIR_WS_MODULES.'controller/controller.php';
	$content_tpl->assign(array("CONTROLLER_MODUL" => get_controller() ));
	$content_tpl->parse('CONTROLLER_BLOCK','.controller_block');	
}*/




$content_tpl->parse('CONTENT_ALL','.content');
$fetch_content = $content_tpl->fetch("CONTENT_ALL");
$main_tpl->assign(array("MAIN_CONTENT" => $fetch_content ));


?>
