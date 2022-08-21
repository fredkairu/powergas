<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicles extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        /* if (!$this->loggedIn) {
             $this->session->set_userdata('requested_page', $this->uri->uri_string());
             redirect('login');
         }*/
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('vehicles', $this->Settings->language);
        $this->lang->load('routes', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->library('ion_auth');
        $this->load->model('vehicles_model');
        $this->load->model('routes_model');
        $this->load->model('companies_model');
        $this->load->model('auth_model');

    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vehicles')));
        $meta = array('page_title' => lang('vehicles'), 'bc' => $bc);
        $this->page_construct('vehicles/index', $meta, $this->data);
    }
    function vehiclesForManualPlannig($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vehicles')));
        $meta = array('page_title' => lang('vehicles'), 'bc' => $bc);
        $this->page_construct('vehicles/test_post', $meta, $this->data);
    }
    function testPage()
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vehicles')));
        $meta = array('page_title' => lang('vehicles'), 'bc' => $bc);
        $this->page_construct('vehicles/test_page', $meta, $this->data);
    }
    function routeplan($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Routes_Allocations_Summary')));
        $meta = array('page_title' => lang('routes_summary'), 'bc' => $bc);
        $this->page_construct('vehicles/route_plan_summary', $meta, $this->data);
    }
    function routeplanByDay($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Daily_Routes')));
        $meta = array('page_title' => lang('allocations'), 'bc' => $bc);
        $this->page_construct('vehicles/daily_route_summary', $meta, $this->data);
    }
    function tomorrowRouteplan($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Route_Allocations_for_Tomorrow')));
        $meta = array('page_title' => lang('allocations'), 'bc' => $bc);
        $this->page_construct('vehicles/tomorrow_route_summary', $meta, $this->data);
    }
    function routeStartingPoints($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Starting_Points')));
        $meta = array('page_title' => lang('Starting_points'), 'bc' => $bc);
       // print_r($this->data);
        $this->page_construct('vehicles/starting_points_summary', $meta, $this->data);
    }
    function disabledRouteplanByDay($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Disabled_Allocations')));
        $meta = array('page_title' => lang('allocations'), 'bc' => $bc);
        $this->page_construct('vehicles/disabled_daily_route', $meta, $this->data);
    }

    function getVehicles()
    {
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_vehicles.id as id, sma_vehicles.plate_no, sma_vehicles.discount_enabled")
            ->from("sma_vehicles")
            ->where('sma_vehicles.distributor_id', $distributor->id)
            ->add_column("Actions", "<center>
                <a class=\"tip\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-pencil\"></i></a> 
                <a class=\"tip\" title='" . $this->lang->line("add_route") . "' href='" . site_url('vehicles/add_route/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-road\"></i></a>
                <a class=\"tip\" title='" . $this->lang->line("view_planned_routes") . "' href='" . site_url('vehicles/view_vroomroutes/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-truck\"></i></a> 
                <a class=\"tip\" title='" . $this->lang->line("View_Routes") . "' href='" . site_url('vehicles/view_routes/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-link\"></i></a> 
                <a class=\"tip\" title='" . $this->lang->line("View_Stock") . "' href='" . site_url('vehicles/view_stock/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-cubes\"></i></a>
                <a class=\"tip\" title='" . $this->lang->line("Edit_Stock") . "' href='" . site_url('vehicles/edit_vehicle_stock/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-cube\"></i></a> 
                <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_vehicle") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('vehicles/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }

/***
    function getRoutPlanData()
    {
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        //$this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_customers.id as id,sma_shops.shop_name, sma_allocation_days.day as day,sma_vehicles.plate_no,sma_customers.name as customer")
            ->from("sma_shops")
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id ', 'left')
            ->join('sma_shop_allocations', 'sma_shops.id = sma_shop_allocations.shop_id ', 'left')
            ->join('sma_vehicle_route', 'sma_shop_allocations.route_id=sma_vehicle_route.route_id', 'left')
            ->join('sma_vehicles', 'sma_vehicle_route.vehicle_id = sma_vehicles.id', 'left')
            ->join('sma_routes', 'sma_vehicle_route.route_id = sma_routes.id', 'left')
            ->join('sma_allocation_days', 'sma_shop_allocations.id = sma_allocation_days.allocation_id', 'left')
           // ->join('sma_days_of_the_week', 'sma_allocation_days.day = sma_days_of_the_week.id ', 'left')
           // ->where('sma_vehicles.distributor_id', $distributor->id)
            ->add_column("Actions", "<center>
                <a class=\"tip\" title='" . $this->lang->line("deactivate_shop") . "' href='" . site_url('vehicles/editShop/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-pencil\"></i></a> 
                <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_vehicle") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('vehicles/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    } **/
    function getRoutPlanData()
    {
    
        //$distributor->id
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        var_dump($distributor);
        $this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_allocation_days.id as id,sma_shops.shop_name,sma_days_of_the_week.name as route,sma_customers.name as customer")
            ->from("sma_shop_allocations")
            ->join('sma_shops', 'sma_shop_allocations.shop_id =sma_shops.id', 'left')
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id ', 'left')
            ->join('sma_allocation_days', 'sma_shop_allocations.id = sma_allocation_days.allocation_id', 'left')
            ->join('sma_days_of_the_week', 'sma_allocation_days.day = sma_days_of_the_week.id ', 'left')
           //->where(`sma_vehicles.distributor_id, $distributor->id)
            //distributor_id
            ->add_column("Actions", "<center> 
            <a class=\"tip\" title='" . $this->lang->line("Activate Shop") . "' href='" . site_url('vehicles/activate_allocation/$1') . "' ><i class=\"fa fa-check\"></i></a>
                <a href='#' class='tip po' title='<b>" . $this->lang->line("deactivate_allocation") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('vehicles/deactivate_allocation/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-remove\"></i></a></center>", "id");
        //->unset_column('id');
        var_dump($this->datatables);
        echo $this->datatables->generate();

    }
    function getRoutPlanDataByDay()
    {
        $date=date('w');
       
       if($date==0)
       {
           $day=7;
    }
       else
       {
           $day=$date;
        }
        
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_allocation_days.id as id,sma_shops.shop_name,sma_days_of_the_week.name as route,sma_customers.name as customer,sma_vehicles.plate_no,sma_allocation_days.active as active")
            ->from("sma_shop_allocations")
            ->join('sma_shops', 'sma_shop_allocations.shop_id =sma_shops.id', 'left')
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id ', 'left')
            ->join('sma_vehicle_route', 'sma_shop_allocations.route_id=sma_vehicle_route.route_id ', 'left')
            ->join('sma_vehicles', 'sma_vehicle_route.vehicle_id=sma_vehicles.id ', 'left')
            ->join('sma_allocation_days', 'sma_shop_allocations.id = sma_allocation_days.allocation_id', 'left')
            ->join('sma_days_of_the_week', 'sma_allocation_days.day = sma_days_of_the_week.id ', 'left')
            ->where('sma_allocation_days.day', $day)
            ->group_by('sma_shop_allocations.id')
            ->add_column("Actions", "<center>
            <a class=\"tip\" title='" . $this->lang->line("Activate Shop") . "' href='" . site_url('vehicles/activate_allocation/$1') . "' ><i class=\"fa fa-check\"></i></a>
                <a href='#' class='tip po' title='<b>" . $this->lang->line("deactivate_allocation?") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('vehicles/deactivate_allocation/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-remove\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }
    function getRouteStartingPoints()
    {
        $date=date('w');
       
       if($date==0)
       {
           $day=7;
       }
       else
        {
           $day=$date;
        }
    
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_allocation_days.id as id,sma_shops.shop_name as nnn,sma_days_of_the_week.name as route,sma_customers.name as customer,sma_vehicles.plate_no,IFNULL(sma_allocation_days.start_point,'0') as start")
            ->from("sma_shop_allocations")
            ->join('sma_shops', 'sma_shop_allocations.shop_id =sma_shops.id', 'left')
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id ', 'left')
            ->join('sma_vehicle_route', 'sma_shop_allocations.route_id=sma_vehicle_route.route_id ', 'left')
            ->join('sma_vehicles', 'sma_vehicle_route.vehicle_id=sma_vehicles.id ', 'left')
            ->join('sma_allocation_days', 'sma_shop_allocations.id = sma_allocation_days.allocation_id', 'left')
            ->join('sma_days_of_the_week', 'sma_allocation_days.day = sma_days_of_the_week.id ', 'left')
            ->where('sma_allocation_days.day', $day)
            ->group_by('sma_shop_allocations.id')
            ->add_column("Actions", "<center>
            <a class=\"tip\" title='" . $this->lang->line("remove starting Point") . "' href='" . site_url('vehicles/remove_start/$1') . "' ><i class=\"fa fa-remove\"></i></a>
            <a href='#' class='tip po' title='<b>" . $this->lang->line("make_start_point?") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-primary po-delete' href='" . site_url('vehicles/make_start/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-send\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function getTomorrowRoutPlanData()
    {
        $date=date('w');
        $day=$date+1;
       
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_allocation_days.id as id,sma_shops.shop_name,sma_days_of_the_week.name as route,sma_customers.name as customer,sma_vehicles.plate_no,sma_allocation_days.active as active")
            ->from("sma_shop_allocations")
            ->join('sma_shops', 'sma_shop_allocations.shop_id =sma_shops.id', 'left')
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id ', 'left')
            ->join('sma_vehicle_route', 'sma_shop_allocations.route_id=sma_vehicle_route.route_id ', 'left')
            ->join('sma_vehicles', 'sma_vehicle_route.vehicle_id=sma_vehicles.id ', 'left')
            ->join('sma_allocation_days', 'sma_shop_allocations.id = sma_allocation_days.allocation_id', 'left')
            ->join('sma_days_of_the_week', 'sma_allocation_days.day = sma_days_of_the_week.id ', 'left')
            ->where('sma_allocation_days.day', $day)
            ->group_by('sma_shop_allocations.id')
            ->add_column("Actions", "<center>
            <a class=\"tip\" title='" . $this->lang->line("Activate Shop") . "' href='" . site_url('vehicles/activate_tmwallocation/$1') . "' ><i class=\"fa fa-check\"></i></a>
                <a href='#' class='tip po' title='<b>" . $this->lang->line("deactivate_allocation?") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('vehicles/deactivate_allocation/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-remove\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }

    function getDisabledRoutPlanDataByDay()
    {
        $date=date('w');
       
       if($date==0)
       {
           $day=7;
    }
       else
       {
           $day=$date;
        }
       
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'vehicles');
        $this->load->library('datatables');
        $this->datatables
            ->select("sma_allocation_days.id as id,sma_shops.shop_name,sma_days_of_the_week.name as route,sma_customers.name as customer,sma_vehicles.plate_no,sma_allocation_days.active as active")
            ->from("sma_shop_allocations")
            ->join('sma_shops', 'sma_shop_allocations.shop_id =sma_shops.id', 'left')
            ->join('sma_customers', 'sma_shops.customer_id=sma_customers.id ', 'left')
            ->join('sma_vehicle_route', 'sma_shop_allocations.route_id=sma_vehicle_route.route_id ', 'left')
            ->join('sma_vehicles', 'sma_vehicle_route.vehicle_id=sma_vehicles.id ', 'left')
            ->join('sma_allocation_days', 'sma_shop_allocations.id = sma_allocation_days.allocation_id', 'left')
            ->join('sma_days_of_the_week', 'sma_allocation_days.day = sma_days_of_the_week.id ', 'left')
            ->where('sma_allocation_days.day', $day)
            ->where('sma_allocation_days.active', 0)
            ->group_by('sma_shop_allocations.id')
            ->add_column("Actions", "<center>
            <a class=\"tip\" title='" . $this->lang->line("Activate Shop") . "' href='" . site_url('vehicles/activate_allocation/$1') . "' ><i class=\"fa fa-check\"></i></a>
                <a href='#' class='tip po' title='<b>" . $this->lang->line("deactivate_allocation?") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('vehicles/deactivate_allocation/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-remove\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }
    function getVehicle($id = NULL)
    {
        $this->sma->checkPermissions('index',true,'vehicles');
        $row = $this->vehicles_model->getVehicleByID($id);
        echo json_encode(array(array('id' => $row->id, 'text' => $row->plate_no)));
    }

    function add()
    {
        $this->sma->checkPermissions('add',true,'vehicles');

        $this->form_validation->set_rules('plate_no', 'Plate No', 'required');
        $this->form_validation->set_rules('discount_enabled', 'Discount', 'required');

        $routes = $this->routes_model->getAllRoutes();

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

        if ($this->form_validation->run('vehicles/add') == true) {

            $data = array(
                'distributor_id' => $distributor->id,
                'plate_no' => $this->input->post('plate_no'),
                'discount_enabled' => $this->input->post('discount_enabled'),
            );

        } elseif ($this->input->post('add_vehicle')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }

        if ($this->form_validation->run() == true && $rid = $this->vehicles_model->addVehicle($data)) {
            $this->session->set_flashdata('message', $this->lang->line("vehicle_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('vehicles');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['routes'] = $routes;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vehicles')));
            $meta = array('page_title' => lang('vehicles'), 'bc' => $bc);
            $this->page_construct('vehicles/add', $meta, $this->data);
        }
    }

    function edit($id = NULL)
    {
        $this->sma->checkPermissions('edit',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $vehicle = $this->vehicles_model->getVehicleByID($id);

        
        $this->form_validation->set_rules('plate_no', 'Plate No', 'required');
        $this->form_validation->set_rules('discount_enabled', 'Discount', 'required');

        if ($this->form_validation->run('vehicles/edit') == true) {

            $data = array(
                'distributor_id' => $vehicle->distributor_id,
                'plate_no' => $this->input->post('plate_no'),
                'discount_enabled' => $this->input->post('discount_enabled'),
            );

        } elseif ($this->input->post('edit_vehicle')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }

        if ($this->form_validation->run() == true && $rid = $this->vehicles_model->updateVehicle($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("vehicle_updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('vehicles');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['vehicle'] = $vehicle;
            $this->load->view($this->theme . 'vehicles/edit', $this->data);
        }
    }
    function viewVehicleRoutes($id = NULL)
    {
        $this->sma->checkPermissions('edit',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $vehicle = $this->vehicles_model->getVehicleByID($id);
        
        $this->form_validation->set_rules('plate_no', 'Plate No', 'required');
        $this->form_validation->set_rules('discount_enabled', 'Discount', 'required');

        if ($this->form_validation->run('vehicles/viewVehicleRoutes') == true) {

            $data = array(
                'distributor_id' => $vehicle->distributor_id,
                'plate_no' => $this->input->post('plate_no'),
                'discount_enabled' => $this->input->post('discount_enabled'),
            );

        } elseif ($this->input->post('edit_vehicle')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }

        if ($this->form_validation->run() == true && $rid = $this->vehicles_model->updateVehicle($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("vehicle_updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('vehicles');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['vehicle'] = $vehicle;
            //$this->load->view($this->theme . 'vehicles/test_test.php',$this->data);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vehicles')));
            $meta = array('page_title' => lang('vehicles'), 'bc' => $bc);
            $this->page_construct('vehicles/test_test', $meta, $this->data);
        }
    }

//     function getVehiclesForRoute()
//     {
//         $current_date = date("Y-m-d").' '.'23:59:00';
//         $day = 1;
//         $vehicle_id = 13;

//         $this->load->library('datatables');
//         $this->datatables
//         ->select('sma_customers.id as id,sma_allocation_days.id as allid, sma_customers.name, sma_customers.phone, sma_customers.active, sma_customers.email, sma_customers.customer_group_id, sma_customers.customer_group_name, sma_allocation_days.duration as durations,sma_allocation_days.position as positions,sma_shops.image as logo, sma_shops.shop_name, sma_shops.id as shop_id, sma_shops.lat, sma_shops.lng, sma_currencies.french_name as county_name, sma_cities.city as town_name,sma_cities.id as town_id')
//         ->from('sma_shops')
//         ->join('sma_customers','sma_customers.id = sma_shops.customer_id','left')
//         ->join('sma_cities','sma_cities.id = sma_customers.city','left')
//         ->join('sma_currencies','sma_currencies.id = sma_cities.county_id','left')
//         ->join('sma_shop_allocations','sma_shop_allocations.shop_id = sma_shops.id','left')
//         ->join('sma_vehicle_route','sma_shop_allocations.route_id=sma_vehicle_route.route_id','left')
//         ->join('sma_vehicles','sma_vehicle_route.vehicle_id = sma_vehicles.id','left')
//         ->join('sma_routes','sma_vehicle_route.route_id = sma_routes.id','left')
//         ->join('sma_allocation_days','sma_allocation_days.allocation_id = sma_shop_allocations.id','left');
//          ->where("NOT EXISTS (SELECT * FROM   sma_sales
//         WHERE  sma_shops.id = sma_sales.shop_id and sma_sales.date = CURRENT_DATE and sma_sales.created < '$current_date')
//         AND NOT EXISTS
//         (SELECT *
//         FROM   sma_tickets
//         WHERE  sma_shops.id = sma_tickets.shop_id and sma_tickets.date = CURRENT_DATE and sma_tickets.created_at < '$current_date') and 
//          sma_vehicles.id = '.$vehicle_id.'and sma_customers.active = 1 and sma_allocation_days.day = '.$day.'and (sma_allocation_days.duration > 0 or sma_allocation_days.start_point = 1) and sma_allocation_days.active = 1 and sma_vehicle_route.day = .'$day.' and sma_allocation_days.salesman_id = '.$vehicle_id.' and 
//          sma_allocation_days.expiry IS NULL or sma_allocation_days.expiry <= CURRENT_TIMESTAMP GROUP BY sma_shops.id ORDER BY sma_allocation_days.position ASC"); 
     
//         // echo $this->datatables->generate();
 
// }

function testFunction($id = NULL)
{
    $vehicle = $this->vehicles_model->getVehicleByID($id);
  //  echo json_encode($vehicle->id);

    $current_date = date("Y-m-d").' '.'23:59:00';
    $day = 1;
    $vehicle_id = $vehicle->id;
  $query=  $this->db->query("
    SELECT sma_customers.id as id,sma_allocation_days.id as allid, sma_customers.name, sma_customers.phone, sma_customers.active, sma_customers.email, sma_customers.customer_group_id, sma_customers.customer_group_name, sma_allocation_days.duration as durations,sma_allocation_days.position as positions,sma_shops.image as logo, sma_shops.shop_name, sma_shops.id as shop_id, sma_shops.lat, sma_shops.lng, sma_currencies.french_name as county_name, sma_cities.city as town_name,sma_cities.id as town_id
    FROM   sma_shops
				left join sma_customers on sma_customers.id = sma_shops.customer_id
                left join sma_cities on sma_cities.id = sma_customers.city
                left join sma_currencies on sma_currencies.id = sma_cities.county_id
                left join sma_shop_allocations on sma_shop_allocations.shop_id = sma_shops.id 
                left join sma_vehicle_route on sma_shop_allocations.route_id=sma_vehicle_route.route_id
                left join sma_vehicles on sma_vehicle_route.vehicle_id = sma_vehicles.id
                left join sma_routes on sma_vehicle_route.route_id = sma_routes.id 
                left join sma_allocation_days on sma_allocation_days.allocation_id = sma_shop_allocations.id 
    WHERE NOT EXISTS
    (SELECT *
    FROM   sma_sales
    WHERE  sma_shops.id = sma_sales.shop_id and sma_sales.date = CURRENT_DATE and sma_sales.created < '$current_date') 
   
    AND NOT EXISTS
    (SELECT *
    FROM   sma_tickets
    WHERE  sma_shops.id = sma_tickets.shop_id and sma_tickets.date = CURRENT_DATE and sma_tickets.created_at < '$current_date') and 
   sma_vehicles.id = $vehicle_id and sma_customers.active = 1 and sma_allocation_days.day = $day and (sma_allocation_days.duration > 0 or sma_allocation_days.start_point = 1) and sma_allocation_days.active = 1 and sma_vehicle_route.day = $day and sma_allocation_days.salesman_id = $vehicle_id and 
   sma_allocation_days.expiry IS NULL or sma_allocation_days.expiry <= CURRENT_TIMESTAMP GROUP BY sma_shops.id ORDER BY sma_allocation_days.position ASC");

   $result=$query->result();
$data["myroutes"] = $result;
  echo json_encode($result);
}

function add_route($id){
    $this->sma->checkPermissions('add-route',true,'vehicles');
   
    $this->form_validation->set_rules('route_id', $this->lang->line("Route"), 'required');
    $this->form_validation->set_rules('day', $this->lang->line("Day"), 'required');

    $routes = $this->routes_model->getAllRoutes();

    $vehicle = $this->vehicles_model->getVehicleByID($id);

    $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

    if ($this->form_validation->run('vehicles/add_route') == true) {
        $data = array(
            'distributor_id' => $distributor->id,
            'vehicle_id' => $id,
            'route_id' => $this->input->post('route_id'),
            'day' => $this->input->post('day'),
        );
    } elseif ($this->input->post('add_route')) {
        $this->session->set_flashdata('error', validation_errors());
        redirect('vehicles');
    }

    if ($this->form_validation->run() == true) {
        
        if($this->vehicles_model->checkVehicleRouteExists($distributor->id,
            $id,
            $this->input->post('route_id'),
            $this->input->post('day'))){
            $this->session->set_flashdata('message', $this->lang->line("Vehicle route already exists"));
        $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
        redirect('vehicles');
        }else{
            if($cid = $this->vehicles_model->addVehicleRoute($data)){
                $this->session->set_flashdata('message', $this->lang->line("Vehicle route added"));
        $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
        redirect('vehicles');
            }else{
                $this->session->set_flashdata('warning', $this->lang->line("Vehicle route not added"));
        $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
        redirect('vehicles'); 
            }
        }
        
    } else {
        $this->data['routes']=  $routes;
        $this->data['vehicle']=  $vehicle;
        $this->data['distributor']=  $distributor;
        $this->data['page_title'] = lang('add_route');
        $this->load->view($this->theme.'vehicles/add_route',$this->data);
    }

}

    function view_routes($id){
        $this->sma->checkPermissions('view-route',true,'vehicles');

        $vehicle = $this->vehicles_model->getVehicleByID($id);
        $vehicleroutes = $this->vehicles_model->getVehicleRoutes($id);
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->data['distributor']=  $distributor;
        $this->data['vehicleroutes']=$vehicleroutes;
        $this->data['vehicle']=  $vehicle;
        $this->data['page_title'] = lang('view_routes');
        $this->load->view($this->theme.'vehicles/view_routes',$this->data);
    }
    
    
    function edit_allocation($route_id,$day){
        //$this->sma->checkPermissions('view-route',true,'vehicles');

        if($day==1){
           $actual_day = "Monday"; 
        }else if($day==2){
           $actual_day = "Tuesday"; 
        }else if($day==3){
           $actual_day = "Wednesday"; 
        }else if($day==4){
           $actual_day = "Thursday"; 
        }else if($day==5){
           $actual_day = "Friday"; 
        }else if($day==6){
           $actual_day = "Saturday"; 
        }else if($day==7){
           $actual_day = "Sunday"; 
        }
        $route = $this->vehicles_model->getRouteByID($route_id);
        $allocations = $this->companies_model->getAllocationsByRouteDay($route_id,$day);
        $this->data['allocations']=  $allocations;
        $this->data['actual_day']=  $actual_day;
        $this->data['day']=  $day;
        $this->data['route']=  $route;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vehicles')));
        $meta = array('page_title' => lang('edit_allocations'), 'bc' => $bc);
        $this->page_construct('vehicles/edit_allocation', $meta, $this->data);
    }
    
    function update_allocation($route_id,$day){
        //$this->sma->checkPermissions('view-route',true,'vehicles');

        $this->form_validation->set_rules('allocation_ids[]', $this->lang->line("Allocations"), 'required');
        $this->form_validation->set_rules('shop_ids[]', $this->lang->line("Shops"), 'required');
        
        if ($this->input->post('allocation_ids')) {
            $allocation_ids = $this->input->post('allocation_ids');
        }
        
        if ($this->input->post('shop_ids')) {
            $shop_ids = $this->input->post('shop_ids');
        }
        
        if ($this->form_validation->run() == true) {
            //delete allocation and day
            foreach($allocation_ids as $allocation_id){
                $this->companies_model->deleteShopAllocation($allocation_id);
            }
            
            //then re insert allocation and day
            foreach($shop_ids as $shop_id){
                $data = array(
                    'shop_id' => $shop_id,
                    'route_id' => $route_id,
                );
                $id = $this->companies_model->addAllocation($data);
                $data2 = array(
                    'allocation_id' => $id,
                    'day' => $day,
                    'expiry' => null,
                );
                $this->companies_model->addAllocationDay($data2);
                $vehicle = $this->vehicles_model->getVehicleByRouteID($route_id,$day);
                if(!empty($vehicle)){
                $companies = $this->vehicles_model->getSalesmanID($vehicle->vehicle_id);
                $response = $this->routes_model->getVroomRoutes($companies->vehicle_id,$day,$companies->id);
                $vehicleroutes=json_decode($response,true);
                $update=$this->routes_model->updateDurationSet($vehicleroutes);
            }
            }
            $this->session->set_flashdata('message', $this->lang->line("allocation_order_edited_successfully"));
            redirect('vehicles');
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }
    }
    
    function view_stock($id){
        $this->sma->checkPermissions('view-stock',true,'vehicles');

        $vehicle = $this->vehicles_model->getVehicleByID($id);
        
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $vehiclestocks = $this->vehicles_model->getVehicleStock($id,$distributor->id);
        $this->data['distributor']=  $distributor;
        $this->data['vehiclestocks']=$vehiclestocks;
        $this->data['vehicle']=  $vehicle;
        $this->data['page_title'] = lang('view_stock');
        //print_r($this->data);
        //print_r($vehiclestock);
        $this->load->view($this->theme.'vehicles/view_stock',$this->data);
    }
    function view_vroomroutes($id){
        //$this->sma->checkPermissions('view-stock',true,'vehicles');  
        

        //$vehicle = $this->vehicles_model->getVehicleByID($id);
        $companies = $this->vehicles_model->getSalesmanID($id);

  /**$url="http://localhost:4000/vroom-php/endpoint.php?action=fetch_shops&vehicle_id=21&day=3&salesman_id=969";
   $curl = curl_init($url);
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //for debug only!
   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

   $resp = curl_exec($curl);
   curl_close($curl);
   //var_dump($resp); **/
   $response = $this->routes_model->getVroomRoutes($id,3,$companies->id);
   $vehicleroutes=json_decode($response,true);
        //$dateD=date('D', strtotime($date));
       // $day = $this->Routes_model->getDay($dateD);
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        //$vehicleroutes = json_decode($this->routes_model->getVroomRoutes(21,3,969));
        //$vehicleroutes=json_decode($vehicleroutesraw);
        
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
        $this->data['distributor']=  $distributor;
        $this->data['vehicleroutes']= $vehicleroutes;
        $this->data['vehicle']=  $vehicle;
        $this->data['page_title'] = lang('view_routes');
        
        //print_r($this->data);
        $this->load->view($this->theme.'vehicles/vehicle_routes',$this->data);
    }
    function updateAllRoutes(){
        //$this->sma->checkPermissions('view-stock',true,'vehicles');  
        
        $allvehicles = $this->vehicles_model->getAllVehicles();
        foreach($allvehicles as $allvehicle)
        {
        $companies = $this->vehicles_model->getSalesmanID($allvehicle->id);
        $days = $this->routes_model->getAllDays();
        foreach($days as $day)
        {
            $response = $this->routes_model->getVroomRoutes($allvehicle->id,$day->id,$companies->id);
            $vehicleroutes=json_decode($response,true);
        //$distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        
        foreach($vehicleroutes as $vehicleroute)
        {
            if(isset($vehicleroute['id']))
            {
                //$routeid=$this->vehicles_model->getRouteByVehicleIDandDay($allvehicle->id,$day->id);
                //$allocationid=$this->routes_model->getAllocationByShopId($vehicleroute['id'],$routeid->route_id);
            $datar = array(
                'duration' => $vehicleroute['duration'],
                'distance' => 0.00,
                'salesman_id' =>$vehicleroute['description'],
            );
            //$allocationday=$this->routes_model->getAllocationByDays($vehicleroute['id']);
            /**foreach($allocationday as $allocationday)
            {

                
                if($allocationday->day==$day->id)
                {**/
            $this->routes_model->updateDurationAll($vehicleroute['id'],$day->id, $datar);
                //}
            //echo $datar['duration'];
            //}
            
            }
         }
         //print_r($vehicleroutes);
    }
    }
    $this->session->set_flashdata('message', $this->lang->line("All routes updated successfully"));
        redirect('welcome');
    }
    function edit_vehicle_stock($id = NULL)
    {
        $this->sma->checkPermissions('edit-stock',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $vehicle = $this->vehicles_model->getVehicleByID($id);
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $vehicle_stocks = $this->vehicles_model->getVehicleStock($id,$distributor->id);
        
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['vehicle'] = $vehicle;
        $this->data['vehicle_stocks'] = $vehicle_stocks;
        $this->data['page_title'] = lang('edit_stock');
        $this->load->view($this->theme . 'vehicles/edit_stock', $this->data);
        
    }
    
    function update_vehicle_stock()
    {
        $this->sma->checkPermissions('edit-stock',true,'vehicles');

        $this->form_validation->set_rules('id', 'Vehicle Id', 'required');
        
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $vehicle = $this->vehicles_model->getVehicleByID($id);
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $vehicle_stocks = $this->vehicles_model->getVehicleStock($id,$distributor->id);


        if ($this->form_validation->run('vehicles/edit_vehicle_stock') == true) {
            
            

            $data = array();
            $product_ids = $this->input->post('product_ids');
            $quantitys = $this->input->post('quantitys');
            
            if(count($product_ids)<1){
                $this->session->set_flashdata('error', 'No stock specified');
                redirect('vehicles');
            }
            
            for ($i=0;$i<count($product_ids);$i++){
               array_push($data,array(
                   'distributor_id' => $distributor->id,
                   'vehicle_id' => $id,
                   'product_id' => $product_ids[$i],
                   'quantity' => $quantitys[$i])
               );
            }

        } elseif ($this->input->post('edit_vehicle_stock')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }

        if ($this->form_validation->run() == true && $rid = $this->vehicles_model->updateStock($id,$distributor->id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("vehicle_stock_updated"));
            redirect('vehicles');
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }
    }
    
    function edit_route($id){
        $this->sma->checkPermissions('edit-route',true,'vehicles');

        $this->form_validation->set_rules('route_id', $this->lang->line("Route"), 'required');
        $this->form_validation->set_rules('day', $this->lang->line("Day"), 'required');

        $routes = $this->routes_model->getAllRoutes();

        $vehicle_route = $this->vehicles_model->getVehicleRouteByID($id);

        $vehicle = $this->vehicles_model->getVehicleByID($vehicle_route->vehicle_id);

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

        if ($this->form_validation->run('customers/edit_route') == true) {
            $data = array(
                'distributor_id' => $distributor->id,
                'vehicle_id' => $vehicle->id,
                'route_id' => $this->input->post('route_id'),
                'day' => $this->input->post('day'),
            );
        } elseif ($this->input->post('edit_route')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('vehicles');
        }

        if ($this->form_validation->run() == true) {
            if($this->vehicles_model->checkVehicleRouteExists($distributor->id,
                $vehicle->id,
                $this->input->post('route_id'),
                $this->input->post('day'))){
            $this->session->set_flashdata('message', $this->lang->line("Route already exists"));
            redirect('vehicles');
            }else{
                $cid = $this->vehicles_model->updateVehicleRoute($id,$data);
            $this->session->set_flashdata('message', $this->lang->line("Route updated"));
            redirect('vehicles');
            }
            
        } else {
            $this->data['routes']=  $routes;
            $this->data['vehicle']=  $vehicle;
            $this->data['vehicle_route']=  $vehicle_route;
            $this->data['distributor']=  $distributor;
            $this->data['page_title'] = lang('edit_route');
            $this->load->view($this->theme.'vehicles/edit_route',$this->data);
        }

    }

    function delete($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->vehicles_model->deleteVehicle($id)) {

            echo $this->lang->line("vehicle_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('vehicle_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    function deactivate_allocation($id = NULL)
    {
        //$this->sma->checkPermissions('delete',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $today = date("Y-m-d");
        
        if ($this->vehicles_model->deactivateAllocation($id,$today)) {
            //die($id);
            
        $allocation=$this->routes_model->getAllocationsByID($id);
        $companies = $this->vehicles_model->getSalesmanID($allocation->vehicle_id);      
        $response = $this->routes_model->getVroomRoutes($allocation->vehicle_id,$allocation->day,$companies->id);
        $vehicleroutes=json_decode($response,true);
        //$distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        
        foreach($vehicleroutes as $vehicleroute)
        {
            if(isset($vehicleroute['id']))
            {
                //$routeid=$this->vehicles_model->getRouteByVehicleIDandDay($allvehicle->id,$day->id);
                //$allocationid=$this->routes_model->getAllocationByShopId($vehicleroute['id'],$routeid->route_id);
            $datar = array(
                'duration' => $vehicleroute['duration'],
                'distance' => 0.00,
                'salesman_id' =>$vehicleroute['description'],
            );
            //$allocationday=$this->routes_model->getAllocationByDays($vehicleroute['id']);
            /**foreach($allocationday as $allocationday)
            {

                
                if($allocationday->day==$day->id)
                {**/
            $this->routes_model->updateDurationAll($vehicleroute['id'],$allocation->day, $datar);
                //}
            //echo $datar['duration'];
            //}
            
            }
         }
         $duplicateallocations=$this->routes_model->getduplicateallocation($allocation->allocation_id,$allocation->day,$allocation->duration,$allocation->vehicle_id);
         
         if(!empty($duplicateallocations)){
            foreach($duplicateallocations as $duplicateallocation)
            {
            
            //deactivate the duplicate allocations
                $res=$this->vehicles_model->deactivateAllocation($duplicateallocation->id,$today);
                
                }
         }
         echo $this->lang->line("allocation_deactivated");
         //die($duplicateallocations);
        } else {
            $this->session->set_flashdata('warning', lang('allocation_not_deactivated'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    function activate_allocation($id = NULL)
    {
        //$this->sma->checkPermissions('delete',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->vehicles_model->activateAllocation($id)) {
    //die();
            //echo $this->lang->line("allocation_activated");
            
            $allocation=$this->routes_model->getAllocationsByID($id);
            $companies = $this->vehicles_model->getSalesmanID($allocation->vehicle_id); 
                 
            $response = $this->routes_model->getVroomRoutes($allocation->vehicle_id,$allocation->day,$companies->id);
           $vehicleroutes=json_decode($response,true);
           //die($response);
                //$distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
                
                foreach($vehicleroutes as $vehicleroute)
                {
                    if(isset($vehicleroute['id']))
                    {
                        //$routeid=$this->vehicles_model->getRouteByVehicleIDandDay($allvehicle->id,$day->id);
                        //$allocationid=$this->routes_model->getAllocationByShopId($vehicleroute['id'],$routeid->route_id);
                    $datar = array(
                        'duration' => $vehicleroute['duration'],
                        'distance' => 0.00,
                        'salesman_id' =>$vehicleroute['description'],
                    );
                    //$allocationday=$this->routes_model->getAllocationByDays($vehicleroute['id']);
                    /**foreach($allocationday as $allocationday)
                    {
        
                        
                        if($allocationday->day==$day->id)
                        {**/
                    $this->routes_model->updateDurationAll($vehicleroute['id'],$allocation->day, $datar);
                        //}
                    //echo $datar['duration'];
                    //}
                    
                    }
                 }
        $this->session->set_flashdata('message', $this->lang->line("allocation_activated"));
        redirect('vehicles/routeplanByDay');
        } else {
            $this->session->set_flashdata('warning', lang('allocation_not_activated'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }

    function make_start($id = NULL)
    {
        //$this->sma->checkPermissions('delete',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $alloc=$this->routes_model->getAllocationsByID($id);
        $res=$this->routes_model->getVehicleallocation($alloc->day,$alloc->vehicle_id);
        if ($res == FALSE) {
        if ($this->vehicles_model->makeStart($id)) {
    //die();
            //echo $this->lang->line("allocation_activated");
            
            $allocation=$this->routes_model->getAllocationsByID($id);
            $companies = $this->vehicles_model->getSalesmanID($allocation->vehicle_id); 
                 
            $response = $this->routes_model->getVroomRoutes($allocation->vehicle_id,$allocation->day,$companies->id);
            $vehicleroutes=json_decode($response,true);
           //die($response);
                //$distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
                
                foreach($vehicleroutes as $vehicleroute)
                {
                    if(isset($vehicleroute['id']))
                    {
                        //$routeid=$this->vehicles_model->getRouteByVehicleIDandDay($allvehicle->id,$day->id);
                        //$allocationid=$this->routes_model->getAllocationByShopId($vehicleroute['id'],$routeid->route_id);
                    $datar = array(
                        'duration' => $vehicleroute['duration'],
                        'distance' => 0.00,
                        'salesman_id' =>$vehicleroute['description'],
                    );
                    //$allocationday=$this->routes_model->getAllocationByDays($vehicleroute['id']);
                    /**foreach($allocationday as $allocationday)
                    {
        
                        
                        if($allocationday->day==$day->id)
                        {**/
                    $this->routes_model->updateDurationAll($vehicleroute['id'],$allocation->day, $datar);
                        //}
                    //echo $datar['duration'];
                    //}
                    
                    }
                 }
        echo $this->lang->line("Shop_made_start_reference");
        } else {
            $this->session->set_flashdata('warning', lang('unable_to_create_reference'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
        else {
            $this->session->set_flashdata('warning', lang('Failed_to_create_Remove_existing_start_points_for_that_route_first'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    function remove_start($id = NULL)
    {
        //$this->sma->checkPermissions('delete',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->vehicles_model->removeStart($id)) {
    //die();
            //echo $this->lang->line("allocation_activated");
            
            $allocation=$this->routes_model->getAllocationsByID($id);
            $companies = $this->vehicles_model->getSalesmanID($allocation->vehicle_id); 
                 
            $response = $this->routes_model->getVroomRoutes($allocation->vehicle_id,$allocation->day,$companies->id);
           $vehicleroutes=json_decode($response,true);
           //die($response);
                //$distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
                
                foreach($vehicleroutes as $vehicleroute)
                {
                    if(isset($vehicleroute['id']))
                    {
                        //$routeid=$this->vehicles_model->getRouteByVehicleIDandDay($allvehicle->id,$day->id);
                        //$allocationid=$this->routes_model->getAllocationByShopId($vehicleroute['id'],$routeid->route_id);
                    $datar = array(
                        'duration' => $vehicleroute['duration'],
                        'distance' => 0.00,
                        'salesman_id' =>$vehicleroute['description'],
                    );
                    //$allocationday=$this->routes_model->getAllocationByDays($vehicleroute['id']);
                    /**foreach($allocationday as $allocationday)
                    {
        
                        
                        if($allocationday->day==$day->id)
                        {**/
                    $this->routes_model->updateDurationAll($vehicleroute['id'],$allocation->day, $datar);
                        //}
                    //echo $datar['duration'];
                    //}
                    
                    }
                 }
        $this->session->set_flashdata('message', $this->lang->line("Shop_start_reference_removed"));
        redirect('vehicles/routeStartingPoints');
        } else {
            $this->session->set_flashdata('warning', lang('unable_to_remove_reference'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    function activate_tmwallocation($id = NULL)
    {
        //$this->sma->checkPermissions('delete',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->vehicles_model->activateAllocation($id)) {
    //die();
            //echo $this->lang->line("allocation_activated");
            
            $allocation=$this->routes_model->getAllocationsByID($id);
            $companies = $this->vehicles_model->getSalesmanID($allocation->vehicle_id); 
                 
            $response = $this->routes_model->getVroomRoutes($allocation->vehicle_id,$allocation->day,$companies->id);
           $vehicleroutes=json_decode($response,true);
           //die($response);
                //$distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
                
                foreach($vehicleroutes as $vehicleroute)
                {
                    if(isset($vehicleroute['id']))
                    {
                        //$routeid=$this->vehicles_model->getRouteByVehicleIDandDay($allvehicle->id,$day->id);
                        //$allocationid=$this->routes_model->getAllocationByShopId($vehicleroute['id'],$routeid->route_id);
                    $datar = array(
                        'duration' => $vehicleroute['duration'],
                        'distance' => 0.00,
                        'salesman_id' =>$vehicleroute['description'],
                    );
                    //$allocationday=$this->routes_model->getAllocationByDays($vehicleroute['id']);
                    /**foreach($allocationday as $allocationday)
                    {
        
                        
                        if($allocationday->day==$day->id)
                        {**/
                    $this->routes_model->updateDurationAll($vehicleroute['id'],$allocation->day, $datar);
                        //}
                    //echo $datar['duration'];
                    //}
                    
                    }
                 }
        $this->session->set_flashdata('message', $this->lang->line("allocation_activated"));
        redirect('vehicles/tomorrowRouteplan');
        } else {
            $this->session->set_flashdata('warning', lang('allocation_not_activated'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
    function delete_route($id = NULL)
    {
        $this->sma->checkPermissions('delete-route',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->vehicles_model->deleteVehicleRoute($id)) {
            $this->session->set_flashdata('message', $this->lang->line("route_deleted"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('vehicles');
        } else {
            $this->session->set_flashdata('warning', $this->lang->line("route_not_deleted"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('vehicles');
        }
    }
    
    function suggestions($term = NULL, $limit = NULL)
    {
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'vehicles');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->vehicles_model->getVehicleSuggestions($term, $limit,$distributor->id);
        echo json_encode($rows);
    }
     
function getVehicleSingleRoute()
{
    // <a class=\"tip\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1') . "'><i class=\"fa fa-eye\"></i></a> 
            
    $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
    $this->sma->checkPermissions('index',true,'vehicles');
    $this->load->library('datatables');
    $this->datatables
        ->select("sma_vehicles.id as id, sma_vehicles.plate_no, sma_vehicles.discount_enabled")
        ->from("sma_vehicles")
        ->where('sma_vehicles.distributor_id', $distributor->id)
        ->add_column("Actions", "
            <div class=\"btn-group dropdown\">
            <button  class=\"btn btn-secondary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
              Action
            </button>
            <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/1') . "'>Monday</a> </br>
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/2') . "'>Tuesday</a> </br>
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/3') . "'>Wednesday</a> </br>
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/4') . "'>Thursday</a> </br>
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/5') . "'>Friday</a> </br>
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/6') . "'>Saturday</a></br>
            <a class=\"dropdown-item\" title='" . $this->lang->line("edit_vehicle") . "' href='" . site_url('vehicles/getRoutesForVehicle/$1/7') . "'>Sunday</a>  
            </div>
          </div>", "id");
    //->unset_column('id');
    echo $this->datatables->generate();
}
function getRoutesForVehicle($id = NULL, $dayNo = NULL)
{
    //$vehicle = $this->vehicles_model->getVehicleByID($id);
    //echo json_encode($vehicle->id);

    $current_date = date("Y-m-d").' '.'23:59:00';
    $day = $dayNo;
    $vehicle_id = $id;
    $query=  $this->db->query("
    SELECT sma_customers.id as id,sma_allocation_days.id as allid, sma_customers.name, sma_customers.phone, sma_customers.active, sma_customers.email, sma_customers.customer_group_id, sma_customers.customer_group_name, sma_allocation_days.duration as durations,sma_allocation_days.position as positions,sma_shops.image as logo, sma_shops.shop_name, sma_shops.id as shop_id, sma_shops.lat, sma_shops.lng, sma_currencies.french_name as county_name, sma_cities.city as town_name,sma_cities.id as town_id
    FROM   sma_shops
				left join sma_customers on sma_customers.id = sma_shops.customer_id
                left join sma_cities on sma_cities.id = sma_customers.city
                left join sma_currencies on sma_currencies.id = sma_cities.county_id
                left join sma_shop_allocations on sma_shop_allocations.shop_id = sma_shops.id 
                left join sma_vehicle_route on sma_shop_allocations.route_id=sma_vehicle_route.route_id
                left join sma_vehicles on sma_vehicle_route.vehicle_id = sma_vehicles.id
                left join sma_routes on sma_vehicle_route.route_id = sma_routes.id 
                left join sma_allocation_days on sma_allocation_days.allocation_id = sma_shop_allocations.id 
    WHERE 
    sma_vehicles.id = $vehicle_id and sma_customers.active = 1 and sma_allocation_days.day = $day and sma_allocation_days.active = 1 and sma_vehicle_route.day = $day and 
    sma_allocation_days.expiry IS NULL or sma_allocation_days.expiry <= CURRENT_TIMESTAMP GROUP BY sma_shops.id ORDER BY sma_allocation_days.position ASC");

    $result=$query->result();
    $this->data["myroutes"] = $result;
    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('vehicle routes')));
    $meta = array('page_title' => "My routes", 'bc' => $bc);
    $this->page_construct('vehicles/test_test', $meta, $this->data);
}
function updatePosition()
{
    if(isset($_POST['update']))
        {
            foreach($_POST['positions'] as $position)
            {
                $index = $position[0];
                $newPosition = $position[1];
                $data = [
                    'position' => $newPosition,
                ];
                $this->db->where('id', $index);
                $this->db->update('sma_allocation_days', $data);
                echo 'record has successfully been updated';
            }

            exit("success");
            }
}


}
