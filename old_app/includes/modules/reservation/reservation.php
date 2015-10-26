<?php 
//vsetko potrenbne co treb nacitat je v init.php
require_once('init.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

function get_events($type="all"){
	global $reservation_mysql;
	$events = array();
	
	
	if($type == "all"){
		$reservation_mysql->query("SELECT r.user_id,r.reservation_id,r.start,r.end,r.title,r.readonly,e.color,e.equipment_name,e.id as equipment_id  FROM ".TABLE_RESERVATION." r INNER JOIN ".TABLE_EQUIPMENT." e ON (r.equipment  = e.id) 
								WHERE 1 ");
		
	}elseif($type == "own"){
		/*var_dump("SELECT r.user_id,r.reservation_id,r.start,r.end,r.title,r.readonly,e.color,e.equipment_name,e.id as equipment_id  FROM ".TABLE_RESERVATION." r INNER JOIN ".TABLE_EQUIPMENT." e ON (r.equipment  = e.id) 
								WHERE r.user_id = '".$_SESSION['user_id']."' ");
		exit();*/
		$reservation_mysql->query("SELECT r.user_id,r.reservation_id,r.start,r.end,r.title,r.readonly,e.color,e.equipment_name,e.id as equipment_id  FROM ".TABLE_RESERVATION." r INNER JOIN ".TABLE_EQUIPMENT." e ON (r.equipment  = e.id) 
								WHERE r.user_id = '".$_SESSION['user_id']."' ");	
	}else{
		$reservation_mysql->query("SELECT r.user_id,r.reservation_id,r.start,r.end,r.title,r.readonly,e.color,e.equipment_name,e.id as equipment_id  FROM ".TABLE_RESERVATION." r INNER JOIN ".TABLE_EQUIPMENT." e ON (r.equipment  = e.id) 
								WHERE e.equipment_name = '".$type."' ");
	}
	
	
	while($event = $reservation_mysql->fetch_array()){
		$events[] = array("id" => $event['reservation_id'],
						  "start" => date( 'c',strtotime($event['start'])),
						  "end" => date( 'c' , strtotime($event['end']) ),
						  "title" => $event['title'],
						  "color" => $event['color'],
						  "equipment" => $event['equipment_name'],
						  "equipment_id" => $event['equipment_id'],
						  "readOnly" => ($event['user_id'] != $_SESSION['user_id'] or time( ) > strtotime( $event['start'] ) ) ? TRUE : FALSE);
	}
	
	return json_encode($events);
}

function get_last_reservation_id(){
	global $reservation_mysql;
	$reservation_mysql->query("SELECT reservation_id FROM ".TABLE_RESERVATION." WHERE user_id = '".$_SESSION['user_id']."' ORDER BY id DESC LIMIT 1 ");	
	return (int)$reservation_mysql->result(0,'reservation_id');
}

function get_equipments_colors(){
	global $reservation_mysql;
	$colors = array();
	
	$reservation_mysql->query("SELECT * FROM  ".TABLE_EQUIPMENT." ORDER BY id");
	while($row = $reservation_mysql->fetch_array() ){
		$colors[$row['equipment_name']] = $row['color'];
	}
	
	return 	$colors;
}

/*
-finalna funkcia modulu get_/nazovamodulu/
*/
function get_reservation(){
	global $reservation_mysql;
	
	$events = get_events();
	$last_event_id = get_last_reservation_id();
	$event_id = ++$last_event_id;
	$admin =  get_user_info($_SESSION['user_id']);
	$user = $admin['name'].' '.$admin['surname'];
	$colors = get_equipments_colors();
	
	$reservationHTML = "";
	$equipmentsHTML = "";
	
	//preklady v js
	$sk_days = array('Nedela','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota');
	$en_days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	if($_SESSION['language'] == 'sk') 
		$longdays = '"'.implode('","',$sk_days).'"';
	else
		$longdays = '"'.implode('","',$en_days).'"';
	
	$reservationHTML = '<script type="text/javascript">';
	$reservationHTML .= 'var id = '.$event_id.';';
	$reservationHTML .= 'var user = "'.$user.'";';
	$reservationHTML .= 'var eventDataFilter = [];';
	//tu su vsetky data bez ohladu zariadenia a uzivatela (iba tie ktore prusluchaju prihlasenemu uzivatelovi sa daju editovat...)
	$reservationHTML .= 'var eventData = {events: '.$events.'};';
	$reservationHTML .= 'eventDataFilter["own"] = {events: '.get_events("own").'};';
	foreach($colors as $equip => $color){
		//vytiahneme si data pre jednotlive zariadenie
		$reservationHTML .= 'eventDataFilter["'.$equip.'"] = {events: '.get_events($equip).'};';
		//fraby pre jednotlive zariadenia
		$reservationHTML .= 'var '.$equip.'_color = "'.$color.'";';
	}
	
	//preklady ktore sa vsujvau do js
	$reservationHTML .= 'var days = ['.$longdays.'];';
	$reservationHTML .= 'var buttonToday = "'.BUTTON_TODAY.'";';
	$reservationHTML .= 'var buttonLastWeek = "'.BUTTON_LAST_WEEK.'";';
	$reservationHTML .= 'var buttonNextWeek = "'.BUTTON_NEX_WEEK.'";';
	$reservationHTML .= 'var titleAddNewReservation = "'.ADD_NEW_RESERVATION.'";';
	$reservationHTML .= 'var titleEditReservation = "'.RESERVATION_EDIT_TILE.'";';
	$reservationHTML .= 'var saveTitle = "'.RESERVATION_EDIT_TILE.'";';
	$reservationHTML .= 'var buttonSaveReservation = "'.BUTTON_SAVE_RESERVATION.'";';
	$reservationHTML .= 'var buttonCancelReservation = "'.BUTTON_CANCEL_RESERVATION.'";';
	$reservationHTML .= 'var buttonDeleteReservation = "'.BUTTON_DELETE_RESERVATION.'";';
	$reservationHTML .= '</script>';
	
		
	
	$reservationHTML .= '<link href="'.FULL_MODULES_PATH.RESERVATION_MODUL_NAME.'/css/jquery.weekcalendar.css" rel="stylesheet" type="text/css" />';	
	$reservationHTML .= '<script src="'.FULL_MODULES_PATH.RESERVATION_MODUL_NAME.'/js/jquery.weekcalendar.js" type="text/javascript" /></script>';	
	$reservationHTML .= '<script src="'.FULL_MODULES_PATH.RESERVATION_MODUL_NAME.'/js/default.js" type="text/javascript" /></script>';	
	
	$reservationHTML .= file_get_contents(dirname(__FILE__).'/reservation.html');
	
	//parsnenme preklady
	$lang_texts = array(BUTTON_ALL_EQUIPMENTS,BUTTON_SAVE_CHANGES,BUTTON_SAVE_CHANGES_TITLE,RESERVATION_DATE,RESERVATION_USER,RESERVATION_EQUIPMENT,RESERVATION_START,RESERVATION_END_TIME,RESERVATION_STARTTIME_SELECTBOX,RESERVATION_ENDTIME_SELECTBOX,BUTTON_MY_RESERVATIONS);
	$lang_replace = array('%all_equip%','%save_changes%','%save_changes_title%','%date%','%user_title%','%equipment%','%start_time%','%end_time%','%selectbox_start%','%selectbox_end%','%my_reservations%');
	$reservationHTML = str_replace($lang_replace,$lang_texts,$reservationHTML); 
	
	//pridame prihlaseneho uzivatel
	$user = get_user_info($_SESSION['user_id']);
	$pattern = array('%user%','%user_hidden%');
	$replace = array($user['name'].' '.$user['surname'],$user['name'].' '.$user['surname']);
	$reservationHTML = str_replace($pattern,$replace,$reservationHTML); 
	
	//pridame zariadenie
	$reservation_mysql->query("SELECT * FROM ".TABLE_EQUIPMENT." ORDER BY id ");
	$equipmentsHTML .= "<select name='equipment'>";
	while($row = $reservation_mysql->fetch_array()){
		$equipmentsHTML .= "<option value='".$row['id']."' >".$row['equipment_name']."</option>";
	}
	$equipmentsHTML .= "</select>";
	
	
	$reservationHTML = str_replace('%equipment_select%',$equipmentsHTML,$reservationHTML); 
	
	return $reservationHTML; 
}

//widget functions
function get_reservations_for_widget(){
	global $reservation_mysql;
	$reservations = array();
	
	$reservationQuery =  $reservation_mysql->query("SELECT * FROM ".TABLE_RESERVATION." r INNER JOIN ".TABLE_EQUIPMENT." e ON (r.equipment = e.id)
								WHERE r.user_id = '".$_SESSION['user_id']."' 
								ORDER BY r.start ASC LIMIT 2 ");
	
	if($reservation_mysql->num_rows($reservationQuery) < 1)
		return false;
	else{
		while($row = $reservation_mysql->fetch_array($reservationQuery)){
			$reservations[] = $row;	
		}
		return $reservations;		
	}
}

/*
-finalna funkcia modulu pre widget get_/nazovamodulu/_widget
*/
function get_reservation_widget(){
	//info o module v root configu
	global $g_modules_array;
	
	$HTMLresponse = '';
	
	//$currentDirectory = array_pop(explode("/", getcwd()));
	$currentSrc = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
	$currentSrc = ltrim(preg_replace('/\\\\/', '/', $currentSrc), '/');
	$currentSrcArray = explode("/",$currentSrc); 
	array_pop($currentSrcArray);
	$currentSrc = '/'.implode("/",$currentSrcArray);
	$HTMLresponse .= '<link href="'.FULL_MODULES_PATH.RESERVATION_MODUL_NAME.'/css/default.css" rel="stylesheet" type="text/css" />';	
	
	if($userReservations = get_reservations_for_widget()){
		//var_dump($userReservations);
		$HTMLresponse.= '<table class="reservations">';
		$HTMLresponse .= '<tr class="header" ><th class="title">'.RESERVATION_EQUIPMENT.'</th><th class="start">'.RESERVATION_BEGINNING.'</th><th class="end" >'.RESERVATION_END.'</th></tr>';
		$rowCounter = 1;
		foreach($userReservations as $r){
		    
			$classLast = (strtotime($r['start']) < time()) ? 'past' : '' ;
			$classRow = ($rowCounter % 2 == 0) ? 'even' : 'uneven';
			
			$HTMLresponse .= '<tr class="'.$classLast.' '.$classRow.'" >';
			$HTMLresponse .=	'<td class="title" >'.(strlen($r['equipment_name']) ? $r['equipment_name'] : 'neuvedený').'</td>';
			$HTMLresponse .=    '<td class="start" >'. date('j.n Y G:i', strtotime($r['start']) ) .'</td>';
			$HTMLresponse .=    '<td class="end">'. date('j.n Y G:i', strtotime($r['end']) ) .'</td>';
			$HTMLresponse .= '</tr>';	
			$rowCounter++;
		}
		$HTMLresponse .= '</table>';
		//$HTMLresponse .= '<div><a style="padding:10px;float:right;" title="Pridať rezerváciu" href="'.ROOT_PATH.$g_modules_array['reservation']['access_key'].'">Pridať rezerváciu</a></div>';
		$HTMLresponse .= '<div style="float:left;padding:10px;" ><a href="'.ROOT_PATH.$g_modules_array['reservation']['access_key'].'" title="'.RESERVATION_ADD.'" >'.RESERVATION_ADD.'</a></div>';
	}else{
		$HTMLresponse .= '<div class="reservation_holder" >';
		$HTMLresponse .= NO_RESERVATION_TEXT.'<a href="'.ROOT_PATH.'dashboard.php?section_id=13'.'" title="'.RESERVATION_LEFT_PANEL_TITLE.'" >'.RESERVATION_LEFT_PANEL_TITLE.'</a>
						  ';
		$HTMLresponse .= '</div>';					  
	}
	
	return $HTMLresponse;
}

	
?>