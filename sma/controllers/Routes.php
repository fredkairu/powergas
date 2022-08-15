<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Routes extends MY_Controller
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
        $this->lang->load('routes', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('routes_model');
        $this->load->model('companies_model');
        $this->load->model('auth_model');
        $this->load->library('ion_auth');
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions('index',true,'routes');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('routes')));
        $meta = array('page_title' => lang('routes'), 'bc' => $bc);
        $this->page_construct('routes/index', $meta, $this->data);
    }

    function getRoutes()
    {
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->sma->checkPermissions('index',true,'routes');
        $this->load->library('datatables');
        $this->datatables
            ->select("id, name")
            ->from("routes")
            ->where('routes.distributor_id', $distributor->id)
            ->add_column("Actions", "<center>
                <a class=\"tip\" title='" . $this->lang->line("edit_route") . "' href='" . site_url('routes/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-pencil\"></i></a> 
                <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_route") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('routes/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }

    function add()
    {
        $this->sma->checkPermissions('add',true,'routes');

        $this->form_validation->set_rules('name', 'Name', 'required');

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

        if ($this->form_validation->run('routes/add') == true) {

            $data = array(
                'distributor_id' => $distributor->id,
                'name' => $this->input->post('name'),
            );

        } elseif ($this->input->post('add_route')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('routes');
        }

        if ($this->form_validation->run() == true && $rid = $this->routes_model->addRoute($data)) {
            $this->session->set_flashdata('message', $this->lang->line("route_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('routes');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('routes')));
            $meta = array('page_title' => lang('routes'), 'bc' => $bc);
            $this->page_construct('routes/add', $meta, $this->data);
        }
    }

    function edit($id = NULL)
    {
        $this->sma->checkPermissions('edit',true,'routes');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $route = $this->routes_model->getRouteByID($id);

        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run('routes/edit') == true) {

            $data = array(
                'distributor_id' => $route->distributor_id,
                'name' => $this->input->post('name'),
            );

        } elseif ($this->input->post('edit_route')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('routes');
        }

        if ($this->form_validation->run() == true && $rid = $this->routes_model->updateRoute($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("route_updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('routes');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['route'] = $route;
            $this->load->view($this->theme . 'routes/edit', $this->data);
        }
    }

    function delete($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'routes');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->routes_model->deleteRoute($id)) {

            echo $this->lang->line("route_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('route_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
}
