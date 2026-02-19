<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MENU
 * @since 28/07/2015
 */
class Menu extends MX_Controller {

    public function __construct() {
        parent::__construct();

    }

    /**
     * Sale del módulo de administración y regresa al login del aplicativo
     * @since 03/08/2015
     */
    public function salir() {
        $this->session->unset_userdata("auth");
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }


    
    
    
}
