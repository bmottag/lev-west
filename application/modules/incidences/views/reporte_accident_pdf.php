<?php
	// create some HTML content
	$html = '<br><h1 align="center" style="color:#337ab7;">' . strtoupper($title) . '<br><br></h1>
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
					<th bgcolor="#337ab7" style="color:white;"><strong>Reported by: </strong></th>
					<th>' . $info[0]["name"] . '</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Unit: </strong></th>
					<th>' . $info[0]["unit"] . '</th>
				</tr>		
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Date of accident: </strong></th>
					<th>' . $info[0]["date_accident"] . '</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Time: </strong></th>
					<th>' . $info[0]["time"] . '</th>
				</tr>
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Location: </strong></th>
					<th>' . $info[0]["location"] . '</th>
				</tr>
			</table>';
			
$call_manager = $info[0]["call_manager"] == 1?"Yes":"No";
$take_pictures = $info[0]["take_pictures"] == 1?"Yes":"No";
$warning_devises = $info[0]["warning_devises"] == 1?"Yes":"No";

			
	$html.= '<table border="0" cellspacing="0" cellpadding="5">
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Did you call your Driver Manager? </strong></th>
					<th>' . $call_manager . '</th>
				</tr>		
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Did you take pictures (Not from injured people)? </strong></th>
					<th>' . $take_pictures . '</th>
				</tr>
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Did you set out all warning devises (on time)? </strong></th>
					<th>' . $warning_devises . '</th>
				</tr>
			</table>';

			
	$html.= '<br><br>
			<table border="0" cellspacing="0" cellpadding="5">
				<tr>
					<th colspan="3" bgcolor="#337ab7" style="color:white;">
					<strong>Brief explanation </strong>
					</th>
				</tr>		
				<tr>
					<th colspan="3">' . $info[0]['brief_explanation']. '</th>
				</tr>
			</table>';
			
	$html.= '<br><br>
			<table border="0" cellspacing="0" cellpadding="5">
				<tr>
					<th colspan="3" bgcolor="#337ab7" style="color:white;">
					<strong>Climate Conditions </strong>
					</th>
				</tr>		
				<tr>
					<th colspan="3">' . $info[0]['climate_conditions']. '</th>
				</tr>
			</table>';

			
	if($carsInvolvedInfo)
	{
		$html.= '<br><br>
				<table border="0" cellspacing="0" cellpadding="5">
					<tr>
						<th bgcolor="#337ab7" style="color:white;"><strong>Make</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Model</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Insurance number</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Register owner</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Driver name</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>License number</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Plate</strong></th>
					</tr>';		

		foreach ($carsInvolvedInfo as $list):
			$html.= "<tr>";					
			$html.= "<th>" . $list['make'] . "</th>";
			$html.= "<th>" . $list['model'] . "</th>";
			$html.= "<th>" . $list['insurance'] . "</th>";
			$html.= "<th>" . $list['register_owner'] . "</th>";
			$html.= "<th>" . $list['driver_name'] . "</th>";
			$html.= "<th>" . $list['license'] . "</th>";
			$html.= "<th>" . $list['plate'] . "</th>";
			$html.= "</tr>";	
		endforeach;

		$html.= '</table>';
	}
	
	if($witnessInfo)
	{
		$html.= '<br><br>
				<table border="0" cellspacing="0" cellpadding="5">
					<tr>
						<th bgcolor="#337ab7" style="color:white;"><strong>Name</strong></th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Phone number</strong></th>
					</tr>';		

		foreach ($witnessInfo as $list):
			$html.= "<tr>";					
			$html.= "<th>" . $list['name'] . "</th>";
			$html.= "<th>" . $list['phone_number'] . "</th>";
			$html.= "</tr>";	
		endforeach;

		$html.= '</table>';
	}
			
echo $html;
						
?>