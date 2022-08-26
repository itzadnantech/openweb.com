<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Update_order_cloudsl extends CI_Controller {

	function index(){
		$canceled_result = $this->get_cancel_order();
		$this->load->model('user/product_model');
		$this->load->model('admin/order_model');
		$update_result = $this->get_update_service();
		if(!empty($canceled_result)){
			foreach ($canceled_result as $i =>$id){
				$order_id = $id['id'];
				$order_data = $this->get_order_data($order_id);
				$cancelled_data = $this->get_updated_cancellations($order_data);
				
			}
		}
		
		if(!empty($update_result)){
			foreach ($update_result as $i =>$id){
				$order_id = $id['id'];
				$order_data = $this->get_order_data($order_id);
				$cancelled_data = $this->update_service($order_data);
			}
		}
		
	}
	
 	function get_cancel_order(){
		$this->db->select('id');
		$this->db->where('date_cancelled !=', '');
		$this->db->where('status','pending cancellation');
		$this->db->where('billing_cycle','Daily');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	} 
	
	function get_update_service(){
		$this->db->select('id');
		$this->db->where('date_update !=', '');
		$this->db->where('status','pending');
		$this->db->where('billing_cycle','Daily');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	}
	
	function get_order_data($order_id){
		$this->db->where('id', $order_id);
		$this->db->where('billing_cycle','Daily');
		$order = $this->db->get('orders');
		$order_data = $order->result_array();
		return $order_data;
	}
	
	function get_updated_cancellations($result) {
		if (!empty($result)) {
			foreach ($result as $i=>$or) {
					// now we check the date
					if (isset($or['date_cancelled']) && trim($or['date_cancelled'] != '')) {
						$time_add=strtotime($or['date']);
						$time_now=strtotime(date('Y-m-d H:i:s',strtotime('now')));
						
						$err=$time_now-$time_add;
					
						if($err>(24*3600)&&$err<=(24*3600+10*60)){	//10 is the core job time
							$id=$or['id'];
							$this->db->where('id', $id);
							$data=array(
								'status'=>'pending',
								'product'=>0,
								'price'=>0,
								'account_comment'=>'',
								'date_cancelled'=>NULL,
								'date_update'=>NULL,
								
							);
							$this->db->update('orders', $data);
							$result[$i]['status'] = 'pending';
						}
					}
			}
		}
		return $result;
	}
	
	function update_service($result){
		if(!empty($result)){
			foreach ($result as $i=>$or){
				if($or['status'] == 'pending'){
					if(isset($or['date_update']) && trim($or['date_update'] !='')){
						$time_add = strtotime($or['date']);
						$time_now = strtotime(date('Y-m-d H:i:s',strtotime('now')));
						$err=$time_now-$time_add;
						$update_date = $or['date_update'];
						$add_date = date('Y-m-d',$time_add);
						/*$order_time = date('H:i',$or['date']);
						$this_day = date('d', strtotime('now'));
						$this_time = date('H:i',strtotime('now'));
						$update_day = date('d', strtotime($update_date)); */
						
						//if($this_day == $update_day&&$order_time<$this_time){
						if($update_date!=$add_date&&$err>(24*3600)&&$err<=(24*3600+10*60)){
							$id = $or['id'];
							$this->db->where('id', $id);
							$data = array(
									'status' => 'active',
									'display_usage'=>1,
									'cancel_flage' => 1,
									'change_flag' => 1,
									'date_update' =>NULL,
									'modify_service' => '',
							);
							$this->db->update('orders', $data);
								
							$acc_username = $or['account_username'];
							$acc_password = $or['account_password'];
							$username = $or['user'];
							$class = $this->product_model->get_is_class($or['product']);
							//$realm_data = $this->product_model->get_is_details($class);
                            if (!isset($this->order_model))
                                $this->load->model('admin/order_model');
                            $realm_data = $this->order_model->get_realm_data_by_order_id($id, $class);

							$rl_user = $realm_data['user'];
							$rl_pass = $realm_data['pass'];
							$sess = $this->start_connect($rl_user, $rl_pass);
							$realm = $realm_data['realm'];
							$acc_realm_user = $acc_username.'@'.$realm;
							$this->set_account_class($sess, $acc_realm_user, $class);
							//get the old order data
							$this->db->select('id');
							$this->db->where('id !=', $id);
							$this->db->where('status !=','deleted');
							$this->db->where('user', $username);
							$this->db->where('account_username', $acc_username);
							$this->db->where('account_password', $acc_password);
							$query = $this->db->get('orders');
							$r = $query->result_array();
								var_dump($r);
							if($query->num_rows == 1){
								$result = $query->row('array');
								$old_id = $result->id;
								//update the old order's status
								$this->db->where('id', $old_id);
								$this->db->update('orders',array('status' => 'deleted'));
							}
						}
					}
				}
			}
		}
		return $result;
	}
	function set_account_class($sess, $username, $class){
		$client = $this->create_client();
		$is_param = array(
			'strSessionID'  =>	$sess,
			'strUserName' 	=>	$username,
			'intClassID'    =>	$class,		
		);
		
		$resp = $client->__call("setAccountClass", $is_param); 
		return $resp['intReturnCode'];
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