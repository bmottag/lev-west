<div id="page-wrapper">
	<br>

	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-purpura">
				<div class="panel-heading">
					<a class="btn btn-purpura btn-xs" href=" <?php echo base_url().'jobs'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="glyphicon glyphicon-screenshot"></i> <strong>ESI - ENVIROMENTAL SITE INSPECTION</strong>
				</div>
				<div class="panel-body">
				
					<div class="alert alert-purpura">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
					</div>					
			
					<a class='btn btn-outline btn-purpura btn-block' href='<?php echo base_url('more/add_environmental/' . $jobInfo[0]['id_job']) ?>'>
							<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Add an Enviromental Site Inspection
					</a>
					
					<br>
<?php
	if($information){
?>
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>Reported by</th>
								<th>Date review</th>
								<th>Site inspector</th>
								<th>Manager</th>
								<th>Download</th>
								<th>Review</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($information as $lista):
									echo "<tr>";
									echo "<td>" . $lista['name'] . "</td>";
									echo "<td class='text-center'>" . $lista['date_environmental'] . "</td>";
									echo "<td>";
									echo $lista['inspector']?$lista['inspector']:"<p class='text-danger text-left'>This field is missing.</p>";									
									echo "</td>";
									echo "<td>";
									echo $lista['manager']?$lista['manager']:"<p class='text-danger text-left'>This field is missing.</p>	";
									echo "</td>";
									
									echo "<td class='text-center'>";									
									if($lista['manager']){
						?>
									<a href='<?php echo base_url('more/generaEnvironmentalPDF/' . $jobInfo[0]['id_job'] ); ?>' target="_blank"> <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php
									}else{ 
										echo "-";
									}
									echo "</td>";									
									
									echo "<td class='text-center'>";									
						?>
									<a class='btn btn-success btn-xs' href='<?php echo base_url('more/add_environmental/' . $lista['fk_id_job'] . '/' . $lista['id_job_environmental'] ) ?>'>
											Review <span class="glyphicon glyphicon-edit" aria-hidden="true">
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