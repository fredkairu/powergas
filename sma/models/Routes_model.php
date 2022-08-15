<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Routes_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addRoute($data = array())
    {
        if ($this->db->insert('routes', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }

    public function getRouteByID($id)
    {
        $q = $this->db->get_where('routes', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllocationByShopId($id,$route_id)
    {
        $q = $this->db->get_where('shop_allocations', array('shop_id' => $id,'route_id' => $route_id),1000);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getAllocationByDays($id)
    {
        //$q = $this->db->get_where('allocation_days', array('allocation_id' => $id), 1);
        $q = $this->db->get_where('allocation_days', array('allocation_id' => $id),1000);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        
    }

    public function getAllRoutes()
    {
        $q = $this->db->get('routes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllDays()
    {
        $q = $this->db->get('days_of_the_week');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getVroomRoutes($vehicle_id,$day,$salesman_id)
    {
    $data = array(
    'action' => 'fetch_shops',
    'vehicle_id' => $vehicle_id,
    'day' => $day,
    'salesman_id' => $salesman_id
); 
    $parsed_string=http_build_query($data);
    $url="http://localhost:4000/vroom-php/endpoint.php?$parsed_string"; 
    //$url="http://localhost:4000/vroom-php/endpoint.php?action=fetch_shops&vehicle_id=21&day=3&salesman_id=969";
   $curl = curl_init($url);
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //for debug only!
   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

   $resp = curl_exec($curl);
   curl_close($curl);
   //var_dump($resp);

        //$response = array("success" => "1", "routes" =>  $resp );
        
        return $resp;
    }
    public function getAllocationsByID($id)
    {
        $this->db->select('sma_allocation_days.id as id, sma_allocation_days.day as day,sma_allocation_days.duration as duration,sma_allocation_days.allocation_id as allocation_id, sma_allocation_days.salesman_id as vehicle_id');

        $q = $this->db->get_where('sma_allocation_days',array('id'=>$id),1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateRoute($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('routes', $data)) {
            return true;
        }
        return false;
    }
    public function updateDuration($id, $data = array())
    {
        $this->db->where('allocation_id', $id);
        if ($this->db->update('allocation_days', $data)) {
            return true;
        }
        return false;
    }
    public function updateDurationAll($id,$day, $data = array())
    {
        //$this->db->where('allocation_id', $id);
        if ($this->db->update('allocation_days', $data,array(
            'allocation_id' => $id,'day' => $day))) {
            return true;
        }
        return false;
        
    }
    public function updateDurationSet($vehicleroutes=array())
    {
        foreach($vehicleroutes as $vehicleroute)
        {
            if(isset($vehicleroute['id']))
            {
            $datar = array(
                'duration' => $vehicleroute['duration'],
                'distance' => 0.00,
                
            );
            $this->updateDuration($vehicleroute['id'], $datar);
            //echo $datar['duration'];
            

        }
        }
        
    }
    public function getduplicateallocation($id,$day,$duration,$vehicleid)
    {
        //$q = $this->db->get_where('allocation_days', array('allocation_id' => $id), 1);
        $q = $this->db->get_where('allocation_days', array('allocation_id' => $id,'day' => $day,'duration' => $duration,'salesman_id' => $vehicleid),1000);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        
    }
    public function getVehicleallocation($day,$vehicleid)
    {
        //$q = $this->db->get_where('allocation_days', array('allocation_id' => $id), 1);
        $q = $this->db->get_where('allocation_days', array('day' => $day,'salesman_id' => $vehicleid,'start_point' => 1),1000);
        if ($q->num_rows() > 0) {
            return true;
        }
        return false; 
    }
    public function updateDistance($id, $data = array())
    {
        $this->db->where('allocation_id', $id);
        if ($this->db->update('allocation_days', $data)) {
            return true;
        }
        return false;
    }

    public function deleteRoute($id)
    {

        if ($this->db->delete('routes', array('id' => $id))){
            return true;
        }
        return FALSE;
    }

}
