<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Distributor_product_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
     public function addProduct($products)
    {
    if (!empty($products)) {
            foreach ($products as $product) {
               $q = $this->db->get_where('distributor_products', array('product_id' =>$product["product_id"],"distributor_id"=>$product["distributor_id"],"distributor_product_name"=>$product["distributor_product_name"]), 1);
       if ($q->num_rows() > 0) {
         // $this->db->update('distributor_productsk', $product,array('product_id' =>$product["product_id"],"distributor_id"=>$product["distributor_id"]));
        } else{
              $this->db->insert('distributor_products', $product);
       }
                
            }
            return true;
        }
        return false;
          
    }
    
    
       public function addProduct1($products)
    {
    if (!empty($products)) {
            foreach ($products as $product) {
               $q = $this->db->get_where('sma_customer_products_name_matching', array('product_id' =>$product["product_id"],"customer_id"=>$product["customer_id"]), 1);
        if ($q->num_rows() > 0) {
           $this->db->update('sma_customer_products_name_matching', $product,array('product_id' =>$product["product_id"],"customer_id"=>$product["customer_id"]));
        } else{
              $this->db->insert('sma_customer_products_name_matching', $product);
        }
                
            }
            return true;
        }
        return false;
          
    }
    
    
    

    public function getProductNames($term, $limit = 5)
    {
        $this->db->where("type = 'standard' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
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

    public function getDistributorByVanID($van_id){
        $q = $this->db->get_where('companies', array('van_id' => $van_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllDistributorProducts()
    {
        
          $this->db->select('distributor_products.id,products.name,currencies.country,distributor_products.distributor_product_name,distributor_products.distributor_code')
                                ->join('products', 'products.id=distributor_products.product_id', 'left')
            ->join('companies', 'companies.id=distributor_products.distributor_id', 'left')
                   ->join('currencies', 'distributor_products.country=currencies.id', 'left')
                 //  ->where('companies', array('group_name' =>'customer'))
            ->order_by('products.name', 'asc');
          
      $q = $this->db->get('distributor_products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
     public function getADistributorProduct($id)
    {
        
           $this->db->select('distributor_products.id,products.name,currencies.country,companies.name,distributor_products.distributor_product_name,distributor_products.distributor_code')
                                ->join('products', 'products.id=distributor_products.product_id', 'left')
            ->join('companies', 'companies.id=distributor_products.distributor_id', 'left')
                  ->join('currencies', 'distributor_products.country=currencies.id', 'left')
                 //  ->where('companies', array('group_name' =>'customer'))
            ->order_by('distributor_products.id', 'asc');
          
      $q = $this->db->get_where('distributor_products',array('product_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
       
    }
    
    
         public function getACustomerProduct($id)
    {
        
          $this->db->select('sma_customer_products_name_matching.id,products.name,currencies.country,customers.name,sma_customer_products_name_matching.customer_naming')
                                ->join('products', 'products.id=sma_customer_products_name_matching.product_id', 'left')
            ->join('customers', 'customers.id=sma_customer_products_name_matching.customer_id', 'left')
                  ->join('currencies', 'sma_customer_products_name_matching.country=currencies.id', 'left')
                 //  ->where('companies', array('group_name' =>'customer'))
            ->order_by('sma_customer_products_name_matching.id', 'asc');
          
      $q = $this->db->get_where('sma_customer_products_name_matching',array('product_id'=>$id));
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

      public function getProductByName($name)
    {
         $trimmedname=  str_replace(" ","",$name);
         
         $this->db->select("product_id,product_name");
        $this->db->where(" REPLACE(distributor_product_name,' ','')='".$trimmedname."'");
        $q = $this->db->get('distributor_products',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
      public function getProductByNameAndSupplier($name,$supplierid)
    {
          $trimmed=str_replace("'","",$name);
         $trimmedname=  str_replace(" ","",$trimmed);
         
         $this->db->select("product_id,product_name");
        $this->db->where(" REPLACE(distributor_product_name,' ','')='".$trimmedname."'");
        $this->db->where(array("distributor_id"=>$supplierid));
        $q = $this->db->get('distributor_products',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
		public function getProductByDescription($product_desc,$country,$distributor)
    {
        $comp=$this->companies_model->getCompanyByNameAndCountry($distributor,$country);
        
	  $trimmedname=  str_replace(" ","",$product_desc);	   
        $this->db->select('sma_distributor_products.product_id as id,sma_distributor_products.product_name as name,sma_products.type,sma_products.code')
            ->join('sma_products', 'sma_distributor_products.product_id = sma_products.id', 'left')
			->join( 'sma_companies', 'sma_companies.id = sma_distributor_products.distributor_id', 'left')
			
			->where(" REPLACE(sma_distributor_products.distributor_product_name,' ','')='".$trimmedname."'")
              ->where('sma_distributor_products.distributor_id', $comp->id)
			  ->where('sma_distributor_products.country',$country)
                          ->where('code is NOT NULL');
            
        $q = $this->db->get('sma_distributor_products');
       //// $q = $this->db->get_where('products', array('code' => $gmid), 1);
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

    public function addPurchase($data, $items)
    {

        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();
            if ($this->site->getReference('po') == $data['reference_no']) {
                $this->site->updateReference('po');
            }
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                $this->db->insert('purchase_items', $item);
                $this->db->update('products', array('cost' => $item['real_unit_cost']), array('id' => $item['product_id']));
                if($item['option_id']) {
                    $this->db->update('product_variants', array('cost' => $item['real_unit_cost']), array('id' => $item['option_id'], 'product_id' => $item['product_id']));
                }
            }
            if ($data['status'] == 'received') {
                //post supplier invoice 
                $this->postPurchaseInvoice($data);
                $this->site->syncQuantity(NULL, $purchase_id);
            }
            return true;
        }
        return false;
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
        if ($this->db->update('purchases', $data, array('id' => $id)) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {
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

}
