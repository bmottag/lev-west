<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<i class="fa fa-life-saver"></i> <strong>INCIDENCES </strong>- INCIDENT/ACCIDENT REPORT
				</div>
				<div class="panel-body">

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-primary">
					<div class="panel-body">
						<strong>Incident/Accident type: </strong><?php echo $information?$information[0]["incident_type"]:""; ?><br>
						<strong>What happened? </strong><br><?php echo $information?$information[0]["what_happened"]:""; ?><br>
						<strong>Date of Incident: </strong><br><?php echo $information?$information[0]["date_incident"]:""; ?><br>
						<strong>Time of Incident: </strong><br><?php echo $information?$information[0]["time"]:""; ?><br>
						<strong>What was the immediate cause? </strong><br><?php echo $information?$information[0]["immediate_cause"]:""; ?><br>
						<strong>What were the contributting factors? </strong><br><?php echo $information?$information[0]["uderlying_causes"]:""; ?><br>
						<strong>Corrective Actions:</strong><br><?php echo $information?$information[0]["corrective_actions"]:""; ?>
					</div>
				</div>
			</div>
		</div>


<!--INICIO WORKERS -->
<?php 
	if($personsInvolved){
?>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">				
			<div class="panel panel-primary">
				<div class="panel-heading">
					<i class="fa fa-user"></i> <strong>Person(s) Involved</strong>
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
						foreach ($personsInvolved as $data):
							echo "<tr>";					
							echo "<td >" . $data['person_name'] . "</td>";
							echo "<td class='text-center'><small><center>";
							$class = "btn-primary";

							if($data['person_signature']){ 
								$class = "btn-default";
					?>
								<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#<?php echo $data['id_incident_person'] . "wModal"; ?>" id="<?php echo $data['id_incident_person']; ?>">
									<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
								</button>

								<div id="<?php echo $data['id_incident_person'] . "wModal"; ?>" class="modal fade" role="dialog">  
									<div class="modal-dialog">
										<div class="modal-content">      
											<div class="modal-header">        
												<button type="button" class="close" data-dismiss="modal">×</button>        
												<h4 class="modal-title">Person Signature</h4>      </div>      
											<div class="modal-body text-center"><img src="<?php echo base_url($data['person_signature']); ?>" class="img-rounded" alt="Meeting conducted by Signature" width="304" height="236" />   </div>      
											<div class="modal-footer">    
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>     
											</div>  
										</div>  
									</div>
								</div>
					<?php
							}
					?>
							<a class='btn <?php echo $class; ?> btn-sm' href='<?php echo base_url('incidences/add_signature/incident/personsInvolved/' . $data['fk_id_incident'] . '/' . $data['id_incident_person']) ?>' id="btn-delete">
									<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Signature
							</a>
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

				</div>
			</div>
		</div>
	</div>	
</div>