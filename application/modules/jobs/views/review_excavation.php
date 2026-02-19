<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<i class="fa fa-pied-piper-alt"></i> <strong>EXCAVATION AND TRENCHING PLAN</strong>
				</div>
				<div class="panel-body">
					<div class="alert alert-danger">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $information?$information[0]["job_description"]:""; ?>
						<br><span class='fa fa-cloud-download' aria-hidden='true'></span> <strong>Download Excavation and Trenching Plan: </strong>
						<a href='<?php echo base_url('jobs/generaExcavationPDF/' . $information[0]["id_job_excavation"] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						
						<?php if($information[0]["fk_id_confined"]){ ?>
						 	<br><span class='fa fa-cloud-download' aria-hidden='true'></span> <strong>Download Confined Space Entry Permit: </strong>
							<a href='<?php echo base_url('more/generaConfinedPDF/' . $information[0]["fk_id_confined"] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php } ?>
						<?php if($information[0]["method_system_doc"]){ ?>
							<br><a href="<?php echo base_url('files/excavation/' . $information[0]["method_system_doc"]) ?>" target="_blank">Attached document: <?php echo $information[0]["method_system_doc"]; ?></a>
						<?php } ?>
						<?php if($information[0]["permit_required_doc"]){ ?>
							<br><a href="<?php echo base_url('files/excavation/' . $information[0]["permit_required_doc"]) ?>" target="_blank">Attached document: <?php echo $information[0]["permit_required_doc"]; ?></a>
						<?php } ?>
						<?php if($information[0]["excavation_sketch_doc"]){ ?>
							<br><a href="<?php echo base_url('files/excavation/' . $information[0]["excavation_sketch_doc"]) ?>" target="_blank">Attached document: <?php echo $information[0]["excavation_sketch_doc"]; ?></a>
						<?php } ?>
					</div>
				<?php 
					if($this->session->rol && $information){
				?>
					<ul class="nav nav-tabs">
						<li><a href="<?php echo base_url('jobs/add_excavation/' . $information[0]['fk_id_job'] . '/' . $information[0]['id_job_excavation']); ?>">Main Form</a>
						</li>
						<li><a href="<?php echo base_url('jobs/upload_excavation_personnel/' . $information[0]['id_job_excavation']); ?>">Personnel</a></li>
						<li><a href="<?php echo base_url('jobs/upload_protection_methods/' . $information[0]['id_job_excavation']); ?>">Protection Methods & Systems</a></li>
						<li><a href="<?php echo base_url('jobs/upload_access_egress/' . $information[0]['id_job_excavation']); ?>">Access & Egress </a></li>
						<li><a href="<?php echo base_url('jobs/upload_affected_zone/' . $information[0]['id_job_excavation']); ?>">Affected Zone </a></li>
						<li><a href="<?php echo base_url('jobs/upload_de_watering/' . $information[0]['id_job_excavation']); ?>">De-Watering </a></li>
						<li><a href="<?php echo base_url('jobs/upload_sketch/' . $information[0]['id_job_excavation']); ?>">Excavation / Trench Sketch </a></li>
						<li class='active'><a href="<?php echo base_url('jobs/review_excavation/' . $information[0]['id_job_excavation']); ?>">Approvals / Review </a></li>
					</ul>
					<br>
				<?php
					}
				?>

	<div class="row">
		<div class="col-lg-6">
			<div class="panel panel-primary">
				<div class="panel-body">
					<strong>Job Code/Name: </strong><?php echo $information[0]["job_description"]; ?><br>
					<strong>Date: </strong><?php echo $information[0]["date_excavation"]; ?><br>
					<strong>Project Location: </strong><?php echo $information[0]["project_location"]; ?><br>
					<strong>Anticipated Depth of excavation / trench: </strong><?php echo $information[0]["depth"]; ?> meters<br>
					<strong>Width: </strong><?php echo $information[0]["width"]; ?> meters<br>
					<strong>Length: </strong><?php echo $information[0]["length"]; ?> meters<br>
					Will or could this excavation / trench be considered a confined space?
					<?php echo $information[0]["confined_space"]==1?"Si":"No"; ?><br>
					Will the excavation / trench atmospheric conditions be tested daily? 
					<?php echo $information[0]["tested_daily"]==1?"Si":"No"; ?><br>
					<?php 
						if($information[0]["tested_daily_explanation"]){
							echo $information[0]["tested_daily_explanation"] . "<br>";
						}
					?>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
	        <div class="panel panel-primary">
	            <div class="panel-heading">
	                <i class="fa fa-edit fa-fw"></i> Project Manager - Signature
	            </div>

                <div class="panel-body">						
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:80%;" align="center">
							<?php 								
								$class = "btn-primary";						
								if($information[0]["manager_signature"])
								{ 
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
													<h4 class="modal-title">Project Manager - Signature</h4>      </div>      
												<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["manager_signature"]); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
												<div class="modal-footer">        
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
												</div>  
											</div>  
										</div>
									</div>
							<?php
								}
							?>
						
								<a class="btn <?php echo $class; ?>" href="<?php echo base_url("jobs/add_signature_excavation/manager/" . $information[0]["id_job_excavation"] . "/x"); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

								<br><br>
								<strong><?php echo $information[0]["manager"]; ?></strong>

							</div>
						</div>
					</div>
				</div>
			</div>

	        <div class="panel panel-primary">
	            <div class="panel-heading">
	                <i class="fa fa-edit fa-fw"></i> Operator performing excavation - Signature
	            </div>

                <div class="panel-body">						
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:80%;" align="center">
							<?php 								
								$class = "btn-primary";						
								if($information[0]["operator_signature"])
								{ 
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
													<h4 class="modal-title">Operator performing excavation - Signature</h4>      </div>      
												<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["operator_signature"]); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
												<div class="modal-footer">        
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
												</div>  
											</div>  
										</div>
									</div>
							<?php
								}
							?>
						
								<a class="btn <?php echo $class; ?>" href="<?php echo base_url("jobs/add_signature_excavation/operator/" . $information[0]["id_job_excavation"] . "/x"); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

								<br><br>
								<strong><?php echo $information[0]["operator"]; ?></strong>

							</div>
						</div>
					</div>
				</div>
			</div>

	        <div class="panel panel-primary">
	            <div class="panel-heading">
	                <i class="fa fa-edit fa-fw"></i> Person supervising excavation - Signature
	            </div>

                <div class="panel-body">						
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:80%;" align="center">
							<?php 								
								$class = "btn-primary";						
								if($information[0]["supervisor_signature"])
								{ 
									$class = "btn-default";
							?>
									<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal2" >
										<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
									</button>

									<div id="myModal2" class="modal fade" role="dialog">  
										<div class="modal-dialog">
											<div class="modal-content">      
												<div class="modal-header">        
													<button type="button" class="close" data-dismiss="modal">×</button>        
													<h4 class="modal-title">Person supervising excavation - Signature</h4>      </div>      
												<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["supervisor_signature"]); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
												<div class="modal-footer">        
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
												</div>  
											</div>  
										</div>
									</div>
							<?php
								}
							?>
						
								<a class="btn <?php echo $class; ?>" href="<?php echo base_url("jobs/add_signature_excavation/supervisor/" . $information[0]["id_job_excavation"] . "/x"); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

								<br><br>
								<strong><?php echo $information[0]["supervisor"]; ?></strong>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	
	</div>

<!--INICIO WORKERS -->
<?php 
	if($excavationWorkers){
?>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-primary">
				<div class="panel-heading">
					<i class="fa fa-user"></i> <strong>VCI WORKERS</strong>
				</div>
				<div class="panel-body">

					<table class="table table-hover">
						<thead>
							<tr>
								<th>Name</th>
								<th class='text-center'>Signature</th>
							</tr>
						</thead>
					<?php
						foreach ($excavationWorkers as $data):
							echo "<tr>";					
							echo "<td >" . $data['name'] . "</td>";
							echo "<td class='text-center'><small><center>";
							$class = "btn-primary";

							if($data['signature']){ 
								$class = "btn-default";
					?>
								<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#<?php echo $data['id_excavation_worker'] . "wModal"; ?>" id="<?php echo $data['id_excavation_worker']; ?>">
									<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
								</button>

								<div id="<?php echo $data['id_excavation_worker'] . "wModal"; ?>" class="modal fade" role="dialog">  
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
							<a class='btn <?php echo $class; ?> btn-sm' href='<?php echo base_url('jobs/add_signature_excavation/worker/' . $data['fk_id_job_excavation'] . '/' . $data['id_excavation_worker']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
							</a>
							</center>
					<?php
							echo "</small></td>"; 
							echo "</tr>";
						endforeach;
					?>
					</table>

				</div>
			</div>
		</div>
	</div>
<?php } ?>
<!--FIN WORKERS -->

<!--INICIO OCCASIONAL SUBCONTRACTOR -->
<?php 
	if($excavationSubcontractors){
?>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-primary">
				<div class="panel-heading">
					<i class="fa fa-user"></i> <strong>SUBCONTRACTOR WORKERS</strong>
				</div>
				<div class="panel-body">

					<table class="table table-hover">
						<thead>
							<tr>
								<th>Name</th>
								<th>Company</th>
								<th class='text-center'>Signature</th>
							</tr>
						</thead>
					<?php
						foreach ($excavationSubcontractors as $data):
							echo "<tr>";					
							echo "<td >" . $data['worker_name'] . "</td>";
							echo "<td >" . $data['company_name'] . "</td>";
							echo "<td class='text-center'>";
							$class = "btn-primary";

							if($data['signature']){ 
								$class = "btn-default";
								
						?>
								<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#<?php echo $data['id_excavation_subcontractor'] . "SubcontractorModal"; ?>" id="<?php echo $data['id_excavation_subcontractor']; ?>">
									<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
								</button>

								<div id="<?php echo $data['id_excavation_subcontractor'] . "SubcontractorModal"; ?>" class="modal fade" role="dialog">  
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
							<a class='btn <?php echo $class; ?> btn-sm' href='<?php echo base_url('jobs/add_signature_excavation/subcontractor/' . $data['fk_id_job_excavation'] . '/' . $data['id_excavation_subcontractor']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
							</a>
						<?php
							echo "</td>";                     
							echo "</tr>";
						endforeach;
					?>
						</table>

				</div>
			</div>
		</div>
	</div>
<?php } ?>
<!--FIN OCCASIONAL SUBCONTRACTOR -->
				</div>
			</div>
		</div>
	</div>	
</div>