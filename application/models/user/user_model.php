<?php
class User_model extends CI_Model
{

	function telkom_order_exists($order_id)
	{
		//telkom check order exist in db
		$this->db->where('id', $order_id);
		$query = $this->db->get('orders');
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	function mtn_order_exists($order_id)
	{
		//telkom check order exist in db
		$this->db->where('id', $order_id);
		$query = $this->db->get('orders');
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	function mobile_order_exists($order_id)
	{
		//telkom check order exist in db
		$this->db->where('id', $order_id);
		$query = $this->db->get('orders');
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	function get_notifications($username)
	{
		$this->db->where('user', $username);
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get('notifications');
		$result = $query->result_array();
		return $result;
	}

	function get_user_data($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user_settings = $query->first_row('array');

		$this->db->where('username', $username);
		$query = $this->db->get('billing');
		$user_billing = $query->first_row('array');

		if (empty($user_billing)) {
			$user_billing = '';
		}
		$data = array(
			'user_settings' => $user_settings,
			'user_billing' => $user_billing,
		);
		return $data;
	}

	function addAviosIds($avios_settings, $user_id)
	{

		$insert_data = $this->validateAviosData($avios_settings);

		//Add empty field with 'null'
		foreach ($insert_data as $name => $val) {
			if ($name == "avios_id") {
				$insert_data["br_a_id"] = null;
			} else if ($name == "br_a_id") {
				$insert_data["avios_id"] = null;
			}
		}
		if ($insert_data === "d") {
			return false;
		}
		//If all field empty
		if ($insert_data == false) {
			$insert_data = array(
				"avios_id" => null,
				"br_a_id" => null
			);
		}

		$this->db->where('id', $user_id);
		$result = $this->db->update('membership', $insert_data);

		return $result;
	}

	function validateAviosData($avios_settings)
	{
		foreach ($avios_settings as $name => $val) {
			if (!empty($val) && preg_match("/[0-9]/i", $val)) {

				/* removed unique ID
                 $this->db->where($name, $val);
                 $query = $this->db->get('membership');
                 $row = $query->result();

                 if (empty($row)) {
                     return array(
                         $name => $val
                     );
                 } else {
                     return "d";
                 }*/

				return [$name => $val];
			}
		}
		return false;
	}

	function get_user_data_by_id($user_id)
	{
		$this->db->where('id', $user_id);
		$query = $this->db->get('membership');
		$user_settings = $query->first_row('array');

		$this->db->where('id_user', $user_id);
		$query = $this->db->get('billing');
		$user_billing = $query->first_row('array');

		if (empty($user_billing)) {
			$user_billing = '';
		}
		$data = array(
			'user_settings' => $user_settings,
			'user_billing' => $user_billing,
		);
		return $data;
	}



	function get_user_data_by_username_and_id($username, $user_id)
	{

		$this->db->where('id', $user_id);
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user_settings = $query->first_row('array');

		$this->db->where('id_user', $user_id);
		$this->db->where('username', $username);
		$query = $this->db->get('billing');
		$user_billing = $query->first_row('array');

		if (empty($user_billing)) {
			$user_billing = '';
		}
		$data = array(
			'user_settings' => $user_settings,
			'user_billing' => $user_billing,
		);
		return $data;
	}

	function update_cancellations($username)
	{
		$result = $this->get_orders($username);
		$this->get_updated_cancellations($result);
	}

	function get_updated_cancellations($result)
	{
		if (!empty($result)) {
			foreach ($result as $i => $or) {
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

	//mark-1000
	function get_active_orders($username, $num = 10, $start = 0, $services = array('adsl'))
	{
		//$where = "user = '$username' AND status != 'pending' AND status != 'cancelled'";
		//$where = "user = '$username' AND status in('active')";//, 'pending'
		//$this->db->where($where);

		if (empty($services))
			$services = array('adsl');

		$this->db->where('user', $username);
		$this->db->where('status', 'active');
		//$this->db->where('billing_cycle !=','Daily');
		$this->db->where_in('service_type', $services);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);

		$data = array();
		if ($result)
			foreach ($result as $r) {

				if (($r['service_type'] == 'fibre-data') || ($r['service_type'] == 'fibre-line') || ($r['service_type'] == 'lte-a') || ($r['service_type'] == 'mobile')) {
					$fibre = $this->get_fibre_data_by_order($r['id']);
					if (!empty($fibre))
						$r['fibre'] = $fibre;
				}
				$data[] = $r;
			}

		return $data;
	}

	function get_inactive_orders($username, $num = 10, $start = 0, $services = array('adsl'))
	{

		if (empty($services))
			$services = array('adsl');

		$where = "user = '$username' AND status != 'active' AND status != 'pending cancellation' AND status != 'pending' AND status != 'revoke cancellation'";
		$this->db->where($where);
		//$this->db->where('billing_cycle !=','Daily');
		$this->db->where_in('service_type', $services);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);

		$data = array();
		if ($result)
			foreach ($result as $r) {

				if (($r['service_type'] == 'fibre-data') || ($r['service_type'] == 'fibre-line')) {
					$fibre = $this->get_fibre_data_by_order($r['id']);
					if (!empty($fibre))
						$r['fibre'] = $fibre;
				}
				$data[] = $r;
			}

		return $data;
	}

	function order_key()
	{
		$order_key = array(
			'account_password' => 'New Account Password',
		);
		return $order_key;
	}

	function get_orders($username, $num = 10, $start = 0, $services = array('adsl'))
	{

		if (empty($services))
			$services = array('adsl');

		$this->db->where('user', $username);
		//$service_array = array('adsl', 'fibre-data', 'fibre-line');
		$this->db->where_in('service_type', $services);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);

		if ($result) {
			foreach ($result as $r) {
				$modify_service = $r['modify_service'];
				if (($r['service_type'] == 'fibre-data') || ($r['service_type'] == 'fibre-line') || $r['service_type'] == 'lte-a') {
					$fibre = $this->get_fibre_data_by_order($r['id']);
					if (!empty($fibre))
						$r['fibre'] = $fibre;
				}

				if (empty($modify_service)) {
					$data[] = $r;
				}
			}
		} else {
			$data = array();
		}


		return $data;
	}


	function get_fibre_data_by_order($order_id)
	{

		$this->db->where('order_id', $order_id);
		$query = $this->db->get('fibre_orders');
		// $result = $query->result_array();
		$result = $query->first_row('array');

		return $result;
	}


	function get_active_orders_count($username, $services = array('adsl'))
	{
		$this->db->select('id');
		//$where = "user = '$username' AND status != 'pending' AND status != 'cancelled'";
		$where = "user = '$username' AND status in('active')"; //, 'pending'
		$this->db->where($where);
		//$this->db->where('billing_cycle !=','Daily');
		$this->db->where_in('service_type', $services);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_user_name($username)
	{
		$this->db->select('first_name, last_name');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		$name = $user['first_name'] . ' ' . $user['last_name'];
		return $name;
	}


	function get_inactive_orders_count($username, $services = array('adsl'))
	{
		$this->db->select('id');
		$where = "user = '$username' AND status != 'active' AND status != 'pending cancellation' AND status != 'pending' AND status != 'revoke cancellation'";
		$this->db->where($where);
		$this->db->where_in('service_type', $services);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_orders_count($username, $services = array('adsl'))
	{
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->where_in('service_type', $services);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function search_active_order($username, $acc_user, $num = 10, $start = 0)
	{
		$this->db->where('status', 'active');
		$this->db->where('user', $username);
		$this->db->where('billing_cycle !=', 'Daily');
		$this->db->like('account_username', $acc_user);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);
		return $result;
	}

	function search_active_count($username, $acc_user)
	{
		$this->db->where('status', 'active');
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->like('account_username', $acc_user);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function search_inactive_order($username, $acc_user, $num = 10, $start = 0)
	{
		$where = "user = '$username' AND status != 'active' AND status != 'pending cancellation' AND status != 'pending' AND status != 'revoke cancellation'";
		$this->db->where($where);
		$this->db->where('billing_cycle !=', 'Daily');
		$this->db->like('account_username', $acc_user);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);
		return $result;
	}

	function search_inactive_count($username, $acc_user)
	{
		$this->db->select('id');
		$where = "user = '$username' AND status != 'active' AND status != 'pending cancellation' AND status != 'pending' AND status != 'revoke cancellation'";
		$this->db->where($where);
		$this->db->where('billing_cycle !=', 'Daily');
		$this->db->like('account_username', $acc_user);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function search_orders($username, $acc_user, $num = 10, $start = 0)
	{
		$this->db->where('user', $username);
		$this->db->where('billing_cycle !=', 'Daily');
		$this->db->like('account_username', $acc_user);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);
		return $result;
	}

	function search_orders_count($username, $acc_user)
	{
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->like('account_username', $acc_user);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function email_register($email, $username)
	{
		$this->load->library('email');

		$this->db->where('username', $username);
		$this->db->from('membership');
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			$username = $row->username;
			$first_name = $row->first_name;
			$last_name = $row->last_name;
			$password = $row->password;
			$email = $row->email_address;
			$joined = $row->joined;
			$status = $row->status;
		}

		$this->db->select('id,title, content,email_address');
		$this->db->where('purpose', 'registration');
		$query = $this->db->get('email_template');
		$result = $query->result_array();

		if (!empty($result)) {
			$result = $result[0];
			$email_template_id = $result['id'];
			$email_address = $result['email_address'];
			$title = $result['title'];
			$content = $result['content'];

			$content = str_ireplace('[User_Name]', $username, $content);
			$content = str_ireplace('[First_Name]', $first_name, $content);
			$content = str_ireplace('[Last_Name]', $last_name, $content);
			$content = str_ireplace('[Password]', $password, $content);
			$content = str_ireplace('[Email_Address]', $email, $content);
			$content = str_ireplace('[Register_Date]', $joined, $content);
			$content = str_ireplace('[Current_Status]', $status, $content);

			$email_attachment_data = $this->db->where('email_template_id', $email_template_id);
			$attac_query = $this->db->get('email_attachment');
			$attac_result = $attac_query->result_array();

			$this->email->from($email_address, 'OpenWeb');
			$this->email->to($email);
			$this->email->subject($title);
			$this->email->message($content);
			if (!empty($attac_result)) {
				foreach ($attac_result as $att) {
					$path = $att['path'];
					$this->email->attach($path);
				}
			}
			$this->email->send();
		}
	}

	function email_active_account($account_id)
	{
		$this->load->library('email');
		$this->load->model('crypto_model');
		//$this->db->select('email_address,username');
		$this->db->where('id', $account_id);
		$this->db->from('membership');
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			$username = $row->username;
			$first_name = $row->first_name;
			$last_name = $row->last_name;
			$password = $this->crypto_model->decode($row->password);;
			$email = $row->email_address;
			$joined = $row->joined;
			$status = $row->status;
		}

		$this->db->select('id,title, content, email_address');
		$this->db->where('purpose', 'activation');
		$query = $this->db->get('email_template');
		$result = $query->result_array();

		if ($result) {
			$result = $result[0];
			$email_template_id = $result['id'];
			$email_address = $result['email_address'];
			$title = $result['title'];
			$content = $result['content'];

			$content = str_ireplace('[User_Name]', $username, $content);
			$content = str_ireplace('[First_Name]', $first_name, $content);
			$content = str_ireplace('[Last_Name]', $last_name, $content);
			$content = str_ireplace('[Password]', $password, $content);
			$content = str_ireplace('[Email_Address]', $email, $content);
			$content = str_ireplace('[Register_Date]', $joined, $content);
			$content = str_ireplace('[Current_Status]', $status, $content);

			$this->db->where('email_template_id', $email_template_id);
			$attac_query = $this->db->get('email_attachment');
			$attac_result = $attac_query->result_array();

			$this->email->from($email_address, 'OpenWeb');
			$this->email->to($email);
			$this->email->subject($title);
			$this->email->message($content);

			//$this->email->attach('application/upload/activation/1383560944.png');
			if ($attac_result) {
				foreach ($attac_result as $att) {
					$path = $att['path'];
					$this->email->attach($path);
				}
			}
			$this->email->send();
		}
	}

	function active_account($account_id)
	{
		$this->db->where('id', $account_id);
		$result  = $this->db->update('membership', array('status' => 'active'));
		return $result;
	}

	function email_forgot_password($email)
	{
		$this->load->library('email');
		$this->load->model('crypto_model');
		//send email use the template
		$this->db->where('email_address', $email);
		$this->db->from('membership');
		$query = $this->db->get();
		$result = $query->result();
		if ($result) {
			foreach ($query->result() as $row) {
				$username = $row->username;
				$first_name = $row->first_name;
				$last_name = $row->last_name;
				$password = $row->password;
				$password = $this->crypto_model->decode($password);
				$email = $row->email_address;
				$joined = $row->joined;
				$status = $row->status;
			}

			$this->db->select('id,title, content,email_address');
			$this->db->where('purpose', 'forgot_password');
			$query = $this->db->get('email_template');
			$result = $query->result_array();

			if (!empty($result)) {
				$result = $result[0];
				$email_template_id = $result['id'];
				$email_address = $result['email_address'];
				$title = $result['title'];
				$content = $result['content'];

				$content = str_ireplace('[User_Name]', $username, $content);
				$content = str_ireplace('[First_Name]', $first_name, $content);
				$content = str_ireplace('[Last_Name]', $last_name, $content);
				$content = str_ireplace('[Password]', $password, $content);
				$content = str_ireplace('[Email_Address]', $email, $content);
				$content = str_ireplace('[Register_Date]', $joined, $content);
				$content = str_ireplace('[Current_Status]', $status, $content);

				$email_attachment_data = $this->db->where('email_template_id', $email_template_id);
				$attac_query = $this->db->get('email_attachment');
				$attac_result = $attac_query->result_array();

				$this->email->from($email_address, 'OpenWeb');
				$this->email->to($email);
				$this->email->subject($title);
				$this->email->message($content);

				if (!empty($attac_result)) {
					foreach ($attac_result as $att) {
						$path = $att['path'];
						$this->email->attach($path);
					}
				}
				$this->email->send();
				return true;
			}
		} else {
			return false;
		}
	}

	function email_to_admin($account_info, $billing_info)
	{
		$this->load->library('email');
		$firstname = $account_info['first_name'];
		$lastname = $account_info['last_name'];
		$username = $account_info['username'];
		$email = $account_info['email_address'];
		$mobile = $account_info['mobile_number'];

		$bank = $billing_info['bank_name'];
		$bank_num = $billing_info['bank_account_number'];
		$bank_type = $billing_info['bank_account_type'];
		$bank_code = $billing_info['bank_branch_code'];

		$content = "
Here is the new client registration information:
First Name : $firstname
Last Name : $lastname
User Name : $username
Email : $email
Mobile Number : $mobile

Here is client's Banking Details:
Bank Name: $bank
Bank Account Number: $bank_num
Bank Type: $bank_type
Branch Code: $bank_code";

		$title = "New Registration Client Information";
		$this->email->from('admin@openweb.com', 'OpenWeb');
		$this->email->to('ceo@openweb.co.za');
		$this->email->subject($title);
		$this->email->message($content);
		$this->email->send();
	}

	function get_blling_info($user)
	{
		$this->db->where('username', $user);
		$query = $this->db->get('billing');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}

	function get_open_ISP()
	{
		$query = $this->db->get('openisp_cc');
		$result = $query->result_array();
		return $result[0];
	}

	function get_invoice_vat($pdf_name, $user)
	{
		$this->db->select('id, create_date');
		$this->db->select('user_name', $user);
		$this->db->select('name', $pdf_name);
		$query = $this->db->get('openisp_cc');
		$result = $query->result_array();
		return $result[0];
	}

	function get_billing_data($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('billing');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}

	function get_billing_data_by_user_id($id)
	{
		$this->db->where('id_user', $id);
		$query = $this->db->get('billing');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}


	function billing_data($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('billing');
		$result = $query->result_array();
		return $result[0];
	}

	function get_user_id($username)
	{
		$this->db->select('id');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		return $user['id'];
	}

	function save_invoice_pdf($data, $file_name)
	{
		$this->db->select('id');
		$this->db->where('name', $file_name);
		$query = $this->db->get('invoice_pdf');
		$pdf_data = $query->result_array();
		if ($pdf_data) {
			$id = $pdf_data[0]['id'];
			$this->db->where('id', $id);
			$result = $this->db->update('invoice_pdf', $data);
		} else {
			$result = $this->db->insert('invoice_pdf', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}

	function save_pdf($invoice_id, $data)
	{
		$this->db->select('id');
		$this->db->where('invoices_id', $invoice_id);
		$query = $this->db->get('invoice_pdf');
		$pdf_data = $query->result_array();
		if ($pdf_data) {
			$id = $pdf_data[0]['id'];
			$this->db->where('id', $id);
			$result = $this->db->update('invoice_pdf', $data);
		} else {
			$result = $this->db->insert('invoice_pdf', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}

	function save_invoices($order_id, $user)
	{
		$data = array(
			'invoice_name' => "Tax Invoice for $user in " . date('Y-m-d', time()),
			'create_date' => date('Y-m-d H:i:s', time()),
			'type' => 'auto',
			'order_id' => $order_id,
			'user_id' => $this->get_user_id($user),
			'user_name' => $user,
		);

		$this->db->insert('invoices', $data);
		return $this->db->insert_id();
	}

	function save_topup_invoice($topup_order_id, $user)
	{
		$data = array(
			'invoice_name' => "Tax Invoice for $user in " . date('Y-m-d', time()),
			'create_date' => date('Y-m-d H:i:s', time()),
			'type' => 'auto',
			'topup_id' => $topup_order_id,
			'user_id' => $this->get_user_id($user),
			'user_name' => $user,
		);

		$this->db->insert('invoices', $data);
		return $this->db->insert_id();
	}

	function get_invoices_data($username)
	{
		$this->db->where('user_name', $username);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('invoices');
		$data = $query->result_array();
		return $data;
	}

	function get_invoice_pdf_path($inv_id)
	{
		$this->db->select('path');
		$this->db->where('invoices_id', $inv_id);
		$query = $this->db->get('invoice_pdf');
		$result = $query->result_array();
		return $result[0]['path'];
	}

	function get_invoices_data_by_id($invoice_id)
	{
		$this->db->where('id', $invoice_id);
		$query = $this->db->get('invoices');
		$data = $query->result_array();
		return $data[0];
	}

	function get_invoice_username($invoice_id)
	{

		$invoice_array = $this->get_invoices_data_by_id($invoice_id);
		return $invoice_array['user_name'];
	}

	function get_user_bulk_param($username)
	{

		if (!isset($username))
			return false;

		$this->db->select('bulk_email');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->result_array();

		if (empty($result))
			return false;


		return $result[0]['bulk_email'];
	}

	function save_user_bulk_param($username, $val)
	{

		if (!isset($username))
			return false;


		if (!isset($val))
			return false;


		$this->db->where('username', $username);
		$result = $this->db->update('membership', array('bulk_email' => $val));

		return $result;
	}


	function get_user_invoice_mail_param($username)
	{

		if (!isset($username))
			return false;

		$this->db->select('invoice_email');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->result_array();

		if (empty($result))
			return false;


		return $result[0]['invoice_email'];
	}


	function save_user_invoice_mail_param($username, $val)
	{

		if (!isset($username))
			return false;

		if (!isset($val))
			return false;

		$this->db->where('username', $username);
		$result = $this->db->update('membership', array('invoice_email' => $val));

		return $result;
	}

	function get_user_imported_option($username)
	{

		$this->db->select('imported_user');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');

		if (empty($result)) {
			return 0;
		}

		return $result['imported_user'];
	}

	function get_user_name_by_id($id)
	{
		$this->db->select('username');
		$this->db->where('id', $id);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		$name = $user['username'];
		return $name;
	}



	function email_topup_with_invoice_individual($username, $inv_id = null)
	{

		$email_tempalte = $this->get_email_detail('topup_order');

		$title = $email_tempalte[0]['title'];
		$msg   = $email_tempalte[0]['content'];
		$admin_email = $email_tempalte[0]['email_address'];

		$this->load->library('email');

		if (isset($username)) {

			$user_data_array = $this->get_user_email_with_first_name($username);

			$client_email = $user_data_array['email_address'];
			$name = $user_data_array['first_name'];
			$date = date('F o', strtotime('now'));

			$path  = "";
			if (!empty($inv_id))
				$path = $this->get_invoice_pdf_path($inv_id);

			$data = array(
				'first_name' => $name,
			);
			$msg = $this->email_content_repalcer($msg, $data);


			$this->email->from($admin_email, 'OpenWeb Home');
			$this->email->to($client_email);
			$this->email->subject($title);
			$this->email->message($msg);
			if (!empty($path))
				$this->email->attach($path);
			$this->email->send();
			$this->email->clear(TRUE);
		}
	}






	function get_user_email_with_first_name($username)
	{

		$this->db->select('first_name,email_address');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');

		return $result;
	}

	function get_email_detail($purpose)
	{
		$this->db->where('purpose', $purpose);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		return $result;
	}

	function email_content_repalcer($content, $data)
	{


		$content = str_ireplace('[User_Name]', $data['username'], $content);
		$content = str_ireplace('[First_Name]', $data['first_name'], $content);
		$content = str_ireplace('[Last_Name]', $data['last_name'], $content);
		$content = str_ireplace('[Password]', $data['password'], $content);
		$content = str_ireplace('[Email_Address]', $data['email'], $content);
		$content = str_ireplace('[Register_Date]', $data['joined'], $content);
		$content = str_ireplace('[Current_Status]', $data['status'], $content);

		return $content;
	}


	function get_user_billing_info($username)
	{

		$user_list = $this->get_user_data($username);
		$result = $user_list['user_billing'];
		if ($result) {

			if (!empty($result['billing_name'])) {
				$billing_name = $result['billing_name'];
			} else {
				return false;
			}

			$address = $result['address_1'] . ' ' . $result['address_2'];
			$city = $result['city'];
			$province = $result['province'];
			$country = $result['country'];
			$phone = $result['contact_number'];

			$billing_data = array(
				'billing_name' => $billing_name,
				'address' => $address,
				'city' => $city,
				'province' => $province,
				'country' => $country,
				'phone' => $phone,
			);
			return $billing_data;
		} else {
			return false;
		}
	}


	function topup_email_to_admin($username, $full_name, $topup_order_info, $topup_id, $base_url)
	{
		$this->load->library('email');

		$content  = "User : " . $full_name . " (" . $username . ")";
		$content .= "\nISDSL account :  " . $topup_order_info['adsl_username'];
		$content .= "\nOrder time :  " . $topup_order_info['order_time'];
		$content .= "\nPayment method :  " . $topup_order_info['payment_method'];
		$content .= "\nPrice :  " . $topup_order_info['price'];
		$content .= "\nLink : " . $base_url . "admin/edit_topup_order/" . $topup_id;

		$address = 'ceo@openweb.co.za';
		if ($username == 'test-vvv')
			$address = 'baf4mail@gmail.com';

		$title = "New TopUp order";
		$this->email->from('noreply@openweb.co.za', 'OpenWeb');
		$this->email->to($address);
		$this->email->subject($title);
		$this->email->message($content);
		$this->email->send();
		$this->email->clear(TRUE);
	}

	function topup_error_email_to_admin($username, $full_name, $topup_order_info, $topup_id, $base_url, $api_code, $api_message)
	{
		$this->load->library('email');

		$content  = "User : " . $full_name . " (" . $username . ")";
		$content .= "\nISDSL account :  " . $topup_order_info['adsl_username'];
		$content .= "\nOrder time :  " . $topup_order_info['order_time'];
		$content .= "\nPayment method :  " . $topup_order_info['payment_method'];
		$content .= "\nPrice :  " . $topup_order_info['price'];
		$content .= "\nLink : " . $base_url . "admin/edit_topup_order/" . $topup_id;

		$content .= "\n\nISDSL status : " . $api_code . " ( " . $api_message . " ) ";

		$address = 'ceo@openweb.co.za';
		//if ($username == 'test-vvv')
		//    $address = 'baf4mail@gmail.com';

		$title = "New TopUp order (class was not changed)";
		$this->email->from('noreply@openweb.co.za', 'OpenWeb');
		$this->email->to($address);
		$this->email->subject($title);
		$this->email->message($content);
		$this->email->send();
		$this->email->clear(TRUE);
	}

	function lteTopupButon()
	{

		$username = $this->site_data['username'];

		$orders = $this->get_active_orders($username, 5, 0, array('lte-a'));

		if (count($orders) > 0) {
			return true;
		}

		return false;
	}


	public function user_percentage($full_usage_data, $order_id)
	{
		$percentage = $this->db->get_where('fibre_orders', ['order_id' => $order_id])->result()[0]->percentage;

		if ($percentage == '' || $percentage == 0) {
			return $full_usage_data;
		}

		if ($full_usage_data['day_usage'] > 0) {
			$full_usage_data['day_usage'] = $full_usage_data['day_usage'] + $full_usage_data['day_usage'] * ($percentage / 100);
		}

		if ($full_usage_data['year_usage'] > 0) {
			$full_usage_data['year_usage'] = $full_usage_data['year_usage'] + $full_usage_data['year_usage'] * ($percentage / 100);
		}

		if ($full_usage_data['month_usage'] > 0) {
			$full_usage_data['month_usage'] = $full_usage_data['month_usage'] + $full_usage_data['month_usage'] * ($percentage / 100);
		}

		return $full_usage_data;
	}

	public function update_data_amount($order_id, $data)
	{
		$percentage = $this->db->get_where('fibre_orders', ['username' => $order_id])->result()[0]->percentage;

		if ($percentage == '' || $percentage == 0) {
			return $data;
		}

		$percentage = (100 + $percentage) / 100;

		if ($data > 0) {
			$data = $data * $percentage;
		}

		return $data;
	}

	public function update_total_data($summary_data, $id, $month_data)
	{
		$replace = $this->db->get_where('fibre_orders', ['order_id' => $id])->result()[0];
		$percentage = $replace->percentage;
		$total_data = $replace->total_data;

		foreach ($summary_data as &$data) {

			if ($total_data != '' &&  $total_data != 0) {
				if ($data['Data Units'] == 'MB') {
					$total_data_res = $total_data * 1024;
				} else {
					$total_data_res = $total_data;
				}
				$data['Total Data'] = $total_data_res;
			}

			if ($data['Remaining Data'] > 0) {
				$data['Remaining Data'] = $data['Total Data'] - $month_data;
			}
		}

		return $summary_data;
	}
}
