<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a class="btn btn-info btn-xs" href="<?php echo base_url().'admin/vehicle/'.$vehicleInfo[0]["type_level_1"] . '/' . $vehicleInfo[0]['inspection_type']; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-wrench"></i> <strong>VEHICLE INSPECTIONS</strong>
				</div>
				<div class="panel-body">

					<div class="alert alert-danger">
						If the record is red is because the inspection have comments.<br>
						For the inspection details, click on the PDF link. <br>
						If you want to check all inspection fails, go to Reports.
					</div>
				<?php
					$tipo = $vehicleInfo[0]['type_level_2'];
					if($info){
				?>				
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th class="text-center">Date of Issue</th>
								<th class="text-center">Employee</th>
								<th class="text-center">Hours/Kilometers</th>
								<th class="text-center">State</th>
								<th class="text-center">Inspection comment</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							$tableInspection = $vehicleInfo[0]['table_inspection'];
							foreach ($info as $lista):
							
									$class = "";
									if($lista['comments'] != ''){
										$class = "danger";
									}
									
									echo "<tr class='" . $class . "'>";
							
									echo "<td class='text-center'><p class='text-" . $class . "'>" . $lista['date_issue'] . "<br>";
									if($lista['fk_id_inspection'] != 0){ 
										if($tableInspection == 'inspection_daily'){
						?>
<a href='<?php echo base_url('report/generaInsectionDailyPDF/x/x/x/x/x/' . $lista['fk_id_inspection'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php
										}elseif($tableInspection == 'inspection_heavy'){
						?>
<a href='<?php echo base_url('report/generaInsectionHeavyPDF/x/x/x/x/' . $lista['fk_id_inspection'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php
										}elseif($tableInspection == 'inspection_generator'){
						?>
<a href='<?php echo base_url('report/generaInsectionSpecialPDF/x/x/x/x/generator/' . $lista['fk_id_inspection'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php
										}elseif($tableInspection == 'inspection_sweeper'){
						?>
<a href='<?php echo base_url('report/generaInsectionSpecialPDF/x/x/x/x/sweeper/' . $lista['fk_id_inspection'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php
										}elseif($tableInspection == 'inspection_hydrovac'){
						?>
<a href='<?php echo base_url('report/generaInsectionSpecialPDF/x/x/x/x/hydrovac/' . $lista['fk_id_inspection'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php	
										}elseif($tableInspection == 'inspection_watertruck'){
								?>
<a href='<?php echo base_url('report/generaInsectionSpecialPDF/x/x/x/x/watertruck/' . $lista['fk_id_inspection'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
								<?php
										}
									}
									echo "</p></td>";
									echo "<td class='text-center'><p class='text-" . $class . "'>" . $lista['name'] . "</p></td>";
									
									echo "<td class='text-right'><p class='text-" . $class . "'>";

									//si es sweeper
									if($tipo == 15){
										echo "Truck engine current hours: " . number_format($lista["current_hours"]);
										echo "<br>Sweeper engine current hours: " . number_format($lista["current_hours_2"]);
									//si es hydrovac
									}elseif($tipo == 16){
										echo "Engine current hours: " . number_format($lista["current_hours"]);
										echo "<br>Hydraulic pump current hours: " . number_format($lista["current_hours_2"]);
										echo "<br>Blower current hours: " . number_format($lista["current_hours_3"]);
									}else{
										echo number_format($lista["current_hours"]);
									}
									
									echo "</p></td>";
														
									echo "<td class='text-center'><p class='text-" . $class . "'>";
									switch ($lista['state']) {
										case 0:
											echo "First Record";
											break;
										case 1:
											echo "Inspection";
//boton para editar la inspeccion
$linkInspection = $vehicleInfo[0]['link_inspection'] . "/". $lista['fk_id_inspection'];
echo "<br><a class='btn btn-success btn-xs' href='" . base_url($linkInspection) . "'>
		Edit <span class='glyphicon glyphicon-edit' aria-hidden='true'>
</a>";

											break;
										case 2:
											echo "Oil Change";
											break;
									}
									
									echo "</p></td>";
									echo "<td ><p class='text-" . $class . "'>" . $lista['comments'] . "</p></td>";
									
									echo "</tr>";
							endforeach;
						?>
						</tbody>
					</table>
				<?php } ?>
				
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


<?php 

$heater_check = $vehicleInfo[0]['heater_check'];
$brakes_check = $vehicleInfo[0]['brakes_check'];
$lights_check = $vehicleInfo[0]['lights_check'];
$steering_wheel_check = $vehicleInfo[0]['steering_wheel_check'];
$suspension_system_check = $vehicleInfo[0]['suspension_system_check'];
$tires_check = $vehicleInfo[0]['tires_check'];
$wipers_check = $vehicleInfo[0]['wipers_check'];
$air_brake_check = $vehicleInfo[0]['air_brake_check'];
$driver_seat_check = $vehicleInfo[0]['driver_seat_check'];
$fuel_system_check = $vehicleInfo[0]['fuel_system_check'];

//preguntar especiales para HYDROVAC para que muestre mensaje si es inseguro sacar el camion
if ($heater_check == 0 || $brakes_check == 0 || $lights_check == 0 || $steering_wheel_check == 0 || $suspension_system_check == 0 || $tires_check == 0 || $wipers_check == 0 || $air_brake_check == 0 || $driver_seat_check == 0 || $fuel_system_check == 0) {

?>
					
		<div class="col-lg-4">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<i class="fa fa-times"></i> <strong>LAST INSPECTIONS FAILS</strong>
				</div>
				<div class="panel-body">
					<?php 
						if ($heater_check == 0) {
							echo "<br><strong>Heater:</strong> Fail"; 
						}
						if ($brakes_check == 0) {
							echo "<br><strong>Brake pedal:</strong> Fail"; 
						}
						if ($lights_check == 0) {
							echo "<br><strong>Lamps and reflectors:</strong> Fail"; 
						}
						if ($steering_wheel_check == 0) {
							echo "<br><strong>Steering wheel:</strong> Fail"; 
						}
						if ($suspension_system_check == 0) {
							echo "<br><strong>Suspension system:</strong> Fail"; 
						}
						if ($tires_check == 0) {
							echo "<br><strong>Tires/Lug Nuts/Pressure:</strong> Fail"; 
						}
						if ($wipers_check == 0) {
							echo "<br><strong>Wipers/Washers:</strong> Fail"; 
						}
						if ($air_brake_check == 0) {
							echo "<br><strong>Air brake system:</strong> Fail"; 
						}
						if ($driver_seat_check == 0) {
							echo "<br><strong>Driver and Passenger door:</strong> Fail"; 
						}
						if ($fuel_system_check == 0) {
							echo "<br><strong>Fuel system:</strong> Fail"; 
						}
					?>
					
				</div>
			</div>
		</div>
<?php } ?>
		
					
	</div>
	
</div>
<!-- /#page-wrapper -->