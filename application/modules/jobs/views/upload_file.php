
<script type="text/javascript" src="<?php echo base_url("assets/js/validate/jobs/upload_file.js"); ?>"></script>

<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a class="btn btn-primary btn-xs" href=" <?php echo base_url().'jobs'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-upload"></i> <strong>UPLOAD JOB DETAIL</strong>
				</div>
				<div class="panel-body">

					<div class="alert alert-info">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
					</div>
					<?php
					if($jobInfo[0]['flag_expenses'] == 1){
					?>
						<div class="col-lg-12">
							<div class="alert alert-danger ">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								Currently, there are entries in the WO (Work Orders) detailing various expenses related to this Job Code. Unfortunately, there is an inability to upload additional information at this time.
							</div>
						</div>
					<?php } ?>
					
					<?php 
						if (!empty($success)) {
							echo '<div class="col-lg-12">';
							echo '<div class="alert text-center alert-success"><label>' . $success . '</label></div>';
							echo '</div>';
						} 
					?>

					<form  name="formCargue" id="formCargue" class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo base_url("jobs/do_upload_job_info"); ?>">
						<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $jobInfo[0]["id_job"]; ?>"/>
						<input type="hidden" id="hddFlagExpenses" name="hddFlagExpenses" value="<?php echo $jobInfo[0]["flag_expenses"]; ?>"/>
						<input type="hidden" id="hddFlagUploadDetails" name="hddFlagUploadDetails" value="<?php echo $jobInfo[0]["flag_upload_details"]; ?>"/>
							
						<div class="col-lg-6">				
							<div class="form-group">					
								<label class="col-sm-5 control-label" for="hddTask">Attach Job Detail File:</label>
								<div class="col-sm-5">
									<input type="file" name="userfile" />
								</div>
							</div>
						</div>
					
						<div class="col-lg-6">				
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:50%;" align="center">
										<button type="submit" id="btnSubmit" name="btnSubmit" class='btn btn-primary'>
												Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>

					<?php if($error){ ?>
					<div class="col-lg-12">
						<div class="alert alert-danger">
						<?php 
							echo "<strong>Error :</strong>";
							pr($error); 
						?><!--$ERROR MUESTRA LOS ERRORES QUE PUEDAN HABER AL SUBIR LA IMAGEN-->
						</div>
					</div>
					<?php } ?>
					
					<div class="col-lg-12">
						<div class="alert alert-info">
								<strong>Note :</strong><br>
								Allowed format: CSV<br>
								Maximum size: 4096 KB
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>