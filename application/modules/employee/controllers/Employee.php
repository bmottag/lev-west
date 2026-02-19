<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("employee_model");
    }

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
			$idUser = $this->session->userdata("id");
			
			$this->load->model("general_model");
			$arrParam = array(
				"table" => "user",
				"order" => "id_user",
				"column" => "id_user",
				"id" => $idUser
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);

			$data["view"] = "form_password";
			$this->load->view("layout", $data);
	}
	

	/**
	 * Update user´s password
	 */
	public function updatePassword()
	{
			$data = array();			
			$data["titulo"] = "UPDATE PASSWORD";
			
			$newPassword = $this->input->post("inputPassword");
			$confirm = $this->input->post("inputConfirm");
			$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$newPassword); 
			
			$data['linkBack'] = "dashboard/";
			$data['titulo'] = "<i class='fa fa-unlock fa-fw'></i>CHANGE PASSWORD";
			
			if($newPassword == $confirm)
			{					
					if ($this->employee_model->updatePassword()) {
						$data["msj"] = "You have updated your password.";
						$data["msj"] .= "<br><strong>User name: </strong>" . $this->input->post("hddUser");
						$data["msj"] .= "<br><strong>Password: </strong>" . $passwd;
						$data["clase"] = "alert-success";
					}else{
						$data["msj"] = "<strong>Error!!!</strong> Ask for help.";
						$data["clase"] = "alert-danger";
					}
			}else{
				//definir mensaje de error
				echo "pailas no son iguales";
			}
						
			$data["view"] = "template/answer";
			$this->load->view("layout", $data);
	}
	
	/**
	 * photo
	 */
	public function profile($error = '')
	{
			$idUser = $this->session->userdata("id");
			
			//busco datos del empleado
			$arrParam = array(
				"table" => "user",
				"order" => "id_user",
				"column" => "id_user",
				"id" => $idUser
			);
			$this->load->model("general_model");
			$data['UserInfo'] = $this->general_model->get_basic_search($arrParam);
						
			$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 
			$data["view"] = 'form_photo';
			$this->load->view("layout", $data);
	}
	
    //FUNCIÓN PARA SUBIR LA IMAGEN 
    function do_upload() 
	{
			$config['upload_path'] = './images/employee/';
			$config['overwrite'] = true;
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '3000';
			$config['max_width'] = '2024';
			$config['max_height'] = '2008';
			$idUser = $this->session->userdata("id");
			$config['file_name'] = $idUser;

			$this->load->library('upload', $config);
			//SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
			if (!$this->upload->do_upload()) {
				$error = $this->upload->display_errors();
				$this->profile($error);
			} else {
				$file_info = $this->upload->data();//subimos la imagen
				
				//USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
				//ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
				$this->_create_thumbnail($file_info['file_name']);
				$data = array('upload_data' => $this->upload->data());
				$imagen = $file_info['file_name'];
				$path = "images/employee/thumbs/" . $imagen;
				
				//actualizamos el campo photo
				$arrParam = array(
					"table" => "user",
					"primaryKey" => "id_user",
					"id" => $idUser,
					"column" => "photo",
					"value" => $path
				);

				$this->load->model("general_model");
				$data['linkBack'] = "employee/profile";
				$data['titulo'] = "<i class='fa fa-user fa-fw'></i>USER PROFILE";
				
				if($this->general_model->updateRecord($arrParam))
				{
					$data['clase'] = "alert-success";
					$data['msj'] = "Good job, you have updated your photo.";			
				}else{
					$data['clase'] = "alert-danger";
					$data['msj'] = "Ask for help.";
				}
							
				$data["view"] = 'template/answer';
				$this->load->view("layout", $data);
				//redirect('employee/photo');
			}
    }
	
    //FUNCIÓN PARA CREAR LA MINIATURA A LA MEDIDA QUE LE DIGAMOS
    function _create_thumbnail($filename) 
	{
        $config['image_library'] = 'gd2';
        //CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
        $config['source_image'] = 'images/employee/' . $filename;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        //CARPETA EN LA QUE GUARDAMOS LA MINIATURA
        $config['new_image'] = 'images/employee/thumbs/';
        $config['width'] = 150;
        $config['height'] = 150;
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
    }

	/**
	 * Signature
     * @since 27/1/2023
     * @author BMOTTAG
	 */
	public function add_signature($idUser)
	{
			if (empty($idUser)) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			if($_POST){
				
				//update signature with the name of de file
				$name = "images/employee/signature/" . $idUser . ".png";
				
				$arrParam = array(
					"table" => "user",
					"primaryKey" => "id_user",
					"id" => $idUser,
					"column" => "user_signature",
					"value" => $name
				);

				//enlace para regresar al formulario
				$data['linkBack'] = "employee/profile";
				
				$data_uri = $this->input->post("image");
				$encoded_image = explode(",", $data_uri)[1];
				$decoded_image = base64_decode($encoded_image);
				file_put_contents($name, $decoded_image);
				
				$this->load->model("general_model");
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
			}else{			
				$this->load->view('template/make_signature');
			}
	}

	
}
