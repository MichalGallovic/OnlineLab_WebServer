<?php

session_start();
//vsetko potrenbne co treb nacitat je v init.php
//require_once('init.php');

require_once('../../config.php');
require_once('../../db_tables.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/' . $_SESSION['language'] . '.php');

require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);

$session_user_id = 'user_id';

if (isset($_POST['action'])) {
	$action = $_POST['action'];

	// Nasledujuci kod zakomentovany # bol nahradeny z dovodu zlej manipulacie
	// s casom na roznych OS. Nizsie je vypis, ako vypada $_POST hodnota datumu 
	// z formulara z roznych OS a pod nimi je vysledok tvaru, ktory chceme dosiahnut
	// 
	// LINUX:	Sat Jun 28 2014 14:30:00 GMT+0200
	// WINDOWS:	Sat Jun 28 2014 14:30:00 GMT+0200 (Central Europe Standard Time)
	// ZIADANE:	Sat Jun 28 2014 14:30:00
	$piecesStart = explode(" ", $_POST['start']);
	$piecesEnd = explode(" ", $_POST['end']);

	for ($i = 0; $i < 5; $i++) {
		$novyStart[] = $piecesStart[$i];
		$noveEnd[] = $piecesEnd[$i];
	}
	$start = implode(" ", $novyStart);
	$end = implode(" ", $noveEnd);

	#$start = trim(preg_replace('/GMT\+0200|GMT\+0100/', '', $_POST['start']));
	#$end = trim(preg_replace('/GMT\+0200|GMT\+0100/', '', $_POST['end']));

	$start = date('Y-m-d H:i:00', strtotime($mysql->escape($start)));
	$end = date('Y-m-d H:i:00', strtotime($mysql->escape($end)));

	$reservation_id = $mysql->escape($_POST['reservation_id']);
	$body = '';

	//koli noticom podmienky
	if (isset($_POST['title']))
		$title = $mysql->escape($_POST['title']);
	if (isset($_POST['body']))
		$body = $mysql->escape($_POST['body']);
	$equipment = $mysql->escape($_POST['equipment']);

	switch ($action) {
		case 'save':
			$mysql->query('INSERT INTO ' . TABLE_RESERVATION . ' (reservation_id,user_id,equipment ,start,end, body , title ) 
								VALUES ("' . $reservation_id . '", 
										"' . $_SESSION[$session_user_id] . '",
										"' . $equipment . '" ,
										"' . $start . '", 
										"' . $end . '",  
										"' . $body . '", 
										"' . $title . '" ) ');
			break;

		case 'delete':
			$mysql->query('DELETE FROM ' . TABLE_RESERVATION . ' WHERE  reservation_id = "' . $reservation_id . '" and  user_id = "' . $_SESSION[$session_user_id] . '" ');
			break;
		case 'update':
			$mysql->query('UPDATE ' . TABLE_RESERVATION . ' 
									SET title = "' . $title . '",
										body  = "' . $body . '",
										start = "' . $start . '",
										end   = "' . $end . '",
										equipment = "' . $equipment . '"
									WHERE  reservation_id = "' . $reservation_id . '" and  user_id = "' . $_SESSION[$session_user_id] . '" ');

			break;
		case 'check' :

			//ak je z minulosti rovno koncime
			$oldTimeZone = date_default_timezone_get();
			date_default_timezone_set('Europe/Bratislava');
			if (strtotime($start) < strtotime(date('Y-m-d H:i:00'))) {
				echo json_encode(array("msg" => "Nie je možné pridávať rezerváciu pre dátum z minulosti.", "status" => 1));
				exit;
			}
			date_default_timezone_set($oldTimeZone);

			//zisakme si unikatne id zazname aby sme dosiahli kontorlu vsetkych datumov okrem datumu tohto eventu
			$mysql->query("SELECT id FROM " . TABLE_RESERVATION . "  WHERE  reservation_id = '" . $reservation_id . "' and  user_id = '" . $_SESSION[$session_user_id] . "'  ");
			$row_id = $mysql->result(0, 'id');
			$mysql->query("SELECT * FROM " . TABLE_RESERVATION . " 
										WHERE (start BETWEEN '" . $start . "' and '" . $end . "' OR end BETWEEN '" . $start . "' and '" . $end . "' )  AND 
											   equipment = " . $equipment . " AND 
											   id != '" . $row_id . "'  ");

			//var_dump("SELECT * FROM ".TABLE_RESERVATION." WHERE (start BETWEEN '".$start."' and '".$end."' OR end BETWEEN '".$start."' and '".$end."')  AND equipment = ".$equipment." AND id != '". $row_id ."'  ");
			//exit;
			//ak sme nasli take
			if ($mysql->num_rows() > 0)
				echo json_encode(array("msg" => "V tomto čase už máte dane zariadenie rezervované. Skúste iný dátum alebo iné zariadenie.", "status" => 1));
			else
				echo json_encode(array("msg" => "", "status" => 0));
			break;
		default:
			exit;
	}
}
?>
