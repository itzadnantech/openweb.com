<?php

    class Showmax_api_model extends CI_Model
    {

        private $debug_mode = true;
        public $mock_mode = true;             // decides what server we use for API ('dev' server or 'production')
        public $mock_response_type = "true";  // can be "true" or "false" (string)
        // will return response $mock_response[$mock_response_type]

        private $stage_api_url = "https://api.showmax.io";
        private $production_api_url = "https://api.showmax.com";
        private $api_url = "";

        private $partner_id = "prt000007za";
        private $stage_token = "0a2c50ad-8b15-48c7-8305-0f5f7f0d36b6";
        private $production_token = "";
        private $token = "";

        private $partner_sub_url = "/partner";  // contains  full {id}/partner string after initialization
        private $user_sub_url = "/user";

        private $auth_type = "Bearer";

        // how much characters allowed for "params" field at "showmax_api_calls" table
        private $db_restriction_params = 350;
        private $db_restriction_response_message = 255;
        private $db_restriction_http_code_message = 255;
        private $db_restriction_raw_response = 255;

        // set to 'true' if you want to log all api calls inside db (required for subscription features)
        private $enable_db_log = true;

        public $subscription_type_premium = "premium";
        public $subscription_type_select = "select";
        public $subscription_types = array();


        private $http_code_storage = array();
/*
            200 – Subscription successfully deactivated. You will typically get 204, but please handle 200 as well.
            204 – Subscription successfully deactivated
            400 – Bad request - general problem with formatting of your request (e.g. not correctly escaped characters in URL)
            403 – Your are not allowed to access this resource (e.g. invalid values or combination of  access_token and partner_id)
            403 – Instrument not suspended when suspended set to false
            403 – Instrument not active
            403 – Instrument deactivated
            423 – User locked. Another billing process is running on given user, try again later
            404 – User with specified id doesn’t exist or doesn’t have an active subscription.
            405 – You are using other HTTP method than expected
            500 – Error on ShowMax side.
*/


        public $url_subscription_postfix = "/subscription";

        /*
         * .io creds:
         *  Username:           TheseAreNotTheDroids
            Password:           YourAreLookingF0r
         */


        public $defaultResponseData = array(
            "result" => false,
            "message" => "Failure : Please contact the admin to resolve it",
            "user_message" => "Failure : Please contact the admin to resolve it",
            "api_response" => null,
            "additional_data" => array(

                        "request_url" => "",
                        "raw_response" => "",
                        "http_code" => "",
                        "db_log" => "",
                        )
             );

        private $mock_response = array("true" => array(), "false" => array());





        /*  DB restrictions

             function name should be lass that 50 chars


        */

        function __construct(){


            $this->api_url = $this->production_api_url;
            $this->token = $this->production_token;
            if ($this->debug_mode){

                $this->api_url = $this->stage_api_url;
                $this->token = $this->stage_token;
            }

            $this->init_mock_response();

            // init subscription types
            $this->subscription_types[] = $this->subscription_type_premium;
            $this->subscription_types[] = $this->subscription_type_select;


            $this->partner_sub_url = "/" . $this->partner_id . $this->partner_sub_url;
            $this->init_http_code_mapper();

        }

        function init_mock_response(){

            $this->mock_response["false"] = $this->defaultResponseData;
            $this->mock_response["false"]["message"] = "mock fail response";
            $this->mock_response["false"]["user_message"] = "mock fail (user) response";
            // add random  error http code from avaialble pool

            /*
           $defaultResponseData = array(
                "result" => false,
                "message" => "Failure : Please contact the admin to resolve it",
                "user_message" => "Failure : Please contact the admin to resolve it",
                "api_response" => null,
                "additional_data" => array(

                    "request_url" => "",
                    "raw_response" => "",
                    "http_code" => "",
                    "db_log" => "",
                )
            );
            */

            $this->mock_response["true"] = $this->defaultResponseData;
            $this->mock_response["true"]["message"] = "mock success response";
            $this->mock_response["true"]["user_message"] = "mock success (user) response";


        }

        function init_http_code_mapper(){

            $basic_mapper = array(
                "success_codes" => array(), // 200, 201 - ?
                "fail_codes"    => array("400", "403", "405", "500"),
                "code_mapper"   => array(
                                "400" => "Bad request - general problem with formatting of your request (e.g. not correctly escaped characters in URL)",
                                "403" => "Your are not allowed to access this resource (e.g. invalid values or combination of access_token and partner_id )",
                                "405" => "You are using other HTTP method than expected",
                                "500" => "Error on ShowMax side" ,
                            ),
                "required_fields_for_response" => array(),
            );

            // init user_search
            $this->http_code_storage["user_search"] = $basic_mapper;
            $this->http_code_storage["user_search"]["success_codes"][] = "200";
            $this->http_code_storage["user_search"]["code_mapper"]["200"] = "Single matching user record found";
            $this->http_code_storage["user_search"]["fail_codes"][] = "404";
            $this->http_code_storage["user_search"]["code_mapper"]["404"] = "User not found";
            $this->http_code_storage["user_search"]["fail_codes"][] = "409";
            $this->http_code_storage["user_search"]["code_mapper"]["409"] = "Multiple user records found";
            $this->http_code_storage["user_search"]["required_fields_for_response"][] = "user_id";




            // init activate_subscription
            $this->http_code_storage["activate_subscription"] = $basic_mapper;
            $this->http_code_storage["activate_subscription"]["success_codes"][] = "201";
            $this->http_code_storage["activate_subscription"]["code_mapper"]["201"] = "Subscription successfully activated";
            $this->http_code_storage["activate_subscription"]["fail_codes"][] = "409";
            $this->http_code_storage["activate_subscription"]["code_mapper"]["409"] = "Subscription is already active";
            $this->http_code_storage["activate_subscription"]["required_fields_for_response"][] = "activation_code";

            // modify_subscription
            $this->http_code_storage["modify_subscription"] = $basic_mapper;
            $this->http_code_storage["modify_subscription"]["success_codes"][] = "200";
            $this->http_code_storage["modify_subscription"]["success_codes"][] = "204";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["200"] = "Subscription successfully deactivated";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["204"] = "Subscription successfully deactivated";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["403"] .= " or Instrument not suspended when suspended set to false";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["403"] .= " or Instrument not active";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["403"] .= " or Instrument deactivated";
            $this->http_code_storage["modify_subscription"]["fail_codes"][] = "404";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["404"] = "User with specified id doesn’t exist or doesn’t have an active subscription.";
            $this->http_code_storage["modify_subscription"]["fail_codes"][] = "423";
            $this->http_code_storage["modify_subscription"]["code_mapper"]["423"] = "User locked. Another billing process is running on given user, try again later";

            // deactivate_subscription
            $this->http_code_storage["deactivate_subscription"] = $this->http_code_storage["modify_subscription"];
            $this->http_code_storage["deactivate_subscription"]["code_mapper"]["403"] = $basic_mapper["code_mapper"]["403"];


        }


        /*
         *
         * Integration https://api.showmax.com/{partner_id}/partner/user/{id}/subscription
         *
         * You must pass access_token with every partner API call, either as a bearer token in
           Authentication header:
           > Authorization: Bearer {access_token}

          or as a value of a query parameter named access_token. Such as
          https://api.showmax.[com|io]/{path}?access_token={access_token}
         *
         */


        private function send_request($requestType, $requestUrl = '', $data = null)
        {

            // make dummy request and return dummy response
            if ($this->mock_mode){
                $response = $this->send_mock_request($requestType, $requestUrl, $data);
                return $response;
            }

            // set default response as FAIL
            $response = $this->defaultResponseData;
            if (!in_array($requestType, array("PUT", "POST", "GET", "DELETE"))) {
                $response["message"] = "Wrong request type";
                return $response;
            }


            // https://api.showmax.[com|io]/{partner_id}/partner/user/search
            // init curl
            $baseUrl = $this->api_url;
            $curlUrl = $baseUrl . $this->partner_sub_url . $requestUrl;

            // prepare data for request
            $jsonData = json_encode($data);

            /*
            var_dump($requestType); echo "<hr/>";
            var_dump($curlUrl); // remove debug later
            echo "<hr/>"; var_dump($jsonData);
            //die(' request test');
            */

            $ch = curl_init($curlUrl);


            //$sessionHash = base64_encode($this->username . ":" . $this->password);
            $auth_hash = "Authorization:". $this->auth_type . " " . $this->token;


            // set CURL opts depends on request type
            switch ($requestType) {
                case "POST" :
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    break;
                case "PUT" :
                    //curl_setopt($ch, CURLOPT_PUT, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    break;
                case "DELETE" :
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                case "GET" :
                    break;
            }


            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array(
                    //'Host:' . "",
                    'Accept:application/json',
                    'Content-Type:application/json',
                    $auth_hash,
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

            //echo "<hr/>";
            //var_dump($rawResponse);
            //die();

            // processes possible curl errors
            $curl_error = '';
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resp === false) {
                $curl_error = 'request error : ' . curl_error($ch) . " ( " . $http_code . " )";
            } else {
                $resp = json_decode($resp);
            }
            curl_close($ch);

            // format Object response from API
            if (is_object($resp))
                $resp = (array)$resp;

            // if (is_string($resp))
            // /* do smth */

            $response["api_response"] = $resp;
            $response["additional_data"]["request_url"] =  $this->api_url . " ... " . $requestUrl;
            $response["additional_data"]["raw_response"] = $rawResponse;
            $response["additional_data"]["http_code"] = $http_code;

            // process curl error
            if (!empty($curl_error)) {
                $response["message"] = $curl_error;
                return $response;
            }

            // set 'Success' message as default
            $response["result"] = true;
            $response["message"] = $response["user_message"] = "Request was successfully processed";


            return $response;

        }


        private function send_mock_request($requestType, $requestUrl = '', $data = null)
        {

            // set default response as FAIL
            $response = $this->defaultResponseData;
            if (!in_array($requestType, array("PUT", "POST", "GET", "DELETE"))) {
                $response["message"] = "Wrong request type";
                return $response;
            }

            // https://api.showmax.[com|io]/{partner_id}/partner/user/search
            // init
            $baseUrl = $this->api_url;
            $curlUrl = $baseUrl . $this->partner_sub_url . $requestUrl;

            $rawResponse = $resp = null;
            $http_code = null;

            $response["api_response"] = $resp;
            $response["additional_data"]["request_url"] =  $this->api_url . " ... " . $requestUrl;
            $response["additional_data"]["raw_response"] = $rawResponse;
            $response["additional_data"]["http_code"] = $http_code;

            // set 'Success' message as default
            $response["result"] = true;
            $response["message"] = $response["user_message"] = "Request was successfully processed";

            return $response;

        }

        public function generate_mock_response($resp, $response_mapper){

            /*
                if mock_response_type is "true" :
                we generate success http_code + add success mock message + add required fields

                if mock_response is "false" :
                we generate random fail http_code + add fail mock message
            */

           // generate a mock response,  depends on $mock_type
           $mock_type = $this->mock_response_type;
           $default_resp_result = false;                // value for $resp["result"]
           $http_code_type_str = "fail_codes";          // type of http code pool
           $resp["message"] = $this->mock_response[$mock_type]["message"];           // message for admin and developer
           $resp["user_message"] = $this->mock_response[$mock_type]["user_message"]; // message for users

            if ($mock_type == "true") {
                $default_resp_result = true;
                // generate success api_response data according to required fields
                $resp["api_response"] = $this->generate_mock_required_fields($response_mapper["required_fields_for_response"]);
                $http_code_type_str = "success_codes";
            }

            $resp["result"] = $default_resp_result;
            $resp["additional_data"]["raw_response"] = json_encode($resp["api_response"]); // generate raw response
            // generate http code according to response type
            $resp["additional_data"]["http_code"] = $this->generate_mock_http_code($response_mapper[$http_code_type_str]);
            // $resp["additional_data"]["request_url"] - already was set inside send_mock_request()


            return $resp;

        }

        public function generate_mock_required_fields($array_of_fields){

            $result = array();
            foreach ($array_of_fields as $key){
                $result[$key] = "Dummy value for '" . $key . "' key";
            }

            return $result;
        }

        public function generate_mock_http_code($array_of_http_codes){

            $count = count($array_of_http_codes);
            $code_index = rand(0, $count - 1);
            if (isset($array_of_http_codes[$code_index]))
                return $array_of_http_codes[$code_index];


            return $array_of_http_codes[0];
        }

        public function api_response_handler($resp, $response_mapper, $checkEmptyResponse = true)
        {

            /*
             example of http_code_mapper

                "success_codes" = ["201", "200", ...]
                "fail_codes" =  ["401", "500", ...]
                "code_mapper" = [
                        "201" => "Subscription successfully activated",
                        "400" =>  "Bad request - general problem with formatting of your request
                ]
                "required_fields_for_response" => []


              private $defaultResponseData = array(
                    "result"        => false,
                    "message"       => "Failure : Default message",
                    "user_message"  => "Failure : Please contact the admin to resolve it",
                    "api_response"  => null,
                    "additional_data" => user_message / request_url / raw_response / http_code
              );
             */

            // response backup
            // $response["message"] = $response["user_message"] = "Request was successfully processed"



            // curl error   or connection issues . Default message were set at the "Request section"
            if ($resp["result"] === false)
                return $resp;


            // correct current response if mock mode was enabled
            $mock_message = "";
            if ($this->mock_mode) {
                $resp = $this->generate_mock_response($resp, $response_mapper);
                $mock_message = " (" . $resp["message"] . ") ";
            }



            // $response["api_response"] always contains result of json_decode("$api_raw_response")
            // json_decode of simple STRING results NULL
            $decoded_api_response = $resp["api_response"];
            $raw_api_response = $resp["additional_data"]["raw_response"];


            $defaultSuccessMessage = "Request was successfully processed";

            // failure by default
            $resp["result"] = false;
            $resp["message"] = "";
            $resp["user_message"] = $this->defaultResponseData["user_message"];

            // init HTTP code message with error/success explanation
            $response_http_code = $resp["additional_data"]["http_code"];
            $response_http_code_message = "";
            if (isset($response_mapper["code_mapper"][$response_http_code]))
                $response_http_code_message = $response_mapper["code_mapper"][$response_http_code];

            $http_code_string = "[http code : "
                                        . $response_http_code
                                        . " "
                                        . $response_http_code_message . "]";


            // check HTTP code according to the mapper
            if (in_array($response_http_code, $response_mapper["fail_codes"]))
                $resp["message"] .= $http_code_string;



            // check "required fields" inside response
            $response_format_validation_result = true;
            if (!empty($response_mapper["required_fields_for_response"]))
                foreach ($response_mapper["required_fields_for_response"] as $response_field){
                    if (!isset($decoded_api_response[$response_field])){ // TODO: perhaps, check if not empty too
                        $response_format_validation_result = false; break;
                    }
                }

             // if response doesn't have required fields set 'result' to false
             // also, set default error messages with http code
             if (!$response_format_validation_result )
                 $resp["message"]      .=  " [Required response fields do not exist]";


             // check response for "error field"
             if (is_array($decoded_api_response) && isset($decoded_api_response["error"]))
                $resp["message"] .= " [API error response : " . $decoded_api_response["error"]. "]";

            // check response for "error field" -> "error_code" + "message"
            // example : {"error_code":"SUB1005","lang":"eng","message":"Instrument not activated"}"
            if (is_array($decoded_api_response) && isset($decoded_api_response["error_code"])
                && isset($decoded_api_response["message"])
            )
                $resp["message"] .= " [API error response : (" . $decoded_api_response["error_code"] . ") "
                                 . $decoded_api_response["message"] . "]";


            // check STRING response
            // if response is a string (not an array) and required fields do not exist
            if (($decoded_api_response == NULL) && is_string($raw_api_response) && ($response_format_validation_result == false))
                $resp["message"] .= " [API raw response : " . $raw_api_response . "]";


            // check if response is empty
            if (empty($raw_api_response) && $checkEmptyResponse)
                $resp["message"] .= " [Response is empty]";

            $resp["message"] = trim($resp["message"]);

            // check success case
            if (empty($resp["message"]) //  there were no error during the processing
                && in_array($response_http_code, $response_mapper["success_codes"]) // http_code is in "success_codes"
                && ($response_format_validation_result === true) // required fields exist
                ){

                $resp["result"] = true;
                $resp["message"] = $resp["user_message"] = $defaultSuccessMessage;
                if (!empty($response_http_code_message))
                    $resp["message"] = $response_http_code_message;

            }
            $resp["message"] .= $mock_message;
            return $resp;

            /*
              Response examples :

                Responses :
            201 – Subscription successfully activated
            400 – Bad request - general problem with formatting of your request (e.g. not correctly escaped characters in URL)
            403 – Your are not allowed to access this resource (e.g. invalid values or combination of acces s _token and partner_id)
            405 – You are using other HTTP method than expected
            409 – Subscription is already active
            500 – Error on ShowMax side.
             */

        }

        // 2.1. User Search
        /*
         * Searches for existing ShowMax users by given parameters.
         * If you provide more parameters to search by, users matching all of the criteria will be returned.
         *
         * Authentication: partner
            Query params:
                Opt. params:

                        msisdn (string) – MSISDN to search by. May or may not include leading ‘+’ sign or ‘00’, preceding country code. Subject to ACL.
                        email (string) – Email to search by. Subject to ACL.
            Responses:

            200 – Single matching user record found
            400 – Bad request - general problem with formatting of your request (e.g. not correctly escaped characters in URL)
            403 – Your are not allowed to access this resource (e.g. invalid values or combination of acces s _token and partner_id )
            404 – User not found
            405 – You are using other HTTP method than expected
            409 – Multiple user records found
            500 – Error on ShowMax side



            /*
             *  Error examples :
             *  "{"error":"msisdn, email are missing, exactly one parameter must be provided"}"
             *
             *  Success example :
             *  "Not Found"
             *
             *
             */


        /**
         *
         * exmaple : GET https://api.showmax.[com|io]/{partner_id}/partner/user/search
         * @param $params (array) optional - e.g. ["msisdn" => value, "email" => value]
         * @return (array) or (null) - result
         */
        function user_search($email = null, $msisdn = null){


            $request_str = "/user/search";

            $log_data = array("email" => $email, "msisdn" => $msisdn, "_function" => "user_search");
            $defaultErrorResponse = $this->defaultResponseData;
            if (empty($email) && empty($msisdn)){
                // write to log and return FAIL result
                $defaultErrorResponse["message"]  = "Email and msisdn are empty";
                $this->check_fail_api($defaultErrorResponse, $log_data);
                return $defaultErrorResponse;
            }


            // generate array with parameters
            $params = array();
            if (!empty($msisdn))
                $params["msisdn"] = $msisdn;

            if (!empty($email))
                $params["email"] = $email;

            $request_params = $this->generate_string_with_parameters($params);
            $request_str .= $request_params;


            $raw_response = $this->send_request("GET", $request_str);
            $result = $this->api_response_handler($raw_response, $this->http_code_storage['user_search']);
            $this->check_fail_api($result, $log_data);
            $result["additional_data"]["db_log"] = $this->api_db_log($result, $log_data);

            return $result;
        }

        /*
         *
         *  Error examples :
         *  "{"error":"msisdn, email are missing, exactly one parameter must be provided"}"
         *
         * Success example :
         *  "Not Found"
         *
         * Response example
         * Data: user_id (string) – uuid uniquely identifying user on ShowMax side.
            {
            "user_id": "05096949-62ee-4a97-86f8-cb4e1d1d7def"
            }
         *
         */



        // 5.2. Subscription Activation
        /*
         * This will create a ‘dormant’ subscription on ShowMax side. It will be active from the moment
         * the user comes to ShowMax website and enters the activation code returned by this API call.
         * This call can be made for non existing (on ShowMax side) user. Partner can choose between
         * Premium and Select subscription types.
         *
         * partner_id (string) – Partner ID assigned to you by ShowMax.
         * id (string) – Unique identification of the user on your side

         * subscription_type (string) – Type of subscription (premium or select). Defaults to premium. Subject to ACL.
         */


        /**
         *
         * POST https://api.showmax.[com|io]/{partner_id}/partner/user/{id}/subscription
         * @param $user_id           (string) - Unique identification of the user on your side
         * @param $subscription_type (string)(optional) - Type of subscription (premium or select).
         *                                        Defaults to premium. Subject to ACL.
         * @return $result
         */
        function activate_subscription($user_id, $subscription_type = "premium"){

            //POST https://api.showmax.[com|io]/{partner_id}/partner/user/{id}/subscription

            $request_str = "";

            $log_data = array(
                            "user_id" => $user_id,
                            "subscription_type" => $subscription_type,
                            "_function" => "activate_subscription",
                );

            $defaultErrorResponse = $this->defaultResponseData;
             if (empty($user_id) || empty($subscription_type)){

                 // write to log and return FAIL result
                 $defaultErrorResponse["message"]  = "User Id or Subscription type is empty";
                 $this->check_fail_api($defaultErrorResponse, $log_data);
                 return $defaultErrorResponse;
             }

            $url_params = array();
            $post_params = array();
            if (!empty($user_id))
                $url_params["user"] = $user_id;

            if (in_array($subscription_type, $this->subscription_types))
                $post_params["subscription"] = $subscription_type;

            $request_params = $this->generate_string_with_parameters($url_params);
            $request_str .= $request_params . $this->url_subscription_postfix;

            $raw_response = $this->send_request("POST", $request_str, $post_params);
            $result = $this->api_response_handler($raw_response, $this->http_code_storage['activate_subscription']);
            $this->check_fail_api($result, $log_data);
            $result["additional_data"]["db_log"] = $this->api_db_log($result, $log_data);

            return $result;


            /*
            Data: activation_code (string) – Activation code, which should be delivered to the customer.
            Typically sequence of 12 digits. When OAuth flow is used, then activation_code is not returned.
            {"activation_code":"123456789012"}

            E.g.

            string(4) "POST"
            string(62) "https://api.showmax.io/prt000007za/partner/user/1/subscription"
            string(26) "{"subscription":"premium"}"
            string(38) "{"activation_code":"2792546428957554"}"


            check init function to research http code mapper for this method

            Actual response example:
            "{"activation_code":"7378858953639757"}"


               Error examples :
               "{"error":"msisdn, email are missing, exactly one parameter must be provided"}"

              Success example :
               "Not Found"


                Error exmaple :
               "{"error_code":"SUB1002","lang":"eng","message":"Subscription already active"}"



            // dummy response for tests

            //$test_api_response = "Empty response";
            //$test_api_response = array("error" => " custom error from ShowMax");
            $test_api_response = array("activation_code" => "");
            $response = array (

                "result"       => true,
                "message"      => "test message",
                "user_message" => "test user message",
                "api_response" => $test_api_response,
                "additional_data" => array (

                    "request_url" => "test request url",
                    "raw_response" => json_encode($test_api_response),
                    "http_code" => "409",
                )


            );

            */


        }

        /*
         * 5.3. Subscription Modification
            PUT https://api.showmax.[com|io]/{partner_id}/partner/user/{id}/subscription
         *  Allows partner to modify existing user subscription. Partner can suspend / ‘unsuspend’
         * the subscription for the user or change the subscription type.
         * The subscription suspension can either take place immediately, or at the end of the billing cycle.
         * Suspended subscription can be ‘unsuspended’ at any time. Only activated subscriptions
         * which haven’t been deactivated can be suspended.
            Changing the subscription type is immediate and no credit is generated, as opposed to
            Partners: Single Voucher Management.Changing the
            type of a suspended subscription does not ‘unsuspend’ it.

        suspended (boolean) – true to suspend current subscription, false to resume formerly suspended subscription.
        terminate_subscription (boolean) – Only effective when  suspended parameter = true.
                true to immediately terminate users subscription (revoke access to content).
        subscription_type (string) – Type of subscription  ( premium or select).
         *
         */

        // PUT https://api.showmax.[com|io]/{partner_id}/partner/user/{id}/subscription
        function modify_subscription($user_id, $suspended = null,
                                     $terminate_subscription = null,
                                     $subscription_type = null){

            $request_str = "";

            $log_data =  array(
                    "user_id" => $user_id,
                    "suspended" => $suspended,
                    "terminate_subscription" => $terminate_subscription,
                    "subscription_type" => $subscription_type,
                    "_function" => "modify_subscription",
                );

            $defaultErrorResponse = $this->defaultResponseData;
            if (empty($user_id)){

                // write to log and return FAIL result
                $defaultErrorResponse["message"]  = "User Id is empty";
                $this->check_fail_api($defaultErrorResponse, $log_data);
                return $defaultErrorResponse;
            }


            $url_params["user"] = $user_id;
            $put_params = array();

            // quick validation
            if (is_bool($suspended) === true)
                $put_params["suspended"] = $suspended;

            if (is_bool($terminate_subscription) === true)
                $put_params["terminate_subscription"] = $terminate_subscription;

            if (in_array($subscription_type, $this->subscription_types ))
                $put_params["subscription_type"] = $subscription_type;

            $request_params = $this->generate_string_with_parameters($url_params);
            $request_str .= $request_params . $this->url_subscription_postfix;

            // $put_params

            if (empty($put_params)){
                // write to log and return FAIL result
                $defaultErrorResponse["message"]  = "PUT parameters are empty. Possible error : [API error response : suspended, subscription_type are missing, at least one parameter must be provided]";
                $this->check_fail_api($defaultErrorResponse, $log_data);
                return $defaultErrorResponse;
            }


            $raw_response = $this->send_request("PUT", $request_str, $put_params);
            $result = $this->api_response_handler($raw_response, $this->http_code_storage["modify_subscription"]);
            $this->check_fail_api($result, $log_data);
            $result["additional_data"]["db_log"] = $this->api_db_log($result, $log_data);

            return $result;
        }
        /*

        Responses:

            200 – Subscription successfully deactivated. You will typically get 204, but please handle 200 as well.
            204 – Subscription successfully deactivated
            400 – Bad request - general problem with formatting of your request (e.g. not correctly escaped characters in URL)
            403 – Your are not allowed to access this resource (e.g. invalid values or combination of  access_token and partner_id)
            403 – Instrument not suspended when suspended set to false
            403 – Instrument not active
            403 – Instrument deactivated
            423 – User locked. Another billing process is running on given user, try again later
            404 – User with specified id doesn’t exist or doesn’t have an active subscription.
            405 – You are using other HTTP method than expected
            500 – Error on ShowMax side.

         */



        /*
            5.4. Subscription Deactivation

            This will deactivate subscription for the user. The subscription deactivation will only be performed
            at the end of the billing cycle. To gain access to the service again, activation endpoint must be called
            and user has to provide the activation code.
        Query params:
            partner_id (string) – Partner ID assigned to you by ShowMax.
            id (string) – Unique identification of the user on your side. It has to be equivalent to value
            returned by user details call during the OAuth2 flow.
        Opt. params: terminate_subscription (boolean) – true to immediately terminate users subscription
            (revoke access to content).

         */
        // DELETE https://api.showmax.[com|io]/{partner_id}/partner/user/{id}/subscription
        public function deactivate_subscription($user_id, $terminate_subscription = true){

            $log_data = array(
                                "user_id" => $user_id,
                                "terminate_subscription" => $terminate_subscription,
                                "_function" => "deactivate_subscription",
                        );
            $defaultErrorResponse = $this->defaultResponseData;
            if (empty($user_id)){

                // write to log and return FAIL result
                $defaultErrorResponse["message"]  = "User Id is empty";
                $this->check_fail_api($defaultErrorResponse, $log_data);
                return $defaultErrorResponse;
            }


            $request_str = "";
            $url_params["user"] = $user_id;

            $del_params = array();
            if (is_bool($terminate_subscription) === true)
                $del_params["terminate_subscription"] = $terminate_subscription;

            $request_params = $this->generate_string_with_parameters($url_params);
            $request_str .= $request_params . $this->url_subscription_postfix;

            $raw_response = $this->send_request("DELETE", $request_str, $del_params);
            $result = $this->api_response_handler($raw_response, $this->http_code_storage["deactivate_subscription"]);
            $this->check_fail_api($result, $log_data);
            $result["additional_data"]["db_log"] = $this->api_db_log($result, $log_data);

            return $result;

        }

        /*
         * 5.5. Redirect back to ShowMax
         * GET https://secure.showmax.[com|io]/{partner_id}/payment/subscriptions/partners/{partner_id}?activation_code={activation_code}
         *
         */



        /*
         * 5.6. Subscription Refund
         * POST https://api.showmax.[com|io]/{partner_id}/partner/user/{id}/subscription/refund
         */



        public function generate_string_with_parameters($request_params_array){

            $request_params_str = "";
            if (!empty($request_params_array)){
                foreach ($request_params_array as $param_key => $param_str)
                    if (!empty($param_str))
                        $request_params_str .= "/" . $param_key . "/" . $param_str;
            }
            //$param_length = strlen($request_params_str);
            //$request_params_str[$param_length - 1] = " ";
            //$request_params_str = trim($request_params_str);

            return $request_params_str;
        }



        // ---------------- // -------------- DB functions -------------------------------


        function api_db_log($response, $params){

            if (!$this->enable_db_log)
                return false;

            return $this->save_api_call_to_db($response, $params);
        }


        function save_api_call_to_db($response, $params){

            $params_str = "";
            foreach($params as $key=>$value)
                $params_str .= "[".$key."]=>".$value."\n";


            // map API response to DB table
            $http_mapper_message = $this->http_code_storage[$params["_function"]]["code_mapper"][$response["additional_data"]["http_code"]];
            $data_row = array(
                "action" => $params["_function"],
                "params" =>  substr($params_str, 0 , $this->db_restriction_params),   //  `params` TEXT(350) NULL,
                "response_result" =>  ($response["result"]) ? 1 : 0,                  //  TINYINT(1) NULL,
                "response_message" => substr($response["message"], 0, $this->db_restriction_response_message),
                "http_code" => $response["additional_data"]["http_code"],
                "http_code_message" => substr($http_mapper_message, 0, $this->db_restriction_http_code_message),
                "raw_response" => substr($response["additional_data"]["raw_response"], 0, $this->db_restriction_raw_response),
            );

            $result = $this->db->insert("showmax_api_calls", $data_row);
            $inserted_id = 0;
            if ($result)
                $inserted_id = $this->db->insert_id();

            return $inserted_id;
        }

        function load_api_call_from_db($params, $limit = 1, $start = 0,
                                       $order_by_key = array("order_key" => "id", "order_type" => "desc")){

            $this->db->select();
            if (!empty($params))
                foreach ($params as $row_key=>$row_value)
                    $this->db->where($row_key, $row_value);

            $this->db->order_by($order_by_key["order_key"], $order_by_key["order_type"]);
            $this->db->limit($limit, $start);
            $query = $this->db->get("showmax_api_calls");
            $result = $query->result();
            return $result;

        }


        function update_db_call($update_data, $where_data){

            if (empty($where_data) || empty($update_data))
                return false;

            foreach ($where_data as $row_key=>$row_value)
                $this->db->where($row_key, $row_value);

            $update_result = $this->db->update("showmax_api_calls", $update_data);
            return $update_result;
        }









        // ------------------- // ------------ LOG functions ------------------------------


        public function check_fail_api($api_result, $some_data = null){

            if ($api_result['result'] != false)
                return true;

            $date = date("Y-m-d H:i:s");
            $head = "\n\n\n ----- " . $date . " ----- ";
            $description = "\n showmax api fail \n ---------------";
            //$response_dump = "\n : Api result" . print_r($api_result, true);

            $additional_data = $api_result;
            $additional_data_text = "\n\n Full data : ";
            foreach ($additional_data as $key => $row){
                $additional_data_text .= "\n " . $key . " : " . print_r($row, true);
            }

            $some_data_text = "";
            if (!empty($some_data)){
                $some_data_text = "\n\n Additional data : ";
                foreach ($some_data as $key => $row){
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


        public function write_port_reset_log($str, $filename = "showmax-api-log.txt")
        {


            $log_file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $filename ;

            $log_handle = fopen($log_file_path, 'a+');
            fwrite($log_handle, "\n " . $str);
            fclose($log_handle);

        }




    }


