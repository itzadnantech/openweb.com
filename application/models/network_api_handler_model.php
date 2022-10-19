<?php
class Network_api_handler_model extends CI_Model
{


    public $api_collection;

    private $rewrite_api = null;
    private $available_api_names;


    public $forceClassMapValidation = array(
        "isdsl"   => true,
        "ionline" => false,
    );


    private $usagePeriodFieldMapper = array(
        //basic 
        "day"         => "day_usage",
        "month"       => "month_usage",
        "year"        => "year_usage",
    );


    private $usageResponseFormat = array(

        "day_usage" => null, "month_usage" => null, "year_usage" => null,
    );

    // get API id from realms
    // int api_collection
    // for methods : get API name by id
    // call internal methods by api name

    public function set_rewrite_api($rewrite_api_value = null)
    {

        if (empty($rewrite_api_value)) {
            $this->rewrite_api = null;
            return;
        }

        if (in_array($rewrite_api_value, $this->available_api_names))
            $this->rewrite_api = $rewrite_api_value;


        return;
    }


    private $defaultResponseData = array(
        "result"        => false,
        "message"       => "Failure : Default message",
        "user_message"  => "Failure : Please contact the admin to resolve it",
        "api_response"  => null,
    );

    public $defaultGetPendingUpdateError = "Data about pending update is not available for this moment";


    public function __construct()
    {

        $this->init_available_api();
    }


    public function init_available_api()
    {

        $api_list = $this->get_all_available_api();
        foreach ($api_list as $api_row) {

            $this->api_collection[$api_row["id"]] = $api_row["name"];
            $this->available_api_names[] = $api_row["name"];
        }
        return;
    }

    public function get_all_available_api()
    {

        $this->db->select();
        $query = $this->db->get('network_api_data');
        $result = $query->result_array();
        return $result;
    }


    /**   [tested]
     * @param $class    string
     * @param $user     string
     * @param $pass
     * @param $comment
     * @param $email
     * @return array
     */
    public function add_new_realm_user($order_data, $class, $pass, $comment, $email)
    {

        // add_realm_new($sess, $class, $user, $pass, $comment, $email)
        // $order_data  array('realm' = 'some-realm', 'account_username' => 'some account username')
        $order_data = $this->pre_process_order_data($order_data);


        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];

        // get and process class via mapper
        $classMapperRow = $this->process_class_from_mapper($class);
        $classValidation = $this->check_class_map_for_api($classMapperRow[$apiName], $apiName);

        $this->apply_force_map_validation($class, $apiName, $classMapperRow, $classValidation);

        $inputData = array(
            "order_data" => $order_data,
            "class"      => $class,
            "pass"       => $pass,
            "comment"    => $comment,
            "email"      => $email,
        );


        $this->check_fail_api($classValidation, $inputData);
        if (!$classValidation['result']) {
            return $classValidation;

            // DEBUG MOCK!
            //echo "class doesn't have a mapper";
            //$classMapperRow[$apiName]['name'] = $class;

        }

        switch ($apiName) {
            case 'isdsl':
                $result = $this->add_new_realm_user_isdsl($order_data, $classMapperRow[$apiName]['name'], $pass, $comment, $email);
                break;
            case 'ionline':
                $result = $this->add_new_realm_user_ionline($order_data, $classMapperRow[$apiName]['id'], $pass, $comment, $email);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, $inputData);
        return $result;
    }



    public function add_new_realm_user_isdsl($order_data, $class, $pass, $comment, $email)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        return $this->is_classes->add_realm_account_with_handler($order_data, $class, $pass, $comment, $email);
    }

    public function add_new_realm_user_ionline($order_data, $class, $pass, $comment, $email)
    {

        if (!isset($this->is_classes))
            $this->load->model('ionline_api_model');

        return $this->ionline_api_model->create_new_account($order_data, $class, $pass, $comment, $email);
    }


    /** [tested] 

     */
    public function cancel_account($order_data,  $date)
    {

        // $order_data  array('realm' = 'some-realm', 'account_username' => 'some account username')
        // $date in format Y-m-d
        if (empty($date))
            $date = date("Y-m-d");

        $order_data = $this->pre_process_order_data($order_data);

        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];

        // get and process class via mapper
        //$classMapperRow = $this->process_class_from_mapper($class);
        // $classValidation = $this->check_class_map_for_api($classMapperRow[$apiName], $apiName);

        //if (!$classValidation['result'])
        //   return $classValidation;

        switch ($apiName) {
            case 'isdsl':
                $result = $this->cancel_account_isdsl($order_data, $date);
                break;
            case 'ionline':
                $result = $this->cancel_account_ionline($order_data, $date);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, array("order_data" => $order_data,  "date" => $date));
        return $result;
    }


    public function cancel_account_isdsl($order_data, $date)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        // default ERROR response
        //$response = $this->defaultResponseData;
        $currentDate = date("Y-m-d");
        switch ($date) {
            case $currentDate:
                break; // cancel order today
            default:
                break; // cancel order next month (pending_cancaltion)
                // $pending_resp = $this->is_classes->set_pending_update_new($sess, $acc_realm_user, 'nosvc');
        }
        //die("inisde isdsl cancel");
        $response = $this->is_classes->delete_realm_account_with_handler($order_data);
        return $response;
    }


    public function cancel_account_ionline($order_data, $date)
    {

        if (!isset($this->is_classes))
            $this->load->model('ionline_api_model');


        return $this->ionline_api_model->cancel_account($order_data, array("cancel_date" => $date));
    }


    /** [tested]
     *
     *
     * @param $order_data
     * @param array $data
     * @return array
     *
     */
    public function suspend_account($order_data, $data = array("status" => ''))
    {

        // check status param
        if (!isset($data["status"]))
            $data = array("status" => "");

        $order_data = $this->pre_process_order_data($order_data);

        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];

        // get and process class via mapper
        //$classMapperRow = $this->process_class_from_mapper($class);
        // $classValidation = $this->check_class_map_for_api($classMapperRow[$apiName], $apiName);

        //if (!$classValidation['result'])
        //   return $classValidation;

        switch ($apiName) {
            case 'isdsl':
                $result = $this->suspend_account_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->suspend_account_ionline($order_data, $data["status"]);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, array("order_data" => $order_data,  "data" => $data));
        return $result;
    }

    public function suspend_account_isdsl($order_data)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        return $this->is_classes->suspend_account_with_handler($order_data);
    }

    public function suspend_account_ionline($order_data, $status)
    {

        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');

        // $status -> “suspended” or “billingSuspended”
        // corresponding status will be assigned inside suspend_account(), if $status is empty

        return $this->ionline_api_model->suspend_account($order_data, $status);
    }


    /**[testing] skip this


     */
    public function set_no_service_account($order_data, $default_class = "nosvc")
    {


        // $order_data  array('realm' = 'some-realm', 'account_username' => 'some account username')
        $order_data = $this->pre_process_order_data($order_data);

        $inputData = array(
            "order_data" => $order_data,
            "default_calass" => $default_class,

        );


        // TODO : check class inside mapper

        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];


        switch ($apiName) {
            case 'isdsl':
                $result = $this->set_no_service_account_isdsl($order_data, $default_class);
                break;
            case 'ionline':
                $result = $this->set_no_service_account_ionline($order_data);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, $inputData);
        return $result;
    }


    public function set_no_service_account_isdsl($order_data, $class = "nosvc")
    {

        $this->defaultResponseData;
        // set class changing here
    }

    public function set_no_service_account_ionline($order_data)
    {

        // suspend account
        $status = "BillingSuspended";
        $result = $this->suspend_account_ionline($order_data, $status);
        return $result;
    }


    /** [testing] skip this

     */
    public function restore_no_service_account($order_data, $account_class = "nosvc")
    {


        // $order_data  array('realm' = 'some-realm', 'account_username' => 'some account username')
        $order_data = $this->pre_process_order_data($order_data);

        $inputData = array(
            "order_data" => $order_data,
            "default_calass" => $account_class,

        );


        // TODO : check class inside mapper

        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];


        switch ($apiName) {
            case 'isdsl':
                $result = $this->restore_no_service_account_isdsl($order_data, $account_class);
                break;
            case 'ionline':
                $result = $this->restore_no_service_account_ionline($order_data);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, $inputData);
        return $result;
    }


    public function restore_no_service_account_isdsl($order_data, $class = "nosvc")
    {

        $this->defaultResponseData;
        // set class changing here
    }

    public function restore_no_service_account_ionline($order_data)
    {


        $result = $this->restore_account_ionline($order_data);
        return $result;
    }


    /**  [tested]
     *
     * @param $order_data
     * @return array
     */
    public function restore_account($order_data)
    {

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->restore_account_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->restore_account_ionline($order_data);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }
        $this->check_fail_api($result, array("order_data" => $order_data));
        return $result;
    }

    public function restore_account_isdsl($order_data)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');


        $response = $this->is_classes->restore_account_with_handler($order_data);
        return $response;
    }

    public function restore_account_ionline($order_data)
    {

        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');

        return $this->ionline_api_model->restore_account($order_data);
    }



    /** [tested]
     * @param $username
     * @return array
     */
    // TODO : set single format for account info
    public function get_user_info($order_data)
    {



        //  getAccountInfo_new($sess,$username)
        //  function getAccountInfo_full_new($sess,$username){
        $order_data = $this->pre_process_order_data($order_data);

        $api_data = $this->get_api_by_realm($order_data['realm']);
        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->get_user_info_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->get_user_info_ionline($order_data);
                break;
            default:
                $result = null;
        }

        return $result;
    }


    public function get_user_info_isdsl($order_data)
    {

        // check models
        // functions inside 'admin/is_classes' and 'user/is_classes' should be similar
        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');


        return $this->is_classes->get_account_full_info_with_handler($order_data);

        // TODO : configure unify interface
        /* Main fields


        [arrAccountInfo] => Array
        (
            [UserName] => test-vvv-check-3443@openweb.adsl
            [Password] => 12345
            [Comment] => test1 test2 (Client) (R99 - 1Mbps Home Uncapped ADSL) (DEBIT ORDER)
            [SystemComment] => Account Status: Unshaped
            [EmailAddress] => example@gmail.com
            [Class] => ow-1024-std
            [Status] => 1

         )
        */
    }


    public function get_user_info_ionline($order_data)
    {


        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');

        return $this->ionline_api_model->get_account_info($order_data);

        /*

        // get user info and assign original API response
        $user_info = $this->ionline_api_model->get_account_info($order_data);
        $user_info['original_api_response'] = $user_info['api_response'];

        // exit if API call failed
        if (!$user_info['result'])
            return $user_info;

        // parse order data and get class id
        $order_data = $this->parse_account_username_to_order_data_format($user_info['original_api_response']->account_name);
        $class_id = $user_info['original_api_response']->current_product->current_product_id;

        //! DEBUG
        //$class_id = 29;

        // get class mapper id
        $class_row = $this->get_rows_from_mapper(
            $row = array( "class_id_ionline" => $class_id),
            $fields = array( "class_id_ionline" ),
            true
        );

        // format unified info
        $parsed_user_data = array(

            "account_username" => $order_data["account_username"],
            "realm" => $order_data["realm"],
            "password" => $user_info['original_api_response']->password,
            "class_mapper_ids" => $class_row,
            "status" => "",   // TODO : decide about status format
            "comment" => $user_info['original_api_response']->account_comment,
        );

        $user_info["api_response"] = $parsed_user_data;
        return $user_info;
        */
    }



    // public function delete_account_new($sess, $strUserName){
    /**
     * @param $username
     * @return array
     */
    public function delete_user_from_realm($order_data)
    {

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->delete_user_from_realm_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->delete_user_from_realm_ionline($order_data);
                break;
            default:
                $result = null;
        }

        $this->check_fail_api($result, array("order_data" => $order_data));
        return $result;
    }

    public function delete_user_from_realm_isdsl($order_data)
    {

        // check models
        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        return $this->is_classes->delete_realm_account_with_handler($order_data);
    }

    public function delete_user_from_realm_ionline($order_data)
    {

        $date = date("Y-m-d"); // set current date
        return $this->cancel_account_ionline($order_data, $date);
    }




    /*
     *  Set new class to user via API
     *  Input format :
     *                  $order_data  array('realm' = 'some-realm', 'account_username' => 'some account username')
     *                  $new_class   string
     *
     *  Output format :
     *                  array(
     *                          'result'       => '', bool   (true/false)
     *                          'message'      => '', string
     *                          'user_message' => '', string
     *                          'api_response' => '', int    (api response code)
     *                        );

     */

    // $order_data['realm'],  $order_data['account_username']
    /** [tested]
     * @param $order_data
     * @param $new_class
     * @return array
     */
    public function set_class_to_user($order_data, $new_class)
    {

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];

        // get and process class via mapper
        $classMapperRow  = $this->process_class_from_mapper($new_class);
        $classValidation = $this->check_class_map_for_api($classMapperRow[$apiName], $apiName);

        $inputData = array('order_data' => $order_data, 'new_class' => $new_class);

        $this->check_fail_api($classValidation, $inputData);
        if (!$classValidation['result'])
            return $classValidation;

        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->set_class_to_user_isdsl($order_data, $classMapperRow[$apiName]['name']);
                break;
            case 'ionline':
                $result = $this->set_class_to_user_ionline($order_data, $classMapperRow[$apiName]['id']);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }
        $this->check_fail_api($result, $inputData);

        return $result;
    }


    public function set_class_to_user_isdsl($order_data, $new_class)
    {

        // check models
        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        return $this->is_classes->set_new_class_with_handler($order_data, $new_class);
    }

    public function set_class_to_user_ionline($order_data, $new_class_id)
    {

        if (!isset($this->is_classes))
            $this->load->model('ionline_api_model');

        $params = array(

            "next_product_id"           => $new_class_id,
            "next_contract_term_id"     => 1,
            "next_product_upgrade_date" => date("Y-m-d"),

        );


        return $this->ionline_api_model->change_class($order_data, $params);
    }

    public function get_all_accounts($apiName = 'ionline', $params = null)
    {


        switch ($apiName) {

            case 'isdsl':
            case 'ionline':
                $result = $this->get_all_accounts_isdsl($params);
                break;
        }
        return $result;
    }

    public function get_all_accounts_isdsl($params = null)
    {

        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');

        return $this->ionline_api_model->get_all_accounts($params);
    }




    public function get_api_by_realm($realm_name)
    {

        $this->db->select('network_api_id');
        $this->db->where('realm', $realm_name);
        $query = $this->db->get('realms');

        // check for unique
        $result = $query->result_array();
        if (empty($result) || (count($result) > 1))
            return array('network_api_id' => false, 'api_name' => false);

        // get API name from internal class field
        $result = $result[0];
        $result['api_name'] = $this->api_collection[$result['network_api_id']];

        return $result;
    }

    public function process_class_from_mapper($className)
    {

        // get class row from mapper
        $this->db->select();

        // for now mapper works only in one way : class_nam_ isdsl -> other APIs.
        // because there are several occurrences of ISDSL classes on one IONLINE class

        $this->db->where('class_name_isdsl', $className);
        $this->db->or_where('class_name_ionline', $className); // do not use !

        $query = $this->db->get('classes_map');
        $result = $query->result_array();
        if (empty($result))
            return false;

        $requiredFields = array(

            "class_id_isdsl", "class_name_isdsl", "class_id_ionline", "class_name_ionline"
        );
        $resultCorrectRow = null;
        // check all class occurrences from database
        foreach ($result as $row) {

            $stopFlag = true;
            // check all if row contains all required fields
            foreach ($requiredFields as $field) {
                if (empty($row[$field])) {
                    $stopFlag = false;
                    break;
                }
            }
            // we find correct row -> exit from loop
            if ($stopFlag) {
                $resultCorrectRow = $row;
                break;
            }
        }

        if (empty($resultCorrectRow))
            return false;

        $responseRow = array(
            'isdsl' => array(
                'id'   => $resultCorrectRow['class_id_isdsl'],
                'name' => $resultCorrectRow['class_name_isdsl'],
            ),
            'ionline' => array(
                'id'   => $resultCorrectRow['class_id_ionline'],
                'name' => $resultCorrectRow['class_name_ionline'],
            )
        );

        return $responseRow;
    }

    public function check_class_map_for_api($classRow, $apiName)
    {

        $defaultResponse = $this->defaultResponseData;
        if (empty($classRow['id']) || empty($classRow['name'])) {
            $defaultResponse['message'] = "Failure : Class doesn't exist for API " . $apiName;
            $defaultResponse['user_message'] = "Failure : Please contact admin to resolve it";
            return $defaultResponse;
        }
        $defaultResponse['result'] = true;
        $defaultResponse['message'] = $defaultResponse['user_message'] = "";

        return $defaultResponse;
    }

    public function apply_force_map_validation(
        $originalClass,
        $apiName,
        &$classMapRow,
        &$classValidation
    ) {

        // check corresponding flags and variables, 
        // if some statement is FALSE -> left $classMapRow and $classValidation unchanged
        if (
            !isset($this->forceClassMapValidation[$apiName])
            || ($this->forceClassMapValidation[$apiName] != true)
        )
            return false;

        // if original class is int -> set class_id and return
        // TODO: perhaps, we should make additional SQL request to get id/name which left
        if (is_numeric($originalClass)) {
            $classMapRow[$apiName]["id"] = $originalClass;
        } else {
            $classMapRow[$apiName]["name"] = $originalClass;
        }

        $classValidation["result"] = true;
        $classValidation['message'] = $classValidation['user_message'] = "";

        return true;
    }

    // -----------------------------------------------------------------------
    // ------------ Get Session Info -----------------------------------------
    // -----------------------------------------------------------------------
    // -----------------------------------------------------------------------

    /** [testing]
     *
     * @param $order_data
     * @param null $options
     * @return mixed
     */
    public function get_session_info($order_data, $options = null)
    {

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->get_session_info_isdsl($order_data, $options);
                break;
            case 'ionline':
                $result = $this->get_session_info_ionline($order_data, $options);
                break;
        }
        return $result;
    }

    /**
     *
     * @param $order_data
     * @param null $options
     * @return mixed
     */
    public function get_session_info_isdsl($order_data, $options = null)
    {


        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        $isdsl_response = $this->is_classes->get_current_session_info_with_handler($order_data);
        $response = $this->map_session_info_isdsl($isdsl_response);
        return $response;
    }

    public function map_session_info_isdsl($response)
    {

        // . . .
        return $response;
    }


    public function get_session_info_ionline($order_data, $options = null)
    {

        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');

        $result = $this->ionline_api_model->get_user_sessions($order_data, $options);
        $result["original_api_response"] = $result["api_response"];
        return $result;
    }

    /** [testing]
     * @param $order_data
     * @param $options
     * @return array
     *
     */
    public function get_activity_info($order_data, $options)
    {

        // options example :  array("activity_type" => "day", "period" => "2016-02-24");

        // TODO : add year activity to ionline (period)
        $order_data = $this->pre_process_order_data($order_data);
        // possible options
        $api_data = $this->get_api_by_realm($order_data['realm']);
        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->get_activity_info_isdsl($order_data, $options);
                break;
            case 'ionline':
                $result = $this->get_activity_info_ionline($order_data, $options);
                break;
                // if no realm like in LTE-A, but must be remaked
            default:
                $result = $this->get_activity_info_isdsl($order_data, $options);
                break;
        }

        return $result;
    }

    public function get_activity_info_day_month_yaer($order_data)
    {


        // get day usage
        $day_usage = $this->network_api_handler_model->get_activity_info(
            $order_data,
            array(
                "period" => date("Y-m-d"),
                "activity_type" => "day",
            )
        );

        // get month usage
        $month_usage = $this->network_api_handler_model->get_activity_info(
            $order_data,
            array(
                "period" => date("Y-m"),
                "activity_type" => "month",
            )
        );

        // get year usage 
        $year_usage = $this->network_api_handler_model->get_activity_info(
            $order_data,
            array(
                "period" => date("Y"),
                "activity_type" => "year",
            )
        );
        $response = $this->usageResponseFormat;
        $response["day_usage"]   = $day_usage["api_response"];
        $response["month_usage"] = $month_usage["api_response"];
        $response["year_usage"]  = $year_usage["api_response"];

        /*
            var_dump($day_usage);
            echo "<hr/>";
            var_dump($month_usage);
            echo "<hr/>";
            var_dump($year_usage);
            echo "<hr/>";
            var_dump($response);
        */

        return $response;
    }


    public function get_activity_info_isdsl($order_data, $options = null)
    {


        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        $isdsl_response = $this->is_classes->get_activity_info_with_handler($order_data, $options);
        //$usageResponse = $this->usageResponseFormat;
        $trafficUsage = 0;

        if ($isdsl_response["result"]) {

            //  calculate total amount of traffic 
            if (!empty($isdsl_response["api_response"]["arrUsageStats"]))
                foreach ($isdsl_response["api_response"]["arrUsageStats"] as $row) {
                    $trafficUsage += round($row["TotalUsageBytes"] / 1000000, 2);
                }
        }

        $isdsl_response["api_response"] = $trafficUsage;
        return $isdsl_response;
    }


    public function get_activity_info_ionline($order_data, $options = null)
    {

        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');


        // TODO : debug this function or replace it to get_account_info (ionline)
        // $result = $this->ionline_api_model->get_user_activity($order_data, $options);

        $result = $this->defaultResponseData;
        // Traffic Usage mapper goes here

        // MOCK 
        // set 0 mb usage by default
        $result["api_response"] = 0;
        return $result;
    }

    // date = (Y-m-d)
    public function set_pending_update($order_data, $class, $date)
    {

        // pre process order data  and get API network
        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];


        // get and process class via mapper
        $classMapperRow = $this->process_class_from_mapper($class);
        $classValidation = $this->check_class_map_for_api($classMapperRow[$apiName], $apiName);

        $inputData = array(
            "order_data" => $order_data,
            "class"      => $class,
            "date"       => $date,
        );

        $this->check_fail_api($classValidation, $inputData);
        if (!$classValidation['result'])
            return $classValidation;

        // TODO : validate date
        // - - -  - -  - - - - -

        switch ($apiName) {
            case 'isdsl':
                $result = $this->set_pending_update_isdsl($order_data, $classMapperRow[$apiName]['name'], $date);
                break;
            case 'ionline':
                $result = $this->set_pending_update_ionline($order_data, $classMapperRow[$apiName]['id'], $date);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, $inputData);
        return $result;
    }


    public function set_pending_update_isdsl($order_data, $class, $date)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        $first_day_of_next_month = date("Y-m-1", strtotime("+ 1 month"));
        $result = null;
        switch ($date) {

            case $first_day_of_next_month:
            default:
                $result = $this->is_classes->set_pending_update_with_handler($order_data, $class);
                break;
                // TODO : set separate function for default case, where user can choose the date of pending update
        }

        return $result;
    }

    // date = (Y-m-d)
    public function set_pending_update_ionline($order_data, $class, $date)
    {

        if (!isset($this->ionline_api_model))
            $this->load->model('ionline_api_model');

        $params = array(

            "next_product_id"           => $class,
            "next_contract_term_id"     => 1,
            "next_product_upgrade_date" => $date,

        );

        return $this->ionline_api_model->change_class($order_data, $params);
    }

    // set_pending_update_new($sess, $acc_realm_user, 'nosvc')


    public function get_pending_update($order_data)
    {

        /*
            isdsl pending update info :
               [0] => Array
                    (
                            [Username] => isp4567@dsl512.isdsl.net
                            [ProductTypeID] => hc11
                            [QueueDate] => 2009-09-01 00:00:00
                    )
        --------------------------------------------
            ionline pending update info :
                    [next_product] => stdClass Object
                            (
                                [next_product_id] => 23
                                [next_product_name] => Business Uncapped Premium 4MB
                                [next_contract_term_id] => 1
                                [next_contract_term_duration] => 1
                                [next_product_upgrade_date] => 2017-01-01 00:00:00
                            )
        ---------------------------------------------
          pending update result:

                    class_name,
                    date,   // Y-m-d
                    class_id (for ISDSL get this separately from mapper)

          private $defaultResponseData = array(
                            "result"        => false,
                            "message"       => "Failure : Default message",
                            "user_message"  => "Failure : Please contact the admin to resolve it",
                            "api_response"  => null,
                        );

         */

        // pre process order data  and get API network
        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];



        switch ($apiName) {
            case 'isdsl':
                $result = $this->get_pending_update_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->get_pending_update_ionline($order_data);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, array("order_data" => $order_data));
        return $result;
    }


    public function get_pending_update_isdsl($order_data)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        // TODO : format handler in a proper way
        $result = $this->is_classes->get_pending_update_with_handler($order_data);
        return $this->defaultResponseData;
    }

    // date = (Y-m-d)
    public function get_pending_update_ionline($order_data)
    {


        $userInfo = $this->get_user_info_ionline($order_data);
        // get info about pending updates

        if ($userInfo['result'] != true)
            return $userInfo;

        $defaultErrorMessage = $this->defaultGetPendingUpdateError;


        // get info about next product and check if it empty
        if (!isset($userInfo["original_api_response"]->next_product) || empty($userInfo["original_api_response"]->next_product)) {
            $userInfo["result"] = false;
            $userInfo["message"] = $userInfo["user_message"] = $defaultErrorMessage;
            return $userInfo;
        }



        // assign data about next product
        $nextProductObject = $userInfo["original_api_response"]->next_product;

        /*
            [next_product_id] => 23
            [next_product_name] => Business Uncapped Premium 4MB
            [next_contract_term_id] => 1
            [next_contract_term_duration] => 1
            [next_product_upgrade_date] => 2017-01-01 00:00:00

         */

        $responseData = array(
            "class_id"   => $nextProductObject->next_product_id,
            "class_name" =>  "",
            "date"       => $nextProductObject->next_product_upgrade_date,
        );

        $userInfo["additional_data"]["pending_product"] = $responseData;


        // get class name by id via mapper
        $mapperRow = $this->get_rows_from_mapper(
            $row = array("class_id_ionline" => $responseData["class_id"]),
            $fields = array("class_id_ionline")
        );


        // check class name
        if (!isset($mapperRow[0]["class_name_ionline"]) || empty($mapperRow)) {
            $userInfo["result"] = false;
            $userInfo["user_message"] = $defaultErrorMessage;
            $userInfo["message"] = "Class mapper doesn't have any info about this IONLINE CLASS ID, or ID is invalid";
            return $userInfo;
        }

        $userInfo["additional_data"]["pending_product"] = $responseData;
        $responseData['class_name'] = $mapperRow[0]["class_name_ionline"];

        // rewrite API response
        $userInfo['api_response'] = $responseData;
        return $userInfo;
    }


    public function get_classes($order_data)
    {


        $inputData = array(
            "order_data" => $order_data,
        );

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];

        switch ($apiName) {
            case 'isdsl':
                $result = $this->get_classes_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->get_classes_ionline($order_data);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, $inputData);
        var_dump($result);
        die();
        return $result;
    }



    public function get_classes_isdsl($order_data)
    {


        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        // TODO : format handler in a proper way
        $result = $this->is_classes->get_classes_with_handler($order_data);
        return $result;
    }

    public function get_classes_ionline($order_data)
    {

        // TODO: check ionline doc later
        return $this->$defaultResponseData;
    }



    public function update_classes($order_data)
    {


        $inputData = array(
            "order_data" => $order_data,
        );

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];

        $this->process_rewrite_api_name($apiName);

        switch ($apiName) {
            case 'isdsl':
                $result = $this->update_classes_isdsl($order_data);
                break;
            case 'ionline':
                $result = $this->update_classes_ionline($order_data);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, $inputData);
        return $result;
    }



    public function update_classes_isdsl($order_data)
    {


        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        $result = $this->is_classes->update_classes_with_handler($order_data);
        return $result;
    }

    public function update_classes_ionline($order_data)
    {

        //die('test');
        // TODO: check ionline doc later
        return $this->$defaultResponseData;
    }




    // ----------------------------------------------------------------
    // ----------------------------------------------------------------
    // ---------------- pending cancellation --------------------------
    // ----------------------------------------------------------------
    // ----------------------------------------------------------------


    //public function set_pending_update($order_data){



    //}

    // set_pending_update_new($sess, $acc_realm_user, 'nosvc')








    // ----------------------------------------------------------------
    // ----------------------------------------------------------------
    // ---------------- update account password -----------------------
    // ----------------------------------------------------------------
    // ----------------------------------------------------------------



    /** [tested]

     */
    public function change_account_password($order_data, $new_password)
    {

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];


        switch ($apiName) {
            case 'isdsl':
                $result = $this->change_account_password_isdsl($order_data, $new_password);
                break;
            case 'ionline':
                $result = $this->change_account_password_ionline($order_data, $new_password);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }

        $this->check_fail_api($result, array("order_data" => $order_data, "new_password" => $new_password));
        return $result;
    }

    public function change_account_password_isdsl($order_data, $new_password)
    {

        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');


        return $this->is_classes->change_account_password_with_handler($order_data, $new_password);
    }

    public function change_account_password_ionline($order_data, $new_password)
    {

        if (!isset($this->is_classes))
            $this->load->model('ionline_api_model');

        return $this->ionline_api_model->update_account_password($order_data, array('password' => $new_password));
    }



    /** [tested] (doesn't work!)

     */
    // $order_data['realm'],  $order_data['account_username']s
    public function change_account_comment($order_data, $comment)
    {

        $order_data = $this->pre_process_order_data($order_data);
        $api_data = $this->get_api_by_realm($order_data['realm']);
        $apiName = $api_data['api_name'];


        switch ($api_data['api_name']) {

            case 'isdsl':
                $result = $this->change_account_comment_isdsl($order_data, $comment);
                break;
            case 'ionline':
                $result = $this->change_account_comment_ionline($order_data, $comment);
                break;
            default:
                $result = $this->defaultResponseData;
                $result['message'] = "Unsupported API name ('default' case)";
                break;
        }
        $this->check_fail_api($result, array("comment" => $comment));
        return $result;
    }


    public function change_account_comment_isdsl($order_data, $comment)
    {

        // check models
        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        return $this->is_classes->set_account_comment_with_handler($order_data, $comment);
    }

    public function change_account_comment_ionline($order_data, $comment)
    {

        if (!isset($this->is_classes))
            $this->load->model('ionline_api_model');

        $params = array("account_comment" => $comment);
        return $this->ionline_api_model->update_account_info($order_data, $params);
    }


    public function parse_account_username_to_order_data_format($account_username_with_realm)
    {

        $lm = explode('@', $account_username_with_realm);
        if (empty($lm) || (count($lm) > 2))
            return false;

        $order_data = array("account_username" => $lm[0], "realm" => $lm[1]);
        return $order_data;
    }



    // ------------------------------------------------------------------


    public function check_fail_api($api_result, $some_data = null)
    {

        if ($api_result['result'] != false)
            return true;

        $date = date("Y-m-d H:i:s");
        $head = "\n\n\n ----- " . $date . " ----- ";
        $description = "\n network api fail \n ---------------";
        //$response_dump = "\n : Api result" . print_r($api_result, true);

        $additional_data = $api_result;
        $additional_data_text = "\n\n Full data : ";
        foreach ($additional_data as $key => $row) {
            $additional_data_text .= "\n " . $key . " : " . print_r($row, true);
        }

        $some_data_text = "";
        if (!empty($some_data)) {
            $some_data_text = "\n\n Additional data : ";
            foreach ($some_data as $key => $row) {
                $some_data_text .= "\n " . $key . " : " . print_r($row, true);
            }
        }

        $final_log = $head;
        $final_log .= $description;
        //$final_log .= $response_dump;
        $final_log .= $additional_data_text;
        $final_log .= $some_data_text;

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


    public function write_port_reset_log($str)
    {


        $log_file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "network-api-log.txt";

        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, "\n " . $str);
        fclose($log_handle);
    }


    public function pre_process_order_data($order_data)
    {

        // TODO : think about order validation
        $order_data['account_username'] = trim($order_data['account_username']);
        $order_data['realm'] = trim($order_data['realm']);

        return $order_data;
    }



    // -- ----------------------------------------------------------------------------
    public function get_rows_from_mapper($row, $fields, $only_map_id = false,  $tableName = "classes_map")
    {

        $this->db->select();
        if (!empty($fields))
            foreach ($fields as $fieldKey) {
                $this->db->where($fieldKey, $row[$fieldKey]);
            }
        $query = $this->db->get($tableName);
        //$query = $this->db->get("classes_map_home");
        $result = $query->result_array();

        if (!empty($result) && ($only_map_id == true)) {
            $backup_result = $result;
            $result = array();
            foreach ($backup_result as $row) {
                $result[] = $row['map_id'];
            }
        }

        return $result;
    }

    // we need function to check if some class 'ow-100' (eg.) is exist in the map array
    // select by fields  ('ionline_class_name') and save to array.
    // compare to arrays and check if they have similar IDs


    // $get_rows_from_mapper_fields in the next format :
    // $row, $fields, $only_map_id = false (ignore),  $tableName = "classes_map"(ignore)
    public function compare_different_types_of_classes($array_of_map_ids, $mapper_fields)
    {


        //quick validation
        if (empty($array_of_map_ids) || empty($mapper_fields))
            return false;

        if (!isset($mapper_fields["row"]) || empty($mapper_fields["row"]))
            return false;

        if (!isset($mapper_fields["fields"]) || empty($mapper_fields["fields"]))
            return false;


        // get rows by fields
        $new_rows = $this->get_rows_from_mapper($mapper_fields["row"], $mapper_fields["fields"], true);
        $result = $this->compare_class_map_ids($array_of_map_ids, $new_rows);

        return $result;
    }

    public function compare_class_map_ids($map_ids_1, $map_ids_2)
    {

        $result = false;
        $intersect_array = array_intersect($map_ids_1, $map_ids_2);

        if (count($intersect_array) > 0)
            $result = true;

        return $result;
    }


    public function insert_row_to_mapper($row, $tableName = "classes_map")
    {

        $insertResult = $this->db->insert($tableName, $row);
        return $insertResult;
    }

    public function update_row_from_mapper($row, $searchFields, $tableName = "classes_map")
    {


        foreach ($searchFields as $field => $value) {
            $this->db->where($field, $value);
        }

        $updateResult = $this->db->update($tableName, $row);
        return $updateResult;
    }


    private function process_rewrite_api_name(&$apiName)
    {

        if (!empty($this->rewrite_api))
            $apiName = $this->rewrite_api;

        return;
    }


    public function debug_generate_valid_mapper()
    {


        $classTables = array('is_classes', 'classes_map');
        $mapperScheme = array(

            "table_id" => "class_id_isdsl",
            "realm"    => "realm_name_isdsl",
            "id"       => "class_name_isdsl", // isdsl class name

        );

        // get all ISDSL classes and fill class_mapper
        $this->debug_fill_class_map_from_is_classes_table($classTables, $mapperScheme);

        // get all rows from old mapper
        //- get Ionline row for each row
        //- generate update data with filelds
        //$this->debug_fill_class_map_from_old_mapper();


    }



    /**
     * This function copy all classes from 'is_classes' table to 'class_map' table
     *
     *  e.g. :
     *  $classTable = array ("is_classes", "classes_map");
     *  $mapperScheme = array("table_id" => "class_id_isdsl", ...)          "key" => "key"
     *
     */
    private function debug_fill_class_map_from_is_classes_table($classTables, $mapperScheme)
    {

        if (empty($classTables[0]) || empty($classTables[1]))
            return false;

        if (empty($mapperScheme))
            return false;

        // --------------------------------------------------

        // get all is_classes rows
        $this->db->select();
        $query1 = $this->db->get($classTables[0]);
        $is_classes_result = $query1->result_array();
        $classes = $is_classes_result;

        // generate valid row collection  for class_map according to mapper
        $mapperArray = array();
        foreach ($classes as $class) {

            $row = array();
            // map 'is_classes' data to 'classes_map'
            foreach ($mapperScheme as $key1 => $key2)
                $row[$key2] = $class[$key1];

            $mapperArray[] = $row;
            unset($row);
        }



        // check if this row already exists in mapper
        foreach ($mapperArray as $mapRow) {

            $classMapRow =  $this->get_rows_from_mapper($mapRow, array("class_name_isdsl", "realm_name_isdsl"));
            if (empty($classMapRow)) {
                // insert row to mapper
                $insertResult = $this->insert_row_to_mapper($mapRow);
            }
        }

        return true;
    }

    public function debug_fill_class_map_from_old_mapper()
    {

        // get all rows from old mapper
        $allRowsFromOldMapper = $this->get_rows_from_mapper(null, null, false, "classes_map_home");


        /*
            [map_id] => 1
            [class_id_isdsl] => 1
            [class_name_isdsl] => nosvc
            [realm_name_isdsl] => mynetwork.co.za
            [class_id_ionline] => 1
            [class_name_ionline] => CAPS0050
        )
         */


        // update current mapper with new data
        if (!empty($allRowsFromOldMapper))
            foreach ($allRowsFromOldMapper as $row) {
                $updateRow = array(

                    "class_id_ionline"   => $row["class_id_ionline"],
                    "class_name_ionline" => $row["class_name_ionline"],
                );
                $searchArray =  array("class_name_isdsl" => $row["class_name_isdsl"]);

                if (!empty($row["class_name_ionline"]))
                    $updateResult = $this->update_row_from_mapper($updateRow, $searchArray);
            }
        //- generate update data with fields
        return true;
    }

    //get usage from IS API for LTE-A users
    function get_lte_usage_all($username, $realm)
    {

        $day = $this->get_lte_usage($username, 'd', $realm);
        $all_data['day_usage'] = round($this->lte_day_usage_result($day) / pow(1024, 2), 2, PHP_ROUND_HALF_UP);
        $all_data['sess'] = $this->get_lte_usage($username, 's', $realm);
        $year = $this->get_lte_usage($username, 'us', $realm);
        if (is_array($year) && is_object($year[0]))
            $all_data['year_usage'] = round($year[0]->MainPackageUsed / pow(1024, 2), 2, PHP_ROUND_HALF_UP);

        $month = $this->get_lte_usage($username, 'm', $realm);
        if (is_array($month) && is_object($month[0]))
            $all_data['month_usage'] = round($month[0]->Total / pow(1024, 2), 2, PHP_ROUND_HALF_UP);

        $all_data['error'] = $this->check_usage_data($all_data);
        return $all_data;
    }

    //NEW
    function get_lte_day_usage($username, $realm, $date)
    {

        $day = $this->get_lte_usage($username, 'd', $realm, $date);
        $rounded = round($this->lte_day_usage_result($day) / pow(1024, 2), 2, PHP_ROUND_HALF_UP);
        return $rounded;
    }

    function lte_day_usage_result($data)
    {
        $res = 0;
        foreach ($data as $ses) {
            if (isset($ses) && is_object($ses)) {
                $res += $ses->charged_quantity;
            } else {
                $res = null;
            }
        }
        return $res;
    }

    function get_lte_usage($username, $period_code, $realm, $date = null)
    {

        $this->load->model('user/is_classes');
        $this->load->model('admin/realm_model');

        $period_type = [
            's' => 'CurrentSessions',
            'm' => 'MonthUsage',
            'ms' => 'MonthSummary',
            'd' => 'DayUsage',
            'us' => 'UsageSummary'
        ];
        $period = '';

        foreach ($period_type as $code => $val) {
            if ($code == $period_code) {
                $period = $val;
            }
        }

        if (empty($date))
            $date = json_encode(["Year" => date('Y'), "Month" => date('m'), "Day" => date('d')]);

        $user = json_encode([$username . "@" . $realm]);

        //API Call
        $realm_data = $this->realm_model->get_realm_data_by_name($realm . ".co.za");
        $connect_result = $this->is_classes->is_connect_new_with_handler($realm_data);

        if (!$connect_result['result'])
            return $connect_result;

        $resp = $this->is_classes->getAccountUsage($connect_result['session'], $period, $date, $user);
        $res = json_decode($resp["strResults"]);

        return $res;
    }

    function write_realm($data)
    {
        $file = fopen("realmData.txt", "w");
        fwrite($file, json_encode($data));
        fclose($file);
    }

    function check_usage_data($all_data)
    {

        foreach ($all_data as $key => &$data) {

            if ($data["intReturnCode"] != 1) {
                //$data['session_error_message']
                $data = $data["strMessage"];
            }
        }

        return $all_data;
    }

    function provisionLTEAccount_Old($data)
    {
        //updated function
        $data = array(
            'Password'     => $data["password"],
            'Email'     => $data["email"],
            'UserName'  => $data["username"],
            'ClassID'     => $data["class_id"],
            'ICCID'     => $data["sim"],
            "RICA" => array(
                "idNumber" => $data["idnumber"],
                "AddressType" => $data["addressType"],
                "ContactName" => $data["contactName"],
                "PostCode" => $data["postcode"],
                "TelCell" => $data["tellcell"],
                "Street" => $data["street"],
                "Suburb" => $data["suburb"],
                "City" => $data["city"],
                "AddressComplex" => $data["addressComplex"]
            ),
            "AddressLocation" => array(
                "Latitude" => $data["latitude"],
                "Longitude" => $data["longitude"]
            )
        );
        $payload = json_encode($data);
        $host = 'https://www.isdsl.net/api/rest/lte/provisionAccount.php';
        $username = 'api@openwebmobile.co.za';
        $password = 'oC3JRkyQ7q==123-';
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'cache-control: no-cache'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    function provisionLTEAccount($data)
    {
        //updated function
        $data = array(
            'Password'     => $data["password"],
            'Email'     => $data["email"],
            'UserName'  => $data["username"],
            'ClassID'     => $data["class_id"],
            'ICCID'     => $data["sim"],
            'DeviceSerialNo'     => $data["device_serial_no"],
            "RICA" => array(
                "idNumber" => $data["idnumber"],
                "ContactName" => $data["contact_name"],
                "ContactSurname" => $data["contact_surname"],
                "TelCell" => $data["tellcell"],
                "AddressType" => $data["addressType"],
                "BuildingType" => $data["BuildingType"],
                "StreetNumber" => $data["StreetNumber"],
                "Street" => $data["street"],
                "Building" => $data["Building"],
                "ComplexName" => $data["ComplexName"],
                "UnitNumber" => $data["UnitNumber"],
                "BuildingName" => $data["BuildingName"],
                "BuildingFloor" => $data["BuildingFloor"],
                "Suburb" => $data["suburb"],
                "City" => $data["city"], 
                "PostCode" => $data["postcode"],
                 
            ),
            "AddressLocation" => array(
                "Latitude" => $data["latitude"],
                "Longitude" => $data["longitude"]
            )
        );
       
        $payload = json_encode($data);
        
        $host = 'https://qa.mwebaws.co.za/reseller/rest/lte/provisionAccount.php';
        $username = 'keoma_wright_5454';
        $password = 'DvzrpXq2';
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'cache-control: no-cache'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    function getClassesByRealm($realm)
    {
        if (!isset($this->is_classes))
            $this->load->model('admin/is_classes');

        return $this->is_classes->getClassesByRealm($realm);
    }

    function getLTEUsernames($realm)
    {
        $this->load->model('admin/is_classes');
        $this->load->model('admin/realm_model');

        //API Call
        $realm_data = $this->realm_model->get_realm_data_by_name($realm);
        $connect_result = $this->is_classes->is_connect_new_with_handler($realm_data);

        if (!$connect_result['result'])
            return $connect_result;

        $resp = $this->is_classes->get_list_of_accounts($connect_result['session']);

        if (isset($resp['arrUserNames']))
            return $resp['arrUserNames'];

        return $resp;
    }

    function simSwapRequest($data)
    {
        $this->load->model('admin/is_classes');

        $requestData = [
            "Username" => $data['username'],
            "Existing MSISDN" => $data['msisdn'],
            "New ICCID" => $data['iccid'],
            "RICA" => [
                "Building" => $data['building'],
                "Street" => $data['street'],
                "Suburb" => $data['suburb'],
                "City" => $data['city'],
                "PostCode" => $data['postcode']
            ],
            "AddressLocation" => [
                "Latitude" => $data['Latitude'],
                "Longitude" => $data['Longitude']
            ]
        ];

        $response = $this->is_classes->ownSimSwap($requestData);

        return $response;
    }
}
