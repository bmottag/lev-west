<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Dayoff_model extends CI_Model {
		
		/**
		 * Add SAFETY HAZARD
		 * @since 7/12/2016
		 */
		public function add_dayoff() 
		{
				$idUser = $this->session->userdata("id");
				$type =  $this->input->post('type');
				$observation =  $this->security->xss_clean($this->input->post('observation'));
				$observation =  addslashes($observation);

				$date =  $this->input->post('date');
				$state =  1;//new
				$fecha = date("Y-m-d G:i:s");

				$sql = "INSERT INTO dayoff";
				$sql.= " (fk_id_user, id_type_dayoff, date_issue, date_dayoff, observation, state)";
				$sql.= " VALUES ($idUser, $type, '$fecha', '$date', '$observation', $state)";
		
				$query = $this->db->query($sql);
				
				$idDayoff = $this->db->insert_id();
				
				if ($query) {
					return $idDayoff;
				} else {
					return false;
				}
		}
		
	    /**
	     * Update dayoff´s state
	     * @author BMOTTAG
	     * @since  8/12/2016
	     */
	    public function update_dayoff()
		{
				$idDayoff = $this->input->post('hddIdParam');
				$idUser = $this->session->userdata("id");
				$state = $this->input->post("state");
				$observation = $this->input->post('observation');
				$fecha = date("Y-m-d G:i:s");
								
				//actualizo fecha del registo
				$sql = "UPDATE dayoff";
				$sql.= " SET fk_id_boss = $idUser, state = $state, admin_observation = '$observation', date_update = '$fecha'";
				$sql.= " WHERE id_dayoff = $idDayoff";
				$query = $this->db->query($sql);

				if ($query) {
					return true;
				} else {
					return false;
				}
	    }

		

		
		
		
		
		
	    
	}