<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Register extends CI_Controller {


    function __construct(){

        parent::__construct();

        $this->load->helper('url');
        $this->load->model('validation_model');

        $this->load->helper('captcha');
        $this->load->helper('file');

    }

		
	function index() {

        // FORCE SSL
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!="on" && ( $_SERVER['HTTP_HOST'] != STAGE_HOST) )
        {
            $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }

         // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $ui_prefix = '';
        $this->load->model('flat_ui_model');
        $ui_prefix = $this->flat_ui_model->check_ui_prefix();
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        //$this->session->set_flashdata('from_register_index', 'true');



		$data['error_message'] = $this->session->flashdata('error_message');
        $terms_txt = read_file('terms.txt');

        $terms_txt = str_replace("“","\"",$terms_txt);
        $terms_txt = str_replace("”","\"",$terms_txt);
        $terms_txt = str_replace("’","'",$terms_txt);
        $terms_txt = str_replace("–","-",$terms_txt);
        $terms_txt = str_replace("‘","'",$terms_txt);


        $registration_fields = $this->validation_model->get_registration_fields();

        $data['form_fields'] = $registration_fields;
        $data['base_url'] = base_url();


        $data['terms_txt'] = $terms_txt;
		$data['main_content'] = 'register_form';
		$data['sidebar'] = FALSE;
        $data['aditional_scripts'] = ['assets/plugins/jquery-inputmask/jquery.inputmask.min.js',

            ];
            
            // echo '<pre>';
            // print_r($data);
            // echo '</pre>';
            // die;

		$this->load->view($ui_prefix . 'user/includes/template', $data);
		$this->site_data = array();
	}
	
	function validate_email(){
		
		if(isset($_POST['email_address']) && trim($_POST['email_address']) != ''){
            $email_address = strip_tags(mysql_real_escape_string($_POST['email_address']));
            $email_address = trim($email_address);

			$result = $this->membership_model->validate_email($email_address);
			if($result){
				echo "false";
			}else{
				echo "true";
			}
		}
	}

    function validate_sa_id(){
        if(isset($_POST['sa_id_number']) && trim($_POST['sa_id_number']) != ''){
            $sa_id_number = strip_tags(mysql_real_escape_string($_POST['sa_id_number']));
            $sa_id_number = trim($sa_id_number);

            $result = $this->membership_model->validate_sa_id_number($sa_id_number);
            if($result){
            }else{
                echo "true";
            }
        }

    }
	
	function validate_username(){
	
		if(isset($_POST['username']) && trim($_POST['username']) != ''){
            $username = strip_tags(mysql_real_escape_string($_POST['username']));
            $username = trim($username);

			$result = $this->membership_model->validate_username($username);
			if($result){
				echo "false";
			}else{
				echo "true";
			}
		}
	}
	
	function create_user()
	{

        $this->load->model('crypto_model');
        $this->validation_model->set_rules_for_registration();
        if ($this->form_validation->run() == FALSE){

            $repopulated_array = $this->validation_model->re_populate_registration_form();
            $registration_fields = $this->validation_model->get_registration_fields();
            $data['repopulated_array'] = $repopulated_array;
            $data['form_fields'] = $registration_fields;

            $this->load->helper('file');
            $terms_txt = read_file('terms.txt');

            $terms_txt = str_replace("“","\"",$terms_txt);
            $terms_txt = str_replace("”","\"",$terms_txt);
            $terms_txt = str_replace("’","'",$terms_txt);
            $terms_txt = str_replace("–","-",$terms_txt);
            $terms_txt = str_replace("‘","'",$terms_txt);

            $data['base_url'] = base_url();
            $data['terms_txt'] = $terms_txt;

            // error message
            $data['main_content'] = 'register_form';
            $data['sidebar'] = FALSE;
            $this->load->view('user/includes/template', $data);
            return;

        }

		if ($_POST){

            // Prepare data , also CodeIgniter

            $username     = $this->validation_model->process_post_field('username');
            $email        = $this->validation_model->process_post_field('email_address');
            $mobile       = $this->validation_model->process_post_field('mobile_number');
            $pass         = $this->validation_model->process_post_field('password');
            $first_name   = $this->validation_model->process_post_field('first_name');
            $last_name    = $this->validation_model->process_post_field('last_name');
            $sa_id_number = $this->validation_model->process_post_field('sa_id_number');

            $pwd = $this->crypto_model->encode($pass);

            $role = 'client';
			$date = date('Y-m-d H:i:s',time('Now'));
			$status = 'active';
			$discount = 0;
			$br_a_id = NULL;//

			if(isset($_POST['br_a_id']))
                $br_a_id = $_POST['br_a_id'];
			
			$user_settings = array (
				'first_name' => $first_name,
				'last_name' => $last_name,
				'password' => $pwd,//remove md5
				'email_address' => $email,
				'username' => $username,
				'role' => $role,
				'joined' => $date,
				'discount' => $discount,
				'status' => $status,
				'mobile_number' => $mobile,
				'subscribe' => 1,
                'br_a_id' => $br_a_id
			);
           $sql = "INSERT INTO membership (first_name, last_name, password,email_address,username,role,joined,discount,status,mobile_number,subscribe,br_a_id)
           VALUES ('$first_name','$last_name','$pwd','$email','$username','$role','$date','$discount','$status','$mobile',1,'$br_a_id')";
           $this->db->query($sql);
			$account_id =  $this->db->insert_id();
		$ow_id = $this->membership_model->create_OW($account_id);
			
			$format_ow = "OW".$ow_id;
			$this->db->where('id', $account_id);
			$this->db->update('membership', array('ow' => $format_ow));
			
			if($account_id){

                // add billing info
                $billing_info = array(

                    'sa_id_number' => $sa_id_number,
                    'email'	       => $email,
                    'id_user'      => $account_id,
                    'username'     => $username,

                );

                $this->db->insert('billing', $billing_info);
                $billing_id =  $this->db->insert_id();


                // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~`

				$this->load->model('user/user_model');
				$this->user_model->email_active_account($account_id);
				
				$sms_content = 'Your account has been successfully created. See email for more details. - OpenWeb';
				$this->load->model('admin/order_model');
                $this->load->model('sms_model');
                $sms_response = $this->sms_model->sendSms($mobile, $sms_content); //Send SMS/
				//$sms_result = $this->order_model->send_sms($mobile, $sms_content);

				$this->order_model->send_system_ceo($account_id);
				
				$message_data = array(
				  'message_type' => 'success',
				  'category' => 'register',
				);


				$get_message = $this->message_model->get_message($message_data); 
				//$suc_msg = "Congratulation! You have successfully registered.";
				$data['success_message'] = $get_message;
				$data['main_content'] = 'login_form';
                $data['aditional_scripts'] = ['assets/plugins/jquery-inputmask/jquery.inputmask.min.js'];
                $data['aditional_scripts'] = ['assets/js/form_elements.js'];
				$data['sidebar'] = FALSE;
				$this->load->view('user/includes/template', $data);
			}else{
				//$error_msg = "Failed to register.Please try it again.";
				$message_data = array(
						'message_type' => 'error',
						'category' => 'register',
				);


				$get_message = $this->message_model->get_message($message_data);
				$this->session->set_flashdata('error_message', $get_message);
				redirect("register/index");
			}
		}else{

			redirect("register/index");
		}


	}
	
	function activate_account($account_id)
	{
		$this->load->model('user/user_model');
		if($account_id){
			$result = $this->user_model->active_account($account_id);
			if($result == 1){			
				$success_msg = 'The account has been activated successfully!';
				$this->session->set_flashdata('success_message', $success_msg);
				//send active email 
				$this->user_model->email_active_account($account_id);				
			}else{
				$error_msg = "Failed to active this account.";
				$this->session->set_flashdata('error_message', $error_msg);			
			}
			redirect("admin/edit_account/".$account_id);
		}else{
			$error_msg = "The account doesn't exist.Please try it agian.";
			$this->session->set_flashdata('error_message', $error_msg);
			redirect("admin/all_account");
		}
	}
}
