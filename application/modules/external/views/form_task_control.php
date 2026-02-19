<script type="text/javascript" src="<?php echo base_url("assets/js/validate/external/task_control.js"); ?>"></script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-success">
				<div class="panel-heading">
					<i class="fa fa-bug"></i> <strong>COVID-19 | PSI SUPPLEMENT | TASK ASSESSMENT AND CONTROL</strong>
				</div>
				<div class="panel-body">
				
					<div class="alert alert-success">
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
						<?php 
						if($information){
								echo "<br><strong>Date review: </strong>";
								echo $information[0]["date_task_control"]; 
								
								echo "<br><strong>Download TAC: </strong>";
						?>
<a href='<?php echo base_url('more/generaTaskControlPDF/' . $information[0]["id_job_task_control"] ); ?>' target="_blank">PDF <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>	
						<?php 
						}
						?>
					</div>
				
				</div>
			</div>
		</div>
	</div>								
	
<?php
$retornoExito = $this->session->flashdata('retornoExito');
if ($retornoExito) {
    ?>
	<div class="col-lg-12">	
		<div class="alert alert-success">
			<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			<?php echo $retornoExito ?>		
		</div>
	</div>
    <?php
}

$retornoError = $this->session->flashdata('retornoError');
if ($retornoError) {
    ?>
	<div class="col-lg-12">	
		<div class="alert alert-danger ">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<?php echo $retornoError ?>
		</div>
	</div>
    <?php
}
?> 

<form  name="form" id="form" class="form-horizontal" method="post"  >
	<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $information?$information[0]["id_job_task_control"]:""; ?>"/>
	<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $jobInfo[0]["id_job"]; ?>"/>


<!-- INICIO FIRMA -->
<?php if($information){ //solo se muestran las firmas cuando hay informacion 
			
			$bandera =	FALSE;
			if($information[0]["distancing"] == 2 || $information[0]["sharing_tools"] == 2 || $information[0]["required_ppe"] == 2 || $information[0]["symptoms"] == 2 || $information[0]["protocols"] == 2)
			{
				$bandera =	TRUE;
			}

			if($bandera){
?>

	<div class="row">
		<div class="col-lg-4">
			<div class="alert alert-danger">
				<strong>Attention: </strong>
				<p>
					If you have selected "NO" to any of the questions, STOP!<br>
					You must not start work until you have:
					<ul>
						<li>Developed additional mitigation strategies;</li>
						<li>Reviewed the mitigation strategies and they have been approved by the Superintendent.</li>
						<li>Ensured you are in compliance with Alberta OH&S regarding "Right to refuse dangerous work".</li>
					</ul>
				</p>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="alert alert-success">
				<strong>Sample Mitigation Strategies: </strong>
				<p>
					The mitigation strategies can include, but are not limited, to items such as…<br>
					You must not start work until you have:
					<ul>
						<li>Splitting crew sizes</li>
						<li>Providing respirators and full-faceshields when distance cannot be maintained</li>
						<li>Utilizing additional equipment to maintain distancing</li>
						<li>Providing shielding to provide a barrier between workers</li>
						<li>Staggering breaks to prevent exposure</li>
						<li>Disinfecting tools that must be shared</li>
						<li>Cleaning offices lunch rooms and other common areas as per COVID-19 Cleaning schedule</li>
						<li>Social distancing of 6 feet required</li>
					</ul>
				</p>
			</div>
		</div>
	
		<div class="col-lg-4">
			<div class="alert alert-success">
				<strong>Mitigation Plan: </strong>
				<p>
					Where an additional mitigation plan is required, it must be approved by the Superintendent(s) and Project
					Manager(s). This includes where items have been highlighted for example splitting crews, adjusting task,
					additional disinfectant steps, barriers, face shields, etc.
				</p>
			</div>
		</div>
	</div>
	
			<?php } ?>
	
	<div class="row">
		<div class="col-lg-6">				
			<div class="panel panel-info">
				<div class="panel-heading">
					<strong>Supervisor: </strong> 
					<?php 
						if($information[0]["supervisor"] =! ''){
							echo $information[0]["name"]; 
						}else{
							echo $information[0]["supervisor"]; 
						}
					?>
				</div>
				<div class="panel-body">								
				
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:70%;" align="center">

		<?php 
		$class = "btn-primary";						
		if($information[0]["supervisor_signature"]){ 
			$class = "btn-default";
		?>
				<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" >
					<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
				</button>

				<div id="myModal" class="modal fade" role="dialog">  
					<div class="modal-dialog">
						<div class="modal-content">      
							<div class="modal-header">        
								<button type="button" class="close" data-dismiss="modal">×</button>        
								<h4 class="modal-title">Supervisor Signature</h4>      </div>      
							<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["supervisor_signature"]); ?>" class="img-rounded" alt="Hauling Supervisor Signature" width="304" height="236" />   </div>      
							<div class="modal-footer">        
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
							</div>  
						</div>  
					</div>
				</div>
		<?php
		}
		?>

		<a class="btn <?php echo $class; ?>" href="<?php echo base_url("external/add_signature_tac/supervisor/" . $jobInfo[0]["id_job"] . "/". $information[0]["id_job_task_control"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Supervisor Signature </a>

							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	
<?php if($bandera){  ?>
		<div class="col-lg-6">				
			<div class="panel panel-info">
				<div class="panel-heading">
					<strong>Project Superintendent: </strong><?php echo $information[0]["superintendent"]; ?>
				</div>
				<div class="panel-body">								
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:70%;" align="center">								 
			<?php 
			$class = "btn-primary";						
			if($information[0]["superintendent_signature"]){ 
				$class = "btn-default";
			?>
					<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myContractorModal" >
						<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
					</button>

					<div id="myContractorModal" class="modal fade" role="dialog">  
						<div class="modal-dialog">
							<div class="modal-content">      
								<div class="modal-header">        
									<button type="button" class="close" data-dismiss="modal">×</button>        
									<h4 class="modal-title">Project Superintendent Signature</h4>      </div>      
					<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["superintendent_signature"]); ?>" class="img-rounded" alt="Safety Coordinator Signature" width="304" height="236" />   </div>
								<div class="modal-footer">        
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
								</div>  
							</div>  
						</div>
					</div>

			<?php
			}
			?>
			<a class="btn <?php echo $class; ?>" href="<?php echo base_url("external/add_signature_tac/superintendent/" . $jobInfo[0]["id_job"] . "/" . $information[0]["id_job_task_control"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Superintendent Signature </a>

							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>	
<?php } ?>
		
		
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
<?php } ?>


	<div class="row">
		<div class="col-lg-5">
			<div class="panel panel-success">
				<div class="panel-heading">
					<strong>General Information</strong>
				</div>
				<div class="panel-body">		

					<div class="col-sm-12">
						<label for="company" class="control-label">Name: *</label>
						<input type="text" id="name" name="name" class="form-control" value="<?php echo $information?$information[0]["name"]:""; ?>" placeholder="Name" required >
					</div>

					<div class="col-sm-12">
						<label for="company" class="control-label">Phone number: *</label>
						<input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo $information?$information[0]["contact_phone_number"]:""; ?>" placeholder="Works phone number" required >
					</div>					
						
					<div class="col-sm-12">
						<label for="company" class="control-label">Company: *</label>
						<select name="company" id="company" class="form-control" >
							<option value=''>Select...</option>
							<?php for ($i = 0; $i < count($companyList); $i++) { ?>
								<option value="<?php echo $companyList[$i]["id_company"]; ?>" <?php if($information && $information[0]["fk_id_company"] == $companyList[$i]["id_company"]) { echo "selected"; }  ?>><?php echo $companyList[$i]["company_name"]; ?></option>
							<?php } ?>
						</select>
					</div>
						
					<div class="col-sm-12">
						<label for="type" class="control-label">Work Location: *</label>
						<textarea id="work_location" name="work_location" placeholder="Work Location"  class="form-control" rows="2"><?php echo $information?$information[0]["work_location"]:""; ?></textarea>
					</div>
					
					<div class="col-sm-12">
						<label for="type" class="control-label">Crew Size: *</label>
						<select name="crew_size" id="crew_size" class="form-control" required>
							<option value='' >Select...</option>
							<?php for ($i = 1; $i <= 15; $i++) { ?>
								<option value='<?php echo $i; ?>' <?php if ($information && $i == $information[0]["crew_size"]) { echo 'selected="selected"'; } ?> ><?php echo $i; ?></option>
							<?php } ?>									
						</select>
					</div>
					
					<div class="col-sm-12">
						<label for="type" class="control-label">Task: *</label>
						<textarea id="task" name="task" placeholder="Task"  class="form-control" rows="2"><?php echo $information?$information[0]["task"]:""; ?></textarea>
					</div>						
					
					<div class="col-sm-12">
						<label for="superintendent" class="control-label">Superintendent name: *</label>
						<input type="text" class="form-control" id="superintendent" name="superintendent" value="<?php echo $information?$information[0]["superintendent"]:""; ?>" />
					</div>	
					
				</div>
			</div>
		</div>

		<div class="col-lg-7">				
			<div class="panel panel-success">
				<div class="panel-heading">
					<strong>Questions must be answered prior to performing work</strong>
				</div>
				<div class="panel-body">
														
					<div class="col-sm-12">
						<label for="type" class="control-label">Can 6ft distancing be maintained between workers during the
task?</label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="distancing" id="distancing1" value=1 <?php if($information && $information[0]["distancing"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="distancing" id="distancing2" value=2 <?php if($information && $information[0]["distancing"] == 2) { echo "checked"; }  ?>>No
							</label>
						</div>
						
						<div class="col-sm-8">
							<textarea id="distancing_comments" name="distancing_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $information?$information[0]["distancing_comments"]:""; ?></textarea>
						</div>
					</div>			
					
					<div class="col-sm-12">
						<label for="type" class="control-label"><br>Workers can perform their tasks without sharing tools or
equipment? </label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="sharing_tools" id="sharing_tools1" value=1 <?php if($information && $information[0]["sharing_tools"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="sharing_tools" id="sharing_tools2" value=2 <?php if($information && $information[0]["sharing_tools"] == 2) { echo "checked"; }  ?>>No
							</label>
						</div>
						
						<div class="col-sm-8">
							<textarea id="sharing_tools_comments" name="sharing_tools_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $information?$information[0]["sharing_tools_comments"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="col-sm-12">
						<label for="type" class="control-label"><br>All workers have the required PPE to safely perform their work?
GLOVES ARE MANDATORY. </label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="required_ppe" id="required_ppe1" value=1 <?php if($information && $information[0]["required_ppe"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="required_ppe" id="required_ppe2" value=2 <?php if($information && $information[0]["required_ppe"] == 2) { echo "checked"; }  ?>>No
							</label>
						</div>
						
						<div class="col-sm-8">
							<textarea id="required_ppe_comments" name="required_ppe_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $information?$information[0]["required_ppe_comments"]:""; ?></textarea>
						</div>
					</div>

					<div class="col-sm-12">
						<label for="type" class="control-label"><br>All workers have no signs or symptoms of being ill (i.e.: Sore throat,
fever, dry cough, shortness of breath)? </label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="symptoms" id="symptoms1" value=1 <?php if($information && $information[0]["symptoms"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="symptoms" id="symptoms2" value=2 <?php if($information && $information[0]["symptoms"] == 2) { echo "checked"; }  ?>>No
							</label>
						</div>
						
						<div class="col-sm-8">
							<textarea id="symptoms_comments" name="symptoms_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $information?$information[0]["symptoms_comments"]:""; ?></textarea>
						</div>
					</div>
						
					<div class="col-sm-12">
						<label for="type" class="control-label"><br>Crew is aware of site COVID protocols for breaks, lunchrooms,
washrooms, elevator use, etc. and practices for hygiene? </label>
						<div class="col-sm-4">
							<label class="radio-inline">
								<input type="radio" name="protocols" id="protocols1" value=1 <?php if($information && $information[0]["protocols"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="protocols" id="protocols2" value=2 <?php if($information && $information[0]["protocols"] == 2) { echo "checked"; }  ?>>No
							</label>
						</div>
						
						<div class="col-sm-8">
							<textarea id="protocols_comments" name="protocols_comments" placeholder="Comments"  class="form-control" rows="1"><?php echo $information?$information[0]["protocols_comments"]:""; ?></textarea>
						</div>
					</div>
										
				</div>
			</div>
		</div>
				
	</div>
	

								
<?php if(!$information){ ?>
						<div class="form-group">
							<div class="row" align="center">
								<div style="width:100%;" align="center">							
<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-primary'>
		Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
</button>
									

								</div>
							</div>
						</div>
<?php } ?>
								

								
						<div class="form-group">
							<div class="row" align="center">
								<div style="width:80%;" align="center">
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
							</div>
						</div>								

	
</form>

</div>
<!-- /#page-wrapper -->