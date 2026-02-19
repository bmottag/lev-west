<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incidences extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("incidences_model");
    }
	
	/**
	 * Near Miss list
     * @since 17/3/2017
     * @author BMOTTAG
	 */
	public function near_miss($idJob)
	{		
		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		$arrParam = array("jobId" => $idJob);
		$data['nearMissInfo'] = $this->incidences_model->get_near_miss_by_idUser($arrParam);

		$data["view"] ='near_miss_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Near Miss
     * @since 17/3/2017
     * @author BMOTTAG
	 */
	public function add_near_miss($idJob, $id = 'x')
	{
			$data['information'] = FALSE;
			$data['deshabilitar'] = '';
			
			$this->load->model("general_model");
			//job info
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "id_job",
				"id" => $idJob
			);
			$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

			//incident type list
			$arrParam = array(
				"table" => "param_incident_type",
				"order" => "id_incident_type",
				"id" => "x"
			);
			$data['incidentType'] = $this->general_model->get_basic_search($arrParam);
			//job´s list - (active´s items)
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "state",
				"id" => 1
			);
			$data['jobs'] = $this->general_model->get_basic_search($arrParam);
			//worker´s list
			$arrParam = array(
				"table" => "user",
				"order" => "first_name, last_name",
				"column" => "state",
				"id" => 1
			);
			$data['workersList'] = $this->general_model->get_basic_search($arrParam);//worker´s list
			
			//si envio el id, entonces busco la informacion 
			if ($id != 'x') {
				
				$arrParam = array(
					"idNearMiss" => $id
				);				
				$data['information'] = $this->incidences_model->get_near_miss_by_idUser($arrParam);

				//busco lista de personal involucrado, para el formulario de NEAR MISS (1)
				$arrParam = array(
					'idIncident' => $id,
					'form' => 1
				);
				$data['personsInvolved'] = $this->incidences_model->get_persons_involved($arrParam);
				
				if (!$data['information']) { 
					show_error('ERROR!!! - You are in the wrong place.');	
				}
			}			

			$data["view"] = 'form_near_miss';
			$this->load->view("layout", $data);
	}
	
	/**
	 * Save near miss
     * @since 28/3/2017
     * @author BMOTTAG
	 */
	public function save_near_miss()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idReport = $this->input->post('hddIdentificador');
			$data["idJob"] = $this->input->post('jobName');

			if ($idNearmiss = $this->incidences_model->add_near_miss()) {
				
				if ($idReport == '') {
					$this->email_to($idNearmiss, 1);//si es un reporte nuevo envio correo
				}				
				
				$data["result"] = true;
				$data["mensaje"] = "You have saved the Near Miss Report, continue uploading the information.";
				$data["idNearmiss"] = $idNearmiss;
				$this->session->set_flashdata('retornoExito', 'You have saved the Near Miss Report, continue uploading the information!!');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$data["idNearmiss"] = "";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	/**
	 * Update incidence state
     * @since 25/4/2017
     * @author BMOTTAG
	 */
	public function update_incidence_state()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$data["identificador"] = $this->input->post('hddIdentificador');
			$incidencesType = $this->input->post('incidencesType');

			$arrParam = array(
				"table" => "incidence_" . $incidencesType,
				"primaryKey" => "id_" . $incidencesType,
				"id" => $data["identificador"],
				"column" => "state_incidence",
				"value" => 2
			);
			$this->load->model("general_model");
	
			//actualizo el estado del formulario a cerrado(2)
			if ($this->general_model->updateRecord($arrParam)) {
				$data["result"] = true;
				$data["mensaje"] = "You have close the Report.";
				$this->session->set_flashdata('retornoExito', 'You have closed Report');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	/**
	 * Signature
	 * param $incidencesType: near_miss / incident / accident
	 * param $userType: supervisor / coordinator
	 * param $idFormulario: llave principal del formulario
     * @since 15/5/2017
     * @author BMOTTAG
	 */
	public function add_signature($incidencesType, $userType, $idJob, $idFormulario, $idPersonal = 'x' )
	{
			if (empty($incidencesType) ||empty($userType) || empty($idFormulario) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			if($_POST){
				//update signature with the name of de file
				if($userType == "personsInvolved")
				{
					$name = "images/signature/safety/" . $userType . "_" . $idPersonal . ".png";
					
					$arrParam = array(
						"table" => "incidence_incident_person",
						"primaryKey" => "id_incident_person ",
						"id" => $idPersonal,
						"column" => "person_signature",
						"value" => $name
					);
					$this->load->model("general_model");
					$updateColumSignature = $this->general_model->updateRecord($arrParam);
					$data['linkBack'] = "incidences/add_incident/" . $idJob . "/" . $idFormulario;
				}else{
					//update signature with the name of de file
					//para firmas de CORDINADORES Y SUPERVISORES
					$name = "images/signature/incidences/" . $incidencesType . "_" . $userType . "_" . $idFormulario . ".png";
					
					$arrParam = array(
						"table" => "incidence_" . $incidencesType,
						"signatureColumn" => $userType. "_signature",
						"valSignature" => $name,
						"userColumn" => "fk_id_user_" . $userType,
						"fechaColumn" => "date_" . $userType,
						"idColumn" => "id_" . $incidencesType,
						"idValue" => $idFormulario
					);
					$updateColumSignature = $this->incidences_model->updateInfoSignature($arrParam);
				}
				$data['linkBack'] = "incidences/add_" . $incidencesType . "/" . $idJob. "/" . $idFormulario;
				
				$data_uri = $this->input->post("image");
				$encoded_image = explode(",", $data_uri)[1];
				$decoded_image = base64_decode($encoded_image);
				file_put_contents($name, $decoded_image);
				
				$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
				if($updateColumSignature) {
					$data['clase'] = "alert-success";
					$data['msj'] = "Good job, you have saved your signature.";	
				} else {				
					$data['clase'] = "alert-danger";
					$data['msj'] = "Ask for help.";
				}
			
				$data["view"] = 'template/answer';
				$this->load->view("layout", $data);

			}else{		
				$this->load->view('template/make_signature');
			}
	}
	
	/**
	 * Evio de correo
     * @since 15/5/2017
     * @author BMOTTAG
	 */
	public function email_to($idIncidence, $incidencesType)
	{
	    	switch($incidencesType){
	    		case 1: //near miss report
					$model = "get_near_miss_by_idUser";
					$subjet = "Near Miss Report";
					$arrParam = array('idNearMiss' => $idIncidence);
					break;
	    		case 2: //incident report
					$model = "get_incident_by";
					$subjet = "Incident Report";
					$arrParam = array('idIncident' => $idIncidence);
					break;
	    		case 3: //accident report
					$model = "get_accident_by";
					$subjet = "Accident Report";
					$arrParam = array('idAccident' => $idIncidence);
					break;
	    	}
			$infoIncident = $this->incidences_model->$model($arrParam);
			
			//mensaje del correo
			$msj = "<p>It is a new " . $subjet . ":</p>";
			$msj .= "<strong>Report by: </strong>" . $infoIncident[0]["name"];
			
			if($incidencesType == 3){
				$msj .= "<br><strong>Brief explanation: </strong>" . $infoIncident[0]["brief_explanation"];
			}else{
				$msj .= "<br><strong>What happened: </strong>" . $infoIncident[0]["what_happened"];
			}
			
			$mensaje = "<html>
			<head>
			  <title> $subjet </title>
			</head>
			<body>
				<p>$msj</p>
				<p>Cordially,</p>
				<p><strong>V-CONTRACTING INC</strong></p>
			</body>
			</html>";

			//mensaje de texto
			$mensajeSMS = "APP VCI - Incident Notification";
			$mensajeSMS .= "\nIt is a new " . $subjet . ":";
			$mensajeSMS .= "\nReport by: " . $infoIncident[0]["name"];
			if($incidencesType == 3){
				$mensajeSMS .= "\nBrief explanation: " . $infoIncident[0]["brief_explanation"];
			}else{
				$mensajeSMS .= "\nWhat happened: " . $infoIncident[0]["what_happened"];
			}

			//enviar correo a VCI
			$arrParam = array(
				"idNotification" => ID_NOTIFICATION_INCIDENT,
				"subjet" => $subjet,
				"msjEmail" => $mensaje,
				"msjPhone" => $mensajeSMS 
			);
			send_notification($arrParam);
	}
	
	/**
	 * incident list
     * @since 15/5/2017
     * @author BMOTTAG
	 */
	public function incident($idJob)
	{
		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);
		
		$arrParam = array("jobId" => $idJob);
		$data['incidentInfo'] = $this->incidences_model->get_incident_by($arrParam);

		$data["view"] ='incident_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Incident
     * @since 15/5/2017
     * @author BMOTTAG
	 */
	public function add_incident($idJob, $id = 'x')
	{
			$data['information'] = FALSE;
			$data['deshabilitar'] = '';
			
			$this->load->model("general_model");
			//job info
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "id_job",
				"id" => $idJob
			);
			$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

			//incident type list
			$arrParam = array(
				"table" => "param_incident_type",
				"order" => "id_incident_type",
				"id" => "x"
			);
			$data['incidentType'] = $this->general_model->get_basic_search($arrParam);

			//workers list
			$arrParam = array(
				"table" => "user",
				"order" => "first_name, last_name",
				"column" => "state",
				"id" => 1
			);
			$data['workersList'] = $this->general_model->get_basic_search($arrParam);//worker´s list

			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "state",
				"id" => 1
			);
			$data['jobs'] = $this->general_model->get_basic_search($arrParam);
			
			//si envio el id, entonces busco la informacion 
			if ($id != 'x')
			{
				$arrParam = array('idIncident' => $id);				
				$data['information'] = $this->incidences_model->get_incident_by($arrParam);

				//busco lista de personal involucrado, para el formulario de INCIDENT (2)
				$arrParam = array(
					'idIncident' => $id,
					'form' => 2
				);
				$data['personsInvolved'] = $this->incidences_model->get_persons_involved($arrParam);
		
				if (!$data['information']) { 
					show_error('ERROR!!! - You are in the wrong place.');	
				}
			}			

			$data["view"] = 'form_incident';
			$this->load->view("layout", $data);
	}
	
	/**
	 * Save incident
     * @since 15/5/2017
     * @author BMOTTAG
	 */
	public function save_incident()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idReport = $this->input->post('hddIdentificador');
			$data["idJob"] = $this->input->post('jobName');

			if ($idIncident = $this->incidences_model->add_incident()) {
				
				if ($idReport == '') {
					$this->email_to($idIncident, 2);//si es un reporte nuevo envio correo
				}				
				
				$data["result"] = true;
				$data["mensaje"] = "You have saved the Incident Report, continue uploading the information.";
				$data["idRecord"] = $idIncident;
				$this->session->set_flashdata('retornoExito', 'You have saved the Incident Report, continue uploading the information!!');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$data["idRecord"] = "";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	/**
	 * accident list
     * @since 12/6/2017
     * @author BMOTTAG
	 */
	public function accident()
	{		
			$arrParam = array();
			$data['accidentInfo'] = $this->incidences_model->get_accident_by($arrParam);

			$data["view"] ='accident_list';
			$this->load->view("layout", $data);
	}

	/**
	 * Form accident
     * @since 12/6/2017
     * @author BMOTTAG
	 */
	public function add_accident($id = 'x')
	{
			$data['information'] = FALSE;
			
			$this->load->model("general_model");
			
			//si envio el id, entonces busco la informacion 
			if ($id != 'x') {
				//informacion del incidente
				$arrParam = array(
					"idAccident" => $id
				);				
				$data['information'] = $this->incidences_model->get_accident_by($arrParam);
				
				
				$this->load->model("general_model");
				//informacion de los testigos
				$arrParam = array(
					"table" => "incidence_accident_witness",
					"order" => "id_witness",
					"column" => "fk_id_accident",
					"id" => $id
				);
				$data['witnessInfo'] = $this->general_model->get_basic_search($arrParam);
				
				//informacion de los carros involucrados
				$arrParam = array(
					"table" => "incidence_accident_car_involved",
					"order" => "id_car_involved",
					"column" => "fk_id_accident",
					"id" => $id
				);
				$data['carsInvolvedInfo'] = $this->general_model->get_basic_search($arrParam);
				
				
				
				if (!$data['information']) { 
					show_error('ERROR!!! - You are in the wrong place.');	
				}
			}			

			$data["view"] = 'form_accident';
			$this->load->view("layout", $data);
	}
	
	/**
	 * Save accident
     * @since 12/6/2017
     * @author BMOTTAG
	 */
	public function save_accident()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idReport = $this->input->post('hddIdentificador');

			if ($idAccident = $this->incidences_model->add_accident()) {
				
				if ($idReport == '') {
					$this->email_to($idAccident, 3);//si es un reporte nuevo envio correo
				}				
				
				$data["result"] = true;
				$data["mensaje"] = "You have saved the Accident Report, continue uploading the information.";
				$data["idRecord"] = $idAccident;
				$this->session->set_flashdata('retornoExito', 'You have saved the Accident Report, continue uploading the information!!');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$data["idRecord"] = "";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
    /**
     * Cargo modal- formulario de captura witness
     * @since 12/6/2017
     */
    public function cargarModalWitness() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

			$identificador = $this->input->post("identificador");
			//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
			$porciones = explode("-", $identificador);
			$data["idIncident"] = $porciones[1];
		
			$this->load->view("modal_witness", $data);
    }
	
	/**
	 * Save formularios
	 * @param varchar $modalToUse: indica que funcion del modelo se debe usar
     * @since 13/6/2017
     * @author BMOTTAG
	 */
	public function save($modalToUse)
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$data["idRecord"] = $this->input->post('hddidIncident');

			if ($this->incidences_model->$modalToUse()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have added a new record!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			echo json_encode($data);
    }
	
    /**
     * Delete accident record
	 * @param varchar $tabla: nombre de la tabla de la cual se va a borrar
	 * @param int $idValue: id que se va a borrar
	 * @param int $idWorkorder: llave  primaria de workorder
     */
    public function deleteRecord($tabla, $idValue, $idWorkorder, $vista) 
	{
			if (empty($tabla) || empty($idValue) || empty($idWorkorder) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			$arrParam = array(
				"table" => "incidence_accident_" . $tabla,
				"primaryKey" => "id_"  . $tabla,
				"id" => $idValue
			);
			$this->load->model("general_model");
			if ($this->general_model->deleteRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'You have deleted one record from <strong>'.$tabla.'</strong> table.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect(base_url('incidences/' . $vista . '/' . $idWorkorder), 'refresh');
    }
	
    /**
     * Cargo modal- formulario de carros en el accidente
     * @since 15/6/2017
     */
    public function cargarModalCarsInvolved() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

			$identificador = $this->input->post("identificador");
			//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
			$porciones = explode("-", $identificador);
			$data["idIncident"] = $porciones[1];
		
			$this->load->view("modal_cars_involved", $data);
    }

	/**
	 * Generate Report in PDF
	 * @param int $idIncident
	 * @param int $type
     * @since 3/7/2017
     * @author BMOTTAG
	 */
	public function generaPDF($idIncident, $type)
	{
			$this->load->library('Pdf');
			
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('VCI');
			$pdf->SetTitle('Incidences Report');
			$pdf->SetSubject('TCPDF Tutorial');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
				require_once(dirname(__FILE__).'/lang/eng.php');
				$pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('dejavusans', '', 8);

			// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
			// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
			
			switch ($type) {
				case 1:
					$arrParam = array("idNearMiss" => $idIncident);				
					$data['info'] = $this->incidences_model->get_near_miss_by_idUser($arrParam);
					$data['title'] = "NEAR MISS REPORT";
					$vista = "reporte_near_miss_pdf";
					break;
				case 2:
					$arrParam = array("idIncident" => $idIncident);				
					$data['info'] = $this->incidences_model->get_incident_by($arrParam);
					$data['title'] = "INCIDENT/ACCIDENT REPORT";
					$vista = "reporte_incident_pdf";
					break;
				case 3:
					$arrParam = array("idAccident" => $idIncident);				
					$data['info'] = $this->incidences_model->get_accident_by($arrParam);				
					$data['title'] = "ACCIDENT REPORT";
					$vista = "reporte_accident_pdf";
					
					$data['carsInvolvedInfo'] = $this->incidences_model->get_accident_car_involved($arrParam);
					$data['witnessInfo'] = $this->incidences_model->get_accident_witness($arrParam);
					break;
			}


			//busco lista de personal involucrado, para el formulario
			$arrParam = array(
				'idIncident' => $idIncident,
				'form' => $type
			);
			$data['personsInvolved'] = $this->incidences_model->get_persons_involved($arrParam);


			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
			// Print a table

				
				// add a page
				$pdf->AddPage();

				$html = $this->load->view($vista, $data, true);
											
				// output the HTML content
				$pdf->writeHTML($html, true, false, true, false, '');
		

			// Print some HTML Cells

			// reset pointer to the last page
			$pdf->lastPage();

			$project = $data['info'][0]["job_description"];
			//Close and output PDF document
			$pdf->Output('incident_report_' . $project . '.pdf', 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		
	}
	
    /**
     * Safe Person Involved
     * @since 24/4/2021
     * @author BMOTTAG
     */
    public function save_person_involved() 
	{
			$idIncident = $this->input->post('hddId');
			$formIdentifier = $this->input->post('hddFormIdentifier');
			
			if($formIdentifier==1){
				$path = 'incidences/add_near_miss/' . $idIncident;
			}else{
				$idJob = $this->input->post('hddIdJob');
				$path = 'incidences/add_incident/' . $idJob . '/' . $idIncident;
			}

			if ($this->incidences_model->savePersonInvolved()) {
				$this->session->set_flashdata('retornoExito', 'You have added a Person Involved.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect(base_url($path), 'refresh');
    }		

    /**
     * Delete personl involved
     * @since 24/4/2021
     * @author BMOTTAG
     */
    public function deleteIncidentPersonInvolved($idPerson, $idIncident, $formIdentifier) 
	{
			if (empty($idPerson) || empty($idIncident) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
			$arrParam = array(
				"table" => "incidence_incident_person",
				"primaryKey" => "id_incident_person",
				"id" => $idPerson
			);

			if($formIdentifier==1){
				$path = 'incidences/add_near_miss/' . $idIncident;
			}else{
				$path = 'incidences/add_incident/' . $idIncident;
			}
			$this->load->model("general_model");
			if ($this->general_model->deleteRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'You have deleted a Person Involved.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect(base_url($path), 'refresh');
    }

	/**
	 * Envio de mensaje para firmar INCIDENCES
	 * param $idFormulario: id del formulario
	 * param $incidencesType: 1:Near Miss / 2: Incident
     * @since 25/4/2021
     * @author BMOTTAG
	 */
	public function sendSMSIncidencesPersons($idFormulario, $incidencesType)
	{			
		$this->load->model("general_model");
//		$this->load->library('encrypt');
//		require 'vendor/Twilio/autoload.php';

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$this->load->model("general_model");
		$parametric = $this->general_model->get_basic_search($arrParam);						
//		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
//		$dato2 = $this->encrypt->decode($parametric[4]["value"]);
		
//        $client = new Twilio\Rest\Client($dato1, $dato2);
		
		$data['infoPersonsInvolved'] = FALSE;
		
		switch ($incidencesType) {
			case 1:
				$arrParam = array("idNearMiss" => $idFormulario);				
				$data['info'] = $this->incidences_model->get_near_miss_by_idUser($arrParam);
				break;
			case 2:
				$arrParam = array("idIncident" => $idFormulario);				
				$data['info'] = $this->incidences_model->get_incident_by($arrParam);
				break;
		}
pr($data['info']);
		//busco lista de personal involucrado, para el formulario
		$arrParam = array(
			'idIncident' => $idFormulario,
			'form' => $incidencesType,
			'movilNumber' => true
		);
		$data['infoPersonsInvolved'] = $this->incidences_model->get_persons_involved($arrParam);
pr($data['infoPersonsInvolved']); exit;
		$mensaje = "";
		$mensaje .= "VCI INCIDENCES - " . date('F j, Y', strtotime($data['info'][0]['date_issue']));
		$mensaje .= "\n" . $data['infoSafety'][0]['job_description'];
		$mensaje .= "\nFollow the link, read and sign.";
		$mensaje .= "\n";
		$mensaje .= "\n";
		if($incidencesType==1){
			$mensaje .= base_url("incidences/review_incident/" . $idFormulario);
		}else{
			$mensaje .= base_url("incidences/review_near_miss/" . $idFormulario);
		}

		if($data['informationWorker']){
			foreach ($data['informationWorker'] as $data):
				$to = '+1' . $data['worker_movil_number'];		
				$client->messages->create(
					$to,
					array(
						'from' => '587 600 8948',
						'body' => $mensaje
					)
				);
			endforeach;
		}
		if($incidencesType==1){
			$path = 'incidences/add_near_miss/' . $idFormulario;
		}else{
			$path = 'incidences/add_incident/' . $idFormulario;
		}
		$data['linkBack'] = $path;
		$data['titulo'] = "<i class='fa fa-list'></i>FLHA - SMS";
		
		$data['clase'] = "alert-info";
		$data['msj'] = "You have send the SMS to Subcontractors";

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}

	/**
	 * Subcontractors view to sign
     * @since 15/4/2021
     * @author BMOTTAG
	 */
	public function review_incident($idFormulario)
	{
			$arrParam = array('idIncident' => $idFormulario);				
			$data['information'] = $this->incidences_model->get_incident_by($arrParam);

			//busco lista de personal involucrado, para el formulario de INCIDENT (2)
			$arrParam = array(
				'idIncident' => $idFormulario,
				'form' => 2
			);
			$data['personsInvolved'] = $this->incidences_model->get_persons_involved($arrParam);

			$data["view"] = 'review_incident';
			$this->load->view("layout_calendar", $data);
	}

	
	
	
	
}