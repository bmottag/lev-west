<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dayoff extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("dayoff_model");
		$this->load->model("general_model");
    }

	/**
	 * List Day Off
     * @since 7/12/2016
     * @author BMOTTAG
	 */
	public function index()
	{
			$arrParam["idEmployee"] = TRUE;
			$data['dayoffList'] = $this->general_model->get_day_off($arrParam);
			$data["view"] = 'dayoff_list';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal- formulario dayoff
     * @since 07/12/2016
     */
    public function cargarModal() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			$this->load->view("modal_dayoff");
    }
	
	/**
	 * Save dayoff
     * @since 04/12/2016
     * @author BMOTTAG
	 */
	public function save_dayoff()
	{			
			header('Content-Type: application/json');
			$data = array();

			//verify ---- 24 hours in advaced for Family/medical appointment and 72 hours for regular
			$type =  $this->input->post('type');//1: Family/medical appointment; 2: Regular
			
			date_default_timezone_set('America/Phoenix');
			$today = date("Y-m-d H:i:s"); 
			$date =  $this->input->post('date'); 

			// Validar formato YYYY-MM-DD
			if (!$date || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
				$data["result"] = "error";
				$data["mensaje"] = "Invalid date format. Please select a valid date.";
				echo json_encode($data);
				exit;
			}

			// Validar que realmente sea una fecha válida (ej. 2025-02-30 no existe)
			list($y, $m, $d) = explode('-', $date);
			if (!checkdate((int)$m, (int)$d, (int)$y)) {
				$data["result"] = "error";
				$data["mensaje"] = "The date is not valid. Please select a correct one.";
				echo json_encode($data);
				exit;
			}

			//START hours calculation
			$minutes = (strtotime($today)-strtotime($date))/60;
			$minutes = abs($minutes);  
			$minutes = round($minutes);

			$hours = $minutes/60;
			
			$flag = TRUE;
			
			if($type == 1 && 24 > $hours ){
				$flag = FALSE;
				$msj = "Error!!. You need more than 24 hours to request the dayoff.";
			}
			if($type == 2 && 72 > $hours ){
				$flag = FALSE;
				$msj = "Error!!. You need more than 72 hours to request the dayoff.";
			}

			if (!$flag) {
				$data["result"] = "error";
				$data["mensaje"] = $msj;
			} else {			
					if ($idDayoff = $this->dayoff_model->add_dayoff()) 
					{
						//revisar si se envia correo o se envia mensaje de texto y a quien se le envia
						$arrParam = array("idNotification" => ID_NOTIFICATION_DAYOFF);
						$configuracionAlertas = $this->general_model->get_notifications_access($arrParam);

						if($configuracionAlertas){
							//buscar info del day off
							$arrParam["idDayoff"] = $idDayoff;
							$dayoffInfo = $this->general_model->get_day_off($arrParam);
							
							switch ($dayoffInfo[0]['id_type_dayoff']) {
								case 1:
									$tipo = 'Family/medical appointment';
									break;
								case 2:
									$tipo = 'Regular';
									break;
							}
							//mensaje del correo
							$subjet = "DAY OFF APP-VCI";
							$observation =  $this->security->xss_clean($this->input->post('observation'));
							$observation =  addslashes($observation);
							$mensajeEmail = "<p>There is a new request for a Day Off:</p>";
							$mensajeEmail .= "<strong>Employee: </strong>" . $dayoffInfo[0]["name"];
							$mensajeEmail .= "<br><strong>Type: </strong>" . $tipo;
							$mensajeEmail .= "<br><strong>Date of dayoff: </strong>" . $dayoffInfo[0]["date_dayoff"];
							$mensajeEmail .= "<br><strong>Observation: </strong>" . $observation;
							$mensajeEmail .= "<p>Follow the link to approve or deny the Day Off: </p>";
							
							//mensaje de texto
							$mensajeSMS = "DAY OFF APP-VCI";
							$mensajeSMS .= "\nThere is a new request for a Day Off:";
							$mensajeSMS .= "\nEmployee: " . $dayoffInfo[0]["name"];
							$mensajeSMS .= "\nType: " . $tipo;
							$mensajeSMS .= "\nDate of dayoff: " . $dayoffInfo[0]["date_dayoff"];
							$mensajeSMS .= "\nObservation: " . $dayoffInfo[0]["observation"];
							$mensajeSMS .= "\nFollow the link to review: ";

							$this->sendNotifications($idDayoff, $configuracionAlertas, $subjet, $mensajeEmail, $mensajeSMS);
						}
						
						$data["result"] = true;
						$data["mensaje"] = "Thank you. The ADMIN will review your request.";
						
						$this->session->set_flashdata('retornoExito', 'Thank you. The ADMIN will review your request.');
					} else {
						$data["result"] = "error";
						$data["mensaje"] = "Error. Reload the page.";
						
						$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
					}
			}

			echo json_encode($data);
    }
	
	/**
	 * List Day Off, for ADMIN
     * @since 8/12/2016
     * @author BMOTTAG
	 */
	public function newDayoffList()
	{
			$data["state"] = 1;//new
			$data['dayoffList'] = $this->general_model->get_day_off($data);
			
			$data["tittle"] = "New Request";
			$data["icon"] = "fa-hand-o-right";
			$data["view"] = 'admin_dayoff_list';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario aprobar dayoff
     * @since 8/12/2016
     */
    public function cargarModalApproved() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			$data["idDayoff"] = $this->input->post("idDayoff");
			$this->load->view("modal_approved", $data);
    }
	
	/**
	 * Save approved
     * @since 8/12/2016
     * @author BMOTTAG
	 */
	public function save_approved()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idSafety = $this->input->post('hddIdParam');
			$state = $this->input->post('state');
			$observation = $this->input->post('observation');
		
			if($state == 3 && $observation ==''){
				$data["result"] = "error";
				$data["mensaje"] = "You must write an observation.";
			}else{
				if ($this->dayoff_model->update_dayoff()) {
					$data["result"] = true;
					$data["mensaje"] = "Solicitud guardada correctamente.";
					$data["idSafety"] = $idSafety;
					
					$this->session->set_flashdata('retornoExito', 'Information saved successfully!!');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
					$data["idSafety"] = "";
					
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
				}
			}

			echo json_encode($data);
    }
	
	/**
	 * List Day Off, for ADMIN
     * @since 8/12/2016
     * @author BMOTTAG
	 */
	public function approvedDayoffList()
	{
			$data["state"] = 2;//approved
			$data['dayoffList'] = $this->general_model->get_day_off($data);
			
			$data["tittle"] = "Approved Request";
			$data["icon"] = "fa-hand-o-up";
			$data["view"] = 'admin_dayoff_list';
			$this->load->view("layout", $data);
	}
	
	/**
	 * List Day Off, for ADMIN
     * @since 8/12/2016
     * @author BMOTTAG
	 */
	public function deniedDayoffList()
	{
			$data["state"] = 3;//denied
			$data['dayoffList'] = $this->general_model->get_day_off($data);
			
			$data["tittle"] = "Denied Request";
			$data["icon"] = "fa-hand-o-down";
			$data["view"] = 'admin_dayoff_list';
			$this->load->view("layout", $data);
	}

    /**
     * Notifications
     * @author BMOTTAG
     * @since  26/12/2022
     */
    public function sendNotifications($idDayoff, $configuracionAlertas, $subjet, $mensajeEmail, $mensajeSMS) 
	{
		//configuracion para envio de mensaje de texto
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
		$twilioPhone = $parametric[5]["value"];
	
		$client = new Twilio\Rest\Client($dato1, $dato2);

		foreach($configuracionAlertas as $envioAlerta):
			//envio correo 
			if (!empty($envioAlerta['email'])) 
			{
				$user = $envioAlerta['name_email'];
				$to = $envioAlerta['email'];
				//$to = "benmotta@gmail.com";

				// ⚡ usar id_user individual, no el JSON completo
				$urlEmail = base_url("external/aproveDayOff/" . $idDayoff . "/" . $envioAlerta['id_user_email']);
				$mensajeEmailFinal = $mensajeEmail . $urlEmail;

				// Contenido correo					
				$mensaje = "<html>
							<head>
							<title>$subjet</title>
							</head>
							<body>
								<p>Dear $user:<br/></p>
								$mensajeEmailFinal
								<p>Cordially,</p>
								<p><strong>V-CONTRACTING INC</strong></p>
							</body>
							</html>";

				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=utf-8\r\n";
				$headers .= "From: VCI APP <info@v-contracting.ca>\r\n";

				mail($to, $subjet, $mensaje, $headers);
			}

			// envío de SMS
			if (!empty($envioAlerta['movil'])) 
			{
				$toSms = '+1'. $envioAlerta['movil'];
				//$toSms = '+14034089921';

				// ⚡ usar id_user individual
				$urlMovil = base_url("external/aproveDayOff/" . $idDayoff . "/" . $envioAlerta['id_user_sms']);
				$mensajeSMSFinal = $mensajeSMS . $urlMovil;

				$client->messages->create(
					$toSms,
					array(
						'from' => $twilioPhone,
						'body' => $mensajeSMSFinal
					)
				);
			}
		endforeach;
		return true;
	}
	
}