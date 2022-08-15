<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends MY_Controller
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
        $this->load->model('sync_model');
        $this->load->model('sales_model');
    }

    public function index()
    {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => '#', 'page' => 'sync database'));
        $meta = array('page_title' => 'sync database', 'bc' => $bc);
        $this->page_construct('sync/index', $meta, $this->data);

    }

    public function import_billers()
    {

        if ($this->sync_model->importBillers()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function import_customers()
    {

        if ($this->sync_model->importCustomers()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function import_suppliers()
    {

        if ($this->sync_model->importSuppliers()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function user_groups()
    {

        if ($this->sync_model->userGroups()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function delete_extra_tables()
    {

        if ($this->sync_model->deleteExtraTables()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function reset_sales()
    {

        if ($this->sync_model->resetSalesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function reset_quotes()
    {

        if ($this->sync_model->resetQuotesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }
 
    
     function updatesalesitems(){ 
       
        $this->db->limit(20000);
         $this->db->select('*');
          $this->db->where("updated_sso ='0'");
        $q=$this->db->get('sales');
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                
              
            echo $row->product_id."sdsd".$row->id."<br>";
             
            $this->db->insert('sale_items', array('sale_id' =>$row->id,'product_id'=>$row->product_id,'product_code'=>$row->gmid,'product_name'=>$row->products,'product_type'=>'standard','quantity'=>$row->quantity_units,'subtotal'=>$row->grand_total,'country_id'=>$row->country_id));
            $this->db->insert('consolidated_sales_sso', array('upload_type'=>'SALE','country'=>$row->country,'monthyear'=>$row->date,'customer_sanofi'=>$row->customer,'customer_id'=>$row->customer_id,'distributor'=>$row->distributor,'distributor_id'=>$row->distributor_id,'promotion'=>$row->promotion,'brand'=>$row->brand,'brand_id'=>$row->brand_id,'bu'=>$row->gbu,'gross_qty'=>$row->quantity_units,'gross_sale'=>$row->grand_total,'sale_id' =>$row->id,'product_id'=>$row->product_id,'gmid'=>$row->gmid,'product_name'=>$row->products,'movement_code'=>$row->movement_code,'country_id'=>$row->country_id));
            $this->db->update('sales', array('updated_sso' =>1), array('id' => $row->id)); 
            }
            

            
    }
   return TRUE;
    }
    
    function updatepricinggmid(){ 
       
        $this->db->limit(300);
         $this->db->select('id,code');
          
           $this->db->where("is_active=0");
        $q=$this->db->get('products');
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                
              
            echo $row->id."sdsd".$row->code."<br>";
             
           
            $this->db->delete('countryproductpricing', array('product_id' => $row->id)); 
            }
            

            
    }
   return TRUE;
    }
    
    
    
    function  updatemsr(){
      $this->db->limit(5000);
         $this->db->select('*');
          $this->db->where("updated_sso ='0'");
        $q=$this->db->get('sales');
       
         //if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
           //echo $row->product_id."sdsd".$row->id."<br>";
             
            $msr_details = $this->sales_model->msr_customer_alignments($row->customer_id,$row->product_id,$row->country_id);
         $msrid=$msr_details->sf_alignment_id;
           $msrname=$msr_details->sf_alignment_name;
         //  if($msr_details){
            $this->db->update('consolidated_sales_sso', array('msr_id' =>$msrid,'msr_name'=>$msrname),array('sale_id' => $row->id));
            $this->db->update('sales', array('msr_alignment_id' =>$msrid,'msr_alignment_name'=>$msrname,'updated_sso'=>1), array('id' => $row->id)); 
           
          //  unset($msr_details);
            
            }
            

            
   // }
   return TRUE;    
    }
            
     function updatesalesitemstender(){ 
       
        $this->db->limit(20000);
         $this->db->select('*');
          $this->db->where("updated_sso ='0'");
        $q=$this->db->get('sales');
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                
              
            echo $row->product_id."sdsd".$row->id."<br>";
             
             $this->db->insert('consolidated_sales_sso', array('upload_type'=>'SALE','country'=>$row->country,'monthyear'=>$row->date,'customer_sanofi'=>$row->customer,'customer_id'=>$row->customer_id,'distributor'=>$row->distributor,'distributor_id'=>$row->distributor_id,'promotion'=>$row->promotion,'brand'=>$row->brand,'brand_id'=>$row->brand_id,'bu'=>$row->gbu,'tender_qty'=>$row->quantity_units,'tender_sale'=>$row->tender_price,'sale_id' =>$row->id,'product_id'=>$row->product_id,'gmid'=>$row->gmid,'product_name'=>$row->products,'movement_code'=>$row->movement_code,'country_id'=>$row->country_id));
             $this->db->update('sales', array('updated_sso' =>1), array('id' => $row->id));
            $this->db->insert('sale_items', array('sale_id' =>$row->id,'product_id'=>$row->product_id,'product_code'=>$row->gmid,'product_name'=>$row->products,'product_type'=>'standard','quantity'=>$row->quantity_units,'subtotal'=>$row->grand_total,'country_id'=>$row->country_id));
            
                }
            

            
    }
   return TRUE;
    }
    
  function updatepurchaseitems(){ 
         $this->db->limit(10000);
         $this->db->select('*');
         $this->db->where("product_id IS NULL");
        $q=$this->db->get('purchases');
       
         if ($q->num_rows() > 0) {
            //get budget data
       foreach (($q->result()) as $row) {    
            echo $row->product_id."stock".$row->id."<br>";
            $q2= $this->db->get_where('products',array("id"=>$row->product_id));
            $resultt2=$q2->row();
    
    $this->db->insert('purchase_items',array('purchase_id' =>$row->id,'product_id'=>$row->product_id,'product_code'=>$resultt2->code,'product_name'=>$resultt2->name,'quantity'=>$row->quantity,'shipping'=>$row->grand_total,'subtotal'=>$row->grand_total,'date'=>$row->date,'supplier'=>$row->supplier,'supplier_id'=>$row->supplier_id));
   $this->db->insert('consolidated_sales_sso', array('upload_type'=>'STOCK','country'=>$row->country,'monthyear'=>$row->date,'distributor'=>$row->supplier,'distributor_id'=>$row->supplier_id,'promotion'=>$row->promotion,'brand'=>$row->brand_name,'brand_id'=>$row->brand_id,'bu'=>$row->gbu,'stock_qty'=>$row->quantity,'stock_value'=>$row->grand_total,'purchase_id' =>$row->id,'product_id'=>$row->product_id,'gmid'=>$resultt2->code,'product_name'=>$resultt2->name,'movement_code'=>@$row->movement_code,'country_id'=>$row->country_id));
                }
            

            
    }
   return TRUE;
    }  
    
    
    function updatebrandgbu(){
         $this->load->model('country_productpricing_model');
        $this->db->limit(25000);
         $this->db->select('id,product_id,country_id');
        $q=$this->db->get_where('sales',array('brand'=>NULL));
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                
               $q2= $this->db->get_where('products',array("id"=>$row->product_id));
               $ctrypring=$this->country_productpricing_model->getCountryProductPricing($row->product_id,$row->country_id);
                if ($q2->num_rows() > 0) {
            //get budget data
             $resultt=$q2->row();
              $q22= $this->db->get_where('categories',array("id"=>$resultt->category_id));
              $resultt2=$q22->row();
           // echo $row->product_id."sdsd".$resultt->id."<br>";
             
            $this->db->update('sales', array('brand_id' =>$resultt2->id,'brand'=>$resultt2->name,'promotion'=>$ctrypring->promoted,'gbu'=>$resultt2->gbu), array('id' => $row->id));
                }
            }

            
    }
   return TRUE;
    }
function updatebrandgbu2(){
         $this->load->model('country_productpricing_model');
        $this->db->limit(10000);
         $this->db->select('id,product_id,country_id');
        $q=$this->db->get_where('sales',array('updated_sso'=>0));
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                
               $q2= $this->db->get_where('products',array("id"=>$row->product_id));
               $ctrypring=$this->country_productpricing_model->getCountryProductPricing($row->product_id,$row->country_id);
                if ($q2->num_rows() > 0) {
            //get budget data
            // $resultt=$q2->row();
//              $q22= $this->db->get_where('categories',array("id"=>$resultt->category_id));
//              $resultt2=$q22->row();
           echo $row->product_id."sdsd".$row->id."<br>";
             
            $this->db->update('consolidated_sales_sso', array('promotion'=>$ctrypring->promoted), array('sale_id' => $row->id,'upload_type'=>'SALE'));
            $this->db->update('sales', array('updated_sso'=>1), array('id' => $row->id));
                }
            }

            
    }
   return TRUE;
    }
  
    function updateproducts(){
         $this->load->model('country_productpricing_model');
      //  $this->db->limit(10000);
         $this->db->select('id,product_id');
        $q=$this->db->get_where('sales');
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                
               $q2= $this->db->get_where('products',array("id"=>$row->product_id));
              // $ctrypring=$this->country_productpricing_model->getCountryProductPricing($row->product_id,$row->country_id);
                if ($q2->num_rows() > 0) {
            //get budget data
             $resultt=$q2->row();
//              $q22= $this->db->get_where('categories',array("id"=>$resultt->category_id));
//              $resultt2=$q22->row();
           echo $row->product_id."sdsd".$row->id."<br>";
             
           // $this->db->update('consolidated_sales_sso', array('promotion'=>$ctrypring->promoted), array('sale_id' => $row->id,'upload_type'=>'SALE'));
            $this->db->update('sales', array('products'=>$resultt->name), array('id' => $row->id));
                }
            }

            
    }
   return TRUE;
    }
    
    function updatebrandgbupurchases(){
         $this->load->model('country_productpricing_model');
        $this->db->limit(10000);
         $this->db->select('id,country_id');
        $q=$this->db->get_where('purchases',array('brand_id'=>NULL));
       
         if ($q->num_rows() > 0) {
            //get budget data
         
            foreach (($q->result()) as $row) {
                  $qq=$this->db->get_where('purchase_items',array('purchase_id'=>$row->id));
                  $roww=$qq->row();
               $q2= $this->db->get_where('products',array("id"=>$roww->product_id));
               $ctrypring=$this->country_productpricing_model->getCountryProductPricing($roww->product_id,$row->country_id);
                if ($q2->num_rows() > 0) {
            //get budget data
             $resultt2=$q2->row();
              $q22= $this->db->get_where('categories',array("id"=>$resultt2->category_id));
            $resultt22=$q22->row();
           echo $roww->product_id."sdsd".$row->id."<br>";
             
           $this->db->update('consolidated_sales_sso', array('promotion'=>$ctrypring->promoted,'product_id'=>$roww->product_id,'product_name'=>$roww->product_name,'brand_id' =>$resultt22->id,'brand'=>$resultt22->name,'bu'=>$resultt22->gbu), array('purchase_id' => $row->id,'upload_type'=>'STOCK'));
            $this->db->update('purchases', array('product_id'=>$roww->product_id,'sku'=>$roww->product_name,'brand_id' =>$resultt22->id,'brand_name'=>$resultt22->name,'promotion'=>$ctrypring->promoted,'gbu'=>$resultt22->gbu), array('id' => $row->id));
                }
            }

            
    }
   return TRUE;
    }
    public function reset_purchases()
    {

        if ($this->sync_model->resetPurchasesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function reset_transfers()
    {

        if ($this->sync_model->resetTransfersTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function reset_deliveries()
    {

        if ($this->sync_model->resetDeliveriesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function reset_products()
    {

        if ($this->sync_model->resetProductsTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function reset_damage_products()
    {

        if ($this->sync_model->resetDamageProductsTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function update_sales()
    {

        if ($this->sync_model->updateSales()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function update_quotes()
    {

        if ($this->sync_model->updateQuotes()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function update_purchases()
    {

        if ($this->sync_model->updatePurchases()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

    public function update_transfers()
    {

        if ($this->sync_model->updateTransfers()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');

    }

}
