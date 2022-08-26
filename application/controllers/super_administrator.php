<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Super_administrator extends CI_Controller {
	public $site_data;
	
	function __construct() {
		parent::__construct();
		$this->is_logged_in(); 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		
	}
	
	function is_logged_in() {
		$is_logged_in = $this->session->userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != true) {
			redirect('login');
		}
	}
	
	function index() {
		redirect('super_administrator/dashboard');
	}
	
	function dashboard () {
		$data['main_content'] = 'administrator/dashboard';
		$this->load->view('administrator/includes/template', $data);
	}
	
	function create_admin(){
		if($_POST){
			$username = $_POST['username'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$user_mobile = trim($_POST['mobile_number']);//mobie_number
			
			$user_settings = array (
				'first_name' => trim($first_name),
				'last_name' => trim($last_name),
				'email_address' => trim($_POST['email_address']),
				'role' => $_POST['role'],
				'username' => trim($username),
				'password' => $_POST['password'],
				'discount' => '5',
				'status' => 'active',
				'mobile_number' => $user_mobile,
			);
			
			$result = $this->db->insert('membership', $user_settings);
			if($result){
				$suc_msg = 'The admin information has been added successfully.';
				$this->session->set_flashdata('success_message', $suc_msg);
			}else{
				$error_msg = 'Failed to added an admin.';
				$this->session->set_flashdata('error_message', $error_msg);
			}
			redirect("super_administrator/admin_list");
		}else{
			$data['main_content'] = 'administrator/create_admin';
			$this->load->view('administrator/includes/template', $data);
		}
		
	}
	
	function admin_list(){
		$suc_msg = $this->session->flashdata('success_message');
		$error_msg = $this->session->flashdata('error_message');
		
		$admin_list = $this->membership_model->get_admin_list();
		$data['succ_message'] = $suc_msg;
		$data['error_message'] = $error_msg;
		
		$data['admin_list'] = $admin_list;
		$data['main_content'] = 'administrator/admin_list';
		$this->load->view('administrator/includes/template', $data);
	}
	
	function delete_admin($admin_id){
		$result = $this->membership_model->delete_user($admin_id);
		if($result){
			$suc_msg = "The admin has been deleted successfully.";
			$this->session->set_flashdata('success_message',$suc_msg);
		}else{
			$error_msg = "Failed to delete the admin.";
			$this->session->set_flashdata('error_message',$error_msg);
		}
		redirect("super_administrator/admin_list");
	}
	
	function edit_admin($admin_id){
		$admin_data = $this->membership_model->get_admin_data($admin_id);
		if($admin_data){
			$data['admin_data'] = $admin_data;
		}else{
			$data['admin_data'] = '';
		}
		
		$data['admin_id'] = $admin_id;
		$data['main_content'] = 'administrator/edit_admin';
		$this->load->view('administrator/includes/template', $data);
		
	}

	function update_admin(){
		$admin_id = $_POST['admin_id'];
		$user_settings = array (
			'first_name' => $_POST['first_name'],
			'last_name' => $_POST['last_name'],
			'email_address' => trim($_POST['email_address']),
			'username' => trim($_POST['username']),
			'password' => $_POST['password'],
			'mobile_number' => $_POST['mobile_number'],
		);
		
		$this->db->where('id', $admin_id);
		$this->db->update('membership', $user_settings);
		$suc_msg = "The admin has been updated successfully.";
		$this->session->set_flashdata('success_message',$suc_msg);
		redirect("super_administrator/admin_list");
	}
	
}