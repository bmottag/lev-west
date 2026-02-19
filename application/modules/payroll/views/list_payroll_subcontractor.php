<div id="page-wrapper">
	<br>
<?php
$retornoExito = $this->session->flashdata('retornoExito');
if ($retornoExito) {
    ?>
    <div class="row">
		<div class="col-lg-12">	
			<div class="alert alert-success ">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				<?php echo $retornoExito ?>		
			</div>
		</div>
	</div>
    <?php
}

$retornoError = $this->session->flashdata('retornoError');
if ($retornoError) {
    ?>
    <div class="row">
		<div class="col-lg-12">	
			<div class="alert alert-danger ">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<?php echo $retornoError ?>
			</div>
		</div>
	</div>
    <?php
}
?>	
	<?php
		if(!$info){
	?>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<a class="btn btn-violeta btn-xs" href=" <?php echo base_url().'payroll/payrollSearchForm'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
					<i class="fa fa-clock-o fa-fw"></i> <b>PAYROLL REPORT - SUBCONTRACTOR </b>
					<br><b>Period Beginning: </b> <?php echo $infoPeriod[0]["date_start"]; ?>
					<br><b>Period Ending: </b> <?php echo $infoPeriod[0]["date_finish"]; ?>
				</div>

				<div class="panel-body">
					<div class="alert alert-danger">
						No data was found matching your criteria. 
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
		}else{

			foreach ($info as $lista):
				$arrParam = array("idUser" => $lista['fk_id_user']);
				$infoUser = $this->general_model->get_user($arrParam);
				$employeeName = $infoUser[0]["first_name"] . ' ' . $infoUser[0]["last_name"];
				$employeeHourRate = $infoUser[0]["employee_rate"];
				$employeeType = $infoUser[0]["employee_type"];
				$bankTime = $infoUser[0]["bank_time"];
				$bankTimeFlag = "";
				$idPeriod = $infoPeriod[0]["id_period"];
				//buscar por usuario y por periodo las horas de cada semana
				$arrParam = array(
					"idUser" => $lista['fk_id_user'],
					"idPeriod" => $idPeriod,
					"weakNumber" => 1
				);
				$infoPayrollUser1 = $this->general_model->get_task_by_period($arrParam);

				$arrParam = array(
					"idUser" => $lista['fk_id_user'],
					"idPeriod" => $idPeriod,
					"weakNumber" => 2
				);
				$infoPayrollUser2 = $this->general_model->get_task_by_period($arrParam);

				//buscar por perido y por usuario si ya tiene guardado el paystub
				$arrParam = array(
					"idEmployee" => $lista['fk_id_user'],
					"idPeriod" => $idPeriod
				);
				$infoPaystub = $this->general_model->get_paystub_by_period($arrParam);

				//si ya se guardo el paystub entonces tomo esos valores 
				if($infoPaystub){
					$employeeHourRate = $infoPaystub[0]["employee_rate_paystub"];
					$employeeType = $infoPaystub[0]["employee_type_paystub"];
				}

				//buscar el total de valores por año y usuario
				$arrParam = array(
					"idUser" => $lista['fk_id_user'],
					"year" => $infoPeriod[0]['year_period']
				);
				$infoTotalYear = $this->general_model->get_total_yearly($arrParam);

				//si hay registro envio el id del registro
				$idTotalYear = $infoTotalYear?$infoTotalYear[0]['id_total_yearly']:'';

	?>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<div class="row">
					<div class="col-lg-12">
						<a class="btn btn-violeta btn-xs" href=" <?php echo base_url().'payroll/payrollSearchForm'; ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back </a> 
						<i class="fa fa-clock-o fa-fw"></i> <b>PAYROLL REPORT - SUBCONTRACTOR</b>
					</div>
					<div class="col-lg-2 col-lg-offset-2"><b>Period Beginning: </b></div>
					<div class="col-lg-8"><?php echo $infoPeriod[0]["date_start"]; ?></div>	
					<div class="col-lg-2 col-lg-offset-2"><b>Period Ending: </b></div>
					<div class="col-lg-8"><?php echo $infoPeriod[0]["date_finish"]; ?></div>	
				</div>
				</div>

				<div class="panel-body">
					<div class="alert alert-default">
						<h2><i class="fa fa-user"></i> <b>Employee: </b> <?php echo $employeeName; ?>
							<br><small><b>Hour rate: </b>$ <?php echo $employeeHourRate; ?></small>
							<br><small><b>Type: </b><?php echo $type = $employeeType==1?'Field':'Admin'; ?></small>
						</h2>
					</div>
					<table width="100%" class="table table-hover" id="dataTables">
						<thead>
							<tr>
								<th class='text-center'>Date & Time - Start</th>
								<th class='text-center'>Date & Time - Finish</th>
								<th class='text-right'>Worked Hours</th>
								<th class='text-right'>Regular Hours</th>
								<th class='text-right'>Daily Overtime Hours</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							$totalHours1 = 0;
							$totalHours2 = 0;
							$actualRegularHours1 = 0;
							$actualOvertimeHours1 = 0;
							$actualRegularHours2 = 0;
							$actualOvertimeHours2 = 0;
							if($infoPayrollUser1)
							{
								echo "<tr><td class='text-left' colspan='6'>";
								echo "<p class='text-danger'><b>First Weak: " . $infoPayrollUser1[0]["period_weak"] . "</b></p>";
								echo "</td></tr>";
								$totalRegular = 0;
								$totalOvertime = 0;
								$totalRegularWeek = 0;
								$weeklyOvertime = 0;
								foreach ($infoPayrollUser1 as $lista):
									echo "<tr>";							
									echo "<td class='text-center'>" . $lista['start'] . "</td>";
									echo "<td class='text-center'>" . $lista['finish'] . "</td>";
									echo "<td class='text-right'>" . $lista['working_hours'] . "</td>";
									echo "<td class='text-right'>" . $lista['regular_hours'] . "</td>";
									echo "<td class='text-right'>" . $lista['overtime_hours'] . "</td>";
									$totalHours1 = $lista['working_hours'] + $totalHours1;
									$totalRegular = $lista['regular_hours'] + $totalRegular;
									$totalOvertime = $lista['overtime_hours'] + $totalOvertime;
									echo "</tr>";
								endforeach;
									echo "<tr><td></td><td></td>";
									echo "<td class='text-right'><strong>Total weekly hours:<br>" . $totalHours1 . "</strong></td>";
									echo "<td class='text-right'><strong>Total daily  regular hours per week:<br>" . $totalRegular . "</strong></td>";
									echo "<td class='text-right'><strong>Total daily overtime per week:<br>" . $totalOvertime . "</strong></td>";
									echo "</tr>";
							}
							if($infoPayrollUser2)
							{
								echo "<tr><td class='text-left' colspan='6'>";
								echo "<p class='text-danger'><b>Second Weak: " . $infoPayrollUser2[0]["period_weak"] . "</b></p>";
								echo "</td></tr>";
								$totalRegular = 0;
								$totalOvertime = 0;
								$totalRegularWeek = 0;
								$weeklyOvertime = 0;
								foreach ($infoPayrollUser2 as $lista):
									echo "<tr>";							
									echo "<td class='text-center'>" . $lista['start'] . "</td>";
									echo "<td class='text-center'>" . $lista['finish'] . "</td>";
									echo "<td class='text-right'>" . $lista['working_hours'] . "</td>";
									echo "<td class='text-right'>" . $lista['regular_hours'] . "</td>";
									echo "<td class='text-right'>" . $lista['overtime_hours'] . "</td>";
									$totalHours2 = $lista['working_hours'] + $totalHours2;
									$totalRegular = $lista['regular_hours'] + $totalRegular;
									$totalOvertime = $lista['overtime_hours'] + $totalOvertime;
									echo "</tr>";
								endforeach;
									echo "<tr><td></td><td></td>";
									echo "<td class='text-right'><strong>Total weekly hours:<br>" . $totalHours2 . "</strong></td>";
									echo "<td class='text-right'><strong>Total daily  regular hours per week:<br>" . $totalRegular . "</strong></td>";
									echo "<td class='text-right'><strong>Total daily overtime per week:<br>" . $totalOvertime . "</strong></td>";
									echo "</tr>";
							}
									$totalRegularHours = $totalWorked = $totalHours1 + $totalHours2;
									$totalOvertimeHours = $actualOvertimeHours1 + $actualOvertimeHours2;

									echo "<tr class='danger text-danger'><td></td>";
									echo "<td class='text-right'><strong>CUT-OFF:</strong></td>";
									echo "<td class='text-right'><strong>Total worked hours:<br>" . $totalWorked . "</strong></td>";
									echo "<td class='text-right'></td>";
									echo "<td class='text-right'></td>";
									echo "</tr>";
						?>
						</tbody>
					</table>

					<div class="row">
					<?php 
						$idUser = $lista['fk_id_user'];
						//Cost regular salary 
						$cost_regular_salary = $totalRegularHours * $employeeHourRate;
						//Cost overtime 
						$cost_overtime = $totalOvertimeHours * $employeeHourRate * 1.5;
						//Cost Vacation Regular Salary
						if($employeeType==1){
							$vacation_rate = 0.096;
						}else{
							$vacation_rate = 0.04;
						}
						$cost_vacation = $cost_regular_salary * $vacation_rate;
						//Gross salary
						$gross_salary = $cost_regular_salary + $cost_overtime + $cost_vacation;

					?>
						<div class="col-lg-4">
							<div class="panel panel-primary">
								<div class="panel-heading">
		                            <div class="row">
		                                <div class="col-lg-6">
		                                    <i class="fa fa-bell fa-fw"></i> Pay Stub Detail
		                                </div>
		                                <div class="col-lg-6 text-right">
										<?php if(!$infoPaystub){ ?>
											<form  name="paystub_<?php echo $idUser ?>" id="paystub_<?php echo $idUser; ?>" method="post" action="<?php echo base_url("payroll/save_paystub"); ?>">

												<input type="hidden" id="hddIdPaytsub" name="hddIdPaytsub" value="<?php echo $infoPaystub?$infoPaystub[0]["id_paystub"]:"" ?>" />
												<input type="hidden" id="hddIdTotalYearly" name="hddIdTotalYearly" value="<?php echo $idTotalYear; ?>" />
												<input type="hidden" id="hddIdPeriod" name="hddIdPeriod" value="<?php echo $idPeriod; ?>" />
												<input type="hidden" id="hddYear" name="hddYear" value="<?php echo $infoPeriod[0]['year_period']; ?>" />
												<input type="hidden" id="hddIdWeakPeriod1" name="hddIdWeakPeriod1" value="<?php echo $infoWeakPeriod[0]['id_period_weak']; ?>" />
												<input type="hidden" id="hddIdWeakPeriod2" name="hddIdWeakPeriod2" value="<?php echo $infoWeakPeriod[1]['id_period_weak']; ?>" />
												<input type="hidden" id="hddIdUser" name="hddIdUser" value="<?php echo $idUser; ?>" />

												<input type="hidden" id="hddBankTime" name="hddBankTime" value="<?php echo $bankTime; ?>" />
												<input type="hidden" id="hddBankTimeFlag" name="hddBankTimeFlag" value="<?php echo $bankTimeFlag; ?>" />			

												<input type="hidden" id="hddEmployeeRate" name="hddEmployeeRate" value="<?php echo $employeeHourRate; ?>" />
												<input type="hidden" id="hddEmployeeType" name="hddEmployeeType" value="<?php echo $employeeType; ?>" />
												
												<input type="hidden" id="hddTotalWorkedHours" name="hddTotalWorkedHours" value="<?php echo $totalWorked; ?>" />
												<input type="hidden" id="hddRegularHours" name="hddRegularHours" value="<?php echo $totalRegularHours; ?>" />
												<input type="hidden" id="hddOvertimeHours" name="hddOvertimeHours" value="<?php echo $totalOvertimeHours; ?>" />
												<input type="hidden" id="hddCostRegularSalary" name="hddCostRegularSalary" value="<?php echo $cost_regular_salary; ?>" />
												<input type="hidden" id="hddCostOvertime" name="hddCostOvertime" value="<?php echo $cost_overtime; ?>" />
												<input type="hidden" id="hddCostVacation" name="hddCostVacation" value="<?php echo $cost_vacation; ?>" />
												<input type="hidden" id="hddFGrossSalary" name="hddFGrossSalary" value="<?php echo $gross_salary; ?>" />

												<!-- campos ocultos para redireccionar al listado inicial buscado -->
												<input type="hidden" id="contractType" name="contractType" value="<?php echo $contractType; ?>" />
												<input type="hidden" id="period" name="period" value="<?php echo $idPeriod; ?>" />
												<input type="hidden" id="employee" name="employee" value="<?php echo $idEmployee; ?>" />

												<input type="hidden" id="ee_cpp" name="ee_cpp" value=0 />
												<input type="hidden" id="ee_ei" name="ee_ei" value=0 />
												<input type="hidden" id="tax" name="tax" value=0 />
												<input type="hidden" id="gwl_deductions" name="gwl_deductions" value=0 />
												<input type="hidden" id="hddBankTimeBalance" name="hddBankTimeBalance" value=0 />

												<button type="submit" id="btnSubmit" name="btnSubmit" class="btn btn-info" >
													Generate Paystub <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
												</button> 
											</form>
										<?php } ?>
		                                </div>
		                            </div>
								</div>
								<!-- /.panel-heading -->
								<div class="panel-body">
									<div class="list-group">
										<p class="text-default">
											<span class="text-muted small"><em><strong> Employee: </strong> <?php echo $employeeName; ?></em>
											</span><br>
											<span class="text-muted small"><em><strong> Period Beginning: </strong> <?php echo $infoPeriod[0]["date_start"]; ?></em>
											</span><br>
											<span class="text-muted small"><em><strong> Period Ending: </strong> <?php echo $infoPeriod[0]["date_finish"]; ?></em>
											</span>
										</p>
										<table width="100%" class="table table-hover" id="dataTables">
											<thead>
												<tr>
													<th><small>PAY</small></th>
													<th class='text-center'><small>Hours</small></th>
													<th class='text-center'><small>Rate</small></th>
													<th class='text-right'><small>Current</small></th>
													<th class='text-right'><small>YTD</small></th>
												</tr>
											</thead>
											<tbody>
											<?php
												if(!$infoPaystub){
													if($infoTotalYear){
														$year_cost_regular_salary = $cost_regular_salary + $infoTotalYear[0]['total_year_cost_regular_salary'];
														$year_cost_overtime = $cost_overtime + $infoTotalYear[0]['total_year_cost_over_time'];
														$year_cost_vacation = $cost_vacation + $infoTotalYear[0]['total_year_cost_vacation_regular_salary'];
													}else{
														$year_cost_regular_salary = $cost_regular_salary;
														$year_cost_overtime = $cost_overtime;
														$year_cost_vacation = $cost_vacation;
													}
												}else{
														$year_cost_regular_salary = $infoTotalYear[0]['total_year_cost_regular_salary'];
														$year_cost_overtime = $infoTotalYear[0]['total_year_cost_over_time'];
														$year_cost_vacation = $infoTotalYear[0]['total_year_cost_vacation_regular_salary'];
												}
												echo "<tr>";
												echo "<td><small>Regular Pay</small></td>";
												echo "<td class='text-center'><small>" . $totalRegularHours . "</small></td>";
												echo "<td class='text-center'><small>" . $employeeHourRate . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($cost_regular_salary, 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($year_cost_regular_salary, 2) . "</small></td>";
												echo "</tr>";

												echo "<tr>";
												echo "<td><small>Overtime Pay</small></td>";
												echo "<td class='text-center'><small>" . $totalOvertimeHours . "</small></td>";
												echo "<td class='text-center'><small>" . $employeeHourRate * 1.5 . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($cost_overtime, 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($year_cost_overtime, 2) . "</small></td>";
												echo "</tr>";

												echo "<tr>";
												echo "<td><small>Vacation Pay</small></td>";
												echo "<td class='text-center'></td>";
												echo "<td class='text-center'></td>";
												echo "<td class='text-right'><small>$ " . number_format($cost_vacation, 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($year_cost_vacation, 2) . "</small></td>";
												echo "</tr>";
											?>
											</tbody>
										</table>
				<?php
					if($infoPaystub){
				?>
										<table width="100%" class="table table-hover" id="dataTables">
											<thead>
												<tr>
													<th><small>DEDUCTIONS</small></th>
													<th class='text-right'><small>Current</small></th>
													<th class='text-right'><small>YTD</small></th>
												</tr>
											</thead>
											<tbody>
											<?php
												echo "<tr>";
												echo "<td><small>GWL Deductions</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['gwl_deductions'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_gwl_deductions'], 2) . "</small></td>";
												echo "</tr>";
											?>
											</tbody>
										</table>

										<table width="100%" class="table table-hover" id="dataTables">
											<thead>
												<tr>
													<th><small>TAXES</small></th>
													<th class='text-right'><small>Current</small></th>
													<th class='text-right'><small>YTD</small></th>
												</tr>
											</thead>
											<tbody>
											<?php
												echo "<tr>";
												echo "<td><small>Income Tax</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['tax'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_tax'], 2) . "</small></td>";
												echo "</tr>";

												echo "<tr>";
												echo "<td><small>Employment Insurance</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['ee_ei'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_ee_ei'], 2) . "</small></td>";
												echo "</tr>";

												echo "<tr>";
												echo "<td><small>Canada Pension Plan</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['ee_cpp'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_ee_cpp'], 2) . "</small></td>";
												echo "</tr>";
											?>
											</tbody>
										</table>
				<?php
					}
				?>
									</div>
								</div>
							</div>
						</div>

				<?php
					if($infoPaystub){
				?>
						<div class="col-lg-4">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<i class="fa fa-bell fa-fw"></i> Summary
								</div>
								<!-- /.panel-heading -->
								<div class="panel-body">
									<div class="list-group">

										<table width="100%" class="table table-hover" id="dataTables">
											<thead>
												<tr>
													<th><small>SUMMARY</small></th>
													<th class='text-right'><small>Current</small></th>
													<th class='text-right'><small>YTD</small></th>
												</tr>
											</thead>
											<tbody>
											<?php
												echo "<tr>";
												echo "<td><small>Total Pay</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['gross_salary'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_gross_salary'], 2) . "</small></td>";
												echo "</tr>";

												echo "<tr>";
												echo "<td><small>Taxes</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['ee_total_taxes'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_ee_total_taxes'], 2) . "</small></td>";
												echo "</tr>";

												echo "<tr>";
												echo "<td><small>Deductions</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoPaystub[0]['gwl_deductions'], 2) . "</small></td>";
												echo "<td class='text-right'><small>$ " . number_format($infoTotalYear[0]['total_year_gwl_deductions'], 2) . "</small></td>";
												echo "</tr>";


												echo "<tr>";
												echo "<td><strong>Net Pay</strong></td>";
												echo "<td class='text-right'><strong>$ " . number_format($infoPaystub[0]['net_pay'], 2) . "</strong></td>";
												echo "<td class='text-right'></td>";
												echo "</tr>";
											?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
				<?php
					}
				?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
			endforeach;
	 	}	
	?>
</div>

<!--INICIO Modal cambio de hora-->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>                       
<!--FIN Modal-->

<!-- Tables -->
<script>
$(document).ready(function() {
	$('#dataTables').DataTable({
		responsive: true,
		"ordering": false,
		"pageLength": 50
	});
});
</script>