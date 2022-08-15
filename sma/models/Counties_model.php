<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Counties_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addCounty($data = array())
    {
        if ($this->db->insert('currencies', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }

    public function getCountyByID($id)
    {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCounties()
    {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function updateCounty($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('currencies', $data)) {
            return true;
        }
        return false;
    }

    public function deleteCounty($id)
    {

        if ($this->db->delete('currencies', array('id' => $id))){
            return true;
        }
        return FALSE;
    }

}
