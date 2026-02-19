<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inspection_model extends CI_Model
{


	/**
	 * Add/Edit HeavyInspection
	 * @since 27/12/2016
	 */
	public function saveHeavyInspection()
	{
		$idUser = $this->session->userdata("id");
		$idHeavyInspection = $this->input->post('hddId');

		$data = array(
			'fk_id_vehicle' => $this->input->post('hddIdVehicle'),
			'belt' => $this->input->post('belt'),
			'oil_level' => $this->input->post('oil'),
			'coolant_level' => $this->input->post('coolantLevel'),
			'coolant_leaks' => $this->input->post('coolantLeaks'),
			'working_lamps' => $this->input->post('workingLamps'),
			'beacon_lights' => $this->input->post('beaconLights'),
			'heater' => $this->input->post('heater'),
			'operator_seat' => $this->input->post('operatorSeat'),
			'gauges' => $this->input->post('gauges'),
			'horn' => $this->input->post('horn'),
			'seatbelt' => $this->input->post('seatbelt'),
			'clean_interior' => $this->input->post('cleanInterior'),
			'windows' => $this->input->post('windows'),
			'clean_exterior' => $this->input->post('cleanExterior'),
			'wipers' => $this->input->post('wipers'),
			'backup_beeper' => $this->input->post('backupBeeper'),
			'door' => $this->input->post('door'),
			'decals' => $this->input->post('decals'),
			'boom_grease' => $this->input->post('boom'),
			'table_excavator' => $this->input->post('tableExcavator'),
			'bucket_pins' => $this->input->post('bucketPins'),
			'blade_pins' => $this->input->post('bladePins'),
			'ripper' => $this->input->post('ripper'),
			'front_axle' => $this->input->post('frontAxle'),
			'rear_axle' => $this->input->post('rearAxle'),
			'table_dozer' => $this->input->post('tableDozer'),
			'pivin_points' => $this->input->post('pivinPoints'),
			'bucket_pins_skit' => $this->input->post('bucketPinsSkit'),
			'side_arms' => $this->input->post('sideArms'),
			'bucket' => $this->input->post('bucket'),
			'cutting_edges' => $this->input->post('cutting'),
			'blades' => $this->input->post('blades'),
			'tracks' => $this->input->post('tracks'),
			'rubber_trucks' => $this->input->post('rubberTrucks'),
			'rollers' => $this->input->post('rollers'),
			'thamper' => $this->input->post('thamper'),
			'drill' => $this->input->post('drill'),
			'fire_extinguisher' => $this->input->post('fire'),
			'first_aid' => $this->input->post('aid'),
			'spill_kit' => $this->input->post('spillKit'),
			'tire_presurre' => $this->input->post('tire'),
			'turn_signals' => $this->input->post('turn'),
			'rims' => $this->input->post('rims'),
			'emergency_brake' => $this->input->post('brake'),
			'transmission' => $this->input->post('transmission'),
			'hydrolic' => $this->input->post('hydrolic'),
			'comments' => $this->input->post('comments'),
			'def' => $this->input->post('def')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');
		$hora = date("G:i:s");
		if ($userRol == 99 && $dateIssue != "") {
			$data['date_issue'] = $dateIssue . " " . $hora;
		} else {
			$data['date_issue'] = date("Y-m-d G:i:s");
		}

		//revisar si es para adicionar o editar
		if ($idHeavyInspection == '') {
			$data['fk_id_user'] = $idUser;

			$query = $this->db->insert('inspection_heavy', $data);
			$idHeavyInspection = $this->db->insert_id();
		} else {
			$this->db->where('id_inspection_heavy', $idHeavyInspection);
			$query = $this->db->update('inspection_heavy', $data);
		}
		if ($query) {
			return $idHeavyInspection;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit DailyInspection
	 * @since 27/12/2016
	 */
	public function saveDailyInspection()
	{
		$idUser = $this->session->userdata("id");
		$idDailyInspection = $this->input->post('hddId');

		$trailer = $this->input->post('trailer');
		$with_trailer = $trailer == '' ? 2 : 1; //Para indicar si se deligencio o no el formulario de trailer

		$data = array(
			'fk_id_vehicle' => $this->input->post('hddIdVehicle'),
			'belt' => $this->input->post('belt'),
			'power_steering' => $this->input->post('powerSteering'),
			'oil_level' => $this->input->post('oil'),
			'coolant_level' => $this->input->post('coolantLevel'),
			'water_leaks' => $this->input->post('waterLeaks'),
			'nuts' => $this->input->post('nuts'),
			'head_lamps' => $this->input->post('headLamps'),
			'hazard_lights' => $this->input->post('hazardLights'),
			'clearance_lights' => $this->input->post('clearanceLights'),
			'bake_lights' => $this->input->post('bakeLights'),
			'work_lights' => $this->input->post('workLights'),
			'glass' => $this->input->post('glass'),
			'clean_exterior' => $this->input->post('cleanExterior'),
			'proper_decals' => $this->input->post('properDecals'),
			'brake_pedal' => $this->input->post('brakePedal'),
			'emergency_brake' => $this->input->post('emergencyBrake'),
			'backup_beeper' => $this->input->post('backupBeeper'),
			'beacon_light' => $this->input->post('beaconLight'),
			'gauges' => $this->input->post('gauges'),
			'horn' => $this->input->post('horn'),
			'hoist' => $this->input->post('hoist'),
			'passenger_door' => $this->input->post('passengerDoor'),
			'seatbelts' => $this->input->post('seatbelts'),
			'fire_extinguisher' => $this->input->post('fireExtinguisher'),
			'emergency_reflectors' => $this->input->post('emergencyReflectors'),
			'first_aid' => $this->input->post('firstAid'),
			'wipers' => $this->input->post('wipers'),
			'drives_axle' => $this->input->post('drivesAxle'),
			'grease_front' => $this->input->post('greaseFront'),
			'grease_end' => $this->input->post('greaseEnd'),
			'spill_kit' => $this->input->post('spillKit'),
			'grease' => $this->input->post('grease'),
			'steering_axle' => $this->input->post('steeringAxle'),
			'turn_signals' => $this->input->post('turnSignals'),
			'clean_interior' => $this->input->post('cleanInterior'),
			'insurance' => $this->input->post('insurance'),
			'driver_seat' => $this->input->post('driverSeat'),
			'registration' => $this->input->post('registration'),
			'heater' => $this->input->post('heater'),
			'steering_wheel' => $this->input->post('steering_wheel'),
			'suspension_system' => $this->input->post('suspension_system'),
			'air_brake' => $this->input->post('air_brake'),
			'fuel_system' => $this->input->post('fuel_system'),
			'comments' => $this->input->post('comments'),
			'with_trailer' => $with_trailer,
			'fk_id_trailer' => $this->input->post('trailer'),
			'trailer_lights' => $this->input->post('trailerLights'),
			'trailer_tires' => $this->input->post('trailerTires'),
			'trailer_slings' => $this->input->post('trailerSlings'),
			'trailer_clean' => $this->input->post('trailerClean'),
			'trailer_chains' => $this->input->post('trailerChains'),
			'trailer_ratchet' => $this->input->post('trailerRatchet'),
			'trailer_comments' => $this->input->post('trailerComments'),
			'def' => $this->input->post('def'),
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');
		$hora = date("G:i:s");
		if ($userRol == 99 && $dateIssue != "") {
			$data['date_issue'] = $dateIssue . " " . $hora;
		} else {
			$data['date_issue'] = date("Y-m-d G:i:s");
		}

		//revisar si es para adicionar o editar
		if ($idDailyInspection == '') {
			$data['fk_id_user'] = $idUser;

			$query = $this->db->insert('inspection_daily', $data);
			$idDailyInspection = $this->db->insert_id();
		} else {
			$this->db->where('id_inspection_daily', $idDailyInspection);
			$query = $this->db->update('inspection_daily', $data);
		}
		if ($query) {
			return $idDailyInspection;
		} else {
			return false;
		}
	}

	/**
	 * Add vehicle next oil change
	 * @since 18/1/2017
	 */
	public function saveVehicleNextOilChange($idVehicle, $state, $idInspection)
	{
		$idUser = $this->session->userdata("id");

		$data = array(
			'fk_id_vehicle' => $idVehicle,
			'fk_id_user' => $idUser,
			'current_hours' => $this->input->post('hours'),
			'date_issue' => date("Y-m-d G:i:s"),
			'state' => $state,
			'current_hours_2' => $this->input->post('hours2'),
			'current_hours_3' => $this->input->post('hours3'),
			'fk_id_inspection' => $idInspection
		);
		$query = $this->db->insert('vehicle_oil_change', $data);
		if ($query) {

			$data = array(
				'hours' => $this->input->post('hours'),
				'hours_2' => $this->input->post('hours2'),
				'hours_3' => $this->input->post('hours3')
			);

			$this->db->where('id_vehicle', $idVehicle);
			$query = $this->db->update('param_vehicle', $data);

			if ($query) {
				return true;
			} else {
				//se debe borrar el registro en la tabla vehicle_oil_change
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit Generator Inspection
	 * @since 17/3/2017
	 */
	public function saveGeneratorInspection()
	{
		$idUser = $this->session->userdata("id");
		$idDailyInspection = $this->input->post('hddId');

		$data = array(
			'fk_id_vehicle' => $this->input->post('hddIdVehicle'),
			'belt' => $this->input->post('belt'),
			'fuel_filter' => $this->input->post('fuelFilter'),
			'oil_level' => $this->input->post('oil'),
			'coolant_level' => $this->input->post('coolantLevel'),
			'coolant_leaks' => $this->input->post('coolantLeaks'),
			'turn_signal' => $this->input->post('turnSignal'),
			'hazard_lights' => $this->input->post('hazardLights'),
			'tail_lights' => $this->input->post('tailLights'),
			'flood_lights' => $this->input->post('floodLights'),
			'boom' => $this->input->post('boom'),
			'gears' => $this->input->post('gears'),
			'gauges' => $this->input->post('gauges'),
			'pulley' => $this->input->post('pulley'),
			'electrical' => $this->input->post('electrical'),
			'brackers' => $this->input->post('brackers'),
			'tires' => $this->input->post('tires'),
			'clean_exterior' => $this->input->post('cleanExterior'),
			'decals' => $this->input->post('decals'),
			'comments' => $this->input->post('comments')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');
		$hora = date("G:i:s");
		if ($userRol == 99 && $dateIssue != "") {
			$data['date_issue'] = $dateIssue . " " . $hora;
		} else {
			$data['date_issue'] = date("Y-m-d G:i:s");
		}

		//revisar si es para adicionar o editar
		if ($idDailyInspection == '') {
			$data['fk_id_user'] = $idUser;

			$query = $this->db->insert('inspection_generator', $data);
			$idDailyInspection = $this->db->insert_id();
		} else {
			$this->db->where('id_inspection_generator', $idDailyInspection);
			$query = $this->db->update('inspection_generator', $data);
		}

		if ($query) {
			return $idDailyInspection;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit Sweeper Inspection
	 * @since 23/4/2017
	 */
	public function saveSweeperInspection()
	{
		$idUser = $this->session->userdata("id");
		$idDailyInspection = $this->input->post('hddId');

		$data = array(
			'fk_id_vehicle' => $this->input->post('hddIdVehicle'),
			'belt' => $this->input->post('belt'),
			'power_steering' => $this->input->post('powerSteering'),
			'oil_level' => $this->input->post('oil'),
			'coolant_level' => $this->input->post('coolantLevel'),
			'coolant_leaks' => $this->input->post('coolantLeaks'),
			'hydraulic' => $this->input->post('hydraulic'),
			'belt_sweeper' => $this->input->post('beltSweeper'),
			'oil_level_sweeper' => $this->input->post('oilSweeper'),
			'coolant_level_sweeper' => $this->input->post('coolantLevelSweeper'),
			'coolant_leaks_sweeper' => $this->input->post('coolantLeaksSweeper'),
			'head_lamps' => $this->input->post('headLamps'),
			'hazard_lights' => $this->input->post('hazardLights'),
			'clearance_lights' => $this->input->post('clearanceLights'),
			'tail_lights' => $this->input->post('tailLights'),
			'work_lights' => $this->input->post('workLights'),
			'turn_signals' => $this->input->post('turnSignals'),
			'beacon_lights' => $this->input->post('beaconLights'),
			'tires' => $this->input->post('tires'),
			'windows' => $this->input->post('windows'),
			'clean_exterior' => $this->input->post('cleanExterior'),
			'wipers' => $this->input->post('wipers'),
			'backup_beeper' => $this->input->post('backupBeeper'),
			'door' => $this->input->post('door'),
			'decals' => $this->input->post('decals'),
			'stering_wheels' => $this->input->post('SteringWheels'),
			'drives' => $this->input->post('drives'),
			'front_drive' => $this->input->post('frontDrive'),
			'elevator' => $this->input->post('elevator'),
			'rotor' => $this->input->post('rotor'),
			'mixture_box' => $this->input->post('mixtureBox'),
			'lf_rotor' => $this->input->post('lfRotor'),
			'elevator_sweeper' => $this->input->post('elevatorSweeper'),
			'mixture_container' => $this->input->post('mixtureContainer'),
			'broom' => $this->input->post('broom'),
			'right_broom' => $this->input->post('rightBroom'),
			'left_broom' => $this->input->post('leftBroom'),
			'sprinkerls' => $this->input->post('sprinkerls'),
			'water_tank' => $this->input->post('waterTank'),
			'hose' => $this->input->post('hose'),
			'cam' => $this->input->post('cam'),
			'brake' => $this->input->post('brake'),
			'emergency_brake' => $this->input->post('emergencyBrake'),
			'gauges' => $this->input->post('gauges'),
			'horn' => $this->input->post('horn'),
			'seatbelt' => $this->input->post('seatbelt'),
			'seat' => $this->input->post('seat'),
			'insurance' => $this->input->post('insurance'),
			'registration' => $this->input->post('registration'),
			'clean_interior' => $this->input->post('cleanInterior'),
			'fire_extinguisher' => $this->input->post('fire'),
			'first_aid' => $this->input->post('aid'),
			'emergency_kit' => $this->input->post('emergencyKit'),
			'spill_kit' => $this->input->post('spillKit'),
			'comments' => $this->input->post('comments')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');
		$hora = date("G:i:s");
		if ($userRol == 99 && $dateIssue != "") {
			$data['date_issue'] = $dateIssue . " " . $hora;
		} else {
			$data['date_issue'] = date("Y-m-d G:i:s");
		}

		//revisar si es para adicionar o editar
		if ($idDailyInspection == '') {
			$data['fk_id_user'] = $idUser;

			$query = $this->db->insert('inspection_sweeper', $data);
			$idDailyInspection = $this->db->insert_id();
		} else {
			$this->db->where('id_inspection_sweeper', $idDailyInspection);
			$query = $this->db->update('inspection_sweeper', $data);
		}

		if ($query) {
			return $idDailyInspection;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit Hydrovac Inspection
	 * @since 23/4/2017
	 */
	public function saveHydrovacInspection()
	{
		$idUser = $this->session->userdata("id");
		$idDailyInspection = $this->input->post('hddId');

		$data = array(
			'fk_id_vehicle' => $this->input->post('hddIdVehicle'),
			'belt' => $this->input->post('belt'),
			'power_steering' => $this->input->post('powerSteering'),
			'oil_level' => $this->input->post('oil'),
			'coolant_level' => $this->input->post('coolantLevel'),
			'coolant_leaks' => $this->input->post('coolantLeaks'),
			'head_lamps' => $this->input->post('headLamps'),
			'hazard_lights' => $this->input->post('hazardLights'),
			'clearance_lights' => $this->input->post('clearanceLights'),
			'tail_lights' => $this->input->post('tailLights'),
			'work_lights' => $this->input->post('workLights'),
			'turn_signals' => $this->input->post('turnSignals'),
			'beacon_lights' => $this->input->post('beaconLights'),
			'tires' => $this->input->post('tires'),
			'windows' => $this->input->post('windows'),
			'clean_exterior' => $this->input->post('cleanExterior'),
			'wipers' => $this->input->post('wipers'),
			'backup_beeper' => $this->input->post('backupBeeper'),
			'door' => $this->input->post('door'),
			'decals' => $this->input->post('decals'),
			'stering_wheels' => $this->input->post('SteringWheels'),
			'drives' => $this->input->post('drives'),
			'front_drive' => $this->input->post('frontDrive'),
			'middle_drive' => $this->input->post('middleDrive'),
			'back_drive' => $this->input->post('backDrive'),
			'transfer' => $this->input->post('transfer'),
			'tail_gate' => $this->input->post('tailGate'),
			'boom' => $this->input->post('boom'),
			'lock_bar' => $this->input->post('lockBar'),
			'brake' => $this->input->post('brake'),
			'emergency_brake' => $this->input->post('emergencyBrake'),
			'gauges' => $this->input->post('gauges'),
			'horn' => $this->input->post('horn'),
			'seatbelt' => $this->input->post('seatbelt'),
			'seat' => $this->input->post('seat'),
			'insurance' => $this->input->post('insurance'),
			'registration' => $this->input->post('registration'),
			'clean_interior' => $this->input->post('cleanInterior'),
			'fire_extinguisher' => $this->input->post('fire'),
			'first_aid' => $this->input->post('aid'),
			'emergency_kit' => $this->input->post('emergencyKit'),
			'spill_kit' => $this->input->post('spillKit'),
			'cartige' => $this->input->post('cartige'),
			'pump' => $this->input->post('pump'),
			'wash_hose' => $this->input->post('washHose'),
			'pressure_hose' => $this->input->post('pressureHose'),
			'pump_oil' => $this->input->post('pumpOil'),
			'hydraulic_oil' => $this->input->post('hydraulicOil'),
			'gear_case' => $this->input->post('gearCase'),
			'hydraulic' => $this->input->post('hydraulic'),
			'control' => $this->input->post('control'),
			'panel' => $this->input->post('panel'),
			'foam' => $this->input->post('foam'),
			'heater' => $this->input->post('heater'),
			'steering_wheel' => $this->input->post('steering_wheel'),
			'suspension_system' => $this->input->post('suspension_system'),
			'air_brake' => $this->input->post('air_brake'),
			'fuel_system' => $this->input->post('fuel_system'),
			'comments' => $this->input->post('comments'),
			'def' => $this->input->post('def')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');
		$hora = date("G:i:s");
		if ($userRol == 99 && $dateIssue != "") {
			$data['date_issue'] = $dateIssue . " " . $hora;
		} else {
			$data['date_issue'] = date("Y-m-d G:i:s");
		}

		//revisar si es para adicionar o editar
		if ($idDailyInspection == '') {
			$data['fk_id_user'] = $idUser;

			$query = $this->db->insert('inspection_hydrovac', $data);
			$idDailyInspection = $this->db->insert_id();
		} else {
			$this->db->where('id_inspection_hydrovac', $idDailyInspection);
			$query = $this->db->update('inspection_hydrovac', $data);
		}

		if ($query) {
			return $idDailyInspection;
		} else {
			return false;
		}
	}

	/**
	 * Add/Edit Water Truck Inspection
	 * @since 12/6/2017
	 */
	public function saveWatertruckInspection()
	{
		$idUser = $this->session->userdata("id");
		$idDailyInspection = $this->input->post('hddId');

		$data = array(
			'fk_id_vehicle' => $this->input->post('hddIdVehicle'),
			'belt' => $this->input->post('belt'),
			'power_steering' => $this->input->post('powerSteering'),
			'oil_level' => $this->input->post('oil'),
			'coolant_level' => $this->input->post('coolantLevel'),
			'coolant_leaks' => $this->input->post('coolantLeaks'),
			'head_lamps' => $this->input->post('headLamps'),
			'hazard_lights' => $this->input->post('hazardLights'),
			'clearance_lights' => $this->input->post('clearanceLights'),
			'tail_lights' => $this->input->post('tailLights'),
			'work_lights' => $this->input->post('workLights'),
			'turn_signals' => $this->input->post('turnSignals'),
			'beacon_lights' => $this->input->post('beaconLights'),
			'tires' => $this->input->post('tires'),
			'mirrors' => $this->input->post('mirrors'),
			'clean_exterior' => $this->input->post('cleanExterior'),
			'wipers' => $this->input->post('wipers'),
			'backup_beeper' => $this->input->post('backupBeeper'),
			'door' => $this->input->post('door'),
			'decals' => $this->input->post('decals'),
			'sprinkelrs' => $this->input->post('sprinkelrs'),
			'stering_axle' => $this->input->post('steringAxle'),
			'drives_axles' => $this->input->post('drives'),
			'front_drive' => $this->input->post('frontDrive'),
			'back_drive' => $this->input->post('backDrive'),
			'water_pump' => $this->input->post('waterPump'),
			'brake' => $this->input->post('brake'),
			'emergency_brake' => $this->input->post('emergencyBrake'),
			'gauges' => $this->input->post('gauges'),
			'horn' => $this->input->post('horn'),
			'seatbelt' => $this->input->post('seatbelt'),
			'seat' => $this->input->post('seat'),
			'insurance' => $this->input->post('insurance'),
			'registration' => $this->input->post('registration'),
			'clean_interior' => $this->input->post('cleanInterior'),
			'fire_extinguisher' => $this->input->post('fire'),
			'first_aid' => $this->input->post('aid'),
			'emergency_kit' => $this->input->post('emergencyKit'),
			'spill_kit' => $this->input->post('spillKit'),
			'heater' => $this->input->post('heater'),
			'steering_wheel' => $this->input->post('steering_wheel'),
			'suspension_system' => $this->input->post('suspension_system'),
			'air_brake' => $this->input->post('air_brake'),
			'fuel_system' => $this->input->post('fuel_system'),
			'comments' => $this->input->post('comments')
		);

		//solo usuarios SUPER_ADMIN pueden ingresar la fecha de la inspeccion
		$userRol = $this->session->rol;
		$dateIssue = $this->input->post('date');
		$hora = date("G:i:s");
		if ($userRol == 99 && $dateIssue != "") {
			$data['date_issue'] = $dateIssue . " " . $hora;
		} else {
			$data['date_issue'] = date("Y-m-d G:i:s");
		}

		//revisar si es para adicionar o editar
		if ($idDailyInspection == '') {
			$data['fk_id_user'] = $idUser;

			$query = $this->db->insert('inspection_watertruck', $data);
			$idDailyInspection = $this->db->insert_id();
		} else {
			$this->db->where('id_inspection_watertruck', $idDailyInspection);
			$query = $this->db->update('inspection_watertruck', $data);
		}

		if ($query) {
			return $idDailyInspection;
		} else {
			return false;
		}
	}

	/**
	 * Para guardar registro de la fecha y maquina de todas las inspecciones que se hacen en una sola tabla
	 * @since 17/1/2019
	 */
	public function saveInspectionTotal($idMachine)
	{
		$data = array(
			'fk_id_machine' => $idMachine,
			'date_inspection' => date("Y-m-d")
		);
		$query = $this->db->insert('inspection_total', $data);


		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Actualizo tabla de vehiculos con el seguimiento para mostrar mensaje si hay fallas
	 * @since 26/1/2019
	 */
	public function saveSeguimiento()
	{
		$idVehicle = $this->input->post('hddIdVehicle');

		//revisar luces
		$headLamps = $this->input->post('headLamps');
		$hazardLights = $this->input->post('hazardLights');
		$bakeLights = $this->input->post('bakeLights');
		$workLights = $this->input->post('workLights');
		$turnSignals = $this->input->post('turnSignals');
		$beaconLight = $this->input->post('beaconLight');
		$clearanceLights = $this->input->post('clearanceLights');

		$lights = 1;
		if ($headLamps == 0 || $hazardLights == 0 || $bakeLights == 0 || $workLights == 0 || $turnSignals == 0 || $beaconLight == 0 || $clearanceLights == 0) {
			$lights = 0;
		}

		$data = array(
			'heater_check' => $this->input->post('heater'),
			'brakes_check' => $this->input->post('brakePedal'),
			'lights_check' => $lights,
			'steering_wheel_check' => $this->input->post('steering_wheel'),
			'suspension_system_check' => $this->input->post('suspension_system'),
			'tires_check' => $this->input->post('nuts'),
			'wipers_check' => $this->input->post('wipers'),
			'air_brake_check' => $this->input->post('air_brake'),
			'driver_seat_check' => $this->input->post('passengerDoor'),
			'fuel_system_check' => $this->input->post('fuel_system')
		);

		$this->db->where('id_vehicle', $idVehicle);
		$query = $this->db->update('param_vehicle', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Actualizo tabla de vehiculos con el seguimiento para mostrar mensaje si hay fallas
	 * @since 26/1/2019
	 */
	public function saveSeguimientoHydrovac()
	{
		$idVehicle = $this->input->post('hddIdVehicle');

		//revisar luces
		$headLamps = $this->input->post('headLamps');
		$hazardLights = $this->input->post('hazardLights');
		$clearanceLights = $this->input->post('clearanceLights');
		$tailLights = $this->input->post('tailLights');
		$workLights = $this->input->post('workLights');
		$turnSignals = $this->input->post('turnSignals');
		$beaconLight = $this->input->post('beaconLights');

		$lights = 1;
		if ($headLamps == 0 || $hazardLights == 0 || $tailLights == 0 || $workLights == 0 || $turnSignals == 0 || $beaconLight == 0 || $clearanceLights == 0) {
			$lights = 0;
		}

		$data = array(
			'heater_check' => $this->input->post('heater'),
			'brakes_check' => $this->input->post('brake'),
			'lights_check' => $lights,
			'steering_wheel_check' => $this->input->post('steering_wheel'),
			'suspension_system_check' => $this->input->post('suspension_system'),
			'tires_check' => $this->input->post('tires'),
			'wipers_check' => $this->input->post('wipers'),
			'air_brake_check' => $this->input->post('air_brake'),
			'driver_seat_check' => $this->input->post('door'),
			'fuel_system_check' => $this->input->post('fuel_system')
		);

		$this->db->where('id_vehicle', $idVehicle);
		$query = $this->db->update('param_vehicle', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Actualizo tabla de vehiculos con el seguimiento para mostrar mensaje si hay fallas
	 * @since 26/1/2019
	 */
	public function saveSeguimientoWatertruck()
	{
		$idVehicle = $this->input->post('hddIdVehicle');

		//revisar luces
		$headLamps = $this->input->post('headLamps');
		$hazardLights = $this->input->post('hazardLights');
		$clearanceLights = $this->input->post('clearanceLights');
		$tailLights = $this->input->post('tailLights');
		$workLights = $this->input->post('workLights');
		$turnSignals = $this->input->post('turnSignals');
		$beaconLight = $this->input->post('beaconLights');

		$lights = 1;
		if ($headLamps == 0 || $hazardLights == 0 || $tailLights == 0 || $workLights == 0 || $turnSignals == 0 || $beaconLight == 0 || $clearanceLights == 0) {
			$lights = 0;
		}

		$data = array(
			'heater_check' => $this->input->post('heater'),
			'brakes_check' => $this->input->post('brake'),
			'lights_check' => $lights,
			'steering_wheel_check' => $this->input->post('steering_wheel'),
			'suspension_system_check' => $this->input->post('suspension_system'),
			'tires_check' => $this->input->post('tires'),
			'wipers_check' => $this->input->post('wipers'),
			'air_brake_check' => $this->input->post('air_brake'),
			'driver_seat_check' => $this->input->post('door'),
			'fuel_system_check' => $this->input->post('fuel_system')
		);

		$this->db->where('id_vehicle', $idVehicle);
		$query = $this->db->update('param_vehicle', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}
}
