<?php defined('BASEPATH') OR exit('No direct script access allowed');

class budgets extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }

        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('welcome');
        }
        $this->lang->load('settings', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('settings_model');
        $this->load->model('products_model');
        $this->load->model('companies_model');
        $this->load->model('budget_model');
        $this->load->model('sales_model');
         $this->digital_upload_path = 'files/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '10000';
    }

    function index()
    {

      

            $this->data['error'] = validation_errors();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
           
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('budgets')));
            $meta = array('page_title' => lang('budgets'), 'bc' => $bc);
            $this->page_construct('budgets/index', $meta, $this->data);
        
    }
    
    
    
        function country_sso_budget()
    {

      

            $this->data['error'] = validation_errors();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
           
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('budgets')));
            $meta = array('page_title' => lang('budgets'), 'bc' => $bc);
            $this->page_construct('budgets/country_sso_budget', $meta, $this->data);
        
    }
    
    
          function country_pso_budget()
    {

      

            $this->data['error'] = validation_errors();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
           
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('budgets')));
            $meta = array('page_title' => lang('budgets'), 'bc' => $bc);
            $this->page_construct('budgets/country_pso_budget', $meta, $this->data);
        
    }
    
    
    
              function customer_sso_budget()
    {

      

            $this->data['error'] = validation_errors();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
           
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('budgets')));
            $meta = array('page_title' => lang('budgets'), 'bc' => $bc);
            $this->page_construct('budgets/customer_sso_budget', $meta, $this->data);
        
    }
    
    

    function paypal()
    {

        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {

            $data = array('active' => $this->input->post('active'),
                'account_email' => $this->input->post('account_email'),
                'fixed_charges' => $this->input->post('fixed_charges'),
                'extra_charges_my' => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other')
            );
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePaypal($data)) {
            $this->session->set_flashdata('message', $this->lang->line('paypal_setting_updated'));
            redirect("system_settings/paypal");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['paypal'] = $this->settings_model->getPaypalSettings();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('paypal_settings')));
            $meta = array('page_title' => lang('paypal_settings'), 'bc' => $bc);
            $this->page_construct('settings/paypal', $meta, $this->data);
        }
    }

    function skrill()
    {

        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {

            $data = array('active' => $this->input->post('active'),
                'account_email' => $this->input->post('account_email'),
                'fixed_charges' => $this->input->post('fixed_charges'),
                'extra_charges_my' => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other')
            );
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSkrill($data)) {
            $this->session->set_flashdata('message', $this->lang->line('skrill_setting_updated'));
            redirect("system_settings/skrill");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['skrill'] = $this->settings_model->getSkrillSettings();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('skrill_settings')));
            $meta = array('page_title' => lang('skrill_settings'), 'bc' => $bc);
            $this->page_construct('settings/skrill', $meta, $this->data);
        }
    }

    function change_logo()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('site_logo', lang("site_logo"), 'xss_clean');
        $this->form_validation->set_rules('biller_logo', lang("biller_logo"), 'xss_clean');
        if ($this->form_validation->run() == true) {

            if ($_FILES['site_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = 300;
                $config['max_height'] = 80;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('site_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;

                $this->db->update('settings', array('logo2' => $photo), array('setting_id' => 1));

                $this->session->set_flashdata('message', lang('logo_uploaded'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($_FILES['biller_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = 300;
                $config['max_height'] = 80;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('biller_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;

                $this->session->set_flashdata('message', lang('logo_uploaded'));
                redirect($_SERVER["HTTP_REFERER"]);

            }

            $this->session->set_flashdata('error', lang('attempt_failed'));
            redirect($_SERVER["HTTP_REFERER"]);
            die();
        } elseif ($this->input->post('upload_logo')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/change_logo', $this->data);
        }
    }

    public function write_index($timezone)
    {

        $template_path = './assets/config_dumps/index.php';
        $output_path = SELF;
        $index_file = file_get_contents($template_path);
        $new = str_replace("%TIMEZONE%", $timezone, $index_file);
        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new)) {
                @chmod($output_path, 0644);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->form_validation->set_rules('purchase_code', lang("purchase_code"), 'required');
        $this->form_validation->set_rules('envato_username', lang("envato_username"), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('settings', array('purchase_code' => $this->input->post('purchase_code', TRUE), 'envato_username' => $this->input->post('envato_username', TRUE)), array('setting_id' => 1));
            redirect('system_settings/updates');
        } else {
            $fields = array('version' => $this->Settings->version, 'code' => $this->Settings->purchase_code, 'username' => $this->Settings->envato_username, 'site' => base_url());
            $this->load->helper('update');
            $protocol = is_https() ? 'https://' : 'http://';
            $updates = get_remote_contents($protocol.'tecdiary.com/api/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('updates')));
            $meta = array('page_title' => lang('updates'), 'bc' => $bc);
            $this->page_construct('settings/updates', $meta, $this->data);
        }
    }

    function install_update($file, $m_version, $version)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->sma->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                redirect("system_settings/updates");
            }
        }
        $this->db->update('settings', array('version' => $version, 'update' => 0), array('setting_id' => 1));
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        redirect("system_settings/updates");
    }

    function backups()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->data['files'] = glob('./files/backups/*.zip', GLOB_BRACE);
        $this->data['dbs'] = glob('./files/backups/*.txt', GLOB_BRACE);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('backups')));
        $meta = array('page_title' => lang('backups'), 'bc' => $bc);
        $this->page_construct('settings/backups', $meta, $this->data);
    }

    function backup_database()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->dbutil();
        $prefs = array(
            'format' => 'txt',
            'filename' => 'sma_db_backup.sql'
        );
        $back = $this->dbutil->backup($prefs);
        $backup =& $back;
        $db_name = 'db-backup-on-' . date("Y-m-d-H-i-s") . '.txt';
        $save = './files/backups/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->session->set_flashdata('messgae', lang('db_saved'));
        redirect("system_settings/backups");
    }

    function backup_files()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $name = 'file-backup-' . date("Y-m-d-H-i-s");
        $this->sma->zip("./", './files/backups/', $name);
        $this->session->set_flashdata('messgae', lang('backup_saved'));
        redirect("system_settings/backups");
        exit();
    }

    function restore_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $file = file_get_contents('./files/backups/' . $dbfile . '.txt');
        $this->db->conn_id->multi_query($file);
        $this->db->conn_id->close();
        redirect('logout/db');
    }

    function download_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->library('zip');
        $this->zip->read_file('./files/backups/' . $dbfile . '.txt');
        $name = 'db_backup_' . date('Y_m_d_H_i_s') . '.zip';
        $this->zip->download($name);
        exit();
    }

    function download_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->helper('download');
        force_download('./files/backups/' . $zipfile . '.zip', NULL);
        exit();
    }

    function restore_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $file = './files/backups/' . $zipfile . '.zip';
        $this->sma->unzip($file, './');
        $this->session->set_flashdata('success', lang('files_restored'));
        redirect("system_settings/backups");
        exit();
    }

    function delete_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        unlink('./files/backups/' . $dbfile . '.txt');
        $this->session->set_flashdata('messgae', lang('db_deleted'));
        redirect("system_settings/backups");
    }

    function delete_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        unlink('./files/backups/' . $zipfile . '.zip');
        $this->session->set_flashdata('messgae', lang('backup_deleted'));
        redirect("system_settings/backups");
    }

    function email_templates($template = "credentials")
    {

        $this->form_validation->set_rules('mail_body', lang('mail_message'), 'trim|required');
        $this->load->helper('file');
        $temp_path = is_dir('./themes/' . $this->theme . '/views/email_templates/');
        $theme = $temp_path ? $this->theme : 'default';
        if ($this->form_validation->run() == true) {
            $data = $_POST["mail_body"];
            if (write_file('./themes/' . $theme . '/views/email_templates/' . $template . '.html', $data)) {
                $this->session->set_flashdata('message', lang('message_successfully_saved'));
                redirect('system_settings/email_templates#' . $template);
            } else {
                $this->session->set_flashdata('error', lang('failed_to_save_message'));
                redirect('system_settings/email_templates#' . $template);
            }
        } else {

            $this->data['credentials'] = file_get_contents('./themes/' . $theme . '/views/email_templates/credentials.html');
            $this->data['sale'] = file_get_contents('./themes/' . $theme . '/views/email_templates/sale.html');
            $this->data['quote'] = file_get_contents('./themes/' . $theme . '/views/email_templates/quote.html');
            $this->data['purchase'] = file_get_contents('./themes/' . $theme . '/views/email_templates/purchase.html');
            $this->data['transfer'] = file_get_contents('./themes/' . $theme . '/views/email_templates/transfer.html');
            $this->data['payment'] = file_get_contents('./themes/' . $theme . '/views/email_templates/payment.html');
            $this->data['forgot_password'] = file_get_contents('./themes/' . $theme . '/views/email_templates/forgot_password.html');
            $this->data['activate_email'] = file_get_contents('./themes/' . $theme . '/views/email_templates/activate_email.html');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('email_templates')));
            $meta = array('page_title' => lang('email_templates'), 'bc' => $bc);
            $this->page_construct('settings/email_templates', $meta, $this->data);
        }
    }

    function create_group()
    {

        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash');
        //$this->form_validation->set_rules('description', lang('group_description'), 'xss_clean');

        if ($this->form_validation->run() == TRUE) {
            $data = array('name' => strtolower($this->input->post('group_name')), 'description' => $this->input->post('description'));
            $new_group_id = $this->settings_model->addGroup($data);
            if ($new_group_id) {
                $this->session->set_flashdata('message', lang('group_added'));
                redirect("system_settings/permissions/" . $new_group_id);
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group_name'] = array(
                'name' => 'group_name',
                'id' => 'group_name',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name'),
            );
            $this->data['description'] = array(
                'name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('description'),
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/create_group', $this->data);
        }
    }

    function edit_group($id)
    {

        if (!$id || empty($id)) {
            redirect('system_settings/user_groups');
        }

        $group = $this->settings_model->getGroupByID($id);

        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash');

        if ($this->form_validation->run() === TRUE) {
            $data = array('name' => strtolower($this->input->post('group_name')), 'description' => $this->input->post('description'));
            $group_update = $this->settings_model->updateGroup($id, $data);

            if ($group_update) {
                $this->session->set_flashdata('message', lang('group_udpated'));
            } else {
                $this->session->set_flashdata('error', lang('attempt_failed'));
            }
            redirect("system_settings/user_groups");
        } else {


            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group'] = $group;

            $this->data['group_name'] = array(
                'name' => 'group_name',
                'id' => 'group_name',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name', $group->name),
            );
            $this->data['group_description'] = array(
                'name' => 'group_description',
                'id' => 'group_description',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_description', $group->description),
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_group', $this->data);
        }
    }

    function permissions($id = NULL)
    {

        $this->form_validation->set_rules('group', lang("group"), 'is_natural_no_zero');
        if ($this->form_validation->run() == true) {

            $data = array(
                'products-index' => $this->input->post('products-index'),
                'products-edit' => $this->input->post('products-edit'),
                'products-add' => $this->input->post('products-add'),
                'products-delete' => $this->input->post('products-delete'),
                'products-cost' => $this->input->post('products-cost'),
                'products-price' => $this->input->post('products-price'),
                'customers-index' => $this->input->post('customers-index'),
                'customers-edit' => $this->input->post('customers-edit'),
                'customers-add' => $this->input->post('customers-add'),
                'customers-delete' => $this->input->post('customers-delete'),
                'suppliers-index' => $this->input->post('suppliers-index'),
                'suppliers-edit' => $this->input->post('suppliers-edit'),
                'suppliers-add' => $this->input->post('suppliers-add'),
                'suppliers-delete' => $this->input->post('suppliers-delete'),
                'sales-index' => $this->input->post('sales-index'),
                'sales-edit' => $this->input->post('sales-edit'),
                'sales-add' => $this->input->post('sales-add'),
                'sales-delete' => $this->input->post('sales-delete'),
                'sales-email' => $this->input->post('sales-email'),
                'sales-pdf' => $this->input->post('sales-pdf'),
                'sales-deliveries' => $this->input->post('sales-deliveries'),
                'sales-edit_delivery' => $this->input->post('sales-edit_delivery'),
                'sales-add_delivery' => $this->input->post('sales-add_delivery'),
                'sales-delete_delivery' => $this->input->post('sales-delete_delivery'),
                'sales-email_delivery' => $this->input->post('sales-email_delivery'),
                'sales-pdf_delivery' => $this->input->post('sales-pdf_delivery'),
                'sales-gift_cards' => $this->input->post('sales-gift_cards'),
                'sales-edit_gift_card' => $this->input->post('sales-edit_gift_card'),
                'sales-add_gift_card' => $this->input->post('sales-add_gift_card'),
                'sales-delete_gift_card' => $this->input->post('sales-delete_gift_card'),
                'quotes-index' => $this->input->post('quotes-index'),
                'quotes-edit' => $this->input->post('quotes-edit'),
                'quotes-add' => $this->input->post('quotes-add'),
                'quotes-delete' => $this->input->post('quotes-delete'),
                'quotes-email' => $this->input->post('quotes-email'),
                'quotes-pdf' => $this->input->post('quotes-pdf'),
                'purchases-index' => $this->input->post('purchases-index'),
                'purchases-edit' => $this->input->post('purchases-edit'),
                'purchases-add' => $this->input->post('purchases-add'),
                'purchases-delete' => $this->input->post('purchases-delete'),
                'purchases-email' => $this->input->post('purchases-email'),
                'purchases-pdf' => $this->input->post('purchases-pdf'),
                'transfers-index' => $this->input->post('transfers-index'),
                'transfers-edit' => $this->input->post('transfers-edit'),
                'transfers-add' => $this->input->post('transfers-add'),
                'transfers-delete' => $this->input->post('transfers-delete'),
                'transfers-email' => $this->input->post('transfers-email'),
                'transfers-pdf' => $this->input->post('transfers-pdf'),
                'sales-return_sales' => $this->input->post('sales-return_sales'),
                'reports-quantity_alerts' => $this->input->post('reports-quantity_alerts'),
                'reports-expiry_alerts' => $this->input->post('reports-expiry_alerts'),
                'reports-products' => $this->input->post('reports-products'),
                'reports-daily_sales' => $this->input->post('reports-daily_sales'),
                'reports-monthly_sales' => $this->input->post('reports-monthly_sales'),
                'reports-payments' => $this->input->post('reports-payments'),
                'reports-purchases' => $this->input->post('reports-purchases'),
                'reports-customers' => $this->input->post('reports-customers'),
                'reports-suppliers' => $this->input->post('reports-suppliers'),
                'sales-payments' => $this->input->post('sales-payments'),
                'purchases-payments' => $this->input->post('purchases-payments'),
                'purchases-expenses' => $this->input->post('purchases-expenses'),
            );

            if (POS) {
                $data['pos-index'] = $this->input->post('pos-index');
            }

            //$this->sma->print_arrays($data);
        }


        if ($this->form_validation->run() == true && $this->settings_model->updatePermissions($id, $data)) {
            $this->session->set_flashdata('message', lang("group_permissions_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['id'] = $id;
            $this->data['p'] = $this->settings_model->getGroupPermissions($id);
            $this->data['group'] = $this->settings_model->getGroupByID($id);

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('group_permissions')));
            $meta = array('page_title' => lang('group_permissions'), 'bc' => $bc);
            $this->page_construct('settings/permissions', $meta, $this->data);
        }
    }

    function user_groups()
    {

        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect('auth');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['groups'] = $this->settings_model->getGroups();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('groups')));
        $meta = array('page_title' => lang('groups'), 'bc' => $bc);
        $this->page_construct('settings/user_groups', $meta, $this->data);
    }

    function delete_group($id = NULL)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect('welcome', 'refresh');
        }

        if ($this->settings_model->checkGroupUsers($id)) {
            $this->session->set_flashdata('error', lang("group_x_b_deleted"));
            redirect("system_settings/user_groups");
        }

        if ($this->settings_model->deleteGroup($id)) {
            $this->session->set_flashdata('message', lang("group_deleted"));
            redirect("system_settings/user_groups");
        }
    }

    function currencies()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('currencies')));
        $meta = array('page_title' => lang('Country_currencies'), 'bc' => $bc);
        $this->page_construct('settings/currencies', $meta, $this->data);
    }
    
    
    function country_prices($id)
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('currencies')));
        $meta = array('page_title' => lang('Country_currencies'), 'bc' => $bc);
        $this->page_construct('settings/currencies', $meta, $this->data);
    }

    function getCurrencies()
    {
        

    $productslink="<a href='" . site_url('system_settings/country_pricing/$1') . "' target='_blank' class='tip' title='" . lang("country_product_pricing") . "'><i class=\"fa fa-search\"></i></a> ";

        $this->load->library('datatables');
        $id=  $this->input->get("id");
       // die($id."dsds");
        if($id){
           
            $this->datatables
                     ->select("currencies.id as id,cluster.name as cluster,currencies.country,currencies.french_name,currencies.portuguese_name,currencies.code,currencies.name,currencies.rate")
            ->from("currencies")
                  
                ->join('cluster', 'cluster.id =currencies.cluster', 'left')
          ->where("currencies.cluster=$id")
            ->add_column("Actions", "<center>".$productslink."<a href='" . site_url('system_settings/product_pricing/$1') . "' class='tip' title='" . lang("product_pricing") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-upload\"></i></a> <a href='" . site_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");   
            
        }
        else{
        $this->datatables
            ->select("currencies.id as id,cluster.name as cluster,currencies.country,currencies.french_name,currencies.portuguese_name,currencies.code,currencies.name,currencies.rate")
            ->from("currencies")
                ->join('cluster', 'cluster.id =currencies.cluster', 'left')
        
            ->add_column("Actions", "<center>".$productslink."<a href='" . site_url('system_settings/product_pricing/$1') . "' class='tip' title='" . lang("product_pricing") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-upload\"></i></a> <a href='" . site_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        }
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function country_pricing_csv(){
        $country=$this->input->get("country");
        $currencyid=$this->input->get("currency_id");   
        $rand=  rand(10000,1000000);
        $file=$country."PRICES".$rand;
        $csvfile="./assets/csv/".$file.".csv";
        $this->load->model('products_model');
  //       header('Content-Type: text/csv');
//header('Content-Disposition: attachment; filename="'.$file.'.csv"');
if($currencyid){
    $countrydetail=$this->settings_model->getCurrencyByID($currencyid);
$user_CSV[0] = array('country','product_gmid', 'unified_price', 'resale_price','tender_price','supply_price','promotion');
$allproducts=  $this->products_model->getAllProducts();
$i=1;
// very simple to increment with i++ if looping through a database result 
        foreach ($allproducts as $pr) {
            $user_CSV[$i] = array($countrydetail->country,$pr->code,0,0,0,0,'non-promoted');
            $i++;
        }

  $fp = fopen($csvfile, 'w') or die("Unable to open file!");
foreach ($user_CSV as $line) {
    // though CSV stands for "comma separated value"
    // in many countries (including France) separator is ";"
    fputcsv($fp, $line, ',');
}
}
else{
   $allcountries=  $this->settings_model->getAllCurrencies();
   $allproducts=  $this->products_model->getAllProducts();
$i=1;
   foreach ($allcountries as $value) {
     $countrydetaill=$this->settings_model->getCurrencyByID($value->id);
$user_CSV[0] = array('country','product_gmid', 'unified_price', 'resale_price','tender_price','supply_price','promotion');

// very simple to increment with i++ if looping through a database result 
        foreach ($allproducts as $pr) {
            $user_CSV[$i] = array($countrydetaill->country,$pr->code,0,0,0,0,'non-promoted');
           $i++; 
        }

  $fp = fopen($csvfile, 'w') or die("Unable to open file!");
foreach ($user_CSV as $line) {
    // though CSV stands for "comma separated value"
    // in many countries (including France) separator is ";"
    fputcsv($fp, $line, ',');
}   
   }
    
}

fclose($fp);
     echo base_url()."assets/csv/".$file.".csv" ; 
    }
    
    function add_currency()
    {

        $this->form_validation->set_rules('code', lang("currency_code"), 'trim|required');
        $this->form_validation->set_rules('cluster', lang("cluster"), 'trim|required');
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('rate', lang("exchange_rate"), 'required|numeric');
        $this->form_validation->set_rules('country', lang("country"), 'trim|required'); //is_unique[currencies.country]

        if ($this->form_validation->run() == true) {
            $data = array('cluster'=>$this->input->post('cluster'),
                'country'=>$this->input->post('country'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                 'french_name' => $this->input->post('french_name'),
                'portuguese_name' => $this->input->post('portuguese_name'),
                'rate' => $this->input->post('rate'),
            );
        } elseif ($this->input->post('add_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/currencies");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCurrency($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang("currency_added"));
            redirect("system_settings/currencies");
        } else {
            $this->load->model('cluster_model');
             $this->data['clusters']=  $this->cluster_model->getClusters();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['page_title'] = lang("new_currency");
            $this->load->view($this->theme . 'settings/add_currency', $this->data);
        }
    }
    
    
     function product_pricing($id)
    {

         
            $this->load->model('cluster_model');
            $this->load->model('country_productpricing_model');
            $productsuploaded=  $this->country_productpricing_model->getCountryProducts($id);
           // die(print_r($productsuploaded));
            if(is_array($productsuploaded)){
               $this->data['notice']="Some product prices have already been uploaded,do you wish to overwrite?"; 
            }else{
                $this->data['notice']="";
            }
            
            $this->data['modal_js'] = $this->site->modal_js();
             $currency=  $this->settings_model->getCurrencyByID($id);
             
             $this->data['country_name']=$currency->country;
             $this->data['currency_id']=$currency->id;
            $this->data['page_title'] = lang("import_product_prices_for_".$currency->country);
            $this->load->view($this->theme . 'settings/import_product', $this->data);
        
    }
    
    function country_pricing($id)
    {         
            $this->load->model('cluster_model');
            $this->load->model('country_productpricing_model');
            $productsuploaded=  $this->country_productpricing_model->getCountryProducts($id);
           // die(print_r($productsuploaded));
            if(is_array($productsuploaded)){
               $this->data['notice']="Some product prices have already been uploaded,do you wish to overwrite?"; 
            }else{
                $this->data['notice']="";
            }
            
            $this->data['modal_js'] = $this->site->modal_js();
             $currency=  $this->settings_model->getCurrencyByID($id);
             $this->data['products']=$productsuploaded;
             $this->data['country_name']=$currency->country;
             $this->data['currency_id']=$currency->id;
            
             $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('country_pricing')));
        $meta = array('page_title' => lang("Product_prices_for_".$currency->country), 'bc' => $bc);
            $this->page_construct('settings/country_prices', $meta, $this->data);
            
        
    }
    
       function prices_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
        $this->load->model('country_productpricing_model');
         $this->load->model('products_model');
//die(print_r($_POST));
        if ($this->form_validation->run() == true) {
$countryid=$this->input->post('currency_id');
$country=$this->input->post('country');
$fromdatee=$this->input->post('fromdate');
$todatee=$this->input->post('todate');


            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("system_settings/currencies");
                }

                $csv = $this->upload->file_name;
                
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

                $keys = array('country','product_gmid','unified_price','resell_price','tender_price','supply_price','promotion');

                $final = array();

                foreach ($arrResult as $key => $value) {
                 $final[] = array_combine($keys, $value);
                }
               //$this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    
                    $trimmedname=  str_replace(" ","",trim($csv_pr['product_gmid']));
                    $product=$this->products_model->getProductByCode($trimmedname);
                    if (!$product){
                        $this->session->set_flashdata('error',"Check product" . " (" .$csv_pr['product_gmid'] . ") " . "doesnt exist in database" . " " . lang("line_no") . " " . $rw);
                        redirect("system_settings/currencies");
                    }
                    
                    $countrydet=  $this->settings_model->getCountryByName(trim($csv_pr['country']));
                      if (!$countrydet){
                        $this->session->set_flashdata('error',"Check country" . " (" .$csv_pr['cuntry'] . ") " . "doesnt exist in database" . " " . lang("line_no") . " " . $rw);
                        redirect("system_settings/currencies");
                    }
                        
                        $product_name[] = trim($product->name);
                        $product_ids[]=trim($product->id);
                        $countryids[]=$countrydet->id;
                        $unifiedprice[]= trim($csv_pr['unified_price']);
                        $resellprice[]= trim($csv_pr['resell_price']);
                        $tenderprice[]= trim($csv_pr['tender_price']);
                        $supplyprice[]=trim($csv_pr['supply_price']);
                        $promotion[]=trim($csv_pr['promotion']);
                        $fromdate[]=$fromdatee;
                        $todate[]=$todatee;
                       
                       
                    $rw++;
                }
            }

           $ikeys = array('country_id', 'product_id','product_name','unified_price','resell_price','tender_price','supply_price','promotion','from_date','to_date');

            $items = array();
            foreach (array_map(null,$countryids, $product_ids,$product_name, $unifiedprice,$resellprice,$tenderprice,$supplyprice,$promotion,$fromdate,$todate) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

        // $this->sma->print_arrays($items);
            
             if ($this->country_productpricing_model->addProductPricing($items)) {
            $this->session->set_flashdata('message', lang($country."_product_prices_imported"));
            redirect('system_settings/currencies');
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
            $this->page_construct('budgets/import_budget', $meta, $this->data); //redirect("system_settings/import_currency");

        }
        }

       
    }
    function import_currency()
    {

        //$this->form_validation->set_rules('file', lang("category_image"), 'xss_clean');
        $this->form_validation->set_rules('file', lang("file"), 'required');
       

        if ($this->form_validation->run() == true) {
           
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
          
                $config = NULL;
            } else {
                $photo = NULL;
            }
        } elseif ($this->input->post('import_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/currencies");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCurrency($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang("currency_added"));
            redirect("system_settings/currencies");
        } else {
            $this->load->model('cluster_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
             $this->data['clusters']=  $this->cluster_model->getClusters();
            $this->data['page_title'] = lang("new_country_currency ");
            $this->load->view($this->theme . 'settings/import_currency', $this->data);
        }
    }

    function edit_currency($id = NULL)
    {

        $this->form_validation->set_rules('code', lang("currency_code"), 'trim|required');
        $this->form_validation->set_rules('cluster', lang("cluster"), 'trim|required');
        $cur_details = $this->settings_model->getCurrencyByID($id);
        $this->load->model('cluster_model');
        if ($this->input->post('code') != $cur_details->code) {
            $this->form_validation->set_rules('code', lang("currency_code"), 'trim|required');
        }
         $this->form_validation->set_rules('country', lang("country"), 'trim|required'); //is_unique[currencies.country]
        $this->form_validation->set_rules('name', lang("currency_name"), 'required');
        $this->form_validation->set_rules('rate', lang("exchange_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array(
                'cluster'=>$this->input->post('cluster'),
                'country'=>$this->input->post('country'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                   'french_name' => $this->input->post('french_name'),
                'portuguese_name' => $this->input->post('portuguese_name'),
                'rate' => $this->input->post('rate'),
            );
           // die(print_r($data));
        } elseif ($this->input->post('edit_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/currencies");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCurrency($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("currency_updated"));
            redirect("system_settings/currencies");
        } else {
            $this->load->model('cluster_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['clusters']=  $this->cluster_model->getClusters();
            $this->data['currency'] = $this->settings_model->getCurrencyByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_currency', $this->data);
        }
    }

    function delete_currency($id = NULL)
    {

        if ($this->settings_model->deleteCurrency($id)) {
            echo lang("currency_deleted");
        }
    
    }   
          function edit_cluster($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("cluster_name"), 'trim|required');
       if(!$id){
           $id=$this->input->post('id');
       }
       
        $cur_details = $this->settings_model->getClusterByID($id);
        $this->load->model('cluster_model');
        
        if ($this->form_validation->run() == true) {

            $data = array(
                'name'=>$this->input->post('name'));
             $id=$this->input->post('id');
           
             
           // die(print_r($data));
        } elseif ($this->input->post('edit_cluster')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/clusters");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCluster($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("cluster_updated"));
            redirect("system_settings/clusters");
        } else {
            $this->load->model('cluster_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['clusters']=  $this->cluster_model->getClusters();
            $this->data['cluster'] = $this->settings_model->getClusterByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_cluster', $this->data);
        }
    }
        
    function cluster_countries($id){
        $this->data['cluster']=$id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings/clusters'), 'page' => lang('clusters')), array('link' => '#', 'page' => lang('cluster_countries')));
            $meta = array('page_title' => lang('Cluster_countries'), 'bc' => $bc);
            $this->page_construct('settings/countries', $meta, $this->data); //redirect("system_settings/import_currency");
        
        
    }
    

    function currency_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCurrency($id);
                    }
                    $this->session->set_flashdata('message', lang("currencies_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('currencies'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('rate'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCurrencyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->rate);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'currencies_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_tax_rate_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    
      function import_budgets()
    {
          
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->load->model('sales_model');
        
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
        if(!$this->input->post('actual_values')){
 $this->form_validation->set_rules('budget_forecast', lang("budget_forecast_flag"), 'required');
 $this->form_validation->set_rules('net_gross', lang("net_gross_flag"), 'required');
        }
        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {
$errorlog="";
                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("budgets/import_budgets");
                }
              $budgetforecast=$this->input->post("budget_forecast");
               $net_gross=$this->input->post("net_gross");
                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 150000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
//                if(count($arrResult)>11000){
//                    $this->session->set_flashdata('error',"Budget_file_too_large_reduce_to_<_11K_lines");
//                    redirect("budgets/import_budgets");  
//                }
                
                  $titles = array_shift($arrResult);
                  if($this->input->post('actual_values')){
                    $errorlogg="";
                    if(count($arrResult)<2){
                        $errorlogg.="File is empty";
                    }
                    if(strlen($arrResult[0][1]) !=10){
                        $errorlogg.="Check date format"; 
                    }  //check the first date in the file
                  
                    
                    
                    
                                  if($errorlogg !=""){
    $this->settings_model->logErrors($errorlogg);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
} else{
    //import
    //die(print_r($arrResult));   
     foreach ($arrResult as $row) {
                
              
           
           
           $saledata=array('scenario'=>$row[0],'date' =>$row[1],'month'=>$row[2],'country'=>$row[3],'distributor_id'=>$row[4],'distributor_name'=>$row[5],'customer_id'=>$row[6],'customer_name'=>$row[7],'product_id'=>$row[9],'product_name'=>$row[10],'business_unit'=>$row[11],'promotion'=>$row[12],'budget_qty'=>$row[13],'budget_value'=>$row[14],'budget_at_resale'=>$row[15],'budget_at_supply'=>$row[16],'av_price'=>$row[17],'budget_forecast'=>$row[18],'net_gross'=>$row[19],'msr_alignment_id'=>$msrid,'msr_alignment_name'=>$msrname,'created'=>date("Y-m-d H:i"));
           $countrydet=$this->sales_model->getCountryByID($saledata['country']);  
           $msr_details = $this->sales_model->msr_customer_alignments($saledata["customer_id"],$saledata["product_id"],$saledata['country']);
         $msrid=$msr_details->sf_alignment_id;
           $msrname=$msr_details->sf_alignment_name;
          // die(print_r($saledata));
           $this->db->insert('budget', $saledata);
            $sale_id = $this->db->insert_id();
           $catdet=$this->site->getProductCategoryByProductId($saledata["product_id"]);
           $saledata['brand_id']=  $catdet["category_id"];
            $saledata['brand']=  $catdet["category_name"];
            $saledata['gmid']=  $catdet["product_gmid"];
             
            
           if(strtoupper($saledata['net_gross'])=="G"){
            $this->db->insert('consolidated_sales_sso', array('upload_type'=>'BUDGET','country'=>$countrydet->country,'monthyear'=>$saledata['date'],'customer_sanofi'=>$saledata['customer_name'],'customer_id'=>$saledata['customer_id'],'distributor'=>$saledata['distributor_name'],'distributor_id'=>$saledata['distributor_id'],'promotion'=>$saledata['promotion'],'brand'=>$saledata['brand'],'brand_id'=>$saledata['brand_id'],'bu'=>$saledata['business_unit'],'budget_qty'=>$saledata['budget_qty'],'gross_budget'=>$saledata['budget_value'],'budget_id' =>$sale_id,'product_id'=>$saledata['product_id'],'gmid'=>$saledata['gmid'],'product_name'=>$saledata['product_name'],'country_id'=>$saledata['country'],'msr_id' =>$msrid,'msr_name'=>$msrname));
           }
           else if(strtoupper($saledata['net_gross'])=="N"){
           $this->db->insert('consolidated_sales_sso', array('upload_type'=>'BUDGET','country'=>$countrydet->country,'monthyear'=>$saledata['date'],'customer_sanofi'=>$saledata['customer_name'],'customer_id'=>$saledata['customer_id'],'distributor'=>$saledata['distributor_name'],'distributor_id'=>$saledata['distributor_id'],'promotion'=>$saledata['promotion'],'brand'=>$saledata['brand'],'brand_id'=>$saledata['brand_id'],'bu'=>$saledata['business_unit'],'budget_qty'=>$saledata['budget_qty'],'net_budget'=>$saledata['budget_value'],'budget_id' =>$sale_id,'product_id'=>$saledata['product_id'],'gmid'=>$saledata['gmid'],'product_name'=>$saledata['product_name'],'country_id'=>$saledata['country'],'msr_id' =>$msrid,'msr_name'=>$msrname));
           }
         
           
           
           // $this->db->update('sales', array('updated_sso' =>1), array('id' => $row->id)); 
            }
    $this->session->set_flashdata('message', $this->lang->line("budget_added"));
            redirect("budgets/import_budgets");
}
                }
                  
                  
                  
                      $final = array();
                $scenario=$this->input->post("type");
                 $year=substr($this->input->post("smonth"),-4);
                 
              if(strtolower($scenario)=="pso"){
                    $keys = array('month','country','distributor','product','budget_qty');//'scenario','value'
                    
              $alldata=array();

                foreach ($arrResult as $key => $value) {
                    //$final = array_combine($keys, $value);
                    
                    $final['month']=$value[0];
                    $final['year']=$year;
                     $final['country']=$value[1];
                      $final['distributor']=$value[2];
                       $final['product']=$value[3];
                       $final['budget_qty']=str_replace(",","",$value[4]);
                        $final['budget_amount']=  str_replace(",","",$value[5]);
                       $final['scenario']=$scenario;
                       $final['budget_forecast']=  $this->input->post("budget_forecast");
                       $final['net_gross']= strtoupper($this->input->post("net_gross"));
                       array_push($alldata, $final);
                    
                }
                if(count($final)==0){
                    $this->session->set_flashdata('error',"Check csv column count");
                    redirect("budgets/import_budgets");  
                }
            //   $this->sma->print_arrays($alldata);
               
             foreach($arrResult as $key => $value){
    $months[] = $value[0];
    $distributors[] = $value[2];
     $countries[] = $value[1];
}
$yourUniquemonths = array_unique($months);
$yourUniquedistributors = array_unique($distributors);
$yourUniquecountries = array_unique($countries);
             foreach($yourUniquemonths as $Values){
    $count[] = $Values;
    foreach($yourUniquedistributors as $Values2){
        foreach($yourUniquecountries as $Values3){
        $country_det= $this->budget_model->getCountryByCode($Values3);
      //  trim($year."-".$Values."-01")
        $distrb=$this->companies_model->getCustomerByNameAndCountry(trim($Values2),$country_det->id); 
       // $this->budget_model->remove_PSObudgetdata(trim($year."-".$Values."-01"),$scenario,$net_gross,$country_det->id);
        
    }
        
    }
    
}
                 $rw = 2; ///row to start collecting data    
                
                 foreach ($alldata as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    
                    $catd = $this->settings_model->getCountryByName(trim($csv_pr['country']));
                    if (!$catd) {
                        $errorlog.=lang("Country_does_not_exist") . " :" . $csv_pr['country'] . ":. " . " " . lang("csv_line_no") . " " . $rw."\n";
                        //$this->session->set_flashdata('error', lang("Country_does_not_exist") . " (" . $csv_pr['country'] . "). " . " " . lang("csv_line_no") . " " . $rw);
                        
                        //redirect("budgets/import_budgets");
                    }
                    $distr=$this->companies_model->getCustomerByNameAndCountry(trim($csv_pr['distributor']),$catd->id);
                    if (!$distr) {
                        //$this->session->set_flashdata('error',"Check distributor" . " (" . $csv_pr['distributor'] . ") " . "doesnt exist in given country:" .$csv_pr['country']." ". lang("line_no") . " " . $rw);
                     //   redirect("budgets/import_budgets");
                        $errorlog.="Check distributor" . " :" . $csv_pr['distributor'] . ": " . "doesnt exist in given country:" .$csv_pr['country']." ". lang("line_no") . " " . $rw."\n";
                    }
                    
                    if ($prd=$this->products_model->getProductByName(str_replace("'","",$csv_pr['product']))) {
                        $dates=array("jan"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","may"=>"05","jun"=>"06","jul"=>"07","aug"=>"08","sept"=>"09","sep"=>"09","oct"=>"10","nov"=>"11","dec"=>"12",
                            "january"=>"01","february"=>"02","march"=>"03","april"=>"04","may"=>"05","june"=>"06","july"=>"07","august"=>"08","september"=>"09","october"=>"10","november"=>"11","december"=>"12",
                            "1"=>"01","2"=>"02","3"=>"03","4"=>"04","5"=>"05","6"=>"06","7"=>"07","8"=>"08","9"=>"09","10"=>"10","11"=>"11","12"=>"12");
                        $montht=  str_replace("M","",$csv_pr['month']);
                        $month=$dates[strtolower(trim($montht))];  
                        if(!$month){
                         // $this->session->set_flashdata('error',"Check month format" . " (" . $csv_pr['month'] . ")". lang("line_no") . " " . $rw);
                       // redirect("budgets/import_budgets"); 
                       $errorlog.= "Check month format" . " :" . $csv_pr['month'] . ":". lang("line_no") . " " . $rw."\n";
                        }
                        //die($this->input->post("type"));
                        $scenarioo =$this->input->post("type");
                        $yearmonth=trim($csv_pr['year']."-".$month."-01");  //"01-".trim($month."-".$csv_pr['year'])
                        $distributor=$distr->id;
                        $distributorname=$distr->name;
                        $product=$prd->id;
                        $product_name=$prd->name;
                   
                        $scenario2=trim($csv_pr["scenario"]);
                        
                        $country =$catd->id;
                        
                        $budget_qty=  str_replace(",","",trim($csv_pr['budget_qty']));
                          $budget_amount=str_replace(",","",trim($csv_pr['budget_amount']));       
                          $av_price=str_replace(",","",trim($csv_pr['av_price']));     
                          
                          $add=$this->budget_model->add_budget(array('scenario'=>$scenarioo,'date'=>$yearmonth,'country'=>$country,'distributor_id'=>$distributor,'distributor_name'=>$distributorname,'product_id'=>$product,'product_name'=>$product_name,'budget_qty'=>$budget_qty,'budget_value'=>$budget_amount,"budget_forecast"=>$budgetforecast,"net_gross"=>$net_gross));
                          
                    } else {
                        //$this->session->set_flashdata('error',"Check product name" . " (" . $csv_pr['product'] . ") " . "doesnt exist" . " " . lang("line_no") . " " . $rw);
                      // redirect("budgets/import_budgets");
                        $errorlog.= "Check product name" . " :" . $csv_pr['product'] . ":" . "doesnt exist" . " " . lang("line_no") . " " . $rw."\n";
                    }

                    $rw++;
                }
                
                                       //if(isset($errorlog)){
                                       if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
              }

              
               else if(strtolower($scenario)=="sso"){
                    
              $alldata=array();
                    //print_r($arrResult);
                  // die();
                             foreach($arrResult as $key => $value){
    $months[] = $value[0];
    $customers[] = $value[2];
    $countries[] = $value[1];
}
$yourUniquemonths = array_unique($months);
$yourUniquedistributors = array_unique($customers);
$yourUniquecountries = array_unique($countries);
// print_r($yourUniquedistributors);
  // die();
             foreach($yourUniquemonths as $Values){
    $count[] = $Values;
   
    foreach($yourUniquedistributors as $Values2){
        foreach($yourUniquecountries as $Values3){
        $country_det= $this->budget_model->getCountryByCode($Values3);
      //  trim($year."-".$Values."-01")
             $distrb=$this->budget_model->getSSOCustomerByName(trim($Values2),$country_det->id); 
             if(strlen($Values)==1){
                 $Values="0".$Values;
             }
      //  $this->budget_model->remove_SSObudgetdata(trim($year."-".$Values."-01"),$scenario,$net_gross,$country_det->id);
    }
        
    }
    
}


                foreach ($arrResult as $value) {
                    //$final = array_combine($keys, $value);
                  //  die(print_r($value));
                    $final['month']=$value[0];
                    $final['year']=$year;
                     $final['country']=$value[1];
                      $final['customer']=$value[2];
                       $final['product']=$value[3];
                       $final['budget_qty']=str_replace(",","",$value[4]);
                       $final['budget_amount']=str_replace(",","",$value[5]);
                       $final['scenario']=$scenario;
                       $final['budget_forecast']=  $this->input->post("budget_forecast");
                        $final['net_gross']= strtoupper($this->input->post("net_gross"));
                       array_push($alldata, $final);
                    
                                    }
                
                if(count($final)==0){
                    $this->session->set_flashdata('error',"Check csv column count");
                    redirect("budgets/import_budgets");  
                }
             
                $rw = 2; ///row to start collecting data
          
                       // die(print_r($alldata));
                
                       foreach ($alldata as $csv_pr) {
                  //  echo "Trying to import <br>";
                    
                    $catd = $this->settings_model->getCountryByName(trim($csv_pr['country']));
                    if (!$catd) {
                       // $this->session->set_flashdata('error', lang("Country_does_not_exist") . " (" . $csv_pr['country'] . "). " . " " . lang("csv_line_no") . " " . $rw);
                        
                       // redirect("budgets/import_budgets");
                        
                         $errorlog.=lang("Country_does_not_exist") . " :" . $csv_pr['country'] . ": " . " " . lang("csv_line_no") . " " . $rw."\n";
                    }
                    
                    $distr=$this->budget_model->getSSOCustomerByName(trim($csv_pr['customer']),$catd->id);
                    if (!$distr) {
                       // $this->session->set_flashdata('error',"Check Customer" . " (" . $csv_pr['customer'] . ") " . "doesnt exist in given country:" .$csv_pr['country']." ". lang("line_no") . " " . $rw);
                        //redirect("budgets/import_budgets");
                        
                         $errorlog.="Check Customer" . " :" . $csv_pr['customer'] . ": " . "doesnt exist in given country:" .$csv_pr['country'].": ". lang("line_no") . " " . $rw."\n";
                    }
                   $prd=$this->products_model->getProductByName(str_replace("'","",$csv_pr['product']));
                    if (is_object($prd)) {
                   
                      //  die(print_r($prd));
                        $dates=array("jan"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","may"=>"05","jun"=>"06","jul"=>"07","aug"=>"08","sept"=>"09","sep"=>"09","oct"=>"10","nov"=>"11","dec"=>"12",
                            "january"=>"01","february"=>"02","march"=>"03","april"=>"04","may"=>"05","june"=>"06","july"=>"07","august"=>"08","september"=>"09","october"=>"10","november"=>"11","december"=>"12",
                            "1"=>"01","2"=>"02","3"=>"03","4"=>"04","5"=>"05","6"=>"06","7"=>"07","8"=>"08","9"=>"09","10"=>"10","11"=>"11","12"=>"12");
                        $montht=  str_replace("M","",$csv_pr['month']);
                        $month=$dates[strtolower(trim($montht))];  
                        if(!$month){
                        // $this->session->set_flashdata('error',"Check month format" . " (" . $csv_pr['month'] . ")". lang("line_no") . " " . $rw);
                       // redirect("budgets/import_budgets");   
                            $errorlog.="Check month format" . " :" . $csv_pr['month'] . ":". lang("line_no") . " " . $rw."\n";
                        }
                         $msr_details = $this->sales_model->msr_customer_alignments($distr->id,$prd->id,$catd->id,$country_det->id);
                        //die($this->input->post("type"));
                        $scenarioo =$this->input->post("type");
                        $yearmonth=trim($csv_pr['year']."-".$month."-01");
                        $distributor=$distr->id;
                        $distributorname=$distr->name;
                        $product=$prd->id;
                        $product_name=$prd->name;
                        $msr_id=  $msr_details->sf_alignment_id;
                        $msr_name=  $msr_details->sf_alignment_name;
                        $scenario2=trim($csv_pr["scenario"]);
                        
                        $country =$catd->id;
                        
                        $budget_qty=  str_replace(",","",trim($csv_pr['budget_qty']));
                          $budget_amount=str_replace(",","",trim($csv_pr['budget_amount']));       
                          $av_price=str_replace(",","",trim($csv_pr['av_price']));     
                          
                                                   
                    } else {
                       // $this->session->set_flashdata('error',"Check product name" . " (" . $csv_pr['product'] . ") " . "doesnt exist" . " " . lang("line_no") . " " . $rw);
                      // redirect("budgets/import_budgets");
                        $errorlog.="Check product name" . " :" . $csv_pr['product'] . ": " . "doesnt exist" . " " . lang("line_no") . " " . $rw."\n";
                    }
                     if(empty($errorlog)){
//                         if($scenarioo=="SSO"){ //remove only once
                          //   $this->budget_model->remove_SSObudgetdata($yearmonth,$distributor,$scenarioo,$net_gross);  
                        // }else{
                        //   $this->budget_model->remove_PSObudgetdata($yearmonth,$distributor,$scenarioo,$net_gross);     
                       //  }
                    //die(print_r(array('scenario'=>$scenarioo,'date'=>$yearmonth,'country'=>$country_det->id,'customer_id'=>$distributor,'customer_name'=>$distributorname,'product_id'=>$product,'product_name'=>$product_name,'budget_qty'=>$budget_qty,'budget_value'=>$budget_amount,"budget_forecast"=>$budgetforecast,"net_gross"=>$net_gross,"msr_alignment_id"=>$msr_id,"msr_alignment_name"=>$msr_name)));
                         $add=$this->budget_model->add_budget(array('scenario'=>$scenarioo,'date'=>$yearmonth,'country'=>$catd->id,'customer_id'=>$distributor,'customer_name'=>$distributorname,'product_id'=>$product,'product_name'=>$product_name,'budget_qty'=>$budget_qty,'budget_value'=>$budget_amount,"budget_forecast"=>$budgetforecast,"net_gross"=>$net_gross,"msr_alignment_id"=>$msr_id,"msr_alignment_name"=>$msr_name));
   
             
    }
                    $rw++;
                }
                                                  //if(isset($errorlog)){
                                                      if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}

              }
               
                else{
                    $this->session->set_flashdata('error',"Uknown budget type,please select PSO,SSO or SI");
                    redirect("budgets/import_budgets");  
                }

            }
             if ($add) {
            $this->session->set_flashdata('message', lang("budget_imported"));
            redirect('budgets/import_budgets');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('import_products_by_csv')));
            $meta = array('page_title' => lang('import_countries_by_csv'), 'bc' => $bc);
            $this->page_construct('budgets/import_budget', $meta, $this->data); //redirect("system_settings/import_currency");

            }
        }
        else{
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('budgets'), 'page' => lang('budgets')), array('link' => '#', 'page' => lang('import_budget')));
            $meta = array('page_title' => lang('import_budget'), 'bc' => $bc);
            $this->page_construct('budgets/import_budget', $meta, $this->data); //redirect("system_settings/import_currency");

        }

       
    }
    
    
    
    
          function import_country_sso_budgets()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
$this->form_validation->set_rules('budget_forecast', lang("budget_forecast_flag"), 'required');
        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("budgets/import_country_sso_budgets");
                }
              $budgetforecast=$this->input->post("budget_forecast");
              $net_gross=$this->input->post("net_gross");
                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                  $titles = array_shift($arrResult);
                      $final = array();
                $scenario=$this->input->post("type");
              if(strtolower($scenario)=="pso" || strtolower($scenario)=="sso"){
                    $keys = array('year','month','gmid','movement','budget_qty','discount','scenario','customer','country');
                    
                

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                if(count($final)==0){
                    $this->session->set_flashdata('error',"Check csv column count");
                    redirect("budgets/import_country_sso_budgets");  
                }
               // $this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
             
                       foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    
                    $catd = $this->settings_model->getCountryByName(trim($csv_pr['country']));
                    if (!$catd) {
                        $this->session->set_flashdata('error', lang("Country_does_not_exist") . " (" . $csv_pr['country'] . "). " . " " . lang("csv_line_no") . " " . $rw);
                        
                        redirect("budgets/import_country_sso_budgets");
                    }
                    $distr=$this->companies_model->getCompanyByNameAndCountry(trim($csv_pr['customer']),$catd->id);
                    if (!$distr) {
                        $this->session->set_flashdata('error',"Check distributor" . " (" . $csv_pr['customer'] . ") " . "doesnt exist in given country:" .$csv_pr['country']." ". lang("line_no") . " " . $rw);
                        redirect("budgets/import_country_sso_budgets");
                    }
                    if ($prd=$this->products_model->getProductByCode(trim($csv_pr['gmid']))) {
                        $dates=array("jan"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","may"=>"05","jun"=>"06","jul"=>"07","aug"=>"08","sept"=>"09","sep"=>"09","oct"=>"10","nov"=>"11","dec"=>"12","january"=>"01","february"=>"02","march"=>"03","april"=>"04","may"=>"05","june"=>"06","july"=>"07","august"=>"08","september"=>"09","october"=>"10","november"=>"11","december"=>"12");
                        $montht=  str_replace("M","",$csv_pr['month']);
                        $month=$dates[strtolower(trim($montht))];  
                        if(!$month){
                          $this->session->set_flashdata('error',"Check month format" . " (" . $csv_pr['month'] . ")". lang("line_no") . " " . $rw);
                        redirect("budgets/import_country_sso_budgets");   
                        }
                        //die($this->input->post("type"));
                        $scenarioo =$this->input->post("type");
                        $yearmonth=trim($csv_pr['year']."-".$month."-01");
                        $distributor=$distr->id;
                        $distributorname=$distr->name;
                        $product=$prd->id;
                        $product_name=$prd->name;
                        $market=trim($csv_pr["market"]);
                        $bu=trim($csv_pr["bu"]);
                        $scenario2=trim($csv_pr["scenario"]);
                        $movement=trim($csv_pr["movement"]);
                        $country =$catd->id;
                        
                        $budget_qty=  str_replace(",","",trim($csv_pr['budget_qty']));
                          $budget_amount=str_replace(",","",trim($csv_pr['budget_amount']));       
                          $av_price=str_replace(",","",trim($csv_pr['av_price']));     
                          
                          $add=$this->budget_model->add_budget(array('scenario'=>$scenarioo,'date'=>$yearmonth,'country'=>$country,'distributor_id'=>$distributor,'distributor_name'=>$distributorname,'market'=>$market,'scenario2'=>$scenario2,'movement'=>$movement,'product_id'=>$product,'product_name'=>$product_name,'business_unit'=>$bu,'budget_qty'=>$budget_qty,'budget_value'=>$budget_amount,'av_price'=>$av_price,"budget_forecast"=>$budgetforecast,"net_gross"=>$net_gross));
                          
                    } else {
                        $this->session->set_flashdata('error',"Check product gmid" . " (" . $csv_pr['gmid'] . ") " . "doesnt exist" . " " . lang("line_no") . " " . $rw);
                       redirect("budgets/import_country_sso_budgets");
                    }

                    $rw++;
                }
            

              }
//                else if(strtolower($scenario)=="sso"){
//                            $keys = array('year','month','country','distributor','market','brand','gmid','epdis_code','product_description','bu','budget_qty','budget_amount','av_price');
//                }
                else if(strtolower($scenario)=="si"){
             $keys = array('year','month','gmid','movement_code','budget_qty','discount','sales_at_unified','sales_at_supply','sales_at_resale','scenario','customer','country');
              foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                if(count($final)==0){
                    $this->session->set_flashdata('error',"Check csv column count");
                    redirect("budgets/import_country_sso_budgets");  
                }
               // $this->sma->print_arrays($final);
                $rw=2;
                      foreach ($final as $csv_pr) {
                          if($csv_pr["year"]&&$csv_pr["month"]){
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    
                    $catd = $this->settings_model->getCountryByName(trim($csv_pr['country']));
                    if (!$catd) {
                        $this->session->set_flashdata('error', lang("Country_does_not_exist") . " (" . $csv_pr['country'] . "). " . " " . lang("csv_line_no") . " " . $rw);
                        
                        redirect("budgets/import_country_sso_budgets");
                    }
                    $distr=$this->companies_model->getCompanyByNameAndCountry(trim($csv_pr['customer']),$catd->id);
                    if (!$distr) {
                        $this->session->set_flashdata('error',"Check distributor" . " (" . $csv_pr['customer'] . ") " . "doesnt exist in given country:" .$csv_pr['country']." ". lang("line_no") . " " . $rw);
                        redirect("budgets/import_country_sso_budgets");
                    }
                    if ($prd=$this->products_model->getProductByCode(trim($csv_pr['gmid']))) {
                        $dates=array("jan"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","may"=>"05","jun"=>"06","jul"=>"07","aug"=>"08","sept"=>"09","sep"=>"09","oct"=>"10","nov"=>"11","dec"=>"12","january"=>"01","february"=>"02","march"=>"03","april"=>"04","may"=>"05","june"=>"06","july"=>"07","august"=>"08","september"=>"09","october"=>"10","november"=>"11","december"=>"12");
                        $montht=  str_replace("M","",$csv_pr['month']);
                        $month=$dates[strtolower(trim($montht))];  
                        if(!$month){
                          $this->session->set_flashdata('error',"Check month format" . " (" . $csv_pr['month'] . ")". lang("line_no") . " " . $rw);
                        redirect("budgets/import_country_sso_budgets");   
                        }
                        //die($this->input->post("type"));
                        $scenarioo =$this->input->post("type");
                        $yearmonth=trim($csv_pr['year']."-".$month."-01");
                        $distributor=$distr->id;
                        $distributorname=$distr->name;
                        $product=$prd->id;
                        $product_name=$prd->name;
                        $market=trim($csv_pr["market"]);
                        $bu=trim($csv_pr["bu"]);
                        $scenario2=trim($csv_pr["scenario"]);
                        $movement=trim($csv_pr["movement"]);
                        $country =$catd->id;
                        $budget_qty=  str_replace(",","",trim($csv_pr['budget_qty']));
                          $budget_amount=str_replace(",","",trim($csv_pr['sales_at_unified']));  
                           $budget_at_resale=str_replace(",","",trim($csv_pr['sales_at_resale']));  
                           $budget_at_supply=str_replace(",","",trim($csv_pr['sales_at_supply']));  
                         
                          
                          $add=$this->budget_model->add_budget(array('scenario'=>$scenarioo,'date'=>$yearmonth,'country'=>$country,'distributor_id'=>$distributor,'distributor_name'=>$distributorname,'market'=>$market,'scenario2'=>$scenario2,'movement'=>$movement,'product_id'=>$product,'product_name'=>$product_name,'business_unit'=>$bu,'budget_qty'=>$budget_qty,'budget_value'=>$budget_amount,'budget_at_resale'=>$budget_at_resale,'budget_at_supply'=>$budget_at_supply,"budget_forecast"=>$budgetforecast,"net_gross"=>$net_gross));
                          
                    } else {
                        $this->session->set_flashdata('error',"Check product gmid" . " (" . $csv_pr['gmid'] . ") " . "doesnt exist" . " " . lang("line_no") . " " . $rw);
                       redirect("budgets/import_country_sso_budgets");
                    }

                    $rw++;
                }
                }
                }
                else{
                    $this->session->set_flashdata('error',"Uknown budget type,please select PSO,SSO or SI");
                    redirect("budgets/import_country_sso_budgets");  
                }

                
           

       
            
             if ($add) {
            $this->session->set_flashdata('message', lang("budget_imported"));
            redirect('budgets/import_country_sso_budgets');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('import_products_by_csv')));
            $meta = array('page_title' => lang('import_countries_by_csv'), 'bc' => $bc);
            $this->page_construct('budgets/import_sso_country_budgets', $meta, $this->data); //redirect("system_settings/import_currency");

            }}
        }
        else{
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('budgets'), 'page' => lang('budgets')), array('link' => '#', 'page' => lang('import_budget')));
            $meta = array('page_title' => lang('import_budget'), 'bc' => $bc);
            $this->page_construct('budgets/import_sso_country_budgets', $meta, $this->data); //redirect("system_settings/import_currency");

        }

       
    }
    
    
    
    
  
    
    function categories()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('categories')));
        $meta = array('page_title' => lang('categories'), 'bc' => $bc);
        $this->page_construct('settings/categories', $meta, $this->data);
    }

    function getCategories()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, image, code, name")
            ->from("categories")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/subcategories/$1') . "' class='tip' title='" . lang("list_subcategories") . "'><i class=\"fa fa-list\"></i></a> <a href='" . site_url('system_settings/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_category") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_category") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        echo $this->datatables->generate();
    }
    
    
     function getBudget()
    {
        $this->sma->checkPermissions('index');

       
        
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_budget") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('budgets/delete_budget/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_budget') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
         
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
      
            $this->datatables
                ->select("sma_budget.id as id,scenario,budget_forecast,DATE_FORMAT(sma_budget.date,'%m-%Y') as date,sma_currencies.country,case scenario
        when 'PSO' then sma_companies.name
       when 'SSO' then sma_customers.name
    end as names,sma_categories.name as category,sma_products.name as name,sma_categories.gbu as business_unit,budget_qty,budget_value,av_price")
                  
                ->from('budget')
                    ->join("products","budget.product_id=products.id","left")
                     ->join("companies","budget.distributor_id=companies.id","left")
                    ->join("customers","budget.customer_id=customers.id","left")
                    ->join("currencies","budget.country=currencies.id","left")
                    ->join("categories","products.category_id=categories.id","left")
          ->add_column("Actions", "<center>".$delete_link."</center>");
   
     
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
    
    
    
    
         function getCountrySSOBudget()
    {
        $this->sma->checkPermissions('index');

       
        
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_budget") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('budgets/delete_budget/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_budget') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
         
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
      
            $this->datatables
                ->select("id,month,country,distributor,product,quantity_units,value")
                  
                ->from('sso_budget_country')
          ->add_column("Actions", "<center>".$delete_link."</center>");
   
     
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
    
    
             function getCountryPSOBudget()
    {
        $this->sma->checkPermissions('index');

       
        
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_budget") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('budgets/delete_budget/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_budget') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
         
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
      
            $this->datatables
                ->select("id,month,country,distributor,product,quantity_units,value")
                  
                ->from('pso_budget_country')
          ->add_column("Actions", "<center>".$delete_link."</center>");
   
       // if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin) {
       //     $this->datatables->where('created_by', $this->session->userdata('user_id'));
       // } elseif ($this->Customer) {
       //     $this->datatables->where('customer_id', $this->session->userdata('user_id'));
      //  }
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
    
    
    
    
             function getCustomerSSOBudget()
    {
        $this->sma->checkPermissions('index');

       
        
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_budget") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('budgets/delete_budget/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_budget') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
         
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
      
            $this->datatables
                ->select("id,month,country,customer,product,quantity_units,value")
                  
                ->from('sso_budget_customer')
          ->add_column("Actions", "<center>".$delete_link."</center>");
   
       // if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin) {
       //     $this->datatables->where('created_by', $this->session->userdata('user_id'));
       // } elseif ($this->Customer) {
       //     $this->datatables->where('customer_id', $this->session->userdata('user_id'));
      //  }
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
    
    
    
    
    
    
     function import_categories_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
//die(print_r($_FILES));
        if ($this->form_validation->run() == true && !$this->input->post()) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("system_settings/categories");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
              //  unlink($this->digital_upload_path . $csv);
                $titles = array_shift($arrResult);

                $keys = array('name');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    $countryy=$this->settings_model->getCategoryName(trim($csv_pr['name']));
                    if ($countryy) {
                        $this->session->set_flashdata('error',"Check brand" . " (" .$csv_pr['name'] . ") " . " already exists" . " " . lang("line_no") . " " . $rw);
                        redirect("system_settings/categories");
                    }
                    
                        $name[] = trim($csv_pr['name']);
                       $code[]=$rw;
                        
                   

                    $rw++;
                }
            }

            $ikeys = array('name','code');

            $items = array();
            foreach (array_map(null,$name) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

          //  $this->sma->print_arrays($items);
            
             if ($this->settings_model->add_Category($items)) {
            $this->session->set_flashdata('message', lang("categories_imported"));
            redirect('system_settings/categories');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('categories'), 'page' => lang('categories')), array('link' => '#', 'page' => lang('import_categories')));
            $meta = array('page_title' => lang('import_categories'), 'bc' => $bc);
            $this->page_construct('settings/import_category', $meta, $this->data); //redirect("system_settings/import_currency");

        }
        }
        else{
             $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('categories'), 'page' => lang('categories')), array('link' => '#', 'page' => lang('import_categories')));
            $meta = array('page_title' => lang('import_categories'), 'bc' => $bc);
           $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_category', $this->data);
        }

       
    }
    
     function import_category_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
//die(print_r($_FILES));
        if ($this->form_validation->run() == true && $this->input->post()) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("system_settings/categories");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
              //  unlink($this->digital_upload_path . $csv);
                $titles = array_shift($arrResult);

                $keys = array('name');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    $countryy=$this->products_model->getCategoryByName(trim($csv_pr['name']));
                    if ($countryy) {
                        $this->session->set_flashdata('error',"Check brand" . " (" .$csv_pr['name'] . ") " . " already exists" . " " . lang("line_no") . " " . $rw);
                        redirect("system_settings/categories");
                    }
                    
                        $name[] = trim($csv_pr['name']);
                       $code[]=$rw;
                        
                   

                    $rw++;
                }
            }

            $ikeys = array('name','code');

            $items = array();
            foreach (array_map(null,$name,$code) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

        // $this->sma->print_arrays($items);
            
             if ($this->settings_model->add_Category($items)) {
            $this->session->set_flashdata('message', lang("categories_imported"));
            redirect('system_settings/categories');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('categories'), 'page' => lang('categories')), array('link' => '#', 'page' => lang('import_categories')));
            $meta = array('page_title' => lang('import_categories'), 'bc' => $bc);
            $this->page_construct('settings/import_categories_csv', $meta, $this->data); //redirect("system_settings/import_currency");

        }
        }
        else{
             $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('categories'), 'page' => lang('categories')), array('link' => '#', 'page' => lang('import_categories')));
            $meta = array('page_title' => lang('import_categories'), 'bc' => $bc);
           $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_category', $this->data);
        }

       
    }
    
    function clusters()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('categories')));
        $meta = array('page_title' => lang('clusters'), 'bc' => $bc);
        $this->page_construct('settings/clusters', $meta, $this->data);
    }
    
    function getClusters()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id,name")
            ->from("cluster")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/cluster_countries/$1') . "' class='tip' title='" . lang("list_countries") . "'><i class=\"fa fa-list\"></i></a> <a href='" . site_url('system_settings/edit_cluster/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_cluster") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_cluster") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_cluster/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        echo $this->datatables->generate();
    }
    
       function add_cluster()
    {

        
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_cluster', $this->data);
        
    }
    
    function delete_cluster($id = NULL)
    {

        if ($this->settings_model->deleteCluster($id)) {
            echo lang("Cluster_deleted");
        }
    }

    function post_cluster(){
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[cluster.name]|required');
       

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'),
                           );
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCluster($data)) {
            $this->session->set_flashdata('message', lang("cluster_added"));
            redirect("system_settings/clusters");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
  redirect("system_settings/clusters");
    }
    }
    
    function edit_budget($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $customermsr_details = $this->budget_model->getBudgetByID($id);
 
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
            $this->load->view($this->theme . 'budgets/edit_budget', $this->data);
        }
    }
    
    function add_category($id = NULL)
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[1]');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
           // $name = $this->input->post('name');
            $name = $this->settings_model->getStimaCategoryByID($this->input->post('name')); 
            $name=$name->name;
           // print_r($name);
           // die($name);
            $code = $this->input->post('code');

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                //$data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'right';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            } else {
                $photo = NULL;
            }
        } elseif ($this->input->post('add_category')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/categories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategory($name, $code, $photo)) {
            $this->session->set_flashdata('message', lang("category_added"));
            redirect("system_settings/categories");
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

//            $this->data['name'] = array('name' => 'name',
//                'id' => 'name',
//                'type' => 'text',
//                'class' => 'form-control',
//                'required' => 'required',
//                'value' => $this->form_validation->set_value('name'),
//            );
            $this->data['categories'] = $this->settings_model->getAllStimaCategories();
            $this->data['category'] = $id ? $this->settings_model->getStimaCategoryById($id) : NULL;
            $this->data['code'] = array('name' => 'code',
                'id' => 'code',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('code'),
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_category', $this->data);
        }
    }

    function edit_category($id = NULL)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|required');
        $pr_details = $this->settings_model->getCategoryByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("category_code"), 'is_unique[categories.code]');
        }
        $this->form_validation->set_rules('name', lang("category_name"), 'required|min_length[3]');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name')
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                //$data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'right';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            } else {
                $photo = NULL;
            }
        } elseif ($this->input->post('edit_category')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/categories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCategory($id, $data, $photo)) {
            $this->session->set_flashdata('message', lang("category_updated"));
            redirect("system_settings/categories");
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $category = $this->settings_model->getCategoryByID($id);
            $this->data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('name', $category->name),
            );
            $this->data['code'] = array('name' => 'code',
                'id' => 'code',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('code', $category->code),
            );

            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'settings/edit_category', $this->data);
        }
    }

    function delete_category($id = NULL)
    {

        if ($this->settings_model->getSubCategoriesByCategoryID($id)) {
            $this->session->set_flashdata('error', lang("category_has_subcategory"));
            redirect("categories", 'refresh');
        }

        if ($this->settings_model->deleteCategory($id)) {
            echo lang("category_deleted");
        }
    }

    function category_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang("categories_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCategoryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_tax_rate_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    
    function edit_country_pricing()
    {
        $this->load->helper('security');
        $this->load->model('country_productpricing_model');
        $this->form_validation->set_rules('unified_price', lang("unified_price"), 'trim|required');
        $this->form_validation->set_rules('supply_price', lang("supply_price"), 'trim|required');
        $id=$this->input->get('id');
        if(!$id){
             $id=$this->input->post('id');
        }
        //die($id."xsdds");
        $pr_details = $this->country_productpricing_model->getCountryProductById($id);
     // die(print_r($pr_details));
        if ($this->form_validation->run() == true && $this->input->post('name')) {
           

            $data = array(
                'product_name' => $this->input->post('name'),
                'unified_price' => $this->input->post('unified_price'),
                'supply_price' => $this->input->post('supply_price'),
                'resell_price' => $this->input->post('resell_price'),   
                'promotion' => $this->input->post('promotion'),  
                'from_date' => $this->input->post('from_date'), 
                'to_date' => $this->input->post('to_date') 
                
            );
            
        } elseif ($this->input->post('edit_country_pricing')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/country_pricing/".$pr_details->country_id);
        }

        if ($this->form_validation->run() == true && $this->country_productpricing_model->updatePrice($id,$data)) {
            $this->session->set_flashdata('message', lang("pricing_updated"));
            redirect("system_settings/country_pricing/".$pr_details->country_id);
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
           
            $this->data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('name',$pr_details->product_name),
            );
            $this->data['unifiedprice'] = array('name' => 'unified_price',
                'id' => 'code',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('unified_price', $pr_details->unified_price),
            );
             $this->data['resellprice'] = array('name' => 'resell_price',
                'id' => 'resellprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('resellprice', $pr_details->resell_price),
            );
             
             $this->data['supplyprice'] = array('name' => 'supply_price',
                'id' => 'supplyprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('supply_price', $pr_details->supply_price),
            );
             
             $this->data['tenderprice'] = array('name' => 'tender_price',
                'id' => 'tenderprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('tender_price', $pr_details->tender_price),
            );
              $this->data['promotion'] = array('name' => 'promotion',
                'id' => 'promotion',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('promotion', $pr_details->promotion),
            );
             $this->data['fromdate'] = array('name' => 'from_date',
                'id' => 'fromdate',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('from_date', $pr_details->from_date),
            );
             
             $this->data['todate'] = array('name' => 'to_date',
                'id' => 'todate',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('to_date', $pr_details->to_date),
            );

            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'settings/edit_country_pricing', $this->data);
        }
    }
    function subcategories($parent_id = NULL)
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['parent_id'] = $parent_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => site_url('system_settings/categories'), 'page' => lang('categories')), array('link' => '#', 'page' => lang('subcategories')));
        $meta = array('page_title' => lang('subcategories'), 'bc' => $bc);
        $this->page_construct('settings/subcategories', $meta, $this->data);
    }

    function getSubcategories($parent_id = NULL)
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("subcategories.id as id, subcategories.image as image, subcategories.code as scode, subcategories.name as sname, categories.name as cname")
            ->from("subcategories")
            ->join('categories', 'categories.id = subcategories.category_id', 'left')
            ->group_by('subcategories.id');

        if ($parent_id) {
            $this->datatables->where('category_id', $parent_id);
        }

        $this->datatables->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_subcategory/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_subcategory") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_subcategory") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_subcategory/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        // ->unset_column('id');
        echo $this->datatables->generate();
    }

    function add_subcategory($parent_id = NULL)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('category', lang("main_category"), 'required');
        $this->form_validation->set_rules('code', lang("subcategory_code"), 'trim|is_unique[categories.code]|is_unique[subcategories.code]|required');
        $this->form_validation->set_rules('name', lang("subcategory_name"), 'required|min_length[2]');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $name = $this->input->post('name');
            $code = $this->input->post('code');
            $category = $this->input->post('category');
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                //$data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'right';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            } else {
                $photo = NULL;
            }
        } elseif ($this->input->post('add_subcategory')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/subcategories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addSubCategory($category, $name, $code, $photo)) {
            $this->session->set_flashdata('message', lang("subcategory_added"));
            redirect("system_settings/subcategories", 'refresh');
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

            $this->data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text', 'class' => 'form-control',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('name'),
            );
            $this->data['code'] = array('name' => 'code',
                'id' => 'code',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('code'),
            );
            $this->data['parent_id'] = $parent_id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->load->view($this->theme . 'settings/add_subcategory', $this->data);
        }
    }

    function edit_subcategory($id = NULL)
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('category', lang("main_category"), 'required');
        $this->form_validation->set_rules('code', lang("subcategory_code"), 'trim|required');
        $pr_details = $this->settings_model->getSubCategoryByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("subcategory_code"), 'is_unique[categories.code]');
        }
        $this->form_validation->set_rules('name', lang("subcategory_name"), 'required|min_length[2]');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = array(
                'category' => $this->input->post('category'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name')
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                //$data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'right';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            } else {
                $photo = NULL;
            }
        } elseif ($this->input->post('edit_subcategory')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/subcategories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSubCategory($id, $data, $photo)) {
            $this->session->set_flashdata('message', lang("subcategory_updated"));
            redirect("system_settings/subcategories");
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['subcategory'] = $this->settings_model->getSubCategoryByID($id);
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'settings/edit_subcategory', $this->data);
        }
    }

    function delete_subcategory($id = NULL)
    {

        if ($this->settings_model->deleteSubCategory($id)) {
            echo lang("subcategory_deleted");
        }
    }

    function subcategory_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteSubcategory($id);
                    }
                    $this->session->set_flashdata('message', lang("subcategories_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('subcategories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('main_category'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getSubcategoryDetails($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->parent);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'subcategories_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_tax_rate_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function tax_rates()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('tax_rates')));
        $meta = array('page_title' => lang('tax_rates'), 'bc' => $bc);
        $this->page_construct('settings/tax_rates', $meta, $this->data);
    }

    function getTaxRates()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, name, code, rate, type")
            ->from("tax_rates")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_tax_rate/$1') . "' class='tip' title='" . lang("edit_tax_rate") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_tax_rate") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_tax_rate/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_tax_rate()
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[tax_rates.name]|required');
        $this->form_validation->set_rules('code', lang("code"), 'required');
        $this->form_validation->set_rules('type', lang("type"), 'required');
        $this->form_validation->set_rules('rate', lang("tax_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
                'rate' => $this->input->post('rate'),
            );
        } elseif ($this->input->post('add_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/tax_rates");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addTaxRate($data)) {
            $this->session->set_flashdata('message', lang("tax_rate_added"));
            redirect("system_settings/tax_rates");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_tax_rate', $this->data);
        }
    }

    function edit_tax_rate($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $tax_details = $this->settings_model->getTaxRateByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('code', lang("code"), 'required');
        $this->form_validation->set_rules('type', lang("type"), 'required');
        $this->form_validation->set_rules('rate', lang("tax_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
                'rate' => $this->input->post('rate'),
            );
        } elseif ($this->input->post('edit_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/tax_rates");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateTaxRate($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("tax_rate_updated"));
            redirect("system_settings/tax_rates");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_tax_rate', $this->data);
        }
    }

    function delete_tax_rate($id = NULL)
    {
        if ($this->settings_model->deleteTaxRate($id)) {
            echo lang("tax_rate_deleted");
        }
    }

    function tax_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteTaxRate($id);
                    }
                    $this->session->set_flashdata('message', lang("tax_rates_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('type'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tax = $this->settings_model->getTaxRateByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $tax->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tax->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tax->rate);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($tax->type == 1) ? lang('percentage') : lang('fixed'));
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tax_rates_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_tax_rate_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function customer_groups()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('customer_groups')));
        $meta = array('page_title' => lang('customer_groups'), 'bc' => $bc);
        $this->page_construct('settings/customer_groups', $meta, $this->data);
    }

    function getCustomerGroups()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, name, percent")
            ->from("customer_groups")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_customer_group/$1') . "' class='tip' title='" . lang("edit_customer_group") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_customer_group") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_customer_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_customer_group()
    {

        $this->form_validation->set_rules('name', lang("group_name"), 'trim|is_unique[customer_groups.name]|required');
        $this->form_validation->set_rules('percent', lang("group_percentage"), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'),
                'percent' => $this->input->post('percent'),
            );
        } elseif ($this->input->post('add_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/customer_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCustomerGroup($data)) {
            $this->session->set_flashdata('message', lang("customer_group_added"));
            redirect("system_settings/customer_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_customer_group', $this->data);
        }
    }

    function edit_customer_group($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("group_name"), 'trim|required');
        $pg_details = $this->settings_model->getCustomerGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang("group_name"), 'is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('percent', lang("group_percentage"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'percent' => $this->input->post('percent'),
            );
        } elseif ($this->input->post('edit_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/customer_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCustomerGroup($id, $data)) {
            $this->session->set_flashdata('message', lang("customer_group_updated"));
            redirect("system_settings/customer_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['customer_group'] = $this->settings_model->getCustomerGroupByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_customer_group', $this->data);
        }
    }

    function delete_customer_group($id = NULL)
    {
        if ($this->settings_model->deleteCustomerGroup($id)) {
            echo lang("customer_group_deleted");
        }
    }

    function customer_group_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCustomerGroup($id);
                    }
                    $this->session->set_flashdata('message', lang("customer_groups_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('group_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('group_percentage'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $pg = $this->settings_model->getCustomerGroupByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pg->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pg->percent);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customer_groups_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_customer_group_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function warehouses()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('warehouses')));
        $meta = array('page_title' => lang('warehouses'), 'bc' => $bc);
        $this->page_construct('settings/warehouses', $meta, $this->data);
    }

    function getWarehouses()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, map, code, name, phone, email, address")
            ->from("warehouses")
            //->edit_column("map", base_url().'assets/uploads/$1', 'map')
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_warehouse/$1') . "' class='tip' title='" . lang("edit_warehouse") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_warehouse") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_warehouse/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id')
        //->unset_column('map');

        echo $this->datatables->generate();
    }

    function add_warehouse()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("code"), 'trim|is_unique[warehouses.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('address', lang("address"), 'required');
        $this->form_validation->set_rules('userfile', lang("map_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '1024';
                $config['max_width'] = '2000';
                $config['max_height'] = '2000';
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    redirect("system_settings/warehouses");
                }

                $map = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'assets/uploads/' . $map;
                $config['new_image'] = 'assets/uploads/thumbs/' . $map;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 76;
                $config['height'] = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $map = NULL;
            }
            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'map' => $map,
            );
        } elseif ($this->input->post('add_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/warehouses");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addWarehouse($data)) {
            $this->session->set_flashdata('message', lang("warehouse_added"));
            redirect("system_settings/warehouses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_warehouse', $this->data);
        }
    }

    function edit_warehouse($id = NULL)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("code"), 'trim|required');
        $wh_details = $this->settings_model->getWarehouseByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang("code"), 'is_unique[warehouses.code]');
        }
        $this->form_validation->set_rules('address', lang("address"), 'required');
        $this->form_validation->set_rules('map', lang("map_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
            );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '1024';
                $config['max_width'] = '2000';
                $config['max_height'] = '2000';
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    redirect("system_settings/warehouses");
                }

                $data['map'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'assets/uploads/' . $data['map'];
                $config['new_image'] = 'assets/uploads/thumbs/' . $data['map'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 76;
                $config['height'] = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        } elseif ($this->input->post('edit_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/warehouses");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateWarehouse($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("warehouse_updated"));
            redirect("system_settings/warehouses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouse'] = $this->settings_model->getWarehouseByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_warehouse', $this->data);
        }
    }

    function delete_budget($id = NULL)
    {
        $post=$this->input->post();
        if(is_array($post["val"])){
            foreach ($post["val"] as $idposted) {
              $this->budget_model->deleteBudget($idposted);  
            }
           $this->session->set_flashdata('success', lang("budget_lines_deleted"));
            redirect("budgets/index");
        }
        if ($this->budget_model->deleteBudget($id)) {
            echo lang("budget_line_deleted");
        }
    }

    function warehouse_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteWarehouse($id);
                    }
                    $this->session->set_flashdata('message', lang("warehouses_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('warehouses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('city'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $wh = $this->settings_model->getWarehouseByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $wh->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $wh->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $wh->address);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $wh->city);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'warehouses_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_warehouse_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function variants()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('variants')));
        $meta = array('page_title' => lang('variants'), 'bc' => $bc);
        $this->page_construct('settings/variants', $meta, $this->data);
    }

    function getVariants()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, name")
            ->from("variants")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_variant/$1') . "' class='tip' title='" . lang("edit_variant") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_budget") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('budgets/delete_budget/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_variant()
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[variants.name]|required');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('add_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/variants");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addVariant($data)) {
            $this->session->set_flashdata('message', lang("variant_added"));
            redirect("system_settings/variants");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_variant', $this->data);
        }
    }

    function edit_variant($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $tax_details = $this->settings_model->getVariantByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'is_unique[variants.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('edit_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/variants");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateVariant($id, $data)) {
            $this->session->set_flashdata('message', lang("variant_updated"));
            redirect("system_settings/variants");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['variant'] = $tax_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_variant', $this->data);
        }
    }

    function delete_variant($id = NULL)
    {
        if ($this->settings_model->deleteVariant($id)) {
            echo lang("variant_deleted");
        }
    }
    function category()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('warehouses')));
        $meta = array('page_title' => lang('warehouses'), 'bc' => $bc);
        $this->page_construct('settings/warehouses', $meta, $this->data);
    }
    
    function  liststimacategorybyid($category_id= NULL){
        
         $mysqli=new mysqli("localhost","root","","techsava_restaurant");//connect_db();
         $sql="select category_id as code, name from ep0ytvat2_categories where category_id=$category_id";
         $result=mysqli_query($mysqli,$sql);
            echo mysqli_error($mysqli);
            if(mysqli_num_rows($result)>0){
               
              $rows= mysqli_fetch_assoc($result);
              // $data[] = $rows;
              $data = json_encode($rows);
            }
    
         echo $data;   
            
    }
    
    
  function get_cities($cluster){
     
        $this->load->model('cluster_model');
        header('Content-Type: application/x-json; charset=utf-8');
                echo(json_encode($this->cluster_model->get_countries_cluster($cluster)));
                
    }

     function get_products($cluster){
     
        $this->load->model('cluster_model');
        header('Content-Type: application/x-json; charset=utf-8');
                echo(json_encode($this->products_model->get_products_category($cluster)));
                
    }
    
    

}
