<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model
{

	/**
	 * Payroll
	 * @since 24/11/2016
	 */
	public function get_payroll($arrData)
	{
		$this->db->select("T.*, CONCAT(first_name, ' ', last_name) name, J.job_description job_start, H.job_description job_finish");
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('param_jobs H', 'H.id_job = T.fk_id_job_finish', 'LEFT');

		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('U.id_user', $arrData["employee"]);
		}

		if (array_key_exists("from", $arrData)) {
			$this->db->where('T.start >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData)) {
			$this->db->where('T.start <=', $arrData["to"]);
		}

		if (array_key_exists("idPayroll", $arrData)) {
			$this->db->where('T.id_task', $arrData["idPayroll"]);
		}

		$this->db->order_by('T.start', 'asc');
		$query = $this->db->get('task T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Payroll
	 * @since 6/01/2017
	 */
	public function get_safety($arrData)
	{
		$this->db->select("S.*, CONCAT(first_name, ' ', last_name) name, J.job_description");
		$this->db->join('user U', 'U.id_user = S.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = S.fk_id_job', 'INNER');

		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('S.date >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('S.date <=', $arrData["to"]);
		}
		if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 'x') {
			$this->db->where('S.fk_id_job', $arrData["jobId"]);
		}
		if (array_key_exists("idSafety", $arrData) && $arrData["idSafety"] != '' && $arrData["idSafety"] != 'x') {
			$this->db->where('S.id_safety', $arrData["idSafety"]);
		}

		$this->db->order_by('S.date', 'asc');
		$query = $this->db->get('safety S');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Near miss list
	 * @since 20/10/2024
	 */
	public function get_near_miss($arrData) 
	{		
			$this->db->select('W.*, CONCAT(U.first_name, " " , U.last_name) name, J.id_job, job_description, T.*, CONCAT(X.first_name, " " , X.last_name) supervisor, CONCAT(Y.first_name, " " , Y.last_name) coordinator');
			$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
			$this->db->join('param_incident_type T', 'T.id_incident_type = W.fk_incident_type', 'INNER');
			$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
			$this->db->join('user X', 'X.id_user = W.manager_user', 'INNER');
			$this->db->join('user Y', 'Y.id_user = W.safety_user', 'INNER');
			if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
				$this->db->where('W.date_issue >=', $arrData["from"]);
			}
			if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
				$this->db->where('W.date_issue <=', $arrData["to"]);
			}
			if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 'x') {
				$this->db->where('W.fk_id_job', $arrData["jobId"]);
			}

			$this->db->order_by('id_near_miss', 'desc');
			$query = $this->db->get('incidence_near_miss W');

			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
	}

		/**
		 * Incident list
		 * @since 20/10/2024
		 */
		public function get_incident($arrData) 
		{
			$this->db->select('W.*, T.*, J.id_job, job_description, CONCAT(U.first_name, " " , U.last_name) name, CONCAT(X.first_name, " " , X.last_name) supervisor, CONCAT(Y.first_name, " " , Y.last_name) coordinator');
			$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'LEFT');
			$this->db->join('param_incident_type T', 'T.id_incident_type = W.fk_incident_type', 'INNER');
			$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
			$this->db->join('user X', 'X.id_user = W.manager_user', 'INNER');
			$this->db->join('user Y', 'Y.id_user = W.safety_user', 'INNER');
			
			if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
				$this->db->where('W.date_issue >=', $arrData["from"]);
			}
			if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
				$this->db->where('W.date_issue <=', $arrData["to"]);
			}
			if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 'x') {
				$this->db->where('W.fk_id_job', $arrData["jobId"]);
			}

			$this->db->order_by('id_incident', 'desc');
			$query = $this->db->get('incidence_incident W');

			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
		}

	/**
	 * Payroll
	 * @since 6/01/2017
	 */
	public function get_csep($arrData)
	{
		$this->load->model("general_model");
		//tool box info
		$arrParam = array(
			"idJob" => $arrData["jobId"],
			"from" => $arrData["from"],
			"to" => $arrData["to"],
		);

		$data['information'] = $this->general_model->get_confined_space($arrParam);

		if ($data['information']) {
			return $data['information'];
		} else {
			return false;
		}
	}

	/**
	 * Get safety hazard info
	 * @since 4/12/2016
	 */
	public function get_safety_hazard($idSafety)
	{
		$this->db->select();
		$this->db->join('param_hazard PH', 'PH.id_hazard = H.fk_id_hazard', 'INNER');
		$this->db->join('param_hazard_activity PA', 'PA.id_hazard_activity = PH.fk_id_hazard_activity', 'INNER');
		$this->db->join('param_hazard_priority PP', 'PP.id_priority = PH.fk_id_priority', 'INNER');
		$this->db->where('H.fk_id_safety', $idSafety); 
		$this->db->order_by('PA.id_hazard_activity, PH.hazard_description', 'asc');
		$query = $this->db->get('safety_hazards H');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get safety workers info
	 * @since 6/12/2016
	 */
	public function get_safety_workers($idSafety)
	{
		$this->db->select("W.id_safety_worker, W.fk_id_safety, W.signature, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->where('W.fk_id_safety', $idSafety);
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('safety_workers W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get safety subcontractors info
	 * @since 27/2/2017
	 */
	public function get_safety_subcontractors($idSafety)
	{
		$this->db->select("W.*, C.company_name");
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'INNER');
		$this->db->where('W.fk_id_safety', $idSafety);
		$this->db->order_by('C.company_name', 'asc');
		$query = $this->db->get('safety_workers_subcontractor W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Hauling
	 * @since 6/01/2017
	 */
	public function get_hauling($arrData)
	{
		$this->db->select("H.*, CONCAT(first_name, ' ', last_name) name, C.company_name, V.unit_number, T.truck_type, J.job_description site_from, Z.job_description site_to, M.material, P.payment");
		$this->db->join('user U', 'U.id_user = H.fk_id_user', 'INNER');
		$this->db->join('param_company C', 'C.id_company = H.fk_id_company', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = H.fk_id_truck', 'LEFT');
		$this->db->join('param_truck_type T', 'T.id_truck_type = H.fk_id_truck_type', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = H.fk_id_site_from', 'INNER');
		$this->db->join('param_jobs Z', 'Z.id_job = H.fk_id_site_to', 'INNER');
		$this->db->join('param_material_type M', 'M.id_material = H.fk_id_material', 'INNER');
		$this->db->join('param_payment P', 'P.id_payment = H.fk_id_payment', 'INNER');

		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('U.id_user', $arrData["employee"]);
		}

		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('V.id_vehicle', $arrData["vehicleId"]);
		}

		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('H.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('H.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("company", $arrData) && $arrData["company"] != 'x') {
			$this->db->where('H.fk_id_company', $arrData["company"]);
		}
		if (array_key_exists("material", $arrData) && $arrData["material"] != 'x') {
			$this->db->where('H.fk_id_material', $arrData["material"]);
		}
		if (array_key_exists("idHauling", $arrData) && $arrData["idHauling"] != 'x') {
			$this->db->where('H.id_hauling', $arrData["idHauling"]);
		}
		if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != 'x') {
			$this->db->where('H.fk_id_site_from', $arrData["jobId"]);
		}

		$this->db->order_by('H.id_hauling', 'desc');
		$query = $this->db->get('hauling H');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Vehicle by tipo de inspeccion
	 * @since 8/03/2017
	 */
	public function get_vehicle_by_type($arrData)
	{
		$this->db->select();
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = V.type_level_2', 'INNER');
		$this->db->join('param_company C', 'C.id_company = V.fk_id_company', 'INNER');
		$this->db->where('V.state', 1); //filtro por activos
		if (array_key_exists("tipo", $arrData) && $arrData["tipo"] == "daily") {
			$where = "T.inspection_type IN (1,3)";
			$this->db->where($where);
		} elseif (array_key_exists("tipo", $arrData) && $arrData["tipo"] == "heavy") {
			$this->db->where('T.inspection_type', 2);
		} elseif (array_key_exists("tipo", $arrData) && $arrData["tipo"] == "trailer") {
			$this->db->where('V.type_level_2', 5);
		} elseif (array_key_exists("tipo", $arrData) && $arrData["tipo"] == "special") {
			$this->db->where('T.inspection_type', 4);
		}

		if (array_key_exists("company_type", $arrData)) {
			$this->db->where('C.company_type', 1);
		}

		$this->db->order_by('T.inspection_type, V.unit_number', 'asc');
		$query = $this->db->get('param_vehicle V');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * daily inspection
	 * @since 6/01/2017
	 */
	public function get_daily_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*, CONCAT(T.unit_number, ' - ', T.description) trailer, TY.type_2, TY.inspection_type");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');
		$this->db->join('param_vehicle T', 'T.id_vehicle = I.fk_id_trailer', 'LEFT');
		$this->db->join('param_vehicle_type_2 TY', 'TY.id_type_2 = V.type_level_2', 'INNER');
		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('I.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('I.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('I.fk_id_user', $arrData["employee"]);
		}
		if (array_key_exists("idInspection", $arrData) && $arrData["idInspection"] != 'x') {
			$this->db->where('I.id_inspection_daily', $arrData["idInspection"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('I.fk_id_vehicle', $arrData["vehicleId"]);
		}
		if (array_key_exists("trailerId", $arrData) && $arrData["trailerId"] != 'x') {
			$this->db->where('I.fk_id_trailer', $arrData["trailerId"]);
		}

		$this->db->order_by('I.date_issue', 'asc');
		$query = $this->db->get('inspection_daily I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * heavy inspection
	 * @since 7/01/2017
	 */
	public function get_heavy_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*, TY.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');
		$this->db->join('param_vehicle_type_2 TY', 'TY.id_type_2 = V.type_level_2', 'INNER');
		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('I.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('I.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('I.fk_id_user', $arrData["employee"]);
		}
		if (array_key_exists("idInspection", $arrData) && $arrData["idInspection"] != 'x') {
			$this->db->where('I.id_inspection_heavy', $arrData["idInspection"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('I.fk_id_vehicle', $arrData["vehicleId"]);
		}

		$this->db->order_by('I.date_issue', 'asc');
		$query = $this->db->get('inspection_heavy I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * sweeper inspection
	 * @since 23/04/2017
	 */
	public function get_sweeper_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*, TY.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');
		$this->db->join('param_vehicle_type_2 TY', 'TY.id_type_2 = V.type_level_2', 'INNER');

		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('I.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('I.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('I.fk_id_user', $arrData["employee"]);
		}
		if (array_key_exists("idInspection", $arrData) && $arrData["idInspection"] != 'x') {
			$this->db->where('I.id_inspection_sweeper', $arrData["idInspection"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('I.fk_id_vehicle', $arrData["vehicleId"]);
		}

		$this->db->order_by('I.date_issue', 'asc');
		$query = $this->db->get('inspection_sweeper I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * hydrovac inspection
	 * @since 23/04/2017
	 */
	public function get_hydrovac_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*, TY.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');
		$this->db->join('param_vehicle_type_2 TY', 'TY.id_type_2 = V.type_level_2', 'INNER');

		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('I.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('I.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('I.fk_id_user', $arrData["employee"]);
		}
		if (array_key_exists("idInspection", $arrData) && $arrData["idInspection"] != 'x') {
			$this->db->where('I.id_inspection_hydrovac', $arrData["idInspection"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('I.fk_id_vehicle', $arrData["vehicleId"]);
		}

		$this->db->order_by('I.date_issue', 'asc');
		$query = $this->db->get('inspection_hydrovac I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * generator inspection
	 * @since 23/04/2017
	 */
	public function get_generator_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*, TY.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');
		$this->db->join('param_vehicle_type_2 TY', 'TY.id_type_2 = V.type_level_2', 'INNER');

		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('I.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('I.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('I.fk_id_user', $arrData["employee"]);
		}
		if (array_key_exists("idInspection", $arrData) && $arrData["idInspection"] != 'x') {
			$this->db->where('I.id_inspection_generator', $arrData["idInspection"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('I.fk_id_vehicle', $arrData["vehicleId"]);
		}

		$this->db->order_by('I.date_issue', 'asc');
		$query = $this->db->get('inspection_generator I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * sweeper inspection
	 * @since 29/06/2017
	 */
	public function get_water_truck_inspection($arrData)
	{
		$this->db->select("I.*, CONCAT(first_name, ' ', last_name) name, V.*, TY.*");
		$this->db->join('user U', 'U.id_user = I.fk_id_user', 'INNER');
		$this->db->join('param_vehicle V', 'V.id_vehicle = I.fk_id_vehicle', 'INNER');
		$this->db->join('param_vehicle_type_2 TY', 'TY.id_type_2 = V.type_level_2', 'INNER');

		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('I.date_issue >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('I.date_issue <=', $arrData["to"]);
		}
		if (array_key_exists("employee", $arrData) && $arrData["employee"] != 'x') {
			$this->db->where('I.fk_id_user', $arrData["employee"]);
		}
		if (array_key_exists("idInspection", $arrData) && $arrData["idInspection"] != 'x') {
			$this->db->where('I.id_inspection_watertruck', $arrData["idInspection"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('I.fk_id_vehicle', $arrData["vehicleId"]);
		}

		$this->db->order_by('I.date_issue', 'asc');
		$query = $this->db->get('inspection_watertruck I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Work Order
	 * @since 26/01/2017
	 */
	public function get_workorder($arrData)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, J.job_description");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');

		if (array_key_exists("from", $arrData)) {
			$this->db->where('W.date >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData)) {
			$this->db->where('W.date <=', $arrData["to"]);
		}
		if (array_key_exists("jobId", $arrData) && $arrData["jobId"] != '' && $arrData["jobId"] != 'x') {
			$this->db->where('W.fk_id_job', $arrData["jobId"]);
		}
		if (array_key_exists("idWorkOrder", $arrData)) {
			$this->db->where('W.id_workorder', $arrData["idWorkOrder"]);
		}

		$this->db->order_by('W.id_workorder', 'asc');
		$query = $this->db->get('workorder W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder personal info
	 * @since 13/1/2017
	 */
	public function get_workorder_personal($idWorkorder)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, T.employee_type as type");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->join('param_employee_type T', 'T.id_employee_type = W.fk_id_employee_type', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('workorder_personal W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder materials info
	 * @since 13/1/2017
	 */
	public function get_workorder_materials($idWorkorder)
	{
		$this->db->select();
		$this->db->join('param_material_type M', 'M.id_material = W.fk_id_material', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$this->db->order_by('M.material', 'asc');
		$query = $this->db->get('workorder_materials W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder personal info
	 * @since 25/1/2017
	 */
	public function get_workorder_equipment($idWorkorder)
	{
		$this->db->select('W.*, V.unit_number, V.description v_description, M.miscellaneous, T.type_2');
		$this->db->join('param_vehicle V', 'V.id_vehicle = W.fk_id_vehicle', 'INNER');
		$this->db->join('param_company C', 'C.id_company = V.fk_id_company', 'INNER');
		$this->db->join('param_miscellaneous M', 'M.id_miscellaneous = W.fk_id_vehicle', 'LEFT');
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = W.fk_id_type_2', 'INNER');
		$this->db->where('W.fk_id_workorder', $idWorkorder);
		$this->db->order_by('C.company_name', 'asc');
		$query = $this->db->get('workorder_equipment W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get workorder Ocasional info
	 * @since 27/2/2017
	 */
	public function get_workorder_ocasional($idWorkorder)
	{
		$this->db->select('O.*, C.company_name');
		$this->db->join('param_company C', 'C.id_company = O.fk_id_company', 'INNER');
		$this->db->where('O.fk_id_workorder', $idWorkorder);
		$this->db->order_by('C.company_name', 'asc');
		$query = $this->db->get('workorder_ocasional O');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Trucks´list
	 * @since 7/1/2020
	 */
	public function get_trucks()
	{
		$trucks = array();
		$sql = "SELECT id_vehicle, CONCAT(unit_number,' -----> ', description) as unit_description 
					FROM param_vehicle 
					WHERE type_level_2 = 4
					AND state = 1
					ORDER BY unit_number";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get safety COVID
	 * @since 15/4/2021
	 */
	public function get_safety_covid($idSafety)
	{
		$this->db->select();
		$this->db->where('W.fk_id_safety', $idSafety);
		$query = $this->db->get('safety_covid W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * maintenance
	 * @since 25/05/2022
	 */
	public function get_maintenance($arrData)
	{
		$this->db->select("M.date_maintenance, M.maintenance_description, M.done_by, M.next_hours_maintenance, M.next_date_maintenance, M.maintenance_state, M.fk_id_vehicle, S.stock_description, T.maintenance_type, V.make, V.description");
		$this->db->join('maintenance_type T', 'T.id_maintenance_type = M.fk_id_maintenance_type', 'INNER');
		$this->db->join('stock S', 'S.id_stock = M.fk_id_stock ', 'LEFT');
		$this->db->join('param_vehicle V', 'V.id_vehicle = M.fk_id_vehicle ', 'INNER');
		if (array_key_exists("from", $arrData) && $arrData["from"] != 'x') {
			$this->db->where('M.date_maintenance >=', $arrData["from"]);
		}
		if (array_key_exists("to", $arrData) && $arrData["to"] != 'x') {
			$this->db->where('M.date_maintenance <=', $arrData["to"]);
		}
		if (array_key_exists("vehicleId", $arrData) && $arrData["vehicleId"] != 'x') {
			$this->db->where('M.fk_id_vehicle', $arrData["vehicleId"]);
		}
		if (array_key_exists("maitenanceType", $arrData)) {
			$this->db->where('M.fk_id_maintenance_type', $arrData["maitenanceType"]);
		}


		$this->db->order_by('M.id_maintenance', 'asc');
		$query = $this->db->get('maintenance M');

		if ($query->num_rows() > 0) {
			return $query->result_array();
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
			$this->db->where('O.fk_id_workorder', $arrData["idWorkOrder"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('O.view_pdf', 1);
		}
		$this->db->order_by('O.place', 'asc');
		$query = $this->db->get('workorder_receipt O');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
}
