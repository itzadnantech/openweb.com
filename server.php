<?php
	
	//$session_id = connect('administrator@fastadsl.co.za', md5('12359'));
	//echo checkSession('6d460dcf22c8e7712813d7d578cb0c4f');
	
if (isset($_POST['function'])) {
	if ($_POST['function'] == 'start') {//7.1
		$userName = $_POST['username'];
		$password = $_POST['password'];
		$session_id = start_connect($userName, $password);
		echo $session_id;
	} else if ($_POST['function'] == 'addRealm') {//7.4
		$class = $_POST['intClassID'];
		$user = $_POST['strUserName'];
		$pass = $_POST['strPassword'];
		$comment = $_POST['strComment'];
		$email = $_POST['strEmailAddress'];
		$sess = $_POST['strSessionID'];
		$resp = add_realm($sess, $class, $user, $pass, $comment, $email);
		echo (int)$resp;
	} else if ($_POST['function'] == 'classes') {//7.5
		$sess = $_POST['sess'];
		$arrClass = get_classes($sess);
		$arrClass = json_encode($arrClass);
		echo $arrClass;
	} else if ($_POST['function'] == 'deleteAccount') { //7.13
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$resp = deleteAccount($sess, $userName);
	} else if ($_POST['function'] == 'setAccountPassword') {//7.8
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$value = $_POST['strValue'];
		//echo "Session: $sess $userName $value";
		$resp = setAccountPassword($sess, $userName, $value);
		//echo "resp $resp";
		echo $resp;
	}else if($_POST['function'] == 'getYearlyStats'){//7.18  getYearlyStat
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$arrUsageStats = get_yearly_stats($sess, $userName);
		$arrUsageStats = json_encode($arrUsageStats);
		echo $arrUsageStats;
	}else if($_POST['function'] == 'getMonthlyStats'){//7.19 getMonthlyStats
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$year = $_POST['intYear'];
		$month = $_POST['intMonth'];
		$arrUsageStats = get_monthly_stats($sess, $userName, $year, $month);
		$arrUsageStats = json_encode($arrUsageStats);
		echo $arrUsageStats;		
	}else if($_POST['function'] == 'getDailyStats'){//7.20 getDailyStats
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$year = $_POST['intYear'];
		$month = $_POST['intMonth'];
		$day = $_POST['intDay'];
		$arrUsageStats = get_daily_stats($sess, $userName, $year, $month, $day);
		$arrUsageStats = json_encode($arrUsageStats);
		echo $arrUsageStats;
	}else if($_POST['function'] == 'getCurrentSessionInfo'){//7.49 getCurrentSessionInfo
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$arrSessionInfo = get_current_session_info($sess, $userName);
		$arrSessionInfo = json_encode($arrSessionInfo);
		echo $arrSessionInfo;
	}else if ($_POST['function'] == 'setAccountClass'){//7.11 procedure : setAccountClass
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$class =  $_POST['intClassID'];		
		$resp = set_account_class($sess, $userName, $class);
		echo $resp;
	}else if($_POST['function'] == 'setPendingUpdate'){//7.15 procedure: setPendingUpdate
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$class =  $_POST['strClassID'];		
		$resp = set_pending_update($sess, $userName, $class);
		echo $resp;
	}else if($_POST['function'] == 'restoreAccount'){//7.14 procedure:restoreAccount
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$resp = restore_account($sess, $userName);
		echo $resp;
	}else if($_POST['function'] == 'getAccountInfo'){
		$sess = $_POST['strSessionID'];
		$userName = $_POST['strUserName'];
		$resp = get_account_info($sess, $userName);
		echo $resp;
	}

}

function setAccountPassword($session_id, $userName, $strValue) {
	$client = create_client();
    $is_param = array( 
		'strSessionID'	=> $session_id, 
		'strUserName' => $userName,
		'strValue' => $strValue,
	);
	$resp = $client->__call("setAccountPassword", $is_param); 
	return $resp['intReturnCode'];
}

function deleteAccount($session_id, $userName) {
	$client = create_client();
    
    $is_param = array( 
		'strSessionID' => $session_id, 
		'strUserName' => $userName,
	); 
	$resp = $client->__call("deleteAccount", $is_param); 
	return $resp['intReturnCode'];
}

function get_classes($sess) {
	$client = create_client();
	$is_param = array (
		'strSessionID' => $sess,
	);
	$resp = $client->__call("getClass", $is_param); 
	return $resp['arrClass'];
}

function add_realm($sess, $class, $user, $pass, $comment, $email) {
	$client = create_client();
    
    $is_param = array( 
		'strSessionID'    =>    $sess, 
		'intClassID'    =>    $class,
		'strUserName' => $user,
		'strPassword' => $pass,
		'strComment' => $comment,
		'strEmailAddress' => $email,
	); 
     	
	$resp = $client->__call("addRealmAccount", $is_param); 	
	//return $resp['intReturnCode'];
    return $resp;
}

 function get_account_info($sess,$username){
	$client = create_client();
	$is_param = array(
			'strSessionID'    =>    $sess,
			'strUserName' => $username,
	);
	$resp = $client->__call("getAccountInfo", $is_param);
	return $resp['intReturnCode'];
} 

function create_client() {
	$options = array('socket' => array('bindto' => API_BINDTO));
	$context = stream_context_create($options);
	$url = API_URL;
	$client = new SoapClient($url,array('trace' => 1, 'exception' => 0, 'stream_context' => $context));
	return $client;
}

function start_connect($userName, $password) {
    $client = create_client();
    
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

function checkSession($session_id) {
	$client = create_client();
	$resp = $client->__call("checkSession", array('strSessionID' => $session_id));
	if ($resp == 1) {
		return true;
	}
	return false;
}

function get_yearly_stats($sess, $userName){
	$client = create_client();
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

function get_monthly_stats($sess, $userName ,$year ,$month){
	$client = create_client();
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

function get_daily_stats($sess, $userName ,$year ,$month, $day){
	$client = create_client();
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

function get_current_session_info($sess, $userName){
	$client = create_client();
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

function set_account_class($sess, $username, $class){
	$client = create_client();
	$is_param = array(
		'strSessionID'  =>	$sess,
		'strUserName' 	=>	$username,
		'intClassID'    =>	$class,		
	);
	
	$resp = $client->__call("setAccountClass", $is_param); 
	return $resp['intReturnCode'];
}

function set_pending_update($sess, $username, $class){
	$client = create_client();
	$is_param = array(
		'strSessionID'    =>    $sess,
		'strClassID'    =>    $class,
		'strUserName' => $username,
	);
	
	$resp = $client->__call("setPendingUpdate", $is_param);
	return $resp['intReturnCode'];
}

function restore_account($sess, $username){
	$client = create_client();
	$is_param = array(
		'strSessionID' => $sess,
		'strUserName' => $username,
	);
	
	$resp = $client->__call("restoreAccount", $is_param);
	return $resp['intReturnCode'];
}
?>