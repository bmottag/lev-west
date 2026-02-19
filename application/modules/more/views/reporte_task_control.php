<?php
	// create some HTML content
	$html = '<h2 align="center" style="color:#337ab7;">COVID-19 | PSI SUPPLEMENT | TASK ASSESSMENT AND CONTROL</h2>
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
				<table cellspacing="0" cellpadding="5">
					<tr>
						<th bgcolor="#337ab7" style="color:white;"><strong>Company: </strong></th>
						<th >' . $info[0]['company_name'] . '</th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Superintendent name: </strong></th>
						<th >' . $info[0]['superintendent'] . '</th>
					</tr>
					<tr>
						<th bgcolor="#337ab7" style="color:white;"><strong>Work Location: </strong></th>
						<th >' . $info[0]['work_location']. '</th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Date: </strong></th>
						<th >' . $info[0]['date_task_control']. '</th>
					</tr>
					<tr>
						<th bgcolor="#337ab7" style="color:white;"><strong>Crew Size: </strong></th>
						<th >' . $info[0]['crew_size'] . '</th>
						<th bgcolor="#337ab7" style="color:white;"><strong>Job Code/Name: </strong></th>
						<th >' . $info[0]['crew_size'] . '</th>
					</tr>
					<tr>
						<th bgcolor="#337ab7" style="color:white;"><strong>Task: </strong></th>
						<th colspan="3" >' . $info[0]['task']. '</th>
					</tr>
				</table>';

	$html.= '<br><br>';
	$html .= '<table border="1" cellspacing="0" cellpadding="5">
			<tr bgcolor="#337ab7" style="color:white;">
				<th width="40%" align="center"><strong>Questions must be answered prior to performing work</strong></th>
				<th width="10%" align="center"><strong>Yes</strong></th>
				<th width="10%" align="center"><strong>No</strong></th>
				<th width="40%" align="center"><strong>Comments</strong></th>
			</tr>';
	$html.= '<tr>';					
	$html.= '<th >Can 6ft distancing be maintained between workers during the task?</th>';					
	if($info[0]['distancing'] == 1){
		$html.= '<th align="center"><strong>X</strong></th>
				 <th align="center"><strong></strong></th>';
	}else{
		$html.='<th align="center"><strong></strong></th>
				 <th align="center"><strong>X</strong></th>';													
	}
	$html.= '<th >' . $info[0]['distancing_comments']  . '</th>';
	$html.= '</tr>';

	$html.= '<tr>';					
	$html.= '<th >Workers can perform their tasks without sharing tools or equipment?</th>';					
	if($info[0]['sharing_tools'] == 1){
		$html.= '<th align="center"><strong>X</strong></th>
				 <th align="center"><strong></strong></th>';
	}else{
		$html.='<th align="center"><strong></strong></th>
				 <th align="center"><strong>X</strong></th>';													
	}
	$html.= '<th >' . $info[0]['sharing_tools_comments'] . '</th>';
	$html.= '</tr>';

	$html.= '<tr>';					
	$html.= '<th >All workers have the required PPE to safely perform their work? GLOVES ARE MANDATORY.</th>';
	if($info[0]['required_ppe'] == 1){
		$html.= '<th align="center"><strong>X</strong></th>
				 <th align="center"><strong></strong></th>';
	}else{
		$html.='<th align="center"><strong></strong></th>
				 <th align="center"><strong>X</strong></th>';													
	}
	$html.= '<th >' . $info[0]['required_ppe_comments'] . '</th>';
	$html.= '</tr>';

	$html.= '<tr>';					
	$html.= '<th >All workers have no signs or symptoms of being ill (i.e.: Sore throat, fever, dry cough, shortness of breath)?</th>';
	if($info[0]['symptoms'] == 1){
		$html.= '<th align="center"><strong>X</strong></th>
				 <th align="center"><strong></strong></th>';
	}else{
		$html.='<th align="center"><strong></strong></th>
				 <th align="center"><strong>X</strong></th>';													
	}
	$html.= '<th >' . $info[0]['symptoms_comments'] . '</th>';
	$html.= '</tr>';

	$html.= '<tr>';					
	$html.= '<th >Crew is aware of site COVID protocols for breaks, lunchrooms, washrooms, elevator use, etc. and practices for hygiene? </th>';
	if($info[0]['protocols'] == 1){
		$html.= '<th align="center"><strong>X</strong></th>
				 <th align="center"><strong></strong></th>';
	}else{
		$html.='<th align="center"><strong></strong></th>
				 <th align="center"><strong>X</strong></th>';													
	}
	$html.= '<th >' . $info[0]['protocols_comments']  . '</th>';
	$html.= '</tr>';			
	
	$html.= "</table>";

	$html.= '<p style="color:#ff0000;"><h4>Attention</h4>';
	$html.= 'If you have selected "NO" to any of the questions, STOP!<br>
		You must not start work until you have: 		
		<ul>
			<li>Developed additional mitigation strategies.</li>
			<li>Reviewed the mitigation strategies and they have been approved by the Superintendent.</li>
			<li>Ensured you are in compliance with Alberta OH&S regarding "Right to refuse dangerous work".</li>
		</ul>
		</p>';
	
	$html.= '<p><h4>Sample Mitigation Strategies</h4>';
	$html.= 'The mitigation strategies can include, but are not limited, to items such as…<br>
		You must not start work until you have:		
		<ul>
			<li>Splitting crew sizes</li>
			<li>Providing respirators and full-faceshields when distance cannot be maintained</li>
			<li>Utilizing additional equipment to maintain distancing</li>
			<li>Providing shielding to provide a barrier between workers</li>
			<li>Staggering breaks to prevent exposure</li>
			<li>Disinfecting tools that must be shared</li>
			<li>Cleaning offices lunch rooms and other common areas as per COVID-19 Cleaning schedule</li>
			<li>Social distancing of 6 feet required</li>
		</ul>
	</p>';
	
	$html.= '<p><h4>Mitigation Plan</h4>';
	$html.= 'Where an additional mitigation plan is required, it must be approved by the Superintendent(s) and 
	Project Manager(s). This includes where items have been highlighted for example splitting crews, 
	adjusting task, additional disinfectant steps, barriers, face shields, etc </p>';

	$html.= '<br><br>';
	$signature = "";
	$superintendentSignature = "";

	if($info[0]['supervisor_signature']){
		//$urlAdvisor = base_url($lista['signature']);
		$signature = '<img src="'.$info[0]['supervisor_signature'].'" border="0" width="70" height="70" />';
	}
	
	if($info[0]['superintendent_signature']){
		//$urlAdvisor = base_url($lista['signature']);
		$superintendentSignature = '<img src="'.$info[0]['superintendent_signature'].'" border="0" width="70" height="70" />';
	}

	$html.= '<table border="1" cellspacing="0" cellpadding="5">
			<tr>
				<th align="center" ><strong><p>Crew Supervisor</p></strong></th>
				<th align="center">' . $signature . '</th>
				<th align="center"></th>
				<th align="center" ><strong><p>Project Superintendent</p></strong></th>
				<th align="center">' . $superintendentSignature . '</th>
			</tr>
			<tr bgcolor="#337ab7" style="color:white;">
				<th align="center" ><strong>Name</strong></th>
				<th align="center"><strong>' . $info[0]['supervisor']. '</strong></th>
				<th align="center" ><strong></strong></th>
				<th align="center" ><strong>Name</strong></th>
				<th align="center"><strong>' . $info[0]['superintendent']. '</strong></th>
			</tr>
			</table>';

echo $html;
						
?>