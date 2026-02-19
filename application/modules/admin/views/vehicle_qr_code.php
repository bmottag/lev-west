<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> SETTINGS - VEHICLE QR CODE
					</h4>
				</div>
			</div>
		</div>
		<!-- /.col-lg-12 -->				
	</div>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a class="btn btn-success btn-xs" href=" <?php echo base_url().'admin/vehicle/'. $vehicleInfo[0]["type_level_1"] . '/' . $vehicleInfo[0]["inspection_type"]; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-automobile"></i> VEHICLE QR CODE
				</div>
				<div class="panel-body">
						
					<?php if($vehicleInfo[0]["qr_code"]){ ?>
						<div class="form-group">
							<div class="row" align="center">
								<img src="<?php echo base_url($vehicleInfo[0]["qr_code"]); ?>" class="img-rounded" alt="QR CODE" />
							</div>
						</div>
					<?php } ?>
					
					<div class="alert alert-danger">
						
<a href="<?php echo base_url("login/index/" . $vehicleInfo[0]["encryption"]) ?>" class="btn btn-danger btn-block">
<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Inspection URL 
</a>
						
					</div>
					
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-8 -->
		
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
						echo "<strong>Truck Engine Hours:</strong>" . number_format($vehicleInfo[0]["hours"]);
						echo "<br><strong>Sweeper Engine Hours:</strong>" . number_format($vehicleInfo[0]["hours_2"]);
					//si es hydrovac
					}elseif($tipo == 16){
						echo "<strong>Engine Hours:</strong>" . number_format($vehicleInfo[0]["hours"]);
						echo "<br><strong>Hydraulic Pump Hours:</strong>" . number_format($vehicleInfo[0]["hours_2"]);								
						echo "<br><strong>Blower Hours:</strong>" . number_format($vehicleInfo[0]["hours_3"]);
					}else{
						echo "<strong>Hours/Kilometers: </strong>" . number_format($vehicleInfo[0]["hours"]);
					}
					echo "</p>";
					
					?>					
				</div>
			</div>
		</div>		
	</div>
</div>