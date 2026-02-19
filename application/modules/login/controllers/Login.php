<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("login_model");
		$this->load->helper("cookie");
    }

	/**
	 * Index Page for this controller.
	 * @param int $id: id del vehiculo encriptado para el hauling
	 */
	public function index($id = 'x', $module = 'x', $idModule = 'x')
	{
			$this->session->sess_destroy();
			$data['idVehicle'] = FALSE;
			$data['inspectionType'] = FALSE;
			$data['moduleInfo'] = $module;
			$data['idModule'] = $idModule;
			$this->load->model("general_model");
			//si envio llave encriptada, entonces busco el ID del vehiculo para pasarlo a la vista
			if ($id != 'x') {				
				$arrParam['encryption'] = $id;
				$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

				$data['idVehicle'] = $data['vehicleInfo'][0]['id_vehicle'];	
				$data['inspectionType'] = $data['vehicleInfo'][0]['inspection_type'];					
			}
			if ($module != 'x') {
				$module = base64_decode($module);
				$arrParam = array(
					"table" => "param_module",
					"order" => "id_module",
					"column" => "module_tag",
					"id" => $module 
				);
				$moduleInfo = $this->general_model->get_basic_search($arrParam);
				$data['moduleInfo'] = $moduleInfo[0]['module_url'];
			}
			$this->load->view('login', $data);
	}
	
	public function validateUser()
	{
			$login = $this->security->xss_clean($this->input->post("inputLogin"));
			$passwd = $this->security->xss_clean($this->input->post("inputPassword"));
			$data['idVehicle'] = $this->input->post("hddId");
			$data['inspectionType'] = $this->input->post("hddInpectionType");	
			$data['moduleInfo'] = 'x';
			$data['idModule'] = 'x';		
			$this->load->model("general_model");
			
			//busco informacion del vehiculo si existe
			$data['linkInspection'] = FALSE;
			$data['formInspection'] = FALSE;
			
			if ($data['idVehicle'] != 'x') {				
				$arrParam['idVehicle'] = $data['idVehicle'];
				$data['vehicleInfo'] = $this->general_model->get_vehicle_by($arrParam);

				if (!empty($data['vehicleInfo']) && is_array($data['vehicleInfo'])) {
					$data['linkInspection'] = $data['vehicleInfo'][0]['link_inspection'];	
					$data['formInspection'] = $data['vehicleInfo'][0]['form'];
				}
			}

			//busco datos del vehiculo
			$arrParam = array(
				"table" => "user",
				"order" => "id_user",
				"column" => "log_user",
				"id" => $login
			);
			
			$userExist = $this->general_model->get_basic_search($arrParam);

			if ($userExist)
			{
					$arrParam = array(
						"login" => $login,
						"passwd" => $passwd
					);
					$user = $this->login_model->validateLogin($arrParam); //brings user information from user table

					if(($user["valid"] == true)) 
					{
						$userRol = intval($user["rol"]);
						//busco url del dashboard de acuerdo al rol del usuario
						$arrParam = array(
							"idRol" => $userRol
						);
						$rolInfo = $this->general_model->get_roles($arrParam);

						$sessionData = array(
							"auth" => "OK",
							"id" => $user["id"],
							"dashboardURL" => $rolInfo[0]["dashboard_url"],
							"firstname" => $user["firstname"],
							"lastname" => $user["lastname"],
							"name" => $user["firstname"] . ' ' . $user["lastname"],
							"logUser" => $user["logUser"],
							"state" => $user["state"],
							"rol" => $user["rol"],
							"bankTime" => $user["bankTime"],
							"photo" => $user["photo"],
							"idVehicle" => $data['idVehicle'],
							"inspectionType" => $data['inspectionType'],
							"linkInspection" => $data['linkInspection'],
							"formInspection" => $data['formInspection']
						);
												
						$this->session->set_userdata($sessionData);
						//cookies
						set_cookie('user',$login, '350000'); 
						set_cookie('password',$passwd,'350000'); 
						
						$this->login_model->redireccionarUsuario();
					}else{					
						$data["msj"] = "<strong>" . $userExist[0]["first_name"] . "</strong> that's not your password.";
						$this->session->sess_destroy();
						$this->load->view('login', $data);
					}
			}else{
				$data["msj"] = "<strong>" . $login . "</strong> doesn't exist.";
				$this->session->sess_destroy();
				$this->load->view('login', $data);
			}
	}
	
	
}
