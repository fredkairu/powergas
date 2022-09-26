<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $warehouse_id, $limit = 10)
    {
        $this->db->select('products.id, code, name, type, warehouses_products.quantity, price, tax_rate, tax_method')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');
        if ($this->Settings->overselling) {
            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR warehouses_products.quantity > 0)  AND "
                . "(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        }
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
        public function NewgetProductNames($term,$limit = 10)
    {
        $this->db->select('products.id, code, name, type, warehouses_products.quantity, price, tax_rate, tax_method')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');
        //if ($this->Settings->overselling) {
            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
       // } 
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
 public function updatemvcode($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('movementcodes', $data)) {
            return true;
        }
        return false;
    }
    public function addTicketSale($data = array())
    {
        $this->db->insert('ticket_sales', $data);
    }
	public function addMvcode($data = array())
    {
        if ($this->db->insert('movementcodes', $data)) {
            return true;
        }
        return false;
    }
    public function addTicket($data = array())
    {
        if ($this->db->insert('sma_tickets', $data)) {
            return true;
        }
        return false;
    }
    public function getProductComboItems($pid, $warehouse_id = NULL)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
// 	public function getProductByName($name)
//     {
//         $q = $this->db->get_where('products', array('name' => $name), 1);
//         if ($q->num_rows() > 0) {
//             return $q->row();
//         }
//         return FALSE;
//     }
    
	public function getProductByMarcafaCode($code)
    {
        $q = $this->db->get_where('products', array('mercafar_gmid' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
		public function getProductByDescription2($product_desc,$country,$distributor)
    {
	  $trimmedname=  str_replace(" ","",$product_desc);	   
        $this->db->select('sma_distributor_products.product_id as id,sma_distributor_products.id as uniqueid,sma_products.name as name,sma_products.type,sma_products.code')
            ->join('sma_products', 'sma_distributor_products.product_id = sma_products.id', 'left')
			->join( 'sma_companies', 'sma_companies.id = sma_distributor_products.distributor_id', 'left')
			->join( 'sma_countryproductpricing', 'sma_distributor_products.product_id=sma_countryproductpricing.product_id', 'left')
			->where(" REPLACE(sma_distributor_products.distributor_product_name,' ','')='".$trimmedname."'")
              ->where('sma_companies.name', $distributor)
			  ->where('sma_distributor_products.country',$country)
			  ->group_by('sma_distributor_products.id');
            
        $q = $this->db->get('sma_distributor_products');
       //// $q = $this->db->get_where('products', array('code' => $gmid), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getProductByDescription($product_desc,$country,$distributor)
    {
	  $trimmedname=  str_replace(" ","",$product_desc);	   
        $this->db->select('sma_distributor_products.product_id as id,sma_products.name as name,sma_products.type,sma_products.code')
            ->join('sma_distributor_products', 'sma_distributor_products.product_id = sma_products.id', 'left')
			->join( 'sma_companies', 'sma_companies.id = sma_distributor_products.distributor_id', 'left')
			//->join( 'sma_countryproductpricing', 'sma_distributor_products.product_id=sma_countryproductpricing.product_id', 'left')
			->where(" REPLACE(sma_distributor_products.distributor_product_name,' ','')='".$trimmedname."'")
              ->where('sma_companies.name', $distributor)
			  ->where('sma_distributor_products.country',$country);
            
        $q = $this->db->get('sma_products');
       //// $q = $this->db->get_where('products', array('code' => $gmid), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    		public function getCountries()
    {
      $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
		public function getCustomerByName($name,$countryid=null)
    {
                    if($countryid){
        $q = $this->db->get_where('customers', array('name' => $name,'country'=>$countryid), 1);
                    }else{
                  $q = $this->db->get_where('customers', array('name' => $name), 1);      
                    }
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getDistributorByName($name,$countryid=null)
    {
             if($countryid){
        $q = $this->db->get_where('companies', array('name' => $name,'country'=>$countryid), 1);
                    }else{
                  $q = $this->db->get_where('companies', array('name' => $name), 1);      
                    }
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    	public function getSSOCustomerdistnaming($distributor_id,$customer,$country_id)
    {
        $q = $this->db->get_where('customer_dist_sanofi_mapping', array('distributor_naming' =>$customer,'distributor'=>$distributor_id,'country'=>$country_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     	public function msr_customer_alignments($customer_id,$product_id,$countryid)
    {
        $q = $this->db->get_where('customer_alignments', array('customer_id' => $customer_id,'product_id'=>$product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
          $q1 = $this->db->get_where('customer_alignments', array('customer_id'=>0,'product_id'=>$product_id,'country_id'=>$countryid), 1);
             if ($q1->num_rows() > 0){
          return $q1->row();    
          }else{
            $q1 = $this->db->get_where('customer_alignments', array('product_id'=>$product_id,'country_id'=>$countryid), 1);
            return $q1->row();
          }

        }
        return FALSE;
    }
    
    	public function getSSOCustomerByName($name,$country)
    {
        $q = $this->db->get_where('customers', array('name' => $name,'country'=>$country), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getCustomerByCompany($name)
    {
        $q = $this->db->get_where('companies', array('company' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function checkDuplicateSale($date,$total,$customer_id)
    {
        $q = $this->db->get_where('sales', array('total' => $total,'date' => $date,'customer_id'=>$customer_id), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
public function getProductunifiedPrice($gmid)
    {
        $q = $this->db->get_where('products', array('code' => $gmid), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    	public function getSpecialProductPrices($prce_type,$customerid,$distribid,$gmid,$country,$sdate2)
    {
            //$this->load->model('country_productpricing_model');
           // $this->load->model('products_model');
           //  $this->load->model('settings_model');
            $country=trim($country);
            $finalcountry=str_replace(" ","", $country);

		if($prce_type=='TN'){
		   $prce_filer = 'sma_countryproductpricing.special_tender_price <> "0" ';
		}else{
		   $prce_filer = 'sma_countryproductpricing.special_resell_price <> "0" '; 
		}
		$this->db->select('sma_countryproductpricing.id, sma_countryproductpricing.unified_price,sma_countryproductpricing.supply_price,sma_countryproductpricing.resell_price,sma_countryproductpricing.tender_price,sma_countryproductpricing.special_resell_price,sma_countryproductpricing.special_tender_price,sma_countryproductpricing.country_id,sma_countryproductpricing.product_name')
            ->join( 'sma_currencies', 'sma_currencies.id = sma_countryproductpricing.country_id', 'left')
            ->join('sma_products', 'sma_countryproductpricing.product_id = sma_products.id', 'left')
            ->where('sma_products.code',trim($gmid))
			->where('STR_TO_DATE(sma_countryproductpricing.from_date,"%m/%Y") <= STR_TO_DATE("'.$sdate2.'","%m/%Y")')
			->where('STR_TO_DATE(sma_countryproductpricing.to_date,"%m/%Y") >= STR_TO_DATE("'.$sdate2.'","%m/%Y")')
			->where('sma_countryproductpricing.distributor_id',$distribid)
			->where('sma_countryproductpricing.customer_id',$customerid)
			->where($prce_filer)
            ->where('sma_currencies.country',$finalcountry);
            
        $q = $this->db->get('sma_countryproductpricing');
       /// $q = $this->db->get_where('products', array('code' => $gmid), 1);
        if ($q->num_rows() > 0) {            
            return $q->row();
        }else{
                   return FALSE;
        }
        
    }
	public function getProductPrices($gmid,$country,$sdate2)
    {
            $this->load->model('country_productpricing_model');
              $this->load->model('products_model');
                $this->load->model('settings_model');
            $country=trim($country);
            $finalcountry=str_replace(" ","", $country);
			//$sdate2 = date('m/Y', strtotime($sdate2));
			//echo 
		//SELECT * FROM `sma_countryproductpricing` 
		//LEFT JOIN sma_currencies ON sma_currencies.id = sma_countryproductpricing.country_id 
		//LEFT JOIN sma_products ON sma_countryproductpricing.product_id = sma_products.id  
		//WHERE sma_products.code = '196680' AND sma_currencies.country = 'KE'
		$this->db->select('sma_countryproductpricing.id, sma_countryproductpricing.unified_price,sma_countryproductpricing.supply_price,sma_countryproductpricing.resell_price,sma_countryproductpricing.tender_price,sma_countryproductpricing.country_id,sma_countryproductpricing.product_name,sma_countryproductpricing.promotion')
            ->join( 'sma_currencies', 'sma_currencies.id = sma_countryproductpricing.country_id', 'left')
            ->join('sma_products', 'sma_countryproductpricing.product_id = sma_products.id', 'left')
            ->where('sma_products.code',trim($gmid))
			->where('STR_TO_DATE(sma_countryproductpricing.from_date,"%m/%Y") <= STR_TO_DATE("'.$sdate2.'","%m/%Y")')
			->where('STR_TO_DATE(sma_countryproductpricing.to_date,"%m/%Y") >= STR_TO_DATE("'.$sdate2.'","%m/%Y")')
            ->where('sma_currencies.country',$finalcountry);
            
        $q = $this->db->get('sma_countryproductpricing');
       /// $q = $this->db->get_where('products', array('code' => $gmid), 1);
        if ($q->num_rows() > 0) {            
            return $q->row();
        }else{
            $prd=$this->products_model->getProductByCode($gmid);
            $ctry=$this->settings_model->getCountryByName($country);
            $ctrypring=$this->country_productpricing_model->getCountryProductPricing($prd->id,$ctry->id);
            if (count($ctrypring) > 0) {            
            return $ctrypring;
        }
            return FALSE;
        }
        
    }
	public function remove_data($sales_type,$sdate,$prce_type,$country)
    {
        $convertdate = $sdate.'-01';
		if($sales_type =='SSO'){
		  	$results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$convertdate' AND 	movement_code ='$prce_type' AND country_id='$country')  ");
                        $results=$this->db->query("DELETE FROM sma_consolidated_sales_sso WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$convertdate' AND movement_code ='$prce_type' AND country_id='$country' )  ");
	if($results){
		$results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$convertdate' AND movement_code ='$prce_type' AND country_id='$country' ");     
	return true;
	} else{
		 return FALSE;
	}  
		}
		else{
		  	$results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$convertdate' AND country_id='$country' )  ");     
	if($results){
		$results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$convertdate' AND country_id='$country' ");     
	return true;
	} else{
		 return FALSE;
	}	
	}  
	
        return FALSE;
		
    }
    
    public function removeChequePayment($customer_id)
    {
        if($this->db->delete('sma_customer_payment_methods', array('customer_id' => $customer_id,'payment_method_id' => 3))){
            return true;
        }
        return FALSE;
    }
    
    public function removeInvoicePayment($customer_id)
    {
        if($this->db->delete('sma_customer_payment_methods', array('customer_id' => $customer_id,'payment_method_id' => 4))){
            return true;
        }
        return FALSE;
    }
    
    public function remove_all_data($sales_type,$fromdate,$todate,$country)
    {
        
		if($sales_type =='SSO'){
                    if($country){
		  	$results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate' AND country_id='$country')  ");
                        $results=$this->db->query("DELETE FROM sma_consolidated_sales_sso WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate' AND country_id='$country' )  ");
                    } else{
                    $results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate' )  ");
                        $results=$this->db->query("DELETE FROM sma_consolidated_sales_sso WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate'  )  ");    
                    }
	if($results){
            if($country){
		$results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate'  AND country_id='$country' ");     
            } else{
             $results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN ('$fromdate' AND '$todate')   ");        
            }
	return true;
	} else{
		 return FALSE;
	}  
		}
		else{
                     if($country){
		  	$results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate' AND country_id='$country' ))");     
                     } else{
                       $results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate')");       
                     }
                     
	if($results){
            if($country){
		$results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate' AND country_id='$country' ");
                
              
            }
 else {
    $results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date BETWEEN '$fromdate' AND '$todate'");
}
                
	return true;
	} else{
		 return FALSE;
	}	
	}  
	
        return FALSE;
		
    }
        		public function removeSI_data($SIdistributor,$sales_type,$sdate)
    {
		
		$results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$sdate' AND customer ='$SIdistributor') ");     
	if($results){
		$results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$sdate' AND customer ='$SIdistributor' ");     
	return true;
	} else{
		 return FALSE;
	}
        return FALSE;
		
    }
	/*	public function remove_data($country_details,$sales_type,$sdate)
    {
		
		$results=$this->db->query("DELETE FROM sma_sale_items WHERE sale_id IN(SELECT id FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$sdate' AND country_id = '$country_details' )  ");     
	if($results){
		$results2=$this->db->query("DELETE FROM sma_sales WHERE sales_type = '$sales_type' AND date = '$sdate' AND country_id = '$country_details'  ");     
	return true;
	} else{
		 return FALSE;
	}
        return FALSE;
		
    }*/
    public function getCountryByCode($code)
    {
        $q = $this->db->get_where('currencies', array('country' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCountryByID($id)
    {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCountryByfrench_name($name)
    {
        $q = $this->db->get_where('currencies', array('french_name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getmvcode_details($code,$sales_type)
    {
        $q = $this->db->get_where('movementcodes', array('m_code' => $code,'scenario'=>$sales_type), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	 public function getConversionByMonth($currency_code,$month)
    {
        $q = $this->db->get_where('conversion', array('currency_code' => $currency_code,'month'=> $month), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getClusternameByCode($code)
    {
        $q = $this->db->get_where('cluster', array('id' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

function getClosingSaleDit($data,$monthyear,$stocktype){
        //die(print_r($data));
      
            
            $finalcustomer=$data["customer"];
            $country=$data["country_name"];
            $product_id=$data["product_id"];
      $this->db->select("SUM(pu.shipping) as total_sales,SUM(pu.quantity_units) as qty")
               ->from("sale_items pi")
               ->join('sales pu', 'pu.id=pi.sale_id','left')
              ->where("pu.sales_type",$stocktype)
              ->where("pi.product_id",$product_id)
            ->where("DATE_FORMAT(pu.date,'%m-%Y')='".$monthyear."' AND pu.distributor_id ='$finalcustomer' AND pu.country='$country' ");
        
         $stock = $this->db->get()->row();
        if (is_object($stock)) {
            return  array("value"=>round($stock->total_sales/1000,5),"qty"=>$stock->qty);
        }
        return array();
    }

function getClosingSaleDitNew($data,$monthyear,$countryid,$distributorid,$brand_id=NULL){
     // $distributor=$data["customer"];
           // $country=$data["country_name"];
            $product_id=$data["gmid"];
           if(!empty($data["distributor"])|| !in_array("all",$data["distributor"]) || $data["distributor"]!="undefined"){
      $distributors=  implode(",", $data["distributor"]);
      }
             if($data["grossnet"]){
      $this->db->select("SUM(grand_total) AS total_sales,SUM(quantity_units) as qty");
        $this->db->where("sales_type","SSO");
          $this->db->where("movement_code","VE");
             } else {
               $this->db->select("SUM(grand_total) AS total_sales,SUM(quantity_units) as qty") ;   
                $this->db->where("movement_code","NT");
             }
             
            if($product_id){
                         $this->db->where("gmid",$product_id);
             }
             if($distributors){
                         $this->db->where("distributor_id IN ('".$distributors."')");
             }
             if($data["gbu"] && $data["gbu"] !="all" && !empty($data["gbu"])){
              
              $gbu=$data["gbu"];
               $this->db->where("gbu IN ('".$gbu."') ");
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
             
                if($data["promotion"] && $data["promotion"]!="null"){
                            $this->db->where("promotion",$data["promotion"]);
                }
        //    $this->db->where("upload_type","SALE");
            if($brand_id){
                $this->db->where("DATE_FORMAT(date,'%m-%Y')='".$monthyear."' AND distributor_id ='$distributorid' AND country_id='$countryid' AND brand_id='$brand_id' ");
            }else{
            $this->db->where("DATE_FORMAT(date,'%m-%Y')='".$monthyear."' AND distributor_id ='$distributorid' AND country_id='$countryid' ");
            }
  $this->db->from("sales");
        
         $stock = $this->db->get()->row();
        
            return  array("value"=>round($stock->total_sales/1000,5),"qty"=>$stock->qty);
       
    
}

    public function syncQuantity($sale_id)
    {
        if ($sale_items = $this->getAllInvoiceItems($sale_id)) {
            foreach ($sale_items as $item) {
                $this->site->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->site->syncVariantQty($item->option_id, $item->warehouse_id);
                }
            }
        }
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }
        return FALSE;
    }

    public function getProductOptions($product_id, $warehouse_id, $all = NULL)
    {
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            //->where('warehouses_products_variants.warehouse_id', $warehouse_id)
            ->group_by('product_variants.id');
            if( ! $this->Settings->overselling && ! $all) {
                $this->db->where('warehouses_products_variants.quantity >', 0);
            }
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductVariants($product_id)
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

    public function getItemByID($id)
    {

        $q = $this->db->get_where('sale_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
public function getMvcodeByID($id)
    {

        $q = $this->db->get_where('movementcodes', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    public function getAllInvoiceItems($sale_id)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant,products.family,products.business_unit')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllDiscountItems($sale_id)
    {
        $this->db->select('discount_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant,products.family,products.business_unit')
        ->join('products', 'products.id=discount_items.product_id', 'left')
        ->join('product_variants', 'product_variants.id=discount_items.option_id', 'left')
        ->join('tax_rates', 'tax_rates.id=discount_items.tax_rate_id', 'left')
        ->group_by('discount_items.id')
        ->order_by('id', 'asc');
        $q = $this->db->get_where('discount_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
        $data[] = $row;
        }
        return $data;
        }
        return FALSE;
    }

    public function getAlltemsOnInvoice($sale_id)
    {
        $this->db->select('invoice_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant,products.family,products.business_unit')
        ->join('products', 'products.id=invoice_items.product_id', 'left')
        ->join('product_variants', 'product_variants.id=invoice_items.option_id', 'left')
        ->join('tax_rates', 'tax_rates.id=invoice_items.tax_rate_id', 'left')
        ->group_by('invoice_items.id')
        ->order_by('id', 'asc');
        $q = $this->db->get_where('invoice_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
        $data[] = $row;
        }
        return $data;
        }
        return FALSE;
    }
    public function getAlltemsOnCheque($sale_id)
    {
        $this->db->select('cheque_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant,products.family,products.business_unit')
        ->join('products', 'products.id=cheque_items.product_id', 'left')
        ->join('product_variants', 'product_variants.id=cheque_items.option_id', 'left')
        ->join('tax_rates', 'tax_rates.id=cheque_items.tax_rate_id', 'left')
        ->group_by('cheque_items.id')
        ->order_by('id', 'asc');
        $q = $this->db->get_where('cheque_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
        $data[] = $row;
        }
        return $data;
        }
        return FALSE;
    }
    public function getAllReturnItems($return_id)
    {
        $this->db->select('return_items.*, products.details as details, product_variants.name as variant')
            ->join('products', 'products.id=return_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=return_items.option_id', 'left')
            ->group_by('return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('return_items', array('return_id' => $return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getAllInvoiceItemsWithDetails($sale_id)
    {
        $this->db->select('sale_items.id, sale_items.product_name, sale_items.product_code, sale_items.quantity, sale_items.serial_no, sale_items.tax, sale_items.net_unit_price, sale_items.item_tax, sale_items.item_discount, sale_items.subtotal, products.details');
        $this->db->join('products', 'products.id=sale_items.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getInvoiceByID($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     public function getInvByID($id)
    {
        $q = $this->db->get_where('invoices', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCheqByID($id)
    {
        $q = $this->db->get_where('cheques', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getDiscountByID($id)
    {
        $q = $this->db->get_where('discounts', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

      public function getInvoiceByIDAsArray($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $result=$q->result('array');
            return $result[0];
        }
        return FALSE;
    }
    
    public function getReturnByID($id)
    {
        $q = $this->db->get_where('return_sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getReturnBySID($sale_id)
    {
        $q = $this->db->get_where('return_sales', array('sale_id' => $sale_id), 1);
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

    public function getPurchasedItems($product_id, $warehouse_id, $option_id = NULL)
    {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, item_tax');
        $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        if ($option_id) {
            $this->db->where('option_id', $option_id);
        }
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function updateOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('product_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('product_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
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

    public function updateProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addSale($data = array(), $items = array(), $payment = array())
    {

        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);
        //die(print_r($data));
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                //enforce sales invoice
                 $q = $this->db->get_where('sales', array('reference_no' =>$data['reference_no']), 1);
        if ($q->num_rows() > 0) {
           $this->site->updateReference('so');
        }
                $this->site->updateReference('so');
            }
            foreach ($items as $item) {

                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        $item_cost['sale_item_id'] = $sale_item_id;
                        $item_cost['sale_id'] = $sale_id;
                        if(! isset($item_cost['pi_overselling'])) {
                            $this->db->insert('costing', $item_cost);
                        }
                    }

                }
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            //post sale invoice to erp
            //CR VAT,CR SALES,DR A/R
            
           
            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
                $payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                    unset($payment['gc_balance']);
                    $this->db->insert('payments', $payment);
                } else {
                    $this->db->insert('payments', $payment);
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }
                $this->site->syncSalePayments($sale_id);
                 //$this->postSaleInvoice($data);

            }

            $this->site->syncQuantity($sale_id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            return true;

        }

        return false;
    }

    
   public function addSale2($data = array(), $items = array(), $payments = array(), $vehicle_id, $bank_acc_id)
    {

        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);
        //die(print_r($data));
        if($this->checkDuplicateSale($data['date'],$data['total'],$data['customer_id'])==FALSE)
        {
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                //enforce sales invoice
                 $q = $this->db->get_where('sales', array('reference_no' =>$data['reference_no']), 1);
            if ($q->num_rows() > 0) {
               $this->site->updateReference('so');
            }
                $this->site->updateReference('so');
            }
            foreach ($items as $item) {
                $this->updateVehicleProductsQuantities($item,$data['distributor_id'],$vehicle_id);
                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        $item_cost['sale_item_id'] = $sale_item_id;
                        $item_cost['sale_id'] = $sale_id;
                        if(! isset($item_cost['pi_overselling'])) {
                            $this->db->insert('costing', $item_cost);
                        }
                    }

                }
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            //post sale invoice to erp
            //CR VAT,CR SALES,DR A/R
            
           
            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' || $data['payment_status'] == 'unpaid' && !empty($payments)) {
                //$payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                    unset($payment['gc_balance']);
                    $this->db->insert('payments', $payment);
                } else {
                    $actual_payment = array();
                    foreach($payments->items as $pymnt){
                        
                        $actual_payment = array(
                            'date' => date("Y-m-d"),
                            'sale_id' => $sale_id,
                            'reference_no' => $this->site->getReference('pay'),
                            'amount' => $this->sma->formatDecimal($pymnt->amount),
                            'paid_by' => $pymnt->paid_by,
                            'cheque_no' => $pymnt->cheque_no,
                            'cc_no' => null,
                            'cc_holder' => null,
                            'cc_month' => null,
                            'cc_year' => null,
                            'cc_type' => null,
                            'created_by' => $data['salesman_id'],
                            'note' => null,
                            'type' => $pymnt->type
                        );
                        
                        $this->db->insert('sma_payments', $actual_payment);
                        $json = array();
    			
    			        if($actual_payment['paid_by']=="Cash Payment"){
                        	
                    		$data2 = array(
                    			'CustId' => $data['customer_id'],
                    			'TransactionRef' => $actual_payment['reference_no'],
                    			'TransDate' => $actual_payment['date'],
                    			'BankAcct' => $bank_acc_id,
                    			'Amount' => $actual_payment['amount']);
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
                        
                        	
                        }else if($actual_payment['paid_by']=="Mpesa Payment"){
                        	
                    		$data2 = array(
                    			'CustId' => $data['customer_id'],
                    			'TransactionRef' => $actual_payment['reference_no'],
                    			'TransDate' => $actual_payment['date'],
                    			'BankAcct' => '15',
                    			'Amount' => $actual_payment['amount']);
                    		$json[] = $data2;
                    		$json_data = json_encode($json);
                             $myfile = fopen("payment.txt", "w");
                             fwrite($myfile,  $json_data);
                             fclose($myfile);
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
                        
                        	
                        }
            			
            			
            			
                    }
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }
                //$this->site->syncSalePayments($sale_id);
                 //$this->postSaleInvoice($data);

            }

            $this->site->syncQuantity($sale_id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            return $sale_id;

        }
        }
        else
        {
         $sale_id="duplicate";  
         return $sale_id;
        }
        
        return false;
    }
    
    
    /**addInvoicePayment-start**/
    
    public function addInvoicePayment($data = array(), $items = array(), $payments = array(), $vehicle_id, $sale_id)
    {

        
        if (isset($sale_id)) {
            
           
            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' || $data['payment_status'] == 'unpaid' && !empty($payments)) {
                //$payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                    unset($payment['gc_balance']);
                    $this->db->insert('invoice_payments', $payment);
                } else {
                    $actual_payment = array();
                    foreach($payments->items as $pymnt){
                        
                        $actual_payment = array(
                            'date' => date("Y-m-d"),
                            'sale_id' => $sale_id,
                            'reference_no' => $this->site->getReference('pay'),
                            'amount' => $this->sma->formatDecimal($pymnt->amount),
                            'paid_by' => $pymnt->paid_by,
                            'cheque_no' => $pymnt->cheque_no,
                            'cc_no' => null,
                            'cc_holder' => null,
                            'cc_month' => null,
                            'cc_year' => null,
                            'cc_type' => null,
                            'created_by' => $data['salesman_id'],
                            'note' => null,
                            'type' => $pymnt->type
                        );
                        
                        $this->db->insert('sma_invoice_payments', $actual_payment);
                        $json = array();
    			
    			        
                        
                        
            			
            			
            			
                    }
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }

            }

        }
        
        return false;
    }
    /**addInvoicePayment-end**/
    
    /**addChequePayment-start**/
    
    public function addChequePayment($data = array(), $items = array(), $payments = array(), $vehicle_id, $sale_id)
    {

        
        if (isset($sale_id)) {
            
           
            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' || $data['payment_status'] == 'unpaid' && !empty($payments)) {
                //$payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                    unset($payment['gc_balance']);
                    $this->db->insert('cheque_payments', $payment);
                } else {
                    $actual_payment = array();
                    foreach($payments->items as $pymnt){
                        
                        $actual_payment = array(
                            'date' => date("Y-m-d"),
                            'sale_id' => $sale_id,
                            'reference_no' => $this->site->getReference('pay'),
                            'amount' => $this->sma->formatDecimal($pymnt->amount),
                            'paid_by' => $pymnt->paid_by,
                            'cheque_no' => $pymnt->cheque_no,
                            'cc_no' => null,
                            'cc_holder' => null,
                            'cc_month' => null,
                            'cc_year' => null,
                            'cc_type' => null,
                            'created_by' => $data['salesman_id'],
                            'note' => null,
                            'type' => $pymnt->type
                        );
                        
                        $this->db->insert('sma_cheque_payments', $actual_payment);
                        $json = array();
    			
    			        
                        
                        
            			
            			
            			
                    }
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }

            }

        }
        
        return false;
    }
    /**addChequePayment-end**/
    
    public function addDiscount($data = array(), $items = array(), $vehicle_id)
    {

        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);
        //die(print_r($data));
        
        if ($this->db->insert('discounts', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                //enforce sales invoice
                 $q = $this->db->get_where('discounts', array('reference_no' =>$data['reference_no']), 1);
            if ($q->num_rows() > 0) {
               $this->site->updateReference('so');
            }
               $this->site->updateReference('so');
            }
            foreach ($items as $item) {
                //$this->updateVehicleProductsQuantities($item,$data['distributor_id'],$vehicle_id);
                $item['sale_id'] = $sale_id;
                $this->db->insert('discount_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        $item_cost['sale_item_id'] = $sale_item_id;
                        $item_cost['sale_id'] = $sale_id;
                        if(! isset($item_cost['pi_overselling'])) {
                            $this->db->insert('costing', $item_cost);
                        }
                    }

                }
            }

            return $sale_id;

        }
        return false;
    }
    
    public function updateDiscount($discount_id)
    {
        
        if ($this->db->update('discounts',
                $data = array(
                    'sold' => 1),
                array(
                    'id' => $discount_id))) {
            
            return true;

        }
        return false;
    }
    
    public function addInvoice($data = array(), $items = array(), $vehicle_id)
    {

        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);
        //die(print_r($data));
        
        if ($this->db->insert('invoices', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                //enforce sales invoice
                 $q = $this->db->get_where('invoices', array('reference_no' =>$data['reference_no']), 1);
            if ($q->num_rows() > 0) {
               $this->site->updateReference('so');
            }
               $this->site->updateReference('so');
            }
            foreach ($items as $item) {
                //$this->updateVehicleProductsQuantities($item,$data['distributor_id'],$vehicle_id);
                $item['sale_id'] = $sale_id;
                $this->db->insert('invoice_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        $item_cost['sale_item_id'] = $sale_item_id;
                        $item_cost['sale_id'] = $sale_id;
                        if(! isset($item_cost['pi_overselling'])) {
                            $this->db->insert('costing', $item_cost);
                        }
                    }

                }
            }

            return $sale_id;

        }
        return false;
    }
    
    public function addCheque($data = array(), $items = array(), $vehicle_id)
    {

        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);
        //die(print_r($data));
        
        if ($this->db->insert('cheques', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                //enforce sales invoice
                 $q = $this->db->get_where('cheques', array('reference_no' =>$data['reference_no']), 1);
            if ($q->num_rows() > 0) {
               $this->site->updateReference('so');
            }
               $this->site->updateReference('so');
            }
            foreach ($items as $item) {
                //$this->updateVehicleProductsQuantities($item,$data['distributor_id'],$vehicle_id);
                $item['sale_id'] = $sale_id;
                $this->db->insert('cheque_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        $item_cost['sale_item_id'] = $sale_item_id;
                        $item_cost['sale_id'] = $sale_id;
                        if(! isset($item_cost['pi_overselling'])) {
                            $this->db->insert('costing', $item_cost);
                        }
                    }

                }
            }

            return $sale_id;

        }
        return false;
    }
    
    public function updateInvoice($invoice_id)
    {
        
        if ($this->db->update('invoices',
                $data = array(
                    'sold' => 1),
                array(
                    'id' => $invoice_id))) {
            
            return true;

        }
        return false;
    }
    
    public function updateCheque($cheque_id)
    {
        
        if ($this->db->update('cheques',
                $data = array(
                    'sold' => 1),
                array(
                    'id' => $cheque_id))) {
            
            return true;

        }
        return false;
    }
    public function  updateVehicleProductsQuantities($item, $distributor_id, $vehicle_id){

        //check if product id exists in products_distributor_quantities
        $q = $this->db->get_where('sma_product_vehicle_quantities', array(
            'product_id' => $item['product_id'],
            'distributor_id' => $distributor_id,
            'vehicle_id' => $vehicle_id));
        if ($q->num_rows() > 0) {
            //get the current quantity and add the new quantity then update the record
            $new_quantity = $q->row()->quantity - $item['quantity'];

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
                'quantity' => -$item['quantity']
            );
            if ($this->db->insert('sma_product_vehicle_quantities', $data)) {
                return true;
            }else{
                return false;
            }
        }


    }
    
	public function addSale_bycsv($data = array(), $items = array(), $payment = array())
    {

        //$cost = $this->site->costing($items);
      // $this->sma->print_arrays($items);
		// die();
//die(print_r($items));
        //if ($this->db->insert('sales', $data)) {
           // $sale_id = $this->db->insert_id();
       
			        $i = 0;  
               foreach ($data as $dt) {

                
				$dt['reference_no'] = $this->site->getReference('so');
                 if(strtolower($dt["promotion"])=="promoted"){$dt["promotion"]=1;} else {$dt["promotion"]=0;}
				$this->db->insert('sma_sales', $dt);
                $sale_id = $this->db->insert_id();
				$this->site->updateReference('so');
            //update consolidated
                                if($dt["sales_type"]=="SSO"){
                                   
                if($dt["tender_price"]){
                    $this->updateConsolidatedSSO(array("upload_type"=>"SALE","promotion"=>$dt["promotion"],"country"=>$dt["country"],"country_id"=>$dt["country_id"],"gmid"=>$dt["gmid"],"product_name"=>$dt["products"],"monthyear"=>$dt["date"],"customer_sanofi"=>$dt["customer"],"customer_id"=>$dt["customer_id"],"distributor"=>$dt["distributor"],"distributor_id"=>$dt["distributor_id"],"gross_sale"=>0,"gross_qty"=>$dt['quantity_units'],"tender_sale"=>$dt["tender_price"],"msr_id"=>$dt["msr_alignment_id"],"msr_name"=>$dt["msr_alignment_name"],"movement_code"=>$dt["movement_code"],"sale_id"=>$sale_id,"session_id"=>$dt["session_id"]));
                } else{
             $this->updateConsolidatedSSO(array("upload_type"=>"SALE","promotion"=>$dt["promotion"],"country"=>$dt["country"],"country_id"=>$dt["country_id"],"gmid"=>$dt["gmid"],"product_name"=>$dt["products"],"monthyear"=>$dt["date"],"customer_sanofi"=>$dt["customer"],"customer_id"=>$dt["customer_id"],"distributor"=>$dt["distributor"],"distributor_id"=>$dt["distributor_id"],"gross_sale"=>$dt["grand_total"],"gross_qty"=>$dt['quantity_units'],"tender_sale"=>$dt["tender_price"],"msr_id"=>$dt["msr_alignment_id"],"msr_name"=>$dt["msr_alignment_name"],"movement_code"=>$dt["movement_code"],"sale_id"=>$sale_id,"session_id"=>$dt["session_id"]));
                }  
                                }
            
 //   echo "$ar[$i] \n";
//}
                $items[$i]['sale_id'] = $sale_id;
				
                $this->db->insert('sale_items', $items[$i]);
				
               
           
			$i++;
				  }
                                  
     //$this->db->update('sales', array('promotion' =>1), array('promotion' =>'promoted'));
    // $this->db->update('sales', array('promotion' =>0), array('promotion' =>'non-promoted'));
                                  
           // if ($data['sale_status'] == 'completed') {
           //     $this->site->syncPurchaseItems($cost);
          //  }

            //post sale invoice to erp
            //CR VAT,CR SALES,DR A/R
            
           
           

            //$this->site->syncQuantity($sale_id);
            //$this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            return true;

        //}

       // return false;
    }

	
    public function updateSale($id, $data, $items = array())
    {
        $this->resetSaleActions($id);

        if ($data['sale_status'] == 'completed') {
            $cost = $this->site->costing($items);
        }

        if ($this->db->update('sales', $data, array('id' => $id)) && $this->db->delete('sale_items', array('sale_id' => $id))) {

            foreach ($items as $item) {

                $item['sale_id'] = $id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {
                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        $item_cost['sale_item_id'] = $sale_item_id;
                        $item_cost['sale_id'] = $id;
                        if(! isset($item_cost['pi_overselling'])) {
                            $this->db->insert('costing', $item_cost);
                        }
                    }
                }

            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            $this->site->syncQuantity($id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            return true;

        }
        return false;
    }

    public function deleteSale($id)
    {
        $sale_items = $this->resetSaleActions($id);
        if ($this->db->delete('payments', array('sale_id' => $id)) &&
        $this->db->delete('sale_items', array('sale_id' => $id)) &&
        $this->db->delete('sales', array('id' => $id))) {
            if ($return = $this->getReturnBySID($id)) {
                $this->deleteReturn($return->id);
            }
            $this->site->syncQuantity(NULL, NULL, $sale_items);
            return true;
        }
        return FALSE;
    }

    
        public function deleteDeletedSale($id)
    {
        
        if ($this->db->delete('deleted_sales', array('id' => $id))) {
    
            return true;
        }
        return FALSE;
    }
    public function resetSaleActions($id)
    {
        $sale = $this->getInvoiceByID($id);
        $items = $this->getAllInvoiceItems($id);
        foreach ($items as $item) {

            if ($sale->sale_status == 'completed') {
                if ($costings = $this->getCostingLines($item->id, $item->product_id)) {
                    $quantity = $item->quantity;
                    foreach ($costings as $cost) {
                        if ($cost->quantity >= $quantity) {
                            $qty = $cost->quantity - $quantity;
                            $bln = $cost->quantity_balance ? $cost->quantity_balance + $quantity : $quantity;
                            $this->db->update('costing', array('quantity' => $qty, 'quantity_balance' => $bln), array('id' => $cost->id));
                            $quantity = 0;
                        } elseif ($cost->quantity < $quantity) {
                            $qty = $quantity - $cost->quantity;
                            $this->db->delete('costing', array('id' => $cost->id));
                            $quantity -= $qty;
                        }
                        if ($quantity == 0) {
                            break;
                        }
                    }
                    $this->updatePurchaseItem($cost->purchase_item_id, $item->quantity, $cost->sale_item_id);
                }
            }

        }
        $this->sma->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, TRUE);
        return $items;
    }

    public function deleteReturn($id)
    {
        if ($this->db->delete('return_items', array('return_id' => $id)) && $this->db->delete('return_sales', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function updatePurchaseItem($id, $qty, $sale_item_id)
    {
        if ($id) {
            if($pi = $this->getPurchaseItemByID($id)) {
                $bln = $pi->quantity_balance + $qty;
                $this->db->update('purchase_items', array('quantity_balance' => $bln), array('id' => $id));
            }
        } else {
            if ($sale_item = $this->getSaleItemByID($sale_item_id)) {
                $option_id = isset($sale_item->option_id) && !empty($sale_item->option_id) ? $sale_item->option_id : NULL;
                $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $sale_item->product_id, 'warehouse_id' => $sale_item->warehouse_id, 'option_id' => $option_id);
                if ($pi = $this->site->getPurchasedItem($clause)) {
                    $quantity_balance = $pi->quantity_balance+$qty;
                    $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), $clause);
                } else {
                    $clause['quantity'] = 0;
                    $clause['quantity_balance'] = $qty;
                    $this->db->insert('purchase_items', $clause);
                }
            }
        }
    }

    public function getPurchaseItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function returnSale($data = array(), $items = array(), $payment = array())
    {

        foreach ($items as $item) {
            if ($costings = $this->getCostingLines($item['sale_item_id'], $item['product_id'])) {
                $quantity = $item['quantity'];
                foreach ($costings as $cost) {
                    if ($cost->quantity >= $quantity) {
                        $qty = $cost->quantity - $quantity;
                        $bln = $cost->quantity_balance && $cost->quantity_balance >= $quantity ? $cost->quantity_balance - $quantity : 0;
                        $this->db->update('costing', array('quantity' => $qty, 'quantity_balance' => $bln), array('id' => $cost->id));
                        $quantity = 0;
                    } elseif ($cost->quantity < $quantity) {
                        $qty = $quantity - $cost->quantity;
                        $this->db->delete('costing', array('id' => $cost->id));
                        $quantity = $qty;
                    }
                }
                $this->updatePurchaseItem($cost->purchase_item_id, $item['quantity'], $cost->sale_item_id);
            }

        }
        //$this->sma->print_arrays($items);
        $sale_items = $this->site->getAllSaleItems($data['sale_id']);

        if ($this->db->insert('return_sales', $data)) {
            $return_id = $this->db->insert_id();
            if ($this->site->getReference('re') == $data['reference_no']) {
                $this->site->updateReference('re');
            }
            foreach ($items as $item) {
                $item['return_id'] = $return_id;
                $this->db->insert('return_items', $item);

                if ($sale_item = $this->getSaleItemByID($item['sale_item_id'])) {
                    if ($sale_item->quantity == $item['quantity']) {
                        $this->db->delete('sale_items', array('id' => $item['sale_item_id']));
                    } else {
                        $nqty = $sale_item->quantity - $item['quantity'];
                        $tax = $sale_item->unit_price - $sale_item->net_unit_price;
                        $discount = $sale_item->item_discount / $sale_item->quantity;
                        $item_tax = $tax * $nqty;
                        $item_discount = $discount * $nqty;
                        $subtotal = $sale_item->unit_price * $nqty;
                        $this->db->update('sale_items', array('quantity' => $nqty, 'item_tax' => $item_tax, 'item_discount' => $item_discount, 'subtotal' => $subtotal), array('id' => $item['sale_item_id']));
                    }

                }
            }
            $this->calculateSaleTotals($data['sale_id'], $return_id, $data['surcharge']);
            if (!empty($payment)) {
                $payment['sale_id'] = $data['sale_id'];
                $payment['return_id'] = $return_id;
                $this->db->insert('payments', $payment);
                if ($this->site->getReference('pay') == $data['reference_no']) {
                    $this->site->updateReference('pay');
                }
                $this->site->syncSalePayments($data['sale_id']);
            }
            $this->site->syncQuantity(NULL, NULL, $sale_items);
            return true;
        }
        return false;
    }

    public function getCostingLines($sale_item_id, $product_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('costing', array('sale_item_id' => $sale_item_id, 'product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSaleItemByID($id)
    {
        $q = $this->db->get_where('sale_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getUnpaidAndPartialPaidSalesByCustomerId($customer_id)
    {
        $this->db->select('*');
        $this->db->from('sales');
        $this->db->where(array('customer_id' => $customer_id));
        $this->db->or_where(array('payment_status' => 'unpaid','payment_status' => 'partial'));
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getPendingSales($sale_id)
    {
        $this->db->select('*');
        $this->db->from('payments');
        $this->db->where(array('sale_id' => $sale_id,'type' => 'pending'));
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function calculateSaleTotals($id, $return_id, $surcharge)
    {
        $sale = $this->getInvoiceByID($id);
        $items = $this->getAllInvoiceItems($id);
        if (!empty($items)) {
            $this->sma->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, TRUE);
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $total_items = 0;
            foreach ($items as $item) {
                $total_items += $item->quantity;
                $product_tax += $item->item_tax;
                $product_discount += $item->item_discount;
                $total += $item->net_unit_price * $item->quantity;
            }
            if ($sale->order_discount_id) {
                $percentage = '%';
                $order_discount_id = $sale->order_discount_id;
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (($total + $product_tax) * (Float)($ods[0])) / 100;
                } else {
                    $order_discount = $order_discount_id;
                }
            }
            if ($sale->order_tax_id) {
                $order_tax_id = $sale->order_tax_id;
                if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                    if ($order_tax_details->type == 2) {
                        $order_tax = $order_tax_details->rate;
                    }
                    if ($order_tax_details->type == 1) {
                        $order_tax = (($total + $product_tax - $order_discount) * $order_tax_details->rate) / 100;
                    }
                }
            }
            $total_discount = $order_discount + $product_discount;
            $total_tax = $product_tax + $order_tax;
            $grand_total = $total + $total_tax + $sale->shipping - $order_discount + $surcharge;
            $data = array(
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'total_items' => $total_items,
                'return_id' => $return_id,
                'surcharge' => $surcharge
            );

            if ($this->db->update('sales', $data, array('id' => $id))) {
                $this->sma->update_award_points($data['grand_total'], $sale->customer_id, $sale->created_by);
                return true;
            }
        } else {
            $this->db->delete('sales', array('id' => $id));
            //$this->db->delete('payments', array('sale_id' => $id, 'return_id !=' => $return_id));
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

    public function addDelivery($data = array())
    {
        if ($this->db->insert('deliveries', $data)) {
            if ($this->site->getReference('do') == $data['do_reference_no']) {
                $this->site->updateReference('do');
            }
            return true;
        }
        return false;
    }

    public function updateDelivery($id, $data = array())
    {
        if ($this->db->update('deliveries', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getDeliveryByID($id)
    {
        $q = $this->db->get_where('deliveries', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteDelivery($id)
    {
        if ($this->db->delete('deliveries', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getInvoicePayments($sale_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
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

    public function getPaymentsForSale($sale_id)
    {
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.cc_no, payments.cheque_no, payments.id, companies.name, type')
            ->join('companies', 'companies.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
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
        //die(print_r($data));
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('pay') == $data['reference_no']) {
                $this->site->updateReference('pay');
            }
            $this->site->syncSalePayments($data['sale_id']);
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', array('balance' => ($gc->balance - $data['amount'])), array('card_no' => $data['cc_no']));
            }
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        if ($this->db->update('payments', $data, array('id' => $id))) {
            //$this->site->syncSalePayments($data['sale_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->site->syncSalePayments($opay->sale_id);
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

    /* ----------------- Gift Cards --------------------- */

    public function addGiftCard($data = array(), $ca_data = array(), $sa_data = array())
    {
        if ($this->db->insert('gift_cards', $data)) {
            if (!empty($ca_data)) {
                $this->db->update('companies', array('award_points' => $ca_data['points']), array('id' => $ca_data['customer']));
            } elseif (!empty($sa_data)) {
                $this->db->update('users', array('award_points' => $sa_data['points']), array('id' => $sa_data['user']));
            }
            return true;
        }
        return false;
    }

    public function updateGiftCard($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('gift_cards', $data)) {
            return true;
        }
        return false;
    }
    
    public function approveDeclineDiscount($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('discounts', $data)) {
            return true;
        }
        return false;
    }

  public function approveDeclineInvoice($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('invoices', $data)) {
            return true;
        }
        return false;
    }
    public function approveDeclineCheque($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('cheques', $data)) {
            return true;
        }
        return false;
    }
    public function deleteGiftCard($id)
    {
        if ($this->db->delete('gift_cards', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getPaypalSettings()
    {
        $q = $this->db->get_where('paypal', array('id' => 1));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSkrillSettings()
    {
        $q = $this->db->get_where('skrill', array('id' => 1));
        if ($q->num_rows() > 0) {
            return $q->row();
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
    


       public function getStaff()
    {
        if (!$this->Owner) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
        public function getCashier()
    {
        if (!$this->Owner) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id =', 9);
        $q = $this->db->get('users');
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

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     public  function getBudgetForecastForMonth($data,$month,$budgetorforecast,$salestype){
        // die(print_r($month));
        
             if($data["grossnet"]){
              
              $this->db->select('SUM(gross_budget) as resale')
                     
//                 ->join("products", "budget.product_id=products.id", 'left')
//                      ->where("scenario='".$salestype."'")
//                      ->where("net_gross='G'")
//                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("DATE_FORMAT(monthyear,'%Y-%m') IN ($month)");
                
              
        }else{
             
               $this->db->select('SUM(net_budget) as resale')
                      
                                              ->where("DATE_FORMAT(monthyear,'%Y-%m') IN ($month)");
                           
        }
                 
           if(count($data["customer"])>0 && !empty($data["customer"][0])&& !in_array("all",$data["customer"])){
       
            foreach ($data["customer"] as $cust) {
				if($cust){
                $clusterss.="'".$cust."',";
				}
                     }
	$valueee=rtrim($clusterss,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("consolidated_sales_sso.customer_id IN (".$valueee.")");
        }
        
        
        
        if(count($data["distributor"])>0 && !empty($data["distributor"][0])&& !in_array("all",$data["distributor"])){
       
            foreach ($data["distributor"] as $cust) {
				if($cust){
                $distributors.="'".$cust."',";
				}
                     }
	$valuee=rtrim($distributors,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("consolidated_sales_sso.distributor_id IN (".$valuee.")");
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
     if($data["product"] && !in_array("all",$data["product"])){
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("consolidated_sales_sso.gmid IN (".$prods.")");
} 

if($data["gbu"] && $data["gbu"] !="all"){
    
$this->db->where('consolidated_sales_sso.bu',$data["gbu"]);	
}

if($data["promotion"] && $data["promotion"] !="all"){
$this->db->where('consolidated_sales_sso.promotion',$data["promotion"]);	
}

  if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
	$valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("consolidated_sales_sso.country_id IN (".$valuee.")");
        }
            $q=$this->db->get('consolidated_sales_sso');
             
            $value=$q->row();                
          if($value){
              return round($value->resale/1000,5);
          }else{
           return 0;   
          }
             
         }
    
   function getSalesTotals($data){
       // die(print_r($data));
       if($data["grossnet"]){
 $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
          ->where("movement_code='VE'")
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                    ->group_by('sales_type');  
       }
       else{
                 $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
               ->where("movement_code='NT'")
                         ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                    ->group_by('sales_type');
         
       }
					if($data["datefrom"] && $data["dateto"]){
            $datefrom="01-".str_replace("/","-",$data["datefrom"]);
			$datefromm=date("Y-m-d",strtotime($datefrom));
            $dateto="31-".str_replace("/","-",$data["dateto"]);
			$datetoo=date("Y-m-d",strtotime($dateto));
		 $this->db->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $datefromm . '" and "' . $datetoo . '"');	
       // $this->db->where("date BETWEEN '".$datefromm."' AND '".$datetoo."'");   
       
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
      if(count($data["countrys"])>0 && !empty($data["countrys"][0]) && !in_array("all",$data["countrys"])){
       //die(print_r($data["countrys"]));
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
					 $valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("sales.country_id IN (".$valuee.")");
        }
        
        
   
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
         $this->db->where('products.category_id',$data["productcategoryfamily"][0]);                                                                                                                                                                                                    
    for($i=1;$i<count($data["productcategoryfamily"]);$i++) {
    $this->db->or_where('products.category_id',$data["productcategoryfamily"][$i]); 
    }

}
if($data["gbu"] && $data["gbu"] !="all"){
    $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('products.promoted', $data["promotion"]);	
}
            $q=$this->db->get('sales');
            $dataa=array();
            $colors=array("#ccc","#349ef3","#c98010");
            $i=0;
             if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                
                $datax["sale"] = $row->sale;
               if(strtolower($row->sale)=="pso"|| strtolower($row->sale)=="sso"){
                    $datax["value"] = round($row->resale/1000,5);
                }
                else{
                $datax["value"] = round($row->value/1000,5);
                }
                $datax["color"]=$colors[$i];
                array_push($dataa, $datax);
                $i++;
            }
        return json_encode($dataa);
             }
        else{
             return json_encode(array("sale"=>0,"value"=>0));
        }
        
        
    
    }
    
    
	 function getLastYearSalesTotal($data,$year){
             if($data["grossnet"]){
       $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                ->where("movement_code='VE'")
               ->where("DATE_FORMAT(date,'%Y')='".$year."'")
                    ->group_by('sales_type');
             }else{
              $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                       ->where("movement_code='NT'")
                      ->where("DATE_FORMAT(date,'%Y')='".$year."'")
                    ->group_by('sales_type');    
             }
      if(!empty($data["cluster"]) && $data["cluster"]!="all"){
       

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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
    
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")"); 

}if($data["gbu"] && $data["gbu"] !="all"){
    $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('products.promoted', $data["promotion"]);	
}
            $q=$this->db->get('sales');
            $dataa=array();
            $colors=array("#ccc","#349ef3","#c98010");
            $i=0;
             if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->sale){
                $datax["sale"] = $row->sale;
                if(strtolower($row->sale)=="pso"|| strtolower($row->sale)=="sso"){
                    $datax["value"] = round($row->resale/1000,5);
                }
                else{
                $datax["value"] = round($row->value/1000,5);
                }
                $datax["color"]=$colors[$i];
                array_push($dataa, $datax);
                $i++;
            }
            }
        return json_encode($dataa);
             }
        else{
             return json_encode(array("sale"=>0,"value"=>0));
        }
          
        
    }
	 }
         
          function getYearSalesTotal($data,$year,$salestype){
             if($data["grossnet"]){
       $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
               ->where("DATE_FORMAT(date,'%Y')='".$year."'")
               ->where("sales_type='".$salestype."'");
             }else{
              $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                       ->where("movement_code='VE'")
                      ->where("DATE_FORMAT(date,'%Y')='".$year."'")
                    ->where("sales_type='".$salestype."'");   
             }
      if(!empty($data["cluster"]) && $data["cluster"]!="all"){
       

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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
    
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")"); 

}

if($data["gbu"] && $data["gbu"] !="all"){
    $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('products.promoted', $data["promotion"]);	
}
            $q=$this->db->get('sales');
           
            $i=0;
             if ($q->num_rows() > 0) {
           $sale=$q->row();
           return round($sale->resale/1000,5);
             }
        else{
            return 0;
        }
          
        
    }
	 }
         
         //consolidated table
         function getYearSalesTotalConsolidated($data,$year,$salestype){
               if($data["grossnet"]){
       $this->db->select('SUM(grand_total) as resale')
                                     ->where("movement_code","VE")
 ->where("sales_type",$salestype)
                  ->where("DATE_FORMAT(date,'%Y')='".$year."'");
               
             }else{
             $this->db->select('SUM(grand_total) as resale')
                                     ->where("movement_code","NT")
->where("sales_type",$salestype)
               
                      ->where("DATE_FORMAT(date,'%Y')='".$year."'");
                      
             }
     // if(!empty($data["cluster"]) && $data["cluster"]!="all"){
       

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
            $q=$this->db->get('sales');
           
            $i=0;
             if ($q->num_rows() > 0) {
           $sale=$q->row();
        // $this->db->flush_cache();
           return round($sale->resale/1000,5);
             }
        else{
            return 0;
        }
          
        
   // }
   // exit();
         }
                 
         
         
         
    function getYearBudgetTotal($data,$year,$scenario,$budgetforecast){
        $budgetforecast=strtolower($budgetforecast);
           if($data["grossnet"]){
       $this->db->select('SUM(budget_value) as value')
                  ->join("products", "budget.product_id=products.id", 'left')
               ->where("DATE_FORMAT(date,'%Y')='".$year."'") //year 
                ->where("budget_forecast='".$budgetforecast."'")
                ->where("net_gross='G'")
                ->where("scenario='".$scenario."'");
           } else{
               $this->db->select('SUM(budget_value) as value')
                  ->join("products", "budget.product_id=products.id", 'left')
               ->where("DATE_FORMAT(date,'%Y')='".$year."'") //year 
                ->where("budget_forecast='".$budgetforecast."'")
                        ->where("net_gross='N'")
                ->where("scenario='".$scenario."'"); 
           }
      
       

          if(count($data["countrys"])>0 && !empty($data["countrys"][0]) && !in_array("all",$data["countrys"])){
    
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
					 $valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("budget.country IN (".$valuee.")");
        }
      if(count($data["customer"])>0 && !empty($data["customer"][0])&& !in_array("all",$data["customer"])){
        
            foreach ($data["customer"] as $cust) {
				if($cust){
                $clusterss.="'".$cust."',";
        }
                     }
	$valueee=rtrim($clusterss,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("budget.customer_id IN (".$valueee.")");
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
   if(count($data["distributor"])>0 && !empty($data["distributor"][0])&& !in_array("all",$data["distributor"])){
   
            foreach ($data["distributor"] as $cust) {
				if($cust){
                $distributors.="'".$cust."',";
				}
                     }
	$valueee=rtrim($distributors,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("budget.distributor_id IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
    
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")"); 

}if($data["gbu"] && $data["gbu"] !="all"){
    $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('products.promoted', $data["promotion"]);	
}
            $q=$this->db->get('budget');
            $dataa=array();
            $colors=array("#ccc","#349ef3","#c98010");
            $i=0;
             if ($q->num_rows() > 0) {
             $budget= $q->row();
             return round($budget->value/1000,5);
             }
        else{
             return 0;
        }
          
        
    
	 }
    function getYearBudgetTotalConsolidated($data,$year,$scenario,$budgetforecast){
        $budgetforecast=strtolower($budgetforecast);
           if($data["grossnet"]){
       $this->db->select('SUM(gross_budget) as value')
                 
               ->where("DATE_FORMAT(monthyear,'%Y')='".$year."'"); //year 
                
           } else{
               $this->db->select('SUM(net_budget) as value')
                 
               ->where("DATE_FORMAT(monthyear,'%Y')='".$year."'"); //year 
           }
      
       

          if(count($data["countrys"])>0 && !empty($data["countrys"][0]) && !in_array("all",$data["countrys"])){
    
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
					 $valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("consolidated_sales_sso.country_id IN (".$valuee.")");
        }
      if(count($data["customer"])>0 && !empty($data["customer"][0])&& !in_array("all",$data["customer"])){
        
            foreach ($data["customer"] as $cust) {
				if($cust){
                $clusterss.="'".$cust."',";
        }
                     }
	$valueee=rtrim($clusterss,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("consolidated_sales_sso.customer_id IN (".$valueee.")");
        }
   if(count($data["distributor"])>0 && !empty($data["distributor"][0])&& !in_array("all",$data["distributor"])){
   
            foreach ($data["distributor"] as $cust) {
				if($cust){
                $distributors.="'".$cust."',";
				}
                     }
	$valueee=rtrim($distributors,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("consolidated_sales_sso.distributor_id IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
    
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("consolidated_sales_sso.brand_id IN (".$categoriess.")"); 

}if($data["gbu"] && $data["gbu"] !="all"){
   
$this->db->where('consolidated_sales_sso.bu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('consolidated_sales_sso.promotion', $data["promotion"]);	
}
            $q=$this->db->get('consolidated_sales_sso');
            $dataa=array();
            $colors=array("#ccc","#349ef3","#c98010");
            $i=0;
             if ($q->num_rows() > 0) {
             $budget= $q->row();
             return round($budget->value/1000,5);
             }
        else{
             return 0;
        }
          
        
    
	 }
         
         
    function getThisYearSalesTotal($data,$year){
             if($data["grossnet"]){
       $this->db->select('SUM(grand_total) as value,SUM(shipping) as resale,sales_type as sale')
             // ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               //->join("products", "sale_items.product_id=products.id", 'left')
                ->where("movement_code='VE'")
               ->where("DATE_FORMAT(date,'%Y')='".$year."'")
                    ->group_by('sales_type');
             }else{
              $this->db->select('SUM(grand_total) as resale,sales_type as sale')
             // ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               //->join("products", "sale_items.product_id=products.id", 'left')
                       ->where("movement_code='NT'") //net sale
                      ->where("DATE_FORMAT(date,'%Y')='".$year."'")
                    ->group_by('sales_type');    
             }
//     if($data["cluster"] && $data["cluster"]!="all"){
//            $clust="";
//            $this->db->where('sales_cluster like "%'.$data["cluster"][0].'%"');                                                                                                                                                                                                    
//    for($j=1;$j<count($data["cluster"]);$j++) {
//        $this->db->or_where('sales_cluster like "%'.$data["cluster"][$j].'%"');
//       
//    }
//         
//            }
          if(count($data["countrys"])>0 && !empty($data["countrys"][0]) && !in_array("all",$data["countrys"])){
       //die(print_r($data["countrys"]));
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
   
           
        
   
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
    
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("sales.brand_id IN (".$categoriess.")"); 

}if($data["gbu"] && $data["gbu"] !="all"){
    
$this->db->where('sales.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('sales.promotion', $data["promotion"]);	
}
            $q=$this->db->get('sales');
            $dataa=array();
            $colors=array("#ccc","#349ef3","#c98010");
            $i=0;
             if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->sale){
                $datax["sale"] = $row->sale;
                if(strtolower($row->sale)=="pso"|| strtolower($row->sale)=="sso"){
                    $datax["value"] =round($row->resale/1000,5);
                }
                else{
                $datax["value"] = round($row->value/1000,5);
                }
                $datax["color"]=$colors[$i];
                array_push($dataa, $datax);
                $i++;
            }
            }
        return json_encode($dataa);
             }
        else{
             return json_encode(array("sale"=>0,"value"=>0));
        }
          
        
    }
     function getGroupedSalesTotals($data,$salestype){
         $datefrom="01-".str_replace("/","-",$data["datefrom"]);
			$datefromm=date("Y-m-d",strtotime($datefrom));
            $dateto="31-".str_replace("/","-",$data["dateto"]);
			$datetoo=date("Y-m-d",strtotime($dateto));
      $months=array("01","02","03","04","05","06","07","08","09","10","11","12");
       $dataa=array();
        if($datefromm !="1970-01-01" ){
              $year=  date("Y",  strtotime($datefromm));
          }else{
              $year=date("Y");
          }
       
      foreach ($months as $month){
         
          $date=$year."-".$month;
         $gooddate="01-".$month."-".$year;
        // die($gooddate."sdd".$datefrom." ".$dateto);
          if((strtotime($gooddate) >= strtotime($datefromm)) && strtotime($gooddate) <= strtotime($dateto)){
           $sales=$this->monthlySales($date, $data,$salestype);
          if(is_array($sales)){
          array_push($dataa, $sales);
          }
          unset($sales);
          }
      }
      
      
     // die(print_r($dataa));
        return json_encode($dataa);
       
    }
    
    
    function getGroupedSalesTotalsQty($data){
      
     
      $datefrom="01-".str_replace("/","-",$data["datefrom"]);
			$datefromm=date("Y-m-d",strtotime($datefrom));
            $dateto="31-".str_replace("/","-",$data["dateto"]);
			
      $months=array("01","02","03","04","05","06","07","08","09","10","11","12");
       $dataa=array();
        if($datefromm !="1970-01-01" ){
              $year=  date("Y",  strtotime($datefromm));
          }else{
              $year=date("Y");
          }
       
      foreach ($months as $month){
         
          $date=$year."-".$month;
         $gooddate="01-".$month."-".$year;
        // die($gooddate."sdd".$datefrom." ".$dateto);
          if((strtotime($gooddate) >= strtotime($datefromm)) && strtotime($gooddate) <= strtotime($dateto)){
          //die($date);
          $sales=$this->monthlySalesQty($date,$data);
          if(is_array($sales)){
          array_push($dataa, $sales);
          }
          unset($sales);
          }
      }
      
      
     // die(print_r($dataa));
        return json_encode($dataa);
       
        
        
    }
    
   
function bestsellingproducts($data,$scenario)
    {
    $datas=$this->getSortedProductSales($data,$scenario);
    array_multisort(array_column($datas, 'SoldQty'), SORT_DESC, $datas);
    
    $newArray = array_slice($datas, 0, 10, true);
return (json_encode($newArray));
//  if($data["grossnet"]){
//      
//      $sp = "(SELECT si.product_id,s.sales_type,s.country_id,s.date,s.sales_cluster, ROUND(SUM(s.shipping)/1000,5) as SoldQty, s.date as sdate,s.staff_note from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id group by si.product_id ) PSales";
//
//		$this->db
//            ->select("" . $this->db->dbprefix('products') . ".name as product,COALESCE( PSales.SoldQty, 0 ) as SoldQty , " . $this->db->dbprefix('products') . ".price as unified_value,PSales.sales_type,PSales.date  as sales_date,CONCAT(PSales.staff_note,'#EE82EE') as color ", FALSE)
//            ->from('products', FALSE)
//            ->join($sp, 'products.id = PSales.product_id', 'left')
//			->where('sales_type',strtoupper($scenario))
//            ->order_by('SoldQty desc')
//            ->limit(10);
//     
//  }
//  else{
//      $sp = "(SELECT si.product_id,s.sales_type,s.country_id,s.date,s.sales_cluster, ROUND(SUM(s.shipping)/1000,5)+ROUND(SUM(s.total_discount)/1000,5) as SoldQty, s.date as sdate,s.staff_note from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id group by si.product_id ) PSales";
//
//		$this->db
//            ->select("" . $this->db->dbprefix('products') . ".name as product, COALESCE( PSales.SoldQty, 0 ) as SoldQty , " . $this->db->dbprefix('products') . ".price as unified_value,PSales.sales_type,PSales.date  as sales_date,CONCAT(PSales.staff_note,'#EE82EE') as color ", FALSE)
//            ->from('products', FALSE)
//            ->join($sp, 'products.id = PSales.product_id', 'left')
//			->where('sales_type',strtoupper($scenario))
//            ->order_by('SoldQty desc')
//            ->limit(10);
//      
//  
//  }
//    
//         if($data["datefrom"] && $data["dateto"]){
//        $datefrom="01-".str_replace("/","-",$data["datefrom"]);
//			$datefromm=date("Ymd",strtotime($datefrom));
//            $dateto="31-".str_replace("/","-",$data["dateto"]);
//			$datetoo=date("Ymd",strtotime($dateto));
//		 $this->db->where('DATE_FORMAT(date,"%Y%m%d") BETWEEN "' . $datefromm . '" and "' . $datetoo . '"');	
//        }
//        if(!empty($data["countrys"]) && !empty($data["countrys"][0]) && !in_array("all",$data["countrys"])){
//       
//            foreach ($data["countrys"] as $value) {
//				if($value){
//                $clusters.="'".$value."',";
//				}
//                     }
//					 $valuee=rtrim($clusters,",");
//					// $valuee=rtrim($valuee.',');
//             $this->db->where("country_id IN (".$valuee.")");
//        }
//      
//       
//  if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
//    foreach ($data["productcategoryfamily"] as $cat) {
//				if($cat){
//                $categories.="'".$cat."',";
//				}
//                     }
//		 $categoriess=rtrim($categories,",");
//         $this->db->where("products.category_id IN (".$categoriess.")"); 
//}
////die(print_r($data));
//if($data["gbu"] && $data["gbu"] !="all"){
// $gbu=$data["gbu"];
//$this->db->where('sma_products.business_unit like "%'.$gbu.'%"');	
//}
//        $q = $this->db->get();
//
//        if ($q->num_rows() > 0) {
//            foreach (($q->result()) as $row) {
//                if($row->product && $row->SoldQty){
//                $datas[] = $row;
//                
//            }
//            }
//           // return $data;
//return json_encode($datas);
//
//        }
//
//        return FALSE;
    }
    
    
    function getPeriodicSales($data,$salestype){
    
    }
    
    
    function getSortedProductSales($data,$scenario){
        $allproductsales=array();
    
          $this->db->select('DISTINCT(product_id) as product,product_name');
                 $q=$this->db->get('sma_sale_items');
                 if ($q->num_rows() > 0) {
               foreach (($q->result()) as $row) {
               $sale= $this->getProductSales($row->product,$row->product_name,$data,$scenario);
                if($sale["SoldQty"]>0){
                array_push($allproductsales,$sale);
                }
    }
                 }
                 
                 return $allproductsales;
    }
    
    function getProductSales($id,$name,$data,$scenario){
         $datefrom="01-".str_replace("/","-",$data["datefrom"]);
			$datefromm=date("Ymd",strtotime($datefrom));
            $dateto="31-".str_replace("/","-",$data["dateto"]);
            $datetoo=date("Ymd",strtotime($dateto));
         if($data["grossnet"]){
              $this->db->select('SUM(shipping) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
                       ->join("products", "sale_items.product_id=products.id", 'left')
              ->where("sale_items.product_id=$id")           
                       ->where("movement_code='VE'")
                       ->where("sales_type='".strtoupper($scenario)."'")      
                        ->where("DATE_FORMAT(date,'%Y%m%d')>=$datefromm  AND DATE_FORMAT(date,'%Y%m%d')<=$datetoo");
          }
          else{
                $this->db->select('SUM(shipping) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
                         ->join("products", "sale_items.product_id=products.id", 'left')
                         ->where("movement_code='NT'")
              ->where("sale_items.product_id=$id")     
                        ->where("sales_type='".strtoupper($scenario)."'")
                        ->where("DATE_FORMAT(date,'%Y%m%d')>=$datefromm  AND DATE_FORMAT(date,'%Y%m%d')<=$datetoo"); 
          }
           if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
      
   
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")");

}if($data["gbu"] && $data["gbu"] !="all"){
    $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
}
        
        
            $q=$this->db->get('sales');
            $total=0;
            if ($q->num_rows() > 0) {
                 
            foreach (($q->result()) as $row) {
          $total+=$row->resale;
          
        
    }
    
    
            }
            return array("product"=>$name,"SoldQty"=>round($total/1000,5));
    }
    
    function monthlySales($date,$data,$salestype){
        //echo $date;
        $this->load->model('purchases_model');
          if($data["grossnet"]){
              $this->db->select('SUM(grand_total) as value,SUM(shipping)as resale,sales_type as sale,date')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                       ->where("movement_code='VE'")
                        ->where("DATE_FORMAT(date,'%Y-%m')",$date)
              ->where("sales_type='".$salestype."'");
          }
          else{
                $this->db->select('SUM(grand_total) as resale,sales_type as sale,date')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                         ->where("movement_code='NT'")
                        ->where("DATE_FORMAT(date,'%Y-%m')",$date)
                        ->where("sales_type='".$salestype."'");
          }
              
              
                    
//             if($data["cluster"] && $data["cluster"]!="all"){
//            $clust="";
//            $this->db->where('sales_cluster like "%'.$data["cluster"][0].'%"');                                                                                                                                                                                                    
//    for($j=1;$j<count($data["cluster"]);$j++) {
//        $this->db->or_where('sales_cluster like "%'.$data["cluster"][$j].'%"');
//    
//    }
//         
//        }
    
         
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
   
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")");

}if($data["gbu"] && $data["gbu"] !="all"){
    $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('products.promoted', $data["promotion"]);	
}
             $this->db->group_by('sales_type');
             $this->db->group_by('date');
            $q=$this->db->get('sales');
           
            
            $i=0;
             if ($q->num_rows() > 0) {
                 
            foreach (($q->result()) as $row) {
                
                if($row->sale){
                    //filter out dates
                   // echo $date."sdsd".date('Y-m',strtotime($row->date));
                    if($date == date('Y-m',strtotime($row->date))){
                    $resultdata["period"]=date('F',strtotime($row->date));
                    $resultdata['valuestockcoverage']=1;
                    
                  
                    //if pso or sso use resale
                    if(strtolower($row->sale)=="pso" || strtolower($row->sale)=="sso"){   
                    $resultdata["value".strtolower($row->sale)]=round($row->resale/1000,5);
               } else{
                   $resultdata["value".strtolower($row->sale)]=round($row->value/1000,5);
               }
                if(strtolower($row->sale)=="pso"){
                $resultdata["color".strtolower($row->sale)]="#349ef3";
                $stock=$this->purchases_model->getClosingStock($data,date('m-Y',strtotime($row->date)),"PSO");
                    $resultdata['valuestock']=  round($stock/1000,5);
                    $resultdata['colorstock']="#800080";
                }
                
                 if(strtolower($row->sale)=="sso"){
                $resultdata["color".strtolower($row->sale)]="#c98012"; 
                $stock=$this->purchases_model->getClosingStock($data,date('m-Y',strtotime($row->date)),"SSO");
                    $resultdata['valuestock']=  round($stock/1000,5);
                    $resultdata['colorstock']="#800080";
                }
                
                else if (strtolower($row->sale)=="si"){
                     $resultdata["color".strtolower($row->sale)]="#ccc";
                         $stock=$this->purchases_model->getClosingStock($data,date('m-Y',strtotime($row->date)),"SI");
                       $resultdata['valuestock']=  round($stock/1000,5);
                    $resultdata['colorstock']="#800080";
                }
                else{
                 $resultdata["color".strtolower($row->sale)]="#c98012";    
                }
               
                    }       
                }
            }
            
             }
           return $resultdata;
         }
         
         
         
    function monthlySalesQty($date,$data){
        //echo $date;
          
              $this->db->select('SUM(si.quantity) as value,sales_type as sale,date')
                      ->join("sale_items si", "si.sale_id=sales.id", 'left')
               ->join("products", "si.product_id=products.id", 'left')
                        ->where("DATE_FORMAT(date,'%Y-%m')",$date);
                    
//             if($data["cluster"] && $data["cluster"]!="all"){
//            $clust="";
//            $this->db->where('sales_cluster like "%'.$data["cluster"][0].'%"');                                                                                                                                                                                                    
//    for($j=1;$j<count($data["cluster"]);$j++) {
//        $this->db->or_where('sales_cluster like "%'.$data["cluster"][$j].'%"');
//    
//    }
//         
//        }
    
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")");
}
if($data["gbu"] && $data["gbu"] !="all"){
$this->db->where('products.business_unit',$data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('products.promoted', $data["promotion"]);	
}
             $this->db->group_by('sales_type');
             $this->db->group_by('date');
            $q=$this->db->get('sales');
           
            
            $i=0;
             if ($q->num_rows() > 0) {
                 
            foreach (($q->result()) as $row) {
                
                if($row->sale){
                    //filter out dates
                   // echo $date."sdsd".date('Y-m',strtotime($row->date));
                    if($date == date('Y-m',strtotime($row->date))){
                    $resultdata["period"]=date('F',strtotime($row->date));
                    $resultdata['valuestockcoverage']=1;
                    $resultdata['valuestock']=1;
                    $resultdata['colorstock']="#ccc";
                $resultdata["value".strtolower($row->sale)]=round($row->value/1000,5);
                if(strtolower($row->sale)=="pso"){
                $resultdata["color".strtolower($row->sale)]="#349ef3";
                }
                else if (strtolower($row->sale)=="si"){
                     $resultdata["color".strtolower($row->sale)]="#ccc";
                }
                else{
                 $resultdata["color".strtolower($row->sale)]="#c98012";    
                }
               
                    }       
                }
            }
            
             }
           return $resultdata;
         }
		 
         //per country sales
         function  getSalesSSOSalesPerCountry($country,$data){
             $data["countrys"]=array($country);
             
            return  $this->consolidatedSalesSSO($data);
         }
                 
         
         
         
         
         
		  function consolidatedSalesPso($data){
        //echo $date;
     //die($data["dateto"]);
        $last12months=$this->getLast12Months($data["dateto"]);

        $last12months=$this->getLast12MonthsYear($data["dateto"]);

       $alldata=array();
        
  
 
       foreach ($last12months as $month) {
                
           $row=$this->getPSOsalesByCountry($data,"'".$month."'");
           $thisyear=substr($month,-4);
           $lastyear=$thisyear-1;
          $newmonth=substr($month,0,2);
          $dt = DateTime::createFromFormat('!m',$newmonth);
$actualdate=$dt->format('M')."-".$thisyear;
          $last=$newmonth."-".$lastyear;
          // die($lastyear."-".$newmonth);
           $rowlastyear=$this->getPSOsalesByCountry($data,"'".$last."'");
         $resale=0;
         $lastresale=0;
         
                     $resale=round($row->resale/1000,5);
                     $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}
                     //chnage date to budget format
                     $datemonth="01-".$month;
                     
                     
                     $resultdata["period"]=$actualdate;
                     $resultdata['Actual']=$resale;
                   $resultdata['Budget']=$this->getBudgetForecastForMonth($data,"'".$datemonth."'","budget","PSO");
                     $resultdata['Forecast']=$this->getBudgetForecastForMonth($data,"'".$datemonth."'","forecast","PSO");
                     $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data,"'".$datemonth."'","forecast2","PSO");             
                     $resultdata["ActualLast"]=$lastresale;
                                  
                    
       
                       
                    
           array_push($alldata,$resultdata);
                             
                     
                     
                }
            
               
              //die(print_r($alldata));
      
      
              
              return (json_encode($alldata));
         }
         
         
     function consolidatedSalesSSO($data){
       
        $last12months=$this->getLast12MonthsYear($data["dateto"]);

       $alldata=array();
        
  
 $lastKey = end($last12months);
 $firstkey=reset($last12months);
 $i=0;
       foreach ($last12months as $k =>$month) {
           
           $row=$this->getSSOsalesByCountryConsolidated($data,"'".$month."'");
           $thisyear=substr($month,-4);
           $lastyear=$thisyear-1;
          $newmonth=substr($month,0,2);
          $dt = DateTime::createFromFormat('!m',$newmonth);
$actualdate=$dt->format('M')."-".$thisyear;
          $last=$newmonth."-".$lastyear;
          // die($lastyear."-".$newmonth);
           $rowlastyear=$this->getSSOsalesByCountryConsolidated($data,"'".$last."'");
         $resale=0;
         $lastresale=0;
         $datemonth="01-".$month;
         $datemonth2=$thisyear."-".$newmonth;
       //  echo($datemonth2)."<br>";
                     $resale=round($row->resale/1000,5);
                     $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}
                     if ( $lastKey== $last12months[$i] || $firstkey == $last12months[$i]) {
    // We are at the first element
                         $resultdata['ActualTrend']=$resale;
                          $resultdata['BudgetTrend']=$this->getBudgetForecastForMonth($data,"'".$datemonth2."'","budget","SSO");
                         
}
                     $resultdata["period"]=$actualdate;
                     $resultdata['Actual']=round($resale,2);
                     $resultdata['Budget']=round($this->getBudgetForecastForMonth($data,"'".$datemonth2."'","budget","SSO"),2);
                     $resultdata['Forecast']=round($this->getBudgetForecastForMonth($data,"'".$datemonth2."'","forecast","SSO"),2);
                     $resultdata['Forecast2']=round($this->getBudgetForecastForMonth($data,"'".$datemonth2."'","forecast2","SSO"),2);              
                     $resultdata["ActualLast"]=round($lastresale,2);
                   
                 
           array_push($alldata,$resultdata);
                             
               $i++; 
                }
          //  die(print_r($alldata));
               
              return (json_encode($alldata));
         }
            
      
function consolidatedSalesSI($data){
      
        $last12months=$this->getLast12MonthsYear($data["dateto"]);
              
       $alldata=array();
        
  
 
       foreach ($last12months as $month) {
           
           $row=$this->getSIsalesByCountry($data,"'".$month."'");
           $thisyear=substr($month,-4);
           $lastyear=$thisyear-1;
          $newmonth=substr($month,0,2);
          $dt = DateTime::createFromFormat('!m',$newmonth);
$actualdate=$dt->format('M')."-".$thisyear;
          $last=$newmonth."-".$lastyear;
          // die($lastyear."-".$newmonth);
           $rowlastyear=$this->getSIsalesByCountry($data,"'".$last."'");
         $resale=0;
         $lastresale=0;
         $datemonth="01-".$month;
                     $resale=round($row->resale/1000,5);
                     $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}
                     $resultdata["period"]=$actualdate;
                     $resultdata['Actual']=round($resale,2);
                   $resultdata['Budget']=$this->getBudgetForecastForMonth($data,"'".$datemonth."'","budget","SI");
                     $resultdata['Forecast']=$this->getBudgetForecastForMonth($data,"'".$datemonth."'","forecast","SI");
                     $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data,"'".$datemonth."'","forecast2","SI");
                                  
                     $resultdata["ActualLast"]=$lastresale;
                   
                    
       
                       
                    
           array_push($alldata,$resultdata);
                             
                     
                     
                }
            
               
              //die(print_r($alldata));
      
      
              
              return (json_encode($alldata));
         }
         
           function consolidatedSalesSumSI($data){
      
               $months="";
               $monthslastyear="";
     $array=$data["period"];
     
     foreach ($array as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
     }
       $months=  rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
       $monthslastyear=  rtrim($monthslastyear,",");
    $monthscurrentyearmonth=  rtrim($monthscurrentyearmonth,",");
       
        

 $row=$this->getSIsalesByCountry($data,$months);
 $rowlastyear=$this->getSIsalesByCountry($data,$monthslastyear);
 $resale=round($row->resale/1000,5);
  $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}

                   
               $budget=$this->getBudgetForecastForMonth($data,$datemonths,"budget","SI");    
                    
       $forecast=$this->getBudgetForecastForMonth($data,$datemonths,"forecast","SI"); 

              $resultdata["period"]=$array[0]." to ".  end($array);
                     $resultdata['Actual']=$resale;
                    $resultdata['Budget']=$budget;
                     $resultdata['Forecast']=$forecast;
                     $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data,$datemonths,"forecast2","SI"); 
                                  
                     $resultdata["ActualLast"]=$lastresale;
               
           
  
      
              
              return (json_encode($resultdata));
         }
         
         function ytdSalesSalesType($data,$salestype){
             $salestypes=strtolower($salestype);
             $endmonth=end($data["period"]);
             $dates=$this->getMonthsFromBeginingOfYear($endmonth);
             $alldates=explode(",",$dates["thisyear"]);
              $months="";
           
               
           foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  \substr($value,-4);
         
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
       
     }
          $months= \rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
       
    $monthscurrentyearmonth=rtrim($monthscurrentyearmonth,",");
          $psoobject=$this->getSSOsalesByCountryConsolidated($data,$months);
          return round($psoobject->resale/1000,5);
                       }
         
                       
                       //ytg
                       function ytgSalesSalesType($data,$salestype){
             $salestypes=strtolower($salestype);
             $endmonth=end($data["period"]);
             $dates=$this->getMonthsToEndOfYear($endmonth);
             $alldates=explode(",",$dates["thisyear"]);
              $months="";
           
               
           foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  \substr($value,-4);
         
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
       
     }
          $months= \rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
       
    $monthscurrentyearmonth=rtrim($monthscurrentyearmonth,",");
          $psoobject=$this->getSSOsalesByCountryConsolidated($data,$months);
          return round($psoobject->resale/1000,5);
                       }
                  
                       function ytgBudget($data,$datemonths,$budgetorforecast, $salestype){
                   $endmonth=end($data["period"]);
             $dates=$this->getMonthsToEndOfYear($endmonth);
             $alldates=explode(",",$dates["thisyear"]);
              $months="";
           
               
           foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         
         
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
       
     }
          $months= \rtrim($months,",");        
                           
          return $this->getBudgetForecastForMonth($data, $months, $budgetorforecast, $salestype);             
                       }
                               
                               
                              function ytdBudget($data,$datemonths,$budgetorforecast, $salestype){
                   $endmonth=end($data["period"]);
             $dates=$this->getMonthsFromBeginingOfYear($endmonth);
             $alldates=explode(",",$dates["thisyear"]);
              $months="";
           
               
           foreach ($alldates as $value) {
         $currentyear=substr($value,-4);
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         
         
         $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
       
     }
          $months= \rtrim($monthscurrentyearmonth,",");        
           //die(print_r($months));               
          return $this->getBudgetForecastForMonth($data, $months, $budgetorforecast, $salestype);             
                       } 
                       
         function ytdSales($data,$salestype,$year){
             $salestypes=strtolower($salestype);
             $endmonth=end($data["period"]);
             $dates=$this->getMonthsFromBeginingOfYear($endmonth);
             $alldates=explode(",",$dates["thisyear"]);
              $months="";
               $monthslastyear="";
               
           foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
     }
              $months=  rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
       $monthslastyear=  rtrim($monthslastyear,",");
    $monthscurrentyearmonth=rtrim($monthscurrentyearmonth,",");
    
    
             
             if($salestypes=="pso"){
                 $psoobject=$this->getPSOsalesByCountry($data,$months);
                 $psoobjectlast=$this->getPSOsalesByCountry($data,$monthslastyear);
                 $budget=  $this->getBudgetForecastForMonth($data,$datemonths,"budget", $salestypes);
                 $forecast=  $this->getBudgetForecastForMonth($data,$datemonths,"forecast", $salestypes);
                   $resultdata['period']=$currentyear;
                $resultdata['Actual']= round($psoobject->resale/1000,5);
                $resultdata['Budget']=$budget;
               $resultdata['Forecast']=$forecast;
               $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data,$datemonths,"forecast2", $salestypes);
               $resultdata["ActualLast"]=round($psoobjectlast->resale/1000,5);
               
              
             }
             else if($salestypes=="sso"){
                
                 $psoobject=$this->getSSOsalesByCountry($data, $months);
                 $psoobjectlast=$this->getSSOsalesByCountry($data, $monthslastyear);
                 $budget=  $this->getBudgetForecastForMonth($data,$datemonths,"budget", $salestypes);
                 $forecast=  $this->getBudgetForecastForMonth($data, $datemonths,"forecast", $salestypes);
                 $resultdata['period']=$currentyear;
                $resultdata['Actual']= round($psoobject->resale/1000,2);
                $resultdata['Budget']=round($budget,2);
               $resultdata['Forecast']=round($forecast,2);
                $resultdata['Forecast2']=round($this->getBudgetForecastForMonth($data, $datemonths,"forecast2", $salestypes),2);
               $resultdata["ActualLast"]=round($psoobjectlast->resale/1000,2);
              
             }
             //si
             else{
                $psoobject=$this->getSIsalesByCountry($data,$months);
                 $psoobjectlast=$this->getSIsalesByCountry($data, $monthslastyear);
                 $budget=  $this->getBudgetForecastForMonth($data, $datemonths,"budget", $salestypes);
                 $forecast=  $this->getBudgetForecastForMonth($data, $datemonths,"forecast", $salestypes);
                 $resultdata['period']=$currentyear;
                $resultdata['Actual']= round($psoobject->resale/1000,5);
                $resultdata['Budget']=$budget;
               $resultdata['Forecast']=$forecast;
               $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data, $datemonths,"forecast2", $salestypes);
               $resultdata["ActualLast"]=round($psoobjectlast->resale/1000,5); 
             
             }
             
             return json_encode($resultdata);
             
         }
         
         
         function consolidatedSalesSumPSO($data){
          $months="";
               $monthslastyear="";
     $array=$data["period"];
     
     foreach ($array as $value) {
         
         $months.="'".$value."',";
         $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
     }
       $months=  rtrim($months,",");
       $datemonths=  rtrim($datemonths,",");
       $monthslastyear=  rtrim($monthslastyear,",");
    $monthscurrentyearmonth=  rtrim($monthscurrentyearmonth,",");
       
        

 $row=$this->getPSOsalesByCountry($data,$months);
 $rowlastyear=$this->getPSOsalesByCountry($data,$monthslastyear);
 $resale=round($row->resale/1000,5);
  $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}

                   
               $budget=$this->getBudgetForecastForMonth($data,$datemonths,"budget","PSO");    
                    
       $forecast=$this->getBudgetForecastForMonth($data,$datemonths,"forecast","PSO"); 

              $resultdata["period"]=$array[0]." to ".  end($array);
                     $resultdata['Actual']=$resale;
                    $resultdata['Budget']=$budget;
                     $resultdata['Forecast']=$forecast;
                         $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data,$datemonths,"forecast2","SSO");  
                     $resultdata["ActualLast"]=$lastresale;
               
           
  
      
              
              return (json_encode($resultdata));
         }
         
         function consolidatedSalesSumSSO($data){
         $months="";
               $monthslastyear="";
     $array=$data["period"];
     
     foreach ($array as $value) {
         
         $months.="'".$value."',";
         $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $datemonths2="'".$currentyear."-".substr($value,0,2)."-01',";
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
     }
       $months=  rtrim($months,",");
       $datemonths=  rtrim($datemonths,",");
       $datemonths2=  rtrim($datemonths2,",");
       $monthslastyear=  rtrim($monthslastyear,",");
    $monthscurrentyearmonth=  rtrim($monthscurrentyearmonth,",");
       
        

 $row=$this->getSSOsalesByCountryConsolidated($data,$months);
 $rowlastyear=$this->getSSOsalesByCountryConsolidated($data,$monthslastyear);
 $resale=round($row->resale/1000,5);
  $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}

                   
               $budget=$this->getBudgetForecastForMonth($data,$datemonths2,"budget","SSO");    
                    
       $forecast=$this->getBudgetForecastForMonth($data,$datemonths2,"forecast","SSO"); 
       

              $resultdata["period"]=$array[0]." to ".  end($array);
                     $resultdata['Actual']=round($resale,2);
                    $resultdata['Budget']=round($budget,2);
                     $resultdata['Forecast']=round($forecast,2);
                      $resultdata['Forecast2']=round($this->getBudgetForecastForMonth($data,$datemonths2,"forecast2","SSO"),2);;            
                     $resultdata["ActualLast"]=round($lastresale,2);
               
           
  
      //die(print_r($resultdata));
              
              return (json_encode($resultdata));
         }

         
         
         
         function  getSSOsalesByCountry($data,$month){
                if(strlen($month)==4){
                    
                    if($data["grossnet"]){
            $this->db->select('SUM(grand_total) as resale')
                                     ->where("movement_code","VE")
->where("sales_type","SSO")
                        ->where("DATE_FORMAT(date,'%Y')",$month)    ;
        }else{
                $this->db->select('SUM(grand_total) as resale')
                                     ->where("movement_code","NT")
->where("sales_type","SSO")
                        
                        ->where("DATE_FORMAT(date,'%Y')",$month); 
                             }
                    
                    
                }
                else{
                   
        if($data["grossnet"]){
                $this->db->select('SUM(grand_total) as resale')
                                     ->where("movement_code","VE")
->where("sales_type","SSO")
                      
                      ->where("DATE_FORMAT(date,'%m-%Y') IN ($month)");
        }else{
                $this->db->select('SUM(grand_total) as resale')
                                     ->where("movement_code","NT")
->where("sales_type","SSO")
                      
                      ->where("DATE_FORMAT(date,'%m-%Y')IN ($month)");
                             }
                }
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
            $q=$this->db->get('sales');
             
                            
          return $q->row();
         }
        
function  getSSOsalesByCountryConsolidated($data,$month){
             // die(print_r($data));
                if(strlen($month)==4){
                    
                    if($data["grossnet"]){
              $this->db->select('SUM(grand_total) as resale')
                      ->where("movement_code","VE")
                     ->where("sales_type","SSO")
                        ->where("DATE_FORMAT(date,'%Y')",$month)    ;
        }else{
               $this->db->select('SUM(grand_total) as resale')
                         ->where("movement_code","NT")
                       ->where("sales_type","SSO")
                        ->where("DATE_FORMAT(date,'%Y')",$month); 
                             }
                    
                    
                }
                else{
                   
        if($data["grossnet"]){
             $this->db->select('SUM(grand_total) as resale')
                      ->where("movement_code","VE")
                      ->where("sales_type","SSO")
                      ->where("DATE_FORMAT(date,'%m-%Y') IN ($month)");
        }else{
              $this->db->select('SUM(grand_total) as resale')
                      ->where("movement_code","NT")
                      ->where("sales_type","SSO")
                      ->where("DATE_FORMAT(date,'%m-%Y')IN ($month)");
                             }
                }
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
            $q=$this->db->get('sales');
             
                            
          return $q->row();
         }
         
         
           function  getSSOsalesByCountryConsolidatedOnly($data,$fromyear,$toyear){
              //die(print_r($data));
              
                    
                    if($data["grossnet"]){
              $this->db->select("SUM(grand_total) as resale,DATE_FORMAT(date,'%m-%Y') as date" )
                      ->where("movement_code","VE")
                        ->where("sales_type","SSO")
                     ->where("DATE_FORMAT(date,'%Y') BETWEEN '$fromyear' AND '$toyear'"); 
        }else{
                $this->db->select("SUM(grand_total) as resale,DATE_FORMAT(date,'%m-%Y') as date" )
                        ->where("movement_code","NT")
                        ->where("sales_type","SSO")
                        ->where("DATE_FORMAT(date,'%Y') BETWEEN '$fromyear' AND '$toyear'"); 
                             }
                    
                    
                
               
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
  $this->db->group_by('date');
            $q=$this->db->get('sales');
             
          foreach (($q->result()) as $row) {
                $dataa[$row->date] =round($row->resale/1000,5);
               
            }
            return $dataa;
         }
         function  getBrandSales($brand,$data,$month){
              //die(print_r($data));
            
              $this->db->where('brand_id',$brand);        
             
                
                   
        if($data["grossnet"]){
              $this->db->select('SUM(gross_sale) as resale')
                      
                      ->where("DATE_FORMAT(monthyear,'%m-%Y') IN ($month)");
        }else{
               $this->db->select('SUM(net_sale) as resale')
                      
                      ->where("DATE_FORMAT(monthyear,'%m-%Y')IN ($month)");
                             }
                
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
$this->db->where('sales.bu', $data["gbu"]);	
}

if(($data["promotion"]=="1" || $data["promotion"]=="0")  && $data["gbu"] !="promotion"){
$this->db->where('sales.promotion', $data["promotion"]);	
}
            $q=$this->db->get('sales');
             $sale=$q->row();
            // print_r($this->db->_compile_select());  
             echo $this->db->queries[0];
                return round($sale->resale/1000,5);           
          
         }
         
         
          function  getPSOsalesByCountry($data,$month){
                if(strlen($month)==4){
                    
                    if($data["grossnet"]){
              $this->db->select('SUM(grand_total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
                ->where("movement_code='VE'")
                      ->where("sales_type='PSO'")
                        ->where("DATE_FORMAT(date,'%Y')",$month)    ;
        }else{
               $this->db->select('SUM(grand_total)  as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->where("movement_code='NT'")
                        ->where("sales_type='PSO'")
                        ->where("DATE_FORMAT(date,'%Y')",$month); 
                             }
                    
                    
                }
                else{
                   
        if($data["grossnet"]){
              $this->db->select('SUM(grand_total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
                ->where("movement_code='VE'")
                      ->where("sales_type='PSO'")
                      ->where("DATE_FORMAT(date,'%m-%Y')IN ($month)");
        }else{
               $this->db->select('SUM(grand_total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
               ->where("movement_code='NT'")
                        ->where("sales_type='PSO'")
                       ->where("DATE_FORMAT(date,'%m-%Y')IN ($month)");
                             }
                }
                 if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
	$valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("sales.country_id IN (".$valuee.")");
        }
        
          
                    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("sma_sales.brand_id IN (".$categoriess.")");
}   
if($data["gbu"] && $data["gbu"] !="all"){
$this->db->where('sma_sales.gbu',$data["gbu"]);	
}

     if($data["product"] && !in_array("all",$data["product"])){
         //die(print_r($data['products']));
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("sma_sales.gmid IN (".$prods.")");
} 
    
if($data["promotion"]=="1" || $data["promotion"]=="0"){
       //$this->db->join("products", "sale_items.product_id=products.id", 'left');
$this->db->where('sma_sales.promotion', $data["promotion"]);	
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
             $this->db->where("sma_sales.distributor_id IN (".$valueee.")");
        }
        
        
            $q=$this->db->get('sales');
             
                            
         $amount= $q->row();
        return round($amount->resale/1000);
         }
         
         
          function  getPSOsalesByCountryYearly($data,$fromyear,$toyear){
                if($data["grossnet"]){
                     $this->db->where("sales.sales_type ='PSO' ");
              $this->db->select("SUM(grand_total) as resale,DATE_FORMAT(date,'%m-%Y') as date" )
                      ->where("movement_code='VE'")
                     ->where("DATE_FORMAT(date,'%Y') BETWEEN '$fromyear' AND '$toyear'"); 
        }else{
            $this->db->where("sales.sales_type ='PSO' ");
                $this->db->select("SUM(grand_total) as resale,DATE_FORMAT(date,'%m-%Y') as date" )
                         ->where("movement_code='NT'")
                        ->where("DATE_FORMAT(date,'%Y') BETWEEN '$fromyear' AND '$toyear'"); 
                             }
                 if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
	$valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("sales.country_id IN (".$valuee.")");
        }
        
          
                    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("sma_sales.brand_id IN (".$categoriess.")");
}   
if($data["gbu"] && $data["gbu"] !="all"){
$this->db->where('sma_sales.gbu',$data["gbu"]);	
}

     if($data["product"] && !in_array("all",$data["product"])){
         //die(print_r($data['products']));
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
                // die(print_r($prods));
         $this->db->where("sma_sales.gmid IN (".$prods.")");
} 
    
if($data["promotion"]=="1" || $data["promotion"]=="0"){
       //$this->db->join("products", "sale_items.product_id=products.id", 'left');
$this->db->where('sma_sales.promotion', $data["promotion"]);	
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
             $this->db->where("sma_sales.distributor_id IN (".$valueee.")");
        }
        
        
            $this->db->group_by('date');
            $q=$this->db->get('sales');
             
          foreach (($q->result()) as $row) {
                $dataa[$row->date] =round($row->resale/1000,5);
               
            }
            return $dataa;
         }
          function  getSIsalesByCountry($data,$month){
              if(strlen($month)==4){
                 if($data["grossnet"]){
              $this->db->select('SUM(total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left');
                     if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"]) || ($data["gbu"] && $data["gbu"] !="all")){
                     $this->db->join("products", "sale_items.product_id=products.id", 'left');
                     }
                $this->db->where("movement_code='VE'");
                     $this->db->where("sales_type='SI'")
                        ->where("DATE_FORMAT(date,'%Y')",$month);
        }
        //net sales
        else{
               $this->db->select('SUM(total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left');
                         if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])|| ($data["gbu"] && $data["gbu"] !="all")){
                     $this->db->join("products", "sale_items.product_id=products.id", 'left');
                     }
                     $this->db->where("movement_code='NT'");
                        $this->db->where("sales_type='SI'")
                        ->where("DATE_FORMAT(date,'%Y')",$month); 
                             }     
                  
              }
              
              /*****monthly sales***********/
              else{
        if($data["grossnet"]){
              $this->db->select('SUM(total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left');
                     if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"]) || ($data["gbu"] && $data["gbu"] !="all")){
                     $this->db->join("products", "sale_items.product_id=products.id", 'left');
                     }
                $this->db->where("movement_code='VE'");
                     $this->db->where("sales_type='SI'")
                        ->where("DATE_FORMAT(date,'%m-%Y')IN ($month)")    ;
        }
        //net sales
        else{
               $this->db->select('SUM(total) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left');
                         if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])|| ($data["gbu"] && $data["gbu"] !="all")){
                     $this->db->join("products", "sale_items.product_id=products.id", 'left');
                     }
               $this->db->where("movement_code='NT'");
                        $this->db->where("sales_type='SI'")
                               
                       ->where("DATE_FORMAT(date,'%m-%Y')IN ($month)");
                             }
              }
                 if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
    if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")");
}

     if($data["product"] && !in_array("all",$data["product"])){
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("products.id IN (".$prods.")");
} 
if($data["gbu"] && $data["gbu"] !="all"){
$this->db->where('products.business_unit',$data["gbu"]);	
}

if($data["gbu"] && $data["gbu"] !="all"){
$this->db->where('products.business_unit',$data["gbu"]);	
}
            $q=$this->db->get('sales');
             
                            
          return $q->row();
         }
         
         
         
         
         
         
         function consolidatedSalesFamily($data){
    
        $last12months=$this->getLast12Months($data["dateto"]);

       $alldata=array();
        
  
 
       foreach ($last12months as $month) {
                
           $row=$this->getPSoSalesByProductFamily($data,$month);
           $thisyear=substr($month,0,4);
           $lastyear=$thisyear-1;
          $newmonth=substr($month, -2); //get month from year in YYYY-mm
           $dt = DateTime::createFromFormat('!m',$newmonth);
$actualdate=$dt->format('M')."-".$thisyear;
          $last=$lastyear."-".$newmonth;
          // die($lastyear."-".$newmonth);
           $rowlastyear=$this->getPSoSalesByProductFamily($data,$last);
         $resale=0;
         $lastresale=0;
         
                     $resale=round($row->resale/1000,5);
                     $lastresale=round($rowlastyear->resale/1000,5);
                     if($resale==0){
                         $resale=0;
                     }
                     if($lastresale==null){$lastresale=0;}
                      $datemonth="01-".$month;
                     $resultdata["period"]=$actualdate;
                     $resultdata['Actual']=$resale;
                     $resultdata['Budget']=$this->getBudgetForecastForMonth($data,$datemonth,"budget","PSO");
                     $resultdata['Forecast']=$this->getBudgetForecastForMonth($data,$datemonth,"forecast","PSO");
                      $resultdata['Forecast2']=$this->getBudgetForecastForMonth($data,$datemonth,"forecast2","PSO");
                                  
                     $resultdata["ActualLast"]=$lastresale;
                   
                    
       
                       
                    
           array_push($alldata,$resultdata);
                             
                     
                     
                }
            
               
            
      
      
              
              return (json_encode($alldata));
         }
         

         
          function getBudgetForecastForYear($data,$year,$budgetorforecast,$salestype){
             $thisyear=substr($month,0,4);
                    $newmonth=substr($month, -2); //get month from year in YYYY-mm
                    $month=$newmonth."-".$thisyear;
             if($data["grossnet"]){
                 if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
              $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date like '%$year%'");
                 }
                 else{
                    $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date like '%$year%'");
                 }
              
        }else{
              if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
               $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                               ->where("date like '%$year%'");
                             }
                             else{
                                 $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                             ->where("date like '%$year%'");
                             }
        }
                 
           if(count($data["customer"])>0 && !empty($data["customer"][0])&& !in_array("all",$data["customer"])){
       
            foreach ($data["customer"] as $cust) {
				if($cust){
                $clusterss.="'".$cust."',";
				}
                     }
	$valueee=rtrim($clusterss,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("budget.distributor_id IN (".$valueee.")");
        }
        
        if(count($data["distributor"])>0 && !empty($data["distributor"][0])&& !in_array("all",$data["distributor"])){
       
            foreach ($data["distributor"] as $cust) {
				if($cust){
                $distributors.="'".$cust."',";
				}
                     }
	$valueee=rtrim($distributors,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("budget.distributor_id IN (".$valueee.")");
        }
        
         if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
      $this->db->where("products.category_id IN (".$categoriess.")");
}

     if($data["products"] && !in_array("all",$data["products"])){
      foreach ($data["products"] as $pid) {
				if($pid){
                $products.="'".$pid."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("products.id IN (".$prods.")");
} 

  if(count($data["countrys"])>0 && !empty($data["countrys"][0])&& !in_array("all",$data["countrys"])){
       
            foreach ($data["countrys"] as $value) {
				if($value){
                $clusters.="'".$value."',";
				}
                     }
	$valuee=rtrim($clusters,",");
					// $valuee=rtrim($valuee.',');
             $this->db->where("budget.country IN (".$valuee.")");
        }
            $q=$this->db->get('budget');
             
            $value=$q->row();                
          if($value){
              return round($value->resale/1000,5);
          }else{
           return 0;   
          }
             
         }
         
         
         function  getPSoSalesByProductFamily($data,$month){
             
             
      
        if($data["grossnet"]){
              $this->db->select('SUM(shipping) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
                 ->join("products", "sale_items.product_id=products.id", 'left')
               ->where("movement_code='VE'")
                      ->where("sales_type='PSO'")
                        ->where("DATE_FORMAT(date,'%Y-%m')",$month)
               ;
        }else{
               $this->db->select('SUM(shipping) as resale')
                      ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
                      ->join("products", "sale_items.product_id=products.id", 'left')
                        ->where("movement_code='VE'")
                        ->where("sales_type='PSO'")
                       
                        ->where("DATE_FORMAT(date,'%Y-%m')",$month); 
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
             $this->db->where("sma_sales.distributor IN (".$valueee.")");
        }
        
         if(isset($data["productcategoryfamily"][0]) && !in_array("all",$data["productcategoryfamily"])){
      foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
      $this->db->where("products.category_id IN (".$categoriess.")");
}
            $q=$this->db->get('sales');
             
                            
          return $q->row();
         }
         
                 
         function getLast12Months($enddatemonthyear){
             $enddatemonthyear="31/".$enddatemonthyear;
             $time = DateTime::createFromFormat("d/m/Y",$enddatemonthyear);
             if(!$time){
                 $enddatemonthyear=date("31/12/".date("Y"));
                 $time= DateTime::createFromFormat("d/m/Y",$enddatemonthyear);
             }
$start = $time->modify('-1 year')->format('d/m/Y');

           
            $start=Datetime::createFromFormat("d/m/Y",$start);
    //$end      = (new DateTime())->modify('first day of this month');
	$end=Datetime::createFromFormat("d/m/Y",$enddatemonthyear);
	//die(print_r($end));
    $interval = new DateInterval('P1M');
    $period   = new DatePeriod($start, $interval, $end);
     
    $months = array();
    $count=0;
    foreach ($period as $dt) { 
        //if($count<12){
        array_push($months,$dt->format('Y-m'));
    //    }
        //$count++;
    }
    //$reverse_months = array_reverse($months);
   return $months;
             
         }
		 
     function getMonthsFromBeginingOfYear($endmonth)
          {
         $dates=array();
         $startmonth=substr($endmonth,0,2);    
         $startyear=substr($endmonth,-4);   
         $lastyear=$startyear-1;
         if($startmonth=="01"){
             $dates["thisyear"]="01-".$startyear;
             $dates["lastyear"]="01-".$lastyear;
         }
         else if($startmonth=="02"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear;  
		 
         }
		 
          else if($startmonth=="03"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear;  
		 
         }
    
           else if($startmonth=="04"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear;  
             
}
         
         else if($startmonth=="05"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear;  
             
         }
         
          else if($startmonth=="06"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear;  
             
         }
         
          else if($startmonth=="07"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear;    
             
         }
         
         else if($startmonth=="08"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear;    
             
         }
         else if($startmonth=="09"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear;    
             
         }
            else if($startmonth=="10"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear;    
             
         }
         
            else if($startmonth=="11"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear;    
             
         }
          else if($startmonth=="12"){
           $dates["thisyear"]="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
           return $dates;  
         }
         
         function getMonthsToEndOfYear($monthyear){
            $dates=array();
         $startmonth=substr($monthyear,0,2);    
         $startyear=substr($monthyear,-4);   
         $lastyear=$startyear-1;
            if($startmonth=="12"){
             $dates["thisyear"]="12-".$startyear;
           $dates["lastyear"]="12-".$lastyear;
         }
         else if($startmonth=="11"){
          $dates["thisyear"]="12-".$startyear;
           $dates["lastyear"]="12-".$lastyear;  
		 
         }
		 
          else if($startmonth=="10"){
          $dates["thisyear"]="11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="11-".$lastyear.",12-".$lastyear;    
		 
         }
    
           else if($startmonth=="09"){
         $dates["thisyear"]="10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="10-".$lastyear.",11-".$lastyear.",12-".$lastyear;   
             
}
         
         else if($startmonth=="08"){
           $dates["thisyear"]="09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;     
             
         }
         
          else if($startmonth=="07"){
            $dates["thisyear"]="08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
         
          else if($startmonth=="06"){
           $dates["thisyear"]="07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
         
         else if($startmonth=="05"){
           $dates["thisyear"]="06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
         else if($startmonth=="04"){
           $dates["thisyear"]="05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
            else if($startmonth=="03"){
            $dates["thisyear"]="04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
         
            else if($startmonth=="02"){
           $dates["thisyear"]="03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
          else if($startmonth=="01"){
           $dates["thisyear"]="02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
           $dates["lastyear"]="02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
             
         }
           return $dates;  
             
         }
         
        function getLast12MonthsYear($enddatemonthyear){
             
                   $time = DateTime::createFromFormat("d/m/Y", $enddatemonthyear);
        if (!$time) {
            $enddatemonthyear = date("01/12/" . date("Y"));
            $start = DateTime::createFromFormat("d/m/Y", $enddatemonthyear);
        }
        


        $start = Datetime::createFromFormat("d/m/Y", $start);
             
for ($i = 0; $i < 12; $i++) 
{
   $months[] = date("m-Y",strtotime($start." -$i months"));
}


         $months= array_reverse($months);
      // die(print_r($months));
        return $months;
    }
		 
    
    function updateConsolidatedSSO($data){
        $datearray=explode("-",$data["monthyear"]);
         if(strlen($datearray[1])< 2){$month="0".$datearray[1];}else{$month=$datearray[1];}
         $monthyear=$datearray[0]."-".$month."-01";
       
        $data["monthyear"]=$monthyear;
       $prd=$this->products_model->getProductByCode($data["gmid"]);
       $brand=$this->products_model->getCategoryById($prd->category_id);
       $data["brand_id"]=$brand->id;
       $data["brand"]=$brand->name;
        $data["bu"]=$brand->gbu;
      // $data["promotion"]=$prd->promoted;
//                 $this->db->select('id,gross_qty,gross_sale,tender_qty,tender_sale,net_budget,gross_budget,stock_qty,stock_value')
//                      ->where(array("country"=>$data["country"],"gmid"=>$data["gmid"],"monthyear"=>$data["monthyear"],"customer_id"=>$data["customer_id"],"distributor_id"=>$data["distributor_id"],"msr_id"=>$data["msr_id"]));
//                $q=$this->db->get('consolidated_sales_sso');
//                $result=$q->row();
//                if($result->id){
//                      //do update
//                 $this->db->where('id', $result->id);
//                // echo $data["gross_qty"]."fdf".$data["gross_sale"]."result".;
//                 $newdata=array("upload_type"=>$data["upload_type"],"gross_qty"=>(@$data["gross_qty"]+$result->gross_qty),"gross_sale"=>(@$data["gross_sale"]+$result->gross_sale),"tender_qty"=>(@$data["tender_qty"]+$result->tender_qty),"tender_sale"=>(@$data["tender_sale"]+$result->tender_sale),"net_budget"=>(@$data["net_budget"]+$result->net_budget),"gross_budget"=>(@$data["gross_budget"]+$result->gross_budget),"stock_qty"=>(@$data["stock_qty"]+$result->stock_qty),"stock_value"=>(@$data["stock_value"]+$result->stock_value));
//      // die(print_r($newdata));
//                 if ($this->db->update('consolidated_sales_sso', $newdata)) {
//            return true;
//        } else{
//            return FALSE;
//        }    
//                    
//                  
//                } else {
                    //do insert
                   // $prd=$this->products_model->getProductByCode($data["gmid"]);
                    $category=$this->products_model->getCategoryById($data["brand_id"]);
                    $data["brand"]=$category->name;
                    $data["brand_id"]=$category->id;
                    $data["bu"]=$category->gbu;
                    $this->db->insert('consolidated_sales_sso',$data);
                    return TRUE;
                
    }
		 
		 
    
}
