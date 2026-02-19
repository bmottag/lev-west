<?php
defined('BASEPATH') or exit('No direct script access allowed');

class More extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("more_model");
		$this->load->helper('form');
	}

	/**
	 * environmental list
	 * @since 10/1/2018
	 * @author BMOTTAG
	 */
	public function environmental($idJob)
	{
		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//environmental info
		$arrParam = array("idJob" => $idJob);
		$data['information'] = $this->more_model->get_environmental($arrParam);

		$data["view"] = 'environmental_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form enviromental
	 * @since 10/1/2018
	 * @author BMOTTAG
	 */
	public function add_environmental($idJob, $idEnvironmental = 'x')
	{
		$data['information'] = FALSE;
		$data['deshabilitar'] = '';

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//worker´s list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idEnvironmental != 'x') {

			$arrParam = array("idJob" => $idJob);
			$data['information'] = $this->more_model->get_environmental($arrParam);

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_environmental';
		$this->load->view("layout", $data);
	}

	/**
	 * save_environmental
	 * @since 13/1/2018
	 * @author BMOTTAG
	 */
	public function save_environmental()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');

		if ($idEnvironmental = $this->more_model->add_environmental()) {
			$data["result"] = true;
			$data["mensaje"] = "You have saved the Environmental Site Inspection, continue uploading the information.";
			$data["idEnvironmental"] = $idEnvironmental;
			$this->session->set_flashdata('retornoExito', 'You have saved the Environmental Site Inspection, continue uploading the information!!');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idEnvironmental"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Signature
	 * param $typo: supervisor / manager
	 * param $idEnvironmental: llave principal del formulario
	 * param $idJob: llave principal de trabajo
	 * @since 13/1/2018
	 * @author BMOTTAG
	 */
	public function add_signature_esi($typo, $idJob, $idEnvironmental)
	{
		if (empty($typo) || empty($idJob) || empty($idEnvironmental)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of the file
			$name = "images/signature/esi/" . $typo . "_" . $idEnvironmental . ".png";

			$arrParam = array(
				"table" => "job_environmental",
				"primaryKey" => "id_job_environmental",
				"id" => $idEnvironmental,
				"column" => $typo . "_signature",
				"value" => $name
			);
			//enlace para regresar al formulario
			$data['linkBack'] = "more/add_environmental/" . $idJob . "/" . $idEnvironmental;

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
			if ($this->general_model->updateRecord($arrParam)) {
				//$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');

				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have saved your signature.";
			} else {
				//$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');

				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);

			//redirect("/safety/add_safety/" . $idSafety,'refresh');
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Generate Environmental Report in PDF
	 * @param int $idJob
	 * @since 14/1/2018
	 * @author BMOTTAG
	 */
	public function generaEnvironmentalPDF($idJob)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('ESI report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array("idJob" => $idJob);

		$data['info'] = $this->more_model->get_environmental($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_esi", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('esi_' . $idJob . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * PPE inspection list
	 * @since 15/1/2018
	 * @author BMOTTAG
	 */
	public function ppe_inspection()
	{
		//ppe inspection info
		$arrParam = array();
		$data['information'] = $this->more_model->get_ppe_inspection($arrParam);

		$data["view"] = 'ppe_inspection_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - pps inspection
	 * @since 15/1/2018
	 */
	public function cargarModalPPEInspection()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idPPEInspection"] = $this->input->post("idPPEInspection");

		if ($data["idPPEInspection"] != 'x') {
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "templates",
				"order" => "id_template",
				"column" => "id_template",
				"id" => $data["idPPEInspection"]
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		}

		$this->load->view("ppe_modal", $data);
	}

	/**
	 * Save PPE INSPECTION
	 * @since 15/1/2018
	 * @author BMOTTAG
	 */
	public function save_ppe_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idPPEInspection = $this->input->post('hddId');

		$msj = "You have added a new PPE Inspection";
		if ($idPPEInspection != '') {
			$msj = "You have updated a PPE Inspection!!";
		}

		if ($idPPEInspection = $this->more_model->savePPEInspection()) {
			$data["result"] = true;
			$data["idRecord"] = $idPPEInspection;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idRecord"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form PPE INSPECTION
	 * @since 15/1/2018
	 * @author BMOTTAG
	 */
	public function add_ppe_inspection($idPPEInspection = 'x')
	{
		$data['information'] = FALSE;

		//si envio el id, entonces busco la informacion 
		if ($idPPEInspection != 'x') {

			$arrParam = array("idPPEInspection" => $idPPEInspection);
			$data['information'] = $this->more_model->get_ppe_inspection($arrParam);

			$data['ppeInspectionWorkers'] = $this->more_model->get_ppe_inspection_workers($idPPEInspection); //workers list

			//workers list
			$this->load->model("general_model");
			$arrParam = array("state" => 1);
			$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_ppe_inspection';
		$this->load->view("layout", $data);
	}

	/**
	 * Signature
	 * param $typo: inspector / worker
	 * param $idPPEInspection: llave principal del formulario
	 * param $idWorker: llave principal del trabajador
	 * @since 22/1/2018
	 * @author BMOTTAG
	 */
	public function add_signature($typo, $idPPEInspection, $idWorker)
	{
		if (empty($typo) || empty($idPPEInspection) || empty($idWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of the file
			if ($typo == "inspector") {
				$name = "images/signature/ppe_inspection/" . $typo . "_" . $idPPEInspection . ".png";

				$arrParam = array(
					"table" => "ppe_inspection",
					"primaryKey" => "id_ppe_inspection",
					"id" => $idPPEInspection,
					"column" => "inspector_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = "more/add_ppe_inspection/" . $idPPEInspection;
			} elseif ($typo == "worker") {
				$name = "images/signature/ppe_inspection/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "ppe_inspection_workers",
					"primaryKey" => "id_ppe_inspection_worker",
					"id" => $idWorker,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores
				$data['linkBack'] = "more/add_ppe_inspection/" . $idPPEInspection . "#anclaWorker";
			}

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
			if ($this->general_model->updateRecord($arrParam)) {
				//$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');

				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have saved your signature.";
			} else {
				//$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');

				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);

			//redirect("/safety/add_safety/" . $idSafety,'refresh');
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Form Add Workers PPE INSPECTION
	 * @since 20/1/2018
	 * @author BMOTTAG
	 */
	public function add_workers_ppe_inspection($idPPEInspection)
	{
		if (empty($idPPEInspection)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//workers list
		$this->load->model("general_model");
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$data["idPPEInspection"] = $idPPEInspection;

		$data["view"] = 'form_add_workers';
		$this->load->view("layout", $data);
	}

	/**
	 * Save worker
	 * @since 21/1/2018
	 * @author BMOTTAG
	 */
	public function save_ppe_inspection_workers()
	{
		header('Content-Type: application/json');
		$data = array();
		$idPPEInspection = $this->input->post('hddIdPPEInpection');

		$data["idRecord"] = $idPPEInspection;

		if ($this->more_model->add_ppe_inspection_worker($idPPEInspection)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', 'You have added the Workers, remember to get the signature of each one.');
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Delete PPE INSPECTION worker
	 */
	public function deleteInspectionWorker()
	{
		header('Content-Type: application/json');
		$data = array();

		$identificador = $this->input->post('identificador');

		//se envian los dos ID toca dividirlos
		$porciones = explode("-", $identificador);
		$idPPEInspection = $porciones[0];
		$idPPEInspectionWorker = $porciones[1];

		$data["idRecord"] = $idPPEInspection;

		$arrParam = array(
			"table" => "ppe_inspection_workers",
			"primaryKey" => "id_ppe_inspection_worker",
			"id" => $idPPEInspectionWorker
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update inspection
	 * para editar el estado de la inspeccion
	 * @since 29/1/2018
	 * @author BMOTTAG
	 */
	public function updateInspection()
	{
		$idPPEInspection = $this->input->post('hddIdPPEInspection');

		if ($this->more_model->update_ppe_inspection_worker()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the Inspection");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('more/add_ppe_inspection/' . $idPPEInspection), 'refresh');
	}

	/**
	 * Safe one worker for the inspection
	 */
	public function add_one_worker()
	{
		$idPPEInspection = $this->input->post('hddIdPPEInspection');

		if ($this->more_model->addOneWorker()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('more/add_ppe_inspection/' . $idPPEInspection), 'refresh');
	}

	/**
	 * Generate PPE INSPECTION Report in PDF
	 * @param int $idPPEInspection
	 * @since 29/1/2018
	 * @author BMOTTAG
	 */
	public function generaPPEInspectionPDF($idPPEInspection)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('PPE INSPECTION REPORT');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array("idPPEInspection" => $idPPEInspection);
		$data['info'] = $this->more_model->get_ppe_inspection($arrParam);

		$data['ppeInspectionWorkers'] = $this->more_model->get_ppe_inspection_workers($idPPEInspection); //workers list

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_ppe_inspection", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('ppe_inspection_' . $idPPEInspection . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * manual con video
	 * @since 8/3/2018
	 * @author BMOTTAG
	 */
	public function video($tema)
	{
		switch ($tema) {
			case 'jobs':
				$data["titulo"] = 'Jobs Info intro';
				$data["enlace"] = "http://youtu.be/ESHRC4o8Hnk";
				break;
			case 1:
				$valor = 'Active';
				$clase = "text-success";
				break;
			case 2:
				$valor = 'Inactive';
				$clase = "text-danger";
				break;
		}
		$data["view"] = 'video';
		$this->load->view("layout", $data);
	}

	/**
	 * confined space entry permit list
	 * @since 13/1/2020
	 * @author BMOTTAG
	 */
	public function confined($idJob)
	{
		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//tool box info
		$arrParam = array(
			"idJob" => $idJob
		);
		$data['information'] = $this->general_model->get_confined_space($arrParam);

		$data["view"] = 'confined_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form confined space entry permit
	 * @since 14/1/2020
	 * @author BMOTTAG
	 */
	public function add_confined($idJob, $idConfined = 'x')
	{
		$data['information'] = FALSE;

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idConfined != 'x') {
			$arrParam = array(
				"idConfined" => $idConfined
			);
			$data['information'] = $this->general_model->get_confined_space($arrParam);

			$data['confinedWorkers'] = $this->more_model->get_confined_workers($idConfined, null); //workers list

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_confined';
		$this->load->view("layout", $data);
	}

	/**
	 * Save confined space entry permit
	 * @since 13/1/2020
	 * @author BMOTTAG
	 */
	public function save_confined()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');

		if ($idConfined = $this->more_model->add_confined()) {
			$data["result"] = true;
			$data["mensaje"] = "You have saved the Confined Space Entry Permit, continue uploading the information.";
			$data["idConfined"] = $idConfined;
			$this->session->set_flashdata('retornoExito', 'You have saved the Confined Space Entry Permit, continue uploading the information. Add Worker(s) in charge of entry and signatures at the end of the form.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idConfined"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form confined space entry permit WORKERS
	 * @since 5/2/2020
	 * @author BMOTTAG
	 */
	public function confined_workers($idJob, $idConfined)
	{
		$data['information'] = FALSE;

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idConfined != 'x') {
			$arrParam = array(
				"idConfined" => $idConfined
			);
			$data['information'] = $this->general_model->get_confined_space($arrParam);

			$data['confinedWorkers'] = $this->more_model->get_confined_workers($idConfined, 2); //workers list

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_confined_workers';
		$this->load->view("layout", $data);
	}

	/**
	 * Form confined space entry permit WORKERS
	 * @since 5/2/2020
	 * @author BMOTTAG
	 */
	public function workers_site($idJob, $idConfined)
	{
		$data['information'] = FALSE;

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idConfined != 'x') {
			$arrParam = array(
				"idConfined" => $idConfined
			);
			$data['information'] = $this->general_model->get_confined_space($arrParam);
			$wos = 1;
			$data['confinedWorkers'] = $this->more_model->get_confined_workers($idConfined, $wos); //workers list

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_confined_workers_site';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Add Workers confined
	 * @since 20/1/2020
	 * @author BMOTTAG
	 */
	public function add_workers_confined($idJob, $idConfined, $wos)
	{
		if (empty($idJob) || empty($idConfined)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//workers list
		$this->load->model("general_model");
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list


		$view = 'form_add_workers_confined';
		$data["idConfined"] = $idConfined;
		$data["idJob"] = $idJob;
		$data["wos"] = $wos;

		$data["view"] = $view;
		$this->load->view("layout", $data);
	}

	/**
	 * Save worker
	 * @since 20/1/2020
	 * @author BMOTTAG
	 */
	public function save_confined_workers()
	{
		header('Content-Type: application/json');
		$data = array();
		$idConfined = $this->input->post('hddIdConfined');
		$idJob = $this->input->post('hddIdJob');
		$wos = $this->input->post('hddWOS');

		$data["idRecord"] = $wos == 2 ? "confined_workers/" . $idJob . "/" . $idConfined : "workers_site/" . $idJob . "/" . $idConfined;

		if ($this->more_model->add_confined_worker($idConfined, $wos)) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";

			$this->session->set_flashdata('retornoExito', 'You have added the Workers, remember to get the signature of each one.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Delete confined worker
	 */
	public function deleteConfinedWorker($idJob, $idConfined, $idConfinedWorker)
	{
		if (empty($idJob) || empty($idConfined) || empty($idConfinedWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "job_confined_workers",
			"primaryKey" => "id_job_confined_worker",
			"id" => $idConfinedWorker
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('more/confined_workers/' . $idJob . '/' . $idConfined), 'refresh');
	}

	/**
	 * Delete confined worker
	 */
	public function deleteConfinedWorkerSite($idJob, $idConfined, $idConfinedWorker)
	{
		if (empty($idJob) || empty($idConfined) || empty($idConfinedWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "job_confined_workers",
			"primaryKey" => "id_job_confined_worker",
			"id" => $idConfinedWorker
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('more/workers_site/' . $idJob . '/' . $idConfined), 'refresh');
	}

	/**
	 * Safe one worker to Confined Space Entry
	 */
	public function confined_One_Worker()
	{
		$idJob = $this->input->post('hddIdJob');
		$idConfined = $this->input->post('hddIdConfined');

		if ($this->more_model->confinedSaveOneWorker()) {
			$this->session->set_flashdata('retornoExito', "You have added one Worker. Don't forget to sign.");
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('more/confined_workers/' . $idJob . "/" . $idConfined), 'refresh');
	}

	/**
	 * Safe one worker to Confined Space Entry
	 */
	public function confined_worker_site()
	{
		$idJob = $this->input->post('hddIdJob');
		$idConfined = $this->input->post('hddIdConfined');

		if ($this->more_model->confinedSaveWorkerOnSite()) {
			$this->session->set_flashdata('retornoExito', "You have added one Worker. Don't forget to sign.");
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('more/workers_site/' . $idJob . "/" . $idConfined), 'refresh');
	}

	/**
	 * Signature
	 * param $typo: supervisor / worker
	 * param $idConfined: llave principal del formulario
	 * param $idWorker: llave principal del trabajador
	 * @since 20/1/2020
	 * @author BMOTTAG
	 */
	public function add_signature_confined($typo, $idJob, $idConfined, $idWorker)
	{
		if (empty($typo) || empty($idConfined) || empty($idWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {
			//update signature with the name of the file
			if ($typo == "authorization") {
				$name = "images/signature/confined/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "job_confined",
					"primaryKey" => "id_job_confined",
					"id" => $idConfined,
					"column" => "authorization_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = "more/add_confined/" . $idJob . "/" . $idConfined . "#anclaSignature";
			} elseif ($typo == "worker") {
				$name = "images/signature/confined/" . $typo . "_" . $idWorker . ".png";

				//actualizo hora y fecha en que se firmo
				$arrParam = array(
					"id" => $idWorker,
					"column" => "date_time_in"
				);
				$this->more_model->updateConfinedWorkerInOut($arrParam);

				$arrParam = array(
					"table" => "job_confined_workers",
					"primaryKey" => "id_job_confined_worker",
					"id" => $idWorker,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores
				$data['linkBack'] = "more/confined_workers/" . $idJob . "/" . $idConfined . "#anclaWorker";
			} elseif ($typo == "worker_out") {
				$name = "images/signature/confined/" . $typo . "_" . $idWorker . ".png";

				//actualizo hora y fecha en que se firmo
				$arrParam = array(
					"id" => $idWorker,
					"column" => "date_time_out"
				);
				$this->more_model->updateConfinedWorkerInOut($arrParam);

				$arrParam = array(
					"table" => "job_confined_workers",
					"primaryKey" => "id_job_confined_worker",
					"id" => $idWorker,
					"column" => "signature_out",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores
				$data['linkBack'] = "more/confined_workers/" . $idJob . "/" . $idConfined . "#anclaWorker";
			} elseif ($typo == "cancellation") {
				$name = "images/signature/confined/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "job_confined",
					"primaryKey" => "id_job_confined",
					"id" => $idConfined,
					"column" => "cancellation_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = "more/add_confined/" . $idJob . "/" . $idConfined . "#anclaSignature";
			} elseif ($typo == "post_entry") {
				$name = "images/signature/confined/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "job_confined",
					"primaryKey" => "id_job_confined",
					"id" => $idConfined,
					"column" => "post_entry_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = "more/post_entry/" . $idJob . "/" . $idConfined;
			}

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
			if ($this->general_model->updateRecord($arrParam)) {
				//$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');

				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have saved your signature.";
			} else {
				//$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');

				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);

			//redirect("/safety/add_safety/" . $idSafety,'refresh');
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Update datos trabajdores
	 * @since 5/2/2020
	 * @author BMOTTAG
	 */
	public function update_confined_worker()
	{
		$idJob = $this->input->post('hddIdJob');
		$idConfined = $this->input->post('hddIdConfined');

		if ($this->more_model->saveConfinedWorker()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('more/confined_workers/' . $idJob . "/" . $idConfined), 'refresh');
	}

	/**
	 * Form re testing
	 * @since 4/2/2020
	 * @author BMOTTAG
	 */
	public function re_testing($idJob, $idConfined)
	{
		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		$arrParam = array(
			"idConfined" => $idConfined
		);
		$data['information'] = $this->general_model->get_confined_space($arrParam);

		//se filtra por company_type para que solo se pueda editar los subcontratistas
		$arrParam = array(
			"table" => "job_confined_re_testing",
			"order" => "id_job_confined_re_testing",
			"column" => " 	fk_id_job_confined",
			"id" => $idConfined
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = 'form_confined_re_testing';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario re-testing
	 * @since 4/2/2020
	 */
	public function cargarModalRetesting()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idConfined"] = $this->input->post("idConfined");
		$data["idRetesting"] = $this->input->post("idRetesting");

		$this->load->model("general_model");

		if ($data["idRetesting"] != 'x') {
			$arrParam = array(
				"idRetesting" => $data["idRetesting"]
			);
			$data['information'] = $this->general_model->get_confined_re_testing($arrParam); //info re-testing

			$data["idConfined"] = $data['information'][0]['fk_id_job_confined'];
		}
		$arrParam = array(
			"idConfined" => $data["idConfined"]
		);
		$infoConfined = $this->general_model->get_confined_space($arrParam);
		$data["idJob"] = $infoConfined[0]["fk_id_job"];

		$this->load->view("re_testing_modal", $data);
	}

	/**
	 * ADD retesting
	 * @since 4/2/2020
	 */
	public function save_re_testing()
	{
		header('Content-Type: application/json');
		$data = array();

		$idConfined = $this->input->post('hddIdConfined');
		$idRetesting = $this->input->post('hddId');
		$idJob = $this->input->post('hddIdJob');

		$data["idRecord"] = $idJob . "/" . $idConfined;

		if ($idRetesting = $this->more_model->saveRetesting()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the ENVIRONMENTAL CONDITIONS - RE TESTING");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form post entry inspection
	 * @since 6/2/2020
	 * @author BMOTTAG
	 */
	public function post_entry($idJob, $idConfined)
	{
		$data['information'] = FALSE;

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list


		//si envio el id, entonces busco la informacion 
		$arrParam = array(
			"idConfined" => $idConfined
		);
		$data['information'] = $this->general_model->get_confined_space($arrParam);

		$data["view"] = 'form_confined_post_entry';
		$this->load->view("layout", $data);
	}

	/**
	 * Save post entry inspection
	 * @since 6/2/2020
	 * @author BMOTTAG
	 */
	public function save_post_entry()
	{
		header('Content-Type: application/json');
		$data = array();

		$idJob = $this->input->post('hddIdJob');
		$idConfined = $this->input->post('hddConfined');
		$data["idRecord"] = $idJob . "/" . $idConfined;

		if ($idConfined = $this->more_model->save_post_entry()) {
			$data["result"] = true;
			$data["mensaje"] = "You have saved the Post-entry Inspection.";
			$this->session->set_flashdata('retornoExito', 'You have saved the Post-entry Inspection.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Save confined Rescue Plan
	 * @since 16/9/2024
	 * @author BMOTTAG
	 */
	public function save_rescue_plan()
	{
		header('Content-Type: application/json');
		$data = array();

		$idJob = $this->input->post('hddIdJob');
		$idConfined = $this->input->post('hddConfined');
		$data["idRecord"] = $idJob . "/" . $idConfined;

		if ($idConfined = $this->more_model->save_rescue_plan()) {
			$data["result"] = true;
			$data["mensaje"] = "You have saved the ON-SITE RESCUE PLAN.";
			$this->session->set_flashdata('retornoExito', 'You have saved the ON-SITE RESCUE PLAN.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idConfined"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Generate Template Report in PDF
	 * @param int $idConfined
	 * @since 29/1/2020
	 * @author BMOTTAG
	 */
	public function generaConfinedPDF($idConfined)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Confined space entry permit report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$this->load->model("general_model");
		$arrParam = array("idConfined" => $idConfined);
		$data['info'] = $this->general_model->get_confined_space($arrParam);

		$data['confinedWorkers'] = $this->more_model->get_confined_workers($idConfined, 2); //workers list

		$data['WorkersOnSite'] = $this->more_model->get_confined_workers($idConfined, 1); //workers list

		$arrParam = array(
			"table" => "job_confined_re_testing",
			"order" => "id_job_confined_re_testing",
			"column" => " 	fk_id_job_confined",
			"id" => $idConfined
		);
		$data['retesting'] = $this->general_model->get_basic_search($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_confined", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();

		ob_end_clean();
		//Close and output PDF document
		$pdf->Output('confined_' . $idConfined . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * TASK CONTROL list
	 * @since 7/4/2020
	 * @author BMOTTAG
	 */
	public function task_control($idJob)
	{
		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//Task Control list
		$arrParam = array("idJob" => $idJob);
		$data['information'] = $this->more_model->get_task_control($arrParam);

		$data["view"] = 'task_control_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form task control
	 * @since 7/4/2020
	 * @author BMOTTAG
	 */
	public function add_task_control($idJob, $idTaskControl = 'x')
	{
		$data['information'] = FALSE;

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//company list
		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => 2
		);
		$data['companyList'] = $this->general_model->get_basic_search($arrParam); //company list

		//si envio el id, entonces busco la informacion 
		if ($idTaskControl != 'x') {

			$arrParam = array("idTaskControl" => $idTaskControl);
			$data['information'] = $this->more_model->get_task_control($arrParam);

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_task_control';
		$this->load->view("layout", $data);
	}

	/**
	 * save _task_control
	 * @since 7/4/2020
	 * @author BMOTTAG
	 */
	public function save_task_control()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');

		if ($idTaskControl = $this->more_model->add_task_control()) {
			$data["result"] = true;
			$data["mensaje"] = "You have saved the Task Assessment and Control, don't forget to sign..";
			$data["idTaskControl"] = $idTaskControl;
			$this->session->set_flashdata('retornoExito', "You haved save the Task Assessment and Control, don't forget to sign.!!");
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idTaskControl"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Signature
	 * param $typo: supervisor / Superintendent
	 * param $idTaskControl: llave principal del formulario
	 * param $idJob: llave principal de trabajo
	 * @since 8/4/2020
	 * @author BMOTTAG
	 */
	public function add_signature_tac($typo, $idJob, $idTaskControl)
	{
		if (empty($typo) || empty($idJob) || empty($idTaskControl)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of the file
			$name = "images/signature/tac/" . $typo . "_" . $idTaskControl . ".png";

			$arrParam = array(
				"table" => "job_task_control",
				"primaryKey" => "id_job_task_control",
				"id" => $idTaskControl,
				"column" => $typo . "_signature",
				"value" => $name
			);
			//enlace para regresar al formulario
			$data['linkBack'] = "more/add_task_control/" . $idJob . "/" . $idTaskControl;

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
			if ($this->general_model->updateRecord($arrParam)) {
				//$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');

				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have saved your signature.";
			} else {
				//$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');

				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);

			//redirect("/safety/add_safety/" . $idSafety,'refresh');
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Form confined space entry permit - WORON-SITE RESCUE PLANKERS
	 * @since 7/9/2024
	 * @author BMOTTAG
	 */
	public function rescue_plan($idJob, $idConfined)
	{
		$data['information'] = FALSE;

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idConfined != 'x') {
			$arrParam = array(
				"idConfined" => $idConfined
			);
			$data['information'] = $this->general_model->get_confined_space($arrParam);
			$wos = 1;
			$data['confinedWorkers'] = $this->more_model->get_confined_workers($idConfined, $wos); //workers list

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_confined_rescue_plan';
		$this->load->view("layout", $data);
	}

	/**
	 * Generate Task Control Report in PDF
	 * @param int $idTaskControl
	 * @since 14/4/2020
	 * @author BMOTTAG
	 */
	public function generaTaskControlPDF($idTaskControl)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Task Control Report');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->setPrintFooter(false); //no imprime el pie ni la linea 

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 8);

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		$arrParam = array("idTaskControl" => $idTaskControl);
		$data['info'] = $this->more_model->get_task_control($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_task_control", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('tac_' . $idTaskControl . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}
}
