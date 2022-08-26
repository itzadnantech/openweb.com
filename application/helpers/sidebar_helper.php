<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_sidebar')){
    function get_sidebar(){
        $ci =& get_instance();
        $ci->load->model('admin/roles_rights_model');
        $sidebar_details = $ci->roles_rights_model->get_role_access();
        return $sidebar_details;
    }
}
if (!function_exists('get_role_id')){
    function get_role_id($role){
        $ci =& get_instance();
        $ci->load->database();
        $query = $ci->db->get_where('roles',array('role_name'=>$role));
        if($query->num_rows() > 0){
            $result = $query->row_array();
            return $result;
        }else{
           return false;
        }
    }
}

if (!function_exists('check_user_acess')){
    function check_user_acess($slug, $role_id){
        $ci =& get_instance();
        $ci->load->database();
        $query = $ci->db->query('SELECT * FROM role_access WHERE slug LIKE "'.$slug.'"');
        if($query->num_rows() > 0){
            $query2 = $ci->db->query('SELECT * FROM role_access WHERE slug LIKE "'.$slug.'" AND allowed_access LIKE "%'.$role_id.'%"');
            if($query2->num_rows() == 0){
                if ($ci->session->userdata('is_logged_in') == TRUE) {
                    redirect('404');
                }else{
                    redirect('login');
                }
            }
        }
    }
}

if (!function_exists('check_acess')){
    function check_acess($slug, $role_id){
        $ci =& get_instance();
        $ci->load->database();
        $query = $ci->db->query('SELECT * FROM role_access WHERE slug LIKE "'.$slug.'"');
        if($query->num_rows() > 0){
            $query2 = $ci->db->query('SELECT * FROM role_access WHERE slug LIKE "'.$slug.'" AND allowed_access LIKE "%'.$role_id.'%"');
            if($query2->num_rows() > 0){
                return true;
            } else {
                return false;
            }
        }else{
            return true;
        }
    }
}