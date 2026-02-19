<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a class="btn btn-primary btn-xs" href=" <?php echo base_url($dashboardURL); ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go to Dashboard </a> 
					<i class="fa fa-list fa-fw"></i> <b>PROJECTS</b>
				</div>
				<div class="panel-body">

				<?php
					if($info){
				?>				
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th class="text-center">Job Code/Name</th>
								<th class="text-center">Actions</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($info as $lista):
								$userRol = $this->session->rol;
								echo "<tr>";
								echo "<td>" . $lista['job_description'] . "</td>";
								echo "<td class='text-center'>";
								if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_MANAGER || $userRol == ID_ROL_SAFETY || $userRol == ID_ROL_WORKORDER || $userRol == ID_ROL_SUPERVISOR || $userRol == ID_ROL_ENGINEER){ 
						?>
								<a class='btn btn-primary btn-xs' href='<?php echo base_url('programming/index/' . $lista['id_job']) ?>'>
									Planning <span class="fa fa-book" aria-hidden="true">
								</a>
						<?php
								}
								if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ENGINEER){ 
						?>
									<a class='btn btn-dark btn-xs' href='<?php echo base_url('jobs/job_detail/' . $lista['id_job']) ?>'>
										LIC <span class="fa fa-gears fa-fw" aria-hidden="true">
									</a>
						<?php
								}
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
		"pageLength": 100
	});
});
</script>