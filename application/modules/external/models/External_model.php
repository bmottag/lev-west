<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class External_model extends CI_Model {
		
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
			$idUser = 1;
			$idTaskControl = $this->input->post('hddIdentificador');
		
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
			if ($idTaskControl == '') {
				$data['fk_id_user'] = $idUser;
				$data['fk_id_job'] = $this->input->post('hddIdJob');
				$data['date_task_control'] = date("Y-m-d");
				$query = $this->db->insert('job_task_control', $data);	
				$idTaskControl = $this->db->insert_id();				
			} else {
				$this->db->where('id_job_task_control', $idTaskControl);
				$query = $this->db->update('job_task_control', $data);
			}
			
			if ($query) {
				return $idTaskControl;
			} else {
				return false;
			}
		}

		/**
		 * Add employee
		 * @since 31/01/2022
		 */
		public function saveEmployee() 
		{
			$newPassword = addslashes($this->security->xss_clean($this->input->post('inputPassword')));
			$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$newPassword); 

			$data = array(
				'first_name' => addslashes($this->security->xss_clean($this->input->post('firstName'))),
				'last_name' => addslashes($this->security->xss_clean($this->input->post('lastName'))),
				'log_user' => addslashes($this->security->xss_clean($this->input->post('user'))),
				'password' => md5($passwd),
				'social_insurance' => addslashes($this->security->xss_clean($this->input->post('insuranceNumber'))),
				'health_number' => addslashes($this->security->xss_clean($this->input->post('healthNumber'))),
				'birthdate' => addslashes($this->security->xss_clean($this->input->post('birth'))),
				'movil' => addslashes($this->security->xss_clean($this->input->post('movilNumber'))),
				'email' => addslashes($this->security->xss_clean($this->input->post('email'))),
				'address' => addslashes($this->security->xss_clean($this->input->post('address'))),
				'perfil' => 7,
				'state' => 1
			);	
			$query = $this->db->insert('user', $data);
			if ($query) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Add checkin
		 * @since 1/06/2022
		 */
		public function saveCheckin($idWorker) 
		{
			$data = array(
				'fk_id_worker' => $idWorker,
				'checkin_date' => date('Y-m-d'),
				'checkin_time' => date("Y-m-d G:i:s"),
				'fk_id_job' => addslashes($this->security->xss_clean($this->input->post('idProject')))
			);	
			$query = $this->db->insert('new_checkin', $data);
			$idCheckin = $this->db->insert_id();	
			if ($query) {
				return $idCheckin;
			} else {
				return false;
			}
		}

		/**
		 * Add new worker
		 * @since 1/06/2022
		 */
		public function saveNewWorker() 
		{
			$data = array(
				'worker_name' => addslashes($this->security->xss_clean($this->input->post('new_name'))),
				'worker_movil' => addslashes($this->security->xss_clean($this->input->post('new_phone_number'))),
			);	
			$query = $this->db->insert('new_workers', $data);
			$idWorker = $this->db->insert_id();	
			if ($query) {
				return $idWorker;
			} else {
				return false;
			}
		}

		/**
		 * Update Checkin - Checkout
		 * @since 4/06/2022
		 */
		public function saveCheckout() 
		{
				$idCheckin = $this->input->post('hddId');
				
				$data = array(
					'checkout_time' => date("Y-m-d G:i:s")
				);
				$this->db->where('id_checkin', $idCheckin);
				$query = $this->db->update('new_checkin', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

	    /**
	     * Update dayoff´s state
	     * @author BMOTTAG
	     * @since  27/12/2022
	     */
	    public function update_dayoff()
		{
				$idDayoff = $this->input->post('hddIdDayOff');
				$idUser = $this->input->post('hddIdUser');
				$state = $this->input->post("status");
				$observation =  $this->security->xss_clean($this->input->post('observation'));
				$observation =  addslashes($observation);
				$fecha = date("Y-m-d G:i:s");
								
				//actualizo fecha del registo
				$sql = "UPDATE dayoff";
				$sql.= " SET fk_id_boss = $idUser, state = $state, admin_observation = '$observation', date_update = '$fecha'";
				$sql.= " WHERE id_dayoff = $idDayoff";
				$query = $this->db->query($sql);

				if ($query) {
					return true;
				} else {
					return false;
				}
	    }

		

	    
	}