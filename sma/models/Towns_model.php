<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Towns_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addTown($data = array())
    {
        if ($this->db->insert('cities', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }

    public function getAllTowns()
    {
        $q = $this->db->get('cities');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllTownsWithCounties()
    {
        $this->db->select('sma_cities.id as id,sma_currencies.french_name,sma_cities.city')
            ->join("sma_currencies","sma_cities.county_id=sma_currencies.id","left");
        $q = $this->db->get('sma_cities');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTownByID($id)
    {
        $q = $this->db->get_where('cities', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getTownByName($name)
    {
        $q = $this->db->get_where('cities', array('city' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateTown($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('cities', $data)) {
            return true;
        }
        return false;
    }

    public function deleteTown($id)
    {

        if ($this->db->delete('cities', array('id' => $id))){
            return true;
        }
        return FALSE;
    }

}
