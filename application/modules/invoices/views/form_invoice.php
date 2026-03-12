<script type="text/javascript" src="<?php echo base_url("assets/js/validate/invoice/form_invoice.js?v=4.0.0"); ?>"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link rel="stylesheet" href="<?php echo base_url('assets/css/cards.css'); ?>">

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

		$(".btn-info").click(function() {
			var oID = $(this).attr("id");
			$.ajax({
				type: 'POST',
				url: base_url + 'invoices/cargarModalItems',
				data: {
					'idInvoice': oID
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
								<li class='active'><a href="<?php echo base_url('invoices/add_invoice/' . $information[0]["id_invoice"]) ?>">Edit</a>
								</li>
								<li><a href="<?php echo base_url('invoices/generaInvoicePDF/' . $information[0]["id_invoice"]) ?>" target="_blank">Download invoice</a>
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
								<input type="text" class="form-control" id="date" name="date"
								value="<?php echo $information ? $information[0]['date_issue'] : date('Y-m-d'); ?>"
								placeholder="Date" required <?php echo $deshabilitar; ?> />
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
								<input type="text" id="companyName" name="companyName" class="form-control" placeholder="Company" value="<?php echo $information ? $information[0]["company_name"] : ""; ?>" disabled>
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

						<div class="form-group">
							<label class="col-sm-4 control-label" for="taskDescription">Link to :</label>
							<div class="col-sm-5">
								<select name="link_to" id="link_to" class="form-control">
									<option value=''>Select...</option>
									<option value='wo' <?php if($information && $information[0]["is_wo_or_claim"] == 'wo') { echo "selected"; }  ?>>W.O.</option>
									<option value='claim' <?php if($information && $information[0]["is_wo_or_claim"] == 'claim') { echo "selected"; }  ?>>Claim</option>
								</select>
							</div>
						</div>

						<input type="hidden" id="selected_link_id" value="<?php echo $information ? $information[0]['fk_id_wo_or_claim'] : ''; ?>">

						<div class="form-group" id="div_list_work_order">
							<label class="col-sm-4 control-label" id="label_list" for="work_order_div">Select Work Order</label>
							<div class="col-sm-5">
								<select name="list_work_order" id="list_work_order" class="form-control">
									<option value="">Select...</option>
								</select>
							</div>
						</div>

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

		<!--ITEMS -->
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<b>ITEMS</b>
					</div>
					<div class="panel-body">

						<?php if (!$deshabilitar) { ?>
							<div class="col-lg-12">
								<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modal" id="<?php echo $information[0]["id_invoice"]; ?>">
									<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add an Item
								</button><br>
							</div>
						<?php } ?>

						<?php
						if ($items) {
						?>
							<form method="post" action="<?php echo base_url('invoices/save_all'); ?>">
								<input type="hidden" name="hddIdInvoice" value="<?php echo $information[0]["id_invoice"]; ?>">

								<table class="table table-bordered table-striped table-hover table-condensed table-mobile">
									<thead>
										<tr>
											<th width='50%' class="text-center">Description</th>
											<th width='10%' class="text-right">Quantity</th>
											<th width='10%' class="text-center">Unit</th>
											<th width='10%' class="text-right">Rate</th>
											<th width='10%' class="text-right">Value</th>
											<th width='10%' class="text-center">Actions</th>
										</tr>
									</thead>

									<?php 
										$total = 0;
										foreach ($items as $data): 
											$total += $data['value'];
									?>
											<tr>
												<td>

													<label class="td-label">Description</label>
													<textarea 
													name="description[]" 
													class="form-control" 
													rows="3"
													required
													<?php echo $deshabilitar; ?>><?php echo htmlspecialchars(trim($data['description'])); ?></textarea>

													<input type="hidden" name="id_item[]" value="<?php echo $data['id_invoices_items']; ?>">

												</td>

												<td class="table-desktop-numeric">
													<label class="td-label">Quantity</label>
													<input type="number" step="0.5" name="quantity[]" class="form-control quantity-field"
													value="<?php echo $data['quantity']; ?>" required>
												</td>

												<td>
													<label class="td-label">Unit</label>
													<input type="text" name="unit[]" class="form-control"
													value="<?php echo $data['unit']; ?>" required>
												</td>

												<td class="table-desktop-numeric">
													<label class="td-label">Rate</label>

													<div class="input-group">
														<span class="input-group-addon">$</span>
														<input type="number" step="any" name="rate[]" class="form-control rate-field"
														value="<?php echo $data['rate']; ?>" required>
													</div>

												</td>

												<td class="table-desktop-numeric">
													<label class="td-label">Value</label>

													<div class="input-group">
														<span class="input-group-addon">$</span>
														<input type="text" class="form-control total-field" value="<?php echo number_format($data['value'],2); ?>" readonly>
													</div>
												</td>

												<td class="text-center action-col">
													<?php if (!$deshabilitar) { ?>
													<a class="btn btn-danger btn-xs"
													href="<?php echo base_url('invoices/delete_item/'.$data['id_invoices_items'].'/'.$information[0]["id_invoice"]); ?>">
													<i class="fa fa-trash"></i>
													</a>
													<?php } ?>
												</td>
											</tr>
									<?php endforeach; ?>
								</table>

								<div class="row" style="margin-top:20px">
									<div class="col-md-4 col-md-offset-8">
										<div class="panel panel-default">
											<div class="panel-body">
												<div class="form-group">
													<label>Subtotal</label>
													<input type="text" id="subtotal" class="form-control" value="$ <?php echo number_format($total,2); ?>" readonly>
												</div>
											</div>
										</div>
									</div>
								</div>

								<?php if(!$deshabilitar){ ?>
									<div class="text-right" style="margin-top:20px;">
										<button type="submit" class="btn btn-primary">
											<span class="glyphicon glyphicon-floppy-disk"></span> Save All Items
										</button>
									</div>
								<?php } ?>

							</form>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!--FIN ITEM -->

	<?php } ?>

</div>

<!--INICIO Modal para ITEM -->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>
<!--FIN Modal para ITEM -->