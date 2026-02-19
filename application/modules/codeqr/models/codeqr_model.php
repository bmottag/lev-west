<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Codeqr_model extends CI_Model {

	    		
		/**
		 * Add/Edit QR CODE
		 * @since 23/7/2017
		 */
		public function saveQRCode($encryption) 
		{
				$idQRCode = $this->input->post('hddId');
				$value = $this->input->post('qr_code');
				
				$data = array(
					'value_qr_code' => $this->input->post('qr_code'),
					'image_qr_code' => 'images/qrcode/' . $value . ".png",
					'encryption' => $encryption,
					'state' => 2 //inactive
				);
				
				//revisar si es para adicionar o editar
				if ($idQRCode == '') {
					$query = $this->db->insert('param_qr_code', $data);
					$idQRCode = $this->db->insert_id();				
				} else {
					$this->db->where('id_qr_code', $idQRCode);
					$query = $this->db->update('param_qr_code', $data);
				}
				if ($query) {
					return $idQRCode;
				} else {
					return false;
				}
		}
				
		
		
		
		
		
	    
	}