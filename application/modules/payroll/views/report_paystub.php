<?php
	// create some HTML content	
	$html = '<br><br>
		<style>
		table {
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		td, th {
			border: 0px solid #dddddd;
			text-align: left;
			padding: 8px;
		}
		</style>';

	$html.= '<hr>';

	$html.= '<table border="0" cellspacing="0" cellpadding="4">';

	$html.= '<tr>
				<th width="50%">
					V Contracting Inc. <br>
					P.O. Box 67037 <br>
					Calgary AB T2L 2L2 <br>
				</th>

				<th width="50%">
				</th>
			</tr>';

	$html.= '<tr>
				<th width="50%">

				</th>

				<th width="50%">
					Pay Stub Detail<br>
					PAY DATE:<br>
					NET PAY: $ ' . number_format($infoPaystub[0]['net_pay'],2) . '
				</th>
			</tr>';

	$html.= '<tr>
				<th width="50%">
					' . $infoPaystub[0]['employee'] . '<br>
					' . $infoPaystub[0]['address'] . '<br>
					Calgary AB ' . $infoPaystub[0]['postal_code'] . '
				</th>

				<th width="50%">
				</th>
			</tr>';

	$html.= '</table>';

	$html.= '<hr>';

	$html.= '<table border="0" cellspacing="0" cellpadding="4">';

	$html.= '<tr>
				<th width="50%">
					<b>EMPLOYER</b> <br>
					V Contracting Inc. <br>
					P.O. Box 67037 <br>
					Calgary AB T2L 2L2
				</th>

				<th width="50%">
					<b>PAY PERIOD</b> <br>
					Period Beginning: ' . $infoPaystub[0]['date_start'] . '<br>
					Period Ending: ' . $infoPaystub[0]['date_finish'] . '<br>
					Pay Date: ' . $infoPaystub[0]['employee'] . '<br>
					Total Hours: ' . $infoPaystub[0]['total_worked_hours'] . '
				</th>
			</tr>';

	$html.= '</table>';

	$html.= '<br><br><br>';

	$html.= '<table border="0" cellspacing="0" cellpadding="4">';

	$html.= '<tr>
				<th width="50%">
					<b>EMPLOYEE</b> <br>
					' . $infoPaystub[0]['employee'] . '<br>
					' . $infoPaystub[0]['address'] . '<br>
					Calgary AB ' . $infoPaystub[0]['postal_code'] . '
				</th>

				<th width="50%">

				</th>
			</tr>';

	$html.= '</table>';

	$html.= '<br><br><br>';

	$html.= '<table border="0" cellspacing="0" cellpadding="4">';

	$html.= '<tr>
				<th width="20%">
					<b>BENEFITS</b>
				</th>
				<th width="20%">
					<b>Used</b>
				</th>
				<th width="20%">
					<b>Available</b>
				</th>
				<th width="20%">
					<b>NET PAY:</b>
				</th>
				<th width="20%">
					<b>$ ' . number_format($infoPaystub[0]['net_pay'],2) . '</b>
				</th>
			</tr>';

	$html.= '<tr>
				<th width="20%">
					Vacation
				</th>
				<th width="20%">
					0.00
				</th>
				<th width="20%">
					0.00
				</th>
				<th width="20%">
					Acct#...1635:
				</th>
				<th width="20%">
					$ ' . number_format($infoPaystub[0]['net_pay'],2) . '
				</th>
			</tr>';
	$html.= '</table>';

	$html.= '<br>';

	$html.= '<hr>';

	$html.= '<br><br>';

	$detailPayment = '<table border="0" cellspacing="0" cellpadding="4">';
	$detailPayment.= '<tr>
				<th width="20%">
					<b>PAY</b>
				</th>
				<th width="20%">
					<b>Hours</b>
				</th>
				<th width="20%">
					<b>Rate</b>
				</th>
				<th width="20%">
					<b>Current</b>
				</th>
				<th width="20%">
					<b>YTD</b>
				</th>
			</tr>';

	$detailPayment.= '<tr>
			<th width="20%">
				Overtime Pay
			</th>
			<th width="20%">
				' . number_format($infoPaystub[0]['total_overtime_hours'],2) . '
			</th>
			<th width="20%">
				' . number_format($infoPaystub[0]['employee_rate_paystub']*1.5,2) . '
			</th>
			<th width="20%">
			' . number_format($infoPaystub[0]['cost_over_time'],2) . '
			</th>
			<th width="20%">
				<b>YTD</b>
			</th>
		</tr>';

	$detailPayment.= '<tr>
		<th width="20%">
		Regular Pay
		</th>
		<th width="20%">
			' . number_format($infoPaystub[0]['total_regular_hours'],2) . '
		</th>
		<th width="20%">
			' . number_format($infoPaystub[0]['employee_rate_paystub'],2) . '
		</th>
		<th width="20%">
		' . number_format($infoPaystub[0]['cost_regular_salary'],2) . '
		</th>
		<th width="20%">
			<b>YTD</b>
		</th>
	</tr>';

	$detailPayment.= '<tr>
		<th width="20%">
			Vacation Pay
		</th>
		<th width="20%">
			-
		</th>
		<th width="20%">
			-
		</th>
		<th width="20%">
			' . number_format($infoPaystub[0]['cost_vacation_regular_salary'],2) . '
		</th>
		<th width="20%">
			<b>YTD</b>
		</th>
	</tr>';

	$detailPayment.= '</table>';


	$detailDeduction = '<table border="0" cellspacing="0" cellpadding="4">';
	$detailDeduction.= '<tr>
				<th width="60%">
					<b>DEDUCTIONS</b>
				</th>
				<th width="20%">
					<b>Current</b>
				</th>
				<th width="20%">
					<b>YTD</b>
				</th>
			</tr>';
	$detailDeduction.= '</table>';

	$html.= '<table border="0" cellspacing="0" cellpadding="4">';
	$html.= '<tr>
				<th width="50%">
					' . $detailPayment . '
				</th>
		
				<th width="50%">
				' . $detailDeduction . '
				</th>
			</tr>';
	$html.= '</table>';

	$html.= '<br><br><br>';

	$taxes = '<table border="0" cellspacing="0" cellpadding="4">';
	$taxes.= '<tr>
				<th width="60%">
					<b>TAXES</b>
				</th>
				<th width="20%">
					<b>Current</b>
				</th>
				<th width="20%">
					<b>YTD</b>
				</th>
			</tr>';

	$taxes.= '<tr>
				<th width="60%">
					Income Tax
				</th>
				<th width="20%">
					' . number_format($infoPaystub[0]['tax'],2) . '
				</th>
				<th width="20%">
					<b>YTD</b>
				</th>
			</tr>';

	$taxes.= '<tr>
				<th width="60%">
					<b>Employment Insurance</b>
				</th>
				<th width="20%">
					' . number_format($infoPaystub[0]['ee_ei'],2) . '
				</th>
				<th width="20%">
					<b>YTD</b>
				</th>
			</tr>';
	$taxes.= '<tr>
			<th width="60%">
				<b>Canada Pension Plan</b>
			</th>
			<th width="20%">
				' . number_format($infoPaystub[0]['ee_cpp'],2) . '
			</th>
			<th width="20%">
				<b>YTD</b>
			</th>
		</tr>';
	$taxes.= '</table>';

	$html.= '<table border="0" cellspacing="0" cellpadding="4">';
	$html.= '<tr>
				<th width="50%">
					' . $taxes . '
				</th>
		
				<th width="50%">
					' . $taxes . '
				</th>
			</tr>';
	$html.= '</table>';
		
	echo $html;
						
?>