<?php

//nested set functions
require_once(DIR_WS_CLASSES . 'nstrees.php');

$tmp->define(array("main_menu" => "boxes/main_menu.htm"));
$tmp->assign(array("ROOT_PATH" => ROOT_PATH));

$thandle['table'] = "categories";
$thandle['lvalname'] = "lft";
$thandle['rvalname'] = "rght";
//nstGetTree($thandle);

$main_cat = getMainCategories();
foreach($main_cat as $id => $cat){
	$tmp->assign(array("CAT_NAME" => $cat['categories_name'],"CAT_ID" => $cat['id'], "CAT_FILENAME" => $cat['filename']));
	$tmp->parse("HL_NAVI",".hlavna_navigacia");
}

$tmp->assign(array("TREE" => "" ));

$tmp->parse("MAIN_MENU",".main_menu"); 
$fetch_box = $tmp->fetch("MAIN_MENU");

?>
