<?php
class Product_model extends CI_Model {

	function get_product_data_list(){
		$query = $this->db->get('products');
		$product_data = $query->result_array();
		return $product_data;
	}

    function get_active_product_list(){
        $query = $this->db->get_where('products', ['status' => 'active', 'parent !=' => 'legacy']);
        $product_data = $query->result_array();
        return $product_data;
    }

    function get_another_product_data($product_id){
		$query = $this->db->query('select * from products where status="active" and parent !="legacy" and id <>'.$product_id);
		$product_data = $query->result_array();
		return $product_data;
	}
	
	function get_product_data($product) {
		$this->db->where('id', $product);
		$query = $this->db->get('products');
		$product_data = $query->first_row('array');
		return $product_data;
	}


    function get_product_data_for_port_reset($product) {
        $this->db->where('id', $product);
        $query = $this->db->get('products');
        $product_data = $query->first_row('array');
        return $product_data;
    }

    function get_product_class_id($product_id){

        $this->db->select('class_id');
        $this->db->where('id',$product_id);
        $query = $this->db->get('products');

        $class_id = $query->first_row('array');
        $result = false;
        if (!empty($class_id))
            $result = $class_id['class_id'];

        return $result;

    }

	function get_payment_methods($product_id, $billing_cycle = null)
    {
        $this->db->select('payment_method');
        $this->db->where('product_id', $product_id);
        if ($billing_cycle != null) {

                 $this->db->where('billing_cycle', $billing_cycle);
        } else {
            $this->db->where('billing_cycle', 'monthly');

        }


		$query = $this->db->get('product_payment_methods');
		if($query->result_array()){
			return $result = $query->result_array();
		}else{
			return $result = '';
		}
	}


    function get_full_payment_methods($product_id){
        $this->db->select('payment_method, billing_cycle');
        $this->db->where('product_id', $product_id);


        $query = $this->db->get('product_payment_methods');
        $result_array = $query->result_array();
        if(!empty($result_array)){

            $final_array = array();
            foreach ( $result_array as $row ){

                $final_array[$row['payment_method']][$row['billing_cycle']] = '1';

            }

            return $final_array;

            //  return $result = $query->result_array();

        }else{
            return $result = '';
        }
    }




	function get_product_price($product_id) {
		$this->db->select('price');
		$this->db->where('id', $product_id);
		$query = $this->db->get('products');
		$result = $query->first_row('array');
		return $result['price'];
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

	function get_is_auto($product_id) {
		$this->db->select('automatic_creation');
		$this->db->where('id', $product_id);
		$query = $this->db->get('products');
		$result = $query->first_row('array');
		
		if ($result['automatic_creation'] == 1) {
			return true;
		}
		return false;
	}

	function get_pro_rate_price($pro_rata_option, $price) {
		$price_out = $price;
		$day_of_month = date('j');

		if ($pro_rata_option == 'standard') {
			// if 1-10, full, if 11- 15 30%, if 16-25 50%, if 26-end free/95%
			if ($day_of_month > 0 && $day_of_month < 11) {
				return $price;
			} else if ($day_of_month >= 11 && $day_of_month <= 15) {
					$price_out = round(($price / 100) * 70, 2);
				return $price_out;
			} else if ($day_of_month >= 15 && $day_of_month <= 25) {
				$price_out = round(($price / 100) * 50, 2);
				return $price_out;
			} else {
				$price_out = 0.00;
				return $price_out;
			}
		} else if ($pro_rata_option == 'basic') {
			// 11-end 50%
			if ($day_of_month > 0 && $day_of_month < 11) {
				return $price;
			} else {
				$price_out = round(($price / 100) * 50, 2);
				return $price_out;
			}
		} else if ($pro_rata_option == 'basic2') {
			// 11-end Free
			if ($day_of_month > 0 && $day_of_month < 11) {
				return $price;
			} else {
				//$price_out = round(($price / 100) * 50, 2);
                $price_out = 0.00;
				return $price_out;
			}
		} else if ($pro_rata_option == 'none') {
			return 0.00;
		} else if ($pro_rata_option == 'full') {
			return $price;
		}

		return $price_out;
	}

	function get_pro_rata_option($product_id) {
		$this->db->select('pro_rata_option');
		$this->db->where('id', $product_id);
		$query = $this->db->get('products');
		$result = $query->first_row('array');
		if ($result){
			return $result['pro_rata_option'];
		}else{
			return null;
		}
	}

	function get_product_name ($product_id) {
		$this->db->select('name');
		$this->db->where('id', $product_id);
		$query = $this->db->get('products');
		$result = $query->result_array();
		
		if($result){
			$name = $result[0]['name'];
			return $name;
		}else{
			return null;
		}
	}

	// Get's product class from Product ID
    /*
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
	} */

    function get_is_class($product_id) {
        $this->db->select('class');
        $this->db->where('id', $product_id);
        $this->db->limit(1);
        $query = $this->db->get('products');
        $result = $query->result_array();
        $product_class = $result[0]['class'];

        return $product_class;
    }


    function get_class_by_billing_cycle($billing_cycle, $product_id){

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

    function get_class_by_product_id($product_id){

            // Then get product class
            $this->db->select('class');
            $this->db->where('id', $product_id);
            $this->db->limit(1);
            $query = $this->db->get('products');
            $result = $query->result_array();
            $product_class = $result[0]['class'];

        return $product_class;


    }


    function get_classes_data($class_id){
        $this->db->where('table_id', $class_id);
        $query = $this->db->get('is_classes');
        //$product_settings = $query->first_row('array');
        //$data = array( 'product_settings' => $product_settings,);
        return $query->result_array();
    }
	
	function get_classes($class) {
		$this->db->select('id, desc, realm,table_id');
		$this->db->where('id',$class);
		$query = $this->db->get('is_classes');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return null;
		}
	}

    /*
	function get_product_realm($product_id) {
		$this->db->select('class');
		$this->db->where('id', $product_id);
		$this->db->limit(1);
		$query = $this->db->get('products');
		$result = $query->result_array();
		if($result){
			$product_class = $result[0]['class'];
			
			$this->db->select('realm');
			$this->db->where('id', $product_class);
			$query = $this->db->get('is_classes');
			$result_2 = $query->first_row('array');
			if($result_2){
				$realm = $result_2['realm'];
			}else{
				$realm = '';
			}
			return $realm;
		}else{
			return '';
		}
	}
	*/

    function get_product_realm($product_id) {

        $product_class_id =  $this->get_product_class_id($product_id);
       // $product_class = $this->get_is_class($product_id);
       // var_dump($product_class);

        $this->db->select('realm');
        $this->db->where('table_id', $product_class_id);
        $query = $this->db->get('is_classes');
        $result = $query->first_row('array');
        return $result['realm'];
    }


	function get_user_full_name($username){		
		$this->db->select('first_name, last_name');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$user = $query->first_row('array');
		if($user){
			$name = $user['first_name'] . ' ' . $user['last_name'];
			return $name;
		}else{
			return '';
		}
		
	}



    function update_order_username_password_db($order_id, $account_username, $account_password){

        $order_data = array(

            'account_username' => $account_username,
            'account_password' => $account_password,

        );

        $this->db->where('id', $order_id);
        $update_result = $this->db->update('orders', $order_data);

        return $update_result;

    }


	function insert_order($data, $active_status = null)
	{
		$username = $data['username'];
		$product_id = $data['product_id'];
		$acc_username = !empty($data['acc_username']) ? $data['acc_username'] : '';
		$acc_password = !empty($data['acc_password']) ? $data['acc_password'] : '';
		$payment_method = $data['payment_type'];
		$choose_cycle = $data['choose_cycle'];
        $avios_code = $data['avios_code'];
		
		$price = $this->get_product_price($product_id);
		$discounted_price = $this->get_discounted_price($username, $price);
		$pro_rata_option = $this->get_pro_rata_option($product_id);
		$pro_rata_price = $this->get_pro_rate_price($pro_rata_option, $price);
		$pro_discounted = $this->get_discounted_price($username, $pro_rata_price);
		$total_price = $discounted_price;
		$pro_rata_total = $pro_discounted;
		
		//Keoma Wright (Client) (R46.55) (DEBIT ORDER)  =>[Name_Surname] (Client) ([amount] - [product_name]) (DEBIT ORDER)
		$product_name = $this->get_product_name($product_id);
		$name = $this->get_user_full_name($username);
		//$acc_comment = $name.'(Client)(R'.round($total_price, 2).' - '.$product_name.')(DEBIT ORDER)';
        // get product comment
        $draft_comment = $this->get_comment_by_product_and_payment_method($product_id, $payment_method);
        $acc_comment = $this->parse_default_comment($draft_comment, $name, $total_price, $product_name);

        //

		$user_id = $this->membership_model->get_user_id($username);


        $realm = '';
        $realm = $this->get_product_realm($product_id);

        $status = 'pending';
        if ($active_status != null)
            $status = 'active';


		//Insert into orders
		$order = array (
			'product' => $product_id,
			'status' => $status,   // pending default
			'user' => $username,
			'price' => round($total_price, 2),
			'pro_rata_extra' => round($pro_discounted, 2),
			'account_username' => $acc_username,
			'account_password' => $acc_password,
			'account_comment' => $acc_comment,
            'realm'     => $realm,
			'type' => 'auto',
			'cancel_flage' => 1,
			'display_usage' => 1,
			'change_flag' => 1,
			'id_user' => $user_id,
			'date' => date('Y-m-d H:i:s', time()),
			'payment_method' => $payment_method,
			'billing_cycle'  => $choose_cycle,
            'avios_code' => $avios_code,
		);	
		$this->db->insert('orders', $order);
		$order_id = $this->db->insert_id();
		
		// Insert into activity log
		$date = date('l jS \of F Y \a\t h:i A');
		$product_name = $this->get_product_name($product_id);
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

		return $order_id;
	}



	function get_invoices_data($invoice_id){
		$this->db->where('id', $invoice_id);
		$query = $this->db->get('invoices');
		$result = $query->result_array();
		return $result[0];
	}

	function get_order_data($order_id) {
		$this->db->where('id', $order_id);
		$query = $this->db->get('orders');
		$result = $query->first_row('array');
		return $result;
	}

	function get_discounted_price($username, $price) {
		$this->db->select('discount');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		if (!empty($result)) {
			$discount = $result['discount']; // x in 100
			$discount = (100 - $discount) / 100;
			$price = $price * $discount;
		}else {
			$price = 0;
		}
		return $price;
	}

	function clear_cart() {
		$this->session->set_userdata('cart', '');
	}

	function get_billing_cycles() {
		$billing_cycles = array (
			'monthly' => 'Monthy',
			'yearly' => 'Yearly',
			'once' => 'Once-Off'
		);

		return $billing_cycles;
	}

	function get_product_list () {
		$this->db->select('id, name, parent');
		$query = $this->db->get('products');
		return $query->result_array();
	}

	function get_product_categories() {
		$this->db->select('id, name, slug');
		$query = $this->db->get('product_categories');
		return $query->result_array();
	}

	function get_product_subcategories() {
		$this->db->select('id, name, slug,parent');
		$query = $this->db->get('product_subcategories');
		return $query->result_array();
	}

    function get_active_categories($role) {

        $table = 'product_categories';
        if($role == 'reseller') {
            $table = 'product_categories_reseller';
        }

        $this->db->select('id, name, slug')->where('visible', 1);
        $query = $this->db->get($table);
        return $query->result_array();
    }

    function get_active_subcategories($categories, $role) {

        $result = [];
        $table = 'product_subcategories';
        if($role == 'reseller') {
            $table = 'product_subcategories_reseller';
        }

        foreach ($categories as $category) {
            $this->db->select('id, name, slug,parent')
                ->where('visible', 1)
                ->where('parent', $category['id']);
            $query = $this->db->get($table);
            $result[$category['id']] = $query->result_array();
        }

        return $result;
    }

	function get_product_data_where_cat($p_id, $c_id) {
		$this->db->where( array('id' => $p_id, 'parent' => $c_id ) );
		$query = $this->db->get('products');
		$product_settings = $query->first_row('array');
		return $product_settings;
	}

	function get_products_from_subcat($sc_slug, $role) {

        $table = 'products';
        if($role == 'reseller') {
            $table = 'products_reseller';
        }
		$this->db->where( 'parent', $sc_slug );
		$this->db->where('active', 1);
		$this->db->where('status', 'active');
		$query = $this->db->get($table);
		$products = $query->result_array();
		return $products;
	}

	function get_products_from_cat($c_id) {
		$this->db->where( 'parent', $c_id );
		$query = $this->db->get('products');
		$products = $query->result_array();
		return $products;
	}
		
	function update_order($order_id,$order_data){	
		// if the password has changed, we need to change that on ISDSL.		
		$isdsl_update = false;
		
		if (isset($order_data['account_password'])) {
			// the password has definitely changed
			$isdsl_update = true;
			$new_password = $order_data['account_password'];
		}

		if ($isdsl_update) {
			// perform the update
			if (isset($new_password) && trim($new_password) != '') {


				$class = $this->get_is_class($order_id);
				//$realm_data = $this->get_is_details($class);


                if (!isset($this->order_model))
                    $this->load->model('admin/order_model');
                $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

                $order_data_for_api['account_username'] = $order_data['account_username'];
                $order_data_for_api['realm'] = $realm_data["realm"];


                // API call
                if (!isset($this->network_api_handler_model))
                    $this->load->model("network_api_handler_model");
                $api_response = $this->network_api_handler_model->change_account_password($order_data_for_api, $new_password);


                // we don't update account_password in DB if api has returned FALSE
                if (!$api_response['result'])
                    unset($order_data['account_password']);

                // TODO : add some error message for client
			}
		}		

		if (isset($order_data['id'])) {
			unset($order_data['id']);
		}

		$this->db->where('id', $order_id);
		$this->db->update('orders', $order_data);
	}
	
	function update_order_by_service($order_id,$data){
		$this->db->where('id',$order_id);
		$result = $this->db->update('orders',$data);
		return $result;		
	}
	
	function get_service_data($id){
		$this->db->select('account_password, change_flag');
		$this->db->where('id', $id);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		return $result[0];
	}
	
	function client_insert_order($user_id, $product_id, $acc_username, $acc_password, $payment_type)
	{
		$product_data = $this->get_product_data($product_id);
		$price = $product_data['price'];
		$product_name = $product_data['name'];
		$class = $product_data['class'];
		$username = $this->membership_model->get_user_name($user_id);
		$full_name = $this->membership_model->get_user_name_nice($username);
		$ac_email = $this->membership_model->get_email($username);
		$acc_comment = $full_name.'(Client)(R'.$price.' - '.$product_name.')(DEBIT ORDER)';
		
		if (isset($product_data['pro_rata_option'])) {
			$pr_option = $product_data['pro_rata_option'];
			$price = $product_data['price'];
			$pro_rata = $this->product_model->get_pro_rate_price($pr_option, $price);
		} else {
			$pro_rata = 0.00;
		}
		
		if ($pro_rata != 0.00) {
			$pro_rata_extra = number_format(round($pro_rata * $price, 2), 2);
		}else{
			$pro_rata_extra = $price;
		}


        $realm = '';
        $realm = $this->get_product_realm($product_id);
		
		$order_data = array(
			'product' => $product_id,
			'date' => date('Y-m-d', time()),
			'status' => 'pending',
			'price' => $price,
			'pro_rata_extra' => $pro_rata_extra,
			'account_username' => $acc_username,
			'account_password' => $acc_password,
			'account_comment' => $acc_comment,
            'realm' => $realm,
			'change_flag' => 1,
			'type' => 'auto',
			'display_usage' => 1,
			'cancel_flage' => 1,
			'id_user' => $user_id,
			'user' => $username,
			'payment_method' => $payment_type,
		);
		$this->db->insert('orders', $order_data);
		$order_id = $this->db->insert_id();
		
		// Insert into activity log
		$date = date('l jS \of F Y \a\t h:i A');
		$nice_price = number_format(round($price, 2), 2);
		$next_month = date("F Y",strtotime("+1 months"));
		$cur_date = date("F Y");
		$activity = "On $date, $username ordered $product_name. \n";
		$activity .= "Pro-rata billing for $cur_date: R$price. \n";
		$activity .= "Billing from $next_month: R$nice_price.";
		$activity = array(
			'user' => $username,
			'activity' => $activity,
			'type' => 'Order Product',
		);
		$this->db->insert('activity_log', $activity);
		
		//Insert into invoices
		$invoice_data = array(
			'invoice_name' => 'Tax Invoice for '.$username.' in '.date('Y-m-d', time()).'',
			'create_date' => date('Y-m-d H:i:s', time()),
			'type' => 'auto',
			'order_id' =>$order_id,
			'user_id' => $user_id,
			'user_name' => $username,
		);
		$this->db->insert('invoices', $invoice_data);
		$invoice_id = $this->db->insert_id();
		
		$return_data = array(
			'order_id' => $order_id,
			'invoice_id' => $invoice_id,
		);
		return  $return_data;
	}
	
	function get_invoice_data($invoice_id)
	{
		$this->db->where('id', $invoice_id);
		$query = $this->db->get('invoices');
		$result = $query->result_array();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
	
	function get_product_id_by_rand($rand_num)
	{
		$this->db->where('random_num', $rand_num);
		$query = $this->db->get('products');
		$result = $query->first_row();
		if($result){
			return $result->id;
		}else{
			return null;
		}
	}
	
	function get_billing_cycle_exist($product_id)
	{
		$this->db->select('billing_cycle');
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('billing_cycle');
		if($query->result_array()){
			return $result = $query->result_array();
		}else{
			return $result = '';
		}
	}


    function get_comment_by_product_and_payment_method($product_id, $payment_method = null){

        // e.g.  [Name_Surname] (Reseller) ([amount] - [product_name]) (DEBIT ORDER)

        // get deafault comment
           $this->db->select('default_comment');
           $this->db->where('id',$product_id);
           $query = $this->db->get('products');
           $result = $query->result_array();

           $default_comment = '';
           if (!empty($result)){

               $default_comment = $result[0]['default_comment'];
           }

          // get payment comment
           $result_comment = $default_comment;
           if ($payment_method != null){



               $this->db->select('default_comment');
               $this->db->where('product_id',$product_id);
               $this->db->where('payment_method',$payment_method);

               $query2 = $this->db->get('product_additional_comments');
               $result2 = $query2->result_array();


               if (!empty($result2)) {

                   $payment_comment = $result2[0]['default_comment'];
                   if (!empty($payment_comment))
                       $result_comment = $payment_comment;
               }

            }
            return $result_comment;
    }

    function parse_default_comment($comment, $name_surname, $amount, $product_name){

        /*
         *  [User_Name] :User's login username.
            [Password] :User's login password.
            [First_Name] :User's first name.
            [Last_Name] :User's last name.
            [Email_Address] :User's email address.
            [Register_Date] :User's registration date. Eg:2013-1-1
            [Current_Status]:The user's current status.

            [Name_Surname] (Reseller) ([amount] - [product_name]) (DEBIT ORDER)
        	//$acc_comment = $name.'(Client)(R'.round($total_price, 2).' - '.$product_name.')(DEBIT ORDER)';
         */

        $comment = str_replace("[Name_Surname]", $name_surname, $comment);
        $comment = str_replace("[amount]", "R" . round($amount, 2), $comment);
        $comment = str_replace("[product_name]", $product_name, $comment);


        return $comment;
    }


    // ================================================================
    // ================================================================
    //                  TopUp functions
    // ================================================================
    // ================================================================


    function get_product_topup_options($product_id){

        if (empty($product_id))
            return false;


        $this->db->select('topup_id, topup_id2, topup_id3 , topup_active');
        $this->db->where('id',$product_id);
        $query = $this->db->get('products');

        $result = $query->row_array();
        if (empty($result))
            return false;

        return $result;
    }


    function topup_get_config($id){

        $this->db->select();
        $this->db->where('topup_id', $id);
        $this->db->from('topup_list');

        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
    }

    function topup_get_payments($id){

        $this->db->select('payment_method');
        $this->db->where('topup_id', $id);
        $this->db->order_by('payment_method','ASC');

        $query = $this->db->get('topup_payment_methods');
        $rows = $query->result_array();

        // { [0]=> array(1) { ["payment_method"]=> string(11) "debit_order" } [1]=> array(1) { ["payment_method"]=> string(3) "eft" } }
        $result_array = array();
        // credit_card_auto, credit_card, debit_order, eft
        foreach ($rows as $payment_method){

            $result_array[$payment_method['payment_method']] = true;
        }


        return $result_array;

    }

    function get_info_for_order_topup($product_id){


        $topup_data = $this->get_product_topup_options($product_id);

        $topup_active_parameter = $topup_data['topup_active'];
        $topup_ids_array[1] = $topup_data['topup_id'];
        $topup_ids_array[2] = $topup_data['topup_id2'];
        $topup_ids_array[3] = $topup_data['topup_id3'];

        //there are not assigned topUps
        if ($topup_active_parameter != '1') {

            return 0;
        }

        $topup_config_array = array();
        $i = 1;
        foreach($topup_ids_array as $topup_id){

            $topup_config = '';
            $topup_payments = '';
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $topup_config = $this->topup_get_config($topup_id);
            $topup_payments = $this->topup_get_payments($topup_id);

            $topup_config_array[$i] = $topup_config;
            $topup_config_array[$i]['payments'] = $topup_payments;

            $i++;
        }

        return $topup_config_array;


    }


    function check_topup_available_for_orders($orders_array, $user_id){


        $topup_array = array();
        foreach($orders_array as $order){
                $order_id = $order['id'];
                $product_id = $order['product'];

                $topup_row =  $this->check_topup_by_order($order_id, $product_id, $user_id);

                $topup_array[$order_id]['topup_config'] = $topup_row['topup_config'];
                $topup_array[$order_id]['topup_current_level'] = $topup_row['topup_current_level'];

            }
            return $topup_array;

        }

        function get_user_topup_level($user_id, $order_id, $month, $year){


            // year 0000 , month 00
            $topup_level = 0;

            $this->db->select('topup_level');
            $this->db->where('user_id', $user_id);
            $this->db->like('order_time', $year . '-' . $month);
            $this->db->where('order_id',$order_id);
            $this->db->where('payment_status !=', 'canceled');
            $this->db->order_by('topup_level', 'desc');

            $this->db->from('topup_orders');

            $query = $this->db->get();
            $result = $query->result_array();


            if (empty($result)){

                return $topup_level;
            }

            $topup_level = $result[0]['topup_level'];
            return $topup_level;
        }


    function get_user_topup_level_with_additional_data($user_id, $order_id, $month, $year){


            // year 0000 , month 00
            $topup_order_array = array(

                'topup_level' => '0',
                'payment_status' => false,
                'id'             => false,


            );

            $this->db->select('topup_level, payment_status, id');
            $this->db->where('user_id', $user_id);
            $this->db->like('order_time', $year . '-' . $month);
            $this->db->where('order_id',$order_id);
            $this->db->where('payment_status !=', 'canceled');
            $this->db->order_by('topup_level', 'desc');

            $this->db->from('topup_orders');

            $query = $this->db->get();
            $result = $query->result_array();


            if (empty($result)){

                return $topup_order_array;
            }

            $topup_order_array = $result[0];
            return $topup_order_array;
        }



        function check_topup_by_order($order_id, $product_id, $user_id){

            // ---------------------------------------------------------------
            $topup_row = array();

            // if product is empty - > return false
            if (empty($product_id)){
                $topup_row['topup_config'] = false;
                $topup_row['topup_current_level'] = 0;

                return $topup_row;

            }

            // ---------------------------------------------------------------

            // get topUps configs by product id
            $topup_data = $this->get_product_topup_options($product_id);

            // Check if TopUp is enabled in current product
            $topup_active_parameter = $topup_data['topup_active'];
            if (!$topup_active_parameter){

                $topup_row['topup_config'] = false;
                $topup_row['topup_current_level'] = 0;
                return $topup_row;
            }

            // ---------------------------------------------------------------

            // check if user already has topUP in current month
            $current_month = date('m');
            $current_year = date('Y');

            // current Order's TopU level (0 - not ordered , 1 - prderd 1st level , 2 - ordered second level)
            //                                      3 - ordered third level

            // get last TopUp level which user ordered (if status 'canceled' -> we load previous order)
            $topup_order_array = $this->get_user_topup_level_with_additional_data($user_id, $order_id, $current_month, $current_year);
            $topup_level = $topup_order_array['topup_level'];
            $topup_payment_status = $topup_order_array['payment_status'];


            $topup_ids_array[1] = $topup_data['topup_id'];
            $topup_ids_array[2] = $topup_data['topup_id2'];
            $topup_ids_array[3] = $topup_data['topup_id3'];

            // compare user last TopUp level with available levels on TopUp
            $topup_config_flag = false;
            if ( isset($topup_ids_array[$topup_level+1]) && !empty($topup_ids_array[$topup_level+1] )   )
                $topup_config_flag = true;

            // ---------------------------------------------------------------

            // if users last TopUp order is canceled -> he can order the same once more time
            // if users last TopUP order is in process -> we block button for user


            // check if current TopUp order is 'in process'
            $topup_payment_status = trim($topup_payment_status);
            if ($topup_payment_status == "in process")
                $topup_config_flag = false;

            $topup_row['topup_config'] = $topup_config_flag;
            $topup_row['topup_current_level'] = $topup_level;


            return $topup_row;

        }


        function process_product_request($field){

            $val = $this->input->get_post($field, TRUE);
            $val = strip_tags(mysql_real_escape_string($val));
            $val = trim($val);
            return $val;

        }


        function insert_topup_order($data){

           $insert_result =  $this->db->insert('topup_orders', $data);
           $insert_id = $this->db->insert_id();
           return $insert_id;

        }


       // true - alredy exist, false - assign schedule
        function check_schedule_topup($account_username, $year,  $month, $user_id, $order_id){

            $this->db->select('id');
            $this->db->like('order_time', $year . '-' . $month);
            $this->db->where('order_id',$order_id);
            $this->db->where('user_id',$user_id);
            $this->db->where('adsl_username', $account_username);
            $this->db->where('schedule_api_status', '1');

            $this->db->from('topup_orders');

            $query = $this->db->get();
            $result = $query->result_array();

            $result_flag = false;
            if (!empty($result))
                $result_flag = $result[0]['id']; // return API


            return $result_flag;
        }

        function update_topup_order($id,$data){

            $this->db->where('id', $id);
            $update_result = $this->db->update('topup_orders', $data);

            return $update_result;
        }


        function get_last_order_topup_for_current_month($order_id, $user_id,  $month, $year){

            $this->db->select('*');
            $this->db->from('topup_orders');
            $this->db->join('topup_list','topup_list.topup_id = topup_orders.topup_config_id');

            $datestr    = $year.'-'.$month;
            $dateformat = date('Y-m', strtotime($datestr));
            $this->db->like('topup_orders.order_time', $dateformat);

            $this->db->where('topup_orders.order_id', $order_id);
            $this->db->where('topup_orders.user_id', $user_id);
            $this->db->where('payment_status !=','canceled');

            $this->db->where('topup_orders.api_message', 'Empty-ok');
            $this->db->or_where('topup_orders.api_status', '1');


            $this->db->order_by('topup_orders.topup_level','DESC');

            $query = $this->db->get();
            $result = $query->result_array();

            $answer_array = array();

            if (!empty($result)){

                $answer_array['topup_name']        = $result[0]['topup_name'];
                $answer_array['topup_description'] = $result[0]['topup_description'];
                $answer_array['topup_level']       = $result[0]['topup_level'];
                $answer_array['order_time']        = $result[0]['order_time'];


            }

           return $answer_array;

        }

    /// ----------------------------------------------------------------------------






    }
