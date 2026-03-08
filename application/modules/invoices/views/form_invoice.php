<script type="text/javascript" src="<?php echo base_url("assets/js/validate/invoice/form_invoice.js?v=1.0.0"); ?>"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>
.ui-datepicker {
    z-index: 9999 !important;
}
</style>

<script>
	$(document).ready(function() {
		$('.js-example-basic-single').select2();
	});
</script>

<script>
	$(function() {
		$("#date, #due_date").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
</script>

<script>
	$(function() {

		$(".btn-primary").click(function() {
			var oID = $(this).attr("id");
			$.ajax({
				type: 'POST',
				url: base_url + 'workorders/cargarModalPersonal',
				data: {
					'idWorkorder': oID
				},
				cache: false,
				success: function(data) {
					$('#tablaDatos').html(data);
				}
			});
		});

	});
</script>

<div id="page-wrapper">
	<br>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">

					<?php
					$userRol = $this->session->rol;
					?>
					<a class="btn btn-gris btn-xs" href=" <?php echo base_url() . 'invoices'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a>
					<i class="fa fa-money"></i> <strong>NEW INVOICE</strong>
				</div>
				<div class="panel-body">

					<?php
					/**
					 * If it is:
					 * SUPER ADMIN, MANAGEMENT, ACCOUNTING ROLES and WORK ORDER USER
					 * They have acces to asign rate and dowloadinvoice
					 */
					if ($information) {
					?>
							<ul class="nav nav-pills">
								<li class='active'><a href="<?php echo base_url('workorders/add_workorder/' . $information[0]["id_invoice"]) ?>">Edit</a>
								</li>
								<li><a href="<?php echo base_url('workorders/view_workorder/' . $information[0]["id_invoice"]) ?>">Asign rate</a>
								</li>
								<li><a href="<?php echo base_url('workorders/generaWorkOrderPDF/' . $information[0]["id_invoice"]) ?>" target="_blank">Download invoice</a>
								</li>
								<li><a href="<?php echo base_url('workorders/foreman_view/' . $information[0]["id_invoice"]) ?>">Foreman View</a>
								</li>
							</ul>
						<?php 
						echo "<br>";
					}
					?>

					<?php
					$retornoExito = $this->session->flashdata('retornoExito');
					if ($retornoExito) {
					?>
						<div class="row">
							<div class="col-lg-12">
								<div class="alert alert-success ">
									<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
									<?php echo $retornoExito ?>
								</div>
							</div>
						</div>
					<?php
					}

					$retornoError = $this->session->flashdata('retornoError');
					if ($retornoError) {
					?>
						<div class="row">
							<div class="col-lg-12">
								<div class="alert alert-danger ">
									<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
									<?php echo $retornoError ?>
								</div>
							</div>
						</div>
					<?php
					}
					?>

					<?php
					if ($information) {
					?>
						<div class="row">
							<div class="col-lg-12">
								<div class="alert alert-<?php echo $information[0]["status_style"]; ?>">
									<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
									Actual status: <strong><?php echo $information[0]["status_name"]; ?></strong>
								</div>
							</div>
						</div>
					<?php } ?>


					<form name="form" id="form" class="form-horizontal" method="post">
						<input type="hidden" id="hddIdentificador" name="hddIdentificador" value="<?php echo $information ? $information[0]["id_invoice"] : ""; ?>" />

						<div class="form-group">
							<label class="col-sm-4 control-label" for="taskDescription">Job Code/Name :</label>
							<div class="col-sm-5">
								<select name="jobName" id="jobName" class="form-control js-example-basic-single" <?php echo $deshabilitar; ?>>
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($jobs); $i++) { ?>
										<option value="<?php echo $jobs[$i]["id_job"]; ?>" <?php if ($information && $information[0]["fk_id_job"] == $jobs[$i]["id_job"]) {
																								echo "selected";
																							}  ?>><?php echo $jobs[$i]["job_description"]; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label" for="hddTask">Date :</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="date" name="date" value="<?php echo $information ? $information[0]["date_issue"] : ""; ?>" placeholder="Date" required <?php echo $deshabilitar; ?> />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label" for="hddTask">Due Date :</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="due_date" name="due_date" value="<?php echo $information ? $information[0]["due_date"] : ""; ?>" placeholder="Due Date" required <?php echo $deshabilitar; ?> />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label" for="company">Company:</label>
							<div class="col-sm-5">
								<input type="hidden" id="company" name="company" class="form-control" placeholder="Company" value="<?php echo $information ? $information[0]["id_company"] : ""; ?>" <?php echo $deshabilitar; ?>>
								<input type="text" id="companyName" name="companyName" class="form-control" placeholder="Company" value="<?php echo $information ? $information[0]["company"] : ""; ?>" disabled>
							</div>
						</div>

						<?php 
						$year = date("Y");
						$numberValue = $nextInvoiceNumber;

						if($information){
							$parts = explode("-", $information[0]["number"]);
							if(count($parts) == 2){
								$year = $parts[0];
								$numberValue = $parts[1];
							}
						}
						?>

						<div class="form-group text-danger">
							<label class="col-sm-4 control-label" for="number">Invoice #:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-addon"><?php echo $year; ?>-</span>
									<input type="text" 
										id="number" 
										name="number" 
										class="form-control"
										value="<?php echo $numberValue; ?>"
										<?php echo $deshabilitar; ?>>
								</div>
							</div>
						</div>

						<input type="hidden" id="year" name="year" value="<?php echo $year; ?>">

						<?php
						if ($information) {
						?>


							<div class="form-group">
								<label class="col-sm-4 control-label" for="taskDescription">Status :</label>
								<div class="col-sm-5">
									<select name="status" id="status" class="form-control" <?php echo $deshabilitar; ?>>
										<option value=''>Select...</option>
										<?php
										if($statusList) {
											foreach ($statusList as $status) {
										?>
											<option value="<?php echo $status["status_slug"]; ?>" <?php if($information && $information[0]["invoice_status"] == $status["status_slug"]) { echo "selected"; }  ?> ><?php echo $status["status_name"]; ?> </option>
										<?php
											}
										}
										?>
									</select>
								</div>
							</div>
						<?php } ?>

						<?php if (!$deshabilitar) { ?>
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:100%;" align="center">

										<button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary">
											Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
										</button>

									</div>
								</div>
							</div>
						<?php } ?>

					</form>
				</div>
			</div>
		</div>
	</div>

	<!--INICIO FORMULARIOS -->
	<?php
	if ($information) {
	?>

		<!--INICIO PERSONAL -->
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<b>PERSONNEL</b>
					</div>
					<div class="panel-body">

						<?php if (!$deshabilitar) { ?>
							<div class="col-lg-12">

								<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal" id="<?php echo $information[0]["id_workorder"]; ?>">
									<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Personnel
								</button><br>
							</div>
						<?php } ?>

						<?php
						if ($workorderPersonal) {
						?>
							<table class="table table-bordered table-striped table-hover table-condensed">
								<tr class="info">
									<th class="text-center">Employee Name</th>
									<th class="text-center">Employee Type</th>
									<th class="text-center">Work Done</th>
									<th class="text-center">Hours</th>
									<th class="text-center">Links</th>
								</tr>
								<?php
								foreach ($workorderPersonal as $data) :
									echo "<tr>";
									echo "<td ><small>" . $data['name'] . "</small></td>";

									$idRecord = $data['id_workorder_personal'];
								?>
									<form name="personal_<?php echo $idRecord ?>" id="personal_<?php echo $idRecord ?>" method="post" action="<?php echo base_url("workorders/save_hour"); ?>">
										<input type="hidden" id="formType" name="formType" value="personal" />
										<input type="hidden" id="hddId" name="hddId" value="<?php echo $idRecord; ?>" />
										<input type="hidden" id="hddIdWorkOrder" name="hddIdWorkOrder" value="<?php echo $data['fk_id_workorder']; ?>" />
										<input type="hidden" id="rate" name="rate" value="<?php echo $data['rate']; ?>" />
										<input type="hidden" id="check_pdf" name="check_pdf" value="<?php echo $data['view_pdf']; ?>" />
										<input type="hidden" id="quantity" name="quantity" value=1>

										<td>
											<select name="type_personal" id="type_personal" class="form-control">
												<option value=''>Select...</option>
												<?php for ($i = 0; $i < count($employeeTypeList); $i++) { ?>
													<option value="<?php echo $employeeTypeList[$i]["id_employee_type"]; ?>" <?php if ($data["fk_id_employee_type"] == $employeeTypeList[$i]["id_employee_type"]) {
																																	echo "selected";
																																}  ?>><?php echo $employeeTypeList[$i]["employee_type"]; ?></option>
												<?php } ?>
											</select>
										</td>

										<td>
											<textarea id="description" name="description" class="form-control" rows="3" required <?php echo $deshabilitar; ?>><?php echo $data['description']; ?></textarea>
										</td>

										<td>
											<input type="text" id="hours" name="hours" class="form-control" placeholder="Hours" value="<?php echo $data['hours']; ?>" required <?php echo $deshabilitar; ?>>
										</td>

										<td class='text-center'>
											<button type="submit" id="btnSubmit" name="btnSubmit" class="btn btn-primary btn-xs" title="Save" <?php echo $deshabilitar; ?>>
												Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
											</button>
									</form>

									<br><br>
									<?php if (!$deshabilitar) { ?>
										<a class='btn btn-danger btn-xs' href='<?php echo base_url('workorders/deleteRecord/personal/' . $data['id_workorder_personal'] . '/' . $data['fk_id_workorder'] . '/add_workorder') ?>' id="btn-delete">
											Delete <i class="fa fa-trash-o"></i>
										</a>
									<?php } else {
										echo "---";
									} ?>
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
		<!--FIN PERSONAL -->

	<?php } ?>

</div>

<!--INICIO Modal para PERSONAL -->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>
<!--FIN Modal para PERSONAL -->