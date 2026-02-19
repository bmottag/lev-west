<script type="text/javascript" src="<?php echo base_url("assets/js/validate/jobs/hazards.js"); ?>"></script>

<div id="page-wrapper">
	<br>

<form  name="form" id="form" class="form-horizontal" method="post" >
	<input type="hidden" id="hddId" name="hddId" value="<?php echo $idJob; ?>"/>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<a class="btn btn-danger btn-xs" href=" <?php echo base_url().'jobs/hazards/' . $idJob; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a>
					<i class="fa fa-life-saver"></i> <strong>HAZARDS</strong>
				</div>
				<!-- .panel-heading -->
				<div class="panel-body">
					<div class="panel-group" id="accordion">	

						<div class="alert alert-danger">
							<strong>Select </strong> all the hazards that apply.

						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="observation">Observation: </label>
							<div class="col-sm-5">
							<textarea id="observation" name="observation" placeholder="Observation"  class="form-control" rows="2"></textarea>
							</div>
						</div>						

            			<p class="text-right text-danger">
            				<small>* For line break use: 
							<button type="button" class="btn btn-danger btn-xs"><strong>&lt;br&gt;</strong></i></button>
            				, if you want to use bold use 
            				<button type="button" class="btn btn-danger btn-xs"><strong>&lt;strong&gt;</strong></i></button>
            				at the beginning and 
            				<button type="button" class="btn btn-danger btn-xs"><strong>&lt;/strong&gt;</strong></i></button>
            				at the end. </small>
            			</p>
					
					<?php
						foreach ($activityList as $lista):
					?>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $lista['id_hazard_activity']; ?>">
									<?php echo $lista['hazard_activity']; ?>
									</a>
								</h4>
							</div>
							<div id="collapse<?php echo $lista['id_hazard_activity']; ?>" class="panel-collapse collapse">
								<div class="panel-body">

					<?php 
						$hazardList = $this->jobs_model->get_hazard_list($lista['id_hazard_activity']);//hazards list
					?>								
                        <table class="table table-striped table-hover table-condensed table-bordered">
                            <tr class="info">
                                <td ><p class="text-center"><strong>Check</strong></p></td>
                                <td ><p class="text-center"><strong>Activity</strong></p></td>
								<td ><p class="text-center"><strong>Hazard</strong></p></td>
								<td ><p class="text-center"><strong>Solution</strong></p></td>
                            </tr>
                            <?php
                            $ci = &get_instance();
                            $ci->load->model("jobs_model");
							
                            foreach ($hazardList as $lista):
							
								$found = $ci->jobs_model->get_job_hazards_by($idJob, $lista['id_hazard']);
			
                                echo "<tr>";
                                echo "<td>";
                                $data = array(
                                    'name' => 'hazards[]',
                                    'id' => 'hazards',
                                    'value' => $lista['id_hazard'],
									'checked' => $found,
                                    'style' => 'margin:10px'
                                );
                                echo form_checkbox($data);
                                echo "</td>";
								echo "<td>" . $lista["hazard_activity"] . "</td>";
								echo "<td>" . $lista["hazard_description"] . "</td>";
								echo "<td>" . $lista["solution"] . "</td>";
                                echo "</tr>";
                            endforeach
                            ?>
                        </table>
								
								</div>
							</div>
						</div>

					<?php
						endforeach;
					?>
					<br>
						<div class="form-group">
							<div class="row" align="center">
								<div style="width:50%;" align="center">
									 <input type="button" id="btnSubmit" name="btnSubmit" value="Save" class="btn btn-primary"/>
								</div>
							</div>
						</div>
					
					</div>
				</div>
				<!-- .panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
</form>
	<!-- /.row -->
</div>
<!-- /#page-wrapper -->