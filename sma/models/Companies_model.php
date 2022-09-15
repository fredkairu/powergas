<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Companies_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomers()
    {
        $this->db->select('id,name');
        $q = $this->db->get('customers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getShopsForCustomer($id)
    {
                 $this->db
                ->select("sma_shops.id as id,sma_shops.shop_name as shop,sma_shop_allocations.id as all_id,sma_customers.name as cust")
                ->from("sma_shops")
                ->join("sma_customers","sma_customers.id=sma_shops.customer_id","left")
                ->join("sma_shop_allocations","sma_shop_allocations.shop_id=sma_shops.id","left")
                ->where("sma_customers.id",$id)
                ->order_by('id','ASC')
                ->group_by('id');
                $query= $this->db->get();
                $result=$query->result();
                //print_r($result);
                

                return $result;
    }
    public function getDays1($all_id)
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
    
    public function getAllCustomersWithRouteId($route_id)
    {
        
        $this->db->select('sma_customers.id as id,sma_customers.name')
            ->join("sma_routes","sma_shops.route_id=sma_routes.id","left")
            ->join("sma_customers","sma_shops.customer_id=sma_customers.id","left")
            ->group_by("sma_customers.id");

        $q = $this->db->get_where('shops',array('route_id'=>$route_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getADistributorCustomer($id)
    {
        
          $this->db->select('customer_dist_sanofi_mapping.id,sma_customers.name,currencies.country,companies.name,customer_dist_sanofi_mapping.distributor_naming,customer_dist_sanofi_mapping.distributor')

    ->join('companies', 'companies.id=customer_dist_sanofi_mapping.distributor', 'left')
    ->join('customers', 'customers.id=customer_dist_sanofi_mapping.customer_id', 'left')
    ->join('currencies', 'customer_dist_sanofi_mapping.country=currencies.id', 'left')
                 //  ->where('companies', array('group_name' =>'customer'))
            ->order_by('customer_dist_sanofi_mapping.id', 'asc');
          
      $q = $this->db->get_where('customer_dist_sanofi_mapping',array('customer_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getCustomerShops($id)
    {

        $this->db->select('sma_shops.id as id,sma_shops.shop_name,sma_shops.lat,sma_shops.lng,sma_shops.day,sma_routes.name as route_name')
            ->join("sma_routes","sma_shops.route_id=sma_routes.id","left");

        $q = $this->db->get_where('shops',array('customer_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCustomerLimit($id)
    {
        $this->db->select('sma_credit_limit.id,sma_credit_limit.cash_limit')
            ->join("sma_customers","sma_credit_limit.customer_id=sma_customers.id","left");

        $q = $this->db->get_where('sma_credit_limit',array('customer_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllCustomersShops($id)
    {

        $this->db->select('sma_shops.id as id,UPPER(sma_shops.shop_name) as shop_name,sma_shops.lat,sma_shops.lng,sma_routes.name as route_name, UPPER(sma_customers.name) as name, sma_vehicles.plate_no, sma_vehicles.id as vehicle_id, sma_companies.name, sma_companies.id as salesperson_id,sma_companies.color as color')
            ->join("sma_routes","sma_shops.route_id=sma_routes.id","left")
            ->join("sma_vehicle_route","sma_vehicle_route.route_id=sma_routes.id","left")
            ->join("sma_vehicles","sma_vehicle_route.vehicle_id=sma_vehicles.id","left")
            ->join("sma_companies","sma_companies.vehicle_id=sma_vehicles.id","left")
            ->join("sma_customers","sma_shops.customer_id=sma_customers.id","left");

        $q = $this->db->get('shops');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getCustomersShops($id,$route_id)
    {

        $this->db->select('sma_shops.id as id,sma_shops.shop_name,sma_shops.lat,sma_shops.lng,sma_shops.day,sma_routes.name as route_name')
            ->join("sma_routes","sma_shops.route_id=sma_routes.id","left");

        $q = $this->db->get_where('shops',array('customer_id'=>$id,'route_id'=>$route_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getCustomerShop($id)
    {

        $q = $this->db->get_where('shops', array('id' => $id),1);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function get_user_data($id)
    {
        $day_of_the_week = date("l");
        if($day_of_the_week=="Monday"){
            $day = 1;
        }elseif ($day_of_the_week=="Tuesday"){
            $day = 2;
        }elseif ($day_of_the_week=="Wednesday"){
            $day = 3;
        }elseif ($day_of_the_week=="Thursday"){
            $day = 4;
        }elseif ($day_of_the_week=="Friday"){
            $day = 5;
        }elseif ($day_of_the_week=="Saturday"){
            $day = 6;
        }elseif ($day_of_the_week=="Sunday"){
            $day = 7;
        }

        $this->db->select('sma_users.id, sma_companies.id as salesman_id, sma_users.username, sma_users.email, sma_users.first_name,
         sma_users.last_name, sma_users.phone, sma_users.avatar, sma_companies.distributor_id,
          sma_companies.vehicle_id, sma_vehicles.plate_no, sma_vehicle_route.route_id, sma_vehicles.discount_enabled')
            ->join("sma_companies","sma_users.company_id=sma_companies.id","left")
            ->join("sma_vehicles","sma_companies.vehicle_id=sma_vehicles.id","left")
            ->join("sma_vehicle_route","sma_vehicle_route.vehicle_id=sma_vehicles.id","left")
            ->join("sma_routes","sma_vehicle_route.route_id=sma_routes.id","left");

        $q = $this->db->get_where('sma_users',array('sma_companies.id'=>$id,'sma_vehicle_route.day'=>$day));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }
     
     public function updateUserMac($id,$mac)
    {
    $userid = $id;
    $macaddress = array('mac_address' => $mac);    
    $this->db->where('id', $userid);
    $this->db->update('users', $macaddress);
    return true;
    }
    public function implodeMac($mac)
    {

    $mac=explode(':',$mac);
    $macfinal=$mac[0].''.$mac[1].''.$mac[2].''.$mac[3].''.$mac[4].''.$mac[5];
    return $macfinal;
    }
    public function getSalespersonsCustomers($id)
    {

        $q = $this->db->get_where('customers', array('salesman_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getShopById($id)
    {

        $q = $this->db->get_where('shops', array('id' => $id),1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getAllocationById($id) 
    {

        $this->db->select('shop_allocations.id as id,shops.shop_name as shop_name,routes.name as route_name,routes.id as route_id')

            ->join('sma_shops', 'shop_allocations.shop_id=shops.id', 'right')
            ->join('sma_routes', 'shop_allocations.route_id=routes.id', 'right');

        $q = $this->db->get_where('shop_allocations', array('shop_allocations.id' => $id),1);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getShopAllocations($id)
    {

        $this->db->select('shop_allocations.id,shops.shop_name,routes.name as route_name')

            ->join('sma_shops', 'shop_allocations.shop_id=shops.id', 'right')
            ->join('sma_routes', 'shop_allocations.route_id=routes.id', 'right');

        $q = $this->db->get_where('shop_allocations', array('shop_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllocationDays($id)
    {

        $this->db->select('sma_allocation_days.id,sma_days_of_the_week.name as day,sma_allocation_days.expiry,sma_shops.shop_name,sma_routes.name')
            ->join('sma_shop_allocations', 'sma_shop_allocations.id=sma_allocation_days.allocation_id', 'right')
            ->join('sma_shops', 'shop_allocations.shop_id=shops.id', 'right')
            ->join('sma_routes', 'shop_allocations.route_id=routes.id', 'right')
            ->join("sma_days_of_the_week","sma_allocation_days.day=sma_days_of_the_week.id","right");;

        $q = $this->db->get_where('sma_allocation_days', array('sma_allocation_days.allocation_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllocationDay($id)
    {

        $this->db->select('sma_allocation_days.id,sma_allocation_days.day as day,sma_days_of_the_week.name as day_name,sma_allocation_days.expiry,sma_shops.shop_name,sma_routes.name')
            ->join('sma_shop_allocations', 'sma_shop_allocations.id=sma_allocation_days.allocation_id', 'right')
            ->join('sma_shops', 'shop_allocations.shop_id=shops.id', 'right')
            ->join('sma_routes', 'shop_allocations.route_id=routes.id', 'right')
            ->join("sma_days_of_the_week","sma_allocation_days.day=sma_days_of_the_week.id","right");;

        $q = $this->db->get_where('sma_allocation_days', array('sma_allocation_days.id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    
    public function getAllocationsByRouteDay($id,$day)
    {

        $this->db->select('sma_shop_allocations.id,sma_shops.shop_name,sma_routes.name as route_name,sma_shop_allocations.shop_id as shop_id,sma_customers.name as customer_name')
            ->join('sma_allocation_days', 'sma_shop_allocations.id=allocation_days.allocation_id', 'right')
            ->join('sma_shops', 'sma_shop_allocations.shop_id=sma_shops.id', 'right')
            ->join('sma_routes', 'sma_shop_allocations.route_id=sma_routes.id', 'right')
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id', 'right');

        $q = $this->db->get_where('sma_shop_allocations', array('sma_shop_allocations.route_id' => $id,'sma_allocation_days.day' => $day ));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function updateCustomerShop($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('shops', $data)) {
            return true;
        }
        return false;
    }
    
    public function addTicketHandle($data = array())
    {
        $this->db->where('id', $data['ticket_id']);
        if ($this->db->update('tickets', array('status'=>1))) {
            if ($this->db->insert('sma_ticket_handles', $data)) {
            return true;
            }
    
        }
        return false;
    }
    
    public function deactivateCustomer2($id)
    {
        $this->db->where('id', $id);
        if ($this->db->update('companies', array('status' => 0))) {
            $this->db->where('company_id', $id);
            if ($this->db->update('users', array('active' => 2))) {
                return true;
            }
        }
        return false;
    }
    
    public function activateCustomer2($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('companies', array('status' => 1))) {
            $this->db->where('company_id', $id);
            if ($this->db->update('users', array('active' => 1))) {
                return true;
            }
        }
        return false;
    }
    
    public function deleteCustomerShop($id)
    {

        if ($this->db->delete('shops', array('id' => $id))) {
            return true;
        }
        return FALSE;
            
    }
    
    public function deleteTicket($id)
    {

        if ($this->db->delete('tickets', array('id' => $id))) {
            return true;
        }
        return FALSE;
            
    }
    public function deleteSmsCode($id)
    {

        if ($this->db->delete('verify_code', array('id' => $id))) {
            return true;
        }
        return FALSE;
            
    }
    
    public function deleteShopAllocation($id)
    {

        if ($this->db->delete('shop_allocations', array('id' => $id)) && 
        $this->db->delete('allocation_days', array('allocation_id' => $id))) {
            return true;
        }
        return FALSE;
            
    }

    public function getCustomerPaymentMethods($id)
    {

        $this->db->select('customer_payment_methods.id,payment_methods.name')

            ->join('payment_methods', 'payment_methods.id=customer_payment_methods.payment_method_id', 'right');

        $q = $this->db->get_where('customer_payment_methods',array('customer_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getCustomerPaymentMethod($id)
    {

        $q = $this->db->get_where('customer_payment_methods', array('id' => $id),1);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function checkCustomerPaymentMethodExists($customer_id,$payment_method_id)
    {

        $q = $this->db->get_where('customer_payment_methods', array('customer_id' => $customer_id,'payment_method_id' => $payment_method_id),1);
        if ($q->num_rows() > 0) {
            
            return true;
        }
        return false;
    }
    
    public function updateCustomerPaymentMethod($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('customer_payment_methods', $data)) {
            return true;
        }
        return false;
    }
    
    public function deleteCustomerPaymentMethod($id)
    {

        if ($this->db->delete('customer_payment_methods', array('id' => $id))) {
            return true;
        }
        return FALSE;
            
    }
    
    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    
    public function getAllCustomerCompanies1()
    {
        $q = $this->db->get_where('customer_dist_sanofi_mapping');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    
    public function getAllCustomerCustomers()
    {
        $q = $this->db->get_where('customers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDistributorTargets($distributor_id)
    {
        

        $this->db->select('distributor_targets.id as id,products.name,distributor_targets.target,distributor_targets.month')

            ->join('products', 'distributor_targets.product_id=products.id', 'left');

        $q = $this->db->get_where('distributor_targets',array('distributor_id'=>$distributor_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSalesmanTargets($salesman_id)
    {

        $this->db->select('sma_salesman_targets.id as id,products.name,sma_salesman_targets.target')

            ->join('products', 'sma_salesman_targets.product_id=products.id', 'left');

        $q = $this->db->get_where('sma_salesman_targets',array('salesman_id'=>$salesman_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addDistributorTarget($data){
        if ($this->db->insert('distributor_targets', $data)) {
            return true;
        }
        return false;
    }

    public function addSalesManTarget($data){
        if ($this->db->insert('sma_salesman_targets', $data)) {
            return true;
        }
        return false;
    }
         
        public function addAlignment($data)
    {
        if ($this->db->insert('alignments', $data)) {
            return true;
        }
        return false;
    }
    
          public function addCustomerAlignment($data)
    {
        if ($this->db->insert('customer_alignments', $data)) {
            return true;
        }
        return false;
    }

    public function getAllDistributorCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'distributor'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllSalespeople()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'sales_person'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    

    public function getAllSupplierCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomerGroups()
    {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyUsers($company_id)
    {
        $q = $this->db->get_where('users', array('company_id' => $company_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDistributorsTargetByID($id)
    {
        $q = $this->db->get_where('sma_distributor_targets', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSalesManTargetByID($id)
    {
        $q = $this->db->get_where('sma_salesman_targets', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getcustomerByID($id)
    {
        $q = $this->db->get_where('customers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
     public function getemployeeByID($id)
    {
        $q = $this->db->get_where('employee', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getcustomermsrByID($id)
    {
        $q = $this->db->get_where('sma_customer_alignments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    
        public function getsalesTeamByID($id)
    {
        $q = $this->db->get_where('sales_team_alignments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

     public function addcustomerMapping($customers)
    {
    if (!empty($customers)) {
            foreach ($customers as $customer) {
               $q = $this->db->get_where('customer_dist_sanofi_mapping', array('customer_id' =>$customer["customer_id"],"distributor"=>$customer["distributor"],"distributor_naming"=>$customer["distributor_naming"]), 1);
        if ($q->num_rows() > 0) {
           $this->db->update('customer_dist_sanofi_mapping', $customer,array('customer_id' =>$customer["customer_id"],"distributor"=>$customer["distributor"],"distributor_naming"=>$customer["distributor_naming"]), 1);
        } else{
              $this->db->insert('customer_dist_sanofi_mapping', $customer);
        }
                
            }
            return true;
        }
        return false;
          
    }
    public function getCompanyByName($name)
    {
         $trimmedname=str_replace(" ","",$name);
         $trimmednamee=str_replace("'","-",$trimmedname);
         $this->db->select("id");
        $this->db->where(" REPLACE(name,' ','')='".$trimmednamee."'");
        $q = $this->db->get('companies',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
        public function getCustomerByName($name)
    {
         $trimmedname=str_replace(" ","",$name);
         $trimmednamee=str_replace("'","-",$trimmedname);
         $this->db->select("id,name,group_name");
        $this->db->where(" REPLACE(name,' ','')='".$trimmednamee."'");
        $q = $this->db->get('customers',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
  
    
    public function getCompanyByNameAndCountry($name,$country)
    {
         $trimmedname=str_replace(" ","",$name);
         $trimmednamee=str_replace("'","-",$trimmedname);
         $this->db->select("id,name");
        $this->db->where(" REPLACE(name,' ','')='".$trimmednamee."'");
        $this->db->where("country='".$country."'");
        $q = $this->db->get('companies',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    
    
        public function getCustomerByNameAndCountry($name,$country)
    {
         $trimmedname=str_replace(" ","",$name);
         $trimmednamee=str_replace("'","-",$trimmedname);
         $this->db->select("id");
        $this->db->where(" REPLACE(name,' ','')='".$trimmednamee."'");
        $this->db->where("country='".$country."'");
        $q = $this->db->get('customers',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    
    public function getCompanyByEmail($email)
    {
        $q = $this->db->get_where('companies', array('email' => $email), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCompany($data = array())
    {
        
        //check for erp suppliers and debtors lastid
       
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
             if($data['group_name']=="supplier"){
                 $id=$this->findLastIdSupplier($cid);
                 if($id>0){
                     $this->db->where('id',$cid);
                     $newdata["id"]=$id+1;
                     $this->db->update('companies',$newdata);
                     $cid=$id+1;
                 }
        }
        
        else if($data['group_name']=="customer"){
                $id=$this->findLastIdCustomer($cid);
                 if($id>0){
                     $this->db->where('id',$cid);
                     $newdata["id"]=$id+1;
                     $this->db->update('companies',$newdata);
                     $cid=$id+1;
                }
        }
        $data["person_id"]=$cid;
        //add to ERP
        //$this->addPersonToErp($data);
      return $cid; 
    }
      return false;
    }
    
     public function addEmployee($data)
    {
        if ($this->db->insert("employee", $data)) {
            return true;
        }
        return false;
    }
    
    public function addShop($data)
    {
        if ($this->db->insert("shops", $data)) {
            return true;
        }
        return false;
    }
    
    public function getLimit($id)
    {
        $q = $this->db->get_where('sma_credit_limit', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addLimit($data,$id)
    {
        $q = $this->db->get_where('credit_limit', array('customer_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }else{
            if ($this->db->insert("credit_limit", $data)) {
                return true;
            }
            return false;
        }

    }

    public function editLimit($data,$id)
    {
        $this->db->where('id', $id);
        if ($this->db->update('sma_credit_limit', $data)) {
            return true;
        }
        return false;

    }

    public function deleteLimit($id){
        if ($this->db->delete('sma_credit_limit', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
    public function addAllocation($data)
    {
        if ($this->db->insert("shop_allocations", $data)) {
            $insert_id = $this->db->insert_id();

            return  $insert_id;
        }
        return false;
    }

    public function updateAllocation($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('shop_allocations', $data)) {
            return true;
        }
        return false;
    }

    public function deleteAllocation($id)
    {

        if ($this->db->delete('shop_allocations', array('id' => $id)) &&
        $this->db->delete('allocation_days', array('allocation_id' => $id)) ) {
            return true;
        }
        return FALSE;

    }

    public function addAllocationDay($data)
    {
        if ($this->db->insert("allocation_days", $data)) {
            return true;
        }
        return false;
    }

    public function updateAllocationDay($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('allocation_days', $data)) {
            return true;
        }
        return false;
    }

    public function deleteAllocationDay($id)
    {

        if ($this->db->delete('allocation_days', array('id' => $id))) {
            return true;
        }
        return FALSE;

    }
    
    public function addCustomerPaymentMethod($data)
    {
        $q = $this->db->get_where('customer_payment_methods', array('customer_id' => $data->customer_id,'payment_method_id' => $data->payment_method_id), 1);
        if ($q->num_rows() > 0) {
            return true;
        }else{
            if ($this->db->insert("customer_payment_methods", $data)) {
                return true;
            }
        }
        return false;
    }
    
    public function getAllPaymentMethods()
    {
        $q = $this->db->get('payment_methods');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
        public function addCustomer($data = array())
    {
        
        //check for erp suppliers and debtors lastid
       //print_r($data);
       //die();
        if ($this->db->insert('customers', $data)) {
            $cid = $this->db->insert_id();
             if($data['group_name']=="supplier"){
                 $id=$this->findLastIdSupplier($cid);
                 if($id>0){
                 $this->db->where('id',$cid);
                 $newdata["id"]=$id+1;
$this->db->update('customers',$newdata);  
$cid=$id+1;
                 }
        }
        
        else if($data['group_name']=="customer"){
                $id=$this->findLastIdCustomer($cid);
                 if($id>0){
                 $this->db->where('id',$cid);
                 $newdata["id"]=$id+1;
$this->db->update('customers',$newdata);  
$cid=$id+1;
        }
        
            
            
           
        }
            $data["person_id"]=$cid;
            //add to ERP
            
           // $this->addPersonToErp($data);
      return $cid; 
    }
      return false;
    }
    
    
      public function addSubCompany($data = array())
    {
          foreach ($data as $value) {
              
         
$last_id=$this->db->insert('distributor_mapping',$value);  
 }
      return $last_id; 
 
    }
    
    public function findLastIdSupplier($id){
    $this->db->set_dbprefix('0_');
    $q = $this->db->get_where('suppliers', array('supplier_id' =>$id));
        if ($q->num_rows() > 0) {
       $maxid = $this->db->query('SELECT MAX(supplier_id) AS `maxid` FROM `0_suppliers`')->row()->maxid;
        $this->db->set_dbprefix('sma_');
      return $maxid;      
    }
     $this->db->set_dbprefix('sma_');
    return 0;
    }
    public function findLastIdCustomer($id){
       $this->db->set_dbprefix('0_');
    $q = $this->db->get_where('debtors_master', array('debtor_no' =>$id));
        if ($q->num_rows() > 0) {
       $maxid = $this->db->query('SELECT MAX(debtor_no) AS `maxid` FROM `0_debtors_master`')->row()->maxid;
        $this->db->set_dbprefix('sma_');
      return $maxid;      
    }
     $this->db->set_dbprefix('sma_');
    return 0; 
    }
    public function addPersonToErp($data){
        
        $this->db->set_dbprefix('0_');
        if($data['group_name']=="supplier"){
       
         $supplier['supplier_id']=$data['person_id'];
            $supplier['supp_name']=$data['name'];
                    $supplier['supp_ref']=$data['name'];
                 $supplier['address']=$data['address'];
                         $supplier['supp_address']=$data['address'];
                         $supplier['gst_no']=$data['vat_no'];
                         $supplier['contact']=$data['phone'];
                         $supplier['curr_code']='KS';
                         $supplier['payment_terms']='4';
                         $supplier['tax_included']=1;
                         $supplier['dimension_id']=0;
                         $supplier['dimension2_id']=1;
                         $supplier['tax_group_id']=1;
                         $supplier['credit_limit']=10000;
                         $supplier['purchase_account']=3011130;
                         $supplier['payable_account']=2100; 
                         $supplier['payment_discount_account']=5060;
                         $supplier['inactive']=0;
         $this->db->insert('suppliers',$supplier);
     unset($supplier);
        }
        else if($data['group_name']=="customer"){
            
          $customer['debtor_no']=$data['person_id'];
           $customer['name']=$data['name'];
                 $customer['debtor_ref']=$data['name'];
                  $customer['address']=$data['address'];
                  $customer['tax_id']=$data['vat_no'];
                 $customer['curr_code']='KS';
                 $customer['sales_type']=1; 
                 $customer['dimension_id']=0;
                 $customer['dimension2_id']=0;
                 $customer['credit_status']=1; 
                 $customer['payment_terms']=4;//credit customer 
                 $customer['discount']=0; 
                 $customer['credit_limit']=10000; 
                 $customer['inactive']=0;
           $this->db->insert('debtors_master',$customer);  
           //insert into customer branch
           
                   $custbranch['debtor_no']=$data['person_id'];
                   $custbranch['br_name']=$data['name'];
                   $custbranch['branch_ref']=$data['name'];
                   $custbranch['br_address']=$data['address'];
                   $custbranch['area']=1;
                   $custbranch['salesman']=1;
                   $custbranch['default_location'] ='DEF';
                   $custbranch['tax_group_id']=1;
                   $custbranch['sales_account']=100101;
                   $custbranch['sales_discount_account'] =4510;
                   $custbranch['receivables_account']=1200;
                   $custbranch['payment_discount_account'] =1060;
                   $custbranch['default_ship_via']=1;
                   $custbranch['br_post_address']=$data['address'];
                   $custbranch['group_no']=0;
                          $custbranch['inactive']=0;
           
           $this->db->insert('cust_branch',$custbranch);  
           
           
           
           
     unset($custbranch); 
            
            
        }
        
          $this->db->set_dbprefix('sma_');
    }
    
    
    
    
    
    
    
    
    

    public function updateCompany($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('companies', $data)) {
            return true;
        }
        return false;
    }

    public function updateDistributorTarget($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('sma_distributor_targets', $data)) {
            return true;
        }
        return false;
    }

    public function updateSalesManTarget($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('sma_salesman_targets', $data)) {
            return true;
        }
        return false;
    }
    
    public function updateCustomer($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('customers', $data)) {
            return true;
        }
        return false;
    }
    
    public function updateEmployee($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('employee', $data)) {
            return true;
        }
        return false;
    }

    public function updateSfMsralignment($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('customer_alignments', $data)) {
            return true;
        }
        return false;
    }

    public function addCompanies($data = array())
    {
        if ($this->db->insert_batch('companies', $data)) {
            return true;
        }
        return false;
    }
    
        public function addCustomers($data = array())
    {
        if ($this->db->insert_batch('customers', $data)) {
            return true;
        }
        return false;
    }
    public function addEmployeebatch($data = array())
    {
        if ($this->db->insert_batch('employee', $data)) {
            return true;
        }
        return false;
    }
    
          public function addAlignmentsBatch($data = array())
    {
        if ($this->db->insert_batch('alignments', $data)) {
            return true;
        }
        return false;
    }
    
    
            public function addCustomerAlignmentsBatch($data = array())
    {
        if ($this->db->insert_batch('customer_alignments', $data)) {
            return true;
        }
        return false;
    }
    
    public function addStAlignmentsBatch($data = array(),$teamdata = array(),
            $dsmdata = array(),$dsmemployeedata = array(),$msrdata = array(),$msremployeedata = array())
    {
        			        $i = 0;  
        			       
               foreach ($data as $dt) {

                
				$this->db->insert('sma_team', $teamdata[$i]);
                $team_id = $this->db->insert_id();
			
                $dsmdata[$i]['team_id'] = $team_id;
                $dsmdata[$i]['team_name'] = $teamdata[$i]['name'];
				
                $this->db->insert('dsm_alignments', $dsmdata[$i]);
				
				$this->db->insert('employee', $dsmemployeedata[$i]);
				$msrdata[$i]['team_id'] = $team_id;
                $msrdata[$i]['team_name'] = $teamdata[$i]['name'];
			    $this->db->insert('msr_alignments', $msrdata[$i]);
			    $this->db->insert('employee', $msremployeedata[$i]);
				
			$i++;
				  }
        return true;
    }
    
    
            public function addStAlignmentsBatch1($data = array())
    {
        if ($this->db->insert_batch('sales_team_alignments', $data)) {
            return true;
        }
        return false;
    }

    public function deleteCustomer($id)
    {
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'customer')) && $this->db->delete('users', array('company_id' => $id))) {
            $this->db->delete('companies', array('parent_company' => $id, 'group_name' => 'customer'));
            return true;
        }
        return FALSE;
    }
    
    public function deleteSalesPerson($id)
    {
        if ($this->getSalesPersonSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'sales_person')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
     public function remove_customermsrdata()
    {
        //if ($this->getCustomerSales($id)) {
         //   return false;
       // }
        if ($this->db->delete('customer_alignments', array('id >1')))  {
           // $this->db->delete('companies', array('parent_company' => $id, 'group_name' => 'customer'));
            return true;
        }
        return FALSE;
    }
    
     public function deleteCustomer1($id)
    {
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('customers', array('id' => $id, 'group_name' => 'customer')) && $this->db->delete('users', array('company_id' => $id))) {
            $this->db->delete('customers', array('parent_company' => $id, 'group_name' => 'customer'));
            return true;
        }
        return FALSE;
    }
    public function deleteEmployee($id)
    {
       // if ($this->getSupplierPurchases($id)) {
       //     return false;
       // }
        if ($this->db->delete('employee', array('id' => $id) )) {
            return true;
        }
        return FALSE;
    }

    public function deleteDistributorTarget($target_id)
    {
        if ($this->db->delete('distributor_targets', array('id' => $target_id) )) {
            return true;
        }
        return FALSE;
    }
 public function deletemsrcustalignement($id)
    {
       // if ($this->getSupplierPurchases($id)) {
       //     return false;
       // }
        if ($this->db->delete('customer_alignments', array('id' => $id) )) {
            return true;
        }
        return FALSE;
    }
    public function deleteSupplier($id)
    {
        if ($this->getSupplierPurchases($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'supplier')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteBiller($id)
    {
        if ($this->getBillerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'biller'))) {
            return true;
        }
        return FALSE;
    }

    public function getBillerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, company as text");
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'biller'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, CONCAT(company, ' (', name, ')') as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'distributor'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getSupplierSuggestions($term, $limit = 10)
    {

               $this->db->select("companies.id, sma_companies.name as text");
        $this->db->where(" (sma_companies.id LIKE '%" . $term . "%' OR sma_companies.name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'distributor'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSales($id)
    {
        $this->db->where('customer_id', $id)->from('sales');
        return $this->db->count_all_results();
    }
    
    public function getSalesPersonSales($id)
    {
        $this->db->where('salesman_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getBillerSales($id)
    {
        $this->db->where('biller_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getSupplierPurchases($id)
    {
        $this->db->where('supplier_id', $id)->from('purchases');
        return $this->db->count_all_results();
    }

}
