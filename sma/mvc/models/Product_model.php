<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default1', TRUE);
    }




  public function getItemByID($id)
    {
        // $this->load->database('default1', TRUE);
        // $this->db->get_where('quote_items', array('id' => $id), 1);
         $q2 = $this->db->get_where('menus', array('menu_id' => $id), 1);
        if ($q2->num_rows() > 0) {
            return $q2->row();
        }
        return FALSE;
    }

}
?>
