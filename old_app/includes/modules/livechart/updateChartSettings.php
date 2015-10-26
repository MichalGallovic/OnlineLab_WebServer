<?php 

if(isset($_POST['updateChartSettings'])){
	
	$chartWitdth = $db_handler->escape(trim($_POST['chartWidth']));
	$chartHeight = $db_handler->escape(trim($_POST['chartHeight']));
	$chartXdisplay = $db_handler->escape(trim($_POST['chartXdisplay']));
	$chartYdisplay = $db_handler->escape(trim($_POST['chartYdisplay']));
	$mainTitleShow = $db_handler->escape(trim($_POST['mainTitleShow']));
	$mainTitleText = $db_handler->escape(trim($_POST['mainTitleText']));
	$subTitleShow = $db_handler->escape(trim($_POST['subTitleShow']));
	$subTitleText = $db_handler->escape(trim($_POST['subTitleText']));
	$showLegend = $db_handler->escape(trim($_POST['showLegend']));
	$showMenu = $db_handler->escape(trim($_POST['showMenu']));
	
	$result = $db_handler->query("UPDATE ".TABLE_ADMIN_USER_SETTINGS." 
									SET width =       		'".$chartWitdth."',
										height=				'".$chartHeight."',
										xTitleShow = 		'".$chartXdisplay."',
										yTitleShow = 		'".$chartYdisplay."', 
										mainTitleShow = 	'".$mainTitleShow."',
										mainTitleText = 	'".$mainTitleText."',
										subTitleShow = 		'".$subTitleShow."',
										subTitleText = 		'".$subTitleText."',
										showLegend = 		'".$showLegend."',
										showMenu = 			'".$showMenu."'
										
								WHERE user_id = '".$_SESSION['user_id']."' ");
	
	if($result){
		header('Location: ? ');
		exit();
	}
}



?>
