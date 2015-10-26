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

if (isset($_REQUEST['action'])) {

    $action = $_REQUEST['action'];

    switch ($action) {

        case 'get_rows':
            getRows();
            break;

        case 'add_row':
            addRow();
            break;

        case 'delete_row':
            deleteRow();
            break;

        case 'prepare_edit':
            prepare_edit();
            break;

        case 'save_edited':
            save_edited();
            break;

        default;
            break;
    }

    exit;
} else {
    return false;
}

function getRows() {
    
    global $mysql;

    $sql = $mysql->query("SELECT * FROM " . TABLE_EQUIPMENT . " ORDER BY id ");

    while ($row = $mysql->fetch_array())
        $data[] = $row;

    $formatted = '';

    if (empty($data)) {
        $formatted = '<tr>
                                        <td colspan="5" style="text-align:center;padding:20px;font-weight:bold;" >
                                                ' . NO_EQUIP . '
                                        </td>
                                </tr>';
        return $formatted;
    }

    foreach ($data as $id => $dat) {

        $actionLinks = '<a href="javascript:void(0);" onclick="edit_equip(' . $dat['id'] . ');" class="settings" title="' . EQ_SETTINGS_TITLE . '"></a>';
        $actionLinks .= '<a href="javascript:void(0);" onclick="delete_equipment(' . $dat['id'] . ');" class="trash" title="' . EQ_TRASH_TITLE . '"></a>';


        if ($id % 2 == 0)
            $rowClass = '';
        else
            $rowClass = 'alternate';
        $formatted .= '<tr id="row-' . $dat['id'] . '" class="' . $rowClass . '" > 
                                            <td>' . $dat['id'] . '</td>
                                            <td>' . $dat['equipment_name'] . '</td>
                                            <td>' . $dat['ip'] . '</td>
                                            <td><div style="background-color:' . $dat['color'] . ' ">' . $dat['color'] . '</div></td>
                                            <td>' . $actionLinks . '</td>
                                    </tr>';
    }
    echo $formatted;
}



/**
 * AJAXova fcia na pridanie noveho realneho devicu
 */
function addRow() {

    // ak sme prijali ze form bol odoslany
    if (isset($_POST['add_equip']) and $_POST['add_equip'] == 1) {

        global $mysql;

        // osetrenie voci debilnym znakom
        $name       = $mysql->escape($_POST['name']);
        $ip         = $mysql->escape($_POST['ip']);
        $colour     = $mysql->escape($_POST['colour']);

        foreach ($_POST as $variableName => $value) {
            if (empty($value))
                $empty_fields[] = $variableName;

            $post_variables[$variableName] = trim($value);
        }

        if (!empty($empty_fields)) {
            echo json_encode(array('status' => -1, 'empty' => $empty_fields, 'msg' => 'Vyplňte prosím vyznačené polia.'));
            exit();
        }

        $result = $mysql->query("INSERT INTO " . TABLE_EQUIPMENT . " (equipment_name, ip, color) 
                                    VALUES('" . $name . "', '" . $ip . "', '" . $colour . "')");

        if ($result) {
            echo json_encode(array('status' => 1, 'msg' => 'Ok.'));
        }
    }
}



/**
 * Funkcia zmaze realne zariadenie
 */
function deleteRow() {
    if (isset($_POST['delete_equipment']) and $_POST['delete_equipment'] == 1) {
        global $mysql;
        $mysql->query("DELETE FROM " . TABLE_EQUIPMENT . " WHERE id = " . $_POST['equipmentId']);
    }
}



/**
 * FUnkcia vytiahne data o aktualne upravovanom realnom zariadeni
 */
function prepare_edit() {
    
    if (isset($_POST['eqp_settings']) and $_POST['eqp_settings'] == 1) {
        
        global $mysql;
        
        $eqpID = $_POST['eqpID'];

        $mysql->query("SELECT * FROM ".TABLE_EQUIPMENT." WHERE id = ".$eqpID);

        $name   = $mysql->result(0, 'equipment_name');
        $ip     = $mysql->result(0, 'ip');
        $colour = $mysql->result(0, 'color');

        echo json_encode(array(
            'eqp_id'        => $eqpID,
            'eqp_name'      => $name,
            'eqp_ip'        => $ip,
            'eqp_colour'    => $colour
        ));
    }
}



/**
 * Funkcia ulozi zmeny realneho zariadenia
 */
function save_edited() {
    
    if (isset($_POST['equip_change_settings']) and $_POST['equip_change_settings'] == 1) {

        global $mysql;

        sleep(2);

        $equip_id       = $_POST['equip_id'];
        $equip_name     = $mysql->escape($_POST['settings_equip_name']);
        $equip_ip       = $mysql->escape($_POST['settings_equip_ip']);
        $equip_colour   = $mysql->escape($_POST['settings_equip_colour']);
        
        $result = $mysql->query("UPDATE ".TABLE_EQUIPMENT." SET equipment_name='".$equip_name."', ip = '".$equip_ip."',color = '".$equip_colour."' WHERE id= ".$equip_id);

        if ($result) {
            echo json_encode(array('status' => 1, 'msg' => 'Ok'));
        }
        else
            echo json_encode(array('status' => -1, 'msg' => 'Error'));
    }
}

?>
