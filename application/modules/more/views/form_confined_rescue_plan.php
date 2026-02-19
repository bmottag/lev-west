<script type="text/javascript" src="<?php echo base_url("assets/js/validate/more/confined_rescue_plan.js"); ?>"></script>

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
							<li><a href="<?php echo base_url("more/add_confined/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">FORM</a>
							</li>
							<li><a href="<?php echo base_url("more/confined_workers/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ENTRANT(S)</a>
							</li>
							<li><a href="<?php echo base_url("more/workers_site/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">WORKERS ON SITE</a>
							</li>
							<li class='active'><a href="<?php echo base_url("more/rescue_plan/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ON-SITE RESCUE PLAN</a>
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
		<input type="hidden" id="hddConfined" name="hddConfined" value="<?php echo $information ? $information[0]["id_job_confined"] : ""; ?>" />
		<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $jobInfo[0]["id_job"]; ?>" />

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Methods of Communication:</strong>
					</div>
					<div class="panel-body">
						<div class="col-lg-6">
							<p class="text-info text-left">Attendant to Rescue Personnel:</p>
							<div class="col-lg-6">
								<div class="form-group">
									<input type="checkbox" id="rescue_phone" name="rescue_phone" value=1 <?php if ($information && $information[0]["rescue_phone"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Phone<br>
									<input type="checkbox" id="rescue_radio" name="rescue_radio" value=1 <?php if ($information && $information[0]["rescue_radio"]) {
																											echo "checked";
																										} elseif (!$information) {
																											echo "";
																										} ?>> Radio
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<input type="checkbox" id="rescue_audible" name="rescue_audible" value=1 <?php if ($information && $information[0]["rescue_audible"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Audible Signal<br>
									<input type="checkbox" id="rescue_intercom" name="rescue_intercom" value=1 <?php if ($information && $information[0]["rescue_intercom"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Intercom
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<p class="text-info text-left">Attendant to workers:</p>
							<div class="col-lg-6">
								<div class="form-group">
									<input type="checkbox" id="rescue_w_phone" name="rescue_w_phone" value=1 <?php if ($information && $information[0]["rescue_w_phone"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Phone<br>
									<input type="checkbox" id="rescue_w_intercom" name="rescue_w_intercom" value=1 <?php if ($information && $information[0]["rescue_w_intercom"]) {
																											echo "checked";
																										} elseif (!$information) {
																											echo "";
																										} ?>> Intercom<br>
									<input type="checkbox" id="rescue_w_visual" name="rescue_w_visual" value=1 <?php if ($information && $information[0]["rescue_w_visual"]) {
																											echo "checked";
																										} elseif (!$information) {
																											echo "";
																										} ?>> Visual Hand Signal
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<input type="checkbox" id="rescue_w_radio" name="rescue_w_radio" value=1 <?php if ($information && $information[0]["rescue_w_radio"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Radio<br>
									<input type="checkbox" id="rescue_w_audible" name="rescue_w_audible" value=1 <?php if ($information && $information[0]["rescue_w_audible"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Audible Signal<br>
									<input type="checkbox" id="rescue_w_rope" name="rescue_w_rope" value=1 <?php if ($information && $information[0]["rescue_w_rope"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Rope Signal
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Methods of Rescue:</strong>
					</div>
					<div class="panel-body">
						<div class="col-lg-12">
							<div class="form-group">
								<input type="checkbox" id="rescue_external" name="rescue_external" value=1 <?php if ($information && $information[0]["rescue_external"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> External (Retrieval)
							</div>
						</div>

						<div class="col-lg-5">
							<div class="form-group">
								<input type="checkbox" id="rescue_internal" name="rescue_internal" value=1 <?php if ($information && $information[0]["rescue_internal"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Internal:
								<input type="text" id="rescue_internal_value" name="rescue_internal_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_internal_value"] : ""; ?>" placeholder="Internal">
							</div>
						</div>
						<div class="col-lg-2"></div>
						<div class="col-lg-5">
							<div class="form-group">
								<input type="checkbox" id="rescue_congested" name="rescue_congested" value=1 <?php if ($information && $information[0]["rescue_congested"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Congested:
								<input type="text" id="rescue_congested_value" name="rescue_congested_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_congested_value"] : ""; ?>" placeholder="Congested">
							</div>
						</div>

						<div class="col-lg-5">
							<div class="form-group">
								<input type="checkbox" id="rescue_hauling" name="rescue_hauling" value=1 <?php if ($information && $information[0]["rescue_hauling"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Hauling System Required:
								<input type="text" id="rescue_hauling_value" name="rescue_hauling_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_hauling_value"] : ""; ?>" placeholder="Hauling System Required">
							</div>
						</div>
						<div class="col-lg-2"></div>
						<div class="col-lg-5">
							<div class="form-group">
								<input type="checkbox" id="rescue_patient" name="rescue_patient" value=1 <?php if ($information && $information[0]["rescue_patient"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Patient lowering system required/lowering area:
								<input type="text" id="rescue_patient_value" name="rescue_patient_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_patient_value"] : ""; ?>" placeholder="Patient lowering system required/lowering area">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<input type="checkbox" id="rescue_anchor" name="rescue_anchor" value=1 <?php if ($information && $information[0]["rescue_anchor"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Anchor overhead:
								<input type="text" id="rescue_anchor_value" name="rescue_anchor_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_anchor_value"] : ""; ?>" placeholder="Anchor overhead">
							</div>
						</div>
						<div class="col-lg-8"></div>
						
						<div class="col-lg-12">
							<p class="text-info text-left">Anchorage:</p>
						</div>
						<div class="col-lg-6">
							<div class="col-lg-6">
								<div class="form-group">
									<input type="checkbox" id="rescue_beam" name="rescue_beam" value=1 <?php if ($information && $information[0]["rescue_beam"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Beam<br>
									<input type="checkbox" id="rescue_strut" name="rescue_strut" value=1 <?php if ($information && $information[0]["rescue_strut"]) {
																											echo "checked";
																										} elseif (!$information) {
																											echo "";
																										} ?>> Support Strut
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<input type="checkbox" id="rescue_stairwell" name="rescue_stairwell" value=1 <?php if ($information && $information[0]["rescue_stairwell"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Stairwell<br>
									<input type="checkbox" id="rescue_column" name="rescue_column" value=1 <?php if ($information && $information[0]["rescue_column"]) {
																															echo "checked";
																														} elseif (!$information) {
																															echo "";
																														} ?>> Support Column
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
							<input type="text" id="rescue_other" name="rescue_other" class="form-control" value="<?php echo $information ? $information[0]["rescue_other"] : ""; ?>" placeholder="Other">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Rescue Equipment Requirements:</strong>
						<small>
						(check where applicable below and indicate quantity needed):
						</small>
					</div>
					<div class="panel-body">
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_hauling" name="rescue_e_hauling" value=1 <?php if ($information && $information[0]["rescue_e_hauling"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Hauling Systems:
								<input type="text" id="rescue_e_hauling_value" name="rescue_e_hauling_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_hauling_value"] : ""; ?>" placeholder="Hauling Systems">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_carabiners" name="rescue_e_carabiners" value=1 <?php if ($information && $information[0]["rescue_e_carabiners"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Carabiners:
								<input type="text" id="rescue_e_carabiners_value" name="rescue_e_carabiners_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_carabiners_value"] : ""; ?>" placeholder="Carabiners">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_pulleys" name="rescue_e_pulleys" value=1 <?php if ($information && $information[0]["rescue_e_pulleys"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Pulleys:
								<input type="text" id="rescue_e_pulleys_value" name="rescue_e_pulleys_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_pulleys_value"] : ""; ?>" placeholder="Pulleys">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_absorbers" name="rescue_e_absorbers" value=1 <?php if ($information && $information[0]["rescue_e_absorbers"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Shock absorbers/lanyards:
								<input type="text" id="rescue_e_absorbers_value" name="rescue_e_absorbers_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_absorbers_value"] : ""; ?>" placeholder="Shock absorbers/lanyards">
							</div>
						</div>

						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_straps" name="rescue_e_straps" value=1 <?php if ($information && $information[0]["rescue_e_straps"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Anchor Straps:
								<input type="text" id="rescue_e_straps_value" name="rescue_e_straps_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_straps_value"] : ""; ?>" placeholder="Anchor Straps">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_webbing" name="rescue_e_webbing" value=1 <?php if ($information && $information[0]["rescue_e_webbing"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Webbing:
								<input type="text" id="rescue_e_webbing_value" name="rescue_e_webbing_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_webbing_value"] : ""; ?>" placeholder="Webbing">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_ascenders" name="rescue_e_ascenders" value=1 <?php if ($information && $information[0]["rescue_e_ascenders"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Ascenders:
								<input type="text" id="rescue_e_ascenders_value" name="rescue_e_ascenders_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_ascenders_value"] : ""; ?>" placeholder="Ascenders">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_harnesses" name="rescue_e_harnesses" value=1 <?php if ($information && $information[0]["rescue_e_harnesses"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Body Harnesses:
								<input type="text" id="rescue_e_harnesses_value" name="rescue_e_harnesses_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_harnesses_value"] : ""; ?>" placeholder="Body Harnesses">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_rigging" name="rescue_e_rigging" value=1 <?php if ($information && $information[0]["rescue_e_rigging"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Rigging Plates:
								<input type="text" id="rescue_e_rigging_value" name="rescue_e_rigging_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_rigging_value"] : ""; ?>" placeholder="Rigging Plates">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_lines" name="rescue_e_lines" value=1 <?php if ($information && $information[0]["rescue_e_lines"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Safety Lines:
								<input type="text" id="rescue_e_lines_value" name="rescue_e_lines_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_lines_value"] : ""; ?>" placeholder="Safety Lines">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_m_lines" name="rescue_e_m_lines" value=1 <?php if ($information && $information[0]["rescue_e_m_lines"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Main Lines:
								<input type="text" id="rescue_e_m_lines_value" name="rescue_e_m_lines_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_m_lines_value"] : ""; ?>" placeholder="Main Lines">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_wrist_har" name="rescue_e_wrist_har" value=1 <?php if ($information && $information[0]["rescue_e_wrist_har"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Wrist/Ankle Harnesses:
								<input type="text" id="rescue_e_wrist_har_value" name="rescue_e_wrist_har_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_wrist_har_value"] : ""; ?>" placeholder="Wrist/Ankle Harnesses">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_e_extinguishers" name="rescue_e_extinguishers" value=1 <?php if ($information && $information[0]["rescue_e_extinguishers"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Fire Extinguishers:
								<input type="text" id="rescue_e_extinguishers_value" name="rescue_e_extinguishers_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_e_extinguishers_value"] : ""; ?>" placeholder="Fire Extinguishers">
							</div>
						</div>
						<div class="col-lg-1"></div>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Rescue Equipment Inspections: *</strong>
					</div>
					<div class="panel-body">

						<div class="col-sm-6">
							<label for="type" class="control-label">Identified rescue equipment inspected by competent worker: </label>
							<input type="text" id="rescue_equipment_inspected" name="rescue_equipment_inspected" class="form-control" value="<?php echo $information ? $information[0]["rescue_equipment_inspected"] : ""; ?>">
						</div>

						<div class="col-sm-6">
							<label for="type" class="control-label">Employer: </label>
							<input type="text" id="rescue_employer" name="rescue_employer" class="form-control" value="<?php echo $information ? $information[0]["rescue_employer"] : ""; ?>">
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Medical Equipment Requirements:</strong>
						<small>
						(check a where applicable below and indicate quantity needed)
						</small>
					</div>
					<div class="panel-body">
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_first_aid" name="rescue_first_aid" value=1 <?php if ($information && $information[0]["rescue_first_aid"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> First Aid Kit:
								<input type="text" id="rescue_first_aid_value" name="rescue_first_aid_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_first_aid_value"] : ""; ?>">
							</div>
						</div>
						<div class="col-lg-1"></div>
						<div class="col-lg-2">
							<div class="form-group">
								<input type="checkbox" id="rescue_packaging" name="rescue_packaging" value=1 <?php if ($information && $information[0]["rescue_packaging"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Packaging Device:
								<input type="text" id="rescue_packaging_value" name="rescue_packaging_value" class="form-control" value="<?php echo $information ? $information[0]["rescue_packaging_value"] : ""; ?>">
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Additional PPE Requirements:</strong><small>(Indicate what is needed)</small>
					</div>
					<div class="panel-body">
						<div class="col-lg-3">
							<div class="form-group">
								<input type="checkbox" id="rescue_vests" name="rescue_vests" value=1 <?php if ($information && $information[0]["rescue_vests"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> High Visibility Vests<br>
								<input type="checkbox" id="rescue_glasses" name="rescue_glasses" value=1 <?php if ($information && $information[0]["rescue_glasses"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Safety Glasses/Goggles
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<input type="checkbox" id="rescue_hearing" name="rescue_hearing" value=1 <?php if ($information && $information[0]["rescue_hearing"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Hearing Protection<br>
								<input type="checkbox" id="rescue_gloves" name="rescue_gloves" value=1 <?php if ($information && $information[0]["rescue_gloves"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Gloves
							</div>
						</div>


						<div class="col-lg-3">
							<div class="form-group">
								<input type="checkbox" id="rescue_boots" name="rescue_boots" value=1 <?php if ($information && $information[0]["rescue_boots"]) {
																													echo "checked";
																												} elseif (!$information) {
																													echo "";
																												} ?>> Safety Boots<br>
								<input type="checkbox" id="rescue_face" name="rescue_face" value=1 <?php if ($information && $information[0]["rescue_face"]) {
																										echo "checked";
																									} elseif (!$information) {
																										echo "";
																									} ?>> Face Shield
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<input type="checkbox" id="rescue_hats" name="rescue_hats" value=1 <?php if ($information && $information[0]["rescue_hats"]) {
																														echo "checked";
																													} elseif (!$information) {
																														echo "";
																													} ?>> Hard Hats
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<strong>Description of Space :</strong><small>(include location of attendant)</small>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-12">
								<textarea id="rescue_description" name="rescue_description" class="form-control" rows="2"><?php echo $information ? $information[0]["rescue_description"] : ""; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

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