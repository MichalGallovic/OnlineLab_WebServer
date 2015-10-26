<?php 
//nacitanie konfiguracneho subora pre konkretny modul
require_once('config.php');

//nacitanie jazyka
require_once('languages/'.$_SESSION['language'].'.php');

//vytvorenie instancie pre pracu so sablonou pre modul
$equip_tpl = new rFastTemplate(MODUL_PATH);

//definovanie nazvu sablony v danom priecinku 
$equip_tpl->define(array("equipments" => "equipments.html"));

//intrepolacia globalnej konstanty ROOT_PATH do sablonovacej premennej ROOT_PATH
//je to dobre napriklad, ked potrebujeme zadefinovat uplnu cestu k css resp. js suborom
$equip_tpl->assign(array("ROOT_PATH"                        => ROOT_PATH,
                        "ADD_NEW_EQUIPMENT_TITLE"           => ADD_NEW_EQUIPMENT_TITLE,
                        "EQUIP_NAME"                        => EQUIP_NAME,
                        "EQUIP_IP"                          => EQUIP_IP,
                        "EQUIP_COLOUR"                      => EQUIP_COLOUR,
                        "ADD_DEVICE"                        => ADD_DEVICE,
                        "CLOSE_W"                           => CLOSE_W,
                        "DELETE_EQUIPMENT_QUESTION"         => DELETE_EQUIPMENT_QUESTION,
                        "TRASH_TITLE"                       => TRASH_TITLE,
                        "BACK_TO_EQUIPMENTS"                => BACK_TO_EQUIPMENTS,
                        "CHANGE_EQUIPMENT_SETTINGS"         => CHANGE_EQUIPMENT_SETTINGS,
                        "SAVE_CHANGE_EQUIPMENT_SETTINGS"    => SAVE_CHANGE_EQUIPMENT_SETTINGS));

//interpolacia celej sablony
$equip_tpl->parse("EQUIPMENTS", ".equipments");

//obsah celej sablony sme ulozily do php premennej
$fetch_module = $equip_tpl->fetch("EQUIPMENTS");

//standardna funckia pre modul
function get_equipments(){
	global $fetch_module;
	return $fetch_module; 
}

//standardna funckia pre obsah widget modulu
function get_equipments_for_widget(){
	global $fetch_module;
	return $fetch_module; 
}
?>
