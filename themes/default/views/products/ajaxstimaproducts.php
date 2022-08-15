<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$mysqli=new mysqli("localhost","root","trymenot#123","stima_pos");

$term=$_REQUEST['term'];
if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
  }
  
  $sql="select a.menu_id as id, a.menu_id as code, a.menu_name as name, a.menu_price as price, b.name as category 
      from ep0ytvat2_menus where a.menu_name like '%" . $term . "%' OR menu_id LIKE '%" . $term . "%' OR concat(a.menu_name,, ' (', a.menu_id, ')') LIKE '%" . $term . "%') 
          left join ep0ytvat2_categories b on b.category_id=a.menu_category_id group by a.menu_id limit 5";
 
$result=mysqli_query($mysqli,$sql);
if(mysqli_num_rows($result)>0){
    while ($rows = mysql_fetch_array($result)) {
      $data[] = $rows;  
    }
}
 if ($data) {
            foreach ($data as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
	
//$row=mysqli_fetch_assoc($select_menu);
//$this->db->select('' . $this->db->dbprefix('products') . '.id, code, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.price as price, ' . $this->db->dbprefix('product_variants') . '.name as vname')
           // ->where("type != 'combo' AND "
            //    . "(" . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
           //     concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')");
     //   $this->db->join('product_variants', 'product_variants.product_id=products.id', 'left')
      //      ->where('' . $this->db->dbprefix('product_variants') . '.name', NULL)
      //      ->group_by('products.id')->limit($limit);
      //  $q = $this->db->get('products');
//        if ($q->num_rows() > 0) {
//            foreach (($q->result()) as $row) {
//                $data[] = $row;
//            }
//            return $data;
//        }
  
  
  
  
// function suggestionsfromstima()
//    {
//        $term = $this->input->get('term', TRUE);
//        if (strlen($term) < 1 || !$term) {
//            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
//        }
//
//        $rows = $this->products_model->getProductNames($term);
//       
//        
//        
//        
//        if ($rows) {
//            foreach ($rows as $row) {
//                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1);
//            }
//            echo json_encode($pr);
//        } else {
//            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
//        }
//    }
    
//    function getProductNames($term, $limit = 5)
//    {
//        $this->db->select('' . $this->db->dbprefix('products') . '.id, code, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.price as price, ' . $this->db->dbprefix('product_variants') . '.name as vname')
//            ->where("type != 'combo' AND "
//                . "(" . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
//                concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')");
//        $this->db->join('product_variants', 'product_variants.product_id=products.id', 'left')
//            ->where('' . $this->db->dbprefix('product_variants') . '.name', NULL)
//            ->group_by('products.id')->limit($limit);
//        $q = $this->db->get('products');
//        if ($q->num_rows() > 0) {
//            foreach (($q->result()) as $row) {
//                $data[] = $row;
//            }
//            return $data;
//        }
//    }
?>
