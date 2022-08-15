<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cluster_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

 

    public function getClusters()
    {
        $q = $this->db->get('cluster');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getTeams()
    {
        $q = $this->db->get('team');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    
    function get_countries_cluster ($cluster){
        $this->db->select('currencies.id,country');
        $this->db->join("cluster","cluster.id=currencies.cluster");
       $this->db->where("cluster.name",$cluster);
       
        $query = $this->db->get('currencies');
        $cities = array();

        if($query->result()){
            foreach ($query->result() as $city) {
                $cities[$city->id] = $city->country;
            }
            return $cities;
        } else {
            return FALSE;
        }
    }
    
    

    

}
