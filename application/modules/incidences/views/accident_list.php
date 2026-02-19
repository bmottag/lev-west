<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-ambulance fa-fw"></i>	INCIDENCES
					</h4>
				</div>
			</div>
		</div>
		<!-- /.col-lg-12 -->				
	</div>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<i class="fa fa-ambulance"></i> ACCIDENT REPORT
				</div>
				<div class="panel-body">
					<a class='btn btn-outline btn-danger btn-block' href='<?php echo base_url('incidences/add_accident/') ?>'>
							<span class="glyphicon glyphicon-edit" aria-hidden="true"> </span>  Add an Accident Report
					</a>
					
					<br>
				<?php
					if($accidentInfo){
				?>
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>#</th>
								<th>Reported by</th>
								<th>Unit</th>
								<th>Brief explanation</th>
								<th>Date of accident</th>
								<th>Edit</th>
								<th>Download</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($accidentInfo as $lista):
									echo "<tr>";
									echo "<td class='text-center'>" . $lista['id_accident'] . "</td>";
									echo "<td>" . $lista['name'] . "</td>";
									echo "<td>" . $lista['unit'] . "</td>";
									echo "<td>" . $lista['brief_explanation'] . "</td>";
									echo "<td class='text-center'>" . $lista['date_accident'] . "</td>";
									echo "<td class='text-center'>";									
									
									if($lista['state_accident']==1){
						?>
									<a class='btn btn-success btn-xs' href='<?php echo base_url('incidences/add_accident/' . $lista['id_accident']) ?>'>
											Edit <span class="glyphicon glyphicon-edit" aria-hidden="true">
									</a>								
						<?php
									}else{
						?>
									<button type="button" class="btn btn-danger btn-xs" >
										Close 
									</button>
						<?php			
									}
									echo "</td>";
									echo "<td class='text-center'>";
						?>
<a href='<?php echo base_url('incidences/generaPDF/' . $lista['id_accident'] . '/' . 3 ); ?>' target="_blank">PDF <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>	
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
    </script>