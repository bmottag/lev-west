<script type="text/javascript" src="<?php echo base_url("assets/js/validate/workorder/hold_back.js"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">HOLD BACK
	<br><small>
				Add Hold back to the Work Order
	</small>
	</h4>
</div>

<div class="modal-body">
	<form  name="formHoldBack" id="formHoldBack" role="form" method="post" >
		<input type="hidden" id="hddidWorkorder" name="hddidWorkorder" value="<?php echo $idWorkorder; ?>"/>
				
		<div class="form-group text-left">
			<label for="hour">Value : *</label>
			<input type="text" id="value" name="value" class="form-control" placeholder="Value" required >
		</div>
		
		<div class="form-group text-left">
			<label for="description">Description : *</label>
			<textarea id="description" name="description" class="form-control" rows="3"></textarea>
		</div>
		
		<div class="form-group">
			<button type="button" id="btnSubmitHoldBack" name="btnSubmitHoldBack" class="btn btn-primary" >Save</button> 
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