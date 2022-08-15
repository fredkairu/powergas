<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases extends MY_Controller {

    function __construct() {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('purchases', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('purchases_model');
        $this->load->model('products_model');
        $this->load->model('country_productpricing_model');
        $this->load->model('settings_model');
        $this->load->model('sales_model');
        $this->load->model('vehicles_model');
        $this->load->model('distributor_product_model');
        $this->load->model('companies_model');
        $this->load->model('auth_model');
        $this->load->library('ion_auth');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '4096';
        $this->data['logo'] = true;
    }

    /* ------------------------------------------------------------------------- */

    function index($warehouse_id = NULL) {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('purchases')));
        $meta = array('page_title' => lang('purchases'), 'bc' => $bc);
        $this->page_construct('purchases/index', $meta, $this->data);
    }

    function getPurchases($warehouse_id = NULL) {
        $this->sma->checkPermissions('index');

//        if (!$this->Owner && !$warehouse_id) {
//            $user = $this->site->getUser();
//            $warehouse_id = $user->warehouse_id;
//        }
        $detail_link = anchor('purchases/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('purchase_details'));
        $payments_link = anchor('purchases/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('purchases/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('purchases/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_purchase'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link = anchor('purchases/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_purchase'));
        $pdf_link = anchor('purchases/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_purchase") . "</b>' data-content=\"<p>"
                . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('purchases/delete/$1') . "'>"
                . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
                . lang('delete_purchase') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
           
            <li>' . $edit_link . '</li>
            <li>' . $pdf_link . '</li>
            <li>' . $email_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
          $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->load->library('datatables');
        if ($warehouse_id) {
            if ($this->Owner || $this->Admin) {
            $this->datatables
                    ->select("sma_purchases.id as id, DATE_FORMAT(sma_purchases.date,'%m-%Y') as date,pi.country,pi.product_code as gmid,pi.product_name,cr.name as categoryname,cr.gbu as b_u,sma_purchases.supplier,pi.quantity,pi.shipping as resaletotal")
                    ->from('purchases')
                    ->join('purchase_items pi', 'pi.purchase_id=purchases.id', 'left')
                    ->join('products pr', 'pr.id=pi.product_id', 'left')
                    ->join('categories cr', 'cr.id=pr.category_id', 'left')
                    ->where('purchases.warehouse_id', $warehouse_id);
            }else{
                
                $this->datatables
                    ->select("sma_purchases.id as id, DATE_FORMAT(sma_purchases.date,'%m-%Y') as date,pi.country,pi.product_code as gmid,pi.product_name,cr.name as categoryname,cr.gbu as b_u,sma_purchases.supplier,pi.quantity,pi.shipping as resaletotal")
                    ->from('purchases')
                    ->where('supplier_id',$distributor->id)
                    ->join('purchase_items pi', 'pi.purchase_id=purchases.id', 'left')
                    ->join('products pr', 'pr.id=pi.product_id', 'left')
                    ->join('categories cr', 'cr.id=pr.category_id', 'left')
                    ->where('purchases.warehouse_id', $warehouse_id);
            }
        } else {
            if ($this->Owner || $this->Admin) {
            $this->datatables
                    ->select("sma_purchases.id as id, DATE_FORMAT(sma_purchases.date,'%m-%Y') as date,pi.product_code as gmid,pi.product_name,sma_purchases.supplier,pi.quantity")
                    ->join('purchase_items pi', 'pi.purchase_id=purchases.id', 'left')
                    ->join('products pr', 'pr.id=pi.product_id', 'left')
                    ->from('purchases');
            }else{
                $this->datatables
                    ->select("sma_purchases.id as id, DATE_FORMAT(sma_purchases.date,'%m-%Y') as date,pi.product_code as gmid,pi.product_name,sma_purchases.supplier,pi.quantity")
                    ->where('supplier_id',$distributor->id)
                    ->join('purchase_items pi', 'pi.purchase_id=purchases.id', 'left')
                    ->join('products pr', 'pr.id=pi.product_id', 'left')
                    ->from('purchases');
            }
        }
        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } else
        if ($this->Supplier) {
            $this->datatables->where('supplier_id', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    /* ----------------------------------------------------------------------------- */

    function modal_view($purchase_id = NULL) {
        $this->sma->checkPermissions('index', TRUE);

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);
        $this->sma->view_rights($inv->created_by, TRUE);
        $this->data['rows'] = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payments'] = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : NULL;

        $this->load->view($this->theme . 'purchases/modal_view', $this->data);
    }

    function view($purchase_id = NULL) {
        $this->sma->checkPermissions('index');

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);
        //$this->sma->view_rights($inv->created_by);
        $this->data['rows'] = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payments'] = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : NULL;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_purchase_details'), 'bc' => $bc);
        $this->page_construct('purchases/view', $meta, $this->data);
    }

    /* ----------------------------------------------------------------------------- */

//generate pdf and force to download

    function pdf($purchase_id = NULL, $view = NULL, $save_bufffer = NULL) {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);
        $this->sma->view_rights($inv->created_by);
        $this->data['rows'] = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['inv'] = $inv;
        $name = $this->lang->line("purchase") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'purchases/pdf', $this->data, TRUE);
        if ($view) {
            $this->load->view($this->theme . 'purchases/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }
    }

    function email($purchase_id = NULL) {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);
        $this->form_validation->set_rules('to', $this->lang->line("to") . " " . $this->lang->line("email"), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', $this->lang->line("subject"), 'trim|required');
        $this->form_validation->set_rules('cc', $this->lang->line("cc"), 'trim');
        $this->form_validation->set_rules('bcc', $this->lang->line("bcc"), 'trim');
        $this->form_validation->set_rules('note', $this->lang->line("message"), 'trim');

        if ($this->form_validation->run() == true) {
            $this->sma->view_rights($inv->created_by);
            $to = $this->input->post('to');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = NULL;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = NULL;
            }
            $supplier = $this->site->getCompanyByID($inv->supplier_id);
            $this->load->library('parser');
            $parse_data = array(
                'reference_number' => $inv->reference_no,
                'contact_person' => $supplier->name,
                'company' => $supplier->company,
                'site_link' => base_url(),
                'site_name' => $this->Settings->site_name,
                'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '" alt="' . $this->Settings->site_name . '"/>'
            );
            $msg = $this->input->post('note');
            $message = $this->parser->parse_string($msg, $parse_data);
            $attachment = $this->pdf($purchase_id, NULL, 'S');
        } elseif ($this->input->post('send_email')) {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sma->send_email($to, $subject, $message, NULL, NULL, $attachment, $cc, $bcc)) {
            delete_files($attachment);
            $this->db->update('purchases', array('status' => 'ordered'), array('id' => $purchase_id));
            $this->session->set_flashdata('message', $this->lang->line("email_sent"));
            redirect("reports/purchases");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if (file_exists('./themes/' . $this->theme . '/views/email_templates/purchase.html')) {
                $purchase_temp = file_get_contents('themes/' . $this->theme . '/views/email_templates/purchase.html');
            } else {
                $purchase_temp = file_get_contents('./themes/default/views/email_templates/purchase.html');
            }
            $this->data['subject'] = array('name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject', 'Purchase Order (' . $inv->reference_no . ') from ' . $this->Settings->site_name),
            );
            $this->data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note', $purchase_temp),
            );
            $this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);

            $this->data['id'] = $purchase_id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'purchases/email', $this->data);
        }
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    function add($quote_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        //$this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');

        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('po');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id = 1;
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $deduct_volume = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = sizeof($_POST['product']);
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : NULL;
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : NULL;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : NULL;
                $item_expiry = (isset($_POST['expiry'][$r]) && ! empty($_POST['expiry'][$r])) ? $this->sma->fsd($_POST['expiry'][$r]) : NULL;

                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchases_model->getProductByCode($item_code);
                    if($product_details->type=="gas"){
                        $deduct_volume+=$deduct_volume+($item_quantity*$product_details->kgs);
                    }
                    if($item_expiry) {
                        $today = date('Y-m-d');
                        if($item_expiry <=  $today) {
                            $this->session->set_flashdata('error', lang('product_expiry_date_issue').' ('.$product_details->name.')');
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    $unit_cost = $real_unit_cost;
                    $pr_discount = 0;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = (($this->sma->formatDecimal($unit_cost)) * (Float)($pds[0])) / 100;
                        } else {
                            $pr_discount = $this->sma->formatDecimal($discount);
                        }
                    }

                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_tax = 0; $pr_item_tax = 0; $item_tax = 0; $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $pr_tax = $item_tax_rate;
                        $tax_details = $this->site->getTaxRateByID($pr_tax);
                        if ($tax_details->type == 1 && $tax_details->rate != 0) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_cost = $unit_cost - $item_tax;
                            }

                        } elseif ($tax_details->type == 2) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_cost = $unit_cost - $item_tax;
                            }

                            $item_tax = $this->sma->formatDecimal($tax_details->rate);
                            $tax = $tax_details->rate;

                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);

                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_quantity) + $pr_item_tax);

                    $products[] = array(
                        'product_id' => $product_details->id,
                        'product_code' => $item_code,
                        'product_name' => $product_details->name,
                        //'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_cost' => $item_net_cost,
                        'unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax),
                        'quantity' => $item_quantity,
                        'quantity_balance' => $item_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $pr_tax,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'expiry' => $item_expiry,
                        'real_unit_cost' => $real_unit_cost,
                        'date' => date('Y-m-d', strtotime($date)),
                        'status' => $status,
                    );

                    $total += $item_net_cost * $item_quantity;
                }
            }
            
            $this->purchases_model->updateLPGGas(array('deduct_volume'=>$deduct_volume));
            
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('discount')) {
                $order_discount_id = $this->input->post('discount');
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

            if ($this->Settings->tax2 != 0) {
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
            $data = array('reference_no' => $reference,
                'date' => $date,
                'stock_type' => "PSO",
                'supplier_id' => $supplier_id,
                'supplier' => $supplier,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'total' => $this->sma->formatDecimal($total),
                'product_discount' => $this->sma->formatDecimal($product_discount),
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $this->sma->formatDecimal($product_tax),
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id')
            );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($data, $products, $supplier_id)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_added"));
            redirect('purchases');
        } else {

            if ($quote_id) {
                $this->data['quote'] = $this->purchases_model->getQuoteByID($quote_id);
                $items = $this->purchases_model->getAllQuoteItems($quote_id);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if ($row->type == 'combo') {
                        $combo_items = $this->purchases_model->getProductComboItems($row->id, $warehouse_id);
                        foreach ($combo_items as $citem) {
                            $crow = $this->site->getProductByID($citem->product_id);
                            if (!$crow) {
                                $crow = json_decode('{}');
                                $crow->quantity = 0;
                            } else {
                                unset($crow->details, $crow->product_details);
                            }
                            $crow->discount = $item->discount ? $item->discount : '0';
                            $crow->cost = $crow->cost ? $crow->cost : 0;
                            $crow->tax_rate = $item->tax_rate_id;
                            $crow->real_unit_cost = $crow->cost ? $crow->cost : 0;
                            $crow->expiry = '';
                            $options = $this->purchases_model->getProductOptions($crow->id);

                            $ri = $this->Settings->item_addition ? $crow->id : $c;
                            if ($crow->tax_rate) {
                                $tax_rate = $this->site->getTaxRateByID($crow->tax_rate);
                                $pr[$ri] = array('id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . " (" . $crow->code . ")", 'row' => $crow, 'tax_rate' => $tax_rate, 'options' => $options);
                            } else {
                                $pr[$ri] = array('id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . " (" . $crow->code . ")", 'row' => $crow, 'tax_rate' => false, 'options' => $options);
                            }
                            $c++;
                        }
                    } elseif ($row->type == 'standard') {
                        if (!$row) {
                            $row = json_decode('{}');
                            $row->quantity = 0;
                        } else {
                            unset($row->details, $row->product_details);
                        }

                        $row->id = $item->product_id;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->qty = $item->quantity;
                        $row->option = $item->option_id;
                        $row->discount = $item->discount ? $item->discount : '0';
                        $row->cost = $row->cost ? $row->cost : 0;
                        $row->tax_rate = $item->tax_rate_id;
                        $row->expiry = '';
                        $row->real_unit_cost = $row->cost ? $row->cost : 0;
                        $options = $this->purchases_model->getProductOptions($row->id);

                        $ri = $this->Settings->item_addition ? $row->id : $c;
                        if ($row->tax_rate) {
                            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                            $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => $tax_rate, 'options' => $options);
                        } else {
                            $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => false, 'options' => $options);
                        }
                        $c++;
                    }
                }
                $this->data['quote_items'] = json_encode($pr);
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['quote_id'] = $quote_id;
            $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['ponumber'] = ''; //$this->site->getReference('po');
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
            $meta = array('page_title' => lang('add_purchase'), 'bc' => $bc);
            $this->page_construct('purchases/add', $meta, $this->data);
        }
    }

    function add2($quote_id = NULL)
    {
        $this->sma->checkPermissions('add-stock',true,'vehicles');

        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        //$this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('vehicle', $this->lang->line("vehicle"), 'required');

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());

        $this->session->unset_userdata('csrf_token');

        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('po');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id = 2;
            $vehicle_id = $this->input->post('vehicle');

            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;

            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = sizeof($_POST['product']);
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : NULL;
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : NULL;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : NULL;
                $item_expiry = (isset($_POST['expiry'][$r]) && ! empty($_POST['expiry'][$r])) ? $this->sma->fsd($_POST['expiry'][$r]) : NULL;

                $total_qty+=$item_quantity;
                
                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchases_model->getProductByCode($item_code);
                    if($item_expiry) {
                        $today = date('Y-m-d');
                        if($item_expiry <=  $today) {
                            $this->session->set_flashdata('error', lang('product_expiry_date_issue').' ('.$product_details->name.')');
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    $unit_cost = $real_unit_cost;
                    $pr_discount = 0;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = (($this->sma->formatDecimal($unit_cost)) * (Float)($pds[0])) / 100;
                        } else {
                            $pr_discount = $this->sma->formatDecimal($discount);
                        }
                    }

                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_tax = 0; $pr_item_tax = 0; $item_tax = 0; $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $pr_tax = $item_tax_rate;
                        $tax_details = $this->site->getTaxRateByID($pr_tax);
                        if ($tax_details->type == 1 && $tax_details->rate != 0) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_cost = $unit_cost - $item_tax;
                            }

                        } elseif ($tax_details->type == 2) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_cost = $unit_cost - $item_tax;
                            }

                            $item_tax = $this->sma->formatDecimal($tax_details->rate);
                            $tax = $tax_details->rate;

                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);

                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_quantity) + $pr_item_tax);

                    $products[] = array(
                        'product_id' => $product_details->id,
                        'product_code' => $item_code,
                        'product_name' => $product_details->name,
                        //'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_cost' => $item_net_cost,
                        'unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax),
                        'quantity' => $item_quantity,
                        'quantity_balance' => $item_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $pr_tax,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'expiry' => $item_expiry,
                        'real_unit_cost' => $real_unit_cost,
                        'date' => date('Y-m-d', strtotime($date)),
                        'status' => $status,
                    );

                    $total += $item_net_cost * $item_quantity;
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('discount')) {
                $order_discount_id = $this->input->post('discount');
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

            if ($this->Settings->tax2 != 0) {
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
            $data = array('reference_no' => $reference,
                'date' => $date,
                'stock_type' => "SSO",
                'vehicle_id' => $vehicle_id,
                'supplier_id' => $distributor->id,
                'supplier' => $distributor->name,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'total' => $this->sma->formatDecimal($total),
                'product_discount' => $this->sma->formatDecimal($product_discount),
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $this->sma->formatDecimal($product_tax),
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
                'quantity' => $total_qty
            );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase2($data, $products, $vehicle_id, $distributor->id)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_added"));
            redirect('vehicles');
        } else {

            if ($quote_id) {
                $this->data['quote'] = $this->purchases_model->getQuoteByID($quote_id);
                $items = $this->purchases_model->getAllQuoteItems($quote_id);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if ($row->type == 'combo') {
                        $combo_items = $this->purchases_model->getProductComboItems($row->id, $warehouse_id);
                        foreach ($combo_items as $citem) {
                            $crow = $this->site->getProductByID($citem->product_id);
                            if (!$crow) {
                                $crow = json_decode('{}');
                                $crow->quantity = 0;
                            } else {
                                unset($crow->details, $crow->product_details);
                            }
                            $crow->discount = $item->discount ? $item->discount : '0';
                            $crow->cost = $crow->cost ? $crow->cost : 0;
                            $crow->tax_rate = $item->tax_rate_id;
                            $crow->real_unit_cost = $crow->cost ? $crow->cost : 0;
                            $crow->expiry = '';
                            $options = $this->purchases_model->getProductOptions($crow->id);

                            $ri = $this->Settings->item_addition ? $crow->id : $c;
                            if ($crow->tax_rate) {
                                $tax_rate = $this->site->getTaxRateByID($crow->tax_rate);
                                $pr[$ri] = array('id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . " (" . $crow->code . ")", 'row' => $crow, 'tax_rate' => $tax_rate, 'options' => $options);
                            } else {
                                $pr[$ri] = array('id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . " (" . $crow->code . ")", 'row' => $crow, 'tax_rate' => false, 'options' => $options);
                            }
                            $c++;
                        }
                    } elseif ($row->type == 'standard') {
                        if (!$row) {
                            $row = json_decode('{}');
                            $row->quantity = 0;
                        } else {
                            unset($row->details, $row->product_details);
                        }

                        $row->id = $item->product_id;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->qty = $item->quantity;
                        $row->option = $item->option_id;
                        $row->discount = $item->discount ? $item->discount : '0';
                        $row->cost = $row->cost ? $row->cost : 0;
                        $row->tax_rate = $item->tax_rate_id;
                        $row->expiry = '';
                        $row->real_unit_cost = $row->cost ? $row->cost : 0;
                        $options = $this->purchases_model->getProductOptions($row->id);

                        $ri = $this->Settings->item_addition ? $row->id : $c;
                        if ($row->tax_rate) {
                            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                            $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => $tax_rate, 'options' => $options);
                        } else {
                            $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => false, 'options' => $options);
                        }
                        $c++;
                    }
                }
                $this->data['quote_items'] = json_encode($pr);
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['quote_id'] = $quote_id;
            $this->data['vehicles'] = $this->vehicles_model->getAllDistributorsVehicles($distributor->id);
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['ponumber'] = ''; //$this->site->getReference('po');
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
            $meta = array('page_title' => lang('add_purchase'), 'bc' => $bc);
            $this->page_construct('purchases/add2', $meta, $this->data);
        }
    }
    
    function add_gas() {
        $this->sma->checkPermissions('edit-stock',true,'vehicles');
        $this->form_validation->set_rules('supplier', lang("Supplier"), 'required');
        $this->form_validation->set_rules('status', lang("Status"), 'required');
        $this->form_validation->set_rules('volume', lang("Volume"), 'required');
        $this->form_validation->set_rules('cost', lang("Cost"), 'required');
        if ($this->form_validation->run() == true) {
            
            $data = array(
                'supplier_id' => $this->input->post('supplier'),
                'status' => $this->input->post('status'),
                'volume' => $this->input->post('volume'),
                'cost' => $this->input->post('cost'),
                'note' => $this->input->post('note'),
            );
        }
        
        if ($this->form_validation->run() == true && $this->purchases_model->addLPGGas($data)) {
            $this->session->set_flashdata('message', $this->lang->line("purchase_added"));
            redirect('purchases/add_gas');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->companies_model->getAllSupplierCompanies();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('purchases'), 'page' => lang('Suppliers')), array('link' => '#', 'page' => lang('Add_LPG_Purchase')));
            $meta = array('page_title' => lang('add_lpg_purchase'), 'bc' => $bc);
            $this->page_construct('purchases/add_gas', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------------- */

    function edit($id = NULL) {
        $this->sma->checkPermissions('edit-stock',true,'vehicles');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');

        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = NULL;
            }
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $invoiceno = $this->input->post('invoice_no');
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = sizeof($_POST['product']);
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : NULL;
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : NULL;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : NULL;
                $item_expiry = isset($_POST['expiry']) ? $_POST['expiry'] : NULL;
                $quantity_balance = $_POST['quantity_balance'][$r];


                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity) && isset($quantity_balance)) {
                    $product_details = $this->purchases_model->getProductByCode($item_code);
                    $unit_cost = $real_unit_cost;
                    $pr_discount = 0;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = (($this->sma->formatDecimal($unit_cost)) * (Float) ($pds[0])) / 100;
                        } else {
                            $pr_discount = $this->sma->formatDecimal($discount);
                        }
                    }

                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $pr_tax = $item_tax_rate;
                        $tax_details = $this->site->getTaxRateByID($pr_tax);
                        if ($tax_details->type == 1 && $tax_details->rate != 0) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_cost = $unit_cost - $item_tax;
                            }
                        } elseif ($tax_details->type == 2) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / 100);
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_cost) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                                $item_net_cost = $unit_cost - $item_tax;
                            }

                            $item_tax = $this->sma->formatDecimal($tax_details->rate);
                            $tax = $tax_details->rate;
                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_quantity) + $pr_item_tax);

                    $products[] = array(
                        'product_id' => $product_details->id,
                        'product_code' => $item_code,
                        'product_name' => $product_details->name,
                        //'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_cost' => $item_net_cost,
                        'unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax),
                        'quantity' => $item_quantity,
                        'quantity_balance' => $item_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $pr_tax,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'expiry' => $item_expiry,
                        'real_unit_cost' => $real_unit_cost,
                        //'date' => date('Y-m-d', strtotime($date)),
                        'status' => 'Received',
                    );

                    $total += $item_net_cost * $item_quantity;
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('discount')) {
                $order_discount_id = $this->input->post('discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = $this->sma->formatDecimal((($total + $product_tax) * (Float) ($ods[0])) / 100);
                } else {
                    $order_discount = $this->sma->formatDecimal($order_discount_id);
                }
            } else {
                $order_discount_id = NULL;
            }
            $total_discount = $this->sma->formatDecimal($order_discount + $product_discount);

            if ($this->Settings->tax2 != 0) {
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
            $data = array('reference_no' => $reference,
                'supplier_id' => $supplier_id,
                'supplier' => $supplier,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'invoice_no' => $invoiceno,
                'total' => $this->sma->formatDecimal($total),
                'product_discount' => $this->sma->formatDecimal($product_discount),
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $this->sma->formatDecimal($product_tax),
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            if ($date) {
                $data['date'] = $date;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updatePurchase($id, $data, $products)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_updated"));
            redirect('reports/purchases');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $this->purchases_model->getPurchaseByID($id);
           /* if ($this->data['inv']->date <= date('Y-m-d', strtotime('-3 months'))) {
                $this->session->set_flashdata('error', lang("purchase_x_edited_older_than_3_months"));
                redirect($_SERVER["HTTP_REFERER"]);
            }*/
            $inv_items = $this->purchases_model->getAllPurchaseItems($id);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                $row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $item->expiry : '');
                $row->qty = $item->quantity;
                $row->quantity_balance = $item->quantity_balance;
                $row->discount = $item->discount ? $item->discount : '0';
                $options = $this->purchases_model->getProductOptions($row->id);
                $row->option = $item->option_id;
                $row->real_unit_cost = $item->real_unit_cost;
                //$row->cost = $this->sma->formatDecimal($item->shipping + ($item->item_discount / $item->quantity));
                $row->cost = $this->sma->formatDecimal($row->real_unit_cost);
                $row->tax_rate = $item->tax_rate_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $ri = $this->Settings->item_addition ? $row->id : $c;
                if ($row->tax_rate) {
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => $tax_rate, 'options' => $options);
                } else {
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => false, 'options' => $options);
                }
                $c++;
            }

            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            if(!$this->Owner){
                $this->data['vehicles'] = $this->vehicles_model->getAllVehicles();
            }
            $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
            $this->data['purchase'] = $this->purchases_model->getPurchaseByID($id);
            $this->data['purchase_detail'] = $this->purchases_model->getAllPurchaseDetailsByID($id);
            $this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('edit_purchase')));
            $meta = array('page_title' => lang('edit_purchase'), 'bc' => $bc);
            $this->page_construct('purchases/edit', $meta, $this->data);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

    function purchase_by_csv() {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        //$this->form_validation->set_rules('supplier', $this->lang->line("distributor"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";

            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('po'); //.rand(1000,100000)
            // if ($this->Owner || $this->Admin) {
             $sdate = $this->input->post('date');
            $date = "01/" . trim($this->input->post('date'));
             $year=substr($date,-4);
//			
//            $date = date("Y-m-d H:i:s", strtotime($date));
//           $smonth = $this->sma->fld('01/'.$sdate);
            
            // } else {
            //   $date = NULL;
            //}
            $warehouse_id = $this->input->post('warehouse');
            $warehousedet = $this->site->getWarehouseByID($warehouse_id);
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';

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
                    redirect("purchases/purchase_by_csv");
                }



                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 50000, ",")) !== FALSE) {
                        if (array(null) !== $row) {
                        $arrResult[] = $row;
                        }
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

               
                $rw = 2;
               
                //actual stock
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
                
              
           
           
           $saledata=array('stock_type'=>$row[0],'date' =>$row[1],'supplier_id'=>$row[3],'supplier'=>$row[4],'warehouse_id'=>10,'quantity'=>$row[5],'total'=>$row[6],'shipping'=>$row[6],'grand_total'=>$row[6],'country_id'=>$row[7],'country'=>$row[8],'promotion'=>$row[9],'product_id'=>$row[10],'sku'=>$row[11],'brand_id'=>$row[12],'brand_name'=>$row[13],'gbu'=>$row[14],'created'=>date("Y-m-d H:i"));
           
           //die(print_r($saledata));
           $this->db->insert('purchases', $saledata);
            $sale_id = $this->db->insert_id();
                    $product_details = $this->products_model->getProductByID($saledata['product_id']);
            $this->db->insert('purchase_items',array('purchase_id'=>$sale_id,'product_id'=>$saledata['product_id'],'product_code'=>$product_details->code,'product_name'=>$product_details->name,'quantity'=>$saledata['quantity'],'subtotal'=>$saledata['total'],'shipping'=>$saledata['total'],'quantity_balance'=>$saledata['quantity'],'supplier_id'=>$saledata['supplier_id'],'supplier'=>$saledata['supplier'],'country'=>$saledata['country']));
           
            $this->db->insert('consolidated_sales_sso', array('upload_type'=>'STOCK','country'=>$saledata['country'],'monthyear'=>$saledata['date'],'distributor'=>$saledata['supplier'],'distributor_id'=>$saledata['supplier_id'],'promotion'=>$saledata['promotion'],'brand'=>$saledata['brand'],'brand_id'=>$saledata['brand_id'],'bu'=>$saledata['gbu'],'stock_qty'=>$saledata['quantity'],'stock_value'=>$saledata['total'],'purchase_id' =>$sale_id,'product_id'=>$saledata['product_id'],'gmid'=>$product_details->code,'product_name'=>$saledata['sku'],'country_id'=>$saledata['country_id']));
          
           
           
           // $this->db->update('sales', array('updated_sso' =>1), array('id' => $row->id)); 
            }
    $this->session->set_flashdata('message', $this->lang->line("stock_added"));
            redirect("purchases/purchase_by_csv");
}
                }
                
                
                // SSO
		$errorlog="";		
                if (strtolower($warehousedet->name) == "sso") {
                    $keys = array('month', 'country','distributor_name','product','quantity',);
                    $final = array();
                    foreach ($arrResult as $key => $value) {
                        
                        $final[] = array_combine($keys, $value);
                        
                    }

		//die(print_r($final));
                    foreach ($final as $csv_pr) {
						
                        $product_description = str_replace("'", "", $csv_pr['product']);
                        $country_details = $this->sales_model->getCountryByCode($csv_pr['country']);
$sdate1 = $sdate .'-'.$csv_pr['month'].'-'.'01';
                        						 if($country_details->code == 'EURO'){
    $conversionrate = 1;
}else{
  $conver = $this->sales_model->getConversionByMonth($country_details->code,$sdate1);
  if(!$conver){
   $errorlog.=$this->lang->line("Conversion_rate_Not_Found_For :") . " $country_details->code : ".$sdate1." : " . $this->lang->line("Row_number") . " " . $rw."\n";
  }
  $conversionrate = $conver->conversion_rate;
}
                        if (!empty($csv_pr['product'])  && !empty($csv_pr['distributor_name'])) { //&& !empty($csv_pr['quantity'])
//$product_detailss = $this->distributor_product_model->getProductByNameAndSupplier($csv_pr['distributor_product_name'],$supplier_id);
                            $product_detailss = $this->distributor_product_model->getProductByDescription($product_description, $country_details->id, $csv_pr['distributor_name']);
							
                            if (!is_object($product_detailss)) {

                                //$this->session->set_flashdata('error', lang("product_matching_distributor_name_not_found") . " (" . $product_description . " " . $csv_pr['distributor_name'] . ") in Country ".$csv_pr['country'] . lang("line_no") . " " . $rw);
                               // redirect($_SERVER["HTTP_REFERER"]);
                                $errorlog.=lang("product_matching_distributor_name_not_found:") . " " . $product_description . ": " . $csv_pr['distributor_name'] . ": in Country :".$csv_pr['country'] ." :" . lang("line_no") . " " . $rw."\n";
                            }
                            $product_details = $this->products_model->getProductByID($product_detailss->id);
								
                            $item_option = json_decode('{}');
                            $item_option->id = NULL;
                           $catd = $this->settings_model->getCountryByName(trim($csv_pr['country']));
                    if (!$catd) {
                        //$this->session->set_flashdata('error', lang("Country_does_not_exist") . " (" . $csv_pr['country'] . "). " . " " . lang("csv_line_no") . " " . $rw);
                        $errorlog.=lang("Country_does_not_exist") . ":" . $csv_pr['country'] . ": " . " " . lang("csv_line_no") . " " . $rw."\n";
                       // redirect($_SERVER["HTTP_REFERER"]);
                    }
                    $distributor_details=$this->companies_model->getCompanyByNameAndCountry(trim($csv_pr['distributor_name']),$catd->id);
                    //die(print_r($distributor_details));
                    if (!$distributor_details) {
                        //$this->session->set_flashdata('error',"Check distributor" . " (" . $csv_pr['distributor_name'] . ") " . "doesnt exist in given country:" .$csv_pr['country']." ". lang("line_no") . " " . $rw);
                          //redirect($_SERVER["HTTP_REFERER"]);
                        $errorlog.="Check distributor" . ": " . $csv_pr['distributor_name'] . ": " . "doesnt exist in given country:" .$csv_pr['country'].": ". lang("line_no") . " " . $rw."\n";
                    }
                            $supplier_idd = $distributor_details->id;
                            $supplierr = $distributor_details->name;
                            $item_code = $product_details->code;
                            $item_net_cost = $this->sma->formatDecimal($product_details->cost);
                            $item_quantity = $csv_pr['quantity'];
                            $quantity_balance = $csv_pr['quantity'];
                            $item_tax_rate = 0;
                            $item_discount = 0;
                            $item_expiry = 'n/a';//isset($csv_pr['expiry']) ? ($csv_pr['expiry']) : NULL;

                            if (isset($item_discount) && $this->Settings->product_discount) {
                                $discount = $item_discount;
                                $dpos = strpos($discount, $percentage);
                                if ($dpos !== false) {
                                    $pds = explode("%", $discount);
                                    $pr_discount = (($this->sma->formatDecimal($item_net_cost)) * (Float) ($pds[0])) / 100;
                                } else {
                                    $pr_discount = $this->sma->formatDecimal($discount);
                                }
                            } else {
                                $pr_discount = 0;
                            }
                            $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                            $product_discount += $pr_item_discount;

                            if (isset($item_tax_rate) && $item_tax_rate != 0) {

                                if ($tax_details = $this->purchases_model->getTaxRateByName($item_tax_rate)) {
                                    $pr_tax = $tax_details->id;
                                    if ($tax_details->type == 1) {
                                        if (!$product_details->tax_method) {
                                            $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / (100 + $tax_details->rate));
                                            $tax = $tax_details->rate . "%";
                                            $item_net_cost -= $item_tax;
                                        } else {
                                            $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / 100);
                                            $tax = $tax_details->rate . "%";
                                        }
                                    } elseif ($tax_details->type == 2) {
                                        $item_tax = $this->sma->formatDecimal($tax_details->rate);
                                        $tax = $tax_details->rate;
                                    }
                                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);
                                } else {
                                    $this->session->set_flashdata('error', lang("tax_not_found") . " ( " . $item_tax_rate . " ). " . lang("line_no") . " " . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            } elseif ($product_details->tax_rate) {

                                $pr_tax = $product_details->tax_rate;
                                $tax_details = $this->site->getTaxRateByID($pr_tax);
                                if ($tax_details->type == 1) {
                                    if (!$product_details->tax_method) {
                                        $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / (100 + $tax_details->rate));
                                        $tax = $tax_details->rate . "%";
                                        $item_net_cost -= $item_tax;
                                    } else {
                                        $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / 100);
                                        $tax = $tax_details->rate . "%";
                                    }
                                } elseif ($tax_details->type == 2) {

                                    $item_tax = $this->sma->formatDecimal($tax_details->rate);
                                    $tax = $tax_details->rate;
                                }
                                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);
                            } else {
                                $pr_tax = 0;
                                $pr_item_tax = 0;
                                $tax = "";
                            }
                            $product_tax += $pr_item_tax;
//pso unified //sso country resale
                            //if(strtolower($warehousedet->name)=="pso"){
                            $price = $product_details->price; //unified price
                            $datecountrypricing = date("Y-m-d", strtotime($date));
                            $countrydet = $this->sales_model->getCountryByCode($csv_pr['country']);
$dates=array("jan"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","may"=>"05","jun"=>"06","jul"=>"07","aug"=>"08","sept"=>"09","oct"=>"10","nov"=>"11","dec"=>"12",
                            "january"=>"01","february"=>"02","march"=>"03","april"=>"04","may"=>"05","june"=>"06","july"=>"07","august"=>"08","september"=>"09","october"=>"10","november"=>"11","december"=>"12",
                            "1"=>"01","2"=>"02","3"=>"03","4"=>"04","5"=>"05","6"=>"06","7"=>"07","8"=>"08","9"=>"09","10"=>"10","11"=>"11","12"=>"12");
                        $montht=  str_replace("M","",$csv_pr['month']);
                        $month=$dates[strtolower(trim($montht))];  
                        if(!$month){
                          //$this->session->set_flashdata('error',"Check month format" . " (" . $csv_pr['month'] . ")". lang("line_no") . " " . $rw);
                       // redirect($_SERVER["HTTP_REFERER"]); 
                            $errorlog.="Check month format :" . " " . $csv_pr['month'] . ":". lang("line_no") . " " . $rw."\n";
                      
                        }
                            $monthyear="".$month."/".$year;
                             $monthyear1=$year."-".$csv_pr['month']."-01";
                            // $countrypricing=$this->country_productpricing_model->getCountryProductPricing($product_details->id,$csv_pr['country'],$datecountrypricing);  
                            $countrypricing = $this->sales_model->getProductPrices($product_detailss->code, $csv_pr['country'], $monthyear);
                            if (!is_object($countrypricing)) {
                                //$this->session->set_flashdata('error', lang("Country_pricing_for " . $countrydet->country . "_and_period " . $monthyear) . " not found for ( " . $csv_pr['product'] . ":Gmid " . $product_detailss->code . " ). " . lang("line_no") . " " . $rw);
                              // redirect($_SERVER["HTTP_REFERER"]);
                              $errorlog.=lang("Country_pricing_for :" . $countrydet->country . ": _and_period " . $monthyear) . ": not found for : " . $csv_pr['product'] . ": Gmid :" . $product_detailss->code . " :. " . lang("line_no") . " " . $rw."\n";  
                            }
                           // die(print_r($countrypricing));
                            $pricee = $countrypricing->resell_price;
                            if(strtolower($countrypricing->promotion)=="promoted"){$promotion=1;} else{$promotion=0;}
                          
                            $allprices[] = $pricee;
//                              if(count($allprices)==3){
//                                  die(print_r($allprices))
//                              }
/*****we dont need unified price for now**********/
                            $unifiedprice =0;// $product_details->price;
                            $subtotal = $this->sma->formatDecimal((($unifiedprice * $item_quantity))); //unified total
                            $shippingg = $this->sma->formatDecimal(($pricee * $item_quantity)); //resell total
                            //$allshipping[]=$shipping;
//                            if(count($allshipping)==3){
//                                die(print_r($allshipping));
//                            }

					

                            $products[] = array(
                                'product_id' => $product_details->id,
                                'product_code' => $item_code,
                                'product_name' => $product_details->name,
                                'option_id' => $item_option->id,
                                'net_unit_cost' => $item_net_cost,
                                'quantity' => $csv_pr['quantity'],
                                'quantity_balance' => $quantity_balance,
                                'supplier_id'=>$supplier_idd,
                                'supplier'=>$supplierr,
                                'warehouse_id' => $warehouse_id,
                                'item_tax' => $pr_item_tax,
                                'tax_rate_id' => $pr_tax,
                                'tax' => $tax,
                                
                                'discount' => $item_discount,
                                'item_discount' => $pr_item_discount,
                                'expiry' => $item_expiry,
                                'subtotal' => $subtotal,
                                'shipping' => $shippingg,
                                'date' =>$monthyear1,
                                'status' => $status,
                                'country'=>$csv_pr['country'],
                                'unit_cost' => $this->sma->formatDecimal($item_net_cost*$conversionrate + $item_tax),
                                'real_unit_cost' => $this->sma->formatDecimal($item_net_cost*$conversionrate + $item_tax + $pr_discount)
                            );
								   $purchasedata[] = array('reference_no' => $reference,
                        'date' => $monthyear1,
                        'supplier_id' =>$supplier_idd,
                        'supplier' => $supplierr,
                        'stock_type'=>'SSO',
                        'warehouse_id' => $warehouse_id,
                        'note' => $note,
                        'invoice_no' => $reference,
                        'total' => $this->sma->formatDecimal($subtotal),
                        'product_discount' => $this->sma->formatDecimal($item_discount),
                        'order_discount_id' => 0,
                        'order_discount' => 0,
                            'promotion'=>$promotion,
                        'total_discount' => $item_discount,
                        'product_tax' => $this->sma->formatDecimal($tax),
                        'order_tax_id' => $$pr_tax,
                        'order_tax' => $tax,
                        'total_tax' => $tax,
                        'shipping' => $this->sma->formatDecimal($shippingg*$conversionrate),
                        'grand_total' => $subtotal*$conversionrate,
                        'status' => $status,
                         'country_id'=>$catd->id,
                        'country'=>$csv_pr['country'],
                        'created_by' => $this->session->userdata('user_id')
                    );	
                            $total += $item_net_cost * $item_quantity;
                            unset($product_detailss);
                        }
                        else {
                            //$this->session->set_flashdata('error', $this->lang->line("pr_not_found_or_quantity_zero_or_no_distributor_naming") . " ( " . $csv_pr['product'] . " ). " . $this->lang->line("line_no") . " " . $rw);
                           // redirect($_SERVER["HTTP_REFERER"]);
                            $errorlog.= $this->lang->line("pr_not_found_or_quantity_zero_or_no_distributor_naming") . ":  " . $csv_pr['product'] . " : " . $this->lang->line("line_no") . " " . $rw."\n";
                            
                        }
                        $rw++;
                    }
                    
                    //upload now
                                        if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
                  
               // print_r($purchasedata);
              ///  die();
                 foreach ($products as $data) {
                     $this->purchases_model->deletePurchaseBySupplierDateAndType($data['supplier_id'],$data['date'],'SSO');
                 }
                    //foreach ($products as $data) {
                        //$this->purchases_model->deletePurchaseBySupplierDateAndType($data['supplier_id'],$monthyear,'SSO');
                 
                //die(print_r($purchasedata));  
                     

                  
               // }
                //print_r($purchasedata);
              // die();
                //  if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($purchasedata, $products)) {
                 if ($this->form_validation->run() == true && $this->purchases_model->addPurchase_bycsv($purchasedata, $products)) {
                      

                        $this->session->set_flashdata('message', $this->lang->line("purchase_added"));
                        // 
                    } else {

                        $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

                        $this->session->set_flashdata('error', 'Unable to add stock');
                        redirect("reports/purchases");
                    }  
                    
                } //if SSO ends here
				else {  //if PSO
                    $keys = array('MONTH','COUNTRY','CUSTOMER','QTY');
                    $final = array();

                    foreach ($arrResult as $key => $value) {
                        $final[] = array_combine($keys, $value);
                    }
                    //for  mercafar
                   
			
                        //for  epdis
						$customer_details = $this->sales_model->getDistributorByName("epdis");
						$supplier_id = $customer_details->id;
						$supplier = $customer_details->name;
                        foreach ($final as $csv_pr) {
								
                            if (isset($csv_pr['code']) && isset($csv_pr['quantity'])) {

                                if ($product_details = $this->purchases_model->getProductByCode($csv_pr['code'])) {

                                    if (!is_object($product_details)) {
                                        $item_option = $this->purchases_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                        if (!$item_option) {
                                            $this->session->set_flashdata('error', lang("product with_code_not_found") . " ( Gmid " . $csv_pr['code'] . " ). " . lang("line_no") . " " . $rw);
                                            redirect("reports/purchases");
                                        }
                                    } else {
                                        $item_option = json_decode('{}');
                                        $item_option->id = NULL;
                                    }

                                    $item_code = $csv_pr['code'];
                                    $item_net_cost = $this->sma->formatDecimal($product_details->cost);
                                    $item_quantity = $csv_pr['quantity'];
                                    $quantity_balance = $csv_pr['quantity'];
                                    $item_tax_rate = $csv_pr['item_tax_rate'];
                                    $item_discount = $csv_pr['discount'];
                                    $item_expiry = isset($csv_pr['expiry']) ? $csv_pr['expiry'] : NULL;

                                    if (isset($item_discount) && $this->Settings->product_discount) {
                                        $discount = $item_discount;
                                        $dpos = strpos($discount, $percentage);
                                        if ($dpos !== false) {
                                            $pds = explode("%", $discount);
                                            $pr_discount = (($this->sma->formatDecimal($item_net_cost)) * (Float) ($pds[0])) / 100;
                                        } else {
                                            $pr_discount = $this->sma->formatDecimal($discount);
                                        }
                                    } else {
                                        $pr_discount = 0;
                                    }
                                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                                    $product_discount += $pr_item_discount;

                                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                                        if ($tax_details = $this->purchases_model->getTaxRateByName($item_tax_rate)) {
                                            $pr_tax = $tax_details->id;
                                            if ($tax_details->type == 1) {
                                                if (!$product_details->tax_method) {
                                                    $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / (100 + $tax_details->rate));
                                                    $tax = $tax_details->rate . "%";
                                                    $item_net_cost -= $item_tax;
                                                } else {
                                                    $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / 100);
                                                    $tax = $tax_details->rate . "%";
                                                }
                                            } elseif ($tax_details->type == 2) {
                                                $item_tax = $this->sma->formatDecimal($tax_details->rate);
                                                $tax = $tax_details->rate;
                                            }
                                            $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);
                                        } else {
                                            $this->session->set_flashdata('error', lang("tax_not_found") . " ( " . $item_tax_rate . " ). " . lang("line_no") . " " . $rw);
                                            redirect("reports/purchases");
                                        }
                                    } elseif ($product_details->tax_rate) {

                                        $pr_tax = $product_details->tax_rate;
                                        $tax_details = $this->site->getTaxRateByID($pr_tax);
                                        if ($tax_details->type == 1) {
                                            if (!$product_details->tax_method) {
                                                $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / (100 + $tax_details->rate));
                                                $tax = $tax_details->rate . "%";
                                                $item_net_cost -= $item_tax;
                                            } else {
                                                $item_tax = $this->sma->formatDecimal((($item_net_cost - $pr_discount) * $tax_details->rate) / 100);
                                                $tax = $tax_details->rate . "%";
                                            }
                                        } elseif ($tax_details->type == 2) {

                                            $item_tax = $this->sma->formatDecimal($tax_details->rate);
                                            $tax = $tax_details->rate;
                                        }
                                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity);
                                    } else {
                                        $pr_tax = 0;
                                        $pr_item_tax = 0;
                                        $tax = "";
                                    }
                                    $product_tax += $pr_item_tax;

//                                $countrypricing=$this->country_productpricing_model->getCountryProductPricing($product_details->id,$supplier_details->country);  
//                                if(!is_object($countrypricing)){
//                                $this->session->set_flashdata('error', lang("country_pricing_not_found") . " ( Gmid " . $csv_pr['code'] . " ). " . lang("line_no") . " " . $rw);
//                                   redirect("purchases");
//                                }
                                    $pricee = 0; //$countrypricing->resell_price;  
                                    $unifiedprice = $product_details->price;
                                    unset($countrypricing);

                                    $subtotal = $this->sma->formatDecimal((($unifiedprice * $item_quantity)));
                                    unset($pricee);
                                    $shippingg = 0; //resell total
                                    $products[] = array(
                                        'product_id' => $product_details->id,
                                        'product_code' => $item_code,
                                        'product_name' => $product_details->name,
                                        'option_id' => $item_option->id,
                                        'net_unit_cost' =>$product_details->cost,
                                        'quantity' => $item_quantity,
                                        'quantity_balance' => $quantity_balance,
                                        'warehouse_id' => $warehouse_id,
                                        'item_tax' => $pr_item_tax,
                                        'tax_rate_id' => $pr_tax,
                                        'tax' => $tax,
                                        'discount' => $item_discount,
                                        'shipping' => $shippingg,
                                        'item_discount' => $pr_item_discount,
                                        'expiry' => $item_expiry,
                                        'subtotal' => $subtotal,
                                        'date' => $csv_pr['month'],
                                        'status' => $status,
                                        'country'=>$csv_pr['country'],
                                        'unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax),
                                        'real_unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax + $pr_discount)
                                    );

                                    $total += $item_net_cost * $item_quantity;
                                } else {
                                    $this->session->set_flashdata('error', $this->lang->line("product_code_not_found") . " ( " . $csv_pr['code'] . " ). " . $this->lang->line("line_no") . " " . $rw);
                                    redirect("reports/purchases");
                                }
                                $rw++;
                            }
                        }
                    
                    //upload now
                    foreach ($products as $data) {
                    $purchasedata = array('reference_no' => $reference,
                        'date' => $csv_pr['month'],
                        'supplier_id' => $supplier_id,
                        'supplier' => $supplier,
                        'warehouse_id' => $warehouse_id,
                        'note' => $note,
                        'stock_type'=>'PSO',
                        'promotion'=>$product_details->promoted,
                        'invoice_no' => $reference,
                        'total' => $this->sma->formatDecimal($data['subtotal']),
                        'product_discount' => $this->sma->formatDecimal($data['item_discount']),
                        'order_discount_id' => 0,
                        'order_discount' => 0,
                        'total_discount' => $data['item_discount'],
                        'product_tax' => $this->sma->formatDecimal($data['tax']),
                        'order_tax_id' => $data['tax_rate_id'],
                        'order_tax' => $data['tax'],
                        'total_tax' => $data['tax'],
                        'shipping' => $this->sma->formatDecimal($shipping),
                        'grand_total' => $data['subtotal'],
                        'status' => $status,
                        'created_by' => $this->session->userdata('user_id')
                    );
                    // //  die(print_r($purchasedata));
                    $this->purchases_model->deletePurchaseBySupplierDateAndType($supplier_id,$date,'PSO');
                    if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($purchasedata, $data)) {

                        $this->session->set_flashdata('message', $this->lang->line("purchase_added"));
                        // 
                    } else {

                        $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

                        $this->session->set_flashdata('error', 'Unable to add stock');
                        redirect("reports/purchases");
                    }
                }
                }
                //foreach products
                //$this->sma->print_arrays($products);
                
                $yourUniquedates = array_unique($dates); //GET UNIQUE COUNTRYS


                foreach($yourUniquedates as $Values){
       $this->sales_model->remove_data($stock_type,$Values);
    //REMOVE DATA THAT HAD BEEN UPLOADED
                }
            
                
            } else {

                $this->session->set_flashdata('error', 'No file input');
            }

            redirect("reports/purchases");
        }
        else {
            if (validation_errors()) {
                $this->session->set_flashdata('error', validation_errors());
            }
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['ponumber'] = $this->site->getReference('po');
            $this->data['companies'] = $this->companies_model->getAllCustomerCompanies();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase_by_csv')));
            $meta = array('page_title' => lang('add_purchase_by_csv'), 'bc' => $bc);
            $this->page_construct('purchases/purchase_by_csv', $meta, $this->data);
        }
    }

    /* --------------------------------------------------------------------------- */

    function delete($id = NULL) {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deletePurchase($id)) {
            if ($this->input->is_ajax_request()) {
                echo lang("purchase_deleted");
                die();
            }
            $this->session->set_flashdata('message', lang('purchase_deleted'));
            redirect('welcome');
        }
    }

    /* --------------------------------------------------------------------------- */

    function suggestions() {
        $term = $this->input->get('term', TRUE);
        $supplier_id = $this->input->get('supplier_id', TRUE);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }

        $spos = strpos($term, ' ');
        if ($spos !== false) {
            $st = explode(" ", $term);
            $sr = trim($st[0]);
            $option = trim($st[1]);
        } else {
            $sr = $term;
            $option = '';
        }

        $rows = $this->purchases_model->getProductNames($term);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = FALSE;
                $row->item_tax_method = $row->tax_method;
                $options = $this->purchases_model->getProductOptions($row->id);
                if ($options) {
                    $opt = current($options);
                    if (!$option) {
                        $option = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->cost = 0;
                }
                $row->option = $option;
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                } else {
                    $row->cost = $row->cost;
                    if ($supplier_id == $row->supplier1 && (!empty($row->supplier1price)) && $row->supplier1price != 0) {
                        $row->cost = $row->supplier1price;
                    } elseif ($supplier_id == $row->supplier2 && (!empty($row->supplier2price)) && $row->supplier2price != 0) {
                        $row->cost = $row->supplier2price;
                    } elseif ($supplier_id == $row->supplier3 && (!empty($row->supplier3price)) && $row->supplier3price != 0) {
                        $row->cost = $row->supplier3price;
                    } elseif ($supplier_id == $row->supplier4 && (!empty($row->supplier4price)) && $row->supplier4price != 0) {
                        $row->cost = $row->supplier4price;
                    } elseif ($supplier_id == $row->supplier5 && (!empty($row->supplier5price)) && $row->supplier5price != 0) {
                        $row->cost = $row->supplier5price;
                    }
                }
                $row->real_unit_cost = $row->cost;
                $row->expiry = '';
                $row->qty = 1;
                $row->quantity_balance = '';
                $row->discount = '0';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                if ($row->tax_rate) {
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $pr[] = array('id' => ($c + $r), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => $tax_rate, 'options' => $options);
                } else {
                    $pr[] = array('id' => ($c + $r), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => false, 'options' => $options);
                }
                $r++;
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* -------------------------------------------------------------------------------- */

    function purchase_actions() {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->purchases_model->deletePurchase($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("purchases_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('purchases'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('country'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('category'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $purchase = $this->purchases_model->getAllPurchaseDetailsByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, date('m-Y',strtotime($purchase->date)));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $purchase->product_code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $purchase->country);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $purchase->product_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $purchase->categoryname);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $purchase->quantity);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $purchase->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase->grand_total);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'stock_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

    function payments($id = NULL) {
        $this->sma->checkPermissions(false, true);

        $this->data['payments'] = $this->purchases_model->getPurchasePayments($id);
        $this->load->view($this->theme . 'purchases/payments', $this->data);
    }

    function payment_note($id = NULL) {
        $payment = $this->purchases_model->getPaymentByID($id);
        $inv = $this->purchases_model->getPurchaseByID($payment->purchase_id);
        $this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = $this->lang->line("payment_note");

        $this->load->view($this->theme . 'purchases/payment_note', $this->data);
    }

    function add_payment($id = NULL) {
        $this->sma->checkPermissions('payments', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'purchase_id' => $this->input->post('purchase_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'sent'
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->purchases_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $purchase = $this->purchases_model->getPurchaseByID($id);
            $this->data['inv'] = $purchase;
            $this->data['payment_ref'] = ''; //$this->site->getReference('pay');
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/add_payment', $this->data);
        }
    }

    function edit_payment($id = NULL) {
        $this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'purchase_id' => $this->input->post('purchase_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->sma->clear_tags($this->input->post('note'))
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect("reports/purchases");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));


            $this->data['payment'] = $this->purchases_model->getPaymentByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/edit_payment', $this->data);
        }
    }

    function delete_payment($id = NULL) {
        $this->sma->checkPermissions('delete', TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

    function expenses($id = NULL) {
        $this->sma->checkPermissions('index',null,'expenses');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('purchases/expenses', $meta, $this->data);
    }
    
    function expenses_approved($id = NULL) {
        $this->sma->checkPermissions('approve',null,'expenses');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('purchases/expenses_approved', $meta, $this->data);
    }

    function getExpenses() {
        $this->sma->checkPermissions('index',null,'expenses');

        $detail_link = anchor('purchases/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
        $edit_link = anchor('purchases/edit_expense/$1', '<i class="fa fa-edit"></i> ' . lang('edit_expense'), 'data-toggle="modal" data-target="#myModal"');
        $approve_link = "<a href='#' class='po' title='<b>" . $this->lang->line("Approve_Expense") . "</b>' data-content=\"<p>"
                . lang('r_u_sure') . "</p><a class='btn btn-success po-delete' href='" . site_url('purchases/approve_expense/$1') . "'>"
                . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i> "
                . lang('Approve_Expense') . "</a>";
        //$attachment_link = '<a href="'.base_url('assets/uploads/$1').'" target="_blank"><i class="fa fa-chain"></i></a>';
        $deny_link = anchor('purchases/deny_expense/$1', '<i class="fa fa-close"></i> ' . lang('Deny_expense'), 'data-toggle="modal" data-target="#myModal"');
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_expense") . "</b>' data-content=\"<p>"
                . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('purchases/delete_expense/$1') . "'>"
                . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
                . lang('delete_expense') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $approve_link . '</li>
            <li>' . $deny_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->load->library('datatables');

        $this->datatables
                ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, amount, note, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as user, sma_expenses.status", FALSE)
                ->from('expenses')
                ->where('distributor_id',$distributor->id)
                ->where('status','Pending')
                ->join('users', 'users.company_id=expenses.salesman_id', 'left')
                ->group_by('expenses.id');

        // if (!$this->Owner && !$this->Admin) {
        //   $this->datatables->where('created_by', $this->session->userdata('user_id'));
        // }
        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
    
    function getStockTaking($action = NULL){
        $this->sma->checkPermissions('index',true,'stock-taking');
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Stock Taking History')));
        $meta = array('page_title' => lang('Stock Taking History'), 'bc' => $bc);
        $this->page_construct('purchases/stock_taking_history', $meta, $this->data);
    }

    function getStockTakingData(){
        $this->sma->checkPermissions('index',true,'stock-taking');
        
        $reverse_link = "<a href='#' class='po' title='<b>" . $this->lang->line("Reverse") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-success po-delete' href='" . site_url('purchases/reverse_stock_taking_history/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-step-backward\"></i> "
            . lang('Reverse') . "</a>";
        $view_link = anchor('purchases/stock_taking_history/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Stock_taking_details'), 'data-toggle="modal" data-target="#myModal2"');
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $reverse_link . '</li>
            <li>' . $view_link . '</li>
        </ul>
    </div></div>';
        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->load->library('datatables');

        $this->datatables
            ->select("sma_stock_taking_history.id as id, sma_stock_taking_history.created_at as date, sma_vehicles.plate_no, sma_companies.name,
             sma_stock_taking_history.total_short as short,sma_stock_taking_history.is_reversed as status", FALSE)
            ->from('sma_stock_taking_history')
            //->where('distributor_id',$distributor->id)
            ->join("sma_companies","sma_stock_taking_history.salesman_id=sma_companies.id","left")
            ->join("sma_vehicles","sma_stock_taking_history.vehicle_id=sma_vehicles.id","left")
            ->group_by('sma_stock_taking_history.id');


        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
//view stock_taking
    function stock_taking_history($id=NULL){
        //$this->sma->checkPermissions('view-stock',true,'stock-taking');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $stock_taking_history=$this->purchases_model->getStockTakingHistoryById($id);
        //$salesman_details = $this->companies_model->getCompanyByID($stock_taking_history->salesman_id);
        $expected = json_decode($stock_taking_history->expected_stock);
        $current = json_decode($stock_taking_history->current_stock); 
        $this->data['expected'] = $expected;
        $this->data['current'] = $current;
        $this->data['stock'] = $stock_taking_history;
        $this->data['page_title'] = $this->lang->line("view_stock");

        
        
        //print_r($stock);
        $this->load->view($this->theme.'purchases/stock_taking_history_detail',$this->data);
    }
    function delete_stock_taking_history($id = NULL) {
        $this->sma->checkPermissions('delete',true,'stock-taking');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->purchases_model->deleteStockTakingHistory($id)) {

            echo $this->lang->line("stock_taking_history_deleted");
        } else {
            $this->session->set_flashdata('warning', lang('route_not_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('purchases/getStockTaking')) . "'; }, 0);</script>");
        }
    }

    function reverse_stock_taking_history($id = NULL) {
        $this->sma->checkPermissions('reverse',true,'stock-taking');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $stock_taking_history=$this->purchases_model->getStockTakingHistoryById($id);

        //print_r(json_decode($stock_taking_history->differences));
        $differences = json_decode($stock_taking_history->differences);
        foreach ($differences as $difference){
            $this->vehicles_model->reverseVehicleStockTaking($stock_taking_history->vehicle_id,$difference->product_id,$stock_taking_history->distributor_id,$difference->difference);
        }

        $this->purchases_model->updateStockTakingHistory($id,array('is_reversed'=>1));

        if($stock_taking_history->total_short>0){
            //send to erp
            $json = array();

            $salesman_details = $this->companies_model->getCompanyByID($stock_taking_history->salesman_id);
            
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
                        'amount'=> $stock_taking_history->total_short,
                        'memo'=> 'Short reversed'.$stock_taking_history->total_comments
                    )
                );
                $json2=array(
                    'currency'=> 'KS',
                    'source_ref'=>$response_data->account_code,
                    'reference'=> $response_data->account_code,
                    'memo'=> 'Short reversed'.$stock_taking_history->total_comments,
                    'amount'=> $stock_taking_history->total_short,
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
                    echo $this->lang->line("Short reversed successfully");
                    
                } else {
                    $this->session->set_flashdata('warning', lang('Failed_to_add_short_in_ERP'));
                    die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('purchases/getStockTaking')) . "'; }, 0);</script>");
                }
            } else {
                $this->session->set_flashdata('warning', lang('Salesperson_account_not_found_in_ERP'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('purchases/getStockTaking')) . "'; }, 0);</script>");
            }
        }else{
            echo $this->lang->line("No short to reverse");
        }
    }
    
    function getApprovedExpenses() {
        $this->sma->checkPermissions('approve',null,'expenses');

        $detail_link = anchor('purchases/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_expense") . "</b>' data-content=\"<p>"
                . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('purchases/delete_expense/$1') . "'>"
                . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
                . lang('delete_expense') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $distributor=$this->companies_model->getCompanyByID($this->ion_auth->get_company_id());
        $this->load->library('datatables');

        $this->datatables
                ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, amount, note, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as user, sma_expenses.status", FALSE)
                ->from('expenses')
                ->where('distributor_id',$distributor->id)
                ->where('status','approved')
                ->join('users', 'users.company_id=expenses.salesman_id', 'left')
                ->group_by('expenses.id');

        // if (!$this->Owner && !$this->Admin) {
        //   $this->datatables->where('created_by', $this->session->userdata('user_id'));
        // }
        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    function expense_note($id = NULL) {
        $this->sma->checkPermissions('index',null,'expenses');
        $expense = $this->purchases_model->getExpenseByID($id);
        $this->data['user'] = $this->site->getUser($expense->created_by);
        $this->data['expense'] = $expense;
        $this->data['page_title'] = $this->lang->line("expense_note");
        $this->load->view($this->theme . 'purchases/expense_note', $this->data);
    }

    function add_expense() {
        $this->sma->checkPermissions('add',true,'expenses');
        $this->load->helper('security');

        //$this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ex'),
                'amount' => $this->input->post('amount'),
                'created_by' => $this->session->userdata('user_id'),
                'note' => $this->input->post('note', TRUE)
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('add_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addExpense($data)) {
            $this->session->set_flashdata('message', lang("expense_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['exnumber'] = ''; //$this->site->getReference('ex');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'purchases/add_expense', $this->data);
        }
    }

    function edit_expense($id = NULL) {
        $this->sma->checkPermissions('edit',true,'expenses');
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('status', lang("status"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount'),
                'note' => $this->input->post('note', TRUE),
                'status' => $this->input->post('status')
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('edit_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_updated"));
            redirect("purchases/expenses");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['expense'] = $this->purchases_model->getExpenseByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/edit_expense', $this->data);
        }
    }
    
    function approve_expense($id = NULL) {
        $this->sma->checkPermissions('approve',true,'expenses');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->purchases_model->getExpenseByID($id);
        $salesman = $this->companies_model->getCompanyById($expense->salesman_id);
        $data = array(
            'status' => "approved",
            'approved' => $expense->amount,
        );

        if ($this->purchases_model->updateExpense($id, $data)) {
            $json = array();
			
			$data = array(
			    "id"=>$salesman->bank_acc_id
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
            		'amount'=> -$expense->amount,
            		'memo'=> $expense->note
            	),
            	array(
            		'account_code'=> '4030',
            		'amount'=> $expense->amount,
            		'memo'=> $expense->note
            	)
                );		
                $json2=array(
                	'currency'=> 'KS',
                    'source_ref'=>$expense->id,
                    'reference'=> $expense->id,
                    'memo'=> $expense->note,
                	'amount'=> $expense->amount,
                	'bank_act'=>$salesman->bank_acc_id,
                	'items'=> $items
                );
                $json_data2 = json_encode($json2);
                
                $username2 = "pos-api";
                $password2 = "admin";
                $headers2 = array(
                	'Authorization: Basic '. base64_encode($username2.':'.$password2),
                	'Content-Type: application/json',
                );
                
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
                CURLOPT_HTTPHEADER => $headers2,
                ));
                
                $response2 = curl_exec($curl2);
                
                curl_close($curl2);
                
                $new_id2 = json_decode($response2)->id;
    
                if ($new_id2) { 
                    echo lang("expense_approved");
                } else {
                    echo lang("expense_not_approved_in_erp");
                }
            } else {
                echo lang("salesperson_acc_not_found_in_erp");
            }
            
            
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['expense'] = $this->purchases_model->getExpenseByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/edit_expense', $this->data);
        }
    }
    
    function deny_expense($id = NULL) {
        $this->sma->checkPermissions('approve',true,'expenses');
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->purchases_model->getExpenseByID($id);
        $salesman = $this->companies_model->getCompanyById($expense->salesman_id);
        
        $this->form_validation->set_rules('reason', lang("reason"), 'required');
        if ($this->form_validation->run() == true) {
            $data = array(
                'reason' => $this->input->post('status',TRUE)
            );
        } elseif ($this->input->post('deny_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("purchases/expenses");
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_denied"));
            redirect("purchases/expenses");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['expense'] = $this->purchases_model->getExpenseByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/deny_expense', $this->data);
        }
    }

    function delete_expense($id = NULL) {
        $this->sma->checkPermissions('delete',true,'expenses');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->purchases_model->getExpenseByID($id);
        if ($this->purchases_model->deleteExpense($id)) {
            if ($expense->attachment) {
                unlink($this->upload_path . $expense->attachment);
            }
            echo lang("expense_deleted");
        }
    }

    function expense_actions() {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->purchases_model->deleteExpense($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("expenses_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('expenses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('amount'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('created_by'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $expense = $this->purchases_model->getExpenseByID($id);
                        $user = $this->site->getUser($expense->created_by);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($expense->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $expense->reference);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($expense->amount));
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $expense->note);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $user->first_name . ' ' . $user->last_name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'expenses_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', $this->lang->line("no_expense_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}
