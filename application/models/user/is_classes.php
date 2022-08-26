<?php

class Is_classes extends CI_Model {

    private $apiUrl = "http://home.openweb.co.za/apihandler";
	
	function update_classes() {
		// First get the realms
		$this->db->truncate('is_classes');
		$query = $this->db->get('realms');
		$result = $query->result_array();
		if (!empty($result)) {
			foreach ($result as $result) {
				$realm = $result['realm'];
				$user = "{$result['user']}@{$realm}";
				$pass = $result['pass'];
				$sess = $this->is_connect($user, $pass);
				$class_list = $this->get_classes($sess);
				$class_list = json_decode($class_list);
				if (!empty($class_list)) {
					foreach ($class_list as $class) {
						$data = array (
							'id' => $class->ID,
							'desc' => $class->Desc,
							'realm' => $realm,
						);
						$this->db->insert('is_classes', $data); 
					}
				}
			}
		}
	}
	//7.1 procedure: startSession
	function is_connect($userName, $password) {
		$data = array(
				'function' => 'start',
				'username' => $userName,
				'password' => $password);
		$url = 'http://home.openweb.co.za/server.php';
	
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	//7.4 procedure: addRealmAccount
	function add_realm($sess, $class, $user, $pass, $comment, $email) {
		$data = array(
				'function' => 'addRealm',
				'strSessionID'    =>	$sess,
				'intClassID'    =>    $class,
				'strUserName' => $user,
				'strPassword' => $pass,
				'strComment' => $comment,
				'strEmailAddress' => $email,
		);
		$url = 'http://home.openweb.co.za/server.php';
	
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	//7.5 procedure: getClass
	function get_classes($sess) {
		$data = array(
			'function' => 'classes',
			'sess' => $sess, 
		);
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	//7.1 procedure: startSession
	/*
	function is_connect($userName, $password) {
		$data = array(
			'function' => 'start',
			'username' => $userName, 
			'password' => $password,
		);
		
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;  //get session_id
	}*/
	
	//7.4 procedure: addRealmAccount
	/*
	function add_realm($sess, $class, $user, $pass, $comment, $email) {
		$data = array(
			'function' => 'addRealm',
			'strSessionID'    =>	$sess, 
			'intClassID'    =>    $class,
			'strUserName' => $user,
			'strPassword' => $pass,
			'strComment' => $comment,
			'strEmailAddress' => $email,
		);

		$url = 'http://home.openweb.co.za/server.php';

		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	} */

	
	function is_setAccountPassword($data) {
		$data['function'] = 'setAccountPassword';
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}

	//7.13 procedure:deleteAccount
	function delete_account($sess, $strUserName) {
		$data = array (
			'function' => 'deleteAccount',
			'strSessionID' => $sess,
			'strUserName' => $strUserName,
		);
		$url = 'http://home.openweb.co.za/server.php';

		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}


		//7.14 procedure:restoreAccount
	function restore_account($sess, $strUserName){
		$data = array(
			'function'     => 'restoreAccount',
			'strSessionID' => $sess,
			'strUserName'  => $strUserName,
		);
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	


	 // do not use it, this function only inserts classes
    function update_classes_new() {
        // First get the realms
        $this->db->truncate('is_classes');
        $query = $this->db->get('realms');
        $result = $query->result_array();
        if (!empty($result)) {
            foreach ($result as $result) {
                $realm = $result['realm'];
                $user = "{$result['user']}@{$realm}";
                $pass = $result['pass'];
                $sess = $this->is_connect_new($user, $pass);
                $class_list = $this->get_classes_new($sess);
                if (!empty($class_list)) {
                    foreach ($class_list as $class) {
                        $data = array (
                            'id' => $class->ID,
                            'desc' => $class->Desc,
                            'realm' => $realm,
                        );
                        $this->db->insert('is_classes', $data);
                    }
                }
            }
        }
    }

	       //7.1 procedure: startSession
    function is_connect_new($userName, $password) {
        $data = array(
            'function' => 'start',
            'username' => $userName,
            'password' => $password);
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }

    function is_connect_stage($userName, $password) {
        $data = array(
            'function' => 'start_stage',
            'username' => $userName,
            'password' => $password);
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }

    //7.4 procedure: addRealmAccount
    function add_realm_new($sess, $class, $user, $pass, $comment, $email) {
        $data = array(
            'function' => 'addRealm',
            'strSessionID'    =>	$sess,
            'intClassID'    =>    $class,
            'strUserName' => $user,
            'strPassword' => $pass,
            'strComment' => $comment,
            'strEmailAddress' => $email,
        );

        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }


    //7.5 procedure: getClass !needs json_decode for result
    function get_classes_new($sess) {
        $data = array(
            'function' => 'classes',
            'sess' => $sess,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }

    function get_classes_stage($sess) {

        $data = array(
            'function' => 'classes_stage',
            'sess' => $sess,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        var_dump($resp);die();
        return json_decode($resp, true);
    }



 //7.8 procedure: setAccountPassword
    function is_setAccountPassword_new($data) {
        $data['function'] = 'setAccountPassword';
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }

	   // API can respond with empty answer if action succeeded
    function is_setAccountPassword_new_full($data) {
        $data['function'] = 'setAccountPassword';
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
   }


    //7.13 procedure:deleteAccount
    function delete_account_new($sess, $strUserName) {
        $data = array (
            'function' => 'deleteAccount',
            'strSessionID' => $sess,
            'strUserName' => $strUserName,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }





	    //7.14 procedure:restoreAccount
    function restore_account_new($sess, $strUserName){
        $data = array(
            'function'     => 'restoreAccount',
            'strSessionID' => $sess,
            'strUserName'  => $strUserName,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }


	     //7.14 procedure:restoreAccount
    function restore_account_new_full($sess, $strUserName){
        $data = array(
            'function'     => 'restoreAccountFull',
            'strSessionID' => $sess,
            'strUserName'  => $strUserName,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp);
    }

	   // 7.12 Procedure: suspendAccount
    function suspend_account_new($sess, $strUserName){

        $data = array(
            "function"     => "suspendAccount",
            "strSessionID" => $sess,
            "strUserName"  => $strUserName,
        );
        $url = "http://home.openweb.co.za/apihandler";

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;

        // suspendAccount
    }


	    // 7.12 Procedure: suspendAccount
    function suspend_account_new_full($sess, $strUserName){

        $data = array(
            "function"     => "suspendAccountFull",
            "strSessionID" => $sess,
            "strUserName"  => $strUserName,
        );
        $url = "http://home.openweb.co.za/apihandler";

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp);

        // suspendAccount
    }
	
	//7.7 Procedure: getAccountInfo
 	function getAccountInfo($sess,$username){
		$data = array(
			'function' => 'getAccountInfo',
			'strSessionID'  =>	$sess,
			'strUserName'   =>  $username,
		);
		//$url = 'http://home.openweb.co.za/server.php';
		$url = 'http://home.openweb.co.za/server.php';
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	} 

	
	//7.11 procedure : setAccountClass
	function set_account_class($sess, $username, $class){
		$data = array(
				'function' 		=> 	'setAccountClass',
				'strSessionID'  =>	$sess,
				'intClassID'    =>  $class,
				'strUserName'   =>  $username,
		);
		$url = 'http://home.openweb.co.za/server.php';
	
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	
	

	//7.15 procedure: setPendingUpdate
	function set_pending_update($sess, $username, $class){
		$data = array(
				'function' 		=> 	'setPendingUpdate',
				'strSessionID'  =>	$sess,
				'strClassID'    =>  $class,
				'strUserName'   =>  $username,
		);
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}



	
	//7.18 procedure: getYearlyStats
	function get_yearly_stats($sess, $username){
		$data = array(
			'function' => 'getYearlyStats',		
			'strSessionID' => $sess,
			'strUserName'  => $username,
		);
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	} 
	
	//7.19 procedure: getMonthlyStats
	function get_monthly_stats($sess, $username, $year, $month){
		$data = array(
			'function' => 'getMonthlyStats',
			'strSessionID' => $sess,
			'strUserName'  => $username,
			'intYear' => $year,
			'intMonth' => $month,		
		);
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	
	//7.20 procedure: getDailyStats
	function get_daily_stats($sess, $username, $year, $month, $day){
		$data = array(
				'function' => 'getDailyStats',
				'strSessionID' => $sess,
				'strUserName'  => $username,
				'intYear' => $year,
				'intMonth' => $month,
				'intDay' => $day,
		);
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}
	
	//7.49 procedure getCurrentSessionInfo
	function get_current_session_info($sess,$username){
		$data = array(
			'function' => 'getCurrentSessionInfo',
			'strSessionID' => $sess,
			'strUserName'  => $username,	
		);
		
		$url = 'http://home.openweb.co.za/server.php';
		
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($handle);
		curl_close($handle);
		return $resp;
	}




    //7.1 procedure: startSession
    function is_connect_new_full($userName, $password, $continue = true, $retries = 20) {

        set_time_limit(0);

        $data = array(
            'function' => 'startFull',
            'username' => $userName,
            'password' => $password,
            'continue' => $continue,
        );

        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($handle, CURLOPT_TIMEOUT, 20); //timeout in seconds

        $resp = curl_exec($handle);
        curl_close($handle);

        $resp = json_decode($resp);
        if (    isset($resp->faultstring)
                && (strpos(strtolower($resp->faultstring),'timeout') !== false)
                && ($retries > 0) ){
            //echo "retries : " . $retries . "<br/>";

            if ( ($retries < 11) && ($continue == true))
                $continue = false;
            $resp = $this->is_connect_new_full($userName, $password, $continue, $retries - 1);
        }

        return $resp;
    }

/*
    function is_connect_new_curl($userName, $password, $continue = true, $retries = 20){

        set_time_limit(0);
        $soapUrl = "http://www.isdsl.net/api/api.php/startSession"; // asmx URL of WSDL

        // xml post structure
/*
        $xml_post_string = '<?xml version="1.0"?>
                                <soap:Envelope  xmlns:soap="http://www.w3.org/2003/05/soap-envelope/"
                                                soap:encodingStyle="http://www.w3.org/2003/05/soap-encoding">
                                        <soap:Body>
                                        <message name="startSessionRequest">
                                            <part name="strUserName" type="xsd:string"/>
                                            <part name="strPassword" type="xsd:string"/>
                                            <part name="blnContinue" type="xsd:boolean"/>
                                        </message>
                                        </soap:Body>
                                </soap:Envelope>';   // data from the form, e.g. some ID number
*/
    /*
        $xml_post_string = '<?xml version="1.0"?>'.
                            '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/"'.
                                            ' soap:encodingStyle="http://www.w3.org/2003/05/soap-encoding">' .
                                '<soap:Body xmlns:m="http://www.example.org/stock">' .
                                  '<m:startSession>' .
                                    '<m:strUserName>'. $userName. '</m:strUserName>' .
                                    '<m:strPassword>'. $password .'</m:strPassword>' .
                                    '<m:blnContinue>'. $continue .'</m:blnContinue>' .
                                  '</m:startSession>' .
                                '</soap:Body>' .
                            '</soap:Envelope>';



           $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction:  http://www.isdsl.net/api/api.php/startSession",
                        "Content-length: ".strlen($xml_post_string),
                    ); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch);
            curl_close($ch);

            if ( (is_string($response))
                && (strpos($response, "timed out") !== false)
                && ($retries > 0)
               ){
                   if ( ($retries < 11) && ($continue == true))
                       $continue = false;

                    echo $retries . "<br/>";
                    $this->is_connect_new_curl($userName, $password, $continue, $retries - 1);
                }


            var_dump($response);
            echo "<hr/>";
            var_dump(curl_error($ch));
            die();


            // converting
            $response1 = str_replace("<soap:Body>","",$response);
            $response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
            $parser = simplexml_load_string($response2);
            // user $parser to get your data out of XML response and to display it.


    }

*/


    //7.4 procedure: addRealmAccount
    function add_realm_new_full($sess, $class, $user, $pass, $comment, $email, $retries = 20) {
        $data = array(
            'function'     => 'addRealmFull',
            'strSessionID' =>	$sess,
            'intClassID'   =>    $class,
            'strUserName'  => $user,
            'strPassword'  => $pass,
            'strComment'   => $comment,
            'strEmailAddress' => $email,
        );

        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);

        $resp = json_decode($resp);
        if (    isset($resp->faultstring)
            && (strpos(strtolower($resp->faultstring),'timeout') !== false)
            && ($retries > 0) ){
            //echo "retries : " . $retries . "<br/>";

            $resp = $this->add_realm_new_full($sess, $class, $user, $pass, $comment, $email, $retries - 1);
        }

        return $resp;
    }



    //7.7 Procedure: getAccountInfo
    function getAccountInfo_new($sess,$username){
        $data = array(
            'function' => 'getAccountInfo',
            'strSessionID'  =>	$sess,
            'strUserName'   =>  $username,
        );
        //$url = 'http://home.openweb.co.za/server.php';
        $url = 'http://home.openweb.co.za/apihandler';
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }


    function getAccountInfo_full_new($sess,$username){
        $data = array(
            'function' => 'getAccountInfoFull',
            'strSessionID'  =>	$sess,
            'strUserName'   =>  $username,
        );
        //$url = 'http://home.openweb.co.za/server.php';
        $url = 'http://home.openweb.co.za/apihandler';
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);

        $return_object = (array)json_decode($resp);
        $return_object['arrAccountInfo'] = (array)$return_object['arrAccountInfo'];
        return $return_object;
    }



    function resp_handler_set_pending_update_new($resp){

        $message = '';
        switch($resp) {

            case '0' : $message='Update was not scheduled ( "Once-Off" billing cycle )'; break;
            case '1' : $message='Success: Update scheduled'; break;
            case '2' : $message='Username does not exist'; break;
            case '5' : $message='Failure: Invalid session identifier
supplied'; break;
            case '7' : $message='Failure: ADSL account does not
belong to your organisation'; break;
            case '8' : $message='Failure: Invalid class'; break;
            default  : break;
        }

        return $message;
    }


    // 7.44 getAvailableTopUpClasses
    // Get a list of available top up clasess
    function get_available_top_up_classes_new($sess){

        $data = array(
            'function'     => 'getAvailableTopUpClasses',
            'strSessionID' => $sess,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        // out : arrClass , intReturnCode  (1 = Success / 12 = Api user does not have required permissions)

        curl_close($handle);
        return $resp;
    }


    // 7.45 Procedure: queueTopUp
    function queue_top_up_new($sess, $username, $topup_class_id){


        $data = array(
            'function'     => 'queueTopUp',
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'topupClassID' => $topup_class_id,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        // out : intReturnCode
        /*
            1 = Success
            2 = Username does not exist or does not have access to the web service.
            7 = Account does not belong to your organisation
            12 = Api user does not have required permissions
            41 = Base account username does not exist
            42 = L2TP account username does not exist
            46 = This operation is not allowed on this account's class
            47 = Invalid TopUp class
            65 = Top Up limit reached for this account

         */

        curl_close($handle);
        return $resp;
    }



    // 7.46 Procedure: cancelQueuedTopUp
    // Allows for the cancelling of a queued top up
    function cancel_queued_top_up_new($sess, $topup_id){

        $data = array(
            'function'     => 'cancelQueuedTopUp',
            'strSessionID' => $sess,
            'intTopUpID'   => $topup_id,

        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        // out : intReturnCode
        /*
            1 = Success
            12 = Api user does not have  required permissions
            48 = TopupID does not exist
            49 = TopupID is active or has been used
            50 = Could not Cancel TopUp

         */

        curl_close($handle);
        return $resp;


    }


    // 7.47 Procedure: getTopUpsPerUser
    // Returns all top ups associated with a BDSL account
    function get_top_ups_per_user_new($sess, $username){

        $data = array(
            'function'      => 'getTopUpsPerUser',
            'strSessionID'  => $sess,
            'strUserName'   => $username,

        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        // out : intReturnCode
        /*
            1 = Success
            2 = Username does not exist or does not have access to the web service.
            7 = Account does not belong to your organisation
            12 = Api user does not have required permissions
            41 = Base account username does not exist
            42 = L2TP account username does not exist
            67 = DSL account username not found

         */

        curl_close($handle);
        return $resp;


    }

         // 7.17
      function get_pending_update_new($sess, $username){
        $data = array(
            'function' 		=> 	'getPendingUpdate',
            'strSessionID'  =>	$sess,
            'strUserName'   =>  $username,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp,true);

          /*

          e.g.

             Array
                (
                    [arrUpdateQueue] => Array
                        (
                            [Username] => test-32341-123@mynetwork.co.za
                            [ProductTypeID] => nosvc
                            [QueueDate] => 2015-05-01 00:00:00
                        )

                    [intReturnCode] => 1
                )

          */

    }


        //7.15 procedure: setPendingUpdate
    // use full name + realm
    function set_pending_update_new($sess, $username, $class){
        $data = array(
            'function' 		=> 	'setPendingUpdate',
            'strSessionID'  =>	$sess,
            'strUserName'   =>  $username,
            'strClassID'    =>  $class,

        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }


  //7.11 procedure : setAccountClass
    function set_account_class_new($sess, $username, $class){
        $data = array(
            'function' 		=> 	'setAccountClass',
            'strSessionID'  =>	$sess,
            'intClassID'    =>  $class,
            'strUserName'   =>  $username,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }


/*
    function get_pending_update_new($sess, $username){
        $data = array(
            'function' 		=> 	'getPendingUpdate',
            'strSessionID'  =>	$sess,
            'strUserName'   =>  $username,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp,true);
    }*/

    function curl_send($data){

        $handle = curl_init($this->apiUrl);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;

    }

        //7.9  setAccountComment
    function set_account_comment_new($sess, $username, $comment){

        $data = array(
            'function' => 'setAccountComment',
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'strValue'     => $comment,
        );

        $resp = $this->curl_send($data);
        return json_decode($resp, true);
    }

    // realm without '@'
    function get_class_id($name, $realm){

        $this->db->select('table_id');
        $this->db->where('realm',$realm);
        $this->db->where('id',$name);

        $query = $this->db->get('is_classes');
        $result = $query->first_row('array');

        $return_id = '0';
        if (!empty($result)){

            $return_id = $result['table_id'];
        }

        return $return_id;
    }



        // ---------------------------- functions with handlers ------------------------------ /


    function resp_handler_set_account_class_new($resp){

        $message = '';
        switch($resp) {

            case '1'   : $message='Success: ADSL field value changed'; break;
            case '2'   : $message='Failure: Username does not exist'; break;
            case '5'   : $message='Failure: Invalid session identifier supplied'; break;
            case '7'   : $message='Failure: ADSL account does not belong to your organisation'; break;
            case '8'   : $message='Failure: Invalid class'; break;
            case '15'  : $message='Cannot Downgrade'; break;
            case ''    : $message='Empty-ok';
            default    : break;
        }

        return $message;
    }


    /**
     * Try to connect ADSL API and get Session ID for further calls
     *
     * @param array $realm_data : params 'user' and 'pass' to connect ADSL API
     * @return array  : function result with next fields 'result'  (True of False)
     *                                                   'message' (message for developer/admin)
     *                                                   'session' (Session ID if the call was successful)
     *                                                   'user_message' (message for the clients/users)
     */
    function is_connect_new_with_handler($realm_data){

        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];

        // returns Session ID or Error code;
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

        // "Failed to connect the ISDSL, the account username does not exist.";
        $result = false;
        $response_message = 'Failure : Session ID has not been received';
        $user_message = "Failure : Can't connect to the API"; // TODO : change users messages
        switch ($sess){

            // case 1 not in use for (is_connect_new) function
            case '1' : $result = true;
                $response_message  = "Success: Session identifier returned";
                $user_message = "Success";
                break;
            case '2' : $result = false; $response_message  = "Failure: Username does not exist";     break;
            case '3' : $result = false; $response_message  = "Failure: Incorrect password supplied"; break;
            case '4' : $result = false; $response_message  = "Failure: Remote IP not allowed";       break;


            default  :

                if (strlen($sess) > 3){
                    $result = true;
                    $response_message  = "Success: Session identifier returned";
                    $user_message = "Success";
                }
                break;

        }

        return array('result' => $result, 'message' => $response_message,
            'session' => $sess, 'user_message' => $user_message, 'api_response' => $sess);
    }



    function check_order_data($order_data){

        if (empty($order_data['account_username']) || empty ($order_data['realm']))
            return array('result' => false, 'message' => "Account username and realm can't be empty",
                'user_message' => "Account username and realm can't be empty");

        return array('result' => true);
    }



    function set_new_class_with_handler($order_data, $new_class){

            /*
             * $order_data['realm'],  $order_data['account_username']
             */

            if (!isset($this->realm_model))
                $this->load->model('admin/realm_model');

            $check_order_data_result = $this->check_order_data($order_data);
            if ($check_order_data_result['result'] == false)
                return $check_order_data_result;


        // API session
            $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
            $connect_result = $this->is_connect_new_with_handler($realm_data);
            if (!$connect_result['result'])
                return $connect_result;


        // construct params for API call and try to change class
           $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
           $change_class =  $this->set_account_class_new($connect_result['session'], $account_username, $new_class);
           $raw_change_class = $change_class;

           $message = 'Failure : Class was not changed';
           $user_message = 'Failure : Class was not changed';
           $result = false;


            $additional_data = array(
                "function" => "set_new_class_with_handler()",
                "response_connection_call" => print_r($connect_result, true),
                "raw_response_function_call"   =>  $raw_change_class,
            );



        if (($change_class == '1') || ($change_class == '')) {
               $result = true;
               $user_message = 'Success : Class was changed';

           }
           $message = $this->resp_handler_set_account_class_new($change_class);

           // patch
           if ($message == 'Empty-ok')
                $message = $this->resp_handler_set_account_class_new('1');
            // (Empty answer - not always 'Success'. e.g. in cases where class doesn't belong to realm)


        return array(
                    'result'          => $result,
                    'message'         => $message,
                    'user_message'    => $user_message,
                    'api_response'    => $change_class,
                    'additional_data' => $additional_data,
            );

    }




 	
    // TODO : duplicate to client side
    function get_account_full_info_with_handler($order_data){
    /*
     *  $order_data['realm'],  $order_data['account_username']
     */


        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;

        // get full info
        $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
        $accountInfo = $this->getAccountInfo_full_new($connect_result['session'], $account_username);



        $rawAccountInfo = $accountInfo;
        $accountInfo['intReturnCode'] = (int)$accountInfo['intReturnCode'];


        $additional_data = array(
            "function" => "get_account_full_info_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   => $rawAccountInfo,

        );


        // default messages
        $message = 'Failure : Default message';
        $user_message = 'Failure : Please contact the admin to resolve it';
        $result = false;

        // process answer
        if ($accountInfo['intReturnCode'] === 1){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($accountInfo['intReturnCode']);
        return array(
                        'result' => $result,
                        'message' => $message,
                        'user_message' => $user_message,
                        'api_response' => $accountInfo,
                        'additional_data' => $additional_data

        );




        /*
         * Response format :
         *
         * Array
                (
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

                    [intReturnCode] => 1
                )
         *
         * Array
                (
                    [arrAccountInfo] => Array
                        (

                        )

                    [intReturnCode] => 2
                )
         *
         *
         *  Typical errors :
         *      2 = Failure: Username does not exist
                5 = Failure: Invalid session identifier supplied
                7 = Failure: ADSL account does not belong to your organisation
         */



    }


    // TODO: check user's class (if this function exists)
    function add_realm_account_with_handler($order_data, $class, $pass, $comment, $email){


        // add_realm_new($sess, $class, $user, $pass, $comment, $email)

        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // TODO:
        // validate $class
        // validate $pass
        // $comment and $email


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;

        $accountInfo = $this->add_realm_new($connect_result['session'], $class, $order_data['account_username'], $pass, $comment, $email);
        //$accountInfo = 1;

        $rawAccountInfo = $accountInfo;
        $accountInfo = (int)$accountInfo;
        // return only API code !!!


        // default messages
        $message = 'Failure : Default message';
        $user_message = 'Failure : Please contact the admin to resolve it';
        $result = false;

        // process answer
        if ($accountInfo == 1){
            $user_message = '';
            $result = true;
        }

        /*
         * [additional_data] => Array
                ([request_url] => accounts/, [raw_response] => {"status":"success","type":"create","result":"Create successful"}
                )
            )
         */
        $additional_data = array(
                                "function" => "add_realm_account_with_handler()",
                                "response_connection_call" => print_r($connect_result, true),
                                "raw_response_function_call"   => $rawAccountInfo,

        );

        $message = $this->response_code_handler($accountInfo);
        return  array(
                        'result'          => $result,
                        'message'         => $message,
                        'user_message'    => $user_message,
                        'api_response'    => $accountInfo,
                        'additional_data' => $additional_data,
                    );


    }


    function delete_realm_account_with_handler($order_data){

        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;


        $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
        $deleteResult = $this->delete_account_new($connect_result['session'], $account_username);
        $raw_delete_result =  $deleteResult;
        $deleteResult = (int)$deleteResult;
        // $deleteResult is a API code !!


        $additional_data = array(
            "function" => "delete_realm_account_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   =>  $raw_delete_result,
        );


        // default messages
        $message = 'Failure : Default message';
        $user_message = 'Failure : Please contact the admin to resolve it';
        $result = false;


        // process answer
        if ($deleteResult === 1){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($deleteResult);
        return array(
                'result'          => $result,
                'message'         => $message,
                'user_message'    => $user_message,
                'api_response'    => $deleteResult,
                'additional_data' => $additional_data,
        );



    }



    function response_code_handler($resp){

        $code = (int)$resp;
        $message = '';
        switch($code) {

            case 1   : $message='Success: ADSL field value changed'; break;
            case 2   : $message='Failure: Username does not exist'; break;
            case 5   : $message='Failure: Invalid session identifier supplied'; break;
            case 7   : $message='Failure: ADSL account does not belong to your organisation'; break;
            case 8   : $message='Failure: Invalid class'; break;
            case 11  : $message='Failure: Username exists'; break;
            case 14  : $message='Failure: Requesting usage statistics for more than 6 months back'; break;
            case 15  : $message='Cannot Downgrade'; break;
           // case ''  : $message='Empty-ok';
            default  : $message=''; break;
        }



        return $message;
    }


/*
    function get_list_of_accounts($sess,$username){
        $data = array(
            'function' => 'getAccountUserNames',
            'strSessionID'  =>	$sess,

        );
        //$url = 'http://home.openweb.co.za/server.php';
        $url = 'http://home.openweb.co.za/apihandler';
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);

        $return_object = (array)json_decode($resp);
        $return_object['arrAccountInfo'] = (array)$return_object['arrAccountInfo'];
        return $return_object;
    }
*/


    //7.18 procedure: getYearlyStats
    function get_yearly_stats_new($sess, $username){
        $data = array(
            'function' => 'getYearlyStats',
            'strSessionID' => $sess,
            'strUserName'  => $username,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }

    function get_yearly_stats_new_full($sess, $username){
        $data = array(
            'function' => 'getYearlyStatsFull',
            'strSessionID' => $sess,
            'strUserName'  => $username,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }


    //7.19 procedure: getMonthlyStats
    function get_monthly_stats_new($sess, $username, $year, $month){
        $data = array(
            'function' => 'getMonthlyStats',
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'intYear' => $year,
            'intMonth' => $month,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }

    function get_monthly_stats_new_full($sess, $username, $year, $month){
        $data = array(
            'function' => 'getMonthlyStatsFull',
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'intYear' => $year,
            'intMonth' => $month,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }

    //7.20 procedure: getDailyStats
    function get_daily_stats_new($sess, $username, $year, $month, $day){
        $data = array(
            'function' => 'getDailyStats',
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'intYear' => $year,
            'intMonth' => $month,
            'intDay' => $day,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }

     //7.20 procedure: getDailyStats
    function get_daily_stats_new_full($sess, $username, $year, $month, $day){
        $data = array(
            'function' => 'getDailyStatsFull',
            'strSessionID' => $sess,
            'strUserName'  => $username,
            'intYear' => $year,
            'intMonth' => $month,
            'intDay' => $day,
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }




    //7.49 procedure getCurrentSessionInfo
    function get_current_session_info_new($sess,$username){
        $data = array(
            'function' => 'getCurrentSessionInfo',
            'strSessionID' => $sess,
            'strUserName'  => $username,
        );

        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }



        //7.49 procedure getCurrentSessionInfo
    function get_current_session_info_new_full($sess,$username){
        $data = array(
            'function'     => 'getCurrentSessionInfoFull',
            'strSessionID' => $sess,
            'strUserName'  => $username,
        );

        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return json_decode($resp, true);
    }







        /** API doc 7.49 Procedure: getCurrentSessionInfo
     *
     * Allows obtaining of Current active session of a DSL account.
     *
     * @param $order_data
     * @return array
     *
     */
    public function get_current_session_info_with_handler($order_data){


        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;


        $info_result = $this->get_current_session_info_new_full($connect_result['session'], 
                                                            $order_data['account_username']);
        $raw_info_result = $info_result;
        $additional_data = array(
            "function"                   => "get_current_session_info_with_handler()",
            "response_connection_call"   => print_r($connect_result, true),
            "raw_response_function_call" => $raw_info_result,
        );


        // default messages
        $message = 'Failure : Default message';
        $user_message = 'Failure : Please contact the admin to resolve it';
        $result = false;

        // process answer
        if ($info_result["intReturnCode"] == 1){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($info_result["intReturnCode"]);
        return array(
                    "result"          => $result, 
                    "message"         => $message, 
                    "user_message"    => $user_message, 
                    "api_response"    => $info_result,
                    "additional_data" => $additional_data,
                );

    }





    public function get_activity_info_with_handler($order_data, $params){

    
        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');


        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);

        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);

        if (!$connect_result['result'])
            return $connect_result;

        //var_dump(array($order_data, $params));

        // params['period'] can be in the follow format :
        //  2016-05-05, 2016-05

        // parse 'period' field
        $date_year  = null;
        $date_month = null;
        $date_day   = null;
        $date_periods = explode("-", $params['period']);

        // [0] will be a year in format "%d%d%d%d", e.g. : "2017"
        if(isset($date_periods[0]))
            $date_year = $date_periods[0];

        // [1] will be a month in format "%d", e.g.: "02"
        if (isset($date_periods[1]))
            $date_month = $date_periods[1];

        if (isset($date_periods[2]))
            $date_day = $date_periods[2];


      
        $raw_activity_type = $params['activity_type'];
        $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
        switch ($params['activity_type']) {

            case 'period' :
            case 'month'  :

                            $activity_result = $this->get_monthly_stats_new_full(
                                                            $connect_result['session'],
                                                            $account_username,
                                                            $date_year,
                                                            $date_month
                                                        );
                            // in cases when original activity_type was "period"
                            $raw_activity_type = "month";
                            break;

            case 'day'    :
                            // get_daily_stats_new($sess, $username, $year, $month, $day)
                            $activity_result = $this->get_daily_stats_new_full(
                                                            $connect_result['session'],
                                                            $account_username,
                                                            $date_year,
                                                            $date_month,
                                                            $date_day
                                                        );
                            break;

            case 'year'   :
                            // get_yearly_stats_new($sess, $username)
                            $activity_result = $this->get_yearly_stats_new_full(
                                                            $connect_result['session'],
                                                            $account_username
                                                        );
                            break;

            // TODO : add failure response
            default :

                return $errorResponse = array(
                     "result"          => false,
                     "message"         => "Wrong time period",
                     "user_mesage"     => "",
                     "api_response"    => null,
                     "additional_data" => null,

                    );
                break;
        }


        $raw_activity_result = $activity_result;


        $additional_data = array(
            "function"                   => "get_activity_info_with_handler()",
            "response_connection_call"   => print_r($connect_result, true),
            "raw_response_function_call" =>  $raw_activity_result ,
            "activity_type"              => $raw_activity_type,
        );

        // return activity type to network handler
        


        //var_dump($raw_activity_result); echo "<hr/>";
        //var_dump($additional_data); die();
        // response example { ["arrUsageStats"]=> array(0) { } ["intReturnCode"]=> int(1) }

        // default messages
        $message = 'Failure : Default message';
        $user_message = 'Failure : Please contact the admin to resolve it';
        $result = false;

        // process answer
        if ($activity_result["intReturnCode"] == 1){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($activity_result["intReturnCode"]);
        return array(
                    'result'          => $result,
                    'message'         => $message,
                    'user_message'    => $user_message,
                    'api_response'    => $activity_result,
                    'additional_data' => $additional_data,
                );

        // ------------------------------------------------
        // ----------- response exmaples ------------------



        /*
            "month" peridod (empty response) : 
            [raw_response_function_call] => 
                Array(
                    [arrUsageStats] => Array()
                    [intReturnCode] => 1
                )

        // -----------------------------------------------        

            "month" peridod (not empty response) :         
            [raw_response_function_call] => 
                Array(
                    [arrUsageStats] => Array(
                                            [0] => Array(
                                                    [Date] => 1-6-2017
                                                    [TotalTimeConnected] => 49084
                                                    [BytesSent] => 66057362
                                                    [BytesReceived] => 1708898728
                                                    [TotalUsageBytes] => 1774956090
                                                )
                        )
                    [intReturnCode] => 1
                )
        
        // ----------------------------------------------- 

            "day" period (not empty response) : 
            [raw_response_function_call] => Array
                (
                    [arrUsageStats] => Array
                        (
                            [0] => Array(
                                    [BytesSent] => 246873
                                    [BytesReceived] => 6480285
                                    [TotalUsageBytes] => 6727158
                                    [ESR] => Kempton Park BNG1
                                    [SessionID] => B0758B367ED567592E9CD1
                                    [StartTime] => 2017-05-31 12:37:06
                                    [StopTime] => 2017-05-31 12:42:39
                                    [SessionLength] => 333
                                    [DataRate] => 8192
                                    [CallingStationID] => 0113916275
                                    [SessionIP] => 196.210.57.92
                                    [DisconnectReason] => Lost-Carrier
                                )
                            [1] => Array(
                                    [BytesSent] => 9720531
                                    [BytesReceived] => 150858659
                                    [TotalUsageBytes] => 160579190
                                    [ESR] => Kempton Park BNG1
                                    [SessionID] => B0758B367EE0FD592E9D85
                                    [StartTime] => 2017-05-31 12:40:05
                                    [StopTime] => 2017-05-31 13:42:03
                                    [SessionLength] => 3718
                                    [DataRate] => 8192
                                    [CallingStationID] => 0113916275
                                    [SessionIP] => 196.210.71.238
                                    [DisconnectReason] => NA
                                )
                            [2] => Array(
                                    [BytesSent] => 761894
                                    [BytesReceived] => 32982512
                                    [TotalUsageBytes] => 33744406
                                    [ESR] => Kempton Park BNG1
                                    [SessionID] => B0758B367EE0FD592E9D85
                                    [StartTime] => 2017-05-31 13:42:03
                                    [StopTime] => 2017-05-31 13:54:44
                                    [SessionLength] => 761
                                    [DataRate] => 8192
                                    [CallingStationID] => 0113916275
                                    [SessionIP] => 196.210.71.238
                                    [DisconnectReason] => NA
                                )
                            [3] => Array(
                                    [BytesSent] => 14015721
                                    [BytesReceived] => 505591429
                                    [TotalUsageBytes] => 519607150
                                    [ESR] => Kempton Park BNG1
                                    [SessionID] => B0758B367FF32B592EAE79
                                    [StartTime] => 2017-05-31 13:52:41
                                    [StopTime] => 2017-05-31 14:51:20
                                    [SessionLength] => 3519
                                    [DataRate] => 8192
                                    [CallingStationID] => 0113916275
                                    [SessionIP] => 196.210.71.160
                                    [DisconnectReason] => NA
                                )
                            [4] => Array(
                                    [BytesSent] => 3696489
                                    [BytesReceived] => 137229757
                                    [TotalUsageBytes] => 140926246
                                    [ESR] => Kempton Park BNG1
                                    [SessionID] => B0758B367FF32B592EAE79
                                    [StartTime] => 2017-05-31 14:51:20
                                    [StopTime] => 2017-05-31 15:06:56
                                    [SessionLength] => 936
                                    [DataRate] => 8192
                                    [CallingStationID] => 0113916275
                                    [SessionIP] => 196.210.71.160
                                    [DisconnectReason] => Lost-Carrier
                                )
                        )
                    [intReturnCode] => 1
                )

            "year" period (not empty response):    
            [raw_response_function_call] => Array
                (
                    [arrUsageStats] => Array(
                            [0] => Array(
                                    [YearMonth] => 2017-June
                                    [TotalTimeConnected] => 52579
                                    [BytesSent] => 73961021
                                    [BytesReceived] => 1936104378
                                    [TotalUsageBytes] => 2010065399
                                )
                            [1] => Array(
                                    [YearMonth] => 2017-May
                                    [TotalTimeConnected] => 43650
                                    [BytesSent] => 163243375
                                    [BytesReceived] => 5893024962
                                    [TotalUsageBytes] => 6056268337
                                )
                        )

                    [intReturnCode] => 1
                )

    
                
        */

    }


    /** API doc 7.12 Procedure: suspendAccount
     *
     * This procedure will suspend the given ADSL account. Suspended accounts are still billable, but are
     * unusable once the user’s connection is reset
     *
     * @param $order_data
     * @return array
     *
     */
    function suspend_account_with_handler($order_data){

        /*
         * $order_data['realm'],  $order_data['account_username']
         */

        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;

        // construct params for API call and try to change class
        $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
        //do not use it -> $suspend_result = $this->suspend_account_new($connect_result['session'], $account_username);
        $suspend_result =  $this->suspend_account_new_full($connect_result['session'], $account_username);
        $raw_suspend_result =  $suspend_result;


        $additional_data = array(
            "function" => "suspend_account_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   =>  $raw_suspend_result,
        );

        $message = 'Failure : Account was not suspended';
        $user_message = 'Failure : Account was not suspended';
        $result = false;

        //if (($suspend_result == '1') || ($suspend_result == '')) {
        if ($suspend_result == '1') {    
            $result = true;
            $user_message = 'Success : Account was suspended';
        }
        $message = $this->response_code_handler($suspend_result);
        return array(
                    'result' => $result,
                    'message' => $message,
                    'user_message' => $user_message,
                    'api_response' => $suspend_result,
                    'additional_data' => $additional_data,
        );

    }

    /** API doc 7.14 Procedure: restoreAccount
     *
     * This procedure will restore the given suspended or deleted ADSL account. Deleted accounts can only be
     * restored up until the end of the month in which the account was deleted. Once restored, ADSL accounts are
     * returned to a used status, and will remain in this status until such time as they are suspended or deleted.
     *
     * @param $order_data ($order_data['realm'],  $order_data['account_username'])
     * @return array - processed API response
     */
    function restore_account_with_handler($order_data){


        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;


        // construct params for API call and try to change class
        $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
        $restore_result = $this->restore_account_new_full($connect_result['session'], $account_username);

        $message = 'Failure : Account was not restored';
        $user_message = 'Failure : Account was not restored';
        $result = false;

        // add raw data
        $raw_restore_result = $restore_result;

        $additional_data = array(
            "function" => "restore_account_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   => $raw_restore_result,

        );

        if ($restore_result == '1') {
            $result = true;
            $user_message = 'Success : Account was restored';
        }
        $message = $this->response_code_handler($restore_result);
        return array(

                'result' => $result,
                'message' => $message,
                'user_message' => $user_message,
                'api_response' => $restore_result,
                'additional_data' => $additional_data,
        );

    }

    /** API doc 7.8 Procedure: setAccountPassword
     *
     * This procedure will set the given ADSL accounts’ password to the requested new value
     * !WARNING! - API response (["api_response"]) can be empty, even for a successful action!
     *
     * @param $order_data
     * @param $new_password
     * @return array
     *
     */
    function change_account_password_with_handler($order_data, $new_password){

        /*
         *     function is_setAccountPassword_new($data) {
        $data['function'] = 'setAccountPassword';
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);
        return $resp;
    }
         *
         *
         * $data = array (
					'strSessionID' => $sess,
					'strUserName' => $new_username,
					'strValue' => $new_password,
				);
         */



        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;


        // construct params for API call and try to change class
        $account_username = $order_data['account_username'] . "@" . $order_data['realm'];
        $change_pass_input = array(
            'strSessionID' => $connect_result['session'],
            'strUserName'  => $account_username,
            'strValue'     => $new_password,
        );
        $password_result = $this->is_setAccountPassword_new_full($change_pass_input);

        $message = 'Failure : Password was not changed';
        $user_message = 'Failure : Password was not changed';
        $result = false;

          // add raw data
        $raw_pass_result = $password_result;
        $additional_data = array(
            "function" => "change_account_password_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   => $raw_pass_result,
        );


        if (($password_result == '1') || empty($password_result)) {
            $result = true;
            $user_message = 'Success : Password was changed';
        }
        $message = $this->response_code_handler($password_result);
        return array(
                    "result" => $result, 
                    "message" => $message, 
                    "user_message" => $user_message, 
                    "api_response" => $password_result,
                    "additional_data"  => $additional_data
            );

    }

    /** API doc  7.15 Procedure: setPendingUpdate
     *
     * This procedure will schedule a class change for the given ADSL account, at the beginning of
     * the next calendar month. No date can be specified.
     *
     * @param $order_data
     * @param $class
     * @return array
     *
     */
    public function set_pending_update_with_handler($order_data, $class){


        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;

        // $update_result
        //$accountInfo = $this->add_realm_new($connect_result['session'], $class, $order_data['account_username'], $pass, $comment, $email);
        $update_result = $this->set_pending_update_new($connect_result['session'],$order_data['account_username'],  $class);

        // return only API code
        $accountInfo = (int)$update_result;

        // default messages
        $message = "Failure : The update was not scheduled";
        $user_message = "Failure : The update was not scheduled";
        $result = false;

        // process answer
        if ($accountInfo === 1){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($accountInfo);
        return array('result' => $result, 'message' => $message, 'user_message' => $user_message, 'api_response' => $accountInfo);

    }

    /** API doc 7.17 Procedure: getPendingUpdate
     *
     * This procedure will return the schedule class updated details of an account provided, if any.
     *
     * @param $order_data
     * @return array
     *
     */
    function get_pending_update_with_handler($order_data){


        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;

        // $update_result
        $update_result = $this->get_pending_update_new($connect_result['session'],$order_data['account_username']);


        // return only API code
        $accountInfo = (int)$update_result;

        // default messages
        $message = "Failure : The update was not received";
        $user_message = "Failure : The update was not received";
        $result = false;

        // process answer
        if ($accountInfo === 1){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($accountInfo);
        return array('result' => $result, 'message' => $message, 'user_message' => $user_message, 'api_response' => $accountInfo);


    }


    /** Api doc 7.9 Procedure: setAccountComment
     *
     * This procedure will set the given ADSL accounts’ comment field to the requested new value
     *
     * @param $order_data
     * @param $comment
     * @return array
     *
     */
    public function set_account_comment_with_handler($order_data, $comment){
        //set_account_comment_new($sess, $username, $comment)


        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        $check_order_data_result = $this->check_order_data($order_data);
        if ($check_order_data_result['result'] == false)
            return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;

        $comment_result = $this->set_account_comment_new($connect_result['session'], $order_data['account_username'], $comment);

        //var_dump($comment_result);
        // echo 'comment was set';
        // return;

        // return only API code
        $raw_comment_result = $comment_result;
        $additional_data = array(

            "function" => "set_account_comment_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   =>  $raw_comment_result,

        );

        // default messages
        $message = "Failure : The comment was not set";
        $user_message = "Failure : The comment was not set";
        $result = false;


        // process answer
        if ($comment_result == '1'){
            $user_message = '';
            $result = true;
        }

        $message = $this->response_code_handler($comment_result);
        return array(
                
                "result" => $result, 
                "message" => $message, 
                "user_message" => $user_message, 
                "api_response" => $comment_result,
                "additional_data" =>  $additional_data,       
              );


    }

    public function get_classes_with_handler($order_data){

        // we need only realm from $order_data

        if (!isset($this->realm_model))
            $this->load->model('admin/realm_model');

        // check input parameters
        //$check_order_data_result = $this->check_order_data($order_data);
        //if ($check_order_data_result['result'] == false)
        //    return $check_order_data_result;


        // API session
        $realm_data = $this->realm_model->get_realm_data_by_name($order_data['realm']);
        $connect_result = $this->is_connect_new_with_handler($realm_data);
        if (!$connect_result['result'])
            return $connect_result;


        $get_classes_result = $this->get_classes_new($connect_result['session']);

        // return only API code
        //$accountInfo = (int)$comment_result;
        $raw_classes_result =  $get_classes_result;


        $additional_data = array(
            "function" => "get_classes_with_handler()",
            "response_connection_call" => print_r($connect_result, true),
            "raw_response_function_call"   =>  $raw_classes_result,
        );


        // default messages
        $message = "Failure : Classes are not avaialable";
        $user_message = "Failure : Classes are not avaialable";
        $result = false;


        // process answer
        if (!empty($raw_classes_result)){
            $message = $user_message = '';
            $result = true;
        }

        //$message = $this->response_code_handler($get_classes_result);
        return array(
                'result' => $result, 
                'message' => $message, 
                'user_message' => $user_message, 
                'api_response' => $get_classes_result,
                'additional_data' => $additional_data
                );
    }


    /**
     * This function update/insert new ISDSL class into local database
     * 
     * @param (array) $order_data  uses following format :
     *                          [ "realm" => "realm.za"  "account_username" => "some-username" ]
     * 
     * @return (array)
     * 
     * 
     * 
        raw response example : 
        /-----------------------------------------------------------/
                [0] => Array
                    (
                        [result] => 1
                        [type] => update
                        [class_row] => Array
                            (
                                [id] => nosvc
                                [desc] => ISDSL No Service
                                [realm] => mynetwork.co.za
                            )
                    )
                [1] => Array
                    (
                        [result] => 1
                        [type] => insert
                        [class_row] => Array
                            (
                                [id] => fhc1
                                [desc] => ISDSL+ Hard Cap 1G
                                [realm] => mynetwork.co.za
                            )
                    )
        /-----------------------------------------------------------/

    */
    function update_classes_with_handler($order_data){
        
        // get new classes and check response
        // return false in the following cases : 
        //      - ["result"] is false
        //      - ["api response"] is empty
        //      - ["api_response"] is not array

        $classes_response = $this->get_classes_with_handler($order_data);
        if (!$classes_response["result"] || empty($classes_response["api_response"]) 
                || !is_array($classes_response["api_response"]) )
            return $classes_response;

      
       $db_response_storage = array(); 
       foreach ($classes_response["api_response"] as $class_row)
            $db_response_storage[] = $this->check_and_update_class($order_data, $class_row);
       

       $respone = array(

                'result' => true, 
                'message' => "", 
                'user_message' => "", 
                'api_response' => $classes_response,
                'additional_data' => array(

                        "function" => "update_classes_with_handler()",
                        "response_connection_call" => "",
                        "raw_response_function_call"   =>  $db_response_storage,
                    ),
                
        );
       return $respone;
    }



    /**
     * 
     * @param (array) $order_data    uses following format :
     *                              [ "realm" => "realm.za", "account_username" => "some-username" ]
     * 
     * @param array $class_row      uses following format :  [ "ID" => "fhc7", "Desc" => "ISDSL+ Hard Cap 7G" ] 
     * @return (array) $response    uses following format : 
     *                              [  
     *                                "result" => null/true/false,
     *                                "type"   => "insert"/"update",
     *                                "class_row" => [   
     *                                                 "id"    => "ow-sc2",
                                                       "desc"  => "OW Soft Capped Account - 2/10GB",
                                                       "realm" => "mynetwork.co.za",
                                                      ]
                                    ]                   
                                       
     */
    function check_and_update_class($order_data, $class_row){


        $response_row = array(
                "result"    => null,
                "type"      => "none",
                "class_row" => null,
         );

        if (empty($class_row) || !isset($class_row["ID"]) 
                || !$class_row["Desc"] || !isset($order_data["realm"]))
            return $response_row;


        $update_class_row = array(
                "id" => $class_row["ID"],
                "desc" => $class_row["Desc"],
                "realm" => $order_data["realm"],
            );

        $table_name = "is_classes";

        // get class by params
        $this->db->select();
        $this->db->where("id", $update_class_row["id"]);
        $this->db->where("realm", $update_class_row["realm"]);
        $query = $this->db->get($table_name);
        $db_class_row = $query->first_row("array");
        
        $response_row = array(
                "result"    => null,
                "type"      => "insert",
                "class_row" => $update_class_row,
            );

        //echo "<hr/><br/>response row : "; var_dump($response_row); echo"<br/> db row : "; var_dump($db_class_row);
        // return;

        // insert new class to the classes table
        if (empty($db_class_row)){
           $response_row["result"] =  $this->db->insert($table_name, $update_class_row);  
           return $response_row;  
        }
        
        // update current table
        $response_row["type"] = "update";
        $this->db->where("table_id", $db_class_row["table_id"]);
        $response_row["result"] = $this->db->update($table_name, $update_class_row);
        return $response_row;

    }

    function getAccountUsage($sess, $usage, $query, $userlist) {

        $data = array(
            'function' => 'getAccountUsage',
            'strSessionID' => $sess,
            'strProduct' => 'lte',
            'strUsageType' => $usage,
            'strQuery' => $query,
            'strUserlist' => $userlist
        );
        $url = 'http://home.openweb.co.za/apihandler';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($handle);
        curl_close($handle);

        return json_decode($resp, true);
    }

}