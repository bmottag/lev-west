<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-dark">
				<div class="panel-heading">
					<i class="fa fa-briefcase"></i> <strong>Accounting Control Sheet (ACS) - Income for all Job Codes/Names</strong>
				</div>
				<div class="panel-body small">

					<?php
					if ($jobList) {
					?>
						<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
							<thead>
								<tr>
									<th class="text-center">Job Code/Name</th>
									<th class="text-center">Numbers of W.O.</th>
									<th class="text-center">Numbers of Personal Hours</th>
									<th class="text-center">Personal Income</th>
									<th class="text-center">Material Income</th>
									<th class="text-center">Receipt Income</th>
									<th class="text-center">Equipment Income</th>
									<th class="text-center">Subcontractor Income</th>
									<th class="text-center">Total Income</th>
									<th class="text-center">Download</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$ci = &get_instance();
								$ci->load->model("acs_model");

								foreach ($jobList as $lista) :

									$arrParam = array("idJob" => $lista['id_job']);
									$noACS = $ci->acs_model->countACS($arrParam); //cuenta registros de Workorders

									$hoursPersonal = $ci->acs_model->countHoursPersonal($arrParam); //cuenta horas de personal

									$arrParam = array("idJob" => $lista['id_job'], "table" => "acs_personal");
									$incomePersonal = $ci->acs_model->countIncome($arrParam); //cuenta horas de personal

									$arrParam = array("idJob" => $lista['id_job'], "table" => "acs_materials");
									$incomeMaterial = $ci->acs_model->countIncome($arrParam); //cuenta horas de personal

									$arrParam = array("idJob" => $lista['id_job'], "table" => "acs_receipt");
									$incomeReceipt = $ci->acs_model->countIncome($arrParam); //cuenta horas de personal

									$arrParam = array("idJob" => $lista['id_job'], "table" => "acs_equipment");
									$incomeEquipment = $ci->acs_model->countIncome($arrParam); //cuenta horas de personal

									$arrParam = array("idJob" => $lista['id_job'], "table" => "acs_ocasional");
									$incomeSubcontractor = $ci->acs_model->countIncome($arrParam); //cuenta horas de personal

									$total = $incomePersonal + $incomeMaterial + $incomeEquipment + $incomeSubcontractor + $incomeReceipt;

									echo "<tr>";
									echo "<td>" . $lista['job_description'] . "</td>";
									echo "<td class='text-center'>" . $noACS . "</td>";
									echo "<td class='text-center'>" . $hoursPersonal . "</td>";

									echo "<td class='text-right'>";
									echo '$' . number_format($incomePersonal, 2);
									echo "</td>";

									echo "<td class='text-right'>";
									echo '$' . number_format($incomeMaterial, 2);
									echo "</td>";

									echo "<td class='text-right'>";
									echo '$' . number_format($incomeReceipt, 2);
									echo "</td>";

									echo "<td class='text-right'>";
									echo '$' . number_format($incomeEquipment, 2);
									echo "</td>";

									echo "<td class='text-right'>";
									echo '$' . number_format($incomeSubcontractor, 2);
									echo "</td>";

									echo "<td class='text-right'>";
									echo '$' . number_format($total, 2);
									echo "</td>";

									echo "<td class='text-center'>";
								?>
									<a href='<?php echo base_url('acs/generaACSXLS/' . $lista['id_job']); ?>' target="_blank"> <img src='<?php echo base_url_images('xls.png'); ?>'></a>
								<?php
									echo "</td>";
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

<!-- Tables -->
<script>
	$(document).ready(function() {
		$('#dataTables').DataTable({
			responsive: true,
			"ordering": true,
			paging: false,
			"info": false
		});

		$('.js-example-basic-single').select2({
			width: '100%'
		});
	});
</script>