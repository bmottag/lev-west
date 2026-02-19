<script>
	$(function() {
		$(".btn-info").click(function() {
			var oID = $(this).attr("id");
			$.ajax({
				type: 'POST',
				url: base_url + 'more/cargarModalRetesting',
				data: {
					'idConfined': oID,
					'idRetesting': 'x'
				},
				cache: false,
				success: function(data) {
					$('#tablaDatos').html(data);
				}
			});
		});

		$(".btn-success").click(function() {
			var oID = $(this).attr("id");
			$.ajax({
				type: 'POST',
				url: base_url + 'more/cargarModalRetesting',
				data: {
					'idConfined': '',
					'idRetesting': oID
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
							<li class='active'><a href="<?php echo base_url("more/re_testing/" . $jobInfo[0]["id_job"] . "/" . $information[0]['id_job_confined']); ?>">ENVIRONMENTAL CONDITIONS - RE-TESTING</a>
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

	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-building"></i> <strong>Environmental conditions - Re-testing</strong>
				</div>
				<div class="panel-body">
					<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modal" id="<?php echo $information[0]['id_job_confined']; ?>">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Re-testing
					</button><br>
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
					if ($info) {
					?>
						<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
							<thead>
								<tr>
									<th class="text-center">Oxygen</th>
									<th class="text-center">Date/Time</th>
									<th class="text-center">Lower Explosive Limit</th>
									<th class="text-center">Date/Time</th>
									<th class="text-center">Toxic Atmosphere</th>
									<th class="text-center">Instruments Used</th>
									<th class="text-center">Edit</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($info as $lista) :
									echo "<tr>";
									echo "<td class='text-center'>" . $lista['re_oxygen'] . " %</td>";
									echo "<td class='text-center'>" . $lista['re_oxygen_time'] . "</td>";
									echo "<td class='text-center'>" . $lista['re_explosive_limit'] . " %</td>";
									echo "<td class='text-center'>" . $lista['re_explosive_limit_time'] . "</td>";
									echo "<td>" . $lista['re_toxic_atmosphere'] . "</td>";
									echo "<td>" . $lista['re_instruments_used'] . "</td>";

									echo "<td class='text-center'>";
								?>
									<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal" id="<?php echo $lista['id_job_confined_re_testing']; ?>">
										Edit <span class="glyphicon glyphicon-edit" aria-hidden="true">
									</button>
								<?php
									echo "</td>";
								endforeach;
								?>
							</tbody>
						</table>
					<?php } ?>
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
<!-- /#page-wrapper -->


<!--INICIO Modal para adicionar HAZARDS -->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>
<!--FIN Modal para adicionar HAZARDS -->

<!-- Tables -->
<script>
	$(document).ready(function() {
		$('#dataTables').DataTable({
			responsive: true,
			"pageLength": 100
		});
	});
</script>