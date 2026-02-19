<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Employee_model extends CI_Model {

	    function __construct(){        
	        parent::__construct();
	    }
	    
	    /**
	     * Update user´s password
	     * @author BMOTTAG
	     * @since  8/11/2016
		 * @review  5/4/2017
	     */
	    public function updatePassword()
		{
				$idUser = $this->input->post("hddId");
				$newPassword = $this->input->post("inputPassword");
				$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$newPassword); 
				$passwd = md5($passwd);
				
				$data = array(
					'password' => $passwd,
					'state' => 1
				);

				$this->db->where('id_user', $idUser);
				$query = $this->db->update('user', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
	    }
	    


		
		
		
		
		
		
		
		
		
		
		
	    
	}