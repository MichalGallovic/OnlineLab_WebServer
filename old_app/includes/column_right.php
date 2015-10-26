<?php

$tmp->define(array("right" => "column_right.htm"));

// skupina pre ktoru rightbox nechceme
if(strpos($_SERVER['PHP_SELF'], "rshop-index.php") === false){
	
}

$tmp->parse("RIGHT",".right"); 
$fetch_content = $tmp->fetch("RIGHT"); 
$main_tpl->assign(array("MAIN_RIGHT_CONTENT" => $fetch_content));

?>