<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="headingQueue">
			<h4 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseQueue" aria-expanded="false" aria-controls="collapseQueue">
				<h3>Dávkové experimenty</h3>
			</a>
			</h4>
		</div>
		<div id="collapseQueue" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingQueue">
			<div class="panel-body">
				<div class="row" id="queueApp">
					{{ csrf_field() }}
					<div class="col-lg-6">
						<form class="form" v-on:submit.prevent="runExperiment">
							<div class="form-group">
								<label>Experiment</label>
								<select v-model="selectedExperiment" class="form-control">
									<option v-for='experiment in experiments' v-bind:value="experiment">@{{ experiment.device }} - @{{ experiment.software }}</option>
								</select>
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
										>
								</olm-input>
							</div>
							<div class="form-group" v-show="selectedExperiment.experiment_commands.length > 0">
								<button class="btn btn-success pull-right" type="submit">Request experiment</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>