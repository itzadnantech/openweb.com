<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//$session_id = connect('administrator@fastadsl.co.za', md5('12359'));
//echo checkSession('6d460dcf22c8e7712813d7d578cb0c4f');
class Apihandler extends CI_Controller {

    function index(){
        // if (!isset($_POST['key-8jascC'])) {

        // add Some Key


        if (isset($_POST['function'])) {
            if ($_POST['function'] == 'start') {//7.1
                $userName = $_POST['username'];
                $password = $_POST['password'];
                $session_id = $this->start_connect($userName, $password);
                echo $session_id;
            }  else if ($_POST['function'] == 'start_stage') {//7.1(test server lte)
                $userName = $_POST['username'];
                $password = $_POST['password'];
                $session_id = $this->start_connect_stage($userName, $password);
                echo $session_id;
            } else if ($_POST['function'] == 'addRealm') {//7.4
                $class = $_POST['intClassID'];
                $user = $_POST['strUserName'];
                $pass = $_POST['strPassword'];
                $comment = $_POST['strComment'];
                $email = $_POST['strEmailAddress'];
                $sess = $_POST['strSessionID'];
                $resp = $this->add_realm($sess, $class, $user, $pass, $comment, $email);
                echo $resp;
            } else if ($_POST['function'] == 'classes') {//7.5
                $sess = $_POST['sess'];
                $realm = $_POST['realm'];
                $arrClass = $this->get_classes($sess, $realm);
                $arrClass = json_encode($arrClass);
                echo $arrClass;
            } else if ($_POST['function'] == 'deleteAccount') { //7.13
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $resp = $this->deleteAccount($sess, $userName);
                echo $resp;
            } else if ($_POST['function'] == 'setAccountPassword') {//7.8
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $value = $_POST['strValue'];
                //echo "Session: $sess $userName $value";
                $resp = $this->setAccountPassword($sess, $userName, $value);
                //echo "resp $resp";
                echo $resp;
            }else if($_POST['function'] == 'getYearlyStats'){//7.18  getYearlyStat
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $arrUsageStats = $this->get_yearly_stats($sess, $userName);
                $arrUsageStats = json_encode($arrUsageStats);
                echo $arrUsageStats;
            }else if($_POST['function'] == 'getMonthlyStats'){//7.19 getMonthlyStats
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $year = $_POST['intYear'];
                $month = $_POST['intMonth'];
                $arrUsageStats = $this->get_monthly_stats($sess, $userName, $year, $month);
                $arrUsageStats = json_encode($arrUsageStats);
                echo $arrUsageStats;
            }else if($_POST['function'] == 'getDailyStats'){//7.20 getDailyStats
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $year = $_POST['intYear'];
                $month = $_POST['intMonth'];
                $day = $_POST['intDay'];
                $arrUsageStats = $this->get_daily_stats($sess, $userName, $year, $month, $day);
                $arrUsageStats = json_encode($arrUsageStats);
                echo $arrUsageStats;
            }else if($_POST['function'] == 'getCurrentSessionInfo'){//7.49 getCurrentSessionInfo
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $arrSessionInfo = $this->get_current_session_info($sess, $userName);
                $arrSessionInfo = json_encode($arrSessionInfo);
                echo $arrSessionInfo;
            }else if ($_POST['function'] == 'setAccountClass'){//7.11 procedure : setAccountClass
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $class =  $_POST['intClassID'];
                $resp = $this->set_account_class($sess, $userName, $class);
                echo $resp;
            }else if($_POST['function'] == 'setPendingUpdate'){//7.15 procedure: setPendingUpdate
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $class =  $_POST['strClassID'];
                $resp = $this->set_pending_update($sess, $userName, $class);
                echo $resp;
            }else if($_POST['function'] == 'restoreAccount'){//7.14 procedure:restoreAccount
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $resp = $this->restore_account($sess, $userName);
                echo $resp;
            } else if ($_POST['function'] == 'setAccountClassNew'){//7.11 procedure : setAccountClass
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $class =  $_POST['intClassID'];
                $resp = $this->set_account_class_new($sess, $userName, $class);
                echo $resp;
            } else if($_POST['function'] == 'getAccountInfo'){
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $resp = $this->get_account_info($sess, $userName);
                echo $resp;
            } else if ($_POST['function'] == 'getAccountInfoFull'){
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $resp = $this->get_account_info_full($sess, $userName);
                echo json_encode($resp);
            } else if ($_POST['function'] == 'setPendingUpdateJSON'){//7.15 procedure: setPendingUpdate
                $sess = $_POST['strSessionID'];
                $userName = $_POST['strUserName'];
                $class =  $_POST['strClassID'];
                $resp = $this->set_pending_update($sess, $userName, $class);
                echo json_encode($resp);

            } else  {

                switch ($_POST['function']){

                    // 7.44 getAvailableTopUpClasses
                    case "getAvailableTopUpClasses" : {

                        $sess = $_POST['strSessionID'];
                        $resp = $this->get_available_top_up_classes($sess);
                        echo json_encode($resp);
                        break;
                    }

                    // 7.45 Procedure: queueTopUp
                    case "queueTopUp" : {


                        $sess = $_POST['strSessionID'];
                        $username = $_POST['strUserName'];
                        $topup_class_id = $_POST['topupClassID'];

                        $resp = $this->queue_top_up($sess, $username, $topup_class_id);
                        echo json_encode($resp);
                        break;

                    }

                    case "queueTopUp_stage" : {


                        $sess = $_POST['strSessionID'];
                        $username = $_POST['strUserName'];
                        $topup_class_id = $_POST['topupClassID'];

                        $resp = $this->queue_top_up_stage($sess, $username, $topup_class_id);
                        echo json_encode($resp);
                        break;

                    }

                    // 7.46 Procedure: cancelQueuedTopUp
                    case "cancelQueuedTopUp" : {

                        $sess = $_POST['strSessionID'];
                        $topup_id = $_POST['intTopUpID'];

                        $resp = $this->cancel_queued_top_up($sess, $topup_id);
                        echo json_encode($resp);
                        break;

                    }

                    case "setAccountComment" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $comment = $_POST['strValue'];
                        $resp = $this->setAccountComment($sess, $userName, $comment);
                        echo json_encode($resp);
                        break;
                    }

                    // 7.47 Procedure: getTopUpsPerUser
                    case "getTopUpsPerUser" : {

                        $sess = $_POST['strSessionID'];
                        $username = $_POST['strUserName'];

                        $resp = $this->get_top_ups_per_user($sess, $username);
                        echo json_encode($resp);
                        break;

                    }

                    // 7.17
                    case "getPendingUpdate" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];

                        $resp = $this->get_pending_update($sess, $userName);
                        echo json_encode($resp);
                        break;

                    }


                    // ----------------------------------------------------

                    case "startFull" : {
                        $userName = $_POST['username'];
                        $password = $_POST['password'];
                        $continue = $_POST['continue'];
                        $start_response = $this->start_connect_full($userName, $password, $continue);
                        echo json_encode($start_response);
                        break;
                    }

                    case "addRealmFull" : {

                        $class   = $_POST['intClassID'];
                        $user    = $_POST['strUserName'];
                        $pass    = $_POST['strPassword'];
                        $comment = $_POST['strComment'];
                        $email   = $_POST['strEmailAddress'];
                        $sess    = $_POST['strSessionID'];
                        $resp    = $this->add_realm_full($sess, $class, $user, $pass, $comment, $email);
                        echo json_encode($resp);
                        break;

                    }

                    // 7.12 Procedure: suspendAccount
                    case "suspendAccount" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $resp = $this->suspend_account($sess, $userName);
                        echo $resp;
                        break;
                    }

                    // 7.12 Procedure: suspendAccount
                    case "suspendAccountFull" : {
                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $resp = $this->suspend_account_full($sess, $userName);
                        echo json_encode($resp);
                        break;

                    }

                    case "restoreAccountFull" : {
                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $resp = $this->restore_account_full($sess, $userName);
                        echo json_encode($resp);
                        break;
                    }


                    // 7.8 Procedure: setAccountPassword
                    case "setAccountPasswordFull" : {
                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $value = $_POST['strValue'];
                        //echo "Session: $sess $userName $value";
                        $resp = $this->setAccountPasswordFull($sess, $userName, $value);
                        //echo "resp $resp";
                        echo json_encode($resp);
                        break;

                     }

                    case "getCurrentSessionInfoFull" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $resp = $this->get_current_session_info_full($sess, $userName);
                        echo json_encode($resp);
                        break;
                    }

                     case "getYearlyStatsFull" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $result = $this->get_yearly_stats_full($sess, $userName);
                        echo json_encode($result);
                        break;
                     }

                     case "getMonthlyStatsFull" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $year = $_POST['intYear'];
                        $month = $_POST['intMonth'];
                        $result = $this->get_monthly_stats_full($sess, $userName, $year, $month);
                        echo json_encode($result);
                        break;
                     }

                    case "getMonthlyStatsFullStage" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $year = $_POST['intYear'];
                        $month = $_POST['intMonth'];
                        $result = $this->get_monthly_stats_full_stage($sess, $userName, $year, $month);
                        echo json_encode($result);
                        break;
                    }

                    case "addLTEAccount" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $password = $_POST['Password'];
                        $realm = $_POST['Realm'];
                        $class_id = $_POST['class_id'];
                        $name = $_POST['ContactName'];
                        $id = $_POST['ContactIDNo'];
                        $cell = $_POST['ContactCell'];
                        $day = $_POST['ContactDay'];
                        $email = $_POST['ContactEmail'];
                        $adrType = $_POST['AddrType'];
                        $adrStr = $_POST['AddrStreet'];
                        $suburb = $_POST['AddrSuburb'];
                        $city = $_POST['AddrCity'];
                        $postalCode = $_POST['AddrPostalCode'];
                        $ricaAdr = $_POST['RicaAddress'];
                        $dev = $_POST['DevType'];

                        $result = $this->add_lte_account($sess, $userName, $password, $realm, $class_id, $name, $id, $cell,
                            $day, $email, $adrType, $adrStr, $suburb, $city, $postalCode, $ricaAdr, $dev);
                        echo json_encode($result);
                        break;
                    }

                    case "getAccountUsage" : {

                        $sess = $_POST['strSessionID'];
                        $product = $_POST['strProduct'];
                        $usageType = $_POST['strUsageType'];
                        $query = $_POST['strQuery'];
                        $userList = $_POST['strUserlist'];
                        $result = $this->get_account_usage($sess, $product, $usageType, $query, $userList);
                        echo json_encode($result);
                        break;
                    }

                     case "getDailyStatsFull" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $year = $_POST['intYear'];
                        $month = $_POST['intMonth'];
                        $day = $_POST['intDay'];
                        $result = $this->get_daily_stats_full($sess, $userName, $year, $month, $day);
                        echo json_encode($result);
                        break;
                     }

                     case "deletePendingUpdate" : {

                        $sess = $_POST['strSessionID'];
                        $userName = $_POST['strUserName'];
                        $resp = $this->deletePendingUpdate($sess, $userName);
                        echo $resp;

                        break;
                    }
                    //for Stage IS server testing
                    case "classes_stage" : {
                        $sess = $_POST['sess'];
                        $arrClass = $this->get_classes_stage($sess);
                        $arrClass = json_encode($arrClass);
                        echo $arrClass;
                        break;
                    }

                    case "add_user_stage" : {
                        $class = $_POST['intClassID'];
                        $user = $_POST['strUserName'];
                        $pass = $_POST['strPassword'];
                        $comment = $_POST['strComment'];
                        $email = $_POST['strEmailAddress'];
                        $sess = $_POST['strSessionID'];
                        $resp = $this->add_realm_stage($sess, $class, $user, $pass, $comment, $email);
                        echo $resp;
                        break;
                    }

                    //5.2.75  provisionLTEAccount
                    case "provisionLTEAccount" : {
                        $class = $_POST['intClassID'];
                        $user = $_POST['strUserName'];
                        $pass = $_POST['strPassword'];
                        $comment = $_POST['strComment'];
                        $email = $_POST['strEmailAddress'];
                        $sess = $_POST['strSessionID'];
                        $sim = $_POST['strICCID'];
                        $resp = $this->provisionLTEAccount($sess, $user, $pass, $email, $class, $sim, $comment);
                        echo $resp;
                        break;
                    }

                    //5.2.6. getAccountUserNames
                    case "getAccountUserNames": {
                        $sess = $_POST['sess'];
                        $resp = $this->getLTEUsers($sess);
                        echo $resp;
                        break;
                    }

                    //3.23 ownSimSwap
                    case "ownSimSwap": {
                        $resp = $this->simSwap($_POST);
                        echo $resp;
                        break;
                    }

                    //3.20 ownSimSwap
                    case "accountMap": {
                        $resp = $this->accountMap($_POST);
                        echo $resp;
                        break;
                    }
                }


            }
        }


    }

    function deletePendingUpdate($session_id, $user_name){

        $client = $this->create_client();
        $is_param = array(
            'strSessionID'  => $session_id,
            'strUserName' => $user_name,
        );
        $resp = $client->__call("deletePendingUpdate",$is_param);
        return $resp;

    }


    // ----------------------------- // -----------------------------------



    function get_pending_update($sess, $userName){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
        );

        $resp = $client->__call("getPendingUpdate", $is_param);

        return $resp;
        /*
            if($resp['intReturnCode'] == 1){
                return $resp['arrUsageStats'];
            }

             return $resp['intReturnCode'];
        */

    }

    // ------------------------------- ~~~~~~~~~~~ ------------------

    function get_top_ups_per_user($sess, $username){

        $client = $this->create_client();
        $is_param = array(

            'strSessionID' => $sess,
            'strUserName'   => $username,

        );
        $resp = $client->__call("getTopUpsPerUser", $is_param);
        return $resp;

    }

    function cancel_queued_top_up($sess, $topup_id){

        $client = $this->create_client();
        $is_param = array(

            'strSessionID' => $sess,
            'intTopUpID'   => $topup_id,

        );
        $resp = $client->__call("cancelQueuedTopUp", $is_param);
        return $resp;

    }


    function queue_top_up($sess, $username, $topup_class_id){

        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'topupClassID' => $topup_class_id,

        );
        $resp = $client->__call("queueTopUp", $is_param);
        return $resp;

    }

    function queue_top_up_stage($sess, $username, $topup_class_id){

        $client = $this->create_client_stage();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'topupClassID' => $topup_class_id,

        );
        $resp = $client->__call("queueTopUp", $is_param);
        return $resp;

    }

    // sends full response
    function setAccountComment($session_id, $user_name, $comment){

        $client = $this->create_client();
        $is_param = array(
            'strSessionID'  => $session_id,
            'strUserName' => $user_name,
            'strValue'     => $comment,
        );
        $resp = $client->__call("setAccountComment",$is_param);
        return $resp;

    }



    function get_available_top_up_classes($sess){

        $client = $this->create_client();
        $is_param = array(
            'strSessionID'    =>    $sess,

        );
        $resp = $client->__call("getAvailableTopUpClasses", $is_param);
        return $resp;

    }


    // ============================= topUp ======================================

    function get_account_info($sess,$username){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'    =>    $sess,
            'strUserName' => $username,
        );
        $resp = $client->__call("getAccountInfo", $is_param);
        return $resp['intReturnCode'];
    }

    function get_account_info_full($sess,$username){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'    =>    $sess,
            'strUserName' => $username,
        );
        $resp = $client->__call("getAccountInfo", $is_param);
        return $resp;
    }


    function setAccountPassword($session_id, $userName, $strValue) {
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'	=> $session_id,
            'strUserName' => $userName,
            'strValue' => $strValue,
        );
        $resp = $client->__call("setAccountPassword", $is_param);
        return $resp['intReturnCode'];
    }


    function setAccountPasswordFull($session_id, $userName, $strValue) {
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'  => $session_id,
            'strUserName' => $userName,
            'strValue' => $strValue,
        );
        $resp = $client->__call("setAccountPassword", $is_param);
        return $resp;
    }

    function deleteAccount($session_id, $userName) {
        $client = $this->create_client();

        $is_param = array(
            'strSessionID' => $session_id,
            'strUserName' => $userName,
        );
        $resp = $client->__call("deleteAccount", $is_param);
       // return $resp['intReturnCode'];
        return $resp;
    }

    function get_classes($sess, $realm = null) {
        $client = $this->create_client();
        $is_param = array (
            'strSessionID' => $sess,
        );

        if (!empty($realm)) {
            $is_param['strRealm'] = $realm;
        }

        $resp = $client->__call("getClass", $is_param);
        return $resp['arrClass'];
    }

    function get_classes_stage($sess) {
        $client = $this->create_test_client();
        $is_param = array (
            'strSessionID' => $sess,
        );
        $resp = $client->__call("getClass", $is_param);
        return $resp['arrClass'];
    }

    function add_realm($sess, $class, $user, $pass, $comment, $email) {
        $client = $this->create_client();

        $is_param = array(
            'strSessionID'    =>    $sess,
            'intClassID'    =>    $class,
            'strUserName' => $user,
            'strPassword' => $pass,
            'strComment' => $comment,
            'strEmailAddress' => $email,
        );

        $resp = $client->__call("addRealmAccount", $is_param);
        //  return $resp['intReturnCode'];
        return $resp;
    }

    function add_realm_stage($sess, $class, $user, $pass, $comment, $email) {
        $client = $this->create_test_client();

        $is_param = array(
            'strSessionID'    =>    $sess,
            'intClassID'    =>    $class,
            'strUserName' => $user,
            'strPassword' => $pass,
            'strComment' => $comment,
            'strEmailAddress' => $email,
        );

        $resp = $client->__call("addRealmAccount", $is_param);
        //  return $resp['intReturnCode'];
        return $resp;
    }

    function provisionLTEAccount($sess, $user, $pass, $email, $class, $sim, $comment) {
        $client = $this->create_client();

        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $user,
            'strPassword' => $pass,
            'strEmailAddress' => $email,
            'intClassID' => $class,
            'strICCID' => $sim,
            'strComment' => $comment
        );

        $resp = $client->__call("provisionLTEAccount", $is_param);

        return json_encode($resp);
    }

    function create_client() {
        $options = array('socket' => array('bindto' => API_BINDTO));
        $context = stream_context_create($options);
        $url = API_URL;
        $client = new SoapClient($url,array('trace' => 1, 'exception' => 0, 'stream_context' => $context));
        return $client;
    }

    function create_test_client() {
        $options = array('socket' => array('bindto' => API_BINDTO));
        $context = stream_context_create($options);
        $url = API_ST_URL;
        $client = new SoapClient($url,array('trace' => 1, 'exception' => 0, 'stream_context' => $context));
        return $client;
    }


    function start_connect($userName, $password) {

//        if($userName == 'api@openwebmobile.co.za') {
//            $client = $this->create_test_client();
//        } else {
            $client = $this->create_client();
//        }

        $is_param = array(
            'strUserName'    =>    $userName,
            'strPassword'    =>    $password,
            'blnContinue' => true,
        );
        $resp = $client->__call("startSession", $is_param);

        if ($resp['intReturnCode'] == 1) {
            $session_id = $resp['strSessionID'];
            return $session_id;
        } else {
            return $resp['intReturnCode'];
        }

    }

    function start_connect_stage($userName, $pass) {

        $client = $this->create_client_stage();

        $is_param = array(
            'strUserName'    =>    $userName,
            'strPassword'    =>    $pass,
            'blnContinue' => true,
        );

        $resp = $client->__call("startSession", $is_param);

        if ($resp['intReturnCode'] == 1) {
            $session_id = $resp['strSessionID'];
            return $session_id;
        } else {
            return $resp['intReturnCode'];
        }
    }

    function create_client_stage() {
        $options = array('socket' => array('bindto' => API_BINDTO));
        $context = stream_context_create($options);
        $url = "https://soap.isdsl.net/api/api.php?wsdl";
        $client = new SoapClient($url,array('trace' => 1, 'exception' => 0, 'stream_context' => $context, 'soap_version' => SOAP_1_1,
            'features' => SOAP_USE_XSI_ARRAY_TYPE,
            'encoding'     => 'ISO-8859-1'));
        return $client;
    }


    function checkSession($session_id) {
        $client = $this->create_client();
        $resp = $client->__call("checkSession", array('strSessionID' => $session_id));
        if ($resp == 1) {
            return true;
        }
        return false;
    }

    function get_yearly_stats($sess, $userName){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
        );

        $resp = $client->__call("getYearlyStats", $is_param);

        if($resp['intReturnCode'] == 1){
            return $resp['arrUsageStats'];
        }else{
            return $resp['intReturnCode'];
        }
    }


     function get_yearly_stats_full($sess, $userName){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
        );

        $resp = $client->__call("getYearlyStats", $is_param);
        return $resp;
    }

    function get_monthly_stats($sess, $userName ,$year ,$month){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
            'intYear' => $year,
            'intMonth' => $month,
        );

        $resp = $client->__call("getMonthlyStats", $is_param);
        if($resp['intReturnCode'] == 1){
            return $resp['arrUsageStats'];
        }else{
            return $resp['intReturnCode'];
        }
    }

    function get_monthly_stats_full($sess, $userName ,$year ,$month){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
            'intYear' => $year,
            'intMonth' => $month,
        );

        $resp = $client->__call("getMonthlyStats", $is_param);
        return $resp;
    }

    function get_monthly_stats_full_stage($sess, $userName ,$year ,$month){
        $client = $this->create_test_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
            'intYear' => $year,
            'intMonth' => $month,
        );

        $resp = $client->__call("getMonthlyStats", $is_param);
        return $resp;
    }

    function get_daily_stats($sess, $userName ,$year ,$month, $day){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
            'intYear' => $year,
            'intMonth' => $month,
            'intDay' => $day,
        );

        $resp = $client->__call("getDailyStats", $is_param);
        if($resp['intReturnCode'] == 1){
            return $resp['arrUsageStats'];
        }else{
            return $resp['intReturnCode'];
        }
    }

    function get_daily_stats_full($sess, $userName ,$year ,$month, $day){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
            'intYear' => $year,
            'intMonth' => $month,
            'intDay' => $day,
        );

        $resp = $client->__call("getDailyStats", $is_param);
        return $resp;
    }


    function get_current_session_info($sess, $userName){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
        );

        $resp = $client->__call("getCurrentSessionInfo", $is_param);
        if($resp['intReturnCode'] == 1){
            return $resp['arrSessionInfo'];
        }else{
            return $resp['intReturnCode'];
        }
    }


     function get_current_session_info_full($sess, $userName){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $userName,
        );

        $resp = $client->__call("getCurrentSessionInfo", $is_param);
        return $resp;

    }

    function set_account_class_new($sess, $username, $class){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'  =>	$sess,
            'strUserName' 	=>	$username,
            'intClassID'    =>	$class,
        );

        $resp = $client->__call("setAccountClass", $is_param);
        return $resp;
    }

    function set_account_class($sess, $username, $class){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'  =>	$sess,
            'strUserName' 	=>	$username,
            'intClassID'    =>	$class,
        );

        $resp = $client->__call("setAccountClass", $is_param);
        return $resp['intReturnCode'];
    }

    function set_pending_update($sess, $username, $class){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID'    =>    $sess,
            'strUserName' => $username,
            'strClassID'    =>    $class,

        );

        $resp = $client->__call("setPendingUpdate", $is_param);
        // return $resp['intReturnCode'];
        return $resp;
    }

    function restore_account($sess, $username){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $username,
        );

        $resp = $client->__call("restoreAccount", $is_param);
        return $resp['intReturnCode'];
    }

   function restore_account_full($sess, $username){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $username,
        );

        $resp = $client->__call("restoreAccount", $is_param);
        return $resp;
    }


    function suspend_account($sess, $username){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $username,
        );

        $resp = $client->__call("suspendAccount", $is_param);
        return $resp['intReturnCode'];

        /*
             1 = Success: ADSL account suspended OUT Parameters intReturnCode
             2 = Username does not exist
             5 = Failure: Invalid session identifier supplied
             7 = Failure: ADSL account does not belong to your organisation
         */
    }


    function suspend_account_full($sess, $username){
        $client = $this->create_client();
        $is_param = array(
            'strSessionID' => $sess,
            'strUserName' => $username,
        );

        $resp = $client->__call("suspendAccount", $is_param);
        return $resp;

        /*
             1 = Success: ADSL account suspended OUT Parameters intReturnCode
             2 = Username does not exist
             5 = Failure: Invalid session identifier supplied
             7 = Failure: ADSL account does not belong to your organisation
         */
    }

    // ---------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------
    // custom functions


    function create_custom_client() {

        ini_set('default_socket_timeout', 60);

        $options = array('socket' => array('bindto' => "195.154.54.150:0"));
        $context = stream_context_create($options);
        $url = API_URL;
        $client = new SoapClient($url, array('trace' => 1, 'exception' => 0, 'stream_context' => $context,
                                        'connection_timeout' => 15));
        return $client;
    }


    function start_connect_full($userName, $password, $continue = true) {
        $client = $this->create_custom_client();

        $is_param = array(
            'strUserName'    =>    $userName,
            'strPassword'    =>    $password,
            'blnContinue'    =>    $continue,  // True or False

        );
        try {
            $resp = $client->__call("startSession", $is_param);
        } catch (Exception $exp){
            $resp = $exp;
            unset($client);
            // try to reconnect with 'FALSE' parameter
            //if ($continue){
            //    $resp = start_connect_full($userName, $password, false);
            //}
        }
        return $resp;

    }

    function add_realm_full($sess, $class, $user, $pass, $comment, $email) {
        $client = $this->create_custom_client();

        $is_param = array(
            'strSessionID'    => $sess,
            'intClassID'      => $class,
            'strUserName'     => $user,
            'strPassword'     => $pass,
            'strComment'      => $comment,
            'strEmailAddress' => $email,
        );

        try {
            $resp = $client->__call("addRealmAccount", $is_param);
        } catch (Exception $exp){
            $resp = $exp;
        }
        return $resp;
    }

    function add_lte_account($sess, $userName, $password, $realm, $class_id, $name, $id, $cell,
                             $day, $email, $adrType, $adrStr, $suburb, $city, $postalCode, $ricaAdr, $dev) {

        $client = $this->create_client_stage();

        $is_param = [
            'strSessionID'    => $sess,
            'arrInputs' =>
            [
                ['Key' => 'UserName', 'Value' => "newtestuser"],
                ['Key' => 'Realm', 'Value' => $realm],
                ['Key' => 'Password', 'Value' => $password],
                ['Key' => 'class_id', 'Value' => $class_id],
                ['Key' => 'ContactName', 'Value' => $name],
                ['Key' => 'ContactIDNo', 'Value' => "12345678999"],
                ['Key' => 'ContactCell', 'Value' => "353535"],
                ['Key' => 'ContactDay', 'Value' => $day],
                ['Key' => 'ContactEmail', 'Value' => $email],
                ['Key' => 'AddrType', 'Value' => $adrType],
                ['Key' => 'AddrStreet', 'Value' => $adrStr],
                ['Key' => 'AddrSuburb', 'Value' => $suburb],
                ['Key' => 'AddrCity', 'Value' => $city],
                ['Key' => 'AddrPostalCode', 'Value' => $postalCode],
                ['Key' => 'RicaAddress', 'Value' => $ricaAdr],
                ['Key' => 'DevType', 'Value' => $dev]
            ]
        ];

        $resp = $client->__call("addLTEAccount", $is_param);
        return $resp;
    }

    function get_account_usage($sess, $product, $usageType, $query, $userList) {

        $client = $this->create_test_client();

        $is_param = [
            'strSessionID' => $sess,
            'strProduct' => $product,
            'strUsageType' => $usageType,
            'strQuery' => $query,
            'strUserlist' => $userList
        ];

        $resp = $client->__call("getAccountUsage", $is_param);
        
        return $resp;
    }

    function getLTEUsers($sess) {

        $client = $this->create_client();

        $is_param = [
            'strSessionID' => $sess,
        ];

        $resp = $client->__call("getAccountUserNames", $is_param);

        return json_encode($resp);
    }

    function simSwap($data) {
        $this->log($data);
        $url = API_REST_URL.'/lte/ownSimSwap.php';
        $auth_data = json_decode($data['realm_data'], true);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Content-Type: application/json',
                'Authorization: Basic '. base64_encode($auth_data['user'] . ":" . $auth_data['pass'])
            ]);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);

        unset($data['realm_data']);
        unset($data['function']);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);

        curl_close($ch);

        $this->log($body);

        return $body;
    }

    function accountMap($data) {
        $this->log($data);
        $url = API_REST_URL.'/lte/accountMap.php?username='.$data['username'];
        $auth_data = json_decode($data['realm_data'], true);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Content-Type: application/json',
                'Authorization: Basic '. base64_encode($auth_data['user'] . ":" . $auth_data['pass'])
            ]);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);

        curl_close($ch);

        $this->log($body);

        return $body;
    }

    function log($data) {
        $this->db->insert('api_log', ['data'=> json_encode($data)]);
    }
}

