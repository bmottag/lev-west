<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Programming_model extends CI_Model
{


	/**
	 * Add/Edit PROGRAMMING
	 * @since 2/7/2018
	 */
	public function saveProgramming()
	{
		$this->load->model("general_model");

		$idUser = $this->session->userdata("id");
		$idProgramming = $this->input->post('hddId');
		//$flagDate = $this->input->post('flag_date');
		$parentId = $this->input->post('hddIdParent');

		$flagDate = ($this->input->post('job_planning') == 1) ? $this->input->post('flag_date') : 1;

		$data = array(
			'fk_id_job' => $this->input->post('jobName'),
			'observation' => $this->input->post('observation'),
			'flag_date' => $flagDate
		);

		if ($flagDate == 2 && $parentId == "") {
			$data['date_programming'] = formatear_fecha($this->input->post('from'));
			$data['date_to'] = formatear_fecha($this->input->post('to'));
			$data['apply_for'] = $this->input->post('apply_for');
		} else {
			$data['date_programming'] = $this->input->post('date');
		}

		//revisar si es para adicionar o editar
		if ($idProgramming == '') {
			$data['fk_id_user'] = $idUser;
			$data['date_issue'] = date("Y-m-d G:i:s");
			$data['state'] = 1;

			$query = $this->db->insert('programming', $data);
			$idProgramming = $this->db->insert_id();
		} else {
			$this->db->where('id_programming', $idProgramming);
			$query = $this->db->update('programming', $data);

			//actualiza la WO
			$arrParam = array(
				"table" => "programming",
				"order" => "id_programming",
				"column" => "id_programming",
				"id" => $idProgramming
			);
			$result = $this->general_model->get_basic_search($arrParam);
			$fk_id_workorder = $result[0]['fk_id_workorder'];

			if ($fk_id_workorder) {
				$dataWO = array(
					'fk_id_job' => $data["fk_id_job"],
					'observation' =>  $data["observation"],
					'date' => $data["date_programming"],
					'date_issue' => $data["date_programming"] . ' ' . date("G:i:s"),
				);

				$this->db->where('id_workorder', $fk_id_workorder);
				$this->db->update('workorder', $dataWO);
			}
		}
		if ($query) {
			return $idProgramming;
		} else {
			return false;
		}
	}

	/**
	 * Add Period Programming
	 * @since 16/10/2023
	 */
	public function savePeriodProgramming($date, $idParent)
	{
		$data = array(
			'fk_id_user' => $this->session->userdata("id"),
			'date_issue' => date("Y-m-d G:i:s"),
			'fk_id_job' => $this->input->post('jobName'),
			'observation' => $this->input->post('observation'),
			'date_programming' => $date,
			'parent_id' => $idParent,
			'apply_for' => $this->input->post('apply_for'),
			'flag_date' => $this->input->post('flag_date'),
			'state' => 1
		);
		$query = $this->db->insert('programming', $data);
		$idProgramming = $this->db->insert_id();

		if ($query) {
			return $idProgramming;
		} else {
			return false;
		}
	}

	/**
	 * Add PROGRAMMING WORKER
	 * @since 16/1/2019
	 */
	public function addProgrammingWorker()
	{
		//add the new workers
		$query = 1;
		if ($workers = $this->input->post('workers')) {
			$tot = count($workers);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_programming' => $this->input->post('hddId'),
					'fk_id_programming_user' => $workers[$i],
					'fk_id_employee_type' => 1, // Se coloca por defecto que es Labour
					'fk_id_hour' => 15, // Se coloca por defecto que ingresen a las 7 am
					'site' => 1 // Se coloca por defecto 1 -> At the yard
				);
				$query = $this->db->insert('programming_worker', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Verify if the project already exist for that date
	 * @author BMOTTAG
	 * @since  15/1/2019
	 */
	public function verifyProject($arrData)
	{
		$this->db->where('fk_id_job', $arrData["idJob"]);
		$this->db->where('date_programming', $arrData["date"]);
		$this->db->where('state !=', 3);

		$query = $this->db->get("programming");

		if ($query->num_rows() >= 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Cmabia el estado de la programacion
	 * @since 8/7/2018
	 */
	public function deleteProgramming()
	{
		$idProgramming = $this->input->post('identificador');

		$data = array('state' => 3); //estado eliminada

		$this->db->where('id_programming', $idProgramming);
		$query = $this->db->update('programming', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Lista de vehiculos, para asginarlos a los trabajadores en la programacion
	 * @since 16/1/2019
	 */
	public function get_vehicles_inspection($arrData)
	{
		$trucks = array();
		$sql = "SELECT id_vehicle, CONCAT(unit_number,' -----> ', description) as unit_description 
						FROM param_vehicle V 
						INNER JOIN param_vehicle_type_2 T ON T.id_type_2 = V.type_level_2 
						WHERE fk_id_company = 1 
						AND T.link_inspection != 'NA' AND V.id_vehicle NOT IN(41,42,43,44,61,62) AND V.state = 1 AND V.so_blocked = 1";
		if (!empty($arrData["vehicleToExclude"])) {
			// Convertir el array de IDs a un string separado por comas
			$excludedIds = implode(",", $arrData["vehicleToExclude"]);
			$sql .= " AND V.id_vehicle NOT IN ($excludedIds)";
		}
		$sql .= " ORDER BY unit_number";

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
	 * Lista de equipment ID para una fecha
	 * @since 17/02/2025
	 */
	public function get_vehicles_selected($filters)
	{
		$sql = "SELECT fk_id_machine FROM programming_worker W 
				INNER JOIN programming P ON P.id_programming = W.fk_id_programming 
				WHERE P.id_programming != ? AND P.date_programming = ? AND fk_id_machine IS NOT NULL";
		$query = $this->db->query($sql, array($filters[0]['id_programming'], $filters[0]['date_programming']));

		$trucks = []; // Inicializar array vacío para evitar errores

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				// Decodificar el JSON para obtener los IDs de las máquinas
				$machines = json_decode($row->fk_id_machine, true);
				if (is_array($machines)) {
					$trucks = array_merge($trucks, $machines);
				}
			}
		}

		return $trucks; // Retorna solo los IDs de las máquinas
	}

	/**
	 * Update worker
	 * @since 16/1/2019
	 */
	public function saveWorker()
	{
		$hddId = $this->input->post('hddId');

		$maquina = NULL;
		if (!empty($this->input->post('machine'))) {
			$maquina = '[' . implode(",", $this->input->post('machine')) . ']';
		}

		$data = array(
			'fk_id_employee_type' => $this->input->post('type'),
			'description' => $this->input->post('description'),
			'fk_id_machine' => $maquina,
			'fk_id_hour' => $this->input->post('hora_inicio'),
			'site' => $this->input->post('site'),
			'safety' => $this->input->post('safety'),
			'creat_wo' => $this->input->post('creat_wo')
		);

		$this->db->where('id_programming_worker', $hddId);
		$query = $this->db->update('programming_worker', $data);

		//actualiza la WO
		$arrParam = array(
			"table" => "workorder_personal",
			"order" => "fk_id_programming_worker",
			"column" => "fk_id_programming_worker",
			"id" => $hddId
		);
		$result = $this->general_model->get_basic_search($arrParam);
		if ($result) {
			$dataWO = array(
				'fk_id_employee_type' => $this->input->post('type'),
				'description' => $this->input->post('description'),
			);

			$this->db->where('fk_id_programming_worker', $hddId);
			$this->db->update('workorder_personal', $dataWO);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Contar trabajadors para una programacion
	 * @since  17/1/2019
	 */
	public function countWorkers($idProgramming)
	{
		$sql = "SELECT count(id_programming_worker) CONTEO";
		$sql .= " FROM programming_worker P";
		$sql .= " WHERE fk_id_programming = " . $idProgramming;

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->CONTEO;
	}

	/**
	 * Save one worker
	 * @since 19/1/2019
	 */
	public function saveOneWorkerProgramming()
	{
		$this->load->model("general_model");
		$idProgramming = $this->input->post('hddId');

		$data = array(
			'fk_id_programming' => $idProgramming,
			'fk_id_programming_user' => $this->input->post('worker'),
			'fk_id_employee_type' => 1, // Se coloca por defecto que es Labour
			'fk_id_hour' => 15, // Se coloca por defecto que ingresen a las 7 am
			'site' => 1 // Se coloca por defecto 1 -> At the yard
		);

		$query = $this->db->insert('programming_worker', $data);

		$id_programming_worker = $this->db->insert_id();

		//actualiza la WO
		$arrParam = array(
			"table" => "programming",
			"order" => "id_programming",
			"column" => "id_programming",
			"id" => $idProgramming
		);
		$result = $this->general_model->get_basic_search($arrParam);
		$fk_id_workorder = $result[0]['fk_id_workorder'];

		if ($fk_id_workorder) {

			$dataWO = array(
				'fk_id_workorder' => $fk_id_workorder,
				'fk_id_user' => $this->input->post('worker'),
				'fk_id_employee_type' => 1,
				'hours' => 0,
				'fk_id_programming_worker' => $id_programming_worker
			);

			$this->db->insert('workorder_personal', $dataWO);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save worker for Flash Planning 
	 * @since 28/12/2022
	 */
	public function saveWorkerFashPlanning($idProgramming, $idHora)
	{
		$machine = '[' . $this->input->post('machine') . ']';
		$data = array(
			'fk_id_programming' => $idProgramming,
			'fk_id_programming_user' => $this->input->post('worker'),
			'fk_id_employee_type' => 1,
			'fk_id_machine' => $machine,
			'fk_id_hour' => $idHora,
			'site' => 2, // Se coloca por defecto 2 -> At the Site
			'safety' => 1 // Se coloca por defecto FLHA
		);
		$query = $this->db->insert('programming_worker', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save child workers for chields
	 * @since 21/10/2023
	 */
	public function saveChildWorkers($idProgramming, $informationWorker)
	{
		$query = 1;
		$tot = count($informationWorker);
		for ($i = 0; $i < $tot; $i++) {
			$data = array(
				'fk_id_programming' => $idProgramming,
				'fk_id_programming_user' => $informationWorker[$i]["fk_id_programming_user"],
				'fk_id_hour' => $informationWorker[$i]["fk_id_hour"],
				'site' => $informationWorker[$i]["site"],
				'description' => $informationWorker[$i]["description"],
				'fk_id_machine' => $informationWorker[$i]["fk_id_machine"],
				'safety' => $informationWorker[$i]["safety"]
			);
			$query = $this->db->insert('programming_worker', $data);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update worker status
	 * @since 22/10/2023
	 */
	public function updateSMSWorkerStatus($idProgrammingWorker, $smsStatus, $smsSID)
	{
		$data = array(
			'sms_sent' => 1,
			'sms_status' => $smsStatus,
			'sms_sid' => $smsSID
		);

		$this->db->where('id_programming_worker', $idProgrammingWorker);
		$query = $this->db->update('programming_worker', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Clone
	 * @since 28/10/2023
	 */
	public function createClone($infoPlanning)
	{
		$data = array(
			'fk_id_user' => $this->session->userdata("id"),
			'date_issue' => date("Y-m-d G:i:s"),
			'fk_id_job' => $infoPlanning[0]["fk_id_job"],
			'observation' => $infoPlanning[0]["observation"],
			'date_programming' => $this->input->post('date'),
			'parent_id' => "",
			'apply_for' => "",
			'flag_date' => "",
			'state' => 1
		);
		$query = $this->db->insert('programming', $data);
		$idProgramming = $this->db->insert_id();

		if ($query) {
			return $idProgramming;
		} else {
			return false;
		}
	}

	/**
	 * Get programming materials info
	 * @since 20/1/2024
	 */
	public function get_programming_materials($arrData)
	{
		$this->db->select();
		$this->db->join('param_material_type M', 'M.id_material = P.fk_id_material', 'INNER');
		if (array_key_exists("idProgramming", $arrData)) {
			$this->db->where('P.fk_id_programming', $arrData["idProgramming"]);
		}
		$this->db->order_by('M.material', 'asc');
		$query = $this->db->get('programming_material P');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add Material
	 * @since 20/1/2024
	 */
	public function saveMaterial()
	{
		$this->load->model("general_model");
		$data = array(
			'fk_id_programming' => $this->input->post('hddidProgramming'),
			'fk_id_material' => $this->input->post('material'),
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('programming_material', $data);
		$id_programming_materials = $this->db->insert_id();

		//actualiza la WO
		$arrParam = array(
			"table" => "programming",
			"order" => "id_programming",
			"column" => "id_programming",
			"id" => $this->input->post('hddidProgramming')
		);
		$result = $this->general_model->get_basic_search($arrParam);
		$fk_id_workorder = $result[0]['fk_id_workorder'];

		if ($fk_id_workorder) {
			$dataWO = array(
				'fk_id_workorder' => $fk_id_workorder,
				'fk_id_material' => $this->input->post('material'),
				'quantity' => $this->input->post('quantity'),
				'unit' => $this->input->post('unit'),
				'description' => $this->input->post('description'),
				'fk_id_programming_materials' => $id_programming_materials
			);

			$this->db->insert('workorder_materials', $dataWO);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Updated Material
	 * @since 20/1/2024
	 */
	public function updatedMaterial()
	{
		$this->load->model("general_model");
		$hddId = $this->input->post('hddId');
		$data = array(
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'description' => $this->input->post('description')
		);

		$this->db->where('id_programming_material', $hddId);
		$query = $this->db->update('programming_material', $data);

		//actualiza la WO
		$arrParam = array(
			"table" => "workorder_materials",
			"order" => "fk_id_programming_materials",
			"column" => "fk_id_programming_materials",
			"id" => $hddId
		);
		$result = $this->general_model->get_basic_search($arrParam);

		if ($result) {
			$dataWO = array(
				'quantity' => $this->input->post('quantity'),
				'unit' => $this->input->post('unit'),
				'description' => $this->input->post('description')
			);

			$this->db->where('fk_id_programming_materials', $hddId);
			$this->db->update('workorder_materials', $data);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function add_workorder($arrDatos)
	{
		$data = array(
			'fk_id_job' => $arrDatos["idJob"],
			'date' => $arrDatos["date"],
			'fk_id_user' => $arrDatos["idUser"],
			'date_issue' => date("Y-m-d G:i:s"),
			'state' => 0,
			'last_message' => $arrDatos["message"],
			'fk_id_company' => $arrDatos["idCompany"],
			'foreman_name_wo' => $arrDatos["foremanName"],
			'foreman_movil_number_wo' => $arrDatos["foremanMovil"],
			'foreman_email_wo' => $arrDatos["foremanEmail"],
			'observation' =>  $arrDatos["observation"]
		);

		$query = $this->db->insert('workorder', $data);
		$idWorkorder = $this->db->insert_id();

		if ($query) {
			return $idWorkorder;
		} else {
			return false;
		}
	}

	/**
	 * Add workorder state
	 * @since 24/01/2024
	 */
	public function add_workorder_state($arrData)
	{
		$data = array(
			'fk_id_workorder' => $arrData["idWorkorder"],
			'fk_id_user' => $arrData["idUser"],
			'date_issue' => date("Y-m-d G:i:s"),
			'observation' => $arrData["observation"],
			'state' => $arrData["state"]
		);

		$query = $this->db->insert('workorder_state', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Material
	 * @since 13/1/2017
	 */
	public function add_item_workorder($table, $data)
	{
		$this->db->insert($table, $data);
	
		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	/**
	 * Get programming ocasional info
	 * @since 20/2/2017
	 */
	public function get_programming_occasional($arrData)
	{
		$sql = "SELECT P.*, C.company_name, C.does_hauling
				FROM programming_ocasional P
				INNER JOIN param_company C ON C.id_company = P.fk_id_company
				WHERE  (P.fk_id_programming = " . $arrData["idProgramming"] . " OR " . $arrData["idProgramming"] . " IS NULL)
				ORDER BY C.company_name ASC;
				";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add Subcontractor
	 * @since 20/1/2024
	 */
	public function saveOcasional()
	{
		$this->load->model("general_model");
		//se filtra por company_type para que solo se pueda editar los subcontratistas
		$arrParam = array(
			"table" => "param_company",
			"order" => "id_company",
			"column" => "id_company",
			"id" => $this->input->post('company')
		);
		$result = $this->general_model->get_basic_search($arrParam);

		$hauling = $result[0]["does_hauling"];

		if ($hauling == 2) {
			$data = array(
				'fk_id_programming' => $this->input->post('hddidProgramming'),
				'fk_id_company' => $this->input->post('company'),
				'equipment' => $this->input->post('equipment'),
				'quantity' => $this->input->post('quantity'),
				'unit' => $this->input->post('unit'),
				'hours' => $this->input->post('hour'),
				'contact' => $this->input->post('contact'),
				'description' => $this->input->post('description')
			);

			$query = $this->db->insert('programming_ocasional', $data);
		} else if ($hauling == 1) {
			$quantity = $this->input->post('quantity');

			$arrParam = array(
				"table" => "programming",
				"order" => "id_programming",
				"column" => "id_programming",
				"id" => $this->input->post('hddidProgramming')
			);
			$result = $this->general_model->get_basic_search($arrParam);

			$idWorkorder = $result[0]["fk_id_workorder"];
			$fk_id_job = $result[0]["fk_id_job"];

			//$this->load->model("hauling/hauling_model");

			for ($i = 0; $i < $quantity; $i++) {
				$data = array(
					'fk_id_programming' => $this->input->post('hddidProgramming'),
					'fk_id_company' => $this->input->post('company'),
					'equipment' => $this->input->post('equipment'),
					'quantity' => 1,
					'unit' => $this->input->post('unit'),
					'hours' => $this->input->post('hour'),
					'contact' => $this->input->post('contact'),
					'description' => $this->input->post('description')
				);

				$query = $this->db->insert('programming_ocasional', $data);
				//$this->hauling_model->saveHaulingByProgramming($info);
			}
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

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
		$idProgramming = $this->input->post('hddIdProgramming');

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

		$table = 'programming_' . $formType;
		$arrParam = array(
			"table" => $table,
			"order" => "fk_id_programming",
			"column" => "fk_id_programming",
			"id" => $idProgramming
		);
		$log['old'] = $this->general_model->get_basic_search($arrParam);

		switch ($formType) {
			case "personal":
				$arrParam = array(
					"table" => $table,
					"order" => "id_programming_personal",
					"column" => "id_programming_personal",
					"id" => $hddId
				);
				$log['old'] = $this->general_model->get_basic_search($arrParam);

				$type = $this->input->post('type_personal');
				$data['fk_id_employee_type'] = $type;
				$data['hours'] = $hours;
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

		$this->db->where('id_programming_' . $formType, $hddId);
		$query = $this->db->update('programming_' . $formType, $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update worker - equipment
	 * @since 11/02/2025
	 */
	public function updateWorkerEquipment()
	{
		$hddId = $this->input->post('hddidProgrammingWorker');

		$this->load->model("general_model");
		$arrParam = array(
			"table" => "programming_worker",
			"order" => "id_programming_worker",
			"column" => "id_programming_worker",
			"id" => $hddId
		);
		$result = $this->general_model->get_basic_search($arrParam);

		$machine = $result[0]['fk_id_machine']; //records from the DB

		if (!is_array($machine)) {
			$machine = json_decode($machine, true);
			if (!is_array($machine)) {
				$machine = [];
			}
		}

		$machine[] = $this->input->post('truck'); //new record
		// Convertirlo a formato string con corchetes
		$machine_string = '[' . implode(',', $machine) . ']';

		$data = array(
			'description' => $this->input->post('description'),
			'fk_id_machine' => $machine_string,
		);

		$this->db->where('id_programming_worker', $hddId);
		$query = $this->db->update('programming_worker', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}
}
