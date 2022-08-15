<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Towns extends MY_Controller
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
        $this->lang->load('towns', $this->Settings->language);
        $this->lang->load('counties', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('towns_model');
        $this->load->model('counties_model');
    }

    function index($action = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('towns')));
        $meta = array('page_title' => lang('towns'), 'bc' => $bc);
        $this->page_construct('towns/index', $meta, $this->data);
    }

    function getTowns()
    {
       //$this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("cities.id as id, cities.city, currencies.french_name")
            ->from("cities")
            ->join('currencies', 'cities.county_id = currencies.id')
            ->add_column("Actions", "<center>
                <a class=\"tip\" title='" . $this->lang->line("edit_town") . "' href='" . site_url('towns/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-pencil\"></i></a> 
                <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_town") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('towns/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }

    function add()
    {
        //$this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('city', 'Town', 'required');
        $this->form_validation->set_rules('county_id', 'County', 'required');

        $counties = $this->counties_model->getAllCounties();

        if ($this->form_validation->run('towns/add') == true) {

            $data = array(
                'city' => $this->input->post('city'),
                'county_id' => $this->input->post('county_id'),
            );

        } elseif ($this->input->post('add_town')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('towns');
        }

        if ($this->form_validation->run() == true && $rid = $this->towns_model->addTown($data)) {
            $this->session->set_flashdata('message', $this->lang->line("town_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('towns');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['counties'] = $counties;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('towns')));
            $meta = array('page_title' => lang('towns'), 'bc' => $bc);
            $this->page_construct('towns/add', $meta, $this->data);
        }
    }

    function edit($id = NULL)
    {
        //$this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $town = $this->towns_model->getTownByID($id);

        $counties = $this->counties_model->getAllCounties();

        $this->form_validation->set_rules('city', 'Town', 'required');
        $this->form_validation->set_rules('county_id', 'County', 'required');

        if ($this->form_validation->run('towns/edit') == true) {

            $data = array(
                'city' => $this->input->post('city'),
                'county_id' => $this->input->post('county_id'),
            );

        } elseif ($this->input->post('edit_town')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('towns');
        }

        if ($this->form_validation->run() == true && $rid = $this->towns_model->updateTown($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("town_updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('towns');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['town'] = $town;
            $this->data['counties'] = $counties;
            $this->load->view($this->theme . 'towns/edit', $this->data);
        }
    }

    function delete($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'towns');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->towns_model->deleteTown($id)) {

            echo $this->lang->line("town_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('town_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
}
