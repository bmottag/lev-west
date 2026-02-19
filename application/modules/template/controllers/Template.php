<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("template_model");
		$this->load->helper('form');
    }
	
	/**
	 * Generar plantillas
     * @since 14/6/2017
     * @author BMOTTAG
	 */
	public function templates()
	{
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "templates",
				"order" => "template_name",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'templates';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario template
     * @since 14/6/2017
     */
    public function cargarModalTemplate() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idTemplate"] = $this->input->post("idTemplate");	
			
			if ($data["idTemplate"] != 'x') {
				$this->load->model("general_model");
				$arrParam = array(
					"table" => "templates",
					"order" => "id_template",
					"column" => "id_template",
					"id" => $data["idTemplate"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("templates_modal", $data);
    }
	
	/**
	 * Update Template
     * @since 14/6/2017
     * @author BMOTTAG
	 */
	public function save_template()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idTemplate = $this->input->post('hddId');
			
			$msj = "You have added a new Template!!";
			if ($idTemplate != '') {
				$msj = "You have updated a Template!!";
			}

			if ($idTemplate = $this->template_model->saveTemplate()) {
				$data["result"] = true;
				$data["idRecord"] = $idTemplate;
				
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$data["idRecord"] = "";
				
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	/**
	 * Form Upload Workers
     * @since 14/6/2017
     * @author BMOTTAG
	 */
	public function use_template($id = 'x')
	{
			$this->load->model("general_model");
			
			//template
			$arrParam = array(
				"table" => "templates",
				"order" => "id_template",
				"column" => "id_template",
				"id" => $id
			);
			$data['template'] = $this->general_model->get_basic_search($arrParam);
						
					
			//si envio el id, entonces busco la informacion 
			if ($id != 'x') {
								
				//workers list
				$this->load->model("general_model");
				$arrParam = array("state" => 1);
				$data['workersList'] = $this->general_model->get_user($arrParam);//workers list

				
				//safety_workes list
				$data['templateWorkers'] = $this->template_model->get_templates_workers($id);

			}			

			$data["view"] = 'form_use_template';
			$this->load->view("layout", $data);
	}

	/**
	 * Form Add Workers
     * @since 14/6/2017
     * @author BMOTTAG
	 */
	public function add_workers_template($id)
	{
			if (empty($id)) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
			
			//workers list
			$this->load->model("general_model");
			$arrParam = array("state" => 1);
			$data['workersList'] = $this->general_model->get_user($arrParam);//workers list

			
			$view = 'form_add_workers';
			$data["idTemplate"] = $id;
			$data["view"] = $view;
			$this->load->view("layout", $data);
	}
	
	/**
	 * Save worker
     * @since 14/6/2017
     * @author BMOTTAG
	 */
	public function save_temlate_workers()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idTemplate = $this->input->post('hddId');

			if ($this->template_model->add_template_worker($idTemplate)) {
				$data["result"] = true;
				$data["mensaje"] = "Solicitud guardada correctamente.";
				$data["idTemplate"] = $idTemplate;
				
				$this->session->set_flashdata('retornoExito', 'You have added the Workers, remember to get the signature of each one.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";
				$data["idTemplate"] = "";
				
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }

	/**
	 * Signature
     * @since 14/6/2017
     * @author BMOTTAG
	 */
	public function add_signature($typo, $idSafety, $idWorker)
	{
			if (empty($typo) || empty($idSafety) || empty($idWorker) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			if($_POST){
				
				//update signature with the name of de file
				if($typo == "advisor"){
					$name = "images/signature/safety/" . $typo . "_" . $idSafety . ".png";
					
					$arrParam = array(
						"table" => "safety",
						"primaryKey" => "id_safety",
						"id" => $idSafety,
						"column" => "signature",
						"value" => $name
					);
					//enlace para regresar al formulario
					$data['linkBack'] = "safety/upload_info_safety/" . $idSafety;
				}elseif($typo == "worker"){
					$name = "images/signature/template/" . $typo . "_" . $idWorker . ".png";
					
					$arrParam = array(
						"table" => "template_used_workers",
						"primaryKey" => "id_template_used_worker",
						"id" => $idWorker,
						"column" => "signature",
						"value" => $name
					);
					//enlace para regresar al formulario con ancla a la lista de trabajadores
					$data['linkBack'] = "template/use_template/" . $idSafety . "#anclaWorker";
				}elseif($typo == "subcontractor"){
					$name = "images/signature/safety/" . $typo . "_" . $idWorker . ".png";
					
					$arrParam = array(
						"table" => "safety_workers_subcontractor",
						"primaryKey" => "id_safety_subcontractor",
						"id" => $idWorker,
						"column" => "signature",
						"value" => $name
					);
					//enlace para regresar al formulario con ancla a la lista de trabajadores
					$data['linkBack'] = "safety/upload_info_safety/" . $idSafety . "#anclaSubcontractor";					
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
			}else{			
				$this->load->view('template/make_signature');
			}
	}
	
    /**
     * Delete template worker
     */
    public function deleteTemplateWorker($idTemplateWorker, $idTemplate) 
	{
			if (empty($idTemplateWorker) || empty($idTemplate) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			$arrParam = array(
				"table" => "template_used_workers",
				"primaryKey" => "id_template_used_worker",
				"id" => $idTemplateWorker
			);
			
			$this->load->model("general_model");
			if ($this->general_model->deleteRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect(base_url('template/use_template/' . $idTemplate), 'refresh');
    }
	
    /**
     * Save one worker to the template
     */
    public function save_one_worker() 
	{
			$idTemplate = $this->input->post('hddId');

			if ($this->template_model->saveOneWorker()) {
				$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect(base_url('template/use_template/' . $idTemplate), 'refresh');
    }
	
	/**
	 * Generate Template Report in PDF
	 * @param int $idTemplate
     * @since 2/7/2017
     * @author BMOTTAG
	 */
	public function generaTemplatePDF($idTemplate)
	{
			$this->load->library('Pdf');
			
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('VCI');
			$pdf->SetTitle('Template report');
			$pdf->SetSubject('TCPDF Tutorial');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
				require_once(dirname(__FILE__).'/lang/eng.php');
				$pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('dejavusans', '', 8);

			// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
			// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		
			$arrParam = array("idTemplate" => $idTemplate);
			$data['info'] = $this->template_model->get_template($arrParam);
			$data['workers'] = $this->template_model->get_templates_workers($idTemplate);

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


			//Close and output PDF document
			$pdf->Output('template.pdf', 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		
	}

	/**
	 * Valves
     * @since 15/04/2025
     * @author BMOTTAG
	 */
	public function valves()
	{
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "valves",
				"order" => "valve_number",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'valves';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario valves
     * @since 15/04/2025
     */
    public function cargarModalValve() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idValve"] = $this->input->post("idValve");	
			
			if ($data["idValve"] != 'x') {
				$this->load->model("general_model");
				$arrParam = array(
					"table" => "valves",
					"order" => "id_valve",
					"column" => "id_valve",
					"id" => $data["idValve"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("valves_modal", $data);
    }
	
	/**
	 * Update Valves
     * @since 15/04/2025
     * @author BMOTTAG
	 */
	public function save_valve()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idValve = $this->input->post('hddId');
			
			$msj = "You have added a new Valve!!";
			if ($idValve != '') {
				$msj = "You have updated a Valve!!";
			}

			if ($idValve = $this->template_model->saveValve()) {
				$data["result"] = true;
				$data["idRecord"] = $idValve;
				
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$data["idRecord"] = "";
				
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	


	
}