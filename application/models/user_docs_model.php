<?php

class User_docs_model extends CI_Model {

    private $folder_salt = "Jm]93NhCaP9Las";
    private $file_salt = "kpo*02jvr,a";

    // upload configs
    private $allowed_types = "gif|jpg|png";
    //private $max_size      = '5000';
    private $max_size      = '5000';
    private $max_width     = '3000';
    private $max_height    = '3000';
    private $encrypt_name  =  true;


    private $base_folder = '';

    private $residence_type = 'residence';
    private $passport_type = 'passport';

    private $pdf_folder_pattern = 'application/PDFdocs/';

    private $sucess_message = 'Success message';
    private $error_message_filetype = 'The filetype you are attempting to upload is not allowed.';

    private $data_fields_sucess_message = 'Success fields';
    private $data_fields_error_message = 'Fail fields';

    private $user_personal_fields = array('delivery_address', 'city', 'postcode');

    private $mobile_upload_log_limit = 7;


    function __construct()
    {
        parent::__construct();
        $this->base_folder =  APPPATH . 'PDFdocs/';
    }



    function get_user_personal_folder($user_id, $username){

        $this->db->select('folder_name');
        //$this->db->where('username', $username);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('user_personal_folder');

        $user_folder = $query->first_row('array');
        $folder_final_name = '';

        if (empty($user_folder)){

            $folder_final_name = $this->save_user_personal_folder($user_id, $username);
        } else {

            $folder_final_name = $user_folder['folder_name'];
            if(is_dir($this->base_folder . $folder_final_name) == false)
                mkdir($this->base_folder . $folder_final_name, DIR_READ_MODE);

        }

        return $folder_final_name;

    }

    // save folder for secure docs
    function save_user_personal_folder($user_id, $username){

        // generate folder name
        $hash_name = $this->generate_new_folder_name($user_id, $username);

        if(is_dir($this->base_folder . $hash_name) == false)
            mkdir($this->base_folder . $hash_name, DIR_READ_MODE);

        $result = false;

        if(is_dir($this->base_folder  . $hash_name) == true){

            // save
            $insert_data = array(

                'user_id'     => $user_id,
                'username'    => $username,
                'folder_name' => $hash_name,

            );
            $insert_result = $this->db->insert('user_personal_folder', $insert_data);

            if ($insert_result)
                $result = $hash_name;
        }

        return $result;
    }

    //
    function generate_new_folder_name($user_id, $username){

        $str_date = date('H:i:s:d:m-y');
        $hash_str = $str_date . $user_id . $this->folder_salt . $username;
        $hash_result = md5($hash_str);

        return $hash_result;
    }


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    function get_file($user_id, $type){

        $this->db->select('path');
        $this->db->where('user_id', $user_id);
        $this->db->where('field_type', $type);

        $query           = $this->db->get('user_personal_docs');
        $file_path_array = $query->first_row('array');
        $file_path       = $file_path_array['path'];

        if (empty($file_path)) {

            $file_path = false;
        } elseif (file_exists($file_path) == false){

            $file_path = false;
        }

        return $file_path;
    }

    function get_file_full_data($user_id, $type, $log_id = false){

        $this->db->select('file_name, path, create_date, size, width, height, image_type, field_type');
        $this->db->where('user_id', $user_id);
        $this->db->where('field_type', $type);
        $table_name = 'user_personal_docs';
        if ($log_id != false ){

            $this->db->where('id', $log_id);
            $table_name = 'user_personal_docs_log';

        }

        $this->db->order_by("create_date", "desc");
        $query = $this->db->get($table_name);

        $file_path_array = $query->first_row('array');

        if (empty($file_path_array)) {

            $file_path_array = false;
        } elseif (file_exists($file_path_array['path']) == false){

            $file_path_array = false;
        }

        return $file_path_array;
    }

    function get_all_log_data($user_id){

        $return_array = '';
        $this->db->select('id, file_name, path, create_date, size, width, height, image_type, field_type');
        $this->db->where('user_id', $user_id);

        $table_name = 'user_personal_docs_log';

        $this->db->order_by("create_date", "desc");
        $query = $this->db->get($table_name);

        $file_path_array = $query->result_array();

        $rows_count = count($file_path_array);
        $final_path_array = false;
        $j = 0;
        for ($i = 0; $i < $rows_count; $i++){

            if (file_exists($file_path_array[$i]['path']) == false){
                $file_path_array[$i] = false; continue;
            }

            $final_path_array[$j] = $file_path_array[$i];
            $j++;
        }

       // $final_rows_count = count($final_path_array);

        return $final_path_array;
    }





    function remove_file_from_db($user_id, $type, $log_id = false){

        $table_name = 'user_personal_docs';
        $this->db->where('user_id', $user_id);
        $this->db->where('field_type', $type);

        if (!empty($log_id)){
            $this->db->where('id', $log_id);
            $table_name = 'user_personal_docs_log';
        }

        $delete_result =  $this->db->delete($table_name);
        return $delete_result;
    }


    function check_user_mobile_documents($user_id){

        $passport_path = $this->get_file($user_id, 'passport');
        $residence_path = $this->get_file($user_id, 'residence');



        $address_data_flag = true;
        $address_data = $this->get_address_data($user_id);

        if (empty($address_data)){
            $address_data_flag = false;
        } else {

            foreach ($this->user_personal_fields as $field){


                if (empty($address_data[$field])){
                    $address_data_flag = false;
                    break;
                } // end IF

            } //end FOREACH
        }

        if ( ($passport_path == false) || ($residence_path  == false) || ($address_data_flag == false))
            return false;


        return true;
    }



    function save_new_file($user_id, $username,  $field){

        // get file type by field
        $type =  $this->return_filetype_by_field($field);


        // check if folder for this user already exist
        $folder_name = $this->get_user_personal_folder($user_id, $username);

        // check if file of that type already exist
       // $file_path = $this->get_file($user_id, $type);
        $full_file = $this->get_file_full_data($user_id, $type);


        // if exist >> remove that file

       /* if ($file_path) {

            $remove_result =  $this->remove_old_file($user_id, $type);
        }
        */

        // config array for uploader
        $uploader_config = $this->configurate_uploader($folder_name);


        // save file according to config
        $this->load->library('upload', $uploader_config);

        // return array
        $return_array = array(

            'result'  => '',
            'message' => '',

        );


        $log_file_count = $this->get_count_of_log_files_by_type($user_id, $type);
       //PROD! MAKE Field on TOP
       // if ($log_file_count > 9 ){
        if ($log_file_count > $this->mobile_upload_log_limit ){

            $return_array['result'] = false;
            $return_array['message'] = 'Seems that you are updating this document too often. Please, contact admin to unblock this function.';

            return $return_array;
        }


        if ($this->upload->do_upload($field))
        {
            // success
            $data = array('upload_data' => $this->upload->data());

            $file_name_with_extension = $data['upload_data']['file_name'];
            $full_path = $data['upload_data']['full_path'];
            $fixed_full_path = $resultPath = strstr($full_path, $this->pdf_folder_pattern);

            $create_date = date("Y-m-d H:i:s");

            $is_image = $data['upload_data']['is_image'];

            $image_width = $data['upload_data']['image_width'];
            $image_height = $data['upload_data']['image_height'];
            $image_type = $data['upload_data']['image_type'];
            $file_size = $data['upload_data']['file_size'];

            $file_data = array(

                'file_name'     => $file_name_with_extension,
                'path'          => $fixed_full_path,
                'create_date'   => $create_date,

                'user_id'       => $user_id,
                'username'      => $username,
                'field_type'    => $type,

                'size'          => $file_size,
                'width'         => $image_width,
                'height'        => $image_height,
                'image_type'    => $image_type,

            );

            // check previous file
            if (!empty($full_file)) {

                $log_file_data = array(

                    'file_name'     => $full_file['file_name'],
                    'path'          => $full_file['path'],
                    'create_date'   => $create_date,

                    'user_id'       => $user_id,
                    'username'      => $username,
                    'field_type'    => $type,

                    'size'          => $full_file['size'],
                    'width'         => $full_file['width'],
                    'height'        => $full_file['height'],
                    'image_type'    => $full_file['image_type'],

                );

                // save previous file  to log
                $log_file_insert = $this->insert_file_to_log($log_file_data);
                // if files was transfered to log
                if ($log_file_insert['result']){
                    // remove this file from current database
                    $remove_result =  $this->remove_old_file($user_id, $type);
                }
            }

            // force to remove row from db (if file doesn't exist - > it returns false, but the row can stay in DB)
            $row_remove_result = $this->remove_file_from_db($user_id, $type);


            // save new file to database
            if ($is_image) {

                $insert_result = $this->db->insert('user_personal_docs', $file_data);

                $return_array['result'] = true;
                $return_array['message'] = $this->sucess_message;
            } else {


                $return_array['result'] = false;
                $return_array['message'] = $this->error_message_filetype;
            }

            /*
             array(1) {
                     ["upload_data"]=> array(14) {
                     ["file_name"]=> string(36) "4105435335760a7a0ad79e33e095be7a.jpg"
                     ["file_type"]=> string(10) "image/jpeg"
                     ["file_path"]=> string(95) "/var/www/html/lamp-wrk/keoma/home/gitrepo/application/PDFdocs/9c627bbc12f480628df193413a0660f4/"
                     ["full_path"]=> string(131) "/var/www/html/lamp-wrk/keoma/home/gitrepo/application/PDFdocs/9c627bbc12f480628df193413a0660f4/4105435335760a7a0ad79e33e095be7a.jpg"
                     ["raw_name"]=> string(32) "4105435335760a7a0ad79e33e095be7a"
                     ["orig_name"]=> string(37) "22calder-hartman-1-tmagArticle-v2.jpg"
                     ["client_name"]=> string(37) "22calder-hartman-1-tmagArticle-v2.jpg"
                     ["file_ext"]=> string(4) ".jpg"
                     ["file_size"]=> float(13.05)
                     ["is_image"]=> bool(true)
                     ["image_width"]=> int(592)
                     ["image_height"]=> int(355)
                     ["image_type"]=> string(4) "jpeg"
                     ["image_size_str"]=> string(24) "width="592" height="355"" }
            }

             */

        } else {

            // failure
            $error =  $this->upload->display_last_error();
            $error = str_replace("<p>", "", $error);
            $error = str_replace("</p>", "", $error);


            $return_array['result'] = false;
            $return_array['message'] = $error;

            if (!empty($full_file)){

                $return_array['result'] = true;
                $return_array['message'] = '';

            }

            /*
            array(1) { ["error"]=> string(79) "
                   <p>The file you are attempting to upload is larger than the permitted size.</p>
                    " }
             */

        }


        return $return_array;
    }


    function insert_file_to_log($log_file_data){

        $return_array['result'] = false;
        $return_array['message'] = '';
        $insert_result = $this->db->insert('user_personal_docs_log', $log_file_data);

        if ($insert_result){

            $return_array['result'] = true;
            $return_array['message'] = $this->sucess_message;
        }

        return $return_array;
    }

    function get_count_of_log_files_by_type($user_id, $type){

        if (empty($user_id))
            return false;

        if (empty($type))
            return false;


        $this->db->select();
        $this->db->where('user_id', $user_id);
        $this->db->where('field_type', $type);
        $this->db->from('user_personal_docs_log');
        $log_count = $this->db->count_all_results();

        return $log_count;

    }



    function remove_old_file($user_id, $type){


        if (empty($user_id))
            return false;

        if (empty($type))
            return false;

        $deleteArray = array(

            'field_type' => $type,
            'user_id' => $user_id,
        );
        $result = $this->db->delete('user_personal_docs', $deleteArray);
        if ($result){

            // unlink file
        }

        return $result;

    }

    // do not need , CodeIgniter  app uses config/encrypt for file uploads
    function generate_file_name($user_id, $username,  $type){


        $str_date = date('H:i:s:d:m-y');
        $hash_str =  $user_id . $this->file_salt . $type .  $str_date . $username;
        $hash_result = md5($hash_str);

        return $hash_result;

    }

    function return_filetype_by_field($field){

        $result_type = '';
        switch($field){

            case 'proof_of_residence' : $result_type = $this->residence_type; break;
            case 'id_or_passport' : $result_type = $this->passport_type; break;

            default : $result_type = 'none'; break;

        }

        return $result_type;
    }

    function configurate_uploader($folder_name){

        $config['upload_path']   = $this->base_folder . $folder_name;
        $config['allowed_types'] = $this->allowed_types;
        $config['max_size']	     = $this->max_size;
        $config['max_width']     = $this->max_width;
        $config['max_height']    = $this->max_height;
        $config['encrypt_name']  = $this->encrypt_name;

        return $config;

    }


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


    function save_address_data($data){

        // returns array with ['result'], ['message']
        $return_array = array(

            'result'  => '',
            'message' => '',
        );
        // ---------------------------------------- //

        if (empty($data) || empty($data['user_id'])) {
            $return_array['result'] = false;
            $return_array['message'] = "Data can't be empty";

            return $return_array;
        }

        $user_id = $data['user_id'];
        $data['update_date'] = date("Y-m-d H:i:s");

        $user_data = $this->get_address_data($user_id);
        if (empty($user_data)){

            // insert new data
            $return_array['result'] = $this->db->insert('user_personal_fields', $data);
        } else {

            // update old data
            $this->db->where('user_id', $user_id);
            $return_array['result']  =  $this->db->update('user_personal_fields', $data);
        }

        if ($return_array['result']){
            $return_array['message'] = $this->data_fields_sucess_message;
        } else {
            $return_array['message'] = $this->data_fields_error_message;
        }

        return $return_array;
    }

    function get_address_data($user_id){


        if (empty($user_id))
            return false;


        $this->db->select('delivery_address, city, postcode');
        $this->db->where('user_id', $user_id);

        $query = $this->db->get('user_personal_fields');

        $fields_array = $query->first_row('array');
        return $fields_array;


    }


    // --------------------------------------------------------------------


    function insert_new_mobile_request($data){



        $request_data = date("Y-m-d H:i:s");
        $status = 'unprocessed';

        if (empty($data) || empty($data['order_id']) || empty($data['user_id']) )
            return false;

        $check_this_request_by_order = $this->get_mobile_request_by_order_id($data['order_id']);
        if (!empty($check_this_request_by_order))
            return true; // this request already exist


        /*
        request_id | request_date | first_response_date | last_modification_time |
			| user_id | username | order_id | status | notice
        | mobile_sim  | mobile_details |
        */

        // save
        $request_data = array(

            'request_date' => $request_data,
            'user_id'      => $data['user_id'],
            'username'     => $data['username'],
            'order_id'     => $data['order_id'],
            'status'       => $status,

        );
        $insert_result = $this->db->insert('user_mobile_requests', $request_data);
        return $insert_result;
    }


    function get_mobile_request_by_id($request_id){

        $this->db->select();
        $this->db->where('request_id', $request_id);

        $query = $this->db->get('user_mobile_requests');

        $result_request = $query->first_row('array');
        return $result_request;

    }

    function get_mobile_request_by_order_id($order_id, $user_id = null){

        $this->db->select();
        $this->db->where('order_id', $order_id);
        if (!empty($user_id))
            $this->db->where('user_id', $user_id);

        $query = $this->db->get('user_mobile_requests');

        $result_request = $query->first_row('array');
        return $result_request;
    }


    function get_mobile_request_for_client($order_id, $user_id){

        if (empty($order_id))
            return false;

        if (empty($user_id))
            return false;

        $mobile_request = $this->get_mobile_request_by_order_id($order_id, $user_id);
        if (empty($mobile_request))
            return false;

        $response_data = array(

            'user_id'  => $mobile_request['user_id'],
            'order_id' => $mobile_request['order_id'],
            'status'   => $mobile_request['status'],
            'notice'   => $mobile_request['notice'],



        );
        if ($response_data['status'] == 'processed'){
            $response_data['mobile_sim'] = $mobile_request['mobile_sim'];
            $response_data['mobile_details'] = $mobile_request['mobile_details'];

        }

        return $response_data;

    }


    function get_all_user_mobile_requests($user_id){

        $this->db->select();
        $this->db->where('user_id', $user_id);

        $query = $this->db->get('user_mobile_requests');
        $result_requests = $query->result_array();

        return $result_requests;

    }

    function get_all_mobile_requests($options = null){

        $this->db->select();

        $query = $this->db->get('user_mobile_requests');
        $result_requests = $query->result_array();

        return $result_requests;

    }

    function get_all_mobile_requests_full($options = null){

        $select_row = 'user_mobile_requests.request_id, user_mobile_requests.request_date,
				user_mobile_requests.first_response_date, user_mobile_requests.last_modification_time,
				user_mobile_requests.user_id, user_mobile_requests.username, user_mobile_requests.order_id,
				user_mobile_requests.status, user_mobile_requests.notice, user_mobile_requests.mobile_sim,
				user_mobile_requests.mobile_details, orders.product as product_id, orders.account_username,
				orders.realm as account_realm, products.name as product_name, membership.first_name, membership.last_name';

        $this->db->select($select_row);
        $this->db->from('user_mobile_requests');
        $this->db->join('orders', 'orders.id = user_mobile_requests.order_id');
        $this->db->join('products', 'orders.product = products.id');
        $this->db->join('membership', 'membership.id = user_mobile_requests.user_id');


        if (isset($options['num']) && isset($options['start'])){
            $this->db->limit($options['num'], $options['start']);
        }

        if (isset($options['filter']) && ($options['filter'] != 'all')){
            $this->db->where('user_mobile_requests.status', $options['filter']);
        }

        if (isset($options['request_id']) && !empty($options['request_id'])  )
            $this->db->where('request_id', $options['request_id']);

        $this->db->order_by('request_id', 'desc');
        $query = $this->db->get();

        if (isset($options['count']) && ($options['count'] === true )){

            $result_requests = $query->num_rows();
        } else {
            $result_requests = $query->result_array();
        }


        return $result_requests;
    }

    function update_mobile_request($request_id, $data){


        // first_response_date
        // last_modification_time

        // user_id
        // username

        // order_id
        // status

        // notice
        // mobile_sim
        // mobile_details

        $data['last_modification_time'] = date("Y-m-d H:i:s");

        $this->db->where('request_id', $request_id);
        $update_result = $this->db->update('user_mobile_requests', $data);
        return $update_result;

    }


    // Unfinished
    function physically_file_remove($user_id, $username, $type, $log_id = false){

        if (empty($user_id) || empty($type))
            return false;


        $file_data = $this->get_file_full_data($user_id, $type, $log_id);
        $user_folder = $this->get_user_personal_folder($user_id, $username);

        $base_path = $this->pdf_folder_pattern;
        $base_folder_app = $base_path . $user_folder . "/";

        $pos_validation_result = strpos($file_data['path'], $base_folder_app);

        // 1st Validation
        if ($pos_validation_result  !== 0) {

            // not valid (base_url doesn't start form 0[zero] element)
            echo "path not valid";
            return false;
        }

       $parsed_file_name = str_replace($base_folder_app, '',$file_data['path'] );


        // 2nd Validation
        if ($parsed_file_name != $file_data['file_name']){

            // not valid (file names are not equal)
            echo "path(filename) not valid";
            return false;
        }

        $final_file_path = $this->base_folder . $file_data['path'];

        echo "<br/>" . $final_file_path;


        // check if file exist. If not -> removce just a row from DB
        // THIS already checks in /get_file_full_data/


       // bool unlink ( string $filename [, resource $context ] )

        //echo $user_folder;

        // check path
        // $pdf_folder_pattern
        // ["path"]=> string(89) "application/PDFdocs/9c627bbc12f480628df193413a0660f4/841d569bcacebef312aa65fd9686b256.jpg"

        // check folder pattern

    }

    function process_mobile_data_request($field){

        $val = $this->input->get_post($field, TRUE);
        $val = strip_tags(mysql_real_escape_string($val));
        $val = trim($val);
        return $val;

    }












}