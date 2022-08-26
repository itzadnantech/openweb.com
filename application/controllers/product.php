<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {
	public $site_data;

	function __construct() 
	{
		parent::__construct();
		$this->is_logged_in();
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");

        // FORCE SSL
        if($_SERVER['HTTPS']!="on" && ( $_SERVER['HTTP_HOST'] != STAGE_HOST) )
        {
            $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }

		$this->load->model('user/product_model');
		$this->load->model('user/category_model');
		$this->load->model('user/is_classes');
		$this->load->model('user/user_model');
        $this->load->model('admin/realm_model');
        $this->load->model('membership_model');


        $this->load->model('payfast_model');
        $this->load->helper('url');

		$category_list = $this->category_model->get_categories();
		$this->site_data['category_list'] = $category_list;
		$sub_assoc = $this->category_model->get_subcategories_assoc();
		$this->site_data['subcategories_assoc'] = $sub_assoc;
		$cart = $this->session->userdata('cart');
		$this->site_data['cart'] = $cart;
		$username = $this->session->userdata('username');
		$this->site_data['username'] = $username;
		$first_name = $this->membership_model->get_name($username);
		$this->site_data['first_name'] = $first_name;
		$discount = $this->membership_model->get_discount($username);
		$this->site_data['discount'] = $discount;

        $last_name = $this->membership_model->get_second_name($username);

        $this->site_data['second_name'] = $last_name;


        $this->site_data['ow'] = $this->session->userdata('ow');
	}

	function _remap($method, $params = array()) {
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
		} else {
			$cat = $this->product_model->get_product_categories();
			$product_categories = array();
			if (!empty($cat)) {
				foreach ($cat as $c) {
					array_push($product_categories, $c['slug']);
				}
			}
			if (in_array($method, $product_categories)) {
				// Then it isn't a 404, we need to show products in that cat.
				$this->product($params);
			} else {
				echo '404';
				print_r($product_categories);
			}
		}
	}

	function show_offerings($subcat_slug) {
		$username = $this->site_data['username'];
		$result = $this->get_user_billing_info($username);
		if(!$result){
			$msg = "Please fill in your billing information first.";

            $billing_data = $this->user_model->get_billing_data($username);
            if($billing_data){
                $data['user_data']['user_billing'] = $billing_data;
            }else{
                $data['user_data']['user_billing'] = '';
            }

			$data['info_message'] = $msg;
			$data['sidebar'] = TRUE;
			$data['navbar'] = TRUE;
			$data['main_content'] = 'user/billing';
            $this->asignSidebarData($data);
			$this->load->view('user/includes/template', $data);
		}else{
			$products = $this->product_model->get_products_from_subcat($subcat_slug, $this->session->userdata('role'));
			// Now we have all product data for products in the category!
			$data['products'] = $products;
			$subcat_name = $this->category_model->get_subcategory_name($subcat_slug, $this->session->userdata('role'));
//			$billing_cycles = $this->product_model->get_billing_cycles();
//			$data['billing_cycles'] = $billing_cycles;
			$data['subcategory'] = $subcat_name;
			$data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
			$data['main_content'] = 'user/product/offerings';
			//$data['aditional_scripts'] =['js/product.js'];
			$this->asignSidebarData($data);
			
			$this->load->view('user/includes/template', $data);
	
		}
	}

    function asignSidebarData(&$data) {

        $data['first_name'] = $this->site_data['first_name'];
        $data['second_name'] = isset($this->site_data['second_name']) ? $this->site_data['second_name'] : null;
        $data['ownumber'] = $this->site_data['ow'];
        $role = $this->session->userdata('role');
        $data['role'] = $role;
        $data['categories'] = $this->product_model->get_active_categories($role);
        $data['sub_categories'] = $this->product_model->get_active_subcategories($data['categories'], $role);
    }

	function product($params){
		$cat = $this->uri->segment(2);
		if (empty($params)) {
			// we were expecting a subcat slug here, but show all products then
			$this->show_offerings($cat);
			//exit();
		} else if (count($params) == 1) {
				$subcat = $params[0];
				$this->show_offerings($subcat, $cat);
		}
	}

	function product_details($p_slug, $c_slug) {
		echo "p: $p_slug c: $c_slug";
		$product_data = $this->product_model->get_product_data_where_cat($p_slug, $c_slug);
		if (empty($product_data) ) {
			echo '404, no such product.';
			exit();
		}

		$data['sidebar'] = FALSE;
		$data['main_content'] = 'user/product/product';
		$data['product_data'] = $product_data;
		$this->load->view('user/includes/template', $data);
	}

	function index($slug = '') {
		echo $slug . ' ';
	}

	function is_logged_in() {
		$is_logged_in = $this->session->userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != true) {
			/* echo "You don't have permission to access this page. ";
			echo '<a href="../login">Login</a>';
			die(); */
			redirect('login');
		}
	}

	function final_checkout()
	{	
		//echo '<pre>';print_r($_POST);die;
		$this->session->set_userdata('payment_error', '');
		$this->site_data['payment_error'] = '';

		$product_id = '';
		$payment_methods = '';
        $choose_cycle = '';
        $product_data = '';

        $acc_username = '';
        $acc_password = '';
        $realm = '';

        $user_id = false;

		if (isset($_POST['product_id'])) {

            $product_id = strip_tags(mysql_real_escape_string($_POST['product_id']));
            $product_id = trim($product_id);


            if (isset($_POST['choose_cycle'])) {

                $choose_cycle =  strip_tags(mysql_real_escape_string($_POST['choose_cycle']));
                $choose_cycle = trim($choose_cycle);
            }

			$payment_methods = $this->product_model->get_payment_methods($product_id, strtolower($choose_cycle));
			if (isset($_POST['username'])) {
				$acc_username = strip_tags(mysql_real_escape_string($_POST['username']));
                $acc_username = trim($acc_username);
			}else{
				$acc_username = '';
			}
			if (isset($_POST['password'])) {
				$acc_password = strip_tags(mysql_real_escape_string($_POST['password']));
                $acc_password = trim($acc_password);
			}else{
				$acc_password = '';
			}
			
			if (isset($_POST['realm'])) {
				$realm = strip_tags(mysql_real_escape_string($_POST['realm']));
                $realm = trim($realm);
			} else {
                $realm = '';
            }


			$product_data = array();
			$pro_da = $this->product_model->get_product_data($product_id);

			if ((isset($pro_da['pro_rata_option'])) && (isset($pro_da['price']))) {
				$pr_option = strip_tags(mysql_real_escape_string($pro_da['pro_rata_option']));
                $pr_option = trim($pr_option);

				$price = strip_tags(mysql_real_escape_string($pro_da['price']));
                $price = trim($price);

				$pro_rata = $this->product_model->get_pro_rate_price($pr_option, $price);
			} else {
				$pro_rata = 0.00;
			}
			$pro_da['pro_rata_extra'] = $pro_rata;

            /* !*/array_push($product_data, $pro_da);
			
			$user_id = $this->membership_model->get_user_id($this->site_data['username']);

            $data['billing_data'] = $this->membership_model->get_billing_data($user_id);
            // Need for PayFast AJAX
            $data['account_username'] = $acc_username;
            $data['account_password'] = $acc_password;
            $data['realm'] = $realm;
            // choose_cycle	Monthly,  password	, product_id	20, realm	mynetwork.co.za
		}
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // amount / item_name / item_description

        $payment_info['item_name'] = $pro_da['name'];
        $payment_info['item_description'] = '';
        $payment_info['discount'] = $this->site_data['discount'];
        $payment_info['price'] = $pro_da['price'];
        $payment_info['pro_price'] = $pro_da['pro_rata_extra'];

        $username = $this->site_data['username'];

        $data_for_payfast = $this->payfast_model->prepare_final_checkout($user_id, $username, $payment_info);
        $sandbox_data_for_payfast = $this->payfast_model->prepare_final_checkout($user_id, $username, $payment_info, "SANDBOX");

        $pre_live_signature_for_payfast = $this->payfast_model->pre_signature($data_for_payfast);
        $pre_sandbox_signature_for_payfast = $this->payfast_model->pre_signature($sandbox_data_for_payfast, 'SANDBOX');


        $order_data = array(

            'account_username' => $acc_username,
            'account_password' => $acc_password,
            'realm'            => $realm,
            'choose_cycle'     => $choose_cycle,
            'product_id'       => $product_id,
            'payment_type'     => 'credit_card'
        );

        $data['order_data_array'] = $order_data;
        $order_signature = $this->payfast_model->generate_order_signature($order_data);

       // var_dump($order_signature);
        $data['order_signature'] = $order_signature;

      //  var_dump($pre_live_signature_for_payfast);
       // var_dump($pre_sandbox_signature_for_payfast);

        $data['username'] = $username;
        $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
        $data['live_payfast_host']   = $this->payfast_model->live_host;
        $data['payfast_data'] = $data_for_payfast;
        $data['sandbox_payfast_data'] = $sandbox_data_for_payfast;

        $data['pre_sandbox'] = $pre_sandbox_signature_for_payfast;
        $data['pre_live']    = $pre_live_signature_for_payfast;

// ----------------------------------------------------------------------------------------------------------------
 /*
        echo "<pre>";
        echo "live

        ";
        var_dump($data_for_payfast);
        echo "</pre>";
        echo "<hr/>";
        echo "<pre>";
        echo "sandbox

        ";
        var_dump($sandbox_data_for_payfast);
        echo "</pre>";
*/
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$billing_cycles = $this->product_model->get_billing_cycles();
        /* !*/$data['choose_cycle'] = $choose_cycle;
        /* !*/$data['payment_methods'] = $payment_methods;
        /* !*/$data['billing_cycles'] = $billing_cycles;
        /* !*/$data['cart_product_data'] = $product_data;
        /* !*/$data['sidebar'] = TRUE;
        /* !*/$data['navbar'] = TRUE;
        $data['aditional_scripts'] = ['js/checkout_all.js'];
        /* !*/$data['main_content'] = 'user/product/checkout_all';

       /*
        echo "<pre>";

        print_r($data);
        echo "</pre>";
    */
		$this->load->view('user/includes/template', $data);
		
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

	function process_cart() 
	{

        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);

        if (!isset($this->order_model))
            $this->load->model('admin/order_model');

        $number = '';

        // get data from request and session
		$user = $this->site_data['username'];
		$product = $this->session->userdata('cart');  // product ID
		$payment_method = !empty($_POST['payment_method']) ? $_POST['payment_method'] : null;  // payment string
		$price = !empty($_POST['price']) ? $_POST['price'] : null;  // flaot p.  price

        $number = $this->membership_model->get_number($user);
        // --------------------------------------------------------------------------------------



		$auto = $this->product_model->get_is_auto($product);//get auto_creation from products  (TRUE or FALSE)
		$product_name = $this->product_model->get_product_name($product);  // product name STRING


		$data = array(
			'username' => $user,
			'product_id' => $product,
			'acc_username' =>!empty($_POST['acc_username']) ? $_POST['acc_username'] : $this->get_rand_str(),
			'acc_password' => !empty($_POST['acc_password']) ? $_POST['acc_password'] : $this->get_rand_str(),
			'payment_type' => $payment_method,
			'choose_cycle' => !empty($_POST['choose_cycle']) ? $_POST['choose_cycle'] : 'Monthly',
            'avios_code' => 'OPNZAUBAMR',
		);

        $choose_cycle = strtolower($data['choose_cycle']);
        $acc_realm = !empty($_POST['acc_realm']) ? $_POST['acc_realm'] : 'none';


        // INSERT ORDER
        $order_id = $this->product_model->insert_order($data);
        $additional_message = '';


        if ($auto) {

            $isdl_create_result = false;
            // ~~~~~~~~~~ isdsl create ~~~~~~~~~~~~~~~~~~~~~
            $this->load->model("network_api_handler_model");

            $order_data = array('account_username' => $data['acc_username'], 'realm' => $acc_realm);
            $isdl_class = $this->product_model->get_class_by_product_id($data['product_id']);
            $pass = $data['acc_password'];

            $new_account_data = $this->product_model->get_order_data($order_id);
            $comment = $new_account_data['account_comment'];//get account_comment form orders
            $email = $this->membership_model->get_email($user);


            $creation_result = $this->network_api_handler_model->add_new_realm_user($order_data, $isdl_class, $pass, $comment, $email);


            // ~~~~~~~~~~~~~ network_api_handler divider ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
/* OLD ISDSL creation code
            $realm_data = $this->realm_model->get_realm_data_by_name($acc_realm);

            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];
            $realm = $acc_realm;
            $sess = 0;


            // ISDSL connect
            //$sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id   //#A
            $sessFull = $this->is_classes->is_connect_new_full($rl_user, $rl_pass);
            //$sessFull = $this->is_classes->is_connect_new_curl($rl_user, $rl_pass, false);
            $sess = $sessFull->strSessionID;

//#DEBUG session full information + session only --------------------
//            var_dump($sessFull);
//            echo "<hr/>";
//            var_dump($sess);
//            echo "<hr/>";
//#------------------------------------------------------------------
//            die();
/*  OLD IDSL creation code

            $new_account_data = $this->product_model->get_order_data($order_id);
            $comment = $new_account_data['account_comment'];//get account_comment form orders
            $class = $this->product_model->get_class_by_product_id($data['product_id']);

            // Add to ISDSL
            $acc_realm_user = $data['acc_username'] . '@' . $realm;
            $ac_email = $this->membership_model->get_email($user);


            //$resp = $this->is_classes->add_realm_new($sess, $class, $data['acc_username'], $data['acc_password'], $comment, $ac_email);//#A

            // HIDE for DEBUG!
            $resp = $this->is_classes->add_realm_new_full($sess, $class, $data['acc_username'], $data['acc_password'], $comment, $ac_email);//#A
            //$resp = 1;
*/
//#DEBUG create account + show new username ------------------------
//            var_dump($acc_realm_user);
//            echo "<hr/>";
//            var_dump($resp);
//# -----------------------------------------------------------------



//            die();
//-----------------------------------------------------------------------------------------------------

// ----------------------------------------------------------------------------------------------------
// account created here

            $resp = $creation_result['result'];
            $ac_email = $email;
            $realm = $acc_realm;
            $acc_realm_user = $data['acc_username'] . '@' . $realm;


            if ($resp == true)
                $isdl_create_result = true;


            // #######################################
            if ($isdl_create_result){

                // activate insert
                $user_name_nice = $this->membership_model->get_user_name_nice($user);//get full name from membership
                $this->order_model->set_activated($order_id);
                $this->order_model->email_activation($ac_email, $product_name, $data['acc_username'], $realm, $data['acc_password'], $user_name_nice);

                //   SMS
                if (!empty($number)) {
                    $data_array = [
                        'service' => 'adsl',
                        'username' => $user_name_nice,
                        'prod_username' => $acc_realm_user,
                        'prod_pass' => $data['acc_password'],
                        'product_name' => $product_name
                    ];
                    $sms_response = $this->sms_model->sms_activation_fibre_order($data_array, $number);
                }

                if ($choose_cycle == 'once-off'){

                    //set  pending . HIDE for DEBUG !
                    // replace by network_api_handler model
                    //! $pending_resp = $this->is_classes->set_pending_update_new($sess, $acc_realm_user, 'nosvc');
                    $cancellationDate = date("Y-m-1", strtotime("+ 1 month"));
                    $this->network_api_handler_model->cancel_account($order_data,  $cancellationDate);
                }
            } else {
                    // handle error
                    /* OLD ISDL code
                    $error_message = '';
                    switch ($resp){
                        case '5' :  $error_message = 'Failure: Invalid session identifier supplied'; break;
                        case '8' :  $error_message = 'Invalid class'; break;
                        case '11' : $error_message = 'Username exists'; break;

                    }
                    */
                    $additional_message = $creation_result['user_message'];
                    // use for debug
                    // $additional_message=$creation_result['message'];
            }
        }
//        die();

        // 	$order_id = $this->product_model->insert_order($data, 'active');

		$invoice_id = $this->user_model->save_invoices($order_id, $user);
		$pdf_id = $this->getPDF($invoice_id, $user, $product_name, $price);


		$this->order_model->email_invoices_individual($user, $pdf_id);
        if ($user != 'test-vvv')                                            // For TESTS
	 	    $this->order_model->email_ceo_product($user, $product_name, $payment_method); // TODO : !PROD



		$billing_data = array(
			'name_on_card' => !empty($_POST['name_on_card']) ? $_POST['name_on_card'] : null,
			'card_num' => !empty($_POST['card_num']) ? $_POST['card_num'] : null,
			'cvc' => !empty($_POST['cvc']) ? $_POST['cvc'] : null,
			'expires_month' => !empty($_POST['expires_month']) ? $_POST['expires_month'] : null,
			'expires_year' => !empty($_POST['expires_year']) ? $_POST['expires_year'] : null,
			'bank_name' => !empty($_POST['bank_name']) ? $_POST['bank_name'] : null,
			'bank_account_number' => !empty($_POST['bank_account_number']) ? $_POST['bank_account_number'] : null,
			'bank_account_type' => !empty($_POST['bank_account_type']) ? $_POST['bank_account_type'] : null,
			'bank_branch_code' => !empty($_POST['bank_branch_code']) ? $_POST['bank_branch_code'] : null,
		);
		$this->db->update('billing', $billing_data, array('username' => $user));


		$this->session->set_flashdata('product_name', $product_name);
		$this->session->set_flashdata('auto_creation', $auto);
		$this->session->set_flashdata('payment_method', $payment_method);
        $this->session->set_flashdata('additional_message', $additional_message);


		redirect('product/congratulations');
	}


    // debug PDF invoice
    function debugPDF(){

        show_404();
        die();
        $invoice_id = '54805721';
        $username = 'test-vvv';
        $product_name = $this->product_model->get_product_name(44);
        $price = "45.56";


        $this->getPDF($invoice_id, $username, $product_name, $price);


    }


	function getPDF($invoice_id, $username, $product_name, $price)
	{	
		$this->load->model('admin/user_model');

        $user_data = $this->user_model->get_user_data($username);
        $first_name = $user_data['user_settings']['first_name'];
        $last_name = $user_data['user_settings']['last_name'];
		
		$this->load->library('tfpdf/MC_Table');
		$pdf=new MC_Table();

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
//		$image = base_url().'img/main.png';
		$image = '/home/home/public_html/img/main.png';
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
	    $pdf->Cell(20,3, INVOICE_ORGANIZATION_ID.$vat_number,'',true);
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
	
	function get_user_billing_info($username){
		$this->load->model('user/user_model');
		$user_list = $this->user_model->get_user_data($username);
		$result = $user_list['user_billing'];
		if($result){

            if (!empty($result['billing_name'])){
                $billing_name = $result['billing_name'];
            } else {
                return false;
            }

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
	
	function get_open_ISP(){
		$result = $this->user_model->get_open_ISP();
		return $result;
	}


    function payfast_success(){


        $username = $this->session->userdata('username');

        $order_row = $this->order_model->get_last_order_by_username($username);

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

        //  $data['product_name'] = $product_name;
        //  $data['auto'] = $auto;

        $this->session->set_userdata('cart', '');
        $this->site_data['cart'] = '';

        $data['sidebar'] = TRUE;
        $data['navbar'] = TRUE;
        $data['main_content'] = 'user/product/congratulations';
        $this->load->view('user/includes/template', $data);
    }

    function payfast_failed(){

        //var_dump($_REQUEST);
        // $data['message'] = 'fail';
        //  $data['product_name'] = $product_name;
        //  $data['auto'] = $auto;
        $message_data = array(
            'id' => 10,
        );
        $data['message'] = $this->message_model->get_message($message_data);


        $data['sidebar'] = TRUE;
        $data['navbar'] = TRUE;
        $data['main_content'] = 'user/product/congratulations';
        $this->load->view('user/includes/template', $data);

    }

	function congratulations()
	{

        $username = $this->session->userdata('username');

		$auto = $this->session->flashdata('auto_creation');
		$product_name = $this->session->flashdata('product_name');
		$payment_method = $this->session->flashdata('payment_method');
        $additional_message = $this->session->flashdata('additional_message');


        $order_message = '';
        if (empty($additional_message) && $auto){

            /*
            $message_data = array(
                'id' => 9,
            );
            */

            $order_message = AUTO_CREATE_SUCCESS_MESSAGE;
            $order_row = $this->order_model->get_last_order_by_username($username);

            if ($order_row != false){

                $order_message .= "<br/>";
               // $order_message .= "Order details : ";
                $order_message .= "<br/> Username : " . $order_row['account_username'] . "@" . $order_row['realm'];
                $order_message .= "<br/> Password : " . $order_row['account_password'];

            }

        } elseif($payment_method == 'credit_card'){
			$message_data = array(
				'id' => 5,
			);
		}elseif ($payment_method == 'eft'){
			$message_data = array(
				'id' => 6,
			);
		}elseif ($payment_method == 'debit_order'){
			$message_data = array(
				'id' => 7,
			);
		}elseif ($payment_method == 'credit_card_auto'){
			$message_data = array(
				'id' => 8,
			);
		}
        $message = '';
        if (!empty($message_data))
	    	$message = $this->message_model->get_message($message_data);

        if (!empty(  $order_message ))
            $message .= " " . $order_message;



        if ( ($payment_method == 'eft') && !$auto)
            $message =  EFT_MESSAGE_FOR_MANUAL;

		//if (!empty($additional_message))
        //    $message .= " " . $additional_message;

		$this->session->set_userdata('cart', '');
		$this->site_data['cart'] = '';
		$data['price'] = $this->session->userdata('total_price');
		$data['message'] = $message;
		$data['product_name'] = $product_name;
		$data['auto'] = $auto;
		$data['sidebar'] = TRUE;
		$data['navbar'] = TRUE;
		$data['main_content'] = 'user/product/congratulations';
		
		$this->load->view('user/includes/template', $data);
	}

	function clear_cart() {


		$this->product_model->clear_cart();
		$this->site_data['cart'] = '';
		$data['sidebar'] = TRUE;
		$data['main_content'] = 'user/product/clear_cart';
		$this->load->view('user/includes/template', $data);
	}

	function checkout($product_id = '') 
	{
        // check billing info
        $username = $this->session->userdata('username');
        $billing_info_check = $this->get_user_billing_info($username);
        if(!$billing_info_check) {

            $billing_data = $this->user_model->get_billing_data($username);
            if($billing_data){
                $data['user_data']['user_billing'] = $billing_data;
            }else{
                $data['user_data']['user_billing'] = '';
            }


            $msg = "Please fill in your billing information first.";
            $data['info_message'] = $msg;
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['main_content'] = 'user/billing';
            $this->load->view('user/includes/template', $data);
            return;
        }

        $base_url = $this->config->base_url();
		// For once we use the product ID! Exciting!
		if (trim($product_id) == '') {
			// Now we just show the whole checkout thing.
			$this->final_checkout();
		} else {
			$product_data = $this->product_model->get_product_data($product_id);
			$billing_cycles = $this->product_model->get_billing_cycle_exist($product_id);
			$data['product_data'] = $product_data;
			//$billing_cycles = $this->product_model->get_billing_cycles();
			$data['billing_cycles'] = $billing_cycles;


			$this->session->set_userdata('cart', $product_id);
			
			if (isset($product_data['pro_rata_option'])) {
				$pr_option = $product_data['pro_rata_option'];
				$price = $product_data['price'];
				$pro_rata = $this->product_model->get_pro_rate_price($pr_option, $price);
			} else {
				$pro_rata = 0.00;
			}
			
			$realm = $this->product_model->get_product_realm($product_id);

			$data['product_data']['realm'] = $realm;
			$data['product_data']['pro_rata_extra'] = $pro_rata;
			$data['product_data']['product_id'] = $product_id;
			$data['payment_error'] = $this->session->userdata('payment_error');
            $data['base_ajax_url'] = $base_url . "user/check_username";
			
			$data['sidebar'] = TRUE;
			$data['navbar'] = TRUE;
			$data['aditional_scripts'] = ['js/checkout.js'];
			$data['main_content'] = 'user/product/checkout';
			$this->load->view('user/includes/template', $data);
		}
	}
	
	function view($sub_category_id =''){
		//$sub_category_data = $this->category_model->get_subcategory_data($sub_category_id);
		//$data['sub_category_data']['all_sub_category'] = $sub_category_data;
		$sub_subcategory_data = $this->category_model->get_sub_category_name($sub_category_id);
		$data['product_data']['name'] = $sub_subcategory_data;
		$data['sidebar'] = TRUE;
		$data['main_content'] = 'user/product/product';
		$this->load->view('user/includes/template', $data);
		
	}



    // DEBUG


 ///get_last_transaction()

    function debug_test(){
    $arr = array(

        'm_payment_id' => '9',
        'pf_payment_id' => '146697',
        'payment_status' => 'COMPLETE',
        'item_name' => '10GB Freedom Capped Unshaped',
        'item_description' => '',
        'amount_gross' => '24.50',
        'amount_fee' => '-0.56',
        'amount_net' => '23.94',
        'custom_str1' => '',
        'custom_str2' => '',
        'custom_str3' => '',
        'custom_str4' => '',
        'custom_str5' => '',
        'custom_int1' => '',
        'custom_int2' => '',
        'custom_int3' => '',
        'custom_int4' => '',
        'custom_int5' => '',
        'name_first' => 'Test',
        'name_last' => 'User 01',
        'email_address' => 'sbtu01@payfast.co.za',
        'merchant_id' => '10000100',
       'signature' => 'd649e9cbaea93bc8358ca8a07ac56dd9',


    );

    //  $result = $this->payfast_model->generate_signature($arr);

        echo "<pre>";
      //  echo $result;
        echo "</pre>";

    }


    function debug_check_account_info(){

        // debug_check_account_info/4686
        // 4686
        $username =  'test-vv-3230976haki81faf4';
        // $username =  'jjkaksduahhjw';
        $realm = 'mynetwork.co.za';
        //$realm_data = $this->order_model->get_realm_data_by_order_id($order_id);

        $realm_data = $this->realm_model->get_realm_data_by_name($realm);
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $lm = explode('@', $realm_data['user']);
        $realm = $lm[1];
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);


       // $account_info = $this->is_classes->delete_account_new($sess, $username . "@" . $realm);
        $account_info = $this->is_classes->getAccountInfo_full_new($sess, $username . "@" . $realm);

        echo "<pre>";
        print_r($account_info);
        echo "</pre>";
       // echo  "<br/>" . $account_info['intReturnCode'];

    }


    function debug_check_account_pend_update(){


        // debug_check_account_info/4686
        // 4686
        $username =  'test-vv-3230976haki81faf4';
        $realm = 'mynetwork.co.za';
        //$realm_data = $this->order_model->get_realm_data_by_order_id($order_id);

        $realm_data = $this->realm_model->get_realm_data_by_name($realm);
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $lm = explode('@', $realm_data['user']);
        $realm = $lm[1];
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

        //$resp = 'set update empty';
       // $account_pend_info = 'get update empty';

        //  var_dump($rl_user);
        // echo "<br/>" . $username . "@" . $realm . "<br/>";

        //  $resp = $this->is_classes->set_pending_update_new($sess, $username . "@" . $realm, 'nosvc');
        $account_pend_info = $this->is_classes->get_pending_update_new($sess, $username . "@" . $realm);

       // var_dump($resp);

        echo "<pre>";
        print_r($account_pend_info);
        echo "</pre>";

    }















}
