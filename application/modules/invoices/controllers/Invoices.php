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

			 $data['images'] = $this->invoices_model->get_images($id);

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

			$is_wo_or_claim = $this->input->post('link_to');
			if($is_wo_or_claim == 'wo'){
				$arrParam["idWorkorder"] = $this->input->post('list_work_order');

				// Traer información de la WO
				$this->load->model('workorders/workorders_model');
				$workorderPersonal   = $this->workorders_model->get_workorder_personal($arrParam);
				$workorderMaterials  = $this->workorders_model->get_workorder_materials($arrParam);
				$workorderReceipt    = $this->workorders_model->get_workorder_receipt($arrParam);
				$workorderEquipment  = $this->workorders_model->get_workorder_equipment($arrParam);
				$workorderOcasional  = $this->workorders_model->get_workorder_ocasional($arrParam);

				// Insertar items
				$this->insertInvoiceItems($workorderPersonal, $idInvoice, 'personal');
				$this->insertInvoiceItems($workorderMaterials, $idInvoice, 'materials');
				$this->insertInvoiceItems($workorderEquipment, $idInvoice, 'equipment');
				$this->insertInvoiceItems($workorderOcasional, $idInvoice, 'ocasional');
				$this->insertInvoiceItems($workorderReceipt, $idInvoice, 'receipt');

			}else if($is_wo_or_claim == 'claim'){
				$data['fk_id_wo_or_claim'] = $this->input->post('list_claim');
			}

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

	/**
	 * Save items
	 * @since 11/03/2026
	 * @author BMOTTAG
	 */
	public function save_item()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdInvoice');
		if ($this->invoices_model->saveItem()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Save all information
	 * @since 11/03/2026
	 * @author BMOTTAG
	 */
	public function save_all()
	{
		$ids = $this->input->post('id_item');
		$descriptions = $this->input->post('description');
		$quantities = $this->input->post('quantity');
		$units = $this->input->post('unit');
		$rates = $this->input->post('rate');

		for ($i = 0; $i < count($ids); $i++) {

			$value = $rates[$i] * $quantities[$i];

			$data = array(
				'description' => $descriptions[$i],
				'quantity' => $quantities[$i],
				'unit' => $units[$i],
				'rate' => $rates[$i],
				'value' => $value
			);

			$this->invoices_model->updateItem($ids[$i], $data);
		}

		redirect('invoices/add_invoice/' . $this->input->post('hddIdInvoice'), 'refresh');
	}

	/**
	 * Delete item record
	 * @param int $idItem: id que se va a borrar
	 * @param int $idInvoice
	 */
	public function delete_item($idItem, $idInvoice)
	{
		if (empty($idItem) || empty($idInvoice)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "invoices_items",
			"primaryKey" => "id_invoices_items",
			"id" => $idItem
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one Item.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect('invoices/add_invoice/' . $idInvoice, 'refresh');
	}

	private function insertInvoiceItems($items, $idInvoice, $type)
	{
		if (!$items) {
			return;
		}

		foreach ($items as $item) {

			switch ($type) {

				case 'personal':
					$description = $item['employee_type'] . ' - ' . $item['description'] . ' by ' . $item['name'];
					$quantity = $item['hours'];
					$unit = 'Hours';
					break;

				case 'materials':
					$description = $item['description'] . ' - ' . $item['material'];
					if($item['markup'] > 0){
						$description = $description . ' - Plus M.U.';
					}
					$quantity = $item['quantity'];
					$unit = $item['unit'];
					break;

				case 'equipment':
					$attachment = '';

					if($item['fk_id_attachment'] != "" && $item['fk_id_attachment'] != 0){
						$attachment = 'ATTACHMENT: ' . $item["attachment_number"] . " - " . $item["attachment_description"] . ' ';
					}

					//si es tipo miscellaneous -> 8, entonces la description es diferente
					if($item['fk_id_type_2'] == 8){
						$equipment = $item['miscellaneous'] . " - " . $item['other'];
						$description = preg_replace('([^A-Za-z0-9 ])', ' ', $item['description']);
					}else{
						$equipment = "Unit #: " .$item['unit_number'] . " Make: " . $item['make'] . " Model: " . $item['model'];
						$description = $item['v_description'] . " - " . preg_replace('([^A-Za-z0-9 ])', ' ', $item['description']);
					}
					
					$description = $attachment . $equipment . ' Description: ' . $description . ', operated by ' . $item['operatedby'];

					$quantity = $item['hours'];
					$unit = 'Hours';
					break;

				case 'ocasional':
					$description = $item['description'];
					if($item['markup'] > 0){
						$description = $description . ' - Plus M.U.';
					}
					$quantity = $item['quantity'];
					$unit = $item['unit'];
					break;

				case 'receipt':
					$description = $item['description'] . ' - ' . $item['place'];
					if($item['markup'] > 0){
						$description = $description . ' - Plus M.U.';
					}
					$quantity = 1;
					$unit = 'Receipt';
					break;

				default:
					return;
			}

			$rate = isset($item['rate']) ? $item['rate'] : $item['value'];

			$dataItem = array(
				'fk_id_invoice' => $idInvoice,
				'description' => $description,
				'quantity' => $quantity,
				'unit' => $unit,
				'rate' => $rate,
				'value' => $item['value']
			);

			$this->invoices_model->insertItem($dataItem);
		}
	}

	/**
	 * Generate INVOICE Report in PDF
	 * @param int $idInvoice
	 * @since 12/03/2026
	 * @author BMOTTAG
	 */
	public function generaInvoicePDF($idInvoice)
	{
		$this->load->library('Pdf');

		$pdf = new TCPDF();

		$pdf->setPrintHeader(false);


		$pdf->setFooterMargin(20);
		$pdf->setPrintFooter(true);

		$pdf->SetMargins(15, 15, 15);
		$pdf->SetAutoPageBreak(TRUE, 15);

		$pdf->SetFont('helvetica', '', 9);

		$arrParam = array("idInvoice" => $idInvoice);

		$data['info'] = $this->invoices_model->get_invoices($arrParam);
		$data['logo'] = FCPATH . 'images/logo_black.jpg';

		if(empty($data['info'])){
			show_error('No Invoice found',404);
		}

		$data['items'] = $this->invoices_model->get_invoices_items($arrParam);

		$pdf->AddPage();

		/* INVOICE VERTICAL */
		$pdf->StartTransform();
		$pdf->Rotate(90,15,70);
		$pdf->SetFont('helvetica','B',36);
		$pdf->Text(15,70,'INVOICE');
		$pdf->StopTransform();

		// Resetear fuente
		$pdf->SetFont('helvetica','',9);

		// Mover el puntero a la derecha para que el HTML no choque con INVOICE
		$pdf->SetXY(35, 25); // X = espacio a la derecha de INVOICE, Y = margen superior

		$html = $this->load->view("report_invoice", $data, true);

		$pdf->writeHTML($html, true, false, false, false, '');
		$pdf->Output('invoice_'.$data['info'][0]['number'].'.pdf','I');
	}

    public function upload_image($idInvoice)
    {

        $config['upload_path'] = './images/invoices/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 5000;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('image')) {

            $error = $this->upload->display_errors();
            echo $error;

        } else {

            $data = $this->upload->data();

            $save = [
                'fk_id_invoice' => $idInvoice,
                'file_name'  => $data['file_name']
            ];

            $this->invoices_model->save_image($save);

            redirect('invoices/add_invoice/' . $idInvoice, 'refresh');
        }
    }


	
}