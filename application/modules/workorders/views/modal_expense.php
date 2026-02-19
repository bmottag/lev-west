<script type="text/javascript" src="<?php echo base_url("assets/js/validate/workorder/expense.js"); ?>"></script>

<div class="modal-body">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<form  name="formExpense" id="formExpense" role="form" method="post" >
		<input type="hidden" id="hddidWorkorder" name="hddidWorkorder" value="<?php echo $idWorkorder; ?>"/>
		<input type="hidden" id="hddidJob" name="hddidJob" value="<?php echo $idJob; ?>"/>

		<?php
			if(!$chapterList){
		?>
				<div class="row">
					<div class="col-lg-12">	
						<div class="alert alert-danger ">
							<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							There are no items for this Job Code/Name.
						</div>
					</div>
				</div>
		<?php
			}elseif($sumPercentage < 100 ){
		?>
				<div class="row">
					<div class="col-lg-12">	
						<div class="alert alert-danger ">
							<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							The percentage of each item is not correctly calculated, contact the administrator.
						</div>
					</div>
				</div>
		<?php
			}else{
		?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-list"></i> ADD Items from Job Details 
					</div>

					<div class="panel-body small">
						<div class="panel-group" id="accordion">	
						<?php
							foreach ($chapterList as $lista):
						?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $lista['chapter_number']; ?>">
										<?php echo $lista['chapter_name']; ?>
										</a>
									</h4>
								</div>
								<div id="collapse<?php echo $lista['chapter_number']; ?>" class="panel-collapse collapse">
									<div class="panel-body">
									<?php 
										$ci = &get_instance();
										$ci->load->model("general_model");
										$arrParam = array("idJob" => $idJob, "chapterNumber" => $lista['chapter_number']);
										$jobDetails = $this->general_model->get_job_detail($arrParam);
									?>								
									<table class="table table-striped table-hover table-condensed table-bordered">
										<thead>
											<tr class="info">
												<th class="text-center">Check</th>
												<th class="text-center">Item</th>
												<th class="text-center">Description</th>
												<th class="text-center">Balance</th>
											</tr>
										</thead>
										<?php
										foreach ($jobDetails as $value):
											$balance = $value['extended_amount'] - $value['expenses'];
											$arrParam = array('idWorkOrder' => $idWorkorder, "idJobDetail" => $value['id_job_detail']);
											$found = $ci->general_model->get_workorder_expense($arrParam);
						
											echo "<tr>";
											echo "<td>";
											$data = array(
												'name' => 'expense[]',
												'id' => 'expense',
												'value' => $value['id_job_detail'],
												'checked' => $found,
												'style' => 'margin:10px'
											);
											echo form_checkbox($data);
											echo "</td>";
											echo "<td>" . $value["chapter_number"] . "." . $value["item"] . "</td>";
											echo "<td class='text-left'>" . $value["description"] . "</td>";
											echo "<td class='text-right'>$ " . number_format($balance,2) . "</td>";
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
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="row" align="center">
				<div style="width:50%;" align="center">
					<button type="button" id="btnSubmitExpense" name="btnSubmitExpense" class="btn btn-primary" >
						Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
					</button> 
				</div>
			</div>
		</div>
					
		<div class="form-group">
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
		<?php } ?>
	</form>
</div>