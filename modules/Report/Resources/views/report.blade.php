<link href="{!! trans('ROOT_PATH ') !!}includes/modules/report/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/report/js/paginator.js"></script>
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/report/js/highcharts.js"></script>
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/report/js/exporting.js"></script>
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/report/js/grid.js"></script>
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/report/js/jquery.caret.js"></script>
<script type="text/javascript" >
	var intenzitaLabel = '{!! trans('INTENZITA_SERIE_LABEL ') !!}';
	var inputLabel = '{!! trans('INPUT_SERIE_LABEL ') !!}';
	var tempLabel = '{!! trans('TEMP_SERIE_LABEL ') !!}';
	var filTempLabel = '{!! trans('FIL_TEMP_SERIE_LABEL ') !!}';
	var filIntLabel = '{!! trans('FIL_LIGHT_INT_LABEL ') !!}';
	var currnetLabel = '{!! trans('CUR_SERIE_LABEL ') !!}';
	var rpmLabel = '{!! trans('RPM_SERIE_LABEL ') !!}';
	
	var personalNotesDefaultValue = '{!! trans('PEROSNAL_NOTES ') !!}';
	
	var rows_per_page = {!! trans('ROWS_PER_PAGE ') !!};
	var numberOfPagesDisplay = {!! trans('NUMBER_OF_PAGE_DISPLAY ') !!};
</script>
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/report/js/default.js"></script>
<script type="text/javascript" >
$(document).ready(function(){!! trans('  
	var options = {!! trans('
			xAxis: {!! trans('
				minPadding: 0.05,
				maxPadding: 0.05,
				title: {!! trans('
					text: '{!! trans('CHART_X_TITLE_SHOW ') !!}',
					margin:10
				 ') !!}
			 ') !!},
			yAxis: {!! trans('
				minPadding: 0.2,
				maxPadding: 0.2,
				title: {!! trans('
					text: '{!! trans('CHART_Y_TITLE_SHOW ') !!}',
					margin: 10
				 ') !!}
			 ') !!},title: {!! trans('
				text: "{!! trans('CHART_MAIN_TITLE_SHOW ') !!}",
				align: "left",
				x: 20
			 ') !!},
			subtitle: {!! trans('
				text: "{!! trans('CHART_SUBTITLE_SHOW ') !!}",
				align: "left",
				x: 23,
				y: 35	
			 ') !!},
			legend: {!! trans('
				 layout: "vertical",
				 align: "right",
				 verticalAlign: "top",
				 x: 0,
				 y: 50,
				 borderWidth: 0,
				 enabled: {!! trans('CAHRT_LEGEND ') !!}
			 ') !!}
		 ') !!};
	report_chart = Highcharts.setOptions(options);
	 ') !!});
</script>

<!-- BEGIN DYNAMIC BLOCK: chart_info_box -->
<div class="report_info_box">
Nastavenia grafu boli úspešne upravené.
</div>
<!-- END DYNAMIC BLOCK: chart_info_box -->
	
<div id="reports">
	<div id="reports-boxes">
		<div id="reports-boxes-wrapper">
			
				<div>
					<label><h5>{!! trans('REPORT_SETTINGS_SHOW_BOXES ') !!}:</h5></label>
					<label for="console_output">
						<input type="checkbox" {!! trans('CHECKBOX_CONSOLE ') !!} value="" id="console_output" name="console_box " />
						{!! trans('OUTPUT_BOX ') !!}
					</label>
					
					<label for="experiment_settings_output">
						<input type="checkbox" {!! trans('CHECKBOX_EXP_SETTINGS ') !!} value="" id="experiment_settings_output" name="input_experiment_settings_box" />
						{!! trans('EXPERIMENT_SETTINGS_BOX ') !!}
					</label>
					
					<label id="personal_notes">
						<input type="checkbox" {!! trans('CHECKBOX_NOTES ') !!} value="" id="personal_notes" name="personal_notes_box " />
						{!! trans('PERSONAL_NOTES_BOX ') !!}
					</label>
					
					
				</div>
			
		</div>
		<div id="reports-boxes-btn-wrapper">
			<a href="#" id="reports-open-options" title="">{!! trans('BUTTON_REPORT_SETTINGS ') !!}</a>
			<a href="#" id="reports-close-options" style="display:none;" title="">{!! trans('BUTTON_REPORT_SETTINGS ') !!}</a>
		</div>
	</div>
	
	<input type="hidden" name="page_count" id="page_count" />
	
	<div id="pager_holder"></div>
	<div id="report_container">
	<table class="reports" cellspacing="0">
		<thead>
			<tr>
				<th class="first">Id.</th>
				<th class="eqip">{!! trans('RT_SYSTEM ') !!}</th>
				<th class="reg">{!! trans('RT_REGULATOR ') !!}</th>
				<th class="date">{!! trans('RT_DATE ') !!}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
		
		<tfoot>
			<tr>
				<th class="first">Id.</th>
				<th>{!! trans('RT_SYSTEM ') !!}</th>
				<th>{!! trans('RT_REGULATOR ') !!}</th>
				<th>{!! trans('RT_DATE ') !!}</th>
				<th></th>
			</tr>
		</tfoot>
	</table>
	</div>
	
	<div id="buttons" style="display:none;margin-bottom:10px;">
		<a id="back_to_reports" href="#" class="default-btn"><span>{!! trans('BACK_TO_REPORTS_BUTTON ') !!}</span></a>
		<a id="previous_report" href="#" rel="" class="default-btn"><span><< {!! trans('PREVIOUS_REPORT_BUTTON ') !!}</span></a>
		<a id="next_report" href="#" rel="" class="default-btn"><span>{!! trans('NEXT_REPORT_BUTTON ') !!} >></span></a>
	</div>
	
	<div id="report_chart_box" class="default-box" style="clear:both;">
		<div class="header">
			<span>Report Id:</span><span id="report_id" style="padding-left:3px;"></span>
		</div>
		
		<div class="box-content" style="position:relative;">
			
			<div id="chart_ajax_loader" ></div>
			<div class="chart-overlay"></div>
			
			<!-- PANELOVE NASTAVENIE GRAFU -->
			<div class="panel">
				<div class="title-bar">
					<h3>{!! trans('GRAPH_SETTINGS ') !!}</h3>
					<a class="close-panel-btn" title="zavrieť okno"></a>
					<a href="#" onclick="open_menu()" title="otvoriť do nového okna" class="new-window-btn" ></a>
				</div>
				
				<div class="content">
					<form action="" method="post" enctype="multipart/form-data">
						<table CELLSPACING="5" >
							<tr>
								<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('WINDOW_DIMENSIONS ') !!}</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('X_AXIS ') !!}</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('Y_AXIS ') !!}</span></th>
							</tr>
							<tr>
								<td><label class="l" for="chartWidth">{!! trans('GRAPH_WIDTH ') !!}:</label></td>
								<td>
									<!--<input id="chartWidth" type="text" value="auto" class="disabled" disabled="disabled" name="chartWidth" />-->
									<input id="chartWidth" type="text" value="{!! trans('CHART_WIDTH ') !!}"  name="chartWidth" /><span class="px"> px</span>
								</td>
								
								<td colspan="2" >
									<input type="checkbox" {!! trans('CHART_X_TITLE_CHECKBOX ') !!} id="chartXdisplay" value="1"   name="chartXdisplay" />
									<label for="chartXdisplay">{!! trans('X_AXIS_SHOW_TITLE ') !!}</label>
								</td>
								
								<td colspan="2" >
									<input type="checkbox" {!! trans('CHART_Y_TITLE_CHECKBOX ') !!} id="chartYdisplay" value="1"  name="chartYdisplay" />
									<label for="chartYdisplay">{!! trans('Y_AXIS_SHOW_TITLE ') !!}</label>
								</td>
							</tr>
							<tr>
								<td><label class="l" for="chartHeight">{!! trans('GRAPH_HEIGHT ') !!}:</label></td>
								<td><input id="chartHeight" type="text" value="{!! trans('CHART_HEIGHT ') !!}" name="chartHeight" /><span class="px"> px</span></td>
							</tr>
							<tr>
								<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('MAIN_TITLE ') !!}</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('SUB_TITLE ') !!}</span></th>
								<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('MENU_AND_LEGEND_TITLE ') !!}</span></th>
							</tr>
							<tr>
								<td colspan="2">
									<input type="text" name="mainTitleText" value="{!! trans('CHART_MAIN_TITLE_VALUE ') !!}" style="width:142px;" />
								</td>
								<td colspan="2">
									<input type="text" name="subTitleText" value="{!! trans('CHART_SUBTITLE_VALUE ') !!}" style="width:142px;" />
								</td>
								<td colspan="2">
									<input type="checkbox" {!! trans('CHART_LEGEND_CHECKBOX ') !!} id="showLegend" value="1"  name="showLegend" />
									<label for="showLegend">{!! trans('SHOW_LEGEND ') !!}</label>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="checkbox" {!! trans('CHART_MAIN_TITLE_CHECKBOX ') !!} id="mainTitleShow" value="1" name="mainTitleShow" />
									<label for="mainTitleShow">{!! trans('MAIN_TITLE_SHOW ') !!}</label>
								</td>
								<td colspan="2">
									<input type="checkbox" {!! trans('CHART_SUB_TITLE_CHECKBOX ') !!} id="subTitleShow" value="1"  name="subTitleShow" />
									<label for="subTitleShow">{!! trans('SUB_TITLE_SHOW ') !!}</label>	
								</td>
								<td colspan="2">
									<input type="checkbox" {!! trans('CHART_MENU_CHECKBOX ') !!} id="showMenu" value="1"  name="showMenu" />
									<label for="showMenu">{!! trans('SHOW_MENU ') !!}</label>	
								</td>
							</tr>
							
							<tr>
								<td colspan="6">
									<input type="submit" value="{!! trans('SAVE_CHANGES_BUTTON ') !!}">
									<input type="hidden" value="1" name="updateChartSettings">
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
			<!-- END PANELOVE NAST GRAFU -->
			
			<!-- DILAOG NASTAV GRAFU -->
			<div id="panel-menu" title="{!! trans('GRAPH_SETTINGS ') !!}">
				<form action="" method="post" enctype="multipart/form-data">
					<table class="chart-settings" CELLSPACING="5" >
						<tr>
							<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('WINDOW_DIMENSIONS ') !!}</span></th>
							<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('X_AXIS ') !!}</span></th>
							<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('Y_AXIS ') !!}</span></th>
						</tr>
						<tr>
							<td><label class="l" for="dialog-chartWidth">{!! trans('GRAPH_WIDTH ') !!}:</label></td>
							<td><input id="dialog-chartWidth" type="text" value="auto" class="disabled" disabled="disabled"  name="chartWidth" /></td>
							
							<td colspan="2" >
								<input  type="checkbox" {!! trans('CHART_X_TITLE_CHECKBOX ') !!} id="dialog-chartXdisplay" value="1"   name="chartXdisplay" />
								<label  for="dialog-chartXdisplay">{!! trans('X_AXIS_SHOW_TITLE ') !!}</label>
							</td>
							
							<td colspan="2" >
								<input type="checkbox" {!! trans('CHART_Y_TITLE_CHECKBOX ') !!} id="dialog-chartYdisplay" value="1"   name="chartYdisplay" />
								<label for="dialog-chartYdisplay">{!! trans('Y_AXIS_SHOW_TITLE ') !!}</label>
							</td>
						</tr>
						<tr>
							<td><label class="l" for="dialog-chartHeight">{!! trans('GRAPH_HEIGHT ') !!}:</label></td>
							<td><input id="dialog-chartHeight" type="text" value="{!! trans('CHART_HEIGHT ') !!}" name="chartHeight" /><span class="px"> px</span></td>
						</tr>
						<tr>
							<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('MAIN_TITLE ') !!}</span></th>
							<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('SUB_TITLE ') !!}</span></th>
							<th colspan="2" style="width:150px;" ><span class="bold">{!! trans('MENU_AND_LEGEND_TITLE ') !!}</span></th>
						</tr>
						<tr>
							<td colspan="2">
								<input type="text" name="mainTitleText" value="{!! trans('CHART_MAIN_TITLE_VALUE ') !!}" style="width:142px;" />
							</td>
							<td colspan="2">
								<input type="text" name="subTitleText" value="{!! trans('CHART_SUBTITLE_VALUE ') !!}" style="width:142px;" />
							</td>
							<td colspan="2">
								<input type="checkbox" {!! trans('CHART_LEGEND_CHECKBOX ') !!} id="dialog-showLegend" value="1"  name="showLegend" />
								<label for="dialog-showLegend">{!! trans('SHOW_LEGEND ') !!}</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="checkbox" {!! trans('CHART_MAIN_TITLE_CHECKBOX ') !!} id="dialog-mainTitleShow" value="1"  name="mainTitleShow" />
								<label for="dialog-mainTitleShow">{!! trans('MAIN_TITLE_SHOW ') !!}</label>
							</td>
							<td colspan="2">
								<input type="checkbox" {!! trans('CHART_SUB_TITLE_CHECKBOX ') !!} id="dialog-subTitleShow" value="1"  name="subTitleShow" />
								<label for="dialog-subTitleShow">{!! trans('SUB_TITLE_SHOW ') !!}</label>	
							</td>
							<td colspan="2">
								<input type="checkbox" {!! trans('CHART_MENU_CHECKBOX ') !!} id="dialog-showMenu" value="1"  name="showMenu" />
								<label for="dialog-showMenu">{!! trans('SHOW_MENU ') !!}</label>	
							</td>
						</tr>
						
						<tr>
							<td colspan="6">
								<input type="submit" value="{!! trans('SAVE_CHANGES_BUTTON ') !!}">
								<input type="hidden" value="1" name="updateChartSettings">
							</td>
						</tr>
					</table>
				</form>
			</div>
			<!-- END DILAOG NASTAV GRAFU -->
			
			<a href="javascript:void(0);" title="{!! trans('MENU_BUTTON_TITLE ') !!}" class="settings_button"></a>
			
			<div id="export-icons" >
			<form action="{!! trans('ROOT_PATH ') !!}{!! trans('MODUL_PATH ') !!}csv_export.php" method="post">
				<input type="image" value="" class="csv_button" title="CSV export" />
				<input type="hidden" value="" name="reportId" />
			</form>
			<form action="{!! trans('ROOT_PATH ') !!}{!! trans('MODUL_PATH ') !!}xml_export.php" method="post">
			<input type="image" value="" class="xml_button" title="XML export" />
				<input type="hidden" value="" name="reportId" />
			</form>
			<form action="{!! trans('ROOT_PATH ') !!}{!! trans('MODUL_PATH ') !!}json_export.php" method="post">
			<input type="image" value="" class="json_button" title="JSON export" />
				<input type="hidden" value="" name="reportId" />
			</form>
			</div>
			
			<div id="report_chart_container" style="height:{!! trans('CHART_CONTAINER_HEIGHT ') !!}px;width:{!! trans('CHART_WIDTH ') !!}px;"></div>
			
			<!-- BEGIN DYNAMIC BLOCK: chart_menu -->
			<div id="chart-menu">
				<div >
					<ul>
						<li><a id="reset-zoom" class="default-btn " href="javascript:void(0);"><span class="zoom-ico">{!! trans('ZOOM_BUTTON ') !!}</span></a></li>
						<li><a id="zoom-1" class="default-btn" href="javascript:void(0);"><span>1x</span></a></li>
						<li><a id="zoom-2" class="default-btn" href="javascript:void(0);"><span>2x</span></a></li>
						<li><a id="backward" class="default-btn" href="javascript:void(0);"><span>&nbsp; < &nbsp;</span></a></li>
						<li><a id="forward" class="default-btn" href="javascript:void(0);"><span>&nbsp;>&nbsp;</span></a></li>
					</ul>
				</div>
			</div>
			<!-- END DYNAMIC BLOCK: chart_menu -->
		</div>
	</div>
	
	<div style="float:left;width:100%;">
		<div id="console_box" class="default-box" >
			<div class="header"><span>{!! trans('OUTPUT_BOX ') !!}</span></div>
			<div class="box-content" style="padding:25px;padding-top:5px;"></div>
		</div>
		
		<div id="input_experiment_settings_box" class="default-box">
			<div class="header"><span>{!! trans('EXPERIMENT_SETTINGS_BOX ') !!}</span></div>
			<div class="box-content">
				
				<div class="column">
					<label>{!! trans('EXP_EQUPMENT ') !!}</label>
					<div class="value"><span id="equipment_name"></span></div>
					<label>{!! trans('EXP_BEGINING ') !!}</label>
					<div class="value" ><span id="report_date"></span></div>
					<label>{!! trans('EXP_REG ') !!}</label>
					<div class="value" ><span id="regulator"></span></div>
					<label>{!! trans('EXP_REG_SETTINGS ') !!}</label>
					<div class="value" ><span id="regulator_settings"></span></div>
					<label>{!! trans('EXP_REQUEST_VALUE ') !!}</label>
					<div class="value"><span id="report_input_value"></span></div>
				</div>
				<div class="column">
					
					<label>{!! trans('EXP_SIMULATION_TIME ') !!}</label>
					<div class="value"><span id="report_time"></span><span> s</span></div>
					<label>{!! trans('EXP_SAMPLING_TIME ') !!}</label>
					<div class="value"><span id="ts"></span><span> ms</span></div>
					<label>{!! trans('EXP_PROCESS_VAR ') !!}</label>
					<div class="value"><span id="out_value"></span></div>
					<label>{!! trans('EXP_REGULATORY_VAR ') !!}</label>
					<div class="value"><span id="in_value"></span></div>
					
					<div id="c_led_info" style="display:none;">
						<label>{!! trans('EXP_VOLTAGE_LED ') !!}</label>
					<div class="value"><span id="c_led"></span> v</div>
					</div>
					<div id="c_fan_info" style="display:none;">
						<label>{!! trans('EXP_VOLTAGE_MOTOR ') !!}</label>
						<div class="value"><span id="c_fan"></span> v</div>
					</div>
					<div id="c_lamp_info" style="display:none;">
						<label>{!! trans('EXP_VOLTAGE_LAMP ') !!}</label>
						<div class="value"><span id="c_lamp"></span> v</div>
					</div>
					
				</div>
				<div style="clear:both"></div>
			</div>
		</div>
		
		<div id="personal_notes_box" class="default-box">
			<div class="header"><span>{!! trans('PERSONAL_NOTES_BOX ') !!}</span></div>
			<div class="box-content">
				<textarea>
					
				</textarea>
			</div>
		</div>
		<div class="breaker"></div>
	</div>
	<div class="breaker"></div>
</div>