<div id="realtime-app">
	<div class="col-lg-8">
		<olm-graph 
			:description="description"
			:series="series"
		></olm-graph>
	</div>
		<div class="col-lg-4">
			<form class="form" v-on:submit.prevent="runExperiment">
				<div class="form-group">
					<label>Experiment</label>
					<select v-model="selectedExperiment" class="form-control">
						<option v-for='experiment in experiments' v-bind:value="experiment">@{{ experiment.device }} - @{{ experiment.software }}</option>
					</select>
				</div>
				<div class="btn-group btn-group-justified">
					<div class="btn-group">
						<button type="button" class="btn btn-primary" v-on:click="showExperiment">Experiment</button>
					</div>
					<div class="btn-group" v-show="selectedExperiment.commands.change">
						<button type="button" class="btn btn-primary" v-on:click="showChange">Change</button>
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
									<label class="radio-inline">
									  <input v-model="selected.instance" type="radio" name="@{{ instance.name }}[]" value="@{{ instance.name }}"> @{{ instance.name }}
									  <span class="label label-warning" v-show="!instance.production">testing</span>
									</label>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group" v-for="commandName in selectedExperiment.experiment_commands">
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
							>
					</olm-input>
				</div>
				<div class="form-group" v-show="selectedExperiment.experiment_commands.length > 0">
					<a
					 class="btn btn-danger pull-left"
					 v-on:click="stopCommand"
					 v-show="selectedExperiment.commands.stop">Stop</a>
					<button class="btn btn-success pull-right" type="submit">Run experiment</button>
				</div>
			</form>
		</div>
	</div>
</div>
<template id="graph-template">
	<div v-el:graph class="olm-graph" v-show="series.length > 1">
		
	</div>
	<div class="olm-graph-placeholder" v-else>
		
	</div>
</template>