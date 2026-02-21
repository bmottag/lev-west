<script type="text/javascript" src="<?php echo base_url("assets/js/validate/incidences/accident.js"); ?>"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(function(){ 
		
	$(".btn-success").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + '/incidences/cargarModalWitness',
                data: {'identificador': oID},
                cache: false,
                success: function (data) {
                    $('#tablaWitness').html(data);
                }
            });
	});
	
	$(".btn-warning").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + '/incidences/cargarModalCarsInvolved',
                data: {'identificador': oID},
                cache: false,
                success: function (data) {
                    $('#tablaWitness').html(data);
                }
            });
	});	


});
</script>


<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-ambulance fa-fw"></i>	INCIDENCES
					</h4>
				</div>
			</div>
		</div>
		<!-- /.col-lg-12 -->				
	</div>
	
	
<?php if($information){ ?>	
<!--INICIO cars involved info -->
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-warning">
				<div class="panel-heading">
					CAR(S) INVOLVED
				</div>
				<div class="panel-body">

					<div class="col-lg-12">													
						<button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#modalWitness" id="<?php echo 'witness-' . $information[0]["id_accident"];//se coloca un ID diferente para que no entre en conflicto con los otros modales ?>">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Car involved
						</button><br>
					</div>
<?php 
	if($carsInvolvedInfo){
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="primary">
					<td><p class="text-center"><strong>Make</strong></p></td>
					<td><p class="text-center"><strong>Model</strong></p></td>
					<td><p class="text-center"><strong>Insurance number</strong></p></td>
					<td><p class="text-center"><strong>Register owner</strong></p></td>
					<td><p class="text-center"><strong>Driver name</strong></p></td>
					<td><p class="text-center"><strong>License number</strong></p></td>
					<td><p class="text-center"><strong>Plate</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
							
				
				<?php
					foreach ($carsInvolvedInfo as $data):
						echo "<tr>";					
						echo "<td >" . $data['make'] . "</td>";
						echo "<td >" . $data['model'] . "</td>";
						echo "<td >" . $data['insurance'] . "</td>";
						echo "<td >" . $data['register_owner'] . "</td>";
						echo "<td >" . $data['driver_name'] . "</td>";
						echo "<td >" . $data['license'] . "</td>";
						echo "<td >" . $data['plate'] . "</td>";
				?>
						<td class='text-center'><small>
							<center>
							<a class='btn btn-danger' href='<?php echo base_url('incidences/deleteRecord/car_involved/' . $data['id_car_involved'] . '/' . $data['fk_id_accident'] . '/add_accident') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
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
	<!-- /.row -->
<!--FIN cars involved info -->


<!--INICIO witness info -->
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-success">
				<div class="panel-heading">
					WITNESS INFO
				</div>
				<div class="panel-body">

					<div class="col-lg-12">													
						<button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modalWitness" id="<?php echo 'witness-' . $information[0]["id_accident"];//se coloca un ID diferente para que no entre en conflicto con los otros modales ?>">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Witness
						</button><br>
					</div>
<?php 
	if($witnessInfo){
?>
			<table class="table table-bordered table-striped table-hover table-condensed">
				<tr class="primary">
					<td><p class="text-center"><strong>Name</strong></p></td>
					<td><p class="text-center"><strong>Phone number</strong></p></td>
					<td><p class="text-center"><strong>Delete</strong></p></td>
				</tr>
							
				
				<?php
					foreach ($witnessInfo as $data):
						echo "<tr>";					
						echo "<td >" . $data['name'] . "</td>";
						echo "<td >" . $data['phone_number'] . "</td>";
				?>
						<td class='text-center'><small>
							<center>
							<a class='btn btn-danger' href='<?php echo base_url('incidences/deleteRecord/witness/' . $data['id_witness'] . '/' . $data['fk_id_accident'] . '/add_accident') ?>' id="btn-delete">
									<span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>  Delete
							</a>
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
	<!-- /.row -->
<!--FIN witness info -->
<?php } ?>	
	

<form  name="form" id="form" class="form-horizontal" method="post"  >
	<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $information?$information[0]["id_accident"]:""; ?>"/>
	<input type="hidden" id="incidencesType" name="incidencesType" value="accident"/>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<a class="btn btn-danger" href=" <?php echo base_url().'incidences/accident'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-ambulance"></i> ACCIDENT REPORT
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

<!-- INICIO FIRMA -->

<?php if($information){ ?>

				<div class="col-lg-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-edit fa-fw"></i> V-Contracting Supervisor
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
						
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:70%;" align="center">
										 
<?php 
								
	$class = "btn-primary";						
if($information[0]["supervisor_signature"]){ 
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
					<h4 class="modal-title">Lev-West Supervisor Signature</h4>      </div>      
				<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["supervisor_signature"]); ?>" class="img-rounded" alt="Hauling Supervisor Signature" width="304" height="236" />   </div>      
				<div class="modal-footer">        
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
				</div>  
			</div>  
		</div>
	</div>

	<?php
	}
	?>

	<a class="btn <?php echo $class; ?>" href="<?php echo base_url("incidences/add_signature/accident/supervisor/" . $information[0]["id_accident"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Lev-West Supervisor Signature </a>

									</div>
								</div>
							</div>
					
						</div>
						<!-- /.panel-body -->
					</div>
				</div>
				
				<div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-edit fa-fw"></i> V-Contracting Safety Coordinator
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

							<div class="form-group">
								<div class="row" align="center">
									<div style="width:70%;" align="center">								 
<?php 
								
$class = "btn-info";						
if($information[0]["coordinator_signature"]){ 
	$class = "btn-default";
?>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myContractorModal" >
	<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
</button>

<div id="myContractorModal" class="modal fade" role="dialog">  
	<div class="modal-dialog">
		<div class="modal-content">      
			<div class="modal-header">        
				<button type="button" class="close" data-dismiss="modal">×</button>        
				<h4 class="modal-title">Safety Coordinator Signature</h4>      </div>      
<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["coordinator_signature"]); ?>" class="img-rounded" alt="Safety Coordinator Signature" width="304" height="236" />   </div>      
			<div class="modal-footer">        
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
			</div>  
		</div>  
	</div>
</div>

<?php
}
?>

<a class="btn <?php echo $class; ?>" href="<?php echo base_url("incidences/add_signature/accident/coordinator/" . $information[0]["id_accident"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Coordinator Signature </a>

							
									</div>
								</div>
							</div>

						</div>
						<!-- /.panel-body -->
					</div>
				</div>
<?php } ?>	

<!-- FIN FIRMA -->
<p class="text-danger text-left">Fields with * are required.</p>
												
						<div class="form-group">
							<label class="col-sm-4 control-label" for="unit">Unit: * </label>
							<div class="col-sm-5">
								<input type="text" id="unit" name="unit" class="form-control" value="<?php echo $information?$information[0]["unit"]:""; ?>" placeholder="Unit" required >
							</div>
						</div>
						

<script>
	$( function() {
		$( "#date" ).datepicker({
			changeMonth: true,
			dateFormat: 'yy-mm-dd'
		});
	});
</script>						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="date">Date accident: *</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="date" name="date" value="<?php echo $information?$information[0]["date_accident"]:""; ?>" placeholder="Date accident" required />
							</div>
						</div>

						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="fromSite">Time: *</label>
							<div class="col-sm-2">
							<?php 
								if($information){
									$timeIn = explode(":",$information[0]["time"]);
									$hourIn = $timeIn[0];
									$minIn = $timeIn[1];
								}
							?>
								<select name="hour" id="hour" class="form-control" required>
									<option value='' >Select...</option>
									<?php
									for ($i = 0; $i < 24; $i++) {
										?>
										<option value='<?php echo $i; ?>' <?php
										if ($information && $i == $hourIn) {
											echo 'selected="selected"';
										}
										?>><?php echo $i; ?></option>
											<?php } ?>									
								</select>
							</div>
							<div class="col-sm-2">
								<select name="min" id="min" class="form-control" required>
									<option value="00" <?php if($information && $minIn == "00") { echo "selected"; }  ?>>00</option>
									<option value="15" <?php if($information && $minIn == "15") { echo "selected"; }  ?>>15</option>
									<option value="30" <?php if($information && $minIn == "30") { echo "selected"; }  ?>>30</option>
									<option value="45" <?php if($information && $minIn == "45") { echo "selected"; }  ?>>45</option>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="location">Location: *</label>
							<div class="col-sm-5">
								<input type="text" id="location" name="location" class="form-control" value="<?php echo $information?$information[0]["location"]:""; ?>" placeholder="Location" required >
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="nearCity">Near City: *</label>
							<div class="col-sm-5">
								<input type="text" id="nearCity" name="nearCity" class="form-control" value="<?php echo $information?$information[0]["near_city"]:""; ?>" placeholder="Near city" required >
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="callManager">Did you call your Driver Manager? *</label>
							<div class="col-sm-5">
								<label class="radio-inline">
									<input type="radio" name="callManager" id="callManager1" value=1 <?php if($information && $information[0]["call_manager"] == 1) { echo "checked"; }  ?>>Yes
								</label>
								<label class="radio-inline">
									<input type="radio" name="callManager" id="callManager2" value=2 <?php if($information && $information[0]["call_manager"] == 2) { echo "checked"; }  ?>>No
								</label>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="pictures">Did you take pictures (Not from injured people)? *</label>
							<div class="col-sm-5">
								<label class="radio-inline">
									<input type="radio" name="pictures" id="pictures1" value=1 <?php if($information && $information[0]["take_pictures"] == 1) { echo "checked"; }  ?>>Yes
								</label>
								<label class="radio-inline">
									<input type="radio" name="pictures" id="pictures2" value=2 <?php if($information && $information[0]["take_pictures"] == 2) { echo "checked"; }  ?>>No
								</label>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="devises">Did you set out all warning devises (on time)? *</label>
							<div class="col-sm-5">
								<label class="radio-inline">
									<input type="radio" name="devises" id="devises1" value=1 <?php if($information && $information[0]["warning_devises"] == 1) { echo "checked"; }  ?>>Yes
								</label>
								<label class="radio-inline">
									<input type="radio" name="devises" id="devises2" value=2 <?php if($information && $information[0]["warning_devises"] == 2) { echo "checked"; }  ?>>No
								</label>
							</div>
						</div>
						
				</div>
			</div>
		</div>
	</div>								
								
								
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-danger">
				<div class="panel-heading">
					<strong>Brief explanation: *</strong>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<div class="col-sm-12">
						<textarea id="explanation" name="explanation" class="form-control" rows="2"><?php echo $information?$information[0]["brief_explanation"]:""; ?></textarea>
						</div>
					</div>				
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-danger">
				<div class="panel-heading">
					<strong>Climate Conditions: *</strong>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<div class="col-sm-12">
						<textarea id="climate" name="climate" class="form-control" rows="2"><?php echo $information?$information[0]["climate_conditions"]:""; ?></textarea>
						</div>
					</div>				
				</div>
			</div>
		</div>
	</div>
		

								
								

					<div class="form-group">
						<div class="row" align="center">
							<div style="width:100%;" align="center">
								<input type="button" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary"/>
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
<!-- /#page-wrapper -->



<!--INICIO Modal WITNESS-->
<div class="modal fade text-center" id="modalWitness" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaWitness">

		</div>
	</div>
</div>                       
<!--FIN Modal WITNESS -->