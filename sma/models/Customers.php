<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companies_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllSupplierCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomerGroups()
    {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyUsers($company_id)
    {
        $q = $this->db->get_where('users', array('company_id' => $company_id));
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

    public function getCompanyByName($name)
    {
         $trimmedname=str_replace(" ","",$name);
         $trimmednamee=str_replace("'","-",$trimmedname);
         $this->db->select("id");
        $this->db->where(" REPLACE(name,' ','')='".$trimmednamee."'");
        $q = $this->db->get('companies',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getADistributorCustomer($id)
    {
        
          $this->db->select('customer_dist_sanofi_mapping.id,customers.name,currencies.country,customers.name,customer_dist_sanofi_mapping.distributor_naming,customer_dist_sanofi_mapping.distributor_code')
        ->join('products', 'products.id=customer_dist_sanofi_mapping.product_id', 'left')
        ->join('customers', 'customers.id=customer_dist_sanofi_mapping.customer_id', 'left')
            ->join('currencies', 'customer_dist_sanofi_mapping.country=currencies.id', 'left')
                 //  ->where('companies', array('group_name' =>'customer'))
            ->order_by('customer_dist_sanofi_mapping.id', 'asc');
          
      $q = $this->db->get_where('customer_dist_sanofi_mapping',array('customer_id'=>$id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getCompanyByNameAndCountry($name,$country)
    {
         $trimmedname=str_replace(" ","",$name);
         $trimmednamee=str_replace("'","-",$trimmedname);
         $this->db->select("id");
        $this->db->where(" REPLACE(name,' ','')='".$trimmednamee."'");
        $this->db->where("country='".$country."'");
        $q = $this->db->get('companies',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCompanyByEmail($email)
    {
        $q = $this->db->get_where('companies', array('email' => $email), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCompany($data = array())
    {
        
        //check for erp suppliers and debtors lastid
       
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
             if($data['group_name']=="supplier"){
                 $id=$this->findLastIdSupplier($cid);
                 if($id>0){
                 $this->db->where('id',$cid);
                 $newdata["id"]=$id+1;
$this->db->update('companies',$newdata);  
$cid=$id+1;
                 }
        }
        
        else if($data['group_name']=="customer"){
                $id=$this->findLastIdCustomer($cid);
                 if($id>0){
                 $this->db->where('id',$cid);
                 $newdata["id"]=$id+1;
$this->db->update('companies',$newdata);  
$cid=$id+1;
        }
        
            
            
           
        }
            $data["person_id"]=$cid;
            //add to ERP
            
           // $this->addPersonToErp($data);
      return $cid; 
    }
      return false;
    }
    
    
      public function addSubCompany($data = array())
    {
          foreach ($data as $value) {
              
         
$last_id=$this->db->insert('distributor_mapping',$value);  
 }
      return $last_id; 
 
    }
    
    public function findLastIdSupplier($id){
    $this->db->set_dbprefix('0_');
    $q = $this->db->get_where('suppliers', array('supplier_id' =>$id));
        if ($q->num_rows() > 0) {
       $maxid = $this->db->query('SELECT MAX(supplier_id) AS `maxid` FROM `0_suppliers`')->row()->maxid;
        $this->db->set_dbprefix('sma_');
      return $maxid;      
    }
     $this->db->set_dbprefix('sma_');
    return 0;
    }
    public function findLastIdCustomer($id){
       $this->db->set_dbprefix('0_');
    $q = $this->db->get_where('debtors_master', array('debtor_no' =>$id));
        if ($q->num_rows() > 0) {
       $maxid = $this->db->query('SELECT MAX(debtor_no) AS `maxid` FROM `0_debtors_master`')->row()->maxid;
        $this->db->set_dbprefix('sma_');
      return $maxid;      
    }
     $this->db->set_dbprefix('sma_');
    return 0; 
    }
    public function addPersonToErp($data){
        
        $this->db->set_dbprefix('0_');
        if($data['group_name']=="supplier"){
       
         $supplier['supplier_id']=$data['person_id'];
            $supplier['supp_name']=$data['name'];
                    $supplier['supp_ref']=$data['name'];
                 $supplier['address']=$data['address'];
                         $supplier['supp_address']=$data['address'];
                         $supplier['gst_no']=$data['vat_no'];
                         $supplier['contact']=$data['phone'];
                         $supplier['curr_code']='KS';
                         $supplier['payment_terms']='4';
                         $supplier['tax_included']=1;
                         $supplier['dimension_id']=0;
                         $supplier['dimension2_id']=1;
                         $supplier['tax_group_id']=1;
                         $supplier['credit_limit']=10000;
                         $supplier['purchase_account']=3011130;
                         $supplier['payable_account']=2100; 
                         $supplier['payment_discount_account']=5060;
                         $supplier['inactive']=0;
         $this->db->insert('suppliers',$supplier);
     unset($supplier);
        }
        else if($data['group_name']=="customer"){
            
          $customer['debtor_no']=$data['person_id'];
           $customer['name']=$data['name'];
                 $customer['debtor_ref']=$data['name'];
                  $customer['address']=$data['address'];
                  $customer['tax_id']=$data['vat_no'];
                 $customer['curr_code']='KS';
                 $customer['sales_type']=1; 
                 $customer['dimension_id']=0;
                 $customer['dimension2_id']=0;
                 $customer['credit_status']=1; 
                 $customer['payment_terms']=4;//credit customer 
                 $customer['discount']=0; 
                 $customer['credit_limit']=10000; 
                 $customer['inactive']=0;
           $this->db->insert('debtors_master',$customer);  
           //insert into customer branch
           
                   $custbranch['debtor_no']=$data['person_id'];
                   $custbranch['br_name']=$data['name'];
                   $custbranch['branch_ref']=$data['name'];
                   $custbranch['br_address']=$data['address'];
                   $custbranch['area']=1;
                   $custbranch['salesman']=1;
                   $custbranch['default_location'] ='DEF';
                   $custbranch['tax_group_id']=1;
                   $custbranch['sales_account']=100101;
                   $custbranch['sales_discount_account'] =4510;
                   $custbranch['receivables_account']=1200;
                   $custbranch['payment_discount_account'] =1060;
                   $custbranch['default_ship_via']=1;
                   $custbranch['br_post_address']=$data['address'];
                   $custbranch['group_no']=0;
                          $custbranch['inactive']=0;
           
           $this->db->insert('cust_branch',$custbranch);  
           
           
           
           
     unset($custbranch); 
            
            
        }
        
          $this->db->set_dbprefix('sma_');
    }
    
    
    
    
    
    
    
    
    

    public function updateCompany($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('companies', $data)) {
            return true;
        }
        return false;
    }

    public function addCompanies($data = array())
    {
        if ($this->db->insert_batch('companies', $data)) {
            return true;
        }
        return false;
    }
    
    
     
        public function addAlignment($data)
    {
        if ($this->db->insert('alignments', $data)) {
            return true;
        }
        return false;
    }
    

    public function deleteCustomer($id)
    {
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'customer')) && $this->db->delete('users', array('company_id' => $id))) {
            $this->db->delete('companies', array('parent_company' => $id, 'group_name' => 'customer'));
            return true;
        }
        return FALSE;
    }
   

    public function deleteSupplier($id)
    {
        if ($this->getSupplierPurchases($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'supplier')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteBiller($id)
    {
        if ($this->getBillerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'biller'))) {
            return true;
        }
        return FALSE;
    }

    public function getBillerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, company as text");
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'biller'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, CONCAT(company, ' (', name, ')') as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'customer'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getSupplierSuggestions($term, $limit = 10)
    {
        $this->db->select("id, CONCAT(company, ' (', name, ')') as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'customer'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSales($id)
    {
        $this->db->where('customer_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getBillerSales($id)
    {
        $this->db->where('biller_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getSupplierPurchases($id)
    {
        $this->db->where('supplier_id', $id)->from('purchases');
        return $this->db->count_all_results();
    }

}
