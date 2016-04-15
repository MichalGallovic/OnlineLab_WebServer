<div id="reservation_holder">
<table>
	<tr class="header">
		<th class="title">{!! trans('EXPERIMENT_EQUIPMENT ') !!}</th>
		<th class="rezervacia">{!! trans('EXP_RESERVATION ') !!}</th>
		<th class="avaibility">{!! trans('EXP_ACCESSIBILITY ') !!}</th>
	</tr>
	<!-- BEGIN DYNAMIC BLOCK: equipment_row -->
	<tr class="{!! trans('EXP_ROW_CLASS ') !!}">
		<td>{!! trans('EQUIPMENT_NAME ') !!}</td>
		<td>{!! trans('EXPERIMENT_AVAIBILITY ') !!}</td>
		<td style="padding-right:10px;padding-left:0px;">{!! trans('AVAIBILITY_CLASS ') !!}</td>
	</tr>
	<!-- END DYNAMIC BLOCK: equipment_row -->
</table>
</div>

<div id="no-avaible-window">
	<div>
		<h2>Zariadenie nie je dostupné</h2>
		<p>
			Pre dané zariadenie buď nemáte vytvorenú žiadnu rezerváciu alebo rezervácia nevyhovuje aktuálnemu miestnemu času.
		</p>
		<input type="button" class="info-dialog-close-btn" value="Zatvoriť okno" name="" />
	</div>
</div>

<div id="avaible-for-termo">
	<div>
		<h2>Tepelno-optická sústava</h2>
		<form id="formular" name="formular" action="#" onsubmit="return false;"  method="post" enctype="multipart/form-data">
			<input type="hidden" name="system" value="0" />
			<table class="layout">
				<tr><td><h3>Nastavenia</h3></td><td><h3 style="margin-left:20px;">Typ regulátora</h3></td></tr>
				<tr>
					<td class="l_content">
						<ul>
							<li>
								<label for="vstup" style="margin-top:0px;">Požadovaná hodnota</label>
								<input style="width:200px;" type="text" value="30" name="vstup" id="vstup" />
							</li>
							<li>
								<label for="simumlation-time">Čas simulácie</label>
								<input style="width:200px;" id="simumlation-time" type="text" name="time"  value="40" /><span>&nbsp;s</span>
							</li>
							<li>
								<label for="sample_time">Perióda vzorkovania</label>
								<input style="width:200px;" id="sample_time" type="text" name="sample_time"  value="100" /><span>&nbsp;ms</span>
							</li>
							<li>
								<label for="vyst_switch">Regulovaná veličina</label>
								<select id="vyst_switch"  name="vyst_switch">
									<option value="1">teploty</option>
									<option value="2">filtrovanej teploty</option>
									<option selected="selected" value="3">svetelnej intenzity</option>
									<option value="4">filtrovanej svetelnej intenzity</option>
									<option value="5">prúdu motorčeka ventilátora</option>
									<option value="6">otáčok ventilátora</option>
								</select>
							</li>
							<li>
								<label for="vstup_switch">Regulačná veličina </label>
								<select id="vstup_switch"  name="vstup_switch">
									<option selected="selected" value="1">napätie lampy</option>
									<option value="2">napätie LED diódy</option>
									<option value="3">napätie ventilátora</option>
								</select>
							</li>
							<li id="c_vst_1">
								<label for="">Napätie lampy </label>
								<input style="width:200px;" type="text" name="c_lamp" id="" value="0" /><span>&nbsp;V</span>
							</li>
							<li id="c_vst_2">
								<label for="">Napätie LED diódy </label>
								<input style="width:200px;" type="text" name="c_led" id="" value="0" /><span>&nbsp;V</span>
							</li>
							<li id="c_vst_3">
								<label for="">Napätie motorčeka </label>
								<input style="width:200px;" type="text" name="c_fan" id="" value="0" /><span>&nbsp;V</span>
							</li>
						</ul>
					</td>
					<td valign="top" style="padding-left:20px;">
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
							<div class="own_reg">
								Sústava bude regulovaná jedným z vybratých regulátorov.<br /><br />
								Zadefinujte funkciu <i><b>f</b></i>, kde <i>y1 = f(u1)</i><br />
								y1 = výstup z regulátora<br />
								u1 = vstup do regulátora<br />
								<div >
									<select id="ctrl_set" name="ctrl_set" size="5" >
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
									<textarea id="own_func" name="own_func" style="width:300px;" rows="5" cols="30">y1=u1</textarea>
								</div>
								
							</div>
							<div class="no_reg">
								Sústava bude regulovaná v otvorenej slučke.
							</div>
						</div>
					</td>
					</tr>
					<tr>
						<td valign="bottom"><input type="button" class="info-dialog-close-btn" value="Zatvoriť okno" name="" /></td>
						<td valign="bottom" ><input id="termo" type="button" class="submit" onclick="" style="float:right;"  value="Spustiť experiment" /></td>
					</tr>
			</table>
			<input type="hidden" name="plant_id" id="plant_id" value="3" />	
		</form>
		
	</div>
</div>

<div id="avaible-for-hydro">
	<div>
		<h2>Trojhladinová hydraulická sústava</h2>
		<p>
			Je nám ľúto, ale na tomto zariadení nie je momentálne možné vykonávať experiment.
		</p>
		<input type="button" class="info-dialog-close-btn" value="Zatvoriť okno" name="" />
	</div>	
</div>

<div id="avaible-for-hydro2">
	<div>
		<h2>Trojhladinová hydraulická sústava</h2>
		<form action="" method="post" enctype="multipart/form-data">
			<table class="layout">
				<tr><td><h3>Nastavenia</h3></td><td><h3 style="margin-left:20px;">Typ regulátora</h3></td></tr>	
				<tr>
					<td class="l_content">
						<table class="settings">
							<thead>
								<tr id="row_num_of_tanks">
									<td><label for="num_of_tanks">Počet nádob</label></td>
									<td>
										<select id="num_of_tanks" name="num_of_tanks" >
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
										</select>
									</td>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td><label for="simumlation-time">Čas simulácie</label></td>
									<td><input id="simumlation-time" type="text" name="time"  value="40" /><span>&nbsp;s</span></td>
								</tr>
								<tr>
									<td><label for="sample_time">Perióda vzorkovania</label></td>
									<td><input id="sample_time" type="text" name="sample_time"  value="100" /><span>&nbsp;ms</span></td>
								</tr>
							</tfoot>
							
							<tbody>
								<tr class="tank">
									<td><label for="vysput_1">Výška hladiny v 1.stlpci</label></td>
									<td><input id="vysput_1" type="text" name="vstup[1]"  value="10"/></td>
								</tr>
							</tbody>
							
						</table>
						
					</td>
					<td valign="top" style="padding-left:20px;">
						<input id="pid_ctrl" type="radio" name="ctrl_typ" value="PID" checked="checked" />
						<label for="" class="radio_btn">Pid</label>
						<input id="own_ctrl" type="radio" name="ctrl_typ" value="OWN" />
						<label for="" class="radio_btn">Vlastný</label>
						<input id="no_ctrl" type="radio" name="ctrl_typ" value="NO" />
						<label for="" class="radio_btn">Otvorená slučka</label>
						
						<div class="regulator_settings_holder">
							<div class="pid_reg">
								<table>
									<tr><td>P</td><td><input type="text" value="8" name="P" /></td></tr>
									<tr><td>I</td><td><input type="text" value="0.25" name="I" /></td></tr>
									<tr><td>D</td><td><input type="text" value="1.3" name="D" /></td></tr>
								</table>
							</div>
							<div class="own_reg">
								Sústava bude regulovaná jedným z vybratých regulátorov. 
							</div>
							<div class="no_reg">
								Sústava bude regulovaná v otvorenej slučke.
							</div>
						</div>
					</td>
					</tr>
					<tr>
						<td valign="bottom"><input type="button" class="info-dialog-close-btn" value="Zatvoriť okno" name="" /></td>
						<td valign="bottom" ><input type="submit" style="float:right;" class="" name="" value="Spustiť experiment" /></td>
					</tr>
			</table>	
		</form>
		
	</div>
</div>