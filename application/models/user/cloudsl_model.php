<?php
class Cloudsl_model extends CI_Model {
	
	function get_user_cloudsl($id,$ow){
		//$result = $this->user_model->get_user_id($username);
		$this->db->where('id_user',$id);
		$this->db->where('ow',$ow);
		$query = $this->db->get('cloudsl');
		$data = $query->result_array();
		return $data;
	}
	function get_account_cloudsl($user){
		$this->db->where('id_user',$user['id']);
		$this->db->where('billing_cycle','Daily');
		$this->db->where('status !=','deleted');
		$this->db->where('user',$user['username']);
		$this->db->where('change_flag',1);
		$query = $this->db->get('orders');
		$data = $query->result_array();
		return $data;
	}
	function get_another_account_cloudsl($user){
		$this->db->where('id_user',$user['id']);
		$this->db->where('product',0);
		$this->db->where('billing_cycle','Daily');
		$this->db->where('user',$user['username']);
		$this->db->where('change_flag',1);
		$query = $this->db->get('orders');
		$data = $query->result_array();
		return $data;
	}
	function active_user_cloudsl($id,$ow){
		$data=array(
			'id_user' => $id,
			'ow' => $ow,
		);
		$this->db->insert('cloudsl',$data);
		//return $result[0];
	}
	function add_credit($id,$credit){
		$credit_be=$this->get_credit_cloudsl($id);
		$credit=$credit+$credit_be;
		$data=array(
			'credit' => $credit,
		);
		$this->db->where('id_user',$id);
		$this->db->update('cloudsl',$data);
	}
	function add_account($user,$info){
		//$this->load->model()
		$data=array(
			'user' => $user['username'],
			//'date' => now(),
			'account_username' => $info['account_username'],
			'account_password' => $info['account_password'],
			'id_user' => $user['id'],
			'display_usage' => 1,
			'billing_cycle' => 'Daily',
			
		);
		$order=$this->get_orders_cloudsl($user['username']);
		if(count($order)==1&&$order[0]['account_username']==''){
			$this->db->where('user',$user['username']);
			$this->db->update('orders',$data);
			//$number=$this->membership_model->get_number($user['username']);
			//$sess=$this->is_classes->is_connect($rl_user, $rl_pass);
		}
		else{
			$this->db->insert('orders',$data);
		}
		$cloudsl = $this->get_user_cloudsl($user['id'], $user['ow']);
		$account_num = $cloudsl[0]['account_num']+1;
		$data = array(
			'account_num' => $account_num,
		);
		$this->db->where('id',$cloudsl[0]['id']);
		$this->db->update('cloudsl',$data);
	}
	function add_order_cloudsl($user,$info){
		$data=array(
			'user'=>$user['username'],
			'id_user'=>$user['id'],
			'display_usage'=>1,
			'cancel_flage'=>1,
			'product'=>$info['product'],
			'account_comment'=>$info['name'],
			'price'=>$info['price'],
			'billing_cycle' => 'Daily',
		);
		$this->db->insert('orders',$data);
		
	}
	function get_service_cloudsl($num=10,$start=0){
		//$this->db->select('id,name,price,trial');
		$this->db->where('type','daily');
		$this->db->limit($num,$start);
		$query=$this->db->get('products');
		$data=$query->result_array();
		return $data;
	}
	function get_service_cloudsl_num(){
		$this->db->where('type','daily');
		$query=$this->db->get('products');
		$num=$query->num_rows();
		return $num;
	}
	function get_another_service_cloudsl($id){
		//$this->db->select('id,name,price,trial');
		$this->db->where('id !=',$id);
		$this->db->where('type','daily');
		$query=$this->db->get('products');
		$data=$query->result_array();
		return $data;
	}
	function get_orders_cloudsl($username='',$id=''){	
		//$this->db->select('id,product,date,');
		if($username!='')
		{
			$this->db->where('user',$username);
			$this->db->where('product !=','0');
		}
		if($id!='')
			$this->db->where('id',$id);
		//$this->db->where('product !=','0');
		$this->db->where('change_flag',1);
		$this->db->where('billing_cycle','Daily');
		$query=$this->db->get('orders');
		$data=$query->result_array();
		return $data;
	}
	function get_credit_cloudsl($id_user){
		$this->db->select('credit');
		$this->db->where('id_user',$id_user);
		$query=$this->db->get('cloudsl');
		$credit=$query->first_row();
		if($credit)
			return $credit->credit;
		else 
			return 0;
	}
	function updata_account_cloudsl($id_product,$id){
		//$time=date('Y-m-d H:i:s',time());
		//date_default_timezone_set('Asia/Shanghai');
		$order=$this->get_orders_cloudsl('',$id);
		$price=$this->product_model->get_product_price($id_product);
		$name=$this->product_model->get_product_name($id_product);
		//var_dump($order);die;
		if($order[0]['product']==0)
		{
			//$time=date('Y-m-d H:i:s',time());
			
			$data=array('product'=>$id_product,
					//'date'=>$time,
					//'date'=>date("Y-m-d H:i:s",strtotime('now')),
					'price'=>$price,
					'account_comment'=>$name,
					//'status'=>'active',
					'cancel_flage'=>1,
					
					);
			if($order[0]['date_cancelled']!=NUll){
				$date=strtotime($order[0]['date']);
				$time_add=date('H:i',$date);
				$time_or=date('H:i',time());
				if($time_add>$time_or)
					$time=date('Y-m-d',time());
				else
					$time=date('Y-m-d',time()+86400);
				$data=array('product'=>$id_product,
						//'date'=>$time,
						//'date'=>date("Y-m-d H:i:s",strtotime('now')),
						//'price'=>$price,
						//'account_comment'=>$name,
						'status'=>'pending',
						//'cancel_flage'=>1,
						'date_update'=>date("Y-m-d H:i:s",strtotime('now')),
						'date_cancelled'=>NULL,
				);
			}
			else {
				$data=array('product'=>$id_product,
						//'date'=>$time,
						'date'=>date("Y-m-d H:i:s",strtotime('now')),
						//'price'=>$price,
						//'account_comment'=>$name,
						'status'=>'active',
						//'cancel_flage'=>1,
							
				);
			}
			$data['account_comment']=$name;
			$data['price']=$price;
			$data['display_usage']=1;
			$data['cancel_flage']=1;
		}
		else
		{
			$date=strtotime($order[0]['date']);
			$time_add=date('H:i',$date);
			$time_or=date('H:i',time());
			if($time_add>$time_or)
				$time=date('Y-m-d',time());
			else 
				$time=date('Y-m-d',time()+86400);
			$data=array('product'=>$id_product,'date_update'=>$time);
		}
		$this->db->where('id',$id);
		$this->db->update('orders',$data);
		return $data['status'];
	}
	function update_credit_cloudsl($id_product,$id_user){
		$price=$this->product_model->get_product_price($id_product);
		
		$credit=$this->get_credit_cloudsl($id_user);
		$credit=$credit-$price;
		$data=array('credit'=>$credit);
		$this->db->where('id_user',$id_user);
		$this->db->update('cloudsl',$data);
	}
	function check_user($user){
		$this->db->where('account_username',$user);
		$query=$this->db->get('orders');
		return $query->num_rows();
	}
	function get_realm(){
		//$this->db->where('')
		$query=$this->db->get('realms');
		$data=$query->result_array();
		return $data;
	}
}