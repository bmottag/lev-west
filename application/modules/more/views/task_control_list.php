<div id="page-wrapper">
	<br>

	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-success">
				<div class="panel-heading">
					<a class="btn btn-success btn-xs" href=" <?php echo base_url().'jobs'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-bug"></i> <strong>COVID-19 | PSI SUPPLEMENT | TASK ASSESSMENT AND CONTROL</strong>
				</div>
				<div class="panel-body">
				
					<div class="alert alert-success">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
					</div>					
				
					<a class='btn btn-outline btn-success btn-block' href='<?php echo base_url('more/add_task_control/' . $jobInfo[0]['id_job']) ?>'>
							<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Add Task Assessment and Control
					</a>
					
					<br>
				<?php
					if($information){
				?>
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>#</th>
								<th>Company</th>
								<th>Reported by</th>
								<th>Date</th>
								<th>Work location</th>
								<th>Task</th>
								<th>Edit</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($information as $lista):
									echo "<tr>";
									echo "<td class='text-center'>" . $lista['id_job_task_control'] . "</td>";
									echo "<td>" . $lista['company_name'] . "</td>";
									echo "<td>" . $lista['supervisor'] . "</td>";
									echo "<td class='text-center'>" . $lista['date_task_control'] . "</td>";
									echo "<td>" . $lista['work_location'] . "</td>";
									echo "<td>" . $lista['task'] . "</td>";
									
									echo "<td class='text-center'>";									
						?>
									<a class='btn btn-success btn-xs' href='<?php echo base_url('more/add_task_control/' . $lista['fk_id_job'] . '/' . $lista['id_job_task_control'] ) ?>'>
											Edit <span class="glyphicon glyphicon-edit" aria-hidden="true">
									</a>								
						<?php
									echo "</td>";
									echo "</tr>";
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

<!-- Tables -->
<script>
$(document).ready(function() {
	$('#dataTables').DataTable({
		responsive: true,
		 "ordering": false,
		 paging: false,
		"searching": false,
		"info": false
	});
});
</script>