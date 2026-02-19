<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> SETTINGS - VEHICLE PHOTO
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
					<a class="btn btn-success" href=" <?php echo base_url().'admin/vehicle/' . $vehicleInfo[0]["type_level_1"] . '/' . $vehicleInfo[0]["inspection_type"]; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-automobile"></i> VEHICLE PHOTO
				</div>
				<div class="panel-body">
					<form  name="form" id="form" class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo base_url("admin/do_upload/photo/" . $vehicleInfo[0]["type_level_1"]); ?>">
					<input type="hidden" id="hddId" name="hddId" value="<?php echo $idVehicle; ?>"/>
					
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>Make</th>
								<th>Model</th>
								<th>Description</th>
								<th>Unit Number</th>
								<th>VIN Number</th>
								<th>Hours/Kilometers</th>
							</tr>
						</thead>
						<tbody>							
						<?php
								echo "<tr>";
								echo "<td class='text-center'>" . $vehicleInfo[0]["make"] . "</td>";
								echo "<td class='text-center'>" . $vehicleInfo[0]["model"] . "</td>";
								echo "<td>" . $vehicleInfo[0]["description"] . "</td>";
								echo "<td class='text-center'>" . $vehicleInfo[0]["unit_number"] . "</td>";
								echo "<td class='text-center'>" . $vehicleInfo[0]["vin_number"] . "</td>";
								echo "<td class='text-right'><strong>" . number_format($vehicleInfo[0]["hours"]) . "</strong></td>";
								echo "</tr>";
						?>
						</tbody>
					</table>

					<?php if($vehicleInfo[0]["photo"]){ ?>
						<div class="form-group">
							<div class="row" align="center">
								<div style="width:70%;" align="center">
									<img src="<?php echo base_url($vehicleInfo[0]["photo"]); ?>" class="img-rounded" alt="Vehicle Photo" />
								</div>
							</div>
						</div>
					<?php } ?>
					
						<div class="col-lg-12">				
							<div class="panel panel-info">
								<div class="panel-heading">
									<b>Upload Equipment Photo</b>
								</div>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-sm-4 control-label" for="hddTask">Photo</label>
										<div class="col-sm-5">
											 <input type="file" name="userfile" />
										</div>
									</div>
						
									<div class="form-group">
										<div class="row" align="center">
											<div style="width:50%;" align="center">
												<input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-primary"/>
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
									
									
									<div class="alert alert-danger">
											<strong>Note :</strong><br>
											Allowed format: gif - jpg - png<br>
											Maximum size: 3000 KB<br>
											Maximum width: 2024 pixels<br>
											Maximum height: 2008 pixels<br>
									</div>

								</div>
							</div>
						</div>

					</form>
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

    <!-- Tables -->
    <script>
    $(document).ready(function() {
        $('#dataTables').DataTable({
            responsive: true,
			 "ordering": false,
			 paging: false,
			"searching": false,
			"info": false
        });
    });
    </script>