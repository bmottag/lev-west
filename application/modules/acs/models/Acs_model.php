<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Acs_model extends CI_Model
{

	/**
	 * ACS list
	 * @since 10/01/2025
	 */
	public function get_acs($arrDatos)
	{
		$this->db->select('W.*, J.id_job, job_description, CONCAT(U.first_name, " ", U.last_name) name, C.company_name company, C.id_company');
		$this->db->join('param_jobs J', 'J.id_job = W.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');

		if (array_key_exists("idACS", $arrDatos)) {
			$this->db->where('id_acs', $arrDatos["idACS"]);
		}
		if (array_key_exists("jobId", $arrDatos) && $arrDatos["jobId"] != '' && $arrDatos["jobId"] != 0) {
			$this->db->where('W.fk_id_job', $arrDatos["jobId"]);
		}
		$this->db->order_by('id_acs', 'desc');
		$query = $this->db->get('acs W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get ACS personal info
	 * @since 10/01/2025
	 */
	public function get_acs_personal($arrData)
	{
		$this->db->select("W.*, CONCAT(first_name, ' ', last_name) name, T.employee_type");
		$this->db->join('user U', 'U.id_user = W.fk_id_user', 'INNER');
		$this->db->join('param_employee_type T', 'T.id_employee_type = W.fk_id_employee_type', 'INNER');
		if (array_key_exists("idACS", $arrData)) {
			$this->db->where('W.fk_id_acs', $arrData["idACS"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		$this->db->order_by('U.first_name, U.last_name', 'asc');
		$query = $this->db->get('acs_personal W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get ACS materials info
	 * @since 10/01/2025
	 */
	public function get_acs_materials($arrData)
	{
		$this->db->select();
		$this->db->join('param_material_type M', 'M.id_material = W.fk_id_material', 'INNER');
		if (array_key_exists("idACS", $arrData)) {
			$this->db->where('W.fk_id_acs', $arrData["idACS"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		$this->db->order_by('M.material', 'asc');
		$query = $this->db->get('acs_materials W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get ACS Receipt info
	 * @since 10/01/2025
	 */
	public function get_acs_receipt($arrData)
	{
		$this->db->select();
		if (array_key_exists("idACS", $arrData)) {
			$this->db->where('W.fk_id_acs', $arrData["idACS"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		$this->db->order_by('W.place', 'asc');
		$query = $this->db->get('acs_receipt W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get ACS equipment info
	 * @since 10/01/2025
	 */
	public function get_acs_equipment($arrData)
	{
		$this->db->select("W.*, V.make, V.model, V.unit_number, V.description v_description, M.miscellaneous, T.type_2, C.*, CONCAT(U.first_name,' ', U.last_name) as operatedby, A.attachment_number, A.attachment_description");
		$this->db->join('param_vehicle V', 'V.id_vehicle = W.fk_id_vehicle', 'LEFT');
		$this->db->join('param_attachments A', 'A.id_attachment = W.fk_id_attachment', 'LEFT');
		$this->db->join('param_miscellaneous M', 'M.id_miscellaneous = W.fk_id_vehicle', 'LEFT');
		$this->db->join('user U', 'U.id_user = W.operatedby', 'LEFT');
		$this->db->join('param_vehicle_type_2 T', 'T.id_type_2 = W.fk_id_type_2', 'INNER');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'LEFT');
		if (array_key_exists("idACS", $arrData)) {
			$this->db->where('W.fk_id_acs', $arrData["idACS"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		$query = $this->db->get('acs_equipment W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Get ACS ocasional info
	 * @since 10/01/2025
	 */
	public function get_acs_ocasional($arrData)
	{
		$this->db->select('W.*, C.company_name');
		$this->db->join('param_company C', 'C.id_company = W.fk_id_company', 'INNER');
		if (array_key_exists("idACS", $arrData)) {
			$this->db->where('W.fk_id_acs', $arrData["idACS"]);
		}
		if (array_key_exists("view_pdf", $arrData)) {
			$this->db->where('W.view_pdf', 1);
		}
		$this->db->order_by('C.company_name', 'asc');
		$query = $this->db->get('acs_ocasional W');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Update ACS information
	 * @since 17/01/2025
	 */
	public function saveInfoACS($id, $data, $formType)
	{
		$this->db->where('id_acs_' . $formType, $id);
		return $this->db->update('acs_' . $formType, $data);
	}

	/**
	 * Add personal
	 * @since 18/01/2025
	 */
	public function savePersonal()
	{
		$data = array(
			'fk_id_acs' => $this->input->post('hddIdACS'),
			'fk_id_user' => $this->input->post('employee'),
			'fk_id_employee_type' => $this->input->post('type'),
			'hours' => $this->input->post('hour'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('acs_personal', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Material
	 * @since 18/01/2025
	 */
	public function saveMaterial()
	{
		$data = array(
			'fk_id_acs' => $this->input->post('hddIdACS'),
			'fk_id_material' => $this->input->post('material'),
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'description' => $this->input->post('description')
		);

		$query = $this->db->insert('acs_materials', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Equipment
	 * @since 18/01/2025
	 */
	public function saveEquipment()
	{
		$type = $this->input->post('type');
		$truck = $this->input->post('truck');

		if ($type == 8) {
			$truck = 5;
		}
		//si es diferente a Pickup entonces colocar que Stanby = NO
		if ($type != 3) {
			$standby = 2;
		} else {
			$standby = $this->input->post('standby');
		}
		$data = array(
			'fk_id_acs' => $this->input->post('hddIdACS'),
			'fk_id_type_2' => $this->input->post('type'),
			'fk_id_vehicle' => $truck,
			'fk_id_attachment' => $this->input->post('attachment'),
			'other' => $this->input->post('otherEquipment'),
			'operatedby' => $this->input->post('operatedby'),
			'hours' => $this->input->post('hour'),
			'quantity' => $this->input->post('quantity'),
			'standby' => $standby,
			'description' => $this->input->post('description')
		);
		$query = $this->db->insert('acs_equipment', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Invoice
	 * @since 18/01/2025
	 */
	public function saveReceipt()
	{
		$price = $this->input->post('price');
		if (!$price) {
			$price = 0;
		}

		$data = array(
			'fk_id_acs' => $this->input->post('hddIdACS'),
			'place' => $this->input->post('place'),
			'price' => $price,
			'description' => $this->input->post('description')
		);
		$query = $this->db->insert('acs_receipt', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add Ocasional
	 * @since 18/01/2025
	 */
	public function saveOcasional()
	{
		$data = array(
			'fk_id_acs' => $this->input->post('hddIdACS'),
			'fk_id_company' => $this->input->post('company'),
			'equipment' => $this->input->post('equipment'),
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'hours' => $this->input->post('hour'),
			'contact' => $this->input->post('contact'),
			'description' => $this->input->post('description')
		);
		$query = $this->db->insert('acs_ocasional', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Contar registros de workorder
	 * Int idJob
	 * @author BMOTTAG
	 * @since  04/02/2025
	 */
	public function countACS($arrDatos)
	{
		$sql = "SELECT count(id_acs) CONTEO";
		$sql .= " FROM acs W";
		$sql .= " WHERE 1=1";
		if (array_key_exists("idJob", $arrDatos)) {
			$sql .= " AND fk_id_job = " . $arrDatos["idJob"];
		} 
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->CONTEO;
	}

	/**
	 * Sumatoria de horas de personal
	 * Int idJob
	 * @author BMOTTAG
	 * @since  04/02/2025
	 */
	public function countHoursPersonal($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(hours),2) TOTAL";
		$sql .= " FROM acs_personal P";
		$sql .= " INNER JOIN acs W on W.id_acs = P.fk_id_acs";
		$sql .= " WHERE W.fk_id_job =" . $arrDatos["idJob"];
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

	/**
	 * Sumatoria de valores para ACS
	 * Int idJob
	 * Var table
	 * @author BMOTTAG
	 * @since  10/2/2020
	 */
	public function countIncome($arrDatos)
	{
		$sql = "SELECT ROUND(SUM(value),2) TOTAL";
		$sql .= " FROM " . $arrDatos["table"] . " P";
		$sql .= " INNER JOIN acs W on W.id_acs = P.fk_id_acs";
		$sql .= " WHERE W.fk_id_job =" . $arrDatos["idJob"];
		
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->TOTAL;
	}

}
