<?php 
class Call_api extends CI_Controller {
	
	function __construct() 
	{
		parent::__construct();
		$this->load->model('admin/order_model');
		$this->load->model('user/product_model');
		$this->load->model('user/is_classes');
	}
	
  function index()
  {	
  	$this->load->view('call_api'); 	
  }
  
  function get_info(){
  	
  	$user = $_GET['user'];
  	$pwd = $_GET['pwd'];
  	$acc_user = $_GET['acc_user'];
  	$acc_pwd = $_GET['acc_pwd'];
  	$lm = explode('@', $acc_user);
  	$acc_username = $lm[0];
  	$query = "select * from orders where user = '$user' and account_username= '$acc_username' and account_password = '$acc_pwd'";
  	$results = $this->db->query($query);
  	$result = $results->result_array();
  	
  	if($result){
  		foreach ($result as $r){
  			$order_id = $r['id'];
  			
  			$order_data = $this->order_model->get_order_data($order_id);
  			$comment = $order_data['account_comment'];
  			$price = $order_data['price'];
  			$product_id = $order_data['product'];
  			$acc_username = $order_data['account_username'];
  			$product_data = $this->product_model->get_product_data($product_id);
  			$product_name = $product_data['name'];
  			 
  			$class = $this->order_model->get_is_class($order_id);
  			//$realm_data = $this->order_model->get_is_details($class);
            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);


            $rl_user = $realm_data['user'];
  			$rl_pass = $realm_data['pass'];
  			$lm = explode('@', $realm_data['user']);
  			$realm = $lm[1];
  			$account_username = $acc_username.'@'.$realm;
  			$data_info = array(
  					'pro_id' => $product_id,
  					'pro_name' => $product_name,
  					'or_id' => $order_id,
  					'rl_user' => $rl_user,
  					'rl_pass' => $rl_pass,
  					'realm' => $realm,
  					'class' => $class,
  					'username' => $order_data['account_username'],
  					'pass' => $order_data['account_password'],
  					'order_comment' =>$comment,
  			);	

  			$session_data = array(
  				'order_id' => $order_id,
				'username' => $acc_username,
				'rl_user' => $rl_user,
				'rl_pass' => $rl_pass,
				'class' => $class,
  				'acc_username' =>$acc_user, 
			);//print_r($session_data);die; 
			$this->session->set_userdata($session_data);
  		}
  		$data['info'] = $data_info;
  	}
  	$data[] = "";
  	//print_r($order_id);die; 	  
  	$this->load->view('call_api',$data);
  }
  
  function ger_session(){	
  
  	$rl_user = $this->session->userdata('rl_user');
  	$rl_pass = $this->session->userdata('rl_pass');  
  	$sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);
  	return $sess;
  }
  
  function connect(){ 	
  	$sess = $this->ger_session();
  	$sess_info = array(
  		'id' => $sess,
  	);
  	$data['sess'] = $sess_info;
  	$this->load->view('call_api',$data);
  }

  
  function set_account_pass(){
  	 	
  	$sess = $this->ger_session(); 	 
  	
 	$new_password = 'new_1122';
 	$account_username = $this->session->userdata('acc_username');
 	
  	$data_set = array (
  		'strSessionID' => $sess,
  		'strUserName' => $account_username,
  		'strValue' => $new_password,
  	);
  	$resp = $this->is_classes->is_setAccountPassword_new($data_set);
  	if($resp){
  		$resp = $resp;
  	}else{
  		$resp = "The result is null.";
  	}
  	$data['account_pass'] = $resp;
  	$this->load->view('call_api',$data);
  }
  
  function delete_account(){

  	$sess = $this->ger_session();
  	$account_username = $this->session->userdata('acc_username');
  	
  	$resp = $this->is_classes->delete_account_new($sess, $account_username);
  	if($resp){
  		$resp = $resp;
  	}else{
  		$resp = "The result is null.";
  	}
  	$data['delete_account'] = $resp;
  	$this->load->view('call_api',$data);
  }
  
  function set_account_class(){

  	$sess = $this->ger_session();
  	
  	$account_username = $this->session->userdata('acc_username');
  	$new_class = "ow-hc1";
  	
  	$resp = $this->is_classes->set_account_class_new($sess, $account_username, $new_class);//var_dump($resp);die;
  	if($resp){
  		$resp = $resp;
  	}else{
  		$resp = "The result is null.";
  	}
  	$data['account_class'] = $resp;
  	$this->load->view('call_api',$data);
  }
  
  function set_pending_update(){

  	$sess = $this->ger_session();
  	
  	$account_username = $this->session->userdata('acc_username');
  	$new_class = "ow-hc1";
  	
  	$resp = $this->is_classes->set_pending_update_new($sess, $account_username, $new_class);
  	if($resp){
  		$resp = $resp;
  	}else{
  		$resp = "The result is null.";
  	}
  	$data['pending_update'] = $resp;
  	$this->load->view('call_api',$data);
  }
}
?>
