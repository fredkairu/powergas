<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Counties extends MY_Controller
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
        $this->lang->load('counties', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('counties_model');
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('counties')));
        $meta = array('page_title' => lang('counties'), 'bc' => $bc);
        $this->page_construct('counties/index', $meta, $this->data);
    }

    function getCounties()
    {
       $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id, french_name")
            ->from("currencies")
            ->add_column("Actions", "<center>
                <a class=\"tip\" title='" . $this->lang->line("edit_counties") . "' href='" . site_url('counties/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-pencil\"></i></a> 
                <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_counties") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('counties/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();

    }

    function add()
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run('counties/add') == true) {

            $data = array(
                'country' => 1,
                'french_name' => $this->input->post('name'),
                'portuguese_name' => $this->input->post('name'),
            );

        } elseif ($this->input->post('add_county')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('counties');
        }

        if ($this->form_validation->run() == true && $rid = $this->counties_model->addCounty($data)) {
            $this->session->set_flashdata('message', $this->lang->line("county_added"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('counties');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('counties')));
            $meta = array('page_title' => lang('counties'), 'bc' => $bc);
            $this->page_construct('counties/add', $meta, $this->data);
        }
    }

    function edit($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $county = $this->counties_model->getCountyByID($id);

        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run('counties/edit') == true) {

            $data = array(
                'country' => 1,
                'french_name' => $this->input->post('name'),
                'portuguese_name' => $this->input->post('name'),
            );

        } elseif ($this->input->post('edit_county')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('counties');
        }

        if ($this->form_validation->run() == true && $rid = $this->counties_model->updateCounty($id,$data)) {
            $this->session->set_flashdata('message', $this->lang->line("county_updated"));
            $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            redirect('counties');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['county'] = $county;
            $this->load->view($this->theme . 'counties/edit', $this->data);
        }
    }

    function delete($id = NULL)
    {
        $this->sma->checkPermissions('delete',true,'counties');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->counties_model->deleteCounty($id)) {

            echo $this->lang->line("county_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('county_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 0);</script>");
        }
    }
}
