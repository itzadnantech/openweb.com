   <?php if (!defined('BASEPATH')) exit('No direct script access allowed');
    class User extends CI_Controller
    {
        public $site_data;
        protected $withoutLogin = array(
            'daily_usage_cron'
        );
        function __construct()

        {
            parent::__construct();

            if (!in_array($this->router->fetch_method(), $this->withoutLogin)) {
                $this->is_logged_in();
            }
            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
            $this->output->set_header("Pragma: no-cache");

            // FORCE SSLS
            if ($_SERVER['HTTPS'] != "on" && ($_SERVER['HTTP_HOST'] != STAGE_HOST)) {
                $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header("Location:$redirect");
            }
            $this->load->model('user/product_model');
            $this->load->model('user/category_model');
            $this->load->model('user/user_model');
            $this->load->model('user/is_classes');
            $this->load->model('user/cloudsl_model');
            $this->load->model('membership_model');
            $this->load->model('payfast_model');
            $this->load->model('user_docs_model');
            $this->load->model('network_api_handler_model');
            $this->load->model('isdsl_model');
            $this->load->model('form_builder_model');
            $this->load->helper('url');
            $this->load->model('admin/realm_model');
            $this->load->model('validation_model');



            $category_list = $this->category_model->get_categories();

            $this->site_data['category_list'] = $category_list;



            $sub_assoc = $this->category_model->get_subcategories_assoc();

            $this->site_data['subcategories_assoc'] = $sub_assoc;



            $last_login_time = $this->session->userdata('last_login_time');

            $this->site_data['last_login_time'] = $last_login_time;



            $cart = $this->session->userdata('cart');

            $this->site_data['cart'] = $cart;



            $username = $this->session->userdata('username');

            $this->site_data['username'] = $username;



            $first_name = $this->membership_model->get_name($username);

            $this->site_data['first_name'] = $first_name;

            $last_name = $this->membership_model->get_second_name($username);

            $this->site_data['second_name'] = $last_name;


            $this->site_data['ow'] = $this->session->userdata('ow');
        }
        private function isMailSend()
        {
            $query = $this->db->get_where('admin_extra_controlles', array('id' => 1));
            $q = $query->result();
            $r = $q[0]->let_mail_toggle;
            return $r;
        }

        //unset telkom session data used for success payment
        private function rest_telkon_session()
        {
            //unset session data used for telkom service
            $this->session->unset_userdata('custom_service');
            $this->session->unset_userdata('custom_service_telkom_order_no');
            $this->session->unset_userdata('telkom_topup_data');
            return true;
        }
        //15-01-2020 -- Telkom lte topup page view
        function lte_telkom_topup_list()
        {
            //Requested order_id
            $order_id = $this->uri->segment(3);
            //check order ID is exist or not
            if (!$this->user_model->telkom_order_exists($order_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            }
            //set session values for payment succes in current session
            $this->session->set_userdata('custom_service_telkom_order_no', $order_id);
            $this->session->set_userdata('custom_service', 'telkom');
            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);


            // $num_per_page = NUM_PER_PAGE;
            $plans_telkom = $this->payfast_model->get_topup_for_lte("telkom");
            $payment_info['item_name'] = null;
            $payment_info['item_description'] = null;
            $payment_info['discount'] = '0';
            $payment_info['price'] = null;
            $payment_info['pro_price'] = null;

            $data_for_payfast = $this->payfast_model->prepare_topup_telkon_final_checkout($user_id, $username, $payment_info);
            $data_for_payfast['custom_str1'] = $username;
            $data_for_payfast['custom_str2'] = $username;


            $data['ajax_url'] = base_url() . "payfast/add_lte_telkom_topup";
            $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
            $data['live_payfast_host']   = $this->payfast_model->live_host;
            $data['payfast_data'] = $data_for_payfast;


            $data['user_name'] = $username;
            $data['order_id'] = $order_id;
            $data['user_id'] = $user_id;
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['telkom_plan'] = $plans_telkom;
            $data['main_content'] = 'user/lte_telkom_topup_list';
            $data['aditional_scripts'] = ['js/topup_lte.js'];
            $this->load->view('user/includes/template', $data);
        }
        function mtn_topup_list()
        {
            //Requested order_id
            $order_id = $this->uri->segment(3);
            //check order ID is exist or not
            if (!$this->user_model->mtn_order_exists($order_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            }
            //set session values for payment succes in current session
            $this->session->set_userdata('custom_service_telkom_order_no', $order_id);
            $this->session->set_userdata('custom_service', 'mtn');
            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);


            // $num_per_page = NUM_PER_PAGE;
            $plans_telkom = $this->payfast_model->get_topup_for_lte("mtn");
            $payment_info['item_name'] = null;
            $payment_info['item_description'] = null;
            $payment_info['discount'] = '0';
            $payment_info['price'] = null;
            $payment_info['pro_price'] = null;

            $data_for_payfast = $this->payfast_model->prepare_topup_telkon_final_checkout($user_id, $username, $payment_info);
            $data_for_payfast['custom_str1'] = $username;
            $data_for_payfast['custom_str2'] = $username;



            $data['ajax_url'] = base_url() . "payfast/add_lte_telkom_topup";
            $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
            $data['live_payfast_host']   = $this->payfast_model->live_host;
            $data['payfast_data'] = $data_for_payfast;


            $data['user_name'] = $username;
            $data['order_id'] = $order_id;
            $data['user_id'] = $user_id;
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['telkom_plan'] = $plans_telkom;
            $data['main_content'] = 'user/mtn_topup_list';
            $data['aditional_scripts'] = ['js/topup_lte.js'];
            $this->load->view('user/includes/template', $data);
        }
        function mobile_topup_list()
        {
            //Requested order_id
            $order_id = $this->uri->segment(3);

            //check order ID is exist or not
            if (!$this->user_model->mobile_order_exists($order_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            }
            //set session values for payment succes in current session
            $this->session->set_userdata('custom_service_mobile_order_no', $order_id);
            $this->session->set_userdata('custom_service', 'mobile');
            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);


            // $num_per_page = NUM_PER_PAGE;
            $plans_telkom = $this->payfast_model->get_topup_for_mobile("mobile");

            $payment_info['item_name'] = null;
            $payment_info['item_description'] = null;
            $payment_info['discount'] = '0';
            $payment_info['price'] = null;
            $payment_info['pro_price'] = null;

            $data_for_payfast = $this->payfast_model->prepare_topup_telkon_final_checkout($user_id, $username, $payment_info);
            $data_for_payfast['custom_str1'] = $username;
            $data_for_payfast['custom_str2'] = $username;



            $data['ajax_url'] = base_url() . "payfast/add_mobile_topup";
            $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
            $data['live_payfast_host']   = $this->payfast_model->live_host;
            $data['payfast_data'] = $data_for_payfast;


            $data['user_name'] = $username;
            $data['order_id'] = $order_id;
            $data['user_id'] = $user_id;
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['telkom_plan'] = $plans_telkom;
            $data['main_content'] = 'user/mobile_topup_list';
            $data['aditional_scripts'] = ['js/topup_lte.js'];
            $this->load->view('user/includes/template', $data);
        }


        //07-01-2020
        function request_telkom_stat()
        {

            $order_id = filter_input(INPUT_POST, 'request_code', FILTER_SANITIZE_STRING);
            $order_type_j = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
            $order_username = filter_input(INPUT_POST, 'order_username', FILTER_SANITIZE_STRING);

            $simnumber = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
            $lte_username = filter_input(INPUT_POST, 'order_username', FILTER_SANITIZE_STRING);
            $network = filter_input(INPUT_POST, 'network', FILTER_SANITIZE_STRING);

            if (isset($network) && ($network == 'mobile')) {
                $order_type_j = 'mobile';
            }

            $logged_in_user_email = $this->session->userdata('email_z');
            if ($order_type_j == 'telkom') {
                $telkom_stat_request_data = array(
                    'telkom_user_code' => $order_id,
                    'telkom_status' => 'REQUESTED',
                    'telkome_request_date' => date('m-d-Y'),
                    'telkom_status_temp_removed_status' => 'NO'
                );

                $result = $this->db->insert('telkome_stat', $telkom_stat_request_data);
            } elseif ($order_type_j == 'mtn') {
                $telkom_stat_request_data = array(
                    'mtn_user_code' => $order_id,
                    'mtn_status' => 'REQUESTED',
                    'mtn_request_date' => date('m-d-Y'),
                    'mtn_status_temp_removed_status' => 'NO'
                );
                $result = $this->db->insert('mtn_stat', $telkom_stat_request_data);
            } elseif ($order_type_j == 'mobile') {



                $telkom_stat_request_data = array(
                    'mobile_user_code' => $order_id,
                    'mobile_status' => 'REQUESTED',
                    'mobile_request_date' => date('m-d-Y'),
                    'mobile_status_temp_removed_status' => 'NO'
                );

                $result = $this->db->insert('mobile_stat', $telkom_stat_request_data);
            }
            $db_error = $this->db->_error_message();


            if (!empty($db_error)) {
                //if dublicate entry error occured update the stats status
                if ($order_type_j == 'telkom') {
                    $telkom_stat_request_update_data = array(
                        'telkom_status' => 'REQUESTED',
                        'telkome_request_date' => date('m-d-Y'),
                        'telkom_status_temp_removed_status' => 'NO'
                    );

                    $this->db->set($telkom_stat_request_update_data)
                        ->where('telkom_user_code', $order_id)
                        ->update('telkome_stat');
                } elseif ($order_type_j == 'mtn') {
                    $telkom_stat_request_update_data = array(
                        'mtn_status' => 'REQUESTED',
                        'mtn_request_date' => date('m-d-Y'),
                        'mtn_status_temp_removed_status' => 'NO'
                    );

                    $this->db->set($telkom_stat_request_update_data)
                        ->where('mtn_user_code', $order_id)
                        ->update('mtn_stat');
                } elseif ($order_type_j == 'mobile') {
                    $telkom_stat_request_update_data = array(
                        'mobile_status' => 'REQUESTED',
                        'mobile_request_date' => date('m-d-Y'),
                        'mobile_status_temp_removed_status' => 'NO'
                    );


                    $this->db->set($telkom_stat_request_update_data)
                        ->where('mobile_user_code', $order_id)
                        ->update('mobile_stat');
                }

                $success = "Thank you - your topup Stats have been requested.Please allow up to 3 hours for the mobile network to send you your Stats. 
                            Stats will be sent via email.";
            } else {
                $success = "Thank you - your topup Stats have been requested.Please allow up to 3 hours for the mobile network to send you your Stats. 
                            Stats will be sent via email.";
            }

            $this->load->library('email');
            // echo '<pre>';
            // print_r($this->isMailSend());
            // echo '</pre>';
            // die;
            // if ($this->isMailSend() == "true") {
            // if ($this->load->library('email')) {
            if ($order_type_j == 'telkom') {
                $email = $this->message_model->get_telkom_new_stats_req_mail_template();
                if ($email) {
                    $content = $email['content'];
                    $content = str_ireplace('[LTE Username]', $lte_username, $content);
                    $content = str_ireplace('[Order Number]', $order_id, $content);
                    $content = str_ireplace('[Status]', 'REQUESTED', $content);
                    $content = str_ireplace('[Request Date]', date('m-d-Y'), $content);
                    $content = str_ireplace('[SIM Serial Number]', $simnumber, $content);
                    $content = str_ireplace('[Network]', $network, $content);
                    $this->email->from('lte@openweb.co.za');
                    $this->email->to('lte@openweb.co.za'); //jamtechtest420@gmail.com
                    $this->email->subject($email['title']);
                    $this->email->message($content);
                    if (!empty($attac_result)) {
                        foreach ($attac_result as $att) {
                            $path = $att['path'];
                            $this->email->attach($path);
                        }
                    }
                    $this->email->send();
                    $this->email->clear(TRUE);
                }
            } elseif ($order_type_j == 'mtn') {

                $email = $this->message_model->get_mtn_new_stats_req_mail_template();
                if ($email) {
                    $content = $email['content'];
                    $content = str_ireplace('[Order Number]', $order_id, $content);
                    $content = str_ireplace('[Status]', 'REQUESTED', $content);
                    $content = str_ireplace('[Request Date]', date('m-d-Y'), $content);
                    $this->email->from('lte@openweb.co.za');
                    $this->email->to('lte@openweb.co.za'); //$topup_buyer_emailjamtechtest@gmail.com
                    $this->email->subject($email['title']);
                    $this->email->message($content);
                    if (!empty($attac_result)) {
                        foreach ($attac_result as $att) {
                            $path = $att['path'];
                            $this->email->attach($path);
                        }
                    }
                    $this->email->send();
                    $this->email->clear(TRUE);
                }
            } elseif ($order_type_j == 'mobile') {

                $email = $this->message_model->get_mobile_new_stats_req_mail_template();
                $this->load->helper('functions_helper');

                if ($email) {
                    $content = $email['content'];
                    $content = str_ireplace('[Username]', $lte_username, $content);
                    $content = str_ireplace('[Order Number]', $order_id, $content);
                    $content = str_ireplace('[Status]', 'REQUESTED', $content);
                    $content = str_ireplace('[SIM Serial Number]', GetFibreOrdersData($order_id)[0]->sim_serial_no, $content);
                    $content = str_ireplace('[Network]', $order_type_j, $content);
                    $content = str_ireplace('[Request Date]', date('m-d-Y'), $content);


                    $this->email->from('lte@openweb.co.za');
                    $this->email->to('lte@openweb.co.za'); //jamtechtest420@gmail.com
                    $this->email->subject($email['title']);
                    $this->email->message($content);
                    if (!empty($attac_result)) {
                        foreach ($attac_result as $att) {
                            $path = $att['path'];
                            $this->email->attach($path);
                        }
                    }
                    $this->email->send();
                    $this->email->clear(TRUE);
                }
            }
            // } else {
            //     echo 'wait';
            //     die;
            // }


            // $support_email = $this->message_model->get_stats_req_support_mail_template();
            // if ($support_email) {
            //     $content = $support_email['content'];
            //     $content = str_ireplace('[username]', $order_username, $content);
            //     $this->email->from('support@openweb.co.za');
            //     $this->email->to($logged_in_user_email);
            //     $this->email->subject($support_email['title']);
            //     $this->email->message($content);
            //     if (!empty($attac_result)) {
            //         foreach ($attac_result as $att) {
            //             $path = $att['path'];
            //             $this->email->attach($path);
            //         }
            //     }
            //     $this->email->send();
            //     $this->email->clear(TRUE);
            // }

            $msg = array('msg' => $success);
            echo json_encode($msg);
            return true;
        }


        function order_product($product_id)

        {

            $product_data = $this->product_model->get_product_data($product_id);
            $payment_methods = $this->product_model->get_payment_methods($product_id);



            $data['main_content'] = 'order_product';

            $data['sidebar'] = FALSE;

            $this->load->view('user/includes/template', $data);

            $this->site_data = array();
        }

        function invoice_email()
        {


            $username = $this->site_data['username'];
            $data['user_data']['username'] = $username;

            if (isset($_POST['invoice_dropdown'])) {

                $invoiceVal = strip_tags(mysql_real_escape_string($_POST['invoice_dropdown']));
                $invoiceVal = trim($invoiceVal);
                $result =  $this->user_model->save_user_invoice_mail_param($username, $invoiceVal);

                if ($result) {

                    $data['success_message'] = "Parameter was updated successfully";
                    // -- active log ------------------------------------------------------
                    $user_id = $this->membership_model->get_user_id($username);
                    $this->membership_model->add_activity_log($user_id, "change_invoice_email");
                    echo "2";
                    die;
                } else {

                    $data['error_message'] = "Update failure";
                    echo "3";
                    die;
                }
            }
            $user_invoice_mail_param = $this->user_model->get_user_invoice_mail_param($username);
            echo $user_invoice_mail_param;
        }



        function email_param()
        {

            $username = $this->site_data['username'];
            $data['user_data']['username'] = $username;

            if (isset($_POST['bulk_dropdown'])) {

                $bulkVal = strip_tags(mysql_real_escape_string($_POST['bulk_dropdown']));
                $bulkVal = trim($bulkVal);
                $result =  $this->user_model->save_user_bulk_param($username, $bulkVal);

                if ($result) {

                    $data['success_message'] = "Parameter was updated successfully";

                    // -- active log ------------------------------------------------------
                    $user_id = $this->membership_model->get_user_id($username);
                    $this->membership_model->add_activity_log($user_id, "change_mailing_list");
                    echo "2";
                    die;
                } else {

                    $data['error_message'] = "Update failure";
                    echo "3";
                    die;
                }
            }

            $user_bulk_param = $this->user_model->get_user_bulk_param($username);

            echo $user_bulk_param;
        }


        function down_pdf($invoice_id)
        {

            // rewrite to procedure

            $username = $this->site_data['username'];
            $invoice_id = strip_tags(mysql_real_escape_string($invoice_id));
            $invoice_id = trim($invoice_id);

            // check if invoice correpssponde to current User
            $invoice_username = $this->user_model->get_invoice_username($invoice_id);
            if ($username != $invoice_username) {

                redirect('user/invoices');
                return false;
            }



            $path = $this->user_model->get_invoice_pdf_path($invoice_id);
            $name = $invoice_id . '.pdf';
            $file_dir = base_url() . $path;
            $file = fopen($file_dir, "r");

            Header("Content-type: application/pdf");
            Header("Content-Disposition: attachment; filename=" . $name);
            readfile($file_dir);
        }


        function send_pdf($invoice_id)
        {

            $username = $this->site_data['username'];
            $invoice_id = strip_tags(mysql_real_escape_string($invoice_id));
            $invoice_id = trim($invoice_id);

            // check if invoice correpssponde to current User
            $invoice_username = $this->user_model->get_invoice_username($invoice_id);
            if ($username != $invoice_username) {

                redirect('user/invoices');
                return false;
            }


            $this->load->library('email');
            $user = $this->site_data['username'];
            $invoice_data = $this->user_model->get_invoices_data_by_id($invoice_id);

            $path = $this->user_model->get_invoice_pdf_path($invoice_id);
            $email = $this->membership_model->get_email($user);
            $title = $invoice_data['invoice_name'];

            $content = '';

            $this->email->from('admin@openweb.co.za', 'OpenWeb');
            $this->email->to($email);
            $this->email->subject($title);
            $this->email->message($content);
            $this->email->attach($path);
            $this->email->send();

            $msg = "The email has been sent successfully.";
            $this->session->set_flashdata('success_message', $msg);
            redirect('user/invoices');
        }



        function invoices()

        {

            $succ_msg = $this->session->flashdata('success_message');

            if ($succ_msg) {

                $data['success_message'] = $succ_msg;
            }



            $username = $this->site_data['username'];
            $invoices_list = $this->user_model->get_invoices_data($username); //echo "<pre>";print_r($new_date);die;
            foreach ($invoices_list as $list) {

                $inv_id = $list['id'];
                $date = $list['create_date'];
                $inv_user = $list['user_name'];
                $pdf_path = $this->user_model->get_invoice_pdf_path($inv_id);



                $pdf_data[] = array(

                    'id' => $inv_id,
                    'invoice_name' => $list['invoice_name'],
                    'user_name' => $inv_user,
                    'create_date' => $date,
                    'pdf_path' => $pdf_path,

                );

                if ($pdf_data) {

                    $data['invoices'] = $pdf_data;
                } else {

                    $data['invoices'] = '';
                }
            }

            $data['user_name'] = $username;
            $this->asignSidebarData($data);
            $data['main_content'] = 'user/invoices';
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['aditional_scripts'] = [
                'assets/plugins/dropzone/dropzone.min.js'
            ];

            $this->load->view('user/includes/template', $data);
        }



        function cancel_order($order_id = '')
        {

            $username = $this->site_data['username'];
            $imported_option = $this->user_model->get_user_imported_option($username);
            if ($imported_option)
                redirect("user/orders");

            redirect("user/orders");


            if (trim($order_id) != '') {

                $order_data = $this->product_model->get_order_data($order_id);
                if ($order_data['user'] != $username) {

                    redirect("user/orders");
                    return;
                }

                if ($order_data['billing_cycle'] == 'Daily' && $order_data['date_update'] != NULL && $order_data['change_flag'] == 1) {

                    $status = 'active';
                } else {
                    $status = $order_data['status'];
                }


                $cancel_order_flag = $order_data['cancel_flage'];


                $this->db->select('id, modify_service, product');
                $this->db->where('account_username', $order_data['account_username']);
                $this->db->where('account_password', $order_data['account_password']);
                $this->db->where('status', 'pending');
                $query = $this->db->get('orders');
                $result = $query->result_array();

                if ($result) {

                    $modify_service = $result[0]['modify_service'];
                    $modify_product_id = $result[0]['product'];
                    $modify_product_name = $this->product_model->get_product_name($modify_product_id);
                    $data['modify_service'] = $modify_service;
                    $data['modfiy_product'] = $modify_product_name;

                    //var_dump($modify_service);die;

                }

                if (isset($_GET['confirm']) && $_GET['confirm'] == true) {


                    // ini_set('display_errors', 1);
                    // $this->load->model('admin/order_model');
                    $this->load->model('admin/is_classes');
                    $this->order_model->delete_order($order_id);

                    $msg = 'The order has been cancelled successfully !';
                    $this->session->set_flashdata('success_message', $msg);
                    if ($order_data['billing_cycle'] == 'Daily') {

                        redirect("user/list_order_cloudsl");
                    } else {

                        redirect("user/orders");
                    }
                } else {

                    $data['cancel_flage'] = $cancel_order_flag;
                    $data['status'] = $status;
                    $data['main_content'] = 'user/confirmation';
                    $data['confirmation_type'] = 'delete';
                    $data['order_id'] = $order_id;

                    $data['sidebar'] = TRUE;
                    $this->load->view('user/includes/template', $data);
                }
            }
        }

        function asignSidebarData(&$data)
        {

            $data['first_name'] = $this->site_data['first_name'];
            $data['second_name'] = $this->site_data['second_name'];
            $data['ownumber'] = $this->site_data['ow'];

            $role = $this->session->userdata('role');
            $data['role'] = $role;
            $data['categories'] = $this->product_model->get_active_categories($role);
            $data['sub_categories'] = $this->product_model->get_active_subcategories($data['categories'], $role);
        }

        function revoke_order($order_id)
        {

            $username = $this->site_data['username'];
            $imported_option = $this->user_model->get_user_imported_option($username);
            if ($imported_option)
                redirect("user/orders");

            //$this->load->model('admin/order_model');

            $order_id = strip_tags(mysql_real_escape_string($order_id));
            $order_id = trim($order_id);
            $order_data = $this->product_model->get_order_data($order_id);
            if ($order_data['user'] != $username) {

                redirect("user/orders");
                return;
            }




            $result = $this->order_model->revoke_order($order_id);
            $revoke_date = date('F Y', strtotime($result));



            $msg = "Pending cancellation at the end of " . $revoke_date . ".";

            //$msg = "The order has been revoked the cancellation successfully, it will be cancelling in ".$revoke_date.".";

            $this->session->set_flashdata('information', $msg);
            $order = $this->cloudsl_model->get_orders_cloudsl('', $order_id);

            if ($order[0]['billing_cycle'] == 'Daily') {

                redirect('user/add_account');
            } else {
                redirect('user/orders');
            }
        }



        function logout()

        {

            $this->session->sess_destroy();
            setcookie('openweb-login', '', time() - 3600, '/');
            $this->site_data = array();
            $data['main_content'] = 'logged_out';
            $data['sidebar'] = FALSE;
            $data['navbar'] = FALSE;
            $this->load->view('user/includes/template', $data);
        }



        function dashboard()
        {

            $username = $this->site_data['username']; //var_dump($username);die;
            $notifications = $this->user_model->get_notifications($username);
            $products = [];
            $orders = $this->user_model->get_active_orders($username, 10, 0, array('adsl', 'fibre-line', 'fibre-data', 'lte-a', 'mobile'));


            $data['products'] = count($orders);
            $data['notifications'] = $notifications;
            $data['username'] = $username;
            $this->asignSidebarData($data);
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['main_content'] = 'user/dashboard';
            $data['aditional_scripts'] = ['js/dashboard2.js'];
            $this->load->view('user/includes/template', $data);
        }



        function billing()
        {

            $username = $this->site_data['username'];
            // get user_id
            $user_id = $this->user_model->get_user_id($username);
            $data['user_id'] = '';
            if (!empty($user_id))
                $data['user_id'] = $user_id;


            $billing_data = $this->user_model->get_billing_data($username);
            if ($billing_data) {
                $data['user_data']['user_billing'] = $billing_data;
            } else {
                $data['user_data']['user_billing'] = '';
            }

            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/billing';
            $this->load->view('user/includes/template', $data);
        }

        function avios_settings()
        {

            $username = $this->site_data['username'];
            // get user_id
            $user_id = $this->user_model->get_user_id($username);
            $data['user_id'] = '';
            if (!empty($user_id))
                $data['user_id'] = $user_id;

            $data['user_data'] = $this->user_model->get_user_data_by_id($user_id);

            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/avios_settings';
            $this->load->view('user/includes/template', $data);
        }

        function update_avios_settings()
        {

            $username = trim($this->site_data['username']);
            $user_id = $this->user_model->get_user_id($username);

            $avios_id = isset($_POST['avios_id']) ? $this->db->escape_str($_POST['avios_id']) : '';
            $avios_id = trim($avios_id);

            $br_a_id = isset($_POST['br_a_id']) ? $this->db->escape_str($_POST['br_a_id']) : '';
            $br_a_id = trim($br_a_id);

            $avios_settings = array(
                'avios_id' => $avios_id,
                'br_a_id' => $br_a_id
            );

            $result = $this->user_model->addAviosIds($avios_settings, $user_id);

            if ($result == true) {
                $data['user_data']['user_settings']['avios_id'] = $avios_id;
                $data['user_data']['user_settings']['br_a_id'] = $br_a_id;
                $data['mess'] = "Well done! You have successfully submitted your Club information.
            If you have a product that qualifies for Avios, you will now automatically start collecting.";
            } else {
                $data['err_mess'] = "Information not saved. ID must be unique";
            }
            /*
        $data['sidebar'] = TRUE;
        $data['main_content'] = 'user/avios_settings';
        $this->load->view('user/includes/template', $data);
        */
            $this->settings('tab4', $data['mess']);
        }

        function update_billing()
        {
            $username = $this->site_data['username'];
            $user_id = $this->user_model->get_user_id($username);
            $msg = '';

            $username = trim($username);
            $billing_name = isset($_POST['billing_name']) ? strip_tags(mysql_real_escape_string($_POST['billing_name'])) : '';
            $billing_name = trim($billing_name);

            $address_1 = isset($_POST['address_1']) ? strip_tags(mysql_real_escape_string($_POST['address_1'])) : '';
            $address_1 = trim($address_1);

            $address_2 = isset($_POST['address_2']) ? strip_tags(mysql_real_escape_string($_POST['address_2'])) : '';
            $address_2 = trim($address_2);

            $city = isset($_POST['city']) ? strip_tags(mysql_real_escape_string($_POST['city'])) : '';
            $city = trim($city);

            $province = isset($_POST['province']) ? strip_tags(mysql_real_escape_string($_POST['province'])) : '';
            $province = trim($province);

            $country = isset($_POST['country']) ? strip_tags(mysql_real_escape_string($_POST['country'])) : '';
            $country = trim($country);

            $postal_code = isset($_POST['postal_code']) ? strip_tags(mysql_real_escape_string($_POST['postal_code'])) : '';
            $postal_code = trim($postal_code);

            $email = isset($_POST['email']) ? strip_tags(mysql_real_escape_string($_POST['email'])) : '';
            $email = trim($email);

            $contact_number = isset($_POST['contact_number']) ? strip_tags(mysql_real_escape_string($_POST['contact_number'])) : '';
            $contact_number = trim($contact_number);

            $mobile = isset($_POST['contact_number']) ? strip_tags(mysql_real_escape_string($_POST['contact_number'])) : '';
            $mobile = trim($mobile);

            $sa_id_number = isset($_POST['sa_id_number']) ? strip_tags(mysql_real_escape_string($_POST['sa_id_number'])) : '';
            $sa_id_number = trim($sa_id_number);

            $billing_settings = array(

                'username' => $username,
                'billing_name' => $billing_name,
                'address_1' => $address_1,
                'address_2' => $address_2,
                'city' => $city,
                'province' => $province,
                'country' => $country,
                'postal_code' => $postal_code,
                'email' => $email,
                'contact_number' => $contact_number,
                'mobile' => $mobile,
                'id_user' => $user_id,

                'sa_id_number' => $sa_id_number,

                //'name_on_card' => isset($_POST['name_on_card']) ? $_POST['name_on_card'] : '',
                //'card_num' => isset($_POST['card_num']) ? $_POST['card_num'] : '',
                //'expires_month' => isset($_POST['expires_month']) ? $_POST['expires_month'] : '',
                //'expires_year' => isset($_POST['expires_year']) ? $_POST['expires_year'] : '',
                //'cvc' => isset($_POST['cvc']) ? $_POST['cvc'] : '',

                //'bank_name' => isset($_POST['bank_name']) ? $_POST['bank_name'] : '',
                //'bank_account_number' => isset($_POST['bank_account_number']) ? $_POST['bank_account_number'] : '',
                //'bank_account_type' => isset($_POST['bank_account_type']) ? $_POST['bank_account_type'] : '',
                //'bank_branch_code' => isset($_POST['bank_branch_code']) ? $_POST['bank_branch_code'] : '',

            );


            $this->db->select('id');
            $this->db->where('username', $username);
            $result = $this->db->get('billing');

            if ($result->num_rows == 1) {

                $this->db->where('username', $username);
                $ret = $this->db->update('billing', $billing_settings);
                $data = $result->result_array();
                $id = $data[0]['id'];
            } else {
                $ret = $this->db->insert('billing', $billing_settings);
                $id = $this->db->insert_id();
            }


            $link = 'admin/edit_account/' . $user_id;
            $date = date('l jS \of F Y \a\t h:i A');
            $activity = "On $date, $username updated his/her CC information. \n";
            $activity .= "View the detail through the link below: \n";
            $activity .= "<a href='" . base_url() . $link . "' target='_blank'>" . base_url() . $link . "</a>";
            $activity = array(

                'user' => $username,
                'activity' => $activity,
                'type' => 'User Change CC Information',
                'link' => $link,

            );
            $this->db->insert('activity_log', $activity);


            $billing_data = $this->user_model->billing_data($id);
            if ($billing_data) {

                $data['user_data']['user_billing'] = $billing_data;
            } else {

                $data['user_data']['user_billing'] = '';
            }

            if ($ret) {

                $msg = "The billing Information has been saved successfully.";
                $data['succ_message'] = $msg;
            } else {

                $msg = "Failed to save the billing Information.";
                $data['error_message'] = $msg;
            }

            $this->settings('tab2', $msg);
            /*
		$data['sidebar'] = TRUE;
		$data['main_content'] = 'user/billing';
		$this->load->view('user/includes/template', $data);*/
        }



        function edit_order($order_id)

        {

            $this->load->model('admin/order_model');

            $data['product_list'] = $this->product_model->get_product_list();

            $data['order_id'] = $order_id;

            $order_data = $this->order_model->get_order_data($order_id);



            //var_dump($order_data['change_flag']);die();

            if ($order_data['change_flag'] == 1) {

                if (isset($order_data['user'])) {

                    $user = $order_data['user'];
                } else {

                    $user = '';
                }

                $signed_in_user = $this->site_data['username'];

                if ($user != $signed_in_user) {

                    die('This does not appear to be your order');
                }

                $user_name = $this->user_model->get_user_name($user);

                //$comment = '';

                if (isset($order_data['product'])) {

                    $product = $order_data['product'];

                    $product_name = $this->product_model->get_product_name($product);

                    $amount = $order_data['price'];
                }

                $data['user'] = $user;

                $data['user_name'] = $user_name;



                $data['order_data'] = $order_data;

                $data['sidebar'] = TRUE;
                $data['navbar'] = TRUE;

                $data['order_key'] = $this->user_model->order_key();

                $data['main_content'] = 'user/manage_order';

                $suc_msg = $this->session->flashdata('success_message');

                $data['messages']['success_message'] = $suc_msg;

                $this->asignSidebarData($data);

                $this->load->view('user/includes/template', $data);
            } else {

                //$warn_msg = "You do not have permission to modify your password";
                $warn_msg = "This information is not available on the product you have chosen at this time.";

                $data['messages']['warning_message'] = $warn_msg;

                $data['main_content'] = 'user/warning_page';

                $data['sidebar'] = TRUE;
                $data['navbar'] = TRUE;
                $this->asignSidebarData($data);

                $this->load->view('user/includes/template', $data);
            }
        }



        function get_month_usage()

        {

            $this->load->model('admin/order_model');
            $date = $_POST['date'];
            $order_id = $_POST['order_id'];

            $order_data = $this->order_model->get_order_data($order_id);
            $class = $this->order_model->get_is_class($order_id);
            //$realm_data = $this->order_model->get_is_details($class);
            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];
            $realm = $realm_data['realm'];
            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

            $acc_username = trim($order_data['account_username']);
            $new_user = $acc_username . '@' . $realm;

            //get monthly info from api
            $year =  date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $month_data = $this->is_classes->get_monthly_stats_new($sess, $new_user, $year, $month);

            $days = array();
            $sents = array();
            $receives = array();
            $totals = array();

            if (!empty($month_data)) {
                foreach ($month_data as $m) {
                    $date = date('Y-m-d', strtotime($m['Date']));
                    $time = date('h:i:s', $m['TotalTimeConnected']);
                    $sent = round($m['BytesSent'] / 1000000, 2);
                    $received = round($m['BytesReceived'] / 1000000, 2);
                    $total = round($m['TotalUsageBytes'] / 1000000, 2);
                    $month_datas[] = array(

                        'Date' => $date,
                        'TotalTimeConnected' => $time,
                        'BytesSent' => $sent,
                        'BytesReceived' => $received,
                        'TotalUsageBytes' => $total,
                    );

                    $day = date('d', strtotime($m['Date']));
                    $sent_oral = $m['BytesSent'];
                    $received_oral = $m['BytesReceived'];
                    $total_oral = $m['TotalUsageBytes'];
                    $days[] = $day;
                    $sents[] = $sent_oral;
                    $receives[] = $received_oral;
                    $totals[] = $total_oral;
                }

                $data['month_stats_data'] = $month_datas;
                $data['day'] = $days;
                $data['sent'] = $sents;
                $data['received'] = $receives;
                $data['total'] = $totals;
            }
            echo json_encode($data);
        }



        function change_service_pwd($id)

        {

            $acc = $this->product_model->get_service_data($id);

            $change_flage = $acc['change_flag'];

            $acc_pwd = $acc['account_password'];



            $data['change_flag'] = $change_flage;

            $data['acc_pwd'] = $acc_pwd;

            $data['service_id'] = $id;

            $data['sidebar'] = TRUE;

            $data['main_content'] = 'user/service_password';

            $this->load->view('user/includes/template', $data);
        }

        function getLTEOrderMonthData()
        {

            $order_id = $_GET['order_id'];

            $order_data = $this->order_model->get_order_data($order_id);
            $days = date('d', time());
            $result = [];
            $m = date('m');

            for ($i = 1; $i <= $days; $i++) {

                $date = json_encode(["Year" => date('Y'), "Month" => date('m'), "Day" => $i]);
                $day = $this->network_api_handler_model->get_lte_day_usage($order_data['account_username'], $order_data['realm'], $date);

                array_push($result, ["y" => $i . "/" . $m, "a" => $day]);
            }

            echo json_encode($result);
        }

        function edit_active_order($order_id)
        {
            // get user from session
            $username = $this->site_data['username'];

            // check order id
            if (!$this->form_validation->numeric($order_id)) {
                redirect("user/orders");
                return;
            }


            // check access rights for current order
            $order_data = $this->order_model->get_order_data($order_id);
            if ($order_data['user'] != $username) {
                redirect("user/orders");
                return;
            }

            // check if it the Fibre order
            $is_fibre = $this->order_model->is_fibre_order($order_id);
            if ($is_fibre) {
                redirect("user/orders");
                return;
            }

            // get account and product info + access flags
            $product_id = $order_data['product'];
            $order_status = $order_data['status'];
            $product_data = $this->product_model->get_product_data($product_id); //echo "<pre>";print_r($product_data);die;
            $product_name = isset($product_data['name']) ? $product_data['name'] : null;  // product_name === service_name
            $change_flage = $order_data['change_flag'];
            $acc_pwd = $order_data['account_password'];
            $dispaly_usage = $order_data['display_usage'];
            $order_type = $order_data['type'];
            $parent = isset($product_data['parent']) ? $product_data['parent'] : null;
            $order_realm = $order_data['realm'];
            $service_type = $order_data['service_type'];

            $data['order_id'] = $order_id;
            if (!empty($product_data)) {
                $data['port_data']['port_active'] = $product_data['port_active'];
                $data['port_data']['port_service_id'] = $product_data['port_service_id'];
                $data['port_data']['port_duration'] = $product_data['port_duration'];
                $data['port_data']['port_counter'] = $product_data['port_counter'];
            }


            // ?

            // ? get additional info about current order
            $this->db->select('id, modify_service, product');
            $this->db->where('account_username', $order_data['account_username']);
            $this->db->where('account_password', $acc_pwd);
            $this->db->where('status', 'pending');
            $query = $this->db->get('orders');
            $result = $query->result_array();

            if ($result) {
                $modify_service = $result[0]['modify_service'];
                $modify_product_id = $result[0]['product'];
                $modify_product_name = $this->product_model->get_product_name($modify_product_id);
                $data['modify_service'] = $modify_service;
                $data['modfiy_product'] = $modify_product_name;
                //var_dump($modify_product_name);die;

            }


            // -- replace from here
            // -------------------------------------------------------------------------------

            // get current date and load network model
            $current_month = date('Y-m');
            $current_date = date('Y-m-d');



            // init usage vars           
            $monthly_billed_total_mb  = null;
            $monthly_unbilled_total_mb = null;
            $day_total_mb = null;
            $month_total = null;

            if ($order_data['service_type'] == 'lte-a') {
                $lte_usage_summary = $this->isdsl_model->getLteUsages($order_data['account_username'], $order_realm);

                $full_usage_data = $this->network_api_handler_model->get_lte_usage_all($order_data['account_username'], $order_realm);
                $full_usage_data = $this->user_model->user_percentage($full_usage_data, $order_id);

                $summary_lte_stats_data = $lte_usage_summary['Packages'];
                $summary_lte_stats_data = $this->order_model->user_percentage_sum($summary_lte_stats_data, $order_id, $full_usage_data['month_usage']);

                $data['summary_lte_stats_data'] = $summary_lte_stats_data;

                $error_messages = $full_usage_data['error'];
            } else {
                $full_usage_data = $this->network_api_handler_model->get_activity_info_day_month_yaer($order_data);
            }

            $month_total =  array(
                "billed_total"   => $full_usage_data["month_usage"],
                "unbilled_total" => $monthly_unbilled_total_mb,
            );
            $day_total = $full_usage_data["day_usage"];
            $year_total = $full_usage_data["year_usage"];


            $session_info = null;

            $current_session_info = isset($full_usage_data['sess']) ? $full_usage_data['sess'] : null;
            if (!empty($session_info['api_response'][0])) {


                $api_response = $session_info['api_response'][0];

                $current_session_info['Username'] = $order_data['account_username'] . "@" . $order_data['realm'];
                $start_time = $api_response->sessionStart;
                $end_time   = $api_response->sessionEnd;


                $a = new DateTime($start_time);
                $b = new DateTime($end_time);
                $interval = $a->diff($b);


                // default init
                $current_session_info['LoginTime'] = '';
                $current_session_info['SessionIPAddress'] = '';

                //echo $interval->format("%D %H:%I:%S");
                // $current_session_info['SessionLength'] = $interval->format("%D %H:%I:%S");
                $current_session_info['SessionLength'] = "Start : " . $a->format("H:i:s (m.d)");
                $current_session_info['SessionLength'] .= "<br/> End : " . $b->format("H:i:s (m.d)");
                $current_session_info['MegabytesSent'] = $api_response->session_outgoing_mb;
                $current_session_info['MegabytesReceived'] = $api_response->session_incoming_mb;
                $current_session_info['Total'] = $api_response->session_outgoing_mb + $api_response->session_incoming_mb;
            }

            if ($service_type == 'lte-a') {
                $current_session_info['SessionLength'] = "-";
                $current_session_info['LoginTime'] = $full_usage_data['sess'][0]->connect_time;
                $current_session_info['SessionIPAddress'] = $full_usage_data['sess'][0]->subscriber_ip;
                $current_session_info['MegabytesSent'] = '-';
                $current_session_info['MegabytesReceived'] = '-';
                $current_session_info['Total'] = '-';
                $current_session_info['Username'] = $full_usage_data['sess'][0]->UserName;
            }

            // get realm data
            // ------------------------------------------------------------------
            $class = $this->order_model->get_is_class($order_id);
            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];

            $lm = explode('@', $realm_data['user']);
            $realm = $lm[1];
            // -----------------------------------------------------------------

            // SET USAGE VARIABLES FOR VIEWER 
            $data['session_error_message'] = $error_messages['sess'];
            $data['session_data'] = $current_session_info;

            $data['year_error_message'] = $error_messages["year_usage"];
            //$data['year_stats_data'] = $year_total;
            $data['year_stats_data_total'] = $year_total;

            $data['month_error_message'] = $error_messages['month_usage'];
            //$data['month_stats_data']
            $data['month_stats_data_total'] = $month_total;
            $data['today_error_message'] = $error_messages['day_usage'];
            //$data['today_stats_data'] = $today_data;
            $data['today_stats_data_total'] = $day_total;


            $data['order_type'] = $order_data['service_type'];
            $data['change_flag'] = $change_flage;
            $data['display_usage'] = $dispaly_usage;
            $data['acc_pwd'] = $acc_pwd;
            $data['order_status'] = $order_status;
            $data['current_service'] = $product_name;
            $data['order_id'] = $order_id;
            $data['order_key'] = $this->user_model->order_key();




            // Show additional messages for Port Reset

            $this->load->model('port_model');
            $port_service_info = $this->product_model->get_classes_data($product_data['port_service_id']);

            if (isset($port_service_info[0]))
                $port_service_info = $port_service_info[0];

            $port_access = $this->port_model->client_port_validation(
                $order_data,
                $product_data,
                // realm check will be ignored
                array('realm' => $realm),
                $port_service_info
            );

            $data['port_available'] = $port_access['port_available'];
            $data['port_enabled'] = $port_access['port_enabled'];
            $data['port_message'] = $port_access['message'];

            // improted option
            $username = $this->site_data['username'];
            $imported_option = $this->user_model->get_user_imported_option($username);
            $data['imported_user'] = $imported_option;
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['main_content'] = 'user/order_detail';

            if ($order_data['service_type'] == 'lte-a') {
                $data['aditional_scripts'] = [
                    'js/edit_order_lte.js',
                    'assets/plugins/jquery-morris-chart/js/morris.min.js'
                ];
            } else {
                $data['aditional_scripts'] = [
                    'js/edit_order.js',
                    'assets/plugins/jquery-morris-chart/js/morris.min.js'
                ];
            }

            $this->load->model('lte_usage_stats_model');
            $data['lte_usage_stats_model'] = $this->lte_usage_stats_model;

            $this->asignSidebarData($data);
            $this->load->view('user/includes/template', $data);
        }

        function getMonthUsageADSL()
        {

            $order_id = $_GET['order_id'];

            $order_data = $this->order_model->get_order_data($order_id);

            $options = [
                "period" => date("Y-m-d"),
                "activity_type" => "month"
            ];

            $result = [];

            $isdsl_response = $this->is_classes->get_activity_info_with_handler($order_data, $options);

            foreach ($isdsl_response['api_response']['arrUsageStats'] as $day) {

                $mb = number_format($day['TotalUsageBytes'] / 1048576, 0, '.', '');
                array_push($result, ["y" => $day['Date'], "a" => $mb]);
            }

            echo json_encode($result);
        }

        function getYearUsageADSL()
        {

            $order_id = $_GET['order_id'];

            $order_data = $this->order_model->get_order_data($order_id);

            $options = [
                "period" => date("Y-m-d"),
                "activity_type" => "year"
            ];

            $result = [];
            $current_year = date("o", mktime());

            $isdsl_response = $this->is_classes->get_activity_info_with_handler($order_data, $options);

            foreach ($isdsl_response['api_response']['arrUsageStats'] as $day) {


                if (date("o", strtotime($day['YearMonth'])) == $current_year) {
                    $date = date("o-n", strtotime($day['YearMonth']));
                    $mb = number_format($day['TotalUsageBytes'] / 1048576, 0, '.', '');
                    array_push($result, ["y" => $date, "a" => $mb]);
                }
            }

            echo json_encode($result);
        }


        function reset_port($order_id)
        {

            // get user from session
            $username = $this->site_data['username'];

            // VALIDATION
            // check order id
            if (!$this->form_validation->numeric($order_id)) {
                redirect("user/orders");
                return;
            }

            $order_data = $this->order_model->get_order_data($order_id);
            if ($order_data['user'] != $username) {
                redirect("user/orders");
                return;
            }

            // check if it the Fibre order
            $is_fibre = $this->order_model->is_fibre_order($order_id);
            if ($is_fibre) {

                redirect("user/orders");
                return;
            }

            $this->load->model('port_model');
            $reset_result = $this->port_model->wrap_port_reset($order_id, 0, true); // send links for models (is_admin = 1)


            //$msg = 'Port was reset unsuccessfully';
            //$msg = $reset_result['message'];
            $msg = $reset_result['user_message'];
            $type_of_message = 'error_message';
            if ($reset_result['result'])
                $type_of_message = 'success_message';


            $this->session->set_flashdata($type_of_message, $msg);
            redirect("user/orders");
        }


        function edit_service()
        {

            $this->load->model('admin/order_model');
            redirect("user/orders");


            if (isset($_POST['order_id'])) {

                $order_id = $_POST['order_id'];
                $this->session->set_userdata(array("order_id" => $order_id));
                $order_data =  $this->order_model->get_order_data($order_id); //current order data

                $product_id = $order_data['product'];
                $product_data = $this->product_model->get_product_data($product_id); //current product data

                if ($product_data) {
                    $data['product_data'] = $product_data;
                }

                $class = $product_data['class'];
                $curr_class = $this->product_model->get_classes($class); //get current class data

                if ($curr_class) {
                    $data['class'] = $curr_class[0];
                }
            }


            $products_data = $this->product_model->get_another_product_data($product_id); //list all product data
            $data['products'] = $products_data;
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/edit_service';
            $this->load->view('user/includes/template', $data);
        }



        function upgrade($product_id)

        {


            // check imported user
            $username = $this->site_data['username'];
            $imported_option = $this->user_model->get_user_imported_option($username);
            if ($imported_option == 1)
                redirect("user/dashboard");



            if ($product_id) {

                $product_data = $this->product_model->get_product_data($product_id);

                if ($product_data) {

                    $data['product_data'] = $product_data;

                    $class = $product_data['class'];

                    $curr_class = $this->product_model->get_classes($class); //get current class data

                    if ($curr_class) {

                        $curr_class = $curr_class[0];

                        $class_detail = "({$curr_class['realm']}) - {$curr_class['desc']}";

                        $data['class'] = $class_detail;
                    }
                }
            }

            $data['title'] = "upgrade";

            $data['sidebar'] = TRUE;

            $data['main_content'] = 'user/change_service';

            $this->load->view('user/includes/template', $data);
        }



        function downgrade($product_id)

        {

            // check imported user
            $username = $this->site_data['username'];
            $imported_option = $this->user_model->get_user_imported_option($username);
            if ($imported_option == 1)
                redirect("user/dashboard");

            if ($product_id) {

                $product_data = $this->product_model->get_product_data($product_id);

                if ($product_data) {

                    $data['product_data'] = $product_data;

                    $class = $product_data['class'];

                    $curr_class = $this->product_model->get_classes($class); //get current class data

                    if ($curr_class) {

                        $curr_class = $curr_class[0];

                        $class_detail = "({$curr_class['realm']}) - {$curr_class['desc']}";

                        $data['class'] = $class_detail;
                    }
                }
            }

            $data['title'] = "downgrade";

            $data['sidebar'] = TRUE;

            $data['main_content'] = 'user/change_service';

            $this->load->view('user/includes/template', $data);
        }



        function checkout()
        {

            $this->load->model('admin/order_model');
            $order_id = $this->session->userdata('order_id');

            $order_data =  $this->order_model->get_order_data($order_id); //current order data
            $curr_product_id = $order_data['product'];
            $curr_acc_user = $order_data['account_username'];
            $curr_username = $order_data['user'];

            $product_data = $this->product_model->get_product_data($curr_product_id); //current product data
            $class = $product_data['class'];
            $curr_class = $this->product_model->get_classes($class);
            $curr_realm = $curr_class[0]['realm'];
            $full_acc_user = $curr_acc_user . '@' . $curr_realm;
            $data['account_username'] = $full_acc_user;

            $discount = $this->membership_model->get_discount($curr_username);

            if ($discount) {

                $data['discount'] = $discount;
            } else {

                $data['discount'] = 0;
            }


            if ($product_data) {

                $data['curr_service_data'] = $product_data;
            } else {

                $data['curr_service_data'] = '';
            }



            $service_id = $_POST['service_id'];
            $sevice_data = $this->product_model->get_product_data($service_id);

            if ($sevice_data) {

                $data['new_service_data'] = $sevice_data; //new service data
            } else {

                $data['new_service_data'] = '';
            }



            $title = $_POST['title'];
            if ($title == 'upgrade') {

                $method = $_POST['grade'];
                if ($method == 0) {

                    $data['grade'] = '0';
                } else {

                    $data['grade'] = '1';
                }

                $data['method'] = 'upgrade';
            } else {

                $data['method'] = 'downgrade';
                $data['grade'] = '0';
            }

            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/checkout';
            $this->load->view('user/includes/template', $data);
            //echo "<pre>";var_dump($data['method']);die;

        }



        function change_service()
        {

            $this->load->model('admin/order_model');

            // some redirect, check !

            $curr_date = date("Ym", time());
            $curr_year = substr($curr_date, 0, 4);
            $curr_mon = substr($curr_date, 4, 2);
            $curr_nextmonth = mktime(0, 0, 0, $curr_mon + 1, 1, $curr_year);
            $fm_next_month = date("Y-m-01 H:i:s", $curr_nextmonth);


            if (!empty($_POST)) {

                $title = $_POST['title'];
                $product_id = $_POST['service_id'];
                $order_id = $this->session->userdata('order_id');

                //echo "<pre>";print_r($order_id);die;

                if ($product_id && $order_id) {

                    $order_data =  $this->order_model->get_order_data($order_id);
                    $user_id = $order_data['id_user'];
                    $acc_username = $order_data['account_username'];
                    $acc_password = $order_data['account_password'];

                    $product_data = $this->product_model->get_product_data($product_id);
                    if ($product_data) {

                        $username = $order_data['user'];
                        $full_name = $this->membership_model->get_user_name_nice($username);
                        $price = $product_data['price'];
                        $pro_rata_option = $product_data['pro_rata_option'];
                        $total_price = $this->product_model->get_discounted_price($username, $price);
                        $pro_rata_price = $this->product_model->get_pro_rate_price($pro_rata_option, $price);
                        $pro_discounted = $this->product_model->get_discounted_price($username, $pro_rata_price);
                        $product_name = $product_data['name'];

                        $new_comment = $full_name . '(Client)(R' . $price . ' - ' . $product_name . ')(DEBIT ORDER)';
                        $pro_type = $product_data['type'];
                        $class = $product_data['class'];

                        //$realm_data = $this->order_model->get_is_details($class);
                        $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

                        $realm = $realm_data['realm'];
                        $rl_user = $realm_data['user'];
                        $rl_pass = $realm_data['pass'];
                        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);
                        $account_username = $acc_username . '@' . $realm;


                        if ($title == 'upgrade') {

                            $method = $_POST['grade'];
                            //upgrade right now
                            if ($method == 1) {

                                $resp = $this->is_classes->set_account_class_new($sess, $account_username, $class);    //return null
                                //var_dump($resp);die;
                                //update order info
                                //if($resp == 1){}
                                $data = array(

                                    'product' => $product_id,
                                    'price' => round($total_price, 2),
                                    'pro_rata_extra' => round($pro_discounted, 2),
                                    'account_comment' => $new_comment,
                                    'realm' => $realm,
                                    'date' => date('Y-m-d H:i:s', time()),
                                );

                                $result = $this->product_model->update_order_by_service($order_id, $data);
                                if ($result) {
                                    $data['type'] = "upgrade_immediately";
                                }
                            } else {    //upgrade next month

                                if ($pro_type != 'daily') {
                                    $resp = $this->is_classes->set_pending_update_new($sess, $account_username, $class);
                                    $data = array(

                                        'user' => $username,
                                        'product' => $product_id,
                                        'status' => 'pending',
                                        'price' => round($total_price, 2),
                                        'pro_rata_extra' => round($pro_discounted, 2),
                                        'account_username' => $acc_username,
                                        'account_password' => $acc_password,
                                        'account_comment' => $new_comment,
                                        'realm' => $realm,
                                        'date_update' => $fm_next_month,
                                        'date' => date('Y-m-d H:i:s', time()),
                                        'display_usage' => 0,
                                        'type' => 'auto',
                                        'cancel_flage' => 0,
                                        'change_flag' => 0,
                                        'id_user' => $user_id,
                                        'modify_service' => 'Upgrading'

                                    );
                                    $data['type'] = "upgrade_next_month";
                                } else {

                                    $date = strtotime($order_data['date']);
                                    $time_add = date('H:i', $date);
                                    $time_or = date('H:i', time());
                                    if ($time_add > $time_or)
                                        $time = date('Y-m-d', time());
                                    else
                                        $time = date('Y-m-d', time() + 86400);

                                    $data = array(

                                        'user' => $username,
                                        'product' => $product_id,
                                        'status' => 'pending',
                                        'price' => round($total_price, 2),
                                        'pro_rata_extra' => round($pro_discounted, 2),
                                        'account_username' => $acc_username,
                                        'account_password' => $acc_password,
                                        'account_comment' => $new_comment,
                                        'realm' => $realm,
                                        'date_update' => $time,
                                        'date' => $order_data['date'],
                                        'display_usage' => 0,
                                        'type' => 'auto',
                                        'cancel_flage' => 0,
                                        'change_flag' => 0,
                                        'modify_service' => 'Upgrading',
                                        'billing_cycle' => 'Daily',
                                        'id_user' => $user_id,
                                    );

                                    $data['type'] = "upgrade_next_day";
                                }

                                //if($resp ==1){}
                                $this->db->select('id');
                                $this->db->where('id !=', $order_id);
                                $this->db->where('status', 'pending');
                                $this->db->where('date_update !=', '');
                                $this->db->where('user', $username);
                                $this->db->where('account_username', $acc_username);
                                $this->db->where('account_password', $acc_password);
                                $query = $this->db->get('orders');


                                /* 	$data = array(
								'user' => $username,
								'product' => $product_id,
								'status' => 'pending',
								'price' => round($total_price, 2),
								'pro_rata_extra' =>round($pro_discounted, 2),
								'account_username' => $acc_username,
								'account_password' => $acc_password,
								'account_comment' => $new_comment,
								'date_update' => $fm_next_month,
								'date' => date('Y-m-d H:i:s',time()),
								'display_usage' => 0,
								'type' => 'auto',
								'cancel_flage' => 0,
								'change_flag' => 0,
								'modify_service' => 'Upgrading'
							); */

                                if ($query->num_rows == 0) {

                                    $this->db->insert('orders', $data);
                                } else {

                                    $result_exist = $query->result_array();
                                    $id = $result_exist[0]['id'];
                                    $this->db->where('id', $id);
                                    $this->db->update('orders', $data);
                                }
                            }
                        } elseif ($title == 'downgrade') {

                            if ($pro_type != 'daily') {
                                $resp = $this->is_classes->set_pending_update_new($sess, $account_username, $class);
                                $data = array(

                                    'user' => $username,
                                    'product' => $product_id,
                                    'status' => 'pending',
                                    'price' => round($total_price, 2),
                                    'pro_rata_extra' => round($pro_discounted, 2),
                                    'account_username' => $acc_username,
                                    'account_password' => $acc_password,
                                    'account_comment' => $new_comment,
                                    'realm' => $realm,
                                    'date_update' => $fm_next_month,
                                    'date' => date('Y-m-d H:i:s', time()),
                                    'display_usage' => 0,
                                    'type' => 'auto',
                                    'cancel_flage' => 0,
                                    'change_flag' => 0,
                                    'id_user' => $user_id,
                                    'modify_service' => 'Downgrading'
                                );

                                $data['type'] = "upgrade_next_month";
                            } else {

                                $date = strtotime($order_data['date']);
                                $time_add = date('H:i', $date);
                                $time_or = date('H:i', time());
                                if ($time_add > $time_or)

                                    $time = date('Y-m-d', time());
                                else
                                    $time = date('Y-m-d', time() + 86400);
                                $data = array(

                                    'user' => $username,
                                    'product' => $product_id,
                                    'status' => 'pending',
                                    'price' => round($total_price, 2),
                                    'pro_rata_extra' => round($pro_discounted, 2),
                                    'account_username' => $acc_username,
                                    'account_password' => $acc_password,
                                    'account_comment' => $new_comment,
                                    'realm' => $realm,
                                    'date_update' => $time,
                                    'date' => $order_data['date'],
                                    'display_usage' => 0,
                                    'type' => 'auto',
                                    'cancel_flage' => 0,
                                    'change_flag' => 0,
                                    'modify_service' => 'Downgrading',
                                    'billing_cycle' => 'Daily',
                                    'id_user' => $user_id,

                                );

                                $data['type'] = "downgrade_next_day";
                            }

                            //if($resp ==1){}

                            $this->db->select('id');
                            $this->db->where('id !=', $order_id);
                            $this->db->where('status', 'pending');
                            $this->db->where('date_update !=', '');
                            $this->db->where('user', $username);
                            $this->db->where('account_username', $acc_username);
                            $this->db->where('account_password', $acc_password);
                            $query = $this->db->get('orders');

                            if ($query->num_rows == 0) {

                                $this->db->insert('orders', $data);
                            } else {

                                $result_exist = $query->result_array();
                                $id = $result_exist[0]['id'];
                                $this->db->where('id', $id);
                                $this->db->update('orders', $data);
                            }
                            $data['type'] = "downgrade";
                        }
                    }
                }
            }

            $data['username'] = "$acc_username@$realm";
            $data['password'] = $acc_password;
            $data['comment'] = $new_comment;
            $data['product_name'] = $product_name;
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/success';
            $this->load->view('user/includes/template', $data);
        }



        function updata_order_next_month()

        {

            $time = date('Y-m-d', time());

            $this->db->select('date_update');

            $order_data = $this->db->get('orders');
            var_dump($order_data);
            die;

            foreach ($order_data as $or) {

                $update = $or['date_update'];

                $order_id = $or['id'];

                if ($update != '') {

                    $update = date('Y-m-d', strtotime($update));

                    if ($time == $update) {

                        $data = array(

                            'status' => 'active',

                        );

                        $this->db->where('id', $order_id);

                        $this->db->update('orders', $data);
                    }
                }
            }
        }



        function active_orders($start = 0)
        {

            $this->session->set_userdata(array("order_status" => 'active'));

            // load port model
            $this->load->model('port_model');


            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);

            $this->user_model->update_cancellations($username);
            $num_per_page = NUM_PER_PAGE;
            $orders = $this->user_model->get_active_orders($username, $num_per_page, $start, array('adsl', 'fibre-line', 'fibre-data', 'lte-a', 'mobile'));

            $topup_orders = $this->product_model->check_topup_available_for_orders($orders, $user_id);
            $this->load->library('pagination');
            $config['base_url'] = base_url('index.php/user/active_orders');
            $config['total_rows'] = $this->user_model->get_active_orders_count($username, array('adsl', 'fibre-line', 'fibre-data', 'lte-a', 'mobile'));
            $config['per_page'] = $num_per_page;
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li><a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['first_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['first_tag_close'] = '</li>';

            $this->pagination->initialize($config);
            $data['pages'] = $this->pagination->create_links();

            //echo "<pre>"; var_dump($orders);die;
            $this->load->model('user_docs_model');
            $order_data = array();
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $product_id = $order['product'];
                    $status = $order['status'];
                    $date = $order['date'];
                    $product_data = $this->product_model->get_product_data($product_id);
                    if (isset($product_data['parent'])) {
                        $parent = $product_data['parent'];
                        //$realm = $this->product_model->get_product_realm($product_id);
                        $realm = $this->order_model->get_order_realm($order['id'], $product_data['class']);
                        // $acc_username = $order['account_username'] . '@' . $realm;
                        $acc_username = $order['account_username'];
                        $acc_password = $order['account_password'];
                        // if port active and order status 'active' -> show button ['port_available']
                        // if all other validations are fine -> enable button ['port_enabled']
                        $port_service_info = $this->product_model->get_classes_data($product_data['port_service_id']);
                        if (isset($port_service_info[0]))
                            $port_service_info = $port_service_info[0];

                        $port_access = $this->port_model->client_port_validation(
                            $order,
                            $product_data,
                            // realm check will be ignored
                            array('realm' => $realm),
                            $port_service_info
                        );
                        if (!isset($order['fibre']))
                            $order['fibre'] = null;
                    }
                    $starts_button_type = '';
                    // get PRODUCT MOBILE DATA info
                    $current_mobile_data = $this->user_docs_model->get_mobile_request_for_client($order['id'], $user_id);

                    if ($order['fibre']['lte_type'] == 'telkom') {
                        $this->db->select("telkom_status");
                        $this->db->from('telkome_stat');
                        $this->db->where('telkom_user_code', $order['id']);
                        $query = $this->db->get();
                        $record_btn = $query->result_array();
                    } else {
                        $this->db->select("mtn_status");
                        $this->db->from('mtn_stat');
                        $this->db->where('mtn_user_code', $order['id']);
                        $query = $this->db->get();
                        $record_btn = $query->result_array();
                    }



                    if ($order['service_type'] == 'mobile') {
                        $this->db->select("mobile_status");
                        $this->db->from('mobile_stat');
                        $this->db->where('mobile_user_code', $order['id']);
                        $query = $this->db->get();
                        $record_btn = $query->result_array();
                    }


                    $order_data[] = array(
                        'status' => $status,
                        'date' => $date,
                        'product_data' => $product_data,
                        'product_id' => $product_id,
                        'id' => $order['id'],
                        'acc_username' => $acc_username,
                        'acc_password' => $acc_password,
                        'mobile_data' => $current_mobile_data,
                        'service_type'  => $order['service_type'],
                        'fibre'         => $order['fibre'],
                        'port_available' => $port_access['port_available'],
                        'port_enabled'   => $port_access['port_enabled'],
                        'port_message'   => $port_access['message'],
                        'realm' => $order['realm'],
                        'stats_button_status' => $record_btn,
                        'lte_username' => $order['username'],
                        'sim_serial_no' => $order['fibre']['sim_serial_no'],
                        'network' => $order['service_type']
                    );
                }
            }
            // echo '<pre>';
            // print_r($order_data);
            // echo '</pre>';
            // die;

            $this->asignSidebarData($data);

            $data['username'] = $username;
            $data['topups'] = $topup_orders;

            $data['title'] = 'Active Services';
            $data['order_status'] = 'active';
            $data['orders'] = $order_data;
            $data['sidebar'] = TRUE;
            $data['navbar'] = true;
            $data['main_content'] = 'user/view_orders';

            $this->load->view('user/includes/template', $data);
        }



        function inactive_orders($start = 0)
        {

            $this->session->set_userdata(array("order_status" => 'inactive'));

            $username = $this->site_data['username'];
            $this->user_model->update_cancellations($username);
            $num_per_page = NUM_PER_PAGE;
            $orders = $this->user_model->get_inactive_orders($username, $num_per_page, $start, array('adsl', 'fibre-line', 'fibre-data'));



            $this->load->library('pagination');
            $config['base_url'] = base_url('user/inactive_orders');
            $config['total_rows'] = $this->user_model->get_inactive_orders_count($username, array('adsl', 'fibre-line', 'fibre-data'));
            $config['per_page'] = $num_per_page;
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li><a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['first_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['first_tag_close'] = '</li>';


            $this->pagination->initialize($config);
            $data['pages'] = $this->pagination->create_links();

            $order_data = array();
            if (!empty($orders)) {

                foreach ($orders as $order) {

                    $product_id = $order['product'];
                    $status = $order['status'];
                    $date = $order['date'];
                    $product_data = $this->product_model->get_product_data($product_id);
                    $parent = $product_data['parent'];

                    if ($parent == 'legacy') {

                        $acc_username = $order['account_username'];
                        $acc_password = $order['account_password'];
                    } else {

                        //$realm = $this->product_model->get_product_realm($product_id);
                        $realm = $this->order_model->get_order_realm($order['id'], $product_data['class']);

                        $acc_username = $order['account_username'] . '@' . $realm;
                        $acc_password = $order['account_password'];
                    }

                    $order_data[] = array(

                        'status' => $status,
                        'date' => $date,
                        'product_data' => $product_data,
                        'product_id' => $product_id,
                        'id' => $order['id'],
                        'acc_username' => $acc_username,
                        'acc_password' => $acc_password,

                        'service_type'  => $order['service_type'],
                        'fibre'         => $order['fibre'],
                    );
                }
            }
            $this->asignSidebarData($data);
            $data['title'] = 'Inactive Services';
            $data['orders'] = $order_data;
            $data['order_status'] = 'inactive';
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['main_content'] = 'user/view_orders';
            $this->load->view('user/includes/template', $data);
        }



        function orders($start = 0)
        {



            $this->session->set_userdata(array("order_status" => 'all'));
            $info = $this->session->flashdata('information');
            $data['info'] = $info; //var_dump($info);die;

            // load port model
            $this->load->model('port_model');



            $success_message = $this->session->flashdata('success_message');
            $error_message   = $this->session->flashdata('error_message');

            $data['success_message'] = $success_message;
            $data['error_message'] = $error_message;

            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);


            $this->user_model->update_cancellations($username);
            $num_per_page = NUM_PER_PAGE;
            $orders = $this->user_model->get_orders($username, $num_per_page, $start, array('adsl', 'fibre-line', 'fibre-data', 'lte-a'));
            // filter orders

            $topup_orders = $this->product_model->check_topup_available_for_orders($orders, $user_id);
            // get TopUp flags for all orders  (#1 if topUp active, if user already bought 1,2,3 topUps  )
            /*
         [id] ->
            ["topup_config"]=>
            ["topup_current_level"]=>
        */

            $this->load->library('pagination');
            $config['base_url'] = base_url('index.php/user/orders');
            $config['total_rows'] = $this->user_model->get_orders_count($username, array('adsl', 'fibre-line', 'fibre-data'));
            $config['per_page'] = $num_per_page;


            // get user import - flag;
            $imported_option = $this->user_model->get_user_imported_option($username);
            $data['imported_user'] = $imported_option;


            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li><a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['first_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['first_tag_close'] = '</li>';



            $this->pagination->initialize($config);
            $data['pages'] = $this->pagination->create_links();

            $order_data = array();

            if (!empty($orders)) {

                foreach ($orders as $order) {

                    $product_id = $order['product'];
                    $status = $order['status'];
                    $date = $order['date'];
                    $product_data = $this->product_model->get_product_data($product_id);

                    if (isset($product_data['parent'])) {
                        $parent = $product_data['parent'];

                        //$realm = $this->product_model->get_product_realm($product_id);
                        $realm = $this->order_model->get_order_realm($order['id'], $product_data['class']);

                        $acc_username = $order['account_username'] . '@' . $realm;
                        $acc_password = $order['account_password'];




                        // if port active and order status 'active' -> show button ['port_available']
                        // if all other validations are fine -> enable button ['port_enabled']

                        $port_service_info = $this->product_model->get_classes_data($product_data['port_service_id']);
                    }

                    if (!isset($product_data['port_active']))
                        $product_data['port_active'] = null;

                    $port_access = $this->port_model->client_port_validation(
                        $order,
                        $product_data,
                        // realm check will be ignored
                        array('realm' => $realm),
                        array($port_service_info),
                        0
                    );

                    // get current TopUp if exist
                    $current_month = date('m');
                    $current_year = date('Y');
                    $topup_info = $this->product_model->get_last_order_topup_for_current_month($order['id'], $user_id, $current_month, $current_year);

                    $order_data[] = array(

                        'status' => $status,
                        'date' => $date,
                        'product_data' => $product_data,
                        'product_id' => $product_id,
                        'id' => $order['id'],
                        'acc_username' => $acc_username,
                        'acc_password' => $acc_password,

                        'topup_info' => $topup_info,

                        'service_type'  => $order['service_type'],
                        'fibre'         => isset($order['fibre']) ? $order['fibre'] : null,

                        'port_available' => $port_access['port_available'],
                        'port_enabled'   => $port_access['port_enabled'],
                        'port_message'   => $port_access['message'],

                    );

                    unset($topup_info);
                }
            }

            $this->asignSidebarData($data);

            $data['username'] = $username;
            $data['title'] = 'Services';
            $data['orders'] = $order_data;
            $data['topups'] = $topup_orders;
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['main_content'] = 'user/view_orders';
            $this->load->view('user/includes/template', $data);
        }



        function search_order($start = 0)
        {

            $username = $this->site_data['username'];
            $this->user_model->update_cancellations($username);
            $num_per_page = NUM_PER_PAGE;
            $order_status = $this->session->userdata('order_status');

            $post_user_name = strip_tags(mysql_real_escape_string($_POST['user_name']));
            $post_user_name = trim($post_user_name);

            if (!empty($post_user_name)) {

                $acc_user = $post_user_name;
                $this->session->set_userdata("acc_user", $acc_user);
            } else {
                $acc_user = $this->session->userdata('acc_user');
            }

            if ($acc_user) {
                if ($order_status == 'active') {

                    $this->session->set_userdata(array("order_status" => 'active'));
                    $orders = $this->user_model->search_active_order($username, $acc_user, $num_per_page, $start);
                    if ($orders) {

                        $total_num = $this->user_model->search_active_count($username, $acc_user);
                    } else {

                        $orders = '';
                        $total_num = 0;
                    }

                    $data['title'] = 'Active Services';
                } elseif ($order_status == 'inactive') {

                    $this->session->set_userdata(array("order_status" => 'inactive'));
                    $orders = $this->user_model->search_inactive_order($username, $acc_user, $num_per_page, $start);

                    if ($orders) {

                        $total_num = $this->user_model->search_inactive_count($username, $acc_user);
                    } else {

                        $orders = '';
                        $total_num = 0;
                    }
                    $data['title'] = 'Inactive Services';
                } else {

                    $this->session->set_userdata(array("order_status" => 'all'));
                    $orders = $this->user_model->search_orders($username, $acc_user, $num_per_page, $start);

                    if ($orders) {

                        $total_num = $this->user_model->search_orders_count($username, $acc_user);
                    } else {

                        $orders = '';
                        $total_num = 0;
                    }

                    $data['title'] = 'Services';
                }

                $data['search_name'] = $acc_user;
            }

            // post null list all the services

            /* else{

			if($order_status == 'active'){

				$orders = $this->user_model->get_active_orders($username, $num_per_page, $start);

				$total_num = $this->user_model->get_active_orders_count($username);

				$data['title'] = 'Active Services';

			}elseif($order_status == 'inactive'){

				$orders = $this->user_model->get_inactive_orders($username, $num_per_page, $start);

				$total_num = $this->user_model->get_inactive_orders_count($username);

				$data['title'] = 'Inactive Services';

			}else{

				$orders = $this->user_model->get_orders($username, $num_per_page, $start);

				$total_num = $this->user_model->get_orders_count($username);

				$data['title'] = 'Services';

			}

		} */



            $this->load->library('pagination');
            $config['base_url'] = base_url('index.php/user/search_order');
            $config['total_rows'] = $total_num;
            $config['per_page'] = $num_per_page;
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li><a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['first_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['first_tag_close'] = '</li>';

            $this->pagination->initialize($config);
            $data['pages'] = $this->pagination->create_links();

            $order_data = array();
            if (!empty($orders)) {

                foreach ($orders as $order) {

                    $product_id = $order['product'];
                    $status = $order['status'];
                    $date = $order['date'];
                    $product_data = $this->product_model->get_product_data($product_id);
                    $realm = $this->product_model->get_product_realm($product_id);

                    $order_data[] = array(
                        'status' => $status,
                        'date' => $date,
                        'product_data' => $product_data,
                        'product_id' => $product_id,
                        'id' => $order['id'],

                        'acc_username' => $order['account_username'] . '@' . $realm,
                        'acc_password' => $order['account_password'],
                    );
                }
            }

            $data['orders'] = $order_data;
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/view_orders';
            $this->load->view('user/includes/template', $data);
        }

        function update_order()
        {


            $order_id = $this->input->post('id', TRUE);
            $order_id = strip_tags(mysql_real_escape_string($order_id));
            $order_id = trim($order_id);

            $username = $this->site_data['username'];


            if (isset($order_id) && trim($order_id) != '') {

                $orders = $this->product_model->get_order_data($order_id);
                if ($orders['user'] != $username) {

                    redirect("user/orders");
                    return;
                }


                $username = $orders['account_username'];
                if (isset($_POST['account_password']) && trim($_POST['account_password']) != '') {
                    //	$password = $_POST['account_password'];

                    $password = $this->input->post('account_password', TRUE);
                    $password = strip_tags(mysql_real_escape_string($password));
                    $password = trim($password);
                    $order_data['account_password'] = $password;
                }

                $order_data['account_username'] = $username;
                $this->product_model->update_order($order_id, $order_data);

                $msg = 'The new Service Password has been saved successfully!';
                $this->session->set_flashdata('success_message', $msg);
                redirect("user/orders");
            }
        }
        public function mtn_fixed_lte_coverage_map()
        {
            $username = $this->site_data['username'];

            $data['main_content'] = 'user/mtn_fixed_lte_coverage_map';
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;

            $this->asignSidebarData($data);
            $this->load->view('user/includes/template', $data);
        }

        public function lte_coverage_map()
        {
            $username = $this->site_data['username'];

            $data['main_content'] = 'user/lte_coverage_map';
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;

            $this->asignSidebarData($data);
            $this->load->view('user/includes/template', $data);
        }
        public function fibre_coverage_map()
        {
            $username = $this->site_data['username'];

            $data['main_content'] = 'user/fibre_coverage_map';
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;

            $this->asignSidebarData($data);
            $this->load->view('user/includes/template', $data);
        }
        function activity_log($start = 0)
        {

            $num_per_page = NUM_PER_PAGE;
            $username = $this->site_data['username'];
            $activity = $this->membership_model->get_activity_log($username, $num_per_page, $start);

            $num_activities = $this->membership_model->get_activity_count($username);
            $data['num_activities'] = $num_activities;
            $this->load->library('pagination');

            $config['base_url'] = base_url('index.php/user/activity_log');
            $config['total_rows'] = $num_activities;
            $config['per_page'] = $num_per_page;
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li><a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['first_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['first_tag_close'] = '</li>';

            $act_count = $start + count($activity);
            $data['showing'] = "Showing $start-$act_count of $num_activities";
            $data['start'] = $start;
            $this->pagination->initialize($config);
            $data['pages'] = $this->pagination->create_links();

            $data['activity'] = $activity;
            $data['main_content'] = 'user/activity_log';
            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $this->asignSidebarData($data);
            $this->load->view('user/includes/template', $data);
        }



        function update_password()
        {
            $this->load->model('crypto_model');

            $new_pass = $_POST['newPass'];
            $new_pass = strip_tags(mysql_real_escape_string($new_pass));
            $new_pass = trim($new_pass);

            $pass_conf = $_POST['passConf'];
            $pass_conf = strip_tags(mysql_real_escape_string($pass_conf));
            $pass_conf = trim($pass_conf);

            if (isset($new_pass) && ($pass_conf != '')) {

                if ($new_pass == $pass_conf) {

                    $username = $this->site_data['username'];
                    $new_pass = $this->crypto_model->encode($new_pass);

                    if (trim($username) != '') {

                        $this->db->where('username', $username);
                        $this->db->update('membership', array('password' => $new_pass));
                        $msg = 'Your new password has been saved.';
                        $this->session->set_flashdata('success_message', $msg);

                        // active log
                        $user_id = $this->membership_model->get_user_id($username);
                        $this->membership_model->add_activity_log($user_id, "change_user_pass");

                        $this->settings('tab5', $msg);
                        //redirect("user/change_password");
                    }
                } else {

                    $msg = 'Your new password does not match its confirmation. Please try again.';
                    $this->session->set_flashdata('error_message', $msg);

                    $this->settings('tab5', $msg);
                    //redirect("user/change_password");
                }
            } else {
                $msg = 'Please use a longer password.';
                $this->session->set_flashdata('error_message', $msg);

                $this->settings('tab5', $msg);
                //redirect("user/change_password");
            }
        }



        function change_password()

        {

            $suc_msg = $this->session->flashdata('success_message');
            $error_msg = $this->session->flashdata('error_message');
            $data['success_message'] = $suc_msg;
            $data['error_message'] = $error_msg;
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/account/change_password';
            $this->load->view('user/includes/template', $data);
        }



        function settings($page = null, $message = null)

        {
            $this->load->model("user_docs_model");

            $msg =  $this->session->flashdata('succ_message');

            $username = $this->site_data['username'];

            $user_id = $this->membership_model->get_user_id($username);

            $email = $this->membership_model->get_user_email($user_id);

            $mobile = $this->membership_model->get_user_mobile($username);

            $data['suc_msg'] = $msg;

            $this->asignSidebarData($data);

            $data['mobile'] = $mobile;

            $data['email'] = $email;

            $data['user_id'] = $user_id;

            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;

            //Personal Data Page
            $user_fields = $this->user_docs_model->get_address_data($user_id);
            $residence   =  $this->user_docs_model->get_file_full_data($user_id, 'residence');
            $passport    =  $this->user_docs_model->get_file_full_data($user_id, 'passport');

            $data['user_fields']      = '';
            $data['residence']        = false;
            $data['passport']         = false;
            $data['residence_data']   = false;
            $data['passport_data']    = false;

            if (!empty($user_fields)) {
                $user_fields['physical_delivery_address'] = $user_fields['delivery_address'];
                $data['user_fields'] = $user_fields;
            }

            if (!empty($residence)) {

                $data['residence'] = true;
                $data['residence_data']['width'] = $residence['width'];
                $data['residence_data']['height'] = $residence['height'];
            }


            if (!empty($passport)) {
                $data['passport'] = true;
                $data['passport_data']['width'] = $passport['width'];
                $data['passport_data']['height'] = $passport['height'];
            }

            $data['from_mobile_data_request'] = $this->session->flashdata('from_mobile_data_request');

            //Avios Page

            $data['user_data'] = $this->user_model->get_user_data_by_id($user_id);

            $data['avios_settings_page'] = $this->load->view('user/avios_settings', $data, true);

            $data['mobile_data'] = $this->load->view('user/account/mobile_data', $data, true);

            $data['billing'] = $this->load->view('user/account/billing', $data, true);

            $data['change_password_page'] = $this->load->view('user/account/change_password', $data, true);

            $data['current_page'] = $page;
            $data['tab_show'] = 'show' . $page;
            $data['message'] = $message;

            $data['aditional_scripts'] = [
                'assets/plugins/ios-switch/ios7-switch.js',
                'assets/js/form_elements.js',
                'assets/plugins/dropzone/dropzone.min.js'
            ];

            $data['main_content'] = 'user/account/account_settings';

            $this->load->view('user/includes/template', $data);
        }



        function is_logged_in()

        {

            $is_logged_in = $this->session->userdata('is_logged_in');

            if (!isset($is_logged_in) || $is_logged_in != true) {

                /* echo "You don't have permission to access this page. ";

			echo '<a href="../login">Login</a>';

			die(); */

                redirect('login');
            }
        }



        function update_account()
        {
            $account_id = $_POST['account_id'];
            $account_id = strip_tags(mysql_real_escape_string($account_id));
            $account_id = trim($account_id);

            $email = $_POST['email_address'];
            $email = strip_tags(mysql_real_escape_string($email));
            $email = trim($email);

            $mobile = isset($_POST['mobile_number']) ? $_POST['mobile_number'] : '';
            $mobile = strip_tags(mysql_real_escape_string($mobile));
            $mobile = trim($mobile);

            $data = array(

                'email_address'    => $email,
                'mobile_number'    => $mobile,

            );

            $this->db->where('id', $account_id);
            $this->db->update('membership', $data);
            $msg = "Your account information has been saved successfully.";

            // add active_log
            $this->membership_model->add_activity_log($account_id, 'change_acc_settings');
            $this->session->set_flashdata('succ_message', $msg);

            redirect('user/settings');
        }

        function validate_email()
        {
            $account_id = strip_tags(mysql_real_escape_string($_POST['account_id']));
            $account_id = trim($account_id);

            $post_email = strip_tags(mysql_real_escape_string($_POST['email_address']));
            $post_email = trim($post_email);

            if ($account_id != '') {

                // get email from membership
                $user_email = $this->membership_model->get_user_email($account_id);
                // get email from biling
                $billing_email = $this->membership_model->get_billing_email($account_id);

                // if billing name eq to users' email -> ok
                // if billing name eq to olf billing -> ok
                if ($post_email == $user_email) {
                    echo "true";
                } else {
                    // validate by memership
                    $membership_result = $this->membership_model->validate_email($post_email);
                    $billing_result = $this->membership_model->validate_billing_email($post_email);
                    if (!$membership_result) {

                        echo "true";
                    } else {

                        echo "false";
                    }
                }
            }
        }

        function validate_email_new()
        {

            return;
            $account_id = strip_tags(mysql_real_escape_string($_POST['account_id']));
            $account_id = trim($account_id);

            $post_email = strip_tags(mysql_real_escape_string($_POST['email']));
            $post_email = trim($post_email);


            if ($account_id != '') {


                // validate by memership
                $membership_result_id = $this->membership_model->validate_email_back_id($post_email);
                $billing_result_id = $this->membership_model->validate_billing_email($post_email);

                $answer = 'false';

                if (!empty($membership_result_id)) {

                    $answer = 'false';
                    if ($membership_result_id == $account_id) {
                        $answer = 'true';
                    }
                }






                // get email from membership
                $user_email = $this->membership_model->get_user_email($account_id);
                // get email from biling
                $billing_email = $this->membership_model->get_billing_email($account_id);

                // if billing name eq to users' email -> ok
                // if billing name eq to olf billing -> ok
                if ($post_email == $user_email) {
                    echo "true";
                    return;
                }
                // validate by memership
                $membership_result = $this->membership_model->validate_email($post_email);



                $billing_result = $this->membership_model->validate_billing_email($post_email);
                if (!$membership_result) {

                    echo "true";
                } else {

                    echo "false";
                }
            }
        }


        function optout($user_id = null)
        {

            if ($user_id) {

                $email = $this->membership_model->get_user_email($user_id);

                $this->db->where('id', $user_id);

                $this->db->update('membership', array('subscribe' => 0));



                $msg = "You have successfully unsubscribe the mail.";

                $this->load->library('email');

                $this->email->from('admin@openweb.com', 'OpenWeb Home');

                $this->email->to($email);

                $this->email->subject("Unsubscribe to Inform");

                $this->email->message($msg);

                $this->email->send();

                redirect('login');
            }
        }

        function activecloudsl()
        {

            $username = $this->site_data['username'];

            $ow = $this->site_data['ow'];

            $id = $this->user_model->get_user_id($username);

            if (isset($_POST['active'])) {

                if ($_POST['active'] == 'yes')

                    $this->cloudsl_model->active_user_cloudsl($id, $ow);
            }

            $cloudsl = $this->cloudsl_model->get_user_cloudsl($id, $ow);

            if ($cloudsl)

                //echo $cloudsl[0]['id'];

                $data['cloudsl'] = $cloudsl[0];

            else

                $data['cloudsl'] = '';

            $data['main_content'] = 'user/activecloudsl';

            $data['sidebar'] = TRUE;

            $this->load->view('user/includes/template', $data);



            //$cloudsl=$this->cloudsl_model->active_user_cloudsl($username,$ow);

            //var_dump($cloudsl);

            //$data['id'] = $cloudsl['id'];

        }

        function addcredit()
        {

            $username = $this->site_data['username'];

            $ow = $this->site_data['ow'];

            $id = $this->user_model->get_user_id($username);

            if (isset($_POST['credit'])) {

                //echo $_POST['credit'];die;

                //$data=$this->cloudsl_model->get_user_cloudsl($id,$ow);

                $credit = $_POST['credit'];

                $this->cloudsl_model->add_credit($id, $credit);

                redirect('user/addcredit');
            }

            $cloudsl = $this->cloudsl_model->get_user_cloudsl($id, $ow);

            if ($cloudsl)

                //echo $cloudsl[0]['id'];

                $data['cloudsl'] = $cloudsl[0];

            else

                $data['cloudsl'] = '';

            $data['main_content'] = 'user/addcredit';

            $data['sidebar'] = TRUE;

            $this->load->view('user/includes/template', $data);
        }

        function add_account()
        {

            $username = $this->site_data['username'];

            $ow = $this->site_data['ow'];

            $id = $this->user_model->get_user_id($username);

            $credit = $this->cloudsl_model->get_credit_cloudsl($id);

            $user['username'] = $username;

            $user['id'] = $id;

            $user['ow'] = $ow;

            //$account = $this->cloudsl_model->get_account_cloudsl($user);

            //$num_acc=count($account);

            //if($credit==0&&$num_acc>0)

            if (isset($_POST['account'])) {



                $info['account_username'] = $_POST['account_username'];

                $info['account_password'] = $_POST['account_password'];

                $usernum = $this->cloudsl_model->check_user($info['account_username']);

                if ($usernum == 0) {

                    $account = $this->cloudsl_model->get_account_cloudsl($user);

                    $realms = $this->cloudsl_model->get_realm();

                    if ($account) {

                        $product = $this->product_model->get_product_data($account[0]['product']);

                        $num_acc = count($account);
                    } else

                        $num_acc = 0;

                    if ($num_acc == 1 && $account[0]['account_username'] == '' && $product['trial'] == 1) {

                        $number = $this->membership_model->get_number($username);

                        $ac_email = $this->membership_model->get_email($username);

                        $class = $this->product_model->get_is_class($account[0]['id']);

                        $product_name = $this->product_model->get_product_name($account[0]['product']);

                        $comment = $account[0]['account_comment'];

                        $realm_data = $this->product_model->get_is_details($class);
                        // get realm by class
                        //$realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);


                        $realm = $realm_data['realm'];

                        $rl_user = $realm_data['user'];

                        $rl_pass = $realm_data['pass'];

                        $acc_realm_user = $info['account_username'] . '@' . $realm;

                        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

                        $resp = $this->is_classes->add_realm_new($sess, $class, $info['account_username'], $info['account_password'], $comment, $ac_email);

                        if ($resp == 5 || $resp == 8 || $resp == 11) {

                            $error = "There was an error (code: $resp). Please try again.";
                        } else {

                            $this->cloudsl_model->add_account($user, $info);

                            //if sunccessfully payment then send email and invoice , active orders

                            $this->load->model('admin/order_model');

                            $this->order_model->email_activation($ac_email, $product_name, $info['account_username'], $realm,  $info['account_password'], $username);

                            $this->order_model->set_activated($account[0]['id']);



                            //use admin order_model

                            $sms_content = "Your ADSL product has been successfully created. See email for more details. Username: $acc_realm_user Password: $info[account_password] - OpenWeb";

                            //$this->order_model->send_sms($number, $sms_content);

                        }
                    }

                    //$realms = $this->cloudsl_model->get_realm();

                    $realm_info = $realms[2];

                    $realm = $realm_info['realm'];

                    $rl_user = $realm_info['user'] . '@' . $realm;

                    $rl_pass = $realm_info['pass'];

                    $acc_realm_user = $info['account_username'] . '@' . $realm;

                    $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);



                    $respacc = $this->is_classes->getAccountInfo_new($sess, $acc_realm_user);

                    //echo $respacc;die;

                    if ($respacc == 2)

                        $this->cloudsl_model->add_account($user, $info);

                    redirect('user/add_account');
                } else {

                    redirect('user/add_account');
                }
            }

            $account = $this->cloudsl_model->get_account_cloudsl($user);

            $product_name = array();

            if ($account) {



                foreach ($account as $a) {

                    $product_name[] =  $this->product_model->get_product_name($a['product']);

                    $realm = $this->product_model->get_product_realm($a['product']);

                    if ($realm != '' && $a['account_username'] != '')

                        $acc_username[] = $a['account_username'] . '@' . $realm;

                    else

                        $acc_username[] = $a['account_username'];
                }
            }

            $cloudsl = $this->cloudsl_model->get_user_cloudsl($id, $ow);

            $data['acc_username'] = $acc_username;

            $data['product_name'] = $product_name;

            $data['credit'] = $credit;

            $data['cloudsl'] = $cloudsl;

            $data['account'] = $account;

            $data['main_content'] = 'user/add_account';

            $data['sidebar'] = TRUE;

            $this->load->view('user/includes/template', $data);
        }

        function list_order_cloudsl($account_id = '', $start = 0)
        {

            $num_per_page = NUM_PER_PAGE;

            $username = $this->site_data['username'];

            $ow = $this->site_data['ow'];

            $id = $this->user_model->get_user_id($username);

            $credit = $this->cloudsl_model->get_credit_cloudsl($id);

            $cloudsl = $this->cloudsl_model->get_user_cloudsl($id, $ow);

            $list = $this->cloudsl_model->get_service_cloudsl($num_per_page, $start);

            $orders = $this->cloudsl_model->get_orders_cloudsl($username);



            $this->load->library('pagination');

            if ($account_id == '')

                $config['base_url'] = base_url('index.php/user/list_order_cloudsl');

            else

                $config['base_url'] = base_url('index.php/user/list_order_cloudsl/') . $account_id;

            $config['total_rows'] = $this->cloudsl_model->get_service_cloudsl_num();

            $config['per_page'] = $num_per_page;

            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li>';

            $config['prev_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['first_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['first_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data['pages'] = $this->pagination->create_links();

            /* echo $orders[0]['date'];

		echo date('H:i:s',strtotime($orders[0]['date']));

		die; */

            $data['product_order'] = array();

            if (!$orders)

                $data['product_order'][] = 0;

            else {

                foreach ($list as $a => $b) {

                    foreach ($orders as $c => $d) {

                        if ($list[$a]['id'] == $orders[$c]['product']) {

                            $data['product_order'][] = $list[$a]['id'];

                            $data['order_id'][] = $orders[$c]['id'];
                        }
                    }
                }
            }

            if ($account_id)

                $data['account_id'] = $account_id;

            else

                $data['account_id'] = '';

            $data['order'] = $orders;

            $data['credit'] = $credit;

            $data['list'] = $list;

            $data['cloudsl'] = $cloudsl;

            $data['main_content'] = 'user/services_cloudsl';

            $data['sidebar'] = TRUE;

            $this->load->view('user/includes/template', $data);
        }

        function order_cloudsl($id_product, $account_id = '')
        {

            //echo $id;

            $username = $this->site_data['username'];

            //$ow=$this->site_data['ow'];

            $id_user = $this->user_model->get_user_id($username);

            $credit = $this->cloudsl_model->get_credit_cloudsl($id_user);

            $price = $this->product_model->get_product_price($id_product);

            if ($credit < $price) {

                redirect('user/addcredit');
            }

            if ($account_id != '') {

                $id = $account_id;

                $status = $this->cloudsl_model->updata_account_cloudsl($id_product, $id);



                if ($status == 'active') {

                    $this->cloudsl_model->update_credit_cloudsl($id_product, $id_user);

                    $this->active_service_cloudsl($id);
                }

                redirect('user/add_account');
            }



            if (isset($_POST['id']) && $_POST['id']) {

                $id = $_POST['id'];



                $status = $this->cloudsl_model->updata_account_cloudsl($id_product, $id);

                if ($status == 'active') {

                    $this->cloudsl_model->update_credit_cloudsl($id_product, $id_user);

                    $this->active_service_cloudsl($id);
                }

                redirect('user/add_account');
            } else {

                //$username=$this->site_data['username'];

                $ow = $this->site_data['ow'];

                $id = $this->user_model->get_user_id($username);

                $user['username'] = $username;

                $user['id'] = $id;

                $user['ow'] = $ow;

                $account = $this->cloudsl_model->get_another_account_cloudsl($user);

                //$cloudsl = $this->cloudsl_model->get_user_cloudsl($id,$ow);

                //$data['cloudsl'] = $cloudsl;

                $data['id_product'] = $id_product;

                $data['account'] = $account;

                $data['main_content'] = 'user/order_cloudsl';

                $data['sidebar'] = TRUE;

                $this->load->view('user/includes/template', $data);
            }
        }

        function edit_active_cloudsl($order_id = '')
        {

            if (isset($_POST['order_id']) && $_POST['order_id'] != '') {

                $order_id = $_POST['order_id'];
            }

            $this->load->model('admin/order_model');

            $user = $this->site_data['username'];

            //$order_id = $_POST['order_id'];

            //$this->session->set_userdata(array("order_id" => $order_id));

            $order_data =  $this->cloudsl_model->get_orders_cloudsl('', $order_id); //current order data

            $this->session->set_userdata(array("order_id" => $order_id));

            //var_dump($order_data);

            if ($order_data && $user == $order_data[0]['user']) {

                $product_id = $order_data[0]['product'];

                $product_data = $this->product_model->get_product_data($product_id); //current product data

                //var_dump($product_data);die;

                if ($product_data) {

                    $data['product_data'] = $product_data;
                }



                $class = $product_data['class'];

                $curr_class = $this->product_model->get_classes($class); //get current class data

                if ($curr_class) {

                    $data['class'] = $curr_class[0];
                }
            } else {

                $data['product_data'] = '';

                $product_id = 0;
            }

            $products_data = $this->cloudsl_model->get_another_service_cloudsl($product_id); //list all product data

            $data['products'] = $products_data;

            $data['sidebar'] = TRUE;

            $data['main_content'] = 'user/edit_service_cloudsl';

            $this->load->view('user/includes/template', $data);
        }


        function edit_service_cloudsl($order_id)

        {

            $this->load->model('admin/order_model');



            $order_data = $this->order_model->get_order_data($order_id);

            $product_id = $order_data['product'];

            $order_status = $order_data['status'];

            $product_data = $this->product_model->get_product_data($product_id); //echo "<pre>";print_r($product_data);die;

            $product_name = $product_data['name'];  // product_name === service_name

            $change_flage = $order_data['change_flag'];

            $acc_pwd = $order_data['account_password'];

            $dispaly_usage = $order_data['display_usage'];

            //$order_type = $order_data['type'];

            $parent = $product_data['parent'];



            $this->db->select('id, modify_service, product');

            $this->db->where('account_username', $order_data['account_username']);

            $this->db->where('account_password', $acc_pwd);

            $this->db->where('status', 'pending');

            $query = $this->db->get('orders');

            $result = $query->result_array();

            if ($result) {

                $modify_service = $result[0]['modify_service'];

                $modify_product_id = $result[0]['product'];

                $modify_product_name = $this->product_model->get_product_name($modify_product_id);

                $data['modify_service'] = $modify_service;

                $data['modfiy_product'] = $modify_product_name;

                //var_dump($modify_product_name);die;

            }



            $class = $this->order_model->get_is_class($order_id);

            //$realm_data = $this->order_model->get_is_details($class);
            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);


            $rl_user = $realm_data['user'];

            $rl_pass = $realm_data['pass'];

            $lm = explode('@', $realm_data['user']);

            $realm = $lm[1];

            $acc_username = trim($order_data['account_username']);

            $new_user = $acc_username . '@' . $realm;



            //if($order_type == 'manual'){

            //	$data['main_content'] = 'user/warning_page';

            //	$data['sidebar'] = TRUE;

            //	$this->load->view('user/includes/template', $data);

            //}else{

            //get data from api

            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session id



            if ($sess == '2') {

                $msg = 'Failed to connect the ISDSL, the account username does not exist.';

                $data['error_message'] = $msg;
            } elseif ($sess == '3') {

                $msg = 'Failed to connect the ISDSL, the account username does not exist.';

                $data['error_message'] = $msg;
            } elseif ($sess == '4') {

                $msg = 'Remote IP not allowed.';

                $data['error_message'] = $msg;
            } else {

                //get session info from api

                $current_data = $this->is_classes->get_current_session_info_new($sess, $new_user);

                if ($current_data == '2') {

                    $data['session_error_message'] = 'Username does not exist or does not have access to the web service';
                } elseif ($current_data == '12') {

                    $data['session_error_message'] = 'Api user does not have required permissions';
                } else {

                    //$current_data = json_decode($current_data, true);

                    if (!empty($current_data)) {

                        $session_info = $current_data[0];

                        $data['session_data'] = $session_info;
                    }
                }



                //get yearly info from api

                $year_data = $this->is_classes->get_yearly_stats_new($sess, $new_user);

                if ($year_data == '2') {

                    $data['year_error_message'] = 'Username does not exist, make sure that the account username have been added to the system.';
                } elseif ($year_data == '5') {

                    $data['year_error_message'] = 'Invalid session identifier supplied';
                } else {

                    //$year_data = json_decode($year_data, true);

                    if (!empty($year_data)) {

                        $data['year_stats_data'] = $year_data;
                    }
                }



                //get monthly info from api

                $year =  date('Y');

                $month = date('m');

                $day = date('d');

                $month_data = $this->is_classes->get_monthly_stats_new($sess, $new_user, $year, $month);

                if ($month_data == '2') {

                    $data['month_error_message'] = 'Username does not exist, make sure that the account username have been added to the system.';
                } elseif ($month_data == '5') {

                    $data['month_error_message'] = 'Invalid session identifier supplied';
                } elseif ($month_data == '14') {

                    $data['month_error_message'] = 'Requesting usage statistics for more than 6 months back';
                } else {

                    $month_data = json_decode($month_data, true);

                    if (!empty($month_data)) {

                        $data['month_stats_data'] = $month_data;
                    }
                }



                //get today info from api

                $today_data = $this->is_classes->get_daily_stats_new($sess, $new_user, $year, $month, $day);

                if ($today_data == 2) {

                    $data['today_error_message'] = 'Username does not exist, make sure that the account username have been added to the system.';
                } elseif ($today_data == '5') {

                    $data['today_error_message'] = 'Invalid session identifier supplied';
                } elseif ($today_data == '14') {

                    $data['today_error_message'] = 'Requesting usage statistics for more than 6 months back';
                } else {

                    $today_data = json_decode($today_data, true);

                    if (!empty($today_data)) {

                        $data['today_stats_data'] = $today_data;
                    }
                }



                $data['change_flag'] = $change_flage;

                $data['display_usage'] = $dispaly_usage;

                $data['acc_pwd'] = $acc_pwd;

                $data['order_status'] = $order_status;

                $data['current_service'] = $product_name;

                $data['order_id'] = $order_id;

                $data['order_key'] = $this->user_model->order_key();
            }

            //}

            $data['sidebar'] = TRUE;

            $data['main_content'] = 'user/order_detail_cloudsl';

            $this->load->view('user/includes/template', $data);
        }

        function active_service_cloudsl($order_id)
        {

            die;

            $order = $this->cloudsl_model->get_orders_cloudsl('', $order_id);

            $username = $this->site_data['username'];

            $ow = $this->site_data['ow'];

            $id = $this->user_model->get_user_id($username);

            $credit = $this->cloudsl_model->get_credit_cloudsl($id);

            /* $user['username'] = $username;

		$user['id'] = $id;

		$user['ow'] = $ow; */

            $number = $this->membership_model->get_number($username);

            $ac_email = $this->membership_model->get_email($username);

            $class = $this->product_model->get_is_class($order_id);

            $product_name = $this->product_model->get_product_name($order[0]['product']);

            $comment = $order[0]['account_comment'];

            //$realm_data = $this->product_model->get_is_details($class);
            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);


            $realm = $realm_data['realm'];

            $rl_user = $realm_data['user'];

            $rl_pass = $realm_data['pass'];

            $acc_realm_user = $order[0]['account_username'] . '@' . $realm;

            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

            //echo $sess;

            $resp = $this->is_classes->add_realm_new($sess, $class, $order[0]['account_username'], $order[0]['account_password'], $comment, $ac_email);

            //echo $resp;die;

            if ($resp == 11) {

                $this->is_classes->restore_account_new($sess, $acc_realm_user);

                $this->is_classes->set_account_class_new($sess, $acc_realm_user, $class);
            }

            if ($resp == 5 || $resp == 8) {

                $error = "There was an error (code: $resp). Please try again.";
            } else {

                //if sunccessfully payment then send email and invoice , active orders

                $this->load->model('admin/order_model');

                $this->order_model->email_activation($ac_email, $product_name, $order[0]['account_username'], $realm,  $order[0]['account_password'], $username);

                $this->order_model->set_activated_cloudsl($order[0]);

                $password = $order[0]['account_password'];

                //use admin order_model

                $sms_content = "Your ADSL product has been successfully created. See email for more details. Username: $acc_realm_user Password: $password - OpenWeb";

                //var_dump($sms_content);die;

                $this->order_model->send_sms($number, $sms_content);
            }

            //$this->cloudsl_model->update_credit_cloudsl($order[0]['product'],$order[0]['id_user']);

            redirect('user/add_account');
        }

        function check_local_username()
        {

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

                // get order by user and realm
                $answer = $this->order_model->check_order_by_username_realm($acc_username, $acc_realm);
            }
            echo json_encode($answer);
        }



        function check_username()
        {

            // $acc_username, $realm
            $answer = false;

            if ($this->input->is_ajax_request()) {
                $answer = false;


                //      acc_username : acc_username,
                //      acc_realm : acc_realm


                //   $ajax_params = (array)json_decode($this->input->post('params'));
                //    $ajax_order = (array)json_decode($this->input->post('order_params'));

                $acc_username = $this->input->post('acc_username');
                $acc_realm = $this->input->post('acc_realm');

                $realm_data = $this->realm_model->get_realm_data_by_name($acc_realm);

                $rl_user = $realm_data['user'];
                $rl_pass = $realm_data['pass'];

                $realm = $acc_realm;
                $sess = 0;
                $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

                $account_info = $this->is_classes->getAccountInfo_full_new($sess, $acc_username . "@" . $realm);
                $code =  $account_info['intReturnCode'];

                if ($code == '2')
                    $answer = true;
            }

            echo json_encode($answer);
        }


        // Order TopUP (info about TopUP)
        function order_topup($order_id = null)
        {

            // Check order_id
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            if (empty($order_id))
                redirect('user/orders');


            // check if this order-id  belongs to this user
            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);
            $this->load->model('admin/order_model');
            $order_data = $this->order_model->get_order_data($order_id);

            // If not belongs - > redirect
            if ($order_data['user'] != $username) {

                redirect("user/orders");
                return;
            }
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            if ($username != 'test-vvv') {

                redirect("user/orders");
                return;
            }
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            // ---------------------------------------------------


            // Check product id
            $product_id = $order_data['product'];
            if (empty($product_id)) {

                redirect("user/orders");
                return;
            }
            $product_name = $this->product_model->get_product_name($product_id);
            $current_month = date('m');
            $current_year = date('Y');

            // check TopUP enable  [topup_config] , [topup_current_level]
            $topup_enable_row = $this->product_model->check_topup_by_order($order_id, $product_id, $user_id);
            if (!$topup_enable_row['topup_config']) {

                redirect("user/orders");
                return;
            }

            // ===================================================================================================

            // get TopUp level for user
            $topup_level_row = $this->product_model->get_user_topup_level($user_id, $order_id, $current_month, $current_year);

            // get all TopUp configurations for this product
            $topup_config_array = $this->product_model->get_info_for_order_topup($product_id);

            if (empty($topup_config_array)) {

                redirect("user/orders");
                return;
            }


            // TopUp iteration
            $iteration = $topup_level_row + 1;  // START FROM 1
            if (!empty($topup_config_array[$iteration])) {
                $topup_for_viewer = $topup_config_array[$iteration];
            } else {
                redirect("user/orders");
                return;
            }

            $topup_for_viewer['iteration'] = $iteration;
            // ------------------------------------------------------------------------------------
            // -- payfast integration -------------------------------------------------------------
            // ------------------------------------------------------------------------------------
            // ------------------------------------------------------------------------------------

            $payment_info['item_name'] = $topup_for_viewer['topup_name'];
            $payment_info['item_description'] = $this->payfast_model->topup_item_description;
            $payment_info['discount'] = '0';
            $payment_info['price'] = $topup_for_viewer['topup_price'];
            $payment_info['pro_price'] = $topup_for_viewer['topup_price'];


            $data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info);
            $sandbox_data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info, "SANDBOX");


            $pre_live_signature_for_payfast = $this->payfast_model->pre_signature($data_for_payfast);
            $pre_sandbox_signature_for_payfast = $this->payfast_model->pre_signature($sandbox_data_for_payfast, 'SANDBOX');


            $order_data_payfast = array(

                'adsl_username'     => $order_data['account_username'] . "@" . $order_data['realm'],
                'product_id'        => $product_id,
                'order_id'          => $order_id,
                'topup_config_id'   => $topup_for_viewer['topup_id'],
                'topup_name'        => $topup_for_viewer['topup_name'],
                'payment_type'      => 'credit_card',
                'topup_level'       => $iteration,

            );


            $data['order_data_array'] = $order_data_payfast;
            $order_signature = $this->payfast_model->generate_topup_order_signature($order_data_payfast);

            // var_dump($order_signature);
            $data['order_signature'] = $order_signature;

            //  var_dump($pre_live_signature_for_payfast);
            // var_dump($pre_sandbox_signature_for_payfast);

            $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
            $data['live_payfast_host']   = $this->payfast_model->live_host;
            $data['payfast_data'] = $data_for_payfast;
            $data['sandbox_payfast_data'] = $sandbox_data_for_payfast;

            $data['pre_sandbox'] = $pre_sandbox_signature_for_payfast;
            $data['pre_live']    = $pre_live_signature_for_payfast;


            // ------------------------------------------------------------------------------------
            // ------------------------------------------------------------------------------------
            // ------------------------------------------------------------------------------------

            $data['account_username'] = $order_data['account_username'];
            $data['current_product_description'] = $product_name;
            $data['realm'] = $order_data['realm'];
            $data['order_id'] = $order_id;
            $data['username'] = $username;
            $data['topup_conf'] = $topup_for_viewer;
            $data['assigned_topups'] = ' ';
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/topup_checkout';
            $this->load->view('user/includes/template', $data);
            //echo "<pre>";var_dump($data['method']);die;


        }



        // TopUP order process
        function order_topup_process()
        {


            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);

            // ------------------- get requests -------------------------------------------------
            $account_username    = $this->product_model->process_product_request('acc_username');
            $account_realm       = $this->product_model->process_product_request('acc_realm');
            $adsl_username       = $account_username . "@" . $account_realm;
            $price               = $this->product_model->process_product_request('price');
            $payment_method      = $this->product_model->process_product_request('payment_method');

            // ------------------- bank data ----------------------------------------------------
            $name_on_card        = $this->product_model->process_product_request('name_on_card');
            $card_number         = $this->product_model->process_product_request('card_num');
            $expires_month       = $this->product_model->process_product_request('expires_month');
            $expires_year        = $this->product_model->process_product_request('expires_year');
            $cvc                 = $this->product_model->process_product_request('cvc');
            $bank_name           = $this->product_model->process_product_request('bank_name');
            $bank_account_number = $this->product_model->process_product_request('bank_account_number');
            $bank_account_type   = $this->product_model->process_product_request('bank_account_type');
            $bank_branch_code    = $this->product_model->process_product_request('bank_branch_code');

            $topup_id            = $this->product_model->process_product_request('topup_id');
            $iteration           = $this->product_model->process_product_request('iteration');
            $order_id            = $this->product_model->process_product_request('order_id');

            // -------------------- get order data ------------------------------------------------
            $order_data = $this->order_model->get_order_data($order_id);

            // -------------------- user check ----------------------------------------------------
            if ($order_data['user'] != $username) {

                redirect("user/orders");
                return;
            }
            // -------------------- product check -------------------------------------------------
            $product_id = $order_data['product'];
            if (empty($product_id)) {

                redirect("user/orders");
                return;
            }
            // --------------------- product info -------------------------------------------------
            $product_data = $this->product_model->get_product_data($product_id);

            // ------------------------------------------------------------------------------------
            $order_time = date('Y-m-d H:i:s');
            $topup_info = $this->product_model->topup_get_config($topup_id);
            $price          = $topup_info['topup_price'];
            $price          = number_format(round($price, 2), 2);
            $payment_status = 'in process';

            $topup_order_data = array(

                'order_time'        => $order_time,
                'user_id'           => $user_id,
                'username'          => $username,
                'topup_config_id'   => $topup_id,
                'order_id'          => $order_id,
                'product_id'        => $product_id,
                'payment_method'    => $payment_method,
                'payment_status'    => $payment_status,
                'price'             => $price,
                'topup_level'       => $iteration,
                'adsl_username'     => $adsl_username,

            );

            $inserted_id = $this->product_model->insert_topup_order($topup_order_data);
            // -------------------------------------------------------------------------------------------------
            // --------------------------------- ISDSL TopUp + (update billing) ---------------------------------

            $isdl_assign_result = 0;
            if (!empty($inserted_id) && ($payment_method != 'credit_card')) {

                // ---------------------------- insert new billing data ---------------------------------------
                $billing_data = array(
                    'name_on_card' => $name_on_card,
                    'card_num' => $card_number,
                    'cvc' => $cvc,
                    'expires_month' => $expires_month,
                    'expires_year' => $expires_year,
                    'bank_name' => $bank_name,
                    'bank_account_number' => $bank_account_number,
                    'bank_account_type' => $bank_account_type,
                    'bank_branch_code' => $bank_branch_code,
                );
                $update_result = $this->db->update('billing', $billing_data, array('username' => $username));

                // ---------------------------------  check schedule flag --------------------------------------------

                $current_month = date('m');
                $current_year = date('Y');
                $schedule_flag =  $this->product_model->check_schedule_topup($adsl_username, $current_year,  $current_month, $user_id, $order_id);

                //  --------------- get current class id / name by order id
                $current_class_id    = $product_data['class_id'];
                $current_class_name = $product_data['class'];

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

                // -------------------------------------- API Session -------------------------------------------
                $isdl_create_result = false;

                $realm_data = $this->realm_model->get_realm_data_by_name($account_realm);

                $rl_user = $realm_data['user'];
                $rl_pass = $realm_data['pass'];
                $realm = $account_realm;
                $sess = 0;

                // session, schedule, change
                $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id

                // -------------------------------------- Get Current API class

                // ----------- get current user class
                $real_account_info = $this->is_classes->getAccountInfo_full_new($sess, $adsl_username);
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

                //      // Check Once-off !!!!
                $once_off_flag = false;
                if (strtolower($order_data['billing_cycle']) == 'once-off')
                    $once_off_flag = true;

                if (!$schedule_flag) {

                    $schedule_current_class_answer = 0;
                    if (!$once_off_flag)
                        $schedule_current_class_answer = $this->is_classes->set_pending_update_new($sess, $adsl_username, $current_class_name);
                    $already_scheduled = 0;
                } else {
                    $schedule_current_class_answer = 1;
                    $already_scheduled      = $schedule_flag; // order-id where class was scheduled

                }
                $assign_new_class_answer =  $this->is_classes->set_account_class_new($sess, $adsl_username, $topup_class_name);
                // ---------------------- update order block ------------------------------------------

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
                $api_update_result =  $this->product_model->update_topup_order($inserted_id, $api_data_for_update);
                if ($assign_new_class_answer == '')
                    $assign_new_class_answer = '1';


                // ------------------------------------------------------------------------------------
                if ($assign_new_class_answer == '1') {

                    //   SMS - YES
                    /* $sms_content = "Your ADSL product has been successfully created. See email for more details. Username: " . $acc_realm_user . " Password: " . $data['acc_password'] . " - OpenWeb";
                    if (!empty($number))
                        $this->order_model->send_sms($number, $sms_content); //#B
                    */


                    //$pdf_id = $this->getPDF($invoice_id, $user, $product_name, $price);
                    // $this->order_model->email_invoices_individual($user, $pdf_id);
                    //$this->order_model->email_ceo_product($user, $product_name, $payment_method);

                    // ----------------------------------- Invoices & Emails ------------------------------


                    // Save Invoice
                    $invoice_id = $this->user_model->save_topup_invoice($inserted_id, $username);

                    // Generate PDF
                    $pdf_id = $this->get_topup_PDF($invoice_id, $username, $topup_info['topup_name'], $price);

                    // Send letter to client
                    $this->user_model->email_topup_with_invoice_individual($username, $invoice_id);

                    // Send letter to CEO
                    $base_url = base_url();
                    $client_full_name = $this->membership_model->get_user_name_nice($username);
                    $this->user_model->topup_email_to_admin($username, $client_full_name, $topup_order_data, $inserted_id, $base_url);



                    // ---------------------------------------------------------------------------------------

                    $this->session->set_flashdata('success_message', " Thank you for loading a Top Up to your account.  Your Topup has been successful and will take up to 30 minutes to become active.");
                } else {
                    $this->session->set_flashdata('fail_message', "Something went wrong");
                }


                $this->session->set_flashdata('topup_id', $topup_id);
                $this->session->set_flashdata('payment_method', $payment_method);
            }
            redirect('user/topup_congratulations');
        }


        // Congratulation/Final page after TopUp order
        function topup_congratulations()
        {


            $data['fail_message']    =  $this->session->flashdata('fail_message');
            $data['success_message'] =  $this->session->flashdata('success_message');

            $data['topup_id']        =  $this->session->flashdata('topup_id');
            $data['payment_method']  =  $this->session->flashdata('payment_method');



            /*
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
            /*
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
*/
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/topup_congratulation';

            $this->load->view('user/includes/template', $data);
        }

        function payfast_failed()
        {

            //var_dump($_REQUEST);
            // $data['message'] = 'fail';
            //  $data['product_name'] = $product_name;
            //  $data['auto'] = $auto;


            $message_data = array(
                'id' => 11,
            );
            $data['message'] = $this->message_model->get_message($message_data);

            // $data['message'] = 'Was cancelled';
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/product/congratulations';
            $this->load->view('user/includes/template', $data);
        }

        function payfast_success()
        {

            //other service type payments 
            $username = $this->session->userdata('username');
            $topup_order_row = $this->order_model->get_last_payfast_topup_order_by_username($username);
            $topup_info      = $this->product_model->topup_get_config($topup_order_row['topup_config_id']);
            $topup_name      = $topup_info['topup_name'];

            $order_message = '';
            if ($topup_order_row  != false) {

                $order_message = "<br/>";
                $order_message .= "Order details : ";
                $order_message .= "<br/> account : " . $topup_order_row['adsl_username'];
                $order_message .= "<br/> TopUp name : " .  $topup_name;
            }
            $message = "Your LTE-A account has been topped up successfully! Kindly give the system around 2 hours to load the topup and you will be back online.Thanks so much!";
            $data['message'] = $message . $order_message;

            //  $data['product_name'] = $product_name;
            //  $data['auto'] = $auto;    


            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/product/congratulations';
            $this->load->view('user/includes/template', $data);
        }



        function test_topup()
        {

            show_404();
            die();
            //redirect("user/dashboard");
            //die();
            $username = $this->site_data['username'];
            if ($username != 'test-vvv')
                redirect('user/dashboard');

            /*!*/
            $account_realm = 'mynetwork.co.za';

            // openweb.co.za - wrong password (3)
            // mynetwork.co.za - correct  // don't have premission (12)
            // fastadsl.co.za  // don't have premission

            // platinum.co.za - wrong password
            // openweb.adsl -





            $realm_data = $this->realm_model->get_realm_data_by_name($account_realm);

            echo "<pre>";
            print_r($realm_data);
            echo "</pre>";
            echo "<hr/>";

            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];
            $realm = $account_realm;
            $sess = 0;


            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id   //#A

            echo "<pre>";
            print_r($sess);
            echo "</pre>";
            echo "<hr/>";
            echo "<br/> 3 - Incorrect password supplied | 5 - wrong session , 12 - doesn't have premission";
            echo "<hr/>";


            $test_username = 'test-3234234@mynetwork.co.za';
            $result = $this->is_classes->get_top_ups_per_user_new($sess, $test_username);

            //die;

            // $available_topup_classes =  $this->is_classes->get_available_top_up_classes_new($sess);
            //  12 - Api user does not have  required permissions

            /*
        "arrClass":[],"intReturnCode":12 (12 - doesn't have premission)
                                            5 - wrong session
        */
            echo "<hr/><pre>";
            print_r($result);
            echo "</pre>";




            die;
        }


        // debug PDF invoice
        function debugPDF()
        {

            show_404();
            die();
            $invoice_id = '54805721';
            $username = 'test-vvv';
            $product_name = "TopUp 32";
            $price = "45.56";


            $this->get_topup_PDF($invoice_id, $username, $product_name, $price);
        }



        function get_topup_PDF($invoice_id, $username, $topup_name, $price)
        {

            // check user_model
            $this->load->library('tfpdf/MC_Table');
            $pdf = new MC_Table();

            $user_data = $this->user_model->get_user_data($username);
            $first_name = $user_data['user_settings']['first_name'];
            $last_name = $user_data['user_settings']['last_name'];

            $date = date('Y-m-d', time());
            $cost = 'R ' . $price;

            $user_billing = $this->user_model->get_user_billing_info($username);
            $billing_name = $user_billing['billing_name'];
            $user_address = $user_billing['address'];
            $user_city = $user_billing['city'];
            $user_country = $user_billing['country'];
            $user_province = $user_billing['province'];
            $user_phone = 'Phone: ' . $user_billing['phone'];
            $user_p_c = $user_province . ', ' . $user_country;

            $open_ISP =  $this->user_model->get_open_ISP();
            $open_name = $open_ISP['name'];
            $vat_number = $open_ISP['vat_number'];
            $country = $open_ISP['country'];
            $province = $open_ISP['province'];
            $address = $open_ISP['address'];
            $phone = $open_ISP['phone'];

            $title = 'New Order Tax Invoice for ' . $date;
            $pdf->AddPage();

            $pdf->SetFont('Arial', '', 20);
            //        $image = base_url().'img/main.png';
            $image = '/home/home/public_html/img/main.png';
            $pdf->Image($image, 70, 5, 60);

            $pdf->SetFont('Arial', '', 20);
            $pdf->SetXY(40, 30);
            $pdf->Cell(20, 8, $title, 'C', true);
            $pdf->Ln();

            //set invoice info
            $invoice_date = date('d/m/Y', time());
            $invoice_id_format = "Tax Inv # : $invoice_id";
            $invoice_date_format = "Date : $invoice_date";

            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(20, 4, $invoice_id_format, '', true);
            $pdf->Cell(36, 10, $invoice_date_format, 0, 0, 'R', false, '');
            $pdf->Ln();

            //set open info
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(20, 4, $open_name, '', true);
            //$pdf->SetXY(150,50);
            $pdf->Cell(185, 3, $billing_name, 0, 0, 'R', false, '');
            $pdf->Ln();

            //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(20, 3, INVOICE_ORGANIZATION_ID . $vat_number, '', true);
            $pdf->Cell(185, 3, $first_name . ' ' . $last_name, 0, 0, 'R', false, '');
            $pdf->Ln();

            $pdf->Cell(20, 3, $address, '', true);
            $pdf->Cell(185, 3, $user_address . ' ' . $user_city, 0, 0, 'R', false, '');
            $pdf->Ln();

            $pdf->Cell(20, 3, $province . ', ' . $country, '', true);
            $pdf->Cell(185, 3, $user_p_c, 0, 0, 'R', false, '');
            $pdf->Ln();

            $pdf->Cell(20, 3, 'Phone: ' . $phone, '', true);
            $pdf->Cell(185, 3, $user_phone, 0, 0, 'R', false, '');
            $pdf->Ln();

            $pdf->Ln();

            //set the body
            $pdf->SetFillColor(128, 128, 128);
            $pdf->SetTextColor(255);
            $pdf->SetDrawColor(92, 92, 92);

            //$pdf->Cell(50,8,"Username",1,0,'C',true);
            $pdf->Cell(95, 8, "Product", 1, 0, 'C', true);
            $pdf->Cell(40, 8, "Date Ordered", 1, 0, 'C', true);
            $pdf->Cell(50, 8, "Cost this month", 1, 0, 'C', true);
            $pdf->Ln();

            //$pdf->SetFillColor(224,235,255);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0);

            $pdf->SetWidths(array(95, 40, 50));
            $pdf->Row(array($topup_name, $date, $cost));

            $pdf->SetFillColor(255, 255, 255);
            $pdf->Ln(1);
            $pdf->Cell(185, 8, 'Total:' . $cost, 0, 0, 'R', true);
            $pdf->Ln();
            $pdf->Cell(0, 8, INVOICE_VAT_ROW, 0, 0, '', true);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Write(8, 'If you are on Debit Order you do not need to pay this invoice.');
            $pdf->Cell(90, 8, 'Banking Details:', 0, 0, 'R', false, '');
            $pdf->Ln();
            $pdf->Write(8, 'Please note, accounts are payable on the 27th of each month,');
            $pdf->Cell(89, 8, 'Bank: ABSA', 0, 0, 'R', false, '');
            $pdf->Ln();
            $pdf->Write(8, 'for the following months access.');
            $pdf->Cell(135, 8, 'Account Number: 4064449626', 0, 0, 'R', false, '');
            $pdf->Ln();
            $pdf->Write(8, 'Please remember, you have to send us proof of payment,');
            $pdf->Cell(96, 8, 'Account Type: Cheque', 0, 0, 'R', false, '');
            $pdf->Ln();
            $pdf->Write(8, 'otherwise we cannot honour the payment.');
            $pdf->Cell(120, 8, 'Branch Code: 632005', 0, 0, 'R', false, '');
            $pdf->Ln();
            $pdf->Write(8, 'Kindly email your proof of payment to : admin@openweb.co.za');
            $pdf->Cell(88, 8, 'Reference: ' . $first_name . ' ' . $last_name, 0, 0, 'R', false, '');
            //$pdf->Ln();
            //$pdf->Write(8, 'Fax proof to: 0866912166');

            $title = 'New Order Invoice for ' . $date;

            $path_name = APPPATH . 'PDFfiles/' . $username;
            if (is_dir($path_name) == false) {
                mkdir($path_name, 0777);
            }

            $file_name = $invoice_id . '.pdf';
            $file_save_path = $path_name . '/' . $file_name;
            $pdf->Output($file_save_path, 'F');

            $data = array(
                'name' => $file_name,
                'path' => $file_save_path,
                'create_date' => date('Y-m-d H:i:s', strtotime('now')),
                'user_name' => $username,
                'invoices_id' => $invoice_id
            );
            $pdf_id = $this->user_model->save_pdf($invoice_id, $data);
            return $pdf_id;
        }


        // ===================================================================================================

        function personal_docs_edit()
        {
            // $this->session->unset_userdata('mobile_data_order_id');

        }



        // page with mobile data
        function mobile_data_docs()
        {

            // get username and id
            $username = $this->site_data['username'];
            $user_id = $this->user_model->get_user_id($username);



            if (empty($user_id) || empty($username))
                redirect('user/active_orders');

            $data['user_id']  = $user_id;
            $data['username'] = $username;


            // load corresponding models
            $this->load->model("user_docs_model");
            $this->load->model('membership_model');


            // get user_id form POST request (check if POST request exist)
            $user_id_post = $this->product_model->process_product_request('user_id');

            // get fields and base url
            $mobile_data_client_fields = $this->validation_model->get_form_fields('mobile_data');
            $data['form_fields'] = $mobile_data_client_fields;
            $data['base_url'] = base_url();


            // handle  POST request
            $post_request_flag = false;
            if (isset($user_id_post) && !empty($user_id_post)) {

                $post_request_flag = true;

                // validation flags
                $fields_validation         = true;
                $file_passpost_validation  = true;
                $file_residence_validation = true;


                $delivery_address = $this->product_model->process_product_request('physical_delivery_address');
                $city = $this->product_model->process_product_request('city');
                $postcode = $this->product_model->process_product_request('postcode');

                //validation fields data
                $this->validation_model->set_rules_for_mobile_docs();
                if ($this->form_validation->run() == FALSE) {

                    // validation FAIL
                    $fields_validation = false;
                } else {

                    // validation TRUE ,  format  the array data
                    $fields_validation = true;
                    $user_data = array(

                        'delivery_address' => $delivery_address,
                        'city'             => $city,
                        'postcode'         => $postcode,
                        'username'         => $username,
                        'user_id'          => $user_id,
                    );

                    // insert or update fields
                    $fields_data_result = $this->user_docs_model->save_address_data($user_data);
                    // save/update fail, return false to validation
                    if (!$fields_data_result['result']) {
                        $fields_validation = false;
                        $data['fields_error_message'] = $fields_data_result['message'];
                    }
                }


                // repopulate data if validation failed
                if (!$fields_validation) {

                    $repopulated_array = $this->validation_model->re_populate_form('mobile_data_client');
                    $data['repopulated_array'] = $repopulated_array;
                }


                // validation Files
                $passport_data_result = $this->user_docs_model->save_new_file($user_id, $username,  'id_or_passport');
                $file_passport_validation = $passport_data_result['result'];
                if (!$file_passport_validation)
                    $data['id_or_passport_error_message'] = $passport_data_result['message'];

                /*
                echo "<pre>";
                print_r($passport_data_result);
                echo "</pre>";
                */

                $proof_data_result  = $this->user_docs_model->save_new_file($user_id, $username,  'proof_of_residence');
                $file_residence_validation = $proof_data_result['result'];
                if (!$file_residence_validation)
                    $data['proof_of_residence_error_message'] = $proof_data_result['message'];

                /*
                echo "<pre>";
                print_r($proof_data_result);
                echo "</pre>";
                */

                // ------------------------------------------------------------------------------------------ //

                if (($fields_validation == true)) {

                    // send request
                    //$mobile_data_order_id = $this->session->userdata('mobile_data_order_id');
                    // $mobile_data_order_id = $this->session->flashdata('mobile_data_order_id');
                    $mobile_data_order_id = false;
                    if (!empty($mobile_data_order_id)) {

                        redirect('user/mobile_data_request/' . $mobile_data_order_id);
                        return;
                    }

                    // log here
                    $data['success_message'] = "Data was updated successfully. Now you can request the Mobile Data from the <a href='active_orders'>Active Orders</a> page";
                }

                if (($fields_validation == true) || ($file_passport_validation == true) || ($file_residence_validation == true)) {

                    //add log
                    $this->membership_model->add_activity_log($user_id, "change_mobile_data");
                }

                // set flashdata
                /// $this->session->set_flashdata('error_message', $error_msg['error']);
                // redirect('admin/all_email');
                /*
                $data['fields_result'] = $fields_data_result;
                $data['proof_result'] = $proof_data_result;
                $data['passport_result'] = $passport_data_result;

                $data['back_link'] = 'user/mobile_data_docs';

                $data['sidebar'] = TRUE;
                $data['main_content'] = 'user/mobile_data_result';
                $this->load->view('user/includes/template', $data);
*/
            }

            // load data of this user
            $residence   =  $this->user_docs_model->get_file_full_data($user_id, 'residence');
            $passport    =  $this->user_docs_model->get_file_full_data($user_id, 'passport');

            if (!isset($data['repopulated_array']))
                $user_fields = $this->user_docs_model->get_address_data($user_id);

            // get first name
            $first_name = $this->membership_model->get_name($username);

            $data['user_fields']      = '';
            $data['residence']        = false;
            $data['passport']         = false;
            $data['residence_data']   = false;
            $data['passport_data']    = false;


            // if previous page was 'Active_services'
            $data['from_mobile_data_request'] = $this->session->flashdata('from_mobile_data_request');
            $data['first_name'] = $first_name;


            if (!empty($user_fields)) {
                $user_fields['physical_delivery_address'] = $user_fields['delivery_address'];
                $data['user_fields'] = $user_fields;
            }

            if (!empty($residence)) {

                $data['residence'] = true;
                $data['residence_data']['width'] = $residence['width'];
                $data['residence_data']['height'] = $residence['height'];
            }


            if (!empty($passport)) {
                $data['passport'] = true;
                $data['passport_data']['width'] = $passport['width'];
                $data['passport_data']['height'] = $passport['height'];
            }

            $message = isset($data['success_message']) ? $data['success_message'] : null;
            $this->settings('tab3', $message);
            /*
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/mobile_data';
            $this->load->view('user/includes/template', $data);*/
        }

        function mobile_data_docs_update()
        {

            die();
            // user data
            $username = $this->site_data['username'];
            $user_id = $this->user_model->get_user_id($username);

            $this->load->model('user_docs_model');

            $delivery_address = $this->product_model->process_product_request('physical_delivery_address');
            $city             = $this->product_model->process_product_request('city');
            $postcode         = $this->product_model->process_product_request('postcode');



            // fields
            // proof_of_residence_element
            // id_or_passport_element

            $user_data = array(

                'delivery_address' => $delivery_address,
                'city'             => $city,
                'postcode'         => $postcode,
                'username'         => $username,
                'user_id'          => $user_id,
            );

            $fields_data_result = $this->user_docs_model->save_address_data($user_data);


            $proof_data_result  = $this->user_docs_model->save_new_file($user_id, $username,  'proof_of_residence');
            $passport_data_result  = $this->user_docs_model->save_new_file($user_id, $username,  'id_or_passport');

            /*
            array(2) { ["result"]=> bool(true) ["message"]=> string(14) "Success fields" }
            array(2) { ["result"]=> bool(false) ["message"]=> string(57) "The filetype you are attempting to upload is not allowed." }
            array(2) { ["result"]=> bool(true) ["message"]=> string(15) "Success message" }

         */

            /*
         *
                $message = "Thank you for loading a Top Up to your account.  Your Topup has been successful and will take up to 30 minutes to become active.";
                $data['message'] = $message . $order_message;

                //  $data['product_name'] = $product_name;
                //  $data['auto'] = $auto;

         */


            // set flashdata
            /// $this->session->set_flashdata('error_message', $error_msg['error']);
            // redirect('admin/all_email');

            $data['fields_result'] = $fields_data_result;
            $data['proof_result'] = $proof_data_result;
            $data['passport_result'] = $passport_data_result;

            $data['back_link'] = 'user/mobile_data_docs';

            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/mobile_data_result';
            $this->load->view('user/includes/template', $data);
        }

        // GET DOCUMENT IMAGE
        // ----------------------------------------------------------------------------------
        function get_mobile_document($type)
        {


            // get username and id
            $username = $this->site_data['username'];
            $user_id = $this->user_model->get_user_id($username);

            if (empty($user_id))
                $this->validation_model->print_404();

            if (!$this->form_validation->numeric($user_id))
                $this->validation_model->print_404();

            // handle 'type' parameter
            $type = strip_tags(mysql_real_escape_string($type));
            $type = trim($type);

            if (($type != 'residence') && ($type != 'passport'))
                $this->validation_model->print_404();


            // load corresponding models
            $this->load->model("user_docs_model");
            $base_url = base_url(); // CHECK IF NOT EMPTY
            $file = $this->user_docs_model->get_file_full_data($user_id, $type);

            // check if row exist
            if (empty($file))
                $this->validation_model->print_404();

            $file_system_path = FCPATH .  $file['path'];
            $type = 'image/' . $file['image_type'];   // doesn't in use for now

            // check if file exist
            if (file_exists($file_system_path) == false)
                $this->validation_model->print_404();

            $file_size = filesize($file_system_path);

            ob_start();
            ob_clean();

            $this->output->set_header('Content-Description: File Transfer');
            $this->output->set_header('Content-Type: application/octet-stream');
            $this->output->set_header('Content-Disposition: attachment; filename="' . basename($file_system_path) . '"');
            $this->output->set_header('Expires: 0');
            $this->output->set_header('Cache-Control: must-revalidate');
            $this->output->set_header('Pragma: public');
            $this->output->set_header('Content-Length: ' . $file_size);

            readfile($file_system_path);
        }
        // ------------------------------------------------------------------------------------------




        function mobile_data_request($order_id)
        {

            //$this->session->unset_userdata('mobile_data_order_id');


            // user data
            $username = $this->site_data['username'];
            $user_id = $this->user_model->get_user_id($username);
            $this->load->model('user_docs_model');


            $this->session->set_flashdata('from_mobile_data_request', true);
            // check if mobile docs already exist
            $mobil_data_exist = $this->user_docs_model->check_user_mobile_documents($user_id);

            // validate Order ID
            if (empty($order_id) || !$this->form_validation->numeric($order_id)) {

                redirect('user/active_orders');
                return;
            }

            // check if current order belongs to current user
            $this->load->model('admin/order_model');


            $order_data = $this->order_model->get_order_data($order_id);
            if (($order_data['user'] != $username) || ($order_data['status'] != 'active')) {

                redirect('user/active_orders');
                return;
            }

            if (!$mobil_data_exist) {


                //$this->session->set_userdata('mobile_data_order_id', $order_id);
                redirect('user/mobile_data_docs');
                return;
            }


            // add new mobile reqeust
            $request_data = array();
            $request_data['user_id'] = $user_id;
            $request_data['username'] = $username;
            $request_data['order_id'] = $order_id;


            // insert_new_mobile_request($data);
            $insert_result = $this->user_docs_model->insert_new_mobile_request($request_data);

            $this->session->set_flashdata('mobile_request_result', $insert_result);
            $this->session->set_flashdata('success_message', 'Thank you. We will verify your documents and get back to you shortly.');
            $this->session->set_flashdata('fail_message', 'Something went wrong. Please report to admins about this issue');

            redirect('user/mobile_data_request_complete');
            return;
        }

        function mobile_data_request_complete()
        {

            $request_message = $this->session->flashdata('mobile_request_result');

            $request_success = $this->session->flashdata('success_message');
            $request_fail = $this->session->flashdata('fail_message');
            $data['return_to_active_orders'] = true;

            $data['message'] = $request_success;
            if ($request_message == false) {
                $data['message'] = $request_fail;
            }

            $data['back_url'] = base_url() . 'user/active_orders';
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/product/congratulations';
            $this->load->view('user/includes/template', $data);
        }





        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


        function debug_log_count()
        {


            die();
            $user_id = 8901;
            $type = 'residence';

            $this->load->model('user_docs_model');

            $user_count = $this->user_docs_model->get_count_of_log_files_by_type($user_id, $type);
            echo $user_count;

            echo "<br/>";

            $log_data = $this->user_docs_model->physically_file_remove($user_id, 'test-vvv', $type, 3);

            echo "<pre>";
            // print_r($log_data);
            echo "</pre>";

            $user_count = $this->user_docs_model->get_count_of_log_files_by_type($user_id, $type);
            echo $user_count;
        }



        function debug_mobile_data()
        {

            die();
            $this->load->model('user_docs_model');

            /*
        $request_data = array(

            'user_id'      => '8902',
            'username'     => 'test-someone',
            'order_id'     => '9',
        );

        $result = $this->user_docs_model->insert_new_mobile_request($request_data);
*/

            $result = $this->user_docs_model->get_mobile_request_by_id(3);
            $result2 = $this->user_docs_model->get_all_user_mobile_requests(8901);

            echo "<pre>";
            print_r($result);
            echo "</pre>";
            echo "<hr/>";

            echo "<pre>";
            print_r($result2);
            echo "</pre>";



            die();
            //$folder_result = $this->user_docs_model->getUserPersonalFolder(8902, 'test-vvv');
            //$hash_result = $this->user_docs_model->generateNewFolderName(8902, 'test-vvv');

            // $hash_result =  $this->user_docs_model->getUserPersonalFolder(8902, 'test-vvv');
            /*
      $path = "/var/www/html/lamp-wrk/keoma/home/gitrepo/application/PDFdocs/9c627bbc12f480628df193413a0660f4/test.jpg";
      $searchPattern =  "application/PDFdocs/";

        $resultPath = strstr($path, $searchPattern);
*/

            $user_id = '22';

            $test = $this->user_docs_model->get_address_data($user_id);
            var_dump($test);



            //  echo $resultPath;






            // var_dump($hash_result);
            // / echo "<br/>";
            //  echo strlen($hash_result);

        }


        function debug_topup_email()
        {



            die();

            $username = $this->site_data['username'];
            if ($username != 'test-vvv')
                redirect('user/dashboard');

            $this->user_model->email_topup_with_invoice_individual($username);
        }


        function debug_check_user_info()
        {

            // die();

            $username = $this->site_data['username'];
            if ($username != 'test-vvv')
                redirect('user/dashboard');


            /*
         * >> mqrf1r3@mynetwork.co.za
         *    spx1@openweb.adsl
           >> armanddewet@openweb.adsl
           >> dc1541@mynetwork.co.za

         */

            // Secret Avenue
            //  test-32342-vv@openweb.adsl
            // test-username889387262632@openweb.adsl

            $username = 'test-vvv-check-3441';
            $realm    = 'openweb.adsl';


            $realm_data = $this->realm_model->get_realm_data_by_name($realm);
            $rl_user = $realm_data['user']; // administrator
            $rl_pass = $realm_data['pass']; // 485c862ab6defdd2267d37bd497787d0

            // $rl_user = 'administrator'; // administrator
            // $rl_pass = '485c862ab6defdd2267d37bd497787d0'; // 485c862ab6defdd2267d37bd497787d0

            $lm = explode('@', $realm_data['user']);
            $realm = $lm[1];
            $sess = 0;
            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);


            $account_info = $this->is_classes->getAccountInfo_full_new($sess, $username . "@" . $realm);

            echo "<pre>";
            print_r($account_info);
            echo "</pre>";
            echo  "<br/>" . $account_info['intReturnCode'];
        }

        function debug_check_user_update()
        {

            die();

            $username = $this->site_data['username'];
            if ($username != 'test-vvv')
                redirect('user/dashboard');


            // $username =  'test-32342-vv';
            // $realm = 'openweb.adsl';
            // test-product-23432534@openweb.adsl
            $username = 'addditioanl-sandb-test32342';
            $realm    = 'openweb.adsl';



            $realm_data = $this->realm_model->get_realm_data_by_name($realm);
            $rl_user = $realm_data['user'];
            $rl_pass = $realm_data['pass'];
            $lm = explode('@', $realm_data['user']);
            $realm = $lm[1];
            $sess = 0;
            $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);


            $account_pend_info = $this->is_classes->get_pending_update_new($sess, $username . "@" . $realm);


            echo "<pre>";
            print_r($account_pend_info);
            echo "</pre>";
        }






        function debugisdsl($m_id)
        {

            die;
            //   $resp = $this->payfast_model->payfast_activate_order($m_id);
            //   var_dump($resp);

        }

        function debugcheckuser($m_id)
        {

            die;
            $pre_order_array = $this->payfast_model->get_pre_order($m_id);
            $account_result = $this->payfast_model->isdsl_get_account($pre_order_array);
            echo "<br/><br/><pre>";
            print_r($account_result);
            echo "</pre>";
        }

        function otherdebug($m_id)
        {

            die;
            //  $payfast_request = $this->payfast_model->get_last_payfast_request_by_internal_id($m_id);
            var_dump($payfast_request);
        }

        function debug_get_comment()
        {
            /*
        $product_id = '4719';
        $payment_method = 'eft';
        $result = $this->product_model->get_comment_by_product_and_payment_method($product_id, $payment_method);
      */
            die;

            $comment      = '[Name_Surname] (Reseller) ([amount] - [product_name]) (DEBIT ORDER)';
            $name_surname = 'Test Name';
            $amount       = 213.323;
            $product_name = '10GB Freedom Capped Unshaped';

            $result = $this->product_model->parse_default_comment($comment, $name_surname, $amount, $product_name);
            var_dump($result);
        }

        function topup_lte()
        {

            $this->rest_telkon_session();

            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);

            $num_per_page = NUM_PER_PAGE;
            $orders = $this->user_model->get_active_orders($username, $num_per_page, 0, array('lte-a'));
            array_unshift($orders, ["id" => 0]);

            $plans_cell = $this->payfast_model->get_topup_for_lte("cell");
            foreach ($plans_cell as &$plan) {
                $plan["topup_name"] = $plan["topup_name"] . " - R" . $plan["topup_price"];
            }
            array_unshift($plans_cell, ["topup_id" => 0, "topup_name" => "Choose TOP UP"]);

            $plans_rain = $this->payfast_model->get_topup_for_lte("rain");
            foreach ($plans_rain as &$plan) {
                $plan["topup_name"] = $plan["topup_name"] . " - R" . $plan["topup_price"];
            }
            array_unshift($plans_rain, ["topup_id" => 0, "topup_name" => "Choose TOP UP"]);


            $payment_info['item_name'] = null;
            $payment_info['item_description'] = null;
            $payment_info['discount'] = '0';
            $payment_info['price'] = null;
            $payment_info['pro_price'] = null;

            $data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info);
            $data_for_payfast['custom_str1'] = $username;
            $data_for_payfast['custom_str2'] = $username;
            //var_dump($data_for_payfast);die;

            $data['ajax_url'] = base_url() . "payfast/add_lte_topup";

            $data['sandbox_payfast_host'] = $this->payfast_model->sandbox_host;
            $data['live_payfast_host']   = $this->payfast_model->live_host;
            $data['payfast_data'] = $data_for_payfast;

            $data['plans_cell'] = $plans_cell;
            $data['plans_rain'] = $plans_rain;
            $data['orders'] = $orders;

            $data['sidebar'] = TRUE;
            $data['navbar'] = TRUE;
            $data['main_content'] = 'user/topup_page';
            $data['aditional_scripts'] = ['js/topup_lte.js'];
            $this->load->view('user/includes/template', $data);
        }

        function check_order_topup()
        {

            $id = $this->db->escape_str($_GET['order']);
            $order = $this->order_model->get_order_data($id);
            $fibre = $this->order_model->get_fibre_data_by_order($id);
            $service = $order["service_type"];

            if ($service == "lte-a") {
                $answ['response'] = "OK";
                $answ['username'] = $order['account_username'];
                $answ['lte_type'] = $fibre['lte_type'];
                echo json_encode($answ);
                die;
            }

            echo "err";
        }
        function topup_signature_telkom()
        {

            $id = $this->db->escape_str($_GET['id']);
            $order_username = $this->db->escape_str($_GET['username']);

            $service = $this->payfast_model->get_topup_plan($id);
            $this->session->set_userdata('telkom_topup_data', $service);
            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);


            $payment_info['item_name'] = $service['topup_name'];
            $payment_info['item_description'] = $service['topup_description'];
            $payment_info['discount'] = '0';
            $payment_info['price'] = $service['topup_price'];
            $payment_info['pro_price'] = $service['topup_price'];


            //$data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info);
            $data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info);

            unset($data_for_payfast['signature']);
            $data_for_payfast['return_url'] = base_url() . "user/succes_topup";
            $data_for_payfast['cancel_url'] = base_url() . "user/error_topup";
            $data_for_payfast['notify_url'] = base_url() . "user/notify";
            $data_for_payfast['custom_str1'] = $order_username;
            $data_for_payfast['custom_str2'] = $username;

            $order_signature = $this->payfast_model->generate_topup_lte_signature($data_for_payfast);

            $answer = [
                "sign" => $order_signature,
                "price" => $service['topup_price'],
                'topup_name' => $service['topup_name'],
                "topup_desc" => $service['topup_description'],
                "return_url" => $data_for_payfast['return_url'],
                "cancel_url" => $data_for_payfast['cancel_url'],
                "notify_url" => $data_for_payfast['notify_url']
            ];

            echo json_encode($answer);
        }
        function topup_signature()
        {

            $id = $this->db->escape_str($_GET['id']);
            $order_username = $this->db->escape_str($_GET['username']);
            $service = $this->payfast_model->get_topup_plan($id);

            $username = $this->site_data['username'];
            $user_id = $this->membership_model->get_user_id($username);

            $payment_info['item_name'] = $service['topup_name'];
            $payment_info['item_description'] = $service['topup_description'];
            $payment_info['discount'] = '0';
            $payment_info['price'] = $service['topup_price'];
            $payment_info['pro_price'] = $service['topup_price'];


            //$data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info);
            $data_for_payfast = $this->payfast_model->prepare_topup_final_checkout($user_id, $username, $payment_info);

            unset($data_for_payfast['signature']);
            $data_for_payfast['return_url'] = base_url() . "user/succes_topup";
            $data_for_payfast['cancel_url'] = base_url() . "user/error_topup";
            $data_for_payfast['notify_url'] = base_url() . "user/notify";
            $data_for_payfast['custom_str1'] = $order_username;
            $data_for_payfast['custom_str2'] = $username;

            $order_signature = $this->payfast_model->generate_topup_lte_signature($data_for_payfast);

            $answer = [
                "sign" => $order_signature,
                "price" => $service['topup_price'],
                'topup_name' => $service['topup_name'],
                "topup_desc" => $service['topup_description'],
                "return_url" => $data_for_payfast['return_url'],
                "cancel_url" => $data_for_payfast['cancel_url'],
                "notify_url" => $data_for_payfast['notify_url']
            ];

            echo json_encode($answer);
        }

        function succes_topup()
        {
            if ($this->session->userdata('custom_service') == 'telkom' && $this->session->userdata('custom_service') != '') {
                //telkom success code place holder
                $username = $this->site_data['username'];
                $user_id = $this->membership_model->get_user_id($username);
                $order_data = $this->session->userdata('telkom_topup_data');
                $order_id =   $this->session->userdata('custom_service_telkom_order_no');

                $data = array(
                    'rel_rec_topup_name' => $order_data['topup_name'],
                    'rel_rec_amount' => $order_data['topup_price'],
                    'rel_rec_order_id' => $order_id,
                    'rel_rec_user_id' => $user_id,
                    'rel_rec_topup_id' => $order_data['topup_id'],
                    'rel_rec_status' => 'Topup Request'
                );

                $this->db->insert('telkom_recharge_requests', $data);
            } elseif ($this->session->userdata('custom_service') == 'mtn' && $this->session->userdata('custom_service') != '') {
                $username = $this->site_data['username'];
                $user_id = $this->membership_model->get_user_id($username);
                $order_data = $this->session->userdata('telkom_topup_data');
                $order_id =   $this->session->userdata('custom_service_telkom_order_no');

                $data = array(
                    'rel_rec_topup_name' => $order_data['topup_name'],
                    'rel_rec_amount' => $order_data['topup_price'],
                    'rel_rec_order_id' => $order_id,
                    'rel_rec_user_id' => $user_id,
                    'rel_rec_topup_id' => $order_data['topup_id'],
                    'rel_rec_status' => 'Topup Request'
                );

                $this->db->insert('mtn_recharge_requests', $data);
            }
            $this->session->unset_userdata('telkom_topup_data');

            $message = "Thank you for purchasing your OpenWeb topup. Topups can take the mobile network anywhere from 1 hour to 24 hours to load, dependent on how busy their systems are at the time. We apologise for the inconvenience, however, this is a limitation on the mobile network side, not OpenWeb. Thank you so much for your understanding.";
            $data['message'] = $message;

            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/product/congratulations';
            $this->load->view('user/includes/template', $data);
        }

        function error_topup()
        {

            $message = "Your topup was unsuccessful due to your credit card not being authorized by your bank.  Kindly check your credit card account and try again.  Alternatively, you can topup via EFT by emailing admin@openweb.co.za";
            $data['message'] = $message;

            $data['sidebar'] = TRUE;
            $data['main_content'] = 'user/product/congratulations';
            $this->load->view('user/includes/template', $data);
        }
        function noify()
        {
            if ($this->session->userdata('custom_service') == 'telkom' && $this->session->userdata('custom_service') != '') {
                //telkom success code place holder
                $username = $this->site_data['username'];
                $user_id = $this->membership_model->get_user_id($username);
                $order_data = $this->session->userdata('telkom_topup_data');
                $order_id =   $this->session->userdata('custom_service_telkom_order_no');

                $data = array(
                    'rel_rec_topup_name' => $order_data['topup_name'],
                    'rel_rec_amount' => $order_data['topup_price'],
                    'rel_rec_order_id' => $order_id,
                    'rel_rec_user_id' => $user_id,
                    'rel_rec_topup_id' => $order_data['topup_id'],
                    'rel_rec_status' => 'Topup Request'
                );

                $this->db->insert('telkom_recharge_requests', $data);
            } elseif ($this->session->userdata('custom_service') == 'mtn' && $this->session->userdata('custom_service') != '') {
                $username = $this->site_data['username'];
                $user_id = $this->membership_model->get_user_id($username);
                $order_data = $this->session->userdata('telkom_topup_data');
                $order_id =   $this->session->userdata('custom_service_telkom_order_no');

                $data = array(
                    'mtn_rec_topup_name' => $order_data['topup_name'],
                    'mtn_rec_amount' => $order_data['topup_price'],
                    'mtn_rec_order_id' => $order_id,
                    'mtn_rec_user_id' => $user_id,
                    'mtn_rec_topup_id' => $order_data['topup_id'],
                    'mtn_rec_status' => 'Topup Request',

                );

                $this->db->insert('mtn_recharge_requests', $data);
            }
            $this->session->unset_userdata('telkom_topup_data');
            exit();
        }
        function read_not()
        {

            $re = $this->membership_model->readNotification($_POST['ids']);
            echo json_encode("ok");
        }

        function request_for_order()
        {

            $id = $this->input->get('id');

            $prodTypes = $this->form_builder_model->getTypesIds();
            $data = [];

            if (isset($id) && in_array($id, $prodTypes)) {
                $formId = $this->form_builder_model->getFormId($id);
                $fields = $this->form_builder_model->getFormFields($formId);
                $data['form_fields'] = json_decode($fields, true);
                $data['packages'] = $this->form_builder_model->getAvailableProducts($id);
                $data['prod_id'] = $id;
            }

            $data['navbar'] = TRUE;
            $data['sidebar'] = TRUE;
            $this->asignSidebarData($data);
            $data['aditional_scripts'] = ['js/request_for_order.js'];
            $data['main_content'] = 'user/product/request_for_order';
            $this->load->view('user/includes/template', $data);
        }

        function place_new_order()
        {

            $user_id = $this->user_model->get_user_id($this->site_data['username']);
            $order_data = $this->input->post();
            $form_id = $this->form_builder_model->getFormId($order_data['prod-id']);
            $form_fields = $this->form_builder_model->getFormFields($form_id);
            $form_fields = json_decode($form_fields, true);
            $package_id = $order_data['package'];

            $package = $this->form_builder_model->getSpecProduct($order_data['package']);
            $order_data['package'] = $package['name'] . ' ' . $package['description'] . ' ' . $package['price'];

            $body = 'User place the new order <br>';

            $base_fields = [
                0 => ['name' => 'first_name', 'value' => 'First Name', 'title' => 'First Name'],
                1 => ['name' => 'second_name', 'value' => 'Second Name', 'title' => 'Second Name'],
                2 => ['name' => 'email', 'value' => 'mail@mail', 'title' => 'Email'],
                3 => ['name' => 'phone', 'value' => 'Phone 00000000', 'title' => 'Phone'],
                4 => ['name' => 'package', 'value' => '', 'title' => 'Package'],
            ];

            $i = 0;
            $count = 0;

            foreach ($order_data as $name => $val) {

                if ($name == 'prod-id' || $name == 'terms' || $name == 'mandate')
                    continue;

                if ($i > 4) {
                    $body .= $form_fields[$count]['name'] . ': ' . $val . '<br>';
                    $count++;
                    $additional_fields[$name] = $val;
                } else {
                    $body .= $base_fields[$i]['title'] . ': ' . $val . '<br>';
                }
                $i++;
            }

            $data['message'] = 'Order not placed. Please, contact us admin@openweb.co.za';

            $fields = [
                'user_id' => $user_id,
                'name' => $order_data['name'] . ' ' . $order_data['second-name'],
                'email' => $order_data['email'],
                'phone' => $order_data['phone'],
                'package_id' => $package_id,
                'additional_fields' => json_encode($additional_fields),
                'date' => date("Y-m-d H:i:s")
            ];
            $order_id = $this->order_model->add_manual_order($fields);

            if ($order_id)
                $data['message'] = 'Your order was successfully placed. Our team contact you soon';


            $this->message_model->send_email_html('new_order@openweb.co.za', 'admin@openweb.co.za', 'New order #' . $order_id, $body, null);
            //$this->message_model->send_email_html('new_order@openweb.co.za','admin@openweb.co.za','New order #', $body, null);

            $data['navbar'] = TRUE;
            $data['sidebar'] = TRUE;
            $this->asignSidebarData($data);
            $data['main_content'] = 'user/product/request_for_order_result';
            $this->load->view('user/includes/template', $data);
        }

        function daily_usage_cron()
        {
            $other_log = array('Function_name' => 'create_next_invoice_page', 'Url' => $this->uri->uri_string(), 'Call_mode' => 'AJAX', 'Ajax_url' => 'user/daily_usage_cron');
            button_log("Send LTD usage stats", $this->session->userdata('username'), $this->session->userdata('role'), json_encode($other_log));

            $this->load->model('daily_usage_log');
            $this->load->model('lte_usage_stats_model');

            $this->daily_usage_log->start();

            try {
                $lte_orders = $this->order_model->get_all_orders_by_service_type('lte-a');

                $this->load->library('email');

                foreach ($lte_orders as $order) {
                    if ($order['status'] == 'active') {
                        $send = $this->membership_model->has_daily_lte_usage($order['user']);

                        $this->daily_usage_log->log('has_daily_lte_usage', json_encode([
                            'user' => $order['user'],
                            'has' => $send
                        ]));

                        if ($send) {
                            $email = $this->membership_model->get_email($order['user']);

                            $this->db->select('id,title,content,email_address');
                            $this->db->where('purpose', 'daily_lte_usage');
                            $query = $this->db->get('email_template');
                            $result = $query->result_array();

                            $this->daily_usage_log->log('db_email', json_encode($result));

                            if (!empty($result)) {
                                $result = $result[0];
                                $email_template_id = $result['id'];
                                $email_address = $result['email_address'];
                                $title = $result['title'];
                                $content = $result['content'];

                                $content = str_ireplace('[First Name]', $this->membership_model->get_name($order['user']), $content);
                                $this->daily_usage_log->log('replace', json_encode([
                                    'replace' => '[First Name]',
                                    'to' => $this->membership_model->get_name($order['user']),
                                    'result' => $content
                                ]));
                                $content = str_ireplace('[Last Name]', $this->membership_model->get_second_name($order['user']), $content);
                                $this->daily_usage_log->log('replace', json_encode([
                                    'replace' => '[Last Name]',
                                    'to' => $this->membership_model->get_second_name($order['user']),
                                    'result' => $content
                                ]));
                                $content = str_ireplace('[Username]', $order['account_username'], $content);
                                $this->daily_usage_log->log('replace', json_encode([
                                    'replace' => '[Username]',
                                    'to' => $order['account_username'],
                                    'result' => $content
                                ]));

                                if (strpos($content, '[Product Name]') !== false) {
                                    $product = $this->product_model->get_product_data($order['product']);
                                    if (empty($product)) {
                                        $product = '';
                                    } else {
                                        $product = $product['name'];
                                    }
                                    $content = str_ireplace('[Product Name]', $product, $content);

                                    $this->daily_usage_log->log('replace', json_encode([
                                        'replace' => '[Product Name]',
                                        'to' => $product,
                                        'result' => $content
                                    ]));
                                }

                                if (strpos($content, '[Day Usage]') !== false) {
                                    $day = $this->network_api_handler_model->get_lte_usage($order['account_username'], 'd', $order['realm']);
                                    $day_usage = round($this->network_api_handler_model->lte_day_usage_result($day) / pow(1024, 2), 2, PHP_ROUND_HALF_UP);
                                    $day_usage = $this->user_model->update_data_amount($order['id'], $day_usage);

                                    $content = str_ireplace('[Day Usage]', $day_usage, $content);

                                    $this->daily_usage_log->log('replace', json_encode([
                                        'replace' => '[Day Usage]',
                                        'to' => $day_usage,
                                        'result' => $content
                                    ]));
                                }

                                //Always needed for Remaining data calculating
                                $month = $this->network_api_handler_model->get_lte_usage($order['account_username'], 'm', $order['realm']);
                                $month_usage = round($month[0]->Total / pow(1024, 2), 2, PHP_ROUND_HALF_UP);
                                echo '<pre>';
                                var_dump($month_usage);
                                $month_usage = $this->user_model->update_data_amount($order['id'], $month_usage);

                                if (strpos($content, '[Month Usage]') !== false) {
                                    $content = str_ireplace('[Month Usage]', $month_usage, $content);
                                    $this->daily_usage_log->log('replace', json_encode([
                                        'replace' => '[Month Usage]',
                                        'to' => $month_usage,
                                        'result' => $content
                                    ]));
                                }

                                if (strpos($content, '[Year Usage]') !== false) {
                                    $year = $this->network_api_handler_model->get_lte_usage($order['account_username'], 'us', $order['realm']);
                                    $year_usage = round($year[0]->MainPackageUsed / pow(1024, 2), 2, PHP_ROUND_HALF_UP);
                                    $year_usage = $this->user_model->update_data_amount($order['id'], $year_usage);

                                    $content = str_ireplace('[Year Usage]', $year_usage, $content);
                                    $this->daily_usage_log->log('replace', json_encode([
                                        'replace' => '[Year Usage]',
                                        'to' => $year_usage,
                                        'result' => $content
                                    ]));
                                }

                                if (strpos($content, '[Usage Summary]') !== false) {
                                    $lte_usage_summary = $this->isdsl_model->getLteUsages($order['account_username'], $order['realm']);
                                    $lte_usage_summary['Packages'] = $this->user_model->update_total_data($lte_usage_summary['Packages'], $order['id'], $month_usage);

                                    $fields = array(
                                        'data_type' => 'Data Type',
                                        'category' => 'Category',
                                        'title' => 'Title',
                                        'total_data' => 'Total Data',
                                        'remaining_data' => 'Remaining Data',
                                        'last_update' => 'Last Update',
                                        'activation_date' => 'Activation Date',
                                        'expire_date' => 'Expire Date'
                                    );

                                    $new = '';

                                    foreach ($lte_usage_summary['Packages'] as $key => $data) {
                                        if ($key > 0) {
                                            $new .= "\n";
                                        }

                                        foreach ($fields as $slug => $field) {
                                            if ($this->lte_usage_stats_model->toDisplay($slug)) {
                                                switch ($field) {
                                                    case 'Total Data':
                                                        $new .= "Total Data : " . $data['Total Data'] . " " . $data['Data Units'] . "\n";
                                                        break;

                                                    case 'Remaining Data':
                                                        $new .= "Remaining Data : " . $data['Remaining Data'] . " " . $data['Data Units'] . "\n";
                                                        break;

                                                    default:
                                                        $new .= $field . " : " . $data[$field] . "\n";
                                                        break;
                                                }
                                            }
                                        }
                                    }

                                    $content = str_ireplace('[Usage Summary]', $new, $content);
                                    $this->daily_usage_log->log('replace', json_encode([
                                        'replace' => '[Usage Summary]',
                                        'to' => $new,
                                        'result' => $content
                                    ]));
                                }

                                $this->db->where('email_template_id', $email_template_id);
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

                                // $result = $this->email->send();

                                $this->daily_usage_log->log('send', json_encode([
                                    'from' => $email_address,
                                    'to' => $email,
                                    'subject' => $title,
                                    'message' => $content,
                                    'result' => $result
                                ]));
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->daily_usage_log->error($e->getMessage());
            }

            $this->daily_usage_log->end();
        }
    }
