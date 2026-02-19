<div id="page-wrapper">

	<br>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-violeta">
                <div class="panel-heading">
					<a class="btn btn-violeta btn-xs" href=" <?php echo base_url($dashboardURL); ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go to Dashboard </a> 
                    <i class="fa fa-fire fa-fw"></i>  <b>PAYSTUBS FORM SEARCH</b>
                </div>
                <div class="panel-body">
					<div class="alert alert-violeta">
						<strong>Note:</strong> 
						Select the period to search your records.
					</div>

					<form  name="form" id="form" role="form" method="post" class="form-horizontal" >

						<div class="form-group">
							<div class="col-sm-3">
								<label for="from">Employee: <small>(This field is NOT required.)</small></label>
								<select name="employee" id="employee" class="form-control js-example-basic-single" >
									<option value=''>Select...</option>
									<?php for ($i = 0; $i < count($workersList); $i++) { ?>
										<option value="<?php echo $workersList[$i]["id_user"]; ?>" <?php if($_POST && $_POST["employee"] == $workersList[$i]["id_user"]) { echo "selected"; }  ?>><?php echo $workersList[$i]["first_name"] . ' ' . $workersList[$i]["last_name"]; ?></option>	
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-2">
								<label for="year">Year: *</label>
								<select name="year" id="year" class="form-control" required >
									<option value=''>Select...</option>
									<option value="2022" <?php if($_POST && $_POST["year"] == "2022") { echo "selected"; }  ?>>2022</option>	
								</select>
							</div>

							<div class="col-sm-2">
								<br>
								 <button type="submit" class="btn btn-primary" id='btnSubmit' name='btnSubmit'>
								 	<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search 
								 </button>
							</div>
						</div>
								
                    </form>

                    <!--si envia informacion por POST entonecs muestro los datos-->
                    <?php 
                    	if($_POST){ 
							if(!$info){
					?>
								<div class="alert alert-danger">
									No data was found matching your criteria. 
								</div>
					<?php
							}else{
                    ?>
								<table width="100%" class="table table-hover" id="dataTables">
									<thead>
										<tr>
											<th><small>Reviewed by</small></th>
											<th><small>Employee</small></th>
											<th><small>Period</small></th>
											<th class='text-right'><small>Worked Hours</small></th>
											<th class='text-right'><small>Regular Hours</small></th>
											<th class='text-right'><small>Overtime Hours</small></th>
											<th class='text-right'><small>Regular Pay</small></th>
											<th class='text-right'><small>Overtime Pay</small></th>
											<th class='text-right'><small>Vacation Pay</small></th>
											<th class='text-right'><small>EE CPP</small></th>
											<th class='text-right'><small>ER CPP</small></th>
											<th class='text-right'><small>EE EI</small></th>
											<th class='text-right'><small>ER EI</small></th>
											<th class='text-right'><small>TAX</small></th>
											<th class='text-right'><small>GWL Deductions</small></th>
											<th class='text-right'><small>Remittance</small></th>
											<th class='text-right'><small>NET PAY</small></th>
										</tr>
									</thead>
									<tbody>
									<?php
										foreach ($info as $lista):
											echo "<tr>";							
											echo "<td><small>" . $lista['name'] . "<br>" . $lista['paystub_date_issue']  . "</small></td>";
											echo "<td><small><b>" . $lista['employee'];
											echo "<br>Rate: </b>" . $lista['employee_rate_paystub'];
											echo "</small></td>";
											echo "<td><small>";
											echo "<b>Beginning: </b><br>" . $lista["date_start"];
											echo "<br><b>Ending: </b><br>" . $lista["date_finish"];
											echo "</small></td>";
											echo "<td class='text-right'><small>" . $lista['total_worked_hours'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['total_regular_hours'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['total_overtime_hours'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['cost_regular_salary'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['cost_over_time'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['cost_vacation_regular_salary'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['ee_cpp'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['er_cpp'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['ee_ei'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['er_ei'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['tax'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['gwl_deductions'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['remittance'] . "</small></td>";
											echo "<td class='text-right'><small>" . $lista['net_pay'] . "</small></td>";
											echo "</tr>";
										endforeach;
									?>
									</tbody>
								</table>
					<?php }} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$('.js-example-basic-single').select2({
		width: '100%'
	});
});
</script>