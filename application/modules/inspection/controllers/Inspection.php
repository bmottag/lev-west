<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inspection extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("inspection_model");
	}

	/**
	 * Form Add Heavy Inspection
	 * @since 17/12/2016
	 * @author BMOTTAG
	 */
	public function index()
	{
		$idVehicle = $this->session->userdata("idVehicle");
		if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");
		//busco datos del vehiculo ingresado
		$arrParam = array(
			"table" => "param_vehicle",
			"order" => "id_vehicle",
			"column" => "id_vehicle",
			"id" => $idVehicle
		);
		$data['vehicleInfo'] = $this->general_model->get_basic_search($arrParam);

		$data["view"] = 'form_inspection';
		$this->load->view("layout", $data);
	}


	/**
	 * Form Add daily Inspection
	 * @since 27/12/2016
	 * @author BMOTTAG
	 */
	public function add_daily_inspection($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;
		$view = 'form_daily_inspection';

		//si envio el id, entonces busco la informacion
		if ($id != 'x') {
			$arrParam = array(
				"table" => "inspection_daily",
				"order" => "id_inspection_daily",
				"column" => "id_inspection_daily",
				"id" => $id
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam); //info inspection_heavy

			$idVehicle = $data['information'][0]['fk_id_vehicle'];
		} else {
			$idVehicle = $this->session->userdata("idVehicle");
			if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam); //busco datos del vehiculo

		//busco lista de trailers
		$arrParam = array(
			"table" => "param_vehicle",
			"order" => "id_vehicle",
			"column" => "type_level_2",
			"id" => 5
		);
		$data['trailerList'] = $this->general_model->get_basic_search($arrParam); //busco lista de trailers

		$data["view"] = $view;
		$this->load->view("layout", $data);
	}

	/**
	 * Save daily_inspection
	 * @since 27/12/2016
	 * @author BMOTTAG
	 */
	public function save_daily_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idDailyInspection = $this->input->post('hddId');
		$idVehicle = $this->input->post('hddIdVehicle');

		$msj = "You have saved your inspection record, please do not forget to sign!!";
		$flag = true;
		if ($idDailyInspection != '') {
			$msj = "You have updated the Inspection record!!";
			$flag = false;
		}

		$trailer = $this->input->post('trailer');
		$trailerLights = $this->input->post('trailerLights');
		$trailerTires = $this->input->post('trailerTires');
		$trailerSlings = $this->input->post('trailerSlings');
		$trailerClean = $this->input->post('trailerClean');
		$trailerChains = $this->input->post('trailerChains');
		$trailerRatchet = $this->input->post('trailerRatchet');

		if ($trailer != '' && ($trailerLights == '' || $trailerTires == '' || $trailerSlings == '' || $trailerClean == '' || $trailerChains == '' || $trailerRatchet == '')) {
			$data["result"] = "error";
			$data["mensaje"] = "If you are using a Tralier, you must fill out the TRAILER or PUP form.";
			$data["idDailyInspection"] = $idDailyInspection;
			$this->session->set_flashdata('retornoError', 'If you are using a Tralier, you must fill out the TRAILER or PUP form.');
		} else {
			if ($idDailyInspection = $this->inspection_model->saveDailyInspection()) {
				//actualizo seguimiento en la tabla de vehiculos, para mostrar mensaje
				$this->inspection_model->saveSeguimiento();

				/**
				 * si es un registro nuevo entonces verifico si hay comentarios y envio correo al administrador
				 */
				if ($flag) {
					//guardo registro de fecha y maquina, para comparar con la programacion
					$this->inspection_model->saveInspectionTotal($idVehicle);

					//el que vaya con comentario le envio correo al administrador
					$comments = $this->input->post('comments');
					$hours = $this->input->post('hours');

					//OIL CAHNGE
					$state = 1; //Inspection
					$this->inspection_model->saveVehicleNextOilChange($idVehicle, $state, $idDailyInspection);

					//si hay un FAIL de los siguientes campos envio correo al ADMINISTRADOR
					$headLamps = $this->input->post('headLamps');
					$hazardLights = $this->input->post('hazardLights');
					$bakeLights = $this->input->post('bakeLights');
					$workLights = $this->input->post('workLights');
					$turnSignals = $this->input->post('turnSignals');
					$beaconLight = $this->input->post('beaconLight');
					$clearanceLights = $this->input->post('clearanceLights');

					$lights_check = 1;
					if ($headLamps == 0 || $hazardLights == 0 || $bakeLights == 0 || $workLights == 0 || $turnSignals == 0 || $beaconLight == 0 || $clearanceLights == 0) {
						$lights_check = 0;
					}

					$heater_check = $this->input->post('heater');
					$brakes_check = $this->input->post('brakePedal');
					$steering_wheel_check = $this->input->post('steering_wheel');
					$suspension_system_check = $this->input->post('suspension_system');
					$tires_check = $this->input->post('nuts');
					$wipers_check = $this->input->post('wipers');
					$air_brake_check = $this->input->post('air_brake');
					$driver_seat_check = $this->input->post('passengerDoor');
					$fuel_system_check = $this->input->post('fuel_system');

					//flag
					$sendNotification = false;
					$subjet = "";
					if ($comments != "") {
						$emailMsnTitle = "<p>The following inspection have comments please check the complete report in the system.</p>";
						$subjet = "Inspection with comments";
						$sendNotification = true;
					}

					$failsEmail = "";
					$fails = "";
					//preguntar especiales para HYDROVAC para que muestre mensaje si es inseguro sacar el camion
					if ($heater_check == 0 || $brakes_check == 0 || $lights_check == 0 || $steering_wheel_check == 0 || $suspension_system_check == 0 || $tires_check == 0 || $wipers_check == 0 || $air_brake_check == 0 || $driver_seat_check == 0 || $fuel_system_check == 0) {

						//mensaje del correo
						$majorDefect = "<p>A major defect has been identified in the last inspecton, a driver is not legally permitted to operate the vehicle until that defect is prepared.</p>";
						$emailMsnTitle = $sendNotification ? $emailMsnTitle . $majorDefect : $majorDefect;

						if ($heater_check == 0) {
							$failsEmail .= "<br>Heater - Fail";
							$fails .= "\nHeater - Fail";
						}
						if ($brakes_check == 0) {
							$failsEmail .= "<br>Heater - Fail";
							$fails .= "\nHeater - Fail";
						}
						if ($lights_check == 0) {
							$failsEmail .= "<br>Lamps and reflectors - Fail";
							$fails .= "\nLamps and reflectors - Fail";
						}
						if ($steering_wheel_check == 0) {
							$failsEmail .= "<br>Steering wheel - Fail";
							$fails .= "\nSteering wheel - Fail";
						}
						if ($suspension_system_check == 0) {
							$failsEmail .= "<br>Suspension system - Fail";
							$fails .= "\nSuspension system - Fail";
						}
						if ($tires_check == 0) {
							$failsEmail .= "<br>Tires/Lug Nuts/Pressure - Fail";
							$fails .= "\nTires/Lug Nuts/Pressure - Fail";
						}
						if ($wipers_check == 0) {
							$failsEmail .= "<br>Wipers/Washers - Fail";
							$fails .= "\nWipers/Washers - Fail";
						}
						if ($air_brake_check == 0) {
							$failsEmail .= "<br>Air brake system - Fail";
							$fails .= "\nAir brake system - Fail";
						}
						if ($driver_seat_check == 0) {
							$failsEmail .= "<br>Driver and Passenger door - Fail";
							$fails .= "\nDriver and Passenger door - Fail";
						}
						if ($fuel_system_check == 0) {
							$failsEmail .= "<br>Fuel system - Fail";
							$fails .= "\nFuel system - Fail";
						}

						$subjet = $sendNotification ? $subjet . " & " : "";
						$subjet .= "Inspection with major defect";
						$sendNotification = true;
					}

					//enviar correo
					if ($sendNotification) {
						//busco datos del vehiculo
						$arrParam = array(
							"table" => "param_vehicle",
							"order" => "id_vehicle",
							"column" => "id_vehicle",
							"id" => $idVehicle
						);
						$this->load->model("general_model");
						$vehicleInfo = $this->general_model->get_basic_search($arrParam);

						$module = base64_encode("INSPECTION_LIST_BY_EQUIPMENT_ID");
						$idModule = base64_encode($idVehicle);
						$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);

						//mensaje del correo
						$emailMsn = $emailMsnTitle;
						$emailMsn .= "<strong>Make: </strong>" . $vehicleInfo[0]["make"];
						$emailMsn .= "<br><strong>Model: </strong>" . $vehicleInfo[0]["model"];
						$emailMsn .= "<br><strong>Unit Number: </strong>" . $vehicleInfo[0]["unit_number"];
						$emailMsn .= "<br><strong>Description: </strong>" . $vehicleInfo[0]["description"];
						$emailMsn .= "<br><strong>Equipment Hours/Kilometers: </strong>" . number_format($hours);
						$emailMsn .= $comments != "" ? "<br><strong>Comments: </strong>" . $comments : "";
						$emailMsn .= $failsEmail ? "<p><b>Fails:</b><br>" . $failsEmail . "</p>" : "";
						$emailMsn .= "<p>Follow the link to see the list. <a href='" . $urlMovil . "' >Click here </a></p>";

						$mensaje = "<html>
										<head>
										<title> $subjet </title>
										</head>
										<body>
											<p>Dear	Administrator:</p>
											<p>$emailMsn</p>
											<p>Cordially,</p>
											<p><strong>V-CONTRACTING INC</strong></p>
										</body>
										</html>";

						//mensaje de texto
						$mensajeSMS = "Lev-West APP - " . $subjet;
						$mensajeSMS .= "\nUnit Number: " . $vehicleInfo[0]["unit_number"];
						$mensajeSMS .= $comments != "" ? "\nComments: " . $comments : "";
						$mensajeSMS .= $fails;
						$mensajeSMS .= "\n\nSee: " . $urlMovil;

						//enviar correo a Lev-West
						$arrParam = array(
							"idNotification" => ID_NOTIFICATION_INSPECTIONS,
							"subjet" => $subjet,
							"msjEmail" => $mensaje,
							"msjPhone" => $mensajeSMS
						);
						send_notification($arrParam);
					}
				}

				$data["result"] = true;
				$data["idDailyInspection"] = $idDailyInspection;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$data["idDailyInspection"] = "";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		}

		echo json_encode($data);
	}

	/**
	 * Form Add Heavy Inspection
	 * @since 17/12/2016
	 * @author BMOTTAG
	 */
	public function add_heavy_inspection($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;

		//si envio el id, entonces busco la informacion
		if ($id != 'x') {
			$arrParam = array(
				"table" => "inspection_heavy",
				"order" => "id_inspection_heavy",
				"column" => "id_inspection_heavy",
				"id" => $id
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam); //info inspection_heavy

			$idVehicle = $data['information'][0]['fk_id_vehicle'];
		} else {
			$idVehicle = $this->session->userdata("idVehicle");
			if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$data["view"] = $data['vehicleInfo'][0]['form'];
		$this->load->view("layout", $data);
	}

	/**
	 * Save heavy_inspection
	 * @since 27/12/2016
	 * @author BMOTTAG
	 */
	public function save_heavy_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idHeavyInspection = $this->input->post('hddId');
		$idVehicle = $this->input->post('hddIdVehicle');

		$msj = "You have saved your inspection record, please do not forget to sign!!";
		$flag = true;
		if ($idHeavyInspection != '') {
			$flag = false;
			$msj = "You have updated the Inspection record!!";
		}

		if ($idHeavyInspection = $this->inspection_model->saveHeavyInspection()) {
			/**
			 * si es un registro nuevo entonces guardo el historial de cambio de aceite
			 * y verifico si hay comentarios y envio correo al administrador
			 */
			if ($flag) {
				//guardo registro de fecha y maquina, para comparar con la programacion
				$this->inspection_model->saveInspectionTotal($idVehicle);

				//el que vaya con comentario le envio correo al administrador
				$comments = $this->input->post('comments');
				$hours = $this->input->post('hours');

				//OIL CAHNGE
				$state = 1; //Inspection
				$this->inspection_model->saveVehicleNextOilChange($idVehicle, $state, $idHeavyInspection);

				//flag
				$sendNotification = false;
				if ($comments != "") {
					$emailMsnTitle = "<p>The following inspection have comments please check the complete report in the system.</p>";
					$subjet = "Inspection with comments";
					$sendNotification = true;
				}

				//enviar correo
				if ($sendNotification) {
					//busco datos del vehiculo
					$arrParam = array(
						"table" => "param_vehicle",
						"order" => "id_vehicle",
						"column" => "id_vehicle",
						"id" => $idVehicle
					);
					$this->load->model("general_model");
					$vehicleInfo = $this->general_model->get_basic_search($arrParam);

					$module = base64_encode("INSPECTION_LIST_BY_EQUIPMENT_ID");
					$idModule = base64_encode($idVehicle);
					$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);

					//mensaje del correo
					$emailMsn = $emailMsnTitle;
					$emailMsn .= "<strong>Make: </strong>" . $vehicleInfo[0]["make"];
					$emailMsn .= "<br><strong>Model: </strong>" . $vehicleInfo[0]["model"];
					$emailMsn .= "<br><strong>Unit Number: </strong>" . $vehicleInfo[0]["unit_number"];
					$emailMsn .= "<br><strong>Description: </strong>" . $vehicleInfo[0]["description"];
					$emailMsn .= "<br><strong>Equipment Hours/Kilometers: </strong>" . number_format($hours);
					$emailMsn .= $comments != "" ? "<br><strong>Comments: </strong>" . $comments : "";
					$emailMsn .= "<p>Follow the link to see the list. <a href='" . $urlMovil . "' >Click here </a></p>";

					$mensaje = "<html>
									<head>
									<title> $subjet </title>
									</head>
									<body>
										<p>Dear	Administrator:</p>
										<p>$emailMsn</p>
										<p>Cordially,</p>
										<p><strong>V-CONTRACTING INC</strong></p>
									</body>
									</html>";

					//mensaje de texto
					$mensajeSMS = "Lev-West APP - " . $subjet;
					$mensajeSMS .= "\nUnit Number: " . $vehicleInfo[0]["unit_number"];
					$mensajeSMS .= $comments != "" ? "\nComments: " . $comments : "";
					$mensajeSMS .= "\n\nSee: " . $urlMovil;

					//enviar correo a Lev-West
					$arrParam = array(
						"idNotification" => ID_NOTIFICATION_INSPECTIONS,
						"subjet" => $subjet,
						"msjEmail" => $mensaje,
						"msjPhone" => $mensajeSMS
					);
					send_notification($arrParam);
				}
			}

			$data["result"] = true;
			$data["idHeavyInspection"] = $idHeavyInspection;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idHeavyInspection"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Signature
	 * @since 27/12/2016
	 * @author BMOTTAG
	 */
	public function add_signature($typo, $idInspection)
	{
		if (empty($typo) || empty($idInspection)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		if ($_POST) {

			//update signature with the name of de file
			$name = "images/signature/inspection/" . $typo . "_" . $idInspection . ".png";

			$arrParam = array(
				"table" => "inspection_" . $typo,
				"primaryKey" => "id_inspection_" . $typo,
				"id" => $idInspection,
				"column" => "signature",
				"value" => $name
			);

			$data_uri = $this->input->post("image");
			$encoded_image = explode(",", $data_uri)[1];
			$decoded_image = base64_decode($encoded_image);
			file_put_contents($name, $decoded_image);

			$this->load->model("general_model");
			$data['linkBack'] = "inspection/add_" . $typo . "_inspection/" . $idInspection;
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
			//redirect("/inspection/add_" . $typo . "_inspection/" . $idInspection,'refresh');
		} else {
			$this->load->view('template/make_signature');
		}
	}

	/**
	 * Form Generator Inspection
	 * @since 16/3/2017
	 * @author BMOTTAG
	 */
	public function add_generator_inspection($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array(
				"table" => "inspection_generator",
				"order" => "id_inspection_generator",
				"column" => "id_inspection_generator",
				"id" => $id
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam); //info inspection_generator

			$idVehicle = $data['information'][0]['fk_id_vehicle'];
		} else {
			$idVehicle = $this->session->userdata("idVehicle");
			if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$data["view"] = $data['vehicleInfo'][0]['form'];
		$this->load->view("layout", $data);
	}

	/**
	 * Save generator_inspection
	 * @since 17/3/2017
	 * @author BMOTTAG
	 */
	public function save_generator_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idGeneratorInspection = $this->input->post('hddId');
		$idVehicle = $this->input->post('hddIdVehicle');

		$msj = "You have saved your inspection record, please do not forget to sign!!";
		$flag = true;
		if ($idGeneratorInspection != '') {
			$flag = false;
			$msj = "You have updated the Inspection record!!";
		}

		if ($idGeneratorInspection = $this->inspection_model->saveGeneratorInspection()) {
			/**
			 * si es un registro nuevo entonces guardo el historial de cambio de aceite
			 * y verifico si hay comentarios y envio correo al administrador
			 */
			if ($flag) {
				//el que vaya con comentario le envio correo al administrador
				$comments = $this->input->post('comments');
				$hours = $this->input->post('hours');

				//OIL CAHNGE
				$state = 1; //Inspection
				$this->inspection_model->saveVehicleNextOilChange($idVehicle, $state, $idGeneratorInspection);

				//flag
				$sendNotification = false;
				if ($comments != "") {
					$emailMsnTitle = "<p>The following inspection have comments please check the complete report in the system.</p>";
					$subjet = "Inspection with comments";
					$sendNotification = true;
				}

				//enviar correo
				if ($sendNotification) {
					//busco datos del vehiculo
					$arrParam = array(
						"table" => "param_vehicle",
						"order" => "id_vehicle",
						"column" => "id_vehicle",
						"id" => $idVehicle
					);
					$this->load->model("general_model");
					$vehicleInfo = $this->general_model->get_basic_search($arrParam);

					$module = base64_encode("INSPECTION_LIST_BY_EQUIPMENT_ID");
					$idModule = base64_encode($idVehicle);
					$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);

					//mensaje del correo
					$emailMsn = $emailMsnTitle;
					$emailMsn .= "<strong>Make: </strong>" . $vehicleInfo[0]["make"];
					$emailMsn .= "<br><strong>Model: </strong>" . $vehicleInfo[0]["model"];
					$emailMsn .= "<br><strong>Unit Number: </strong>" . $vehicleInfo[0]["unit_number"];
					$emailMsn .= "<br><strong>Description: </strong>" . $vehicleInfo[0]["description"];
					$emailMsn .= "<br><strong>Equipment Hours/Kilometers: </strong>" . number_format($hours);
					$emailMsn .= $comments != "" ? "<br><strong>Comments: </strong>" . $comments : "";
					$emailMsn .= "<p>Follow the link to see the list. <a href='" . $urlMovil . "' >Click here </a></p>";

					$mensaje = "<html>
									<head>
									<title> $subjet </title>
									</head>
									<body>
										<p>Dear	Administrator:</p>
										<p>$emailMsn</p>
										<p>Cordially,</p>
										<p><strong>V-CONTRACTING INC</strong></p>
									</body>
									</html>";

					//mensaje de texto
					$mensajeSMS = "Lev-West APP - " . $subjet;
					$mensajeSMS .= "\nUnit Number: " . $vehicleInfo[0]["unit_number"];
					$mensajeSMS .= $comments != "" ? "\nComments: " . $comments : "";
					$mensajeSMS .= "\n\nSee: " . $urlMovil;

					//enviar correo a Lev-West
					$arrParam = array(
						"idNotification" => ID_NOTIFICATION_INSPECTIONS,
						"subjet" => $subjet,
						"msjEmail" => $mensaje,
						"msjPhone" => $mensajeSMS
					);
					send_notification($arrParam);
				}
			}

			$data["result"] = true;
			$data["idGeneratorInspection"] = $idGeneratorInspection;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idGeneratorInspection"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form SWEEPER Inspection
	 * @since 22/4/2017
	 * @author BMOTTAG
	 */
	public function add_sweeper_inspection($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array(
				"table" => "inspection_sweeper",
				"order" => "id_inspection_sweeper",
				"column" => "id_inspection_sweeper",
				"id" => $id
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam); //info inspection_generator

			$idVehicle = $data['information'][0]['fk_id_vehicle'];
		} else {
			$idVehicle = $this->session->userdata("idVehicle");
			if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$data["view"] = $data['vehicleInfo'][0]['form'];
		$this->load->view("layout", $data);
	}

	/**
	 * Save sweeper inspection
	 * @since 22/4/2017
	 * @author BMOTTAG
	 */
	public function save_sweeper_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idSweeperInspection = $this->input->post('hddId');
		$idVehicle = $this->input->post('hddIdVehicle');

		$msj = "You have saved your inspection record, please do not forget to sign!!";
		$flag = true;
		if ($idSweeperInspection != '') {
			$flag = false;
			$msj = "You have updated the Inspection record!!";
		}

		if ($idSweeperInspection = $this->inspection_model->saveSweeperInspection()) {
			/**
			 * si es un registro nuevo entonces guardo el historial de cambio de aceite
			 * y verifico si hay comentarios y envio correo al administrador
			 */
			if ($flag) {
				//guardo registro de fecha y maquina, para comparar con la programacion
				$this->inspection_model->saveInspectionTotal($idVehicle);

				//el que vaya con comentario le envio correo al administrador
				$comments = $this->input->post('comments');
				$hours = $this->input->post('hours');
				$hours2 = $this->input->post('hours2');

				//OIL CAHNGE
				$state = 1; //Inspection
				$this->inspection_model->saveVehicleNextOilChange($idVehicle, $state, $idSweeperInspection);

				//flag
				$sendNotification = false;
				if ($comments != "") {
					$emailMsnTitle = "<p>The following inspection have comments please check the complete report in the system.</p>";
					$subjet = "Inspection with comments";
					$sendNotification = true;
				}

				//enviar correo
				if ($sendNotification) {
					//busco datos del vehiculo
					$arrParam = array(
						"table" => "param_vehicle",
						"order" => "id_vehicle",
						"column" => "id_vehicle",
						"id" => $idVehicle
					);
					$this->load->model("general_model");
					$vehicleInfo = $this->general_model->get_basic_search($arrParam);

					$module = base64_encode("INSPECTION_LIST_BY_EQUIPMENT_ID");
					$idModule = base64_encode($idVehicle);
					$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);

					//mensaje del correo
					$emailMsn = $emailMsnTitle;
					$emailMsn .= "<strong>Make: </strong>" . $vehicleInfo[0]["make"];
					$emailMsn .= "<br><strong>Model: </strong>" . $vehicleInfo[0]["model"];
					$emailMsn .= "<br><strong>Unit Number: </strong>" . $vehicleInfo[0]["unit_number"];
					$emailMsn .= "<br><strong>Description: </strong>" . $vehicleInfo[0]["description"];
					$emailMsn .= "<br><strong>Truck Engine Hours: </strong>" . number_format($hours);
					$emailMsn .= "<br><strong>Sweeper Engine Hours: </strong>" . number_format($hours2);
					$emailMsn .= $comments != "" ? "<br><strong>Comments: </strong>" . $comments : "";
					$emailMsn .= "<p>Follow the link to see the list. <a href='" . $urlMovil . "' >Click here </a></p>";

					$mensaje = "<html>
									<head>
									<title> $subjet </title>
									</head>
									<body>
										<p>Dear	Administrator:</p>
										<p>$emailMsn</p>
										<p>Cordially,</p>
										<p><strong>V-CONTRACTING INC</strong></p>
									</body>
									</html>";

					//mensaje de texto
					$mensajeSMS = "Lev-West APP - " . $subjet;
					$mensajeSMS .= "\nUnit Number: " . $vehicleInfo[0]["unit_number"];
					$mensajeSMS .= $comments != "" ? "\nComments: " . $comments : "";
					$mensajeSMS .= "\n\nSee: " . $urlMovil;

					//enviar correo a Lev-West
					$arrParam = array(
						"idNotification" => ID_NOTIFICATION_INSPECTIONS,
						"subjet" => $subjet,
						"msjEmail" => $mensaje,
						"msjPhone" => $mensajeSMS
					);
					send_notification($arrParam);
				}
			}

			$data["result"] = true;
			$data["idSweeperInspection"] = $idSweeperInspection;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idSweeperInspection"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form hydrovac Inspection
	 * @since 23/4/2017
	 * @author BMOTTAG
	 */
	public function add_hydrovac_inspection($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array(
				"table" => "inspection_hydrovac",
				"order" => "id_inspection_hydrovac",
				"column" => "id_inspection_hydrovac",
				"id" => $id
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam); //info inspection_generator

			$idVehicle = $data['information'][0]['fk_id_vehicle'];
		} else {
			$idVehicle = $this->session->userdata("idVehicle");
			if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$data["view"] = $data['vehicleInfo'][0]['form'];
		$this->load->view("layout", $data);
	}

	/**
	 * Save hydrovac inspection
	 * @since 23/4/2017
	 * @author BMOTTAG
	 */
	public function save_hydrovac_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idHydrovacInspection = $this->input->post('hddId');
		$idVehicle = $this->input->post('hddIdVehicle');

		$msj = "You have saved your inspection record, please do not forget to sign!!";
		$flag = true;
		if ($idHydrovacInspection != '') {
			$flag = false;
			$msj = "You have updated the Inspection record!!";
		}

		if ($idHydrovacInspection = $this->inspection_model->saveHydrovacInspection()) {
			//actualizo seguimiento en la tabla de vehiculos, para mostrar mensaje
			$this->inspection_model->saveSeguimientoHydrovac();

			/**
			 * si es un registro nuevo entonces guardo el historial de cambio de aceite
			 * y verifico si hay comentarios y envio correo al administrador
			 */
			if ($flag) {
				//guardo registro de fecha y maquina, para comparar con la programacion
				$this->inspection_model->saveInspectionTotal($idVehicle);

				//el que vaya con comentario le envio correo al administrador
				$comments = $this->input->post('comments');
				$hours = $this->input->post('hours');
				$hours2 = $this->input->post('hours2');
				$hours3 = $this->input->post('hours3');

				//OIL CAHNGE
				$state = 1; //Inspection
				$this->inspection_model->saveVehicleNextOilChange($idVehicle, $state, $idHydrovacInspection);

				//si hay un FAIL de los siguientes campos envio correo al ADMINISTRADOR
				$headLamps = $this->input->post('headLamps');
				$hazardLights = $this->input->post('hazardLights');
				$clearanceLights = $this->input->post('clearanceLights');
				$tailLights = $this->input->post('tailLights');
				$workLights = $this->input->post('workLights');
				$turnSignals = $this->input->post('turnSignals');
				$beaconLight = $this->input->post('beaconLights');

				$lights_check = 1;
				if ($headLamps == 0 || $hazardLights == 0 || $tailLights == 0 || $workLights == 0 || $turnSignals == 0 || $beaconLight == 0 || $clearanceLights == 0) {
					$lights_check = 0;
				}

				$heater_check = $this->input->post('heater');
				$brakes_check = $this->input->post('brake');
				$steering_wheel_check = $this->input->post('steering_wheel');
				$suspension_system_check = $this->input->post('suspension_system');
				$tires_check = $this->input->post('tires');
				$wipers_check = $this->input->post('wipers');
				$air_brake_check = $this->input->post('air_brake');
				$driver_seat_check = $this->input->post('door');
				$fuel_system_check = $this->input->post('fuel_system');

				//flag
				$sendNotification = false;
				$subjet = "";
				if ($comments != "") {
					$emailMsnTitle = "<p>The following inspection have comments please check the complete report in the system.</p>";
					$subjet = "Inspection with comments";
					$sendNotification = true;
				}

				$failsEmail = "";
				$fails = "";
				//preguntar especiales para HYDROVAC para que muestre mensaje si es inseguro sacar el camion
				if ($heater_check == 0 || $brakes_check == 0 || $lights_check == 0 || $steering_wheel_check == 0 || $suspension_system_check == 0 || $tires_check == 0 || $wipers_check == 0 || $air_brake_check == 0 || $driver_seat_check == 0 || $fuel_system_check == 0) {

					//mensaje del correo
					$majorDefect = "<p>A major defect has beed identified in the last inspecton, a driver is not legally permitted to operate the vehicle until that defect is prepared.</p>";
					$emailMsnTitle = $sendNotification ? $emailMsnTitle . $majorDefect : $majorDefect;

					if ($heater_check == 0) {
						$failsEmail .= "<br>Heater - Fail";
						$fails .= "\nHeater - Fail";
					}
					if ($brakes_check == 0) {
						$failsEmail .= "<br>Brake pedal - Fail";
						$fails .= "\Brake pedal - Fail";
					}
					if ($lights_check == 0) {
						$failsEmail .= "<br>Lamps and reflectors - Fail";
						$fails .= "\nLamps and reflectors - Fail";
					}
					if ($steering_wheel_check == 0) {
						$failsEmail .= "<br>Steering wheel - Fail";
						$fails .= "\nSteering wheel - Fail";
					}
					if ($suspension_system_check == 0) {
						$failsEmail .= "<br>Suspension system - Fail";
						$fails .= "\nSuspension system - Fail";
					}
					if ($tires_check == 0) {
						$failsEmail .= "<br>Tires/Lug Nuts/Pressure - Fail";
						$fails .= "\nTires/Lug Nuts/Pressure - Fail";
					}
					if ($wipers_check == 0) {
						$failsEmail .= "<br>Wipers/Washers - Fail";
						$fails .= "\nWipers/Washers - Fail";
					}
					if ($air_brake_check == 0) {
						$failsEmail .= "<br>Air brake system - Fail";
						$fails .= "\nAir brake system - Fail";
					}
					if ($driver_seat_check == 0) {
						$failsEmail .= "<br>Driver and Passenger door - Fail";
						$fails .= "\nDriver and Passenger door - Fail";
					}
					if ($fuel_system_check == 0) {
						$failsEmail .= "<br>Fuel system - Fail";
						$fails .= "\nFuel system - Fail";
					}
					$subjet = $sendNotification ? $subjet . " & " : "";
					$subjet .= "Inspection with major defect";
					$sendNotification = true;
				}

				//enviar correo
				if ($sendNotification) {
					//busco datos del vehiculo
					$arrParam = array(
						"table" => "param_vehicle",
						"order" => "id_vehicle",
						"column" => "id_vehicle",
						"id" => $idVehicle
					);
					$this->load->model("general_model");
					$vehicleInfo = $this->general_model->get_basic_search($arrParam);

					$module = base64_encode("INSPECTION_LIST_BY_EQUIPMENT_ID");
					$idModule = base64_encode($idVehicle);
					$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);

					//mensaje del correo
					$emailMsn = $emailMsnTitle;
					$emailMsn .= "<strong>Make: </strong>" . $vehicleInfo[0]["make"];
					$emailMsn .= "<br><strong>Model: </strong>" . $vehicleInfo[0]["model"];
					$emailMsn .= "<br><strong>Unit Number: </strong>" . $vehicleInfo[0]["unit_number"];
					$emailMsn .= "<br><strong>Description: </strong>" . $vehicleInfo[0]["description"];
					$emailMsn .= "<br><strong>Engine Hours: </strong>" . number_format($hours);
					$emailMsn .= "<br><strong>Hydraulic Pump Hours: </strong>" . number_format($hours2);
					$emailMsn .= "<br><strong>Blower Hours:: </strong>" . number_format($hours3);
					$emailMsn .= $comments != "" ? "<br><strong>Comments: </strong>" . $comments : "";
					$emailMsn .= $failsEmail;
					$emailMsn .= "<p>Follow the link to see the list. <a href='" . $urlMovil . "' >Click here </a></p>";

					$mensaje = "<html>
									<head>
									<title> $subjet </title>
									</head>
									<body>
										<p>Dear	Administrator:</p>
										<p>$emailMsn</p>
										<p>Cordially,</p>
										<p><strong>V-CONTRACTING INC</strong></p>
									</body>
									</html>";

					//mensaje de texto
					$mensajeSMS = "Lev-West APP - " . $subjet;
					$mensajeSMS .= "\nUnit Number: " . $vehicleInfo[0]["unit_number"];
					$mensajeSMS .= $comments != "" ? "\nComments: " . $comments : "";
					$mensajeSMS .= $fails;
					$mensajeSMS .= "\n\nSee: " . $urlMovil;

					//enviar correo a Lev-West
					$arrParam = array(
						"idNotification" => ID_NOTIFICATION_INSPECTIONS,
						"subjet" => $subjet,
						"msjEmail" => $mensaje,
						"msjPhone" => $mensajeSMS
					);
					send_notification($arrParam);
				}
			}

			$data["result"] = true;
			$data["idHydrovacInspection"] = $idHydrovacInspection;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idHydrovacInspection"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Form water truck Inspection
	 * @since 11/6/2017
	 * @author BMOTTAG
	 */
	public function add_watertruck_inspection($id = 'x')
	{
		$this->load->model("general_model");

		$data['information'] = FALSE;

		//si envio el id, entonces busco la informacion 
		if ($id != 'x') {
			$arrParam = array(
				"table" => "inspection_watertruck",
				"order" => "id_inspection_watertruck",
				"column" => "id_inspection_watertruck",
				"id" => $id
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam); //info inspection watertruck

			$idVehicle = $data['information'][0]['fk_id_vehicle'];
		} else {
			$idVehicle = $this->session->userdata("idVehicle");
			if (!$idVehicle || empty($idVehicle) || $idVehicle == "x") {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		}

		//busco datos del vehiculo
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$data["view"] = $data['vehicleInfo'][0]['form'];
		$this->load->view("layout", $data);
	}

	/**
	 * Save water truck inspection
	 * @since 12/6/2017
	 * @author BMOTTAG
	 */
	public function save_watertruck_inspection()
	{
		header('Content-Type: application/json');
		$data = array();

		$idWatertruckInspection = $this->input->post('hddId');
		$idVehicle = $this->input->post('hddIdVehicle');

		$msj = "You have saved your inspection record, please do not forget to sign!!";
		$flag = true;
		if ($idWatertruckInspection != '') {
			$flag = false;
			$msj = "You have updated the Inspection record!!";
		}

		if ($idWatertruckInspection = $this->inspection_model->saveWatertruckInspection()) {
			//actualizo seguimiento en la tabla de vehiculos, para mostrar mensaje
			$this->inspection_model->saveSeguimientoWatertruck();

			/**
			 * si es un registro nuevo entonces guardo el historial de cambio de aceite
			 * y verifico si hay comentarios y envio correo al administrador
			 */
			if ($flag) {
				//guardo registro de fecha y maquina, para comparar con la programacion
				$this->inspection_model->saveInspectionTotal($idVehicle);

				//el que vaya con comentario le envio correo al administrador
				$comments = $this->input->post('comments');
				$hours = $this->input->post('hours');

				//OIL CAHNGE
				$state = 1; //Inspection
				$this->inspection_model->saveVehicleNextOilChange($idVehicle, $state, $idWatertruckInspection);

				//si hay un FAIL de los siguientes campos envio correo al ADMINISTRADOR
				$headLamps = $this->input->post('headLamps');
				$hazardLights = $this->input->post('hazardLights');
				$clearanceLights = $this->input->post('clearanceLights');
				$tailLights = $this->input->post('tailLights');
				$workLights = $this->input->post('workLights');
				$turnSignals = $this->input->post('turnSignals');
				$beaconLight = $this->input->post('beaconLights');

				$lights_check = 1;
				if ($headLamps == 0 || $hazardLights == 0 || $tailLights == 0 || $workLights == 0 || $turnSignals == 0 || $beaconLight == 0 || $clearanceLights == 0) {
					$lights_check = 0;
				}

				$heater_check = $this->input->post('heater');
				$brakes_check = $this->input->post('brake');
				$steering_wheel_check = $this->input->post('steering_wheel');
				$suspension_system_check = $this->input->post('suspension_system');
				$tires_check = $this->input->post('tires');
				$wipers_check = $this->input->post('wipers');
				$air_brake_check = $this->input->post('air_brake');
				$driver_seat_check = $this->input->post('door');
				$fuel_system_check = $this->input->post('fuel_system');

				//flag
				$sendNotification = false;
				$subjet = "";
				if ($comments != "") {
					$emailMsnTitle = "<p>The following inspection have comments please check the complete report in the system.</p>";
					$subjet = "Inspection with comments";
					$sendNotification = true;
				}

				$failsEmail = "";
				$fails = "";
				//preguntar especiales para HYDROVAC para que muestre mensaje si es inseguro sacar el camion
				if ($heater_check == 0 || $brakes_check == 0 || $lights_check == 0 || $steering_wheel_check == 0 || $suspension_system_check == 0 || $tires_check == 0 || $wipers_check == 0 || $air_brake_check == 0 || $driver_seat_check == 0 || $fuel_system_check == 0) {

					//mensaje del correo
					$majorDefect = "<p>A major defect has been identified in the last inspecton, a driver is not legally permitted to operate the vehicle until that defect is prepared.</p>";
					$emailMsnTitle = $sendNotification ? $emailMsnTitle . $majorDefect : $majorDefect;

					if ($heater_check == 0) {
						$failsEmail .= "<br>Heater - Fail";
						$fails .= "\nHeater - Fail";
					}
					if ($brakes_check == 0) {
						$failsEmail .= "<br>Brake pedal - Fail";
						$fails .= "\nBrake pedal - Fail";
					}
					if ($lights_check == 0) {
						$failsEmail .= "<br>Lamps and reflectors - Fail";
						$fails .= "\nLamps and reflectors - Fail";
					}
					if ($steering_wheel_check == 0) {
						$failsEmail .= "<br>Steering wheel - Fail";
						$fails .= "\nSteering wheel - Fail";
					}
					if ($suspension_system_check == 0) {
						$failsEmail .= "<br>Suspension system - Fail";
						$fails .= "\nSuspension system - Fail";
					}
					if ($tires_check == 0) {
						$failsEmail .= "<br>Tires/Lug Nuts/Pressure - Fail";
						$fails .= "\nTires/Lug Nuts/Pressure - Fail";
					}
					if ($wipers_check == 0) {
						$failsEmail .= "<br>Wipers/Washers - Fail";
						$fails .= "\nWipers/Washers - Fail";
					}
					if ($air_brake_check == 0) {
						$failsEmail .= "<br>Air brake system - Fail";
						$fails .= "\nAir brake system - Fail";
					}
					if ($driver_seat_check == 0) {
						$failsEmail .= "<br>Driver and Passenger door - Fail";
						$fails .= "\nDriver and Passenger door - Fail";
					}
					if ($fuel_system_check == 0) {
						$failsEmail .= "<br>Fuel system - Fail";
						$fails .= "\nFuel system - Fail";
					}

					$subjet = $sendNotification ? $subjet . " & " : "";
					$subjet .= "Inspection with major defect";
					$sendNotification = true;
				}

				//enviar correo
				if ($sendNotification) {
					//busco datos del vehiculo
					$arrParam = array(
						"table" => "param_vehicle",
						"order" => "id_vehicle",
						"column" => "id_vehicle",
						"id" => $idVehicle
					);
					$this->load->model("general_model");
					$vehicleInfo = $this->general_model->get_basic_search($arrParam);

					$module = base64_encode("INSPECTION_LIST_BY_EQUIPMENT_ID");
					$idModule = base64_encode($idVehicle);
					$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);

					//mensaje del correo
					$emailMsn = $emailMsnTitle;
					$emailMsn .= "<strong>Make: </strong>" . $vehicleInfo[0]["make"];
					$emailMsn .= "<br><strong>Model: </strong>" . $vehicleInfo[0]["model"];
					$emailMsn .= "<br><strong>Unit Number: </strong>" . $vehicleInfo[0]["unit_number"];
					$emailMsn .= "<br><strong>Description: </strong>" . $vehicleInfo[0]["description"];
					$emailMsn .= "<br><strong>Equipment Hours/Kilometers: </strong>" . number_format($hours);
					$emailMsn .= $comments != "" ? "<br><strong>Comments: </strong>" . $comments : "";
					$emailMsn .= $failsEmail;
					$emailMsn .= "<p>Follow the link to see the list. <a href='" . $urlMovil . "' >Click here </a></p>";

					$mensaje = "<html>
									<head>
									<title> $subjet </title>
									</head>
									<body>
										<p>Dear	Administrator:</p>
										<p>$emailMsn</p>
										<p>Cordially,</p>
										<p><strong>V-CONTRACTING INC</strong></p>
									</body>
									</html>";

					//mensaje de texto
					$mensajeSMS = "Lev-West APP - " . $subjet;
					$mensajeSMS .= "\nUnit Number: " . $vehicleInfo[0]["unit_number"];
					$mensajeSMS .= $comments != "" ? "\nComments: " . $comments : "";
					$mensajeSMS .= $fails;
					$mensajeSMS .= "\n\nSee: " . $urlMovil;

					//enviar correo a Lev-West
					$arrParam = array(
						"idNotification" => ID_NOTIFICATION_INSPECTIONS,
						"subjet" => $subjet,
						"msjEmail" => $mensaje,
						"msjPhone" => $mensajeSMS
					);
					send_notification($arrParam);
				}
			}

			$data["result"] = true;
			$data["idWatertruckInspection"] = $idWatertruckInspection;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {

			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Ask for help.";
			$data["idWatertruckInspection"] = "";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Search vehicle by vin number
	 * @since 14/4/2020
	 * @author BMOTTAG
	 */
	public function search_vehicle()
	{
		$data["view"] = 'form_search_vehicle';
		$this->load->view("layout", $data);
	}

	/**
	 * Vehicle information
	 * @since 14/4/2020
	 * @author BMOTTAG
	 */
	public function vehicleInfo()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		//busco info de vehiculo
		$this->load->model("general_model");
		$arrParam = array(
			"vinNumber" => $this->input->post('vinNumber'),
			"vehicleState" => 1
		);
		$vehicleInfo = $this->general_model->get_vehicle_by($arrParam); //busco datos del vehiculo

		if ($vehicleInfo) {
			foreach ($vehicleInfo as $lista) :

				echo '<div class="panel panel-info">
						<div class="panel-heading">
							<i class="fa fa-automobile"></i> <b>INFO - </b>' . $lista['description'] . '
						</div>
						<div class="panel-body">';

				if ($lista["photo"]) {
					echo '<div class="form-group">
										<div class="row" align="center">';
					echo '<img src="' . base_url($lista["photo"]) . '" class="img-rounded" alt="Vehicle Photo" />';
					echo '</div>
									</div>';
				}

				echo '<strong>Make: </strong>' . $lista['make'] . '<br>';
				echo '<strong>Model: </strong>' . $lista['model'] . '<br>';

				echo '<strong>Description: </strong>' . $lista['description'] . '<br>';
				echo '<strong>Unit Number: </strong>' . $lista['unit_number'] . '<br>';
				echo '<strong>VIN Number: </strong>' . $lista['vin_number'] . '<br>';
				echo '<strong>Type: </strong><br>';

				switch ($lista['type_level_1']) {
					case 1:
						$type = 'Fleet';
						break;
					case 2:
						$type = 'Rental';
						break;
					case 99:
						$type = 'Other';
						break;
				}
				echo $type . " - " . $lista['type_2'];
				echo '<br>';
				$tipo = $lista['type_level_2'];
				echo "<p class='text-danger'>";
				//si es sweeper
				if ($tipo == 15) {
					echo "<strong>Truck Engine Hours: </strong>" . number_format($lista["hours"]);
					echo "<br><strong>Sweeper Engine Hours: </strong>" . number_format($lista["hours_2"]);
					//si es hydrovac
				} elseif ($tipo == 16) {
					echo "<strong>Engine Hours: </strong>" . number_format($lista["hours"]);
					echo "<br><strong>Hydraulic Pump Hours: </strong>" . number_format($lista["hours_2"]);
					echo "<br><strong>Blower Hours: </strong>" . number_format($lista["hours_3"]);
				} else {
					echo "<strong>Equipment Hours/Kilometers: </strong>" . number_format($lista["hours"]);
				}
				echo "</p>";

				$inspectionType = $lista['inspection_type'];
				$linkInspection = $lista['link_inspection'];

				if ($inspectionType == 99 || $linkInspection == "NA") {
					echo "<div class='alert alert-danger'>";
					echo "<b>No Inspection Format</b>";
					echo "</div>";
				} else {
					echo "<a class='btn btn-info btn-block' href='" . base_url('inspection/set_vehicle/' . $lista['id_vehicle']) . "'>";
					echo " Inspection Form <span class='fa fa-wrench' aria-hidden='true'>";
					echo "</a>";
				}

				echo '</div></div>';
			endforeach;
		} else {
			echo "<p class='text-danger'>There are no records with that VIN number.</p>";
		}
	}

	/**
	 * Set session with vehicle ID to do inspection
	 * @since 14/4/2020
	 * @author BMOTTAG
	 */
	public function set_vehicle($idVehicle)
	{
		//busco informacion del vehiculo
		$this->load->model("general_model");
		$arrParam['idVehicle'] = $idVehicle;
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		$sessionData = array(
			"idVehicle" => $idVehicle,
			"inspectionType" => $data['vehicleInfo'][0]['inspection_type'],
			"linkInspection" => $data['vehicleInfo'][0]['link_inspection'],
			"formInspection" => $data['vehicleInfo'][0]['form']
		);

		$this->session->set_userdata($sessionData);

		redirect($data['vehicleInfo'][0]['link_inspection'], "location", 301);
	}
}
