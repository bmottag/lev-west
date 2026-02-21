<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class External extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("external_model");
        $this->load->model("general_model");
		$this->load->helper('form');
    }
	
	/**
	 * Form task control
     * @since 7/4/2020
     * @author BMOTTAG
	 */
	public function add_task_control($idJob, $idTaskControl = 'x')
	{
			$data['information'] = FALSE;
			
			//job info
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "id_job",
				"id" => $idJob
			);
			$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);
			
			//company list
			$arrParam = array(
				"table" => "param_company",
				"order" => "company_name",
				"column" => "company_type",
				"id" => 2
			);
			$data['companyList'] = $this->general_model->get_basic_search($arrParam);//company list
			
			//si envio el id, entonces busco la informacion 
			if ($idTaskControl != 'x') {
				
				$arrParam = array("idTaskControl" => $idTaskControl);				
				$data['information'] = $this->external_model->get_task_control($arrParam);
				
				if (!$data['information']) { 
					show_error('ERROR!!! - You are in the wrong place.');	
				}
			}			

			$data["view"] = 'form_task_control';
			$this->load->view("layout", $data);
	}
	
	/**
	 * save _task_control
     * @since 7/4/2020
     * @author BMOTTAG
	 */
	public function save_task_control()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$data["idRecord"] = $this->input->post('hddIdJob');

			if ($idTaskControl = $this->external_model->add_task_control()) 
			{
				$data["result"] = true;
				$data["mensaje"] = "You have saved the Task Assessment and Control, don't forget to sign..";
				$data["idTaskControl"] = $idTaskControl;
				$this->session->set_flashdata('retornoExito', "You haved saved the Task Assessment and Control, don't forget to sign.!!");
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$data["idTaskControl"] = "";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			
			echo json_encode($data);
    }
	
	/**
	 * Signature
	 * param $typo: supervisor / Superintendent
	 * param $idTaskControl: llave principal del formulario
	 * param $idJob: llave principal de trabajo
     * @since 8/4/2020
     * @author BMOTTAG
	 */
	public function add_signature_tac($typo, $idJob, $idTaskControl)
	{
			if (empty($typo) || empty($idJob) || empty($idTaskControl) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			if($_POST){
				
				//update signature with the name of the file
				$name = "images/signature/tac/" . $typo . "_" . $idTaskControl . ".png";
				
				$arrParam = array(
					"table" => "job_task_control",
					"primaryKey" => "id_job_task_control",
					"id" => $idTaskControl,
					"column" => $typo . "_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = "external/add_task_control/" . $idJob . "/" . $idTaskControl;
				
				$data_uri = $this->input->post("image");
				$encoded_image = explode(",", $data_uri)[1];
				$decoded_image = base64_decode($encoded_image);
				file_put_contents($name, $decoded_image);
				
				$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i> SIGNATURE";
				if ($this->general_model->updateRecord($arrParam)) {
					//$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');
					
					$data['clase'] = "alert-success";
					$data['msj'] = "Good job, you have saved your signature.";	
				} else {
					//$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
					
					$data['clase'] = "alert-danger";
					$data['msj'] = "Ask for help.";
				}
				
				$data["view"] = 'template/answer';
				$this->load->view("layout", $data);

				//redirect("/safety/add_safety/" . $idSafety,'refresh');
			}else{			
				$this->load->view('template/make_signature');
			}
	}
	
	/**
	 * Envio de mensaje
     * @since 26/11/2020
     * @author BMOTTAG
	 */
	public function sendSMSWorker($idJob, $idTaskControl)
	{			
		$this->load->library('encrypt');
		require 'vendor/Twilio/autoload.php';

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$parametric = $this->general_model->get_basic_search($arrParam);						
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);
		
        $client = new Twilio\Rest\Client($dato1, $dato2);

		//task control info
		$arrParam = array("idTaskControl" => $idTaskControl);				
		$data['information'] = $this->external_model->get_task_control($arrParam);
		
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$jobInfo = $this->general_model->get_basic_search($arrParam);
		
		$mensaje = "";
		
		//$mensaje .= date('F j, Y', strtotime($data['JSOInfo'][0]['date_issue_jso']));
		$mensaje .= "\n" . $jobInfo[0]['job_description'];
		$mensaje .= "\n";
		$mensaje .= "Click the following link to fill the COVID FORM.";
		$mensaje .= "\n";
		$mensaje .= "\n";
		$mensaje .= base_url("external/add_task_control/" . $idJob . "/" . $idTaskControl);

		$to = '+1' . $data['information'][0]['contact_phone_number'];
	
		// Use the client to do fun stuff like send text messages!
		$client->messages->create(
		// the number you'd like to send the message to
			$to,
			array(
				// A Twilio phone number you purchased at twilio.com/console
				'from' => '587 600 8948',
				'body' => $mensaje
			)
		);

		$data['linkBack'] = "more/task_control/" . $idJob;
		$data['titulo'] = "<i class='fa fa-list'></i> COVID-19";
		
		$data['clase'] = "alert-info";
		$data['msj'] = "We have send the SMS to the Worker to fill the COVID form.";

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}
	
	/**
	 * Envio de mensaje para firmar FLHA
     * @since 14/4/2021
     * @author BMOTTAG
	 */
	public function sendSMSFLHAWorker($idSafety, $idSafetySubcontractor = 'x')
	{			
		$this->load->library('encrypt');
		require 'vendor/Twilio/autoload.php';

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$parametric = $this->general_model->get_basic_search($arrParam);						
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);
		
		
        $client = new Twilio\Rest\Client($dato1, $dato2);
		
		$data['informationWorker'] = FALSE;
		$data['idSafety'] = $idSafety;
														
		$arrParam = array(
			'idSafety' => $idSafety,
			'movilNumber' => true,
			'idSafetySubcontractor' => $idSafetySubcontractor
		);
		$data['infoSafety'] = $this->general_model->get_safety($arrParam);

		//lista de subcontratistas para este FLHA
		$data['informationWorker'] = $this->general_model->get_safety_subcontractors_workers($arrParam);//info safety workers

		$mensaje = "";
		$mensaje .= "Lev-West FLHA - " . date('F j, Y', strtotime($data['infoSafety'][0]['date']));
		$mensaje .= "\n" . $data['infoSafety'][0]['job_description'];
		$mensaje .= "\nFollow the link, read the FLHA and sign.";
		$mensaje .= "\n";
		$mensaje .= "\n";
		$mensaje .= base_url("safety/review_flha/" . $idSafety);

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
		$data['linkBack'] = "safety/review_flha/" . $idSafety;
		$data['titulo'] = "<i class='fa fa-list'></i>FLHA - SMS";
		
		$data['clase'] = "alert-info";
		$data['msj'] = "You have send the SMS to Subcontractors";

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}

	/**
	 * Envio de mensaje para firmar - Excavation and Trenching Plan
     * @since 14/4/2021
     * @author BMOTTAG
	 */
	public function sendSMSExcavationWorker($idExcavation, $idSubcontractor = 'x')
	{			
		$this->load->library('encrypt');
		require 'vendor/Twilio/autoload.php';

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$parametric = $this->general_model->get_basic_search($arrParam);						
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);
		$phone = $parametric[5]["value"];
		
        $client = new Twilio\Rest\Client($dato1, $dato2);
		
		$data['informationWorker'] = FALSE;
		$data['idExcavation'] = $idExcavation;
														
		$arrParam = array(
			'idExcavation' => $idExcavation,
			'movilNumber' => true,
			'idSubcontractor' => $idSubcontractor
		);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		//lista de subcontratistas para este FLHA
		$data['informationWorker'] = $this->general_model->get_excavation_subcontractors($arrParam);//info safety workers

		$mensaje = "";
		$mensaje .= "Lev-West Excavation and Trenching Plan - " . date('F j, Y', strtotime($data['information'][0]['date_excavation']));
		$mensaje .= "\n" . $data['information'][0]['job_description'];
		$mensaje .= "\nFollow the link, read the Excavation and Trenching Plan and sign.";
		$mensaje .= "\n";
		$mensaje .= "\n";
		$mensaje .= base_url("jobs/review_excavation/" . $idExcavation);

		if($data['informationWorker']){
			foreach ($data['informationWorker'] as $data):
				$to = '+1' . $data['worker_movil_number'];		
				$client->messages->create(
					$to,
					array(
						'from' => $phone,
						'body' => $mensaje
					)
				);
			endforeach;
		}
		$data['linkBack'] = "jobs/review_excavation/" . $idExcavation;
		$data['titulo'] = "<i class='fa fa-pied-piper-alt'></i>Excavation and Trenching Plan - SMS";
		
		$data['clase'] = "alert-info";
		$data['msj'] = "You have send the SMS to Subcontractors";

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}

	/**
	 * Add and employye
     * @since 31/1/2022
     * @author BMOTTAG
	 */
	public function newEmployee($key)
	{
			$filtro = 'uiAqv828TZr';
			if($filtro != $key) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
					
			$data["view"] = "form_employee";
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Save new employee
     * @since 31/01/2022
     * @author BMOTTAG
	 */
	public function save_employee()
	{	
			header('Content-Type: application/json');
			$data = array();

			$log_user =  addslashes($this->security->xss_clean($this->input->post('user')));
			$social_insurance = addslashes($this->security->xss_clean($this->input->post('insuranceNumber')));
			$email = addslashes($this->security->xss_clean($this->input->post('email')));
			$firstName = addslashes($this->security->xss_clean($this->input->post('firstName')));
			
			$result_user = false;
			$result_email = false;

			//Verify if the user already exist by the user name
			$arrParam = array(
				"column" => "log_user",
				"value" => $log_user
			);
			$result_user = $this->general_model->verifyUser($arrParam);
			//Verify if the user already exist by the email
			$arrParam = array(
				"column" => "email",
				"value" => $email
			);
			$result_email = $this->general_model->verifyUser($arrParam);

			if($result_user || $result_email) 
			{
				$data["result"] = "error";
				if($result_user)
				{
					$data["mensaje"] = " Error. The user name already exist.";
				}
				if($result_email)
				{
					$data["mensaje"] = " Error. The email already exist.";
				}
				if($result_user && $result_email)
				{
					$data["mensaje"] = " Error. The user name an email already exist.";
				}
			}else{
				if ($this->external_model->saveEmployee()) {
					$newPassword = addslashes($this->security->xss_clean($this->input->post('inputPassword')));
					$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$newPassword); 

					$msj = "Thank you for registering, an email was sent with the access data to the system.";
					$msj .= "<br><br><strong>User name: </strong>" . $log_user;
					$msj .= "<br><strong>Password: </strong>" . $passwd;

					//Envio de correo
					$subjet = "Welcome to Lev-West";
					$user = $firstName;
					$to = $email;

					$link = base_url();
					$emailmsj = "<strong>APP Link: </strong><a href='" . $link . "'>Lev-West APP</a>";
					$emailmsj .= "<br><strong>User name: </strong>" . $log_user;
					$emailmsj .= "<br><strong>Password: </strong>" . $passwd;

					$mensaje = "<html>
					<head>
					  <title> $subjet </title>
					</head>
					<body>
						<p>Dear	$user:</p>
						<p>Thank you for registering, the following employees information is the access data to the system:</p>
						<p>$emailmsj</p>
						<p>Cordially,</p>
						<p><strong>V-CONTRACTING INC</strong></p>
					</body>
					</html>";		
	
					$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
					$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$cabeceras .= 'From: Lev-West APP <fabian.v@lev-west.com>' . "\r\n";

					//enviar correo
					mail($to, $subjet, $mensaje, $cabeceras);
					//Fin envio de correo

					$data["result"] = true;					
					$this->session->set_flashdata('retornoExito', $msj);
				} else {
					$data["result"] = "error";
				}
			}
			echo json_encode($data);
    }

	/**
	 * Form Checkin
     * @since 30/5/2022
     * @author BMOTTAG
	 */
	public function checkin($idProject, $idCheckin = 'x')
	{
			$data['information'] = FALSE;
			$data['idCheckin'] = FALSE;

			$arrParam = array(
				"table" => "new_workers",
				"order" => "worker_name",
				"id" => "x"
			);
			$data['workers'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_jobs",
				"order" => "id_job",
				"column" => "id_job",
				"id" => $idProject
			);
			$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);//company list

			$arrParam = array(
				"today" => date('Y-m-d'),
				"idJob" => $idProject
			);
			$data['checkinList'] = $this->general_model->get_checkin($arrParam);

			if ($idCheckin != 'x') {
				$data['idCheckin'] = $idCheckin;
				$arrParam = array("idCheckin" => $idCheckin);
				$data['checkinList'] = $this->general_model->get_checkin($arrParam);
			}
			$data["view"] = "form_checkin";
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Save Checkin
     * @since 1/6/2022
     * @author BMOTTAG
	 */
	public function save_checkin()
	{
			header('Content-Type: application/json');
			$data = array();

			$login_before =  addslashes($this->security->xss_clean($this->input->post('login_before')));
			$idWorker =  addslashes($this->security->xss_clean($this->input->post('id_name')));
			$idJob =  addslashes($this->security->xss_clean($this->input->post('idProject')));
			$msj = "Welcome, work safe!";

			if($login_before == 1){
				if ($idCheckin = $this->external_model->saveCheckin($idWorker)) 
				{
					$data["result"] = true;
					$data["idCheckin"] = $idJob ."/".$idCheckin;
					$this->session->set_flashdata('retornoExito', $msj);
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$data["idInspection"] = "";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
				}
			}else{
				if ($idWorker = $this->external_model->saveNewWorker()) 
				{
					if ($idCheckin = $this->external_model->saveCheckin($idWorker)) 
					{
						$data["result"] = true;
						$data["idCheckin"] = $idJob ."/".$idCheckin;
						$this->session->set_flashdata('retornoExito', $msj);
					} else {
						$data["result"] = "error";
						$data["mensaje"] = "Error!!! Ask for help.";
						$data["idInspection"] = "";
						$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
					}
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$data["idInspection"] = "";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
				}				
			}
			echo json_encode($data);
    }

    /**
     * Cargo modal - formulario checkout
     * @since 4/06/2022
     */
    public function cargarModalCheckout() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idCheckin"] = $this->input->post("idCheckin");	

			if ($data["idCheckin"] != 'x') {
				$arrParam = array(
					"idCheckin" => $data["idCheckin"]
				);
				$data['information'] = $this->general_model->get_checkin($arrParam);
			}
			$this->load->view("checkout_modal", $data);
    }

	/**
	 * Update checkin - checkout
     * @since 4/06/2022
     * @author BMOTTAG
	 */
	public function save_checkout()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idCheckin = $this->input->post('hddId');
			$idJob = $this->input->post('idProject');
			$data["idCheckin"] = $idJob ."/".$idCheckin;
			$msj = "Thanks for coming, have a good day!";

			if ($this->external_model->saveCheckout()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			echo json_encode($data);	
    }

	/**
	 * List Day Off, for ADMIN
     * @since 27/12/2022
     * @author BMOTTAG
	 */
	public function aproveDayOff($idDayoff, $idUser)
	{
			$data["idDayoff"] = $idDayoff;
			$data["idUser"] = $idUser;
			$data["dayOffInfo"] = $this->general_model->get_day_off($data);

			if($data["dayOffInfo"]) {
				$data["view"] = 'dayoff';
				$this->load->view("layout_calendar", $data);
			}else{
				show_error('ERROR!!! - You are in the wrong place.');
			}	
	}

	/**
	 * Update dayoff status
     * @since 27/12/2022
     * @author BMOTTAG
	 */
	public function update_dayoff_status()
	{	
			header('Content-Type: application/json');
			$data = array();
			$idDayoff = $this->input->post('hddIdDayOff');
			$idUser = $this->input->post('hddIdUser');
			$data["return"] = $idDayoff ."/".$idUser;
			if ($this->external_model->update_dayoff()) {
				$data["result"] = true;			
				$this->session->set_flashdata('retornoExito', 'Information saved successfully!!');
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			echo json_encode($data);
    }
	
	
}