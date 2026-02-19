<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hauling_model extends CI_Model
{


	/**
	 * Add/Edit hauling
	 * @since 16/12/2016
	 */
	public function saveHauling()
	{
		$idUser = $this->session->userdata("id");
		$idHauling = $this->input->post('hddId');
		$fk_id_submodule = "";

		if ($idHauling != '') {
			$sql = "SELECT fk_id_submodule FROM hauling WHERE id_hauling = $idHauling";
			$query = $this->db->query($sql);
			$result = $query->row_array();
			$fk_id_submodule = $result['fk_id_submodule'];
		}
		
		$hourIn = $this->input->post('hourIn');
		$hourOut = $this->input->post('hourOut');

		$hourIn = $hourIn < 10 ? "0" . $hourIn : $hourIn;
		$hourOut = $hourOut < 10 ? "0" . $hourOut : $hourOut;

		$timeIn = $hourIn . ":" . $this->input->post('minIn');
		$timeOut = $hourOut . ":" . $this->input->post('minOut');

		$isUsingWO = ($this->input->post('id_work_order') == '') ? null : $this->input->post('id_work_order');
		$id_work_order = $this->input->post('list_work_order');

		if ($isUsingWO == 1) {
			$data = array(
				'date' => date("Y-m-d"),
				'fk_id_job' => $this->input->post('fromSite'),
				'observation' => $this->input->post('comments'),
				'state' => 0,
				'last_message' => 'A new Work Order was created from the Hauling',
				'fk_id_user' => $idUser,
				'date_issue' => date("Y-m-d G:i:s"),
				'fk_id_company' => $this->input->post('company'),
			);

			$query = $this->db->insert('workorder', $data);
			$id_work_order = $this->db->insert_id();

			$data = array(
				'fk_id_workorder' => $id_work_order,
				'fk_id_user' => $idUser,
				'date_issue' => date("Y-m-d G:i:s"),
				'observation' => 'A new Work Order was created from the Hauling',
				'state' => 0
			);

			$query = $this->db->insert('workorder_state', $data);
		}

		$minIn = (int)$this->input->post('minIn');
		$minOut = (int)$this->input->post('minOut');
		// Asegurarse de que las horas y minutos estén en el rango adecuado
		if (
			$hourIn < 0 || $hourIn > 23 || $minIn < 0 || $minIn > 59 ||
			$hourOut < 0 || $hourOut > 23 || $minOut < 0 || $minOut > 59
		) {
			echo "Error: Las horas deben estar entre 0-23 y los minutos entre 0-59.";
			return;
		}

		// Convertir todo a minutos
		$totalMinutesIn = ($hourIn * 60) + $minIn;
		$totalMinutesOut = ($hourOut * 60) + $minOut;

		// Calcular la diferencia en minutos
		$differenceInMinutes = $totalMinutesOut - $totalMinutesIn;

		// Asegurarse de que la diferencia no sea negativa
		if ($differenceInMinutes < 0) {
			echo "Error: La hora de salida no puede ser anterior a la hora de entrada.";
			return;
		}

		// Convertir la diferencia a horas fraccionarias
		$fractionalHours = $differenceInMinutes / 60.0; // Dividir por 60 para obtener horas

		// Redondear a dos decimales si es necesario
		$fractionalHours = round($fractionalHours, 2);

		if($id_work_order){
			if ($this->input->post('company') == 1) {
				$dataEquipment = array(
					'fk_id_workorder' => $id_work_order,
					'fk_id_type_2' => 10,
					'fk_id_vehicle' => $this->input->post('truck'),
					'fk_id_attachment' => null,
					'other' => null,
					'operatedby' => $idUser,
					'hours' => $fractionalHours,
					'quantity' => 1,
					'standby' => 2,
					'description' => $this->input->post('comments'),
				);

				if ($idHauling == '') {
					$query = $this->db->insert('workorder_equipment', $dataEquipment);
					$fk_id_submodule = $this->db->insert_id();
				} else {
					$this->db->where('id_workorder_equipment', $fk_id_submodule);
					$query = $this->db->update('workorder_equipment', $dataEquipment);
				}
			} else {
				$this->load->model("general_model");
				$arrParam = array(
					"table" => "param_company",
					"order" => "company_name",
					"column" => "id_company",
					"id" => $this->input->post('company')
				);
				$contact = $this->general_model->get_basic_search($arrParam); //company list

				//truckType´s list
				$arrParam = array(
					"table" => "param_truck_type",
					"order" => "truck_type",
					"column" => "id_truck_type",
					"id" => $this->input->post('truckType')
				);
				$equipment = $this->general_model->get_basic_search($arrParam);

				$dataOccasional = array(
					'fk_id_workorder' => $id_work_order,
					'fk_id_company' => $this->input->post('company'),
					'equipment' => $equipment[0]['truck_type'],
					'quantity' => 1,
					'unit' => $this->input->post('plate'),
					'hours' => $fractionalHours,
					'contact' => $contact[0]['contact'],
					'description' => $this->input->post('comments'),
				);

				if ($idHauling == '') {
					$query = $this->db->insert('workorder_ocasional', $dataOccasional);
					$fk_id_submodule = $this->db->insert_id();
				} else {
					$this->db->where('id_workorder_ocasional', $fk_id_submodule);
					$query = $this->db->update('workorder_ocasional', $dataOccasional);
				}
			}
		}

		$data = array(
			'fk_id_company' => $this->input->post('company'),
			'fk_id_truck' => $this->input->post('truck'),
			'fk_id_truck_type' => $this->input->post('truckType'),
			'fk_id_material' => $this->input->post('materialType'),
			'fk_id_site_from' => $this->input->post('fromSite'),
			'fk_id_site_to' => $this->input->post('toSite'),
			'plate' => $this->input->post('plate'),
			'time_in' => $timeIn,
			'time_out' => $timeOut,
			'fk_id_payment' => $this->input->post('payment'),
			'comments' => $this->input->post('comments'),
			'fk_id_workorder' => $id_work_order,
			'fk_id_submodule' => $fk_id_submodule,
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');

		//revisar si es para adicionar o editar
		if ($idHauling == '') {
			$data['date_issue'] = date("Y-m-d");
			$data['fk_id_user'] = $idUser;
			if (($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_MANAGER) && $dateIssue != "") {
				$data['date_issue'] = $dateIssue;
			}

			$query = $this->db->insert('hauling', $data);
			$idHauling = $this->db->insert_id();
		} else {
			if (($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_MANAGER) && $dateIssue != "") {
				$data['date_issue'] = $dateIssue;
			}

			$this->db->where('id_hauling', $idHauling);
			$query = $this->db->update('hauling', $data);
		}
		if ($query) {
			return $idHauling;
		} else {
			return false;
		}
	}

	/**
	 * Trucks´list by company
	 * @since 12/12/2016
	 */
	public function get_trucks_by_id($idCompany)
	{
		$trucks = array();
		$sql = "SELECT id_vehicle, CONCAT(unit_number,' -----> ', description) as unit_description 
					FROM param_vehicle 
					WHERE fk_id_company = $idCompany
					AND type_level_2 = 4
					AND state = 1
					ORDER BY unit_number";
		//pr($sql); exit;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$i = 0;
			foreach ($query->result() as $row) {
				$trucks[$i]["id_truck"] = $row->id_vehicle;
				$trucks[$i]["unit_number"] = $row->unit_description;
				$i++;
			}
		}
		$this->db->close();
		return $trucks;
	}

	/**
	 * Obtiene datos de hauling
	 * @param int $idHaulig: ID hauling
	 * @author BMOTTAG
	 * @since  23/1/2017
	 */
	public function get_hauling_byId($idHaulig)
	{
		$this->db->select('H.*, C.company_name, C.contact, C.email, C.company_type, V.unit_number, T.truck_type, M.material, P.payment, J.job_description from, L.job_description to');
		$this->db->from('hauling H');
		$this->db->where('id_hauling', $idHaulig);
		$this->db->join('param_company C', 'C.id_company = H.fk_id_company', 'LEFT');
		$this->db->join('param_vehicle V', 'V.id_vehicle = H.fk_id_truck', 'LEFT');
		$this->db->join('param_truck_type T', 'T.id_truck_type = H.fk_id_truck_type', 'LEFT');
		$this->db->join('param_material_type M', 'M.id_material = H.fk_id_material', 'LEFT');
		$this->db->join('param_jobs J', 'J.id_job = H.fk_id_site_from', 'LEFT');
		$this->db->join('param_jobs L', 'L.id_job = H.fk_id_site_from', 'LEFT');
		$this->db->join('param_payment P', 'P.id_payment = H.fk_id_payment', 'LEFT');


		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Trucks´list by company
	 * @since 12/12/2016
	 */
	public function get_wo_job_code($jobCode)
	{
		$wos = array();
		$sql = "SELECT * FROM workorder WHERE date >= CURDATE() - INTERVAL 5 DAY AND state = 0 AND fk_id_job = $jobCode;";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			$i = 0;
			foreach ($query->result() as $row) {
				$wos[$i]["id_workorder"] = $row->id_workorder;
				$wos[$i]["observation"] = $row->observation;
				$i++;
			}
		}
		$this->db->close();
		return $wos;
	}

	/**
	 * ID WorkOrder by job code
	 * @since 12/12/2016
	 */
	public function list_by_job_code($jobCode)
	{
		$sql = "SELECT * FROM hauling WHERE fk_id_site_from = $jobCode AND DATE(date_issue) = CURDATE()";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$fk_id_workorder = $result['fk_id_workorder'];
		} else {
			$fk_id_workorder = 0;
		}

		$this->db->close();
		return $fk_id_workorder;
	}

	/**
	 * Add/Edit hauling
	 * @since 16/12/2016
	 */
	public function saveHaulingByProgramming($data)
	{
		$id_work_order = $data['id_work_order'];
		$fk_id_job = $data['fk_id_job'];
		$equipment = $data['equipment'];
		$unit = $data['unit'];
		$description = $data['description'];
		$fk_id_submodule = null;
		$fk_id_programming = $data['fk_id_programming'];

		$idUser = $this->session->userdata("id");

		$hourIn = $this->input->post('hourIn');
		$hourOut = $this->input->post('hourOut');

		$hourIn = $hourIn < 10 ? "0" . $hourIn : $hourIn;
		$hourOut = $hourOut < 10 ? "0" . $hourOut : $hourOut;

		$timeIn = $hourIn . ":00";
		$timeOut = $hourOut . ":00";

		if ($id_work_order) {
			$data = array(
				'date' => date("Y-m-d"),
				'fk_id_job' => $fk_id_job,
				'observation' => $this->input->post('comments'),
				'state' => 0,
				'last_message' => 'A new Work Order was created from the Hauling',
				'fk_id_user' => $idUser,
				'date_issue' => date("Y-m-d G:i:s"),
				'fk_id_company' => $this->input->post('company'),
			);
			$this->db->where('id_workorder', $id_work_order);
			$query = $this->db->update('workorder', $data);
		}

		$minIn = (int)$this->input->post('minIn');
		$minOut = (int)$this->input->post('minOut');
		// Asegurarse de que las horas y minutos estén en el rango adecuado
		if (
			$hourIn < 0 || $hourIn > 23 || $minIn < 0 || $minIn > 59 ||
			$hourOut < 0 || $hourOut > 23 || $minOut < 0 || $minOut > 59
		) {
			echo "Error: Las horas deben estar entre 0-23 y los minutos entre 0-59.";
			return;
		}

		// Convertir todo a minutos
		$totalMinutesIn = ($hourIn * 60) + $minIn;
		$totalMinutesOut = ($hourOut * 60) + $minOut;

		// Calcular la diferencia en minutos
		$differenceInMinutes = $totalMinutesOut - $totalMinutesIn;

		// Asegurarse de que la diferencia no sea negativa
		if ($differenceInMinutes < 0) {
			echo "Error: La hora de salida no puede ser anterior a la hora de entrada.";
			return;
		}

		// Convertir la diferencia a horas fraccionarias
		$fractionalHours = $differenceInMinutes / 60.0; // Dividir por 60 para obtener horas

		// Redondear a dos decimales si es necesario
		$fractionalHours = round($fractionalHours, 2);
		if ($id_work_order) {
			if ($this->input->post('company') == 1) {

				$dataEquipment = array(
					'fk_id_workorder' => $id_work_order,
					'fk_id_type_2' => 10,
					'fk_id_vehicle' => $this->input->post('truck'),
					'fk_id_attachment' => null,
					'other' => null,
					'operatedby' => $idUser,
					'hours' => $fractionalHours,
					'quantity' => 1,
					'standby' => 2,
					'description' => $this->input->post('comments'),
				);

				$query = $this->db->insert('workorder_equipment', $dataEquipment);
				$fk_id_submodule = $this->db->insert_id();
			} else {
				$this->load->model("general_model");
				$arrParam = array(
					"table" => "param_company",
					"order" => "company_name",
					"column" => "id_company",
					"id" => $this->input->post('company')
				);
				$contact = $this->general_model->get_basic_search($arrParam); //company list

				$dataOccasional = array(
					'fk_id_workorder' => $id_work_order,
					'fk_id_company' => $this->input->post('company'),
					'equipment' => $equipment,
					'quantity' => 1,
					'unit' => $unit,
					'hours' => $fractionalHours,
					'contact' => $contact[0]['contact'],
					'description' => $description,
				);

				$query = $this->db->insert('workorder_ocasional', $dataOccasional);
				$fk_id_submodule = $this->db->insert_id();
			}
		}

		$dataHauling = array(
			'fk_id_user' => $idUser,
			'fk_id_company' => $this->input->post('company'),
			'fk_id_truck' => $this->input->post('truck'),
			'fk_id_truck_type' => null,
			'fk_id_material' => null,
			'fk_id_site_from' => $fk_id_job,
			'fk_id_site_to' => null,
			'plate' => ' ',
			'time_in' => $timeIn,
			'time_out' => $timeOut,
			'fk_id_payment' => null,
			'comments' => $description,
			'fk_id_workorder' => $id_work_order,
			'fk_id_submodule' => $fk_id_submodule,
			'fk_id_programming' => $fk_id_programming,
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');

		$dataHauling['date_issue'] = date("Y-m-d");
		if ($userRol == 99 && $dateIssue != "") {
			$dataHauling['date_issue'] = $dateIssue;
		}

		$queryHauling = $this->db->insert('hauling', $dataHauling);
		$idHauling = $this->db->insert_id();

		if ($queryHauling) {
			return $idHauling;
		} else {
			return false;
		}
	}
}
