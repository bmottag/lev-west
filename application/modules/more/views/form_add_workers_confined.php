<script type="text/javascript" src="<?php echo base_url("assets/js/validate/more/workers_confined.js"); ?>"></script>

<div id="page-wrapper">
	<br>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-life-saver"></i> CONFINED SPACE ENTRY PERMIT - ADD WORKERS
				</div>
				<div class="panel-body">

					<form  name="form" id="form" class="form-horizontal" method="post" >
						<input type="hidden" id="hddIdConfined" name="hddIdConfined" value="<?php echo $idConfined; ?>"/>
						<input type="hidden" id="hddIdJob" name="hddIdJob" value="<?php echo $idJob; ?>"/>
						<input type="hidden" id="hddWOS" name="hddWOS" value="<?php echo $wos; ?>"/>
															
                        <table class="table table-striped table-hover table-condensed table-bordered">
                            <tr class="info">
                                <td ><p class="text-center"><strong>Check</strong></p></td>
                                <td ><p class="text-center"><strong>Worker</strong></p></td>
                            </tr>
                            <?php
                            $ci = &get_instance();
                            $ci->load->model("more_model");
                            foreach ($workersList as $lista):
							
                                $found = $ci->more_model->get_confined_byIdworker_byIdConfined($idConfined, $lista['id_user'], $wos);
								
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