<?php

class Validation_model extends CI_Model {



    private $fields_registration_form = Array('username', 'email_address', 'mobile_number',
                                             'password', 're_password', 'first_name',
                                             'last_name', 'sa_id_number');

    private $fields_mobile_docs_form = Array('physical_delivery_address', 'city', 'postcode');

    private $fields_assing_adsl_order = array('service', 'username', 'productType', 'product_id',
                                          'legacyProduct', 'price', 'proRata', 'status',
                                          'account_username', 'account_password', 'realm',
                                          'change_flag', 'display_usage', 'cancel_flage',
                                          'email_sms', 'write_to_log', 'avios_code' );

    private $fields_assign_fibre_data_order = array( 'service', 'username', 'product_name_fd',
                                                'price', 'proRata', 'status', 'username_fd',
                                                'password_fd', 'provider_fd', 'change_flag_fd',
                                                'display_usage_fd', 'cancel_flage',
                                                'email_sms', 'write_to_log', 'billing_cycle', 'avios_code' );

    private $fields_assign_lte_a_order = array( 'service', 'username', 'product_name_fd',
                                                'price', 'proRata', 'status', 'username_la',
                                                'password_la', 'change_flag_fd',
                                                'display_usage_fd', 'cancel_flage',
                                                'email_sms', 'write_to_log', 'billing_cycle', 'avios_code',
                                                'lte_type', 'total_data','sim_serial_number');

    private $fields_update_fibre_data_order = array('fibre_id', 'product_name_fd',
                                            'username_fd', 'password_fd', 'provider_fd', 'avios_code');

    private $fields_update_lte_a_order = array('fibre_id', 'product_name_fd',
                                                'username_fd', 'password_fd', 'avios_code');

    private $fields_assign_fibre_line_order = array( 'service', 'username', 'product_name_fd',
                                                'price', 'proRata', 'number_fl',
                                                'status', 'email_sms', 'write_to_log', 'billing_cycle', 'avios_code');

    private $fields_assign_showmx_sub_order = array("service", "username", "showmax_subscription_type",
                                                    "price", "proRata", "status");

    private $fields_update_fibre_line_order = array('fibre_id', 'product_name_fd', 'number_fl' );


    private $fields_update_showmax_usbscription = array("id", "account_id", "activation_code", "subscription_type",
                                                            "subscription_status", "subscription_suspend_type");




    function process_post_field($field){

        $val = $this->input->get_post($field, TRUE);
       // $val = strip_tags(mysql_real_escape_string($val));
        $val = strip_tags($val);
        $val = trim($val);

        return $val;

    }


    function process_int_post_field($field){

        $post_val = $this->process_post_field($field);
        $int_val = 0;

        if ( !empty($post_val) && (strval(intval($post_val)) == $post_val) )
            $int_val = (int)$post_val;

        return $int_val;
    }



    function process_value($val){

        //$val = strip_tags(mysql_real_escape_string($val));
        $val = strip_tags($val);
        $val = trim($val);
        return $val;

    }


    function set_rules_for_registration(){

        $config = array(
            array(
                'field'   => 'username',
                'label'   => 'Username',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[5]|max_length[20]|alpha_dash|is_unique[membership.username]',
            ),
            array(
                'field'   => 'email_address',
                'label'   => 'Email Address',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[50]|valid_email|is_unique[membership.email_address]',
            ),
            array(
                'field'   => 'mobile_number',
                'label'   => 'Mobile Number',
              //was unique  'rules'   => 'trim|strip_tags|mysql_real_escape_string|min_length[3]|max_length[50]|alpha_dash|is_unique[membership.mobile_number]',
                'rules'   => 'trim|strip_tags|mysql_real_escape_string|min_length[3]|max_length[50]|alpha_dash',

            ),

            array(
                'field'   => 'password',
                'label'   => 'Password',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[5]|max_length[50]|matches[re_password]',

            ),
            array(
                'field'   => 're_password',
                'label'   => 'Confirm Password',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[50]',

            ),
            array(
                'field'   => 'first_name',
                'label'   => 'First Name',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[1]|max_length[50]',

        ),
            array(
                'field'   => 'last_name',
                'label'   => 'Last Name',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[1]|max_length[50]',

            ),
            array(
                'field'   => 'sa_id_number',
                'label'   => 'SA ID Number',
               // was unique 'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[10]|max_length[20]|numeric|is_unique[billing.sa_id_number]',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[10]|max_length[20]|numeric',

            ),


        );

        $this->form_validation->set_rules($config);

    }

    function set_rules_for_mobile_docs(){

        $config = array(

            array(

                'field' => 'physical_delivery_address',
                'label' => 'Physical Delivery Address',
                'rules' => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[250]',

            ),
            array (

                'field' => 'city',
                'label' => 'City',
                'rules' => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[250]',

            ),

            array(

                'field' => 'postcode',
                'label' => 'Postcode',
                'rules' => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[250]',
            ),

        );

        $this->form_validation->set_rules($config);

    }




    function get_registration_fields(){

        return $this->fields_registration_form;
    }

    function get_form_fields($parameter){

        $return_fields = '';
        switch ($parameter){

            case 'registration' : $return_fields = $this->fields_registration_form;
            case 'mobile_data'  : $return_fields = $this->fields_mobile_docs_form;

            default : $return_fields = '';
        }

        return $return_fields;

    }

    function re_populate_registration_form(){
        $repopulated_array = array();
        foreach ($this->fields_registration_form  as $field){

            // $repopulated_array[$field] = set_value($field);
            // maybe - remove back-slashes \
            $repopulated_array[$field] =  $this->input->get_post($field, TRUE);

        }

        return $repopulated_array;

    }

    function re_populate_form($form_type)
    {

        switch ($form_type) {

            case 'registration' :
                $fields_array = $this->fields_registration_form;
                break;
            case 'mobile_data_client' :
                $fields_array = $this->fields_mobile_docs_form;
                break;

            default :
                $fields_array = false;
        }

        $repopulated_array = false;
        foreach ($fields_array as $field) {

            // $repopulated_array[$field] = set_value($field);
            // maybe - remove back-slashes \
            $repopulated_array[$field] = $this->input->get_post($field, TRUE);
        }

        return $repopulated_array;
    }


    function print_404(){
        show_404();
        die();
    }


    // -----------------------------------------------------------------------------


    function handle_assign_order_fields($service){

        $keys_array = array();
        

        switch ($service){
            case "adsl" :       $keys_array = new ArrayObject($this->fields_assing_adsl_order); break;
            case "fibre-data" : $keys_array = new ArrayObject($this->fields_assign_fibre_data_order); break;
            case "fibre-line" : $keys_array = new ArrayObject($this->fields_assign_fibre_line_order); break;
            case "lte-a"      : $keys_array = new ArrayObject($this->fields_assign_lte_a_order); break;
           // case "showmx-sub" : $keys_array = new ArrayObject($this->fields_assign_showmx_sub_order); break;
            default : $keys_array  = ''; break;

        }

        if (empty($keys_array))
            return false;

        $data_array = array();
        foreach($keys_array as $field)
            $data_array[$field] = $this->process_post_field($field);


        // validation rules
         // if isset username
        return $data_array;

    }

    function handle_update_order_fields($service){

        switch ($service){
           // case "adsl" :       $keys_array = new ArrayObject($this->fields_assing_adsl_order); break;
            case "lte-a" : $keys_array = new ArrayObject($this->fields_update_lte_a_order); break;
            case "fibre-data" : $keys_array = new ArrayObject($this->fields_update_fibre_data_order); break;
            case "fibre-line" : $keys_array = new ArrayObject($this->fields_update_fibre_line_order); break;
            default : $keys_array  = ''; break;

        }


        if (empty($keys_array))
            return false;

        $data_array = array();
        foreach($keys_array as $field)
            $data_array[$field] = $this->process_post_field($field);


        // validation rules
        // if isset username
        return $data_array;

    }

    function handle_showmax_subscription_update(){

        $data_array = array();
        foreach($this->fields_update_showmax_usbscription as $field)
            $data_array[$field] = $this->process_post_field($field);


        return $data_array;
    }


    /*
     *
    private $fields_update_showmax_usbscription = array(
                                                            "subscription_status", "subscription_suspend_type");
     */

    function set_rules_for_update_showmax_subscription(){

        $config = array(
            array(
                'field'   => 'id',
                'label'   => 'id',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|numeric',
            ),
            array(
                'field'   => 'account_id',
                'label'   => 'User ID',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[50]',
            ),
            array(
                'field'   => 'activation_code',
                'label'   => 'Activation Code',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[50]',
            ),

            array(
                'field'   => 'subscription_type',
                'label'   => 'Subscription Type',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[5]|max_length[50]',

            ),
            array(
                'field'   => 'subscription_status',
                'label'   => 'Subscription Status',
                'rules'   => 'trim|strip_tags|required|mysql_real_escape_string|min_length[3]|max_length[50]',

            ),
            array(
                'field'   => 'subscription_suspend_type',
                'label'   => 'Subscription Suspend Type',
                'rules'   => 'trim|strip_tags|mysql_real_escape_string|min_length[3]|max_length[50]',

            ),

        );

        // TODO : ?? magic method form_validation
        $this->form_validation->set_rules($config);
    }

    public function getRealmLteUsername($username) {

        $realm = substr($username, strpos($username, '@')+1);

        return $realm;
    }
}