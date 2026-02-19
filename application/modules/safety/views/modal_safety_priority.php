<script type="text/javascript" src="<?php echo base_url("assets/js/validate/safety/priority.js"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">HAZARDS PRIORITY
	<br><small>
				<?php 
					echo "<strong>Hazard: </strong>" . $hazardInfo["hazard_description"]; 
					echo "<br><strong>Solution: </strong>" . $hazardInfo["solution"]; 
				?>
	</small>
	</h4>
</div>

<div class="modal-body">
	<form  name="formPriority" id="formPriority" role="form" method="post" >
		<input type="hidden" id="hddIdHazard" name="hddIdHazard" value="<?php echo $idHazard; ?>"/>
		<input type="hidden" id="hddIdSafety" name="hddIdSafety" value="<?php echo $idSafety; ?>"/>
		
		<div class="form-group text-left">
				<label for="priority">Priority : *</label>
				<select name="priority" id="priority" class="form-control" >
					<option value=''>Select...</option>
					<?php for ($i = 0; $i < count($priority); $i++) { ?>
						<option value="<?php echo $priority[$i]["id_priority"]; ?>" ><?php echo $priority[$i]["priority_description"]; ?></option>	
					<?php } ?>
				</select>
		</div>  
		
		<div class="form-group">
			<button type="button" id="btnSubmitPriority" name="btnSubmitPriority" class="btn btn-primary" >Save</button> 
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