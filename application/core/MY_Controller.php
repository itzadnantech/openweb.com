<?php 
class My_Controller extends CI_Controller{
    function __construct() {
        parent::__construct();
        $this->load->helper('sidebar');
        $role = $this->session->userdata('role');
        $role_data = get_role_id($role);
        $record_num = end($this->uri->segment_array());
        check_user_acess($record_num, $role_data['role_code']);
    }
}