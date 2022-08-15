<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
         $this->db2 = $this->load->database('default1', TRUE);
         //$this->db = $this->load->database('default', TRUE);
    }

    public function updateLogo($photo)
    {
        $logo = array('logo' => $photo);
        if ($this->db->update('settings', $logo)) {
            return true;
        }
        return false;
    }

    public function updateLoginLogo($photo)
    {
        $logo = array('logo2' => $photo);
        if ($this->db->update('settings', $logo)) {
            return true;
        }
        return false;
    }

    public function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDateFormats()
    {
        $q = $this->db->get('date_format');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function updateSetting($data)
    {
        $this->db->where('setting_id', '1');
        if ($this->db->update('settings', $data)) {
            return true;
        }
        return false;
    }

    public function addTaxRate($data)
    {
        if ($this->db->insert('tax_rates', $data)) {
            return true;
        }
        return false;
    }
    public function addExchangeRate($data)
    {
        if ($this->db->insert('conversion', $data)) {
            return true;
        }
        return false;
    }
    
    public function addTeam($data)
    {
        if ($this->db->insert('team', $data)) {
            return true;
        }
        return false;
    }
    public function addDSM($data)
    {
        if ($this->db->insert('dsm_alignments', $data)) {
            return true;
        }
        return false;
    }

    public function updateTaxRate($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('tax_rates', $data)) {
            return true;
        }
        return false;
    }
    public function updateMSR($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('msr_alignments', $data)) {
            return true;
        }
        return false;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
public function getAllMsr()
    {
        $q = $this->db->get('msr_alignments');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getmaxbrandcode() {
		$this->db->select('max(id)+1 as id ');
        $q = $this->db->get('sma_categories');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getMsrNotAssigned()
    {
         $q = $this->db->query("SELECT * FROM `sma_msr_alignments` WHERE id NOT in (SELECT alignment_id  from sma_employee WHERE group_id = '1' )");

        //$q = $this->db->get('msr_alignments');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
   public function getMsrAlignmentByname($id,$coun)
    {
        $this->db->select("id,msr_alignment_name,msr_alignment_name as name,country_id,country,business_unit,team_id,team_name  ");
        $q = $this->db->get_where('msr_alignments', array('msr_alignment_name' => $id,'country'=> $coun), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 
    public function getDsmAlignmentByname($id,$coun)
    {
         $this->db->select("id,dsm_alignment_name,dsm_alignment_name as name,country_id,country,business_unit ");
        $q = $this->db->get_where('dsm_alignments', array('dsm_alignment_name' => $id,'country'=> $coun), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 
     public function getMsrByID($id)
    {
        $q = $this->db->get_where('msr_alignments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 
    public function getAllTeams() {
        $q = $this->db->get('team');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getTeamsForCountryID($coun)
    {
       $this->db->select('id as id, name as text');
       // $this->db->select('id,name,country_id,country,business_unit');
        $q = $this->db->get_where("team", array('country_id' => $coun));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return FALSE;
    } 
    public function getDsmByID($id)
    {
      
        $q = $this->db->get_where("dsm_alignments", array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 

    public function getDSMteams($id)
    {
      
        $q = $this->db->get_where("dsm_team_mapping", array('dsm_alignment_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 
     public function getTeamsNoDSMForCountryID($id)
    {
       $q = $this->db->query("SELECT id,name ,country_id,country FROM `sma_team` WHERE country_id  = $id and id NOT in(SELECT team_id from sma_dsm_team_mapping )");
        //$q = $this->db->get_where("dsm_team_mapping", array('dsm_alignment_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
   
      public function getDsmNotAssigned()
    {
        $q = $this->db->query("SELECT * FROM `sma_dsm_alignments` WHERE id NOT in (SELECT alignment_id  from sma_employee WHERE group_id = '2' )");

         // $q = $this->db->get('dsm_alignments');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 
  public function getAllDsm()
    {
        $q = $this->db->get('dsm_alignments');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }  
    public function getTeamByID($id)
    {
        $q = $this->db->get_where('team', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTaxRateByID($id)
    {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addWarehouse($data)
    {
        if ($this->db->insert('warehouses', $data)) {
            return true;
        }
        return false;
    }

    public function updateWarehouse($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('warehouses', $data)) {
            return true;
        }
        return false;
    }
    
    public function updateDSM($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('dsm_alignments', $data)) {
            return true;
        }
        return false;
    }

    public function getAllWarehouses()
    {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteTaxRate($id)
    {
        if ($this->db->delete('tax_rates', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    public function delete_conversion($id)
    {
        if ($this->db->delete('conversion', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteInvoiceType($id)
    {
        if ($this->db->delete('invoice_types', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteWarehouse($id)
    {
        if ($this->db->delete('warehouses', array('id' => $id)) && $this->db->delete('warehouses_products', array('warehouse_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteCluster($id)
    {
        if ($this->db->delete('cluster', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
    public function addCustomerGroup($data)
    {
        if ($this->db->insert('customer_groups', $data)) {
            return true;
        }
        return false;
    }
   

    public function updateCustomerGroup($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('customer_groups', $data)) {
            return true;
        }
        return false;
    }


    public function updateAlignment($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('alignment', $data)) {
            return true;
        }
        return false;
    }
    
    public function updateTeam($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('team', $data)) {
            return true;
        }
        return false;
    }
    
    public function updateConversion($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('conversion', $data)) {
            return true;
        }
        return false;
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
    
    
        public function getAllAlignmentGroups()
    {
        $q = $this->db->get('alignments');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCustomerGroupByID($id)
    {
        $q = $this->db->get_where('customer_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

 public function getConversionByID($id)
    {
        $q = $this->db->get_where('conversion', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
 public function getAlignmentByID($id)
    {
        $q = $this->db->get_where('alignment', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteCustomerGroup($id)
    {
        if ($this->db->delete('customer_groups', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
    
    
    public function deleteAlignment($id)
    {
        if ($this->db->delete('alignment', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
   public function getBU($active=FALSE)
    {
          if($active){
              $this->db->where('active', 'Y'); 
          }
        $this->db->where('id >', 0);
        $q = $this->db->get('business_unit');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    
    
    public function addBU($data)
    {
        if ($this->db->insert("business_unit", $data)) {
            return true;
        }
        return false;
    }
     public function addMSR($data)
    {
        if ($this->db->insert("msr_alignments", $data)) {
            return true;
        }
        return false;
    }
    public function getBUByID($id)
    {
        $q = $this->db->get_where('business_unit', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
      public function updateBU($id, $data = array())
    {
        if ($this->db->update('business_unit', $data, array('id' => $id)) ) {
            return true;
        }
        return false;
    }
    
     public function deleteBU($id)
    {
        if ($this->db->delete("business_unit", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getGroups()
    {
        $this->db->where('id >', 4);
        $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getGroupByID($id)
    {
        $q = $this->db->get_where('groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function GroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }

    public function updatePermissions($id, $data = array())
    {
        if ($this->db->update('permissions', $data, array('group_id' => $id)) && $this->db->update('users', array('show_price' => $data['products-price'], 'show_cost' => $data['products-cost']), array('group_id' => $id))) {
            return true;
        }
        return false;
    }

    public function addGroup($data)
    {
        if ($this->db->insert("groups", $data)) {
            $gid = $this->db->insert_id();
            $this->db->insert('permissions', array('group_id' => $gid));
            return $gid;
        }
        return false;
    }

    public function updateGroup($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update("groups", $data)) {
            return true;
        }
        return false;
    }


    public function getAllCurrencies()
    {
        $this->db->order_by("country","ASC");
            $q = $this->db->get('currencies');
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCurrencyByID($id)
    {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
      public function getTrailByID($id)
    {
        $q = $this->db->get_where('user_logins', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     
    
     public function getCountryByName($name)
    {
         $trimmedname=str_replace(" ","",$name);
         
         $this->db->select("id");
        $this->db->where("REPLACE(country,' ','')='".$trimmedname."'");
        $q = $this->db->get('currencies',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
      public function getCategoryName($name)
    {
         $trimmedname=str_replace(" ","",$name);
         
         $this->db->select("id,name");
        $this->db->where("REPLACE(name,' ','')='".$trimmedname."'");
        $q = $this->db->get('categories',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
	     public function getCountryByFullName($name)
    {
         $trimmedname=str_replace(" ","",$name);
         
         $this->db->select("id");
        $this->db->where("REPLACE(portuguese_name,' ','')='".$trimmedname."'");
        $q = $this->db->get('currencies',1);
      //  die(print_r($q));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
     public function getClusterByName($name)
    {
        $q = $this->db->get_where('cluster', array('name' =>trim($name)), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    
     public function addCluster($data)
    {
        if ($this->db->insert("cluster", $data)) {
            return true;
        }
        return false;
    }

    public function addCurrency($data)
    {
        if ($this->db->insert("currencies", $data)) {
            return true;
        }
        return false;
    }
     public function add_currencies($products = array())
    {
        if (!empty($products)) {
            foreach ($products as $product) {
              
              $this->db->insert('currencies', $product);
                
            }
            return true;
        }
        return false;
    }
       public function addDSMTEAMAlignmentsBatch($alignmens = array())
    {
        if (!empty($alignmens)) {
            foreach ($alignmens as $alignmen) {
              
              $this->db->insert('dsm_team_mapping', $alignmen);
                
            }
            return true;
        }
        return false;
    }

    public function updateCurrency($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update("currencies", $data)) {
            return true;
        }
        return false;
    }

    public function deleteCurrency($id)
    {
        if ($this->db->delete("currencies", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
public function deleteTeam($id)
    {
        if ($this->db->delete("team", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
    public function deleteDsm($id)
    {
        if ($this->db->delete("dsm_alignments", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    public function deleteMsr($id)
    {
        if ($this->db->delete("msr_alignments", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    
   function getPromotedProductCategory($catid,$promotion){
       //get promotion status of all products
       $productspromotionstatus= $this->getProductsInCategory($catid);
     //  print_r($products);
       //if any of the products is in promotion return true
      if(in_array($promotion, $productspromotionstatus)){
          return TRUE;
      }else{
         return FALSE;
      }
   }
  
   function getProductsInCategory($catid){
       $this->db->select("promoted");
        $this->db->where('category_id',$catid);
        $q = $this->db->get("products");   
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->promoted;
            }
            return $data;
        } 
        else{
        return FALSE;
        }
   }
    
    
    public function getAllCategories($data=NULL)
    {
       
        if(!empty($data["gbu"]) && ($data["gbu"]!="all")){
           $this->db->where('gbu', $data["gbu"]);
            $this->db->order_by('name');
          $q = $this->db->get("categories");  
        } else{
            $this->db->order_by('name');
        $q = $this->db->get("categories");
        }
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    
     public function getMinCategories($data,$limit=NULL,$offset=0)
    {
          if(!empty($data["gbu"]) && ($data["gbu"]!="all")){
           $this->db->where('gbu', $data["gbu"]);
          }
          
          if($limit && strtolower($limit) !="all"){
            //  die($limit."dsd");
              $this->db->limit($limit,$offset);
             
          }
        $this->db->select("id,name,gbu");
         $this->db->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllSubCategories()
    {
        $q = $this->db->get("subcategories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSubcategoryDetails($id)
    {
        $this->db->select("subcategories.code as code, subcategories.name as name, categories.name as parent")
            ->join('categories', 'categories.id = subcategories.category_id', 'left')
            ->group_by('subcategories.id');
        $q = $this->db->get_where("subcategories", array('subcategories.id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSubCategoriesByCategoryID($category_id)
    {
        $q = $this->db->get_where("subcategories", array('category_id' => $category_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id)
    {
        $q = $this->db->get_where("categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
      public function getClusterByID($id)
    {
        $q = $this->db->get_where("cluster", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSubCategoryByID($id)
    {
        $q = $this->db->get_where("subcategories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCategory($name, $code,$gbu, $photo)
    {
        if ($this->db->insert("categories", array('code' => $code, 'name' => $name, 'image' => $photo,'gbu'=>$gbu))) {
            return true;
        }
        return false;
    }
    
     public function add_Category($data)
    {
           foreach ($data as $item) {
        if ($this->db->insert("categories",$item)) {
          
        }
           }
         return true;
    }

    public function addSubCategory($category, $name, $code, $photo)
    {
        if ($this->db->insert("subcategories", array('category_id' => $category, 'code' => $code, 'name' => $name, 'image' => $photo))) {
            return true;
        }
        return false;
    }

    public function updateCategory($id, $data = array(), $photo)
    {
        $categoryData = array('code' => $data['code'], 'name' => $data['name'],'gbu'=>$data['gbu']);
        if ($photo) {
            $categoryData['image'] = $photo;
        }
        $this->db->where('id', $id);
        if ($this->db->update("categories", $categoryData)) {
            return true;
        }
        return false;
    }
public function updateCluster($id, $data = array())
    {
  
        
        $this->db->where('id', $id);
        if ($this->db->update("cluster", $data)) {
            return true;
        }
        return false;
    }
    public function updateSubCategory($id, $data = array(), $photo)
    {
        $categoryData = array(
            'category_id' => $data['category'],
            'code' => $data['code'],
            'name' => $data['name'],
        );
        if ($photo) {
            $categoryData['image'] = $photo;
        }
        $this->db->where('id', $id);
        if ($this->db->update("subcategories", $categoryData)) {
            return true;
        }
        return false;
    }

    public function deleteCategory($id)
    {
        if ($this->db->delete("categories", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteSubCategory($id)
    {
        if ($this->db->delete("subcategories", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getPaypalSettings()
    {
        $q = $this->db->get('paypal');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updatePaypal($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('paypal', $data)) {
            return true;
        }
        return FALSE;
    }

    public function getSkrillSettings()
    {
        $q = $this->db->get('skrill');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateSkrill($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('skrill', $data)) {
            return true;
        }
        return FALSE;
    }

    public function checkGroupUsers($id)
    {
        $q = $this->db->get_where("users", array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteGroup($id)
    {
        if ($this->db->delete('groups', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addVariant($data)
    {
        if ($this->db->insert('variants', $data)) {
            return true;
        }
        return false;
    }

    public function updateVariant($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('variants', $data)) {
            return true;
        }
        return false;
    }

    public function getAllVariants()
    {
        $q = $this->db->get('variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getVariantByID($id)
    {
        $q = $this->db->get_where('variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteVariant($id)
    {
        if ($this->db->delete('variants', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
     public function getAllStimaCategories() {
        $this->db2->order_by('name');
        $q = $this->db2->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getStimaCategoryByID($id)
    {
   
         $q2 = $this->db->get_where('categories', array('category_id' => $id), 1);
        if ($q2->num_rows() > 0) {
            return $q2->row();
        }
        return FALSE;
    }
    
    function logErrors($errors){
       $file="./assets/logs/uploaderror.txt";
       $fp = fopen($file, 'w+') or die("Unable to open file!");
      // fclose($fp);
       // $fp = fopen($file, 'a');

    // though CSV stands for "comma separated value"
    // in many countries (including France) separator is ";"
    fwrite($fp, $errors.PHP_EOL);
   
   

fclose($fp);
     //echo 
   }
    
}
