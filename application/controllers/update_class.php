<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update_class extends CI_Controller 
{
	function index(){
		//get all realms
		$realm_list = $this->get_realm();
        $this->load->model('admin/is_classes');

        // check each realm
		if($realm_list){
			foreach ($realm_list as $r){
				$realm = $r['realm'];
				$user = $r['user'].'@'.$realm;
				$pass = $r['pass'];

				// get classes for current realm
				$classes = $this->get_class_from_api_new($user, $pass);
				if (!empty($classes)) {
					$new_class = array();

                    //for each class
					foreach($classes as $class) {
						$class_id = $class->ID;
						$desc = $class->Desc;

                        // check if current class already exist in database
						$result = $this->get_class_id($class_id, $realm);
						$new_class = array(
								'id' => $class_id,
								'desc' => $desc,
								'realm' => $realm,
						);

                        // update DB info
						if(!empty($result)){
							$this->db->where('id', $class_id);
							$this->db->where('realm', $realm);
							$this->db->update('is_classes', $new_class);
						}else{
							$this->db->insert('is_classes', $new_class);
						}
					}
				}
			}
		}
		
	}




    function get_class_from_api_new($user, $pass){

        $sess_id = $this->is_classes->is_connect_new($user, $pass);
        $resp = $this->is_classes->get_classes_new($sess_id);
        $resp = json_decode($resp);

        return $resp;
    }




   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



	function get_class_id($class_id, $realm){

		$this->db->select('table_id');
		$this->db->where('id', $class_id);
		$this->db->where('realm', $realm);
		$query = $this->db->get('is_classes');
		$result = $query->result_array();
		return $result;
	}
	
	function get_realm(){
		$query = $this->db->get('realms');
		$result = $query->result_array();
		return $result;
		
	}
	function get_class_form_api($user, $pass){



		//$user = 'administrator@mynetwork.co.za';
		//$pass = '485c862ab6defdd2267d37bd497787d0';
		$client = $this->create_client();
		$sess_id = $this->start_connect($user, $pass);
        
		$data = array(
				'sess_id' => $sess_id,
		);
		$resp = $client->__call('getClass', $data);
		// Should return class and return code
		if ($resp['intReturnCode'] == 1) {
			return $resp['arrClass'];
		} else {
			return '';
		}
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
	
	function create_client() {
		$options = array('socket' => array('bindto' => API_BINDTO));
		$context = stream_context_create($options);
		$url = API_URL;
		$client = new SoapClient($url,array('trace' => 1, 'exception' => 0, 'stream_context' => $context));
		return $client;
	}
	

	/*
	 Done: administrator@mynetwork.co.za,
	Add Classes to Database
	*/
	/*
	 $user = 'administrator@fastadsl.co.za';
	$pass = '12359';
	$sess_id = '';
	$session_valid = false;
	$sess_id = $this->session->userdata('is_sess');
	if ($sess_id && trim($sess_id) != '1') {
	// check that the session is still valid.
	echo "<div>got session: $sess_id</div>";
	$session_valid = $this->is_check_session($sess_id);
	}
	if (!$session_valid) {
	$sess_id = $this->is_start_connection($user, $pass);
	$this->session->set_userdata('is_sess', $sess_id);
	echo 'adding session';
	}
	*/
	
	
	// Adds classes to database
	
	/*
	 $class = $this->is_get_class($sess_id);
	$to_add = array();
	if (!empty($class)) {
	$new_class = array();
	foreach($class as $class) {
	$new_class['id'] = $class->ID;
	$new_class['desc'] = $class->Desc;
	array_push($to_add, $new_class);
	}
	$this->db->insert_batch('is_classes', $to_add);
	}
	*/
	
/* 	function is_get_class($sess_id) {
		$data = array(
				'sess_id' => $sess_id,
		);
		$resp = $this->server_call('getClass', $data);
		// Should return class and return code
		if ($resp->intReturnCode) {
			return $resp->arrClass;
		} else {
			return 0;
		}
	}
	
	function is_check_session($sess_id) {
		$data = array(
				'strSessionID' => $sess_id,
		);
		$code = $this->server_call('checkSession', $data);
		return $code;
	}
	
	function is_start_connection($user, $pass) {
		$data = array(
				'strUserName' => $user,
				'strPassword' => md5($pass),
				'blnContinue' => true
		);
		$resp = $this->server_call('startSession', $data);
		//echo 'response: ' . $resp->intReturnCode;
		if ($resp->intReturnCode == 1) {
			$sess_id = $resp->strSessionID;
			return $sess_id;
		} else {
			echo 'fail';
			return 0;//'failure: ' . $resp->intReturnCode;
		}
	
	}
	
	function server_call($function, $data) {
		$server = 'http://start.openweb.co.za/server.php';
		$curl = curl_init($server);
		curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => array (
		'request' => $function,
		'attr' => http_build_query($data),
		),
		));
	
		$response = curl_exec($curl);
		echo($response);
		curl_close($curl);
		$response = json_decode($response);
		print_r($response);
		return $response;
	} */
}