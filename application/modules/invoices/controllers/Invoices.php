<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("invoices_model");
    }

	/**
	 * Predios
	 * @since 04/03/2026
	 * @author BMOTTAG
	 */
	public function index()
	{
		$arrParam = array();
		if($this->input->post('date')){
			$arrParam['date'] = $this->input->post('date');
		}
		if($this->input->post('idJobCode')){
			$arrParam['idJobCode'] = $this->input->post('idJobCode');
		}
		if($this->input->post('status')){
			$arrParam['status'] = $this->input->post('status');
		}
		if($this->input->post('number')){
			$arrParam['number'] = $this->input->post('number');
		}
		$data['info'] = $this->invoices_model->get_invoices($arrParam);

		//company list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);

		$arrParam = array(
			"table" => "param_status",
			"order" => "status_order",
			"column" => "status_key",
			"id" => "invoices"
		);
		$data['statusList'] = $this->general_model->get_basic_search($arrParam);
			
		$data["view"] = 'invoices';
		$this->load->view("layout", $data);
	}
	
	/**
	 * Form Add Invoice
	 * @since 5/03/2026
	 * @author BMOTTAG
	 */
	public function add_invoice($id = 'x')
	{
		$data['information'] = FALSE;
		$data['deshabilitar'] = '';

		$this->load->model("general_model");
		//job list - (active items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);

		$arrParam = array(
			"table" => "param_status",
			"order" => "status_order",
			"column" => "status_key",
			"id" => "invoices"
		);
		$data['statusList'] = $this->general_model->get_basic_search($arrParam);

		$data["nextInvoiceNumber"] = $this->invoices_model->getNextInvoiceNumber();
		
		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array('idInvoice' => $id);
			$data['information'] = $this->invoices_model->get_invoices($arrParam); //info invoice

			$data['items'] = $this->invoices_model->get_invoices_items($arrParam); //items

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}


		}

		$data["view"] = 'form_invoice';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Invoice
	 * @since 05/03/2026
	 * @author BMOTTAG
	 */
	public function save_invoice()
	{
		header('Content-Type: application/json');
		$data = array();

		$idInvoiceInicial = $this->input->post('hddIdentificador');

		$msj = "You have added a new Invoice, continue uploading the information.";
		if ($idInvoiceInicial != '') {
			$msj = "You have updated the Invoice, continue uploading the information.";
		}

		if ($idInvoice = $this->invoices_model->add_invoice()) {
			$data["result"] = true;
			$data["mensaje"] = $msj;
			$data["idInvoice"] = $idInvoice;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idInvoice"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Claim list
	 * @since 10/03/2026
	 * @author BMOTTAG
	 */
	public function claimList()
	{
		header("Content-Type: text/plain; charset=utf-8");
		$jobCode = $this->input->post('jobCode');
		$list = $this->invoices_model->get_claim_by_job_code($jobCode);
		echo "<option value=''>Select...</option>";
		if ($list) {
			foreach ($list as $fila) {
				echo "<option value='" . $fila["id_claim"] . "' >" . $fila["id_claim"] . " - " . $fila["observation"] . "</option>";
			}
		}
	}

	/**
	 * Cargo modal- formulario de captura items
	 * @since 10/03/2026
	 */
	public function cargarModalItems()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idInvoice"] = $this->input->post("idInvoice");

		$this->load->view("modal_items", $data);
	}


	
}