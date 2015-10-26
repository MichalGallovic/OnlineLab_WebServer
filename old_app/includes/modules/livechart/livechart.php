<?php
//vsetko potrenbne co treb nacitat je v init.php
require_once('init.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

//globalna premenna pre vlastnosti grafu
$chartSettings = array();

function setChartSettings(){
	global $db_handler, $chartSettings;
	
	$result = $db_handler->query("SELECT * FROM ".TABLE_ADMIN_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
	if($db_handler->num_rows() < 1){
		$db_handler->query("INSERT INTO ".TABLE_ADMIN_USER_SETTINGS." (user_id) VALUES ('".$_SESSION['user_id']."')  ");
		$db_handler->query("SELECT * FROM ".TABLE_ADMIN_USER_SETTINGS." WHERE user_id = '".$_SESSION['user_id']."' ");
		$chartSettings = $db_handler->fetch_array();
	}else{
		$chartSettings = $db_handler->fetch_array($result);
	}
	
	return $chartSettings;
}

function renderPanelMenu($chartSet){
	$response = '';
	
	$xTitleShowChcecked = ($chartSet['xTitleShow'] == 1 ? 'checked = checked' : '' );
	$xTitleShowValue = ($chartSet['xTitleShow'] == 1 ? 'Časová os' : '' );
	
	$yTitleShowChcecked = ($chartSet['yTitleShow'] == 1 ? 'checked = checked' : '' );
	$yTitleShowValue = ($chartSet['yTitleShow'] == 1 ? 'Namerané hodnoty' : '' );
	
	$mainTitleShow = ($chartSet['mainTitleShow'] == 1  ? 'checked = checked' : '');
	$mainTitleShowText = ($chartSet['mainTitleShow'] == 1 ? $chartSet['mainTitleText'] : '');
	
	$subTitleShow = ($chartSet['subTitleShow'] == 1  ? 'checked = checked' : '');
	$subTitleText = ($chartSet['subTitleShow'] == 1 ? $chartSet['subTitleText'] : '');
	
	$showLegendChecked = ($chartSet['showLegend'] == 1 ? 'checked = checked' : '');
	$legendShow = ($chartSet['showLegend'] == 1 ? 'true' : 'false');
	
	$showMenuChecked = ($chartSet['showMenu'] == 1 ? 'checked = checked' : '');
	$showMenu = ($chartSet['showMenu'] == 1 ? true : false);
	
	$response = '<div id="panel-menu" title="'. GRAPH_SETTINGS .'">
					<form action="" method="post" enctype="multipart/form-data">
						<table class="chart-settings" CELLSPACING="5" >
							<tr>
								<th colspan="2" style="width:150px;" ><span class="bold">'.WINDOW_DIMENSIONS.'</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">'. X_AXIS .'</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">'. Y_AXIS .'</span></th>
							</tr>
							<tr>
								<td><label for="dialog-chartWidth">'.GRAPH_WIDTH.':</label></td>
								<td><input id="dialog-chartWidth" type="text" value="'.$chartSet['width'].'" name="chartWidth" /><span> px</span></td>
								
								<td colspan="2" >
									<input type="checkbox" id="dialog-chartXdisplay" value="1"  '.$xTitleShowChcecked.' name="chartXdisplay" />
									<label for="dialog-chartXdisplay">'. X_AXIS_SHOW_TITLE .'</label>
								</td>
								
								<td colspan="2" >
									<input type="checkbox" id="dialog-chartYdisplay" value="1" '.$yTitleShowChcecked.'  name="chartYdisplay" />
									<label for="dialog-chartYdisplay">'. Y_AXIS_SHOW_TITLE .'</label>
								</td>
							</tr>
							<tr>
								<td><label for="dialog-chartHeight">'.GRAPH_HEIGHT.':</label></td>
								<td><input id="dialog-chartHeight" type="text" value="'.$chartSet['height'].'" name="chartHeight" /><span> px</span></td>
							</tr>
							<tr>
								<th colspan="2" style="width:150px;" ><span class="bold">'.MAIN_TITLE.'</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">'.SUB_TITLE.'</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">'.MENU_AND_LEGEND_TITLE.'</span></th>
							</tr>
							<tr>
								<td colspan="2">
									<input type="text" name="mainTitleText" value="'.$chartSet['mainTitleText'].'" style="width:142px;" />
								</td>
								<td colspan="2">
									<input type="text" name="subTitleText" value="'.$chartSet['subTitleText'].'" style="width:142px;" />
								</td>
								<td colspan="2">
									<input type="checkbox" id="dialog-showLegend" value="1" '.$showLegendChecked.' name="showLegend" />
									<label for="dialog-showLegend">'.SHOW_LEGEND.'</label>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="checkbox" id="dialog-mainTitleShow" value="1" '.$mainTitleShow.' name="mainTitleShow" />
									<label for="dialog-mainTitleShow">'. MAIN_TITLE_SHOW .'</label>
								</td>
								<td colspan="2">
									<input type="checkbox" id="dialog-subTitleShow" value="1" '.$subTitleShow.' name="subTitleShow" />
									<label for="dialog-subTitleShow">'.SUB_TITLE_SHOW.'</label>	
								</td>
								<td colspan="2">
									<input type="checkbox" id="dialog-showMenu" value="1" '.$showMenuChecked.' name="showMenu" />
									<label for="dialog-showMenu">'. SHOW_MENU .'</label>	
								</td>
							</tr>
							
							<tr>
								<td colspan="6">
									<input type="submit" value="'.SAVE_CHANGES_BUTTON.'">
									<input type="hidden" value="1" name="updateChartSettings">
								</td>
							</tr>
						</table>
					</form>
				</div>';
	
	return $response;
}

function renderChart($chartSet){}


function renderNewChart($chartSet){
	$response = '';
	
	$xTitleShowChcecked = ($chartSet['xTitleShow'] == 1 ? 'checked = checked' : '' );
	$xTitleShowValue = ($chartSet['xTitleShow'] == 1 ? 'Časová os' : '' );
	
	$yTitleShowChcecked = ($chartSet['yTitleShow'] == 1 ? 'checked = checked' : '' );
	$yTitleShowValue = ($chartSet['yTitleShow'] == 1 ? 'Namerané hodnoty' : '' );
	
	$mainTitleShow = ($chartSet['mainTitleShow'] == 1  ? 'checked = checked' : '');
	$mainTitleShowText = ($chartSet['mainTitleShow'] == 1 ? $chartSet['mainTitleText'] : '');
	
	$subTitleShow = ($chartSet['subTitleShow'] == 1  ? 'checked = checked' : '');
	$subTitleText = ($chartSet['subTitleShow'] == 1 ? $chartSet['subTitleText'] : '');
	
	$showLegendChecked = ($chartSet['showLegend'] == 1 ? 'checked = checked' : '');
	$legendShow = ($chartSet['showLegend'] == 1 ? 'true' : 'false');
	
	$showMenuChecked = ($chartSet['showMenu'] == 1 ? 'checked = checked' : '');
	$showMenu = ($chartSet['showMenu'] == 1 ? true : false);
	
	//VYSKA BEZ DRAG LISTY
	$newHeight = $chartSet['height'] - 30;
	//ak nie je menu tak pridame este 50px ktore zaberalo
	if($showMenu) $newHeight = $chartSet['height'] - 30 - 50;		
	
	$response .= '<script type="text/javascript">';
	
	/*$response .= '
		$(document).ready(function() {
			$( "#livechart-modul" ).draggable({ 
				containment: "#containment-wrapper",
				handle: "div.header",
				grid: [ 5,5 ],
				stop: function(event, ui) { 
					var position = $(\'#livechart-modul\').position();
					var postString = \'left=\' + position.left +\'&top=\' + position.top + \'&action=updateChartPosition\';
					$.post("/miso/updateDB.php", postString, function(theResponse){});
				}
				
			});
		 });';*/
	
	$response .= 'var options = {
		xAxis: {
			/*type: \'datetime\',*/
			minPadding: 0.05,
			maxPadding: 0.05,
			title: {
				text: "'.$xTitleShowValue.'",
				margin:10
			}
		},
		yAxis: {
			minPadding: 0.2,
			maxPadding: 0.2,
			title: {
				text: "'.$yTitleShowValue.'",
				margin: 10
			}
		},title: {
        	text: "'.$mainTitleShowText.'",
			align: "left",
			x: 20
		},
		subtitle: {
        	text: "'.$subTitleText.'",
			align: "left",
			x: 23,
			y: 35	
		},
		tooltip: {
			formatter: function() {
					return "<b>"+ this.series.name +"</b> <br/>"+
					"Čas:" + this.x +"s<br/>Hodnota:"+ this.y;
			}
		},
		legend: {
			 layout: "vertical",
			 align: "right",
			 verticalAlign: "top",
			 x: 0,
			 y: 20,
			 borderWidth: 0,
			 enabled: '.$legendShow.'
		}
	};
	
	chart = Highcharts.setOptions(options);';
	$response .= '</script>';
	
	//$response .= '<div id="livechart-modul" class="default-box" style="width:auto;"  >';
	
	//panel menu
	$response .= '
	<div class="panel">
		<div class="title-bar">
			<h3>'. GRAPH_SETTINGS .'</h3>
			<a class="close-panel-btn" title="zavrieť okno"></a>
			<a href="#" onclick="open_menu()" title="otvoriť do nového okna" class="new-window-btn" ></a>
		</div>
		<div class="content">
			<form action="" method="post" enctype="multipart/form-data">
				<table CELLSPACING="5" >
					<tr>
						<th colspan="2" style="width:150px;" ><span class="bold">'. WINDOW_DIMENSIONS .'</span></th>
						<th colspan="2" style="width:150px;" ><span class="bold">'. X_AXIS .'</span></th>
						<th colspan="2" style="width:150px;" ><span class="bold">'. Y_AXIS .'</span></th>
					</tr>
					<tr>
						<td>
							<label for="chartWidth">'. GRAPH_WIDTH .':</label>
							
						</td>
						<td >
							<input id="chartWidth" type="text" value="'.$chartSet['width'].'" name="chartWidth" /><span> px</span>
							<div>
						</td>
						
						<td colspan="2" >
							<input type="checkbox" id="chartXdisplay" value="1"  '.$xTitleShowChcecked.' name="chartXdisplay" />
							<label for="chartXdisplay">'. X_AXIS_SHOW_TITLE .'</label>
						</td>
						
						<td colspan="2" >
							<input type="checkbox" id="chartYdisplay" value="1" '.$yTitleShowChcecked.'  name="chartYdisplay" />
							<label for="chartYdisplay">'. X_AXIS_SHOW_TITLE .'</label>
						</td>
					</tr>
					<tr>
						<td><label for="chartHeight">'. GRAPH_HEIGHT .':</label></td>
						<td><input id="chartHeight" type="text" value="'.$chartSet['height'].'" name="chartHeight" /><span> px</span></td>
					</tr>
					<tr>
						<th colspan="2" style="width:150px;" ><span class="bold">'. MAIN_TITLE .'</span></th>
						<th colspan="2" style="width:150px;" ><span class="bold">'. SUB_TITLE .'</span></th>
						<th colspan="2" style="width:150px;" ><span class="bold">'. MENU_AND_LEGEND_TITLE .'</span></th>
					</tr>
					<tr>
						<td colspan="2">
							<input type="text" name="mainTitleText" value="'.$chartSet['mainTitleText'].'" style="width:142px;" />
						</td>
						<td colspan="2">
							<input type="text" name="subTitleText" value="'.$chartSet['subTitleText'].'" style="width:142px;" />
						</td>
						<td colspan="2">
							<input type="checkbox" id="showLegend" value="1" '.$showLegendChecked.' name="showLegend" />
							<label for="showLegend">'. SHOW_LEGEND .'</label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="checkbox" id="mainTitleShow" value="1" '.$mainTitleShow.' name="mainTitleShow" />
							<label for="mainTitleShow">'. MAIN_TITLE_SHOW .'</label>
						</td>
						<td colspan="2">
							<input type="checkbox" id="subTitleShow" value="1" '.$subTitleShow.' name="subTitleShow" />
							<label for="subTitleShow">'. SUB_TITLE_SHOW .'</label>	
						</td>
						<td colspan="2">
							<input type="checkbox" id="showMenu" value="1" '.$showMenuChecked.' name="showMenu" />
							<label for="showMenu">'. SHOW_MENU .'</label>	
						</td>
					</tr>
					
					<tr>
						<td colspan="6">
							<input type="submit" value="'.SAVE_CHANGES_BUTTON.'">
							<input type="hidden" value="1" name="updateChartSettings">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>';
	
	//div pre info vypisi
	$response .= '<div id="info-chart" class="info-chart">'.INFO_MESSAGE_ON_MEASURMENT_PAUSE.'</div>';
	
	$response .= '<div id="stop-chart" class="info-chart">'.INFO_MESSAGE_ON_MEASURMENT_STOP.'</div>';
	
	
	//chart loader
	$response .= '<div id="chart_ajax_loader"></div>';
	
	//overlay
	$response .= '<div class="chart-overlay"></div>';
	
	//$response .= 	'<div class="header"></div>';
		
	$response .= 	'<div class="box-content" style="width:'.$chartSet['width'].'px;height:'.$newHeight.'px;position:relative;" >';
	$response .= 		'<a href="javascript:void(0);" title="'. MENU_BUTTON_TITLE .'" class="settings_button"></a>';
	$response .= 		'<div id="chart_container" style="width:'.$chartSet['width'].'px;height:'.$newHeight.'px;"></div>';
	$response .= 		'<div id="intro" ></div>';
	
	$response .= 	'</div>';
	
	//<li><a id="start-simulation" class="default-btn" href="javascript:void(0);"><span>start</span></a></li>
	
	if($showMenu)
	$response .= '<div id="chart-menu">
						<div >
						<ul>
							<li>
								<a id="stop-continue-btn" class="default-btn" href="javascript:void(0);">
									<span id="stop-measurement" >'. STOP_BUTTON .'</span>
									<span id="continue-measurement" style="display:none;" >'. CONTINUE_BUTTON .'</span>
								</a>
							</li>
							
							<li><a id="reset-zoom" class="default-btn " href="javascript:void(0);"><span class="zoom-ico">'. ZOOM_BUTTON .'</span></a></li>
							<li><a id="zoom-1" class="default-btn" href="javascript:void(0);"><span>1x</span></a></li>
							<li><a id="zoom-2" class="default-btn" href="javascript:void(0);"><span>2x</span></a></li>
							<li><a id="backward" class="default-btn" href="javascript:void(0);"><span>&nbsp;<&nbsp;</span></a></li>
							<li><a id="forward" class="default-btn" href="javascript:void(0);"><span>&nbsp;>&nbsp;</span></a></li>
							<li class="stop"><a id="button_stop_sim" class="default-btn" href="javascript:void(0);"><span>'. MANUALY_STOP_BUTTON .'</span></a></li>
						</ul>
						</div>
					</div>';
	
	//$response .= '</div>';
	
	$response .= renderPanelMenu($chartSet);
	return $response;
}

/*
-finalna funkcia modulu get_/nazovamodulu/
*/
function get_livechart(){
	$response = '';
	
	$currentSrc = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
	$currentSrc = ltrim(preg_replace('/\\\\/', '/', $currentSrc), '/');
	$currentSrcArray = explode("/",$currentSrc); 
	array_pop($currentSrcArray);
	$currentSrc = '/'.implode("/",$currentSrcArray);
	
	//$currentDirectory = array_pop(explode("/", getcwd()));
	
	$response =  '<link href="'.FULL_MODULES_PATH.LIVECHART_MODUL_NAME.'/css/style.css" rel="stylesheet" type="text/css" />';	
	$response .= '<script src="'.FULL_MODULES_PATH.LIVECHART_MODUL_NAME.'/js/highcharts.js" type="text/javascript" /></script>';	
	$response .= '<script src="'.FULL_MODULES_PATH.LIVECHART_MODUL_NAME.'/js/exporting.js" type="text/javascript" /></script>';	
	$response .= '<script src="'.FULL_MODULES_PATH.LIVECHART_MODUL_NAME.'/js/grid.js" type="text/javascript" /></script>';	
	$response .= '<script src="'.FULL_MODULES_PATH.LIVECHART_MODUL_NAME.'/js/chart.js" type="text/javascript" /></script>';	
	
	
	$chartSet = setChartSettings();
	
	//$response = renderChart($chartSet);
	$response .= renderNewChart($chartSet);		
	 
    return $response;	
}


function getLiveChartWidget(){
	$response = '';
	
	$currentDirectory = array_pop(explode("/", getcwd()));
	$response =  '<link href="'.substr(dirname(__FILE__),strpos(dirname(__FILE__),$currentDirectory)-1).'/css/style.css" rel="stylesheet" type="text/css" />';	
	$response .= '<script src="'.substr(dirname(__FILE__),strpos(dirname(__FILE__),$currentDirectory)-1).'/js/highcharts.js" type="text/javascript" /></script>';	
	$response .= '<script src="'.substr(dirname(__FILE__),strpos(dirname(__FILE__),$currentDirectory)-1).'/js/exporting.js" type="text/javascript" /></script>';	
	$response .= '<script src="'.substr(dirname(__FILE__),strpos(dirname(__FILE__),$currentDirectory)-1).'/js/grid.js" type="text/javascript" /></script>';	
	$response .= '<script src="'.substr(dirname(__FILE__),strpos(dirname(__FILE__),$currentDirectory)-1).'/js/chart.js" type="text/javascript" /></script>';	
	
	$chartSet = setChartSettings();
	$response .= renderWidgetChart($chartSet);
	
	return $response;		
}



			


?>