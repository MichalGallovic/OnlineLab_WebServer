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

        default;
            break;
    }

    exit;
} else {
    return false;
}


function getRows() {
    
    global $mysql;
    
    $sql = $mysql->query("SELECT * FROM ".TABLE_ADMIN_USERS." ORDER BY id ");
    
    while ($row = $mysql->fetch_array())
        $data[] = $row;

    $formatted = '';

    if (empty($data)) {
        $formatted = '<tr>
                            <td colspan="6" style="text-align:center;padding:20px;font-weight:bold;" >
                                    '.NO_USRS.'
                            </td>
                    </tr>';
        return $formatted;
    }

    foreach ($data as $id => $dat) {

        $actionLinks = '<a href="javascript:void(0);" onclick="edit_usr('.$dat['id'].');" class="settings" title="'.USR_SETTINGS_TITLE.'"></a>';
        $actionLinks .= '<a href="javascript:void(0);" onclick="delete_usr('.$dat['id'].');" class="trash" title="'.USR_TRASH_TITLE.'"></a>';


        if ($id % 2 == 0)
            $rowClass = '';
        else
            $rowClass = 'alternate';
        
        $formatted .= '<tr id="row-'.$dat['id'].'" class="' . $rowClass . '" > 
                                <td>'.$dat['id'].'</td>
                                <td>'.$dat['login'].'</td>
                                <td>'.$dat['name'].' '.$dat['surname'].'</td>
                                <td>'.$dat['email'].'</td>';
        
                                // vytiahneme si uzivatelsku rolu
                                $rola = $mysql->query("SELECT nazov FROM ".TABLE_ADMIN_USER_ROLES." WHERE id=".$dat['role']);
                                $row = $mysql->fetch_array();

                                switch ($row['nazov']) {
                                    case "USER_ROLE_TITLE_ADMIN":
                                        $formatted .= '<td>'.USER_ROLE_TITLE_ADMIN.'</td>';
                                        break;

                                    case "USER_ROLE_TITLE_TEACHER":
                                        $formatted .= '<td>'.USER_ROLE_TITLE_TEACHER.'</td>';
                                        break;

                                    case "USER_ROLE_TITLE_DEVELOPER":
                                        $formatted .= '<td>'.USER_ROLE_TITLE_DEVELOPER.'</td>';
                                        break;

                                    case "USER_ROLE_TITLE_STUDENT":
                                        $formatted .= '<td>'.USER_ROLE_TITLE_STUDENT.'</td>';
                                        break;
                                }
                                $formatted .= '<td>'.$actionLinks . '</td>
                        </tr>';
    }
    echo $formatted;
}
?>
