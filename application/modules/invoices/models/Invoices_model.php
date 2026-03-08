<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_model extends CI_Model {

	/**
	 * Predios
	 * @since 27/02/2025
	 */
	public function get_invoices($arrData) 
	{		
		$this->db->select("I.*, S.*, J.id_job, job_description, C.id_company, C.company_name company");
		$this->db->join('param_status S', 'S.status_slug = I.invoice_status', 'INNER');
		$this->db->join('param_jobs J', 'J.id_job = I.fk_id_job', 'INNER');
		$this->db->join('param_company C', 'C.id_company = J.fk_id_company', 'LEFT');
		if (array_key_exists("idInvoice", $arrData)) {
			$this->db->where('I.id_invoice', $arrData["idInvoice"]);
		}
		if (array_key_exists("date", $arrData) && $arrData["date"] != 'x') {
			$this->db->where('I.date_issue', $arrData["date"]);
		}
		if (array_key_exists("idJobCode", $arrData) && $arrData["idJobCode"] != 'x') {
			$this->db->where('I.fk_id_job', $arrData["idJobCode"]);
		}
		if (array_key_exists("idEmpleado", $arrData) && $arrData["idEmpleado"] != 'x') {
			$this->db->where('E.numero_unico', $arrData["idEmpleado"]);
		}
		$query = $this->db->get('invoices I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Add Invoice
	 * @since 05/03/2026
	 * @author BMOTTAG
	 */	
	public function add_invoice()
	{
		$idUser = $this->session->userdata("id");
		$idInvoice = $this->input->post('hddIdentificador');

		$data = array(
			'fk_id_job' => $this->input->post('jobName'),
			'date_issue' => $this->input->post('date'),
			'due_date' => $this->input->post('due_date'),
			'number' => $this->input->post('number')
		);

		//revisar si es para adicionar o editar
		if ($idInvoice == '') {
			$data['fk_id_user'] = $idUser;
			$data['invoice_status'] = 'draft';
			$query = $this->db->insert('invoices', $data);
			$idInvoice = $this->db->insert_id();
		} else {
			$data['invoice_status'] = $this->input->post('status');
			$this->db->where('id_invoice', $idInvoice);
			$query = $this->db->update('invoices', $data);
		}

		if ($query) {
			return $idInvoice;
		} else {
			return false;
		}
	}

	public function getNextInvoiceNumber()
	{
		$year = date("Y");

		$this->db->select("number");
		$this->db->like("number", $year . "-", "after");
		$this->db->order_by("number", "DESC");
		$this->db->limit(1);

		$query = $this->db->get("invoices");

		if ($query->num_rows() > 0) {

			$row = $query->row();
			$parts = explode("-", $row->number);

			$next = intval($parts[1]) + 1;

		} else {
			$next = 1;
		}

		return str_pad($next, 3, "0", STR_PAD_LEFT);
	}
		
	    
}