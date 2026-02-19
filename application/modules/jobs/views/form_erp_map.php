<div id="page-wrapper">
	<br>

	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-success">
				<div class="panel-heading">
					<a class="btn btn-success btn-xs" href=" <?php echo base_url().'jobs'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-fire-extinguisher "></i> <strong>ERP - EMERGENCY RESPONSE PLAN</strong>
				</div>
				<div class="panel-body">
				
					<ul class="nav nav-pills">
						<li ><a href="<?php echo base_url("jobs/erp/" . $jobInfo[0]["id_job"]); ?>">ERP - INFO</a>
						</li>
						<li ><a href="<?php echo base_url("jobs/erp_personnel/" . $jobInfo[0]["id_job"]); ?>">ERP - EVACUATION PERSONNEL </a>
						</li>
						<li class='active'><a href="<?php echo base_url("jobs/erp_map/" . $jobInfo[0]["id_job"]); ?>">ERP - EVACUATION MAP</a>
						</li>
					</ul>
					
					<div class="alert alert-success">
						<span class="fa fa-briefcase" aria-hidden="true"></span>
						<strong>Job Code/Name: </strong><?php echo $jobInfo[0]['job_description']; ?>
						<br><span class='fa fa-clock-o' aria-hidden='true'></span> <strong>Date: </strong>
						<?php 
						if($information){
								echo $information[0]["date_erp"]; 
								
								echo "<br><span class='fa fa-cloud-download' aria-hidden='true'></span> <strong>Download ERP: </strong>";
						?>
<a href='<?php echo base_url('jobs/generaERPPDF/' . $information[0]["id_erp"] ); ?>' target="_blank">PDF <img src='<?php echo base_url_images('pdf.png'); ?>' ></a>
						<?php 
						}else{
								echo date("Y-m-d");
						}
						?>
					</div>
					

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
									
				</div>
			</div>
		</div>
	</div>		

	<div class="row">			
		<div class="col-lg-12">				
			<div class="panel panel-success">
				<div class="panel-heading">
					<strong>EVACUATION MAP</strong>
				</div>
				<div class="panel-body">
				
	<?php
	if($information[0]["evacuation_map"]){ ?>
		<div class="col-lg-3">	
			<div class="form-group">
				<div class="row" align="center">
					<div style="width:70%;" align="center">
						<h3><a href="<?php echo base_url($information[0]["evacuation_map"]); ?>" target="_blank" /> - View map -</a></h3>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

		<form  name="form_map" id="form_map" class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo base_url("jobs/do_upload"); ?>">
		<input type="hidden" id="hddIdJobMap" name="hddIdJobMap" value="<?php echo $jobInfo[0]["id_job"]; ?>"/>
				
				<div class="col-lg-6">				
					<div class="form-group">					
						<label class="col-sm-5 control-label" for="hddTask">Attach evacuation map</label>
						<div class="col-sm-5">
							 <input type="file" name="userfile" />
						</div>
					</div>
				</div>
					
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row" align="center">
							<div style="width:50%;" align="center">
								<button type="submit" id="btnSubmitMap" name="btnSubmitMap" class='btn btn-primary'>
										Upload map <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
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
					<div class="alert alert-danger">
							<strong>Note :</strong><br>
							Allowed format: gif - jpg - png - pdf<br>
							Maximum size: 3000 KB<br>
							Maximum width: 3200 pixels<br>
							Maximum height: 2400 pixels<br>
							<strong>Don´t forget the following items :</strong><br>
1.Emergency exits<br>
2.Primary and secondary evacuation routes<br>
3.Locations of fire extinguishers<br>
4.Fire alarm pull stations’ location<br>
5.Assembly points<br>
6.Medical center<br>
7.First Aid locations
							
					</div>
		</div>

					
				</div>
			</div>
		</div>
	</div>



</div>
<!-- /#page-wrapper -->