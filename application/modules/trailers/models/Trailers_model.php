<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Trailers_model extends CI_Model
{
    public function get_trailers()
    {
        $this->db->select('id_inspection_daily, date_issue, fk_id_trailer, trailer_lights, trailer_tires, trailer_slings, trailer_clean, trailer_chains, trailer_ratchet, trailer_comments, param_vehicle.description');

        $this->db->join('param_vehicle', 'inspection_daily.fk_id_trailer = param_vehicle.id_vehicle', 'INNER');
        $this->db->where('(fk_id_trailer, date_issue) IN (SELECT fk_id_trailer, MAX(date_issue) FROM inspection_daily GROUP BY fk_id_trailer)');
        $this->db->where('type_level_2 =', 5);
        $this->db->order_by('id_inspection_daily', 'desc');

        $query = $this->db->get('inspection_daily');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_not_inspection($month)
    {
        $sub = '-' . $month . ' months';
        $date = date("Y-m-d", strtotime($sub));

        $this->db->select('fk_id_trailer');

        $this->db->join('param_vehicle', 'inspection_daily.fk_id_trailer = param_vehicle.id_vehicle', 'INNER');
        $this->db->where('(fk_id_trailer, date_issue) IN (SELECT fk_id_trailer, MAX(date_issue) FROM inspection_daily GROUP BY fk_id_trailer)');
        $this->db->where('type_level_2 =', 5);
        $this->db->where('date_issue <', $date);
        $this->db->where('param_vehicle.state =', 1);
        $this->db->order_by('fk_id_trailer', 'asc');

        $trailersMonthsInspect = $this->db->get('inspection_daily');

        if ($trailersMonthsInspect->num_rows() > 0) {
            $trailersMonthsInspect = $trailersMonthsInspect->result_array();
        } else {
            $trailersMonthsInspect = false;
        }

        foreach ($trailersMonthsInspect as $key => $value) {
            $clean[] = $value['fk_id_trailer'];
        }

        $this->db->select('id_vehicle');

        $this->db->where('type_level_2 =', 5);
        $this->db->where('param_vehicle.id_vehicle NOT IN (
            SELECT fk_id_trailer
            FROM inspection_daily
            JOIN param_vehicle ON inspection_daily.fk_id_trailer = param_vehicle.id_vehicle
            WHERE (fk_id_trailer, date_issue) IN (SELECT fk_id_trailer, MAX(date_issue) FROM inspection_daily GROUP BY fk_id_trailer)
            AND type_level_2 = 5
            AND param_vehicle.state = 1
            ORDER BY id_inspection_daily DESC
        )');
        $this->db->where('state =', 1);

        $trailersNotInspect = $this->db->get('param_vehicle');

        if ($trailersNotInspect->num_rows() > 0) {
            $trailersNotInspect = $trailersNotInspect->result_array();
        } else {
            $trailersNotInspect = false;
        }

        foreach ($trailersNotInspect as $key => $value) {
            $clean[] = $value['id_vehicle'];
        }

        $clean = implode(",", $clean);

        $sql = "SELECT description, state
        FROM param_vehicle
        WHERE type_level_2 = 5 AND id_vehicle IN ($clean) AND param_vehicle.state = 1";

        $query = $this->db->query($sql);
        $query = $query->result_array();

        return $query;
    }
}
