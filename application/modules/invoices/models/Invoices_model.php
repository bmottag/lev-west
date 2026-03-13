<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_model extends CI_Model {

	/**
	 * Predios
	 * @since 27/02/2025
	 */
	public function get_invoices($arrData) 
	{		
		$this->db->select("I.*, S.*, J.id_job, job_description, C.*");
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
		if (array_key_exists("status", $arrData) && $arrData["status"] != 'x') {
			$this->db->where('I.invoice_status', $arrData["status"]);
		}
		if (array_key_exists("number", $arrData) && $arrData["number"] != 'x') {
			$this->db->like('I.number', $arrData["number"]);
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
			'number' => $this->input->post('number'),
			'is_wo_or_claim' => $this->input->post('link_to'),
			'fk_id_wo_or_claim' => $this->input->post('list_work_order')
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

	/**
	 * Get Invoices Items
	 * @since 9/03/2026
	 */
	public function get_invoices_items($arrData)
	{
		$this->db->select();
		if (array_key_exists("idInvoice", $arrData)) {
			$this->db->where('I.fk_id_invoice', $arrData["idInvoice"]);
		}
		$query = $this->db->get('invoices_items I');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Claim list by Job Code
	 * @since 10/03/2026
	 * @author BMOTTAG
	 */
	public function get_claim_by_job_code($jobCode)
	{
		$wos = array();
		$sql = "SELECT * FROM claim WHERE date_issue_claim >= CURDATE() - INTERVAL 10 DAY AND fk_id_job = $jobCode;";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			$i = 0;
			foreach ($query->result() as $row) {
				$wos[$i]["id_claim"] = $row->id_claim;
				$wos[$i]["observation"] = $row->observation_claim;
				$i++;
			}
		}
		$this->db->close();
		return $wos;
	}

	/**
	 * Add invoice
	 * @since 10/03/2026
	 * @author BMOTTAG
	 */
	public function saveItem()
	{
		$rate = $this->input->post('rate');
		$quantity = $this->input->post('quantity');

		$value = $rate * $quantity;

		$data = array(
			'fk_id_invoice' => $this->input->post('hddIdInvoice'),
			'description' => $this->input->post('description'),
			'quantity' => $this->input->post('quantity'),
			'unit' => $this->input->post('unit'),
			'rate' => $this->input->post('rate'),
			'value' => $value
		);
		$query = $this->db->insert('invoices_items', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function insertItem($data)
	{
		$this->db->insert('invoices_items', $data);
		return $this->db->insert_id();
	}

	public function updateItem($idItem, $data)
	{
		$this->db->where('id_invoices_items', $idItem);
		return $this->db->update('invoices_items', $data);
	}

    public function save_image($data)
    {
        $this->db->insert('invoices_images', $data);
    }

    public function get_images($invoice_id)
    {
        $this->db->where('fk_id_invoice', $invoice_id);
        return $this->db->get('invoices_images')->result();
    }

	public function save_payment($data)
	{
		$this->db->insert('invoices_payments', $data);
	}

	public function get_payments($invoice_id)
	{
		$this->db->where('fk_id_invoice', $invoice_id);
		$this->db->order_by('date_paid','DESC');
		return $this->db->get('invoices_payments')->result();
	}
		
	    
}