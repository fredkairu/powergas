<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->model('auth_model');
        $this->load->library('ion_auth');
        $this->lang->load('customers', $this->Settings->language);
        $this->lang->load('vehicles', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('companies_model');
        $this->load->model('products_model');
        $this->load->model('vehicles_model');
        $this->load->model('settings_model');
        $this->load->model('counties_model');
        $this->load->model('towns_model');
        $this->load->model('routes_model');
        //$this->load->model('customers');
        $this->allowed_file_size = '4096';
        $this->load->model('settings_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        // $this->allowed_file_size = '1024';
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'distributors');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct('customers/index', $meta, $this->data);
    }
    
    function index2($warehouse_id = NULL)
    {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Tickets')));
        $meta = array('page_title' => lang('Tickets'), 'bc' => $bc);
        $this->page_construct('customers/tickets', $meta, $this->data);
    }
    
    function getTickets()
    {
        //$this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_tickets.id as id,UPPER(sma_customers.name) as customer_name,sma_customers.phone as customer_phone,UPPER(sma_companies.name) as salesperson,UPPER(sma_vehicles.plate_no) as plate_no,UPPER(sma_shops.shop_name) as shop_name,sma_tickets.created_at as created_at,sma_tickets.reason as reason,sma_tickets.status as status")
            ->from("sma_tickets")
            ->join('sma_companies', 'sma_tickets.salesman_id=sma_companies.id', 'left')
            ->join('sma_customers', 'sma_tickets.customer_id=sma_customers.id', 'left')
            ->join('sma_shops', 'sma_tickets.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_tickets.vehicle_id=sma_vehicles.id', 'left')
            ->add_column("Actions", "<center>
            <a class=\"tip\" title='" . $this->lang->line("Handle_Ticket") . "' href='" . site_url('customers/handle_ticket/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-check\"></i></a>
            <a href='#' class='po' title='<b>" . lang("Delete_Ticket") . "</b>' data-content=\"<p>"
             . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete_ticket/$1') . "'>"
             . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
             . "</a>
             </center>", "id")
            ->group_by('sma_tickets.id');
        //->unset_column('id');
        echo $this->datatables->generate();
    }
    
    function handle_ticket($id){
        

        $this->form_validation->set_rules('feedback', $this->lang->line("Feedback"), 'required');


        if ($this->form_validation->run('customers/add_handle_ticket') == true) {
            $data = array(
                'ticket_id' => $id,
                'feedback' => $this->input->post('feedback'),
            );
        } elseif ($this->input->post('add_handle_ticket')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/index2');
        }

        if ($this->form_validation->run() == true && $tid = $this->companies_model->addTicketHandle($data)) {
            $this->session->set_flashdata('message', $this->lang->line("Ticket handled"));
            redirect('customers/index2');
        } else {

            $this->data['id']=$id;
            $this->data['page_title'] = lang('Handle_Ticekt');
            $this->load->view($this->theme.'customers/handle_ticket',$this->data);
        }

    }
    
    function maps($action = NULL)
    {
        //$this->sma->checkPermissions('index',true,'distributors');


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $this->data['salespeople'] = $this->companies_model->getAllSalespeople();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customer_Mapping')));
        $meta = array('page_title' => lang('Customer_Mapping'), 'bc' => $bc);
        $this->page_construct('customers/map', $meta, $this->data);
    }
    
    function customers($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'customers');

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        if($this->Distributor){

            $vehicle = $this->vehicles_model->getVehicleByID($distributor->vehicle_id);
            //get company id from users table
            //get vehicle id from company table using company id
            //get vehicle route id
            //use route to get all shops in the same route
            $this->db
                ->select("sma_customers.id as id,sma_customers.created_at,sma_customers.name,sma_customers.email, sma_customers.phone, sma_currencies.french_name, sma_cities.city, sma_companies.name as sales_person_name,sma_customers.active")
                ->from("sma_customers")
                ->join("sma_cities","sma_cities.id=sma_customers.city","left")
                ->join("sma_currencies","sma_currencies.id=sma_cities.county_id","left")
                ->join("sma_companies","sma_companies.id=sma_customers.salesman_id","left")
                ->where('sma_customers.group_name', 'customer')
                ->where('sma_customers.distributor_id', $distributor->id);
                $query= $this->db->get();
                $result=$query->result();
                $this->data['customers']=$result;
        }else{
            $this->db
                ->select("sma_customers.id as id,sma_customers.created_at,sma_customers.name,sma_customers.email, sma_customers.phone, sma_currencies.french_name, sma_cities.city, sma_companies.name as sales_person_name,sma_customers.active")
                ->from("sma_customers")
                ->join("sma_cities","sma_cities.id=sma_customers.city","left")
                ->join("sma_currencies","sma_currencies.id=sma_cities.county_id","left")
                ->join("sma_companies","sma_companies.id=sma_customers.salesman_id","left")
                ->where('sma_customers.group_name', 'customer')
                ->where('sma_customers.distributor_id', $distributor->id);
                $query= $this->db->get();
                $result=$query->result();
                $this->data['customers']=$result;
        }
       
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customers')));
        $meta = array('page_title' => 'Customers', 'bc' => $bc);
        $this->page_construct('customers/customers', $meta, $this->data);
    }
    function getshops($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'customers');

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        
            //get company id from users table
            //get vehicle id from company table using company id
            //get vehicle route id
            //use route to get all shops in the same route
            $this->db
                ->select("sma_shops.id as id,sma_shops.shop_name as shop,sma_shop_allocations.id as all_id,sma_customers.name as cust")
                ->from("sma_shops")
                ->join("sma_customers","sma_customers.id=sma_shops.customer_id","left")
                ->join("sma_shop_allocations","sma_shop_allocations.shop_id=sma_shops.id","left")
                ->order_by('id','ASC')
                ->group_by('id');
                $query= $this->db->get();
                $result=$query->result();
                //print_r($result);
                $this->data['shops']=$result;
       
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customers')));
        $meta = array('page_title' => 'Shops', 'bc' => $bc);
        $this->page_construct('customers/shops_summary', $meta, $this->data);
    }
    
    function smscode($action = null)
    {
        $this->sma->checkPermissions('index',true,'customers');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customers')));
        $meta = array('page_title' => 'Customers', 'bc' => $bc);
        $this->page_construct('customers/sms_code', $meta, $this->data);
    }
    
    function customers2($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'salespeople');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Sales_People')));
        $meta = array('page_title' => lang('Sales_People'), 'bc' => $bc);
        $this->page_construct('customers/customers2', $meta, $this->data);
    }
    function customersByCounty($id)
    {
        $this->sma->checkPermissions('index',true,'salespeople');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $this->data['id'] = $id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customers')));
        $meta = array('page_title' => lang('Customers_By_County'), 'bc' => $bc);
        $this->page_construct('customers/customers_by_county', $meta, $this->data);
    }
    
    function employee($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Employee')));
        $meta = array('page_title' => lang('Employee'), 'bc' => $bc);
        $this->page_construct('customers/employee', $meta, $this->data);
    }
    
    function alignments($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('alignments')));
        $meta = array('page_title' => lang('alignments'), 'bc' => $bc);
        $this->page_construct('customers/alignments', $meta, $this->data);
    }
    


    function customer_alignments($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct('customers/customer_alignments', $meta, $this->data);
    }
    
    
    
    function st_alignments($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct('customers/st_alignments', $meta, $this->data);
    }
    
    
    
    function sub_customers($id)
    {
        $this->sma->checkPermissions();
       if(!$id){
        $id=$this->input->get('id');     
         }
        $this->data['company']=$this->companies_model->getCompanyByID($id);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('sub_customers')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct('customers/subcustomers', $meta, $this->data);
    }
    
    
    
    function customer_description($id)
    {
        $this->sma->checkPermissions();
       if(!$id){
        $id=$this->input->get('id');     
         }
        $this->data['company']=$this->companies_model->getCompanyByID($id);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customer description')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct('customers/customer_description', $meta, $this->data);
    }

    function getCustomers()
    {
        $this->sma->checkPermissions('index',true,'distributors');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_companies.id as id,sma_companies.name,sma_companies.email,sma_companies.phone")
            ->from("sma_companies")
            ->where('sma_companies.group_name', 'distributor')
//            ->where('is_subsidiary',0)
            ->add_column("Actions", "<center>
<a class=\"tip\" title='" . $this->lang->line("edit_customer") . "' href='" . site_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> 
<a class=\"tip\" title='" . $this->lang->line("Add_Target") . "' href='" . site_url('customers/add_target/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-line-chart\"></i></a> 
<a class=\"tip\" title='" . $this->lang->line("View_Target") . "' href='" . site_url('customers/view_targets/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-bullseye\"></i></a> 
 <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        //<a class=\"tip\" title='" . $this->lang->line("view_subsidiaries") . "' href='" . site_url('customers/sub_customers/$1') . "' ><i class=\"fa fa-search\"></i></a>
        echo $this->datatables->generate();
    }
    
    function getSmsCodes()
    {
        $this->sma->checkPermissions('index',true,'distributors');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_verify_code.id as id,sma_customers.name as name,sma_customers.phone as phone,sma_users.username,sma_verify_code.expiry,sma_verify_code.token")
            ->from("sma_verify_code")
            ->join("sma_users","sma_users.id=sma_verify_code.user_id","left")
            ->join("sma_customers","sma_customers.id=sma_verify_code.customer_id","left")
            //->where('sma_companies.group_name', 'salesperson')
//            ->where('is_subsidiary',0)
            ->add_column("Actions", "<center>
 <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_code") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customer/delete_smscode/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        echo $this->datatables->generate();
    }
    
    function getAlignments()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id,alignment_name,alignment_rep,region,country,period")
            ->from("sma_alignments")
            ->add_column("Actions", "<center><a class=\"tip\" title='". $this->lang->line("edit_customer1") . "' href='" . site_url('customers/edit_alignment/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . $this->lang->line("delete_customer1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();
    }
    
    function getEmployee()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_employee.id as id,sma_employee.name,sma_employee.group_name,alignment_name,sma_currencies.country,phone, city")
            ->from("sma_employee")
            ->join("sma_currencies","sma_currencies.id=sma_employee.country","left")
            //->where('group_name', 'employee')
            //->where('is_subsidiary',0)
            ->add_column("Actions", "<center> <a class=\"tip\" title='" . $this->lang->line("edit_Employee") . "' href='" . site_url('customers/edit_employee/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("Delete_Employee") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete_employee/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();
    }
    
    function getCustomers1()
    {
        $this->sma->checkPermissions('index',true,'customers');
        $this->load->library('datatables');
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        if($this->Distributor){

            $vehicle = $this->vehicles_model->getVehicleByID($distributor->vehicle_id);
            //get company id from users table
            //get vehicle id from company table using company id
            //get vehicle route id
            //use route to get all shops in the same route
            $this->datatables
                ->select("sma_customers.id as id,sma_customers.created_at,sma_customers.name,sma_customers.email, sma_customers.phone, sma_currencies.french_name, sma_cities.city, sma_companies.name as sales_person_name,sma_customers.active")
                ->from("sma_customers")
                ->join("sma_cities","sma_cities.id=sma_customers.city","left")
                ->join("sma_currencies","sma_currencies.id=sma_cities.county_id","left")
                ->join("sma_companies","sma_companies.id=sma_customers.salesman_id","left")
                ->where('sma_customers.group_name', 'customer')
                ->where('sma_customers.distributor_id', $distributor->id)
                ->add_column("Actions", "<center>

<a class=\"tip\" title='" . $this->lang->line("add_shop") . "' href='" . site_url('customers/add_shop/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-building\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_shops") . "' href='" . site_url('customers/view_shops/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("Add_Credit_Limit") . "' href='" . site_url('customers/add_limit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("View_Credit_Limit") . "' href='" . site_url('customers/view_limit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-credit-card\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("add_payment_method") . "' href='" . site_url('customers/add_payment_method/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_payment_methods") . "' href='" . site_url('customers/view_payment_methods/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list-alt\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("edit_customer1") . "' href='" . site_url('customers/edit1/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("activate_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-success po-active' href='" . site_url('customers/activate_customer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>
</center>", "id");
        }else{
            $this->datatables
                ->select("sma_customers.id as id,sma_customers.created_at,sma_customers.name,sma_customers.email, sma_customers.phone, sma_currencies.french_name, sma_cities.city, sma_companies.name as sales_person_name,sma_customers.active")
                ->from("sma_customers")
                ->join("sma_cities","sma_cities.id=sma_customers.city","left")
                ->join("sma_currencies","sma_currencies.id=sma_cities.county_id","left")
                ->join("sma_companies","sma_companies.id=sma_customers.salesman_id","left")
                ->where('sma_customers.group_name', 'customer')
                ->where('sma_customers.distributor_id', $distributor->id)
                ->add_column("Actions", "<center>

<a class=\"tip\" title='" . $this->lang->line("add_shop") . "' href='" . site_url('customers/add_shop/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-building\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_shops") . "' href='" . site_url('customers/view_shops/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("add_payment_method") . "' href='" . site_url('customers/add_payment_method/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_payment_methods") . "' href='" . site_url('customers/view_payment_methods/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list-alt\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("edit_customer1") . "' href='" . site_url('customers/edit1/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("Activate_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-success po-active' href='" . site_url('customers/activate_customer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>
</center>", "id");
        }
        echo $this->datatables->generate();
    }
    
    function getCustomers2()
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
        $this->sma->checkPermissions('index',true,'salespeople');
        $this->load->library('datatables');
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->datatables
            ->select("sma_companies.id as id,sma_companies.name,sma_companies.email,sma_companies.phone,sma_vehicles.plate_no,sma_routes.name as route_name,sma_companies.status as status")
            ->from("sma_companies")
            ->join("sma_vehicles","sma_vehicles.id=sma_companies.vehicle_id","left")
            ->join("sma_vehicle_route","sma_vehicle_route.vehicle_id=sma_vehicles.id","left")
            ->join("sma_routes","sma_routes.id=sma_vehicle_route.route_id","left")
            ->where('group_name', 'sales_person')
            ->where('sma_vehicle_route.distributor_id',$distributor->id)
            ->where('sma_vehicle_route.day',$day)
            ->add_column("Actions", "<center>
<a class=\"tip\" title='" . $this->lang->line("Add_Target") . "' href='" . site_url('customers/add_salesman_target/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-line-chart\"></i></a> 
<a class=\"tip\" title='" . $this->lang->line("View_Target") . "' href='" . site_url('customers/view_salesman_targets/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-bullseye\"></i></a> 
<a class=\"tip\" title='" . $this->lang->line("edit_sales_person") . "' href='" . site_url('customers/edit2/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("activate_sales_person") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-success po-delete' href='" . site_url('customers/activate_salesperson/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("deactivate_sales_person") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/deactivate_salesperson/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-close\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_sales_person") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        //<a class=\"tip\" title='" . $this->lang->line("view_subsidiaries") . "' href='" . site_url('customers/sub_customers/$1') . "' ><i class=\"fa fa-search\"></i></a>
        echo $this->datatables->generate();
    }

    function getCustomersByCounty($id)
    {
        $this->sma->checkPermissions('index',true,'customers');
        $this->load->library('datatables');
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        if($this->Distributor){

            $vehicle = $this->vehicles_model->getVehicleByID($distributor->vehicle_id);
            //get company id from users table
            //get vehicle id from company table using company id
            //get vehicle route id
            //use route to get all shops in the same route
            $this->datatables
                ->select("sma_customers.id as id,sma_customers.name,sma_customers.phone, sma_currencies.french_name, sma_companies.name as sales_person_name,sma_customers.active")
                ->from("sma_customers")
                ->join("sma_cities","sma_cities.id=sma_customers.city","left")
                ->join("sma_currencies","sma_currencies.id=sma_cities.county_id","left")
                ->join("sma_companies","sma_companies.id=sma_customers.salesman_id","left")
                ->where('sma_customers.group_name', 'customer')
                ->where('sma_currencies.id', $id)
                ->where('sma_customers.distributor_id', $distributor->id)
                ->add_column("Actions", "<center>

<a class=\"tip\" title='" . $this->lang->line("add_shop") . "' href='" . site_url('customers/add_shop/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-building\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_shops") . "' href='" . site_url('customers/view_shops/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("Add_Credit_Limit") . "' href='" . site_url('customers/add_limit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("View_Credit_Limit") . "' href='" . site_url('customers/view_limit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-credit-card\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("add_payment_method") . "' href='" . site_url('customers/add_payment_method/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_payment_methods") . "' href='" . site_url('customers/view_payment_methods/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list-alt\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("edit_customer1") . "' href='" . site_url('customers/edit1/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("activate_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-success po-active' href='" . site_url('customers/activate_customer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>
</center>", "id");
        }else{
            $this->datatables
                ->select("sma_customers.id as id,sma_customers.name,sma_customers.phone, sma_currencies.french_name, sma_companies.name as sales_person_name,sma_customers.active")
                ->from("sma_customers")
                ->join("sma_cities","sma_cities.id=sma_customers.city","left")
                ->join("sma_currencies","sma_currencies.id=sma_cities.county_id","left")
                ->join("sma_companies","sma_companies.id=sma_customers.salesman_id","left")
                ->where('sma_customers.group_name', 'customer')
                ->where('sma_currencies.id', $id)
                //->where('sma_customers.distributor_id', $distributor->id)
                ->add_column("Actions", "<center>

<a class=\"tip\" title='" . $this->lang->line("add_shop") . "' href='" . site_url('customers/add_shop/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-building\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_shops") . "' href='" . site_url('customers/view_shops/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("add_payment_method") . "' href='" . site_url('customers/add_payment_method/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("view_payment_methods") . "' href='" . site_url('customers/view_payment_methods/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list-alt\"></i></a>
<a class=\"tip\" title='" . $this->lang->line("edit_customer1") . "' href='" . site_url('customers/edit1/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("Activate_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-success po-active' href='" . site_url('customers/activate_customer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i></a>
<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>
</center>", "id");
        }
        echo $this->datatables->generate();
    }
    
    function import_customeralign($id){
                   $distributorcustomer=array();
          $distributorcustomer = $this->companies_model->getADistributorCustomer($id);
        
        $customer=$this->companies_model->getCustomerByID($id);
        $countries=$this->settings_model->getAllCurrencies();
        
        $this->data['countries']=  $countries;
         $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
        $this->data['cutomer_id']=$customer->id;
        $this->data['customer_name']=$customer->name;
        $this->data['distributor_customer']=$distributorcustomer;
        $this->data['page_title'] = lang('import_customer_descriptions');
        $this->load->view($this->theme.'customers/import_description',$this->data);
        
    }

    function add_target($id){
        $this->sma->checkPermissions('add-targets',true,'distributors');

        $this->form_validation->set_rules('product_id', $this->lang->line("Product"), 'required');
        $this->form_validation->set_rules('target', $this->lang->line("Target"), 'required');

        $products = $this->products_model->getAllProducts();

        $distributor = $this->companies_model->getCompanyByID($id);

        if ($this->form_validation->run('customers/add_target') == true) {
            $data = array(
                'distributor_id' => $id,
                'product_id' => $this->input->post('product_id'),
                'target' => $this->input->post('target'),
                'month' => $this->input->post('month'),
            );
        } elseif ($this->input->post('add_target')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addDistributorTarget($data)) {
            $this->session->set_flashdata('message', $this->lang->line("Target added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['products']=  $products;
            $this->data['distributor']=  $distributor;
            $this->data['page_title'] = lang('add_target');
            $this->load->view($this->theme.'customers/add_target',$this->data);
        }

    }

    function targets($id){
        $this->sma->checkPermissions('index-targets',true,'distributors');


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Targets')));
        $meta = array('page_title' => lang('Targets'), 'bc' => $bc);
        $this->page_construct('targets/index', $meta, $this->data);
    }
    
    function getTargets() {
        $this->sma->checkPermissions('index-targets',true,'distributors');

        $current_month = date("F");
        if($current_month=="January"){
            $month = 1;
        }else if($current_month=="February"){
            $month = 2;
        }else if($current_month=="March"){
            $month = 3;
        }else if($current_month=="April"){
            $month = 4;
        }else if($current_month=="May"){
            $month = 5;
        }else if($current_month=="June"){
            $month = 6;
        }else if($current_month=="July"){
            $month = 7;
        }else if($current_month=="August"){
            $month = 8;
        }else if($current_month=="September"){
            $month = 9;
        }else if($current_month=="October"){
            $month = 10;
        }else if($current_month=="November"){
            $month = 11;
        }else if($current_month=="December"){
            $month = 12;
        }
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->load->library('datatables');

        $this->datatables
        ->select("sma_distributor_targets.id AS id,sma_products.name,sma_distributor_targets.target")
            ->from("sma_distributor_targets")
            ->where('sma_distributor_targets.distributor_id',$distributor->id)
            ->where('sma_distributor_targets.month',$month)
            ->join("sma_products","sma_distributor_targets.product_id=sma_products.id","left")
            ->group_by('sma_distributor_targets.id')

        // if (!$this->Owner && !$this->Admin) {
        //   $this->datatables->where('created_by', $this->session->userdata('user_id'));
        // }
        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
        ->add_column("Month", $current_month);
       
        echo $this->datatables->generate();
    }
    
    function view_targets($id){
        $this->sma->checkPermissions('index-targets',true,'distributors');


        $distributortargets = $this->companies_model->getDistributorTargets($id);
        $distributor = $this->companies_model->getCompanyByID($id);
        $this->data['distributor']=  $distributor;
        $this->data['distributortargets']=$distributortargets;
        $this->data['page_title'] = lang('view_targets');
        $this->load->view($this->theme.'customers/view_targets',$this->data);
    }

    function edit_target($id){
        $this->sma->checkPermissions('edit-targets',true,'distributors');

        $this->form_validation->set_rules('product_id', $this->lang->line("Product"), 'required');
        $this->form_validation->set_rules('target', $this->lang->line("Target"), 'required');

        $products = $this->products_model->getAllProducts();

        $target = $this->companies_model->getDistributorsTargetByID($id);

        $distributor = $this->companies_model->getCompanyByID($target->distributor_id);

        if ($this->form_validation->run('customers/edit_target') == true) {
            $data = array(
                'distributor_id' => $distributor->id,
                'product_id' => $this->input->post('product_id'),
                'target' => $this->input->post('target'),
                'month' => $this->input->post('month'),
            );
        } elseif ($this->input->post('edit_target')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->updateDistributorTarget($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("Target updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['products']=  $products;
            $this->data['distributor']=  $distributor;
            $this->data['target']=  $target;
            $this->data['page_title'] = lang('edit_target');
            $this->load->view($this->theme.'customers/edit_target',$this->data);
        }

    }

    function add_salesman_target($id){
        $this->sma->checkPermissions('add-targets',true,'salespeople');

        $this->form_validation->set_rules('product_id', $this->lang->line("Product"), 'required');
        $this->form_validation->set_rules('target', $this->lang->line("Target"), 'required');

        $products = $this->products_model->getAllProducts();

        $sales_person = $this->companies_model->getCompanyByID($id);

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

        if ($this->form_validation->run('customers/add_salesman_target') == true) {
            $data = array(
                'distributor_id' => $distributor->id,
                'salesman_id' => $id,
                'product_id' => $this->input->post('product_id'),
                'target' => $this->input->post('target'),
            );
        } elseif ($this->input->post('add_target')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addSalesManTarget($data)) {
            $this->session->set_flashdata('message', $this->lang->line("Target added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['products']=  $products;
            $this->data['sales_person']=  $sales_person;
            $this->data['distributor']=  $distributor;
            $this->data['page_title'] = lang('add_target');
            $this->load->view($this->theme.'customers/add_salesman_target',$this->data);
        }

    }

    function view_salesman_targets($id){
        $this->sma->checkPermissions('index-targets',true,'salespeople');

        $sales_person = $this->companies_model->getCompanyByID($id);
        $salesmantargets = $this->companies_model->getSalesmanTargets($id);
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->data['distributor']=  $distributor;
        $this->data['sales_person']=  $sales_person;
        $this->data['salesmantargets']=$salesmantargets;
        $this->data['page_title'] = lang('view_targets');
        $this->load->view($this->theme.'customers/view_salesman_targets',$this->data);
    }

    function edit_salesman_target($id){
        $this->sma->checkPermissions('edit-targets',true,'salespeople');

        $this->form_validation->set_rules('product_id', $this->lang->line("Product"), 'required');
        $this->form_validation->set_rules('target', $this->lang->line("Target"), 'required');

        $products = $this->products_model->getAllProducts();

        $target = $this->companies_model->getSalesManTargetByID($id);

        $sales_person = $this->companies_model->getCompanyByID($target->salesman_id);

        $distributor = $this->companies_model->getCompanyByID($target->distributor_id);

        if ($this->form_validation->run('customers/edit_target') == true) {
            $data = array(
                'distributor_id' => $distributor->id,
                'salesman_id' => $sales_person->id,
                'product_id' => $this->input->post('product_id'),
                'target' => $this->input->post('target'),
            );
        } elseif ($this->input->post('edit_target')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->updateSalesManTarget($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("Target updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['products']=  $products;
            $this->data['distributor']=  $distributor;
            $this->data['target']=  $target;
            $this->data['sales_person']=  $sales_person;
            $this->data['page_title'] = lang('edit_target');
            $this->load->view($this->theme.'customers/edit_salesman_target',$this->data);
        }

    }

    function add_shop($id){
        //$this->sma->checkPermissions('add-shops',true,'customers');

        $this->form_validation->set_rules('shop_name', $this->lang->line("shop_name"), 'required');
        $this->form_validation->set_rules('lat', $this->lang->line("latitude"), 'required');
        $this->form_validation->set_rules('lng', $this->lang->line("longitude"), 'required');

        $distributorcustomer=array();
        $distributorcustomer = $this->companies_model->getADistributorCustomer($id);

        $customer=$this->companies_model->getCustomerByID($id);
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $countries=$this->settings_model->getAllCurrencies();

        $vehicle = $this->vehicles_model->getVehicleByID($distributor->vehicle_id);
        if ($this->form_validation->run('customers/add_shop') == true) {
            $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $data = array(
                'customer_id' => $id,
                'route_id' => $vehicle->route_id,
                'shop_name' => $this->input->post('shop_name'),
                'lat' => $this->input->post('lat'),
                'lng' => $this->input->post('lng'),
            );
        } elseif ($this->input->post('add_shop')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addShop($data)) {
            $this->session->set_flashdata('message', $this->lang->line("shop_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['countries']=  $countries;
            $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['customer_id']=$customer->id;
            $this->data['customer_name']=$customer->name;
            $this->data['distributor_customer']=$distributorcustomer;
            $this->data['page_title'] = lang('add_shop');
            $this->load->view($this->theme.'customers/add_shop',$this->data);
        }

    }

    function view_shops($id){
        $this->sma->checkPermissions('index',true,'customers');

        
        $distributorcustomershops = $this->companies_model->getCustomerShops($id);

        $customer=$this->companies_model->getCustomerByID($id);
        $countries=$this->settings_model->getAllCurrencies();

        
        $this->data['countries']=  $countries;
        $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
        $this->data['customer_id']=$customer->id;
        $this->data['customer_name']=$customer->name;
        $this->data['distributor_customer_shops']=$distributorcustomershops;
        $this->data['page_title'] = lang('view_shops');
        $this->load->view($this->theme.'customers/view_shops',$this->data);
    }

    function add_limit($id){
        $this->sma->checkPermissions('add-credit-limit',true,'customers');

        $this->form_validation->set_rules('cash_limit', $this->lang->line("cash_limit"), 'required');

        $customer=$this->companies_model->getCustomerByID($id);

        if ($this->form_validation->run('customers/add_limit') == true) {
            $data = array(
                'customer_id' => $id,
                'cash_limit' => abs($this->input->post('cash_limit')),
            );
        } elseif ($this->input->post('add_limit')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addLimit($data,$id)) {
            $this->session->set_flashdata('message', $this->lang->line("credit_limit_added"));
            redirect('customers/customers');
        } else {

            $this->data['customer']=$customer;
            $this->data['page_title'] = lang('add_credit_limit');
            $this->load->view($this->theme.'customers/add_credit_limit',$this->data);
        }

    }

    function edit_limit($id){
        $this->sma->checkPermissions('edit-credit-limit',true,'customers');

        $this->form_validation->set_rules('cash_limit', $this->lang->line("cash_limit"), 'required');


        $limit=$this->companies_model->getLimit($id);
        $customer=$this->companies_model->getCustomerByID($limit->customer_id);

        if ($this->form_validation->run('customers/edit_limit') == true) {
            $data = array(
                'customer_id' => $limit->customer_id,
                'cash_limit' => abs($this->input->post('cash_limit')),
            );
        } elseif ($this->input->post('edit_limit')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->editLimit($data,$id)) {
            $this->session->set_flashdata('message', $this->lang->line("credit_limit_edited"));
            redirect('customers/customers');
        } else {

            $this->data['limit']=$limit;
            $this->data['customer']=$customer;
            $this->data['page_title'] = lang('edit_credit_limit');
            $this->load->view($this->theme.'customers/edit_limit',$this->data);
        }

    }

    function view_limit($id){
        $this->sma->checkPermissions('index',true,'customers');


        $limits = $this->companies_model->getCustomerLimit($id);

        $customer=$this->companies_model->getCustomerByID($id);


        $this->data['customer']=$customer;
        $this->data['customer_id']=$customer->id;
        $this->data['customer_name']=$customer->name;
        $this->data['limits']=$limits;
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['page_title'] = lang('view_credit_limit');
        $this->load->view($this->theme.'customers/view_limit',$this->data);
    }

    function delete_limit($id = NULL)
    {
        $this->sma->checkPermissions('delete-credit-limit',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteLimit($id)) {

            $this->session->set_flashdata('message', lang('credit_limit_deleted'));
            redirect('customers/customers');
        } else {
            $this->session->set_flashdata('warning', lang('credit_limit_not_deleted'));
            redirect('customers/customers');
            }

    }

    function add_allocation($id){
        //$this->sma->checkPermissions('add-shops',true,'customers');

        $this->form_validation->set_rules('route_id', $this->lang->line("Route"), 'required');

        $routes = $this->routes_model->getAllRoutes();

        $shop = $this->companies_model->getShopById($id);

        if ($this->form_validation->run('customers/add_allocation') == true) {
            $data = array(
                'shop_id' => $id,
                'route_id' => $this->input->post('route_id'),
            );
        } elseif ($this->input->post('add_allocation')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addAllocation($data)) {
            $this->session->set_flashdata('message', $this->lang->line("allocation_added"));
            redirect('customers/customers');
        } else {
            $this->data['routes'] =  $routes;
            $this->data['shop'] =  $shop;
            $this->data['page_title'] = lang('add_allocation');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'customers/add_allocation',$this->data);
        }

    }
    
    function edit_allocation($id){
        //$this->sma->checkPermissions('add-shops',true,'customers');

        $this->form_validation->set_rules('route_id', $this->lang->line("Route"), 'required');

        $allocation = $this->companies_model->getAllocationById($id);

        $routes = $this->routes_model->getAllRoutes();

        if ($this->form_validation->run('customers/edit_allocation') == true) {
            $data = array(
                'route_id' => $this->input->post('route_id'),
            );
        } elseif ($this->input->post('edit_allocation')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->updateAllocation($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("allocation_updated"));
            redirect('customers/customers');
        } else {
            $this->data['routes'] =  $routes;
            $this->data['allocation'] = $allocation;
            $this->data['page_title'] = lang('edit_allocation');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'customers/edit_allocation',$this->data);
        }

    }
    
    function view_allocations($id){
        //$this->sma->checkPermissions('index',true,'customers');

        $allocations = $this->companies_model->getShopAllocations($id);
        $shop = $this->companies_model->getShopById($id);

        $this->data['allocations']=$allocations;
        $this->data['shop'] =  $shop;
        $this->data['page_title'] = lang('view_allocations');
        $this->load->view($this->theme.'customers/view_allocations',$this->data);
    }
    
    function delete_allocation($id = NULL)
    {
        //$this->sma->checkPermissions('delete-shops',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteAllocation($id)) {

            $this->session->set_flashdata('message', lang('allocation_deleted'));
            redirect('customers/customers');
        } else {
            $this->session->set_flashdata('warning', lang('allocation_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

    }
    
    function add_day($id){
        //$this->sma->checkPermissions('add-shops',true,'customers');

        $this->form_validation->set_rules('day', $this->lang->line("Day"), 'required');
        //$this->form_validation->set_rules('expiry', $this->lang->line("Expiry"), 'required');

        $allocation = $this->companies_model->getAllocationById($id);

        if ($this->form_validation->run('customers/add_day') == true) {
            $data = array(
                'allocation_id' => $id,
                'day' => $this->input->post('day'),
                'expiry' => $this->input->post('expiry') ? $this->input->post('expiry') : null,
            );
        } elseif ($this->input->post('add_day')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addAllocationDay($data)) {
           /***  $vehicle = $this->vehicles_model->getVehicleByRouteID($allocation->route_id,$data['day']);
                if(!empty($vehicle)){
                $companies = $this->vehicles_model->getSalesmanID($vehicle->vehicle_id);
                $response = $this->routes_model->getVroomRoutes($companies->vehicle_id,$data['day'],$companies->id);
                $vehicleroutes=json_decode($response,true);
                foreach($vehicleroutes as $vehicleroute)
        {
            if(isset($vehicleroute['id']))
            {
            $datar = array(
                'duration' => $vehicleroute['duration'],
                'distance' => 0.00,
            );
            $update=$this->routes_model->updateDuration($vehicleroute['id'], $datar);
            //echo $datar['duration'];
            

        }
        }
                
            }
                if($update==FALSE)
                { **/
           // $this->session->set_flashdata('message', $this->lang->line("allocation_day_added but route plan not updated"));
                //}else{
                    $this->session->set_flashdata('message', $this->lang->line("allocation_day_added")); 
                //}
                //print_r($vehicle);
            redirect('customers/customers');

        } else {
            $this->data['allocation'] = $allocation;
            $this->data['page_title'] = lang('add_day');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'customers/add_day',$this->data);
        }

    }
    
    function edit_day($id){
        //$this->sma->checkPermissions('add-shops',true,'customers');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->form_validation->set_rules('day', $this->lang->line("Day"), 'required');
        //$this->form_validation->set_rules('expiry', $this->lang->line("Expiry"), 'required');

        $allocation_day = $this->companies_model->getAllocationDay($id);

        if ($this->form_validation->run('customers/add_day') == true) {
            $data = array(
                'day' => $this->input->post('day'),
                'expiry' => $this->input->post('expiry') ? $this->input->post('expiry') : null,
            );
        } elseif ($this->input->post('add_day')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->updateAllocationDay($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("allocation_day_updated"));
            redirect('customers/customers');
        } else {
            $this->data['allocation_day'] = $allocation_day;
            $this->data['page_title'] = lang('edit_day');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'customers/edit_day',$this->data);
        }

    }
    
    function delete_day($id = NULL)
    {
        //$this->sma->checkPermissions('delete-shops',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteAllocationDay($id)) {

            $this->session->set_flashdata('message', lang('allocation_day_deleted'));
            redirect('customers/customers');
        } else {
            $this->session->set_flashdata('warning', lang('allocation_day_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

    }
    
    function view_days($id){
        //$this->sma->checkPermissions('index',true,'customers');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $allocation_days = $this->companies_model->getAllocationDays($id);
        $allocation = $this->companies_model->getAllocationById($id);

        $this->data['allocation_days']=$allocation_days;
        $this->data['allocation'] = $allocation;
        $this->data['page_title'] = lang('view_days');
        $this->load->view($this->theme.'customers/view_days',$this->data);
    }
    
    function getCustomersShops($id=NULL,$route_id=NULL){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
            $company_details = $this->companies_model->getCompanyByID($id);
        }
        if ($this->input->get('route_id')) {
            $route_id = $this->input->get('route_id');
        }

        $customersshops = $this->companies_model->getCustomersShops($id,$route_id);
        $response['data']=$customersshops;
        $response['town_id']=$company_details->city;
        echo  json_encode($customersshops);

    }

    function getSalespersonsCustomers($id=NULL){

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
            $salesperson_details = $this->companies_model->get_user_data($id);
        }

        $customers = $this->companies_model->getSalespersonsCustomers($id,$salesperson_details->route_id);

        $response['data']=$customers;
        $response['route_id']=$salesperson_details->route_id;
        echo  json_encode($response);

    }
    
    function getSalespersonsVehicles($id=NULL){

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $vehicle = $this->vehicles_model->getSalesmansVehicle($id);

        $response['data']=$vehicle;
        echo  json_encode($response);

    }
    
    function edit_shop($id){
        $this->sma->checkPermissions('edit-shops',true,'customers');

        $this->form_validation->set_rules('shop_name', $this->lang->line("shop_name"), 'required');
        $this->form_validation->set_rules('lat', $this->lang->line("latitude"), 'required');
        $this->form_validation->set_rules('lng', $this->lang->line("longitude"), 'required');

        $distributorcustomershop=array();
        $distributorcustomershop = $this->companies_model->getCustomerShop($id);
        $customer=$this->companies_model->getCustomerByID($distributorcustomershop[0]->customer_id);
        if ($this->form_validation->run('customers/edit_shop') == true) {
            
            $data = array(
                'customer_id' => $distributorcustomershop[0]->customer_id,
                'shop_name' => $this->input->post('shop_name'),
                'lat' => $this->input->post('lat'),
                'lng' => $this->input->post('lng'),
            );
        } elseif ($this->input->post('edit_shop')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->updateCustomerShop($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("shop_edited"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['countries']=  $countries;
            $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['customer_id']=$customer->id;
            $this->data['customer_name']=$customer->name;
            $this->data['distributor_customer_shop']=$distributorcustomershop;
            $this->data['page_title'] = lang('edit_shop');
            $this->load->view($this->theme.'customers/edit_shop',$this->data);
        }

    }
    
    function delete_shop($id = NULL)
    {
        $this->sma->checkPermissions('delete-shops',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteCustomerShop($id)) {
            
            $this->session->set_flashdata('message', lang('shop_deleted'));
            redirect('customers/customers');
        } else {
            $this->session->set_flashdata('warning', lang('shop_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

    }

    function delete_target($id = NULL)
    {
        $this->sma->checkPermissions('delete-targets',true,'distributors');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteDistributorTarget($id)) {

            $this->session->set_flashdata('message', lang('Target deleted'));
            redirect('customers');
        } else {
            $this->session->set_flashdata('warning', lang('Target not deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

    }
    
    function add_payment_method($id){
        $this->sma->checkPermissions('add-pm',true,'customers');

        $this->form_validation->set_rules('payment_method_id', $this->lang->line("Payment Method"), 'required');

        $distributorcustomer=array();
        $distributorcustomer = $this->companies_model->getADistributorCustomer($id);

        $customer=$this->companies_model->getCustomerByID($id);
        $countries=$this->settings_model->getAllCurrencies();

        if ($this->form_validation->run('customers/add_payment_method') == true) {
            $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $data = array(
                'customer_id' => $id,
                'payment_method_id' => $this->input->post('payment_method_id'),
            );
        } elseif ($this->input->post('add_payment_method')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true) {
            if($this->companies_model->checkCustomerPaymentMethodExists($id,
                $this->input->post('payment_method_id'))){
            $this->session->set_flashdata('message', $this->lang->line("Payment method already exists"));
            redirect('customers/customers');
            }else{
                $cid = $this->companies_model->addCustomerPaymentMethod($data);
            $this->session->set_flashdata('message', $this->lang->line("payment_method_added"));
            redirect('customers/customers');
            }
            
        } else {
            $this->data['countries']=  $countries;
            $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['payment_methods']=  $this->companies_model->getAllPaymentMethods();
            $this->data['customer_id']=$customer->id;
            $this->data['customer_name']=$customer->name;
            $this->data['distributor_customer']=$distributorcustomer;
            $this->data['page_title'] = lang('add_payment_method');
            $this->load->view($this->theme.'customers/add_payment_method',$this->data);
        }

    }
    function select_county(){
        //$this->sma->checkPermissions('add-pm',true,'customers');

        $this->form_validation->set_rules('county_id', $this->lang->line("County"), 'required');

        $countries=$this->settings_model->getAllCurrencies();

        if ($this->form_validation->run('customers/select_county') == true) {
            //$cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $id=$this->input->post('county_id');
            $data = array(
                'county_id' => $id,
                //'payment_method_id' => $this->input->post('payment_method_id'),
            );
            redirect('customers/customersByCounty/'.$id);
        } 

         else {
            /**$this->data['counties']=$this->counties_model->getAllCounties();
            $this->data['page_title'] = lang('select_county');
            $this->load->view($this->theme.'customers/select_county',$this->data);**/
            $this->data['counties']=$this->counties_model->getAllCounties();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
            $meta = array('page_title' => lang('customers_by_county'), 'bc' => $bc);
            $this->page_construct('customers/select_county', $meta, $this->data);
        }

    }
    
    function view_payment_methods($id){
        $this->sma->checkPermissions('index-pm',true,'customers');

        
        $distributorcustomerpaymentmethods = $this->companies_model->getCustomerPaymentMethods($id);
        
        
        $customer=$this->companies_model->getCustomerByID($id);
        $countries=$this->settings_model->getAllCurrencies();

        $this->data['countries']=  $countries;
        $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
        $this->data['payment_methods']=  $this->companies_model->getAllPaymentMethods();
        $this->data['customer_id']=$customer->id;
        $this->data['customer_name']=$customer->name;
        $this->data['distributor_customer_payment_methods']=$distributorcustomerpaymentmethods;
        $this->data['page_title'] = lang('view_payment_methods');
        $this->load->view($this->theme.'customers/view_payment_methods',$this->data);
    }
    
    function edit_payment_method($id){
        $this->sma->checkPermissions('edit-pm',true,'customers');

        $this->form_validation->set_rules('payment_method_id', $this->lang->line("Payment Method"), 'required');

        $distributorcustomer=array();
        $distributor_customer_payment_method = $this->companies_model->getCustomerPaymentMethod($id);

        $customer=$this->companies_model->getCustomerByID($distributor_customer_payment_method[0]->customer_id);

        if ($this->form_validation->run('customers/edit_payment_method') == true) {
            $data = array(
                'customer_id' => $distributor_customer_payment_method[0]->customer_id,
                'payment_method_id' => $this->input->post('payment_method_id'),
            );
        } elseif ($this->input->post('edit_payment_method')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }

        if ($this->form_validation->run() == true) {
            if($this->companies_model->checkCustomerPaymentMethodExists($distributor_customer_payment_method[0]->customer_id,
                $this->input->post('payment_method_id'))){
            $this->session->set_flashdata('message', $this->lang->line("Payment method already exists"));
            redirect('customers/customers');
            }else{
                $cid = $this->companies_model->updateCustomerPaymentMethod($id,$data);
            $this->session->set_flashdata('message', $this->lang->line("payment_method_edited"));
            redirect('customers/customers');
            }
        } else {
            $this->data['countries']=  $countries;
            
            $this->data['payment_methods']=  $this->companies_model->getAllPaymentMethods();
            $this->data['customer_id']=$customer->id;
            $this->data['customer_name']=$customer->name;
            $this->data['distributor_customer_payment_method']=$distributor_customer_payment_method;
            $this->data['page_title'] = lang('edit_payment_method');
            $this->load->view($this->theme.'customers/edit_payment_method',$this->data);
        }

    }
    
    function delete_payment_method($id = NULL)
    {
        $this->sma->checkPermissions('delete-pm',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteCustomerPaymentMethod($id)) {
            $this->session->set_flashdata('message', lang('payment_method_deleted'));
            redirect('customers/customers');
        } else {
            $this->session->set_flashdata('warning', lang('shop_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

    }
    
    function import_customeralign_all($id){
                   $distributorcustomer=array();
          $distributorcustomer = $this->companies_model->getADistributorCustomer($id);
        
        $customer=$this->companies_model->getCustomerByID($id);
        $countries=$this->settings_model->getAllCurrencies();
        
        $this->data['countries']=  $countries;
         $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies();
        $this->data['cutomer_id']=$customer->id;
        $this->data['customer_name']=$customer->name;
        $this->data['distributor_customer']=$distributorcustomer;
        $this->data['page_title'] = lang('import_customer_descriptions');
        $this->load->view($this->theme.'customers/import_all_descr',$this->data);
        
    }
    
    function edit_cust_mapping(){
        
         $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('dp_id')) {
            $id = $this->input->get('dp_id');
            $newname=  str_replace("'","",$this->input->get('newname'));
            $newcountry=$this->input->get('newcountry');
            $data=array("distributor_naming"=>$newname);
            $this->db->where('id', $id);
            if ($this->db->update("customer_dist_sanofi_mapping",$data)) {
           die("Distributor customer mapping updated");
        }
           else{
            die("Could not update,check parameters!!");
        } 
            
        }else{
            die("Could not update,check parameters!");
        }
    }

    function delete_cust_mapping(){
        
         $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('dp_id')) {
            $id = $this->input->get('dp_id');
            $newname=  str_replace("'","",$this->input->get('newname'));
            $newcountry=$this->input->get('newcountry');
            $data=array("distributor_product_name"=>$newname,"country"=>$newcountry);
            $this->db->where('id', $id);
            //echo $id
             if ($this->db->delete("customer_dist_sanofi_mapping", array('id' => $id))) {
           die("Distributor customer mapping removed");
        }
           else{
            die("Could not delete,check parameters!!");
        } 
            
        }else{
            die("Could not delete,check parameters!");
        }
    }
    
    function getCustomerAlignments()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id,sf_alignment_name,customer_name,products")
            ->from("customer_alignments")
           // ->add_column("Actions", "<center><a class=\"tip\" title='". $this->lang->line("Msr_customer_Edit") . "' href='" . site_url('customers/edit_msr_customer/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . $this->lang->line("Delete_customer_Alignment") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
         ->add_column("Actions", "<center> <a class=\"tip\" title='" . $this->lang->line("Msr_customer_Edit") . "' href='" . site_url('customers/edit_msr_customer/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("Delete_customer_Alignment") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete_msr_cust/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    

    function getSalesTeamAlignment()
        {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');


         $qry1 = "(SELECT si.*, sma_employee.name as msrname FROM sma_msr_alignments si LEFT JOIN sma_employee ON sma_employee.alignment_id = si.id AND sma_employee.group_id = '1' ) sma_jtable1";
        $this->datatables
            ->select("jtable1.id,sma_jtable1.country,jtable1.business_unit,jtable1.team_name,sma_dsm_team_mapping.dsm_alignment_name,sma_employee.name as dsm,jtable1.msr_alignment_name as msr,jtable1.msrname as msrname")
            ->from($qry1)
             ->join("sma_dsm_team_mapping", 'dsm_team_mapping.team_id = jtable1.team_id ', 'left')
             ->join("sma_employee", 'employee.alignment_id = dsm_team_mapping.dsm_alignment_id AND employee.group_id = 2', 'left');
      
          
        //->unset_column('id');
        echo $this->datatables->generate();
    }
    
    
    
    function getCustomerDescription($id = NULL)
    {
                    $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        if($id){
        $this->datatables
             ->select("customer_dist_sanofi_mapping.id as id,customer_dist_sanofi_mapping.distributor_naming,customer_dist_sanofi_mapping.sanofi_naming")
            ->from("customer_dist_sanofi_mapping")
          ->add_column("Actions", "<center><a class=\"tip\" title='" . $this->lang->line("edit_customer") . "' href='" . site_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . $this->lang->line("delete_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        }
        else{
         $this->datatables
          ->select("customer_dist_sanofi_mapping.id as id,customer_dist_sanofi_mapping.distributor_naming,customer_dist_sanofi_mapping.sanofi_naming")
            ->from("customer_dist_sanofi_mapping")
         ->add_column("Actions", "<center><a class=\"tip\" title='" . $this->lang->line("edit_customer") . "' href='" . site_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . $this->lang->line("delete_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        }
        //->unset_column('id');
        echo $this->datatables->generate();
    }

    function getSubCustomers($id = NULL)
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        if($id){
        $this->datatables
             ->select("sma_companies.id as id,companies.name,currencies.country,phone, city")
            ->from("companies")
            ->join("currencies","currencies.id=companies.country","left")
            ->where('is_subsidiary',1)
            ->where('parent_company',$id)
          ->add_column("Actions", "<center><a class=\"tip\" title='" . $this->lang->line("edit_customer") . "' href='" . site_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . $this->lang->line("list_users") . "' href='" . site_url('customers/users/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-users\"></i></a> <a class=\"tip\" title='" . $this->lang->line("add_user") . "' href='" . site_url('customers/add_user/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        }
        else{
         $this->datatables
             ->select("sma_companies.id as id,companies.name,currencies.country,phone, city")
            ->from("companies")
            ->join("currencies","currencies.id=companies.country","left")
            ->where('is_subsidiary',1)
           
          ->add_column("Actions", "<center><a class=\"tip\" title='" . $this->lang->line("edit_customer") . "' href='" . site_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . $this->lang->line("list_users") . "' href='" . site_url('customers/users/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-users\"></i></a> <a class=\"tip\" title='" . $this->lang->line("add_user") . "' href='" . site_url('customers/add_user/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");    
        }
        //->unset_column('id');
        echo $this->datatables->generate();
    }

    function add()
    {
        $this->sma->checkPermissions('add',true,'distributors');

        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'is_unique[companies.email]');
            

        //$this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'required');

        if ($this->form_validation->run() == true) {
            if ($_FILES['avatar']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/avatars';
                $config['allowed_types'] = 'gif|jpg|png';
                //$config['max_size'] = '500';
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('avatar')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'assets/uploads/avatars/' . $photo;
                $config['new_image'] = 'assets/uploads/avatars/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 150;
                $config['height'] = 150;;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }

            } else {
                $this->form_validation->set_rules('avatar', lang("avatar"), 'required');
            }
            
            $data = array('name' => trim($this->input->post('first_name')) . ' ' . trim($this->input->post('last_name')),
                'email' => $this->input->post('email'),
                'group_id'=>'12',
                'group_name'=>'distributor',
                'customer_group_id'=>null,
                'customer_group_name'=>null,
                'company' => $this->input->post('company'),
                'address' => $this->input->post('address'),
                'vat_no' => $this->input->post('vat_no'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'is_subsidiary' => $this->input->post('parent_subsidiary'),
                'parent_company' => $this->input->post('parent_company'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'also_distributor' => 'Y',
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
            );
        } elseif ($this->input->post('add_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addCompany($data)) {
            $this->ion_auth->register($this->input->post('first_name') . ' ' . $this->input->post('last_name'), "powergas001", $this->input->post('email'), array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
                'avatar' => $photo,
                'gender' => "male",
                'group_id' => '12',
                'biller_id' => null,
                'company_id' => $cid,
                'warehouse_id' => null,), 1, 1);
            $this->session->set_flashdata('message', $this->lang->line("customer_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
            $this->data['companies']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/add', $this->data);
        }
    }
    
    function add2()
    {
        $this->sma->checkPermissions('add',true,'salespeople');

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

        $vehicles = $this->vehicles_model->getAllDistributorsVehicles($distributor->id);

        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'is_unique[companies.email]');

        //$this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'required');

        if ($this->form_validation->run() == true) {
            
            $data = array('name' => trim($this->input->post('first_name')) . ' ' . trim($this->input->post('last_name')),
                'email' => $this->input->post('email'),
                'group_id'=>'13',
                'vehicle_id'=>$this->input->post('vehicle_id'),
                'group_name'=>'sales_person',
                'customer_group_id'=>null,
                'customer_group_name'=>null,
                'distributor_id' => $distributor->id,
                'company' => $this->input->post('company'),
                'address' => $this->input->post('address'),
                'vat_no' => $this->input->post('vat_no'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'is_subsidiary' => $this->input->post('parent_subsidiary'),
                'parent_company' => $this->input->post('parent_company'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'also_distributor' => 'N',
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
            );
        } elseif ($this->input->post('add_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers2');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addCompany($data)) {
            $this->ion_auth->register($this->input->post('first_name') . ' ' . $this->input->post('last_name'), "powergas001", $this->input->post('email'), array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
                'gender' => "male",
                'group_id' => '13',
                'biller_id' => null,
                'company_id' => $cid,
                'warehouse_id' => null,
                'stock'=>$this->input->post('stock')), 1, 1);
            $json = array();
			
			$data = array(
			    "account_code"=>"",
			    "account_type"=>"3",
			    "bank_account_name"=>trim($this->input->post('first_name')) . ' ' . trim($this->input->post('last_name')).' CASH',
                "bank_name"=>"salesperson",
                "bank_account_number"=>"",
                "bank_address"=>"",
                "bank_curr_code"=>"KS", 
                "dflt_curr_act"=>"0",
                "bank_charge_act"=>"4024"
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
			CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/bank_account.php?action=add-bank-account&company-id=KAMP",
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
        	$bank_acc_id = $response_data->id;
           

            if ($status == "ok") { 
                $this->companies_model->updateCompany($cid,array('bank_acc_id'=>$bank_acc_id));
                $this->session->set_flashdata('message', $this->lang->line("Sales person added"));
                $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
                redirect('customers/customers2');
            } else {
                $this->session->set_flashdata('error', "Unable to add bank account to accounts erp" . "Response:" . $response);
                redirect('customers/customers2');
            }
            
        } else {
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
            $this->data['companies']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['vehicles'] = $vehicles;
            $this->load->view($this->theme . 'customers/addSalesPerson', $this->data);
        }
    }
      
    function add_alignment()
    {

        $this->form_validation->set_rules('alignment_name', lang("name"), 'trim|is_unique[alignments.alignment_name]|required');
        $this->form_validation->set_rules('alignment_rep', lang("alignment rep"), 'required');
        $this->form_validation->set_rules('region', lang("region"), 'required');
        $this->form_validation->set_rules('country', lang("country"), 'required');
        $this->form_validation->set_rules('period', lang("period"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array('alignment_name' => $this->input->post('alignment_name'),
            'alignment_rep' => $this->input->post('alignment_rep'),
                'region' => $this->input->post('region'),
                'country' => $this->input->post('country'),
                'period' => $this->input->post('period')
            );
        } elseif ($this->input->post('add_alignment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("customers/alignments");
        }

        if ($this->form_validation->run() == true && $this->companies_model->addAlignment($data)) {
            $this->session->set_flashdata('message', lang("Alignment Added"));
            redirect("customers/alignments");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/add_alignment', $this->data);
        }
    }

    function add_customer_alignment()
    {

       // $this->form_validation->set_rules('sf_alignment_name', lang("Sf Alignment"), 'trim|is_unique[customer_alignments.sf_alignment_name]|required');
       // $this->form_validation->set_rules('customer_name', lang("Customer Name"), 'required');
       // $this->form_validation->set_rules('products', lang("products"), 'required');
        
        if ($this->form_validation->run() == true) {
            
            
            $msr = $this->site->getmsrByID($this->input->post('sf_alignment_name'));
            $customer_details = $this->companies_model->getcustomerByID($this->input->post('customer_name'));
            $product_details = $this->products_model->getProductByID($this->input->post('products'));
          
            $data = array('customer_id' => $this->input->post('customer_name'),
                'sf_alignment_name' => $msr->msr_alignment_name,
                'customer_name'=>$customer_details->name,
                'products'=>$product_details->name,
                'country_id'=>$customer_details->country,
                'product_id'=>$this->input->post('products'),
                'sf_alignment_id'=>$this->input->post('sf_alignment_name'),
                
            );
        } elseif ($this->input->post('add_alignment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("customers/customer_alignments");
        }

        if ($this->form_validation->run() == true && $this->companies_model->addCustomerAlignment($data)) {
            $this->session->set_flashdata('message', lang("Alignment Added"));
            redirect("customers/customer_alignments");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['msrs']=  $this->settings_model->getAllMsr();
             $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
              $this->data['products']=  $this->products_model->getAllProducts();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/add_customer_alignment', $this->data);
        }
    }
    
    function import_names($id){
        //   $this->load->model('Distributor_product_model');
        
        //   $distributorproducts=array();
        //   $distributorproducts=$this->Distributor_product_model->getADistributorProduct($id);
        
        // $product=$this->products_model->getProductByID($id);
        // $countries=$this->settings_model->getAllCurrencies();
        
        // $this->data['countries']=  $countries;
         $this->data['distributors']=  $this->companies_model->getAllCustomerCompanies1();
        $this->data['product_id']=$product->id;
        $this->data['product_name']=$product->name;
        $this->data['distributor_products']=$distributorproducts;
        $this->data['page_title'] = lang('import customer names');
        $this->load->view($this->theme.'customers/import_names',$this->data);
        
    }
    
    function addEmployee()
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'is_unique[employee.email]');
            

        $this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');

        if ($this->form_validation->run() == true) {
            $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $msr = $this->site->getmsrByID($this->input->post('msr_alignment'));
            $dsm = $this->site->getdsmByID($this->input->post('dsm_alignment'));
            if($this->input->post('emp_grp') == 'MSR'){
                $group_id = '1';
                $alignment = $this->input->post('msr_alignment');
                $alignmentname = $msr->msr_alignment_name;

            }else{
                $group_id = '2';
                $alignment = $this->input->post('dsm_alignment');
            $alignmentname = $dsm->dsm_alignment_name;
            }
            $data = array('name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'group_id'=>$group_id,
                'group_name'=>$this->input->post('emp_grp'),
                'alignment_id'=>$alignment,
                'alignment_name'=>$alignmentname,
                'address' => $this->input->post('address'),
                'vat_no' => $this->input->post('vat_no'),
                'city' => $this->input->post('city'),
                'parent_company' => $this->input->post('parent_company'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
            );
        } elseif ($this->input->post('add_employee')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/employee');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addEmployee($data)) {
            $this->session->set_flashdata('message', $this->lang->line("Employee_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
           // $this->data['teams']=  $this->settings_model->getAllTeams();
            $this->data['msrs']=  $this->settings_model->getMsrNotAssigned();
            $this->data['dsms']=  $this->settings_model->getDsmNotAssigned();
            
             $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/addEmployee', $this->data);
        }
    }  

    function add1()
        {
            $this->sma->checkPermissions('add',true,'customers');

            if($this->Owner && $this->Admin){
                $this->form_validation->set_rules('distributor_id', $this->lang->line("Distributor"), 'required');
            }
            $is_distributor=0;
            if($this->Distributor){
                $is_distributor=1;
            }

            $distributors = $this->companies_model->getAllDistributorCompanies();

            $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'is_unique[customers.email]');

            $this->form_validation->set_rules('name', $this->lang->line("name"), 'is_unique[customers.name]');

            //$this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
            //$this->form_validation->set_rules('name', $this->lang->line('name'), 'required');

            if ($this->form_validation->run() == true) {
                $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
                $data = array('name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'group_id'=>'3',
                    'group_name'=>'customer',
                    'customer_group_id'=>$this->input->post('customer_group'),
                    'customer_group_name'=>$cg->name,
                    'address' => $this->input->post('address'),
                    'vat_no' => $this->input->post('vat_no'),
                    'city' => $this->input->post('city'),
                    'is_subsidiary' => 0,
                    'parent_company' => $this->input->post('parent_company'),
                    'country' => $this->input->post('country'),
                    'phone' => $this->input->post('phone'),
                    'phone2' => $this->input->post('phone2'),
                    'cf1' => $this->input->post('cf1'),
                    'cf2' => $this->input->post('cf2'),
                    'cf3' => $this->input->post('cf3'),
                    'cf4' => $this->input->post('cf4'),
                    'cf5' => $this->input->post('cf5'),
                    'cf6' => $this->input->post('cf6'),
                );
            } elseif ($this->input->post('add_customer1')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('customers/customers');
            }

            if ($this->form_validation->run() == true && $cid = $this->companies_model->addCustomer($data)) {
            
                $name = $this->input->post('name');
                $address = $this->input->post('address');
                
    			$json = array();
    			
    			$data = array('CustName' => $name,
                            'CustId' => $cid,
                            'Address' => $address,
                            'TaxId' => '',
                            'CurrencyCode' => 'KS',
                            'SalesType' => '1',
                            'CreditStatus' => '0',
    						'PaymentTerms' => '7',
    						'Discount' => '0',
    						'paymentDiscount' => '0',
    						'CreditLimit' => '0',
    						'Notes' => '');
    			
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
    			CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/customers.php?action=add-customer&company-id=KAMP",
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
                    $this->session->set_flashdata('message', $this->lang->line("customer_added1"));
                    redirect('customers/customers');
                } else {
                    $this->session->set_flashdata('error', "Unable to add item to account erp" . "Response:" . $response);
                    redirect('customers/customers');
                }
            
                
            } else {
                $this->data['countries']=  $this->settings_model->getAllCurrencies();
                $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['modal_js'] = $this->site->modal_js();
                $this->data['distributors'] = $distributors;
                $this->data['is_distributor'] = $is_distributor;
                $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
                $this->load->view($this->theme . 'customers/addCustomer', $this->data);
            }
        }

    function edit($id = NULL)
    {
        $this->sma->checkPermissions('edit',true,'distributor');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $company_details = $this->companies_model->getCompanyByID($id);
        if ($this->input->post('email') != $company_details->email) {
            $this->form_validation->set_rules('code', lang("email_address"), 'is_unique[companies.email]');
        }

        //$this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'required');
        if ($this->form_validation->run() == true) {
            
            $data = array('name' => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'group_id' => '12',
                'group_name' => 'distributor',
                'customer_group_id' => null,
                'customer_group_name' => null,
                'company' => $this->input->post('company'),
                'address' => $this->input->post('address'),
                'vat_no' => $this->input->post('vat_no'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'is_subsidiary' => $this->input->post('parent_subsidiary'),
                'parent_company' => $this->input->post('parent_company'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
                'award_points' => $this->input->post('award_points'),
            );
        } elseif ($this->input->post('edit_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCompany($id, $data)) {
            $user = $this->auth_model->getUserWithCompanyID($id);
            $this->ion_auth->update($user->id, array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
            ));
            $this->session->set_flashdata('message', $this->lang->line("customer_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['customer'] = $company_details;
            $this->data['companies']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['vehicles'] = $vehicles;
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/edit', $this->data);
        }
    }
    
    function edit2($id = NULL)
    {
        $this->sma->checkPermissions('edit',true,'salespeople');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $vehicles = $this->vehicles_model->getAllVehicles();
        $company_details = $this->companies_model->getCompanyByID($id);
        if ($this->input->post('email') != $company_details->email) {
            $this->form_validation->set_rules('code', lang("email_address"), 'is_unique[companies.email]');
        }

        //$this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'required');
        if ($this->form_validation->run() == true) {
            
            $data = array(
                'name' => trim($this->input->post('first_name')) . ' ' . trim($this->input->post('last_name')),
                'email' => $this->input->post('email'),
                'vehicle_id'=>$this->input->post('vehicle_id'),
                'group_id' => '13',
                'group_name' => 'sales_person',
                'distributor_id' => $distributor->id,
                'phone' => $this->input->post('phone'),
            );
        } elseif ($this->input->post('edit_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCompany($id, $data)) {
            $user = $this->auth_model->getUserWithCompanyID($id);
            $this->ion_auth->update($user->id, array(
                'username' => trim($this->input->post('first_name'))." ".trim($this->input->post('last_name')),
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'stock' => $this->input->post('stock'),
            ));
            $json = array();
			
			$data = array(
			    "id"=>$company_details->bank_acc_id,
			    "bank_account_name"=>trim($this->input->post('first_name')) . ' ' . trim($this->input->post('last_name')).' CASH',
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
			CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/bank_account.php?action=update-bank-account&company-id=KAMP",
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
                $this->session->set_flashdata('message', $this->lang->line("Sales person updated"));
                redirect('customers/customers2');
            } else {
                $this->session->set_flashdata('error', "Unable to update bank account and gl account in accounts erp" . "Response:" . $response);
                redirect('customers/customers2');
            }
        } else {
            $this->data['customer'] = $company_details;
            $this->data['companies']=  $this->companies_model->getAllCustomerCompanies();
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['vehicles'] = $vehicles;
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/edit2', $this->data);
        }
    }
    
    function editTeamAlignment($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $team_details = $this->companies_model->getsalesTeamByID($id);

        $this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('business_unit', $this->lang->line('business_unit'), 'required');
        if ($this->form_validation->run() == true) {

            $data = array('country' => $this->input->post('country'),
                'business_unit' => $this->input->post('business_unit'),
                'dm_alignment' => $this->input->post('dsm'),
                'dm_name' => $this->input->post('dsm_name'),
                'sf_alignment' => $this->input->post('msr'),
                'sf_name' => $this->input->post('msr_name'),
                'team_name' => $this->input->post('team_name'),
                
             
            );
        } elseif ($this->input->post('edit_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCustomer($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("customer_updated1"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['team'] = $team_details;
            //print_r($this->data['team']);
            //die();
            $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['teams']=  $this->settings_model->getTeams();
             $this->data['bu']=  $this->site->getAllBu();
             $this->data['msrs']=  $this->settings_model->getAllMsr();
            $this->data['dsms']=  $this->settings_model->getAllDsm();
            $dsmnam= 'DSM';
        $this->data['dsmemployee']=  $this->settings_model->getDSMemployee($dsmnam);
        // print_r($this->data['dsmemployee']);
        // die();
        $this->data['msremployee']=  $this->settings_model->getMSRemployee();    
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/edit_alignment', $this->data);
        }
    }
    
    function edit_msr_customer($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $customermsr_details = $this->companies_model->getcustomermsrByID($id);
 
        $this->form_validation->set_rules('msr_alignment', $this->lang->line('msr_alignment'), 'required');
        $this->form_validation->set_rules('product', $this->lang->line('product'), 'required');

        if ($this->form_validation->run() == true) {
            //$cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $msr = $this->site->getmsrByID($this->input->post('msr_alignment'));
            $customer_details = $this->companies_model->getcustomerByID($this->input->post('customer'));
            $product_details = $this->products_model->getProductByID($this->input->post('product'));
          
            $data = array('customer_id' => $this->input->post('customer'),
                'sf_alignment_name' => $msr->msr_alignment_name,
                'customer_name'=>$customer_details->name,
                'products'=>$product_details->name,
                'product_id'=>$this->input->post('product'),
                'sf_alignment_id'=>$this->input->post('msr_alignment'),
                
            );
        } elseif ($this->input->post('edit_msrcut')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateSfMsralignment($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("MSR_Customer_Updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['customermsr'] = $customermsr_details;
           // $this->data['teams']=  $this->settings_model->getAllTeams();
            $this->data['msrs']=  $this->settings_model->getAllMsr();
            $this->data['dsms']=  $this->settings_model->getAllDsm();
            $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
            $this->data['products']=  $this->products_model->getAllProducts();

            $this->data['countries']=  $this->settings_model->getAllCurrencies();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/edit_msr_customer', $this->data);
        }
    }
    
    function edit_employee($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $employee_details = $this->companies_model->getemployeeByID($id);
        if ($this->input->post('email') != $employee_details->email) {
            $this->form_validation->set_rules('code', lang("email_address"), 'is_unique[employee.email]');
        }

               

        $this->form_validation->set_rules('country', $this->lang->line('country'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');

        if ($this->form_validation->run() == true) {
            $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $msr = $this->site->getmsrByID($this->input->post('msr_alignment'));
            $dsm = $this->site->getdsmByID($this->input->post('dsm_alignment'));
            if($this->input->post('emp_grp') == 'MSR'){
                $group_id = '1';
                $alignment = $this->input->post('msr_alignment');
                $alignmentname = $msr->msr_alignment_name;

            }else{
                $group_id = '2';
            $alignment = $this->input->post('dsm_alignment');
            $alignmentname = $dsm->dsm_alignment_name;
            }
            $data = array('name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'group_id'=>$group_id,
                'group_name'=>$this->input->post('emp_grp'),
                'alignment_id'=>$alignment,
                'alignment_name'=>$alignmentname,
                'address' => $this->input->post('address'),
                'vat_no' => $this->input->post('vat_no'),
                'city' => $this->input->post('city'),
                'parent_company' => $this->input->post('parent_company'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
            );
        } elseif ($this->input->post('edit_employee')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateEmployee($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("Employee_Update"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['employee'] = $employee_details;
           // $this->data['teams']=  $this->settings_model->getAllTeams();
            $this->data['msrs']=  $this->settings_model->getMsrNotAssigned();
            $this->data['dsms']=  $this->settings_model->getDsmNotAssigned();
            $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
            $this->data['countries']=  $this->settings_model->getAllCurrencies();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/edt_employee', $this->data);
        }
    }

    function edit1($id = NULL)
    {
        $this->sma->checkPermissions('edit',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $company_details = $this->companies_model->getcustomerByID($id);
        if ($this->input->post('email') != $company_details->email) {
            $this->form_validation->set_rules('code', lang("email_address"), 'is_unique[customers.email]');
        }

        
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        if ($this->form_validation->run() == true) {
            $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $data = array(
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'group_id' => '3',
                'group_name' => 'customer',
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'phone' => $this->input->post('phone'),
                'phone2' => $this->input->post('phone2'),
                'active' => $this->input->post('active'),
            );
        } elseif ($this->input->post('edit_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCustomer($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("customer_updated1"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['customer'] = $company_details;
            $this->data['companies']=  $this->companies_model->getAllCustomerCustomers();
            $this->data['countries']=  $this->settings_model->getAllCurrencies(); 
            $this->data['towns']=  $this->towns_model->getAllTownsWithCounties();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->load->view($this->theme . 'customers/edit1', $this->data);
        }
    }

    function activate_customer($id = NULL)
    {
        $this->sma->checkPermissions('activate',null,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $data = array(
            'active' => '1',
        );

        if ( $this->companies_model->updateCustomer($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("Customer_activated"));
            redirect('customers/customers');
        } else {
            $this->session->set_flashdata('warning', lang('customer_not_activated'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }

    function users($company_id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }


        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['company'] = $this->companies_model->getCompanyByID($company_id);
        $this->data['users'] = $this->companies_model->getCompanyUsers($company_id);
        $this->load->view($this->theme . 'customers/users', $this->data);

    }

    function add_user($company_id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'is_unique[users.email]');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|min_length[8]|max_length[20]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('confirm_password'), 'required');

        if ($this->form_validation->run('companies/add_user') == true) {
            $active = $this->input->post('status');
            $notify = $this->input->post('notify');
            list($username, $domain) = explode("@", $this->input->post('email'));
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone'),
                'gender' => $this->input->post('gender'),
                'company_id' => $company->id,
                'company' => $company->company,
                'group_id' => 3
            );
            $this->load->library('ion_auth');
        } elseif ($this->input->post('add_user')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {
            $this->session->set_flashdata('message', $this->lang->line("user_added"));
            redirect("customers");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company'] = $company;
            $this->load->view($this->theme . 'customers/add_user', $this->data);
        }
    }

    function import_csv()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {
$errorlog="";
                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                //$keys = array( 'name', 'email', 'phone', 'address', 'city', 'state', 'postal_code', 'country','is_subsidiary','parent_company');
                $keys = array( 'name', 'email', 'phone', 'address', 'city', 'state', 'postal_code', 'country');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                   // if(strtolower($csv['is_subsidiary'])=="yes"){
                   //     $company=$this->companies_model->getCompanyByName($csv['parent_company']);
                   //     $csv['is_subsidiary']=1;
                   // }
                   // else{
                    //    $csv['is_subsidiary']=0;
                   // }
                    $csv['is_subsidiary']=0;
               // if (!is_object($company) && strtolower($csv['is_subsidiary'])==1) {
               //         $this->session->set_flashdata('error', $this->lang->line("check_customer_name") . " (" . $csv['parent_company'] . "). " . $this->lang->line("customer_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")");
                //        redirect("customers");
                //   }
                    
                    //$csv['parent_company']=$company->id;
                    $country=$this->settings_model->getCountryByName($csv['country']);
                    if(!$country){
                        $errorlog.= $this->lang->line("check_country") . " :" . $csv['country'] . ":. " . $this->lang->line("country_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw  .")\n";
                        //redirect("customers"); 
                    }
                    
                   
                    $csv['group_id'] = 3;
                    $csv['group_name'] = 'customer';
                    $csv['customer_group_id'] = 1;
                    $csv['customer_group_name'] = 'General';
                    $csv['country']=$country->id;
                    $csv['name']=str_replace("'","-",$csv['name']);
                    $csv['company']=str_replace("'","-",$csv['name']);
                    $csv['alert_quantity']=$csv['alert_qty'];
                    
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                
                                                    if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
            }

        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompanies($data)) {
                $this->session->set_flashdata('message', $this->lang->line("customers_added"));
                redirect('customers');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import', $this->data);
        }
    }
    
    function import_employee_csv()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {
$errorlog="";
                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers/employee");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array( 'name', 'employee_group', 'alignment', 'country',  'email','phone','city');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
if($csv['employee_group']=='MSR'){
                    $MsrAlignmentdet=$this->settings_model->getMsrAlignmentByname($csv['alignment'],$csv['country']);
}else{
    $MsrAlignmentdet=$this->settings_model->getDsmAlignmentByname($csv['alignment'],$csv['country']);
}
                    if(!$MsrAlignmentdet){
                        $errorlog.= $this->lang->line("msr_alignments") . " :" . $csv['alignment'] . ":" . $csv['country'] . ": " . $this->lang->line("msr_alignment_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                        //redirect("customers/employee"); 
                    }
                    
                     $countrydet=$this->settings_model->getCountryByName($csv['country']);
                     if(!$countrydet){
                       $errorlog.=  $this->lang->line("Country") . ":" . $csv['country'] . ": " . $this->lang->line("Country_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                        //redirect("customers/employee"); 
                    }
                   if($csv['employee_group']=='MSR'){
                        $csv['group_id'] = 1;
                        $csv['group_name'] = $csv['employee_group'];
                   }else if ($csv['employee_group']=='DSM'){
                        $csv['group_id'] = 2;
                         $csv['group_name'] = $csv['employee_group'];
                   }else{
                        $errorlog.= $this->lang->line("Employee_Group") . " :" . $csv['employee_group'] . ": " . $this->lang->line("Employee_Group_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                       // redirect("customers/employee"); 

                   }
                   
                    $csv['alignment_id'] = $MsrAlignmentdet->id;
                    $csv['alignment_name'] = $MsrAlignmentdet->name;
                    $csv['name']=str_replace("'","-",$csv['name']);
                   $csv['country'] = $countrydet->id;
                    $csv['email']=str_replace("'","-",$csv['email']);
                     $csv['phone']=str_replace("'","-",$csv['phone']);
                      $csv['city']=str_replace("'","-",$csv['city']);

                    
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                
                                                                 if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
            }

        } elseif ($this->input->post('import1')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/employee');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addEmployeebatch($data)) {
                $this->session->set_flashdata('message', $this->lang->line("Employee_added"));
                redirect('customers/employee');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import_employee', $this->data);
        }
    }
    
    function import_csv1()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');
        $errorlog="";

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers/customers");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }


                $titles = array_shift($arrResult);

                $keys = array(
                    'name',
                    'phone',
                    'city');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;

                foreach ($final as $csv) {
                    if(strtolower($csv['is_subsidiary'])=="yes"){
                        $company=$this->companies_model->getCustomerByName($csv['parent_company']);
                        $csv['is_subsidiary']=1;
                    }
                    else{
                        $csv['is_subsidiary']=0;
                    }

                    $city=$this->towns_model->getTownByName($csv['city']);

                    $csv['group_id'] = 3;
                    $csv['group_name'] = 'customer';
                    $csv['customer_group_id'] = 1;
                    $csv['customer_group_name'] = 'General';
                    $csv['city']=$city->id;
                    $csv['name']=str_replace("'","-",$csv['name']);
                   
                    $csv['alert_quantity']=$csv['alert_qty'];
                    
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                
              if($errorlog !=""){
                $this->settings_model->logErrors($errorlog);
                $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                redirect($_SERVER["HTTP_REFERER"]);
              }
            }

        } elseif ($this->input->post('import1')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customers');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCustomers($data)) {
                $this->session->set_flashdata('message', $this->lang->line("customers_added1"));
                redirect('customers/customers');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import1', $this->data);
        }
    }

    function import_alignment_csv()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers/alignments");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array( 'alignment_name', 'alignment_rep', 'region', 'country', 'period');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
//                    if(strtolower($csv['is_subsidiary'])=="yes"){
//                        $company=$this->companies_model->getCustomerByName($csv['parent_company']);
//                        $csv['is_subsidiary']=1;
//                    }
//                    else{
//                        $csv['is_subsidiary']=0;
//                    }
                    
//                if (!is_object($company) && strtolower($csv['is_subsidiary'])==1) {
//                        $this->session->set_flashdata('error', $this->lang->line("check_customer_name") . " (" . $csv['parent_company'] . "). " . $this->lang->line("customer_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")");
//                        redirect("customers/customers");
//                  }
                    
//                    $csv['parent_company']=$company->id;
//                    $country=$this->settings_model->getCountryByName($csv['country']);
//                    if(!$country){
//                        $this->session->set_flashdata('error', $this->lang->line("check_country") . " (" . $csv['country'] . "). " . $this->lang->line("country_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")");
//                        redirect("customers/customers"); 
//                    }
                    
                   
//                    $csv['group_id'] = 3;
//                    $csv['group_name'] = 'customer';
//                    $csv['customer_group_id'] = 1;
//                    $csv['customer_group_name'] = 'General';
//                    $csv['country']=$country->id;
//                    $csv['name']=str_replace("'","-",$csv['name']);
//                  
//                    $csv['alert_quantity']=$csv['alert_qty'];
                  
                             //  $csv['group_id'];
//                    $csv['group_name'] = 'customer';
//                    $csv['customer_group_id'] = 1;
//                    $csv['customer_group_name'] = 'General';
//                    $csv['country']=$country->id;
//                    $csv['name']=str_replace("'","-",$csv['name']);
//                  
//                    $csv['alert_quantity']=$csv['alert_qty'];
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                
                //$this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import1')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/alignments');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addAlignmentsBatch($data)) {
                $this->session->set_flashdata('message', $this->lang->line("Alignments Added"));
                redirect('customers/alignments');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import_alignments', $this->data);
        }
    }

    function import_customer_alignment_csv()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {
$errorlog="";
                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers/customer_alignments");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array( 'sf_alignment_name', 'customer_name', 'products');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {

                 
                    	if ($msr_details = $this->site->getmsrByName($csv['sf_alignment_name'])) {
						}else{
							$errorlog.= $this->lang->line("MSR_Alignment_Not_Found") . " : " . $csv['sf_alignment_name'] . " : " . $this->lang->line("Row_number") . " " . $rw."\n";
                            //redirect($_SERVER["HTTP_REFERER"]);
						}
					
						if ($customer_details = $this->companies_model->getCustomerByName($csv['customer_name'])) {
						}else{
							$errorlog.= $this->lang->line("Customer_Not_Found") . " : " . $csv['customer_name'] . " : " . $this->lang->line("Row_number") . " " . $rw."\n";
                            //redirect($_SERVER["HTTP_REFERER"]);
						}
						 
						if ($product_details = $this->products_model->getProductByName(str_replace("'","",$csv['products']))) {
						}else{
							$errorlog.= $this->lang->line("Product_Not_Found") . " : " . $csv['products'] . " : " . $this->lang->line("Row_number") . " " . $rw."\n";
                           // redirect($_SERVER["HTTP_REFERER"]);
						}
						
						 $rw++;
						    
                   // $data[] = $csv;
                    $data[] = array('customer_id' => $customer_details->id,
                    'customer_name' => $customer_details->name,
                    'sf_alignment_id' => $msr_details->id,
                    'sf_alignment_name' => $msr_details->msr_alignment_name,
                    'country_id'=>$customer_details->country,
                    'product_id' => $product_details->id,
                    'products' => $product_details->name
                    );
                   
                   
                }
                if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
               // $this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import1')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/customer_alignments');
        }
        
    //REMOVE DATA THAT HAD BEEN UPLOADED
        
        if ($this->form_validation->run() == true && !empty($data)) {
            $this->companies_model->remove_customermsrdata();
           // print_r($data);
           // die();
            if ($this->companies_model->addCustomerAlignmentsBatch($data)) {
                $this->session->set_flashdata('message', $this->lang->line("Alignments Added"));
                redirect('customers/customer_alignments');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import_customer_alignments', $this->data);
        }
    }

    function import_st_alignment_csv()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers/st_alignments");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                      $keys = array( 'country', 'business_unit', 'team_name',  'dm_alignment', 'dm_name', 'sf_alignment', 'sf_name',);

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                   
                    $data[] = $csv;
                    if ($country_details = $this->settings_model->getCountryByCode($csv['country'])) {
						}else{
							$this->session->set_flashdata('error', $this->lang->line("Country_Code_Not_Found") . " ( " . $csv['country'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
						}
					$teamdet = $this->settings_model->getTeamByID($this->input->post('team_name'));	
					
                    $teamdata = array('name' => $csv['team_name'],
		                'country_id'=>$country_details->id,
                        'country'=> $csv['country'],
                    );
                    
                    $dsmdata = array('dsm_alignment_name' => $csv['dm_alignment'],
		                'country_id'=>$country_details->id,
                        'country'=> $csv['country'],
                        'business_unit'=> $csv['business_unit'],
                    );
                    $dsmemployeedata = array('group_id' =>'1',
                    'group_name' =>'DSM',
		                'alignment_name'=>$csv['dm_alignment'],
                        'name'=> $csv['dm_name'],
                        'country'=> $country_details->id,
                    );
                    $msrdata = array('msr_alignment_name' => $csv['sf_alignment'],
		                'country_id'=>$country_details->id,
                        'country'=> $csv['country'],
                        'business_unit'=> $csv['business_unit'],
                    );
                    $msremployeedata = array('group_id' =>'2',
                    'group_name' =>'MSR',
		                'alignment_name'=>$csv['sf_alignment'],
                        'name'=> $csv['sf_name'],
                        'country'=> $country_details->id,
                    );
                    $rw++;
                }
                
                //$this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import1')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers/st_alignments');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addStAlignmentsBatch($data,$teamdata,
            $dsmdata,$dsmemployeedata,$msrdata,$msremployeedata)) {
                $this->session->set_flashdata('message', $this->lang->line("Alignments Added"));
                redirect('customer/msr');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import_st_alignments', $this->data);
        }
    }

    function import_customer()
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line("disabled_in_demo"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if (isset($_FILES["csv_file"])) /* if($_FILES['userfile']['size'] > 0) */ {

                $this->load->library('upload');
//
//                $config['upload_path'] = 'assets/uploads/csv/';
//                $config['allowed_types'] = 'csv|rtf|xls|xlsx|';
//                $config['max_size'] = '2000';
//                $config['overwrite'] = TRUE;
//
//                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('name', 'parent_company', 'country', 'phone', 'city');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                    $company=$this->companies_model->getCompanyByName($csv['parent_company']);
                    if (!$company) {
                        $this->session->set_flashdata('error', $this->lang->line("check_customer_name") . " (" . $csv['parent_company'] . "). " . $this->lang->line("customer_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")");
                        redirect("customers");
                    }
                    $country=$this->settings_model->getCountryByName($csv['country']);
                    if (!$country) {
                        $this->session->set_flashdata('error', $this->lang->line("check_country_name") . " (" . $csv['country'] . "). " . $this->lang->line("Doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")");
                        redirect("customers");
                    }
                    
                    $record['main_distributor'] =$company->id;
                    $record['sub_distributor'] =$csv['name'];
                    $record['country_id'] =$country->id;
                    $record['phone'] =$csv['phone'];
                    $record['city'] =$csv['city'];
                    $data[] = $record;
                    //print_r($csv);
                    $rw++;
                }
               
             //  $this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customers');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addSubCompany($data)) {
                $this->session->set_flashdata('message', $this->lang->line("customers_added"));
                redirect('customers');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import_customer', $this->data);
        }
    }

    function edit_alignment($id = NULL)
    {

        $this->form_validation->set_rules('alignment_name', lang("group_name1"), 'trim|required');
        $pg_details = $this->settings_model->getAlignmentByID($id);
        if ($this->input->post('alignment_name') != $pg_details->alignment_name) {
            $this->form_validation->set_rules('alignment_name', lang("group_name1"), 'is_unique[alignment.alignment_name]');
        }
        $this->form_validation->set_rules('region/country', lang("group_percentage1"), 'required');

        if ($this->form_validation->run() == true) {

            $data = array('alignment_name' => $this->input->post('alignment_name'),
                'region/country' => $this->input->post('region/country'),
            );
        } elseif ($this->input->post('edit_alignment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/alignment_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateAlignment($id, $data)) {
            $this->session->set_flashdata('message', lang("customer_group_updated1"));
            redirect("customers/alignments");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['customer_group'] = $this->settings_model->getAlignmentByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/edit_alignment', $this->data);
        }
    }

    function delete_ticket($id = NULL)
    {
        
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->companies_model->deleteTicket($id)) {
            echo $this->lang->line("Ticket deleted");
        }
        
    }
    function delete_smscode($id = NULL)
    {
        
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->companies_model->deleteSmsCode($id)) {
            echo $this->lang->line("Ticket deleted");
        }
        
    }
    function delete($id = NULL)
    {
        $this->sma->checkPermissions('delete',null,'salespeople');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->input->get('id') == 1) {
            $this->session->set_flashdata('error', lang('customer_x_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

        $company_data = $this->companies_model->getCompanyByID($id);
        if ($this->companies_model->deleteSalesPerson($id)) {
            $json = array();
			
			$data = array(
			    "id"=>$company_data->bank_acc_id
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
			CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/bank_account.php?action=delete-bank-account&company-id=KAMP",
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
                echo $this->lang->line("Sales person deleted");
            } else {
                $this->session->set_flashdata('error', "Unable to delete bank account and gl account in accounts erp" . "Response:" . $response);
                redirect('customers/customers2');
            }
        } else {
            $this->session->set_flashdata('warning', lang('customer_x_deleted_have_sales'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }

    function delete1($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->input->get('id') == 1) {
            $this->session->set_flashdata('error', lang('customer_x_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

        if ($this->companies_model->deleteCustomer1($id)) {
            echo $this->lang->line("customer_deleted1");
        } else {
            $this->session->set_flashdata('warning', lang('customer_x_deleted_have_sales'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    
    

    function deactivate_salesperson($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deactivateCustomer2($id)) {
            echo $this->lang->line("salesperson_deactivated");
        } else {
            $this->session->set_flashdata('warning', lang('failed_to_deactivate'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    
    function activate_salesperson($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'customers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->activateCustomer2($id)) {
            echo $this->lang->line("saleperson_activated");
        } else {
            $this->session->set_flashdata('warning', lang('failed_to_activate'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }

    function delete_employee($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->input->get('id') == 1) {
            $this->session->set_flashdata('error', lang('employee_x_deleted1'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

        if ($this->companies_model->deleteEmployee($id)) {
            echo $this->lang->line("employee_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('employee_x_deleted_have_sales1'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }

    function delete_msr_cust($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->input->get('id') == 1) {
            $this->session->set_flashdata('error', lang('custmsr_x_deleted1'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }

        if ($this->companies_model->deletemsrcustalignement($id)) {
            echo $this->lang->line("MSR_Customer_alignment_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('custmsr_x_deleted_have_sales1'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }

    function suggestions($term = NULL, $limit = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        if (strlen($term) < 1) {
            return FALSE;
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->companies_model->getCustomerSuggestions($term, $limit);
        echo json_encode($rows);
    }

    function getCustomer($id = NULL)
    {
        $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        echo json_encode(array(array('id' => $row->id, 'text' => ($row->company != '-' ? $row->company : $row->name))));
    }

    function get_award_points($id = NULL)
    {
        $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        echo json_encode(array('ca_points' => $row->award_points));
    }

    function customer_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('country'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('phone'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('city'));
                   
                    

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getCompanyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->counname);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $customer->phone);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $customer->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $customer->city);
                      
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'disributors_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    function customerAlignments_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteEmployee($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('Country'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Business_Unit'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Team_Name'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('Dsm'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('Dsm_Name'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('MSR'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('MSR_Name'));
                    

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getsalesTeamalignmentsByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->country);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->business_unit);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->team_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $customer->dsm);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $customer->dsm_alignment_name);
    
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $customer->msr);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $customer->msrname);
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'salesteamalignment_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    function add_mapping(){
        
         $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('newname')) {
            
            $newname=  str_replace("'","",$this->input->get('newname'));
            $newcountry=$this->input->get('newcountry');
            $newdistributor=$this->input->get('newdistributor');
            $customer=$this->companies_model->getCustomerByID($this->input->get('cutomer_id'));
            $data=array("distributor_naming"=>$newname,"country"=>$newcountry,"distributor"=>$newdistributor,"sanofi_naming"=>$customer->name,"customer_id"=>$this->input->get('cutomer_id'));
            
        if ($this->db->insert("customer_dist_sanofi_mapping",$data)) {
                die("Customer mapping added");
        }
           else {
            die("Could not add,check parameters!!");
        } 
            
        }   else {
            die("Could not add,check parameters!");
        }
    }
    
    function import_customer_mapping()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
         $this->load->model('companies_model');
//die(print_r($_FILES));
$errorlog="";
        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {   

                $this->load->library('upload');
                
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);
               // die(print_r($config['upload_path']));
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("customers/customers");
                }

                $csv = $this->upload->file_name;
                //$productid=$this->input->post('product_id');
                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !==FALSE) {
                        $arrResult[] = $row;
                    }

                    fclose($handle);
                }
              //  unlink($this->digital_upload_path . $csv);
                $titles = array_shift($arrResult);

                $keys = array('country','distributor','distributor_customer_name','sanofi_customer_name');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
             // $this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    
                    $trimmedname=  str_replace(" ","",$csv_pr['distributor']);
                    $trimmedcountry=  str_replace(" ","",$csv_pr['country']);
                   $countrydet= $this->settings_model->getCountryByName($trimmedcountry);
                    if (!$countrydet){
                         $errorlog.=  "Check country" . " :" .$csv_pr['country'] . ": " . "doesnt exist" . " " . lang("line_no") . " " . $rw. "\n";
                                //                redirect("customers/customers");
                    }
                    $distr=$this->companies_model->getCompanyByNameAndCountry($trimmedname,$countrydet->id);
                    if (!$distr){
                         $errorlog.= "Check distributor" . " :" .$csv_pr['distributor'] . ": " . "doesnt exist or linked to wrong country" . " " . lang("line_no") . " " . $rw."\n";
                        //redirect("customers/customers");
                    }
                    $basecustomerdetails=  $this->companies_model->getCustomerByName(trim($csv_pr['sanofi_customer_name']));
                     if (!$basecustomerdetails){
                     $errorlog.= "Check customer" . " :" .$csv_pr['sanofi_customer_name'] . ": " . "doesnt exist" . " " . lang("line_no") . " " . $rw."\n";
                       // redirect("customers/customers");
                     
                    }
                    
                    $countryy[]=$countrydet->id;
                        $distributorid[] = trim($distr->id);
                    $distributorname[] = trim($csv_pr['distributor_customer_name']);
                        $customer_name[] = trim(str_replace("'","",$csv_pr['sanofi_customer_name']));
                        $customer_ids[]=trim($basecustomerdetails->id);
                        $base_customer_names[]=$basecustomerdetails->name;
                       
                    $rw++;
                }
                if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
            }

            $ikeys = array('customer_id','distributor', 'distributor_naming', 'sanofi_naming', 'country');

            $items = array();
            foreach (array_map(null,$customer_ids,$distributorid,$distributorname, $customer_name,$countryy) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

         // $this->sma->print_arrays($items);
            
             if ($this->companies_model->addcustomerMapping($items)) {
            $this->session->set_flashdata('message', lang("distributor_product_names_imported"));
            redirect('customers/customers');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products_by_csv')));
            $meta = array('page_title' => lang('map_distributor_products'), 'bc' => $bc);
            $this->page_construct('customers/customers', $meta, $this->data); //redirect("system_settings/import_currency");

        }
        }

       
    }
    

    function customer_actions1()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer1($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted1"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('phone'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('city'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('country'));
                    

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getCompanyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->company);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->email);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $customer->phone);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $customer->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $customer->city);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $customer->country);
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customers_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    
     function sanoficustomer_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer1($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted1"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('country'));
                   
                    

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getCustByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->counname);
                        
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customers_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
            function msrcust_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer2($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted1"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer_alignments'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('MSR'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Cusomer_Name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Products'));
                    

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getCustomeralignmentsByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->sf_alignment_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->customer_name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->products);
                       
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'msrcustomers_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    
    function employee_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer2($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted1"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('Employee'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('Name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Designation'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Alignment'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('Country'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('Phone'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('City'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getEmployeeByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->group_name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->alignment_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $customer->country);
                       $this->excel->getActiveSheet()->SetCellValue('E' . $row, $customer->phone);
                       $this->excel->getActiveSheet()->SetCellValue('F' . $row, $customer->city);
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'employee' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
   
        function alignments_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer1($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line("customers_deleted1"));
                    }
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('phone'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('city'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('country'));
                    

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getCompanyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->company);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->email);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $customer->phone);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $customer->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $customer->city);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $customer->country);
                        
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customers_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_customer_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    } 
    
    

}
