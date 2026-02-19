<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Workorders_model extends CI_Model
{


	/**
	 * Workorders´s list
	 * las 10 records
	 * @since 12/1/2017
	 */
	public function get_workordes_by_idUser($arrDatos)
	{
		//$year = date('Y');
		//$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year));//para filtrar solo los registros del año actual

		$this->db->select('W.*, J.id_job, job_description, CONCAT(U.first_name, " ", U.last_name) name, C.company_name company, C.id_company, A.id_acs');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');
		$this->db->join('acs A', 'A.fk_id_workorder = W.id_workorder', 'LEFT');
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');

		if (array_key_exists("idWorkOrder", $arrDatos)) {
			$this->db->where('id_workorder', $arrDatos["idWorkOrder"]);
		}
		if (array_key_exists("idEmployee", $arrDatos)) {
			$this->db->where('W.fk_id_user', $arrDatos["idEmployee"]);
		}

		//$this->db->where('W.date >=', $firstDay);

		$this->db->order_by('id_workorder', 'desc');
		$query = $this->db->get('workorder W', 50);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add workorder
	 * @since 13/1/2017
	 */
	public function add_workorder()
	{
		$this->load->library('logger');
		$this->load->model("general_model");

		$idUser = $this->session->userdata("id");
		$idWorkorder = $this->input->post('hddIdentificador');

		$data = array(
			'fk_id_job' => $this->input->post('jobName'),
			'date' => $this->input->post('date'),
			'fk_id_company' => $this->input->post('company'),
			'foreman_name_wo' => $this->input->post('foreman'),
			'foreman_movil_number_wo' => $this->input->post('movilNumber'),
			'foreman_email_wo' => $this->input->post('email'),
			'observation' => $this->input->post('observation')
		);

		//revisar si es para adicionar o editar
		if ($idWorkorder == '') {
			$data['fk_id_user'] = $idUser;
			$data['date_issue'] = date("Y-m-d G:i:s");
			$data['state'] = 0;
			$data['last_message'] = 'New work order.';
			$query = $this->db->insert('workorder', $data);
			$idWorkorder = $this->db->insert_id();

			$log['old'] = null;
			$log['new'] = json_encode($data);

			$this->logger
				->user($this->session->userdata("id")) //;//Set UserID, who created this  Action
				->type('workorder') //Entry type like, Post, Page, Entry
				->id($idWorkorder) //Entry ID
				->token('insert') //Token identify Action
				->comment(json_encode($log))
				->log(); //Add Database Entry
		} else {
			$arrParam = array(
				"table" => "workorder",
				"order" => "id_workorder",
				"column" => "id_workorder",
				"id" => $idWorkorder
			);
			$log['old'] = $this->general_model->get_basic_search($arrParam);
			$log['new'] = json_encode($data);

			$this->db->where('id_workorder', $idWorkorder);
			$query = $this->db->update('workorder', $data);


			$this->logger
				->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
				->type('workorder') //Entry type like, Post, Page, Entry
				->id($idWorkorder) //Entry ID
				->token('update') //Token identify Action
				->comment(json_encode($log))
				->log(); //Add Database Entry
		}

		if ($query) {
			return $idWorkorder;
		} else {
			return false;
		}
	}

	/**
	 * Get workorder personal info
	 * @since 9/2/2021
	 */
	public function get_workorder_personal($arrData)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, T.employee_type");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->join('param_employee_type T', 'T.id_employee_type = W.fk_id_employee_type', 'INNER');
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		if (array_key_exists("flag_expenses", $arrData)) {
			$this->db->where('W.flag_expenses', 0);
		}
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('workorder_personal W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add personal
	 * @since 13/1/2017
	 */
	public function savePersonal()
	{
		$this->load->library('logger');
		$idWorkorder = $this->input->post('hddidWorkorder');

		$data = array(
			'fk_id_workorder' => $idWorkorder,
			'fk_id_user' => $this->input->post('employee'),
			'fk_id_employee_type' => $this->input->post('type'),
			'hours' => $this->input->post('hour'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('workorder_personal', $data);

		$log['old'] = null;
		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
			->type('workorder_personal') //Entry type like, Post, Page, Entry
			->id($idWorkorder) //Entry ID
			->token('insert') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get workorder materials info
	 * @since 13/1/2017
	 */
	public function get_workorder_materials($arrData)
	{
		$this->db->select();
		$this->db->join('param_material_type M', 'M.id_material = W.fk_id_material', 'INNER');
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		if (array_key_exists("flag_expenses", $arrData)) {
			$this->db->where('W.flag_expenses', 0);
		}
		$this->db->order_by('M.material', 'asc');
		$query = $this->db->get('workorder_materials W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add Material
	 * @since 13/1/2017
	 */
	public function saveMaterial()
	{
		$this->load->library('logger');
		$idWorkorder = $this->input->post('hddidWorkorder');

		$data = array(
			'fk_id_workorder' => $this->input->post('hddidWorkorder'),
			'fk_id_material' => $this->input->post('material'),
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('workorder_materials', $data);

		$log['old'] = null;
		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
			->type('workorder_materials') //Entry type like, Post, Page, Entry
			->id($idWorkorder) //Entry ID
			->token('insert') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Ocasional
	 * @since 20/2/2017
	 */
	public function saveOcasional()
	{
		$this->load->library('logger');
		$idWorkorder = $this->input->post('hddidWorkorder');

		$data = array(
			'fk_id_workorder' => $this->input->post('hddidWorkorder'),
			'fk_id_company' => $this->input->post('company'),
			'equipment' => $this->input->post('equipment'),
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'hours' => $this->input->post('hour'),
			'contact' => $this->input->post('contact'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('workorder_ocasional', $data);

		$log['old'] = null;
		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
			->type('workorder_ocasional') //Entry type like, Post, Page, Entry
			->id($idWorkorder) //Entry ID
			->token('insert') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add HOLD BACK
	 * @since 12/11/2018
	 */
	public function saveHoldBack()
	{
		$data = array(
			'fk_id_workorder' => $this->input->post('hddidWorkorder'),
			'value' => $this->input->post('value'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('workorder_hold_back', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Trucks list by type1 = rentals
	 * que esten activas
	 * @since 8/3/2017
	 */
	public function get_trucks_by_id1()
	{
		$trucks = array();
		$sql = "SELECT id_vehicle, CONCAT(unit_number,' -----> ', description) as unit_description
					FROM param_vehicle 
					WHERE type_level_1 = 2 AND state = 1
					ORDER BY unit_number";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$i = 0;
			foreach ($query->result() as $row) {
				$trucks[$i]["id_truck"] = $row->id_vehicle;
				$trucks[$i]["unit_number"] = $row->unit_description;
				$i++;
			}
		}
		$this->db->close();
		return $trucks;
	}

	/**
	 * Add Equipment
	 * @since 25/1/2017
	 */
	public function saveEquipment()
	{
		$this->load->library('logger');
		$idWorkorder = $this->input->post('hddidWorkorder');

		$type = $this->input->post('type');
		$truck = $this->input->post('truck');

		if ($type == 8) {
			$truck = 5;
		}
		//si es diferente a Pickup entonces colocar que Stanby = NO
		if ($type != 3) {
			$standby = 2;
		} else {
			$standby = $this->input->post('standby');
		}
		$data = array(
			'fk_id_workorder' => $this->input->post('hddidWorkorder'),
			'fk_id_type_2' => $this->input->post('type'),
			'fk_id_vehicle' => $truck,
			'fk_id_attachment' => $this->input->post('attachment'),
			'other' => $this->input->post('otherEquipment'),
			'operatedby' => $this->input->post('operatedby'),
			'hours' => $this->input->post('hour'),
			'quantity' => $this->input->post('quantity'),
			'standby' => $standby,
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('workorder_equipment', $data);

		$log['old'] = null;
		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
			->type('workorder_equipment') //Entry type like, Post, Page, Entry
			->id($idWorkorder) //Entry ID
			->token('insert') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get workorder equipment info
	 * @since 25/1/2017
	 */
	public function get_workorder_equipment($arrData)
	{
		$this->db->select("W.*, V.make, V.model, V.unit_number, V.description v_description, M.miscellaneous, T.type_2, C.*, CONCAT(U.first_name,' ', U.last_name) as operatedby, A.attachment_number, A.attachment_description");
		$this->db->join('param_vehicle V', 'V.id_vehicle = W.fk_id_vehicle', 'LEFT');
		$this->db->join('param_attachments A', 'A.id_attachment = W.fk_id_attachment', 'LEFT');
		$this->db->join('param_miscellaneous M', 'M.id_miscellaneous = W.fk_id_vehicle', 'LEFT');
		$this->db->join('user U', 'U.id_user = W.operatedby', 'LEFT');
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = W.fk_id_type_2', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		if (array_key_exists("flag_expenses", $arrData)) {
			$this->db->where('W.flag_expenses', 0);
		}
		$query = $this->db->get('workorder_equipment W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder ocasional info
	 * @since 20/2/2017
	 */
	public function get_workorder_ocasional($arrData)
	{
		$this->db->select('W.*, C.company_name, H.id_hauling');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'INNER');
		$this->db->join('hauling H', 'H.fk_id_submodule = W.id_workorder_ocasional', 'LEFT');
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		if (array_key_exists("flag_expenses", $arrData)) {
			$this->db->where('W.flag_expenses', 0);
		}
		$this->db->order_by('C.company_name', 'asc');
		$query = $this->db->get('workorder_ocasional W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder HOLD BACK info
	 * @since 12/11/2018
	 */
	public function get_workorder_hold_back($idWorkorder)
	{
		$this->db->select();
		$this->db->where('H.fk_id_workorder', $idWorkorder);
		$this->db->order_by('H.id_workorder_hold_back', 'asc');
		$query = $this->db->get('workorder_hold_back H');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Work Order
	 * @since 21/02/2017
	 */
	public function get_workorder_by_idJob($arrData)
	{
		//$year = date('Y');
		//$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year));

		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, J.job_description, J.markup, J.notes, C.*");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');

		if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 0) {
			$this->db->where('W.fk_id_job', $arrData["jobId"]);
		}
		if (array_key_exists("idWorkOrder", $arrData) && $arrData["idWorkOrder"] != '' && $arrData["idWorkOrder"] != 0) {
			$this->db->where('W.id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("idWorkOrderFrom", $arrData) && $arrData["idWorkOrderFrom"] != '' && $arrData["idWorkOrderFrom"] != 0) {
			$this->db->where('W.id_workorder >=', $arrData["idWorkOrderFrom"]);
		}
		if (array_key_exists("idWorkOrderTo", $arrData) && $arrData["idWorkOrderTo"] != '' && $arrData["idWorkOrderTo"] != 0) {
			$this->db->where('W.id_workorder <=', $arrData["idWorkOrderTo"]);
		}
		if (array_key_exists("from", $arrData) && $arrData["from"] != '') {
			$this->db->where('W.date >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != '' && $arrData["from"] != '') {
			$this->db->where('W.date <=', $arrData["to"]);
		}
		if (array_key_exists("state", $arrData) && $arrData["state"] != '') {
			$this->db->where('W.state', $arrData["state"]);
		}

		//$this->db->where('W.date >=', $firstDay);

		$this->db->order_by('W.id_workorder', 'desc');
		$query = $this->db->get('workorder W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update Rate
	 * @since 27/2/2017
	 */
	public function saveRate()
	{
		$this->load->library('logger');
		$this->load->model("general_model");

		$hddId = $this->input->post('hddId');
		$formType = $this->input->post('formType');
		$description = $this->input->post('description');
		$unit = $this->input->post('unit');
		$rate = $this->input->post('rate');
		$quantity = $this->input->post('quantity');
		$hours = $this->input->post('hours');
		$checkPDF = $this->input->post('check_pdf');
		$idWorkorder = $this->input->post('hddIdWorkOrder');

		if ($checkPDF) {
			$checkPDF = 1;
		} else {
			$checkPDF = 2;
		}

		$value = $rate * $quantity * $hours;

		$data = array(
			'description' => $description,
			'rate' => $rate,
			'value' => $value,
			'view_pdf' => $checkPDF
		);

		$table = 'workorder_' . $formType;
		$arrParam = array(
			"table" => $table,
			"order" => "fk_id_workorder",
			"column" => "fk_id_workorder",
			"id" => $idWorkorder
		);
		$log['old'] = $this->general_model->get_basic_search($arrParam);

		switch ($formType) {
			case "personal":
				$arrParam = array(
					"table" => $table,
					"order" => "id_workorder_personal",
					"column" => "id_workorder_personal",
					"id" => $hddId
				);
				$log['old'] = $this->general_model->get_basic_search($arrParam);

				$type = $this->input->post('type_personal');
				$data['fk_id_employee_type'] = $type;
				$data['hours'] = $hours;

				//actualizar las horas de la tabla task si tiene relacion con esta WO
				if($log['old'][0]['hours'] != $hours){
					$arrTask = array(
						"idWorkOrder" => $idWorkorder,
						"idEmployee" => $log['old'][0]['fk_id_user'],
						"column" => "wo_start_project"
					);
					$taskStart = $this->general_model->get_task($arrTask);
					if($taskStart){
						$arrParam = array(
							"table" => "task",
							"primaryKey" => "id_task",
							"id" => $taskStart[0]["id_task"],
							"column" => "hours_start_project",
							"value" => $hours
						);
						$this->general_model->updateRecord($arrParam);
					}

					$arrTask = array(
						"idWorkOrder" => $idWorkorder,
						"idEmployee" => $log['old'][0]['fk_id_user'],
						"column" => "wo_end_project"
					);
					$taskEnd = $this->general_model->get_task($arrTask);
					if($taskEnd){
						$arrParam = array(
							"table" => "task",
							"primaryKey" => "id_task",
							"id" => $taskEnd[0]["id_task"],
							"column" => "hours_end_project",
							"value" => $hours
						);
						$this->general_model->updateRecord($arrParam);
					}
				}
				break;
			case "materials":
				$markup = $this->input->post('markup');
				$value = $value * ($markup + 100) / 100;

				$data['markup'] = $markup;
				$data['value'] = $value;
				$data['quantity'] = $quantity;
				$data['unit'] = $unit;
				break;
			case "equipment":
				$data['hours'] = $hours;
				$data['quantity'] = $quantity;
				break;
			case "ocasional":
				$markup = $this->input->post('markup');
				$value = $value * ($markup + 100) / 100;

				$data['markup'] = $markup;
				$data['value'] = $value;

				$data['hours'] = $hours;
				$data['quantity'] = $quantity;
				$data['unit'] = $unit;
				break;
			case "hold_back":
				break;
		}

		$this->db->where('id_workorder_' . $formType, $hddId);
		$query = $this->db->update('workorder_' . $formType, $data);

		//update expenses if exist
		if($log['old'][0]['flag_expenses'] == 1){
			$data = array(
				'expense_value' => $value
			);
			$this->db->where('fk_id_workorder', $idWorkorder);
			$this->db->where('fk_id_submodule', $hddId);
			$this->db->where('submodule', $formType);
			$query = $this->db->update('workorder_expense', $data);
	
		}

		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
			->type('workorder_' . $formType) //Entry type like, Post, Page, Entry
			->id($idWorkorder) //Entry ID
			->token('update') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add info boton go back
	 * @since 11/11/2018
	 */
	public function saveInfoGoBack($arrData)
	{
		$idUser = $this->session->userdata("id");

		//delete datos anteriores del usuario
		$this->db->delete('workorder_go_back', array('fk_id_user' => $idUser));

		$data = array(
			'fk_id_user' => $idUser,
			'post_from' => $arrData["from"],
			'post_to' => $arrData["to"]
		);

		if (array_key_exists("jobId", $arrData)) {
			$data['post_id_job'] = $arrData["jobId"];
		}
		if (array_key_exists("idWorkOrder", $arrData)) {
			$data['post_id_work_order'] = $arrData["idWorkOrder"];
		}
		if (array_key_exists("idWorkOrderFrom", $arrData)) {
			$data['post_id_wo_from'] = $arrData["idWorkOrderFrom"];
		}
		if (array_key_exists("idWorkOrderTo", $arrData)) {
			$data['post_id_wo_to'] = $arrData["idWorkOrderTo"];
		}
		if (array_key_exists("state", $arrData)) {
			$data['post_state'] = $arrData["state"];
		}

		$query = $this->db->insert('workorder_go_back', $data);
	}

	/**
	 * Work Order info go back
	 * @since 11/11/2018
	 */
	public function get_workorder_go_back()
	{
		$idUser = $this->session->userdata("id");

		$this->db->where('fk_id_user', $idUser);

		$query = $this->db->get('workorder_go_back');

		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder additional info
	 * @since 11/1/2020
	 */
	public function get_workorder_state($idWorkorder)
	{
		$this->db->select("W.*, U.first_name");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$this->db->order_by('W.id_workorder_state', 'desc');
		$query = $this->db->get('workorder_state W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add workorder state
	 * @since 11/1/2020
	 */
	public function add_workorder_state($arrData)
	{
		$this->load->library('logger');

		$idUser = $this->session->userdata("id");

		$idWorkorder = $arrData["idWorkorder"];

		$data = array(
			'fk_id_workorder' => $idWorkorder,
			'fk_id_user' => $idUser,
			'date_issue' => date("Y-m-d G:i:s"),
			'observation' => $arrData["observation"],
			'state' => $arrData["state"]
		);

		$query = $this->db->insert('workorder_state', $data);

		$log['old'] = null;
		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
			->type('workorder_state') //Entry type like, Post, Page, Entry
			->id($idWorkorder) //Entry ID
			->token('insert') //Token identify Action
			->comment(json_encode($log))
			->log(); //Add Database Entry

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Contar registros de workorder
	 * Int idJob
	 * Int Year
	 * Int Estado
	 * @author BMOTTAG
	 * @since  7/2/2020
	 */
	public function countWorkorders($arrDatos)
	{
		$sql = "SELECT count(id_workorder) CONTEO";
		$sql .= " FROM workorder W";
		$sql .= " WHERE 1=1";

		//filtrar por JOB ID
		if (array_key_exists("idJob", $arrDatos)) {
			$sql .= " AND fk_id_job = " . $arrDatos["idJob"];
		} elseif (array_key_exists("year", $arrDatos)) {
			$year = $arrDatos["year"];
			$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //primer dia del año
			//$lastDay = date('Y-m-d',(mktime(0,0,0,13,1,$year)-1));//ultimo dia del año
			$lastDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year + 1)); //primer dia del siguiente año para que incluya todo el dia anterior en la consulta

			$sql .= " AND date >= '$firstDay'";
			$sql .= " AND date <= '$lastDay'";
		} else { //si no envia año entonces selecciono el año actual
			$year = date('Y');
			$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //primer dia del año
			$sql .= " AND date >= '$firstDay'";
		}

		if (array_key_exists("from", $arrDatos) && $arrDatos["from"] != '') {
			$sql .= " AND date >= '" . $arrDatos["from"] . "'";
		}
		if (array_key_exists("to", $arrDatos) && $arrDatos["to"] != '' && $arrDatos["from"] != '') {
			$sql .= " AND date <= '" . $arrDatos["to"] . "'";
		}

		//filtrar por estado
		if (array_key_exists("state", $arrDatos)) {
			$sql .= " AND state =" . $arrDatos["state"];
		}

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->CONTEO;
	}

	/**
	 * Sumatoria de horas de personal
	 * Int idJob
	 * @author BMOTTAG
	 * @since  10/2/2020
	 */
	public function countHoursPersonal($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(hours),2) TOTAL";
		$sql .= " FROM workorder_personal P";
		$sql .= " INNER JOIN workorder W on W.id_workorder = P.fk_id_workorder";
		$sql .= " WHERE W.fk_id_job =" . $arrDatos["idJob"];
		if (array_key_exists("from", $arrDatos) && $arrDatos["from"] != '') {
			$sql .= " AND date >= '" . $arrDatos["from"] . "'";
		}
		if (array_key_exists("to", $arrDatos) && $arrDatos["to"] != '' && $arrDatos["from"] != '') {
			$sql .= " AND date <= '" . $arrDatos["to"] . "'";
		}
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Sumatoria de valores para la WO
	 * Int idJob
	 * Var table
	 * @author BMOTTAG
	 * @since  10/2/2020
	 */
	public function countIncome($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(value),2) TOTAL";
		$sql .= " FROM " . $arrDatos["table"] . " P";
		$sql .= " INNER JOIN workorder W on W.id_workorder = P.fk_id_workorder";
		$sql .= " WHERE W.fk_id_job =" . $arrDatos["idJob"];
		if (array_key_exists("idWorkOrder", $arrDatos)) {
			$sql .= " AND P.fk_id_workorder =" . $arrDatos["idWorkOrder"];
		}
		if (array_key_exists("from", $arrDatos) && $arrDatos["from"] != '') {
			$sql .= " AND date >= '" . $arrDatos["from"] . "'";
		}
		if (array_key_exists("to", $arrDatos) && $arrDatos["to"] != '' && $arrDatos["from"] != '') {
			$sql .= " AND date <= '" . $arrDatos["to"] . "'";
		}

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Informacion del foreman
	 * @since 4/6/2020
	 */
	public function info_foreman($idForeman)
	{
		$data = array(
			'foreman_name' => $this->input->post('foreman'),
			'foreman_movil_number' => $this->input->post('movilNumber'),
			'foreman_email' => $this->input->post('email')
		);

		//revisar si es para adicionar o editar
		if ($idForeman == '') {
			$data['fk_id_job'] = $this->input->post('jobName');
			$data['fk_id_param_company'] = $this->input->post('company');
			$query = $this->db->insert('param_company_foreman', $data);
		} else {
			$this->db->where('id_company_foreman', $idForeman);
			$query = $this->db->update('param_company_foreman', $data);
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Prices for workorder personal
	 * @since 13/1/2017
	 */
	public function get_workorder_personal_prices($idWorkorder, $idJob)
	{
		$this->db->select("W.*, T.job_employee_type_unit_price");
		$this->db->join('job_employee_type_price T', 'T.fk_id_employee_type = W.fk_id_employee_type', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$this->db->where('T.fk_id_job ', $idJob);
		$query = $this->db->get('workorder_personal W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update WO personal rate and value
	 * @since 7/11/2020
	 */
	public function update_wo_personal_rate($workorderPersonalRate)
	{
		$idWO = $this->input->post('identificador');

		//add the new employee types
		$query = 1;
		if ($workorderPersonalRate) {
			$tot = count($workorderPersonalRate);
			for ($i = 0; $i < $tot; $i++) {
				$rate = $workorderPersonalRate[$i]['job_employee_type_unit_price'];
				$hours = $workorderPersonalRate[$i]['hours'];
				$value = $rate * $hours;

				$data = array(
					'rate' => $rate,
					'value' => $value
				);
				$this->db->where('id_workorder_personal  ', $workorderPersonalRate[$i]['id_workorder_personal']);
				$query = $this->db->update('workorder_personal', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Prices for workorder equipment
	 * @since 10/11/2020
	 */
	public function get_workorder_equipment_prices($idWorkorder, $idJob)
	{
		$this->db->select("W.*, T.job_equipment_unit_price, T.job_equipment_without_driver");
		$this->db->join('job_equipment_price T', 'T.fk_id_equipment = W.fk_id_vehicle', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$this->db->where('W.fk_id_type_2 !=', 8); //los equipos tipo miscelaneo no se les coloca precio
		$this->db->where('T.fk_id_job ', $idJob);
		$query = $this->db->get('workorder_equipment W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update WO equipment rate and value
	 * @since 10/11/2020
	 */
	public function update_wo_equipment_rate($workorderEquipmentRate)
	{
		$idWO = $this->input->post('identificador');

		//add the new employee types
		$query = 1;
		if ($workorderEquipmentRate) {
			$tot = count($workorderEquipmentRate);
			for ($i = 0; $i < $tot; $i++) {
				//verificar si esta en STANBY para saber que rate coger
				$standby = $workorderEquipmentRate[$i]['standby'];
				if ($standby == 1) {
					$rate = $workorderEquipmentRate[$i]['job_equipment_without_driver'];
				} else {
					$rate = $workorderEquipmentRate[$i]['job_equipment_unit_price'];
				}

				$hours = $workorderEquipmentRate[$i]['hours'];

				$quantity = $workorderEquipmentRate[$i]['quantity'];
				$quantity = $quantity == 0 ? 1 : $quantity;

				$value = $rate * $quantity * $hours;

				$data = array(
					'rate' => $rate,
					'value' => $value
				);
				$this->db->where('id_workorder_equipment', $workorderEquipmentRate[$i]['id_workorder_equipment']);
				$query = $this->db->update('workorder_equipment', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Prices for workorder material
	 * @since 16/12/2020
	 */
	public function get_workorder_material_prices($idWorkorder)
	{
		$this->db->select("W.*, T.material_price");
		$this->db->join('param_material_type T', 'T.id_material = W.fk_id_material', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$query = $this->db->get('workorder_materials W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update WO material rate and value
	 * @since 18/12/2020
	 */
	public function update_wo_material_rate($workorderMaterialRate)
	{
		$idWO = $this->input->post('identificador');

		//calcular valor y actualizar campos
		$query = 1;
		if ($workorderMaterialRate) {
			$tot = count($workorderMaterialRate);
			for ($i = 0; $i < $tot; $i++) {
				$rate = $workorderMaterialRate[$i]['material_price'];
				$quantity = $workorderMaterialRate[$i]['quantity'];
				$value = $rate * $quantity;

				$data = array(
					'rate' => $rate,
					'value' => $value
				);
				$this->db->where('id_workorder_materials  ', $workorderMaterialRate[$i]['id_workorder_materials']);
				$query = $this->db->update('workorder_materials', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update state and last message
	 * @since 23/12/2020
	 */
	public function update_workorder($arrData)
	{
		$data = array(
			'state' => $arrData["state"],
			'last_message' => $arrData["lastMessage"]
		);
		$this->db->where('id_workorder', $arrData["idWorkorder"]);
		$query = $this->db->update('workorder', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get workorder Receipt info
	 * @since 4/1/2021
	 */
	public function get_workorder_receipt($arrData)
	{
		$this->db->select();
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		if (array_key_exists("flag_expenses", $arrData)) {
			$this->db->where('W.flag_expenses', 0);
		}
		$this->db->order_by('W.place', 'asc');
		$query = $this->db->get('workorder_receipt W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add Invoice
	 * @since 4/1/2021
	 */
	public function saveReceipt()
	{
		$this->load->library('logger');
		$this->load->model("general_model");
		$idWorkorder = $this->input->post('hddidWorkorder');

		$idWOReceipt = $this->input->post('hddId');
		$price = $this->input->post('price');
		$checkPDF = $this->input->post('check_pdf');

		if ($checkPDF) {
			$checkPDF = 1;
		} else {
			$checkPDF = 2;
		}

		if (!$price) {
			$price = 0;
		}

		$data = array(
			'place' => $this->input->post('place'),
			'price' => $price,
			'description' => $this->input->post('description')
		);

		//revisar si es para adicionar o editar
		if ($idWOReceipt == '') {
			$value = $price / 1.05;

			$data['fk_id_workorder'] = $idWorkorder;
			$data['markup'] = 0;
			$data['view_pdf'] = 1;
			$data['value'] = $value;
			$query = $this->db->insert('workorder_receipt', $data);

			$log['old'] = null;
			$log['new'] = json_encode($data);

			$this->logger
				->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
				->type('workorder_receipt') //Entry type like, Post, Page, Entry
				->id($idWorkorder) //Entry ID
				->token('insert') //Token identify Action
				->comment(json_encode($log))
				->log(); //Add Database Entry
		} else {
			$markup = $this->input->post('markup');

			$arrParam = array(
				"table" => "workorder_receipt",
				"order" => "fk_id_workorder",
				"column" => "fk_id_workorder",
				"id" => $idWorkorder
			);
			$log['old'] = $this->general_model->get_basic_search($arrParam);

			$price = $price / 1.05; //quitar el 5% de GST
			$value = $price + ($price * $markup / 100); //valor con el markup

			$data['markup'] = $markup;
			$data['value'] = $value;
			$data['view_pdf'] = $checkPDF;
			$this->db->where('id_workorder_receipt', $idWOReceipt);
			$query = $this->db->update('workorder_receipt', $data);

			$log['new'] = json_encode($data);

			$this->logger
				->user($this->session->userdata("id")) //$this->session->userdata("id");//Set UserID, who created this  Action
				->type('workorder_receipt') //Entry type like, Post, Page, Entry
				->id($idWorkorder) //Entry ID
				->token('update') //Token identify Action
				->comment(json_encode($log))
				->log(); //Add Database Entry

		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update WO INVOICE markup and value
	 * @since 4/1/2021
	 */
	public function update_wo_invoice_markup($workorderReceipt, $markup)
	{
		//calcular valor y actualizar campos
		$query = 1;
		if ($workorderReceipt) {
			$tot = count($workorderReceipt);
			for ($i = 0; $i < $tot; $i++) {
				$price = $workorderReceipt[$i]['price'];

				$price = $price / 1.05; //quitar el 5% de GST
				$value = $price + ($price * $markup / 100); //valor con el markup

				$data = array(
					'markup' => $markup,
					'value' => $value
				);
				$this->db->where('id_workorder_receipt  ', $workorderReceipt[$i]['id_workorder_receipt']);
				$query = $this->db->update('workorder_receipt', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update WO MATERIAL markup and value
	 * @since 5/1/2021
	 */
	public function update_wo_material_markup($workorderMaterials, $markup)
	{
		//calcular valor y actualizar campos
		$query = 1;
		if ($workorderMaterials) {
			$tot = count($workorderMaterials);
			for ($i = 0; $i < $tot; $i++) {
				$rate = $workorderMaterials[$i]['rate'];
				$quantity = $workorderMaterials[$i]['quantity'];

				$value = $rate * $quantity;

				$value = $value * ($markup + 100) / 100;

				$data = array(
					'markup' => $markup,
					'value' => $value
				);
				$this->db->where('id_workorder_materials', $workorderMaterials[$i]['id_workorder_materials']);
				$query = $this->db->update('workorder_materials', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update WO OCASIONAL markup and value
	 * @since 5/1/2021
	 */
	public function update_wo_ocasional_markup($workorderOcasional, $markup)
	{
		//calcular valor y actualizar campos
		$query = 1;
		if ($workorderOcasional) {
			$tot = count($workorderOcasional);
			for ($i = 0; $i < $tot; $i++) {
				$quantity = $workorderOcasional[$i]['quantity'];
				$hours = $workorderOcasional[$i]['hours'];
				$rate = $workorderOcasional[$i]['rate'];

				$value = $rate * $quantity * $hours;

				$value = $value * ($markup + 100) / 100;

				$data = array(
					'markup' => $markup,
					'value' => $value
				);
				$this->db->where('id_workorder_ocasional', $workorderOcasional[$i]['id_workorder_ocasional']);
				$query = $this->db->update('workorder_ocasional', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update WO state
	 * @since 12/1/2021
	 */
	public function updateWOState()
	{
		$idUser = $this->session->userdata("id");
		$state = $this->input->post('state');
		$information = $this->input->post('information');

		//update states
		$query = 1;
		if ($wo = $this->input->post('wo')) {
			$tot = count($wo);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_workorder' => $wo[$i],
					'fk_id_user' => $idUser,
					'date_issue' => date("Y-m-d G:i:s"),
					'observation' => $information,
					'state' => $state
				);

				$query = $this->db->insert('workorder_state', $data);


				//actualizo la tabla de WO
				$data = array(
					'state' => $state,
					'last_message' => $information
				);
				$this->db->where('id_workorder', $wo[$i]);
				$query = $this->db->update('workorder', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get workorder expenses info
	 * @since 13/1/2023
	 */
	public function get_workorder_expense($arrData)
	{
		$this->db->join('job_details J', 'J.id_job_detail = W.fk_id_job_detail', 'INNER');
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		$query = $this->db->get('workorder_expense W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add expense
	 * @since 13/1/2023
	 */
	public function saveExpense()
	{
		//delete Expenses
		$idWorkorder = $this->input->post('hddidWorkorder');
		$this->db->delete('workorder_expense', array('fk_id_workorder' => $idWorkorder));

		//add the new expenses
		$query = 1;
		if ($expenses = $this->input->post('expense')) {
			$tot = count($expenses);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_workorder' => $idWorkorder,
					'fk_id_job_detail' => $expenses[$i]
				);
				$query = $this->db->insert('workorder_expense', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update WO Expenses Values
	 * @since 21/1/2023
	 */
	public function updateExpensesValues($workorderExpenses, $totalWOIncome, $sumPercentageExpense)
	{
		$query = 1;

		if ($workorderExpenses) {
			$tot = count($workorderExpenses);
			for ($i = 0; $i < $tot; $i++) {
				$expenseValue = $workorderExpenses[$i]["percentage"] * $totalWOIncome / $sumPercentageExpense;

				$data = array(
					'expense_value' => $expenseValue
				);
				$this->db->where('id_workorder_expense', $workorderExpenses[$i]["id_workorder_expense"]);
				$query = $this->db->update('workorder_expense', $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sumatoria de valores de porcentage para los gastos guardados
	 * Int idWorkOrder
	 * @author BMOTTAG
	 * @since  24/1/2023
	 */
	public function sumPercentageExpense($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(percentage),2) TOTAL";
		$sql .= " FROM workorder_expense W";
		$sql .= " INNER JOIN job_details J on J.id_job_detail = W.fk_id_job_detail";
		$sql .= " WHERE W.fk_id_workorder =" . $arrDatos["idWorkOrder"];
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Work Order Log
	 * @since 20/02/2024
	 */
	public function get_workorder_log($arrData)
	{
		$this->db->select("L.*, CONCAT(first_name, ' ', last_name) name, J.job_description");
		$this->db->join('user U', 'U.id_user = L.created_by', 'INNER');
		$this->db->join('workorder W', 'L.type_id = W.id_workorder', 'LEFT');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');

		$parameters = array('workorder', 'workorder_state', 'workorder_personal', 'workorder_materials', 'workorder_equipment', 'workorder_receipt', 'workorder_ocasional');
		$this->db->where_in('L.type', $parameters);

		if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 0) {
			$this->db->where('J.id_job', $arrData["jobId"]);
		}

		if (array_key_exists("idWorkOrder", $arrData) && $arrData["idWorkOrder"] != '' && $arrData["idWorkOrder"] != 0) {
			$this->db->where('L.type_id', $arrData["idWorkOrder"]);
		}

		if (array_key_exists("userId", $arrData) && $arrData["userId"] != '' && $arrData["userId"] != 0) {
			$this->db->where('L.created_by', $arrData["userId"]);
		}

		if (array_key_exists("from", $arrData) && $arrData["from"] != '') {
			$this->db->where('L.created_on >=', $arrData["from"]);
		}

		if (array_key_exists("to", $arrData) && $arrData["to"] != '' && $arrData["from"] != '') {
			$this->db->where('L.created_on <=', $arrData["to"]);
		}
		$this->db->order_by('L.id', 'asc');
		$query = $this->db->get('logger L');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Save WO Expenses
	 * @since 24/03/2024
	 */
	public function saveWOExpenses()
	{
		$query = 1;
		$idWorkOrder = $this->input->post('hddidWorkorder');
		$idJobDetail = $this->input->post('item_job_detail');
		if ($item = $this->input->post('item')) {
			$tot = count($item);
			for ($i = 0; $i < $tot; $i++) {
				$parts = explode('__', $item[$i]);

				$data = array(
					'fk_id_workorder' => $idWorkOrder,
					'fk_id_job_detail' => $idJobDetail,
					'submodule' => $parts[0],
					'fk_id_submodule' => $parts[1],
					'expense_value' => $parts[2]
				);
				$query = $this->db->insert('workorder_expense', $data);

				//UPDATE SUBMODULE
				$data = array('flag_expenses' => 1);
				$this->db->where('id_workorder_' . $parts[0], $parts[1]);
				$query = $this->db->update('workorder_' . $parts[0], $data);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete Expenses
	 * @since 2/05/2024
	 */
	public function deleteExpenses($arrDatos)
	{
		$this->db->where(array(
			'fk_id_workorder' => $arrDatos['fk_id_workorder'],
			'submodule' => $arrDatos['submodule'],
			'fk_id_submodule' => $arrDatos['fk_id_submodule']
		));
		$query = $this->db->delete('workorder_expense');
		
		return $query ? true : false;
	}

	/**
	 * Clone workorder to create Accounting Control Sheet (ACS)
	 * @since 09/01/2025
	 */
	public function clone_workorder($arrData)
	{
		$data = array(
			'fk_id_workorder' => $arrData['workorder'][0]['id_workorder'],
			'fk_id_user' => $arrData['workorder'][0]['fk_id_user'],
			'fk_id_job' => $arrData['workorder'][0]['fk_id_job'],
			'date_issue' => date("Y-m-d G:i:s"),
			'date' => $arrData['workorder'][0]['date'],
			'fk_id_company' => $arrData['workorder'][0]['fk_id_company'],
			'foreman_name_wo' => $arrData['workorder'][0]['foreman_name_wo'],
			'foreman_movil_number_wo' => $arrData['workorder'][0]['foreman_movil_number_wo'],
			'foreman_email_wo' => $arrData['workorder'][0]['foreman_email_wo'],
			'observation' => $arrData['workorder'][0]['observation'],
			'state' => $arrData['workorder'][0]['state'],
			'last_message' => $arrData['workorder'][0]['last_message'],
		);
		$query = $this->db->insert('acs', $data);
		$idACS = $this->db->insert_id();

		//Insert Personal
        if (!empty($arrData['workorderPersonal'])) {
            foreach ($arrData['workorderPersonal'] as $personal) {
                $data = array(
                    'fk_id_acs' => $idACS, 
                    'fk_id_user' => $personal['fk_id_user'],
                    'fk_id_employee_type' => $personal['fk_id_employee_type'],
                    'hours' => $personal['hours'],
					'rate' => $personal['rate'],
					'value' => $personal['value'],
                    'description' => $personal['description'],
					'view_pdf' => $personal['view_pdf']
                );
                $this->db->insert('acs_personal', $data);
            }
        }

		//Insert Material
        if (!empty($arrData['workorderMaterials'])) {
            foreach ($arrData['workorderMaterials'] as $material) {
                $data = array(
                    'fk_id_acs' => $idACS, 
                    'fk_id_material' => $material['fk_id_material'],
                    'quantity' => $material['quantity'],
                    'unit' => $material['unit'],
					'rate' => $material['rate'],
					'markup' => $material['markup'],
                    'value' => $material['value'],
					'description' => $material['description'],
					'view_pdf' => $material['view_pdf']
                );
                $this->db->insert('acs_materials', $data);
            }
        }

		//Insert Receipt
        if (!empty($arrData['workorderReceipt'])) {
            foreach ($arrData['workorderReceipt'] as $receipt) {
                $data = array(
                    'fk_id_acs' => $idACS, 
                    'place' => $receipt['place'],
                    'price' => $receipt['price'],
                    'markup' => $receipt['markup'],
                    'value' => $receipt['value'],
					'description' => $receipt['description'],
					'view_pdf' => $receipt['view_pdf']
                );
                $this->db->insert('acs_receipt', $data);
            }
        }

		//Insert Equipment
        if (!empty($arrData['workorderEquipment'])) {
            foreach ($arrData['workorderEquipment'] as $equipment) {
                $data = array(
                    'fk_id_acs' => $idACS, 
                    'fk_id_type_2' => $equipment['fk_id_type_2'],
                    'fk_id_vehicle' => $equipment['fk_id_vehicle'],
                    'fk_id_attachment' => $equipment['fk_id_attachment'],
					'fk_id_company' => $equipment['fk_id_company'],
					'other' => $equipment['other'],
                    'operatedby' => $equipment['operatedby'],
                    'hours' => $equipment['hours'],
                    'quantity' => $equipment['quantity'],
					'rate' => $equipment['rate'],
					'standby' => $equipment['standby'],
                    'value' => $equipment['value'],
					'description' => $equipment['description'],
					'view_pdf' => $equipment['view_pdf']
                );
                $this->db->insert('acs_equipment', $data);
            }
        }

		//Insert Ocasional
        if (!empty($arrData['workorderOcasional'])) {
            foreach ($arrData['workorderOcasional'] as $ocasional) {
                $data = array(
                    'fk_id_acs' => $idACS, 
                    'fk_id_company' => $ocasional['fk_id_company'],
                    'equipment' => $ocasional['equipment'],
                    'quantity' => $ocasional['quantity'],
                    'unit' => $ocasional['unit'],
                    'hours' => $ocasional['hours'],
                    'rate' => $ocasional['rate'],
                    'markup' => $ocasional['markup'],
                    'value' => $ocasional['value'],
					'contact' => $ocasional['contact'],
					'description' => $ocasional['description'],
					'view_pdf' => $ocasional['view_pdf']
                );
                $this->db->insert('acs_ocasional', $data);
            }
        }

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit Subcontractors Invoices
	 * @since 13/02/2025
	 */
	public function saveSubcontractorInvoice($archivo) 
	{
		$idSubcontractorInvoice = $this->input->post('hddIdentificador');
		$idSubcontractor = $this->input->post('company');

		if($idSubcontractor == ""){
			$data = array(
				'company_name' => addslashes($this->security->xss_clean($this->input->post('subcontractor_name'))),
				'company_type' => 3,
				'contact' => addslashes($this->security->xss_clean($this->input->post('subcontractor_contact'))),
				'movil_number' => addslashes($this->security->xss_clean($this->input->post('subcontractor_mobile_number'))),
				'email' => addslashes($this->security->xss_clean($this->input->post('subcontractor_email'))),
				'does_hauling' => 2
			);

			$query = $this->db->insert('param_company', $data);	
			$idSubcontractor = $this->db->insert_id();		
		}

		$data = array(
			'date_issue' => date("Y-m-d"),
			'fk_id_company' => $idSubcontractor,
			'invoice_number' => addslashes($this->security->xss_clean($this->input->post('invoice_number'))),
			'invoice_amount' => addslashes($this->security->xss_clean($this->input->post('amount')))
		);

		if ($archivo != 'xxx') {
			$data['file'] = $archivo;
		}
		
		//revisar si es para adicionar o editar
		if ($idSubcontractorInvoice == '') {
			$query = $this->db->insert('subcontractor_invoice', $data);	
			$idSubcontractorInvoice = $this->db->insert_id();			
		} else {
			$this->db->where('id_subcontractor_invoice', $idSubcontractorInvoice);
			$query = $this->db->update('subcontractor_invoice', $data);
		}
		if ($query) {
			return $idSubcontractorInvoice;
		} else {
			return false;
		}
	}

	/**
	 * Subcontractors list
	 * las 50 records
	 * @since 12/1/2017
	 */
	public function get_subcontractors_invoice($arrDatos)
	{
		$this->db->select('
			S.*, 
			C.company_name AS company,
			(
				SELECT GROUP_CONCAT(DISTINCT O.fk_id_workorder)
				FROM workorder_ocasional O
				WHERE O.fk_id_subcontractor_invoice = S.id_subcontractor_invoice
			) AS related_workorders,
			(
				SELECT COALESCE(SUM(O.value), 0)
				FROM workorder_ocasional O
				WHERE O.fk_id_subcontractor_invoice = S.id_subcontractor_invoice
			) AS total_workorder_value
		');
		$this->db->join('param_company C', 'C.id_company = S.fk_id_company', 'INNER');

		if (array_key_exists("idSubcontractorInvoice", $arrDatos)) {
			$this->db->where('id_subcontractor_invoice', $arrDatos["idSubcontractorInvoice"]);
		}
		if (array_key_exists("idCompany", $arrDatos)) {
			$this->db->where('S.fk_id_company', $arrDatos["idCompany"]);
		}
		$this->db->order_by('id_subcontractor_invoice', 'desc');
		$query = $this->db->get('subcontractor_invoice S', 50);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

}