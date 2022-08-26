<?php
class Port_model extends CI_Model
{


    private $table_fields = array(

      'id', 'order_id', 'product_id', 'user_id', 'original_service_id', 'original_service_name',
      'new_service_id', 'new_service_name', 'restore_status', 'assign_date',
      'assign_month_str', 'port_duration', 'restore_schedule', 'restore_date', 'is_admin',
    );


    /**
     * @param $order_id
     * @param int $is_admin if $id_admin == 1 ->> ignore some validation restrictions
     * @param bool $user  if $user == false ->> script was executed form the admin controller and will use admin model
     * @return array
     */
    public function wrap_port_reset($order_id,  $is_admin = 0, $user = false){


        $path_prefix = 'admin';
        if ($user)
            $path_prefix = 'user';

        // get data for port reset
        if (!isset($this->order_model))
            $this->load->model($path_prefix . '/order_model');

        if (!isset($this->product_model))
            $this->load->model($path_prefix . '/product_model');

/*
        if (!isset($this->is_classes))
            $this->load->model($path_prefix . '/is_classes');
*/

        if (!isset($this->network_api_handler_model))
            $this->load->model('network_api_handler_model');

        $port_reset_result = $this->process_port_reset($order_id,  $is_admin, $this->order_model, $this->product_model, $this->network_api_handler_model);
        return $port_reset_result;
    }



    /**
     *
     *
     * @param array $order_data         from `orders` table
     * @param array $product_data       from `products` table
     * @param array $old_service_data   from class mapper
     * @param $new_service_data         from class mapper
     * @param null $api_model           link to network API handler model
     * @return array
     */

   // check  process_port_reset_client()
   public function process_port_reset($order_id,  $is_admin = 0, &$order_model, &$product_model, &$network_api_handler_model){

       $response = array(
           'result'       => false,
           'message'      => "Port was reset unsuccessful", //TODO : default error message
           'user_message' => "Port was reset unsuccessful, please contact the admin to resolve it",
       );


       // debug
       //error_reporting(E_ALL);
       //ini_set('display_errors', 1);


       $order_data = $order_model->get_order_data($order_id);
       $product_data = $product_model->get_product_data_for_port_reset($order_data['product']);
       $old_service_info = $product_model->get_classes_data($product_data['class_id']);
       $new_service_info = $product_model->get_classes_data($product_data['port_service_id']);
       $old_service_data = $old_service_info[0];
       $new_service_data = $new_service_info[0];

       $additional_data = array(
           'order_id' => $order_id,
           'is_admin' => $is_admin,
           'order_account' => $order_data['account_username'] . "@" . $order_data['realm'],
       );


        // validationE!E
       $validation_result = $this->reset_port_validation($order_data, $product_data, $old_service_data, $new_service_data, $is_admin);
       if (!$validation_result['result']){
           $response['message'] = $validation_result['message'];
           $this->check_fail_api(array("result" => $validation_result['result'], "message" => $response['message']), $additional_data);
           return $response;
       }


       // $order_model = get_order_data($order_id)
       $log_data = array(

            'order_id'                => $order_data['id'],
            'product_id'              => $product_data['id'],
            'user_id'                 => $order_data['id_user'],

            // services
            'original_service_id'     => $old_service_data['table_id'],
            'original_service_name'   => $old_service_data['id'],
            'new_service_id'          => $new_service_data['table_id'],
            'new_service_name'        => $new_service_data['id'],

            // status and date
            'restore_status'          => 0,
            'assign_date'             => '',
            'assign_month_str'        => '',

            'port_duration'           => $product_data['port_duration'],
            'restore_schedule'        => '',
            'restore_date'            => '',

            'is_admin'                => $is_admin,
        );


       // calculate restore time and set assign date
       $log_data['assign_date'] = date("Y-m-d H:i:s");
       $log_data['assign_month_str'] = date("Y-m");

       // parse port duration and convert to PxDTxHxI
       $interval_string = $this->parse_time_duration($product_data['port_duration']);
       if (empty($interval_string)){
           $response['message'] = 'Interval validation error';
           $additional_data["log_data"] = $log_data;
           $this->check_fail_api(array("result" => false, "message" => $response['message']), $additional_data);
           return $response;
       }

       $restore_schedule_object   = DateTime::createFromFormat("Y-m-d H:i:s", $log_data['assign_date']);
       $duration_interval         = new DateInterval($interval_string);
       $restore_schedule_object->add($duration_interval);

       // check month
       // if ( (int)date("m") != (int)$restore_schedule_object->format("m")) {

       //}



       $log_data['restore_schedule'] = $restore_schedule_object->format("Y-m-d H:i:s");

       // try to assign new class
       //$api_result = $this->assign_new_class($order_data);

        // MOCK FOR TEST
        //$api_result = $is_class->set_new_class_with_handler($order_data, $new_service_data['id']); // check on the client side
        //$api_result['result'] = false;
        //$api_result['message'] = "Some failure message (admin)";
        //$api_result['api_response'] = "some API code (adminv)";

       $api_result = $network_api_handler_model->set_class_to_user($order_data, $new_service_data['id']);

       /*
        api_result :

        "result"        => false,
        "message"       => "Failure : Default message",
        "user_message"  => "Failure : Please contact the admin to resolve it",
        "api_response"  => null,
        */

       if (!$api_result['result']) {
           // log all unsuccessful results
           $additional_data = array(
             'order_id' => $order_id,
             'is_admin' => $is_admin,
             'log_data' => $log_data,
             'order_account' => $order_data['account_username'] . "@" . $order_data['realm'],
             'api_response' => $api_result['api_response'],
           );
           $this->check_fail_api($api_result, $additional_data);

           $response['message'] = $api_result['message']; // add user default error message
           return $response;
       }

       $result = $this->insert_new_log($log_data);
       $response['result'] = true;
       $response['user_message'] = $response['message'] = "Thank you. Your Telkom Port and OpenWeb Account Port has been reset. Kindly power down your ADSL router for 30 minutes then back on.";
       return $response ;
   }




    function client_port_validation($order_data, $product_data, $old_service, $new_service){

        $response = array(

            'port_available' => 0,
            'port_enabled'   => 0,
            'message'        => '',

        );

        /* codes :
           0 : default
           1 : all fine
           2 : order is not active
           3 : port reset is not active
           4 : counter limitation
           5 : previous reset is not finished yet
           6 : realm validation
        */

        $reset_validation = $this->reset_port_validation($order_data, $product_data, $old_service, $new_service, 0);
        $response_code = $reset_validation['code'];

        if ( ($response_code == 2) || ($response_code == 3) )
            return $response;


        if ( ($response_code == 4) || ($response_code == 5) || ($response_code == 6) ){
            $response['port_available'] = 1;
            $response['message'] = $reset_validation['message'];
            return $response;
        }

        $response['port_available'] = 1;
        $response['port_enabled'] = 1;
        return $response;

    }






    function reset_port_validation($order_data, $product_data, $old_service, $new_service, $is_admin = 0){

        $response = array(
            'result'  => false,
            'message' => ' test ',
            'code'    => 0,
        );

        /* codes :
           0 : default
           1 : all fine
           2 : order is not active
           3 : port reset is not active
           4 : counter limitation
           5 : previous reset is not finished yet
           6 : realm validation
        */


        // 0.0 check order status
        if ($order_data['status'] != 'active'){
            $response['message'] = 'The order is not active'; // TODO : validation error message #0.0
            $response['code'] = 2;
            return $response;
        }


        // 0. check if port_reset flag is activated
        // -------------------------------------------------------------------------------
        if ($product_data['port_active'] != 1){

            $response['message'] = 'The port reset is not active.'; // TODO : validation error message #0
            $response['code'] = 3;
            return $response;
        }


        // 1. check counter
        // -------------------------------------------------------------------------------
        // get counter
        $port_counter = $product_data['port_counter'];

        // get all port resets for current month
        $current_month = date("Y-m");
        $check_array = array(

            'order_id'                => $order_data['id'],
            'user_id'                 => $order_data['id_user'],
            'assign_month_str'        => $current_month,
            'is_admin'                => '0',
        );

        $check_result = $this->get_log_by_fields($check_array);
        $check_result_count = count($check_result);
        if (($check_result_count >= $port_counter) && ($is_admin == 0) ){

            $response['message'] = 'Sorry, you are only permitted to reset your port ' . $port_counter
                                 . ' times per month. The counter resets on the 1st day of each month.'; // TODO : validation error message #1
            $response['code'] = 4;
            return $response;
        }


        // 2. check previous port reset
        // ----------------------------------------------------------------------------------------
        // check all unfinished rows for current month (incl. admin)
        unset($check_array['is_admin']);
        $check_result = $this->get_log_by_fields($check_array);
        if (!empty($check_result))
            foreach ($check_result as $log_row)
                if ($log_row['restore_status'] == 0){


                    // calculate countdown
                    $assign_date   = DateTime::createFromFormat("Y-m-d H:i:s", $log_row['assign_date']);
                    $restore_schedule = DateTime::createFromFormat("Y-m-d H:i:s", $log_row['restore_schedule']);
                    $current_time = new DateTime('now - 35 minutes');

                    $assign_date_str = $assign_date->format("H:i d/m");
                    $countdown = $restore_schedule->diff($current_time);


                    $countdown_hours = ceil($countdown->d*24 + $countdown->h + $countdown->i/60); // PHP 5.3> . need to check !

                    $reset_message = "You can reset your port again in ";
                    // hours > 2
                    $countdown_message = $reset_message  . " " . $countdown_hours . " hours";
                    if ($countdown_hours < 2)
                        $countdown_message =  $reset_message  . " a " . $countdown_hours . " hour";
                    
                    if ($countdown->m > 0)
                        $countdown_message = "The countdown to the next reset more than a month."; // TODO : message

                    if ($restore_schedule <= $current_time)
                        $countdown_message = "The next reset will be available soon."; // TODO : message


                    //$response['message'] = 'The previous reset is not finished yet.';
                    $response['message'] = "You reset your port on " . $assign_date_str  .  ". " . $countdown_message;
                   // $response['message'] = '';

                    $response['code'] = 5;
                    return $response;
                }


       // 3. checks realms matching
       // -----------------------------------------------------------------------------------------
        //$realm_validation = $this->realm_validation($old_service['realm'], $new_service['realm']);
        $realm_validation = true; // disable realm validation
        if (!$realm_validation){
            $response['message'] = 'The reset service is not valid.'; // TODO : validation error message #3
            $response['code'] = 6;
            return $response;
        }

        $response['result'] = true;
        $response['message'] = 'success';
        return $response;
    }



    public function realm_validation($old_realm, $new_realm){

        return true; // disable realm validation
        if ($old_realm != $new_realm)
            return false;

        return true;
    }

    /**
     *  Validate `port_duration` from the `products` table
     *
     * @param  string $port_duration string in the next format : d:H:i
     * @return bool|string
     */
   public function parse_time_duration($port_duration){

       // parse port duration
       $port_duration_array = explode(":", $port_duration);

       // validation
       if (count($port_duration_array) != 3 )
           return false;


       foreach ($port_duration_array as $number){

           preg_match("/[0-9]{2}/", $number, $matches);
           if (empty($matches) || (count($matches) > 1))
               return false;

           if (!isset($matches[0]) || (strlen($matches[0]) != 2))
               return false;
       }

       $port_duration_interval = "P" . $port_duration_array[0] .
                                "DT" . $port_duration_array[1] .
                                 "H" . $port_duration_array[2] . "M";

       return $port_duration_interval;
   }

    /**
     * Inserts the new row into the log (table `port_log`)
     *
     * @param $data
     * @return mixed
     */
   public  function insert_new_log($data){

       $new_log = $data;


       $result = $this->db->insert('port_log', $new_log);
       return $result;

   }


   public function update_log($log_id, $log_data){

       $this->db->where('id', $log_id);
       $update_result = $this->db->update('port_log', $log_data);
       return $update_result;

   }

    public function get_log_row_by_id($id){

        if (empty($id))
            return false;

        $this->db->select();
        $this->db->where('id', $id);
        $query = $this->db->get('port_log');
        $result = $query->first_row('array');
        return $result;
    }

    public function get_log_by_fields($data_array){


        $this->db->select();
        foreach ($data_array as $field=>$value){

            if (!in_array($field, $this->table_fields))
                return false;

            $this->db->where($field, $value);
        }
        $this->db->order_by('assign_date', 'desc');
        $query = $this->db->get('port_log');
        $result = $query->result_array();

        return $result;
    }



    public function check_cron_key($key){

        if ($key != 'lkuinnnfpuoslcuy34xjnca324')
            return false;

        return true;

    }


    public function process_cron_restoration(){

        // get current date and 20 top unrestored rows
        $current_date = date("Y-m-d H:i:s");

        // get 20 top unrestored rows where attempts < %attempt_limit%
        $attempt_limit = 15;
        $unrestored_rows = $this->get_all_unrestored_rows($current_date, 20, $attempt_limit);

        // start restoring all services
        $results = $this->restore_collection_of_services($unrestored_rows);


        return true;
    }

/* not in use
    public function process_unsuccessful_restoration($results_array){

    	// --- thoughts ---
        // count unsuccessful restorations
        // --- it will be a $skip_value$

        //  %limit_value%
        // additiona case : %row_id% > $last_unsuccessful_id$   e.g. : 28
        // or add 'attempts count'

    }
*/

    public function get_all_unrestored_rows($date_line = null, $limit = 10, $attempt_limit = 15){

        // set current time if $date_line is empty
        if (empty($date_line))
            $date_line = date("Y-m-d H:i:s");

        $this->db->select();
        // get all unfinished rows with date which is smaller that now
        $this->db->where('restore_schedule <=', $date_line);
        $this->db->where('restore_status', '0');
        $this->db->where('restoration_attempts <', $attempt_limit);
        $this->db->order_by('restore_schedule', 'asc');
        $this->db->limit($limit);

        $query = $this->db->get('port_log');
        $result = $query->result_array();

        return $result;
    }


    public function restore_service($row){


        // don't check 'restore_schedule' and 'restore_status'
        // only force restore

        /*
         * (
            [id] => 5
            [order_id] => 12768
            [product_id] => 44
            [user_id] => 8901
            [original_service_id] => 267
            [original_service_name] => ow-1024-std
            [new_service_id] => 248
            [new_service_name] => ow-hc3
            [restore_status] => 0
            [assign_date] => 2016-06-28 17:57:55
            [assign_month_str] => 2016-06
            [port_duration] => 02:05:03
            [restore_schedule] => 2016-06-30 23:00:55
            [restore_date] => 0000-00-00 00:00:00
            [is_admin] => 0
            ["restoration_attempts"]=> string(1) "0"
            ["last_attempt"]=> NULL


            )
         */

            // assign data
            $log_id = $row['id'];
            $order_id = $row['order_id'];

            $original_service_id = $row['original_service_id'];
            $original_service_name = $row['original_service_name'];

            $new_service_id = $row['new_service_id'];
            $new_service_name = $row['new_service_name'];

            $response = array(
                'result'   => false,
                'message'  => 'test message', //TODO : default error message
            );


            // get data for port reset
            if (!isset($this->order_model))
                $this->load->model('admin/order_model');

            if (!isset($this->product_model))
                $this->load->model('admin/product_model');

            // get order data with classes
            $order_data = $this->order_model->get_order_data($order_id);
            // $product_data = $this->product_model->get_product_data($order_data['product']);
            // $product_data = $product_data['product_settings'];
            $old_service_info = $this->product_model->get_classes_data($original_service_id);
            $new_service_info = $this->product_model->get_classes_data($new_service_id);
            $old_service_data = $old_service_info[0];
            $new_service_data = $new_service_info[0];


            // validation
            $validation_result = $this->restore_port_validation($order_data, $old_service_data, $new_service_data);
            if (!$validation_result['result']){
                $response['message'] = $validation_result['message'];
                return $response;
            }


            // try to assign new class
            /*
            if (!isset($this->is_classes))
                $this->load->model('admin/is_classes');
            */
            if (!isset($this->network_api_handler_model))
                $this->load->model('network_api_handler_model');

            //$api_result = $this->is_classes->set_new_class_with_handler($order_data, $original_service_name); // check on the client side
            $api_result = $this->network_api_handler_model->set_class_to_user($order_data, $original_service_name);

            $response['message'] = $api_result['message'];

            // update attempts and last change
            $current_attempts_count = (int)$row["restoration_attempts"];
            $log_data["restoration_attempts"] = $current_attempts_count + 1;
            $log_data["last_attempt"] = date("Y-m-d H:i:s");


            // success restoration
            if ($api_result['result']) {

                $log_data['restore_status'] = '1';
                $log_data['restore_date'] = $log_data["last_attempt"];
                $response['result'] = true;
                $response['message'] = "Service was successfully restored";
            }

            // update log
            $result = $this->update_log($log_id, $log_data);
            return $response;

    }

    public function restore_collection_of_services($data_array){

        foreach ($data_array as $row){
            if (!empty($row['id']))
                $row_result[$row['id']] = $this->restore_service($row);
        }

        return $row_result;
    }


    public function restore_port_validation($order_data,  $old_service, $new_service){


        $response = array(
            'result'  => false,
            'message' => ' test ',

        );

        // ? 0.0 check order status
        if ($order_data['status'] != 'active'){
            $response['message'] = 'The order is not active'; // TODO : validation error message #0.0
            return $response;
        }


        // 3. checks realms matching
        // -----------------------------------------------------------------------------------------
        $realm_validation = $this->realm_validation($old_service['realm'], $new_service['realm']);
        if (!$realm_validation){
            $response['message'] = 'The reset service is not valid.'; // TODO : validation error message #3
            return $response;
        }

        $response['result'] = true;
        $response['message'] = 'success';
        return $response;

    }



    public function check_fail_api($api_result, $additional_data){

            if ($api_result['result'] != false)
                    return true;

            $date = date("Y-m-d H:i:s");
            $head = "\n\n\n ----- " . $date . " ----- ";
            $description = "\n Port Rest log \n ---------------";
            $response_dump = "\n : Api result" . print_r($api_result, true);

            $additional_data_text = "\n\n Additional data : ";
            foreach ($additional_data as $key => $row){
                $additional_data_text .= "\n " . $key . " : " . print_r($row, true);
            }

            $final_log = $head;
            $final_log .= $description;
            $final_log .= $response_dump;
            $final_log .= $additional_data_text;

            // add API code

            $this->write_port_reset_log($final_log);

            /*
             Possible response formats

                       ('result' => $result, 'message' => $response_message,
                        'session' => $sess, 'user_message' => $user_message)

                        ('result' => false, 'message' => "Account username and realm can't be empty",
                        'user_message' => "Account username and realm can't be empty")

                        ('result' => $result, 'message' => $message, 'user_message' => $user_message)
             */




    }
    

    public function write_port_reset_log($str){


            $log_file_path =  dirname(__FILE__)  . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "port-reset-api-log.txt";

            $log_handle = fopen($log_file_path,'a+');
            fwrite($log_handle,"\n " . $str);
            fclose($log_handle);

        }



}