<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends My_Controller
{

    private $ui_prefix = '';
    private $page_category = '';
    private $args = array();
    private $isMailSendable = false;

    function __construct()
    {

        parent::__construct();
        $this->is_logged_in();
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");

        // FORCE SSL

        if ($_SERVER['HTTPS'] != "on" && ($_SERVER['HTTP_HOST'] != STAGE_HOST)) {
            $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $this->load->model('flat_ui_model');
        $this->ui_prefix = $this->flat_ui_model->check_ui_prefix();
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // page marker
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // $this->load->library('session');

        $this->load->model('admin/stats_model');
        $this->load->model('admin/user_model');
        $this->load->model('admin/product_model');
        $this->load->model('admin/category_model');
        $this->load->model('admin/order_model');
        $this->load->model('admin/realm_model');
        $this->load->model('admin/is_classes');

        // migration model & payfast
        $this->load->model('admin/migration_model');
        $this->load->model('payfast_model');
        $this->load->model('sms_model');
        $this->load->model('validation_model');
        $this->load->model('membership_model');
        $this->load->model('crypto_model');
        $this->load->model('avios/avios_main');
        $this->load->model('form_builder_model');

        $username = $this->session->userdata('username');
        $last_login_time = $this->session->userdata('last_login_time');
        $first_name = $this->membership_model->get_name($username);


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $this->page_category = array(
            'orders' => array(
                '/admin/all_orders' => 'All Orders',
                '/admin/pending_orders' => 'Pending Orders',
                '/admin/user_orders' => 'User Orders',
                '/admin/assign_order' => 'Assign a Service to User',
                '/admin/all_undef_orders' => 'All undefined Orders',
            ),
            'monthly_reports' => array(
                '/admin/new_orders' => 'Orders this Month',
                '/admin/all_reports' => 'Billing per User',
                '/admin/changed_bills' => 'Changed Bills',
                '/admin/bills_history' => 'Billing History',
            ),
            'manage_users' => array(
                '/admin/all_account' => 'All Accounts',
                '/admin/create_account' => 'Create Account',
            ),
            'avios_bonus' => array(
                '/admin/avios_award_form' => 'Award user Avios bonuses',
                '/admin/avios_stat' => 'Avios Statistic'
            ),
            'products' => array(
                '/admin/all_product' => 'All Products',
                '/admin/create_product' => 'Add a Product',
                '/admin/all_nosvc_product' => 'All (nosvc) Products',
                '/admin/all_product_reseller' => 'All Reseller Products',
                '/admin/create_category' => 'Add a Category',
                '/admin/all_category' => 'All Categories',
                '/admin/all_category_reseller' => 'All Categories (Reseller)',
                '/admin/create_subcategory' => 'Add a Sub-Category',
                '/admin/all_subcategory' => 'All Subcategories',
                '/admin/all_subcategory_reseller' => 'All Subcategories (Reseller)',
            ),
            'topup' => array(
                '/admin/all_topup' => 'All TopUps',
                '/admin/create_topup' => 'Add a TopUp configuration',
                '/admin/topup_reports' => 'TopUp Orders Report',
            ),
            'realms_and_classes' => array(
                '/admin/create_realm' => 'Create a New Realm',
                '/admin/all_realms' => 'All Realms',
                '/admin/create_class' => 'Create a New Class',
                '/admin/view_classes' => 'All Classes',
            ),
            'messages' => array(
                '/admin/all_email' => 'Email Templates',
                '/admin/send_notification' => 'Send a Notification to a User',
                '/admin/bulk_mail' => 'Bulk mailing',
                '/admin/all_messages' => 'All System Messages',
            ),
            'invoices' => array(
                '/admin/create_invoice' => 'Create Invoice',
                '/admin/all_invoices' => 'Invoices List',
            ),
            'usage_stats_settings' => array(
                '/admin/lte_usage_stats_settings' => 'LTE Usage Stats Settings'
            ),
            'user_role_rights' => array(
                '/admin/user_role_rights' => 'User Manager'
            )
        );
        // -------------------------------------------------------

        $this->site_data['username'] = $username;
        $this->site_data['first_name'] = $first_name;
        $this->site_data['last_login_time'] = $last_login_time;
        $this->site_data['ow'] = $this->session->userdata('ow');

        $role = $this->session->userdata('role');
        // Cloud DSL
        if ($role == 'client') {
            redirect('user/dashboard');
        }

        $this->args["strUserName"] = "wri52";
        $this->args["strPassword"] = "keoma99";
    }
    /*----------------------------------------------------------------------------------------------------------------------------    
*MENU Manager START
*----------------------------------------------------------------------------------------------------------------------------
*/
    function button_logger_view()
    {
        $this->db->select('*');
        $query = $this->db->get('button_log');
        $res =  $query->result();
        $data['logs'] = $res;
        $data['main_content'] = 'admin/button_log_viewer';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }
    function user_role_rights()
    {
        $data['main_content'] = 'admin/user_role_rights';
        $data['sidebar'] = TRUE;
        $this->load->model('admin/roles_rights_model');
        $data['roles_rights'] = $this->roles_rights_model->get_role_access();
        $this->load->view('admin/includes/template', $data);
    }
    function set_permissions_on_buttons()
    {
        $data['main_content'] = 'admin/set_permissions_on_buttons';
        $data['sidebar'] = TRUE;
        $this->load->model('admin/roles_rights_model');
        $data['roles_rights'] = $this->roles_rights_model->get_buttons_access();
        $this->load->view('admin/includes/template', $data);
    }
    function user_management()
    {
        $data['main_content'] = 'admin/user_management';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function button_permissions_roles()
    {
        $data = $this->input->post('roles');

        $updatearr = array();
        foreach ($data as $ar => $el) {
            if ($el['checked'] == 'true') {
                $found = -1;
                // does the key already exist? Thought I could use array_search(), but no
                for ($i = 0; $i < count($updatearr); $i++) {
                    if ($updatearr[$i]['role_id'] == $el['role_id']) {
                        $found = $i;
                        break;
                    }
                }
                if ($found != -1) {
                    // already have a main entry, so just add to the val array
                    $updatearr[$found]['accessor_id'] = $updatearr[$found]['accessor_id'] . ',' . $el['accessor_id'];
                } else {
                    // there *must* be a better way to do this bit in one line
                    $updatearr[]['role_id'] = $el['role_id'];
                    $latest = count($updatearr) - 1;
                    $updatearr[$latest]['accessor_id'] = $el['accessor_id'];
                }
            }
        }
        $this->load->model('admin/roles_rights_model');
        $result = $this->roles_rights_model->update_button_permission($updatearr);
        echo $result;
    }


    function save_user_roles()
    {
        $data = $this->input->post('roles');

        $updatearr = array();
        foreach ($data as $ar => $el) {
            if ($el['checked'] == 'true') {
                $found = -1;
                // does the key already exist? Thought I could use array_search(), but no
                for ($i = 0; $i < count($updatearr); $i++) {
                    if ($updatearr[$i]['role_id'] == $el['role_id']) {
                        $found = $i;
                        break;
                    }
                }
                if ($found != -1) {
                    // already have a main entry, so just add to the val array
                    $updatearr[$found]['accessor_id'] = $updatearr[$found]['accessor_id'] . ',' . $el['accessor_id'];
                } else {
                    // there *must* be a better way to do this bit in one line
                    $updatearr[]['role_id'] = $el['role_id'];
                    $latest = count($updatearr) - 1;
                    $updatearr[$latest]['accessor_id'] = $el['accessor_id'];
                }
            }
        }
        $this->load->model('admin/roles_rights_model');
        $result = $this->roles_rights_model->update_role($updatearr);
        echo $result;
    }
    /*----------------------------------------------------------------------------------------------------------------------------    
*MENU Manager END
*----------------------------------------------------------------------------------------------------------------------------
*/
    public function data_transfer()
    {
        //22-06-2020    
        $data['main_content'] = 'admin/data_transfer';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    public function data_transfer_submit()
    {
        $data = array(
            "Sender MSISDN" => $_POST['sender_MSISDN'],
            "Recipient MSISDN" => $_POST['recipient_MSISDN'],
            "Amount" => $_POST['amount']
        );

        $payload = json_encode($data);
        $host = 'https://www.isdsl.net/api/rest/lte/transferData.php';
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';

        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $msg_filter = strstr($result, '{');
        $msg = json_decode($msg_filter, TRUE);
        if (array_key_exists("error_msg", $msg)) {

            $this->session->set_flashdata('error_message', $msg['error_msg']);
        } else {
            $this->session->set_flashdata('success_message', "API Responce : " . $msg_filter);
        }
        redirect('/admin/data_transfer', 'refresh'); //
    }

    public function toggleLteMail()
    {
        $toggle = $_GET['toggle'];
        $this->db->where('id', 1);
        $this->db->set('let_mail_toggle', $toggle);
        $this->db->update('admin_extra_controlles');
        $msg = array('msg' => "LTE Mail Status Changed to " . $toggle . ' successfully.');
        echo json_encode($msg);
    }

    public function getToogle()
    {
        $query = $this->db->get_where('admin_extra_controlles', array('id' => 1));
        $q = $query->result();
        $r = $q[0]->let_mail_toggle;
        echo json_encode($r);
    }

    public function mtn_usage_summary()
    {
        $data['main_content'] = 'admin/mtn_usage_summary';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    public function get_mtn_usage_summary()
    {
        if (isset($_POST['request_type'])) {
            $user = $_POST['username'];

            //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';

            $username = 'api@openwebmobile.co.za';
            $password = 'oC3JRkyQ7q==123-';
            $host = 'https://www.isdsl.net/api/rest/lte/usageSummary.php?username=' . $user;
            echo $user;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
            $response = curl_exec($ch);
            $msg_filter = strstr($response, '{');
            $msg = json_decode($msg_filter, TRUE);
            if (array_key_exists("error_msg", $msg)) {
                $this->session->set_flashdata('error_message', $msg['error_msg']);
            } else {
                $this->session->set_flashdata('success_message', "Successfull" . "<br>Complete API Responce : " . $msg_filter);
                $this->session->set_flashdata('res_data', $msg['data']);
                $this->session->set_flashdata('res_data_user_name', $user);
            }
            redirect('/admin/mtn_usage_summary');
            return;
        }
        redirect('/admin/mtn_usage_summary');
        return;
    }

    public function queue_topup_lte_account()
    {
        $data['main_content'] = 'admin/queue_topup_lte_account';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    public function submit_queue_topup_lte_account()
    {
        if (trim($_POST['request_type']) == 'queue_topup_request') {

            $data = [
                "Username" => $_POST['username'],
                "Topup" => $_POST['topup'],
            ];
            $payload = json_encode($data, TRUE);
            $host = 'https://www.isdsl.net/api/rest/lte/queueTopup.php';
            $username = 'api@openwebmobile.co.za';
            $password = 'oC3JRkyQ7q==123-';
            $ch = curl_init($host);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($ch);
            curl_close($ch);
            $msg_filter = strstr($return, '{');
            $msg = json_decode($msg_filter, TRUE);
            if (array_key_exists("error_msg", $msg)) {
                $this->session->set_flashdata('error_message', $msg['error_msg']);
            } else {
                $this->session->set_flashdata('success_message', "Topup ID is." . $msg['data']['Topup ID'] . "<br>Complete API Responce : " . $msg_filter);
            }

            redirect('/admin/queue_topup_lte_account');
            return;
        }
        $this->session->set_flashdata('error_message', "Something is wrong.");
        redirect('/admin/queue_topup_lte_account');
        return;
    }

    public function rica_mtn_sim()
    {
        $data['main_content'] = 'admin/rica_mtn_sim';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    public function submit_rica_mtn_sim()
    {
        if (trim($_POST['request_type']) == 'rica_mtn_sim') {
            $data = [
                "RICA" => array(
                    "UserName" => $_POST['Username'],
                    "IdNumber" => $_POST['IdNumber'],
                    "ContactName" => $_POST['ContactName'],
                    "TelCell" => $_POST['TellCell'],
                    "Building" => $_POST['Building'],
                    "AddressComplex" => $_POST['AddressComplex'],
                    "Street" => $_POST['Street'],
                    "Suburb" => $_POST['Suburb'],
                    "City" => $_POST['City'],
                    "PostCode" => $_POST['PostCode'],
                )
            ];
            $payload = json_encode($data, TRUE);
            $host = 'https://www.isdsl.net/api/rest/lte/ricaMTNSim.php';
            $usernameAuth = 'api@openwebmobile.co.za';
            $passwordAuth = 'oC3JRkyQ7q==123-';
            $ch = curl_init($host);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $usernameAuth . ":" . $passwordAuth);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($ch);
            curl_close($ch);
            $msg_filter = strstr($return, '{');
            $msg = json_decode($msg_filter, TRUE);
            if (array_key_exists("error_msg", $msg)) {
                $this->session->set_flashdata('error_message', $msg['error_msg']);
            } else {
                $this->session->set_flashdata('success_message', "Success" . "<br>Complete API Responce : " . $msg_filter);
            }
            return redirect('/admin/rica_mtn_sim');
        }
        $this->session->set_flashdata('error_message', "Something is wrong.");
        return redirect('/admin/rica_mtn_sim');
    }

    public function get_class_name()
    {
        $usernameAuth = 'api@openwebmobile.co.za';
        $passwordAuth = 'oC3JRkyQ7q==123-';
        $host = 'https://www.isdsl.net/api/rest/lte/getClass.php?realm=openwebmobile';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $usernameAuth . ":" . $passwordAuth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
    }

    function index()
    {
        redirect('admin/dashboard');
    }

    // 	function d(){
    //Testing mail function
    // 	   $this->load->library('email');
    //         $email = $this->message_model->get_telkom_topup_loaded_mail_template();
    //         if($email){
    //           $email_attachment_data = $this->db->where('email_template_id',$email['id']);
    // 			$attac_query = $this->db->get('email_attachment');
    // 			$attac_result = $attac_query->result_array(); 
    // 		            $content = $email['content'];
    // 					$content = str_ireplace('[Username]',"Test Name", $content);
    // 					$content = str_ireplace('[Order Number]',"Test order number", $content);
    // 					$content = str_ireplace('[Product Name]',"Rock the mount", $content);
    // 					$content = str_ireplace('[Product Price]',"100", $content);
    // 					$this->email->from($email['email_address'], 'OpenWeb Home');
    // 					$this->email->to('jamtechtest@gmail.com');
    // 					$this->email->subject($email['title']);
    // 					$this->email->message($content);
    // 					if(!empty($attac_result)){
    // 						foreach ($attac_result as $att){
    // 							$path = $att['path'];
    // 							$this->email->attach($path);
    // 						}
    // 					}
    // 					$this->email->send();
    //                     $this->email->clear(TRUE);
    //         }else{
    //         echo "No template avail";    
    //         }
    // 	}
    //telkom topup loaded d
    //l = loaded
    function telkom_topup_loaded()
    {
        $topup_id = filter_input(INPUT_POST, 'l_topup_id', FILTER_SANITIZE_STRING);
        $topup_name = filter_input(INPUT_POST, 'l_topup_name', FILTER_SANITIZE_STRING);
        $topup_price = filter_input(INPUT_POST, 'l_topup_price', FILTER_SANITIZE_STRING);
        $topup_buyer_name = filter_input(INPUT_POST, 'topup_buyer_name', FILTER_SANITIZE_STRING);
        $topup_buyer_email = filter_input(INPUT_POST, 'topup_buyer_email', FILTER_SANITIZE_STRING);
        $topup_order_id = filter_input(INPUT_POST, 'topup_order_id', FILTER_SANITIZE_STRING);

        $telkom_topup_request_data = array(
            'tel_rec_loaded_date' => date('Y-m-d H:i:s'),
            'rel_rec_status' => 'Topup Loaded'
        );
        $this->db->set($telkom_topup_request_data)
            ->where('tel_rec_id', $topup_id)
            ->update('telkom_recharge_requests');
        $db_error = $this->db->_error_message();
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $this->load->library('email');
            $email = $this->message_model->get_telkom_topup_loaded_mail_template();
            if ($email) {
                $email_attachment_data = $this->db->where('email_template_id', $email['id']);
                $attac_query = $this->db->get('email_attachment');
                $attac_result = $attac_query->result_array();

                $content = $email['content'];
                $content = str_ireplace('[Username]', $topup_buyer_name, $content);
                $content = str_ireplace('[Order Number]', $topup_order_id, $content);
                $content = str_ireplace('[Product Name]', $topup_name, $content);
                $content = str_ireplace('[Product Price]', 'R' . $topup_price, $content);
                $this->email->from($email['email_address'], 'OpenWeb Home');
                $this->email->to($topup_buyer_email); //$topup_buyer_email
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
                $success = "Telkom Topup loaded mailed Successfully.";
            } else {

                $title = 'Telkom Topup Loaded Mail';
                $content = 'Dear ' . $topup_buyer_name . ',<br/>';
                $content .= "Thank you for purchasing your Home LTE Topup. Please find the details regarding your order below.<br/>";
                $content .= "Your topup has been loaded and will be available within 30 minutes. If you still cannot access the internet in 30 minutes, please reboot your router.<br/>";
                $content .= "Order Number: " . $topup_order_id . "<br/>";
                $content .= "Product Name: " . $topup_name . "<br/>";
                $content .= "Product Price: R" . $topup_price . "<br/>";
                $content .= "If you have any queries, please do not hesitate to contact us on support@openweb.co.za<br/>";
                $content .= "Best Wishes<br/>";
                $content .= "OpenWeb.co.za";
                $this->email->set_mailtype("html");
                $this->email->from('admin@openweb.co.za', 'OpenWeb');
                $this->email->to($topup_buyer_email); //
                $this->email->subject($title);
                $this->email->message($content);
                $this->email->send();
                $success = "Telkom Topup loaded mailed Successfully.";
            }
            $success = "Telkom Topup loaded mailed Successfully.";
        }
        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    ///mobile
    function mobile_topup_loaded()
    {
        extract($_POST);

        $telkom_topup_request_data = array(
            'mob_rec_loaded_date' => date('Y-m-d H:i:s'),
            'mob_rec_status' => 'Topup Loaded'
        );
        $this->db->set($telkom_topup_request_data)
            ->where('mob_rec_id', $topup_id)
            ->update('mob_recharge_requests');
        $db_error = $this->db->_error_message();
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $this->load->library('email');
            $email = $this->message_model->get_telkom_topup_loaded_mail_template();
            if ($email) {
                $email_attachment_data = $this->db->where('email_template_id', $email['id']);
                $attac_query = $this->db->get('email_attachment');
                $attac_result = $attac_query->result_array();

                $content = $email['content'];
                $content = str_ireplace('[Username]', $topup_buyer_name, $content);
                $content = str_ireplace('[Order Number]', $topup_order_id, $content);
                $content = str_ireplace('[Product Name]', $topup_name, $content);
                $content = str_ireplace('[Product Price]', 'R' . $topup_price, $content);
                $this->email->from($email['email_address'], 'OpenWeb Home');
                $this->email->to($topup_buyer_email); //$topup_buyer_email
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
                $success = "Telkom Topup loaded mailed Successfully.";
            } else {

                $title = 'Mobile Topup Loaded Mail';
                $content = 'Dear ' . $topup_buyer_name . ',<br/>';
                $content .= "Thank you for purchasing your Home LTE Topup. Please find the details regarding your order below.<br/>";
                $content .= "Your topup has been loaded and will be available within 30 minutes. If you still cannot access the internet in 30 minutes, please reboot your router.<br/>";
                $content .= "Order Number: " . $topup_order_id . "<br/>";
                $content .= "Product Name: " . $topup_name . "<br/>";
                $content .= "Product Price: R" . $topup_price . "<br/>";
                $content .= "If you have any queries, please do not hesitate to contact us on support@openweb.co.za<br/>";
                $content .= "Best Wishes<br/>";
                $content .= "OpenWeb.co.za";
                $this->email->set_mailtype("html");
                $this->email->from('admin@openweb.co.za', 'OpenWeb');
                $this->email->to($topup_buyer_email); //
                $this->email->subject($title);
                $this->email->message($content);
                $this->email->send();
                $success = "Telkom Topup loaded mailed Successfully.";
            }
            $success = "Mobile Topup loaded mailed Successfully.";
        }
        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    function mtn_topup_loaded()
    {
        $topup_id = filter_input(INPUT_POST, 'l_topup_id', FILTER_SANITIZE_STRING);
        $topup_name = filter_input(INPUT_POST, 'l_topup_name', FILTER_SANITIZE_STRING);
        $topup_price = filter_input(INPUT_POST, 'l_topup_price', FILTER_SANITIZE_STRING);
        $topup_buyer_name = filter_input(INPUT_POST, 'topup_buyer_name', FILTER_SANITIZE_STRING);
        $topup_buyer_email = filter_input(INPUT_POST, 'topup_buyer_email', FILTER_SANITIZE_STRING);
        $topup_order_id = filter_input(INPUT_POST, 'topup_order_id', FILTER_SANITIZE_STRING);

        $telkom_topup_request_data = array(
            'mtn_rec_loaded_date' => date('Y-m-d H:i:s'),
            '	mtn_rec_status' => 'Topup Loaded'
        );
        $this->db->set($telkom_topup_request_data)
            ->where('	mtn_rec_id', $topup_id)
            ->update(' mtn_recharge_requests');
        $db_error = $this->db->_error_message();
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $this->load->library('email');
            $email = $this->message_model->get_telkom_topup_loaded_mail_template();
            if ($email) {
                $email_attachment_data = $this->db->where('email_template_id', $email['id']);
                $attac_query = $this->db->get('email_attachment');
                $attac_result = $attac_query->result_array();

                $content = $email['content'];
                $content = str_ireplace('[Username]', $topup_buyer_name, $content);
                $content = str_ireplace('[Order Number]', $topup_order_id, $content);
                $content = str_ireplace('[Product Name]', $topup_name, $content);
                $content = str_ireplace('[Product Price]', 'R' . $topup_price, $content);
                $this->email->from($email['email_address'], 'OpenWeb Home');
                $this->email->to($topup_buyer_email); //$topup_buyer_email
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
                $success = "MTN Topup loaded mailed Successfully.";
            } else {

                $title = 'MTN Topup Loaded Mail';
                $content = 'Dear ' . $topup_buyer_name . ',<br/>';
                $content .= "Thank you for purchasing your Home LTE Topup. Please find the details regarding your order below.<br/>";
                $content .= "Your topup has been loaded and will be available within 30 minutes. If you still cannot access the internet in 30 minutes, please reboot your router.<br/>";
                $content .= "Order Number: " . $topup_order_id . "<br/>";
                $content .= "Product Name: " . $topup_name . "<br/>";
                $content .= "Product Price: R" . $topup_price . "<br/>";
                $content .= "If you have any queries, please do not hesitate to contact us on support@openweb.co.za<br/>";
                $content .= "Best Wishes<br/>";
                $content .= "OpenWeb.co.za";
                $this->email->set_mailtype("html");
                $this->email->from('admin@openweb.co.za', 'OpenWeb');
                $this->email->to($topup_buyer_email); //
                $this->email->subject($title);
                $this->email->message($content);
                $this->email->send();
                $success = "MTN Topup loaded mailed Successfully.";
            }
            $success = "MTN Topup loaded mailed Successfully.";
        }
        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    function telkom_topup_temp_removed()
    {
        $order_type = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
        if ($order_type == 'telkom') {
            $telkom_remove_data = array(
                'telkom_status_temp_removed_status' => 'TEMP REMOVED'
            );
            $this->db->set($telkom_remove_data)
                ->update('telkome_stat');
        } elseif ($order_type == 'mtn') {
            $telkom_remove_data = array(
                'mtn_status_temp_removed_status' => 'TEMP REMOVED'
            );
            $this->db->set($telkom_remove_data)
                ->update('mtn_stat');
        } elseif ($order_type == 'mobile') {
            $telkom_remove_data = array(
                'mobile_status_temp_removed_status' => 'TEMP REMOVED'
            );
            $this->db->set($telkom_remove_data)
                ->update('mobile_stat');
            // ->update('mobile_stat');
        }

        $db_error = $this->db->_error_message();
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $success = $order_type . " LTE Stats Request Records Removed Successfully.";
        }
        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    //09-01-2020
    function reset_telkom_stats()
    {
        $order_id = filter_input(INPUT_POST, 'request_code', FILTER_SANITIZE_STRING);
        $order_type = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
        if ($order_type == 'telkom') {
            $telkom_stat_request_reset_data = array(
                'telkom_status' => 'RESETED',
                'telkom_total_cap' => '...',
                'telkom_time_cap' => '...',
                'telkom_night_cap' => '...'
            );
            $this->db->set($telkom_stat_request_reset_data)
                ->where('telkom_user_code', $order_id)
                ->update('telkome_stat');
        } elseif ($order_type == 'mtn') {
            $telkom_stat_request_reset_data = array(
                'mtn_status' => 'RESETED',
                'mtn_total_cap' => '...',
                'mtn_time_cap' => '...',
                'mtn_night_cap' => '...'
            );
            $this->db->set($telkom_stat_request_reset_data)
                ->where('mtn_user_code', $order_id)
                ->update('mtn_stat');
        } elseif ($order_type == 'mobile') {
            $telkom_stat_request_reset_data = array(
                'mobile_status' => 'RESETED',
                'mobile_total_minutes' => '...',
                'mobile_total_data' => '...',
                'mobile_total_sms' => '...'
            );
            $this->db->set($telkom_stat_request_reset_data)
                ->where('mobile_user_code', $order_id)
                ->update('mobile_stat');
        }

        $db_error = $this->db->_error_message();
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $success = $order_type . " Record Reset Successfully.";
        }
        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    function delete_status_request()
    {
        extract($_POST);
        $order_id = $request_code;
        if ($order_type == 'telkom') {
            $this->db->where('telkom_user_code', $order_id);
            $this->db->delete('telkome_stat');
        } elseif ($order_type == 'mtn') {
            $this->db->where('mtn_user_code', $order_id);
            $this->db->delete('mtn_stat');
        } elseif ($order_type == 'mobile') {
            $this->db->where('mobile_user_code', $order_id);
            $this->db->delete('mobile_stat');
        }
        $db_error = $this->db->_error_message();
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $success = $order_type . " Record Deleted Successfully";
        }
        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    function api()
    {

        $providers_array = array();
        $previous_id = array();
        $sessionResult = $this->CurlFunction($this->args, "getSession");

        $this->args['strSessionId'] = $sessionResult->object->strSessionId;


        $result = $this->CurlFunction($this->args, "checkSession");

        if ($result->object->intCode == '200') {

            $address = $_POST['address'];
            $latlan = explode(',', $_POST['latlan']);
            $lat = $latlan[0];
            $long = $latlan[1];
            $this->args['strLongitude'] = $long;
            $this->args['strLatitude'] = $lat;
            $this->args['strAddress'] = $address;
            $re = $this->CurlFunction($this->args, "checkFibreAvailability");

            foreach ($re->object->arrAvailableProvidersGuids as $provider) {

                $this->args['guidNetworkProviderId'] = $provider->guidNetworkProviderId;
                $previous_id[] = array(
                    'guiId' => $provider->guidNetworkProviderId,
                    'preOrder' => $provider->intPreOrder
                );

                $providers_array = $this->CurlFunction($this->args, "getNetworkProviders");
            }
            $networlProvidersList = '<h4>' . $re->object->strMessage . '</h4><table class="table"><thead>
<tr>
<th>Provider name</th>
<th>Status</th>
</tr>
</thead><tbody>';

            foreach ($providers_array->object->arrNetworkProviders as $pro) {

                foreach ($previous_id as $pId) {

                    if ($pro->guidNetworkProviderId == $pId['guiId']) {
                        $initOrder = '';
                        if ($pId['preOrder'] == 0) {
                            $initOrder = 'Live';
                        } else {
                            $initOrder = 'Pre-Order';
                        }
                        $networlProvidersList .= '<tr><td><strong>' . $pro->strName . '</strong></td>';
                        $networlProvidersList .= '<td><strong>' . $initOrder . '</strong></td></tr>';
                    }
                }
            }
            $networlProvidersList .= '</tbody></table>';
            echo $networlProvidersList;
        }
    }

    // monthsummaryinfo api call
    function monthsummaryinfo()
    {
        $usernames = trim($_POST['usernames']);
        $year = $_POST['year'];
        $month = $_POST['month'];
        $date = $_POST['date'];
        $APIuri5 = "https://www.isdsl.net/api/rest/lte/monthSummary.php?year=" . $year . "&month=" . $month . "&usernames=" . $usernames;
        /* These are the lyrics to Hello Dolly */
        $curl5 = curl_init();
        curl_setopt_array($curl5, array(
            CURLOPT_URL => $APIuri5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            curl_setopt($curl5, CURLOPT_SSL_VERIFYPEER, false),
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
            ),
        ));
        $response5 = curl_exec($curl5);
        $data5 = json_decode($response5, true);
        if (!empty($data5['data'])) {
            $monthsummaryinfo .= "<div class='pull-right'>$showing</div>";
            $tmpl = array('table_open' => '<div class="month_summary_start" style="overflow: auto;"><table class="table">');
            $monthsummaryinfo .= $this->table->set_template($tmpl);
            $monthsummaryinfo .= $this->table->set_heading(array('YM', 'Source', 'Year', 'MonthName', 'Day', 'UserName', 'MSISDSN', 'ConnectedTime', 'Total', 'TotalIn', 'TotalOut'));
            foreach ($data5['data'] as $key => $mnthsmmry) {
                $Total = round($mnthsmmry['Total'] / 1024 / 1024, 4) . 'MB';
                $TotalIn = round($mnthsmmry['TotalIn'] / 1024 / 1024, 4) . 'MB';
                $TotalOut = round($mnthsmmry['TotalOut'] / 1024 / 1024, 4) . 'MB';
                $monthsummaryinfo .= $this->table->add_row(array($mnthsmmry['YM'], $mnthsmmry['Source'], $mnthsmmry['Year'], $mnthsmmry['MonthName'], $mnthsmmry['Day'], $mnthsmmry['UserName'], $mnthsmmry['MSISDSN'], $mnthsmmry['ConnectedTime'], $mnthsmmry['Total'], $Total, $TotalIn, $TotalOut));
            }
            $monthsummaryinfo .= $this->table->generate();
            $monthsummaryinfo .= "<div class='pull-right'>$pages</div></div>";
        } else {

            $monthsummaryinfo .= '<div class="alert alert-warning">';
            $monthsummaryinfo .= '<strong>Data not found.</strong>';
            $monthsummaryinfo .= '</div>';
        }
        echo $monthsummaryinfo;
    }

    function monthUsageinfo()
    {

        $usernames = trim($_POST['usernames']);
        $year = $_POST['year'];
        $month = $_POST['month'];
        $date = $_POST['date'];
        $APIuri2 = "https://www.isdsl.net/api/rest/lte/monthUsage.php?year=" . $year . "&month=" . $month . "&usernames=" . $usernames;
        /* These are the lyrics to Hello Dolly */
        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $APIuri2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false),
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
            ),
        ));
        $response2 = curl_exec($curl2);
        $data2 = json_decode($response2, true);
        if (!empty($data2['data'])) {
            $monthusageinfo .= "<div class='pull-right'>$showing</div>";
            $tmpl = array('table_open' => '<div class="month_summary_start" style="overflow: auto;"><table class="table">');
            $monthusageinfo .= $this->table->set_template($tmpl);
            $monthusageinfo .= $this->table->set_heading(array('YM', 'Source', 'Year', 'MonthName', 'UserName', 'MSISDSN', 'ConnectedTime', 'Total'));
            foreach ($data2['data'] as $key => $mnthusage) {
                $total = round($mnthusage['Total'] / 1024 / 1024, 4) . 'MB';
                $monthusageinfo .= $this->table->add_row(array($mnthusage['YM'], $mnthusage['Source'], $mnthusage['Year'], $mnthusage['MonthName'], $mnthusage['UserName'], $mnthusage['MSISDSN'], $mnthusage['ConnectedTime'], $total));
            }
            $monthusageinfo .= $this->table->generate();
            $monthusageinfo .= "<div class='pull-right'>$pages</div></div>";
        } else {

            $monthusageinfo .= '<div class="alert alert-warning">';
            $monthusageinfo .= '<strong>Data not found.</strong>';
            $monthusageinfo .= '</div>';
        }
        echo $monthusageinfo;
    }

    // monthsummaryinfo api call
    function dayUsageinfo()
    {
        $usernames = trim($_POST['usernames']);
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];
        $APIuri6 = "https://www.isdsl.net/api/rest/lte/dayUsage.php?year=" . $year . "&month=" . $month . "&day=" . $day . "&usernames=" . $usernames;
        /* These are the lyrics to Hello Dolly */
        $curl6 = curl_init();
        curl_setopt_array($curl6, array(
            CURLOPT_URL => $APIuri6,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            curl_setopt($curl6, CURLOPT_SSL_VERIFYPEER, false),
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
            ),
        ));
        $response6 = curl_exec($curl6);
        $data6 = json_decode($response6, true);
        if (!empty($data6['data'])) {
            $dayusageinfo .= "<div class='pull-right'>$showing</div>";
            $tmpl = array('table_open' => '<div class="month_summary_start" style="overflow: auto;"><table class="table">');
            $dayusageinfo .= $this->table->set_template($tmpl);
            $dayusageinfo .= $this->table->set_heading(array('i_xdr', 'connect_time', 'disconnect_time', 'MSISDSN', 'ConnectedTime', 'disconnect_reason', 'charged_quantity'));
            foreach ($data6['data'] as $key => $dayusage) {
                $chargedQuantity = round($dayusage['charged_quantity'] / 1024 / 1024, 4) . 'MB';
                $dayusageinfo .= $this->table->add_row(array($dayusage['i_xdr'], $dayusage['connect_time'], $dayusage['disconnect_time'], $dayusage['MSISDSN'], $dayusage['ConnectedTime'], $dayusage['disconnect_reason'], $chargedQuantity));
            }
            $dayusageinfo .= $this->table->generate();
            $dayusageinfo .= "<div class='pull-right'>$pages</div></div>";
        } else {

            $dayusageinfo .= '<div class="alert alert-warning">';
            $dayusageinfo .= '<strong>Data not found.</strong>';
            $dayusageinfo .= '</div>';
        }
        echo $dayusageinfo;
    }

    // usercommentsinfo data

    function usercommentsinfo($start = 0)
    {

        $usernames = $this->session->userdata('acc_pro');

        if (isset($_POST['search'])) {
            $search = trim($_POST['search']);
            $this->session->set_userdata("acc_usercmnts", $search);
        } else {
            $search = $this->session->userdata('acc_usercmnts');
        }

        $num_per_page = NUM_PER_PAGE;
        // get the lte account info for all users
        $APIuri = "https://www.isdsl.net/api/rest/lte/usernameInfo.php?usernames=" . $usernames;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $APIuri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
            ),
        ));
        $response = curl_exec($curl);
        $data = json_decode($response, true);

        $data_arr = array();
        foreach ($data['data'] as $key => $value) {
            $keyword = $value['User Comment'];
            if (preg_match("/{$search}/i", $keyword)) {
                $data_arr[$key] = $value;
            }
        }

        $items = array_slice($data_arr, $start, 10);
        $num_item = count($data_arr);

        if ($items) {
            $items = $items;
        } else {
            $items = '';
            $num_item = 0;
        }


        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/usercommentsinfo');

        $config['total_rows'] = $num_item;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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

        $item_data = array();
        if (!empty($items)) {
            foreach ($items as $key => $item) {
                $username = $key;
                $status = $item['status'];
                $class = $item['Class'];
                $password = $item['Password'];
                $user_comment = $item['User Comment'];
                $system_comment = $item['System Comment'];
                $email_address = $item['Email Address'];

                $item_data[] = array(
                    'username' => $username,
                    'status' => $status,
                    'class' => $class,
                    'password' => $password,
                    'user_comment' => $user_comment,
                    'system_comment' => $system_comment,
                    'email_address' => $email_address
                );
            }
        }
        $iem_count = count($items);
        $item_ind = $start + $iem_count;
        $start = $start + 1;
        $data['product_type'] = 'All ';
        $data['showing'] = "Showing $start-$item_ind of $num_item";
        $data['num_per_page'] = $num_per_page;
        $data['num_item'] = $num_item;
        $data['items'] = $item_data;
        $data['main_content'] = 'admin/lte_account_search';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;


        // page category + title + link data
        $data['page_link'] = "/admin/lte_account_search";
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);

        //   echo '<pre>';
        //   print_r($items);
        //   exit;
    }

    // usernameInfo api call
    function usernameinfo($start = 0)
    {

        if (isset($_POST['usernames'])) {
            $usernames = trim($_POST['usernames']);
            $this->session->set_userdata("acc_pro", $usernames);
        } else {
            $usernames = $this->session->userdata('acc_pro');
        }


        $num_per_page = NUM_PER_PAGE;
        $APIuri = "https://www.isdsl.net/api/rest/lte/usernameInfo.php?usernames=" . $usernames;
        /* These are the lyrics to Hello Dolly */
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $APIuri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
            ),
        ));
        $response = curl_exec($curl);

        $datas = json_decode($response, true);

        $items = array_slice($datas['data'], $start, 10);
        $num_item = count($datas['data']);

        if ($items) {
            $items = $items;
        } else {
            $items = '';
            $num_item = 0;
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/usernameinfo');

        $config['total_rows'] = $num_item;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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

        $item_data = array();
        if (!empty($items)) {
            foreach ($items as $key => $item) {
                $username = $key;
                $status = $item['status'];
                $class = $item['Class'];
                $password = $item['Password'];
                $user_comment = $item['User Comment'];
                $system_comment = $item['System Comment'];
                $email_address = $item['Email Address'];
                $item_data[] = array(
                    'username' => $username,
                    'status' => $status,
                    'class' => $class,
                    'password' => $password,
                    'user_comment' => $user_comment,
                    'system_comment' => $system_comment,
                    'email_address' => $email_address
                );
            }
        }

        $iem_count = count($items);
        $item_ind = $start + $iem_count;
        $start = $start + 1;
        $data['product_type'] = 'All ';
        $data['showing'] = "Showing $start-$item_ind of $num_item";
        $data['num_per_page'] = $num_per_page;
        $data['num_item'] = $num_item;
        $data['items'] = $item_data;
        $data['main_content'] = 'admin/lte_account_search';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        // page category + title + link data
        $data['page_link'] = "/admin/lte_account_search";
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function usernameInfoAjaxRequest()
    {
        if (isset($_GET['usernames']) && !empty($_GET['usernames']) && $_GET['usernames'] != 'All' && $_GET['isAjax'] == true) {
            $usernames = trim($_GET['usernames']);
        } else {
            echo "Unable to find User Info";
            exit();
        }
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        $host = 'https://www.isdsl.net/api/rest/lte/usernameInfo.php?usernames=' . $usernames;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $dataArray = json_decode($response, TRUE);
        $d = $dataArray['data'][$usernames];
        $data = [
            'user' => $usernames,
            'status' => $d['Status'],
            'class' => $d['Class'],
            'password' => $d['Password'],
            'user_comment' => $d['User Comment'],
            'system_comment' => $d['System Comment'],
            'email_address' => $d['Email Address'],
        ];
        echo json_encode($data, TRUE);
    }

    public function deleteAccountAjaxRequest()
    {
        $usernames = trim($_GET['usernames']);
        $reason_code = trim($_GET['reason_code']);
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        // $username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/deleteAccount.php?username=' . $usernames . '&reason_code=' . $reason_code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $msg = [
            'msg' => "Responce :" . $response
        ];

        echo json_encode($msg, TRUE);
    }

    public function restoreAccountAjaxRequest()
    {
        $usernames = trim($_GET['usernames']);
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/restoreAccount.php';
        $data = ["Username" => $usernames];
        $payload = json_encode($data, TRUE);
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        curl_close($ch);
        $msg = [
            'msg' => "Responce :" . $return
        ];

        echo json_encode($msg, TRUE);
    }

    public function usageSummaryAjaxRequest()
    {
        $usernames = trim($_GET['usernames']);
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/usageSummary.php?username=' . $usernames;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $data_array = json_decode($response, TRUE);
        $data = $data_array['data'][$usernames];
        $tbl = '<table><thead><th>ID</th>
<th>Data Type</th>
<th>Category</th>
<th>Title</th>
<th>Total Data</th>
<th>Remaining Data</th>
<th>Data Units</th>
<th>Last Update</th>
<th>Assigned Date</th>
<th>Activation Date</th>
<th>Expire Date</th>
<th>Type ID</th>
<th>Status</th></thead><tbody>';
        foreach ($data['Packages'] as $p) {
            $tbl .= '<tr>';
            $tbl .= '<td>' . $p['ID'] . '</td>';
            $tbl .= '<td>' . $p['Data Type'] . '</td>';
            $tbl .= '<td>' . $p['Category'] . '</td>';
            $tbl .= '<td>' . $p['Title'] . '</td>';
            $tbl .= '<td>' . $p['Total Data'] . '</td>';
            $tbl .= '<td>' . $p['Remaining Data'] . '</td>';
            $tbl .= '<td>' . $p['Data Units'] . '</td>';
            $tbl .= '<td>' . $p['Last Update'] . '</td>';
            $tbl .= '<td>' . $p['Assigned Date'] . '</td>';
            $tbl .= '<td>' . $p['Activation Date'] . '</td>';
            $tbl .= '<td>' . $p['Expire Date'] . '</td>';
            $tbl .= '<td>' . $p['Type ID'] . '</td>';
            $tbl .= '<td>' . $p['Status'] . '</td>';
            $tbl .= '</tr>';
        }
        $tbl .= '</tbody></table>';
        $msg = [
            'msg' => $tbl
        ];

        echo json_encode($msg, TRUE);
    }

    public function pendingUpdateAjaxRequest()
    {
        $usernames = trim($_GET['usernames']);
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/pendingUpdate.php?username=' . $usernames;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $msg = [
            'msg' => $response
        ];

        echo json_encode($msg, TRUE);
    }

    public function deletePendingUpdateAjaxRequest()
    {
        $usernames = trim($_GET['usernames']);
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/deletePendingUpdate.php?username=' . $usernames;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $msg = [
            'msg' => $response
        ];

        echo json_encode($msg, TRUE);
    }

    public function setPendingUpdateAjaxRequest()
    {
        $data = [
            "Username" => $_GET['username'],
            "Package" => $_GET['topup'],
        ];
        $payload = json_encode($data, TRUE);

        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/setPendingUpdate.php';
        $ch = curl_init($host);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $return = curl_exec($ch);
        curl_close($ch);
        $msg = [
            'msg' => "Responce :" . $return
        ];

        echo json_encode($msg, TRUE);
    }

    public function topupAccountAjaxRequest()
    {
        $data = [
            "Username" => $_GET['username'],
            "Topup" => $_GET['topup'],
        ];
        $payload = json_encode($data, TRUE);

        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';
        $host = 'https://www.isdsl.net/api/rest/lte/queueTopup.php';
        $ch = curl_init($host);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $return = curl_exec($ch);
        curl_close($ch);
        $msg = [
            'msg' => "Responce :" . $return
        ];

        echo json_encode($msg, TRUE);
    }

    //----------------------------------------------------------
    function select_user_orders($user = '')
    {
        if (isset($_POST['user'])) {
            $user = $_POST['user'];
        }
        $this->session->set_userdata(array("user_name" => $user));
        redirect("admin/user_orders");
    }

    function user_orders($start = 0)
    {
        $this->session->set_userdata(array("manage_flag" => "user_orders"));

        $user = $this->session->userdata('user_name');
        $users = $this->user_model->get_user_list();
        $user_list = array();
        if (!empty($users)) {
            /* foreach ($users as $u) {
          $key = $u['username'];
          $value = "{$u['first_name']} {$u['last_name']} ($key)";
          $user_list[$key] = $value;
          } */
            $data['user_list'] = $users;
        }


        if (trim($user) != '') {
            $data['user'] = $user;
            $user_name = $this->user_model->get_user_name($user);
            $data['user_name'] = $user_name; //full name

            $num_per_page = NUM_PER_PAGE;
            //$orders = $this->order_model->get_user_orders($user);
            $orders = $this->user_model->get_orders($user, $num_per_page, $start, array('adsl', 'fibre-data', 'fibre-line', 'lte-a'), $this->order_model);
            $num_order = $this->user_model->get_orders_count($user);
            $this->load->library('pagination');
            $config['base_url'] = base_url('index.php/admin/user_orders');
            $config['total_rows'] = $num_order;
            $config['per_page'] = $num_per_page;
            $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active"><a href="#"><b>';
            $config['cur_tag_close'] = '</b></a></li>';
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
                    //var_dump($product_data);die();
                    if (!isset($order['fibre']))
                        $order['fibre'] = '';

                    $order_data[] = array(
                        'user' => $user,
                        'status' => $status,
                        'date' => $date,
                        'product_data' => $product_data,
                        'product_id' => $product_id,
                        'id' => $order['id'],
                        'acc_username' => $order['account_username'],
                        'acc_password' => $order['account_password'],
                        'service_type' => $order['service_type'],
                        'fibre' => $order['fibre'],
                    );
                }
            }
            $data['user_orders'] = $order_data;
            $act_count = $start + count($orders);
            $data['showing'] = "Showing $start-$act_count of $num_order";
        }
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $error_msg = $this->session->flashdata('error_message');
        $data['messages']['error_message'] = $error_msg;

        $data['main_content'] = 'admin/accounts/user_orders';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/user_orders";
        $data['page_category'] = $this->page_category['orders'];
        $data['sidebar_category'] = 'orders';
        $data['page_title'] = $this->page_category['orders'][$data['page_link']];
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function calculate_reports()
    {
        $pro_ratas = $this->order_model->get_pro_ratas();
        $all_bills = $this->order_model->get_all_monthly_bills();
        $data['monthly_reports']['pro_ratas'] = $pro_ratas;
        $data['main_content'] = 'admin/accounts/monthly_reports';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
        //$this->order_model->find_changed($reports);
    }

    function new_orders()
    {
        $pro_ratas = $this->order_model->get_pro_ratas();
        $data['monthly_reports']['pro_ratas'] = $pro_ratas;
        $data['main_content'] = 'admin/accounts/monthly_reports';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/new_orders";
        $data['page_category'] = $this->page_category['monthly_reports'];
        $data['page_title'] = $this->page_category['monthly_reports'][$data['page_link']];
        $data['sidebar_category'] = 'monthly_reports';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_reports()
    {
        $all_bills = $this->order_model->get_all_monthly_bills();
        $data['monthly_reports']['all_bills'] = $all_bills;
        $data['main_content'] = 'admin/accounts/monthly_reports';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/all_reports";
        $data['page_category'] = $this->page_category['monthly_reports'];
        $data['page_title'] = $this->page_category['monthly_reports'][$data['page_link']];
        $data['sidebar_category'] = 'monthly_reports';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function update_changed_bills()
    {
        $this->order_model->update_changed_bills();
        redirect('admin/changed_bills');
    }

    function changed_bills()
    {
        $changed_bills = $this->order_model->get_changed_bills();
        $data['monthly_reports']['changed_bills'] = $changed_bills;
        $data['main_content'] = 'admin/accounts/monthly_reports';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/changed_bills";
        $data['page_category'] = $this->page_category['monthly_reports'];
        $data['page_title'] = $this->page_category['monthly_reports'][$data['page_link']];
        $data['sidebar_category'] = 'monthly_reports';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function monthly_reports()
    {
        $data['main_content'] = 'admin/accounts/monthly_reports';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function activate_order($order_id = 0)
    {


        $manage_flag = $this->session->userdata('manage_flag');

        if ($order_id) {



            // ------------------------------------ Start replacing here ----------------------------
            // --------------------------------------------------------------------------------------
            // get order data

            $class = $this->order_model->get_is_class($order_id);
            $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);
            $realm = $realm_data['realm'];

            // account data
            $new_account_data = $this->order_model->get_order_data($order_id);
            $new_user = trim($new_account_data['account_username']);
            $order_user = $new_account_data['user'];
            $new_pass = trim($new_account_data['account_password']);
            $comment = $new_account_data['account_comment'];
            $product_id = $new_account_data['product'];

            $product_name = $this->product_model->get_product_name($product_id);

            // membership data
            $email = $this->membership_model->get_email($order_user);
            $number = $this->membership_model->get_number($order_user); //get mobile number from billing


            $this->load->model("network_api_handler_model");

            $order_data = array('account_username' => $new_user, 'realm' => $realm);

            $creation_result = $this->network_api_handler_model->add_new_realm_user($order_data, $class, $new_pass, $comment, $email);


            if ($creation_result['result'] == true) {

                // Everything was successful.
                // Email user
                $this->order_model->email_activation($email, $product_name, $new_user, $realm, $new_pass, $order_user);
                $this->order_model->set_activated($order_id);
                $sms_content = 'Your ADSL product has been successfully created. See email for more details. - OpenWeb';
                $this->order_model->send_sms($number, $sms_content);

                $msg = 'The order has been successfully activated, and realm account added!';
                $this->session->set_flashdata('success_message', $msg);
                if (isset($manage_flag)) {
                    if ($manage_flag == 'pending_order') {
                        redirect("/admin/pending_orders");
                    } elseif ($manage_flag == 'all_orders') {
                        redirect("/admin/all_orders");
                    } elseif ($manage_flag == 'user_orders') {
                        redirect("/admin/user_orders");
                    } elseif ($manage_flag == 'user_services') {
                        redirect("admin/user_service");
                    }
                } else {
                    redirect("admin/user_service");
                }
            } else {

                $error = $creation_result['message'];
                die($error);
            }
        }
    }

    // 	function lte_test() {
    // 	    $realmData = $this->realm_model->get_realm_data(17)["realm_settings"];
    // 	    $sess = $this->is_classes->is_connect_stage($realmData["user"], $realmData["pass"]);
    //         $res = $this->is_classes->queue_top_up_new($sess, "newtestuser@openwebmobile", "lte-top-3g");
    //         echo "<pre>"; var_dump($res); die;
    //     }
    /*
  MTN Fixed LTE functions-area-start
 */
    // mtn lte stats requests

    function mtn_stats_requests()
    {
        $this->db->select("orders.*,mtn_stat.*,membership.*,fibre_orders.*,mtn_recharge_requests.*");
        $this->db->from('mtn_stat');
        $this->db->join('orders', 'mtn_stat.mtn_user_code = orders.id', 'left');
        $this->db->join('membership', 'orders.id_user = membership.id', 'left');
        $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
        $this->db->join('mtn_recharge_requests', 'orders.id = mtn_recharge_requests.mtn_rec_order_id', 'left');
        $this->db->where('mtn_stat.mtn_status_temp_removed_status !=', 'TEMP REMOVED');
        $query = $this->db->get();
        $record = $query->result_array();
        //   echo"<pre>";
        //      print_r($query->result_array());exit();
        //      echo "</pre>";
        //view creator
        $data['main_content'] = 'admin/mtn_lte_stats_request';
        $data['sidebar'] = TRUE;
        $data['telkom_stat_request'] = $record;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    /*
  MTN Fixed LTE functions-area-end
 */

    function fibre_coverage_map()
    {
        //view creater
        $data['main_content'] = 'admin/fiber_coverage_map';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    //dev
    function is_lte_account_search()
    {

        //view creater
        $data['main_content'] = 'admin/lte_account_search';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    //telkom lte send stats request
    function send_telkom_request_stats()
    {
        $order_id = filter_input(INPUT_POST, 'order_code', FILTER_SANITIZE_STRING);
        $total_cap = filter_input(INPUT_POST, 'total_cap', FILTER_SANITIZE_STRING);
        $night_cap = filter_input(INPUT_POST, 'night_cap', FILTER_SANITIZE_STRING);
        $anytime_cap = filter_input(INPUT_POST, 'anytime_cap', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
        $order_type = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
        $simnumber = filter_input(INPUT_POST, 'simnumber', FILTER_SANITIZE_STRING);
        if ($order_type == 'telkom') {

            $stats_data = array(
                'telkom_total_cap' => $total_cap,
                'telkom_time_cap' => $anytime_cap,
                'telkom_night_cap' => $night_cap,
                'telkom_status' => 'MAILED',
                'admin_add_status_date' => date("l jS \of F Y h:i:s A")
            );
            $this->db->set($stats_data);
            $this->db->where('telkom_user_code', $order_id);
            $this->db->update('telkome_stat');

            $db_error = $this->db->_error_message();
        } elseif ($order_type == 'mtn') {

            $stats_data = array(
                'mtn_total_cap' => $total_cap,
                'mtn_time_cap' => $anytime_cap,
                'mtn_night_cap' => $night_cap,
                'mtn_status' => 'MAILED',
                'mtn_admin_add_status_date' => date("l jS \of F Y h:i:s A")
            );
            $this->db->set($stats_data);
            $this->db->where('mtn_user_code', $order_id);
            $this->db->update('mtn_stat');

            $db_error = $this->db->_error_message();
        }

        $content = '';
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $this->load->library('email');
            $email_model = $this->message_model->get_telkom_topup_stats_mail_template();

            $email_attachment_data = $this->db->where('email_template_id', $email_model['id']);
            $attac_query = $this->db->get('email_attachment');
            $attac_result = $attac_query->result_array();
            $content = $email_model['content'];
            $content = str_ireplace('[First Name]', $name, $content);
            $content = str_ireplace('[Last Name]', '', $content);
            $content = str_ireplace('[Username]', $name, $content);
            $content = str_ireplace('[Total Cap]', $total_cap . 'GB', $content);
            $content = str_ireplace('[Anytime Cap Used]', $anytime_cap . 'GB', $content);
            $content = str_ireplace('[Night Cap Used]', $night_cap . 'GB', $content);
            $content = str_ireplace('[SIM Serial Number]', $simnumber, $content);
            $content = str_ireplace('[Network]', $order_type, $content);
            $this->email->from($email_model['email_address'], 'OpenWeb Home');
            $this->email->to($email); //'jamtechtest420@gmail.com'
            $this->email->subject($email_model['title']);
            $this->email->message($content);
            if (!empty($attac_result)) {
                foreach ($attac_result as $att) {
                    $path = $att['path'];
                    $this->email->attach($path);
                }
            }
            $this->email->send();
            $this->email->clear(TRUE);
            $success = $order_type . " stats update and mailed Successfully.";
        }

        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    // telkom lte stats requests-900
    function telkom_lte_stats_requests()
    {
        $this->db->select("orders.*,telkome_stat.*,membership.*,fibre_orders.*,telkom_recharge_requests.*");
        $this->db->from('telkome_stat');
        $this->db->join('orders', 'telkome_stat.telkom_user_code = orders.id', 'left');
        $this->db->join('membership', 'orders.id_user = membership.id', 'left');
        $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
        $this->db->join('telkom_recharge_requests', 'orders.id = telkom_recharge_requests.rel_rec_order_id', 'left');
        $this->db->where('telkome_stat.telkom_status_temp_removed_status !=', 'TEMP REMOVED');
        // $this->db->where('telkome_stat.telkom_status','REQUESTED');
        //  $this->db->where('telkome_stat.telkom_status','RESETED');
        $query = $this->db->get();
        $record = $query->result_array();
        //view creator
        $data['main_content'] = 'admin/telkom_lte_stats_request';
        $data['sidebar'] = TRUE;
        $data['telkom_stat_request'] = $record;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    ///mobile state service
    function mobile_stats_requests()
    {
        $this->db->select("orders.*,mobile_stat.*,membership.*,fibre_orders.*,mob_recharge_requests.*");
        $this->db->from('mobile_stat');
        $this->db->join('orders', 'mobile_stat.mobile_user_code = orders.id', 'left');
        $this->db->join('membership', 'orders.id_user = membership.id', 'left');
        $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
        $this->db->join('mob_recharge_requests', 'orders.id = mob_recharge_requests.mob_rec_order_id', 'left');
        $this->db->where('mobile_stat.mobile_status_temp_removed_status !=', 'TEMP REMOVED');
        // $this->db->where('telkome_stat.telkom_status','REQUESTED');
        //  $this->db->where('telkome_stat.telkom_status','RESETED');
        $query = $this->db->get();
        $record = $query->result_array();

        //view creator
        $data['main_content'] = 'admin/mobile_stats_requests';
        $data['sidebar'] = TRUE;
        $data['mob_stat_request'] = $record;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    //telkom lte send stats request
    function send_mobile_request_stats()
    {

        extract($_POST);

        if ($order_type == 'mobile') {

            $stats_data = array(
                'mobile_total_minutes' => $minutes,
                'mobile_total_data' => $data,
                'mobile_total_sms' => $sms,
                'mobile_status' => 'MAILED',
                'admin_add_status_date' => date("l jS \of F Y h:i:s A")
            );
            $this->db->set($stats_data);
            $this->db->where('mobile_user_code', $order_id);
            $this->db->update('mobile_stat');

            $db_error = $this->db->_error_message();
        }

        $content = '';
        if (!empty($db_error)) {
            $success = "Server Error please try again later.";
        } else {
            $this->load->library('email');
            $email_model = $this->message_model->get_mobile_topup_stats_mail_template();


            $email_attachment_data = $this->db->where('email_template_id', $email_model['id']);
            $attac_query = $this->db->get('email_attachment');
            $attac_result = $attac_query->result_array();
            $content = $email_model['content'];
            $content = str_ireplace('[Username]', $user_name, $content);
            // $content = str_ireplace('[First Name]', $user_name, $content);
            // $content = str_ireplace('[Last Name]', '', $content);
            // $content = str_ireplace('[Username]', $user_name, $content);
            $content = str_ireplace('[Minutes Used]', $minutes . ' Min', $content);
            $content = str_ireplace('[GB Used]', $data . ' GB', $content);
            $content = str_ireplace('[SMS Used]', $sms . ' SMS', $content);
            $content = str_ireplace('[SIM Serial Number]', $simnumber, $content);
            $content = str_ireplace('[Network]', $order_type, $content);

            $this->email->from($email_model['email_address'], 'OpenWeb Home');
            $this->email->to($user_email); //'jamtechtest420@gmail.com'
            $this->email->subject($email_model['title']);
            $this->email->message($content);
            if (!empty($attac_result)) {
                foreach ($attac_result as $att) {
                    $path = $att['path'];
                    $this->email->attach($path);
                }
            }
            if ($this->email->send()) {
                $success = $order_type . " stats update and mailed Successfully.";
            } else {

                $success = $order_type . " stats update but mail not sent!";
            }
            $this->email->clear(TRUE);
        }



        $msg = array('msg' => $success);
        echo json_encode($msg);
    }

    function process_notification()
    {
        if (isset($_POST['noteType'])) {
            $content = $_POST['content'];
            $user = $_POST['username'];
            $type = $_POST['noteType'];
            $new_note = array(
                'user' => $user,
                'content' => $content,
                'type' => $type,
            );
            $this->db->insert('notifications', $new_note);

            $msg = 'You have successfully posted your notification';
            $this->session->set_flashdata('success_message', $msg);
        }
        redirect("/admin/send_notification");
    }

    function send_notification()
    {
        $users = $this->user_model->get_user_list();
        $user_list = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $key = $user['username'];
                $value = "{$user['first_name']} {$user['last_name']} ($key)";
                $user_list[$key] = $value;
            }
        }
        $data['user_list'] = $user_list;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $data['main_content'] = 'admin/accounts/send_notification';
        $data['sidebar'] = TRUE;



        // page category + title + link data
        $data['page_link'] = "/admin/send_notification";
        $data['page_category'] = $this->page_category['messages'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function process_assign_order()
    {

        // validate POST data according to the service;
        $service = $this->validation_model->process_post_field('service'); // GET POST DATA 
        $lte_type = $this->validation_model->process_post_field('lte_type'); // GET POST DATA 
        $available_services = array("adsl", "fibre-data", "fibre-line", "showmx-sub", "lte-a", "mobile");

        if (in_array($service, $available_services)) {
            $username = $_POST['username'];
            $full_name = $this->membership_model->get_user_name_nice($username);
            $id_user = $this->membership_model->get_user_id($username);


            if ($service == 'lte-a' || $service == 'fibre-line' || $service == 'mobile') {
                if ($lte_type == 'mtn' || $lte_type == 'telkom') {

                    extract($_POST);
                    $price = $_POST['price'];
                    //Product
                    $product_data = array(
                        'name' => $_POST['product_name_fd'],
                        'parent' => 'legacy',
                        'class' => 'nosvc',
                    );

                    // comment line 5/6/2022
                    $this->db->insert('products', $product_data);
                    $product_id = $this->db->insert_id();

                    $product_name = $_POST['product_name_fd'];
                    $order_status = $_POST['status'];
                    $avios_code = $_POST['avios_code'];
                    $comment = $full_name . '(Client)(R' . $price . ' - ' . $product_name . ')(DEBIT ORDER)';

                    if (isset($_POST['change_flag'])) {
                        $change_flag = $_POST['change_flag'];
                    } else {
                        $change_flag = 0;
                    }

                    if (isset($_POST['display_usage'])) {
                        $display_usage = $_POST['display_usage'];
                    } else {
                        $display_usage = 0;
                    }

                    if (isset($_POST['cancel_flage'])) {
                        $cancel_flage = $_POST['cancel_flage'];
                    } else {
                        $cancel_flage = 0;
                    }

                    $ac_username = strtok($_POST['username_la'], '@');

                    $ac_realm = substr($_POST['username_la'], strpos($_POST['username_la'], '@') + 1);

                    $order_data = array(
                        'user' => $_POST['username'],
                        'product' => $product_id,
                        'status' => $order_status,
                        'account_username' => $ac_username,
                        'realm' => $ac_realm,
                        'price' => $_POST['price'],
                        'pro_rata_extra' => $_POST['proRata'],
                        'account_comment' => $comment,
                        'change_flag' => $change_flag,
                        'display_usage' => $display_usage,
                        'type' => 'manual',
                        'cancel_flage' => $cancel_flage,
                        'id_user' => $id_user,
                        'billing_cycle' => $billing_cycle,
                        'service_type' => $service,
                        'avios_code' => $avios_code,
                        'order_lte_type' => $lte_type
                    );
                }

                // comment line 5/6/2022
                $order_id = $this->order_model->assign_order_ltea($order_data);


                $filber_data = array(
                    'user_id' => $id_user,
                    'username' => $_POST['username'],
                    'order_id' => $order_id,
                    'product_name' => $_POST['product_name_fd'],
                    'fibre_data_username' => $ac_username,
                    'fibre_type' => $service,
                    'lte_type' => $lte_type,
                );
                if ($lte_type == 'mtn' || $lte_type == 'telkom' && $service != 'fibre-line') {
                    $filber_data['sim_serial_no'] = $_POST['sim_serial_number'];
                } else if ($lte_type == 'rain' && $service != 'fibre-line') {
                    $filber_data['fibre_data_password'] = $_POST['password_la'];
                } else if ($service == 'fibre-line') {
                    $filber_data['number_fl'] = $_POST['number_fl'];
                }




                // comment line 5/6/2022
                $this->order_model->assign_order_ltea_fiber_data($filber_data);


                // send email and SMS to user
                // -----------------------------------------------------------------------------------
                if (isset($_POST['email_sms']) && ($_POST['email_sms'] == 1) && ($_POST['status'] == 'active')) {

                    $this->load->model('sms_model');
                    $activation_message_data = $this->membership_model->get_email_with_number($_POST['username']);




                    $this->sms_model->sms_activation_fibre_order($order_data, $activation_message_data['number'], $filber_data);

                    //$email_details = $this->user_model->get_full_email_detail('fibre_data_activation');
                    $this->order_model->email_activate_fibre_order($activation_message_data['email'], $service, $order_data, $filber_data);
                }

                // write to active log
                $this->membership_model->add_fibre_order_activity_log($order_data, $service);
                $msg = 'The order has been assigned successfully !';
                $this->session->set_flashdata('success_message', $msg);
            } else if (isset($_POST['username']) && ($service == 'adsl')) {
                $username = $_POST['username'];
                $full_name = $this->membership_model->get_user_name_nice($username);
                $id_user = $this->membership_model->get_user_id($username);
                $price = $_POST['price'];
                if (isset($_POST['change_flag'])) {
                    $change_flag = $_POST['change_flag'];
                } else {
                    $change_flag = 0;
                }

                if (isset($_POST['display_usage'])) {
                    $display_usage = $_POST['display_usage'];
                } else {
                    $display_usage = 0;
                }

                if (isset($_POST['cancel_flage'])) {
                    $cancel_flage = $_POST['cancel_flage'];
                } else {
                    $cancel_flage = 0;
                }

                if (isset($_POST['email_sms'])) {
                    $email_sms = $_POST['email_sms'];
                } else {
                    $email_sms = 0;
                }

                if (isset($_POST['write_to_log'])) {
                    $write_to_log = $_POST['write_to_log'];
                } else {
                    $write_to_log = 0;
                }

                //Product
                $product_data = array(
                    'name' => $_POST['product_name_fd'],
                    'parent' => 'legacy',
                    'class' => 'nosvc',
                );
                $this->db->insert('products', $product_data);
                $product_id = $this->db->insert_id();
                $product_name = $_POST['product_name_fd'];

                $order_status = $_POST['status'];
                $avios_code = $_POST['avios_code'];

                $comment = $full_name . '(Client)(R' . $price . ' - ' . $product_name . ')(DEBIT ORDER)';

                // get billing cycle by product
                //$billing_cycle = '';
                //$billing_cycle = $this->product_model->get_billing_cycle_by_id($product_id);
                $ac_username = strtok($_POST['account_username'], '@');

                $ac_realm = substr($_POST['account_username'], strpos($_POST['account_username'], '@') + 1);
                $order_data = array(
                    'user' => $_POST['username'],
                    'product' => $product_id,
                    'status' => $order_status,
                    'account_username' => $ac_username,
                    'account_password' => $_POST['account_password'],
                    'realm' => $ac_realm,
                    'price' => $_POST['price'],
                    'pro_rata_extra' => $_POST['proRata'],
                    'account_comment' => $comment,
                    'change_flag' => $change_flag,
                    'display_usage' => $display_usage,
                    'type' => 'manual',
                    'cancel_flage' => $cancel_flage,
                    'id_user' => $id_user,
                    //'billing_cycle' => $billing_cycle,
                    'avios_code' => $avios_code,
                    'order_lte_type' => $lte_type
                );
                // echo '<pre>';
                // print_r($order_data);
                // echo '</pre>';
                // die;
                $this->order_model->assign_order($order_data);

                // Email and SMS notification
                // ----------------------------------------------------------------------------------------------------------------------------------------
                $acc_username = $order_data['account_username'];
                $acc_password = $order_data['account_password'];


                $membership_mobile = $this->membership_model->get_user_mobile($username); // get mobile from account info
                $number = $this->membership_model->get_number($username); //get mobile number from billing
                $ac_email = $this->membership_model->get_email($username); //get email address from membership
                // $realm = $this->product_model->get_product_realm($product_id); //get realm from is_class
                $realm = $order_data['realm'];
                // $acc_realm_user = $acc_username . '@' . $realm;
                $acc_realm_user = $acc_username;
                $real_mobile_number = $membership_mobile;
                if (empty($real_mobile_number))
                    $real_mobile_number = $number;
                $sms_dump = '';
                if (($email_sms == 1) && ($order_status == 'active')) {

                    $this->load->model('sms_model');
                    $this->order_model->email_activation($ac_email, $product_name, $acc_username, $realm, $acc_password, $full_name);
                    //$this->order_model->set_activated($order_id);

                    if (!empty($real_mobile_number)) {
                        $data_array = [
                            'service_type' => 'adsl',
                            'user' => $username,
                            'prod_username' => $acc_realm_user,
                            'prod_pass' => $acc_password,
                            'product_name' => $product_name
                        ];

                        $sms_response = $this->sms_model->sms_activation_fibre_order($data_array, $real_mobile_number);

                        //#$this->order_model->send_sms($real_mobile_number, $sms_content);
                        $sms_dump = print_r($sms_response, true);
                    }

                    // $this->order_model->email_invoices_individual($username);
                }

                // Add to Active Log
                // -------------------------------------------------------------------------------------------------------------------------------------------
                if ($write_to_log) {
                    $product_name = $this->product_model->get_product_name($product_id);
                    $this->membership_model->add_order_activity_log($username, $product_id, $product_name, $_POST['price'], $_POST['proRata']);
                }

                $msg = 'The order has been assigned successfully !'; // . "<br/>" . $sms_dump;
                $this->session->set_flashdata('success_message', $msg);
            }
            redirect("/admin/assign_order");
        } else {
            $message_type = 'error_message';
            $msg = 'Invalid Service type';
            $this->session->set_flashdata($message_type, $msg);
            redirect("/admin/assign_order");
        }
    }

    function debug_sms()
    {

        die();
        /*
      $sms_content = 'test message';
      $response = $this->order_model->send_sms($number, $sms_content);
      var_dump($response);

      $this->load->model('sms_model');
      $sms_content = 'Test Message 2(home.openweb)';
      //$response = $this->order_model->send_sms($number, $sms_content);
      // $response = $this->sms_model->sendSms($number, 'Test Message(home.openweb)'); //Send SMS
     */
        //   $this->load->model('sms_model');
        //    $real_mobile_number = '0826987888';
        //                           0826987888
        //   $sms_content = "test sms (home.openweb)";
        //$sms_response = $this->sms_model->sendSms($real_mobile_number, $sms_content); //Send SMS
        //  echo "<pre>";
        //   print_r($sms_response);
        //   echo "</pre>";
    }

    function assign_order($id_user = null)
    {

        if ($id_user) {
            $username = $this->membership_model->get_user_name($id_user);
            $data['username'] = $username;
        }

        $realms_list = $this->realm_model->get_realm_list();
        $data['realm_list'] = $realms_list;


        //$product_list = $this->product_model->get_product_nice_list();
        $product_list = $this->product_model->get_product_nice_list_by_params(
            array(
                'select' => 'id, name',
                'fields' => array(
                    'parent !=' => 'legacy',
                    'status' => 'active',
                    'active' => '1',
                )
            )
        );

        $avios = $this->avios_logs->getRules();
        $avios_code = [null => null];
        foreach ($avios as $code) {
            $avios_code[$code['billing_code']] = $code['desc'];
        }

        $lte_types = [
            // "rain" => "RAIN",
            "telkom" => "Telkom",
            "mtn" => "MTN"
        ];

        $data['lte_types'] = $lte_types;
        $data['avios_code'] = $avios_code;
        $data['product_list'] = $product_list;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $error_msg = $this->session->flashdata('error_message');
        $data['messages']['error_message'] = $error_msg;

        $data['main_content'] = 'admin/accounts/assign_order';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/assign_order";
        $data['page_category'] = $this->page_category['orders'];
        $data['sidebar_category'] = 'orders';
        $data['page_title'] = $this->page_category['orders'][$data['page_link']];

        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function delete_order($order_id)
    {
        $manage_flag = $this->session->userdata('manage_flag');

        $username = $this->session->userdata('user_name');
        if (isset($_GET['confirm']) && $_GET['confirm'] == true) {
            $this->order_model->delete_order($order_id);

            $msg = 'The order has been successfully deleted!';
            $this->session->set_flashdata('success_message', $msg);
            if (isset($manage_flag)) {
                if ($manage_flag == 'pending_order') {
                    redirect("/admin/pending_orders");
                } elseif ($manage_flag == 'all_orders') {
                    redirect("/admin/all_orders");
                } elseif ($manage_flag == 'user_orders') {
                    redirect("/admin/user_orders");
                }
            } else {
                redirect("/admin/user_orders/" . $username);
            }
        } else {
            $data['main_content'] = 'admin/confirmation';
            $data['confirmation_type'] = 'delete';
            $data['order_id'] = $order_id;
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    function local_order_remove($order_id, $confirm = null)
    {
        $manage_flag = $this->session->userdata('manage_flag');

        $username = $this->session->userdata('user_name');

        if (isset($confirm) && $confirm) {
            $this->order_model->local_order_remove($order_id);

            $msg = 'The order has been successfully deleted!';
            $this->session->set_flashdata('success_message', $msg);

            redirect("/admin/user_service/");
        } else {
            $data['main_content'] = 'admin/local_remove_confirmation';
            $data['confirmation_type'] = 'delete';
            $data['order_id'] = $order_id;
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    function delete_user($account_id)
    {
        if (isset($_GET['confirm']) && $_GET['confirm'] == true) {
            $this->membership_model->delete_user($account_id);
            $success_msg = 'The user has been successfully deleted!';
            $this->session->set_flashdata('success_message', $success_msg);
            redirect("/admin/all_account");
        } else {
            $data['main_content'] = 'admin/confirmation';
            $data['confirmation_type'] = 'delete';
            $data['account_id'] = $account_id;
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    // function reseller_bulk_mail()
    // {

    //     $purpose = 'reseller_mail';
    //     $suc_msg = '';
    //     $error_msg = '';

    //     $email_detail = $this->user_model->get_email_detail($purpose);
    //     $template_id = $email_detail[0]['id'];

    //     $email_users = $this->user_model->get_bulk_reseller_users();

    //     if ($email_detail) {

    //         $data['email_detail'] = $email_detail;
    //         $email_attach_data = $this->user_model->get_email_attach($template_id);
    //         if ($email_attach_data) {
    //             $data['attach_data'] = $email_attach_data;
    //         } else {
    //             $data['attach_data'] = '';
    //         }

    //         if (isset($_POST['email_idx'])) {
    //             // JS POST request AJAX in other action 
    //             $result = $this->message_model->send_bulk_email($email_users, $email_detail[0], $email_attach_data);
    //             $suc_msg = 'Success';
    //         }
    //     } else {
    //         $data['email_detail'] = '';
    //     }

    //     $data['users'] = count($email_users);
    //     $data['user_email'] = $email_users;
    //     $data['current_purpose'] = $purpose;

    //     $data['success_message'] = $suc_msg;
    //     $data['error_message'] = $error_msg;
    //     $data['main_content'] = 'admin/email/reseller_bulk_email';
    //     $data['sidebar'] = TRUE;

    //     // page category + title + link data
    //     $data['page_link'] = "/admin/reseller_bulk_email";
    //     $data['page_category'] = $this->page_category['bulk_mail'];
    //     $data['page_title'] = $this->page_category['messages'][$data['page_link']];
    //     $data['sidebar_category'] = 'messages';
    //     $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    // }

    /*
    |--------------------------------------------------------------------------
    | New Neseller Bulk Function
    |--------------------------------------------------------------------------
    */

    function reseller_bulk_mail()
    {

        $purpose = 'reseller_mail';
        $suc_msg = '';
        $error_msg = '';

        $email_detail = $this->user_model->get_email_detail($purpose);
        $template_id = $email_detail[0]['id'];

        $email_users = $this->user_model->get_bulk_reseller_users();

        if ($email_detail) {

            $data['email_detail'] = $email_detail;
            $email_attach_data = $this->user_model->get_email_attach($template_id);
            if ($email_attach_data) {
                $data['attach_data'] = $email_attach_data;
            } else {
                $data['attach_data'] = '';
            }


            /*
            |--------------------------------------------------------------------------
            | New If Statement
            |--------------------------------------------------------------------------
            */
            if (isset($_POST['email_id'])) {


                ///first check the batche who is already active
                $check_status = $this->user_model->get_email_crons_table('reseller_bulk_mail');


                if ($check_status == 'inactive') {
                    $limit = $_POST['n_users'];
                    $offset = 0;

                    ///save to database
                    $post_data = array();
                    $post_data['total_users'] = count($email_users);
                    $email_users = $this->user_model->get_reseller_bulk_users_new($limit, $offset);

                    // echo '<pre>';
                    // print_r($this->db->last_query());
                    // echo '</pre>';
                    // die;
                    $post_data['offset'] = $offset + $limit;
                    $post_data['user_limit'] = $limit;
                    $post_data['time'] = $_POST['email_time'];
                    $post_data['cron_time'] = 0;
                    $post_data['total_sent_emails'] = $limit;
                    $post_data['email_type'] = 'reseller_bulk_mail';
                    $post_data['status'] = 'active';



                    $last_id = $this->user_model->add_batch($post_data);

                    ///set crons
                    if ($last_id) {
                        // JS POST request AJAX in other action 
                        $result = $this->message_model->send_bulk_email($email_users, $email_detail[0], $email_attach_data, $last_id);
                        $data = array('code' => 'success', 'message' => 'emails send');
                        echo json_encode($data);
                        die;
                    }
                } else {
                    $data = array('code' => 'warning', 'message' => 'Sorry, you have another batch that is active yet');
                    echo json_encode($data);
                    die;
                }
            }
        } else {
            $data['email_detail'] = '';
        }

        $data['users'] = count($email_users);
        $data['user_email'] = $email_users;
        $data['current_purpose'] = $purpose;

        $data['success_message'] = $suc_msg;
        $data['error_message'] = $error_msg;
        $data['main_content'] = 'admin/email/reseller_bulk_email';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/reseller_bulk_email";
        $data['page_category'] = $this->page_category['bulk_mail'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function bulk_mail()
    {

        $purpose = 'bulk';
        $suc_msg = '';
        $error_msg = '';


        $email_detail = $this->user_model->get_email_detail($purpose);
        $template_id = $email_detail[0]['id'];
        $email_users = $this->user_model->get_bulk_users();

        if ($email_detail) {

            $data['email_detail'] = $email_detail;

            $email_attach_data = $this->user_model->get_email_attach($template_id);
            if ($email_attach_data) {
                $data['attach_data'] = $email_attach_data;
            } else {
                $data['attach_data'] = '';
            }

            if (isset($_POST['email_id'])) {

                ///first check the batche who is already active
                $check_status = $this->user_model->get_email_crons_table('bulk_mail');
                if ($check_status == 'inactive') {
                    $limit = $_POST['n_users'];
                    $offset = 0;

                    ///save to database
                    $post_data = array();
                    $post_data['total_users'] = count($email_users);
                    $email_users = $this->user_model->get_bulk_users_new($limit, $offset);

                    $post_data['offset'] = $offset + $limit;
                    $post_data['user_limit'] = $limit;
                    $post_data['time'] = $_POST['email_time'];
                    $post_data['cron_time'] = 0;
                    $post_data['total_sent_emails'] = $limit;
                    $post_data['status'] = 'active';


                    $last_id = $this->user_model->add_batch($post_data);

                    ///set crons
                    if ($last_id) {
                        // JS POST request AJAX in other action 
                        $result = $this->message_model->send_bulk_email($email_users, $email_detail[0], $email_attach_data, $last_id);
                        $data = array('code' => 'success', 'message' => 'emails send');
                        echo json_encode($data);
                        die;
                    }
                } else {
                    $data = array('code' => 'warning', 'message' => 'Sorry, you have another batch that is active yet');
                    echo json_encode($data);
                    die;
                }
            }
        } else {
            $data['email_detail'] = '';
        }

        $data['users'] = count($email_users);
        $data['current_purpose'] = $purpose;

        $data['success_message'] = $suc_msg;
        $data['error_message'] = $error_msg;
        $data['main_content'] = 'admin/email/bulk_email';
        $data['sidebar'] = TRUE;



        // page category + title + link data
        $data['page_link'] = "/admin/bulk_mail";
        $data['page_category'] = $this->page_category['bulk_mail'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }



    ///client_bulk_email_dashboard
    function client_bulk_email_dashboard()
    {
        // page category + title + link data



        $data['main_content'] = 'admin/email/client_bulk_email_dashboard';
        $data['sidebar'] = TRUE;



        // page category + title + link data
        $data['page_link'] = "/admin/client_bulk_email_dashboard";
        $data['page_category'] = $this->page_category['bulk_mail'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';
        $data['batch_data'] = $this->user_model->batch_data('bulk_mail');
        ///get batch id
        if (isset($_GET['batch_id'])) {
            $data['batch_id'] = $_GET['batch_id'];
            $data['single_batch'] = $this->user_model->batch_data_by_id($_GET['batch_id']);
        }


        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }


    ///resller_email_dashboard
    function reseller_bulk_email_dashboard()
    {
        // page category + title + link data  
        $data['main_content'] = 'admin/email/reseller_bulk_email_dashboard';
        $data['sidebar'] = TRUE;



        // page category + title + link data
        $data['page_link'] = "/admin/reseller_bulk_email_dashboard";
        $data['page_category'] = $this->page_category['bulk_mail'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';
        $data['batch_data'] = $this->user_model->batch_data('reseller_bulk_mail');
        ///get batch id
        if (isset($_GET['batch_id'])) {
            $data['batch_id'] = $_GET['batch_id'];
            $data['single_batch'] = $this->user_model->batch_data_by_id($_GET['batch_id']);
        }

        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }



    function update_order()
    { //var_dump($_POST);die;
        $manage_flag = $this->session->userdata('manage_flag');

        if (isset($_POST['id'])) {
            $order_id = $_POST['id'];

            // $order_key = $this->order_model->order_key_with_realm();
            $order_deatil_data = $this->order_model->get_order_data($order_id);
            $curr_order_acc_password = $order_deatil_data['account_password'];

            $product_id = $_POST['product'];
            $product_setting = $this->product_model->get_product_data($product_id);
            $product_data = $product_setting['product_settings'];
            $parent = $product_data['parent'];

            if (isset($_POST['change_flag'])) {
                $change_flag = $_POST['change_flag'];
            } else {
                $change_flag = 0;
            }

            if (isset($_POST['display_usage'])) {
                $display_usage = $_POST['display_usage'];
            } else {
                $display_usage = 0;
            }

            if (isset($_POST['cancel_flage'])) {
                $cancel_flage = $_POST['cancel_flage'];
            } else {
                $cancel_flage = 0;
            }

            $username = $_POST['user'];
            $price = $_POST['price'];
            $product_name = $this->product_model->get_product_name($product_id);
            $full_name = $this->membership_model->get_user_name_nice($username);
            $comment = $full_name . "(Client)(R" . $price . " - " . $product_name . ")(DEBIT ORDER)";

            $realm = '';
            if (isset($_POST['realm']))
                $realm = $_POST['realm'];

            $order_data = array(
                'product' => $product_id,
                'status' => $_POST['status'],
                'price' => $price,
                'pro_rata_extra' => $_POST['pro_rata_extra'],
                'account_username' => trim($_POST['account_username']),
                'account_comment' => $comment,
                'realm' => $realm,
                'change_flag' => $change_flag,
                'display_usage' => $display_usage,
                'cancel_flage' => $cancel_flage,
                'user' => $username,
                'date' => $_POST['date'],
            );

            if (trim($_POST['account_password']) != $curr_order_acc_password) {
                $order_data['account_password'] = trim($_POST['account_password']);
            }

            $this->order_model->update_order($order_id, $order_data);

            $user_id = $order_deatil_data['id_user'];
            $service_type = $order_deatil_data['service_type'];

            $msg = 'This order has been successfully updated!';
            $response_message = array(
                'msg' => $msg,
                'flag' => 'success_message',
            );


            // fibre-data, fibre-line, LTE-A
            if (!empty($service_type) && ($service_type != 'adsl')) {

                // check fibre data + update fibre
                $fibre_update = $this->order_model->update_fibre_data($service_type, $order_id, $user_id);

                if ($fibre_update['result'] == false) {
                    $response_message = array(
                        'msg' => $fibre_update['message'],
                        'flag' => 'error_message',
                    );
                }
            }

            $this->session->set_flashdata($response_message['flag'], $response_message['msg']);

            if (isset($manage_flag)) {
                if ($manage_flag == 'pending_order') {
                    redirect("/admin/pending_orders");
                } elseif ($manage_flag == 'all_orders') {
                    redirect("/admin/all_orders");
                } elseif ($manage_flag == 'user_orders') {
                    redirect("/admin/user_orders");
                } elseif ($manage_flag = 'all_undef') {
                    redirect("/admin/all_undef_orders");
                }
            } else {
                redirect("/admin/manage_order/$order_id");
            }
        }
    }

    function manage_order($order_id = '')
    {
        $data['product_list'] = $this->product_model->get_product_list();
        $data['order_id'] = $order_id;
        $order_data = $this->order_model->get_order_data($order_id);
        // check fibre data for current order
        $fibre_data = $this->order_model->check_fibre_data($order_data['service_type'], $order_id, $order_data['id_user']);
        if (!empty($fibre_data))
            $order_data['fibre'] = $fibre_data;

        if (isset($order_data['user'])) {
            $user = $order_data['user'];
        } else {
            $user = '';
        }
        $user_name = $this->user_model->get_user_name($user);

        if (isset($order_data['product'])) {
            $product = $order_data['product'];
            $product_name = $this->product_model->get_product_name($product);
            $default_comment = $this->product_model->get_default_comment($product);
            $amount = $order_data['price'];
            $comment = $default_comment;
            $comment = str_replace('[product_name]', $product_name, $comment);
            $comment = str_replace('[Name_Surname]', $user_name, $comment);
            $comment = str_replace('[amount]', 'R' . $amount, $comment);
        }


        // get realm name
        $order_realm = $this->order_model->get_order_realm($order_id);
        $data['order_realm'] = $order_realm;
        $realms_list = $this->realm_model->get_realm_list();
        $data['realm_list'] = $realms_list;

        $data['user'] = $user;
        $data['user_name'] = $user_name;

        $data['order_data'] = $order_data;
        $data['sidebar'] = TRUE;
        //$data['order_key'] = $this->order_model->order_key();
        $data['order_key'] = $this->order_model->order_key_with_realm();


        $data['main_content'] = 'admin/accounts/manage_order';
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $this->load->view('admin/includes/template', $data);
    }

    function stats()
    {
        // Interesting Data
        $num_users = $this->stats_model->get_num_users();
        $last_logged = $this->stats_model->get_last_logged_in();
        $last_logged_name = $this->user_model->get_user_name($last_logged);

        $last_joined = $this->stats_model->get_last_joined();
        $last_joined_name = $this->user_model->get_user_name($last_joined);
        $data['num_users'] = $num_users;
        $data['sidebar'] = TRUE;
        $data['last_joined'] = "$last_joined_name ($last_joined)";
        $data['last_logged'] = "$last_logged_name ($last_logged)";
        $data['main_content'] = 'admin/stats';


        // page category + title + link data
        $data['page_title'] = "Usage Statistics";
        $data['sidebar_category'] = 'stats';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function dashboard()
    {
        $username = $this->site_data['username'];
        $suc_msg = $this->session->flashdata('success_message');
        $warn_msg = $this->session->flashdata('warn_message');
        $notifications = $this->user_model->get_notifications($username);

        $users = $this->user_model->get_user_list();
        $user_list = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $key = $user['username'];
                $value = "{$user['first_name']} {$user['last_name']} ($key)";
                $user_list[$key] = $value;
            }
        }
        $month = $date = date('M/Y', strtotime('+1 month'));
        $month_invs_log = $this->order_model->get_month_log($month);
        /* if($month_invs_log){
      $invoice_id = $this->order_model->get_invoiceby_log($month_invs_log['id']);
      $data['invoices_id'] = '';
      }else{
      $data['invoices_id'] = null;
      } */
        //echo '<pre>';print_r($month_invs_log);die;

        $data['month_invs_log'] = $month_invs_log;
        $data['user_list'] = $user_list;
        $data['messages']['success_message'] = $suc_msg;
        $data['messages']['warn_message'] = $warn_msg;
        $data['notifications'] = $notifications;
        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/dashboard';
        $data['sidebar_category'] = 'dashboard';
        $data['user_role'] =  $this->session->userdata('role');
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function send_invoices_individual()
    {
        $username = $_POST['user'];

        $other_log = array('Function_name' => 'send_invoices_individual', 'Url' => $this->uri->uri_string(), 'Send_invoice_to' => $username);
        button_log("Send Invoices", $this->session->userdata('username'), $this->session->userdata('role'), json_encode($other_log));


        if (isset($username)) {
            $this->order_model->email_invoice_inv($username);
            $suc_msg = "Successfully send the email to user.";
            $this->session->set_flashdata('success_message', $suc_msg);
        } else {
            $warn_msg = "Failed to send the email.";
            $this->session->set_flashdata('warn_message', $warn_msg);
        }
        redirect("admin/dashboard");
    }

    function validate_user()
    {

        if (empty($_POST['account_id'])) {
            if ((isset($_POST['email_address']) && trim($_POST['email_address']) != '') && (isset($_POST['username']) && trim($_POST['username']) != '')) {
                $role = $this->membership_model->validate_email(trim($_POST['email_address']));
                $user = $this->membership_model->validate_username(trim($_POST['username']));

                if ($role) {
                    $msg = 'Whoops, the email address seems already exists.';
                    $this->session->set_flashdata('error_message', $msg);
                    redirect("admin/create_account");
                }

                if ($user) {
                    $msg = 'Whoops, the username seems already exists.';
                    $this->session->set_flashdata('error_message', $msg);
                    redirect("admin/create_account");
                }

                $this->update_user(); //
            }
        } else {
            $account_id = $_POST['account_id'];
            $email = $this->membership_model->get_user_email($account_id);

            $avios_id = isset($_POST['avios_id']) ? $this->db->escape_str($_POST['avios_id']) : '';
            $avios_id = trim($avios_id);

            $br_a_id = isset($_POST['br_a_id']) ? $this->db->escape_str($_POST['br_a_id']) : '';
            $br_a_id = trim($br_a_id);

            $avios_settings = array(
                'avios_id' => $avios_id,
                'br_a_id' => $br_a_id
            );
            $result = $this->user_model->preValidateAvios($avios_settings);

            if ($result === "d") {
                $msg = "Dublicate Avios number";
                $this->session->set_flashdata('error_message', $msg);
                redirect("admin/edit_account/" . $account_id);
            }

            if ($result === false) {
                $msg = "Error while adding Avios number";
                $this->session->set_flashdata('error_message', $msg);
                redirect("admin/edit_account/" . $account_id);
            }

            if ($_POST['email_address'] == $email) {

                $this->update_user();
            } else {
                $result = $this->membership_model->validate_email($_POST['email_address']);
                if (!$result) {
                    $this->update_user();
                } else {
                    $msg = 'Whoops, the email address seems already exists.';
                    $this->session->set_flashdata('error_message', $msg);

                    redirect("/admin/edit_account/$account_id");
                }
            }
        }
    }

    function create_account()
    {

        $data['error_message'] = $this->session->flashdata('error_message');
        $data['main_content'] = 'admin/accounts/create_account';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_account";
        $data['page_category'] = $this->page_category['manage_users'];
        $data['page_title'] = $this->page_category['manage_users'][$data['page_link']];
        $data['sidebar_category'] = 'manage_users';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function edit_account($user_id = '')
    {
        $this->session->set_userdata(array("user_id" => $user_id));

        $username = $this->membership_model->get_user_name($user_id);
        $suc_msg = '';
        $warn_msg = '';
        $info_msg = '';
        $error_msg = $this->session->flashdata('error_message');
        $suc_msg = $this->session->flashdata('success_message');

        $data['user_data']['account_id'] = $user_id;
        $data['user_data']['edit_user'] = $username;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $admin_username = $this->site_data['username'];
        $admin_id = $this->membership_model->get_user_id($admin_username);
        $admin_limitation = $this->membership_model->get_admin_limitation_by_id($admin_id);
        $data['admin_limitation'] = $admin_limitation;
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


        $data['user_data']['all_users'] = $this->user_model->get_user_list();

        if (trim($username) != '') {
            //$user_data = $this->user_model->get_user_data($username);
            $user_data = $this->user_model->get_user_data_by_id($user_id);

            $data['user_data']['user_data'] = $user_data;
            if (empty($user_data['user_settings'])) {
                $warn_msg = 'There is no record of a user with that username!';
            } else {
                if (isset($user_data['user_settings']['first_name']) && isset($user_data['user_settings']['last_name'])) {
                    $f_n = $user_data['user_settings']['first_name'];
                    $l_n = $user_data['user_settings']['last_name'];
                    //$info_msg = "You are currently editing $f_n $l_n's data.";
                }
            }
            //	$billing_data = $this->user_model->get_billing_data($username);
            $billing_data = $this->user_model->get_billing_data_by_user_id($user_id);


            $data['user_data']['user_billing'] = $billing_data;
        } else {
            $data['user_data']['user_data'] = '';
        }

        $pass = $data['user_data']['user_data']['user_settings']['password'];
        $data['user_data']['user_data']['user_settings']['password'] = $this->crypto_model->decode($pass);

        $data['super_admin'] = false;
        if ($this->session->userdata('role') == 'super_admin') {
            $data['super_admin'] = true;
        }

        $data['messages']['success_message'] = $suc_msg;
        //$data['messages']['info_message'] = $info_msg;
        $data['messages']['warning_message'] = $warn_msg;
        $data['messages']['error_message'] = $error_msg;
        $data['main_content'] = 'admin/accounts/edit_account';
        $data['sidebar'] = TRUE;


        $this->load->view('admin/includes/template', $data);
    }

    //Not use
    function user_mobile_data()
    {

        // $this->session->set_userdata(array("manage_flag" => "user_services"));
        $account_id = $this->session->userdata('user_id');
        $username = $this->user_model->get_user_name_by_id($account_id);


        $this->load->model("user_docs_model");
        $data['base_url'] = base_url();


        $residence = $this->user_docs_model->get_file_full_data($account_id, 'residence');
        $passport = $this->user_docs_model->get_file_full_data($account_id, 'passport');

        $user_fields = $this->user_docs_model->get_address_data($account_id);

        // get log data
        $log_data = $this->user_docs_model->get_all_log_data($account_id);

        $data['log_data'] = $log_data;
        $data['user_id'] = $account_id;
        $data['residence'] = $residence;
        $data['passport'] = $passport;
        $data['fields'] = $user_fields;


        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/mobile_data_admin';
        $this->load->view('admin/includes/template', $data);
    }

    function update_user()
    {

        $new_user = 0;
        if (isset($_POST['account_id']) && trim($_POST['account_id']) != '') {
            $account_id = $_POST['account_id'];
            $username = $this->membership_model->get_user_name($account_id);
        }

        if (isset($_POST['username']) && trim($_POST['username']) != '') {
            $username = $_POST['username'];
        } else {
            $username = random_string('alnum', 6);
        }

        if (isset($_POST['discount']) && time($_POST['discount']) != '') {
            $discount = $_POST['discount'];
        } else {
            $discount = '5';
        }

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $full_name = "$first_name $last_name";
        $user_mobile = $_POST['mobile_number']; //mobie_number
        $user_mobile = trim($user_mobile);
        $email_address = $_POST['email_address'];


        $reason_post = $_POST['reason'];
        $adsl_number = $_POST['adsl_number'];

        $user_settings = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email_address' => $email_address,
            'role' => $_POST['role'],
            'username' => trim($username),
            'discount' => $discount,
            'status' => 'active',
            'mobile_number' => $user_mobile,
            'reason' => $reason_post,
            '2auth' => $_POST['2auth'],
            '2auth_type' => $_POST['2auth_type'],
            'daily_lte_usage' => $_POST['daily_lte_usage'],
        );

        if (isset($_POST['password'])) {
            $new_user = 1;
            $user_settings['password'] = $_POST['password']; //remove md5j
            $user_settings['password'] = $user_settings['password'];
            $user_settings['password'] = $this->crypto_model->encode($user_settings['password']);
        }

        //update membership
        $this->db->select('email_address');
        $this->db->from('membership');
        $this->db->where('username', $username);
        $result = $this->db->get();
        if ($result->num_rows == 1) {
            $this->db->where('id', $account_id);
            $this->db->update('membership', $user_settings);

            $reseller = $this->db->get_where('resellers', ['user_id' => $account_id])->result_array();

            if ($user_settings['role'] == 'reseller') {
                if (count($reseller) <= 0) {
                    $this->db->insert('resellers', ['user_id' => $account_id, 'active' => 1]);
                } else {
                    $this->db->where('user_id', $account_id);
                    $this->db->update('resellers', ['active' => 1]);
                }
            } else {
                if (count($reseller) > 0) {
                    $this->db->where('user_id', $account_id);
                    $this->db->update('resellers', ['active' => 0]);
                }
            }
        } else {
            $this->db->insert('membership', $user_settings);
            $account_id = $this->db->insert_id();

            $ow_id = $this->membership_model->create_OW($account_id);

            $format_ow = "OW" . $ow_id;
            $this->db->where('id', $account_id);
            $this->db->update('membership', array('ow' => $format_ow));

            $this->user_model->email_active_account($account_id);
            $sms_content = 'Your account has been successfully created. See email for more details. - OpenWeb';
            $this->order_model->send_sms($user_mobile, $sms_content);

            if ($user_settings['role'] == 'reseller') {
                $this->db->insert('resellers', ['user_id' => $account_id, 'active' => 1]);
            }
        }

        $email_address_billing = isset($_POST['email']) ? $_POST['email'] : '';
        $contact_number_billing = isset($_POST['contact_number']) ? $_POST['contact_number'] : '';
        $sa_id_number = isset($_POST['sa_id_number']) ? $_POST['sa_id_number'] : '';


        //update billing
        $billing_settings = array(
            'username' => $username,
            'billing_name' => isset($_POST['billing_name']) ? $_POST['billing_name'] : '',
            'address_1' => isset($_POST['address_1']) ? $_POST['address_1'] : '',
            'address_2' => isset($_POST['address_2']) ? $_POST['address_2'] : '',
            'city' => isset($_POST['city']) ? $_POST['city'] : '',
            'province' => isset($_POST['province']) ? $_POST['province'] : '',
            'postal_code' => isset($_POST['postal_code']) ? $_POST['postal_code'] : '',
            'country' => isset($_POST['country']) ? $_POST['country'] : '',
            'email' => $email_address_billing,
            'contact_number' => $contact_number_billing,
            'id_user' => $account_id,
            'sa_id_number' => $sa_id_number,
            'adsl_number' => $adsl_number,
        );

        $billing_settings['name_on_card'] = $_POST['name_on_card'] ? $_POST['name_on_card'] : null;
        $billing_settings['card_num'] = $_POST['card_num'] ? $_POST['card_num'] : null;
        $billing_settings['expires_month'] = $_POST['expires_month'] ? $_POST['expires_month'] : null;
        $billing_settings['expires_year'] = $_POST['expires_year'] ? $_POST['expires_year'] : null;
        $billing_settings['cvc'] = $_POST['cvc'] ? $_POST['cvc'] : null;
        $billing_settings['bank_name'] = $_POST['bank_name'] ? $_POST['bank_name'] : null;
        $billing_settings['bank_account_number'] = $_POST['bank_account_number'] ? $_POST['bank_account_number'] : null;
        $billing_settings['bank_account_type'] = $_POST['bank_account_type'] ? $_POST['bank_account_type'] : null;
        $billing_settings['bank_branch_code'] = $_POST['bank_branch_code'] ? $_POST['bank_branch_code'] : null;
        // First check if record exists
        $this->db->select('email');
        $this->db->from('billing');
        $this->db->where('username', $username);
        $result = $this->db->get();

        //add user's billing info
        if ($result->num_rows == 1) {
            $this->db->where('username', $username);
            $this->db->update('billing', $billing_settings);
        } else {
            $this->db->insert('billing', $billing_settings);
        }
        if ($new_user) {
            $msg = "$full_name's account has been created successfully!";
        } else {
            $msg = "$full_name's data have been updated successfully!";
        }

        //Avios Data
        $avios_id = isset($_POST['avios_id']) ? $_POST['avios_id'] : '';
        $avios_id = trim($avios_id);

        $br_a_id = isset($_POST['br_a_id']) ? $_POST['br_a_id'] : '';
        $br_a_id = trim($br_a_id);

        $avios_settings = array(
            'avios_id' => $avios_id,
            'br_a_id' => $br_a_id
        );

        $result = $this->user_model->addAviosIds($avios_settings, $account_id);

        if ($result != false) {
            $msg .= " Avios data added";
        }

        $this->session->set_flashdata('success_message', $msg);
        redirect("/admin/all_account");
    }

    function update_billing()
    {
        $billing_settings = array(
            'username' => trim($username),
            'account_type' => $_POST['account_type'],
            'account_num' => $_POST['account_num'],
            'expires_month' => $_POST['expires_month'],
            'expires_year' => $_POST['expires_year'],
            'billing_name' => $_POST['billing_name'],
            'address_1' => $_POST['address_1'],
            'address_2' => $_POST['address_2'],
            'city' => $_POST['city'],
            'province' => $_POST['province'],
            'postal_code' => $_POST['postal_code'],
            'country' => $_POST['country'],
            'email' => $_POST['email'],
            'contact_number' => $_POST['contact_number'],
            'cvv' => $_POST['cvv'],
            'bank_name' => $_POST['bank_name'],
            'bank_account_number' => $_POST['bank_account_number'],
            'bank_account_type' => $_POST['bank_account_type'],
            'bank_branch_code' => $_POST['bank_branch_code'],
            'mobile' => $_POST['mobile'],
            //'vat_number' => $_POST['vat_number'],
            'vat_number' => $vat_number,
        );
        // First check if record exists

        $this->db->select('account_type');
        $this->db->from('billing');
        $this->db->where('username', $username);
        $result = $this->db->get();

        //add user's cc info
        if ($result->num_rows == 1) {
            $this->db->where('username', $username);
            $this->db->update('billing', $billing_settings);
        } else {
            $this->db->insert('billing', $billing_settings);
        }
    }

    function all_account($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;
        $accounts = $this->membership_model->get_all_account($num_per_page, $start);
        // echo '<pre>';
        // print_r($accounts);
        // echo '</pre>';
        // die;
        $role_list = $this->membership_model->get_role_data();
        $sataus_list = $this->membership_model->get_status_data();

        $data['account_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_account');
        $num_account = $this->membership_model->get_account_count();
        $config['total_rows'] = $num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a style='text-decoration: underline; font-weight: bold;' href='#'>";
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

        $account_data = $accounts;

        $account_count = count($accounts);
        $account_ind = $start + $account_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$account_ind of $num_account";
        $data['num_per_page'] = $num_per_page;
        $data['num_account'] = $num_account;
        $data['accounts'] = $account_data;
        $data['role_list'] = $role_list;
        $data['status_list'] = $sataus_list;
        $data['current_start_param'] = $start;

        $data['main_content'] = 'admin/accounts/all_account';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $error_msg = $this->session->flashdata('error_message');
        $data['messages']['success_message'] = $suc_msg;
        $data['messages']['error_message'] = $error_msg;



        // page category + title + link data
        $data['page_link'] = "/admin/all_account";
        $data['page_category'] = $this->page_category['manage_users'];
        $data['page_title'] = $this->page_category['manage_users'][$data['page_link']];
        $data['sidebar_category'] = 'manage_users';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function validate_realm_name()
    {
        $realm_id = isset($_POST['realm_id']) ? $_POST['realm_id'] : '';

        if ($realm_id != '') {
            $realm_name = $this->realm_model->get_realm_name($realm_id);

            if ($_POST['name'] == $realm_name) {
                echo "true";
            } else {
                $result = $this->realm_model->validate_realm_name($_POST['name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        } else {
            if (isset($_POST['name']) && trim($_POST['name']) != '') {
                $result = $this->realm_model->validate_realm_name($_POST['name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        }
    }

    function validate_email()
    {
        $account_id = isset($_POST['account_id']) ? $_POST['account_id'] : '';

        if ($account_id != '') {
            $account_id = trim($_POST['account_id']);
            $email = $this->membership_model->get_user_email($account_id);
            if ($_POST['email_address'] == $email) {
                echo "true";
            } else {
                $result = $this->membership_model->validate_email($_POST['email_address']);
                if (!$result) {
                    echo "true";
                } else {
                    echo "false";
                }
            }
        } else {
            if (isset($_POST['email_address']) && trim($_POST['email_address']) != '') {
                $result = $this->membership_model->validate_email($_POST['email_address']);
                if ($result) {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        }
    }

    function validate_username()
    {
        if (isset($_POST['username']) && trim($_POST['username']) != '') {
            $result = $this->membership_model->validate_username(trim($_POST['username']));
            if ($result) {
                echo "false";
            } else {
                echo "true";
            }
        }
    }

    /* 	function validate_class_name(){
  $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
  //print_r($_POST['name']);die();
  if($class_id != ''){
  $class_name = $this->product_model->get_class_name($class_id);

  if($_POST['name'] == $class_name){
  echo "true";
  }else{
  $result = $this->product_model->validate_class_name($_POST['name']);
  if($result == '1'){
  echo "false";
  }else{
  echo "true";
  }
  }
  }else{
  if (isset($_POST['name'])  && tirm($_POST['name']) != ''){
  $result = $this->product_model->validate_class_name($_POST['name']);
  if($result == '1'){
  echo "false";
  }else{
  echo "true";
  }
  }
  }
  } */

    function validate_category_name()
    {
        $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
        if ($category_id != '') {
            $category_name = $this->category_model->get_category_name($category_id);

            if ($_POST['category_name'] == $category_name) {
                echo "true";
            } else {
                $result = $this->category_model->validate_category_name($_POST['category_name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        } else {
            if (isset($_POST['category_name']) && trim($_POST['category_name']) != '') {
                $result = $this->category_model->validate_category_name($_POST['category_name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        }
    }

    function validate_subcategory_name()
    {
        $subcategory_id = isset($_POST['subcategory_id']) ? $_POST['subcategory_id'] : '';
        if ($subcategory_id != '') {
            $subcategory_name = $this->category_model->get_subcategory_name($subcategory_id);

            if ($_POST['subcategory_name'] == $subcategory_name) {
                echo "true";
            } else {
                $result = $this->category_model->validate_subcategory_name($_POST['subcategory_name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        } else {
            if (isset($_POST['subcategory_name']) && trim($_POST['subcategory_name']) != '') {
                $result = $this->category_model->validate_subcategory_name($_POST['subcategory_name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        }
    }

    function validate_product_name()
    {
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

        if ($product_id != '') {
            $product_name = $this->product_model->get_product_name($product_id);

            if ($_POST['product_name'] == $product_name) {
                echo "true";
            } else {
                $result = $this->product_model->validate_product_name($_POST['product_name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        } else {
            if (isset($_POST['product_name']) && trim($_POST['product_name']) != '') {
                $result = $this->product_model->validate_product_name($_POST['product_name']);
                if ($result == '1') {

                    echo "false";
                } else {
                    echo "true";
                }
            }
        }
    }

    // allow to create product with same names
    function validate_product_name2()
    {
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

        if ($product_id != '') {
            $product_name = $this->product_model->get_product_name($product_id);

            if ($_POST['product_name'] == $product_name) {
                echo "true";
            } else {
                $result = $this->product_model->validate_product_name($_POST['product_name']);
                if ($result == '1') {
                    echo "false";
                } else {
                    echo "true";
                }
            }
        } else {

            echo "true";
            return;
            /*
          if(isset($_POST['product_name']) && trim($_POST['product_name']) != ''){
          $result = $this->product_model->validate_product_name($_POST['product_name']);
          if($result == '1'){

          echo "false";
          }else{
          echo "true";
          }
          }
         */
        }
    }

    function update_realm()
    {
        if (isset($_POST['realm_id'])) {
            $realm_id = $_POST['realm_id'];
        } else {
            $realm_id = '';
        }
        $realm_name = $_POST['name'];
        $user_name = $_POST['user'];
        $pwd = md5($_POST['pass']);

        $realm_fields = $this->realm_model->get_realm_fields();
        $realm_setings = array('realm' => $realm_name, 'user' => $user_name, 'pass' => $pwd);

        if ($realm_id) {
            $this->db->where('id', $realm_id);
            $result = $this->db->update('realms', $realm_setings);

            $msg = 'This realm has been successfully updated!';
            $this->session->set_flashdata('success_message', $msg);
            redirect("/admin/all_realms");
        } else {
            $msg = 'Failed to update this realm.';
            $this->session->set_flashdata('error_message', $msg);
            redirect("/admin/edit_realm/$realm_id");
        }
    }

    function add_realm()
    {
        $realm_name = $_POST['name'];
        $user_name = $_POST['user'];
        $pwd = md5($_POST['pass']);

        $realm_fields = $this->realm_model->get_realm_fields();

        $realm_setings = array('realm' => $realm_name, 'user' => $user_name, 'pass' => $pwd);

        $this->db->insert('realms', $realm_setings);
        $realm_id = $this->db->insert_id();

        if ($realm_id) {
            $msg = 'The realm has been successfully created!';
            $this->session->set_flashdata('success_message', $msg);
            redirect("/admin/all_realms");
        } else {
            $msg = 'Failed to insert a new realm.Please try it again.';
            $this->session->set_flashdata('error_message', $msg);
            redirect("/admin/create_realm");
        }
    }

    function manage_categories()
    {
        $data['main_content'] = 'admin/categories/manage_categories';
        $data['sidebar'] = TRUE;
        $data['category_data'] = array();
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function manage_subcategories()
    {
        $data['main_content'] = 'admin/subcategories/manage_categories';
        $data['sidebar'] = TRUE;
        $data['subcategory_data'] = array();
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function create_subcategory()
    {
        $data['subcategory_data']['subcategory_fields'] = $this->category_model->get_subcategory_fields();
        // It also needs all parents for selecting parent.
        $data['subcategory_data']['all_categories'] = $this->category_model->get_categories();
        $data['all_categories_reseller'] = $this->category_model->get_categories_reseller();
        $data['main_content'] = 'admin/subcategories/create_category';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_subcategory";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function select_subcategory()
    {
        if (isset($_POST['subcategory'])) {
            $cat_slug = $_POST['subcategory'];
        } else {
            $cat_slug = '';
        }

        redirect("/admin/edit_subcategory/$cat_slug");
    }

    function create_category()
    {
        $data['category_data']['category_fields'] = $this->category_model->get_category_fields();
        $data['main_content'] = 'admin/categories/create_category';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_category";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function select_category()
    {
        if (isset($_POST['category'])) {
            $cat_id = $_POST['category'];
        } else {
            $cat_id = '';
        }

        redirect("/admin/edit_category/$cat_id");
    }

    function select_realm()
    {
        if (isset($_POST['realm'])) {
            $realm_name = $_POST['realm'];
        } else {
            $realm_name = '';
        }

        redirect("/admin/edit_realm/$realm_name");
    }

    function update_category()
    {
        if (isset($_POST['id'])) {
            $category_id = $_POST['id'];
        } else {
            $category_id = '';
        }
        $cat_slug = $_POST['slug'];
        $category_fields = $this->category_model->get_category_fields();
        $category_setings = array();
        foreach ($category_fields as $f => $n) {
            // field => name
            if (isset($_POST[$f])) {
                $category_setings[$f] = $_POST[$f];
            } else {
                $category_setings = '';
            }
        }

        $table = 'product_categories';
        $redirect = 'all_category';
        if ($category_setings['type'] == 'reseller') {
            $table = 'product_categories_reseller';
            $redirect = 'all_category_reseller';
        }

        unset($category_setings['type']);

        if (trim($category_id) != '') {
            //var_dump($category_id);die();
            $this->db->where('id', $category_id);
            $this->db->update($table, $category_setings);
            $msg = 'This category has been successfully updated!';
        } else {
            $this->db->insert($table, $category_setings);
            $category_id = $this->db->insert_id();
            $msg = 'This category has been successfully created!';
        }
        $this->session->set_flashdata('success_message', $msg);
        //redirect("/admin/edit_category/$cat_slug");
        redirect("/admin/" . $redirect);
    }

    function update_subcategory()
    {
        if (isset($_POST['id'])) {
            $category_id = $_POST['id'];
        } else {
            $category_id = '';
        }

        $category_fields = $this->category_model->get_subcategory_fields();
        $category_setings = array();
        foreach ($category_fields as $f => $n) {
            // field => name
            if (isset($_POST[$f])) {
                $category_setings[$f] = $_POST[$f];
            } else {
                $category_setings = '';
            }
        }
        $category_slug = $category_setings['slug'];

        $table = 'product_subcategories';
        $redirect = 'all_subcategory';
        if ($category_setings['type'] == 'reseller') {
            $table = 'product_subcategories_reseller';
            $redirect = 'all_subcategory_reseller';
            $category_setings['parent'] = $_POST['parent_r'];
        }

        unset($category_setings['type']);

        if (trim($category_id) != '') {
            $this->db->where('id', $category_id);
            $this->db->update($table, $category_setings);

            $msg = 'This sub-category has been successfully updated!';
        } else {
            $this->db->insert($table, $category_setings);
            $category_id = $this->db->insert_id();

            $msg = 'This sub-category has been successfully created!';
        }
        $this->session->set_flashdata('success_message', $msg);
        redirect("/admin/" . $redirect);
    }

    function create_product()
    {
        $billing_cycles = $this->product_model->get_billing_cycles();
        $data['pro_rata_options'] = $this->product_model->get_pro_rata_options();
        $data['product_data']['billing_cycles'] = $billing_cycles;
        $fields = $this->product_model->get_product_fields();
        $fields['type'] = 'Type';
        $data['product_data']['product_fields'] = $fields; //echo'<pre>'; var_dump($data['product_data']['product_fields']);die;
        $data['product_data']['is_classes'] = $this->product_model->get_classes();
        $data['product_data']['potential_parents'] = $this->product_model->get_product_categories();
        $data['categories_res'] = $this->product_model->get_product_categories_reseller();
        $data['main_content'] = 'admin/products/create_product';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_product";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function select_user()
    {
        if (isset($_POST['user'])) {
            $user_id = $_POST['user'];
        } else {
            $user_id = '';
        }
        redirect("/admin/edit_account/$user_id");
    }

    function filter_user($start = 0)
    {
        $role_list = $this->membership_model->get_role_data();
        $sataus_list = $this->membership_model->get_status_data();

        if (isset($_POST['role'])) {
            $role = $_POST['role'];
        } else {
            $role_ss = $this->session->userdata('current_role');
            if ($role_ss) {
                $role = $role_ss;
            }
        }
        if (isset($_POST['status'])) {
            $status = $_POST['status'];
        } else {
            $status_ss = $this->session->userdata('current_status');
            if ($status_ss) {
                $status = $status_ss;
            }
        }

        $num_per_page = NUM_PER_PAGE;
        $account_data = array();

        if ($role == 'all' && $status == 'all') {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_role', $role);
            $accounts = $this->membership_model->get_all_account($num_per_page, $start);
            $num_account = $this->membership_model->get_account_count();
            $data['role'] = $role;
            $data['status'] = $status;
        } elseif ($role != 'all' && $status == 'all') {
            $this->session->set_userdata('current_role', $role);
            $this->session->set_userdata('current_status', $status);
            $result = $this->membership_model->search_by_role($role, $num_per_page, $start);
            if ($result) {
                $accounts = $result;
                $num_account = $this->membership_model->get_role_count($role);
                $data['role'] = $role;
                $data['status'] = $status;
            } else {
                $accounts = '';
                $num_account = 0;
                $msg = "No data";
            }
        } elseif ($status != 'all' && $role == 'all') {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_role', $role);
            $result = $this->membership_model->search_by_status($status, $num_per_page, $start);
            if ($result) {
                $accounts = $result;
                $num_account = $this->membership_model->get_stauts_count($status);
                $data['status'] = $status;
                $data['role'] = $role;
            } else {
                $accounts = '';
                $num_account = 0;
                $msg = "No data";
            }
        } else {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_role', $role);
            $result = $this->membership_model->search_by_role_status($role, $status, $num_per_page, $start);
            if ($result) {
                $accounts = $result;
                $num_account = $this->membership_model->get_role_status_count($role, $status);

                $data['role'] = $role;
                $data['status'] = $status;
            } else {
                $accounts = '';
                $num_account = 0;
                $msg = "No data";
            }
        }
        $account_data = $accounts;

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/filter_user');
        $config['total_rows'] = $num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination ">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $account_count = count($accounts);
        $account_ind = $start + $account_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$account_ind of $num_account";
        $data['num_per_page'] = $num_per_page;
        $data['num_account'] = $num_account;
        $data['accounts'] = $account_data;
        $data['role_list'] = $role_list;
        $data['status_list'] = $sataus_list;
        $data['main_content'] = 'admin/accounts/all_account';
        $data['sidebar'] = TRUE;



        // page category + title + link data
        $data['page_link'] = "/admin/all_account";
        $data['page_category'] = $this->page_category['manage_users'];
        $data['page_title'] = $this->page_category['manage_users'][$data['page_link']];
        $data['sidebar_category'] = 'manage_users';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function search_for_user($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;
        $role_list = $this->membership_model->get_role_data();
        $sataus_list = $this->membership_model->get_status_data();

        $start = $this->product_model->process_get_product_request('page');
        if (empty($start) || ($start < 0))
            $start = 0;

        //$this->product_model->process_get_product_request($field)
        // parse Post request

        $search_keys = array(
            'user_name' => '',
            'first_name' => '',
            'last_name' => '',
            'email_address' => '',
            'ow_num' => '',
            'user_id' => '',
            'sa_id_number' => '',
            'avios' => '',
        );

        $search_array = array();
        foreach ($search_keys as $k => $v) {
            // GET request
            $get_val = trim($this->product_model->process_get_product_request($k));
            if (!empty($get_val)) {
                $search_array[$k] = $get_val;
            }
        }

        $num_account = 0;
        if (empty($search_array)) {

            $account_data = $this->membership_model->get_all_account($num_per_page, $start);

            $num_account = $this->membership_model->get_account_count();
        } else {

            // priority : sa_id, user_id, ow_num
            $account_data = $this->membership_model->search_users_by_array_of_params($search_array, $num_per_page, $start);

            $num_account = $this->membership_model->get_account_total_count_by_array_of_params($search_array);
            $priority_flag_array = $this->membership_model->return_priority_flag_for_user_search($search_array);
        }

        //$num_account = count($account_data); // WRONG

        $this->load->library('pagination');

        $base_url_for_paginator = base_url('index.php/admin/search_for_user?');
        $base_url_for_paginator = $this->product_model->handle_base_url_for_paginator($search_array, $base_url_for_paginator);

        $config['base_url'] = $base_url_for_paginator;

        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $config['total_rows'] = $num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li><a style='text-decoration: underline; font-weight: bold;' href='#'>";
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

        $data['priority_flag'] = $priority_flag_array;
        $data['search_array'] = $search_array;
        $account_ind = $start + $num_account;
        $data['showing'] = "Showing $start-$account_ind of $num_account";
        $data['num_per_page'] = $num_per_page;
        $data['num_account'] = $num_account;
        $data['accounts'] = $account_data;
        $data['role_list'] = $role_list;
        $data['status_list'] = $sataus_list;
        $data['current_start_param'] = $start;
        $data['main_content'] = 'admin/accounts/all_account';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/all_account";
        $data['page_category'] = $this->page_category['manage_users'];
        $data['page_title'] = $this->page_category['manage_users'][$data['page_link']];
        $data['sidebar_category'] = 'manage_users';

        //Search for avios page
        if (isset($search_array['avios'])) {
            $data['billing_codes'] = $this->avios_main->billingCodes;
            $data['main_content'] = 'admin/avios/avios_award_form';
            $data['page_link'] = "/admin/award_user_form";
            $data['page_category'] = $this->page_category['avios_bonus'];
            $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
            $data['sidebar_category'] = 'award_user_form';
        }


        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function view_product($product_id, $type = '')
    {
        $data['product_data']['product_id'] = $product_id;

        if (trim($product_id) != '') {
            $data['product_data']['product_data'] = $this->product_model->get_product_data($product_id, $type);
            $data['product_data']['payment_methods'] = $this->product_model->get_payment_methods($product_id);
            $data['product_data']['billing_cycle'] = $this->product_model->get_billing_cycle_exist($product_id);
        } else {
            $data['product_data']['product_data'] = '';
            $data['product_data']['payment_methods'] = '';
            $data['product_data']['billing_cycle'] = '';
        }

        $data['main_content'] = 'admin/products/view_product';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function select_product()
    {
        if (isset($_POST['product'])) {
            $product_id = $_POST['product'];
        } else {
            $product_id = '';
        }

        redirect("/admin/edit_product/$product_id");
    }

    function update_product()
    {
        if (!isset($this->port_model))
            $this->load->model('port_model');

        // check ID
        if (isset($_POST['id'])) {
            $product_id = $_POST['id'];
        } else {
            $product_id = '';
        }

        // get product fields
        $product_fields = $this->product_model->get_product_fields();

        // fill product fields
        $product_setings = [];
        foreach ($product_fields as $f => $n) {
            if (isset($_POST[$f])) {

                $product_setings[$f] = $_POST[$f];
                if ($f == 'type' | $f == 'billing_cycle') {
                    if ($_POST[$f] == 'daily' | isset($_POST[$f]['daily']))
                        $product_setings['billing_cycle'] = 'daily';
                    else {
                        //$product_setings['billing_cycle'] = '';
                        $product_setings[$f] = '';
                    }
                }
            } else {
                $product_setings[$f] = '';
            }
        }

        if ($_POST['type'] == 'reseller') {
            $product_setings['parent'] = $_POST['parent_r'];
        }


        // fill class
        if (isset($_POST['class'])) {
            $class_list = $_POST['class'];
            $class = $_POST['class'][0];
            $product_setings['class_id'] = $class;
        }


        // get class name by id
        $class_name = $this->product_model->get_class_name($class);
        $product_setings['class'] = $class_name;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // add TopUp params
        // topup_option -> topup_active
        // topup_list -> topup_id

        $topup_active = $this->input->post('topup_option', true);
        $topup_active = strip_tags(mysql_real_escape_string($topup_active));
        $topup_active = trim($topup_active);
        $product_setings['topup_active'] = $topup_active;

        $topup_id = $this->input->post('topup_list', true);
        $topup_id = strip_tags(mysql_real_escape_string($topup_id));
        $topup_id = trim($topup_id);
        $product_setings['topup_id'] = $topup_id;

        // 2nd TopUp
        $topup_id2 = $this->input->post('topup_list2', true);
        $topup_id2 = strip_tags(mysql_real_escape_string($topup_id2));
        $topup_id2 = trim($topup_id2);
        $product_setings['topup_id2'] = $topup_id2;

        // 3rd TopUp
        $topup_id3 = $this->input->post('topup_list3', true);
        $topup_id3 = strip_tags(mysql_real_escape_string($topup_id3));
        $topup_id3 = trim($topup_id3);
        $product_setings['topup_id3'] = $topup_id3;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $mobile_data_checkbox = $this->product_model->process_product_request('mobile_data_checkbox');
        $mobile_data_input = $this->product_model->process_product_request('mobile_data_input');
        $mobile_data_dropdown = $this->product_model->process_product_request('mobile_data_dropdown');

        if ($mobile_data_checkbox == 'on') {
            $mobile_data_checkbox = '1';
        } else {
            $mobile_data_checkbox = '0';
        }

        $product_setings['mobile_data_enabled'] = $mobile_data_checkbox;
        $product_setings['mobile_data_amount'] = 0;
        $product_setings['mobile_data_type'] = $mobile_data_dropdown;


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $post_activate = $this->validation_model->process_post_field('port_activate');
        $product_setings['port_active'] = 0;
        if (isset($post_activate) && !empty($post_activate))
            $product_setings['port_active'] = 1;


        //$product_setings['port_service_id'] = $this->validation_model->process_post_field('port_class');
        $product_setings['port_service_id'] = 1;
        $product_setings['port_duration'] = $this->validation_model->process_post_field('port_duration');
        //$product_setings['port_counter'] = $this->validation_model->process_post_field('port_counter');
        $product_setings['port_counter'] = 1;
        $product_setings['trial'] = 1;
        $product_setings['pro_rata_option'] = 1;
        $product_setings['default_comment'] = '';

        // validate port class
        $old_service_info = $this->product_model->get_classes_data($product_setings['class_id']);
        $new_service_info = $this->product_model->get_classes_data($product_setings['port_service_id']);

        $product_setings['form_link'] = $this->validation_model->process_post_field('caldera_link');
        // ignore realm validation
        // $realm_validation = $this->port_model->realm_validation($old_service_info[0]['realm'], $new_service_info[0]['realm']);
        //if (!realm_validation)
        // redirect back with message
        // -------------------------------------------------------------------------------------------

        $table = 'products';
        if ($_POST['type'] == 'reseller') {
            $table = 'products_reseller';
        }

        if (trim($product_id) != '') {
            $this->db->where('id', $product_id);
            $this->db->update($table, $product_setings);

            $return_billing_cycle_monthly = $this->product_model->get_billing_cycle($product_id, 'Monthly');
            $return_billing_cycle_once = $this->product_model->get_billing_cycle($product_id, 'Once-Off');
            $return_billing_cycle_daily = $this->product_model->get_billing_cycle($product_id, 'Daily');

            if (isset($_POST['billing_cycle'])) {
                if (isset($_POST['billing_cycle']['monthly'])) {
                    if (empty($return_billing_cycle_monthly)) {
                        $billing_cycle = array(
                            'billing_cycle' => 'Monthly',
                            'product_id' => $product_id,
                        );
                        $this->db->insert('billing_cycle', $billing_cycle);
                    }
                } else {
                    if (!empty($return_billing_cycle_monthly)) {
                        $this->db->delete('billing_cycle', array('id' => $return_billing_cycle_monthly));
                    }
                }

                if (isset($_POST['billing_cycle']['once'])) {
                    if (empty($return_billing_cycle_once)) {
                        $billing_cycle = array(
                            'billing_cycle' => 'Once-Off',
                            'product_id' => $product_id,
                        );
                        $this->db->insert('billing_cycle', $billing_cycle);
                    }
                } else {
                    if (!empty($return_billing_cycle_once)) {
                        $this->db->delete('billing_cycle', array('id' => $return_billing_cycle_once));
                    }
                }

                if (isset($_POST['billing_cycle']['daily'])) {
                    if (empty($return_billing_cycle_daily)) {
                        $billing_cycle = array(
                            'billing_cycle' => 'Daily',
                            'product_id' => $product_id,
                        );
                        $this->db->insert('billing_cycle', $billing_cycle);
                    }
                } else {
                    if (!empty($return_billing_cycle_once)) {
                        $this->db->delete('billing_cycle', array('id' => $return_billing_cycle_daily));
                    }
                }
            } else {
                $this->db->delete('billing_cycle', array('product_id' => $product_id));
            }

            // PAYMENTS PART


            /*
          $return_method_credit = $this->product_model->get_credit_order($product_id, 'credit_card');
          $return_method_credit_auto = $this->product_model->get_credit_order($product_id, 'credit_card_auto');
          $return_method_debit = $this->product_model->get_credit_order($product_id, 'debit_order');
          $return_method_eft = $this->product_model->get_credit_order($product_id, 'eft');
         */


            $msg = 'This product has been successfully updated!';
        } else {


            $rand_num = $this->product_model->get_product_rand_id();
            // echo " 2"; die;
            $product_setings['random_num'] = $rand_num;
            $product_setings['billing_cycle'] = '';
            $this->db->insert($table, $product_setings);
            $product_id = $this->db->insert_id();


            if (isset($_POST['billing_cycle'])) {
                if (isset($_POST['billing_cycle']['monthly'])) {
                    if (empty($return_billing_cycle_monthly)) {
                        $billing_cycle = array(
                            'billing_cycle' => 'Monthly',
                            'product_id' => $product_id,
                        );
                        $this->db->insert('billing_cycle', $billing_cycle);
                    }
                }

                if (isset($_POST['billing_cycle']['once'])) {
                    if (empty($return_billing_cycle_once)) {
                        $billing_cycle = array(
                            'billing_cycle' => 'Once-Off',
                            'product_id' => $product_id,
                        );
                        $this->db->insert('billing_cycle', $billing_cycle);
                    }
                }
                if (isset($_POST['billing_cycle']['daily'])) {
                    if (empty($return_billing_cycle_daily)) {
                        $billing_cycle = array(
                            'billing_cycle' => 'Daily',
                            'product_id' => $product_id,
                        );
                        $this->db->insert('billing_cycle', $billing_cycle);
                    }
                }
            }

            /*

          if(isset($_POST['Payment'])){
          if(isset($_POST['Payment']['credit_card'])){
          $payment_method = array(
          'payment_method' => 'credit_card',
          'product_id' => $product_id,
          );
          $this->db->insert('product_payment_methods', $payment_method);
          }

          if(isset($_POST['Payment']['credit_card_auto'])){
          $payment_method = array(
          'payment_method' => 'credit_card_auto',
          'product_id' => $product_id,
          );
          $this->db->insert('product_payment_methods', $payment_method);
          }

          if(isset($_POST['Payment']['debit_order'])){
          $payment_method = array(
          'payment_method' => 'debit_order',
          'product_id' => $product_id,
          );
          $this->db->insert('product_payment_methods', $payment_method);
          }

          if(isset($_POST['Payment']['eft'])){
          $payment_method = array(
          'payment_method' => 'eft',
          'product_id' => $product_id,
          );
          $this->db->insert('product_payment_methods', $payment_method);
          }
          }
         */
            $msg = 'This product has been successfully created!';
        }

        // Payment methods ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $return_methods = $this->product_model->get_all_credit_orders($product_id);

        // $post_payment_array[$payment_method . '_' . $billing_cycle]
        if (isset($_POST['Payment'])) {


            // handle all payment
            $this->product_model->handle_payment_update($product_id, $_POST['Payment'], $return_methods);
        } else {
            $this->db->delete('product_payment_methods', array('product_id' => $product_id));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        if (isset($_POST['credit_card_comment'])) {

            $credit_card_comment = strip_tags(mysql_real_escape_string($_POST['credit_card_comment']));
            $this->product_model->save_additional_default_comment($product_id, 'credit_card', $credit_card_comment);
        }

        if (isset($_POST['credit_card_auto_comment'])) {

            $credit_card_auto_comment = strip_tags(mysql_real_escape_string($_POST['credit_card_auto_comment']));
            $this->product_model->save_additional_default_comment($product_id, 'credit_card_auto', $credit_card_auto_comment);
        }

        if (isset($_POST['debit_order_comment'])) {

            $debit_order_comment = strip_tags(mysql_real_escape_string($_POST['debit_order_comment']));
            $this->product_model->save_additional_default_comment($product_id, 'debit_order', $debit_order_comment);
        }


        if (isset($_POST['eft_comment'])) {

            $eft_comment = strip_tags(mysql_real_escape_string($_POST['eft_comment']));
            $this->product_model->save_additional_default_comment($product_id, 'eft', $eft_comment);
        }

        // insert additional comments;
        /*

      [credit_card_comment] => onceoffpament11
      [credit_card_auto_comment] => 2credit-card
      [debit_order_comment] => debit-order3
      [eft_comment] => eft4


     */

        $this->session->set_flashdata('success_message', $msg);

        $nosvc_edit = '';
        if (isset($_POST['nosvc_edit']))
            $nosvc_edit = $_POST['nosvc_edit'];

        if ($nosvc_edit == 'nosvc_edit') {
            redirect("/admin/all_nosvc_product");
        } elseif ($_POST['type'] == 'reseller') {
            redirect("/admin/all_product_reseller");
        } else {
            redirect("/admin/all_product");
        }
    }

    function edit_category($category = '', $type = '')
    {
        $suc_msg = $this->session->flashdata('success_message');


        $data['category_data']['category_fields'] = $this->category_model->get_category_fields();

        if (trim($category) != '') {
            //$category_data = $this->category_model->get_category_data($category);
            $category_data = $this->category_model->get_category_data_by_id($category, $type);
            if (!empty($category_data)) {
                $data['category_data']['category_data'] = $category_data;
                $data['category_data']['edit_category'] = $category_data['name'];
            } else {
                $data['category_data']['category_data'] = '';
            }
        } else {
            $data['category_data']['category_data'] = '';
        }

        $data['messages']['success_message'] = $suc_msg;
        $data['main_content'] = 'admin/categories/edit_category';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function edit_subcategory($category = '', $type = '')
    {
        $suc_msg = $this->session->flashdata('success_message');

        // It also needs all parents for selecting parent.
        $data['subcategory_data']['all_categories_reseller'] = $this->category_model->get_categories_reseller();
        $data['subcategory_data']['all_categories'] = $this->category_model->get_categories();

        $data['subcategory_data']['subcategory_fields'] = $this->category_model->get_subcategory_fields();

        if (trim($category) != '') {
            //$category_data = $this->category_model->get_subcategory_data($category);
            $subcategory_data = $this->category_model->get_subcategory_data_by_id($category, $type);

            $categoryType = $type == 'r' ? 'reseller' : 'client';

            if (!empty($subcategory_data)) {
                $parent = $subcategory_data['parent'];
                $subcategory_data['parent_id'] = $subcategory_data['parent'];
                $subcategory_data['parent'] = $this->category_model->get_category_name($parent);
                $subcategory_data['parent_r'] = $this->category_model->get_category_name($parent, 'r');
                $data['subcategory_data']['subcategory_data'] = $subcategory_data;
                $data['subcategory_data']['subcategory_data']['type'] = $categoryType;
                $data['subcategory_data']['edit_subcategory'] = $subcategory_data['name'];
            } else {
                $data['subcategory_data']['subcategory_data'] = '';
            }
        } else {
            $data['subcategory_data']['subcategory_data'] = '';
        }

        $data['messages']['success_message'] = $suc_msg;
        $data['main_content'] = 'admin/subcategories/edit_category';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function edit_realm($realm_id = '')
    {
        $suc_msg = $this->session->flashdata('success_message');

        $realm_name = $this->realm_model->get_realm_name($realm_id);
        $data['realm_data']['edit_realm'] = $realm_name;
        $data['realm_data']['all_realms'] = $this->realm_model->get_realm_list();

        if (trim($realm_id) != '') {
            //$realm_data = $this->realm_model->get_realm_data($realm_name);
            $realm_data = $this->realm_model->get_realm_data($realm_id);
            if ($realm_data == '') {
                $data['realm_data']['realm_data'] = '';
            } else {
                $data['realm_data']['realm_data'] = $realm_data;
            }
        } else {
            $data['realm_data']['realm_data'] = '';
        }

        $data['messages']['success_message'] = $suc_msg;
        $data['main_content'] = 'admin/realms/edit_realms';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    // The $product param here is the product ID.
    function edit_product($product_id = '', $duplicate = 0, $type = '')
    {
        $product_name = $this->product_model->get_product_name($product_id);
        $topup_options = array();

        $suc_msg = $this->session->flashdata('success_message');
        $error_msg = $this->session->flashdata('error_message');

        $data['pro_rata_options'] = $this->product_model->get_pro_rata_options();
        $billing_cycles = $this->product_model->get_billing_cycles();
        $data['product_data']['billing_cycles'] = $billing_cycles;
        $data['product_data']['product_fields'] = $this->product_model->get_product_fields();
        $data['product_data']['is_classes'] = $this->product_model->get_classes();
        $data['product_data']['all_products'] = $this->product_model->get_product_list();
        $data['product_data']['potential_parents'] = $this->product_model->get_product_categories();
        $data['product_data']['categories_res'] = $this->product_model->get_product_categories_reseller();
        if (trim($product_id) != '') {
            if ($type == 'r') {
                $data['product_data']['product_data'] = $this->product_model->get_product_data($product_id, 'r');
                $data['type'] = 'reseller';
            } else {
                $data['product_data']['product_data'] = $this->product_model->get_product_data($product_id);
                $data['type'] = 'client';
            }

            $data['product_data']['edit_product'] = $data['product_data']['product_data']['product_settings']['name'];
            //  $data['product_data']['payment_methods'] = $this->product_model->get_payment_methods($product_id);
            $data['product_data']['payment_methods'] = $this->product_model->get_full_payment_methods($product_id);

            $data['product_data']['billing_cycle'] = $this->product_model->get_billing_cycle_exist($product_id);

            // try fetch default - comments
            $additional_comments_array = $this->product_model->get_additional_default_comments($product_id);
            $data['product_additional_comments'] = $additional_comments_array;


            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $topup_options = $this->product_model->get_product_topup_options($product_id);
            // -----------------------------
            //echo "<pre>";print_r($data['product_data']['billing_cycle']);die;
        } else {
            $data['product_data']['product_data'] = '';
            $data['product_data']['payment_methods'] = '';
            $data['product_data']['billing_cycle'] = '';
        }

        $topup_full_list = $this->product_model->topup_get_full_list();


        $data['topup_full_list'] = $topup_full_list;
        $data['topup_options'] = $topup_options;

        $data['main_content'] = 'admin/products/edit_product';
        $data['messages']['success_message'] = $suc_msg;
        $data['messages']['error_message'] = $error_msg;
        $data['sidebar'] = TRUE;

        $this->load->view('admin/includes/template', $data);
    }

    function select_delete_product()
    {
        if (isset($_POST['product'])) {
            $id = $_POST['product'];
            redirect("admin/delete_product/$id");
        }
    }

    function confirm_delete($product_id, $type = '')
    {
        $this->product_model->delete_product($product_id, $type);

        $msg = 'Your product have been successfully deleted!';
        $this->session->set_flashdata('success_message', $msg);
        redirect('admin/all_product');
    }

    function delete_product($product_id = '', $type = '')
    {
        if (trim($product_id) != '') {
            $users = $this->product_model->users_on_product($product_id);
            $data['users'] = $users;
        }
        $data['type'] = $type;
        $data['product_id'] = $product_id;
        $data['delete_product'] = $product_id;
        $data['main_content'] = 'admin/products/delete_product';
        $data['product_data']['all_products'] = $this->product_model->get_product_list();
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function manage_products()
    {

        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        $data['main_content'] = 'admin/products/manage_products';
        $data['sidebar'] = TRUE;
        $data['product_data'] = array();
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function manage_topups()
    {
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        $data['main_content'] = 'admin/topups/manage_topups';
        $data['sidebar'] = TRUE;
        $data['product_data'] = array();
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function manage_showmax_subscriptions()
    {

        /*
     *     $suc_msg = $this->session->flashdata('success_message');
      $data['messages']['success_message'] = $suc_msg;

      $data['main_content'] =
      'admin/topups/manage_topups';
      $data['sidebar'] = TRUE;
      $data['product_data'] = array();
      $this->load->view(
      'admin/includes/template', $data);
     */
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        $data['main_content'] = 'admin/showmax/manage_showmax_subscriptions';
        $data['sidebar'] = TRUE;
        $data['product_data'] = array();
        $this->load->view('admin/includes/template', $data);
    }

    function create_showmax_subscription()
    {

        show_404();
        die();

        $users = $this->user_model->get_user_list();
        $user_list = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $key = $user['username'];
                $value = "{$user['first_name']} {$user['last_name']} ($key)";
                $user_list[$key] = $value;
            }
        }
        // get all users and generate array of them

        $data['user_list'] = $user_list;
        $data['main_content'] = 'admin/showmax/create_showmax_subscription';
        $data['sidebar'] = TRUE;
        $data['product_data'] = array();
        $this->load->view('admin/includes/template', $data);


        /*
     *
     *        $data['main_content'] = 'admin/topups/create_topup';
      $data['sidebar'] = TRUE;
      // page category + title + link data
      $data['page_link'] = "/admin/create_topup";
      $data['page_category'] = $this->page_category['topup'];
      $data['page_title'] = $this->page_category['topup'][$data['page_link']];
      $data['sidebar_category'] = 'topup';
      $this->load->view($this->ui_prefix . 'admin/includes/template', $data);

     */
    }

    function showmax_subscriptions()
    {

        $data['main_content'] = 'admin/showmax/all_showmax_subscriptions';
        $data['sidebar'] = TRUE;
        $data['product_data'] = array();
        $this->load->view('admin/includes/template', $data);
    }

    /*
 *            'create_showmax_subscription' => 'Create new ShowMax subscription',
  'showmax_subscriptions' => 'All ShowMax subscriptions',
 */

    function manage_users()
    {
        $data['main_content'] = 'admin/accounts/manage_users';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function manage_realms()
    {
        $data['main_content'] = 'admin/realms/manage_realms';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function manage_class()
    {
        $data['main_content'] = 'admin/classes/manage_class';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function manage_emails()
    {
        $data['main_content'] = 'admin/email/manage_emails';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function manage_messages()
    {
        $data['main_content'] = 'admin/message/manage_messages';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function create_realm()
    {
        $data['error_message'] = $this->session->flashdata('error_message');
        $data['main_content'] = 'admin/realms/create_realms';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_realm";
        $data['page_category'] = $this->page_category['realms_and_classes'];
        $data['page_title'] = $this->page_category['realms_and_classes'][$data['page_link']];
        $data['sidebar_category'] = 'realms_and_classes';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function is_user_admin()
    {
        // For now, it's just checking if logged in!
        $is_logged_in = $this->session->userdata('is_logged_in');
        $role = $this->session->userdata('role');
        if (!isset($is_logged_in) || $is_logged_in != true || !isset($role) || $role != 'admin') {
            /* echo "You don't have permission to access this page. ";
          echo '<a href="../login">Login</a>';
          die(); */
            redirect('login');
        }
    }

    function view_classes($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;

        $class_list = $this->product_model->get_is_classes($num_per_page, $start);
        $data['classes']['class_list'] = $class_list;
        $data['class_type'] = 'All';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/view_classes');
        $num_class = $this->product_model->get_class_count();

        $config['total_rows'] = $num_class;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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

        $classes_count = count($class_list);
        $class_ind = $start + $classes_count;

        $data['showing'] = "Showing $start-$class_ind of $num_class";
        $data['num_per_page'] = $num_per_page;
        $data['num_realm'] = $num_class;

        $data['main_content'] = 'admin/classes/view_classes';
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/view_classes";
        $data['page_category'] = $this->page_category['realms_and_classes'];
        $data['page_title'] = $this->page_category['realms_and_classes'][$data['page_link']];
        $data['sidebar_category'] = 'realms_and_classes';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function create_class()
    {
        $data['realm_list'] = $this->realm_model->get_all_realm_name();
        $data['error_message'] = $this->session->flashdata('error_message');
        $data['main_content'] = 'admin/classes/create_class';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_class";
        $data['page_category'] = $this->page_category['realms_and_classes'];
        $data['page_title'] = $this->page_category['realms_and_classes'][$data['page_link']];
        $data['sidebar_category'] = 'realms_and_classes';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function validate_class_id()
    {
        if (isset($_POST['class_id']) && trim($_POST['class_id']) != '') {
            $result = $this->realm_model->validate_class_id($_POST['class_id']);
            if ($result == '1') {
                echo "false";
            } else {
                echo "true";
            }
        }
    }

    function add_class()
    {
        $class_id = $_POST['class_id'];
        $class_des = $_POST['class_des'];
        $realm = md5($_POST['realm']);
        $data = array(
            'id' => $class_id,
            'desc' => $class_des,
            'realm' => $realm,
        );
        $this->db->insert('is_classes', $data);
        $table_id = $this->db->insert_id();

        if ($table_id) {
            $msg = 'This class has been successfully created!';
            $this->session->set_flashdata('success_message', $msg);
            redirect("/admin/view_classes");
        } else {
            $msg = 'Failed to insert a new class.Please try it again.';
            $this->session->set_flashdata('error_message', $msg);
            redirect("/admin/create_class");
        }
    }

    function edit_class($class_id = '')
    {
        $suc_msg = $this->session->flashdata('success_message');

        $data['classes_data']['class_id'] = $class_id;

        if (trim($class_id) != '') {
            $data['classes_data']['classes_data'] = $this->product_model->get_classes_data($class_id);
        } else {
            $data['classes_data']['classes_data'] = '';
        }
        $data['realm_list'] = $this->realm_model->get_all_realm_name();
        $data['main_content'] = 'admin/classes/edit_class';
        $data['messages']['success_message'] = $suc_msg;
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function update_class()
    {
        if (isset($_POST['class_id'])) {
            $class_id = $_POST['class_id'];
        } else {
            $class_id = '';
        }

        $class_name = $_POST['name'];
        $desc = $_POST['desc'];
        $realm = $_POST['realm'];

        $class_fields = $this->product_model->get_class_fields();
        $class_setting = array('id' => $class_name, 'desc' => $desc, 'realm' => $realm);

        if ($class_id) {
            $this->load->model('admin/is_classes');
            $this->is_classes->update_classes_new();

            $this->db->where('table_id', $class_id);
            $result = $this->db->update('is_classes', $class_setting);

            $msg = 'This class has been updated successfully!';
            $this->session->set_flashdata('success_message', $msg);
            redirect("/admin/view_classes");
        } else {
            $msg = 'Failed to update this class.';
            $this->session->set_flashdata('error_message', $msg);
            redirect("/admin/edit_class/$class_id");
        }
    }

    function update_classes($confirm = false)
    {
        /*
      When updating the class list,
      first check to make sure that all order with the classes
      that will be updated will still be there?
     */
        if ($confirm == 'confirm') {
            // Update the classes!
            $this->load->model('admin/is_classes');
            $this->is_classes->update_classes_new();
            $msg = 'Your classes have been successfully updated!';
            $this->session->set_flashdata('success_message', $msg);
            redirect('admin/dashboard');
        } else {
            $data['main_content'] = 'admin/update_classes';
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    function pending_orders($start = 0)
    {
        $this->session->set_userdata(array("manage_flag" => "pending_order"));

        $num_per_page = NUM_PER_PAGE;
        $orders = $this->order_model->get_pending_orders($num_per_page, $start);
        $data['order_type'] = 'Pending ';
        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/pending_orders');
        $num_orders = $this->order_model->get_pending_orders_count();
        $config['total_rows'] = $num_orders;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
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
                $user = $order['user'];
                $user_name = $this->user_model->get_user_name($user);
                $user_nice = $user_name . " ($user)";
                $product_data = $this->product_model->get_product_data($product_id);
                $order_data[] = array(
                    'product_id' => $product_id,
                    'status' => $status,
                    'date' => $date,
                    'product_data' => $product_data['product_settings'],
                    'user' => $user_nice,
                    'order_id' => $order['id'],
                    'price' => 'R' . number_format(round($order['price'], 2), 2),
                );
            }
        }
        $orders_count = $start + count($orders);
        $data['showing'] = "Showing $start-$orders_count of $num_orders";
        $data['num_per_page'] = $num_per_page;
        $data['num_orders'] = $num_orders;
        $data['orders'] = $order_data;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $data['main_content'] = 'admin/orders';
        $data['sidebar'] = TRUE;

        //  page category + title + link data
        $data['page_category'] = $this->page_category['orders'];
        $data['page_link'] = "/admin/pending_orders";
        $data['sidebar_category'] = 'orders';
        $data['page_title'] = $this->page_category['orders'][$data['page_link']];
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function activity_log($start = 0)
    {
        if (isset($_GET['u'])) {
            $user = $_GET['u'];
        } else {
            $user = '';
        }

        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
        } else {
            $start_date = '';
            $end_date = '';
        }

        if (isset($_GET['type']) && $_GET['type'] != '') {
            $type = $_GET['type'];
            if ($type == 0) {
                $type = 'Order Product';
            } else {
                $type = 'User Change CC Information';
            }
        } else {
            $type = '';
        }
        //var_dump($type);die;
        $num_per_page = NUM_PER_PAGE;
        if ($user != '' && $type != '' && trim($start_date) != '' && trim($end_date) != '') {
            $activity_validte = $this->membership_model->get_activity_log_by_type($user, $type, $start_date, $end_date, $num_per_page, $start);
            if (empty($activity_validte)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_activity_log_by_type($user, $type, $start_date, $end_date, $num_per_page, $start);
            $num_activities = $this->membership_model->get_activity_count_by_type($user, $type, $start_date, $end_date);
            //print_r('1');
        } elseif ($user != '' && $type != '' && trim($start_date) == '' && trim($end_date) == '') {
            $activity_validte = $this->membership_model->get_activity_log_by_type_only($user, $type, $num_per_page, $start);
            if (empty($activity_validte)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_activity_log_by_type_only($user, $type, $num_per_page, $start);
            $num_activities = $this->membership_model->get_activity_count_by_type_only($user, $type);

            //print_r('2');
        } elseif ($user != '' && $type == '' && trim($start_date) != '' && trim($end_date) != '') {
            $activity_validte = $this->membership_model->get_activity_log_by_date($user, $start_date, $end_date, $num_per_page, $start);
            if (empty($activity_validte)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_activity_log_by_date($user, $start_date, $end_date, $num_per_page, $start);
            $num_activities = $this->membership_model->get_activity_count_by_date($user, $start_date, $end_date);

            //print_r('3');
        } elseif ($user == '' && $type != '' && trim($start_date) != '' && trim($end_date) != '') {
            $activity_validate = $this->membership_model->get_all_activity_log_by_type($type, $start_date, $end_date, $num_per_page, $start);
            if (empty($activity_validate)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_all_activity_log_by_type($type, $start_date, $end_date, $num_per_page, $start);
            $num_activities = $this->membership_model->get_all_activity_count_by_type($type, $start_date, $end_date);

            //print_r('4');
        } elseif ($user != '' && $type == '' && trim($start_date) == '' && trim($end_date) == '') {
            $activity_validte = $this->membership_model->get_activity_log($user, $num_per_page, $start);
            if (empty($activity_validte)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_activity_log($user, $num_per_page, $start);
            $num_activities = $this->membership_model->get_activity_count($user);

            //print_r('5');
        } elseif ($user == '' && $type == '' && trim($start_date) != '' && trim($end_date) != '') {
            $activity_validate = $this->membership_model->get_all_activity_log_by_date($start_date, $end_date, $num_per_page, $start);
            if (empty($activity_validate)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_all_activity_log_by_date($start_date, $end_date, $num_per_page, $start);
            $num_activities = $this->membership_model->get_all_activity_count_by_date($start_date, $end_date);

            //print_r('6');
        } elseif ($user == '' && $type != '' && trim($start_date) == '' && trim($end_date) == '') {
            $activity_validate = $this->membership_model->get_all_activity_log_by_type_only($type, $num_per_page, $start);
            if (empty($activity_validate)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_all_activity_log_by_type_only($type, $num_per_page, $start);
            $num_activities = $this->membership_model->get_all_activity_count_by_date_only($type);

            //print_r('7');
        } else { //$user == '' && $type == '' && trim($start_date) == '' && trim($end_date) == ''
            $activity_validate = $this->membership_model->get_all_activity_log($num_per_page, $start);
            if (empty($activity_validate)) {
                $start = 0;
            }
            $activity = $this->membership_model->get_all_activity_log($num_per_page, $start);
            $num_activities = $this->membership_model->get_all_activity_count();
            //print_r('8');
        }

        $data['activity'] = $activity;

        $data['num_activities'] = $num_activities;
        $this->load->library('pagination');

        $config['base_url'] = base_url('index.php/admin/activity_log');

        if (count($_GET) > 0)
            $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);

        $config['total_rows'] = $num_activities;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<div class="pull-right"><ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['first_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_close'] = '</li>';

        $data['all_users'] = $this->user_model->get_user_list();

        $act_count = $start + count($activity);
        $data['showing'] = "Showing $start-$act_count of $num_activities";
        $data['start'] = $start;
        $this->pagination->initialize($config);
        $data['pages'] = $this->pagination->create_links();

        $data['main_content'] = 'admin/activity_log';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_title'] = 'Activity Log';
        $data['sidebar_category'] = 'activity_log';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function bills_history($start = 0)
    {
        if (isset($_GET['u'])) {
            $user = $_GET['u'];
        } else {
            $user = '';
        }

        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
        } else {
            $start_date = '';
            $end_date = '';
        }

        $num_per_page = NUM_PER_PAGE;

        if (trim($user) != '') {
            if (trim($start_date) != '' && trim($end_date) != '') {
                $bills_validate = $this->membership_model->get_bills_by_date($user, $start_date, $end_date, $num_per_page, $start);
                if (empty($bills_validate)) {
                    $start = 0;
                }
                $bills = $this->membership_model->get_bills_by_date($user, $start_date, $end_date, $num_per_page, $start);
                $num_bills = $this->membership_model->get_bills_count_by_date($user, $start_date, $end_date);
            } else {
                $bills_validate = $this->membership_model->get_bills($user, $num_per_page, $start);
                if (empty($bills_validate)) {
                    $start = 0;
                }
                $bills = $this->membership_model->get_bills($user, $num_per_page, $start);
                $num_bills = $this->membership_model->get_bills_count($user);
            }
        } else {
            if (trim($start_date) != '' && trim($end_date) != '') {
                $bills_validate = $this->membership_model->get_all_bills_by_date($start_date, $end_date, $num_per_page, $start);
                if (empty($bills_validate)) {
                    $start = 0;
                }
                $bills = $this->membership_model->get_all_bills_by_date($start_date, $end_date, $num_per_page, $start);
                $num_bills = $this->membership_model->get_all_bills_count_by_date($start_date, $end_date);
            } else {
                $bills_validate = $this->membership_model->get_all_bills($num_per_page, $start);
                if (empty($bills_validate)) {
                    $start = 0;
                }
                $bills = $this->membership_model->get_all_bills($num_per_page, $start);
                $num_bills = $this->membership_model->get_all_bills_count();
            }
        }

        $data['bills'] = $bills;
        $data['main_content'] = 'admin/accounts/bills_history';
        $data['sidebar'] = TRUE;

        $data['num_bills'] = $num_bills;
        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/bills_history');

        if (count($_GET) > 0)
            $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);

        $config['total_rows'] = $num_bills;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<div class="pull-right"><ul class="pagination uiflat-mix-pagination ">';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['first_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_close'] = '</li>';

        $data['all_users'] = $this->user_model->get_user_list();
        $act_count = $start + count($bills);
        $data['showing'] = "Showing $start-$act_count of $num_bills";
        $data['start'] = $start;
        $this->pagination->initialize($config);
        $data['pages'] = $this->pagination->create_links();


        // page category + title + link data
        $data['page_link'] = "/admin/bills_history";
        $data['page_category'] = $this->page_category['monthly_reports'];
        $data['page_title'] = $this->page_category['monthly_reports'][$data['page_link']];
        $data['sidebar_category'] = 'monthly_reports';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_orders($start = 0)
    {
        $this->session->set_userdata(array("manage_flag" => "all_orders"));

        $num_per_page = NUM_PER_PAGE;
        $orders = $this->order_model->get_all_orders($num_per_page, $start, array('adsl', 'fibre-data', 'fibre-line', 'lte-a'));
        $data['order_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_orders');
        $num_orders = $this->order_model->get_all_orders_count(array('adsl', 'fibre-data', 'fibre-line'));
        $config['total_rows'] = $num_orders;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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
                $user = $order['user'];
                $user_name = $this->user_model->get_user_name($user);
                $user_nice = $user_name . " ($user)";
                $product_data = $this->product_model->get_product_data($product_id);
                $order_data[] = array(
                    'product_id' => $product_id,
                    'status' => $status,
                    'date' => $date,
                    'product_data' => $product_data['product_settings'],
                    'user' => $user_nice,
                    'order_id' => $order['id'],
                    'price' => 'R' . number_format(round($order['price'], 2), 2),
                    'service_type' => $order['service_type'],
                    'fibre' => $order['fibre'],
                );
            }
        }

        $orders_count = count($orders);
        $orders_ind = $start + $orders_count;
        $data['showing'] = "Showing $start-$orders_ind of $num_orders";
        $data['num_per_page'] = $num_per_page;
        $data['num_orders'] = $num_orders;
        $data['orders'] = $order_data;
        $data['main_content'] = 'admin/orders';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $error_msg = $this->session->flashdata('error_message');
        $data['messages']['error_message'] = $error_msg;

        // page category + title + link data
        $data['page_category'] = $this->page_category['orders'];
        $data['sidebar_category'] = 'orders';
        $data['page_link'] = "/admin/all_orders";
        $data['page_title'] = $this->page_category['orders'][$data['page_link']];
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_undef_orders($start = 0)
    {

        $this->session->set_userdata(array("manage_flag" => "all_undef"));

        $num_per_page = NUM_PER_PAGE;
        // get undef orders
        $orders = $this->order_model->get_all_undef_orders($num_per_page, $start);
        $data['order_type'] = 'All undefined';


        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_undef_orders');
        $num_orders = $this->order_model->get_all_undef_orders_count();
        $config['total_rows'] = $num_orders;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
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
                $user = $order['user'];
                $user_name = $this->user_model->get_user_name($user);
                $user_nice = $user_name . " ($user)";
                $product_data = $this->product_model->get_product_data($product_id);
                $order_data[] = array(
                    'product_id' => $product_id,
                    'status' => $status,
                    'date' => $date,
                    'product_data' => $product_data['product_settings'],
                    'user' => $user_nice,
                    'order_id' => $order['id'],
                    'price' => 'R' . number_format(round($order['price'], 2), 2),
                );
            }
        }


        $orders_count = count($orders);
        $orders_ind = $start + $orders_count;
        $data['showing'] = "Showing $start-$orders_ind of $num_orders";
        $data['num_per_page'] = $num_per_page;
        $data['num_orders'] = $num_orders;
        $data['orders'] = $order_data;
        $data['main_content'] = 'admin/orders';
        $data['sidebar'] = TRUE;

        $suc_msg = $this->session->flashdata('success_message');
        $err_msg = $this->session->flashdata('error_message');
        $data['messages']['error_message'] = $err_msg;
        $data['messages']['success_message'] = $suc_msg;

        // var_dump($order_data);
        // die;
        // page category + title + link data
        $data['page_link'] = "/admin/all_undef_orders";
        $data['page_category'] = $this->page_category['orders'];
        $data['sidebar_category'] = 'orders';
        $data['page_title'] = $this->page_category['orders'][$data['page_link']];
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_product_reseller($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;

        $params_array = [
            'cycle' => 'all',
            'visibility' => 'visible',
            'type' => 'reseller'
        ];

        $products = $this->product_model->get_filtered_product($num_per_page, $start, $params_array);
        $num_product = $this->product_model->get_filtered_product_total_count($params_array);


        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_product_reseller');

        $config['total_rows'] = $num_product;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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

        $products_data = array();
        if (!empty($products)) {
            foreach ($products as $product) {

                $parent_id = $product['parent']; //product class
                $parent = $this->category_model->get_subcategory_name($parent_id, 'r');

                $products_data[] = array(
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'visibility' => $product['active'], // visibility  hidden or visable
                    'price' => $product['price'],
                    'product_parent' => $parent,
                    'billing_cycle' => $product['billing_cycle'], // : monthly  yearly once-off
                    'product_class' => $product['class'], //product class
                    'package_speed' => $product['package_speed'],
                    'service_level' => $product['service_level'], //dervice level
                    'recommended_use' => $product['recommended_use'], //recommanded use
                    'global_backbone' => $product['global_backbone'], //global backbone
                    'automatic_creation' => $product['automatic_creation'], // 0 or 1 manual/auto-create
                    'default_comment' => $product['default_comment'], //default comment
                );
            }
        }
        $product_count = count($products);
        $product_ind = $start + $product_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$product_ind of $num_product";
        $data['num_per_page'] = $num_per_page;
        $data['num_product'] = $num_product;
        $billing_cycle = $this->product_model->get_cycles();
        $data['cycle'] = $billing_cycle;
        $data['products'] = $products_data;
        $data['main_content'] = 'admin/products/all_product';
        $data['reseller'] = 1;
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        // page category + title + link data
        $data['page_link'] = "/admin/all_product";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_product($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;

        $params_array = array(
            'cycle' => 'all',
            'visibility' => 'visible',
        );

        $products = $this->product_model->get_filtered_product($num_per_page, $start, $params_array);
        $num_product = $this->product_model->get_filtered_product_total_count($params_array);


        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_product');

        $config['total_rows'] = $num_product;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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

        $products_data = array();
        if (!empty($products)) {
            foreach ($products as $product) {

                $parent_id = $product['parent']; //product class
                $parent = $this->category_model->get_subcategory_name($parent_id);

                $products_data[] = array(
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'visibility' => $product['active'], // visibility  hidden or visable
                    'price' => $product['price'],
                    'product_parent' => $parent,
                    'billing_cycle' => $product['billing_cycle'], // : monthly  yearly once-off
                    'product_class' => $product['class'], //product class
                    'package_speed' => $product['package_speed'],
                    'service_level' => $product['service_level'], //dervice level
                    'recommended_use' => $product['recommended_use'], //recommanded use
                    'global_backbone' => $product['global_backbone'], //global backbone
                    'automatic_creation' => $product['automatic_creation'], // 0 or 1 manual/auto-create
                    'default_comment' => $product['default_comment'], //default comment
                );
            }
        }
        $product_count = count($products);
        $product_ind = $start + $product_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$product_ind of $num_product";
        $data['num_per_page'] = $num_per_page;
        $data['num_product'] = $num_product;
        $billing_cycle = $this->product_model->get_cycles();
        $data['cycle'] = $billing_cycle;
        $data['products'] = $products_data;
        $data['main_content'] = 'admin/products/all_product';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        // page category + title + link data
        $data['page_link'] = "/admin/all_product";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_nosvc_product($start = 0)
    {

        $num_per_page = NUM_PER_PAGE;
        $products = $this->product_model->get_all_undef_nosvc_product($num_per_page, $start);
        $data['product_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_nosvc_product');
        $num_product = $this->product_model->get_product_undef_nosvc_count();
        $config['total_rows'] = $num_product;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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

        $products_data = array();
        if (!empty($products)) {
            foreach ($products as $product) {
                $product_id = $product['id']; //product id
                $name = $product['name']; //product name
                //$desc = $product['desc'];
                //$num_users = $product['num_users'];
                $active = $product['active'];  // visibility  hide or visable
                //$discount_codes = $product['discount_codes'];
                $price = $product['price']; //price
                //$automatic_biling = $product['automatic_billing'];
                $parent_id = $product['parent']; //product class
                $parent = $this->category_model->get_subcategory_name($parent_id);

                //$type = $product['type'];
                //$features = $product['features'];
                $billing_cycle = $product['billing_cycle']; //billing cycle monthly  yearly once-off
                //$billing_occurs_on = $product['billing_occurs_on'];
                $class = $product['class']; //product class
                $package_speed = $product['package_speed']; //package speed
                $service_level = $product['service_level']; //dervice level
                $recommended_use = $product['recommended_use']; //recommanded use
                $global_backbone = $product['global_backbone']; //global backbone
                $automatic_creation = $product['automatic_creation']; // 0 or 1 manual/auto-create
                //$pro_rata_option = $product['pro_rata_option'];
                $default_comment = $product['default_comment']; //default comment

                $products_data[] = array(
                    'id' => $product_id,
                    'name' => $name,
                    'visibility' => $active,
                    'price' => $price,
                    'product_parent' => $parent,
                    'billing_cycle' => $billing_cycle,
                    'product_class' => $class,
                    'package_speed' => $package_speed,
                    'service_level' => $service_level,
                    'recommended_use' => $recommended_use,
                    'global_backbone' => $global_backbone,
                    'automatic_creation' => $automatic_creation,
                    'default_comment' => $default_comment
                );
            }
        }
        $product_count = count($products);
        $product_ind = $start + $product_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$product_ind of $num_product";
        $data['num_per_page'] = $num_per_page;
        $data['num_product'] = $num_product;
        $billing_cycle = $this->product_model->get_cycles();
        $data['cycle'] = $billing_cycle;
        $data['products'] = $products_data;
        $data['main_content'] = 'admin/products/all_product';
        $data['sidebar'] = TRUE;

        $data['nosvc_flag'] = TRUE;
        // $data['redirect_param'] = '';
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;


        // page category + title + link data
        $data['page_link'] = "/admin/all_nosvc_product";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function filter_product($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;


        $cycle = $this->product_model->process_product_request('cycle');
        $visibility = $this->product_model->process_product_request('visibility');

        if (empty($cycle)) {
            $cycle = $this->session->userdata('current_cycle');
        }

        if (empty($visibility)) {
            $visibility = $this->session->userdata('current_visibility');
        }


        if (empty($cycle))
            $cycle = 'all';

        if (empty($visibility))
            $visibility = 'visible';



        $params_array = array(
            'cycle' => $cycle,
            'visibility' => $visibility,
        );

        $products = $this->product_model->get_filtered_product($num_per_page, $start, $params_array);
        $num_product = $this->product_model->get_filtered_product_total_count($params_array);


        $this->session->set_userdata('current_cycle', $cycle);
        $this->session->set_userdata('current_visibility', $visibility);
        $data['curr_cycle'] = $cycle;
        $data['curr_visibility'] = $visibility;

        //              --- rewrite ---
        //// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        /*
      if($cycle == 'all'){
      // $products =  $this->product_model->get_all_product($num_per_page, $start);
      $products = $this->product_model->get_all_not_imported_product($num_per_page, $start);

      // $num_product = $this->product_model->get_product_count();
      $num_product = $this->product_model->get_product_count_without_import();
      $this->session->set_userdata('current_cycle', $cycle);
      $data['curr_cycle'] = $cycle;
      }else{
      $products_data =  $this->product_model->get_cycle_product($cycle, $num_per_page, $start);
      if($products_data){
      $products = $products_data;
      $num_product = $this->product_model->get_cycle_count($cycle);
      }else{
      $products = '';
      $num_product = 0;
      }

      $this->session->set_userdata('current_cycle', $cycle);
      $data['curr_cycle'] = $cycle;
      }
     */
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $products_data = array();
        if (!empty($products)) {
            foreach ($products as $product) {
                $product_id = $product['id'];
                $name = $product['name'];
                $active = $product['active'];
                $price = $product['price'];
                $parent_id = $product['parent'];
                $parent = $this->category_model->get_subcategory_name($parent_id);
                $billing_cycle = $product['billing_cycle'];
                $class = $product['class'];
                $package_speed = $product['package_speed'];
                $service_level = $product['service_level'];
                $recommended_use = $product['recommended_use'];
                $global_backbone = $product['global_backbone'];
                $automatic_creation = $product['automatic_creation'];
                $default_comment = $product['default_comment'];

                $products_data[] = array(
                    'id' => $product_id,
                    'name' => $name,
                    'visibility' => $active,
                    'price' => $price,
                    'product_parent' => $parent,
                    'billing_cycle' => $billing_cycle,
                    'product_class' => $class,
                    'package_speed' => $package_speed,
                    'service_level' => $service_level,
                    'recommended_use' => $recommended_use,
                    'global_backbone' => $global_backbone,
                    'automatic_creation' => $automatic_creation,
                    'default_comment' => $default_comment
                );
            }
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/filter_product');
        $config['total_rows'] = $num_product;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['first_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_close'] = '</li>';

        $product_count = count($products);
        $product_ind = $start + $product_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$product_ind of $num_product";
        $data['num_per_page'] = $num_per_page;
        $data['num_product'] = $num_product;
        $this->pagination->initialize($config);
        $data['pages'] = $this->pagination->create_links();

        $billing_cycle = $this->product_model->get_cycles();
        $data['cycle'] = $billing_cycle;
        $data['products'] = $products_data;
        $data['main_content'] = 'admin/products/all_product';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        // page category + title + link data
        $data['page_link'] = "/admin/all_product";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_realms($start = 0)
    {
        $num_per_page = NUM_PER_PAGE;
        $realms = $this->realm_model->get_all_realm($num_per_page, $start);
        $data['realms_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_realms');
        $num_realm = $this->realm_model->get_realm_count();

        $config['total_rows'] = $num_realm;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $realms_data = array();
        if (!empty($realms)) {
            foreach ($realms as $realm) {
                $realms_data[] = array(
                    'id' => $realm['id'],
                    'realm' => $realm['realm'],
                    'user' => $realm['user'],
                    'password' => $realm['pass'],
                );
            }
        }
        $realm_count = count($realms);
        $realm_ind = $start + $realm_count;
        $data['showing'] = "Showing $start-$realm_ind of $num_realm";
        $data['num_per_page'] = $num_per_page;
        $data['num_realm'] = $num_realm;
        $data['realms'] = $realms_data;
        $data['main_content'] = 'admin/realms/realms_list';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;


        // page category + title + link data
        $data['page_link'] = "/admin/all_realms";
        $data['page_category'] = $this->page_category['realms_and_classes'];
        $data['page_title'] = $this->page_category['realms_and_classes'][$data['page_link']];
        $data['sidebar_category'] = 'realms_and_classes';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_subcategory($filter = null, $start = 0)
    {
        $num_per_page = NUM_PER_PAGE;
        $subcategories = [];
        $config = [];
        if (isset($filter) && $filter != 'all') {
            $subcategories = $this->category_model->get_all_subcategory_by_filter($num_per_page, $start, $filter);
        } else {
            $subcategories = $this->category_model->get_all_subcategory($num_per_page, $start);
        }

        $filter = $filter == null ? 'all' : $filter;

        $data['subcategory_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_subcategory/' . $filter);
        $num_subcategory = $this->category_model->get_subcategory_count($filter);
        $config['uri_segment'] = 4;
        $config['total_rows'] = $num_subcategory;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $data['pages'] = $this->pagination->create_links(); //var_dump($data['pages']);die;
        $subcategory_data = array();
        if (!empty($subcategories)) {
            foreach ($subcategories as $subcategory) {
                $subcategory_data[] = array(
                    'id' => $subcategory['id'],
                    'name' => $subcategory['name'],
                    'desc' => $subcategory['desc'],
                    'slug' => $subcategory['slug'],
                    'parent' => $this->category_model->get_category_name($subcategory['parent']),
                );
            }
        }

        $data['selected_filter'] = $this->uri->segment(3);
        $subcategory_count = count($subcategories);
        $subcategory_ind = $start + $subcategory_count;
        $data['showing'] = "Showing $start-$subcategory_ind of $num_subcategory";
        $data['num_per_page'] = $num_per_page;
        $data['num_subcategory'] = $num_subcategory;
        $data['subcategories'] = $subcategory_data;
        $data['main_content'] = 'admin/subcategories/subcategories_list';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        // page category + title + link data
        $data['page_link'] = "/admin/all_subcategory";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_subcategory_reseller($filter = null, $start = 0)
    {
        $num_per_page = NUM_PER_PAGE;
        $subcategories = [];
        $config = [];
        if (isset($filter) && $filter != 'all') {
            $subcategories = $this->category_model->get_all_subcategory_by_filter($num_per_page, $start, $filter, 1);
        } else {
            $subcategories = $this->category_model->get_all_subcategory($num_per_page, $start, 1);
        }

        $filter = $filter == null ? 'all' : $filter;

        $data['subcategory_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_subcategory_reseller/' . $filter);
        $num_subcategory = $this->category_model->get_subcategory_count($filter, 1);
        $config['uri_segment'] = 4;
        $config['total_rows'] = $num_subcategory;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $data['pages'] = $this->pagination->create_links(); //var_dump($data['pages']);die;
        $subcategory_data = array();
        if (!empty($subcategories)) {
            foreach ($subcategories as $subcategory) {
                $subcategory_data[] = array(
                    'id' => $subcategory['id'],
                    'name' => $subcategory['name'],
                    'desc' => $subcategory['desc'],
                    'slug' => $subcategory['slug'],
                    'parent' => $this->category_model->get_category_name($subcategory['parent'], 'r'),
                );
            }
        }
        //var_dump($subcategory_data);die();
        $data['selected_filter'] = $this->uri->segment(3);
        $subcategory_count = count($subcategories);
        $subcategory_ind = $start + $subcategory_count;
        $data['showing'] = "Showing $start-$subcategory_ind of $num_subcategory";
        $data['num_per_page'] = $num_per_page;
        $data['num_subcategory'] = $num_subcategory;
        $data['subcategories'] = $subcategory_data;
        $data['type'] = 'reseller';
        $data['main_content'] = 'admin/subcategories/subcategories_list_reseller';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        // page category + title + link data
        $data['page_link'] = "/admin/all_subcategory_reseller";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_category_reseller($filter = null, $start = 0)
    {

        $num_per_page = NUM_PER_PAGE;
        $categories = [];
        if (isset($filter) && $filter != 'all') {
            $categories = $this->category_model->get_all_category_by_filter($num_per_page, $start, $filter, 1);
        } else {
            $categories = $this->category_model->get_all_category($num_per_page, $start, 1);
        }
        $num_category = $this->category_model->get_category_count($filter, 1);

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_category_reseller');
        $config['total_rows'] = $num_category;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $category_data = array();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category_data[] = array(
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'desc' => $category['desc'],
                    'slug' => $category['slug'],
                );
            }
        }

        $category_count = count($categories);
        $category_ind = $start + $category_count;
        $data['showing'] = "Showing $start-$category_ind of $num_category";
        $data['num_per_page'] = $num_per_page;
        $data['num_category'] = $num_category;
        $data['categories'] = $category_data;
        $data['type'] = 'reseller';
        $data['main_content'] = 'admin/categories/categories_list_reseller';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        $data['selected_filter'] = $this->uri->segment(3);
        // page category + title + link data
        $data['page_link'] = "/admin/categories_list_reseller";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function all_category($filter = null, $start = 0)
    {
        $num_per_page = NUM_PER_PAGE;
        $categories = [];
        if (isset($filter) && $filter != 'all') {
            $categories = $this->category_model->get_all_category_by_filter($num_per_page, $start, $filter);
        } else {
            $categories = $this->category_model->get_all_category($num_per_page, $start);
        }
        $num_category = $this->category_model->get_category_count($filter);

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_category');
        $config['total_rows'] = $num_category;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $category_data = array();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category_data[] = array(
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'desc' => $category['desc'],
                    'slug' => $category['slug'],
                );
            }
        }

        $category_count = count($categories);
        $category_ind = $start + $category_count;
        $data['showing'] = "Showing $start-$category_ind of $num_category";
        $data['num_per_page'] = $num_per_page;
        $data['num_category'] = $num_category;
        $data['categories'] = $category_data;
        $data['main_content'] = 'admin/categories/categories_list';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;

        $data['selected_filter'] = $this->uri->segment(3);
        // page category + title + link data
        $data['page_link'] = "/admin/all_category";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function is_logged_in()
    {
        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            /* echo "You don't have permission to access this page. ";
          echo '<a href="../login">Login</a>';
          die(); */
            //header("Location: login.php");
            redirect('login');
        }
    }

    function user_service($start = 0)
    {
        $this->session->set_userdata(array("manage_flag" => "user_services"));
        $account_id = $this->session->userdata('user_id');
        $username = $this->user_model->get_user_name_by_id($account_id);
        $success_message = $this->session->flashdata('success_message');
        $error_message = $this->session->flashdata('error_message');
        if ($username) {

            $this->user_model->update_cancellations($username);
            $num_per_page = NUM_PER_PAGE;
            $num_order = $this->user_model->get_orders_count($username);
            $orders = $this->user_model->get_orders($username, $num_per_page, $start, array('adsl', 'fibre-data', 'fibre-line', 'lte-a', 'mobile'), $this->order_model);
            // echo '<pre>';
            // print_r($orders);
            // echo '</pre>';
            // die;

            // get showmax subscription
            $this->load->model("showmax_manager");
            $showmax_subscription = $this->order_model->get_showmax_subscription($account_id, $this->showmax_manager);
            if (!empty($showmax_subscription))
                array_unshift($orders, $showmax_subscription);
            $this->load->library('pagination');
            $config['base_url'] = base_url('index.php/admin/user_service');
            $config['total_rows'] = $num_order;
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
                    $product_data = '';
                    if (!empty($product_id))
                        $product_data = $this->product_model->get_product_data($product_id);
                    $price = $order['price'];
                    $pro_rata_extra = $order['pro_rata_extra'];

                    if (!isset($order['fibre']))
                        $order['fibre'] = '';
                    // showmax comment
                    //if (!isset($order["showmax_subscription"]))
                    //    $order["showmax_subscription"] = "";
                    //print_r($username);die();
                    $order_data[] = array(
                        'user' => $username,
                        'status' => $status,
                        'date' => $date,
                        'product_data' => $product_data,
                        'product_id' => $product_id,
                        'id' => $order['id'],
                        'acc_username' => $order['account_username'],
                        'acc_password' => $order['account_password'],
                        'realm' => $order['realm'],
                        'price' => $price,
                        'pro_rata_extra' => $pro_rata_extra,
                        'service_type' => $order['service_type'],
                        'fibre' => $order['fibre'],
                        // showmax comment
                        // 'showmax_subscription' => $order["showmax_subscription"],
                    );
                }
            }

            $act_count = $start + count($orders);
            $service_list = $this->product_model->get_service_data();
            $status_list = $this->product_model->get_status_data();


            if (!empty($success_message))
                $data['success_message'] = $success_message;

            if (!empty($error_message))
                $data['error_message'] = $error_message;


            // enable DELETE button
            $data['enable_delete'] = '1';

            $data['id_user'] = $account_id;
            $data['showing'] = "Showing $start-$act_count of $num_order";
            $data['orders'] = $order_data;
            $data['product'] = $service_list;
            $data['status_data'] = $status_list;
            $data['sidebar'] = TRUE;
            $data['main_content'] = 'admin/view_orders';
            $this->load->view('admin/includes/template', $data);
        } else {
            $msg = 'The order does not exist!';
            $this->session->set_flashdata('error_message', $msg);
            redirect("admin/all_account");
        }
    }

    function filter_service($start = 0)
    {
        $service_list = $this->product_model->get_service_data();
        $status_list = $this->product_model->get_status_data();

        $account_id = $this->session->userdata('user_id');
        $username = $this->user_model->get_user_name_by_id($account_id);

        if (isset($_POST['pro_id'])) {
            $pro_id = $_POST['pro_id'];
        } else {
            $pro_id_ss = $this->session->userdata('current_pro_id');
            if ($pro_id_ss) {
                $pro_id = $pro_id_ss;
            }
        }

        if (isset($_POST['status'])) {
            $status = $_POST['status'];
        } else {
            $status_ss = $this->session->userdata('current_status');
            if ($status_ss) {
                $status = $status_ss;
            }
        }

        $num_per_page = NUM_PER_PAGE;
        $order_data = array();

        if ($pro_id == 'all' && $status == 'all') {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_pro_id', $pro_id);

            $services = $this->user_model->get_orders($username, $num_per_page, $start);
            $num_services = $this->user_model->get_orders_count($username);
            $data['pro_id'] = $pro_id;
            $data['status'] = $status;
        } elseif ($pro_id != 'all' && $status == 'all') {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_pro_id', $pro_id);

            $result = $this->product_model->search_by_product($username, $pro_id, $num_per_page, $start);
            if ($result) {
                $services = $result;
                $num_services = $this->product_model->get_role_count($username, $pro_id);
                $data['pro_id'] = $pro_id;
                $data['status'] = $status;
            } else {
                $services = '';
                $num_services = 0;
                $msg = "No data";
            }
        } elseif ($status != 'all' && $pro_id == 'all') {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_pro_id', $pro_id);

            $result = $this->product_model->search_by_status($username, $status, $num_per_page, $start);
            if ($result) {
                $services = $result;
                $num_services = $this->product_model->get_status_count($username, $status);
                $data['pro_id'] = $pro_id;
                $data['status'] = $status;
            } else {
                $services = '';
                $num_services = 0;
                $msg = "No data";
            }
        } else {
            $this->session->set_userdata('current_status', $status);
            $this->session->set_userdata('current_pro_id', $pro_id);

            $result = $this->product_model->search_by_status_pro($username, $status, $pro_id, $num_per_page, $start);
            if ($result) {
                $services = $result;
                $num_services = $this->product_model->get_status_pro_count($username, $status, $pro_id);
                $data['pro_id'] = $pro_id;
                $data['status'] = $status;
            } else {
                $services = '';
                $num_services = 0;
                $msg = "No data";
            }
        }
        //print_r($num_services);die;
        if (!empty($services)) {
            foreach ($services as $order) {
                //designing...
                $product_id = $order['product'];
                $status = $order['status'];
                $date = $order['date'];
                $product_data = $this->product_model->get_product_data($product_id);
                $price = $order['price'];
                $pro_rata_extra = $order['pro_rata_extra'];

                $order_data[] = array(
                    'user' => $username,
                    'status' => $status,
                    'date' => $date,
                    'product_data' => $product_data,
                    'product_id' => $product_id,
                    'id' => $order['id'],
                    'acc_username' => $order['account_username'],
                    'acc_password' => $order['account_password'],
                    'price' => $price,
                    'pro_rata_extra' => $pro_rata_extra,
                );
            }
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/filter_service');
        $config['total_rows'] = $num_services;
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
        $act_count = $start + count($services);
        $start = $start + 1;
        $data['showing'] = "Showing $start-$act_count of $num_services";
        $data['orders'] = $order_data;
        $data['product'] = $service_list;
        $data['status_data'] = $status_list;
        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/view_orders';
        $this->load->view('admin/includes/template', $data);
    }

    function cancel_order($order_id)
    {
        if (trim($order_id) != '') {
            $this->order_model->delete_order($order_id);
            $order_data = $this->order_model->get_order_data($order_id);
            $username = $order_data['user'];
            $account_id = $this->membership_model->get_user_id($username);
            if ($account_id) {
                $msg = 'The order has been successfully cancelled!';
                $this->session->set_flashdata('success_message', $msg);
                redirect("admin/user_service");
            } else {
                $msg = 'Failed to cancel the order!';
                $this->session->set_flashdata('error_message', $msg);
                redirect("admin/all_account");
            }
        } else {
            $msg = 'Failed to cancel the order!';
            $this->session->set_flashdata('error_message', $msg);
            redirect("admin/all_account");
        }
    }

    function edit_order($order_id)
    {
        if (trim($order_id) != '') {
            $data['product_list'] = $this->product_model->get_product_list();
            $data['order_id'] = $order_id;
            $order_data = $this->order_model->get_order_data($order_id);

            if (isset($order_data['user'])) {
                $user = $order_data['user'];
            } else {
                $user = '';
            }
            $account_id = $this->membership_model->get_user_id($user);
            $user_name = $this->user_model->get_user_name($user);
            $comment = '';
            if (isset($order_data['product'])) {
                $product = $order_data['product'];
                $product_name = $this->product_model->get_product_name($product);
                $default_comment = $this->product_model->get_default_comment($product);
                $amount = $order_data['price'];
                $comment = $default_comment;
                $comment = str_replace('[product_name]', $product_name, $comment);
                $comment = str_replace('[Name_Surname]', $user_name, $comment);
                $comment = str_replace('[amount]', 'R' . $amount, $comment);
            }
            $data['user'] = $user;
            $data['user_name'] = $user_name;
            $data['user_id'] = $account_id;

            $data['order_data'] = $order_data;
            //$data['order_data']['account_comment'] = $comment;
            $data['sidebar'] = TRUE;
            $data['order_key'] = $this->order_model->order_key();
            $data['main_content'] = 'admin/edit_order';
            $suc_msg = $this->session->flashdata('success_message');
            $data['messages']['success_message'] = $suc_msg;
            $this->load->view('admin/includes/template', $data);
        } else {
            $msg = 'Failed to edit the order!';
            $this->session->set_flashdata('error_message', $msg);
            redirect("admin/all_account");
        }
    }

    function edit_lte_order($id)
    {
        if (trim($id) == '') {
            redirect("admin/all_account");
        }

        $formData = $this->input->post();
      
        if (!empty($formData)) { 
            $orderData = $this->order_model->get_fibre_data_by_order($id); 
            $orderSumData = $this->order_model->get_order_data($id);
            $data['order_data'] = $this->order_model->lte_form_data($orderData, $orderSumData); 
            $this->order_model->update_lte_order($formData, $data['order_data']);
        }

        $orderData = $this->order_model->get_fibre_data_by_order($id);

        $orderSumData = $this->order_model->get_order_data($id);

        $data['order_data'] = $this->order_model->lte_form_data($orderData, $orderSumData);

        $data['order_id'] = $orderData['order_id'];
        $data['show_percentage'] = false;
        if ($this->session->userdata('role') == 'super_admin') {
            $data['show_percentage'] = true;
        }

        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/edit_lte_order';
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $this->load->view('admin/includes/template', $data);
    }
    function edit_mobile_order($id)
    {
        if (trim($id) == '') {
            redirect("admin/all_account");
        }

        ///post request
        $formData = $this->input->post();
        if (!empty($formData)) {
            $orderData = $this->order_model->get_fibre_data_by_order($id);
            $orderSumData = $this->order_model->get_order_data($id);
            $data['order_data'] = $this->order_model->mobile_form_data($orderData, $orderSumData);
            $this->order_model->update_mobile_order($formData, $data['order_data']);
        }

        $orderData = $this->order_model->get_fibre_data_by_order($id);

        $orderSumData = $this->order_model->get_order_data($id);




        $data['order_data'] = $this->order_model->mobile_form_data($orderData, $orderSumData);

        $data['order_id'] = $orderData['order_id'];
        // $data['show_percentage'] = false;
        // if ($this->session->userdata('role') == 'super_admin') {
        //     $data['show_percentage'] = true;
        // }

        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/edit_mobile_order';
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $this->load->view('admin/includes/template', $data);
    }


    function edit_showmax_order($order_id)
    {

        // Order validation :
        // ---------------------------------------------
        $this->load->model("showmax_manager");

        // check order id
        if (!$this->form_validation->numeric($order_id)) {
            redirect("admin/dashboard");
            return;
        }

        // get showmax order and check if it corresponds to order_id
        // (return array with fields  "order_data", "showmax_subscription",  "validation_result" )
        $order_storage = $this->order_model->validate_showmax_subscription_order($order_id, $this->showmax_manager);
        if (!$order_storage["validation_result"]) {
            redirect("admin/dashboard");
            return;
        }
        // -----------------------------------------------
        // send data to viewer
        $data['order_id'] = $order_id;
        $data['user_name'] = $order_storage["showmax_subscription"]["user"];
        $data['user_id'] = $order_storage["showmax_subscription"]["id_user"];
        $data['order_data'] = $order_storage["showmax_subscription"];
        $data['sidebar'] = TRUE;

        // get required variables from showmax_manger model
        $this->showmax_manager->prepare_data_for_subscription_edit($data);

        $data['main_content'] = 'admin/edit_showmax_order';
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;
        $this->load->view('admin/includes/template', $data);


        // Perhapse I whould implement "Order doesn't exist" message/redirection/error later
        // $msg = 'Failed to edit the order!';
        // $this->session->set_flashdata('error_message', $msg);
        // redirect("admin/all_account");
    }

    function update_showmax_order($order_id)
    {

        // Order validation :
        // ---------------------------------------------
        $this->load->model("showmax_manager");

        // check order id
        if (!$this->form_validation->numeric($order_id)) {
            redirect("admin/dashboard");
            return;
        }

        // get showmax order and check if it corresponds to order_id
        // (return array with fields  "order_data", "showmax_subscription",  "validation_result" )
        $order_storage = $this->order_model->validate_showmax_subscription_order($order_id, $this->showmax_manager);
        if (!$order_storage["validation_result"]) {
            redirect("admin/dashboard");
            return;
        }
        // -----------------------------------------------
        // process variables from request
        $this->showmax_manger->validate_request_for_subscription_update($request_data, $validation_model);


        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        // Post validation by fields



        /*
      Array
      (
      [id] => 12810
      [account_id] => 8901
      [activation_code] => Dummy value for 'activation_code' key
      [subscription_type] => premium
      [subscription_status] => suspended
      [subscription_suspend_type] => true
      )

      Array
      (
      [id] => 12810
      [account_id] => 8901
      [activation_code] => Dummy value for 'activation_code' key
      [subscription_type] => premium
      [subscription_status] => active
      [subscription_suspend_type] => true
      )


     */


        // send variables to special handler
    }

    function save_order()
    {
        if (isset($_POST['id'])) {
            $order_id = $_POST['id'];
            $account_id = isset($_POST['account_id']) ? $_POST['account_id'] : '';
            $username = isset($_POST['user']) ? $_POST['user'] : '';
            $product_id = isset($_POST['product']) ? $_POST['product'] : '';
            $price = isset($_POST['price']) ? $_POST['price'] : '';

            if (isset($_POST['change_flag'])) {
                $change_flag = $_POST['change_flag'];
            } else {
                $change_flag = 0;
            }

            $product = $this->product_model->get_product_name($product_id);
            $full_name = $this->membership_model->get_user_name_nice($username);
            $comment = $full_name . "(Client)(R" . $price . " - " . $product . ")(DEBIT ORDER)";

            $order_key = $this->order_model->order_key();
            $order_data = array();

            foreach ($order_key as $k => $n) {
                if ($k == 'account_password') {
                    if ($_POST[$k] && trim($_POST[$k]) != '') {
                        if (trim($_POST[$k]) != $_POST['hidden_password']) {
                            $new_pass = $_POST[$k];
                            $order_data[$k] = $new_pass;
                            $order_data['account_username'] = trim($_POST['hidden_username']);
                        }
                    }
                } elseif ($k == 'account_username') {
                    if ($_POST[$k] && trim($_POST[$k]) != '') {
                        if (trim($_POST[$k]) != $_POST['hidden_username']) {
                            $new_username = $_POST[$k];
                            $order_data[$k] = $new_username;
                            $order_data['account_password'] = trim($_POST['hidden_password']);
                        }
                    }
                } elseif ($k == 'change_flag') {
                    $order_data[$k] = $change_flag;
                } else {
                    if (isset($_POST[$k])) {
                        $order_data[$k] = $_POST[$k];
                    } else {
                        $order_data[$k] = '';
                    }
                }
            }
            if (trim($_POST['account_password']) != '' && (trim($_POST['account_password']) != trim($_POST['hidden_password']))) {
                $order_data['account_password'] = trim($_POST['account_password']);
            }
            if (trim($_POST['account_username']) != '' && (trim($_POST['account_username']) != trim($_POST['hidden_username']))) {
                $order_data['account_username'] = trim($_POST['account_username']);
            }

            $this->order_model->update_order($order_id, $order_data);
            $msg = 'This order has been successfully updated!';
            $this->session->set_flashdata('success_message', $msg);
            redirect("/admin/user_service");
        }
    }

    function create_email()
    {
        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/email/create_email';
        $this->load->view('admin/includes/template', $data);
    }

    function add_email()
    {
        if (isset($_POST)) {
            $purpose = $_POST['purpose'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            $email = $_POST['email'];

            $email_data = array(
                'title' => $title,
                'content' => $content,
                'purpose' => $purpose,
                'email_address' => $email,
            );

            $result = $this->db->insert('email_template', $email_data);

            if ($result) {
                $msg = 'The email information has been added successfully.';
                $this->session->set_flashdata(array("success_message" => $msg));
                redirect("admin/all_email");
            } else {
                $msg = 'Failed to add the email information.Please try it agian.';
                $data['sidebar'] = TRUE;
                $data['error_message'] = $msg;
                $data['main_content'] = 'admin/email/create_email';
                $this->load->view('admin/includes/template', $data);
            }
        }
    }

    function all_email()
    {
        $purpose = $this->session->userdata('purpose');

        if ($purpose) {
            $email_detail = $this->user_model->get_email_detail($purpose);

            $template_id = $email_detail[0]['id'];
            if ($email_detail) {
                $data['email_detail'] = $email_detail;

                $email_attach_data = $this->user_model->get_email_attach($template_id);
                if ($email_attach_data) {
                    $data['attach_data'] = $email_attach_data;
                } else {
                    $data['attach_data'] = '';
                }
            } else {
                $data['email_detail'] = '';
            }
        }
        $data['current_purpose'] = $purpose;
        $email_list = $this->user_model->get_emails_list();
        $data['emails_list'] = $email_list;

        $suc_msg = $this->session->flashdata('success_message');
        $error_msg = $this->session->flashdata('error_message');
        $data['success_message'] = $suc_msg;
        $data['error_message'] = $error_msg;
        $data['main_content'] = 'admin/email/email_list';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/all_email";
        $data['page_category'] = $this->page_category['messages'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';


        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function select_email($purpose = '')
    {
        $array_items = array('success_message' => '', 'error_message' => '');
        $this->session->unset_userdata($array_items);

        if (isset($_POST['purpose'])) {
            $purpose = $_POST['purpose'];
        }
        $this->session->set_userdata(array("purpose" => $purpose));
        redirect("admin/all_email");
    }

    function edit_email()
    {
        if (isset($_POST)) {
            $id = $_POST['email_id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            $email = $_POST['email'];

            $email_data = array(
                'title' => $title,
                'content' => $content,
                'email_address' => $email,
            );
            $this->db->where('id', $id);
            $result = $this->db->update('email_template', $email_data);
            if ($result == 1) {
                $suc_msg = "The email information has been updated succesfully.";
                $this->session->set_flashdata('success_message', $suc_msg);
            } else {
                $error_msg = "Failed to update the email infromation.Please try it again.";
                $this->session->set_flashdata('error_message', $error_msg);
            }
        }
        redirect("admin/all_email");
    }

    function upload_file()
    {
        $this->load->library('upload');

        $purpose = $this->session->userdata('purpose');
        $email_tempate_id = $this->user_model->get_email_id($purpose);
        $create_time = date('Y-m-d H:i:s', strtotime('now'));
        $path = APPPATH . 'upload/' . $purpose . '/';
        if (!is_dir(APPPATH . 'upload/' . $purpose))
            mkdir(APPPATH . 'upload/' . $purpose, 0777, true);

        $name = pathinfo($_FILES['attachment_file']['name']);
        $extension = $name['extension']; //jpg pdf txt....
        $save_name = $name['basename'];

        $config['upload_path'] = $path;
        $allowed_types = 'pdf|txt|text|php|zip|rar|gif|jpg|jpeg|jpe|png|bmp|tiff|tif|csv|exe|psd|xls|ppt|mp3|wav|html|htm|avi|doc|docx|xlsx|word|xl';
        $config['allowed_types'] = $allowed_types;
        $this->upload->initialize($config);

        if ($this->upload->do_upload('attachment_file')) {
            $save_name = $this->upload->file_name;
            $file_save_path = $path . $save_name;

            $data = array(
                'name' => $name['basename'],
                'path' => $file_save_path,
                'create_date' => $create_time,
                'email_template_id' => $email_tempate_id,
            );

            $result = $this->user_model->add_email_attachment($data);
            if ($result) {
                $suc_msg = "The file has been uploaded successfully.";
                $this->session->set_flashdata('success_message', $suc_msg);
            } else {
                $error_msg = "Failed to upload the file.";
                $this->session->set_flashdata('error_message', $error_msg);
            }
        } else {
            $error_msg = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('error_message', $error_msg['error']);
        }
        redirect('admin/all_email');
    }

    function delete_attach($id)
    {
        if ($id) {
            $path = $this->user_model->get_email_attachement($id);
            $result = $this->user_model->delete_email_attachement($id);
            if ($result) {
                $suc_msg = "The file has been deleted successfully.";
                $this->session->set_flashdata('success_message', $suc_msg);
                unlink($path);
            } else {
                $error_msg = "Failed to delete the file.";
                $this->session->set_flashdata('error_message', $error_msg);
            }
        } else {
            $error_msg = "Invalidate file.Please try it again.";
            $this->session->set_flashdata('error_message', $error_msg);
        }
        redirect('admin/all_email');
    }

    function all_messages()
    {
        $suc_msg = $this->session->flashdata('success_message');
        $error_msg = $this->session->flashdata('error_message');
        $data['success_message'] = $suc_msg;
        $data['error_message'] = $error_msg;

        if (!empty($_POST['category'])) {
            $category = trim($_POST['category']);
            $data['current_category'] = $category;
            $message_list = $this->message_model->get_message_list($category);
            $data['message_list'] = $message_list;
        } else {
            $data['message_list'] = '';
        }

        $messages_category_list = $this->message_model->get_message_category_list();
        $data['messages_category_list'] = $messages_category_list;

        $data['main_content'] = 'admin/message/message_list';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/all_messages";
        $data['page_category'] = $this->page_category['messages'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function edit_message()
    {
        $message_id = $_POST['message_id'];
        $message_content = $_POST['content'];
        $message_data = array(
            'id' => $message_id,
            'content' => $message_content,
        );
        if ($message_id) {
            $result = $this->message_model->update_message_template($message_data);
            if ($result) {
                $success_msg = "The message content has been updated successfully.";
                $this->session->set_flashdata('success_message', $success_msg);
            } else {
                $error_msg = "The message content has been failed to update.";
                $this->session->set_flashdata('error_message', $error_msg);
            }
        }
        redirect('admin/all_messages');
    }

    function search_for_product($start = 0)
    {
        //$pro_name = $_POST['pro_name'];
        if (isset($_POST['pro_name'])) {
            $pro_name = trim($_POST['pro_name']);
            $this->session->set_userdata("acc_pro", $pro_name);
        } else {
            $pro_name = $this->session->userdata('acc_pro');
        }

        $num_per_page = NUM_PER_PAGE;
        $products = $this->product_model->get_search_product($pro_name, $num_per_page, $start);
        $num_product = $this->product_model->get_search_product_count($pro_name);

        if ($products) {
            $products = $products;
        } else {
            $products = '';
            $num_product = 0;
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/search_for_product');

        $config['total_rows'] = $num_product;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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

        $products_data = array();
        if (!empty($products)) {
            foreach ($products as $product) {
                $product_id = $product['id']; //product id
                $name = $product['name']; //product name
                $active = $product['active'];  // visibility  hide or visable
                $price = $product['price']; //price
                $parent_id = $product['parent']; //product class
                $parent = $this->category_model->get_subcategory_name($parent_id);
                $billing_cycle = $product['billing_cycle']; //billing cycle monthly  yearly once-off
                $class = $product['class']; //product class
                $package_speed = $product['package_speed']; //package speed
                $service_level = $product['service_level']; //dervice level
                $recommended_use = $product['recommended_use']; //recommanded use
                $global_backbone = $product['global_backbone']; //global backbone
                $automatic_creation = $product['automatic_creation']; // 0 or 1 manual/auto-create
                $default_comment = $product['default_comment']; //default comment

                $products_data[] = array(
                    'id' => $product_id,
                    'name' => $name,
                    'visibility' => $active,
                    'price' => $price,
                    'product_parent' => $parent,
                    'billing_cycle' => $billing_cycle,
                    'product_class' => $class,
                    'package_speed' => $package_speed,
                    'service_level' => $service_level,
                    'recommended_use' => $recommended_use,
                    'global_backbone' => $global_backbone,
                    'automatic_creation' => $automatic_creation,
                    'default_comment' => $default_comment
                );
            }
        }

        $product_count = count($products);
        $product_ind = $start + $product_count;
        $start = $start + 1;
        $data['product_type'] = 'All ';
        $data['showing'] = "Showing $start-$product_ind of $num_product";
        $data['num_per_page'] = $num_per_page;
        $data['num_product'] = $num_product;
        $data['products'] = $products_data;
        $data['main_content'] = 'admin/products/all_product';
        $data['sidebar'] = TRUE;
        $suc_msg = $this->session->flashdata('success_message');
        $data['messages']['success_message'] = $suc_msg;


        // page category + title + link data
        $data['page_link'] = "/admin/all_product";
        $data['page_category'] = $this->page_category['products'];
        $data['page_title'] = $this->page_category['products'][$data['page_link']];
        $data['sidebar_category'] = 'products';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function manage_invoices()
    {
        $data['main_content'] = 'admin/invoices/manage_invoices';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function create_invoice($username = null)
    {

        $data['username'] = $username;
        $data['product_list'] = $this->product_model->get_product_list();
        $data['user_list'] = $this->user_model->get_user_list();
        $data['main_content'] = 'admin/invoices/create_invoice';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_invoice";
        $data['page_category'] = $this->page_category['invoices'];
        $data['page_title'] = $this->page_category['invoices'][$data['page_link']];
        $data['sidebar_category'] = 'invoices';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function insert_manuall_invoice()
    {
        // echo '<pre>';print_r($_POST);die;


        $user = $_POST['user'];
        $invoices = $_POST['invoices'];

        if (!empty($_POST['Payment'])) {
            $payment = $_POST['Payment'];
        } else {
            $payment = null;
        }
        $user_id = $this->membership_model->get_user_id($user);
        $create_date_p = $_POST['create_date'];
        $create_date = date('Y-m-d', time());

        ///new code is here
        if (isset($create_date_p) && !empty($create_date_p)) {
            $valid_format = $this->validateDate($create_date_p);
            if ($valid_format == true) {
                $create_date = $create_date_p;
            } else { 
                redirect('admin/create_invoice?error=date-format');
            }
        }



        // if (!empty($create_date_p) && (date('Y-m-d H:i:s', strtotime($create_date_p)) == $create_date_p)) {
        //     $create_date = $create_date_p;
        // }

        // echo '<pre>';
        // print_r($create_date);
        // echo '</pre>';
        // die;

        // -----------------------
        // process Custom invoice ID (returns 0 if field is empty)
        $custom_id = $this->validation_model->process_int_post_field('invoice_custom_id');

        // process autoskip ID, validate int value (returns 0 if field is empty)
        $skip_id = $this->validation_model->process_int_post_field('invoice_skip_id');
        $invoce_data = array(
            'user_name' => $user,
            'create_date' => $create_date,
            'type' => 'manuall',
            'user_id' => $user_id,
        );

        // check skip ID and Custom ID
        $invoce_data = $this->order_model->generate_invoice_data_incl_skip_id($invoce_data, $skip_id, $this->user_model->get_invoices_list(1, 0));
        $invoce_data = $this->order_model->generate_invoice_data_incl_custom_id($invoce_data, $custom_id, $this->user_model->get_invoices_list(1, 0));

        // insert data
        $this->db->insert('invoices', $invoce_data);
        $invoice_id = $this->db->insert_id();

        foreach ($invoices as $iv) {
            $des = $iv['des'];
            $price = !empty($iv['price']) ? $iv['price'] : 0;

            $manuall_data = array(
                'description' => $des,
                'amount' => $price,
                'create_date' => $create_date,
                'invoice_id' => $invoice_id,
            );
            $this->db->insert('manuall_invoice', $manuall_data);
        }

        //create pdf file
        $pdf_id = $this->create_manuall_PDF($invoice_id, $user);

        //send email to user
        if (isset($_POST['send_invoice'])) {
            $this->order_model->email_invoices_individual($user, $pdf_id);
        }

        if ($pdf_id) {
            $msg = 'The Invoice has been saved successfully';
            $this->session->set_flashdata('success_message', $msg);
        } else {
            $msg = 'Failed to save the Invoice.';
            $this->session->set_flashdata('error_message', $msg);
        }
        redirect('admin/all_invoices');
    }

    function validateDate($date)
    {
        $flag = false;
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    // debug PDF invoice
    function debugPDF()
    {

        show_404();
        die();
        $invoice_id = '54805716';
        $username = 'test-vvv';
        $product_name = "TopUp 32";
        $price = "45.56";


        $this->create_manuall_PDF($invoice_id, $username);
    }

    function debugPDFModel()
    {

        show_404();
        die();

        $username = 'test-vvv';
        $user_id = '8901';
        $month_invoice_id = '159667'; // test-vvv month invoice (mar/2016)

        $user_data = $this->user_model->get_user_data_by_username_and_id($username, $user_id);
        // $result = $this->order_model->create_month_invoice_pdf_hash($username, $user_id, $month_invoice_id, $user_data);
        $result = $this->order_model->create_month_invoice_pdf($username, $user_id, $month_invoice_id, $user_data);

        var_dump($result);
    }

    function create_manuall_PDF($invoice_id, $username)
    {
        // echo 'wait';
        // die;
        $invoice_data = $this->user_model->get_manuall_invoice($invoice_id);
        $create_date = date('Y-m-d H:i:s', time());
        if (isset($invoice_data[0]['create_date']))
            $create_date = $invoice_data[0]['create_date'];

        //user billing data
        $user_list = $this->user_model->get_user_data($username);
        //   echo '<pre>';print_r($user_list);die;
        $user_billing = $user_list['user_billing'];
        if ($user_billing) {
            $billing_name = $user_billing['billing_name'];
            $user_address = $user_billing['address_1'] . ' ' . $user_billing['address_2'];
            $user_city = $user_billing['city'];
            $user_country = $user_billing['country'];
            $user_province = $user_billing['province'];
            $user_phone = 'Phone: ' . $user_billing['contact_number'];
            $user_p_c = $user_province . ', ' . $user_country;
        } else {
            $billing_name = '';
            $user_address = '';
            $user_city = '';
            $user_country = '';
            $user_province = '';
            $user_phone = '';
            $user_p_c = '';
        }

        $first_name = $user_list['user_settings']['first_name'];
        $last_name = $user_list['user_settings']['last_name'];

        //open ISP data
        $open_ISP = $this->user_model->get_open_ISP();
        // echo '<pre>';print_r($open_ISP);die;
        // $open_name = $open_ISP['name'];
        $open_name = 'Open ISP cc';
        $vat_number = '2005 / 156968 / 23';
        // $vat_number = $open_ISP['vat_number'];

        $country = $open_ISP['country'];
        $province = $open_ISP['province'];
        $address = $open_ISP['address'];

        ///replace address
        $address = str_replace("4420", " ", $address);
        // echo '<pre>';print_r($address);die;

        $phone = $open_ISP['phone'];

        /* $this->load->library('FPDF/fpdf');
      $pdf = $this->fpdf; */
        //$this->load->library('tfpdf/tfpdf');
        //$pdf = $this->tfpdf;
        //require('mc_table.php');
        $this->load->library('tfpdf/MC_Table');
        $pdf = new MC_Table();
        //echo "<pre>";print_r($pdf);die;
        //$month = date('Y-m-d', time());
        $month = date('Y-m-d', strtotime($create_date));
        $title = "Tax Invoice for $username in $month";

        //create PDF file page
        $pdf->AddPage();

        $pdf->SetFont('Arial', '', 20);
        //		$image = base_url().'img/main.png';
        $image = '/home/home/public_html/img/main.png';
        $pdf->Image($image, 70, 5, 60);

        $pdf->SetFont('Arial', '', 20);
        $pdf->SetXY(40, 30);
        $pdf->Cell(20, 8, $title, 'C', true);
        $pdf->Ln();

        //set invoice info
        //$invoice_date = date('d/m/Y', time()) ;
        $invoice_date = date('d/m/Y', strtotime($create_date));
        $invoice_id_format = "Tax Inv # : $invoice_id";
        $invoice_date_format = "Date : $invoice_date";

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20, 4, $invoice_id_format, '', true);
        $pdf->Cell(36, 10, $invoice_date_format, 0, 0, 'R', false, '');
        $pdf->Ln();

        //set open info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20, 4, $open_name, '', true);
        $pdf->Cell(185, 3, $billing_name, 0, 0, 'R', false, '');
        $pdf->Ln();

        ///new line for vat number
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 3, 'Vat Number: 4020230068', '', true);
        // $pdf->Cell(185, 3, $first_name . ' ' . $last_name, 0, 0, 'R', false, '');
        $pdf->Ln();


        $pdf->SetFont('Arial', '', 10);
        // $pdf->Cell(20, 3, INVOICE_ORGANIZATION_ID . $vat_number, '', true); 
        $pdf->Cell(20, 3, 'Company Registration Number: ' . $vat_number, '', true);
        $pdf->Cell(185, 3, $first_name . ' ' . $last_name, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, $address, '', true);
        $pdf->Cell(185, 3, $user_address . ' ' . $user_city, 0, 0, 'R', false, '');

        $pdf->Ln();

        $pdf->Cell(20, 3, $province . ', ' . $country . ', 4420', '', true);
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

        $pdf->Cell(110, 8, "Description", 1, 0, 'C', true);
        $pdf->Cell(45, 8, "Date Created", 1, 0, 'C', true);
        $pdf->Cell(30, 8, "Amount", 1, 0, 'C', true);
        $pdf->Ln();

        $total = 0;
        $pdf->SetWidths(array(110, 45, 30));
        foreach ($invoice_data as $iv) {
            $des = $iv['description'];
            $price = $iv['amount'];
            $date = date('Y-m-d', strtotime($iv['create_date']));
            $cost = 'R ' . $price;
            $total = $total + $price;

            $pdf->Row(array($des, $date, $cost));
        }

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Ln(1);
        $pdf->Cell(185, 8, 'Total: R ' . $total, 0, 0, 'R', true);
        $pdf->Ln();
        // $pdf->Cell(0, 8, INVOICE_VAT_ROW, 0, 0, '', true);
        $pdf->Ln();
        $pdf->Write(8, 'If you are on Debit Order you do not need to pay this invoice.');
        $pdf->Cell(90, 8, 'Banking Details:', 0, 0, 'R', false, '');
        $pdf->Ln();
        $pdf->Write(8, 'Please note, accounts are payable on the 27th of each month,');
        $pdf->Cell(89, 8, 'Bank: ABSA', 0, 0, 'R', false, '');
        $pdf->Ln();
        $pdf->Write(8, 'for the following months access.');
        $pdf->Cell(136, 8, 'Account Number: 4064449626', 0, 0, 'R', false, '');
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

        $path_name = APPPATH . 'PDFfiles/' . $username;
        if (is_dir($path_name) == false) {
            mkdir($path_name, 0777);
        }
        $file_name = $invoice_id . '.pdf';
        $file_save_path = $path_name . '/' . $file_name;
        $pdf->Output($file_save_path, 'F');

        //save the pdf
        $pdf_data = array(
            'name' => $file_name,
            'path' => $file_save_path,
            'create_date' => date('Y-m-d H:i:s', strtotime($create_date)),
            'user_name' => $username,
            'invoices_id' => $invoice_id
        );
        $result = $this->db->insert('invoice_pdf', $pdf_data);
        $pdf_id = $this->db->insert_id();

        $update_invoice = array(
            'invoice_name' => $title,
            //'price' => $total,
        );
        $this->db->where('id', $invoice_id);
        $this->db->update('invoices', $update_invoice);
        return $pdf_id;
    }

    function all_invoices($start = 0)
    {
        $this->load->library('pagination');

        $error_msg = $this->session->flashdata('error_message');
        if ($error_msg) {
            $data['error_message'] = $error_msg;
        }
        $succ_msg = $this->session->flashdata('success_message');
        if ($succ_msg) {
            $data['success_message'] = $succ_msg;
        }

        $num_per_page = NUM_PER_PAGE;

        if (isset($_POST['user']) && !empty($_POST['user'])) {
            $username = $_POST['user'];
            $data['user'] = $username;

            $invoice = $this->user_model->get_invoice_user($username, $num_per_page, $start);
            $inv_count = $this->user_model->get_invoice_count($username);

            if ($invoice) {
                foreach ($invoice as $list) {
                    $inv_id = $list['id'];
                    $inv_title = $list['invoice_name'];
                    $inv_user = $list['user_name'];
                    $date = $list['create_date'];
                    $pdf_path = $this->user_model->get_invoice_pdf_path($inv_id);

                    $pdf_data[] = array(
                        'id' => $inv_id,
                        'user_name' => $inv_user,
                        'invoice_name' => $inv_title,
                        'create_date' => $date,
                        'pdf_path' => $pdf_path,
                    );
                    if ($pdf_data) {
                        $data['invoices'] = $pdf_data;
                    } else {
                        $data['invoices'] = '';
                    }
                }
            } else {
                $data['invoices'] = '';
            }
        } else {
            $invoices_list = $this->user_model->get_invoices_list($num_per_page, $start);
            $inv_count = $this->user_model->get_invoice_list_count();

            foreach ($invoices_list as $list) {
                $inv_id = $list['id'];
                $inv_title = $list['invoice_name'];
                $user_id = $list['user_id'];
                $date = $list['create_date'];
                $pdf_path = $this->user_model->get_invoice_pdf_path($inv_id);

                $pdf_data[] = array(
                    'id' => $inv_id,
                    'user_name' => $this->membership_model->get_user_name($user_id),
                    'invoice_name' => $inv_title,
                    'create_date' => $date,
                    'pdf_path' => $pdf_path,
                );
                if ($pdf_data) {
                    $data['invoices'] = $pdf_data;
                } else {
                    $data['invoices'] = '';
                }
            }
            $data['user'] = '';
        }

        $config['base_url'] = base_url('index.php/admin/all_invoices');
        $config['total_rows'] = $inv_count;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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

        $data['user_list'] = $this->user_model->get_user_list();
        // echo '<pre>';
        // print_r($data['user_list']);
        // echo '</prev>';
        // die;

        $data['main_content'] = 'admin/invoices/all_invoices';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/all_invoices";
        $data['page_category'] = $this->page_category['invoices'];
        $data['page_title'] = $this->page_category['invoices'][$data['page_link']];
        $data['sidebar_category'] = 'invoices';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function delete_invoice($id)
    {
        $pdf_path = $this->user_model->get_invoice_pdf_path($id);
        if ($pdf_path) {
            unlink($pdf_path);
            $this->db->delete('invoice_pdf', array('invoices_id' => $id));
        }
        $this->user_model->delete_invoice($id);
        $msg = 'The Invoice has been deleted successfully';
        $this->session->set_flashdata('success_message', $msg);
        redirect('admin/all_invoices');
    }

    function user_invoices($start = 0)
    {
        $account_id = $this->session->userdata('user_id');
        $username = $this->membership_model->get_user_name($account_id);

        $this->load->library('pagination');
        $num_per_page = NUM_PER_PAGE;
        if ($account_id) {

            $invoice = $this->user_model->get_invoice_user($username, $num_per_page, $start);
            $inv_count = $this->user_model->get_invoice_count($username);

            if ($invoice) {
                foreach ($invoice as $list) {
                    $inv_id = $list['id'];
                    $inv_title = $list['invoice_name'];
                    $inv_user = $list['user_name'];
                    $date = $list['create_date'];
                    $pdf_path = $this->user_model->get_invoice_pdf_path($inv_id);

                    $pdf_data[] = array(
                        'id' => $inv_id,
                        'user_name' => $inv_user,
                        'invoice_name' => $inv_title,
                        'create_date' => $date,
                        'pdf_path' => $pdf_path,
                    );
                    if ($pdf_data) {
                        $data['invoices'] = $pdf_data;
                    } else {
                        $data['invoices'] = '';
                    }
                }
            } else {
                $data['invoices'] = '';
            }

            $data['username'] = $username;
            $config['base_url'] = base_url('index.php/admin/all_invoices');
            $config['total_rows'] = $inv_count;
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

            $data['user_list'] = $this->user_model->get_user_list();
            $data['main_content'] = 'admin/invoices/all_invoices';
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    function send_email($invoice_id)
    {
        $this->db->where('id', $invoice_id);
        $query = $this->db->get('invoices');
        $result = $query->first_row();
        $user = $result->user_name;

        $this->db->where('invoices_id', $invoice_id);
        $pdf_query = $this->db->get('invoice_pdf');
        $pdf_result = $pdf_query->first_row();
        $pdf_id = $pdf_result->id;

        $this->order_model->email_invoices_individual($user, $pdf_id);
        $msg = 'The Invoice has been send successfully';
        $this->session->set_flashdata('success_message', $msg);
        redirect('admin/all_invoices');
    }

    function create_next_invoice()
    {

        die("~");
        // error_reporting(E_ALL);
        $month = date('M/Y', strtotime('+1 month'));

        // get active users
        $user_query = $this->db->query('select distinct id_user, user from orders
                                    where status ="active"');
        $user_list = $user_query->result_array();

        echo count($user_list) . "<br/>";
        echo "<pre>";
        print_r($user_list);
        echo "</pre>";
        die();

        if (!isset($this->user_model))
            $this->load->model('admin/user_model');

        $data = array();
        if ($user_list) {

            // month_invoice_log
            $invoice_log_id = $this->order_model->insert_month_log($month);
            // $index = 0;
            foreach ($user_list as $key => $value) {
                $username = $value['user'];
                $user_id = $value['id_user'];

                $month_invoice_id = $this->order_model->save_montly_inovice($username, $user_id);
                // if returns 'false' - billing data is empty
                $user_data = $this->user_model->get_user_data_by_username_and_id($username, $user_id);

                $pdf_id = $this->order_model->create_month_invoice_pdf($username, $user_id, $month_invoice_id, $user_data);
                $this->order_model->update_invoice_log($invoice_log_id, $month_invoice_id);
                $data[] = $month_invoice_id;
                // $index++;
            }
        }
        echo json_encode($data);
        // echo "done";
        // echo " " . $index;
    }

    // regular page for invoice generation process
    function create_next_invoice_page()
    {
        $other_log = array('Function_name' => 'create_next_invoice_page', 'Url' => $this->uri->uri_string());
        button_log("Check Update Monthly Invoice", $this->session->userdata('username'), $this->session->userdata('role'), json_encode($other_log));

        $month = date('M/Y', strtotime('+1 month'));
        // get active users
        $user_query = $this->db->query('select distinct id_user, user from orders
                                    where status ="active"');
        $user_list = $user_query->result_array();

        $data = array();
        //$user_list = null;
        $viewer_str = '';
        $base_url = base_url();

        if ($user_list) {


            if (!isset($this->user_model))
                $this->load->model('admin/user_model');

            // month_invoice_log
            $invoice_log_id = $this->order_model->insert_month_log($month);

            $success_count = 0;
            $fail_count = 0;
            $index = 0;
            $userListCount = count($user_list);
            $viewer_str .= "<br/><b> total user count : " . $userListCount . "</b>";
            foreach ($user_list as $key => $value) {
                $index++;
                // user data
                $username = $value['user'];
                $user_id = $value['id_user'];


                // save month invoice
                $month_invoice_id = $this->order_model->save_montly_inovice($username, $user_id);
                // if returns 'false' - billing data is empty (FALSE)
                $pdf_result = array();
                try {
                    $user_data = $this->user_model->get_user_data_by_username_and_id($username, $user_id);
                    $pdf_result = $this->order_model->create_month_invoice_pdf_hash($username, $user_id, $month_invoice_id, $user_data);
                } catch (Exception $e) {

                    $pdf_result['result'] = 0;
                    $pdf_result['message'] = $e->getMessage();
                }
                // if something wrong we should remove first month invoice
                if (!$pdf_result['result']) {
                    $fail_count++;
                    // remove previous invoice
                    $viewer_str .= "<br/><p style='color : red'> #"
                        . $index . ",  user : <a href='" . $base_url . "admin/edit_account/" . $user_id . "' target='_blank'>" . $username . " (" . $user_id . ")</a> - pdf was generated unsuccessfully ( " . $pdf_result['message'] . " ) </p>";

                    $this->order_model->remove_montly_inovice($month_invoice_id, $username, $user_id);
                    continue;
                }
                $viewer_str .= "<br/><p style='color : green'> #"
                    . $index . ",  user : <a href='" . $base_url . "admin/edit_account/" . $user_id . "' target='_blank'>" . $username . " (" . $user_id . ")</a> - pdf was generated successfully </p>";
                $this->order_model->update_invoice_log($invoice_log_id, $month_invoice_id);
                $success_count++;
                //$data[] = $month_invoice_id;
            }
        }

        $viewer_str .= "<hr/><br/<br/> success : " . $success_count . "<br/> fail : " . $fail_count;

        $data['report_rows'] = $viewer_str;
        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/dashboard_invoices';
        $data['sidebar_category'] = 'dashboard';

        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function debug_invoices()
    {

        // die();
        //$month_invoice_id = 159163;
        //$month_invoice_id = 159146;
        //$username = 'williamwright';
        $username = 'williewiese';
        $user_id = 18041;
        //$user_id = 19410;
        // $result = $this->order_model->remove_montly_inovice($month_invoice_id, $username, $user_id);
        $month_invoice_id = $this->order_model->save_montly_inovice($username, $user_id);
        $user_data = $this->user_model->get_user_data_by_username_and_id($username, $user_id);
        $result = $this->order_model->create_month_invoice_pdf_hash($username, $user_id, $month_invoice_id, $user_data);

        echo "<pre>";
        var_dump($user_data);
        die("here2");
    }

    function send_invoices()
    {

        die();
        $date = date('M/Y', strtotime('+1 month'));
        $invoice_log_id = $this->order_model->get_invoice_log_id($date);

        if ($invoice_log_id) {
            $invs = $this->order_model->get_month_invoices($invoice_log_id);
            //$invs = explode(',', $_POST['invoices']);

            if (!isset($this->user_model))
                $this->load->model('admin/user_model');

            foreach ($invs as $key => $value) {
                $invoice_id = $value['id'];


                $pdf_path = $this->order_model->get_pdf_path($invoice_id);
                $user_id = $this->order_model->get_invoices_user($invoice_id);

                $user_bulk_invoice_param = $this->user_model->get_user_invoice_mail_param_by_id($user_id);


                if ($user_id && $user_bulk_invoice_param) {
                    $email = $this->membership_model->get_user_email($user_id);
                    $name = $this->membership_model->get_user_name($user_id);

                    $body = "Dear $name,
This is the invoices for $date,
Please check the attachment.
If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";

                    $this->load->library('email');
                    $this->email->from('admin@openweb.com', 'OpenWeb Home');
                    $this->email->to($email);
                    $this->email->subject("Invoice for $date");
                    $this->email->message($body);
                    $this->email->attach($pdf_path);
                    if ($this->email->send()) {
                        $this->order_model->update_month_log($date);
                    }
                }
            }
        }
    }

    function send_invoices_page()
    {

        $other_log = array('Function_name' => 'send_invoices_page', 'Url' => $this->uri->uri_string());
        button_log("Invoice was send", $this->session->userdata('username'), $this->session->userdata('role'), json_encode($other_log));

        $report_string = '';
        $date = date('M/Y', strtotime('+1 month'));
        $invoice_log_id = $this->order_model->get_invoice_log_id($date);
        $month_invs_log = $this->order_model->get_month_log($date);
        $count = 0;

        $data['sidebar'] = TRUE;
        $data['main_content'] = 'admin/dashboard_invoices';
        $data['sidebar_category'] = 'dashboard';

        // check if invoices exist
        if (empty($month_invs_log)) {

            $report_string = "<br/><b>Monthly invocies doesn't exist </b>";
            $data['report_rows'] = $report_string;
            $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
            return;
        }


        // if invoices was sent
        if ($month_invs_log['send_email_status'] == 1) {

            $report_string = "<br/><b>Emails already was sent</b>";
            $data['report_rows'] = $report_string;
            $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
            return;
        }

        // send invocie now
        if ($invoice_log_id) {
            $invs = $this->order_model->get_month_invoices($invoice_log_id);
            //$invs = explode(',', $_POST['invoices']);

            $invoicesCount = count($invs);
            $report_string = "<br/><b> invocies count : " . $invoicesCount . "</b><br/>";

            if (!isset($this->user_model))
                $this->load->model('admin/user_model');

            foreach ($invs as $key => $value) {
                $invoice_id = $value['id'];

                $pdf_path = $this->order_model->get_pdf_path($invoice_id);
                $user_id = $this->order_model->get_invoices_user($invoice_id);
                $username = $this->membership_model->get_user_name($user_id);
                $user_bulk_invoice_param = $this->user_model->get_user_invoice_mail_param_by_id($user_id);

                $report_string .= "<br/><b>user : </b> " . $username . ",  <b>bulk param :</b> " . $user_bulk_invoice_param;
                $report_string .= "<br/><b>pdf_path :</b> " . $pdf_path;


                if ($user_id && $user_bulk_invoice_param) {

                    $email = $this->membership_model->get_user_email($user_id);
                    $user_first_name = $this->membership_model->get_name_by_id($user_id);

                    $name = $user_first_name;
                    if (empty($name))
                        $name = $username;

                    $body = "Dear $name,
This is the invoices for $date,
Please check the attachment.
If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";

                    $report_string .= "<p style='color : green;'><b>email :</b> " . $email . "</p>";
                    $report_string .= "<p style='color : green;'><b>text :</b> " . $body . "</p>";


                    //if ($count > 20)
                    //    continue;
                    //$email = 'test_email';
                    $this->load->library('email');
                    $this->email->from('admin@openweb.com', 'OpenWeb Home');
                    $this->email->to($email);
                    $this->email->subject("Invoice for $date");
                    $this->email->message($body);
                    $this->email->attach($pdf_path);
                    if ($this->email->send()) {
                        $this->order_model->update_month_log($date);
                    }
                    $this->email->clear(TRUE);
                    $count++;
                }

                $report_string .= "<hr/>";
            }
        }

        $report_string .= "<br/><br/> count : " . $count;
        $data['report_rows'] = $report_string;
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
        return;
    }

    function month_invoice()
    {


        $username = $this->site_data['username'];
        $data['user_data']['username'] = $username;

        if (isset($_POST['month_invoice_param'])) {
            $val = $_POST['month_invoice_param'];
            $result = $this->order_model->save_month_invoice_toggle($val);
            if ($result) {
                $data['success_message'] = "Parameter was updated successfully";
            } else {
                $data['error_message'] = "Update failure";
            }
        }


        $data['month_invoice_param'] = $this->order_model->get_month_invoice_toggle();
        $data['main_content'] = 'admin/month_invoice';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function massMailer()
    {
        $this->load->library('email');
        $users = $this->membership_model->get_subscribed_user();
        $email = $this->message_model->get_email_template();

        if ($email) {
            $email_attachment_data = $this->db->where('email_template_id', $email['id']);
            $attac_query = $this->db->get('email_attachment');
            $attac_result = $attac_query->result_array();

            if ($users) {
                foreach ($users as $user => $value) {
                    $content = $email['content'];
                    $content = str_ireplace('[User_Name]', $value['username'], $content);
                    $content = str_ireplace('[First_Name]', $value['first_name'], $content);
                    $content = str_ireplace('[Last_Name]', $value['last_name'], $content);
                    $content = str_ireplace('[Password]', $value['password'], $content);
                    $content = str_ireplace('[Email_Address]', $value['email_address'], $content);
                    $user_id = $value['id'];
                    $content .= "  http://home.openweb.co.za/user/optout/$user_id";

                    $this->email->from($email['email_address'], 'OpenWeb Home');
                    $this->email->to($value['email_address']);
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
        }
    }

    // ====================================================


    function invoice_email($user_id)
    {


        // $this->session->set_userdata(array("manage_flag" => "user_services"));
        $account_id = $this->session->userdata('user_id');
        $username = $this->user_model->get_user_name_by_id($account_id);
        $data['user_data']['edit_user'] = $username;
        $data['user_data']['account_id'] = $account_id;


        if (isset($_POST['invoice_dropdown'])) {
            $invoiceVal = strip_tags(mysql_real_escape_string($_POST['invoice_dropdown']));
            $result = $this->user_model->save_user_invoice_mail_param($username, $invoiceVal);
            if ($result) {
                $data['success_message'] = "Parameter was updated successfully";
            } else {
                $data['error_message'] = "Update failure";
            }
        }

        $user_invoice_mail_param = $this->user_model->get_user_invoice_mail_param($username);
        $data['user_data']['invoice_param'] = $user_invoice_mail_param;


        $data['main_content'] = 'admin/accounts/edit_invoice_email';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function create_topup()
    {
        $data['data_classes'] = $this->product_model->get_classes();


        $data['main_content'] = 'admin/topups/create_topup';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/create_topup";
        $data['page_category'] = $this->page_category['topup'];
        $data['page_title'] = $this->page_category['topup'][$data['page_link']];
        $data['sidebar_category'] = 'topup';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function update_topup()
    {

        $data_array = array();

        // POST HANDLER
        // --------------------------------------------------
        $id = $this->input->post('id_topup', true); // TRUE for XSS filter
        if ($id) {

            $data_array['id'] = $id;
            $data_array['id'] = strip_tags(mysql_real_escape_string($data_array['id']));
            $data_array['id'] = trim($data_array['id']);
        }
        $name = $this->input->post('name_topup', true);
        if ($name) {

            $data_array['name'] = $name;
            $data_array['name'] = strip_tags(mysql_real_escape_string($data_array['name']));
            $data_array['name'] = trim($data_array['name']);
        }

        $description = $this->input->post('description', true);
        if ($description) {

            $data_array['description'] = $description;
            $data_array['description'] = strip_tags(mysql_real_escape_string($data_array['description']));
            $data_array['description'] = trim($data_array['description']);
        }

        // class id
        $class = $this->input->post('class_topup', true);

        if ($class) {

            $data_array['class_id'] = $class;
            $data_array['class_id'] = strip_tags(mysql_real_escape_string($data_array['class_id']));
            $data_array['class_id'] = trim($data_array['class_id']);

            $class_name = $this->product_model->get_class_name($data_array['class_id']);
            $data_array['class_name'] = $class_name;
        }


        $price = $this->input->post('price', true);
        if ($price) {

            $data_array['price'] = $price;
            $data_array['price'] = strip_tags(mysql_real_escape_string($data_array['price']));
            $data_array['price'] = trim($data_array['price']);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $credit_card_payment = $this->input->post('credit_card_payment', true);
        if ($credit_card_payment) {

            $data_array['credit_card_payment'] = $credit_card_payment;
            $data_array['credit_card_payment'] = strip_tags(mysql_real_escape_string($data_array['credit_card_payment']));
            $data_array['credit_card_payment'] = trim($data_array['credit_card_payment']);
        }

        $credit_card_auto_payment = $this->input->post('credit_card_auto_payment', true);
        if ($credit_card_auto_payment) {

            $data_array['credit_card_auto_payment'] = $credit_card_auto_payment;
            $data_array['credit_card_auto_payment'] = strip_tags(mysql_real_escape_string($data_array['credit_card_auto_payment']));
            $data_array['credit_card_auto_payment'] = trim($data_array['credit_card_auto_payment']);
        }

        $debit_order_payment = $this->input->post('debit_order_payment', true);
        if ($debit_order_payment) {

            $data_array['debit_order_payment'] = $debit_order_payment;
            $data_array['debit_order_payment'] = strip_tags(mysql_real_escape_string($data_array['debit_order_payment']));
            $data_array['debit_order_payment'] = trim($data_array['debit_order_payment']);
        }

        $eft_payment = $this->input->post('eft_payment', true);
        if ($eft_payment) {

            $data_array['eft_payment'] = $eft_payment;
            $data_array['eft_payment'] = strip_tags(mysql_real_escape_string($data_array['eft_payment']));
            $data_array['eft_payment'] = trim($data_array['eft_payment']);
        }

        $topup_result = $this->product_model->topup_config_handler($data_array);
        if ($topup_result) {

            // get id for payment methods
            // -----------------------------------------------------------
            $id_for_payment_methods = '';
            if ($id) {
                $id_for_payment_methods = $id;
            } else {
                $id_for_payment_methods = $topup_result;
            }

            // handle payment methods for current id;
            $topup_payments_result = $this->product_model->topup_handle_payment_methods($id_for_payment_methods, $data_array);


            $msg = 'TopUp configuration was saved successfully';
            $this->session->set_flashdata('success_message', $msg);
        } else {
            $msg = 'Failed to save the TopUp configuration';
            $this->session->set_flashdata('error_message', $msg);
        }
        redirect('admin/all_topup');
    }

    function all_topup()
    {

        // Get request

        $topup_name = $this->product_model->process_get_product_request('topup_name');
        $start = $this->product_model->process_get_product_request('page');
        if (empty($start) || ($start < 0))
            $start = 0;


        //$this->session->set_flashdata('flash_topup_name', $topup_name);

        $num_per_page = NUM_PER_PAGE;

        $topup_list = $this->product_model->topup_get_list($num_per_page, $start, $topup_name);
        // $data['product_type'] = 'All ';
        $topup_count = $this->product_model->topup_get_list_count($topup_name);

        // ---------------------------------------------------
        // pagination
        $this->load->library('pagination');
        $config['base_url'] = base_url('/admin/all_topup?');
        if (!empty($topup_name))
            $config['base_url'] = base_url('/admin/all_topup/?topup_name=' . $topup_name);

        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';


        $config['total_rows'] = $topup_count;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = "<ul class='pagination uiflat-mix-pagination'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = "<li>";
        $config['num_tag_close'] = "</li>";
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
        $config['cur_tag_close'] = "</a></li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tag_close'] = "</li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['first_tag_open'] = "<li>";
        $config['last_tag_close'] = "</li>";
        $config['first_tag_close'] = "</li>";

        $this->pagination->initialize($config);
        $data['pages'] = $this->pagination->create_links();



        // --------------
        // SAVE messages
        // --------------

        $data['topup_list'] = $topup_list;


        $suc_msg = $this->session->flashdata('success_message');
        $err_msg = $this->session->flashdata('error_message');
        if (!empty($suc_msg))
            $data['messages']['success_message'] = $suc_msg;

        if (!empty($err_msg))
            $data['messages']['error_message'] = $err_msg;


        $data['topup_name'] = $topup_name;
        $data['main_content'] = 'admin/topups/all_topups';
        $data['sidebar'] = TRUE;



        // page category + title + link data
        $data['page_link'] = "/admin/all_topup";
        $data['page_category'] = $this->page_category['topup'];
        $data['page_title'] = $this->page_category['topup'][$data['page_link']];
        $data['sidebar_category'] = 'topup';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function edit_topup($id)
    {

        if (empty($id)) {

            redirect('admin/create_topup');
            return false;
        }


        // load data of current topup
        $topup_row = $this->product_model->topup_get_config($id);
        $payment_methods = $this->product_model->topup_get_payments($id);


        $data['data_topup_row'] = $topup_row;
        $data['payment_methods'] = $payment_methods;
        $data['data_classes'] = $this->product_model->get_classes();

        $data['main_content'] = 'admin/topups/edit_topup';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function delete_topup($topup_id)
    {

        $username = $this->session->userdata('user_name');

        if (isset($_GET['confirm']) && $_GET['confirm'] == true) {
            $this->product_model->topup_remove_config($topup_id);

            $msg = 'The TopUp has been successfully deleted!';
            $this->session->set_flashdata('success_message', $msg);

            redirect("/admin/all_topup");
        } else {

            $data['main_content'] = 'admin/confirmation';
            $data['confirmation_type'] = 'topup_config_delete';
            $data['topup_id'] = $topup_id;
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    function delete_topup_order($topup_report_id)
    {

        $username = $this->session->userdata('user_name');
        if (isset($_GET['confirm']) && $_GET['confirm'] == true) {
            $this->product_model->topup_remove_order($topup_report_id);

            $msg = 'The TopUp Order has been successfully deleted!';
            $this->session->set_flashdata('success_message', $msg);

            redirect("/admin/topup_reports");
        } else {

            $data['main_content'] = 'admin/confirmation';
            $data['confirmation_type'] = 'topup_order_delete';
            $data['topup_id'] = $topup_report_id;
            $data['sidebar'] = TRUE;
            $this->load->view('admin/includes/template', $data);
        }
    }

    // SHOW all TopUp reports
    function topup_reports()
    {

        // Requests
        $topup_name = $this->product_model->process_get_product_request('topup_name');
        $user_name = $this->product_model->process_get_product_request('user_name');
        $from_date = $this->product_model->process_get_product_request('from_date');
        $to_date = $this->product_model->process_get_product_request('to_date');


        $search_array = array(
            'topup_name' => $topup_name,
            'user_name' => $user_name,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );

        // base       2015-06-23 00:19:15
        // interface  dd-m-Y

        $this->product_model->handle_search_array_for_orders($search_array);

        $start = $this->product_model->process_get_product_request('page');

        if (empty($start) || ($start < 0))
            $start = 0;

        // date format
        // ~~~~~~~~~~~

        $num_per_page = NUM_PER_PAGE;

        $topup_reports_list = $this->product_model->get_topup_report($num_per_page, $start, $search_array);
        $topup_reports_list_count = $this->product_model->get_topup_report_count($search_array);

        // ----------------------------------------------------------------------------------------------------
        // pagination
        $this->load->library('pagination');
        $base_url_for_paginator = base_url('/admin/topup_reports?');
        $base_url_for_paginator = $this->product_model->handle_base_url_for_paginator($search_array, $base_url_for_paginator);

        $config['base_url'] = $base_url_for_paginator;

        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $config['total_rows'] = $topup_reports_list_count;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = "<ul class='pagination uiflat-mix-pagination'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = "<li>";
        $config['num_tag_close'] = "</li>";
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
        $config['cur_tag_close'] = "</a></li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tag_close'] = "</li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['first_tag_open'] = "<li>";
        $config['last_tag_close'] = "</li>";
        $config['first_tag_close'] = "</li>";

        $this->pagination->initialize($config);
        $data['pages'] = $this->pagination->create_links();

        // -------------------------------------------------------------------------------------------------------
        // SAVE messages
        // ------------------------------------------------
        // first+last name for each order

        $array_of_user_names = array();
        $array_of_topup_names = array();
        foreach ($topup_reports_list as $order) {

            $user_id = $order['user_id'];
            $full_user_name = $this->membership_model->get_user_name_nice_by_id($user_id);
            $array_of_user_names[$user_id] = $full_user_name;

            $topup_id = $order['topup_config_id'];
            $topup_info = $this->product_model->topup_get_config($topup_id);
            $topup_name = $topup_info['topup_name'];
            $array_of_topup_names[$topup_id] = $topup_name;
        }
        $data['topup_list'] = $topup_reports_list;
        $data['user_names'] = $array_of_user_names;
        $data['topup_names'] = $array_of_topup_names;


        $suc_msg = $this->session->flashdata('success_message');
        $err_msg = $this->session->flashdata('error_message');
        if (!empty($suc_msg))
            $data['messages']['success_message'] = $suc_msg;

        if (!empty($err_msg))
            $data['messages']['error_message'] = $err_msg;

        $data['search_array'] = $search_array;
        $data['main_content'] = 'admin/topups/topup_report';
        $data['sidebar'] = TRUE;




        // page category + title + link data
        $data['page_link'] = "/admin/topup_reports";
        $data['page_category'] = $this->page_category['topup'];
        $data['page_title'] = $this->page_category['topup'][$data['page_link']];
        $data['sidebar_category'] = 'topup';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function edit_topup_order($topup_id)
    {


        $topup_order_info = $this->product_model->get_single_topup_order($topup_id);

        $user_id = $topup_order_info['user_id'];
        $full_user_name = $this->membership_model->get_user_name_nice_by_id($user_id);

        $topup_id = $topup_order_info['topup_config_id'];
        $topup_info = $this->product_model->topup_get_config($topup_id);




        if ($topup_order_info['api_message'] == 'Empty-ok') {

            $topup_order_info['api_status'] = '1';
            $topup_order_info['api_message'] = $this->is_classes->resp_handler_set_account_class_new($topup_order_info['api_status']);
        }

        if ($topup_order_info['revert_api_message'] == 'Empty-ok') {

            $topup_order_info['revert_api_status'] = '1';
            $topup_order_info['revert_api_message'] = $this->is_classes->resp_handler_set_account_class_new($topup_order_info['api_status']);
        }

        $data['full_user_name'] = $full_user_name;

        $service_order_data = $this->order_model->get_order_data($topup_order_info['order_id']);
        $product_info = $this->product_model->get_product_data($topup_order_info['product_id']);

        $payment_status_list = array('in process' => 'in process', 'completed' => 'completed', 'canceled' => 'canceled');
        $data['payment_status_list'] = $payment_status_list;

        $data['topup_order_info'] = $topup_order_info;
        $data['topup_info'] = $topup_info;
        $data['service_order'] = $service_order_data;
        $data['product_info'] = $product_info;

        $data['main_content'] = 'admin/topups/edit_topup_order';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function update_topup_order()
    {


        $topup_order_id = $this->product_model->process_product_request('topup_order_id');
        $payment_status = $this->product_model->process_product_request('payment_status');

        $revert_flag = $this->product_model->process_product_request('revert_flag');
        $real_revert_flag = false;
        if (!empty($revert_flag) && ($revert_flag == 'yes'))
            $real_revert_flag = true;

        $update_time = date("Y-m-d H:m:i");
        $data = array(
            'payment_status' => $payment_status,
            'last_update_time' => $update_time,
        );


        if ($payment_status == 'canceled' && $real_revert_flag) {

            // revert class to previous
            $revert_array = $this->product_model->get_previous_topup_class($topup_order_id);
            /*

          [class_id] => 2
          [class_name] => ow-hc1
          [type] => topup
         */

            // -------------------------------------- API Session -------------------------------------------
            $adsl_username = $this->product_model->get_adsl_account_topup_order($topup_order_id);
            $account_realm = $this->product_model->get_realm_from_topup_order($topup_order_id);

            // $order_data = $this->order_model->get_order_data($order_id);


            $onceoff_flag = false;
            $service_payment_method = $this->product_model->get_service_cycle_data_by_topup_order($topup_order_id);
            if ($service_payment_method['payment_method'] == 'once-off') {

                $current_date_month = date('Y-m');
                if ($current_date_month > $service_payment_method['order_date']) {

                    $onceoff_flag = true;
                }
            }
            // ------------------------------------------------------------------------------

            if (!$onceoff_flag) {

                $realm_data = $this->realm_model->get_realm_data_by_name($account_realm);

                $rl_user = $realm_data['user'];
                $rl_pass = $realm_data['pass'];
                $sess = 0;
                $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id
                // --------------------------------------  Change current class

                $revert_class_answer = $this->is_classes->set_account_class_new($sess, $adsl_username, $revert_array['class_name']);
                $revert_class_message = $this->is_classes->resp_handler_set_account_class_new($revert_class_answer);
            } else {

                $revert_class_answer = '0';
                $revert_class_message = 'Once-off service - class was not changed';
            }

            $revert_time = date('Y-m-d H:i:s');

            // update revert data
            $api_data_for_update = array(
                'revert_time' => $revert_time,
                'revert_class_id' => $revert_array['class_id'],
                'revert_class_name' => $revert_array['class_name'],
                'revert_type' => $revert_array['type'],
                'revert_api_status' => $revert_class_answer,
                'revert_api_message' => $revert_class_message,
            );
            $api_update_result = $this->product_model->update_topup_order($topup_order_id, $api_data_for_update);
        }

        $update_result = $this->product_model->update_topup_order($topup_order_id, $data);
        if ($update_result) {

            $msg = 'TopUp order was successfully updated!';
            $this->session->set_flashdata('success_message', $msg);
        } else {

            $msg = 'Something went wrong!';
            $this->session->set_flashdata('error_message', $msg);
        }

        redirect('admin/topup_reports');
    }

    function topup_name_validation()
    {


        $answer = true;
        if ($this->input->is_ajax_request()) {


            $topup_id = $this->input->post('topup_id', true);
            $topup_name = $this->input->post('topup_name', true);

            if (!empty($topup_id)) {
            }

            if (!empty($topup_name)) {

                $topup_name = strip_tags(mysql_real_escape_string($topup_name));
                $topup_name = trim($topup_name);
            }

            $answer = $this->product_model->topup_check_name($topup_name, $topup_id);
            $answer = !$answer; // for JQuery Validator , FALSE - if Name already exist
        }
        echo json_encode($answer);
    }

    //  GET DOCUMENT IMAGE
    function get_mobile_document_admin($type, $user_id)
    {


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

        $file_system_path = FCPATH . $file['path'];
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

    function all_mobile_requests($start = 0)
    {

        $num_per_page = NUM_PER_PAGE;
        $this->load->model("user_docs_model");

        $options['num'] = $num_per_page = 5;
        $options['start'] = $start;

        $request_filter = $this->user_docs_model->process_mobile_data_request('request_filter');
        if (empty($request_filter))
            $request_filter = $this->session->userdata('current_request_filter');
        if (empty($request_filter))
            $request_filter = 'all';

        $options['filter'] = $request_filter;

        // get data
        $all_mobile_requests = $this->user_docs_model->get_all_mobile_requests_full($options);
        unset($options['num']);
        unset($options['start']);
        $options['count'] = true;
        $num_requests = $this->user_docs_model->get_all_mobile_requests_full($options);
        $this->session->set_userdata('current_request_filter', $request_filter);
        $data['curr_request_filter'] = $request_filter;

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/all_mobile_requests');

        $config['total_rows'] = $num_requests;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
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
        $data['mobile_data_requests'] = $all_mobile_requests;
        $data['main_content'] = 'admin/mobile_data/all_mobile_requests';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    // -----------------------------------------------------------------------------------------

    function edit_mobile_data_request($request_id)
    {


        if (empty($request_id))
            $this->validation_model->print_404();

        if (!$this->form_validation->numeric($request_id))
            $this->validation_model->print_404();


        $this->load->model("user_docs_model");
        $mobile_request = $this->user_docs_model->get_all_mobile_requests_full(array('request_id' => $request_id));

        if (empty($mobile_request)) {
            $this->validation_model->print_404();
        }

        $success_update_message = $this->session->flashdata('success_update');
        $fail_update_message = $this->session->flashdata('fail_update');

        if (!empty($success_update_message))
            $data['success_message'] = $success_update_message;

        if (!empty($fail_update_message))
            $data['fail_message'] = $fail_update_message;



        $data['mobile_request'] = $mobile_request[0];
        $data['main_content'] = 'admin/mobile_data/mobile_data_form_admin';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    function update_mobile_data_request()
    {

        $this->load->model('user_docs_model');

        $request_id = $this->validation_model->process_post_field('request_id');
        $status = $this->validation_model->process_post_field('status');
        $notice = $this->validation_model->process_post_field('notice');
        $mobile_sim = $this->validation_model->process_post_field('mobile_sim');
        $mobile_details = $this->validation_model->process_post_field('mobile_details');

        $data = array(
            'status' => $status,
            'notice' => $notice,
            'mobile_sim' => $mobile_sim,
            'mobile_details ' => $mobile_details,
        );


        $update_result = $this->user_docs_model->update_mobile_request($request_id, $data);

        if ($update_result) {

            $this->session->set_flashdata('success_update', 'Request was updated successfully');
        } else {
            $this->session->set_flashdata('fail_update', 'Update failed');
        }

        redirect('admin/edit_mobile_data_request/' . $request_id);
    }

    function reset_port($order_id)
    {


        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);

        if (empty($order_id) || !$this->form_validation->numeric($order_id)) {
            redirect('user/active_orders');
            return;
        }

        $this->load->model('port_model');
        //$reset_result = $this->port_model->process_port_reset($order_id, 1); // send links for models (is_admin = 1)
        $reset_result = $this->port_model->wrap_port_reset($order_id, 1, false);

        //$msg = 'Port was reset unsuccessfully'; // TODO : change error message
        $msg = $reset_result['message'];
        $type_of_message = 'error_message';
        if ($reset_result['result'])
            $type_of_message = 'success_message';


        $this->session->set_flashdata($type_of_message, $msg);
        redirect("/admin/user_service");
    }

    // =========================debug ========================================================================


    function debug_update_ionline_realms()
    {

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $this->load->model("ionline_api_model");
        $result = $this->ionline_api_model->update_ionline_realms_table();

        var_dump($result);
    }

    function debug_update_ionline_products()
    {

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $this->load->model("ionline_api_model");
        $result = $this->ionline_api_model->update_ionline_products_table();

        var_dump($result);
    }

    function debug_get_all_accounts()
    {

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $this->load->model("network_api_handler_model");
        $result = $this->network_api_handler_model->get_all_accounts($apiName = 'ionline', array(
            //    'limit' => '20',
            //    'skip'  => '5',
            'filters' => array(
                //product_id' => '25',+ other fields
            )
        ));

        var_dump($result);
    }

    function debug_payfast_api_network()
    {


        die();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // load payfast model
        $this->load->model('payfast_model');


        $pre_order_array = array();
        $order_id = null;
        // get pre order
        $pre_order_array = $this->payfast_model->get_pre_order(47);
        $order_id = '12795';
        var_dump($pre_order_array);
        //die();
        $account_result = $this->payfast_model->isdsl_create_account($pre_order_array, $order_id);
    }

    function debug_update_isdsl_classes_new()
    {
        $realmsArray = array(
            "mynetwork.co.za", "fastadsl.co.za", "platinum.co.za",
            "openweb.adsl", "openweb.co.za", "ow.adsl", "dslheaven.co.za",
            "openweb.adsl.co.za", "openweb"
        );
        $this->load->model("network_api_handler_model");

        $this->network_api_handler_model->set_rewrite_api("isdsl");
        foreach ($realmsArray as $realm) {
            $order_data = array(
                "realm" => $realm,
                "account_username" => "some-username",
            );



            $classes_update_result = $this->network_api_handler_model->update_classes($order_data);
            echo "<pre>";
            print_r($classes_update_result);
            echo "</pre>";
            echo "<hr/> - " . $realm;
            $classes_update_result = null;
        }
        die(' - end - ');
    }

    function debug_new_api_network()
    {
        die();
        $this->load->model("network_api_handler_model");


        $order_data_isdsl = array("realm" => "mynetwork.co.za", "account_username" => "test_api_handler_is");
        $order_data_isdsl2 = array("realm" => "mynetwork.co.za", "account_username" => "testapihandleris2");
        $order_data_isdsl3 = array("realm" => "mynetwork.co.za", "account_username" => "testapihandleris3");
        $order_data_isdsl4 = array("realm" => "openweb.adsl", "account_username" => "testapihandleris4");
        $order_data_isdsl5 = array("realm" => "openweb.adsl", "account_username" => "test6764");
        $order_data_isdsl6 = array("realm" => "openweb.adsl", "account_username" => "test6764-test43");

        $order_data_isdsl7 = array("realm" => "mynetwork.co.za", "account_username" => "test-api-call-a");
        $order_data_isdsl8 = array("realm" => "openweb.adsl", "account_username" => "test-api-call-b");
        $order_data_isdsl9 = array("realm" => "mynetwork.co.za", "account_username" => "test-api-call-d");

        $order_data_isdsl10 = array("realm" => "openweb.co.za", "account_username" => "test-api-call-opcz");

        $order_data_ionline = array("realm" => "mynetwork.co.za", "account_username" => "test_api_handler_io");

        $order_data_isdsl11 = array("realm" => "mynetwork.co.za", "account_username" => "test-api-call-32");
        $order_data_isdsl12 = array("realm" => "openweb.adsl", "account_username" => "test-api-call-33");
        $order_data_isdsl13 = array("realm" => "dslheaven.co.za", "account_username" => "test-api-call-34");

        $order_data_isdsl14 = array("realm" => "mynetwork.co.za", "account_username" => "test-api-call-35");
        $order_data_isdsl15 = array("realm" => "openweb.adsl", "account_username" => "test-api-call-35o");

        $order_data_isdsl16 = array("realm" => "mynetwork.co.za", "account_username" => "test-api-call-36");
        $order_data_isdsl17 = array("realm" => "mynetwork.co.za", "account_username" => "test-api-call-37");

        $order_data_ionline = array("realm" => "mynetwork.co.za", "account_username" => "test_api_handler_io");
        ///$order_data_isdsl = array("realm" => "mynetwork.co.za", "account_username" => "test_api_handler_is_test");
        $order_data = $order_data_isdsl14;

        $order_data = $order_data_isdsl16;
        $order_data = $order_data_isdsl17;
        $order_data = array(
            "account_username" => "test-isdsl-test-local33",
            "realm" => "openweb"
        );
        $class = "ow-stduc-20m";
        $pass = "test1235";
        $comment = "testing";
        $email = "baf4mail@gmail.com";

        $new_password = "54321-abcd";
        $new_comment = "test comment2";
        $order_data = array("account_username" => "pc2513", "realm" => "openweb");
        $order_data = array("account_username" => "test-user-3234-1", "realm" => "openweb");
        $order_data = array("account_username" => "test-user-3234-2", "realm" => "openweb");

        // ACCOUNT INFO
        echo date("Y-m-d H:i:s");
        $account_info = $this->network_api_handler_model->get_user_info($order_data);
        echo "<pre>";
        print_r($account_info);
        echo "</pre>";
        echo "<hr/>";
        die();
        $session_result = $this->network_api_handler_model->get_session_info($order_data);
        echo "<pre>";
        print_r($session_result);
        echo "</pre><hr/>";

        $usage_param = array(
            "period" => "2017-05-31",
            "activity_type" => "year",
            //"activity_type" => "month",
            //"activity_type" => "day",
        );
        $activity_result = $this->network_api_handler_model->get_activity_info($order_data, $usage_param);
        echo "<pre>";
        print_r($activity_result);
        echo "</pre>";
    }

    function debug_showmax_api()
    {

        // Showmax_api_model
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $user_id = 8901; // test-vvv id

        $this->load->model("showmax_manager");
        $subscription_type = "premium";

        $activation_result = $this->showmax_manager->activate_showmax_subscription($user_id, $subscription_type);
        var_dump($activation_result);
        die("showmax manager -> activate sm subscription fin");
    }

    function debug()
    {
        die();
        $topup_order_id = '8';
        $result_array = $this->product_model->get_service_cycle_data_by_topup_order($topup_order_id);

        echo "<pre>";
        print_r($result_array);
        echo "</pre>";
        die;
        $answer = utf8_decode('Ð”Ð¸ÑÐºÑ€ÐµÑ‚Ð½Ð° Ð¼Ð°Ñ‚ÐµÐ¼Ð°Ñ‚Ð¸ÐºÐ°');
        echo $answer;
        die;
        //echo dirname(__FILE__)  . DIRECTORY_SEPARATOR . '..' ."/logs/invoice_cron_log.txt";

        $str = 'test';
        $log_file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . "/logs/test-log-text.txt";
        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, "\n " . $str);
        fclose($log_handle);

        echo $log_file_path;
    }

    function debug_payfast_log()
    {
        //phpinfo();
        die();

        $month = date('M/Y', strtotime('+1 month'));
        // get active users
        $user_query = $this->db->query('select distinct id_user, user from orders where status ="active"');
        $user_list = $user_query->result_array();
        $count = count($user_list);

        echo $count;
        die;
        $str = 'ksksks';
        $this->payfast_model->write_log($str);
    }

    // #1 update all classes id for product
    function debug_update_all_products()
    {
        die;
        // fetch all products
        $product_list = $this->product_model->get_all_product_list_with_classes();
        foreach ($product_list as $product) {

            // if class-id is NULL , we try to assign it (if class is not NOSVC)

            if (($product['class_id'] == NULL) && ($product['class'] != 'nosvc')) {

                // get class id
                $class_array = $this->product_model->get_classes_data_by_name($product['class']);

                if ($class_array == false)
                    continue;
                // $class_array['table_id']
                // update class id
                $update_result = $this->product_model->update_product_class_id($product['id'], $class_array['table_id']);

                echo "<br/><br/>  product-id : " . $product['id'] . " | product-name : " . $product['name'];
                echo "<br/>  class-name : " . $product['class'] . " | class id : " . $class_array['table_id'];
                echo "<br/>  update-result : " . $update_result;
            }
        }
    }

    // #2 update_user_class by
    function debug_update_all_users_realm_by_product()
    {

        die;
        // get all users(orders) list
        $full_orders_list = $this->order_model->get_full_order_list();

        // for all orders
        foreach ($full_orders_list as $order) {


            // get order product
            $product_id = $order['product'];

            // get product data (class_id)
            $class_id = $this->product_model->get_product_class_id($product_id);

            // get realm by class_id
            $realm_data = $this->order_model->get_is_details_by_id($class_id);

            // set realm to user
            $update_result = $this->order_model->update_order_realm($order['id'], $realm_data['realm']);
        }
    }

    function debug_check_get_realm()
    {

        die;
        //$order_id = '452';
        // get is details check
        $realm_data = $this->realm_model->get_realm_data_by_name('mynetwork.co.za');

        // $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, 'fhc80');

        echo "<pre>";
        print_r($realm_data);
        echo "</pre>";
    }

    function debug_timezone()
    {

        var_dump(date_default_timezone_get());
        var_dump(date("Y-m-d H:i:s"));
        echo "<br> Africa/Johannesburg : ";
        date_default_timezone_set('Africa/Johannesburg');
        var_dump(date("Y-m-d H:i:s"));
    }

    function debug_timezone_tests()
    {

        die();
        // check timezone for Error
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // calculate countdown
        $assign_date = DateTime::createFromFormat("Y-m-d H:i:s", "2016-07-21 20:30:10");
        $restore_schedule = DateTime::createFromFormat("Y-m-d H:i:s", "2016-08-10 20:30:10");
        $current_time = new DateTime('now - 35 minutes');

        $assign_date_str = $assign_date->format("H:i d/m");
        $countdown = $restore_schedule->diff($current_time);


        $countdown_hours = ceil($countdown->d * 24 + $countdown->h + $countdown->i / 60); // PHP 5.3> . need to check !

        $reset_message = "You can reset your port again in ";
        // hours > 2
        $countdown_message = $reset_message . " " . $countdown_hours . " hours";
        if ($countdown_hours < 2)
            $countdown_message = $reset_message . " a " . $countdown_hours . " hour";

        if ($countdown->m > 0)
            $countdown_message = "The countdown to the next reset more than a month."; // TODO : message

        if ($restore_schedule <= $current_time)
            $countdown_message = "The next reset will be available soon."; // TODO : message




        //$response['message'] = 'The previous reset is not finished yet.';
        $message = "You reset your port on " . $assign_date_str . ". " . $countdown_message;
        var_dump($message);
    }

    function debug_check_account_info()
    {
        die();
        // ---------------------------------------------------------------------------- second
        $username = "test-invoice-3452";
        $realm = 'openweb.adsl';

        $realm_data = $this->realm_model->get_realm_data_by_name($realm);
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $lm = explode('@', $realm_data['user']);
        $realm = $lm[1];
        //$sess = 0;
        //$rl_user = 'administrator';
        //$sess2 = $this->is_classes->is_connect_new($rl_user, $rl_pass);
        $sess = $this->is_classes->is_connect_new_with_handler(array('user' => $rl_user, 'pass' => $rl_pass));

        echo "realm : " . $realm . "<br/>";
        echo $username . "@" . $realm;

        $account_info = $this->is_classes->getAccountInfo_full_new($sess['session'], $username . "@" . $realm);
        //  $account_info = $this->is_classes->delete_account_new($sess, $username . "@" . $realm);

        echo "<pre>";
        print_r($account_info);
        echo "</pre>";
        echo "<br/>" . $account_info['intReturnCode'];
    }

    function debug_check_account_pend_update()
    {

        die();
        $username = 'test-username889387262632';
        $realm = 'openweb.adsl';


        //$realm_data = $this->order_model->get_realm_data_by_order_id($order_id);

        $realm_data = $this->realm_model->get_realm_data_by_name($realm);
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $lm = explode('@', $realm_data['user']);
        $realm = $lm[1];
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

        $resp = 'set update empty';
        $account_pend_info = 'get update empty';

        //  var_dump($rl_user);
        // echo "<br/>" . $username . "@" . $realm . "<br/>";
        //  $resp = $this->is_classes->set_pending_update_new($sess, $username . "@" . $realm, 'nosvc');
        $account_pend_info = $this->is_classes->get_pending_update_new($sess, $username . "@" . $realm);

        // var_dump($resp);

        echo "<pre>";
        print_r($account_pend_info);
        echo "</pre>";
    }

    // =============================== migration-debug ===============================================

    function users_migration_debug($limit, $offset)
    {

        die;
        echo " limit : " . $limit;
        echo "<br/> offset : " . $offset;
        echo "start.";
        //      $all_old_users = $this->migration_model->fetch_all_users_from_user_tbl();
        echo "<pre>";
        //print_r($all_old_users);
        echo "</pre>";

        $this->migration_model->handle_all_old_user_tbl_row($limit, $offset);

        echo "<br/>done.";
    }

    function debug_old_orders($limit, $offset)
    {

        die;
        echo "<br/> limit : " . $limit;
        echo "<br/> offset : " . $offset;

        $this->migration_model->handle_all_old_orders_tbl_row($limit, $offset);


        echo "<br/><br/>end";
    }

    function debug_check_user_id_billing()
    {

        die();
        // add array of id
        // cehck user info by id
        // check billing info by id
        echo "hello";
        $rows = "";

        $query_array = explode("\n", $rows);
        $count = count($query_array);
        unset($query_array[$count - 1]);
        unset($query_array[0]);

        $head1 = "SELECT * FROM `membership` ";
        $head2 = "SELECT * FROM `billing` ";

        echo "<pre>";
        inprint_r($query_array);
        echo "</pre>";

        $query_sub_array = '';
        foreach ($query_array as $row) {
            unset($temp_array);
            $temp_array = explode(",", $row);
            if (!empty($temp_array))
                $query_sub_array[] = $temp_array;
        }

        echo "<hr>";
        echo "<pre>";
        //print_r($query_sub_array);
        echo "</pre>";

        // build 2 queries
        $query1 = $head1;
        $query2 = $head2;

        $i = 1;
        foreach ($query_sub_array as $sub_row) {

            $prefix1 = "OR `id` = ";
            $prefix2 = "OR `id_user` = ";

            if ($i <= 1) {
                $prefix1 = "WHERE `id` = ";
                $prefix2 = " WHERE `id_user` = ";
            }
            $count_sub_row = count($sub_row);
            $query1 .= $prefix1 . $sub_row[20] . " /*" . $i . "*/<br/>";
            $query2 .= $prefix2 . $sub_row[20] . " /*" . $i . "*/<br/>";

            $i++;
        }
        $query1 .= " ORDER BY `id` DESC;";
        $query2 .= " ORDER BY `id_user` DESC;";


        echo "<hr/>";
        echo $query1;
        echo "<br/><br/><br/>";
        echo $query2;
    }

    // str repalce ' for \'
    function debug_restore_backup_orders($start, $limit)
    {
        die();

        $this->load->model('admin/restore_backup_model');
        $this->load->model('admin/realm_model');
        $this->load->model('admin/is_classes');
        // 1. Process each backup order info (limit, sckip)

        $backup_orders = $this->restore_backup_model->select_backup_orders($start, $limit);

        $j = $start;
        foreach ($backup_orders as $backup_order) {

            // 2. get user from backup_membership by id
            $backup_user = $this->restore_backup_model->get_backup_user_by_id($backup_order['id_user']);

            // 3. get user from current_membership by id
            $current_user = $this->restore_backup_model->get_current_user_by_id($backup_order['id_user']);


            echo "<pre>";
            //print_r($backup_order);
            echo "</pre>";

            echo "<br/>/* " . $j . "<br/>";
            echo "Backup order : user - " . $backup_order['user'] . " == product - " . $backup_order['product']
                . " == acc - " . $backup_order['account_username'] . " == realm - " . $backup_order['realm'] . " == id_user - " . $backup_order['id_user'];

            echo "<br/>";
            echo "Backup  user : == " . $backup_user['username'] . " == " . $backup_user['first_name'] . " == " . $backup_user['last_name'] .
                " == " . $backup_user['email_address'] . " == " . $backup_user['ow'];
            echo "<br/>";
            echo "Current user : == " . $current_user['username'] . " == " . $current_user['first_name'] . " == " . $current_user['last_name'] .
                " == " . $current_user['email_address'] . " == " . $current_user['ow'];


            // if user not empty , if  equal : username, OW

            if (!empty($current_user) && ($backup_user['username'] == $current_user['username']) && ($backup_user['ow'] == $current_user['ow'])) {

                // 4. get current order if exist
                $current_order = $this->restore_backup_model->select_current_order_by_user_id($backup_order['id_user'], $backup_order['product'], $backup_order['account_username'], $backup_order['realm']);

                $current_id_check = $this->restore_backup_model->check_current_order_id($backup_order['id']);


                if (empty($current_order) && empty($current_id_check)) {




                    // `date_cancelled`, `date_update`, `date_revoke` modify_service
                    $backup_order_row = $backup_order;
                    if (empty($backup_order_row['date_cancelled']) || ($backup_order_row['date_cancelled'] == '0000-00-00')) {
                        $backup_order_row['date_cancelled'] = 'NULL';
                    } else {
                        $backup_order_row['date_cancelled'] = "'" . $backup_order_row['date_cancelled'] . "'";
                    }

                    if (empty($backup_order_row['date_update']) || ($backup_order_row['date_update'] == '0000-00-00')) {
                        $backup_order_row['date_update'] = 'NULL';
                    } else {
                        $backup_order_row['date_update'] = "'" . $backup_order_row['date_update'] . "'";
                    }

                    if (empty($backup_order_row['date_revoke']) || ($backup_order_row['date_revoke'] == '0000-00-00')) {
                        $backup_order_row['date_revoke'] = 'NULL';
                    } else {
                        $backup_order_row['date_revoke'] = "'" . $backup_order_row['date_revoke'] . "'";
                    }

                    if (empty($backup_order_row['modify_service'])) {
                        $backup_order_row['modify_service'] = 'NULL';
                    }

                    // ISDSL API
                    // =======================================================================================================


                    $username = $backup_order['account_username'];
                    $realm = $backup_order['realm'];

                    //$realm_data = $this->order_model->get_realm_data_by_order_id($order_id);

                    $realm_data = $this->realm_model->get_realm_data_by_name($realm);
                    $rl_user = $realm_data['user'];
                    $rl_pass = $realm_data['pass'];
                    $lm = explode('@', $realm_data['user']);
                    $realm = $lm[1];
                    $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

                    $account_info = $this->is_classes->getAccountInfo_full_new($sess, $username . "@" . $realm);
                    $api_flag = false;

                    if (isset($account_info['intReturnCode']) && ($account_info['intReturnCode'] == '1') && isset($account_info['arrAccountInfo']['Status']) && ($account_info['arrAccountInfo']['Status'] == '1')) {

                        $api_flag = true;
                        if (isset($account_info['arrAccountInfo']['Password'])) {
                            $backup_order_row['account_password'] = $account_info['arrAccountInfo']['Password'];
                        }
                    }
                    // ==================================================================================================

                    if ($api_flag === true) {

                        echo "<br/> ISDSL API : TRUE";
                        // insert backup order
                        echo "<br/> SQL STATEMENT NEXT : */ <br/>";
                        $statement = "INSERT INTO `orders` (`id`, `user`, `product`, `date`, `status`, `price`, `pro_rata_extra`, `account_username`, `account_password`, `account_comment`, `realm`, `change_flag`, `date_cancelled`, `date_update`, `date_revoke`, `type`, `display_usage`, `cancel_flage`, `modify_service`, `id_user`, `payment_method`, `billing_cycle`)";

                        $statement .= "<br/>VALUES (" . $backup_order_row['id'] . ", '" . $backup_order_row['user'] . "',"
                            . $backup_order_row['product'] . ", '" . $backup_order_row['date'] . "', '"
                            . $backup_order_row['status'] . "'," . $backup_order_row['price'] . ","
                            . $backup_order_row['pro_rata_extra'] . ", '" . $backup_order_row['account_username'] . "','"
                            . $backup_order_row['account_password'] . "','" . $backup_order_row['account_comment'] . "','"
                            . $backup_order_row['realm'] . "'," . $backup_order_row['change_flag'] . ","
                            . $backup_order_row['date_cancelled'] . "," . $backup_order_row['date_update'] . ","
                            . $backup_order_row['date_revoke'] . ",'" . $backup_order_row['type'] . "',"
                            . $backup_order_row['display_usage'] . "," . $backup_order_row['cancel_flage'] . ","
                            . $backup_order_row['modify_service'] . "," . $backup_order_row['id_user'] . ",'"
                            . $backup_order_row['payment_method'] . "','" . $backup_order_row['billing_cycle'] . "');";

                        echo "<br/>" . $statement;
                    } else {
                        echo "<br/> ISDSL API : FALSE <br/> */";
                    }
                } else {
                    // skip current order
                    echo "<br/>Current  order : id - " . $current_id_check['id'] . " == user - " . $current_order['user'] . " == product - " . $current_order['product']
                        . " == acc - " . $current_order['account_username'] . " == realm - " . $current_order['realm'] . " == id_user - " . $current_order['id_user'];
                    if (!empty($current_id_check) && empty($current_order))
                        echo "<br/>## id exist but it's different order";

                    echo "<br/> */";
                }
            } else {
                echo "<br/> */";
            }

            echo "<br/><br/>";
            $j++;
        }

        /*
      // 4.
      /* try to get new billing info by old row id / user_id
      eg
      current billing by id :
      current billing by user id
      4 check if credentials (main data) are equal
      5. echo all info but comment it for sql query ()
      6. generate SQL which should update new database to the current state

     */
    }

    function debug_restore_backup_billing($start, $limit)
    {

        die();
        $this->load->model('admin/restore_backup_model');

        /*
      1. Process each backup billing info (limit, sckip)
     */
        $old_billings = $this->restore_backup_model->select_backup_billing($start, $limit);



        $j = $start;
        foreach ($old_billings as $backup_billing) {

            // 2. get user from backup_membership by id
            $backup_user = $this->restore_backup_model->get_backup_user_by_id($backup_billing['id_user']);

            // 3. get user from current_membership by id
            $current_user = $this->restore_backup_model->get_current_user_by_id($backup_billing['id_user']);

            // echo main info abiut billing

            echo "<br/><br/>/* " . $j . "<br/>";
            echo "Backup billing : id - " . $backup_billing['id'] . " == user - " . $backup_billing['username']
                . " == bill-name - " . $backup_billing['billing_name'] . " == email - " . $backup_billing['email']
                . " == number - " . $backup_billing['contact_number'] . " == addr1 - " . $backup_billing['address_1'];


            // echo main info about backup and current users
            echo "<br/>";
            echo "Backup  user : ==  " . $backup_user['id'] . " == " . $backup_user['username'] . " == " . $backup_user['first_name'] . " == " . $backup_user['last_name'] .
                " == " . $backup_user['email_address'] . " == " . $backup_user['ow'] . " == " . $backup_user['mobile_number'];
            echo "<br/>";
            echo "Current user : == " . $current_user['id'] . " == " . $current_user['username'] . " == " . $current_user['first_name'] . " == " . $current_user['last_name'] .
                " == " . $current_user['email_address'] . " == " . $current_user['ow'] . " == " . $current_user['mobile_number'];


            // if user not empty , if  equal : username, OW
            if (
                !empty($current_user) && ($backup_user['username'] == $current_user['username']) && ($backup_user['ow'] == $current_user['ow']) && ($backup_user['first_name'] == $current_user['first_name']) && ($backup_user['last_name'] == $current_user['last_name']) && ($backup_user['mobile_number'] == $current_user['mobile_number']) && ($backup_user['id'] == $current_user['id']) // not sure
            ) {


                // we have found corresponding user
                echo "<br/> ~~~~ coresponding user was found ~~~~~";

                // check if current user already have billing info
                // (get billing info by row_id , user_id,  or_username)
                $current_billing_exist = false;

                // ======= backup row_id
                $current_billing1 = $this->restore_backup_model->get_current_billing_data_by_row_id($backup_billing['id']);
                if (!empty($current_billing1)) {
                    echo "<br/> >> Current billing (by row_id) : id - " . $current_billing1['id'] . " == user - " . $current_billing1['username']
                        . " == bill-name - " . $current_billing1['billing_name'] . " == email - " . $current_billing1['email']
                        . " == number - " . $current_billing1['contact_number'] . " == addr1 - " . $current_billing1['address_1'];

                    $current_billing_exist = true;
                    // exit
                }

                // ======= user_id
                $current_billing2 = $this->restore_backup_model->get_current_billing_data_by_user_id($current_user['id']);
                if (!empty($current_billing2)) {
                    echo "<br/> >> Current billing (by user_id) : id - " . $current_billing2['id'] . " == user - " . $current_billing2['username']
                        . " == bill-name - " . $current_billing2['billing_name'] . " == email - " . $current_billing2['email']
                        . " == number - " . $current_billing2['contact_number'] . " == addr1 - " . $current_billing2['address_1'];

                    $current_billing_exist = true;
                    // exit
                }
                // ======= billing username
                $current_billing3 = $this->restore_backup_model->get_current_billing_data_by_username($backup_billing['username']);
                if (!empty($current_billing3)) {
                    echo "<br/> >> Current billing (by billing_username) : id - " . $current_billing3['id'] . " == user - " . $current_billing3['username']
                        . " == bill-name - " . $current_billing3['billing_name'] . " == email - " . $current_billing3['email']
                        . " == number - " . $current_billing3['contact_number'] . " == addr1 - " . $current_billing3['address_1'];

                    $current_billing_exist = true;
                    // exit
                }


                // ======= membership username
                $current_billing4 = $this->restore_backup_model->get_current_billing_data_by_username($current_user['username']);
                if (!empty($current_billing4)) {
                    echo "<br/> >> Current billing (by membership_username) : id - " . $current_billing4['id'] . " == user - " . $current_billing4['username']
                        . " == bill-name - " . $current_billing4['billing_name'] . " == email - " . $current_billing4['email']
                        . " == number - " . $current_billing4['contact_number'] . " == addr1 - " . $current_billing4['address_1'];

                    $current_billing_exist = true;
                    // exit
                } else {
                    echo "<br/> >> Current billing (by membership_username) : not found ";
                }
                // if billing info not exist => generate query to insert backup info back to DB
                if (!$current_billing_exist) {
                    $backup_billing_row = $backup_billing;
                    if (empty($backup_billing_row['cvc']))
                        $backup_billing_row['cvc'] = 'NULL';

                    // fix username$
                    $backup_billing_row['username'] = str_replace("\\", "", $backup_billing_row['username']);
                    $backup_billing_row['name_on_card'] = str_replace("\\", "", $backup_billing_row['name_on_card']);
                    $backup_billing_row['billing_name'] = str_replace("\\", "", $backup_billing_row['billing_name']);
                    $backup_billing_row['city'] = str_replace("\\", "", $backup_billing_row['city']);
                    $backup_billing_row['address_1'] = str_replace("\\", "", $backup_billing_row['address_1']);
                    $backup_billing_row['address_2'] = str_replace("\\", "", $backup_billing_row['address_2']);
                    $backup_billing_row['province'] = str_replace("\\", "", $backup_billing_row['province']);
                    $backup_billing_row['country'] = str_replace("\\", "", $backup_billing_row['country']);
                    $backup_billing_row['bank_name'] = str_replace("\\", "", $backup_billing_row['bank_name']);

                    $backup_billing_row['username'] = str_replace("'", "\\'", $backup_billing_row['username']);
                    $backup_billing_row['name_on_card'] = str_replace("'", "\\'", $backup_billing_row['name_on_card']);
                    $backup_billing_row['billing_name'] = str_replace("'", "\\'", $backup_billing_row['billing_name']);
                    $backup_billing_row['city'] = str_replace("'", "\\'", $backup_billing_row['city']);
                    $backup_billing_row['address_1'] = str_replace("'", "\\'", $backup_billing_row['address_1']);
                    $backup_billing_row['address_2'] = str_replace("'", "\\'", $backup_billing_row['address_2']);
                    $backup_billing_row['province'] = str_replace("'", "\\'", $backup_billing_row['province']);
                    $backup_billing_row['country'] = str_replace("'", "\\'", $backup_billing_row['country']);
                    $backup_billing_row['bank_name'] = str_replace("'", "\\'", $backup_billing_row['bank_name']);

                    $statement = "INSERT INTO `billing` (`id`, `username`, `sa_id_number`, `expires_month`, `cvc`, `billing_name`, `address_1`, `city`, `province`, `postal_code`, `country`, `email`, `contact_number`, `bank_name`, `bank_account_number`, `bank_account_type`, `bank_branch_code`, `mobile`, `name_on_card`, `card_num`, `id_user`, `address_2`, `expires_year`, `adsl_number`)";
                    // 24 fields
                    $statement .= "<br/>VALUES (" . $backup_billing_row['id'] . ", '" . $backup_billing_row['username'] . "','"
                        . $backup_billing_row['sa_id_number'] . "', '" . $backup_billing_row['expires_mont'] . "',"
                        . $backup_billing_row['cvc'] . ",'" . $backup_billing_row['billing_name'] . "','"
                        . $backup_billing_row['address_1'] . "','" . $backup_billing_row['city'] . "','"
                        . $backup_billing_row['province'] . "','" . $backup_billing_row['postal_code'] . "','"
                        . $backup_billing_row['country'] . "','" . $backup_billing_row['email'] . "','"
                        . $backup_billing_row['contact_number'] . "','" . $backup_billing_row['bank_name'] . "','"
                        . $backup_billing_row['bank_account_number'] . "','" . $backup_billing_row['bank_account_type'] . "','"
                        . $backup_billing_row['bank_branch_code'] . "','" . $backup_billing_row['mobile'] . "','"
                        . $backup_billing_row['name_on_card'] . "','" . $backup_billing_row['card_num'] . "',"
                        . $backup_billing_row['id_user'] . ",'" . $backup_billing_row['address_2'] . "','"
                        . $backup_billing_row['expires_year'] . "','" . $backup_billing_row['adsl_number'] . "');";


                    echo "<br/>" . $statement;
                } else {

                    echo "<br/> */";
                }
            } else {
                echo "<br/>*/";
            }

            $j++;
        }
    }

    //=========================================AVIOS==============================
    public function award_user()
    {

        $this->load->model("avios/avios_main");
        $this->load->library('form_validation');

        $this->form_validation->set_rules('user_id', 'User ID', 'required|integer');
        $this->form_validation->set_rules('points', 'Points', 'required|integer');
        $this->form_validation->set_rules('bonus', 'Bonus', 'required|integer');
        //$this->form_validation->set_rules('billing_code', 'Billing', 'required');

        $user_id = "";
        $points = 0;
        $bonus = 0;

        if ($this->form_validation->run() == TRUE) {

            $bonus_code = "";

            if (isset($_POST['user_id'])) {
                $user_id = $_POST['user_id'];
            }

            if (isset($_POST['points'])) {
                $points = $_POST['points'];
            }

            if (isset($_POST['bonus']) && $_POST['bonus'] > 0) {
                $bonus = $_POST['bonus'];
                $bonus_code = $this->avios_main->bonusBillingCode;
            }

            if (isset($_POST['billing_code'])) {
                $billing_code = $_POST['billing_code'];
            }

            $transact = array(
                "user_id" => $user_id,
                "order_id" => 0,
                "points" => $points,
                "bonus-points" => $bonus,
                "billing-code" => $billing_code,
                'bonus-billing-code' => $bonus_code
            );

            $this->avios_main->giveAviosAward($transact);
            $this->session->set_flashdata('result', 'ok');
            $this->session->set_flashdata('user', $user_id);
            $this->session->set_flashdata('points', $points);
            $this->session->set_flashdata('bonus', $bonus);
            redirect('admin/award_user_form');
        } else {
            $this->session->set_flashdata('err', 'error');
            redirect('admin/award_user_form');
        }
    }

    public function award_user_form()
    {

        switch ($this->session->flashdata('result')) {
            case "ok":
                $data['ok_message'] = "User with id " . $this->session->flashdata('user') .
                    " awarded on " . $this->session->flashdata('points') . " points and " . $this->session->flashdata('bonus') . " bonus points.";
                break;
            case "fileok":
                $data['ok_message'] = "File created and will be send in 10 min";
                break;
        }

        switch ($this->session->flashdata('err')) {
            case "error":
                $data['er_message'] = "Not awarded, error validation fields";
                break;
            case "error_file":
                $data['er_message'] = "File not created. Maybee no awards in system";
                break;
        }

        $data['billing_codes'] = $this->avios_main->billingCodes;

        $data['main_content'] = 'admin/avios/avios_award_form';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    public function avios_bonus()
    {
        $data['main_content'] = 'admin/avios_bonus';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
        //redirect('admin/award_user_form');
    }

    public function create_avios_file()
    {

        if ($this->avios_main->dailyAwardFile()) {
            $this->session->set_flashdata('result', 'fileok');
        } else {
            $this->session->set_flashdata('err', 'error_file');
        }

        redirect('admin/award_user_form');
    }

    public function avios_stat($start = 0)
    {

        $num_per_page = NUM_PER_PAGE;
        //Statistic page data
        $awards = $this->avios_logs->getAllPrepAwards($num_per_page, $start);
        $status_list = $this->avios_logs->get_status_data();
        $biling_list = $this->avios_main->billingCodes;

        $data['award_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/avios_stat');
        $num_account = $this->avios_logs->get_awards_count();
        $config['total_rows'] = $num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a style='text-decoration: underline; font-weight: bold;' href='#'>";
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

        $this->session->set_userdata('current_status', 'all');
        $this->session->set_userdata('current_role', 'all');

        //Prep data
        $awards_count = count($awards);
        $awards_ind = $start + $awards_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$awards_ind of $num_account";
        $data['num_per_page'] = $num_per_page;
        $data['num_account'] = $num_account;
        $data['accounts'] = $awards;
        $data['status_list'] = $status_list;
        $data['billing_list'] = $biling_list;
        $data['current_start_param'] = $start;

        $data['main_content'] = 'admin/avios/avios_stat';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/avios_stat";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_stat';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function avios_sent($sent_start = 0)
    {

        $data['sent']['month_list'] = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        $data['sent']['year_list'] = $this->avios_logs->getAwardYears();
        $data['month'] = date('n');
        $data['current_year'] = date('Y');

        $num_per_page = NUM_PER_PAGE;

        $biling_list = $this->avios_main->billingCodes;
        $sent = $this->avios_logs->getAllSentAwards($num_per_page, $sent_start, null, null, $data['month'], $data['year']);
        $sent_status_list = $this->avios_logs->get_sent_statuses();

        $data['award_type'] = 'All ';

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/avios_sent');
        $sent_num_account = $this->avios_logs->get_sent_awards_count();
        $config['total_rows'] = $sent_num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a style='text-decoration: underline; font-weight: bold;' href='#'>";
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
        $data['sent']['pages'] = $this->pagination->create_links();

        $this->session->set_userdata('current_status', 'all');
        $this->session->set_userdata('current_role', 'all');

        $data['num_per_page'] = $num_per_page;
        $data['billing_list'] = $biling_list;

        //Sent data
        $sent_awards_count = count($sent);
        $sent_awards_ind = $sent_start + $sent_awards_count;
        $sent_start = $sent_start + 1;
        $data['sent']['showing'] = "Showing $sent_start-$sent_awards_ind of $sent_num_account";
        $data['sent']['num_account'] = $sent_num_account;
        $data['sent']['accounts'] = $sent;
        $data['sent']['status_list'] = $sent_status_list;
        $data['sent']['current_start_param'] = $sent_start;

        $data['main_content'] = 'admin/avios/avios_sent';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/avios_sent";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus']["/admin/avios_stat"];
        $data['sidebar_category'] = 'avios_stat';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function filter_avios_award($start = 0)
    {

        $status_list = $this->avios_logs->get_status_data();
        $biling_list = $this->avios_main->billingCodes;

        if (isset($_POST['billing'])) {
            $role = $_POST['billing'];
        } else {
            $role_ss = $this->session->userdata('current_role');
            if ($role_ss) {
                $role = $role_ss;
            } else {
                $role = null;
            }
        }

        if (isset($_POST['status'])) {
            $status = $_POST['status'];
        } else {
            $status_ss = $this->session->userdata('current_status');
            if ($status_ss) {
                $status = $status_ss;
            } else {
                $status = null;
            }
        }

        $conditions = ['status' => $status, 'billing_code' => $role];

        $num_per_page = NUM_PER_PAGE;
        $account_data = array();

        $this->session->set_userdata('current_role', $role);
        $this->session->set_userdata('current_status', $status);

        $result = $this->avios_logs->getAllPrepAwards($num_per_page, $start, $status, $role);

        if ($result) {
            $accounts = $result;
            $num_account = $this->avios_logs->get_count($conditions);
            $data['billing'] = $role;
            $data['status'] = $status;
        } else {
            $accounts = '';
            $num_account = 0;
            $msg = "No data";
            $data['billing'] = $role;
            $data['status'] = $status;
        }

        $account_data = $accounts;

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/filter_avios_award');
        $config['total_rows'] = $num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination ">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $account_count = count($accounts);
        $account_ind = $start + $account_count;
        $start = $start + 1;
        $data['showing'] = "Showing $start-$account_ind of $num_account";
        $data['num_per_page'] = $num_per_page;
        $data['num_account'] = $num_account;
        $data['accounts'] = $account_data;
        $data['billing_list'] = $biling_list;
        $data['status_list'] = $status_list;
        $data['main_content'] = 'admin/avios/avios_stat';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/avios_stat";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_stat';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function filter_sent_avios_award($start = 0)
    {

        $status_list = $this->avios_logs->get_sent_statuses();
        $biling_list = $this->avios_main->billingCodes;

        $data['sent']['month_list'] = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        $data['sent']['year_list'] = $this->avios_logs->getAwardYears();

        if (isset($_POST['billing'])) {
            $role = $_POST['billing'];
        } else {
            $role_ss = $this->session->userdata('current_role');
            if ($role_ss) {
                $role = $role_ss;
            } else {
                $role = null;
            }
        }

        if (isset($_POST['status'])) {
            $status = $_POST['status'];
        } else {
            $status_ss = $this->session->userdata('current_status');
            if ($status_ss) {
                $status = $status_ss;
            } else {
                $status = null;
            }
        }

        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        } else {
            $month_ss = $this->session->userdata('current_month');
            if ($month_ss) {
                $month = $month_ss;
            } else {
                $month = null;
            }
        }

        if (isset($_POST['year'])) {
            $year = $_POST['year'];
        } else {
            $year_ss = $this->session->userdata('current_year');
            if ($year_ss) {
                $year = $year_ss;
            } else {
                $year = null;
            }
        }

        $num_per_page = NUM_PER_PAGE;
        $account_data = array();

        $conditions = ['status' => $status, 'billing_code' => $role];

        $num_per_page = NUM_PER_PAGE;
        $account_data = array();

        $this->session->set_userdata('current_role', $role);
        $this->session->set_userdata('current_status', $status);
        $this->session->set_userdata('current_month', $month);
        $this->session->set_userdata('current_year', $year);

        $result = $this->avios_logs->getAllSentAwards($num_per_page, $start, $status, $role, $month, $year);

        if ($result) {
            $accounts = $result;
            $num_account = $this->avios_logs->get_sent_count($conditions, $month, $year);
        } else {
            $accounts = '';
            $num_account = 0;
            $msg = "No data";
        }

        $data['billing'] = $role;
        $data['status'] = $status;
        $data['month'] = $month;
        $data['current_year'] = $year;

        $account_data = $accounts;

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/filter_sent_avios_award');
        $config['total_rows'] = $num_account;
        $config['per_page'] = $num_per_page;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination ">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
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
        $data['sent']['pages'] = $this->pagination->create_links();
        $account_count = count($accounts);
        $account_ind = $start + $account_count;
        $start = $start + 1;
        $data['sent']['showing'] = "Showing $start-$account_ind of $num_account";
        $data['num_per_page'] = $num_per_page;
        $data['sent']['num_account'] = $num_account;
        $data['sent']['accounts'] = $account_data;
        $data['billing_list'] = $biling_list;
        $data['sent']['status_list'] = $status_list;
        $data['main_content'] = 'admin/avios/avios_sent';
        $data['sidebar'] = TRUE;

        // page category + title + link data
        $data['page_link'] = "/admin/avios_sent";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_stat';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function avios_summary()
    {

        $this->load->library('table');

        $data['month_list'] = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        $data['year_list'] = $this->avios_logs->getAwardYears();

        $data['table_data'] = [['Totals', 'Amount', 'Bonus']];

        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        } else {
            $month_ss = $this->session->userdata('current_month');
            if ($month_ss) {
                $month = $month_ss;
            } else {
                $month = date('n');
            }
        }

        if (isset($_POST['year'])) {
            $year = $_POST['year'];
        } else {
            $year_ss = $this->session->userdata('current_year');
            if ($year_ss) {
                $year = $year_ss;
            } else {
                $year = date('Y');
            }
        }

        $month_data = $this->avios_logs->getTotalsMonth($month, $year);

        foreach ($month_data as $row) {
            array_push($data['table_data'], $row);
        }

        $this->session->set_userdata('current_month', $month);
        $this->session->set_userdata('current_year', $year);
        $data['prev_month'] = $month;
        $data['cur_year'] = $year;

        $data['main_content'] = 'admin/avios/avios_sum';
        $data['sidebar'] = TRUE;
        // page category + title + link data
        $data['page_link'] = "/admin/avios_summary";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_stat';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function edit_avios_award($award_id)
    {

        $award_data = $this->avios_logs->get_award_data($award_id)[0];
        $award_data['reason'] = $award_data['billing_code'];

        foreach ($this->avios_main->billingCodes as $key => $val) {
            if ($award_data['billng_code'] == $key) {
                $award_data['reason'] = $val;
            }
        }

        $user_data = $this->membership_model->get_user_data($award_data['user_id']);

        $award_data['first_name'] = $user_data['first_name'];
        $award_data['last_name'] = $user_data['last_name'];
        $award_data['username'] = $user_data['username'];

        $data['billingCodes'] = $this->avios_main->billingCodes;
        $data['award_data'] = $award_data;

        $data['main_content'] = 'admin/avios/avios_edit_award';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/avios_edit_award";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_stat';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function edit_award($id)
    {

        $points = 0;
        $bonus = 0;

        if (isset($_POST['points'])) {
            $points = $_POST['points'];
        }

        if (isset($_POST['bonus'])) {
            $bonus = $_POST['bonus'];
        }

        $billing = $_POST['billing_code'];

        $result = $this->avios_logs->edit_award($id, $points, $bonus, $billing);

        $mes = 'Not updated!';

        if ($result) {
            $mes = 'Updated!';
        }

        $this->session->set_flashdata('mes', $mes);

        redirect('admin/filter_avios_award');
    }

    public function delete_avios_award($id)
    {

        $result = $this->avios_logs->updatePrepareStatus("deleted", $id);

        $mes = "Not deleted!";

        if ($result) {
            $mes = "Deleted";
        }

        $this->session->set_flashdata('mes', $mes);

        redirect("/admin/filter_avios_award");
    }

    public function avios_monthly($start = 0)
    {

        $num_per_page = NUM_PER_PAGE;
        $orders = $this->order_model->get_without_billing_code(10, $start);
        $num_orders = $this->order_model->get_count_withot_billing_code();
        $data['new'] = 5;
        $data['orders'] = $orders ? $orders : [];
        $data['num_per_page '] = $num_per_page;
        $data['num_account'] = $num_orders;
        $data['main_content'] = 'admin/avios/avios_monthly';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/avios_monthly";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_monthly';

        if ($num_orders > 0) {

            if (is_array($orders)) {
                foreach ($orders as &$order) {
                    if ($order['service_type'] != 'adsl') {
                        $row = $this->order_model->get_fibre_data_by_order($order['id']);
                        $order['account_comment'] = $row['product_name'];
                    }
                }
            }
            //$data['award_type'] = 'All ';

            $this->load->library('pagination');
            $config['base_url'] = base_url('index.php/admin/avios_monthly');

            $config['total_rows'] = $num_orders;
            $config['per_page'] = $num_per_page;
            $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = "<li class='active'><a style='text-decoration: underline; font-weight: bold;' href='#'>";
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

            $this->session->set_userdata('current_status', 'all');
            $this->session->set_userdata('current_role', 'all');

            $orders_count = count($orders);
            $orders_ind = $start + $orders_count;
            $start = $start + 1;
            $data['billingCodes'] = [
                '' => '',
                'OPNZAFIBMR' => 'Fibre Monthly Rental',
                'OPNZALINRT' => 'ADSL Line Rental',
                'OPNZAUBAMR' => 'Uncapped ADSL Monthly Rental',
                'OPNZAUBFMR' => 'Uncapped Fibre Monthly Rental',
                'OPNZAMMDPS' => 'Monthly mobile data package subscription',
            ];
            $data['showing'] = "Showing $start-$orders_ind of $num_orders";
            $data['num_per_page'] = $num_per_page;
            $data['num_account'] = $num_orders;
            $data['orders'] = $orders;
            //$data['status_list'] = $status_list;
            //$data['billing_list'] = $biling_list;
            $data['current_start_param'] = $start;
        }
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function award_avios_ajax()
    {

        $this->load->model("avios/avios_main");
        $this->load->model("admin/order_model");

        $user_id = $_POST['user_id'];
        $order_id = $_POST['order_id'];
        $billing = $_POST['billing'];

        $billing_data = [
            'avios_code' => $billing,
        ];

        $this->order_model->addBillingCode($order_id, $billing_data);

        echo "OK";
    }

    public function avios_rules()
    {

        $flag = 0;

        //$insert_rules is array for DB in such way $code_id => {$param => $value}
        if (isset($_POST['OPNZAFIBMR'])) {
            $insert_rules[4]['m_rule'] = $_POST['OPNZAFIBMR'];
            $flag = 1;
        }

        if (isset($_POST['OPNZAUBAMR'])) {
            $insert_rules[9]['m_rule'] = $_POST['OPNZAUBAMR'];
            $flag = 1;
        }

        if (isset($_POST['OPNZAUBFMR'])) {
            $insert_rules[10]['m_rule'] = $_POST['OPNZAUBFMR'];
            $flag = 1;
        }

        if (isset($_POST['OPNZAFIBRT'])) {
            $insert_rules[0]['once_points'] = $_POST['OPNZAFIBRT'];
            $flag = 1;
        }

        if (isset($_POST['OPNZALINRT'])) {
            $insert_rules[5]['once_points'] = $_POST['OPNZALINRT'];
            $flag = 1;
        }

        if (isset($_POST['OPNZAMMDPS'])) {
            $insert_rules[6]['m_rule'] = $_POST['OPNZAMMDPS'];
            $flag = 1;
        }

        if ($flag == 1) {
            $answer = $this->avios_logs->setRules($insert_rules);

            $mes = "Not saved!";
            if ($answer) {
                $mes = "Saved";
            }
        }
        $all_rules = $this->avios_logs->getRules();

        $data['message'] = $mes;
        $data['rules'] = $all_rules;

        $data['main_content'] = 'admin/avios/avios_rules';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/avios_rules";
        $data['page_category'] = $this->page_category['avios_bonus'];
        $data['page_title'] = $this->page_category['avios_bonus'][$data['page_link']];
        $data['sidebar_category'] = 'avios_rules';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function get_nonbilling_count()
    {

        header('Content-Type: application/json');

        echo json_encode(["state" => "OK", "num" => $this->order_model->get_count_withot_billing_code()]);
    }

    public function bulk_sms_page()
    {

        $this->load->model('sms_model');
        $data['credits'] = $this->sms_model->checkCredits();
        $data['user_status'] = $this->user_model->userStatuses();
        $data['product_type'] = $this->product_model->productTypes();
        $data['order_status'] = $this->order_model->orderStatuses();
        $data['groups'] = $this->sms_model->getGroups();

        $data['main_content'] = 'admin/email/bulk_sms';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/bulk_sms_page";
        $data['page_category'] = $this->page_category['bulk_mail'];
        $data['page_title'] = 'Bulk SMS';
        $data['sidebar_category'] = 'bulk_mail';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    public function bulk_mail_cat()
    {
        $data['main_content'] = 'admin/email/bulk_mail_cat';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
        //redirect('admin/bulk_mail');
    }

    public function create_users_list()
    {

        $list = $this->user_model->getUsersByFilters($_POST);
        $filename = date("m-Y-H-i") . ".csv";

        $file_path = FCPATH . "bulk_sms_lists/" . $filename;
        $file = fopen($file_path, "w");

        foreach ($list as $user) {
            fwrite($file, $user['mobile_number'] . "," . $user['username'] . "\n");
        }

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
        fclose($file);
        //not write to file
    }

    public function send_bulk_sms()
    {

        $message = $_POST['message'];
        $group = $_POST['group_name'];

        $this->load->model("sms_model");
        $send = $this->sms_model->sendGroupSMS($group, $message);

        $data['main_content'] = 'admin/email/bulk_sms';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/bulk_sms_page";
        $data['page_category'] = $this->page_category['bulk_mail'];
        $data['page_title'] = 'Bulk SMS';
        $data['sidebar_category'] = 'bulk_mail';
        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function sms_templates()
    {
        $purpose = $this->session->userdata('purpose');

        if ($purpose) {
            $sms_detail = $this->sms_model->get_template($purpose);

            $template_id = $sms_detail[0]->id;
            if ($sms_detail) {
                $data['sms_detail'] = $sms_detail;
            } else {
                $data['sms_detail'] = '';
            }
        }
        $data['current_purpose'] = $purpose;
        $sms_list = $this->sms_model->get_sms_list();
        $data['sms_list'] = $sms_list;
        //var_dump($sms_list);die;
        $suc_msg = $this->session->flashdata('success_message');
        $error_msg = $this->session->flashdata('error_message');
        $data['success_message'] = $suc_msg;
        $data['error_message'] = $error_msg;
        $data['main_content'] = 'admin/email/sms_list';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/sms_templates";
        $data['page_category'] = $this->page_category['messages'];
        $data['page_title'] = $this->page_category['messages'][$data['page_link']];
        $data['sidebar_category'] = 'messages';


        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function select_sms($purpose = '')
    {
        $array_items = array('success_message' => '', 'error_message' => '');
        $this->session->unset_userdata($array_items);

        if (isset($_POST['purpose'])) {
            $purpose = $_POST['purpose'];
        }
        $this->session->set_userdata(array("purpose" => $purpose));
        redirect("admin/sms_templates");
    }

    function edit_sms()
    {
        if (isset($_POST)) {
            $id = $_POST['email_id'];
            $content = $_POST['content'];

            $sms_data = array(
                'body' => $content
            );
            $this->db->where('id', $id);
            $result = $this->db->update('sms_templates', $sms_data);
            if ($result == 1) {
                $suc_msg = "The email information has been updated succesfully.";
                $this->session->set_flashdata('success_message', $suc_msg);
            } else {
                $error_msg = "Failed to update the email infromation.Please try it again.";
                $this->session->set_flashdata('error_message', $error_msg);
            }
        }
        redirect("admin/sms_templates");
    }

    function lte_orders_type()
    {

        $orders = $this->order_model->get_lte_without_type();

        $lte_types = [
            [
                'type' => 'rain',
                'name' => 'RAIN'
            ],
            [
                'type' => 'cell_c',
                'name' => 'Cell C'
            ],
            [
                'type' => 'telkom',
                'name' => 'TELKOM'
            ], [
                'type' => 'mtn',
                'name' => 'MTN'
            ]
        ];

        $data['nem_per_page'] = 10;
        $data['orders'] = $orders;
        $data['lte_types'] = $lte_types;
        $data['main_content'] = 'admin/accounts/lte_orders_type';
        $data['sidebar'] = TRUE;


        // page category + title + link data
        $data['page_link'] = "/admin/manage_users";
        $data['page_category'] = $this->page_category['manage_users'];
        $data['page_title'] = $this->page_category['manage_users'][$data['page_link']];
        $data['sidebar_category'] = 'manage_users';

        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function add_lte_type()
    {

        $id = $_GET['id'];
        $type = $_GET['type'];

        if (isset($id) && isset($type))
            $res = $this->order_model->update_lte_type($id, $type);

        echo json_encode(["answ" => $res, "id" => $id]);
    }

    function get_new_lte_orders()
    {

        $orders = $this->order_model->get_lte_without_type();

        echo json_encode($orders);
    }

    function get_message_count()
    {

        $messages = $this->message_model->get_messages_count();

        echo $messages;
    }

    function get_bulk_email_result()
    {

        $data = $this->message_model->get_bulk_email_result();

        $res["success"] = $data[1]["count"];
        $res["all"] = $data[0]["count"];

        echo json_encode($res);
    }

    function manual_ordering_settings()
    {

        $data['order_types'] = $this->form_builder_model->getOrderTypes();

        $data['main_content'] = 'admin/manual_ordering_settings';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/form_builder";
        $data['page_title'] = 'Form Builder';

        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function getProducts()
    {

        $id = $this->input->get('id', true);
        $products = $this->form_builder_model->getAvailableProducts($id);

        echo json_encode($products);
    }

    function order_form_builder()
    {

        $base_fields = [
            ['name' => 'first_name', 'value' => 'First Name', 'title' => 'First Name'],
            ['name' => 'second_name', 'value' => 'Second Name', 'title' => 'Second Name'],
            ['name' => 'email', 'value' => 'mail@mail', 'title' => 'Email'],
            ['name' => 'phone', 'value' => 'Phone 00000000', 'title' => 'Phone'],
        ];

        $data['selected'] = $this->input->get('id', true);

        $form_id = $this->form_builder_model->getFormId($data['selected']);
        $fields = $this->form_builder_model->getFormFields($form_id);
        $data['form_fields'] = json_decode($fields, true);

        $packages = $this->form_builder_model->getAvailableProducts($data['selected']);
        foreach ($packages as $pack) {

            $data['packages'][$pack['id']] = $pack['name'] . ' - R' . $pack['price'] . " " . $pack['description'];
        }

        $data['type_options'] = [];

        $options = $this->form_builder_model->getOrderTypes();
        foreach ($options as $option) {
            $data['type_options'][$option['id']] = $option['name'];
        }

        $data['base_form_fields'] = $base_fields;
        $data['main_content'] = 'admin/order_form_builder';
        $data['sidebar'] = TRUE;
        $data['page_link'] = "/admin/form_builder";
        $data['page_title'] = 'Form Builder';

        $this->load->view($this->ui_prefix . 'admin/includes/template', $data);
    }

    function saveForm()
    {

        $fields = $this->input->post(null, true);
        $res = $this->form_builder_model->saveNewForm($fields);

        if ($res) {
            echo "ok";
        }
    }

    function saveProductType()
    {

        $data = $this->input->post(null, true);
        $res = $this->form_builder_model->addType($data);
        echo json_encode($res);
    }

    function deleteSpecObject()
    {
        $get = $this->input->get(null, true);

        if ($get['type'] == 't')
            $deleted = $this->form_builder_model->deleteSpecType($get['id']);

        if ($get['type'] == 'p')
            $deleted = $this->form_builder_model->deleteSpecProd($get['id']);

        echo $deleted;
    }

    function saveProduct()
    {
        $data = $this->input->post(null, true);
        $res = $this->form_builder_model->addProduct($data);
        echo json_encode($res);
    }

    function getTypeFields()
    {

        $id = $_GET['id'];

        $data = $this->form_builder_model->getManualOrderTypeData($id);

        echo json_encode($data);
    }

    function getProductFields()
    {

        $id = $_GET['id'];

        $data = $this->form_builder_model->getManualOrderProdData($id);

        echo json_encode($data);
    }

    function editType()
    {

        $post = $this->input->post(null, true);
        $res = $this->form_builder_model->editOrderTypeData($post);

        if ($res) {
            echo "ok";
            die;
        }

        echo "err";
    }

    function editProduct()
    {

        $post = $this->input->post(null, true);
        $res = $this->form_builder_model->editProdTypeData($post);

        if ($res) {
            echo "ok";
            die;
        }

        echo "err";
    }

    public function searchUser()
    {

        $search = $this->input->get('string');
        $jquery = $this->input->get('callback');

        $users = $this->user_model->searchUser($search);
        $response = [];

        foreach ($users as $user) {

            $response[] = [
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'username' => $user['username'],
                'value' => $user['first_name'] . ' ' . $user['last_name'] . '(' . $user['username'] . ')'
            ];
        }
        $response = json_encode($response);
        echo $jquery . '(' . $response . ')';
    }

    function fibre_management()
    {
        $data['main_content'] = 'admin/fiber_management';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function usage_stats_settings()
    {
        $data['main_content'] = 'admin/usage_stats_settings';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function lte_usage_stats_settings()
    {
        $data['main_content'] = 'admin/lte_usage_stats_settings';
        $data['sidebar'] = TRUE;
        $this->load->model('lte_usage_stats_model');
        $data['lte_usage_stats_model'] = $this->lte_usage_stats_model;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function save_lte_usage_stats_settings()
    {
        $new = [];
        foreach ($_POST as $key => $value) {
            $new[] = $key;
        }

        $this->load->model('lte_usage_stats_model');
        $this->lte_usage_stats_model->save($new);

        redirect('/admin/lte_usage_stats_settings');
    }

    function create_new_lte_account()
    {
        $data['main_content'] = 'admin/create_new_lte_account';
        $data['sidebar'] = TRUE;
        $data['messages']['success_message'] = $this->session->flashdata('success_message');
        $data['messages']['error_message'] = $this->session->flashdata('error_message');

        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function submit_create_new_lte_account()
    {
        $this->load->model("network_api_handler_model");

        $res = $this->network_api_handler_model->provisionLTEAccount($_POST);

        $msg_filter = strstr($res, '{');
        $msg = json_decode($msg_filter, TRUE);
        if (array_key_exists("error_msg", $msg)) {
            $this->session->set_flashdata('error_message', $msg['error_msg']);
        } else {
            $this->session->set_flashdata('success_message', "You successfully add new LTE account." . "<br>Complete API Responce : " . $msg_filter);
        }

        redirect('/admin/create_new_lte_account');
        return;
    }

    public function unlock_mtn_sim_card_device_lock()
    {
        $data['main_content'] = 'admin/unlock_mtn_sim_card_device_lock';
        $data['sidebar'] = TRUE;

        $this->load->view('admin/includes/template', $data);
    }

    public function submit_unlock_mtn_sim_card_device_lock()
    {
        if ($_POST['type'] == "LocationUnlock") {
            $data = array(
                "Username" => $_POST['Username'],
                "Type" => $_POST['type'],
                "Comment" => $_POST['comment'],
                "Location" => array(
                    "Latitude" => $_POST['Latitude'],
                    "Longitude" => $_POST['Longitude']
                )
            );
        } else {
            $data = array(
                "Username" => $_POST['Username'],
                "Type" => $_POST['type'],
                "Comment" => $_POST['comment'],
            );
        }

        $payload = json_encode($data);
        $host = 'https://www.isdsl.net/api/rest/lte/unlockMTNSim.php';
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $msg_filter = strstr($result, '{');
        $msg = json_decode($msg_filter, TRUE);
        if (array_key_exists("error_msg", $msg)) {

            $this->session->set_flashdata('error_message', $msg['error_msg']);
        } else {
            $this->session->set_flashdata('success_message', "Successfully updated." . "<br>Complete API Responce : " . $msg_filter);
        }
        redirect('/admin/unlock_mtn_sim_card_device_lock', 'refresh');
    }

    public function mtn_sim_lock_status()
    {
        $data['main_content'] = 'admin/mtn_sim_lock_status';
        $data['sidebar'] = TRUE;
        $this->load->view('admin/includes/template', $data);
    }

    public function submit_mtn_sim_lock_status()
    {
        $user = $_POST['username'];
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';

        $host = 'https://www.isdsl.net/api/rest/lte/getMTNSimUnlockStatus.php?Username=' . $user;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        $msg_filter = strstr($result, '{');

        $msg = json_decode($msg_filter, TRUE);
        if (array_key_exists("error_msg", $msg)) {

            $this->session->set_flashdata('error_message', $msg['error_msg']);
        } else {
            $this->session->set_flashdata('record_data', $msg['data']);
            $this->session->set_flashdata('success_message', "Success ! Complete API Responce : " . $msg_filter);
        }
        redirect('/admin/mtn_sim_lock_status', 'refresh');
    }

    function getClasses()
    {
        $username = $_GET['username'];

        if (empty($username)) {
            die();
        }

        $realm = substr($username, strpos($username, '@') + 1);

        $this->load->model("network_api_handler_model");

        $result = $this->network_api_handler_model->getClassesByRealm($realm);

        echo json_encode($result);
    }

    function lte_usage_stats($filter = 'all', $offset = 0)
    {
        $data['main_content'] = 'admin/lte_usage_stats';
        $data['sidebar'] = TRUE;

        $this->load->library('pagination');
        $config['base_url'] = base_url('index.php/admin/lte_usage_stats/' . $filter);
        $config['total_rows'] = $this->db->query('SELECT * FROM lte_usage_stat')->num_rows();
        $config['per_page'] = 50;
        $config['full_tag_open'] = '<ul class="pagination uiflat-mix-pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='active'><a style='text-decoration: underline; font-weight: bold;' href='#'>";
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['first_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_close'] = '</li>';
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $data['pages'] = $this->pagination->create_links();

        switch ($filter) {
            case 'usage_desc':
                $data['stats'] = $this->db->limit(50, $offset)->order_by('usage', 'desc')->get('lte_usage_stat')->result_array();
                $data['uriFilter'] = 'usage_asc';
                $data['usageArrow'] = '<i class="fas fa-angle-down"></i>';
                break;
            case 'usage_asc':
                $data['stats'] = $this->db->limit(50, $offset)->order_by('usage', 'asc')->get('lte_usage_stat')->result_array();
                $data['uriFilter'] = 'usage_desc';
                $data['usageArrow'] = '<i class="fas fa-angle-up"></i>';
                break;
            default:
                $data['stats'] = $this->db->limit(50, $offset)->get('lte_usage_stat')->result_array();
                $data['uriFilter'] = 'usage_desc';
                $data['usageArrow'] = '';
                break;
        }

        $data['dateUpdate'] = $this->db->where('action', 'lte_updated')->get('system_param')->result()[0]->toggle;

        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function updateLteUsageStats()
    {
        $this->load->model('lte_usage_stats_model');
        $this->lte_usage_stats_model->getLteUsageStats();

        echo 'ok';
    }

    function sim_swap()
    {
        $data['main_content'] = 'admin/sim_swap';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    #3306

    function telkom_coverage_map()
    {
        $data['main_content'] = 'admin/telkom_coverage_map';
        $data['sidebar'] = TRUE;
        $this->load->view(
            'admin/includes/template',
            $data
        );
    }

    function submit_sim_swap()
    {
        $this->load->model("network_api_handler_model");
        $response = $this->network_api_handler_model->simSwapRequest($data);
        $data = $this->input->post();
        $requestData = [
            "Username" => $data['username'],
            "Existing MSISDN" => $data['msisdn'],
            "New ICCID" => $data['iccid'],
            "RICA" => [
                "Building" => $data['building'],
                "Street" => $data['street'],
                "Suburb" => $data['suburb'],
                "City" => $data['city'],
                "PostCode" => $data['postcode'],
                "ContactName" => $data['ContactName'],
                "TelCell" => $data['TelCell'],
                "idNumber" => $data['idNumber'],
                "AddressType" => $data['AddressType']
            ],
            "AddressLocation" => [
                "Latitude" => $data['Latitude'],
                "Longitude" => $data['Longitude']
            ]
        ];

        $payload = json_encode($requestData, TRUE);

        $host = 'https://www.isdsl.net/api/rest/lte/ownSimSwap.php';
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';

        //$username='api@openwebmobile.co.za';$password='kjhdkjsa6i213hjksa!';

        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        curl_close($ch);
        $msg_filter = strstr($return, '{');
        $msg = json_decode($msg_filter, TRUE);

        if (array_key_exists("error_msg", $msg)) {
            $this->session->set_flashdata('error_message', $msg['error_msg']);
        } else {
            $this->session->set_flashdata('success_message', "Complete API Responce : " . $msg_filter);
        }



        // if($response['ok'] == 'true') {
        //     $data['messages']['success_message'] = 'Request accepted';
        // } else {
        //     $data['messages']['error_message'] = 'Request not accepted. API response: '. $response['error_msg'];
        // }
        // $data['main_content'] = 'admin/sim_swap';
        // $data['sidebar'] = TRUE;
        // $this->load->view(
        //     'admin/includes/template', $data);
        redirect('/admin/sim_swap');
    }

    function getAccountInfoAPI()
    {

        $username = $this->input->post('username');
        $response = $this->is_classes->accountMap($username);


        if ($response['ok'] == 'true') {
            echo json_encode($response['data']);
        } else {
            echo $response['error_msg'];
        }
    }

    function lte_order_stats($id, $type, $fiberuser)
    {
        if ($type == "telkom") {

            $this->db->select("orders.*,telkome_stat.*,membership.*,fibre_orders.*,telkom_recharge_requests.*");
            $this->db->from('telkome_stat');
            $this->db->join('orders', 'telkome_stat.telkom_user_code = orders.id', 'left');
            $this->db->join('membership', 'orders.id_user = membership.id', 'left');
            $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
            $this->db->join('telkom_recharge_requests', 'orders.id = telkom_recharge_requests.rel_rec_order_id', 'left');
            $this->db->where('telkome_stat.telkom_status_temp_removed_status !=', 'TEMP REMOVED');
            $this->db->where('telkome_stat.telkom_user_code', $id);
            $query = $this->db->get();
            $record = $query->result_array();
            $req_button_data = array(
                'order_id' => $id,
                'order_type' => $type,
                'username' => $fiberuser
            );
        } elseif ($type == "mtn") {

            $this->db->select("orders.*,mtn_stat.*,membership.*,fibre_orders.*");
            $this->db->from('mtn_stat');
            $this->db->join('orders', 'mtn_stat.mtn_user_code = orders.id', 'left');
            $this->db->join('membership', 'orders.id_user = membership.id', 'left');
            $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
            $this->db->where('mtn_stat.mtn_status_temp_removed_status !=', 'TEMP REMOVED');
            $this->db->where('mtn_stat.mtn_user_code', $id);
            $query = $this->db->get();
            $record = $query->result_array();

            $req_button_data = array(
                'order_id' => $id,
                'order_type' => $type,
                'username' => $fiberuser
            );
        }
        $data['lte_stats_data'] = $record;
        $data['stats_btn'] = $req_button_data;
        $data['main_content'] = 'admin/lte_order_stats';
        $data['sidebar'] = TRUE;

        $this->load->view('admin/includes/template', $data);
    }
    function mobile_order_stats($id, $type, $fiberuser)
    {

        if ($type == "mobile") {
            $this->db->select("orders.*,mobile_stat.*,membership.*,fibre_orders.*,mob_recharge_requests.*");
            $this->db->from('mobile_stat');
            $this->db->join('orders', 'mobile_stat.mobile_user_code = orders.id', 'left');
            $this->db->join('membership', 'orders.id_user = membership.id', 'left');
            $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
            $this->db->join('mob_recharge_requests', 'orders.id = mob_recharge_requests.mob_rec_order_id', 'left');
            $this->db->where('mobile_stat.mobile_status_temp_removed_status !=', 'TEMP REMOVED');
            $this->db->where('mobile_stat.mobile_user_code', $id);
            //  $this->db->where('telkome_stat.telkom_status','RESETED');

            // $this->db->select("orders.*,telkome_stat.*,membership.*,fibre_orders.*,telkom_recharge_requests.*");
            // $this->db->from('telkome_stat');
            // $this->db->join('orders', 'telkome_stat.telkom_user_code = orders.id', 'left');
            // $this->db->join('membership', 'orders.id_user = membership.id', 'left');
            // $this->db->join('fibre_orders', 'orders.id = fibre_orders.order_id', 'left');
            // $this->db->join('telkom_recharge_requests', 'orders.id = telkom_recharge_requests.rel_rec_order_id', 'left');
            // $this->db->where('telkome_stat.telkom_status_temp_removed_status !=', 'TEMP REMOVED');
            // $this->db->where('telkome_stat.telkom_user_code', $id);
            $query = $this->db->get();
            $record = $query->result_array();
            $req_button_data = array(
                'order_id' => $id,
                'order_type' => $type,
                'username' => $fiberuser
            );
        }
        $data['mobile_stats_data'] = $record;
        $data['stats_btn'] = $req_button_data;
        $data['main_content'] = 'admin/mobile_order_stats';

        $data['sidebar'] = TRUE;

        $this->load->view('admin/includes/template', $data);
    }

    function CurlFunction($d, $curlcall, $verb = "")
    {
        $Username = "ResellerAdmin";
        $Password = "jFbd5lg7Djfbn48idmlf4Kd";

        $curl = new Curl();
        $response = new Response();
        switch ($curlcall) {
            case "getSession": {
                    $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/getSession.json";
                    $curl->setBasicAuthentication($Username, $Password);
                    $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
                    $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
                    $curl->get($Url, $d);
                    break;
                }
            case "checkSession": {
                    $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/checkSession.json";
                    $curl->setBasicAuthentication($Username, $Password);
                    $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
                    $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
                    $curl->get($Url, $d);
                    break;
                }
            case "checkFibreAvailability": {
                    $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/checkFibreAvailability.json";
                    $curl->setBasicAuthentication($Username, $Password);
                    $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
                    $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
                    $curl->get($Url, $d);
                    break;
                }
            case "getNetworkProviders": {
                    $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/getNetworkProviders.json";
                    $curl->setBasicAuthentication($Username, $Password);
                    $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
                    $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
                    $curl->get($Url, $d);
                    break;
                }
            default: {
                    $response->intCode = 500;
                    $response->message = "NADA Requested, missing required data or REST Call is not valid!";
                }
        }

        $response->curl = $curl;
        if ($curl->error) {
            $response->intCode = $curl->error_code;
        } else {
            $response->json = $curl->response;
            $result = json_decode($curl->response);
            if (null === $result) {
                $response->intCode = 500;
                $response->message = "Too many nested arrays or error decoding.";
            } else {
                $response->intCode = $result->intCode;
                $response->message = isset($result->message) ? $result->message : null;
                $response->object = $result;
            }
        }
        if ($response->intCode != 200) {
            $response->hasError = true;
        }
        $curl->close();
        return $response;
    }

    /**
     * @param $array
     * @return bool
     */
    function is_array_assoc($array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * @param $array
     * @return bool
     */
    function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }
        return !(count($array) === count($array, COUNT_RECURSIVE));
    }

    /**
     * @param $data
     * @param null $key
     * @return string
     */
    function http_build_multi_query($data, $key = null)
    {
        $query = array();
        if (empty($data)) {
            return $key . '=';
        }
        $is_array_assoc = is_array_assoc($data);
        foreach ($data as $k => $value) {
            if (is_string($value) || is_numeric($value)) {
                $brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
                $query[] = urlencode(is_null($key) ? $k : $key . $brackets) . '=' . rawurlencode($value);
            } else if (is_array($value)) {
                $nested = is_null($key) ? $k : $key . '[' . $k . ']';
                $query[] = http_build_multi_query($value, $nested);
            }
        }
        return implode('&', $query);
    }
}

//Responce class used for map api
class Response
{

    public $intCode;
    public $hasError = false;
    public $message;
    public $curl;
    public $json;
    public $object;
}

//Curl class used for map api
class Curl
{

    const USER_AGENT = 'OPEN WEB';

    private $_cookies = array();
    private $_headers = array();
    private $_options = array();
    private $_multi_parent = false;
    private $_multi_child = false;
    private $_before_send = null;
    private $_success = null;
    private $_error = null;
    private $_complete = null;
    public $curl;
    public $curls;
    public $error = false;
    public $error_code = 0;
    public $error_message = null;
    public $curl_error = false;
    public $curl_error_code = 0;
    public $curl_error_message = null;
    public $http_error = false;
    public $http_status_code = 0;
    public $http_error_message = null;
    public $request_headers = null;
    public $response_headers = null;
    public $response = null;

    /**
     * @throws \ErrorException
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is has not been loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADER, true);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param $url_mixed
     * @param array $data
     * @return int|mixed
     * @throws \ErrorException
     */
    public function get($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $curl_multi = curl_multi_init();
            $this->_multi_parent = true;

            $this->curls = array();

            foreach ($url_mixed as $url) {
                $curl = new Curl();
                $curl->_multi_child = true;
                $curl->setOpt(CURLOPT_URL, $this->_buildURL($url, $data), $curl->curl);
                $curl->setOpt(CURLOPT_HTTPGET, true);
                $this->_call($this->_before_send, $curl);
                $this->curls[] = $curl;

                $curlm_error_code = curl_multi_add_handle($curl_multi, $curl->curl);
                if (!($curlm_error_code === CURLM_OK)) {
                    throw new \ErrorException('cURL multi add handle error: ' .
                        curl_multi_strerror($curlm_error_code));
                }
            }

            foreach ($this->curls as $ch) {
                foreach ($this->_options as $key => $value) {
                    $ch->setOpt($key, $value);
                }
            }

            do {
                $status = curl_multi_exec($curl_multi, $active);
            } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

            foreach ($this->curls as $ch) {
                $this->exec($ch);
            }
        } else {
            $this->setopt(CURLOPT_URL, $this->_buildURL($url_mixed, $data));
            $this->setopt(CURLOPT_HTTPGET, true);
            return $this->exec();
        }
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function post($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($data));
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function put($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function patch($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function delete($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->exec();
    }

    /**
     * @param $username
     * @param $password
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $key . ': ' . $value;
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->_headers));
    }

    /**
     * @param $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    /**
     * @param $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setCookie($key, $value)
    {
        $this->_cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }

    /**
     * @param $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * @param $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * @param $option
     * @param $value
     * @param null $_ch
     * @return bool
     */
    public function setOpt($option, $value, $_ch = null)
    {
        $ch = is_null($_ch) ? $this->curl : $_ch;

        $required_options = array(
            CURLINFO_HEADER_OUT => 'CURLINFO_HEADER_OUT',
            CURLOPT_HEADER => 'CURLOPT_HEADER',
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->_options[$option] = $value;
        return curl_setopt($ch, $option, $value);
    }

    /**
     * @param bool $on
     */
    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    /**
     *
     */
    public function close()
    {
        if ($this->_multi_parent) {
            foreach ($this->curls as $curl) {
                curl_close($curl->curl);
            }
        }

        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * @param $function
     */
    public function beforeSend($function)
    {
        $this->_before_send = $function;
    }

    /**
     * @param $callback
     */
    public function success($callback)
    {
        $this->_success = $callback;
    }

    /**
     * @param $callback
     */
    public function error($callback)
    {
        $this->_error = $callback;
    }

    /**
     * @param $callback
     */
    public function complete($callback)
    {
        $this->_complete = $callback;
    }

    /**
     * @param $url
     * @param array $data
     * @return string
     */
    private function _buildURL($url, $data = array())
    {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    /**
     * @param $data
     * @return array|string
     */
    private function _postfields($data)
    {
        if (is_array($data)) {
            if (is_array_multidim($data)) {
                $data = http_build_multi_query($data);
            } else {
                foreach ($data as $key => $value) {
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param null $_ch
     * @return int|mixed
     */
    protected function exec($_ch = null)
    {
        $ch = is_null($_ch) ? $this : $_ch;

        if ($ch->_multi_child) {
            $ch->response = curl_multi_getcontent($ch->curl);
        } else {
            $ch->response = curl_exec($ch->curl);
        }
        $ch->curl_error_code = curl_errno($ch->curl);
        $ch->curl_error_message = curl_error($ch->curl);
        $ch->curl_error = !($ch->curl_error_code === 0);
        $ch->http_status_code = curl_getinfo($ch->curl, CURLINFO_HTTP_CODE);
        $ch->http_error = in_array(floor($ch->http_status_code / 100), array(4, 5));
        $ch->error = $ch->curl_error || $ch->http_error;
        $ch->error_code = $ch->error ? ($ch->curl_error ? $ch->curl_error_code : $ch->http_status_code) : 0;

        $ch->request_headers = preg_split('/\r\n/', curl_getinfo($ch->curl, CURLINFO_HEADER_OUT), null, PREG_SPLIT_NO_EMPTY);
        $ch->response_headers = '';
        if (!(strpos($ch->response, "\r\n\r\n") === false)) {
            list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            if ($response_header === 'HTTP/1.1 100 Continue') {
                list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            }
            $ch->response_headers = preg_split('/\r\n/', $response_header, null, PREG_SPLIT_NO_EMPTY);
        }
        $ch->http_error_message = $ch->error ? (isset($ch->response_headers['0']) ? $ch->response_headers['0'] : '') : '';
        $ch->error_message = $ch->curl_error ? $ch->curl_error_message : $ch->http_error_message;

        if (!$ch->error) {
            $ch->_call($this->_success, $ch);
        } else {
            $ch->_call($this->_error, $ch);
        }

        $ch->_call($this->_complete, $ch);

        return $ch->error_code;
    }

    /**
     * @param $function
     */
    private function _call($function)
    {
        if (is_callable($function)) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array($function, $args);
        }
    }


    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }
}
