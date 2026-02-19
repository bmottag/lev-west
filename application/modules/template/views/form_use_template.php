<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-edit fa-fw"></i>	<?php echo $template[0]['template_name'];  ?>
					</h4>
				</div>
			</div>
		</div>
		<!-- /.col-lg-12 -->				
	</div>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a class="btn btn-success" href=" <?php echo base_url().'template/templates'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-life-saver"></i> <?php echo $template[0]['template_name'];  ?>
				</div>
				<div class="panel-body">

<?php
$retornoExito = $this->session->flashdata('retornoExito');
if ($retornoExito) {
    ?>
	<div class="col-lg-12">	
		<div class="alert alert-success ">
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


	<div class="col-lg-12">	
		<div class="alert alert-success ">
			<strong>Location:</strong><br><?php echo $template[0]['location'];  ?><br>
			<strong>Description:</strong><br><?php echo $template[0]['template_description'];  ?><br>
			<span class='fa fa-cloud-download' aria-hidden='true'></span> 
			<strong>Download to: </strong>
<a href='<?php echo base_url('template/generaTemplatePDF/' . $template[0]['id_template'] ); ?>' target="_blank">PDF <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>	
			
		</div>
	</div>

							<form  name="form" id="form" class="form-horizontal" method="post" action="<?php echo base_url("payroll/savePayroll"); ?>" >
								<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $template[0]['id_template']; ?>"/>
								<!-- 2: id_task FILED LEVEL HAZARD Assessment -->
								<input type="hidden" id="hddTask" name="hddTask" value="2"/>


								


								
								<!--INICIO WORKERS -->								


								<div class="col-lg-12">				
									<div class="panel panel-warning">
										<div class="panel-heading">
											<a name="anclaWorker" ></a><strong>VCI WORKERS</strong>
										</div>
										<div class="panel-body">
											<div class="col-lg-12">	
<?php if($templateWorkers){ ?>
												
					<button type="button" class="btn btn-warning btn-lg btn-block" data-toggle="modal" data-target="#modalWorker" id="x">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add VCI Workers
					</button>
<?php }else { ?>
					<a href="<?php echo base_url("template/add_workers_template/" . $template[0]["id_template"]); ?>" class="btn btn-warning btn-lg btn-block"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add VCI Workers</a>
												
<?php } ?>
											
												<br>
											</div>

										
<?php 
	if($templateWorkers){
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr>
					<td><p class="text-center"><strong>Name</strong></p></td>
					<td><p class="text-center"><strong>Signature</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
				<?php
					foreach ($templateWorkers as $data):
						echo "<tr>";					
						echo "<td ><small>" . $data['name'] . "</small></td>";
						echo "<td class='text-center'><small><center>";
$class = "btn-primary";						
if($data['signature']){ 
	$class = "btn-default";
	
?>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#<?php echo $data['id_template_used_worker'] . "wModal"; ?>" id="<?php echo $data['id_template_used_worker']; ?>">
	<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
</button>

<div id="<?php echo $data['id_template_used_worker'] . "wModal"; ?>" class="modal fade" role="dialog">  
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
					
					<a class='btn <?php echo $class; ?>' href='<?php echo base_url('template/add_signature/worker/' . $data['fk_id_template_used'] . '/' . $data['id_template_used_worker']) ?>' id="btn-delete">
							<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
					</a>
					</center>
				<?php
						echo "</small></td>"; 
						echo "<td class='text-center'><small>";
				?>
					<center>
					<a class='btn btn-danger' href='<?php echo base_url('template/deleteTemplateWorker/' . $data['id_template_used_worker'] . '/' . $data['fk_id_template_used']) ?>' id="btn-delete">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
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

								<!--FIN WORKERS -->
								



								
							</form>

					<!-- /.row (nested) -->
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
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
				<form name="formHazard" id="formHazard" role="form" method="post" action="<?php echo base_url("template/save_one_worker") ?>" >
					<input type="hidden" id="hddId" name="hddId" value="<?php echo $template[0]['id_template']; ?>"/>
					
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
