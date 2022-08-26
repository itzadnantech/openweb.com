<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class delete_account extends CI_Controller 
{
	function index()
	{
		//get realm and connect to the api
		$once_off_result = $this->get_once_off();


		//echo '<pre>';print_r($once_off_result);die;
		if($once_off_result){
			foreach ($once_off_result as $k=>$v){
                $order_id = $v['id'];
                $acc_username = $v['account_username'];
                $product_id = $v['product'];
                $this->load->model('user/product_model');
                $this->load->model('admin/order_model');
                $class = $this->product_model->get_is_class($product_id);
                //$realm_data = $this->product_model->get_is_details($class);
                $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

                $rl_user = $realm_data['user'];
                //if openweb realm
                if(substr_count($realm_data['user'], "@") > 1) {
                    $rl_user = substr($realm_data['user'], 0, 28);
                }

                $rl_pass = $realm_data['pass'];
                $sess = $this->start_connect($rl_user, $rl_pass);


                //$realm_data = $this->order_model->get_is_details($class);
                $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

                $realm = $realm_data['realm'];
                $acc_realm_user = $acc_username.'@'.$realm;

                $this->delete_account_api($sess, $acc_realm_user);
                $this->inactive_order($order_id);
                $this->save_id($order_id);
			}
		}
	}

	function save_id($id) {
	    $this->db->insert('orders_after_cron', array('id' => $id));
    }
	
	function get_once_off()
	{
		//$this->db->select('id');
		$this->db->where('billing_cycle', 'Once-Off');
		$this->db->where('status', 'active');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		if(!empty($result)){
			foreach ($result as $i=>$or){
				if($or['billing_cycle'] == 'Once-Off'){
					$this_month = date('m', strtotime('now'));
					$crate_month = date('m', strtotime($or['date']));
		
					if($this_month > $crate_month){
						$array[] = $or;
					}
				}
			}
		}
		return $array;
	}
	
	function inactive_order($order_id)
	{
		$this->db->where('id', $order_id);
		$this->db->update('orders', array('status' => 'deleted'));
	}
	
	function delete_account_api($sess_id, $username)
	{	
		$client = $this->create_client();
		//$sess_id = $this->start_connect();
		$data = array(
			'sess_id' => $sess_id,
			'strUserName' => $username,
		);
		$resp = $client->__call('deleteAccount', $data);
		// Should return class and return code
		return $resp['intReturnCode'] ;
	}
	
	function start_connect($rl_user, $rl_pass) 
	{
		$client = $this->create_client();

		$is_param = array(
			'strUserName'    => $rl_user, //'administrator@mynetwork.co.za',
			'strPassword'    =>$rl_pass, //'485c862ab6defdd2267d37bd497787d0',
			'blnContinue' => true,
		);
		$resp = $client->__call("startSession", $is_param);

		if ($resp['intReturnCode'] == 1) {
			$session_id = $resp['strSessionID'];
			return $session_id;
		} else {
			return $resp['intReturnCode'];
		}
	
	}
	
	function create_client() 
	{
		$options = array('socket' => array('bindto' => API_BINDTO));
		$context = stream_context_create($options);
		$url = API_URL;
		$client = new SoapClient($url,array('trace' => 1, 'exception' => 0, 'stream_context' => $context));

		return $client;
	}
}