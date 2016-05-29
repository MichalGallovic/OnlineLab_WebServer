<div id="realtime-app">
	<div class="col-lg-8">
		<olm-graph
			:status="status"
			:description="description"
			:series="series"
			v-if="selected.device != 'led_cube'"
		></olm-graph>
		<olm-webcam
		:ip="selectedExperiment.server_ip"
		v-else
		>
		</olm-webcam>
	</div>
		<div class="col-lg-4">
			<form class="form" v-on:submit.prevent="runExperiment">
				<div class="form-group">
					<label>Experiment</label>
					<select v-model="selectedExperiment" class="form-control" v-if="canSwitch">
						<option v-for='experiment in experiments' v-bind:value="experiment">@{{ experiment.device }} - @{{ experiment.software }}</option>
					</select>
					<select v-model="selectedExperiment" class="form-control" v-else disabled>
						<option v-for='experiment in experiments' v-bind:value="experiment">@{{ experiment.device }} - @{{ experiment.software }}</option>
					</select>
				</div>
				<div class="btn-group btn-group-justified">
					<div class="btn-group" 
					v-if="!(selectedExperiment.experiment_commands.length == 1 && selectedExperiment.experiment_commands[0] == 'change')">
						<button type="button" class="btn btn-default" v-on:click="showExperiment"
						v-bind:class="{'btn-primary': mode == 'experiment'}">Experiment</button>
					</div>
					<div class="btn-group" v-show="selectedExperiment.commands.change">
						<button type="button" class="btn btn-default" v-on:click="showChange"
						v-bind:class="{'btn-primary': mode == 'change'}">Change</button>
					</div>
				</div>
				<div class="form-group" v-show="experiments && selectedExperiment">
					<div class="row" style="margin-top:10px">
						<div v-el:input class="form-group">
							<label 
							class="control-label col-xs-12"
							>Run on instance</label>
							<div class="col-xs-12">
								<span v-for="(index, instance) in selectedExperiment.instances" >
									<label class="radio-inline" v-if="canSwitch">
									  <input v-model="selected.instance" type="radio" name="@{{ instance.name }}[]" value="@{{ instance.name }}"> @{{ instance.name }}
									  <span class="label label-warning" v-show="!instance.production">testing</span>
									</label>
									<label class="radio-inline" v-else>
									  <input v-model="selected.instance" type="radio" name="@{{ instance.name }}[]" value="@{{ instance.name }}" disabled> @{{ instance.name }}
									  <span class="label label-warning" v-show="!instance.production">testing</span>
									</label>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group" v-for="commandName in filteredCommands">
					<span 
					v-show="selectedExperiment.commands[commandName]" 
					class="label label-primary" 
					style="font-size: 13px;">@{{ commandName }}</span>
					<olm-input
							v-for="input in selectedExperiment.commands[commandName]"
							:label="input.title"
							:name="input.name"
							:type="input.type"
							:values="input.values"
							:placeholder="input.placeholder"
							:command="commandName"
							:meaning="input.meaning"
							:visibleon="input.visible"
							>
					</olm-input>
				</div>
				<div class="form-group" v-show="selectedExperiment.experiment_commands.length > 0">
					<a
					 class="btn btn-danger pull-left"
					 v-on:click="stopCommand"
					 v-show="selectedExperiment.commands.stop">Stop</a>
					<button class="btn btn-success pull-right" type="submit" v-bind:class="{'disabled' : !canSwitch}" v-if="mode == 'experiment'">Run experiment</button>
					<button class="btn btn-warning pull-right" type="button" v-if="mode == 'change'" v-on:click="postChange">Change</button>
				</div>
			</form>
		</div>
	</div>
</div>
<template id="graph-template">
	<div v-el:graph class="olm-graph" v-show="series.length > 1 && status == 'experimenting'">
		
	</div>
	<div 
	v-if="status == 'idle'"
	class="olm-graph-placeholder olm-graph-idle">
	</div>
	<div class="olm-graph-placeholder olm-graph-init" v-if="status == 'initializing'">
		<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate" style="font-size: 23px; color: rgb(80,80,80);"></i>
	</div>
</template>