<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Jobs_model extends CI_Model
{


	/**
	 * Add tool box
	 * @since 24/10/2017
	 */
	public function add_TOOLBOX()
	{
		$idUser = $this->session->userdata("id");
		$idToolBox = $this->input->post('hddIdentificador');

		$data = array(
			'new_safety' => $this->input->post('newSafety'),
			'activities' => $this->input->post('activities'),
			'suggestions' => $this->input->post('suggestions'),
			'corrective_actions' => $this->input->post('correctiveActions')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');

		//revisar si es para adicionar o editar
		if ($idToolBox == '') {
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job'] = $this->input->post('hddIdJob');

			//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
			$data['date_tool_box'] = date("Y-m-d G:i:s");
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_tool_box'] = $dateIssue;
			}

			$query = $this->db->insert('tool_box', $data);
			$idToolBox = $this->db->insert_id();
		} else {
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_tool_box'] = $dateIssue;
			}

			$this->db->where('id_tool_box', $idToolBox);
			$query = $this->db->update('tool_box', $data);
		}

		if ($query) {
			return $idToolBox;
		} else {
			return false;
		}
	}

	/**
	 * Add ERP
	 * @since 20/11/2017
	 */
	public function add_erp()
	{
		$idUser = $this->session->userdata("id");
		$idERP = $this->input->post('hddIdentificador');

		$data = array(
			'address' => $this->input->post('address'),
			'responsible_user' => $this->input->post('responsible'),
			'coordinator_user' => $this->input->post('coordinator'),
			'fire_department' => $this->input->post('fire_department'),
			'paramedics' => $this->input->post('paramedics'),
			'ambulance' => $this->input->post('ambulance'),
			'police' => $this->input->post('police'),
			'federal_protective' => $this->input->post('federal_protective'),
			'security' => $this->input->post('security'),
			'manager' => $this->input->post('manager'),
			'electric' => $this->input->post('electric'),
			'water' => $this->input->post('water'),
			'gas' => $this->input->post('gas'),
			'emergency_user_1' => $this->input->post('contact1'),
			'emergency_user_2' => $this->input->post('contact2'),
			'voice' => $this->input->post('voice'),
			'radio' => $this->input->post('radio'),
			'phone' => $this->input->post('phone'),
			'other' => $this->input->post('other'),
			'specify' => $this->input->post('specify'),
			'location' => $this->input->post('location'),
			'location2' => $this->input->post('location2'),
			'location3' => $this->input->post('location3'),
			'directions' => $this->input->post('directions')

		);

		//revisar si es para adicionar o editar
		if ($idERP == '') {
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job'] = $this->input->post('hddIdJob');
			$data['date_erp'] = date("Y-m-d G:i:s");
			$query = $this->db->insert('erp', $data);
		} else {
			$this->db->where('id_erp', $idERP);
			$query = $this->db->update('erp', $data);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add new hazard
	 * @since 25/10/2017
	 */
	public function saveNewHazard()
	{
		$data = array(
			'fk_id_tool_box' => $this->input->post('hddidToolBox'),
			'hazard' => $this->input->post('hazard'),
			'hazard_type' => $this->input->post('hazardType'),
			'actions' => $this->input->post('actions')
		);

		$query = $this->db->insert('tool_box_new_hazard', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get tool box new hazards info
	 * @since 25/10/2017
	 */
	public function get_new_hazards($idToolBox)
	{
		$this->db->select();
		$this->db->where('W.fk_id_tool_box', $idToolBox);
		$this->db->order_by('W.hazard', 'asc');
		$query = $this->db->get('tool_box_new_hazard W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update NEW HAZARD
	 * @since 25/10/2017
	 */
	public function updateNewHazard()
	{
		$idNewHazard = $this->input->post('hddIdNewHazard');

		$data = array(
			'hazard' => $this->input->post('hazard'),
			'hazard_type' => $this->input->post('hazardType'),
			'actions' => $this->input->post('actions')
		);

		$this->db->where('id_new_hazard', $idNewHazard);
		$query = $this->db->update('tool_box_new_hazard', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @author BMOTTAG
	 * @since 2/11/2017
	 * Consulta de empleados para un tool box especifico
	 */
	public function get_toolbox_byIdworker_byIdToolBox($idToolBox, $idWorker)
	{
		$this->db->where('fk_id_tool_box', $idToolBox);
		$this->db->where('fk_id_user', $idWorker);
		$query = $this->db->get('tool_box_workers');
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Add TOOL BOX WORKER
	 * @since 2/11/2017
	 */
	public function add_tool_box_worker($idToolBox)
	{
		//add the new workers
		$query = 1;
		if ($workers = $this->input->post('workers')) {
			$tot = count($workers);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_tool_box' => $idToolBox,
					'fk_id_user' => $workers[$i]
				);
				$query = $this->db->insert('tool_box_workers', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Get tool box workers info
	 * @since 2/11/2017
	 */
	public function get_tool_box_workers($idToolBox)
	{
		$this->db->select("W.id_tool_box_worker, W.fk_id_tool_box, W.signature, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->where('W.fk_id_tool_box', $idToolBox);
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('tool_box_workers W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
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
	 * ERP
	 * @since 20/11/2017
	 */
	public function get_erp($arrDatos)
	{
		$this->db->select('E.*, U.movil phone_res, X.movil phone_co, CONCAT(U.first_name, " " , U.last_name) responsible, CONCAT(X.first_name, " " , X.last_name) coordinator, J.id_job, J.job_description,
Y.movil phone_emer_1, CONCAT(Y.first_name, " " , Y.last_name) emer_1, Z.movil phone_emer_2, CONCAT(Z.first_name, " " , Z.last_name) emer_2');
		$this->db->join('param_jobs J', 'J.id_job = E.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = E.responsible_user', 'INNER');
		$this->db->join('user X', 'X.id_user = E.coordinator_user', 'INNER');
		$this->db->join('user Y', 'Y.id_user = E.emergency_user_1', 'INNER');
		$this->db->join('user Z', 'Z.id_user = E.emergency_user_2', 'INNER');
		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("idERP", $arrDatos)) {
			$this->db->where('id_erp', $arrDatos["idERP"]);
		}

		$query = $this->db->get('erp E');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add ERP TRAINING WORKER
	 * @since 23/11/2017
	 */
	public function add_training_worker($idJob)
	{
		//add the new workers
		$query = 1;
		if ($workers = $this->input->post('workers')) {
			$tot = count($workers);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_job' => $idJob,
					'fk_id_user' => $workers[$i]
				);
				$query = $this->db->insert('erp_training_workers', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get ERP training workers info
	 * @since 23/11/2017
	 */
	public function get_erp_training_workers($idJob)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, U.*");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->where('W.fk_id_job', $idJob);
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('erp_training_workers W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * @author BMOTTAG
	 * @since 23/11/2017
	 * Consulta de empleados para un job especifico
	 */
	public function get_erp_training_byIdworker_byIdJob($idJob, $idWorker)
	{
		$this->db->where('fk_id_job', $idJob);
		$this->db->where('fk_id_user', $idWorker);
		$query = $this->db->get('erp_training_workers');
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Save one worker
	 * @since 23/11/2017
	 */
	public function saveOneWorker()
	{
		$data = array(
			'fk_id_job' => $this->input->post('hddId'),
			'fk_id_user' => $this->input->post('worker')
		);

		$query = $this->db->insert('erp_training_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Rate
	 * @since 11/4/2021
	 */
	public function updateERPWorker()
	{
		$idWorkerErp = $this->input->post('hddId');

		$data = array(
			'title' => $this->input->post('title'),
			'responsability' => $this->input->post('responsability')
		);

		$this->db->where('id_erp_training_worker', $idWorkerErp);
		$query = $this->db->update('erp_training_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get activity list
	 * @since 23/2/2017
	 */
	public function get_activity_list()
	{
		$this->db->select('distinct(id_hazard_activity), hazard_activity');
		$this->db->join('param_hazard_activity A', 'A.id_hazard_activity = H.fk_id_hazard_activity', 'INNER');
		$this->db->order_by('A.hazard_activity, H.hazard_description', 'asc');
		$query = $this->db->get('param_hazard H');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get hazard list
	 * @since 27/11/2017
	 */
	public function get_hazard_list($idActivity)
	{
		$this->db->select();
		$this->db->join('param_hazard_activity A', 'A.id_hazard_activity = H.fk_id_hazard_activity', 'INNER');
		$this->db->where('A.id_hazard_activity', $idActivity);
		$this->db->order_by('A.hazard_activity, H.hazard_description', 'asc');
		$query = $this->db->get('param_hazard H');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * @author BMOTTAG
	 * @since 27/11/2017
	 * Consulta si existe un hazard especifico para un job especifico
	 */
	public function get_job_hazards_by($idJob, $idHazard)
	{
		$this->db->where('fk_id_job', $idJob);
		$this->db->where('fk_id_hazard', $idHazard);
		$query = $this->db->get('job_hazards');
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Add JOB HAZARDS
	 * @since 27/11/2016
	 */
	public function add_safety_hazard($idJob)
	{
		//delete hazards 
		$this->db->delete('job_hazards', array('fk_id_job' => $idJob));

		//add the new hazards
		$query = 1;
		if ($hazards = $this->input->post('hazards')) {
			$tot = count($hazards);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_job' => $idJob,
					'fk_id_hazard' => $hazards[$i]
				);
				$query = $this->db->insert('job_hazards', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add JOB HAZARDS LOG
	 * @since 27/11/2016
	 */
	public function add_hazard_log()
	{
		$idUser = $this->session->userdata("id");

		$data = array(
			'date_log' => date("Y-m-d G:i:s"),
			'fk_id_job' => $this->input->post('hddId'),
			'fk_id_user' => $idUser,
			'observation' => $this->input->post('observation')

		);
		$query = $this->db->insert('job_hazards_log', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get job locates info
	 * @since 29/11/2017
	 */
	public function get_job_locates($idJob)
	{
		$this->db->select();
		$this->db->where('L.fk_id_job', $idJob);
		$this->db->order_by('L.id_job_locates', 'asc');
		$query = $this->db->get('job_locates L');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add locates
	 * @since 29/11/2017
	 */
	public function add_locates($path)
	{
		$idUser = $this->session->userdata("id");

		$data = array(
			'fk_id_job' => $this->input->post('hddIdJob'),
			'fk_id_user' => $idUser,
			'locates_description' => $this->input->post('description'),
			'locates_photo' => $path,
			'locates_date' => date("Y-m-d G:i:s")
		);

		$query = $this->db->insert('job_locates', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add JSO
	 * @since 4/1/2018
	 */
	public function addJSO()
	{
		$idUser = $this->session->userdata("id");
		$idJSO = $this->input->post('hddIdentificador');

		$data = array(
			'fk_id_user_manager' => $this->input->post('manager'),
			'fk_id_user_supervisor' => $this->input->post('supervisor'),
			'potential_hazards' => $this->input->post('potential_hazards'),
			'health_safety' => $this->input->post('health_safety'),
			'rights_responsibilities' => $this->input->post('rights_responsibilities'),
			'company_safety_rules' => $this->input->post('company_safety_rules'),
			'hazard_awareness' => $this->input->post('hazard_awareness'),
			'reporting_procedures' => $this->input->post('reporting_procedures'),
			'personal_equipment' => $this->input->post('personal_equipment'),
			'drug_alcohol' => $this->input->post('drug_alcohol'),
			'violence_workplace' => $this->input->post('violence_workplace'),
			'whmis' => $this->input->post('whmis'),
			'equipment_operation' => $this->input->post('equipment_operation'),
			'workplace_inspections' => $this->input->post('workplace_inspections'),
			'accident_forms' => $this->input->post('accident_forms'),
			'first_aid' => $this->input->post('first_aid'),
			'erp' => $this->input->post('erp'),
			'flha' => $this->input->post('flha'),
			'near_miss' => $this->input->post('near_miss'),
			'erp_subcontractor' => $this->input->post('erp_subcontractor'),
			'accident_incident' => $this->input->post('accident_incident'),
			'preventive_maintenance' => $this->input->post('preventive_maintenance'),
			'msds' => $this->input->post('msds'),
			'notification_hazards' => $this->input->post('notification_hazards'),
			'first_aid_subcontractor' => $this->input->post('first_aid_subcontractor'),
			'smoking_drug' => $this->input->post('smoking_drug'),
			'flha_subcontractor' => $this->input->post('flha_subcontractor'),
			'environmental_management' => $this->input->post('environmental_management'),
			'working_alone' => $this->input->post('working_alone'),
			'muster_point' => $this->input->post('muster_point'),
			'fire_extinguishers' => $this->input->post('fire_extinguishers'),
			'personal_equipment_subcontractor' => $this->input->post('personal_equipment_subcontractor'),
			'equipment_inspections' => $this->input->post('equipment_inspections'),
			'housekeeping' => $this->input->post('housekeeping'),
			'hazard_identification' => $this->input->post('hazard_identification'),
			'site_safe_work' => $this->input->post('site_safe_work'),
			'site_safe_job' => $this->input->post('site_safe_job'),
			'reporting' => $this->input->post('reporting'),
			'attendance' => $this->input->post('attendance'),
			'site_rules' => $this->input->post('site_rules'),
			'confined_space' => $this->input->post('confined_space'),
			'fall_protection' => $this->input->post('fall_protection'),
			'tdg' => $this->input->post('tdg'),
			'first_aid_site' => $this->input->post('first_aid_site'),
			'whmis_site' => $this->input->post('whmis_site'),
			'traffic_control' => $this->input->post('traffic_control'),
			'backhoe' => $this->input->post('backhoe'),
			'excavator' => $this->input->post('excavator'),
			'forklift' => $this->input->post('forklift'),
			'cranes' => $this->input->post('cranes'),
			'trailer_towing' => $this->input->post('trailer_towing'),
			'power_tools' => $this->input->post('power_tools'),
			'dump_truck' => $this->input->post('dump_truck'),
			'hoists' => $this->input->post('hoists'),
			'loader' => $this->input->post('loader'),
			'light_vehicles' => $this->input->post('light_vehicles'),
			'conveyors' => $this->input->post('conveyors'),
			'compressor' => $this->input->post('compressor'),
			'environmental_reporting' => $this->input->post('environmental_reporting'),
			'low_boys' => $this->input->post('low_boys'),
			'scaffolds' => $this->input->post('scaffolds'),
			'light_towers' => $this->input->post('light_towers'),
			'generators' => $this->input->post('generators'),
			'hydrovacs' => $this->input->post('hydrovacs'),
			'hydroseeds' => $this->input->post('hydroseeds'),
			'ground_disturbance' => $this->input->post('ground_disturbance'),
			'load_securement' => $this->input->post('load_securement'),
			'traffic_accommodation' => $this->input->post('traffic_accommodation'),
			'safety_advisor' => $this->input->post('safety_advisor'),
			'wib' => $this->input->post('wib'),
			'safe_trenching' => $this->input->post('safe_trenching'),
			'street_sweeper' => $this->input->post('street_sweeper'),
			'skid_steer' => $this->input->post('skid_steer'),
			'dozers' => $this->input->post('dozers')
		);

		//revisar si es para adicionar o editar
		if ($idJSO == '') {
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job'] = $this->input->post('hddIdJob');
			$data['date_issue_jso'] = date("Y-m-d G:i:s");
			$query = $this->db->insert('job_jso', $data);
			$idJSO = $this->db->insert_id();
		} else {
			$this->db->where('id_job_jso', $idJSO);
			$query = $this->db->update('job_jso', $data);
		}

		if ($query) {
			return $idJSO;
		} else {
			return false;
		}
	}

	/**
	 * JSO
	 * @since 4/1/2018
	 */
	public function get_jso($arrDatos)
	{
		$this->db->select('S.*, CONCAT(U.first_name, " " , U.last_name) supervisor, CONCAT(X.first_name, " " , X.last_name) manager, J.id_job, J.job_description');
		$this->db->join('param_jobs J', 'J.id_job = S.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = S.fk_id_user_supervisor', 'INNER');
		$this->db->join('user X', 'X.id_user = S.fk_id_user_manager', 'INNER');

		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("idJobJso", $arrDatos)) {
			$this->db->where('id_job_jso', $arrDatos["idJobJso"]);
		}

		$query = $this->db->get('job_jso S');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * JSO
	 * @since 5/1/2018
	 */
	public function get_jso_workers($arrDatos)
	{
		$this->db->select();

		if (array_key_exists("idJobJso", $arrDatos)) {
			$this->db->where('fk_id_job_jso', $arrDatos["idJobJso"]);
		}
		if (array_key_exists("idJobJsoWorker", $arrDatos)) {
			$this->db->where('id_job_jso_worker', $arrDatos["idJobJsoWorker"]);
		}
		$query = $this->db->get('job_jso_workers');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add jso worker
	 * @since 5/1/2018
	 */
	public function saveJSOWorker()
	{
		$idJobJsoWorker = $this->input->post('hddidJobJsoWorker');

		$data = array(
			'fk_id_job_jso' => $this->input->post('hddidJobJso'),
			'name' => $this->security->xss_clean($this->input->post("name")),
			'position' => $this->security->xss_clean($this->input->post('position')),
			'emergency_contact' => $this->security->xss_clean($this->input->post('emergency_contact')),
			'driver_license_required' => $this->security->xss_clean($this->input->post('license')),
			'license_number' => $this->security->xss_clean($this->input->post('license_number')),
			'city' => $this->security->xss_clean($this->input->post('city')),
			'works_for' => $this->security->xss_clean($this->input->post('worksfor')),
			'works_phone_number' => $this->security->xss_clean($this->input->post('phone_number'))
		);

		//revisar si es para adicionar o editar
		if ($idJobJsoWorker == '') {
			$data['date_oriented'] = date('Y-m-d');
			$query = $this->db->insert('job_jso_workers', $data);
		} else {
			$this->db->where('id_job_jso_worker', $idJobJsoWorker);
			$query = $this->db->update('job_jso_workers', $data);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save one worker TOOL BOX
	 * @since 8/5/2018
	 */
	public function toolBoxSaveOneWorker()
	{
		$data = array(
			'fk_id_tool_box' => $this->input->post('hddIdToolBox'),
			'fk_id_user' => $this->input->post('worker')
		);

		$query = $this->db->insert('tool_box_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Lista hazards logs
	 * @since 21/8/2018
	 */
	public function get_hazards_logs($arrDatos)
	{
		$this->db->select('A.*, J.*, CONCAT(U.first_name, " " , U.last_name) name');
		$this->db->join('param_jobs J', 'J.id_job = A.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = A.fk_id_user', 'INNER');

		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}

		if (array_key_exists("idJobHazardLog", $arrDatos)) {
			$this->db->where('id_job_hazard_log', $arrDatos["idJobHazardLog"]);
		}

		$this->db->order_by('date_log', 'desc');
		$query = $this->db->get('job_hazards_log A');

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
	public function get_job_hazards_v2($idJob)
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
	 * Get tool box subcontractor workers info
	 * @since 26/2/2018
	 */
	public function get_tool_box_subcontractors_workers($idToolBox)
	{
		$this->db->select();
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'INNER');
		$this->db->where('W.fk_id_tool_box', $idToolBox);
		$this->db->order_by('C.company_name, W.worker_name', 'asc');
		$query = $this->db->get('tool_box_workers_subcontractor W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Save subcontractor worker
	 * @since 26/2/2018
	 */
	public function saveSubcontractorWorker()
	{
		$data = array(
			'fk_id_tool_box' => $this->input->post('hddIdToolBox'),
			'fk_id_company' => $this->input->post('company'),
			'worker_name' => $this->input->post('workerName')
		);

		$query = $this->db->insert('tool_box_workers_subcontractor', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Excavation
	 * @since 2/8/2021
	 */
	public function addExcavation()
	{
		$idUser = $this->session->userdata("id");
		$idExcavation = $this->input->post('hddIdentificador');
		$idJob = $this->input->post('hddIdJob');

		$data = array(
			'fk_id_job' => $idJob,
			'project_location' => $this->input->post('project_location'),
			'depth' => $this->input->post('depth'),
			'width' => $this->input->post('width'),
			'length' => $this->input->post('length'),
			'confined_space' => $this->input->post('confined_space'),
			'fk_id_confined' => $this->input->post('idConfined'),
			'tested_daily' => $this->input->post('tested_daily'),
			'tested_daily_explanation' => $this->input->post('tested_daily_explanation'),
			'ventilation' => $this->input->post('ventilation'),
			'ventilation_explanation' => $this->input->post('ventilation_explanation'),
			'soil_classification' => $this->input->post('soil_classification'),
			'soil_type' => $this->input->post('soil_type'),
			'description_safe_work' => $this->input->post('description_safe_work'),
			'practice_work_alone' => $this->input->post('practice_work_alone'),
			'practice_eye_contact' => $this->input->post('practice_eye_contact'),
			'practice_communication' => $this->input->post('practice_communication'),
			'practice_walls' => $this->input->post('practice_walls'),
			'practice_protective_structures' => $this->input->post('practice_protective_structures'),
			'practice_identify_underground' => $this->input->post('practice_identify_underground'),
			'practice_scope' => $this->input->post('practice_scope'),
			'practice_site_locates' => $this->input->post('practice_site_locates'),
			'practice_provided_safe' => $this->input->post('practice_provided_safe'),
			'practice_traffic_control' => $this->input->post('practice_traffic_control'),
			'practice_flaggers' => $this->input->post('practice_flaggers'),
			'practice_barricades' => $this->input->post('practice_barricades')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');

		//revisar si es para adicionar o editar
		if ($idExcavation == '') {
			$data['fk_id_user'] = $idUser; //solo se ingresa el usuario cuando se crea

			//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
			$data['date_excavation'] = date("Y-m-d"); //fecha del registro
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_excavation'] = $dateIssue;
			}
			$query = $this->db->insert('job_excavation', $data);
			$idExcavation = $this->db->insert_id(); //ID guardado
		} else {
			//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
			$data['date_excavation'] = date("Y-m-d"); //fecha del registro
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_excavation'] = $dateIssue;
			}
			$this->db->where('id_job_excavation', $idExcavation);
			$query = $this->db->update('job_excavation', $data);
		}
		if ($query) {
			return $idExcavation;
		} else {
			return false;
		}
	}

	/**
	 * Update Excavation - Protection Methods
	 * @since 8/8/2021
	 */
	public function updateExcavation($archivo)
	{
		$idExcavation = $this->input->post('hddIdentificador');

		$data = array(
			'protection_sloping' => $this->input->post('sloping'),
			'protection_type_a' => $this->input->post('type_a'),
			'protection_type_b' => $this->input->post('type_b'),
			'protection_type_c' => $this->input->post('type_c'),
			'protection_benching' => $this->input->post('benching'),
			'protection_shoring' => $this->input->post('shoring'),
			'protection_shielding' => $this->input->post('shielding'),
			'additional_comments' => $this->input->post('additional_comments')
		);

		if ($archivo != 'xxx') {
			$data['method_system_doc'] = $archivo;
		}

		$this->db->where('id_job_excavation', $idExcavation);
		$query = $this->db->update('job_excavation', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Excavation - Access & Egress
	 * @since 8/8/2021
	 */
	public function updateExcavationAccess()
	{
		$idExcavation = $this->input->post('hddIdentificador');

		$data = array(
			'access_ladder' => $this->input->post('ladder'),
			'access_ramp' => $this->input->post('ramp'),
			'access_other' => $this->input->post('other'),
			'access_explain' => $this->input->post('access_explain')
		);


		$this->db->where('id_job_excavation', $idExcavation);
		$query = $this->db->update('job_excavation', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Excavation - Affected Zone, Traffic & Utilities
	 * @since 8/8/2021
	 */
	public function updateExcavationAffectedZone($archivo)
	{
		$idExcavation = $this->input->post('hddIdentificador');

		$data = array(
			'located' => $this->input->post('located'),
			'permit_required' => $this->input->post('permit_required'),
			'utility_lines' => $this->input->post('utility_lines'),
			'utility_lines_explain' => $this->input->post('utility_lines_explain'),
			'encumbrances' => $this->input->post('encumbrances'),
			'method_support' => $this->input->post('method_support'),
			'utility_shutdown' => $this->input->post('utility_shutdown'),
			'spoil_piles' => $this->input->post('spoil_piles'),
			'spoils_transported' => $this->input->post('spoils_transported'),
			'environmental_controls' => $this->input->post('environmental_controls'),
			'open_overnight' => $this->input->post('open_overnight'),
			'methods_secure' => $this->input->post('methods_secure'),
			'vehicle_traffic' => $this->input->post('vehicle_traffic')
		);

		if ($archivo != 'xxx') {
			$data['permit_required_doc'] = $archivo;
		}

		$this->db->where('id_job_excavation', $idExcavation);
		$query = $this->db->update('job_excavation', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Excavation - De-Watering
	 * @since 8/8/2021
	 */
	public function updateExcavationDeWatering()
	{
		$idExcavation = $this->input->post('hddIdentificador');

		$data = array(
			'dewatering_needed' => $this->input->post('dewatering_needed'),
			'explain_equipment' => $this->input->post('explain_equipment'),
			'body_water' => $this->input->post('body_water'),
			'water_conducted' => $this->input->post('water_conducted'),
			'additional_notes' => $this->input->post('additional_notes')
		);


		$this->db->where('id_job_excavation', $idExcavation);
		$query = $this->db->update('job_excavation', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Excavation - Personnel
	 * @since 14/8/2021
	 */
	public function updatePersonnel()
	{
		$idExcavation = $this->input->post('hddIdentificador');

		$data = array(
			'fk_id_user_manager' => $this->input->post('manager'),
			'fk_id_user_operator' => $this->input->post('operator'),
			'fk_id_user_supervisor' => $this->input->post('supervisor')
		);


		$this->db->where('id_job_excavation', $idExcavation);
		$query = $this->db->update('job_excavation', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @author BMOTTAG
	 * @since 14/11/2021
	 * Consulta de empleados para un formato de excavation
	 */
	public function get_excavation_byIdworker_byIdExcavation($idExcavation, $idWorker)
	{
		$this->db->where('fk_id_job_excavation', $idExcavation);
		$this->db->where('fk_id_user', $idWorker);
		$query = $this->db->get('job_excavation_workers');
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Add Excavation and Trenching Plan WORKER
	 * @since 14/8/2021
	 */
	public function add_excavation_worker($idExcavation)
	{
		//add the new workers
		$query = 1;
		if ($workers = $this->input->post('workers')) {
			$tot = count($workers);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_job_excavation' => $idExcavation,
					'fk_id_user' => $workers[$i]
				);
				$query = $this->db->insert('job_excavation_workers', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save one worker - Excavation and Trenching Plan
	 * @since 14/8/2021
	 */
	public function excavationSaveOneWorker()
	{
		$data = array(
			'fk_id_job_excavation' => $this->input->post('hddIdExcavation'),
			'fk_id_user' => $this->input->post('worker')
		);
		$query = $this->db->insert('job_excavation_workers', $data);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save subcontractor worker - Excavation and Trenching Plan
	 * @since 14/8/2021
	 */
	public function saveSubcontractorWorkerExcavation()
	{
		$data = array(
			'fk_id_job_excavation' => $this->input->post('hddIdExcavation'),
			'fk_id_company' => $this->input->post('company'),
			'worker_name' => $this->input->post('workerName'),
			'worker_movil_number' => $this->input->post('phone_number')
		);

		$query = $this->db->insert('job_excavation_subcontractor', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Upload file information
	 * @since 31/12/2022
	 */
	public function upload_file_detail($lista)
	{
		$query = $this->db->insert('job_details', $lista);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit JOB DETAIL
	 * @since 6/1/2023
	 */
	public function saveJobDetail()
	{
		$idJobDetail = $this->input->post('hddId');
		$extendedAmount = $this->input->post('quantity') * $this->input->post('unit_price');

		$data = array(
			'description' => $this->input->post('description'),
			'unit' => $this->input->post('unit'),
			'quantity' => $this->input->post('quantity'),
			'unit_price' => $this->input->post('unit_price'),
			'extended_amount' => $extendedAmount
		);
		//revisar si es para adicionar o editar
		if ($idJobDetail == '') {
			$idUser = $this->session->userdata("id");
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job '] = $this->input->post('hddIdJob');
			$data['chapter_number'] = $this->input->post('chapter_number');
			$data['chapter_name'] = $this->input->post('chapter');
			$data['item'] = $this->input->post('item');
			$data['status'] = 1;
			$query = $this->db->insert('job_details', $data);
		} else {
			$data['status'] = $this->input->post('status');
			$this->db->where('id_job_detail', $idJobDetail);
			$query = $this->db->update('job_details', $data);
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sumatoria de valores para Job Detail
	 * Int idJob
	 * @author BMOTTAG
	 * @since  11/1/2022
	 */
	public function sumExtendedAmount($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(extended_amount),2) TOTAL";
		$sql .= " FROM job_details J";
		$sql .= " WHERE J.fk_id_job =" . $arrDatos["idJob"];

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Update Job Detail
	 * @since 13/1/2023
	 */
	public function updateJobDetail($jobDetails, $totalExtendedAmount)
	{
		$query = true;

		if ($jobDetails) {
			$tot = count($jobDetails);
			for ($i = 0; $i < $tot; $i++) {
				if ($totalExtendedAmount != 0) {
					$percentage = number_format($jobDetails[$i]["extended_amount"] * 100 / $totalExtendedAmount, 2);
				} else {
					$percentage = 0.00;
				}

				$data = array(
					'percentage' => $percentage
				);
				$this->db->where('id_job_detail', $jobDetails[$i]["id_job_detail"]);
				if (!$this->db->update('job_details', $data)) {
					$query = false;
				}
			}
		}

		return $query;
	}

	/**
	 * Add/Edit FIRE WATCH
	 * @since 27/3/2023
	 */
	public function saveFireWatchSetup()
	{
		$idUser = $this->session->userdata("id");
		$metodo = $this->input->post('hddMetodo');
		$date = $this->input->post('date');
		$time = $this->input->post('time');
		$completeDate = $date . " " . $time . ":00:00";

		$dateRestored = $this->input->post('dateRestored');
		$timeRestored = $this->input->post('timeRestored');
		$completeDateRestored = $dateRestored . " " . $timeRestored . ":00:00";

		$data = array(
			'fk_id_user' => $idUser,
			'fk_id_supervisor' => $this->input->post('supervisor'),
			'building_address' => $this->input->post('address'),
			'date_out' => $completeDate,
			'date_restored' => $completeDateRestored,
			'fire_alarm' => $this->input->post('fire_alarm'),
			'fire_sprinkler' => $this->input->post('fire_sprinkler'),
			'standpipe' => $this->input->post('standpipe'),
			'fire_pump' => $this->input->post('fire_pump'),
			'fire_suppression' => $this->input->post('fire_suppression'),
			'other' => $this->input->post('other'),
			'areas' => $this->input->post('areas')
		);

		//revisar si es para adicionar o editar
		if ($metodo == 'create') {
			$data["fk_id_job"] = $this->input->post('hddIdJob');
			$query = $this->db->insert('job_fire_watch_setttings', $data);
		} else {
			$this->db->where('fk_id_job', $this->input->post('hddIdJob'));
			$query = $this->db->update('job_fire_watch_setttings', $data);
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit FIRE WATCH
	 * @since 27/3/2023
	 */
	public function saveFireWatch()
	{
		$idUser = $this->session->userdata("id");
		$idFireWatch = $this->input->post('hddIdFireWatch');
		$date = $this->input->post('date');
		$time = $this->input->post('time');
		$completeDate = $date . " " . $time . ":00:00";

		$dateRestored = $this->input->post('dateRestored');
		$timeRestored = $this->input->post('timeRestored');
		$completeDateRestored = $dateRestored . " " . $timeRestored . ":00:00";

		$data = array(
			'fk_id_job' => $this->input->post('hddIdJob'),
			'fk_id_user' => $idUser,
			'fk_id_conducted_by' => $idUser,
			'fk_id_supervisor' => $this->input->post('supervisor'),
			'building_address' => $this->input->post('address'),
			'date_out' => $completeDate,
			'date_restored' => $completeDateRestored,
			'fire_alarm' => $this->input->post('fire_alarm'),
			'fire_sprinkler' => $this->input->post('fire_sprinkler'),
			'standpipe' => $this->input->post('standpipe'),
			'fire_pump' => $this->input->post('fire_pump'),
			'fire_suppression' => $this->input->post('fire_suppression'),
			'other' => $this->input->post('other'),
			'areas' => $this->input->post('areas'),
			'training_completed' => $this->input->post('training'),
			'safety_shoes' => $this->input->post('safety_shoes'),
			'safety_vest' => $this->input->post('safety_vest'),
			'safety_glasses' => $this->input->post('safety_glasses'),
			'hearing_protection' => $this->input->post('hearing_protection'),
			'snow_cleets' => $this->input->post('snow_cleets'),
			'dust_proof_mask' => $this->input->post('dust_proof_mask'),
			'hard_hat' => $this->input->post('hard_hat'),
			'gloves' => $this->input->post('gloves'),
			'other_ppe' => $this->input->post('other_ppe'),
			'operational_impacts' => $this->input->post('operational_impacts'),
			'map_routing' => $this->input->post('map_routing'),
			'raic_access' => $this->input->post('raic_access'),
			'radio' => $this->input->post('radio'),
			'emergency_contacts' => $this->input->post('emergency_contacts'),
			'keys_access' => $this->input->post('keys_access')
		);

		//revisar si es para adicionar o editar
		if ($idFireWatch == '') {
			$data["date_commenced"] = date("Y-m-d G:i:s");
			$query = $this->db->insert('job_fire_watch', $data);
		} else {
			$this->db->where('id_job_fire_watch', $idFireWatch);
			$query = $this->db->update('job_fire_watch', $data);
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Fire Watch checkin
	 * @since 3/02/2023
	 */
	public function saveFireWatchCheckin()
	{
		$idWorker = $this->session->userdata("id");
		$data = array(
			'fk_id_job_fire_watch' => $this->input->post('idFireWatch'),
			'fk_id_worker' => $idWorker,
			'checkin_date' => date('Y-m-d'),
			'checkin_time' => date("Y-m-d G:i:s"),
			'address_start' => $this->input->post('address'),
			'latitude_start' => $this->input->post('latitude'),
			'longitude_start' => $this->input->post('longitude'),
			'notes' => $this->input->post('notes')
		);
		$query = $this->db->insert('job_fire_watch_checkin', $data);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Fire Watch Checkin - Checkout
	 * @since 3/02/2023
	 */
	public function saveFireWatchCheckout()
	{
		$idCheckin = $this->input->post('hddId');

		$data = array(
			'checkout_time' => date("Y-m-d G:i:s"),
			'notes' => $this->input->post('notes')
		);
		$this->db->where('id_checkin', $idCheckin);
		$query = $this->db->update('job_fire_watch_checkin', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Fire watch setup
	 * @since 27/1/2023
	 */
	public function get_fire_watch_setup($arrDatos)
	{
		$this->db->select('F.*, CONCAT(U.first_name, " " , U.last_name) reportedby, CONCAT(Z.first_name, " " , Z.last_name) supervisor, Z.movil as super_number, J.id_job, J.job_description');
		$this->db->join('param_jobs J', 'J.id_job = F.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = F.fk_id_user', 'INNER');
		$this->db->join('user Z', 'Z.id_user = F.fk_id_supervisor', 'INNER');
		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}
		$this->db->order_by('id_job_fire_watch_settings', 'desc');
		$query = $this->db->get('job_fire_watch_setttings F');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Fire watch list
	 * @since 27/1/2023
	 */
	public function get_fire_watch($arrDatos)
	{
		$this->db->select('F.*, CONCAT(U.first_name, " " , U.last_name) reportedby, CONCAT(X.first_name, " " , X.last_name) conductedby, CONCAT(Z.first_name, " " , Z.last_name) supervisor, Z.movil as super_number, J.id_job, J.job_description');
		$this->db->join('param_jobs J', 'J.id_job = F.fk_id_job', 'INNER');
		$this->db->join('user U', 'U.id_user = F.fk_id_user', 'INNER');
		$this->db->join('user X', 'X.id_user = F.fk_id_conducted_by', 'INNER');
		$this->db->join('user Z', 'Z.id_user = F.fk_id_supervisor', 'INNER');
		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("idFireWatch", $arrDatos)) {
			$this->db->where('id_job_fire_watch', $arrDatos["idFireWatch"]);
		}

		$this->db->order_by('id_job_fire_watch', 'desc');
		$query = $this->db->get('job_fire_watch F');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Check In List for fire watch
	 * @since 1/6/2022
	 */
	public function get_fire_watch_checkin($arrDatos)
	{
		if (array_key_exists("distinctUser", $arrDatos)) {
			$this->db->select('distinct(id_user), first_name, last_name, movil');
		} else {
			$this->db->select();
		}

		$this->db->join('user U', 'U.id_user = C.fk_id_worker', 'INNER');
		if (array_key_exists("idCheckin", $arrDatos)) {
			$this->db->where('C.id_checkin', $arrDatos["idCheckin"]);
		}
		if (array_key_exists("idFireWatch", $arrDatos)) {
			$this->db->where('C.fk_id_job_fire_watch', $arrDatos["idFireWatch"]);
		}
		/*
				if (array_key_exists("today", $arrDatos)) {
					$this->db->where('C.checkin_date', $arrDatos["today"]);
				}
				*/
		if (array_key_exists("checkout", $arrDatos)) {
			$this->db->where('C.checkout_time', '0000-00-00 00:00:00');
		}

		if (array_key_exists("distinctUser", $arrDatos)) {
			$this->db->order_by('U.first_name, U.last_name', 'asc');
		} else {
			$this->db->order_by('C.fk_id_job_fire_watch, C.id_checkin', 'asc');
		}
		$query = $this->db->get('job_fire_watch_checkin C');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add new bitacora
	 * @since 03/01/2024
	 */
	public function saveBitacora()
	{
		$data = array(
			'fk_id_job' => $this->input->post('hddId'),
			'fk_id_user' => $this->session->userdata("id"),
			'date_bitacora' => date("Y-m-d H:i:s"),
			'notification' => $this->input->post('notification')
		);

		$query = $this->db->insert('bitacora', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get bitacora
	 * @since 03/01/2024
	 */
	public function get_bitacora_job($id_job)
	{
		$this->db->select("*");
		$this->db->join('user', 'bitacora.fk_id_user = user.id_user', 'left');
		$this->db->where('fk_id_job =', $id_job);

		$this->db->order_by('id_bitacora', 'desc');
		$query = $this->db->get('bitacora');


		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Delete WO Expenses
	 * @since 1/07/2024
	 */
	public function deleteWOExpenses($arrDatos) 
	{
		$idJob = $arrDatos["idJob"];

		$sql = "DELETE FROM workorder_expense ";
		$sql .= "WHERE fk_id_job_detail IN (SELECT id_job_detail FROM job_details WHERE fk_id_job = ?)";
		
		$query = $this->db->query($sql, array($idJob));

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update Flags Param Job
	 * @since 31/1/2024
	 */
	public function resetFlagsParamJob($arrDatos)
	{
		$data = array(
			'flag_expenses' => 0,
			'flag_upload_details' => 0
		);
		$this->db->where('id_job', $arrDatos["idJob"]);
		$query = $this->db->update('param_jobs', $data);
		return $query ? true : false;  
	}

	public function get_workorder_expense($arrData)
	{
		$this->db->join('workorder W', 'W.id_workorder = E.fk_id_workorder', 'INNER');
		$this->db->join('job_details J', 'J.id_job_detail = E.fk_id_job_detail', 'INNER');
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
	 * Sumatoria de valores para la WO
	 * Int idJobDetail
	 * Var table
	 * @author BMOTTAG
	 * @since  30/03/2024
	 */
	public function countExpenses($arrData)
	{
		$this->db->select('W. id_workorder, W.date, W.observation, SUM(expense_value) AS total_expenses');
		$this->db->from('workorder_expense E');
		$this->db->join('workorder W', 'W.id_workorder = E.fk_id_workorder', 'INNER');
		$this->db->where('fk_id_job_detail', $arrData["idJobDetail"]);
		$this->db->group_by('fk_id_workorder');
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update flag expenses in WO SUBMODULES
	 * @since 30/03/2024
	 */
	public function updateWOSubmoduleFlag($arrData)
	{
		$sql = "UPDATE " . $arrData["table"] . " X
					INNER JOIN workorder W ON W.id_workorder = X.fk_id_workorder
					SET X.flag_expenses = 0
					WHERE W.fk_id_job = " . $arrData["idJob"];
		
		$query = $this->db->query($sql);

		return $query ? true : false;
	}
	
}
