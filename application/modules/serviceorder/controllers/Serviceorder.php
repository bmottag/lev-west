<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Serviceorder extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("serviceorder_model");
	}

	/**
	 * Service Order Control Panel
	 * @since 19/5/2023
	 * @author BMOTTAG
	 */
	public function index($idServiceOrder = 'x', $idEquipment = 'x', $moduleView = 'x')
	{
		$data['infoSpecificSO'] = false;
		$data['infoEquipment'] = false;
		$data['infoSO'] = false;
		$data['information'] = false;
		$data['idEquipment'] = false;
		$data['moduleView'] = $moduleView;
		if ($idServiceOrder != 'x') {
			//Service Order info. When comes form the twilio message
			$idServiceOrder = base64_decode($idServiceOrder);
			$arrParam = array("idServiceOrder" => $idServiceOrder);
			$data['infoSpecificSO'] = $this->serviceorder_model->get_service_order($arrParam);
		} elseif ($idEquipment != 'x') {
			//Preventive Maintenace When comes form maintenance List
			$data['idEquipment'] = base64_decode($idEquipment);
		} else {
			$this->load->model("general_model");
			$data['infoEquipment'] = $this->general_model->countEquipmentByType();
			$data['infoSO'] = $this->general_model->countSOByStatus();
			$arrParam = array(
				"limit" => 50
			);
			$data['information'] = $this->serviceorder_model->get_service_order($arrParam);
		}

		$data["view"] = 'dashboard_so';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - service order
	 * @since 18/5/2023
	 */
	public function cargarModalServiceOrder()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idServiceOrder"] = $this->input->post("idServiceOrder");

		//worker list
		$arrParam = array("state" => 1);
		$this->load->model("general_model");
		$data['workersList'] = $this->general_model->get_user($arrParam); //worker list

		$arrParam = array(
			"table" => "param_status",
			"order" => "status_order",
			"column" => "status_key",
			"id" => "priority"
		);
		$data['priorityList'] = $this->general_model->get_basic_search($arrParam);

		if ($data["idServiceOrder"] != 'x') {
			//status list
			$arrParam = array(
				"table" => "param_status",
				"order" => "status_order",
				"column" => "status_key",
				"id" => "serviceorder"
			);
			$data['statusList'] = $this->general_model->get_basic_search($arrParam);

			//Service Order info
			$arrParam = array(
				"idServiceOrder" => $data["idServiceOrder"]
			);
			$data['information'] = $this->serviceorder_model->get_service_order($arrParam);

			$data['maintenanceType'] = $data['information'][0]["maintenace_type"];
			$idMaintenance = $data['information'][0]["fk_id_maintenace"];
		} else {
			$data['maintenanceType']  = $this->input->post("maintenanceType");
			$idMaintenance = $this->input->post("idMaintenance");
		}

		$arrParam = array("idMaintenance" => $idMaintenance);
		$data['currentMaintenance'] = "";
		$data['nextMaintenance'] = "";
		$data['nextMaintenanceValue'] = "";
		$data['maintenanceDescription'] = "";
		$data['maintenanceDescriptionSMS'] = "";
		$data['maintenanceTypeDescription'] = "";
		if ($data['maintenanceType'] == "corrective") {
			$infoMaintenance = $this->serviceorder_model->get_corrective_maintenance($arrParam);
			$data['maintenanceDescriptionSMS'] = $data['maintenanceDescription'] = $infoMaintenance[0]["description_failure"];
			$data['maintenanceTypeDescription'] = "Corrective Maintenance";
		} else {
			$infoMaintenance = $this->serviceorder_model->get_preventive_maintenance($arrParam);

			if ($infoMaintenance) {
				$data['maintenanceDescription'] = $infoMaintenance[0]["maintenance_description"] . "<br><strong>Maintennace Type: </strong>" . $infoMaintenance[0]["maintenance_type"];
				$data['maintenanceDescriptionSMS'] = $infoMaintenance[0]["maintenance_description"];
				$data['maintenanceTypeDescription'] = "Preventive Maintenance";

				if ($data["idServiceOrder"] != 'x') {
					$tipo = $data['information'][0]['type_level_2'];
					//si es sweeper
					if ($tipo == 15) {
						$data['currentMaintenance'] = $infoMaintenance[0]["id_maintenance_type"] == 8 ? "<br><b>Current Sweeper Engine Hours: </b>" . number_format($data['information'][0]["hours_2"]) : "<b>Current Truck Engine Hours: </b>" . number_format($data['information'][0]["hours"]);
						//si es hydrovac
					} elseif ($tipo == 16) {
						if ($infoMaintenance[0]["id_maintenance_type"] == 10) {
							$data['currentMaintenance'] = "<br><strong>Blower Hours: </strong>" . number_format($data['information'][0]["hours_3"]);
						} elseif ($infoMaintenance[0]["id_maintenance_type"] == 9) {
							$data['currentMaintenance'] = "<br><strong>Hydraulic Pump Hours: </strong>" . number_format($data['information'][0]["hours_2"]);
						} else {
							$data['currentMaintenance'] = "<br><strong>Engine Hours: </strong>" . number_format($data['information'][0]["hours"]);
						}
					} else {
						$data['currentMaintenance'] = "<br><b>Current Equipment Hours/Kilometers: </b>" . number_format($data['information'][0]["hours"]);
					}
				}
				$data['nextMaintenance'] = $infoMaintenance[0]["verification_by"] == 1 ? "<br><b>Next Hours/Kilometers Maintenance: </b>" . number_format($infoMaintenance[0]["next_hours_maintenance"]) : "<br><b>Next Date Maintenance: </b>" . $infoMaintenance[0]["next_date_maintenance"];
				$data['nextMaintenanceValue'] = $infoMaintenance[0]["verification_by"] == 1 ? $infoMaintenance[0]["next_hours_maintenance"] : $infoMaintenance[0]["next_date_maintenance"];
			}
		}

		$this->load->view("service_order_modal", $data);
	}

	/**
	 * Cargo modal - Preventive Maintenace
	 * @since 23/5/2023
	 */
	public function cargarModalPreventiveMaintenance()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idMaintenance"] = $this->input->post("idMaintenance");
		//busco tipos de mantenimiento
		$arrParam = array(
			"table" => "maintenance_type",
			"order" => "maintenance_type",
			"id" => "x"
		);
		$this->load->model("general_model");
		$data['infoTypeMaintenance'] = $this->general_model->get_basic_search($arrParam);

		if ($data["idMaintenance"] != 'x') {
			$arrParam = array(
				"idMaintenance" => $data["idMaintenance"]
			);
			$data['information'] = $this->serviceorder_model->get_preventive_maintenance($arrParam);
		}

		$this->load->view("preventive_maintenance_modal", $data);
	}

	/**
	 * Save Service Order
	 * @since 18/5/2023
	 * @author BMOTTAG
	 */
	public function save_service_order()
	{
		header('Content-Type: application/json');
		$data = array();

		$idServiceOrder = $this->input->post('hddIdServiceOrder');
		$data["idEquipment"] = $this->input->post('hddIdEquipment');
		$maintenace_type = $this->input->post('hddMaintenanceType');
		$status = $this->input->post('status');
		$oldStatus = $this->input->post('hddStatus');
		$this->load->model("general_model");

		$msj = "You have added a new Service Order!!";
		if ($idServiceOrder != '') {
			$msj = "You have updated the Service Order!!";
		}

		if ($data["idServiceOrder"] = $this->serviceorder_model->saveServiceOrder()) {
			if ($idServiceOrder == '' && $maintenace_type == "corrective") {
				//change corrective maintenance status to in_progress
				$arrParam = array(
					"table" => "corrective_maintenance",
					"primaryKey" => "id_corrective_maintenance",
					"id" => $this->input->post('hddIdMaintenance'),
					"column" => "maintenance_status",
					"value" => "in_progress"
				);
				$this->general_model->updateRecord($arrParam);
			} elseif ($idServiceOrder != '' && $maintenace_type == "corrective" && $status == "closed_so") {
				//change corrective maintenance status to closed
				$arrParam = array(
					"table" => "corrective_maintenance",
					"primaryKey" => "id_corrective_maintenance",
					"id" => $this->input->post('hddIdMaintenance'),
					"column" => "maintenance_status",
					"value" => "closed"
				);
				$this->general_model->updateRecord($arrParam);
			}

			if ($idServiceOrder == '') {
				//chat first message
				$arrParam = array(
					"fk_id_module" => $data["idServiceOrder"],
					"module" => ID_MODULE_SERVICE_ORDER,
					"message" => "New Service Order"
				);
				$this->general_model->saveChat($arrParam);
				//send Twilio message
				//busco datos del vehiculo
				$arrParam = array("idVehicle" => $data["idEquipment"]);
				$vehicleInfo = $this->general_model->get_vehicle_by($arrParam);

				$arrParamUser = array("idUser" => $this->input->post('assign_to'));
				$userInfo = $this->general_model->get_user($arrParamUser);

				$mensajeSMS = "APP VCI - New Service Order #" . $data["idServiceOrder"];
				$mensajeSMS .= "\nUnit #: " . $vehicleInfo[0]["unit_number"];
				$mensajeSMS .= "\nVIN #: " . $vehicleInfo[0]["vin_number"];
				$mensajeSMS .= "\nMaintenance: " . $this->input->post('hddMaintenanceDescription');

				$module = base64_encode("ID_MODULE_SERVICE_ORDER");
				$idModule = base64_encode($data["idServiceOrder"]);
				$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);
				$mensajeSMS .= "\n\nSee: " . $urlMovil;

				$arrParam = array(
					"msjPhone" => $mensajeSMS,
					"userMovil" => $userInfo[0]['movil']
				);
				send_twilio_message($arrParam);
			} else {
				$hddIdCanBeUsed = $this->input->post('hddIdCanBeUsed');
				$can_be_used = $this->input->post('can_be_used');

				$arrParam = array(
					"table" => "param_vehicle",
					"primaryKey" => "id_vehicle",
					"id" => $data["idEquipment"],
					"column" => "so_blocked"
				);
				if ($can_be_used == 2 && $status == "closed_so") {
					//update param_vehicle, field so_blocked
					$arrParam["value"] = 1;
					$this->general_model->updateRecord($arrParam);
				} elseif ($can_be_used != $hddIdCanBeUsed) {
					//update param_vehicle, field so_blocked
					$arrParam["value"] = $can_be_used;
					$this->general_model->updateRecord($arrParam);
				}

				//update preventive maintenance
				$verification = $this->input->post('hddVerificationBy');
				if ($maintenace_type == "preventive" && $status == "closed_so") {
					$arrParam = array(
						"table" => "preventive_maintenance",
						"primaryKey" => "id_preventive_maintenance",
						"id" => $this->input->post('hddIdMaintenance')
					);
					if ($verification == 1) {
						$arrParam["column"] = "next_hours_maintenance";
						$arrParam["value"] = $this->input->post('next_hours_maintenance');
						$this->general_model->updateRecord($arrParam);
					} else {
						$arrParam["column"] = "next_date_maintenance";
						$arrParam["value"] = $this->input->post('next_date_maintenance');
						$this->general_model->updateRecord($arrParam);
					}
				}
				//If we close the service orden then we send a twilio message
				if ($status == "closed_so" && $oldStatus != $status) {
					//busco datos del vehiculo
					$arrParam = array("idVehicle" => $data["idEquipment"]);
					$vehicleInfo = $this->general_model->get_vehicle_by($arrParam);

					//BEGIN send Twilio message
					$comments = $this->input->post('comments');
					$arrParamUser = array("idUser" => $this->input->post('hddIdAssignedBy'));
					$userInfo = $this->general_model->get_user($arrParamUser);

					$mensajeSMS = "APP VCI - Service Order #" . $data["idServiceOrder"] . " closed.";
					$mensajeSMS .= "\nUnit #: " . $vehicleInfo[0]["unit_number"];
					$mensajeSMS .= "\nVIN #: " . $vehicleInfo[0]["vin_number"];
					$mensajeSMS .= "\nMaintenance: " . $this->input->post('hddMaintenanceDescription');
					$mensajeSMS .= "\nComments: " . $comments;

					$module = base64_encode("ID_MODULE_SERVICE_ORDER");
					$idModule = base64_encode($data["idServiceOrder"]);
					$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);
					$mensajeSMS .= "\n\nSee: " . $urlMovil;

					$arrParamTwilio = array(
						"msjPhone" => $mensajeSMS,
						"userMovil" => $userInfo[0]['movil']
					);
					send_twilio_message($arrParamTwilio);
					//END send Twilio message

					//If the user change the status to closed, then the systmen have to calculate the time
					$arrParam = array(
						"idServiceOrder" => $this->input->post('hddIdServiceOrder'),
						"idTime" => $this->input->post('hddIdTime'),
						"timeDate" => $this->input->post('hddTimeDate'),
						"time" => $this->input->post('hddTime')
					);
					$this->serviceorder_model->saveTime($arrParam);
				}

				//when the status is chnaged then a new message in the chat table is inserted
				if ($oldStatus != $status) {
					$arrParam = array(
						"fk_id_module" => $data["idServiceOrder"],
						"module" => ID_MODULE_SERVICE_ORDER,
						"message" => "The Service Order status has been updated: " . $oldStatus . " ---> " . $status . "."
					);
					$this->general_model->saveChat($arrParam);
				}

				//If the user change the status to in_progress, then the systmen have to check if any other SO is in progress
				if ($status == "in_progress_so" && $oldStatus != "in_progress_so") {
					$arrParam = array(
						"idAssignTo" => $this->input->post('hddIdAssignedTo'),
						"diffIdServiceOrder" => $data["idServiceOrder"],
						"status" => $status
					);
					if ($inProgressSO = $this->serviceorder_model->get_service_order($arrParam)) {
						$msj .= " <b>Remember that you can only have one SO as In Progress.</b>";
						//update time for the OTHER SO
						$arrParam = array(
							"idServiceOrder" => $inProgressSO[0]["id_service_order"],
							"idTime" => $inProgressSO[0]["id_time"],
							"timeDate" => $inProgressSO[0]["time_date"],
							"time" => $inProgressSO[0]["time"]
						);
						$this->serviceorder_model->saveTime($arrParam);
						//update SO status
						$arrParam = array(
							"table" => "service_order",
							"primaryKey" => "id_service_order",
							"id" => $inProgressSO[0]["id_service_order"],
							"column" => "service_status",
							"value" => "on_hold"
						);
						$this->general_model->updateRecord($arrParam);
					}
					//update time for the current SO
					$arrParam = array(
						"idServiceOrder" => $this->input->post('hddIdServiceOrder'),
						"idTime" => $this->input->post('hddIdTime')
					);
					$this->serviceorder_model->saveTime($arrParam);
				} elseif ($status != "in_progress_so" && $oldStatus == "in_progress_so") {
					//update time for the current SO
					$arrParam = array(
						"idServiceOrder" => $this->input->post('hddIdServiceOrder'),
						"idTime" => $this->input->post('hddIdTime'),
						"timeDate" => $this->input->post('hddTimeDate'),
						"time" => $this->input->post('hddTime')
					);
					$this->serviceorder_model->saveTime($arrParam);
				}
			}

			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Equipment List
	 * @since 20/5/2023
	 * @author BMOTTAG
	 */
	public function equipmentList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		//busco info de vehiculo
		$this->load->model("general_model");
		$data['subTitle'] = $this->input->post('headerInspectionType');
		$arrParam = array(
			"vehicleType" => $this->input->post('inspectionType'),
			"vinNumber" => $this->input->post('vinNumber'),
			"vehicleState" => 1
		);
		$data["vehicleInfo"] = $this->general_model->get_vehicle_by($arrParam); //busco datos del vehiculo

		if ($data["vehicleInfo"]) {
			echo $this->load->view("equipment_list", $data);
		} else {
			echo "<p class='text-danger'>There are no records.</p>";
		}
	}

	/**
	 * SO List
	 * @since 14/6/2023
	 * @author BMOTTAG
	 */
	public function serviceOrderList()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$status = $this->input->post('status');
		if ($status != 'x') {
			$arrParam = array("status" => $this->input->post('status'));
		} else {
			$arrParam = array("idServiceOrder" => $this->input->post('soNumber'));
		}
		$data['information'] = $this->serviceorder_model->get_service_order($arrParam);
		echo $this->load->view("service_order_list", $data);
	}

	/**
	 * Equipment Detail
	 * @since 21/5/2023
	 * @author BMOTTAG
	 */
	public function equipmentDetail()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		//busco info de vehiculo
		$this->load->model("general_model");
		$arrParam = array(
			"idVehicle" => $this->input->post('equipmentId')
		);
		$data["vehicleInfo"] = $this->general_model->get_vehicle_by($arrParam); //busco datos del vehiculo

		$data["tabview"] = $this->input->post('tabview');

		if ($data["tabview"] == "tab_service_order") {
			$data['information'] = $this->serviceorder_model->get_service_order($arrParam);
		} elseif ($data["tabview"] == "tab_corrective_maintenance") {
			$data['infoCorrectiveMaintenance'] = $this->serviceorder_model->get_corrective_maintenance($arrParam);
		} elseif ($data["tabview"] == "tab_preventive_maintenance") {
			$data['infoPreventiveMaintenance'] = $this->serviceorder_model->get_preventive_maintenance($arrParam);
		} elseif ($data["tabview"] == "tab_inspections") {
			$data['infoInspections'] = false;
			if($data['vehicleInfo'][0]['table_inspection']){
				$data['infoInspections'] = $this->general_model->get_vehicle_oil_change($data['vehicleInfo']); //vehicle oil change history
			}
		} elseif ($data["tabview"] == "tab_service_order_detail") {
			$arrParam["idServiceOrder"] = $this->input->post('serviceOrderId');
			$data['information'] = $this->serviceorder_model->get_service_order($arrParam);
			$arrParam = array(
				"idModule" => $this->input->post('serviceOrderId'),
				"module" => ID_MODULE_SERVICE_ORDER
			);
			$data['chatInfo'] = $this->general_model->get_chat_info($arrParam);

			$arrParam = array("idServiceOrder" => $this->input->post('serviceOrderId'));
			$data['infoParts'] = $this->serviceorder_model->get_parts($arrParam);
		} elseif ($data["tabview"] == "tab_parts_by_store") {
			$data['infoPartsByStore'] = $this->serviceorder_model->get_parts_store_by_equipment($arrParam);
		}

		if ($data["vehicleInfo"]) {
			echo $this->load->view("equipment_detail", $data);
		} else {
			echo "<p class='text-danger'>There are no records.</p>";
		}
	}

	/**
	 * Save Preventive Maintenance
	 * @since 22/5/2023
	 * @author BMOTTAG
	 */
	public function save_preventive_maintenance()
	{
		header('Content-Type: application/json');
		$data = array();

		$idMaintenance = $this->input->post('hddIdMaintenance');
		$data["idEquipment"] = $this->input->post('hddIdEquipment');

		$msj = "You have added a new Preventive Maintenance!!";
		if ($idMaintenance != '') {
			$msj = "You have updated the Preventive Maintenance!!";
		}

		if ($idMaintenance = $this->serviceorder_model->savePreventiveMaintenance()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Cargo modal - Corrective Maintenace
	 * @since 26/5/2023
	 */
	public function cargarModalCorrectiveMaintenance()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idMaintenance"] = $this->input->post("idMaintenance");

		if ($data["idMaintenance"] != 'x') {
			$arrParam = array(
				"idMaintenance" => $data["idMaintenance"]
			);
			$data['information'] = $this->serviceorder_model->get_corrective_maintenance($arrParam);
		}

		$this->load->view("corrective_maintenance_modal", $data);
	}

	/**
	 * Save Corrective Maintenance
	 * @since 26/5/2023
	 * @author BMOTTAG
	 */
	public function save_corrective_maintenance()
	{
		header('Content-Type: application/json');
		$data = array();

		$idMaintenance = $this->input->post('hddIdMaintenance');
		$data["idEquipment"] = $this->input->post('hddIdEquipment');

		$msj = "You have added a new Corrective Maintenance!!";
		if ($idMaintenance != '') {
			$msj = "You have updated the Corrective Maintenance!!";
		}

		if ($idMaintenance = $this->serviceorder_model->saveCorrectiveMaintenance()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Save chat
	 * @since 29/5/2023
	 * @author BMOTTAG
	 */
	public function save_chat()
	{
		header('Content-Type: application/json');
		$data["idEquipment"] = $this->input->post('hddIdEquipment');
		$idServiceOrder = $this->input->post('hddId');
		$idAssignedTo = $this->input->post('hddIdAssignedTo');
		$idAssignedBy = $this->input->post('hddIdAssignedBy');
		$data["view"] = $this->input->post('hddView');
		$data["idModule"] = $this->input->post('hddId');

		$arrParam = array(
			"fk_id_module" => $idServiceOrder,
			"module" => ID_MODULE_SERVICE_ORDER,
			"message" => addslashes($this->security->xss_clean($this->input->post('message')))
		);
		$this->load->model("general_model");
		if ($this->general_model->saveChat($arrParam)) {
			//BEGIN send Twilio message
			$idUser = $this->session->userdata("id");

			$arrParam = array("idVehicle" => $data["idEquipment"]);
			$vehicleInfo = $this->general_model->get_vehicle_by($arrParam); //busco datos del vehiculo

			$idUserTo = $idUser == $idAssignedTo ? $idAssignedBy : $idAssignedTo;
			$arrParamUser = array("idUser" => $idUserTo);
			$userInfo = $this->general_model->get_user($arrParamUser);

			$mensajeSMS = "APP VCI - Service Order #" . $idServiceOrder;
			$mensajeSMS .= "\nUnit #: " . $vehicleInfo[0]["unit_number"];
			$mensajeSMS .= "\nVIN #: " . $vehicleInfo[0]["vin_number"];
			$mensajeSMS .= "\nMaintenance: " . $this->input->post('hddMaintenanceDescription');
			$mensajeSMS .= "\nMessage: " . addslashes($this->security->xss_clean($this->input->post('message')));

			$module = base64_encode("ID_MODULE_SERVICE_ORDER");
			$idModule = base64_encode($idServiceOrder);
			$urlMovil = base_url("login/index/x/" . $module . "/" . $idModule);
			$mensajeSMS .= "\n\nSee: " . $urlMovil;

			$arrParamTwilio = array(
				"msjPhone" => $mensajeSMS,
				"userMovil" => $userInfo[0]['movil']
			);
			send_twilio_message($arrParamTwilio);
			//END send Twilio message
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', "You have added a message");
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}
		echo json_encode($data);
	}

	/**
	 * Cargo modal - service order Parts
	 * @since 30/5/2023
	 */
	public function cargarModalParts()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data["idPart"] = $this->input->post("idPart");

		if ($data["idPart"] != 'x') {
			$arrParam = array("idPart" => $data["idPart"]);
			$data['information'] = $this->serviceorder_model->get_parts($arrParam);
		}

		$this->load->view("parts_modal", $data);
	}

	/**
	 * Save Parts
	 * @since 30/5/2023
	 * @author BMOTTAG
	 */
	public function save_parts()
	{
		header('Content-Type: application/json');
		$data = array();

		$idParts = $this->input->post('hddIdPart');
		$data["idServiceOrder"] = $this->input->post('hddIdServiceOrder');
		$data["idEquipment"] = $this->input->post('hddIdEquipment');

		$msj = "You have added a new record!!";
		if ($idParts != '') {
			$msj = "You have updated the information!!";
		}

		if ($this->serviceorder_model->saveParts()) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Maintenance Check
	 * CRON: Time: Every Day at 12am 
	 */
	public function maintenance_check()
	{
		$fecha = date('Y-m-d');
		$filtroFecha = strtotime('+7 day', strtotime($fecha));

		//listado de registros mantenimientos PREVENTIVOS activos
		$arrParam = array("maintenanceStatus" => 1);
		$infoMaintenance = $this->serviceorder_model->get_preventive_maintenance($arrParam);

		$this->serviceorder_model->delete_maintenance_check(); //elimino los registros de maintenance_check
		//revisar cuales estan proximo a vencerse por kilometros o fechas
		foreach ($infoMaintenance as $lista) :
			$diferencia = $lista["next_hours_maintenance"] - $lista["hours"];

			if ($lista["fk_id_maintenance_type"] == 8 || $lista["fk_id_maintenance_type"] == 9) {
				$diferencia = $lista["next_hours_maintenance"] - $lista["hours_2"];
			} elseif ($lista["fk_id_maintenance_type"] == 10) {
				$diferencia = $lista["next_hours_maintenance"] - $lista["hours_3"];
			}

			$nextDateMaintenance = strtotime($lista["next_date_maintenance"]);

			if (($lista["verification_by"] == 1 && $lista["next_hours_maintenance"] != 0 && $diferencia <= 50) || ($lista["verification_by"] == 2 && $lista["next_date_maintenance"] != "" && $lista['next_date_maintenance'] != "0000-00-00" && $nextDateMaintenance <= $filtroFecha)) {
				$this->serviceorder_model->add_maintenance_check($lista["id_preventive_maintenance"]);
			}
		endforeach;

		$this->load->model("general_model");
		$infoMaintenance = $this->general_model->get_maintenance_check();
		if ($infoMaintenance) {
			//send notifiation
			$subjet = "Preventive Maintenance List";
			$module = base64_encode("DASHBOARD_MAINTENANCE_LIST");
			$emailMsn = "There is an urgent need to carry out <b>Preventive Maintenance</b> as soon as possible.";
			$emailMsn .= "<br>Follow the link to see the list. ";
			$emailMsn .= "<a href='" . base_url("login/index/x/" . $module . "/x") . "' >Click here </a>";

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
			$mensajeSMS = "APP VCI - " . $subjet;
			$mensajeSMS .= "\nThere is an urgent need to carry out Preventive Maintenance as soon as possible.";
			$mensajeSMS .= "\nFollow the link to see the list.";
			$mensajeSMS .= "\n\n" . base_url("login/index/x/" . $module . "/x");

			//enviar correo a VCI
			$arrParam = array(
				"idNotification" => ID_NOTIFICATION_MAINTENANCE,
				"subjet" => $subjet,
				"msjEmail" => $mensaje,
				"msjPhone" => $mensajeSMS
			);
			send_notification($arrParam);
		}

		return true;
	}

	/**
	 * Generate Service Order Report in PDF
	 * @param int $idServiceOrder
	 * @since 20/07/2023
	 * @author BMOTTAG
	 */
	public function generateSOReportPDF($idServiceOrder)
	{
		$this->load->library('Pdf');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$arrParam = array("idServiceOrder" => $idServiceOrder);
		$data['info'] = $this->serviceorder_model->get_service_order($arrParam);

		$fecha = date('F j, Y', strtotime($data['info'][0]['created_at']));

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('VCI');
		$pdf->SetTitle('WORK ORDER');
		$pdf->SetSubject('TCPDF Tutorial');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'SERVICE ORDER', 'S.O. #: ' . $idServiceOrder . "\nS.O. date: " . $fecha, array(0, 64, 255), array(0, 64, 128));

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

		$arrParam = array(
			"idModule" => $idServiceOrder,
			"module" => ID_MODULE_SERVICE_ORDER
		);
		$data['chatInfo'] = $this->serviceorder_model->get_chat_info($arrParam);

		$arrParam = array("idServiceOrder" => $idServiceOrder);
		$data['infoParts'] = $this->serviceorder_model->get_parts($arrParam);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// Print a table

		// add a page
		//$pdf->AddPage('L', 'A4');
		$pdf->AddPage();

		$html = $this->load->view("service_order_report", $data, true);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// Print some HTML Cells

		// reset pointer to the last page
		$pdf->lastPage();


		//Close and output PDF document
		$pdf->Output('service_order_report' . $idServiceOrder . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * Expenses
	 * @since 21/7/2023
	 * @author BMOTTAG
	 */
	public function expenses()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$arrParam = array();
		$data['information'] = $this->serviceorder_model->get_expenses($arrParam);

		if ($data["information"]) {
			echo $this->load->view("expenses", $data);
		} else {
			echo "<p class='text-danger'>There are no records.</p>";
		}
	}

	/**
	 * Expenses
	 * @since 21/7/2023
	 * @author BMOTTAG
	 */
	public function expensesByEquipment()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$arrParam = array("idVehicle" => $this->input->post("equipmentId"));
		$data['information'] = $this->serviceorder_model->get_expenses_by_equipment($arrParam);
		$this->load->model("general_model");
		$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

		if ($data["information"]) {
			echo $this->load->view("expenses_by_equipment", $data);
		} else {
			echo "<p class='text-danger'>There are no records.</p>";
		}
	}

	/**
	 * Parts List
	 * @since 30/10/2023
	 * @author BMOTTAG
	 */
	public function parts_by_store()
	{
		$arrParam = array();
		$data['info'] = $this->serviceorder_model->get_parts_by_store($arrParam);

		$data["view"] = 'parts_list';
		$this->load->view("layout", $data);
	}

	/**
	 * Cargo modal - part form
	 * @since 30/10/2023
	 */
	public function cargarModalShopParts()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$data['information'] = FALSE;
		$data['informationParts'] = FALSE;
		$data["idPartShop"] = $this->input->post("idPartShop");

		$this->load->model("general_model");
		$data['equipmentType'] = $this->general_model->equipmentByTypeList();

		$arrParam = array(
			"table" => "param_shop",
			"order" => "shop_name",
			"id" => "x"
		);
		$data['shopList'] = $this->general_model->get_basic_search($arrParam);

		if ($data["idPartShop"] != 'x') {
			$arrParam = array(
				"idPartShop" => $data["idPartShop"]
			);
			$data['information'] = $this->serviceorder_model->get_parts_by_store($arrParam);
			$data['informationParts'] = $this->serviceorder_model->get_parts_equipment($arrParam);
		}

		$this->load->view("parts_shop_modal", $data);
	}

	/**
	 * Save Shop Parts
	 * @since 30/10/2023
	 * @author BMOTTAG
	 */
	public function save_shop_parts()
	{
		header('Content-Type: application/json');
		$data = array();

		$idPartShop = $this->input->post('hddId');

		$msj = "You have added a Part!!";
		if ($idPartShop != '') {
			$msj = "You have updated a Part!!";
		}

		if ($idPartShop = $this->serviceorder_model->saveShopParts()) {
			$this->serviceorder_model->add_equipment_shop_parts($idPartShop);
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', $msj);
		} else {
			$data["result"] = "error";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}

	/**
	 * Equipment list
	 * @since 24/6/2023
	 * @author BMOTTAG
	 */
	public function equipmentListForPartsShop()
	{
		header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos

		$arrParam = array(
			"vehicleType" => $this->input->post('type'),
			"vehicleState" => 1
		);
		$this->load->model("general_model");
		$lista = $this->general_model->get_vehicle_by($arrParam);
		$arrayInformationAttachments = false;
		if ($this->input->post('idEquipmentPart') != "") {
			$arrParam = array(
				"idEquipmentPart" => $this->input->post('idEquipmentPart'),
				"relation" => true
			);
			$arrayInformationAttachments = $this->serviceorder_model->get_parts_equipment($arrParam);
		}

		echo "<option value=''>Select...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				$s = "";
				if ($arrayInformationAttachments) {
					$found = false;
					foreach ($arrayInformationAttachments as $idVehicle) {
						if (in_array($fila['id_vehicle'], $idVehicle)) {
							$found = true;
							break;
						}
					}
					$s = $found ? "selected" : "";
				}
				echo "<option value='" . $fila["id_vehicle"] . "'" . $s . ">" . $fila["unit_number"] . " -----> " . $fila["description"]  . "</option>";
			}
		}
	}
}
