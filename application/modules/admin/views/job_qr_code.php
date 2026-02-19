<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> SETTINGS - JOB CODE/NAME - TIMESHEET QR CODE
					</h4>
				</div>
			</div>
		</div>		
	</div>
	
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a class="btn btn-success btn-xs" href=" <?php echo base_url().'admin/JOB/1'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-briefcase"></i> JOB CODE/NAME - TIMESHEET QR CODE
				</div>
				<div class="panel-body">
						
					<?php 
                        if($jobInfo[0]["qr_code_timesheet"]){
                            $rutaImagen = base_url($jobInfo[0]["qr_code_timesheet"]);
                        }else{
                            $rutaImagen = base_url("images/qrcode/job_timesheet/" . $idJob . "_qr_code.png");
                        }
                    ?>
						<div class="form-group">
							<div class="row" align="center">
								<img src="<?php echo $rutaImagen; ?>" class="img-rounded" alt="QR CODE" /> <br>
                                <?php echo $jobInfo[0]['job_description']; ?>
							</div>
						</div>
				</div>
			</div>
		</div>
		
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-automobile"></i> <strong>JOB CODE/NAME INFORMATION</strong>
				</div>
				<div class="panel-body">
					<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
				</div>
			</div>
		</div>		
	</div>
</div>