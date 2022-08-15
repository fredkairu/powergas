<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Cmt_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function getAllComments()
    {

        $q = $this->db->get("notifications");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getNotifications()
    {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where("from_date <=", $date);
        $this->db->where("till_date >=", $date);
        if (!$this->Owner) {
            if ($this->Supplier) {
                $this->db->where('scope', 4);
            } elseif ($this->Customer) {
                $this->db->where('scope', 1)->or_where('scope', 3);
            } elseif (!$this->Customer && !$this->Supplier) {
                $this->db->where('scope', 2)->or_where('scope', 3);
            }
        }
        $q = $this->db->get("notifications");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCommentByID($id)
    {

        $q = $this->db->get_where("notifications", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function addNotification($data)
    {

        if ($this->db->insert("notifications", $data)) {
            return true;
        } else {
            return false;
        }
    }

     public function addSmsNotification($data)
    {
   $api_key = 'UG93ZXJHYXM6cG93ZXJnYXMwMDE=';

// Step 2: Change the from number below. It can be a valid phone number or a String
$from = 'POWER_GAS';

// Step 3: the number we are sending to - Any phone number
// Using comma (,) at end of the every phone number. You must have to insert country code at beginning of the number
//You can insert maximum 100 number at a time
$destination = '254722166011';

// Step 4: Replace your Install URL like https://mywebhost.com/sms/api with https://portal.paylifesms.com/sms/api is mandatory.

$url = 'https://portal.paylifesms.com/sms/api';

// the sms body
$sms = 'test message from Ultimate SMS';

// Create SMS Body for request
$sms_body = array(
    'action' => 'send-sms',
    'api_key' => $api_key,
    'to' => $destination,
    'from' => $from,
    'sms' => $sms
);

$send_data = http_build_query($sms_body);

$gateway_url = $url . "?" . $send_data;
echo ".$gateway_url.";

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $gateway_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    $output = curl_exec($ch);
    if (curl_errno($ch)) {
        $output = curl_error($ch);
    }
    curl_close($ch);

    var_dump($output);

}catch (Exception $exception){
    echo $exception->getMessage();
}

     
        /**if ($this->db->insert("notifications", $data)) {
            return true;
        } else {
            return false;
        }**/
    }
    public function updateNotification($id, $data)
    {

        $this->db->where('id', $id);
        if ($this->db->update("notifications", $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteComment($id)
    {
        if ($this->db->delete("notifications", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }


}

/* End of file pts_model.php */
/* Location: ./application/models/pts_types_model.php */
