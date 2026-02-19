<script type="text/javascript" src="<?php echo base_url("assets/js/validate/workorder/update_state.js"); ?>"></script>
<div id="page-wrapper">
	<br>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a class="btn btn-info btn-xs" href=" <?php echo base_url('workorders/log'); ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back to Search W.O.</a>
					<i class="fa fa-money"></i> <strong>WORK ORDERS - AUDIT LOG</strong>
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
									<th class='text-center'>W.O. #</th>
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
								$textOld = '';
								$textNew = '';
								foreach ($workOrderInfo as $lista) :
									$old = (json_decode($lista['comment'])->old != '') ? json_encode(json_decode($lista['comment'])->old[0]) : 'null';
									$new = (json_decode($lista['comment'])->new != '') ? json_decode($lista['comment'])->new : 'null';

									if ($lista['token'] == 'update') {
										$oldDecode = json_decode($lista['comment'])->old[0];
										$newDecode = json_decode(json_decode($lista['comment'])->new);

										if ($lista['type'] === 'workorder') {

											$textOld = 'date: ' . $oldDecode->date . ', observation: ' . $oldDecode->observation . ', foreman_name_wo: ' . $oldDecode->foreman_name_wo . ', foreman_movil_number_wo: ' . $oldDecode->foreman_movil_number_wo . ', foreman_email_wo: ' . $oldDecode->foreman_email_wo . '';

											$textNew = 'date: ' . $newDecode->date . ', observation: ' . $newDecode->observation . ', foreman_name_wo: ' . $newDecode->foreman_name_wo . ', foreman_movil_number_wo: ' . $newDecode->foreman_movil_number_wo . ', foreman_email_wo: ' . $newDecode->foreman_email_wo . '';
										} else if ($lista['type'] === 'workorder_personal') {
											$arrParam = array(
												"table" => "user",
												"order" => "id_user",
												"column" => "id_user",
												"id" => $oldDecode->fk_id_user
											);
											$user = $this->general_model->get_basic_search($arrParam); //job list

											$arrEmployee = array(
												"table" => "param_employee_type",
												"order" => "employee_type",
												"column" => "id_employee_type",
												"id" => $oldDecode->fk_id_employee_type
											);
											$oldEmployeeType = $this->general_model->get_basic_search($arrEmployee); //employee type list

											$textOld = 'user: ' . $user[0]['first_name'] . ' ' . $user[0]['last_name'] . ', Employee_type: ' . $oldEmployeeType[0]['employee_type'] . ', hours: ' . $oldDecode->hours . ', description: ' . $oldDecode->description . '';

											if(isset($newDecode->fk_id_employee_type)){
												$arrEmployee = array(
													"table" => "param_employee_type",
													"order" => "employee_type",
													"column" => "id_employee_type",
													"id" => $newDecode->fk_id_employee_type
												);
												$newEmployeeType = $this->general_model->get_basic_search($arrEmployee); //employee type list

												$textNew = 'user: ' . $user[0]['first_name'] . ' ' . $user[0]['last_name'] . ', Employee_type: ' . $newEmployeeType[0]['employee_type'] . ', hours: ' . $newDecode->hours . ', description: ' . $newDecode->description . '';
											}else{
												$textNew = 'user: ' . $user[0]['first_name'] . ' ' . $user[0]['last_name'] . ', Change: ' . json_decode($lista['comment'])->new;
											}
										} else if ($lista['type'] === 'workorder_materials') {

											$arrParam = array(
												"table" => "param_material_type",
												"order" => "material",
												"column" => "id_material",
												"id" => $oldDecode->fk_id_material
											);
											$material = $this->general_model->get_basic_search($arrParam); //worker´s list

											$textOld = 'material: ' . $material[0]['material'] . ', quantity: ' . $oldDecode->quantity . ', unit: ' . $oldDecode->unit . ', description: ' . $oldDecode->description . '';

											$textNew = 'material: ' . $material[0]['material'] . ', quantity: ' . $newDecode->quantity . ', unit: ' . $newDecode->unit . ', description: ' . $newDecode->description . '';
										} else if ($lista['type'] === 'workorder_receipt') {

											$textOld = 'place: ' . $oldDecode->place . ', price: ' . $oldDecode->price . ', description: ' . $oldDecode->description . '';

											$textNew = 'place: ' . $newDecode->place . ', price: ' . $newDecode->price . ', description: ' . $newDecode->description . '';
										} else if ($lista['type'] === 'workorder_equipment') {

											$textOld = 'fk_id_vehicle: ' . $oldDecode->fk_id_vehicle . ', description: ' . $oldDecode->description . ', hours: ' . $oldDecode->hours . ', quantity: ' . $oldDecode->quantity . '';

											$textNew = 'fk_id_vehicle: ' . $oldDecode->fk_id_vehicle . ', description: ' . $newDecode->description . ', hours: ' . $newDecode->hours . ', quantity: ' . $newDecode->quantity . '';
										} else if ($lista['type'] === 'workorder_ocasional') {

											$arrParam = array(
												"table" => "param_company",
												"order" => "id_company",
												"column" => "id_company",
												"id" => $oldDecode->fk_id_company
											);
											$company = $this->general_model->get_basic_search($arrParam); //job list

											$textOld = 'company: ' . $company[0]['company_name'] . ', equipment: ' . $oldDecode->equipment . ', quantity: ' . $oldDecode->quantity . ', unit: ' . $oldDecode->unit . ', hours: ' . $oldDecode->hours . ' description: ' . $oldDecode->description . '';

											$textNew = 'company: ' . $company[0]['company_name'] . ', equipment: ' . $oldDecode->equipment . ', quantity: ' . $newDecode->quantity . ', unit: ' . $newDecode->unit . ', hours: ' . $newDecode->hours . ' description: ' . $newDecode->description . '';
										} else {
											$textNew = $new;
											$textOld = $old;
										}
									} else if ($lista['token'] == 'insert') {
										$textOld = $old;
										$newDecode = json_decode(json_decode($lista['comment'])->new);
										if ($lista['type'] === 'workorder_state') {

											switch ($newDecode->state) {
												case 0:
													$text = 'On Field';
													break;
												case 1:
													$text = 'In Progress';
													break;
												case 2:
													$text = 'Revised';
													break;
												case 3:
													$text = 'Send to the Client';
													break;
												case 4:
													$text = 'Closed';
													break;
												case 5:
													$text = 'Accounting';
													break;
											}

											$textNew = '	date_issue: ' . $newDecode->date_issue . ', observation: ' . $newDecode->observation . ', state: ' . $text . '';
										} else if ($lista['type'] === 'workorder_personal') {

											$arrParam = array(
												"table" => "user",
												"order" => "id_user",
												"column" => "id_user",
												"id" => $newDecode->fk_id_user
											);
											$user = $this->general_model->get_basic_search($arrParam); //job list

											$arrEmployee = array(
												"table" => "param_employee_type",
												"order" => "employee_type",
												"column" => "id_employee_type",
												"id" => $newDecode->fk_id_employee_type
											);
											$newEmployeeType = $this->general_model->get_basic_search($arrEmployee); //employee type list

											$textNew = 'user: ' . $user[0]['first_name'] . ' ' . $user[0]['last_name'] . ', Employee_type: ' . $newEmployeeType[0]['employee_type']  . ', hours: ' . $newDecode->hours . ', description: ' . $newDecode->description . '';
										} else if ($lista['type'] === 'workorder_materials') {

											$arrParam = array(
												"table" => "param_material_type",
												"order" => "material",
												"column" => "id_material",
												"id" => $newDecode->fk_id_material
											);
											$material = $this->general_model->get_basic_search($arrParam); //worker´s list

											$textNew = 'material: ' . $material[0]['material'] . ', quantity: ' . $newDecode->quantity . ', unit: ' . $newDecode->unit . ', description: ' . $newDecode->description . '';
										} else if ($lista['type'] === 'workorder_equipment') {

											$textNew = 'fk_id_vehicle: ' . $newDecode->fk_id_vehicle . ', description: ' . $newDecode->description . ', hours: ' . $newDecode->hours . ', quantity: ' . $newDecode->quantity . '';
										} else if ($lista['type'] === 'workorder_ocasional') {

											$arrParam = array(
												"table" => "param_company",
												"order" => "id_company",
												"column" => "id_company",
												"id" => $newDecode->fk_id_company
											);
											$company = $this->general_model->get_basic_search($arrParam); //job list

											$textNew = 'company: ' . $company[0]['company_name'] . ', equipment: ' . $newDecode->equipment . ', quantity: ' . $newDecode->quantity . ', unit: ' . $newDecode->unit . ', hours: ' . $newDecode->hours . ' description: ' . $newDecode->description . '';
										} else {
											$textNew = $new;
										}
									} 
									/*
									else {
										$oldDecode = json_decode($lista['comment'])->old[0];
										$textNew = 'null';
										if ($lista['type'] === 'workorder') {

											$textOld = 'date: ' . $oldDecode->date . ', observation: ' . $oldDecode->observation . ', foreman_name_wo: ' . $oldDecode->foreman_name_wo . ', foreman_movil_number_wo: ' . $oldDecode->foreman_movil_number_wo . ', foreman_email_wo: ' . $oldDecode->foreman_email_wo . '';
										} else if ($lista['type'] === 'workorder_personal') {
											$arrParam = array(
												"table" => "user",
												"order" => "id_user",
												"column" => "id_user",
												"id" => $oldDecode->fk_id_user
											);
											$user = $this->general_model->get_basic_search($arrParam); //job list

											$arrEmployee = array(
												"table" => "param_employee_type",
												"order" => "employee_type",
												"column" => "id_employee_type",
												"id" => $oldDecode->fk_id_employee_type
											);
											$oldEmployeeType = $this->general_model->get_basic_search($arrEmployee); //employee type list

											$textOld = 'user: ' . $user[0]['first_name'] . ' ' . $user[0]['last_name'] . ', Employee_type: ' . $oldEmployeeType[0]['employee_type'] . ', hours: ' . $oldDecode->hours . ', description: ' . $oldDecode->description . '';
										} else if ($lista['type'] === 'workorder_materials') {

											$arrParam = array(
												"table" => "param_material_type",
												"order" => "material",
												"column" => "id_material",
												"id" => $oldDecode->fk_id_material
											);
											$material = $this->general_model->get_basic_search($arrParam); //worker´s list

											$textOld = 'material: ' . $material[0]['material'] . ', quantity: ' . $oldDecode->quantity . ', unit: ' . $oldDecode->unit . ', description: ' . $oldDecode->description . '';
										} else if ($lista['type'] === 'workorder_receipt') {

											$textOld = 'place: ' . $oldDecode->place . ', price: ' . $oldDecode->price . ', description: ' . $oldDecode->description . '';
										} else if ($lista['type'] === 'workorder_equipment') {

											$textOld = 'fk_id_vehicle: ' . $oldDecode->fk_id_vehicle . ', description: ' . $oldDecode->description . ', hours: ' . $oldDecode->hours . ', quantity: ' . $oldDecode->quantity . '';
										} else if ($lista['type'] === 'workorder_ocasional') {

											$arrParam = array(
												"table" => "param_company",
												"order" => "id_company",
												"column" => "id_company",
												"id" => $oldDecode->fk_id_company
											);
											$company = $this->general_model->get_basic_search($arrParam); //job list

											$textOld = 'company: ' . $company[0]['company_name'] . ', equipment: ' . $oldDecode->equipment . ', quantity: ' . $oldDecode->quantity . ', unit: ' . $oldDecode->unit . ', hours: ' . $oldDecode->hours . ' description: ' . $oldDecode->description . '';
										} else {

											$textOld = $old;
										}
									}
*/
									echo "<tr>";
									echo "<td class='text-center'>";
									echo "<a href='" . base_url('workorders/add_workorder/' . $lista['type_id']) . "'>" . $lista['type_id'] . "</a>";
									echo "</td>";
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