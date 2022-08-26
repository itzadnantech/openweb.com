<?php

class Ionline_api_model extends CI_Model {

    private $key = '';
    private $apiVersion = '1';

   // private $apiUrl    = 'http://api.fls.ionline.co.za/v1/api/';
   // private $apiUrlGet = 'http://api.fls.ionline.co.za/v1/';
   // HTTP private $apiUrl    = 'http://api.fls.ionline.co.za/api/v1/';
    private $apiUrl    = 'https://api.fls.ionline.co.za/api/v1/';


    private $username = "api@openweb.co.za";
    //private $password = "0p3nWE8!2016";
    private $password = "pKyvWen6";


    //  GET http://api.fls.ionline.co.za/v1/account/fred@idsl.ionline.co.za/activity/date?date=2016-09-01
    //  PUT /api/v1/account/test1@dsl.ionline.co.za/upgrade
    // POST /api/v1/accounts

    private $apiHost = 'api.fls.ionline.co.za';

    private $accountApiPrefix = "account/";
    private $allAccountsApiPrefix = "accounts/";
    private $allRealmsApiPrefix = 'realms/';
    private $productApiPrefix = 'product/';
    private $allProductsApiPrefix = 'products/';

    private $topupSuffix = '/topup/';
    private $activitySuffix = '/activity/';
    private $sessionSuffix = '/session/';

    // TODO : Fail codes


    private $defaultResponseData = array(
        "result"        => false,
        "message"       => "Failure : Please contact the admin to resolve it",
        "user_message"  => "Failure : Please contact the admin to resolve it",
        "api_response"  => null,
    );


    private function send_request($requestType, $requestUrl = '', $data = null){

        // set default response as FAIL
        $response = $this->defaultResponseData;
        if (!in_array($requestType, array('PUT', 'POST', 'GET'))){
            $response['message'] = 'Wrong request type';
            return $response;
        }


        // init curl
        $baseUrl = $this->apiUrl;
        $curlUrl = $baseUrl . $requestUrl;
        //$curlUrl = urlencode($baseUrl . $requestUrl);

        //echo "<hr/>" . $curlUrl . "<hr/>";

        $ch = curl_init($curlUrl);

        // prepare data for request
        $jsonData = json_encode($data);
        $sessionHash = base64_encode($this->username . ":" . $this->password);

        // set CURL opts depends on request type
        switch ($requestType){
            case 'POST' :
                          curl_setopt($ch, CURLOPT_POST, true);
                          curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                          break;
            case 'PUT' :
                          //curl_setopt($ch, CURLOPT_PUT, true);
                          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                          curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                          break;
            case 'GET' :  break;
        }


        curl_setopt($ch, CURLOPT_HTTPHEADER,
                         array(
                                 'Host:' . $this->apiHost,
                                 'Accept:application/json',
                                 'Content-Type:application/json',
                                 'Authorization:Basic ' . $sessionHash,
                         )
                   );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        // For HTTPS protocol
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

        $resp = curl_exec($ch);
        $rawResponse = $resp;

        // processes possible curl errors
        $curl_error = '';
        if($resp === false) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = 'request error : ' . curl_error($ch) . " ( " . $http_code . " )";
        } else {
            $resp = json_decode($resp);
        }
        curl_close($ch);

        // format Object response from API
        if (is_object($resp))
            $resp = (array)$resp;

        $response["api_response"] = $resp;
        $response["user_message"] = " - ";  // add custom message for users
        if (!empty($curl_error)){
            $response['message'] = $curl_error;
            $response['additional_data']['request_url'] = $requestUrl;
            $response['additional_data']['raw_response'] = $rawResponse;
            return $response;
        }

        $response["result"]  = true;
        $response['api_response'] = $resp;
        // set 'Success' message as default
        $response["message"] = $response["user_message"] = "Request was successfully processed";
        $response['additional_data']['request_url'] = $requestUrl;
        $response['additional_data']['raw_response'] = $rawResponse;


        return $response;







        /*
            Get example :

            http://api.fls.ionline.co.za/v1/account/fred@idsl.ionline.co.za/activity/date?date=2016-09-01

            Put example :

            PUT /api/v1/account/test1@dsl.ionline.co.za/upgrade
            Host: api.fls.ionline.co.za
            Authorization: Basic j1jk21kj2g1jgkjg12
            Content-Type: application/json
            Accept: application/json
            BODY
            { "next_product_id" :31,
            "next_contract_term_id":1,
            "next_product_upgrade_date":"2016-10-10"
            }


            Post example :

            POST /api/v1/accounts
            Host: api.fls.ionline.co.za
            Authorization: Basic xyz
            Content-Type: application/json
            Accept: application/json
            BODY
            {"account_name":"me@test.com",
            "password":"123123123",
            "realm_id":1,
            "current_product_id":1,
            "current_contract_term_id":8,
            "account_email":"me@test.com",
            "account_customer_code":"123",
            "current_product_start_date": "2016-09-13",
            "send_email":1
            }


            $curl = curl_init($url . "/Contacts/{$recordId}");
            $data = array(
              'first_name' => 'John',
              );
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json',"OAuth-Token: $token"));
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

            // Make the REST call, returning the result
            $response = curl_exec($curl);
            if (!$response) {
                die("Connection Failure.n");
            }
         */


    }

    public function api_response_handler($resp, $checkEmptyResponse = true){

        /*
          private $defaultResponseData = array(
                "result"        => false,
                "message"       => "Failure : Default message",
                "user_message"  => "Failure : Please contact the admin to resolve it",
                "api_response"  => null,
          );
         */



        // curl error or connection issues . Default message were set at the "Request section"
        if ($resp['result'] === false)
            return $resp;


        // check if response is NULL
        if (empty($resp['api_response']) && $checkEmptyResponse){
            $resp["result"] = false;
            $resp["message"] = 'Response is empty';
            $resp["user_message"] = $this->defaultResponseData["user_message"];
            return $resp;
        }

        // TODO : process validation response and notice user

        // check API 'Error' status
        if ($resp['api_response']['status'] == 'error'){

            $resultErrorDescription = $resp['api_response']['result'];
            if (!is_string($resultErrorDescription))
                $resultErrorDescription = json_encode($resultErrorDescription);

            $resultErrorDescription = "(" . $resp['api_response']['type'] . ") " . $resultErrorDescription;
            $resp['message'] = $resultErrorDescription;
            $resp['result'] = false;
            $resp["user_message"] = $this->defaultResponseData["user_message"];

            return $resp;
        }

        $resp['api_response'] = $resp['api_response']['result'];
        return $resp;


        /*
          Response examples :

          { "status": string, "type": string "result": string" }

          E.g Status 200 OK
          { "status": "success", "type": "update", "result": "Update successful" }

          E.g. Status 401 Unauthorized
          { "status": "error", "type": "configuration", "result": "Invalid HTTP Headers." }

          E.g. Status 401 Unauthorized
          { "status": "error", "type": "validation",
              "result": {
                  "status": [
                  "The selected status is invalid."
                      ]
                  }
          }

          E.g. Status 404 Not Found
          { "status": "error", "type": "system", "result": "Resource Not Found" }

          E.g. Success
          { "status" : "success", "type" : "read", "result" : Array() }

         */

    }



    public function get_account_info($order_data){

        $username = $order_data['account_username'] . "@" . $order_data['realm'];
        // TODO : quick username validation

        // generate request URL for account info
        $requestUrl = $this->accountApiPrefix . $username;
        $response = $this->api_response_handler($this->send_request('GET', $requestUrl, null));

        if (is_array($response['api_response']) && (count($response['api_response']) == 1 ))
            $response['api_response'] = $response['api_response'][0];


        return $response;

        /*

        Response example :
        (without response handler,
          handler should replace $resp['api_response']['result'] to $resp['api_response'] ) :

        Array
            ( [result] => 1, [message] => Request was successfully processed,
              [user_message] =>  - ,
              [api_response] => stdClass Object
                    (
                        [status] => success, [type] => read
                        [result] => Array
                            (
                                [0] => stdClass Object
                                    (
                                        [account_name] => test67899@test.com
                                        [password] => 123123123
                                        [realm_id] => 2
                                        [realm_name] => openweb.adsl
                                        [status] => Active
                                        [account_cap_status] =>
                                        [shape_state] =>
                                        [usage] => stdClass Object
                                            (
                                                [quota_full_mb] => 0
                                                [quota_product_mb] => 51200
                                                [quota_rollover_mb] => 0
                                                [monthly_billed_total_mb] => 0.00
                                                [monthly_unbilled_total_mb] => 0.00
                                                [rolling_billed_usage] => 0.00
                                                [rolling_unbilled_usage] => 0.00
                                                [day_total_mb] => 0.00
                                                [previous_day_total_mb] => 0.00
                                                [quota_percent] => 0
                                            )

                                        [current_product] => stdClass Object
                                            (
                                                [current_product_id] => 1
                                                [current_product_name] => Capped Standard 50GB
                                                [current_product_solution] => Capped
                                                [current_contract_term_id] => 1
                                                [current_contract_term_duration] => 1
                                                [current_product_start_date] => 2016-10-19 00:00:00
                                            )

                                        [next_product] => stdClass Object
                                            (
                                                [next_product_id] => 0
                                                [next_product_name] =>
                                                [next_contract_term_id] => 0
                                                [next_contract_term_duration] =>
                                                [next_product_upgrade_date] =>
                                            )

                                        [account_comment] =>
                                        [account_customer_code] =>
                                        [account_email] => baf4mail@gmail.com
                                        [send_email] => 0
                                        [thresholds] => stdClass Object
                                            (
                                                [threshold_1] => 0
                                                [threshold_2] => 0
                                                [threshold_3] => 0
                                                [threshold_4] => 0
                                                [threshold_5] => 0
                                            )

                                        [cancel_date] =>
                                        [created_at] => 2016-10-19 19:43:23
                                        [updated_at] => 2016-10-19 19:43:23
                                    )
                            )
                    )
            )
         */
    }

    public function create_new_account($order_data, $class, $pass, $comment, $email){

        /* RAW DATA
        $requestData = array(
            'account_name'          => $order_data['account_username'] . "@" . $order_data['realm'],
            'current_product_id'    => '', // 12
            'contract_term_id'      => '',   // 1
            'realm_id'              => '',           // 1
            'account_email'         => '',      //fred@gmail.com !Not mandatory
            'account_customer_code' => '', // GAR012 // !Not mandatory
            'send_email'            => 'true',      // true

            'password'                   => '',
            'current_contract_term_id'   => '',
            'current_product_start_date' => '',
            'account_comment'  => '',  // !Not mandatory
        );
        unset($requestData);
        */

        // get realmId by realmname
        $realmId = $this->get_ionline_realm_id( array('realm_name' => $order_data['realm']));
        $username = $order_data['account_username'] . "@" . $order_data['realm'];

        // TODO : fetch realm id by realm name
        // fetch product id by product name

        // data from answer
        $requestData = array(

             "account_name"               => $username,
             "password"                   => $pass,
             "realm_id"                   => $realmId, //  openweb.adsl
             "current_product_id"         => $class, //  CAPS0050 , Capped Standard 50GB
             "current_contract_term_id"   => 1, // always should be 1 for 1 month
             "account_email"              => $email,
            // "account_customer_code"      => "1",
             "current_product_start_date" => date("Y-m-d"),//"2016-10-13",
             "send_email"                 => 1,
             "account_comment"             => $comment // string , not mandatory

        );


        // e.g. :
        /*
                POST /api/v1/accounts
                Host: api.fls.ionline.co.za
                Authorization: Basic xyz
                Content-Type: application/json
                Accept: application/json
                BODY
                {"account_name":"me@test.com",
                "password":"123123123",
                "realm_id":1,
                "current_product_id":1,
                "current_contract_term_id":8,
                "account_email":"me@test.com",
                "account_customer_code":"123",
                "current_product_start_date": "2016-09-13",
                "send_email":1
                }

         */

        $response = $this->api_response_handler($this->send_request('POST', $this->allAccountsApiPrefix, $requestData));
        return $response;

        /*
         * response without handler :
          Array
            (
                [result] => 1
                [message] => Request was successfully processed
                [user_message] =>  -
                [api_response] => stdClass Object
                    (
                        [status] => success
                        [type] => create
                        [result] => Create successful
                    )

            )

         */
    }

    public function get_all_realms(){

        // generate request URL
        $requestUrl = $this->allRealmsApiPrefix;

        $response = $this->api_response_handler($this->send_request('GET', $requestUrl, null));
        // parse response
        return $response;

        // example :
        /*
          [result] => Array
                (
                    [0] => stdClass Object
                        (
                            [realm_id] => 1
                            [realm_name] => openweb.co.za
                            [access_provider] => OpenServe IPC
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )

                    [1] => stdClass Object
                        (
                            [realm_id] => 2
                            [realm_name] => openweb.adsl
                            [access_provider] => OpenServe IPC
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )

                    [2] => stdClass Object
                        (
                            [realm_id] => 3
                            [realm_name] => platinum.co.za
                            [access_provider] => OpenServe IPC
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )

                    [3] => stdClass Object
                        (
                            [realm_id] => 4
                            [realm_name] => mynetwork.co.za
                            [access_provider] => OpenServe IPC
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )

                    [4] => stdClass Object
                        (
                            [realm_id] => 5
                            [realm_name] => dslheaven.co.za
                            [access_provider] => OpenServe IPC
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )

                )





         */
    }

    public function get_all_products(){

        // generate request URL
        $requestUrl = $this->allProductsApiPrefix;

        $response = $this->api_response_handler($this->send_request('GET', $requestUrl, null));

        return $response;


        /*
         *  Response without handler
         *
        [user_message] =>  -
        [api_response] => stdClass Object
        (
            [status] => success
            [type] => read
            [result] => Array
            (
                [0] => stdClass Object
                        (
                            [product_id] => 1
                            [product_code] => CAPS0050
                            [product_name] => Capped Standard 50GB
                            [product_solution] => Capped
                            [product_group] => Capped
                            [product_class] => Standard
                            [product_description] => A cool product
                            [product_limit] => 50
                            [product_base_speed] => 0
                            [active_status] => 1
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )

            [1] => stdClass Object
                        (
                            [product_id] => 2
                            [product_code] => CAPS0075
                            [product_name] => Capped Standard 75GB
                            [product_solution] => Capped
                            [product_group] => Capped
                            [product_class] => Standard
                            [product_description] => A cool product
                            [product_limit] => 75
                            [product_base_speed] => 0
                            [active_status] => 1
                            [created_at] => 2016-09-13 00:00:00
                            [updated_at] => 2016-09-13 00:00:00
                        )
        */

    }

    public function get_product_info($productId){
            // get particular product information

            // TODO : validate productID

            $requestUrl = $this->productApiPrefix . $productId;


            $response = $this->send_request('GET', $requestUrl, null);
            return $response;

            /*
             Array
                (
                    [result] => 1
                    [message] => Request was successfully processed
                    [user_message] =>  -
                    [api_response] => Array
                        (
                            [status] => success
                            [type] => read
                            [result] => Array
                                (
                                    [0] => stdClass Object
                                        (
                                            [product_id] => 1
                                            [product_code] => CAPS0050
                                            [product_name] => Capped Standard 50GB
                                            [product_solution] => Capped
                                            [product_group] => Capped
                                            [product_class] => Standard
                                            [product_description] => A cool product
                                            [product_limit] => 51200
                                            [product_base_speed] => 0
                                            [active_status] => 1
                                            [created_at] => 2016-09-13 00:00:00
                                            [updated_at] => 2016-09-13 00:00:00
                                        )

                                )

                        )

                )

            */

    }



    public function generate_string_with_parameters($requestParamsArray, $slashFirst = false){

        $requestParamsStr = "";
        if (!empty($requestParamsArray)){
            $requestParamsStr = "?";
            foreach ($requestParamsArray as $paramStr)
                $requestParamsStr .= "&" . $paramStr;

            $requestParamsStr = str_replace("?&", "?", $requestParamsStr);
        }
        if ($slashFirst === true)
            $requestParamsStr = str_replace("?", "/", $requestParamsStr);


        return $requestParamsStr;
    }

    public function get_all_accounts($params = null){

        $requestParamsArray = array();
        $pageSize = 0;
        // check if int
        if (isset($params['limit']))
            $requestParamsArray[] = "pageSize=" . $params['limit'];

        $startIndex = 0;
        // check if int
        if (isset($params['skip']))
            $requestParamsArray[] = "pageStartIndex=" . $params['skip'];

        // add filters support

        $requestParamsStr = $this->generate_string_with_parameters($requestParamsArray);

        // generate request URL
        $requestUrl = $this->allAccountsApiPrefix . $requestParamsStr;

        //var_dump($requestUrl);
        //die();

        $response = $this->send_request('GET', $requestUrl, null);
        // parse response
        return $response;

    }

    // including pending updates
    public function change_class($order_data, $params){
    /*
     3.2.2. Change Product (upgrade/downgrade)

    To change a product the next_product array must be completed
    The next_product_id, next_contract_term_id, next_product_upgrade_date are required fields to effect the change.
    The next_product_upgrade_date enables the scheduling of an upgrade at a particular date. For immediate
    upgrades, the current day’s date can be used and change will be done immediate.
    WARNING: Downgrade of products is also possible immeadately but counters will note be reset. Therefore a customer
    may become capped or be shaped further still if they downgrade to another product which is a package below. A
    recommendation is to schedule the downgrade at month end.
    Account comment is available for inclusion but is not required.

        Example - :
        PUT /api/v1/account/test1@dsl.ionline.co.za/upgrade
        Host: api.fls.ionline.co.za
        Authorization: Basic j1jk21kj2g1jgkjg12
        Content-Type: application/json
        Accept: application/json
        BODY
        { "next_product_id" :31,
        "next_contract_term_id":1,
        "next_product_upgrade_date":"2016-10-10"}
     */

        $username = $order_data['account_username'] . "@" . $order_data['realm'];

        $requestData = array(

            'next_product_id'           => $params["next_product_id"], // INT , !mandatory, e.g. 12
            'next_contract_term_id'     => $params["next_contract_term_id"], // INT , !mandatory  e.g. 1
            'next_product_upgrade_date' => $params["next_product_upgrade_date"], // DATE (YYYY-MM-DD), !mandatory e.g.  2016-05-10

            //'account_comment' => $params["account_comment"], // STRING, not mandatory

        );

        // doesn't wrk
        //if (isset($params["account_comment"]))
        //   $requestData["account_comment"] = $params["account_comment"];



        $requestUrl = $this->accountApiPrefix . $username . '/upgrade';
        $response = $this->send_request('PUT', $requestUrl, $requestData);
        $response = $this->api_response_handler($response);

        return $response;
    }


    public function update_account_info($order_data, $params){

        /*
            Example:
            PUT /api/v1/account/test1@dsl.ionline.co.za/info
            Host: api.fls.ionline.co.za
            Authorization: Basic j1jk21kj2g1jgkjg12==
            Accept: application/json
            Content-Type: application/json

            BODY
            {
                "account_email":"fred@gmail.com",
                "account_customer_code":"FRE010",
                "account_comment":"This is a comment.",
                "send_email":false
            }
         */

        $accountInfoFields = array("account_email" ,  "account_customer_code" , "account_comment" , "send_email");
        $username = $order_data['account_username'] . "@" . $order_data['realm'];
        $requestData = array();

        foreach ($accountInfoFields as $field)
            if (isset($params[$field]) && !empty($params[$field]))
                $requestData[$field] = $params[$field];

        /*
            $requestData = array(

                "account_email"         => '', // STRING, not mandatory
                "account_customer_code" => "", // e.g. FRE010 , STRING, not mandatory
                "account_comment"       => "", // STRING, not mandatory
                "send_email"            => "", //e.g. true/false,  bool , not mandatory
            );
        */

        $requestUrl = $this->accountApiPrefix . $username . '/info';
        $response = $this->send_request('PUT', $requestUrl, $requestData);
        $response = $this->api_response_handler($response);

        return $response;

    }


    public function update_account_password($order_data, $params){
    /*
       Example:
        PUT /api/v1/account/test1@dsl.ionline.co.za/password
        Host: api.fls.ionline.co.za
        Authorization: Basic j1jk21kj2g1jgkjg12==
        Accept: application/json
        Content-Type: application/json
        BODY
        {"password": "udaufu1"}
     */

        // some pass validation


        $username = $order_data['account_username'] . "@" . $order_data['realm'];
        $requestData = array(
            "password"         => $params['password'], // STRING, e.g. j1jh22ha12,   mandatory (At least 8 characters, at least 1 number)
        );

        $requestUrl = $this->accountApiPrefix . $username . '/password';
        $response = $this->send_request('PUT', $requestUrl, $requestData);
        $response = $this->api_response_handler($response);

        return $response;

    }


    public function suspend_account($order_data, $status){


        // $status -> “Suspended” or “BillingSuspended”
        $suspend_status = "Suspended";
        //if (in_array($status, array("suspended", "billingSuspended"))) // statuses from API are wrong
        if (in_array($status, array("Suspended", "BillingSuspended")))
            $suspend_status = $status;


        $username = $order_data['account_username'] . "@" . $order_data['realm'];
        /*
           //suspends an account from the user
            side. billingSuspended can be used for
            accounts/non-payment billing .
            ----------------------------------------

            The account can be suspended by the user or by the enterprise.
            The status needs to be submitted as either “suspended” or “billingSuspended”.
            Suspended refers to the suspension of the account for operational reasons (suspected fraud or inuse by the customer)
            Billing suspended refers to account payment issues for accounts and the next logical status might be cancelled.
            All suspensions are effective immediately with the update the account status.

            Example:
            PUT /api/v1/account/edwin@dsl.ionline.co.za/suspend
            Host: api.fls.ionline.co.za
            Authorization: Basic j1jk21kj2g1jgkjg12==
            Content-Type: application/json
            Accept: application/json
            BODY
            { "status" : "BillingSuspended"}

         */

        //var_dump($suspend_status); die();

        $requestData = array(
            "status"         => $suspend_status, // STRING, e.g. 'suspended' or 'billingSuspended', mandatory
        );

        $requestUrl = $this->accountApiPrefix . $username . '/suspend';
        $response = $this->send_request('PUT', $requestUrl, $requestData);
        $response = $this->api_response_handler($response);

        return $response;


    }

    public function cancel_account($order_data, $params){


        /*
         //cancels an account via backend script
         on date of cancel_date which can be future cancel or current.


        Cancelling an account is effective immediately. The account will be removed from the system at month end.
        This will mean the customer cannot connect and will not be able to use that particular username. Cancelled status is
        automatically updated upon calling this endpoint. The cancel_date must be submitted as well. A future date will
        indicate the date by which the account must be cancelled. The present (today) date will indicate that the account
        must be cancelled with immediate effect.
        The account comment field is available to add additional information to the account on the user’s side.

            Example:
            PUT /api/v1/account/test1@dsl.ionline.co.za/cancel
            Host: api.fls.ionline.co.za
            Authorization: Basic cj1jk21kj2g1jgkjg12==
            Content-Type: application/json
            BODY
            {"cancel_date":"2016-10-31"}

        */
        // TODO : validate date (YYYY-MM-DD)



        $username = $order_data['account_username'] . "@" . $order_data['realm'];
        $requestData = array(
            "cancel_date"         => $params['cancel_date'], // DATE, e.g. 2016-09-24 ,  mandatory
        );

        $requestUrl = $this->accountApiPrefix . $username . '/cancel';
        $response = $this->send_request('PUT', $requestUrl, $requestData);
        $response = $this->api_response_handler($response);

        return $response;

    }


    public function restore_account($order_data){

        $username = $order_data['account_username'] . "@" . $order_data['realm'];
        /*
          //'uncancels' an account in case of
            error or paid-up status before month end

            If the account has been cancelled and not cleared (before month-end) the account
            can be restored with an update on the account status

            Example
            PUT /api/v1/account/test1@dsl.ionline.co.za/restore
            Host: api.fls.ionline.co.za
            Authorization: Basic cj1jk21kj2g1jgkjg12==
            Content-Type: application/json
            Accept: application/json
            BODY:
            n/a

         */

        $requestUrl = $this->accountApiPrefix . $username . '/restore';
        $response = $this->send_request('PUT', $requestUrl, null);
        $response = $this->api_response_handler($response);

        return $response;


    }


    public function topup_to_account($username, $params){

        /*
          //top up a capped account with ‘product limit value from products table.


            Capped accounts are able to be topped up or temporary limit increases.
            Top ups expire in the month they were purchased
            Account comment can be completed if required.
            The top up product id needs to be sent in the call and will be validated.
            The size of the top up will be automatically added based on the product’s limit size.


            Example
                PUT /api/v1/account/test1@dsl.ionline.co.za/topup/add
                Host: api.fls.ionline.co.za
                Authorization: Basic cj1jk21kj2g1jgkjg12==
                Accept: application/json
                Content-Type: application/json
                BODY
                {"product_id":45}
         */


        /*
          product_id :
                Lookup to table. Must be a Top up product. Must be applied to a capped product.
         */
        $requestData = array(
            "product_id"         => '', // INT, e.g. 12 ,  mandatory
        );

        $requestUrl = $this->accountApiPrefix . $username . $this->topupSuffix . 'add';
        $response = $this->send_request('PUT', $requestUrl, $requestData);

    }

    public function get_topup_history($username, $params){
        /*
          //get a list topups applied to the account for a particular month

          Example :
          http://api.fls.ionline.co.za/v1/api/v1/account/test1@dsl.ionline.co.za/topup/history?month=2016-10

        */

        $requestData = array(

            'month' => '', // DATE, e.g. 2016-10 ,

        );


        $requestUrl = $this->accountApiPrefix . $username . $this->topupSuffix . '/history';
        $response = $this->send_request('GET', $requestUrl, $requestData);

    }


    // activity
    public function get_user_activity($order_data, $params){

        // example
        // http://api.fls.ionline.co.za/v1/account/fred@idsl.ionline.co.za/activity/date?date=2016-09-01
        // http://api.fls.ionline.co.za/v1/account/fred@idsl.ionline.co.za/session/period/date-from=2016-08-01&date-to=2016-08-31

        // validate $params fields
        $username = $order_data['account_username'] . "@" . $order_data['realm'];

        $firstSlashForParams = false;
        switch ($params['activity_type']) {

            case 'month' :
                            // activity type : '/month';    fields : 'month' e.g. 2016-05
                            // account billing transactions for a month

                            $requestParamsArray[] = 'month=' . $params['period'];
                            break;

            case 'day'   :
                            // activity type : '/day';       fields : 'date' , e.g. 2016-05-05
                            // account billing transactions for a day

                            $requestParamsArray[] =  'date='. $params['period'];
                            break;

            case 'period':
                            // activity type : '/period';    fields : 'date-from', 'date-to' , e.g. 2016-05-01
                            // time period of requested activity
                           $firstSlashForParams = true;
                           $requestParamsArray = array(
                                                    '0' => 'date-from=' . $params['period']['from'],
                                                    '1' => 'date-to=' . $params['period']['to'],
                                                );
                            break;

            // TODO : add failure response
            default :

                $errorResponse = $this->defaultResponseData;
                $errorResponse['message'] = "Parameter is not set";
                $errorResponse['user_message'] = "";
                return $errorResponse;
                break;
        }

        $requestParamsStr = $this->generate_string_with_parameters($requestParamsArray, false);


        //$activityType = "/" . $params['activity_type']; // '/month', '/day' or '/period'
        $activityType =  $params['activity_type']; // '/month', '/day' or '/period'
        $requestUrl = $this->accountApiPrefix . $username . $this->activitySuffix . $activityType . $requestParamsStr;

        $rawResponse = $this->send_request('GET', $requestUrl, null);
        $response = $this->api_response_handler($rawResponse);
        //$response['raw'] = $rawResponse;
        return $response;
        


    }


    public function get_user_sessions($order_data, $params){

        /*
           examples :

        http://api.fls.ionline.co.za/v1/account/fred@idsl.ionline.co.za/session/month?month=2016-09
        http://api.fls.ionline.co.za/v1/account/fred@idsl.ionline.co.za/session/period/date-from=2016-08-01&date-to=2016-08-31

         */


        // validate $params fields
        $username = $order_data['account_username'] . "@" . $order_data['realm'];

        if (!isset($params['activity_type']))
            $params['activity_type'] = null;

        $firstSlashForParams = false;
        switch ($params['activity_type']) {

            case 'month' :
                // activity type : '/month';    fields : 'month' e.g. 2016-05
                // sessions for a month

                $requestParamsArray[] = 'month=' . $params['period'];
                break;

            case 'day'   :
                // activity type : '/day';       fields : 'date' , e.g. 2016-05-05
                // list of sessions where start time = date

                $requestParamsArray[] =  'date='. $params['period'];
                break;

            case 'period':
                // activity type : '/period';    fields : 'date-from', 'date-to' , e.g. 2016-05-01
                // time period of requested sessions
                $firstSlashForParams = true;
                $requestParamsArray = array(
                    '0' => 'date-from=' . $params['period']['from'],
                    '1' => 'date-to=' . $params['period']['to'],
                );
                break;

            case 'current':
                // current open session
                $requestParamsArray = null;
                break;

            // TODO : add failure response
            default :

                $errorResponse = $this->defaultResponseData;
                $errorResponse['message'] = "Parameter is not set";
                $errorResponse['user_message'] = "";
                return $errorResponse;
                break;
        }

        $requestParamsStr = $this->generate_string_with_parameters($requestParamsArray, false);


        //$activityType = "/" . $params['activity_type']; // '/month', '/day', '/period' or /current
        $activityType =  $params['activity_type']; // '/month', '/day', '/period' or /current
        $requestUrl = $this->accountApiPrefix . $username . $this->sessionSuffix . $activityType . $requestParamsStr;

        $rawResponse = $this->send_request('GET', $requestUrl, null);
        $response = $this->api_response_handler($rawResponse);
        //$response['raw'] = $rawResponse;
        return $response;

    }

    public function update_ionline_realms_table(){

        // get all realms from API
        $realmsResponse = $this->get_all_realms();
        if (!$realmsResponse['result'])
            return false;

        $realmsArray = $realmsResponse['api_response'];
        foreach ($realmsArray as $realm) {

            $realmData = array(

                'realm_id' => $realm->realm_id,
                'realm_name' => $realm->realm_name,
                'access_provider' => $realm->access_provider,
                'created_at' => $realm->created_at,
                'updated_at' => $realm->updated_at,
                'cron_latest_update' => date('Y-m-d H:i:s'),
            );

            if (empty($realm->realm_id))
                continue;

            // get realm only by unique name !!
            $row = $this->get_realm_row_from_ionline_realms(array('realm_name' => $realmData['realm_name']));

            if (empty($row)) {
                // insert
                $result = $this->add_realm_to_ionline_realms($realmData);
            } else {
                // update
                $result = $this->update_realm_ionline_realms($realmData);
            }


        }

        return;


    }

    public function add_realm_to_ionline_realms($row){

        if (empty($row))
            return false;


        $result = $this->db->insert('realms_ionline', $row);
        $response = false;
        if ($result)
            $response = $this->db->insert_id();
        return $response;

    }

    public function update_realm_ionline_realms($row){


        $this->db->where('realm_name', $row['realm_name']);
        $result = $this->db->update('realms_ionline', $row);
        $response = $result;

        // possible handler

        return $response;
    }


    public function get_realm_row_from_ionline_realms($searchKeys = array()){


        $this->db->select();
        foreach ($searchKeys as $key => $value){
            $this->db->where($key, $value);
        }

        $query = $this->db->get('realms_ionline');
        $result = $query->result_array();
        if (!empty($result) && (count($result) == 1))
            $result = $result[0];

        return $result;


    }

    public function get_ionline_realm_id($searchKeys = array()){


        // possible keys : 'realm_name' (realm name without '@')
        if (empty($searchKeys))
            return false;

        $this->db->select('realm_id');
        foreach ($searchKeys as $key => $value){

            $this->db->where($key, $value);
        }

        $query = $this->db->get('realms_ionline');
        $result = $query->result_array();

        $returnId = false;
        if (!empty($result))
            $returnId = $result[0]['realm_id'];

        return $returnId;
    }


    // not in use
    // ------------------------------------------------------------------------------
    public function get_ionline_realm_id_from_isdsl_table($searchKeys = array()){

        // possible keys : 'id', 'realm' (realm name without '@')
        if (empty($searchKeys))
            return false;

        $this->db->select('ionline_id');
        foreach ($searchKeys as $key => $value){

            $this->db->where($key, $value);
        }

        $query = $this->db->get('realms');
        $result = $query->result_array();

        $returnId = false;
        if (!empty($result))
            $returnId = $result[0]['ionline_id'];

        return $returnId;
    }



    public function update_ionline_products_table(){


        // get all products from API
        $allProductsResponse = $this->get_all_products();
        //echo "<pre>";
        //print_r($allProductsResponse);
        //echo "</pre>";
        //die();

        if (!$allProductsResponse['result'])
            return false;

        echo "<pre>";
        print_r($allProductsResponse);
        echo "</pre>";
        echo "<hr/>";

        $productsArray = $allProductsResponse['api_response'];
        foreach ($productsArray as $product) {

            $productData = array(

                'product_id'          => $product->product_id,
                'product_code'        => $product->product_code,
                'product_name'        => $product->product_name,
                'product_solution'    => $product->product_solution,
                'product_group'       => $product->product_group,
                'product_class'       => $product->product_class,
                'product_description' => $product->product_description,
                'product_limit'       => $product->product_limit,
                'product_base_speed'  => $product->product_base_speed,
                'active_status'       => $product->active_status,
                'created_at'          => $product->created_at,
                'updated_at'          => $product->updated_at,
                'cron_latest_update' => date('Y-m-d H:i:s'),
            );

            if (empty($product->product_code) || empty($product->product_id))
                continue;

            // get unique product by name
            $row = $this->get_product_row_from_ionline_classes(array('product_code' => $productData['product_code']));

            if (empty($row)) {
                // insert
                $result = $this->add_product_to_classes_ionline($productData);
            } else {
                // update
                $result = $this->update_product_classes_ionline($productData);

            }
        }
        return;
    }


    public function get_product_row_from_ionline_classes($searchKeys = array()){

        $this->db->select();
        foreach ($searchKeys as $key => $value){
            $this->db->where($key, $value);
        }

        $query = $this->db->get('classes_ionline');
        $result = $query->result_array();

        if (!empty($result) && (count($result) == 1))
            $result = $result[0];

        return $result;

    }


    public function add_product_to_classes_ionline($row){

        if (empty($row))
            return false;


        $result = $this->db->insert('classes_ionline', $row);
        $response = false;
        if ($result)
            $response = $this->db->insert_id();
        return $response;

    }

    public function update_product_classes_ionline($row){


        $this->db->where('realm_name', $row['realm_name']);
        $result = $this->db->update('classes_ionline', $row);
        $response = $result;

        // possible handler

        return $response;
    }

    // helper
    public function get_row_from_mapper($searchKeys = array('map_id >' => '0')){

        $this->db->select();
        foreach ($searchKeys as $key => $value){
            $this->db->where($key, $value);
        }

        $query = $this->db->get('classes_map');
        $result = $query->result_array();
        if (!empty($result) && (count($result) == 1))
            $result = $result[0];

        return $result;

    }

    // helper
    public function update_mapper_ionline_classes_ids(){


        $allMapClasses = $this->get_row_from_mapper();
        foreach ($allMapClasses as $mapClassRow){
            $ionlineClassRow = $this->get_product_row_from_ionline_classes(
                                            array('product_code' => trim($mapClassRow['class_name_ionline']))
                                         );
            if (empty($ionlineClassRow))
                 continue;

            $updateResult = $this->update_row_classes_map(
                                            array(
                                                    'map_id'             => $mapClassRow['map_id'],
                                                    'class_id_ionline'   => $ionlineClassRow['product_id'],
                                                    'class_name_ionline' => $ionlineClassRow['product_code'],
                                                )
                                        );

            // handle result
        }

     }
    // helper
     public function update_row_classes_map($row){

         /*
                [0] => Array
                    (
                        [map_id] => 1
                        [class_id_isdsl] => 1
                        [class_name_isdsl] => nosvc
                        [realm_name_isdsl] => mynetwork.co.za
                        [class_id_ionline] =>
                        [class_name_ionline] => CAPS0050
                    )

                [1] => Array
                    (
                        [map_id] => 2
                        [class_id_isdsl] => 2
                        [class_name_isdsl] => ow-hc1
                        [realm_name_isdsl] => mynetwork.co.za
                        [class_id_ionline] =>
                        [class_name_ionline] => CAPS0050
                    )
          */



            $this->db->where('map_id', $row['map_id']);
            $result = $this->db->update('classes_map', $row);
            $response = $result;

            // possible handler

            return $response;

     }

        //$allProductsFormDb = $this->get_product_row_from_ionline_classes(array('product_'));



}
