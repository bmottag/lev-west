<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Trailers extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("trailers_model");
    }

    /**
     * Trailers List
     * @since 06/01/2024
     * @author FOROZCO
     */
    public function index()
    {
        $month = 2;
        $data['trailer_not_inspect'] = $this->trailers_model->get_not_inspection($month);

        $data['trailer_inspect'] = $this->trailers_model->get_trailers(); //busco lista de trailers

        $data["view"] = 'trailers_index';
        $this->load->view("layout", $data);
    }
}
