<?php
class Session_model extends CI_Model {
	
	function get_session_data(){
		$query = $this->db->get('ci_sessions');
		$result = $query->result_array();
		return $result;
	}
}