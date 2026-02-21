<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Programming extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("programming_model");
		$this->load->helper('form');
	}

	/**
	 * Listado de programaciones
	 * @since 15/1/2019
	 * @author BMOTTAG
	 */
	public function index($idJob, $idProgramming = 'x')
	{
		$this->load->model("general_model");
		$data['information'] = FALSE;
		$data['informationWorker'] = FALSE;
		$data['idProgramming'] = $idProgramming;
		$data['workersList'] = FALSE;
		$data['dayoffList'] = FALSE;
		$data['programmingMaterials'] = FALSE;
		$data['programmingOccasional'] = FALSE;

		$arrParamJob['idJob'] = $idJob;
		$data['jobInfo'] = $this->general_model->get_job($arrParamJob);

		//si envio el id, entonces busco la informacion 
		if ($idProgramming != 'x') {
			$arrData = array(
				"table" => "param_employee_type",
				"order" => "employee_type",
				"id" => "x"
			);
			$data['employeeTypeList'] = $this->general_model->get_basic_search($arrData); //employee type list

			$arrParam = array("idProgramming" => $idProgramming);
			$data['information'] = $this->general_model->get_programming($arrParam); //info programacion

			//lista de trabajadores para esta programacion
			$data['informationWorker'] = $this->general_model->get_programming_workers($arrParam); //info trabajadores

			//get ID of vehicules for the same day
			$equipmentSelected = $this->programming_model->get_vehicles_selected($data['information']);
			$data['informationVehicles'] = $this->programming_model->get_vehicles_inspection(["vehicleToExclude" => $equipmentSelected]);

			$data['programmingOccasional'] = $this->programming_model->get_programming_occasional($arrParam); //programming ocasional list

			$data['horas'] = $this->general_model->get_horas(); //LISTA DE HORAS

			//workers list
			$arrParam = array("state" => 1);
			$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

			$arrParamDayOff["forPlanning"] = true; //approved
			$data['dayoffList'] = $this->general_model->get_day_off_planning($arrParamDayOff);

			//si hay trabajadores reviso si ya se ha hecho la inspeccion de las maquinas
			if ($data['informationWorker']) {
				$data['memo'] = $this->verificacion($idProgramming, $data['information'][0]['date_programming']);
				$data['memo_flha'] = $this->verificacion_flha($idProgramming, $data['information'][0]['date_programming']);
				$data['memo_tool_box'] = $this->verificacion_tool_box($idProgramming, $data['information'][0]['date_programming']);
			}

			$id_job = $data['information'][0]['fk_id_job'];
			//job´s list - (active´s items)
			$arrParam = array(
				"table" => "param_jobs",
				"order" => "id_job",
				"column" => "id_job",
				"id" => $id_job
			);
			$data['job_planning'] = $this->general_model->get_basic_search($arrParam)[0]['planning_message'];
			$arrParam = array("idProgramming" => $idProgramming);
			$data['programmingMaterials'] = $this->programming_model->get_programming_materials($arrParam); //material list
		} else {
			$arrParam = array("jobId" => $idJob, "estado" => "ACTIVAS");
			$data['information'] = $this->general_model->get_programming($arrParam); //info solicitudes
		}

		$data["view"] = 'programming_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Form programming
	 * @since 15/1/2019
	 * @author BMOTTAG
	 */
	public function add_programming($idJob, $idProgramming = 'x')
	{
		$this->load->model("general_model");
		$data['information'] = FALSE;

		//job´s list - (active´s items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);

		$arrParamJob['idJob'] = $idJob;
		$data['jobInfo'] = $this->general_model->get_job($arrParamJob);

		//si envio el id, entonces busco la informacion 
		if ($idProgramming != 'x') {
			$arrParam = array("idProgramming" => $idProgramming);
			$data['information'] = $this->general_model->get_programming($arrParam); //info programacion
		}

		$data["view"] = 'form_programming';
		$this->load->view("layout", $data);
	}

	/**
	 * Guardar programacion
	 * @since 15/1/2019
	 */
	public function save_programming()
	{
		header('Content-Type: application/json');

		$idProgramming = $this->input->post('hddId');
		$idJob = $this->input->post('jobName');

		$date = ($this->input->post('date')) ? $this->input->post('date') : date('Y-m-d', strtotime($this->input->post('from')));

		$msj = "You have added a new Planning. Do not forget to asign the workers.";
		$result_project = false;
		if ($idProgramming != '') {
			$msj = "You have updated a Planning!!";
		} else {
			//verificar si ya existe el proyecto para esa fecha
			$arrParam = array(
				"idJob" => $idJob,
				"date" => $date
			);
			$result_project = $this->programming_model->verifyProject($arrParam);
		}

		if ($result_project) {
			$data["result"] = "error";

			$data["mensaje"] = " Error. This project is already scheduled for that date.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> This project is already scheduled for that date.');
		} else {

			if ($idProgramming = $this->programming_model->saveProgramming()) {
				$flagDate = $this->input->post('flag_date');
				$parentId = $this->input->post('hddIdParent');
				if ($flagDate == 2 && $parentId == "") {
					//delete previous records
					$arrParam = array(
						"table" => "programming",
						"primaryKey" => "parent_id",
						"id" => $idProgramming
					);
					$this->load->model("general_model");
					$this->general_model->deleteRecord($arrParam);

					//add new records
					$date_from = strtotime(formatear_fecha($this->input->post('from')));
					$date_to = strtotime(formatear_fecha($this->input->post('to')));

					$diferencia = $date_to - $date_from;
					$diferencia_en_dias = floor($diferencia / (60 * 60 * 24));

					for ($i = 1; $i <= $diferencia_en_dias; $i++) {
						$applyFor = $this->input->post('apply_for');
						$nextDate = date('Y-m-d', strtotime('+' . $i . ' day ', strtotime(formatear_fecha($this->input->post('from')))));
						if ($applyFor == 1) {
							$this->programming_model->savePeriodProgramming($nextDate, $idProgramming);
						} else {
							$numero_dia_semana = date("N", strtotime($nextDate));

							if ($applyFor == 2 && $numero_dia_semana >= 1 && $numero_dia_semana <= 5) {
								$this->programming_model->savePeriodProgramming($nextDate, $idProgramming);
							} elseif ($applyFor == 3 && $numero_dia_semana >= 6 && $numero_dia_semana <= 7) {
								$this->programming_model->savePeriodProgramming($nextDate, $idProgramming);
							}
						}
					}
				}
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador.');
			}
		}
		$data["path"] = $idJob . "/" . $idProgramming;
		echo json_encode($data);
	}

	/**
	 * Form Add Workers
	 * @since 16/1/2019
	 * @author BMOTTAG
	 */
	public function add_programming_workers($idProgramming)
	{
		if (empty($idProgramming)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$this->load->model("general_model");

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$data["idProgramming"] = $idProgramming;
		$data["view"] = 'form_add_workers';
		$this->load->view("layout", $data);
	}

	/**
	 * Save worker
	 * @since 16/1/2019
	 * @author BMOTTAG
	 */
	public function save_programming_workers()
	{
		header('Content-Type: application/json');
		$data = array();
		$idProgramming = $this->input->post('hddId');

		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		$data["path"] = $infoProgramming[0]["fk_id_job"] . "/" . $idProgramming;

		if ($this->programming_model->addProgrammingWorker()) {
			$data["result"] = true;
			$data["mensaje"] = "Solicitud guardada correctamente.";

			//actualizo el estado de la programacion -> dependiento si se completaron o no la cantidad de trabajadores
			$updateState = $this->update_state($idProgramming);

			$this->session->set_flashdata('retornoExito', 'You have added Workers to the Planning, if they are going to use a machine remember to assign it to the worker.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error al guardar. Intente nuevamente o actualice la p\u00e1gina.";

			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Delete programming
	 * @since 8/7/2018
	 */
	public function delete_programming()
	{
		header('Content-Type: application/json');
		$data = array();

		$idProgramming = $this->input->post('identificador');
		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		$data["path"] = $infoProgramming[0]["fk_id_job"];

		if ($this->programming_model->deleteProgramming()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', 'You have delete the record.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Update datos trabajdores
	 * @since 16/1/2019
	 * @author BMOTTAG
	 */
	public function update_worker()
	{
		$this->load->model("general_model");
		$idProgramming = $this->input->post('hddIdProgramming');
		$idProgrammingWorker = $this->input->post('hddId');

		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam); //info programacion
		$fechaProgramming = $infoProgramming[0]['date_programming'];
		$idMachine = $this->input->post('machine');

		//si envia maquina entonces buscar si existe programacion para este mismo dia para esa maquina
		$inspecciones = false;
		if ($idMachine != '') {
			$arrParam = array(
				'idProgrammingWorker' => $idProgrammingWorker,
				'fechaProgramming' => $fechaProgramming,
				'maquina' => $idMachine
			);
			$inspecciones = $this->general_model->get_programming_machine_vs_date_programming($arrParam);
		}

		if ($inspecciones) {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', 'This Equiment has already been used');
		} else {
			if ($this->programming_model->saveWorker()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the record!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
		}

		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * Generate child workers
	 * @since 21/10/2023
	 * @author BMOTTAG
	 */
	public function generate_child_workers()
	{
		$this->load->model("general_model");
		$idProgramming = $this->input->post('hddIdProgramming');

		$arrParam = array("idParent" => $idProgramming);
		$childList = $this->general_model->get_programming($arrParam);

		if (!$childList) {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', 'This Planning does not have any child.');
		} else {
			$arrParam = array("idProgramming" => $idProgramming);
			$informationWorker = $this->general_model->get_programming_workers($arrParam);

			foreach ($childList as $data) :
				$this->delete_child_workers($data['id_programming']);
				$this->programming_model->saveChildWorkers($data['id_programming'], $informationWorker);
				$this->update_state($data["id_programming"]);
			endforeach;

			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have updated the record!!");
		}

		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * Delete worker
	 */
	public function deleteWorker($idProgramming, $idWorker)
	{
		if (empty($idProgramming) || empty($idWorker)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}

		$arrParam = array(
			"table" => "programming_worker",
			"primaryKey" => "id_programming_worker",
			"id" => $idWorker
		);

		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			//actualizo el estado de la programacion -> dependiento si se completaron o no la cantidad de trabajadores
			$updateState = $this->update_state($idProgramming);

			//actualiza la WO
			$arrParam = array(
				"table" => "workorder_personal",
				"order" => "fk_id_programming_worker",
				"column" => "fk_id_programming_worker",
				"id" => $idWorker
			);
			$result = $this->general_model->get_basic_search($arrParam);
			if ($result) {
				$dataWO =
					array(
						"table" => "workorder_personal",
						"primaryKey" => "fk_id_programming_worker",
						"id" => $idWorker
					);

				$this->general_model->deleteRecord($dataWO);
			}


			$this->session->set_flashdata('retornoExito', 'You have deleted one worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * Envio de mensaje
	 * @since 16/1/2019
	 * @author BMOTTAG
	 */
	public function send($idProgramming, $flashPlanning = false)
	{
		$this->load->model("general_model");
		$this->load->library('encrypt');
		if (!class_exists('Twilio\Rest\Client')) {
			require 'vendor/Twilio/autoload.php';
		}

		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$parametric = $this->general_model->get_basic_search($arrParam);
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);
		$twilioPhone = $parametric[5]["value"];

		$client = new Twilio\Rest\Client($dato1, $dato2);

		$data['informationWorker'] = FALSE;
		$data['idProgramming'] = $idProgramming;

		$arrParam = array(
			"idProgramming" => $idProgramming
		);
		$data['information'] = $this->general_model->get_programming($arrParam); //info programacion

		$idWorkorder = $data['information'][0]['fk_id_workorder'] ?: $this->create_work_order($data['information']);

		//lista de trabajadores para esta programacion
		$copiaInfoWorker = $data['informationWorker'] = $this->general_model->get_programming_workers($arrParam); //info trabajadores

		$msgHeader = "";
		$msgHeader .= date('F j, Y', strtotime($data['information'][0]['date_programming']));
		$msgHeader .= "\n" . $data['information'][0]['job_description'];
		$msgHeader .= "\n" . $data['information'][0]['observation'];
		$msgHeader .= "\n";
		$msgHeader .= "\nPlease confirm by replying '1' to this text message!";
		$msgHeader .= "\n";

		if ($data['informationWorker']) {
			foreach ($data['informationWorker'] as $info) :

				if ($info['fk_id_machine'] != NULL) {
					$id_values = implode(',', json_decode($info['fk_id_machine'], true));
					$arrParam = array(
						"idValues" => $id_values,
						"forTextMessague" => true
					);
					$informationEquipments = $this->general_model->get_vehicle_info_for_planning($arrParam);
				}

				$mensaje = $msgHeader . "\n";
				switch ($info['site']) {
					case 1:
						$mensaje .= "At the yard - ";
						break;
					case 2:
						$mensaje .= "At the site - ";
						break;
					case 3:
						$mensaje .= "At Terminal - ";
						break;
					case 4:
						$mensaje .= "On-line training - ";
						break;
					case 5:
						$mensaje .= "At training facility - ";
						break;
					case 6:
						$mensaje .= "At client's office - ";
						break;
					default:
						$mensaje .= "At the yard - ";
						break;
				}
				$mensaje .= $info['hora'];

				$mensaje .= "\n" . $info['name'];
				$mensaje .= $info['description'] ? "\n" . $info['description'] : "";
				$mensaje .= $info['fk_id_machine'] != NULL ? "\nInspect following unit(s):\n" . $informationEquipments["unit_description"] : "";

				if ($info['safety'] == 1) {
					$mensaje .= "\nFLHA has being assigned to you.";
				} elseif ($info['safety'] == 2) {
					$mensaje .= "\nIHSR has being assigned to you.";
				} elseif ($info['safety'] == 3) {
					$mensaje .= "\nJSO has being assigned to you.";
				}

				if ($info['creat_wo'] == 1) {
					$mensaje .= "\nYou are in charge of the W.O. #" . $idWorkorder;
				}

				$excluded_numbers = ["686289126", "5068494482", "5068393681"];
				if (!in_array($info['movil'], $excluded_numbers)) {
					$to = '+1' . $info['movil'];
					$message = $client->messages->create(
						$to,
						array(
							'from' => $twilioPhone,
							'body' => $mensaje
						)
					);
					$this->programming_model->updateSMSWorkerStatus($info['id_programming_worker'], $message->status, $message->sid);	
				}

			endforeach;
		}

		if ($flashPlanning) {
			return true;
		} else {
			$data['linkBack'] = "programming/index/" . $data['information'][0]["fk_id_job"] . "/". $idProgramming;
			$data['titulo'] = "<i class='fa fa-list'></i> PLANNING LIST";

			$data['clase'] = "alert-info";
			$data['msj'] = "The message has been sent to the workers.";

			$data["view"] = 'template/answer';
			$this->load->view("layout", $data);
		}
	}

	/**
	 * Actualizo estado de la programacion 2 si esta completa 1 si esta incompleta
	 * @since 17/1/2019
	 */
	function update_state($idProgramming)
	{
		//programming info
		$this->load->model("general_model");

		//cuento la cantidad de trabajadores guardados en la base de datos
		$countWorkers = $this->programming_model->countWorkers($idProgramming);

		$state = 1; //incompleta
		if ($countWorkers >= 1) {
			$state = 2; //completa
		}

		//guardo estado de la programacion			
		$arrParam = array(
			"table" => "programming",
			"primaryKey" => "id_programming",
			"id" => $idProgramming,
			"column" => "state",
			"value" => $state
		);

		if ($this->general_model->updateRecord($arrParam)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Actualizo estado de los mensajes.
	 * $nuevoEstoadoSMS = 0 no enviado; 1 enviado al empleado; 2 enviado al administrador
	 * $tipoSMS = columna de la base de datos: sms_inspection; sms_safety
	 * @since 17/1/2019
	 */
	function update_sms_worker($idProgrammingWorker, $columnaTipoSMS, $nuevoEstoadoSMS)
	{
		//programming info
		$this->load->model("general_model");

		//guardo estado de la programacion			
		$arrParam = array(
			"table" => "programming_worker",
			"primaryKey" => "id_programming_worker",
			"id" => $idProgrammingWorker,
			"column" => $columnaTipoSMS,
			"value" => $nuevoEstoadoSMS
		);

		if ($this->general_model->updateRecord($arrParam)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * CRON
	 * Verificar para la fecha actual si existen maquinas asignadas y si se les hizo inspeccion
	 * El CRON se corre cada hora pero se le sumo 3 horas mas para que nosea inmediatamente empiece
	 * @since 17/1/2019
	 */
	function verificacion($idProgramming = 'x', $fecha = 'x')
	{
		$this->load->model("general_model");
		$bandera = false;

		if ($fecha != 'x') {
			$fechaBusqueda = $fecha;
			$arrParam = array("idProgramming" => $idProgramming, "fecha" => $fechaBusqueda);
		} else {
			$fechaBusqueda = date("Y-m-d");
			$bandera = true;
			$arrParam = array("fecha" => $fechaBusqueda);
		}

		$information = $this->general_model->get_programming($arrParam); //info programacion

		$i = 0;
		$nombres = "";

		if ($information) {

			//inicio configuracion
			if ($bandera) {
				$this->load->library('encrypt');
				require 'vendor/Twilio/autoload.php';

				//busco datos parametricos twilio
				$arrParam = array(
					"table" => "parametric",
					"order" => "id_parametric",
					"id" => "x"
				);
				$parametric = $this->general_model->get_basic_search($arrParam);
				$dato1 = $this->encrypt->decode($parametric[3]["value"]);
				$dato2 = $this->encrypt->decode($parametric[4]["value"]);
				$phone = $parametric[5]["value"];
				$phoneAdmin = $parametric[6]["value"];

				$client = new Twilio\Rest\Client($dato1, $dato2);
			}
			//fin configuracion

			//para cada programacion buscar los trabajadores que tienen maquinas asignadas
			foreach ($information as $lista) :
				//lista de trabajadores para esta programacion que tiene maquinas asignadas
				$arrParam = array("idProgramming" => $lista['id_programming'], "withEquipment" => TRUE);
				$informationWorker = $this->general_model->get_programming_workers($arrParam); //info trabajadores

				if ($informationWorker) {
					//busco para la fecha y para esa maquina si hay inspecciones
					//si no hay inspecciones envio mensaje de alerta
					foreach ($informationWorker as $dato) :

						$inspecciones = false;
						if (!empty(json_decode($dato['fk_id_machine']))) {
							$arrParam = array("fecha" => $fechaBusqueda, "maquina" => $dato['fk_id_machine']);
							$inspecciones = $this->general_model->get_missing_programming_inspecciones($arrParam); //ID de maquinas sin inspeccion
							if ($inspecciones) {
								$arrParam = array(
									"idValues" => implode(",", $inspecciones)
								);
								if ($bandera) {
									$arrParam["forTextMessague"] = true;
								}
								$inspeccionesValues = $this->general_model->get_vehicle_info_for_planning($arrParam);

								if ($inspeccionesValues) {
									$i++;
									$nombres .= "<br>" . $dato['name'] . " - " . $inspeccionesValues["unit_description"];

									//ENVIO MENSAJE DE TEXTO
									if ($bandera && $dato['sms_inspection'] != 2) {

										//buscar si ya empezo la hora laboral del trabajador
										$fechaProgramacion = $fechaBusqueda . " " . $dato['formato_24'];

										$datetime1 = date_create($fechaProgramacion);


										//$datetime2 = date_create(date('Y-m-d G:i'));//fecha actual


										$ajuste = strtotime('-2 hour', strtotime(date("Y-m-d G:i"))); //le resto 2 horas a la hora actual
										$datetime2 = date_create(date("Y-m-d G:i", $ajuste));

										//si ya empezo a trabajar y no se le ha enviado mensaje, entonces le envio sms
										if ($datetime1 < $datetime2) {
											//si no le ha enviado sms entonces lo envio
											if ($dato['sms_inspection'] == 0) {
												//actualizo la bandera sms_inspection a 1
												$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_inspection", 1);

												$to = '+1' . $dato['movil'];

												$mensaje = "INSPECTION APP-Lev-West";
												$mensaje .= "\nDo not forget to do the Inspection:";
												$mensaje .= "\n" . $inspeccionesValues["unit_description"];

												// Use the client to do fun stuff like send text messages!
												$message = $client->messages->create(
													// the number you'd like to send the message to
													$to,
													array(
														// A Twilio phone number you purchased at twilio.com/console
														'from' => $phone,
														'body' => $mensaje
													)
												);
											}
											//si ya se le evio al trabajador entonces se envio al ADMIN
											if ($dato['sms_inspection'] == 1) {
												//actualizo la bandera sms_inspection a 2
												$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_inspection", 2);

												//$to = '+1' . $phoneAdmin;
												$to = '+14034089921'; // . $phoneAdmin;

												$mensaje = "INSPECTION APP-Lev-West";
												$mensaje .= "\nThe user has not done the Inspection:";
												$mensaje .= "\n" . $dato['name'] . " - " . $inspeccionesValues["unit_description"];

												// Use the client to do fun stuff like send text messages!
												$message = $client->messages->create(
													// the number you'd like to send the message to
													$to,
													array(
														// A Twilio phone number you purchased at twilio.com/console
														'from' => $phone,
														'body' => $mensaje
													)
												);
											}
										}
									}
									//FIN MENSAJE DE TEXTO

								}
							}
						}
					endforeach;
				}

			endforeach;
		}

		if ($i != 0) {
			$memo =  "INSPECTIONS missing:";
			$memo .= $nombres;
		} else {
			$memo = "There is no INSPECTIONS missing.";
		}

		return $memo;
	}

	public function calendar()
	{
		$this->load->model("general_model");

		$arrParam = array("estado" => "ACTIVAS");
		$data['information'] = $this->general_model->get_programming($arrParam); //info solicitudes

		$data["view"] = 'calendar';
		$this->load->view("layout", $data);
	}

	/**
	 * Save one worker
	 */
	public function save_One_Worker_programming()
	{
		$idProgramming = $this->input->post('hddId');

		if ($this->programming_model->saveOneWorkerProgramming()) {
			$this->session->set_flashdata('retornoExito', 'You have added one Worker.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * CRON
	 * Verificar para la fecha actual si existen FLHA asignadas y si se hizo el FLHA (1) 
	 * El CRON se corre a las 10:30 AM de todos los dias
	 * @since 17/1/2019
	 */
	function verificacion_flha($idProgramming = 'x', $fecha = 'x')
	{
		$this->load->model("general_model");
		$bandera = false;

		if ($fecha != 'x') {
			$fechaBusqueda = $fecha;
			$arrParam = array("idProgramming" => $idProgramming, "fecha" => $fechaBusqueda);
		} else {
			$fechaBusqueda = date("Y-m-d");
			$bandera = true;
			$arrParam = array("fecha" => $fechaBusqueda);
		}
		$information = $this->general_model->get_programming($arrParam); //info programacion

		$i = 0;
		$j = 0;
		$nombres = "";
		$nombresJSO = "";

		if ($information) {

			//inicio configuracion
			if ($bandera) {
				$this->load->library('encrypt');
				require 'vendor/Twilio/autoload.php';

				//busco datos parametricos twilio
				$arrParam = array(
					"table" => "parametric",
					"order" => "id_parametric",
					"id" => "x"
				);
				$parametric = $this->general_model->get_basic_search($arrParam);
				$dato1 = $this->encrypt->decode($parametric[3]["value"]);
				$dato2 = $this->encrypt->decode($parametric[4]["value"]);
				$phone = $parametric[5]["value"];
				$phoneAdmin = $parametric[6]["value"];

				$client = new Twilio\Rest\Client($dato1, $dato2);
			}
			//fin configuracion

			//para cada programacion buscar los trabajadores que tienen FLHA
			foreach ($information as $lista) :
				//lista de trabajadores para esta programacion que tiene FLHA
				$arrParam = array("idProgramming" => $lista['id_programming'], "safety" => 1);
				$informationWorkerFLHA = $this->general_model->get_programming_workers($arrParam); //info trabajadores con FLHA

				if ($informationWorkerFLHA) {
					//busco para la fecha, para ese JOB CODE si hay FLHA
					//si no hay FLHA envio mensaje de alerta
					foreach ($informationWorkerFLHA as $dato) :
						$arrParam = array(
							"fecha" => $fechaBusqueda,
							"idJob" => $lista['fk_id_job'],
							"limit" => 30
						);
						$inspecciones = $this->general_model->get_safety($arrParam);

						if (!$inspecciones) {
							$i++;
							$nombres .= "<br>" . $dato['name'] . " - Missing FLHA";
							//ENVIO MENSAJE DE TEXTO
							if ($bandera && $dato['sms_safety'] != 2) {

								//buscar si ya empezo la hora laboral del trabajador
								$fechaProgramacion = $fechaBusqueda . " " . $dato['formato_24'];

								$datetime1 = date_create($fechaProgramacion);
								//$datetime2 = date_create(date('Y-m-d G:i'));//fecha actual


								$ajuste = strtotime('-2 hour', strtotime(date("Y-m-d G:i"))); //le resto 2 horas a la hora actual
								$datetime2 = date_create(date("Y-m-d G:i", $ajuste));

								//si ya empezo a trabajar y no se le ha enviado mensaje, entonces le envio sms
								if ($datetime1 < $datetime2) {

									//si no le ha enviado sms entonces lo envio
									if ($dato['sms_safety'] == 0) {
										//actualizo la bandera sms_safety a 1
										$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_safety", 1);

										$to = '+1' . $dato['movil'];

										$mensaje = "FLHA APP-Lev-West";
										$mensaje .= "\nDo not forget to do the FLHA:";
										$mensaje .= "\n" . $lista['job_description'];

										// Use the client to do fun stuff like send text messages!
										$message = $client->messages->create(
											// the number you'd like to send the message to
											$to,
											array(
												// A Twilio phone number you purchased at twilio.com/console
												'from' => $phone,
												'body' => $mensaje
											)
										);
									}
									//si ya se le evio al trabajador entonces se envio al ADMIN
									elseif ($dato['sms_safety'] == 1) {
										//actualizo la bandera sms_inspection a 2
										$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_safety", 2);

										$to = '+1' . $phoneAdmin;

										$mensaje = "FLHA APP-Lev-West";
										$mensaje .= "\nThe user has not done the FLHA:";
										$mensaje .= "\n" . $dato['name'];

										// Use the client to do fun stuff like send text messages!
										$message = $client->messages->create(
											// the number you'd like to send the message to
											$to,
											array(
												// A Twilio phone number you purchased at twilio.com/console
												'from' => $phone,
												'body' => $mensaje
											)
										);
									}
								}
							}
							//FIN MENSAJE DE TEXTO

						}

					//echo $this->db->last_query(); exit;

					endforeach;
				}

				$arrParam = array("idProgramming" => $lista['id_programming'], "safety" => 3);
				$informationWorkerJSO = $this->general_model->get_programming_workers($arrParam); //info trabajadores con JSO

				if ($informationWorkerJSO) {
					//busco para la fecha, para ese JOB CODE si hay JSO
					//si no hay JSO envio mensaje de alerta
					foreach ($informationWorkerJSO as $dato) :
						$arrParam = array(
							"fecha" => $fechaBusqueda,
							"idJob" => $lista['fk_id_job'],
							"limit" => 30
						);
						$inspecciones = $this->general_model->get_safety($arrParam);

						if (!$inspecciones) {
							$j++;
							$nombresJSO .= "<br>" . $dato['name'] . " - Missing JSO";
							//ENVIO MENSAJE DE TEXTO
							if ($bandera && $dato['sms_jso'] != 2) {

								//buscar si ya empezo la hora laboral del trabajador
								$fechaProgramacion = $fechaBusqueda . " " . $dato['formato_24'];

								$datetime1 = date_create($fechaProgramacion);
								//$datetime2 = date_create(date('Y-m-d G:i'));//fecha actual

								$ajuste = strtotime('-2 hour', strtotime(date("Y-m-d G:i"))); //le resto 2 horas a la hora actual
								$datetime2 = date_create(date("Y-m-d G:i", $ajuste));

								//si ya empezo a trabajar y no se le ha enviado mensaje, entonces le envio sms
								if ($datetime1 < $datetime2) {

									//si no le ha enviado sms entonces lo envio
									if ($dato['sms_jso'] == 0) {
										//actualizo la bandera sms_jso a 1
										$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_jso", 1);

										$to = '+1' . $dato['movil'];

										$mensaje = "JSO APP-Lev-West";
										$mensaje .= "\nDo not forget to do the JSO:";
										$mensaje .= "\n" . $lista['job_description'];

										// Use the client to do fun stuff like send text messages!
										$message = $client->messages->create(
											// the number you'd like to send the message to
											$to,
											array(
												// A Twilio phone number you purchased at twilio.com/console
												'from' => $phone,
												'body' => $mensaje
											)
										);
									}
									//si ya se le evio al trabajador entonces se envio al ADMIN
									elseif ($dato['sms_jso'] == 1) {
										//actualizo la bandera sms_inspection a 2
										$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_jso", 2);

										$to = '+1' . $phoneAdmin;

										$mensaje = "JSO APP-Lev-West";
										$mensaje .= "\nThe user has not done the JSO:";
										$mensaje .= "\n" . $dato['name'];

										// Use the client to do fun stuff like send text messages!
										$message = $client->messages->create(
											// the number you'd like to send the message to
											$to,
											array(
												// A Twilio phone number you purchased at twilio.com/console
												'from' => $phone,
												'body' => $mensaje
											)
										);
									}
								}
							}
							//FIN MENSAJE DE TEXTO
						}

					//echo $this->db->last_query(); exit;

					endforeach;
				}

			endforeach;
		}

		if ($i != 0) {
			$memo =  "Missing FLHA:";
			$memo .= $nombres;
		} else if ($i == 0) {
			$memo = "There is no FLHA missing";
		}

		if ($j != 0) {
			$memo =  ", Missing JSO:";
			$memo .= $nombresJSO;
		} else if ($j == 0) {
			$memo = "There is no JSO missing";
		}

		return $memo;
	}

	/**
	 * CRON
	 * Verificar para la fecha actual si existen TOOL BOX asignadas y si se hizo el TOOL BOX (2) 
	 * El CRON se corre a las 10:15 AM de todos los dias
	 * @since 20/1/2019
	 */
	function verificacion_tool_box($idProgramming = 'x', $fecha = 'x')
	{
		$this->load->model("general_model");
		$bandera = false;

		if ($fecha != 'x') {
			$fechaBusqueda = $fecha;
			$arrParam = array("idProgramming" => $idProgramming, "fecha" => $fechaBusqueda);
		} else {
			$fechaBusqueda = date("Y-m-d");
			$bandera = true;
			$arrParam = array("fecha" => $fechaBusqueda);
		}

		$information = $this->general_model->get_programming($arrParam); //info programacion

		$i = 0;
		$nombres = "";

		if ($information) {

			//inicio configuracion			
			if ($bandera) {
				$this->load->library('encrypt');
				require 'vendor/Twilio/autoload.php';

				//busco datos parametricos twilio
				$arrParam = array(
					"table" => "parametric",
					"order" => "id_parametric",
					"id" => "x"
				);
				$parametric = $this->general_model->get_basic_search($arrParam);
				$dato1 = $this->encrypt->decode($parametric[3]["value"]);
				$dato2 = $this->encrypt->decode($parametric[4]["value"]);
				$phone = $parametric[5]["value"];
				$phoneAdmin = $parametric[6]["value"];

				$client = new Twilio\Rest\Client($dato1, $dato2);
			}
			//fin configuracion

			//para cada programacion buscar los trabajadores que tienen TOOL BOX
			foreach ($information as $lista) :
				//lista de trabajadores para esta programacion que tiene TOOL BOX
				$arrParam = array("idProgramming" => $lista['id_programming'], "safety" => 2);
				$informationWorker = $this->general_model->get_programming_workers($arrParam); //info trabajadores con TOOL BOX

				if ($informationWorker) {
					//busco para la fecha, para ese JOB CODE si hay TOOL BOX
					//si no hay TOOL BOX envio mensaje de alerta
					foreach ($informationWorker as $dato) :
						$arrParam = array(
							"fecha" => $fechaBusqueda,
							"idJob" => $lista['fk_id_job']
						);
						$inspecciones = $this->general_model->get_tool_box($arrParam);

						if (!$inspecciones) {
							$i++;
							$nombres .= "<br>" . $dato['name'] . " - Missing IHSR";
							//ENVIO MENSAJE DE TEXTO
							if ($bandera && $dato['sms_safety'] != 2) {

								//buscar si ya empezo la hora laboral del trabajador
								$fechaProgramacion = $fechaBusqueda . " " . $dato['formato_24'];

								$datetime1 = date_create($fechaProgramacion);
								//$datetime2 = date_create(date('Y-m-d G:i'));//fecha actual


								$ajuste = strtotime('-2 hour', strtotime(date("Y-m-d G:i"))); //le resto 2 horas a la hora actual
								$datetime2 = date_create(date("Y-m-d G:i", $ajuste));

								//si ya empezo a trabajar y no se le ha enviado mensaje, entonces le envio sms
								if ($datetime1 < $datetime2) {

									//si no le ha enviado sms entonces lo envio
									if ($dato['sms_safety'] == 0) {
										//actualizo la bandera sms_safety a 1
										$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_safety", 1);

										$to = '+1' . $dato['movil'];

										$mensaje = "IHSR APP-Lev-West";
										$mensaje .= "\nDo not forget to do the IHSR:";
										$mensaje .= "\n" . $lista['job_description'];

										// Use the client to do fun stuff like send text messages!
										$message = $client->messages->create(
											// the number you'd like to send the message to
											$to,
											array(
												// A Twilio phone number you purchased at twilio.com/console
												'from' => $phone,
												'body' => $mensaje
											)
										);
									}
									//si ya se le evio al trabajador entonces se envio al ADMIN
									elseif ($dato['sms_safety'] == 1) {
										//actualizo la bandera sms_inspection a 2
										$updateState = $this->update_sms_worker($dato['id_programming_worker'], "sms_safety", 2);

										$to = '+1' . $phoneAdmin;

										$mensaje = "IHSR APP-Lev-West";
										$mensaje .= "\nThe user has not done the IHSR:";
										$mensaje .= "\n" . $dato['name'];

										// Use the client to do fun stuff like send text messages!
										$message = $client->messages->create(
											// the number you'd like to send the message to
											$to,
											array(
												// A Twilio phone number you purchased at twilio.com/console
												'from' => $phone,
												'body' => $mensaje
											)
										);
									}
								}
							}
							//FIN MENSAJE DE TEXTO

						}

					//echo $this->db->last_query(); exit;

					endforeach;
				}
			//pr($informationWorker);

			endforeach;
		}

		if ($i != 0) {
			$memo =  "Missing IHSR:";
			$memo .= $nombres;
		} else {
			$memo = "There is no IHSR missing";
		}

		return $memo;
	}

	/**
	 * Form Flash Planning
	 * @since 28/12/2022
	 * @author BMOTTAG
	 */
	public function flash_planning()
	{
		$this->load->model("general_model");
		$data['information'] = FALSE;
		$arrToExclude = array();
		$data['informationVehicles'] = $this->programming_model->get_vehicles_inspection($arrToExclude);

		//jobs list - (active items)
		$arrParam = array(
			"table" => "param_jobs",
			"order" => "job_description",
			"column" => "state",
			"id" => 1
		);
		$data['jobs'] = $this->general_model->get_basic_search($arrParam);

		//workers list
		$arrParam = array("state" => 1);
		$data['workersList'] = $this->general_model->get_user($arrParam); //workers list

		$data["view"] = 'form_planning_flash';
		$this->load->view("layout", $data);
	}

	/**
	 * Save Flash Planning
	 * @since 28/12/2022
	 */
	public function save_flash_planning()
	{
		header('Content-Type: application/json');

		$this->load->model("general_model");
		$idProgramming = $this->input->post('hddId');
		$msj = "You have added a new Planning.";
		if ($idProgramming != '') {
			$msj = "You have updated a Planning!!";
		}

		$horas = $this->general_model->get_horas(); //LISTA DE HORAS
		$horaActual = date("G:i");

		$idHora = "";
		foreach ($horas as $hora) :
			$hora1 = strtotime($hora["formato_24"]);
			$hora2 = strtotime($horaActual);

			if ($hora1 > $hora2) {
				$idHora = $hora["id_hora"];
				break;
			}
		endforeach;

		if ($idProgramming = $this->programming_model->saveProgramming()) {
			//Save worker
			if ($this->programming_model->saveWorkerFashPlanning($idProgramming, $idHora)) {
				//envio mensaje de texto
				$this->send($idProgramming, true);
				$msj .= " Message is sent to the worker.";
			}
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador.');
		}

		$idJob = $this->input->post('jobName');
		$data["path"] = $idJob . "/" . $idProgramming;

		echo json_encode($data);
	}

	/**
	 * Receive SMS
	 * @since 27/8/2023
	 * @author BMOTTAG
	 */
	public function receive_sms()
	{
		$twilio_signature = $_SERVER['HTTP_X_TWILIO_SIGNATURE'];
		$twilio_post_data = file_get_contents('php://input');

		$post_data = $_POST;
		// Get the incoming message and sender's phone number
		$incoming_message = $post_data['Body'];
		$sender_number = $post_data['From'];

		header("Content-Type: text/xml");
		// Process the response
		$arrParam = array("movil" => str_replace("+1", "", $sender_number));
		$this->load->model("general_model");
		if ($informationWorker = $this->general_model->get_programming_user($arrParam)) {
			if ($incoming_message === '1') {
				$arrParam = array(
					"table" => "programming_worker",
					"primaryKey" => "id_programming_worker",
					"id" => $informationWorker["id_programming_worker"],
					"column" => "confirmation",
					"value" => 1
				);

				if ($this->general_model->updateRecord($arrParam)) {
					echo "<Response><Message>Thank you for your response!</Message></Response>";

					$this->send_confirmation($informationWorker['employee'], $informationWorker['date_programming'], $informationWorker['hora'], $informationWorker['movil']);
				}
			} else {
				echo "<Response><Message>The confirmation should be sent by replying with the number 1.</Message></Response>";
			}
		}
	}

	/**
	 * Send confirmation to supervisor
	 * @since 29/08/2023
	 */
	function send_confirmation($employee, $dateProgramming, $hora, $movil)
	{
		$this->load->library('encrypt');
		require 'vendor/Twilio/autoload.php';
		//busco datos parametricos twilio
		$arrParam = array(
			"table" => "parametric",
			"order" => "id_parametric",
			"id" => "x"
		);
		$parametric = $this->general_model->get_basic_search($arrParam);
		$dato1 = $this->encrypt->decode($parametric[3]["value"]);
		$dato2 = $this->encrypt->decode($parametric[4]["value"]);
		$twilioPhone = $parametric[5]["value"];

		$client = new Twilio\Rest\Client($dato1, $dato2);

		$mensaje = "Lev-West APP - Planning";
		$mensaje .= "\n\n" . $employee . " confirmed the plan for " . $dateProgramming  . " at " . $hora .  ".";

		$to = '+1' . $movil;
		$client->messages->create(
			$to,
			array(
				'from' => $twilioPhone,
				'body' => $mensaje
			)
		);

		return true;
	}

	/**
	 * Delete child workers whene generate the workers information
	 * @since 21/10/2023
	 */
	function delete_child_workers($idProgramming)
	{
		//programming info
		$this->load->model("general_model");

		$arrParam = array(
			"table" => "programming_worker",
			"primaryKey" => "fk_id_programming",
			"id" => $idProgramming
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * CRON
	 * Emvio aotomatico de programacion 
	 * El CRON se corre a las 12:00 PM de todos los dias
	 * @since 22/10/2023
	 */
	function automatic_planning_message()
	{
		$this->load->model("general_model");
		$nextDate = date('Y-m-d', strtotime('+1 day ', strtotime(date("Y-m-d"))));
		$arrParam = array(
			"fecha" => $nextDate,
			"estado" => "ACTIVAS",
			"smsAutomatic" => true
		);
		$information = $this->general_model->get_programming($arrParam); //info programacion
		if ($information) {
			foreach ($information as $lista) :
				$this->send($lista["id_programming"], true);
			endforeach;
		}
	}

	/**
	 * Clone Planning
	 * @since 28/10/2023
	 * @author BMOTTAG
	 */
	public function clone_planning()
	{
		header('Content-Type: application/json');

		$idProgramming = $this->input->post('hddIdProgramming');
		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoPlanning = $this->general_model->get_programming($arrParam);

		if ($idProgrammingClone = $this->programming_model->createClone($infoPlanning)) {
			$arrParam = array("idProgramming" => $idProgramming);
			$informationWorker = $this->general_model->get_programming_workers($arrParam);

			//insert workers to the clone planning
			$this->programming_model->saveChildWorkers($idProgrammingClone, $informationWorker);
			$this->update_state($idProgrammingClone);

			$data["result"] = true;
		} else {
			$data["result"] = "error";
		}
		echo json_encode($data);
	}

	/**
	 * Cargo modal- formulario de captura Material
	 * @since 20/1/2024
	 */
	public function loadModalMaterials()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idProgramming = $this->input->post("idProgramming");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idProgramming);
		$data["idProgramming"] = $porciones[1];

		//workers list
		$this->load->model("general_model");
		$arrParam = array(
			"table" => "param_material_type",
			"order" => "material",
			"id" => "x"
		);
		$data['materialList'] = $this->general_model->get_basic_search($arrParam); //worker´s list

		$this->load->view("modal_material", $data);
	}

	/**
	 * Save material
	 * @since 20/1/2024
	 * @author BMOTTAG
	 */
	public function save_material()
	{
		header('Content-Type: application/json');
		$data = array();

		$idProgramming = $this->input->post('hddidProgramming');

		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		$data["path"] = $infoProgramming[0]["fk_id_job"] . "/" . $idProgramming;

		if ($this->programming_model->saveMaterial()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		$data["controller"] = "index";

		echo json_encode($data);
	}

	/**
	 * Delete workorder record
	 */
	public function deleteMaterial($idProgrammingMaterial, $fk_id_programming)
	{
		if (empty($idProgrammingMaterial) || empty($fk_id_programming)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "programming_material",
			"primaryKey" => "id_programming_material",
			"id" => $idProgrammingMaterial
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {

			//actualiza la WO
			$arrParam = array(
				"table" => "workorder_materials",
				"order" => "fk_id_programming_materials",
				"column" => "fk_id_programming_materials",
				"id" => $idProgrammingMaterial
			);
			$result = $this->general_model->get_basic_search($arrParam);
			if ($result) {
				$dataWO =
					array(
						"table" => "workorder_materials",
						"primaryKey" => "fk_id_programming_materials",
						"id" => $idProgrammingMaterial
					);

				$this->general_model->deleteRecord($dataWO);
			}

			$this->session->set_flashdata('retornoExito', 'You have deleted one record from <strong> Materials </strong> table.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		$arrParam = array("idProgramming" => $fk_id_programming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $fk_id_programming), 'refresh');
	}

	public function updated_material()
	{
		$idProgramming = $this->input->post('hddidProgramming');

		if ($this->programming_model->updatedMaterial()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * Create work order form planning
	 * @since 23/1/2023
	 * @author BMOTTAG
	 */
	public function create_work_order($infoPlanning)
	{
		$idProgramming = $infoPlanning[0]["id_programming"];
		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$informationWorker = $this->general_model->get_programming_workers($arrParam); //info trabajadores

		$informationWorkerWithEquipment = $this->general_model->get_programming_equipment($arrParam); //info trabajadores con maquinas

		$programmingMaterials = $this->programming_model->get_programming_materials($arrParam); //material list

		$programmingSubcontractor = $this->programming_model->get_programming_occasional($arrParam); //Subcontractor list

		/*
		$arrParamHauling = array(
			"table" => "hauling",
			"order" => "fk_id_programming",
			"column" => "fk_id_programming",
			"id" => $idProgramming
		);
		$programmingHauling = $this->general_model->get_basic_search($arrParamHauling); //Hauling list
*/
		$arrParam = array(
			"idProgramming" => $idProgramming,
			"createWO" => TRUE
		);
		$informationWorkerWO = $this->general_model->get_programming_workers($arrParam); //info trabajado con WO
		$idUser = $informationWorkerWO ? $informationWorkerWO[0]["fk_id_programming_user"] :  $infoPlanning[0]["fk_id_user"];

		//Get info foreman if exist
		$idJob = $infoPlanning[0]["fk_id_job"];
		$idCompany = $infoPlanning[0]["id_company"];

		$data = [
			"foreman_name" => "",
			"foreman_movil" => "",
			"foreman_email" => "",
		];

		// Intentamos encontrar foreman por trabajo
		$infoForeman = $this->getForemanData("param_company_foreman", "id_company_foreman", "fk_id_job", $idJob);
		if (!$infoForeman && $idCompany > 0) {
			// Si no se encontró por trabajo, buscamos por empresa
			$infoForeman = $this->getForemanData("param_company_foreman", "id_company_foreman", "fk_id_param_company", $idCompany);
		}

		// Si encontramos foreman, asignamos los datos
		if ($infoForeman) {
			$data["foreman_name"] = $infoForeman["foreman_name"];
			$data["foreman_movil"] = $infoForeman["foreman_movil_number"];
			$data["foreman_email"] = $infoForeman["foreman_email"];
		}

		$message = "A new Work Order was created from the Planning.";
		$arrParam = array(
			"idUser" => $idUser,
			"idJob" => $idJob,
			"date" => $infoPlanning[0]["date_programming"],
			'idCompany' => $idCompany,
			'foremanName' => $data["foreman_name"],
			'foremanMovil' => $data["foreman_movil"],
			'foremanEmail' => $data["foreman_email"],
			"observation" => $infoPlanning[0]["observation"],
			"message" => $message
		);

		if ($idWorkorder = $this->programming_model->add_workorder($arrParam)) {

			//guardo el ID de la WO en la tabla de la programacion
			$arrParam = array(
				"table" => "programming",
				"primaryKey" => "id_programming",
				"id" => $idProgramming,
				"column" => "fk_id_workorder",
				"value" => $idWorkorder
			);
			$this->general_model->updateRecord($arrParam);

			//guardo el primer estado de la workorder
			$arrParam = array(
				"idUser" => $infoPlanning[0]["fk_id_user"],
				"idWorkorder" => $idWorkorder,
				"observation" => $message,
				"state" => 0
			);
			$this->programming_model->add_workorder_state($arrParam);

			//save workers info
			if ($informationWorker) {
				$columnas_mapeo = array(
					'fk_id_programming_user' => 'fk_id_user',
					'fk_id_employee_type' => 'fk_id_employee_type',
					'description' => 'description',
					'id_programming_worker' => 'fk_id_programming_worker'
				);

				foreach ($informationWorker as $indice => $datos_indice) {
					$datos_formateados = array();

					$datos_formateados["fk_id_workorder"] = $idWorkorder;
					$datos_formateados["hours"] = 0;
					foreach ($datos_indice as $columna => $valor) {
						if (isset($columnas_mapeo[$columna])) {
							$columna_destino = $columnas_mapeo[$columna];
							
							// Si es fk_id_employee_type y viene vacío, poner 1
							if ($columna_destino == 'fk_id_employee_type' && (empty($valor) || is_null($valor))) {
								$valor = 1;
							}
				
							$datos_formateados[$columna_destino] = $valor;
						}
					}
					$table = 'workorder_personal';
					$this->programming_model->add_item_workorder($table, $datos_formateados);
				}				
			}

			//save equipment info
			if ($informationWorkerWithEquipment) {
				$columnas_mapeo = array(
					'type_level_2' => 'fk_id_type_2',
					'id_vehicle' => 'fk_id_vehicle',
					'fk_id_programming_user' => 'operatedby',
					'description' => 'description',
				);

				foreach ($informationWorkerWithEquipment as $indice => $datos_indice) {
					$datos_formateados = array();

					$datos_formateados["fk_id_workorder"] = $idWorkorder;
					$datos_formateados["quantity"] = 1;
					$datos_formateados["hours"] = 0;
					$datos_formateados["standby"] = 2;
					foreach ($datos_indice as $columna => $valor) {
						if (isset($columnas_mapeo[$columna])) {
							$columna_destino = $columnas_mapeo[$columna];
							$datos_formateados[$columna_destino] = $valor;
						}
					}
					$table = 'workorder_equipment';
					$this->programming_model->add_item_workorder($table, $datos_formateados);
				}
			}

			//save material info
			if ($programmingMaterials) {
				$columnas_mapeo = array(
					'fk_id_material' => 'fk_id_material',
					'quantity' => 'quantity',
					'unit' => 'unit',
					'description' => 'description',
					'id_programming_material' => 'fk_id_programming_materials',
				);

				foreach ($programmingMaterials as $indice => $datos_indice) {
					$datos_formateados = array();

					$datos_formateados["fk_id_workorder"] = $idWorkorder;
					foreach ($datos_indice as $columna => $valor) {
						if (isset($columnas_mapeo[$columna])) {
							$columna_destino = $columnas_mapeo[$columna];
							$datos_formateados[$columna_destino] = $valor;
						}
					}
					$table = 'workorder_materials';
					$this->programming_model->add_item_workorder($table, $datos_formateados);
				}
			}

			//save subcontractor
			if ($programmingSubcontractor) {

				$column_map = array(
					'fk_id_company' => 'fk_id_company',
					'equipment' => 'equipment',
					'quantity' => 'quantity',
					'unit' => 'unit',
					'hours' => 'hours',
					'rate' => 'rate',
					'markup' => 'markup',
					'value' => 'value',
					'contact' => 'contact',
					'description' => 'description',
					'view_pdf' => 'view_pdf',
					'flag_expenses' => 'flag_expenses',
				);

				foreach ($programmingSubcontractor as $key => $data_indix) {
					$data_format = array();

					$data_format["fk_id_workorder"] = $idWorkorder;
					$data_format["unit"] = ' ';
					$data_format["contact"] = ' ';
					$data_format["description"] = ' ';
					foreach ($data_indix as $colum => $value) {
						if (isset($column_map[$colum])) {
							$colum_dest = $column_map[$colum];
							$data_format[$colum_dest] = $value;
						}
					}

					$table = 'workorder_ocasional';
					$insertedId = $this->programming_model->add_item_workorder($table, $data_format);

					if($data_indix["does_hauling"] == 1){
						$info = array(
							'fk_id_user' => $infoPlanning[0]["fk_id_user"],
							'fk_id_company' => $data_indix["fk_id_company"],
							'fk_id_site_from' => $infoPlanning[0]["fk_id_job"],
							'fk_id_site_to' => $infoPlanning[0]["fk_id_job"],
							'comments' => $data_indix["description"],
							'plate' => $data_indix["unit"],
							'date_issue' => $infoPlanning[0]["date_programming"],
							'fk_id_workorder' => $idWorkorder,
							'fk_id_submodule' => $insertedId,
							'fk_id_programming' => $idProgramming
						);
						$this->db->insert('hauling', $info);
					}
				}
			}
/*
			if ($programmingHauling) {
				foreach ($programmingHauling as $key => $data_hauling) {
					//actulizo la hauling
					$arrParam = array(
						"table" => "hauling",
						"primaryKey" => "id_hauling",
						"id" => $data_hauling["id_hauling"],
						"column" => "fk_id_workorder",
						"value" => $idWorkorder
					);

					$this->general_model->updateRecord($arrParam);
				}
			}
*/
			return $idWorkorder;
		} else {
			return FALSE;
		}
	}

	function getForemanData($table, $order, $column, $id)
	{
		$arrParam = [
			"table" => $table,
			"order" => $order,
			"column" => $column,
			"id" => $id
		];
		$result = $this->general_model->get_basic_search($arrParam);
		return $result ? $result[0] : null;
	}

	/**
	 * Cargo modal- formulario de captura Ocasional
	 * @since 20/2/2017
	 */
	public function cargarModalOcasional()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idProgramming = $this->input->post("idProgramming");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idProgramming);

		if (count($porciones) > 1) {
			$data["idProgramming"] = $porciones[1];

			//workers list
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "param_company",
				"order" => "company_name",
				"column" => "company_type",
				"id" => 2
			);
			$data['companyList'] = $this->general_model->get_basic_search($arrParam); //company list

			$this->load->view("modal_ocasional", $data);
		}
	}

	/**
	 * Save Subcontractor 
	 * @since 20/1/2024
	 * @author BMOTTAG
	 */
	public function save_ocasional()
	{
		header('Content-Type: application/json');
		$data = array();

		$idProgramming = $this->input->post('hddidProgramming');

		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		$data["path"] = $infoProgramming[0]["fk_id_job"] . "/" . $idProgramming;

		if ($this->programming_model->saveOcasional()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		$data["controller"] = "index";
		echo json_encode($data);
	}

	public function save_hour()
	{
		$idProgramming = $this->input->post('hddIdProgramming');

		if ($this->programming_model->saveRate()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have saved the Rate!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * Delete workorder record
	 * @param varchar $tabla: nombre de la tabla de la cual se va a borrar
	 * @param int $idValue: id que se va a borrar
	 * @param int $idProgramming: llave  primaria de idProgramming
	 */
	public function deleteRecord($tabla, $idValue, $idProgramming, $vista)
	{
		if (empty($tabla) || empty($idValue) || empty($idProgramming)) {
			show_error('ERROR!!! - You are in the wrong place.');
		}
		$arrParam = array(
			"table" => "programming_" . $tabla,
			"primaryKey" => "id_programming_"  . $tabla,
			"id" => $idValue
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) {
			$this->session->set_flashdata('retornoExito', 'You have deleted one record from <strong>' . $tabla . '</strong> table.');
		} else {
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		redirect(base_url('programming/index/' . $infoProgramming[0]["fk_id_job"] . '/' . $idProgramming), 'refresh');
	}

	/**
	 * Cargo modal- formulario de captura Equipment
	 * @since 13/02/2025
	 */
	public function loadModalEquipment()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$idProgramming = $this->input->post("idProgramming");
		//como se coloca un ID diferente para que no entre en conflicto con los otros modales, toca sacar el ID
		$porciones = explode("-", $idProgramming);
		$data["idProgramming"] = $porciones[1];
		$data["idProgrammingWorker"] = $porciones[2];

		$this->load->model("general_model");
		//buscar la lista de tipo de equipmentType
		$arrParam = array(
			"table" => "param_vehicle_type_2",
			"order" => "type_2",
			"column" => "show_workorder",
			"id" => 1
		);
		$data['equipmentType'] = $this->general_model->get_basic_search($arrParam); //equipmentType list

		$this->load->view("modal_equipment", $data);
	}

	/**
	 * Save Equipment 
	 * @since 11/02/2025
	 * @author BMOTTAG
	 */
	public function save_equipment()
	{
		header('Content-Type: application/json');
		$data = array();

		$idProgramming = $this->input->post('hddidProgramming');

		$this->load->model("general_model");
		$arrParam = array("idProgramming" => $idProgramming);
		$infoProgramming = $this->general_model->get_programming($arrParam);
		$data["path"] = $infoProgramming[0]["fk_id_job"] . "/" . $idProgramming;

		if ($this->programming_model->updateWorkerEquipment()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a new record!!");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		$data["controlador"] = "index";

		echo json_encode($data);
	}
}
