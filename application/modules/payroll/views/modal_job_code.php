<script type="text/javascript" src="<?php echo base_url("assets/js/validate/payroll/info_by_day.js"); ?>"></script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">Edit Worked Hours for each Job Code</h4>
	<p>Total Worked Hours: <b><?php echo $information["working_hours"] . " Hours"; ?></b></p>
</div>

<div class="modal-body">
	<form name="formWorker" id="formWorker" role="form" method="post">
		<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $information["id_task"]; ?>" />
		<input type="hidden" id="workedHours" name="workedHours" value="<?php echo $information["working_hours"]; ?>" />

		<div class="row">
			<div class="col-sm-8">
				<div class="form-group text-left">
					<label class="control-label" for="jobName">Job Code/Name - Start: *  <?php echo $information["wo_start_project"]?"(W.O. # " . $information["wo_start_project"] . ")":"";  ?></label>
					<select name="jobName" id="jobName" class="form-control js-example-basic-single" <?php echo $information["wo_start_project"]?"disabled":"";  ?> >
						<option value=''>Select...</option>
						<?php for ($i = 0; $i < count($jobs); $i++) { ?>
							<option value="<?php echo $jobs[$i]["id_job"]; ?>" <?php if ($information && $information["fk_id_job"] == $jobs[$i]["id_job"]) {
																					echo "selected";
																				}  ?>><?php echo $jobs[$i]["job_description"]; ?></option>
						<?php } ?>
					</select>
					<?php if ($information["wo_start_project"]) { ?>
						<input type="hidden" name="jobName" value="<?php echo $information["fk_id_job"]; ?>">
					<?php } ?>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group text-left">
					<label for="type" class="control-label">Worked hours: *</label>
					<input id="hours_first_project" name="hours_first_project" class="form-control" type="number" min="0" step="any" placeholder="Hours" value="<?php echo $information["hours_start_project"]; ?>"  />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<div class="form-group text-left">
					<label class="control-label" for="jobNameFinish">Job Code/Name - Finish: *    <?php echo $information["wo_end_project"]?"(W.O. # " . $information["wo_end_project"] . ")":"";  ?></label>
					<select name="jobNameFinish" id="jobNameFinish" class="form-control js-example-basic-single" <?php echo $information["wo_end_project"]?"disabled":"";  ?>>
						<option value=''>Select...</option>
						<?php for ($i = 0; $i < count($jobs); $i++) { ?>
							<option value="<?php echo $jobs[$i]["id_job"]; ?>" <?php if ($information && $information["fk_id_job_finish"] == $jobs[$i]["id_job"]) {
																					echo "selected";
																				}  ?>><?php echo $jobs[$i]["job_description"]; ?></option>
						<?php } ?>
					</select>
					<?php if ($information["wo_end_project"]) { ?>
						<input type="hidden" name="jobNameFinish" value="<?php echo $information["fk_id_job_finish"]; ?>">
					<?php } ?>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group text-left">
					<label for="type" class="control-label">Worked hours: *</label>
					<input id="hours_last_project" name="hours_last_project" class="form-control" type="number" min="0" step="any" placeholder="Hours" value="<?php echo $information["hours_end_project"]; ?>"  />
				</div>
			</div>
		</div>
		<div class="form-group">
			<button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary">
				Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
			</button>
		</div>

		<div class="form-group">
			<div id="div_load" style="display:none">
				<div class="progress progress-striped active">
					<div class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
						<span class="sr-only">45% completado</span>
					</div>
				</div>
			</div>
			<div id="div_error" style="display:none">
				<div class="alert alert-danger"><span class="glyphicon glyphicon-remove" id="span_msj">&nbsp;</span></div>
			</div>
		</div>

	</form>
</div>