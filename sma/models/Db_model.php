<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Db_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getLatestSales()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get("sales", 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLastestQuotes()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get("quotes", 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestPurchases()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get("purchases", 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestTransfers()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get("transfers", 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestCustomers()
    {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get_where("companies", array('group_name' => 'customer'), 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    
    public function getBestSellingCustomers($data,$scenario)
    {
       
        if($data["grossnet"]){
        $this->db->select('SUM(shipping) as value,sales_type as sale,sma_companies.name as customer')
                ->where("sales_type",strtoupper($scenario))
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
              ->join("companies","sma_sales.customer_id=sma_companies.id",'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                    ->group_by('customer_id')
                  ->order_by('value', 'desc')
                ->limit(10);
        }
        else{
          $this->db->select('SUM(shipping)+SUM(total_discount) as value,sales_type as sale,sma_companies.name as customer')
                ->where("sales_type",strtoupper($scenario))
               ->join("sale_items", "sale_items.sale_id=sales.id", 'left')
              ->join("companies","sma_sales.customer_id=sma_companies.id",'left')
               ->join("products", "sale_items.product_id=products.id", 'left')
                    ->group_by('customer_id')
                  ->order_by('value', 'desc')
                ->limit(10);  
        }
					
					if($data["datefrom"] && $data["dateto"]){
            $datefrom="01-".str_replace("/","-",$data["datefrom"]);
			$datefromm=date("Y-m-d",strtotime($datefrom));
            $dateto="31-".str_replace("/","-",$data["dateto"]);
			$datetoo=date("Y-m-d",strtotime($dateto));
		 $this->db->where('DATE_FORMAT(sma_sales.date,"%Y-%m-%d") BETWEEN "' . $datefromm . '" and "' . $datetoo . '"');	
       // $this->db->where("date BETWEEN '".$datefromm."' AND '".$datetoo."'");   
       
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
        
        
   //die($data["productcategoryfamily"]."dsd");
    if(count($data["productcategoryfamily"]) >0 && !in_array("all",$data["productcategoryfamily"])){
        
              foreach ($data["productcategoryfamily"] as $cat) {
				if($cat){
                $categories.="'".$cat."',";
				}
                     }
		 $categoriess=rtrim($categories,",");
         $this->db->where("products.category_id IN (".$categoriess.")");                                                                                                                                                                                                   
  
}
if($data["gbu"] && $data["gbu"] !="all"){
$this->db->where('products.business_unit', $data["gbu"]);	
}
            $q=$this->db->get('sales');
         
            $dataa=array();

            $i=0;
           
             if (count($q)> 0) {
           foreach ($q->result() as $row){
                if($row->customer){
               $datan["customer"]=$row->customer;
    $datan["salesvalue"]=$row->value;
    $datan["saletype"]=$row->sale;
    array_push($dataa, $datan);
                $i++;
                }
            }
            
            //die(print_r($dataa));
            if(count($dataa)>0){
        return json_encode($dataa);
            }else{
        return json_encode(array(array("customer"=>"None","salesvalue"=>1),array("customer"=>"None","salesvalue"=>1)));
   
            } 
             }
        else{
             return json_encode(array(array("customer"=>"None","salesvalue"=>1),array("customer"=>"None","salesvalue"=>1)));
        }
        
       
    }
    
    
    
    public function getLatestSuppliers()
    {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get_where("companies", array('group_name' => 'supplier'), 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getChartData()
    {
//        $myQuery = "SELECT S.month,
//        COALESCE(MAX(S.sales), 0) as sales,
//        COALESCE(MAX(P.purchases), 0 ) as purchases,
//        COALESCE(MAX(S.tax1), 0) as tax1,
//        COALESCE(MAX(S.tax2), 0) as tax2,
//        COALESCE(MAX(P.ptax), 0 ) as ptax
//        FROM (  SELECT  FORMAT(date, 'yyyy-MM') Month,
//                SUM(total) Sales,
//                SUM(product_tax) tax1,
//                SUM(order_tax) tax2
//                FROM " . $this->db->dbprefix('sales') . "
//                WHERE date >= dateadd( MONTH, -12, GETDATE( ))
//                GROUP BY FORMAT(date, 'yyyy-MM')) S
//            LEFT JOIN ( SELECT  FORMAT(date, 'yyyy-MM') Month,
//                        SUM(product_tax) ptax,
//                        SUM(order_tax) otax,
//                        SUM(total) purchases
//                        FROM " . $this->db->dbprefix('purchases') . "
//                        GROUP BY FORMAT(date, 'yyyy-MM')) P
//            ON S.Month = P.Month
//            GROUP BY S.Month
//            ORDER BY S.Month";
        
         $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(product_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(total) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            GROUP BY S.Month
            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStockValue()
    {
        $q = $this->db->query("SELECT SUM(qty*price) as stock_by_price, SUM(qty*cost) as stock_by_cost
        FROM (
            Select sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0)) as qty, '0' as price, '0' as cost
            FROM " . $this->db->dbprefix('products') . "
            JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id
            GROUP BY " . $this->db->dbprefix('warehouses_products') . ".id ) a");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getBestSeller($start_date = NULL, $end_date = NULL)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }
        $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, MAX(s.date) as sdate from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id where s.date >= '{$start_date}' and s.date < '{$end_date}' group by si.product_id ) PSales";
        $this->db
            ->select("CONCAT(" . $this->db->dbprefix('products') . ".name,' ') as name, COALESCE( PSales.soldQty, 0 ) as SoldQty", FALSE)
            ->from('products', FALSE)
            ->join($sp, 'products.id = PSales.product_id', 'left')
            ->order_by('PSales.soldQty desc')
			//->group_by('sdate')
            ->limit(10);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

}
