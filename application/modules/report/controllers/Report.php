<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(FCPATH . 'vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Report extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("report_model");
	}

	/**
	 * Search
	 * @since 24/11/2016
	 * @author BMOTTAG
	 */
	public function payrollSearch()
	{
		$this->load->view("layout", $data);
	}

	/**
	 * Search by daterange safety reports
	 * @since 6/01/2017
	 * @author BMOTTAG
	 */
	public function searchByDateRange($modulo)
	{
		if (empty($modulo)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$this->load->model("general_model");

		$data['workersList'] = FALSE; //lista para filtrar e el payroll report de ADMIN
		$data['companyList'] = FALSE; //lista para filtrar en el hauling report
		$data['materialList'] = FALSE; //lista para filtrar en el hauling report
		$data['jobList'] = FALSE; //lista para filtrar en work order report
		$data['vehicleList'] = FALSE; //lista para filtrar en inspection report
		$data['trailerList'] = FALSE; //lista para filtrar en inspection report
		$data['truckList'] = FALSE; //lista para filtrar en hauling report
		$data['vehicleRequired'] = FALSE;

		switch ($modulo) {
			case 'payroll':
				$data["titulo"] = "<i class='fa fa-book fa-fw'></i> PAYROLL REPORT";
				break;
			case 'payrollByAdmin':
				$data["titulo"] = "<i class='fa fa-book fa-fw'></i> PAYROLL REPORT";
				//workers list
				$arrParam = array("state" => 1);
				$data['workersList'] = $this->general_model->get_user($arrParam); //workers list
				break;
			case 'safety':
				//job list
				$arrParam = array(
					"table" => "param_jobs",
					"order" => "job_description",
					"column" => "state",
					"id" => 1
				);
				$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list
				$data["titulo"] = "<i class='fa fa-life-saver fa-fw'></i> FLHA REPORT";
				break;
			case 'hauling':
				$data["titulo"] = "<i class='fa fa-truck fa-fw'></i> HAULING REPORT";

				//trucklist
				$data['truckList'] = $this->report_model->get_trucks();

				//workers list
				$arrParam = array("state" => 1);
				$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

				//job list
				$arrParam = array(
					"table" => "param_jobs",
					"order" => "job_description",
					"column" => "state",
					"id" => 1
				);
				$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list

				//company list
				$arrParam = array(
					"table" => "param_company",
					"order" => "company_name",
					"id" => "x"
				);
				$data['companyList'] = $this->general_model->get_basic_search($arrParam); //company list

				//material list
				$arrParam = array(
					"table" => "param_material_type",
					"order" => "material",
					"id" => "x"
				);
				$data['materialList'] = $this->general_model->get_basic_search($arrParam); //material list
				break;
			case 'dailyInspection':
				//workers list
				$arrParam = array("state" => 1);
				$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

				$arrParam["tipo"] = "daily";
				$data['vehicleList'] = $this->report_model->get_vehicle_by_type($arrParam); //vehicle's list
				$arrParam["tipo"] = "trailer";
				$data['trailerList'] = $this->report_model->get_vehicle_by_type($arrParam); //vehicle's list

				$data["titulo"] = "<i class='fa fa-search fa-fw'></i> PICKUPS & TRUCKS INSPECTION REPORT";
				break;
			case 'heavyInspection':
				//workers list
				$arrParam = array("state" => 1);
				$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

				$arrParam["tipo"] = "heavy";
				$data['vehicleList'] = $this->report_model->get_vehicle_by_type($arrParam); //vehicle's list
				$data["titulo"] = "<i class='fa fa-search fa-fw'></i> CONTRUCTION EQUIPMENT INSPECTION REPORT";
				break;
			case 'specialInspection':
				//workers list
				$arrParam = array("state" => 1);
				$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

				$arrParam["tipo"] = "special";
				$data['vehicleList'] = $this->report_model->get_vehicle_by_type($arrParam); //vehicle's list
				$data["titulo"] = "<i class='fa fa-search fa-fw'></i> SPECIAL EQUIPMENT INSPECTION REPORT";
				break;
			case 'workorder':
				//job list
				$arrParam = array(
					"table" => "param_jobs",
					"order" => "job_description",
					"column" => "state",
					"id" => 1
				);
				$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list
				$data["titulo"] = "<i class='fa fa-money fa-fw'></i> WORK ORDER REPORT";
				break;
			case 'maintenance':
				$arrParam = array("company_type" => true);
				$data['vehicleList'] = $this->report_model->get_vehicle_by_type($arrParam); //vehicle's list
				$data['vehicleRequired'] = 'required';
				$data["titulo"] = "<i class='fa fa-money fa-fw'></i> MAINTENANCE PROGRAM";
				break;
			case 'csep_report':
				//job list
				$arrParam = array(
					"table" => "param_jobs",
					"order" => "job_description",
					"column" => "state",
					"id" => 1
				);
				$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list
				$data["titulo"] = "<i class='fa fa-life-saver fa-fw'></i> CSEP Report";
				break;
			case 'near_miss':
				//job list
				$arrParam = array(
					"table" => "param_jobs",
					"order" => "job_description",
					"column" => "state",
					"id" => 1
				);
				$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list
				$data["titulo"] = "<i class='fa fa-ambulance fa-fw'></i> INCIDENCES - NEAR MISS REPORT";
				break;
			case 'incident':
				//job list
				$arrParam = array(
					"table" => "param_jobs",
					"order" => "job_description",
					"column" => "state",
					"id" => 1
				);
				$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list
				$data["titulo"] = "<i class='fa fa-ambulance fa-fw'></i> INCIDENCES - INCIDENT/ACCIDENT REPORT";
				break;
		}

		$data["view"] = "form_search";

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($this->input->post('from')) {
			$from =  $this->input->post('from');
			$to =  $this->input->post('to');
			$data['employee'] =  $this->input->post('employee');
			$data['employee'] = $data['employee'] == '' ? 'x' : $data['employee'];
			$data['from'] = formatear_fecha($from);
			$data['to'] = formatear_fecha($to);

			//le sumo un dia al dia final para que ingrese ese dia en la consulta
			$data['to'] = date('Y-m-d', strtotime('+1 day ', strtotime($data['to'])));

			$arrParam = array(
				"from" => $data['from'],
				"to" => $data['to'],
				"employee" => $data['employee']
			);

			switch ($modulo) {
				case 'payroll':
					$data['info'] = $this->report_model->get_payroll($arrParam);
					$data["view"] = "list_payroll";
					$data["modulo"] = $modulo;
					break;
				case 'payrollByAdmin':
					$data['info'] = $this->report_model->get_payroll($arrParam);
					$data["view"] = "list_payroll";
					$data["modulo"] = $modulo;
					break;
				case 'safety':
					$arrParam['jobId'] =  $this->input->post('jobName');
					$arrParam['jobId'] = $arrParam['jobId'] == '' ? 'x' : $arrParam['jobId'];
					$data['jobId'] = $arrParam['jobId'];

					$data['info'] = $this->report_model->get_safety($arrParam);
					$data["view"] = "list_safety";
					break;
				case 'hauling':
					$arrParam['jobId'] =  $this->input->post('jobName');
					$arrParam['jobId'] = $arrParam['jobId'] == '' ? 'x' : $arrParam['jobId'];
					$data['jobId'] = $arrParam['jobId'];

					$arrParam['company'] =  $this->input->post('company');
					$arrParam['company'] = $arrParam['company'] == '' ? 'x' : $arrParam['company'];
					$data['company'] = $arrParam['company'];

					$arrParam['material'] =  $this->input->post('material');
					$arrParam['material'] = $arrParam['material'] == '' ? 'x' : $arrParam['material'];
					$data['material'] = $arrParam['material'];

					$arrParam['vehicleId'] =  $this->input->post('truck');
					$arrParam['vehicleId'] = $arrParam['vehicleId'] == '' ? 'x' : $arrParam['vehicleId'];
					$data['vehicleId'] = $arrParam['vehicleId'];

					$data['info'] = $this->report_model->get_hauling($arrParam);
					$data["view"] = "list_hauling";
					break;
				case 'dailyInspection':
					$arrParam['vehicleId'] =  $this->input->post('vehicleId');
					$arrParam['vehicleId'] = $arrParam['vehicleId'] == '' ? 'x' : $arrParam['vehicleId'];
					$data['vehicleId'] = $arrParam['vehicleId'];

					$arrParam['trailerId'] =  $this->input->post('trailerId');
					$arrParam['trailerId'] = $arrParam['trailerId'] == '' ? 'x' : $arrParam['trailerId'];
					$data['trailerId'] = $arrParam['trailerId'];

					$data['info'] = $this->report_model->get_daily_inspection($arrParam);

					$data["view"] = "list_daily_inspection";
					break;
				case 'heavyInspection':
					$arrParam['vehicleId'] =  $this->input->post('vehicleId');
					$arrParam['vehicleId'] = $arrParam['vehicleId'] == '' ? 'x' : $arrParam['vehicleId'];
					$data['vehicleId'] = $arrParam['vehicleId'];

					$data['info'] = $this->report_model->get_heavy_inspection($arrParam);
					$data["view"] = "list_heavy_inspection";
					break;
				case 'specialInspection':
					$arrParam['vehicleId'] =  $this->input->post('vehicleId');
					$arrParam['vehicleId'] = $arrParam['vehicleId'] == '' ? 'x' : $arrParam['vehicleId'];
					$data['vehicleId'] = $arrParam['vehicleId'];

					$data['infoWaterTruck'] = $this->report_model->get_water_truck_inspection($arrParam); //info de water truck
					$data['infoHydrovac'] = $this->report_model->get_hydrovac_inspection($arrParam); //info de hydrovac
					$data['infoSweeper'] = $this->report_model->get_sweeper_inspection($arrParam); //info de sweeper
					$data['infoGenerator'] = $this->report_model->get_generator_inspection($arrParam); //info de generador

					$data["view"] = "list_special_inspection";
					break;
				case 'workorder':
					$arrParam['jobId'] =  $this->input->post('jobName');
					$arrParam['jobId'] = $arrParam['jobId'] == '' ? 'x' : $arrParam['jobId'];
					$data['jobId'] = $arrParam['jobId'];

					$data['info'] = $this->report_model->get_workorder($arrParam);
					$data["view"] = "list_workorder";
					break;
				case 'maintenance':
					$arrParam['vehicleId'] =  $this->input->post('vehicleId');
					$data['info'] = $this->report_model->get_maintenance($arrParam);
					$data["view"] = "list_maintenance";
					break;
				case 'csep_report':
					$arrParam['jobId'] =  $this->input->post('jobName');
					$arrParam['jobId'] = $arrParam['jobId'] == '' ? 'x' : $arrParam['jobId'];
					$data['jobId'] = $arrParam['jobId'];

					$data['info'] = $this->report_model->get_csep($arrParam);
					$data["view"] = "list_csep_report";
					break;
				case 'near_miss':
					$arrParam['jobId'] =  $this->input->post('jobName');
					$arrParam['jobId'] = $arrParam['jobId'] == '' ? 'x' : $arrParam['jobId'];
					$data['jobId'] = $arrParam['jobId'];

					$data['info'] = $this->report_model->get_near_miss($arrParam);
					$data["view"] = "list_near_miss";
					break;
				case 'incident':
					$arrParam['jobId'] =  $this->input->post('jobName');
					$arrParam['jobId'] = $arrParam['jobId'] == '' ? 'x' : $arrParam['jobId'];
					$data['jobId'] = $arrParam['jobId'];

					$data['info'] = $this->report_model->get_incident($arrParam);
					$data["view"] = "list_incident";
					break;
			}
		}

		$this->load->view("layout", $data);
	}

	/**
	 * timeshift using reportico----se dejo de usar, se hicieron las vistas y las salidas en PDF con codeigniter
	 * @since 17/11/2016
	 * @author BMOTTAG
	 */
	public function timeshift()
	{
		$this->load->view("report_timeshift");
	}

	/**
	 * Generate Safety Report in PDF
	 * @param date $from
	 * @param date $to
	 * @since 7/01/2017
	 * @review 28/01/2017
	 * @author BMOTTAG
	 */
	public function generaSafetyPDF($jobId, $from, $to, $idSafety = 'x')
	{
		$this->load->library('Pdf');
		set_time_limit(60);
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Safety Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);


		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$data['from'] = $from;
		$data['to'] = $to;

		$jobId = $jobId == 'x' ? '' : $jobId;
		$arrParam = array(
			"from" => $from,
			"to" => $to,
			"jobId" => $jobId,
			"idSafety" => $idSafety
		);

		$info = $this->report_model->get_safety($arrParam);
		$idSafety = $info[0]['id_safety'];
		$job_description = $info[0]['job_description'];
		$fileName = 'flha_' . $job_description . '_' . $idSafety . '.pdf';

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table
		foreach ($info as $lista) :

			$hazards = $this->report_model->get_safety_hazard($lista['id_safety']);
			$safetyCovid = $this->report_model->get_safety_covid($idSafety); //safety covid
			$workers = $this->report_model->get_safety_workers($lista['id_safety']);
			$subcontractors = $this->report_model->get_safety_subcontractors($lista['id_safety']);

			// add a page
			$pdf->AddPage();

			$ppe = "No";
			if ($lista['ppe'] == 1) {
				$ppe = "Yes";
			}

			// create some HTML content
			$html = '<br><h1 align="center" style="color:#337ab7;">FIELD LEVEL HAZARD ASSESSMENT<br><br></h1>
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
									<th bgcolor="#337ab7" style="color:white;"><strong>Task(s) to be done: </strong></th>
									<th >' . $lista['work'] . '</th>
									<th bgcolor="#337ab7" style="color:white;"><strong>Date: </strong></th>
									<th >' . $lista['date'] . '</th>
								</tr>
								<tr>
									<th bgcolor="#337ab7" style="color:white;"><strong>PPE inspected: </strong></th>
									<th >' . $ppe . '</th>
									<th bgcolor="#337ab7" style="color:white;"><strong>Job Code/Name: </strong></th>
									<th >' . $lista['job_description'] . '</th>
								</tr>';

			if ($lista['specify_ppe']) {
				$html .= '<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Specialized PPE: </strong></th>
								<th colspan="3" >' . $lista['specify_ppe'] . '</th>
							</tr>';
			}

			$html .= '<tr>
									<th bgcolor="#337ab7" style="color:white;"><strong>Primary muster point: </strong></th>
									<th colspan="3" >' . $lista['muster_point'] . '</th>
								</tr>';
			if ($lista['muster_point_2']) {
				$html .= '<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Secondary muster point: </strong></th>
								<th colspan="3" >' . $lista['muster_point_2'] . '</th>
							</tr>';
			}
			if ($lista['primary_head_counter']) {
				$html .= '<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Primary head counter: </strong></th>
								<th colspan="3" >' . $lista['primary_head_counter'] . '</th>
							</tr>';
			}
			if ($lista['secondary_head_counter']) {
				$html .= '<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Secondary head counter: </strong></th>
								<th colspan="3" >' . $lista['secondary_head_counter'] . '</th>
							</tr>';
			}
			$html .= '</table>';


			//INICIO - Tabla de prioridades
			$html .= '<br><br>';
			$html .= '<table cellspacing="0" cellpadding="5" >
								<tr>
									<th bgcolor="#337ab7" style="color:white; text-align: center;" colspan="5" ><strong>Priority: High (6-8) Medium (4-5) Low (2-3) </strong></th>
								</tr>
								<tr>
									<th width="28%" bgcolor="#337ab7" style="color:white; text-align: center;" rowspan="2" ><strong>Frecuency </strong></th>

									<th width="72%" bgcolor="#337ab7" style="color:white; text-align: center;" colspan="4" ><strong>Severity </strong></th>
								</tr>';
			$html .= '<tr>
									<th width="18%" style="text-align: center;">1. No health impacts</th>
									<th width="18%" style="text-align: center;">2. Minor health impacts</th>
									<th width="18%" style="text-align: center;">3. Moderate / Reversible health impacts</th>
									<th width="18%" style="text-align: center;">4. Permanent consequences / Death </th>
						</tr>';
			$html .= '<tr>
									<th width="28%" >1. Not expected to occur</th>
									<th width="18%" style="text-align: center;" >2</th>
									<th width="18%" style="text-align: center;" >3</th>
									<th width="18%" style="text-align: center;" >4</th>
									<th width="18%" style="text-align: center;" >5</th>
						</tr>';
			$html .= '<tr>
									<th >2. Could occur once</th>
									<th style="text-align: center;" >3</th>
									<th style="text-align: center;" >4</th>
									<th style="text-align: center;" >5</th>
									<th style="text-align: center;" >6</th>
						</tr>';
			$html .= '<tr>
									<th >3. Could occur several time</th>
									<th style="text-align: center;" >4</th>
									<th style="text-align: center;" >5</th>
									<th style="text-align: center;" >6</th>
									<th style="text-align: center;" >7</th>
						</tr>';
			$html .= '<tr>
									<th >4. Could occur continuously</th>
									<th style="text-align: center;" >5</th>
									<th style="text-align: center;" >6</th>
									<th style="text-align: center;" >7</th>
									<th style="text-align: center;" >8</th>
						</tr>';
			$html .= '</table>';
			$html .= '<br><br>';
			//FIN - Tabla de prioridades

			$html .= '<table border="1" cellspacing="0" cellpadding="5">
						<tr>
							<th colspan="4"><strong><i>Identify and prioritize hazards below, then identify plans to eliminate/control the hazards</i></strong></th>
						</tr>
						<tr bgcolor="#337ab7" style="color:white;">
							<th width="5%" align="center"><strong>#</strong></th>
							<th width="25%" align="center"><strong>Activity</strong></th>
							<th width="20%" align="center"><strong>Hazard</strong></th>
							<th width="10%" align="center"><strong>Priority</strong></th>
							<th width="40%" align="center"><strong>Control/Eliminate</strong></th>
						</tr>';
			if (!$hazards) {
				$html .= '<tr>';
				$html .= '<th colspan="4" align="center"> ---- No data was found for Hazard -----</th>';
				$html .= '</tr>';
			} else {
				$i = 0;
				foreach ($hazards as $data) :
					$i++;
					$html .= '<tr>';
					$html .= '<th align="center">' . $i . '</th>';
					$html .= '<th >' . $data['hazard_activity'] . '</th>';
					$html .= '<th >' . $data['hazard_description'] . '</th>';
					$priority = $data['priority_description'] == "" ? "-" : $data['priority_description'];
					$html .= '<th align="center">' . $priority . '</th>';
					$html .= '<th >' . $data['solution']  . '</th>';
					$html .= '</tr>';
				endforeach;
			}
			$html .= "</table><br><br><br><br>";

			if ($safetyCovid) {
				$html .= '<table border="1" cellspacing="0" cellpadding="5">
								<tr bgcolor="#337ab7" style="color:white;">
									<th width="40%" align="center"><strong>Questions must be answered prior to performing work</strong></th>
									<th width="10%" align="center"><strong>Yes</strong></th>
									<th width="10%" align="center"><strong>No</strong></th>
									<th width="40%" align="center"><strong>Comments</strong></th>
								</tr>';
				$html .= '<tr>';
				$html .= '<th >Can 6ft distancing be maintained between workers during the task?</th>';
				if ($safetyCovid[0]['distancing'] == 1) {
					$html .= '<th align="center"><strong>X</strong></th>
									 <th align="center"><strong></strong></th>';
				} else {
					$html .= '<th align="center"><strong></strong></th>
									 <th align="center"><strong>X</strong></th>';
				}
				$html .= '<th >' . $safetyCovid[0]['distancing_comments']  . '</th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th >Workers can perform their tasks without sharing tools or equipment?</th>';
				if ($safetyCovid[0]['sharing_tools'] == 1) {
					$html .= '<th align="center"><strong>X</strong></th>
									 <th align="center"><strong></strong></th>';
				} else {
					$html .= '<th align="center"><strong></strong></th>
									 <th align="center"><strong>X</strong></th>';
				}
				$html .= '<th >' . $safetyCovid[0]['sharing_tools_comments'] . '</th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th >All workers have the required PPE to safely perform their work? GLOVES ARE MANDATORY.</th>';
				if ($safetyCovid[0]['required_ppe'] == 1) {
					$html .= '<th align="center"><strong>X</strong></th>
									 <th align="center"><strong></strong></th>';
				} else {
					$html .= '<th align="center"><strong></strong></th>
									 <th align="center"><strong>X</strong></th>';
				}
				$html .= '<th >' . $safetyCovid[0]['required_ppe_comments'] . '</th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th >All workers have no signs or symptoms of being ill (i.e.: Sore throat, fever, dry cough, shortness of breath)?</th>';
				if ($safetyCovid[0]['symptoms'] == 1) {
					$html .= '<th align="center"><strong>X</strong></th>
									 <th align="center"><strong></strong></th>';
				} else {
					$html .= '<th align="center"><strong></strong></th>
									 <th align="center"><strong>X</strong></th>';
				}
				$html .= '<th >' . $safetyCovid[0]['symptoms_comments'] . '</th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th >Crew is aware of site COVID protocols for breaks, lunchrooms, washrooms, elevator use, etc. and practices for hygiene? </th>';
				if ($safetyCovid[0]['protocols'] == 1) {
					$html .= '<th align="center"><strong>X</strong></th>
									 <th align="center"><strong></strong></th>';
				} else {
					$html .= '<th align="center"><strong></strong></th>
									 <th align="center"><strong>X</strong></th>';
				}
				$html .= '<th >' . $safetyCovid[0]['protocols_comments']  . '</th>';
				$html .= '</tr>';
				$html .= "</table>";

				$html .= '<p><h4>Sample Mitigation Strategies</h4>';
				$html .= 'The mitigation strategies can include, but are not limited, to items such as…<br>
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
			}

			if (!$workers) {
				$html .= 'No data was found for workers';
			} else {

				$html .= '<table border="1" cellspacing="0" cellpadding="5">';

				//pintar las firmas de a 4 por fila
				$total = count($workers); //contar numero de trabajadores
				$totalFilas  = 1;
				if ($total >= 4) { //si es mayor 4 entonces calcular cuantas filas deben ser
					$div = $total / 4;
					$totalFilas = ceil($div); //redondeo hace arriba
				}

				$n = 1;
				for ($i = 0; $i < $totalFilas; $i++) {
					$html .= '<tr>
										<th align="center" width="20%"><strong><p>Initials</p></strong></th>';

					$finish = $n * 4;
					$star = $finish - 4;
					if ($finish > $total) {
						$finish = $total;
					}
					$n++;

					for ($j = $star; $j < $finish; $j++) {

						$html .= '<th align="center" width="20%">';

						if ($workers[$j]['signature']) {
							//$url = base_url($workers[$j]['signature']);
							$html .= '<img src="' . $workers[$j]['signature'] . '" border="0" width="70" height="70" />';
						}
						$html .= '</th>';
					}

					$html .= '</tr>';

					$html .= '<tr bgcolor="#337ab7" style="color:white;">
										<th align="center"><strong>Company</strong></th>';
					for ($j = $star; $j < $finish; $j++) {
						$html .= '<th align="center"><strong>VCI</strong></th>';
					}
					$html .= '</tr>';

					$html .= '<tr bgcolor="#337ab7" style="color:white;">
										<th align="center"><strong>Worker Name</strong></th>';
					for ($j = $star; $j < $finish; $j++) {
						$html .= '<th align="center"><strong>' . $workers[$j]['name'] . '</strong></th>';
					}
					$html .= '</tr>';
				}

				$html .= '</table>';
			}

			$html .= '<br><br>';

			if ($subcontractors) {

				$html .= '<table border="1" cellspacing="0" cellpadding="5">';

				//pintar las firmas de a 4 por fila
				$total = count($subcontractors); //contar numero de trabajadores
				$totalFilas  = 1;
				if ($total >= 4) { //si es mayor 4 entonces calcular cuantas filas deben ser
					$div = $total / 4;
					$totalFilas = ceil($div); //redondeo hace arriba
				}

				$n = 1;
				for ($i = 0; $i < $totalFilas; $i++) {
					$html .= '<tr>
										<th align="center" width="20%"><strong><p>Initials</p></strong></th>';

					$finish = $n * 4;
					$star = $finish - 4;
					if ($finish > $total) {
						$finish = $total;
					}
					$n++;

					for ($j = $star; $j < $finish; $j++) {

						$html .= '<th align="center" width="20%">';

						if ($subcontractors[$j]['signature']) {
							//$url = base_url($subcontractors[$j]['signature']);
							$html .= '<img src="' . $subcontractors[$j]['signature'] . '" border="0" width="70" height="70" />';
						}
						$html .= '</th>';
					}

					$html .= '</tr>';

					$html .= '<tr bgcolor="#337ab7" style="color:white;">
										<th align="center"><strong>Company</strong></th>';
					for ($j = $star; $j < $finish; $j++) {
						$html .= '<th align="center"><strong>' . $subcontractors[$j]['company_name'] . '</strong></th>';
					}
					$html .= '</tr>';

					$html .= '<tr bgcolor="#337ab7" style="color:white;">
										<th align="center"><strong>Worker Name</strong></th>';
					for ($j = $star; $j < $finish; $j++) {
						$html .= '<th align="center"><strong>' . $subcontractors[$j]['worker_name'] . '</strong></th>';
					}
					$html .= '</tr>';
				}

				$html .= '</table>';
			}

			$html .= '<br><br>';
			$signature = "";

			if ($lista['signature']) {
				//$urlAdvisor = base_url($lista['signature']);
				$signature = '<img src="' . $lista['signature'] . '" border="0" width="70" height="70" />';
			}

			$html .= '<table border="1" cellspacing="0" cellpadding="5" width="40%">
						<tr>
							<th align="center" ><strong><p>Meeting conducted by</p></strong></th>
							<th align="center">' . $signature . '</th>
						</tr>
						<tr bgcolor="#337ab7" style="color:white;">
							<th align="center" ><strong>Name</strong></th>
							<th align="center"><strong>' . $lista['name'] . '</strong></th>
						</tr>
						</table>';

			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');

		endforeach;

		// reset pointer to the last page
		$pdf->lastPage();

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output($fileName, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}


	/**
	 * Generate Hauling Report in PDF
	 * @param int $idCompany
	 * @param date $from
	 * @param date $to
	 * @param date $idHauling
	 * @since 8/01/2017
	 * @review 28/1/2017
	 * @review 1/2/2018
	 * @author BMOTTAG
	 */
	public function generaHaulingPDF($idCompany, $idMaterial, $from, $to, $idHauling = 'x')
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('App Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);


		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array(
			"company" => $idCompany,
			"material" => $idMaterial,
			"from" => $from,
			"to" => $to,
			"idHauling" => $idHauling
		);
		$info = $this->report_model->get_hauling($arrParam);

		if (!$info || !is_array($info) || count($info) == 0) {
			show_error('ERROR!!! - No hauling data found for the selected parameters.');
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table
		foreach ($info as $lista) :

			// add a page
			$pdf->AddPage();

			$titlePlate = "Plate Number";
			$unitNumber = $lista['plate'];
			//si la empresa es VCI entonces debe venir con un vehiculo de la base de datos, sino se le coloco el numero
			if ($lista["fk_id_company"] == 1) {
				$titlePlate = "Plate Number";
				$unitNumber = $lista['unit_number'];
			}


			// create some HTML content
			$html = '<br><h1 align="center" style="color:#337ab7;">HAULING REPORT<br><br></h1>
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
								<th bgcolor="#337ab7" style="color:white;"><strong>Number: </strong></th>
								<th>' . $lista['id_hauling'] . '</th>
								<th bgcolor="#337ab7" style="color:white;"><strong>Employee: </strong></th>
								<th>' . $lista['name'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Hauling done by: </strong></th>
								<th>' . $lista['company_name'] . '</th>
								<th bgcolor="#337ab7" style="color:white;"><strong>Date: </strong></th>
								<th>' . $lista['date_issue'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Truck - ' . $titlePlate . ': </strong></th>
								<th>' . $unitNumber . '</th>
								<th bgcolor="#337ab7" style="color:white;"><strong>Truck Type: </strong></th>
								<th>' . $lista['truck_type'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Material Type: </strong></th>
								<th>' . $lista['material'] . '</th>
								<th bgcolor="#337ab7" style="color:white;"><strong>Payment: </strong></th>
								<th>' . $lista['payment'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Job Code/Name: </strong></th>
								<th>' . $lista['site_from'] . '</th>
								<th bgcolor="#337ab7" style="color:white;"><strong>To Site: </strong></th>
								<th>' . $lista['site_to'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Time In: </strong></th>
								<th>' . $lista['time_in'] . '</th>
								<th bgcolor="#337ab7" style="color:white;"><strong>Time Out: </strong></th>
								<th>' . $lista['time_out'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#337ab7" style="color:white;"><strong>Comments: </strong></th>
								<th colspan="3">' . $lista['comments'] . '</th>
							</tr>
							

						</table>
						</p>';

			$signatureVCI = "";
			$signatureContractor = "";
			if ($lista['vci_signature']) {
				//$urlVCI = base_url($lista['vci_signature']);
				$signatureVCI = '<img src="' . $lista['vci_signature'] . '" border="0" width="100" height="100" />';
			}

			if ($lista['contractor_signature']) {
				//$urlContractor = base_url($lista['contractor_signature']);
				$signatureContractor = '<img src="' . $lista['contractor_signature'] . '" border="0" width="100" height="100" />';
			}

			$html .= '<table border="0" cellspacing="0" cellpadding="5">
						<tr bgcolor="#337ab7" style="color:white;">
							<th align="center" colspan="3"><strong>Signatures</strong></th>
						</tr>
						<tr>
							<th width="35%" align="center">' . $signatureVCI . '</th>
							<th width="30%"></th>
							<th width="35%" align="center">' . $signatureContractor . '</th>
						</tr>
						
						<tr bgcolor="#337ab7" style="color:white;">
							<th align="center"><strong>VCI Representative<br>' . $lista['name'] . '</strong></th>
							<th ></th>
							<th align="center"><strong>Contractor</strong></th>
						</tr>
						</table>';


			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells
		endforeach;

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('hauling_' . $idHauling . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Inspection Daily Report in PDF
	 * @param int $idEmployee
	 * @param date $from
	 * @param date $to
	 * @param date $idInspection
	 * @since 8/01/2017
	 * @since 31/01/2018
	 * @review 28/01/2017
	 * @author BMOTTAG
	 */
	public function generaInsectionDailyPDF($idEmployee, $idVehicle, $idTrailer, $from, $to, $idInspection = 'x')
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Inspection Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 7);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array(
			"employee" => $idEmployee,
			"vehicleId" => $idVehicle,
			"trailerId" => $idTrailer,
			"from" => $from,
			"to" => $to,
			"idInspection" => $idInspection
		);
		$info = $this->report_model->get_daily_inspection($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table
		foreach ($info as $lista) :

			// add a page
			$pdf->AddPage();

			switch ($lista['type_level_1']) {
				case 1:
					$type1 = 'Fleet';
					break;
				case 2:
					$type1 = 'Rental';
					break;
				case 99:
					$type1 = 'Other';
					break;
			}


			//bandera para saber el tipo de inpeccion si es TRUCK o PICKUP
			$inspectionType = $lista["inspection_type"];
			$truck = FALSE; //cargo bandera para utilizarla en los campos que son para TRUCK -> inpection type 3
			$title = "PICKUP";
			if ($inspectionType == 3) {
				$truck = TRUE;
				$title = "TRUCK";
			}
			//FIN BANDERA

			// create some HTML content
			$html = '<h1 align="center" style="color:#337ab7;">' . $title . ' INSPECTION REPORT</h1>
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
						<table border="1" cellspacing="0" cellpadding="5">
							<tr bgcolor="#337ab7" style="color:white;">
								<th><strong>Type: </strong><br>' . $type1 . ' - ' . $lista['type_2'] . '</th>
								<th><strong>Make: </strong><br>' . $lista['make'] . '</th>
								<th><strong>Model: </strong><br>' . $lista['model'] . '</th>
								<th><strong>Unit Number: </strong><br>' . $lista['unit_number'] . '</th>
								<th><strong>Hours/Kilometers: </strong><br>' . $lista['hours'] . '</th>
								<th><strong>Date & Time: </strong><br>' . $lista['date_issue'] . '</th>
							</tr>
						</table>
						<br><br>';

			$html .= '<table border="1" cellspacing="0" cellpadding="5">
							<tr bgcolor="#337ab7" style="color:white;">
								<th align="center" width="30%"><strong>Items to Check</strong></th>
								<th align="center" width="10%"><strong>Pass</strong></th>
								<th align="center" width="10%"><strong>Fail</strong></th>
								<th align="center" width="30%"><strong>Items to Check</strong></th>
								<th align="center" width="10%"><strong>Pass</strong></th>
								<th align="center" width="10%"><strong>Fail</strong></th>
							</tr>';

			$html .= '<tr bgcolor="#337ab7" style="color:white;">
								<th align="center" colspan="3"><strong>ENGINE</strong></th>
								<th align="center" colspan="3"><strong>LIGHTS</strong></th>
								</tr>';

			$html .= '<tr>
								<th align="center"><strong>Belts/Hoses</strong></th>';
			if ($lista["belt"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["belt"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Head Lamps</strong></th>';
			if ($lista["head_lamps"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["head_lamps"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Power Steering Fluid</strong></th>';
			if ($lista["power_steering"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["power_steering"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Hazard Lights</strong></th>';
			if ($lista["hazard_lights"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["hazard_lights"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Oil Level</strong></th>';
			if ($lista["oil_level"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["oil_level"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Tail Lights</strong></th>';
			if ($lista["bake_lights"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["bake_lights"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Coolant Level</strong></th>';
			if ($lista["coolant_level"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["coolant_level"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Work Lights</strong></th>';
			if ($lista["work_lights"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["work_lights"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Coolant/Oil Leaks</strong></th>';
			if ($lista["water_leaks"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["water_leaks"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Turn signals lights</strong></th>';
			if ($lista["turn_signals"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["turn_signals"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$change = '';
			if ($truck) {
				$change = 'rowspan="2"';
			}
			$html .= '<tr>';

			$html .= '<th align="center"><strong>DEF Level:</strong></th>';
			$html .= '<th colspan="2" align="center"><strong>' . $lista["def"] . ' %</strong></th>';

			$html .= '<th align="center"><strong>Beacon Light:</strong></th>';
			if ($lista["beacon_light"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["beacon_light"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			if ($truck) {
				$html .= '<tr>';

				$html .= '<th align="center"><strong>Clearance Lights</strong></th>';
				if ($lista["clearance_lights"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["clearance_lights"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}

				$html .= '</tr>';
			}

			$html .= '<tr bgcolor="#337ab7" style="color:white;">
								<th align="center" colspan="3"><strong>SERVICE</strong></th>
								<th align="center" colspan="3"><strong>EXTERIOR</strong></th>
								</tr>';

			$html .= '<tr>
								<th align="center"><strong>Brake pedal</strong></th>';
			if ($lista["brake_pedal"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["brake_pedal"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Tires/Lug Nuts/Pressure</strong></th>';
			if ($lista["nuts"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["nuts"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Emergency brake</strong></th>';
			if ($lista["emergency_brake"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["emergency_brake"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Glass (All) & Mirror</strong></th>';
			if ($lista["glass"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["glass"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Gauges: Volt/Fuel/Temp/Oil</strong></th>';
			if ($lista["gauges"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["gauges"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Clean exterior</strong></th>';
			if ($lista["clean_exterior"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["clean_exterior"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Electrical & Air Horn</strong></th>';
			if ($lista["horn"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["horn"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Wipers/Washers</strong></th>';
			if ($lista["wipers"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["wipers"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Seatbelts </strong></th>';
			if ($lista["seatbelts"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["seatbelts"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Backup Beeper </strong></th>';
			if ($lista["backup_beeper"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["backup_beeper"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Driver & Passenger seat</strong></th>';
			if ($lista["driver_seat"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["driver_seat"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Driver and Passenger door</strong></th>';
			if ($lista["passenger_door"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["passenger_door"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Insurance information</strong></th>';
			if ($lista["insurance"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["insurance"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '<th align="center"><strong>Decals</strong></th>';
			if ($lista["proper_decals"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["proper_decals"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Registration</strong></th>';
			if ($lista["registration"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["registration"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			if ($truck) {
				$html .= '<th align="center" colspan="3" rowspan="2"><strong></strong></th>';
			}
			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Clean interior</strong></th>';
			if ($lista["clean_interior"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["clean_interior"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}

			$html .= '</tr>';

			$html .= '<tr bgcolor="#337ab7" style="color:white;">
								<th align="center" colspan="3"><strong>SAFETY</strong></th>';

			$grease = "";
			if ($truck) {
				$grease = "GREASING";
			}
			$html .= '<th align="center" colspan="3">' . $grease . '<strong></strong></th>
								</tr>';

			$html .= '<tr>
								<th align="center"><strong>Fire extinguisher</strong></th>';
			if ($lista["fire_extinguisher"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["fire_extinguisher"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			if ($truck) {
				$html .= '<th align="center"><strong>Steering Axle</strong></th>';
				if ($lista["steering_axle"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["steering_axle"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}
			} else {
				$html .= '<th colspan="3" rowspan="4"></th>';
			}
			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>First Aid</strong></th>';
			if ($lista["first_aid"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["first_aid"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			if ($truck) {
				$html .= '<th align="center"><strong>Drives Axles</strong></th>';
				if ($lista["drives_axle"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["drives_axle"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}
			}
			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Emergency Kit</strong></th>';
			if ($lista["emergency_reflectors"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["emergency_reflectors"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			if ($truck) {
				$html .= '<th align="center"><strong>Front drive shaft</strong></th>';
				if ($lista["grease_front"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["grease_front"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}
			}
			$html .= '</tr>';

			$html .= '<tr>
								<th align="center"><strong>Spill Kit</strong></th>';
			if ($lista["spill_kit"] == 1) {
				$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
			} elseif ($lista["spill_kit"] == 0) {
				$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
			} else {
				$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
			}
			if ($truck) {
				$html .= '<th align="center"><strong>Back drive shaft</strong></th>';
				if ($lista["grease_end"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["grease_end"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}
			}
			$html .= '</tr>';

			if ($truck) {
				$html .= '<tr>';
				$html .= '<th colspan="3" rowspan="2"></th>';

				$html .= '<th align="center"><strong>Grease 5th wheel</strong></th>';
				if ($lista["grease"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["grease"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}


				$html .= '</tr>';

				$html .= '<tr>';

				$html .= '<th align="center"><strong>Box hoist & hinge</strong></th>';
				if ($lista["hoist"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["hoist"] == 0) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}

				$html .= '</tr>';
			}

			$html .= '<tr>';
			$html .= '<th colspan="6"><strong>Comments : </strong>' . $lista["comments"] . '</th>';
			$html .= '</tr>';


			if ($lista["with_trailer"] == 1) {
				$html .= '<tr bgcolor="#337ab7" style="color:white;">
								<th colspan="6"><strong>TRAILER :  ' . $lista["trailer"] . '</strong></th>
							 </tr>';

				$html .= '<tr>
								<th align="center"><strong>Lights</strong></th>';
				if ($lista["trailer_lights"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["trailer_lights"] == 2) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}

				$html .= '<th align="center"><strong>Tires</strong></th>';
				if ($lista["trailer_tires"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["trailer_tires"] == 2) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}
				$html .= '</tr>';

				$html .= '<tr>
								<th align="center"><strong>Clean</strong></th>';
				if ($lista["trailer_clean"] == 1) {
					$html .= '<th align="center"><strong>Y</strong></th>
										 <th align="center"><strong></strong></th>';
				} elseif ($lista["trailer_clean"] == 2) {
					$html .= '<th align="center"><strong></strong></th>
										 <th align="center"><strong>X</strong></th>';
				} else {
					$html .= '<th align="center"><strong>N/A</strong></th>
										 <th align="center"><strong>N/A</strong></th>';
				}

				$html .= '<th align="center"><strong>Slings</strong></th>';
				$html .= '<th colspan="2" align="center"><strong>' . $lista["trailer_slings"] . '</strong></th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th align="center"><strong>Chains</strong></th>';
				$html .= '<th colspan="2" align="center"><strong>' . $lista["trailer_chains"] . '</strong></th>';

				$html .= '<th align="center"><strong>Ratchet</strong></th>';
				$html .= '<th colspan="2" align="center"><strong>' . $lista["trailer_ratchet"] . '</strong></th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th colspan="6"><strong>Comments : </strong>' . $lista["trailer_comments"] . '</th>';
				$html .= '</tr>';
			}

			$html .= '</table>';

			$signature = '';
			if ($lista['signature']) {
				//$urlSignature = base_url($lista['signature']);
				$signature = '<img src="' . $lista['signature'] . '" border="0" width="70" height="70" />';
			}
			$html .= '<br><br>';
			$html .= '<table border="1" cellspacing="0" cellpadding="5" width="40%">
						<tr>
							<th align="center" ><strong><p>Driver</p></strong></th>
							<th align="center">' . $signature . '</th>
						</tr>
						<tr bgcolor="#337ab7" style="color:white;">
							<th align="center" ><strong>Name</strong></th>
							<th align="center"><strong>' . $lista['name'] . '</strong></th>
						</tr>
						</table>';

			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');

		endforeach;
		// Print some HTML Cells


		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('daily_inspection.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Inspection Heavy Report in PDF
	 * @param int $idEmployee
	 * @param date $from
	 * @param date $to
	 * @param date $idInspection
	 * @since 9/01/2017
	 * @review 29/01/2017
	 * @review 1/02/2018
	 * @author BMOTTAG
	 */
	public function generaInsectionHeavyPDF($idEmployee, $idVehicle, $from, $to, $idInspection = 'x')
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Inspection Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array(
			"employee" => $idEmployee,
			"vehicleId" => $idVehicle,
			"from" => $from,
			"to" => $to,
			"idInspection" => $idInspection
		);
		$data['info'] = $this->report_model->get_heavy_inspection($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table
		$data['consecutivo'] = 0;
		$html = "";
		foreach ($data['info'] as $lista) :

			// add a page
			$pdf->AddPage();

			$html = $this->load->view($lista['form'], $data, true);

			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');

			$data['consecutivo']++;
		endforeach;
		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('heavy_inspection.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Inspection Specail Report in PDF
	 * @param int $idEmployee
	 * @param int $idVehicle
	 * @param date $from
	 * @param date $to
	 * @param date $idInspection
	 * @since 23/04/2017
	 * @review 1/02/2018
	 * @author BMOTTAG
	 */
	public function generaInsectionSpecialPDF($idEmployee, $idVehicle, $from, $to, $type, $idInspection = 'x')
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Inspection Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array(
			"employee" => $idEmployee,
			"vehicleId" => $idVehicle,
			"from" => $from,
			"to" => $to,
			"idInspection" => $idInspection
		);

		switch ($type) {
			case 'watertruck':
				$data['info'] = $this->report_model->get_water_truck_inspection($arrParam);
				break;
			case 'hydrovac':
				$data['info'] = $this->report_model->get_hydrovac_inspection($arrParam);
				break;
			case 'sweeper':
				$data['info'] = $this->report_model->get_sweeper_inspection($arrParam);
				break;
			case 'generator':
				$data['info'] = $this->report_model->get_generator_inspection($arrParam);
				break;
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table
		$data['consecutivo'] = 0;
		$html = "";
		foreach ($data['info'] as $lista) :

			// add a page
			$pdf->AddPage();

			$html = $this->load->view($lista['form'], $data, true);

			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');

			$data['consecutivo']++;
		endforeach;
		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('special_inspection.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Payroll Report in PDF
	 * @param int $idUser
	 * @param date $from
	 * @param date $to
	 * @since 16/01/2017
	 * @author BMOTTAG
	 */
	public function generaPayrollPDF($idUser, $from, $to)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 7);

		// add a page
		$pdf->AddPage();

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		//$idUser = $idUser=='x'?'':$idUser;
		$arrParam = array(
			"from" => $from,
			"to" => $to,
			"employee" => $idUser
		);

		$info = $this->report_model->get_payroll($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// create some HTML content
		$html = '<h1 align="center" style="color:#337ab7;">PAYROLL REPORT</h1>
					<p style="color:#337ab7;"><strong><i>From Date: </i></strong>' . $from . '<br>
					<strong><i>To Date: </i></strong>' . $to . '</p>
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

				<table border="0" cellspacing="1" cellpadding="3">
					<tr bgcolor="#337ab7" style="color:white;">
						<th rowspan="2" align="center" width="12%"><strong>Employee Name</strong></th>
						<th colspan="2" align="center" width="17%"><strong>Date & Time</strong></th>
						<th colspan="2" align="center" width="20%"><strong>Job</strong></th>
						<th rowspan="2" align="center" width="8%"><strong>Working Hours</strong></th>
						<th rowspan="2" align="center" width="7%"><strong>Total Hours</strong></th>
						<th rowspan="2" align="center" width="15%"><strong>Task description</strong></th>
						<th rowspan="2" align="center" width="21%"><strong>Observation</strong></th>
					</tr>
					<tr bgcolor="#337ab7" style="color:white;">
						<th align="center"><strong>In</strong></th>
						<th align="center"><strong>Out</strong></th>
						<th align="center"><strong>Start</strong></th>
						<th align="center"><strong>Finish</strong></th>
					</tr>
					';

		$total = 0;
		foreach ($info as $data) :
			$html .= '<tr>';
			$html .= '<th >' . $data['name'] . '</th>';

			$html .= '<th align="center">' . $data['start'] . '</th>';
			$html .= '<th align="center">' . $data['finish'] . '</th>';
			$html .= '<th>' . $data['job_start'] . '</th>';
			$html .= '<th>' . $data['job_finish'] . '</th>';
			$html .= '<th align="center">' . substr($data['working_hours_new'], 0, 5)  . '</th>';

			$parts = explode(':', $data['working_hours_new']);
			$total += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
			$hours = floor($total / 3600);
			$mins = floor(($total / 60) % 60);

			//$total = $data['working_hours'] + $total;
			$html .= '<th align="center">' . sprintf('%02d:%02d', $hours, $mins) . '</th>';
			$html .= '<th>' . $data['task_description'] . '</th>';
			$html .= '<th>' . $data['observation'] . '</th>';
			$html .= '</tr>';
		endforeach;

		$html .= "</table><br><br>";




		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells


		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('payroll.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Work Order Report in PDF
	 * @param int $idWorkOrder
	 * @since 26/01/2017
	 * @author BMOTTAG
	 */
	public function generaWorkOrderPDF($idWorkOrder)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Work Order Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// add a page
		$pdf->AddPage();

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam['idWorkOrder'] = $idWorkOrder;
		$workOrder = $this->report_model->get_workorder($arrParam);

		$workorderPersonal = $this->report_model->get_workorder_personal($idWorkOrder); //workorder personal list
		$workorderMaterials = $this->report_model->get_workorder_materials($idWorkOrder); //workorder material list
		$workorderEquipment = $this->report_model->get_workorder_equipment($idWorkOrder); //workorder equipment list

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// create some HTML content
		$html = '<h1 style="color:#337ab7;">WORK ORDER</h1><p></p><p>
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
								<th bgcolor="#dddddd" ><strong>Work Order #: </strong></th>
								<th >' . $workOrder[0]['id_workorder'] . '</th>
								<th bgcolor="#dddddd" ><strong>Date work order: </strong></th>
								<th >' . $workOrder[0]['date'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#dddddd" ><strong>Supervisor: </strong></th>
								<th >' . $workOrder[0]['name'] . '</th>
								<th bgcolor="#dddddd" ><strong>Date of Issue: </strong></th>
								<th >' . $workOrder[0]['date_issue'] . '</th>
							</tr>
							<tr>
								<th bgcolor="#dddddd" ><strong>Observation: </strong></th>
								<th colspan="3" >' . $workOrder[0]['observation'] . '</th>
							</tr>
						</table>
					</p>';

		$html .= '<table border="1" cellspacing="0" cellpadding="5">
					<tr>
						<th colspan="5"><strong>PERSONNEL</strong></th>
					</tr>
					<tr bgcolor="#dddddd">
						<th width="5%" align="center"><strong>#</strong></th>
						<th width="40%" align="center"><strong>Employee Name</strong></th>
						<th width="15%" align="center"><strong>Employee Type</strong></th>
						<th width="10%" align="center"><strong>Hours</strong></th>
						<th width="20%" align="center"><strong>Description</strong></th>
					</tr>';
		if (!$workorderPersonal) {
			$html .= '<tr>';
			$html .= '<th colspan="5" align="center"> ---- No data was found for Personal -----</th>';
			$html .= '</tr>';
		} else {
			$i = 0;
			foreach ($workorderPersonal as $data) :
				$i++;
				$html .= '<tr>';
				$html .= '<th align="center">' . $i . '</th>';
				$html .= '<th>' . $data['name'] . '</th>';
				$html .= '<th align="center">' . $data['type']  . '</th>';
				$html .= '<th align="center">' . $data['hours']  . '</th>';
				$html .= '<th >' . $data['description']  . '</th>';
				$html .= '</tr>';
			endforeach;
		}
		$html .= "</table><br><br>";

		$html .= '<table border="1" cellspacing="0" cellpadding="5">
					<tr>
						<th colspan="4"><strong>MATERIALS</strong></th>
					</tr>
					<tr bgcolor="#dddddd">
						<th width="5%" align="center"><strong>#</strong></th>
						<th width="25%" align="center"><strong>Material</strong></th>
						<th width="15%" align="center"><strong>Quantity</strong></th>
						<th width="35%" align="center"><strong>Description</strong></th>
					</tr>';
		if (!$workorderMaterials) {
			$html .= '<tr>';
			$html .= '<th colspan="5" align="center"> ---- No data was found for Personal -----</th>';
			$html .= '</tr>';
		} else {
			$i = 0;
			foreach ($workorderMaterials as $data) :
				$i++;
				$html .= '<tr>';
				$html .= '<th align="center">' . $i . '</th>';
				$html .= '<th>' . $data['material'] . '</th>';
				$html .= '<th>' . $data['quantity']  . '</th>';
				$html .= '<th >' . $data['description']  . '</th>';
				$html .= '</tr>';
			endforeach;
		}
		$html .= "</table><br><br>";









		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');



		// reset pointer to the last page
		$pdf->lastPage();

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('work_order.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Payroll Report in XLS
	 * @param int $idUser
	 * @param date $from
	 * @param date $to
	 * @since 27/01/2017
	 * @author BMOTTAG
	 */
	public function generaPayrollXLS($idUser, $from, $to)
	{
		$fechaActual = date('Y-m-d');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=payroll_' . $fechaActual . '.xlsx');

		$data['from'] = $from;
		$data['to'] = $to;

		//$idUser = $idUser=='x'?'':$idUser;
		$arrParam = array(
			"from" => $from,
			"to" => $to,
			"employee" => $idUser
		);

		$info = $this->report_model->get_payroll($arrParam);

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Consolidado');

		// Create a first sheet
		$dateRange = $from . '-' . $to;

		$spreadsheet->getActiveSheet(0)->setCellValue('A1', 'PAYROLL REPORT');

		$spreadsheet->getActiveSheet(0)->setCellValue('A2', 'Date Range:')
			->setCellValue('B2', $dateRange);

		$spreadsheet->getActiveSheet(0)->setCellValue('A4', 'Employee Name')
			->setCellValue('B4', 'Time In')
			->setCellValue('C4', 'Time Out')
			->setCellValue('D4', 'Job Start')
			->setCellValue('E4', 'Job Finish')
			->setCellValue('F4', 'Task Description')
			->setCellValue('G4', 'Observation')
			->setCellValue('H4', 'Working Hours')
			->setCellValue('I4', 'Total Hours');

		$j = 5;
		$total = 0;
		foreach ($info as $data) :
			$total = $data['working_hours'] + $total;

			$parts = explode(':', $data['working_hours_new']);
			$total += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
			$hours = floor($total / 3600);
			$mins = floor(($total / 60) % 60);


			$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['name'])
				->setCellValue('B' . $j, $data['start'])
				->setCellValue('C' . $j, $data['finish'])
				->setCellValue('D' . $j, $data['job_start'])
				->setCellValue('E' . $j, $data['job_finish'])
				->setCellValue('F' . $j, $data['task_description'])
				->setCellValue('G' . $j, $data['observation'])
				->setCellValue('H' . $j, substr($data['working_hours_new'], 0, 5))
				->setCellValue('I' . $j, sprintf('%02d:%02d', $hours, $mins));
			$j++;
		endforeach;

		// Set column widths
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(23);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);

		// Add conditional formatting
		$spreadsheet->getActiveSheet()->getStyle('A1:I1')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A1:I4')->getFont()->setBold(true);

		$spreadsheet->getActiveSheet()->getStyle('A1:I4')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('A1:I4')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A1:I4')->getFill()->getStartColor()->setARGB('236e09');

		$spreadsheet->getActiveSheet()->getStyle('A1:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A1:I4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	/**
	 * Generate Hauling Report in XLS
	 * @param int $idCompany
	 * @param int $idMaterial
	 * @param date $from
	 * @param date $to
	 * @since 8/05/2017
	 * @author BMOTTAG
	 */
	public function generaHaulingXLS($idCompany, $idMaterial, $from, $to)
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=hauling_report_' . $from . '_' . $to . '.xlsx');

		$data['from'] = $from;
		$data['to'] = $to;

		$arrParam = array(
			"company" => $idCompany,
			"material" => $idMaterial,
			"from" => $from,
			"to" => $to
		);
		$info = $this->report_model->get_hauling($arrParam);

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Hauling Report');

		$spreadsheet->getActiveSheet(0)
			->setCellValue('A1', 'ID')
			->setCellValue('B1', 'Hauling done by')
			->setCellValue('C1', 'Employee')
			->setCellValue('D1', 'Truck - Unit Number')
			->setCellValue('E1', 'Truck Type')
			->setCellValue('F1', 'Plate')
			->setCellValue('G1', 'Material Type')
			->setCellValue('H1', 'Job Code/Name')
			// ->setCellValue('I1', 'To Site')
			->setCellValue('I1', 'Payment')
			->setCellValue('J1', 'Date of Issue')
			->setCellValue('K1', 'Time In')
			->setCellValue('L1', 'Time Out')
			->setCellValue('M1', 'Comments');

		$j = 2;
		foreach ($info as $data) :
			$spreadsheet->getActiveSheet()
				->setCellValue('A' . $j, $data['id_hauling'])
				->setCellValue('B' . $j, $data['company_name'])
				->setCellValue('C' . $j, $data['name'])
				->setCellValue('D' . $j, $data['unit_number'])
				->setCellValue('E' . $j, $data['truck_type'])
				->setCellValue('F' . $j, $data['plate'])
				->setCellValue('G' . $j, $data['material'])
				->setCellValue('H' . $j, $data['site_from'])
				->setCellValue('I' . $j, $data['site_to'])
				->setCellValue('J' . $j, $data['payment'])
				->setCellValue('K' . $j, $data['date_issue'])
				->setCellValue('L' . $j, $data['time_in'])
				->setCellValue('M' . $j, $data['time_out'])
				->setCellValue('N' . $j, $data['comments']);
			$j++;
		endforeach;

		// Set column widths
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(100);

		// Add conditional formatting
		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);

		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFill()->getStartColor()->setARGB('236e09');

		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	/**
	 * Generate Payroll Report in XLS
	 * @param int $jobId
	 * @param date $from
	 * @param date $to
	 * @since 30/01/2017
	 * @author BMOTTAG
	 */
	public function generaWorkOrderXLS($jobId, $from, $to)
	{
		$data['from'] = $from;
		$data['to'] = $to;

		$jobId = $jobId == 'x' ? '' : $jobId;
		$arrParam = array(
			"from" => $from,
			"to" => $to,
			"jobId" => $jobId
		);

		$info = $this->report_model->get_workorder($arrParam);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=workorder_report.xlsx');

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Work Order Report');

		$spreadsheet->getActiveSheet(0)->setCellValue('A1', 'Work Order #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Work Order Date')
			->setCellValue('E1', 'Job Code/Name')
			->setCellValue('F1', 'Work Done')
			->setCellValue('G1', 'Description')
			->setCellValue('H1', 'Employee Name')
			->setCellValue('I1', 'Employee Type')
			->setCellValue('J1', 'Material')
			->setCellValue('K1', 'Equipment')
			->setCellValue('L1', 'Hours')
			->setCellValue('M1', 'Quantity')
			->setCellValue('N1', 'Unit')
			->setCellValue('O1', 'Unit price')
			->setCellValue('P1', 'Operated by')
			->setCellValue('Q1', 'Line Total');

		$j = 2;
		foreach ($info as $data) :
			$idWorkOrder = $data['id_workorder'];
			$arrParam = array('idWorkOrder' => $idWorkOrder);
			$workorderPersonal = $this->report_model->get_workorder_personal($idWorkOrder); //workorder personal list
			$workorderMaterials = $this->report_model->get_workorder_materials($idWorkOrder); //workorder material list
			$workorderReceipt = $this->report_model->get_workorder_receipt($arrParam); //workorder ocasional list
			$workorderEquipment = $this->report_model->get_workorder_equipment($idWorkOrder); //workorder equipment list
			$workorderOcasional = $this->report_model->get_workorder_ocasional($idWorkOrder); //workorder ocasional list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderPersonal) {
				foreach ($workorderPersonal as $infoP) :
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $infoP['description'])
						->setCellValue('H' . $j, $infoP['name'])
						->setCellValue('I' . $j, $infoP['type'])
						->setCellValue('L' . $j, $infoP['hours'])
						->setCellValue('N' . $j, 'Hours')
						->setCellValue('O' . $j, $infoP['rate'])
						->setCellValue('Q' . $j, $infoP['value']);
					$j++;
				endforeach;
			}

			if ($workorderMaterials) {
				foreach ($workorderMaterials as $infoM) :
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $infoM['description'])
						->setCellValue('J' . $j, $infoM['material'])
						->setCellValue('M' . $j, $infoM['quantity'])
						->setCellValue('N' . $j, $infoM['unit'])
						->setCellValue('O' . $j, $infoM['rate'])
						->setCellValue('Q' . $j, $infoM['value']);
					$j++;
				endforeach;
			}

			if ($workorderReceipt) {
				foreach ($workorderReceipt as $infoR) :

					$description = $infoR['description'] . ' - ' . $infoR['place'];
					if ($infoR['markup'] > 0) {
						$description = $description . ' - Plus M.U.';
					}

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $description)
						->setCellValue('Q' . $j, $infoR['value']);
					$j++;
				endforeach;
			}

			if ($workorderEquipment) {
				foreach ($workorderEquipment as $infoE) :

					//si es tipo miscellaneous -> 8, entonces la description es diferente
					if ($infoE['fk_id_type_2'] == 8) {
						$equipment = $infoE['miscellaneous'] . " - " . $infoE['other'];
					} else {
						$equipment = $infoE['type_2'] . " - " . $infoE['unit_number'] . " - " . $infoE['v_description'];
					}

					$quantity = $infoE['quantity'] == 0 ? 1 : $infoE['quantity']; //cantidad si es cero es porque es 1

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $infoE['description'])
						->setCellValue('K' . $j, $equipment)
						->setCellValue('L' . $j, $infoE['hours'])
						->setCellValue('M' . $j, $quantity)
						->setCellValue('N' . $j, 'Hours')
						->setCellValue('O' . $j, $infoE['rate'])
						->setCellValue('P' . $j, $infoE['operatedby'])
						->setCellValue('Q' . $j, $infoE['value']);
					$j++;
				endforeach;
			}

			if ($workorderOcasional) {
				foreach ($workorderOcasional as $infoO) :
					$equipment = $infoO['company_name'] . '-' . $infoO['equipment'];
					$hours = $infoO['hours'] == 0 ? 1 : $infoO['hours'];

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $infoO['description'])
						->setCellValue('K' . $j, $equipment)
						->setCellValue('L' . $j, $hours)
						->setCellValue('M' . $j, $infoO['quantity'])
						->setCellValue('N' . $j, $infoO['unit'])
						->setCellValue('O' . $j, $infoO['rate'])
						->setCellValue('Q' . $j, $infoO['value']);
					$j++;
				endforeach;
			}

		endforeach;

		$spreadsheet->getActiveSheet()->getStyle('P' . $j . ':Q' . $j)->getFont()->setBold(true);

		// Set column widths							  
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(22);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(22);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(60);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(60);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(15);

		// Add conditional formatting
		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);

		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getFill()->getStartColor()->setARGB('236e09');

		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A1:Q1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	/**
	 * Info payroll con boton para editar horas
	 * @since 5/6/2018
	 * @author BMOTTAG
	 */
	public function botonEditHour($idPayroll)
	{
		if (empty($idPayroll)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$data["titulo"] = "<i class='fa fa-book fa-fw'></i> PAYROLL REPORT";
		$data["modulo"] = "payrollByAdmin";

		$arrParam = array("idPayroll" => $idPayroll);
		$data['info'] = $this->report_model->get_payroll($arrParam);
		$data["view"] = "list_payroll_v2";

		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Generate Report in PDF - Checkin report
	 * @param varchar $date
	 * @since 5/06/2022
	 * @author BMOTTAG
	 */
	public function checkinPDF($date)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Check-In report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$this->load->model("general_model");
		$data['requestDate'] = $date;
		$arrParam = array("today" => $date);
		$data['checkinList'] = $this->general_model->get_checkin($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("checkin_report", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();

		ob_end_clean();
		//Close and output PDF document
		$pdf->Output('check_in_' . $date . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Employee Bank Time List
	 * @since 11/9/2022
	 * @author BMOTTAG
	 */
	public function employeBankTime()
	{
		$idUser = $this->session->userdata("id");
		$arrParam = array("idUser" => $idUser);
		$this->load->model("general_model");
		$data['info'] = $this->general_model->get_bank_time($arrParam);
		$data["view"] = 'employee_bank_time';
		$this->load->view("layout_calendar", $data);
	}

	public function valvesReport()
	{
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "valves",
			"order" => "valve_number",
			"id" => "x"
		);
		$info = $this->general_model->get_basic_search($arrParam);

		ob_end_clean();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="valves_report.xlsx"');
		header('Cache-Control: max-age=0');
		
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Valves Report');

		$spreadsheet->getActiveSheet(0)->setCellValue('A1', 'Date Issued')
			->setCellValue('B1', 'Valve #s')
			->setCellValue('C1', '# of Turns')
			->setCellValue('D1', 'Position that valve was found')
			->setCellValue('E1', 'Condition')
			->setCellValue('F1', 'The direction of the turn for operation')
			->setCellValue('G1', 'Rewarks');

		$j = 2;
		foreach ($info as $data) :
			$sheet = $spreadsheet->getActiveSheet();
			$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['date_issue'])
				->setCellValue('B' . $j, $data['valve_number'])
				->setCellValue('C' . $j, $data['number_of_turns'])
				->setCellValue('D' . $j, $data['position'])
				->setCellValue('E' . $j, str_replace(["\r", "\n"], ' ', $data['status']))
				->setCellValue('F' . $j, $data['direction'])
				->setCellValue('G' . $j, str_replace(["\r", "\n"], ' ', $data['rewarks']));
			$j++;
		endforeach;
		
		// Set column widths							  
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(100); 
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40); 
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(100);

		// Add conditional formatting
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);

		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('236e09');

		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		// Ajustar texto en columnas
		$spreadsheet->getActiveSheet()->getStyle('E1:E' . ($j - 1))
			->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getStyle('G1:G' . ($j - 1))
			->getAlignment()->setWrapText(true);

		// Ajustar altura automática
		$spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit();
	}
}
