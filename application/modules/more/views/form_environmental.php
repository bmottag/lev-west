<script type="text/javascript" src="<?php echo base_url("assets/js/validate/jobs/environmental_v2.js"); ?>"></script>

<div id="page-wrapper">
	<br>

	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<a class="btn btn-purpura btn-xs" href=" <?php echo base_url().'more/environmental/' . $jobInfo[0]['id_job']; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="glyphicon glyphicon-screenshot"></i> <strong>ESI - ENVIROMENTAL SITE INSPECTION</strong>
				</div>
				<div class="panel-body">
				
					<div class="alert alert-purpura">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
						<?php 
						if($information){
								echo "<br><strong>Date review: </strong>";
								echo $information[0]["date_environmental"]; 
								
								echo "<br><span class='fa fa-cloud-download' aria-hidden='true'></span> <strong>Download ESI: </strong>";
						?>
<a href='<?php echo base_url('more/generaEnvironmentalPDF/' . $jobInfo[0]['id_job'] ); ?>' target="_blank">PDF <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>	
						<?php 
						}
						?>
					</div>

<?php
$retornoExito = $this->session->flashdata('retornoExito');
if ($retornoExito) {
    ?>
    <div class="row">
		<div class="col-lg-12">	
			<div class="alert alert-success">
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
				
				</div>
			</div>
		</div>
	</div>								
	
<form  name="form" id="form" class="form-horizontal" method="post"  >
	<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $information?$information[0]["id_job_environmental"]:""; ?>"/>
	<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $jobInfo[0]["id_job"]; ?>"/>


<!-- INICIO FIRMA -->
<?php if($information){ //solo se muestran las firmas cuando hay informacion ?> 						
	
	<div class="row">
		<div class="col-lg-6">				
			<div class="panel panel-info">
				<div class="panel-heading">
					<strong>Site inspector: *</strong>
				</div>
				<div class="panel-body">								
				
					<div class="form-group">
						<div class="col-sm-12">
							<select name="inspector" id="inspector" class="form-control" required>
								<option value=''>Select...</option>
								<?php for ($i = 0; $i < count($workersList); $i++) { ?>
									<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if($information && $information[0]["fk_id_user_inspector"] == $workersList[$i]["id_user"]) { echo "selected"; }  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
								<?php } ?>
							</select>								
						</div>
					</div>

					<div class="form-group">
						<div class="row" align="center">
							<div style="width:70%;" align="center">
		<?php 
		$class = "btn-primary";						
		if($information[0]["inspector_signature"]){ 
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
								<h4 class="modal-title">Lev-West Inspector Signature</h4>      </div>      
							<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["inspector_signature"]); ?>" class="img-rounded" alt="Hauling Supervisor Signature" width="304" height="236" />   </div>      
							<div class="modal-footer">        
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
							</div>  
						</div>  
					</div>
				</div>
		<?php
		}
		?>

		<a class="btn <?php echo $class; ?>" href="<?php echo base_url("more/add_signature_esi/inspector/" . $jobInfo[0]["id_job"] . "/". $information[0]["id_job_environmental"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Lev-West Inspector Signature </a>

							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	
		<div class="col-lg-6">				
			<div class="panel panel-info">
				<div class="panel-heading">
					<strong>Manager: *</strong>
				</div>
				<div class="panel-body">								
				
					<div class="form-group">
						<div class="col-sm-12">
							<select name="manager" id="manager" class="form-control" required>
								<option value=''>Select...</option>
								<?php for ($i = 0; $i < count($workersList); $i++) { ?>
									<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if($information && $information[0]["fk_id_user_manager"] == $workersList[$i]["id_user"]) { echo "selected"; }  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
								<?php } ?>
							</select>								
						</div>
					</div>

					<div class="form-group">
						<div class="row" align="center">
							<div style="width:70%;" align="center">								 
			<?php 
			$class = "btn-primary";						
			if($information[0]["manager_signature"]){ 
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
									<h4 class="modal-title">Lev-West Manager Signature</h4>      </div>      
					<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["manager_signature"]); ?>" class="img-rounded" alt="Safety Coordinator Signature" width="304" height="236" />   </div>      
								<div class="modal-footer">        
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
								</div>  
							</div>  
						</div>
					</div>

			<?php
			}
			?>
			<a class="btn <?php echo $class; ?>" href="<?php echo base_url("more/add_signature_esi/manager/" . $jobInfo[0]["id_job"] . "/" . $information[0]["id_job_environmental"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Lev-West Manager Signature </a>

							</div>
						</div>
					</div>
					
				</div>
			</div>
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
<?php } ?>



	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<strong>1. Air pollution Control</strong>
				</div>
				<div class="panel-body">
									
					<div class="form-group">
						<label class="col-sm-4 control-label" for="sites_watered">Are the construction sites watered
to minimize dust? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="sites_watered" id="sites_watered1" value=1 <?php if($information && $information[0]["sites_watered"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="sites_watered" id="sites_watered2" value=2 <?php if($information && $information[0]["sites_watered"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="sites_watered" id="sites_watered3" value=99 <?php if($information && $information[0]["sites_watered"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="sites_watered_remarks" name="sites_watered_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["sites_watered_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="being_swept">Are the main entrance and surrounding
roads being swept? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="being_swept" id="being_swept1" value=1 <?php if($information && $information[0]["being_swept"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="being_swept" id="being_swept2" value=2 <?php if($information && $information[0]["being_swept"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="being_swept" id="being_swept3" value=99 <?php if($information && $information[0]["being_swept"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="being_swept_remarks" name="being_swept_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["being_swept_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="dusty_covered">Are all vehicles carrying dusty loads
covered? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="dusty_covered" id="dusty_covered1" value=1 <?php if($information && $information[0]["dusty_covered"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="dusty_covered" id="dusty_covered2" value=2 <?php if($information && $information[0]["dusty_covered"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="dusty_covered" id="dusty_covered3" value=99 <?php if($information && $information[0]["dusty_covered"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="dusty_covered_remarks" name="dusty_covered_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["dusty_covered_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="speed_control">Are speed control measures
applied? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="speed_control" id="speed_control1" value=1 <?php if($information && $information[0]["speed_control"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="speed_control" id="speed_control2" value=2 <?php if($information && $information[0]["speed_control"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="speed_control" id="speed_control3" value=99 <?php if($information && $information[0]["speed_control"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="speed_control_remarks" name="speed_control_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["speed_control_remarks"]:""; ?></textarea>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<strong>2. Noise Control</strong>
				</div>
				<div class="panel-body">
									
					<div class="form-group">
						<label class="col-sm-4 control-label" for="noise_permit">Is the construction noise permit
valid? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="noise_permit" id="noise_permit1" value=1 <?php if($information && $information[0]["noise_permit"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="noise_permit" id="noise_permit2" value=2 <?php if($information && $information[0]["noise_permit"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="noise_permit" id="noise_permit3" value=99 <?php if($information && $information[0]["noise_permit"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="noise_permit_remarks" name="noise_permit_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["noise_permit_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="air_compressors">Do air compressors operate with
doors closed? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="air_compressors" id="air_compressors1" value=1 <?php if($information && $information[0]["air_compressors"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="air_compressors" id="air_compressors2" value=2 <?php if($information && $information[0]["air_compressors"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="air_compressors" id="air_compressors3" value=99 <?php if($information && $information[0]["air_compressors"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="air_compressors_remarks" name="air_compressors_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["air_compressors_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="noise_mitigation">Any noise mitigation measures
adopted </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="noise_mitigation" id="noise_mitigation1" value=1 <?php if($information && $information[0]["noise_mitigation"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="noise_mitigation" id="noise_mitigation2" value=2 <?php if($information && $information[0]["noise_mitigation"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="noise_mitigation" id="noise_mitigation3" value=99 <?php if($information && $information[0]["noise_mitigation"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="noise_mitigation_remarks" name="noise_mitigation_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["noise_mitigation_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="idle_plan">Is idle plan/equipment turned off
or throttled down? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="idle_plan" id="idle_plan1" value=1 <?php if($information && $information[0]["idle_plan"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="idle_plan" id="idle_plan2" value=2 <?php if($information && $information[0]["idle_plan"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="idle_plan" id="idle_plan3" value=99 <?php if($information && $information[0]["idle_plan"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="idle_plan_remarks" name="idle_plan_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["idle_plan_remarks"]:""; ?></textarea>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<strong>3. Site Management</strong>
				</div>
				<div class="panel-body">
									
					<div class="form-group">
						<label class="col-sm-4 control-label" for="garbage_bin">Is there enough garbage bins on site?</label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="garbage_bin" id="garbage_bin1" value=1 <?php if($information && $information[0]["garbage_bin"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="garbage_bin" id="garbage_bin2" value=2 <?php if($information && $information[0]["garbage_bin"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="garbage_bin" id="garbage_bin3" value=99 <?php if($information && $information[0]["garbage_bin"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="garbage_bin_remarks" name="garbage_bin_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["garbage_bin_remarks"]:""; ?></textarea>
						</div>
					</div>
										
					<div class="form-group">
						<label class="col-sm-4 control-label" for="disposed_periodically">Are garbage bins collected and
disposed periodically? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="disposed_periodically" id="disposed_periodically1" value=1 <?php if($information && $information[0]["disposed_periodically"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="disposed_periodically" id="disposed_periodically2" value=2 <?php if($information && $information[0]["disposed_periodically"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="disposed_periodically" id="disposed_periodically3" value=99 <?php if($information && $information[0]["disposed_periodically"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="disposed_periodically_remarks" name="disposed_periodically_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["disposed_periodically_remarks"]:""; ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-4 control-label" for="recycling_being">Is recycling being followed and placed
accordingly? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="recycling_being" id="recycling_being1" value=1 <?php if($information && $information[0]["recycling_being"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="recycling_being" id="recycling_being2" value=2 <?php if($information && $information[0]["recycling_being"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="recycling_being" id="recycling_being3" value=99 <?php if($information && $information[0]["recycling_being"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="recycling_being_remarks" name="recycling_being_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["recycling_being_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="spill_containment">Is the spill containment workstation being
implemented? Is It in good conditions? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="spill_containment" id="spill_containment1" value=1 <?php if($information && $information[0]["spill_containment"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="spill_containment" id="spill_containment2" value=2 <?php if($information && $information[0]["spill_containment"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="spill_containment" id="spill_containment3" value=99 <?php if($information && $information[0]["spill_containment"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="spill_containment_remarks" name="spill_containment_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["spill_containment_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="spillage_happen">Did we have any spillage happen on site? If so,
how effective or immediately was it taking care? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="spillage_happen" id="spillage_happen1" value=1 <?php if($information && $information[0]["spillage_happen"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="spillage_happen" id="spillage_happen2" value=2 <?php if($information && $information[0]["spillage_happen"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="spillage_happen" id="spillage_happen3" value=99 <?php if($information && $information[0]["spillage_happen"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="spillage_happen_remarks" name="spillage_happen_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["spillage_happen_remarks"]:""; ?></textarea>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<strong>4. Storage of chemicals and Dangerous goods</strong>
				</div>
				<div class="panel-body">
									
					<div class="form-group">
						<label class="col-sm-4 control-label" for="chemicals_stored">Are chemicals, fuel, oils, coolant, and
hydraulic stored and labelled property? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="chemicals_stored" id="chemicals_stored1" value=1 <?php if($information && $information[0]["chemicals_stored"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="chemicals_stored" id="chemicals_stored2" value=2 <?php if($information && $information[0]["chemicals_stored"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="chemicals_stored" id="chemicals_stored3" value=99 <?php if($information && $information[0]["chemicals_stored"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="chemicals_stored_remarks" name="chemicals_stored_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["chemicals_stored_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="absorbing_chemical">Are spill kits / sand / saw dust used for
absorbing chemical spillage readily
accessible? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="absorbing_chemical" id="absorbing_chemical1" value=1 <?php if($information && $information[0]["absorbing_chemical"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="absorbing_chemical" id="absorbing_chemical2" value=2 <?php if($information && $information[0]["absorbing_chemical"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="absorbing_chemical" id="absorbing_chemical3" value=99 <?php if($information && $information[0]["absorbing_chemical"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="absorbing_chemical_remarks" name="absorbing_chemical_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["absorbing_chemical_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="spill_kits">Do all equipment, & trucks have
spill kits? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="spill_kits" id="spill_kits1" value=1 <?php if($information && $information[0]["spill_kits"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="spill_kits" id="spill_kits2" value=2 <?php if($information && $information[0]["spill_kits"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="spill_kits" id="spill_kits3" value=99 <?php if($information && $information[0]["spill_kits"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="spill_kits_remarks" name="spill_kits_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["spill_kits_remarks"]:""; ?></textarea>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<strong>5. Resource Conservation</strong>
				</div>
				<div class="panel-body">
									
					<div class="form-group">
						<label class="col-sm-4 control-label" for="excessive_use">Are Diesel-powered plant and
equipment shut off while not in use to
reduce excessive use? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="excessive_use" id="excessive_use1" value=1 <?php if($information && $information[0]["excessive_use"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="excessive_use" id="excessive_use2" value=2 <?php if($information && $information[0]["excessive_use"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="excessive_use" id="excessive_use3" value=99 <?php if($information && $information[0]["excessive_use"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="excessive_use_remarks" name="excessive_use_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["excessive_use_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="materials_stored">Are materials stored in good condition
to prevent deterioration and wastage? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="materials_stored" id="materials_stored1" value=1 <?php if($information && $information[0]["materials_stored"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="materials_stored" id="materials_stored2" value=2 <?php if($information && $information[0]["materials_stored"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="materials_stored" id="materials_stored3" value=99 <?php if($information && $information[0]["materials_stored"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="materials_stored_remarks" name="materials_stored_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["materials_stored_remarks"]:""; ?></textarea>
						</div>
					</div>
					

					

					
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<strong>6. Emergency Preparedness and Response</strong>
				</div>
				<div class="panel-body">
									
					<div class="form-group">
						<label class="col-sm-4 control-label" for="fire_extinguishers">Are fire extinguishers / fighting
facilities properly maintained and not
expired? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="fire_extinguishers" id="fire_extinguishers1" value=1 <?php if($information && $information[0]["fire_extinguishers"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="fire_extinguishers" id="fire_extinguishers2" value=2 <?php if($information && $information[0]["fire_extinguishers"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="fire_extinguishers" id="fire_extinguishers3" value=99 <?php if($information && $information[0]["fire_extinguishers"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="fire_extinguishers_remarks" name="fire_extinguishers_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["fire_extinguishers_remarks"]:""; ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label" for="preventive_actions">Are accidents and incidents reported and
reviewed, and corrective & preventive actions
identified and recorded? </label>
						<div class="col-sm-3">
							<label class="radio-inline">
								<input type="radio" name="preventive_actions" id="preventive_actions1" value=1 <?php if($information && $information[0]["preventive_actions"] == 1) { echo "checked"; }  ?>>Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="preventive_actions" id="preventive_actions2" value=2 <?php if($information && $information[0]["preventive_actions"] == 2) { echo "checked"; }  ?>>No
							</label>
							<label class="radio-inline">
								<input type="radio" name="preventive_actions" id="preventive_actions3" value=99 <?php if($information && $information[0]["preventive_actions"] == 99) { echo "checked"; }  ?>>N/A
							</label>
						</div>
						
						<div class="col-sm-5">
							<textarea id="preventive_actions_remarks" name="preventive_actions_remarks" placeholder="Remarks: Location good practices, problem observed, Yes NO possible causes"  class="form-control" rows="1"><?php echo $information?$information[0]["preventive_actions_remarks"]:""; ?></textarea>
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

<!--INICIO Modal para NEW HAZARD -->
<div class="modal fade text-center" id="modalNewHazard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatosNewHazard">

		</div>
	</div>
</div>                       
<!--FIN Modal para NEW HAZARD -->