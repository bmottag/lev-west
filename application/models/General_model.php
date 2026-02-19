<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Clase para consultas generales a una tabla
 */
class General_model extends CI_Model
{

	/**
	 * Consulta BASICA A UNA TABLA
	 * @param $TABLA: nombre de la tabla
	 * @param $ORDEN: orden por el que se quiere organizar los datos
	 * @param $COLUMNA: nombre de la columna en la tabla para realizar un filtro (NO ES OBLIGATORIO)
	 * @param $VALOR: valor de la columna para realizar un filtro (NO ES OBLIGATORIO)
	 * @since 8/11/2016
	 */
	public function get_basic_search($arrData)
	{
		if ($arrData["id"] != 'x')
			$this->db->where($arrData["column"], $arrData["id"]);
		$this->db->order_by($arrData["order"], "ASC");
		$query = $this->db->get($arrData["table"]);

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}

	/**
	 * Task list
	 * Modules: Dashboard - Payroll
	 * @since 10/11/2016
	 */
	public function get_task($arrData)
	{
		$this->db->select('T.*, id_user, first_name, last_name, log_user, J.job_description job_start, H.job_description job_finish, O.task');
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('param_jobs H', 'H.id_job = T.fk_id_job_finish', 'LEFT');
		$this->db->join('param_operation O', 'O.id_operation = T.fk_id_operation', 'INNER');

		if (array_key_exists("idTask", $arrData)) {
			$this->db->where('id_task', $arrData["idTask"]);
		}
		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}
		if (array_key_exists("from", $arrData) && $arrData["from"] != '') {
			$this->db->where('T.start >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != '' && $arrData["from"] != '') {
			$this->db->where('T.start <', $arrData["to"]);
		}
		if (array_key_exists("toLimit", $arrData) && $arrData["toLimit"] != '') {
			$this->db->where('T.start <', $arrData["toLimit"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$this->db->like('T.start', $arrData["fecha"]);
		}
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where($arrData["column"], $arrData["idWorkOrder"]);
		}

		$this->db->order_by('id_task', 'desc');

		if (array_key_exists("limit", $arrData)) {
			$query = $this->db->get('task T', $arrData["limit"]);
		} else {
			$query = $this->db->get('task T');
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Contar registros de un usuario para el año actual
	 * si es administrador cuenta todo
	 * @author BMOTTAG
	 * @since  14/11/2016
	 */
	public function countTask($arrDatos)
	{
		$userRol = $this->session->userdata("rol");
		$idUser = $this->session->userdata("id");
		$idOperation = $arrDatos["task"];

		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year));

		$sql = "SELECT count(id_task) CONTEO";
		$sql .= " FROM task";

		if (array_key_exists("task", $arrDatos)) {
			$sql .= " WHERE fk_id_operation = $idOperation";
		}

		if ($userRol == 7) { //If it is a normal user, just show the records of the user session
			$sql .= " AND fk_id_user = $idUser";
		}
		$sql .= " AND start >= '$firstDay'";

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->CONTEO;
	}

	/**
	 * Safety´s list
	 * Modules: Dashboard
	 * @since 6/12/2016
	 * @review 9/3/2017
	 */
	public function get_safety($arrData)
	{
		$this->db->select("S.*, CONCAT(first_name, ' ', last_name) name, J.job_description");
		$this->db->join('user U', 'U.id_user = S.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = S.fk_id_job', 'INNER');

		if (array_key_exists("idSafety", $arrData)) {
			$this->db->where('S.id_safety', $arrData["idSafety"]);
		}
		if (array_key_exists("idJob", $arrData)) {
			$this->db->where('S.fk_id_job', $arrData["idJob"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$fecha = $arrData["fecha"] . '%';
			$this->db->where('S.date LIKE', $fecha);
		}

		$this->db->order_by('id_safety', 'desc');

		if (array_key_exists("limit", $arrData)) {
			$query = $this->db->get('safety S', $arrData["limit"]);
		} else {
			$query = $this->db->get('safety S');
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get safety subcontractor workers info
	 * @since 26/2/2016
	 */
	public function get_safety_subcontractors_workers($arrData)
	{
		$this->db->select();
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'INNER');
		if (array_key_exists("idSafetySubcontractor", $arrData) && $arrData["idSafetySubcontractor"] != 'x') {
			$this->db->where('W.id_safety_subcontractor', $arrData["idSafetySubcontractor"]);
		}
		if (array_key_exists("idSafety", $arrData)) {
			$this->db->where('W.fk_id_safety', $arrData["idSafety"]);
		}
		if (array_key_exists("movilNumber", $arrData)) {
			$where = "W.worker_movil_number != ''";
			$this->db->where($where);
		}
		$this->db->order_by('C.company_name, W.worker_name', 'asc');
		$query = $this->db->get('safety_workers_subcontractor W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Hauling list
	 * Modules: Dashboard 
	 * @since 13/1/2017
	 */
	public function get_hauling($arrData)
	{
		$this->db->select("H.*, CONCAT(first_name, ' ', last_name) name, C.company_name, V.unit_number, T.truck_type, J.job_description site_from, Z.job_description site_to, M.material, P.payment");
		$this->db->join('user U', 'U.id_user = H.fk_id_user', 'INNER');
		$this->db->join('param_company C', 'C.id_company = H.fk_id_company', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = H.fk_id_truck', 'LEFT');
		$this->db->join('param_truck_type T', 'T.id_truck_type = H.fk_id_truck_type', 'LEFT');
		$this->db->join('param_jobs J', 'J.id_job = H.fk_id_site_from', 'INNER');
		$this->db->join('param_jobs Z', 'Z.id_job = H.fk_id_site_to', 'LEFT');
		$this->db->join('param_material_type M', 'M.id_material = H.fk_id_material', 'LEFT');
		$this->db->join('param_payment P', 'P.id_payment = H.fk_id_payment', 'LEFT');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$this->db->where('H.date_issue', $arrData["fecha"]);
		}
		if (array_key_exists("from", $arrData) && $arrData["from"] != '') {
			$this->db->where('H.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != '' && $arrData["from"] != '') {
			$this->db->where('H.date_issue <', $arrData["to"]);
		}
		if (array_key_exists("state_delete", $arrData)) {
			$this->db->where('H.state', 3);
		}
		if (array_key_exists("state_active", $arrData)) {
			$this->db->where('H.state !=', 3);
		}

		$this->db->order_by('H.id_hauling', 'desc');

		if (array_key_exists("limit", $arrData)) {
			$query = $this->db->get('hauling H', $arrData["limit"]);
		} else {
			$query = $this->db->get('hauling H');
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Daily inspection list
	 * Modules: Dashboard 
	 * @since 14/1/2017
	 */
	public function get_daily_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}

		$this->db->order_by('I.date_issue', 'desc');
		$query = $this->db->get('inspection_daily I', $arrData["limit"]);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Heavy inspection list
	 * Modules: Dashboard 
	 * @since 14/1/2017
	 */
	public function get_heavy_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}

		$this->db->order_by('I.date_issue', 'desc');
		$query = $this->db->get('inspection_heavy I', $arrData["limit"]);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Special inspection list
	 * Modules: Dashboard 
	 * @since 8/5/2017
	 */
	public function get_special_inspection_generator($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}

		$this->db->order_by('I.date_issue', 'desc');
		$query = $this->db->get('inspection_generator I', $arrData["limit"]);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Special inspection list
	 * Modules: Dashboard 
	 * @since 8/5/2017
	 */
	public function get_special_inspection_hydrovac($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}

		$this->db->order_by('I.date_issue', 'desc');
		$query = $this->db->get('inspection_hydrovac I', $arrData["limit"]);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Special inspection list
	 * Modules: Dashboard 
	 * @since 8/5/2017
	 */
	public function get_special_inspection_sweeper($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}

		$this->db->order_by('I.date_issue', 'desc');
		$query = $this->db->get('inspection_sweeper I', $arrData["limit"]);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Special inspection list
	 * Modules: Dashboard 
	 * @since 27/6/2017
	 */
	public function get_special_inspection_water_truck($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');

		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $arrData["idEmployee"]);
		}

		$this->db->order_by('I.date_issue', 'desc');
		$query = $this->db->get('inspection_watertruck I', $arrData["limit"]);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update field in a table
	 * @since 11/12/2016
	 */
	public function updateRecord($arrDatos)
	{
		$data = array(
			$arrDatos["column"] => $arrDatos["value"]
		);
		$this->db->where($arrDatos["primaryKey"], $arrDatos["id"]);
		$query = $this->db->update($arrDatos["table"], $data);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update field in a table
	 * @since 11/12/2016
	 */
	public function updateWORecords($arrDatos)
	{
		$this->load->library('logger');
		$this->load->model("general_model");

		$data = array(
			$arrDatos["column"] => $arrDatos["value"]
		);
		$this->db->where($arrDatos["primaryKey"], $arrDatos["id"]);
		$query = $this->db->update($arrDatos["table"], $data);

		$log['old'] = $this->general_model->get_basic_search($arrDatos);
		$log['new'] = json_encode($data);

		$this->logger
			->user($this->session->userdata("id")) //Set UserID, who created this  Action
			->type($arrDatos["table"]) //Entry type like, Post, Page, Entry
			->id($arrDatos["id"]) //Entry ID
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
	 * Delete Record
	 * @since 5/12/2016
	 */
	public function deleteRecord($arrDatos)
	{
		$query = $this->db->delete($arrDatos["table"], array($arrDatos["primaryKey"] => $arrDatos["id"]));
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get vehicle list -> Se usa en el Login y en Inspection
	 * Param varchar $encryption -> dato que viene del QR code
	 * Param varchar $idVehicle -> identificador del vehiculo
	 * @since 3/3/2016
	 */
	public function get_vehicle_by($arrData)
	{
		$this->db->select();
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = V.type_level_2', 'INNER');
		if (array_key_exists("encryption", $arrData)) {
			$this->db->where('V.encryption', $arrData["encryption"]);
		}
		if (array_key_exists("idVehicle", $arrData)) {
			$this->db->where('V.id_vehicle', $arrData["idVehicle"]);
		}
		if (array_key_exists("vehicleState", $arrData)) {
			$this->db->where('V.state', $arrData["vehicleState"]);
		}
		if (array_key_exists("vinNumber", $arrData)) {
			if ($arrData["vinNumber"] != "false") {
				$this->db->like('V.vin_number', $arrData["vinNumber"]);
			}
		}
		if (array_key_exists("vehicleType", $arrData)) {
			if ($arrData["vehicleType"] != "false") {
				$this->db->where('V.fk_id_company', 1);
				$this->db->where('T.inspection_type', $arrData["vehicleType"]);
			}
		}
		$this->db->order_by('V.unit_number', 'asc');
		$query = $this->db->get('param_vehicle V');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get job hazard info
	 * @since 27/11/2017
	 */
	public function get_job_hazards($idJob)
	{
		$this->db->select();
		$this->db->join('param_hazard PH', 'PH.id_hazard = H.fk_id_hazard', 'INNER');
		$this->db->join('param_hazard_activity PA', 'PA.id_hazard_activity = PH.fk_id_hazard_activity', 'INNER');
		$this->db->join('param_hazard_priority PP', 'PP.id_priority = PH.fk_id_priority', 'INNER');
		$this->db->where('H.fk_id_job', $idJob);
		$this->db->order_by('PA.hazard_activity, PH.hazard_description', 'asc');
		$query = $this->db->get('job_hazards H');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de enlaces
	 * Modules: MENU
	 * @since 27/4/2018
	 */
	public function get_enlaces($arrData)
	{
		if (array_key_exists("idEnlace", $arrData)) {
			$this->db->where('id_enlace', $arrData["idEnlace"]);
		}
		if (array_key_exists("tipoEnlace", $arrData)) {
			$this->db->where('tipo_enlace', $arrData["tipoEnlace"]);
		}
		if (array_key_exists("estadoEnlace", $arrData)) {
			$this->db->where('enlace_estado', $arrData["estadoEnlace"]);
		}

		$this->db->order_by('order', 'asc');
		$query = $this->db->get('enlaces');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de programacion
	 * @since 15/1/2018
	 */
	public function get_programming($arrData)
	{
		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year - 1));

		$this->db->select("P.*, X.id_job, X.job_description, X.fk_id_company id_company, U.id_user, CONCAT(U.first_name, ' ', U.last_name) name");
		$this->db->join('user U', 'U.id_user = P.fk_id_user', 'INNER');
		$this->db->join('param_jobs X', 'X.id_job = P.fk_id_job', 'INNER');

		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('P.fk_id_user', $arrData["idUser"]);
		}
		if (array_key_exists("idProgramming", $arrData)) {
			$this->db->where('P.id_programming', $arrData["idProgramming"]);
		}
		if (array_key_exists("jobId", $arrData)) {
			$this->db->where('P.fk_id_job', $arrData["jobId"]);
		}
		if (array_key_exists("idParent", $arrData)) {
			$this->db->where('P.parent_id', $arrData["idParent"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$this->db->where('P.date_programming', $arrData["fecha"]);
		}
		if (array_key_exists("smsAutomatic", $arrData)) {
			$this->db->where('X.planning_message', 1);
		}
		if (array_key_exists("estado", $arrData)) {
			if ($arrData["estado"] == "ACTIVAS") {
				$this->db->where('P.state !=', 3);
			} else {
				$this->db->where('P.state', $arrData["estado"]);
			}
		}

		$this->db->where('P.date_issue >=', $firstDay); //se filtran por registros mayores al primer dia del año

		$this->db->order_by("P.date_programming DESC");
		$query = $this->db->get("programming P");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}

	/**
	 * Lista trabajadores para una programacion
	 * @since 15/1/2019
	 */
	public function get_programming_workers($arrData)
	{
		$sql = "SELECT U.movil, CONCAT(first_name, ' ', last_name) name, P.*, H.hora, H.formato_24";
		$sql .= " FROM programming_worker P";
		$sql .= " INNER JOIN user U ON U.id_user = P.fk_id_programming_user ";
		$sql .= " LEFT JOIN param_horas H ON H.id_hora = P.fk_id_hour ";
		$sql .= " WHERE P.id_programming_worker is NOT NULL ";

		if (array_key_exists("idUser", $arrData)) {
			$idUser = $arrData['idUser'];
			$sql .= " AND P.fk_id_programming_user = '$idUser'";
		}

		if (array_key_exists("idProgramming", $arrData)) {
			$idProgramming = $arrData['idProgramming'];
			$sql .= " AND P.fk_id_programming = '$idProgramming'";
		}

		if (array_key_exists("createWO", $arrData)) {
			$sql .= " AND P.creat_wo = 1 ";
		}

		if (array_key_exists("withEquipment", $arrData)) {
			$sql .= " AND P.fk_id_machine is NOT NULL AND P.fk_id_machine != '' ";
		}

		if (array_key_exists("safety", $arrData)) {
			$safety = $arrData['safety'];
			$sql .= " AND P.safety = '$safety'";
		}

		$sql .= " GROUP BY P.fk_id_programming_user ORDER BY U.first_name, U.last_name ASC ";

		$query = $this->db->query($sql);

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista trabajadores para una programacion
	 * @since 15/1/2019
	 */
	public function get_programming_equipment($arrData)
	{
		$sql = "SELECT P.fk_id_programming_user, P.description, V.id_vehicle, V.type_level_2 ";
		$sql .= " FROM programming_worker P";
		$sql .= " LEFT JOIN param_vehicle V ON FIND_IN_SET(V.id_vehicle, REPLACE(REPLACE(fk_id_machine, '[', ''), ']', '')) > 0 ";
		$sql .= " WHERE P.fk_id_machine is NOT NULL AND P.fk_id_machine != '' ";

		if (array_key_exists("idProgramming", $arrData)) {
			$idProgramming = $arrData['idProgramming'];
			$sql .= " AND P.fk_id_programming = '$idProgramming'";
		}

		$query = $this->db->query($sql);

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}


	/*public function get_programming_workers($arrData)
	{
		// print_r($arrData);
		// exit;
		$this->db->select("U.movil, CONCAT(first_name, ' ', last_name) name, P.*, CONCAT(V.unit_number,' -----> ', V.description) as unit_description, H.hora, H.formato_24");
		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('P.fk_id_programming_user', $arrData["idUser"]);
		}
		if (array_key_exists("idProgramming", $arrData)) {
			$this->db->where('P.fk_id_programming', $arrData["idProgramming"]);
		}
		if (array_key_exists("machine", $arrData)) {
			$this->db->where('P.fk_id_machine is NOT NULL');
			$this->db->where('P.fk_id_machine != 0');
		}
		if (array_key_exists("wo", $arrData)) {
			$this->db->where('P.creat_wo = 1');
		}
		if (array_key_exists("safety", $arrData)) {
			$this->db->where('P.safety', $arrData["safety"]);
		}

		$this->db->join('user U', 'U.id_user = P.fk_id_programming_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = P.fk_id_machine', 'LEFT');
		$this->db->join('param_horas H', 'H.id_hora = P.fk_id_hour', 'LEFT');

		$this->db->order_by("U.first_name, U.last_name ASC");
		$query = $this->db->get("programming_worker P");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}*/

	/**
	 * Lista de inspeccions para maquinas asignadas en una programacion
	 * @since 15/1/2019
	 */
	public function get_programming_wo($arrData)
	{
		$this->db->select();
		$this->db->where('fk_id_user', $arrData["idUser"]);
		$this->db->where('date_issue', $arrData["fecha"]);

		$query = $this->db->get("workorder");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}


	/**
	 * Lista de inspeccions para maquinas asignadas en una programacion
	 * @since 15/1/2019
	 */
	public function get_programming_inspecciones($arrData)
	{
		//Como la el campo fk_id_machine ahora es un string en donde se pueden guardar varias maquinas, convertimos en json para saber si tiene mas de una maquina
		$encode = json_decode($arrData["maquina"]);

		if (is_array($encode)) {
			$machine = implode(",", json_decode($arrData["maquina"]));
		} else {
			$machine = json_decode($arrData["maquina"]);
		}

		$this->db->select();

		$where = "fk_id_machine IN ($machine)";
		$this->db->where($where);
		//$this->db->where('fk_id_machine', $arrData["maquina"]);
		$this->db->where('date_inspection', $arrData["fecha"]);

		$query = $this->db->get("inspection_total");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}

	public function get_missing_programming_inspecciones($arrData)
	{
		$machines = json_decode($arrData["maquina"]);

		$this->db->select('fk_id_machine');
		$this->db->where('date_inspection', $arrData["fecha"]);
		$registered_machines_query = $this->db->get('inspection_total');

		$registered_machines = array_column($registered_machines_query->result_array(), 'fk_id_machine');

		$missing_machines = array_diff($machines, $registered_machines);

		if (empty($missing_machines)) {
			return false;
		}

		return $missing_machines;
	}

	/**
	 * Lista de horas
	 * @since 18/1/2019
	 */
	public function get_horas()
	{
		$this->db->order_by("order", "ASC");
		$query = $this->db->get("param_horas");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}

	/**
	 * tool_box list
	 * para año vigente
	 * @since 24/10/2017
	 */
	public function get_tool_box($arrDatos)
	{
		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //para filtrar solo los registros del año actual

		$this->db->select('T.*, CONCAT(U.first_name, " " , U.last_name) name, J.id_job, J.job_description');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("fecha", $arrDatos)) {
			$this->db->where('date_tool_box', $arrDatos["fecha"]);
		}
		if (array_key_exists("idToolBox", $arrDatos)) {
			$this->db->where('id_tool_box', $arrDatos["idToolBox"]);
		}

		//$this->db->where('T.date_tool_box >=', $firstDay);

		$this->db->order_by('id_tool_box', 'asc');
		$query = $this->db->get('tool_box T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * confined space entry permit list
	 * @since 13/1/2020
	 */
	public function get_confined_space($arrDatos)
	{
		$this->db->select('C.*, CONCAT(U.first_name, " " , U.last_name) name, J.id_job, J.job_description, CONCAT(X.first_name, " " , X.last_name) user_authorization, CONCAT(Z.first_name, " " , Z.last_name) user_cancellation');
		$this->db->join('param_jobs J', 'J.id_job = C.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = C.fk_id_user', 'INNER');
		$this->db->join('user X', 'X.id_user = C.fk_id_user_authorization', 'INNER');
		$this->db->join('user Z', 'Z.id_user = C.fk_id_user_cancellation', 'INNER');

		if (array_key_exists("idJob", $arrDatos) && $arrDatos["idJob"] != 'x') {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}

		if (array_key_exists("idConfined", $arrDatos)) {
			$this->db->where('id_job_confined', $arrDatos["idConfined"]);
		}

		if (array_key_exists("from", $arrDatos)) {
			$this->db->where('date_confined >=', $arrDatos["from"]);
		}
		if (array_key_exists("to", $arrDatos)) {
			$this->db->where('date_confined <=', $arrDatos["to"]);
		}

		$this->db->order_by('id_job_confined', 'asc');
		$query = $this->db->get('job_confined C');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * re-testing
	 * @since 2/2/2020
	 */
	public function get_confined_re_testing($arrDatos)
	{
		$this->db->select();

		if (array_key_exists("idRetesting", $arrDatos)) {
			$this->db->where('id_job_confined_re_testing', $arrDatos["idRetesting"]);
		}

		if (array_key_exists("idConfined", $arrDatos)) {
			$this->db->where('fk_id_job_confined', $arrDatos["idConfined"]);
		}

		$this->db->order_by('id_job_confined_re_testing', 'asc');
		$query = $this->db->get('job_confined_re_testing');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Maintenance Check list
	 * @since 13/3/2020
	 */
	public function get_maintenance_check()
	{
		$this->db->select();
		$this->db->join('preventive_maintenance M', 'M.id_preventive_maintenance = C.fk_id_maintenance', 'INNER');
		$this->db->join('maintenance_type T', 'T.id_maintenance_type = M.fk_id_maintenance_type', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = M.fk_id_equipment', 'INNER');

		$this->db->order_by('V.unit_number', 'asc');
		$query = $this->db->get('maintenance_check C');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Stock list
	 * @since 22/3/2020
	 */
	public function get_stock($arrDatos)
	{
		$this->db->select();
		$this->db->where('quantity >', 0);
		if (array_key_exists("idStock", $arrDatos)) {
			$this->db->where('id_stock', $arrDatos["idStock"]);
		}
		$this->db->order_by('stock_description');
		$query = $this->db->get('stock');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de menu
	 * Modules: MENU
	 * @since 30/3/2020
	 */
	public function get_menu($arrData)
	{
		if (array_key_exists("idMenu", $arrData)) {
			$this->db->where('id_menu', $arrData["idMenu"]);
		}
		if (array_key_exists("menuType", $arrData)) {
			$this->db->where('menu_type', $arrData["menuType"]);
		}
		if (array_key_exists("menuState", $arrData)) {
			$this->db->where('menu_state', $arrData["menuState"]);
		}
		if (array_key_exists("columnOrder", $arrData)) {
			$this->db->order_by($arrData["columnOrder"], 'asc');
		} else {
			$this->db->order_by('menu_order', 'asc');
		}

		$query = $this->db->get('param_menu');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de roles
	 * Modules: ROL
	 * @since 30/3/2020
	 */
	public function get_roles($arrData)
	{
		if (array_key_exists("filtro", $arrData)) {
			$this->db->where('id_rol !=', 99);
		}
		if (array_key_exists("idRol", $arrData)) {
			$this->db->where('id_rol', $arrData["idRol"]);
		}

		$this->db->order_by('rol_name', 'asc');
		$query = $this->db->get('param_rol');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * User list
	 * @since 30/3/2020
	 */
	public function get_user($arrData)
	{
		$this->db->select();
		$this->db->join('param_rol R', 'R.id_rol = U.perfil', 'INNER');
		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('U.id_user', $arrData["idUser"]);
		}
		if (array_key_exists("idUserMANAGERS", $arrData)) {
			$IDmagers = array(2, 3);
			$this->db->where_in('U.id_user', $IDmagers);
		}
		if (array_key_exists("state", $arrData)) {
			$this->db->where('U.state', $arrData["state"]);
		}
		//list without inactive users
		if (array_key_exists("filtroState", $arrData)) {
			$this->db->where('U.state !=', 2);
		}
		if (array_key_exists("employee_subcontractor", $arrData)) {
			$this->db->where('U.employee_subcontractor', $arrData["employee_subcontractor"]);
		}
		if (array_key_exists("idRolesSupervisors", $arrData)) {
			$idRoles = array(ID_ROL_SUPER_ADMIN, ID_ROL_MANAGER, ID_ROL_SAFETY, ID_ROL_SUPERVISOR);
			$this->db->where_in('U.perfil', $idRoles);
			$this->db->where('U.id_user !=', 1);
		}

		$this->db->order_by("first_name, last_name", "ASC");
		$query = $this->db->get("user U");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de enlaces
	 * Modules: MENU
	 * @since 31/3/2020
	 */
	public function get_links($arrData)
	{
		$this->db->select();
		$this->db->join('param_menu M', 'M.id_menu = L.fk_id_menu', 'INNER');

		if (array_key_exists("idMenu", $arrData)) {
			$this->db->where('fk_id_menu', $arrData["idMenu"]);
		}
		if (array_key_exists("idLink", $arrData)) {
			$this->db->where('id_link', $arrData["idLink"]);
		}
		if (array_key_exists("linkType", $arrData)) {
			$this->db->where('link_type', $arrData["linkType"]);
		}
		if (array_key_exists("linkState", $arrData)) {
			$this->db->where('link_state', $arrData["linkState"]);
		}

		$this->db->order_by('M.menu_order, L.order', 'asc');
		$query = $this->db->get('param_menu_links L');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de permisos
	 * Modules: MENU
	 * @since 31/3/2020
	 */
	public function get_role_access($arrData)
	{
		$this->db->select('P.id_permiso, P.fk_id_menu, P.fk_id_link, P.fk_id_rol, M.menu_name, M.menu_order, M.menu_type, L.link_name, L.link_url, L.order, L.link_icon, L.link_type, R.rol_name, R.estilos');
		$this->db->join('param_menu M', 'M.id_menu = P.fk_id_menu', 'INNER');
		$this->db->join('param_menu_links L', 'L.id_link = P.fk_id_link', 'LEFT');
		$this->db->join('param_rol R', 'R.id_rol = P.fk_id_rol', 'INNER');

		if (array_key_exists("idPermiso", $arrData)) {
			$this->db->where('id_permiso', $arrData["idPermiso"]);
		}
		if (array_key_exists("idMenu", $arrData)) {
			$this->db->where('P.fk_id_menu', $arrData["idMenu"]);
		}
		if (array_key_exists("idLink", $arrData)) {
			$this->db->where('P.fk_id_link', $arrData["idLink"]);
		}
		if (array_key_exists("idRole", $arrData)) {
			$this->db->where('P.fk_id_rol', $arrData["idRole"]);
		}
		if (array_key_exists("menuType", $arrData)) {
			$this->db->where('M.menu_type', $arrData["menuType"]);
		}
		if (array_key_exists("linkState", $arrData)) {
			$this->db->where('L.link_state', $arrData["linkState"]);
		}
		if (array_key_exists("menuURL", $arrData)) {
			$this->db->where('M.menu_url', $arrData["menuURL"]);
		}
		if (array_key_exists("linkURL", $arrData)) {
			$this->db->where('L.link_url', $arrData["linkURL"]);
		}

		$this->db->order_by('M.menu_order, L.order', 'asc');
		$query = $this->db->get('param_menu_permisos P');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * menu list for a role
	 * Modules: MENU
	 * @since 2/4/2020
	 */
	public function get_role_menu($arrData)
	{
		$this->db->select();
		$this->db->join('param_menu M', 'M.id_menu = P.fk_id_menu', 'INNER');

		if (array_key_exists("idRole", $arrData)) {
			$this->db->where('P.fk_id_rol', $arrData["idRole"]);
		}
		if (array_key_exists("menuType", $arrData)) {
			$this->db->where('M.menu_type', $arrData["menuType"]);
		}
		if (array_key_exists("menuState", $arrData)) {
			$this->db->where('M.menu_state', $arrData["menuState"]);
		}

		$this->db->group_by("P.fk_id_menu");
		$this->db->order_by('M.menu_order', 'asc');
		$query = $this->db->get('param_menu_permisos P');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get job hazard info
	 * @since 27/11/2017
	 */
	public function get_job_employee_type_unit_price($idJob)
	{
		$this->db->select();
		$this->db->join('param_employee_type PE', 'PE.id_employee_type = JE.fk_id_employee_type ', 'INNER');
		$this->db->where('JE.fk_id_job', $idJob);
		$this->db->order_by('PE.employee_type', 'asc');
		$query = $this->db->get('job_employee_type_price JE');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get equipment list
	 * Param int $companyType -> 1: VCI; 2: Subcontractor
	 * @since 6/11/2020
	 */
	public function get_equipment_info_by($arrData)
	{
		$this->db->select('id_vehicle, make, unit_number,model, type_2, equipment_unit_price, equipment_unit_cost, equipment_unit_price_without_driver');
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = A.type_level_2', 'INNER');

		if (array_key_exists("idVehicle", $arrData)) {
			$this->db->where('A.id_vehicle', $arrData["idVehicle"]);
		}
		if (array_key_exists("vehicleState", $arrData)) {
			$this->db->where('A.state', $arrData["vehicleState"]);
		}
		if (array_key_exists("companyType", $arrData)) {
			$this->db->where('A.type_level_1', $arrData["companyType"]);
		}

		$this->db->order_by('T.inspection_type, A.unit_number', 'asc');
		$query = $this->db->get('param_vehicle A');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get equipment list
	 * Param int $companyType -> 1: VCI; 2: Subcontractor
	 * @since 6/11/2020
	 */
	public function get_equipment_price($arrData)
	{
		$this->db->select('JE.*, id_vehicle, make, unit_number,model, type_2');
		$this->db->join('param_vehicle A', 'A.id_vehicle = JE.fk_id_equipment', 'INNER');
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = A.type_level_2', 'INNER');
		$this->db->where('JE.fk_id_job', $arrData["idJob"]);
		if (array_key_exists("vehicleState", $arrData)) {
			$this->db->where('A.state', $arrData["vehicleState"]);
		}
		if (array_key_exists("companyType", $arrData)) {
			$this->db->where('A.type_level_1', $arrData["companyType"]);
		}


		$this->db->order_by('T.inspection_type, A.unit_number', 'asc');
		$query = $this->db->get('job_equipment_price JE');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Work Order
	 * @since 21/12/2020
	 */
	public function get_workorder_info($arrData)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, J.id_job, J.job_description, C.*");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');

		if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 0) {
			$this->db->where('W.fk_id_job', $arrData["jobId"]);
		}
		if (array_key_exists("idClaim", $arrData)) {
			$this->db->where('W.fk_id_claim', $arrData["idClaim"]);
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
			$this->db->where('W.date <', $arrData["to"]);
		}
		if (array_key_exists("state", $arrData) && $arrData["state"] != '') {
			$this->db->where('W.state', $arrData["state"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$this->db->where('W.date', $arrData["fecha"]);
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
	 * Forceaccounts info
	 * @since 05/05/2025
	 */
	public function get_forceaccount_info($arrData)
	{
		$this->db->select('W.*, J.id_job, job_description, CONCAT(U.first_name, " ", U.last_name) name, C.company_name company, C.id_company, A.id_acs');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');
		$this->db->join('acs A', 'A.fk_id_workorder = W.id_forceaccount', 'LEFT');
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');

		if (array_key_exists("from", $arrData) && $arrData["from"] != '') {
			$this->db->where('W.date >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != '' && $arrData["from"] != '') {
			$this->db->where('W.date <', $arrData["to"]);
		}

		$this->db->order_by('W.id_forceaccount', 'desc');
		$query = $this->db->get('forceaccount W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Lista de programacion
	 * @since 21/12/2020
	 */
	public function get_programming_info($arrData)
	{
		$this->db->select("P.*, X.id_job, X.job_description, U.id_user, CONCAT(U.first_name, ' ', U.last_name) name");
		$this->db->join('user U', 'U.id_user = P.fk_id_user', 'INNER');
		$this->db->join('param_jobs X', 'X.id_job = P.fk_id_job', 'INNER');

		if (array_key_exists("nextPlanning", $arrData)) {
			$currentDate = date('Y-m-d');
			$plus2 = date('Y-m-d', strtotime($currentDate . ' +2 days'));

			$this->db->where('P.date_programming >', $currentDate);
			$this->db->where('P.date_programming <', $plus2);
			$this->db->where('P.state !=', 3);
		}
		if (array_key_exists("idProgramming", $arrData)) {
			$this->db->where('P.id_programming', $arrData["idProgramming"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$this->db->where('P.date_programming', $arrData["fecha"]);
		}
		if (array_key_exists("estado", $arrData)) {
			if ($arrData["estado"] == "ACTIVAS") {
				$this->db->where('P.state !=', 3);
			} else {
				$this->db->where('P.state', $arrData["estado"]);
			}
		}
		if (array_key_exists("from", $arrData) && $arrData["from"] != '') {
			$this->db->where('P.date_programming >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != '' && $arrData["from"] != '') {
			$this->db->where('P.date_programming <', $arrData["to"]);
		}

		$this->db->order_by("P.date_programming DESC");
		$query = $this->db->get("programming P");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Verificacion si existe maquina programada para una fecha
	 * @since 8/1/2021
	 */
	public function get_programming_machine_vs_date_programming($arrData)
	{
		$this->db->select();
		$this->db->join('programming P', 'P.id_programming = W.fk_id_programming ', 'INNER');

		if (array_key_exists("idProgrammingWorker", $arrData)) {
			$this->db->where('W.id_programming_worker !=', $arrData["idProgrammingWorker"]);
		}
		if (array_key_exists("fechaProgramming", $arrData)) {
			$this->db->where('P.date_programming', $arrData["fechaProgramming"]);
		}
		if (array_key_exists("maquina", $arrData)) {

			$maquina = implode(", ", $arrData["maquina"]);
			$where = "W.fk_id_machine IN ($maquina)";
			$this->db->where($where);
		}

		$query = $this->db->get("programming_worker W");

		if ($query->num_rows() >= 1) {
			return true;
		} else
			return false;
	}

	/**
	 * Excavation and Trenching Plan list
	 * For current year
	 * @since 1/08/2021
	 */
	public function get_excavation($arrDatos)
	{
		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //para filtrar solo los registros del año actual

		$this->db->select('E.*, CONCAT(W.first_name, " " , W.last_name) name, CONCAT(U.first_name, " " , U.last_name) manager, CONCAT(X.first_name, " " , X.last_name) operator, CONCAT(Z.first_name, " " , Z.last_name) supervisor, J.id_job, J.job_description');
		$this->db->join('param_jobs J', 'J.id_job = E.fk_id_job', 'INNER');
		$this->db->join('user W', 'W.id_user = E.fk_id_user', 'INER');
		$this->db->join('user U', 'U.id_user = E.fk_id_user_manager', 'LEFT');
		$this->db->join('user X', 'X.id_user = E.fk_id_user_operator', 'LEFT');
		$this->db->join('user Z', 'Z.id_user = E.fk_id_user_supervisor', 'LEFT');
		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("fecha", $arrDatos)) {
			$this->db->where('date_excavation', $arrDatos["fecha"]);
		}
		if (array_key_exists("idExcavation", $arrDatos)) {
			$this->db->where('id_job_excavation', $arrDatos["idExcavation"]);
		}

		//$this->db->where('T.date_tool_box >=', $firstDay);

		$this->db->order_by('id_job_excavation', 'asc');
		$query = $this->db->get('job_excavation E');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get Excavation subcontractor workers info
	 * @since 2/8/2021
	 */
	public function get_excavation_subcontractors($arrData)
	{
		$this->db->select();
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'INNER');
		if (array_key_exists("idExcavation", $arrData)) {
			$this->db->where('W.fk_id_job_excavation', $arrData["idExcavation"]);
		}
		if (array_key_exists("idSubcontractor", $arrData) && $arrData["idSubcontractor"] != 'x') {
			$this->db->where('W.id_excavation_subcontractor', $arrData["idSubcontractor"]);
		}
		if (array_key_exists("movilNumber", $arrData)) {
			$where = "W.worker_movil_number != ''";
			$this->db->where($where);
		}
		$this->db->order_by('C.company_name, W.worker_name', 'asc');
		$query = $this->db->get('job_excavation_subcontractor W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get Excavation workers info
	 * @since 2/8/2021
	 */
	public function get_excavation_workers($arrData)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		if (array_key_exists("idExcavation", $arrData)) {
			$this->db->where('W.fk_id_job_excavation', $arrData["idExcavation"]);
		}
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('job_excavation_workers W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get user certificates
	 * @since 15/1/2022
	 */
	public function get_user_certificates($arrData)
	{
		$this->db->select();
		$this->db->join('user U', 'U.id_user = X.fk_id_user', 'INNER');
		$this->db->join('param_certificates C', 'C.id_certificate = X.fk_id_certificate ', 'INNER');
		if (array_key_exists("idUserCertificate", $arrData)) {
			$this->db->where('X.id_user_certificate', $arrData["idUserCertificate"]);
		}
		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('U.id_user', $arrData["idUser"]);
		}
		if (array_key_exists("state", $arrData)) {
			$this->db->where('U.state', $arrData["state"]);
		}
		if (array_key_exists("expires", $arrData)) {
			$this->db->where('X.expires', $arrData["expires"]);
		}
		if (array_key_exists("idCertificate", $arrData)) {
			$this->db->where('C.id_certificate', $arrData["idCertificate"]);
		}
		if (array_key_exists("date", $arrData)) {
			$this->db->where('X.date_through <=', $arrData["date"]);
			$this->db->where('X.expires', 1);
		}
		$this->db->order_by('C.certificate', 'asc');
		$query = $this->db->get('user_certificates X');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get NOTIFICATION ACCESS
	 * @since 22/12/2022
	 */
	public function get_notifications_access($arrData)
	{
		$this->db->select("
			A.*, 
			N.notification, 
			N.description, 
			CONCAT(U.first_name, ' ', U.last_name) AS name_email, 
			U.email, 
			U.id_user AS id_user_email, 
			CONCAT(X.first_name, ' ', X.last_name) AS name_sms, 
			X.movil, 
			X.id_user AS id_user_sms
		");
		$this->db->join('notifications N', 'N.id_notification = A.fk_id_notification', 'INNER');

		// Convertir el ID de usuario a cadena antes de usar JSON_QUOTE
		$this->db->join('user U', 'JSON_CONTAINS(A.fk_id_user_email, JSON_QUOTE(CAST(U.id_user AS CHAR)))', 'LEFT');
		$this->db->join('user X', 'JSON_CONTAINS(A.fk_id_user_sms, JSON_QUOTE(CAST(X.id_user AS CHAR)))', 'LEFT');

		if (array_key_exists("idNotificationAccess", $arrData)) {
			$this->db->where('A.id_notification_access', $arrData["idNotificationAccess"]);
		}

		if (array_key_exists("idNotification", $arrData)) {
			$this->db->where('A.fk_id_notification', $arrData["idNotification"]);
		}

		$this->db->order_by('N.notification', 'asc');
		$query = $this->db->get('notifications_access A');

		if ($query->num_rows() > 0) {

			$results = $query->result_array();

			foreach ($results as $key => $value) {
				$data[$key]['id_user_email'] = $value['id_user_email'];
				$data[$key]['id_user_sms']   = $value['id_user_sms'];
				$data[$key]['id_notification_access'] = $value['id_notification_access'];
				$data[$key]['fk_id_notification'] = $value['fk_id_notification'];
				$data[$key]['fk_id_user_email'] = $value['fk_id_user_email'];
				$data[$key]['fk_id_user_sms'] = $value['fk_id_user_sms'];
				$data[$key]['notification'] = $value['notification'];
				$data[$key]['description'] = $value['description'];
				$data[$key]['name_email'] = $value['name_email'];
				$data[$key]['email'] = $value['email'];
				$data[$key]['name_sms'] = $value['name_sms'];
				$data[$key]['movil'] = $value['movil'];
			}

			$uniqueEmails = [];
			$uniqueMoviles = [];

			foreach ($data as &$item) {
				// Verificar si el email ya está en el arreglo de únicos
				if (in_array($item['email'], $uniqueEmails)) {
					$item['email'] = ''; // Dejar el campo email vacío si es duplicado
					$item['name_email'] = '';
				} else {
					$uniqueEmails[] = $item['email']; // Agregar a la lista de únicos
				}
				// Verificar si el móvil ya está en el arreglo de únicos
				if (in_array($item['movil'], $uniqueMoviles)) {
					$item['movil'] = ''; // Dejar el campo móvil vacío si es duplicado
					$item['name_sms'] = '';
				} else {
					$uniqueMoviles[] = $item['movil']; // Agregar a la lista de únicos
				}
			}

			return $data;
		} else {
			return false;
		}
		/*$this->db->select("A.*, N.notification, N.description,CONCAT(U.first_name, ' ', U.last_name) name_email, U.email, CONCAT(X.first_name, ' ', X.last_name) name_sms, X.movil");
		$this->db->join('notifications N', 'N.id_notification = A.fk_id_notification', 'INNER');
		$this->db->join('user U', 'U.id_user = A.fk_id_user_email', 'LEFT');
		$this->db->join('user X', 'X.id_user = A.fk_id_user_sms', 'LEFT');
		if (array_key_exists("idNotificationAccess", $arrData)) {
			$this->db->where('A.id_notification_access', $arrData["idNotificationAccess"]);
		}
		if (array_key_exists("idNotification", $arrData)) {
			$this->db->where('A.fk_id_notification', $arrData["idNotification"]);
		}
		$this->db->order_by('N.notification', 'asc');
		$query = $this->db->get('notifications_access A');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}*/
	}

	public function get_notifications_access_view($arrData)
	{
		$this->db->select("A.*, 
			N.notification, 
			N.description, 
			GROUP_CONCAT(DISTINCT CONCAT(U.first_name, ' ', U.last_name) ORDER BY U.id_user ASC SEPARATOR ', ') AS name_email, 
			GROUP_CONCAT(DISTINCT U.email ORDER BY U.id_user ASC SEPARATOR ', ') AS email, 
			GROUP_CONCAT(DISTINCT CONCAT(X.first_name, ' ', X.last_name) ORDER BY X.id_user ASC SEPARATOR ', ') AS name_sms, 
			GROUP_CONCAT(DISTINCT X.movil ORDER BY X.id_user ASC SEPARATOR ', ') AS movil");

		$this->db->from('notifications_access A');
		$this->db->join('notifications N', 'N.id_notification = A.fk_id_notification', 'INNER');

		// Cambiar el JOIN para usar JSON_CONTAINS
		$this->db->join('user U', 'JSON_CONTAINS(A.fk_id_user_email, JSON_QUOTE(CAST(U.id_user AS CHAR)))', 'LEFT');
		$this->db->join('user X', 'JSON_CONTAINS(A.fk_id_user_sms, JSON_QUOTE(CAST(X.id_user AS CHAR)))', 'LEFT');

		//$this->db->where('A.id_notification_access', 14);
		if (array_key_exists("idNotificationAccess", $arrData)) {
			$this->db->where('A.id_notification_access', $arrData["idNotificationAccess"]);
		}
		if (array_key_exists("idNotification", $arrData)) {
			$this->db->where('A.fk_id_notification', $arrData["idNotification"]);
		}

		// Agrupar por el ID de acceso a la notificación para evitar duplicados
		$this->db->group_by('A.id_notification_access');
		$this->db->order_by('N.notification', 'asc');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {

			$results = $query->result_array();

			return $results;
		} else {
			return false;
		}
	}
	/**
	 * Verify if the user already exist by specific column
	 * @author BMOTTAG
	 * @since  8/11/2016
	 * @review 31/01/2022
	 */
	public function verifyUser($arrData)
	{
		$this->db->where($arrData["column"], $arrData["value"]);
		$query = $this->db->get("user");

		if ($query->num_rows() >= 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * PERIOD
	 * @since 9/02/2022
	 */
	public function get_period($arrData)
	{
		if (array_key_exists("idPeriod", $arrData)) {
			$this->db->where('id_period', $arrData["idPeriod"]);
		}
		if (array_key_exists("year_period", $arrData)) {
			$this->db->where('year_period', $arrData["year_period"]);
		}
		$this->db->order_by('id_period', 'desc');

		if (array_key_exists("limit", $arrData)) {
			$query = $this->db->get('payroll_period', $arrData["limit"]);
		} else {
			$query = $this->db->get('payroll_period');
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * WEAK PERIOD
	 * @since 9/02/2022
	 */
	public function get_weak_period($arrData)
	{
		if (array_key_exists("idPeriodWeak", $arrData)) {
			$this->db->where('id_period_weak', $arrData["idPeriodWeak"]);
		}

		if (array_key_exists("idPeriod", $arrData)) {
			$this->db->where('fk_id_period', $arrData["idPeriod"]);
		}

		$this->db->order_by('id_period_weak', 'desc');

		if (array_key_exists("limit", $arrData)) {
			$query = $this->db->get('payroll_period_weaks', $arrData["limit"]);
		} else {
			$query = $this->db->get('payroll_period_weaks');
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Task list
	 * Modules: Payroll/search
	 * @since 10/02/2022
	 */
	public function get_users_by_period($arrData)
	{
		$this->db->select("T.fk_id_user");
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		$this->db->join('payroll_period_weaks W', 'W.id_period_weak = T.fk_id_weak_period', 'LEFT');
		$this->db->join('payroll_period P', 'P.id_period = W.fk_id_period', 'INNER');

		if (array_key_exists("idPeriod", $arrData)) {
			$this->db->where('P.id_period', $arrData["idPeriod"]);
		}
		if (array_key_exists("idEmployee", $arrData) && $arrData["idEmployee"] != '') {
			$this->db->where('T.fk_id_user', $arrData["idEmployee"]);
		}
		if (array_key_exists("from", $arrData)) {
			$this->db->where('T.start >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData)) {
			$this->db->where('T.start <=', $arrData["to"]);
		}
		if (array_key_exists("employee_subcontractor", $arrData)) {
			$this->db->where('U.employee_subcontractor', $arrData["employee_subcontractor"]);
		}
		$this->db->group_by("T.fk_id_user");
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('task T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Task list
	 * Modules: Payroll/search
	 * @since 11/02/2022
	 */
	public function get_task_by_period($arrData)
	{
		$this->db->select("T.*, CONCAT(first_name, ' ', last_name) name, id_user, W.period_weak, J.job_description job_start, H.job_description job_finish");
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('param_jobs H', 'H.id_job = T.fk_id_job_finish', 'LEFT');
		$this->db->join('payroll_period_weaks W', 'W.id_period_weak = T.fk_id_weak_period', 'LEFT');
		$this->db->join('payroll_period P', 'P.id_period = W.fk_id_period', 'INNER');

		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('U.id_user', $arrData["idUser"]);
		}
		if (array_key_exists("idPeriod", $arrData)) {
			$this->db->where('P.id_period', $arrData["idPeriod"]);
		}
		if (array_key_exists("weakNumber", $arrData)) {
			$this->db->where('W.weak_number', $arrData["weakNumber"]);
		}
		if (array_key_exists("from", $arrData)) {
			$this->db->where('T.start >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData)) {
			$this->db->where('T.start <=', $arrData["to"]);
		}
		$this->db->order_by('id_task', 'asc');
		$query = $this->db->get('task T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Paystub by period
	 * Modules: Payroll/search
	 * @since 25/02/2022
	 */
	public function get_paystub_by_period($arrData)
	{
		$this->db->select("P.*, CONCAT(U.first_name, ' ', U.last_name) name, CONCAT(W.first_name, ' ', W.last_name) employee, W.address, W.postal_code, X.date_start,X.date_finish");
		$this->db->join('user U', 'U.id_user = P.paystub_fk_id_user', 'INNER');
		$this->db->join('user W', 'W.id_user = P.fk_id_employee', 'INNER');
		$this->db->join('payroll_period X', 'X.id_period = P.fk_id_period', 'INNER');

		if (array_key_exists("idPaytsub", $arrData)) {
			$this->db->where('P.id_paystub', $arrData["idPaytsub"]);
		}
		if (array_key_exists("idEmployee", $arrData) && $arrData["idEmployee"] != '') {
			$this->db->where('P.fk_id_employee', $arrData["idEmployee"]);
		}
		if (array_key_exists("idPeriod", $arrData)) {
			$this->db->where('P.fk_id_period', $arrData["idPeriod"]);
		}
		if (array_key_exists("year", $arrData)) {
			$this->db->where('X.year_period', $arrData["year"]);
		}
		$this->db->order_by('W.first_name, W.last_name, P.fk_id_period', 'asc');
		$query = $this->db->get('payroll_paystub P');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Total Paystub by year and employee
	 * Modules: Payroll/search
	 * @since 27/02/2022
	 */
	public function get_total_yearly($arrData)
	{
		$this->db->select("Y.*, CONCAT(U.first_name, ' ', U.last_name) employee");
		$this->db->join('user U', 'U.id_user = Y.fk_id_employee', 'INNER');

		if (array_key_exists("idUser", $arrData) && $arrData["idUser"] != '') {
			$this->db->where('Y.fk_id_employee', $arrData["idUser"]);
		}
		if (array_key_exists("year", $arrData)) {
			$this->db->where('Y.year', $arrData["year"]);
		}
		$this->db->order_by('U.first_name, U.last_name, year', 'asc');
		$query = $this->db->get('payroll_total_yearly Y');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Check In List
	 * @since 1/6/2022
	 */
	public function get_checkin($arrDatos)
	{
		$this->db->select();
		$this->db->join('new_workers W', 'W.id_worker = C.fk_id_worker', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = C.fk_id_job', 'INNER');
		if (array_key_exists("idCheckin", $arrDatos)) {
			$this->db->where('C.id_checkin', $arrDatos["idCheckin"]);
		}
		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('C.fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("today", $arrDatos)) {
			$this->db->where('C.checkin_date', $arrDatos["today"]);
		}
		if (array_key_exists("checkout", $arrDatos)) {
			$this->db->where('C.checkout_time', '0000-00-00 00:00:00');
		}
		$this->db->order_by('C.fk_id_job, C.id_checkin', 'asc');
		$query = $this->db->get('new_checkin C');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Count daily checkin people
	 * @author BMOTTAG
	 * @since  4/06/2022
	 */
	public function countCheckin()
	{
		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year));

		$sql = "SELECT count(id_checkin) CONTEO";
		$sql .= " FROM new_checkin";
		$sql .= " WHERE checkin_date >= '$firstDay'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->CONTEO;
	}

	/**
	 * Save banktime balance
	 * @since 9/9/2022
	 */
	public function saveBankTimeBalance($arrData)
	{
		$idUser = $this->session->userdata("id");

		$data = array(
			'fk_id_period' => $arrData["idPeriod"],
			'fk_id_employee' => $arrData["idEmployee"],
			'time_in' => $arrData["bankTimeAdd"],
			'time_out' => $arrData["bankTimeSubtract"],
			'balance' => $arrData["bankNewBalance"],
			'change_done_by' => $idUser,
			'observation' => $arrData["observation"],
			'date_issue' => date("Y-m-d G:i:s")
		);
		$query = $this->db->insert('payroll_bank_time', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Bank time list
	 * @since 09/7/2022
	 */
	public function get_bank_time($arrData)
	{
		$this->db->select("T.*, CONCAT(U.first_name, ' ', U.last_name) employee, CONCAT(W.first_name, ' ', W.last_name) done_by, X.period");
		$this->db->join('user U', 'U.id_user = T.fk_id_employee', 'INNER');
		$this->db->join('user W', 'W.id_user = T.change_done_by', 'INNER');
		$this->db->join('payroll_period X', 'X.id_period = T.fk_id_period', 'LEFT');

		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('T.fk_id_employee ', $arrData["idUser"]);
		}

		if (array_key_exists("limit", $arrData)) {
			$this->db->order_by("id_bank_time", "DESC");
			$query = $this->db->get('payroll_bank_time T', $arrData["limit"]);
		} else {
			$this->db->order_by("id_bank_time", "DESC");
			$query = $this->db->get('payroll_bank_time T');
		}

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get dayoff info
	 * @since 7/12/2016
	 * @review 6/2/2017
	 */
	public function get_day_off($arrData)
	{
		$idUser = $this->session->userdata("id");
		$firstDay = date('Y-m-d', strtotime('-6 month', time()));
		$actualDay = date('Y-m-d'); //dia actual
		$afterTommorrow = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));

		$beforeYesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));

		$this->db->select("D.*, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = D.fk_id_user', 'INNER');
		if (array_key_exists("idEmployee", $arrData)) {
			$this->db->where('U.id_user', $idUser); //filtro por empleado
		}
		if (array_key_exists("state", $arrData)) {
			$this->db->where('D.state', $arrData["state"]); //filtro por estado

			if ($arrData["state"] > 1) {
				$this->db->where('D.date_dayoff >=', $beforeYesterday); //filtro para los aprobados que la fecha de solicitud del permiso ya paso
			}
		}
		if (array_key_exists("idDayoff", $arrData)) {
			$this->db->where('D.id_dayoff', $arrData["idDayoff"]); //filtro por idDayoff
		}
		$this->db->where('D.date_issue >=', $firstDay); //filtro para el año actual
		$this->db->order_by('D.id_dayoff', 'desc');
		$query = $this->db->get('dayoff D');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * List of days off for Planning
	 * @since 06/02/2025
	 */
	public function get_day_off_planning($arrData)
	{

		$firstDay = date('Y-m-d', strtotime('-2 month', time()));
		$actualDay = date('Y-m-d'); //dia actual
		$afterTommorrow = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));

		$this->db->select("U.id_user, CONCAT(U.first_name, ' ', U.last_name) AS name, 
        GROUP_CONCAT(DATE_FORMAT(D.date_dayoff, '%d %b %Y') ORDER BY D.date_dayoff ASC SEPARATOR ', ') AS days_off");

		$this->db->join('user U', 'U.id_user = D.fk_id_user', 'INNER');
		//filtro para mostrar en el PLANNING los que son aprobados y el dia off en en los proximos dos dias o menos
		if (array_key_exists("forPlanning", $arrData)) {
			$this->db->where('D.state', 2); //filtro por aprobados

			$this->db->where('D.date_dayoff >=', $actualDay);
			$this->db->where('D.date_dayoff <=', $afterTommorrow);
		}
		$this->db->where('D.date_issue >=', $firstDay); //filtro para el año actual
		$this->db->group_by('U.id_user'); // Agrupar por empleado
		$this->db->order_by('U.first_name', 'asc');
		$query = $this->db->get('dayoff D');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Verificar si el job code ya existe en la base de datos
	 * @author BMOTTAG
	 * @since  30/12/2022
	 */
	public function jobCodeVerify($arrData)
	{
		if (array_key_exists("idJob", $arrData)) {
			$this->db->where('id_job !=', $arrData["idJob"]);
		}

		$this->db->where($arrData["column"], $arrData["value"]);
		$query = $this->db->get("param_jobs");

		if ($query->num_rows() >= 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Job Detail
	 * @author BMOTTAG
	 * @since  03/01/2023
	 */
	public function get_job_detail($arrData)
	{
		if (array_key_exists("idJobDetail", $arrData)) {
			$this->db->where('id_job_detail', $arrData["idJobDetail"]);
		}
		if (array_key_exists("idJob", $arrData)) {
			$this->db->where('fk_id_job', $arrData["idJob"]);
		}
		if (array_key_exists("chapterNumber", $arrData)) {
			$this->db->where('chapter_number', $arrData["chapterNumber"]);
		}
		if (array_key_exists("status", $arrData)) {
			$this->db->where('status', $arrData["status"]);
		}

		// Subconsulta para calcular la suma de los gastos
		$this->db->select('D.*, (SELECT ROUND(SUM(W.expense_value), 2) 
						FROM workorder_expense W 
						WHERE W.fk_id_job_detail = D.id_job_detail) AS expenses');

		$this->db->order_by('id_job_detail', 'asc');
		$query = $this->db->get('job_details D');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Job Detail
	 * @author BMOTTAG
	 * @since 13/05/2025
	 */
	public function get_job_detail_claims_info($arrData)
	{

		$this->db->select('D.*, C.quantity quantity_claim, C.cost');
		$this->db->join('claim_apus C', 'C.fk_id_job_detail = D.id_job_detail AND C.fk_id_claim = ' . $arrData["idClaim"], 'INNER');

		if (array_key_exists("idJobDetail", $arrData)) {
			$this->db->where('id_job_detail', $arrData["idJobDetail"]);
		}
		if (array_key_exists("idJob", $arrData)) {
			$this->db->where('fk_id_job', $arrData["idJob"]);
		}
		if (array_key_exists("chapterNumber", $arrData)) {
			$this->db->where('chapter_number', $arrData["chapterNumber"]);
		}
		if (array_key_exists("status", $arrData)) {
			$this->db->where('status', $arrData["status"]);
		}
		$this->db->order_by('id_job_detail', 'asc');
		$query = $this->db->get('job_details D');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get Distinct Chapter list
	 * @since 25/1/2023
	 */
	public function get_chapter_list($arrData)
	{
		$this->db->select('distinct(chapter_number), chapter_name');
		$this->db->where('fk_id_job', $arrData["idJob"]);
		$this->db->order_by('chapter_number', 'asc');
		$query = $this->db->get('job_details');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Sumatoria de gastos para un item
	 * Int idJobDetail
	 * @author BMOTTAG
	 * @since  24/1/2023
	 */
	public function sumExpense($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(expense_value),2) TOTAL";
		$sql .= " FROM workorder_expense W";
		$sql .= " WHERE W.fk_id_job_detail =" . $arrDatos["idJobDetail"];

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Sumatoria de valores de porcentage para un job
	 * Int idJob
	 * @author BMOTTAG
	 * @since  6/6/2023
	 */
	public function sumPercentageByJob($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(percentage),2) TOTAL";
		$sql .= " FROM job_details";
		$sql .= " WHERE fk_id_job =" . $arrDatos["idJob"];
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Get workorder expenses info
	 * @since 13/1/2023
	 */
	public function get_workorder_expense($arrData)
	{
		$this->db->join('workorder W', 'W.id_workorder = E.fk_id_workorder', 'INNER');
		$this->db->join('job_details J', 'J.id_job_detail = E.fk_id_job_detail', 'INNER');
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('E.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("idJobDetail", $arrData)) {
			$this->db->where('E.fk_id_job_detail', $arrData["idJobDetail"]);
		}
		$query = $this->db->get('workorder_expense E');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder expenses info
	 * @since 23/04/2025
	 */
	public function get_forceaccount_expense($arrData)
	{
		$this->db->join('forceaccount F', 'F.id_forceaccount = E.fk_id_forceaccount', 'INNER');
		$this->db->join('job_details J', 'J.id_job_detail = E.fk_id_job_detail', 'INNER');

		if (array_key_exists("idForceAccount", $arrData)) {
			$this->db->where('E.fk_id_forceaccount', $arrData["idForceAccount"]);
		}
		if (array_key_exists("idJobDetail", $arrData)) {
			$this->db->where('E.fk_id_job_detail', $arrData["idJobDetail"]);
		}
		$query = $this->db->get('forceaccount_expense E');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get validate user credentials
	 * @since 26/1/2023
	 */
	public function validateCredentials($arrData)
	{
		$login = str_replace(array("<", ">", "[", "]", "*", "^", "-", "'", "="), "", $arrData["login"]);
		$passwd = str_replace(array("<", ">", "[", "]", "*", "^", "-", "'", "="), "", $arrData["passwd"]);
		$passwd = md5($passwd);

		$this->db->select();
		$this->db->where('id_user', $arrData["idUser"]);
		$this->db->where('log_user', $login);
		$this->db->where('password', $passwd);
		$query = $this->db->get('user');

		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder expenses info
	 * @since 18/4/2023
	 */
	public function get_certificate_list($arrData)
	{
		if (array_key_exists("idCertificate", $arrData)) {
			$this->db->where('id_certificate', $arrData["idCertificate"]);
		}
		$this->db->order_by('certificate', 'asc');
		$query = $this->db->get('param_certificates');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Trucks list by company and type2
	 * @since 25/1/2017
	 */
	public function get_trucks_by_id2($idCompany, $type)
	{
		$trucks = array();
		$sql = "SELECT id_vehicle, CONCAT(unit_number,' -----> ', description) as unit_description
					FROM param_vehicle 
					WHERE fk_id_company = $idCompany AND type_level_2 = $type AND state = 1
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
	 * Count equipment by Type
	 * @author BMOTTAG
	 * @since  19/05/2023
	 */
	public function countEquipmentByType()
	{
		$this->db->select("inspection_type, header_inspection_type, count(id_vehicle) as number");
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = A.type_level_2', 'INNER');
		$this->db->where('A.state', 1);

		$this->db->group_by("T.inspection_type");
		$query = $this->db->get('param_vehicle A');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get vehicle oil change history
	 * last 30 records
	 * @since 17/1/2017
	 */
	public function get_vehicle_oil_change($infoVehicle)
	{
		$table = $infoVehicle[0]['table_inspection'] . ' T';
		$idTable = 'T.' . $infoVehicle[0]['id_table_inspection'];

		$this->db->select("A.*, T.comments, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = A.fk_id_user', 'INNER');
		$this->db->join("$table", "$idTable = A.fk_id_inspection", "LEFT");
		$this->db->where('A.fk_id_vehicle', $infoVehicle[0]['id_vehicle']);
		$this->db->order_by('id_oil_change', 'desc');
		$query = $this->db->get('vehicle_oil_change A');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get Chat
	 * @since 29/5/2023
	 */
	public function get_chat_info($arrData)
	{
		$this->db->select("C.*, U.first_name user_from, W.first_name user_to");
		$this->db->join('user U', 'U.id_user = C.fk_id_user_from', 'INNER');
		$this->db->join('user W', 'W.id_user = C.fk_id_user_to', 'LEFT');
		if (array_key_exists("idChat", $arrData)) {
			$this->db->where('C.id_chat', $arrData["idChat"]);
		}
		if (array_key_exists("idModule", $arrData)) {
			$this->db->where('C.fk_id_module', $arrData["idModule"]);
		}
		if (array_key_exists("module", $arrData)) {
			$this->db->where('C.module', $arrData["module"]);
		}
		$this->db->order_by('C.id_chat', 'asc');
		$query = $this->db->get('chat C');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Save Chat
	 * @since 29/5/2023
	 */
	public function saveChat($arrData)
	{
		$data = array(
			'fk_id_module' => $arrData["fk_id_module"],
			'module' => $arrData["module"],
			'fk_id_user_from' => $this->session->userdata("id"),
			'created_at' => date("Y-m-d G:i:s"),
			'message' => $arrData["message"]
		);
		$query = $this->db->insert('chat', $data);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Count SO by status and by Year
	 * @author BMOTTAG
	 * @since  14/06/2023
	 */
	public function countSOByStatus()
	{
		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year));

		$this->db->select("status_name, status_slug, status_style, status_icon, count(id_service_order) as number");
		$this->db->join('param_status P', 'P.status_slug = S.service_status', 'INNER');
		$this->db->where('P.status_key', 'serviceorder');
		$this->db->where('S.created_at >=', $firstDay);
		$this->db->group_by("S.service_status");
		$query = $this->db->get('service_order S');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Equipment by Type
	 * @author BMOTTAG
	 * @since  24/06/2023
	 */
	public function equipmentByTypeList()
	{
		$this->db->select();
		$this->db->group_by("inspection_type");
		$query = $this->db->get('param_vehicle_type_2');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Attachments by Equipment ID
	 * @since 28/6/2023
	 */
	public function get_attachments_by_equipment($arrDatos)
	{
		$this->db->select('id_attachment, attachment_number, attachment_description');
		$this->db->join('param_attachments_equipment A', 'A.fk_id_attachment = P.id_attachment', 'INNER');
		if (array_key_exists("idEquipment", $arrDatos)) {
			$this->db->where('fk_id_equipment', $arrDatos["idEquipment"]);
		}
		$this->db->order_by('attachment_number', 'asc');
		$query = $this->db->get('param_attachments P');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Search user by movil for planning
	 * @since 27/8/2023
	 */
	public function get_programming_user($arrData)
	{
		$this->db->select("id_programming_worker, CONCAT(U.first_name, ' ', U.last_name) as employee, Z.movil, P.date_programming, H.hora");
		$this->db->where('confirmation', 2);
		$this->db->where('sms_sent', 1);
		if (array_key_exists("movil", $arrData)) {
			$this->db->where('U.movil', $arrData["movil"]);
		}
		$this->db->join('user U', 'U.id_user = W.fk_id_programming_user', 'INNER');
		$this->db->join('programming P', 'P.id_programming = W.fk_id_programming', 'INNER');
		$this->db->join('user Z', 'Z.id_user = P.fk_id_user', 'INNER');
		$this->db->join('param_horas H', 'H.id_hora = W.fk_id_hour', 'LEFT');
		$this->db->order_by("W.id_programming_worker", "desc");
		$this->db->limit(1);

		$query = $this->db->get("programming_worker W");

		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Info Planning for the Employee
	 * @since 27/8/2023
	 */
	public function get_planning_for_employee($arrData)
	{
		$this->db->select("P.date_programming, P.observation, X.job_description, W.*, CONCAT(V.unit_number,' -----> ', V.description) as unit_description, H.hora");
		$this->db->join('programming_worker W', 'W.fk_id_programming = P.id_programming', 'INNER');
		$this->db->join('param_jobs X', 'X.id_job = P.fk_id_job', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = W.fk_id_machine', 'LEFT'); //!AQUI
		$this->db->join('param_horas H', 'H.id_hora = W.fk_id_hour', 'LEFT');

		if (array_key_exists("idUser", $arrData)) {
			$this->db->where('W.fk_id_programming_user', $arrData["idUser"]);
		}
		if (array_key_exists("fecha", $arrData)) {
			$this->db->where('P.date_programming', $arrData["fecha"]);
		}
		if (array_key_exists("nextPlanning", $arrData)) {
			$this->db->where('P.date_programming >=', date('Y-m-d'));
		}
		$this->db->where('P.state !=', 3);


		$this->db->order_by("P.date_programming ASC");
		$query = $this->db->get("programming P");

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else
			return false;
	}

	public function get_vehicle_info_for_planning($arrData)
	{
		if (array_key_exists("forTextMessague", $arrData)) {
			$sql = "SELECT GROUP_CONCAT(unit_number, '-', description SEPARATOR ' \n') AS unit_description FROM param_vehicle WHERE id_vehicle IN (" . $arrData['idValues'] . ")";
		} else {
			$sql = "SELECT GROUP_CONCAT(unit_number, '-', description SEPARATOR '<br>') AS unit_description FROM param_vehicle WHERE id_vehicle IN (" . $arrData['idValues'] . ")";
		}

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Get JOBs 
	 * @since 03/01/2025
	 */
	public function get_job($arrData)
	{
		$this->db->join('param_company C', 'C.id_company = J.fk_id_company', 'LEFT');
		$this->db->join('param_company_foreman F', 'F.fk_id_job = J.id_job', 'LEFT');
		if (array_key_exists("idJob", $arrData)) {
			$this->db->where('J.id_job', $arrData["idJob"]);
		}
		if (array_key_exists("state", $arrData)) {
			$this->db->where('state', $arrData["state"]);
		}
		if (array_key_exists("withLIC", $arrData)) {
			$this->db->where('flag_upload_details', 1);
		}
		$this->db->order_by("J.job_description", "asc");
		$query = $this->db->get("param_jobs J");

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get Companys List
	 * @since 13/02/2025
	 */
	public function get_company($arrData)
	{
		if (array_key_exists("idCompany", $arrData)) {
			$this->db->where('C.id_company', $arrData["idCompany"]);
		}
		if (array_key_exists("company_type", $arrData)) {
			$this->db->where('C.company_type', $arrData["company_type"]);
		}
		if (array_key_exists("allSubcontractors", $arrData)) {
			$types = array(2, 3);
			$this->db->where_in('C.company_type', $types);
		}
		if (array_key_exists("isHauling", $arrData)) {
			$this->db->where('C.does_hauling', 1);
		}
		$this->db->order_by("C.company_name", "asc");
		$query = $this->db->get("param_company C");

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	public function get_without_work_order()
	{
		/*$sql = "SELECT u.first_name, u.last_name, j.job_description, t.*
				FROM task t
				JOIN user u ON t.fk_id_user = u.id_user
				JOIN param_jobs j ON t.fk_id_job_finish = j.id_job
				WHERE t.wo_end_project IS NULL
				AND t.hours_end_project IS NOT NULL
				AND t.hours_end_project <> 0
				ORDER BY t.id_task DESC;
			";*/

		$sql = "SELECT u.first_name, u.last_name, j.job_description, t.*
				FROM task t
				JOIN user u ON t.fk_id_user = u.id_user
				JOIN param_jobs j ON t.fk_id_job_finish = j.id_job
				WHERE t.wo_end_project IS NULL
					AND t.hours_end_project IS NOT NULL
					AND t.hours_end_project <> 0
					AND t.start >= '2025-01-01'
				UNION ALL
				-- Registros donde fk_id_programming ES NULL y también cumplen con la validación de fecha
				SELECT u.first_name, u.last_name, j.job_description, t.*
				FROM task t
				JOIN user u ON t.fk_id_user = u.id_user
				JOIN param_jobs j ON t.fk_id_job = j.id_job
				WHERE t.fk_id_programming IS NULL
					AND t.start >= '2025-01-01'  -- Validación de fecha
				ORDER BY id_task DESC;";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Task list, to check with WO
	 * Modules: Dashboard - Payroll
	 * @since 21/02/2025
	 */
	public function get_task_in_danger($arrData)
	{
		$this->db->select('T.*, id_user, first_name, last_name, log_user, J.job_description job_start, H.job_description job_finish, O.task');
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('param_jobs H', 'H.id_job = T.fk_id_job_finish', 'LEFT');
		$this->db->join('param_operation O', 'O.id_operation = T.fk_id_operation', 'INNER');

		$this->db->where('T.finish !=', "0000-00-00 00:00:00");
		if (array_key_exists("fecha", $arrData)) {
			$this->db->like('T.start', $arrData["fecha"]);
		}
		$this->db->order_by('id_task', 'desc');
		$query = $this->db->get('task T');


		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update fields WO in the task table
	 * @since 21/01/2025
	 */
	public function updateWOTasks($arrDatos)
	{
		$data = ($arrDatos["column"] === "bothColumns") ? 
			["wo_start_project" => $arrDatos["value"], "wo_end_project" => $arrDatos["value"]] : 
			[$arrDatos["column"] => $arrDatos["value"]];

		return $this->db->where("id_task", $arrDatos["id"])->update("task", $data);
	}

	/**
	 * Sumatoria de horas de personal for Calendar
	 * @param $idWorkorder
	 * @param $idUser
	 * @author BMOTTAG
	 * @since  24/02/2025
	 */
	public function countHoursPersonal($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(hours),2) TOTAL";
		$sql .= " FROM workorder_personal P";
		$sql .= " WHERE P.fk_id_workorder =" . $arrDatos["idWorkorder"];
		$sql .= " AND P.fk_id_user =" . $arrDatos["idUser"];

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Sumatoria de horas de personal en los equipos for Calendar
	 * @param $idWorkorder
	 * @param $idUser
	 * @author BMOTTAG
	 * @since  24/02/2025
	 */
	public function countHoursEquipmentPersonal($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(hours),2) TOTAL";
		$sql .= " FROM workorder_equipment P";
		$sql .= " WHERE P.fk_id_workorder =" . $arrDatos["idWorkorder"];
		$sql .= " AND P.operatedby =" . $arrDatos["idUser"];

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Sumatoria total de costos para un job_detail
	 * @param $idJobDetail
	 * @author BMOTTAG
	 * @since  15/05/2025
	 */
	public function get_total_cost_by_job_detail($arrData) {
		$this->db->select_sum('cost');
		$this->db->from('claim_apus');
		$this->db->where('fk_id_job_detail', $arrData["idJobDetail"]);
		$query = $this->db->get();
	
		if ($query->num_rows() > 0) {
			return $query->row()->cost ?? 0;
		} else {
			return 0;
		}
	}

	/**
	 * Get get_claims_by_id_job_detail
	 * @since 16/05/2025
	 */
	public function get_claims_by_id_job_detail($arrData)
	{
		$this->db->select('C.claim_number, A.quantity, A.cost');
		$this->db->join('claim C', 'C.id_claim = A.fk_id_claim', 'INNER');
		if (array_key_exists("idJobDetail", $arrData)) {
			$this->db->where('A.fk_id_job_detail', $arrData["idJobDetail"]);
		}
		$query = $this->db->get('claim_apus A');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Task list for Payroll Check
	 * Modules: Payroll payroll_check
	 * @since 19/08/2025
	 */
	public function get_payroll_check()
	{
		$this->db->select('T.*, id_user, first_name, last_name, log_user, J.job_description job_start, H.job_description job_finish, O.task');
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('param_jobs H', 'H.id_job = T.fk_id_job_finish', 'LEFT');
		$this->db->join('param_operation O', 'O.id_operation = T.fk_id_operation', 'INNER');
		//$this->db->where('T.start >=', '2023-06-01');
		//$this->db->where('T.start <', '2025-06-01');
		$this->db->where('T.finish', '0000-00-00 00:00:00');

		$this->db->order_by('id_task', 'desc');

		$query = $this->db->get('task T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
}
