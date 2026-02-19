<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a class="btn btn-primary btn-xs" href=" <?php echo base_url().'enlaces/manuals'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-image"></i> <strong>SETTINGS - MANUAL LINKS</strong>
				</div>
				<div class="panel-body">
				
					<form  name="form_map" id="form_map" class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo base_url("enlaces/do_upload_manual"); ?>">
					<input type="hidden" id="hddId" name="hddId" value="<?php echo $information?$information[0]["id_link"]:""; ?>"/>
				
						<div class="form-group">
							<label class="col-sm-4 control-label" for="link_name">Link name: *</label>
							<div class="col-sm-5">
								<input type="text" id="link_name" name="link_name" class="form-control" value="<?php echo $information?$information[0]["link_name"]:""; ?>" placeholder="Link name" required >
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="order">Order: *</label>
							<div class="col-sm-5">
								<select name="order" id="order" class="form-control" required>
									<option value='' >Select...</option>
									<?php for ($i = 1; $i <= 20; $i++) { ?>
										<option value='<?php echo $i; ?>' <?php if ($information && $i == $information[0]["order"]) { echo 'selected="selected"'; } ?> ><?php echo $i; ?></option>
									<?php } ?>									
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="link_state">State: *</label>
							<div class="col-sm-5">
								<select name="link_state" id="link_state" class="form-control" required>
									<option value=''>Select...</option>
									<option value=1 <?php if($information[0]["link_state"] == 1) { echo "selected"; }  ?>>Active</option>
									<option value=2 <?php if($information[0]["link_state"] == 2) { echo "selected"; }  ?>>Inactive</option>
								</select>
							</div>
						</div>
				
						<div class="col-lg-6">				
							<div class="form-group">					
								<label class="col-sm-5 control-label" for="hddTask">Attach manual</label>
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
						<div class="alert alert-danger">
								<strong>Note :</strong><br>
								Allowed format: pdf<br>
								Maximum size: 3000 KB
								
						</div>
					</div>

					
					<!-- /.row (nested) -->
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