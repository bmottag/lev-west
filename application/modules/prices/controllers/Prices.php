<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prices extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model("prices_model");
    }
	
	/**
	 * Lista de precios para tipos de empleados
     * @since 5/11/2020
     * @author BMOTTAG
	 */
	public function employeeTypeUnitPrice($idJob)
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

			//job_employee_type_unit_price list
			$data['employeeTypeUnitPrice'] = $this->general_model->get_job_employee_type_unit_price($idJob);				

			$data["view"] = "employeeTypeUnitPrice_list";
			$this->load->view("layout", $data);
	}
	
	/**
	 * Load employee types
     * @since 5/11/2020
     * @author BMOTTAG
	 */
	public function load_employee_type()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$data["idJob"] = $this->input->post('identificador');
			
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "param_employee_type",
				"order" => "employee_type",
				"id" => "x"
			);
			$employeeTypeUnitPrice = $this->general_model->get_basic_search($arrParam);			

//falta cargar historial de estos cambios

			if ($this->prices_model->add_employee_type($employeeTypeUnitPrice)) 
			{				
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}				

			echo json_encode($data);
    }
	
	/**
	 * Update the price of each field
     * @since 6/11/2020
     * @author BMOTTAG
	 */
	public function update_employee_type_price()
	{	
			$idJob = $this->input->post('hddIdJob');
	
			if ($this->prices_model->updateEmployeeTypePrice()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the Employee Type Unit Price!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url("prices/employeeTypeUnitPrice/$idJob"), 'refresh');
	}
	
	/**
	 * Equipment list
     * @since 6/11/2020
     * @author BMOTTAG
	 */
	public function equipmentList($companyType)
	{
			$data['vehicleState'] = 1;
			$data['companyType'] = $companyType;
			$data['title'] = $companyType==1?"VCI":"RENTALS";

			$this->load->model("general_model");
			$arrParam = array(
				"companyType" => $companyType,
				"vehicleState" => $data['vehicleState']
			);
			
			$data['info'] = $this->general_model->get_equipment_info_by($arrParam);//vehicle list
			
			$data["view"] = 'equipment_list';
			$this->load->view("layout", $data);
	}
	
	/**
	 * Update the price of each field
     * @since 6/11/2020
     * @author BMOTTAG
	 */
	public function update_equipment_price()
	{	
			$companyType = $this->input->post('hddIdCompanyType');
			
			if ($this->prices_model->updateEquipmentPrice()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the Equipment Prices!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url("prices/equipmentList/$companyType"), 'refresh');
	}
	
	/**
	 * Lista de precios de quipos por hora para un proyecto
     * @since 5/11/2020
     * @author BMOTTAG
	 */
	public function equipmentUnitPrice($idJob, $companyType)
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

			//job_equipment unit price list			
			$data['vehicleState'] = 1;
			$data['companyType'] = $companyType;
			$data['title'] = $companyType==1?"VCI":"RENTALS";

			$arrParam = array(
				"companyType" => $companyType,
				"vehicleState" => $data['vehicleState'],
				"idJob" => $idJob
			);
			$data['equipmentUnitPrice'] = $this->general_model->get_equipment_price($arrParam);				

			$data["view"] = "equipmentPrice_list";
			$this->load->view("layout", $data);
	}
	
	/**
	 * Load equipment
     * @since 7/11/2020
     * @author BMOTTAG
	 */
	public function load_equipment()
	{			
			header('Content-Type: application/json');
			$data = array();

			$data["idJob"] = $this->input->post('identificador');
				
			$data['vehicleState'] = 1;

			$this->load->model("general_model");
			$arrParam = array(
				"vehicleState" => $data['vehicleState']
			);	
			$equipmentUnitPrice = $this->general_model->get_equipment_info_by($arrParam);//vehicle list

//falta cargar historial de estos cambios

			if ($this->prices_model->add_equipment($equipmentUnitPrice)) 
			{				
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have loaded the data.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}				

			echo json_encode($data);
    }
	
	/**
	 * Update Equipment price of each field for job
     * @since 7/11/2020
     * @author BMOTTAG
	 */
	public function update_job_equipment_price()
	{	
			$idJob = $this->input->post('hddIdJob');
			$companyType = $this->input->post('hddIdCompanyType');
	
			if ($this->prices_model->updateJobEquipmentPrice()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the Employee Type Unit Price!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url("prices/equipmentUnitPrice/$idJob/$companyType"), 'refresh');
	}
	
	/**
	 * Update the price of each field
     * @since 13/11/2020
     * @author BMOTTAG
	 */
	public function update_general_employee_type_price()
	{	
			if ($this->prices_model->updateGeneralEmployeeTypePrice()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the Employee Type Unit Price!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url("admin/employeeType"), 'refresh');
	}

	/**
	 * Update the price of each field - Material
     * @since 18/12/2020
     * @author BMOTTAG
	 */
	public function update_general_material_price()
	{	
			if ($this->prices_model->updateGeneralMaterialPrice()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "You have updated the Material Price!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url("admin/material"), 'refresh');
	}

	
	
}