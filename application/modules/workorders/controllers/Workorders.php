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

class Workorders extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("workorders_model");
		$this->load->helper('form');
	}

	/**
	 * Form Workorders
	 * @since 12/1/2017
	 * @author BMOTTAG
	 */
	public function index()
	{
		$this->load->model("general_model");

		$arrParam = array();
		$userRol = $this->session->userdata("rol");
		$idUser = $this->session->userdata("id");
		//If it is a BASIC ROLE OR SAFETY&MAINTENACE ROLE, just show the records of the user session
		if ($userRol == ID_ROL_BASIC) {
			$arrParam["idEmployee"] = $this->session->userdata("id");
		}
		$data['workOrderInfo'] = $this->workorders_model->get_workordes_by_idUser($arrParam);

		//delete records for button go back from the search form
		$arrParam = array(
			"table" => "workorder_go_back",
			"primaryKey" => "fk_id_user",
			"id" => $idUser
		);
		$this->general_model->deleteRecord($arrParam);

		$data["view"] = 'workorder';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Add workorder
	 * @since 12/1/2017
	 * @author BMOTTAG
	 */
	public function add_workorder($id = 'x')
	{
		$data['information'] = FALSE;
		$data['workorderPersonal'] = FALSE;
		$data['workorderMaterials'] = FALSE;
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
			$arrParam = array('idWorkOrder' => $id);
			$data['workorderPersonal'] = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list
			$data['workorderMaterials'] = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
			$data['workorderReceipt'] = $this->workorders_model->get_workorder_receipt($arrParam); //workorder invoice list
			$data['workorderEquipment'] = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list
			$data['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list
			$data['workorderState'] = $this->workorders_model->get_workorder_state($id); //workorder additional information

			$data['information'] = $this->workorders_model->get_workordes_by_idUser($arrParam); //info workorder

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}

			$arrParam = array(
				"table" => "param_employee_type",
				"order" => "employee_type",
				"id" => "x"
			);
			$data['employeeTypeList'] = $this->general_model->get_basic_search($arrParam); //employee type list

			//DESHABILITAR WORK ORDER
			$userRol = $this->session->rol;
			$workorderState = $data['information'][0]['state'];
			//si es diferente al rol de SUPER ADMIN
			if ($userRol != ID_ROL_SUPER_ADMIN) {
				//si esta "Closed" deshabilito los botones
				if ($workorderState == 4) {
					$data['deshabilitar'] = 'disabled';
					//If it is DIFERRENT THAN ON FIELD and ROLE is SUPERVISOR OR BASIC OR Safety&Maintenance
				} elseif ($workorderState != 0 && ($userRol == ID_ROL_SAFETY || $userRol == ID_ROL_SUPERVISOR || $userRol == ID_ROL_BASIC)) {
					$data['deshabilitar'] = 'disabled';
					//IF it is “Accounting“ solo este estado es para “Accounting“
				} elseif ($workorderState == 5 && $userRol != ID_ROL_ACCOUNTING_ASSISTANT) {
					$data['deshabilitar'] = 'disabled';
					//If it is "Revised" or "Send to Client" and USER is Work Order 
				} elseif (($workorderState == 2 || $workorderState == 3) && $userRol == ID_ROL_WORKORDER) {
					$data['deshabilitar'] = 'disabled';
					//If it is "Send to Client" and USER is Engineer
				} elseif (($workorderState == 3) && $userRol == ID_ROL_ENGINEER) {
					$data['deshabilitar'] = 'disabled';
					//If it is "In Progress" and USER is Manager or Accounting
				} elseif ($workorderState == 1 && ($userRol == ID_ROL_MANAGER || $userRol == ID_ROL_ACCOUNTING)) {
					$data['deshabilitar'] = 'disabled';
				}
			}

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_workorder';
		$this->load->view("layout", $data);
	}

	/**
	 * Save workorder
	 * @since 13/1/2017
	 * @author BMOTTAG
	 */
	public function save_workorder()
	{
		header('Content-Type: application/json');
		$data = array();

		$idWorkorderInicial = $this->input->post('hddIdentificador');

		$msj = "You have added a new Work Order, continue uploading the information.";
		if ($idWorkorderInicial != '') {
			$msj = "You have updated the Work Order, continue uploading the information.";
		}

		if ($idWorkorder = $this->workorders_model->add_workorder()) {
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
				$this->workorders_model->info_foreman($idForeman);
				//FIN
			}

			//guardo el primer estado de la workorder
			if (!$idWorkorderInicial) {
				$arrParam = array(
					"idWorkorder" => $idWorkorder,
					"observation" => "New work order.",
					"state" => 0
				);
				$this->workorders_model->add_workorder_state($arrParam);
			}

			$data["result"] = true;
			$data["mensaje"] = $msj;
			$data["idWorkorder"] = $idWorkorder;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idWorkorder"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update workorder
	 * @since 25/1/2017
	 * @author BMOTTAG
	 */
	public function update_workorder()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWorkorder"] = $this->input->post('hddIdentificador');

		$arrParam = array(
			"table" => "workorder",
			"primaryKey" => "id_workorder",
			"id" => $data["idWorkorder"],
			"column" => "state",
			"value" => 2
		);
		$this->load->model("general_model");

		//actualizo el estado del formulario a cerrado(2)
		if ($this->general_model->updateWORecords($arrParam)) {
			$data["result"] = true;
			$data["mensaje"] = "You have closed the Work Order.";
			$this->session->set_flashdata('retornoExito', 'You have closed the Work Order');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Cargo modal- formulario de captura personal
	 * @since 13/1/2017
	 */
	public function cargarModalPersonal()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idWorkorder"] = $this->input->post("idWorkorder");

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
	 * @since 13/1/2017
	 * @author BMOTTAG
	 */
	public function save($modalToUse)
	{
		header('Content-Type: application/json');
		$data = array();

		$arrParam["idWorkOrder"] = $data["idRecord"] = $idWorkOrder = $this->input->post('hddidWorkorder');
		$infoWorkorder = $this->workorders_model->get_workordes_by_idUser($arrParam);

		if ($this->workorders_model->$modalToUse()) {
			if ($modalToUse == 'saveExpense') {
				$idJob = $this->input->post('hddidJob');
				$this->update_wo_expenses_values($idWorkOrder, $idJob);
				//add a flag in the WO table, for the expenses
				$arrParam = array(
					"table" => "workorder",
					"primaryKey" => "id_workorder",
					"id" => $idWorkOrder,
					"order" => 'id_workorder',
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
		$data["controlador"] = "add_workorder";
		if ($userRol == 99) {
			$data["controlador"] = "view_workorder";
		}

		echo json_encode($data);
	}

	/**
	 * Delete workorder record
	 * @param varchar $tabla: nombre de la tabla de la cual se va a borrar
	 * @param int $idValue: id que se va a borrar
	 * @param int $idWorkorder: llave  primaria de workorder
	 */
	public function deleteRecord($tabla, $idValue, $idWorkOrder, $vista)
	{
		if (empty($tabla) || empty($idValue) || empty($idWorkOrder)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "workorder_" . $tabla,
			"primaryKey" => "id_workorder_"  . $tabla,
			"id" => $idValue
		);
		$this->load->model("general_model");
		$this->load->library('logger');

		$table = "workorder_" . $tabla;

		$arrOld = array(
			"table" => $table,
			"order" => "id_workorder_"  . $tabla,
			"column" => "id_workorder_"  . $tabla,
			"id" => $idValue
		);
		$log['old'] = $this->general_model->get_basic_search($arrOld);
		$log['new'] = null;

		$this->logger
			->user($this->session->userdata("id")) //;//Set UserID, who created this  Action
			->type($table) //Entry type like, Post, Page, Entry
			->id($idWorkOrder) //Entry ID
			->token('delete') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($this->general_model->deleteRecord($arrParam)) 
		{
			//si elimino PERSONAL, ENTONCES VERIFICO SI TIENE EN LA TABLA TASK ALGUN REGISTRO RELACIONADO CON LA WO
	        if ($tabla == 'personal' && !empty($log['old']) && is_array($log['old']) && isset($log['old'][0]['fk_id_user'])) {
				$arrTask = array(
					"idWorkOrder" => $idWorkOrder,
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
					"idWorkOrder" => $idWorkOrder,
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
				"fk_id_workorder" => $idWorkOrder,
				"submodule" => $tabla,
				"fk_id_submodule" => $idValue,
			);
			$this->workorders_model->deleteExpenses($arrExpenses);

			//para expenses recalculo los valores gastados para cada item
			$arrParam = array('idWorkOrder' => $idWorkOrder);
			$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder
			$idJob = $data['information'][0]["fk_id_job"];
			$this->update_wo_expenses_values($idWorkOrder, $idJob);
			/**
			 * If table is expense then check if there are more records 
			 * if not then delete put flag of expenses in 0 on WO table
			 */
			$workorderExpense = $this->workorders_model->get_workorder_expense($arrParam); //workorder expense list
			if (!$workorderExpense) {
				$arrParam = array(
					"table" => "workorder",
					"primaryKey" => "id_workorder",
					"id" => $idWorkOrder,
					"column" => "expenses_flag",
					"value" => 0
				);
				$this->general_model->updateRecord($arrParam);
			}
			$this->session->set_flashdata('retornoExito', 'You have deleted one record from <strong>' . $tabla . '</strong> table.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('workorders/' . $vista . '/' . $idWorkOrder), 'refresh');
	}

	/**
	 * Cargo modal- formulario de captura Material
	 * @since 13/1/2017
	 */
	public function cargarModalMaterials()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idWorkorder = $this->input->post("idWorkorder");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idWorkorder);
		$data["idWorkorder"] = $porciones[1];

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
	 * @since 25/1/2017
	 */
	public function cargarModalEquipment()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idWorkorder = $this->input->post("idWorkorder");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idWorkorder);
		$data["idWorkorder"] = $porciones[1];

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
	 * @since 25/1/2017
	 * @author BMOTTAG
	 */
	public function truckList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$company = 1; //la empresa es Lev-West que el id es 1
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
			$lista = $this->workorders_model->get_trucks_by_id1();

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
	 * @since 4/2/2017
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
	 * @since 20/2/2017
	 */
	public function cargarModalOcasional()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idWorkorder = $this->input->post("idWorkorder");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idWorkorder);

		if (count($porciones) > 1) {
			$data["idWorkorder"] = $porciones[1];

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
	 * Cargo modal- formulario de captura HOLD BACK
	 * @since 12/11/2018
	 */
	public function cargarModalHoldBack()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idWorkorder = $this->input->post("idWorkorder");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idWorkorder);
		$data["idWorkorder"] = $porciones[1];

		$this->load->view("modal_hold_back", $data);
	}

	/**
	 * Search by JOB CODE
	 * @since 21/02/2017
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
		$data['noOnfield'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 1);
		$data['noProgress'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 2);
		$data['noRevised'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 3);
		$data['noSend'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 4);
		$data['noClosed'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 5);
		$data['noAccounting'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders

		$data["view"] = "asign_rate_form_search";

		//viene desde el boton go back
		if ($goBack == 'y') {
			$workOrderGoBackInfo = $this->workorders_model->get_workorder_go_back();

			if (!$workOrderGoBackInfo) {
				redirect(base_url('workorders'), 'refresh');
			} else {
				//le sumo un dia al dia final para que ingrese ese dia en la consulta
				$to = date('Y-m-d', strtotime('+1 day ', strtotime(formatear_fecha($workOrderGoBackInfo['post_to']))));
				//$from = formatear_fecha($workOrderGoBackInfo['post_from']);

				$arrParam = array(
					"jobId" => $workOrderGoBackInfo['post_id_job'],
					"idWorkOrder" => $workOrderGoBackInfo['post_id_work_order'],
					"idWorkOrderFrom" => $workOrderGoBackInfo['post_id_wo_from'],
					"idWorkOrderTo" => $workOrderGoBackInfo['post_id_wo_to'],
					"from" => $workOrderGoBackInfo['post_from'], //$from,
					"to" => $to,
					"state" => $workOrderGoBackInfo['post_state']
				);

				$data['workOrderInfo'] = $this->workorders_model->get_workorder_by_idJob($arrParam);

				$data["view"] = "asign_rate_list";
				$this->load->view("layout_calendar", $data);
			}
		}
		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		elseif ($this->input->post('jobName') || $this->input->post('workOrderNumber') || $this->input->post('workOrderNumberFrom') || $this->input->post('from')) {
			$data['jobName'] =  $this->input->post('jobName');
			$data['workOrderNumber'] =  $this->input->post('workOrderNumber');
			$data['workOrderNumberFrom'] =  $this->input->post('workOrderNumberFrom');
			$data['workOrderNumberTo'] =  $this->input->post('workOrderNumberTo');
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
				"idWorkOrder" => $this->input->post('workOrderNumber'),
				"idWorkOrderFrom" => $this->input->post('workOrderNumberFrom'),
				"idWorkOrderTo" => $this->input->post('workOrderNumberTo'),
				"from" => $from,
				"to" => $to
			);

			//guardo la informacion en la base de datos para el boton de regresar
			$this->workorders_model->saveInfoGoBack($arrParam);

			//informacion Work Order
			$data['workOrderInfo'] = $this->workorders_model->get_workorder_by_idJob($arrParam);

			$data["view"] = "asign_rate_list";
			$this->load->view("layout_calendar", $data);
		} else {
			$this->load->view("layout", $data);
		}
	}

	/**
	 * View info WOrk order to asign rate
	 * @since 21/2/2017
	 * @author BMOTTAG
	 */
	public function view_workorder($id)
	{
		$this->load->model("general_model");

		$arrParam = array('idWorkOrder' => $id);
		$data['workorderExpense'] = $this->workorders_model->get_workorder_expense($arrParam); //workorder expense list
		$data['workorderPersonal'] = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list
		$data['workorderMaterials'] = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
		$data['workorderReceipt'] = $this->workorders_model->get_workorder_receipt($arrParam); //workorder invoice list
		$data['workorderEquipment'] = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list
		$data['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list
		$data['workorderHoldBack'] = false; //$this->workorders_model->get_workorder_hold_back($id); //workorder ocasional list

		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		//search for total WO INCOME
		$arrParam2 = array('idWorkOrder' => $id);
		$arrParam2['idJob'] = $data['information'][0]['fk_id_job'];
		$arrParam2['table'] = "workorder_personal";
		$data['incomePersonal'] = $this->workorders_model->countIncome($arrParam2); //INCOME PERSOMAL

		$arrParam2['table'] = "workorder_materials";
		$data['incomeMaterial'] = $this->workorders_model->countIncome($arrParam2); //INCOME MATERIAL

		$arrParam2['table'] = "workorder_equipment";
		$data['incomeEquipment'] = $this->workorders_model->countIncome($arrParam2); //INCOME EQUIPMENT

		$arrParam2['table'] = "workorder_ocasional";
		$data['incomeSubcontractor'] = $this->workorders_model->countIncome($arrParam2); //INCOME OCASIONAL

		$arrParam2['table'] = "workorder_receipt";
		$data['incomeReceipt'] = $this->workorders_model->countIncome($arrParam2); //INCOME RECEIPT

		$data['totalWOIncome'] = $data['incomePersonal'] + $data['incomeMaterial'] + $data['incomeEquipment'] + $data['incomeSubcontractor'] + $data['incomeReceipt'];

		//DESHABILITAR WORK ORDER
		$userRol = $this->session->rol;
		$workorderState = $data['information'][0]['state'];
		$data['deshabilitar'] = '';

		//si esta cerrada deshabilito los botones
		if ($workorderState == 4) {
			$data['deshabilitar'] = 'disabled';
			//If it is DIFERRENT THAN ON FILD and ROLE is SUPERVISOR OR BASIC OR Safety&Maintenance
		} elseif ($workorderState != 0 && ($userRol == 4 || $userRol == 6 || $userRol == 7)) {
			$data['deshabilitar'] = 'disabled';
		} elseif (($workorderState == 2 || $workorderState == 3) && $userRol == 5) { //WORK ORDER  USER
			$data['deshabilitar'] = 'disabled';
		} elseif ($workorderState == 1 && ($userRol == 2 || $userRol == 3)) { //MANAGEMENT AND ACCOUNTING USER
			$data['deshabilitar'] = 'disabled';
		}

		$data["view"] = 'asign_rate_form_v2';
		$this->load->view("layout", $data);
	}

	/**
	 * Save rate
	 * @since 27/2/2017
	 * @author BMOTTAG
	 */
	public function save_rate()
	{
		$idWorkorder = $this->input->post('hddIdWorkOrder');

		if ($this->workorders_model->saveRate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Rate!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('workorders/view_workorder/' . $idWorkorder), 'refresh');
	}

	/**
	 * Save hour
	 * para editar las horas que se han guardado
	 * @since 17/4/2017
	 * @author BMOTTAG
	 */
	public function save_hour()
	{
		$arrParam["idWorkOrder"] = $idWorkorder = $this->input->post('hddIdWorkOrder');
		$infoWorkorder = $this->workorders_model->get_workordes_by_idUser($arrParam);
		$formType = $this->input->post('formType');

		if ($this->workorders_model->saveRate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Rate!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('workorders/add_workorder/' . $idWorkorder), 'refresh');
	}

	/**
	 * Evio de correo a la empresa
	 * @since 2/7/2017
	 * @author BMOTTAG
	 */
	public function email($id)
	{
		$arrParam = array('idWorkOrder' => $id);
		$infoWorkorder = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		$infoWorkorderEquipment = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list

		foreach ($infoWorkorderEquipment as $lista) {
			$nota = "";
			if ($lista["fk_id_company"] != "" && $lista["fk_id_company"] != 0) {

				$subjet = "Workorder Information";
				$user = $lista["contact"];
				$to = $lista["email"];

				//mensaje del correo
				$msj = "<p>The following is the workorder information:</p>";
				$msj .= "<strong>Company: </strong> V-CONTRACTING";
				$msj .= "<br><strong>Job Code/Name: </strong>" . $infoWorkorder[0]["job_description"];
				$msj .= "<br><strong>Date: </strong>" . $infoWorkorder[0]["date"];
				$msj .= "<br><strong>Done by: </strong>" . $infoWorkorder[0]["name"];
				$msj .= "<br><strong>Observation: </strong>" . $infoWorkorder[0]["observation"];

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
				$cabeceras .= 'From: Lev-West APP <fabian.v@lev-west.com>' . "\r\n";

				//enviar correo al cliente
				mail($to, $subjet, $mensaje, $cabeceras);

				//enviar correo a Lev-West
				mail('fabian.v@lev-west.com', $subjet, $mensaje, $cabeceras);

				if ($lista['foreman_name'] && $lista['foreman_email']) {

					$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
					$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$cabeceras .= 'From: Lev-West APP <fabian.v@lev-west.com>' . "\r\n";

					//enviar correo al FOREMAN
					mail($lista['foreman_email'], $subjet, $mensaje, $cabeceras);
				}

				$nota = 'You have send an email to <strong>' . $lista["company_name"] . '</strong> with the information.';
			}
		}

		$this->session->set_flashdata('retornoExito', $nota);
		redirect("/workorders/add_workorder/" . $id, 'refresh');
	}


	/**
	 * Signature
	 * @since 24/11/2017
	 * @author BMOTTAG
	 */
	public function add_signature($idWorkOrder)
	{
		if (empty($idWorkOrder)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of de file
			$name = "images/signature/workorder/workorder_" . $idWorkOrder . ".png";

			$arrParam = array(
				"table" => "workorder",
				"primaryKey" => "id_workorder",
				"id" => $idWorkOrder,
				"column" => "signature_wo",
				"value" => $name
			);
			//enlace para regresar al formulario
			$data['linkBack'] = "workorders/foreman_view/" . $idWorkOrder;


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
	 * Save workorder and send email
	 * @since 29/3/2018
	 * @author BMOTTAG
	 */
	public function save_workorder_and_send_email()
	{
		header('Content-Type: application/json');
		$data = array();

		if ($idWorkorder = $this->workorders_model->add_workorder()) {
			$data["result"] = true;
			$data["mensaje"] = "You have update the Work Order and send an email to the contractor, continue uploading the information.";
			$data["idWorkorder"] = $idWorkorder;
			$this->email_v2($idWorkorder);
			$this->session->set_flashdata('retornoExito', 'You have updated the Work Order and send an email to the contractor, continue uploading the information.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idWorkorder"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Evio de correo a la empresa
	 * @since 2/7/2017
	 * @author BMOTTAG
	 */
	public function email_v2($id)
	{
		$arrParam = array('idWorkOrder' => $id);
		$infoWorkorder = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		$subjet = "Workorder Information";
		$user = $infoWorkorder[0]["contact"];
		$to = $infoWorkorder[0]["email"];

		//mensaje del correo
		$msj = "<p>The following is the workorder information:</p>";
		$msj .= "<strong>Workorder number: </strong>" . $id;
		$msj .= "<br><strong>Company: </strong> V-CONTRACTING";
		$msj .= "<br><strong>Job Code/Name: </strong>" . $infoWorkorder[0]["job_description"];
		$msj .= "<br><strong>Date: </strong>" . $infoWorkorder[0]["date"];
		$msj .= "<br><strong>Done by: </strong>" . $infoWorkorder[0]["name"];

		if ($infoWorkorder[0]["foreman_name_wo"]) {
			$msj .= "<br><strong>Foreman's name: </strong>" . $infoWorkorder[0]["foreman_name_wo"];
		}

		if ($infoWorkorder[0]["observation"]) {
			$msj .= "<br><strong>Observation: </strong>" . $infoWorkorder[0]["observation"];
		}

		$infoWorkorderEquipment = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list

		if ($infoWorkorderEquipment) {

			foreach ($infoWorkorderEquipment as $lista) {

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

		if ($infoWorkorder[0]['signature_wo']) {
			$msj .= "<p><img src='http://v-contracting.ca/app/" . $infoWorkorder[0]['signature_wo'] . "' width='304' height='236' />";
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
		$cabeceras .= 'From: Lev-West APP <hugo@v-contracting.com>' . "\r\n";

		//enviar correo al cliente
		mail($to, $subjet, $mensaje, $cabeceras);

		//mensaje de texto
		$mensajeSMS = "Lev-West APP - Work Order Email";
		$mensajeSMS .= "\nAn email was sent to the client with the Work Order information.";
		$mensajeSMS .= "\nWorkorder number: " . $id;

		//enviar correo a Lev-West
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
			$cabeceras .= 'From: Lev-West APP <fabian.v@lev-west.com>' . "\r\n";

			//enviar correo al FOREMAN
			mail($lista['foreman_email'], $subjet, $mensaje, $cabeceras);
		}

		return TRUE;
	}

	/**
	 * Save workorder state
	 * @since 11/1/2020
	 * @author BMOTTAG
	 */
	public function save_workorder_state()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWorkorder"] = $this->input->post('hddIdWorkOrder');
		$idACS = $this->input->post('hddIdAcs');
		$status = $this->input->post('state');

		$msj = "You have added additional information to the Work Order.";

		$arrParam = array(
			"idWorkorder" => $data["idWorkorder"],
			"observation" => $this->input->post('information'),
			"state" => $status
		);

		if ($this->workorders_model->add_workorder_state($arrParam)) {
			//actualizo el estado del formulario
			$arrParam = array(
				"idWorkorder" => $data["idWorkorder"],
				"state" => $status,
				"lastMessage" => $this->input->post('information')
			);
			$this->workorders_model->update_workorder($arrParam);

			if ($status == IN_PROGRESS && !$idACS) {
				$arrParam = array('idWorkOrder' => $data["idWorkorder"]);
				$info['workorder'] = $this->workorders_model->get_workordes_by_idUser($arrParam); //info workorder
				$info['workorderPersonal'] = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list
				$info['workorderMaterials'] = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
				$info['workorderReceipt'] = $this->workorders_model->get_workorder_receipt($arrParam); //workorder invoice list
				$info['workorderEquipment'] = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list
				$info['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list
				$this->workorders_model->clone_workorder($info);

				//Closed hauling cards 
				$this->load->model("general_model");
				$arrParam = array(
					"table" => "hauling",
					"order" => "id_hauling",
					"column" => "fk_id_workorder",
					"id" => $data["idWorkorder"]
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
	 * Lista de Workorders filtrado por estado
	 * @since 22/2/2020
	 * @author BMOTTAG
	 */
	public function wo_by_state($state, $year = "x")
	{
		$from = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //primer dia del año
		//$to = date('Y-m-d',(mktime(0,0,0,13,1,$year)-1));//ultimo dia del año
		$to = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year + 1)); //primer dia del siguiente año para que incluya todo el dia anterior en la consulta

		$data['jobName'] =  $this->input->post('jobName');
		$data['workOrderNumber'] =  $this->input->post('workOrderNumber');
		$data['workOrderNumberFrom'] =  $this->input->post('workOrderNumberFrom');
		$data['workOrderNumberTo'] =  $this->input->post('workOrderNumberTo');
		$data['from'] =  $this->input->post('from');
		$data['to'] =  $this->input->post('to');

		$arrParam = array(
			"from" => $from,
			"to" => $to,
			"state" => $state
		);

		//guardo la informacion en la base de datos para el boton de regresar
		$this->workorders_model->saveInfoGoBack($arrParam);

		//informacion Work Order
		$data['workOrderInfo'] = $this->workorders_model->get_workorder_by_idJob($arrParam);

		$data["view"] = "asign_rate_list";
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Generate WORK ORDER Report in PDF
	 * @param int $idWorkOrder
	 * @since 4/11/2018
	 * @author BMOTTAG
	 */
	public function generaWorkOrderPDF($idWorkOrder)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$arrParam = array("idWorkOrder" => $idWorkOrder);
		$data['info'] = $this->workorders_model->get_workorder_by_idJob($arrParam);

		if (empty($data['info'])) {
			// Option 1: Show a custom error message
			show_error('No Work Order information found for ID: ' . $idWorkOrder, 404);
			return;
		}

		$fecha = date('F j, Y', strtotime($data['info'][0]['date']));

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Lev-West');
		$pdf->SetTitle('WORK ORDER');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'WORK ORDER', 'W.O. #: ' . $idWorkOrder . "\nW.O. date: " . $fecha, array(0, 64, 255), array(0, 64, 128));

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
		$data['workorderPersonal'] = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list			
		$data['workorderMaterials'] = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
		$data['workorderReceipt'] = $this->workorders_model->get_workorder_receipt($arrParam); //workorder ocasional list
		$data['workorderEquipment'] = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list
		$data['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list
		$data['workorderHoldBack'] = false;//$this->workorders_model->get_workorder_hold_back($idWorkOrder); //workorder ocasional list
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		//$pdf->AddPage('L', 'A4');
		$pdf->AddPage();

		$html = $this->load->view("reporte_work_order", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('work_order_' . $idWorkOrder . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate Payroll Report in XLS
	 * @param int $jobId
	 * @since 10/02/2020
	 * @author BMOTTAG
	 */
	public function generaWorkOrderXLS($jobId, $from = '', $to = '')
	{
		$arrParam = array(
			"jobId" => $jobId,
			"from" => $from,
			"to" => $to
		);
		$info = $this->workorders_model->get_workorder_by_idJob($arrParam);
		$jobCode = $info[0]['job_description'];

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=workorder_' . $jobCode . '.xlsx');

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
		$total = 0;
		foreach ($info as $data) :

			$arrParam = array('idWorkOrder' => $data['id_workorder']);
			$workorderPersonal = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list
			$workorderMaterials = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
			$workorderReceipts = $this->workorders_model->get_workorder_receipt($arrParam); //workorder receipts list
			$workorderEquipment = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list
			$workorderOcasional = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderPersonal) {
				foreach ($workorderPersonal as $infoP) :
					$total += $infoP['value'];
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
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

			if ($workorderMaterials) {
				foreach ($workorderMaterials as $infoM) :
					$total += $infoM['value'];
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

			if ($workorderReceipts) {
				foreach ($workorderReceipts as $infoR) :
					$total += $infoR['value'];
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
					$total += $infoE['value'];
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
					$total += $infoO['value'];
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

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Work Order #')
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

		$totalP = 0;
		$j = 2;
		foreach ($info as $data) :

			$arrParam = array('idWorkOrder' => $data['id_workorder']);
			$workorderPersonal = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderPersonal) {
				foreach ($workorderPersonal as $infoP) :
					$totalP += $infoP['value'];
					$spreadsheet->getActiveSheet()->setCellValue('A' . $j, $data['id_workorder'])
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

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Work Order #')
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

		$totalM = 0;
		$j = 2;
		foreach ($info as $data) :

			$arrParam = array('idWorkOrder' => $data['id_workorder']);
			$workorderMaterials = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderMaterials) {
				foreach ($workorderMaterials as $infoM) :
					$totalM += $infoM['value'];
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

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Work Order #')
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

		$totalR = 0;
		$j = 2;
		foreach ($info as $data) :

			$arrParam = array('idWorkOrder' => $data['id_workorder']);
			$workorderReceipts = $this->workorders_model->get_workorder_receipt($arrParam); //workorder receipts list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderReceipts) {
				foreach ($workorderReceipts as $infoR) :
					$totalR += $infoR['value'];
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

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Work Order #')
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

		$totalE = 0;
		$j = 2;
		foreach ($info as $data) :
			$arrParam = array('idWorkOrder' => $data['id_workorder']);
			$workorderEquipment = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderEquipment) {
				foreach ($workorderEquipment as $infoE) :
					$totalE += $infoE['value'];
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

		$spreadsheet->getActiveSheet()->setCellValue('A1', 'Work Order #')
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

		$totalS = 0;
		$j = 2;
		foreach ($info as $data) :
			$arrParam = array('idWorkOrder' => $data['id_workorder']);
			$workorderOcasional = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list

			$observation = $data['observation'] ? $data['observation'] : '';
			if ($workorderOcasional) {
				foreach ($workorderOcasional as $infoO) :
					$totalS += $infoO['value'];
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
	 * @since 16/03/2020
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
		$data['noOnfield'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 1);
		$data['noProgress'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 2);
		$data['noRevised'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 3);
		$data['noSend'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 4);
		$data['noClosed'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 5);
		$data['noAccounting'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders

		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		if ($this->input->post('jobName') && $this->input->post('from') && $this->input->post('to')) {
			$data['idJob'] =  $this->input->post('jobName');
			$data['from'] =  $this->input->post('from');
			$data['to'] =  $this->input->post('to');

			$to = isset($data['to']) ? formatear_fecha($data['to']) : "";
			$from = isset($data['from']) ? formatear_fecha($data['from']) : "";

			$data['fromFormat'] =  $from;
			$data['toFormat'] =  $to;

			//informacion Work Order
			$arrParam = array(
				"idJob" => $data['idJob'],
				"from" => $from,
				"to" => $to
			);
			$data['noWO'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders

			$data['hoursPersonal'] = $this->workorders_model->countHoursPersonal($arrParam); //cuenta horas de personal

			$arrParam['table'] = "workorder_personal";
			$data['incomePersonal'] = $this->workorders_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "workorder_materials";
			$data['incomeMaterial'] = $this->workorders_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "workorder_equipment";
			$data['incomeEquipment'] = $this->workorders_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "workorder_ocasional";
			$data['incomeSubcontractor'] = $this->workorders_model->countIncome($arrParam); //cuenta horas de personal

			$arrParam['table'] = "workorder_receipt ";
			$data['incomeReceipt'] = $this->workorders_model->countIncome($arrParam); //cuenta horas de personal

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
	 * Foreman workorder view to sign
	 * @since 4/6/2020
	 * @author BMOTTAG
	 */
	public function foreman_view($id)
	{
		$arrParam = array('idWorkOrder' => $id);
		$data['workorderPersonal'] = $this->workorders_model->get_workorder_personal($arrParam); //workorder personal list
		$data['workorderMaterials'] = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
		$data['workorderEquipment'] = $this->workorders_model->get_workorder_equipment($arrParam); //workorder equipment list
		$data['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list
		$data['workorderHoldBack'] = false;//$this->workorders_model->get_workorder_hold_back($id); //workorder ocasional list

		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		$data["view"] = 'foreman_view';
		$this->load->view("layout", $data);
	}

	/**
	 * Foreman info
	 * @since 25/2/2020
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
	 * @since 4/6/2020
	 * @author BMOTTAG
	 */
	public function sendSMSForeman($idWorkOrder)
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

		$arrParam['idWorkOrder'] =  $idWorkOrder;
		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		$mensaje = "";

		$mensaje .= date('F j, Y', strtotime($data['information'][0]['date']));
		$mensaje .= "\n" . $data['information'][0]['job_description'];
		$mensaje .= "\n" . $data['information'][0]['observation'];
		$mensaje .= "\n";
		$mensaje .= "Click the following link to review W.O. " . $idWorkOrder;
		$mensaje .= "\n";
		$mensaje .= "\n";
		$mensaje .= base_url("workorders/foreman_view/" . $idWorkOrder);

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

		$data['linkBack'] = "workorders/add_workorder/" . $idWorkOrder;
		$data['titulo'] = "<i class='fa fa-list'></i>WORK ORDER";

		$data['clase'] = "alert-info";
		$data['msj'] = "We have send the SMS to the foreman to sign the Work Order No." . $idWorkOrder;

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}

	/**
	 * Load prices WO
	 * @since 7/11/2020
	 * @author BMOTTAG
	 */
	public function load_prices_wo()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWO"] = $idWO = $this->input->post('identificador');

		$arrParam['idWorkOrder'] =  $idWO;
		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder
		$idJob = $data['information'][0]['fk_id_job'];

		$workorderPersonalRate = $this->workorders_model->get_workorder_personal_prices($idWO, $idJob); //workorder personal list
		$workorderEquipmentRate = $this->workorders_model->get_workorder_equipment_prices($idWO, $idJob); //workorder equipment list
		$workorderMaterialRate = $this->workorders_model->get_workorder_material_prices($idWO); //workorder material list

		if ($workorderPersonalRate) {
			if ($this->workorders_model->update_wo_personal_rate($workorderPersonalRate)) {
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

		if ($workorderEquipmentRate) {
			if ($this->workorders_model->update_wo_equipment_rate($workorderEquipmentRate)) {
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

		if ($workorderMaterialRate) {
			if ($this->workorders_model->update_wo_material_rate($workorderMaterialRate)) {
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
	 * @since 4/1/2021
	 */
	public function cargarModalReceipts()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idWorkorder = $this->input->post("idWorkorder");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idWorkorder);
		$data["idWorkorder"] = $porciones[1];

		$this->load->view("modal_receipt", $data);
	}

	/**
	 * Actualizar info de invoice
	 * @since 4/1/2021
	 * @author BMOTTAG
	 */
	public function update_receipt()
	{
		$idWorkorder = $this->input->post('hddidWorkorder');
		$view = $this->input->post('view');

		if ($this->workorders_model->saveReceipt()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the information!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('workorders/' . $view . '/' . $idWorkorder), 'refresh');
	}

	/**
	 * Load markup WO
	 * @since 4/1/2021
	 * @author BMOTTAG
	 */
	public function load_markup_wo()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWO"] = $idWO = $this->input->post('identificador');

		$arrParam = array('idWorkOrder' => $idWO);
		$infoWO = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		$workorderMaterials = $this->workorders_model->get_workorder_materials($arrParam); //workorder material list
		$workorderReceipt = $this->workorders_model->get_workorder_receipt($arrParam); //workorder invoice list
		$workorderOcasional = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list

		if ($workorderReceipt) {
			if ($this->workorders_model->update_wo_invoice_markup($workorderReceipt, $infoWO[0]['markup'])) {
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

		if ($workorderMaterials) {
			if ($this->workorders_model->update_wo_material_markup($workorderMaterials, $infoWO[0]['markup'])) {
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

		if ($workorderOcasional) {
			if ($this->workorders_model->update_wo_ocasional_markup($workorderOcasional, $infoWO[0]['markup'])) {
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
	 * @since 12/1/2021
	 * @author BMOTTAG
	 */
	public function update_wo_state()
	{
		header('Content-Type: application/json');
		$data = array();

		$wo = $this->input->post('wo');
		if ($wo) {
			if ($this->workorders_model->updateWOState()) {
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
	 * @since 13/1/2022
	 */
	public function cargarModalExpense()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idWorkorder"] = $this->input->post("idWorkorder");

		$this->load->model("general_model");
		$arrParam = array('idWorkOrder' => $data["idWorkorder"]);
		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder

		//JOB detail list
		$data["idJob"] = $data['information'][0]["fk_id_job"];
		$arrParam = array("idJob" => $data["idJob"]);
		$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);
		$data['sumPercentage'] = $this->general_model->sumPercentageByJob($arrParam);

		$this->load->view("modal_expense", $data);
	}

	/**
	 * Update WO Expenses Values
	 * @since 21/1/2023
	 * @author BMOTTAG
	 */
	public function update_wo_expenses_values($idWorkorder, $idJob)
	{
		$arrParam = array('idWorkOrder' => $idWorkorder);
		$workorderExpenses = $this->workorders_model->get_workorder_expense($arrParam); //workorder expense list
		$sumPercentageExpense = $this->workorders_model->sumPercentageExpense($arrParam); //workorder expense list

		//search for total WO INCOME
		$arrParam['idJob'] = $idJob;
		$arrParam['table'] = "workorder_personal";
		$incomePersonal = $this->workorders_model->countIncome($arrParam); //INCOME PERSOMAL

		$arrParam['table'] = "workorder_materials";
		$incomeMaterial = $this->workorders_model->countIncome($arrParam); //INCOME MATERIAL

		$arrParam['table'] = "workorder_equipment";
		$incomeEquipment = $this->workorders_model->countIncome($arrParam); //INCOME EQUIPMENT

		$arrParam['table'] = "workorder_ocasional";
		$incomeSubcontractor = $this->workorders_model->countIncome($arrParam); //INCOME OCASIONAL

		$arrParam['table'] = "workorder_receipt ";
		$incomeReceipt = $this->workorders_model->countIncome($arrParam); //INCOME RECEIPT

		$totalWOIncome = $incomePersonal + $incomeMaterial + $incomeEquipment + $incomeSubcontractor + $incomeReceipt;

		if ($totalWOIncome > 0 && $sumPercentageExpense) {
			//$this->workorders_model->updateExpensesValues($workorderExpenses, $totalWOIncome, $sumPercentageExpense);
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
	 * @since 9/06/2023
	 * @author BMOTTAG
	 */
	public function recalculate_expenses()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idWO"] = $idWorkorder = $this->input->post('identificador');

		$arrParam['idWorkOrder'] =  $idWorkorder;
		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder
		$idJob = $data['information'][0]['fk_id_job'];

		if ($this->update_wo_expenses_values($idWorkorder, $idJob)) {
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
	 * @since 28/6/2023
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
	 * LOG Workorders
	 * @since 20/02/2024
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
		$data['noOnfield'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 1);
		$data['noProgress'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 2);
		$data['noRevised'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 3);
		$data['noSend'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders
		$arrParam = array("state" => 4);
		$data['noClosed'] = $this->workorders_model->countWorkorders($arrParam); //cuenta registros de Workorders

		$data["view"] = "job_search";

		//viene desde el boton go back
		if ($goBack == 'y') {
			$workOrderGoBackInfo = $this->workorders_model->get_workorder_go_back();

			if (!$workOrderGoBackInfo) {
				redirect(base_url('workorders'), 'refresh');
			} else {
				//le sumo un dia al dia final para que ingrese ese dia en la consulta
				$to = date('Y-m-d', strtotime('+1 day ', strtotime(formatear_fecha($workOrderGoBackInfo['post_to']))));
				//$from = formatear_fecha($workOrderGoBackInfo['post_from']);

				$arrParam = array(
					"jobId" => $workOrderGoBackInfo['post_id_job'],
					"idWorkOrder" => $workOrderGoBackInfo['post_id_work_order'],
					"idWorkOrderFrom" => $workOrderGoBackInfo['post_id_wo_from'],
					"idWorkOrderTo" => $workOrderGoBackInfo['post_id_wo_to'],
					"from" => $workOrderGoBackInfo['post_from'], //$from,
					"to" => $to,
					"state" => $workOrderGoBackInfo['post_state']
				);

				$data['workOrderInfo'] = $this->workorders_model->get_workorder_by_idJob($arrParam);

				$data["view"] = "asign_rate_list";
				$this->load->view("layout_calendar", $data);
			}
		}
		//Si envian los datos del filtro entonces lo direcciono a la lista respectiva con los datos de la consulta
		elseif ($this->input->post('jobName') || $this->input->post('user') || $this->input->post('from') || $this->input->post('workOrderNumber')) {



			$data['jobName'] =  $this->input->post('jobName');
			$data['workOrderNumber'] =  $this->input->post('workOrderNumber');
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
				"idWorkOrder" => $this->input->post('workOrderNumber'),
				"userId" => $this->input->post('user'),
				"from" => $from,
				"to" => $to
			);

			//guardo la informacion en la base de datos para el boton de regresar
			//$this->workorders_model->saveInfoGoBack($arrParam);

			//informacion Work Order
			$data['workOrderInfo'] = $this->workorders_model->get_workorder_log($arrParam);

			$data["view"] = "log_list";
			$this->load->view("layout_calendar", $data);
		} else {
			$this->load->view("layout", $data);
		}
	}

	/**
	 * View workorder_expenses
	 * @since 23/03/2024
	 * @author BMOTTAG
	 */
	public function workorder_expenses($idWorkOrder)
	{
		$this->load->model("general_model");

		$arrParam = array("idWorkOrder" => $idWorkOrder, "flag_expenses" => true);
		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam);
		$data['workorderPersonal'] = $this->workorders_model->get_workorder_personal($arrParam);
		$data['workorderMaterials'] = $this->workorders_model->get_workorder_materials($arrParam);
		$data['workorderReceipt'] = $this->workorders_model->get_workorder_receipt($arrParam);
		$data['workorderEquipment'] = $this->workorders_model->get_workorder_equipment($arrParam);
		$data['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam);
		$data['workorderExpenses'] = $this->general_model->get_workorder_expense($arrParam);

		$arrParam = array("idJob" => $data['information'][0]['fk_id_job'], "status" => 1);
		$data['jobDetails'] = $this->general_model->get_job_detail($arrParam);

		$data["view"] = 'expenses';
		$this->load->view("layout", $data);
	}

	/**
	 * Save WO Expenses
	 * @since 24/03/2024
	 * @author BMOTTAG
	 */
	public function save_wo_expenses()
	{
		$idWorkOrder = $this->input->post('hddidWorkorder');
		$idJob = $this->input->post('hddidJob');

		if ($this->workorders_model->saveWOExpenses()) {
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
				"table" => "workorder",
				"primaryKey" => "id_workorder",
				"id" => $idWorkOrder,
				"order" => 'id_workorder',
				"column" => "expenses_flag",
				"value" => 1
			);
			$this->general_model->updateRecord($arrParam);
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the W.O. Expenses!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('workorders/workorder_expenses/' . $idWorkOrder), 'refresh');
	}

	/**
	 * Subcontracotrs list
	 * @since 13/02/2025
	 * @author BMOTTAG
	 */
	public function subcontractor_invoice()
	{
		$arrParam = array();
		$data['information'] = $this->workorders_model->get_subcontractors_invoice($arrParam);

		$data["view"] = 'subcontractors_invoices';
		$this->load->view("layout", $data);
	}

	/**
	 * Form To add invoice
	 * @since 12/02/2025
	 * @author BMOTTAG
	 */
	public function add_invoice($id = 'x')
	{
		$data['information'] = FALSE;
		$data['deshabilitar'] = '';

		$this->load->model("general_model");
		$arrParam = array("allSubcontractors" => true);
		$data['companyList'] = $this->general_model->get_company($arrParam);

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array('idSubcontractorInvoice' => $id);
			$data['information'] = $this->workorders_model->get_subcontractors_invoice($arrParam);

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_invoice';
		$this->load->view("layout", $data);
	}


	/**
	 * Save Subcontractor Invoice
	 * @since 13/02/2025
	 * @author BMOTTAG
	 */
	public function save_subcontractor_invoice()
	{
		header('Content-Type: application/json');
		$data = array();

		$idSubcontractorInvoice = $this->input->post('hddIdentificador');

		$msj = "You have added a new Subcontractor Invoice.";
		if ($idSubcontractorInvoice != '') {
			$msj = "You have updated a Subcontractor Invoice.";
		}

		$config['upload_path'] = './files/sub_invoices/';
		$config['overwrite'] = TRUE;
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = '3000';
		$config['max_width'] = '2024';
		$config['max_height'] = '2008';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload() && $_FILES['userfile']['name'] != "") {
			//SI EL ARCHIVO FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
            $error = $this->upload->display_errors('', '');
            $data["result"] = "error";
            $data["message"] = "File upload failed: " . strip_tags($error);
            echo json_encode($data);
            return;
		} else {
			if ($_FILES['userfile']['name'] == "") {
				$archivo = 'xxx';
			} else {
				$file_info = $this->upload->data(); //subimos ARCHIVO				
				$data = array('upload_data' => $this->upload->data());
				$archivo = $file_info['file_name'];
			}

			if ($idSubcontractorInvoice = $this->workorders_model->saveSubcontractorInvoice($archivo)) {
				$data["result"] = true;
				$data["idRecord"] = $idSubcontractorInvoice;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

		}
		echo json_encode($data);
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
	 * @param int $idWorkorder: llave  primaria de workorder
	 */
	public function deleteRecordExpenses($subModule, $idWorkorderExpenses, $idSubmodule, $idWorkOrder)
	{
		if (empty($subModule) || empty($idWorkorderExpenses) || empty($idWorkOrder)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "workorder_expense",
			"primaryKey" => "id_workorder_expense",
			"id" => $idWorkorderExpenses
		);
		$this->load->model("general_model");

		if ($this->general_model->deleteRecord($arrParam)) {
			//para expenses recalculo los valores gastados para cada item
			$arrParam = array('idWorkOrder' => $idWorkOrder);
			$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder
			$idJob = $data['information'][0]["fk_id_job"];
			$this->update_wo_expenses_values($idWorkOrder, $idJob);

			//Update workorder submodule
			$table = "workorder_" . $subModule;
			$arrParam = array(
				"table" => "workorder_" . $subModule,
				"primaryKey" => "id_workorder_" . $subModule,
				"id" => $idSubmodule,
				"column" => "flag_expenses",
				"value" => 0
			);
			$this->general_model->updateRecord($arrParam);

			$this->session->set_flashdata('retornoExito', 'You have deleted one record.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('workorders/workorder_expenses/' . $idWorkOrder), 'refresh');
	}

	/**
	 * View Subcontractors to asocited with Invoices
	 * @since 23/04/2025
	 * @author BMOTTAG
	 */
	public function subcontractor_invoices($id)
	{
		$this->load->model("general_model");

		$arrParam = array('idWorkOrder' => $id);
		$data['information'] = $this->workorders_model->get_workorder_by_idJob($arrParam); //info workorder
		$data['workorderOcasional'] = $this->workorders_model->get_workorder_ocasional($arrParam); //workorder ocasional list

		//DESHABILITAR WORK ORDER
		$userRol = $this->session->rol;
		$workorderState = $data['information'][0]['state'];
		$data['deshabilitar'] = '';

		//si esta cerrada deshabilito los botones
		if ($workorderState == 4) {
			$data['deshabilitar'] = 'disabled';
			//If it is DIFERRENT THAN ON FILD and ROLE is SUPERVISOR OR BASIC OR Safety&Maintenance
		} elseif ($workorderState != 0 && ($userRol == 4 || $userRol == 6 || $userRol == 7)) {
			$data['deshabilitar'] = 'disabled';
		} elseif (($workorderState == 2 || $workorderState == 3) && $userRol == 5) { //WORK ORDER  USER
			$data['deshabilitar'] = 'disabled';
		} elseif ($workorderState == 1 && ($userRol == 2 || $userRol == 3)) { //MANAGEMENT AND ACCOUNTING USER
			$data['deshabilitar'] = 'disabled';
		}

		$data["view"] = 'subcontractor_invoices';
		$this->load->view("layout", $data);
	}

	/**
	 * Save subcontractor invoices
	 * @since 23/04/2025
	 * @author BMOTTAG
	 */
	public function save_subcontractor_invoices()
	{
		$idWorkorder = $this->input->post('hddIdWorkOrder');

		$arrParam = array(
			"table" => "workorder_ocasional",
			"primaryKey" => "id_workorder_ocasional",
			"id" => $this->input->post('hddId'),
			"column" => "fk_id_subcontractor_invoice",
			"value" => $this->input->post('idInvoices')
		);
		$this->load->model("general_model");
		if ($this->general_model->updateRecord($arrParam)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Rate!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('workorders/subcontractor_invoices/' . $idWorkorder), 'refresh');
	}

}
