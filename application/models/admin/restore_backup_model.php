<?php

class Restore_backup_model extends CI_Model{

    function select_backup_orders($start, $limit){

        $this->db->select();
        $this->db->where('billing_cycle !=','Once-Off');
        $this->db->where('status','active');
        $this->db->limit($limit, $start);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('backup_orders');
        $result = $query->result_array();
        return $result;

    }

    function select_current_order_by_user_id($user_id, $product, $acc_username, $realm){

        $this->db->select();
        $this->db->where('id_user', $user_id);
        $this->db->where('product', $product);
        $this->db->where('account_username', $acc_username);
        $this->db->where('realm', $realm);

        $query = $this->db->get('orders');

        $result = $query->first_row('array');
        return $result;

    }

    function check_current_order_id($backup_order_id){
        $this->db->select('id');
        $this->db->where('id', $backup_order_id);
        $query = $this->db->get('orders');

        $result = $query->first_row('array');
        return $result;


    }

    function get_backup_user_by_id($user_id){

        $this->db->where('id', $user_id);
        $query = $this->db->get('backup_membership');
        $result = $query->first_row('array');
        return $result;

    }

    function get_current_user_by_id($user_id){

        $this->db->where('id', $user_id);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');
        return $result;

    }


    function select_backup_billing($start, $limit){

        $this->db->select();

        $this->db->limit($limit, $start);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('backup_billing');
        $result = $query->result_array();
        return $result;


    }


    function get_current_billing_data_by_user_id($user_id)
    {
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('billing');
        $result = $query->result_array();
        if($result){
            return $result[0];
        }else{
            return null;
        }
    }


    function get_current_billing_data_by_row_id($row_id)
    {
        $this->db->where('id', $row_id);
        $query = $this->db->get('billing');
        $result = $query->result_array();
        if($result){
            return $result[0];
        }else{
            return null;
        }
    }


    function get_current_billing_data_by_username($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('billing');
        $result = $query->result_array();
        if($result){
            return $result[0];
        }else{
            return null;
        }
    }


}