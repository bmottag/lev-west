<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Incidences_model extends CI_Model {

	
		/**
		 * Near miss list
		 * para año vigente
		 * last 20 records
		 * @since 31/3/2017
		 */
		public function get_near_miss_by_idUser($arrDatos) 
		{
				$year = date('Y');
				$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year-1));//para filtrar solo los registros del año actual
				
				$this->db->select('W.*, CONCAT(U.first_name, " " , U.last_name) name, J.id_job, job_description, T.*, CONCAT(X.first_name, " " , X.last_name) supervisor, CONCAT(Y.first_name, " " , Y.last_name) coordinator');
				$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
				$this->db->join('param_incident_type T', 'T.id_incident_type = W.fk_incident_type', 'INNER');
				$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
				$this->db->join('user X', 'X.id_user = W.manager_user', 'INNER');
				$this->db->join('user Y', 'Y.id_user = W.safety_user', 'INNER');
				if (array_key_exists("idNearMiss", $arrDatos)) {
					$this->db->where('id_near_miss', $arrDatos["idNearMiss"]);
				}
				if (array_key_exists("idEmployee", $arrDatos)) {
					$this->db->where('w.fk_id_user', $arrDatos["idEmployee"]);
				}
				if (array_key_exists("jobId", $arrDatos)) {
					$this->db->where('fk_id_job =', $arrDatos["jobId"]);
				}
				
				$this->db->where('W.date_issue >=', $firstDay);
				
				$this->db->order_by('id_near_miss', 'desc');
				$query = $this->db->get('incidence_near_miss W', 20);

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
			
		/**
		 * Add near miss
		 * @since 29/3/2017
		 */
		public function add_near_miss() 
		{
			$idUser = $this->session->userdata("id");
			$idNearmiss = $this->input->post('hddIdentificador');
			
			$hour = $this->input->post('hour');
			$hour = $hour<10?"0".$hour:$hour;
			$time = $hour . ":" . $this->input->post('min');
			
			$data = array(
				'fk_id_user' => $idUser,
				'fk_incident_type' => $this->input->post('nearMissType'),
				'what_happened' => $this->input->post('happened'),
				'date_near_miss' => $this->input->post('date'),
				'time' => $time,
				'location' => $this->input->post('location'),
				'fk_id_job' => $this->input->post('jobName'),
				'immediate_cause' => $this->input->post('cause'),
				'uderlying_causes' => $this->input->post('uderlyingCauses'),
				'corrective_actions' => $this->input->post('correctiveActions'),
				'preventative_action' => $this->input->post('preventativeAction'),
				'manager_user' => $this->input->post('manager'),
				'safety_user' => $this->input->post('coordinator'),
				'comments' => $this->input->post('comments')
			);
			
			//revisar si es para adicionar o editar
			if ($idNearmiss == '') {
				$data['date_issue'] = date("Y-m-d G:i:s");
				$data['state_incidence'] = 1;
				$query = $this->db->insert('incidence_near_miss', $data);	
				$idNearmiss = $this->db->insert_id();				
			} else {
				$this->db->where('id_near_miss', $idNearmiss);
				$query = $this->db->update('incidence_near_miss', $data);
			}
			
			if ($query) {
				return $idNearmiss;
			} else {
				return false;
			}
		}
		
		/**
		 * Update INCIDENCE
		 * @since 15/5/2017
		 */
		public function updateInfoSignature($arrDatos) 
		{
				$table = $arrDatos["table"];
				$signatureColumn =  $arrDatos["signatureColumn"];
				$valSignature =  $arrDatos["valSignature"];
				$userColumn =  $arrDatos["userColumn"];
				$valUser =  $this->session->userdata("id");
				$fechaColumn =  $arrDatos["fechaColumn"];
				$fecha = date("Y-m-d G:i:s");
				$idColumn =  $arrDatos["idColumn"];
				$idValue =  $arrDatos["idValue"];
			
				$sql = "UPDATE $table";
				$sql.= " SET $signatureColumn='$valSignature', $userColumn =  '$valUser', $fechaColumn='$fecha'";
				$sql.= " WHERE $idColumn=$idValue";

				$query = $this->db->query($sql);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Incident list
		 * para año vigente
		 * last 20 records
		 * @since 15/5/2017
		 */
		public function get_incident_by($arrDatos) 
		{
				$year = date('Y');
				$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year-1));//para filtrar solo los registros del año actual
				
				$this->db->select('W.*, T.*, J.id_job, job_description, CONCAT(U.first_name, " " , U.last_name) name, CONCAT(X.first_name, " " , X.last_name) supervisor, CONCAT(Y.first_name, " " , Y.last_name) coordinator');
				$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'LEFT');
				$this->db->join('param_incident_type T', 'T.id_incident_type = W.fk_incident_type', 'INNER');
				$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
				$this->db->join('user X', 'X.id_user = W.manager_user', 'INNER');
				$this->db->join('user Y', 'Y.id_user = W.safety_user', 'INNER');
				
				if (array_key_exists("idIncident", $arrDatos)) {
					$this->db->where('id_incident', $arrDatos["idIncident"]);
				}
				if (array_key_exists("idEmployee", $arrDatos)) {
					$this->db->where('W.fk_id_user', $arrDatos["idEmployee"]);
				}
				if (array_key_exists("jobId", $arrDatos)) {
					$this->db->where('fk_id_job =', $arrDatos["jobId"]);
				}
				
				$this->db->where('W.date_issue >=', $firstDay);
				
				$this->db->order_by('id_incident', 'desc');
				$query = $this->db->get('incidence_incident W', 20);

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
		
		/**
		 * Add Incident
		 * @since 15/5/2017
		 */
		public function add_incident() 
		{
			$idUser = $this->session->userdata("id");
			$idIncident = $this->input->post('hddIdentificador');
			
			$hour = $this->input->post('hour');
			$hour = $hour<10?"0".$hour:$hour;
			$time = $hour . ":" . $this->input->post('min');
			
			$data = array(
				'fk_incident_type' => $this->input->post('incidentType'),
				'fk_id_job' => $this->input->post('jobName'),
				'what_happened' => $this->input->post('happened'),
				'date_incident' => $this->input->post('date'),
				'time' => $time,
				'call_manager' => $this->input->post('callManager'),
				'immediate_cause' => $this->input->post('cause'),
				'uderlying_causes' => $this->input->post('uderlyingCauses'),
				'instruction_before' => $this->input->post('instructionBefore'),
				'corrective_actions' => $this->input->post('correctiveActions'),
				'preventative_action' => $this->input->post('preventativeAction'),
				'manager_user' => $this->input->post('manager'),
				'safety_user' => $this->input->post('coordinator'),
				'comments' => $this->input->post('comments')
			);
			
			//revisar si es para adicionar o editar
			if ($idIncident == '') {
				$data['fk_id_user'] = $idUser;
				$data['date_issue'] = date("Y-m-d G:i:s");
				$data['state_incidence'] = 1;
				$query = $this->db->insert('incidence_incident', $data);	
				$idIncident = $this->db->insert_id();				
			} else {
				$this->db->where('id_incident', $idIncident);
				$query = $this->db->update('incidence_incident', $data);
			}
			
			if ($query) {
				return $idIncident;
			} else {
				return false;
			}
		}
		
		/**
		 * Accident list
		 * para año vigente
		 * last 20 records
		 * @since 12/6/2017
		 */
		public function get_accident_by($arrDatos) 
		{
				$year = date('Y');
				$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year));//para filtrar solo los registros del año actual
				
				$this->db->select('W.*, CONCAT(U.first_name, " " , U.last_name) name');
				$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
				if (array_key_exists("idAccident", $arrDatos)) {
					$this->db->where('id_accident', $arrDatos["idAccident"]);
				}
				if (array_key_exists("idEmployee", $arrDatos)) {
					$this->db->where('W.fk_id_user', $arrDatos["idEmployee"]);
				}
				
				$this->db->where('W.date_issue >=', $firstDay);
				
				$this->db->order_by('id_accident', 'desc');
				$query = $this->db->get('incidence_accident W', 20);

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
		
		/**
		 * Add Incident
		 * @since 12/6/2017
		 */
		public function add_accident() 
		{
			$idUser = $this->session->userdata("id");
			$idIncident = $this->input->post('hddIdentificador');
			
			$hour = $this->input->post('hour');
			$hour = $hour<10?"0".$hour:$hour;
			$time = $hour . ":" . $this->input->post('min');
			
			$data = array(
				'fk_id_user' => $idUser,
				'date_accident' => $this->input->post('date'),
				'time' => $time,
				'unit' => $this->input->post('unit'),
				'location' => $this->input->post('location'),
				'near_city' => $this->input->post('nearCity'),
				'brief_explanation' => $this->input->post('explanation'),
				'climate_conditions' => $this->input->post('climate'),
				'call_manager' => $this->input->post('callManager'),
				'take_pictures' => $this->input->post('pictures'),
				'warning_devises' => $this->input->post('devises')
			);
			
			//revisar si es para adicionar o editar
			if ($idIncident == '') {
				$data['date_issue'] = date("Y-m-d G:i:s");
				$data['state_accident'] = 1;
				$query = $this->db->insert('incidence_accident', $data);	
				$idIncident = $this->db->insert_id();				
			} else {
				$this->db->where('id_accident', $idIncident);
				$query = $this->db->update('incidence_accident', $data);
			}
			
			if ($query) {
				return $idIncident;
			} else {
				return false;
			}
		}
		
		/**
		 * Add Witness
		 * @since 13/6/2017
		 */
		public function saveWitness() 
		{			
				$data = array(
					'fk_id_accident' => $this->input->post('hddidIncident'),
					'name' => $this->input->post('name'),
					'phone_number' => $this->input->post('phone')
				);

				$query = $this->db->insert('incidence_accident_witness', $data);			

				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add Car involved
		 * @since 26/6/2017
		 */
		public function saveInvolved() 
		{				
				$data = array(
					'fk_id_accident' => $this->input->post('hddidIncident'),
					'make' => $this->input->post('make'),
					'model' => $this->input->post('model'),
					
					'type' => $this->input->post('type'),
					'insurance' => $this->input->post('insurance'),
					'register_owner' => $this->input->post('owner'),
					'driver_name' => $this->input->post('driver'),
					'license' => $this->input->post('license'),
					'company_name' => $this->input->post('company'),
					'plate' => $this->input->post('plate')
				);

				$query = $this->db->insert('incidence_accident_car_involved', $data);			

				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Accident witness
		 * @since 3/7/2017
		 */
		public function get_accident_car_involved($arrDatos) 
		{			
				$this->db->select();
				$this->db->where('fk_id_accident', $arrDatos["idAccident"]);
				
				$this->db->order_by('id_car_involved', 'asc');
				$query = $this->db->get('incidence_accident_car_involved');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
		
		/**
		 * Accident witness
		 * @since 3/7/2017
		 */
		public function get_accident_witness($arrDatos) 
		{			
				$this->db->select();
				$this->db->where('fk_id_accident', $arrDatos["idAccident"]);
				
				$this->db->order_by('id_witness', 'asc');
				$query = $this->db->get('incidence_accident_witness');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Save Person involved
		 * @since 24/4/2021
		 */
		public function savePersonInvolved() 
		{							
				$data = array(
					'fk_id_incident' => $this->input->post('hddId'),
					'person_name' => $this->input->post('workerName'),
					'person_movil_number' => $this->input->post('phone_number'),
					'form_identifier ' => $this->input->post('hddFormIdentifier')
				);			

				$query = $this->db->insert('incidence_incident_person', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Get incidente Persons involved
		 * @since 24/4/2021
		 */
		public function get_persons_involved($arrData) 
		{		
				$this->db->select();
				if (array_key_exists("idIncident", $arrData)) {
					$this->db->where('P.fk_id_incident', $arrData["idIncident"]);
				}
				if (array_key_exists("form", $arrData)) {
					$this->db->where('P.form_identifier', $arrData["form"]);
				}
				if (array_key_exists("movilNumber", $arrData)) {
					$where = "P.person_movil_number != ''";
					$this->db->where($where);
				}
				$this->db->order_by('P.person_name', 'asc');
				$query = $this->db->get('incidence_incident_person P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

	
		
		
		
	    
	}