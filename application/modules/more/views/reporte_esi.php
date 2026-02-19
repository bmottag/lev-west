<?php
	// create some HTML content
	$html = '<h3 align="right" style="color:#337ab7;">Project code: ' . $info[0]['job_description'] . '</h3><br>';
	
	$html .= '<br><h2 align="center" style="color:#337ab7;">Environmental site inspection</h2><br>';
	
	$html .= '<style>
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
			<table border="0" cellspacing="0" cellpadding="5" bgcolor="#337ab7" style="color:white;">';
			
			$html.='<tr>
						<th align="center" rowspan="2" width="41%"><br><br>Inspection Items</th>
						<th align="center" width="16%">Implemented ?</th>
						<th align="center" rowspan="2" width="8%"><br><br><br>N/A</th>
						<th rowspan="2" width="35%"><br><br>Remarks: Location good practices, problem observed, possible causes</th>
					</tr>
					<tr>
						<th align="center" width="8%">Yes</th>
						<th align="center" width="8%">No</th>
					</tr>
					</table>';
			$html.= '<table border="0" cellspacing="0" cellpadding="5">';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
					<th colspan="5">1. Air pollution Control</th>
					</tr>';
			
			$html.= '<tr>';
			$html.='<th width="41%">Are the construction sites watered to minimize dust?</th>';
				if($info[0]["sites_watered"] == 1){
					$html.= '<th align="center" width="8%">Yes</th>
							 <th align="center" width="8%"></th>
							 <th align="center" width="8%"></th>';
				}elseif($info[0]["sites_watered"] == 2){
					$html.= '<th align="center" width="8%"></th>
							 <th align="center" width="8%">No</th>
							 <th align="center" width="8%"></th>';
				}elseif($info[0]["sites_watered"] == 99){
					$html.= '<th align="center" width="8%"></th>
							 <th align="center" width="8%"></th>
							 <th align="center" width="8%">N/A</th>';
				}
			$html.= '<th align="center" width="35%">' . $info[0]["sites_watered_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Cement debagging process undertaken in sheltered areas</th>';
				if($info[0]["being_swept"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["being_swept"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["being_swept"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["being_swept_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Are all vehicles carrying dusty loads covered?</th>';
				if($info[0]["dusty_covered"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["dusty_covered"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["dusty_covered"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["dusty_covered_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Are speed control measures applied?</th>';
				if($info[0]["speed_control"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["speed_control"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["speed_control"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["speed_control_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
					<th colspan="5">2. Noise Control</th>
					</tr>';
					
			$html.= '<tr>';
			$html.='<th>Is the construction noise permit valid?</th>';
				if($info[0]["noise_permit"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["noise_permit"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["noise_permit"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["noise_permit_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Do air compressors operate with doors closed?</th>';
				if($info[0]["air_compressors"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["air_compressors"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["air_compressors"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["air_compressors_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Any noise mitigation measures adopted</th>';
				if($info[0]["noise_mitigation"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["noise_mitigation"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["noise_mitigation"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["noise_mitigation_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Is idle plan/equipment turned off or throttled down?</th>';
				if($info[0]["idle_plan"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["idle_plan"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["idle_plan"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["idle_plan_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
					<th colspan="5">3. Site Management</th>
					</tr>';
					
			$html.= '<tr>';
			$html.='<th>Is there enough garbage bins on site?</th>';
				if($info[0]["garbage_bin"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["garbage_bin"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["garbage_bin"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["garbage_bin_remarks"] . '</th>';
			$html.= '</tr>';
						
			$html.= '<tr>';
			$html.='<th>Are garbage bins collected and disposed periodically?</th>';
				if($info[0]["disposed_periodically"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["disposed_periodically"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["disposed_periodically"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["disposed_periodically_remarks"] . '</th>';
			$html.= '</tr>';

			$html.= '<tr>';
			$html.='<th>Is recycling being followed and placed accordingly?</th>';
				if($info[0]["recycling_being"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["recycling_being"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["recycling_being"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["recycling_being_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Is the spill containment workstation being implemented? Is It in good conditions?</th>';
				if($info[0]["spill_containment"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["spill_containment"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["spill_containment"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["spill_containment_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Did we have any spillage happen on site? If so, how effective or immediately was it taking care?</th>';
				if($info[0]["spillage_happen"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["spillage_happen"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["spillage_happen"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["spillage_happen_remarks"] . '</th>';
			$html.= '</tr>';

			$html.= '<tr bgcolor="#337ab7" style="color:white;">
					<th colspan="5">4. Storage of chemicals and Dangerous goods</th>
					</tr>';
					
			$html.= '<tr>';
			$html.='<th>Are chemicals, fuel, oils, coolant, and hydraulic stored and labelled property?</th>';
				if($info[0]["chemicals_stored"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["chemicals_stored"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["chemicals_stored"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["chemicals_stored_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Are spill kits / sand / saw dust used for absorbing chemical spillage readily accessible?</th>';
				if($info[0]["absorbing_chemical"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["absorbing_chemical"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["absorbing_chemical"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["absorbing_chemical_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Do all equipment, & trucks have spill kits?</th>';
				if($info[0]["spill_kits"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["spill_kits"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["spill_kits"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["spill_kits_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
					<th colspan="5">5. Resource Conservation</th>
					</tr>';
					
			$html.= '<tr>';
			$html.='<th>Are Diesel-powered plant and equipment shut off while not in use to reduce excessive use?</th>';
				if($info[0]["excessive_use"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["excessive_use"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["excessive_use"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["excessive_use_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Are materials stored in good condition to prevent deterioration and wastage?</th>';
				if($info[0]["materials_stored"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["materials_stored"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["materials_stored"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["materials_stored_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
					<th colspan="5">6. Emergency Preparedness and Response</th>
					</tr>';
					
			$html.= '<tr>';
			$html.='<th>Are fire extinguishers / fighting facilities properly maintained and not expired?</th>';
				if($info[0]["fire_extinguishers"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["fire_extinguishers"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["fire_extinguishers"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["fire_extinguishers_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '<tr>';
			$html.='<th>Are accidents and incidents reported and reviewed, and corrective & preventive actions identified and recorded?</th>';
				if($info[0]["preventive_actions"] == 1){
					$html.= '<th align="center">Yes</th>
							 <th align="center"></th>
							 <th align="center"></th>';
				}elseif($info[0]["preventive_actions"] == 2){
					$html.= '<th align="center"></th>
							 <th align="center">No</th>
							 <th align="center"></th>';
				}elseif($info[0]["preventive_actions"] == 99){
					$html.= '<th align="center"></th>
							 <th align="center"></th>
							 <th align="center">N/A</th>';
				}
			$html.= '<th align="center">' . $info[0]["preventive_actions_remarks"] . '</th>';
			$html.= '</tr>';
			
			$html.= '</table>';
			
//FIRMAS INSPECTOR Y MANAGER
			$html.= '<br><br><table border="0" cellspacing="0" cellpadding="5">';

			$html.= '<tr>';
			
			$html.= '<th align="center" width="30%">';
				if($info[0]['inspector_signature']){
					$html.= '<img src="'.$info[0]['inspector_signature'] . '" border="0" width="50" height="50" />';
				}
			$html.= '</th>';
			
			$html.= '<th align="center" width="40%"></th>';
			
			$html.= '<th align="center" width="30%">';
				if($info[0]['manager_signature']){
					$html.= '<img src="' . $info[0]['manager_signature'] . '" border="0" width="50" height="50" />';
				}
			$html.= '</th>';
		
			$html.= '</tr>';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
						<th align="center"><strong>Inspector: </strong>' . $info[0]['inspector'] . '</th>
						<th align="center" ></th>
						<th align="center"><strong>Manager: </strong>' . $info[0]['manager'] . '</th>';
			$html.= '</tr>';
						
			$html.= '</table>';

echo $html;
						
?>