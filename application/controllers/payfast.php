<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payfast extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('payfast_model');
        $this->load->model('user/user_model');
        $this->load->model('user/is_classes');
        $this->load->model('admin/realm_model');
    }
    function notify()
    {
        $post_dump = print_r($_POST, true);
        $this->payfast_model->write_log('');
        $this->payfast_model->write_log('------------------------------------------------------------');
        $this->payfast_model->write_log(date("Y-m-d H:i:s"));
        $this->payfast_model->write_log($post_dump);

        if (empty($_POST)) {
            return false;
        }

        $payment_response['m_payment_id']       = isset($_POST['m_payment_id']) ? $_POST['m_payment_id'] : '';            //  8542
        $payment_response['pf_payment_id']      = isset($_POST['pf_payment_id']) ? $_POST['pf_payment_id'] : '';          // 143305
        $payment_response['payment_status']     = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';        // COMPLETE
        $payment_response['item_name']          = isset($_POST['item_name']) ? $_POST['item_name'] : '';                  // Item Name
        $payment_response['item_description']   = isset($_POST['item_description']) ? $_POST['item_description'] : '';    // Item Description
        $item_description =  trim($payment_response['item_description']);
        $payment_response['amount_gross']       = isset($_POST['amount_gross']) ? $_POST['amount_gross'] : '';             //  1.00
        $payment_response['amount_fee']         = isset($_POST['amount_fee']) ? $_POST['amount_fee'] : '';                 // -0.02
        $payment_response['amount_net']         = isset($_POST['amount_net']) ? $_POST['amount_net'] : '';                 //  0.98
        $payment_response['custom_str1']        = isset($_POST['custom_str1']) ? $_POST['custom_str1'] : '';
        $payment_response['custom_str2']        = isset($_POST['custom_str2']) ? $_POST['custom_str2'] : '';
        $payment_response['custom_str3']        = isset($_POST['custom_str3']) ? $_POST['custom_str3'] : '';
        $payment_response['custom_str4']        = isset($_POST['custom_str4']) ? $_POST['custom_str4'] : '';
        $payment_response['custom_str5']        = isset($_POST['custom_str5']) ? $_POST['custom_str5'] : '';

        $payment_response['custom_int1']        = isset($_POST['custom_int1']) ? $_POST['custom_int1'] : '';
        $payment_response['custom_int2']        = isset($_POST['custom_int2']) ? $_POST['custom_int2'] : '';
        $payment_response['custom_int3']        = isset($_POST['custom_int3']) ? $_POST['custom_int3'] : '';
        $payment_response['custom_int4']        = isset($_POST['custom_int4']) ? $_POST['custom_int4'] : '';
        $payment_response['custom_int5']        = isset($_POST['custom_int5']) ? $_POST['custom_int5'] : '';

        $payment_response['name_first']         = isset($_POST['name_first']) ? $_POST['name_first'] : '';                  // Test
        $payment_response['name_last']          = isset($_POST['name_last']) ? $_POST['name_last'] : '';                    // User 01
        $payment_response['email_address']      = isset($_POST['email_address']) ? $_POST['email_address'] : '';            // sbtu01@payfast.co.za
        $payment_response['merchant_id']        = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : '';                // 10000100
        $payment_response['signature']          = isset($_POST['signature']) ? $_POST['signature'] : '';


        foreach ($payment_response as $key => $value) {

            $payment_response[$key] = mysql_real_escape_string(strip_tags($value));
        }

        $server_remote_addr = $_SERVER['REMOTE_ADDR'];
        $this->payfast_model->write_log($server_remote_addr);

        $validation_result = $this->payfast_model->full_validation($server_remote_addr, $payment_response);

        $validation_dump_result = print_r($validation_result, true);
        $this->payfast_model->write_log("validation : " . $validation_dump_result);
        //  payfast response update
        $saved_transaction_id = $this->payfast_model->save_transaction_reponse($payment_response, $validation_result['validation'], $validation_result['message']);
        $this->payfast_model->write_log("save transaction response , m_payment_id  : " . $payment_response['m_payment_id']);

        //  payfast request update
        $payfast_request = $this->payfast_model->get_last_payfast_request_by_internal_id($payment_response['m_payment_id']);
        $request_update_result =  $this->payfast_model->payfast_request_update_receive_status($payfast_request['request_id']);
        $this->payfast_model->write_log("payfast request update");

        if ($validation_result['validation']) {

            // - get pre_order
            $this->payfast_model->write_log("in validation ");
            $activate_order_result = $this->payfast_model->payfast_activate_order($payment_response['m_payment_id'], $item_description);
            $this->payfast_model->write_log("order activated ");

            // update statuses (order, transactions (request))
        } else {

            $this->payfast_model->write_log("validation fail ");
            // validation error
        }

        // save into database

        $this->payfast_model->write_log("transcation id " . $saved_transaction_id);
    }

    function prevalid()
    {


        if ($this->input->is_ajax_request()) {

            $ajax_params =  (array)json_decode($this->input->post('params'));
            $ajax_order  =  (array)json_decode($this->input->post('order_params')); //gg
            $username    =  $this->input->post('user');
            $pre_signature   = $this->input->post('pre_signature');
            $order_signature = $this->input->post('order_signature');


            foreach ($ajax_params as $key => $value) {

                $new_value = urlencode($value);
                $new_value = mysql_real_escape_string(strip_tags($new_value));
                $value     = $new_value;

                $new_value = null;
            }
            foreach ($ajax_order as $key => $value) {

                $new_value = urlencode($value);
                $new_value = mysql_real_escape_string(strip_tags($new_value));
                $value     = $new_value;

                $new_value = null;
            }


            $username  = mysql_real_escape_string(strip_tags($username));
            $pre_signature = mysql_real_escape_string(strip_tags($pre_signature));
            $order_signature = mysql_real_escape_string(strip_tags($order_signature));

            $user_id = $this->user_model->get_user_id($username);

            // validate main signature all
            $pre_validation_result = $this->payfast_model->pre_validate_all_signature($ajax_params, $pre_signature);
            $order_validation = $this->payfast_model->pre_validate_order_signature($ajax_order, $order_signature);

            if ($pre_validation_result['result'] && $order_validation) {

                // save pre transaction and pre order
                $save_result    =  $this->payfast_model->save_pre_transaction($ajax_params, $username, $user_id, $pre_validation_result['host_param']);
                $pre_order_save =  $this->payfast_model->save_pre_order($ajax_order, $ajax_params['m_payment_id'], $username, $ajax_params['amount']);
            } else {



                // save error  dump
                // save

            }
        }
    }


    function topup_prevalid()
    {


        if ($this->input->is_ajax_request()) {

            $ajax_params =  (array)json_decode($this->input->post('params'));
            $ajax_order  =  (array)json_decode($this->input->post('order_params'));
            $username    =  $this->input->post('user');
            $pre_signature   = $this->input->post('pre_signature');
            $order_signature = $this->input->post('order_signature');


            foreach ($ajax_params as $key => $value) {

                $new_value = urlencode($value);
                $new_value = mysql_real_escape_string(strip_tags($new_value));
                $value     = $new_value;

                $new_value = null;
            }
            foreach ($ajax_order as $key => $value) {
                $new_value = urlencode($value);
                $new_value = mysql_real_escape_string(strip_tags($new_value));
                $value     = $new_value;
                $new_value = null;
            }

            $username  = mysql_real_escape_string(strip_tags($username));
            $pre_signature = mysql_real_escape_string(strip_tags($pre_signature));
            $order_signature = mysql_real_escape_string(strip_tags($order_signature));

            $user_id = $this->user_model->get_user_id($username);

            // validate main signature all
            $pre_validation_result = $this->payfast_model->pre_validate_all_signature($ajax_params, $pre_signature);
            $order_validation = $this->payfast_model->pre_validate_topup_order_signature($ajax_order, $order_signature);

            if ($pre_validation_result['result'] && $order_validation) {
                // save pre transaction and pre order
                $save_result    =  $this->payfast_model->save_pre_transaction($ajax_params, $username, $user_id, $pre_validation_result['host_param']);
                $pre_order_save =  $this->payfast_model->save_topup_pre_order($ajax_order, $ajax_params['m_payment_id'], $username, $ajax_params['amount']);
            }
        }
    }


    function client_prevalid()
    {

        if ($this->input->is_ajax_request()) {
            $ajax_params =  (array)json_decode($this->input->post('params'));
            $ajax_order  =  (array)json_decode($this->input->post('order_params'));
            $username    =  $this->input->post('user');
            $pre_signature   =  $this->input->post('pre_signature');
            $order_signature = $this->input->post('order_signature');
            // generate signature for pre
            $order_signature  = $this->payfast_model->generate_order_signature($ajax_order);
            // +
            //echo json_encode($order_signature);
            // die;
            // +
            foreach ($ajax_params as $key => $value) {

                $new_value = urlencode($value);
                $new_value = mysql_real_escape_string(strip_tags($new_value));
                $value     = $new_value;

                $new_value = null;
            }
            foreach ($ajax_order as $key => $value) {

                $new_value = urlencode($value);
                $new_value = mysql_real_escape_string(strip_tags($new_value));
                $value     = $new_value;

                $new_value = null;
            }


            $username  = mysql_real_escape_string(strip_tags($username));
            $pre_signature = mysql_real_escape_string(strip_tags($pre_signature));
            $order_signature = mysql_real_escape_string(strip_tags($order_signature));

            $user_id = $this->user_model->get_user_id($username);

            // validate main signature all
            $pre_validation_result = $this->payfast_model->pre_validate_all_signature($ajax_params, $pre_signature);
            $order_validation = $this->payfast_model->pre_validate_order_signature($ajax_order, $order_signature);


            //echo json_encode($order_validation);
            //die;
            // disable signature check

            if ($pre_validation_result['result'] && $order_validation) {

                // save pre transaction and pre order
                $save_result    =  $this->payfast_model->save_pre_transaction($ajax_params, $username, $user_id, $pre_validation_result['host_param']);
                $pre_order_save =  $this->payfast_model->save_pre_order($ajax_order, $ajax_params['m_payment_id'], $username, $ajax_params['amount']);
            } else {

                // save error  dump
                // save
            }
        }
    }


    function payfast_debug()
    {

        echo "start";
        $result =  $this->payfast_model->load_pre_transaction(2);
        var_dump($result);
    }

    function sand_notify()
    {

        $str = $_POST;
        $log_file_path =  dirname(__FILE__)  . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "test-notify-log.txt";

        // $log_file_path = "../logs/notify-log.txt";
        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, "\n " . date("m.d.y") . "--------------------");
        fwrite($log_handle, "\n " . json_encode($str));


        $payment_response['m_payment_id']       = isset($_POST['m_payment_id']) ? $_POST['m_payment_id'] : '';            //  8542
        $payment_response['pf_payment_id']      = isset($_POST['pf_payment_id']) ? $_POST['pf_payment_id'] : '';          // 143305
        $payment_response['payment_status']     = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';        // COMPLETE

        $payment_response['item_name']          = isset($_POST['item_name']) ? $_POST['item_name'] : '';                  // Item Name
        $payment_response['item_description']   = isset($_POST['item_description']) ? $_POST['item_description'] : '';    // Item Description


        $item_description =  trim($payment_response['item_description']);

        $payment_response['amount_gross']       = isset($_POST['amount_gross']) ? $_POST['amount_gross'] : '';             //  1.00
        $payment_response['amount_fee']         = isset($_POST['amount_fee']) ? $_POST['amount_fee'] : '';                 // -0.02
        $payment_response['amount_net']         = isset($_POST['amount_net']) ? $_POST['amount_net'] : '';                 //  0.98


        $payment_response['custom_str1']        = isset($_POST['custom_str1']) ? $_POST['custom_str1'] : '';
        $payment_response['custom_str2']        = isset($_POST['custom_str2']) ? $_POST['custom_str2'] : '';
        $payment_response['custom_str3']        = isset($_POST['custom_str3']) ? $_POST['custom_str3'] : '';
        $payment_response['custom_str4']        = isset($_POST['custom_str4']) ? $_POST['custom_str4'] : '';
        $payment_response['custom_str5']        = isset($_POST['custom_str5']) ? $_POST['custom_str5'] : '';

        $payment_response['custom_int1']        = isset($_POST['custom_int1']) ? $_POST['custom_int1'] : '';
        $payment_response['custom_int2']        = isset($_POST['custom_int2']) ? $_POST['custom_int2'] : '';
        $payment_response['custom_int3']        = isset($_POST['custom_int3']) ? $_POST['custom_int3'] : '';
        $payment_response['custom_int4']        = isset($_POST['custom_int4']) ? $_POST['custom_int4'] : '';
        $payment_response['custom_int5']        = isset($_POST['custom_int5']) ? $_POST['custom_int5'] : '';

        $payment_response['name_first']         = isset($_POST['name_first']) ? $_POST['name_first'] : '';                  // Test
        $payment_response['name_last']          = isset($_POST['name_last']) ? $_POST['name_last'] : '';                    // User 01
        $payment_response['email_address']      = isset($_POST['email_address']) ? $_POST['email_address'] : '';            // sbtu01@payfast.co.za
        $payment_response['merchant_id']        = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : '';                // 10000100
        $payment_response['signature']          = isset($_POST['signature']) ? $_POST['signature'] : '';

        foreach ($payment_response as $key => $value) {

            $payment_response[$key] = $this->db->escape_str($value);
        }

        $server_remote_addr = $_SERVER['REMOTE_ADDR'];
        fwrite($log_handle, "\n " . "VAliD");
        $validation_result = $this->payfast_model->full_validation($server_remote_addr, $payment_response);
        fwrite($log_handle, "\n " . "resutl val: " . $validation_result['validation']);
        //  payfast response update
        $saved_transaction_id = $this->payfast_model->update_lte_reponse($payment_response, $validation_result['validation'], $validation_result['message']);
        fwrite($log_handle, "\n " . json_encode($saved_transaction_id));

        if ($validation_result['validation']) {
            fwrite($log_handle, "\n " . "I am here");
            $realm_data = $this->realm_model->get_realm_data_by_name("openwebmobile.co.za");
            fwrite($log_handle, "\n " . "realm data " . json_encode($realm_data));
            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];
            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);
            fwrite($log_handle, "\n " . "ses" . json_encode($sess));
            fwrite($log_handle, "\n " . "user: " . $rl_user);
            fwrite($log_handle, "\n " . json_encode($payment_response['custom_str1']));
            fwrite($log_handle, "\n " . json_encode($payment_response['item_description']));
            $resp = $this->is_classes->queue_top_up_new($sess, $payment_response['custom_str1'] . "@openwebmobile", $payment_response['item_description']);
            fwrite($log_handle, "\n respone: " . json_encode($resp));
            if (isset($resp) && $payment_response['payment_status'] == "COMPLETE") {
                $activity = array(
                    'user' => $payment_response['custom_str2'],
                    'activity' => "Top Up for LTE order " . $payment_response['custom_str1'] . "@openwebmobile with " . $payment_response['item_name'] . " price "
                        . $payment_response['amount_gross'],
                    'type' => 'Top Up LTE order',
                    'link' => $resp,

                );
                $this->db->insert('activity_log', $activity);
            }
        }

        fclose($log_handle);
    }

    function add_lte_topup()
    {

        $params = json_decode($_GET['params'], true);

        $add = $this->payfast_model->add_new_transaction($params, "created");
        echo $add;
    }
    function add_lte_telkom_topup()
    {
        $params = json_decode($_GET['params'], true);
        $add = $this->payfast_model->add_new_transaction($params, "created");
        echo $add;
    }
    function add_mobile_topup()
    {
        $params = json_decode($_GET['params'], true);
        $add = $this->payfast_model->add_new_transaction($params, "created");
        echo $add;
    }
}
