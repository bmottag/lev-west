<script type="text/javascript" src="<?php echo base_url("assets/js/validate/jobs/workers_training.js"); ?>"></script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-success">
				<div class="panel-heading">
					<a class="btn btn-success btn-xs" href=" <?php echo base_url().'jobs/erp/' . $idJob; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-life-saver"></i> <strong>ERP - TRAINING - ADD WORKERS</strong>
				</div>
				<div class="panel-body">

					<form  name="form" id="form" class="form-horizontal" method="post" >
						<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $idJob; ?>"/>
								
															
                        <table class="table table-striped table-hover table-condensed table-bordered">
                            <tr class="success">
                                <td ><p class="text-center"><strong>Check</strong></p></td>
                                <td ><p class="text-center"><strong>Worker</strong></p></td>
                            </tr>
                            <?php
                            $ci = &get_instance();
                            $ci->load->model("jobs_model");
                            foreach ($workersList as $lista):
							
                                $found = $ci->jobs_model->get_erp_training_byIdworker_byIdJob($idJob, $lista['id_user']);
								
                                echo "<tr>";
                                echo "<td>";
                                $data = array(
                                    'name' => 'workers[]',
                                    'id' => 'workers',
                                    'value' => $lista['id_user'],
                                    'checked' => $found,
                                    'style' => 'margin:10px'
                                );
                                echo form_checkbox($data);
                                echo "</td>";
								echo "<td>" . $lista["first_name"] . ' ' . $lista["last_name"] . "</td>";
                                echo "</tr>";
                            endforeach
                            ?>
                        </table>					
						<div class="form-group">							
							<div class="row" align="center">
								<div style="width:50%;" align="center">
									<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-primary'>
											Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
									</button>
									 
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row" align="center">
								<div style="width:80%;" align="center">
									<div id="div_load" style="display:none">		
										<div class="progress progress-striped active">
											<div class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
												<span class="sr-only">45% completado</span>
											</div>
										</div>
									</div>
									<div id="div_error" style="display:none">			
										<div class="alert alert-danger"><span class="glyphicon glyphicon-remove" id="span_msj">&nbsp;</span></div>
									</div>
								</div>
							</div>
						</div>						
						
					</form>

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