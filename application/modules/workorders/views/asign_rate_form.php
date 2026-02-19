<script>
$(function(){ 

	$(".btn-default").click(function () {	
			var oID = $(this).attr("id");
			
			//Activa icono guardando
			if(window.confirm('Are you sure you want to load the general configuration data?'))
			{
					$(".btn-default").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'workorders/load_prices_wo',
						data: {'identificador': oID},
						cache: false,
						success: function(data){
												
							if( data.result == "error" )
							{
								alert(data.mensaje);
								$(".btn-default").removeAttr('disabled');							
								return false;
							} 
											
							if( data.result )//true
							{	                                                        
								$(".btn-default").removeAttr('disabled');

								var url = base_url + "workorders/view_workorder/" + data.idWO
								$(location).attr("href", url);
							}
							else
							{
								alert('Error. Reload the web page.');
								$(".btn-default").removeAttr('disabled');
							}	
						},
						error: function(result) {
							alert('Error. Reload the web page.');
							$(".btn-default").removeAttr('disabled');
						}

					});
			}
	});
	
	$(".btn-warning").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'workorders/cargarModalPersonal',
                data: {'idWorkorder': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatos').html(data);
                }
            });
	});	
	
	$(".btn-success").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'workorders/cargarModalMaterials',
                data: {'idWorkorder': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatosMaterial').html(data);
                }
            });
	});	
	
	$(".btn-info").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'workorders/cargarModalEquipment',
                data: {'idWorkorder': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatosEquipment').html(data);
                }
            });
	});	
	
	$(".btn-primary").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'workorders/cargarModalOcasional',
                data: {'idWorkorder': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatosOcasional').html(data);
                }
            });
	});	
	
	$(".btn-purpura").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'workorders/cargarModalHoldBack',
                data: {'idWorkorder': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatosHoldBack').html(data);
                }
            });
	});

});
</script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">									
					<a class="btn btn-default btn-xs" href=" <?php echo base_url().'workorders/search/y'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-money"></i> <strong>WORK ORDERS</strong>
				</div>
				<div class="panel-body">
				
					<ul class="nav nav-pills">
						<li><a href="<?php echo base_url('workorders/add_workorder/' . $information[0]["id_workorder"]) ?>">Edit</a>
						</li>
						<li class='active'><a href="<?php echo base_url('workorders/view_workorder/' . $information[0]["id_workorder"]) ?>">Asign rate</a>
						</li>
						<li><a href="<?php echo base_url('workorders/generaWorkOrderPDF/' . $information[0]["id_workorder"]) ?>" target="_blank">Download invoice</a>
						</li>
					</ul>
					<br>

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

<?php 
	if($information)
	{ 
			switch ($information[0]['state']) {
					case 0:
							$valor = 'On Field';
							$clase = "alert-danger";
							break;
					case 1:
							$valor = 'In Progress';
							$clase = "alert-warning";
							break;
					case 2:
							$valor = 'Revised';
							$clase = "alert-info";
							break;
					case 3:
							$valor = 'Send to the Client';
							$clase = "alert-success";
							break;
					case 4:
							$valor = 'Closed';
							$clase = "alert-danger";
							break;
			}
?>
		<div class="col-lg-12">	
			<div class="alert <?php echo $clase; ?>">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				This work order is <strong><?php echo $valor; ?></strong>
			</div>
		</div>
						
<?php } ?>

					<div class="row">
						<div class="col-lg-12">								
							<div class="alert alert-info">
								<strong>Work Order #: </strong><?php echo $information[0]["id_workorder"]; ?>
								<br><strong>Work Order Date: </strong><?php echo $information[0]["date"]; ?>
								<br><strong>Job Code/Name: </strong><?php echo $information[0]["job_description"]; ?>
								<br><strong>Supervisor: </strong><?php echo $information[0]["name"]; ?>
								<br><strong>Observation: </strong><?php echo $information[0]["observation"]; ?>
								
								<br><br>
								<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
								Update rates from the following button.
								<button type="button" id="<?php echo $information[0]["id_workorder"]; ?>" class='btn btn-default btn-xs' title="Update">
										Update Rates <i class="fa fa-refresh"></i>
								</button>
							</div>
						</div>
					</div>

					<!-- /.row (nested) -->
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->


	
	
<!--INICIO FORMULARIOS -->								
<?php if($information){ ?>
	
	
<!--INICIO PERSONAL -->

	<!-- /.row -->
	<div class="row">
								<div class="col-lg-12">				
									<div class="panel panel-warning">
										<div class="panel-heading">
											PERSONAL
										</div>
										<div class="panel-body">
											<div class="col-lg-12">	
			
					<?php if(!$deshabilitar){ ?>
					<button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#modal" id="<?php echo $information[0]["id_workorder"]; ?>">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Personal
					</button><br>
					<?php } ?>
											</div>
<?php 										
	if(!$workorderPersonal){ 
		echo "<div class='col-lg-12'><a href='#' class='btn btn-danger btn-block'>No data was found.</a></div>";
	}else{
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="warning">
					<td><p class="text-center"><strong>Employee Name</strong></p></td>
					<td><p class="text-center"><strong>Employee Type</strong></p></td>
					<td><p class="text-center"><strong>Hours</strong></p></td>
					<td><p class="text-center"><strong>Task Description</strong></p></td>
					<td><p class="text-center"><strong>Rate</strong></p></td>
					<td><p class="text-center"><strong>Value</strong></p></td>
					<td><p class="text-center"><strong>Rate</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
				<?php
					foreach ($workorderPersonal as $data):
						echo "<tr>";					
						echo "<td ><small>" . $data['name'] . "</small></td>";
						echo "<td ><small>" . $data['employee_type'] . "</small></td>";
						echo "<td class='text-center'><small>" . $data['hours'] . "</small></td>";
						echo "<td ><small>" . $data['description'] . "</small></td>";
												
						$idRecord = $data['id_workorder_personal'];
				?>

						
						<form  name="personal_<?php echo $idRecord ?>" id="personal_<?php echo $idRecord ?>" method="post" action="<?php echo base_url("workorders/save_rate"); ?>">
						
						<td>
				
						<input type="hidden" id="formType" name="formType" value="personal"/>
						<input type="hidden" id="hddId" name="hddId" value="<?php echo $idRecord; ?>"/>
						<input type="hidden" id="hddIdWorkOrder" name="hddIdWorkOrder" value="<?php echo $data['fk_id_workorder']; ?>"/>
						<input type="hidden" id="hours" name="hours" value="<?php echo $data['hours']; ?>"/>
						<input type="hidden" id="description" name="description" value="<?php echo $data['description']; ?>"/>
						<input type="hidden" id="quantity" name="quantity" value=1 >
						
						<input type="text" id="rate" name="rate" class="form-control" placeholder="Rate" value="<?php echo $data['rate']; ?>" required >
		
						</td>
						<td class='text-right'><small><?php echo $data['value']; ?></small></td>
						<td class='text-center'>
							<input type="submit" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary" <?php echo $deshabilitar; ?>/>
						</td>
						</form>
						<td class='text-center'>
							<?php if(!$deshabilitar){ ?>
							<a class='btn btn-danger' href='<?php echo base_url('workorders/deleteRecord/personal/' . $data['id_workorder_personal'] . '/' . $data['fk_id_workorder'] . '/view_workorder') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
							<?php }else{ echo "---";} ?>
						</td>
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
	<!-- /.row -->
								
<!--FIN PERSONAL -->
	
	
<!--INICIO MATERIALS -->

	<!-- /.row -->
	<div class="row">
								<div class="col-lg-12">				
									<div class="panel panel-success">
										<div class="panel-heading">
											MATERIALS
										</div>
										<div class="panel-body">
											<div class="col-lg-12">	
					
					<?php if(!$deshabilitar){ ?>					
					<button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modalMaterials" id="<?php echo 'material-' . $information[0]["id_workorder"];//se coloca un ID diferente para que no entre en conflicto con los otros modales ?>">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Materials
					</button><br>
					<?php } ?>
											</div>
<?php 										
	if(!$workorderMaterials){ 
		echo "<div class='col-lg-12'><a href='#' class='btn btn-danger btn-block'>No data was found.</a></div>";
	}else{
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="success">
					<td><p class="text-center"><strong>Info. Material</strong></p></td>
					<td><p class="text-center"><strong>Quantity</strong></p></td>
					<td><p class="text-center"><strong>Unit</strong></p></td>
					<td><p class="text-center"><strong>Rate</strong></p></td>
					<td><p class="text-center"><strong>Value</strong></p></td>
					<td><p class="text-center"><strong>Save</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
				<?php
					foreach ($workorderMaterials as $data):
						echo "<tr>";					
						echo "<td ><small><strong>Material</strong><br>" . $data['material'] . "</small>";
						echo "<br><small><strong>Description</strong><br>" . $data['description'] . "</small></td>";
						echo "<td class='text-right'><small>" . $data['quantity'] . "</small></td>";
						echo "<td ><small>" . $data['unit'] . "</small></td>";
						
						$idRecord = $data['id_workorder_materials'];
				?>

						
						<form  name="material_<?php echo $idRecord ?>" id="material_<?php echo $idRecord ?>" method="post" action="<?php echo base_url("workorders/save_rate"); ?>">
						
						<td>
				
						<input type="hidden" id="formType" name="formType" value="materials"/>
						<input type="hidden" id="hddId" name="hddId" value="<?php echo $idRecord; ?>"/>
						<input type="hidden" id="hddIdWorkOrder" name="hddIdWorkOrder" value="<?php echo $data['fk_id_workorder']; ?>"/>
						<input type="hidden" id="quantity" name="quantity" value="<?php echo $data['quantity']; ?>"/>
						<input type="hidden" id="hours" name="hours" value=1 />
						<input type="hidden" id="description" name="description" value="<?php echo $data['description']; ?>"/>
						<input type="hidden" id="unit" name="unit" value="<?php echo $data['unit']; ?>"/>

						<input type="text" id="rate" name="rate" class="form-control" placeholder="Rate" value="<?php echo $data['rate']; ?>" required >
		
						</td>
						<td class='text-right'><small><?php echo $data['value']; ?></small></td>
						<td class='text-center'>
							<input type="submit" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary" <?php echo $deshabilitar; ?>/>
						</td>
						</form>
						<td class='text-center'>
							<?php if(!$deshabilitar){ ?>
							<a class='btn btn-danger' href='<?php echo base_url('workorders/deleteRecord/materials/' . $data['id_workorder_materials'] . '/' . $data['fk_id_workorder'] . '/view_workorder') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
							<?php }else{ echo "---";} ?>
						</td>
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
	<!-- /.row -->
								
<!--FIN MATERIALS -->

	
<!--INICIO EQUIPMENT -->

	<!-- /.row -->
	<div class="row">
								<div class="col-lg-12">				
									<div class="panel panel-info">
										<div class="panel-heading">
											EQUIPMENT
										</div>
										<div class="panel-body">
											<div class="col-lg-12">	
												
					<?php if(!$deshabilitar){ ?>
					<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modalEquipment" id="<?php echo 'equipment-' . $information[0]["id_workorder"];//se coloca un ID diferente para que no entre en conflicto con los otros modales ?>">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Equipment
					</button><br>
					<?php } ?>
											</div>
<?php 										
	if(!$workorderEquipment){ 
		echo "<div class='col-lg-12'><a href='#' class='btn btn-danger btn-block'>No data was found.</a></div>";
	}else{
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="info">
					<td><p class="text-center"><strong>Info. Equipment</strong></p></td>
					

					<td><p class="text-center"><strong>Hours</strong></p></td>
					<td><p class="text-center"><strong>Quantity</strong></p></td>

					<td><p class="text-center"><strong>Rate</strong></p></td>
					<td><p class="text-center"><strong>Value</strong></p></td>
					<td><p class="text-center"><strong>Save</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
				<?php
					foreach ($workorderEquipment as $data):
						echo "<tr>";
						echo "<td ><small><strong>Type</strong><br>" . $data['type_2'] . "</small>";
						//si es tipo miscellaneous -> 8, entonces la description es diferente
						if($data['fk_id_type_2'] == 8){
							$equipment = $data['miscellaneous'] . " - " . $data['other'];
						}else{
							//$equipment = $data['unit_number'] . " - " . $data['v_description'];
							$equipment = $data['unit_number'] . " - " . $data['make'] . " - " . $data['model'];
						}
						
						echo "<br><small><strong>Equipment</strong><br>" . $equipment . "</small>";
						echo "<br><small><strong>Operated by</strong><br>" . $data['operatedby'] . "</small>";
						echo "<br><small><strong>Description</strong><br>" . $data['description'] . "</small>";
						
						if($data['company_name']){
							echo "<br><small><strong>Client</strong><br>" . $data['company_name'] . "</small> ";
						}
						
						if($data['foreman_name']){
							echo "<br><small><strong>Foreman's name</strong><br>" . $data['foreman_name'] . "</small> ";
						}
						
						if($data['foreman_email']){
							echo "<br><small><strong>Foreman's email</strong><br>" . $data['foreman_email'] . "</small> ";
						}

						echo "</td>";
						
						echo "<td class='text-right'><small>" . $data['hours'] . "</small></td>";
						$quantity = $data['quantity']==0?1:$data['quantity'];
						echo "<td class='text-right'><small>" . $quantity . "</small></td>";

						
						$idRecord = $data['id_workorder_equipment'];
				?>

						
						<form  name="equipment_<?php echo $idRecord ?>" id="equipment_<?php echo $idRecord ?>" method="post" action="<?php echo base_url("workorders/save_rate"); ?>">
						
						<td>
				
						<input type="hidden" id="formType" name="formType" value="equipment"/>
						<input type="hidden" id="hddId" name="hddId" value="<?php echo $idRecord; ?>"/>
						<input type="hidden" id="hddIdWorkOrder" name="hddIdWorkOrder" value="<?php echo $data['fk_id_workorder']; ?>"/>
						<input type="hidden" id="hours" name="hours" value="<?php echo $data['hours']; ?>"/>
						<input type="hidden" id="quantity" name="quantity" value="<?php echo $quantity; ?>" />
						<input type="hidden" id="description" name="description" value="<?php echo $data['description']; ?>"/>

						<input type="text" id="rate" name="rate" class="form-control" placeholder="Rate" value="<?php echo $data['rate']; ?>" required >
		
						</td>
						<td class='text-right'><small><?php echo $data['value']; ?></small></td>
						<td class='text-center'>
							<input type="submit" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary" <?php echo $deshabilitar; ?>/>
						</td>
						</form>			
						<td class='text-center'>
							<?php if(!$deshabilitar){ ?>
							<a class='btn btn-danger' href='<?php echo base_url('workorders/deleteRecord/equipment/' . $data['id_workorder_equipment'] . '/' . $data['fk_id_workorder'] . '/view_workorder') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
							<?php }else{ echo "---";} ?>
						</td>
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
	<!-- /.row -->
	
<!--FIN EQUIPMENT -->	
	
	
<!--INICIO SUBCONTRACTOR -->

	<!-- /.row -->
	<div class="row">
								<div class="col-lg-12">				
									<div class="panel panel-primary">
										<div class="panel-heading">
											OCCASIONAL
										</div>
										<div class="panel-body">
											<div class="col-lg-12">	
								
					<?php if(!$deshabilitar){ ?>
					<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modalOcasional" id="<?php echo 'ocasional-' . $information[0]["id_workorder"];//se coloca un ID diferente para que no entre en conflicto con los otros modales ?>">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Occasional Subcontractor
					</button><br>
					<?php } ?>
											</div>
<?php 										
	if(!$workorderOcasional){ 
		echo "<div class='col-lg-12'><a href='#' class='btn btn-danger btn-block'>No data was found.</a></div>";
	}else{
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="primary">
					<td><p class="text-center"><strong>Info. Subcontractor</strong></p></td>
					
				
				
					<td><p class="text-center"><strong>Quantity</strong></p></td>
					<td><p class="text-center"><strong>Unit</strong></p></td>
					<td><p class="text-center"><strong>Hours</strong></p></td>
		
		
					<td><p class="text-center"><strong>Rate</strong></p></td>
					<td><p class="text-center"><strong>Value</strong></p></td>
					<td><p class="text-center"><strong>Save</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
				<?php
					foreach ($workorderOcasional as $data):
						echo "<tr>";
						echo "<td ><small><strong>Company</strong><br>" . $data['company_name'] . "</small>";
						echo "<br><small><strong>Equipment</strong><br>" . $data['equipment'] . "</small>";
						echo "<br><small><strong>Contact</strong><br>" . $data['contact'] . "</small>";
						echo "<br><small><strong>Description</strong><br>" . $data['description'] . "</small></td>";
						
						
						
						
						echo "<td class='text-right'><small>" . $data['quantity'] . "</small></td>";
						echo "<td ><small>" . $data['unit'] . "</small></td>";
						$hours = $data['hours']==0?1:$data['hours'];
						echo "<td class='text-right'><small>" . $hours . "</small></td>";
					
					
						
						$idRecord = $data['id_workorder_ocasional'];
				?>

						
						<form  name="ocasional_<?php echo $idRecord ?>" id="ocasional_<?php echo $idRecord ?>" method="post" action="<?php echo base_url("workorders/save_rate"); ?>">
						
						<td>
				
						<input type="hidden" id="formType" name="formType" value="ocasional"/>
						<input type="hidden" id="hddId" name="hddId" value="<?php echo $idRecord; ?>"/>
						<input type="hidden" id="hddIdWorkOrder" name="hddIdWorkOrder" value="<?php echo $data['fk_id_workorder']; ?>"/>
						<input type="hidden" id="quantity" name="quantity" value="<?php echo $data['quantity']; ?>"/>
						<input type="hidden" id="hours" name="hours" value="<?php echo $hours; ?>"/>
						<input type="hidden" id="description" name="description" value="<?php echo $data['description']; ?>"/>
						<input type="hidden" id="unit" name="unit" value="<?php echo $data['unit']; ?>"/>

						<input type="text" id="rate" name="rate" class="form-control" placeholder="Rate" value="<?php echo $data['rate']; ?>" required >
		
						</td>
						<td class='text-right'><small><?php echo $data['value']; ?></small></td>
						<td class='text-center'>
							<input type="submit" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary" <?php echo $deshabilitar; ?>/>
						</td>
						</form>	
						<td class='text-center'>
							<?php if(!$deshabilitar){ ?>
							<a class='btn btn-danger' href='<?php echo base_url('workorders/deleteRecord/ocasional/' . $data['id_workorder_ocasional'] . '/' . $data['fk_id_workorder'] . '/view_workorder') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
							<?php }else{ echo "---";} ?>
						</td>
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
	<!-- /.row -->
	
<!--FIN SUBCONTRACTOR -->


<!--INICIO HOLD BACK -->

	<!-- /.row -->
	<div class="row">
								<div class="col-lg-12">				
									<div class="panel panel-purpura">
										<div class="panel-heading">
											HOLD BACK
										</div>
										<div class="panel-body">
											<div class="col-lg-12">	
								
					<?php if(!$deshabilitar){ ?>
					<button type="button" class="btn btn-purpura btn-block" data-toggle="modal" data-target="#modalHoldBack" id="holdBack-<?php echo $information[0]["id_workorder"]; ?>">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Hold Back
					</button><br>
					<?php } ?>
											</div>
<?php 										
	if(!$workorderHoldBack){ 
		echo "<div class='col-lg-12'><a href='#' class='btn btn-danger btn-block'>No data was found.</a></div>";
	}else{
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="warning">
					<td><p class="text-center"><strong>Description</strong></p></td>
					<td><p class="text-center"><strong>Value</strong></p></td>
					<td><p class="text-center"><strong>Links</strong></p></td>
				</tr>
				<?php
					foreach ($workorderHoldBack as $data):
						echo "<tr>";					
												
						$idRecord = $data['id_workorder_hold_back'];
				?>

						
						<form  name="hold_back_<?php echo $idRecord ?>" id="hold_back_<?php echo $idRecord ?>" method="post" action="<?php echo base_url("workorders/save_rate"); ?>">
						<input type="hidden" id="formType" name="formType" value="hold_back"/>
						<input type="hidden" id="hddId" name="hddId" value="<?php echo $idRecord; ?>"/>
						<input type="hidden" id="hddIdWorkOrder" name="hddIdWorkOrder" value="<?php echo $data['fk_id_workorder']; ?>"/>
						<input type="hidden" id="hours" name="hours" value=1 >
						<input type="hidden" id="quantity" name="quantity" value=1 >
						
						<td>
							<textarea id="description" name="description" class="form-control" rows="3" required><?php echo $data['description']; ?></textarea>
						</td>
						
						<td>
						<input type="text" id="rate" name="rate" class="form-control" placeholder="Value" value="<?php echo $data['value']; ?>" required >
						</td>

						<td class='text-center'>
							<input type="submit" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary" <?php echo $deshabilitar; ?>/>
						</form>

						<br><br>
							<?php if(!$deshabilitar){ ?>
							<a class='btn btn-danger' href='<?php echo base_url('workorders/deleteRecord/hold_back/' . $data['id_workorder_hold_back'] . '/' . $data['fk_id_workorder'] . '/view_workorder') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
							<?php }else{ echo "---";} ?>
						</td>
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
	<!-- /.row -->
								
<!--FIN HOLD BACK -->
	
	
<?php } ?>	
		
</div>
<!-- /#page-wrapper -->

<!--INICIO Modal para PERSONAL -->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>                       
<!--FIN Modal para PERSONAL -->


<!--INICIO Modal para MATERIAL -->
<div class="modal fade text-center" id="modalMaterials" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatosMaterial">

		</div>
	</div>
</div>                       
<!--FIN Modal para MATERIAL -->

<!--INICIO Modal para EQUIPMENT -->
<div class="modal fade text-center" id="modalEquipment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatosEquipment">

		</div>
	</div>
</div>                       
<!--FIN Modal para EQUIPMENT -->

<!--INICIO Modal para OCASIONAL-->
<div class="modal fade text-center" id="modalOcasional" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatosOcasional">

		</div>
	</div>
</div>                       
<!--FIN Modal para OCASIONAL -->

<!--INICIO Modal para OCASIONAL-->
<div class="modal fade text-center" id="modalHoldBack" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatosHoldBack">

		</div>
	</div>
</div>                       
<!--FIN Modal para OCASIONAL -->