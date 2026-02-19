<script type="text/javascript" src="<?php echo base_url("assets/js/validate/more/post_entry.js"); ?>"></script>

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
							<li><a href="<?php echo base_url("more/add_confined/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">FORM</a>
							</li>
							<li><a href="<?php echo base_url("more/confined_workers/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ENTRANT(S)</a>
							</li>
							<li><a href="<?php echo base_url("more/workers_site/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">WORKERS ON SITE</a>
							</li>
							<li><a href="<?php echo base_url("more/re_testing/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ENVIRONMENTAL CONDITIONS - RE-TESTING</a>
							</li>
							<li class='active'><a href="<?php echo base_url("more/post_entry/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">POST ENTRY INSPECTION</a>
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
		<input type="hidden" id="hddConfined" name="hddConfined" value="<?php echo $information ? $information[0]["id_job_confined"] : ""; ?>" />
		<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $jobInfo[0]["id_job"]; ?>" />

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Post-entry Inspection</strong>
					</div>
					<div class="panel-body">

						<div class="form-group">
							<label class="col-sm-7 control-label" for="certify">
								Are all personnel out of the confined space and accounted for?
							</label>
							<div class="col-sm-2">
								<select name="personnel_out" id="personnel_out" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["personnel_out"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["personnel_out"] == 2) {
														echo "selected";
													}  ?>>No</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="isolation">
								Have isolation devices been removed and pipes been restored to their original positions?
							</label>
							<div class="col-sm-2">
								<select name="isolation" id="isolation" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["isolation"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["isolation"] == 2) {
														echo "selected";
													}  ?>>No</option>
									<option value=3 <?php if ($information[0]["isolation"] == 3) {
														echo "selected";
													}  ?>>N/A</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="lockouts_removed">
								Have all lockouts been removed?
							</label>
							<div class="col-sm-2">
								<select name="lockouts_removed" id="lockouts_removed" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["lockouts_removed"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["lockouts_removed"] == 2) {
														echo "selected";
													}  ?>>No</option>
									<option value=3 <?php if ($information[0]["lockouts_removed"] == 3) {
														echo "selected";
													}  ?>>N/A</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="tags_removed">
								Have all safe entry tags and sings been removed?
							</label>
							<div class="col-sm-2">
								<select name="tags_removed" id="tags_removed" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["tags_removed"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["tags_removed"] == 2) {
														echo "selected";
													}  ?>>No</option>
									<option value=3 <?php if ($information[0]["tags_removed"] == 3) {
														echo "selected";
													}  ?>>N/A</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="equipment_removed">
								Have all equipment and waste been removed from the work area?
							</label>
							<div class="col-sm-2">
								<select name="equipment_removed" id="equipment_removed" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["equipment_removed"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["equipment_removed"] == 2) {
														echo "selected";
													}  ?>>No</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="ppe_cleaned">
								Has all specialized PPE been cleaned, post-inspected and put away?
							</label>
							<div class="col-sm-2">
								<select name="ppe_cleaned" id="ppe_cleaned" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["ppe_cleaned"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["ppe_cleaned"] == 2) {
														echo "selected";
													}  ?>>No</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="rescue_equipment">
								Has all rescue equipment been post -inspected, cleaned and stored (If Applicable)?
							</label>
							<div class="col-sm-2">
								<select name="rescue_equipment" id="rescue_equipment" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["rescue_equipment"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["rescue_equipment"] == 2) {
														echo "selected";
													}  ?>>No</option>
									<option value=3 <?php if ($information[0]["rescue_equipment"] == 3) {
														echo "selected";
													}  ?>>N/A</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="permits_signed">
								Have all permits been signed out and filed properly?
							</label>
							<div class="col-sm-2">
								<select name="permits_signed" id="permits_signed" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["permits_signed"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["permits_signed"] == 2) {
														echo "selected";
													}  ?>>No</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-7 control-label" for="areas_notified">
								Have other applicable areas of the facility been notified that the work in the confined space is complete and operations are ready to be resumed?
							</label>
							<div class="col-sm-2">
								<select name="areas_notified" id="areas_notified" class="form-control" required>
									<option value="">Select...</option>
									<option value=1 <?php if ($information[0]["areas_notified"] == 1) {
														echo "selected";
													}  ?>>Yes</option>
									<option value=2 <?php if ($information[0]["areas_notified"] == 2) {
														echo "selected";
													}  ?>>No</option>
									<option value=3 <?php if ($information[0]["areas_notified"] == 3) {
														echo "selected";
													}  ?>>N/A</option>
								</select>
							</div>
						</div>


					</div>
				</div>
			</div>
		</div>

		<!--INICIO FIRMAS ENCARGADOS -->
		<div class="row">

			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<a name="anclaSignature"></a>
						<strong>Post-entry Check done By: *</strong>
					</div>
					<div class="panel-body">

						<div class="form-group">
							<div class="col-sm-12">
								<select name="post_entry" id="post_entry" class="form-control">
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($workersList); $i++) { ?>
										<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if ($information && $information[0]["fk_id_post_entry_user"] == $workersList[$i]["id_user"]) {
																										echo "selected";
																									}  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<!-- INICIO FIRMA -->
						<?php if ($information[0]["fk_id_post_entry_user"]) { //solo se muestran las firmas cuando hay informacion 
						?>
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:70%;" align="center">
										<?php
										$class = "btn-primary";
										if ($information[0]["post_entry_signature"]) {
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
															<h4 class="modal-title">Post-entry Check – Signature</h4>
														</div>
														<div class="modal-body text-center"><img src="<?php echo base_url($information[0]["post_entry_signature"]); ?>" class="img-rounded" alt="Hauling Supervisor Signature" width="304" height="236" /> </div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
														</div>
													</div>
												</div>
											</div>
										<?php
										}
										?>

										<a class="btn <?php echo $class; ?>" href="<?php echo base_url("more/add_signature_confined/post_entry/" . $jobInfo[0]["id_job"] . "/" . $information[0]["id_job_confined"] . "/" . $information[0]["fk_id_post_entry_user"]); ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Signature </a>

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