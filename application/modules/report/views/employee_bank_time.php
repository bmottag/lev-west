<div id="page-wrapper">
	<br>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<i class="fa fa-flag-o"></i> EMPLOYEE BANK TIME
				</div>
				<div class="panel-body">
				<?php	
				if(!$info){
				?>
					<div class="alert alert-danger ">
						<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
						<strong>Note: </strong>No records.
					</div>
				<?php	
				}else{
				?>				
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th class="text-center">Name</th>
								<th class="text-center">Period</th>
								<th class="text-center">Change done by</th>
								<th class="text-center">Observation</th>
								<th class="text-center">Date & Time</th>
								<th class="text-center">Time In</th>
								<th class="text-center">Time Out</th>
								<th class="text-center">Balance</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($info as $lista):
								echo "<tr>";
								echo "<td class='text-center'><b>" . $lista['employee'] . "</b>";
								echo "<td class='text-center'>" . $lista['period'] . "</td>";
								echo "<td class='text-center'>" . $lista['done_by'] . "</td>";
								echo "<td>" . $lista['observation'] . "</td>";
								echo "<td class='text-center'>" . $lista['date_issue'] . "</td>";
								echo "<td class='text-right'>" . $lista['time_in'] . "</td>";
								echo "<td class='text-right'>" . $lista['time_out'] . "</td>";
								echo "<td class='text-right'><b>" . $lista['balance'] . "</b>";
								echo "</tr>";
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
		"ordering": false
	});
});
</script>