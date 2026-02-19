<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/validate/workorder/search_v2.js"); ?>"></script>

<div id="page-wrapper">

	<br>	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<i class="fa fa-money"></i> <strong>WORK ORDERS - SEARCH</strong>
				</div>
				<div class="panel-body">
					<div class="alert alert-info">
						<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
						Select at least one of the following fields
					</div>
					<form  name="form" id="form" role="form" method="post" class="form-horizontal" >

						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">Job Code/Name </label>
								<select name="jobName" id="jobName" class="form-control js-example-basic-single" >
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($jobList); $i++) { ?>
										<option value="<?php echo $jobList[$i]["id_job"]; ?>" ><?php echo $jobList[$i]["job_description"]; ?></option>	
									<?php } ?>
								</select>
							</div>
							
							<div class="col-sm-5">
								<label for="from">Work order number </label>
								<input type="text" id="workOrderNumber" name="workOrderNumber" class="form-control" placeholder="W.O. #" >
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">Work order number range </label>
								<input type="text" id="workOrderNumberFrom" name="workOrderNumberFrom" class="form-control" placeholder="W.O. # From" >
							</div>
							
							<div class="col-sm-5">
								<label for="from">To </label>
								<input type="text" id="workOrderNumberTo" name="workOrderNumberTo" class="form-control" placeholder="W.O. # To" >
							</div>
						</div>
						
<script>
$( function() {
var dateFormat = "mm/dd/yy",
from = $( "#from" )
.datepicker({
changeMonth: true,
numberOfMonths: 2
})
.on( "change", function() {
to.datepicker( "option", "minDate", getDate( this ) );
}),
to = $( "#to" ).datepicker({
changeMonth: true,
numberOfMonths: 2
})
.on( "change", function() {
from.datepicker( "option", "maxDate", getDate( this ) );
});

function getDate( element ) {
var date;
try {
date = $.datepicker.parseDate( dateFormat, element.value );
} catch( error ) {
date = null;
}

return date;
}
});
</script>
						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-1">
								<label for="from">From Date</label>
								<input type="text" id="from" name="from" class="form-control" placeholder="From" >
							</div>
							<div class="col-sm-5">
								<label for="to">To Date</label>
								<input type="text" id="to" name="to" class="form-control" placeholder="To" >
							</div>
						</div>

<div class="row"></div><br>
						<div class="form-group">
							<div class="row" align="center">
								<div style="width80%;" align="center">
									
								 <button type="submit" class="btn btn-primary" id='btnSubmit' name='btnSubmit'><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Go </button>
									
								</div>
							</div>
						</div>
						
					</form>

				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
		
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-bell fa-fw"></i> Notifications Panel - Work Orders <b><?php echo date("Y"); ?></b>
				</div>
				<!-- /.panel-heading -->
				<div class="panel-body">
					<div class="list-group">
						<a href="<?php echo base_url("workorders/wo_by_state/0/" . date("Y")); ?>" class="list-group-item">
							<p class="text-danger"><i class="fa fa-thumb-tack fa-fw"></i><strong> On Field</strong>
								<span class="pull-right text-muted small"><em><?php echo $noOnfield; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url("workorders/wo_by_state/1/" . date("Y")); ?>" class="list-group-item">
							<p class="text-warning"><i class="fa fa-refresh fa-fw"></i><strong> In Progress</strong>
								<span class="pull-right text-muted small"><em><?php echo $noProgress; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url("workorders/wo_by_state/2/" . date("Y")); ?>" class="list-group-item">
							<p class="text-primary"><i class="fa fa-check fa-fw"></i><strong> Revised</strong>
								<span class="pull-right text-muted small"><em><?php echo $noRevised; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url("workorders/wo_by_state/3/" . date("Y")); ?>" class="list-group-item">
							<p class="text-success"><i class="fa fa-envelope-o  fa-fw"></i><strong> Send to the Client</strong>
								<span class="pull-right text-muted small"><em><?php echo $noSend; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url("workorders/wo_by_state/4/" . date("Y")); ?>" class="list-group-item">
							<p class="text-danger"><i class="fa fa-power-off fa-fw"></i><strong> Closed</strong>
								<span class="pull-right text-muted small"><em><?php echo $noClosed; ?></em>
								</span>
							</p>
						</a>

					</div>
					<!-- /.list-group -->

				</div>
				<!-- /.panel-body -->
			</div>
		</div>
	</div>

	<div class="row">

		<?php
			for ($x = 1; $x <= 3; $x++) {
				$year = date("Y") - $x; 

				//consultar datos
				$arrParam = array(
					'state' => 0,
					'year' => $year
				);
				$noOnfieldLastYear = $this->workorders_model->countWorkorders($arrParam);//cuenta registros de Workorders
				$arrParam['state'] = 1;
				$noProgressLastYear = $this->workorders_model->countWorkorders($arrParam);//cuenta registros de Workorders
				$arrParam['state'] = 2;
				$noRevisedLastYear = $this->workorders_model->countWorkorders($arrParam);//cuenta registros de Workorders
				$arrParam['state'] = 3;
				$noSendLastYear = $this->workorders_model->countWorkorders($arrParam);//cuenta registros de Workorders
				$arrParam['state'] = 4;
				$noClosedLastYear = $this->workorders_model->countWorkorders($arrParam);//cuenta registros de Workorders
		?>
		<div class="col-lg-4">
			<div class="panel panel-violeta">
				<div class="panel-heading">
					<i class="fa fa-bell fa-fw"></i> Notifications Panel - Work Orders <b><?php echo $year; ?></b>
				</div>
				<!-- /.panel-heading -->
				<div class="panel-body">
					<div class="list-group">
						<a href="<?php echo base_url('workorders/wo_by_state/0/' . $year); ?>" class="list-group-item">
							<p class="text-danger"><i class="fa fa-thumb-tack fa-fw"></i><strong> On Field</strong>
								<span class="pull-right text-muted small"><em><?php echo $noOnfieldLastYear; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url('workorders/wo_by_state/1/' . $year); ?>" class="list-group-item">
							<p class="text-warning"><i class="fa fa-refresh fa-fw"></i><strong> In Progress</strong>
								<span class="pull-right text-muted small"><em><?php echo $noProgressLastYear; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url('workorders/wo_by_state/2/' . $year); ?>" class="list-group-item">
							<p class="text-primary"><i class="fa fa-check fa-fw"></i><strong> Revised</strong>
								<span class="pull-right text-muted small"><em><?php echo $noRevisedLastYear; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url('workorders/wo_by_state/3/' . $year); ?>" class="list-group-item">
							<p class="text-success"><i class="fa fa-envelope-o  fa-fw"></i><strong> Send to the Client</strong>
								<span class="pull-right text-muted small"><em><?php echo $noSendLastYear; ?></em>
								</span>
							</p>
						</a>
						<a href="<?php echo base_url('workorders/wo_by_state/4/' . $year); ?>" class="list-group-item">
							<p class="text-danger"><i class="fa fa-power-off fa-fw"></i><strong> Closed</strong>
								<span class="pull-right text-muted small"><em><?php echo $noClosedLastYear; ?></em>
								</span>
							</p>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
			}
		?>
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