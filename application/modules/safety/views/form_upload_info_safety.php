<script type="text/javascript" src="<?php echo base_url("assets/js/validate/safety/safety.js"); ?>"></script>

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

<?php 
	if($safetyClose){
?>
	<div class="row">
		<div class="col-lg-12">	
			<div class="alert alert-success ">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				This safety form is close.
			</div>
		</div>
	</div>
<?php		
	}else{
?>



	<div class="row">
		<div class="col-lg-6">								
			<div class="alert alert-info">
<?php
	$ppe=$information[0]['ppe']==1?"Yes":"No";
?>
<strong>Task(s)to be done: </strong><br><?php echo $information?$information[0]["work"]:""; ?>
<br><strong>Job Code/Name: </strong><?php echo $information?$information[0]["job_description"]:""; ?>
<br><strong>Primary muster point: </strong><?php echo $information?$information[0]["muster_point"]:""; ?>
<?php if($information[0]["muster_point_2"]){ ?>
<br><strong>Secondary muster point: </strong><?php echo $information?$information[0]["muster_point_2"]:""; ?>
<?php } ?>
<br><strong>PPE (Basic): </strong><?php echo $ppe; ?>
<?php if($information[0]["specify_ppe"]){ ?>
<br><strong>Specialized PPE: </strong><?php echo $information?$information[0]["specify_ppe"]:""; ?>
<?php } ?>

<a class="btn btn-success btn-xs" href="<?php echo base_url("safety/add_safety/" . $information[0]["fk_id_job"] . "/" . $information[0]["id_safety"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit </a>

<br><strong>Date & Time: </strong><?php echo $information?$information[0]["date"]:""; ?>
<?php 
if($information){ 	
	echo "<br><strong>Download FLHA: </strong>";
?>
<a href='<?php echo base_url('report/generaSafetyPDF/x/x/x/' . $information[0]["id_safety"] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>	
<?php 
}
?>


										</div>
									</div>
								
				<div class="col-lg-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-edit fa-fw"></i> Meeting conducted by Signature
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
						
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:80%;" align="center">
								<?php 								
									$class = "btn-primary";						
									if($information[0]["signature"]){ 
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
				<h4 class="modal-title">Meeting conducted by Signature</h4>      </div>      
			<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["signature"]); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
			<div class="modal-footer">        
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
			</div>  
		</div>  
	</div>
</div>
								<?php
								}
								?>
						
<a class="btn <?php echo $class; ?>" href="<?php echo base_url("safety/add_signature/advisor/" . $information[0]["id_safety"] . "/x"); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

									</div>
								</div>
							</div>
					
						</div>
						<!-- /.panel-body -->
			</div>
		</div>
	</div>

<?php		
	}
?>
					<!-- /.row (nested) -->
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	
	
	<!--INICIO HAZARDS -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-primary">
				<div class="panel-heading">
					<strong>HAZARDS</strong>
				</div>
				<div class="panel-body">
				
					<div class="col-lg-12">	
						<a href="<?php echo base_url("safety/add_hazards_flha/" . $information[0]["fk_id_job"] . "/" . $information[0]["id_safety"]); ?>" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Hazards</a>
						<br>
					</div>
														
				<?php 
				
					if($safetyHazard){
				?>
					<table class="table table-bordered table-striped table-hover table-condensed">
						<tr class="dafault">
							<th class='text-center'>Activity</th>
							<th class='text-center'>Hazard</th>
							<th class='text-center'>Solution</th>
							<th class='text-center'>Priority</th>
						</tr>
						<?php
							foreach ($safetyHazard as $data):
								$priority = $data['priority_description'];
								
								if($priority == 1 || $priority == 2) {
									$class = "success";
								}elseif($priority == 3 || $priority == 4) {
									$class = "info";
								}elseif($priority == 5 || $priority == 6) {
									$class = "warning";
								}elseif($priority == 7 || $priority == 8) {
									$class = "danger";
								}

								echo "<tr>";					
								echo "<td class='text-" . $class . " " . $class . "'><small>" . $data['hazard_activity'] . "</small></td>";
								echo "<td class='text-" . $class . " " . $class . "'><small>" . $data['hazard_description'] . "</small></td>";
								echo "<td class='text-" . $class . " " . $class . "'><small>" . $data['solution']  . "</small></td>";
								echo "<td class='text-center " . $class . "'><p class='text-" . $class . "'><strong>" . $data['priority_description'] . "</strong></p></td>";
								                    
								echo "</tr>";
							endforeach;
						?>
					</table>
				<?php } ?>

				</div>
			</div>
		</div>
	</div>
	<!--INICIO HAZARDS -->
	
	<!--INICIO WORKERS -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-warning">
				<div class="panel-heading">
					<strong>VCI WORKERS</strong>
				</div>
				<div class="panel-body">	
										
					<div class="col-lg-12">	
<?php if($safetyWorkers){ ?>
												
					<button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#modalWorker" id="x">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add VCI Workers
					</button>
<?php }else { ?>
					<a href="<?php echo base_url("safety/add_workers/" . $information[0]["id_safety"]); ?>" class="btn btn-warning btn-block"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add VCI Workers</a>
												
<?php } ?>
											
						<br>
					</div>

										
<?php 
	if($safetyWorkers){
?>
					<table class="table table-bordered table-striped table-hover table-condensed">
						<tr>
							<th class='text-center'>Name</th>
							<th class='text-center'>Signature</th>
							<th class='text-center'>Delete</th>
						</tr>
						<?php
							foreach ($safetyWorkers as $data):
								echo "<tr>";					
								echo "<td ><small>" . $data['name'] . "</small></td>";
								echo "<td class='text-center'><small><center>";
		$class = "btn-primary";						
		if($data['signature']){ 
			$class = "btn-default";
			
		?>
		<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#<?php echo $data['id_safety_worker'] . "wModal"; ?>" id="<?php echo $data['id_safety_worker']; ?>">
			<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
		</button>

		<div id="<?php echo $data['id_safety_worker'] . "wModal"; ?>" class="modal fade" role="dialog">  
			<div class="modal-dialog">
				<div class="modal-content">      
					<div class="modal-header">        
						<button type="button" class="close" data-dismiss="modal">×</button>        
						<h4 class="modal-title">Worker Signature</h4>      </div>      
					<div class="modal-body text-center"><img src="<?php echo base_url($data['signature']); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
					<div class="modal-footer">    
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
					</div>  
				</div>  
			</div>
		</div>
		<?php
		}
						?>
							
							<a class='btn <?php echo $class; ?> btn-xs' href='<?php echo base_url('safety/add_signature/worker/' . $data['fk_id_safety'] . '/' . $data['id_safety_worker']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
							</a>
							</center>
						<?php
								echo "</small></td>"; 
								echo "<td class='text-center'><small>";
						?>
							<center>
							<a class='btn btn-danger btn-xs' href='<?php echo base_url('safety/deleteSafetyWorker/' . $data['id_safety_worker'] . '/' . $data['fk_id_safety']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-trash" aria-hidden="true"> </span> 
							</a>
							</center>
						<?php
								echo "</small></td>";                     
								echo "</tr>";
							endforeach;
						?>
					</table>
<?php } ?>

				</div>
			</div>
		</div>
	</div>
	<!--INICIO WORKERS -->
	
	<!--INICIO SUBCONTRACTOR WORKER-->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-danger">
				<div class="panel-heading">
					<strong>SUBCONTRACTOR WORKERS</strong>
				</div>
				<div class="panel-body">	
										
					<div class="col-lg-12">													
						<button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#modalSubcontractorWorker" id="x">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Subcontractor Worker
						</button>
						<br>
					</div>

										
<?php 
	if($safetySubcontractorsWorkers){
?>
					<table class="table table-bordered table-striped table-hover table-condensed">
						<tr>
							<th class='text-center'>Name</th>
							<th class='text-center'>Company</th>
							<th class='text-center'>Mobile number</th>
							<th class='text-center'>Signature</th>
							<th class='text-center'>Delete</th>
						</tr>
						<?php
							foreach ($safetySubcontractorsWorkers as $data):
								echo "<tr>";					
								echo "<td ><small>" . $data['worker_name'] . "</small></td>";
								echo "<td ><small>" . $data['company_name'] . "</small></td>";
								echo "<td class='text-center'><small>" . $data['worker_movil_number'] . "</small></td>";
								echo "<td class='text-center'><small><center>";
		$class = "btn-primary";						
		if($data['signature']){ 
			$class = "btn-default";
			
		?>
		<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#<?php echo $data['id_safety_subcontractor'] . "SubcontractorModal"; ?>" id="<?php echo $data['id_safety_subcontractor']; ?>">
			<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
		</button>

		<div id="<?php echo $data['id_safety_subcontractor'] . "SubcontractorModal"; ?>" class="modal fade" role="dialog">  
			<div class="modal-dialog">
				<div class="modal-content">      
					<div class="modal-header">        
						<button type="button" class="close" data-dismiss="modal">×</button>        
						<h4 class="modal-title">Worker Signature</h4>      </div>      
					<div class="modal-body text-center"><img src="<?php echo base_url($data['signature']); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
					<div class="modal-footer">    
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
					</div>  
				</div>  
			</div>
		</div>
		<?php
		}
						?>
							
							<a class='btn <?php echo $class; ?> btn-xs' href='<?php echo base_url('safety/add_signature/subcontractor/' . $data['fk_id_safety'] . '/' . $data['id_safety_subcontractor']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
							</a>
							</center>
						<?php
								echo "</small></td>"; 
								echo "<td class='text-center'><small>";
						?>
							<center>
							<a class='btn btn-danger btn-xs' href='<?php echo base_url('safety/deleteSafetySubcontractor/' . $data['id_safety_subcontractor'] . '/' . $data['fk_id_safety']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>
							</a>
							</center>
						<?php
								echo "</small></td>";                     
								echo "</tr>";
							endforeach;
						?>
					</table>
<?php } ?>

				</div>
			</div>
		</div>
	</div>
	<!--FIN SUBCONTRACTOR WORKER-->
	
	
</div>
<!-- /#page-wrapper -->

<!--INICIO Modal para adicionar WORKER -->
<div class="modal fade text-center" id="modalWorker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="exampleModalLabel">ADD WORKER</h4>
			</div>

			<div class="modal-body">
				<form name="formHazard" id="formHazard" role="form" method="post" action="<?php echo base_url("safety/safet_One_Worker") ?>" >
					<input type="hidden" id="hddId" name="hddId" value="<?php echo $information[0]["id_safety"]; ?>"/>
					
					<div class="form-group text-left">
						<label class="control-label" for="worker">Worker</label>
						<select name="worker" id="worker" class="form-control" required>
							<option value=''>Select...</option>
							<?php for ($i = 0; $i < count($workersList); $i++) { ?>
								<option value="<?php echo $workersList[$i]["id_user"]; ?>" ><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
							<?php } ?>
						</select>
					</div>
					
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:50%;" align="center">
								<input type="submit" id="btnSubmitWorker" name="btnSubmitWorker" value="Save" class="btn btn-primary"/>
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
						
				</form>
			</div>

		</div>
	</div>
</div>                       
<!--FIN Modal para adicionar WORKER -->

<!--INICIO Modal para adicionar subcontractor WORKER -->
<div class="modal fade text-center" id="modalSubcontractorWorker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="exampleModalLabel">ADD SUBCONTRACTOR WORKER</h4>
			</div>

			<div class="modal-body">
				<form name="formSubcontractor" id="formSubcontractor" role="form" method="post" action="<?php echo base_url("safety/safet_subcontractor_Worker") ?>" >
					<input type="hidden" id="hddId" name="hddId" value="<?php echo $information[0]["id_safety"]; ?>"/>
					
					<div class="row">
						<div class="col-sm-6">	
							<div class="form-group text-left">
								<label class="control-label" for="company">Company: *</label>
								<select name="company" id="company" class="form-control" required>
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($companyList); $i++) { ?>
										<option value="<?php echo $companyList[$i]["id_company"]; ?>" ><?php echo $companyList[$i]["company_name"]; ?></option>	
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group text-left">
								<label class="control-label" for="workerName">Worker Name: *</label>
								<input type="text" id="workerName" name="workerName" class="form-control" placeholder="Worker Name" required >
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">	
							<div class="form-group text-left">
								<label for="phone_number">Worker mobile number:</label>
								<input type="number" id="phone_number" name="phone_number" class="form-control" placeholder="Worker mobile number" maxlength="12">
							</div>
						</div>
						<div class="col-sm-6">

						</div>
					</div>

					<div class="form-group">
						<div class="row" align="center">
							<div style="width:50%;" align="center">
								<input type="submit" id="btnSubmitSubcontractor" name="btnSubmitSubcontractor" value="Save" class="btn btn-primary"/>
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
						
				</form>
			</div>

		</div>
	</div>
</div>                       
<!--FIN Modal para adicionar WORKER -->