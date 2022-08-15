<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $limit = 5)
    {
        $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllProducts()
    {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductByID($id)
    {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getStockTakingHistory(){
        $this->db->select('sma_stock_taking_history.id, sma_companies.name, sma_vehicles.plate_no, sma_stock_taking_history.is_reversed,
         sma_stock_taking_history.vehicle_id, sma_stock_taking_history.salesman_id, sma_stock_taking_history.stock_taker_id,
         sma_stock_taking_history.total_short,sma_stock_taking_history.comments, sma_stock_taking_history.expected_stock,
         sma_stock_taking_history.current_stock, sma_stock_taking_history.differences, sma_stock_taking_history.created_at')
            ->join("sma_companies","sma_stock_taking_history.salesman_id=sma_companies.id","left")
            ->join("sma_vehicles","sma_stock_taking_history.vehicle_id=sma_vehicles.id","left")
            ->group_by('sma_stock_taking_history.id')
            ->order_by('sma_stock_taking_history.id', 'asc');
        $q = $this->db->get('sma_stock_taking_history');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStockTakingHistoryById($id){
        $this->db->select('sma_stock_taking_history.id, sma_companies.name, sma_vehicles.plate_no, sma_stock_taking_history.is_reversed,
         sma_stock_taking_history.distributor_id,sma_stock_taking_history.vehicle_id, sma_stock_taking_history.salesman_id, sma_stock_taking_history.stock_taker_id,
         sma_stock_taking_history.total_short,sma_stock_taking_history.comments, sma_stock_taking_history.expected_stock,
         sma_stock_taking_history.current_stock, sma_stock_taking_history.differences, sma_stock_taking_history.created_at')
            ->join("sma_companies","sma_stock_taking_history.salesman_id=sma_companies.id","left")
            ->join("sma_vehicles","sma_stock_taking_history.vehicle_id=sma_vehicles.id","left")
            ->group_by('sma_stock_taking_history.id')
            ->order_by('sma_stock_taking_history.id', 'asc');
        $q = $this->db->get_where('sma_stock_taking_history',array('sma_stock_taking_history.id'=>$id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteStockTakingHistory($id)
    {
        if ($this->db->delete('sma_stock_taking_history', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    public function updateStockTakingHistory($id,$data)
    {
        if ($this->db->update('sma_stock_taking_history', $data,array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getProductsByCode($code)
    {
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getProductByMercafarCode($code)
    {
        $q = $this->db->get_where('products', array('mercafar_gmid' =>trim($code)), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByName($name)
    {
        $q = $this->db->get_where('products', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateProductQuantity($product_id, $quantity, $warehouse_id, $product_cost)
    {
        if ($this->addQuantity($product_id, $warehouse_id, $quantity)) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function calculateAndUpdateQuantity($item_id, $product_id, $quantity, $warehouse_id, $product_cost)
    {
        if ($this->updatePrice($product_id, $product_cost) && $this->calculateAndAddQuantity($item_id, $product_id, $warehouse_id, $quantity)) {
            return true;
        }
        return false;
    }

    public function calculateAndAddQuantity($item_id, $product_id, $warehouse_id, $quantity)
    {

        if ($this->getProductQuantity($product_id, $warehouse_id)) {
            $quantity_details = $this->getProductQuantity($product_id, $warehouse_id);
            $product_quantity = $quantity_details['quantity'];
            $item_details = $this->getItemByID($item_id);
            $item_quantity = $item_details->quantity;
            $after_quantity = $product_quantity - $item_quantity;
            $new_quantity = $after_quantity + $quantity;
            if ($this->updateQuantity($product_id, $warehouse_id, $new_quantity)) {
                return TRUE;
            }
        } else {

            if ($this->insertQuantity($product_id, $warehouse_id, $quantity)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addQuantity($product_id, $warehouse_id, $quantity)
    {

        if ($this->getProductQuantity($product_id, $warehouse_id)) {
            $warehouse_quantity = $this->getProductQuantity($product_id, $warehouse_id);
            $old_quantity = $warehouse_quantity['quantity'];
            $new_quantity = $old_quantity + $quantity;

            if ($this->updateQuantity($product_id, $warehouse_id, $new_quantity)) {
                return TRUE;
            }
        } else {

            if ($this->insertQuantity($product_id, $warehouse_id, $quantity)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function insertQuantity($product_id, $warehouse_id, $quantity)
    {
        $productData = array(
            'product_id' => $product_id,
            'warehouse_id' => $warehouse_id,
            'quantity' => $quantity
        );
        if ($this->db->insert('warehouses_products', $productData)) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function updateQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->update('warehouses_products', array('quantity' => $quantity), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id))) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1);

        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }

        return FALSE;
    }

    public function updatePrice($id, $unit_cost)
    {

        if ($this->db->update('products', array('cost' => $unit_cost), array('id' => $id))) {
            return true;
        }

        return false;
    }

    public function getAllPurchases()
    {
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllPurchaseItems($purchase_id)
    {
        $this->db->select('purchase_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit,products.price as price, products.details as details, product_variants.name as variant')
            ->join('products', 'products.id=purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=purchase_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=purchase_items.tax_rate_id', 'left')
            ->group_by('purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchaseByID($id)
    {
        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getAllPurchaseDetailsByID($id)
    {
         $this->db ->select("purchases.id as id, purchases.date,pi.product_code,pi.country,pi.product_name,cr.name as categoryname,pi.quantity,pi.expiry,purchases.supplier,pi.shipping as grand_total")
                 ->join('purchase_items pi', 'pi.purchase_id=purchases.id','left')
                 ->join('products pr', 'pr.id=pi.product_id', 'left')
                 ->join('categories cr', 'cr.id=pr.category_id','left');
        $q = $this->db->get_where('purchases', array('purchases.id' => $id), 1);
        
           
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function resetProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getOverSoldCosting($product_id)
    {
        $q = $this->db->get_where('costing', array('overselling' => 1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addLPGGas($data){
        if ($this->db->insert('sma_lpg_purchases', $data)) {
            $q = $this->db->get_where('lpg_volume', array('id' => 1));
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $current_volume = $row->volume;
                }
                if ($this->db->update('sma_lpg_volume', array('volume' => $current_volume+$data['volume']), array('id' => 1))){
                    return true;
                }
                //return $data;
            }
        }
        return false;
    }
    
    public function getLPGGas(){
         $q = $this->db->get_where('lpg_volume', array('id' => 1), 1);
        if ($q->num_rows() > 0) {
            return $q->row()->volume;
        }
        return FALSE;
    }
    
    public function updateLPGGas($data){
        $q = $this->db->get_where('lpg_volume', array('id' => 1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $current_volume = $row->volume;
            }
            if ($this->db->update('sma_lpg_volume', array('volume' => $current_volume-$data['deduct_volume']), array('id' => 1))){
                return true;
            }
            //return $data;
        }
        return FALSE;
    }
    
    public function addPurchase($data, $items, $supplier_id)
    {
        //print_r($items);
        //die();
        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();
            if ($this->site->getReference('po') == $data['reference_no']) {
                $this->site->updateReference('po');
            }
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                $this->updateDistributorProductsQuantities($item,$supplier_id);
                $this->db->insert('purchase_items', $item);
                $this->db->update('products', array('cost' => $item['real_unit_cost']), array('id' => $item['product_id']));
               // if($item['option_id']) {
                  //  $this->db->update('product_variants', array('cost' => $item['real_unit_cost']), array('id' => $item['option_id'], 'product_id' => $item['product_id']));
             //   }
            }
          
//            if ($data['status'] == 'received') {
//                //post supplier invoice 
//                $this->postPurchaseInvoice($data);
//                $this->site->syncQuantity(NULL, $purchase_id);
//            }
            return true;
        }else{
        return false;
        }
    }

    public function addPurchase2($data, $items, $vehicle_id, $distributor_id)
    {
        //print_r($items);
        //die();
        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();
            if ($this->site->getReference('po') == $data['reference_no']) {
                $this->site->updateReference('po');
            }
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                $this->updateVehicleProductsQuantities($item,$distributor_id,$vehicle_id);
                $this->db->insert('purchase_items', $item);
                $this->db->update('products', array('cost' => $item['real_unit_cost']), array('id' => $item['product_id']));
                // if($item['option_id']) {
                //  $this->db->update('product_variants', array('cost' => $item['real_unit_cost']), array('id' => $item['option_id'], 'product_id' => $item['product_id']));
                //   }
            }

//            if ($data['status'] == 'received') {
//                //post supplier invoice
//                $this->postPurchaseInvoice($data);
//                $this->site->syncQuantity(NULL, $purchase_id);
//            }
            return true;
        }else{
            return false;
        }
    }

    public function  updateDistributorProductsQuantities($item, $distributor_id){

        //check if product id exists in products_distributor_quantities
        $q = $this->db->get_where('sma_product_distributor_quantities', array(
            'product_id' => $item['product_id'],
            'distributor_id' => $distributor_id));
        if ($q->num_rows() > 0) {
            //get the current quantity and add the new quantity then update the record
            $new_quantity = $q->row()->quantity + $item['quantity'];

            if ($this->db->update('sma_product_distributor_quantities',
                $data = array(
                    'product_id' => $item['product_id'],
                    'distributor_id' => $distributor_id,
                    'quantity' => $new_quantity),
                array(
                    'product_id' => $item['product_id'],
                    'distributor_id' => $distributor_id))) {
                return true;
            }else{
                return false;
            }

        }else{
            //insert new record in table products_distributor_quantities
            $data = array(
                'distributor_id' => $distributor_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            );
            if ($this->db->insert('sma_product_distributor_quantities', $data)) {
                return true;
            }else{
                return false;
            }
        }


    }

    public function  updateVehicleProductsQuantities($item, $distributor_id, $vehicle_id){

        //check if product id exists in products_distributor_quantities
        $q = $this->db->get_where('sma_product_vehicle_quantities', array(
            'product_id' => $item['product_id'],
            'distributor_id' => $distributor_id,
            'vehicle_id' => $vehicle_id));
        if ($q->num_rows() > 0) {
            //get the current quantity and add the new quantity then update the record
            $new_quantity = $q->row()->quantity + $item['quantity'];

            if ($this->db->update('sma_product_vehicle_quantities',
                $data = array(
                    'product_id' => $item['product_id'],
                    'distributor_id' => $distributor_id,
                    'vehicle_id' => $vehicle_id,
                    'quantity' => $new_quantity),
                array(
                    'product_id' => $item['product_id'],
                    'vehicle_id' => $vehicle_id,
                    'distributor_id' => $distributor_id))) {
                return true;
            }else{
                return false;
            }

        }else{
            //insert new record in table products_distributor_quantities
            $data = array(
                'distributor_id' => $distributor_id,
                'vehicle_id' => $vehicle_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            );
            if ($this->db->insert('sma_product_vehicle_quantities', $data)) {
                return true;
            }else{
                return false;
            }
        }


    }
    
        
	public function addPurchase_bycsv($data = array(), $items = array(), $payment = array())
    {

   			        $i = 0;  
               foreach ($data as $dt) {

                
				$dt['reference_no'] = $this->site->getReference('po');
                
				$this->db->insert('sma_purchases', $dt);
                $purchase_id = $this->db->insert_id();
				$this->site->updateReference('po');
            //update consolidated table
                                if($dt["stock_type"]=="SSO"){
             $this->sales_model->updateConsolidatedSSO(array("upload_type"=>"STOCK","promotion"=>$dt["promotion"],"country"=>$dt["country"],"country_id"=>$dt["country_id"],"gmid"=>$items[$i]['product_code'],"product_name"=>$items[$i]['product_name'],"monthyear"=>$dt["date"],"distributor"=>$dt["supplier"],"distributor_id"=>$dt["supplier_id"],"stock_value"=>$dt["shipping"],"stock_qty"=>$items[$i]['quantity'],"purchase_id"=>$purchase_id));
                                            }
            
                 $items[$i]['purchase_id']=$purchase_id;	
                $this->db->insert('purchase_items',$items[$i]);
				
                $sale_item_id = $this->db->insert_id();
//                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($items[$i]['product_id'])) {
//
//                    $item_costs = $this->site->item_costing($items[$i]);
//                   
//
//                
//            }
			$i++;
				  }
         
            return true;

       
    }

    //get supplier id and match the one in erp
    function postPurchaseInvoice($data){
        $this->db->set_dbprefix('0_');
//$this->db->dbprefix('tablename');
        //DR VAT
     
        $invoice["type"]=20;
        $invoice["type_no"]=5;
         $invoice["tran_date"]=date("Y-m-d");
        $invoice["account"]=2150;
        $invoice['memo_']="POS purchase";
        $invoice["dimension_id"]=0;
         $invoice["dimension2_id"]=0;
        $invoice["amount"]=$data["total_tax"];
      
         $this->db->insert('gl_trans',$invoice);
         unset($invoice);
         
         
          //CR TRADE CREDITORS
              $invoice["type"]=20;
        $invoice["type_no"]=5;
         $invoice["tran_date"]=date("Y-m-d");
        $invoice["account"]=2100;
        $invoice['memo_']="POS purchase";
        $invoice["dimension_id"]=0;
         $invoice["dimension2_id"]=0;
         $invoice["person_type_id"]=$data["supplier_id"];  //link with ERP supplier
        $invoice["amount"]=-$data["grand_total"];
         $this->db->insert('gl_trans',$invoice);
     unset($invoice);
         //DR PURCHASE ACCOUNT EXPENSE
             $invoice["type"]=20;
        $invoice["type_no"]=5;
         $invoice["tran_date"]=date("Y-m-d");
        $invoice["account"]=1510;
        $invoice['memo_']="POS purchase";
        $invoice["dimension_id"]=0;
         $invoice["dimension2_id"]=0;
         $invoice["person_type_id"]=$data["supplier_id"];  //link with ERP supplier
        $invoice["amount"]=$data["grand_total"]-$data["total_tax"];
         $this->db->insert('gl_trans',$invoice);
     unset($invoice);
         
               
      $this->db->set_dbprefix('sma_');
    }
    
    
    
    public function  postSupplierPayment(){
        
        
    }
    
    public function updatePurchase($id, $data, $items = array())
    {
        $opurchase = $this->getPurchaseByID($id);
        $oitems = $this->getAllPurchaseItems($id);
        if ($this->db->update('purchases', $data, array('id' => $id)) &&
            $this->db->delete('purchase_items', array('purchase_id' => $id))) {
            $purchase_id = $id;
            foreach ($items as $item) {
                $item['purchase_id'] = $id;
                $this->db->insert('purchase_items', $item);
            }
            if ($opurchase->status == 'received') {
                $this->site->syncQuantity(NULL, NULL, $oitems);
            }
            if ($data['status'] == 'received') {
                $this->site->syncQuantity(NULL, $id);
            }
            $this->site->syncPurchasePayments($id);
            return true;
        }

        return false;
    }

    public function deletePurchaseBySupplierDateAndType($supplierid,$date,$stocktype)
    {
     $newdate=date("Y-m",  strtotime($date));
     //die($newdate."sdsd");
//      $newdate=DateTime::createFromFormat("Y/m/d H:i:s",$date);
//      
//        $yearmonth=$newdate->format("Y")."-".$newdate->format("m");
        
        $this->db->select("id")
                ->where('DATE_FORMAT(date,"%Y-%m")="'.$newdate.'"')
                    ->where('supplier_id="'.$supplierid.'"');
          $q = $this->db->get_where('purchases', array('stock_type' => $stocktype));
 if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
        $this->db->delete('purchase_items', array('purchase_id' => $row->id));
        $this->db->delete('consolidated_sales_sso', array('purchase_id' => $row->id));
        $this->db->delete('purchases', array('id' => $row->id)); 
            $this->db->delete('payments', array('purchase_id' => $row->id));
           // $this->site->syncQuantity(NULL, NULL, $purchase_items);
            return true;
        
        return FALSE;
            }
 }
    }
    public function deletePurchase($id)
    {
        $purchase_items = $this->site->getAllPurchaseItems($id);

        if ($this->db->delete('purchase_items', array('purchase_id' => $id)) && $this->db->delete('purchases', array('id' => $id))) {
            $this->db->delete('payments', array('purchase_id' => $id));
            $this->site->syncQuantity(NULL, NULL, $purchase_items);
            return true;
        }
        return FALSE;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasePayments($purchase_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getPaymentsForPurchase($purchase_id)
    {
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('pay') == $data['reference_no']) {
                $this->site->updateReference('pay');
            }
            $this->site->syncPurchasePayments($data['purchase_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->site->syncPurchasePayments($data['purchase_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->site->syncPurchasePayments($opay->purchase_id);
            return true;
        }
        return FALSE;
    }

    public function getProductOptions($product_id)
    {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductVariantByName($name, $product_id)
    {
        $q = $this->db->get_where('product_variants', array('name' => $name, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getExpenseByID($id)
    {
        $q = $this->db->get_where('expenses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addExpense($data = array())
    {
        if ($this->db->insert('expenses', $data)) {
            if ($this->site->getReference('ex') == $data['reference']) {
                $this->site->updateReference('ex');
            }
            return true;
        }
        return false;
    }

    public function updateExpense($id, $data = array())
    {
        if ($this->db->update('expenses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteExpense($id)
    {
        if ($this->db->delete('expenses', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getQuoteByID($id)
    {
        $q = $this->db->get_where('quotes', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllQuoteItems($quote_id)
    {
        $q = $this->db->get_where('quote_items', array('quote_id' => $quote_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    
     function getStockCover($data,$salestype){
         //die(print_r($data));
         if(empty($data["datefrom"])){
          $fromdaymonthyear="01-".date("Y");
          $todaymonthyear=="12-".date("Y");
         }else{
              $fromdaymonthyear=$data["datefrom"];
			
                $todaymonthyear=$data["dateto"];
			
         }
        $months=array("01","02","03","04","05","06","07","08","09","10","11","12");
        $startdate=explode("-",$fromdaymonthyear);
        $enddate=explode("-",$todaymonthyear);
         $dataa=array();
         
        if($enddate[0] > $startdate[0] && $enddate[1]==$startdate[1]){ //end date is higher than start date
            
            for($i=(int)$startdate[0];$i<=(int)$enddate[0];$i++){
                 if(strlen($i)<2){
                    $newdate="0".$i."-".$enddate[1];  
                } else{
                $newdate=$i."-".$enddate[1];
                
                }   
              //print_r($newdate);
              $closingstock=$this->getClosingStock($data,$newdate,$salestype);
           //  echo $closingstock."stock";
              $averagesales=$this->get3MonthSalesAverage($data,$newdate,$salestype);
             // echo $averagesales."sales";
        //  print_r($averagesales.";stock;".$closingstock." ;".$newdate."<br>");
              $sales["date"]=$newdate;
              $sales["value"]=round($closingstock/$averagesales,2);
                array_push($dataa, $sales);    
            }
        }
          
          
          unset($sales);
         return json_encode($dataa);
      }
        
     function getMonthStockCover($data,$monthyear,$salestype){
   
              $closingstock=$this->getClosingStock($data,$monthyear,$salestype);
            //echo $monthyear."<br>";
              $averagesales=round(($this->get3MonthSalesAverage($data,$monthyear,$salestype)),2);
               //   echo $closingstock."stock".$averagesales."m".$monthyear."<br>";
              $value=round($closingstock/$averagesales,2);
   
         return $value;
      
     }
     
      function getAverageSales($data,$monthyear,$salestype){
        $averagesales=$this->get3MonthSalesAverage($data,$monthyear,$salestype);
         
         return $averagesales;
      
     }
     
    
    
     function getClosingStock($data,$monthyear,$stocktype){
        //die(print_r($data));
         
          if($data["promotion"] && $data["promotion"]!="null"){
                            $this->db->where("pu.promotion",$data["promotion"]);
                }
                 if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
	$valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("pu.country_id IN (".$valuee.")");
        }
        
          
                    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])  ){
                        
                        $this->db->join('products', 'product_id=products.id', 'left');
                          $this->db->join('categories', 'products.category_id=categories.id', 'left');
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("categories.id IN (".$categoriess.")");
}   
if($data["gbu"] && $data["gbu"] !="all"){
   /// $this->db->join('products', 'product_id=products.id', 'left');
                         // $this->db->join('categories', 'products.category_id=categories.id', 'left');
$this->db->where('pu.gbu',$data["gbu"]);	
}

     if($data["product"] && !in_array("all",$data["product"])){
         //die(print_r($data['products']));
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("pi.product_code IN (".$prods.")");
} 
    
if($data["promotion"]=="1" || $data["promotion"]=="0"){
       //$this->db->join("products", "sale_items.product_id=products.id", 'left');
$this->db->where('pu.promotion', $data["promotion"]);	
}
        
         if(count($data["customer"])>0 && !empty($data["customer"][0])&& !in_array("all",$data["customer"])){
       
            foreach ($data["customer"] as $cust) {
				if($cust){
                $clusterss.="'".$cust."',";
				}
                     }
	$valueee=rtrim($clusterss,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("pu.supplier_id IN (".$valueee.")");
        }
   if(count($data["distributor"])>0 && !empty($data["distributor"][0])&& !in_array("all",$data["distributor"])){
       
            foreach ($data["distributor"] as $cust) {
				if($cust){
                $distributors.="'".$cust."',";
				}
                     }
	$valueee=rtrim($distributors,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("pu.supplier_id IN (".$valueee.")");
        }
                
                
        if(@$data["customer"][0]=="all" || empty($data["customer"]) ){
            //die("sdsd");
            $this->db->select("SUM(pi.shipping) as total_stock")
               ->from("purchase_items pi")
               ->join('purchases pu', 'pu.id=pi.purchase_id','left')
              ->where("pu.stock_type",$stocktype)
            ->where("DATE_FORMAT(pu.date,'%m-%Y')='".$monthyear."'");
        }
        else{
             foreach ($data["customer"] as $cust) {
                $customerr.=$cust.",";
            }
            $finalcustomer=rtrim($customerr,",");
      $this->db->select("SUM(pi .shipping) as total_stock")
               ->from("purchase_items pi")
               ->join('purchases pu', 'pu.id=pi.purchase_id','left')
              ->where("pu.stock_type",$stocktype)
            ->where("DATE_FORMAT(pu.date,'%m-%Y')='".$monthyear."' AND pi.supplier_id IN(".$finalcustomer.")");
        }
         $stock = $this->db->get()->row();
         
       //echo $monthyear.print_r($stock); 
      
        if (is_object($stock)) {
            
            return round($stock->total_stock/1000,2);
        }
        return 0;
    }
    
    
    function getClosingStockDit($data,$monthyear,$stocktype){
        //die(print_r($data));
      
            
            $finalcustomer=$data["customer"];
            $country=$data["country_name"];
            $product_id=$data["product_id"];
      $this->db->select("SUM(pi .shipping) as total_stock,SUM(pi.quantity) as qty")
               ->from("purchase_items pi")
               ->join('purchases pu', 'pu.id=pi.purchase_id','left')
              ->where("pu.stock_type",$stocktype)
              ->where("pi.product_id",$product_id)
            ->where("DATE_FORMAT(pu.date,'%m-%Y')='".$monthyear."' AND pi.supplier_id ='$finalcustomer' AND pu.country='$country' ");
        
         $stock = $this->db->get()->row();
        if (is_object($stock)) {
           
            return  array("value"=>round($stock->total_stock/1000),"qty"=>$stock->qty);
        }
        return array();
    }
    
    function getClosingStockDitNew($data,$monthyear,$countryid,$distributorid,$brand_id=NULL){
       // die(print_r($data));
      
       $product_id=$data["gmid"];
          if(!empty($data["distributor"])|| !in_array("all",$data["distributor"])|| $data["distributor"]!="undefined"){
      $distributors=  implode(",", $data["distributor"]);
      }
       
         $this->db->select("SUM(stock_value) AS total_stock,SUM(stock_qty) as qty");
             
             if($product_id){
                         $this->db->where("gmid",$product_id);
             }
             if($distributors){
                         $this->db->where("distributor_id IN ('".$distributors."')");
             }
              if($data["gbu"] && $data["gbu"] !="all" && !empty($data["gbu"])){
              
              $gbu=$data["gbu"];
               $this->db->where("bu IN ('".$gbu."') ");
          }
             
            if($data["promotion"] && $data["promotion"]!="null"){
                            $this->db->where("promotion",$data["promotion"]);
                }
                
                  if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
      $this->db->where("consolidated_sales_sso.brand_id IN (".$categoriess.")");
}
             
            $this->db->where("upload_type","STOCK");
                    if($brand_id){
            $this->db->where("DATE_FORMAT(monthyear,'%m-%Y')='".$monthyear."' AND distributor_id ='$distributorid' AND country_id='$countryid' AND brand_id='$brand_id'"); // 
                    } else{
                        $this->db->where("DATE_FORMAT(monthyear,'%m-%Y')='".$monthyear."' AND distributor_id ='$distributorid' AND country_id='$countryid' ");
                    }
                    $this->db->from("consolidated_sales_sso");
        
         $stock = $this->db->get()->row();
     
        
      
           
            return  array("value"=>round($stock->total_stock/1000,5),"qty"=>$stock->qty);
       
    }
    
    
    //return sales average
    //$distributorid,$endmonthyear,$gross="1",$salestype="si"
    function get3MonthSalesAverage($data,$endmonthyear,$salestype){
    //   die(print_r($data));
        $monthsyear=$this->getLast3MonthsWithCurrentMonth($endmonthyear);
        
        foreach ($monthsyear as $value) {
            $monthyear.="'".$value."',";
        };
        $monthyear=  rtrim($monthyear,",");
        
      //  echo "month is".$endmonthyear."months=>".$monthyear."<br>";
      //  die(print_r($data));
        
          if(count($data["countrys"])>0 && !empty($data["countrys"][0]) && !in_array("all",$data["countrys"])){
    
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
					 $valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("sales.country_id IN (".$valuee.")");
        }
      if(count($data["customer"])>0 && !empty($data["customer"][0])&& !in_array("all",$data["customer"])){
        
            foreach ($data["customer"] as $cust) {
				if($cust){
                $clusterss.="'".$cust."',";
        }
                     }
	$valueee=rtrim($clusterss,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("sales.customer_id IN (".$valueee.")");
        }
   if(count($data["distributor"])>0 && !empty($data["distributor"][0])&& !in_array("all",$data["distributor"])){
   
            foreach ($data["distributor"] as $cust) {
				if($cust){
                $distributors.="'".$cust."',";
				}
                     }
	$valueee=rtrim($distributors,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("sales.distributor_id IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
    
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("sales.brand_id IN (".$categoriess.")"); 

}

if($data["product"] && !in_array("all",$data["product"])){
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("gmid IN (".$prods.")");
} 

if($data["gbu"] && $data["gbu"] !="all"){
    //$this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('sales.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('sales.promotion', $data["promotion"]);	
}
        $sales=0;
        //$salestype=$data["scenario"];
        if(@$data["distributor"][0]=="all" || empty($data["distributor"])){
           
              
                 $this->db->select("SUM(grand_total) as total_sales")
                           ->where("movement_code","VE")
                         ->where("sales_type","SSO")
               ->from("sales")
            ->where("DATE_FORMAT(date,'%m-%Y') IN (".$monthyear.") ");
                // ->where("sma_sales.sales_type",$salestype);
              
                  $stock= $this->db->get()->row();
                  
            
            $sales=round(($stock->total_sales/3)/1000,2);
                  
           
      
        
        }
        else{  //individual distributor
            foreach ($data["distributor"] as $cust) {
                $customerr.=$cust.",";
            }
            $finalcustomer=rtrim($customerr,",");
            
          $this->db->select("SUM(gross_sale) as total_sales")
               ->from("sales")
            ->where("DATE_FORMAT(monthyear,'%m-%Y') IN (".$monthyear.") AND distributor_id IN(".$finalcustomer.")");
          
            $stock= $this->db->get()->row();
             
         
           $sales=round(($stock->total_sales/3)/1000,2);
           // $sales=round(($sales/3),2);
                  
           
        }
        
        
        return $sales;
        
    }
    
    
    
    
    //get last 3 months from given month
function getLast3Months($monthYear){
    $dates=explode("-", $monthYear);
    $month=$dates[0];
    $year=$dates[1];
    if($month=="01"){
      $monthsyear=array("10-".($year-1),"11-".($year-1),"12-".($year-1)); 
    }
    else if($month=="02"){
     $monthsyear=array("11-".($year-1),"12-".($year-1),"01-".($year));    
    }
    else if($month=="03"){
     $monthsyear=array("12-".($year-1),"01-".($year),"02-".($year));    
    }
    
    else if($month=="04"){
     $monthsyear=array("01-".($year),"02-".($year),"03-".($year));    
    }
    else if($month=="05"){
     $monthsyear=array("02-".($year),"03-".($year),"04-".($year));    
    }
    
    else if($month=="06"){
     $monthsyear=array("03-".($year),"04-".($year),"05-".($year));    
    }
    else if($month=="07"){
     $monthsyear=array("04-".($year),"05-".($year),"06-".($year));    
    }
    else if($month=="08"){
     $monthsyear=array("05-".($year),"06-".($year),"07-".($year));    
    }
    else if($month=="09"){
     $monthsyear=array("06-".($year),"07-".($year),"08-".($year));    
    }
    else if($month=="10"){
     $monthsyear=array("07-".($year),"08-".($year),"09-".($year));    
    }
    else if($month=="11"){
     $monthsyear=array("08-".($year),"09-".($year),"10-".($year));    
    }
    else if($month=="12"){
     $monthsyear=array("09-".($year),"10-".($year),"11-".($year));    
    }
    
    return $monthsyear;
}

function getLast3MonthsWithCurrentMonth($monthYear){
    $dates=explode("-", $monthYear);
    $month=$dates[0];
    $year=$dates[1];
    if($month=="01"){
      $monthsyear=array("11-".($year-1),"12-".($year-1),"01-".($year)); 
    }
    else if($month=="02"){
     $monthsyear=array("12-".($year-1),"01-".($year),"02-".($year));    
    }
    else if($month=="03"){
     $monthsyear=array("01-".($year),"02-".($year),"03-".($year));    
    }
    
    else if($month=="04"){
     $monthsyear=array("02-".($year),"03-".($year),"04-".($year));    
    }
    else if($month=="05"){
     $monthsyear=array("03-".($year),"04-".($year),"05-".($year));    
    }
    
    else if($month=="06"){
     $monthsyear=array("04-".($year),"05-".($year),"06-".($year));    
    }
    else if($month=="07"){
     $monthsyear=array("05-".($year),"06-".($year),"07-".($year));    
    }
    else if($month=="08"){
     $monthsyear=array("06-".($year),"07-".($year),"08-".($year));    
    }
    else if($month=="09"){
     $monthsyear=array("07-".($year),"08-".($year),"09-".($year));    
    }
    else if($month=="10"){
     $monthsyear=array("08-".($year),"09-".($year),"10-".($year));    
    }
    else if($month=="11"){
     $monthsyear=array("09-".($year),"10-".($year),"11-".($year));    
    }
    else if($month=="12"){
     $monthsyear=array("10-".($year),"11-".($year),"12-".($year));    
    }
    
    return $monthsyear;
}

}
