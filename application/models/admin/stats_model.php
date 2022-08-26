<?php

class Stats_model extends CI_Model {

	function get_num_users () {
		$this->db->select('count(username)');
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		return $result['count(username)'];
	}

	function get_last_logged_in () {
		$this->db->select('username');
		$this->db->order_by('date', 'desc');
		$this->db->limit(1);
		$query = $this->db->get('use_log');
		$result = $query->first_row('array');
		return $result['username'];
	}

	function get_last_joined () {
		$this->db->select('username');
		$this->db->order_by('joined', 'desc');
		$this->db->limit(1);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		return $result['username'];
	}
}

?>