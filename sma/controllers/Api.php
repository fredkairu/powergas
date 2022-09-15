<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller
{

    function __construct()
    {

        parent::__construct();
        $this->lang->load('customers', $this->Settings->language);
        $this->lang->load('vehicles', $this->Settings->language);
        $this->load->library('ion_auth');
        $this->load->library('tsp');
        $this->load->model('auth_model');
        $this->load->library('form_validation');
        $this->load->model('companies_model');
        $this->load->model('products_model');
        $this->load->model('vehicles_model');
        $this->load->model('routes_model');
        $this->load->model('settings_model');
        $this->load->model('towns_model');
        $this->load->model('counties_model');
        $this->load->model('sales_model');
        $this->load->model('site');
    }

    function register()
    {
        $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
        $this->form_validation->set_rules('email', lang('email_address'), 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('phone', lang('phone'), 'required');
        $this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[25]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', lang('confirm_password'), 'required');
        $this->form_validation->set_rules('distributor_id', lang('Distributor'), 'required');

        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'group_id' => '13',
                'group_name' => 'sales_person',
                'distributor_id' => $this->input->post('distributor_id'),
                'phone' => $this->input->post('phone'),
            );
        }
        if ($this->form_validation->run() == true && $cid = $this->companies_model->addCompany($data)) {

            $this->ion_auth->register($this->input->post('first_name') . ' ' . $this->input->post('last_name'), $this->input->post('password'), $this->input->post('email'), array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone'),
                'gender' => "male",
                'group_id' => '13',
                'biller_id' => null,
                'company_id' => $cid,
                'warehouse_id' => null,), 1, 1);
            $response = array("success" => "1", "message" => "Registration successful");
        } else {

            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }

        echo json_encode($response);

    }
    
    /**function login()
    {
        $this->form_validation->set_rules('email', lang('email_address'), 'required');
        $this->form_validation->set_rules('password', lang('password'), 'required');
        //$this->form_validation->set_rules('macaddress', lang('macaddress'),'required');
        $macraw=$this->input->post('macaddress');
        if(isset($macraw)){
         $mac=$this->companies_model->implodeMac($macraw);
        if ($this->form_validation->run() == true) {
            $users = $this->auth_model->get_user_data($this->input->post('email'));
            if(empty($users->mac_address))
                { 
                    
                    $this->companies_model->updateUserMac($users->id,$mac);
                }

            if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'), false) and $users->mac_address==$mac) {

                //$users = $this->auth_model->get_user_data($this->input->post('email'));
                
                if($users->group_id==14 and $users->active==1 and $users->mac_address==$mac ){
                    $response = array("success" => "1", "message" => "login successful", "user" => $users);
                }else{
                    if($users->active==2 and $users->vehicle_id==null or  $users->vehicle_id==NULL or $users->vehicle_id==" "){
                        if($users->active==2){
                            $response = array("success" => "0", "message" => "Login unsuccessful. Account has been deactivated please contact your distributor");
                        }else if($users->vehicle_id==null or  $users->vehicle_id==NULL or $users->vehicle_id==" "){
                            $response = array("success" => "0", "message" => "Login unsuccessful. No vehicle or route attached please contact your distributor");
                        }
                    else if($users->mac_address != $mac){
                            $response = array("success" => "0", "message" => "The device could not be auntheticated");
                        }
                    }else{
                        $response = array("success" => "1", "message" => "login successful", "user" => $users);
                    }
                }
                
            }else{
                
                $response= array("success" => "0", "message" => $this->ion_auth->errors());
            }
        } else {
            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }
        
    }else{
     $response = array("success" => "0", "message" => "login failed.The device could not be auntheticated", "user" => $users);

    }
    echo json_encode($response);
    }**/
    function login()
    {
        $this->form_validation->set_rules('email', lang('email_address'), 'required');
        $this->form_validation->set_rules('password', lang('password'), 'required');
        $macraw=$this->input->post('macaddress');
        

        if ($this->form_validation->run() == true) {

            if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'), false)) {

                $users = $this->auth_model->get_user_data($this->input->post('email'));
                if(isset($macraw)){
         $mac=$this->companies_model->implodeMac($macraw);
        
        if(empty($users->mac_address))
                { 
                    
                    $this->companies_model->updateUserMac($users->id,$mac);
                }}
                if($users->group_id==14 and $users->active==1){
                    $response = array("success" => "1", "message" => "login successful", "user" => $users);
                }else{
                    if($users->active==2 and $users->vehicle_id==null or  $users->vehicle_id==NULL or $users->vehicle_id==" "){
                        if($users->active==2){
                            $response = array("success" => "0", "message" => "Login unsuccessful. Account has been deactivated please contact your distributor");
                        }else if($users->vehicle_id==null or  $users->vehicle_id==NULL or $users->vehicle_id==" "){
                            $response = array("success" => "0", "message" => "Login unsuccessful. No vehicle or route attached please contact your distributor");
                        }
                    }else{
                        $response = array("success" => "1", "message" => "login successful", "user" => $users);
                    }
                }
                
            }else{
                
                $response= array("success" => "0", "message" => $this->ion_auth->errors());
            }
        } else {
            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }
        echo json_encode($response);
    }
    
    
    function route()
    {
        $s = microtime(true);

        $tsp = new Tsp;

        //$tsp->_add(39.25,  106.30,  'Leadville,CO'); // 9th point (~30 seconds)
        $tsp->_add(39.18,  103.70,  'Limon,CO');
        $tsp->_add(38.50,  107.88,  'Montrose,CO');
        $tsp->_add(38.28,  104.52,  'Pueblo,CO');
        $tsp->_add(39.53,  107.80,  'Rifle,CO');
        $tsp->_add(38.53,  106.05,  'Salida,CO');
        $tsp->_add(40.48,  106.82,  'Steamboat Sp,CO');
        $tsp->_add(37.25,  104.33,  'Trinidad,CO');
        $tsp->_add(40.00,  105.87,  'Winter Park,CO');

        $tsp->compute();

        $e = microtime(true);
        $t = $e - $s;

        $response = array("success" => "1", "Shortest Route" => $tsp->shortest_route());

        echo json_encode($response);
    }
    
    //function routePlan($vehicle_id,$day,$salesman_id)
    function routePlan()
    {
    /**$data = array(
    'action' => 'fetch_shops',
    'vehicle_id' => $vehicle_id,
    'day' => $day,
    'salesman_id' => $salesman_id
);
    $parsed_string=http_build_query($data);
    $url="http://localhost:4000/vroom-php/endpoint.php?".'"'.$parsed_string.'"';**/
    $url="http://localhost:4000/vroom-php/endpoint.php?action=fetch_shops&vehicle_id=21&day=3&salesman_id=969";
   $curl = curl_init($url);
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //for debug only!
   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

   $resp = curl_exec($curl);
   curl_close($curl);
   //var_dump($resp);

        $response = array("success" => "1", "routes" =>  $resp );

        //echo json_encode($response);
        $responses=json_decode($resp,true);
        foreach($responses as $res)
        {
            echo $res['duration'];
        }
    }
    function routePlanTest()
    {
        $vehicleroutes=array();
        $vehicleroutes = $this->Routes_model->getVroomRoutes(21,3,969);

        //$response = array("success" => "1", "routes" =>  $resp );

        foreach($vehicleroutes as $res)
        {
            echo $res['id'];
        }
    }
    
    function getAllCustomers()
    {
        $customers =  $this->companies_model->getAllCustomers();

        $response = $customers;

        echo json_encode($response);
    }
    
    function getAllCustomersShops()
    {
        $customers =  $this->companies_model->getAllCustomersShops();

        $response = $customers;

        echo json_encode($response);
    }
    
    function getAllCustomersWithRouteId()
    {
        $this->form_validation->set_rules('route_id', lang("Route"), 'required');
        
        $route_id = $this->input->post('route_id');
        
        $customers =  $this->companies_model->getAllCustomersWithRouteId($route_id);

        $response = $customers;

        echo json_encode($response);
    }

    function processCartProducts(){

        
        $this->form_validation->set_rules('json', lang('JSON Required'), 'required');
        if ($this->form_validation->run() == true) {
            
            $params = json_decode($this->input->post('json'));

            //echo $params->discount;
            //echo "<br>";
            //echo $params->loyalty;
            //echo "<br>";
            //echo $params->customer_id;
            //echo "<br>";
            //echo $params->distributor_id;
            //echo "<br>";
            
            //die();
            
            $total_discount = 0;
            $total_loyalty = 0;
            $total_price = 0;
            $response = array();
            if($this->checkDiscount($params->vehicle_id)) {
                foreach($params->items as $item){
                    $total_discount += $item->discount;
                    //$total_loyalty += ($this->getLoyaltyPoints($item->product_id,$item->quantity,$params->distributor_id)*$item->quantity);
                    $total_price += ($item->price*$item->quantity);
                }

                // if($params->discount == 1){
                //     $response['total_discount'] = $total_discount;
                //     $response['total_loyalty'] = 0;
                //     $response['total_price'] = $total_price;
                //     $response['total_final_price'] = $total_price-$total_discount;
                // }
                // if($params->loyalty == 1){
                //     $response['total_loyalty'] = $total_loyalty;
                //     $response['total_discount'] = 0;
                //     $response['total_price'] = $total_price;
                //     $response['total_final_price'] = $total_price;
                // }
                $response['total_discount'] = $total_discount;
                $response['total_loyalty'] = 0;
                $response['total_price'] = $total_price;
                $response['total_final_price'] = $total_price-$total_discount;
            }else{
                foreach  ($params->items as $item){
                    //$total_discount += ($this->getDiscount($item->product_id,$item->quantity,$params['distributor_id'])*$item->quantity);
                    //$total_loyalty += ($this->getLoyaltyPoints($item->product_id,$item->quantity,$params['distributor_id'])*$item->quantity);
                    $total_price += ($item->price*$item->quantity);
                }
                $response['total_loyalty'] = 0;
                $response['total_discount'] = 0;
                $response['total_price'] = $total_price;
                $response['total_final_price'] = $total_price;
            }

            if($params->customer_id){
                $response['payment_methods'] = $this->getPaymentMethods($params->customer_id);
            }
        } else {
            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }
        echo json_encode($response);
    }

    function getDiscount($product_id,$quantity,$distributor_id){
        $dscnt = $this->products_model->getProductDiscountByProductIDandQuantity($product_id,$quantity,$distributor_id);
        $lyty = $this->products_model->getProductDiscountByProductIDandQuantity($product_id,$quantity,$distributor_id);

        if($dscnt){
            $dscnt=$dscnt->discount;
            $lyty=$lyty->loyalty;
        }else{
            $all_discounts_for_product = $this->products_model->getProductDiscountsByProductIDAndDistributorID($product_id,$distributor_id);

            $last_discount = $all_discounts_for_product[count($all_discounts_for_product)-1];

            $dscnt=$last_discount->discount;
            $lyty=$last_discount->loyalty;
        }

        return $dscnt;
    }
    
    function getLoyaltyPoints($product_id,$quantity,$distributor_id){
        $dscnt = $this->products_model->getProductDiscountByProductIDandQuantity($product_id,$quantity,$distributor_id);
        $lyty = $this->products_model->getProductDiscountByProductIDandQuantity($product_id,$quantity,$distributor_id);

        if($dscnt){
            $dscnt=$dscnt->discount;
            $lyty=$lyty->loyalty;
        }else{
            $all_discounts_for_product = $this->products_model->getProductDiscountsByProductIDAndDistributorID($product_id,$distributor_id);

            $last_discount = $all_discounts_for_product[count($all_discounts_for_product)-1];

            $dscnt=$last_discount->discount;
            $lyty=$last_discount->loyalty;
        }

        return $lyty;
    }

    function checkDiscount($vehicle_id){
        $vehicle =  $this->vehicles_model->getVehicleByID($vehicle_id);
        if($vehicle->discount_enabled=="Enabled"){
            return true;
        }else{
            return false;
        }
    }

    function getPaymentMethods($customer_id){

        $array = $this->companies_model->getCustomerPaymentMethods($customer_id);
        
        foreach($array as $k=>$v) { 
            foreach ($array[$k] as $key=>$value) { 
              if ($key === "name" && $value === "Mpesa Payment") {
        
                  unset($array[$k]); //Delete from Array 
              }
            }  
        }
        
        return $this->companies_model->getCustomerPaymentMethods($customer_id);
    }
    
    function getPaymentMethodsJson($customer_id){

        $array = $this->companies_model->getCustomerPaymentMethods($customer_id);
        
        // foreach($array as $k=>$v) { 
        //     foreach ($array[$k] as $key=>$value) { 
        //       if ($key === "name" && $value === "Invoice Payment") {
        
        //           unset($array[$k]); //Delete from Array 
        //       }
        //     }  
        // }
        
        echo json_encode($array);
    }

    function fillTicket(){
        $this->form_validation->set_rules('customer_id', lang("Customer"), 'required');
        $this->form_validation->set_rules('salesman_id', lang("Salesman"), 'required');
        $this->form_validation->set_rules('reason', lang("Reason"), 'required');
        $this->form_validation->set_rules('shop_id', lang("Shop"), 'required');
        $this->form_validation->set_rules('vehicle_id', lang("Vehicle"), 'required');
        $this->form_validation->set_rules('distributor_id', lang("Distributor"), 'required');
        
        if ($this->form_validation->run() == true) {

            
            $reason = $this->input->post('reason');
            $shop_id = $this->input->post('shop_id');
            $vehicle_id = $this->input->post('vehicle_id');
            $vehicle =  $this->vehicles_model->getVehicleByID($vehicle_id);
            $salesman_id = $this->input->post('salesman_id');
            $salesman_details = $this->companies_model->getCompanyByID($salesman_id);
            $customer_id = $this->input->post('customer_id');
            $distributor_id = $this->input->post('distributor_id');
            $distributor_details = $this->companies_model->getCompanyByID($distributor_id);
            $distributor = $distributor_details->name;
            $customer_details = $this->site->getCustByID($customer_id);
            $customer = $customer_details->name;
            $date = date("Y-m-d");
            $data = array(
                'date' => $date,
                'distributor_id' => $distributor_id,
                'customer_id' => $customer_id,
                'salesman_id' => $salesman_id,
                'vehicle_id' => $vehicle_id,
                'shop_id' => $shop_id,
                'reason' => $reason,
            );
            
            
            $sale_data = array('date' => $date,
                'gmid'=> 'P6',
                'reference_no' => '0',
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'customer' => $customer,
                'customer_id' => $customer_id,
                'salesman_id' => $salesman_id,
                'vehicle_id' => $vehicle_id,
                'shop_id' => $shop_id,
                'country'=> '1',
                'country_id'=> '1',
                'products'=>'POWER REFIL 6KG',
                'product_id' =>'4099',
                'value'=> '1480',
                'total' => $this->sma->formatDecimal(1480),
                'total_discount' => '0',
                'product_tax' => $this->sma->formatDecimal(0),
                'sales_type' =>'SSO',
                'shipping' => $this->sma->formatDecimal(0),
                'grand_total' => '1480',
                'quantity_units' => '1',
                'msr_alignment_id' => $msr_details->sf_alignment_id,
                'msr_alignment_name' =>$msr_details->sf_alignment_name,
                'paid' => 0,
                'created_by' => $salesman_id,
                'payment_status'=> 'paid',
                'signature'=>''
            );

        }
        
        if ($this->form_validation->run() == true) {
            if($ticket_id = $this->sales_model->addTicket($data)){
                $this->sales_model->addTicketSale($sale_data);
                $response= array("success" => "1", "message" => "Ticket added");
            }else{
                $response= array("success" => "0", "message" => "Ticekt not added");
            }
        }else{
            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }
        
        
        echo json_encode($response);
    }
    function receipt($id = NULL, $view = NULL, $save_bufffer = NULL){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        
        $inv = $this->sales_model->getInvoiceByID($id);
        
        $this->data2['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data2['vehicle'] = $this->vehicles_model->getVehicleByID($inv->vehicle_id);
        $this->data2['payments'] = $this->sales_model->getPaymentsForSale($id);
        
        $this->data2['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data2['user'] = $this->site->getUser($inv->created_by);
        
        $this->data2['inv'] = $inv;
        
        $this->data2['rows'] = $this->sales_model->getAllInvoiceItems($id);
        
        
        echo json_encode($this->data2);
        //$this->data['paypal'] = $this->sales_model->getPaypalSettings();
        //$this->data['skrill'] = $this->sales_model->getSkrillSettings();

        //$name = lang("sale") . "_" . $id . ".pdf";
        //$html = $this->load->view($this->theme . 'sales/receipt', $this->data, TRUE);
        /**if ($view) {
            $this->load->view($this->theme . 'sales/receipt', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, FALSE, $this->data['biller']->invoice_footer);
        }**/
    }
    
    function getCurrentSoldItems(){
        $this->form_validation->set_rules('vehicle_id', lang("Vehicle"), 'required');
       $this->form_validation->set_rules('distributor_id', lang("Distributor"), 'required');
       $this->form_validation->set_rules('start_date', lang("Distributor"), 'required');
       $this->form_validation->set_rules('end_date', lang("Distributor"), 'required');
       if ($this->form_validation->run() == true) {
           $vehicle_id = $this->input->post('vehicle_id');
           $distributor_id = $this->input->post('distributor_id');
           $start_date = $this->input->post('start_date');
           $end_date = $this->input->post('end_date');

           $response = $this->vehicles_model->getVehicleSoldStock($vehicle_id, $distributor_id,$start_date,$end_date);

       }else{
           $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
       }
       echo json_encode($response);
    }
    
    function closeStock(){
        $this->form_validation->set_rules('vehicle_id', lang("Vehicle"), 'required');
        $this->form_validation->set_rules('distributor_id', lang("Distributor"), 'required');
        if ($this->form_validation->run() == true) {
            $vehicle_id = $this->input->post('vehicle_id');
            $distributor_id = $this->input->post('distributor_id');

            $current_stock = $this->vehicles_model->getVehicleStock($vehicle_id, $distributor_id);
            //delete where created = today
            if($this->vehicles_model->deleteVehicleClosingStock($vehicle_id, $distributor_id)){
                $data= array();
                foreach($current_stock as $stock){
                    array_push($data,array(
                        'distributor_id'=>$distributor_id,
                        'vehicle_id'=>$vehicle_id,
                        'product_id'=>$stock->product_id,
                        'quantity'=>$stock->product_quantity));
                }
                //bulk add the current stock as closing stock
                $this->vehicles_model->addVehicleClosingStock($data);
                $response= array("success" => "1", "message" => 'Stock closed');
            }
        }else{
            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }
        echo json_encode($response);
    }
    
    function getVehicleData(){
        $response = $this->vehicles_model->getVehicleSalesman();

        echo json_encode($response);
    }
    
    function getStock(){
       $this->form_validation->set_rules('vehicle_id', lang("Vehicle"), 'required');
       $this->form_validation->set_rules('distributor_id', lang("Distributor"), 'required');
       if ($this->form_validation->run() == true) {
           $vehicle_id = $this->input->post('vehicle_id');
           $distributor_id = $this->input->post('distributor_id');

           $response = $this->vehicles_model->getVehicleStock($vehicle_id, $distributor_id);

       }else{
           $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
       }
       echo json_encode($response);
   }

   function postStock(){
        $this->form_validation->set_rules('vehicle_id', lang("Vehicle"), 'required');
        $this->form_validation->set_rules('salesman_id', lang("Salesman"), 'required');
        $this->form_validation->set_rules('distributor_id', lang("Distributor"), 'required');
        $this->form_validation->set_rules('expected_stock', lang('Expected Stock'), 'required');


        if ($this->form_validation->run() == true) {
            $vehicle_id = $this->input->post('vehicle_id');
            $salesman_id = $this->input->post('salesman_id');
            $distributor_id = $this->input->post('distributor_id');

            //get current stock
            $current_stock = $this->vehicles_model->getVehicleStock($vehicle_id,$distributor_id);
            //compare with expected stock
            $expected_stock = json_decode($this->input->post('expected_stock'));
            $differences = array();
            //compare
            //get salesman details to get the bank acc id
            $salesman_details = $this->companies_model->getCompanyByID($salesman_id);
            //Post short to the sales man acc - journal entry with memo for missing items

            if(count($current_stock) == count($expected_stock)){

                $total_short = 0;
                $total_comments = '';

                for($i=0;$i<count($current_stock);$i++){
                    if($expected_stock[$i]->product_quantity < $current_stock[$i]->product_quantity){
                        array_push($differences,array("product_id"=>$expected_stock[$i]->product_id,"product_name"=>$expected_stock[$i]->product_name,"difference"=>($current_stock[$i]->product_quantity-$expected_stock[$i]->product_quantity),"product_price"=>$current_stock[$i]->product_price));
                        $total_comments .=
                            ' Short detected for '.$expected_stock[$i]->product_name.
                            ' current quantity = '.$current_stock[$i]->product_quantity.
                            ' physical quantity = '.$expected_stock[$i]->product_quantity.
                            ' difference = '.($current_stock[$i]->product_quantity-$expected_stock[$i]->product_quantity).
                            ' current price = '.$current_stock[$i]->product_price.
                            ' short = '.$current_stock[$i]->product_price.' x '
                            .($current_stock[$i]->product_quantity-$expected_stock[$i]->product_quantity).
                            ' = '.$current_stock[$i]->product_price
                            *($current_stock[$i]->product_quantity-$expected_stock[$i]->product_quantity).'<br>';

                        $total_short+=($current_stock[$i]->product_quantity-$expected_stock[$i]->product_quantity)*$current_stock[$i]->product_price;
                        
                        $data = array(
                            'quantity' => $expected_stock[$i]->product_quantity
                        );
                        $this->vehicles_model->updateVehicleStock($current_stock[$i]->id,$data);
                    }
                }

                $stock_taking_history = array(
                    'distributor_id' => $distributor_id,
                    'vehicle_id' => $vehicle_id,
                    'salesman_id' => $salesman_id,
                    'stock_taker_id' => null,
                    'total_short' => $total_short,
                    'comments' => $total_comments,
                    'expected_stock' => json_encode($expected_stock),
                    'current_stock' => json_encode($current_stock),
                    'differences' => json_encode($differences)
                );
                $this->vehicles_model->addStockTakingHistory($stock_taking_history);
                if($total_short>0){
                    //send to erp
                    $json = array();

                    $data = array(
                        "id"=>$salesman_details->bank_acc_id
                    );


                    $json[] = $data;
                    $json_data = json_encode($json);
                    $username = "pos-api";
                    $password = "admin";
                    $headers = array(
                        'Authorization: Basic '. base64_encode($username.':'.$password),
                    );

                    //Perform curl post request to add item to the accounts erp
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/bank_account.php?action=get-bank-account-code&company-id=KAMP",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $json_data,
                        CURLOPT_HTTPHEADER => $headers,
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $response_data = json_decode($response);

                    $status = $response_data->status;


                    if ($status == "ok") {
                        $items = array(
                            array(
                                'account_code'=> $response_data->account_code,
                                'amount'=> -$total_short,
                                'memo'=> $total_comments
                            )
                        );
                        $json2=array(
                            'currency'=> 'KS',
                            'source_ref'=>$response_data->account_code,
                            'reference'=> $response_data->account_code,
                            'memo'=> $total_comments,
                            'amount'=> -$total_short,
                            'bank_act'=>$salesman_details->bank_acc_id,
                            'items'=> $items
                        );
                        $json_data2 = json_encode($json2);


                        //Perform curl post request to add gl to the accounts erp
                        $curl2 = curl_init();

                        curl_setopt_array($curl2, array(
                            CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/journal.php?action=add-journal&company-id=KAMP",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $json_data2,
                            CURLOPT_HTTPHEADER => $headers,
                        ));

                        $response2 = curl_exec($curl2);

                        curl_close($curl2);

                        $new_id2 = json_decode($response2)->id;

                        if ($new_id2) {
                            $response= array("success" => "1", "message" => "Success");
                            //$response = array("success" => "1", "message" => "Short detected and added total_short = " . $total_short. " total_comments = ". $total_comments);
                        } else {
                            $response= array("success" => "0", "message" => "Failed to add short in ERP");
                        }
                    } else {
						$response= array("success" => "0", "message" => "Salesperson account not found in ERP");
                    }
                    //$response= array("success" => "1", "message" => "Success", "status" => "total_short = " . $total_short. " total_comments = ". $total_comments);
                }else{
                    $response= array("success" => "1", "message" => "Success");
                }

            }else{
                $response= array("success" => "0", "message" => "Expected stock and current stock dont match");
            }


        }
       echo json_encode($response);
    }
   
    function updateSale(){
        $this->form_validation->set_rules('sale_id', lang("Sale"), 'required');
        $this->form_validation->set_rules('salesman_id', lang("Salesman"), 'required');
        $this->form_validation->set_rules('amount', lang("Amount"), 'required');
        $this->form_validation->set_rules('payments', lang('Payments'), 'required');
        $this->form_validation->set_rules('payment_status', lang("Payment Status"), 'required');

        $id = $this->input->post('sale_id');
        $salesman_id = $this->input->post('salesman_id');
        $salesman_details = $this->companies_model->getCompanyByID($salesman_id);
        //$amount = $this->input->post('amount');

        $salesman_details = $this->companies_model->getCompanyByID($salesman_id);
        $new_payments = json_decode($this->input->post('payments'));
        $inv = $this->sales_model->getInvoiceByID($id);
        $payments = $this->sales_model->getPaymentsForSale($id);

        $paid=0;
        foreach ($payments as $payment){
            if($payment->type=='received'){
                $paid+=$payment->amount;
            }
        }

        $amount = 0;
        
        foreach($new_payments->items as $item){
            if($item->paid_by=="Cash Payment"){
                $amount+=$item->amount;
            }
            if($item->paid_by=="Mpesa Payment"){
                $amount+=$item->amount;
            }
            
        }
        
        $total_paid = $paid+$amount;
        $new_balance = $inv->grand_total - $total_paid;
        
        //$new_balance = $balance - $total_paid;
        
        if($new_balance == 0){
            $data['updated_at']=date("Y-m-d H:i:s");
            $data['payment_status']='paid';
        }else{
            if($new_balance==$inv->grand_total){
                $data['payment_status']='unpaid';
            }else{
                $data['payment_status']='partial';
            }
        }

        //echo json_encode($new_payments);
        //exit;
        if($this->db->update('sales', $data, array('id' => $id))){
            $actual_payment = array();
            foreach($new_payments->items as $item){

                $actual_payment = array(
                    'date' => date("Y-m-d"),
                    'sale_id' => $id,
                    'reference_no' => $this->site->getReference('pay'),
                    'amount' => $this->sma->formatDecimal($item->amount),
                    'paid_by' => $item->paid_by,
                    'cheque_no' => $item->cheque_no,
                    'cc_no' => null,
                    'cc_holder' => null,
                    'cc_month' => null,
                    'cc_year' => null,
                    'cc_type' => null,
                    'created_by' => $salesman_id,
                    'note' => null,
                    'type' => $item->type
                );

                if($this->db->insert('sma_payments', $actual_payment)){
                    
                    foreach ($payments as $payment){
                        if($payment->type=='pending'){
                            $pdata = array();
                            $pdata = array(
                                'type' => "repaid",
                            );
                            $this->sales_model->updatePayment($payment->id, $pdata);
                        }
                    }
                    
                    
                    $json = array();
                    $data2 = array();
                    if($item->paid_by=="Cash Payment"){
                        $data2 = array(
                            'CustId' => $data['customer_id'],
                            'TransactionRef' => $actual_payment['reference_no'],
                            'TransDate' => $actual_payment['date'],
                            'BankAcct' => $salesman_details->bank_acc_id,
                            'Amount' => $actual_payment['amount']);
                        $json[] = $data2;
                        $json_data = json_encode($json);
                        $username = "pos-api";
                        $password = "admin";
                        $headers = array(
                            'Authorization: Basic '. base64_encode($username.':'.$password),
                        );


                        //Perform curl post request to add item to the accounts erp
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/payment.php?action=make-payment&company-id=KAMP",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $json_data,
                            CURLOPT_HTTPHEADER => $headers,
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);

                        $response_data = json_decode($response);
                        // Further processing ...
                        foreach($response_data as $itemObj){
                            $status = $itemObj->Status;
                        }
    
                        if ($status == 'ok') {
                            $response= array("success" => "1", "message" => "Sale updated " . $item);
                        } else {
                            $response= array("success" => "0", "message" => "Sale not updated");
                        }
                    }
                    
                    if($item->paid_by=="Mpesa Payment"){
                        $data2 = array(
                            'CustId' => $data['customer_id'],
                            'TransactionRef' => $actual_payment['reference_no'],
                            'TransDate' => $actual_payment['date'],
                            'BankAcct' => '15',
                            'Amount' => $actual_payment['amount']);
                        $json[] = $data2;
                        $json_data = json_encode($json);
                        $username = "pos-api";
                        $password = "admin";
                        $headers = array(
                            'Authorization: Basic '. base64_encode($username.':'.$password),
                        );

                        //Perform curl post request to add item to the accounts erp
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/payment.php?action=make-payment&company-id=KAMP",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $json_data,
                            CURLOPT_HTTPHEADER => $headers,
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);

                        $response_data = json_decode($response);
                        // Further processing ...
                        foreach($response_data as $itemObj){
                            $status = $itemObj->Status;
                        }
    
                        if ($status == 'ok') {
                            $response= array("success" => "1", "message" => "Sale updated " . $item);
                        } else {
                            $response= array("success" => "0", "message" => "Sale not updated");
                        }
                    }
                    
                    
                }else{
                    $response= array("success" => "0", "message" => "Sale not updated. Failed to add payment"); 
                }

            }
        }else{
            $response= array("success" => "0", "message" => "Sale not updated. Failed to update sale");
        }

        echo json_encode($response);
    }
    
    function  addSale(){
        $this->form_validation->set_rules('customer_id', lang("Customer"), 'required');
        $this->form_validation->set_rules('salesman_id', lang("Salesman"), 'required');
        
        $this->form_validation->set_rules('shop_id', lang("Shop"), 'required');
        $this->form_validation->set_rules('vehicle_id', lang("Vehicle"), 'required');
        $this->form_validation->set_rules('town_id', lang("Town"), 'required');
        $this->form_validation->set_rules('distributor_id', lang("Distributor"), 'required');
        
        $this->form_validation->set_rules('total', lang("Total"), 'required');
        
        $this->form_validation->set_rules('json', lang('JSON'), 'required');
        $this->form_validation->set_rules('discount', lang('Discount'), 'required');
        
        if($this->input->post('discount')==0){
            $this->form_validation->set_rules('payment_status', lang("Payment Status"), 'required');
            $this->form_validation->set_rules('payments', lang('Payments'), 'required');
        }
        
        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no') ?
                $this->input->post('reference_no') :
                $this->site->getReference('so');
            $date = date("Y-m-d");
            $salestype = 'SSO';
            $warehouse_id = 2;
            $payments = json_decode($this->input->post('payments'));
            $shop_id = $this->input->post('shop_id');
            $ttl = $this->input->post('total');
            $vehicle_id = $this->input->post('vehicle_id');
            $signature = $this->input->post('signature');
            $vehicle =  $this->vehicles_model->getVehicleByID($vehicle_id);
            $salesman_id = $this->input->post('salesman_id');
            $salesman_details = $this->companies_model->getCompanyByID($salesman_id);
            $customer_id = $this->input->post('customer_id');
            $distributor_id = $this->input->post('distributor_id');
            $distributor_details = $this->companies_model->getCompanyByID($distributor_id);
            $distributor = $distributor_details->name;
            $town = $this->towns_model->getTownByID($this->input->post('town_id'));
            $country = $town->county_id;
            $county = $this->counties_model->getCountyByID($country);
            $payment_status = $this->input->post('payment_status');
            $image_url = $this->input->post('image') ? $this->input->post('image') : null;
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCustByID($customer_id);
            $customer = $customer_details->name;
            $country_details = $this->sales_model->getCountryByID($country);
            $country_code = $country_details->country;
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $params = json_decode($this->input->post('json'));
            foreach($params->items as $item){
                $item_id = $item->product_id;
                $item_type = "standard";
                $item_code = $item->code;
                $item_name = $item->name;
                $item_option = NULL;
                $real_unit_price = $this->sma->formatDecimal($item->price);
                $unit_price = $this->sma->formatDecimal($item->price);
                $item_quantity = $item->quantity;
                $item_serial = '';
                $item_tax_rate = NULL;
                

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->sales_model->getProductByCode($item_code) : NULL;
                    $unit_price = $real_unit_price;
                    $pr_discount = 0;
                    if ($this->input->post('discount') == 1) {
                        
                            $item_discount = $item->discount/$item_quantity;
                            $product_discount = $item->discount;
                        
                    }else{
                        $item_discount = 0;
                        $product_discount = 0;
                    }

                    //$unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $item_net_price = $unit_price;
                    
                    $pr_tax = 0; $pr_item_tax = 0; $item_tax = 0; $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $pr_tax = $item_tax_rate;
                        $tax_details = $this->site->getTaxRateByID($pr_tax);
                        if ($tax_details->type == 1 && $tax_details->rate != 0) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_price = $unit_price - $item_tax;
                            }

                        } elseif ($tax_details->type == 2) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_price = $unit_price - $item_tax;
                            }

                            $item_tax = $this->sma->formatDecimal($tax_details->rate);
                            $tax = $tax_details->rate;

                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);

                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

                    $total += $item_net_price * $item_quantity;
                }
                $products[] = array(
                    'product_id' => $item_id,
                    'product_code' => $item_code,
                    'product_name' => $item_name,
                    'product_type' => $item_type,
                    'option_id' => $item_option,
                    'net_unit_price' => $item_net_price,
                    'unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax),
                    'quantity' => $item_quantity,
                    'warehouse_id' => $warehouse_id,
                    'item_tax' => $pr_item_tax,
                    'tax_rate_id' => $pr_tax,
                    'tax' => $tax,
                    'discount' => $item_discount,
                    'item_discount' => $product_discount,
                    'subtotal' => $this->sma->formatDecimal($subtotal),
                    'serial_no' => $item_serial,
                    'real_unit_price' => $real_unit_price
                );
                
                $total_item_quantity += $item_quantity;
            }

            if(empty($products)){
                // $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('order_discount')) {
                $order_discount_id = $this->input->post('order_discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = $this->sma->formatDecimal((($total + $product_tax) * (Float)($ods[0])) / 100);
                } else {
                    $order_discount = $this->sma->formatDecimal($order_discount_id);
                }
            } else {
                $order_discount_id = NULL;
            }

            $total_discount = $this->sma->formatDecimal($order_discount + $product_discount);

            if ($this->Settings->tax2) {
                $order_tax_id = $this->input->post('order_tax');
                if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                    if ($order_tax_details->type == 2) {
                        $order_tax = $this->sma->formatDecimal($order_tax_details->rate);
                    }
                    if ($order_tax_details->type == 1) {
                        $order_tax = $this->sma->formatDecimal((($total + $product_tax - $order_discount) * $order_tax_details->rate) / 100);
                    }
                }
            } else {
                $order_tax_id = NULL;
            }

            $total_tax = $this->sma->formatDecimal($product_tax + $order_tax);
            $grand_total = $this->sma->formatDecimal($this->sma->formatDecimal($total) + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount);
            $msr_details = $this->sales_model->msr_customer_alignments($customer_id,$item_id,$country_details->id);
            $data = array('date' => $date,
                'gmid'=> $item_code,
                'reference_no' => $reference,
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'customer' => $customer,
                'customer_id' => $customer_id,
                'salesman_id' => $salesman_id,
                'vehicle_id' => $vehicle_id,
                'shop_id' => $shop_id,
                'country'=> $country_code,
                'country_id'=> $country,
                'products'=>$item_name,
                'product_id' =>$item_id,
                'value'=> $subtotal,
                'total' => $this->sma->formatDecimal($ttl),
                'total_discount' => $total_discount,
                'product_tax' => $this->sma->formatDecimal($product_tax),
                'sales_type' =>$salestype,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $ttl,
                'quantity_units' => $total_item_quantity,
                'msr_alignment_id' => $msr_details->sf_alignment_id,
                'msr_alignment_name' =>$msr_details->sf_alignment_name,
                'paid' => 0,
                'created_by' => $salesman_id,
                'payment_status'=> $payment_status,
                'signature'=>$signature
            );

        
        $datacheque = array('date' => $date,
                'gmid'=> $item_code,
                'reference_no' => $reference,
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'customer' => $customer,
                'customer_id' => $customer_id,
                'salesman_id' => $salesman_id,
                'vehicle_id' => $vehicle_id,
                'shop_id' => $shop_id,
                'country'=> $country_code,
                'country_id'=> $country,
                'products'=>$item_name,
                'product_id' =>$item_id,
                'value'=> $subtotal,
                'total' => $this->sma->formatDecimal($ttl),
                'total_discount' => $total_discount,
                'product_tax' => $this->sma->formatDecimal($product_tax),
                'sales_type' =>$salestype,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $ttl,
                'quantity_units' => $total_item_quantity,
                'msr_alignment_id' => $msr_details->sf_alignment_id,
                'msr_alignment_name' =>$msr_details->sf_alignment_name,
                'paid' => 0,
                'created_by' => $salesman_id,
                'payment_status'=> $payment_status,
                'signature'=>$signature,
                'image_url'=>$image_url
            );

        }
        if ($this->form_validation->run() == true){
            if ($this->input->post('discount') == 0) {
                
            if($this->input->post('invoice') == 1){
            if ($sale_id = $this->sales_model->addInvoice($data, $products, $vehicle_id )) {
                        $this->sales_model->addInvoicePayment($data, $products, $payments, $vehicle_id,$sale_id);
                    $response= array("success" => "12", "message" => "Invoice sale added");
                        
                }else{
                    $response= array("success" => "0", "message" => "Invoice Sale  failed");
                }
               
                
            }
            elseif($this->input->post('cheque') == 1){
            if ($sale_id = $this->sales_model->addCheque($datacheque, $products, $vehicle_id )) {
                        $this->sales_model->addChequePayment($datacheque, $products, $payments, $vehicle_id,$sale_id);
                    $response= array("success" => "17", "message" => "Cheque sale added");
                        
                }else{
                    $response= array("success" => "0", "message" => "Cheque Sale  failed");
                }
                
            }
            else{
                
                if ($sale_id = $this->sales_model->addSale2($data, $products, $payments, $vehicle_id, $salesman_details->bank_acc_id )) {
                    if($sale_id=="duplicate")
                    {
                       $response= array("success" => "0", "message" => "Sale not added"); 
                    }
                    else
                    {
                        if($this->input->post('discount_id')){
                            $this->sales_model->updateDiscount($this->input->post('discount_id'));
                        }
                        if($this->input->post('invoice_id')){
                            $this->sales_model->updateInvoice($this->input->post('invoice_id'));
                        }
                        if($this->input->post('cheque_id')){
                            $this->sales_model->updateCheque($this->input->post('cheque_id'));
                        }
                        $json = array();
            			
            			$data2 = array('InvoiceNo' => $sale_id,
                                    'CustId' => $customer_id,
                                    'RefNo' => $reference,
                                    'comments' => 'some comment',
                                    'OrderDate' => $date,
                                    'DeliverTo' => $customer,
                                    'DeliveryAddress' => $town->city.' Town '.$county->french_name.' County ',
            						'DeliveryCost' => '0',
            						'DeliveryDate' => $date,
            						'InvoiceTotal' => $ttl,
            						'DueDate' => $date,
            						'items' => $products);
            			
            			$json[] = $data2;
                        $json_data = json_encode($json);
                        $username = "pos-api";
                        $password = "admin";
                        $headers = array(
                            'Authorization: Basic '. base64_encode($username.':'.$password),
                        );
            
                        //Perform curl post request to add item to the accounts erp
                        $curl = curl_init();
            
                        curl_setopt_array($curl, array(
            			CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/invoice.php?action=add-invoice&company-id=KAMP",
            			CURLOPT_RETURNTRANSFER => true,
            			CURLOPT_ENCODING => "",
            			CURLOPT_MAXREDIRS => 10,
            			CURLOPT_TIMEOUT => 0,
            			CURLOPT_FOLLOWLOCATION => true,
            			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            			CURLOPT_CUSTOMREQUEST => "POST",
            			CURLOPT_POSTFIELDS => $json_data,
            			CURLOPT_HTTPHEADER => $headers,
            		    ));
            
            		    $response = curl_exec($curl);
            	
            		    curl_close($curl);
                        
                        $response_data = json_decode($response);
                        // Further processing ...
                        foreach($response_data as $itemObj){
                        	$status = $itemObj->Status;
                        }
            
                        if ($status == 'ok') { 
                            $response= array("success" => "1", "message" => "Sale added","data"=>$payments);
                        } else {
                            $response= array("success" => "0", "message" => "Sale not added. Erp fail.");
                        }
                    }
                }else{
                    $response= array("success" => "0", "message" => "Sale not added");
                }
            }
            
            }
            else{
                if ($sale_id = $this->sales_model->addDiscount($data, $products, $vehicle_id )) {
                        
                    $response= array("success" => "1", "message" => "Discount added");
                        
                }else{
                    $response= array("success" => "0", "message" => "Discount not added");
                }
                
            }
        }else{
            $response= array("success" => "0", "message" => (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
        }
        
        
        echo json_encode($response);
        
    }
    
    
    function checkCreditLimit($customer_id){
        //get all unpaid or partial sales for customer
        $sales = $this->sales_model->getUnpaidAndPartialPaidSalesByCustomerId($customer_id);
        
        foreach($sales as $sale){
            $payment = $this->sales_model->getPendingSales($sale->id);
        }
    }
}
