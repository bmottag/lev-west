<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(FCPATH . 'vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Claims extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("claims_model");
        $this->load->helper('form');
    }
	
	/**
	 * Form Workorders
     * @since 3/2/2021
     * @author BMOTTAG
	 */
	public function index($idJob)
	{		
			$data['tituloListado'] = 'LIST OF CLAIMS';
		
			$this->load->model("general_model");
			$arrParam['idJob'] = $idJob;
			$data['jobInfo'] = $this->general_model->get_job($arrParam);
			$data['claimsInfo'] = $this->claims_model->get_claims($arrParam);

			$data["view"] ='claims';
			$this->load->view("layout", $data);
	}

    /**
     * Cargo modal - formulario CLAIMS
     * @since 3/2/2021
     */
    public function cargarModalClaim() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$this->load->model("general_model");
			$arrParam['idJob'] = $idJob = $this->input->post("idJob");
			$data['jobs'] = $this->general_model->get_job($arrParam);

			$arrParam = array('idJob' => $idJob, 'limit' => 1);
			$claim = $this->claims_model->get_claims($arrParam);
		
			$data['nextClaimNumber'] = $claim ? ($claim[0]['claim_number'] + 1) : 1;
			$data['lastObservation'] = $claim ? ($claim[0]['observation_claim']) : false;
								
			$this->load->view("claims_modal", $data);
    }

	/**
	 * Guardar claim
	 * @since 3/2/2021
     * @author BMOTTAG
	 */
	public function guardar_claims()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idClaimInicial = $this->input->post('hddId');
			$msj = $idClaimInicial ? "You have updated the Claim, continue uploading the information." : "You have added a Claim, continue uploading the information.";

			$resultSearch = false;
			if ($idClaimInicial == '') {
				$arrParam = array(
					"idJob" => $this->input->post('id_job'),
					"claimNumberSearch" => $this->input->post('claimNumber')
				);
				$resultSearch = $this->claims_model->get_claims($arrParam);
			}
	
			if ($resultSearch) {
				$data["result"] = "error";
				$data["mensaje"] = " Error. Duplicate entry: This claim number already exists for the selected job.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Duplicate entry: This claim number already exists for the selected job.');
			} else {
				if ($idClaim = $this->claims_model->guardarClaim()) 
				{
					//guardo el primer estado del claim
					if(!$idClaimInicial){
						$arrParam = array(
							"idClaim" => $idClaim,
							"message" => "New Claim.",
							"state" => 1
						);					
						$this->claims_model->add_claim_state($arrParam);
					}

					$data["idRecord"] = $idClaim;
					$data["result"] = true;		
					$this->session->set_flashdata('retornoExito', '<strong>Right!</strong> ' . $msj);
				} else {
					$data["result"] = "error";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Form Upload APU to claim
     * @since 12/05/2025
     * @author BMOTTAG
	 */
	public function upload_apu($idClaim = 'x')
	{
		//Claim info
		$arrParam = array('idClaim' => $idClaim);
		$data['claimsInfo'] = $this->claims_model->get_claims($arrParam);

		//Claim State history
		$data['claimsHistory'] = $this->claims_model->get_claims_history($arrParam);
										
		$this->load->model("general_model");
		//$data['WOList'] = $this->general_model->get_workorder_info($arrParam);	
		$arrParam = array("idJob" => $data['claimsInfo'][0]['fk_id_job']);
		$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);
		
		$data["view"] = 'form_upload_info_claim';
		$this->load->view("layout_calendar", $data);
	}

	/**
	 * Update Claim info
     * @since 12/05/2025
     * @author BMOTTAG
	 */
	public function update_claim()
	{
		header('Content-Type: application/json');

		$idClaim = $this->input->post('hddIdClaim');
		$records = $this->input->post('records');
		$successCount = 0;
		$errorCount = 0;

		foreach ($records as $record) {
			$dataToSave = [
				'fk_id_claim'   => $idClaim,
				'fk_id_job_detail' => $record['id_job_detail'],
				'quantity'   => $record['quantity'],
				'cost'      => $record['quantity'] ? $record['unit_price'] * $record['quantity'] : $record['cost']
			];

			if ($this->claims_model->updateInfoAPU($dataToSave)) {
				$successCount++;
			} else {
				$errorCount++;
			}
		}
		echo json_encode([
			"status" => $errorCount === 0 ? "success" : "error",
			"message" => $errorCount === 0 ? "$successCount records saved successfully!" : "$errorCount records failed to save."
		]);
	}

	/**
	 * Form Add APU to Claim
	 * Muestre lista de APU y los que estan asignados al CLAIM
     * @since 24/05/2025
     * @author BMOTTAG
	 */
	public function add_apu($idJob, $idClaim)
	{
			if (empty($idJob) || empty($idClaim)) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
			
			 //list de WO para un JOB que no estan asignadas
			$this->load->model("general_model");
			$arrParam = array("idJob" => $idJob);
			$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);
			$data['jobInfo'] = $this->general_model->get_job($arrParam);

			$data["idJob"] = $idJob;
			$data["idClaim"] = $idClaim;
			$data["view"] = 'form_add_apu';
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Asignar APU al claim
     * @since 25/05/2025
     * @author BMOTTAG
	 */
	public function save_claim_apu()
	{	
			header('Content-Type: application/json');
			$data = array();
			$data['idRecord'] = $this->input->post('hddId');

			$apu = $this->input->post('apu');
			if($apu){
				if ($this->claims_model->saveClaimAPU()) {
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', "LIC assigned to the claim!!");
				} else {
					$data["result"] = "error";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
				}
			}else{
					$data["result"] = "error";
					$data["mensaje"] = " You have to select LIC";
					$this->session->set_flashdata('retornoError', 'You have to select LIC');
			}
			echo json_encode($data);
	}

	/**
	 * Delete WO de Claims
     * @since 4/2/2021
	 */
	public function delete_wo_from_claim()
	{			
			header('Content-Type: application/json');
			$data = array();
			$this->load->model("general_model");

			$idCompuesto = $this->input->post("identificador");
			$porciones = explode('-', $idCompuesto);

			$idWO = $porciones[0];
			$data['idRecord'] = $porciones[1];
			
			$arrParam = array(
				"table" => "workorder",
				"primaryKey" => "id_workorder",
				"id" => $idWO,
				"column" => "fk_id_claim",
				"value" => 0
			);
			if ($this->general_model->updateRecord($arrParam)) 
			{				
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', 'You have delete the W.O. from the claim.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}				

			echo json_encode($data);
    }

	/**
     * Cargo modal - formulario Estado Claim
     * @since 5/02/2021
     * @author BMOTTAG
     */
    public function cargarModalClaimState() 
	{
			header("Content-Type: text/plain; charset=utf-8");
			$data['idClaim'] = $this->input->post("idClaim");

			$this->load->view("claim_state_modal", $data);
    }

	/**
	 * Guardar orden de trabajo estado
	 * @since 29/1/2021
     * @author BMOTTAG
	 */
	public function save_claim_state()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idClaim = $data['idRecord'] = $this->input->post("hddIdClaim");
				
			$msj = "You have updated the information!";

			$claimState = $this->input->post("state");

			$arrParam = array(
				"idClaim" => $idClaim,
				"message" => $this->input->post("message"),
				"state" => $claimState
			);		
			if ($this->claims_model->add_claim_state($arrParam)) 
			{
				//actualizar estado actual en CLAIM
				$this->claims_model->update_claim($arrParam);

				$WOList = FALSE;
				//busco listado de WO del claim, si es 2. Send to Client o 6. Final Payment
				if($claimState == 2 || $claimState == 6)
				{
					if($claimState == 2){
						$stateMSJ = 'Send to Client';
					}else{
						$stateMSJ = 'Closed';
					}
					$msj .= ' And Word Order state changed to <strong>' . $stateMSJ . '</strong>.';
					$this->load->model("general_model");
					$arrParam = array('idClaim' => $idClaim);
					$WOList = $this->general_model->get_workorder_info($arrParam);	
					//si el estado es igual a 2. Send to Client o 6. Final Payment, entonces actualizo todos los estados de las WO automaticamente
					if($WOList){
						$this->claims_model->updateWOStateFromClaimChange($WOList);
					}
				} 

				$data["result"] = true;		
				$this->session->set_flashdata('retornoExito', '<strong>	Right!</strong> ' . $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
		
			echo json_encode($data);
    }

	/**
	 * Next claim number
	 * @since 13/05/2025
	 * @author BMOTTAG
	 */
	public function nextClaimNumber()
	{
		$idJob = $this->input->post('job');
		$this->load->model("general_model");
	
		$arrParam = array('idJob' => $idJob, 'limit' => 1);
		$claim = $this->claims_model->get_claims($arrParam);
	
		$nextClaimNumber = $claim ? ($claim[0]['claim_number'] + 1) : 1;
		$lastObservation = $claim ? ($claim[0]['observation_claim']) : false;
	
		echo json_encode(['next' => $nextClaimNumber, 'lastObservation' => $lastObservation]);
	}

	/**
	 * Claim history
     * @since 20/05/2025
     * @author BMOTTAG
	 */
	public function claim_history($idClaim = 'x')
	{
			//Claim info
			$arrParam = array('idClaim' => $idClaim);
			$data['claimsInfo'] = $this->claims_model->get_claims($arrParam);

			//Claim State history
			$data['claimsHistory'] = $this->claims_model->get_claims_history($arrParam);
											
			$this->load->model("general_model");
			//$data['WOList'] = $this->general_model->get_workorder_info($arrParam);	
			$arrParam = array("idJob" => $data['claimsInfo'][0]['fk_id_job']);
			$data['chapterList'] = $this->general_model->get_chapter_list($arrParam);

			$arrParam = array('idJob' => $data['claimsInfo'][0]['fk_id_job'], 'order' => 'asc');
			$data['allClaims'] = $this->claims_model->get_claims($arrParam);

			$data["view"] = 'claim_history';
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Generate Project Progress Report for claim in XLS
	 * @param int $idJob
	 * @since 22/5/2025
	 * @author BMOTTAG
	 */
	public function generaProgressreportXLS($idJob)
	{
		$arrParam = array("idJob" => $idJob);
		$this->load->model("general_model");
		$jobInfo = $this->general_model->get_job($arrParam);

		$chapterList = $this->general_model->get_chapter_list($arrParam);

		$arrParam = array('idJob' => $idJob, 'order' => 'asc');
		$allClaims = $this->claims_model->get_claims($arrParam);


		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=' . $jobInfo[0]['job_code'] . ' Project Progress Report.xlsx');

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Project Progress Report');

		$sheet = $spreadsheet->getActiveSheet();


		/**
		 * LOGO
		 */
		$logoPath = FCPATH . 'images/logo.png';
		if (file_exists($logoPath)) {
			$drawing = new Drawing();
			$drawing->setName('Logo');
			$drawing->setDescription('Company Logo');
			$drawing->setPath($logoPath);
			$drawing->setHeight(120);
			$drawing->setCoordinates('B1');
			$drawing->setOffsetX(10);
			$drawing->setOffsetY(10);
			$drawing->setWorksheet($spreadsheet->getActiveSheet());
		}

		/**
		 * Project Information
		 */
		$sheet->mergeCells('D3:E3');
		$sheet->mergeCells('D4:E4');
		$sheet->mergeCells('D5:E5');
		$sheet->mergeCells('D6:E6');

		$sheet->mergeCells('F3:G3');
		$sheet->mergeCells('F4:G4');
		$sheet->mergeCells('F5:G5');
		$sheet->mergeCells('F6:G6');

		$sheet->getStyle('D3:E6')->getFont()->setBold(true);

		$spreadsheet->getActiveSheet()->getStyle('D3:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('D4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('D5:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('D6:G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('D3:G6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('D3:G6')->getFont()->setSize(14);

		$sheet->setCellValue('D3', 'Project Name:')
					->setCellValue('D4', 'Project No:')
					->setCellValue('D5', 'Client:')
					->setCellValue('D6', 'Date:')
					->setCellValue('F3', $jobInfo[0]['job_name'])
					->setCellValue('F4', $jobInfo[0]['job_code'])
					->setCellValue('F5', $jobInfo[0]['company_name'])
					->setCellValue('F6', date('m/d/Y'));

		/**
		 * Project Description
		 */
		$sheet->getStyle('D8:G9')->getFont()->setBold(true);
		$sheet->mergeCells('D8:G9');
		$sheet->getStyle('D8:G9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('D8:G9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('D8:G9')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('D8:G9')->getFont()->setSize(14);
		$spreadsheet->getActiveSheet()->getStyle('D8:G9')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('D8:G9')->getFill()->getStartColor()->setARGB('FFCCE5FF');

		$sheet->setCellValue('D8', $jobInfo[0]['job_description']);

		/**
		 * Job Detail Information
		 */
		$spreadsheet->getActiveSheet(0)->setCellValue('B11', 'Item')
										->setCellValue('C11', 'Description')
										->setCellValue('D11', 'Unit')
										->setCellValue('E11', 'Qty')
										->setCellValue('F11', 'Unit Price')
										->setCellValue('G11', 'Extended Amount');

		$colIndex = 8;
		if (isset($allClaims) && $allClaims) {
			
			//TITLE COST & QUANTITY
			foreach ($allClaims as $claim) {
				$colQty = Coordinate::stringFromColumnIndex($colIndex);
				$colCost = Coordinate::stringFromColumnIndex($colIndex + 1);

				$spreadsheet->getActiveSheet()->setCellValue($colQty . '11', 'Qty Claim ' . $claim['claim_number']);
				$spreadsheet->getActiveSheet()->setCellValue($colCost . '11', 'Cost Claim ' . $claim['claim_number']);

				$spreadsheet->getActiveSheet()->getColumnDimension($colQty)->setWidth(18);
				$spreadsheet->getActiveSheet()->getColumnDimension($colCost)->setWidth(18);

				$colIndex += 2;
			}

			$colGreen = Coordinate::stringFromColumnIndex($colIndex-1);
			$colQtyComplete = Coordinate::stringFromColumnIndex($colIndex);
			$colCostComplete = Coordinate::stringFromColumnIndex($colIndex+1);
			$colPerComplete = Coordinate::stringFromColumnIndex($colIndex+2);
			$spreadsheet->getActiveSheet(0)->setCellValue($colQtyComplete . '11', 'Qty to complete')
											->setCellValue($colCostComplete . '11', 'Cost to complete')
											->setCellValue($colPerComplete . '11', '% completed');

			$spreadsheet->getActiveSheet()->getColumnDimension($colQtyComplete)->setWidth(20);
			$spreadsheet->getActiveSheet()->getColumnDimension($colCostComplete)->setWidth(20);
			$spreadsheet->getActiveSheet()->getColumnDimension($colPerComplete)->setWidth(20);
			
			$colClaimIni = Coordinate::stringFromColumnIndex(8);
			$spreadsheet->getActiveSheet()->getStyle($colClaimIni.'11:'.$colPerComplete.'11')->getFont()->setBold(true);
			$spreadsheet->getActiveSheet()->getStyle($colQtyComplete . '11:' . $colPerComplete . '11')->getFill()->getStartColor()->setARGB('FFFFCCCC');//RED COLOR

			$colIndex += 3;
		}
		$finalCol = Coordinate::stringFromColumnIndex($colIndex-1);
		$finalCol = Coordinate::stringFromColumnIndex($colIndex-1);
		$sheet->getStyle('B11:' . $finalCol . '11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
	
		/**
		 * DATA
		 */
		$j = 12;
		foreach ($chapterList as $chapter) :
			$j++;
			$ini = $j;
			$spreadsheet->getActiveSheet()->setCellValue('B' . $j, $chapter['chapter_number'] . '. ' . $chapter['chapter_name']);//CHAPTER NAME
		
			$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFill()->setFillType(Fill::FILL_SOLID);
			$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFill()->getStartColor()->setARGB('FFD9D9D9');
			$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFont()->setBold(true);

			$arrParam = array("idJob" => $idJob, "chapterNumber" => $chapter['chapter_number']);
			$jobDetails = $this->general_model->get_job_detail($arrParam);

			if($jobDetails){

				$sumExtendedAmount = 0;
				foreach ($jobDetails as $detail):
					$j++;
					$sumExtendedAmount += $detail['extended_amount'];
					$spreadsheet->getActiveSheet()->setCellValue('B' . $j, $detail['chapter_number'] . "." . $detail['item'])
													->setCellValue('C' . $j, $detail['description'])
													->setCellValue('D' . $j, $detail['unit'])
													->setCellValue('E' . $j, $detail['quantity'])
													->setCellValue('F' . $j, $detail['unit_price'])
													->setCellValue('G' . $j, $detail['extended_amount']);
	
					$spreadsheet->getActiveSheet()->getStyle('F' . $j)->getNumberFormat()->setFormatCode('"$"#,##0.00');
					$spreadsheet->getActiveSheet()->getStyle('G' . $j)->getNumberFormat()->setFormatCode('"$"#,##0.00');

					if (isset($allClaims) && $allClaims) {
						$colIndexInfo = 8;
						$subTotalQty = 0;
						$subTotalCost = 0;
						foreach ($allClaims as $claim) {
							$arrParamCheck = array("idClaim" => $claim['id_claim'], "idJobDetail" => $detail['id_job_detail']);
							$claimInfo = $this->general_model->get_job_detail_claims_info($arrParamCheck);

							$colQty = Coordinate::stringFromColumnIndex($colIndexInfo);
							$colCost = Coordinate::stringFromColumnIndex($colIndexInfo + 1);

							$subTotalQty += isset($claimInfo[0]['quantity_claim']) ? $claimInfo[0]['quantity_claim'] : 0;
							$subTotalCost += isset($claimInfo[0]['cost']) ? $claimInfo[0]['cost'] : 0;
							$qty  = isset($claimInfo[0]['quantity_claim']) ? $claimInfo[0]['quantity_claim'] : '';
							$cost = isset($claimInfo[0]['cost']) ? $claimInfo[0]['cost'] : '';

							$spreadsheet->getActiveSheet()->setCellValue($colQty . $j, $qty);
							$spreadsheet->getActiveSheet()->setCellValue($colCost . $j, $cost);
							if ($cost !== '') {
								$spreadsheet->getActiveSheet()
									->getStyle($colCost . $j)
									->getNumberFormat()
									->setFormatCode('"$"#,##0.00');
							}

							$colIndexInfo += 2;
						}

						//DATA COMPLETE
						$totalQty = $detail['quantity']-$subTotalQty;
						$totalCost = $detail['extended_amount']-$subTotalCost;
						$percentage = $qty? (100 * ($totalQty/$qty)):0;
						$colQtyCompleteInfo = Coordinate::stringFromColumnIndex($colIndexInfo);
						$colCostCompleteInfo = Coordinate::stringFromColumnIndex($colIndexInfo+1);
						$colPerCompleteInfo = Coordinate::stringFromColumnIndex($colIndexInfo+2);
						$spreadsheet->getActiveSheet(0)->setCellValue($colQtyCompleteInfo . $j, $totalQty)
														->setCellValue($colCostCompleteInfo . $j, $totalCost)
														->setCellValue($colPerCompleteInfo . $j, $percentage.'%');

						$spreadsheet->getActiveSheet()->getStyle($colCostCompleteInfo . $j)->getNumberFormat()->setFormatCode('"$"#,##0.00');

					}
				endforeach;
				$j++;
				$spreadsheet->getActiveSheet()->setCellValue('B' . $j, 'SUB-TOTAL PART ' . $chapter['chapter_number']);
				$spreadsheet->getActiveSheet()->setCellValue('G' . $j, $sumExtendedAmount);

				$spreadsheet->getActiveSheet()->getStyle('G' . $j)->getNumberFormat()->setFormatCode('"$"#,##0.00');

				$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFont()->setSize(14);
				$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFill()->setFillType(Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFill()->getStartColor()->setARGB('FFCCE5FF');
				$spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$finalCol.$j)->getFont()->setBold(true);
				$sheet->mergeCells('B'.$j.':C'.$j);

				$sheet->getStyle('B' . $ini . ':' . $finalCol .$j)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			}

			$j++;
		endforeach;

		// Set column widths							  
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(140);
		$spreadsheet->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);

		// Add conditional formatting
		$spreadsheet->getActiveSheet()->getStyle('B11:' . $finalCol . '11')->getFont()->setSize(11);

		$spreadsheet->getActiveSheet()->getStyle('B11:' . $finalCol . '11')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('B11:' . $colGreen . '11')->getFill()->getStartColor()->setARGB('FFCCFFCC');//GREEN COLOR


		$spreadsheet->getActiveSheet()->getStyle('B11:' . $finalCol . '11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('B11:' . $finalCol . '11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	
	
}