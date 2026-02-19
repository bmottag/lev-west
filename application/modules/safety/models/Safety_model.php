<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Safety_model extends CI_Model {

		
		/**
		 * Add SAFETY
		 * @since 3/12/2016
		 */
		public function add_safety() 
		{
			$idUser = $this->session->userdata("id");
			$idSafety = $this->input->post('hddIdentificador');
			$idJob = $this->input->post('hddIdJob');
			
			$data = array(
				'fk_id_operation' => $this->input->post('hddTask'),
				'work' => $this->input->post('work'),
				'fk_id_job' => $idJob,
				'muster_point' => $this->input->post('musterPoint'),
				'muster_point_2' => $this->input->post('musterPoint2'),
				'primary_head_counter' => $this->input->post('primaryHeadCounter'),
				'secondary_head_counter' => $this->input->post('secondaryHeadCounter'),
				'specify_ppe' => $this->input->post('specify')
			);
			
			$ppe = $this->input->post("ppe"); // 1: con ppe; 2: sin ppe

			if(!empty($ppe)) {
				if($ppe == 'on') {
					$data['ppe'] = 1;
				} else if($ppe == 'off') {
					unset($data['ppe']);
				}
			} else {
				$data['ppe'] = 2;
			}
			
			//revisar si es para adicionar o editar
			if ($idSafety == '') {
				$data['fk_id_user'] = $idUser; //solo se ingresa el usuario cuando se crea
				$data['date'] = date("Y-m-d G:i:s");//fecha del registro
				
				$query = $this->db->insert('safety', $data);
								
				$idSafety = $this->db->insert_id();//ID guardado
			} else {
				$this->db->where('id_safety', $idSafety);
				$query = $this->db->update('safety', $data);
			}
			if ($query) {
				return $idSafety;
			} else {
				return false;
			}
		}
		
		/**
		 * Add SAFETY HAZARD
		 * @since 4/12/2016
		 * @review 10/12/2016
		 */
		public function add_safety_hazard($idSafety) 
		{
			//delete hazards 
			$this->db->delete('safety_hazards', array('fk_id_safety' => $idSafety));

			//add the new hazards
			$query = 1;
			if ($hazards = $this->input->post('hazards')) {
				$tot = count($hazards);
				for ($i = 0; $i < $tot; $i++) {
					$data = array(
						'fk_id_safety' => $idSafety,
						'fk_id_hazard' => $hazards[$i]
					);
					$query = $this->db->insert('safety_hazards', $data);
				}
			}
			if ($query) {
				return true;
			} else{
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
		 * Delete Record
		 * @since 5/12/2016
		 */
		public function deleteRecord($arrDatos) 
		{
				$query = $this->db->delete($arrDatos ["table"], array($arrDatos ["primaryKey"] => $arrDatos ["id"]));
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add SAFETY WORKER
		 * @since 6/12/2016
		 * @review 10/12/2016
		 */
		public function add_safety_worker($idSafety) 
		{
			//add the new workers
			$query = 1;
			if ($workers = $this->input->post('workers')) {
				$tot = count($workers);
				for ($i = 0; $i < $tot; $i++) {
					$data = array(
						'fk_id_safety' => $idSafety,
						'fk_id_user' => $workers[$i]
					);
					$query = $this->db->insert('safety_workers', $data);
				}
			}
			if ($query) {
				return true;
			} else{
				return false;
			}
		}
		
		/**
		 * Get safety workers info
		 * @since 6/12/2016
		 */
		public function get_safety_workers($idSafety) 
		{		
				$this->db->select("W.id_safety_worker, W.fk_id_safety, W.signature, W.fk_id_user, W.understanding, CONCAT(first_name, ' ', last_name) name");
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
		 * @author BMOTTAG
		 * @since 10/12/2016
		 * Consulta de empleados para un safety especifico
		 */
		public function get_safety_byIdworker_byIdSafety($idSafety, $idWorker) {
			$this->db->where('fk_id_safety', $idSafety);
			$this->db->where('fk_id_user', $idWorker);
			$query = $this->db->get('safety_workers');
			if ($query->num_rows() == 1) {
				return TRUE;
			}else{
				return FALSE;
			}
		}

		/**
		 * @author BMOTTAG
		 * @since 10/12/2016
		 * Consulta de hazards para un safety especifico
		 */
		public function get_safety_byIdHazard_byIdSafety($idSafety, $idHazard) {
			$this->db->where('fk_id_safety', $idSafety);
			$this->db->where('fk_id_hazard', $idHazard);
			$query = $this->db->get('safety_hazards');
			if ($query->num_rows() == 1) {
				return TRUE;
			}else{
				return FALSE;
			}

		}
		
		/**
		 * Get safety hazard info
		 * @since 10/12/2016
		 */
		public function get_safety_hazard_byIdHazard($idHazard) 
		{		
				$this->db->select();
				$this->db->join('param_hazard P', 'P.id_hazard = H.fk_id_hazard', 'INNER');
				$this->db->where('H.id_safety_hazard', $idHazard); 
				$query = $this->db->get('safety_hazards H');

				if ($query->num_rows() > 0) {
					return $query->row_array();
				} else {
					return false;
				}
		}
		
		/**
		 * Save one hazard
		 * @since 18/1/2017
		 */
		public function saveOneHazard() 
		{							
				$data = array(
					'fk_id_safety' => $this->input->post('hddId'),
					'fk_id_hazard' => $this->input->post('hazard')
				);			

				$query = $this->db->insert('safety_hazards', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Save one worker
		 * @since 18/1/2017
		 */
		public function saveOneWorker() 
		{							
				$data = array(
					'fk_id_safety' => $this->input->post('hddId'),
					'fk_id_user' => $this->input->post('worker')
				);			

				$query = $this->db->insert('safety_workers', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Save subcontractor worker
		 * @since 26/2/2017
		 */
		public function saveSubcontractorWorker() 
		{							
				$data = array(
					'fk_id_safety' => $this->input->post('hddId'),
					'fk_id_company' => $this->input->post('company'),
					'worker_name' => $this->input->post('workerName'),
					'worker_movil_number' => $this->input->post('phone_number')
				);			

				$query = $this->db->insert('safety_workers_subcontractor', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Payroll
		 * @since 4/02/2017
		 */
		public function get_safety_by_id($idSafety) 
		{						
				$this->db->select("S.*, CONCAT(first_name, ' ', last_name) name, J.job_description");
				$this->db->join('user U', 'U.id_user = S.fk_id_user', 'INNER');
				$this->db->join('param_jobs J', 'J.id_job = S.fk_id_job', 'INNER');
				
				$this->db->where('S.id_safety', $idSafety);
				
				$this->db->order_by('S.date', 'asc');
				$query = $this->db->get('safety S');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
		
		/**
		 * Get hazard list
		 * @since 5/2/2017
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
		 * Get activity list
		 * @since 23/2/2017
		 */
		public function get_activity_list_by_job($idJob) 
		{		
				$this->db->select('distinct(id_hazard_activity), hazard_activity');
				$this->db->join('param_hazard H', 'H.id_hazard = J.fk_id_hazard', 'INNER');
				$this->db->join('param_hazard_activity A', 'A.id_hazard_activity = H.fk_id_hazard_activity', 'INNER');
				$this->db->where('J.fk_id_job', $idJob);
				$this->db->order_by('A.hazard_activity, H.hazard_description', 'asc');
				$query = $this->db->get('job_hazards J');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
		
		/**
		 * Get hazard list
		 * @since 17/05/2019
		 */
		public function get_hazard_list_by_job($idActivity, $idJob) 
		{		
				$this->db->select();
				$this->db->join('param_hazard H', 'H.id_hazard = J.fk_id_hazard', 'INNER');
				$this->db->join('param_hazard_activity A', 'A.id_hazard_activity = H.fk_id_hazard_activity', 'INNER');
				$this->db->where('A.id_hazard_activity', $idActivity);
				$this->db->where('J.fk_id_job', $idJob);
				$this->db->order_by('A.hazard_activity, H.hazard_description', 'asc');
				$query = $this->db->get('job_hazards J');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Get safety COVID
		 * @since 14/4/2021
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
		 * Add COVID
		 * @since 14/4/2021
		 */
		public function add_covid() 
		{
			$idSafetyCovid = $this->input->post('hddIdentificador');			

			$data = array(
				'fk_id_safety' => $this->input->post('hddIdSafety'),
				'distancing' => $this->input->post('hddDistancing'),
				'distancing_comments' => $this->input->post('distancing_comments'),
				'sharing_tools' => $this->input->post('hddSharingTools'),
				'sharing_tools_comments' => $this->input->post('sharing_tools_comments'),
				'required_ppe' => $this->input->post('hddRequiredPPE'),
				'required_ppe_comments' => $this->input->post('required_ppe_comments'),
				'symptoms' => $this->input->post('hddSymptoms'),
				'symptoms_comments' => $this->input->post('symptoms_comments'),
				'protocols' => $this->input->post('hddProtocols'),
				'protocols_comments' => $this->input->post('protocols_comments'),
				'crew_size' => $this->input->post('crew_size')
			);
						
			//revisar si es para adicionar o editar
			if ($idSafetyCovid == '') {	
				$query = $this->db->insert('safety_covid', $data);								
			} else {
				$this->db->where('id_safety_covid', $idSafetyCovid);
				$query = $this->db->update('safety_covid', $data);
			}
			if ($query) {
				return true;
			} else {
				return false;
			}
		}

		
		
		
	    
	}