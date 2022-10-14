<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicles_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addVehicle($data = array())
    {
        if ($this->db->insert('vehicles', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }
    
    public function addStockTakingHistory($data = array())
    {
        if ($this->db->insert('stock_taking_history', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }
    public function disabletemporary($data = array()){
        if ($this->db->insert('temporary_alloc_disable', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }
    public function getDays($all_id)
    {
        
          $this->db->select('days_of_the_week.id,days_of_the_week.name')
        ->join('days_of_the_week', 'days_of_the_week.id=allocation_days.day', 'left');
          
            $q = $this->db->get_where('allocation_days', array('allocation_id' => $all_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function addVehicleClosingStock($data = array())
    {
        if ($this->db->insert_batch('sma_closing_product_vehicle_quantities', $data)) {
            return true;
        }
        return false;
    }

    public function addVehicleRoute($data = array())
    {
        if ($this->db->insert('sma_vehicle_route', $data)) {
            $rid = $this->db->insert_id();
            return $rid;
        }
        return false;
    }

    public function getAllVehicles()
    {
        $q = $this->db->get('vehicles');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllDistributorsVehicles($distributor_id)
    {
        $q = $this->db->get_where('vehicles', array('distributor_id' => $distributor_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSalesmansVehicle($id)
    {
        $this->db->select("sma_vehicles.plate_no as name,sma_vehicles.id as id")
            ->join("sma_vehicles", 'sma_companies.vehicle_id = sma_vehicles.id', 'left');
        $q = $this->db->get_where('sma_companies', array('sma_companies.id =' => $id));;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getVehicleSalesman()
    {
        $this->db->select("sma_companies.id, sma_companies.name, sma_companies.email,
         sma_companies.phone, sma_vehicles.plate_no, sma_vehicles.distributor_id,
         sma_vehicle_route.vehicle_id, sma_vehicle_route.route_id,
         sma_routes.name as route_name")
            ->join("sma_vehicles", 'sma_companies.vehicle_id = sma_vehicles.id', 'left')
            ->join("sma_vehicle_route", 'sma_vehicles.id = sma_vehicle_route.vehicle_id', 'left')
            ->join("sma_routes", 'sma_vehicle_route.route_id = sma_routes.id', 'left')
            ->group_by('sma_companies.id');
        $q = $this->db->get_where('sma_companies', array('sma_companies.vehicle_id !=' => NULL, 'sma_companies.status'=>'1'));;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getVehicleByID($id)
    {
        $q = $this->db->get_where('vehicles', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getSalesmanID($id)
    {
        $q = $this->db->get_where('companies', array('vehicle_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getRouteByID($id)
    {
        $q = $this->db->get_where('routes', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getRouteByVehicleIDandDay($id,$day)
    {
        $q = $this->db->get_where('vehicle_route', array('vehicle_id' => $id,'day' => $day), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getVehicleStock($id,$distributor_id)
    {
        $this->db->select('sma_product_vehicle_quantities.id as id,sma_products.name as product_name,sma_products.price as product_price,sma_product_vehicle_quantities.quantity as product_quantity,sma_products.id as product_id')
            ->join("sma_products","sma_product_vehicle_quantities.product_id=sma_products.id","left")
            ->order_by('sma_products.id', 'ASC');
        $q = $this->db->get_where('sma_product_vehicle_quantities', array('sma_product_vehicle_quantities.vehicle_id' => $id,'sma_product_vehicle_quantities.distributor_id' => $distributor_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getVehicleSoldStock($vehicle_id,$distributor_id,$start_date,$end_date)
    {
        $this->db->select("sma_products.id, sma_products.name, SUM(sma_sale_items.quantity) AS soldQty, SUM(sma_sale_items.subtotal) as totalSale")
        ->join("sma_sale_items", 'sma_sale_items.product_id = sma_products.id', 'left')
        ->join("sma_sales", 'sma_sales.id = sma_sale_items.sale_id', 'left')
        ->join("sma_vehicles", 'sma_sales.vehicle_id = sma_vehicles.id', 'left')
        ->group_by('sma_products.id');
        $q = $this->db->get_where('sma_products', array('sma_sales.date >=' => $start_date,'sma_sales.date <=' => $end_date,'sma_vehicles.id'=>$vehicle_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getVehicleRoutes($id)
    {
        $this->db->select('sma_vehicle_route.id as id,sma_routes.name as route_name,sma_days_of_the_week.name as day,sma_vehicle_route.day as actual_day,sma_vehicle_route.route_id')
            ->join("sma_routes","sma_vehicle_route.route_id=sma_routes.id","right")
            ->join("sma_days_of_the_week","sma_vehicle_route.day=sma_days_of_the_week.id","right");

        $q = $this->db->get_where('sma_vehicle_route',array('vehicle_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    
    
    public function updateStock($vehicle_id,$distributor_id, $data = array())
    {
        //delete old stock data in table
        if ($this->db->delete('sma_product_vehicle_quantities', array('vehicle_id' => $vehicle_id,'distributor_id' => $distributor_id))){
            //insert new stock
            if ($this->db->insert_batch('sma_product_vehicle_quantities', $data) ) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function reverseVehicleStockTaking($vehicle_id,$product_id,$distributor_id,$difference )
    {
        //select where vehicle_id product_id and distributor_id
        $this->db->select('sma_product_vehicle_quantities.id,sma_product_vehicle_quantities.product_id,sma_product_vehicle_quantities.quantity');
        $q = $this->db->get_where('sma_product_vehicle_quantities', array('sma_product_vehicle_quantities.distributor_id' => $distributor_id,'sma_product_vehicle_quantities.vehicle_id' => $vehicle_id,
            'sma_product_vehicle_quantities.product_id' => $product_id),1);
        if ($q->num_rows() > 0) {
            $data = array(
                'sma_product_vehicle_quantities.quantity' => ($q->row()->quantity+$difference)
            );

            $this->db->where('id', $q->row()->id);
            if ($this->db->update('sma_product_vehicle_quantities', $data)) {
                return true;
            }
            return false;
        }
        return false;
    }
    
    public function updateVehicleStock($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('sma_product_vehicle_quantities', $data)) {
            return true;
        }
        return false;
    }

    public function getVehicleSuggestions($term, $limit = 10, $distributor_id)
    {

        $this->db->select("sma_vehicles.id, sma_vehicles.plate_no as text");
        $this->db->where(" (sma_vehicles.id LIKE '%" . $term . "%' OR sma_vehicles.plate_no LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('sma_vehicles', array('sma_vehicles.distributor_id' => $distributor_id), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getVehicleRouteByID($id)
    {
        $this->db->select('sma_vehicle_route.id as id, sma_vehicle_route.vehicle_id, sma_vehicle_route.route_id, sma_vehicle_route.day');

        $q = $this->db->get_where('sma_vehicle_route',array('sma_vehicle_route.id'=>$id),1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getVehicleByRouteID($id,$day)
    {
        $this->db->select('sma_vehicle_route.id as id, sma_vehicle_route.vehicle_id, sma_vehicle_route.route_id, sma_vehicle_route.day');

        $q = $this->db->get_where('sma_vehicle_route',array('sma_vehicle_route.route_id'=>$id,'sma_vehicle_route.day'=>$day),1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function checkVehicleRouteExists($distributor_id,$vehicle_id,$route_id,$day)
    {
        $this->db->select('sma_vehicle_route.id as id, sma_vehicle_route.vehicle_id, sma_vehicle_route.route_id, sma_vehicle_route.day');

        $q = $this->db->get_where('sma_vehicle_route',array(
            'sma_vehicle_route.distributor_id'=>$distributor_id,
            'sma_vehicle_route.vehicle_id'=>$vehicle_id,
            'sma_vehicle_route.route_id'=>$route_id,
            'sma_vehicle_route.day'=>$day),1);
        if ($q->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function updateVehicle($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('vehicles', $data)) {
            return true;
        }
        return false;
    }

    public function updateVehicleRoute($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('sma_vehicle_route', $data)) {
            return true;
        }
        return false;
    }

    public function deleteVehicle($id)
    {

        if ($this->db->delete('vehicles', array('id' => $id)) && $this->db->delete('vehicle_route', array('vehicle_id' => $id))){
            return true;
        }
        return FALSE;
    }
    public function deactivateAllocation($id,$today)
    {

        $data = array(
            'sma_allocation_days.active' => 0,
            'sma_allocation_days.disabled_date' => $today
        );
        $this->db->where('id', $id);
        if ($this->db->update('sma_allocation_days', $data)) {
            return true;
        }
        return false;
        
    }
    public function activateAllocation($id)
    {

        $data = array(
            'sma_allocation_days.active' => 1
        );
        $this->db->where('id', $id);
        if ($this->db->update('sma_allocation_days', $data)) {
            return true;
        }
        return false;
        
    }
    public function makeStart($id)
    {

        $data = array(
            'sma_allocation_days.start_point' => 1
        );
        $this->db->where('id', $id);
        if ($this->db->update('sma_allocation_days', $data)) {
            return true;
        }
        return false;
        
    }
    public function removeStart($id)
    {

        $data = array(
            'sma_allocation_days.start_point' => NULL
        );
        $this->db->where('id', $id);
        if ($this->db->update('sma_allocation_days', $data)) {
            return true;
        }
        return false;
        
    }

    public function deleteVehicleClosingStock($id,$distributor_id)
    {

        if ($this->db->delete('closing_product_vehicle_quantities', array('vehicle_id' => $id,'distributor_id' => $distributor_id,'CURDATE()'=>'created_at'))){
            return true;
        }
        return FALSE;
    }
    
    public function deleteVehicleRoute($id)
    {

        if ($this->db->delete('vehicle_route', array('id' => $id))){
            return true;
        }
        return FALSE;
    }
}
