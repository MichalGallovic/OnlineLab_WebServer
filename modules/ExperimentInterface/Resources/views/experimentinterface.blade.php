<link href="{!! trans('ROOT_PATH ') !!}includes/modules/experimentinterface/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/experimentinterface/js/default.js"></script>

<div id="experimentinterface">


	<div id="experimentinterface-termo">
		<form id="formular2" name="formular2" action="#" onsubmit="return false;"  method="post" enctype="multipart/form-data">
				<input type="hidden" name="system" value="0" />
				<table class="layout">
					<tr><td><h3>Nastavenia</h3></td><td><h3 style="margin-left:20px;">Typ regulátora</h3></td></tr>
					<tr>
						<td class="l_content">
							<ul>
								<li>
									<label for="vstup" style="margin-top:0px;">Požadovaná hodnota</label>
									<input  type="text" value="" name="vstup" id="vstup" />
								</li>
								<li>
									<label for="simumlation-time">Čas simulácie</label>
									<input  id="simumlation-time" type="text" name="time"  value="40" /><span>&nbsp;s</span>
								</li>
								<li>
									<label for="sample_time">Perióda vzorkovania</label>
									<input  id="sample_time" type="text" name="sample_time"  value="100" /><span>&nbsp;ms</span>
								</li>
								<li>
									<label for="vyst_switch">Regulovaná veličina</label>
									<select id="vyst_switch-interface"  name="vyst_switch">
										<option value="1">teploty</option>
										<option value="2">filtrovanej teploty</option>
										<option selected="selected" value="3">svetelnej intenzity</option>
										<option value="4">filtrovanej svetelnej intenzity</option>
										<option value="5">prúdu motorčeka ventilátora</option>
										<option value="6">otáčok ventilátora</option>
									</select>
								</li>
								<li>
									<label for="vstup_switch-intreface">Regulačná veličina </label>
									<select id="vstup_switch-intreface"  name="vstup_switch">
										<option value="1">napätie lampy</option>
										<option value="2">napätie LED diódy</option>
										<option value="3">napätie ventilátora</option>
									</select>
								</li>
								<li id="c_vst_1-intreface">
									<label for="">Napätie lampy </label>
									<input  type="text" name="c_lamp" id="" value="0" /><span>&nbsp;V</span>
								</li>
								<li id="c_vst_2-intreface">
									<label for="">Napätie LED diódy </label>
									<input  type="text" name="c_led" id="" value="0" /><span>&nbsp;V</span>
								</li>
								<li id="c_vst_3-intreface">
									<label for="">Napätie motorčeka </label>
									<input  type="text" name="c_fan" id="" value="0" /><span>&nbsp;V</span>
								</li>
							</ul>
						</td>
						<td valign="top" style="padding-left:20px;padding-top:10px;width:210px;">
							<input id="pid_ctrl" type="radio" name="ctrl_typ" value="PID" checked="checked" />
							<label for="" class="radio_btn">Pid</label>
							<input id="own_ctrl" type="radio" name="ctrl_typ" value="OWN" />
							<label for="" class="radio_btn">Vlastný</label>
							<input id="no_ctrl" type="radio" name="ctrl_typ" value="NO" />
							<label for="" class="radio_btn">Otvorená slučka</label>
							
							<div class="regulator_settings_holder">
								<div class="pid_reg">
									<table>
										<tr><td>P</td><td><input type="text" value="0.8" name="P" /></td></tr>
										<tr><td>I</td><td><input type="text" value="2.95" name="I" /></td></tr>
										<tr><td>D</td><td><input type="text" value="0" name="D" /></td></tr>
									</table>
								</div>
								<div class="own_reg" >
									Sústava bude regulovaná jedným z vybratých regulátorov.<br /><br />
									Zadefinujte funkciu <i><b>f</b></i>, kde <i>y1 = f(u1)</i><br />
									y1 = výstup z regulátora<br />
									u1 = vstup do regulátora<br />
									<select id="ctrl_set-interface" name="ctrl_set" size="5" style="width:200px;margin:3px 0px;" >
										<optgroup label="prioritné">
											<!-- BEGIN DYNAMIC BLOCK: ctrl_priorit -->
											<option value="{!! trans('CTRL_ID ') !!}">{!! trans('CTRL_NAME ') !!}</option>
											<!-- END DYNAMIC BLOCK: ctrl_priorit -->
										</optgroup>
										<optgroup label="verejné">
											<!-- BEGIN DYNAMIC BLOCK: ctrl_public -->
											<option value="{!! trans('CTRL_ID ') !!}">{!! trans('CTRL_NAME ') !!}</option>
											<!-- END DYNAMIC BLOCK: ctrl_public -->
										</optgroup>
										<optgroup label="privátne">
											<!-- BEGIN DYNAMIC BLOCK: ctrl_own -->
											<option value="{!! trans('CTRL_ID ') !!}">{!! trans('CTRL_NAME ') !!}</option>
											<!-- END DYNAMIC BLOCK: ctrl_own -->
										</optgroup>
									</select>
									<div >
										<textarea id="own_func-interface" name="own_func" style="width:194px;" rows="5" cols="30">y1=u1</textarea>
									</div>
									
								</div>
								<div class="no_reg">
									Sústava bude regulovaná v otvorenej slučke.
								</div>
							</div>
						</td>
						</tr>
						<tr>
							<td valign="bottom">
							</td>
							<td valign="bottom" ><input id="termo-from-interface" type="button" onclick="" style="" class="default-submit-btn right"  value="Spustiť experiment" /></td>
						</tr>
				</table>
				<input type="hidden" name="plant_id" id="plant_id" value="3" />	
			</form>
		
	</div>

	<div id="experimentinterface-intro">
		V tomto okne sa Vám zobrazí formulár pre nastavenie experimentu.
	</div>

</div>