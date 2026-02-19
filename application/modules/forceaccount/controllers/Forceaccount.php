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

class Forceaccount extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("forceaccount_model");
		$this->load->helper('form');
	}

	/**
	 * Form Forceaccounts
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function index()
	{
		$this->load->model("general_model");

		$arrParam = array();
		$userRol = $this->session->userdata("rol");
		$idUser = $this->session->userdata("id");
		//If it is a BASIC ROLE OR SAFETY&MAINTENACE ROLE, just show the records of the user session
		if ($userRol == ID_ROL_BASIC || $userRol == ID_ROL_SAFETY) {
			$arrParam["idEmployee"] = $this->session->userdata("id");
		}
		$data['forceAccountInfo'] = $this->forceaccount_model->get_forceaccount_by_idUser($arrParam);

		//delete records for button go back from the search form
		$arrParam = array(
			"table" => "forceaccount_go_back",
			"primaryKey" => "fk_id_user",
			"id" => $idUser
		);
		$this->general_model->deleteRecord($arrParam);

		$data["view"] = 'forceaccount';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Add forceaccount
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function add_forceaccount($id = 'x')
	{
		$data['information'] = FALSE;
		$data['forceaccountPersonal'] = FALSE;
		$data['forceaccountMaterials'] = FALSE;
		$data['deshabilitar'] = '';

		$this->load->model("general_model");
		//job list - (active items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array('idForceAccount' => $id);
			$data['forceaccountPersonal'] = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list
			$data['forceaccountMaterials'] = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
			$data['forceaccountReceipt'] = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount invoice list
			$data['forceaccountEquipment'] = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list
			$data['forceaccountOcasional'] = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list
			$data['forceaccountState'] = $this->forceaccount_model->get_forceaccount_state($id); //forceaccount additional information

			$data['information'] = $this->forceaccount_model->get_forceaccount_by_idUser($arrParam); //info forceaccount

			$arrParam = array(
				"table" => "param_employee_type",
				"order" => "employee_type",
				"id" => "x"
			);
			$data['employeeTypeList'] = $this->general_model->get_basic_search($arrParam); //employee type list

			//DESHABILITAR Force Account
			$userRol = $this->session->rol;
			$forceaccountState = $data['information'][0]['state'];
			//si es diferente al rol de SUPER ADMIN
			if ($userRol != ID_ROL_SUPER_ADMIN) {
				//si esta "Closed" deshabilito los botones
				if ($forceaccountState == 4) {
					$data['deshabilitar'] = 'disabled';
					//If it is DIFERRENT THAN ON FIELD and ROLE is SUPERVISOR OR BASIC OR Safety&Maintenance
				} elseif ($forceaccountState != 0 && ($userRol == ID_ROL_SAFETY || $userRol == ID_ROL_SUPERVISOR || $userRol == ID_ROL_BASIC)) {
					$data['deshabilitar'] = 'disabled';
					//IF it is “Accounting“ solo este estado es para “Accounting“
				} elseif ($forceaccountState == 5 && $userRol != ID_ROL_ACCOUNTING_ASSISTANT) {
					$data['deshabilitar'] = 'disabled';
					//If it is "Revised" or "Send to Client" and USER is Force Account 
				} elseif (($forceaccountState == 2 || $forceaccountState == 3) && $userRol == ID_ROL_WORKORDER) {
					$data['deshabilitar'] = 'disabled';
					//If it is "Send to Client" and USER is Engineer
				} elseif (($forceaccountState == 3) && $userRol == ID_ROL_ENGINEER) {
					$data['deshabilitar'] = 'disabled';
					//If it is "In Progress" and USER is Manager or Accounting
				} elseif ($forceaccountState == 1 && ($userRol == ID_ROL_MANAGER || $userRol == ID_ROL_ACCOUNTING)) {
					$data['deshabilitar'] = 'disabled';
				}
			}

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_forceaccount';
		$this->load->view("layout", $data);
	}

	/**
	 * Save forceaccount
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save_forceaccount()
	{
		header('Content-Type: application/json');
		$data = array();

		$idForceaccountInicial = $this->input->post('hddIdentificador');

		$msj = "You have added a new Force Account, continue uploading the information.";
		if ($idForceaccountInicial != '') {
			$msj = "You have updated the Force Account, continue uploading the information.";
		}

		if ($idForceaccount = $this->forceaccount_model->add_forceaccount()) {
			$nameForeman = $this->input->post('foreman');

			if ($nameForeman != '') {
				//INICIO 
				//codigo para revisar si se actuliza o se adiciona informacion del foreman
				$this->load->model("general_model");
				$idJob = $this->input->post('jobName');

				//reviso si hay formean para esa empresa
				$arrParam = array(
					"table" => "param_company_foreman",
					"order" => "id_company_foreman",
					"column" => "fk_id_job",
					"id" => $idJob
				);
				$infoForeman = $this->general_model->get_basic_search($arrParam);

				if ($infoForeman) { //actualizo informacion del foreman
					$idForeman = $infoForeman[0]["id_company_foreman"];
				} else { //adiciono informacion del foreman
					$idForeman = '';
				}

				//guardo datos de foreman
				$this->forceaccount_model->info_foreman($idForeman);
				//FIN
			}

			//guardo el primer estado de la forceaccount
			if (!$idForceaccountInicial) {
				$arrParam = array(
					"idForceaccount" => $idForceaccount,
					"observation" => "New Force Account.",
					"state" => 0
				);
				$this->forceaccount_model->add_forceaccount_state($arrParam);
			}

			$data["result"] = true;
			$data["mensaje"] = $msj;
			$data["idForceaccount"] = $idForceaccount;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idForceaccount"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update forceaccount
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function update_forceaccount()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idForceaccount"] = $this->input->post('hddIdentificador');

		$arrParam = array(
			"table" => "forceaccount",
			"primaryKey" => "id_forceaccount",
			"id" => $data["idForceaccount"],
			"column" => "state",
			"value" => 2
		);
		$this->load->model("general_model");

		//actualizo el estado del formulario a cerrado(2)
		if ($this->general_model->updateWORecords($arrParam)) {
			$data["result"] = true;
			$data["mensaje"] = "You have closed the Force Account.";
			$this->session->set_flashdata('retornoExito', 'You have closed the Force Account');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Cargo modal- formulario de captura personal
	 * @since 16/04/2025
	 */
	public function cargarModalPersonal()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idForceaccount"] = $this->input->post("idForceaccount");

		//workers list
		$this->load->model("general_model");
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list


		//employee type list
		$arrParam = array(
			"table" => "param_employee_type",
			"order" => "employee_type",
			"id" => "x"
		);
		$data['employeeTypeList'] = $this->general_model->get_basic_search($arrParam); //employee type list

		$this->load->view("modal_personal", $data);
	}

	/**
	 * Save formularios
	 * @param varchar $modalToUse: indica que funcion del modelo se debe usar
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save($modalToUse)
	{
		header('Content-Type: application/json');
		$data = array();

		$arrParam["idForceAccount"] = $data["idRecord"] = $idForceAccount = $this->input->post('hddidForceaccount');
		$infoForceaccount = $this->forceaccount_model->get_forceaccount_by_idUser($arrParam);

		if ($this->forceaccount_model->$modalToUse()) {
			if ($modalToUse == 'saveExpense') {
				$idJob = $this->input->post('hddidJob');
				$this->update_wo_expenses_values($idForceAccount, $idJob);
				//add a flag in the WO table, for the expenses
				$arrParam = array(
					"table" => "forceaccount",
					"primaryKey" => "id_forceaccount",
					"id" => $idForceAccount,
					"order" => 'id_forceaccount',
					"column" => "expenses_flag",
					"value" => 1
				);
				$this->load->model("general_model");
				$this->general_model->updateWORecords($arrParam);
			}

			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		//si es SUPER ADMIN entonces lo redirecciono al formulario para asignar rate
		$userRol = $this->session->rol;
		$data["controlador"] = "add_forceaccount";
		if ($userRol == 99) {
			$data["controlador"] = "view_forceaccount";
		}

		echo json_encode($data);
	}

	/**
	 * Delete forceaccount record
	 * @param varchar $tabla: nombre de la tabla de la cual se va a borrar
	 * @param int $idValue: id que se va a borrar
	 * @param int $idForceaccount: llave  primaria de forceaccount
	 */
	public function deleteRecord($tabla, $idValue, $idForceAccount, $vista)
	{
		if (empty($tabla) || empty($idValue) || empty($idForceAccount)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "forceaccount_" . $tabla,
			"primaryKey" => "id_forceaccount_"  . $tabla,
			"id" => $idValue
		);
		$this->load->model("general_model");
		$this->load->library('logger');

		$table = "forceaccount_" . $tabla;

		$arrOld = array(
			"table" => $table,
			"order" => "id_forceaccount_"  . $tabla,
			"column" => "id_forceaccount_"  . $tabla,
			"id" => $idValue
		);
		$log['old'] = $this->general_model->get_basic_search($arrOld);
		$log['new'] = null;

		$this->logger
			->user($this->session->userdata("id")) //;//Set UserID, who created this  Action
			->type($table) //Entry type like, Post, Page, Entry
			->id($idForceAccount) //Entry ID
			->token('delete') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($this->general_model->deleteRecord($arrParam)) 
		{
			//si elimino PERSONAL, ENTONCES VERIFICO SI TIENE EN LA TABLA TASK ALGUN REGISTRO RELACIONADO CON LA WO
			if($tabla == 'personal'){
				$arrTask = array(
					"idForceAccount" => $idForceAccount,
					"idEmployee" => $log['old'][0]['fk_id_user'],
					"column" => "wo_start_project"
				);
				$taskStart = $this->general_model->get_task($arrTask);
				if($taskStart){
					$arrParam = array(
						"table" => "task",
						"primaryKey" => "id_task",
						"id" => $taskStart[0]["id_task"],
						"column" => "wo_start_project",
						"value" => null
					);
					$this->general_model->updateRecord($arrParam);
				}

				$arrTask = array(
					"idForceAccount" => $idForceAccount,
					"idEmployee" => $log['old'][0]['fk_id_user'],
					"column" => "wo_end_project"
				);
				$taskEnd = $this->general_model->get_task($arrTask);
				if($taskEnd){
					$arrParam = array(
						"table" => "task",
						"primaryKey" => "id_task",
						"id" => $taskEnd[0]["id_task"],
						"column" => "wo_end_project",
						"value" => null
					);
					$this->general_model->updateRecord($arrParam);
				}
			}
			//elimino de la tabla expenses
			$arrExpenses = array(
				"fk_id_forceaccount" => $idForceAccount,
				"submodule" => $tabla,
				"fk_id_submodule" => $idValue,
			);
			$this->forceaccount_model->deleteExpenses($arrExpenses);

			//para expenses recalculo los valores gastados para cada item
			$arrParam = array('idForceAccount' => $idForceAccount);
			$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount
			$idJob = $data['information'][0]["fk_id_job"];
			$this->update_wo_expenses_values($idForceAccount, $idJob);
			/**
			 * If table is expense then check if there are more records 
			 * if not then delete put flag of expenses in 0 on WO table
			 */
			$forceaccountExpense = $this->forceaccount_model->get_forceaccount_expense($arrParam); //forceaccount expense list
			if (!$forceaccountExpense) {
				$arrParam = array(
					"table" => "forceaccount",
					"primaryKey" => "id_forceaccount",
					"id" => $idForceAccount,
					"column" => "expenses_flag",
					"value" => 0
				);
				$this->general_model->updateRecord($arrParam);
			}
			$this->session->set_flashdata('retornoExito', 'You have deleted one record from <strong>' . $tabla . '</strong> table.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('forceaccount/' . $vista . '/' . $idForceAccount), 'refresh');
	}

	/**
	 * Cargo modal- formulario de captura Material
	 * @since 16/04/2025
	 */
	public function cargarModalMaterials()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idForceaccount = $this->input->post("idForceaccount");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idForceaccount);
		$data["idForceaccount"] = $porciones[1];

		//workers list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_material_type",
			"order" => "material",
			"id" => "x"
		);
		$data['materialList'] = $this->general_model->get_basic_search($arrParam); //worker´s list

		$this->load->view("modal_material", $data);
	}

	/**
	 * Cargo modal- formulario de captura Equipment
	 * @since 16/04/2025
	 */
	public function cargarModalEquipment()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idForceaccount = $this->input->post("idForceaccount");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idForceaccount);
		$data["idForceaccount"] = $porciones[1];

		$this->load->model("general_model");
		//buscar la lista de tipo de equipmentType
		$arrParam = array(
			"table" => "param_vehicle_type_2",
			"order" => "type_2",
			"column" => "show_workorder",
			"id" => 1
		);
		$data['equipmentType'] = $this->general_model->get_basic_search($arrParam); //equipmentType list

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list


		$this->load->view("modal_equipment", $data);
	}

	/**
	 * Trucks list by company and type
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function truckList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$company = 1; //la empresa es VCI que el id es 1
		$type = $this->input->post('type');
		$this->load->model("general_model");
		//si es igual a 8 es miscellaneous entonces la informacion la debe sacar de la tabla param_miscellaneous
		if ($type == 8) {
			//miscellaneous list
			$arrParam = array(
				"table" => "param_miscellaneous",
				"order" => "miscellaneous",
				"id" => "x"
			);
			$lista = $this->general_model->get_basic_search($arrParam); //miscellaneous list

			echo "<option value=''>Select...</option>";
			if ($lista) {
				foreach ($lista as $fila) {
					echo "<option value='" . $fila["id_miscellaneous"] . "' >" . $fila["miscellaneous"] . "</option>";
				}
			}
		} elseif ($type == 9) {
			$lista = $this->forceaccount_model->get_trucks_by_id1();

			echo "<option value=''>Select...</option>";
			if ($lista) {
				foreach ($lista as $fila) {
					echo "<option value='" . $fila["id_truck"] . "' >" . $fila["unit_number"] . "</option>";
				}
			}
		} else {
			$lista = $this->general_model->get_trucks_by_id2($company, $type);

			echo "<option value=''>Select...</option>";
			if ($lista) {
				foreach ($lista as $fila) {
					echo "<option value='" . $fila["id_truck"] . "' >" . $fila["unit_number"] . "</option>";
				}
			}
		}
	}

	/**
	 * Company list
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function companyList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$CompanyType = $this->input->post('CompanyType');

		//company list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => $CompanyType
		);
		$lista = $this->general_model->get_basic_search($arrParam); //company list

		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["id_company"] . "' >" . $fila["company_name"] . "</option>";
			}
		}
	}

	/**
	 * Cargo modal- formulario de captura Ocasional
	 * @since 16/04/2025
	 */
	public function cargarModalOcasional()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idForceaccount = $this->input->post("idForceaccount");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idForceaccount);

		if (count($porciones) > 1) {
			$data["idForceaccount"] = $porciones[1];

			//workers list
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "param_company",
				"order" => "company_name",
				"column" => "company_type",
				"id" => 2
			);
			$data['companyList'] = $this->general_model->get_basic_search($arrParam); //company list

			$this->load->view("modal_ocasional", $data);
		}
	}

	/**
	 * Search by JOB CODE
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function search($goBack = 'x')
	{
		//job list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list

		$arrParam = array("state" => 0);
		$data['noOnfield'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 1);
		$data['noProgress'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 2);
		$data['noRevised'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 3);
		$data['noSend'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 4);
		$data['noClosed'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 5);
		$data['noAccounting'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts

		$data["view"] = "asign_rate_form_search";

		//viene desde el boton go back
		if ($goBack == 'y') {
			$forceAccountGoBackInfo = $this->forceaccount_model->get_forceaccount_go_back();

			if (!$forceAccountGoBackInfo) {
				redirect(base_url('forceaccount'), 'refresh');
			} else {
				//le sumo un dia al dia final para que ingrese ese dia en la consulta
				$to = date('Y-m-d', strtotime('+1 day ', strtotime(formatear_fecha($forceAccountGoBackInfo['post_to']))));
				//$from = formatear_fecha($forceAccountGoBackInfo['post_from']);

				$arrParam = array(
					"jobId" => $forceAccountGoBackInfo['post_id_job'],
					"idForceAccount" => $forceAccountGoBackInfo['post_id_work_order'],
					"idForceAccountFrom" => $forceAccountGoBackInfo['post_id_wo_from'],
					"idForceAccountTo" => $forceAccountGoBackInfo['post_id_wo_to'],
					"from" => $forceAccountGoBackInfo['post_from'], //$from,
					"to" => $to,
					"state" => $forceAccountGoBackInfo['post_state']
				);

				$data['forceAccountInfo'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);

				$data["view"] = "asign_rate_list";
				$this->load->view("layout_calendar", $data);
			}
		}
		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		elseif ($this->input->post('jobName') || $this->input->post('forceAccountNumber') || $this->input->post('forceAccountNumberFrom') || $this->input->post('from')) {
			$data['jobName'] =  $this->input->post('jobName');
			$data['forceAccountNumber'] =  $this->input->post('forceAccountNumber');
			$data['forceAccountNumberFrom'] =  $this->input->post('forceAccountNumberFrom');
			$data['forceAccountNumberTo'] =  $this->input->post('forceAccountNumberTo');
			$data['from'] =  $this->input->post('from');
			$data['to'] =  $this->input->post('to');

			//le sumo un dia al dia final para que ingrese ese dia en la consulta
			if ($data['to']) {
				//$to = date('Y-m-d', strtotime('+1 day ', strtotime(formatear_fecha($data['to']))));
				$to = formatear_fecha($data['to']);
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
				"idForceAccount" => $this->input->post('forceAccountNumber'),
				"idForceAccountFrom" => $this->input->post('forceAccountNumberFrom'),
				"idForceAccountTo" => $this->input->post('forceAccountNumberTo'),
				"from" => $from,
				"to" => $to
			);

			//guardo la informacion en la base de datos para el boton de regresar
			$this->forceaccount_model->saveInfoGoBack($arrParam);

			//informacion Force Account
			$data['forceAccountInfo'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);

			$data["view"] = "asign_rate_list";
			$this->load->view("layout_calendar", $data);
		} else {
			$this->load->view("layout", $data);
		}
	}

	/**
	 * View info Force Account to asign rate
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function view_forceaccount($id)
	{
		$this->load->model("general_model");

		$arrParam = array('idForceAccount' => $id);
		$data['forceaccountExpense'] = $this->forceaccount_model->get_forceaccount_expense($arrParam); //forceaccount expense list
		$data['forceaccountPersonal'] = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list
		$data['forceaccountMaterials'] = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
		$data['forceaccountReceipt'] = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount invoice list
		$data['forceaccountEquipment'] = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list
		$data['forceaccountOcasional'] = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list

		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		//search for total WO INCOME
		$arrParam2 = array('idForceAccount' => $id);
		$arrParam2['idJob'] = $data['information'][0]['fk_id_job'];
		$arrParam2['table'] = "forceaccount_personal";
		$data['incomePersonal'] = $this->forceaccount_model->countIncome($arrParam2); //INCOME PERSOMAL

		$arrParam2['table'] = "forceaccount_materials";
		$data['incomeMaterial'] = $this->forceaccount_model->countIncome($arrParam2); //INCOME MATERIAL

		$arrParam2['table'] = "forceaccount_equipment";
		$data['incomeEquipment'] = $this->forceaccount_model->countIncome($arrParam2); //INCOME EQUIPMENT

		$arrParam2['table'] = "forceaccount_ocasional";
		$data['incomeSubcontractor'] = $this->forceaccount_model->countIncome($arrParam2); //INCOME OCASIONAL

		$arrParam2['table'] = "forceaccount_receipt";
		$data['incomeReceipt'] = $this->forceaccount_model->countIncome($arrParam2); //INCOME RECEIPT

		$data['totalWOIncome'] = $data['incomePersonal'] + $data['incomeMaterial'] + $data['incomeEquipment'] + $data['incomeSubcontractor'] + $data['incomeReceipt'];

		//DESHABILITAR Force Account
		$userRol = $this->session->rol;
		$forceaccountState = $data['information'][0]['state'];
		$data['deshabilitar'] = '';

		//si esta cerrada deshabilito los botones
		if ($forceaccountState == 4) {
			$data['deshabilitar'] = 'disabled';
			//If it is DIFERRENT THAN ON FILD and ROLE is SUPERVISOR OR BASIC OR Safety&Maintenance
		} elseif ($forceaccountState != 0 && ($userRol == 4 || $userRol == 6 || $userRol == 7)) {
			$data['deshabilitar'] = 'disabled';
		} elseif (($forceaccountState == 2 || $forceaccountState == 3) && $userRol == 5) { //Force Account  USER
			$data['deshabilitar'] = 'disabled';
		} elseif ($forceaccountState == 1 && ($userRol == 2 || $userRol == 3)) { //MANAGEMENT AND ACCOUNTING USER
			$data['deshabilitar'] = 'disabled';
		}

		$data["view"] = 'asign_rate_form';
		$this->load->view("layout", $data);
	}

	/**
	 * Save rate
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save_rate()
	{
		$idForceaccount = $this->input->post('hddIdWorkOrder');

		if ($this->forceaccount_model->saveRate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Rate!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('forceaccount/view_forceaccount/' . $idForceaccount), 'refresh');
	}

	/**
	 * Save hour
	 * para editar las horas que se han guardado
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save_hour()
	{
		$arrParam["idForceAccount"] = $idForceaccount = $this->input->post('hddIdWorkOrder');
		$infoForceaccount = $this->forceaccount_model->get_forceaccount_by_idUser($arrParam);
		$formType = $this->input->post('formType');

		if ($this->forceaccount_model->saveRate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Rate!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('forceaccount/add_forceaccount/' . $idForceaccount), 'refresh');
	}

	/**
	 * Evio de correo a la empresa
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function email($id)
	{
		$arrParam = array('idForceAccount' => $id);
		$infoForceaccount = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		$infoForceaccountEquipment = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list

		foreach ($infoForceaccountEquipment as $lista) {
			$nota = "";
			if ($lista["fk_id_company"] != "" && $lista["fk_id_company"] != 0) {

				$subjet = "Forceaccount Information";
				$user = $lista["contact"];
				$to = $lista["email"];

				//mensaje del correo
				$msj = "<p>The following is the forceaccount information:</p>";
				$msj .= "<strong>Company: </strong> V-CONTRACTING";
				$msj .= "<br><strong>Job Code/Name: </strong>" . $infoForceaccount[0]["job_description"];
				$msj .= "<br><strong>Date: </strong>" . $infoForceaccount[0]["date"];
				$msj .= "<br><strong>Done by: </strong>" . $infoForceaccount[0]["name"];
				$msj .= "<br><strong>Observation: </strong>" . $infoForceaccount[0]["observation"];

				if ($lista['fk_id_type_2'] == 8) {
					$equipment = $lista['miscellaneous'];
				} else {
					$equipment = $lista['unit_number'] . " - " . $lista['v_description'];
				}

				$msj .= "<br><strong>Equipment: </strong>" . $equipment;
				$msj .= "<br><strong>Hours: </strong>" . $lista['hours'];

				if ($lista['foreman_name']) {
					$msj .= "<br><strong>Foreman's name: </strong>" . $lista['foreman_name'];
				}


				if ($lista['signature']) {
					$msj .= "<p><img src='http://v-contracting.ca/app/" . $lista['signature'] . "' width='304' height='236' /></p>";
				}

				$mensaje = "<html>
					<head>
					  <title> $subjet </title>
					</head>
					<body>
						<p>Dear	$user:</p>
						<p>$msj</p>
						<p>Cordially,</p>
						<p><strong>V-CONTRACTING INC</strong></p>
					</body>
					</html>";

				$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
				$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$cabeceras .= 'From: VCI APP <info@v-contracting.ca>' . "\r\n";

				//enviar correo al cliente
				mail($to, $subjet, $mensaje, $cabeceras);

				//enviar correo a VCI
				mail('info@v-contracting.ca', $subjet, $mensaje, $cabeceras);

				if ($lista['foreman_name'] && $lista['foreman_email']) {

					$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
					$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$cabeceras .= 'From: VCI APP <info@v-contracting.ca>' . "\r\n";

					//enviar correo al FOREMAN
					mail($lista['foreman_email'], $subjet, $mensaje, $cabeceras);
				}

				$nota = 'You have send an email to <strong>' . $lista["company_name"] . '</strong> with the information.';
			}
		}

		$this->session->set_flashdata('retornoExito', $nota);
		redirect("/forceaccount/add_forceaccount/" . $id, 'refresh');
	}


	/**
	 * Signature
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function add_signature($idForceAccount)
	{
		if (empty($idForceAccount)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of de file
			$name = "images/signature/forceaccount/forceaccount_" . $idForceAccount . ".png";

			$arrParam = array(
				"table" => "forceaccount",
				"primaryKey" => "id_forceaccount",
				"id" => $idForceAccount,
				"column" => "signature_wo",
				"value" => $name
			);
			//enlace para regresar al formulario
			$data['linkBack'] = "forceaccount/foreman_view/" . $idForceAccount;


			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
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
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Save forceaccount and send email
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save_forceaccount_and_send_email()
	{
		header('Content-Type: application/json');
		$data = array();

		if ($idForceaccount = $this->forceaccount_model->add_forceaccount()) {
			$data["result"] = true;
			$data["mensaje"] = "You have update the Force Account and send an email to the contractor, continue uploading the information.";
			$data["idForceaccount"] = $idForceaccount;
			$this->email_v2($idForceaccount);
			$this->session->set_flashdata('retornoExito', 'You have updated the Force Account and send an email to the contractor, continue uploading the information.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idForceaccount"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Evio de correo a la empresa
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function email_v2($id)
	{
		$arrParam = array('idForceAccount' => $id);
		$infoForceaccount = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		$subjet = "Forceaccount Information";
		$user = $infoForceaccount[0]["contact"];
		$to = $infoForceaccount[0]["email"];

		//mensaje del correo
		$msj = "<p>The following is the forceaccount information:</p>";
		$msj .= "<strong>Forceaccount number: </strong>" . $id;
		$msj .= "<br><strong>Company: </strong> V-CONTRACTING";
		$msj .= "<br><strong>Job Code/Name: </strong>" . $infoForceaccount[0]["job_description"];
		$msj .= "<br><strong>Date: </strong>" . $infoForceaccount[0]["date"];
		$msj .= "<br><strong>Done by: </strong>" . $infoForceaccount[0]["name"];

		if ($infoForceaccount[0]["foreman_name_wo"]) {
			$msj .= "<br><strong>Foreman's name: </strong>" . $infoForceaccount[0]["foreman_name_wo"];
		}

		if ($infoForceaccount[0]["observation"]) {
			$msj .= "<br><strong>Observation: </strong>" . $infoForceaccount[0]["observation"];
		}

		$infoForceaccountEquipment = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list

		if ($infoForceaccountEquipment) {

			foreach ($infoForceaccountEquipment as $lista) {

				if ($lista['fk_id_type_2'] == 8) {
					$equipment = $lista['miscellaneous'];
				} else {
					$equipment = $lista['unit_number'] . " - " . $lista['v_description'];
				}
				$msj .= "<p>";
				$msj .= "<strong>Equipment: </strong>" . $equipment;
				$msj .= "<br><strong>Hours: </strong>" . $lista['hours'];
				$msj .= "<br><strong>Description: </strong>" . $lista['description'];
				$msj .= "<br>*************************************</p>";
			}
		}

		if ($infoForceaccount[0]['signature_wo']) {
			$msj .= "<p><img src='http://v-contracting.ca/app/" . $infoForceaccount[0]['signature_wo'] . "' width='304' height='236' />";
			$msj .= "<br><strong>Foreman signature</strong></p>";
		}

		$mensaje = "<html>
			<head>
			  <title> $subjet </title>
			</head>
			<body>
				<p>Dear	$user:</p>
				<p>$msj</p>
				<p>Cordially,</p>
				<p><strong>V-CONTRACTING INC</strong></p>
			</body>
			</html>";

		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$cabeceras .= 'From: VCI APP <hugo@v-contracting.com>' . "\r\n";

		//enviar correo al cliente
		mail($to, $subjet, $mensaje, $cabeceras);

		//mensaje de texto
		$mensajeSMS = "APP VCI - Force Account Email";
		$mensajeSMS .= "\nAn email was sent to the client with the Force Account information.";
		$mensajeSMS .= "\nForceaccount number: " . $id;

		//enviar correo a VCI
		$arrParam = array(
			"idNotification" => ID_NOTIFICATION_WORKORDER,
			"subjet" => $subjet,
			"msjEmail" => $mensaje,
			"msjPhone" => $mensajeSMS
		);
		send_notification($arrParam);

		if ($lista['foreman_name'] && $lista['foreman_email']) {

			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$cabeceras .= 'From: VCI APP <info@v-contracting.ca>' . "\r\n";

			//enviar correo al FOREMAN
			mail($lista['foreman_email'], $subjet, $mensaje, $cabeceras);
		}

		return TRUE;
	}

	/**
	 * Save forceaccount state
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save_forceaccount_state()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idForceaccount"] = $this->input->post('hddIdWorkOrder');
		$idACS = $this->input->post('hddIdAcs');
		$status = $this->input->post('state');

		$msj = "You have added additional information to the Force Account.";

		$arrParam = array(
			"idForceaccount" => $data["idForceaccount"],
			"observation" => $this->input->post('information'),
			"state" => $status
		);

		if ($this->forceaccount_model->add_forceaccount_state($arrParam)) {
			//actualizo el estado del formulario
			$arrParam = array(
				"idForceaccount" => $data["idForceaccount"],
				"state" => $status,
				"lastMessage" => $this->input->post('information')
			);
			$this->forceaccount_model->update_forceaccount($arrParam);

			if ($status == REVISED && !$idACS) {
				$arrParam = array('idForceAccount' => $data["idForceaccount"]);
				$info['forceaccount'] = $this->forceaccount_model->get_forceaccount_by_idUser($arrParam); //info forceaccount
				$info['forceaccountPersonal'] = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list
				$info['forceaccountMaterials'] = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
				$info['forceaccountReceipt'] = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount invoice list
				$info['forceaccountEquipment'] = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list
				$info['forceaccountOcasional'] = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list
				$this->forceaccount_model->clone_forceaccount($info);

				//Closed hauling cards 
				$this->load->model("general_model");
				$arrParam = array(
					"table" => "hauling",
					"order" => "id_hauling",
					"column" => "fk_id_forceaccount",
					"id" => $data["idForceaccount"]
				);
				$haulingList = $this->general_model->get_basic_search($arrParam); //hauling list

				if ($haulingList) {
					foreach ($haulingList as $key => $hauling) {

						if ($hauling["state"] != 3) {
							$arrParam = array(
								"table" => "hauling",
								"primaryKey" => "id_hauling",
								"id" => $hauling['id_hauling'],
								"column" => "state",
								"value" => 2
							);

							//actualizo el estado del formulario a cerrado(2)
							$this->general_model->updateRecord($arrParam);
						}
					}
				}
			}

			$data["result"] = true;
			$data["mensaje"] = $msj;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Lista de Forceaccounts filtrado por estado
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function wo_by_state($state, $year = "x")
	{
		$from = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //primer dia del año
		//$to = date('Y-m-d',(mktime(0,0,0,13,1,$year)-1));//ultimo dia del año
		$to = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year + 1)); //primer dia del siguiente año para que incluya todo el dia anterior en la consulta

		$data['jobName'] =  $this->input->post('jobName');
		$data['forceAccountNumber'] =  $this->input->post('forceAccountNumber');
		$data['forceAccountNumberFrom'] =  $this->input->post('forceAccountNumberFrom');
		$data['forceAccountNumberTo'] =  $this->input->post('forceAccountNumberTo');
		$data['from'] =  $this->input->post('from');
		$data['to'] =  $this->input->post('to');

		$arrParam = array(
			"from" => $from,
			"to" => $to,
			"state" => $state
		);

		//guardo la informacion en la base de datos para el boton de regresar
		$this->forceaccount_model->saveInfoGoBack($arrParam);

		//informacion Force Account
		$data['forceAccountInfo'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);

		$data["view"] = "asign_rate_list";
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Generate Force Account Report in PDF
	 * @param int $idForceAccount
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function generaForceAccountPDF($idForceAccount)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$arrParam = array("idForceAccount" => $idForceAccount);
		$data['info'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);

		$fecha = date('F j, Y', strtotime($data['info'][0]['date']));

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Force Account');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Force Account Summary', 'Force Account #: ' . $idForceAccount . "\nDate: " . $fecha, array(0, 0, 0), array(0, 0, 0));

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

		$arrParam['view_pdf'] = True;
		$data['forceaccountPersonal'] = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list			
		$data['forceaccountMaterials'] = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
		$data['forceaccountReceipt'] = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount ocasional list
		$data['forceaccountEquipment'] = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list
		$data['forceaccountOcasional'] = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		//$pdf->AddPage('L', 'A4');
		$pdf->AddPage();

		$html = $this->load->view("reporte_force_account", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('force_account_' . $idForceAccount . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Payroll Report in XLS
	 * @param int $jobId
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function generaWorkOrderXLS($jobId, $from = '', $to = '')
	{
		$arrParam = array(
			"jobId" => $jobId,
			"from" => $from,
			"to" => $to
		);
		$info = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);
		$jobCode = $info[0]['job_description'];

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=forceaccount_' . $jobCode . '.xlsx');

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Force Account Report');

		$spreadsheet->getActiveSheet(0)->setCellValue('A1', 'Force Account #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Force Account Date')
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
		$total = 0;
		foreach ($info as $data) :

			$arrParam = array('idForceAccount' => $data['id_forceaccount']);
			$forceaccountPersonal = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list
			$forceaccountMaterials = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
			$forceaccountReceipts = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount receipts list
			$forceaccountEquipment = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list
			$forceaccountOcasional = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($forceaccountPersonal) {
				foreach ($forceaccountPersonal as $infoP) :
					$total += $infoP['value'];
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $infoP['description'])
						->setCellValue('H' . $j, $infoP['name'])
						->setCellValue('I' . $j, $infoP['employee_type'])
						->setCellValue('L' . $j, $infoP['hours'])
						->setCellValue('N' . $j, 'Hours')
						->setCellValue('O' . $j, $infoP['rate'])
						->setCellValue('Q' . $j, $infoP['value']);
					$j++;
				endforeach;
			}

			if ($forceaccountMaterials) {
				foreach ($forceaccountMaterials as $infoM) :
					$total += $infoM['value'];
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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

			if ($forceaccountReceipts) {
				foreach ($forceaccountReceipts as $infoR) :
					$total += $infoR['value'];
					$description = $infoR['description'] . ' - ' . $infoR['place'];
					if ($infoR['markup'] > 0) {
						$description = $description . ' - Plus M.U.';
					}

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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

			if ($forceaccountEquipment) {
				foreach ($forceaccountEquipment as $infoE) :
					$total += $infoE['value'];
					//si es tipo miscellaneous -> 8, entonces la description es diferente
					if ($infoE['fk_id_type_2'] == 8) {
						$equipment = $infoE['miscellaneous'] . " - " . $infoE['other'];
					} else {
						$equipment = $infoE['type_2'] . " - " . $infoE['unit_number'] . " - " . $infoE['v_description'];
					}

					$quantity = $infoE['quantity'] == 0 ? 1 : $infoE['quantity']; //cantidad si es cero es porque es 1

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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

			if ($forceaccountOcasional) {
				foreach ($forceaccountOcasional as $infoO) :
					$total += $infoO['value'];
					$equipment = $infoO['company_name'] . '-' . $infoO['equipment'];
					$hours = $infoO['hours'] == 0 ? 1 : $infoO['hours'];

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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

		$total = '$ ' . number_format($total, 2);
		$spreadsheet->getActiveSheet()->setCellValue('P' . $j, 'Total Income');
		$spreadsheet->getActiveSheet()->setCellValue('Q' . $j, $total);

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

		/**
		 * INFO PERSONAL
		 */
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1);
		$spreadsheet->getActiveSheet()->setTitle('Personal Income');

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Force Account #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Force Account Date')
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

		$totalP = 0;
		$j = 2;
		foreach ($info as $data) :

			$arrParam = array('idForceAccount' => $data['id_forceaccount']);
			$forceaccountPersonal = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($forceaccountPersonal) {
				foreach ($forceaccountPersonal as $infoP) :
					$totalP += $infoP['value'];
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
						->setCellValue('B' . $j, $data['name'])
						->setCellValue('C' . $j, $data['date_issue'])
						->setCellValue('D' . $j, $data['date'])
						->setCellValue('E' . $j, $data['job_description'])
						->setCellValue('F' . $j, $observation)
						->setCellValue('G' . $j, $infoP['description'])
						->setCellValue('H' . $j, $infoP['name'])
						->setCellValue('I' . $j, $infoP['employee_type'])
						->setCellValue('L' . $j, $infoP['hours'])
						->setCellValue('N' . $j, 'Hours')
						->setCellValue('O' . $j, $infoP['rate'])
						->setCellValue('Q' . $j, $infoP['value']);
					$j++;
				endforeach;
			}
		endforeach;

		$totalP = '$ ' . number_format($totalP, 2);
		$spreadsheet->getActiveSheet()->setCellValue('P' . $j, 'Total Income');
		$spreadsheet->getActiveSheet()->setCellValue('Q' . $j, $totalP);

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
		/**
		 * FIN INFO PERSONAL
		 */

		/**
		 * INFO MATERIAL
		 */
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(2);
		$spreadsheet->getActiveSheet()->setTitle('Material Income');

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Force Account #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Force Account Date')
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

		$totalM = 0;
		$j = 2;
		foreach ($info as $data) :

			$arrParam = array('idForceAccount' => $data['id_forceaccount']);
			$forceaccountMaterials = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($forceaccountMaterials) {
				foreach ($forceaccountMaterials as $infoM) :
					$totalM += $infoM['value'];
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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
		endforeach;

		$totalM = '$ ' . number_format($totalM, 2);
		$spreadsheet->getActiveSheet()->setCellValue('P' . $j, 'Total Income');
		$spreadsheet->getActiveSheet()->setCellValue('Q' . $j, $totalM);

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
		/**
		 * FIN INFO MATERIAL
		 */

		/**
		 * INFO RECEIPT
		 */
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(3);
		$spreadsheet->getActiveSheet()->setTitle('Receipt Income');

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Force Account #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Force Account Date')
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

		$totalR = 0;
		$j = 2;
		foreach ($info as $data) :

			$arrParam = array('idForceAccount' => $data['id_forceaccount']);
			$forceaccountReceipts = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount receipts list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($forceaccountReceipts) {
				foreach ($forceaccountReceipts as $infoR) :
					$totalR += $infoR['value'];
					$description = $infoR['description'] . ' - ' . $infoR['place'];
					if ($infoR['markup'] > 0) {
						$description = $description . ' - Plus M.U.';
					}

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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
		endforeach;

		$totalR = '$ ' . number_format($totalR, 2);
		$spreadsheet->getActiveSheet()->setCellValue('P' . $j, 'Total Income');
		$spreadsheet->getActiveSheet()->setCellValue('Q' . $j, $totalR);

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
		/**
		 * FIN INFO RECEIPT 
		 */

		/**
		 * INFO EQUIPMENT
		 */
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(4);
		$spreadsheet->getActiveSheet()->setTitle('Equipment Income');

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Force Account #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Force Account Date')
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

		$totalE = 0;
		$j = 2;
		foreach ($info as $data) :
			$arrParam = array('idForceAccount' => $data['id_forceaccount']);
			$forceaccountEquipment = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($forceaccountEquipment) {
				foreach ($forceaccountEquipment as $infoE) :
					$totalE += $infoE['value'];
					//si es tipo miscellaneous -> 8, entonces la description es diferente
					if ($infoE['fk_id_type_2'] == 8) {
						$equipment = $infoE['miscellaneous'] . " - " . $infoE['other'];
					} else {
						$equipment = $infoE['type_2'] . " - " . $infoE['unit_number'] . " - " . $infoE['v_description'];
					}

					$quantity = $infoE['quantity'] == 0 ? 1 : $infoE['quantity']; //cantidad si es cero es porque es 1

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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
		endforeach;

		$totalE = '$ ' . number_format($totalE, 2);
		$spreadsheet->getActiveSheet()->setCellValue('P' . $j, 'Total Income');
		$spreadsheet->getActiveSheet()->setCellValue('Q' . $j, $totalE);

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
		/**
		 * FIN INFO EQUIPMENT 
		 */

		/**
		 * INFO SUBCONTRACTOR
		 */
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(5);
		$spreadsheet->getActiveSheet()->setTitle('Subcontractor Income');

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Force Account #')
			->setCellValue('B1', 'Supervisor')
			->setCellValue('C1', 'Date of Issue')
			->setCellValue('D1', 'Force Account Date')
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

		$totalS = 0;
		$j = 2;
		foreach ($info as $data) :
			$arrParam = array('idForceAccount' => $data['id_forceaccount']);
			$forceaccountOcasional = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($forceaccountOcasional) {
				foreach ($forceaccountOcasional as $infoO) :
					$totalS += $infoO['value'];
					$equipment = $infoO['company_name'] . '-' . $infoO['equipment'];
					$hours = $infoO['hours'] == 0 ? 1 : $infoO['hours'];

					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_forceaccount'])
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

		$totalS = '$ ' . number_format($totalS, 2);
		$spreadsheet->getActiveSheet()->setCellValue('P' . $j, 'Total Income');
		$spreadsheet->getActiveSheet()->setCellValue('Q' . $j, $totalS);

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
		/**
		 * FIN INFO SUBCONTRACTOR 
		 */

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	/**
	 * Search by JOB CODE
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function search_income()
	{
		//job list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobList'] = $this->general_model->get_basic_search($arrParam); //job list

		$arrParam = array("state" => 0);
		$data['noOnfield'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 1);
		$data['noProgress'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 2);
		$data['noRevised'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 3);
		$data['noSend'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 4);
		$data['noClosed'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 5);
		$data['noAccounting'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($this->input->post('jobName') && $this->input->post('from') && $this->input->post('to')) {
			$data['idJob'] =  $this->input->post('jobName');
			$data['from'] =  $this->input->post('from');
			$data['to'] =  $this->input->post('to');

			$to = isset($data['to']) ? formatear_fecha($data['to']) : "";
			$from = isset($data['from']) ? formatear_fecha($data['from']) : "";

			$data['fromFormat'] =  $from;
			$data['toFormat'] =  $to;

			//informacion Force Account
			$arrParam = array(
				"idJob" => $data['idJob'],
				"from" => $from,
				"to" => $to
			);
			$data['noWO'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts

			$data['hoursPersonal'] = $this->forceaccount_model->countHoursPersonal($arrParam); //cuenta horas de personal

			$arrParam['table'] = "forceaccount_personal";
			$data['incomePersonal'] = $this->forceaccount_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "forceaccount_materials";
			$data['incomeMaterial'] = $this->forceaccount_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "forceaccount_equipment";
			$data['incomeEquipment'] = $this->forceaccount_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "forceaccount_ocasional";
			$data['incomeSubcontractor'] = $this->forceaccount_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "forceaccount_receipt ";
			$data['incomeReceipt'] = $this->forceaccount_model->countIncome($arrParam); //cuenta horas de personal

			$data['total'] = $data['incomePersonal'] + $data['incomeMaterial'] + $data['incomeEquipment'] + $data['incomeSubcontractor'] + $data['incomeReceipt'];

			//job list
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "job_description",
				"column" => "id_job",
				"id" => $data['idJob']
			);
			$data['jobListSearch'] = $this->general_model->get_basic_search($arrParam); //job list

		}
		$data["view"] = "form_search_income";

		$this->load->view("layout", $data);
	}

	/**
	 * Foreman forceaccount view to sign
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function foreman_view($id)
	{
		$arrParam = array('idForceAccount' => $id);
		$data['forceaccountPersonal'] = $this->forceaccount_model->get_forceaccount_personal($arrParam); //forceaccount personal list
		$data['forceaccountMaterials'] = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
		$data['forceaccountEquipment'] = $this->forceaccount_model->get_forceaccount_equipment($arrParam); //forceaccount equipment list
		$data['forceaccountOcasional'] = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list

		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		$data["view"] = 'foreman_view';
		$this->load->view("layout", $data);
	}

	/**
	 * Foreman info
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function foremanInfo()
	{
		header('Content-Type: application/json');
		$data = array();

		$idJob = $this->input->post('idJob');

		$this->load->model("general_model");
		//info JOB to get Company
		$arrParam['idJob'] = $idJob;
		$jobInfo = $this->general_model->get_job($arrParam);

		//reviso si hay formean para esa JOB CODE
		$arrParam = array(
			"table" => "param_company_foreman",
			"order" => "id_company_foreman ",
			"column" => "fk_id_job",
			"id" => $idJob
		);
		$infoForeman = $this->general_model->get_basic_search($arrParam);

		$data["result"] = true;
		$data["company_id"] = $jobInfo[0]["fk_id_company"];
		$data["company_name"] = $jobInfo[0]["company_name"];
		$data["foreman_name"] = "";
		$data["foreman_movil"] = "";
		$data["foreman_email"] = "";
		if ($infoForeman) {
			$data["foreman_name"] = $infoForeman[0]["foreman_name"];
			$data["foreman_movil"] = $infoForeman[0]["foreman_movil_number"];
			$data["foreman_email"] = $infoForeman[0]["foreman_email"];
		} elseif ($jobInfo[0]["fk_id_company"] > 0 && $jobInfo[0]["fk_id_company"] != "") {
			//reviso si hay formean para esa empresa
			$arrParam = array(
				"table" => "param_company_foreman",
				"order" => "id_company_foreman ",
				"column" => "fk_id_param_company",
				"id" => $jobInfo[0]["fk_id_company"]
			);
			$infoForeman = $this->general_model->get_basic_search($arrParam);
			if ($infoForeman) {
				$data["foreman_name"] = $infoForeman[0]["foreman_name"];
				$data["foreman_movil"] = $infoForeman[0]["foreman_movil_number"];
				$data["foreman_email"] = $infoForeman[0]["foreman_email"];
			}
		}

		echo json_encode($data);
	}

	/**
	 * Envio de mensaje
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function sendSMSForeman($idForceAccount)
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
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);

		$client = new Twilio\Rest\Client($dato1, $dato2);

		$arrParam['idForceAccount'] =  $idForceAccount;
		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		$mensaje = "";

		$mensaje .= date('F j, Y', strtotime($data['information'][0]['date']));
		$mensaje .= "\n" . $data['information'][0]['job_description'];
		$mensaje .= "\n" . $data['information'][0]['observation'];
		$mensaje .= "\n";
		$mensaje .= "Click the following link to review W.O. " . $idForceAccount;
		$mensaje .= "\n";
		$mensaje .= "\n";
		$mensaje .= base_url("forceaccount/foreman_view/" . $idForceAccount);

		$to = '+1' . $data['information'][0]['foreman_movil_number_wo'];

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

		$data['linkBack'] = "forceaccount/add_forceaccount/" . $idForceAccount;
		$data['titulo'] = "<i class='fa fa-list'></i>Force Account";

		$data['clase'] = "alert-info";
		$data['msj'] = "We have send the SMS to the foreman to sign the Force Account No." . $idForceAccount;

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}

	/**
	 * Load prices WO
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function load_prices_wo()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWO"] = $idWO = $this->input->post('identificador');

		$arrParam['idForceAccount'] =  $idWO;
		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount
		$idJob = $data['information'][0]['fk_id_job'];

		$forceaccountPersonalRate = $this->forceaccount_model->get_forceaccount_personal_prices($idWO, $idJob); //forceaccount personal list
		$forceaccountEquipmentRate = $this->forceaccount_model->get_forceaccount_equipment_prices($idWO, $idJob); //forceaccount equipment list
		$forceaccountMaterialRate = $this->forceaccount_model->get_forceaccount_material_prices($idWO); //forceaccount material list

		if ($forceaccountPersonalRate) {
			if ($this->forceaccount_model->update_wo_personal_rate($forceaccountPersonalRate)) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = true;
		}

		if ($forceaccountEquipmentRate) {
			if ($this->forceaccount_model->update_wo_equipment_rate($forceaccountEquipmentRate)) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = true;
		}

		if ($forceaccountMaterialRate) {
			if ($this->forceaccount_model->update_wo_material_rate($forceaccountMaterialRate)) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = true;
		}

		echo json_encode($data);
	}

	/**
	 * Cargo modal- formulario de captura Invoice
	 * @since 16/04/2025
	 */
	public function cargarModalReceipts()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idForceaccount = $this->input->post("idForceaccount");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idForceaccount);
		$data["idForceaccount"] = $porciones[1];

		$this->load->view("modal_receipt", $data);
	}

	/**
	 * Actualizar info de invoice
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function update_receipt()
	{
		$idForceaccount = $this->input->post('hddidForceaccount');
		$view = $this->input->post('view');

		if ($this->forceaccount_model->saveReceipt()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the information!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('forceaccount/' . $view . '/' . $idForceaccount), 'refresh');
	}

	/**
	 * Load markup WO
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function load_markup_wo()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWO"] = $idWO = $this->input->post('identificador');

		$arrParam = array('idForceAccount' => $idWO);
		$infoWO = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		$forceaccountMaterials = $this->forceaccount_model->get_forceaccount_materials($arrParam); //forceaccount material list
		$forceaccountReceipt = $this->forceaccount_model->get_forceaccount_receipt($arrParam); //forceaccount invoice list
		$forceaccountOcasional = $this->forceaccount_model->get_forceaccount_ocasional($arrParam); //forceaccount ocasional list

		if ($forceaccountReceipt) {
			if ($this->forceaccount_model->update_wo_invoice_markup($forceaccountReceipt, $infoWO[0]['markup'])) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = true;
		}

		if ($forceaccountMaterials) {
			if ($this->forceaccount_model->update_wo_material_markup($forceaccountMaterials, $infoWO[0]['markup'])) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = true;
		}

		if ($forceaccountOcasional) {
			if ($this->forceaccount_model->update_wo_ocasional_markup($forceaccountOcasional, $infoWO[0]['markup'])) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = true;
		}

		echo json_encode($data);
	}

	/**
	 * Cambio de estado de las WO
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function update_wo_state()
	{
		header('Content-Type: application/json');
		$data = array();

		$wo = $this->input->post('wo');
		if ($wo) {
			if ($this->forceaccount_model->updateWOState()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the state!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		} else {
			$data["result"] = "error";
			$data["mensaje"] = " You have to select a W.O.";
			$this->session->set_flashdata('retornoError', 'You have to select a W.O.');
		}
		echo json_encode($data);
	}

	/**
	 * Cargo modal- formulario de captura Expense
	 * @since 16/04/2025
	 */
	public function cargarModalExpense()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idForceaccount"] = $this->input->post("idForceaccount");

		$this->load->model("general_model");
		$arrParam = array('idForceAccount' => $data["idForceaccount"]);
		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount

		//JOB detail list
		$data["idJob"] = $data['information'][0]["fk_id_job"];
		$arrParam = array("idJob" => $data["idJob"]);
		$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);
		$data['sumPercentage'] = $this->general_model->sumPercentageByJob($arrParam);

		$this->load->view("modal_expense", $data);
	}

	/**
	 * Update WO Expenses Values
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function update_wo_expenses_values($idForceaccount, $idJob)
	{
		$arrParam = array('idForceAccount' => $idForceaccount);
		$forceaccountExpenses = $this->forceaccount_model->get_forceaccount_expense($arrParam); //forceaccount expense list
		$sumPercentageExpense = $this->forceaccount_model->sumPercentageExpense($arrParam); //forceaccount expense list

		//search for total WO INCOME
		$arrParam['idJob'] = $idJob;
		$arrParam['table'] = "forceaccount_personal";
		$incomePersonal = $this->forceaccount_model->countIncome($arrParam); //INCOME PERSOMAL

		$arrParam['table'] = "forceaccount_materials";
		$incomeMaterial = $this->forceaccount_model->countIncome($arrParam); //INCOME MATERIAL

		$arrParam['table'] = "forceaccount_equipment";
		$incomeEquipment = $this->forceaccount_model->countIncome($arrParam); //INCOME EQUIPMENT

		$arrParam['table'] = "forceaccount_ocasional";
		$incomeSubcontractor = $this->forceaccount_model->countIncome($arrParam); //INCOME OCASIONAL

		$arrParam['table'] = "forceaccount_receipt ";
		$incomeReceipt = $this->forceaccount_model->countIncome($arrParam); //INCOME RECEIPT

		$totalWOIncome = $incomePersonal + $incomeMaterial + $incomeEquipment + $incomeSubcontractor + $incomeReceipt;

		if ($totalWOIncome > 0 && $sumPercentageExpense) {
			//$this->forceaccount_model->updateExpensesValues($forceaccountExpenses, $totalWOIncome, $sumPercentageExpense);
			//update WO expenses flag
			$arrParam = array(
				"table" => "param_jobs",
				"primaryKey" => "id_job",
				"id" => $idJob,
				"column" => "flag_expenses",
				"value" => 1
			);
			$this->load->model("general_model");
			$this->general_model->updateRecord($arrParam);
		}
		return true;
	}

	/**
	 * Recalculate Expenses
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function recalculate_expenses()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWO"] = $idForceaccount = $this->input->post('identificador');

		$arrParam['idForceAccount'] =  $idForceaccount;
		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount
		$idJob = $data['information'][0]['fk_id_job'];

		if ($this->update_wo_expenses_values($idForceaccount, $idJob)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "The information was saved successfully!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Attachement List
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function attachmentList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$this->load->model("general_model");
		$arrParam = array(
			"idEquipment" => $this->input->post('equipmentId')
		);
		$lista = $this->general_model->get_attachments_by_equipment($arrParam);

		if ($lista) {
			echo "<option value=''>Select...</option>";
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["id_attachment"] . "'>" . $fila["attachment_number"] . " - " . $fila["attachment_description"]  . "</option>";
			}
		}
	}

	/**
	 * LOG Forceaccounts
	 * @since 16/04/2025
	 * @author FOROZCO
	 */
	public function log($goBack = 'x')
	{
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

		$arrParam = array("state" => 0);
		$data['noOnfield'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 1);
		$data['noProgress'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 2);
		$data['noRevised'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 3);
		$data['noSend'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts
		$arrParam = array("state" => 4);
		$data['noClosed'] = $this->forceaccount_model->countForceaccounts($arrParam); //cuenta registros de Forceaccounts

		$data["view"] = "job_search";

		//viene desde el boton go back
		if ($goBack == 'y') {
			$forceAccountGoBackInfo = $this->forceaccount_model->get_forceaccount_go_back();

			if (!$forceAccountGoBackInfo) {
				redirect(base_url('forceaccount'), 'refresh');
			} else {
				//le sumo un dia al dia final para que ingrese ese dia en la consulta
				$to = date('Y-m-d', strtotime('+1 day ', strtotime(formatear_fecha($forceAccountGoBackInfo['post_to']))));
				//$from = formatear_fecha($forceAccountGoBackInfo['post_from']);

				$arrParam = array(
					"jobId" => $forceAccountGoBackInfo['post_id_job'],
					"idForceAccount" => $forceAccountGoBackInfo['post_id_work_order'],
					"idForceAccountFrom" => $forceAccountGoBackInfo['post_id_wo_from'],
					"idForceAccountTo" => $forceAccountGoBackInfo['post_id_wo_to'],
					"from" => $forceAccountGoBackInfo['post_from'], //$from,
					"to" => $to,
					"state" => $forceAccountGoBackInfo['post_state']
				);

				$data['forceAccountInfo'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);

				$data["view"] = "asign_rate_list";
				$this->load->view("layout_calendar", $data);
			}
		}
		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		elseif ($this->input->post('jobName') || $this->input->post('user') || $this->input->post('from') || $this->input->post('forceAccountNumber')) {



			$data['jobName'] =  $this->input->post('jobName');
			$data['forceAccountNumber'] =  $this->input->post('forceAccountNumber');
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
				"idForceAccount" => $this->input->post('forceAccountNumber'),
				"userId" => $this->input->post('user'),
				"from" => $from,
				"to" => $to
			);

			//guardo la informacion en la base de datos para el boton de regresar
			//$this->forceaccount_model->saveInfoGoBack($arrParam);

			//informacion Force Account
			$data['forceAccountInfo'] = $this->forceaccount_model->get_forceaccount_log($arrParam);

			$data["view"] = "log_list";
			$this->load->view("layout_calendar", $data);
		} else {
			$this->load->view("layout", $data);
		}
	}

	/**
	 * View forceaccount_expenses
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function forceaccount_expenses($idForceAccount)
	{
		$this->load->model("general_model");

		$arrParam = array("idForceAccount" => $idForceAccount, "flag_expenses" => true);
		$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam);
		$data['forceaccountPersonal'] = $this->forceaccount_model->get_forceaccount_personal($arrParam);
		$data['forceaccountMaterials'] = $this->forceaccount_model->get_forceaccount_materials($arrParam);
		$data['forceaccountReceipt'] = $this->forceaccount_model->get_forceaccount_receipt($arrParam);
		$data['forceaccountEquipment'] = $this->forceaccount_model->get_forceaccount_equipment($arrParam);
		$data['forceaccountOcasional'] = $this->forceaccount_model->get_forceaccount_ocasional($arrParam);
		$data['forceaccountExpenses'] = $this->general_model->get_forceaccount_expense($arrParam);

		$arrParam = array("idJob" => $data['information'][0]['fk_id_job'], "status" => 1);
		$data['jobDetails'] = $this->general_model->get_job_detail($arrParam);

		$data["view"] = 'expenses';
		$this->load->view("layout", $data);
	}

	/**
	 * Save WO Expenses
	 * @since 16/04/2025
	 * @author BMOTTAG
	 */
	public function save_fa_expenses()
	{
		$idForceAccount = $this->input->post('hddidForceaccount');
		$idJob = $this->input->post('hddidJob');

		if ($this->forceaccount_model->saveFAExpenses()) {
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "param_jobs",
				"primaryKey" => "id_job",
				"id" => $idJob,
				"column" => "flag_expenses",
				"value" => 1
			);
			$this->general_model->updateRecord($arrParam);

			$arrParam = array(
				"table" => "forceaccount",
				"primaryKey" => "id_forceaccount",
				"id" => $idForceAccount,
				"order" => 'id_forceaccount',
				"column" => "expenses_flag",
				"value" => 1
			);
			$this->general_model->updateRecord($arrParam);
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the Force Account Expenses!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('forceaccount/forceaccount_expenses/' . $idForceAccount), 'refresh');
	}

	/**
	 * FUNCIÓN PARA SUBIR LA IMAGEN 
	 */
	function do_upload()
	{
		$config['upload_path'] = './images/erp/';
		$config['overwrite'] = true;
		$config['allowed_types'] = 'gif|jpg|png|pdf|jpeg';
		$config['max_size'] = '3000';
		$config['max_width'] = '3200';
		$config['max_height'] = '2400';
		$idJob = $this->input->post("hddIdJobMap");
		$config['file_name'] = $idJob;

		$this->load->library('upload', $config);
		//SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
		if (!$this->upload->do_upload()) {
			$error = $this->upload->display_errors();
			$this->erp_map($idJob, $error);
		} else {
			$file_info = $this->upload->data(); //subimos la imagen

			//USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
			//ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
			$this->_create_thumbnail($file_info['file_name']);
			$data = array('upload_data' => $this->upload->data());
			$imagen = $file_info['file_name'];
			$path = "images/erp/" . $imagen;

			//actualizamos el campo photo
			$arrParam = array(
				"table" => "erp",
				"primaryKey" => "fk_id_job",
				"id" => $idJob,
				"column" => "evacuation_map",
				"value" => $path
			);

			$this->load->model("general_model");

			if ($this->general_model->updateRecord($arrParam)) {
				$data['clase'] = "alert-success";
				$data['msj'] = "Se subio la imagen con éxito.";
			} else {
				$data['clase'] = "alert-danger";
				$data['msj'] = "Error, contactarse con el administrador.";
			}

			redirect('jobs/erp_map/' . $idJob);
		}
	}

	/**
	 * Delete Expenses
	 * @param int $idValue: id que se va a borrar
	 * @param int $idForceaccount: llave  primaria de forceaccount
	 */
	public function deleteRecordExpenses($subModule, $idForceaccountExpenses, $idSubmodule, $idForceAccount)
	{
		if (empty($subModule) || empty($idForceaccountExpenses) || empty($idForceAccount)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "forceaccount_expense",
			"primaryKey" => "id_forceaccount_expense",
			"id" => $idForceaccountExpenses
		);
		$this->load->model("general_model");

		if ($this->general_model->deleteRecord($arrParam)) {
			//para expenses recalculo los valores gastados para cada item
			$arrParam = array('idForceAccount' => $idForceAccount);
			$data['information'] = $this->forceaccount_model->get_forceaccount_by_idJob($arrParam); //info forceaccount
			$idJob = $data['information'][0]["fk_id_job"];
			$this->update_wo_expenses_values($idForceAccount, $idJob);

			//Update forceaccount submodule
			$table = "forceaccount_" . $subModule;
			$arrParam = array(
				"table" => "forceaccount_" . $subModule,
				"primaryKey" => "id_forceaccount_" . $subModule,
				"id" => $idSubmodule,
				"column" => "flag_expenses",
				"value" => 0
			);
			$this->general_model->updateRecord($arrParam);

			$this->session->set_flashdata('retornoExito', 'You have deleted one record from <strong>' . $table . '</strong> table.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('forceaccount/forceaccount_expenses/' . $idForceAccount), 'refresh');
	}

}
