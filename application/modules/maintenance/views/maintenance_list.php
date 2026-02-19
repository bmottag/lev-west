<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-success">
				<div class="panel-heading">
					<a class="btn btn-success btn-xs" href="<?php echo base_url().'admin/vehicle/'.$vehicleInfo[0]["type_level_1"] . '/' . $vehicleInfo[0]['inspection_type']; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-flag"></i> <strong>MAINTENANCE LIST</strong>
				</div>
				<div class="panel-body">
				
					<a class='btn btn-success btn-block' href='<?php echo base_url('maintenance/maintenance_form/' . $vehicleInfo[0]["id_vehicle"]) ?>'>
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Maintenance
					</a><br>
					
<?php
$retornoExito = $this->session->flashdata('retornoExito');
if ($retornoExito) {
    ?>
	<div class="col-lg-12">	
		<div class="alert alert-success">
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
	
<?php if($infoRecords){ ?>	
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th class="text-center">Maintenance</th>
								<th class="text-center">More info</th>
								<th class="text-center">Next due maintenance </th>
								<th class="text-center">Stock</th>
								<th class="text-center">Amount</th>
								<th class="text-center">Total</th>
								<th class="text-center">Estado</th>
							</tr>
						</thead>
						<tbody>							
						<?php 
							foreach ($infoRecords as $lista):
									
								$class = "";
								if($lista['maintenance_state'] == 1){
									$class = "success";
								}
								
								echo "<tr class='" . $class . "' >";
								
								echo "<td>";
								echo "<strong>Type:<br></strong>". $lista['maintenance_type'];
								echo "<br><strong>Description:<br></strong>". $lista['maintenance_description'];
								echo "<br>";
								if($lista['maintenance_state'] == 1){
								?>
<a href="<?php echo base_url().'maintenance/maintenance_form/' . $vehicleInfo[0]["id_vehicle"] . '/' . $lista["id_maintenance"]; ?>" class="btn btn-danger btn-xs">
<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit <span class="badge">Active</span>
</a>
								<?php
								}else{
								?>
<a href="#" class="btn btn-danger btn-xs" disabled>
<span class="badge">Inactive</span>
</a>
								<?php
								}
								
								echo "</td>";
								
								echo "<td>";
								echo "<strong>Date of issue:<br></strong>". $lista['date_maintenance'];
								echo "<br><strong>Done by:<br></strong>". $lista['done_by'];
								echo "<br><strong>Revised by:<br></strong>". $lista['name'];								
								echo "</td>";
								
								echo "<td class='text-right'>";
								if($lista['next_hours_maintenance']){
									echo "<strong>Hours/Km:<br></strong>". number_format((float)$lista['next_hours_maintenance']);
								}
								
								if($lista['next_hours_maintenance'] && $lista['next_date_maintenance']){
									echo "<br>";
								}
								if($lista['next_date_maintenance']){
									echo "<strong>Date:<br></strong>". $lista['next_date_maintenance'];
								}
								echo "</td>";
																
								if(IS_NULL($lista['fk_id_stock']) || $lista['fk_id_stock'] == 0){
									echo "<td class='text-center'>";
									echo "-";
									echo "</td>";
									$subTotal = 0;
								}else{
									echo "<td>";
									echo "<strong>Description:<br></strong>" . $lista['stock_description'];
									
									$stock_price = '$' . number_format($lista['stock_price'], 2);
									echo "<br><strong>Price by unit:<br></strong>". $stock_price;
									echo "<br><strong>Quantity:<br></strong>". $lista['stock_quantity'];
									echo "</td>";
									
									$subTotal = $lista['stock_quantity'] * $lista['stock_price'];
								}
																
								$amount = '$' . number_format($lista['amount'], 2);
								$total = $lista['amount'] + $subTotal;
								$total = '$' . number_format($total, 2);
								
								echo "<td  class='text-right'>" . $amount . "</td>";
								echo "<td  class='text-right'>" . $total . "</td>";
								echo "<td  class='text-right'>" . $lista['maintenance_state'] . "</td>";							
								
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
<!-- /#page-wrapper -->

<!-- Tables -->
<script>
$(document).ready(function() {
    $('#dataTables').DataTable( {
		"order": [[ 6, "asc" ]],
		paging: false,
		"columnDefs": [
            {
                "targets": [ 6 ],
                "visible": false,
                "searchable": false
            }
        ]
    } );
} );
</script>