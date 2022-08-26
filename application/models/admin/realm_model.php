<?php 
class Realm_model extends CI_Model {
	
	function  get_realm_name($realm_id){
		$this->db->select('realm');
		$this->db->where('id', $realm_id);
		$query = $this->db->get('realms');
		$realm = $query->first_row('array');
		$name = $realm['realm'] ;
		return $name;
	}
	
	function get_realm_list(){
		$query = $this->db->get('realms');
		return $query->result_array();
	}
	
	function get_realm_data($realm_id){
		$this->db->where('id', $realm_id);
		$query = $this->db->get('realms');
		$user_settings = $query->first_row('array');
		if($user_settings){
			$data = array('realm_settings' => $user_settings);
		}else{
			$data = '';
		}				
		return $data;
	}
	
	function validate_realm_name($name){
		$this->db->select('id');
		$this->db->where('realm',$name);
		$query = $this->db->get('realms');
		if($query->num_rows == 1){
			$result = $query->row();
			return 1;
		}else{
			return 0;
		}
	}
	
	function get_realm_fields(){
		$realm_fields = array (
				'realm' => 'Realm Name',
				'user' => 'User Name',
				'pass' => 'Password',
		);
		return $realm_fields;
	}
	
	function get_all_realm($num=10, $start=0){
		$this->db->limit($num, $start);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('realms');
		$result = $query->result_array();
		return $result;
	}
	
	function get_realm_count(){
		$this->db->select('id');
		$this->db->from('realms');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function get_all_realm_name(){
		$this->db->select('realm');
		$query = $this->db->get('realms');
		return $query->result_array();
	}
	
	function validate_class_id($name){
		$this->db->select('table_id');
		$this->db->where('id',$name);
		$query = $this->db->get('is_classes');
		if($query->num_rows == 1){
			$result = $query->row();
			return 1;
		}else{
			return 0;
		}
	}

    function get_realm_data_by_name($realm){

        $this->db->select('user, pass');
        $this->db->where('realm', $realm);
        $query = $this->db->get('realms');
        $result = $query->result_array();

        if(!empty($result)) {
            $user = $result[0]['user'];
            $password = $result[0]['pass'];

            $data = array(
                'user' => $user . "@" . $realm,
                'pass' => $password,
                'realm' => $realm,
            );

            $userExplode = explode("@", $data["user"]);
            if (count($userExplode) > 2)
                $data["user"] = $userExplode[0] . "@" . $userExplode[1];

            return $data;
        }

        return false;
    }

}
?>