 <?php
class Message_model extends CI_Model
{
	private $bulk_mail_log_filename = "bulk-mail";

	function get_message_category_list()
	{
		$query = $this->db->query('select distinct category from messages_template');
		$result = $query->result_array();
		return $result;
	}

	function get_stats_req_support_mail_template()
	{
		$this->db->where('id', 21);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {

			return null;
		}
	}

	function get_message_list($category)
	{
		$query = $this->db->query('select * from messages_template where category = "' . $category . '"');
		$result = $query->result_array();
		return $result;
	}

	function update_message_template($data)
	{
		$id = $data['id'];
		$content = $data['content'];
		//$query = $this->db->query('update messages_template set content = "'.$content.'" where id= '.$id.'');
		$data = array('content' => $content);
		$result = $this->db->update('messages_template', $data, array('id' => $id));
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	function get_message($data)
	{
		$this->db->where($data);
		$query = $this->db->get('messages_template');
		foreach ($query->result() as $row) {
			$content = $row->content;
		}
		return $content;
	}

	function get_email_template()
	{
		$this->db->where('purpose', 'subscribe');
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}
	function get_telkom_topup_loaded_mail_template()
	{
		$this->db->where('id', 18);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}
	function get_telkom_topup_stats_mail_template()
	{
		$this->db->where('id', 17);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}
	function get_mobile_topup_stats_mail_template()
	{
		$this->db->where('id', 23);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}
	function get_mobile_new_stats_req_mail_template()
	{
		$this->db->where('id', 24);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {

			return null;
		}
	}
	function get_telkom_new_stats_req_mail_template()
	{
		$this->db->where('id', 19);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {

			return null;
		}
	}
	function get_mtn_new_stats_req_mail_template()
	{
		$this->db->where('id', 20);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {

			return null;
		}
	}

	function get_email_template_by_purpose($pur)
	{
		$this->db->where('purpose', $pur);
		$query = $this->db->get('email_template');
		$result = $query->result_array();
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}


	function send_bulk_email($users, $email_detail, $email_attach_data, $batch_id = null)
	{


		// if (empty($users))
		//     return false;

		// if (empty($email_detail))
		//     return false;
		/*
        if (empty($email_attach_data))
            return false;
        */
		// echo json_encode("test 1");
		$this->write_log(
			"\n-- " . date("Y-m-d  H:i:s") . "--\n" .
				"users count : " . count($users),
			//"users list : " . print_r($users, true) . "\n\n",
			$this->bulk_mail_log_filename
		);

		$this->beginCounter();

		$i = 0;
		foreach ($users as $user) {

			$this->addToCounter();

			if (empty($user['email_address'])) {
				$this->write_log(
					" # " . ++$i . " " . $user['username'] .
						"  @ : " . " [EMPTY-SKIP] " .  $user['email_address'],
					$this->bulk_mail_log_filename
				);
				continue;
			}

			$message = $email_detail['content'];
			$message = str_replace('[User_Name]', $user['username'], $message);
			$message = str_ireplace('[First_Name]', $user['first_name'], $message);
			$message = str_ireplace('[Last_Name]', $user['last_name'], $message);
			$message = str_ireplace('[Password]', $user['password'], $message);
			$message = str_ireplace('[Email_Address]', $user['email_address'], $message);
			$message = str_ireplace('[Current_Status]', $user['status'], $message);

			$res = $this->send_email(
				$email_detail['email_address'],
				$user['email_address'],
				$email_detail['title'],
				$message,
				$email_attach_data
			);

			if ($res) {
				$email_post_data = array();
				$email_post_data['batch_id'] = $batch_id;
				$email_post_data['username'] = $user['username'];
				$email_post_data['email'] = $user['email_address'];
				$email_post_data['status'] = $res;
				// echo '<pre>';
				// print_r($email_post_data);
				// echo '</pre>';
				// die;
				$this->add_email_data($email_post_data);
				$this->addToCounter(2);
			}

			$this->write_log(
				" # " . ++$i . " " . $user['username'] .
					"  @ : " . " " .  $user['email_address'],
				$this->bulk_mail_log_filename
			);
		}
	}

	///add_email_data
	function add_email_data($data)
	{
		$this->db->insert('email_data', $data);
		return $this->db->insert_id();
	}


	function send_email($from, $to, $subject, $message, $attach_array)
	{
		$this->load->library('email');

		$this->email->from($from);
		$this->email->to($to);
		// $this->email->to('');
		$this->email->subject($subject);
		$this->email->message($message);
		foreach ($attach_array as $attach) {
			$this->email->attach($attach['path']);
		}


		// $res = $this->email->send();
		if (!$this->email->send()) {
			$res = 'Failed';
		} else {
			$res = 'Sent';
		}
		$this->email->clear(TRUE);
		return $res;
	}

	function send_email_html($from, $to, $subject, $message, $attach_array)
	{
		$this->load->library('email');
		$this->email->set_mailtype("html");

		$this->email->from($from);
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($message);
		foreach ($attach_array as $attach) {
			$this->email->attach($attach['path']);
		}


		$res = $this->email->send();
		$this->email->clear(TRUE);

		return $res;
	}


	public function write_log($str, $filename)
	{

		$log_file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
			DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $filename . "-log.txt";

		$log_handle = fopen($log_file_path, 'a+');
		fwrite($log_handle, "\n " . $str);
		fclose($log_handle);
	}

	function addToCounter($id = null)
	{

		$query = "UPDATE bulk_email_counter SET count = count + 1 WHERE id =1";

		if (isset($id))
			$query = "UPDATE bulk_email_counter SET count = count + 1 WHERE id =" . $id;

		$this->db->query($query);
	}

	function beginCounter()
	{

		$query = "REPLACE INTO bulk_email_counter VALUES (1, 0), (2, 0), (3, 0)";
		$this->db->query($query);
	}

	function get_messages_count()
	{

		$this->db->where("id", 1);
		$query = $this->db->get("bulk_email_counter");
		$answ = $query->result_array();

		return $answ[0]["count"];
	}

	function get_bulk_email_result()
	{

		$q = $this->db->get('bulk_email_counter');
		$res = $q->result_array();

		return $res;
	}
}
