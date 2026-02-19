<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enlaces extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("enlaces_model");
		$this->load->helper('form');
    }
	
	/**
	 * Listado Menu
     * @since 30/3/2020
     * @author BMOTTAG
	 */
	public function menu()
	{
			$this->load->model("general_model");
			$arrParam = array();
			$data['info'] = $this->general_model->get_menu($arrParam);

			$data["view"] = 'menu';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario menu
     * @since 30/3/2020
     */
    public function cargarModalMenu() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idMenu"] = $this->input->post("idMenu");	
			
			if ($data["idMenu"] != 'x') {
				$this->load->model("general_model");
				$arrParam = array("idMenu" => $data["idMenu"]);
				$data['information'] = $this->general_model->get_menu($arrParam);
			}
			
			$this->load->view("menu_modal", $data);
    }
	
	/**
	 * Update Menu
     * @since 30/3/2020
     * @author BMOTTAG
	 */
	public function save_menu()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idEnlace = $this->input->post('hddId');
			
			$msj = "You have added a new Menu link!!";
			if ($idEnlace != '') {
				$msj = "You have updated a Menu link!!";
			}

			if ($this->enlaces_model->saveMenu()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	/**
	 * Links list
     * @since 31/3/2020
     * @author BMOTTAG
	 */
	public function links()
	{
			$this->load->model("general_model");
			$arrParam = array();
			$data['info'] = $this->general_model->get_links($arrParam);
			
			$data["view"] = 'links';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario link
     * @since 31/3/2020
     */
    public function cargarModalLink() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idLink"] = $this->input->post("idLink");	
			
			$this->load->model("general_model");
			$arrParam = array("columnOrder" => "menu_name");
			$data['menuList'] = $this->general_model->get_menu($arrParam);
			
			if ($data["idLink"] != 'x') {
				$arrParam = array("idLink" => $data["idLink"]);
				$data['information'] = $this->general_model->get_links($arrParam);
			}
			
			$this->load->view("links_modal", $data);
    }
	
	/**
	 * Update link
     * @since 31/3/2020
     * @author BMOTTAG
	 */
	public function save_link()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idLink = $this->input->post('hddId');
			$newIdMenu = $this->input->post('id_menu');
			$oldIdMenu = $this->input->post('hddIdMenu');
			
			$msj = "You have added a new Link!!";
			if ($idLink != '') {
				$msj = "You have updated a Link!!";
			}

			if ($this->enlaces_model->saveLink()) {
				if ($idLink != '' && $newIdMenu != $oldIdMenu) {
					//Update fk_id_menu in param_menu_permisos
					$this->load->model("general_model");
					$arrParam = array(
						"table" => "param_menu_permisos ",
						"primaryKey" => "fk_id_link",
						"id" => $idLink,
						"column" => "fk_id_menu",
						"value" => $newIdMenu
					);
					$this->general_model->updateRecord($arrParam);
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
	 * Access list
     * @since 31/3/2020
     * @author BMOTTAG
	 */
	public function role_access()
	{
			$this->load->model("general_model");
			$arrParam = array();
			$data['info'] = $this->general_model->get_role_access($arrParam);

			$data["view"] = 'role_access';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario Acesss
     * @since 31/3/2020
     */
    public function cargarModalRoleAccess() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data['linkList'] = FALSE;
			$data["idPermiso"] = $this->input->post("idPermiso");	
			
			$this->load->model("general_model");
			$arrParam = array(
				"columnOrder" => "menu_name",
				"menuState" => 1
			);
			$data['menuList'] = $this->general_model->get_menu($arrParam);
			
			$arrParam = array();
			$data['roles'] = $this->general_model->get_roles($arrParam);
			
			if ($data["idPermiso"] != 'x') {
				$arrParam = array("idPermiso" => $data["idPermiso"]);
				$data['information'] = $this->general_model->get_role_access($arrParam);
				
				//busca lista de links para el menu guardado
				$arrParam = array("idMenu" => $data['information'][0]['fk_id_menu']);
				$data['linkList'] = $this->general_model->get_links($arrParam);
			}
			
			$this->load->view("role_access_modal", $data);
    }
	
	/**
	 * Update access
     * @since 31/3/2020
     * @author BMOTTAG
	 */
	public function save_role_access()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idPermiso = $this->input->post('hddId');
			
			$msj = "You have added a new access!";
			if ($idPermiso != '') {
				$msj = "You have updated an access!";
			}
			
			//para verificar si ya existe este permiso
			$result_access = FALSE;

			$this->load->model("general_model");
			$arrParam = array(
				"idMenu" => $this->input->post('id_menu'),
				"idLink" => $this->input->post('id_link'),
				"idRole" => $this->input->post('id_rol')
			);
			$result_access = $this->general_model->get_role_access($arrParam);
			
			if ($result_access) {
				$data["result"] = "error";
				$data["mensaje"] = " Error. The access already exist.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> The access already exist.');
			} else {
				if ($this->enlaces_model->saveRoleAccess()) {
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', $msj);
				} else {
					$data["result"] = "error";
					$data["mensaje"] = " Error. Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }
	
	/**
	 * Listado de enlaces
     * @since 2/4/2018
     * @author BMOTTAG
	 */
	public function videos()
	{
			$this->load->model("general_model");
			
			$arrParam = array("linkType" => 4);
			$data['info'] = $this->general_model->get_links($arrParam);
			
			$data["view"] = 'videos_links';
			$this->load->view("layout", $data);
	}
	
    /**
     * Cargo modal - formulario enlace
     * @since 2/4/2018
     */
    public function cargarModalVideoLinks() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$this->load->model("general_model");
			$idLink = $this->input->post("idLink");	
			
			if ($idLink != 'x') {
				$arrParam = array("idLink" => $idLink);
				$data['information'] = $this->general_model->get_links($arrParam);
			}
			
			$this->load->view("videos_modal", $data);
    }
	
	/**
	 * Update Enlace
     * @since 2/4/2018
     * @author BMOTTAG
	 */
	public function save_video()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idLink = $this->input->post('hddId');
			
			$msj = "You have added a new Link!!";
			if ($idLink != '') {
				$msj = "You have updated a Link!!";
			}

			if ($this->enlaces_model->saveVideo()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
	/**
	 * Listado de enlaces para los manuales
     * @since 27/4/2018
     * @author BMOTTAG
	 */
	public function manuals()
	{
			$this->load->model("general_model");
			
			$arrParam = array("linkType" => 5);
			$data['info'] = $this->general_model->get_links($arrParam);
			
			$data["view"] = 'manuals_links';
			$this->load->view("layout", $data);
	}
	
	/**
	 * Form Upload Locates 
     * @since 27/4/2018
     * @author BMOTTAG
	 */
	public function manuals_form($idLink = 'x', $error = '')
	{			
			$data['information'] = FALSE;
			$this->load->model("general_model");

			if ($idLink != 'x' && $idLink != '') {
				$arrParam = array("idLink" => $idLink);
				$data['information'] = $this->general_model->get_links($arrParam);
			}
			
			$data['error'] = $error; //se usa para mostrar los errores al cargar la imagen 			

			$data["view"] = "manuals_form";
			$this->load->view("layout", $data);
	}
	
	/**
	 * FUNCIÓN PARA SUBIR el archivo
	 */
    function do_upload_manual() 
	{
        $config['upload_path'] = './files/';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = '3000';
        $config['max_width'] = '2024';
        $config['max_height'] = '2008';
        $idLink = $this->input->post("hddId");

        $this->load->library('upload', $config);
        //SI EL ARCHIVO FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA 
        if (!$this->upload->do_upload()) {
            $error = $this->upload->display_errors();
            $this->manuals_form($idLink,$error);
        } else {
            $file_info = $this->upload->data();//subimos ARCHIVO
			
			$data = array('upload_data' => $this->upload->data());
			$archivo = $file_info['file_name'];
			$path = base_url() . "files/" . $archivo;
			
			//insertar datos
			if($this->enlaces_model->saveManual($path))
			{
				$this->session->set_flashdata('retornoExito', 'You have uploaded the information.');
			}else{
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect('enlaces/manuals');
        }
    }
	
    /**
     * Delete Job locate
     */
    public function deleteManual($idJobLocate, $idJob) 
	{
			if (empty($idJobLocate) || empty($idJob) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			$arrParam = array(
				"table" => "job_locates",
				"primaryKey" => "id_job_locates",
				"id" => $idJobLocate
			);
			
			$this->load->model("general_model");
			if ($this->general_model->deleteRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'You have deleted the image.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			redirect(base_url('jobs/locates/' . $idJob), 'refresh');
    }

	/**
	 * Link list by menu
     * @since 1/4/2020
     * @author BMOTTAG
	 */
    public function linkListInfo() {
        header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
        $idMenu = $this->input->post('idMenu');
				
		//busco info tabla de stock
		$this->load->model("general_model");
		$arrParam = array(
			"idMenu" => $idMenu,
			"linkState" => 1
		);
		$linkList = $this->general_model->get_links($arrParam);

        echo "<option value=''>Select...</option>";
        if ($linkList) {
            foreach ($linkList as $fila) {
                echo "<option value='" . $fila["id_link"] . "' >" . $fila["link_name"] . "</option>";
            }
        }
    }	
	
	/**
	 * Delete link acces
     * @since 1/4/2020
	 */
	public function delete_role_access()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idPermiso = $this->input->post('identificador');
			
			$arrParam = array(
				"table" => "param_menu_permisos",
				"primaryKey" => "id_permiso",
				"id" => $idPermiso
			);
			
			$this->load->model("general_model");
			if ($this->general_model->deleteRecord($arrParam)) 
			{
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have deleted the role access.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}
			
			echo json_encode($data);
    }

	/**
	 * Delete Link
	 * @since 27/02/2025
	 */
	public function delete_link()
	{
		header('Content-Type: application/json');
		$data = array();

		$idLink = $this->input->post('identificador');

		$arrParam = array(
			"table" => "param_menu_permisos",
			"primaryKey" => "fk_id_link",
			"id" => $idLink
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) 
		{
			$arrParam = array(
				"table" => "param_menu_links",
				"primaryKey" => "id_link",
				"id" => $idLink
			);
			$this->general_model->deleteRecord($arrParam);

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
	 * Delete Link
	 * @since 27/02/2025
	 */
	public function delete_menu()
	{
		header('Content-Type: application/json');
		$data = array();

		$idMenu = $this->input->post('identificador');

		$arrParam = array(
			"table" => "param_menu_permisos",
			"primaryKey" => "fk_id_menu",
			"id" => $idMenu
		);
		$this->load->model("general_model");
		if ($this->general_model->deleteRecord($arrParam)) 
		{
			$arrParam = array(
				"table" => "param_menu_links",
				"primaryKey" => "fk_id_menu",
				"id" => $idMenu
			);
			$this->general_model->deleteRecord($arrParam);

			$arrParam = array(
				"table" => "param_menu",
				"primaryKey" => "id_menu",
				"id" => $idMenu
			);
			$this->general_model->deleteRecord($arrParam);

			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', 'You have delete the record.');
		} else {
			$data["result"] = "error";
			$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
			$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
		}

		echo json_encode($data);
	}
	


	
}