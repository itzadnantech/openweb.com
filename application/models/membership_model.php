<?php
//
class Membership_model extends CI_Model {
	
	function get_name($username) {
		$this->db->select('first_name');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$name = $result['first_name'];
		return $name;
	}

    function get_name_by_id($user_id) {
        $this->db->select('first_name');
        $this->db->where('id', $user_id);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');
        $name = $result['first_name'];
        return $name;
    }
    
    		function get_email_z($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		return $result['email_address'];
	}

    function get_second_name_by_id($user_id) {
        $this->db->select('last_name');
        $this->db->where('id', $user_id);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');
        $name = $result['last_name'];
        return $name;
    }

    function get_second_name($username) {
        $this->db->select('last_name');
        $this->db->where('username', $username);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');
        $name = $result['last_name'];
        return $name;
    }
	
	function get_user_name_nice($username) {
		$this->db->select('first_name, last_name');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		$name = $user['first_name'] . ' ' . $user['last_name'];
		return $name;
	}

    function get_user_name_nice_by_id($id) {
        $this->db->select('first_name, last_name');
        $this->db->where('id', $id);
        $query = $this->db->get('membership');
        $user = $query->first_row('array');
        $name = $user['first_name'] . ' ' . $user['last_name'];
        return $name;
    }
	
	function get_number($username) {
		$this->db->select('mobile');
		$this->db->where('username', $username);
		$query = $this->db->get('billing');
		$result = $query->first_row('array');
		$number = $result['mobile'];
		return $number;
	}
	
	function get_email($username) {
		$this->db->select('email_address');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$email = $result['email_address'];
		return $email;
	}
	
	function get_user_mobile($username){
		$this->db->select('mobile_number');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$mobile = $result['mobile_number'];
		return $mobile;
	}
	
	function get_all_activity_count() {
		$this->db->select('id');
		$query = $this->db->get('activity_log');
		return $query->num_rows();
	}
	
	function get_account_count(){
		$this->db->select('id');
		$this->db->where('status !=', 'deleted');
		$this->db->where('role !=', 'super_administrator');
		$this->db->where('role !=', 'admin');
		$this->db->from('membership');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function get_account_data($account_id){
		$this->db->where('id', $account_id);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		return $result;
	}
	
	function get_all_account($num = 10, $start=0) {
		$this->db->limit($num, $start);
		$this->db->where('status !=', 'deleted');
		$this->db->where('role !=', 'super_administrator');
		$this->db->where('role !=', 'admin');
		$this->db->order_by('joined', 'desc');
		$query = $this->db->get('membership');
		$result = $query->result_array();
		return $result;
	}

    // -----------------  Activity part --------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

	function get_all_activity_log($num = 10, $start = 0) {
		$this->db->order_by('date', 'desc');
		$this->db->limit($num, $start);
		$query = $this->db->get('activity_log');
		$result = $query->result_array();
		return $result;
	}

	function get_activity_count($username) {
		$this->db->select('id');
		$this->db->where('user', $username);
		$query = $this->db->get('activity_log');
		return $query->num_rows();
	}

	function get_activity_log($username, $num = 10, $start = 0) {
		$this->db->where('user', $username);
		$this->db->order_by('date', 'desc');
		$this->db->limit($num, $start);
		$query = $this->db->get('activity_log');
		$result = $query->result_array();
		return $result;
	}
	
	function get_activity_log_by_date($username, $start_date, $end_date, $num = 10, $start = 0){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select * from activity_log where user ="'.$username.'" and 
									date between "'.$star_date.'" and "'.$end_date.'" 
									order by date desc 
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return null;
		}
		
	}
	
	function get_activity_count_by_date($username,$start_date, $end_date){
		$query = $this->db->query('select id from activity_log where user ="'.$username.'" and date between "'.$start_date.'" and "'.$end_date.'"');
		return $query->num_rows();
	}
	
	function get_all_activity_log_by_date($start_date, $end_date,$num = 10, $start = 0){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select * from activity_log where date between "'.$start_date.'" and "'.$end_date.'" 
									order by date desc
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_all_activity_count_by_date($start_date, $end_date){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select id from activity_log where date between "'.$start_date.'" and "'.$end_date.'"');
		return $query->num_rows();
	}
	
	function get_activity_log_by_type($username, $type, $start_date, $end_date, $num = 10, $start = 0){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select * from activity_log where user ="'.$username.'" and type ="'.$type.'" and 
									date between "'.$star_date.'" and "'.$end_date.'" 
									order by date desc 
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_activity_count_by_type($username,$type, $start_date, $end_date){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select id from activity_log where user ="'.$username.'" and type ="'.$type.'" 
									and date between "'.$start_date.'" and "'.$end_date.'"');
		return $query->num_rows();
	}
	
	function get_activity_log_by_type_only($username, $type, $num = 10, $start = 0){
		$query = $this->db->query('select * from activity_log where user ="'.$username.'" and type ="'.$type.'"
									order by date desc 
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_activity_count_by_type_only($username, $type){
		$query = $this->db->query('select id from activity_log where user ="'.$username.'" and type ="'.$type.'"');
		return $query->num_rows();
	}
	
	function get_all_activity_log_by_type($type, $start_date, $end_date, $num = 10, $start = 0){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select * from activity_log where type="'.$type.'" 
									and date between "'.$start_date.'" and "'.$end_date.'"
									order by date desc  
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_all_activity_count_by_type($type, $start_date, $end_date){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select id from activity_log where type ="'.$type.'" and 
									date between "'.$start_date.'" and "'.$end_date.'"');
		return $query->num_rows();
	}
	
	function get_all_activity_log_by_type_only($type, $num = 10, $start = 0){
		$query = $this->db->query('select * from activity_log where type="'.$type.'" 
									order by date desc 
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_all_activity_count_by_date_only($type){
		$query = $this->db->query('select id from activity_log where type ="'.$type.'"');
		return $query->num_rows();
	}

    function get_2auth ($username) {
        $this->db->select('2auth');
        $this->db->where('username', $username);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');
        $auth = $result['2auth'];
        return $auth === 'y';
    }

    function get_2auth_type ($username) {
        $this->db->select('2auth_type');
        $this->db->where('username', $username);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');
        return $result['2auth_type'];
    }

    // Activity types :
    public $activityTypes = array(

        'order_product'       => 'Order Product',
        'change_cc_info'      => 'User Change CC Information',

        // ..user/settings
        'change_acc_settings' => 'User Change Account Settings (Email Address, Mobile Number)',

        // ..user/mobile_data_docs
        'change_mobile_data' => 'User Change Personal data',

        // ..user/change_password

        'change_user_pass' => 'User Change personal password',

        // ..user/email_param
        'change_mailing_list' => 'User update Mailing List parameter',

        // ../user/invoice_email
        'change_invoice_email' => 'User update Invoice Email parameter',
    );

    function add_activity_log($account_id, $activity_type, $link = ''){

        if (empty($account_id) || empty($activity_type))
            return false;

        $date = date('l jS \of F Y \a\t h:i A');
        /*
              $link = 'admin/edit_account/'.$user_id;
              $date = date('l jS \of F Y \a\t h:i A');
              $activity = "On $date, $username updated his/her CC information. \n";
              $activity .= "View the detail through the link below: \n";
              $activity .= "<a href='".base_url().$link."' target='_blank'>".base_url().$link."</a>";


         */

        $username = $this->get_user_name($account_id);
        $type = $this->activityTypes[$activity_type];
        if (empty($type))
            return false;

        $activity_message = '';
        switch ($activity_type) {

            case "change_acc_settings" : {
                $link_message = base_url() . 'admin/edit_account/' . $account_id;
                $activity_message = "On $date, $username updated his/her Email and Mobile number. \n";
                $activity_message .= "View the detail through the link below: \n";
                $activity_message .= "<a href='" . $link_message ."' target='_blank'>". $link_message ."</a>";
                break;
            }

            case "change_mobile_data" : {
                $link_message = base_url() . 'admin/edit_account/' . $account_id;
                $activity_message = "On $date, $username updated his/her Personal data (Required for Mobile Data). \n";
                $activity_message .= "View the detail through the link below: \n";
                $activity_message .= "<a href='" . $link_message ."' target='_blank'>". $link_message ."</a>";
                break;
            }

            case "change_user_pass" : {

                $link_message = base_url() . 'admin/edit_account/' . $account_id;
                $activity_message = "On $date, $username updated his/her password. \n";
                $activity_message .= "View the detail through the link below: \n";
                $activity_message .= "<a href='" . $link_message ."' target='_blank'>". $link_message ."</a>";
                break;
            }

            case "change_mailing_list" : {

                $link_message = base_url() . 'admin/edit_account/' . $account_id;
                $activity_message = "On $date, $username updated his/her 'Mailing List' parameter.";
                break;
            }

            case 'change_invoice_email' : {

                $link_message = base_url() . 'admin/edit_account/' . $account_id;
                $activity_message = "On $date, $username updated his/her 'Invoice Email' parameter. \n";
                $activity_message .= "View the detail through the link below: \n";
                $activity_message .= "<a href='" . $link_message ."' target='_blank'>". $link_message ."</a>";
                break;
            }


        };

        $activity = array(

            'user' => $username,
            'activity' => $activity_message,
            'type' => $type,
            'link' => $link,
        );

        $this->db->insert('activity_log', $activity);

    }

    function add_order_activity_log($username, $product_id, $product_name, $total_price, $pro_rata_total){


        // Insert into activity log
        $date = date('l jS \of F Y \a\t h:i A');
        $nice_price = number_format(round($total_price, 2), 2);
        $next_month = date("F Y",strtotime("+1 months"));
        $cur_date = date("F Y");
        $activity = "On $date, $username ordered $product_name. \n";
        $activity .= "Pro-rata billing for $cur_date: R$pro_rata_total. \n";
        $activity .= "Billing from $next_month: R$nice_price.";
        $activity = array(
            'user' => $username,
            'activity' => $activity,
            'type' => 'Order Product',
        );
        $this->db->insert('activity_log', $activity);

    }

    function add_fibre_order_activity_log($data_array, $service){
        $product_name = "none";
        // write to active log
        if (isset($data_array['write_to_log']) and ($data_array['write_to_log'] == 1)){
            $service_for_log = str_replace("-", " ", $service);

            if(isset($data_array['product_name_fd'])) {
                $product_name = $data_array['product_name_fd'] . " (" . $service_for_log . ")";
            }
            $this->add_order_activity_log($data_array['username'], '', $product_name,  $data_array['price'], $data_array['proRata']);

            return true;
        }

        return false;


    }


    // ------------------------ end Activity ---------------------------------------------------------------------------




	function get_bills_count($username){
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->where('status','active');
		$query = $this->db->get('orders');
		return $query->num_rows();
	}
	
	function get_all_bills_count(){
		$this->db->select('id');
		$this->db->where('status','active');
		$query = $this->db->get('orders');
		return $query->num_rows();
	}
	
	function get_bills($username, $num = 10, $start = 0){
		$this->db->where('user',$username);
		$this->db->where('status','active');
		$this->db->order_by('date','desc');
		$this->db->limit($num, $start);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	}
	
	function get_bills_by_date($username, $start_date,$end_date,$num = 10, $start = 0){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select * from orders where user ="'.$username.'" and status= "active" and 
									date between "'.$start_date.'" and "'.$end_date.'" limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_bills_count_by_date($username, $start_date, $end_date){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select id from orders where user ="'.$username.'" and status= "active" and 
									date between "'.$start_date.'" and "'.$end_date.'" ');
		return $query->num_rows();
	}
	
	function get_all_bills_by_date($start_date, $end_date, $num = 10, $start = 0){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select * from orders where status= "active" and date between "'.$start_date.'" and "'.$end_date.'" 
									limit '. $start .','.$num.'');
		$result = $query->result_array();
		return $result;
	}
	
	function get_all_bills_count_by_date($start_date, $end_date){
		$star_date = date('Y-m-d',strtotime('-1 day',strtotime($start_date)));
		$end_date = date('Y-m-d',strtotime('+1 day',strtotime($end_date)));
		$query = $this->db->query('select id from orders where status= "active" and date between "'.$start_date.'" and "'.$end_date.'"');
		return $query->num_rows();
	}
	
	function get_all_bills($num = 10, $start = 0){
		$this->db->order_by('date', 'desc');
		$this->db->limit($num, $start);
		$this->db->where('status','active');
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result;
	}

	function get_discount($username) {
		$this->db->select('discount');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$name = $result['discount'];
		return $name;
	}

	function validate() 
	{
	    $this->load->model('crypto_model');

		$this->db->select('role');
        $this->db->select('password');

        $post_username = $this->input->post('username');
        $post_username = strip_tags(mysql_real_escape_string($post_username));
        $post_username = trim($post_username);

        iconv(mb_detect_encoding($post_username, mb_detect_order(), true), "UTF-8", $post_username);

        $post_password = $this->input->post('password');
        $post_password = strip_tags(mysql_real_escape_string($post_password));
        $post_password = trim($post_password);

        iconv(mb_detect_encoding($post_password , mb_detect_order(), true), "UTF-8", $post_password);


		$this->db->where('username', $post_username);
		//$this->db->where('password', $pass);//remove md5

		$query_username = $this->db->get('membership');

		if ($query_username->num_rows == 1) {
			$result = $query_username->row('array');
			$pass = $this->crypto_model->decode($result->password);
			if ($pass == $post_password)
			    return $result->role;
		}else{
			$this->db->select('role');
            $this->db->select('password');
			$this->db->where('email_address',$post_username);
			//$this->db->where('password',$post_password);
			$query_email = $this->db->get('membership');
			
			if($query_email->num_rows == 1) {
				$result = $query_email->row('array');
                $pass = $this->crypto_model->decode($result->password);
                if ($pass == $post_password)
				    return $result->role;
			}else{
				$this->db->select('role');
                $this->db->select('password');
				$this->db->where('ow',$post_username);
				//$this->db->where('password',$post_password);
				$query_ow = $this->db->get('membership');

				if ($query_ow->num_rows == 1){
					$result = $query_ow->row('array');
                    $pass = $this->crypto_model->decode($result->password);
                    if ($pass == $post_password)
                        return $result->role;
				}else{
					return 0;
				}
			}
		}
	}
	
	function get_input_username(){


        $username = $this->input->post('username');
        $username = strip_tags(mysql_real_escape_string($username));
        $username = trim($username);


		$this->db->select('id');
		$this->db->where('username', $username);
		$query_username = $this->db->get('membership');
		if ($query_username->num_rows == 1) {
			return $username;
		}else{
			$this->db->select('username');
			$this->db->where('email_address', $username);
			$query_email = $this->db->get('membership');
			if($query_email->num_rows == 1){
				$result = $query_email->row('array');
				return $result->username;
			}else{
				$this->db->select('username');
				$this->db->where('ow', $username);
				$query_ow = $this->db->get('membership');
				if($query_ow->num_rows == 1){
					$result = $query_ow->row('array');
					return $result->username;
				}else{
					return 0;
				}
			}
		}
	}
	
	function get_user_status()
	{

        $post_username = $this->input->post('username');
        $post_username = strip_tags(mysql_real_escape_string($post_username));
        $post_username = trim($post_username);

        $post_password = $this->input->post('password');
        $post_password = strip_tags(mysql_real_escape_string($post_password));
        $post_password = trim($post_password);



		$this->db->select('status');
		$this->db->where('username', $post_username);
		$this->db->where('password', $post_password);//remove md5
		$query = $this->db->get('membership');
		if ($query->num_rows == 1) {
			$result = $query->row('array');
			return $result->status;
		} else {
			return 0;
		}
	}
	
	function validate_email($email){
		$this->db->select('role');
		$this->db->where('email_address',$email);
		$query = $this->db->get('membership');
		if($query->num_rows >= 1){
			$result = $query->row();
			return $result->role;
		}else{
			return 0;
		}
	}

    function validate_email_back_id($email){
        $this->db->select('id');
        $this->db->where('email_address',$email);
        $query = $this->db->get('membership');
        if($query->num_rows >= 1){
            $result = $query->row();
            return $result;
        }else{
            return 0;
        }
    }


    function validate_billing_email($email){
        $this->db->select('id');
        $this->db->where('email',$email);
        $query = $this->db->get('billing');
        if($query->num_rows >= 1){
            $result = $query->row();
            return $result->id;
        }else{
            return 0;
        }
    }
	
	function validate_username($username){
		$this->db->select('role');
		$this->db->where('username',$username);
		$query = $this->db->get('membership');
		if($query->num_rows == 1){
			$result = $query->row();
			return $result->role;
		}else{
			return 0;
		}
	}

    function validate_sa_id_number($sa_id_number){
        $this->db->select('sa_id_number');
        $this->db->where('sa_id_number',$sa_id_number);
        $query = $this->db->get('billing');
        if($query->num_rows == 1){
            $result = $query->row();
            return $result->sa_id_number;
        }else{
            return 0;
        }
    }

	function create_member() {


        $post_first_name  = $this->input->post('first_name');
        $post_first_name  = strip_tags(mysql_real_escape_string($post_first_name));
        $post_first_name  = trim($post_first_name);
        // ------------------------------------------------
        $post_last_name   = $this->input->post('last_name');
        $post_last_name   = strip_tags(mysql_real_escape_string($post_last_name));
        $post_last_name   = trim($post_last_name);
        // ------------------------------------------------
        $post_email_address = $this->input->post('email_address');
        $post_email_address = strip_tags(mysql_real_escape_string($post_email_address));
        $post_email_address = trim($post_email_address);
        // ------------------------------------------------
        $post_username = $this->input->post('username');
        $post_username = strip_tags(mysql_real_escape_string($post_username));
        $post_username = trim($post_username);
        // ------------------------------------------------
        $post_password = $this->input->post('password');
        $post_password = strip_tags(mysql_real_escape_string($post_password));
        $post_password = trim($post_password);
        // ------------------------------------------------
            // strip_tags(mysql_real_escape_string(


		$new_member_insert_data = array (
			'first_name' => $post_first_name,
			'last_name' =>  $post_last_name,
			'email_address' => $post_email_address,
			'username' => $post_username,
			'password' => $post_password,//remove md5
		);
		$insert = $this->db->insert(
			'membership', $new_member_insert_data
		);
		return $insert;
	}


	function get_user_name($user_id){
		$this->db->select('username');
		$this->db->where('id',$user_id);
		$query = $this->db->get('membership');
		if($query->num_rows == 1){
			$result = $query->row();
			return $result->username;
		}else{
			return 0;
		}
		
	}
	
	function get_user_email($account_id){
		$this->db->select('email_address');
		$this->db->where('id', $account_id);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		if($result){
			$email = $result['email_address'];
		}else{
			$email = '';
		}
		return $email;
	}

    function get_billing_email($account_id){
        $this->db->select('email');
        $this->db->where('id_user', $account_id);
        $query = $this->db->get('billing');
        $result = $query->first_row('array');
        if($result){
            $email = $result['email_address'];
        }else{
            $email = '';
        }
        return $email;
    }
	
	function delete_user($account_id){
		//delete user=> delete billing info, order info, invoice info...
		$username = $this->get_user_name($account_id);
		$this->db->delete('membership', array('id' => $account_id));
		$this->db->delete('billing', array('username' => $username));	
		$this->db->delete('orders', array('user' => $username));
		$this->db->delete('invoices', array('user_id' => $account_id));
		$this->db->delete('invoice_pdf', array('user_name' => $username));
		$this->db->delete('ow', array('id_user' => $account_id));
		/* $data = array(
			'status' => 'deleted'		
		);
		$result = $this->db->update('membership',$data,array('id' => $account_id));
		if($result){
			return true;
		}else{
			return false;
		} */
		return true;
	}
	
	function get_user_id($username){
		$this->db->select('id');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$id = $result['id'];
		return $id;
	}
	
	function get_password($username)
	{
		$this->db->select('password');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result_user = $query->first_row('array');	

		$this->db->select('password');
		$this->db->where('email_address', $username);
		$query = $this->db->get('membership');
		$result_email = $query->first_row('array');
		
		$this->db->select('password');
		$this->db->where('ow', $username);
		$query = $this->db->get('membership');
		$result_ow = $query->first_row('array');
		
		if($result_user || $result_email || $result_ow){
			if($result_user){
				$pwd = $result_user['password'];
			}elseif ($result_email){
				$pwd = $result_email['password'];
			}elseif($result_ow){
				$pwd = $result_ow['password'];
			}
			return $pwd;
		}else{
			return false;
		}	
	}

	function get_login_info($username){
		$this->db->where('username',$username);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get('use_log');
		$result = $query->result_array();

		if($result && $query->num_rows() > 1){
			return $result[1];
		}else{
			return $result[0];
		}
	}
	
	function search_by_username($username, $num = 10, $start = 0){
		$this->db->where('role', 'client');
		$this->db->like('username', $username);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function search_by_id($account_id)
	{
		$this->db->where('id', $account_id);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		return $result;
	}

    // identification number
    function search_by_sa_id($sa_id)
    {

        $this->db->select();
        $this->db->from('billing');
        $this->db->where('sa_id_number', $sa_id);
        $this->db->join('membership','membership.id = billing.id_user','left');

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }
	
	function search_by_firstname($firstname, $num = 10, $start = 0){
		$this->db->where('role', 'client');
		$this->db->like('first_name', $firstname);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function search_by_lastname($lastname, $num = 10, $start = 0)
	{
		$this->db->where('role', 'client');
		$this->db->like('last_name', $lastname);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function search_by_fullname($firstname, $lastname, $num = 10, $start = 0)
	{
		$this->db->where('role', 'client');
		$this->db->like('first_name', $firstname);
		$this->db->like('last_name', $lastname);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function search_by_ow($ow, $num = 10, $start = 0)
	{
	   

        // ~~~ parse row
        $ow = strtoupper($ow);
        $sub_ow = substr($ow,0,2);

        if ($sub_ow != 'OW'){

            $ow = "OW" . $ow;
        }

        // ----------------------------------
		//$this->db->where('role', 'client');
		$this->db->where('ow', $ow);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
		   
			return $result;
		}else{
			return false;
		}
	}
	
	function search_by_role($role, $num = 5, $start = 0){
		$this->db->where('role', $role);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}


    function return_priority_flag_for_user_search($array_of_params){

        $flag = 'all';
        $message = 'It seems that there is no user with these data. ';

        if(isset($array_of_params['sa_id_number']) && !empty($array_of_params['sa_id_number']) ){

            $flag = 'sa_id';
            $message = 'It seems that there is no user with this sa_id. ';

        }elseif(isset($array_of_params['user_id']) && !empty($array_of_params['user_id']) ){

            $flag = 'user_id';
            $message = 'It seems that there is no user with this user_id. ';

        }elseif (isset($array_of_params['ow_num']) && !empty($array_of_params['ow_num']) ){

            $flag = 'ow_num';
            $message = 'It seems that there is no user with this ow_number. ';
        }


        $return_array = array(

             'flag' => $flag,
             'message' => $message,
        );


        if ($flag == 'all') {

            $additional_fields = array();
            $additional_row = "";

            if (isset($array_of_params['user_name']) && !empty($array_of_params['user_name']))
                $additional_fields[] = "username";

            if (isset($array_of_params['first_name']) && !empty($array_of_params['first_name']))
                $additional_fields[] = "first name";

            if (isset($array_of_params['last_name']) && !empty($array_of_params['last_name']))
                $additional_fields[] = "last name";

            if (isset($array_of_params['email_address']) && !empty($array_of_params['email_address']))
               $additional_fields[] = "email address";

            for ($i = 0 ; $i < count($additional_fields); $i++){

                $additional_row .= ", " . $additional_fields[$i];
            }

            $row_count = strlen($additional_row);
            $additional_row = substr($additional_row, 2, $row_count - 2);
            $additional_row = " (" . $additional_row . ")";
            $message .= $additional_row;
            $return_array['message'] = $message;
        }

        return $return_array;

    }


    function search_users_by_array_of_params($array_of_params, $num_per_page = 5, $start = 0){
     // check get_account_total_count_by_array_of_params($array_of_params) if you make any changes here

        if(isset($array_of_params['sa_id_number']) && !empty($array_of_params['sa_id_number']) ){

            $account_data = $this->search_by_sa_id($array_of_params['sa_id_number']);
        }elseif(isset($array_of_params['user_id']) && !empty($array_of_params['user_id']) ){

            $account_data = $this->search_by_id($array_of_params['user_id']);
        }elseif (isset($array_of_params['ow_num']) && !empty($array_of_params['ow_num']) ){

            $account_data = $this->search_by_ow($array_of_params['ow_num']);
        } else {

            /*
            $search_keys = array(

                'user_name'     => '',
                'first_name'    => '',
                'last_name'     => '',
                'email_address' => '',
                'ow_num'        => '',
                'user_id'       => '',
            );
            */
            /*
            if (isset($array_of_params['user_name']) && !empty($array_of_params['user_name']) )
                 $this->db->or_where('username', $array_of_params['user_name']);

            if (isset($array_of_params['first_name']) && !empty($array_of_params['first_name']) )
                $this->db->or_where('first_name', $array_of_params['first_name']);

            if (isset($array_of_params['last_name']) && !empty($array_of_params['last_name']) )
                $this->db->or_where('last_name', $array_of_params['last_name']);

            if (isset($array_of_params['email_address']) && !empty($array_of_params['email_address']) )
                $this->db->or_where('email_address', $array_of_params['email_address']);
            */


            // ~~~~~~~~~~~~~~~~~~~ multiple search ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            if (isset($array_of_params['user_name']) && !empty($array_of_params['user_name']) )
                $this->db->like('username', $array_of_params['user_name']);

            if (isset($array_of_params['first_name']) && !empty($array_of_params['first_name']) )
                $this->db->like('first_name', $array_of_params['first_name']);

            if (isset($array_of_params['last_name']) && !empty($array_of_params['last_name']) )
                $this->db->like('last_name', $array_of_params['last_name']);

            if (isset($array_of_params['email_address']) && !empty($array_of_params['email_address']) )
                $this->db->like('email_address', $array_of_params['email_address']);


            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            $this->db->limit($num_per_page, $start);
            $query = $this->db->get('membership');
            $result = $query->result_array();
            if($result){
                $account_data = $result;
            }else{
                $account_data =  false;
            }

        }


        return $account_data;
    }


    function get_account_total_count_by_array_of_params($array_of_params){


        if(isset($array_of_params['sa_id_number']) && !empty($array_of_params['sa_id_number']) ){
            $account_data = $this->search_by_sa_id($array_of_params['sa_id_number']);
            return count($account_data);

        }elseif(isset($array_of_params['user_id']) && !empty($array_of_params['user_id']) ){
            $account_data = $this->search_by_id($array_of_params['user_id']);
            return count($account_data);

        }elseif (isset($array_of_params['ow_num']) && !empty($array_of_params['ow_num']) ){
            $account_data = $this->search_by_ow($array_of_params['ow_num']);
            return count($account_data);

        } else {

            $this->db->select('id');
            $this->db->where('status !=', 'deleted');
            $this->db->where('role !=', 'super_administrator');
            $this->db->where('role !=', 'admin');

            if (isset($array_of_params['user_name']) && !empty($array_of_params['user_name']))
                $this->db->like('username', $array_of_params['user_name']);

            if (isset($array_of_params['first_name']) && !empty($array_of_params['first_name']))
                $this->db->like('first_name', $array_of_params['first_name']);

            if (isset($array_of_params['last_name']) && !empty($array_of_params['last_name']))
                $this->db->like('last_name', $array_of_params['last_name']);

            if (isset($array_of_params['email_address']) && !empty($array_of_params['email_address']))
                $this->db->like('email_address', $array_of_params['email_address']);
        }

        $this->db->from('membership');
        $query = $this->db->get();
        return $query->num_rows();
    }



	
	function get_role_count($role){
		$this->db->select('id');
		$this->db->where('role', $role);
		$query = $this->db->get('membership');
		return $query->num_rows();
	}
	
	function search_by_status($status, $num = 10, $start = 0){
		$this->db->where('status', $status);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_stauts_count($status){
		$this->db->select('id');
		$this->db->where('status', $status);
		$query = $this->db->get('membership');
		return $query->num_rows();
	}
	
	function search_by_role_status($role ,$status, $num = 10, $start = 0){
		$this->db->where('status', $status);
		$this->db->where('role', $role);
		$this->db->limit($num, $start);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_role_status_count($role, $status){
		$this->db->select('id');
		$this->db->where('status', $status);
		$this->db->where('role', $role);
		$query = $this->db->get('membership');
		return $query->num_rows();
	}
	
	function get_role_data(){
		$query = $this->db->query('select distinct role from membership where role != "super_administrator" and role != "admin"');
		$result = $query->result_array();
		return $result;
	}
	
	function get_status_data(){
		$query = $this->db->query('select distinct status from membership where status != "deleted"');
		$result = $query->result_array();
		return $result;
	}
	
	function get_admin_list(){
		$this->db->where('role', 'admin');
		$this->db->where('status !=', 'deleted');
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_admin_data($admin_id){
		$this->db->where('id', $admin_id);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_user_data($user_id)
	{
		$this->db->where('id', $user_id);
		$query = $this->db->get('membership');
		$result = $query->result_array();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
	
	function get_billing_data($user_id)
	{
		$this->db->where('id_user', $user_id);
		$query = $this->db->get('billing');
		$result = $query->result_array();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
	
	function create_OW($id_user)
	{
		$this->db->insert('ow', array('id_user' => $id_user));
		return $this->db->insert_id();
	}
	
	function get_ow($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		return $result['ow'];
	}
	
	function get_subscribed_user()
	{
		$this->db->where('subscribe', 1);
		$this->db->where('status', 'active');
		$query = $this->db->get('membership');
		$result = $query->result_array();
		return $result;
	}

    function get_admin_limitation_by_id($user_id){

        $this->db->select();
        $this->db->from('admin_limitation');
        $this->db->where('admin_id',$user_id);
        $query = $this->db->get();

        $lim_level = 0;
        $result_row = $query->row_array();
        if (!empty($result_row)){
            $lim_level = $result_row['limitation_level'];
        }

        return $lim_level;

    }

    /**
     * get email and number for order activation
     *
     * @param string $username
     * @return array
     */
    function get_email_with_number($username){

        $membership_mobile = $this->get_user_mobile($username); // get mobile from account info
        $number = $this->get_number($username);//get mobile number from billing
        $email = $this->get_email($username);//get email address from membership


        $real_mobile_number = $membership_mobile;
        if (empty($real_mobile_number))
            $real_mobile_number = $number;

        return array('email' => $email, 'number' => $real_mobile_number);

    }

    function getLoyaltyData($user_id) {

        $this->db->select('avios_id, br_a_id');
        $query = $this->db->get_where('membership', array('id' => $user_id));

        foreach ($query->result() as $row) {

            $avios_id = $row->avios_id;
            $br_a_id = $row->br_a_id;
        }

        if(!empty($avios_id)) {
            $data = [$avios_id, "AVIOS"];
            return $data;
        }

        if(!empty($br_a_id)) {
            $data = [$br_a_id, "BAEC"];
            return $data;
        }

        return false;
    }

    function storeTokenForUser($username, $token) {

        $data = ["login_token" => $token];
        $this->db->where('username', $username);
        $this->db->update('membership', $data);
    }

    function getLoginToken($username) {

        $this->db->select('login_token');
        $this->db->select('role');
        $this->db->where('username', $username);
        $q = $this->db->get('membership');
        $res = $q->result_array();

        return $res;
    }

    function readNotification($ids) {


        $this->db->where_in('id', $ids);
        $this->db->update('notifications', ['new' => 0]);

        return "ok";
    }

    function add_mobile($username, $mobile) {
        $this->db->where('username', $username);
        $this->db->update('membership', ['mobile_number' => $mobile]);
    }

    function has_daily_lte_usage($username) {
        $this->db->where('username', $username);
        $this->db->select('daily_lte_usage');
        $query = $this->db->get('membership');
        $res = $query->result_array();

        if (!empty($res)) {
            return $res[0]['daily_lte_usage'] == '1';
        } else {
            return false;
        }
    }

    public function get_global_oauth() {
        $setting = $this->db->where('action', 'oauth')->get('system_param')->result_array();

        return $setting[0]['toggle'];
    }
}
?>