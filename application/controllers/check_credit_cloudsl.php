<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Check_credit_cloudsl extends CI_Controller {

	  function index(){

		$orders=$this->get_orders();

		$this->load->model('user/product_model');

		$this->load->model('admin/order_model');

		//$id_user=get_none_credit();

		if($orders)

		{

			foreach ($orders as $order){

				/* $time_add=strtotime($order['date']);

				$date_add=date('M d',$time_add);

				$date_now=date('M d',strtotime('now'));

				$add_time=date('H:i',$time_add);

				$now_time=date('H:i',strtotime('now'));

				$poor=strtotime($now_time)-strtotime($add_time);

				echo $date_add.'\n';

				echo $date_now.'\n';

				echo $add_time.'\n';

				echo $now_time.'\n';

				echo $poor; */

				//$flag=date('i',$poor);

				$time_add=strtotime($order['date']);

				$time_now=strtotime(date('Y-m-d H:i:s',strtotime('now')));

				$err=$time_now-$time_add;

				if($err>(24*3600)&&$err<=(24*3600+10*60)){	//10 is the core job time

					$id_user=$order['id_user'];

					$cloudsl=$this->get_credit($id_user);

					

					$credit=$cloudsl[0]['credit']-$order['price'];

					//echo $credit;

					if($credit>=00){

						$this->update_credit($id_user, $credit);

						$time=$time_add+24*60*60;

						

						$date=date('Y-m-d H:i:s',$time);

						$this->update_date($order['id'], $date);

					}

					else{

						$this->send_info($id_user);

						$class = $this->product_model->get_is_class($order['product']);

						$realm_data = $this->product_model->get_is_details($class);

                        if (isset($order['id'])){
                             $realm_data = '';
                             $order_id = $order['id'];
                             $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);
                        }


						$rl_user = $realm_data['user'];

						$rl_pass = $realm_data['pass'];

						$sess = $this->start_connect($rl_user, $rl_pass);

						$realm_data = $this->order_model->get_is_details($class);

                        if (isset($order['id'])){
                            $realm_data = '';
                            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);
                        }


						$realm = $realm_data['realm'];

						$acc_realm_user = $order['account_username'] .'@'.$realm;

						$this->cancel_order($order['id']);	//

						$this->set_no_service($sess,$acc_realm_user);

						

					}

				}

				/* if($date_add<$date_now&&$poor>0&&$poor<600){		

					$id_user=$order['id_user'];

					$cloudsl=$this->get_credit($id_user);

					

					$credit=$cloudsl[0]['credit']-$order['price'];

					echo $credit;

					if($credit>0){

						$this->update_credit($id_user, $credit);

						$time=$time_add+24*60*60;

						$date=date('Y-m-d H:i:s',$time);

						$this->update_date($order['id'], $date);

					}

					else{

						send_info($id_user);

						cancel_order($order['id']);	//

					}

				} */

			}

		}

		

	}

	  function get_credit($id_user){

		//$this->db->where('credit','0');

		$this->db->select('credit');

		$this->db->where('id_user',$id_user);

		$query=$this->db->get('cloudsl');

		$data=$query->result_array();

		return $data;

	}

	

	  function get_orders(){

		$this->db->where('product !=',0);
		$this->db->where('status','active');
		$this->db->where('billing_cycle','Daily');
		//$this->db->where('id',344);
		$order = $this->db->get('orders');
		$order = $order->result_array();

		return $order;

	}

	

	  function update_credit($id_user,$credit){

	  	$this->db->where('id_user',$id_user);

	  	$data=array(

	  		'credit'=>$credit,

	  	);

	  	$this->db->update('cloudsl',$data);

	}

	function update_date($id_order,$date){

		$this->db->where('id',$id_order);

		$data=array('date'=>$date);	

		$this->db->update('orders',$data);

	}

	function send_info($id_user){

		//$this->load->model('user/product_model');

		$this->db->where('id',$id_user);

		$query=$this->db->get('membership');

		$result=$query->result_array();

		$ac_email=$result[0]['email_address'];

		$number=$result[0]['mobile_number'];

 		$content = "You have run out of credit, therefore your account has been set to No Service.";

		$this->load->library('email');

		$this->email->from('noreply@openweb.co.za', 'OpenWeb');

		$this->email->to($ac_email);

		$this->email->subject('Run out of credit');

		$this->email->message($content);

		$this->email->send();

		$this->load->model('admin/order_model');

		$sms_content = "You have run out of credit, therefore your account has been set to No Service.";

		//var_dump($sms_content);die;

		$this->order_model->send_sms($number, $sms_content);

	}

	function cancel_order($id_order){

		$this->db->where('id',$id_order);

		$data=array(

			'status'=>'pending',

			'product'=>0,

			'price'=>0,

			'account_comment'=>'',

			'pro_rata_extra'=>NULL,

		);

		$this->db->update('orders',$data);

	}

	function set_no_service($sess_id,$username){

		$client = $this->create_client();

		$data = array(

				'sess_id' => $sess_id,

				'strUserName' => $username,

		);

		$resp = $client->__call("suspendAccount", $data);

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

