<?php

$tmp->define(array("left" => "column_left.htm"));

// skupina pre ktoru leftbox nechceme
if(strpos($_SERVER['PHP_SELF'], "rshop-index.php") === false){
	
}

$tmp->parse("LEFT",".left"); 
$fetch_content = $tmp->fetch("LEFT"); 
$main_tpl->assign(array("MAIN_LEFT_CONTENT" => $fetch_content));

?>