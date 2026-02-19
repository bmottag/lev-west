<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Enlaces_model extends CI_Model {

	    		
		/**
		 * Add/Edit ENLACE
		 * @since 2/4/2018
		 */
		public function saveVideo() 
		{
				$idLink = $this->input->post('hddId');
				
				$data = array(
					'link_name' => $this->input->post('link_name'),
					'link_url' => $this->input->post('link_url'),
					'order' => $this->input->post('order'),
					'link_state' => $this->input->post('link_state')
				);
				
				//revisar si es para adicionar o editar
				if ($idLink == '') {
					$data['fk_id_menu'] = 9;//menu manuals
					$data['link_icon'] = 'fa-hand-o-up';
					$data['date_issue'] = date("Y-m-d G:i:s");
					$data['link_type'] = 4;//Complete URL; Videos

					$query = $this->db->insert('param_menu_links', $data);			
				} else {
					$this->db->where('id_link', $idLink);
					$query = $this->db->update('param_menu_links', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add/Edit MANUAL
		 * @since 27/4/2018
		 */
		public function saveManual($path) 
		{
				$idLink = $this->input->post('hddId');
				
				$data = array(
					'link_name' => $this->input->post('link_name'),
					'link_url' => $path,
					'order' => $this->input->post('order'),
					'link_state' => $this->input->post('link_state')
				);
				
				//revisar si es para adicionar o editar
				if ($idLink == '') {
					$data['fk_id_menu'] = 9;//menu manuals
					$data['link_icon'] = 'fa-hand-o-up';
					$data['date_issue'] = date("Y-m-d G:i:s");
					$data['link_type'] = 5;//Complete URL; Manuals

					$query = $this->db->insert('param_menu_links', $data);			
				} else {
					$this->db->where('id_link', $idLink);
					$query = $this->db->update('param_menu_links', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add/Edit MENU
		 * @since 30/3/2020
		 */
		public function saveMenu() 
		{
				$idMenu = $this->input->post('hddId');
				
				$data = array(
					'menu_name' => $this->input->post('menu_name'),
					'menu_url' => $this->input->post('menu_url'),
					'menu_icon' => $this->input->post('menu_icon'),
					'menu_order' => $this->input->post('order'),
					'menu_type' => $this->input->post('menu_type'),
					'menu_state' => $this->input->post('menu_state')
				);
				
				//revisar si es para adicionar o editar
				if ($idMenu == '') {
					$query = $this->db->insert('param_menu', $data);			
				} else {
					$this->db->where('id_menu', $idMenu);
					$query = $this->db->update('param_menu', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add/Edit LINK
		 * @since 31/3/2020
		 */
		public function saveLink() 
		{
				$idLink = $this->input->post('hddId');
				
				$data = array(
					'fk_id_menu' => $this->input->post('id_menu'),
					'link_name' => $this->input->post('link_name'),
					'link_url' => $this->input->post('link_url'),
					'link_icon' => $this->input->post('link_icon'),
					'order' => $this->input->post('order'),
					'link_state' => $this->input->post('link_state'),
					'link_type' => $this->input->post('link_type')
				);
				
				//revisar si es para adicionar o editar
				if ($idLink == '') {
					$data['date_issue'] = date("Y-m-d G:i:s");
					$query = $this->db->insert('param_menu_links', $data);			
				} else {
					$this->db->where('id_link', $idLink);
					$query = $this->db->update('param_menu_links', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add/Edit LINK ACCESS
		 * @since 1/4/2020
		 */
		public function saveRoleAccess() 
		{
				$idPermiso = $this->input->post('hddId');
				
				$data = array(
					'fk_id_menu' => $this->input->post('id_menu'),
					'fk_id_link' => $this->input->post('id_link'),
					'fk_id_rol' => $this->input->post('id_rol')

				);
				
				//revisar si es para adicionar o editar
				if ($idPermiso == '') {
					$query = $this->db->insert('param_menu_permisos', $data);			
				} else {
					$this->db->where('id_permiso', $idPermiso);
					$query = $this->db->update('param_menu_permisos', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
	
		
		
		
		
		
		
		
	    
	}