<script type="text/javascript" src="<?php echo base_url("assets/js/validate/safety/safety_covid.js"); ?>"></script>

<script>
function valid_inconvenientes() 
{
	if(document.getElementById('distancing1').checked){
		document.getElementById('hddDistancing').value = 1;
	}else if(document.getElementById('distancing2').checked){
		document.getElementById('hddDistancing').value = 2;
	}else{
		document.getElementById('hddDistancing').value = "";
	}

	if(document.getElementById('sharing_tools1').checked){
		document.getElementById('hddSharingTools').value = 1;
	}else if(document.getElementById('sharing_tools2').checked){
		document.getElementById('hddSharingTools').value = 2;
	}else{
		document.getElementById('hddSharingTools').value = "";
	}

	if(document.getElementById('required_ppe1').checked){
		document.getElementById('hddRequiredPPE').value = 1;
	}else if(document.getElementById('required_ppe2').checked){
		document.getElementById('hddRequiredPPE').value = 2;
	}else{
		document.getElementById('hddRequiredPPE').value = "";
	}

	if(document.getElementById('symptoms1').checked){
		document.getElementById('hddSymptoms').value = 1;
	}else if(document.getElementById('symptoms2').checked){
		document.getElementById('hddSymptoms').value = 2;
	}else{
		document.getElementById('hddSymptoms').value = "";
	}

	if(document.getElementById('protocols1').checked){
		document.getElementById('hddProtocols').value = 1;
	}else if(document.getElementById('protocols2').checked){
		document.getElementById('hddProtocols').value = 2;
	}else{
		document.getElementById('hddProtocols').value = "";
	}

}
</script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-life-saver"></i> <strong>FLHA - FIELD LEVEL HAZARD ASSESSMENT</strong>
				</div>
				<div class="panel-body">
					<div class="alert alert-info">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $information?$information[0]["job_description"]:""; ?><br>
						<strong>Task(s) to be done: </strong><br><?php echo $information?$information[0]["work"]:""; ?>
					</div>
				<?php 
					if($information){
				?>
					<ul class="nav nav-tabs">
						<li><a href="<?php echo base_url('safety/add_safety_v2/' . $information[0]['fk_id_job'] . '/' . $information[0]['id_safety']); ?>">Main Form</a>
						</li>
						<li><a href="<?php echo base_url('safety/upload_info_safety_v2/' . $information[0]['id_safety']); ?>">Hazards</a>
						</li>
						<li class='active'><a href="<?php echo base_url('safety/upload_covid/' . $information[0]['id_safety']); ?>">COVID Form</a>
						</li>
						<li><a href="<?php echo base_url('safety/upload_workers/' . $information[0]['id_safety']); ?>">Workers</a>
						</li>
						<li><a href="<?php echo base_url('safety/review_flha/' . $information[0]['id_safety']); ?>">Review and Sign</a>
						</li>
					</ul>
					<br>
				<?php
					}
				?>

<?php
$retornoExito = $this->session->flashdata('retornoExito');
if ($retornoExito) {
    ?>
    <div class="row">
		<div class="col-lg-12">	
			<div class="alert alert-success ">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				<?php echo $retornoExito ?>		
			</div>
		</div>
	</div>
    <?php
}

$retornoError = $this->session->flashdata('retornoError');
if ($retornoError) {
    ?>
    <div class="row">
		<div class="col-lg-12">	
			<div class="alert alert-danger ">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<?php echo $retornoError ?>
			</div>
		</div>
	</div>
    <?php
}
?> 
<!--INICIO COVID -->
			<form  name="form" id="form" class="form-horizontal" method="post" >
				<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $safetyCovid?$safetyCovid[0]["id_safety_covid"]:""; ?>"/>
				<input type="hidden" id="hddIdSafety" name="hddIdSafety" value="<?php echo $information?$information[0]["id_safety"]:""; ?>"/>

<?php 
$valorDistancing = "";
$valorSharingTools = "";
$valorRequiredPPE = "";
$valorSymptoms = "";
$valorProtocols = "";
if($safetyCovid)
{
		$valorDistancing = $safetyCovid[0]["distancing"];
		$valorSharingTools = $safetyCovid[0]["sharing_tools"];
		$valorRequiredPPE = $safetyCovid[0]["required_ppe"];
		$valorSymptoms = $safetyCovid[0]["symptoms"];
		$valorProtocols = $safetyCovid[0]["protocols"];
}
?>
			    <div class="row">
					<div class="col-lg-12">	
						<div class="alert alert-danger ">
							<span class="fa fa-info" aria-hidden="true"></span>
							<strong>Attention:</strong>
							If you have selected "NO" to any of the questions, STOP!<br>
							You must not start work until you have:

						    Developed additional mitigation strategies.
						    Reviewed the mitigation strategies and they have been approved by the Superintendent.
						    Ensured you are in compliance with Alberta OH&S regarding "Right to refuse dangerous work".
	
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<label for="type" class="control-label">Can 6ft distancing be maintained between workers during the
task?</label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="distancing" id="distancing1" value=1 <?php if($safetyCovid && $safetyCovid[0]["distancing"] == 1) { echo "checked"; }  ?> onclick="valid_inconvenientes()">Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="distancing" id="distancing2" value=2 <?php if($safetyCovid && $safetyCovid[0]["distancing"] == 2) { echo "checked"; }  ?> onclick="valid_inconvenientes()">No
							</label>

							<input type="hidden" id="hddDistancing" name="hddDistancing" value="<?php echo $valorDistancing; ?>"/>
						</div>
						
						<div class="col-sm-8">
							<textarea id="distancing_comments" name="distancing_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $safetyCovid?$safetyCovid[0]["distancing_comments"]:""; ?></textarea>
						</div>
					</div>			
					
					<div class="col-sm-6">
						<label for="type" class="control-label">Workers can perform their tasks without sharing tools or
equipment? </label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="sharing_tools" id="sharing_tools1" value=1 <?php if($safetyCovid && $safetyCovid[0]["sharing_tools"] == 1) { echo "checked"; }  ?> onclick="valid_inconvenientes()">Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="sharing_tools" id="sharing_tools2" value=2 <?php if($safetyCovid && $safetyCovid[0]["sharing_tools"] == 2) { echo "checked"; }  ?> onclick="valid_inconvenientes()">No
							</label>

							<input type="hidden" id="hddSharingTools" name="hddSharingTools" value="<?php echo $valorSharingTools; ?>"/>
						</div>
						
						<div class="col-sm-8">
							<textarea id="sharing_tools_comments" name="sharing_tools_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $safetyCovid?$safetyCovid[0]["sharing_tools_comments"]:""; ?></textarea>
						</div>
					</div>
				</div>

				<div class="row">	
					<div class="col-sm-6">
						<br><p><strong>All workers have the required PPE to safely perform their work?</strong><small> GLOVES ARE MANDATORY. </small></p>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="required_ppe" id="required_ppe1" value=1 <?php if($safetyCovid && $safetyCovid[0]["required_ppe"] == 1) { echo "checked"; }  ?> onclick="valid_inconvenientes()">Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="required_ppe" id="required_ppe2" value=2 <?php if($safetyCovid && $safetyCovid[0]["required_ppe"] == 2) { echo "checked"; }  ?> onclick="valid_inconvenientes()">No
							</label>

							<input type="hidden" id="hddRequiredPPE" name="hddRequiredPPE" value="<?php echo $valorRequiredPPE; ?>"/>
						</div>
						
						<div class="col-sm-8">
							<textarea id="required_ppe_comments" name="required_ppe_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $safetyCovid?$safetyCovid[0]["required_ppe_comments"]:""; ?></textarea>
						</div>
					</div>

					<div class="col-sm-6">
						<br><p><strong>All workers have no signs or symptoms of being ill (i.e.: Sore throat,
fever, dry cough, shortness of breath)?</strong></p>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="symptoms" id="symptoms1" value=1 <?php if($safetyCovid && $safetyCovid[0]["symptoms"] == 1) { echo "checked"; }  ?> onclick="valid_inconvenientes()">Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="symptoms" id="symptoms2" value=2 <?php if($safetyCovid && $safetyCovid[0]["symptoms"] == 2) { echo "checked"; }  ?> onclick="valid_inconvenientes()">No
							</label>

							<input type="hidden" id="hddSymptoms" name="hddSymptoms" value="<?php echo $valorSymptoms; ?>"/>
						</div>
						
						<div class="col-sm-8">
							<textarea id="symptoms_comments" name="symptoms_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $safetyCovid?$safetyCovid[0]["symptoms_comments"]:""; ?></textarea>
						</div>
					</div>
				</div>

				<div class="row">	
					<div class="col-sm-6">
						<br><p><strong>Crew is aware of site COVID protocols for breaks, lunchrooms,
washrooms, elevator use, etc. and practices for hygiene?</strong></p>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="protocols" id="protocols1" value=1 <?php if($safetyCovid && $safetyCovid[0]["protocols"] == 1) { echo "checked"; }  ?> onclick="valid_inconvenientes()">Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="protocols" id="protocols2" value=2 <?php if($safetyCovid && $safetyCovid[0]["protocols"] == 2) { echo "checked"; }  ?> onclick="valid_inconvenientes()">No
							</label>

							<input type="hidden" id="hddProtocols" name="hddProtocols" value="<?php echo $valorProtocols; ?>"/>
						</div>
						
						<div class="col-sm-8">
							<textarea id="protocols_comments" name="protocols_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $safetyCovid?$safetyCovid[0]["protocols_comments"]:""; ?></textarea>
						</div>
					</div>

					<div class="col-sm-6">
						<br><p><strong>Crew Size: </strong></p>
						<div class="col-sm-8">
							<input type="number" id="crew_size" name="crew_size" class="form-control" min="1" max="15" value="<?php echo $safetyCovid?$safetyCovid[0]["crew_size"]:""; ?>" placeholder="Crew Size">
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
						<div style="width:100%;" align="center">
							<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-primary'>
									Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
							</button>						
						</div>
					</div>
				</div>

			</form>
<!--FIN COVID -->

				</div>
			</div>
		</div>
	</div>	
</div>