<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Add PAYROLL
	 * @since 9/11/2016
	 */
	public function savePayroll()
	{
		$idUser = $this->session->userdata("id");
		$idOperation =  $this->input->post('hddTask');
		$idJob =  $this->input->post('jobName');
		$task =  $this->security->xss_clean($this->input->post('taskDescription'));
		$task =  addslashes($task);
		$latitude =  $this->input->post('latitud');
		$longitude =  $this->input->post('longitud');
		$fk_id_programming =  $this->input->post('programming');

		if ($fk_id_programming) {
			//job programming - (active´s items)
			$programming_sql = "SELECT p.fk_id_job, p.fk_id_workorder
					FROM programming p
					WHERE p.id_programming = $fk_id_programming;
					";
			$queryProgramming = $this->db->query($programming_sql);

			$job_programming = null;
			if ($queryProgramming->row_array()) {
				$result = $queryProgramming->row_array();
				$fk_id_job = $result['fk_id_job'];
				$job_programming = $fk_id_job;
			}

			if ($job_programming != $idJob) {
				$fk_id_programming = null;
			}
		} else {
			$fk_id_programming = null;
		}

		$address =  $this->security->xss_clean($this->input->post('address'));
		$address =  addslashes($address);

		$fecha = date("Y-m-d G:i:s");

		if ($fk_id_programming) {
			$sql = "INSERT INTO task";
			$sql .= " (fk_id_user, fk_id_operation, fk_id_job, task_description, start, latitude_start, longitude_start, address_start, fk_id_programming)";
			$sql .= " VALUES ($idUser, $idOperation, $idJob, '$task', '$fecha', $latitude, $longitude, '$address', '$fk_id_programming')";
		} else {
			$sql = "INSERT INTO task";
			$sql .= " (fk_id_user, fk_id_operation, fk_id_job, task_description, start, latitude_start, longitude_start, address_start)";
			$sql .= " VALUES ($idUser, $idOperation, $idJob, '$task', '$fecha', $latitude, $longitude, '$address')";
		}

		$query = $this->db->query($sql);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update PAYROLL - signature
	 * @since 11/11/2016
	 */
	public function updateSignature()
	{
		$idTask =  $this->input->post('idTask');
		$signature =  $this->input->post('output');

		$sql = "UPDATE task";
		$sql .= " SET signature='$signature'";
		$sql .= " WHERE id_task=$idTask";

		$query = $this->db->query($sql);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update PAYROLL - workin time and workin hours
	 * param $adminUpdate: si no envian es porque viene el usuario registrando el cierre, de lo contrariro es porque el administrador esta actualizando
	 * @since 17/11/2016
	 * @review 2/02/2022
	 */
	public function updateWorkingTimePayroll($fechaStart, $fechaCierre, $adminUpdate = 'x')
	{
		$dteStart = new DateTime($fechaStart);
		$dteEnd   = new DateTime($fechaCierre);
		$hours_first_project =  $this->input->post('hours_first_project');
		$idProgramming =  $this->input->post('programming');
		$idTask =  $this->input->post('hddIdentificador');

		$dteDiff  = $dteStart->diff($dteEnd);
		$workingTime = $dteDiff->format("%R%a days %H:%I:%S"); //days hours:minutes:seconds

		//START hours calculation
		$overtimeHours = 0;
		$workingHours = calculate_time_difference_in_hours($fechaStart, $fechaCierre);
		if ($workingHours > 8) {
			$regularHours = 8;
			$overtimeHours = $workingHours - 8;
		} else {
			$regularHours = $workingHours;
		}

		log_message('debug', 'workingHours: ' . print_r($workingHours, true));
		log_message('debug', 'hours_first_project: ' . print_r($hours_first_project, true));	

		if ($idProgramming) {
			$hours_first_project = ($hours_first_project) ? $hours_first_project : 0;
			$hours_end_project = $workingHours - $hours_first_project;
		} else {
			$hours_first_project = ($hours_first_project) ? $hours_first_project : 0;
			$hours_end_project = $workingHours - $hours_first_project;
		}

		$sqlTask = "select fk_id_job from task where id_task = $idTask";
		$queryTask = $this->db->query($sqlTask);
		$resultTask = $queryTask->row_array();
		$fk_id_job = $resultTask['fk_id_job'];

		if ($this->input->post('jobName') == $fk_id_job) {
			$hours_end_project = null;
		}
		//FINISH hours calculation

		//New cal hours
		$hoursNEW = $dteDiff->h + ($dteDiff->days * 24);
		$minutesNEW = $dteDiff->i;
		$secondsNEW = $dteDiff->s;
		$formatNEW = sprintf("%02d:%02d:%02d", $hoursNEW, $minutesNEW, $secondsNEW);

		$newOvertimeHours = 0;
		if ($hoursNEW >= 8) {
			$newRegularHours =  '08:00';
			$hoursNEW = $hoursNEW - 8;
			$newOvertimeHours = sprintf("%02d:%02d:%02d", $hoursNEW, $minutesNEW, $secondsNEW);
		} else {
			$newRegularHours = $formatNEW;
		}
		//FINISH New cal hours

		if ($adminUpdate == 'x') {
			$idJob =  $this->input->post('jobName');
			$observation =  $this->security->xss_clean($this->input->post('observation'));
			$observation =  addslashes($observation);
			$latitude =  $this->input->post('latitud');
			$longitude =  $this->input->post('longitud');

			$address =  $this->security->xss_clean($this->input->post('address'));
			$address =  addslashes($address);

			$sql = "UPDATE task";
			$sql .= " SET observation='$observation', finish =  '$fechaCierre', fk_id_job_finish='$idJob', latitude_finish = $latitude, longitude_finish = $longitude, address_finish = '$address', working_time='$workingTime', working_hours =  $workingHours, working_hours_new =  '$formatNEW', regular_hours =  $regularHours,
			regular_hours_new =  '$newRegularHours', overtime_hours =  $overtimeHours, overtime_hours_new =  '$newOvertimeHours', hours_end_project =  '$hours_end_project', hours_start_project =  '$hours_first_project'";
			$sql .= " WHERE id_task=$idTask";
		} else {
			$sql = "UPDATE task";
			$sql .= " SET working_time='$workingTime', working_hours =  $workingHours, working_hours_new = '$formatNEW', regular_hours =  $regularHours, regular_hours_new =  '$newRegularHours', overtime_hours =  $overtimeHours, overtime_hours_new =  '$newOvertimeHours', hours_start_project =  '$hours_first_project', hours_end_project =  '$hours_end_project'";
			$sql .= " WHERE id_task=$idTask";
		}

		if ($idProgramming) {

			//job programming - (active´s items)
			$programming_sql = "SELECT p.fk_id_job, p.fk_id_workorder
					FROM programming p
					WHERE p.id_programming = $idProgramming;";
					
			$queryProgramming = $this->db->query($programming_sql);

			$job_programming = null;
			$id_workorder = null;
			if ($queryProgramming->row_array()) {
				$result = $queryProgramming->row_array();
				$job_programming = $result['fk_id_job'];
				$id_workorder = $result['fk_id_workorder'];
			}

			$idJob =  $this->input->post('jobName');
			$idUser = $this->session->userdata("id");

			if ($hours_first_project == 0) {

				if ($idJob == $job_programming && $id_workorder != null) {
					$request = "SELECT * FROM workorder_personal W WHERE W.fk_id_workorder  = '$id_workorder' AND W.fk_id_user = '$idUser'";
					$query = $this->db->query($request);
		
					if ($query->num_rows() >= 1) {
		
						if ($query->row()->hours == 0) {
							$data = array(
								'hours' => $workingHours,
							);
			
							$this->db->where('fk_id_workorder  ', $id_workorder);
							$this->db->where('fk_id_user  ', $idUser);
							$query = $this->db->update('workorder_personal', $data);
						}
					} else {
						$data = array(
							'fk_id_workorder' => $id_workorder,
							'fk_id_user' => $idUser,
							'fk_id_employee_type' => 1,
							'hours' => $workingHours
						);
		
						$this->db->insert('workorder_personal', $data);
					}

					//update TASK with wo
					$data = array(
						'wo_start_project' => $id_workorder,
						'wo_end_project' => $id_workorder,
					);
					$this->db->where('id_task  ', $idTask);
					$query = $this->db->update('task', $data);
				}
			} else {

				if ($id_workorder != null) {
					$request = "SELECT * FROM workorder_personal W WHERE W.fk_id_workorder  = '$id_workorder' AND W.fk_id_user = '$idUser'";
					$query = $this->db->query($request);
		
					if ($query->num_rows() >= 1) {
		
						if ($query->row()->hours == 0) {
							$data = array(
								'hours' => $hours_first_project,
							);
			
							$this->db->where('fk_id_workorder  ', $id_workorder);
							$this->db->where('fk_id_user  ', $idUser);
							$query = $this->db->update('workorder_personal', $data);
						}
					}
				}

				//update TASK with wo
				$data = array(
					'wo_start_project' => $id_workorder,
				);
				$this->db->where('id_task  ', $idTask);
				$query = $this->db->update('task', $data);
			}
		}

		$query = $this->db->query($sql);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update PAYROLL - from Payroll check
	 * @since 19/08/2025
	 */
	public function updateWorkingTimePayrollCheck($fechaStart, $fechaCierre, $idTask)
	{
		$dteStart = new DateTime($fechaStart);
		$dteEnd   = new DateTime($fechaCierre);

		$dteDiff  = $dteStart->diff($dteEnd);
		$workingTime = $dteDiff->format("%R%a days %H:%I:%S"); //days hours:minutes:seconds
		
		//START hours calculation
		$overtimeHours = 0;
		$workingHours = calculate_time_difference_in_hours($fechaStart, $fechaCierre);
		if ($workingHours > 8) {
			$regularHours = 8;
			$overtimeHours = $workingHours - 8;
		} else {
			$regularHours = $workingHours;
		}
		//FINISH hours calculation

		//New cal hours
		$hoursNEW = $dteDiff->h + ($dteDiff->days * 24);
		$minutesNEW = $dteDiff->i;
		$secondsNEW = $dteDiff->s;
		$formatNEW = sprintf("%02d:%02d:%02d", $hoursNEW, $minutesNEW, $secondsNEW);

		$newOvertimeHours = 0;
		if ($hoursNEW >= 8) {
			$newRegularHours =  '08:00';
			$hoursNEW = $hoursNEW - 8;
			$newOvertimeHours = sprintf("%02d:%02d:%02d", $hoursNEW, $minutesNEW, $secondsNEW);
		} else {
			$newRegularHours = $formatNEW;
		}
		//FINISH New cal hours

		$observation = "********************<br><strong>Changue hour by the system, automatically.</strong><br>********************";
		$sql = "UPDATE task";
		$sql .= " SET observation='$observation', finish =  '$fechaCierre', working_time='$workingTime', working_hours =  $workingHours, working_hours_new =  '$formatNEW', regular_hours =  $regularHours, regular_hours_new =  '$newRegularHours', overtime_hours =  $overtimeHours, overtime_hours_new =  '$newOvertimeHours'";
		$sql .= " WHERE id_task=$idTask";
		$query = $this->db->query($sql);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Insert PAYROLL
	 * @since 2/02/2022
	 */
	public function insertWorkingTimePayroll($fechaStart, $fechaCierre, $taskInfo = 'x')
	{
		$dteStart = new DateTime($fechaStart);
		$dteEnd   = new DateTime($fechaCierre);

		$dteDiff  = $dteStart->diff($dteEnd);
		$workingTime = $dteDiff->format("%R%a days %H:%I:%S"); //days hours:minutes:seconds

		//START hours calculation
		$minutes = (strtotime($fechaStart) - strtotime($fechaCierre)) / 60;
		$minutes = abs($minutes);
		$minutes = round($minutes);

		$hours = $minutes / 60;
		$hours = round($hours, 2);

		$justHours = intval($hours);
		$decimals = $hours - $justHours;

		//Ajuste de los decimales para redondearlos a .25 / .5 / .75
		if ($decimals < 0.12) {
			$transformation = 0;
		} elseif ($decimals >= 0.12 && $decimals < 0.37) {
			$transformation = 0.25;
		} elseif ($decimals >= 0.37 && $decimals < 0.62) {
			$transformation = 0.5;
		} elseif ($decimals >= 0.62 && $decimals < 0.87) {
			$transformation = 0.75;
		} elseif ($decimals >= 0.87) {
			$transformation = 1;
		}
		$workingHours = $justHours + $transformation;
		$overtimeHours = 0;
		if ($workingHours > 8) {
			$regularHours = 8;
			$overtimeHours = $workingHours - 8;
		} else {
			$regularHours = $workingHours;
		}
		//FINISH hours calculation
		$idUser = $this->session->userdata("id");
		$idTask =  $this->input->post('hddIdentificador');
		$idJob =  $this->input->post('jobName');
		$observation =  $this->security->xss_clean($this->input->post('observation'));
		$observation =  addslashes($observation);
		$latitude =  $this->input->post('latitud');
		$longitude =  $this->input->post('longitud');
		$address =  $this->security->xss_clean($this->input->post('address'));
		$address =  addslashes($address);

		if ($taskInfo != 'x') {
			$idUser = $taskInfo['fk_id_user'];
			$idTask = $taskInfo['id_task'];
			$idJob = $taskInfo['fk_id_job'];
			$observation = "********************<br><strong>Changue hour by the system, automatically.</strong><br>********************";
			$latitude =  0;
			$longitude =  0;
			$address = '';
		}

		$sql = "INSERT INTO task";
		$sql .= " (fk_id_user, fk_id_operation, fk_id_job, task_description, start, latitude_start, longitude_start, address_start,
							observation, finish, fk_id_job_finish, latitude_finish, longitude_finish, address_finish, working_time, working_hours, 
							regular_hours, overtime_hours)";
		$sql .= " VALUES ($idUser, 1, $idJob, '', '$fechaStart', $latitude, $longitude, '$address', '$observation', '$fechaCierre', '$idJob', 
								$latitude, $longitude, '$address', '$workingTime', $workingHours, $regularHours, $overtimeHours)";

		$query = $this->db->query($sql);
		$idtask = $this->db->insert_id();

		if ($query) {
			return $idtask;
		} else {
			return false;
		}
	}

	/**
	 * Consulta BASICA A UNA TABLA
	 * @since 17/11/2016
	 */
	public function get_taskbyid($id)
	{
		$this->db->where("id_task", $id);
		$query = $this->db->get("task");

		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else
			return false;
	}

	/**
	 * Update payroll hour
	 * @since 2/2/2018
	 */
	public function savePayrollHour()
	{
		$idTask = $this->input->post('hddIdentificador');
		$inicio = $this->input->post('hddInicio');
		$fin = $this->input->post('hddFin');

		$firstObservation =  $this->security->xss_clean($this->input->post('hddObservation'));
		$firstObservation =  addslashes($firstObservation);

		$observation =  $this->security->xss_clean($this->input->post('observation'));
		$observation =  addslashes($observation);

		$moreInfo = "<strong>Change hour by SUPER ADMIN.</strong> <br>Before -> Start: " . $inicio . " <br>Before -> Finish: " . $fin;
		$observation = $firstObservation . "<br>********************<br>" . $moreInfo . "<br>" . $observation . "<br>Date: " . date("Y-m-d G:i:s") . "<br>********************";

		$fechaStart = $this->input->post('start_date');
		$horaStart = $this->input->post('start_hour');
		$minStart = $this->input->post('start_min');
		$fechaFinish = $this->input->post('finish_date');
		$horaFinish = $this->input->post('finish_hour');
		$minFinish = $this->input->post('finish_min');

		$fechaStart = $fechaStart . " " . $horaStart . ":" . $minStart . ":00";
		$fechaFinish = $fechaFinish . " " . $horaFinish . ":" . $minFinish . ":00";

		$sql = "UPDATE task";
		$sql .= " SET observation='$observation', finish =  '$fechaFinish', start='$fechaStart'";
		$sql .= " WHERE id_task=$idTask";

		$query = $this->db->query($sql);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add PERIOD
	 * @since 9/02/2022
	 */
	public function savePeriod($arrData)
	{
		$period = $arrData["periodoIniNew"] . " - " . $arrData["periodoFinNew"];

		$data = array(
			'date_start' => $arrData["periodoIniNew"],
			'date_finish' => $arrData["periodoFinNew"],
			'period' => $period,
			'year_period' => $arrData["yearPeriodo"]
		);

		$query = $this->db->insert('payroll_period', $data);
		$idPeriod = $this->db->insert_id();

		if ($query) {
			return $idPeriod;
		} else {
			return false;
		}
	}

	/**
	 * Add Weak PERIOD
	 * @since 9/02/2022
	 */
	public function saveWeakPeriod($arrData)
	{
		$weak1 = $arrData["semana1IniNew"] . " - " . $arrData["semana1FinNew"];
		$weak2 = $arrData["semana2IniNew"] . " - " . $arrData["semana2FinNew"];

		$data = array(
			'fk_id_period' => $arrData["idPeriod"],
			'date_weak_start' => $arrData["semana1IniNew"],
			'date_weak_finish' => $arrData["semana1FinNew"],
			'period_weak' => $weak1,
			'weak_number' => 1
		);
		$query = $this->db->insert('payroll_period_weaks', $data);

		$data = array(
			'fk_id_period' => $arrData["idPeriod"],
			'date_weak_start' => $arrData["semana2IniNew"],
			'date_weak_finish' => $arrData["semana2FinNew"],
			'period_weak' => $weak2,
			'weak_number' => 2
		);
		$query = $this->db->insert('payroll_period_weaks', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save paystub
	 * @since 21/2/2022
	 */
	public function savePaystub()
	{
		$idUser = $this->session->userdata("id");
		$idPaytsub = $this->input->post('hddIdPaytsub');
		$costRegularSalary = $this->input->post('hddCostRegularSalary');
		$costOvertime = $this->input->post('hddCostOvertime');
		$costVacation = $this->input->post('hddCostVacation');
		$totalIncome = $costRegularSalary + $costOvertime;
		$gross_salary = $totalIncome + $costVacation;
		$er_cpp = $ee_cpp = $this->input->post('ee_cpp');
		$ee_ei = $this->input->post('ee_ei');
		$er_ei = $ee_ei * 1.4;
		$tax = $this->input->post('tax');
		$gwl_deductions = $this->input->post('gwl_deductions');
		$ee_total_taxes = $ee_cpp + $ee_ei + $tax;
		$remittance = $ee_cpp + $er_cpp + $ee_ei + $er_ei + $tax;
		$net_pay = $gross_salary - $ee_total_taxes - $gwl_deductions;
		$btnGenerate =  $this->input->post('btnGenerate');
		$valueCommit = 2;
		if (isset($btnGenerate)) {
			$valueCommit = 1;
		}

		$data = array(
			'employee_rate_paystub' => $this->input->post('hddEmployeeRate'),
			'employee_type_paystub' => $this->input->post('hddEmployeeType'),
			'total_worked_hours' => $this->input->post('hddTotalWorkedHours'),
			'total_regular_hours' => $this->input->post('hddRegularHours'),
			'total_overtime_hours' => $this->input->post('hddOvertimeHours'),
			'cost_regular_salary' => $costRegularSalary,
			'cost_over_time' => $costOvertime,
			'total_income' => $totalIncome,
			'cost_vacation_regular_salary' => $costVacation,
			'gross_salary' => $gross_salary,
			'ee_cpp' => $ee_cpp,
			'er_cpp' => $er_cpp,
			'ee_ei' => $ee_ei,
			'er_ei' => $er_ei,
			'tax' => $tax,
			'ee_total_taxes' => $ee_total_taxes,
			'gwl_deductions' => $gwl_deductions,
			'remittance' => $remittance,
			'net_pay' => $net_pay,
			'commit' => $valueCommit
		);

		//revisar si es para adicionar o editar
		if ($idPaytsub == '') {
			$data['paystub_date_issue'] = date('Y-m-d');
			$data['paystub_fk_id_user'] = $idUser;
			$data['fk_id_period'] = $this->input->post('hddIdPeriod');
			$data['fk_id_employee'] = $this->input->post('hddIdUser');
			$data['actual_bank_time_balance'] = $this->input->post('hddBankTimeBalance');
			$query = $this->db->insert('payroll_paystub', $data);
		} else {
			$this->db->where('id_paystub', $idPaytsub);
			$query = $this->db->update('payroll_paystub', $data);
		}
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update task estatus
	 * @since 27/2/2022
	 */
	public function updateTaskStatus()
	{
		$idPeriod =  $this->input->post('period');
		$idEmployee =  $this->input->post('hddIdUser');
		$idWeak1 =  $this->input->post('hddIdWeakPeriod1');
		$idWeak2 =  $this->input->post('hddIdWeakPeriod2');

		$idWeaks = array($idWeak1, $idWeak2);
		$data['period_status'] = 2;

		$this->db->where('fk_id_user', $idEmployee);
		$this->db->where_in('fk_id_weak_period', $idWeaks);
		$query = $this->db->update('task', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Ingreso los datos de totales por año
	 * @since 27/2/2022
	 */
	public function updatePayrollTotalYearly($arrData)
	{
		$idTotalYearly = $this->input->post('hddIdTotalYearly');

		$workedHours = $this->input->post('hddTotalWorkedHours');
		$regularHours = $this->input->post('hddRegularHours');
		$overtimeHours = $this->input->post('hddOvertimeHours');
		$costRegularSalary = $this->input->post('hddCostRegularSalary');
		$costOvertime = $this->input->post('hddCostOvertime');
		$costVacation = $this->input->post('hddCostVacation');
		$totalIncome = $costRegularSalary + $costOvertime;
		$gross_salary = $totalIncome + $costVacation;
		$er_cpp = $ee_cpp = $this->input->post('ee_cpp');
		$ee_ei = $this->input->post('ee_ei');
		$er_ei = $ee_ei * 1.4;
		$tax = $this->input->post('tax');
		$gwl_deductions = $this->input->post('gwl_deductions');
		$ee_total_taxes = $ee_cpp + $ee_ei + $tax;
		$remittance = $ee_cpp + $er_cpp + $ee_ei + $er_ei + $tax;
		$net_pay = $gross_salary - $ee_total_taxes - $gwl_deductions;

		if ($arrData) {
			$TOTALworkedHours = $workedHours + $arrData[0]['total_year_worked_hours'];
			$TOTALregularHours = $regularHours + $arrData[0]['total_year_regular_hours'];
			$TOTALovertimeHours = $overtimeHours + $arrData[0]['total_year_overtime_hours'];
			$TOTALcostRegularSalary = $costRegularSalary + $arrData[0]['total_year_cost_regular_salary'];
			$TOTALcostOvertime = $costOvertime + $arrData[0]['total_year_cost_over_time'];
			$TOTALcostVacation = $costVacation + $arrData[0]['total_year_cost_vacation_regular_salary'];
			$TOTALgross_salary = $gross_salary + $arrData[0]['total_year_gross_salary'];
			$TOTALee_cpp = $ee_cpp + $arrData[0]['total_year_ee_cpp'];
			$TOTALer_cpp = $er_cpp + $arrData[0]['total_year_er_cpp'];
			$TOTALee_ei = $ee_ei + $arrData[0]['total_year_ee_ei'];
			$TOTALer_ei = $er_ei + $arrData[0]['total_year_er_ei'];
			$TOTALtax = $tax + $arrData[0]['total_year_tax'];
			$TOTALeetax = $ee_total_taxes + $arrData[0]['total_year_ee_total_taxes'];
			$TOTALgwl_deductions = $gwl_deductions + $arrData[0]['total_year_gwl_deductions'];
			$TOTALremittance = $remittance + $arrData[0]['total_year_remittance'];
			$TOTALnet_pay = $net_pay + $arrData[0]['total_year_net_pay'];
		} else {
			$TOTALworkedHours = $workedHours;
			$TOTALregularHours = $regularHours;
			$TOTALovertimeHours = $overtimeHours;
			$TOTALcostRegularSalary = $costRegularSalary;
			$TOTALcostOvertime = $costOvertime;
			$TOTALcostVacation = $costVacation;
			$TOTALgross_salary = $gross_salary;
			$TOTALee_cpp = $ee_cpp;
			$TOTALer_cpp = $er_cpp;
			$TOTALee_ei = $ee_ei;
			$TOTALer_ei = $er_ei;
			$TOTALtax = $tax;
			$TOTALeetax = $ee_total_taxes;
			$TOTALgwl_deductions = $gwl_deductions;
			$TOTALremittance = $remittance;
			$TOTALnet_pay = $net_pay;
		}


		$data = array(
			'total_year_worked_hours' => $TOTALworkedHours,
			'total_year_regular_hours' => $TOTALregularHours,
			'total_year_overtime_hours' => $TOTALovertimeHours,
			'total_year_cost_regular_salary' => $TOTALcostRegularSalary,
			'total_year_cost_over_time' => $TOTALcostOvertime,
			'total_year_cost_vacation_regular_salary' => $TOTALcostVacation,
			'total_year_gross_salary' => $TOTALgross_salary,
			'total_year_ee_cpp' => $TOTALee_cpp,
			'total_year_er_cpp' => $TOTALer_cpp,
			'total_year_ee_ei' => $TOTALee_ei,
			'total_year_er_ei' => $TOTALer_ei,
			'total_year_tax' => $TOTALtax,
			'total_year_ee_total_taxes' => $TOTALeetax,
			'total_year_gwl_deductions' => $TOTALgwl_deductions,
			'total_year_remittance' => $TOTALremittance,
			'total_year_net_pay' => $TOTALnet_pay
		);

		//revisar si es para adicionar o editar
		if ($idTotalYearly == '') {
			$data['year'] = $this->input->post('hddYear');
			$data['fk_id_employee'] = $this->input->post('hddIdUser');
			$query = $this->db->insert('payroll_total_yearly ', $data);
		} else {
			$this->db->where('id_total_yearly', $idTotalYearly);
			$query = $this->db->update('payroll_total_yearly ', $data);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Paystub by period
	 * Modules: Payroll/search
	 * @since 25/02/2022
	 */
	public function get_paystub_by_period($arrData)
	{
		$this->db->select("P.*, CONCAT(U.first_name, ' ', U.last_name) name, CONCAT(W.first_name, ' ', W.last_name) employee, W.address, W.postal_code, X.date_start,X.date_finish");
		$this->db->join('user U', 'U.id_user = P.paystub_fk_id_user', 'INNER');
		$this->db->join('user W', 'W.id_user = P.fk_id_employee', 'INNER');
		$this->db->join('payroll_period X', 'X.id_period = P.fk_id_period', 'INNER');

		if (array_key_exists("idPaytsub", $arrData)) {
			$this->db->where('P.id_paystub', $arrData["idPaytsub"]);
		}
		if (array_key_exists("idEmployee", $arrData) && $arrData["idEmployee"] != '') {
			$this->db->where('P.fk_id_employee', $arrData["idEmployee"]);
		}
		if (array_key_exists("idPeriod", $arrData)) {
			$this->db->where('P.fk_id_period', $arrData["idPeriod"]);
		}
		if (array_key_exists("year", $arrData)) {
			$this->db->where('X.year_period', $arrData["year"]);
		}
		$this->db->order_by('W.first_name, W.last_name, P.fk_id_period', 'asc');
		$query = $this->db->get('payroll_paystub P');

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * In progress service order
	 * @since 1/7/2023
	 */
	public function get_in_progress_service_order()
	{
		$this->db->select('T.*');
		$this->db->join('service_order_time T', 'T.fk_id_service_order = S.id_service_order', 'LEFT');
		$this->db->where('S.service_status', 'in_progress_so');
		$this->db->where('S.fk_id_assign_to', 1);
		$query = $this->db->get('service_order S');

		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Update SERVICE ORDER TIME
	 * @since 1/7/2023
	 */
	public function updateServiceOrderTime($infoServiceOrder, $path)
	{
		$date = date("Y-m-d G:i:s");
		if ($path == "start") {
			$data = array('time_date' => $date);
		} else {
			$minutes = (strtotime($infoServiceOrder["time_date"]) - strtotime($date)) / 60;
			$minutes = abs($minutes);
			$minutes = round($minutes);

			$hours = $minutes / 60;
			$hours = round($hours, 2) + $infoServiceOrder["time"];

			$data = array('time' => $hours);
		}
		$this->db->where('id_time', $infoServiceOrder["id_time"]);
		$query = $this->db->update('service_order_time', $data);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update TASK CON WO
	 * @since 17/02/2025
	 */
	public function updateTaskWithWO()
	{
		$idTask =  $this->input->post('hddIdentificador');

		$data['fk_id_job'] = $this->input->post('jobName');
		$data['hours_start_project'] = $this->input->post('hours_first_project');
		$data['fk_id_job_finish'] = $this->input->post('jobNameFinish');
		$data['hours_end_project'] = $this->input->post('hours_last_project');

		$this->db->where('id_task', $idTask);
		$query = $this->db->update('task', $data);

		$task = $this->get_taskbyid($idTask);

		if ($task['wo_start_project']) {
			$dataWoStar = array(
				'hours' => $data['hours_start_project'],
			);

			$this->db->where('fk_id_workorder  ', $task['wo_start_project']);
			$this->db->where('fk_id_user  ', $task['fk_id_user']);
			$this->db->update('workorder_personal', $dataWoStar);
		}

		if ($task['wo_end_project']) {
			$dataWoEnd = array(
				'hours' => $data['hours_end_project'],
			);

			$this->db->where('fk_id_workorder  ', $task['wo_end_project']);
			$this->db->where('fk_id_user  ', $task['fk_id_user']);
			$this->db->update('workorder_personal', $dataWoEnd);
		}

		if ($query) {
			return true;
		} else {
			return false;
		}
	}
}
