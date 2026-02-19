<script type="text/javascript" src="<?php echo base_url("assets/js/validate/admin/alerts.js"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">Alerts Settings Form
	<br><small>Add/Edit Alerts</small>
	</h4>
</div>

<div class="modal-body">
	<form name="form" id="form" role="form" method="post" >
		<input type="hidden" id="hddId" name="hddId" value="<?php echo $information?$information[0]["id_alerts_settings"]:""; ?>"/>
		
		<div class="row">
			<div class="col-sm-12">		
				<div class="form-group text-left">
					<label class="control-label" for="alert_description">Alert Description: *</label>
					<textarea id="alert_description" name="alert_description" class="form-control" rows="3"><?php echo $information?$information[0]["alert_description"]:""; ?></textarea>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-6">		
				<div class="form-group text-left">
					<label class="control-label" for="emailTo">Send Email to:</label>
					<select name="emailTo" id="emailTo" class="form-control" >
						<option value=''>Select...</option>
						<?php for ($i = 0; $i < count($workersList); $i++) { ?>
							<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if($information && $information[0]["fk_id_user_email"] == $workersList[$i]["id_user"]) { echo "selected"; }  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
						<?php } ?>
					</select>
				</div>
			</div>
			
			<div class="col-sm-6">
				<div class="form-group text-left">
					<label class="control-label" for="smsTo">Send SMS to:</label>
					<select name="smsTo" id="smsTo" class="form-control" >
						<option value=''>Select...</option>
						<?php for ($i = 0; $i < count($workersList); $i++) { ?>
							<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if($information && $information[0]["fk_id_user_sms"] == $workersList[$i]["id_user"]) { echo "selected"; }  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
						<?php } ?>
					</select>
				</div>
			</div>
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

		<div class="form-group">
			<div class="row" align="center">
				<div style="width:50%;" align="center">
					<button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary" >
						Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
					</button> 
				</div>
			</div>
		</div>
			
	</form>
</div>