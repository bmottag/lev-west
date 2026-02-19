<script type="text/javascript" src="<?php echo base_url("assets/js/validate/incidences/car_involved.js"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">CAR INVOLVED
	<br><small>
				Add car involved in accident info
	</small>
	</h4>
</div>

<div class="modal-body">
	<p class="text-danger text-left">Fields with * are required.</p>
	
	<form  name="formInvolved" id="formInvolved" role="form" method="post" >
		<input type="hidden" id="hddidIncident" name="hddidIncident" value="<?php echo $idIncident; ?>"/>
		
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group text-left">
					<label for="name">Make: *</label>
					<input type="text" id="make" name="make" class="form-control" placeholder="Make" required >
				</div>
			</div>
			
			<div class="col-sm-6">
				<div class="form-group text-left">
					<label for="phone">Model: *</label>
					<input type="text" id="model" name="model" class="form-control" placeholder="Model" required >
				</div>
			</div>
		</div>
		
		
		<div class="row">
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="name">Type: </label>
					<input type="text" id="type" name="type" class="form-control" placeholder="Type"  >
				</div>
			</div>
			
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="phone">Insurance number: *</label>
					<input type="text" id="insurance" name="insurance" class="form-control" placeholder="Insurance number" required >
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="name">Register owner: *</label>
					<input type="text" id="owner" name="owner" class="form-control" placeholder="Register owner" required >
				</div>
			</div>
			
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="phone">Driver name: *</label>
					<input type="text" id="driver" name="driver" class="form-control" placeholder="Driver name" required >
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="name">License number: *</label>
					<input type="text" id="license" name="license" class="form-control" placeholder="License number" required >
				</div>
			</div>
			
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="phone">Company name: </label>
					<input type="text" id="company" name="company" class="form-control" placeholder="Company name" >
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6">			
				<div class="form-group text-left">
					<label for="phone">Plate: *</label>
					<input type="text" id="plate" name="plate" class="form-control" placeholder="Plate" required >
				</div>
			</div>
		</div>
			
			
			
			
			
			
				
		<div class="form-group">
			<button type="button" id="btnSubmitInvolved" name="btnSubmitInvolved" class="btn btn-primary" >Save</button> 
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