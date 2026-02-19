<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("payroll_model");
	}

	/**
	 * Form Add Payroll
	 * @since 09/11/2016
	 * @author BMOTTAG
	 */
	public function add_payroll($id = 'x')
	{
		$idUser = $this->session->userdata("id");
		$this->load->model("general_model");

		//job programming - (active´s items)
		$sql = "SELECT p.fk_id_job, p.id_programming
					FROM programming_worker pw
					JOIN programming p ON pw.fk_id_programming = p.id_programming
					WHERE pw.fk_id_programming_user = $idUser
					AND DATE(p.date_programming) = CURDATE();
					";
		$query = $this->db->query($sql);

		$data['job_programming'] = null;
		$data['programming'] = null;
		if ($query->row_array()) {
			$result = $query->row_array();
			$fk_id_job = $result['fk_id_job'];
			$data['job_programming'] = $fk_id_job;
			$id_programming = $result['id_programming'];
			$data['programming'] = $id_programming;
		}
		$this->db->close();

		//job´s list - (active´s items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);


		//search for the last user´s record
		$arrParam = array(
			"idEmployee" => $this->session->userdata("id"),
			"limit" => 1
		);
		$data['record'] = $this->general_model->get_task($arrParam);

		$view = 'form_add_payroll';

		//if the last record doesn´t have finish time
		if ($data['record'] && $data['record'][0]['finish'] == '0000-00-00 00:00:00') {

			$data['programming'] = $data['record'][0]['fk_id_programming'];
			$data['start'] = $data['record'][0]['start'];
			$view = 'form_end_payroll';
		}

		$data["view"] = $view;
		$this->load->view("layout", $data);
	}

	/**
	 * Save payroll
	 * @since 09/11/2016
	 * @author BMOTTAG
	 */
	public function savePayroll()
	{
		$hour = date("G:i");

		if ($this->payroll_model->savePayroll()) {
			/**
			 * Busco si tiene una Service Order con estado in progress
			 * Actualizo la fecha
			 */
			$infoServiceOrder = $this->payroll_model->get_in_progress_service_order();
			if ($infoServiceOrder) {
				$this->payroll_model->updateServiceOrderTime($infoServiceOrder, "start");
			}

			$this->session->set_flashdata('retornoExito', 'have a nice shift, you started at ' . $hour . '.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		$dashboard = $this->session->userdata("dashboardURL");
		redirect($dashboard, 'refresh');
	}

	/**
	 * Update finish time payroll
	 * @since 11/11/2016
	 * @review 2/02/2022
	 * @author BMOTTAG
	 */
	public function updatePayroll()
	{
		$idTask = $this->input->post('hddIdentificador');
		$start = $this->input->post('hddStart');
		$fechaStart = strtotime($start);
		$fechaStart = date("Y-m-d", $fechaStart);
		$fechaActual = date("Y-m-d");

		$hour = date("G:i");

		//update working time and working hours
		$finish = date("Y-m-d G:i:s");
		if ($this->payroll_model->updateWorkingTimePayroll($start, $finish)) {
			/**
			 * Guardar el id del periodo en la tabla task
			 * busco el periodo, sino existe lo creo
			 */
			$periodo = $this->save_period($idTask);
			/**
			 * Busco si tiene una Service Order con estado in progress
			 * Actualizo el tiempo de trabajo de ese service order
			 */
			$infoServiceOrder = $this->payroll_model->get_in_progress_service_order();
			if ($infoServiceOrder) {
				$this->payroll_model->updateServiceOrderTime($infoServiceOrder, "finish");
			}

			$this->session->set_flashdata('retornoExito', 'have a good night, you finished at ' . $hour . '.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> bad at math.');
		}

		$dashboard = $this->session->userdata("dashboardURL");
		redirect($dashboard, 'refresh');
	}

	/**
	 * Signature
	 * @since 10/12/2016
	 * @author BMOTTAG
	 */
	public function add_signature($idTask)
	{
		if (empty($idTask)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {
			//update signature with the name of de file
			date_default_timezone_set('America/Phoenix');
			$today = date("Y-m-d");
			$name = "images/signature/payroll/" . $idTask . "_" . $today . ".png";

			$arrParam = array(
				"table" => "task",
				"primaryKey" => "id_task",
				"id" => $idTask,
				"column" => "signature",
				"value" => $name
			);

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['linkBack'] = "payroll/add_payroll/";
			$data['titulo'] = "<i class='fa fa-pencil fa-fw'></i>SIGNATURE";
			if ($this->general_model->updateRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');

				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have saved your signature.";
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');

				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);

			//redirect("/payroll/add_payroll/",'refresh');

		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Location
	 * @since 22/11/2016
	 * @author BMOTTAG
	 */
	public function view_location()
	{
		$this->load->view('location');
	}

	/**
	 * Cargo modal- formulario para editar las horas de los empleados
	 * @since 2/2/2018
	 */
	public function cargarModalHours()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;

		//busco inicio y fin para calcular horas de trabajo y guardar en la base de datos
		//START search info for the task
		$idTask =  $this->input->post('idTask');
		$data['information'] = $this->payroll_model->get_taskbyid($idTask);
		//END of search				

		$this->load->view("modal_hours_worker", $data);
	}

	/**
	 * Save payroll hours 
	 * Se usa cuando el adminstrador esta actualizadno las horas del empleado
	 * @since 2/2/2018
	 * @author BMOTTAG
	 */
	public function savePayrollHour()
	{
		header('Content-Type: application/json');
		$data = array();

		$idTask = $this->input->post('hddIdentificador');
		$fechaAnterior = $this->input->post('hddfechaInicio');
		$fechaStart = $this->input->post('start_date');
		$data["idRecord"] = $idTask;
		$data["datePayroll"] = $fechaStart;

		if ($this->payroll_model->savePayrollHour()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', 'You have updated the payroll hour');

			//busco inicio y fin para calcular horas de trabajo y guardar en la base de datos
			//START search info for the task
			$idTask =  $this->input->post('hddIdentificador');
			$infoTask = $this->payroll_model->get_taskbyid($idTask);
			$start = $infoTask["start"];
			$finish = $infoTask["finish"];
			//END of search	

			//update working time and working hours
			if ($this->payroll_model->updateWorkingTimePayroll($start, $finish, 1)) {
				//verificar si cambio la fecha se debe actualizar el id del periodo
				if ($fechaAnterior != $fechaStart) {
					/**
					 * Guardar el id del periodo en la tabla task
					 * busco el periodo, sino existe lo creo
					 */
					$this->save_period($idTask);
				}

				$this->session->set_flashdata('retornoExito', 'You have updated the payroll hour');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> bad at math.');
			}
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Busco a que periodo pertenece y se guarda el id periodo en la tabla de task
	 * @since 9/2/2022
	 * @author BMOTTAG
	 */
	function save_period($idTask)
	{
		$this->load->model("general_model");
		$idWeakPeriod = FALSE;

		//mientras no tenga ID PERIDO entonces lo busco y si no existe creo 2 mas
		while (!$idWeakPeriod) {
			$idWeakPeriod = $this->search_period($idTask); //busco el ID del periodo en los ultimos 10

			//no esta dentro de ninguno de los ultimos 10 periodos entonces se genero 2 peridos mas
			if (!$idWeakPeriod) {
				$this->generate_period();
			}
		}

		//guardo el id en la tabla task			
		$arrParam = array(
			"table" => "task",
			"primaryKey" => "id_task",
			"id" => $idTask,
			"column" => "fk_id_weak_period",
			"value" => $idWeakPeriod
		);
		if ($this->general_model->updateRecord($arrParam)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Busco a que periodo pertenece
	 * @since 9/2/2022
	 */
	function search_period($idTask)
	{
		//info for the payroll
		$this->load->model("general_model");
		$arrParam = array("idTask" => $idTask);
		$infoPayroll = $this->general_model->get_task($arrParam); //informacion del payroll

		$start = strtotime($infoPayroll[0]['start']);
		$fechaStart = date('Y-m-j', $start);

		$arrParam = array("limit" => 10);
		$infoPeriod = $this->general_model->get_weak_period($arrParam); //lista de periodos los ultimos 10

		//valido si la fecha esta dentro de alguno de los 10 ultimos periodos
		$fechaStart = date_create($fechaStart);

		$idWeakPeriod = FALSE;
		foreach ($infoPeriod as $data) :
			$periodoIni = date_create($data['date_weak_start']);
			$periodoFin = date_create($data['date_weak_finish']);

			if ($fechaStart >= $periodoIni && $fechaStart <= $periodoFin) {
				//esta dentro del periodo entonces saco el id del periodo
				$idWeakPeriod = $data['id_period_weak'];
				break;
			}
		endforeach;

		return $idWeakPeriod;
	}

	/**
	 * Genera 2 secuencias de periodos
	 * @since 9/2/2022
	 * @author BMOTTAG
	 */
	public function generate_period()
	{
		$this->load->model("general_model");

		//genero 2 periodos nuevos
		for ($i = 0; $i < 2; $i++) {
			$arrParam = array("limit" => 1);
			$infoPeriod = $this->general_model->get_period($arrParam); //info ultimo periodo

			//fecha inicial del siguiente periodo se le suma un dia a la fecha final del ultimo periodo
			//fecha final del siguiente periodo se le suma 14 dias a la fecha final del ultimo periodo
			$periodoIniNew = date('Y-m-d', strtotime('+1 day ', strtotime($infoPeriod[0]['date_finish']))); //le sumo un dia 
			$periodoFinNew = date('Y-m-d', strtotime('+14 day ', strtotime($infoPeriod[0]['date_finish']))); //le sumo 14 dias
			//sacar el año de la vigencia
			$yearPeriodo = date("Y", strtotime($periodoIniNew));


			//guardo el nuevo periodo
			$arrParam = array(
				"periodoIniNew" => $periodoIniNew,
				"periodoFinNew" => $periodoFinNew,
				"yearPeriodo" => $yearPeriodo
			);

			if ($idPeriod = $this->payroll_model->savePeriod($arrParam)) {
				//guardo las dos semanas del periodo
				$semana1IniNew = date('Y-m-d', strtotime('+1 day ', strtotime($infoPeriod[0]['date_finish']))); //le sumo un dia 
				$semana1FinNew = date('Y-m-d', strtotime('+7 day ', strtotime($infoPeriod[0]['date_finish']))); //le sumo 7 dias
				$semana2IniNew = date('Y-m-d', strtotime('+1 day ', strtotime($semana1FinNew))); //le sumo un dia 
				$semana2FinNew = date('Y-m-d', strtotime('+7 day ', strtotime($semana1FinNew))); //le sumo 7 dias
				//guardo la primer semana
				$arrParam = array(
					"idPeriod" => $idPeriod,
					"semana1IniNew" => $semana1IniNew,
					"semana1FinNew" => $semana1FinNew,
					"semana2IniNew" => $semana2IniNew,
					"semana2FinNew" => $semana2FinNew,
				);
				$this->payroll_model->saveWeakPeriod($arrParam);
			}
		}
		return TRUE;
	}

	/**
	 * Payroll form search
	 * @since 10/02/2022
	 * @author BMOTTAG
	 */
	public function payrollSearchForm($contractType = 'x', $idPeriod = 'x', $idEmployee = '')
	{
		$this->load->model("general_model");

		$actualYear = Date("Y");
		$arrParam = array("year_period" => $actualYear);
		$data['infoPeriod'] = $this->general_model->get_period($arrParam); //lista de periodos para el presente ano

		$data["view"] = "form_search";

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($contractType  != 'x' && $idPeriod != 'x' || $_POST) {
			if ($idPeriod != 'x') {
				$data['contractType'] =  $contractType;
				$data['idPeriod'] =  $idPeriod;
				$data['idEmployee'] =  $idEmployee;
			}

			if ($this->input->post('period')) {
				$data['contractType'] =  $this->input->post('contractType');
				$data['idPeriod'] =  $this->input->post('period');
				$data['idEmployee'] =  $this->input->post('employee');
			}

			$arrParam = array("idPeriod" => $data['idPeriod']);
			$data['infoPeriod'] = $this->general_model->get_period($arrParam); //info del periodo

			$data['infoWeakPeriod'] = $this->general_model->get_weak_period($arrParam); //lista de periodos los ultimos 10

			$arrParam = array(
				"idPeriod" => $data['idPeriod'],
				"idEmployee" => $data['idEmployee'],
				"employee_subcontractor" => $data['contractType']
			);
			$data['info'] = $this->general_model->get_users_by_period($arrParam);
			$data["view"] = $data['contractType'] == 2 ? "list_payroll" : "list_payroll_subcontractor";
		}
		$data['dashboardURL'] = $this->session->userdata("dashboardURL");
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Save paystub
	 * @since 21/2/2022
	 * @author BMOTTAG
	 */
	public function save_paystub()
	{
		$contractType =  $this->input->post('contractType');
		$idPeriod =  $this->input->post('period');
		$idEmployee =  $this->input->post('employee');

		$userBankTime =  $this->input->post('hddBankTime');
		$bankTimeFlag =  $this->input->post('hddBankTimeFlag');
		$btnSubmit =  $this->input->post('btnSubmit');
		$btnGenerate =  $this->input->post('btnGenerate');

		if ($this->payroll_model->savePaystub()) {
			$this->session->set_flashdata('retornoExito', "You have saved the Paystub!!");
			if (isset($btnGenerate)) {
				//if user is with bank time, then i have to update the new balanace
				if ($userBankTime == 1 && $bankTimeFlag != "") {
					$arrParamBankTime = array(
						"idPeriod" => $idPeriod,
						"idEmployee" => $idEmployee,
						"bankTimeAdd" => $this->input->post('hddBankTimeAdd'),
						"bankTimeSubtract" => $this->input->post('hddBankTimeSubtract'),
						"bankNewBalance" => $this->input->post('hddBankTimeNewBalance'),
						"observation" => "New Paystub"
					);
					$this->load->model("general_model");
					$this->general_model->saveBankTimeBalance($arrParamBankTime);
				}

				//update TABLE task set period_status to 2 (PAID)
				$this->payroll_model->updateTaskStatus();

				//update TABLE payroll_total_yearly
				//buscar el total de valores por año y usuario
				$arrParam = array(
					"idUser" => $this->input->post('hddIdUser'),
					"year" => $this->input->post('hddYear')
				);
				$this->load->model("general_model");
				$infoTotalYear = $this->general_model->get_total_yearly($arrParam);
				$this->payroll_model->updatePayrollTotalYearly($infoTotalYear);
			}

			$data["result"] = true;
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('payroll/payrollSearchForm/' . $contractType . '/' . $idPeriod . '/' . $idEmployee), 'refresh');
	}

	/**
	 * Payroll form search, to review paystubs
	 * @since 28/02/2022
	 * @author BMOTTAG
	 */
	public function reviewPaystubs()
	{
		$this->load->model("general_model");

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($_POST) {
			$data['year'] =  $this->input->post('year');
			$data['idEmployee'] =  $this->input->post('employee');

			$arrParam = array(
				"year" => $data['year'],
				"idEmployee" => $data['idEmployee']
			);
			$data['info'] = $this->general_model->get_paystub_by_period($arrParam);
		}
		$data["view"] = "form_search_paystubs";
		$data['dashboardURL'] = $this->session->userdata("dashboardURL");
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Payroll form search, to review total yearly
	 * @since 28/02/2022
	 * @author BMOTTAG
	 */
	public function reviewYearly()
	{
		$this->load->model("general_model");

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($_POST) {
			$data['year'] =  $this->input->post('year');
			$data['idEmployee'] =  $this->input->post('employee');

			$arrParam = array(
				"year" => $data['year'],
				"idUser" => $data['idEmployee']
			);
			$data['info'] = $this->general_model->get_total_yearly($arrParam);
		}
		$data["view"] = "form_search_total_yearly";
		$data['dashboardURL'] = $this->session->userdata("dashboardURL");
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Payroll form search
	 * @since 10/04/2022
	 * @author BMOTTAG
	 */
	public function payrollSearchTimeSheet($idPeriod = 'x', $idEmployee = '')
	{
		$this->load->model("general_model");

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$data["view"] = "form_search_time_sheet";

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($this->input->post('from')) {
			if ($idPeriod != 'x') {
				$data['idPeriod'] =  $idPeriod;
				$data['idEmployee'] =  $idEmployee;
			}

			if ($this->input->post('from')) {
				$data['idEmployee'] =  $this->input->post('employee');

				$from =  $this->input->post('from');
				$to =  $this->input->post('to');
				$data['from'] = formatear_fecha($from);
				$data['to'] = formatear_fecha($to);

				//le sumo un dia al dia final para que ingrese ese dia en la consulta
				$data['to'] = date('Y-m-d', strtotime('+1 day ', strtotime($data['to'])));
			}

			$arrParam = array(
				"from" => $data['from'],
				"to" => $data['to'],
				"idEmployee" => $data['idEmployee']
			);
			$data['info'] = $this->general_model->get_users_by_period($arrParam);
			$data["view"] = "list_payroll_time_sheet";
		}
		$data['dashboardURL'] = $this->session->userdata("dashboardURL");
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Form Payroll Check
	 * Used by cron
	 * @since 12/04/2022
	 * @author BMOTTAG
	 */
	public function payroll_check()
	{
		$this->load->model("general_model");
		$records = $this->general_model->get_payroll_check();
pr($records);		
		foreach ($records as $data) :
			$fechaStart = strtotime($data['start']);
			$fechaStart = date("Y-m-d G:i:s", $fechaStart);
			$fechaActual = date("Y-m-d G:i:s");

			//START hours calculation
			$hours = (strtotime($fechaStart) - strtotime($fechaActual)) / 3600;
			$hours = abs($hours);
			$hours = round($hours);
			echo "IDTASK: " . $data['id_task'] . " - ";
			echo $hours . " - ";
			if ($hours > 18) {
				//end the current task automatically
				echo "entro a cerrar payroll";
				$this->updatePayrollAutomatically($data['id_task'], $data['start']);
			} elseif ($hours > 14) {
				//send sms to the employee
				echo "entro a enviar mensaje de text";
				$this->sendSMSWorkerTask($data['fk_id_user']);
			}
			echo "<br>";
		endforeach;
	}

	/**
	 * Close finish time payroll - automatically
	 * @since 13/04/2022
	 * @author BMOTTAG
	 */
	public function updatePayrollAutomatically($idTask, $start)
	{
		$fechaStart = strtotime($start);
		$finish = date("Y-m-d G:i:s");
		$this->payroll_model->updateWorkingTimePayrollCheck($start, $finish, $idTask);
		/**
		 * Guardar el id del periodo en la tabla task
		 * busco el periodo, sino existe lo creo
		 */
		$this->save_period($idTask);

		return true;
	}

	/**
	 * Send text message to employee when he haven't checkout
	 * @since 13/4/2022
	 * @author BMOTTAG
	 */
	public function sendSMSWorkerTask($idUser)
	{
		$this->load->library('encrypt');
		require 'vendor/Twilio/autoload.php';

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$this->load->model("general_model");
		$parametric = $this->general_model->get_basic_search($arrParam);
		$twilioSID = $this->encrypt->decode($parametric[3]["value"]);
		$twilioToken = $this->encrypt->decode($parametric[4]["value"]);
		$twilioPhone = $parametric[5]["value"];

		$client = new Twilio\Rest\Client($twilioSID, $twilioToken);

		//task control info
		$arrParam = array("idUser" => $idUser);
		$userInfo = $this->general_model->get_user($arrParam);

		$mensaje = "VCI TIME SHEET";
		$mensaje .= "\n" . $userInfo[0]['first_name'] . ' ' .  $userInfo[0]['last_name'];
		$mensaje .= "\n";
		$mensaje .= "This message is to remind you that you have been working more than 14 hours, it is possible that you forgot to check out, if it is the case please login the system and check out.";

		$to = '+1' . $userInfo[0]['movil'];

		// Use the client to do fun stuff like send text messages!
		$client->messages->create(
			// the number you'd like to send the message to
			$to,
			array(
				// A Twilio phone number you purchased at twilio.com/console
				'from' => $twilioPhone,
				'body' => $mensaje
			)
		);

		return true;
	}

	/**
	 * Generate PAYSTUB
	 * @param int $idPaytsub
	 * @since 24/12/2022
	 * @author BMOTTAG
	 */
	public function generaPaystubPDF($idPaytsub)
	{
		$this->load->library('Pdf');
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$arrParam = array("idPaytsub" => $idPaytsub);
		$data['infoPaystub'] = $this->payroll_model->get_paystub_by_period($arrParam);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('WORK ORDER');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'WORK ORDER', 'W.O. #: ' . $idPaytsub . "\nW.O. date: " . $idPaytsub, array(0, 64, 255), array(0, 64, 128));

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

		// add a page
		//$pdf->AddPage('L', 'A4');
		$pdf->AddPage();

		$html = $this->load->view("report_paystub", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();

		//Close and output PDF document
		$pdf->Output('paystub_' . $idPaytsub . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Employee list by contract type
	 * @since 11/9/2022
	 * @author BMOTTAG
	 */
	public function employeeList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$identificador = $this->input->post('identificador');
		$arrParam = array(
			"state" => 1,
			"employee_subcontractor" => $identificador
		);
		$this->load->model("general_model");
		$lista = $this->general_model->get_user($arrParam); //workers list
		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["id_user"] . "' >" . $fila["first_name"] . " " . $fila["last_name"] . "</option>";
			}
		}
	}

	/**
	 * Period list by year
	 * @since 23/12/2022
	 * @author BMOTTAG
	 */
	public function periodList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$identificador = $this->input->post('identificador');

		$arrParam = array("year_period" => $identificador);
		$this->load->model("general_model");
		$lista = $this->general_model->get_period($arrParam); //lista de periodos los ultimos 10	

		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["id_period"] . "' >" . $fila["period"] . "</option>";
			}
		}
	}

	/**
	 * NOTIFICATION HOURS PAYROLL CHECK
	 * CRON: Time: Every Day at 12am
	 */
	public function hours_payroll_check()
	{
		$this->load->model("general_model");
		$infoTask = $this->general_model->get_without_work_order();
		if ($infoTask) {
			//send notifiation
			$subjet = "Hour sin WO";

			//mensaje de texto
			$mensajeSMS = "APP VCI - " . $subjet;
			$mensajeSMS .= "\nHoras sin asignar a WO.";
			$mensajeSMS .= "\nFollow the link to see the list.";
			$mensajeSMS .= "\n\n" . base_url("dashboard/without_work_order");

			//enviar correo a VCI
			$arrParam = array(
				"idNotification" => ID_NOTIFICATION_HOURS_PAYROLL_CHECK,
				"subjet" => $subjet,
				"msjEmail" => '',
				"msjPhone" => $mensajeSMS
			);
			send_notification($arrParam);
		}

		return true;
	}

	/**
	 * Cargo modal- formulario para editar las horas de los empleados
	 * @since 2/2/2018
	 */
	public function cargarModalJobCode()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		//job list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam); //job list

		//busco inicio y fin para calcular horas de trabajo y guardar en la base de datos
		//START search info for the task
		$idTask =  $this->input->post('idTask');
		$data['information'] = $this->payroll_model->get_taskbyid($idTask);

		$data['hours_start'] = $this->separarHorasMinutosEntero($data['information']['hours_start_project']);
		$data['hours_end'] = $this->separarHorasMinutosEntero($data['information']['hours_end_project']);
		$this->load->view("modal_job_code", $data);
	}

	function separarHorasMinutosEntero($horas_decimal)
	{
		/**
		 * Separa las horas y los minutos de un valor decimal.
		 *
		 * @param float $horas_decimal Un número decimal que representa las horas (ej. 4.25).
		 * @return array Un array con las horas y los minutos como enteros.
		 */
		$horas = intval($horas_decimal); // Obtiene la parte entera
		$minutos_decimal = $horas_decimal - $horas; // Obtiene la parte decimal
		$minutos = round($minutos_decimal * 60);  // Convierte a minutos y redondea
		return array("horas" => $horas, "minutos" => $minutos);
	}

	/**
	 * @since 2/2/2018
	 * @author BMOTTAG
	 */
	public function updateTaskWithWO()
	{
		header('Content-Type: application/json');

		$data = array();

		$idTask = $this->input->post('hddIdentificador');
		$data["idRecord"] = $idTask;

		if ($this->payroll_model->updateTaskWithWO()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', 'You have updated the payroll hour');
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}
}
