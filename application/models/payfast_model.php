<?php
class Payfast_model extends CI_Model
{


    //  private $test = true;  // true | false

    // HOST !!
    public $sandbox_host = 'sandbox.payfast.co.za';
    public $live_host    = 'www.payfast.co.za';

    private $ajax_confirm = "http://home.openweb.co.za/payfast/pre-valid";

    private $valid_hosts = array(
        'www.payfast.co.za',
        'sandbox.payfast.co.za',
        'w1w.payfast.co.za',
        'w2w.payfast.co.za',
    );


    // User Credentials
    private $sandbox_user_email  = "sbtm01@payfast.co.za";
    private $sandbox_merchant_id = "10000100";
    private $sandbox_merchant_key = "46f0cd694581a";


    private $user_email = "ceo@openweb.co.za";
    private $merchant_id = '10620095';
    private $merchant_key = 'v2c9e5sv4j3r6';


    // -------------------------------------------
    private $passphrase = 'Kjn782BhuAxzva';
    // private $passphrase = '';


    private $pre_sandbox_pass = "iJMaC(ala";
    private $pre_live_pass    = "jU5QA(@ac";
    private $order_pass = "8kmNVual";



    //  public $base_url = 'http://home.openweb.co.za';
    // public $base_url = 'http://home.keoma.local';
    public $base_url = '';

    //  public $prod_base_url     = 'http://home.openweb.co.za';
    //  public $stage_base_url    = '';


    public  $return_url   = 'product/payfast_success';
    public  $cancel_url   = 'product/payfast_failed';
    private $notify_url   = 'payfast/notify';

    public $topup_return_url = 'user/payfast_success';
    public $topup_cancel_url = 'user/payfast_failed';
    public $topup_item_description = 'topup-item';



    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

        if (!isset($this->membership_model)) {

            $this->load->model('membership_model');
        }

        if (!isset($this->helper)) {

            $this->load->helper('url');
        }

        if (!isset($this->is_classes)) {

            $this->load->model('user/is_classes');
        }


        if (!isset($this->product_model)) {

            $this->load->model('user/product_model');
        }

        if (!isset($this->order_model)) {

            $this->load->model('user/order_model');
        }

        if (!isset($this->user_model)) {

            $this->load->model('user/user_model');
        }

        $this->base_url = base_url();
    }



    function prepare_final_checkout($user_id, $username,  $order_data, $sand_string = null)
    {


        if (isset($user_id) && ($user_id == false))
            return false;


        $payment_data['item_name'] =  $order_data['item_name'];  // (100 chars) (255 database )
        $payment_data['item_description'] = $order_data['item_description']; // (100 chars) (255 database  )


        // 100 chars validations
        $str_item_name = strlen($payment_data['item_name']);
        if ($str_item_name > 100) {
            $payment_data['item_name'] = substr($payment_data['item_name'], 0, 100);
        }

        $str_item_desc = strlen($payment_data['item_description']);
        if ($str_item_desc > 100) {
            $payment_data['item_description'] = substr($payment_data['item_description'], 0, 100);
        }


        $discount = $order_data['discount'];
        $price    = $order_data['price'];
        $pro_price = $order_data['pro_price'];

        $user_data['first_name'] =  $this->membership_model->get_name($username); // (100 chars) (55 database)
        $user_data['last_name']  =  $this->membership_model->get_second_name($username); //(100 chars) (55 database)
        $user_data['email']      =  $this->membership_model->get_email($username); //  (100 chars) (55 database)


        // Internal payment ID;
        $last_payment = $this->get_last_transaction();
        if (empty($last_payment)) {

            $payment_data['internal_payment_id'] = 1;
        } else {

            $payment_data['internal_payment_id'] = $last_payment + 1;
        }



        $amount_array = $this->count_amount($price, $pro_price, $discount);
        $payment_data['amount'] = $amount_array['pro_price']; // get PRO

        // var_dump($user_data);
        // die;



        if (isset($sand_string) && ($sand_string == 'SANDBOX')) {

            $payfast_form_data = $this->sandbox_generate_form_data($user_data, $payment_data);
            $payfast_form_data['signature'] =  $this->generate_signature($payfast_form_data);
        } else {

            $payfast_form_data =  $this->generate_form_data($user_data, $payment_data);
            $payfast_form_data['signature'] =  $this->generate_signature($payfast_form_data, $this->passphrase);
        }

        // var_dump($payfast_form_data); die;
        return $payfast_form_data;
    }
    function prepare_topup_telkon_final_checkout($user_id, $username, $order_data, $sand_string = null)
    {
        if (isset($user_id) && ($user_id == false))
            return false;
        $payment_data['item_name'] =  $order_data['item_name'];  // (100 chars) (255 database )
        $payment_data['item_description'] = $order_data['item_description']; // (100 chars) (255 database  )

        // 100 chars validations
        $str_item_name = strlen($payment_data['item_name']);
        if ($str_item_name > 100) {
            $payment_data['item_name'] = substr($payment_data['item_name'], 0, 100);
        }

        $str_item_desc = strlen($payment_data['item_description']);
        if ($str_item_desc > 100) {
            $payment_data['item_description'] = substr($payment_data['item_description'], 0, 100);
        }
        $discount = $order_data['discount'];
        $price    = $order_data['price'];
        $pro_price = $order_data['pro_price'];
        $user_data['first_name'] =  $this->membership_model->get_name($username); // (100 chars) (55 database)
        $user_data['last_name']  =  $this->membership_model->get_second_name($username); //(100 chars) (55 database)
        $user_data['email']      =  $this->membership_model->get_email($username); //  (100 chars) (55 database)
        // Internal payment ID;
        $last_payment = $this->get_last_transaction();
        if (empty($last_payment)) {

            $payment_data['internal_payment_id'] = 1;
        } else {

            $payment_data['internal_payment_id'] = $last_payment + 1;
        }
        $amount_array = $this->count_amount($price, $pro_price, $discount);
        $payment_data['amount'] = $amount_array['pro_price']; // get PRO

        //set telkom session values
        $this->session->set_userdata('telkom_topup_data', $payment_data);
        if (isset($sand_string) && ($sand_string == 'SANDBOX')) {

            $payfast_form_data = $this->sandbox_generate_topup_form_data($user_data, $payment_data, "log");
            $payfast_form_data['signature'] =  $this->generate_signature($payfast_form_data);
        } else {

            $payfast_form_data =  $this->generate_topup_form_data($user_data, $payment_data);
            $payfast_form_data['signature'] =  $this->generate_signature($payfast_form_data, $this->passphrase);
        }
        return $payfast_form_data;
    }

    function prepare_topup_final_checkout($user_id, $username,  $order_data, $sand_string = null)
    {
        if (isset($user_id) && ($user_id == false))
            return false;
        $payment_data['item_name'] =  $order_data['item_name'];  // (100 chars) (255 database )
        $payment_data['item_description'] = $order_data['item_description']; // (100 chars) (255 database  )
        // 100 chars validations
        $str_item_name = strlen($payment_data['item_name']);
        if ($str_item_name > 100) {
            $payment_data['item_name'] = substr($payment_data['item_name'], 0, 100);
        }

        $str_item_desc = strlen($payment_data['item_description']);
        if ($str_item_desc > 100) {
            $payment_data['item_description'] = substr($payment_data['item_description'], 0, 100);
        }

        $discount = $order_data['discount'];
        $price    = $order_data['price'];
        $pro_price = $order_data['pro_price'];
        $user_data['first_name'] =  $this->membership_model->get_name($username); // (100 chars) (55 database)
        $user_data['last_name']  =  $this->membership_model->get_second_name($username); //(100 chars) (55 database)
        $user_data['email']      =  $this->membership_model->get_email($username); //  (100 chars) (55 database)
        // Internal payment ID;
        $last_payment = $this->get_last_transaction();
        if (empty($last_payment)) {

            $payment_data['internal_payment_id'] = 1;
        } else {

            $payment_data['internal_payment_id'] = $last_payment + 1;
        }



        $amount_array = $this->count_amount($price, $pro_price, $discount);
        $payment_data['amount'] = $amount_array['pro_price']; // get PRO

        //var_dump($payfast_form_data);
        // die;



        if (isset($sand_string) && ($sand_string == 'SANDBOX')) {

            $payfast_form_data = $this->sandbox_generate_topup_form_data($user_data, $payment_data, "log");
            $payfast_form_data['signature'] =  $this->generate_signature($payfast_form_data);
        } else {

            $payfast_form_data =  $this->generate_topup_form_data($user_data, $payment_data);
            $payfast_form_data['signature'] =  $this->generate_signature($payfast_form_data, $this->passphrase);
        }


        //var_dump($payfast_form_data); die;

        return $payfast_form_data;
    }



    function write_log($str)
    {

        $log_file_path =  dirname(__FILE__)  . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "notify-log.txt";

        // $log_file_path = "../logs/notify-log.txt";
        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, "\n " . $str);
        fclose($log_handle);

        // echo $log_file_path;

        return;
    }




    function generate_form_data($user_data, $payment_data)
    {

        $data = array(

            /* Merchant Details */
            'merchant_id'  => $this->merchant_id,   //!
            'merchant_key' => $this->merchant_key, // !
            'return_url'   => $this->base_url . $this->return_url,  //	The URL where the user is returned to after payment has been successfully taken.
            'cancel_url'   => $this->base_url . $this->cancel_url, //The URL where the user should be redirected should they choose to cancel their payment while on the PayFast system.
            'notify_url'   => $this->base_url . $this->notify_url, //The URL which is used by PayFast to post the Instant Transaction Notifications (ITNs) for this transaction.

            /* Payer Details */
            'name_first' => $user_data['first_name'],  // First name of the payer/sender. (100 chars)
            'name_last'  => $user_data['last_name'],     // (100 chars)
            'email_address' => $user_data['email'], // (100 chars)


            'm_payment_id' => $payment_data['internal_payment_id'], //Unique payment ID to pass through to notify_url ||  Unique payment ID on the merchant's system.  (100 chars)
            'amount' => $payment_data['amount'],
            'item_name' => $payment_data['item_name'],  // The name of the item being charged for. (100 chars)
            'item_description' => $payment_data['item_description'],   // (255 char)
            //  'custom_int1' => '9586', //custom integer to be passed through   custom_int1..5
            //  'custom_str1' => 'custom string to be passed through with the transaction to the notify_url page'       custom_str1..5

        );


        return $data;
    }

    function sandbox_generate_form_data($user_data, $payment_data)
    {

        $data = array(


            /* Merchant Details */
            'merchant_id'  => $this->sandbox_merchant_id,   //!
            'merchant_key' => $this->sandbox_merchant_key, // !
            'return_url'   => $this->base_url . $this->return_url,  //	The URL where the user is returned to after payment has been successfully taken.
            'cancel_url'   => $this->base_url . $this->cancel_url, //The URL where the user should be redirected should they choose to cancel their payment while on the PayFast system.
            'notify_url'   => $this->base_url . $this->notify_url, //The URL which is used by PayFast to post the Instant Transaction Notifications (ITNs) for this transaction.

            /* Payer Details */
            'name_first'   => $user_data['first_name'],  // First name of the payer/sender. (100 chars)
            'name_last'    => $user_data['last_name'],     // (100 chars)
            'email_address' => $user_data['email'], // (100 chars)


            'm_payment_id' => $payment_data['internal_payment_id'], //Unique payment ID to pass through to notify_url ||  Unique payment ID on the merchant's system.  (100 chars)
            'amount' => $payment_data['amount'],
            'item_name' => $payment_data['item_name'],  // The name of the item being charged for. (100 chars)
            'item_description' => $payment_data['item_description'],   // (255 char)
            //  'custom_int1' => '9586', //custom integer to be passed through   custom_int1..5
            //  'custom_str1' => 'custom string to be passed through with the transaction to the notify_url page'       custom_str1..5

        );


        return $data;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // TOpUp


    function generate_topup_form_data($user_data, $payment_data)
    {

        $data = array(

            /* Merchant Details */
            'merchant_id'  => $this->merchant_id,   //!
            'merchant_key' => $this->merchant_key, // !
            'return_url'   => $this->base_url . $this->topup_return_url,  //	The URL where the user is returned to after payment has been successfully taken.
            'cancel_url'   => $this->base_url . $this->topup_cancel_url, //The URL where the user should be redirected should they choose to cancel their payment while on the PayFast system.
            'notify_url'   => $this->base_url . $this->notify_url, //The URL which is used by PayFast to post the Instant Transaction Notifications (ITNs) for this transaction.

            /* Payer Details */
            'name_first' => $user_data['first_name'],  // First name of the payer/sender. (100 chars)
            'name_last'  => $user_data['last_name'],     // (100 chars)
            'email_address' => $user_data['email'], // (100 chars)


            'm_payment_id' => $payment_data['internal_payment_id'], //Unique payment ID to pass through to notify_url ||  Unique payment ID on the merchant's system.  (100 chars)
            'amount' => $payment_data['amount'],
            'item_name' => $payment_data['item_name'],  // The name of the item being charged for. (100 chars)
            'item_description' => $payment_data['item_description'],   // (255 char)
            //  'custom_int1' => '9586', //custom integer to be passed through   custom_int1..5
            //  'custom_str1' => 'custom string to be passed through with the transaction to the notify_url page'       custom_str1..5

        );


        return $data;
    }

    function sandbox_generate_topup_form_data($user_data, $payment_data, $log = null)
    {

        $data = array(


            /* Merchant Details */
            'merchant_id'  => $this->sandbox_merchant_id,   //!
            'merchant_key' => $this->sandbox_merchant_key, // !
            'return_url'   => $this->base_url . $this->topup_return_url,  //	The URL where the user is returned to after payment has been successfully taken.
            'cancel_url'   => $this->base_url . $this->topup_cancel_url, //The URL where the user should be redirected should they choose to cancel their payment while on the PayFast system.
            'notify_url'   => $this->base_url . $this->notify_url, //The URL which is used by PayFast to post the Instant Transaction Notifications (ITNs) for this transaction.

            /* Payer Details */
            'name_first'   => $user_data['first_name'],  // First name of the payer/sender. (100 chars)
            'name_last'    => $user_data['last_name'],     // (100 chars)
            'email_address' => $user_data['email'], // (100 chars)


            'm_payment_id' => $payment_data['internal_payment_id'], //Unique payment ID to pass through to notify_url ||  Unique payment ID on the merchant's system.  (100 chars)
            'amount' => $payment_data['amount'],
            'item_name' => $payment_data['item_name'],  // The name of the item being charged for. (100 chars)
            'item_description' => $payment_data['item_description'],   // (255 char)
            //  'custom_int1' => '9586', //custom integer to be passed through   custom_int1..5
            //  'custom_str1' => 'custom string to be passed through with the transaction to the notify_url page'       custom_str1..5

        );

        if (isset($log)) {
            $data['notify_url'] = "https://home.openweb.co.za/payfast/sand_notify";
        }

        return $data;
    }



    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    function count_amount($price, $pro_rate, $discount)
    {


        $discount = (100 - $discount) / 100;
        $full_price_with_discount = $price * $discount;
        $pro_price_with_discount = $pro_rate * $discount;

        $return_array = array(

            'full_price' => $full_price_with_discount,
            'pro_price'  => $pro_price_with_discount,

        );

        return $return_array;
    }


    function generate_signature($data_array, $passphrase = '')
    {


        $string_out = '';
        foreach ($data_array as $key => $val) {
            //   if(!empty($val) && ($key != 'signature'))
            if ($key != 'signature') {
                $string_out .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        // Remove last ampersand
        $final_string_out = substr($string_out, 0, -1);
        if (!empty($passphrase)) {
            $final_string_out .= '&passphrase=' . $passphrase;
        }

        $signature = md5($final_string_out); // 32 char;
        return $signature;
    }

    function get_last_transaction()
    {
        $this->db->select('internal_payment_id');
        $this->db->order_by('internal_payment_id', 'desc');
        $query = $this->db->get('payfast_response_transactions');
        $last_transaction_response  = $query->first_row('array');
        $last_transaction_response = $last_transaction_response['internal_payment_id'];



        $this->db->select('internal_payment_id');
        $this->db->order_by('internal_payment_id', 'desc');
        $query = $this->db->get('payfast_request');
        $last_transaction_request = $query->first_row('array');
        $last_transaction_request =  $last_transaction_request['internal_payment_id'];



        $last_transaction =  $last_transaction_response;
        if ($last_transaction_request > $last_transaction)
            $last_transaction = $last_transaction_request;



        return $last_transaction;
    }




    // ----------------------------------- ----------------------------------------



    function prepare_data(&$payment_response)
    {

        foreach ($payment_response as $key => $value) {

            $payment_response[$key] = mysql_real_escape_string(strip_tags($value));
        }
    }


    function host_validation($server_remote_addr, $sandbox_mode = null)
    {

        $return_array = array(
            'message' => '',
            'validation' => false,
        );

        $valid_ips = array();
        foreach ($this->valid_hosts as $hostname) {
            $ips = gethostbynamel($hostname);

            if ($ips !== false) {
                $valid_ips = array_merge($valid_ips, $ips);
            }
        }

        $valid_ips = array_unique($valid_ips);

        if (!in_array($server_remote_addr, $valid_ips)) {

            $return_array['message'] = "wrong remote addr ($server_remote_addr)";
            $return_array['validation'] = false;

            return $return_array;
        }

        $return_array['message'] = "";
        $return_array['validation'] = true;

        return  $return_array;
    }

    function signature_validation($data_array, $sandbox_mode = null)
    {

        $return_array = array(
            'message' => '',
            'validation' => false,
        );

        $pass = $this->passphrase;
        if (isset($sandbox_mode)  && ($sandbox_mode == 'SANDBOX')) {

            $pass = '';
        }


        $signature =  $this->generate_signature($data_array, $pass);
        if ($signature != $data_array['signature']) {

            $return_array['generated_signature'] = $signature;
            $return_array['response_signature']  = $data_array['signature'];
            $return_array['message'] = "wrong signature";
            $return_array['validation'] = false;

            return $return_array;
        }



        $return_array['message'] = "";
        $return_array['validation'] = true;

        return $return_array;
    }

    function curl_validation($host, $parameters)
    {

        $return_array = array(
            'message' => '',
            'validation' => false,
        );

        $url = 'https://' . $host . '/eng/query/validate';

        if (!in_array('curl', get_loaded_extensions())) {

            $return_array['message'] = "curl doesn't exist on the server";
            return $return_array;
        }

        // Create default cURL object
        $ch = curl_init();

        // Set cURL options - Use curl_setopt for freater PHP compatibility
        // Base settings
        curl_setopt($ch, CURLOPT_USERAGENT, PF_USER_AGENT);  // Set user agent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // Return output as string rather than outputting it
        curl_setopt($ch, CURLOPT_HEADER, false);             // Don't include header in output
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Standard settings
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
        curl_setopt($ch, CURLOPT_TIMEOUT, PF_TIMEOUT);
        /*
            if( !empty( $pfProxy ) )
            {
                curl_setopt( $ch, CURLOPT_PROXY, $proxy );
            }
           */

        // Execute CURL
        $response = curl_exec($ch);

        $curl_info    = curl_getinfo($ch);
        $curl_error1  = curl_errno($ch);
        $curl_error2  = curl_error($ch);


        if (!empty($curl_error2)) {

            $return_array['message'] = "curl exec error  [curl info] : $curl_info, [curl_code] : $curl_error1, [curl message] :  $curl_error2";
            $return_array['validation'] = false;

            return $return_array;
        }

        curl_close($ch);
        // https://www.payfast.co.za/developers/itn!


        $lines = explode("\r\n", $response);
        $verify_result = trim($lines[0]);

        if (strcasecmp($verify_result, 'VALID') != 0) {

            $return_array['message'] = 'curl validation status is not VALID';
            $return_array['validation'] = false;
        }


        $return_array['validation'] = true;
        return $return_array;
    }



    function sandbox_validation($server_remote_addr)
    {


        $sandbox_mode = "UNKNOWN";
        if ($server_remote_addr['merchant_id'] == $this->merchant_id) {

            $sandbox_mode = "LIVE";
        } elseif ($server_remote_addr['merchant_id'] == $this->sandbox_merchant_id) {

            $sandbox_mode = "SANDBOX";
        }

        return $sandbox_mode;
    }


    function full_validation($server_remote_addr, &$params, $sandbox_mode = null)
    {

        // host_validation
        $host_validation_result =  $this->host_validation($server_remote_addr, $sandbox_mode);
        /* REMOVE AFTER TEST */ // $host_validation_result['validation'] = true;
        if ($host_validation_result['validation'] == false) {

            $this->write_log($host_validation_result['message']);
            return $host_validation_result;
        }

        // pre_order_validation
        /*
         $pre_order_validation_result = $this->pre_order_validation();
         if ($pre_order_validation_result['validation'] = false ){

             return $pre_order_validation_result;
         }
         */

        // sandbox and host validation
        $sandbox_mode  = $this->sandbox_validation($params);
        $host = $this->live_host;
        if (!empty($sandbox_mode) && ($sandbox_mode == 'SANDBOX'))
            $host = $this->sandbox_host;

        $this->write_log("sandbox_mode : $sandbox_mode");
        $this->write_log("host : $host");


        // signature validation
        $signature_validation_result =  $this->signature_validation($params, $sandbox_mode);
        if ($signature_validation_result['validation'] == false) {

            $this->write_log($signature_validation_result['message']  . '|  generated : ' .
                $signature_validation_result['generated_signature'] . ' | original : ' .
                $signature_validation_result['response_signature']);
            return $signature_validation_result;
        }

        // curl validation
        $curl_validation_result =  $this->curl_validation($host, $params);
        if ($curl_validation_result['validation'] == false) {

            $this->write_log($curl_validation_result['message']);
            return $curl_validation_result;
        }

        // is it  unique payment validation ?


        $response_array = array(

            'message' => '',
            'validation' => '',
        );

        $response_array['validation'] = true;
        return $response_array;
    }

    // -- ------------- database

    function save_pre_transaction($param_array, $username, $user_id, $payfast_mode)
    {

        /*
        `internal_payment_id`  int NULL ,           -
        `product_name`  varchar(100) NULL ,         -
        `product_description`  varchar(100) NULL ,  -
        `amount`  varchar(50) NULL ,                  -
        `request_date`  timestamp NULL ,            -
        `response_date`  timestamp NULL ,           -
        `username`  varchar(250) NULL ,                -
        `user_id`  int NULL ,                       -
        `name_first`  varchar(100) NULL ,           -
        `name_last`  varchar(100) NULL ,                -
        `email_address`  varchar(100) NULL ,        -
        `payfast_mode`  varchar(10) NULL ,          -
        `status`  varchar(15) NULL                  -


          "merchant_id"   :"10000100",
        "merchant_key"  :"46f0cd694581a",
        "return_url"    :"http:\/\/home.keoma.local\/return.php",
        "cancel_url"    :"http:\/\/home.keoma.local\/cancel.php",
        "notify_url"    :"http:\/\/home.keoma.local\/payfast\/notify",
        "name_first"    :"vitalii-test",                    -
        "name_last"     :"lastN",
        "email_address" :"baf4mail@gmail.com",
         "m_payment_id":"1",                                -
         "amount":"32.585",                                 -
        "item_name":"10GB Freedom Capped Unshaped",         -
        "item_description":"",                              -
        "signature":"575c33868109ed27e6f5973677065061"

         */

        //  $exist_row = $this->load_pre_transaction($param_array['m_payment_id']);
        //  if (!empty ($exist_row))
        //      return true;


        $request_date = date("Y-m-d H:i:s");
        $data_from_params = array(

            'internal_payment_id'  =>  $param_array['m_payment_id'],
            'product_name'         =>  $param_array['item_name'],
            'product_description'  =>  $param_array['item_description'],
            'amount'               =>  $param_array['amount'],

            'request_date'         =>  $request_date,
            'response_date'        =>  '',

            'username'             =>  $username,
            'user_id'              =>  $user_id,
            'name_first'           =>  $param_array['name_first'],
            'name_last'            =>  $param_array['name_last'],

            'email_address'         => $param_array['email_address'],
            'payfast_mode'         =>  $payfast_mode,
            'status'               =>  '',

        );



        $result = $this->db->insert('payfast_request', $data_from_params);
        return $result;
    }

    function load_pre_transaction($internal_payment_id)
    {
        $this->db->where('internal_payment_id', $internal_payment_id);
        $query = $this->db->get('payfast_request');
        $result = $query->first_row('array');
        return $result;
    }

    function add_new_transaction($param_array, $status = null)
    {

        $exist_row = $this->load_transaction($param_array['m_payment_id']);
        if (!empty($exist_row))
            return "exist";

        $response_date = date("Y-m-d H:i:s");

        $data = [
            'internal_payment_id'  =>  $param_array['m_payment_id'],
            //'external_payment_id'  =>  $param_array['pf_payment_id'],

            'product_name'         =>  $param_array['item_name'],
            'product_description'  =>  $param_array['item_description'],

            'amount_gross'         =>  $param_array['amount'],
            //'amount_fee'           =>  $param_array['amount_fee'],
            //'amount_net'           =>  $param_array['amount_net'],

            'name_first'           =>  $param_array['name_first'],
            'name_last'            =>  $param_array['name_last'],
            'email_address'        =>  $param_array['email_address'],
            'merchant_id'          =>  $param_array['merchant_id'],

            'response_date'        =>  $response_date,
            //'payment_status'       =>  $param_array['payment_status'],
            'validation_status'    =>  $status,
            //'validation_message'   =>  $validation_message,
        ];

        $id = $this->db->insert('payfast_response_transactions', $data);
        return $id;
    }

    function save_transaction_reponse($param_array, $validation_status = null, $validation_message = null)
    {

        $exist_row = $this->load_transaction($param_array['m_payment_id']);
        if (!empty($exist_row))
            return true;



        $response_date = date("Y-m-d H:i:s");
        $data_from_params = array(

            'internal_payment_id'  =>  $param_array['m_payment_id'],
            'external_payment_id'  =>  $param_array['pf_payment_id'],

            'product_name'         =>  $param_array['item_name'],
            'product_description'  =>  $param_array['item_description'],

            'amount_gross'         =>  $param_array['amount_gross'],
            'amount_fee'           =>  $param_array['amount_fee'],
            'amount_net'           =>  $param_array['amount_net'],

            'name_first'           =>  $param_array['name_first'],
            'name_last'            =>  $param_array['name_last'],
            'email_address'        =>  $param_array['email_address'],
            'merchant_id'          =>  $param_array['merchant_id'],

            'response_date'        =>  $response_date,
            'payment_status'       =>  $param_array['payment_status'],
            'validation_status'    =>  $validation_status,
            'validation_message'   =>  $validation_message,

        );


        /*
          `internal_payment_id` int(11) DEFAULT NULL,       -
          `external_payment_id` int(11) DEFAULT NULL,       -
          `product_name` varchar(100) DEFAULT NULL,         -
          `product_description` varchar(100) DEFAULT NULL,  -
          `amount_gross` varchar(50) DEFAULT NULL,          -
          `amount_fee` varchar(50) DEFAULT NULL,            -
          `amount_net` varchar(50) DEFAULT NULL,            -
          `name_first` varchar(100) DEFAULT NULL,           -
          `name_last` varchar(100) DEFAULT NULL,            -
          `email_address` varchar(100) DEFAULT NULL,        -
          `merchant_id` varchar(80) DEFAULT NULL,           -
          `response_date` timestamp NULL DEFAULT NULL,      -
          `payment_status` varchar(20) DEFAULT NULL,        -
          `validation_status` varchar(80) DEFAULT NULL      -

        ----------------------------------------------------------

        $payment_response['m_payment_id']        -
        $payment_response['pf_payment_id']       -
        $payment_response['payment_status']      -

        $payment_response['item_name']           -
        $payment_response['item_description']    -

        $payment_response['amount_gross']        -
        $payment_response['amount_fee']          -
        $payment_response['amount_net']          -

        $payment_response['custom_str1']
        $payment_response['custom_str2']
        $payment_response['custom_str3']
        $payment_response['custom_str4']
        $payment_response['custom_str5']

        $payment_response['custom_int1']
        $payment_response['custom_int2']
        $payment_response['custom_int3']
        $payment_response['custom_int4']
        $payment_response['custom_int5']

        $payment_response['name_first']         -
        $payment_response['name_last']          -
        $payment_response['email_address']      -
        $payment_response['merchant_id']        -
        $payment_response['signature']

        */
        $this->db->insert('payfast_response_transactions', $data_from_params);
        return $this->db->insert_id();
    }

    function update_lte_reponse($param_array, $validation_status = null, $validation_message = null)
    {

        $response_date = date("Y-m-d H:i:s");

        $data_from_params = array(

            'external_payment_id' => $param_array['pf_payment_id'],

            'amount_fee' => $param_array['amount_fee'],
            'amount_net' => $param_array['amount_net'],

            'response_date' => $response_date,
            'payment_status' => $param_array['payment_status'],
            'validation_status' => $validation_status,
            'validation_message' => $validation_message,

        );

        $this->db->where('internal_payment_id', $param_array['m_payment_id']);
        $res = $this->db->update('payfast_response_transactions', $data_from_params);

        return $res;
    }

    function load_transaction($internal_payment_id)
    {
        $this->db->where('internal_payment_id', $internal_payment_id);
        $query = $this->db->get('payfast_response_transactions');
        $result = $query->first_row('array');
        return $result;
    }



    // -- -----------------------

    function pre_signature($param_array, $sandbox_mode = null)
    {

        $pass = $this->pre_live_pass;
        //$pass = '1';
        if (isset($sandbox_mode) && ($sandbox_mode == 'SANDBOX')) {

            $pass = $this->pre_sandbox_pass;
            // $pass = '2';
        }


        $concat_string  = $param_array['merchant_key'];
        $concat_string .= $param_array['name_first'];
        $concat_string .= $param_array['name_last'];
        $concat_string .= $param_array['item_name'];
        $concat_string .= $param_array['amount'];
        $concat_string .= $param_array['return_url'];
        $concat_string .= $param_array['notify_url'];
        $concat_string .= $pass;

        $signature_hash = md5($concat_string);
        return $signature_hash;
    }

    function if_pre_is_sandbox($param_array, $signature)
    {

        $result_signature = 'NONE';




        $live_signature = $this->pre_signature($param_array);
        $sandbox_signature = $this->pre_signature($param_array, "SANDBOX");

        if (trim($signature) == $live_signature) {

            $result_signature = 'LIVE';
        } elseif (trim($signature) == $sandbox_signature) {

            $result_signature = 'SANDBOX';
        }

        // ----------------------------------------------------------------

        $result_merchant_id = 'NONE';
        if (trim($param_array['merchant_id']) == $this->merchant_id) {

            $result_merchant_id = 'LIVE';
        } elseif (trim($param_array['merchant_id']) == $this->sandbox_merchant_id) {

            $result_merchant_id = "SANDBOX";
        }

        // ---------------------------------------------------------------

        $result_merchant_key = 'NONE';
        if (trim($param_array['merchant_key']) == $this->merchant_key) {

            $result_merchant_key = 'LIVE';
        } elseif (trim($param_array['merchant_key']) == $this->sandbox_merchant_key) {

            $result_merchant_key = "SANDBOX";
        }


        if (($result_signature    == "SANDBOX") &&
            ($result_merchant_id  == "SANDBOX") &&
            ($result_merchant_key == "SANDBOX")
        ) {

            return 'SANDBOX';
        };


        if (($result_signature     == "LIVE") &&
            ($result_merchant_id   == "LIVE") &&
            ($result_merchant_key  == "LIVE")
        ) {

            return 'LIVE';
        };

        return false;
    }




    function pre_validate_all_signature($param_array, $signature)
    {

        $result_param = array(
            'message'    =>  '',
            'host_param' => '',
            'result'     => '',

        );

        // validate main request
        $sandbox_check_result = $this->if_pre_is_sandbox($param_array, $signature);

        if ($sandbox_check_result === false) {

            $result_param['message']    = 'invalid_data (Sandbox/Live - undefined)';
            $result_param['host_param'] = 'NONE';
            $result_param['result']     =  false;
            return $result_param;
        }

        // validation request signature
        $sandbox_mode = $sandbox_check_result;
        $signature_validation_result = $this->signature_validation($param_array, $sandbox_mode);
        if ($signature_validation_result['validation'] == false) {

            $result_param['message'] = 'Main signature failed';
            $result_param['result']    = false;
            $result_param['host_param'] = $sandbox_check_result;
            return $result_param;
        }


        $result_param['message'] = '';
        $result_param['host_param'] = $sandbox_check_result;
        $result_param['result']  = true;


        return $result_param;
    }


    // -----------------------------------------------------------------------------------------------


    function generate_order_signature($data)
    {

        $string  =  $data['account_username'];
        $string .=  $data['realm'];
        $string .=  $data['account_password'];
        $string .=  $data['choose_cycle'];
        $string .=  $data['product_id'];
        $string .=  $data['payment_type'];
        $string .= $this->order_pass;

        $md_string = md5($string);

        return $md_string;
    }


    function generate_topup_order_signature($data)
    {

        $string  =  $data['adsl_username'];
        $string .=  $data['product_id'];
        $string .=  $data['order_id'];
        $string .=  $data['topup_config_id'];
        $string .=  $data['topup_name'];
        $string .=  $data['payment_type'];
        $string .=  $data['topup_level'];
        $string .= $this->order_pass;

        $md_string = md5($string);

        return $md_string;
    }

    function generate_topup_lte_signature($param_array, $sandbox = null)
    {

        $pfOutput = '';

        foreach ($param_array as $key => $value) {

            if (!empty($value)) {
                $pfOutput .= $key . '=' . urlencode(trim($value)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);

        if (!isset($sandbox)) {
            $getString .= '&passphrase=' . urlencode(trim($this->passphrase));
        }

        $sign = md5($getString);

        return $sign;
    }


    function pre_validate_order_signature($data, $received_signature)
    {

        $order_signature = $this->generate_order_signature($data);
        if ($order_signature != trim($received_signature))
            return false;
        return true;
    }

    function pre_validate_topup_order_signature($data, $received_signature)
    {

        $order_signature = $this->generate_topup_order_signature($data);
        if ($order_signature != trim($received_signature))
            return false;
        return true;
    }


    // -- ------------------------------------------------------------------------


    function save_pre_order($data, $payment_id, $username, $amount)
    {

        /*
            `pre_order_id`  integer UNSIGNED NULL AUTO_INCREMENT ,
            `m_payment_id`  integer UNSIGNED NULL ,
            `username`  varchar(80) NULL ,
            `product_id`  int NULL ,
            `acc_username`  varchar(80) NULL ,
            `acc_password`  varchar(80) NULL ,
            `payment_method`  varchar(25) NULL ,
            `choose_cycle`  varchar(15) NULL ,
            `amount`  varchar(80) NULL ,
            `status`  varchar(25) NULL ,

            account_username":"",
        "account_password":"",
        "realm":"mynetwork.co.za",
        "choose_cycle":"Monthly",
        "product_id":"20",
        "payment_type":"payfast-payment"

         */


        if (empty($data['account_username'])) {
            $data['account_username'] = $this->get_payfast_rand_str($username);
        }

        if (empty($data['account_password'])) {
            $data['account_password'] = $this->get_payfast_rand_str($username);
        }


        $current_date = date("Y-m-d H:i:s");

        $table_data = array(

            'm_payment_id'    => $payment_id,
            'username'        => $username,
            'product_id'      => $data['product_id'],
            'acc_username'    => $data['account_username'],
            'acc_password'    => $data['account_password'],
            'realm'           => $data['realm'],
            'payment_method'  => $data['payment_type'],
            'choose_cycle'    => $data['choose_cycle'],
            'amount'          => $amount,
            'status'          => 'waiting',
            'create_date'     => $current_date,
        );


        $result = $this->db->insert('payfast_pre_order', $table_data);
        return $result;
    }

    // -----------------------------------------------------------------------

    function save_topup_pre_order($data, $payment_id, $username, $amount)
    {

        /*
            `pre_order_id`  integer UNSIGNED NULL AUTO_INCREMENT ,
            `m_payment_id`  integer UNSIGNED NULL ,
            `username`  varchar(80) NULL ,
            `product_id`  int NULL ,
            `acc_username`  varchar(80) NULL ,
            `acc_password`  varchar(80) NULL ,
            `payment_method`  varchar(25) NULL ,
            `choose_cycle`  varchar(15) NULL ,
            `amount`  varchar(80) NULL ,
            `status`  varchar(25) NULL ,

            'adsl_username'     => $order_data['account_username'] . "@" . $order_data['realm'],
            'product_id'        => $product_id,
            'order_id'          => $order_id,
            'topup_config_id'   => $topup_for_viewer['topup_id'],
            'topup_name'        => $topup_for_viewer['topup_name'],
         */

        $current_date = date("Y-m-d H:i:s");
        $table_data = array(

            'm_payment_id'    => $payment_id,
            'username'        => $username,
            'adsl_username'   => $data['adsl_username'],
            'product_id'      => $data['product_id'],
            'order_id'        => $data['order_id'],
            'topup_config_id' => $data['topup_config_id'],
            'topup_name'      => $data['topup_name'],
            'amount'          => $amount,
            'payment_method'  => $data['payment_type'],
            'topup_level'     => $data['topup_level'],
            'status'          => 'waiting',
            'create_date'     => $current_date,
        );

        $result = $this->db->insert('payfast_topup_pre_order', $table_data);
        return $result;
    }



    function get_pre_order($m_id)
    {

        $this->db->select('');
        $this->db->where('m_payment_id', $m_id);
        $this->db->order_by('pre_order_id', 'desc');
        $query = $this->db->get('payfast_pre_order');
        $result = $query->result_array();
        return $result[0];
    }


    function get_topup_pre_order($m_id)
    {

        $this->db->select('');
        $this->db->where('m_payment_id', $m_id);
        $this->db->order_by('pre_order_id', 'desc');
        $query = $this->db->get('payfast_topup_pre_order');
        $result = $query->result_array();
        return $result[0];
    }


    function update_topup_pre_order($m_payment_id, $status)
    {

        $update_date = date("Y-m-d H:i:s");
        $update_array = array(

            'update_date'   => $update_date,
            'status'        => $status
        );

        $this->db->where('m_payment_id', $m_payment_id);
        $update_result =  $this->db->update('payfast_topup_pre_order', $update_array);
        return $update_result;
    }

    function modify_pre_order_isdsl_acccount($id, $username, $password)
    {

        $data = array(
            'acc_username' => $username,
            'acc_password' => $password,
        );

        $this->db->where('pre_order_id', $id);
        $update_result =  $this->db->update('payfast_pre_order', $data);
        return $update_result;
    }




    function get_payfast_rand_str($username)
    {
        $chars = '0123456789';
        $str = '';
        for ($i = 0; $i < 5; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        $data_row = date("His");
        return 'isp-' . $username . $str . $data_row;
    }



    // ---------------------------------------------------------

    function payfast_activate_order($m_payment_id, $item_description = null)
    {

        $this->write_log("in payfast activate order ");

        if ($item_description  == $this->topup_item_description) {
            // this is topup order

            $pre_order_array = $this->get_topup_pre_order($m_payment_id);
            $pre_order_dump = print_r($pre_order_array, true);
            $this->write_log("get pre order done :  " . $pre_order_dump);

            $order_time = date('Y-m-d H:i:s');
            $user_id = $this->membership_model->get_user_id($pre_order_array['username']);
            $new_topup_order_data = array(

                'order_time'        => $order_time,
                'user_id'           => $user_id,
                'username'          => $pre_order_array['username'],
                'topup_config_id'   => $pre_order_array['topup_config_id'],
                'order_id'          => $pre_order_array['order_id'],
                'product_id'        => $pre_order_array['product_id'],
                'payment_method'    => $pre_order_array['payment_method'],
                'payment_status'    => 'completed',
                'price'             => $pre_order_array['amount'],
                'topup_level'       => $pre_order_array['topup_level'],
                'adsl_username'     => $pre_order_array['adsl_username'],

            );

            $inserted_id = $this->product_model->insert_topup_order($new_topup_order_data);
            // update pre_order data
            $this->update_topup_pre_order($m_payment_id, 'completed');
            $this->write_log("topup order inserted with id  :  " . $inserted_id);
            $this->write_log("topup pre order updated with m-id  :  " . $m_payment_id);

            $isdsl_result = $this->payfast_model->isdsl_topup_account($inserted_id, $pre_order_array['username'],  $new_topup_order_data);
            $this->write_log("isdsl account upgraded");
        } else {

            $pre_order_array = $this->get_pre_order($m_payment_id);
            $pre_order_array['payment_type'] = $pre_order_array['payment_method'];
            $pre_order_dump = print_r($pre_order_array, true);
            $this->write_log("get pre order done :  " . $pre_order_dump);

            $order_id = $this->product_model->insert_order($pre_order_array);
            $this->write_log("order inserted with id  :  " . $order_id);

            $isdsl_result = $this->payfast_model->isdsl_create_account($pre_order_array, $order_id);
            $this->write_log("isdsl account created");
        }

        return $isdsl_result;

        //   $account_result = $this->payfast_model->isdsl_get_account($pre_order_array);
        // active pre statuses

    }



    function isdsl_create_account($pre_order_array, $order_id)
    {

        /*
    DROP TABLE IF EXISTS `payfast_pre_order`;
CREATE TABLE `payfast_pre_order` (
  `pre_order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `m_payment_id` int(10) unsigned DEFAULT NULL,
  `username` varchar(80) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `acc_username` varchar(80) DEFAULT NULL,              +
  `acc_password` varchar(80) DEFAULT NULL,              +
  `realm` varchar(80) DEFAULT NULL,                     +
  `payment_method` varchar(25) DEFAULT NULL,
  `choose_cycle` varchar(15) DEFAULT NULL,
  `amount` varchar(80) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pre_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

*/


        $this->write_log("## netwok API - prepare data");
        // ---- prepare data ----

        $account_username = $pre_order_array['acc_username'];
        $account_password = $pre_order_array['acc_password'];
        $realm = $pre_order_array['realm'];
        $product_id = $pre_order_array['product_id'];
        $billing_cycle = $pre_order_array['choose_cycle'];

        $ac_email = $this->membership_model->get_email($pre_order_array['username']);
        $product_name = $this->product_model->get_product_name($pre_order_array['product_id']);

        $new_account_data = $this->product_model->get_order_data($order_id);
        $comment = $new_account_data['account_comment']; //get account_comment form orders
        $class = $this->product_model->get_class_by_product_id($product_id);

        $user_name_nice = $this->membership_model->get_user_name_nice($pre_order_array['username']); //get full name from membership
        $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);


        $this->write_log("## api network - credentials ");

        // ---- connect to Network api network -----;
        $this->load->model("network_api_handler_model");


        $order_data = array('account_username' => $account_username, 'realm' => $realm);
        $creation_result = $this->network_api_handler_model->add_new_realm_user($order_data, $class, $account_password, $comment, $ac_email);

        //$creation_result['result'] = true;
        //var_dump($creation_result);

        /*
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $lm = explode('@', $realm_data['user']);
        $realm = $lm[1];
        $sess = 0;
        */

        $this->write_log("## api network - creation executed");
        // ISDSL connect
        //$sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id   //#A


        //$this->write_log("## isdsl - add product");
        // Add to ISDSL
        $acc_realm_user = $account_username . '@' . $realm;

        //$resp = $this->is_classes->add_realm_new($sess, $class, $account_username, $account_password, $comment, $ac_email);//#A
        //$resp = $this->is_classes->add_realm_new($sess, $class, $account_username, $account_password, $comment, $ac_email);//#A

        // Resp = 0 is ok
        $resp_dump = print_r($creation_result, true);
        $this->write_log("## get resp : " . $resp_dump);

        $second_add_realm = 0;
        if (!$creation_result['result']) {


            $this->write_log("## network api - generate new account data");
            $account_username = $this->get_payfast_rand_str($pre_order_array['username'] . date('sdHi'));
            $account_password = $this->get_payfast_rand_str($pre_order_array['username']);
            $order_data['account_username'] = $account_username;

            // second turn
            //$resp = $this->is_classes->add_realm_new($sess, $class, $account_username, $account_password, $comment, $ac_email);//#A
            $creation_result = $this->network_api_handler_model->add_new_realm_user($order_data, $class, $account_password, $comment, $ac_email);
            $second_add_realm = 1;
        }

        $this->write_log("## api network - second resp");

        if ($creation_result['result']) {

            if ($second_add_realm  == 1) {

                $this->write_log("## api network - second add success , modify orders");

                $pre_order_update_result = $this->modify_pre_order_isdsl_acccount($pre_order_array['pre_order_id'], $account_username, $account_password);
                $order_update_result = $this->product_model->update_order_username_password_db($order_id, $account_username, $account_password);
            }

            // if payment  On - off  -> schedule nosvc
            //  set_pending_update_new($sess, $username, $class)
            if ($billing_cycle == 'Once-Off') {

                // set pending update
                //$pending_resp = $this->is_classes->set_pending_update_new($sess, $acc_realm_user, 'nosvc');

                // cancel order next month
                $cancellationDate = date("Y-m-1", strtotime("+ 1 month"));
                $this->network_api_handler_model->cancel_account($order_data,  $cancellationDate);
            }


            $this->write_log("## api network - activation order !");

            $this->order_model->email_activation($ac_email, $product_name, $account_username, $realm, $account_password, $user_name_nice);


            $this->write_log("## api network - set- activated | order id : " . $order_id);
            $this->order_model->set_activated($order_id);
            $this->write_log("## api network - pre_order | pre_order_id : " . $pre_order_array['pre_order_id']);
            $this->activate_pre_order($pre_order_array['pre_order_id']);

            // !--- SMS ----!
            $sms_content = "Your ADSL product has been successfully created. See email for more details. Username: $acc_realm_user Password: $account_password - OpenWeb";
            if (!empty($number))
                $this->order_model->send_sms($number, $sms_content); //#B
        } else {

            // can't create Account

            $resp_dump = print_r($creation_result, true);
            $message = $creation_result['message'];
            /*
            switch ($resp){
                case 2 : $message = 'Username does not exist'; break;
                case 5 : $message = 'Failure: Invalid session identifier supplied'; break;
                case 7 : $message = 'Failure: ADSL account does not belong to your organisation'; break;
                case 8 : $message = 'Failure: Invalid class'; break;
                case 3 : $message = 'Incorrect password supplied'; break;
                case 6 : $message = 'No user accounts available'; break;
                case 11 : $message = 'Username exists'; break;
                case FALSE : $message = 'invalid chars (FALSE)'; break;

            }*/

            $this->write_log("## api network - second resp is wrong : " . " - " . " | msg : " . $message);
            $this->write_log("## api network - second resp is wrong , full response : \n" . $resp_dump);


            $failed_update_result = $this->failed_pre_order($pre_order_array['pre_order_id'], "#", $message);
            $this->write_log("## isdsl - failed update result : " . $failed_update_result);
        }


        return $creation_result;
    }


    function isdsl_topup_account($inserted_id, $username, $topup_order_data)
    {

        /*
            $new_topup_order_data = array(

                    'order_time'        => $order_time,
                    'user_id'           => $user_id,
                    'username'          => $pre_order_array['username'],
                    'topup_config_id'   => $pre_order_array['topup_config_id'],
                    'order_id'          => $pre_order_array['order_id'],
                    'product_id'        => $pre_order_array['product_id'],
                    'payment_method'    => $pre_order_array['payment_method'],
                    'payment_status'    => 'completed',
                    'price'             => $pre_order_array['amount'],
                    'topup_level'       => $pre_order_array['topup_level'],
                    'adsl_username'     => $pre_order_array['adsl_username'],

            );

              $this->write_log("");

         */

        $this->write_log(" in ISDSL topup account");
        $isdl_assign_result = 0;
        $order_id = $topup_order_data['order_id'];

        // ---------------------------------  check schedule flag --------------------------------------------

        $current_month = date('m');
        $current_year = date('Y');
        $schedule_flag =  $this->product_model->check_schedule_topup($topup_order_data['adsl_username'], $current_year,  $current_month, $topup_order_data['user_id'], $order_id);

        $this->write_log(" schedule flag : ", $schedule_flag);

        $product_data = $this->product_model->get_product_data($topup_order_data['product_id']);
        $order_data   = $this->order_model->get_order_data($order_id);
        $topup_info   = $this->product_model->topup_get_config($topup_order_data['topup_config_id']);

        //  --------------- get service class id / name by order id
        $current_class_id    = $product_data['class_id'];
        $current_class_name  = $product_data['class'];

        // ---------------- get TopUp class id/ name
        $topup_class_id   = $topup_info['class_id'];
        $topup_class_name = $topup_info['class_name'];

        // ---------------- update order info
        $classes_data_for_update = array(

            'service_class_id'   => $current_class_id,
            'service_class_name' => $current_class_name,

            'topup_class_id'          => $topup_class_id,
            'topup_class_name'        => $topup_class_name,

        );


        $classes_update_result =  $this->product_model->update_topup_order($inserted_id, $classes_data_for_update);
        $this->write_log(" updated TopUp order with service class id & topup_class_id");

        // -------------------------------------- API Session -------------------------------------------
        $isdl_create_result = false;

        $adsl_array = explode('@', $topup_order_data['adsl_username']);
        $account_realm = $adsl_array[1];

        $this->write_log(" adsl username : " . $topup_order_data['adsl_username']);
        $this->write_log(" realm : " . $account_realm);

        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');
        $realm_data = $this->realm_model->get_realm_data_by_name($account_realm);

        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $realm = $account_realm;
        $sess = 0;

        // session, schedule, change
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id

        // -------------------------------------- Get Current API class

        // ----------- get current user class
        $real_account_info = $this->is_classes->getAccountInfo_full_new($sess, $topup_order_data['adsl_username']);
        $this->write_log(" real account info code : " . $real_account_info['intReturnCode']);

        if ($real_account_info['intReturnCode'] == '1') {

            $real_class_name = $real_account_info['arrAccountInfo']['Class'];
            $real_class_id   = $this->is_classes->get_class_id($real_class_name, $realm);


            $real_data_update = array(

                'real_class_id'   => $real_class_id,
                'real_class_name' => $real_class_name,

            );
            $real_update_result =  $this->product_model->update_topup_order($inserted_id, $real_data_update);
        }
        // -------------------------------------- Set Pending update + change current class

        // Check Once-off !!!!
        $once_off_flag = false;
        if (strtolower($order_data['billing_cycle']) == 'once-off')
            $once_off_flag = true;


        $this->write_log(" ocne off flag : " . $once_off_flag);
        if (!$schedule_flag) {

            $schedule_current_class_answer = 0;
            if (!$once_off_flag)
                $schedule_current_class_answer = $this->is_classes->set_pending_update_new($sess, $topup_order_data['adsl_username'], $current_class_name);
            $already_scheduled = 0;
        } else {
            $schedule_current_class_answer = 1;
            $already_scheduled      = $schedule_flag; // order-id where class was scheduled

        }
        $this->write_log(" sheduled code ");
        $assign_new_class_answer =  $this->is_classes->set_account_class_new($sess, $topup_order_data['adsl_username'], $topup_class_name);
        // ---------------------- update order block ------------------------------------------
        $this->write_log(" assigned new class ");
        // 1msg =  shcedule answer handler
        $schedule_api_message      =  $this->is_classes->resp_handler_set_pending_update_new($schedule_current_class_answer);

        // 2msg =  change class answer handler
        $change_class_api_message  =  $this->is_classes->resp_handler_set_account_class_new($assign_new_class_answer);


        // ----------------------------------- update order data with API functions

        $api_data_for_update = array(

            'api_status'           => $assign_new_class_answer,
            'api_message'          => $change_class_api_message,

            'schedule_api_status'  => $schedule_current_class_answer,
            'schedule_api_message' => $schedule_api_message,

            'already_scheduled_id' => $already_scheduled,

        );
        $this->write_log(" updated api_status ");
        $api_update_result =  $this->product_model->update_topup_order($inserted_id, $api_data_for_update);
        if ($assign_new_class_answer == '')
            $assign_new_class_answer = '1';

        $client_full_name = $this->membership_model->get_user_name_nice($username);
        if ($assign_new_class_answer == '1') {

            $this->write_log(" api success code ");
            // send letter to client
            $this->user_model->email_topup_with_invoice_individual($username, null);

            // send letter to CEO
            $this->user_model->topup_email_to_admin($username, $client_full_name, $topup_order_data, $inserted_id, $this->base_url);

            $this->write_log("## isdsl - set - activated | inserted id : " . $inserted_id);
        } else {
            // fail handler
            $this->write_log(" api fail code ");
            $this->write_log("## isdsl - second resp is wrong : " . $assign_new_class_answer . " | msg : " . $change_class_api_message);
            // email to CEO
            if ($change_class_api_message == 'Empty-ok')
                $change_class_api_message = '';
            $this->user_model->topup_error_email_to_admin($username, $client_full_name, $topup_order_data,  $inserted_id, $this->base_url, $assign_new_class_answer, $change_class_api_message);
        }

        return $assign_new_class_answer;
    }



    function activate_pre_order($pre_order_id)
    {

        $date = date("Y-m-d H:i:s");
        $data = array(

            'status' => 'activated',
            'update_date' => $date,

        );

        $this->db->where('pre_order_id', $pre_order_id);
        $this->db->update('payfast_pre_order', $data);
    }

    function failed_pre_order($pre_order_id, $resp_code, $message)
    {

        $date = date("Y-m-d H:i:s");
        $data = array(

            'status' => 'failed',
            'message' => 'code : ' . $resp_code . ' | ' . $message,
            'update_date' => $date,

        );

        $this->db->where('pre_order_id', $pre_order_id);
        $update_result = $this->db->update('payfast_pre_order', $data);
        return $update_result;
    }


    function isdsl_get_account($pre_order_array)
    {



        $account_username = $pre_order_array['acc_username'];
        $account_password = $pre_order_array['acc_password'];
        $realm = $pre_order_array['realm'];
        $product_id = $pre_order_array['product_id'];
        $billing_cycle = $pre_order_array['choose_cycle'];

        $isdsl_username = $account_username . '@' . $realm;


        //$class = $this->product_model->get_class_by_billing_cycle($billing_cycle, $product_id);
        $class = $this->product_model->get_class_by_product_id($product_id);

        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // $realm_data = $this->product_model->get_is_details($class);     //get user and pwd from realm
        // $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

        $realm_data = $this->realm_model->get_realm_data_by_name($realm);
        if (empty($realm_data))
            $realm_data = $this->product_model->get_is_details($class);


        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $lm = explode('@', $realm_data['user']);
        $realm = $lm[1];
        $sess = 0;



        // ISDSL connect
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id   //#A

        //echo $sess;
        // die;
        $result = $this->is_classes->getAccountInfo_full_new($sess, $isdsl_username); // array return

        return $result;
    }


    function get_last_payfast_request_by_internal_id($payment_id)
    {

        $this->write_log("++ payfast request / in");

        $this->db->select('');
        $this->db->where('internal_payment_id', $payment_id);
        $this->db->order_by('request_id', 'desc');

        $this->write_log("++ payfast request / pre query");

        $query = $this->db->get('payfast_request');

        $this->write_log("++ payfast request / after query");

        $result = $query->result_array();

        $reuslt_dump = print_r($result, true);
        $this->write_log("++ payfast request / result :  " . $reuslt_dump);

        if ($result[0]['status'] == 'done') {

            $this->write_log("++ payfast request / status already DONE (exit from process) ");
            die();
        }



        return $result[0];
    }


    function payfast_request_update_receive_status($payfast_request_id)
    {

        $date = date("Y-m-d H:i:s");
        $data = array(

            'status' => 'done',
            'response_date' => $date,

        );

        $this->db->where('request_id', $payfast_request_id);
        $update_result = $this->db->update('payfast_request', $data);
        return $update_result;
    }


    function get_topup_for_lte($type)
    {

        $query = $this->db->get_where('topup_list', ['class_name' => $type]);

        $res = $query->result_array();

        return $res;
    }
    function get_topup_for_mobile($type)
    {

        $query = $this->db->get_where('topup_list', ['class_name' => $type]);

        $res = $query->result_array();

        return $res;
    }

    function get_topup_plan($id)
    {

        $query = $this->db->get_where('topup_list', ['topup_id' => $id]);
        $res = $query->result_array();

        return $res[0];
    }
}
