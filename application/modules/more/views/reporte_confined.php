<?php
// create some HTML content
$html = '<br><h2 align="center" style="color:#337ab7;">CONFINED SPACE ENTRY PERMIT<br><br></h2>
				<style>
				table {
					font-family: arial, sans-serif;
					border-collapse: collapse;
					width: 100%;
				}

				td, th {
					border: 1px solid #dddddd;
					text-align: left;
					padding: 8px;
				}
				</style>
			<table border="0" cellspacing="0" cellpadding="5">
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Job Code/Name: </strong></th>
					<th>' . strtoupper($info[0]['job_description']) . '</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Permit No.: </strong></th>
					<th>' . $info[0]['id_job_confined'] . '</th>
				</tr>
			
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Done by: </strong></th>
					<th>' . $info[0]['name'] . '<p>I Have completed a Field Level Hazard Assessment.</p></th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Date: </strong></th>
					<th>' . $info[0]['date_confined'] . '</th>
				</tr>
			
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Location: </strong></th>
					<th colspan="3">' . $info[0]['location'] . '</th>
				</tr>
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Purpose of Entry: </strong></th>
					<th colspan="3">' . $info[0]['purpose'] . '</th>
				</tr>
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Scheduled start: </strong></th>
					<th>' . $info[0]['scheduled_start'] . '</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Scheduled finish: </strong></th>
					<th>' . $info[0]['scheduled_finish'] . '</th>
				</tr>

			</table>';

$html .= '<br><br>';

$html .= '<h2 align="center" style="color:#337ab7;">Entrant(s)<br></h2>';

if (!$confinedWorkers) {
	$html .= 'No data was found for workers';
} else {
	$html .= '<table border="0" cellspacing="0" cellpadding="5">
								<tr>
									<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Worker</strong></th>
									<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Time In</strong></th>
									<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Time Out</strong></th>
									<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Task</strong></th>
									<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Signature</strong></th>
								</tr>';

	foreach ($confinedWorkers as $data) :
		$html .= '<tr>
									<th>' . $data['name'] . '</th>
									<th align="center">';
		if ($data['signature'] && $data['date_time_in']) {
			$html .= $data['date_time_in'];
		}
		$html .= '</th>
											<th align="center">';
		if ($data['signature'] && $data['date_time_in']) {
			$html .= $data['date_time_out'];
		}
		$html .= '</th>
									<th>' . $data['task'] . '</th>';
		$html .= '<th align="center"><img src="' . $data['signature'] . '" border="0" width="70" height="70" /></th>';
		$html .= '</tr>';
	endforeach;
	$html .= '</table>';
}

$html .= '<br><br>';

$html .= '<h2 align="center" style="color:#337ab7;">WORKERS ON SITE<br></h2>';

if (!$WorkersOnSite) {
	$html .= 'No data was found for workers';
} else {
	$html .= '<table border="0" cellspacing="0" cellpadding="5">
				<tr>
					<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Worker</strong></th>
				</tr>';

	foreach ($WorkersOnSite as $data) :
		$html .= '<tr>
					<th>' . $data['name'] . '</th>';
		$html .= '</tr>';
	endforeach;
	$html .= '</table>';
}

$html .= '<br><br>';

$html .= '<table border="0" cellspacing="0" cellpadding="5">';
$html .= '<tr>
							<th>';
$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
$html .= ' <b>Oxygen (Acceptable Level)</b>			19.5 % - 22 %<br>';
$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
$html .= ' <b>Carbon Monoxide (Ocupational Exposure Limit)</b>	25 ppm <br>';
$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
$html .= ' <b>Hydrogen Sulphide (Ocupational Exposure Limit)</b>	10 ppm';
$html .= '</th>';
$html .= '</tr>';
$html .= '</table><br><br>';

$html .= '<h2 align="center" style="color:#337ab7;">Pre-Entry Authorizartion<br></h2>';

$html .= '<table border="0" cellspacing="0" cellpadding="5">';

$html .= '<tr>
						<th>';
if ($info[0]['oxygen_deficient'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Oxygen-Deficient Atmosphere<br>';

if ($info[0]['oxygen_enriched'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Oxygen-Enriched Atmosphere<br>';

if ($info[0]['welding'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Welding/cutting';

$html .= '</th>';

$html .= '<th>';
if ($info[0]['engulfment'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Engulfment<br>';

if ($info[0]['toxic_atmosphere'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Toxic Atmosphere<br>';

if ($info[0]['flammable_atmosphere'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Flammable Atmosphere';

$html .= '</th>';

$html .= '<th>';

if ($info[0]['energized_equipment'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Energized Electric Equipment<br>';

if ($info[0]['entrapment'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Entrapment<br>';

if ($info[0]['hazardous_chemical'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Hazardous Chemical';

$html .= '</th>';

$html .= '</tr>';

$html .= '</table><br><br>';


$html .= '<h2 align="center" style="color:#337ab7;">Safety precautions<br></h2>';

$html .= '<table border="0" cellspacing="0" cellpadding="5">';

$html .= '<tr>
						<th>';
if ($info[0]['breathing_apparatus'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Self-Contained Breathing Apparatus<br>';

if ($info[0]['line_respirator'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Air-Line Respirator<br>';

if ($info[0]['resistant_clothing'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Flame Resistant Clothing<br>';

if ($info[0]['ventilation'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Ventilation<br>';

if ($info[0]['protective_gloves'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Protective Gloves';

$html .= '</th>';

$html .= '<th>';
if ($info[0]['linelines'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Linelines<br>';

if ($info[0]['respirators'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Respirators<br>';

if ($info[0]['lockout'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Lockout/Tagout<br>';

if ($info[0]['fire_extinguishers'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Fire Extinguishers<br>';

if ($info[0]['barricade'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Barricade Job Area';

$html .= '</th>';

$html .= '<th>';

if ($info[0]['signs_posted'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Signs Posted<br>';

if ($info[0]['clearance_secured'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Clearance Secured<br>';

if ($info[0]['lighting'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Lighting<br>';

if ($info[0]['interrupter'] == 1) {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark.png" height="12" width="12"/>';
} else {
	$html .= '<img src="http://v-contracting.ca/app/images/check_mark_whyte.png" height="12" width="12"/>';
}
$html .= ' Ground Fault Interrupter';

$html .= '</th>';

$html .= '</tr>';

$html .= '</table><br><br>';

$html .= '<h2 align="center" style="color:#337ab7;">Environmental conditions - Test to be taken<br></h2>';

$html .= '<table border="0" cellspacing="0" cellpadding="5">
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Oxygen : </strong></th>
					<th>' . $info[0]['oxygen'] . ' %</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Date/Time: </strong></th>
					<th>' . $info[0]['oxygen_time'] . '</th>
				</tr>
			
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Lower Explosive Limit: </strong></th>
					<th>' . $info[0]['explosive_limit'] . ' %</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Date/Time: </strong></th>
					<th>' . $info[0]['explosive_limit_time'] . '</th>
				</tr>
			
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Toxic Atmosphere: </strong></th>
					<th colspan="3">' . $info[0]['toxic_atmosphere_cond'] . '</th>
				</tr>
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Instruments Used: </strong></th>
					<th colspan="3">' . $info[0]['instruments_used'] . '</th>
				</tr>
			</table>';

$html .= '<br><br>';

$html .= '<table border="0" cellspacing="0" cellpadding="5">
<tr>
	<th bgcolor="#337ab7" style="color:white;"><strong>Remarks on the overall condition of the confined space : </strong></th>
</tr>
<tr>
	<th>' . $info[0]['remarks'] . '</th>
</tr>
</table>';

$html .= '<br><br>';

if ($retesting) {
	$html .= '<h2 align="center" style="color:#337ab7;">Environmental conditions - Re-Testing<br></h2>';

	$html .= '<table border="0" cellspacing="0" cellpadding="5">
	<tr>
		<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Oxigen</strong></th>
		<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Date/Time</strong></th>
		<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Lower/Explosive Limit</strong></th>
		<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Date/Time</strong></th>
		<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Toxic Atmosphere</strong></th>
		<th align="center" bgcolor="#337ab7" style="color:white;"><strong>Instruments Used</strong></th>
	</tr>';

	foreach ($retesting as $data) :
		$html .= '<tr>
				<th align="center">' . $data['re_oxygen'] . ' %</th>
				<th align="center">' . $data['re_oxygen_time'] . '</th>
				<th align="center">' . $data['re_explosive_limit'] . '%</th>
				<th align="center">' . $data['re_explosive_limit_time'] . '</th>
				<th align="center">' . $data['re_toxic_atmosphere'] . '</th>
				<th>' . $data['re_instruments_used'] . '</th>';
		$html .= '</tr>';
	endforeach;
	$html .= '</table>';

	$html .= '<br><br>';
}


$html .= '<h2 align="center" style="color:#337ab7;">Post-entry Inspection<br></h2>';

$html .= '<table border="0" cellspacing="0" cellpadding="5">';
$html .= '<tr>
			<th>Are all personnel out of the confined space and accounted for? </th>
			<th align="center">';
$html .= $info[0]["personnel_out"] == 1 ? 'Yes' : ($info[0]["personnel_out"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Have isolation devices been removed and pipes been restored to their original positions? </th>
			<th align="center">';
$html .= $info[0]["isolation"] == 1 ? 'Yes' : ($info[0]["isolation"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Have all lockouts been removed? </th>
			<th align="center">';
$html .= $info[0]["lockouts_removed"] == 1 ? 'Yes' : ($info[0]["lockouts_removed"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Have all safe entry tags and sings been removed? </th>
			<th align="center">';
$html .= $info[0]["tags_removed"] == 1 ? 'Yes' : ($info[0]["tags_removed"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Have all equipment and waste been removed from the work area? </th>
			<th align="center">';
$html .= $info[0]["equipment_removed"] == 1 ? 'Yes' : ($info[0]["equipment_removed"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Has all specialized PPE been cleaned, post-inspected and put away? </th>
			<th align="center">';
$html .= $info[0]["ppe_cleaned"] == 1 ? 'Yes' : ($info[0]["ppe_cleaned"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Has all rescue equipment been post -inspected, cleaned and stored (If Applicable)? </th>
			<th align="center">';
$html .= $info[0]["rescue_equipment"] == 1 ? 'Yes' : ($info[0]["rescue_equipment"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Have all permits been signed out and filed properly? </th>
			<th align="center">';
$html .= $info[0]["permits_signed"] == 1 ? 'Yes' : ($info[0]["permits_signed"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>
			<th>Have other applicable areas of the facility been notified that the work in the confined space is complete and operations are ready to be resumed? </th>
			<th align="center">';
$html .= $info[0]["areas_notified"] == 1 ? 'Yes' : ($info[0]["areas_notified"] == 2 ? 'No' : 'N/A');
$html .= '</th>';
$html .= '</tr>';

$html .= '</table>';
$html .= '<br><br>';

$html .= '<br><br>';
//FIRMAS SUPERVISOR Y MANAGER
$html .= '<table border="0" cellspacing="0" cellpadding="5">';

$html .= '<tr>';

$html .= '<th align="center" width="30%">';
if ($info[0]['authorization_signature']) {
	$html .= '<img src="' . $info[0]['authorization_signature'] . '" border="0" width="70" height="70" />';
}
$html .= '</th>';

$html .= '<th align="center" width="40%"></th>';

$html .= '<th align="center" width="30%">';
if ($info[0]['cancellation_signature']) {
	$html .= '<img src="' . $info[0]['cancellation_signature'] . '" border="0" width="70" height="70" />';
}
$html .= '</th>';

$html .= '</tr>';

$html .= '<tr bgcolor="#337ab7" style="color:white;">
						<th align="center"><strong>' . $info[0]['user_authorization'] . '</strong></th>
						<th align="center" width="40%"></th>
						<th align="center"><strong>' . $info[0]['user_cancellation'] . '</strong></th>';
$html .= '</tr>';

$html .= '<tr bgcolor="#337ab7" style="color:white;">
						<th align="center"><strong>Entry authorization Signature</strong></th>
						<th align="center" width="40%"></th>
						<th align="center"><strong>Entry cancellation Signature</strong></th>';
$html .= '</tr>';

$html .= '</table>';


echo $html;
