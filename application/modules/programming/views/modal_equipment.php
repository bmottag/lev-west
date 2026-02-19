<script type="text/javascript" src="<?php echo base_url("assets/js/validate/workorder/ajaxTrucks_v3.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/validate/programming/equipment.js?v=2"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">EQUIPMENT
	<br><small>
			Add Equipment
	</small>
	</h4>
</div>

<div class="modal-body">
	<form  name="formEquipment" id="formEquipment" role="form" method="post" >
		<input type="hidden" id="hddidProgramming" name="hddidProgramming" value="<?php echo $idProgramming; ?>"/>
		<input type="hidden" id="hddidProgrammingWorker" name="hddidProgrammingWorker" value="<?php echo $idProgrammingWorker; ?>"/>
				
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group text-left">
					<label class="control-label" for="type">Type: *</label>
					<select name="type" id="type" class="form-control" required>
						<option value=''>Select...</option>
						<?php for ($i = 0; $i < count($equipmentType); $i++) { ?>
							<option value="<?php echo $equipmentType[$i]["id_type_2"]; ?>"><?php echo $equipmentType[$i]["type_2"]; ?></option>	
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
					
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group text-left">
					<div id="div_truck">
						<label class="control-label" for="truck">Equipment: *</label>
						<select name="truck" id="truck" class="form-control" >

						</select>
					</div>
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
					<button type="button" id="btnSubmitEquipment" name="btnSubmitEquipment" class="btn btn-primary" >
						Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
					</button> 
				</div>
			</div>
		</div>
		
	</form>
</div>