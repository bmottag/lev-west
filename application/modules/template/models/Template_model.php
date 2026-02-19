<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Template_model extends CI_Model {

	    		
		/**
		 * Add/Edit TEMPLATE
		 * @since 14/6/2017
		 */
		public function saveTemplate() 
		{
				$idTemplate = $this->input->post('hddId');
				
				$data = array(
					'template_name' => $this->input->post('templateName'),
					'template_description' => $this->input->post('description'),
					'location' => $this->input->post('location')
				);
				
				//revisar si es para adicionar o editar
				if ($idTemplate == '') {
					$data['fk_id_user'] = $this->session->userdata("id");
					$data['date_issue'] = date("Y-m-d G:i:s");
					$query = $this->db->insert('templates', $data);
					$idTemplate = $this->db->insert_id();				
				} else {
					$this->db->where('id_template', $idTemplate);
					$query = $this->db->update('templates', $data);
				}
				if ($query) {
					return $idTemplate;
				} else {
					return false;
				}
		}
		
		/**
		 * Get workers of a template
		 * @since 14/6/2017
		 */
		public function get_templates_workers($idUsedTemplate) 
		{		
				$this->db->select("W.id_template_used_worker, W.fk_id_template_used, W.signature, CONCAT(first_name, ' ', last_name) name");
				$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
				$this->db->where('W.fk_id_template_used', $idUsedTemplate); 
				$this->db->order_by('U.first_name, U.last_name', 'asc');
				$query = $this->db->get('template_used_workers W');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}
		
		/**
		 * @author BMOTTAG
		 * @since 14/6/2017
		 * Consulta de empleados para un template
		 */
		public function get_template_byIdworker_byIdSafety($idTemplate, $idWorker) {
			$this->db->where('fk_id_template_used', $idTemplate);
			$this->db->where('fk_id_user', $idWorker);
			$query = $this->db->get('template_used_workers');
			if ($query->num_rows() == 1) {
				return TRUE;
			}else{
				return FALSE;
			}
		}
		
		/**
		 * Add TEMPLATE WORKER
		 * @since 14/6/2017
		 */
		public function add_template_worker($idTemplate) 
		{
			//add the new workers
			$query = 1;
			if ($workers = $this->input->post('workers')) {
				$tot = count($workers);
				for ($i = 0; $i < $tot; $i++) {
					$data = array(
						'fk_id_template_used' => $idTemplate,
						'fk_id_user' => $workers[$i]
					);
					$query = $this->db->insert('template_used_workers', $data);
				}
			}
			if ($query) {
				return true;
			} else{
				return false;
			}
		}
		
		/**
		 * Save one worker
		 * @since 2/7/2017
		 */
		public function saveOneWorker() 
		{							
				$data = array(
					'fk_id_template_used' => $this->input->post('hddId'),
					'fk_id_user' => $this->input->post('worker')
				);			

				$query = $this->db->insert('template_used_workers', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Template info
		 * @since 2/7/2017
		 */
		public function get_template($arrData) 
		{
			$this->db->select("T.*, CONCAT(first_name, ' ', last_name) name");
			$this->db->join('user U', 'U.id_user = T.fk_id_user', 'INNER');
			
			if (array_key_exists("idTemplate", $arrData)) {
				$this->db->where('T.id_template', $arrData["idTemplate"]);
			}
			
			$query = $this->db->get('templates T');

			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
		}
	
		/**
		 * Add/Edit VALVES
		 * @since 15/04/2025
		 */
		public function saveValve() 
		{
			$idValve = $this->input->post('hddId');
			
			$data = array(
				'valve_number' => $this->input->post('valve_number'),
				'number_of_turns' => $this->input->post('number_of_turns'),
				'position' => $this->input->post('position'),
				'status' => $this->input->post('status'),
				'direction' => $this->input->post('direction'),
				'rewarks' => $this->input->post('rewarks'),
			);
			
			//revisar si es para adicionar o editar
			if ($idValve == '') {
				$data['fk_id_user'] = $this->session->userdata("id");
				$data['date_issue'] = date("Y-m-d G:i:s");
				$query = $this->db->insert('valves', $data);			
			} else {
				$this->db->where('id_valve', $idValve);
				$query = $this->db->update('valves', $data);
			}
			if ($query) {
				return true;
			} else {
				return false;
			}
		}
		
		
	    
	}