<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Test extends CI_Controller {

	function index() {
// 		$username = "administrator@fastadsl.co.za";
		$username = "administrator@platinum.co.za";
		$password = md5('12359');
		$session_id = $this->start_connect($username, $password);
		var_dump($session_id);
		$username = "24tgw@platinum.co.za";
		$response = $this->getAccountInfo($session_id, $username);
		var_dump($response);
	}
	
	function create_client() {
	    $options = array('socket' => array('bindto' => API_BINDTO));
	    $context = stream_context_create($options);
	    $url = API_URL;
	    $client = new SoapClient($url,
	            array('trace' => 1, 'exception' => 0, 'stream_context' => $context));
	    return $client;
	}
	
	function start_connect($userName, $password) {
	    $client = $this->create_client();
	    
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

	function get_classes($sess) {
		$client = $this->create_client();
		$is_param = array (
			'strSessionID' => $sess,
		);
		$resp = $client->__call("getClass", $is_param); 
		return $resp['arrClass'];
	}

	function getAccountUserNames($sess) {
		$client = $this->create_client();
		$is_param = array (
			'strSessionID' => $sess,
		);
		$resp = $client->__call("getAccountUserNames", $is_param); 
		return $resp;
	}

	function getAccountInfo($sess, $username) {
		$client = $this->create_client();
		$is_param = array (
			'strSessionID' => $sess,
			'strUserName' => $username
		);
		$resp = $client->__call("getAccountInfo", $is_param); 
		return $resp;
	}
}