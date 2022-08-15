<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Country_productpricing_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
     public function addProductPricing($products)
    {
    if (!empty($products)) {
            foreach ($products as $product) {
   
              $inserted=$this->db->insert('countryproductpricing', $product);
      
          
    }
    return TRUE;
    
    }
    else {
        return false;
    }
    }
    
    
        public function duplicateCountryProduct($id,$fromdate,$todate)
    {
           
        
        $this->db->select($this->db->dbprefix('countryproductpricing') . '.* ');
                     

        $q = $this->db->get_where('countryproductpricing', array('countryproductpricing.id' => $id));
        if ($q->num_rows() > 0) {
            $ctry=$q->row();
          $this->db->insert('countryproductpricing',array("country_id"=>$ctry->country_id,"product_id"=>$ctry->product_id,"product_name"=>$ctry->product_name,"unified_price"=>$ctry->unified_price,"resell_price"=>$ctry->resell_price,"tender_price"=>$ctry->tender_price,"supply_price"=>$ctry->supply_price,"from_date"=>$fromdate,"to_date"=>$todate,"promotion"=>$ctry->promotion));  
          return TRUE;
        }
        return FALSE;
    }
     
    
     public function getCountryProductPricingForDate($productid,$country_id,$todate)
    {
        $this->db->select($this->db->dbprefix('countryproductpricing') . '.*, ' . $this->db->dbprefix('currencies') . '.country')
            ->join('currenciesk', 'currencies.id=countryproductpricing.country_id', 'left');
        $q = $this->db->get_where('countryproductpricing', array('countryproductpricing.product_id'=>$productid,'countryproductpricing.country_id'=>$country_id,"to_date"=>$todate));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     public function deleteCountryProductPricingForDate($productid,$country_id,$todate)
    {
        $results=$this->db->query("DELETE FROM sma_countryproductpricing WHERE sma_countryproductpricing.product_id = '$productid' AND sma_countryproductpricing.country_id = '$country_id' AND to_date = '$todate' ");     

       if($results){
			return true;
	} else{
		 return FALSE;
	}
        return FALSE;
    }
    
        public function getCountryProductsCategorized($id)
    {
         $this->db->order_by("to_date","ASC");
              $this->db->group_by("to_date");
              
        $this->db->select($this->db->dbprefix('countryproductpricing') . '.id,from_date,to_date ');
                     

        $q = $this->db->get_where('countryproductpricing', array('countryproductpricing.country_id' => $id));
        if ($q->num_rows() > 0) {
           foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }
     public function getCountryProducts($id=NULL,$startdate=NULL,$enddate=NULL)
    {
         $this->db->order_by("to_date","ASC");
         $formatedstartdate=  str_replace("/","",$startdate);
         $formatedenddate=str_replace("/","",$enddate);
         
     
        if($id){
        $this->db->select($this->db->dbprefix('countryproductpricing') . '.*, ' . $this->db->dbprefix('currencies') . '.country,'.$this->db->dbprefix('products') . '.code as code,'.$this->db->dbprefix('customers').'.name as cname,'.$this->db->dbprefix('companies').'.name as dname')
            ->join('currencies', 'currencies.id=countryproductpricing.country_id', 'left')
            ->join('customers', 'customers.id=countryproductpricing.customer_id', 'left')
            ->join('companies', 'companies.id=countryproductpricing.distributor_id', 'left')
                     ->join('products', 'countryproductpricing.product_id=products.id', 'left');
        if($formatedstartdate && $formatedenddate){
            $this->db->where("STR_TO_DATE(from_date,'%m/%Y') >= STR_TO_DATE('$startdate','%m/%Y') AND STR_TO_DATE(to_date,'%m/%Y') <= STR_TO_DATE('$enddate','%m/%Y')");
            // $this->db->where("(REPLACE(from_date,'/','')>='$formatedstartdate' AND REPLACE(to_date,'/','') <='$formatedenddate')");
          //  $this->db->where("countryproductpricing.from_date='".$startdate."' AND countryproductpricing.to_date='".$enddate."'" );
        }

        $q = $this->db->get_where('countryproductpricing', array('countryproductpricing.country_id' => $id));
        }
        
        else{
           $this->db->order_by("country","ASC");
     
            $this->db->select($this->db->dbprefix('countryproductpricing') . '.*, ' . $this->db->dbprefix('currencies') . '.country,'.$this->db->dbprefix('products') . '.code as code,'.$this->db->dbprefix('customers').'.name  as cname,'.$this->db->dbprefix('companies').'.name as dname')
            ->join('currencies', 'currencies.id=countryproductpricing.country_id', 'left')
            ->join('customers', 'customers.id=countryproductpricing.customer_id', 'left')
            ->join('companies', 'companies.id=countryproductpricing.distributor_id', 'left')
                     ->join('products', 'countryproductpricing.product_id=products.id', 'left');
      
             if($formatedstartdate && $formatedenddate){
             $this->db->where("(REPLACE(from_date,'/','')>='$formatedstartdate' AND REPLACE(to_date,'/','') <='$formatedenddate')");
         
        }
           $q=$this->db->get('countryproductpricing');
       }

      
        
        if ($q->num_rows() > 0) {
           foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }
 public function getCustomerByID($id)
    {
        $q = $this->db->get_where('customers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getdistributorByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCountryProductById($id)
    {
        $this->db->select($this->db->dbprefix('countryproductpricing') . '.*, ' . $this->db->dbprefix('currencies') . '.country')
            ->join('currencies', 'currencies.id=countryproductpricing.country_id', 'left');
        $q = $this->db->get_where('countryproductpricing', array('countryproductpricing.id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    
    public function getCountryProductPricing($productid,$country_id,$datemonthyear)
    {
        if(!$datemonthyear){
            $datemonthyear=date("Y-m-d");
        }
        $formateddate=date("mY",  strtotime($datemonthyear));
        $this->db->select($this->db->dbprefix('countryproductpricing') . '.*, ' . $this->db->dbprefix('currencies') . '.country')
            ->join('currencies', 'currencies.id=countryproductpricing.country_id', 'left')
          ->where("(REPLACE(from_date,'/','')>='$formateddate' AND REPLACE(to_date,'/','') <='$formateddate')");
        $q = $this->db->get_where('countryproductpricing', array('countryproductpricing.product_id'=>$productid,'countryproductpricing.country_id'=>$country_id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
     public function updatePrice($id,$data = array())
    {

        if ($this->db->update('countryproductpricing',$data,array('id'=>$id))) {
            return true;
        } else {
            return false;
        }
    }
    
     public function deletePricing($id)
    {
        if ($this->db->delete("countryproductpricing", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
    
    
}
