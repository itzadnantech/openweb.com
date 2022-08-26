<?php
class Order_model extends CI_Model {
	
	function email_activation($email, $product_name, $username, $realm, $password, $user_name)
	{
		$this->db->select('from, subject, content');
		$this->db->where('purpose', 'activation');
		$query = $this->db->get('email_details');
		$result = $query->result_array();
		if (!empty($result)) {
			$result = $result[0];
			$from = $result['from'];
			$subject = $result['subject'];
			$content = $result['content'];
	
			$content = str_replace('[name_surname]', $user_name, $content);
			$content = str_replace('[username]', "$username@$realm", $content);
			$content = str_replace('[password]', $password, $content);
	
			$this->load->library('email');
			$this->email->from($from, 'OpenWeb');
			$this->email->to($email);
	
			$this->email->subject($subject);
	
			$this->email->message($content);
			$this->email->send();
		}
	}
	
	function get_order_data($order_id)
	{
		$this->db->where('id', $order_id);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
	
	function email_invoices_individual($username, $pdf_id, $acc_username)
	{	
		$this->load->library('email');
		$this->db->where('id',$pdf_id);
		$this->db->from('invoice_pdf');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$pdf_name = $row->name;
			$path = $row->path;
			$user = $row->user_name;
		}

		if(isset($username)){
			$this->db->select('first_name,email_address');
			$this->db->where('username', $username);
			$query = $this->db->get('membership');
			$result = $query->first_row('array');
			
			$this->db->select('account_username, account_password');
			$this->db->where('user', $username);
			$query_2 = $this->db->get('orders');
			$result_2 = $query_2->first_row('array');
			$acc_password = $result_2['account_password'];
			
			$email = $result['email_address'];
			$name = $result['first_name'];
			$date = date('F o', strtotime('now'));
			$link_date = strtolower(date('F-o', strtotime('now')));
			$link = "http://home.openweb.co.za/user/invoices/$username/$link_date";
			/* Your ADSL product has been successfully created.
			Username: $acc_username
			Password: $acc_password */
			$msg = "Dear $name,
Please visit the following link for your invoice dated $date.

$link

If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";			
			
			$this->email->from('admin@openweb.com', 'OpenWeb Home');
			$this->email->to($email);
			$this->email->subject("New Order Invoice for $date");
			$this->email->message($msg);
			$this->email->attach($path);
			$this->email->send();			
		}
	}
	
	function set_activated($order_id) {
		$data = array(
			'status' => 'active',
		);
	
		$this->db->where('id', $order_id);
		$this->db->update('orders', $data);
	}

    function email_ceo_product($user, $product_name, $payment_method)
    {

        $this->db->where('username', $user);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');


        $firstname = $result['first_name'];
        $lastname = $result['last_name'];
        $email = $result['email_address'];
        $ow = $result['ow'];



        if($payment_method == 'credit_card_auto'){
            $payment_method = 'Auto Billing using your Credit Card';
        }elseif($payment_method == 'credit_card'){
            $payment_method = 'Once off payment from your Credit Card';
        }elseif($payment_method == 'debit_order'){
            $payment_method = 'Debit Order';
        }elseif($payment_method == 'eft'){
            $payment_method = 'EFT';
        }

        $content = "
First Name and Last Name: $firstname  $lastname
Email Address: $email
OW Number: $ow
Product Purchased: $product_name
Payment Method: $payment_method";

        $this->load->library('email');
        $this->email->from('noreply@openweb.co.za', 'OpenWeb');
        $this->email->to('ceo@openweb.co.za');
        $this->email->subject('New Product Created');
        $this->email->message($content);
        $this->email->send();
    }


    // ex get_is_detail
    function get_realm_data_by_order_id($order_id, $class = null) {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // get realm by id
        $this->db->select('realm');
        $this->db->where('id', $order_id);
        $query = $this->db->get('orders');
        $result_realm = $query->first_row('array');

        if (empty($result_realm))
            return false;

        $realm_name = $result_realm['realm'];


        $detail_array = false;
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // echo "<br/> realm name : $realm_name";

        if ($realm_name != null){

            // echo "<br/> realm exist";
            //                     relm exist
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            $this->db->select('user, pass');
            $this->db->where('realm', $realm_name);
            $query2 = $this->db->get('realms');
            $result_detail = $query2->result_array();
            $user = $result_detail[0]['user'];
            $password = $result_detail[0]['pass'];

            $detail_array = array (
                'user' => $user . "@" . $realm_name,
                'pass' => $password,
                'realm' => $realm_name,
            );

            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        } else {

            //  echo "<br/> realm not exist";
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            // get product by order id
            $order_array = $this->get_order_data($order_id);
            if (empty($order_array))
                return false;  // order data is not exist


            //  echo "<br/>order data exist";

            $product_id = $order_array['product'];
            if (!isset($this->product_data))
                $this->load->model('admin/product_model');


            // get class_id by product
            $class_id = $this->product_model->get_product_class_id($product_id);


            // if class id is  exist
            if ($class_id != null){

                // echo "<br/> class id exist";
                // get detail by class
                $detail_array = $this->get_is_details_by_id($class_id);

            } else {

                //  echo "<br/>class id not exist";

                // ##try old IS DETAIL##
                if ($class != null){

                    //   echo "<br/>class is set";
                    $detail_array = $this->get_is_details($class);
                }
            }

        } // realm name null (end)

        return $detail_array;
    }

    function get_order_realm($order_id, $class = null){

            $this->db->select('realm');
            $this->db->where('id', $order_id);
            $query = $this->db->get('orders');
            $result_realm = $query->first_row('array');

            if (empty($result_realm))
                return false;

            $realm_name = $result_realm['realm'];
            $detail_array = false;

            if ($realm_name == null){


                // get product by order id
                $order_array = $this->get_order_data($order_id);

                if (empty($order_array))
                    return false;  // order data is not exist

                $product_id = $order_array['product'];

                if (!isset($this->product_data))
                    $this->load->model('admin/product_model');


                // get class_id by product

                $class_id = $this->product_model->get_product_class_id($product_id);


                // if class id is  exist
                if ($class_id != null){

                    // echo "<br/> class id exist";
                    // get detail by class
                    $detail_array = $this->get_is_details_by_id($class_id);
                    $realm_name = $detail_array['realm'];


                } else {


                    // ##try old IS DETAIL##
                    if ($class != null){

                        $detail_array = $this->get_is_details($class);
                        $realm_name  = $detail_array['realm'];
                    }
                }

            } // realm name null (end)

            return $realm_name;


    }

    function get_is_details($product_class) {
        // From here we will get the class's realm details!
        $this->db->select('realm');
        $this->db->where('id', $product_class);
        $this->db->limit(1);
        $query = $this->db->get('is_classes');
        $result = $query->result_array();
        $realm = $result[0]['realm'];

        if ($realm) {
            // Now we get realm user and password
            $this->db->select('user, pass');
            $this->db->where('realm', $realm);
            $query = $this->db->get('realms');
            $result = $query->result_array();
            $user = $result[0]['user'];
            $password = $result[0]['pass'];

            $data = array (
                'user' => "$user@$realm",
                'pass' => $password,
                'realm' => $realm,
            );
            return $data;
        }
        return false;
    }


    function get_is_class($order_id) {
        // First get product ID
        $this->db->select('product, billing_cycle');
        $this->db->where('id', $order_id);
        $this->db->limit(1);
        $query = $this->db->get('orders');
        $result = $query->result_array();
        $product_id = $result[0]['product'];
        $billing_cycle = $result[0]['billing_cycle'];

        if($billing_cycle == 'Once-Off'){
            $this->db->select('id');
            $this->db->where('desc', 'ISDSL No Service');
            $this->db->limit(1);
            $query = $this->db->get('is_classes');
            $result = $query->result_array();
            $product_class = $result[0]['id'];
        }else{
            // Then get product class
            $this->db->select('class');
            $this->db->where('id', $product_id);
            $this->db->limit(1);
            $query = $this->db->get('products');
            $result = $query->result_array();
            $product_class = $result[0]['class'];
        }
        return $product_class;
    }

    function get_is_details_by_id($product_class_id) {


        // From here we will get the class's realm details!
        $this->db->select('realm');
        $this->db->where('table_id', $product_class_id);
        $this->db->limit(1);
        $query = $this->db->get('is_classes');
        $result = $query->result_array();

        if ($result) {
            $realm = $result[0]['realm'];
            // Now we get realm user and password
            $this->db->select('user, pass');
            $this->db->where('realm', $realm);
            $query = $this->db->get('realms');
            $result = $query->result_array();
            $user = $result[0]['user'];
            $password = $result[0]['pass'];

            $data = array (
                'user' => $user . "@" . $realm,
                'pass' => $password,
                'realm' => $realm,
            );
            return $data;
        }
        return false;
    }

    function delete_order($order_id) {
        // First remove ISDSL account
        //$this->load->model('admin/is_classes');


        $class = $this->get_is_class($order_id);//get class name form the products table


        // $realm_data = $this->get_is_details($class);
        $realm_data = $this->get_realm_data_by_order_id($order_id, $class);

        $account_data = $this->get_order_data($order_id); //order data
        //$order_user = $account_data['user'];
        $order_acc_user = $account_data['account_username'];//username
        $order_acc_pwd  = $account_data['account_password'];

        $this->db->select('id');
        $this->db->where('id !=', $order_id);
        $this->db->where('account_username', $order_acc_user);
        $this->db->where('account_password', $order_acc_pwd);
        $query = $this->db->get('orders');

        if($query->result_array()){
            $result = $query->result_array();
            $change_service = $result[0]['id'];
            $this->db->delete('orders', array('id' => $change_service));
        }

        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $realm = $realm_data['realm'];
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);


        //echo "Delete: $sess, $order_acc_user";
        if($account_data['billing_cycle']!='Daily'){
            $resp = $this->is_classes->delete_account_new($sess, "$order_acc_user@$realm");


        }

        //var_dump('resp----'.$resp);die();//echo null
        //if($resp == 1){
        // Now remove order
        $this->db->where('id', $order_id);
        //if($account_data['billing_cycle']!='Daily'){
        $this->db->update('orders', array(
                'status' => 'pending cancellation',
                'date_cancelled' => date('Y-m-d H:i:s',strtotime('now')),
                'date_revoke' => '',
                'modify_service' => '',
            )
        );


        //	}
        /* 	else{
                $this->db->update('orders', array(
                        'status' => 'pending',
                        'date_cancelled' => date('Y-m-d H:i:s',strtotime('now')),
                        'date_revoke' => '',
                        'modify_service' => '',
                        'product' => 0,
                        'price' => 0,
                        'account_comment' =>'',
                        //'date' => date("Y-m-d H:i:s",strtotime('now')),
                        )
                    );
            } */
        //}else{
        //	die('Failed to delete account from the api.');
        //}

    }

    function revoke_order($order_id){
        $class = $this->get_is_class($order_id);
        //$realm_data = $this->get_is_details($class);
        $realm_data = $this->get_realm_data_by_order_id($order_id, $class);
        $account_data = $this->order_model->get_order_data($order_id);

        $order_acc_user = $account_data['account_username'];
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $realm = $realm_data['realm'];
        $acc_username = $order_acc_user.'@'.$realm;
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);
        if($account_data['billing_cycle']!='Daily')
            $resp = $this->is_classes->restore_account_new($sess, $acc_username);
        //var_dump('rep---->'.$resp);  die; //echo null
        //if($resp == 1){
        $revoke_date = date('Y-m-d H:i:s',strtotime('now'));
        //update the order status in db
        $this->db->where('id', $order_id);
        $this->db->update('orders', array(
                'status' => 'active',
                'date_revoke' => $revoke_date,
                'date_cancelled' => NULL,
            )
        );
        return $revoke_date;
        //}else{
        //	die('Failed to revoke account from the api.');
        //}
    }

    function get_last_order_by_username($username){

        if (empty($username))
            return false;

        $this->db->select('');
        $this->db->where('user', $username);
        $this->db->where('type', 'auto');
        $this->db->order_by('id','desc');

        $query = $this->db->get('orders');
        $result = $query->first_row('array');

        if (!empty($result)){

            return $result;
        }
        return false;
    }


    function get_last_payfast_topup_order_by_username($username){

        if (empty($username))
            return false;

        $this->db->select('');
        $this->db->where('username', $username);
        $this->db->where('payment_method', 'credit_card');
        $this->db->where('payment_status', 'completed');
        $this->db->order_by('id','desc');

        $query = $this->db->get('topup_orders');
        $result = $query->first_row('array');

        if (!empty($result)){

            return $result;
        }
        return false;
    }


    function check_order_by_username_realm($user_name, $realm){

        $answer = false;

        $this->db->select();
        $this->db->where('account_username', $user_name);
        $this->db->where('realm', $realm);

        $query = $this->db->get('orders');
        $result = $query->first_row('array');

        if (!empty($result))
            $answer  =  true;

        return $answer;
    }


    /* SMS sending */
    function send_sms($number, $content) {
        $data= array(
            "Type"=> "sendparam",
            "Username" => "keoma",
            "Password" => "maniac20",
            "live" => "true",
            "numto" => $number,
            "data1" => $content,
        ) ;
        $data = http_build_query($data);
        return $this->do_post_request('http://www.mymobileapi.com/api5/http5.aspx', $data);
    }

    function do_post_request($url, $data, $optional_headers = null) {
        $params = array('http' => array(
            'method' => 'POST',
            'content' => $data
        ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }
        $response;
        return $this->formatXmlString($response);
    }

    function formatXmlString($xml) {
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);//"262168553True"==>" 262168553 True"
        $token      = strtok($xml, "\n");//" 262168553 True"==>" 262168553 True "
        $result     = ''; // holds formatted version as it is built
        $pad        = 0; // initial indent
        $matches    = array(); // returns from preg_matches()
        while ($token !== false) :
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
                $indent=0;
            elseif (preg_match('/^<\/\w/', $token, $matches)) :
                $pad--;
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
                $indent=1;
            else :
                $indent = 0;
            endif;
            $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines
        endwhile;
        return $result;
    }

    function is_fibre_order($order_id){

        $this->db->select('service_type');
        $this->db->where('id', $order_id);

        $query = $this->db->get('orders');
        $result = $query->first_row('array');

        $service_type = $result['service_type'];
        if ( ($service_type == 'fibre-line' ) || ($service_type == 'fibre-data' )  )
            return true;

        return false;

    }

    function get_fibre_data_by_order($order_id, $user_id = null){

        if (empty($order_id))
            return false;

        $this->db->where('order_id', $order_id);
        if (!empty($user_id))
            $this->db->where('user_id', $user_id);


        $query = $this->db->get('fibre_orders');
        // $result = $query->result_array();
        $result = $query->first_row('array');

        return $result;
    }

	function add_manual_order($data) {

        $this->db->insert('orders_on_email', $data);
        return $this->db->insert_id();
    }

    function get_all_orders_by_service_type($type) {
        $this->db->where('service_type', $type);
        $query = $this->db->get('orders');
        return $query->result_array();
    }

    public function user_percentage_sum($stats_data, $order_id, $month_usage)
    {
        $data = $this->db
            ->select('total_data, percentage')
            ->where('order_id', $order_id)
            ->get('fibre_orders')
            ->result();

        foreach ($stats_data as &$stats) {

            if($stats['Title'] != 'Main') {
                continue;
            }

            if ($data[0]->total_data != '' && $data[0]->total_data != 0) {
                $stats['Total Data'] = $data[0]->total_data;

                if ($stats['Data Units'] == 'MB') {
                    $stats['Total Data'] = $stats['Total Data'] * 1024;
                }
            }

            if ($stats['Remaining Data'] > 0) {
                $stats['Remaining Data'] = $stats['Total Data'] - $month_usage;
            }
        }

        return $stats_data;
    }
}