<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client extends CI_Controller {
	public $site_data;

	function __construct() 
	{
		parent::__construct();
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");

		$this->load->model('user/product_model');
		$this->load->model('user/category_model');
		$this->load->model('user/user_model');
		$this->load->model('user/is_classes');
		$this->load->model('user/cloudsl_model');
		$this->load->model('admin/order_model');

        $this->load->model('admin/realm_model');
        $this->load->model('membership_model');
        $this->load->model('crypto_model');
        $this->load->model('validation_model');

        // payfast model
        $this->load->model('payfast_model');
        $this->load->helper('url');


        // FORCE SSL
        if($_SERVER['HTTPS']!="on" && ( $_SERVER['HTTP_HOST'] != STAGE_HOST) )
        {
            $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }

        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);

    }
	
	function order_product($rand_num)
	{
        $rand_num = $this->validation_model->process_value($rand_num);
        $rand_num_validation = $this->form_validation->numeric($rand_num);

        if (!$rand_num_validation){
            $data['main_content'] = 'client/error';
            $data['sidebar'] = FALSE;
            $this->load->view('client/includes/template', $data);
            $this->site_data = array();
            return;
        }

		$product_id = $this->product_model->get_product_id_by_rand($rand_num);
		if($product_id){
			$product_data = $this->product_model->get_product_data($product_id);

			if (isset($product_data['pro_rata_option'])) {
				$pr_option = $product_data['pro_rata_option'];
				$price = $product_data['price'];
				$pro_rata = $this->product_model->get_pro_rate_price($pr_option, $price);
			} else {
				$pro_rata = 0.00;
			}
			$billing_cycle = $this->product_model->get_billing_cycle_exist($product_id);


            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $this->load->helper('file');

            // load Terms of Use
            $terms_txt = read_file('terms.txt');

            $terms_txt = str_replace("“","\"",$terms_txt);
            $terms_txt = str_replace("”","\"",$terms_txt);
            $terms_txt = str_replace("’","'",$terms_txt);
            $terms_txt = str_replace("–","-",$terms_txt);
            $terms_txt = str_replace("‘","'",$terms_txt);


            $data['terms_txt'] = $terms_txt;
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


			$data['billing_cycle'] = $billing_cycle;
			$data['pro_rata'] = $pro_rata;
			$data['product_data'] = $product_data;
			$data['main_content'] = 'client/order_prodcuct';
			$data['sidebar'] = FALSE;
			$this->load->view('client/includes/template', $data);
			$this->site_data = array();
		}else{
			$data['main_content'] = 'client/error';
			$data['sidebar'] = FALSE;
			$this->load->view('client/includes/template', $data);
			$this->site_data = array();
		}
	}
	
	function create_client()
	{	
		if($_POST){
			$product_id = strip_tags(mysql_real_escape_string($_POST['product_id']));
            $product_id = trim($product_id);
			$product_data = $this->product_model->get_product_data($product_id);
			if($product_data['type']=='daily')
				$role = 'cloudsl';
			else 
				$role = 'client';
			$first_name = strip_tags(mysql_real_escape_string($_POST['first_name']));
            $first_name = trim($first_name);

			$last_name = strip_tags(mysql_real_escape_string($_POST['last_name']));
            $last_name = trim($last_name);

			$username = strip_tags(mysql_real_escape_string($_POST['user_name']));
            $username = trim($username);

			$pass = !empty($_POST['pwd']) ? strip_tags(mysql_real_escape_string($_POST['pwd'])) : null;
            $pass = trim($pass);
            $pwd = $this->crypto_model->encode($pass);

			$email = !empty($_POST['email_address']) ? strip_tags(mysql_real_escape_string($_POST['email_address'])) : null;
			$email = trim($email);

            $mobile = !empty($_POST['mobile']) ? strip_tags(mysql_real_escape_string($_POST['mobile'])) : '0';
            $mobile = trim($mobile);

			$reason = !empty($_POST['reason']) ? strip_tags(mysql_real_escape_string($_POST['reason'])) : null;
            $reason = trim($reason);

			$billing_name = !empty($_POST['billing_name']) ? strip_tags(mysql_real_escape_string($_POST['billing_name'])) : null;
			$billing_name = trim($billing_name);

            $address_1 = !empty($_POST['address_1']) ? strip_tags(mysql_real_escape_string($_POST['address_1'])) : null;
            $address_1 = trim($address_1);

			$address_2 = !empty($_POST['address_2']) ? strip_tags(mysql_real_escape_string($_POST['address_2'])) : null;
            $address_2 = trim($address_2);

			$city = !empty($_POST['city']) ? strip_tags(mysql_real_escape_string($_POST['city'])) : null;
            $city = trim($city);

			$province = !empty($_POST['province']) ? strip_tags(mysql_real_escape_string($_POST['province'])) : null;
            $province = trim($province);

			$country = !empty($_POST['country']) ? strip_tags(mysql_real_escape_string($_POST['country'])) : null;
            $country = trim($country);

			$postal_code  = !empty($_POST['postal_code']) ? strip_tags(mysql_real_escape_string($_POST['postal_code'])) : null;
			$postal_code = trim($postal_code);

            $contact_number	 = !empty($_POST['contact_number']) ? strip_tags(mysql_real_escape_string($_POST['contact_number'])) : null;
            $contact_number = trim($contact_number);

            $adsl_number = !empty($_POST['adsl_number']) ? strip_tags(mysql_real_escape_string($_POST['adsl_number'])) : null;
			$adsl_number = trim($adsl_number);


            $sa_id_number = strip_tags(mysql_real_escape_string($_POST['sa_id_number']));
            $sa_id_number = trim($sa_id_number);

			//$bank_name = !empty($_POST['bank_name']) ? $_POST['bank_name'] : null;
			//$bank_account_number = !empty($_POST['bank_account_number']) ? $_POST['bank_account_number'] : null;
			//$bank_account_type = !empty($_POST['bank_account_type']) ? $_POST['bank_account_type'] : null;
			//$bank_branch_code = !empty($_POST['bank_branch_code']) ? $_POST['bank_branch_code'] : null;
			
			$account_info = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'username' => $username,
				'password' => $pwd,
				'email_address' => $email,
				'role' => $role,
				'joined' => date('y-m-d', time()),
				'status' => 'active',
				'mobile_number' => $mobile,
				'reason' => $reason,
			);
			
			$this->db->insert('membership', $account_info);
			$client_id =  $this->db->insert_id();
			
			//create OW id
			$ow_id = $this->membership_model->create_OW($client_id);
			
			//insert the ow id into user
			$format_ow = "OW".$ow_id;
			$this->db->where('id', $client_id);
			$this->db->update('membership', array('ow' => $format_ow));
			
			$billing_info = array(
				'billing_name'	=> $billing_name,
				'address_1'	=> $address_1,
				'address_2'	=> $address_2,
				'city'	=> $city,
				'province'	=> $province,
				'country'	=> $country,
				'postal_code' => $postal_code,
				'email'	 => $email,
				'contact_number' => $contact_number,
				'name_on_card' => '',
				'card_num' => '',
				'cvc'	=> '',
				'expires_month'	=> '',
				'expires_year'	=> '',
				'bank_name' => '',
				'bank_account_number' => '',
				'bank_account_type'  => '',
				'bank_branch_code' => '',
				'mobile' => $mobile,
				'id_user' => $client_id,
				'username' => $username,
                'sa_id_number' => $sa_id_number,

			);

            if (!empty($adsl_number))
                $billing_info['adsl_number'] = $adsl_number;



			$this->db->insert('billing', $billing_info);
			$billing_id =  $this->db->insert_id();
			$this->user_model->email_to_admin($account_info, $billing_info);
			$this->user_model->email_register($email,$username);
			
			//$this->user_model->email_active_account($client_id);
			$sms_content = 'Your account has been successfully created. See email for more details. - OpenWeb';
			$sms_result = $this->order_model->send_sms($mobile, $sms_content);
			
			$this->order_model->send_system_ceo($client_id);
			
			$product_data = $this->product_model->get_product_data($product_id);
			if($product_data['type']=='daily'){
				$this->cloudsl_model->active_user_cloudsl($client_id,$format_ow);
				$user['username']=$username;
				$user['id']=$client_id;
				$info['product']=$product_data['id'];
				$info['name']=$product_data['name'];
				$info['price']=$product_data['price'];
				$this->cloudsl_model->add_order_cloudsl($user,$info);
				
				redirect('user/login');
			} else {

				if (isset($product_data['pro_rata_option'])) {
					$pr_option = $product_data['pro_rata_option'];
					$price = $product_data['price'];
					$pro_rata = $this->product_model->get_pro_rate_price($pr_option, $price);
				} else {
					$pro_rata = 0.00;
				}

                $billing_cycle = $this->product_model->get_billing_cycle_exist($product_id);

                //add username to session
                $user_data_array = array(

                    'payfast_username' => $username,
                    'payfast_email'    => $email,
                );
                $this->session->set_userdata($user_data_array);


                // ~ payfast code ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                $discount = 0 ;
                $payment_info['item_name'] = $product_data['name'];
                $payment_info['item_description'] = '';
                $payment_info['discount'] = $discount;
                $payment_info['price'] = $product_data['price'];
                $payment_info['pro_price'] = $pro_rata;


                //   public  $return_url   = 'product/payfast_success';
                //   public  $cancel_url   = 'product/payfast_falied';


                // - change url --------------------------------------------------------------------------------
                $payfast_original_return =  $this->payfast_model->return_url;
                $payfast_original_cancel =  $this->payfast_model->cancel_url;
                $this->payfast_model->return_url = str_replace('product', 'client',  $payfast_original_return);
                $this->payfast_model->cancel_url = str_replace('product', 'client',  $payfast_original_cancel);
                // ----------------------------------------------------------------------------------------------

                $data_for_payfast = $this->payfast_model->prepare_final_checkout($client_id, $username, $payment_info);
                $sandbox_data_for_payfast = $this->payfast_model->prepare_final_checkout($client_id, $username, $payment_info, "SANDBOX");


                // - change url back --------------------------------------------------------------------------
                $this->payfast_model->return_url = $payfast_original_return;
                $this->payfast_model->cancel_url = $payfast_original_cancel;

                // --------------------------------------------------------------------------------------------

                $pre_live_signature_for_payfast = $this->payfast_model->pre_signature($data_for_payfast);
                $pre_sandbox_signature_for_payfast = $this->payfast_model->pre_signature($sandbox_data_for_payfast, 'SANDBOX');


               // local order data & additional signature

                $order_data = array(

                 // 'account_username' => $acc_username,
                 // 'account_password' => $acc_password,
                 // 'realm'            => $realm,
                 //   'choose_cycle'   =>  $choose_cycle,
                    'product_id'       => $product_id,
                 //   'payment_type'     => 'payfast-payment'
                    'payment_type'     => 'credit_card',
                );

                $data['order_data_array'] = $order_data;
                //$order_signature = $this->payfast_model->generate_order_signature($order_data);
                //$data['order_signature'] = $order_signature;





                $data['username'] = $username;
                $spc_username = strpos($username,"sandbox-access");
                $data['sandbox_access'] = $spc_username;
                /* uncommeent after prod */  //$data['sandbox_access'] = 0;

                $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
                $data['live_payfast_host']   = $this->payfast_model->live_host;
                $data['payfast_data'] = $data_for_payfast;
                $data['sandbox_payfast_data'] = $sandbox_data_for_payfast;

                $data['pre_sandbox'] = $pre_sandbox_signature_for_payfast;
                $data['pre_live']    = $pre_live_signature_for_payfast;

                $data['user_discount1']  = $this->site_data['discount'];
                $data['user_name1'] = $this->site_data['username'];

                // ~ payfast code ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

				$data['billing_cycle'] = $billing_cycle;
				$data['pro_rata'] = $pro_rata;
				$data['user'] = $username;
				$data['billing_data'] = $billing_info;
				$data['realm'] = $this->product_model->get_product_realm($product_id);
				$data['product_data'] =  $product_data;
				$data['client_id'] = $client_id;
				//$data['payment_methods'] = $this->product_model->get_payment_methods($product_id,'all');

                $data['payment_methods'] = $this->product_model->get_full_payment_methods($product_id);



				$data['main_content'] = 'client/product_info';
				$data['sidebar'] = FALSE;
				$this->load->view('client/includes/template', $data);
			}
		}
	}
	
	function get_rand_str()
	{
		$chars = '0123456789';
		$str = '';
		for ( $i = 0; $i <5; $i++ ){
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return 'isp'.$str;
	}
	
	function cofirm_product()
	{

        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);

		$payment_type = strip_tags(mysql_real_escape_string($_POST['payment_method']));
        $payment_type = trim($payment_type);

		$user_id = strip_tags(mysql_real_escape_string($_POST['user_id']));
        $user_id = trim($user_id);

		$product_id = strip_tags(mysql_real_escape_string($_POST['product_id']));
        $product_id = trim($product_id);

        if ( empty($user_id) || empty($product_id)){

            $data = '';
            $data['main_content'] = 'client/congratulations';
            $data['sidebar'] = FALSE;
            $this->load->view('client/includes/template', $data);
           return ;
        }

        $number = '';
		
		$product_data = $this->product_model->get_product_data($product_id);
		$data['product_data'] =  $product_data;
		$data['client_id'] = $user_id;
		$data['payment_type'] = $payment_type;
		
		$price = $product_data['price'];
		$product_name = $product_data['name'];
		$class = $product_data['class'];
		
		$user = $this->membership_model->get_user_name($user_id);

        $number = $this->membership_model->get_number($user);

        $acc_username = !empty($_POST['acc_username']) ? strip_tags(mysql_real_escape_string($_POST['acc_username'])) : $this->get_rand_str();
        $acc_username = trim($acc_username);

        $acc_password = !empty($_POST['acc_password']) ? strip_tags(mysql_real_escape_string($_POST['acc_password'])) : $this->get_rand_str();
        $acc_password = trim($acc_password);

        $choose_cycle = !empty($_POST['billing_cycle']) ? strip_tags(mysql_real_escape_string($_POST['billing_cycle'])) : 'Monthly';
        $choose_cycle = trim($choose_cycle);

		$order_data = array(
			'username' => $user,
			'product_id' => $product_id,
			'acc_username' => $acc_username,
			'acc_password' => $acc_password,
			'payment_type' => $payment_type,
			'choose_cycle' => $choose_cycle,
		);

        $order_data['acc_username'] = trim($order_data['acc_username']);
        $order_data['acc_password'] = trim($order_data['acc_password']);


		$order_id = $this->product_model->insert_order($order_data);

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~ auto-creation code ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $additional_message = '';
        $auto = $this->product_model->get_is_auto($product_id);//get auto_creation from products  (TRUE or FALSE)
        $acc_realm = !empty($_POST['acc_realm']) ? strip_tags(mysql_real_escape_string($_POST['acc_realm'])) : 'none';
        $acc_realm = trim($acc_realm);

        $choose_cycle = strtolower($order_data['choose_cycle']);


        if ($auto) {

            $isdl_create_result = false;
            // ~~~~~~~~~~ isdsl create ~~~~~~~~~~~~~~~~~~~~~
            $realm_data = $this->realm_model->get_realm_data_by_name($acc_realm);


            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];
            $realm = $acc_realm;
            $sess = 0;

            // ISDSL connect
            //$sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id   //#A

            $new_account_data = $this->product_model->get_order_data($order_id);
            $comment = $new_account_data['account_comment'];//get account_comment form orders
             // $class = $this->product_model->get_class_by_product_id($product_id);

            // Add to ISDSL
            $acc_realm_user = $order_data['acc_username'] . '@' . $realm;
            $ac_email = $this->membership_model->get_email($user);

            //$resp = $this->is_classes->add_realm_new($sess, $class, $order_data['acc_username'], $order_data['acc_password'], $comment, $ac_email);//#A
            //$resp = '1';

            $this->load->model("network_api_handler_model");

            $order_data_for_api = array(
                'account_username' => $order_data['acc_username'],
                'realm' => $realm,

            );
            $creation_result = $this->network_api_handler_model->add_new_realm_user($order_data_for_api, $class, $order_data['acc_password'], $comment, $ac_email);



            if ($creation_result['result'] == true) {
                $isdl_create_result = true;
            }

            // #######################################
            if ($isdl_create_result){

                // activate insert
                $user_name_nice = $this->membership_model->get_user_name_nice($user);//get full name from membership
                $this->order_model->set_activated($order_id);
                $this->order_model->email_activation($ac_email, $product_name, $order_data['acc_username'], $realm, $order_data['acc_password'], $user_name_nice);

                //   SMS
                     $sms_content = "Your ADSL product has been successfully created. See email for more details. Username: " . $acc_realm_user . " Password: " . $order_data['acc_password'] . " - OpenWeb";
                    if (!empty($number))
                        $this->order_model->send_sms($number, $sms_content); //#B

                if ($choose_cycle == 'once-off'){


                    //set  pending
                    //! $pending_resp = $this->is_classes->set_pending_update_new($sess, $acc_realm_user, 'nosvc');
                    $cancellationDate = date("Y-m-1", strtotime("+ 1 month"));
                    $response = $this->network_api_handler_model->cancel_account($order_data_for_api,  $cancellationDate);

                }
            } else {
                // handle error

                /*
                $error_message = '';
                switch ($resp){
                    case '5' :  $error_message = 'Failure: Invalid session identifier supplied'; break;
                    case '8' :  $error_message = 'Invalid class'; break;
                    case '11' : $error_message = 'Username exists'; break;

                }
                */
                $additional_message = $creation_result['user_message'];
            }
        }



        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		$invoice_id = $this->user_model->save_invoices($order_id, $user);
		$pdf_id = $this->getPDF($invoice_id, $user, $product_name, $price);
		$this->order_model->email_invoices_individual($user, $pdf_id);
		$this->order_model->email_ceo_product($user, $product_name, $payment_type);


        $post_name_on_card = !empty($_POST['name_on_card']) ? strip_tags(mysql_real_escape_string($_POST['name_on_card'])) : null;
        $post_name_on_card = trim($post_name_on_card);

        $post_card_num = !empty($_POST['card_num']) ? strip_tags(mysql_real_escape_string($_POST['card_num'])) : null;
        $post_card_num = trim($post_card_num);

        $post_cvc = !empty($_POST['cvc']) ? strip_tags(mysql_real_escape_string($_POST['cvc'])) : null;
        $post_cvc = trim($post_cvc);

        $post_expires_month = !empty($_POST['expires_month']) ? strip_tags(mysql_real_escape_string($_POST['expires_month'])) : null;
        $post_expires_month = trim($post_expires_month);

        $post_expires_year = !empty($_POST['expires_year']) ? strip_tags(mysql_real_escape_string($_POST['expires_year'])) : null;
        $post_expires_year = trim($post_expires_year);

        $post_bank_name = !empty($_POST['bank_name']) ? strip_tags(mysql_real_escape_string($_POST['bank_name'])) : null;
        $post_bank_name = trim($post_bank_name);

        $post_bank_account_number =  !empty($_POST['bank_account_number']) ? strip_tags(mysql_real_escape_string($_POST['bank_account_number'])) : null;
        $post_bank_account_number = trim($post_bank_account_number);

        $post_bank_account_type = !empty($_POST['bank_account_type']) ? strip_tags(mysql_real_escape_string($_POST['bank_account_type'])) : null;
        $post_bank_account_type = trim($post_bank_account_type);

        $post_bank_branch_code = !empty($_POST['bank_branch_code']) ? strip_tags(mysql_real_escape_string($_POST['bank_branch_code'])) : null;
        $post_bank_branch_code = trim($post_bank_branch_code);


		$billing_data = array(
			'name_on_card' => $post_name_on_card,
			'card_num' => $post_card_num,
			'cvc' => $post_cvc,
			'expires_month' => $post_expires_month,
			'expires_year' => $post_expires_year,
			'bank_name' => $post_bank_name,
			'bank_account_number' => $post_bank_account_number,
			'bank_account_type' => $post_bank_account_type,
			'bank_branch_code' => $post_bank_branch_code,
		);
		$this->db->update('billing', $billing_data, array('username' => $user));

        $order_message = '';
        if (empty($additional_message) && $auto){

            /*
            $message_data = array(
                'id' => 9,
            );
            */
            $order_message = AUTO_CREATE_SUCCESS_MESSAGE;
            $order_row = $this->order_model->get_last_order_by_username($user);
            if ($order_row != false){

                $order_message .= "<br/>";
             //   $order_message .= "Order details : ";
                $order_message .= "<br/> Username : " . $order_row['account_username'] . "@" . $order_row['realm'];
                $order_message .= "<br/> Password : " . $order_row['account_password'];

            }

        } elseif($payment_type == 'credit_card'){
			$message_data = array(
					'id' => 5,
			);
		}elseif ($payment_type == 'eft'){
			$message_data = array(
					'id' => 6,
			);
		}elseif ($payment_type == 'debit_order'){
			$message_data = array(
					'id' => 7,
			);
		}elseif ($payment_type == 'credit_card_auto'){
			$message_data = array(
					'id' => 8,
			);
		}

        $message = '';
        if (!empty($message_data))
            $message = $this->message_model->get_message($message_data);

        if (!empty($order_message))
            $message .= " " . $order_message;

        if ( ($payment_type == 'eft') && !$auto)
            $message =  EFT_MESSAGE_FOR_MANUAL;


		$data['message'] = $message;
		$data['price'] = $price;
        $data['conversion_flag'] = true;
		$data['main_content'] = 'client/congratulations';
		$data['sidebar'] = FALSE;
		$this->load->view('client/includes/template', $data);
	}
	
	function validate_username()
	{
		$id_user= isset($_POST['id_user']) ? $_POST['id_user'] : '';
        $id_user = strip_tags(mysql_real_escape_string($id_user));
        $id_user = trim($id_user);

        $post_username = $_POST['username'];
        $post_username = strip_tags(mysql_real_escape_string($post_username));
        $post_username = trim($post_username);
		
		if($id_user != ''){

			$username = $this->membership_model->get_user_name($id_user);
			if($post_username ==  $username){
				echo "true";
			}else{
				$result = $this->membership_model->validate_username($post_username);
				if(!$result){
					echo "true";
				}else{
					echo "false";
				}
			}
		}else{
			if(isset($post_username) && ($post_username != '') ){
				$result = $this->membership_model->validate_username($post_username);
				if($result){
					echo "false";
				}else{
					echo "true";
				}
			}
		}
	}
	
	function validate_email()
	{
		$id_user = isset($_POST['id_user']) ? $_POST['id_user'] : '';
        $id_user = strip_tags(mysql_real_escape_string($id_user));
        $id_user = trim($id_user);

        $post_email_address = $_POST['email_address'];
        $post_email_address = strip_tags(mysql_real_escape_string($post_email_address));
        $post_email_address = trim($post_email_address);



		if($id_user != ''){

			$email = $this->membership_model->get_user_email($id_user);
			if($post_email_address ==  $email){
				echo "true";
			}else{
				$result = $this->membership_model->validate_email($post_email_address);
				if(!$result){
					echo "true";
				}else{
					echo "false";
				}
			}
		}else{			
			if(isset($post_email_address) && ($post_email_address != '')){
				$result = $this->membership_model->validate_email($post_email_address);
				if($result){
					echo "false";
				}else{
					echo "true";
				}
			}			
		}
	}
	
	function get_open_ISP(){
		$result = $this->user_model->get_open_ISP();
		return $result;
	}
	
	function get_user_billing_info($username)
	{
		$user_list = $this->user_model->get_user_data($username);

		$result = $user_list['user_billing'];
		if($result){
			$billing_name = $result['billing_name'];
			$address = $result['address_1'].' '.$result['address_2'];
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
		}else{
			return false;
		}
	}


    // debug PDF invoice
    function debugPDF(){

        show_404();
        die();
        $invoice_id = '54805721';
        $username = 'test-vvv';
        $product_name = "TopUp 32";
        $price = "459.56";


        $this->getPDF($invoice_id, $username, $product_name, $price);
    }

	
	function getPDF($invoice_id, $username, $product_name, $price)
	{
		$this->load->library('tfpdf/MC_Table');
		$pdf=new MC_Table();

        $user_data = $this->user_model->get_user_data($username);
        $first_name = $user_data['user_settings']['first_name'];
        $last_name = $user_data['user_settings']['last_name'];

		$date = date('Y-m-d', time());
		$cost = 'R '.$price;
	
		$user_billing = $this->get_user_billing_info($username);
		$billing_name = $user_billing['billing_name'];
		$user_address = $user_billing['address'];
		$user_city = $user_billing['city'];
		$user_country = $user_billing['country'];
		$user_province = $user_billing['province'];
		$user_phone = 'Phone: '.$user_billing['phone'];
		$user_p_c = $user_province.', '.$user_country;
	
		$open_ISP = $this->get_open_ISP();
		$open_name = $open_ISP['name'];
		$vat_number = $open_ISP['vat_number'];
		$country = $open_ISP['country'];
		$province = $open_ISP['province'];
		$address = $open_ISP['address'];
		$phone = $open_ISP['phone'];
	
		$title = 'New Order Tax Invoice for '.$date;
		$pdf->AddPage();
	
		$pdf->SetFont('Arial','',20);
		$image = base_url().'img/main.png';
		$pdf->Image($image,70,5,60);
	
		$pdf->SetFont('Arial','',20);
		$pdf->SetXY(40, 30 );
		$pdf->Cell(20,8,$title,'C',true);
		$pdf->Ln();
		 
		//set invoice info
		$invoice_date = date('d/m/Y', time()) ;
		$invoice_id_format = "Tax Inv # : $invoice_id";
		$invoice_date_format = "Date : $invoice_date";
		 
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(20, 4, $invoice_id_format, '',true);
		$pdf->Cell(36, 10, $invoice_date_format, 0,0,'R',false,'');
		$pdf->Ln();
		 
		//set open info
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(20,4,$open_name,'',true);
		//$pdf->SetXY(150,50);
		$pdf->Cell(185,3,$billing_name,0,0,'R',false,'');
		$pdf->Ln();
		 
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(20,3,INVOICE_ORGANIZATION_ID.$vat_number,'',true);
        $pdf->Cell(185,3,$first_name.' '.$last_name,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,$address,'',true);
        $pdf->Cell(185,3,$user_address.' '.$user_city,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,$province.', '.$country,'',true);
        $pdf->Cell(185,3, $user_p_c,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,'Phone: '.$phone,'',true);
        $pdf->Cell(185,3, $user_phone,0,0,'R',false,'');
		$pdf->Ln();

        $pdf->Ln();
	
		//set the body
		$pdf->SetFillColor(128,128,128);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(92,92,92);
	
		//$pdf->Cell(50,8,"Username",1,0,'C',true);
		$pdf->Cell(95,8,"Product",1,0,'C',true);
		$pdf->Cell(40,8,"Date Ordered",1,0,'C',true);
		$pdf->Cell(50,8,"Cost this month",1,0,'C',true);
		$pdf->Ln();
	
		//$pdf->SetFillColor(224,235,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0);
		 
		$pdf->SetWidths(array(95,40,50));
		$pdf->Row(array($product_name, $date, $cost));
	
		$pdf->SetFillColor(255,255,255);
		$pdf->Ln(1);
		$pdf->Cell(185,8,'Total:'.$cost, 0, 0,'R',true);
		$pdf->Ln();
		$pdf->Cell(0,8, INVOICE_VAT_ROW, 0, 0,'',true);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Write(8, 'If you are on Debit Order you do not need to pay this invoice.');
		$pdf->Cell(90,8,'Banking Details:',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'Please note, accounts are payable on the 27th of each month,');
		$pdf->Cell(89,8,'Bank: ABSA',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'for the following months access.');
		$pdf->Cell(135,8,'Account Number: 4064449626',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'Please remember, you have to send us proof of payment,');
		$pdf->Cell(96,8,'Account Type: Cheque',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'otherwise we cannot honour the payment.');
		$pdf->Cell(120,8,'Branch Code: 632005',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'Kindly email your proof of payment to : admin@openweb.co.za');
		$pdf->Cell(88,8,'Reference: ' . $first_name . ' ' . $last_name,0,0,'R',false,'');
		//$pdf->Ln();
		//$pdf->Write(8, 'Fax proof to: 0866912166');
		 
		$title = 'New Order Invoice for '.$date;
	
		$path_name = APPPATH.'PDFfiles/'.$username;
		if(is_dir($path_name) == false){
			mkdir($path_name,0777);
		}
	
		$file_name = $invoice_id.'.pdf';
		$file_save_path = $path_name.'/'.$file_name;
		$pdf->Output($file_save_path, 'F');
	
		$data = array(
			'name' => $file_name,
			'path' => $file_save_path,
			'create_date' => date('Y-m-d H:i:s',strtotime('now')),
			'user_name' => $username,
			'invoices_id' => $invoice_id
		);
		$pdf_id = $this->user_model->save_pdf($invoice_id, $data);
		return $pdf_id;
	}


    function payfast_success(){

       // ini_set('display_errors', 1);


        $payfast_username = $this->session->userdata('payfast_username');
        $payfast_email    = $this->session->userdata('payfast_email');


        // get last order via this username
        $order_row = $this->order_model->get_last_order_by_username($payfast_username);

        $order_message = '';
        if ($order_row != false){

            $order_message = "<br/>";
            $order_message .= "Order details : ";
            $order_message .= "<br/> username : " . $order_row['account_username'] . "@" . $order_row['realm'];
            $order_message .= "<br/> password : " . $order_row['account_password'];

        }

        $message_data = array(
            'id' => 9,
        );
        $message = $this->message_model->get_message($message_data);
        $data['message'] = $message . $order_message;



        // show order data
        // send email
        // send sms




        //  $data['product_name'] = $product_name;
        //  $data['auto'] = $auto;
      //  $this->session->set_userdata('cart', '');
      //  $this->site_data['cart'] = '';

        $data['sidebar'] = FALSE;
        $data['main_content'] = 'user/product/congratulations';
        $this->load->view('user/includes/template', $data);

    }


    function payfast_failed(){


        $message_data = array(
            'id' => 11,
        );
        $data['message'] = $this->message_model->get_message($message_data);



        $data['sidebar'] = FALSE;
        $data['main_content'] = 'user/product/congratulations';
        $this->load->view('user/includes/template', $data);

    }


    function check_local_username(){

        // $acc_username, $realm
        $answer = false;
        //  echo json_encode($answer);
        //  die;

        if ($this->input->is_ajax_request()) {

            //      acc_username : acc_username,
            //      acc_realm : acc_realm

            //   $ajax_params = (array)json_decode($this->input->post('params'));
            //    $ajax_order = (array)json_decode($this->input->post('order_params'));
            $acc_username = $this->input->post('acc_username');
            $acc_realm = $this->input->post('acc_realm');

            $acc_username = strip_tags(mysql_real_escape_string($acc_username));
            $acc_username = trim($acc_username);

            $acc_realm = strip_tags(mysql_real_escape_string($acc_realm));
            $acc_realm = trim($acc_realm);

            // get order by user and realm
            $answer = $this->order_model->check_order_by_username_realm($acc_username, $acc_realm);

        }
        echo json_encode($answer);
    }

}
?>