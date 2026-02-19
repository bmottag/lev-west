<?php
	// create some HTML content	
	$html = '<h2 align="center" style="color:#337ab7;">PPE INSPECTION PROGRAM</h2>';
	
//fecha y observacion
			$html.= '<p></p>
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
			
			<table border="0" cellspacing="0" cellpadding="5">';

			$html.= '<tr>
						<th width="20%" bgcolor="#337ab7" style="color:white;"><strong>Observation: </strong></th>
						<th width="50%">' . $info[0]['observation'] . '</th>
						<th width="10%" bgcolor="#337ab7" style="color:white;"><strong>Date: </strong></th>
						<th align="center" width="20%" >' . $info[0]['date_ppe_inspection'] . '</th>';
			$html.= '</tr>';
			
						
			$html.= '</table><br><br>';
	
if($ppeInspectionWorkers){
	
	$html .= '<table border="0" cellspacing="0" cellpadding="5">';
			
			$html.='<tr bgcolor="#337ab7" style="color:white;">
						<th align="center" width="18%">Employee name</th>
						<th align="center" width="13%">Steel toe boots</th>
						<th align="center" width="13%">Hard hat</th>
						<th align="center" width="13%">Reflective vest</th>
						<th align="center" width="13%">Safety glasses</th>
						<th align="center" width="13%">Gloves</th>
						<th align="center" width="18%">Signature</th>
					</tr>';
			
			foreach ($ppeInspectionWorkers as $data):
			
			$html.= '<tr>';
			$html.= '<th >' . $data["name"] . '</th>';
				if($data["safety_boots"] == 1){
					$html.= '<th align="center">Good</th>';
				}elseif($data["safety_boots"] == 2){
					$html.= '<th align="center">Bad</th>';
				}
				
				if($data["hart_hat"] == 1){
					$html.= '<th align="center">Good</th>';
				}elseif($data["hart_hat"] == 2){
					$html.= '<th align="center">Bad</th>';
				}
				
				if($data["reflective_vest"] == 1){
					$html.= '<th align="center">Good</th>';
				}elseif($data["reflective_vest"] == 2){
					$html.= '<th align="center">Bad</th>';
				}
				
				if($data["safety_glasses"] == 1){
					$html.= '<th align="center">Good</th>';
				}elseif($data["safety_glasses"] == 2){
					$html.= '<th align="center">Bad</th>';
				}
				
				if($data["gloves"] == 1){
					$html.= '<th align="center">Good</th>';
				}elseif($data["gloves"] == 2){
					$html.= '<th align="center">Bad</th>';
				}
				
			$html.= '<th align="center">';
				if($data['signature']){
					$html.= '<img src="' . $data['signature'] . '" border="0" width="50" height="50" />';
				}
			$html.= '</th>';
			
			$html.= '</tr>';
			
			endforeach;
						
			$html.= '</table>';
			
}
			
//FIRMA INSPECTOR
			$html.= '<br><br><table border="0" cellspacing="0" cellpadding="5">';

			$html.= '<tr>';
			
			$html.= '<th align="center" width="30%">';
				if($info[0]['inspector_signature']){
					$html.= '<img src="'.$info[0]['inspector_signature'] . '" border="0" width="50" height="50" />';
				}
			$html.= '</th>';
					
			$html.= '</tr>';
			
			$html.= '<tr bgcolor="#337ab7" style="color:white;">
						<th align="center"><strong>Inspector: </strong>' . $info[0]['name'] . '</th>';
			$html.= '</tr>';
						
			$html.= '</table>';

			
			
echo $html;
						
?>