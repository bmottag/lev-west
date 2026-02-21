<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hauling extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("hauling_model");
	}

	/**
	 * Evio de correo a la empresa
	 * @since 23/1/2017
	 * @author BMOTTAG
	 */
	public function email($id)
	{
		$infoHauling = $this->hauling_model->get_hauling_byId($id); //info hauling

		$subjet = "Hauling Information";
		$user = $infoHauling["contact"];
		$to = $infoHauling["email"];

		//mensaje del correo
		$msj = "<p>The following is the Hauling Information:</p>";
		$msj .= "<strong>Haul report number: </strong>" . $infoHauling["id_hauling"];
		$msj .= "<br><strong>Company: </strong>" . $infoHauling["company_name"];
		$msj .= "<br><strong>Truck: </strong>" . $infoHauling["unit_number"];
		$msj .= "<br><strong>Truck Type: </strong>" . $infoHauling["truck_type"];
		$msj .= "<br><strong>Material Type: </strong>" . $infoHauling["material"];
		$msj .= "<br><strong>Job Code/Name: </strong>" . $infoHauling["from"];
		$msj .= "<br><strong>To Site: </strong>" . $infoHauling["to"];
		$msj .= "<br><strong>Time In: </strong>" . $infoHauling["time_in"];
		$msj .= "<br><strong>Time Out: </strong>" . $infoHauling["time_out"];
		$msj .= "<br><strong>Payment: </strong>" . $infoHauling["payment"];
		$msj .= "<br><strong>Comments: </strong>" . $infoHauling["comments"];

		$msj .= "<br><br><a href='" . base_url('report/generaHaulingPDF/x/x/x/x/' . $infoHauling['id_hauling']) . "' target='_blank'>Download Report</a>";

		if ($infoHauling["contractor_signature"]) {
			$msj .= "<br><br><strong><a href='" . base_url($infoHauling["contractor_signature"]) . "'>View Subcontractor Signature </a></strong><br>";
		}

		if ($infoHauling["vci_signature"]) {
			$msj .= "<br><br><strong><a href='" . base_url($infoHauling["vci_signature"]) . "'>View V-Contracting Signature</a> </strong><br>";
		}

		$mensaje = "<html>
			<head>
			  <title> $subjet </title>
			</head>
			<body>
				<p>Dear	$user:</p>
				<p>$msj</p>
				<p>Cordially,</p>
				<p><strong>V-CONTRACTING INC</strong></p>
			</body>
			</html>";

		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$cabeceras .= 'From: Lev-West APP <fabian.v@lev-west.com>' . "\r\n";

		//enviar correo al cliente
		mail($to, $subjet, $mensaje, $cabeceras);

		//enviar correo a Lev-West
		$arrParam = array(
			"idNotification" => ID_NOTIFICATION_HAULING,
			"subjet" => $subjet,
			"msjEmail" => $mensaje,
			"msjPhone" => false
		);
		send_notification($arrParam);

		$nota = 'You have send an email to <strong>' . $infoHauling["company_name"] . '</strong> with the information.';

		$this->session->set_flashdata('retornoExito', $nota);
		redirect("/hauling/add_hauling/" . $id, 'refresh');
	}

	/**
	 * Form Add Hauling
	 * @since 11/12/2016
	 * @author BMOTTAG
	 */
	public function add_hauling($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;
		$data['HaulingClose'] = FALSE;
		$view = 'form_add_hauling';

		$arrParam = array("isHauling" => true);
		$data['companyList'] = $this->general_model->get_company($arrParam);

		//truckType´s list
		$arrParam = array(
			"table" => "param_truck_type",
			"order" => "truck_type",
			"id" => "x"
		);
		$data['truckTypeList'] = $this->general_model->get_basic_search($arrParam);

		//truckType´s list
		$arrParam = array(
			"table" => "param_material_type",
			"order" => "material",
			"id" => "x"
		);
		$data['materialTypeList'] = $this->general_model->get_basic_search($arrParam);

		//job´s list - (active´s items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);

		//truckType´s list
		$arrParam = array(
			"table" => "param_payment",
			"order" => "payment",
			"id" => "x"
		);
		$data['paymentList'] = $this->general_model->get_basic_search($arrParam);

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {

			$data['information'] = $this->hauling_model->get_hauling_byId($id); //info hauling

			$idWorkorder = $data['information']['fk_id_workorder'];
			//work order list
			$arrWOParam = array(
				"table" => "workorder",
				"order" => "id_workorder",
				"column" => "id_workorder",
				"id" => $idWorkorder
			);
			$data['workorder'] = $this->general_model->get_basic_search($arrWOParam);

			if ($data['workorder'] != null) {
				$data['workorder'] = $data['workorder'][0]['id_workorder'] . ' - ' . $data['workorder'][0]['observation'];
			} else {
				$data['workorder'] = 'Not Work Order';
			}

			//truck list
			$arrParam = array(
				"table" => "param_vehicle",
				"order" => "unit_number",
				"id" => "x"
			);
			$data['truckList'] = $this->general_model->get_basic_search($arrParam);

			//consultar si esta cerrado, si es asi lo pasamos a otra vista
			// if ($data['information']['state'] == 2) {
			// 	$data['HaulingClose'] = TRUE;
			// 	$view = 'view_hauling';
			// }
		}

		$data["view"] = $view;
		$this->load->view("layout", $data);
	}

	/**
	 * Save hauling
	 * @since 16/12/2016
	 * @author BMOTTAG
	 */
	public function save_hauling()
	{
		header('Content-Type: application/json');
		$data = array();

		$idHauling = $this->input->post('hddId');

		$timeIn = $this->input->post('hourIn');
		$timeOut = $this->input->post('hourOut');


		$msj = "You have added a new Hauling!!";
		if ($idHauling != '') {
			$msj = "You have updated a Hauling!!";
		}

		if ($idHauling = $this->hauling_model->saveHauling()) {
			$data["result"] = true;
			$data["idHauling"] = $idHauling;
			$this->session->set_flashdata('retornoExito', 'You have saved your hauling record, remember to sign and get the contractor signature!!');
		} else {
			$data["result"] = "error";
			$data["idHauling"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}


		echo json_encode($data);
	}

	/**
	 * Trucks´list by company
	 * @since 12/12/2016
	 * @author BMOTTAG
	 */
	public function truckList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$identificador = $this->input->post('identificador');
		$lista = $this->hauling_model->get_trucks_by_id($identificador);
		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["id_truck"] . "' >" . $fila["unit_number"] . "</option>";
			}
		}
	}

	/**
	 * Signature
	 * @since 9/1/201/
	 * @author BMOTTAG
	 */
	public function add_signature($type, $idHauling)
	{
		if (empty($type) || empty($idHauling)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of de file
			$name = "images/signature/hauling/" . $type . "_" . $idHauling . ".png";

			$arrParam = array(
				"table" => "hauling",
				"primaryKey" => "id_hauling",
				"id" => $idHauling,
				"column" => $type . "_signature",
				"value" => $name
			);

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['linkBack'] = "hauling/add_hauling/" . $idHauling;
			$data['titulo'] = "<i class='fa fa-life-saver fa-fw'></i>SIGNATURE";
			if ($this->general_model->updateRecord($arrParam)) {
				$data['clase'] = "alert-success";
				$data['msj'] = "Good job, you have saved your signature.";
			} else {
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
	 * Company list
	 * @since 4/2/2017
	 * @author BMOTTAG
	 */
	public function companyList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$CompanyType = $this->input->post('CompanyType');

		//company list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_company",
			"order" => "company_name",
			"column" => "company_type",
			"id" => $CompanyType
		);
		$lista = $this->general_model->get_basic_search($arrParam); //company list

		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["id_company"] . "' >" . $fila["company_name"] . "</option>";
			}
		}
	}

	/**
	 * Update hauling state
	 * @since 6/2/2017
	 * @author BMOTTAG
	 */
	public function update_hauling_state()
	{
		header('Content-Type: application/json');
		$data = array();
		$data["idHauling"] = $this->input->post('hddId');

		$state = ($this->input->post('delete')) ? 3 : 2;

		$arrParam = array(
			"table" => "hauling",
			"primaryKey" => "id_hauling",
			"id" => $data["idHauling"],
			"column" => "state",
			"value" => $state
		);
		$this->load->model("general_model");

		//actualizo el estado del formulario a cerrado(2)
		if ($this->general_model->updateRecord($arrParam)) {

			if ($state == 3) {
				$idHauling = $data["idHauling"];

				$sql = "SELECT fk_id_submodule FROM hauling WHERE id_hauling = $idHauling";
				$query = $this->db->query($sql);
				$result = $query->row_array();
				$fk_id_submodule = $result['fk_id_submodule'];

				if ($fk_id_submodule) {
					$arrParamDelete = array(
						"table" => "workorder_ocasional",
						"primaryKey" => "id_workorder_ocasional",
						"id" => $fk_id_submodule
					);
					$this->general_model->deleteRecord($arrParamDelete);
				}
			}

			$data["result"] = true;
			$data["mensaje"] = "You have closed the Hauling Report.";
			$this->session->set_flashdata('retornoExito', 'You have closed the Hauling Report');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Save hauling and send email
	 * @since 29/3/2018
	 * @author BMOTTAG
	 */
	public function save_hauling_and_send_email()
	{
		header('Content-Type: application/json');
		$data = array();

		$idHauling = $this->input->post('hddId');

		$timeIn = $this->input->post('hourIn');
		$timeOut = $this->input->post('hourOut');

		$msj = "You have added a new Hauling!!";
		if ($idHauling != '') {
			$msj = "You have updated a Hauling!!";
		}

		if ($idHauling = $this->hauling_model->saveHauling()) {
			$data["result"] = true;
			$data["idHauling"] = $idHauling;
			$this->email_v2($idHauling);
			$this->session->set_flashdata('retornoExito', 'You have saved your hauling record and send an email to the contractor, remember to sign and get the contractor signature!!');
		} else {
			$data["result"] = "error";
			$data["idHauling"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Evio de correo a la empresa
	 * @since 23/1/2017
	 * @author BMOTTAG
	 */
	public function email_v2($id)
	{
		$infoHauling = $this->hauling_model->get_hauling_byId($id); //info hauling

		$subjet = "Hauling Information";
		$user = $infoHauling["contact"];
		$to = $infoHauling["email"];

		//mensaje del correo
		$msj = "<p>The following is the Hauling Information:</p>";
		$msj .= "<strong>Haul report number: </strong>" . $infoHauling["id_hauling"];
		$msj .= "<br><strong>Company: </strong>" . $infoHauling["company_name"];
		$msj .= "<br><strong>Truck: </strong>" . $infoHauling["unit_number"];
		$msj .= "<br><strong>Truck Type: </strong>" . $infoHauling["truck_type"];
		$msj .= "<br><strong>Material Type: </strong>" . $infoHauling["material"];
		$msj .= "<br><strong>Job Code/Name: </strong>" . $infoHauling["from"];
		$msj .= "<br><strong>To Site: </strong>" . $infoHauling["to"];
		$msj .= "<br><strong>Time In: </strong>" . $infoHauling["time_in"];
		$msj .= "<br><strong>Time Out: </strong>" . $infoHauling["time_out"];
		$msj .= "<br><strong>Payment: </strong>" . $infoHauling["payment"];
		$msj .= "<br><strong>Comments: </strong>" . $infoHauling["comments"];

		$msj .= "<br><br><a href='" . base_url('report/generaHaulingPDF/x/x/x/x/' . $infoHauling['id_hauling']) . "' target='_blank'>Download Report</a>";

		if ($infoHauling["contractor_signature"]) {
			$msj .= "<br><br><strong><a href='" . base_url($infoHauling["contractor_signature"]) . "'>View Subcontractor Signature </a></strong><br>";
		}

		if ($infoHauling["vci_signature"]) {
			$msj .= "<br><br><strong><a href='" . base_url($infoHauling["vci_signature"]) . "'>View V-Contracting Signature</a> </strong><br>";
		}

		$mensaje = "<html>
			<head>
			  <title> $subjet </title>
			</head>
			<body>
				<p>Dear	$user:</p>
				<p>$msj</p>
				<p>Cordially,</p>
				<p><strong>V-CONTRACTING INC</strong></p>
			</body>
			</html>";

		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$cabeceras .= 'From: Lev-West APP <fabian.v@lev-west.com>' . "\r\n";

		//enviar correo al cliente
		mail($to, $subjet, $mensaje, $cabeceras);

		//enviar correo a Lev-West
		$arrParam = array(
			"idNotification" => ID_NOTIFICATION_HAULING,
			"subjet" => $subjet,
			"msjEmail" => $mensaje,
			"msjPhone" => false
		);
		send_notification($arrParam);

		return true;
	}

	/**
	 * Work Order list
	 * @since 9/1/2025
	 * @author BMOTTAG
	 */
	public function woList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$jobCode = $this->input->post('jobCode');
		$list = $this->hauling_model->get_wo_job_code($jobCode);
		echo "<option value=''>Select...</option>";
		if ($list) {
			foreach ($list as $fila) {
				echo "<option value='" . $fila["id_workorder"] . "' >" . $fila["id_workorder"] . " - " . $fila["observation"] . "</option>";
			}
		}
	}

	/**
	 * Work Order list
	 * @since 9/1/2025
	 * @author BMOTTAG
	 */
	public function list_by_job_code()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$jobCode = $this->input->post('jobCode');
		$id_workorder = $this->hauling_model->list_by_job_code($jobCode);

		echo $id_workorder;
	}
}
