<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Serviceorder_model extends CI_Model {

		
		/**
		 * Service Order Info
		 * @since 18/5/2023
		 */
		public function get_service_order($arrDatos) 
		{
				$sql = 'SELECT S.*, CONCAT(U.first_name, " ", U.last_name) assigned_by, CONCAT(Z.first_name, " ", Z.last_name) assigned_to, 
						CONCAT(V.unit_number, " -----> ", V.description) as unit_description, V.vin_number, V.hours, V.hours_2, V.hours_3, V.type_level_2,
						P.status_name, P.status_style, P.status_icon, W.status_name priority_name, 
						W.status_style priority_style, W.status_icon priority_icon, S.maintenace_type,
						CASE 
							WHEN S.maintenace_type = "preventive" THEN PM.maintenance_description 
							WHEN S.maintenace_type = "corrective" THEN CM.description_failure 
						END as main_description, 
						PM.verification_by,
						T.id_time, T.time_date, T.time
						FROM service_order S
						INNER JOIN user U ON U.id_user = S.fk_id_assign_by
						INNER JOIN user Z ON Z.id_user = S.fk_id_assign_to
						INNER JOIN param_vehicle V ON V.id_vehicle = S.fk_id_equipment
						INNER JOIN param_status P ON P.status_slug = S.service_status
						INNER JOIN param_status W ON W.status_slug = S.priority
						LEFT JOIN preventive_maintenance PM ON S.maintenace_type = "preventive" AND PM.id_preventive_maintenance = S.fk_id_maintenace
						LEFT JOIN corrective_maintenance CM ON S.maintenace_type = "corrective" AND CM.id_corrective_maintenance = S.fk_id_maintenace
						LEFT JOIN service_order_time T ON T.fk_id_service_order = S.id_service_order
						WHERE P.status_key = "serviceorder"';
				if (array_key_exists("idServiceOrder", $arrDatos)) {
					$sql .= ' AND id_service_order = ' . $arrDatos["idServiceOrder"];
				}
				if (array_key_exists("idVehicle", $arrDatos)) {
					$sql .= ' AND S.fk_id_equipment = ' . $arrDatos["idVehicle"];
				}
				if (array_key_exists("idAssignTo", $arrDatos)) {
					$sql .= ' AND S.fk_id_assign_to = ' . $arrDatos["idAssignTo"];
					$sql .= ' AND S.id_service_order != ' . $arrDatos["diffIdServiceOrder"];
					$sql .= ' AND S.service_status = "' . $arrDatos["status"] . '"';
				}elseif (array_key_exists("status", $arrDatos)) {
					$year = date('Y');
					$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year));
					$sql .= ' AND S.service_status = "' . $arrDatos["status"] . '"';
					$sql .= ' AND S.created_at >= "' . $firstDay. '"';
				}
				$sql .= ' ORDER BY id_service_order DESC';
				if (array_key_exists("limit", $arrDatos)) {
					$sql .= ' LIMIT ' . $arrDatos["limit"];
				}

				$query = $this->db->query($sql);

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit SERVICE ORDER
		 * @since 18/5/2023
		 */
		public function saveServiceOrder() 
		{		
				$idServiceOrder = $this->input->post('hddIdServiceOrder');
				$data = array(
					'fk_id_assign_to' => $this->input->post('assign_to'),
					'priority' => $this->input->post('priority'),
				);

				//revisar si es para adicionar o editar
				if ($idServiceOrder == '') {
					$data["fk_id_assign_by"] = $this->session->userdata("id");
					$data["fk_id_equipment"] = $this->input->post('hddIdEquipment');
					$data["fk_id_maintenace"] = $this->input->post('hddIdMaintenance');
					$data["maintenace_type"] = $this->input->post('hddMaintenanceType');
					$data["created_at"] = date("Y-m-d G:i:s");
					$data["current_hours"] = 0;
					$data["service_status"] = "new";
					$query = $this->db->insert('service_order', $data);
					$idServiceOrder = $this->db->insert_id();				
				} else {
					$data["service_status"] = $this->input->post('status');
					$data["updated_at"] = date("Y-m-d G:i:s");
					$data["current_hours"] = $this->input->post('hour');
					$data["damages"] = $this->input->post('damages');
					$data["can_be_used"] = $this->input->post('can_be_used');
					$data["purchasing_staff"] = $this->input->post('purchasing_staff');
					$data["mechanic"] = $this->input->post('mechanic');
					$data["engine_oil"] = $this->input->post('engine_oil');
					$data["transmission_oil"] = $this->input->post('transmission_oil');
					$data["hydraulic_oil"] = $this->input->post('hydraulic_oil');
					$data["fuel"] = $this->input->post('fuel');
					$data["filters"] = $this->input->post('filters');
					$data["parts"] = $this->input->post('parts');
					$data["blade"] = $this->input->post('blade');
					$data["ripper"] = $this->input->post('ripper');
					$data["other"] = $this->input->post('other');
					$data["comments"] = $this->input->post('comments');
					if($this->input->post('status') == "closed_so"){
						if($this->input->post('hddVerificationBy') == 1){
							$data["next_hours"] = $this->input->post('next_hours_maintenance');
						}else{
							$data["next_date"] = $this->input->post('next_date_maintenance');
						}
					}
					$this->db->where('id_service_order', $idServiceOrder);
					$query = $this->db->update('service_order', $data);
				}
				if ($query) {
					return $idServiceOrder;
				} else {
					return false;
				}
		}

		/**
		 * Preventive Maintenance Info
		 * @since 22/5/2023
		 */
		public function get_preventive_maintenance($arrDatos) 
		{
				$this->db->select();
				$this->db->join('maintenance_type T', 'T.id_maintenance_type = P.fk_id_maintenance_type', 'INNER');
				$this->db->join('param_vehicle V', 'V.id_vehicle = P.fk_id_equipment', 'INNER');
				if (array_key_exists("idVehicle", $arrDatos)) {
					$this->db->where('fk_id_equipment', $arrDatos["idVehicle"]);
				}
				if (array_key_exists("idMaintenance", $arrDatos)) {
					$this->db->where('id_preventive_maintenance', $arrDatos["idMaintenance"]);
				}
				if (array_key_exists("maintenanceStatus", $arrDatos)) {
					$this->db->where('P.maintenance_status', $arrDatos["maintenanceStatus"]);
				}
				$this->db->order_by('id_preventive_maintenance', 'desc');
				$query = $this->db->get('preventive_maintenance P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit PREVENTIVE MAINTENACE
		 * @since 22/5/2023
		 */
		public function savePreventiveMaintenance() 
		{		
				$idMaintenance = $this->input->post('hddIdMaintenance');
				$nextHours = $this->input->post('next_hours_maintenance');
				$nextDate = $this->input->post('next_date_maintenance');
				if($this->input->post('verification')==1){
					$nextDate = "";
				}else{
					$nextHours = 0;
				}

				$data = array(
					'fk_id_equipment' => $this->input->post('hddIdEquipment'),
					'fk_id_maintenance_type' => $this->input->post('maintenance_type'),
					'maintenance_description' => $this->input->post('description'),
					'verification_by' => $this->input->post('verification'),
					'next_hours_maintenance' => $nextHours,
					'next_date_maintenance' => $nextDate,
					'maintenance_status' => $this->input->post('maintenance_status')
				);
				//revisar si es para adicionar o editar
				if ($idMaintenance == '') {
					$query = $this->db->insert('preventive_maintenance', $data);				
				} else {
					$this->db->where('id_preventive_maintenance', $idMaintenance);
					$query = $this->db->update('preventive_maintenance', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Corrective Maintenance Info
		 * @since 26/5/2023
		 */
		public function get_corrective_maintenance($arrDatos) 
		{
				$this->db->select('P.*, CONCAT(U.first_name, " " , U.last_name) request_by, S.status_name, S.status_style, S.status_icon');
				$this->db->join('user U', 'U.id_user = P.request_by', 'INNER');
				$this->db->join('param_status S', 'S.status_slug = P.maintenance_status', 'INNER');
				if (array_key_exists("idVehicle", $arrDatos)) {
					$this->db->where('fk_id_equipment', $arrDatos["idVehicle"]);
				}
				if (array_key_exists("idMaintenance", $arrDatos)) {
					$this->db->where('id_corrective_maintenance', $arrDatos["idMaintenance"]);
				}	
				$this->db->order_by('id_corrective_maintenance', 'desc');
				$query = $this->db->get('corrective_maintenance P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit CORRECTIVE MAINTENACE
		 * @since 22/5/2023
		 */
		public function saveCorrectiveMaintenance() 
		{		
				$idMaintenance = $this->input->post('hddIdMaintenance');
				$data = array(
					'fk_id_equipment' => $this->input->post('hddIdEquipment'),			
					'description_failure' => $this->input->post('description')
				);
				//revisar si es para adicionar o editar
				if ($idMaintenance == '') {
					$data["request_by"] = $this->session->userdata("id");
					$data["created_at"] = date("Y-m-d G:i:s");
					$data["maintenance_status"] = "pending";
					$query = $this->db->insert('corrective_maintenance', $data);				
				} else {
					$this->db->where('id_corrective_maintenance', $idMaintenance);
					$query = $this->db->update('corrective_maintenance', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit PARTS
		 * @since 18/5/2023
		 */
		public function saveParts() 
		{		
				$idParts = $this->input->post('hddIdPart');

				$data = array(
					'part_description' => $this->input->post('part_description'),					
					'quantity' => $this->input->post('quantity'),
					'value' => $this->input->post('value'),
					'supplier' => $this->input->post('supplier')
				);
				
				//revisar si es para adicionar o editar
				if ($idParts == '') {
					$data["fk_id_service_order"] = $this->input->post('hddIdServiceOrder');
					$data["created_at"] = date("Y-m-d G:i:s");
					$data["part_status"] = "new_request";
					$query = $this->db->insert('service_order_parts', $data);				
				} else {
					$data["part_status"] = $this->input->post('status');
					$data["updated_at"] = date("Y-m-d G:i:s");
					$this->db->where('id_part', $idParts);
					$query = $this->db->update('service_order_parts', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Parts info
		 * @since 30/5/2023
		 */
		public function get_parts($arrDatos) 
		{
				if (array_key_exists("idServiceOrder", $arrDatos)) {
					$this->db->where('fk_id_service_order', $arrDatos["idServiceOrder"]);
				}
				if (array_key_exists("idPart", $arrDatos)) {
					$this->db->where('id_part', $arrDatos["idPart"]);
				}	
				$this->db->order_by('id_part', 'asc');
				$query = $this->db->get('service_order_parts P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit TIME
		 * @since 1/7/2023
		 */
		public function saveTime($arrDatos) 
		{		
				$date = date("Y-m-d G:i:s");
				$data = array(
					'time_date' => $date
				);
				
				//revisar si es para adicionar o editar
				if ($arrDatos["idTime"] == '') {
					$data["fk_id_service_order"] = $arrDatos["idServiceOrder"];
					$data["time"] = 0;
					$query = $this->db->insert('service_order_time', $data);				
				} else {
					if (array_key_exists("timeDate", $arrDatos)) {
						$minutes = (strtotime($arrDatos["timeDate"])-strtotime($date))/60;
						$minutes = abs($minutes);  
						$minutes = round($minutes);
				
						$hours = $minutes/60;
						$data["time"] = round($hours,2) + $arrDatos["time"];
					}	
					$this->db->where('id_time', $arrDatos["idTime"] );
					$query = $this->db->update('service_order_time', $data);
				}
				if ($query) {
					return true;
				} else {
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
		 * Get Chat
		 * @since 29/5/2023
		 */
		public function get_chat_info($arrData) 
		{		
				$this->db->select("C.*, CONCAT(U.first_name, ' ', U.last_name) user_from, W.first_name user_to");
				$this->db->join('user U', 'U.id_user = C.fk_id_user_from', 'INNER');
				$this->db->join('user W', 'W.id_user = C.fk_id_user_to', 'LEFT');
				if (array_key_exists("idChat", $arrData)) {
					$this->db->where('C.id_chat', $arrData["idChat"]);
				}
				if (array_key_exists("idModule", $arrData)) {
					$this->db->where('C.fk_id_module', $arrData["idModule"]);
				}
				if (array_key_exists("module", $arrData)) {
					$this->db->where('C.module', $arrData["module"]);
				}
				$this->db->order_by('C.id_chat', 'asc');
				$query = $this->db->get('chat C');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Expenses Info
		 * Var year
		 * @author BMOTTAG
		 * @since  22/7/2023
		 */
		public function get_expenses($arrDatos)
		{	
			$year = date('Y');
			$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year));

			$sql = 'SELECT 
						V.id_vehicle, V.unit_number, V.description,
						COUNT(S.fk_id_equipment) AS so_number,
						ROUND(SUM(T.time),2) AS time_expenses,
						ROUND(SUM(P.value),2) AS parts_expenses
					FROM param_vehicle V
					INNER JOIN service_order S ON V.id_vehicle = S.fk_id_equipment
					LEFT JOIN service_order_time T ON S.id_service_order = T.fk_id_service_order
					LEFT JOIN service_order_parts P ON S.id_service_order = P.fk_id_service_order
					WHERE 
						V.fk_id_company = 1 
						AND V.state = 1
						AND S.created_at >= "' . $firstDay . '"
					GROUP BY V.id_vehicle
					ORDER BY V.unit_number;';
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
		}

		/**
		 * Expenses Info by Equipment
		 * Var year
		 * @author BMOTTAG
		 * @since  22/7/2023
		 */
		public function get_expenses_by_equipment($arrDatos)
		{	
			$year = date('Y');
			$firstDay = date('Y-m-d', mktime(0,0,0, 1, 1, $year));

			$sql = 'SELECT 
						S.id_service_order, S.maintenace_type, S.fk_id_equipment,
						ROUND(T.time,2) AS time_expenses,
						ROUND(SUM(P.value),2) AS parts_expenses,
						CASE 
							WHEN S.maintenace_type = "preventive" THEN PM.maintenance_description 
							WHEN S.maintenace_type = "corrective" THEN CM.description_failure 
						END as main_description
					FROM service_order S
					LEFT JOIN service_order_time T ON S.id_service_order = T.fk_id_service_order
					LEFT JOIN service_order_parts P ON S.id_service_order = P.fk_id_service_order
					LEFT JOIN preventive_maintenance PM ON S.maintenace_type = "preventive" AND PM.id_preventive_maintenance = S.fk_id_maintenace
					LEFT JOIN corrective_maintenance CM ON S.maintenace_type = "corrective" AND CM.id_corrective_maintenance = S.fk_id_maintenace
					WHERE S.created_at >= "' . $firstDay . '"
					AND S.fk_id_equipment = ' . $arrDatos["idVehicle"]. '
					GROUP BY S.id_service_order
					ORDER BY S.id_service_order DESC;';
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
		}

		/**
		 * Add/Edit Shop Parts
		 * @since 30/10/2023
		 */
		public function saveShopParts() 
		{
				$idPartShop = $this->input->post('hddId');
				$idShop = $this->input->post('id_shop');

				if($idShop == ""){
					$data = array(
						'shop_name' => addslashes($this->security->xss_clean($this->input->post('shop_name'))),
						'shop_contact' => addslashes($this->security->xss_clean($this->input->post('shop_contact'))),
						'shop_address' => addslashes($this->security->xss_clean($this->input->post('shop_address'))),
						'mobile_number' => addslashes($this->security->xss_clean($this->input->post('mobile_number'))),
						'shop_email' => addslashes($this->security->xss_clean($this->input->post('shop_email')))
					);

					$query = $this->db->insert('param_shop', $data);	
					$idShop = $this->db->insert_id();		
				}

				$data = array(
					'part_description' => $this->input->post('part_description'),
					'fk_id_shop' => $idShop,
					'fk_inspection_type' => $this->input->post('type')
				);
				
				//revisar si es para adicionar o editar
				if ($idPartShop == '') {
					$query = $this->db->insert('param_parts_shop', $data);	
					$idPartShop = $this->db->insert_id();			
				} else {
					$this->db->where('id_part_shop', $idPartShop);
					$query = $this->db->update('param_parts_shop', $data);
				}
				if ($query) {
					return $idPartShop;
				} else {
					return false;
				}
		}

		/**
		 * Get parts list by store
		 * @since 30/10/2023
		 */
		public function get_parts_by_store($arrDatos) 
		{
				$this->db->select('P.*, S.*, 
						(SELECT GROUP_CONCAT(V.unit_number, " -----> ", V.description SEPARATOR "<br>") 
						FROM param_equipment_parts E 
						JOIN param_vehicle V ON V.id_vehicle = E.fk_id_equipment 
						WHERE E.fk_id_part_shop = P.id_part_shop 
						GROUP BY P.id_part_shop) AS equipments');
				$this->db->join('param_shop S', 'S.id_shop = P.fk_id_shop', 'INNER');
				if (array_key_exists("idPartShop", $arrDatos)) {
					$this->db->where('id_part_shop', $arrDatos["idPartShop"]);
				}
				$this->db->order_by('P.part_description', 'asc');
				$query = $this->db->get('param_parts_shop P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Get parts list by store by equipment
		 * @since 3/01/2024
		 */
		public function get_parts_store_by_equipment($arrDatos) 
		{
				$this->db->join('param_shop S', 'S.id_shop = P.fk_id_shop', 'INNER');
				$this->db->join('param_equipment_parts E', 'E.fk_id_part_shop = P.id_part_shop', 'INNER');
				if (array_key_exists("idVehicle", $arrDatos)) {
					$this->db->where('fk_id_equipment', $arrDatos["idVehicle"]);
				}
				$this->db->order_by('P.part_description', 'asc');
				$query = $this->db->get('param_parts_shop P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add Equipment to Shop Parts
		 * @since 13/12/2023
		 */
		public function add_equipment_shop_parts($idPartShop) 
		{
			//delete Attachements 
			$this->db->delete('param_equipment_parts', array('fk_id_part_shop' => $idPartShop));

			$query = 1;
			if ($equipment = $this->input->post('equipment')) {
				$tot = count($equipment);
				for ($i = 0; $i < $tot; $i++) {
					$data = array(
						'fk_id_part_shop' => $idPartShop,
						'fk_id_equipment' => $equipment[$i]
					);
					$query = $this->db->insert('param_equipment_parts', $data);
				}
			}
			if ($query) {
				return true;
			} else{
				return false;
			}
		}

		/**
		 * Equipment list
		 * @since 16/12/2023
		 */
		public function get_parts_equipment($arrDatos) 
		{
				if (array_key_exists("relation", $arrDatos)) {
					$this->db->select('P.fk_id_equipment');
				}else{
					$this->db->select('P.*, T.inspection_type');
					$this->db->join('param_vehicle V', 'V.id_vehicle = P.fk_id_equipment', 'INNER');
					$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = V.type_level_2', 'INNER');
				}
				if (array_key_exists("idEquipmentPart", $arrDatos)) {
					$this->db->where('fk_id_part_shop', $arrDatos["idEquipmentPart"]);
				}
				$query = $this->db->get('param_equipment_parts P');

				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}


	}