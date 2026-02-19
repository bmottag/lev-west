<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin_model");
		$this->load->model("general_model");
		$this->load->helper('form');
	}

	/**
	 * employee List
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function employee($state)
	{
		$data['state'] = $state;
		if ($state == 1) {
			$arrParam = array("filtroState" => TRUE);
		} else {
			$arrParam = array("state" => $state);
		}

		$data['info'] = $this->general_model->get_user($arrParam);

		$data["view"] = 'employee';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario Employee
	 * @since 15/12/2016
	 */
	public function cargarModalEmployee()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idEmployee"] = $this->input->post("idEmployee");

		$arrParam = array("filtro" => TRUE);
		$data['roles'] = $this->general_model->get_roles($arrParam);

		if ($data["idEmployee"] != 'x') {
			$arrParam = array(
				"table" => "user",
				"order" => "id_user",
				"column" => "id_user",
				"id" => $data["idEmployee"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("employee_modal", $data);
	}

	/**
	 * Update Employee
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function save_employee()
	{
		header('Content-Type: application/json');
		$data = array();

		$idUser = $this->input->post('hddId');

		$msj = "You have added a new Employee!!";
		if ($idUser != '') {
			$msj = "You have updated an Employee!!";
		}

		$log_user = $this->input->post('user');
		$social_insurance = $this->input->post('insuranceNumber');

		$result_user = false;
		$result_insurance = false;
		$data["state"] = $this->input->post('state');
		if ($idUser == '') {
			$data["state"] = 1; //para el direccionamiento del JS, cuando es usuario nuevo no se envia state

			//Verify if the user already exist by the user name
			$arrParam = array(
				"column" => "log_user",
				"value" => $log_user
			);
			$result_user = $this->general_model->verifyUser($arrParam);
			//Verify if the user already exist by the social insurance number
			$arrParam = array(
				"column" => "social_insurance",
				"value" => $social_insurance
			);
			$result_insurance = $this->general_model->verifyUser($arrParam);
		}

		if ($result_user || $result_insurance) {
			$data["result"] = "error";
			if ($result_user) {
				$data["mensaje"] = " Error. The user already exist by the user name.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> The user already exist by the user name.');
			}
			if ($result_insurance) {
				$data["mensaje"] = " Error. The user already exist by the Social Insurance Number.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> The user already exist by the social insurance number.');
			}
			if ($result_user && $result_insurance) {
				$data["mensaje"] = " Error. The user already exist by the user name and the Social Insurance Number.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> The user already exist by the user name and the Social Insurance Number.');
			}
		} else {
			if ($this->admin_model->saveEmployee()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		}

		echo json_encode($data);
	}

	/**
	 * Reset employee password
	 * Reset the password to '123456'
	 * And change the status to '0' to changue de password 
	 * @since 11/1/2017
	 * @author BMOTTAG
	 */
	public function resetPassword($idUser)
	{
		if ($this->admin_model->resetEmployeePassword($idUser)) {
			$this->session->set_flashdata('retornoExito', 'You have reset the Employee pasword to: 123456');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect("/admin/employee/", 'refresh');
	}

	/**
	 * Material List
	 * @since 13/12/2016
	 * @author BMOTTAG
	 */
	public function material()
	{
		$data['info'] = $this->admin_model->get_material_with_shop();
		$data["view"] = 'material';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario material type
	 * @since 13/12/2016
	 */
	public function cargarModalMaterial()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idMaterial"] = $this->input->post("idMaterial");

		if ($data["idMaterial"] != 'x') {
			$arrParam = array(
				"table" => "param_material_type",
				"order" => "id_material",
				"column" => "id_material",
				"id" => $data["idMaterial"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("material_modal", $data);
	}

	/**
	 * Update material
	 * @since 13/12/2016
	 * @author BMOTTAG
	 */
	public function save_material()
	{
		header('Content-Type: application/json');
		$data = array();

		$idMaterial = $this->input->post('hddId');

		$msj = "You have added a new Material Type!!";
		if ($idMaterial != '') {
			$msj = "You have updated a Material Type!!";
		}

		if ($idMaterial = $this->admin_model->saveMaterial()) {
			$data["result"] = true;
			$data["idRecord"] = $idMaterial;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Cargo modal - Shop
	 * @since 30/10/2023
	 */
	public function loadModalShop()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idMaterial"] = $this->input->post("idMaterial");

		$arrParam = array(
			"table" => "param_shop",
			"order" => "shop_name",
			"id" => "x"
		);
		$data['shopList'] = $this->general_model->get_basic_search($arrParam);

		$this->load->view("shop_modal", $data);
	}

	/**
	 * Save Shop Parts
	 * @since 30/10/2023
	 * @author BMOTTAG
	 */
	public function save_shop_materials()
	{
		header('Content-Type: application/json');
		$data = array();

		$idMaterial = $this->input->post('hddId');

		$msj = "You have added the Shop Information for Material!!";
		if ($idMaterial != '') {
			$msj = "You have updated the Shop Information for Material!!";
		}

		if ($idMaterial = $this->admin_model->saveShopParts()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Company List
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function company()
	{
		//se filtra por company_type para que solo se pueda editar los subcontratistas
		$arrParam = array(
			"table" => "param_company",
			"order" => "id_company",
			"column" => "company_type",
			"id" => 2
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = 'company';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario company
	 * @since 15/12/2016
	 */
	public function cargarModalCompany()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idCompany"] = $this->input->post("idCompany");

		if ($data["idCompany"] != 'x') {
			$arrParam = array(
				"table" => "param_company",
				"order" => "id_company",
				"column" => "id_company",
				"id" => $data["idCompany"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("company_modal", $data);
	}

	/**
	 * Update company
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function save_company()
	{
		header('Content-Type: application/json');
		$data = array();

		$idCompany = $this->input->post('hddId');

		$msj = "You have added a new company!!";
		if ($idCompany != '') {
			$msj = "You have updated a company!!";
		}

		if ($idCompany = $this->admin_model->saveCompany()) {
			$data["result"] = true;
			$data["idRecord"] = $idCompany;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * hazard List
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function hazard()
	{
		$data['info'] = $this->admin_model->get_hazard_list();

		$data["view"] = 'hazard';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario hazard
	 * @since 15/12/2016
	 */
	public function cargarModalHazard()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idHazard"] = $this->input->post("idHazard");

		$arrParam = array(
			"table" => "param_hazard_activity",
			"order" => "hazard_activity",
			"id" => "x"
		);
		$data['activityList'] = $this->general_model->get_basic_search($arrParam);

		$arrParam = array(
			"table" => "param_hazard_priority",
			"order" => "priority_description",
			"id" => "x"
		);
		$data['priorityList'] = $this->general_model->get_basic_search($arrParam);

		if ($data["idHazard"] != 'x') {
			$arrParam = array(
				"table" => "param_hazard",
				"order" => "id_hazard",
				"column" => "id_hazard",
				"id" => $data["idHazard"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("hazard_modal", $data);
	}

	/**
	 * Update hazard
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function save_hazard()
	{
		header('Content-Type: application/json');
		$data = array();

		$idHazard = $this->input->post('hddId');

		$msj = "You have added a new hazard!!";
		if ($idHazard != '') {
			$msj = "You have updated a hazard!!";
		}

		if ($idHazard = $this->admin_model->saveHazard()) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";
			$data["idRecord"] = $idHazard;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * job List
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function job($state)
	{
		if ($state == 'log') {

			//job list
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "state",
				"id" => 1
			);
			$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list

			$arrParam = array(
				"table" => "user",
				"order" => "id_user",
				"column" => "id_user",
				"id" => "x"
			);
			$data['user'] = $this->general_model->get_basic_search($arrParam); //job list

			if ($this->input->post('jobName') || $this->input->post('user') || $this->input->post('from')) {

				$data['jobName'] =  $this->input->post('jobName');
				$data['user'] =  $this->input->post('user');
				$data['from'] =  $this->input->post('from');
				$data['to'] =  $this->input->post('to');

				//le sumo un dia al dia final para que ingrese ese dia en la consulta
				if ($data['to']) {
					$to = date('Y-m-d', strtotime('+1 day ', strtotime(formatear_fecha($data['to']))));
				} else {
					$to = "";
				}
				if ($data['from']) {
					$from = formatear_fecha($data['from']);
				} else {
					$from = "";
				}

				$arrParam = array(
					"jobId" => $this->input->post('jobName'),
					"userId" => $this->input->post('user'),
					"from" => $from,
					"to" => $to
				);

				//informacion Work Order
				$data['workOrderInfo'] = $this->admin_model->get_job_log($arrParam);

				$data["view"] = "log_list";
				$this->load->view("layout_calendar", $data);
			} else {
				$data["view"] = 'job_log';
				$this->load->view("layout", $data);
			}
		} else {
			$data['state'] = $state;

			$arrParam['state'] = $state;
			$data['info'] = $this->general_model->get_job($arrParam);
			$data['dashboardURL'] = $this->session->userdata("dashboardURL");
			$data["view"] = 'job';
			$this->load->view("layout_calendar", $data);
		}
	}

	/**
	 * Cargo modal - formulario job
	 * @since 15/12/2016
	 */
	public function cargarModalJob()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idJob"] = $this->input->post("idJob");

		//company list
		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => 2
		);
		$data['companyList'] = $this->general_model->get_basic_search($arrParam);

		if ($data["idJob"] != 'x') {
			$arrParam['idJob'] = $data["idJob"];
			$data['information'] = $this->general_model->get_job($arrParam);
		}

		$this->load->view("job_modal", $data);
	}

	/**
	 * Update job
	 * @since 15/12/2016
	 * @author BMOTTAG
	 */
	public function save_job()
	{
		header('Content-Type: application/json');
		$data = array();

		$idJob = $this->input->post('hddId');
		$jobCode = trim($this->security->xss_clean($this->input->post('jobCode')));
		$jobName = trim($this->security->xss_clean($this->input->post('jobName')));
		$jobDescription = $jobCode . " " . $jobName;
		$companyId = trim($this->security->xss_clean($this->input->post('company')));

		$msj = "You have added a new job!!";
		if ($idJob != '') {
			$msj = "You have updated a Job!!";
		}

		//verificar si ya el job code
		$arrParam = array(
			"idJob" => $idJob,
			"column" => "job_code",
			"value" => $jobCode
		);
		$result_job = $this->general_model->jobCodeVerify($arrParam);

		if ($result_job) {
			$data["result"] = "error";
			$data["mensaje"] = " Error. The Job Code already exist.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> The Job Code already exist.');
		} else {
			if ($idJobSaved = $this->admin_model->saveJob()) {

				//If it is a new JOB, then send text messague to SAFETY USER
				if ($idJob == '') {
					//revisar si se envia mensaje de texto y a quien se le envia
					$arrParam = array("idNotification" => ID_NOTIFICATION_NEW_JOB);
					$configuracionAlertas = $this->general_model->get_notifications_access($arrParam);

					if ($configuracionAlertas) {

						//mensaje de texto
						$mensajeSMS = "NEW JOB APP-VCI";
						$mensajeSMS .= "\nFor your records, a new Job Code has been created in the system.";
						$mensajeSMS .= "\nJob Code/Name: " . $jobDescription;

						if ($companyId) {
							$arrParam = array(
								"table" => "param_company",
								"order" => "id_company",
								"column" => "id_company",
								"id" => $companyId
							);
							$company = $this->general_model->get_basic_search($arrParam); //company list

							$company = $company[0]['company_name'];

							$mensajeSMS .= "\nCompany name: " . $company;

							//foreman company
							$arrParam = array(
								"table" => "param_company_foreman",
								"order" => "fk_id_param_company",
								"column" => "fk_id_param_company",
								"id" => $companyId
							);
							$company_foreman = $this->general_model->get_basic_search($arrParam); //company list

							if ($company_foreman) {
								$company_foreman = $company_foreman[0]['foreman_name'];

								$mensajeSMS .= "\nForeman name: " . $company_foreman;
							}
						}

						$this->db->select("L.*, CONCAT(first_name, ' ', last_name) name, J.job_description");
						$this->db->join('user U', 'U.id_user = L.created_by', 'INNER');
						$this->db->join('param_jobs j', 'L.type_id = j.id_job', 'LEFT');
						$this->db->order_by('L.id', 'asc');
						$this->db->where('L.type_id', $idJobSaved);
						$this->db->where('L.token', 'insert');
						$query = $this->db->get('logger L');
						$data = $query->result_array();

						$mensajeSMS .= "\nCreate for: " . $data[0]['name'];

						$this->sendNotifications($configuracionAlertas, $mensajeSMS);
					}
				}

				//save info FOREMAN
				$nameForeman = $this->input->post('foreman');
				if ($nameForeman != '') {
					$this->admin_model->save_foreman($idJobSaved);
				}

				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		}

		echo json_encode($data);
	}

	/**
	 * vehicle List
	 * @since 15/12/2016
	 * @review 5/5/2017
	 * @author BMOTTAG
	 */
	public function vehicle($companyType, $vehicleType = 1, $vehicleState = 1)
	{
		$data['companyType'] = $companyType;
		$data['vehicleType'] = $vehicleType;
		$data['vehicleState'] = $vehicleState;
		$data['title'] = $companyType == 1 ? "VCI" : "RENTALS";

		$arrParam = array(
			"companyType" => $companyType,
			"vehicleState" => $vehicleState
		);
		//si es estado en 1 entonces envio el tipo de vehiculo
		if ($vehicleState == 1) {
			$arrParam['vehicleType'] = $vehicleType;
		}

		$data['info'] = $this->admin_model->get_vehicle_info_by($arrParam); //vehicle list
		$data["view"] = 'vehicle';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario vehicle
	 * @since 15/12/2016
	 * @review 27/12/2016
	 */
	public function cargarModalVehicle()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$idVehicle = $this->input->post("idVehicle");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idVehicle);

		$data["companyType"] = $porciones[0];
		$data["idVehicle"] = $porciones[1];

		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => 2
		);
		$data['company'] = $this->general_model->get_basic_search($arrParam); //company list

		//buscar la lista de tipo de vehiculo
		$arrParam = array(
			"table" => "param_vehicle_type_2",
			"order" => "type_2",
			"column" => "show_vehicle",
			"id" => 1
		);
		$data['vehicleType'] = $this->general_model->get_basic_search($arrParam); //vehicleType list

		if ($data["idVehicle"] != 'x') {
			$arrParam = array(
				"table" => "param_vehicle",
				"order" => "id_vehicle",
				"column" => "id_vehicle",
				"id" => $data["idVehicle"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("vehicle_modal", $data);
	}

	/**
	 * Update vehicle
	 * @since 15/12/2016
	 * @review 27/12/2016
	 * @author BMOTTAG
	 */
	public function save_vehicle()
	{
		header('Content-Type: application/json');
		$data = array();

		$idVehicle = $this->input->post('hddId');
		$idCompany = $this->input->post('company');
		$data["compannyType"] = $idCompany == 1 ? 1 : 2; //1:VCI; 2:Subcontractor

		$pass = $this->generaPass(); //clave para colocarle al codigo QR

		$msj = "You have added a new vehicle!!";
		$flag = true;
		if ($idVehicle != '') {
			$msj = "You have updated a vehicle!!";
			$flag = false;
		}

		if ($idVehicle = $this->admin_model->saveVehicle($pass)) {

			if ($flag) { //si es un registro nuevo entonces guardo el historial de cambio de aceite
				$state = 0; //primer registro
				$this->admin_model->saveVehicleNextOilChange($idVehicle, $state);

				//si es un registro nuevo genero el codigo QR y subo la imagen
				//INCIO - genero imagen con la libreria y la subo 
				$this->load->library('ciqrcode');

				$valorQRcode = base_url("login/index/" . $idVehicle . $pass);
				$rutaImagen = "images/vehicle/" . $idVehicle . "_qr_code.png";

				$params['data'] = $valorQRcode;
				$params['level'] = 'H';
				$params['size'] = 10;
				$params['savename'] = FCPATH . $rutaImagen;

				$this->ciqrcode->generate($params);
				//FIN - genero imagen con la libreria y la subo
			}

			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	public function generaPass()
	{
		//Se define una cadena de caractares. Te recomiendo que uses esta.
		$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		//Obtenemos la longitud de la cadena de caracteres
		$longitudCadena = strlen($cadena);

		//Se define la variable que va a contener la contraseña
		$pass = "";
		//Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
		$longitudPass = 50;

		//Creamos la contraseña
		for ($i = 1; $i <= $longitudPass; $i++) {
			//Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
			$pos = rand(0, $longitudCadena - 1);

			//Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
			$pass .= substr($cadena, $pos, 1);
		}
		return $pass;
	}

	/**
	 * photo
	 */
	public function photo($idVehicle, $error = '')
	{
		if (empty($idVehicle)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//busco datos del vehiculo
		$arrParam = array(
			"idVehicle" => $idVehicle
		);
		$data['vehicleInfo'] = $this->admin_model->get_vehicle_info_by($arrParam);

		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 
		$data['idVehicle'] = $idVehicle;
		$data["view"] = 'vehicle_photo';
		$this->load->view("layout", $data);
	}

	/**
	 * FUNCIÓN PARA SUBIR LA IMAGEN 
	 * @param int vistaRegreso -> para saber si es de VCI o RENTADA
	 */
	function do_upload($type, $vistaRegreso)
	{
		$config['upload_path'] = './images/vehicle/';
		$config['overwrite'] = true;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size'] = '3000';
		$config['max_width'] = '2024';
		$config['max_height'] = '2008';
		$idVehicle = $this->input->post("hddId");
		$config['file_name'] = $idVehicle . "_" . $type;

		$this->load->library('upload', $config);
		//SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
		if (!$this->upload->do_upload()) {
			$error = $this->upload->display_errors();
			$this->$type($idVehicle, $error);
		} else {
			$file_info = $this->upload->data(); //subimos la imagen

			//USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
			//ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
			if ($type == "photo") {
				$this->_create_thumbnail($file_info['file_name']);
				$data = array('upload_data' => $this->upload->data());
				$imagen = $file_info['file_name'];
				$path = "images/vehicle/thumbs/" . $imagen;
			} elseif ($type == "qr_code") {
				$path = "images/vehicle/" . $file_info['file_name'];
			}

			//actualizamos el campo photo
			$arrParam = array(
				"table" => "param_vehicle",
				"primaryKey" => "id_vehicle",
				"id" => $idVehicle,
				"column" => $type,
				"value" => $path
			);

			$data['linkBack'] = "admin/vehicle/" . $vistaRegreso;
			$data['titulo'] = "<i class='fa fa-automobile'></i>VEHICLE";

			if ($this->general_model->updateRecord($arrParam)) {
				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have uploaded the photo.";
			} else {
				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);
			//redirect('employee/photo');
		}
	}

	//FUNCIÓN PARA CREAR LA MINIATURA A LA MEDIDA QUE LE DIGAMOS
	function _create_thumbnail($filename)
	{
		$config['image_library'] = 'gd2';
		//CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
		$config['source_image'] = 'images/vehicle/' . $filename;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		//CARPETA EN LA QUE GUARDAMOS LA MINIATURA
		$config['new_image'] = 'images/vehicle/thumbs/';
		$config['width'] = 150;
		$config['height'] = 150;
		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
	}

	/**
	 * qr_code
	 */
	public function qr_code($idVehicle, $error = '')
	{
		if (empty($idVehicle)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//busco datos del vehiculo
		$arrParam = array(
			"idVehicle" => $idVehicle
		);
		$data['vehicleInfo'] = $this->admin_model->get_vehicle_info_by($arrParam);

		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 
		$data['idVehicle'] = $idVehicle;
		$data["view"] = 'vehicle_qr_code';
		$this->load->view("layout", $data);
	}

	/**
	 * Next Oil change
	 * @param int $idVehicle
	 * @since 17/1/2017
	 */
	public function nextOilChange($idVehicle)
	{
		if (empty($idVehicle)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$data['info'] = false;
		if($data['vehicleInfo'][0]['table_inspection']){
			$data['info'] = $this->general_model->get_vehicle_oil_change($data['vehicleInfo']); //vehicle oil change history
		}

		$data['idVehicle'] = $idVehicle;
		$data["view"] = 'vehicle_inspections';
		$this->load->view("layout", $data);
	}

	/**
	 * Add vehicle oil change
	 * @since 17/1/2017
	 * @author BMOTTAG
	 */
	public function save_vehicle_oil_change()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddId');
		$state = 2; //next oil change

		if ($this->admin_model->saveVehicleNextOilChange($data["idRecord"], $state)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added the Next Oil Change");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Employee Type List
	 * @since 4/2/2017
	 * @author BMOTTAG
	 */
	public function employeeType()
	{
		$arrParam = array(
			"table" => "param_employee_type",
			"order" => "employee_type",
			"id" => "x"
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = 'employee_type';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario employee type
	 * @since 4/2/2017
	 */
	public function cargarModalEmployeeType()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idEmployeeType"] = $this->input->post("idEmployeeType");

		if ($data["idEmployeeType"] != 'x') {
			$arrParam = array(
				"table" => "param_employee_type",
				"order" => "id_employee_type",
				"column" => "id_employee_type",
				"id" => $data["idEmployeeType"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("employee_type_modal", $data);
	}

	/**
	 * Update employee type
	 * @since 4/2/2017
	 * @author BMOTTAG
	 */
	public function save_employee_type()
	{
		header('Content-Type: application/json');
		$data = array();

		$idEmployeeType = $this->input->post('hddId');

		$msj = "You have added a new Employee Type!!";
		if ($idEmployeeType != '') {
			$msj = "You have updated an Employee Type!!";
		}

		if ($idEmployeeType = $this->admin_model->saveEmployeeType()) {
			$data["result"] = true;
			$data["idRecord"] = $idEmployeeType;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Hazard Activity List
	 * @since 5/2/2017
	 * @author BMOTTAG
	 */
	public function hazardActivity()
	{
		$arrParam = array(
			"table" => "param_hazard_activity",
			"order" => "hazard_activity",
			"id" => "x"
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = 'hazard_activity';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario hazard Activity
	 * @since 5/2/2017
	 */
	public function cargarModalHazardActivity()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idHazardActivity"] = $this->input->post("idHazardActivity");

		if ($data["idHazardActivity"] != 'x') {
			$arrParam = array(
				"table" => "param_hazard_activity",
				"order" => "id_hazard_activity",
				"column" => "id_hazard_activity",
				"id" => $data["idHazardActivity"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("hazard_activity_modal", $data);
	}

	/**
	 * Update Hazard Activity
	 * @since 5/2/2017
	 * @author BMOTTAG
	 */
	public function save_hazard_activity()
	{
		header('Content-Type: application/json');
		$data = array();

		$idHazardActivity = $this->input->post('hddId');

		$msj = "You have added a new Activity!!";
		if ($idHazardActivity != '') {
			$msj = "You have updated an Activity!!";
		}

		if ($idHazardActivity = $this->admin_model->saveHazardActivity()) {
			$data["result"] = true;
			$data["idRecord"] = $idHazardActivity;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Change password
	 * @since 15/4/2017
	 * @author BMOTTAG
	 */
	public function change_password($idUser)
	{
		if (empty($idUser)) {
			show_error('ERROR!!! - You are in the wrong place. The ID USER is missing.');
		}

		$arrParam = array(
			"table" => "user",
			"order" => "id_user",
			"column" => "id_user",
			"id" => $idUser
		);
		$data['information'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = "form_password";
		$this->load->view("layout", $data);
	}

	/**
	 * Update user´s password
	 */
	public function update_password()
	{
		$data = array();
		$data["titulo"] = "UPDATE PASSWORD";

		$newPassword = $this->input->post("inputPassword");
		$confirm = $this->input->post("inputConfirm");
		$passwd = str_replace(array("<", ">", "[", "]", "*", "^", "-", "'", "="), "", $newPassword);

		$data['linkBack'] = "admin/employee/1";
		$data['titulo'] = "<i class='fa fa-unlock fa-fw'></i>CHANGE PASSWORD";

		if ($newPassword == $confirm) {
			if ($this->admin_model->updatePassword()) {
				$data["msj"] = "You have updated the password.";
				$data["msj"] .= "<br><strong>User name: </strong>" . $this->input->post("hddUser");
				$data["msj"] .= "<br><strong>Password: </strong>" . $passwd;
				$data["clase"] = "alert-success";
			} else {
				$data["msj"] = "<strong>Error!!!</strong> Ask for help.";
				$data["clase"] = "alert-danger";
			}
		} else {
			//definir mensaje de error
			echo "pailas no son iguales";
		}

		$data["view"] = "template/answer";
		$this->load->view("layout", $data);
	}

	/**
	 * Cambio de estado de los proyectos
	 * @since 12/1/2019
	 * @author BMOTTAG
	 */
	public function jobs_state($state)
	{
		if (empty($this->input->post('job'))) {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> If you want to change the state of a single JOB CODE, you must do it through the form. This functionality is intended for all JOB CODES.');
			redirect(base_url('admin/job/1'), 'refresh');
			return; 
		}

		if ($this->admin_model->updateJobsState($state)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the state!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('admin/job/1'), 'refresh');
	}

	/**
	 * Cargo modal- formulario OIL CHANGE
	 * @since 13/1/2019
	 */
	public function cargarModalOilChange()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $this->input->post("idVehicle");
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$tipo = $data['vehicleInfo'][0]['type_level_2'];

		//si es la sweeper o hydrovac muestro un formulario diferente
		if ($tipo == 15) {
			$vista = "modal_oil_change_sweeper";
		} elseif ($tipo == 16) {
			$vista = "modal_oil_change_hydrovac";
		} else {
			$vista = "modal_oil_change_normal";
		}


		$this->load->view($vista, $data);
	}

	/**
	 * Stock List
	 * @since 17/3/2020
	 * @author BMOTTAG
	 */
	public function stock()
	{
		//se filtra por company_type para que solo se pueda editar los subcontratistas
		$arrParam = array(
			"table" => "stock",
			"order" => "stock_description",
			"id" => "x"
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = 'stock';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario stock
	 * @since 17/3/2020
	 */
	public function cargarModalStock()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idStock"] = $this->input->post("idStock");

		if ($data["idStock"] != 'x') {
			$arrParam = array(
				"table" => "stock",
				"order" => "id_stock",
				"column" => "id_stock",
				"id" => $data["idStock"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("stock_modal", $data);
	}

	/**
	 * Update stock
	 * @since 17/3/2020
	 * @author BMOTTAG
	 */
	public function save_stock()
	{
		header('Content-Type: application/json');
		$data = array();

		$idStock = $this->input->post('hddId');

		$msj = "You have added a new stock!!";
		if ($idStock != '') {
			$msj = "You have updated a stock!!";
		}

		if ($idStock = $this->admin_model->saveStock()) {
			$data["result"] = true;
			$data["idRecord"] = $idStock;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Certificate List
	 * @since 14/1/2022
	 * @author BMOTTAG
	 */
	public function certificate()
	{
		$arrParam = array();
		$data['certificateList'] = $this->general_model->get_certificate_list($arrParam);

		//se filtra por id_certificate
		if ($_POST && $_POST['idCertificate'] != "") {
			$arrParam['idCertificate'] = $this->input->post('idCertificate');
		}
		$data['info'] = $this->general_model->get_certificate_list($arrParam);

		$data["view"] = 'certificate';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - Certificados
	 * @since 14/1/2022
	 */
	public function cargarModalCertificate()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idCertificate"] = $this->input->post("idCertificate");

		if ($data["idCertificate"] != 'x') {
			$arrParam = array(
				"table" => "param_certificates",
				"order" => "id_certificate",
				"column" => "id_certificate",
				"id" => $data["idCertificate"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("certificate_modal", $data);
	}

	/**
	 * Guardar certificados
	 * @since 14/1/2022
	 * @author BMOTTAG
	 */
	public function save_certificate()
	{
		header('Content-Type: application/json');
		$data = array();

		$idCertificate = $this->input->post('hddId');

		$msj = "You have added a new Certificate!!";
		if ($idCertificate != '') {
			$msj = "You have updated a Certificate!!";
		}

		if ($this->admin_model->saveCertificate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}


	/**
	 * User Certificates
	 * @param int $idEmployee
	 * @since 15/1/2022
	 */
	public function userCertificates($idUser)
	{
		if (empty($idUser)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//busco datos del vehiculo
		$arrParam['idUser'] = $idUser;
		$data['UserInfo'] = $this->general_model->get_user($arrParam);

		$data['info'] = $this->general_model->get_user_certificates($arrParam);

		$data["view"] = 'employee_certificates';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal- Formulario de certificados
	 * @since 15/1/2022
	 */
	public function cargarModalUserCertificate()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idEmployee"] = $this->input->post("idEmployee");

		$arrParam = array(
			"table" => "param_certificates ",
			"order" => "certificate",
			"id" => "x"
		);
		$data['certificateList'] = $this->general_model->get_basic_search($arrParam);

		$this->load->view("employee_certificates_modal", $data);
	}

	/**
	 * Save employee certificate
	 * @since 15/1/2022
	 * @author BMOTTAG
	 */
	public function save_employee_certificate()
	{
		header('Content-Type: application/json');
		$data = array();

		$idEmployee = $this->input->post('hddidEmployee');
		$idEmployeeCertificate = $this->input->post('hddidEmployeeCertificate');

		$data["idRecord"] = $idEmployee;
		$msj = "You have added a new Certificate!!";

		//para verificar si ya existe este certificado asigando al empleado
		$certificate_exist = FALSE;

		if ($idEmployeeCertificate == '') {
			$arrParam = array(
				"idUser" => $idEmployee,
				"idCertificate" => $this->input->post('certificate')
			);
			$certificate_exist = $this->general_model->get_user_certificates($arrParam);
		}

		if ($certificate_exist) {
			$data["result"] = "error";
			$data["mensaje"] = " Error. The Employee already has the certificate.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> The access already exist.');
		} else {
			if ($this->admin_model->saveEmployeeCertificate()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		}

		echo json_encode($data);
	}

	/**
	 * Delete Employee Certificatte
	 * @since 8/7/2018
	 */
	public function delete_user_certificate()
	{
		header('Content-Type: application/json');
		$data = array();

		$arrParam['idUserCertificate']  = $this->input->post('identificador');
		$certificate_exist = $this->general_model->get_user_certificates($arrParam);
		$data["idRecord"] = $certificate_exist[0]['fk_id_user'];

		//eliminaos registros
		$arrParam = array(
			"table" => "user_certificates",
			"primaryKey" => "id_user_certificate ",
			"id" => $arrParam['idUserCertificate']
		);

		if ($this->general_model->deleteRecord($arrParam)) {
			$data["result"] = true;
			$data["mensaje"] = "You have deleted one record.";
			$this->session->set_flashdata('retornoExito', 'You have deleted one record');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update User Certificate
	 * @since 15/1/2022
	 * @author BMOTTAG
	 */
	public function update_user_certificate()
	{
		$arrParam['idUserCertificate']  = $this->input->post('hddidEmployeeCertificate');
		$certificate_exist = $this->general_model->get_user_certificates($arrParam);
		$data["idRecord"] = $certificate_exist[0]['fk_id_user'];

		if ($this->admin_model->saveEmployeeCertificate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have update the Date!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('admin/userCertificates/' . $data["idRecord"]), 'refresh');
	}

	/**
	 * Alert List
	 * @since 23/01/2022
	 * @review 22/12/2022
	 * @author BMOTTAG
	 */
	public function notifications()
	{
		$arrParam = array();
		$data['info'] = $this->general_model->get_notifications_access_view($arrParam);

		$data["view"] = 'notifications';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario configuracion de alertas
	 * @since 23/01/2022
	 */
	public function cargarModalNotification()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idNotificationAccess"] = $this->input->post("idNotificationAccess");

		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam);

		$sql = "SELECT n.*
			FROM notifications n
			LEFT JOIN notifications_access na ON n.id_notification = na.fk_id_notification
			WHERE na.fk_id_notification IS NULL AND n.setup = 1;
			";

		$query = $this->db->query($sql);

		if ($query->num_rows() >= 1) {
			$data['notificationsList'] =  $query->result_array();
		} else {
			$data['notificationsList'] = [];
		}

		if ($data["idNotificationAccess"] != 'x') {
			$arrParam = array(
				"idNotificationAccess" => $data["idNotificationAccess"]
			);
			$data['information'] = $this->general_model->get_notifications_access_view($arrParam);
		}

		$this->load->view("notifications_modal", $data);
	}

	/**
	 * Save notifications access settings
	 * @since 23/01/2022
	 * @review 22/12/2022
	 * @author BMOTTAG
	 */
	public function save_notifications()
	{
		header('Content-Type: application/json');
		$data = array();

		$idNotificationAccess = $this->input->post('hddId');

		$msj = "You have added a new Notification Access!!";
		if ($idNotificationAccess != '') {
			$msj = "You have updated the Notification Access!!";
		}

		if ($this->admin_model->saveNotification()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * CRON
	 * Verificar si esta proximo a vencerse los certificados para los empleados activos
	 * El CRON se corre todos los lunes
	 * @since 23/1/2022
	 */
	function certifications_check()
	{
		$arrParam = array(
			"state" => 1,
			"expires" => 1,
		);
		$information  = $this->general_model->get_user_certificates($arrParam);

		if ($information) {
			//revisar si se envia correo o se envia mensaje de texto y a quien se le envia
			$arrParam = array("idNotification" => ID_NOTIFICATION_CERTIFICATION);
			$configuracionAlertas = $this->general_model->get_notifications_access($arrParam);

			if ($configuracionAlertas) {
				$filtroFecha = strtotime(date('Y-m-d'));
				$msj = "";
				//para cada certificado reviso si esta vencido
				foreach ($information as $lista) :
					//semaforo de acuerdo a fecha de vencimiento
					$fechaVencimiento = strtotime($lista['date_through']);
					$diferencia = $fechaVencimiento - $filtroFecha;
					$updateAlert = false;
					//2678400 --> equivalen a 30 dias
					if ($diferencia < 8035200 && $lista['alerts_sent'] == 0) {
						//si la diferencia es menor de 90 dias
						//envio notificacion y actualizo el campo a 1
						$updateAlert = true;
					} elseif ($diferencia <= 5356800 && $lista['alerts_sent'] == 1) {
						//si la diferencia es entre 60 dias y 30 dias 
						//envio notificacion y actualizo el campo a 2
						$updateAlert = true;
					} elseif ($diferencia <= 2678400 && $diferencia >= 0 && $lista['alerts_sent'] == 2) {
						//si la diferencia es menor de 30 dias
						//envio notificacion y actualizo el campo a 3
						$updateAlert = true;
					}

					if ($updateAlert) {
						//mensaje para el correo
						$msj .= "<p>";
						$msj .= "<strong>Employee: </strong>" . $lista['first_name'] . " " . $lista['last_name'];
						$msj .= "<br><strong>Certificate: </strong>" . $lista['certificate'];
						$msj .= "<br><strong>Date Throught : </strong>" . $lista['date_through'];
						$msj .= "</p>";

						$alerts_sent = $lista['alerts_sent'] + 1;
						$arrParam = array(
							"table" => "user_certificates",
							"primaryKey" => "id_user_certificate",
							"id" => $lista['id_user_certificate'],
							"column" => "alerts_sent",
							"value" => $alerts_sent
						);
						$this->general_model->updateRecord($arrParam);
					}
				endforeach;

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

				foreach ($configuracionAlertas as $envioAlerta) :
					//envio correo 
					if ($envioAlerta['email'] && $msj) {
						$user = $envioAlerta['name_email'];
						$to = $envioAlerta['email'];

						//Contenido correo
						$subjet = "Certificate Overdue";
						$mensaje = "<html>
							<head>
							<title> $subjet </title>
							</head>
							<body>
								<p>Dear	$user:</p>
								<p>The following employees has a certificate that is about to expire:</p>
								<p>$msj</p>
								<p>Cordially,</p>
								<p><strong>V-CONTRACTING INC</strong></p>
							</body>
							</html>";

						$headers = "MIME-Version: 1.0\r\n";
						$headers .= "Content-Type: text/html; charset=utf-8\r\n";
						$headers .= "From: VCI APP <info@v-contracting.ca>\r\n";

						//enviar correo
						$envio = mail($to, $subjet, $mensaje, $headers);
					}

					//envio mensaje de texto
					if ($envioAlerta['movil'] && $msj) {
						$to = '+1' . $envioAlerta['movil'];
						$mensaje = "APP VCI - Employees Certificates";
						$mensaje .= "\n There are some employees who have a certificate that is about to expire, go to Settings - Employee and check.";

						$client->messages->create(
							$to,
							array(
								'from' => $twilioPhone,
								'body' => $mensaje
							)
						);
					}

				endforeach;
			}
		}
		return true;
	}

	/**
	 * Employee Rate List
	 * @since 16/2/2022
	 * @author BMOTTAG
	 */
	public function employeeSettings()
	{
		$arrParam = array("filtroState" => TRUE);
		$data['info'] = $this->general_model->get_user($arrParam);

		$data['dashboardURL'] = $this->session->userdata("dashboardURL");
		$data["view"] = 'employee_settings';
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Update the employee rate of each field
	 * @since 16/2/2022
	 * @author BMOTTAG
	 */
	public function update_employee_rate()
	{
		if ($this->admin_model->updateEmployeeRate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the Employee Rate List!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url("admin/employeeSettings"), 'refresh');
	}

	/**
	 * Form CkeckIN check
	 * Used by cron
	 * @since 4/06/2022
	 * @author BMOTTAG
	 */
	public function checkin_check()
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
		$twilioSID = $this->encrypt->decode($parametric[3]["value"]);
		$twilioToken = $this->encrypt->decode($parametric[4]["value"]);
		$twilioPhone = $parametric[5]["value"];

		$client = new Twilio\Rest\Client($twilioSID, $twilioToken);

		$arrParam = array(
			"today" => date('Y-m-d'),
			"checkout" => true
		);
		$checkinList = $this->general_model->get_checkin($arrParam);

		if ($checkinList) {
			$x = 0;
			foreach ($checkinList as $data) :
				$x++;
				//send sms to the employee
				$mensaje = "VCI Sign-Out";
				$mensaje .= "\n" . $data['worker_name'];
				$mensaje .= "\n";
				$mensaje .= "This message is to remind you that you still ON the working list at the work site, it is possible that you forgot to sign out.";
				$mensaje .= "\nUse the following link to Sign-Out.";
				$mensaje .= "\n";
				$mensaje .= "\n";
				$mensaje .= base_url("external/checkin/" . $data['fk_id_job'] . "/" . $data['id_checkin']);

				$to = '+1' . $data['worker_movil'];
				$client->messages->create(
					$to,
					array(
						'from' => $twilioPhone,
						'body' => $mensaje
					)
				);
			endforeach;
			echo $x . " messages have been sent to people that haven't Check-Out";
		} else {
			echo "Everybody have the Check-Out done.";
		}
	}

	/**
	 * Employee Bank Time List
	 * @since 9/9/2022
	 * @author BMOTTAG
	 */
	public function employeBankTime($idUser)
	{
		$data["idUser"] = $idUser;

		$arrParam = array("idUser" => $idUser);
		$data['info'] = $this->general_model->get_bank_time($arrParam);

		$data["view"] = 'employee_bank_time';
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Cargo modal - formulario Add Balance to Bank time
	 * @since 9/9/2022
	 */
	public function cargarModalBankTimeBalance()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idEmployee"] = $this->input->post("idEmployee");

		$arrParam = array(
			"idUser" => $data["idEmployee"]
		);
		$data['information'] = $this->general_model->get_user($arrParam);

		$this->load->view("employee_bank_time_modal", $data);
	}

	/**
	 * Insert bank time
	 * @since 9/9/2022
	 * @author BMOTTAG
	 */
	public function save_bank_time_balance()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idEmployee"] = $this->input->post('hddId');
		$bankTimeAdd = $this->input->post('time');
		$msj = "You have added Balance to Bank Time!!";

		$arrParam = array(
			"idUser" => $data["idEmployee"],
			"limit" => 1
		);
		$infoBankTime = $this->general_model->get_bank_time($arrParam);

		$bankNewBalance = $infoBankTime ? $infoBankTime[0]["balance"] + $bankTimeAdd : $bankTimeAdd;

		$arrParamBankTime = array(
			"idPeriod" => 0,
			"idEmployee" => $data["idEmployee"],
			"bankTimeAdd" => $bankTimeAdd,
			"bankTimeSubtract" => 0,
			"bankNewBalance" => $bankNewBalance,
			"observation" => "Bank Time Added"
		);
		if ($this->general_model->saveBankTimeBalance($arrParamBankTime)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * View for JOB CODE - QR CODE
	 * @since 19/12/2022
	 * @author BMOTTAG
	 */
	public function job_qr_code($idJob)
	{
		if (empty($idJob)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//if there is not a QR CORE then it generate the QR CODE
		if (!$data['jobInfo'][0]["qr_code_timesheet"]) {
			//INCIO - genero imagen con la libreria y la subo 
			$this->load->library('ciqrcode');
			https: //v-contracting.ca/app//576
			$valorQRcode = base_url("external/checkin/" . $idJob);
			$rutaImagen = "images/qrcode/job_timesheet/" . $idJob . "_qr_code.png";

			$params['data'] = $valorQRcode;
			$params['level'] = 'H';
			$params['size'] = 10;
			$params['savename'] = FCPATH . $rutaImagen;

			$this->ciqrcode->generate($params);
			//FIN - genero imagen con la libreria y la subo

			//Update timesheet qr code field
			$arrParam = array(
				"table" => "param_jobs",
				"primaryKey" => "id_job",
				"id" => $idJob,
				"column" => "qr_code_timesheet",
				"value" => $rutaImagen
			);
			$this->general_model->updateRecord($arrParam);
		}

		$data['idJob'] = $idJob;
		$data["view"] = 'job_qr_code';
		$this->load->view("layout", $data);
	}

	/**
	 * Attachments List
	 * @since 23/06/2023
	 * @author BMOTTAG
	 */
	public function attachments($status)
	{
		$data['status'] = $status;
		$arrParam = array(
			"status" => $status
		);
		$data['info'] = $this->admin_model->get_attachments($arrParam);

		$data["view"] = 'attachment';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario company
	 * @since 23/06/2023
	 */
	public function cargarModalAttachments()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data['informationAttachments'] = FALSE;
		$data["idAttachment"] = $this->input->post("idAttachment");

		$data['equipmentType'] = $this->general_model->equipmentByTypeList();

		if ($data["idAttachment"] != 'x') {
			$arrParam = array(
				"idAttachment" => $data["idAttachment"]
			);
			$data['information'] = $this->admin_model->get_attachments($arrParam);
			$data['informationAttachments'] = $this->admin_model->get_attachments_equipment($arrParam);
		}

		$this->load->view("attachment_modal", $data);
	}

	/**
	 * Update Attachments
	 * @since 23/06/2023
	 * @author BMOTTAG
	 */
	public function save_attachments()
	{
		header('Content-Type: application/json');
		$data = array();

		$idAttachment = $this->input->post('hddId');

		$msj = "You have added a new Attachment!!";
		if ($idAttachment != '') {
			$msj = "You have updated an Attachment!!";
		}

		if ($idAttachment = $this->admin_model->saveAttachment()) {
			$this->admin_model->add_equipment_attachement($idAttachment);
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update Attachments
	 * @since 23/06/2023
	 * @author BMOTTAG
	 */
	public function update_status()
	{
		header('Content-Type: application/json');

		$data = array();

		$idAttachment = $this->input->post('attachmentId');
		$status = $this->input->post('status');
		$value = $status == "active" ? "inactive" : "active";

		$arrParam = array(
			"table" => "param_attachments",
			"primaryKey" => "id_attachment",
			"id" => $idAttachment,
			"column" => "attachment_status",
			"value" => $value
		);
		if ($this->general_model->updateRecord($arrParam)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have changed the Attachment status!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Equipment list
	 * @since 24/6/2023
	 * @author BMOTTAG
	 */
	public function equipmentList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$arrParam = array(
			"vehicleType" => $this->input->post('type'),
			"vehicleState" => 1
		);
		$lista = $this->general_model->get_vehicle_by($arrParam);

		if ($this->input->post('idAttachment') != "") {
			$arrParam = array(
				"idAttachment" => $this->input->post('idAttachment'),
				"relation" => true
			);
			$arrayInformationAttachments = $this->admin_model->get_attachments_equipment($arrParam);
		}

		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				$s = "";
				if ($arrayInformationAttachments) {
					$found = false;
					foreach ($arrayInformationAttachments as $idVehicle) {
						if (in_array($fila['id_vehicle'], $idVehicle)) {
							$found = true;
							break;
						}
					}
					$s = $found ? "selected" : "";
				}
				echo "<option value='" . $fila["id_vehicle"] . "'" . $s . ">" . $fila["unit_number"] . " -----> " . $fila["description"]  . "</option>";
			}
		}
	}

	/**
	 * Notifications
	 * @author BMOTTAG
	 * @since  14/01/2025
	 */
	public function sendNotifications($configuracionAlertas, $mensajeSMS)
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

		foreach ($configuracionAlertas as $envioAlerta):
			//envio mensaje de texto
			if ($envioAlerta['movil']) {
				$to = '+1' . $envioAlerta['movil'];
				$client->messages->create(
					$to,
					array(
						'from' => $twilioPhone,
						'body' => $mensajeSMS
					)
				);
			}

		endforeach;
		return true;
	}
}
