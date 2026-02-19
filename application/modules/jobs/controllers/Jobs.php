<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jobs extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("jobs_model");
		$this->load->helper('form');
	}

	/**
	 * Seleccionar job para tool box
	 * @since 24/10/2017
	 * @author BMOTTAG
	 */
	public function index()
	{
		$this->load->model("general_model");
		//job´s list - (active´s items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data['dashboardURL'] = $this->session->userdata("dashboardURL");

		$data["view"] = 'jobs_safety_list';
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * tool_box list
	 * @since 24/10/2017
	 * @author BMOTTAG
	 */
	public function tool_box($idJob)
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
		$data['information'] = $this->general_model->get_tool_box($arrParam);

		$data["view"] = 'tool_box_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Tool Box
	 * @since 24/10/2017
	 * @author BMOTTAG
	 */
	public function add_tool_box($idJob, $idToolBox = 'x')
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

		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => 2
		);
		$data['companyList'] = $this->general_model->get_basic_search($arrParam); //company list

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idToolBox != 'x') {

			$arrParam = array(
				"idToolBox" => $idToolBox
			);
			$data['information'] = $this->general_model->get_tool_box($arrParam);

			$data['newHazards'] = $this->jobs_model->get_new_hazards($idToolBox); //new hazard list
			$data['toolBoxWorkers'] = $this->jobs_model->get_tool_box_workers($idToolBox); //workers list

			//tool box subcontractors workers list
			$data['toolBoxSubcontractorsWorkers'] = $this->jobs_model->get_tool_box_subcontractors_workers($idToolBox);

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_tool_box';
		$this->load->view("layout", $data);
	}

	/**
	 * Save tool box
	 * @since 24/10/2017
	 * @author BMOTTAG
	 */
	public function save_tool_box()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');

		if ($idToolBox = $this->jobs_model->add_TOOLBOX()) {
			$data["result"] = true;
			$data["mensaje"] = "You have saved the IHSR, continue uploading the information.";
			$data["idToolBox"] = $idToolBox;
			$this->session->set_flashdata('retornoExito', 'You have saved the IHSR, continue uploading the information!!');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idToolBox"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Signature
	 * param $typo: supervisor / worker
	 * param $idToolBox: llave principal del formulario
	 * param $idWorker: llave principal del trabajador
	 * @since 24/5/2017
	 * @author BMOTTAG
	 */
	public function add_signature($typo, $idJob, $idToolBox, $idWorker)
	{
		if (empty($typo) || empty($idToolBox) || empty($idWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of the file
			if ($typo == "supervisor") {
				$name = "images/signature/tool_box/" . $typo . "_" . $idToolBox . ".png";

				$arrParam = array(
					"table" => "tool_box",
					"primaryKey" => "id_tool_box",
					"id" => $idToolBox,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = "jobs/add_tool_box/" . $idJob . "/" . $idToolBox;
			} elseif ($typo == "worker") {
				$name = "images/signature/tool_box/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "tool_box_workers",
					"primaryKey" => "id_tool_box_worker",
					"id" => $idWorker,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores
				$data['linkBack'] = "jobs/add_tool_box/" . $idJob . "/" . $idToolBox . "#anclaWorker";
			} elseif ($typo == "subcontractor") {
				$name = "images/signature/tool_box/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "tool_box_workers_subcontractor",
					"primaryKey" => "id_tool_box_subcontractor",
					"id" => $idWorker,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores		
				$data['linkBack'] = "jobs/add_tool_box/" . $idJob . "/" . $idToolBox . "#anclaSubcontractor";
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
	 * Cargo modal- formulario de captura new hazard
	 * @since 25/10/2017
	 */
	public function cargarModalNewHazard()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idToolBox = $this->input->post("idToolBox");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idToolBox);
		$data["idToolBox"] = $porciones[1];

		$this->load->view("modal_new_hazard", $data);
	}

	/**
	 * Save new hazard
	 * @since 25/10/2017
	 * @author BMOTTAG
	 */
	public function save_modal_new_hazard()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idToolBox"] = $this->input->post('hddidToolBox');

		//buscar ID del JOB
		$arrParam = array(
			"idToolBox" => $data["idToolBox"]
		);
		$this->load->model("general_model");
		$infoToolBox = $this->general_model->get_tool_box($arrParam);
		$data["idJob"] = $infoToolBox[0]["fk_id_job"];

		if ($this->jobs_model->saveNewHazard()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update new hazard
	 * para editar un registro de new hazard
	 * @since 25/10/2017
	 * @author BMOTTAG
	 */
	public function update_new_hazard()
	{
		$idJob = $this->input->post('hddIdJob');
		$idToolBox = $this->input->post('hddIdToolBox');

		if ($this->jobs_model->updateNewHazard()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('jobs/add_tool_box/' . $idJob . '/' . $idToolBox), 'refresh');
	}

	/**
	 * Delete NEW HAZARD
	 */
	public function deleteRecordNewHazard()
	{
		header('Content-Type: application/json');

		$identificador = $this->input->post("identificador");
		//toca recuperar todos los ID
		$porciones = explode("-", $identificador);

		$idNewHazard = $porciones[0];
		$idToolBox = $porciones[1];
		$idJob = $porciones[2];

		$data["idRecord"] = $idJob . '/' . $idToolBox;

		//eliminaos registros
		$arrParam = array(
			"table" => "tool_box_new_hazard",
			"primaryKey" => "id_new_hazard",
			"id" => $idNewHazard
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$data["result"] = true;
			$data["mensaje"] = "You have deleted one record.";
			$this->session->set_flashdata('retornoExito', 'You have deleted one record');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Add Workers tool box
	 * @since 2/11/2017
	 * @author BMOTTAG
	 */
	public function add_workers_tool_box($idJob, $idToolBox)
	{
		if (empty($idJob) || empty($idToolBox)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//workers list
		$this->load->model("general_model");
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$view = 'form_add_workers';
		$data["idToolBox"] = $idToolBox;
		$data["idJob"] = $idJob;

		$data["view"] = $view;
		$this->load->view("layout", $data);
	}

	/**
	 * Save worker
	 * @since 2/11/2017
	 * @author BMOTTAG
	 */
	public function save_tool_box_workers()
	{
		header('Content-Type: application/json');
		$data = array();
		$idToolBox = $this->input->post('hddIdToolBox');
		$idJob = $this->input->post('hddIdJob');

		$data["idRecord"] = $idJob . "/" . $idToolBox;

		if ($this->jobs_model->add_tool_box_worker($idToolBox)) {
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
	 * Delete tool box worker
	 */
	public function deleteToolBoxWorker($idJob, $idToolBox, $idToolBoxWorker)
	{
		if (empty($idJob) || empty($idToolBox) || empty($idToolBoxWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "tool_box_workers",
			"primaryKey" => "id_tool_box_worker",
			"id" => $idToolBoxWorker
		);

		if ($this->jobs_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/add_tool_box/' . $idJob . '/' . $idToolBox), 'refresh');
	}

	/**
	 * Form ERP
	 * @since 20/11/2017
	 * @author BMOTTAG
	 */
	public function erp($idJob, $error = '')
	{
		$data['information'] = FALSE;
		$data['trainingWorkers'] = FALSE;
		$data['deshabilitar'] = '';
		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//worker list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //worker list

		//ERP info
		$arrParam = array(
			"idJob" => $idJob
		);
		$data['information'] = $this->jobs_model->get_erp($arrParam);


		//erp training list
		$data['trainingWorkers'] = $this->jobs_model->get_erp_training_workers($idJob);


		$data["view"] = 'form_erp';
		$this->load->view("layout", $data);
	}

	/**
	 * Save ERP
	 * @since 20/11/2017
	 * @author BMOTTAG
	 */
	public function save_erp()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');

		if ($this->jobs_model->add_erp()) {
			$data["result"] = true;
			$data["mensaje"] = "You have save the ERP.";
			$this->session->set_flashdata('retornoExito', 'You have saved the ERP!!');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Add Workers for ERP Trainning
	 * @since 23/11/2017
	 * @author BMOTTAG
	 */
	public function add_workers_training($id)
	{
		if (empty($id)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//workers list
		$this->load->model("general_model");
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$view = 'form_add_workers_training';
		$data["idJob"] = $id;
		$data["view"] = $view;
		$this->load->view("layout", $data);
	}

	/**
	 * Save worker trainigno
	 * @since 23/11/2017
	 * @author BMOTTAG
	 */
	public function save_training_workers()
	{
		header('Content-Type: application/json');
		$data = array();
		$idJob = $this->input->post('hddIdJob');

		$data["idRecord"] = $idJob;

		if ($this->jobs_model->add_training_worker($idJob)) {
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
	 * Delete ERP TRAINING worker
	 */
	public function deleteERPTRAINGINWorker($idJob, $idErpTrainingWorker)
	{
		if (empty($idJob) || empty($idErpTrainingWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "erp_training_workers",
			"primaryKey" => "id_erp_training_worker",
			"id" => $idErpTrainingWorker
		);

		if ($this->jobs_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/erp_personnel/' . $idJob), 'refresh');
	}

	/**
	 * Save one worker to the ERP TRAINING
	 */
	public function save_one_erp_training_worker()
	{
		$idJob = $this->input->post('hddId');

		if ($this->jobs_model->saveOneWorker()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/erp_personnel/' . $idJob), 'refresh');
	}

	/**
	 * Generate Template Report in PDF
	 * @param int $idToolBox
	 * @since 2/11/2017
	 * @author BMOTTAG
	 */
	public function generaTemplatePDF($idToolBox)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('IHSR report');
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
		$arrParam = array("idToolBox" => $idToolBox);
		$data['info'] = $this->general_model->get_tool_box($arrParam);

		$data['newHazards'] = $this->jobs_model->get_new_hazards($idToolBox); //new hazard list
		$data['toolBoxWorkers'] = $this->jobs_model->get_tool_box_workers($idToolBox); //workers list
		$data['subcontractors'] = $this->jobs_model->get_tool_box_subcontractors_workers($idToolBox); //subcontractor list

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();

		ob_end_clean();
		//Close and output PDF document
		$pdf->Output('IHSR_' . $idToolBox . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Generate ERP Report in PDF
	 * @param int $idERP
	 * @since 20/11/2017
	 * @author BMOTTAG
	 */
	public function generaERPPDF($idERP)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('ERP report');
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

		$arrParam = array("idERP" => $idERP);
		$data['info'] = $this->jobs_model->get_erp($arrParam);

		$data['trainingWorkers'] = $this->jobs_model->get_erp_training_workers($data['info'][0]['fk_id_job']);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		// create some HTML content
		$html = '<p></p><p></p><p></p><p></p>
	<p><h1 align="center" style="color:#337ab7;">EMERGENCY RESPONSE PLAN</h1></p>
	<p></p><p></p><p></p><p></p><p></p>
	<p><h2 align="center" style="color:#337ab7;">Project code:<br>' . $data['info'][0]["job_description"] . '</h2></p>
	<p></p><p></p><p></p><p></p><p></p>
	<p><h2 align="center" style="color:#337ab7;">Facility Address:<br>' . $data['info'][0]["address"] . '</h2></p>
	<p></p><p></p><p></p><p></p><p></p>
	<p><h2 align="center" style="color:#337ab7;">DATE PREPARED:<br>' . $data['info'][0]["date_erp"] . '</h2></p>';

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// add a page
		$pdf->AddPage();

		// create some HTML content
		$html = '<p><h1 align="center" style="color:#337ab7;">EMERGENCY PERSONNEL NAMES AND PHONE NUMBERS</h1></p>
				<style>
				table {
					font-family: arial, sans-serif;
					border-collapse: collapse;
					width: 100%;
				}

				td, th {
					border: 1px solid #dddddd;
					text-align: left;
					padding: 8px;
				}
				</style>
			<table border="0" cellspacing="0" cellpadding="5">
	
				<tr>
					<th bgcolor="#337ab7" style="color:white;" width="30%"><strong>Site supervisor: </strong></th>
					<th width="30%">' . $data['info'][0]['responsible'] . '</th>
					<th bgcolor="#337ab7" style="color:white;" width="20%"><strong>Phone: </strong></th>
					<th width="20%">' . $data['info'][0]['phone_res'] . '</th>
				</tr>
				
				<tr>
					<th bgcolor="#337ab7" style="color:white;"><strong>Emergency coordinator: </strong></th>
					<th>' . $data['info'][0]['coordinator'] . '</th>
					<th bgcolor="#337ab7" style="color:white;"><strong>Phone: </strong></th>
					<th>' . $data['info'][0]['phone_co'] . '</th>
				</tr>
			
			</table>';

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		// add a page
		//			$pdf->AddPage();

		$html = $this->load->view("reporte_evacuation_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_evacuation_procedures_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_evacuation_procedures_fire_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_evacuation_procedures_chemical_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_evacuation_procedures_weather_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');



		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_training_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('erp_' . $idERP . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * FUNCIÓN PARA SUBIR LA IMAGEN 
	 */
	function do_upload()
	{
		$config['upload_path'] = './images/erp/';
		$config['overwrite'] = true;
		$config['allowed_types'] = 'gif|jpg|png|pdf|jpeg';
		$config['max_size'] = '3000';
		$config['max_width'] = '3200';
		$config['max_height'] = '2400';
		$idJob = $this->input->post("hddIdJobMap");
		$config['file_name'] = $idJob;

		$this->load->library('upload', $config);
		//SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
		if (!$this->upload->do_upload()) {
			$error = $this->upload->display_errors();
			$this->erp_map($idJob, $error);
		} else {
			$file_info = $this->upload->data(); //subimos la imagen

			//USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
			//ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
			$this->_create_thumbnail($file_info['file_name']);
			$data = array('upload_data' => $this->upload->data());
			$imagen = $file_info['file_name'];
			$path = "images/erp/" . $imagen;

			//actualizamos el campo photo
			$arrParam = array(
				"table" => "erp",
				"primaryKey" => "fk_id_job",
				"id" => $idJob,
				"column" => "evacuation_map",
				"value" => $path
			);

			$this->load->model("general_model");

			if ($this->general_model->updateRecord($arrParam)) {
				$data['clase'] = "alert-success";
				$data['msj'] = "Se subio la imagen con éxito.";
			} else {
				$data['clase'] = "alert-danger";
				$data['msj'] = "Error, contactarse con el administrador.";
			}

			redirect('jobs/erp_map/' . $idJob);
		}
	}

	//FUNCIÓN PARA CREAR LA MINIATURA A LA MEDIDA QUE LE DIGAMOS
	function _create_thumbnail($filename)
	{
		$config['image_library'] = 'gd2';
		//CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
		$config['source_image'] = 'images/erp/' . $filename;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		//CARPETA EN LA QUE GUARDAMOS LA MINIATURA
		$config['new_image'] = 'images/erp/thumbs/';
		$config['width'] = 150;
		$config['height'] = 150;
		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
	}

	/**
	 * Form Upload Hazards 
	 * @since 27/11/2017
	 * @author BMOTTAG
	 */
	public function hazards($idJob)
	{
		$this->load->model("general_model");
		$data['information'] = FALSE;

		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//hazards list
		$data['hazards'] = $this->general_model->get_job_hazards($idJob);

		$data["view"] = "hazards_list";
		$this->load->view("layout", $data);
	}

	/**
	 * Form Add Hazards
	 * @since 27/11/2017
	 * @author BMOTTAG
	 */
	public function add_hazards($idJob)
	{
		if (empty($idJob)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$data['activityList'] = $this->jobs_model->get_activity_list();

		$data["idJob"] = $idJob;
		$data["view"] = 'form_add_hazards';
		$this->load->view("layout", $data);
	}

	/**
	 * Save hazards
	 * @since 27/11/2017
	 * @author BMOTTAG
	 */
	public function save_safety_hazards()
	{
		header('Content-Type: application/json');
		$data = array();
		$data["idJob"] = $this->input->post('hddId');

		if ($this->jobs_model->add_safety_hazard($data["idJob"])) {
			$data["result"] = true;

			$this->jobs_model->add_hazard_log();

			$this->session->set_flashdata('retornoExito', 'You have added Hazards.');
		} else {
			$data["result"] = "error";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Hazards logs
	 * @since 21/8/2018
	 * @author BMOTTAG
	 */
	public function hazards_logs($idJob)
	{
		$arrParam = array("idJob" => $idJob);
		$data['info'] = $this->jobs_model->get_hazards_logs($arrParam);

		$data["idJob"] = $idJob;
		$data["view"] = 'hazards_logs';
		$this->load->view("layout", $data);
	}

	/**
	 * Delete Job hazard
	 */
	public function deleteJobHazard($idJobHazard, $idJob)
	{
		if (empty($idJobHazard) || empty($idJob)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "job_hazards",
			"primaryKey" => "id_job_hazard",
			"id" => $idJobHazard
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one hazard.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/hazards/' . $idJob), 'refresh');
	}

	/**
	 * Form Upload Locates 
	 * @since 29/11/2017
	 * @author BMOTTAG
	 */
	public function locates($idJob, $error = '')
	{
		$this->load->model("general_model");
		$data['information'] = FALSE;

		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//locates list
		$data['locates'] = $this->jobs_model->get_job_locates($idJob);
		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 			

		$data["view"] = "locates_list";
		$this->load->view("layout", $data);
	}

	/**
	 * FUNCIÓN PARA SUBIR LA IMAGEN 
	 */
	function do_upload_locates()
	{
		$config['upload_path'] = './images/locates/';
		$config['overwrite'] = false;
		$config['allowed_types'] = 'gif|jpg|png|pdf|jpeg';
		$config['max_size'] = '3000';
		$config['max_width'] = '3200';
		$config['max_height'] = '2400';
		$idJob = $this->input->post("hddIdJob");

		$this->load->library('upload', $config);
		//SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
		if (!$this->upload->do_upload()) {
			$error = $this->upload->display_errors();
			$this->locates($idJob, $error);
		} else {
			$file_info = $this->upload->data(); //subimos la imagen

			$data = array('upload_data' => $this->upload->data());
			$imagen = $file_info['file_name'];
			$path = "images/locates/" . $imagen;

			//insertar datos
			if ($this->jobs_model->add_locates($path)) {
				$this->session->set_flashdata('retornoExito', 'You have uploaded the image.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect('jobs/locates/' . $idJob);
		}
	}

	/**
	 * Delete Job locate
	 */
	public function deleteJobLocate($idJobLocate, $idJob)
	{
		if (empty($idJobLocate) || empty($idJob)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "job_locates",
			"primaryKey" => "id_job_locates",
			"id" => $idJobLocate
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted the image.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/locates/' . $idJob), 'refresh');
	}

	/**
	 * SAFETY list
	 * @since 2/1/2018
	 * @author BMOTTAG
	 */
	public function safety($idJob)
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

		//info de safety
		$arrParam = array(
			"limit" => 30,
			"idJob" => $idJob
		);
		$data['information'] = $this->general_model->get_safety($arrParam); //info de safety

		//hazards list
		$data['hazards'] = $this->general_model->get_job_hazards($idJob);

		$data["view"] = 'safety_list';
		$this->load->view("layout", $data);
	}

	/**
	 * tool_box list
	 * @since 24/10/2017
	 * @author BMOTTAG
	 */
	public function jso($idJob)
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
		//jso info
		$arrParam = array('idJob' => $idJob);
		$data['information'] = $this->jobs_model->get_jso($arrParam);
		$data["view"] = 'jso_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form JSO
	 * @since 3/1/2018
	 * @author BMOTTAG
	 */
	public function add_jso($idJob, $idJobJso = 'x')
	{
		$data['information'] = FALSE;
		$data['trainingWorkers'] = FALSE;
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

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		//si envio el id, entonces busco la informacion 
		if ($idJobJso != 'x') {
			//JSO info
			$arrParam = array("idJobJso" => $idJobJso);
			$data['information'] = $this->jobs_model->get_jso($arrParam);

			//workers list
			$data['infoWorkers'] = "";
			if ($data['information']) {
				$arrParam = array("idJobJso" => $idJobJso);
				$data['infoWorkers'] = $this->jobs_model->get_jso_workers($arrParam);
			}

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_jso';
		$this->load->view("layout", $data);
	}

	/**
	 * Save JSO
	 * @since 3/1/2018
	 * @author BMOTTAG
	 */
	public function save_jso()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');

		if ($idJSO = $this->jobs_model->addJSO()) {
			$data["result"] = true;
			$data["mensaje"] = "You have save the JSO.";
			$data["idJSO"] = $idJSO;
			$this->session->set_flashdata('retornoExito', 'You have saved the JSO!!');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idJSO"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Signature
	 * param $typo: supervisor / worker
	 * param $idJSO: llave principal del formulario
	 * param $idWorker: llave principal del trabajador
	 * @since 5/1/2018
	 * @author BMOTTAG
	 */
	public function add_signature_jso($typo, $idJob, $idJSO, $idWorker = 'x')
	{
		if (empty($typo) || empty($idJSO)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of the file
			if ($typo == "supervisor") {
				$name = "images/signature/jso/" . $typo . "_" . $idJSO . ".png";

				$arrParam = array(
					"table" => "job_jso",
					"primaryKey" => "id_job_jso",
					"id" => $idJSO,
					"column" => "supervisor_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = 'jobs/add_jso/' . $idJob . '/' . $idJSO;
			} elseif ($typo == "manager") {
				$name = "images/signature/jso/" . $typo . "_" . $idJSO . ".png";

				$arrParam = array(
					"table" => "job_jso",
					"primaryKey" => "id_job_jso",
					"id" => $idJSO,
					"column" => "manager_signature",
					"value" => $name
				);
				//enlace para regresar al formulario
				$data['linkBack'] = 'jobs/add_jso/' . $idJob . '/' . $idJSO;
			} elseif ($typo == "worker") {
				$name = "images/signature/jso/" . $typo . "_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "job_jso_workers",
					"primaryKey" => "id_job_jso_worker",
					"id" => $idWorker,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores
				$data['linkBack'] = 'jobs/add_jso/' . $idJob . '/' . $idJSO;
			} elseif ($typo == "externalWorker") {
				$name = "images/signature/jso/worker_" . $idWorker . ".png";

				$arrParam = array(
					"table" => "job_jso_workers",
					"primaryKey" => "id_job_jso_worker",
					"id" => $idWorker,
					"column" => "signature",
					"value" => $name
				);
				//enlace para regresar al formulario con ancla a la lista de trabajadores
				$data['linkBack'] = "jobs/jso_worker_view/" . $idWorker;
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
	 * Cargo modal- formulario de captura workers para jso
	 * @since 5/1/2018
	 */
	public function cargarModalWorker()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idJobJso"] = $this->input->post("idJobJso");
		$data["idJobJsoWorker"] = $this->input->post("idJobJsoWorker");

		if ($data["idJobJsoWorker"] != 'x') {
			$arrParam = array("idJobJsoWorker" => $data["idJobJsoWorker"]);
			$data['information'] = $this->jobs_model->get_jso_workers($arrParam);
		
			if ($data['information'] && is_array($data['information']) && isset($data['information'][0])) {
				$data["idJobJso"] = $data['information'][0]['fk_id_job_jso'];
			}
		}		

		$this->load->view("modal_jso_worker", $data);
	}

	/**
	 * Save formularios
	 * @since 5/1/2018
	 * @author BMOTTAG
	 */
	public function saveJSOWorker()
	{
		header('Content-Type: application/json');
		$data = array();

		$idJobJso = $this->input->post('hddidJobJso');
		$idJobWorker = $this->input->post('hddidJobJsoWorker');

		//JSO info
		$arrParam = array("idJobJso" => $idJobJso);
		$infoJSO = $this->jobs_model->get_jso($arrParam);

		$data["idRecord"] = $infoJSO[0]['fk_id_job'];
		$data["idJSO"] = $idJobJso;
		$data["idRecordExternal"] = $idJobWorker;

		$msj = "You have added a new worker.";
		if ($idJobWorker != '') {
			$msj = "You have edited the information.";
		}

		if ($this->jobs_model->saveJSOWorker()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Generate JSO Report in PDF
	 * @param int $idJSO
	 * @since 7/1/2018
	 * @author BMOTTAG
	 */
	public function generaJSOPDF($idJSO)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('JSO report');
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

		$arrParam = array("idJobJso" => $idJSO);

		$data['info'] = $this->jobs_model->get_jso($arrParam);
		$data['workers'] = $this->jobs_model->get_jso_workers($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_jso", $data, true);

		if ($data['workers']) {
			$html .= $this->load->view("reporte_jso_workers", $data, true);
		}

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('jso_' . $data['info'][0]['job_description']  . $idJSO . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Form ERP - PERSONNEL
	 * @since 4/5/2018
	 * @author BMOTTAG
	 */
	public function erp_personnel($idJob, $error = '')
	{
		$data['information'] = FALSE;
		$data['trainingWorkers'] = FALSE;
		$data['deshabilitar'] = '';
		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 

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

		//ERP info
		$arrParam = array(
			"idJob" => $idJob
		);
		$data['information'] = $this->jobs_model->get_erp($arrParam);


		//erp training list
		$data['trainingWorkers'] = $this->jobs_model->get_erp_training_workers($idJob);


		$data["view"] = 'form_erp_personnel';
		$this->load->view("layout", $data);
	}

	/**
	 * Update infor personal
	 * @since 11/4/2021
	 * @author BMOTTAG
	 */
	public function update_erp_personnel()
	{
		$idJob = $this->input->post('hddIdERP');

		if ($this->jobs_model->updateERPWorker()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Worker Information!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		redirect(base_url('jobs/erp_personnel/' . $idJob), 'refresh');
	}

	/**
	 * Form ERP - MAP
	 * @since 4/5/2018
	 * @author BMOTTAG
	 */
	public function erp_map($idJob, $error = '')
	{
		$data['information'] = FALSE;
		$data['deshabilitar'] = '';
		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 

		$this->load->model("general_model");
		//job info
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "id_job",
			"id" => $idJob
		);
		$data['jobInfo'] = $this->general_model->get_basic_search($arrParam);

		//ERP info
		$arrParam = array(
			"idJob" => $idJob
		);
		$data['information'] = $this->jobs_model->get_erp($arrParam);

		$data["view"] = 'form_erp_map';
		$this->load->view("layout", $data);
	}

	/**
	 * Safe one worker to the TOOL BOX
	 */
	public function tool_box_One_Worker()
	{
		$idJob = $this->input->post('hddIdJob');
		$idToolBox = $this->input->post('hddIdToolBox');

		if ($this->jobs_model->toolBoxSaveOneWorker()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/add_tool_box/' . $idJob . "/" . $idToolBox), 'refresh');
	}

	/**
	 * Generate JHA - JOB HAZARDS ANALYSIS Report in PDF
	 * @param int $idJobHazardLog
	 * @since 6/9/2018
	 * @author BMOTTAG
	 */
	public function generaJHAPDF($idJobHazardLog)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('JOB HAZARDS ANALYSIS report');
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

		$arrParam = array("idJobHazardLog" => $idJobHazardLog);
		$data['info'] = $this->jobs_model->get_hazards_logs($arrParam);

		//hazards list
		$data['hazards'] = $this->jobs_model->get_job_hazards_v2($data['info'][0]['id_job']);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_jha_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('jha_' . $idJobHazardLog . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Tool box subcontractor worker
	 */
	public function tool_box_subcontractor_Worker()
	{
		$idJob = $this->input->post('hddIdJob');
		$idToolBox = $this->input->post('hddIdToolBox');

		if ($this->jobs_model->saveSubcontractorWorker()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/add_tool_box/' . $idJob . '/' . $idToolBox), 'refresh');
	}

	/**
	 * Delete tool box subcontractor
	 */
	public function deleteToolBoxSubcontractorWorker($idJob, $idToolBox, $idToolBoxSubcontractor)
	{
		if (empty($idJob) || empty($idToolBox) || empty($idToolBoxSubcontractor)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "tool_box_workers_subcontractor",
			"primaryKey" => "id_tool_box_subcontractor",
			"id" => $idToolBoxSubcontractor
		);

		if ($this->jobs_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/add_tool_box/' . $idJob . '/' . $idToolBox), 'refresh');
	}

	/**
	 * Envio de mensaje
	 * @since 26/11/2020
	 * @author BMOTTAG
	 */
	public function sendSMSworkerJSO($idJSOworker)
	{
		$this->load->library('encrypt');
		require 'vendor/Twilio/autoload.php';

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$this->load->model("general_model");
		$parametric = $this->general_model->get_basic_search($arrParam);
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);

		$client = new Twilio\Rest\Client($dato1, $dato2);

		//Worker info		
		$arrParam = array("idJobJsoWorker" => $idJSOworker);
		$data['information'] = $this->jobs_model->get_jso_workers($arrParam); //info bloques

		//JSO info
		$idJobJso = $data['information'][0]['fk_id_job_jso'];
		$arrParam = array("idJobJso" => $idJobJso);
		$data['JSOInfo'] = $this->jobs_model->get_jso($arrParam);

		$idJob = $data['JSOInfo'][0]['id_job'];

		$mensaje = "";

		$mensaje .= date('F j, Y', strtotime($data['JSOInfo'][0]['date_issue_jso']));
		$mensaje .= "\n" . $data['JSOInfo'][0]['job_description'];
		$mensaje .= "\n";
		$mensaje .= "Click the link below to sign the JOB SITE ORIENTATION.";
		$mensaje .= "\n";
		$mensaje .= "\n";
		$mensaje .= base_url("jobs/jso_worker_view/" . $idJSOworker);

		$to = '+1' . preg_replace('/[^0-9]/', '', $data['information'][0]['works_phone_number']); // sanitize

		try {
			// Attempt to send SMS
			$client->messages->create(
				$to,
				array(
					'from' => '+15876008948', // Make sure this is in E.164 format
					'body' => $mensaje
				)
			);

			$data['clase'] = "alert-info";
			$data['msj'] = "We have sent the SMS to the Worker to sign the JSO.";
		} catch (\Twilio\Exceptions\RestException $e) {
			// Handle error
			log_message('error', 'Twilio error: ' . $e->getMessage());
			$data['clase'] = "alert-danger";
			$data['msj'] = "Failed to send SMS: " . $e->getMessage();
		}

		$data['linkBack'] = "jobs/jso/" . $idJob;
		$data['titulo'] = "<i class='fa fa-list'></i> JSO";

		$data["view"] = 'template/answer';
		$this->load->view("layout", $data);
	}

	/**
	 * JSO workorder view to sign
	 * @since 26/11/2020
	 * @author BMOTTAG
	 */
	public function jso_worker_view($idJSOworker)
	{
		//Worker info		
		$arrParam = array("idJobJsoWorker" => $idJSOworker);
		$data['information'] = $this->jobs_model->get_jso_workers($arrParam); //info bloques

		//JSO info
		$idJobJso = $data['information'][0]['fk_id_job_jso'];
		$arrParam = array("idJobJso" => $idJobJso);
		$data['JSOInfo'] = $this->jobs_model->get_jso($arrParam);

		$data["view"] = 'jso_worker_view';
		$this->load->view("layout", $data);
	}

	/**
	 * Informacion detallada del proyecto
	 * @since 23/12/2020
	 * @author BMOTTAG
	 */
	public function bitacora($idJob)
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
			"jobId" => $idJob
		);

		//informacion Work Order
		$data['workOrderInfo'] = $this->general_model->get_workorder_info($arrParam);

		//bitacora
		$data['bitacora'] = $this->jobs_model->get_bitacora_job($idJob);

		$data["view"] = "job_bitacora";
		$this->load->view("layout", $data);
	}

	public function loadModalBitacora()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data["idBitacora"] = $this->input->post("idBitacora");

		$this->load->view("bitacora_modal", $data);
	}

	public function save_bitacora()
	{
		header('Content-Type: application/json');
		$data = array();

		$msj = "You have added a new note!!";

		if ($this->jobs_model->saveBitacora() == true) {
			$data["result"] = true;
			$data["idJob"] = $this->input->post('hddId');;

			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["idJob"] = "";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Excavation and Trenching Plan list
	 * @since 1/08/2021
	 * @author BMOTTAG
	 */
	public function excavation($idJob)
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

		//Excavation and trenching info
		$arrParam = array("idJob" => $idJob);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data["view"] = 'excavation_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form Excavation and Trenching Plan
	 * @since 1/08/2021
	 * @author BMOTTAG
	 */
	public function add_excavation($idJob, $idExcavation = 'x')
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

		//tool box info
		$arrParam = array(
			"idJob" => $idJob
		);
		$data['confinedList'] = $this->general_model->get_confined_space($arrParam);

		//si envio el id, entonces busco la informacion 
		if ($idExcavation != 'x') {

			$arrParam = array("idExcavation" => $idExcavation);
			$data['information'] = $this->general_model->get_excavation($arrParam);

			if (!$data['information']) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		$data["view"] = 'form_excavation';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Excavation and Trenching Plan
	 * @since 1/08/2021
	 * @author BMOTTAG
	 */
	public function save_excavation()
	{
		header('Content-Type: application/json');
		$data = array();

		if ($idExcavation = $this->jobs_model->addExcavation()) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";
			$data["idExcavation"] = $idExcavation;
			$this->session->set_flashdata('retornoExito', 'You have saved your Excavation and Trenching Plan, do not forget to add Workers and signatures.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
			$data["idExcavation"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Upload Personnel to Excavation Plan
	 * @since 2/8/2021
	 * @author BMOTTAG
	 */
	public function upload_excavation_personnel($idExcavation)
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$this->load->model("general_model");
		$data['information'] = FALSE;

		$arrParam = array("state" => 1, "idUserMANAGERS" => true);
		$data['adminList'] = $this->general_model->get_user($arrParam); //workers list

		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data['excavationWorkers'] = $this->general_model->get_excavation_workers($arrParam); //excavation_worker list
		$data['excavationSubcontractors'] = $this->general_model->get_excavation_subcontractors($arrParam); //excavation subcontractors  list

		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => 2
		);
		$data['companyList'] = $this->general_model->get_basic_search($arrParam); //company list

		$data['view'] = 'form_excavation_personnel';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Excavation and Trenching Plan - Personnel
	 * @since 14/08/2021
	 * @author BMOTTAG
	 */
	public function save_personnel()
	{
		header('Content-Type: application/json');
		$data = array();
		$idExcavation = $this->input->post('hddIdentificador');

		if ($this->jobs_model->updatePersonnel()) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";
			$data["idExcavation"] = $idExcavation;
			$this->session->set_flashdata('retornoExito', 'You have updated the information of  your Excavation and Trenching Plan.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
			$data["idExcavation"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Excavation and Trenching Plan - Protection Methods
	 * @since 3/08/2021
	 * @author BMOTTAG
	 */
	public function upload_protection_methods($idExcavation, $error = '')
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");
		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);
		$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 	

		$data["view"] = 'form_excavation_protection_methods';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Excavation and Trenching Plan - Protection Methods
	 * @since 8/08/2021
	 * @author BMOTTAG
	 */
	function save_protection_methods()
	{
		$data = array();
		$idExcavation = $this->input->post('hddIdentificador');

		$config['upload_path'] = './files/excavation/';
		$config['overwrite'] = TRUE;
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = '3000';
		$config['max_width'] = '2024';
		$config['max_height'] = '2008';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload() && $_FILES['userfile']['name'] != "") {
			//SI EL ARCHIVO FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong>' . $error);
			$this->upload_protection_methods($idExcavation, $error);
		} else {
			if ($_FILES['userfile']['name'] == "") {
				$archivo = 'xxx';
			} else {
				$file_info = $this->upload->data(); //subimos ARCHIVO				
				$data = array('upload_data' => $this->upload->data());
				$archivo = $file_info['file_name'];
			}
			//insertar datos
			if ($this->jobs_model->updateExcavation($archivo)) {
				$this->session->set_flashdata('retornoExito', 'You have updated the information of  your Excavation and Trenching Plan.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect('jobs/upload_protection_methods/' . $idExcavation);
		}
	}

	/**
	 * Form Excavation and Trenching Plan - Access & Egress
	 * @since 3/08/2021
	 * @author BMOTTAG
	 */
	public function upload_access_egress($idExcavation)
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");
		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data["view"] = 'form_excavation_access_egress';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Excavation and Trenching Plan - Access & Egress
	 * @since 8/08/2021
	 * @author BMOTTAG
	 */
	public function save_access_egress()
	{
		header('Content-Type: application/json');
		$data = array();
		$idExcavation = $this->input->post('hddIdentificador');

		if ($this->jobs_model->updateExcavationAccess()) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";
			$data["idExcavation"] = $idExcavation;
			$this->session->set_flashdata('retornoExito', 'You have updated the information of  your Excavation and Trenching Plan.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
			$data["idExcavation"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Excavation and Trenching Plan - Affected Zone, Traffic & Utilities
	 * @since 3/08/2021
	 * @author BMOTTAG
	 */
	public function upload_affected_zone($idExcavation, $error = '')
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");
		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data["view"] = 'form_excavation_affected_zone';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Excavation and Trenching Plan - Affected Zone, Traffic & Utilities
	 * @since 8/08/2021
	 * @author BMOTTAG
	 */
	public function save_affected_zone()
	{
		$data = array();
		$idExcavation = $this->input->post('hddIdentificador');

		$config['upload_path'] = './files/excavation/';
		$config['overwrite'] = TRUE;
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = '3000';
		$config['max_width'] = '2024';
		$config['max_height'] = '2008';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload() && $_FILES['userfile']['name'] != "") {
			//SI EL ARCHIVO FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong>' . $error);
			$this->upload_affected_zone($idExcavation, $error);
		} else {
			if ($_FILES['userfile']['name'] == "") {
				$archivo = 'xxx';
			} else {
				$file_info = $this->upload->data(); //subimos ARCHIVO				
				$data = array('upload_data' => $this->upload->data());
				$archivo = $file_info['file_name'];
			}
			//insertar datos
			if ($this->jobs_model->updateExcavationAffectedZone($archivo)) {
				$this->session->set_flashdata('retornoExito', 'You have updated the information of  your Excavation and Trenching Plan.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect('jobs/upload_affected_zone/' . $idExcavation);
		}
	}

	/**
	 * Form Excavation and Trenching Plan - De-Watering
	 * @since 3/08/2021
	 * @author BMOTTAG
	 */
	public function upload_de_watering($idExcavation)
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");
		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data["view"] = 'form_excavation_de_watering';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Excavation and Trenching Plan - De-Watering
	 * @since 8/08/2021
	 * @author BMOTTAG
	 */
	public function save_de_watering()
	{
		header('Content-Type: application/json');
		$data = array();
		$idExcavation = $this->input->post('hddIdentificador');

		if ($this->jobs_model->updateExcavationDeWatering()) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";
			$data["idExcavation"] = $idExcavation;
			$this->session->set_flashdata('retornoExito', 'You have updated the information of  your Excavation and Trenching Plan.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
			$data["idExcavation"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Add Workers - Excavation
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function add_workers_excavation($idExcavation)
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		//workers list
		$this->load->model("general_model");
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$data["idExcavation"] = $idExcavation;

		$data["view"] = 'form_add_workers_excavation';
		$this->load->view("layout", $data);
	}

	/**
	 * Save worker - Excavation and Trenching Plan
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function save_excavation_workers()
	{
		header('Content-Type: application/json');
		$data = array();
		$idExcavation = $this->input->post('hddIdExcavation');

		$data["idRecord"] = $idExcavation;

		if ($this->jobs_model->add_excavation_worker($idExcavation)) {
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
	 * Delete worker - Excavation and Trenching Plan
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function deleteExcavationWorker($idExcavation, $idWorker)
	{
		if (empty($idExcavation) || empty($idWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "job_excavation_workers",
			"primaryKey" => "id_excavation_worker",
			"id" => $idWorker
		);
		if ($this->jobs_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/upload_excavation_personnel/' . $idExcavation), 'refresh');
	}

	/**
	 * Save one worker to Excavation and Trenching Plan
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function excavation_One_Worker()
	{
		$idExcavation = $this->input->post('hddIdExcavation');

		if ($this->jobs_model->excavationSaveOneWorker()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/upload_excavation_personnel/' . $idExcavation), 'refresh');
	}

	/**
	 * Excavation - Subcontractor worker
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function excavation_subcontractor_Worker()
	{
		$idExcavation = $this->input->post('hddIdExcavation');

		if ($this->jobs_model->saveSubcontractorWorkerExcavation()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/upload_excavation_personnel/' . $idExcavation), 'refresh');
	}

	/**
	 * Delete subcontractor - Excavation and Trenching Plan
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function deleteExcavationSubcontractorWorker($idExcavation, $idSubcontractor)
	{
		if (empty($idExcavation) || empty($idSubcontractor)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "job_excavation_subcontractor",
			"primaryKey" => "id_excavation_subcontractor",
			"id" => $idSubcontractor
		);
		if ($this->jobs_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		redirect(base_url('jobs/upload_excavation_personnel/' . $idExcavation), 'refresh');
	}

	/**
	 * Subcontractors view to sign
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function review_excavation($idExcavation)
	{
		$this->load->model("general_model");
		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data['excavationWorkers'] = $this->general_model->get_excavation_workers($arrParam); //excavation_worker list
		$data['excavationSubcontractors'] = $this->general_model->get_excavation_subcontractors($arrParam); //excavation 

		$data["view"] = 'review_excavation';
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Signature
	 * param $typo: supervisor / worker
	 * param $idExcavation: llave principal del formulario
	 * param $idWorker: llave principal del trabajador
	 * @since 14/8/2021
	 * @author BMOTTAG
	 */
	public function add_signature_excavation($typo, $idExcavation, $idWorker = 'x')
	{
		if (empty($typo) || empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of the file
			if ($typo == "sketch") {
				$data['linkBack'] = 'jobs/upload_sketch/' . $idExcavation;
				$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>EXCAVATION / TRENCH SKETCH";
				$msj = "Good job, you have saved the excavation/trench sketch.";

				$name = "images/signature/etp/" . $typo . "_" . $idExcavation . ".png";

				$arrParam = array(
					"table" => "job_excavation",
					"primaryKey" => "id_job_excavation",
					"id" => $idExcavation,
					"column" => "excavation_sketch",
					"value" => $name
				);
			} else {
				$data['linkBack'] = 'jobs/review_excavation/' . $idExcavation;
				$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
				$msj = "Good job, you have saved your signature.";
				if ($typo == "supervisor" || $typo == "operator" || $typo == "manager") {
					$name = "images/signature/etp/" . $typo . "_" . $idExcavation . ".png";

					$arrParam = array(
						"table" => "job_excavation",
						"primaryKey" => "id_job_excavation",
						"id" => $idExcavation,
						"column" => $typo . "_signature",
						"value" => $name
					);
				} elseif ($typo == "worker") {
					$name = "images/signature/etp/" . $typo . "_" . $idWorker . ".png";

					$arrParam = array(
						"table" => "job_excavation_workers",
						"primaryKey" => "id_excavation_worker",
						"id" => $idWorker,
						"column" => "signature",
						"value" => $name
					);
				} elseif ($typo == "subcontractor") {
					$name = "images/signature/etp/" . $typo . "_" . $idWorker . ".png";

					$arrParam = array(
						"table" => "job_excavation_subcontractor",
						"primaryKey" => "id_excavation_subcontractor",
						"id" => $idWorker,
						"column" => "signature",
						"value" => $name
					);
				}
			}

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			if ($this->general_model->updateRecord($arrParam)) {
				//$this->session->set_flashdata('retornoExito', 'You just save your signature!!!');

				$data['clase'] = "alert-success";
				$data['msj'] = $msj;
			} else {
				//$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');

				$data['clase'] = "alert-danger";
				$data['msj'] = "Ask for help.";
			}

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Form Excavation and Trenching Plan - Sketch
	 * @since 20/01/2022
	 * @author BMOTTAG
	 */
	public function upload_sketch($idExcavation)
	{
		if (empty($idExcavation)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");
		$arrParam = array("idExcavation" => $idExcavation);
		$data['information'] = $this->general_model->get_excavation($arrParam);

		$data["view"] = 'form_excavation_sketch';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Trenching Plan - Sketch
	 * @since 12/03/2022
	 * @author BMOTTAG
	 */
	public function save_upload_sketch()
	{
		$data = array();
		$idExcavation = $this->input->post('hddIdentificador');

		$config['upload_path'] = './files/excavation/';
		$config['overwrite'] = TRUE;
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = '3000';
		$config['max_width'] = '2024';
		$config['max_height'] = '2008';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload()) {
			//SI EL ARCHIVO FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong>' . $error);
			$this->upload_sketch($idExcavation);
		} else {

			$file_info = $this->upload->data(); //subimos ARCHIVO				
			$data = array('upload_data' => $this->upload->data());
			$archivo = $file_info['file_name'];

			$arrParam = array(
				"table" => "job_excavation",
				"primaryKey" => "id_job_excavation",
				"id" => $idExcavation,
				"column" => "excavation_sketch_doc",
				"value" => $archivo
			);
			//insertar datos
			$this->load->model("general_model");
			if ($this->general_model->updateRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'You have updated the information of  your Excavation and Trenching Plan.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect('jobs/upload_sketch/' . $idExcavation);
		}
	}

	/**
	 * Generate Report in PDF - Excavation and Trenching Plan
	 * @param int $idExcavation
	 * @since 15/08/2021
	 * @author BMOTTAG
	 */
	public function generaExcavationPDF($idExcavation)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Excavation and Trenching Plan report');
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
		$arrParam = array("idExcavation" => $idExcavation);
		$data['info'] = $this->general_model->get_excavation($arrParam);

		$data['excavationWorkers'] = $this->general_model->get_excavation_workers($arrParam); //excavation_worker list
		$data['excavationSubcontractors'] = $this->general_model->get_excavation_subcontractors($arrParam); //excavation 

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_excavation", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');


		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();
		$date = $data['info'][0]['date_excavation'];

		ob_end_clean();
		//Close and output PDF document
		$pdf->Output('Excavation_Trenching_Plan_' . $date . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Upload Job detail
	 * @since 30/12/2022
	 */
	public function upload_job_detail($idJob, $error = "", $success = "")
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

		$data["error"] = $error;
		$data["success"] = $success;
		$data["view"] = "upload_file";
		$this->load->view("layout", $data);
	}

	/**
	 *Cargue de archivo
	 * @since 20/06/2022
	 */
	public function do_upload_job_info()
	{
		$config['upload_path'] = './tmp/';
		$config['overwrite'] = true;
		$config['allowed_types'] = 'csv';
		$config['max_size'] = '5000';
		$config['file_name'] = 'job_detail.csv';
		$idJob = $this->input->post("hddIdJob");

		$this->load->library('upload', $config);
		$bandera = false;
		if (!$this->upload->do_upload()) {
			$error = $this->upload->display_errors();
			$msgError = html_escape(substr($error, 3, -4));
			$this->job_detail($idJob, $msgError);
		} else {
			
			$flagExpenses = $this->input->post("hddFlagExpenses");
			$flagUploadDetails = $this->input->post("hddFlagUploadDetails");
			$this->load->model("general_model");

			if($flagUploadDetails == 1){
				//DELETE PREVIOS INFORMATION
				$arrParam = array(
					"table" => "job_details",
					"primaryKey" => "fk_id_job ",
					"id" => $idJob 
				);
				$this->general_model->deleteRecord($arrParam);
			}

			if($flagExpenses == 1){
				//si tienes expenses eliminarlos
				$arrParam = array(
					"idJob" => $idJob 
				);
				$this->jobs_model->deleteWOExpenses($arrParam);

				//update WO expenses flag in table param_job
				$arrParam = array(
					"table" => "param_jobs",
					"primaryKey" => "id_job",
					"id" => $idJob,
					"column" => "flag_expenses",
					"value" => 0
				);
				$this->general_model->updateRecord($arrParam);

				//update WO expenses flag in table workorders

				/*
				$arrParam = array(
					"idJob" => $idJob 
				);
				$this->jobs_model->deleteWOExpenses($arrParam);







				$arrParam = array(
					"table" => "workorder",
					"primaryKey" => "id_workorder",
					"id" => $idWorkOrder,
					"column" => "expenses_flag",
					"value" => 1
				);
				$this->load->model("general_model");
				$this->general_model->updateRecord($arrParam);
*/











			}

			$file_info = $this->upload->data();
			$data = array('upload_data' => $this->upload->data());

			$archivo = $file_info['file_name'];

			$registros = array();
			if (($fichero = fopen(FCPATH . 'tmp/' . $archivo, "a+")) !== FALSE) {
				// Lee los nombres de los campos
				$nombres_campos = fgetcsv($fichero, 0, ";");
				$num_campos = count($nombres_campos);
				// Lee los registros

				$idUser = $this->session->userdata("id");
				while (($datos = fgetcsv($fichero, 0, ";")) !== FALSE) {
					// Crea un array asociativo con los nombres y valores de los campos
					for ($icampo = 0; $icampo < $num_campos; $icampo++) {
						if ($icampo == 0) {
							//adicionar el id del usuario
							$registro["fk_id_user"] = $idUser;
							$registro["fk_id_job"] = $idJob;
						}
						$registro[$nombres_campos[$icampo]] = utf8_encode($datos[$icampo]);
					}
					// Multiplicar columnas y guardar el resultado
					$quantity = floatval($registro['quantity']);
					$unitPrice = floatval($registro['unit_price']);
					$registro['extended_amount'] = $quantity * $unitPrice;
					// Añade el registro leido al array de registros
					$registros[] = $registro;
				}
				fclose($fichero);

				$x = 0;
				$errores = array();
				$bandera = false;
				foreach ($registros as $lista) {
					$x++;
					if ($this->jobs_model->upload_file_detail($lista)) {
					} else {
						$errores["numero_registro"] = $x;
						$bandera = true;
					}
				}
			}

		//update upload field flag
		$arrParam = array(
			"table" => "param_jobs",
			"primaryKey" => "id_job",
			"id" => $idJob,
			"column" => "flag_upload_details",
			"value" => 1
		);
		$this->general_model->updateRecord($arrParam);

		$success = 'The file was uploaded successfully.';

		if ($bandera) {
			$registros = implode(",", $errores["numero_registro"]);
			$success = 'El archivo se cargó pero hay errores en los siguientes registros:::' . $registros;
		}
		$this->update_job_detail($idJob);
		
		redirect(base_url('jobs/job_detail/' . $idJob), 'refresh');
		}


	}

	/**
	 * Job Detail List
	 * @since 6/1/2023
	 * @author BMOTTAG
	 */
	public function job_detail($idJob, $error = "", $success = "")
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

		$arrParam = array("idJob" => $idJob);
		$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);

		$data["error"] = $error;
		$data["view"] = 'job';
		
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Job Detail List
	 * @since 12/05/2025
	 * @author BMOTTAG
	 */
	public function charged_lic($idJob, $status)
	{
		$this->load->model("general_model");
		$arrParam = array("idJob" => $idJob);
		$data['jobInfo'] = $this->general_model->get_job($arrParam);

		$arrParam = array("idJob" => $idJob);
		$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);
		$data["status"] = $status;
		$data["view"] = 'job_lic_charged';
		
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Cargo modal - Job Detail
	 * @since 6/1/2023
	 */
	public function cargarModalJobDetail()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data['claimPercentage'] = FALSE;
		$identification = $this->input->post("identification");
		$data["idJob"] = "";
		if($identification != ""){
			$porciones = explode("-", $identification);
			$data["idJob"] = $porciones[0];
			$data["chapterNumber"] = $porciones[1];
			$data["chapterName"] = $porciones[2];
		}

		$data["idJobDetail"] = $this->input->post("idJobDetail");

		if ($data["idJobDetail"] != 'x') {
			$arrParam = array(
				"idJobDetail" => $data["idJobDetail"]
			);
			$this->load->model("general_model");
			$data['information'] = $this->general_model->get_job_detail($arrParam);

			//calcuate %
			$claimCost = $this->general_model->get_total_cost_by_job_detail($arrParam);
			$totalAmount = $data['information'][0]['extended_amount'];

			if ($totalAmount > 0) {
				$data['claimPercentage'] = $percentage = round(($claimCost / $totalAmount) * 100, 2);
			} else {
				$data['claimPercentage'] = 0;
			}
			$data["idJob"] = $data['information'][0]['fk_id_job'];
		}

		$this->load->view("job_detail_modal", $data);
	}

	/**
	 * Update Job Detail
	 * @since 6/1/2023
	 * @author BMOTTAG
	 */
	public function save_job_detail()
	{
		header('Content-Type: application/json');
		$data = array();

		$idJobDetail = $this->input->post('hddId');
		$data["idRecord"] = $this->input->post('hddIdJob');

		$msj = "You have added a new Register!!";
		if ($idJobDetail != '') {
			$msj = "You have updated a Register!!";
		}

		if ($idJobDetail = $this->jobs_model->saveJobDetail()) {
			$this->update_job_detail($data["idRecord"]);
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Delete Job Detail
	 * @since 20/9/2024
	 * @author BMOTTAG
	 */
	public function deleteRecordJobDetail()
	{
		header('Content-Type: application/json');

		$identificador = $this->input->post("identificador");
		//Recover jobId and jobDetailsId
		$porciones = explode("-", $identificador);

		$data["idRecord"] = $porciones[0];
		$idJobDetail = $porciones[1];

		//eliminaos registros
		$arrParam = array(
			"table" => "job_details",
			"primaryKey" => "id_job_detail",
			"id" => $idJobDetail
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->update_job_detail($data["idRecord"]);
			$data["result"] = true;
			$data["mensaje"] = "You have deleted one record.";
			$this->session->set_flashdata('retornoExito', 'You have deleted one record');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update Job Deteails Percentage
	 * @since 11/1/2023
	 * @author BMOTTAG
	 */
	public function update_job_detail($idJob)
	{
		$this->load->model("general_model");

		$arrParam = array("idJob" => $idJob);
		$jobDetails = $this->general_model->get_job_detail($arrParam);

		$totalExtendedAmount = $this->jobs_model->sumExtendedAmount($arrParam);

		$this->jobs_model->updateJobDetail($jobDetails, $totalExtendedAmount);
	}

	/**
	 * Delete All Deteails Info
	 * @since 31/1/2024
	 * @author BMOTTAG
	 */
	public function delete_job_detail_info()
	{
		$idJob = $this->input->post('hddIdJob');
		$this->load->model("general_model");
		$result = true;
		//DELETE PREVIOS INFORMATION
		$arrParam = array(
			"table" => "job_details",
			"primaryKey" => "fk_id_job ",
			"id" => $idJob 
		);
		if(!$this->general_model->deleteRecord($arrParam)){
			$result = false;
		}

		//DELETE expenses
		$arrParam = array(
			"idJob" => $idJob 
		);
		if(!$this->jobs_model->deleteWOExpenses($arrParam)){
			$result = false;
		}

		//update FLAGS in table param_job
		if(!$this->jobs_model->resetFlagsParamJob($arrParam)){
			$result = false;
		}	

		//update WO expenses flag in table workorders
		$arrParam = array(
			"table" => "workorder",
			"primaryKey" => "fk_id_job",
			"id" => $idJob,
			"column" => "expenses_flag",
			"value" => 2
		);
		if(!$this->general_model->updateRecord($arrParam)){
			$result = false;
		}

		//update submodule flag for that job
		$arrParam = array(
			"idJob" => $idJob,
			"table" => "workorder_personal"
		);
		if(!$this->jobs_model->updateWOSubmoduleFlag($arrParam)){
			$result = false;
		}
		$arrParam['table'] = "workorder_materials";
		if(!$this->jobs_model->updateWOSubmoduleFlag($arrParam)){
			$result = false;
		}
		$arrParam['table'] = "workorder_equipment";
		if(!$this->jobs_model->updateWOSubmoduleFlag($arrParam)){
			$result = false;
		}
		$arrParam['table'] = "workorder_ocasional";
		if(!$this->jobs_model->updateWOSubmoduleFlag($arrParam)){
			$result = false;
		}
		$arrParam['table'] = "workorder_receipt";
		if(!$this->jobs_model->updateWOSubmoduleFlag($arrParam)){
			$result = false;
		}

		if ($result) {
			$this->session->set_flashdata('retornoExito', "All information has been reset.");
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Error occurred. Please contact support');
		}

		redirect(base_url('jobs/job_detail/' . $idJob), 'refresh');
	}

	/**
	 * Fire watch list
	 * @since 27/1/2023
	 * @author BMOTTAG
	 */
	public function fire_watch($idJob)
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

		//fire watch info
		$arrParam = array("idJob" => $idJob);
		$data['infoFireWatchSetup'] = $this->jobs_model->get_fire_watch_setup($arrParam);

		//fire watch info			
		$data['information'] = $this->jobs_model->get_fire_watch($arrParam);

		$data["view"] = 'fire_watch_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - formulario fire watch setup
	 * @since 27/1/2023
	 */
	public function cargarModalFireWatchSetup()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idJob"] = $this->input->post("idJob");
		$data["metodo"] = $this->input->post("metodo");

		//worker list
		$arrParam = array("state" => 1, "idRolesSupervisors" => true);
		$this->load->model("general_model");
		$data['workersList'] = $this->general_model->get_user($arrParam); //worker list

		//fire watch setup info
		$arrParam = array(
			"idJob" => $data["idJob"]
		);
		$data['information'] = $this->jobs_model->get_fire_watch_setup($arrParam);

		$this->load->view("fire_watch_setup_modal", $data);
	}

	/**
	 * Cargo modal - formulario fire watch
	 * @since 27/1/2023
	 */
	public function cargarModalFireWatch()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idFireWatch"] = $this->input->post("idFireWatch");
		$data["idJob"] = $this->input->post("idJob");

		//worker list
		$arrParam = array("state" => 1);
		$this->load->model("general_model");
		$data['workersList'] = $this->general_model->get_user($arrParam); //worker list

		//fire watch setup info
		$arrParam = array(
			"idJob" => $data["idJob"]
		);
		$data['information'] = $this->jobs_model->get_fire_watch_setup($arrParam);

		$this->load->view("fire_watch_modal", $data);
	}

	/**
	 * Save fire watch
	 * @since 27/1/2023
	 * @author BMOTTAG
	 */
	public function save_fire_watch_setup()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');
		$msj = "You have saved the information!!";

		if ($this->jobs_model->saveFireWatchSetup()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Save fire watch
	 * @since 27/1/2023
	 * @author BMOTTAG
	 */
	public function save_fire_watch()
	{
		header('Content-Type: application/json');
		$data = array();

		$data["idRecord"] = $this->input->post('hddIdJob');
		$idFireWatch = $this->input->post('hddIdFireWatch');

		$msj = "You have added a new Fire Watch!!";
		if ($idFireWatch != '') {
			$msj = "You have updated a Fire Watch!!";
		}

		if ($idFireWatch = $this->jobs_model->saveFireWatch()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form Checkin for Fire Watch
	 * @since 3/2/2023
	 * @author BMOTTAG
	 */
	public function fire_watch_checkin($idFireWatch, $idCheckin = 'x')
	{
		$data['idCheckin'] = FALSE;

		//fire watch info
		$arrParam = array(
			"idFireWatch" => $idFireWatch
		);
		$data['information'] = $this->jobs_model->get_fire_watch($arrParam);

		$arrParam = array(
			"today" => date('Y-m-d'),
			"idFireWatch" => $idFireWatch
		);
		$data['checkinList'] = $this->jobs_model->get_fire_watch_checkin($arrParam);

		if ($idCheckin != 'x') {
			$data['idCheckin'] = $idCheckin;
			$arrParam = array("idCheckin" => $idCheckin);
			$data['checkinList'] = $this->jobs_model->get_fire_watch_checkin($arrParam);
		}
		$data["view"] = "form_fire_watch_checkin";
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Save Fire Watch Checkin
	 * @since 3/2/2023
	 * @author BMOTTAG
	 */
	public function save_fire_watch_checkin()
	{
		header('Content-Type: application/json');
		$data = array();

		$idFireWatch =  $this->input->post('idFireWatch');
		$msj = "Information saved successfully!";

		if ($this->jobs_model->saveFireWatchCheckin()) {
			$data["result"] = true;
			$data["idFireWatch"] = $idFireWatch;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Cargo modal - formulario checkout
	 * @since 3/02/2023
	 */
	public function cargarModalFireWatchCheckout()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idCheckin"] = $this->input->post("idCheckin");

		if ($data["idCheckin"] != 'x') {
			$arrParam = array(
				"idCheckin" => $data["idCheckin"]
			);
			$data['information'] = $this->jobs_model->get_fire_watch_checkin($arrParam);
		}
		$this->load->view("fire_watch_checkout_modal", $data);
	}

	/**
	 * Update checkin - checkout FIRE WATCH
	 * @since 3/02/2023
	 * @author BMOTTAG
	 */
	public function save_fire_watch_checkout()
	{
		header('Content-Type: application/json');
		$data = array();

		$idFireWatch = $this->input->post('idFireWatch');
		$data["idFireWatch"] = $idFireWatch;
		$msj = "Information saved successfully!";

		if ($this->jobs_model->saveFireWatchCheckout()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Generate Fire Watch Record Report in PDF
	 * @param int $idFireWatch
	 * @since 26/07/2023
	 * @author BMOTTAG
	 */
	public function generaFIREWATCHPDF($idFireWatch)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Fire Watch report');
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

		//fire watch info
		$arrParam = array(
			"idFireWatch" => $idFireWatch,
			"distinctUser" => true
		);
		$data['info'] = $this->jobs_model->get_fire_watch($arrParam);
		$data['checkinList'] = $this->jobs_model->get_fire_watch_checkin($arrParam);



		$arrParam = array(
			"idFireWatch" => $idFireWatch
		);
		$data['info'] = $this->jobs_model->get_fire_watch($arrParam);
		$data['checkinList_log'] = $this->jobs_model->get_fire_watch_checkin($arrParam);


		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_fire_watch_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// add a page
		$pdf->AddPage();
		$html = $this->load->view("reporte_fire_watch_log_pdf", $data, true);
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('fire_watch_report' . $data['info'][0]['job_description'] . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	/**
	 * Generate Fire Watch Log Sheet Report in PDF
	 * @param int $idFireWatch
	 * @since 27/03/2023
	 * @author BMOTTAG
	 */
	public function generaFIREWATCHLOGPDF($idFireWatch)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('Fire Watch Log Sheet');
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

		//fire watch info
		$arrParam = array(
			"idFireWatch" => $idFireWatch
		);
		$data['info'] = $this->jobs_model->get_fire_watch($arrParam);
		$data['checkinList'] = $this->jobs_model->get_fire_watch_checkin($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		$pdf->AddPage();

		$html = $this->load->view("reporte_fire_watch_log_pdf", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('fire_watch_log_sheet' . $data['info'][0]['job_description'] . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	/**
	 * Job LIC list
	 * @since 11/01/2025
	 * @author BMOTTAG
	 */
	public function job_lic_list()
	{
		$this->load->model("general_model");
		//job´s list - (active´s items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['info'] = $this->general_model->get_basic_search($arrParam);

		$data['dashboardURL'] = $this->session->userdata("dashboardURL");

		$data["view"] = 'jobs_engineering_list';
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Job Details View
	 * @since 16/05/2025
	 * @author BMOTTAG
	 */
	public function load_claims_view()
	{
		$this->load->model("general_model");
		//job´s list - (active´s items)
		$arrParam = array(
			"idJobDetail" => $this->input->post('idJobDetail')
		);
		$data['claims'] = $this->general_model->get_claims_by_id_job_detail($arrParam);

		$this->load->view('claims_view', $data);
	}
}
