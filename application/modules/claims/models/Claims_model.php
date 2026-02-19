<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Claims_model extends CI_Model {

			
		/**
		 * Claims list
		 * @since 3/2/2021
		 */
		public function get_claims($arrDatos) 
		{							
				$this->db->select('C.*, J.id_job, job_description, CONCAT(U.first_name, " ", U.last_name) name');
				$this->db->join('param_jobs J', 'J.id_job = C.fk_id_job', 'INNER');
				$this->db->join('user U', 'U.id_user = C.fk_id_user', 'INNER');
				if (array_key_exists("idClaim", $arrDatos) && $arrDatos["idClaim"] != '') {
					$this->db->where('id_claim', $arrDatos["idClaim"]);
				}
				if (array_key_exists("claimNumberSearch", $arrDatos) && $arrDatos["claimNumberSearch"] != '') {
					$this->db->where('claim_number', $arrDatos["claimNumberSearch"]);
				}
				if (array_key_exists("idJob", $arrDatos) && $arrDatos["idJob"] != '') {
					$this->db->where('fk_id_job', $arrDatos["idJob"]);
				}
				if (array_key_exists("state", $arrDatos) && $arrDatos["state"] != '' ) {
					$this->db->where('current_status_claim', $arrDatos["state"]);
				}
				if (array_key_exists("order", $arrDatos) && $arrDatos["order"] != '' ) {
					$this->db->order_by('id_claim', $arrDatos["order"]);
				}else{
					$this->db->order_by('id_claim', 'desc');
				}
				
				if (array_key_exists("limit", $arrDatos)) {
					$query = $this->db->get('claim C', $arrDatos["limit"]);
				}else{
					$query = $this->db->get('claim C');
				}

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Guardar claim
		 * @since 3/2/2021
		 */
		public function guardarClaim() 
		{
				$idClaim = $this->input->post('hddId');
				$idUser = $this->session->userdata("id");
				
				$data = array(
					'fk_id_user' => $idUser,
					'claim_number' => $this->input->post('claimNumber'),
					'fk_id_job' => $this->input->post('id_job'),
					'observation_claim' => $this->input->post('observation')
				);	

				//revisar si es para adicionar o editar
				if ($idClaim == '') 
				{	
					$data['current_status_claim'] = 1;
					$data['date_issue_claim'] = date("Y-m-d G:i:s");
					$query = $this->db->insert('claim', $data);
					$idClaim = $this->db->insert_id();
				} else {
					$this->db->where('id_claim', $idClaim);
					$query = $this->db->update('claim', $data);
				}
				if ($query) {
					return $idClaim;
				} else {
					return false;
				}
		}

		/**
		 * Save Claim LIC 
		 * @since 25/05/2025
		 */
		public function saveClaimAPU() 
		{
			$idClaim = $this->input->post('hddId');
			$query = true;

			if ($apus = $this->input->post('apu')) {
				foreach ($apus as $idJobDetail) {
					// Verificar si ya existe el registro
					$this->db->where('fk_id_claim', $idClaim);
					$this->db->where('fk_id_job_detail', $idJobDetail);
					$exists = $this->db->get('claim_apus')->num_rows() > 0;

					if (!$exists) {
						$data = array(
							'fk_id_claim' => $idClaim,
							'fk_id_job_detail' => $idJobDetail
						);			
						if (!$this->db->insert('claim_apus', $data)) {
							$query = false;
						}
					}
				}
			}

			return $query;
	}

		/**
		 * Add Claim state
		 * @since 5/2/2021
		 */
		public function add_claim_state($arrData) 
		{
			$idUser = $this->session->userdata("id");
			
			$data = array(
				'fk_id_claim' => $arrData["idClaim"],
				'fk_id_user_claim' => $idUser,
				'date_issue_claim_state' => date("Y-m-d G:i:s"),
				'message_claim' => $arrData["message"],
				'state_claim' => $arrData["state"]
			);
			
			$query = $this->db->insert('claim_state', $data);

			if ($query) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Claims list History
		 * @since 5/2/2021
		 */
		public function get_claims_history($arrDatos) 
		{							
				$this->db->select('C.*, U.first_name');
				$this->db->join('user U', 'U.id_user = C.fk_id_user_claim', 'INNER');
				if (array_key_exists("idClaim", $arrDatos)) {
					$this->db->where('C.fk_id_claim', $arrDatos["idClaim"]);
				}								
				$this->db->order_by('id_claim_state', 'desc');
				$query = $this->db->get('claim_state C');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Update Claim current state and last message
		 * @since 5/2/2021
		 */
		public function update_claim($arrData)
		{			
			$data = array(
				'current_status_claim' => $arrData["state"],
				'last_message_claim' => $arrData["message"]
			);			
			$this->db->where('id_claim', $arrData["idClaim"]);
			$query = $this->db->update('claim', $data);

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
		public function updateWOStateFromClaimChange($WOList) 
		{
			$idUser = $this->session->userdata("id");
			$claimState = $this->input->post('state');
			$information = $this->input->post("message");

			if($claimState == 2){
				$claimWO = 3;
			}elseif($claimState == 6){
				$claimWO = 4;
			}
			//update states
			$query = 1;

			$tot = count($WOList);
			for ($i = 0; $i < $tot; $i++) 
			{
				$data = array(
					'fk_id_workorder' => $WOList[$i]['id_workorder'],
					'fk_id_user' => $idUser,
					'date_issue' => date("Y-m-d G:i:s"),
					'observation' => $information,
					'state' => $claimWO
				);
				
				$query = $this->db->insert('workorder_state', $data);


				//actualizo la tabla de WO
				$data = array(
					'state' => $claimWO,
					'last_message' => $information
				);			
				$this->db->where('id_workorder', $WOList[$i]['id_workorder']);
				$query = $this->db->update('workorder', $data);
			}

			if ($query) {
				return true;
			} else{
				return false;
			}
		}

	/**
	 * Update APU information
	 * @since 12/05/2025
	 */
	public function updateInfoAPU($data)
	{
		$this->db->where('fk_id_claim', $data['fk_id_claim']);
		$this->db->where('fk_id_job_detail', $data['fk_id_job_detail']);
		return $this->db->update('claim_apus', [
			'quantity' => $data['quantity'],
			'cost' => $data['cost']
		]);
	}
	
	    
}