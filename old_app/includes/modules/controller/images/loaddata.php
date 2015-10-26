<?php
	session_start();
	
	require_once('../../config.php');
	require_once('../../db_tables.php');
	
	//nacitanie jazyka, ktory sa handluje v application_top.php
	require_once('languages/'.$_SESSION['language'].'.php');
	
	require_once('../../classes/mysql.php');
	$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
	
	//naciame config modulu
	require('config.php');
	
	
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
		
		switch ($action) {
			case 'get_rows':
				getRows();
				break;
			case 'row_count':
				getRowCount();
				break;
			default;
				break;
		}
		
		exit;
	} else {
		return false;
	}
	
	function getRowCount() {
		$db = array(
			'host' => 'localhost',
			'login' => 'jenis',
			'password' => 'CePctsS6ZXSPsTJz',
			'database' => 'jenis',
		);
		
		$link = mysql_connect($db['host'], $db['login'], $db['password']);
		if (!$link) {
			exit;
		}
		
		mysql_select_db($db['database']);
		
		$strSQL = "SELECT COUNT(*) AS count FROM ".TABLE_REPORTS." WHERE user_id = ".$_SESSION['user_id']." ";
		
		$result = mysql_query($strSQL);
		$count = mysql_fetch_row($result);
		
		echo $count[0];
	}
	
	function getRows() {
		$start_row = isset($_REQUEST['start'])?$_REQUEST['start']:0;
		$start_row = ROWS_PER_PAGE * (int)$start_row;
		
		$dataRows = loadRows($start_row);
		
		$formatted_employees = formatData($dataRows) ;
		
		echo $formatted_employees;
	}
	
	function loadRows($start_row = 0) {
		$db = array(
			'host' => 'localhost',
			'login' => 'jenis',
			'password' => 'CePctsS6ZXSPsTJz',
			'database' => 'jenis',
		);
		
		$link = mysql_connect($db['host'], $db['login'], $db['password']);
		if (!$link) {
			exit;
		}
		   
		$strSQL ="SELECT e.equipment_name,r.regulator,r.report_date,r.id FROM  ".TABLE_REPORTS." r INNER JOIN ".TABLE_EQUIPMENT." e ON (r.equipment_id = e.id)  
				WHERE r.user_id  = ".$_SESSION['user_id']."
				ORDER BY r.report_date DESC LIMIT {$start_row}, ".ROWS_PER_PAGE." ";
		
		
		$result = mysql_query($strSQL);	
		
		$rows = array();
		
		while ($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		
		
		return $rows;
	}
	
	function formatData($data) {
		$formatted = '';
		
		if(empty($data)){
			$formatted = '<tr>
							<td colspan="5" style="text-align:center;padding:20px;font-weight:bold;" >
								'.RT_NO_EXPERIEMNTS.'
							</td>
						</tr>';
			return $formatted;
		}
		
		foreach ($data as $id => $dat) {
			if($id % 2 == 0)
				$rowClass = '';
			else
				$rowClass = 'alternate';	
			$formatted .= '<tr class="'.$rowClass.'" > 
								<td>' . $dat['id'] . '</td>
								<td>' . $dat['equipment_name'] . '</td>
								<td>' . $dat['regulator']. '</td>
								<td>' . date('j.n.Y H:i:s', strtotime($dat['report_date'])) . '</td>
								<td><a href="javascript:void(0);" onclick="get_report('.$dat['id'].');" class="preview" title="'.RT_SHOW_REPORT_ICO.'"></a></td>
							</tr>';
		}
		return $formatted;
	}
	
	function er($data) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
	
	function correct_encoding($text) {
    $current_encoding = mb_detect_encoding($text, 'auto');
    $text = iconv($current_encoding, 'UTF-8', $text);
    return $text;
}


?>