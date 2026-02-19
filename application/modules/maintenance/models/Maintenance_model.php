<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Maintenance_model extends CI_Model {

	
		/**
		 * Add maintenance
		 * @since 11/2/2020
		 */
		public function add_maintenance() 
		{
			$idVehicle = $this->input->post('hddIdVehicle');
			$idMaintenace = $this->input->post('hddIdMaintenance');
		
			$data = array(
				'amount' => $this->input->post('amount'),
				'fk_id_maintenance_type' => $this->input->post('id_maintenance_type'),
				'fk_id_stock' => $this->input->post('id_stock'),
				'maintenance_description' => $this->input->post('description'),
				'done_by' => $this->input->post('done_by'),
				'fk_revised_by_user' => $this->input->post('revised_by'),
				'next_hours_maintenance' => $this->input->post('next_hours_maintenance'),
				'next_date_maintenance' => $this->input->post('next_date_maintenance'),
				'stock_quantity' => $this->input->post('stockQuantity')
			);
			
			//revisar si es para adicionar o editar
			if ($idMaintenace == '') 
			{
				$data['date_maintenance'] = date("Y-m-d");
				$data['fk_id_vehicle'] = $this->input->post('hddIdVehicle');
				$query = $this->db->insert('maintenance', $data);
			} else {
				$this->db->where('id_maintenance', $idMaintenace);
				$query = $this->db->update('maintenance', $data);
			}
			
			//ACTUALIZO EL CAMPO DE OIL CHANGE EN LA TABLA DE VEHICULOS, REVISO EL TIPO DE MANTENIMIENTO
			$maintenanceType = $this->input->post('id_maintenance_type');
			
			if($maintenanceType == 1 || $maintenanceType == 8 || $maintenanceType == 9 || $maintenanceType == 10){
			
				if($maintenanceType == 1){//Oil change - Engine
					$data = array(
						'oil_change' => $this->input->post('next_hours_maintenance')
					);
				}elseif($maintenanceType == 8 || $maintenanceType == 9){//Oil change - Sweeper engine --- Hydraulic pump
					$data = array(
						'oil_change_2' => $this->input->post('next_hours_maintenance')
					);
				}elseif($maintenanceType == 10){//Oil change - Blower
					$data = array(
						'oil_change_3' => $this->input->post('next_hours_maintenance')
					);
				}
				
				$this->db->where('id_vehicle', $idVehicle);
				$query = $this->db->update('param_vehicle', $data);
				
			}
	
			if ($query) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Maintenance list
		 * @since 11/2/2020
		 */
		public function get_maintenance($arrDatos) 
		{
			$this->db->select('M.*, T.*, S.*, CONCAT(U.first_name, " " , U.last_name) name, V.hours, V.hours_2, V.hours_3');
			$this->db->join('maintenance_type T', 'T.id_maintenance_type = M.fk_id_maintenance_type', 'INNER');
			$this->db->join('stock S', 'S.id_stock = M.fk_id_stock', 'LEFT');
			$this->db->join('param_vehicle V', 'V.id_vehicle = M.fk_id_vehicle', 'INNER');
			$this->db->join('user U', 'U.id_user = M.fk_revised_by_user', 'INNER');
			
			if (array_key_exists("idMaintenance", $arrDatos)) {
				$this->db->where('M.id_maintenance', $arrDatos["idMaintenance"]);
			}
			if (array_key_exists("idVehicle", $arrDatos)) {
				$this->db->where('M.fk_id_vehicle', $arrDatos["idVehicle"]);
			}
			if (array_key_exists("maintenanceState", $arrDatos)) {
				$this->db->where('M.maintenance_state', $arrDatos["maintenanceState"]);
			}
			if (array_key_exists("filtroFecha", $arrDatos)) {
				$this->db->where('M.next_date_maintenance !=', "");
				$this->db->where('M.next_date_maintenance <=', $arrDatos["filtroFecha"]);//filtro para dias menores a 7 dias
			}
			
			$this->db->order_by('M.id_maintenance', 'desc');
			$query = $this->db->get('maintenance M',30);

			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
		}
		
		/**
		 * Update maintenance update
		 * @since 12/2/2020
		 */
		public function update_maintenance_state()
		{
			$idVehicle = $this->input->post('hddIdVehicle');
			$idMaintenanceType = $this->input->post('id_maintenance_type');
				
			$sql = "UPDATE maintenance SET maintenance_state = 2";
			$sql.= " WHERE fk_id_vehicle = $idVehicle AND fk_id_maintenance_type = $idMaintenanceType AND maintenance_state = 1;";
		
			$query = $this->db->query($sql);

			if ($query) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Add Maintenance check
		 * @since 13/2/2020
		 */
		public function add_maintenance_check($idMaintenace) 
		{
			//add the new hazards
			$data = array(
				'fk_id_maintenance' => $idMaintenace
			);
			$query = $this->db->insert('maintenance_check', $data);
			
			if ($query) {
				return true;
			} else{
				return false;
			}
		}
		
		/**
		 * Delete Maintenance check
		 * @since 13/2/2020
		 */
		public function delete_maintenance_check()
		{
			//delete maintenance check
			$sql = "TRUNCATE maintenance_check";		
			$query = $this->db->query($sql);

			if ($query) {
				return true;
			} else{
				return false;
			}
		}
		
		/**
		 * Update STOCK
		 * @since 26/3/2020
		 */
		public function updateStock($arrDatos) 
		{
				$idStock = $this->input->post('id_stock');
								
				$data = array(
					'quantity' => $arrDatos["newQuantity"]
				);

				$this->db->where('id_stock', $arrDatos["idStock"]);
				$query = $this->db->update('stock', $data);

				if ($query) {
					return $idStock;
				} else {
					return false;
				}
		}
		
		

		
		

		
	    
	}