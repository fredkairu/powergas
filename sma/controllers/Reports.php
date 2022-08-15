<?php defined('BASEPATH') OR exit('No direct script access allowed');
  require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");

class Reports extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }

        $this->lang->load('reports', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('reports_model');
        $this->load->model('settings_model');  
       $this->load->model('companies_model');
        $this->load->model('sales_model');
        $this->load->model('cluster_model');
        $this->load->model('site');
ini_set('memory_limit', '8096M');
    }

    function index()
    {
        $this->sma->checkPermissions();
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();
        $this->data['stock'] = $this->reports_model->getStockValue();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
        $this->page_construct('reports/index', $meta, $this->data);

    }

    function warehouse_stock($warehouse = NULL)
    {
        $this->sma->checkPermissions('index', TRUE);
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }

        $this->data['stock'] = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
        $this->data['warehouses'] = $this->reports_model->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse'] = $warehouse ? $this->site->getWarehouseByID($warehouse) : NULL;
        $this->data['totals'] = $this->reports_model->getWarehouseTotals($warehouse);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
        $this->page_construct('reports/warehouse_stock', $meta, $this->data);

    }

    function expiry_alerts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('expiry_alerts');
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('product_expiry_alerts')));
        $meta = array('page_title' => lang('product_expiry_alerts'), 'bc' => $bc);
        $this->page_construct('reports/expiry_alerts', $meta, $this->data);
    }

    function getExpiryAlerts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('expiry_alerts', TRUE);
        $date = date('Y-m-d', strtotime('+3 months'));

        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('warehouse_id', $warehouse_id)->where('expiry <', $date);
        } else {
            $this->datatables
                ->select("image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('expiry <', $date);
        }
        echo $this->datatables->generate();
    }

    function quantity_alerts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('quantity_alerts');
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('product_quantity_alerts')));
        $meta = array('page_title' => lang('product_quantity_alerts'), 'bc' => $bc);
        $this->page_construct('reports/quantity_alerts', $meta, $this->data);
    }

    function getQuantityAlerts($warehouse_id = NULL, $pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('quantity_alerts', TRUE);
        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        if ($pdf || $xls) {

            if ($warehouse_id) {
                $this->db
                    ->select('products.image as image, products.code, products.name, warehouses_products.quantity, alert_quantity')
                    ->from('products')->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
                    ->where('alert_quantity > warehouses_products.quantity', NULL)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('track_quantity', 1)
                    ->order_by('products.code desc');
            } else {
                $this->db
                    ->select('image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', NULL)
                    ->where('track_quantity', 1)
                    ->order_by('code desc');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('product_quantity_alerts'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('alert_quantity'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->alert_quantity);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);

                $filename = 'product_quantity_alerts';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            if ($warehouse_id) {
                $this->datatables
                    ->select('products.image as image, products.code, products.name, warehouses_products.quantity, alert_quantity')
                    ->from('products')->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
                    ->where('alert_quantity > warehouses_products.quantity', NULL)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('track_quantity', 1);
            } else {
                $this->datatables
                    ->select('image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', NULL)
                    ->where('track_quantity', 1);
            }

            echo $this->datatables->generate();

        }

    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1) {
            die();
        }

        $rows = $this->reports_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")");

            }
            echo json_encode($pr);
        } else {
            echo FALSE;
        }
    }

    function products()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('products_report')));
        $meta = array('page_title' => lang('products_report'), 'bc' => $bc);
        $this->page_construct('reports/products', $meta, $this->data);
    }

    function getProductsReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('products', TRUE);
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('cf1')) {
            $cf1 = $this->input->get('cf1');
        } else {
            $cf1 = NULL;
        }
        if ($this->input->get('cf2')) {
            $cf2 = $this->input->get('cf2');
        } else {
            $cf2 = NULL;
        }
        if ($this->input->get('cf3')) {
            $cf3 = $this->input->get('cf3');
        } else {
            $cf3 = NULL;
        }
        if ($this->input->get('cf4')) {
            $cf4 = $this->input->get('cf4');
        } else {
            $cf4 = NULL;
        }
        if ($this->input->get('cf5')) {
            $cf5 = $this->input->get('cf5');
        } else {
            $cf5 = NULL;
        }
        if ($this->input->get('cf6')) {
            $cf6 = $this->input->get('cf6');
        } else {
            $cf6 = NULL;
        }
        if ($this->input->get('category')) {
            $category = $this->input->get('category');
        } else {
            $category = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');

            $pp = "( SELECT pi.product_id, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase, p.date as pdate from " . $this->db->dbprefix('purchases') . " p JOIN " . $this->db->dbprefix('purchase_items') . " pi on p.id = pi.purchase_id where p.date >= '{$start_date}' and p.date < '{$end_date}' group by pi.product_id ) PCosts";
            $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale, s.date as sdate from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id where s.date >= '{$start_date}' and s.date < '{$end_date}' group by si.product_id ) PSales";
          //  die($sp);
        } else {
           die("Select Period");
            $pp = "( SELECT pi.product_id, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from " . $this->db->dbprefix('purchase_items') . " pi group by pi.product_id ) PCosts";
            $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from " . $this->db->dbprefix('sale_items') . " si group by si.product_id ) PSales";
        }
        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,
				COALESCE( PCosts.purchasedQty, 0 ) as PurchasedQty,
				COALESCE( PSales.soldQty, 0 ) as SoldQty,
				COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
				COALESCE( PSales.totalSale, 0 ) as TotalSales", FALSE)
                ->from('products')
                ->join($sp, 'products.id = PSales.product_id', 'left')
                ->join($pp, 'products.id = PCosts.product_id', 'left')
                ->order_by('products.name');
                   // ->where('products.iskitchen', 0);

            if ($product) {
                $this->db->where($this->db->dbprefix('products') . ".id", $product);
            }
            if ($cf1) {
                $this->db->where($this->db->dbprefix('products') . ".cf1", $cf1);
            }
            if ($cf2) {
                $this->db->where($this->db->dbprefix('products') . ".cf2", $cf2);
            }
            if ($cf3) {
                $this->db->where($this->db->dbprefix('products') . ".cf3", $cf3);
            }
            if ($cf4) {
                $this->db->where($this->db->dbprefix('products') . ".cf4", $cf4);
            }
            if ($cf5) {
                $this->db->where($this->db->dbprefix('products') . ".cf5", $cf5);
            }
            if ($cf6) {
                $this->db->where($this->db->dbprefix('products') . ".cf6", $cf6);
            }
            if ($category) {
                $this->db->where($this->db->dbprefix('products') . ".category_id", $category);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":G" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

                $filename = 'products_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,
				COALESCE( PCosts.purchasedQty, 0 ) as PurchasedQty,
				COALESCE( PSales.soldQty, 0 ) as SoldQty,
				COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
				COALESCE( PSales.totalSale, 0 ) as TotalSales,
				(COALESCE( PSales.totalSale, 0 ) - COALESCE( PCosts.totalPurchase, 0 )) as Profit", FALSE)
                ->from('products')
                ->join($sp, 'sma_products.id = PSales.product_id', 'left')
                ->join($pp, 'sma_products.id = PCosts.product_id', 'left')
                 ->where('products.iskitchen', 0)
                    
             ->group_by('products.id');
      

            if ($product) {
                $this->datatables->where($this->db->dbprefix('products') . ".id", $product);
            }
            if ($cf1) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf1", $cf1);
            }
            if ($cf2) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf2", $cf2);
            }
            if ($cf3) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf3", $cf3);
            }
            if ($cf4) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf4", $cf4);
            }
            if ($cf5) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf5", $cf5);
            }
            if ($cf6) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf6", $cf6);
            }
            if ($category) {
                $this->datatables->where($this->db->dbprefix('products') . ".category_id", $category);
            }
 
            echo $this->datatables->generate();

        }

    }

    function categories()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('categories_report')));
        $meta = array('page_title' => lang('categories_report'), 'bc' => $bc);
        $this->page_construct('reports/categories', $meta, $this->data);
    }
    
    function vehicles()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['vehicles'] = $this->site->getAllVehicles();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Vehicles_Report')));
        $meta = array('page_title' => lang('Vehicles_Report'), 'bc' => $bc);
        $this->page_construct('reports/vehicle', $meta, $this->data);
    }
    
    function customer_payment_method()
    {
        //$this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['payment_methods'] =  $this->companies_model->getAllPaymentMethods();

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Customer_Payment_Method_Report')));
        $meta = array('page_title' => lang('Customer_Payment_Method_Report'), 'bc' => $bc);
        $this->page_construct('reports/customer_payment_methods', $meta, $this->data);
    }

    function getCustomerByPaymentMethod(){
        //$this->sma->checkPermissions('vehicles', TRUE);

        if ($this->input->get('payment_method')) {
            $payment_method = $this->input->get('payment_method');
        } else {
            $payment_method = NULL;
        }

        $this->load->library('datatables');
        if ($payment_method) {
            $this->datatables
                ->select("sma_customers.name, sma_credit_limit.cash_limit", FALSE)
                ->from('sma_customers')
                ->join('sma_credit_limit', 'sma_credit_limit.customer_id = sma_customers.id', 'left')
                ->join('sma_customer_payment_methods', 'sma_customer_payment_methods.customer_id = sma_customers.id', 'left')
                ->join('sma_payment_methods', 'sma_payment_methods.id = sma_customer_payment_methods.payment_method_id', 'left')
                ->where('sma_customer_payment_methods.payment_method_id=',$payment_method)
                ->group_by('sma_customers.id');
        } else {
            $this->datatables
                ->select("sma_customers.name, sma_credit_limit.cash_limit", FALSE)
                ->from('sma_customers')
                ->join('sma_credit_limit', 'sma_credit_limit.customer_id = sma_customers.id', 'left')
                ->join('sma_customer_payment_methods', 'sma_customer_payment_methods.customer_id = sma_customers.id', 'left')
                ->join('sma_payment_methods', 'sma_payment_methods.id = sma_customer_payment_methods.payment_method_id', 'left')
                ->group_by('sma_customers.id');
        }
        if ($pdf || $xls) {

            $this->db->from('sma_products');

            if ($vehicle) {
                $this->db->where($this->db->dbprefix('vehicles') . ".id", $vehicle);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $vehicles = $this->site->getAllVehicles();
                foreach($vehicles as $vhcl){
                    if($vhcl->id == $vehicle){
                        $vehicle_plate = $vhcl->plate_no;
                    }
                }
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle('Vehicle Report');
                $this->excel->getActiveSheet()->SetCellValue('A1', 'Vehicle Report For '.$vehicle_plate." From ".$start_date." To ".$end_date);
                $this->excel->getActiveSheet()->SetCellValue('A2', lang('Product'));
                $this->excel->getActiveSheet()->SetCellValue('B2', lang('Quantity'));
                $this->excel->getActiveSheet()->SetCellValue('C2', lang('Amount'));

                $row = 3;
                $sQty = 0;
                $sAmt = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->soldQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->totalSale);

                    $sQty += $data_row->soldQty;
                    $sAmt += $data_row->totalSale;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("B" . $row . ":C" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sAmt);
                $this->excel->getActiveSheet()->getColumnDimension('A1')->setWidth(100);
                $this->excel->getActiveSheet()->getColumnDimension('A2')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B2')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C2')->setWidth(15);

                $filename = 'vehicle report for '.$vehicle_plate." from ".$start_date." to ".$end_date;
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('B3:C' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            echo $this->datatables->generate();

        }
    }
    
    function salespeople()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['salespeople'] = $this->site->getAllSalesmen();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Sales_Report')));
        $meta = array('page_title' => lang('Sales_Report'), 'bc' => $bc);
        $this->page_construct('reports/salesman', $meta, $this->data);
    }

    function getSalespersonReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('vehicles', TRUE);

        if ($this->input->get('salesperson')) {
            $salesperson = $this->input->get('salesperson');
        } else {
            $salesperson = NULL;
        }
        if ($this->input->get('payment_method')) {
            $payment_method = $this->input->get('payment_method');
        } else {
            $payment_method = NULL;
        }
        if ($this->input->get('start_date')) {
            $time = strtotime($this->input->get('start_date'));

            $newformat = date('Y-d-m',$time);
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $time = strtotime($this->input->get('end_date'));

            $newformat = date('Y-d-m',$time);
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        $this->load->library('datatables');
        if ($start_date) {
            $start_date = $start_date;
            $end_date = $end_date ? $end_date : date('Y-m-d');
            $this->datatables
                ->select("sma_products.name, SUM(sma_sale_items.quantity) AS soldQty, SUM(sma_sale_items.subtotal) as totalSale", FALSE)
                ->from('sma_products')
                ->join("sma_sale_items", 'sma_sale_items.product_id = sma_products.id', 'left')
                ->join("sma_sales", 'sma_sales.id = sma_sale_items.sale_id', 'left')
                ->join("sma_companies", 'sma_sales.salesman_id = sma_companies.id', 'left')
                ->join("sma_payments", 'sma_sales.id = sma_payments.sale_id', 'left')
                ->where("sma_sales.date >= ",$start_date)
                ->where("sma_sales.date <= ",$end_date)
                ->group_by('sma_products.id');

            if ($salesperson) {
                $this->datatables->where($this->db->dbprefix('companies') . ".id", $salesperson);
            }
            if ($payment_method and $payment_method!="All") {
                $this->datatables->where($this->db->dbprefix('payments') . ".paid_by", $payment_method);
            }
        } else {
            $this->datatables
                ->select("sma_products.name, SUM(sma_sale_items.quantity) AS soldQty, SUM(sma_sale_items.subtotal) as totalSale", FALSE)
                ->from('sma_products')
                ->join("sma_sale_items", 'sma_sale_items.product_id = sma_products.id', 'left')
                ->join("sma_sales", 'sma_sales.id = sma_sale_items.sale_id', 'left')
                ->join("sma_companies", 'sma_sales.salesman_id = sma_companies.id', 'left')
                ->group_by('sma_products.id');
        }
        if ($pdf || $xls) {

            
            $this->db->from('sma_products');
            
            if ($salesperson) {
                $this->db->where($this->db->dbprefix('companies') . ".id", $salesperson);
            }
            if ($payment_method) {
                $this->db->where($this->db->dbprefix('payments') . ".paid_by", $payment_method);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $salespeople = $this->site->getAllSalesmen();
                foreach($salepeople as $vhcl){
                    if($vhcl->id == $salesperson){
                        $vehicle_plate = $vhcl->name;
                    }
                }
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle('Sales Report');
                $this->excel->getActiveSheet()->SetCellValue('A1', 'Salesman Report For '.$vehicle_plate." From ".$start_date." To ".$end_date);
                $this->excel->getActiveSheet()->SetCellValue('A2', lang('Product'));
                $this->excel->getActiveSheet()->SetCellValue('B2', lang('Quantity'));
                $this->excel->getActiveSheet()->SetCellValue('C2', lang('Amount'));

                $row = 3;
                $sQty = 0;
                $sAmt = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->soldQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->totalSale);
                    
                    $sQty += $data_row->soldQty;
                    $sAmt += $data_row->totalSale;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("B" . $row . ":C" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sAmt);
                $this->excel->getActiveSheet()->getColumnDimension('A1')->setWidth(100);
                $this->excel->getActiveSheet()->getColumnDimension('A2')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B2')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C2')->setWidth(15);

                $filename = 'sales report for '.$vehicle_plate." from ".$start_date." to ".$end_date;
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('B3:C' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            
            //$this->datatables->unset_column('vid');
            echo $this->datatables->generate();

        }

    }
    
    function getCategoriesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('categories', TRUE);

        if ($this->input->get('category')) {
            $category = $this->input->get('category');
        } else {
            $category = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');

            $pp = "( SELECT pp.category_id as category, pi.product_id, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase, p.date as pdate from " . $this->db->dbprefix('products') . " pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi on pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id
                where p.date >= '{$start_date}' and p.date < '{$end_date}' group by pp.category_id
                ) PCosts";
            $sp = "( SELECT sp.category_id as category, si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale, s.date as sdate from " . $this->db->dbprefix('products') . " sp
                left JOIN " . $this->db->dbprefix('sale_items') . " si on sp.id = si.product_id
                left join " . $this->db->dbprefix('sales') . " s ON s.id = si.sale_id
                where s.date >= '{$start_date}' and s.date < '{$end_date}' group by sp.category_id
                ) PSales";
        } else {
            $pp = "( SELECT pp.category_id as category, pi.product_id, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from " . $this->db->dbprefix('products') . " pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi on pp.id = pi.product_id
                group by pp.category_id
                ) PCosts";
            $sp = "( SELECT sp.category_id as category, si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from " . $this->db->dbprefix('products') . " sp
                left JOIN " . $this->db->dbprefix('sale_items') . " si on sp.id = si.product_id
                group by sp.category_id
                ) PSales";
        }
        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('categories') . ".code, " . $this->db->dbprefix('categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('categories')
                ->join($sp, 'categories.id = PSales.category', 'left')
                ->join($pp, 'categories.id = PCosts.category', 'left')
                ->group_by('categories.id');

            if ($category) {
                $this->db->where($this->db->dbprefix('categories') . ".id", $category);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('categories_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('category_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('category_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":G" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

                $filename = 'categories_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('categories') . ".id as cid, " .$this->db->dbprefix('categories') . ".code, " . $this->db->dbprefix('categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('categories')
                ->join($sp, 'categories.id = PSales.category', 'left')
                ->join($pp, 'categories.id = PCosts.category', 'left')
            ->group_by('categories.id');

            if ($category) {
                $this->datatables->where($this->db->dbprefix('categories') . ".id", $category);
            }
            $this->datatables->unset_column('cid');
            echo $this->datatables->generate();

        }

    }
    
    function getVehiclesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('vehicles', TRUE);

        if ($this->input->get('vehicle')) {
            $vehicle = $this->input->get('vehicle');
        } else {
            $vehicle = NULL;
        }
        if ($this->input->get('start_date')) {
            $time = strtotime($this->input->get('start_date'));

            $newformat = date('Y-d-m',$time);
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $time = strtotime($this->input->get('end_date'));

            $newformat = date('Y-d-m',$time);
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        $this->load->library('datatables');
        if ($start_date) {
            $start_date = $start_date;
            $end_date = $end_date ? $end_date : date('Y-m-d');
            $this->datatables
                ->select("sma_products.name, SUM(sma_sale_items.quantity) AS soldQty, SUM(sma_sale_items.subtotal) as totalSale", FALSE)
                ->from('sma_products')
                ->join("sma_sale_items", 'sma_sale_items.product_id = sma_products.id', 'left')
                ->join("sma_sales", 'sma_sales.id = sma_sale_items.sale_id', 'left')
                ->join("sma_vehicles", 'sma_sales.vehicle_id = sma_vehicles.id', 'left')
                ->where("sma_sales.date >= ",$start_date)
                ->where("sma_sales.date <= ",$end_date)
                ->group_by('sma_products.id');

            if ($vehicle) {
                $this->datatables->where($this->db->dbprefix('vehicles') . ".id", $vehicle);
            }
        } else {
            $this->datatables
                ->select("sma_products.name, SUM(sma_sale_items.quantity) AS soldQty, SUM(sma_sale_items.subtotal) as totalSale", FALSE)
                ->from('sma_products')
                ->join("sma_sale_items", 'sma_sale_items.product_id = sma_products.id', 'left')
                ->join("sma_sales", 'sma_sales.id = sma_sale_items.sale_id', 'left')
                ->join("sma_vehicles", 'sma_sales.vehicle_id = sma_vehicles.id', 'left')
                ->group_by('sma_products.id');
        }
        if ($pdf || $xls) {

            
            $this->db->from('sma_products');

            if ($vehicle) {
                $this->db->where($this->db->dbprefix('vehicles') . ".id", $vehicle);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $vehicles = $this->site->getAllVehicles();
                foreach($vehicles as $vhcl){
                    if($vhcl->id == $vehicle){
                        $vehicle_plate = $vhcl->plate_no;
                    }
                }
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle('Vehicle Report');
                $this->excel->getActiveSheet()->SetCellValue('A1', 'Vehicle Report For '.$vehicle_plate." From ".$start_date." To ".$end_date);
                $this->excel->getActiveSheet()->SetCellValue('A2', lang('Product'));
                $this->excel->getActiveSheet()->SetCellValue('B2', lang('Quantity'));
                $this->excel->getActiveSheet()->SetCellValue('C2', lang('Amount'));

                $row = 3;
                $sQty = 0;
                $sAmt = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->soldQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->totalSale);
                    
                    $sQty += $data_row->soldQty;
                    $sAmt += $data_row->totalSale;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("B" . $row . ":C" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sAmt);
                $this->excel->getActiveSheet()->getColumnDimension('A1')->setWidth(100);
                $this->excel->getActiveSheet()->getColumnDimension('A2')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B2')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C2')->setWidth(15);

                $filename = 'vehicle report for '.$vehicle_plate." from ".$start_date." to ".$end_date;
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('B3:C' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            
            //$this->datatables->unset_column('vid');
            echo $this->datatables->generate();

        }

    }
    
     function getSubCategoriesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('categories', TRUE);

        if ($this->input->get('subcategory')) {
            $category = $this->input->get('subcategory');
        } else {
            $category = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');

            $pp = "( SELECT pp.category_id as category, pi.product_id, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase, p.date as pdate from " . $this->db->dbprefix('products') . " pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi on pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id
                where p.date >= '{$start_date}' and p.date < '{$end_date}' group by pp.category_id
                ) PCosts";
            $sp = "( SELECT sp.category_id as category, si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale, s.date as sdate from " . $this->db->dbprefix('products') . " sp
                left JOIN " . $this->db->dbprefix('sale_items') . " si on sp.id = si.product_id
                left join " . $this->db->dbprefix('sales') . " s ON s.id = si.sale_id
                where s.date >= '{$start_date}' and s.date < '{$end_date}' group by sp.category_id
                ) PSales";
        } else {
            $pp = "( SELECT pp.category_id as category, pi.product_id, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from " . $this->db->dbprefix('products') . " pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi on pp.id = pi.product_id
                group by pp.category_id
                ) PCosts";
            $sp = "( SELECT sp.category_id as category, si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from " . $this->db->dbprefix('products') . " sp
                left JOIN " . $this->db->dbprefix('sale_items') . " si on sp.id = si.product_id
                group by sp.category_id
                ) PSales";
        }
        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('categories') . ".code, " . $this->db->dbprefix('categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('categories')
                ->join($sp, 'categories.id = PSales.category', 'left')
                ->join($pp, 'categories.id = PCosts.category', 'left')
                ->group_by('categories.id');

            if ($category) {
                $this->db->where($this->db->dbprefix('categories') . ".id", $category);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('categories_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('category_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('category_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":G" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

                $filename = 'categories_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('categories') . ".id as cid, " .$this->db->dbprefix('categories') . ".code, " . $this->db->dbprefix('categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('categories')
                ->join($sp, 'categories.id = PSales.category', 'left')
                ->join($pp, 'categories.id = PCosts.category', 'left')
            ->group_by('categories.id');

            if ($category) {
                $this->datatables->where($this->db->dbprefix('categories') . ".id", $category);
            }
            $this->datatables->unset_column('cid');
            echo $this->datatables->generate();

        }

    }

    function daily_sales($year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL)
    {
        $this->sma->checkPermissions('daily_sales');
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => site_url('reports/daily_sales'),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
		{heading_row_start}<tr>{/heading_row_start}
		{heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
		{heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
		{heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
		{heading_row_end}</tr>{/heading_row_end}
		{week_row_start}<tr>{/week_row_start}
		{week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
		{week_row_end}</tr>{/week_row_end}
		{cal_row_start}<tr class="days">{/cal_row_start}
		{cal_cell_start}<td class="day">{/cal_cell_start}
		{cal_cell_content}
		<div class="day_num">{day}</div>
		<div class="content">{content}</div>
		{/cal_cell_content}
		{cal_cell_content_today}
		<div class="day_num highlight">{day}</div>
		<div class="content">{content}</div>
		{/cal_cell_content_today}
		{cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
		{cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
		{cal_cell_blank}&nbsp;{/cal_cell_blank}
		{cal_cell_end}</td>{/cal_cell_end}
		{cal_row_end}</tr>{/cal_row_end}
		{table_close}</table>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $user_id ? $sales = $this->reports_model->getStaffDailySales($user_id, $year, $month) : $this->reports_model->getDailySales($year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("shipping") . "</td><td>" . $this->sma->formatMoney($sale->shipping) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/daily', $this->data, true);
            $name = lang("daily_sales") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('daily_sales_report')));
        $meta = array('page_title' => lang('daily_sales_report'), 'bc' => $bc);
        $this->page_construct('reports/daily', $meta, $this->data);

    }


    function monthly_sales($year = NULL, $pdf = NULL, $user_id = NULL)
    {
        $this->sma->checkPermissions('monthly_sales');
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year'] = $year;
        $this->data['sales'] = $user_id ? $this->reports_model->getStaffMonthlySales($user_id, $year) : $this->reports_model->getMonthlySales($year);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/monthly', $this->data, true);
            $name = lang("monthly_sales") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('monthly_sales_report')));
        $meta = array('page_title' => lang('monthly_sales_report'), 'bc' => $bc);
        $this->page_construct('reports/monthly', $meta, $this->data);

    }
function bestsellingproducts($cluster,$country,$datefrom,$dateto,$productcategoryfamily,$gbu="Rx")
    {
		//$sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, s.date as sdate from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id WHERE s.sales_cluster = '".$cluster."' AND s.country_id = '".$country."' AND s.date BETWEEN '".$datefrom."' and '".$dateto."' AND s.sa group by si.product_id ) PSales";
     //   $sp = "( SELECT si.product_id, SUM( s.shipping ) soldQty, s.date as sdate from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id  group by si.product_id ) PSales";
 $sp = "(SELECT si.product_id,s.sales_type,s.country_id,s.date,s.sales_cluster, SUM(s.shipping) as soldQty, s.date as sdate,s.staff_note from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id group by si.product_id ) PSales";

		$this->db
            ->select("" . $this->db->dbprefix('products') . ".name as product, COALESCE( PSales.soldQty, 0 ) as qty , " . $this->db->dbprefix('products') . ".price as unified_value ", FALSE)
            ->from('products', FALSE)
            ->join($sp, 'products.id = PSales.product_id', 'left')
			->where('sales_type','PSO')
            ->order_by('PSales.soldQty desc')
            ->limit(10);
			//if($productcategoryfamily){
			//$this->db->where('products.category_id', $productcategoryfamily);
			//}if($gbu){
			//$this->db->where('products.business_unit', $gbu);	
			//}
        $q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
           // return $data;
			echo json_encode($data);
			die();
        }
		
        return FALSE;
    }
    
    
     function budget()
    {
        $this->sma->checkPermissions('sales');
          $this->load->model('sales_model');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
		 //$this->data['clusters']=  $this->cluster_model->getClusters();
		$this->data['currencies']=  $this->site->getAllCurrencies();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['clusters'] =$this->site->getAllClusters();
               
		$this->data['sanoficustomer']=$this->companies_model->getAllCustomerCustomers();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Budgets_report')));
        $meta = array('page_title' => lang('Budgets_report'), 'bc' => $bc);
        $this->page_construct('reports/budget', $meta, $this->data);
    }
    
    function getBudgetReport($pdf = NULL, $xls = NULL)
    {
        
        $this->sma->checkPermissions('sales', TRUE);
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
		if ($this->input->get('type')) {
            $salestype = $this->input->get('type');
        } else {
            $salestype = NULL;
        }
		//print_r($_GET['cluster']);
		//die();
		if ($this->input->get('cluster')) {
            $cluster = $_GET['cluster'];
			//print_r($cluster);
	//die();
        } else {
            $cluster = NULL;
        }

		if ($this->input->get('country')) {
            $country = $this->input->get('country');
        } else {
            $country = NULL;
        }
		
		//echo $salestype;
		//die();
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('PSOdist')) {
            $PSOdist = $this->input->get('PSOdist');
        } else {
            $PSOdist = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        
        if ($this->input->get('budget_forecast')) {
            $budget_forecast = $this->input->get('budget_forecast');
        } else {
            $budget_forecast = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('serial')) {
            $serial = $this->input->get('serial');
        } else {
            $serial = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld('01/'.$start_date);
            $end_date =$this->sma->fld('31/'.$end_date);
            
            
        }
        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

 $this->db->select("sma_budget.id as id,DATE_FORMAT(sma_budget.date,'%m-%Y') as date,scenario,budget_forecast,currencies.country,companies.name as names,products.business_unit,products.name as name,if(sma_budget.net_gross= 'N',sma_budget.budget_qty,'0') as budget_qty,if(sma_budget.net_gross='N',sma_budget.budget_value,'0') as budget_value,if(sma_budget.net_gross= 'G',sma_budget.budget_qty,'0') as gqty,if(sma_budget.net_gross='G',sma_budget.budget_value,'0') as gvalue ")
                  
                ->from('budget')
                    ->join("products","budget.product_id=products.id","left")
                     ->join("companies","budget.distributor_id=companies.id","left")
                    ->join("currencies","budget.country=currencies.id","left");
				
 $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          if($start_date==""){
$this->db->where('DATE_FORMAT(sma_budget.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
//$this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . $yesterday . '" and "' . $now . '"');
            
			if ($salestype=="SI") {
			    if($PSOdist){
                $this->db->where('budget.scenario', $salestype);
               $this->db->where('sma_budget.distributor_name', $PSOdist);  
                
			    }else{
			     $this->db->where('budget.scenario', $salestype);   
			    }
            }
			if ($salestype=="PSO") {
			    if($PSOdist){
                $this->db->where('budget.scenario', $salestype);
                if($PSOdist=='MERCAFAR'){
                    $PSOnew = $PSOdist;
                $this->db->where('sma_budget.distributor_name', $PSOnew);
                }else{
                  $this->db->where('sma_budget.distributor_name', $PSOdist);  
                }
			    }else{
			     $this->db->where('budget.scenario', $salestype);   
			    }
            }	
            if ($salestype=="SSO") {
			    
			     $this->db->where('budget.scenario', $salestype);   
			    
            }	
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->db->where('sales.sales_cluster', $cluster);
          //  }
			if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->db->where('budget.country IN ('.$selectedOption.')');
	
            }			
            if ($product) {
                $this->db->like('products.product_id', $product);
            }
            if ($serial) {
                $this->db->like('products.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('budget.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('budget.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('budget.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('budget.reference_no', $reference_no, 'both');
            }
              if ( $budget_forecast) {
                $this->db->like('budget.budget_forecast', $budget_forecast);
            }
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
              $this->db->where('sma_budget.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
               // $this->db->where('STR_TO_DATE(sma_budget.date,"%d-%m-%Y") BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
				
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('budget_forecast_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('scenario'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('budget_forecast'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('country'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('distributor_customer'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Business_unit'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Product_SKU'));
				$this->excel->getActiveSheet()->SetCellValue('H1', lang('Net_Qty'));
                                 $this->excel->getActiveSheet()->SetCellValue('I1', lang('Net_Value'));
                                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Gross_Qty'));
                $this->excel->getActiveSheet()->SetCellValue('K1', lang('Gross_Value'));
			
                

                $row = 2;
				$qty = 0;
                $unified = 0;
                $resale = 0;
                $avprice = 0;
				$supply =0;
    //sma_budget.id as id,sma_budget.date as date,scenario,budget_forecast,currencies.country,companies.name as names,products.business_unit,products.name as name,budget_qty,budget_value,budget_at_resale,budget_at_supply,av_price
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->date);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->scenario);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->budget_forecast);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->country);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->names);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->business_unit);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->name);
					$this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->budget_qty);
                                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->budget_value);
                                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->gqty);
                    $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->gvalue);

					
                    $qty += $data_row->budget_quantity;
					$unified += $data_row->budget_value;
                    $resale += $data_row->gqty;
					$supply += $data_row->gvalue;
                    //$avprice += $data_row->av_price;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
				 $this->excel->getActiveSheet()->SetCellValue('H' . $row, $qty);	
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $unified);
                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $resale);
				$this->excel->getActiveSheet()->SetCellValue('K' . $row, $supply);
              
               

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				
                $filename = 'budget_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {
        // die( $start_date." fsdfd".$end_date);
$delete_link = "<a href='#' class='po' title='<b>" . lang("delete_budget") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('budgets/delete_budget/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_budget') . "</a>";     
$edi_link = '<a href="' . site_url('budgets/edit_budget/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_budget') . '</a>';    

             $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
         <li>' . $edi_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

            $this->load->library('datatables');
          
           
          $this->datatables
                  // ->select("sma_budget.id as id,DATE_FORMAT(sma_budget.date,'%m-%Y') as date,scenario,budget_forecast,currencies.country,companies.name as names,customers.name as custname,categories.gbu as business_unit,products.name as name,categories.name as brand,budget_qty,budget_value,CONCAT('<a href=\'budgets/edit_budget/',sma_budget.id,'\'>IF(tender_price= 0,quantity_units,'0') as quantity_units<i class=\"fa fa-edit\"></a>') as link,CONCAT('<a href=\'budgets/delete/',sma_budget.id,'\'><i class=\"fa fa-trash-o\"></a>') as link")
               ->select("sma_budget.id as id,DATE_FORMAT(sma_budget.date,'%m-%Y') as date,scenario,budget_forecast,sma_currencies.country,sma_companies.name as names,sma_customers.name as custname,sma_categories.gbu as business_unit,sma_products.name as name,sma_categories.name as brand,if(sma_budget.net_gross= 'N',sma_budget.budget_qty,'0') as nqty,if(sma_budget.net_gross='N',sma_budget.budget_value,'0') as nvalue,if(sma_budget.net_gross= 'G',sma_budget.budget_qty,'0') as gqty,if(sma_budget.net_gross='G',sma_budget.budget_value,'0') as gvalue")

                ->from('budget')
                    ->join("products","budget.product_id=products.id","left")
                     ->join("companies","budget.distributor_id=companies.id","left")
                      ->join("customers","budget.customer_id=customers.id","left")
                      ->join('categories', 'categories.id=products.category_id', 'left')
                    ->join("currencies","budget.country=currencies.id","left")
                    ->add_column("Actions", "<center>".$delete_link."</center>");
   
     
        $this->datatables->add_column("Actions", $action, "id");
                                    
               // ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
				//->order_by('sale_items.product_name')
               
           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          if($start_date==""){
//$this->datatables->where('DATE_FORMAT(sma_budget.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');

$this->datatables->where('DATE_FORMAT(sma_budget.date,"%m") = "'.date('m').'" ');
            }
//$this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . $yesterday . '" and "' . $now . '"');
//            if ($user)  {
//                $this->datatables->where('budget.created_by', $user);
//            }
			if ($salestype=="SI") {
			    if($PSOdist){
                $this->datatables->where('budget.scenario', $salestype);
               $this->datatables->where('sma_budget.distributor_name', $PSOdist);  
                
			    }else{
			     $this->datatables->where('budget.scenario', $salestype);   
			    }
            }
			if ($salestype=="PSO") {
			    if($PSOdist){
                $this->datatables->where('budget.scenario', $salestype);
                if($PSOdist=='MERCAFAR'){
                    $PSOnew = $PSOdist;
                $this->datatables->where('sma_budget.distributor_name', $PSOnew);
                }else{
                  $this->datatables->where('sma_budget.distributor_name', $PSOdist);  
                }
			    }else{
			     $this->datatables->where('budget.scenario', $salestype);   
			    }
            }	
            if ($salestype=="SSO") {
			    
			     $this->datatables->where('budget.scenario', $salestype);   
			    
            }	
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->datatables->where('sales.sales_cluster', $cluster);
          //  }
			if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->datatables->where('budget.country IN ('.$selectedOption.')');
	
            }			
            if ($product) {
                $this->datatables->like('products.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('products.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('budget.biller_id', $biller);
            }
           if ($customer) {
                $this->datatables->where('budget.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('budget.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('budget.reference_no', $reference_no, 'both');
            }
              if ( $budget_forecast) {
                $this->datatables->like('budget.budget_forecast', $budget_forecast);
            }
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
                $this->datatables->where('sma_budget.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }

            echo $this->datatables->generate();

        }

    }
    
    
	
    function sales()
    {
        $this->sma->checkPermissions('sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       $this->load->model('cluster_model');
        if($this->input->post("delete")){
            $this->load->model('sales_model');  
            $sales_type=$this->input->post("type");
            $fromdate=$this->input->post("start_date");
             $fromdate=substr($fromdate,-4)."-".substr($fromdate,0,2)."-01";
            $todate=$this->input->post("end_date");
            $todate=substr($todate,-4)."-".substr($todate,0,2)."-01";
           // die($fromdate."dsd".$todate);
            $country=$this->input->post("country");
            $deleted=$this->sales_model->remove_all_data($sales_type,$fromdate,$todate,$country);
            if($deleted){
              $this->session->set_flashdata('success', lang('sales_successfully_deleted'));
            } else{
                $this->session->set_flashdata('error', lang('sales_were_not_deleted')); 
            }
            redirect($_SERVER["HTTP_REFERER"]);
        }
        
        $this->data['users'] = $this->reports_model->getStaff();
		 //$this->data['clusters']=  $this->cluster_model->getClusters();
        
		$this->data['currencies']=  $this->site->getAllCurrencies();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
         $this->data['categories'] = $this->site->getAllCategories();
		$this->data['clusters'] =$this->site->getAllClusters();
		 $this->data['companies']=$this->companies_model->getAllCustomerCompanies();
		 $this->data['sanoficustomer']=$this->companies_model->getAllCustomerCustomers();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('sales_report')));
        $meta = array('page_title' => lang('sales_report'), 'bc' => $bc);
        $this->page_construct('reports/sales', $meta, $this->data);
    }
    
    
    
       function mashariki_report()
    {
 // $this->site->checkModulePermission('mashariki_report');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
		$this->data['tm']=  $this->site->getAllTeams();
		$this->data['bu']=  $this->site->getAllBu();
		$this->data['currencies']=  $this->site->getAllCurrencies();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['clusters'] =$this->site->getAllClusters();
			 $this->data['companies']=$this->companies_model->getAllCustomerCompanies();
    $this->data['customers']=$this->companies_model->getAllCustomerCustomers();
			  $this->data['categories'] = $this->site->getAllCategories();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('mashariki report')));
        $meta = array('page_title' => lang('mashariki report'), 'bc' => $bc);
        $this->page_construct('reports/mashariki_report', $meta, $this->data);
    }
    
    function mashariki_rpt()
    {
        $this->site->checkModulePermission('mashariki_report');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
		$this->data['tm']=  $this->site->getAllTeams();
		$this->data['bu']=  $this->site->getAllBu();
		$this->data['currencies']=  $this->site->getAllCurrencies();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['clusters'] =$this->site->getAllClusters();
			 $this->data['companies']=$this->companies_model->getAllCustomerCompanies();
    $this->data['customers']=$this->companies_model->getAllCustomerCustomers();
			  $this->data['categories'] = $this->site->getAllCategories();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('mashariki report')));
        $meta = array('page_title' => lang('mashariki report'), 'bc' => $bc);
        $this->page_construct('reports/mashariki_rpt', $meta, $this->data);
    }
    
    
        function customer_details()
    {
        $this->sma->checkPermissions('sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
		 //$this->data['clusters']=  $this->cluster_model->getClusters();
		$this->data['clusters'] =$this->site->getAllCustomers1();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customer report')));
        $meta = array('page_title' => lang('customer report'), 'bc' => $bc);
        $this->page_construct('reports/customer_details', $meta, $this->data);
    }



function getSalesReport($pdf = NULL, $xls = NULL)
    {
         
        $this->sma->checkPermissions('sales', TRUE);
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
         if ($this->input->get('netgross')) {
            $grossnet = $this->input->get('netgross');
        } else {
            $grossnet = NULL;
        }
		if ($this->input->get('type')) {
            $salestype = $this->input->get('type');
        } else {
            $salestype = NULL;
        }
		//print_r($_GET['cluster']);
		//die();
		if ($this->input->get('cluster')) {
            $cluster = $_GET['cluster'];
			//print_r($cluster);
	//die();
        } else {
            $cluster = NULL;
        }
        
        if ($this->input->get('gbu')) {
            $gbu = $_GET['gbu'];
			//print_r($cluster);
	//die();
        } else {
            $gbu = NULL;
        }

		if ($this->input->get('country')) {
            $country = $this->input->get('country');
        } else {
            $country = NULL;
        }
		
		//echo $salestype;
		//die();
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('PSOdist')) {
            $PSOdist = $this->input->get('PSOdist');
        } else {
            $PSOdist = NULL;
        }
        if ($this->input->get('customer1')) {
            $customer1 = $this->input->get('customer1');
        } else {
            $customer1 = NULL;
        }
        
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
         if ($this->input->get('category')) {
            $category = $this->input->get('category');
        } else {
            $category = NULL;
        }
        if ($this->input->get('serial')) {
            $serial = $this->input->get('serial');
        } else {
            $serial = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld('01/'.$start_date);
            $end_date = $this->sma->fld('31/'.$end_date);
        }
        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {
 $this->db

                
                   ->select("sma_sales.id,sales_type,DATE_FORMAT(sma_sales.date,'%m-%Y') as month,country,gbu,distributor,customer, products,brand,IF(".$this->db->dbprefix('sales') . ".promotion=1,'P','NP') As promotion,quantity_units,value")
            ->from("sma_sales");
                //->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
                  // ->join('products', 'sales.product_id=products.id', 'left')
                  // ->join('categories', 'categories.id=sma_products.category_id', 'left');
				
$now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          if($start_date==""){
			 // $this->db->where('sales.created_by', $user);
$this->db->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
//$this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . $yesterday . '" and "' . $now . '"');
//            if ($user)  {
//                $this->db->where('sales.created_by', $user);
//            }
      
			if ($PSOdist) {
		$selectedOption=rtrim($_GET['PSOdist'],",");
        $this->db->where('sales.distributor_id IN ('.$selectedOption.')');
                  
            }
            
            	if ($gbu && $gbu!="ALL") {
		
        $this->db->where('sales.gbu IN ('.$gbu.')');
                  
            }
            if ($customer1) {
		$selectedOption1=rtrim($_GET['customer1'],",");
        $this->db->where('sales.customer_id IN ('.$selectedOption1.')');
                  
            }
            
            if ($salestype) {
			    
			     $this->db->where('sales.sales_type', $salestype);   
			    
            }		
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->datatables->where('sales.sales_cluster', $cluster);
          //  }
			if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->db->where('sales.country_id IN ('.$selectedOption.')');
	
            }			
            if ($product) {
                $this->db->like('sales.product_id', $product);
            }
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if($category){
                $selectedcategory=rtrim($_GET['category'],",");
        $this->db->where('sales.brand_id IN ('.$selectedcategory.')');
                       }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
             if ($grossnet) {
                $this->db->where('sales.movement_code',$grossnet);
            }
            if ($warehouse) {
               $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
                $this->db->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
				
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('sales_type'));
                 $this->excel->getActiveSheet()->SetCellValue('B1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('country'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('BU'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('distributor'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Brand'));
                 $this->excel->getActiveSheet()->SetCellValue('H1', lang('Promotion'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('Product'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('qty'));
				$this->excel->getActiveSheet()->SetCellValue('K1', lang('Value'));
				//$this->excel->getActiveSheet()->SetCellValue('L1', lang('Tender'));
                //$this->excel->getActiveSheet()->SetCellValue('L1', lang('Source'));
               // $this->excel->getActiveSheet()->SetCellValue('J1', lang('Value_at_Resale_Price'));
			//	$this->excel->getActiveSheet()->SetCellValue('K1', lang('Value_at_Tender_Price'));


                $row = 2;
				$qty = 0;
                $total = 0;
                $paid = 0;
                $balance = 0;
				$tender =0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->sales_type);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->month);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->country);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->gbu);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->distributor);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->brand);
                     $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->promotion);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->products);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->quantity_units);
					$this->excel->getActiveSheet()->SetCellValue('K' . $row, round($data_row->value/1000,5));
					//$this->excel->getActiveSheet()->SetCellValue('L' . $row, round($data_row->tender_price/1000,5));
                    //$this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->source);
                  //  $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->shipping);
                 //   $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->tender_price);
					
                    $qty += $data_row->quantity_units;
					$total += $data_row->value;
                    $paid += $data_row->shipping;
				//	$tender += $data_row->tender_price;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("I" . $row . ":K" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
				 $this->excel->getActiveSheet()->SetCellValue('J' . $row, $qty);	
                $this->excel->getActiveSheet()->SetCellValue('K' . $row, round($total/1000,5));
              //  $this->excel->getActiveSheet()->SetCellValue('L' . $row, round($tender/1000,5));

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				//$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
			//	$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
			
                $filename = 'sales_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
          
           
          $this->datatables
                ->select("sma_sales.id,DATE_FORMAT(sma_sales.date,'%m-%Y') as month,sales_type,country,sma_categories.gbu,distributor,customer,sma_categories.name,IF(".$this->db->dbprefix('sales') . ".promotion=1,'P','NP') As promotion,products,IF(sma_sales.movement_code ='VE',quantity_units,'0') as quantity_units ,(value/1000) as value,CONCAT('<a href=\'sales/edit/',sma_sales.id,'\'><i class=\"fa fa-edit\"></a>') as link ,CONCAT('<a href=\'sales/delete/',sma_sales.id,'\'><i class=\"fa fa-trash-o\"></a>') as deletelink")
            ->from("sma_sales")
                   ->join('products', 'sales.product_id=products.id', 'left')
                   ->join('categories', 'categories.id=sma_products.category_id', 'left')
               ->group_by('sales.id');
               //               //

               //->order_by('month desc');
         //  ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("Delete") . "' href='" . site_url('sales/delete/sma_sales.id') . "'><span class='label label-primary'>" . lang("Delete") . "</span></a></div>", "id");

           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          if($start_date==""){
    //$this->datatables->where('DATE_FORMAT(sma_sales.date,"%Y") = "'.date('Y').'" ');       
//$this->datatables->where('DATE_FORMAT(sma_sales.date,"%m") = "'.date('m', strtotime("+1 month")).'" ');
$this->datatables->where('DATE_FORMAT(sma_sales.date,"%m-%Y") = "'.date('m-Y',strtotime("-1 month")).'" ');
//->limit(1000);

            }
          // $this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . date('m', strtotime("-1 month")) . '" and "' . date('m', strtotime("+1 month")) . '"');
//$this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . $yesterday . '" and "' . $now . '"');
//            if ($user)  {
//                $this->datatables->where('sales.created_by', $user);
//            }
			
			if ($salestype) {
			    
			     $this->datatables->where('sales.sales_type', $salestype);   
			   
            }	
            if ($gbu && $gbu!="ALL") {
			    
			     $this->datatables->where('sales.gbu', $gbu);   
			   
            }	
            
            if ($PSOdist) {
			$selectedOption=rtrim($_GET['PSOdist'],",");
        $this->db->where('sales.distributor_id IN ('.$selectedOption.')');
                    // $this->db->where('sma_sales.distributor_id ='.$PSOdist.'');  
            }
             if ($customer1) {
		$selectedOption1=rtrim($_GET['customer1'],",");
        $this->db->where('sales.customer_id IN ('.$selectedOption1.')');
                  
            }
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->datatables->where('sales.sales_cluster', $cluster);
          //  }
			if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->datatables->where('sales.country_id IN ('.$selectedOption.')');
	
            }			
            if ($product) {
                $this->datatables->like('sales.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('sale_items.serial_no', $serial);
            }
           
            if($category){
                $selectedcategory=rtrim($_GET['category'],",");
        $this->db->where('products.category_id IN ('.$selectedcategory.')');
                       }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
             if ($grossnet) {
                $this->datatables->where('sales.movement_code',$grossnet);
            }
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
                $this->datatables->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }

            echo $this->datatables->generate();

        }

    }
    
    
function getMasharikiReport($pdf = NULL, $xls = NULL)
    {
        
        $this->sma->checkPermissions('sales', TRUE);
       
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
		if ($this->input->get('type')) {
            $salestype = $this->input->get('type');
        } else {
            $salestype = NULL;
        }
		//print_r($_GET['cluster']);
		//die();
		if ($this->input->get('cluster')) {
            $cluster = $_GET['cluster'];
			//print_r($cluster);
	//die();
        } else {
            $cluster = NULL;
        }

		if ($this->input->get('country')) {
            $country = $this->input->get('country');
        } else {
            $country = NULL;
        }
           if ($this->input->get('category')) {
            $category = $this->input->get('category');
        } else {
            $category = NULL;
        }
		
		//echo $salestype;
		//die();
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
         if ($this->input->get('customer1')) {
            $customer1 = $this->input->get('customer1');
        } else {
            $customer1 = NULL;
        }
         if ($this->input->get('salecolumn')) {
            $salecolumn = $this->input->get('salecolumn');
        } else {
            $salecolumn = NULL;
        }
        if ($this->input->get('stockcolumn')) {
            $stockcolumn = $this->input->get('stockcolumn');
        } else {
            $stockcolumn = NULL;
        }
        if ($this->input->get('budgetcolumn')) {
            $budgetcolumn = $this->input->get('budgetcolumn');
        } else {
            $budgetcolumn = NULL;
        }
        if ($this->input->get('PSOdist')) {
            $PSOdist = $this->input->get('PSOdist');
        } else {
            $PSOdist = NULL;
        }
         if ($this->input->get('PSOdist1')) {
            $PSOdist1 = $this->input->get('PSOdist1');
        } else {
            $PSOdist1 = NULL;
        }
         if ($this->input->get('bus_unit')) {
            $bus_unit = $this->input->get('bus_unit');
        } else {
            $bus_unit = NULL;
        }
          if ($this->input->get('team')) {
            $team = $this->input->get('team');
        } else {
            $team = NULL;
            
        } if ($this->input->get('products')) {
            $products = $this->input->get('products');
        } else {
            $products = NULL;
        }
        
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
         if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }

        if ($this->input->get('serial')) {
            $serial = $this->input->get('serial');
        } else {
            $serial = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld('01/'.$start_date);
            $end_date = $this->sma->fld('31/'.$this->input->get('end_date'));
        }
        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {
            if(empty($this->input->get('start_date')) || empty($this->input->get('end_date'))){
         $this->session->set_flashdata('error', $this->lang->line("Please_select_period"));
            redirect($_SERVER["HTTP_REFERER"]);
    }
            if($start_date){
          $slaesdate = " WHERE sma_sales.date BETWEEN '".$start_date."' and '".$end_date."' ";
          $stckdate = " WHERE sma_purchases.date BETWEEN '".$start_date."' and '".$end_date."' ";
          $bdgtdate = " WHERE sma_budget.date BETWEEN '".$start_date."' and '".$end_date."' ";
            }
            if ($country) {
				$selectedOption=rtrim($_GET['country'],",");
	    $slecntry = " AND sma_sales.country_id  IN  ($selectedOption) ";
	     $stckcntry = " AND sma_purchases.country_id  IN  ($selectedOption) ";
	      $bdgtcntry = " AND sma_budget.country  IN  ($selectedOption) ";
            }	
            if($bus_unit){
             $bus_unit = " AND sma_products.business_unit  = '".$bus_unit."' ";
	     
            }
            if($category){
            $qry_category = " AND sma_products.category_id  = '".$category."' ";
            }
            
            if($products){
        $selectedOption3=rtrim($_GET['products'],",");
          $qry_products = " AND sma_products.id  IN  ($selectedOption3) ";
                }
                
             if ($customer) {
		$selectedOption1=rtrim($_GET['customer'],",");
		$sales_dist = " AND sma_sales.distributor_id  IN  ($selectedOption1) ";
        $stck_dist = " AND sma_purchases.supplier_id  IN  ($selectedOption1) ";
        $bdgt_dist = " AND sma_budget.distributor_id  IN  ($selectedOption1) ";       
            }   
            if ($customer1){
         $selectedOption3=rtrim($_GET['customer1'],",");
       $sales_cust = " AND sma_sales.customer_id  IN  ($selectedOption3) ";
        $bdgt_cust = " AND sma_budget.customer_id  IN  ($selectedOption3) ";         
            }
           $sp = " SELECT DATE_FORMAT(sma_purchases.date,'%m-%Y') as date,sma_purchases.country,sma_products.id as product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (stock_type='SSO',sma_purchases.supplier,'') AS distibutr,IF(stock_type='PSO',sma_purchases.supplier,'') AS supplier,'0' AS ssoqty,'0' AS ssovalue,
        SUM(IF(stock_type='SSO',sma_purchase_items.quantity,'0')) AS StockQTY,
        SUM(IF(stock_type='SSO',sma_purchase_items.shipping,'0')) AS StockValue,'0' as PSOBudgetQTY,'0' as PSOBudgetValue,'0' as SSOBudgetQTY,'0' as SSOBudgetValue, '' as msr_alignment_name,'' as team_name
        FROM `sma_products`
        JOIN sma_purchase_items ON sma_purchase_items.product_id=sma_products.id
           LEFT JOIN sma_purchases ON sma_purchase_items.purchase_id=sma_purchases.id 
           LEFT JOIN sma_categories ON sma_categories.id=sma_products.category_id
           $stckdate $stckcntry $bus_unit $qry_category  $qry_products $stck_dist
           ";
   
    
           
         $pp = " SELECT DATE_FORMAT(sma_budget.date,'%m-%Y') as date,sma_currencies.country,sma_products.id as product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (sma_budget.scenario='PSO',sma_budget.distributor_name,'') AS distibutr,IF(sma_budget.scenario='SSO',sma_customers.name,'') AS customer,'0' AS ssoqty,'0' AS ssovalue, '0' AS StockQTY,'0' AS StockValue,IF(sma_budget.scenario='PSO',budget_qty,'0') as PSOBudgetQTY, IF(sma_budget.scenario='PSO',budget_value,'0') as PSOBudgetValue,IF(sma_budget.scenario='SSO',budget_qty,'0') as SSOBudgetQTY, IF(sma_budget.scenario='SSO',budget_value,'0') as SSOBudgetValue,'' as msr_alignment_name,'' as team_name   
        FROM `sma_budget` 
        LEFT JOIN sma_products ON sma_budget.product_id=sma_products.id
        LEFT JOIN sma_categories ON sma_categories.id=sma_products.category_id
        LEFT JOIN sma_currencies ON sma_currencies.id = sma_budget.country 
        LEFT JOIN sma_customers ON sma_customers.id = sma_budget.customer_id
        $bdgtdate $bdgtcntry $bus_unit $qry_category  $qry_products $bdgt_dist
         $bdgt_cust
         ";
        
           $salesP = " SELECT DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,sma_sales.country,sma_products.id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,sma_sales.distributor,sma_sales.customer,IF(sales_type='SSO',sma_sales.quantity_units,'0') AS ssoqty,IF(sales_type='SSO',sma_sales.total,'0') AS ssovalue,
         '0' as StockQTY,
         '0' as StockValue,
         '0' as PSOBudgetQTY,
         '0' as PSOBudgetValue,
         '0' as SSOBudgetQTY,
         '0' as SSOBudgetValue,
         sma_sales.msr_alignment_name,
         sma_msr_alignments.team_name
        FROM `sma_products` 
        LEFT JOIN sma_sales ON sma_sales.product_id=sma_products.id
        LEFT JOIN sma_categories ON sma_categories.id=sma_products.category_id
        LEFT JOIN sma_msr_alignments ON sma_sales.msr_alignment_id = sma_msr_alignments.id 
        $slaesdate $slecntry $bus_unit $qry_category  $qry_products $sales_dist
         $sales_cust
         ";
           
 $q = $this->db
->query("select * from ($salesP UNION $pp UNION $sp) as unionTable"); 


				
$now=date("Y-m-d");


          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));

			    if($PSOdist){
			        $selectedOption=rtrim($_GET['PSOdist'],",");
                     $this->db->where('sma_sales.distributor_id IN ('.$selectedOption.')');
                }
                
                 if($PSOdist1){
			        $selectedOption=rtrim($_GET['PSOdist1'],",");
                     $this->db->where('sma_sales.customer IN ('.$selectedOption.')');
                }
                if($bus_unit){
                     $selectedOption2=rtrim($_GET['bus_unit'],",");
                     $this->db->where('sma_products.business_unit IN ('.$selectedOption2.')');
                }
                   if($team){
                     $selectedOption4=rtrim($_GET['team'],",");
                     $this->db->where('sma_sales_team_alignments.team_name IN ('.$selectedOption4.')');
                }
                if($products){
                     $selectedOption3=rtrim($_GET['products'],",");
                     $this->db->where('sma_products.id IN ('.$selectedOption3.')');
                }
               
                if ($category) {
                $this->datatables->like('sma_products.category_id', $category);
            }
            	
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->datatables->where('sales.sales_cluster', $cluster);
          //  }
			if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->db->where('sales.country_id IN ('.$selectedOption.')');
	
            }			
           
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
             
            if ($warehouse) {
               $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            
          
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }
           
            if (!empty($data)) {
				
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('Month/Year'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('country'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('business_unit'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Producct_name'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('distributor'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('SSO_customer'));
				$this->excel->getActiveSheet()->SetCellValue('H1', lang('SSO Qty)'));
				$this->excel->getActiveSheet()->SetCellValue('I1', lang('SSO Value(Euros)'));
				$this->excel->getActiveSheet()->SetCellValue('J1', lang('StockQTY'));
				$this->excel->getActiveSheet()->SetCellValue('K1', lang('StockValue'));
				$this->excel->getActiveSheet()->SetCellValue('L1', lang('PSOBudgetQTY'));
				$this->excel->getActiveSheet()->SetCellValue('M1', lang('PSOBudgetValue'));
				$this->excel->getActiveSheet()->SetCellValue('N1', lang('SSOBudgetQTY'));
				$this->excel->getActiveSheet()->SetCellValue('O1', lang('SSOBudgetValue'));
			    $this->excel->getActiveSheet()->SetCellValue('P1', lang('MSR Alignment'));
			    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Team Name'));
            
                $row = 2;
				$ssoqty = 0;
                $ssovalue = 0;
                $StockQTY = 0;
                $StockValue = 0;
				$PSOBudgetQTY =0;
	            $PSOBudgetValue =0;
	            $SSOBudgetQTY =0;
	            $SSOBudgetValue =0;
	            
	            
	            
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->date);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->country);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->business_unit);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->brand);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->distributor);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->customer);
					$this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->ssoqty);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row,round($data_row->ssovalue/1000,5));
                 $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->StockQTY);
                 $this->excel->getActiveSheet()->SetCellValue('K' . $row, round($data_row->StockValue/1000,5));
                 $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->PSOBudgetQTY);
                 $this->excel->getActiveSheet()->SetCellValue('M' . $row, round($data_row->PSOBudgetValue/1000,5));
                 $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->SSOBudgetQTY);
                 $this->excel->getActiveSheet()->SetCellValue('O' . $row, round($data_row->SSOBudgetValue/1000,5));
                 $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->msr_alignment_name);
                  $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->team_name);  
                    $ssoqty +=$data_row->ssoqty;
                $ssovalue +=round($data_row->ssovalue/1000,5);
                $StockQTY +=$data_row->StockQTY;
                $StockValue +=round($data_row->StockValue/1000,5);
				$PSOBudgetQTY +=$data_row->PSOBudgetQTY;
				$PSOBudgetValue +=round($data_row->PSOBudgetValue/1000,5);
				$SSOBudgetQTY +=$data_row->SSOBudgetQTY;
	            $SSOBudgetValue +=round($data_row->SSOBudgetValue/1000,5);
				
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("H" . $row . ":P" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
				 $this->excel->getActiveSheet()->SetCellValue('H' . $row, $ssoqty);	
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $ssovalue);
                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $StockQTY);
				$this->excel->getActiveSheet()->SetCellValue('K' . $row, $StockValue);
                $this->excel->getActiveSheet()->SetCellValue('L' . $row, $PSOBudgetQTY);
                $this->excel->getActiveSheet()->SetCellValue('M' . $row, $PSOBudgetValue);
                $this->excel->getActiveSheet()->SetCellValue('N' . $row, $SSOBudgetQTY);
                $this->excel->getActiveSheet()->SetCellValue('O' . $row, $SSOBudgetValue);

                
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $Hiddensales = explode(',',$salecolumn);
if (in_array(3, $Hiddensales)) {
  $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
} else {
  $this->excel->getActiveSheet()->removeColumn('H');
}
if (in_array(4, $Hiddensales)) {
 $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
} else {
   $this->excel->getActiveSheet()->removeColumn('I');
}
if($stockcolumn){
               $Hiddenstocks = explode(',',$stockcolumn);
    if (in_array(1, $Hiddenstocks)) {
   $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
} else {
   $this->excel->getActiveSheet()->removeColumn('J');
}     
 if (in_array(2, $Hiddenstocks)) {
  $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20); 
} else {
  	$this->excel->getActiveSheet()->removeColumn('K');  
} 
}  else {
  	$this->excel->getActiveSheet()->removeColumn('K');
  	$this->excel->getActiveSheet()->removeColumn('J');
}             
                if($budgetcolumn){
               
				$Hiddenbudgets = explode(',',$budgetcolumn);
    if (in_array(1, $Hiddenbudgets)) {
   $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
} else {
   $this->excel->getActiveSheet()->removeColumn('L');
}     
 if (in_array(2, $Hiddenbudgets)) {
 $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
} else {
  	$this->excel->getActiveSheet()->removeColumn('M');  
} 
if (in_array(3, $Hiddenbudgets)) {
 	$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
} else {
  	$this->excel->getActiveSheet()->removeColumn('N');  
} 
if (in_array(4, $Hiddenbudgets)) {
 	$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
} else {
  	$this->excel->getActiveSheet()->removeColumn('O');  
}
                }else{
                  $this->excel->getActiveSheet()->removeColumn('L');
				$this->excel->getActiveSheet()->removeColumn('M');
				$this->excel->getActiveSheet()->removeColumn('N');
				$this->excel->getActiveSheet()->removeColumn('O');   
                }
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20); 
			
			
                $filename = 'Consolidated_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
          
   
          $this->datatables
         ->select("date,gmid,sales_type,month,country,distributor,customer")
         ->from ("sma_sales")  
		->group_by('sma_sales.id');
		$now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
		if($start_date==""){
$this->db->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
   
           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
    
		
                
          
            
        
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
                $this->datatables->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
               //  $this->db->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            
          
            echo $this->datatables->generate();

        }

    }
       
    
function getMasharikiRpt($pdf = NULL, $xls = NULL)
    {
        
        $this->sma->checkPermissions('sales', TRUE);
       

		if ($this->input->get('rtype')) {
            $reportype = $this->input->get('rtype');
        } else {
            $reportype = NULL;
        }

        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
         if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }

        if ($start_date) {
            $start_date = $this->sma->fld('01/'.$start_date);
            $end_date = $this->sma->fld('31/'.$this->input->get('end_date'));
        }
        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }
        
        if ($this->input->get('country')) {
            $country = $this->input->get('country');
        } else {
            $country = NULL;
        }

        if ($this->input->get('bus_unit')) {
            $bus_unit = $this->input->get('bus_unit');
        } else {
            $bus_unit = NULL;
        }
        
         if ($this->input->get('net_gross')) {
             $net_gross = $this->input->get('net_gross');
            // die($net_gross."fdfd");
             if($net_gross=="G" || $net_gross=="VE"  ){$netgrosscode="VE";} elseif($net_gross=="TN"){$netgrosscode="TN";} elseif($net_gross=="NT"){$netgrosscode="NT";}
        } else {
            $net_gross = NULL;
            $netgrosscode=NULL;
        }
       
        
        
        
        
        if ($pdf || $xls) {
            if(empty($this->input->get('start_date')) || empty($this->input->get('end_date'))){
         $this->session->set_flashdata('error', $this->lang->line("Please_select_period"));
            redirect($_SERVER["HTTP_REFERER"]);
    }
     if($start_date){
          $slaesdate = " WHERE sma_sales.date BETWEEN '".$start_date."' and '".$end_date."' ";
          $stckdate = " WHERE sma_purchases.date BETWEEN '".$start_date."' and '".$end_date."' ";
          $bdgtdate = " WHERE sma_budget.date BETWEEN '".$start_date."' and '".$end_date."' ";
            }
         //country and bu filter
         $salesadddition="";$stockadddition="";$budgetaddition="";
          if ($country) {
			
				$selectedOption=rtrim($_GET['country'],",");
               $salesadddition.=' AND sma_sales.country_id IN ('.$selectedOption.')';
               $stockadddition.=' WHERE sma_purchases.country_id IN ('.$selectedOption.')';
               $budgetaddition.=' AND sma_budget.country IN ('.$selectedOption.')';
	
            }
            
            if ($bus_unit) {
			
               $salesadddition.='AND sma_sales.gbu="'.$bus_unit.'"';
                $stockadddition.='AND sma_purchases.gbu="'.$bus_unit.'"';
                $budgetaddition.=' AND sma_budget.business_unit IN ('.$selectedOption.')';
	//no budget
            }
            
            //die($reportype);
            	
            //ssosales
            if($reportype == 'ssosales' && $netgrosscode !="TN"){
        $sp = " SELECT DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,sma_sales.country,product_id,gbu, brand,products,sma_sales.distributor,sma_sales.customer,sma_sales.quantity_units  AS ssoqty,sma_sales.total AS ssovalue,sma_sales.msr_alignment_name,
         sma_msr_alignments.team_name,sma_sales.id as saleid
        FROM `sma_sales` 
       
        LEFT JOIN sma_msr_alignments ON sma_sales.msr_alignment_id = sma_msr_alignments.id 
$slaesdate AND sma_sales.sales_type = 'SSO' AND sma_sales.movement_code='$netgrosscode'  $salesadddition ";
          
          //Group by sma_sales.country,sma_sales.customer,sma_sales.distributor_id,sma_sales.product_id
           
           
            }
            
            else if($reportype == 'ssosales' && $netgrosscode =="TN"){
                  $sp = " SELECT DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,sma_sales.country,product_id,gbu, brand,products,sma_sales.distributor,sma_sales.customer,sma_sales.quantity_units  AS ssoqty,sma_sales.tender_price AS ssovalue,sma_sales.msr_alignment_name,
         sma_msr_alignments.team_name,sma_sales.id as saleid
        FROM `sma_sales` 
       
        LEFT JOIN sma_msr_alignments ON sma_sales.msr_alignment_id = sma_msr_alignments.id 
$slaesdate AND sma_sales.sales_type = 'SSO' AND sma_sales.movement_code='$netgrosscode'  $salesadddition ";
               
            }
            //psosales
            else if($reportype == 'psosales'){
          $sp = " SELECT DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,sma_sales.country,product_id,gbu,brand,products,sma_sales.distributor,sma_sales.customer,IF(sales_type='PSO',sma_sales.quantity_units,'0') AS psoqty,
        IF(sales_type='PSO',sma_sales.total,'0') AS psovalue,
         sma_sales.msr_alignment_name,
         sma_msr_alignments.team_name
        FROM `sma_sales` 
        
        LEFT JOIN sma_msr_alignments ON sma_sales.msr_alignment_id = sma_msr_alignments.id 
$slaesdate AND sma_sales.sales_type = 'PSO'  $salesadddition
           ";      
          
///Group by sma_sales.customer,sma_sales.distributor_id,sma_products.id
          //AND sma_sales.movement_code='$netgrosscode'
            }
            else if($reportype == 'stock'){
         $sp = " SELECT DATE_FORMAT(sma_purchases.date,'%m-%Y') as date,sma_purchases.country, product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (stock_type='SSO',sma_purchases.supplier,'') AS distributor,IF(stock_type='PSO',sma_purchases.supplier,'') AS supplier,
        (IF(stock_type='SSO',sma_purchase_items.quantity,'0')) AS StockQTY,
        (IF(stock_type='SSO',sma_purchase_items.shipping,'0')) AS StockValue
        FROM `sma_purchases`
        JOIN sma_purchase_items ON sma_purchase_items.purchase_id=sma_purchases.id 
           LEFT JOIN sma_products ON  sma_purchase_items.product_id=sma_products.id
           LEFT JOIN sma_categories ON sma_categories.id=sma_products.category_id
$stckdate $stockaddition
Group by sma_purchases.supplier_id,sma_purchase_items.product_id
           ";        
            }else if($reportype == 'psobudget'){
                     
         $sp = " SELECT DATE_FORMAT(sma_budget.date,'%m-%Y') as date,sma_currencies.country,sma_products.id as product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (sma_budget.scenario='PSO',sma_budget.distributor_name,'') AS distributor,
         IF(sma_budget.scenario='PSO',budget_qty,'0') as PSOBudgetQTY, IF(sma_budget.scenario='PSO',budget_value,'0') as PSOBudgetValue,'' as msr_alignment_name,'' as team_name   
        FROM `sma_budget` 
        LEFT JOIN sma_products ON sma_budget.product_id=sma_products.id
        LEFT JOIN sma_categories ON sma_categories.id=sma_products.category_id
        LEFT JOIN sma_currencies ON sma_currencies.id = sma_budget.country $bdgtdate AND sma_budget.scenario = 'PSO' AND sma_budget.net_gross='$net_gross' $budgetaddition";  //;
//Group by sma_budget.distributor_id,sma_budget.product_id
            }else if($reportype == 'ssobudget'){  $sp = " SELECT DATE_FORMAT(sma_budget.date,'%m-%Y') as date,sma_currencies.country,sma_products.id as product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (sma_budget.scenario='PSO',sma_budget.distributor_name,'') AS distributor,IF(sma_budget.scenario='SSO',sma_customers.name,'') AS customer,
         IF(sma_budget.scenario='SSO',budget_qty,'0') as SSOBudgetQTY, IF(sma_budget.scenario='SSO',budget_value,'0') as SSOBudgetValue,sma_budget.msr_alignment_name,
         sma_msr_alignments.team_name 
        FROM `sma_budget` 
        LEFT JOIN sma_products ON sma_budget.product_id=sma_products.id
        LEFT JOIN sma_categories ON sma_categories.id=sma_products.category_id
        LEFT JOIN sma_currencies ON sma_currencies.id = sma_budget.country 
        LEFT JOIN sma_customers ON sma_customers.id = sma_budget.customer_id
        LEFT JOIN sma_msr_alignments ON sma_budget.msr_alignment_id = sma_msr_alignments.id 
$bdgtdate AND sma_budget.scenario = 'SSO' AND sma_budget.net_gross='$net_gross' $budgetaddition"; 
         //Group by sma_budget.customer_id,sma_budget.distributor_id,sma_budget.product_id
            }
     
  //  $sp.=" AND";       
 $q = $this->db
->query($sp); 
 //$q = $this->db->get();

				
$now=date("Y-m-d");


          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));

			    
                
            	
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->datatables->where('sales.sales_cluster', $cluster);
          //  }
			
           
            
            
            
          
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }
           
            if (!empty($data)) {
				
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('Month/Year'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('Country'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('Business_unit'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('Brand'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Product_name'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Distributor'));
                if($reportype == 'ssosales'){

                $this->excel->getActiveSheet()->SetCellValue('G1', lang('SSO_customer'));
				$this->excel->getActiveSheet()->SetCellValue('H1', lang('SSO (Qty)'));
				$this->excel->getActiveSheet()->SetCellValue('I1', lang('SSO Value(KEuro)'));
				///$this->excel->getActiveSheet()->SetCellValue('J1', lang('SSO Net Value(KEuro)'));
				//$this->excel->getActiveSheet()->SetCellValue('J1', lang('PSO Qty'));
				//$this->excel->getActiveSheet()->SetCellValue('K1', lang('PSO Value(Euros)'));
				//$this->excel->getActiveSheet()->SetCellValue('L1', lang('StockQTY'));
				//$this->excel->getActiveSheet()->SetCellValue('M1', lang('StockValue'));
				//$this->excel->getActiveSheet()->SetCellValue('N1', lang('PSOBudgetQTY'));
			//	$this->excel->getActiveSheet()->SetCellValue('O1', lang('PSOBudgetValue'));
				//$this->excel->getActiveSheet()->SetCellValue('P1', lang('SSOBudgetQTY'));
			//	$this->excel->getActiveSheet()->SetCellValue('Q1', lang('SSOBudgetValue'));
			    $this->excel->getActiveSheet()->SetCellValue('K1', lang('MSR Alignment'));
			    $this->excel->getActiveSheet()->SetCellValue('L1', lang('Team Name'));
                } else if($reportype == 'psosales'){
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('PSO Qty'));
				$this->excel->getActiveSheet()->SetCellValue('H1', lang('PSO Value(KEuro)'));
		
                }else if($reportype == 'stock'){
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('StockQTY'));
				$this->excel->getActiveSheet()->SetCellValue('H1', lang('StockValue'));
		
                }else if($reportype == 'psobudget'){
                  $this->excel->getActiveSheet()->SetCellValue('G1', lang('PSOBudgetQTY'));
				$this->excel->getActiveSheet()->SetCellValue('H1', lang('PSOBudgetValue'));  
                }
                else if($reportype == 'ssobudget'){
                     $this->excel->getActiveSheet()->SetCellValue('G1', lang('SSO_customer'));
                     $this->excel->getActiveSheet()->SetCellValue('H1', lang('SSOBudgetQTY'));
				$this->excel->getActiveSheet()->SetCellValue('I1', lang('SSOBudgetValue')); 
				$this->excel->getActiveSheet()->SetCellValue('J1', lang('MSR Alignment')); 
				$this->excel->getActiveSheet()->SetCellValue('K1', lang('Team Name')); 
                }
                $row = 2;
				$ssoqty = 0;
                $ssovalue = 0;
                $StockQTY = 0;
                $StockValue = 0;
				$PSOBudgetQTY =0;
	            $PSOBudgetValue =0;
	            $SSOBudgetQTY =0;
	            $SSOBudgetValue =0;
	            $pu=0;
	            $pv = 0;
	            $nsso =0;
	            $nssototoal =0;
                foreach ($data as $data_row) {
                     $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->date);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->country);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->gbu);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->brand);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->products);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->distributor);
                    if($reportype == 'ssosales'){
                      ///$pu = $data_row->ssoqty - $data_row->tenderqty;
                      //$pv = $pu*$data_row->ssovalue;
                     // $nsso = ($data_row->ssoqty - $data_row->tenderqty) * ($data_row->ssovalue/$data_row->ssoqty) + $data_row->tendervalue;
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->customer);
					$this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->ssoqty);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, round($data_row->ssovalue/1000,5));
                   // $this->excel->getActiveSheet()->SetCellValue('J' . $row, round($nsso/1000,5) );
                   // $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->psovalue);
                // $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->StockQTY);
                // $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->StockValue);
                // $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->PSOBudgetQTY);
               //  $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->PSOBudgetValue);
                // $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->SSOBudgetQTY);
               //  $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->SSOBudgetValue);
                 $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->msr_alignment_name);
                $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->team_name);  
                    }
                    else if($reportype == 'psosales'){
                      $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->psoqty);
                   $this->excel->getActiveSheet()->SetCellValue('H' . $row, round($data_row->psovalue/1000,5));
                   
                    }else if($reportype == 'stock'){
                      $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->StockQTY);
                   $this->excel->getActiveSheet()->SetCellValue('H' . $row, round($data_row->StockValue/1000,5));
                   
                    }else if($reportype == 'psobudget'){
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->PSOBudgetQTY);
                   $this->excel->getActiveSheet()->SetCellValue('H' . $row, round($data_row->PSOBudgetValue/1000,5));

                    }
                    else if($reportype == 'ssobudget'){
                         $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->customer);
				 $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->SSOBudgetQTY);
                   $this->excel->getActiveSheet()->SetCellValue('I' . $row, round($data_row->SSOBudgetValue/1000,5));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->msr_alignment_name);
                   $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->team_name);
                }
                    $ssoqty +=$data_row->ssoqty;
                $ssovalue +=round($data_row->ssovalue/1000,5);
                $psoqty +=$data_row->psoqty;
                $psovalue +=round($data_row->psovalue/1000,5);
                $StockQTY +=$data_row->StockQTY;
                $StockValue +=round($data_row->StockValue/1000,5);
				$PSOBudgetQTY +=$data_row->PSOBudgetQTY;
				$PSOBudgetValue +=round($data_row->PSOBudgetValue/1000,5);
				$SSOBudgetQTY +=$data_row->SSOBudgetQTY;
	            $SSOBudgetValue +=round($data_row->SSOBudgetValue/1000,5);
				$nssototoal +=$nsso;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("H" . $row . ":P" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                    if($reportype == 'ssosales'){
				 $this->excel->getActiveSheet()->SetCellValue('H' . $row, $ssoqty);	
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $ssovalue);
                 $this->excel->getActiveSheet()->SetCellValue('J' . $row, round($nssototoal/1000,5));
                    }
                    else if($reportype == 'psosales'){
                   $this->excel->getActiveSheet()->SetCellValue('G' . $row, $psoqty);	
               $this->excel->getActiveSheet()->SetCellValue('H' . $row, $psovalue);   
                    }
                    else if($reportype == 'stock'){
                      $this->excel->getActiveSheet()->SetCellValue('G' . $row, $StockQTY);
                   $this->excel->getActiveSheet()->SetCellValue('H' . $row, $StockValue);
                   
                    }
                    else if($reportype == 'psobudget'){
                      $this->excel->getActiveSheet()->SetCellValue('G' . $row, $PSOBudgetQTY);
                   $this->excel->getActiveSheet()->SetCellValue('H' . $row, $PSOBudgetValue);
       
                    }
                     else if($reportype == 'ssobudget'){
                      
                      $this->excel->getActiveSheet()->SetCellValue('H' . $row, $SSOBudgetQTY);
                   $this->excel->getActiveSheet()->SetCellValue('I' . $row, $SSOBudgetValue);
   
                     }
                // $this->excel->getActiveSheet()->SetCellValue('J' . $row, $psoqty);	
                //$this->excel->getActiveSheet()->SetCellValue('K' . $row, $psovalue);
                //$this->excel->getActiveSheet()->SetCellValue('L' . $row, $StockQTY);
				//$this->excel->getActiveSheet()->SetCellValue('M' . $row, $StockValue);
                //$this->excel->getActiveSheet()->SetCellValue('N' . $row, $PSOBudgetQTY);
                //$this->excel->getActiveSheet()->SetCellValue('O' . $row, $PSOBudgetValue);
                //$this->excel->getActiveSheet()->SetCellValue('P' . $row, $SSOBudgetQTY);
               // $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $SSOBudgetValue);

                
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                if($reportype == 'ssosales'){

                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                //$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
               // $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                          }
                else if($reportype == 'psosales'){
                 $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
               
                }
                else if($reportype == 'stock'){
                 $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
               
                }
                 else if($reportype == 'psobudget'){
                 $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
               
                }
                
			if($reportype == 'ssosales'){
                $filename = 'ssosales_report';
			} 
			else if($reportype == 'psosales'){
			    $filename = 'psosales_report';
			} 
			else if($reportype == 'stock'){
			    $filename = 'stock_report';
			} 
			else if($reportype == 'psobudget'){
			    $filename = 'psobudget';
			}
			else {
			  	$filename = 'Mashariki_report_'.$filename;
			} 
		
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } 
        
       //datatables 
        else {

            $this->load->library('datatables');
       if($reportype == 'ssosales'){   
           if($net_gross=="G"){
   $this->datatables
->select("sma_sales.id, DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,'SS0 Sales' as rpe,sma_sales.country,gbu as business_unit,brand,products,sma_sales.distributor,sma_sales.customer,(IF(movement_code='VE',sma_sales.quantity_units,'0')) AS ssoqty,(sma_sales.total/1000) AS ssovalue")
         ->where("movement_code","VE");  
   } else if($net_gross=="TN"){
 $this->datatables
->select("sma_sales.id, DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,'SS0 Sales' as rpe,sma_sales.country,gbu as business_unit,brand,products,sma_sales.distributor,sma_sales.customer,(IF(movement_code='TN',sma_sales.quantity_units,'0')) AS ssoqty,(sma_sales.tender_price/1000) AS ssovalue")             
 ->where("movement_code","TN");          
 
           }
           else if($net_gross=="NT"){
            $this->datatables
->select("sma_sales.id, DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,'SS0 Sales' as rpe,sma_sales.country,gbu as business_unit,brand,products,sma_sales.distributor,sma_sales.customer,(IF(movement_code='NT',sma_sales.quantity_units,'0')) AS ssoqty,(sma_sales.total/1000) AS ssovalue")
                    ->where("movement_code","NT");
            
           }
           /****sso saleseprt**********/
            $this->datatables
->from ("sma_sales") 

->join('sma_msr_alignments', 'sma_sales.msr_alignment_id=sma_msr_alignments.id', 'left')
->where('sma_sales.sales_type = "SSO" ');
//->group_by('sma_sales.customer,sma_sales.distributor_id');
     
      $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
		if($start_date==""){
$this->datatables->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
   
           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          	if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
               $this->datatables->where('sales.country_id IN ('.$selectedOption.')');
	
            }
            
            if ($bus_unit) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sales.gbu="'.$bus_unit.'"');
	
            }
            
             if ($netgrosscode) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sma_sales.movement_code="'.$netgrosscode.'"');
	
            }
            
           if ($start_date && $end_date) {
                $this->datatables->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }
                  
            
            
            
           }
        else if($reportype == 'psosales'){
      $this->datatables
->select("sma_sales.id, DATE_FORMAT(sma_sales.date,'%m-%Y') as date ,'PS0 Sales' as rpe,sma_sales.country,sma_categories.gbu as business_unit,sma_categories.name as brand,sma_products.name,sma_sales.distributor,sma_sales.customer,IF(sales_type='PSO',sma_sales.quantity_units,'0') AS psoqty,IF(sales_type='PSO',sma_sales.total,'0') AS psovalue")
->from ("sma_sales") 
->join('sma_products', 'sma_sales.product_id=sma_products.id', 'left')
->join('sma_categories', 'sma_categories.id=sma_products.category_id', 'left')
->join('sma_msr_alignments', 'sma_sales.msr_alignment_id=sma_msr_alignments.id', 'left')
->where('sma_sales.sales_type = "PSO" ');
//->group_by('sma_sales.customer,sma_sales.distributor_id,sma_products.id');     
      //filters
      $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
		if($start_date==""){
$this->db->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
   
           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          	if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->datatables->where('sales.country_id IN ('.$selectedOption.')');
	
            }
            
            if ($bus_unit) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sales.gbu="'.$bus_unit.'"');
	
            }
            
           if ($start_date && $end_date) {
                $this->datatables->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }
            
      
      
      
      
                    }
        else if($reportype == 'ssobudget' || $reportype=="" ){  
            
       
            
             $this->datatables
->select("sma_budget.id, DATE_FORMAT(sma_budget.date,'%m-%Y') as date,sma_currencies.country,sma_products.id as product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (sma_budget.scenario='PSO',sma_budget.distributor_name,'') AS distributor,IF(sma_budget.scenario='SSO',sma_customers.name,'') AS customer,
         IF(sma_budget.scenario='SSO',budget_qty,'0') as SSOBudgetQTY, IF(sma_budget.scenario='SSO',budget_value,'0') as SSOBudgetValue,sma_budget.msr_alignment_name,
         sma_msr_alignments.team_name")
->from ("sma_budget") 
->join('sma_products', 'sma_budget.product_id=sma_products.id', 'left')
->join('sma_categories', 'sma_categories.id=sma_products.category_id', 'left')
 ->join('sma_currencies', 'sma_currencies.id = sma_budget.country', 'left')
 ->join('sma_customers', 'sma_customers.id = sma_budget.customer_id', 'left')
->join('sma_msr_alignments', 'sma_budget.msr_alignment_id = sma_msr_alignments.id', 'left')
->where('sma_budget.scenario = "SSO" ');
//->group_by('sma_budget.customer_id,sma_budget.distributor_id,sma_budget.product_id');
             
             $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
		if($start_date==""){
$this->datatables->where('DATE_FORMAT(sma_budget.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
   
           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          	if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->datatables->where('sma_budget.country IN ('.$selectedOption.')');
	
            }
            
            if ($bus_unit) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sma_budget.business_unit="'.$bus_unit.'"');
	
            }
            if ($net_gross) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sma_budget.net_gross="'.$net_gross.'"');
	
            }
            
           if ($start_date && $end_date) {
                $this->datatables->where('sma_budget.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }
            
        }
        
         else if($reportype == 'psobudget'){  
            
       
            
             $this->datatables
->select("sma_budget.id, DATE_FORMAT(sma_budget.date,'%m-%Y') as date,sma_currencies.country,sma_products.id as product_id,sma_products.business_unit,sma_categories.name as brand,sma_products.name,IF (sma_budget.scenario='PSO',sma_budget.distributor_name,'') AS distributor,IF(sma_budget.scenario='SSO',sma_customers.name,'') AS customer,
         IF(sma_budget.scenario='PSO',budget_qty,'0') as SSOBudgetQTY, IF(sma_budget.scenario='PSO',budget_value,'0') as SSOBudgetValue,sma_budget.msr_alignment_name,
         sma_msr_alignments.team_name")
->from ("sma_budget") 
->join('sma_products', 'sma_budget.product_id=sma_products.id', 'left')
->join('sma_categories', 'sma_categories.id=sma_products.category_id', 'left')
 ->join('sma_currencies', 'sma_currencies.id = sma_budget.country', 'left')
 ->join('sma_customers', 'sma_customers.id = sma_budget.customer_id', 'left')
->join('sma_msr_alignments', 'sma_budget.msr_alignment_id = sma_msr_alignments.id', 'left')
->where('sma_budget.scenario = "PSO" ');
//->group_by('sma_budget.customer_id,sma_budget.distributor_id,sma_budget.product_id');
             
             $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
		if($start_date==""){
$this->datatables->where('DATE_FORMAT(sma_budget.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
   
           $now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
          	if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
               $this->datatables->where('sma_budget.country IN ('.$selectedOption.')');
	
            }
            
            if ($bus_unit) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sma_budget.business_unit="'.$bus_unit.'"');
	
            }
            
            if ($net_gross) {
				//die(print_r($_POST['country']));
				//die();
				
                $this->datatables->where('sma_budget.net_gross="'.$net_gross.'"');
	
            }
            
           if ($start_date && $end_date) {
                $this->datatables->where('sma_budget.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }
            
        }
        
        
        
        
        
        
		
          
            echo $this->datatables->generate();

        }

    }
           
    
    
    function getCustomersReport($pdf = NULL, $xls = NULL)
    {
        
        $this->sma->checkPermissions('sales', TRUE);
        
		if ($this->input->get('cluster')) {
            $cluster = $_GET['cluster'];
			//print_r($cluster);
	//die();
        } else {
            $cluster = NULL;
        }

        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

 $this->db
                ->select("sf_alignment_name as msr")  //paid_by,transaction_id
                ->from('customer_alignments')
		->join('sma_sales_team_alignments', 'customer_alignments.sf_alignment_name=sma_sales_team_alignments.sf_name', 'left')
			->group_by('sf_name');
			
			
							
$now=date("Y-m-d");
          $yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ($now) ) ));
        
      if($start_date==""){
			 // $this->db->where('sales.created_by', $user);
$this->db->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $yesterday . '" and "' . $now . '"');
            }
            
            if ($reference_no) {
                $this->db->like('sales.refe  rence_no', $reference_no, 'both');
            }
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
                $this->db->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
				
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('sales_type'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('cluster'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('country'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('movement'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('distributor'));
				$this->excel->getActiveSheet()->SetCellValue('G1', lang('Business_Unit'));
                                 $this->excel->getActiveSheet()->SetCellValue('H1', lang('Brand'));
                                $this->excel->getActiveSheet()->SetCellValue('I1', lang('Product Gmid'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Product'));
				$this->excel->getActiveSheet()->SetCellValue('K1', lang('qty'));
                $this->excel->getActiveSheet()->SetCellValue('L1', lang('Value_at_Unified_Price'));
                $this->excel->getActiveSheet()->SetCellValue('M1', lang('Value_at_Resale_Price'));
				$this->excel->getActiveSheet()->SetCellValue('N1', lang('Value_at_Tender_Price'));
				$this->excel->getActiveSheet()->SetCellValue('O1', lang('Discount'));

                $row = 2;
				$qty = 0;
                $total = 0;
                $paid = 0;
                $balance = 0;
				$tender =0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->date);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->sales_type);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sales_cluster);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->sales_region);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->movement_name);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
					$this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->business_unit);
                                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->name);
                                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->product_code);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->iname);
					 $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->quantity);
					$this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->shipping);
                    $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->tender_sale);
					$this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->total_discount);
                    $qty += $data_row->quantity;
					$total += $data_row->total;
                    $paid += $data_row->shipping;
					$tender += $data_row->tender_sale;
                    $balance += $data_row->total_discount;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
				 $this->excel->getActiveSheet()->SetCellValue('I' . $row, $qty);	
                $this->excel->getActiveSheet()->SetCellValue('L' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('M' . $row, $paid);
				$this->excel->getActiveSheet()->SetCellValue('N' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('O' . $row, $tender);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                                $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
                                $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
                $filename = 'sales_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
          
           
          $this->datatables
                ->select("customers.name,customer_alignments.sf_alignment_name, sma_sales_team_alignments.sf_name, customer_alignments.products,sma_sales_team_alignments.dm_alignment, sma_sales_team_alignments.dm_name")  //paid_by,transaction_id
                ->from('customer_alignments')
		->join('sma_sales_team_alignments', 'customer_alignments.sf_alignment_name=sma_sales_team_alignments.sf_alignment', 'left')
		->join('customers', 'customer_alignments.customer_name=customers.name', 'left');
		
    
          if ($cluster)  {
                $this->datatables->where('customers.id', $cluster);
            }
		
			//if ($cluster) {
				//print_r($cluster);
				//echo implode( ", ", $cluster );
				//die();
              //  $this->datatables->where('sales.sales_cluster', $cluster);
          //  }
			if ($country) {
				//die(print_r($_POST['country']));
				//die();
				$selectedOption=rtrim($_GET['country'],",");
                $this->datatables->where('sales.country_id IN ('.$selectedOption.')');
	
            }			
            if ($product) {
                $this->datatables->like('sale_items.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date && $end_date) {
             //  die($start_date."dds".$end_date);
                $this->datatables->where('sma_sales.date BETWEEN "'.$start_date.'" and "'.$end_date.'"');
            }

            echo $this->datatables->generate();

        }

    }


    function getQuotesReport($pdf = NULL, $xls = NULL)
    {

        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('quote_items') . ".product_name, ' (', " . $this->db->dbprefix('quote_items') . ".quantity, ')') SEPARATOR '<br>') as iname, grand_total, status", FALSE)
                ->from('quotes')
                ->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            
            if ($product) {
                $this->db->like('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->db->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('quotes').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('quotes_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $filename = 'quotes_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('quote_items') . ".product_name, '__', " . $this->db->dbprefix('quote_items') . ".quantity) SEPARATOR '___') as iname, grand_total, status", FALSE)
                ->from('quotes')
                ->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            
            if ($product) {
                $this->datatables->like('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->datatables->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('quotes').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    function getTransfersReport($pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('transfers') . ".date, transfer_no, (CASE WHEN " . $this->db->dbprefix('transfers') . ".status = 'completed' THEN  GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '<br>') ELSE GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('transfer_items') . ".product_name, ' (', " . $this->db->dbprefix('transfer_items') . ".quantity, ')') SEPARATOR '<br>') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, " . $this->db->dbprefix('transfers') . ".status")
                ->from('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')
                ->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')
                ->group_by('transfers.id')->order_by('transfers.date desc');
            if ($product) {
                $this->db->where($this->db->dbprefix('purchase_items') . ".product_id", $product);
                $this->db->or_where($this->db->dbprefix('transfer_items') . ".product_id", $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('transfers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('transfer_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('warehouse') . ' (' . lang('from') . ')');
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('warehouse') . ' (' . lang('to') . ')');
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->transfer_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->fname . ' (' . $data_row->fcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->tname . ' (' . $data_row->tcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $filename = 'transfers_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('transfers') . ".date, transfer_no, (CASE WHEN " . $this->db->dbprefix('transfers') . ".status = 'completed' THEN  GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, '__', " . $this->db->dbprefix('purchase_items') . ".quantity) SEPARATOR '___') ELSE GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('transfer_items') . ".product_name, '__', " . $this->db->dbprefix('transfer_items') . ".quantity) SEPARATOR '___') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, " . $this->db->dbprefix('transfers') . ".status", FALSE)
                ->from('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')
                ->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')
                ->group_by('transfers.id');
            if ($product) {
                $this->datatables->where($this->db->dbprefix('purchase_items') . ".product_id", $product);
                $this->datatables->or_where($this->db->dbprefix('transfer_items') . ".product_id", $product);
            }
            $this->datatables->edit_column("fname", "$1 ($2)", "fname, fcode")
                ->edit_column("tname", "$1 ($2)", "tname, tcode")
                ->unset_column('fcode')
                ->unset_column('tcode');
            echo $this->datatables->generate();

        }

    }

    function getReturnsReport($pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('return_sales') . ".date as date, " . $this->db->dbprefix('return_sales') . ".reference_no as ref, " . $this->db->dbprefix('sales') . ".reference_no as sal_ref, " . $this->db->dbprefix('return_sales') . ".biller, " . $this->db->dbprefix('return_sales') . ".customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('return_items') . ".product_name, ' (', " . $this->db->dbprefix('return_items') . ".quantity, ')') SEPARATOR '<br>') as iname, " . $this->db->dbprefix('return_sales') . ".surcharge, " . $this->db->dbprefix('return_sales') . ".grand_total, " . $this->db->dbprefix('return_sales') . ".id as id", FALSE)
                ->join('sales', 'sales.id=return_sales.sale_id', 'left')
                ->from('return_sales')
                ->join('return_items', 'return_items.return_id=return_sales.id', 'left')
                ->group_by('return_sales.id')->order_by('return_sales.date desc');
            if ($product) {
                $this->db->like($this->db->dbprefix('return_items') . ".product_id", $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_return_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_ref'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sal_ref);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->surcharge);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->grand_total);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $filename = 'sales_return_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('F2:F' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('return_sales') . ".date as date, " . $this->db->dbprefix('return_sales') . ".reference_no as ref, " . $this->db->dbprefix('sales') . ".reference_no as sal_ref, " . $this->db->dbprefix('return_sales') . ".biller, " . $this->db->dbprefix('return_sales') . ".customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('return_items') . ".product_name, '__', " . $this->db->dbprefix('return_items') . ".quantity) SEPARATOR '___') as iname, " . $this->db->dbprefix('return_sales') . ".surcharge, " . $this->db->dbprefix('return_sales') . ".grand_total, " . $this->db->dbprefix('return_sales') . ".id as id", FALSE)
                ->join('sales', 'sales.id=return_sales.sale_id', 'left')
                ->from('return_sales')
                ->join('return_items', 'return_items.return_id=return_sales.id', 'left')
                ->group_by('return_sales.id');
            //->where('return_sales.warehouse_id', $warehouse_id);
            if ($product) {
                $this->datatables->like($this->db->dbprefix('return_items') . ".product_id", $product);
            }

            echo $this->datatables->generate();

        }

    }

    function purchases()
    {
        $this->sma->checkPermissions('index',true,'purchases');
        $this->load->model('companies_model');
        $this->load->model('cluster_model');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['currencies']=  $this->site->getAllCurrencies();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['companies']=$this->companies_model->getAllCustomerCompanies();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('purchases_report')));
        $meta = array('page_title' => lang('purchases_report'), 'bc' => $bc);
        $this->page_construct('reports/purchases', $meta, $this->data);
    }

    function getPurchasesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('index',true,'purchases');
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('supplier')) {
            $supplier = $this->input->get('supplier');
        } else {
            $supplier = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('country')) {
            $country = $this->input->get('country');
        } else {
            $country = NULL;
        }
        if ($this->input->get('category')) {
            $category = $this->input->get('category');
        } else {
            $category = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld('01/'.$start_date);
            $end_date = $this->sma->fld('31/'.$end_date);
            
        }
        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("sma_purchases.created as date,
                sma_purchases.country,purchase_items.product_code as gmid,vehicles.plate_no,gbu,
                IF(".$this->db->dbprefix('purchases') . ".promotion=1,'P','NP') As promotion, "
                    . $this->db->dbprefix('warehouses') . ".name as wname, sma_purchases.supplier,"
                    . $this->db->dbprefix('purchase_items') . ".quantity ,purchase_items.shipping,", FALSE)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('vehicles', 'purchases.vehicle_id=vehicles.id', 'left')
                    // ->join('products pr','pr.id=purchase_items.product_id','left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
              
                ->group_by('purchases.id')
                ->order_by('purchases.date desc');

           
            if ($product) {
                $this->db->like('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($country) {
                $this->db->where('purchases.country_id ', $country);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if($category){
                $selectedcategory=rtrim($_GET['category'],",");
        $this->db->where('products.category_id IN ('.$selectedcategory.')');
                       }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('purchase_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('Country'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('GMID'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('SKU'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('BU'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Promotion'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Value'));
                

                $row = 2;
                $total = 0;
                $paid = 0;
                $quantitytotal = 0;
                $totalshipping=0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->date);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->country);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->gmid);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->product_name);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->gbu);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->promotion);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->wname);
                      $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->supplier);
                      $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, round($data_row->shipping/1000,5));
                    
                    $quantitytotal+= $data_row->quantity;
                    $totalshipping+=$data_row->shipping;
                    
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("H" . $row . ":I" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                     $this->excel->getActiveSheet()->SetCellValue('H' . $row, $quantitytotal);
                      $this->excel->getActiveSheet()->SetCellValue('I' . $row, $totalshipping);
               

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
                $filename = 'Stock_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("" . $this->db->dbprefix('purchases') . ".id,sma_purchases.created as date,sma_purchases.supplier,sma_vehicles.plate_no, " . $this->db->dbprefix('purchases') . ".quantity ,CONCAT('<a href=\'purchases/edit/',sma_purchases.id,'\'><i class=\"fa fa-edit\"></a>' ) as link", FALSE)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('products pr','pr.id=purchase_items.product_id','left')
                ->join('vehicles', 'purchases.vehicle_id=vehicles.id', 'left')
                // ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
				->join('companies', 'companies.id=purchases.supplier_id', 'left')
                ->group_by('purchases.id');
                 if($start_date==""){
                 $this->datatables->where('DATE_FORMAT(sma_purchases.date,"%Y") = "'.date('Y').'" ');
            }



            if ($product) {
                $this->datatables->like('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($country) {
                $this->db->where('purchases.country_id ', $country);
            }
            if($category){
                $selectedcategory=rtrim($_GET['category'],",");
                $this->db->where('products.category_id IN ('.$selectedcategory.')');
                       }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            
       

            echo $this->datatables->generate();

        }

    }

    function payments()
    {
        $this->sma->checkPermissions('payments');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('payments_report')));
        $meta = array('page_title' => lang('payments_report'), 'bc' => $bc);
        $this->page_construct('reports/payments', $meta, $this->data);
    }

    function getPaymentsReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('payments', TRUE);
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('supplier')) {
            $supplier = $this->input->get('supplier');
        } else {
            $supplier = NULL;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('payment_ref')) {
            $payment_ref = $this->input->get('payment_ref');
        } else {
            $payment_ref = NULL;
        }
        if ($this->input->get('sale_ref')) {
            $sale_ref = $this->input->get('sale_ref');
        } else {
            $sale_ref = NULL;
        }
        if ($this->input->get('purchase_ref')) {
            $purchase_ref = $this->input->get('purchase_ref');
        } else {
            $purchase_ref = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fsd($start_date);
            $end_date = $this->sma->fsd($end_date);
        }
        if (!$this->Owner && !$this->Admin) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {

            $this->db
                ->select("" . $this->db->dbprefix('payments') . ".date, " . $this->db->dbprefix('payments') . ".reference_no as payment_ref, " . $this->db->dbprefix('sales') . ".reference_no as sale_ref, " . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                ->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id')
                ->order_by('payments.date desc');

            if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->db->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('payments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('payment_reference'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('type'));

                $row = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->type);
                    if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $filename = 'payments_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('payments') . ".date, " . $this->db->dbprefix('payments') . ".reference_no as payment_ref, " . $this->db->dbprefix('sales') . ".reference_no as sale_ref, " . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                ->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id');

            if ($user) {
                $this->datatables->where('payments.created_by', $user);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->datatables->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($sale_ref) {
                $this->datatables->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->datatables->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    function customers()
    {
        $this->sma->checkPermissions('customers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customers_report')));
        $meta = array('page_title' => lang('customers_report'), 'bc' => $bc);
        $this->page_construct('reports/customers', $meta, $this->data);
    }

    function getCustomers($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('customers', TRUE);

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(" . $this->db->dbprefix('sales') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount,  ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('sales', 'sales.distributor_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('customers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_sales'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatNumber($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatMoney($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $filename = 'customers_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(" . $this->db->dbprefix('sales') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('sales', 'sales.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->group_by('companies.id')
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . site_url('reports/customer_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
                ->unset_column('id');
            echo $this->datatables->generate();

        }

    }

    function customer_report($user_id = NULL,$pdf=NULL)
    {
        
        $this->sma->checkPermissions('customers', TRUE);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_customer_selected"));
            redirect('reports/customers');
        }
         $this->load->model('companies_model');
$sales=$this->reports_model->getCustomerDueSales($user_id);
//die(print_r($sales));
        $this->data['sales'] =$sales;
        $this->data['total_sales'] = $this->reports_model->getCustomerSales($user_id);
        $this->data['total_quotes'] = $this->reports_model->getCustomerQuotes($user_id);
        $this->data['total_returns'] = $this->reports_model->getCustomerReturns($user_id);
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
     $company=$this->companies_model->getCompanyByID($user_id);
     
        $thirtydaybalance=$this->reports_model->getAgeingCustomer($user_id);
       $bal= $thirtydaybalance[0]["balance"];
        if($bal<1) 
        
        $html='<table width="98%" border="1" style="margin-left:10px;border-collapse:collapse">
            <tr><th width="10%"><img src="' . site_url() . 'assets/uploads/logos/logo3.png" alt="' . SITE_NAME . '"  /><u> </th><th style="text-align:center" colspan="7">CUSTOMER STATEMENT:'.strtoupper($company->name).'</u>'.' as of '.date("d/m/Y").'</th></tr>
                <tr><td>#</td><td>Date</td><td>Invoice/Ref No</td><td>Document</td><td>Invoice Amount</td><td>Paid Amount</td><td>Balance</td><td>Payment Status</td></tr>
';
        $count=1;
        $totalbill=0;$totalpaid=0;
        foreach ($sales as $value) { 
            $totalbill+=$value->total;
            $totalpaid+=$value->paid;
           // $saleitems=$this->site->getAllSaleItems($value->id);
$html.='<tr>
    <td class="contentDetails">'.$count.'</td><td class="contentDetails">'.date("d/m/Y H:i",  strtotime($value->date)).'</td><td class="contentDetails">'.$value->reference_no.'</td> <td>Invoice';

        $html.='</td><td class="contentDetails" style="text-align:right">'.round($value->total,2).'</td><td style="text-align:right">'.round($value->paid,2).'</td><td style="text-align:right">'.round($value->total-$value->paid,2).'</td><td class="contentDetails">'.$value->payment_status.'</td>
  </tr>';
$count++;
            }
            $html.="<tr><td><b>TOTALS</b></td><td></td><td></td><td></td><td style='text-align:right'><b>".number_format($totalbill)."</b></td><td style='text-align:right'><b>".number_format($totalpaid)."</b></td><td style='text-align:right'><b>".number_format($totalbill-$totalpaid)."</b></td><td></td></tr>";

$html.='</table><br>'
        . '<b>&nbsp;&nbsp;Ageing Balances</b><br>'
        . '&nbsp;&nbsp;30 days ='.$bal."<br>"
. '&nbsp;&nbsp;60 days ='.$bal."<br>"
. '&nbsp;&nbsp;90 days ='.$bal;

  $mpdf=new \mPDF('c','A4-L','','' , 0 , 0 , 0 , 0 , 0 , 0); 
  $mpdf->SetMargins(0,0,10);
  $mpdf->SetLeftMargin(30);
 
//$mpdf->SetDisplayMode('fullpage');
 
$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
 
$mpdf->WriteHTML($html);


$mpdf->Output('receipt.pdf','I');

       
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customers_report')));
        $meta = array('page_title' => lang('customers_report'), 'bc' => $bc);
        $this->page_construct('reports/customer_report', $meta, $this->data);

    }

    function suppliers()
    {
        $this->sma->checkPermissions('suppliers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('suppliers_report')));
        $meta = array('page_title' => lang('suppliers_report'), 'bc' => $bc);
        $this->page_construct('reports/suppliers', $meta, $this->data);
    }

    function getSuppliers($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('suppliers', TRUE);

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(purchases.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('purchases', 'purchases.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('suppliers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_purchases'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatNumber($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatMoney($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $filename = 'suppliers_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(" . $this->db->dbprefix('purchases') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('purchases', 'purchases.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->group_by('companies.id')
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . site_url('reports/supplier_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
                ->unset_column('id');
            echo $this->datatables->generate();

        }

    }


    function supplier_report($user_id = NULL)
    {
        $this->sma->checkPermissions('suppliers', TRUE);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_supplier_selected"));
            redirect('reports/suppliers');
        }

        $this->data['purchases'] = $this->reports_model->getPurchasesTotals($user_id);
        $this->data['total_purchases'] = $this->reports_model->getSupplierPurchases($user_id);
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('suppliers_report')));
        $meta = array('page_title' => lang('suppliers_report'), 'bc' => $bc);
        $this->page_construct('reports/supplier_report', $meta, $this->data);

    }

    function users()
    {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('staff_report')));
        $meta = array('page_title' => lang('staff_report'), 'bc' => $bc);
        $this->page_construct('reports/users', $meta, $this->data);
    }

    function getUsers()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('users').".id as id, first_name, last_name, email, company, ".$this->db->dbprefix('groups').".name, active")
            ->from("users")
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', NULL);
        if (!$this->Owner) {
            $this->datatables->where('group_id !=', 1);
        }
        $this->datatables
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . site_url('reports/staff_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
            ->unset_column('id');
        echo $this->datatables->generate();
    }

    function staff_report($user_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $cal = 0)
    {

        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_user_selected"));
            redirect('reports/users');
        }
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['purchases'] = $this->reports_model->getStaffPurchases($user_id);
        $this->data['sales'] = $this->reports_model->getStaffSales($user_id);
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        if (!$year) {
            $year = date('Y');
        }
        if (!$month || $month == '#monthly-con') {
            $month = date('m');
        }
        if ($pdf) {
            if ($cal) {
                $this->monthly_sales($year, $pdf, $user_id);
            } else {
                $this->daily_sales($year, $month, $pdf, $user_id);
            }
        }
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => site_url('reports/staff_report/'.$user_id),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
		{heading_row_start}<tr>{/heading_row_start}
		{heading_previous_cell}<th class="text-center"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
		{heading_title_cell}<th class="text-center" colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
		{heading_next_cell}<th class="text-center"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
		{heading_row_end}</tr>{/heading_row_end}
		{week_row_start}<tr>{/week_row_start}
		{week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
		{week_row_end}</tr>{/week_row_end}
		{cal_row_start}<tr class="days">{/cal_row_start}
		{cal_cell_start}<td class="day">{/cal_cell_start}
		{cal_cell_content}
		<div class="day_num">{day}</div>
		<div class="content">{content}</div>
		{/cal_cell_content}
		{cal_cell_content_today}
		<div class="day_num highlight">{day}</div>
		<div class="content">{content}</div>
		{/cal_cell_content_today}
		{cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
		{cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
		{cal_cell_blank}&nbsp;{/cal_cell_blank}
		{cal_cell_end}</td>{/cal_cell_end}
		{cal_row_end}</tr>{/cal_row_end}
		{table_close}</table>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $this->reports_model->getStaffDailySales($user_id, $year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }
        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        if ($this->input->get('pdf')) {

        }
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['msales'] = $this->reports_model->getStaffMonthlySales($user_id, $year);
        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('staff_report')));
        $meta = array('page_title' => lang('staff_report'), 'bc' => $bc);
        $this->page_construct('reports/staff_report', $meta, $this->data);

    }

    

    function getUserLogins($id = NULL, $pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('login_start_date')) {
            $login_start_date = $this->input->get('login_start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('login_end_date')) {
            $login_end_date = $this->input->get('login_end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        if ($pdf || $xls) {

            $this->db
                ->select("login, ip_address, time")
                ->from("user_logins")
                ->where('user_id', $id)
                ->order_by('time desc');
            if ($login_start_date) {
                $this->datatables->where('time BETWEEN "' . $login_start_date . '" and "' . $login_end_date . '"', FALSE);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('staff_login_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('ip_address'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('time'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->login);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->ip_address);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrld($data_row->time));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);

                $filename = 'staff_login_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("login, ip_address, time")
                ->from("user_logins")
                ->where('user_id', $id);
            if ($login_start_date) {
                $this->datatables->where('time BETWEEN "' . $login_start_date . '" and "' . $login_end_date . '"', FALSE);
            }
            echo $this->datatables->generate();

        }

    }

    function getCustomerLogins($id = NULL)
    {
        if ($this->input->get('login_start_date')) {
            $login_start_date = $this->input->get('login_start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('login_end_date')) {
            $login_end_date = $this->input->get('login_end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        $this->load->library('datatables');
        $this->datatables
            ->select("login, ip_address, time")
            ->from("user_logins")
            ->where('customer_id', $id);
        if ($login_start_date) {
            $this->datatables->where('time BETWEEN "' . $login_start_date . '" and "' . $login_end_date . '"');
        }
        echo $this->datatables->generate();
    }

    function profit_loss($start_date = NULL, $end_date = NULL)
    {
        $this->sma->checkPermissions('profit_loss');
        if (!$start_date) {
            $start = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['total_purchases'] = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales'] = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses'] = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['total_paid'] = $this->reports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received'] = $this->reports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash'] = $this->reports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc'] = $this->reports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->reports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp'] = $this->reports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->reports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned'] = $this->reports_model->getTotalReturnedAmount($start, $end);
        $this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('profit_loss')));
        $meta = array('page_title' => lang('profit_loss'), 'bc' => $bc);
        $this->page_construct('reports/profit_loss', $meta, $this->data);
    }

    function profit_loss_pdf($start_date = NULL, $end_date = NULL)
    {
        $this->sma->checkPermissions('profit_loss');
        if (!$start_date) {
            $start = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }

        $this->data['total_purchases'] = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales'] = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses'] = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['total_paid'] = $this->reports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received'] = $this->reports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash'] = $this->reports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc'] = $this->reports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->reports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp'] = $this->reports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->reports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned'] = $this->reports_model->getTotalReturnedAmount($start, $end);
        $this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);

        $html = $this->load->view($this->theme . 'reports/profit_loss_pdf', $this->data, true);
        $name = lang("profit_loss") . "-" . str_replace(array('-', ' ', ':'), '_', $this->data['start']) . "-" . str_replace(array('-', ' ', ':'), '_', $this->data['end']) . ".pdf";
        $this->sma->generate_pdf($html, $name, false, false, false, false, false, 'L');
    }

    function register()
    {
        $this->sma->checkPermissions('register');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('register_report')));
        $meta = array('page_title' => lang('register_report'), 'bc' => $bc);
        $this->page_construct('reports/register', $meta, $this->data);
    }

    function getRrgisterlogs($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('register', TRUE);
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' (', users.email, ')') as user, cash_in_hand, total_cc_slips, total_cheques, total_cash, total_cc_slips_submitted, total_cheques_submitted,total_cash_submitted, note", FALSE)
                ->from("pos_register")
                ->join('users', 'users.id=pos_register.user_id', 'left')
                ->order_by('date desc');
            //->where('status', 'close');

            if ($user) {
                $this->db->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('register_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('open_time'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('close_time'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('user'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('cash_in_hand'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('cc_slips'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('cheques'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('total_cash'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('cc_slips_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('cheques_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('total_cash_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('K1', lang('note'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->closed_at);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->user);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->cash_in_hand);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total_cc_slips);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->total_cheques);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->total_cash);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->total_cc_slips_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_cheques_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->total_cash_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->note);
                    if($data_row->total_cash_submitted < $data_row->total_cash || $data_row->total_cheques_submitted < $data_row->total_cheques || $data_row->total_cc_slips_submitted < $data_row->total_cc_slips) {
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray(
                                array( 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'F2DEDE')) )
                                );
                    }
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                $filename = 'register_report';
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                if ($pdf) {
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
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
                    $objWriter->save('php://output');
                    exit();
                }
                if ($xls) {
                    //$this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                    ob_clean();
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                    $objWriter->save('php://output');
                    exit();
                }

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, '<br>', " . $this->db->dbprefix('users') . ".email) as user, cash_in_hand, CONCAT(total_cc_slips, ' (', total_cc_slips_submitted, ')'), CONCAT(total_cheques, ' (', total_cheques_submitted, ')'), CONCAT(total_cash, ' (', total_cash_submitted, ')'), note", FALSE)
                ->from("pos_register")
                ->join('users', 'users.id=pos_register.user_id', 'left');

            if ($user) {
                $this->datatables->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    function routes()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['vehicles'] = $this->site->getAllVehicles();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Vehicles_Report')));
        $meta = array('page_title' => lang('Vehicles_Report'), 'bc' => $bc);
        $this->page_construct('reports/routes', $meta, $this->data);
    }

    function getRoutes()
    {
        $vehicle_id=$this->input->post('vehicle_ids');
        $day=$this->input->post('day_ids');

        $current_date = date("Y-m-d").' '.'23:59:00';
        // $day = $dayNo;
        // $vehicle_id = $id;
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
        sma_vehicles.id = $vehicle_id and sma_customers.active = 1 and sma_allocation_days.day = $day and sma_allocation_days.active = 1 and sma_vehicle_route.day = $day and 
        sma_allocation_days.expiry IS NULL or sma_allocation_days.expiry <= CURRENT_TIMESTAMP GROUP BY sma_shops.id ORDER BY sma_allocation_days.position ASC");
    
        $result=$query->result();

        echo json_encode($result);
        // $this->data["myroutes"] = $result;
        // $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('vehicle routes')));
        // $meta = array('page_title' => "My routes", 'bc' => $bc);
        // $this->page_construct('vehicles/test_test', $meta, $this->data);
        // echo $vehicle_id;
    }

}
