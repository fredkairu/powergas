<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Budget_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

 
public function add_budget($products = array())
    {
            
   
         $this->db->insert('budget', $products);
         $budget_id = $this->db->insert_id();
         if($products["scenario"]=="SSO"){
             $prd=$this->products_model->getProductById($products["product_id"]);
             $currency=$this->settings_model->getCurrencyByID($products["country"]);
    if($products["net_gross"]=="N"){
             $this->sales_model->updateConsolidatedSSO(array("upload_type"=>"BUDGET","country"=>$currency->country,"country_id"=>$products["country"],"gmid"=>$prd->code,"product_name"=>$prd->name,"monthyear"=>$products["date"],"customer_sanofi"=>$products["customer_name"],"customer_id"=>$products["customer_id"],"net_budget"=>$products["budget_value"],"budget_qty"=>$products["budget_qty"],"msr_id"=>$products["msr_alignment_id"],"msr_name"=>$products["msr_alignment_name"],"budget_id"=>$budget_id));
    } else{
     $this->sales_model->updateConsolidatedSSO(array("upload_type"=>"BUDGET","country"=>$currency->country,"country_id"=>$products["country"],"gmid"=>$prd->code,"product_name"=>$prd->name,"monthyear"=>$products["date"],"customer_sanofi"=>$products["customer_name"],"customer_id"=>$products["customer_id"],"gross_budget"=>$products["budget_value"],"budget_qty"=>$products["budget_qty"],"msr_id"=>$products["msr_alignment_id"],"msr_name"=>$products["msr_alignment_name"],"budget_id"=>$budget_id));
    }                              
}
 
      return true;
    
    
           
    }
    
    public function remove_PSObudgetdata($month,$scenario,$net_gross)
    {
		
		$results=$this->db->query("DELETE FROM sma_budget WHERE scenario = '$scenario' AND date = '$month'  AND  net_gross = '$net_gross' ");     
	if($results){
		return true;
	} else{
		 return FALSE;
	}
       // return FALSE;
		
    }
    
     public function remove_SSObudgetdata($month,$scenario,$net_gross,$countryid)
    {
		$resultss=$this->db->query("DELETE FROM sma_consolidated_sales_sso WHERE budget_id IN (SELECT id from sma_budget WHERE scenario = '$scenario' AND date = '$month'  AND net_gross = '$net_gross' AND country='$countryid')  ");     
		$results=$this->db->query("DELETE FROM sma_budget WHERE scenario = '$scenario' AND date = '$month'  AND net_gross = '$net_gross' AND country='$countryid'  ");     
	if($results){
		return true;
	} else{
		 return FALSE;
	}
        //return FALSE;
		
    }
    public function getCountryByCode($code)
    {
        $q = $this->db->get_where('currencies', array('country' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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
     public function getBudgetData($data)
    {
       if ($data['scenario']=="SSO"){
           
       }
     $q = $this->db->get_where('budget',array('scenario'=>$data['scenario'],'date'=>$data['date'],'country'=>$data['country'],'distributor_id'=>$data['distributor_id'],'product_id'=>$data['product_id'],"budget_forecast"=>$data['budget_forecast']));
      if ($q->num_rows() > 0) {
           foreach (($q->result()) as $row) {
             //  print_r($row);  
              $this->db->delete("budget", array('id' => $row->id));
            }
    
     
    } else{
        return FALSE;
    }
    }
    
    public function deleteBudget($id)
    {
        if ($this->db->delete("budget", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
    
    
    function getBudget($salestype,$data){
       $month=date("Y");
       $budgetorforecast="budget";
     if($data["grossnet"]){
                 if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
              $this->db->select('SUM(budget_at_resale) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                      ->where("net_gross='G'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date LIKE '%".$month. "%'");
                 }
                 else{
                    $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                            ->where("net_gross='G'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date LIKE '%".$month. "%'");
                 }
              
        }else{
              if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
               $this->db->select('SUM(budget_at_resale) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                       ->where("net_gross='N'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                              ->where("date LIKE '%".$month. "%'");
                             }
                             else{
                                 $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                                          ->where("scenario='".$salestype."'")
                                         ->where("net_gross='N'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                              ->where("date LIKE '%".$month. "%'");
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
              return round($value->resale/1000,2);
          }else{
           return 0;   
          }
    }
    
     public function getBudgetByID($id)
    {
        $q = $this->db->get_where('sma_budget', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    function getMonthlyBudget($salestype,$data,$monthyear){
       
       $budgetorforecast="budget";
     if($data["grossnet"]){
                 if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
              $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                      ->where("net_gross='G'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date IN ($monthyear) ");
                 }
                 else{
                    $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                            ->where("net_gross='G'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date IN ($monthyear) ");
                 }
              
        }else{
              if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
               $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                       ->where("net_gross='N'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                              ->where("date IN ($monthyear) ");
                             }
                             else{
                                 $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                                         
                        ->where("scenario='".$salestype."'")
                                         ->where("net_gross='N'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                             ->where("date IN ($monthyear) ");
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
                $products.="'".$cat."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("products.id IN (".$prods.")");
} 
if($data["gbu"] && $data["gbu"] !="all"){
$this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
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
              return round($value->resale/1000,2);
          }else{
           return 0;   
          }
    }
    
    function getBudgetForMonth($salestype,$data,$monthyear){
        
       $month=  substr($monthyear,0,2);
       $year=  substr($monthyear,-4);
       $monthyear="'".$year."-".$month."-01"."'";
       $budgetorforecast="budget";
     if($data["grossnet"]){
                 if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
              $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                      ->where("net_gross='G'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date IN ($monthyear)");
                 }
                 else{
                    $this->db->select('SUM(budget_value) as resale')
                     
                 ->join("products", "budget.product_id=products.id", 'left')
                      ->where("scenario='".$salestype."'")
                            ->where("net_gross='G'")
                      ->where("budget_forecast='".$budgetorforecast."'")
                        ->where("date IN ($monthyear) ");
                 }
              
        }else{
              if(strtolower($salestype)=="pso" || strtolower($salestype)=="sso"){
               $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                       ->where("net_gross='N'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                              ->where("date IN ($monthyear) ");
                             }
                             else{
                                 $this->db->select('SUM(budget_value) as resale')
                      ->join("products", "budget.product_id=products.id", 'left')
                        ->where("scenario='".$salestype."'")
                                         ->where("net_gross='N'")
                        ->where("budget_forecast='".$budgetorforecast."'")
                                             ->where("date IN ($monthyear) ");
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
                $products.="'".$cat."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("products.id IN (".$prods.")");
} 

if($data["gbu"] && $data["gbu"] !="all"){
 $this->db->join("categories", "products.category_id=categories.id", 'left');
$this->db->where('categories.gbu', $data["gbu"]);	
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
              return round($value->resale/1000,2);
          }else{
           return 0;   
          }
    }
     function getBudgetForMonthConsolidated($salestype,$data,$monthyear){
        
       $month=  substr($monthyear,0,2);
       $year=  substr($monthyear,-4);
       $monthyear="'".$year."-".$month."'";
       $budgetorforecast="budget";
     if($data["grossnet"]){
              
              $this->db->select('SUM(gross_budget) as resale')
               
                        ->where("DATE_FORMAT(monthyear,'%Y-%m') IN ($monthyear)");
                
              
        }else{
            
               $this->db->select('SUM(net_budget) as resale')
                      
                                              ->where("DATE_FORMAT(monthyear,'%Y-%m') IN ($monthyear) ");
                            
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
}

     if($data["product"] && !in_array("all",$data["product"])){
      foreach ($data["product"] as $pid) {
				if($pid){
                $products.="'".$cat."',";
				}
                     }
		 $prods=rtrim($products,",");
         $this->db->where("consolidated_sales_sso.gmid IN (".$prods.")");
} 

if($data["gbu"] && $data["gbu"] !="all"){

$this->db->where('consolidated_sales_sso.bu', $data["gbu"]);	
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
              return round($value->resale/1000,2);
          }else{
           return 0;   
          }
    }
    
    public function getClusters()
    {
        $q = $this->db->get('budget');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
   
    
    
    function get_countries_cluster ($cluster){
        $this->db->select('currencies.id,country');
        $this->db->join("cluster","cluster.id=currencies.cluster");
       $this->db->where("cluster.name",$cluster);
       
        $query = $this->db->get('currencies');
        $cities = array();

        if($query->result()){
            foreach ($query->result() as $city) {
                $cities[$city->id] = $city->country;
            }
            return $cities;
        } else {
            return FALSE;
        }
    }
    
  
    function getMsrTotalBudgetForPeriodWithProduct($monthyearsstring,$msrid,$customer_id,$product_id,$salestype,$budgetforecast,$filters){
    // die(print_r($filters));
         if($filters["period"]){
              $period=$filters["period"];
              $monthyearsstring=$period;
              
          }
          
          if($filters["countrys"]){
              $countrys=$filters["countrys"];
               $this->db->where("country_id IN ($countrys) ");
          }
          if($filters["net_gross"]){
             $this->db->select('SUM(gross_budget) as resale');
          } else{
             $this->db->select('SUM(net_budget) as resale');
          }
          //die(print_r($filters));
          if($filters["gbu"] && $filters["gbu"] !="all" && !empty($filters["gbu"])){
              
              $gbu=$filters["gbu"];
               $this->db->where("bu IN ('".$gbu."') ");
          }
       
                    $this->db->where("msr_id",$msrid);
                    $this->db->where("customer_id",$customer_id);
                             $this->db->where("gmid",$product_id)
               ->where("monthyear IN ($monthyearsstring) "); 
       $q=$this->db->get('consolidated_sales_sso');
             
            $value=$q->row();                
          
              return round($value->resale/1000,4);
         
       
    }
    

     function getMsrTotalSalesForPeriodWithProduct($monthyearsstring,$msrid,$customer_id,$product_id,$salestype,$filters){
     //    die(print_r($filters));
        if($filters["period"]){
              $period=$filters["period"];
              $monthyearsstring=$period;
              
          }
          
          if($filters["countrys"]){
              $countrys=$filters["countrys"];
               $this->db->where("country_id IN ($countrys) ");
          }
           if($filters["net_gross"]){
             $this->db->select('SUM(gross_sale) as value');
          } else{
              $this->db->select('SUM(net_sale) as value');
          }
          
           if($filters["gbu"] && $filters["gbu"] !="all" && !empty($filters["gbu"])){
              
              $gbu=$filters["gbu"];
               $this->db->where("bu IN ('".$gbu."') ");
          }
      
              // ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
             
                                              //->where("scenario='".$salestype."'")
                        //->where("budget_forecast='".$budgetorforecast."'")
                        $this->db->where("msr_id",$msrid);
                        $this->db->where("customer_id",$customer_id);
                        $this->db->where("gmid",$product_id)
             
                                              ->where("monthyear IN ($monthyearsstring) "); 
       $q=$this->db->get('consolidated_sales_sso');
             
            $value=$q->row();                
          
              return round($value->value/1000,4);
         
       
    }
    
 function getMsrTotalBudgetForPeriod($monthyearsstring,$msrid,$salestype,$budgetforecast,$filters){
    // die(print_r($filters));
         if($filters["period"]){
              $period=$filters["period"];
              $monthyearsstring=$period;
                      }
          
          if($filters["countrys"]){
              $countrys=$filters["countrys"];
               $this->db->where("country_id IN ($countrys) ");
          }
          if($filters["net_gross"] || $filters["gsales"] ){
             $this->db->select('SUM(gross_budget) as resale');
          } else{
             $this->db->select('SUM(net_budget) as resale');
          }
          //die(print_r($filters));
          if($filters["gbu"] && $filters["gbu"] !="all" && !empty($filters["gbu"])){
              
              $gbu=$filters["gbu"];
               $this->db->where("bu IN ('".$gbu."') ");
          }
       
                    $this->db->where("msr_id",$msrid)
               ->where("monthyear IN ($monthyearsstring) "); 
       $q=$this->db->get('consolidated_sales_sso');
             
            $value=$q->row();                
          
              return round($value->resale/1000,4);
         
       
    }
    

     function getMsrTotalSalesForPeriod($monthyearsstring,$msrid,$salestype,$filters){
     //   die(print_r($filters));
        if($filters["period"]){
              $period=$filters["period"];
              $monthyearsstring=$period;
              
          }
          
          if($filters["countrys"]){
              $countrys=$filters["countrys"];
               $this->db->where("country_id IN ($countrys) ");
          }
           if($filters["net_gross"]){
             $this->db->select('SUM(gross_sale) as value');
          } else{
              $this->db->select('SUM(net_sale) as value');
          }
      
           if($filters["gbu"] && $filters["gbu"] !="all" && !empty($filters["gbu"])){
              
              $gbu=$filters["gbu"];
               $this->db->where("bu IN ('".$gbu."') ");
          }
              // ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
             
                                              //->where("scenario='".$salestype."'")
                        //->where("budget_forecast='".$budgetorforecast."'")
                        $this->db->where("msr_id",$msrid)
             
                                              ->where("monthyear IN ($monthyearsstring) "); 
       $q=$this->db->get('consolidated_sales_sso');
             
            $value=$q->row();                
          if($value){
              return round($value->value/1000,2);
          }else{
           return 0;   
          }
       
    }

     function getTeamTotalBudgetForPeriod($monthyearsstring,$teamid,$salestype,$budgetforecast,$filters){
         if($filters["period"]){
              $period=$filters["period"];
              $monthyearsstring=$period;
              
          }
          
          if($filters["countrys"]){
              $countrys=$filters["countrys"];
               $this->db->where("country IN ($countrys) ");
          }
          if($filters["gsales"]){
              $this->db->where("net_gross","G");
          } else{
              $this->db->where("net_gross","N");
          }
       $this->db->select('SUM(budget_value) as resale')
                     ->where("msr_alignment_id",$teamid)
               ->where("date IN ($monthyearsstring) "); 
       $q=$this->db->get('budget');
             
            $value=$q->row();                
          if($value){
              return round($value->resale/1000,2);
          }else{
           return 0;   
          }
       
    }
    

     function getTeamTotalSalesForPeriod($monthyearsstring,$teamid,$salestype,$filters){
     //    die(print_r($filters));
        if($filters["period"]){
              $period=$filters["period"];
              $monthyearsstring=$period;
              
          }
          
          if($filters["countrys"]){
              $countrys=$filters["countrys"];
               $this->db->where("country_id IN ($countrys) ");
          }
           if($filters["gsales"]){
             $this->db->select('SUM(gross_sale) as value');
          } else{
              $this->db->select('SUM(net_sale) as value');
          }
      
              // ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
             
                                              //->where("scenario='".$salestype."'")
                        //->where("budget_forecast='".$budgetorforecast."'")
                        $this->db->where("team_id",$teamid)
             
                                              ->where("monthyear IN ($monthyearsstring) "); 
       $q=$this->db->get('consolidated_sales_sso');
             
            $value=$q->row();                
          if($value){
              return round($value->value/1000,2);
          }else{
           return 0;   
          }
       
    }
    

}
