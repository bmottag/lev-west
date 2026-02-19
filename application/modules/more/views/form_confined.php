<script type="text/javascript" src="<?php echo base_url("assets/js/validate/more/confined.js"); ?>"></script>

<script type="text/javascript" src="<?php echo base_url("assets/timepicker/moment.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/timepicker/bootstrap-datetimepicker.min.js"); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url("assets/timepicker/bootstrap-datetimepicker.min.css"); ?>" />

<?php
/**
 * If it is an ADMIN user, show date 
 * @author BMOTTAG
 * @since  11/5/2017
 */
$userRol = $this->session->rol;
if ($userRol == 99) {
?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php } ?>

<div id="page-wrapper">
	<br>


	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-warning">
				<div class="panel-heading">
					<a class="btn btn-warning btn-xs" href=" <?php echo base_url() . 'more/confined/' . $jobInfo[0]['id_job']; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a>
					<i class="fa fa-cube"></i> <strong>CONFINED SPACE ENTRY PERMIT FORM</strong>
				</div>
				<div class="panel-body">

					<?php
					if ($information) {
					?>
						<ul class="nav nav-pills">
							<li class='active'><a href="<?php echo base_url("more/add_confined/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">FORM</a>
							</li>
							<li><a href="<?php echo base_url("more/confined_workers/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ENTRANT(S)</a>
							</li>
							<li><a href="<?php echo base_url("more/workers_site/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">WORKERS ON SITE</a>
							</li>
							<li><a href="<?php echo base_url("more/rescue_plan/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ON-SITE RESCUE PLAN</a>
							</li>
							<li><a href="<?php echo base_url("more/re_testing/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ENVIRONMENTAL CONDITIONS - Re-Testing</a>
							</li>
							<li><a href="<?php echo base_url("more/post_entry/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">POST ENTRY INSPECTION</a>
							</li>
						</ul>
						<br>
					<?php
					}
					?>

					<div class="alert alert-warning">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
						<br><span class='fa fa-clock-o' aria-hidden='true'></span> <strong>Date: </strong>
						<?php
						if ($information) {
							echo $information[0]["date_confined"];

							echo "<br><span class='fa fa-cloud-download' aria-hidden='true'></span> <strong>Download Confined Entry Permit Form: </strong>";
						?>
							<a href='<?php echo base_url('more/generaConfinedPDF/' . $information[0]["id_job_confined"]); ?>' target="_blank">PDF <img src='<?php echo base_url_images('pdf.png'); ?>'></a>
						<?php
						} else {
							echo date("Y-m-d");
						}
						?>
					</div>

				</div>
			</div>
		</div>
	</div>

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
	<p class="text-danger text-left">Fields with * are required.</p>

	<form name="form" id="form" class="form-horizontal" method="post">
		<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $information ? $information[0]["id_job_confined"] : ""; ?>" />
		<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $jobInfo[0]["id_job"]; ?>" />


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<div class="row">
							<div class="col-sm-6">
								<input type="checkbox" id="completed_flha" name="completed_flha" value=1 <?php if ($information && $information[0]["completed_flha"]) {
																												echo "checked";
																											} elseif (!$information) {
																												echo "";
																											} ?>> I Have completed a Field Level Hazard Assessment.
								<br><br>
								<strong>Location: *</strong>
							</div>

							<?php
							/**
							 * If it is an ADMIN user, show date 
							 * @author BMOTTAG
							 * @since  11/5/2017
							 */
							if ($userRol == 99) {
							?>
								<script>
									$(function() {
										$("#date").datepicker({
											changeMonth: true,
											changeYear: true,
											dateFormat: 'yy-mm-dd'
										});
									});
								</script>
								<div class="col-sm-6">
									<br><br>
									<label class="col-sm-4 control-label" for="date">Date of Issue:</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="date" name="date" value="<?php echo $information ? $information[0]["date_confined"] : ""; ?>" placeholder="Date of Issue" />
									</div>

								</div>
							<?php } ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-12">
								<textarea id="location" name="location" class="form-control" rows="2"><?php echo $information ? $information[0]["location"] : ""; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<strong>Purpose of entry: *</strong>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-12">
								<textarea id="purpose" name="purpose" class="form-control" rows="2"><?php echo $information ? $information[0]["purpose"] : ""; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php

		if ($information) {
			$inicio = $information[0]['scheduled_start'];
			$fechaInicio = substr($inicio, 0, 10);
			$horaInicio = substr($inicio, 11, 2);
			$minutosInicio = substr($inicio, 14, 2);

			$fin = $information[0]['scheduled_finish'];
			$fechaFin = substr($fin, 0, 10);
			$horaFin = substr($fin, 11, 2);
			$minutosFin = substr($fin, 14, 2);
		} else {
			$inicio = '';
			$fechaInicio = '';
			$horaInicio = '';
			$minutosInicio = '';

			$fin = '';
			$fechaFin = '';
			$horaFin = '';
			$minutosFin = '';
		}

		?>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<strong>Scheduled: *</strong>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-4">
								<script>
									$(function() {
										$("#start_date").datepicker({
											changeMonth: true,
											changeYear: true,
											dateFormat: 'yy-mm-dd'
										});
									});
								</script>
								<label class="control-label" for="start_date">Start date: *</label>
								<input type="text" class="form-control" id="start_date" name="start_date" value="<?php echo $fechaInicio; ?>" placeholder="Start date" required />
							</div>
							<div class="col-sm-4">
								<label for="type" class="control-label">Start hour: *</label>
								<select name="start_hour" id="start_hour" class="form-control" required>
									<option value=''>Select...</option>
									<?php
									for ($i = 0; $i < 24; $i++) {

										$i = $i < 10 ? "0" . $i : $i;
									?>
										<option value='<?php echo $i; ?>' <?php
																			if ($information && $i == $horaInicio) {
																				echo 'selected="selected"';
																			}
																			?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-sm-4">
								<label for="type" class="control-label">Start minutes: *</label>
								<select name="start_min" id="start_min" class="form-control" required>
									<?php
									for ($xxx = 0; $xxx < 60; $xxx++) {

										$xxx = $xxx < 10 ? "0" . $xxx : $xxx;
									?>
										<option value='<?php echo $xxx; ?>' <?php
																			if ($information && $xxx == $minutosInicio) {
																				echo 'selected="selected"';
																			}
																			?>><?php echo $xxx; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-4">
								<script>
									$(function() {
										$("#finish_date").datepicker({
											changeMonth: true,
											changeYear: true,
											dateFormat: 'yy-mm-dd'
										});
									});
								</script>
								<label class="control-label" for="finish_date">Finish date: *</label>
								<input type="text" class="form-control" id="finish_date" name="finish_date" value="<?php echo $fechaFin; ?>" placeholder="Start date" required />
							</div>

							<div class="col-sm-4">
								<label for="type" class="control-label">Finish hour: *</label>
								<select name="finish_hour" id="finish_hour" class="form-control" required>
									<option value=''>Select...</option>
									<?php
									for ($i = 0; $i < 24; $i++) {

										$i = $i < 10 ? "0" . $i : $i;
									?>
										<option value='<?php echo $i; ?>' <?php
																			if ($information && $i == $horaFin) {
																				echo 'selected="selected"';
																			}
																			?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-4">
								<label for="type" class="control-label">Finish minutes: *</label>
								<select name="finish_min" id="finish_min" class="form-control" required>
									<?php
									for ($xxx = 0; $xxx < 60; $xxx++) {

										$xxx = $xxx < 10 ? "0" . $xxx : $xxx;
									?>
										<option value='<?php echo $xxx; ?>' <?php
																			if ($information && $xxx == $minutosFin) {
																				echo 'selected="selected"';
																			}
																			?>><?php echo $xxx; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="alert alert-danger ">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<b>Oxygen (Acceptable Level)</b> 19.5 % - 22 % <br>
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<b>Carbon Monoxide (Ocupational Exposure Limit)</b> 25 ppm <br>
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<b>Hydrogen Sulphide (Ocupational Exposure Limit)</b> 10 ppm
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<strong>Pre-Entry Authorizartion</strong>
					</div>
					<div class="panel-body">
						<p class="text-info text-left">Check those items below which are applicable to your confined space entry permit</p>

						<div class="col-lg-4">
							<div class="form-group">

								<input type="checkbox" id="oxygen_deficient" name="oxygen_deficient" value=1 <?php if ($information && $information[0]["oxygen_deficient"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Oxygen-Deficient Atmosphere<br>
								<input type="checkbox" id="oxygen_enriched" name="oxygen_enriched" value=1 <?php if ($information && $information[0]["oxygen_enriched"]) {
																												echo "checked";
																											} elseif (!$information) {
																												echo "";
																											} ?>> Oxygen-Enriched Atmosphere<br>
								<input type="checkbox" id="welding" name="welding" value=1 <?php if ($information && $information[0]["welding"]) {
																								echo "checked";
																							} elseif (!$information) {
																								echo "";
																							} ?>> Welding/cutting
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group">

								<input type="checkbox" id="engulfment" name="engulfment" value=1 <?php if ($information && $information[0]["engulfment"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Engulfment<br>
								<input type="checkbox" id="toxic_atmosphere" name="toxic_atmosphere" value=1 <?php if ($information && $information[0]["toxic_atmosphere"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Toxic Atmosphere<br>
								<input type="checkbox" id="flammable_atmosphere" name="flammable_atmosphere" value=1 <?php if ($information && $information[0]["flammable_atmosphere"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Flammable Atmosphere

							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group">

								<input type="checkbox" id="energized_equipment" name="energized_equipment" value=1 <?php if ($information && $information[0]["energized_equipment"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Energized Electric Equipment<br>
								<input type="checkbox" id="entrapment" name="entrapment" value=1 <?php if ($information && $information[0]["entrapment"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Entrapment<br>
								<input type="checkbox" id="hazardous_chemical" name="hazardous_chemical" value=1 <?php if ($information && $information[0]["hazardous_chemical"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Hazardous Chemical

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<strong>SAFETY PRECAUTIONS</strong>
					</div>
					<div class="panel-body">
						<p class="text-info text-left">Check those items below which are applicable to your confined space entry permit</p>

						<div class="col-lg-4">
							<div class="form-group">

								<input type="checkbox" id="breathing_apparatus" name="breathing_apparatus" value=1 <?php if ($information && $information[0]["breathing_apparatus"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Self-Contained Breathing Apparatus<br>
								<input type="checkbox" id="line_respirator" name="line_respirator" value=1 <?php if ($information && $information[0]["line_respirator"]) {
																												echo "checked";
																											} elseif (!$information) {
																												echo "";
																											} ?>> Air-Line Respirator<br>
								<input type="checkbox" id="resistant_clothing" name="resistant_clothing" value=1 <?php if ($information && $information[0]["resistant_clothing"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Flame Resistant Clothing<br>
								<input type="checkbox" id="ventilation" name="ventilation" value=1 <?php if ($information && $information[0]["ventilation"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Ventilation<br>
								<input type="checkbox" id="protective_gloves" name="protective_gloves" value=1 <?php if ($information && $information[0]["protective_gloves"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Protective Gloves

							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group">

								<input type="checkbox" id="linelines" name="linelines" value=1 <?php if ($information && $information[0]["linelines"]) {
																									echo "checked";
																								} elseif (!$information) {
																									echo "";
																								} ?>> Linelines<br>
								<input type="checkbox" id="respirators" name="respirators" value=1 <?php if ($information && $information[0]["respirators"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Respirators<br>
								<input type="checkbox" id="lockout" name="lockout" value=1 <?php if ($information && $information[0]["lockout"]) {
																								echo "checked";
																							} elseif (!$information) {
																								echo "";
																							} ?>> Lockout/Tagout<br>
								<input type="checkbox" id="fire_extinguishers" name="fire_extinguishers" value=1 <?php if ($information && $information[0]["fire_extinguishers"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Fire Extinguishers<br>
								<input type="checkbox" id="barricade" name="barricade" value=1 <?php if ($information && $information[0]["barricade"]) {
																									echo "checked";
																								} elseif (!$information) {
																									echo "";
																								} ?>> Barricade Job Area

							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group">

								<input type="checkbox" id="signs_posted" name="signs_posted" value=1 <?php if ($information && $information[0]["signs_posted"]) {
																											echo "checked";
																										} elseif (!$information) {
																											echo "";
																										} ?>> Signs Posted<br>
								<input type="checkbox" id="clearance_secured" name="clearance_secured" value=1 <?php if ($information && $information[0]["clearance_secured"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Clearance Secured<br>
								<input type="checkbox" id="lighting" name="lighting" value=1 <?php if ($information && $information[0]["lighting"]) {
																									echo "checked";
																								} elseif (!$information) {
																									echo "";
																								} ?>> Lighting<br>
								<input type="checkbox" id="interrupter" name="interrupter" value=1 <?php if ($information && $information[0]["interrupter"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Ground Fault Interrupter

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<strong>ENVIRONMENTAL CONDITIONS - Test to be taken: *</strong>
					</div>
					<div class="panel-body">

						<div class="col-sm-6">
							<label for="type" class="control-label">Oxygen (%): </label>
							<input type="number" step="any" id="oxygen" name="oxygen" class="form-control" value="<?php echo $information ? $information[0]["oxygen"] : ""; ?>" placeholder="Oxygen">
						</div>

						<div class='col-sm-6'>
							<label for="type" class="control-label">Date/Time:</label>
							<div class="form-group">
								<div class='input-group date' id='datetimepicker1'>
									<input type='text' id="oxygen_time" name="oxygen_time" class="form-control" value="<?php echo $information ? $information[0]["oxygen_time"] : ""; ?>" placeholder="YYYY-MM-DD HH:mm:ss" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
						<script type="text/javascript">
							$(function() {
								$('#datetimepicker1').datetimepicker({
									format: 'YYYY-MM-DD HH:mm:ss'
								});
							});
						</script>

						<div class="col-sm-6">
							<label for="type" class="control-label">Lower Explosive Limit (%): </label>
							<input type="number" step="any" id="explosive_limit" name="explosive_limit" class="form-control" value="<?php echo $information ? $information[0]["explosive_limit"] : ""; ?>" placeholder="Lower Explosive Limit">
						</div>

						<div class='col-sm-6'>
							<label for="type" class="control-label">Date/Time:</label>
							<div class="form-group">
								<div class='input-group date' id='datetimepicker2'>
									<input type='text' id="explosive_limit_time" name="explosive_limit_time" class="form-control" value="<?php echo $information ? $information[0]["explosive_limit_time"] : ""; ?>" placeholder="YYYY-MM-DD HH:mm:ss" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
						<script type="text/javascript">
							$(function() {
								$('#datetimepicker2').datetimepicker({
									format: 'YYYY-MM-DD HH:mm:ss'
								});
							});
						</script>

						<div class="col-sm-6">
							<label for="type" class="control-label">Toxic Atmosphere: </label>
							<input type="text" id="toxic_atmosphere_cond" name="toxic_atmosphere_cond" class="form-control" value="<?php echo $information ? $information[0]["toxic_atmosphere_cond"] : ""; ?>" placeholder="Toxic Atmosphere">
						</div>

						<div class="col-sm-6">
							<label for="type" class="control-label">Instruments Used: </label>
							<input type="text" id="instruments_used" name="instruments_used" class="form-control" value="<?php echo $information ? $information[0]["instruments_used"] : ""; ?>" placeholder="Instruments Used">
						</div>


					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<strong>Remarks on the overall condition of the confined space: </strong>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-12">
								<textarea id="remarks" name="remarks" class="form-control" rows="2"><?php echo $information ? $information[0]["remarks"] : ""; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--INICIO FIRMAS ENCARGADOS -->
		<div class="row">

			<div class="col-lg-6">
				<div class="panel panel-info">
					<div class="panel-heading">
						<a name="anclaSignature"></a>
						<strong>ENTRY AUTHORIZATION: *</strong>
						<br>All actions and/or conditions for safety entry have been performed.
						<br>Person in charge of entry:
					</div>
					<div class="panel-body">

						<div class="form-group">
							<div class="col-sm-12">
								<select name="authorization" id="authorization" class="form-control">
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($workersList); $i++) { ?>
										<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if ($information && $information[0]["fk_id_user_authorization"] == $workersList[$i]["id_user"]) {
																										echo "selected";
																									}  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<!-- INICIO FIRMA -->
						<?php if ($information && $information[0]["fk_id_user_authorization"]) { //solo se muestran las firmas cuando hay informacion 
						?>
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:70%;" align="center">
										<?php
										$class = "btn-primary";
										if ($information[0]["authorization_signature"]) {
											$class = "btn-default";
										?>
											<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
												<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
											</button>

											<div id="myModal" class="modal fade" role="dialog">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal">×</button>
															<h4 class="modal-title">ENTRY AUTHORIZATION – Signature</h4>
														</div>
														<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["authorization_signature"]); ?>" class="img-rounded" alt="Hauling Supervisor Signature" width="304" height="236" /> </div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
														</div>
													</div>
												</div>
											</div>
										<?php
										}
										?>

										<a class="btn <?php echo $class; ?>" href="<?php echo base_url("more/add_signature_confined/authorization/" . $jobInfo[0]["id_job"] . "/" . $information[0]["id_job_confined"] . "/" . $information[0]["fk_id_user_authorization"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>ENTRY CANCELLATION: *</strong>
						<br>Entry has been completed and all entrants have left the space.
						<br>Person in charge of entry:
					</div>
					<div class="panel-body">

						<div class="form-group">
							<div class="col-sm-12">
								<select name="cancellation" id="cancellation" class="form-control">
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($workersList); $i++) { ?>
										<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if ($information && $information[0]["fk_id_user_cancellation"] == $workersList[$i]["id_user"]) {
																										echo "selected";
																									}  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<!-- INICIO FIRMA -->
						<?php if ($information && $information[0]["fk_id_user_cancellation"]) { //solo se muestran las firmas cuando hay informacion 
						?>
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:70%;" align="center">
										<?php
										$class = "btn-primary";
										if ($information[0]["cancellation_signature"]) {
											$class = "btn-default";
										?>
											<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myContractorModal">
												<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View Signature
											</button>

											<div id="myContractorModal" class="modal fade" role="dialog">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal">×</button>
															<h4 class="modal-title">ENTRY CANCELLATION - Signature</h4>
														</div>
														<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["cancellation_signature"]); ?>" class="img-rounded" alt="Safety Coordinator Signature" width="304" height="236" /> </div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
														</div>
													</div>
												</div>
											</div>

										<?php
										}
										?>

										<a class="btn <?php echo $class; ?>" href="<?php echo base_url("more/add_signature_confined/cancellation/" . $jobInfo[0]["id_job"] . "/" . $information[0]["id_job_confined"] . "/" . $information[0]["fk_id_user_authorization"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>

		</div>
		<!--FIN FIRMAS ENCARGADOS -->




		<div class="form-group">
			<div class="row" align="center">
				<div style="width:100%;" align="center">
					<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-primary'>
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
<!-- /#page-wrapper -->