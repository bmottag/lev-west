<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-user fa-fw"></i> USER PROFILE
					</h4>
				</div>
			</div>
		</div>		
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-user"></i> <strong><?php echo $this->session->userdata("name"); ?></strong>
				</div>
				<div class="panel-body">
				
					<?php if($UserInfo[0]["photo"]){ ?>
						<div class="form-group">
							<div class="row" align="center">
								<img src="<?php echo base_url($UserInfo[0]["photo"]); ?>" class="img-rounded" alt="Employee Photo" />
							</div>
						</div>
					<?php } ?>
				
					<strong>Name: </strong><?php echo $this->session->userdata("name"); ?><br>
					<strong>DOB: </strong><?php echo $UserInfo[0]["birthdate"]; ?><br>
					<?php
						$movil = $UserInfo[0]["movil"];
						// Separa en grupos de tres 
						$count = strlen($movil); 
							
						$num_tlf1 = substr($movil, 0, 3); 
						$num_tlf2 = substr($movil, 3, 3); 
						$num_tlf3 = substr($movil, 6, 2); 
						$num_tlf4 = substr($movil, -2); 

						if($count == 10){
							$resultado = "$num_tlf1 $num_tlf2 $num_tlf3 $num_tlf4";  
						}else{
							
							$resultado = chunk_split($movil,3," "); 
						}
					?>
					<strong>Mobile number: </strong><?php echo $resultado; ?><br>
					<strong>Email: </strong><?php echo $UserInfo[0]["email"]; ?><br>
					<strong>Address: </strong><?php echo $UserInfo[0]["address"]; ?><br>
					<strong>Postal code: </strong><?php echo $UserInfo[0]["postal_code"]; ?>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-image"></i> <b>Upload your photo</b>
				</div>
				<div class="panel-body">
								
					<form  name="form" id="form" class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo base_url("employee/do_upload"); ?>">
					
						<div class="form-group">
							<label class="col-sm-4 control-label" for="hddTask">Photo:</label>
							<div class="col-sm-5">
								 <input type="file" name="userfile" />
							</div>
						</div>

						<div class="form-group">
							<div class="row" align="center">
								<div style="width:50%;" align="center">							
									<button type="submit" id="btnSubmit" name="btnSubmit" class="btn btn-info" >
										Submit <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
									</button> 
								</div>
							</div>
						</div>
						
						<?php if($error){ ?>
						<div class="alert alert-danger">
							<?php 
								echo "<strong>Error :</strong>";
								pr($error); 
							?><!--$ERROR MUESTRA LOS ERRORES QUE PUEDAN HABER AL SUBIR LA IMAGEN-->
						</div>
						<?php } ?>
						<div class="alert alert-info">
								<strong>Note :</strong><br>
								Allowed format: gif - jpg - png<br>
								Maximum size: 3000 KB<br>
								Maximum width: 2024 pixels<br>
								Maximum height: 2008 pixels<br>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="panel panel-primary">
	            <div class="panel-heading">
	                <i class="fa fa-edit fa-fw"></i> Signature
	            </div>

                <div class="panel-body">						
					<div class="form-group">
						<div class="alert alert-danger">
							The signature is only used by the owner of the signature and whenever it is used it must be authorized with the user's credentials.
						</div>
						<div class="row" align="center">
							<div style="width:80%;" align="center">
							<?php 								
								$class = "btn-primary";						
								if($UserInfo[0]["user_signature"])
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
													<h4 class="modal-title"><?php echo $this->session->userdata("name") . "'s signature"; ?></h4>      </div>      
												<div class="modal-body text-center"><img src="<?php echo base_url($UserInfo[0]["user_signature"]); ?>" class="img-rounded" alt="Signature" width="304" height="236" />   </div>      
												<div class="modal-footer">        
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
												</div>  
											</div>  
										</div>
									</div>
							<?php
								}
							?>
						
								<a class="btn <?php echo $class; ?>" href="<?php echo base_url("employee/add_signature/" . $UserInfo[0]["id_user"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>