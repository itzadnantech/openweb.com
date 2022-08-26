<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Update_order extends CI_Controller {
	
	function index(){
		/* $r = fopen(APPPATH.'PDFfiles/text.txt', 'a');
		fwrite($r,'aa');
		fclose($r);
		die; */
		$canceled_result = $this->get_cancel_order();
		$update_result = $this->get_update_service();
		$once_off_result = $this->get_once_off();
		//$revoke_result = $this->get_revoke_order();

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
		
		if(!empty($once_off_result)){
			foreach ($once_off_result as $i => $id){
				$order_id = $id['id'];
				$order_data = $this->get_order_data($order_id);
				$this->inactive_order($order_data);
			}
		}
		
		/* if(!empty($revoke_result)){
			foreach ($revoke_result as $i =>$id){
				$order_id = $id['id'];
				$order_data = $this->get_order_data($order_id);
				$cancelled_data = $this->get_cancellations_revoke($order_data);
			}
		} */
	}
	
	function get_once_off(){
		$this->db->select('id');
		$this->db->where('billing_cycle', 'Once-Off');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	}
	
	function get_cancel_order(){
		$this->db->select('id');
		$this->db->where('date_cancelled !=', '');
		$this->db->where('billing_cycle !=','Daily');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	}
	
	function get_update_service(){
		$this->db->select('id');
		$this->db->where('date_update !=', '');
		$this->db->where('billing_cycle !=','Daily');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	}
	
	function get_order_data($order_id){
		$this->db->where('id', $order_id);
		$this->db->where('billing_cycle !=','Daily');
		$order = $this->db->get('orders');
		$order_data = $order->result_array();
		return $order_data;
	}
	
	//update the order's status pending cancellation to  cancelled in the 1th next month
	function get_updated_cancellations($result) {
		if (!empty($result)) {
			foreach ($result as $i=>$or) {
				if ($or['status'] == 'pending cancellation') {
					// now we check the date
					if (isset($or['date_cancelled']) && trim($or['date_cancelled'] != '')) {
						$cancelled = $or['date_cancelled'];
						// check if it was cancelled before this month.
						$this_month = date('M', strtotime('now'));
						$cancelled_month = date('M', strtotime($cancelled));
						//echo "$this_month vs $cancelled_month";
						if ($this_month != $cancelled_month) {
							$id = $or['id'];
							$this->db->where('id', $id);
							$this->db->update('orders', array('status' => 'cancelled'));
							$result[$i]['status'] = 'cancelled';
						}
					}
				}
			}
		}
		return $result;
	}
	
	//
	function update_service($result){
		if(!empty($result)){
			foreach ($result as $i=>$or){
				if($or['status'] == 'pending'){
					if(isset($or['date_update']) && trim($or['date_update'] !='')){
						$update_date = $or['date_update'];
						$this_month = date('M', strtotime('now'));
						$update_month = date('M', strtotime($update_date));
						if($this_month == $update_month){
							$id = $or['id'];
							$this->db->where('id', $id);
							$data = array(
								'status' => 'active', 
								'display_usage'=>1,
								'cancel_flage' => 1, 
								'change_flag' => 1,
								'date_update' =>'',
								'modify_service' => '',
							);
							$this->db->update('orders', $data);
							
							$acc_username = $or['account_username'];
							$acc_password = $or['account_password'];
							$username = $or['user'];
								
							//get the old order data
							$this->db->select('id');
							$this->db->where('id !=', $id);
							$this->db->where('user', $username);
							$this->db->where('account_username', $acc_username);
							$this->db->where('account_password', $acc_password);
							$query = $this->db->get('orders');
							$r = $query->result_array();
							
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
	
	function inactive_order($result)
	{		
		if(!empty($result)){
			foreach ($result as $i=>$or){
				if($or['billing_cycle'] == 'Once-Off'){
					$this_month = date('m', strtotime('now'));
					$crate_month = date('m', strtotime($or['date']));
					
					if($this_month > $crate_month){
						$this->db->where('id', $or['id']);
						$this->db->update('orders', array('status' => 'deleted'));
					}
				}
			}
		}
	}
	
	/* function get_revoke_order(){
		$this->db->select('id');
		$this->db->where('date_revoke !=', '');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	} */
	
	//revoke the cancelled order
	//update the order's status cancelled to active in the 1th next month
	/* function get_cancellations_revoke($result){
		if (!empty($result)) {
			foreach ($result as $i=>$or) {
				if ($or['status'] == 'pending cancellation') {
				// now we check the date
					if (isset($or['date_revoke']) && trim($or['date_revoke'] != '')) {
						$revoke = $or['date_revoke'];
						// check if it was cancelled before this month.
						$this_month = date('M', strtotime('now'));
						$revoke_month = date('M', strtotime($revoke));
						//revoke action must be do it before cancelled date
						if ($this_month == $revoke_month) {
							$id = $or['id'];
							$this->db->where('id', $id);
							$this->db->update('orders', array('status' => 'active'));
							$result[$i]['status'] = 'active';
						}
					}
				}
			}
		}
		return $result;
	} */
}
?>