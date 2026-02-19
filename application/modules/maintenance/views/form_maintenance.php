<script type="text/javascript" src="<?php echo base_url("assets/js/validate/maintenance/maintenance.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/validate/maintenance/ajaxStock.js"); ?>"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<a class="btn btn-purpura btn-xs" href="<?php echo base_url().'maintenance/entrance/'. $vehicleInfo[0]["id_vehicle"]; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-wrench"></i> <strong>FORM MAINTENANCE</strong>
				</div>
				<div class="panel-body">
				
<form  name="form" id="form" class="form-horizontal" method="post"  >
	<input type="hidden" id="hddIdVehicle" name="hddIdVehicle" value="<?php echo $vehicleInfo[0]['id_vehicle']; ?>"/>
	<input type="hidden" id="hddIdMaintenance" name="hddIdMaintenance" value="<?php echo $information?$information[0]["id_maintenance"]:""; ?>"/>

						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">Maintenance type </label>
								<select name="id_maintenance_type" id="id_maintenance_type" class="form-control" >
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($infoTypeMaintenance); $i++) { ?>
										<option value="<?php echo $infoTypeMaintenance[$i]["id_maintenance_type"]; ?>" <?php if($information && $information[0]["fk_id_maintenance_type"] == $infoTypeMaintenance[$i]["id_maintenance_type"]) { echo "selected"; }  ?>><?php echo $infoTypeMaintenance[$i]["maintenance_type"]; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-5">
								<label for="from">Amount </label>
								<input type="text" id="amount" name="amount" class="form-control" value="<?php echo $information?$information[0]["amount"]:""; ?>" placeholder="Amount" >
							</div>							
						</div>
						
<?php
	//si hay stock muestro campos
	if($infoStock){
?>	
					
						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">Stock </label>
								<select name="id_stock" id="id_stock" class="form-control" >
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($infoStock); $i++) { ?>
										<option value="<?php echo $infoStock[$i]["id_stock"]; ?>" <?php if($information && $information[0]["fk_id_stock"] == $infoStock[$i]["id_stock"]) { echo "selected"; }  ?>><?php echo $infoStock[$i]["stock_description"] . " - $" . $infoStock[$i]["stock_price"] ; ?></option>
									<?php } ?>
								</select>
							</div>
							
<?php 
	$mostrar = "none";
	if($information && !IS_NULL($information[0]["fk_id_stock"]) && $information[0]["fk_id_stock"] > 0){
		$mostrar = "inline";
	}
?>

<?php	
	if($information){
?>
<input type="hidden" id="hddOldIdStock" name="hddOldIdStock" value="<?php echo $information?$information[0]["fk_id_stock"]:""; ?>"/>
<input type="hidden" id="hddOldMaintenanceQuantity" name="hddOldMaintenanceQuantity" value="<?php echo $information?$information[0]["stock_quantity"]:""; ?>"/>
<?php		
	}else{
?>
<input type="hidden" id="hddOldIdStock" name="hddOldIdStock" value=0 />
<input type="hidden" id="hddOldMaintenanceQuantity" name="hddOldMaintenanceQuantity" value=0 />	
<?php	
	}
?>

							<div class="col-sm-5" id="div_stockQuantity" style="display:<?php echo $mostrar; ?>">
								<label for="from">Sock quantity </label>
								<select name="stockQuantity" id="stockQuantity" class="form-control" required>
									<?php
									if($information){
										echo "<option value=''>Select...</option>";
										$quantity = $information[0]['quantity'];
										for ($i = 1; $i <= $quantity; $i++) {
											echo "<option value='" . $i . "' ";
											if($information[0]["stock_quantity"] == $i) { echo "selected"; } 
											echo ">" . $i . "</option>";
										}
									}
									?>
								</select>
							</div>
							
						</div>
						
<?php 
	}
?>
												
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-1">
								<label for="from">Description </label>
								<input type="text" id="description" name="description" class="form-control" value="<?php echo $information?$information[0]["maintenance_description"]:""; ?>" placeholder="Description">
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">Done by </label>
								<input type="text" id="done_by" name="done_by" class="form-control" value="<?php echo $information?$information[0]["done_by"]:""; ?>" placeholder="Done by">
							</div>
							
							<div class="col-sm-5">
								<label for="from">Revised by </label>
								<select name="revised_by" id="revised_by" class="form-control">
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($workersList); $i++) { ?>
										<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if($information && $information[0]["fk_revised_by_user"] == $workersList[$i]["id_user"]) { echo "selected"; }  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
									<?php } ?>
								</select>
							</div>
						</div>

<script>
	$( function() {
		$( "#next_date_maintenance" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
</script>
						
						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">Next Hours/Kilometers maintenance </label>
								<input type="text" id="next_hours_maintenance" name="next_hours_maintenance" class="form-control" value="<?php if($information){echo $information[0]["next_hours_maintenance"]?$information[0]["next_hours_maintenance"]:"";} ?>" placeholder="Insert Next Hours/Kilometers">
							</div>
							
							<div class="col-sm-5">
								<label for="from">Next date maintenance </label>
								<input type="text" id="next_date_maintenance" name="next_date_maintenance" class="form-control" value="<?php echo $information?$information[0]["next_date_maintenance"]:""; ?>" placeholder="Insert next due date">
							</div>
						</div>

						<div class="form-group">
							<div class="row" align="center">
								<div style="width:100%;" align="center">							
									<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-purpura'>
										Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
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

					</form>

				</div>
			</div>
		</div>
	
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-automobile"></i> <strong>VEHICLE INFORMATION</strong>
				</div>
				<div class="panel-body">
				
					<?php if($vehicleInfo[0]["photo"]){ ?>
						<div class="form-group">
							<div class="row" align="center">
								<img src="<?php echo base_url($vehicleInfo[0]["photo"]); ?>" class="img-rounded" alt="Vehicle Photo" />
							</div>
						</div>
					<?php } ?>
				
					<strong>Make: </strong><?php echo $vehicleInfo[0]['make']; ?><br>
					<strong>Model: </strong><?php echo $vehicleInfo[0]['model']; ?><br>
					<strong>Description: </strong><?php echo $vehicleInfo[0]['description']; ?><br>
					<strong>Unit Number: </strong><?php echo $vehicleInfo[0]['unit_number']; ?><br>
					<strong>VIN Number: </strong><?php echo $vehicleInfo[0]['vin_number']; ?><br>
					<strong>Type: </strong><br>
					<?php
						switch ($vehicleInfo[0]['type_level_1']) {
							case 1:
								$type = 'Fleet';
								break;
							case 2:
								$type = 'Rental';
								break;
							case 99:
								$type = 'Other';
								break;
						}
						echo $type . " - " . $vehicleInfo[0]['type_2'];
					?><br>
					
					<?php
					$tipo = $vehicleInfo[0]['type_level_2'];
					
					echo "<p class='text-danger'>";
					//si es sweeper
					if($tipo == 15){
						echo "<strong>Truck engine current hours:</strong><br>";
						echo "Current: " . number_format($vehicleInfo[0]["hours"]);
						echo "<br>Next oil change: " . number_format($vehicleInfo[0]["oil_change"]);
						
						echo "<br><strong>Sweeper engine current hours:</strong><br>";
						echo "Current: " . number_format($vehicleInfo[0]["hours_2"]);
						echo "<br>Next oil change: " . number_format($vehicleInfo[0]["oil_change_2"]);
					//si es hydrovac
					}elseif($tipo == 16){
						echo "<strong>Engine hours:</strong><br>";
						echo "Current: " . number_format($vehicleInfo[0]["hours"]);
						echo "<br>Next oil change: " . number_format($vehicleInfo[0]["oil_change"]);

						echo "<br><strong>Hydraulic pump hours:</strong><br>";
						echo "Current: " . number_format($vehicleInfo[0]["hours_2"]);
						echo "<br>Next oil change: " . number_format($vehicleInfo[0]["oil_change_2"]);
						
						echo "<br><strong>Blower hours:</strong><br>";
						echo "Current: " . number_format($vehicleInfo[0]["hours_3"]);
						echo "<br>Next oil change: " . number_format($vehicleInfo[0]["oil_change_3"]);
					}else{
						echo "<strong>Current Hours/Kilometers: </strong><br>";
						echo "Current: " . number_format($vehicleInfo[0]["hours"]);
						echo "<br>Next oil change: " . number_format($vehicleInfo[0]["oil_change"]);
					}
					echo "</p>";
					
					?>
					
					
				</div>
			</div>
		</div>
					
	</div>	
</div>
<!-- /#page-wrapper -->