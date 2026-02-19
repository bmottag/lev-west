<script type="text/javascript" src="<?php echo base_url("assets/js/validate/incidences/witness.js"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">WITNESS
	<br><small>
				Add the witness information
	</small>
	</h4>
</div>

<div class="modal-body">
	<form  name="formWitness" id="formWitness" role="form" method="post" >
		<input type="hidden" id="hddidIncident" name="hddidIncident" value="<?php echo $idIncident; ?>"/>
				
		<div class="form-group text-left">
			<label for="name">Name: *</label>
			<input type="text" id="name" name="name" class="form-control" placeholder="Name" required >
		</div>
		
		<div class="form-group text-left">
			<label for="phone">Phone number: *</label>
			<input type="text" id="phone" name="phone" class="form-control" placeholder="Phone number" required >
		</div>
				
		<div class="form-group">
			<button type="button" id="btnSubmitWitness" name="btnSubmitWitness" class="btn btn-primary" >Save</button> 
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