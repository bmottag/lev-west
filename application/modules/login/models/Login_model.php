<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Login_model extends CI_Model {

	    function __construct(){        
	        parent::__construct();
     
	    }
	    
	    /**
	     * Valida los datos del formulario de ingreso (login y password) contra la base de datos del aplicativo
	     * @author BMOTTAG
	     * @since  8/11/2016
	     */
	    public function validateLogin($arrData)
		{
	    	$user = array();
	    	$user["valid"] = false;
			
	    	$login = str_replace(array("<",">","[","]","*","^","-","'","="),"",$arrData["login"]);   
	    	$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$arrData["passwd"]); 
			$passwd = md5($passwd);
			
	    	$sql = "SELECT * FROM user WHERE log_user = '$login' and password = '$passwd'";
	    	$query = $this->db->query($sql);

	    	if ($query->num_rows() > 0){	    		
	    		//$encrypt = $this->danecrypt->encode($passwd);
	    		foreach($query->result() as $row){
	    			//if (strcmp($row->PAS_USUARIO, $encrypt)===0){
	    				$user["valid"] = true;
	    				$user["id"] = $row->id_user;
	    				$user["firstname"] = $row->first_name;
	    				$user["lastname"] = $row->last_name;
						$user["logUser"] = $row->log_user;
	    				$user["movil"] = $row->movil;
						$user["state"] = $row->state;
						$user["rol"] = $row->perfil;
						$user["bankTime"] = $row->bank_time;
						$user["photo"] = $row->photo;
	    			//}	    			
	    		}
	    	}
			
			//var_dump($user); die();
	    	$this->db->close();	    	
	    	return $user;
	    }
		
	    /**
	     * Redirecciona el usuario al módulo correspondiente dependiendo de los datos almacenados en la session
	     * @author BMOTTAG
	     * @since  8/11/2016
		 * @review  18/12/2016
	     */
	    public function redireccionarUsuario()
		{
			$idVehicle = $this->session->userdata("idVehicle");
			$inspectionType = $this->session->userdata("inspectionType");
			$linkInspection = $this->session->userdata("linkInspection");
			$state = $this->session->userdata("state");
			$dashboardURL = $this->session->userdata("dashboardURL");
			$moduleURL = $this->input->post("hddModuleURL");
			$moduleId = $this->input->post("hddModuleId");

			if($moduleURL != "x"){	
				$state = 10;		
				if($moduleId != "x"){
					$pattern = '/ID_REPLACE/i';
					$moduleURL = preg_replace($pattern, $moduleId, $moduleURL);
				}
			}elseif($idVehicle != "x"){				
				if($inspectionType == 99 || $linkInspection == "NA"){
					$state = 99;//NO HAY FORMATO DE INSPECTION
				}else{
					$state = 88;
				}
			}
		
	    	switch($state){
	    		case 0: //NEW USER, must change the password
	    				redirect("/employee","location",301);
	    				break;
	    		case 1: //ACTIVE USER
						redirect($dashboardURL,"location",301);
	    				break;
	    		case 2: //INACTIVE USER
	    				$this->session->sess_destroy();
	    				redirect("/login","location",301);
	    				break;
				case 10: //ES PARA UN MODULO, VIENE DE UN ENLACE ENVIADO POR CORREO O POR MENSAJE DE TEXTO
						redirect($moduleURL,"location",301);
						break;
	    		case 99: //NO HAY FORMATO DE INSPECCION, ENTONCES MUESTRO MENSAJE 
						$data['linkBack'] = "dashboard/";
						$data['titulo'] = "<i class='fa fa-unlock fa-fw'></i>QR CODE SCAN";				
						$data["msj"] = "<strong>Error!!!</strong> This QR code doesn't have an inspection form.";
						$data["clase"] = "alert-danger";
									
						$data["view"] = "template/answer";
						$this->load->view("layout", $data);
	    				break;
	    		case 88: //ES PARA UNA INSPECCION, LO REDIRECCIONO AL FORMUALIO DE LA BD
	    				redirect($linkInspection,"location",301);
	    				break;
	    		default: //No sé como llegaron hasta acá, pero los devuelvo al Login.
	    				$this->session->sess_destroy();
	    				redirect("/login","location",301);
	    				break;
	    	}
	    }
	    
	
		
		
		
		
		
		
		
		
	    
	}