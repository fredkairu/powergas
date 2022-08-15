<?php defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(500); // 
class Sales extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('sales', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('sales_model');
        $this->load->model('site');
        $this->load->model('settings_model');
        $this->load->model('products_model');
        $this->load->model('companies_model');
        $this->load->model('vehicles_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '11000';
        $this->data['logo'] = true;
        ini_set('memory_limit', '8096M');
    }

    function index($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
        $this->page_construct('sales/index', $meta, $this->data);
    }
    
    function index2($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Discounts')));
        $meta = array('page_title' => lang('Discounts'), 'bc' => $bc);
        $this->page_construct('discounts/index', $meta, $this->data);
    }
    
    function index3($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Discounts')));
        $meta = array('page_title' => lang('Approved Discounts'), 'bc' => $bc);
        $this->page_construct('discounts/discount_approved', $meta, $this->data);
    }

 function invoice($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Invoices')));
        $meta = array('page_title' => lang('Invoices'), 'bc' => $bc);
        $this->page_construct('invoices/index', $meta, $this->data);
    }
    
    function invoice1($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Invoices')));
        $meta = array('page_title' => lang('Approved Invoices'), 'bc' => $bc);
        $this->page_construct('invoices/invoice_approved', $meta, $this->data);
    }
    function cheque($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Cheques')));
        $meta = array('page_title' => lang('Cheques'), 'bc' => $bc);
        $this->page_construct('cheques/index', $meta, $this->data);
    }
    function cheque1($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
       // } else {
       //     $this->data['warehouses'] = NULL;
       //     $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
       //     $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
       // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Cheques')));
        $meta = array('page_title' => lang('Approved Cheques'), 'bc' => $bc);
        $this->page_construct('cheques/cheque_approved', $meta, $this->data);
    }
      function graph(){
          if(!$this->Owner && !$this->Admin){
                    $this->session->set_flashdata('error',"Not authorised to view page");
            redirect($_SERVER["HTTP_REFERER"]);
          }
          $fromdate=trim($this->input->get("fromdate"));
          $todate=trim($this->input->get("todate"));
        //die($fromdate."sds");
         $date1 = DateTime::createFromFormat('d/m/Y',$fromdate);
         $date2 = DateTime::createFromFormat('d/m/Y',$todate);
      $table="ps_";
        
          if(empty($fromdate)){
              $fromdate=date("Y-m-d");
            $todate=date("Y-m-d");
          }
          else{
                $fromdate=$date1->format("Y-m-d");
            $todate=$date2->format("Y-m-d");
          }
          
          $results=$this->db->query("select sum(grand_total) as total_sales from sma_sales where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' ")->result_array();     
       $resultspurchases=$this->db->query("select sum(grand_total) as total_purchases from sma_purchases where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' ")->result_array();     
          $resultsexpenses=$this->db->query("select sum(amount) as total_expense from sma_expenses where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' ")->result_array();     
       $resultspayments=$this->db->query("select sum(amount) as total_amount,paid_by from sma_payments where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' group by paid_by")->result_array();     
    
//          $resultbookings=$this->db->query('SELECT SUM(cap.total_order_amount - cap.total_paid_amount) AS `amount_due`,SUM(op.amount) as paid_amount,so.total_paid_tax_incl,so.total_paid_tax_incl ,so.payment as payment_method,so.board_type,DATE_FORMAT(so.invoice_date,"%Y-%m-%d") as invoice_date, so.source AS order_source,op.transaction_id as transaction_id,
//        so.id_currency,
//        so.id_order AS id_pdf,
//        CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
//         CONCAT(LEFT(emp.`firstname`, 1), \'. \', emp.`lastname`) AS `employee_names`,
//        osl.`name` AS `osname`,
//        os.`color`,hri.`room_num` as room_num  FROM `'.$table.'orders` so LEFT JOIN `'.$table.'customer` c ON (c.`id_customer` = so.`id_customer`)
//           
//            
//                   LEFT JOIN `'.$table.'employee` emp ON (so.`employee` = emp.`id_employee`)  
//              LEFT JOIN `'.$table.'order_payment` op ON (so.`reference` = op.`order_reference`)
//              LEFT JOIN `'.$table.'htl_cart_booking_data` hcb ON (so.`id_order` = hcb.`id_order`)   
//                  LEFT JOIN `'.$table.'htl_room_information` hri ON (hcb.`id_room` = hri.`id`)  
//                       INNER JOIN `'.$table.'order_state` os ON (os.`id_order_state` = so.`current_state`)
//                 LEFT JOIN `'.$table.'htl_customer_adv_payment` cap ON (cap.`id_order` = so.`id_order`)
//                      LEFT JOIN `'.$table.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`)
//                 WHERE  (DATE_FORMAT(so.invoice_date,"%Y-%m-%d") between "'.$fromdate.'" and "'.$todate.'" ) group by invoice_date ')->result_array();
//         
       
          
       
      // die(print_r(json_encode($resultbookings)));
       
       $resultbookings=array();
          $this->data['warehouses'] = NULL;
         $this->data['sales'] = round($results[0]["total_sales"],2);
          $this->data['purchases'] = round($resultspurchases[0]["total_purchases"],2);
          $this->data["fromdate"]=$fromdate;
           $this->data["todate"]=$todate;
          $this->data["payment_type"]=json_encode($resultspayments);
          $this->data["room_bookings"]=json_encode($resultbookings);
          $this->data['expenses'] = round($resultsexpenses[0]["total_expense"],2);
         $meta = array('page_title' => lang('product_expiry_alerts'));
       
          $this->page_construct('reports/graph', $meta, $this->data); 
    }
    
    // function getSales($warehouse_id = NULL)
    // {
    //     $this->sma->checkPermissions('index');

    //     if (!$this->Owner && !$warehouse_id) {
    //         $user = $this->site->getUser();
    //         $warehouse_id = $user->warehouse_id;
    //     }
    //      $complete_payment = anchor('sales/add_payment/$1', '<i class="fa fa-money"></i> ' . " Complete Payment", 'data-toggle="modal" data-target="#myModal"');
    //     $detail_link = anchor('sales/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
    //     $payments_link = anchor('sales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
    //     $add_payment_link = anchor('sales/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
    //     $add_delivery_link = anchor('sales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
    //     $email_link = anchor('sales/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'data-toggle="modal" data-target="#myModal"');
    //     $edit_link = anchor('sales/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
    //     $pdf_link = anchor('sales/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
    //     $return_link = anchor('sales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));
    //     $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_sale") . "</b>' data-content=\"<p>"
    //         . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('sales/delete/$1') . "'>"
    //         . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
    //         . lang('delete_sale') . "</a>";
    //     $action = '<div class="text-center"><div class="btn-group text-left">'
    //         . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
    //         . lang('actions') . ' <span class="caret"></span></button>
    //     <ul class="dropdown-menu pull-right" role="menu">
           
    //         <li>' . $detail_link . '</li>
    //         <li>' . $edit_link . '</li>
    //         <li>' . $pdf_link . '</li>
    //         <li>' . $email_link . '</li>
    //         <li>' . $return_link . '</li>
    //         <li>' . $delete_link . '</li>
    //     </ul>
    // </div></div>';
    //     //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

    //     $this->load->library('datatables');
    //     if ($warehouse_id) {
    //      $this->datatables
    //             ->select("sales.id as id, sales.date,sales.reference_no,sales.sales_type,products.code,products.name,sales.customer,products.business_unit,categories.name as catname,sales.sales_cluster,sales.sales_region")
    //               ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
    //                 ->join('products', 'sale_items.product_id=products.id', 'left')
    //                 ->join('categories', 'products.category_id=categories.id', 'left')
    //                 ->join('sma_alignments', 'sma_sales.alignment_id=sma_alignments.id', 'left')
    //                 ->join('sma_users', 'sma_sales.sales_person_id=sma_users.id', 'left')
    //             ->from('sales')
    //             ->where('warehouse_id', $warehouse_id);
    //     } else {
    //         $this->datatables
    //             ->select("sma_sales.id as id, sma_sales.date,sma_sales.reference_no,sma_sales.sales_type,sma_products.code,sma_products.name,sma_sales.customer,sma_products.business_unit,sma_categories.name as catname,sma_sales.sales_cluster,sma_sales.sales_region")
    //               ->join('sma_sale_items', 'sma_sale_items.sale_id=sma_sales.id', 'left')
    //                 ->join('sma_products', 'sma_sale_items.product_id=sma_products.id', 'left')
    //                 ->join('sma_categories', 'sma_products.category_id=sma_categories.id', 'left')
    //                  ->join('sma_alignments', 'sma_sales.alignment_id=sma_alignments.id', 'left')
    //                 ->join('sma_users', 'sma_sales.sales_person_id=sma_users.id', 'left')
    //             ->from('sma_sales');
    //     }
    //     $this->datatables->where('pos !=', 1);
    //   // if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin) {
    //   //     $this->datatables->where('created_by', $this->session->userdata('user_id'));
    //   // } elseif ($this->Customer) {
    //   //     $this->datatables->where('customer_id', $this->session->userdata('user_id'));
    //   //  }
    //     $this->datatables->add_column("Actions", $action, "id");
    //     echo $this->datatables->generate();
    // }

    /** start get shops and routes details */
   /*** function shopsDetails(){
          if(!$this->Owner && !$this->Admin){
                    $this->session->set_flashdata('error',"Not authorised to view page");
            redirect($_SERVER["HTTP_REFERER"]);
          }
          $fromdate=trim($this->input->get("fromdate"));
          $todate=trim($this->input->get("todate"));
      
          
          $results=$this->db->query("select sum(grand_total) as total_sales from sma_sales where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' ")->result_array();     
       $resultspurchases=$this->db->query("select sum(grand_total) as total_purchases from sma_purchases where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' ")->result_array();     
          $resultsexpenses=$this->db->query("select sum(amount) as total_expense from sma_expenses where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' ")->result_array();     
       $resultspayments=$this->db->query("select sum(amount) as total_amount,paid_by from sma_payments where DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate' group by paid_by")->result_array();     
    

       
       $resultbookings=array();
          $this->data['warehouses'] = NULL;
         $this->data['sales'] = round($results[0]["total_sales"],2);
          $this->data['purchases'] = round($resultspurchases[0]["total_purchases"],2);
          $this->data["fromdate"]=$fromdate;
           $this->data["todate"]=$todate;
          $this->data["payment_type"]=json_encode($resultspayments);
          $this->data["room_bookings"]=json_encode($resultbookings);
          $this->data['expenses'] = round($resultsexpenses[0]["total_expense"],2);
         $meta = array('page_title' => lang('product_expiry_alerts'));
       
          $this->page_construct('routes/summary', $meta, $this->data); 
    } */
    /** end get shops and routes details */
    
    function getSales()
    {  $user=$this->session->userdata('company_id');
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
             if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_sales.id,sales_type,sma_sales.created,UPPER(sma_sales.distributor) AS distributor,UPPER(sma_sales.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_sales.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no)")
            ->from("sma_sales")
            ->join('sma_companies', 'sma_sales.salesman_id=sma_companies.id', 'left')
            ->join('sma_sale_items', 'sma_sales.id=sma_sale_items.sale_id', 'left')
            ->join('sma_shops', 'sma_sales.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_sales.vehicle_id=sma_vehicles.id', 'left')
            ->group_by('sma_sales.id');
        //->unset_column('id');
    } else
    {$this->datatables
            ->select("sma_sales.id,sales_type,sma_sales.created,UPPER(sma_sales.distributor) AS distributor,UPPER(sma_sales.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_sales.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no)")
            ->from("sma_sales")
            ->join('sma_companies', 'sma_sales.salesman_id=sma_companies.id', 'left')
            ->join('sma_sale_items', 'sma_sales.id=sma_sale_items.sale_id', 'left')
            ->join('sma_shops', 'sma_sales.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_sales.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_sales.distributor_id',$user)
            ->group_by('sma_sales.id');
        }
        echo $this->datatables->generate();
    }
    
    function getDiscounts()
    {
        //$this->sma->checkPermissions('index');
        $user=$this->session->userdata('company_id');
        $this->load->library('datatables');
         $today=date("Y-m-d");
         if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_discounts.id as id,sales_type,sma_discounts.created,UPPER(sma_discounts.distributor) AS distributor,UPPER(sma_discounts.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_discounts.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_discounts.status as status")
            ->from("sma_discounts")
            ->join('sma_companies', 'sma_discounts.salesman_id=sma_companies.id', 'left')
            ->join('sma_discount_items', 'sma_discounts.id=sma_discount_items.sale_id', 'left')
            ->join('sma_shops', 'sma_discounts.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_discounts.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_discounts.date', $today)
            ->where('sma_discounts.status',0)
            ->group_by('sma_discounts.id');
        //->unset_column('id');
         } else
         {
             $this->datatables
            ->select("sma_discounts.id as id,sales_type,sma_discounts.created,UPPER(sma_discounts.distributor) AS distributor,UPPER(sma_discounts.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_discounts.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_discounts.status as status")
            ->from("sma_discounts")
            ->join('sma_companies', 'sma_discounts.salesman_id=sma_companies.id', 'left')
            ->join('sma_discount_items', 'sma_discounts.id=sma_discount_items.sale_id', 'left')
            ->join('sma_shops', 'sma_discounts.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_discounts.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_discounts.status',0)
            ->where('sma_discounts.distributor_id',$user)
            ->where('sma_discounts.date', $today)
            ->group_by('sma_discounts.id');
         }
        echo $this->datatables->generate();
    }
    
    function getApprovedDiscounts()
    {
        //$this->sma->checkPermissions('index');
        $user=$this->session->userdata('company_id');
        $this->load->library('datatables');
         if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_discounts.id as id,sales_type,sma_discounts.created,UPPER(sma_discounts.distributor) AS distributor,UPPER(sma_discounts.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_discounts.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_discounts.status as status")
            ->from("sma_discounts")
            ->where('sma_discounts.status',1)
            ->where('sma_discounts.date', $today)
            ->join('sma_companies', 'sma_discounts.salesman_id=sma_companies.id', 'left')
            ->join('sma_discount_items', 'sma_discounts.id=sma_discount_items.sale_id', 'left')
            ->join('sma_shops', 'sma_discounts.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_discounts.vehicle_id=sma_vehicles.id', 'left')
            ->group_by('sma_discounts.id');
        //->unset_column('id');
        echo $this->datatables->generate();
         } else
         {
             $this->datatables
            ->select("sma_discounts.id as id,sales_type,sma_discounts.created,UPPER(sma_discounts.distributor) AS distributor,UPPER(sma_discounts.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_discounts.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_discounts.status as status")
            ->from("sma_discounts")
            ->where('sma_discounts.status',1)
            ->where('sma_discounts.date', $today)
            ->join('sma_companies', 'sma_discounts.salesman_id=sma_companies.id', 'left')
            ->join('sma_discount_items', 'sma_discounts.id=sma_discount_items.sale_id', 'left')
            ->join('sma_shops', 'sma_discounts.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_discounts.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_discounts.distributor_id',$user)
            ->group_by('sma_discounts.id');
            echo $this->datatables->generate();
         }
        
    }
    
    function getApprovedInvoices()
    {
        //$this->sma->checkPermissions('index');
        $user=$this->session->userdata('company_id');
        $this->load->library('datatables');
         if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_invoices.id as id,sales_type,sma_invoices.created,UPPER(sma_invoices.distributor) AS distributor,UPPER(sma_invoices.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_invoices.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_invoices.status as status")
            ->from("sma_invoices")
            ->where('sma_invoices.status',1)
            ->join('sma_companies', 'sma_invoices.salesman_id=sma_companies.id', 'left')
            ->join('sma_invoice_items', 'sma_invoices.id=sma_invoice_items.sale_id', 'left')
            ->join('sma_shops', 'sma_invoices.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_invoices.vehicle_id=sma_vehicles.id', 'left')
            ->group_by('sma_invoices.id');
        //->unset_column('id');
         } else
         {
             $this->datatables
            ->select("sma_invoices.id as id,sales_type,sma_invoices.created,UPPER(sma_invoices.distributor) AS distributor,UPPER(sma_invoices.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_invoices.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_invoices.status as status")
            ->from("sma_invoices")
            ->where('sma_invoices.status',1)
            ->join('sma_companies', 'sma_invoices.salesman_id=sma_companies.id', 'left')
            ->join('sma_invoice_items', 'sma_invoices.id=sma_invoice_items.sale_id', 'left')
            ->join('sma_shops', 'sma_invoices.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_invoices.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_invoices.distributor_id',$user)
            ->group_by('sma_invoices.id');
         }
        echo $this->datatables->generate();
    }
   
   function getInvoices()
    {
        //$this->sma->checkPermissions('index');
        $user=$this->session->userdata('company_id');
        $this->load->library('datatables');
         if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_invoices.id as id,sales_type,sma_invoices.created,UPPER(sma_invoices.distributor) AS distributor,UPPER(sma_invoices.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_invoices.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_invoices.status as status")
            ->from("sma_invoices")
            ->join('sma_companies', 'sma_invoices.salesman_id=sma_companies.id', 'left')
            ->join('sma_invoice_items', 'sma_invoices.id=sma_invoice_items.sale_id', 'left')
            ->join('sma_shops', 'sma_invoices.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_invoices.vehicle_id=sma_vehicles.id', 'left')
            ->group_by('sma_invoices.id');
        //->unset_column('id');
         } else
         {
             $this->datatables
            ->select("sma_invoices.id as id,sales_type,sma_invoices.created,UPPER(sma_invoices.distributor) AS distributor,UPPER(sma_invoices.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_invoices.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_invoices.status as status")
            ->from("sma_invoices")
            ->join('sma_companies', 'sma_invoices.salesman_id=sma_companies.id', 'left')
            ->join('sma_invoice_items', 'sma_invoices.id=sma_invoice_items.sale_id', 'left')
            ->join('sma_shops', 'sma_invoices.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_invoices.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_invoices.distributor_id',$user)
            ->group_by('sma_invoices.id');
         }
        echo $this->datatables->generate();
    }
    
    function getCheques()
    {
        //$this->sma->checkPermissions('index');
        $user=$this->session->userdata('company_id');
        $this->load->library('datatables');
         if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_cheques.id as id,sales_type,sma_cheques.created,UPPER(sma_cheques.distributor) AS distributor,UPPER(sma_cheques.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_cheques.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_cheques.status as status")
            ->from("sma_cheques")
            ->join('sma_companies', 'sma_cheques.salesman_id=sma_companies.id', 'left')
            ->join('sma_cheque_items', 'sma_cheques.id=sma_cheque_items.sale_id', 'left')
            ->join('sma_shops', 'sma_cheques.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_cheques.vehicle_id=sma_vehicles.id', 'left')
            ->group_by('sma_cheques.id');
        //->unset_column('id');
         } else
         {
             $this->datatables
            ->select("sma_cheques.id as id,sales_type,sma_cheques.created,UPPER(sma_cheques.distributor) AS distributor,UPPER(sma_cheques.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_cheques.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_cheques.status as status")
            ->from("sma_cheques")
            ->join('sma_companies', 'sma_cheques.salesman_id=sma_companies.id', 'left')
            ->join('sma_cheque_items', 'sma_cheques.id=sma_cheque_items.sale_id', 'left')
            ->join('sma_shops', 'sma_cheques.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_cheques.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_cheques.distributor_id',$user)
            ->group_by('sma_cheques.id');
         }
        echo $this->datatables->generate();
    }
    
    function getApprovedCheques()
    {
        //$this->sma->checkPermissions('index');
        $user=$this->session->userdata('company_id');
        $this->load->library('datatables');
         if ($this->Owner || $this->Admin) {
        $this->datatables
            ->select("sma_cheques.id as id,sales_type,sma_cheques.created,UPPER(sma_cheques.distributor) AS distributor,UPPER(sma_cheques.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_cheques.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_cheques.status as status")
            ->from("sma_cheques")
            ->where('sma_cheques.status',1)
            ->join('sma_companies', 'sma_cheques.salesman_id=sma_companies.id', 'left')
            ->join('sma_cheque_items', 'sma_cheques.id=sma_cheque_items.sale_id', 'left')
            ->join('sma_shops', 'sma_cheques.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_cheques.vehicle_id=sma_vehicles.id', 'left')
            ->group_by('sma_cheques.id');
        //->unset_column('id');
         } else
         {
             $this->datatables
            ->select("sma_cheques.id as id,sales_type,sma_cheques.created,UPPER(sma_cheques.distributor) AS distributor,UPPER(sma_cheques.customer) AS customer,UPPER(sma_shops.shop_name) AS shop,quantity_units,sma_cheques.grand_total,UPPER(sma_companies.name) AS salesman_name,UPPER(sma_vehicles.plate_no),sma_cheques.status as status")
            ->from("sma_cheques")
            ->where('sma_cheques.status',1)
            ->join('sma_companies', 'sma_cheques.salesman_id=sma_companies.id', 'left')
            ->join('sma_invoice_items', 'sma_cheques.id=sma_cheque_items.sale_id', 'left')
            ->join('sma_shops', 'sma_cheques.shop_id=sma_shops.id', 'left')
            ->join('sma_vehicles', 'sma_cheques.vehicle_id=sma_vehicles.id', 'left')
            ->where('sma_cheques.distributor_id',$user)
            ->group_by('sma_cheques.id');
         }
        echo $this->datatables->generate();
    }
    
    
    
    function return_sales($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse'] = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('return_sales')));
        $meta = array('page_title' => lang('return_sales'), 'bc' => $bc);
        $this->page_construct('sales/return_sales', $meta, $this->data);
    }
    
    
    function return_dsm($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        // if ($this->Owner) {
        //     $this->data['warehouses'] = $this->site->getAllWarehouses();
        //     $this->data['warehouse_id'] = $warehouse_id;
        //     $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        // } else {
        //     $user = $this->site->getUser();
        //     $this->data['warehouses'] = NULL;
        //     $this->data['warehouse_id'] = $user->warehouse_id;
        //     $this->data['warehouse'] = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        // }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('District Sales Managers')));
        $meta = array('page_title' => lang('return_dsm'), 'bc' => $bc);
        $this->page_construct('sales/dsm', $meta, $this->data);
    }
    

    function getReturns($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('return_sales');

        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('sales/view_return/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Return_Sale_Details'));
		
        $edit_link = ''; //anchor('sales/edit/$1', '<i class="fa fa-edit"></i>', 'class="reedit"');
			$delete_link = "<a href='#' class='po' title='<b>" . lang("delete_Return_sale") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('sales/delete_return/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_sale') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select($this->db->dbprefix('return_sales') . ".id as id,".$this->db->dbprefix('return_sales') . ".date as date, " . $this->db->dbprefix('return_sales') . ".reference_no as ref, " . $this->db->dbprefix('sales') . ".reference_no as sal_ref, " . $this->db->dbprefix('return_sales') . ".biller, " . $this->db->dbprefix('return_sales') . ".customer, " . $this->db->dbprefix('return_sales') . ".surcharge, " . $this->db->dbprefix('return_sales') . ".grand_total")
                ->join('sales', 'sales.id=return_sales.sale_id', 'left')
                ->from('return_sales')
                ->group_by('return_sales.id')
                ->where('return_sales.warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select($this->db->dbprefix('return_sales') . ".id as id,".$this->db->dbprefix('return_sales') . ".date as date, " . $this->db->dbprefix('return_sales') . ".reference_no as ref, " . $this->db->dbprefix('sales') . ".reference_no as sal_ref, " . $this->db->dbprefix('return_sales') . ".biller, " . $this->db->dbprefix('return_sales') . ".customer, " . $this->db->dbprefix('return_sales') . ".surcharge, " . $this->db->dbprefix('return_sales') . ".grand_total")
                ->join('sales', 'sales.id=return_sales.sale_id', 'left')
                ->from('return_sales')
                ->group_by('return_sales.id');
        }
       // if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin) {
        //    $this->datatables->where('return_sales.created_by', $this->session->userdata('user_id'));
       // } elseif ($this->Customer) {
        //    $this->datatables->where('return_sales.customer_id', $this->session->userdata('customer_id'));
       // }
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
    
    
    
    
//         function getReturns1()
//     {
//         $this->sma->checkPermissions('dsm');
//  $this->load->library('datatables');
//         $this->datatables
 
//                       ->select("dsm_alignments.id,dsm_alignments.dsm_name,dsm_alignments.dsm_rep_name,dsm_alignments.manager_level")
//                       ->from("dsm_alignments");
         
//         $this->datatables->add_column("Actions", $action, "id");
//         echo $this->datatables->generate();
//     }
    
    
    function getReturns1()
    {
        $this->sma->checkPermissions('dsm');
        $this->load->library('datatables');
        $this->datatables
           
                      ->select("dsm_alignments.id,dsm_alignments.dsm_name,dsm_alignments.dsm_rep_name,dsm_alignments.manager_level")
                      ->from("dsm_alignments")
           
-> add_column("Actions", $action, "id");        //->unset_column('id');
        echo $this->datatables->generate();
    }
  

    function modal_view($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        //$this->sma->view_rights($inv->created_by, TRUE);
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['default_currency'] = array('code'=>'KS');
        $this->data['shop'] = $this->companies_model->getShopById($inv->shop_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);

        $this->load->view($this->theme.'sales/modal_view', $this->data);
    }
    
    function modal_view2($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getDiscountByID($id);
        //$this->sma->view_rights($inv->created_by, TRUE);
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['default_currency'] = array('code'=>'KS');
        $this->data['shop'] = $this->companies_model->getShopById($inv->shop_id);
        //$this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        $this->data['rows'] = $this->sales_model->getAllDiscountItems($id);

        $this->load->view($this->theme.'discounts/modal_view', $this->data);
    }
    
    function approve($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        
        if($this->sales_model->approveDeclineDiscount($id, array('status'=>1))){
            $this->session->set_flashdata('message', lang("discount_approved"));
            
            redirect("sales/index2");
        }else{
            $this->session->set_flashdata('message', lang("discount_approval_fail"));
            
            redirect("sales/index2");
        }
        
    }
    
    function decline($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if($this->sales_model->approveDeclineDiscount($id, array('status'=>2))){
            $this->session->set_flashdata('message', lang("discount_approved"));
            
            redirect("sales/index2");
        }else{
            $this->session->set_flashdata('message', lang("discount_approval_fail"));
            
            redirect("sales/index2");
        }
    }
    
    function modal_viewInvoice($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvByID($id);
        //$this->sma->view_rights($inv->created_by, TRUE);
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['default_currency'] = array('code'=>'KS');
        $this->data['shop'] = $this->companies_model->getShopById($inv->shop_id);
        //$this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        $this->data['rows'] = $this->sales_model->getAlltemsOnInvoice($id);

        $this->load->view($this->theme.'invoices/modal_view', $this->data);
    }
    
    function modal_viewCheque($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getCheqByID($id);
        //$this->sma->view_rights($inv->created_by, TRUE);
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['default_currency'] = array('code'=>'KS');
        $this->data['shop'] = $this->companies_model->getShopById($inv->shop_id);
        //$this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        $this->data['rows'] = $this->sales_model->getAlltemsOnCheque($id);

        $this->load->view($this->theme.'cheques/modal_view', $this->data);
    }
    
    function approveInvoice($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        
        if($this->sales_model->approveDeclineInvoice($id, array('status'=>1))){
            $this->session->set_flashdata('message', lang("invoice_approved"));
            
            redirect("sales/invoice");
        }else{
            $this->session->set_flashdata('message', lang("invoice_approval_failed"));
            
            redirect("sales/invoice");
        }
        
    }
    
    function declineInvoice($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if($this->sales_model->approveDeclineInvoice($id, array('status'=>2))){
            $this->session->set_flashdata('message', lang("invoice_declined"));
            
            redirect("sales/invoice");
        }else{
            $this->session->set_flashdata('message', lang("invoice_decline_failed"));
            
            redirect("sales/invoice");
        }
    }
    
    function approveCheque($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        
        if($this->sales_model->approveDeclineCheque($id, array('status'=>1))){
            $this->session->set_flashdata('message', lang("cheque_approved"));
            
            redirect("sales/cheque");
        }else{
            $this->session->set_flashdata('message', lang("cheque_approval_fail"));
            
            redirect("sales/cheque");
        }
        
    }
    
    function declineCheque($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if($this->sales_model->approveDeclineCheque($id, array('status'=>2))){
            $this->session->set_flashdata('message', lang("Cheque_declined"));
            
            redirect("sales/cheque");
        }else{
            $this->session->set_flashdata('message', lang("Cheque_decline_failed"));
            
            redirect("sales/cheque");
        }
    }


    function view($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        //$this->sma->view_rights($inv->created_by);
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['shop'] = $this->companies_model->getShopById($inv->shop_id);
        $this->data['default_currency'] = array('code'=>'KS');
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : NULL;
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
        //$this->data['return_items'] = $return ? $this->sales_model->getAllReturnItems($return->id) : NULL;
        $this->data['paypal'] = $this->sales_model->getPaypalSettings();
        $this->data['skrill'] = $this->sales_model->getSkrillSettings();

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_sales_details'), 'bc' => $bc);
        $this->page_construct('sales/view', $meta, $this->data);
    }

    function view_return($id = NULL)
    {
        $this->sma->checkPermissions('return_sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getReturnByID($id);
       // $this->sma->view_rights($inv->created_by);
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllReturnItems($id);
        $this->data['sale'] = $this->sales_model->getInvoiceByID($inv->sale_id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('view_return')));
        $meta = array('page_title' => lang('view_return_details'), 'bc' => $bc);
        $this->page_construct('sales/view_return', $meta, $this->data);
    }

    function pdf($id = NULL, $view = NULL, $save_bufffer = NULL)
    {
        $this->sma->checkPermissions('pdf', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        //$this->sma->view_rights($inv->created_by);
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['shop'] = $this->companies_model->getShopById($inv->shop_id);
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        if($inv->sale_status=="pending"){
     $this->data['title'] ="INVOICE";
        }  else {
            $this->data['title'] ="INVOICE";
        }
        $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
        $this->data['return_items'] = $return ? $this->sales_model->getAllReturnItems($return->id) : NULL;
        //$this->data['paypal'] = $this->sales_model->getPaypalSettings();
        //$this->data['skrill'] = $this->sales_model->getSkrillSettings();

        $name = lang("sale") . "_" .$inv->id. ".pdf";
        $html = $this->load->view($this->theme . 'sales/pdf', $this->data, TRUE);
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, FALSE, $this->data['biller']->invoice_footer);
        }
    }
    
    function receipt($id = NULL, $view = NULL, $save_bufffer = NULL)
    {
        $this->sma->checkPermissions('index', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        //$this->sma->view_rights($inv->created_by);
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->companies_model->getcustomerByID($inv->customer_id);
        $this->data['vehicle'] = $this->vehicles_model->getVehicleByID($inv->vehicle_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->companies_model->getCompanyByID('934');
        $this->data['created_by'] = $this->site->getCompanyByID($inv->created_by);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $return = $this->sales_model->getReturnBySID($id);
        $this->data['return_sale'] = $return;
        if($inv->sale_status=="pending"){
        $this->data['title'] ="INVOICE";
        }  else {
            $this->data['title'] ="INVOICE";
        }
        $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
        $this->data['return_items'] = $return ? $this->sales_model->getAllReturnItems($return->id) : NULL;
        //$this->data['paypal'] = $this->sales_model->getPaypalSettings();
        //$this->data['skrill'] = $this->sales_model->getSkrillSettings();

        $name = lang("sale") . "_" .$id. ".pdf";
        $html = $this->load->view($this->theme . 'sales/receipt', $this->data, TRUE);
        if ($view) {
            $this->load->view($this->theme . 'sales/receipt', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, FALSE, $this->data['biller']->invoice_footer);
        }
    }

    function email($id = NULL)
    {
        $this->sma->checkPermissions('email', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->sales_model->getInvoiceByID($id);
        $this->form_validation->set_rules('to', lang("to") . " " . lang("email"), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', lang("subject"), 'trim|required');
        $this->form_validation->set_rules('cc', lang("cc"), 'trim');
        $this->form_validation->set_rules('bcc', lang("bcc"), 'trim');
        $this->form_validation->set_rules('note', lang("message"), 'trim');

        if ($this->form_validation->run() == true) {
           // $this->sma->view_rights($inv->created_by);
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
            $customer = $this->site->getCompanyByID($inv->customer_id);
            $this->load->library('parser');
            $parse_data = array(
                'reference_number' => $inv->reference_no,
                'contact_person' => $customer->name,
                'company' => $customer->company,
                'site_link' => base_url(),
                'site_name' => $this->Settings->site_name,
                'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '" alt="' . $this->Settings->site_name . '"/>',
                'footer' => '<img src="' . base_url() . 'assets/uploads/logos/footerimage.png" alt="' . $this->Settings->site_name . '"/>'
            );
            $msg = $this->input->post('note');
            $message = $this->parser->parse_string($msg, $parse_data);

            $biller = $this->site->getCompanyByID($inv->biller_id);
            $paypal = $this->sales_model->getPaypalSettings();
            $skrill = $this->sales_model->getSkrillSettings();
            $btn_code = '<div id="payment_buttons" class="text-center margin010"> </div>';
            $message = $message . $btn_code;

            $attachment = $this->pdf($id, NULL, 'S');
        } elseif ($this->input->post('send_email')) {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sma->send_email($to, $subject, $message, NULL, NULL, $attachment, $cc, $bcc)) {
            delete_files($attachment);
            $this->session->set_flashdata('message', lang("email_sent"));
            redirect("reports/sales");
        } else {

            if (file_exists('./themes/' . $this->theme . '/views/email_templates/sale.html')) {
                $sale_temp = file_get_contents('themes/' . $this->theme . '/views/email_templates/sale.html');
            } else {
                $sale_temp = file_get_contents('./themes/default/views/email_templates/sale.html');
            }

            $this->data['subject'] = array('name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject', lang('invoice').' (' . $inv->reference_no . ') '.lang('from').' ' . $this->Settings->site_name),
            );
            $this->data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note', $sale_temp),
            );
            $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/email', $this->data);
        }
    }

    /* ------------------------------------------------------------------ */


    function add($quote_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        //$this->form_validation->set_rules('biller', lang("biller"), 'required');
        $this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');
        $this->form_validation->set_rules('payment_status', lang("payment_status"), 'required');

        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
          
            $date = date('Y-m-d H:i:s');
         
            $salestype = $this->input->post('sales_type');
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $ssocustomer_id = $this->input->post('ssocustomer');
            $biller_id = $this->input->post('biller');
					$ref_doc_no = $this->input->post('ref_doc_no');
            $total_items = $this->input->post('total_items');
            $sale_status = $this->input->post('sale_status');
             $country = $this->input->post('country');
            $payment_status = $this->input->post('payment_status');
            $payment_term = $this->input->post('payment_term');
            //$due_date = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days')) : NULL;
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->company ? $customer_details->company : $customer_details->name;
              $ssocustomer_details = $this->site->getCustByID($ssocustomer_id);
            $ssocustomer = $ssocustomer_details->company ? $ssocustomer_details->company : $ssocustomer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
            $quote_id = $this->input->post('quote_id') ? $this->input->post('quote_id') : NULL;
            $country_details = $this->sales_model->getCountryByID($country);
            $country_code = $country_details->country; 
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : NULL;
                //$option_details = $this->sales_model->getProductOptionByID($item_option);
                $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : NULL;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : NULL;

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->sales_model->getProductByCode($item_code) : NULL;
                    $unit_price = $real_unit_price;
                    $pr_discount = 0;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = (($this->sma->formatDecimal($unit_price)) * (Float)($pds[0])) / 100;
                        } else {
                            $pr_discount = $this->sma->formatDecimal($discount);
                        }
                    }

                    $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
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
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'serial_no' => $item_serial,
                        'real_unit_price' => $real_unit_price
                    ); 
            }
            //print_r($products);
           // die();
            if (empty($products)) {
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
            	$msr_details = $this->sales_model->msr_customer_alignments($ssocustomer_id,$item_id,$country_details->id);
            $data = array('date' => $date,
                'gmid'=> $item_code,
                'reference_no' => $reference,
                'distributor_id' => $customer_id,
                'distributor' => $customer,
                'customer' => $ssocustomer,
                'customer_id' => $ssocustomer_id,
                'country'=> $country_code,
                'country_id'=> $country,
                'products'=>$item_name,
                'product_id' =>$item_id,
                'value'=> $subtotal,
                'total' => $this->sma->formatDecimal($total),
                'total_discount' => $total_discount,
                'product_tax' => $this->sma->formatDecimal($product_tax),
                'sales_type' =>$salestype,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'quantity_units' => $item_quantity,
                'msr_alignment_id' => $msr_details->sf_alignment_id,
                'msr_alignment_name' =>$msr_details->sf_alignment_name,
                'paid' => 0,
                'created_by' => $this->session->userdata('user_id')
            );
            //print_r($data);
            //die();
            if ($payment_status == 'partial' || $payment_status == 'paid') {
                if ($this->input->post('paid_by') == 'gift_card') {
                    $gc = $this->site->getGiftCardByNO($this->input->post('gift_card_no'));
                    $amount_paying = $grand_total >= $gc->balance ? $gc->balance : $grand_total;
                    $gc_balance = $gc->balance - $amount_paying;
                    $payment = array(
                        'date' => $date,
                        'reference_no' => $this->input->post('payment_reference_no'),
                        'amount' => $this->sma->formatDecimal($amount_paying),
                        'paid_by' => $this->input->post('paid_by'),
                        'cheque_no' => $this->input->post('cheque_no'),
                        'cc_no' => $this->input->post('gift_card_no'),
                        'cc_holder' => $this->input->post('pcc_holder'),
                        'cc_month' => $this->input->post('pcc_month'),
                        'cc_year' => $this->input->post('pcc_year'),
                        'cc_type' => $this->input->post('pcc_type'),
                        'created_by' => $this->session->userdata('user_id'),
                        'note' => $this->input->post('payment_note'),
                        'type' => 'received',
                        'gc_balance' => $gc_balance
                    );
                } else {
                    $payment = array(
                        'date' => $date,
                        'reference_no' => $this->input->post('payment_reference_no'),
                        'amount' => $this->sma->formatDecimal($this->input->post('amount-paid')),
                        'paid_by' => $this->input->post('paid_by'),
                        'cheque_no' => $this->input->post('cheque_no'),
                        'cc_no' => $this->input->post('pcc_no'),
                        'cc_holder' => $this->input->post('pcc_holder'),
                        'cc_month' => $this->input->post('pcc_month'),
                        'cc_year' => $this->input->post('pcc_year'),
                        'cc_type' => $this->input->post('pcc_type'),
                        'created_by' => $this->session->userdata('user_id'),
                        'note' => $this->input->post('payment_note'),
                        'type' => 'received'
                    );
                }
            } else {
                $payment = array();
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

            // $this->sma->print_arrays($data, $products, $payment);
        }


        if ($this->form_validation->run() == true && $this->sales_model->addSale($data, $products, $payment)) {
            $this->session->set_userdata('remove_slls', 1);
            if ($quote_id) {
                $this->db->update('quotes', array('status' => 'completed'), array('id' => $quote_id));
            }
             //send admin email
       
            $this->session->set_flashdata('message', lang("sale_added"));
            
            redirect("reports/sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['quote_id'] = $quote_id;
            $this->data['salespeople'] = $this->site->getAllSalesmen();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['companies']=$this->site->getAllCustomerCompanies();
            $this->data['bu']=  $this->site->getAllBu();
		    $this->data['customers']=$this->site->getAllCustomerCustomers();
            $this->data['slnumber'] = ''; //$this->site->getReference('so');
            $this->data['payment_ref'] = $this->site->getReference('pay');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale')));
            $meta = array('page_title' => lang('add_sale'), 'bc' => $bc);
            $this->page_construct('sales/add', $meta, $this->data);
        }
        
       
        
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    function edit($id = NULL)
    {
        $this->sma->checkPermissions('edit', TRUE, 'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        //$this->form_validation->set_rules('biller', lang("biller"), 'required');
        //$this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');
        //$this->form_validation->set_rules('payment_status', lang("payment_status"), 'required');
        $this->form_validation->set_rules('note', lang("note"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no');
          //  if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim('01/'.$this->input->post('date')));
                 
           // } else {
             //   $date = date('Y-m-d H:i:s');
           // }
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $ssocustomer_id = $this->input->post('ssocustomer');
            $french_name = $this->input->post('country');
            
            $country_det= $this->sales_model->getCountryByID($this->input->post('country'));
             $mvntcode = $this->input->post('mvntcode');
            $biller_id = $this->input->post('biller');
             $cashier_id = $this->input->post('cashier');
            if(isset($cashier_id)){
              $cashier=$cashier_id;  
            }
             
            $total_items = $this->input->post('total_items');
            $sale_status = $this->input->post('sale_status');
            $payment_status = $this->input->post('payment_status');
            $payment_term = $this->input->post('payment_term');
            $due_date = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days')) : NULL;
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->company ? $customer_details->company : $customer_details->name;
             $ssocustomer_details = $this->site->getCustByID($ssocustomer_id);
            $ssocustomer = $ssocustomer_details->company ? $ssocustomer_details->company : $ssocustomer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : NULL;
                //$option_details = $this->sales_model->getProductOptionByID($item_option);
                $real_unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : NULL;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : NULL;

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->sales_model->getProductByCode($item_code) : NULL;
                    $unit_price = $real_unit_price;
                    $pr_discount = 0;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = (($this->sma->formatDecimal($unit_price)) * (Float)($pds[0])) / 100;
                        } else {
                            $pr_discount = $this->sma->formatDecimal($discount);
                        }
                    }

                    $unit_price = $this->sma->formatDecimal($unit_price);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
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
                    $subtotal = ($item_net_price * $item_quantity);

                    

                    $total += $item_net_price * $item_quantity;
                }
                $products[] = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_price' => $item_net_price,
                        'unit_price' => $this->sma->formatDecimal($item_net_price),
                        'quantity' => $item_quantity,
                        //'warehouse_id' => $warehouse_id,
                        //'item_tax' => $pr_item_tax,
                        //'tax_rate_id' => $pr_tax,
                        //'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'serial_no' => $item_serial,
                        'real_unit_price' => $item_net_price
                    );
                
            }
            //print_r($products);
           // die();
            if (empty($products)) {
               $this->form_validation->set_rules('product', lang("order_items"), 'required');
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
          $msr_details = $this->sales_model->msr_customer_alignments($ssocustomer_id,$item_id,$country_details->id);
          $cashier_name= $this->site->getUser($cashier);
            $data = array('date' => $date,
                'reference_no' => $reference,
                'country' =>$country_det->country,
                'country_id' => $french_name,
                'distributor' => $customer,
                'distributor_id' => $customer_id,
                'customer' => $ssocustomer,
                'customer_id' => $ssocustomer_id,
                'quantity_units' => $item_quantity,
                'value' => $subtotal,
                'grand_total' => $subtotal,
                'shipping' => $subtotal,
                'total' => $subtotal,
                'products' => $item_name,
                'product_id' => $item_id,
                'msr_alignment_id' => $msr_details->sf_alignment_id,
                'msr_alignment_name' =>$msr_details->sf_alignment_name,
                //'movement'=>$mvntcode,
                
                //'customer_id' => $customer_id,
               // 'customer' => $customer,
                //'biller_id' => $biller_id,
                //'biller' => $biller,
                //'warehouse_id' => $warehouse_id,
                //'note' => $note,
                //'cashier_id'=>$cashier,
                //'cashier'=>$cashier_name->first_name." ".$cashier_name->last_name,
                //'staff_note' => $staff_note,
                //'total' => $this->sma->formatDecimal($total),
                //'product_discount' => $this->sma->formatDecimal($product_discount),
                //'order_discount_id' => $order_discount_id,
                //'order_discount' => $order_discount,
                //'total_discount' => $total_discount,
               // 'product_tax' => $this->sma->formatDecimal($product_tax),
               // 'order_tax_id' => $order_tax_id,
               // 'order_tax' => $order_tax,
               // 'total_tax' => $total_tax,
               // 'shipping' => $this->sma->formatDecimal($shipping),
               // 'grand_total' => $grand_total,
               // 'total_items' => $total_items,
               // 'sale_status' => $sale_status,
               // 'payment_status' => $payment_status,
               // 'payment_term' => $payment_term,
                //'due_date' => $due_date,
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            //die(print_r($data));

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

        if ($this->form_validation->run() == true && $this->sales_model->updateSale($id, $data, $products)) {
            $this->session->set_userdata('remove_slls', 1);
            $this->session->set_flashdata('message', lang("sale_updated"));
            redirect("reports/sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $this->sales_model->getInvoiceByID($id);
          
//            if ($this->data['inv']->date <= date('Y-m-d', strtotime('-3 months'))) {
//                $this->session->set_flashdata('error', lang("sale_x_edited_older_than_3_months"));
//                redirect($_SERVER["HTTP_REFERER"]);
//            }
            $inv_items = $this->sales_model->getAllInvoiceItems($id);

            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                if (!$row) {
                    $row = json_decode('{}');
                    $row->tax_method = 0;
                    $row->quantity = 0;
                } else {
                    unset($row->details, $row->product_details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $pis = $this->sales_model->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if($pis){
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $row->qty = $item->quantity;
                $row->quantity += $item->quantity;
                $row->discount = $item->discount ? $item->discount : '0';
                $row->price = $this->sma->formatDecimal($item->net_unit_price+$this->sma->formatDecimal($item->item_discount/$item->quantity));
                $row->unit_price = $row->tax_method ? $item->unit_price+$this->sma->formatDecimal($item->item_discount/$item->quantity)+$this->sma->formatDecimal($item->item_tax/$item->quantity) : $item->unit_price+($item->item_discount/$item->quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate = $item->tax_rate_id;
                $row->serial = $item->serial_no;
                $row->option = $item->option_id;
                $options = $this->sales_model->getProductOptions($row->id, $item->warehouse_id);

                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->sales_model->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                        if($pis){
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        $option_quantity += $item->quantity;
                        if($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }

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
            //$this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['billers'] = ($this->Owner || $this->Admin) ? $this->site->getAllCompanies('biller') : NULL;
            $this->data['companies']=$this->site->getAllCustomerCompanies();
		 $this->data['sanoficustomer']=$this->site->getAllCustomerCustomers();
            $this->data['movemntcodes'] = $this->site->getAllmovementcodes();
            $this->data['countries'] = $this->sales_model->getCountries();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
          $this->data['cashiers'] =$this->sales_model->getCashier();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('edit_sale')));
            $meta = array('page_title' => lang('edit_sale'), 'bc' => $bc);
            $this->page_construct('sales/edit', $meta, $this->data);
        }
    }

    /* ------------------------------- */

    function return_sale($id = NULL)
    {
        $this->sma->checkPermissions('return_sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        // $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paying_by"), 'required');

        if ($this->form_validation->run() == true) {
            $sale = $this->sales_model->getInvoiceByID($id);
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('re');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $return_surcharge = $this->input->post('return_surcharge') ? $this->input->post('return_surcharge') : 0;
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
                $sale_item_id = $_POST['sale_item_id'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : NULL;
                //$option_details = $this->sales_model->getProductOptionByID($item_option);
                $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : NULL;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : NULL;

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->sales_model->getProductByCode($item_code) : NULL;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = (($this->sma->formatDecimal($unit_price)) * (Float)($pds[0])) / 100;
                        } else {
                            $pr_discount = $this->sma->formatDecimal($discount);
                        }
                    } else {
                        $pr_discount = 0;
                    }
                    $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $pr_tax = $item_tax_rate;
                        $tax_details = $this->site->getTaxRateByID($pr_tax);
                        if ($tax_details->type == 1 && $tax_details->rate != 0) {

                            if (!$product_details->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / (100 + $tax_details->rate));
                                $tax = $tax_details->rate . "%";
                            } else {
                                $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / 100);
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

                    $item_net_price = $product_details->tax_method ? $this->sma->formatDecimal($unit_price-$pr_discount) : $this->sma->formatDecimal($unit_price-$item_tax-$pr_discount);
                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

                    $products[] = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_price' => $item_net_price,
                        // 'unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity' => $item_quantity,
                        'warehouse_id' => $sale->warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $pr_tax,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'serial_no' => $item_serial,
                        'real_unit_price' => $real_unit_price,
                        'sale_item_id' => $sale_item_id
                    );

                    $total += $item_net_price * $item_quantity;
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('discount')) {
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
            $total_discount = $order_discount + $product_discount;

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
            $grand_total = $this->sma->formatDecimal($this->sma->formatDecimal($total) + $total_tax - $this->sma->formatDecimal($return_surcharge) - $order_discount);
            $data = array('date' => $date,
                'sale_id' => $id,
                'reference_no' => $reference,
                'customer_id' => $sale->customer_id,
                'customer' => $sale->customer,
                'biller_id' => $sale->biller_id,
                'biller' => $sale->biller,
                'warehouse_id' => $sale->warehouse_id,
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
                'surcharge' => $this->sma->formatDecimal($return_surcharge),
                'grand_total' => $grand_total,
                'created_by' => $this->session->userdata('user_id'),
            );
            if ($this->input->post('amount-paid') && $this->input->post('amount-paid') != 0) {
                $payment = array(
                    'date' => $date,
                    'reference_no' => $this->input->post('payment_reference_no'),
                    'amount' => $this->sma->formatDecimal($this->input->post('amount-paid')),
                    'paid_by' => $this->input->post('paid_by'),
                    'cheque_no' => $this->input->post('cheque_no'),
                    'cc_no' => $this->input->post('pcc_no'),
                    'cc_holder' => $this->input->post('pcc_holder'),
                    'cc_month' => $this->input->post('pcc_month'),
                    'cc_year' => $this->input->post('pcc_year'),
                    'cc_type' => $this->input->post('pcc_type'),
                    'created_by' => $this->session->userdata('user_id'),
                    'type' => 'returned'
                );
            } else {
                $payment = array();
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

            // $this->sma->print_arrays($data, $products, $payment);
        }

        if ($this->form_validation->run() == true && $this->sales_model->returnSale($data, $products, $payment)) {
            $this->session->set_flashdata('message', lang("return_sale_added"));
            redirect("sales/return_sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $this->sales_model->getInvoiceByID($id);
            if ($this->data['inv']->sale_status != 'completed') {
                $this->session->set_flashdata('error', lang("sale_status_x_competed"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $inv_items = $this->sales_model->getAllInvoiceItems($id);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                if (!$row) {
                    $row = json_decode('{}');
                    $row->tax_method = 0;
                    $row->quantity = 0;
                } else {
                    unset($row->details, $row->product_details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $pis = $this->sales_model->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if($pis){
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id = $item->product_id;
                $row->sale_item_id = $item->id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $row->qty = $item->quantity;
                $row->oqty = $item->quantity;
                $row->discount = $item->discount ? $item->discount : '0';
                $row->price = $this->sma->formatDecimal($item->net_unit_price+$this->sma->formatDecimal($item->item_discount/$item->quantity));
                $row->unit_price = $row->tax_method ? $item->unit_price+$this->sma->formatDecimal($item->item_discount/$item->quantity)+$this->sma->formatDecimal($item->item_tax/$item->quantity) : $item->unit_price+($item->item_discount/$item->quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate = $item->tax_rate_id;
                $row->serial = $item->serial_no;
                $row->option = $item->option_id;
                $options = $this->sales_model->getProductOptions($row->id, $item->warehouse_id, TRUE);
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
            $this->data['payment_ref'] = $this->site->getReference('pay');
            $this->data['reference'] = ''; // $this->site->getReference('re');
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('return_sale')));
            $meta = array('page_title' => lang('return_sale'), 'bc' => $bc);
            $this->page_construct('sales/return_sale', $meta, $this->data);
        }
    }


    /* ------------------------------- */

    function delete($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $deleteddata=$this->sales_model->getInvoiceByIDAsArray($id);


        $this->db->insert('deleted_sales',$deleteddata);
        //die(print_r($deleteddata));
        if ($this->sales_model->deleteSale($id)) {
            if($this->input->is_ajax_request()) {
                echo lang("sale_deleted"); die();
            }
            $this->session->set_flashdata('message', lang('sale_deleted'));
            redirect('sales');
        }
    }

    function delete_return($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->sales_model->deleteReturn($id)) {
            if($this->input->is_ajax_request()) {
                echo lang("return_sale_deleted"); die();
            }
            $this->session->set_flashdata('message', lang('return_sale_deleted'));
            redirect('welcome');
        }
    }

    function sale_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');
//die(print_r($_POST));
        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->sales_model->deleteSale($id);
                    }
                    $this->session->set_flashdata('message', lang("sales_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                
                if ($this->input->post('form_action') == 'delete_deleted') {
                    foreach ($_POST['val'] as $id) {
                        $this->sales_model->deleteDeletedSale($id);
                    }
                    $this->session->set_flashdata('message', lang("sales_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
if ($this->input->post('form_action') == 'bulk_payment') {
  
    if($this->input->post('paid_by')=="selectmethod"){
       $this->session->set_flashdata('error', lang('select_payment_method'));
            redirect($_SERVER["HTTP_REFERER"]);  
    }
     $totalamount=0;
      $date = date('Y-m-d H:i:s');
      $paymentmethod=$this->input->post('paid_by');
      if(strtolower($paymentmethod)=="mpesa"){
           $mpesareference=$this->input->post('reference');
      }else if(strtolower($paymentmethod)=="costcenter"){
            $costcenterref=$this->input->post('reference');
      }
      else if(strtolower($paymentmethod)=="cheque"){
            $chequeref=$this->input->post('reference');
      }
        else if(strtolower($paymentmethod)=="cc"){
            $ccref=$this->input->post('reference');
      }
     
                    foreach ($_POST['val'] as $id) {
                        $salesinvoice=$this->sales_model->getInvoiceByID($id);
                       
                        if($salesinvoice->payment_status=="due"){
                     $payment = array(
                'date' => $date,
                'sale_id' => $id,
                'reference_no' => $salesinvoice->reference_no,
                'amount' =>$salesinvoice->grand_total,
                'bill_change' =>0 ,
                'paid_by' => $this->input->post('paid_by'),
                
                'chef_id' => $salesinvoice->chef_id,
                'chef' => $salesinvoice->chef,
                
                'cashier_id' => $salesinvoice->cashier_id,
                'cashier' => $salesinvoice->cashier,
                
                'mpesa_transaction_no' => @$mpesareference,
                'cost_center_no' => @$costcenterref,
                'cheque_no' =>@$chequeref,
                'cc_no' =>@$ccref,// $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder' => "",
                'cc_month' => "",
                'cc_year' => "",
                'cc_type' => "",
               // 'cc_cvv2' =>"",
                'note' =>"",
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'received'
            );
                     
                     $msg = $this->sales_model->addPayment($payment);
                        
                    }
                    }
                    $this->session->set_flashdata('message', lang("payments_confirmed"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                if ($this->input->post('form_action') == 'export_excel' || 
                        $this->input->post('form_action') == 'export_pdf'|| 
                        $this->input->post('form_action') == 'bulk_payment') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('sales'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Sales_Type'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Country'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('distributor'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('products'));
					$this->excel->getActiveSheet()->SetCellValue('G1', lang('quantity_units'));
					$this->excel->getActiveSheet()->SetCellValue('H1', lang('value'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('source'));

                    
                     $bulk_array=array();
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sale = $this->sales_model->getInvoiceByID($id);
						$product = $this->sales_model->getAllInvoiceItems($id);
						
						foreach ($product as $item) {
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, date('m-Y',strtotime($sale->date)));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sale->sales_type);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sale->country);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sale->distributor);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sale->customer);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $sale->products);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale->quantity_units);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $sale->value);
						$this->excel->getActiveSheet()->SetCellValue('I' . $row, $sale->source);
					
                        array_push($bulk_array,$sale);
                        $row++;
                    }}

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'sales_' . date('Y_m_d_H_i_s');
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

//     function bulk_payment($sale_id = NULL, $modal = NULL)
//    {
      
//                       
//                        print_r($bulk_rows);
//                      // die(); 
//                       $this->session->set_flashdata('error',  $_POST['val']);
//                //redirect($_SERVER["HTTP_REFERER"]); 
//                        return 'sales/bulk_payment/'.$_POST['val'];
//                    }
//         
//        $this->load->helper('text');
//        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
//        $this->data['message'] = $this->session->flashdata('message');
//        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
//        $inv = $this->pos_model->getInvoiceByID($sale_id);
//        $biller_id = $inv->biller_id;
//        $customer_id = $inv->customer_id;
//        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
//        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
//        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
//        $this->data['pos'] = $this->pos_model->getSetting();
//        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code39', 30);
//        $this->data['inv'] = $inv;
//        $this->data['sid'] = $sale_id;
//        $this->data['modal'] = $modal;
//        $this->data['page_title'] = $this->lang->line("invoice");
//     $this->load->view($this->theme . 'pos/view_complete', $this->data);
//    }
    /* ------------------------------- */

function movemnt_code()
    {
        $this->sma->checkPermissions();

        $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('movement_code')));
        $meta = array('page_title' => lang('movement_code'), 'bc' => $bc);
        $this->page_construct('sales/movement_code', $meta, $this->data);

    }
	function getMovementCodes()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id,m_code,movement_name,pl, scenario")
            ->from("movementcodes")
            //->join("currencies","currencies.id=companies.country","left")
           // ->where('group_name', 'customer')
           // ->where('is_subsidiary',0)
            ->add_column("Actions", "<center><a class=\"tip\" title='" . $this->lang->line("edit_movementCode") . "' href='" . site_url('sales/editmvcode/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>  <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_movementcode") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('sales/deletemvcode/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();
    }
	function addMovementCode()
   {
$this->sma->checkPermissions(false, true);
        $this->form_validation->set_rules('m_code', $this->lang->line('m_code'), 'required');
        $this->form_validation->set_rules('movement_name', $this->lang->line('movement_name'), 'required');
        if ($this->form_validation->run() == true) {
           // $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $data = array('m_code' => $this->input->post('m_code'),
                'movement_name' => $this->input->post('movement_name'),
                'pl' => $this->input->post('pl'),
                'scenario' => $this->input->post('scenario'),
                //'customer_group_name' => $cg->name,
            );
        } elseif ($this->input->post('add_mvcode')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sales_model->addMvcode($data)) {
            $this->session->set_flashdata('message', $this->lang->line("Movement_Code_Added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            //$this->data['movement'] = $mvcode_details;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            
            $this->load->view($this->theme.'sales/mvcode_add', $this->data);
			
        }
    }

	function editmvcode($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $mvcode_details = $this->sales_model->getMvcodeByID($id);
		
        if ($this->input->post('m_code') != $mvcode_details->m_code) {
            //$this->form_validation->set_rules('code', lang("email_address"), 'is_unique[companies.email]');
        }

        $this->form_validation->set_rules('m_code', $this->lang->line('m_code'), 'required');
        $this->form_validation->set_rules('movement_name', $this->lang->line('movement_name'), 'required');
        if ($this->form_validation->run() == true) {
           // $cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $data = array('m_code' => $this->input->post('m_code'),
                'movement_name' => $this->input->post('movement_name'),
                'pl' => $this->input->post('pl'),
                'scenario' => $this->input->post('scenario'),
                //'customer_group_name' => $cg->name,
            );
        } elseif ($this->input->post('edit_mvcode')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sales_model->updatemvcode($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("Movement_Code_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['movement'] = $mvcode_details;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            
            $this->load->view($this->theme.'sales/mvcode_edit', $this->data);
			
        }
    }

	
    function deliveries()
    {
        $this->sma->checkPermissions();

        $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('deliveries')));
        $meta = array('page_title' => lang('deliveries'), 'bc' => $bc);
        $this->page_construct('sales/deliveries', $meta, $this->data);

    }

    function getDeliveries()
    {
        $this->sma->checkPermissions('deliveries');

        $detail_link = anchor('sales/view_delivery/$1', '<i class="fa fa-file-text-o"></i> ' . lang('delivery_details'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('sales/email_delivery/$1', '<i class="fa fa-envelope"></i> ' . lang('email_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link = anchor('sales/edit_delivery/$1', '<i class="fa fa-edit"></i> ' . lang('edit_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $pdf_link = anchor('sales/pdf_delivery/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_delivery") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('sales/delete_delivery/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_delivery') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
    <ul class="dropdown-menu pull-right" role="menu">
        <li>' . $detail_link . '</li>
        <li>' . $edit_link . '</li>
        <li>' . $pdf_link . '</li>
        <li>' . $delete_link . '</li>
    </ul>
</div></div>';

        $this->load->library('datatables');
        //GROUP_CONCAT(CONCAT('Name: ', sale_items.product_name, ' Qty: ', sale_items.quantity ) SEPARATOR '<br>')
        $this->datatables
            ->select("deliveries.id as id, date, do_reference_no, sale_reference_no, customer, address")
            ->from('deliveries')
            ->join('sale_items', 'sale_items.sale_id=deliveries.sale_id', 'left')
            ->group_by('deliveries.id');
        $this->datatables->add_column("Actions", $action, "id");

        echo $this->datatables->generate();
    }

    function pdf_delivery($id = NULL, $view = NULL, $save_bufffer = NULL)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $deli = $this->sales_model->getDeliveryByID($id);

        $this->data['delivery'] = $deli;
        $sale = $this->sales_model->getInvoiceByID($deli->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($sale->biller_id);
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithDetails($deli->sale_id);
        $this->data['user'] = $this->site->getUser($deli->created_by);


        $name = lang("delivery") . "_" . str_replace('/', '_', $deli->do_reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/pdf_delivery', $this->data, TRUE);
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf_delivery', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }
    }

    function view_delivery($id = NULL)
    {
        $this->sma->checkPermissions('deliveries');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $deli = $this->sales_model->getDeliveryByID($id);

        $this->data['delivery'] = $deli;
        $sale = $this->sales_model->getInvoiceByID($deli->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($sale->biller_id);
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithDetails($deli->sale_id);
        $this->data['user'] = $this->site->getUser($deli->created_by);
        $this->data['page_title'] = lang("delivery_order");

        $this->load->view($this->theme . 'sales/view_delivery', $this->data);
    }

    function add_delivery($id = NULL)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        //$this->form_validation->set_rules('do_reference_no', lang("do_reference_no"), 'required');
        $this->form_validation->set_rules('sale_reference_no', lang("sale_reference_no"), 'required');
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        $this->form_validation->set_rules('address', lang("address"), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $dlDetails = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'do_reference_no' => $this->input->post('do_reference_no') ? $this->input->post('do_reference_no') : $this->site->getReference('do'),
                'sale_reference_no' => $this->input->post('sale_reference_no'),
                'customer' => $this->input->post('customer'),
                'address' => $this->input->post('address'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'created_by' => $this->session->userdata('user_id')
            );
        } elseif ($this->input->post('add_delivery')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->sales_model->addDelivery($dlDetails)) {
            $this->session->set_flashdata('message', lang("delivery_added"));
            redirect("sales/deliveries");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $sale = $this->sales_model->getInvoiceByID($id);
            $this->data['customer'] = $this->site->getCompanyByID($sale->customer_id);
            $this->data['inv'] = $sale;
            $this->data['do_reference_no'] = ''; //$this->site->getReference('do');
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'sales/add_delivery', $this->data);
        }
    }

    function edit_delivery($id = NULL)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('do_reference_no', lang("do_reference_no"), 'required');
        $this->form_validation->set_rules('sale_reference_no', lang("sale_reference_no"), 'required');
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        $this->form_validation->set_rules('address', lang("address"), 'required');
        //$this->form_validation->set_rules('note', lang("note"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $dlDetails = array(
                'sale_id' => $this->input->post('sale_id'),
                'do_reference_no' => $this->input->post('do_reference_no'),
                'sale_reference_no' => $this->input->post('sale_reference_no'),
                'customer' => $this->input->post('customer'),
                'address' => $this->input->post('address'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'created_by' => $this->session->userdata('user_id')
            );

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
                $dlDetails['date'] = $date;
            }
        } elseif ($this->input->post('edit_delivery')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->sales_model->updateDelivery($id, $dlDetails)) {
            $this->session->set_flashdata('message', lang("delivery_updated"));
            redirect("sales/deliveries");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));


            $this->data['delivery'] = $this->sales_model->getDeliveryByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'sales/edit_delivery', $this->data);
        }
    }

    function delete_delivery($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->sales_model->deleteDelivery($id)) {
            echo lang("delivery_deleted");
        }

    }

    function delivery_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->sales_model->deleteDelivery($id);
                    }
                    $this->session->set_flashdata('message', lang("deliveries_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('deliveries'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('do_reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $delivery = $this->sales_model->getDeliveryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($delivery->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $delivery->do_reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $delivery->sale_reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $delivery->customer);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $delivery->address);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);

                    $filename = 'deliveries_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_delivery_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

    function payments($id = NULL)
    {
        $this->sma->checkPermissions(false, true);
        $this->data['payments'] = $this->sales_model->getInvoicePayments($id);
        $this->load->view($this->theme . 'sales/payments', $this->data);
    }

    function payment_note($id = NULL)
    {
        $payment = $this->sales_model->getPaymentByID($id);
        $inv = $this->sales_model->getInvoiceByID($payment->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = $this->lang->line("payment_note");

        $this->load->view($this->theme . 'sales/payment_note', $this->data);
    }

    function add_payment($id = NULL)
    {
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
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'received'
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


        if ($this->form_validation->run() == true && $this->sales_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $sale = $this->sales_model->getInvoiceByID($id);
            $this->data['inv'] = $sale;
            $this->data['payment_ref'] = ''; //$this->site->getReference('pay');
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'sales/add_payment', $this->data);
        }
    }

    function edit_payment($id = NULL)
    {
        $this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        
        //$this->form_validation->set_rules('note', lang("note"), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        
        if ($this->form_validation->run() == true) {
            $pymnt = $this->sales_model->getPaymentByID($id);
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $inv = $this->sales_model->getInvoiceByID($this->input->post('sale_id'));
            $payment = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $pymnt->paid_by,
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'type' => $this->input->post('status'),
                'created_by' => $inv->salesman_id
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


        if ($this->form_validation->run() == true && $this->sales_model->updatePayment($id, $payment)) {
            if($this->input->post('status')=="pending" && $pymnt->paid_by=="Cheque Payment" || $pymnt->paid_by=="Invoice Payment"){
                if($pymnt->paid_by=="Cheque Payment"){
                    $this->sales_model->removeChequePayment($inv->customer_id);
                }
                if($pymnt->paid_by=="Invoice Payment"){
                    $this->sales_model->removeInvoicePayment($inv->customer_id);
                }
                
            }
            
            $pmt = $this->sales_model->getPaymentByID($id);
            $inv = $this->sales_model->getInvoiceByID($pmt->sale_id);
            $all_pymnt = $this->sales_model->getPaymentsForSale($pmt->sale_id);
            $paid=0;
            foreach ($all_pymnt as $pyment){
                if($pyment->type=='received'){
                    $paid+=$pyment->amount;
                }
            }
            $bal = $inv->grand_total - $paid;
            $s_data=array('payment_status'=>'paid');
            if($bal==0){
                $this->db->update('sales', $s_data, array('id' => $inv->id));
            }
            
            if($this->input->post('status')=="received"){
                $data2 = array(
                'CustId' => $inv->customer_id,
                'TransactionRef' => $payment['reference_no'],
                'TransDate' => $payment['date'],
                'BankAcct' => 16,
                'Amount' => $payment['amount']);
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
                    $this->session->set_flashdata('message', lang("payment_updated"));
                    redirect("sales/view/".$id);
                } else {
                    $this->session->set_flashdata('error', lang("payment_not_updated"));
                    redirect("sales/view/".$id);
                }
            }else{
                $this->session->set_flashdata('message', lang("payment_updated"));
                    redirect("sales/view/".$id);
            }
            
            
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['payment'] = $this->sales_model->getPaymentByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'sales/edit_payment', $this->data);
        }
    }
//start void payment
function void_payment($id = NULL)
    {
        $this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        
        //$this->form_validation->set_rules('note', lang("note"), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        
        if ($this->form_validation->run() == true) {
            $pymnt = $this->sales_model->getPaymentByID($id);
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $inv = $this->sales_model->getInvoiceByID($this->input->post('sale_id'));
            $payment = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => 0,
                'paid_by' => $pymnt->paid_by,
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'type' => $this->input->post('status'),
                'created_by' => $inv->salesman_id
            );
            $payment1 = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $pymnt->paid_by,
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'type' => $this->input->post('status'),
                'created_by' => $inv->salesman_id
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

        } elseif ($this->input->post('void_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->sales_model->updatePayment($id, $payment)) {
            if($this->input->post('status')=="pending" && $pymnt->paid_by=="Cheque Payment" || $pymnt->paid_by=="Invoice Payment"){
                if($pymnt->paid_by=="Cheque Payment"){
                    $this->sales_model->removeChequePayment($inv->customer_id);
                }
                if($pymnt->paid_by=="Invoice Payment"){
                    $this->sales_model->removeInvoicePayment($inv->customer_id);
                }
                
            }
            
            $pmt = $this->sales_model->getPaymentByID($id);
            $inv = $this->sales_model->getInvoiceByID($pmt->sale_id);
            $all_pymnt = $this->sales_model->getPaymentsForSale($pmt->sale_id);
            $paid=0;
            foreach ($all_pymnt as $pyment){
                if($pyment->type=='received'){
                    $paid+=$pyment->amount;
                }
            }
            $bal = $inv->grand_total - $paid;
            $s_data=array('payment_status'=>'paid');
            if($bal==0){
                $this->db->update('sales', $s_data, array('id' => $inv->id));
            }
            
            if($this->input->post('status')=="received"){
                $data2 = array(
                'CustId' => $inv->customer_id,
                'TransactionRef' => $payment1['reference_no'],
                'TransDate' => $payment1['date'],
                'BankAcct' => 16,
                'Amount' => $payment1['amount']);
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
                    CURLOPT_URL => "https://powergaserp.techsavanna.technology/api/endpoints/payment.php?action=reverse-payment&company-id=KAMP",
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
                    $this->session->set_flashdata('message', lang("payment_voided"));
                    redirect("sales/view/".$id);
                } else {
                    $this->session->set_flashdata('error', lang("payment_not_voided.Check_ERP_Configuration"));
                    redirect("sales/view/".$id);
                }
            }else{
                $this->session->set_flashdata('message', lang("payment_voided"));
                    redirect("sales/view/".$id);
            }
            
            
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['payment'] = $this->sales_model->getPaymentByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'sales/void_payment', $this->data);
        }
    }
//end void payment
    function delete_payment($id = NULL)
    {
        $this->sma->checkPermissions('delete');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->sales_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* --------------------------------------------------------------------------------------------- */

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        $warehouse_id = $this->input->get('warehouse_id', TRUE);
        $customer_id = $this->input->get('customer_id', TRUE);

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
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $rows = $this->sales_model->getProductNames($sr, $warehouse_id);
        if ($rows) {
            foreach ($rows as $row) {
                $option = FALSE;
                $row->quantity = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty = 1;
                $row->discount = '0';
                $row->shippping = '0';
                $row->serial = '';
                $options = $this->sales_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $options[0];
                    if (!$option) {
                        $option = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->price = 0;
                }
                $row->option = $option;
                $pis = $this->sales_model->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if($pis){
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->sales_model->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if($pis){
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($opt->price != 0) {
                    $row->price = $opt->price + (($opt->price * $customer_group->percent) / 100);
                } else {
                    $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
                }
                $row->real_unit_price = $row->price;
                $combo_items = FALSE;
                if ($row->tax_rate) {
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    if ($row->type == 'combo') {
                        $combo_items = $this->sales_model->getProductComboItems($row->id, $warehouse_id);
                    }
                    $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'options' => $options);
                } else {
                    $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => false, 'options' => $options);
                }
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* ------------------------------------ Gift Cards ---------------------------------- */

    function gift_cards()
    {
        $this->sma->checkPermissions();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('gift_cards')));
        $meta = array('page_title' => lang('gift_cards'), 'bc' => $bc);
        $this->page_construct('sales/gift_cards', $meta, $this->data);
    }

    function getGiftCards()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('gift_cards') . ".id as id, card_no, value, balance, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as created_by, customer, expiry", FALSE)
            ->join('users', 'users.id=gift_cards.created_by', 'left')
            ->from("gift_cards")
            ->add_column("Actions", "<center><a href='" . site_url('sales/edit_gift_card/$1') . "' class='tip' title='" . lang("edit_gift_card") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_gift_card") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('sales/delete_gift_card/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></center>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function validate_gift_card($no)
    {
        //$this->sma->checkPermissions();
        if ($gc = $this->site->getGiftCardByNO($no)) {
            if ($gc->expiry) {
                if ($gc->expiry >= date('Y-m-d')) {
                    echo json_encode($gc);
                } else {
                    echo json_encode(false);
                }
            } else {
                echo json_encode($gc);
            }
        } else {
            echo json_encode(false);
        }
    }

    function add_gift_card()
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('card_no', lang("card_no"), 'trim|is_unique[gift_cards.card_no]|required');
        $this->form_validation->set_rules('value', lang("value"), 'required');

        if ($this->form_validation->run() == true) {
            $customer_details = $this->input->post('customer') ? $this->site->getCompanyByID($this->input->post('customer')) : NULL;
            $customer = $customer_details ? $customer_details->company : NULL;
            $data = array('card_no' => $this->input->post('card_no'),
                'value' => $this->input->post('value'),
                'customer_id' => $this->input->post('customer') ? $this->input->post('customer') : NULL,
                'customer' => $customer,
                'balance' => $this->input->post('value'),
                'expiry' => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : NULL,
                'created_by' => $this->session->userdata('user_id')
            );
            $sa_data = array();
            $ca_data = array();
            if ($this->input->post('staff_points')) {
                $sa_points = $this->input->post('sa_points');
                $user = $this->site->getUser($this->input->post('user'));
                if ($user->award_points < $sa_points) {
                    $this->session->set_flashdata('error', lang("award_points_wrong"));
                    redirect("sales/gift_cards");
                }
                $sa_data = array('user' => $user->id, 'points' => ($user->award_points - $sa_points));
            } elseif ($customer_details && $this->input->post('use_points')) {
                $ca_points = $this->input->post('ca_points');
                if ($customer_details->award_points < $ca_points) {
                    $this->session->set_flashdata('error', lang("award_points_wrong"));
                    redirect("sales/gift_cards");
                }
                $ca_data = array('customer' => $customer->id, 'points' => ($customer_details->award_points - $ca_points));
            }
        } elseif ($this->input->post('add_gift_card')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("sales/gift_cards");
        }

        if ($this->form_validation->run() == true && $this->sales_model->addGiftCard($data, $ca_data, $sa_data)) {
            $this->session->set_flashdata('message', lang("gift_card_added"));
            redirect("sales/gift_cards");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['users'] = $this->sales_model->getStaff();
            $this->data['page_title'] = lang("new_gift_card");
            $this->load->view($this->theme . 'sales/add_gift_card', $this->data);
        }
    }

    function edit_gift_card($id = NULL)
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('card_no', lang("card_no"), 'trim|required');
        $gc_details = $this->site->getGiftCardByID($id);
        if ($this->input->post('card_no') != $gc_details->card_no) {
            $this->form_validation->set_rules('card_no', lang("card_no"), 'is_unique[gift_cards.card_no]');
        }
        $this->form_validation->set_rules('value', lang("value"), 'required');
        //$this->form_validation->set_rules('customer', lang("customer"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $gift_card = $this->site->getGiftCardByID($id);
            $customer_details = $this->input->post('customer') ? $this->site->getCompanyByID($this->input->post('customer')) : NULL;
            $customer = $customer_details ? $customer_details->company : NULL;
            $data = array('card_no' => $this->input->post('card_no'),
                'value' => $this->input->post('value'),
                'customer_id' => $this->input->post('customer') ? $this->input->post('customer') : NULL,
                'customer' => $customer,
                'balance' => ($this->input->post('value') - $gift_card->value) + $gift_card->balance,
                'expiry' => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : NULL,
            );
        } elseif ($this->input->post('edit_gift_card')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("sales/gift_cards");
        }

        if ($this->form_validation->run() == true && $this->sales_model->updateGiftCard($id, $data)) {
            $this->session->set_flashdata('message', lang("gift_card_updated"));
            redirect("sales/gift_cards");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['gift_card'] = $this->site->getGiftCardByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/edit_gift_card', $this->data);
        }
    }

    function sell_gift_card()
    {
        $this->sma->checkPermissions('gift_cards', true);
        $error = NULL;
        $gcData = $this->input->get('gcdata');
        if (empty($gcData[0])) {
            $error = lang("value") . " " . lang("is_required");
        }
        if (empty($gcData[1])) {
            $error = lang("card_no") . " " . lang("is_required");
        }


        $customer_details = (!empty($gcData[2])) ? $this->site->getCompanyByID($gcData[2]) : NULL;
        $customer = $customer_details ? $customer_details->company : NULL;
        $data = array('card_no' => $gcData[0],
            'value' => $gcData[1],
            'customer_id' => (!empty($gcData[2])) ? $gcData[2] : NULL,
            'customer' => $customer,
            'balance' => $gcData[1],
            'expiry' => (!empty($gcData[3])) ? $this->sma->fsd($gcData[3]) : NULL,
            'created_by' => $this->session->userdata('username')
        );

        if (!$error) {
            if ($this->sales_model->addGiftCard($data)) {
                echo json_encode(array('result' => 'success', 'message' => lang("gift_card_added")));
            }
        } else {
            echo json_encode(array('result' => 'failed', 'message' => $error));
        }

    }

    function delete_gift_card($id = NULL)
    {
        $this->sma->checkPermissions();

        if ($this->sales_model->deleteGiftCard($id)) {
            echo lang("gift_card_deleted");
        }
    }

    function gift_card_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->sales_model->deleteGiftCard($id);
                    }
                    $this->session->set_flashdata('message', lang("gift_cards_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('gift_cards'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('card_no'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('value'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('customer'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->site->getGiftCardByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->card_no);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->value);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->customer);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'gift_cards_' . date('Y_m_d_H_i_s');
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
                $this->session->set_flashdata('error', lang("no_gift_card_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function get_award_points($id = NULL)
    {
        $this->sma->checkPermissions('index');

        $row = $this->site->getUser($id);
        echo json_encode(array('sa_points' => $row->award_points));
    }

    /* -------------------------------------------------------------------------------------- */

    function sale_by_csv()
    {
		
       $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('smonth', lang("smonth"), 'required');
$errorlog="";
        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
         
            $warehouse_id = $this->input->post('warehouse');
            $alignment_id = $this->input->post('alignment');
            $customer_id = $this->input->post('customer');
			$sales_type = $this->input->post('type');
			$distributor = $this->input->post('distributor');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');
            $sale_status = $this->input->post('sale_status');
            $payment_status = $this->input->post('payment_status');
            $prce_type = $this->input->post('prce_type');
			$syear = $this->input->post('smonth');
			//$sdate2 = $this->input->post('smonth');
			str_replace(',','',$csv_pr['discount']);
			$smonth = $this->sma->fld('01/'.$sdate);
            $payment_term = $this->input->post('payment_term');
         
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->company ? $customer_details->company : $customer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
				
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
$rw =2;
$sessionid=$sessionid=session_id().rand(1000,10000);
            if (isset($_FILES["userfile"])) {
				
                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("sales/sale_by_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
				
                $titles = array_shift($arrResult);
                
                if($this->input->post('actual_values')){
                    $errorlogg="";
                    if(count($arrResult)<2){
                        $errorlogg.="File is empty";
                    }
                    if(strlen($arrResult[0][0]) !=10){
                        $errorlogg.="Check date format"; 
                    }  //check the first date in the file
                  
                    
                    
                    
                                  if($errorlogg !=""){
    $this->settings_model->logErrors($errorlogg);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
} else{
    //import
   // die(print_r($arrResult));   
    $count=0;
    $quantityunits=0;
    $valuetotal=0;
     foreach ($arrResult as $row) {
               
              
          
            $msr_details = $this->sales_model->msr_customer_alignments($row[9],$row[11],$row[5]);
         $msrid=$msr_details->sf_alignment_id;
           $msrname=$msr_details->sf_alignment_name;
           $saledata=array('date' =>$row[0],'gmid'=>$row[1],'sales_type'=>$row[2],'month'=>$row[3],'country'=>$row[4],'country_id'=>$row[5],'distributor'=>$row[6],'distributor_id'=>$row[7],'customer'=>$row[8],'customer_id'=>$row[9],'products'=>$row[10],'product_id'=>$row[11],'quantity_units'=>$row[12],'price_type'=>$row[13],'value'=>$row[14],'source'=>$row[15],'tender_price'=>$row[17],'grand_total'=>$row[18],'total_discount'=>$row[19],'shipping'=>$row[18],'total'=>$row[18],'brand_id'=>$row[22],'brand'=>$row[23],'gbu'=>$row[24],'promotion'=>$row[25],'movement_code'=>$row[29],'created_by'=>$this->session->userdata('user_id'),'msr_alignment_id'=>$msrid,'msr_alignment_name'=>$msrname,'created'=>date("Y-m-d H:i"),'updated_sso'=>1,'session_id'=>$sessionid);
           $quantityunits+=$saledata['quantity_units'];
           $valuetotal+=$saledata['value'];
           $this->db->insert('sales', $saledata);
             
              $sale_id = $this->db->insert_id();
            $this->db->insert('sale_items', array('sale_id' =>$sale_id,'product_id'=>$saledata['product_id'],'product_code'=>$saledata['gmid'],'product_name'=>$saledata['products'],'product_type'=>'standard','quantity'=>$saledata['quantity_units'],'subtotal'=>$saledata['grand_total'],'country_id'=>$saledata['country_id'],'session_id'=>$sessionid));
           if(strtoupper($saledata['movement_code'])=="VE"){
            $this->db->insert('consolidated_sales_sso', array('upload_type'=>'SALE','country'=>$saledata['country'],'monthyear'=>$saledata['date'],'customer_sanofi'=>$saledata['customer'],'customer_id'=>$saledata['customer_id'],'distributor'=>$saledata['distributor'],'distributor_id'=>$saledata['distributor_id'],'promotion'=>$saledata['promotion'],'brand'=>$saledata['brand'],'brand_id'=>$saledata['brand_id'],'bu'=>$saledata['gbu'],'gross_qty'=>$saledata['quantity_units'],'gross_sale'=>$saledata['grand_total'],'sale_id' =>$sale_id,'product_id'=>$saledata['product_id'],'gmid'=>$saledata['gmid'],'product_name'=>$saledata['products'],'movement_code'=>$saledata['movement_code'],'country_id'=>$saledata['country_id'],'msr_id' =>$msrid,'msr_name'=>$msrname,'session_id'=>$sessionid));
           }
           else if(strtoupper($saledata['movement_code'])=="TN"){
           $this->db->insert('consolidated_sales_sso', array('upload_type'=>'SALE','country'=>$saledata['country'],'monthyear'=>$saledata['date'],'customer_sanofi'=>$saledata['customer'],'customer_id'=>$saledata['customer_id'],'distributor'=>$saledata['distributor'],'distributor_id'=>$saledata['distributor_id'],'promotion'=>$saledata['promotion'],'brand'=>$saledata['brand'],'brand_id'=>$saledata['brand_id'],'bu'=>$saledata['gbu'],'tender_qty'=>$saledata['quantity_units'],'tender_sale'=>$saledata['grand_total'],'sale_id' =>$sale_id,'product_id'=>$saledata['product_id'],'gmid'=>$saledata['gmid'],'product_name'=>$saledata['products'],'movement_code'=>$saledata['movement_code'],'country_id'=>$saledata['country_id'],'msr_id' =>$msrid,'msr_name'=>$msrname,'session_id'=>$sessionid));    
           }
           else if(strtoupper($saledata['movement_code'])=="NT"){
               $this->db->insert('consolidated_sales_sso', array('upload_type'=>'SALE','country'=>$saledata['country'],'monthyear'=>$saledata['date'],'customer_sanofi'=>$saledata['customer'],'customer_id'=>$saledata['customer_id'],'distributor'=>$saledata['distributor'],'distributor_id'=>$saledata['distributor_id'],'promotion'=>$saledata['promotion'],'brand'=>$saledata['brand'],'brand_id'=>$saledata['brand_id'],'bu'=>$saledata['gbu'],'net_qty'=>$saledata['quantity_units'],'net_sale'=>$saledata['grand_total'],'sale_id' =>$sale_id,'product_id'=>$saledata['product_id'],'gmid'=>$saledata['gmid'],'product_name'=>$saledata['products'],'movement_code'=>$saledata['movement_code'],'country_id'=>$saledata['country_id'],'msr_id' =>$msrid,'msr_name'=>$msrname,'session_id'=>$sessionid));    
           }
          $count++; 
           
           // $this->db->update('sales', array('updated_sso' =>1), array('id' => $row->id)); 
            }
            $uploadarray=array('upload_type' =>"SALE", 'file_name' =>$csv, 'record_count' =>$count, 'quantity' =>$quantityunits,'value'=>$valuetotal,'date_created'=>date('Y-m-d H:i:s'),'created_by'=>$this->session->userdata('user_id'),'created_by_name'=>$this->session->userdata('username'),'session_id'=>$sessionid,'date_updated'=>date('Y-m-d H:i:s'));
            $this->site->recordUpload($uploadarray);
    $this->session->set_flashdata('message', $this->lang->line("sale_added_with_count_of_".$uploadarray['record_count']."_quantity_".$uploadarray['quantity']."_and_value_".$uploadarray['value']."<a href='sales/approvals' target='_blank'>view more</a>"));
            redirect("reports/sales");
}
                }
                
                
                $biggerarray=array();
                
				 if($sales_type=='PSO'){
		
				  foreach ($arrResult as $key => $value) {
				      if (strtolower($value) !="month"){
				
                    $final["month"]=$value[0];
                    $final["country"]=$value[1];
                    $final["distributor"]=$value[2];
                    $final["gmid"]=$value[3];
                    $final["product"]=$value[4];
                    $final["quantity_units"]=$value[5];
                    $final["value"]=$value[6];
                    $final["source"]=$value[7];
               
                    array_push($biggerarray,$final);
				      }
		
                }
                
                //print_r($arrResult);
               // die();
         
				}
				
            elseif ($sales_type=='SSO'){
            
				// $keys = array('month','country','distributor','customer','product','quantity_units');
				
				 foreach ($arrResult as $key => $value) {
                  
                    
                    $final["month"]=$value[0];
                    $final["country"]=$value[1];
                    $final["distributor"]=$value[2];
                    $final["customer"]=$value[3];
                    $final["product"]=$value[4];
                    $final["quantity_units"]=$value[5];
                    $final["value"]=$value[6];
                  
                  
                    array_push($biggerarray,$final);
  
                }
	
				} 
	
                    // print_r($biggerarray);
			//	die();           
        	if($sales_type=='PSO' ){
     
		   foreach ($biggerarray as $csv_pr) {
                    
                    if (isset($csv_pr)){
                        $sdate = $syear.'-'.$csv_pr['month'].'-'.'01';
                        $sdate2 = $csv_pr['month'].'/'.$syear;
								$country_details = $this->sales_model->getCountryByCode($csv_pr['country']);
								
								 if($csv_pr['source'] =='MARCO'){ //IF MARCO GET FROM MARCO gmid
								$product_details = $this->sales_model->getProductByMarcafaCode($csv_pr['gmid']);
                                                                //$getprices = $this->sales_model->getProductPrices($product_details->code,$csv_pr['country'],$sdate2);
								$item_net_price =$csv_pr['value']/$csv_pr['quantity_units']; //$this->sma->formatDecimal($getprices->supply_price);
								$item_unified_price = 0;
								 }else{
                                                                    // $getprices = $this->sales_model->getProductPrices($csv_pr['gmid'],$csv_pr['country'],$sdate2);
								 $product_details = $this->sales_model->getProductByCode($csv_pr['gmid']); 
								 	$item_net_price = $csv_pr['value']/$csv_pr['quantity_units'];//$this->sma->formatDecimal($getprices->resell_price);
								 }
								$distributor_details = $this->sales_model->getDistributorByName($csv_pr['distributor'],$country_details->id);
							
								$customer_details = $this->sales_model->getCustomerByName($csv_pr['customer'],$country_details->id);
                                $item_id = $product_details->id;
                                $item_type = $product_details->type;
                                $item_code = $product_details->code;
                                $item_name = $product_details->name;
                                
                                $item_quantity = $csv_pr['quantity_units'];
								$sale_country = $csv_pr['country'];
								$date = $smonth;
								

                                    $product_details = $this->sales_model->getProductByCode($item_code);

                                        $pr_discount = 0;
                                  
                                    $item_net_price = $this->sma->formatDecimal($item_net_price - $pr_discount);
				$unified_price = $this->sma->formatDecimal($item_unified_price - $pr_discount);
                                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                                    $product_discount += $pr_item_discount;

                                   
                                        $item_tax = 0;
                                        $pr_tax = 0;
                                        $pr_item_tax = 0;
                                        $tax = "";
                                    
                                    $product_tax += $pr_item_tax;

                                    $subtotal = $csv_pr['value'];//($item_net_price * $item_quantity);
                                    
                                   $products[] = array(
                                        'product_id' => $item_id,
                                        'product_code' => $item_code,
                                        'product_name' => $item_name,
                                        'product_type' => $item_type,
                                        'option_id' => $item_option->id,
                                        'net_unit_price' => $this->sma->formatDecimal($item_net_price),
                                        'unit_price' => $this->sma->formatDecimal($subtotal),
                                        'quantity' => $item_quantity,
                                        'warehouse_id' => $warehouse_id,
                                        'item_tax' => $pr_item_tax,
                                        'tax_rate_id' => $pr_tax,
                                        'tax' => $tax,
                                        'discount' => $item_discount,
                                        'item_discount' => $item_discount,
                                        'subtotal' => $this->sma->formatDecimal($subtotal + $item_tax),
                                        'serial_no' => $item_serial,
                                        'unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax + $pr_discount),
					'session_id'=>$sessionid				
                                    );
								
                                   $brand=$this->products_model->getCategoryById($product_details->category_id);
		$data[] = array('month' => $csv_pr['month'],
		            'date'=>$sdate,
                'sales_type'=> 'PSO',
                'shipping'=> $subtotal,
                'product_id' => $item_id,
                'country' => $csv_pr['country'],
                'country_id' => $country_details->id,
                'distributor' =>$distributor_details->name,
                'distributor_id' =>$distributor_details->id,
                'gmid' =>$csv_pr['gmid'],
                     "brand_id"=>$brand->id,
       "brand"=>$brand->name,
        "gbu"=>$brand->gbu,
       "promotion"=>$getprices->promotion,
                'movement_code' =>$prce_type,
                'products' =>$item_name,
                'quantity_units' =>$csv_pr['quantity_units'],
                'value' =>$subtotal,
                'source' =>$csv_pr['source'],
                'grand_total' =>$subtotal,
                'total' =>$subtotal,
                    'session_id'=>$sessionid
            );
//print_r($data);
//die();

            
           $total += $item_net_price * $item_quantity;
                              //  }
							
                        } else {
                         $errorlog.=  $this->lang->line("Mercafa_GMID_Not_Found :") . " " . $csv_pr['mercafa_gmid'] . " : " . $this->lang->line("Row_number") . " " . $rw."\n";
                          //  $this->session->set_flashdata('error', $this->lang->line("Mercafa_GMID_Not_Found") . " ( " . $csv_pr['mercafa_gmid'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                           // redirect($_SERVER["HTTP_REFERER"]);
                        }
				
						if ($country_details = $this->sales_model->getCountryByCode($csv_pr['country'])) {
						}else{
							//$this->session->set_flashdata('error', $this->lang->line("Country_Code_Not_Found") . " ( " . $csv_pr['country'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
                          //  redirect($_SERVER["HTTP_REFERER"]);
                            $errorlog.=$this->lang->line("Country_Code_Not_Found :") . " " . $csv_pr['country'] . " : " . $this->lang->line("Row_number") . " " . $rw."\n";
						}
						
						if($csv_pr['source'] =='MARCO'){ //IF MARCO GET FROM MARCO gmid
								$product_details = $this->sales_model->getProductByMarcafaCode($csv_pr['gmid']);
								 }else{
								 $product_details = $this->sales_model->getProductByCode($csv_pr['gmid']);    
								 }
							if ($product_details) {
						}else{
							//$this->session->set_flashdata('error', $this->lang->line("Product gmid does not exist") . " ( " . $csv_pr['gmid'] . " -".$csv_pr['source']." ). " . $this->lang->line("Row_number") . " " . $rw);
                            //redirect($_SERVER["HTTP_REFERER"]);
                            $errorlog.=$this->lang->line("Product gmid does not exist :") . " " . $csv_pr['gmid'] . " :".$csv_pr['product']." : " . $this->lang->line("Row_number") . " " . $rw."\n";
						}
						
						
						
						
				// 		if ($country_details = $this->sales_model->getDistributorByName($csv_pr['distributor'])) {
				// 		}else{
				// 			$this->session->set_flashdata('error', $this->lang->line("Distributor_Not_Found") . " ( " . $csv_pr['distributor'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
    //                         redirect($_SERVER["HTTP_REFERER"]);
				// 		}
				 		//if ($getprices = $this->sales_model->getProductPrices($product_details->code,$csv_pr['country'],$sdate2)) {
				 		//}else{
				 			//$this->session->set_flashdata('error', $this->lang->line("Country_Pricing_Not_Found") . " ( " . $csv_pr['mercafa_gmid'].".".$csv_pr['country']." ). " . $this->lang->line("Row_number") . " " . $rw);
                            // redirect($_SERVER["HTTP_REFERER"]);
			 	//$errorlog.=$this->lang->line("Country_Pricing_Not_Found :") . " " . $csv_pr['gmid'].": ".$csv_pr['product']." :".$csv_pr['country'].": " . $this->lang->line("Row_number") . " " . $rw."\n";	
                             
                                            //    }
                        $rw++;
                   
                  //  {
				//		$this->session->set_flashdata('error', $this->lang->line("Compulsory Fields not yet filled") . $this->lang->line("Row_number") . " " . $rw);
    //                         redirect($_SERVER["HTTP_REFERER"]);
						//}

                }
			
			                       foreach($biggerarray as $yourValues){
    $dates[] = $syear.'-'.$yourValues['month'];
}
$yourUniquedates = array_unique($dates); //GET UNIQUE COUNTRYS


                foreach($yourUniquedates as $Values){
       $this->sales_model->remove_data($sales_type,$Values,$prce_type,$country_details->id);
    //REMOVE DATA THAT HAD BEEN UPLOADED
}	
			}

            elseif($sales_type=='SSO'){
                
	foreach ($biggerarray as $csv_pr) {
					
	 //echo $csv_pr['month'];
	 
      $product_description = str_replace(array("'","â‚¬","Â£"),"",utf8_decode($csv_pr['product']));	
      // $product_description = str_replace(chr(0xE2).chr(0x82).chr(0xAC),"EUR",$csv_pr['product']);
     // $product_description = str_replace('?',"EUR",utf8_decode($csv_pr['product']));
      
       //die($product_description);
                  //  if (isset($product_description) && isset($csv_pr['month']) && isset($csv_pr['country']) && isset($csv_pr['distributor'])) {
$country_details = $this->sales_model->getCountryByCode($csv_pr['country']);
 $sdate = $syear.'-'.$csv_pr['month'].'-'.'01';
                        $sdate2 = $csv_pr['month'].'/'.$syear;
  if($country_details->code == 'EURO'){
    $conversionrate = 1;
}else{
  $conver = $this->sales_model->getConversionByMonth($country_details->code,$sdate);
  if(!$conver){
   $errorlog.=$this->lang->line("Conversion_rate_Not_Found_For :") . " $country_details->code : ".$sdate." : " . $this->lang->line("Row_number") . " " . $rw."\n";
  }
  $conversionrate = $conver->conversion_rate;
}                      
if(!$country_details){
	//$this->session->set_flashdata('error', $this->lang->line("Country with name :".$csv_pr['country']." not found") . $this->lang->line("Row_number") . " " . $rw);
                            //redirect($_SERVER["HTTP_REFERER"]);
    $errorlog.=$this->lang->line("Country with name :".$csv_pr['country'].": not found") . $this->lang->line("Row_number") . " " . $rw."\n";
} 

$product_details2 = $this->sales_model->getProductByDescription($product_description,$country_details->id,str_replace(array("'", "â‚¬"," "),"",$csv_pr['distributor']));

                        if ($product_details = $this->sales_model->getProductByDescription($product_description,$country_details->id,str_replace(array("'", "â‚¬"," "),"",$csv_pr['distributor']))) {


								$distributor_details = $this->sales_model->getDistributorByName($csv_pr['distributor'],$country_details->id);
							
								$SSOcustomer_details = $this->sales_model->getSSOCustomerdistnaming($distributor_details->id,$csv_pr['customer'],$country_details->id);
			if($csv_pr['customer']==''){
			    $SSOcustomer_details->customer_id = 0;
			    $SSOcustomer_details->sanofi_naming ='';
			}
			else{
			if(!$SSOcustomer_details){
//	$this->session->set_flashdata('error', $this->lang->line("Customer Mapping :".$csv_pr['customer']." - ".$csv_pr['distributor']." not found") . $this->lang->line("Row_number") . " " . $rw);
//                            redirect($_SERVER["HTTP_REFERER"]);
                            $errorlog.=$this->lang->line("Customer Mapping :".$csv_pr['customer']." : ".$csv_pr['distributor'].": not found") . $this->lang->line("Row_number") . " " . $rw."\n";
                            
}	}
				$msr_details = $this->sales_model->msr_customer_alignments($SSOcustomer_details->customer_id,$product_details->id,$country_details->id);
		//	if(!$msr_details){
//	$this->session->set_flashdata('error', $this->lang->line("Customer MSR Mapping :".$csv_pr['customer']." - ".$csv_pr['product']." not found") . $this->lang->line("Row_number") . " " . $rw);
 //                           redirect($_SERVER["HTTP_REFERER"]);
//}
								// $clustername = $this->sales_model->getClusternameByCode($country_details->cluster);

								
                                $item_id = $product_details->id;
                                $item_type = $product_details->type;
                                $item_code = $product_details->code;
                                $item_name = $product_details->name;
                               // print_r($product_details);
//die();
// check special


if($this->sales_model->getSpecialProductPrices($prce_type,$SSOcustomer_details->customer_id,$distributor_details->id,$item_code,$csv_pr['country'],$sdate2)){
    $getprices = $this->sales_model->getSpecialProductPrices($prce_type,$SSOcustomer_details->customer_id,$distributor_details->id,$item_code,$csv_pr['country'],$sdate2);
							 //print_r($getprices);
							 //die();
								//if($getprices->resell_price != '0'){
								    $item_net_price = $this->sma->formatDecimal($getprices->special_resell_price);
								//}else {
								   //$item_net_price = $this->sma->formatDecimal($getprices->supply_price); 
								//}
                                
                                $item_quantity = $csv_pr['quantity_units'];
								$sale_country = $csv_pr['country'];
								$date = $smonth;
								//$item_discount = str_replace(',','',$csv_pr['discount']);
								$item_discount = '0';
                             
                                    $product_details = $this->sales_model->getProductByCode($item_code);
									//	print_r($getprices);
								//die();
                                    
                                        $pr_discount = 0;
                                  
                                    $item_net_price = $this->sma->formatDecimal($item_net_price - $pr_discount);
                                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                                    $product_discount += $pr_item_discount;

                                   
                                        $item_tax = 0;
                                        $pr_tax = 0;
                                        $pr_item_tax = 0;
                                        $tax = "";
                                    
                                    $product_tax += $pr_item_tax;

                                    
                                    if($prce_type =='TN'){ 
                                    $tender_price =$csv_pr['value'];// ($getprices->special_tender_price * $item_quantity);
                                 
                                    $subtotal = $tender_price;
									
									}else {
									$tender_price = 0;
									$subtotal = ($item_net_price * $item_quantity);
									}
									//close check special
}
else{
if ($getprices = $this->sales_model->getProductPrices($item_code,$csv_pr['country'],$sdate2)) {
						}else{
				 			//$this->session->set_flashdata('error', $this->lang->line("Country_Pricing_Not_Found_For:") . " ( " . $csv_pr['product']." - ".$item_code."- ".$csv_pr['country']." - ".$sdate2." ). " . $this->lang->line("Row_number") . " " . $rw);
                           //  redirect($_SERVER["HTTP_REFERER"]);
                             $errorlog.=$this->lang->line("Country_Pricing_Not_Found_For :") . " " . $csv_pr['product']." : ".$item_code.": ".$csv_pr['country']." : ".$sdate2." : " . $this->lang->line("Row_number") . " " . $rw."\n";
				 		}
								$getprices = $this->sales_model->getProductPrices($item_code,$csv_pr['country'],$sdate2);
								//if($getprices->resell_price != '0'){
								    $item_net_price = $this->sma->formatDecimal($getprices->resell_price);
								//}else {
								   //$item_net_price = $this->sma->formatDecimal($getprices->supply_price); 
								//}
                                
                                $item_quantity = $csv_pr['quantity_units'];
								$sale_country = $csv_pr['country'];
								$date = $smonth;
								//$item_discount = str_replace(',','',$csv_pr['discount']);
								$item_discount = '0';
                             
                                    $product_details = $this->sales_model->getProductByCode($item_code);
									//	print_r($getprices);
								//die();
                                    
                                        $pr_discount = 0;
                                  
                                    $item_net_price = $this->sma->formatDecimal($item_net_price - $pr_discount);
                                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_quantity);
                                    $product_discount += $pr_item_discount;

                                   
                                        $item_tax = 0;
                                        $pr_tax = 0;
                                        $pr_item_tax = 0;
                                        $tax = "";
                                    
                                    $product_tax += $pr_item_tax;

                                    
                                    if($prce_type =='TN'){ 
                                    $tender_price =$csv_pr['value'];// ($getprices->tender_price * $item_quantity);
                                   // if ($tender_price == 0) {
//                                        	$this->session->set_flashdata('error', $this->lang->line("Tender_Price_Not_Found") . "  " . " ( " . $csv_pr['product'] . $this->lang->line("Row_number") . " " . $rw);
//                            redirect($_SERVER["HTTP_REFERER"]);
                                       // $errorlog.=$this->lang->line("Tender_Price_Not_Found :") . "  " . " " . $csv_pr['product'] . " :" . $this->lang->line("Row_number") . " " . $rw."\n";
                                        
					//	}else{
						
					//	}
                                    $subtotal = $tender_price;
									
									}else {
									$tender_price = 0;
									$subtotal = ($item_net_price * $item_quantity);
									}
// check special
    
}

                                    $products[] = array(
                                        'product_id' => $item_id,
                                        'product_code' => $item_code,
                                        'product_name' => $item_name,
                                        'product_type' => $item_type,
                                        'option_id' => $item_option->id,
                                        'net_unit_price' => $this->sma->formatDecimal($item_net_price),
                                        'unit_price' => $this->sma->formatDecimal($subtotal),
                                        'quantity' => $item_quantity,
                                        'warehouse_id' => $warehouse_id,
                                        'item_tax' => $pr_item_tax,
                                        'tax_rate_id' => $pr_tax,
                                        'tax' => $tax,
                                        'discount' => $item_discount,
                                        'item_discount' => $item_discount,
                                        'subtotal' => $this->sma->formatDecimal($subtotal + $item_tax),
                                        'serial_no' => $item_serial,
                                        'unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax + $pr_discount),
                                        'session_id'=>$sessionid
										//'country_id'
                                    );
		$brand=$this->products_model->getCategoryById($product_details->category_id);
      
                
                $data[] = array('month' => $csv_pr['month'],
		            'date'=>$sdate,
                'sales_type'=> 'SSO',
                'shipping'=>$subtotal,
                'product_id' => $item_id,
                'country' => $csv_pr['country'],
                'country_id' => $country_details->id,
                'distributor' =>$distributor_details->name,
                'distributor_id' =>$distributor_details->id,
                'gmid' =>$item_code,
                'products' =>$item_name,
                'quantity_units' =>$csv_pr['quantity_units'],
                'value' =>$subtotal*$conversionrate,
                'movement_code' =>$prce_type,
                  "brand_id"=>$brand->id,
       "brand"=>$brand->name,
        "gbu"=>$brand->gbu,
       "promotion"=>$getprices->promotion,
                'customer' =>$SSOcustomer_details->sanofi_naming,
                'customer_id'=> $SSOcustomer_details->customer_id,
                'grand_total' =>$subtotal*$conversionrate,
                'total' =>$subtotal*$conversionrate,
                'tender_price' => $tender_price*$conversionrate,
                'msr_alignment_id' => $msr_details->sf_alignment_id,
                'msr_alignment_name' =>$msr_details->sf_alignment_name,
                 'session_id'=>$sessionid
            );

                                    $total += $item_net_price * $item_quantity;
                              //  }

                        } else {
                            //$this->session->set_flashdata('error', $this->lang->line("Product mapping does not exist for ") . " ( " . $csv_pr['product'] . " - " . $csv_pr['distributor'] . " - ".$csv_pr['country']."  ). " . $this->lang->line("Row_number") . " " . $rw);
                            //redirect($_SERVER["HTTP_REFERER"]);
                            $errorlog.=$this->lang->line("Product mapping does not exist for : ") . " " . $csv_pr['product'] . " : " . $csv_pr['distributor'] . " : ".$csv_pr['country']." : " . $this->lang->line("Row_number") . " " . $rw."\n";
                        }
					    
						if ($country_details = $this->sales_model->getCountryByCode($csv_pr['country'])) {
						}else{
//							$this->session->set_flashdata('error', $this->lang->line("Country_Code_Not_Found") . " ( " . $csv_pr['country'] . " ). " . $this->lang->line("Row_number") . " " . $rw);
//                            redirect($_SERVER["HTTP_REFERER"]);
                                                    $errorlog.=$this->lang->line("Country_Code_Not_Found :") . "  " . $csv_pr['country'] . ":  " . $this->lang->line("Row_number") . " " . $rw."\n";
						}
				// 		if ($country_details = $this->sales_model->getCustomerByName($csv_pr['distributor'],$country_details->id)) {
				// 		}else{
				// 			$this->session->set_flashdata('error', $this->lang->line("Distributor_Not_Found") . " ( " . $csv_pr['distributor'] . $country_details->portuguese_name. " ). " . $this->lang->line("Row_number") . " " . $rw);
    //                         redirect($_SERVER["HTTP_REFERER"]);
				// 		}	
			//print_r($product_details2);
         // die();
				 		
                        $rw++;
                        
                    //}
    //                 }else{
                    //$errorlog.=$this->lang->line("Compulsory Fields not yet filled") . $this->lang->line("Row_number") . " " . $rw;
				// 			$this->session->set_flashdata('error', $this->lang->line("Compulsory Fields not yet filled") . $this->lang->line("Row_number") . " " . $rw);
    //                         redirect($_SERVER["HTTP_REFERER"]);
				// 		}

                }
                 foreach($biggerarray as $yourValues){
    $dates[] = $syear.'-'.$yourValues['month'];
}
$yourUniquedates = array_unique($dates); //GET UNIQUE COUNTRYS


                foreach($yourUniquedates as $Values){
       $this->sales_model->remove_data($sales_type,$Values,$prce_type,$country_details->id);
    //REMOVE DATA THAT HAD BEEN UPLOADED
}	


			}else{
				$this->session->set_flashdata('error', $this->lang->line("Select_Sales_Type") . " (  )." . $rw);
                            redirect($_SERVER["HTTP_REFERER"]); 
			}
                      
                        if($errorlog !=""){
    $this->settings_model->logErrors($errorlog);
  $this->session->set_flashdata('error', $this->lang->line("There_were_some_errors_during_upload_check_<a href='./assets/logs/uploaderror.txt' target='_blank'>error_log_file</a>"));
                            redirect($_SERVER["HTTP_REFERER"]);   
}
			
			}

          
                $order_discount_id = NULL;
           
            $total_discount = $this->sma->formatDecimal($order_discount + $product_discount);

       
                $order_tax_id = NULL;
          

            $total_tax = $this->sma->formatDecimal($product_tax + $order_tax);
            $grand_total = $this->sma->formatDecimal($this->sma->formatDecimal($total) + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount);
    
                $payment = array();
          
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

            //$this->sma->print_arrays($data, $products, $payment);
			
        }

if($this->sales_model->remove_data($sales_type,$smonth,$prce_type,$country_details->id)){
        if ($this->form_validation->run() == true && $this->sales_model->addSale_bycsv($data, $products, $payment)) {
            $this->session->set_userdata('remove_slls', 1);
            
            //function to record the data
            
            $uploadarray=array('upload_type' =>"SALE", 'file_name' =>$csv, 'record_count' =>  count($products), 'quantity' =>array_sum(array_column($data, 'quantity_units')),'value'=>array_sum(array_column($data, 'total')),'date_created'=>date('Y-m-d H:i:s'),'created_by'=>$this->session->userdata('user_id'),'created_by_name'=>$this->session->userdata('username'),'session_id'=>$sessionid,'date_updated'=>date('Y-m-d H:i:s'));
            $this->site->recordUpload($uploadarray);
            
         $this->session->set_flashdata('message', $this->lang->line("sale_added_with_count_of_".$uploadarray['record_count']."_quantity_".$uploadarray['quantity']."_and_value_".$uploadarray['value']."<a href='sales/approvals' target='_blank'>view more</a>"));   
            
            redirect("reports/sales");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$countries=$this->site->getAllCurrencies();

foreach($countries as $clust){
    $datas[$clust->id]=$clust->country;
}
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['slnumber'] = $this->site->getReference('so');
			$this->data['countries']=$datas;

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports/sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale_by_csv')));
            $meta = array('page_title' => lang('add_sale_by_csv'), 'bc' => $bc);
            $this->page_construct('sales/sale_by_csv', $meta, $this->data);

        }
    }
	}
        
        
        
        function import_actuals(){
           	
       $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('smonth', lang("smonth"), 'required');
$errorlog="";
        if ($this->form_validation->run() == true) {
            $rw =2;

            if (isset($_FILES["userfile"])) {
				
                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = '*';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("sales/sale_by_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
				
                $titles = array_shift($arrResult);
            
            
            
        }
        }
        
        }
        
   
          function approvals($type)
    {
        $this->site->checkModulePermission('sales-index');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
      

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('approvals')));
        $meta = array('page_title' => lang('approvals'), 'bc' => $bc);
        $this->page_construct('sales/approvals', $meta, $this->data);
    }
    
     function getApprovals()
    {
         $this->site->checkModulePermission('sales-index');

       
       // $detail_link = anchor('products/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('product_details'));
        $approvelink = "<a href='#' class='tip po' title='<b>" . $this->lang->line("approval") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-success po-delete1' id='a__$1' href='" . site_url('sales/approvedata?approval=approve&id=$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i> "
            . lang('approve_data') . "</a>";
        
        $rejectlink = "<a href='#' class='tip po' title='<b>" . $this->lang->line("reject") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('sales/approvedata?approval=reject&id=$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-warning\"></i> "
            . lang('reject_data') . "</a>";
         if ($this->Owner || $this->Admin) {
        $deletelink = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_primary_data") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('sales/approvedata?approval=delete&id=$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_primary_data') . "</a>";
         }
         else{
            $deletelink =""; 
         }
       // $single_barcode = anchor_popup('products/single_barcode/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_barcode'), $this->popup_attributes);
        //$single_label = anchor_popup('products/single_label/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_label'), $this->popup_attributes);
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">';
			
			
			
        $action .= '<li class="divider"></li>
				<li>' . $approvelink . '</li>
                                    <li>' . $rejectlink . '</li>
                                        <li>' . $deletelink . '</li>
                                      
			</ul>
		</div></div>';
        $this->load->library('datatables');
       
            $this->datatables
               ->select($this->db->dbprefix('upload_approvals') . ".id as id,date_created,upload_type,CONCAT('<a href=\'sales/filedata?id=',".$this->db->dbprefix('upload_approvals') . ".id,'\'> ', file_name, '</a>' ) as filename,record_count,created_by_name,session_id,approved,CONCAT(us.first_name,' ',us.last_name) as approver,date_updated ", FALSE)
                    ->join('users us', 'upload_approvals.approvals=us.id', 'left')
               ->where('DATE_FORMAT(date_created,"%Y")',date("Y"))
                   
                ->from('upload_approvals');
                
       
        $this->datatables->add_column("Actions", $action,"id");
        echo $this->datatables->generate();
    }
    
    
    function approvedata(){
        $this->site->checkModulePermission('sales-edit');
        $id=$this->input->get("id");
      //  die($id."dsds");
         $approval=$this->input->get("approval");
        $approvaldetails=  $this->getApproval($id);
        $user = $this->site->getUser();
        
        
        
        if($user->id==$approvaldetails->created_by && $approval=="approve"){
           // $approvaldetails->created_by;
            
           echo "<span style='color:red'>Uploader cannot authorise the request!</span>";
           exit(); 
           
        }
           else{
            echo 'Processing...<br>';
            switch (strtoupper($approvaldetails->upload_type)) {
                case "SALE":
                    if($approval=="approve"){
                        $this->appendApprovalAndData($id, $approvaldetails->session_id,"SALE");  
                    }
                    else if($approval=="reject") {
                    $this->deleteApprovalAndData($id, $approvaldetails->session_id,"SALE");      
                    }
                     else if($approval=="delete") {
                         //delete underlying data and approval
                    $this->truncateApprovalAndData($id, $approvaldetails->session_id,"SALE");      
                    }
                    break;
                
                case "BUDGET":
if($approval=="approve"){
   // die($id.$approvaldetails->session_id);
                        $this->appendApprovalAndData($id, $approvaldetails->session_id,"BUDGET");  
                    }
                    else if($approval=="reject") {
                    $this->deleteApprovalAndData($id, $approvaldetails->session_id,"BUDGET");      
                    }
                    else if($approval=="delete") {
                    $this->truncateApprovalAndData($id, $approvaldetails->session_id,"BUDGET");      
                    }

                    break;

                case "STOCK":
if($approval=="approve"){
                        $this->appendApprovalAndData($id, $approvaldetails->session_id,"STOCK");  
                    }
                    else if($approval=="reject") {
                    $this->deleteApprovalAndData($id, $approvaldetails->session_id,"STOCK");      
                    }
                     else if($approval=="delete") {
                    $this->truncateApprovalAndData($id, $approvaldetails->session_id,"STOCK");      
                    }

                    break;


                default:
                    break;
            }
            if($approval=="approve"){
            echo "Approval_action_completed";
            }
            if($approval=="reject"){
            echo "Reject_action_completed";
            }
            if($approval=="delete"){
            echo "Delete_action_completed";
            }
            exit();
            
        }
                
    }
    
    
    function deleteApprovalAndData($id,$dataid,$datattype){
       if($datattype=="SALE"){
           $this->db->delete("upload_approvals", array('session_id' => $dataid)); 
           $this->db->delete("temp_sales", array('session_id' => $dataid));
           $this->db->delete("temp_sale_items", array('session_id' => $dataid));
       }
       else if($datattype=="STOCK"){
           $this->db->delete("upload_approvals", array('session_id' => $dataid)); 
          $this->db->delete("temp_purchases", array('session_id' => $dataid));
           $this->db->delete("temp_purchase_items", array('session_id' => $dataid));  
       }
       else if($datattype=="BUDGET"){
           $this->db->delete("upload_approvals", array('session_id' => $dataid)); 
            $this->db->delete("temp_budget", array('session_id' => $dataid));
       }
        $user = $this->site->getUser();
      $this->db->where('id',$id);
     //  $this->db->update('upload_approvals',array("approved"=>"N","date_updated"=>date("Y-m-d H:i:s"),"approvals"=>$user->id)); 
       
           
    }
    
    
    function truncateApprovalAndData($id,$dataid,$datattype){
       if($datattype=="SALE"){
           
           $this->db->where("sale_id IN (SELECT id from sma_sales WHERE session_id='$dataid')");
           $this->db->delete("sale_items");
           
           $this->db->delete("sales", array('session_id' => $dataid));
           $this->db->delete("upload_approvals", array('session_id' => $dataid)); 
       }
       else if($datattype=="STOCK"){
            $this->db->where("purchase_id IN (SELECT id from sma_purchases WHERE session_id='$dataid')");
           $this->db->delete("purchase_items");
           
          $this->db->delete("purchases", array('session_id' => $dataid));
        $this->db->delete("upload_approvals", array('session_id' => $dataid)); 
       }
       else if($datattype=="BUDGET"){
          
            $this->db->delete("budget", array('session_id' => $dataid));
             $this->db->delete("upload_approvals", array('session_id' => $dataid)); 
       }
        $user = $this->site->getUser();
      $this->db->where('id',$id);
     //  $this->db->update('upload_approvals',array("approved"=>"N","date_updated"=>date("Y-m-d H:i:s"),"approvals"=>$user->id)); 
       
           
    }
    
    
 function getApproval($id){
         $q = $this->db->get_where("upload_approvals", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function filedata(){
        $id=$this->input->get("id");
        $approvaldetails=$this->getApproval($id);
        $uploadtype=strtoupper($approvaldetails->upload_type);
        if($uploadtype=="SALE"){
          redirect("sales/sales?session_id=".$approvaldetails->session_id);  
        }
        else if($uploadtype=="STOCK"){
         redirect("sales/stock?session_id=".$approvaldetails->session_id);   
            
        }
        else if($uploadtype=="BUDGET")
                {
            redirect("sales/budgets?session_id=".$approvaldetails->session_id);
                }
                else {
                    die("No such upload type");
                }
        
    }
    
        
 
	}

