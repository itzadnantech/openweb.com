 <?php

class User_model extends CI_Model
{

	function get_notifications($username)
	{
		$this->db->where('user', $username);
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get('notifications');
		$result = $query->result_array();
		return $result;
	}

	function get_user_id($username)
	{
		$this->db->select('id');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		return $user['id'];
	}

	function get_email($username)
	{
		$this->db->select('email_address');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$email = $result['email_address'];
		return $email;
	}

	// Give a username, this function will return all data in an array
	function get_user_data($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user_settings = $query->first_row('array');

		$this->db->where('username', $username);
		$query = $this->db->get('billing');
		$user_billing = $query->first_row('array');

		$data = array(
			'user_settings' => $user_settings,
			'user_billing' => $user_billing,
		);
		return $data;
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




	function get_user_name($username)
	{
		$this->db->select('first_name, last_name');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		if ($user) {
			$name = $user['first_name'] . ' ' . $user['last_name'];
		} else {
			$name = '';
		}
		return $name;
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

	function get_user_list()
	{
		$this->db->where('username !=', 'Administrator');
		$this->db->where('status', 'active');
		$this->db->select('first_name, last_name, username');
		$query = $this->db->get('membership');
		return $query->result_array();
	}


	// ->                                                                       admin/order_model
	function get_orders($username, $num = 10, $start = 0, $services = array('adsl'), &$order_model = null)
	{

		if (empty($services))
			$services = array('adsl');

		$this->db->where('user', $username);
		$this->db->where_in('service_type', $services);
		$this->db->limit($num, $start);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		$result = $this->get_updated_cancellations($result);

		if (empty($order_model))
			return $result;

		foreach ($result as $r) {
			if (($r['service_type'] == 'fibre-data') || ($r['service_type'] == 'fibre-line') || ($r['service_type'] == 'mobile') || $r['service_type'] == 'lte-a') {
				$fibre = $this->order_model->get_fibre_data_by_order($r['id']);
				if (!empty($fibre))
					$r['fibre'] = $fibre;
			}
			$data[] = $r;
		}

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
					if (
						isset($or['date_cancelled'])
						&& trim($or['date_cancelled'] != '')
					) {
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

	function get_orders_count($username)
	{

		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->from('orders');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_emails_list()
	{
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		return $result;
	}

	function get_email_detail($purpose)
	{
		$this->db->where('purpose', $purpose);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		return $result;
	}

	function get_email_id($purpose)
	{
		$this->db->select('id');
		$this->db->where('purpose', $purpose);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0]['id'];
		} else {
			return null;
		}
	}

	function add_email_attachment($data)
	{
		if ($data) {
			$result = $this->db->insert('email_attachment', $data);
		}
		return $result;
	}

	function get_email_attach($template_id)
	{
		$this->db->where('email_template_id', $template_id);
		$query = $this->db->get('email_attachment');
		$result = $query->result_array();
		return $result;
	}

	function get_full_email_detail($purpose)
	{

		if (empty($purpose))
			return false;


		$response_array = array(
			'email_detail' => '',
			'email_attach' => '',
		);

		$email_detail = $this->get_email_detail($purpose);
		if (!empty($email_detail)) {

			$response_array['email_detail'] = $email_detail[0];
			$template_id = $email_detail[0]['id'];
			$email_attach_data = $this->user_model->get_email_attach($template_id);
			if (!empty($email_attach_data))
				$response_array['email_attach'] =  $email_attach_data;
		}

		return $response_array;
	}



	function delete_email_attachement($id)
	{
		$result = $this->db->delete('email_attachment', array('id' => $id));
		return $result;
	}

	function get_email_attachement($id)
	{
		$this->db->select('path');
		$this->db->where('id', $id);
		$query = $this->db->get('email_attachment');
		$result = $query->result_array();
		if ($result) {
			return $result[0]['path'];
		} else {
			return null;
		}
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



	function create_invoices($data)
	{
		$this->db->insert('invoices', $data);
		$id = $this->db->insert_id();
		return $id;
	}

	function save_invoice_pdf($invoice_id, $data)
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

	function get_open_ISP()
	{
		$query = $this->db->get('openisp_cc');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}

	function get_invoice_data($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('invoices');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}

	function get_invoices_list($num = 10, $start)
	{
		$this->db->order_by('id', 'desc');
		$this->db->limit($num, $start);
		$query = $this->db->get('invoices');
		$result = $query->result_array();
		return $result;
	}

	function get_invoice_list_count()
	{
		$this->db->select('id');
		$this->db->from('invoices');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_invoice_pdf_path($inv_id)
	{
		$this->db->select('path');
		$this->db->where('invoices_id', $inv_id);
		$query = $this->db->get('invoice_pdf');
		$result = $query->result_array();
		if ($result) {
			return $result[0]['path'];
		} else {
			return null;
		}
	}

	function delete_invoice($id)
	{
		$result = $this->db->delete('invoices', array('id' => $id));
		return $result;
	}

	function get_invoice_user($user, $num = 10, $start)
	{
		$this->db->where('user_name', $user);
		$this->db->order_by('id', 'desc');
		$this->db->limit($num, $start);
		$query = $this->db->get('invoices');
		$result = $query->result_array();
		if ($result) {
			return $result;
		} else {
			return false;
		}
	}

	function get_invoice_count($user)
	{
		$this->db->select('id');
		$this->db->where('user_name', $user);
		$this->db->from('invoices');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_manuall_invoice($id)
	{
		$this->db->where('invoice_id', $id);
		$query = $this->db->get('manuall_invoice');
		$result = $query->result_array();
		return $result;
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
			$password = $this->crypto_model->decode($row->password);
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

	function get_bulk_users()
	{

		$this->db->select('first_name, last_name, username, password, email_address, status');
		$this->db->where('bulk_email', '1');
		$query = $this->db->get('membership');
		$result = $query->result_array();
		return $result;
	}


	function get_bulk_users_new($limit = null, $offset = null)
	{

		$this->db->select('first_name, last_name, username, password, email_address, status');
		$this->db->where('bulk_email', '1');
		$this->db->limit($limit, $offset);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		return $result;
	}

	function batch_data($email_type = null)
	{
		$where = array('email_type' => $email_type);
		$this->db->select('*');
		$query = $this->db->where($where);
		$query = $this->db->get('email_crons');
		$result = $query->result_array();
		return $result;
	}

	function get_cron_bulk_mail_state($email_type = null)
	{
		$where = array('status' => 'active', 'email_type' => $email_type);
		$this->db->select('*');
		$query = $this->db->where($where);
		$query = $this->db->get('email_crons');
		$result = $query->result_array();
		return $result;
	}

	function update_email_crons_table($data = array(), $batch_id = null)
	{
		$where = array('status' => 'active', 'batch_id' => $batch_id);
		$this->db->where($where);
		return $this->db->update('email_crons', $data);
	}


	function batch_data_by_id($batch_id)
	{
		$this->db->select('*');
		$query = $this->db->where('batch_id', $batch_id);
		$query = $this->db->get('email_data');
		$result = $query->result_array();
		return $result;
	}

	///email crons
	function add_batch($data = array())
	{
		$this->db->insert('email_crons', $data);
		$last_id = $this->db->insert_id();
		return $last_id;
	}


	///table email crons
	function get_email_crons_table($email_type = null)
	{
		$where = array('status' => 'active', 'email_type' => $email_type);
		$this->db->select('*');
		$this->db->where($where);
		$query =  $this->db->get('email_crons');
		$result = $query->result_array();
		if (!empty($result)) {
			return 'active';
		} else {
			return 'inactive';
		}
	}



	function get_bulk_reseller_users()
	{
		$this->db->select('first_name, last_name, username, password, email_address, status,role');
		$this->db->where('role', 'reseller');
		$query = $this->db->get('membership');
		$result = $query->result_array();
		return $result;
	}

	function get_reseller_bulk_users_new($limit = null, $offset = null)
	{

		$this->db->select('first_name, last_name, username, password, email_address, status');
		$this->db->where('role', 'reseller');
		$this->db->limit($limit, $offset);
		$query = $this->db->get('membership');
		$result = $query->result_array();
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


	function get_user_invoice_mail_param_by_id($user_id)
	{

		if (!isset($user_id))
			return false;

		$this->db->select('invoice_email');
		$this->db->where('id', $user_id);
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


	function get_invoice_username($invoice_id)
	{

		$invoice_array = $this->get_invoices_data_by_id($invoice_id);
		return $invoice_array['user_name'];
	}

	//Validation on page(jQuery)
	function preValidateAvios($avios_settings)
	{
		foreach ($avios_settings as $name => $val) {
			if (!empty($val) && preg_match("/[0-9]/i", $val)) {
				/*
                $this->db->where($name, $val);
                $query = $this->db->get('membership');
                $row = $query->result();

                if (empty($row)) {
                    return array(
                        $name => $val
                    );
                } else {
                    return "d";
                }
*/
				return [$name => $val];
			}
		}
		$counter = 0;
		foreach ($avios_settings as $name => $val) {
			if (empty($val)) {
				$counter += 1;
			}
		}
		if ($counter == 2) {
			return true;
		}
		return false;
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
			return $insert_data;
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


	function getAviosId($user_id)
	{
		$this->select('br_a_id');
		$this->select('avios_id');
		$this->db->where('id', $user_id);
		$query = $this->db->get('membership');
		$row = $query->result();

		if (empty($row)) {
			return false;
		}

		return true;
	}

	function userStatuses()
	{

		$query = $this->db->query('SELECT DISTINCT status FROM membership');
		$row = $query->result_array();
		$res = ['All'];
		foreach ($row as $status) {
			array_push($res, $status["status"]);
		}

		return $res;
	}

	function getUsersByFilters($post)
	{

		$query = "SELECT DISTINCT membership.mobile_number, membership.username FROM membership LEFT JOIN orders ON membership.username=orders.user";
		$query .= " WHERE (membership.mobile_number<>'' AND membership.mobile_number IS NOT NULL)";

		foreach ($post as $key => $value) {

			if ($value != "All" && strpos($key, "order") === 0) {
				$col = substr($key, strpos($key, "_") + 1);
				$query .= " AND orders." . $this->db->escape_str($col) . "='" . $this->db->escape_str($value) . "'";
			}

			if ($value != "All" && strpos($key, "user") === 0) {

				$col = substr($key, strpos($key, "_") + 1);
				$query .= " AND membership." . $this->db->escape_str($col) . "='" . $this->db->escape_str($value) . "'";
			}
		}

		$list = $this->db->query($query);
		$row = $list->result_array();

		return $row;
	}

	function mailAboutAvios($user_id, $points, $bonus)
	{

		$this->load->model('message_model');

		$user = $this->get_user_data_by_id($user_id);

		if (!isset($user['user_settings']['email_address'])) {
			return false;
		}

		$tmpl = $this->message_model->get_email_template_by_purpose('avios_confirmation');
		$message = $tmpl['content'];
		$message = str_replace('[First_Name]', $user['user_settings']['first_name'], $message);
		$message = str_replace('[Points]', $points, $message);
		$message = str_replace('[Bonus_Points]', $bonus, $message);

		$this->message_model->send_email($tmpl['email_address'], $user['user_settings']['email_address'], $tmpl['title'], $message, array());
	}

	function searchUser($string)
	{

		$string = trim($string);

		if (strpos($string, ' ') > 0) {

			$first = substr($string, 0, strpos($string, ' '));
			$last = substr($string, strpos($string, ' ') + 1);
			$this->db->like('first_name', $first);
			$this->db->like('last_name', $last);
		} else {
			$this->db->like('first_name', $string);
			$this->db->or_like('last_name', $string);
			$this->db->or_like('username', $string);
		}

		$res = $this->db->get('membership')->result_array();

		return $res;
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
}
