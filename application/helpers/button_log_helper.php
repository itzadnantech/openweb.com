<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('button_log')){
function button_log($button_name,$action_by,$role,$other_details){      
  $ci =& get_instance();
  $ci->load->database();
  $log_data = array(
                    'button_name' => $button_name,
                    'action_by' => $action_by,
                    'role' =>$role,
                    'other_details' => $other_details
                );
 $ci->db->insert('button_log',$log_data);
    return true;
}    
    
}

if (!function_exists('is_button_accessable')){
function is_button_accessable($button_name,$pagename,$role){  
    $user_role='';
    
    if($role == 'super_admin'){
        $user_role ='7001';
    }elseif($role == 'admin'){
    $user_role ='7002';    
    }elseif($role== 'staff'){
    $user_role ='7003';    
    }
    
  $ci =& get_instance();
  $ci->load->database();
  
    $query = $ci->db->query('SELECT * FROM buttons_permission WHERE page_name LIKE "'.$pagename.'"');
        if($query->num_rows() > 0){
            $query2 = $ci->db->query('SELECT * FROM buttons_permission WHERE button_name = "'.$button_name.'" AND access_granted LIKE "%'.$user_role.'%"');
            if($query2->num_rows() > 0){
                return true;
            } else {
                return false;
            }
        }else{
            return false;
 }
  
}    
    
}


?>