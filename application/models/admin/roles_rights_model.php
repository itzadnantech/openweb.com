<?php 
class Roles_rights_model extends CI_Model {
   public function get_role_access(){
        $this->db->select('*');
        $this->db->from('role_access');
        $this->db->where('parent_id', 0);
    
        $parent = $this->db->get();
        
        $roles = $parent->result();
        $i=0;
        foreach($roles as $p_role){
            $roles[$i]->sub = $this->sub_role_access($p_role->id);
            $i++;
        }
        return $roles;
    }
    
    
    public function update_role($updatearr){
        foreach($updatearr as $update){
            $this->db->set('allowed_access', $update['accessor_id']);
            $this->db->where('id', $update['role_id']);
            $this->db->update('role_access');
        }
        return $this->db->affected_rows();
    }
    
    
        public function sub_role_access($id){
        $this->db->select('*');
        $this->db->from('role_access');
        $this->db->where('parent_id', $id);
    
        $child = $this->db->get();
        $roles = $child->result();
        $i=0;
        foreach($roles as $p_role){
            $roles[$i]->sub = $this->sub_role_access($p_role->id);
            $i++;
        }
        return $roles;       
    }

//Buttons Access
   public function get_buttons_access(){
        $this->db->select('*');
        $this->db->from('buttons_permission');
        $parent = $this->db->get();
        $roles = $parent->result();
        return $roles;
    }
    
    public function update_button_permission($updatearr){
        foreach($updatearr as $update){
            $this->db->set('access_granted', $update['accessor_id']);
            $this->db->where('button_id', $update['role_id']);
            $this->db->update('buttons_permission');
        }
        return $this->db->affected_rows();
    }    
    
}
?>