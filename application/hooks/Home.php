<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home {

    private $ci;
    private $db;

    public function __construct() {
        $this->ci = & get_instance();
        !$this->ci->load->library('session') ? $this->ci->load->library('session') : false;
        !$this->ci->load->helper('url') ? $this->ci->load->helper('url') : false;
        $this->db = $this->ci->load->database("default", true);
    }

    public function check_login() {
        $error = FALSE;
		$flag = TRUE;
        $arrModules = array("login", "ieredirect", "external");
        if (!in_array($this->ci->uri->segment(1), $arrModules)) {
            if ($this->ci->uri->segment(1) == "menu") {
                if(($this->ci->uri->segment(2) . '/' . $this->ci->uri->segment(3)) != 'menu/salir') {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
            } else if ($this->ci->uri->segment(1) == "admin") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "certifications_check", "checkin_check");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
                
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
                    $flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "programming") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "verificacion", "verificacion_flha", "verificacion_tool_box", "receive_sms", "automatic_planning_message");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
				
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "serviceorder") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "maintenance_check");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
				
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "payroll") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "payroll_check");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
                
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
                    $flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "workorders") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "foreman_view", "add_signature");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
				
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "forceaccount") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "foreman_view", "add_signature");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
				
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "jobs") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "jso_worker_view", "add_signature_jso", "saveJSOWorker", "review_excavation", "add_signature_excavation");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
				
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "safety") {//SI NO LLEVAN SESSION LOS DEJA PASAR, A LOS SIGUIENTES METODOS
                $arrControllers = array($this->ci->uri->segment(1), "review_flha", "add_signature");
                if ($this->ci->uri->segment(2) != FALSE && !in_array($this->ci->uri->segment(2), $arrControllers)) {
                    if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                        $error = TRUE;
                    }
                }
                
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
                    $flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else {
				//si no hay session entonces los redireciono al login
                if ($this->ci->session->userdata('id') == FALSE) {
                    $error = TRUE;
                }
            }
			
			//metodos que no se verifica que tengan permisos
			if ($this->ci->uri->segment(1) == "report") {
                $arrControllers = array("generaWorkOrderXLS", "generaHaulingXLS", "generaPayrollXLS", "generaWorkOrderPDF", "generaPayrollPDF", "generaInsectionSpecialPDF", "generaInsectionHeavyPDF", "generaInsectionDailyPDF", "generaHaulingPDF", "generaSafetyPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "workorders") {
                $arrControllers = array("generaWorkOrderXLS", "generaWorkOrderPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "serviceorder") {
                $arrControllers = array("generateSOReportPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "jobs") {
                $arrControllers = array("generaERPPDF", "generaTemplatePDF", "generaJSOPDF", "generaJHAPDF", "generaFIREWATCHPDF", "generaFIREWATCHLOGPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "more") {
                $arrControllers = array("generaEnvironmentalPDF", "generaPPEInspectionPDF", "generaConfinedPDF", "generaTaskControlPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "incidences") {
                $arrControllers = array("generaPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "template") {
                $arrControllers = array("generaTemplatePDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
                    $flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "payroll") {
                $arrControllers = array("generaPaystubPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
                    $flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "acs") {
                $arrControllers = array("reportPDF", "generaACSXLS");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "forceaccount") {
                $arrControllers = array("generaForceAccountPDF");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            } else if ($this->ci->uri->segment(1) == "claims") {
                $arrControllers = array("generaProgressreportXLS");
                if ($this->ci->uri->segment(2) != FALSE && in_array($this->ci->uri->segment(2), $arrControllers)) {
					$flag = FALSE;//NO SE VERIFICA SI EXISTE PERMISOS A ESTE ENLACE
                }
            }
			            
            if ($error == FALSE && $flag) {
                //Se consulta si la ruta actual tiene permiso o no en el sistema
                $this->ci->load->model('general_model', 'mm');
                $ruta_validar = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($this->ci->uri->segment($i)) {
                        $ruta_validar .= ($i == 1) ? $this->ci->uri->segment($i) : '/' . $this->ci->uri->segment($i);
                    }
                }
                
				//consulto si existe permiso en menu para esa URL
				$arrParam = array(
					'menuURL' => $ruta_validar
				);
                if($ruta_valida = $this->ci->mm->get_role_access($arrParam)) 
				{
                    //Se consulta si el usuario actual tiene permiso para esa URL
                    $arrParam = array(
                        'idRole' => $this->ci->session->userdata('rol'),
						'menuURL' => $ruta_validar
                    );

                    if(!$ruta_valida = $this->ci->mm->get_role_access($arrParam)) {
                        $error = TRUE;
                    }
				}else{					
					//consulto si existe permiso para en los enlaces para esa URL
					$arrParam = array(
						'linkURL' => $ruta_validar
					);
					if($ruta_valida = $this->ci->mm->get_role_access($arrParam)) {
						//Se consulta si el usuario actual tiene permiso para esa URL
						$arrParam = array(
							'idRole' => $this->ci->session->userdata('rol'),
							'linkURL' => $ruta_validar
						);

						if(!$ruta_valida = $this->ci->mm->get_role_access($arrParam)) 
						{
							$error = TRUE;
						}
					}
				}
            }
        }
        
        if ($error) {
            if (isset($this->ci->session) && $this->ci->session->userdata('id') == FALSE) {
                $this->ci->session->unset_userdata("auth");
                $this->ci->session->sess_destroy();
            }
            redirect(site_url("/menu/menu/salir"));
        }
    }
}
//EOC