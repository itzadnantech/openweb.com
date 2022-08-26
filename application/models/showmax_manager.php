<?php


class Showmax_manager extends CI_Model
{

    public $showmax_api = null;

    public $subscription_status_active = "active";
    public $subscription_status_suspended = "suspended";
    public $subscription_status_delete = "deleted";
    public $subscription_statuses = array();//array("active", "suspended", "deleted");
    public $terminate_subscription_options = array("true" => "Instant", "false" => "End of the billing cycle");

    public $modify_subscription_status_mapper = array();

    public $status_mapper = array(
        // inits in constructor
        // mapper ==> active -> active, pending->suspended, deleted->deleted, expired->deleted
    );


    public $default_manger_response = array();

    function __construct(){

       $this->load->model('showmax_api_model');
       $this->showmax_api = &$this->showmax_api_model;
       // use $this->showmax_api_model to access corresponding api handler

        $this->subscription_statuses[] = $this->subscription_status_active;
        $this->subscription_statuses[] = $this->subscription_status_suspended;
        $this->subscription_statuses[] = $this->subscription_status_delete;

        $this->default_manger_response = $this->showmax_api_model->defaultResponseData;

        $modify_subscription_status_mapper["suspend_to_dbstatus"] = array(
            "true"  => $this->subscription_status_suspended,
            "false" => $this->subscription_status_active,
        );

        $modify_subscription_status_mapper["dbstatus_to_suspend"]  = array_flip(
                        $modify_subscription_status_mapper["suspend_to_db_status"] );


        // init mapper for "order" => "showmax_subscription" statuses
        $this->init_order_showmax_sub_statuses();


    }


    function init_order_showmax_sub_statuses(){

        $order_to_showmax_map = array(
                "active"  => $this->subscription_status_active,
                "pending" => $this->subscription_status_suspended,
                "deleted" => $this->subscription_status_delete,
                "expired" => $this->subscription_status_delete,
        );

        $this->status_mapper = array(
            "order_to_showmax" => $order_to_showmax_map,
            "showmax_to_order" => array(), // generates below

            // based on statuses from regular orders
            "order_map_description" => array(

                                 // TODO : edit mapper description messages later
                                "active"  => "status represents active showmax subscription",
                                "pending" => "will suspend Showmax subscription",
                                "deleted" => "will delete Showmax subscription",
                                "expired" => "", // same as "deleted"
                            ),
        );

        // set description for 'expired' status
        $this->status_mapper["order_map_description"]["expired"] = $this->status_mapper["order_map_description"]["deleted"];

        // set "showmax_to_order" mapper
        unset($order_to_showmax_map["expired"]);
        $this->status_mapper["showmax_to_order"] = array_flip($order_to_showmax_map);

        return true;
    }

    function activate_showmax_subscription($user_id, $subscription_type){



        // TODO : check if this user already have  active subscription (via db)
        // then prepare response/message
        $user_active_subscriptions = $this->get_subscriptions(array("user_id" => $user_id, "subscription_status" => $this->subscription_status_active));
        $default_response = $this->default_manger_response;

        //var_dump($user_active_subscriptions); echo "<hr/>";
        //echo "<hr/>"; die();
        //var_dump($default_response);echo "<hr/>";

        if (!empty($user_active_subscriptions)){

            // TODO : perhaps include default message for users
            $default_response["message"] = "This user already have an active subscription [local-app]";
            return $default_response;
        }

        // execute API call
        // TODO : only if status is `active`
        $api_call_result = $this->showmax_api_model->activate_subscription($user_id,  $subscription_type);

        // if API call has failed
        if (!$api_call_result["result"]){
            return $api_call_result;
        }

        // if API response is OK, we save activation code to DB
        // prepare data for saving
        $creation_time = date("Y-m-d H:i:s");
        $new_subscription_data = array(

            "user_id" => $user_id,
            "creation_time" => $creation_time,
            "last_update_time" => $creation_time,
            "activation_code" => $api_call_result["api_response"]["activation_code"],
            "subscription_status" => "active",
            "creation_api_call_id" => $api_call_result["additional_data"]["db_log"],
            "last_update_api_call_id" => $api_call_result["additional_data"]["db_log"],
            "subscription_type" => $subscription_type,
        );

        $api_call_result["additional_data"]["subscription_db_log_id"] = $this->insert_subscription($new_subscription_data);
        return $api_call_result;
    }

    function modify_showmax_subscription($user_id,  $order_id, $suspended_param,
                                         $terminate_subscription_param, $subscription_type_param){

        // array for search
        $where_data = array(
            "order_id" => $order_id,
            "user_id" => $user_id,
        );

        // validation
        $validation_result = $this->check_if_order_exists($where_data);
        if (!$validation_result["result"])
            return $validation_result["response"];


        // execute API call
        $api_call_result = $this->showmax_api_model->modify_subscription($user_id,  $suspended_param,
                                    $terminate_subscription_param, $subscription_type_param);

        // if API call has failed
        if (!$api_call_result["result"]){
            return $api_call_result;
        }

        // convert bool to str
        $suspended_param_str = var_export($suspended_param, true);
        // TODO : Do we need to store $terminate_subscription_param inside DB ?

        // if API response is OK, we save activation code to DB
        // prepare data for update
        $update_time = date("Y-m-d H:i:s");
        $update_data = array(

            "user_id" => $user_id,
            "last_update_time" => $update_time,
            "subscription_status" =>
                            $this->modify_subscription_status_mapper["suspend_to_dbstatus"][$suspended_param_str],
            "last_update_api_call_id" => $api_call_result["additional_data"]["db_log"],
            "subscription_type" => $subscription_type_param,
        );

        // add row ID into response
        if ($this->update_subscription($where_data, $update_data))
            $api_call_result["additional_data"]["subscription_db_log_id"] = $order_id;

        return $api_call_result;
    }



    function deactivate_showmax_subscription($user_id, $order_id,  $terminate_subscription_param){


        // array for search
        $where_data = array(
            "order_id" => $order_id,
            "user_id" => $user_id,
        );

        // validation
        $validation_result = $this->check_if_order_exists($where_data);
        if (!$validation_result["result"])
            return $validation_result["response"];


        // execute API call
        $api_call_result = $this->showmax_api_model->deactivate_subscription($user_id, $terminate_subscription_param);


        // if API call has failed
        if (!$api_call_result["result"]){
            return $api_call_result;
        }


        // if API response is OK, we save activation code to DB
        // prepare data for update
        $update_time = date("Y-m-d H:i:s");
        $update_data = array(

            "user_id" => $user_id,
            "last_update_time" => $update_time,
            "subscription_status" => 'deleted',
            "last_update_api_call_id" => $api_call_result["additional_data"]["db_log"],
        );

        // add row ID into response
        if ($this->update_subscription($where_data, $update_data))
            $api_call_result["additional_data"]["subscription_db_log_id"] = $order_id;

        return $api_call_result;
    }


    function manual_edit_showmax_subscription($user_id, $subscription_id, $update_data){

        // $data can be  "activation_code" or "subscription_status"


        // TODO : validate user_id and subscription_id
        // return false

        $search_array = array( "id" => $subscription_id,  "user_id" => $user_id);

        // get subscription and check if it exist
        $subscriptions = $this->get_subscriptions($search_array);
        if (empty($subscriptions)){
            // TODO :  error message

            return false;
        }

        // check certain fields
        $new_update_data = array();
        if (isset($update_data["activation_code"]))
            $new_update_data["activation_code"] = $update_data["activation_code"];

        if (isset($update_data["subscription_status"]))
            $new_update_data["subscription_status"] = $update_data["subscription_status"];

        // process DB update
        $update_result = $this->update_subscription($search_array, $new_update_data);

        // add some update messages

        return $update_result;

    }

    // insert data to `showmax_subscription`
    function insert_subscription($data_row){

        $inserted_id = 0;
        $result = $this->db->insert("showmax_subscription", $data_row);
        if ($result)
            $inserted_id = $this->db->insert_id();

        return $inserted_id;

    }

    function get_subscriptions($params, $limit = 1, $start = 0,
                               $order_by_key = array("order_key" => "id", "order_type" => "desc")){

        $this->db->select();
        if (!empty($params))
            foreach ($params as $row_key=>$row_value)
                $this->db->where($row_key, $row_value);

        $this->db->order_by($order_by_key["order_key"], $order_by_key["order_type"]);
        $this->db->limit($limit, $start);
        $query = $this->db->get("showmax_subscription");
        $result = $query->result_array();
        return $result;

    }



    function update_subscription($where_data, $update_data){

        if (empty($where_data) || empty($update_data))
            return false;

        foreach ($where_data as $row_key=>$row_value)
            $this->db->where($row_key, $row_value);

        $update_result = $this->db->update("showmax_subscription", $update_data);
        return $update_result;

    }

    function remove_subscription(){

        if (empty($where_data) || empty($update_data))
            return false;

        foreach ($where_data as $row_key=>$row_value)
            $this->db->where($row_key, $row_value);

        $update_result = $this->db->delete("showmax_subscription");
        return $update_result;

    }

    function validate_subscription_user($user_id, $showmax_data){

        return ( ($user_id == $showmax_data["id_user"]) && ( $user_id == $showmax_data['showmax_subscription']["user_id"]))
            ? true : false;
    }



    function get_subscription_types(){

        return $this->showmax_api->subscription_types;
    }

    function prepare_data_for_subscription_edit(&$data = array()){

        $data["subscription_statuses"] = array_combine($this->subscription_statuses, $this->subscription_statuses);
        $data["subscription_mapper"] = $this->status_mapper;
        $data['subscription_types'] = $this->get_subscription_types();
        $data['subscription_types'] = array_combine($data['subscription_types'], $data['subscription_types']);
        $data["subscription_termination"] = $this->terminate_subscription_options;

        // TODO : check the return variable (is it link or data)
        return $data;
    }

    function check_if_order_exists($where_data){


        // soft validation
        $current_order = $this->get_subscriptions($where_data);

        // TODO : perhaps include default message for users
        $default_response = $this->default_manger_response;
        $default_response["message"] = "Order doesn't exist";
        $result = (empty($current_order)) ? false : true ;

        return  array("result" => $result, "response" => $default_response );

    }

    // check if subscription type is a valid type
    function validate_subscription_type($subscription_type){

        // $subscription_types
        return in_array($subscription_type, $this->showmax_api->subscription_types);
    }

    function validate_subscription_status($subscription_status){

        return in_array($subscription_status, $this->subscription_statuses);
    }

    function validate_suspend_type($suspend_type){

        return in_array($suspend_type, array_keys($this->terminate_subscription_options));
    }


    function validate_request_for_subscription_update($request_data,  $validation_model, $form_validation){


        $params = $validation_model->handle_showmax_subscription_update();
        $form_validation->set_rules_for_update_showmax_subscription();
        if ($form_validation->run() == FALSE)
            return false;
        // TODO : error message to viewer
        $subscription_type_validation = $this->validate_subscription_type(NULL);
        $subscription_status_validation = $this->validate_subscription_status(NULL);
        $suspend_type_validation = $this->validate_suspend_type(NULL);

        if (!($subscription_type_validation && $subscription_status_validation && $suspend_type_validation))
            return false;




        // TODO : Later show subscriptions types

    }









}