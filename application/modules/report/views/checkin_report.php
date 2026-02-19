<?php
// create some HTML content	
$html = '<style>
			table {
				font-family: arial, sans-serif;
				border: 1px solid black;
				border-collapse: collapse;
				width: 100%;
			}

			td, th {
				border: 1px solid black;
				text-align: left;
				padding: 8px;
			}
			</style>';
				
//datos especificos
if($checkinList)
{ 
	$html.= '<h2><b>Date:</b> ' . $requestDate . '</h2>';
	$html.= '<table cellspacing="0" cellpadding="5">
				<tr>
					<th width="5%" bgcolor="#dde1da" style="color:#3e403e;text-align:center;"><strong>#</strong></th>
					<th width="15%" bgcolor="#dde1da" style="color:#3e403e;text-align:center;"><strong>Date</strong></th>
					<th width="20%" bgcolor="#dde1da" style="color:#3e403e;text-align:center;"><strong>Worker</strong></th>
					<th width="20%" bgcolor="#dde1da" style="color:#3e403e;text-align:center;"><strong>Phone Number</strong></th>
					<th width="20%" bgcolor="#dde1da" style="color:#3e403e;text-align:center;"><strong>Sign-In</strong></th>
					<th width="20%" bgcolor="#dde1da" style="color:#3e403e;text-align:center;"><strong>Sign-Out</strong></th>			
				</tr>';

				$x=0;
				foreach ($checkinList as $lista):
					$x++;
					$checkOut = $lista['checkout_time'];
					if($lista['checkout_time']=="0000-00-00 00:00:00"){
						$checkOut = "Still working";
					}
					$html.= '<tr>
								<th style="text-align:center;">' . $x. '</th>
								<th >' . $lista['checkin_date']. '</th>
								<th >' . $lista['worker_name']. '</th>
								<th >' . $lista['worker_movil']. '</th>
								<th >' . $lista['checkin_time']. '</th>
								<th >' . $checkOut. '</th>
							</tr>';
				endforeach;
	$html.= '</table>';


}
			
echo $html;


?>