<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class More_model extends CI_Model
{


	/**
	 * environmental list
	 * @since 10/1/2018
	 */
	public function get_environmental($arrDatos)
	{
		$this->db->select('E.*, CONCAT(R.first_name, " " , R.last_name) name, CONCAT(U.first_name, " " , U.last_name) inspector, CONCAT(X.first_name, " " , X.last_name) manager, J.id_job, J.job_description');
		$this->db->join('param_jobs J', 'J.id_job = E.fk_id_job', 'INNER');
		$this->db->join('user R', 'R.id_user = E.fk_id_user', 'INNER');
		$this->db->join('user U', 'U.id_user = E.fk_id_user_inspector', 'LEFT');
		$this->db->join('user X', 'X.id_user = E.fk_id_user_manager', 'LEFT');

		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('E.fk_id_job', $arrDatos["idJob"]);
		}
		$query = $this->db->get('job_environmental E');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add environmental
	 * @since 13/1/2018
	 */
	public function add_environmental()
	{
		$idUser = $this->session->userdata("id");
		$idEnvironmental = $this->input->post('hddIdentificador');

		$data = array(
			'fk_id_user_inspector' => $this->input->post('inspector'),
			'fk_id_user_manager' => $this->input->post('manager'),
			'sites_watered' => $this->input->post('sites_watered'),
			'sites_watered_remarks' => $this->input->post('sites_watered_remarks'),
			'being_swept' => $this->input->post('being_swept'),
			'being_swept_remarks' => $this->input->post('being_swept_remarks'),
			'dusty_covered' => $this->input->post('dusty_covered'),
			'dusty_covered_remarks' => $this->input->post('dusty_covered_remarks'),
			'speed_control' => $this->input->post('speed_control'),
			'speed_control_remarks' => $this->input->post('speed_control_remarks'),
			'noise_permit' => $this->input->post('noise_permit'),
			'noise_permit_remarks' => $this->input->post('noise_permit_remarks'),
			'air_compressors' => $this->input->post('air_compressors'),
			'air_compressors_remarks' => $this->input->post('air_compressors_remarks'),
			'noise_mitigation' => $this->input->post('noise_mitigation'),
			'noise_mitigation_remarks' => $this->input->post('noise_mitigation_remarks'),
			'idle_plan' => $this->input->post('idle_plan'),
			'idle_plan_remarks' => $this->input->post('idle_plan_remarks'),
			'garbage_bin' => $this->input->post('garbage_bin'),
			'garbage_bin_remarks' => $this->input->post('garbage_bin_remarks'),
			'disposed_periodically' => $this->input->post('disposed_periodically'),
			'disposed_periodically_remarks' => $this->input->post('disposed_periodically_remarks'),
			'recycling_being' => $this->input->post('recycling_being'),
			'recycling_being_remarks' => $this->input->post('recycling_being_remarks'),
			'spill_containment' => $this->input->post('spill_containment'),
			'spill_containment_remarks' => $this->input->post('spill_containment_remarks'),
			'spillage_happen' => $this->input->post('spillage_happen'),
			'spillage_happen_remarks' => $this->input->post('spillage_happen_remarks'),
			'chemicals_stored' => $this->input->post('chemicals_stored'),
			'chemicals_stored_remarks' => $this->input->post('chemicals_stored_remarks'),
			'absorbing_chemical' => $this->input->post('absorbing_chemical'),
			'absorbing_chemical_remarks' => $this->input->post('absorbing_chemical_remarks'),
			'spill_kits' => $this->input->post('spill_kits'),
			'spill_kits_remarks' => $this->input->post('spill_kits_remarks'),
			'excessive_use' => $this->input->post('excessive_use'),
			'excessive_use_remarks' => $this->input->post('excessive_use_remarks'),
			'materials_stored' => $this->input->post('materials_stored'),
			'materials_stored_remarks' => $this->input->post('materials_stored_remarks'),
			'fire_extinguishers' => $this->input->post('fire_extinguishers'),
			'fire_extinguishers_remarks' => $this->input->post('fire_extinguishers_remarks'),
			'preventive_actions' => $this->input->post('preventive_actions'),
			'preventive_actions_remarks' => $this->input->post('preventive_actions_remarks')
		);

		//revisar si es para adicionar o editar
		if ($idEnvironmental == '') {
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job'] = $this->input->post('hddIdJob');
			$data['date_environmental'] = date("Y-m-d");
			$query = $this->db->insert('job_environmental', $data);
			$idEnvironmental = $this->db->insert_id();
		} else {
			$this->db->where('id_job_environmental', $idEnvironmental);
			$query = $this->db->update('job_environmental', $data);
		}

		if ($query) {
			return $idEnvironmental;
		} else {
			return false;
		}
	}

	/**
	 * PPE INSPECTION list
	 * para año vigente
	 * @since 15/1/2018
	 */
	public function get_ppe_inspection($arrDatos)
	{
		$year = date('Y');
		$firstDay = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)); //para filtrar solo los registros del año actual

		$this->db->select('T.*, CONCAT(U.first_name, " " , U.last_name) name');
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');

		if (array_key_exists("idPPEInspection", $arrDatos)) {
			$this->db->where('id_ppe_inspection', $arrDatos["idPPEInspection"]);
		}

		//$this->db->where('T.date_ppe_inspection >=', $firstDay);

		$this->db->order_by('id_ppe_inspection', 'desc');
		$query = $this->db->get('ppe_inspection T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit PPE INSPECTION
	 * @since 15/1/2018
	 */
	public function savePPEInspection()
	{
		$idPPEInspection = $this->input->post('hddId');

		$data = array(
			'observation' => $this->input->post('observation')
		);
		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');

		//revisar si es para adicionar o editar
		if ($idPPEInspection == '') {
			$data['fk_id_user'] = $this->session->userdata("id");
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_ppe_inspection'] = $dateIssue;
			} else {
				$data['date_ppe_inspection'] = date("Y-m-d");
			}
			$query = $this->db->insert('ppe_inspection', $data);
			$idPPEInspection = $this->db->insert_id();
		} else {
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_ppe_inspection'] = $dateIssue;
			}
			$this->db->where('id_ppe_inspection', $idPPEInspection);
			$query = $this->db->update('ppe_inspection', $data);
		}
		if ($query) {
			return $idPPEInspection;
		} else {
			return false;
		}
	}

	/**
	 * Get PPE inspection workers info
	 * @since 20/1/2018
	 */
	public function get_ppe_inspection_workers($idPPEInspection)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->where('W.fk_id_ppe_inspection', $idPPEInspection);
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('ppe_inspection_workers W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * @author BMOTTAG
	 * @since 20/1/2018
	 * Consulta de empleados para un ppe inspection especifico
	 */
	public function get_ppe_inspection_byIdworker_byIdPPEIspection($idPPEInspection, $idWorker)
	{
		$this->db->where('fk_id_ppe_inspection', $idPPEInspection);
		$this->db->where('fk_id_user', $idWorker);
		$query = $this->db->get('ppe_inspection_workers');
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Add PPE INSPECTION WORKER
	 * @since 21/1/2018
	 */
	public function add_ppe_inspection_worker($idPPEInspection)
	{
		//add the new workers
		$query = 1;
		if ($workers = $this->input->post('workers')) {
			$tot = count($workers);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_ppe_inspection' => $idPPEInspection,
					'fk_id_user' => $workers[$i],
					'date_issue' => date("Y-m-d G:i:s")
				);
				$query = $this->db->insert('ppe_inspection_workers', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update inspeccion trabajadores
	 * @since 29/1/2018
	 */
	public function update_ppe_inspection_worker()
	{
		$idPPEInspectionWorker = $this->input->post('hddIdPPEInspectionWorker');

		$data = array(
			'safety_boots' => $this->input->post('safety_boots'),
			'hart_hat' => $this->input->post('hart_hat'),
			'reflective_vest' => $this->input->post('reflective_vest'),
			'safety_glasses' => $this->input->post('safety_glasses'),
			'gloves' => $this->input->post('gloves')
		);

		$this->db->where('id_ppe_inspection_worker', $idPPEInspectionWorker);
		$query = $this->db->update('ppe_inspection_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * ADD one worker
	 * @since 29/1/2018
	 */
	public function addOneWorker()
	{
		$data = array(
			'fk_id_ppe_inspection' => $this->input->post('hddIdPPEInspection'),
			'fk_id_user' => $this->input->post('worker'),
			'date_issue' => date("Y-m-d G:i:s")
		);

		$query = $this->db->insert('ppe_inspection_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add confined space entry permit
	 * @since 13/1/2020
	 */
	public function add_confined()
	{
		$idUser = $this->session->userdata("id");
		$idConfined = $this->input->post('hddIdentificador');

		$fechaStart = $this->input->post('start_date');
		$horaStart = $this->input->post('start_hour');
		$minStart = $this->input->post('start_min');
		$fechaFinish = $this->input->post('finish_date');
		$horaFinish = $this->input->post('finish_hour');
		$minFinish = $this->input->post('finish_min');

		$fechaStart = $fechaStart . " " . $horaStart . ":" . $minStart . ":00";
		$fechaFinish = $fechaFinish . " " . $horaFinish . ":" . $minFinish . ":00";

		$data = array(
			'completed_flha' => $this->input->post('completed_flha'),
			'location' => $this->input->post('location'),
			'purpose' => $this->input->post('purpose'),
			'scheduled_start' => $fechaStart,
			'scheduled_finish' => $fechaFinish,
			'oxygen_deficient' => $this->input->post('oxygen_deficient'),
			'oxygen_enriched' => $this->input->post('oxygen_enriched'),
			'welding' => $this->input->post('welding'),
			'engulfment' => $this->input->post('engulfment'),
			'toxic_atmosphere' => $this->input->post('toxic_atmosphere'),
			'flammable_atmosphere' => $this->input->post('flammable_atmosphere'),
			'energized_equipment' => $this->input->post('energized_equipment'),
			'entrapment' => $this->input->post('entrapment'),
			'hazardous_chemical' => $this->input->post('hazardous_chemical'),
			'breathing_apparatus' => $this->input->post('breathing_apparatus'),
			'line_respirator' => $this->input->post('line_respirator'),
			'resistant_clothing' => $this->input->post('resistant_clothing'),
			'ventilation' => $this->input->post('ventilation'),
			'protective_gloves' => $this->input->post('protective_gloves'),
			'linelines' => $this->input->post('linelines'),
			'respirators' => $this->input->post('respirators'),
			'lockout' => $this->input->post('lockout'),
			'fire_extinguishers' => $this->input->post('fire_extinguishers'),
			'barricade' => $this->input->post('barricade'),
			'signs_posted' => $this->input->post('signs_posted'),
			'clearance_secured' => $this->input->post('clearance_secured'),
			'lighting' => $this->input->post('lighting'),
			'interrupter' => $this->input->post('interrupter'),
			'oxygen' => $this->input->post('oxygen'),
			'oxygen_time' => $this->input->post('oxygen_time'),
			'explosive_limit' => $this->input->post('explosive_limit'),
			'explosive_limit_time' => $this->input->post('explosive_limit_time'),
			'toxic_atmosphere_cond' => $this->input->post('toxic_atmosphere_cond'),
			'instruments_used' => $this->input->post('instruments_used'),
			'remarks' => $this->input->post('remarks'),
			'fk_id_user_authorization' => $this->input->post('authorization'),
			'fk_id_user_cancellation' => $this->input->post('cancellation')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');

		//revisar si es para adicionar o editar
		if ($idConfined == '') {
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job'] = $this->input->post('hddIdJob');

			//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
			$data['date_confined'] = date("Y-m-d");
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_confined'] = $dateIssue;
			}

			$query = $this->db->insert('job_confined', $data);
			$idConfined = $this->db->insert_id();
		} else {
			if ($userRol == 99 && $dateIssue != "") {
				$data['date_confined'] = $dateIssue;
			}

			$this->db->where('id_job_confined', $idConfined);
			$query = $this->db->update('job_confined', $data);
		}

		if ($query) {
			return $idConfined;
		} else {
			return false;
		}
	}

	/**
	 * Get confined workers info
	 * @since 20/1/2020
	 */
	public function get_confined_workers($idConfined, $wos)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->where('W.fk_id_job_confined', $idConfined);
		$this->db->where('W.flag_workers', $wos);
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('job_confined_workers W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * @author BMOTTAG
	 * @since 20/1/2020
	 * Consulta de empleados para un confined especifico
	 */
	public function get_confined_byIdworker_byIdConfined($idConfined, $idWorker, $wos)
	{
		$this->db->where('fk_id_job_confined', $idConfined);
		$this->db->where('fk_id_user', $idWorker);
		$this->db->where('flag_workers', $wos);
		$query = $this->db->get('job_confined_workers');
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Add confined WORKER
	 * @since 20/1/2020
	 */
	public function add_confined_worker($idConfined, $wos)
	{
		//add the new workers
		$query = 1;
		if ($workers = $this->input->post('workers')) {
			$tot = count($workers);
			for ($i = 0; $i < $tot; $i++) {
				$data = array(
					'fk_id_job_confined' => $idConfined,
					'fk_id_user' => $workers[$i],
					'flag_workers' => $wos
				);
				$query = $this->db->insert('job_confined_workers', $data);
			}
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save one worker CONFINED
	 * @since 20/1/2020
	 */
	public function confinedSaveOneWorker()
	{
		$data = array(
			'fk_id_job_confined' => $this->input->post('hddIdConfined'),
			'fk_id_user' => $this->input->post('worker'),
			'flag_workers' => 2
		);

		$query = $this->db->insert('job_confined_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save one worker CONFINED
	 * @since 29/1/2024
	 */
	public function confinedSaveWorkerOnSite()
	{
		$data = array(
			'fk_id_job_confined' => $this->input->post('hddIdConfined'),
			'fk_id_user' => $this->input->post('worker'),
			'flag_workers' => 1
		);

		$query = $this->db->insert('job_confined_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit RETESTING
	 * @since 4/2/2020
	 */
	public function saveRetesting()
	{
		$idConfined = $this->input->post('hddIdConfined');
		$idRetesting = $this->input->post('hddId');

		$data = array(
			'fk_id_job_confined' => $idConfined,
			're_oxygen' => $this->input->post('re_oxygen'),
			're_oxygen_time' => $this->input->post('re_oxygen_time'),
			're_explosive_limit' => $this->input->post('re_explosive_limit'),
			're_explosive_limit_time' => $this->input->post('re_explosive_limit_time'),
			're_toxic_atmosphere' => $this->input->post('re_toxic_atmosphere'),
			're_instruments_used' => $this->input->post('re_instruments_used')
		);

		//revisar si es para adicionar o editar
		if ($idRetesting == '') {
			$query = $this->db->insert('job_confined_re_testing', $data);
			$idRetesting = $this->db->insert_id();
		} else {
			$this->db->where('id_job_confined_re_testing', $idRetesting);
			$query = $this->db->update('job_confined_re_testing', $data);
		}
		if ($query) {
			return $idRetesting;
		} else {
			return false;
		}
	}

	/**
	 * Update confined worker
	 * @since 5/2/2020
	 */
	public function saveConfinedWorker()
	{
		$hddId = $this->input->post('hddId');

		$data = array(
			'task' => $this->input->post('task'),
			'fk_id_safety_watch_user' => $this->input->post('safety_watch')
		);

		$this->db->where('id_job_confined_worker', $hddId);
		$query = $this->db->update('job_confined_workers', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update confined worker date and time signature
	 * @since 6/2/2020
	 */
	public function updateConfinedWorkerInOut($arrDatos)
	{
		$data = array(
			$arrDatos["column"] => date("Y-m-d G:i:s")
		);
		$this->db->where('id_job_confined_worker', $arrDatos["id"]);
		$query = $this->db->update('job_confined_workers', $data);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update confined worker
	 * @since 5/2/2020
	 */
	public function save_post_entry()
	{
		$idConfined = $this->input->post('hddConfined');

		$data = array(
			'personnel_out' => $this->input->post('personnel_out'),
			'isolation' => $this->input->post('isolation'),
			'lockouts_removed' => $this->input->post('lockouts_removed'),
			'tags_removed' => $this->input->post('tags_removed'),
			'equipment_removed' => $this->input->post('equipment_removed'),
			'ppe_cleaned' => $this->input->post('ppe_cleaned'),
			'rescue_equipment' => $this->input->post('rescue_equipment'),
			'permits_signed' => $this->input->post('permits_signed'),
			'areas_notified' => $this->input->post('areas_notified'),
			'fk_id_post_entry_user' => $this->input->post('post_entry')
		);

		$this->db->where('id_job_confined', $idConfined);
		$query = $this->db->update('job_confined', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save save_rescue_plan
	 * @since 16/9/2024
	 */
	public function save_rescue_plan()
	{
		$idConfined = $this->input->post('hddConfined');

		$data = array(
			'rescue_phone' => $this->input->post('rescue_phone'),
			'rescue_radio' => $this->input->post('rescue_radio'),
			'rescue_audible' => $this->input->post('rescue_audible'),
			'rescue_intercom' => $this->input->post('rescue_intercom'),
			'rescue_w_phone' => $this->input->post('rescue_w_phone'),
			'rescue_w_intercom' => $this->input->post('rescue_w_intercom'),
			'rescue_w_visual' => $this->input->post('rescue_w_visual'),
			'rescue_w_radio' => $this->input->post('rescue_w_radio'),
			'rescue_w_audible' => $this->input->post('rescue_w_audible'),
			'rescue_w_rope' => $this->input->post('rescue_w_rope'),
			'rescue_congested_value' => $this->input->post('rescue_congested_value'),
			'rescue_hauling' => $this->input->post('rescue_hauling'),
			'rescue_hauling_value' => $this->input->post('rescue_hauling_value'),
			'rescue_patient' => $this->input->post('rescue_patient'),
			'rescue_patient_value' => $this->input->post('rescue_patient_value'),
			'rescue_anchor' => $this->input->post('rescue_anchor'),
			'rescue_anchor_value' => $this->input->post('rescue_anchor_value'),
			'rescue_beam' => $this->input->post('rescue_beam'),
			'rescue_strut' => $this->input->post('rescue_strut'),
			'rescue_stairwell' => $this->input->post('rescue_stairwell'),
			'rescue_column' => $this->input->post('rescue_column'),
			'rescue_other' => $this->input->post('rescue_other'),
			'rescue_e_hauling' => $this->input->post('rescue_e_hauling'),
			'rescue_e_hauling_value' => $this->input->post('rescue_e_hauling_value'),
			'rescue_e_carabiners' => $this->input->post('rescue_e_carabiners'),
			'rescue_e_carabiners_value' => $this->input->post('rescue_e_carabiners_value'),
			'rescue_e_pulleys' => $this->input->post('rescue_e_pulleys'),
			'rescue_e_pulleys_value' => $this->input->post('rescue_e_pulleys_value'),
			'rescue_e_absorbers' => $this->input->post('rescue_e_absorbers'),
			'rescue_e_absorbers_value' => $this->input->post('rescue_e_absorbers_value'),
			'rescue_e_straps' => $this->input->post('rescue_e_straps'),
			'rescue_e_straps_value' => $this->input->post('rescue_e_straps_value'),
			'rescue_e_webbing' => $this->input->post('rescue_e_webbing'),
			'rescue_e_webbing_value' => $this->input->post('rescue_e_webbing_value'),
			'rescue_e_ascenders' => $this->input->post('rescue_e_ascenders'),
			'rescue_e_ascenders_value' => $this->input->post('rescue_e_ascenders_value'),
			'rescue_e_harnesses' => $this->input->post('rescue_e_harnesses'),
			'rescue_e_harnesses_value' => $this->input->post('rescue_e_harnesses_value'),
			'rescue_e_rigging' => $this->input->post('rescue_e_rigging'),
			'rescue_e_rigging_value' => $this->input->post('rescue_e_rigging_value'),
			'rescue_e_lines' => $this->input->post('rescue_e_lines'),
			'rescue_e_lines_value' => $this->input->post('rescue_e_lines_value'),
			'rescue_e_m_lines' => $this->input->post('rescue_e_m_lines'),
			'rescue_e_m_lines_value' => $this->input->post('rescue_e_m_lines_value'),
			'rescue_e_wrist_har' => $this->input->post('rescue_e_wrist_har'),
			'rescue_e_wrist_har_value' => $this->input->post('rescue_e_wrist_har_value'),
			'rescue_e_extinguishers' => $this->input->post('rescue_e_extinguishers'),
			'rescue_equipment_inspected' => $this->input->post('rescue_equipment_inspected'),
			'rescue_employer' => $this->input->post('rescue_employer'),
			'rescue_first_aid' => $this->input->post('rescue_first_aid'),
			'rescue_first_aid_value' => $this->input->post('rescue_first_aid_value'),
			'rescue_packaging' => $this->input->post('rescue_packaging'),
			'rescue_packaging_value' => $this->input->post('rescue_packaging_value'),
			'rescue_vests' => $this->input->post('rescue_vests'),
			'rescue_glasses' => $this->input->post('rescue_glasses'),
			'rescue_hearing' => $this->input->post('rescue_hearing'),
			'rescue_gloves' => $this->input->post('rescue_gloves'),
			'rescue_boots' => $this->input->post('rescue_boots'),
			'rescue_face' => $this->input->post('rescue_face'),
			'rescue_hats' => $this->input->post('rescue_hats'),
			'rescue_description' => $this->input->post('rescue_description')
		);

		$this->db->where('id_job_confined', $idConfined);
		$query = $this->db->update('job_confined', $data);


		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * task control list
	 * @since 7/4/2020
	 */
	public function get_task_control($arrDatos)
	{
		$this->db->select('T.*, CONCAT(U.first_name, " " , U.last_name) supervisor, J.id_job, J.job_description, C.company_name');
		$this->db->join('param_jobs J', 'J.id_job = T.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = T.fk_id_company', 'LEFT');
		$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');

		if (array_key_exists("idJob", $arrDatos)) {
			$this->db->where('T.fk_id_job', $arrDatos["idJob"]);
		}
		if (array_key_exists("idTaskControl", $arrDatos)) {
			$this->db->where('T.id_job_task_control', $arrDatos["idTaskControl"]);
		}
		$query = $this->db->get('job_task_control T');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}


	/**
	 * Add Task Control
	 * @since 8/4/2020
	 */
	public function add_task_control()
	{
		$idUser = $this->session->userdata("id");
		$idEnvironmental = $this->input->post('hddIdentificador');

		$data = array(
			'name' => $this->input->post('name'),
			'contact_phone_number' => $this->input->post('phone_number'),
			'superintendent' => $this->input->post('superintendent'),
			'fk_id_company' => $this->input->post('company'),
			'work_location' => $this->input->post('work_location'),
			'crew_size' => $this->input->post('crew_size'),
			'task' => $this->input->post('task'),
			'distancing' => $this->input->post('distancing'),
			'distancing_comments' => $this->input->post('distancing_comments'),
			'sharing_tools' => $this->input->post('sharing_tools'),
			'sharing_tools_comments' => $this->input->post('sharing_tools_comments'),
			'required_ppe' => $this->input->post('required_ppe'),
			'required_ppe_comments' => $this->input->post('required_ppe_comments'),
			'symptoms' => $this->input->post('symptoms'),
			'symptoms_comments' => $this->input->post('symptoms_comments'),
			'protocols' => $this->input->post('protocols'),
			'protocols_comments' => $this->input->post('protocols_comments')
		);

		//revisar si es para adicionar o editar
		if ($idEnvironmental == '') {
			$data['fk_id_user'] = $idUser;
			$data['fk_id_job'] = $this->input->post('hddIdJob');
			$data['date_task_control'] = date("Y-m-d");
			$query = $this->db->insert('job_task_control', $data);
			$idEnvironmental = $this->db->insert_id();
		} else {
			$this->db->where('id_job_task_control', $idEnvironmental);
			$query = $this->db->update('job_task_control', $data);
		}

		if ($query) {
			return $idEnvironmental;
		} else {
			return false;
		}
	}
}
