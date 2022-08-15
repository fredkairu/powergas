<?php defined('BASEPATH') OR exit('No direct script access allowed');

class system_settings extends MY_Controller
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
        $this->load->model('country_productpricing_model');
         $this->digital_upload_path = 'files/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '4096';
    }

    function index()
    {

        $this->form_validation->set_rules('site_name', lang('site_name'), 'trim|required');
        $this->form_validation->set_rules('dateformat', lang('dateformat'), 'trim|required');
        $this->form_validation->set_rules('timezone', lang('timezone'), 'trim|required');
        $this->form_validation->set_rules('mmode', lang('maintenance_mode'), 'trim|required');
        //$this->form_validation->set_rules('logo', lang('logo'), 'trim');
        $this->form_validation->set_rules('iwidth', lang('image_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('iheight', lang('image_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('twidth', lang('thumbnail_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('theight', lang('thumbnail_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('watermark', lang('watermark'), 'trim|required');
        $this->form_validation->set_rules('reg_ver', lang('reg_ver'), 'trim|required');
        $this->form_validation->set_rules('allow_reg', lang('allow_reg'), 'trim|required');
        $this->form_validation->set_rules('reg_notification', lang('reg_notification'), 'trim|required');
        $this->form_validation->set_rules('currency', lang('default_currency'), 'trim|required');
        $this->form_validation->set_rules('email', lang('default_email'), 'trim|required');
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('default_warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('default_biller'), 'trim|required');
        $this->form_validation->set_rules('tax_rate', lang('product_tax'), 'trim|required');
        $this->form_validation->set_rules('tax_rate2', lang('invoice_tax'), 'trim|required');
        $this->form_validation->set_rules('sales_prefix', lang('sales_prefix'), 'trim');
        $this->form_validation->set_rules('quote_prefix', lang('quote_prefix'), 'trim');
        $this->form_validation->set_rules('purchase_prefix', lang('purchase_prefix'), 'trim');
        $this->form_validation->set_rules('transfer_prefix', lang('transfer_prefix'), 'trim');
        $this->form_validation->set_rules('delivery_prefix', lang('delivery_prefix'), 'trim');
        $this->form_validation->set_rules('payment_prefix', lang('payment_prefix'), 'trim');
        $this->form_validation->set_rules('return_prefix', lang('return_prefix'), 'trim');
        $this->form_validation->set_rules('expense_prefix', lang('expense_prefix'), 'trim');
        $this->form_validation->set_rules('detect_barcode', lang('detect_barcode'), 'trim|required');
        $this->form_validation->set_rules('theme', lang('theme'), 'trim|required');
        $this->form_validation->set_rules('rows_per_page', lang('rows_per_page'), 'trim|required|greater_than[9]|less_than[501]');
        $this->form_validation->set_rules('accounting_method', lang('accounting_method'), 'trim|required');
        $this->form_validation->set_rules('product_serial', lang('product_serial'), 'trim|required');
        $this->form_validation->set_rules('product_discount', lang('product_discount'), 'trim|required');
        $this->form_validation->set_rules('bc_fix', lang('bc_fix'), 'trim|numeric|required');
        $this->form_validation->set_rules('protocol', lang('email_protocol'), 'trim|required');
        if ($this->input->post('protocol') == 'smtp') {
            $this->form_validation->set_rules('smtp_host', lang('smtp_host'), 'required');
            $this->form_validation->set_rules('smtp_user', lang('smtp_user'), 'required');
            $this->form_validation->set_rules('smtp_pass', lang('smtp_pass'), 'required');
            $this->form_validation->set_rules('smtp_port', lang('smtp_port'), 'required');
        }
        if ($this->input->post('protocol') == 'sendmail') {
            $this->form_validation->set_rules('mailpath', lang('mailpath'), 'required');
        }
        $this->form_validation->set_rules('decimals', lang('decimals'), 'trim|required');
        $this->form_validation->set_rules('decimals_sep', lang('decimals_sep'), 'trim|required');
        $this->form_validation->set_rules('thousands_sep', lang('thousands_sep'), 'trim|required');
        $this->load->library('encrypt');

        if ($this->form_validation->run() == true) {

            $language = $this->input->post('language');

            if ((file_exists('sma/language/' . $language . '/sma_lang.php') && is_dir('sma/language/' . $language)) || $language == 'english') {
                $lang = $language;
            } else {
                $this->session->set_flashdata('error', lang('language_x_found'));
                redirect("system_settings");
                $lang = 'english';
            }

            $tax1 = ($this->input->post('tax_rate') != 0) ? 1 : 0;
            $tax2 = ($this->input->post('tax_rate2') != 0) ? 1 : 0;

            $data = array('site_name' => DEMO ? 'Stock Manager Advance' : $this->input->post('site_name'),
                'rows_per_page' => $this->input->post('rows_per_page'),
                'dateformat' => $this->input->post('dateformat'),
                'timezone' => DEMO ? 'Africa/Nairobi' : $this->input->post('timezone'),
                'mmode' => trim($this->input->post('mmode')),
                'iwidth' => $this->input->post('iwidth'),
                'iheight' => $this->input->post('iheight'),
                'twidth' => $this->input->post('twidth'),
                'theight' => $this->input->post('theight'),
                'watermark' => $this->input->post('watermark'),
                'reg_ver' => $this->input->post('reg_ver'),
                'allow_reg' => $this->input->post('allow_reg'),
                'reg_notification' => $this->input->post('reg_notification'),
                'accounting_method' => $this->input->post('accounting_method'),
                'default_email' => DEMO ? 'noreply@sma.tecdiary.my' : $this->input->post('email'),
                'language' => $lang,
                'default_warehouse' => $this->input->post('warehouse'),
                'default_tax_rate' => $this->input->post('tax_rate'),
                'default_tax_rate2' => $this->input->post('tax_rate2'),
                'sales_prefix' => $this->input->post('sales_prefix'),
                'quote_prefix' => $this->input->post('quote_prefix'),
                'purchase_prefix' => $this->input->post('purchase_prefix'),
                'transfer_prefix' => $this->input->post('transfer_prefix'),
                'delivery_prefix' => $this->input->post('delivery_prefix'),
                'payment_prefix' => $this->input->post('payment_prefix'),
                'return_prefix' => $this->input->post('return_prefix'),
                'expense_prefix' => $this->input->post('expense_prefix'),
                'auto_detect_barcode' => trim($this->input->post('detect_barcode')),
                'theme' => trim($this->input->post('theme')),
                'product_serial' => $this->input->post('product_serial'),
                'customer_group' => $this->input->post('customer_group'),
                'product_expiry' => $this->input->post('product_expiry'),
                'product_discount' => $this->input->post('product_discount'),
                'default_currency' => $this->input->post('currency'),
                'bc_fix' => $this->input->post('bc_fix'),
                'tax1' => $tax1,
                'tax2' => $tax2,
                'overselling' => $this->input->post('restrict_sale'),
                'reference_format' => $this->input->post('reference_format'),
                'racks' => $this->input->post('racks'),
                'attributes' => $this->input->post('attributes'),
                'restrict_calendar' => $this->input->post('restrict_calendar'),
                'captcha' => $this->input->post('captcha'),
                'item_addition' => $this->input->post('item_addition'),
                'protocol' => DEMO ? 'mail' : $this->input->post('protocol'),
                'mailpath' => $this->input->post('mailpath'),
                'smtp_host' => $this->input->post('smtp_host'),
                'smtp_user' => $this->input->post('smtp_user'),
                'smtp_port' => $this->input->post('smtp_port'),
                'smtp_crypto' => $this->input->post('smtp_crypto') ? $this->input->post('smtp_crypto') : NULL,
                'decimals' => $this->input->post('decimals'),
                'decimals_sep' => $this->input->post('decimals_sep'),
                'thousands_sep' => $this->input->post('thousands_sep'),
                'default_biller' => $this->input->post('biller'),
                'invoice_view' => $this->input->post('invoice_view'),
                'rtl' => $this->input->post('rtl'),
                'each_spent' => $this->input->post('each_spent') ? $this->input->post('each_spent') : NULL,
                'ca_point' => $this->input->post('ca_point') ? $this->input->post('ca_point') : NULL,
                'each_sale' => $this->input->post('each_sale') ? $this->input->post('each_sale') : NULL,
                'sa_point' => $this->input->post('sa_point') ? $this->input->post('sa_point') : NULL,
                'sac' => $this->input->post('sac')
            );
            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->encrypt->encode($this->input->post('smtp_pass'));
            }
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {
            if ($this->write_index($data['timezone']) == false) {
                $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                redirect('system_settings');
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            redirect("system_settings");
        } else {

            $this->data['error'] = validation_errors();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
             $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['alignment_groups'] = $this->settings_model->getAllAlignmentGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
            $this->data['smtp_pass'] = $this->encrypt->decode($this->data['settings']->smtp_pass);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('system_settings')));
            $meta = array('page_title' => lang('system_settings'), 'bc' => $bc);
            $this->page_construct('settings/index', $meta, $this->data);
        }
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
                'expenses-index' => $this->input->post('expenses-index'),
                'expenses-edit' => $this->input->post('expenses-edit'),
                'expenses-add' => $this->input->post('expenses-add'),
                'expenses-delete' => $this->input->post('expenses-delete'),
                'expenses-approve' => $this->input->post('expenses-approve'),
                'routes-index' => $this->input->post('routes-index'),
                'routes-edit' => $this->input->post('routes-edit'),
                'routes-add' => $this->input->post('routes-add'),
                'routes-delete' => $this->input->post('routes-delete'),
                'vehicles-index' => $this->input->post('vehicles-index'),
                'vehicles-edit' => $this->input->post('vehicles-edit'),
                'vehicles-add' => $this->input->post('vehicles-add'),
                'vehicles-delete' => $this->input->post('vehicles-delete'),
                'vehicles-add-route' => $this->input->post('vehicles-add-route'),
                'vehicles-view-route' => $this->input->post('vehicles-view-route'),
                'vehicles-edit-route' => $this->input->post('vehicles-edit-route'),
                'vehicles-delete-route' => $this->input->post('vehicles-delete-route'),
                'vehicles-add-stock' => $this->input->post('vehicles-add-stock'),
                'vehicles-view-stock' => $this->input->post('vehicles-view-stock'),
                'vehicles-edit-stock' => $this->input->post('vehicles-edit-stock'),
                'customers-index' => $this->input->post('customers-index'),
                'customers-edit' => $this->input->post('customers-edit'),
                'customers-add' => $this->input->post('customers-add'),
                'customers-edit-shops' => $this->input->post('customers-edit-shops'),
                'customers-delete-shops' => $this->input->post('customers-delete-shops'),
                'customers-index-pm' => $this->input->post('customers-index-pm'),
                'customers-add-pm' => $this->input->post('customers-add-pm'),
                'customers-edit-pm' => $this->input->post('customers-edit-pm'),
                'customers-delete-pm' => $this->input->post('customers-delete-pm'),
                'customers-delete' => $this->input->post('customers-delete'),
                'customers-activate' => $this->input->post('customers-activate'),
                'customers-add-credit-limit' => $this->input->post('customers-add-credit-limit'),
                'customers-edit-credit-limit' => $this->input->post('customers-edit-credit-limit'),
                'customers-delete-credit-limit' => $this->input->post('customers-delete-credit-limit'),
                'distributors-index' => $this->input->post('distributors-index'),
                'distributors-edit' => $this->input->post('distributors-edit'),
                'distributors-add' => $this->input->post('distributors-add'),
                'distributors-delete' => $this->input->post('distributors-delete'),
                'distributors-activate' => $this->input->post('distributors-activate'),
                'distributors-add-targets' => $this->input->post('distributors-add-targets'),
                'distributors-index-targets' => $this->input->post('distributors-index-targets'),
                'distributors-edit-targets' => $this->input->post('distributors-edit-targets'),
                'distributors-delete-targets' => $this->input->post('distributors-delete-targets'),
                'salespeople-index' => $this->input->post('salespeople-index'),
                'salespeople-edit' => $this->input->post('salespeople-edit'),
                'salespeople-add' => $this->input->post('salespeople-add'),
                'salespeople-delete' => $this->input->post('salespeople-delete'),
                'salespeople-activate' => $this->input->post('salespeople-activate'),
                'salespeople-add-targets' => $this->input->post('salespeople-add-targets'),
                'salespeople-index-targets' => $this->input->post('salespeople-index-targets'),
                'salespeople-edit-targets' => $this->input->post('salespeople-edit-targets'),
                'salespeople-delete-targets' => $this->input->post('salespeople-delete-targets'),
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
                'stock-taking-index' => $this->input->post('stock-taking-index'),
                'stock-taking-view' => $this->input->post('stock-taking-view'),
                'stock-taking-delete' => $this->input->post('stock-taking-delete'),
                'stock-taking-reverse' => $this->input->post('stock-taking-reverse'),
                'budget-index' => $this->input->post('budget-index'),
                'budget-edit' => $this->input->post('budget-edit'),
                'budget-add' => $this->input->post('budget-add'),
                'budget-delete' => $this->input->post('budget-delete'),
                'budget-email' => $this->input->post('budget-email'),
                'budget-pdf' => $this->input->post('budget-pdf'),
                'sales-return_sales' => $this->input->post('sales-return_sales'),
                'reports-vehicles' => $this->input->post('reports-vehicles'),
                'reports-salespeople' => $this->input->post('reports-salespeople'),
                'reports-brand' => $this->input->post('reports-brand'),
                'reports-sales' => $this->input->post('reports-sales'),
                'reports-products' => $this->input->post('reports-products'),
                'reports-budget' => $this->input->post('reports-budget'),
                'mashariki_report' => $this->input->post('mashariki_report'),
                'reports-payments' => $this->input->post('reports-payments'),
                'reports-purchases' => $this->input->post('reports-purchases'),
                'reports-customers' => $this->input->post('reports-customers'),
                'reports-suppliers' => $this->input->post('reports-suppliers'),
                'sales-payments' => $this->input->post('sales-payments'),
                'purchases-payments' => $this->input->post('purchases-payments'),
                'purchases-expenses' => $this->input->post('purchases-expenses'),
                
                'dashboard' => $this->input->post('dashboard'),
                'pso' => $this->input->post('pso'),
                'sso' => $this->input->post('sso'),
                'si' => $this->input->post('si'),
                'monthly_trend' => $this->input->post('monthly_trend'),
                'pso_sso_sit' => $this->input->post('pso_sso_sit'),
                'distributor_sit' => $this->input->post('distributor_sit'),
                'msr_summary' => $this->input->post('msr_summary')
                
                
                
                
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
    
      function audit()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('audit_trail')));
        $meta = array('page_title' => lang('Audit_Trail'), 'bc' => $bc);
        $this->page_construct('settings/audit', $meta, $this->data);
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
        

    $productslink="<a href='" . site_url('system_settings/country_pricing_categorized/$1') . "' target='_blank' class='tip' title='" . lang("country_product_pricing") . "'><i class=\"fa fa-search\"></i></a> ";

        $this->load->library('datatables');
        $id=  $this->input->get("id");
       // die($id."dsds");
        if($id){
           
            $this->datatables
                     ->select("currencies.id as id,cluster.name as cluster,currencies.country,currencies.portuguese_name,currencies.code,currencies.name,currencies.rate")
            ->from("currencies")
                  
                ->join('cluster', 'cluster.id =currencies.cluster', 'left')
          ->where("currencies.cluster=$id")
            ->add_column("Actions", "<center>".$productslink."<a href='" . site_url('system_settings/product_pricing/$1') . "' class='tip' title='" . lang("product_pricing") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-upload\"></i></a> <a href='" . site_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");   
            
        }
        else{
        $this->datatables
            ->select("currencies.id as id,cluster.name as cluster,currencies.country,currencies.portuguese_name,currencies.code,currencies.name,currencies.rate")
            ->from("currencies")
                ->join('cluster', 'cluster.id =currencies.cluster', 'left')
        
            ->add_column("Actions", "<center>".$productslink."<a href='" . site_url('system_settings/product_pricing/$1') . "' class='tip' title='" . lang("product_pricing") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-upload\"></i></a> <a href='" . site_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        }
        //->unset_column('id');

        echo $this->datatables->generate();
    }
    
      function getTrail()
    {
        

    $productslink="<a href='" . site_url('system_settings/country_pricing_categorized/$1') . "' target='_blank' class='tip' title='" . lang("country_product_pricing") . "'><i class=\"fa fa-search\"></i></a> ";

        $this->load->library('datatables');
      
        $this->datatables
            ->select("user_logins.id as id,users.first_name,user_logins.ip_address,login,time")
            ->from("user_logins")
                ->join('users', 'user_logins.user_id =users.id', 'left');
        $this->db->order_by("id", "desc");
        $this->db->limit(10000);
                
           
       
        //->unset_column('id');

        echo $this->datatables->generate();
    }
function geteamMembers()
    {
        

    $productslink="<a href='" . site_url('system_settings/country_pricing_categorized/$1') . "' target='_blank' class='tip' title='" . lang("country_product_pricing") . "'><i class=\"fa fa-search\"></i></a> ";

        $this->load->library('datatables');
        $id=  $this->input->get("id");
       // die($id."dsds");
        if($id){
           
            $this->datatables
                     ->select("msr_alignments.id,msr_alignments.msr_alignment_name,employee.name")
            ->from("msr_alignments")
                ->join('employee', 'employee.alignment_id =msr_alignments.id', 'left')
                ->where("employee.group_id='1'")
          ->where("msr_alignments.team_id=$id");
           // ->add_column("Actions", "<center>".$productslink."<a href='" . site_url('system_settings/product_pricing/$1') . "' class='tip' title='" . lang("product_pricing") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-upload\"></i></a> <a href='" . site_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");   
            
        }
        else{
        $this->datatables
          ->select("msr_alignments.id,msr_alignments.msr_alignment_name,employee.name")
            ->from("msr_alignments")
                ->join('employee', 'employee.alignment_id =msr_alignments.id', 'left')
                ->where("employee.group_id='1'");
        
            //->add_column("Actions", "<center>".$productslink."<a href='" . site_url('system_settings/product_pricing/$1') . "' class='tip' title='" . lang("product_pricing") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-upload\"></i></a> <a href='" . site_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        }
        //->unset_column('id');

        echo $this->datatables->generate();
    }
    
    
    function bu(){
         if (!$this->Owner) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect('auth');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

       
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('bu')));
        $meta = array('page_title' => lang('bu'), 'bc' => $bc);
        $this->page_construct('settings/bu', $meta, $this->data);
    }
    
    function getBUs(){
         $this->load->library('datatables');
         $this->datatables
                     ->select("id,name,active")
            ->from("business_unit")
              
        
            ->add_column("Actions", "<center>"." <a href='" . site_url('system_settings/edit_bu/$1') . "' class='tip' title='" . lang("edit_bu") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a><a href='#' class='tip po' title='<b>" . lang("delete_bu") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_bu/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id"); //<a href='" . site_url('system_settings/edit_bu/$1') . "' class='tip' title='" . lang("edit_bu") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> 
        
        //->unset_column('id');

        echo $this->datatables->generate();
    }
    
    function add_bu()
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|required|is_unique[business_unit.name]');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');
        

        if ($this->form_validation->run() == true) {
            $data = array('name'=>$this->input->post('name'),
                'active'=>$this->input->post('active'),
                
            );
        } else if ($this->input->post('add_bu')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/add_bu");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addBU($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang("bu_added"));
            redirect("system_settings/bu");
        } else {
            
           //  $this->data['bus']=  $this->settings_model->getBU();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['page_title'] = lang("new_bu");
            $this->load->view($this->theme . 'settings/add_bu', $this->data);
        }
    }
    
        function edit_bu($id = NULL)
    {

       $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');
       if(!$id){
           $id=$this->input->post('id');
       }
       
       
        $cur_details = $this->settings_model->getBUByID($id);
        $this->load->model('settings_model');
        
        if ($this->form_validation->run() == true) {

            $data = array(
                'name'=>$this->input->post('name'),
                'active'=>$this->input->post('active')
                    );
           
             $id=$this->input->post('id');
           
             
           // die(print_r($data));
        } elseif ($this->input->post('edit_bu')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/bu");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateBU($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("bu_updated"));
            redirect("system_settings/bu");
        } else {
            $this->load->model('settings_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['bus']=  $this->settings_model->getBU();
            $this->data['bu'] = $this->settings_model->getBUByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_bu', $this->data);
        }
    }
    function delete_bu($id = NULL)
    {

        if ($this->settings_model->deleteBU($id)) {
            echo lang("bu_deleted");
        }
    
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
$user_CSV[0] = array('country','distributor','customer','product_gmid','supply_price','resale_price','tender_price','special_resale','special_tender','promotion');
$allproducts=  $this->products_model->getAllProducts();
$i=1;
// very simple to increment with i++ if looping through a database result 
        foreach ($allproducts as $pr) {
            $user_CSV[$i] = array($countrydetail->country,'distributor','customer',$pr->code,0,0,0,0,0,'non-promoted');
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
       function pricing_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
         $this->load->model('country_productpricing_model');
         $this->load->model('products_model');

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                     
                        $this->country_productpricing_model->deletePricing($id);
                    }
                    $this->session->set_flashdata('message', lang("pricing_deleted"));
                    redirect("system_settings/country_pricing_categorized");
                }
                
                if($this->input->post('form_action')=="duplicate"){
                    $fromdate=$this->input->post("newtodate");
                    $todate=$this->input->post("newfromdate");
                    
                    
                   foreach ($_POST['val'] as $id) {
                     
                        $this->country_productpricing_model->duplicateCountryProduct($id,$fromdate,$todate);
                    }
                    $this->session->set_flashdata('message', lang("pricing_for_".$fromdate."_to_".$todate."_duplicated_successfully"));
                      redirect("system_settings/country_pricing_categorized");
                }
                
                
             

                if ($this->input->post('form_action') == 'export_excel' || 
                        $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('country_pricing'));
                     $this->excel->getActiveSheet()->SetCellValue('A1', lang('Country'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Gmid'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('SKU'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('Unified_Price'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('Resale_Price'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('Tender_Price'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('Supply_Price'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('Promotion'));
                     $this->excel->getActiveSheet()->SetCellValue('I1', lang('Effective_From'));
                      $this->excel->getActiveSheet()->SetCellValue('J1', lang('Effective_To'));
                     $bulk_array=array();
                    $row = 2;
                    //die(print_r($_POST['val']));
                    foreach ($_POST['val'] as $id) {
                        $sale = $this->country_productpricing_model->getCountryProductById($id);
                       // die(print_r($sale));
                        $product=$this->products_model->getProductById($sale->product_id);
                         $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sale->country);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sale->product_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sale->unified_price);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sale->resell_price);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sale->tender_price);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale->supply_price);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $sale->promotion);
                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $sale->from_date);
                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $sale->to_date);
                        array_push($bulk_array,$sale);
                        $row++;
                    }

        
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'country_pricing_' . date('Y_m_d_H_i_s');
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
                   
                    if ($this->input->post('form_action') == 'bulk_payment'){
                        
                       //print_r($bulk_array);
                      
                     }
                    
                    
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_sale_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    
    function country_pricing()
    {         
        $id=$this->input->get("id");
                    $this->load->model('cluster_model');
            $this->load->model('country_productpricing_model');
            if($this->input->post("fromdate") && $this->input->post("todate")&& $this->input->post("country")){
                       // die(print_r($this->input->post("country")));
                $start=$this->input->post("fromdate");
                $end=$this->input->post("todate");
                $id=$this->input->post("country");
                
            $productsuploaded=$this->country_productpricing_model->getCountryProducts($this->input->post("country"),$start,$end);
            }
            //die(print_r($productsuploaded));
            if(is_array($productsuploaded)){
               $this->data['notice']="Some product prices have already been uploaded,do you wish to overwrite?"; 
            }else{
                $this->data['notice']="";
            }
            
            $this->data['modal_js'] = $this->site->modal_js();
             $currency=  $this->settings_model->getCurrencyByID($id);
             $this->data['products']=$productsuploaded;
             if(!$id or empty($currency)){
             $this->data['country_id']=null;
             $this->data['country_name']="all countries";
             
             } else{
                 $this->data['country_id']=$id;
             $this->data['country_name']=$currency->country;
             }
             $this->data['currency_id']=$currency->id;
            
             $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('country_pricing')));
        $meta = array('page_title' => lang("Product_prices_for_".  strtoupper($this->data['country_name'])." from ".$start. " to ".$end), 'bc' => $bc);
            $this->page_construct('settings/country_prices', $meta, $this->data);
            
        
    }
    
    
    function country_pricing_categorized($id)
    {         
            $this->load->model('cluster_model');
            $this->load->model('country_productpricing_model');
            $productsuploaded=$this->country_productpricing_model->getCountryProductsCategorized($id);
            //die(print_r($productsuploaded));
            if(is_array($productsuploaded)){
               $this->data['notice']="Some product prices have already been uploaded,do you wish to overwrite?"; 
            }else{
                $this->data['notice']="";
            }
            
            $this->data['modal_js'] = $this->site->modal_js();
             $currency=  $this->settings_model->getCurrencyByID($id);
             $this->data['products']=$productsuploaded;
             $this->data['country_id']=$id;
             $this->data['country_name']=$currency->country;
             $this->data['currency_id']=$currency->id;
             $this->data['currencies']=$this->settings_model->getAllCurrencies();
             //die(print_r($this->settings_model->getAllCurrencies()));
            
             $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('country_pricing')));
        $meta = array('page_title' => lang("Product_prices_for_".$currency->country), 'bc' => $bc);
            $this->page_construct('settings/countrypricescategorised', $meta, $this->data);
            
        
    }
    
       function prices_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
        $this->load->model('country_productpricing_model');
           $this->load->model('companies_model');
         $this->load->model('products_model');
//die(print_r($_POST));
        if ($this->form_validation->run() == true) {
$countryid=$this->input->post('currency_id');
$country=$this->input->post('country');
$fromdatee=$this->input->post('fromdate');
$todatee=$this->input->post('todate');
if((strlen($fromdatee)< 7 || strlen($todatee) < 7 || strpos($fromdatee, '/') == false)){
  $this->session->set_flashdata('error',"Ensure date format is mm/YYYY.Current format is ".$fromdatee." and ". $todatee);
                    redirect("system_settings/currencies");  
}


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
   //if($upload_type="")
                $keys=array('country','distributor','customer','product_gmid','supply_price','resell_price','tender_price','special_resale','special_tender','promotion');

                $final =array();

                foreach ($arrResult as $key => $value) {
                 $final[] = array_combine($keys, $value);
                }
            //  $this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                $errorlog="";
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    
                    $trimmedname=  str_replace(" ","",trim($csv_pr['product_gmid']));
                    $product=$this->products_model->getProductByCode($trimmedname);
                    if (!$product){
                       $errorlog.="Check product" . " (" .$csv_pr['product_gmid'] . ") " . "doesnt exist in database" . " " . lang("line_no") . " " . $rw;
                       
                    }
                    if(!empty($countryid)){
                        $countrydet=  $this->settings_model->getCurrencyByID(trim($countryid));
                    }else{
                    $countrydet=  $this->settings_model->getCountryByName(trim($csv_pr['country']));
                      if (!$countrydet){
                       $errorlog.="Check country" . " (" .$csv_pr['country'] . ") " . "doesnt exist in database" . " " . lang("line_no") . " " . $rw;
                       
                    }
                    }
                    
                    
                    //$pricingdetails=  $this->country_productpricing_model->getCountryProductPricingForDate($product->id,$countrydet->id,$todatee);
                    $pricingdetails=  $this->country_productpricing_model->deleteCountryProductPricingForDate($product->id,$countrydet->id,$todatee);
                    $cst=$this->companies_model->getCustomerByNameAndCountry(trim($csv_pr['customer']),$countrydet->id);
                     if (!$cst && !empty($csv_pr['customer'])){
                        $errorlog.="Check customer " . " (" .$csv_pr['customer'] . ") " . "doesnt exist in country " .$csv_pr['country'] . lang("line_no") . " " . $rw;
                       
                    }
                    
                    $dist=$this->companies_model->getCompanyByNameAndCountry(trim($csv_pr['distributor']),$countrydet->id);
                     if (!$dist && !empty($csv_pr['distributor'])){
                       $errorlog.="Check distributor" . " (" .$csv_pr['distributor'] . ") " . "doesnt exist in country " . $csv_pr['country'] . lang("line_no") . " " . $rw;
                 
                    }
                    
                              if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
                     //print_r($pricingdetails);
                   
                      //if ($pricingdetails){
                       // $this->country_productpricing_model->deletePricing($pricingdetails->id);
                        //$this->session->set_flashdata('error',"Check country pricing for " . " (" .$csv_pr['country'] ." Product Gmid:".$csv_pr['product_gmid']." and date ".$todatee. ") " . "already exists" . " " . lang("line_no") . " " . $rw);
                        //redirect("system_settings/currencies");
                   // }
                         //die();
                        $product_name[] = trim($product->name);
                        $product_ids[]=trim($product->id);
                        $countryids[]=$countrydet->id;
                        $distributor[]=@$dist->id;
                        $customer[]=$cst->id;
                        $unifiedprice[]=0;// trim($csv_pr['upp_price']);
                        $resellprice[]= trim($csv_pr['resell_price']);
                        $tenderprice[]= trim($csv_pr['tender_price']);
                        $supplyprice[]=trim($csv_pr['supply_price']);
                        $specialresale[]=trim($csv_pr['special_resale']);
                        $specialtender[]=trim($csv_pr['special_tender']);
                        $promotion[]=  strtolower(trim($csv_pr['promotion']));
                        $fromdate[]=$fromdatee;
                        $todate[]=$todatee;
                       
                       
                    $rw++;
                }
            }

           $ikeys = array('country_id', 'product_id','product_name','distributor_id','customer_id','unified_price','resell_price','tender_price','supply_price','special_resell_price','special_tender_price','promotion','from_date','to_date');

            $items = array();
            foreach (array_map(null,$countryids, $product_ids,$product_name,$distributor,$customer,$unifiedprice,$resellprice,$tenderprice,$supplyprice,$specialresale,$specialtender,$promotion,$fromdate,$todate) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

         //$this->sma->print_arrays($items);
            
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
            $this->page_construct('system_settings/currencies', $meta, $this->data); //redirect("system_settings/import_currency");

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
     function edit_msr($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("MSR"), 'trim|required');
       if(!$id){
           $id=$this->input->post('id');
       }
       
       $this->form_validation->set_rules('team_name', lang("Team_name"), 'required|min_length[1]');
       $countrydet=  $this->settings_model->getCurrencyByID($this->input->post('country'));
       $teamdet = $this->settings_model->getTeamByID($this->input->post('team_name'));
         $this->load->model('cluster_model');
        if ($this->form_validation->run() == true) {
            $data = array('msr_alignment_name' => $this->input->post('name'),
                        'country_id' => $this->input->post('country'),
                        'country' => $countrydet->country,
                        'team_id' => $this->input->post('team_name'),
                        'team_name' => $teamdet->name,
                        'business_unit' => $this->input->post('business_unit')
                        
                        );
       
        $id=$this->input->post('id');
      
        }
        
        elseif ($this->input->post('update_dsm')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/msr");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateMSR($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("Team_updated"));
            redirect("system_settings/msr");
        } else {
            $this->load->model('cluster_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['msr'] = $this->settings_model->getMsrByID($id);
           $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['teams']=  $this->settings_model->getAllTeams();
              $this->data['bu']=  $this->site->getAllBu();
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_msr', $this->data);
        }
    }
    function edit_dsm($id = NULL)
    {

       // $this->form_validation->set_rules('name', lang("dsm_name"), 'trim|is_unique[dsm_alignments.dsm_alignment_name]|required');
        
       if(!$id){
           $id=$this->input->post('id');
       }
       
       $this->form_validation->set_rules('name', lang("dsm_name"), 'required|min_length[1]');
       $countrydet=  $this->settings_model->getCurrencyByID($this->input->post('country'));
      // $teamdet = $this->settings_model->getTeamByID($this->input->post('team_name'));
         $this->load->model('cluster_model');
        if ($this->form_validation->run() == true) {
            $data = array('dsm_alignment_name' => $this->input->post('name'),
                        'country_id' => $this->input->post('country'),
                        'country' => $countrydet->country,
                        'business_unit' => $this->input->post('business_unit')
                        
                        );
       
        $id=$this->input->post('id');
      print_r($data);
        } elseif ($this->input->post('update_dsm')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/dsm");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateDSM($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("Team_updated"));
            redirect("system_settings/dsm");
        } else {
            $this->load->model('cluster_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['dsm'] = $this->settings_model->getDsmByID($id);
           $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['teams']=  $this->settings_model->getAllTeams();
              $this->data['bu']=  $this->site->getAllBu();
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_dsm', $this->data);
        }
    }
    
          function edit_team($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("team_name"), 'trim|required');
       if(!$id){
           $id=$this->input->post('id');
       }
       
       
        
        $this->load->model('cluster_model');
        	 $countrydet=  $this->settings_model->getCurrencyByID($this->input->post('country'));
        if ($this->form_validation->run() == true) {

            $data = array(
                'name'=>$this->input->post('name'),
                 'country_id'=>$this->input->post('country'),
                 'country'=>$countrydet->country,
                  'business_unit' => $this->input->post('business_unit'));
             $id=$this->input->post('id');
           
             
           // die(print_r($data));
        } elseif ($this->input->post('update_team')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/teams");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateTeam($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("Team_updated"));
            redirect("system_settings/teams");
        } else {
            $this->load->model('cluster_model');
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
             $this->data['teams']=  $this->cluster_model->getTeams();
            $this->data['team'] = $this->settings_model->getTeamByID($id);
            $countries=$this->settings_model->getAllCurrencies();
        $this->data['countries']=  $countries;
            $this->data['id'] = $id;
            $this->data['bu']=  $this->site->getAllBu();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_team', $this->data);
        }
    }
         function getCountryTeams($country_id = NULL)
    {
        if ($rows = $this->settings_model->getTeamsForCountryID($country_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    } 
     function getCountryTeamsnotassigned($country_id = NULL)
    {
        if ($rows = $this->settings_model->getTeamsNoDSMForCountryID($country_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    } 
    function cluster_countries($id){
        $this->data['cluster']=$id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings/clusters'), 'page' => lang('clusters')), array('link' => '#', 'page' => lang('cluster_countries')));
            $meta = array('page_title' => lang('Cluster_countries'), 'bc' => $bc);
            $this->page_construct('settings/countries', $meta, $this->data); //redirect("system_settings/import_currency");
        
        
    }
      function team_members($id){
        $this->data['team']=$id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings/clusters'), 'page' => lang('clusters')), array('link' => '#', 'page' => lang('cluster_countries')));
            $meta = array('page_title' => lang('Team_members'), 'bc' => $bc);
            $this->page_construct('settings/team_members', $meta, $this->data); //redirect("system_settings/import_currency");
        
        
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
                $this->session->set_flashdata('error', lang("no_item_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
      function trail_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
           

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('Audit_Trail'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('User'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('IP'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Username'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('Time'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getTrailById($id);
                        $user=$this->site->getUser($sc->user);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row,$user->first_name." ".$user->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->ip_address);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->login);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sc->time);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'audit_trail_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_item_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
 function add_team_csv()
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
                    redirect("system_settings/teams");
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

                $keys = array( 'name', 'country', 'business_unit');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                    
              
                    $country=$this->settings_model->getCountryByName($csv['country']);
                    if(!$country){
                        $errorlog.= $this->lang->line("check_country") . " :" . $csv['country'] . ": " . $this->lang->line("country_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw .")\n";
                        //redirect("system_settings/teams"); 
                    }
                    
                   
                    $csv['name'] = $csv['name'];
                    $csv['country_id'] = $country->id;
                    $csv['country'] = $csv['country'];
                    $csv['business_unit'] = $csv['business_unit'];
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
              //  $this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import_teams')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('system_settings/teams');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->settings_model->addTeamcsv($data)) {
                $this->session->set_flashdata('message', $this->lang->line("Team_added"));
                redirect('system_settings/teams');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_team_csv', $this->data);
        }
    }
    
    function add_dsm_csv()
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
                    redirect("system_settings/dsm");
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

                $keys = array( 'dsm_alignment_name', 'country', 'business_unit');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                    
              
                    $country=$this->settings_model->getCountryByName($csv['country']);
                    if(!$country){
                        $errorlog.= $this->lang->line("check_country") . " (" . $csv['country'] . "). " . $this->lang->line("country_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                        //redirect("system_settings/dsm"); 
                    }
                    
                   
                    $csv['dsm_alignment_name'] = $csv['dsm_alignment_name'];
                    $csv['country_id'] = $country->id;
                    $csv['country'] = $csv['country'];
                    $csv['business_unit'] = $csv['business_unit'];
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                                                       if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
              //  $this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import_dsm')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('system_settings/dsm');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->settings_model->addDsmcsv($data)) {
                $this->session->set_flashdata('message', $this->lang->line("Team_added"));
                redirect('system_settings/dsm');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_dsm_csv', $this->data);
        }
    } 
    function add_Msr_csv()
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
                    redirect("system_settings/msr");
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

                $keys = array( 'name', 'country', 'bu', 'team');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                    
              
                    $country=$this->settings_model->getCountryByName($csv['country']);
                    if(!$country){
                        $errorlog.= $this->lang->line("check_country") . " :" . $csv['country'] . ": " . $this->lang->line("country_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                        //redirect("system_settings/msr"); 
                    }
                    $team_details=$this->site->getTeamByName($csv['team']);
                    if(!$team_details){
                        $errorlog.= $this->lang->line("check_Team") . " :" . $csv['team'] . ": " . $this->lang->line("Team_doesnt_exist") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                        //redirect("system_settings/msr"); 
                    }
                     $msr_details=$this->site->getmsrByName($csv['name']);
                    if($msr_details){
                        $errorlog.= $this->lang->line("check_MSR") . " :" . $csv['name'] . ": " . $this->lang->line("MSR_exists") . " (" . $this->lang->line("line_no") . " " . $rw . ")\n";
                        //redirect("system_settings/msr"); 
                    }
                   
                    $csv['name'] = $csv['name'];
                    $csv['country_id'] = $country->id;
                    $csv['country'] = $csv['country'];
                    $csv['business_unit'] = $csv['bu'];
                    $csv['team'] = $csv['team'];
                    $csv['team_id'] =$team_details->id;
                    
                    $data[] = $csv;
                    
                    $rw++;
                }
                if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
              //  $this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import_msr')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('system_settings/msr');
        }
        
        
        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->settings_model->addMsrcsv($data)) {
                $this->session->set_flashdata('message', $this->lang->line("MSR_added"));
                redirect('system_settings/msr');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_msr', $this->data);
        }
    } 
      function import_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
//die(print_r($_FILES));
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
                    redirect("system_settings/currencies");
                }
               $fromdate=  $this->input->post('startdate'); 
               $todate=  $this->input->post('enddate'); 
                

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

                $keys = array('cluster', 'country', 'french_desc', 'portuguese_desc', 'currency_code', 'currency_name', 'exchange_rate');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    $countryy=$this->settings_model->getCountryByName(trim($csv_pr['country']));
                    if ($countryy) {
                        $this->session->set_flashdata('error',"Check country" . " (" . $country->country . ") " . " already exists" . " " . lang("line_no") . " " . $rw);
                        redirect("system_settings/currencies");
                    }
                    if ($catd = $this->settings_model->getClusterByName(trim($csv_pr['cluster']))) {
                        $cluster[] = trim($catd->id);
                        $country[] = trim($csv_pr['country']);
                        $french_desc[] =trim($csv_pr['french_desc']);
                        $portuguese_desc[] =trim($csv_pr['portuguese_desc']);
                        $currency_code[] = trim($csv_pr['currency_code']);
                        $currency_name[] =trim($csv_pr['currency_name']);
                        $rate[] =trim($csv_pr['rate']);
                        $autoupdate[]=0;
                        
                    } else {
                        $this->session->set_flashdata('error', lang("Cluster_does_not_exist") . " (" . $csv_pr['cluster'] . "). " . " " . lang("csv_line_no") . " " . $rw);
                       redirect("system_settings/currencies");
                    }

                    $rw++;
                }
            }

            $ikeys = array('cluster', 'country', 'french_name', 'portuguese_name', 'code', 'name', 'rate', 'auto_update');

            $items = array();
            foreach (array_map(null,$cluster, $country, $french_desc, $portuguese_desc, $currency_code, $currency_name, $rate, $autoupdate) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

          //  $this->sma->print_arrays($items);
            
             if ($this->settings_model->add_currencies($items)) {
            $this->session->set_flashdata('message', lang("country_currencies_imported"));
            redirect('system_settings/currencies');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('import_products_by_csv')));
            $meta = array('page_title' => lang('import_countries_by_csv'), 'bc' => $bc);
            $this->page_construct('settings/currencies', $meta, $this->data); //redirect("system_settings/import_currency");

        }
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
            ->select("id, image, code, name,gbu")
            ->from("categories")
            ->add_column("Actions", "<center> <a href='" . site_url('system_settings/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_category") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_category") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

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

                $keys = array('brand','bu');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    //print_r($csv_pr);
                    $countryy=$this->settings_model->getCategoryName(trim($csv_pr['brand']));
                    if ($countryy) {
                        $errorlog.= "Check brand " . " :" .$csv_pr['brand'] . ":" . " already exists" . " " . lang("line_no") . " " . $rw."\n";
                        //r//edirect("system_settings/categories");
                    }
                    
                        $name[] = trim($csv_pr['brand']);
                       $code[]=$rw;
                        
                   

                    $rw++;
                }
                if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
            }

            $ikeys = array('brand','bu');

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

                $keys = array('brand','bu');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                  
                    $countryy=$this->settings_model->getCategoryName(trim($csv_pr['brand']));
                     
                    if ($countryy) {
                       $errorlog.= "Check brand" . " :" .$csv_pr['brand'] . ": " . " already exists" . " " . lang("line_no") . " " . $rw."\n";
                        //redirect("system_settings/categories");
                    }
                    
                        $name[] = trim($csv_pr['brand']);
                        $bu[] = trim($csv_pr['bu']);
                       $code[]=$rw;
                        
                   

                    $rw++;
                }
                if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
            }

            $ikeys = array('name','code','gbu');

            $items = array();
            foreach (array_map(null,$name,$code,$bu) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

         //$this->sma->print_arrays($items);
           // die();
             if ($this->settings_model->add_Category($items)) {
            $this->session->set_flashdata('message', lang("Brands_imported"));
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
    function msr()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('MSR')));
        $meta = array('page_title' => lang('MSR'), 'bc' => $bc);
        $this->page_construct('settings/msr', $meta, $this->data);
    }
    function getMsr()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id,msr_alignment_name,team_name")
            ->from("msr_alignments")
            ->add_column("Actions", "<center> <a href='" . site_url('system_settings/edit_msr/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("Edit_MSR") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_Msr") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_msr/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        echo $this->datatables->generate();
    }
     function dsm()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Teams')));
        $meta = array('page_title' => lang('DSM_Alignment'), 'bc' => $bc);
        $this->page_construct('settings/dsm', $meta, $this->data);
    }
        function getDsm()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id,dsm_alignment_name,country,business_unit")
            ->from("dsm_alignments")
            ->add_column("Actions", "<center> <a href='" . site_url('system_settings/edit_dsm/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("Edit_DSM") . "'><i class=\"fa fa-edit\"></i></a><a class=\"tip\" title='" . $this->lang->line("DSM_Team_mapping") . "' href='" . site_url('system_settings/import_dsmteammapping/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_DSM") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_team/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        echo $this->datatables->generate();
    }
    function import_dsmteammapping($id){
                   $dsmteam=array();
          $dsmteam = $this->settings_model->getDSMteams($id);
        
        $dsm=$this->settings_model->getDsmByID($id);
        $countries=$this->settings_model->getAllCurrencies();
        
        $this->data['countries']=  $countries;
         $this->data['teams']=  $this->settings_model->getTeamsNoDSMForCountryID($dsm->country_id);
         $this->data['teamsaall']=  $this->settings_model->getAllTeams();
        $this->data['dsm_id']=$dsm->id;
        $this->data['dsm_name']=$dsm->dsm_alignment_name;
        $this->data['dsmteam_mapping']=$dsmteam;
        $this->data['page_title'] = lang('Teams');
        $this->load->view($this->theme.'settings/import_dsm_teams',$this->data);
        
    }
    function teams()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Teams')));
        $meta = array('page_title' => lang('Team'), 'bc' => $bc);
        $this->page_construct('settings/teams', $meta, $this->data);
    }
    
     function conversion()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Exchange_Rate')));
        $meta = array('page_title' => lang('Exchange_Rate'), 'bc' => $bc);
        $this->page_construct('settings/conversion', $meta, $this->data);
    }
    
        function delete_dsm_mapping(){
        
         $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('dp_id')) {
            $id = $this->input->get('dp_id');
            
            $this->db->where('id', $id);
            //echo $id
             if ($this->db->delete("dsm_team_mapping", array('id' => $id))) {
           die("DSM Team mapping removed");
        }
           else{
            die("Could not delete,check parameters!!");
        } 
            
        }else{
            die("Could not delete,check parameters!");
        }
    }
    
        function add_dsm_team_mapping(){
        
         $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('newteam')) {
            
            $newteamid =  $this->input->get('newteam');
            $dsm_id = $this->input->get('dsm_id');
           
            $teamdet = $this->settings_model->getTeamByID($newteamid);
            $dsmdet = $this->settings_model->getDsmByID($dsm_id);
            $data=array("team_id"=>$newteamid,
            "team_name"=>$teamdet->name,
            "dsm_alignment_id"=>$dsm_id,
            "dsm_alignment_name"=>$dsmdet->dsm_alignment_name);
            
        if ($this->db->insert("dsm_team_mapping",$data)) {
                die("DSM Team mapping added");
        }
           else {
            die("Could not add,check parameters!!");
        } 
            
        }   else {
            die("Could not add,check parameters!");
        }
    }
     function import_dsm_mapping_csv1()
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
                    redirect("system_settings/dsm");
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

                $keys = array( 'dsm', 'team');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {

                 
                    	if ($dsm_details = $this->site->getdsmByName($csv['dsm'])) {
						}else{
							$this->session->set_flashdata('error', $this->lang->line("DSM_Alignment_Not_Found") . " ( " . $csv['dsm'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
						}
					
						if ($team_details = $this->site->getTeamByName($csv['team'])) {
						}else{
							$this->session->set_flashdata('error', $this->lang->line("Team_Not_Found") . " ( " . $csv['team'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
						}
						 
						
						 $rw++;
						    
                   // $data[] = $csv;
                    $data = array('team_id' => $team_details->id,
                    'team_name' => $csv['team'],
                    'dsm_alignment_id' => $dsm_details->id,
                    'dsm_alignment_name' => $csv['dsm']
                    );
                   
                   
                }
                
               // $this->sma->print_arrays($data);
            }

        } elseif ($this->input->post('import_mapping')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('system_settings/dsm');
        }
        
    //REMOVE DATA THAT HAD BEEN UPLOADED
        
        if ($this->form_validation->run() == true && !empty($data)) {
          //  $this->customers->remove_customermsrdata();
            if ($this->companies_model->addDSMTEAMAlignmentsBatch($data)) {
                $this->session->set_flashdata('message', $this->lang->line("DSM_Team_Mapping Added"));
                redirect('system_settings/dsm');
            }
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'system_settings/dsm', $this->data);
        }
    }
    
     function import_dsm_mapping_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
         $this->load->model('companies_model');
//die(print_r($_FILES));
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
                    redirect("system_settings/dsm");
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

               // $keys = array('country','distributor','distributor_customer_name','sanofi_customer_name');
                $keys = array( 'dsm', 'team');
                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
             // $this->sma->print_arrays($final);
                $rw = 2; ///row to start collecting data
                foreach ($final as $csv_pr) {
                  //  echo "Trying to import <br>";
                    
                    $trimmeddsmname=  str_replace(" ","",$csv_pr['dsm']);
                    $trimmedteamname=  str_replace(" ","",$csv_pr['team']);

                    if ($dsm_details = $this->site->getdsmByName($trimmeddsmname)) {
						}else{
							$this->session->set_flashdata('error', $this->lang->line("DSM_Alignment_Not_Found") . " ( " . $csv['dsm'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
						}
                    if ($team_details = $this->site->getTeamByName($trimmedteamname)) {
						}else{
							$this->session->set_flashdata('error', $this->lang->line("Team_Not_Found") . " ( " . $csv['team'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
						}
                   //print_r($team_details->country);
                   //die();
                    $team_id[]=$team_details->id;
                        $team_name[] = $trimmedteamname;
                    $dsm_id[] = $dsm_details->id;
                        $dsm_name[] = $trimmeddsmname;
                       
                    $rw++;
                }
            }

            $ikeys = array('team_id','team_name', 'dsm_alignment_id', 'dsm_alignment_name');

            $items = array();
            foreach (array_map(null,$team_id,$team_name,$dsm_id, $dsm_name) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }

          //$this->sma->print_arrays($items);
           // die();
             if ($this->settings_model->addDSMTEAMAlignmentsBatch($items)) {
            $this->session->set_flashdata('message', $this->lang->line("DSM_Team_Mapping Added"));
                redirect('system_settings/dsm');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//die(print_r($this->data['error']));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_dsm_mapping_by_csv')));
            $meta = array('page_title' => lang('map_dsm_team'), 'bc' => $bc);
            $this->page_construct('system_settings/dsm', $meta, $this->data); //redirect("system_settings/import_currency");

        }
        }

       
    }
    
function getConversionrates()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("id,currency_code,conversion_rate,DATE_FORMAT(month,'%m-%Y') as month")
            ->from("conversion")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_conversion/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("Edit_Rate") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_Exchange_Rate") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_conversion/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        echo $this->datatables->generate();
    }
	
    
    function getTeams()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id,name")
            ->from("team")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/team_members/$1') . "' class='tip' title='" . lang("List_Team_Members") . "'><i class=\"fa fa-list\"></i></a> <a href='" . site_url('system_settings/edit_team/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("Edit_Team") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_Team") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_team/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");

        echo $this->datatables->generate();
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
      function add_conversion()
    {

        
            $this->data['modal_js'] = $this->site->modal_js();
             $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['bu']=  $this->site->getAllBu();
            $this->load->view($this->theme . 'settings/add_conversion', $this->data);
        
    }
      function add_team()
    {

        
            $this->data['modal_js'] = $this->site->modal_js();
             $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['bu']=  $this->site->getAllBu();
            $this->load->view($this->theme . 'settings/add_team', $this->data);
        
    }
          function add_Dsm()
    {

        
            $this->data['modal_js'] = $this->site->modal_js();
             $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['teams']=  $this->settings_model->getAllTeams();
              $this->data['bu']=  $this->site->getAllBu();
            $this->load->view($this->theme . 'settings/add_dsm', $this->data);
        
    }
    function add_Msr()
    {

        
            $this->data['modal_js'] = $this->site->modal_js();
             $this->data['countries']=  $this->settings_model->getAllCurrencies();
             $this->data['teams']=  $this->settings_model->getAllTeams();
              $this->data['bu']=  $this->site->getAllBu();
            $this->load->view($this->theme . 'settings/add_msr', $this->data);
        
    }
     function import_msr()
    {

        
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_msr_csv', $this->data);
        
    }
    
    function delete_cluster($id = NULL)
    {

        if ($this->settings_model->deleteCluster($id)) {
            echo lang("Cluster_deleted");
        }
    }
    
     function delete_team($id = NULL)
    {

        if ($this->settings_model->deleteTeam($id)) {
            echo lang("Team_deleted");
        }
    }
    function delete_dsm($id = NULL)
    {

        if ($this->settings_model->deleteDsm($id)) {
            echo lang("DSM_deleted");
        }
    }
    
    function delete_msr($id = NULL)
    {

        if ($this->settings_model->deleteMsr($id)) {
            echo lang("MSR_deleted");
        }
    }
    function delete_conversion($id = NULL)
    {

        if ($this->settings_model->delete_conversion($id)) {
            echo lang("Exchange_rate_deleted");
        }
    }

    function post_conversion(){
        $this->form_validation->set_rules('exchange_rate', lang("exchange_rate"), 'trim|required');
       $month =  '01-'.$this->input->post('csmonth');
      $date = date('Y-m-d',strtotime($month));
        if ($this->form_validation->run() == true) {
            $data = array('currency_code' => $this->input->post('currency_codee'),
                        'conversion_rate' => $this->input->post('exchange_rate'),
                        'month' => $date
                                );
         }

        if ($this->form_validation->run() == true && $this->settings_model->addExchangeRate($data)) {
            $this->session->set_flashdata('message', lang("Exchange_Rate_added"));
            redirect("system_settings/conversion");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
  redirect("system_settings/conversion");
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
    
function post_msr(){
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[msr_alignments.msr_alignment_name]|required');
       $this->form_validation->set_rules('team_name', lang("Team_name"), 'required|min_length[1]');
       $countrydet=  $this->settings_model->getCurrencyByID($this->input->post('country'));
       $teamdet = $this->settings_model->getTeamByID($this->input->post('team_name'));
        if ($this->form_validation->run() == true) {
            $data = array('msr_alignment_name' => $this->input->post('name'),
                        'country_id' => $this->input->post('country'),
                        'country' => $countrydet->country,
                        'team_id' => $this->input->post('team_name'),
                        'team_name' => $teamdet->name,
                        'business_unit' => $this->input->post('business_unit')
                        
                        );
                 
        }

        if ($this->form_validation->run() == true && $this->settings_model->addMSR($data)) {
            $this->session->set_flashdata('message', lang("MSR_added"));
            redirect("system_settings/msr");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
  redirect("system_settings/msr");
    }
    }
    
  function post_dsm(){
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[dsm_alignments.dsm_alignment_name]|required');
       $countrydet=  $this->settings_model->getCurrencyByID($this->input->post('country'));
       $teamdet = $this->settings_model->getTeamByID($this->input->post('team_name'));
        if ($this->form_validation->run() == true) {
            $data = array('dsm_alignment_name' => $this->input->post('name'),
                        'country_id' => $this->input->post('country'),
                        'country' => $countrydet->country,
                        'business_unit' => $this->input->post('business_unit')
                        
                        );
      
                 
        }

        if ($this->form_validation->run() == true && $this->settings_model->addDSM($data)) {
            $this->session->set_flashdata('message', lang("DSM_added"));
            redirect("system_settings/dsm");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
  redirect("system_settings/dsm");
    }
    }
    
  function post_team(){
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[team.name]|required');
       
        $countrydet=  $this->settings_model->getCurrencyByID($this->input->post('country'));
        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'),
                        'country_id'=>$this->input->post('country'),
                        'country'=>$countrydet->country,
                        'business_unit' => $this->input->post('business_unit'));
                 
        }

        if ($this->form_validation->run() == true && $this->settings_model->addTeam($data)) {
            $this->session->set_flashdata('message', lang("Team_added"));
            redirect("system_settings/teams");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
  redirect("system_settings/teams");
    }
    }
    
    
    function add_category($id = NULL)
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[1]');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $name = $this->input->post('name');
            $gbu =  $this->input->post('bu');
           // $name = $this->settings_model->getStimaCategoryByID($this->input->post('name')); 
           // $name=$name->name;
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

        if ($this->form_validation->run() == true && $this->settings_model->addCategory($name, $code,$gbu, $photo)) {
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
            $this->data['bu']=  $this->site->getAllBu();
            $this->data['lastid'] = $this->settings_model->getmaxbrandcode();
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
                'name' => $this->input->post('name'),'gbu' => $this->input->post('gbu')
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
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Bu'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCategoryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->gbu);
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

 function team_actions()
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
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('Team_Name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Country'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Business_Unit'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getTeamByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->country);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->business_unit);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'teams_' . date('Y_m_d_H_i_s');
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

 function dsm_actions()
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
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('Title'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Country'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Business_Unit'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getDsmByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->dsm_alignment_name 	);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->country);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->business_unit);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'dsm_' . date('Y_m_d_H_i_s');
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
    function msr_actions()
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
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('Title'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Country'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Business_Unit'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('Team'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getMsrByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->msr_alignment_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->country);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->business_unit);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sc->team_name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'msr_' . date('Y_m_d_H_i_s');
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
     function add_country_pricing($id)
    {
        $this->load->helper('security');
        $this->load->model('country_productpricing_model');
        $this->form_validation->set_rules('unified_price', lang("unified_price"), 'trim|required');
        $this->form_validation->set_rules('supply_price', lang("supply_price"), 'trim|required');
        $id=$this->input->get('id');
        
      
        //die($id."xsdds");
      
     
        if ($this->form_validation->run() == true && $this->input->post('product_id')) {
           
           //die(print_r($this->input->post()));
            $prd=$this->products_model->getProductByID($this->input->post('product_id'));
$products=array();
            $data = array(
                'product_id'=>$this->input->post('product_id'),
                'country_id'=>$this->input->post('thiscountry'),
                'product_name' =>$prd->name,
                'unified_price' => $this->input->post('unified_price'),
                'tender_price' => $this->input->post('tender_price'),  
                'supply_price' => $this->input->post('supply_price'),
                'resell_price' => $this->input->post('resell_price'),
                'distributor_id' => $this->input->post('customer'),
                'customer_id' => $this->input->post('ssocustomer'),
                'special_resell_price'=>$this->input->post('sp_resellprice'),
                'special_tender_price' =>$this->input->post('sp_tenderprice'),
                'promotion' => $this->input->post('promotion'),  
                'from_date' => $this->input->post('from_date'), 
                'to_date' => $this->input->post('to_date') 
                
            );
            array_push($products, $data);
        } elseif ($this->input->post('add_country_pricing')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/country_pricing/".$this->input->post('thiscountry'));
        }

        if ($this->form_validation->run() == true && $this->country_productpricing_model->addProductPricing($products)) {
            $this->session->set_flashdata('message', lang("pricing_updated"));
            redirect("system_settings/country_pricing/".$this->input->post('thiscountry'));
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
           
             $prds=$this->products_model->getAllProducts();
             foreach ($prds as $value) {
                 $productt[$value->id]=$value->name."(".$value->code.")";
             }
             $this->data['sanoficustomer']=$this->site->getAllCustomerCustomers();
             $this->data['distrib']=$this->site->getAllCustomerCompanies();
               $this->data['product_ids'] = $productt;
            $this->data['unifiedprice'] = array('name' => 'unified_price',
                'id' => 'code',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('unified_price', '0'),
            );
             $this->data['resellprice'] = array('name' => 'resell_price',
                'id' => 'resellprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('resellprice','0'),
            );
              
             $this->data['supplyprice'] = array('name' => 'supply_price',
                'id' => 'supplyprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('supply_price', '0'),
            );
             
             $this->data['tenderprice'] = array('name' => 'tender_price',
                'id' => 'tenderprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('tender_price', '0'),
            );
             $this->data['sp_tenderprice'] = array('name' => 'sp_tenderprice',
                'id' => 'sp_tenderprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('sp_tenderprice', '0'),
            );
             $this->data['sp_resellprice'] = array('name' => 'sp_resellprice',
                'id' => 'sp_resellprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('sp_resellprice', '0'),
            );
            
              $this->data['promotion'] = array("non-promoted"=>"non-promoted","promoted"=>"promoted");
             $this->data['fromdate'] = array('name' => 'from_date',
                'id' => 'fromdate',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                 'placeholder'=>'mm/YY',
                'value' => $this->form_validation->set_value('from_date', $pr_details->from_date),
            );
             
             $this->data['todate'] = array('name' => 'to_date',
                'id' => 'todate',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                  'placeholder'=>'mm/YY',
                'value' => $this->form_validation->set_value('to_date', $pr_details->to_date),
            );

            $this->data['modal_js'] = $this->site->modal_js();
            
            $this->data['id'] =$id;
            $this->load->view($this->theme . 'settings/add_country_pricing', $this->data);
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
                'tender_price' => $this->input->post('tender_price'),
                'distributor_id' => $this->input->post('customer'),
                'customer_id' => $this->input->post('ssocustomer'),
                'special_resell_price'=>$this->input->post('sp_resellprice'),
                'special_tender_price' =>$this->input->post('sp_tenderprice'),
                'promotion' => $this->input->post('promotion'),  
                'from_date' => $this->input->post('from_date'), 
                'to_date' => $this->input->post('to_date') 
                
            );
            
        } elseif ($this->input->post('edit_country_pricing')) {
            $this->session->set_flashdata('error', validation_errors());
          redirect($_SERVER["HTTP_REFERER"]);
           // redirect("system_settings/country_pricing_categorized");
        }

        if ($this->form_validation->run() == true && $this->country_productpricing_model->updatePrice($id,$data)) {
            $this->session->set_flashdata('message', lang("pricing_updated"));
            redirect("system_settings/country_pricing_categorized");
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
           
            $this->data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'readonly' => 'true',
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
              $this->data['tender'] = array('name' => 'tender_price',
                'id' => 'tenderprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('tenderprice', $pr_details->tender_price),
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
            $this->data['sp_tenderprice'] = array('name' => 'sp_tenderprice',
                'id' => 'sp_tenderprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('sp_tenderprice', $pr_details->special_tender_price),
            );
             $this->data['sp_resellprice'] = array('name' => 'sp_resellprice',
                'id' => 'sp_resellprice',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('sp_resellprice', $pr_details->special_resell_price),
            );
            $this->data['sanoficustomer']=$this->site->getAllCustomerCustomers();
            $this->data['distrib']=$this->site->getAllCustomerCompanies();
            $this->data['pricedetails'] = $pr_details;
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


    function edit_conversion($id = NULL)
    {

        $this->form_validation->set_rules('edi_exchange_rate', lang("name"), 'trim|required');
       // $this->form_validation->set_rules('code', lang("code"), 'required');
        $this->form_validation->set_rules('edi_csmonth', lang("date"), 'required');
       // $this->form_validation->set_rules('rate', lang("tax_rate"), 'required|numeric');
        $month =  '01-'.$this->input->post('edi_csmonth');
        $date = date('Y-m-d',strtotime($month));
        if ($this->form_validation->run() == true) {

            $data = array('conversion_rate' => $this->input->post('edi_exchange_rate'),
                'month' => $date,
                'currency_code' => $this->input->post('edi_currency_codee'),
                            );
        } elseif ($this->input->post('edit_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/conversion");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateConversion($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("Exchange_rate_updated"));
            redirect("system_settings/conversion");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['exchange_rate'] = $this->settings_model->getConversionByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_conversion', $this->data);
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
    
    
      function alignment_groups()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('alignment_groups')));
        $meta = array('page_title' => lang('alignment_groups'), 'bc' => $bc);
        $this->page_construct('settings/alignment_groups', $meta, $this->data);
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
    
    
        function getAlignmentGroups()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, person_name, region, country")
            ->from("alignment")
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_alignment/$1') . "' class='tip' title='" . lang("edit_customer_group1") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_customer_group1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_alignment/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
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
    
    
       function add_alignment()
    {

        $this->form_validation->set_rules('alignment_name', lang("group_name1"), 'trim|is_unique[alignment.alignment_name]|required');
        $this->form_validation->set_rules('region', lang("group_percentage1"), 'required');
        $this->form_validation->set_rules('country', lang("group_percentage1"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array('alignment_name' => $this->input->post('alignment_name'),
                'region' => $this->input->post('region'),  'country' => $this->input->post('country'),
            );
        } elseif ($this->input->post('add_alignment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/alignment_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addAlignment($data)) {
            $this->session->set_flashdata('message', lang("customer_group_added1"));
            redirect("system_settings/alignment_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_alignment', $this->data);
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
            redirect("system_settings/alignment_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['customer_group'] = $this->settings_model->getAlignmentByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_alignment', $this->data);
        }
    }



    function delete_customer_group($id = NULL)
    {
        if ($this->settings_model->deleteCustomerGroup($id)) {
            echo lang("customer_group_deleted");
        }
    }
    
    
        function delete_alignment($id = NULL)
    {
        if ($this->settings_model->deleteAlignment($id)) {
            echo lang("customer_group_deleted1");
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
    
    
    
      function alignment_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteAlignment($id);
                    }
                    $this->session->set_flashdata('message', lang("customer_group_deleted1"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('alignments_groups'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('group_name1'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('group_percentage1'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $pg = $this->settings_model->getAlignmentByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pg->alignment_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pg->region/country);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'Alignments' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("No Alignments Selected"));
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

    function delete_warehouse($id = NULL)
    {
        if ($this->settings_model->deleteWarehouse($id)) {
            echo lang("warehouse_deleted");
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
            ->add_column("Actions", "<center><a href='" . site_url('system_settings/edit_variant/$1') . "' class='tip' title='" . lang("edit_variant") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_variant") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('system_settings/delete_variant/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
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
    
    
    //emails
    function send_notification_email(){
        $temp_path = is_dir('./themes/' . $this->theme . '/views/email_templates/');
                                $theme = $temp_path ? $this->theme : 'default';
                                $msg = file_get_contents('./themes/' . $theme . '/views/email_templates/payment.html');
                               // $message = $this->parser->parse_string($msg, $parse_data);
                                $this->sma->send_email($paypal->account_email, 'Payment has been made via Paypal', $msg);
        
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
