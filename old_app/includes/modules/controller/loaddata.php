<?php

session_start();

require_once('../../config.php');
require_once('../../db_tables.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/' . $_SESSION['language'] . '.php');

require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);

//naciame config modulu
require('config.php');

//textova podoba stupna prava prioritny/verejny/privatny
$accessibility = array(0 => ACCES_PRIORITNY, 1 => ACCES_VEREJNY, 2 => ACCES_PRIVATNY);


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
        'host' => DB_SERVER,
        'login' => DB_SERVER_USERNAME,
        'password' => DB_SERVER_PASSWORD,
        'database' => DB_DATABASE,
    );

    $link = mysql_connect($db['host'], $db['login'], $db['password']);
    if (!$link) {
        exit;
    }

    mysql_select_db($db['database']);

    $strSQL = "SELECT COUNT(*) AS count FROM " . TABLE_COTROLLERS . " WHERE user_id = " . $_SESSION['user_id'] . " or permissions = 0 or permissions = 1 ";

    $result = mysql_query($strSQL);
    $count = mysql_fetch_row($result);

    echo $count[0];
}

function getRows() {
    $start_row = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
    $start_row = ROWS_PER_PAGE * (int) $start_row;

    $dataRows = loadRows($start_row);

    $formatted_employees = formatData($dataRows);

    echo $formatted_employees;
}

function loadRows($start_row = 0) {
    $db = array(
        'host' => DB_SERVER,
        'login' => DB_SERVER_USERNAME,
        'password' => DB_SERVER_PASSWORD,
        'database' => DB_DATABASE,
    );

    $link = mysql_connect($db['host'], $db['login'], $db['password']);
    if (!$link) {
        exit;
    }

    $strSQL = "SELECT u.name as uname,u.surname,e.equipment_name,c.name,c.date,c.permissions,c.id,c.user_id FROM  " . TABLE_COTROLLERS . " c 
					INNER JOIN " . TABLE_EQUIPMENT . " e ON (c.equipment_id = e.id)  
					INNER JOIN " . TABLE_ADMIN_USERS . " u ON (c.user_id = u.id)
				WHERE c.user_id  = " . $_SESSION['user_id'] . " OR c.permissions = 0 OR c.permissions = 1
				ORDER BY c.date DESC LIMIT {$start_row}, " . ROWS_PER_PAGE . " ";


    $result = mysql_query($strSQL);

    $rows = array();

    while ($row = mysql_fetch_assoc($result)) {
        $rows[] = $row;
    }


    return $rows;
}

function formatData($data) {
    global $accessibility;
    $formatted = '';

    if (empty($data)) {
        $formatted = '<tr>
							<td colspan="5" style="text-align:center;padding:20px;font-weight:bold;" >
								' . RT_NO_EXPERIEMNTS . '
							</td>
						</tr>';
        return $formatted;
    }

    foreach ($data as $id => $dat) {

        $actionLinks = '<a href="javascript:void(0);" onclick="show_reg(' . $dat['id'] . ');" class="preview" title="' . PREVIEW_TITLE . '"></a>';
        if ($dat['user_id'] == $_SESSION['user_id']) {
            $actionLinks .= '<a href="javascript:void(0);" onclick="settings_reg(' . $dat['id'] . ');" class="settings" title="' . SETTINGS_TITLE . '"></a>';
            $actionLinks .= '<a href="javascript:void(0);" onclick="delete_reg(' . $dat['id'] . ');" class="trash" title="' . TRASH_TITLE . '"></a>';
        }

        if ($id % 2 == 0)
            $rowClass = '';
        else
            $rowClass = 'alternate';
        $formatted .= '<tr id="row-' . $dat['id'] . '" class="' . $rowClass . '" > 
								<td>' . $dat['id'] . '</td>
								<td>' . $dat['name'] . '</td>
								<td>' . $dat['equipment_name'] . '</td>
								<td>' . $dat['uname'] . ' ' . $dat['surname'] . '</td>
								<td>' . $accessibility[$dat['permissions']] . '</td>
								<td>' . $actionLinks . '</td>
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