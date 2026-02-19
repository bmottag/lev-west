<script type="text/javascript" src="<?php echo base_url("assets/js/validate/jobs/workers_excavation.js"); ?>"></script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<a class="btn btn-danger btn-xs" href=" <?php echo base_url('jobs/upload_excavation_personnel/' . $idExcavation); ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-pied-piper-alt"></i> <strong>EXCAVATION AND TRENCHING PLAN - ADD WORKERS</strong>
				</div>
				<div class="panel-body">

					<form  name="form" id="form" class="form-horizontal" method="post" >
						<input type="hidden" id="hddIdExcavation" name="hddIdExcavation" value="<?php echo $idExcavation; ?>"/>								
															
                    	<table class="table table-hover">
                    		<thead>
	                            <tr class="info">
	                                <td class="text-center" width="10%"><strong>Check</strong></td>
	                                <td ><strong>Worker</strong></td>
	                            </tr>
							</thead>
						
                            <?php
                            $ci = &get_instance();
                            $ci->load->model("jobs_model");
                            foreach ($workersList as $lista):
							
                                $found = $ci->jobs_model->get_excavation_byIdworker_byIdExcavation($idExcavation, $lista['id_user']);
								
                                echo "<tr>";
                                echo "<td class='text-center'>";
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

						<div class="form-group">
							<div class="row" align="center">
								<div style="width:100%;" align="center">
									<button type="button" id="btnSubmit" name="btnSubmit" class='btn btn-info'>
											Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
									</button>						
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