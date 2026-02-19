<script type="text/javascript" src="<?php echo base_url("assets/js/validate/workorder/update_state.js"); ?>"></script>
<div id="page-wrapper">
	<br>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a class="btn btn-info btn-xs" href=" <?php echo base_url('admin/job/log'); ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back to Search Job Code/Name.</a>
					<i class="fa fa-money"></i> <strong>JOB CODE/NAME - AUDIT LOG</strong>
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

					<?php
					if (!$workOrderInfo) {
						echo "<a href='#' class='btn btn-danger btn-block'>No data was found matching your criteria</a>";
					} else {
					?>
						<table width="100%" class="table table-striped table-bordered table-hover small" id="dataTables">
							<thead>
								<tr>
									<th class='text-center'>Job Name</th>
									<th class='text-center'>Responsible</th>
									<th class='text-center'>Date</th>
									<th class='text-center'>Action</th>
									<th class='text-center'>Table</th>
									<th class='text-center'>Description</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($workOrderInfo as $lista) :
									$old = (json_decode($lista['comment'])->old != '') ? json_encode(json_decode($lista['comment'])->old[0]) : 'null';
									$new = (json_decode($lista['comment'])->new != '') ? json_decode($lista['comment'])->new : 'null';

									if ($lista['token'] == 'update') {
										$oldDecode = json_decode($lista['comment'])->old;
										$newDecode = json_decode(json_decode($lista['comment'])->new);

										if ($lista['type'] === 'job_code_status') {

											switch ($newDecode->state) {
												case 1:
													$text = 'Active';
													break;
												case 2:
													$text = 'Inactive';
													break;
											}

											switch ($oldDecode) {
												case '"1"':
													$oldState = 'Active';
													break;
												case '"2"':
													$oldState = 'Inactive';
													break;
											}

											$textOld = 'state: ' . $oldState . '';
											$textNew = 'state: ' . $text . '';
										} else if ($lista['type'] === 'job_code') {

											switch ($newDecode->state) {
												case 1:
													$text = 'Active';
													break;
												case 2:
													$text = 'Inactive';
													break;
											}

											switch ($oldDecode[0]->state) {
												case 1:
													$oldState = 'Active';
													break;
												case 2:
													$oldState = 'Inactive';
													break;
											}

											$company = ' ';
											if ($newDecode->fk_id_company != 0) {
												$arrParam = array(
													"table" => "param_company",
													"order" => "id_company",
													"column" => "id_company",
													"id" => $newDecode->fk_id_company
												);
												$company = $this->general_model->get_basic_search($arrParam); //company list

												$company = $company[0]['company_name'];
											}

											$companyOld = ' ';
											if ($oldDecode[0]->fk_id_company != 0) {
												$arrParam = array(
													"table" => "param_company",
													"order" => "id_company",
													"column" => "id_company",
													"id" => $oldDecode[0]->fk_id_company
												);
												$companyOld = $this->general_model->get_basic_search($arrParam); //company list

												$companyOld = $companyOld[0]['company_name'];
											}

											$textOld = 'job_code: ' . $oldDecode[0]->job_code . ', job_name: ' . $oldDecode[0]->job_name . ', job_description: ' . $oldDecode[0]->job_description . 'fk_id_company: ' . $companyOld . ', state: ' . $oldState . ', markup: ' . $oldDecode[0]->markup . ' profit: ' . $oldDecode[0]->profit . ', notes: ' . $oldDecode[0]->notes . ', qr_code_timesheet: ' . $oldDecode[0]->qr_code_timesheet . 'planning_message: ' . $oldDecode[0]->planning_message . ', flag_upload_details: ' . $oldDecode[0]->flag_upload_details . ', flag_expenses: ' . $oldDecode[0]->flag_expenses . '';

											$textNew = 'job_code: ' . $newDecode->job_code . ', job_name: ' . $newDecode->job_name . ', job_description: ' . $newDecode->job_description . ', company: ' . $company . ', state: ' . $text . ', markup: ' . $newDecode->markup . ' profit: ' . $newDecode->profit . ', notes: ' . $newDecode->notes . ', qr_code_timesheet: ' . (isset($newDecode->qr_code_timesheet) ? $newDecode->qr_code_timesheet : ' ') . ', planning_message: ' . $newDecode->planning_message . ', flag_upload_details: ' . (isset($newDecode->flag_upload_details) ? $newDecode->flag_upload_details : ' ') . ', flag_expenses: ' . (isset($newDecode->flag_expenses) ? $newDecode->flag_expenses : ' ') . '';
										} else {
											$textNew = $new;
											$textOld = $old;
										}
									} else if ($lista['token'] == 'insert') {
										$textOld = $old;
										$newDecode = json_decode(json_decode($lista['comment'])->new);

										$company = ' ';
										if ($newDecode->fk_id_company != 0) {
											$arrParam = array(
												"table" => "param_company",
												"order" => "id_company",
												"column" => "id_company",
												"id" => $newDecode->fk_id_company
											);
											$company = $this->general_model->get_basic_search($arrParam); //company list

											$company = $company[0]['company_name'];
										}

										switch ($newDecode->state) {
											case 1:
												$text = 'Active';
												break;
											case 2:
												$text = 'Inactive';
												break;
										}
										if ($lista['type'] === 'job_code_status') {
											$textNew = 'state: ' . $text . '';
										} else if ($lista['type'] === 'job_code') {
											$textNew = 'job_code: ' . $newDecode->job_code .
												', job_name: ' . $newDecode->job_name .
												', job_description: ' . $newDecode->job_description .
												', company: ' . $company .
												', state: ' . $text .
												', markup: ' . $newDecode->markup .
												', profit: ' . $newDecode->profit .
												', notes: ' . $newDecode->notes .
												', qr_code_timesheet: ' . (isset($newDecode->qr_code_timesheet) ? $newDecode->qr_code_timesheet : ' ') .
												', planning_message: ' . $newDecode->planning_message .
												', flag_upload_details: ' . (isset($newDecode->flag_upload_details) ? $newDecode->flag_upload_details : ' ') .
												', flag_expenses: ' . (isset($newDecode->flag_expenses) ? $newDecode->flag_expenses : ' ') . '';
										} else {
											$textNew = $new;
										}
									}
									echo "<tr>";
									echo "<td>" . $lista['job_description'] . "</td>";
									echo "<td>" . $lista['name'] . "</td>";
									echo "<td>" . $lista['created_on'] . "</td>";
									echo "<td class='text-center'>" . $lista['token'] . "</td>";
									echo "<td class='text-center'>" . $lista['type'] . "</td>";
									echo "<td> <b>Before: </b>" . $textOld . "<br><br> <b>After:</b> " . $textNew . "</td>";
									echo '</tr>';
								endforeach;
								?>
							</tbody>
						</table>

					<?php } ?>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- /#page-wrapper -->

<!-- Tables -->
<script>
	$(document).ready(function() {
		$('#dataTables').DataTable({
			responsive: true,
			"ordering": false,
			paging: false,
			"info": false,
			"searching": false
		});
	});
</script>