<script type="text/javascript" src="<?php echo base_url("assets/js/validate/more/ppe_inspection.js"); ?>"></script>
<?php
/**
 * If it is an ADMIN user, show date 
 * @author BMOTTAG
 * @since  11/5/2017
 */
$userRol = $this->session->rol;
if($userRol==99){
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php } ?>

<div id="page-wrapper">
	<br>

<form  name="form" id="form" class="form-horizontal" method="post"  >
	<input type="hidden" id="hddId" name="hddId" value="<?php echo $information?$information[0]["id_ppe_inspection"]:""; ?>"/>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a class="btn btn-info btn-xs" href=" <?php echo base_url().'more/ppe_inspection'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-cube"></i> <strong>PPE INSPECTION FORM</strong>
				</div>
				<div class="panel-body">
					<div class="col-lg-6">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="aire_acondicionado">Observation: </label>
							<div class="col-sm-8">
								<textarea id="observation" name="observation" class="form-control" rows="2"><?php echo $information?$information[0]["observation"]:""; ?></textarea>
							</div>
						</div>

<?php
/**
 * If it is an ADMIN user, show date 
 * @author BMOTTAG
 * @since  11/5/2017
 */
if($userRol==99){
?>				
<script>
	$( function() {
		$( "#date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
</script>
						<div class="form-group">									
							<label class="col-sm-4 control-label" for="date">Date of Issue:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="date" name="date" value="<?php echo $information?$information[0]["date_ppe_inspection"]:""; ?>" placeholder="Date of Issue" />
							</div>
						</div>
<?php } ?>
						
						<div class="form-group">
							<div class="row" align="center">
								<div style="width:100%;" align="center">							
									<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-primary'>
											Guardar <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
									</button>
								</div>
							</div>
						</div>

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
					</div>
					
					
<!-- INICIO FIRMA -->
<?php if($information){ ?>
				<div class="col-lg-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-edit fa-fw"></i> Inspector
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
						
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
					<h4 class="modal-title"><?php echo $information[0]["name"]; ?> Signature</h4>      </div>      
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

	<a class="btn <?php echo $class; ?>" href="<?php echo base_url("more/add_signature/inspector/" . $information[0]["id_ppe_inspection"] . "/x"); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> <?php echo $information[0]["name"]; ?> Signature </a>

									</div>
								</div>
							</div>
					
						</div>
						<!-- /.panel-body -->
					</div>
				</div>
<?php } ?>	
<!-- FIN FIRMA -->

					
					
					
				</div>
			</div>
		</div>
				
	</div>
	
</form>
						
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

<!--INICIO WORKERS -->								
<?php if($information){ ?>
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-info">
				<div class="panel-heading">
					<a name="anclaWorker" ></a><strong>Lev-West WORKERS</strong>
				</div>
				<div class="panel-body">
					<div class="col-lg-12">	
<?php if($ppeInspectionWorkers){ ?>
												
					<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modalWorker" id="x">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Lev-West Workers
					</button>
<?php }else { ?>
					<a href="<?php echo base_url("more/add_workers_ppe_inspection/" . $information[0]["id_ppe_inspection"]); ?>" class="btn btn-info btn-block"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Lev-West Workers</a>
												
<?php } ?>
											
						<br>
					</div>
										
<?php 
	if($ppeInspectionWorkers){
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr>
					<td><p class="text-center"><strong>Name</strong></p></td>
					<td><p class="text-center"><strong>Signature</strong></p></td>
					<td><p class="text-center"><strong>Steel toe boots</strong></p></td>
					<td><p class="text-center"><strong>Hard hat</strong></p></td>
					<td><p class="text-center"><strong>Reflective vest</strong></p></td>
					<td><p class="text-center"><strong>Safety glasses</strong></p></td>
					<td><p class="text-center"><strong>Gloves</strong></p></td>
					<td><p class="text-center"><strong>Save</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
				<?php
					foreach ($ppeInspectionWorkers as $data):
						echo "<tr>";					
						echo "<td ><small>" . $data['name'] . "</small></td>";
						echo "<td class='text-center'><small><center>";
$class = "btn-primary btn-xs";						
if($data['signature']){ 
	$class = "btn-default btn-xs";
	
?>
<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#<?php echo $data['id_ppe_inspection_worker'] . "wModal"; ?>" id="<?php echo $data['id_ppe_inspection_worker']; ?>">
	<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
</button>

<div id="<?php echo $data['id_ppe_inspection_worker'] . "wModal"; ?>" class="modal fade" role="dialog">  
	<div class="modal-dialog">
		<div class="modal-content">      
			<div class="modal-header">        
				<button type="button" class="close" data-dismiss="modal">×</button>        
				<h4 class="modal-title">Worker Signature</h4>      </div>      
			<div class="modal-body text-center"><img src="<?php echo base_url($data['signature']); ?>" class="img-rounded" alt="Management/Safety Advisor Signature" width="304" height="236" />   </div>      
			<div class="modal-footer">    
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
			</div>  
		</div>  
	</div>
</div>
<?php
}
				?>
					
					<a class='btn <?php echo $class; ?>' href='<?php echo base_url('more/add_signature/worker/' .  $data['fk_id_ppe_inspection'] . '/' . $data['id_ppe_inspection_worker']) ?>' id="btn-delete">
							<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
					</a>
					</center>
					
					</small></td>
					
					<?php $idRecord = $data['id_ppe_inspection_worker'] ?>
					
					<form  name="datos_<?php echo $idRecord; ?>" id="datos_<?php echo $idRecord; ?>" method="post" action="<?php echo base_url("more/updateInspection"); ?>">
					<input type="hidden" id="hddIdPPEInspectionWorker" name="hddIdPPEInspectionWorker" value="<?php echo $idRecord; ?>"/>
					<input type="hidden" id="hddIdPPEInspection" name="hddIdPPEInspection" value="<?php echo $data['fk_id_ppe_inspection']; ?>"/>
					
					<td>
					<select name="safety_boots" id="safety_boots" class="form-control" required>
						<option value="">Select...</option>
						<option value=1 <?php if($data['safety_boots'] == 1) { echo "selected"; }  ?>>Good</option>
						<option value=2 <?php if($data['safety_boots'] == 2) { echo "selected"; }  ?>>Bad</option>
					</select>
					</td>
					
					<td>
					<select name="hart_hat" id="hart_hat" class="form-control" required>
						<option value="">Select...</option>
						<option value=1 <?php if($data['hart_hat'] == 1) { echo "selected"; }  ?>>Good</option>
						<option value=2 <?php if($data['hart_hat'] == 2) { echo "selected"; }  ?>>Bad</option>
					</select>
					</td>
					
					<td>
					<select name="reflective_vest" id="reflective_vest" class="form-control" required>
						<option value="">Select...</option>
						<option value=1 <?php if($data['reflective_vest'] == 1) { echo "selected"; }  ?>>Good</option>
						<option value=2 <?php if($data['reflective_vest'] == 2) { echo "selected"; }  ?>>Bad</option>
					</select>
					</td>
					
					<td>
					<select name="safety_glasses" id="safety_glasses" class="form-control" required>
						<option value="">Select...</option>
						<option value=1 <?php if($data['safety_glasses'] == 1) { echo "selected"; }  ?>>Good</option>
						<option value=2 <?php if($data['safety_glasses'] == 2) { echo "selected"; }  ?>>Bad</option>
					</select>
					</td>
					
					<td>
					<select name="gloves" id="gloves" class="form-control" required>
						<option value="">Select...</option>
						<option value=1 <?php if($data['gloves'] == 1) { echo "selected"; }  ?>>Good</option>
						<option value=2 <?php if($data['gloves'] == 2) { echo "selected"; }  ?>>Bad</option>
					</select>
					</td>
					
					<td class='text-center'>
						<input type="submit" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary"/>
					</td>
					</form>
					
					<td class='text-center'><small>
					<center>				
						<button type="button" class="btn btn-danger" id="<?php echo $data['fk_id_ppe_inspection'] . '-' . $data['id_ppe_inspection_worker']; ?>" >
							Delete
						</button>
					</center>
				
					</small></td>
					</tr>
				<?php
					endforeach;
				?>
			</table>
	<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<!--FIN WORKERS -->

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
				<form name="formAddWorker" id="formAddWorker" role="form" method="post" action="<?php echo base_url("more/add_one_worker") ?>" >
					<input type="hidden" id="hddIdPPEInspection" name="hddIdPPEInspection" value="<?php echo $information[0]["id_ppe_inspection"]; ?>"/>
					
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