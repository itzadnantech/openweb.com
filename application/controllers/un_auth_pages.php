<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Un_Auth_Pages extends CI_Controller {

    private $isAllowed = array('https://cherrybyte.co.za','https://openweb.co.za');
    private  $args = array();
    function __construct(){
        	parent::__construct();
             $this->args["strUserName"] = "wri52";
        $this->args["strPassword"] = "keoma99";
    }
function mtn_fixed_lte_coverage_map(){
// if(isset($_GET['isAllowed']) && in_array($_GET['isAllowed'],$this->isAllowed)){
    
// }else{
//     echo "<h3>You are not allowed to use this service..</h3>";
// }    
return $this->load->view('unauth/mtn_fixed_lte_coverage_map');
}

function lte_coverage_map(){
//   if(isset($_GET['isAllowed']) && in_array($_GET['isAllowed'],$this->isAllowed)){
  
// }else{
//     echo "<h3>You are not allowed to use this service..</h3>";
// }    
  return $this->load->view('unauth/lte_coverage_map');    
   }
   
 function telkom_lte_coverage_map(){
//   if(isset($_GET['isAllowed']) && in_array($_GET['isAllowed'],$this->isAllowed)){
  
// }else{
//     echo "<h3>You are not allowed to use this service..</h3>";
// }    
  return $this->load->view('unauth/lte_coverage_map');    
   }  
   
   function fibre_coverage_map(){
//   if(isset($_GET['isAllowed']) && in_array($_GET['isAllowed'],$this->isAllowed)){
  
// }else{
//     echo "<h3>You are not allowed to use this service..</h3>";
// }    
  return $this->load->view('unauth/fibre_coverage_map');    
   }   
function CurlFunction($d, $curlcall, $verb = "")
{
    $Username = "ResellerAdmin";
    $Password = "jFbd5lg7Djfbn48idmlf4Kd";

    $curl = new Curl();
    $response = new Response();

    switch ($curlcall)
    {
        case "getSession":
        {
            $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/getSession.json";
            $curl->setBasicAuthentication($Username, $Password);
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,2);
            $curl->get($Url, $d);

            break;
        }
        case "checkSession":
        {
            $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/checkSession.json";
            $curl->setBasicAuthentication($Username, $Password);
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,2);
            $curl->get($Url, $d);

            break;
        }
        case "checkFibreAvailability":
        {
            $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/checkFibreAvailability.json";
            $curl->setBasicAuthentication($Username, $Password);
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,2);
            $curl->get($Url, $d);

            break; 
        }
         case "getNetworkProviders":
        {
            $Url = "https://rcp.axxess.co.za/" . "calls/rsapi/getNetworkProviders.json";
            $curl->setBasicAuthentication($Username, $Password);
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,2);
            $curl->get($Url, $d);

            break; 
        }
        default:
        {
            $response->intCode = 500;
            $response->message = "NADA Requested, missing required data or REST Call is not valid!";
        }
    }

    $response->curl = $curl;

    if ($curl->error)
    {
        $response->intCode = $curl->error_code;
    }
    else
    {
        $response->json = $curl->response;
        $result = json_decode($curl->response);

        if (null === $result)
        {
            $response->intCode = 500;
            $response->message = "Too many nested arrays or error decoding.";
        }
        else
        {
            $response->intCode = $result->intCode;
            $response->message = isset($result->message) ? $result->message : null;
            $response->object = $result;
        }
    }

    if ($response->intCode != 200)
    {
        $response->hasError = true;
    }

    $curl->close();
    return $response;
}  
/**
 * @param $array
 * @return bool
 */
function is_array_assoc($array)
{
    return (bool)count(array_filter(array_keys($array), 'is_string'));
}

/**
 * @param $array
 * @return bool
 */
function is_array_multidim($array)
{
    if (!is_array($array)) {
        return false;
    }

    return !(count($array) === count($array, COUNT_RECURSIVE));
}

/**
 * @param $data
 * @param null $key
 * @return string
 */
function http_build_multi_query($data, $key = null)
{
    $query = array();

    if (empty($data)) {
        return $key . '=';
    }

    $is_array_assoc = is_array_assoc($data);

    foreach ($data as $k => $value) {
        if (is_string($value) || is_numeric($value)) {
            $brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
            $query[] = urlencode(is_null($key) ? $k : $key . $brackets) . '=' . rawurlencode($value);
        } else if (is_array($value)) {
            $nested = is_null($key) ? $k : $key . '[' . $k . ']';
            $query[] = http_build_multi_query($value, $nested);
        }
    }

    return implode('&', $query);
}
function api(){

    $providers_array = array();
    $previous_id = array();
      $sessionResult = $this->CurlFunction($this->args, "getSession");
   $this->args['strSessionId'] =$sessionResult->object->strSessionId;
   
   
  $result = $this->CurlFunction($this->args, "checkSession");   
   if($result->object->intCode == '200'){
       
       $address = $_POST['address'];
       $latlan = explode(',',$_POST['latlan']);
       $lat = $latlan[0];
       $long = $latlan[1];
           $this->args['strLongitude'] =$long;
            $this->args['strLatitude'] = $lat;
       $this->args['strAddress'] =$address;
        $re = $this->CurlFunction($this->args, "checkFibreAvailability");
    foreach($re->object->arrAvailableProvidersGuids as $provider){
    
     $this->args['guidNetworkProviderId'] = $provider->guidNetworkProviderId;
     $previous_id[] = array(
         'guiId'=>$provider->guidNetworkProviderId,
         'preOrder' => $provider->intPreOrder
         );
    
    $providers_array = $this->CurlFunction($this->args, "getNetworkProviders");
}
$networlProvidersList ='<h4>'.$re->object->strMessage.'</h4><table><thead>
<tr>
<th>Provider name</th>
<th>Status</th>
</tr>
</thead><tbody>';

foreach($providers_array->object->arrNetworkProviders as $pro){
    
foreach($previous_id as $pId){
    
if($pro->guidNetworkProviderId == $pId['guiId']){
$initOrder ='';
if($pId['preOrder'] == 0){
$initOrder = 'Live';        
}else{
$initOrder ='Pre-Order';
}
$networlProvidersList.='<tr><td><strong>'.$pro->strName.'</strong></td>';
$networlProvidersList.='<td><strong>'.$initOrder.'</strong></td></tr>';

   }    
  }
}
$networlProvidersList .='</tbody></table>';
  echo $networlProvidersList;
   
   }

}
}
class Response
{
    public $intCode;

    public $hasError = false;

    public $message;

    public $curl;

    public $json;

    public $object;
}

class Curl
{
    // TODO: set this.
    const USER_AGENT = 'OPEN WEB';

    private $_cookies = array();
    private $_headers = array();
    private $_options = array();

    private $_multi_parent = false;
    private $_multi_child = false;
    private $_before_send = null;
    private $_success = null;
    private $_error = null;
    private $_complete = null;

    public $curl;
    public $curls;

    public $error = false;
    public $error_code = 0;
    public $error_message = null;

    public $curl_error = false;
    public $curl_error_code = 0;
    public $curl_error_message = null;

    public $http_error = false;
    public $http_status_code = 0;
    public $http_error_message = null;

    public $request_headers = null;
    public $response_headers = null;
    public $response = null;

    /**
     * @throws \ErrorException
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is has not been loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADER, true);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param $url_mixed
     * @param array $data
     * @return int|mixed
     * @throws \ErrorException
     */
    public function get($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $curl_multi = curl_multi_init();
            $this->_multi_parent = true;

            $this->curls = array();

            foreach ($url_mixed as $url) {
                $curl = new Curl();
                $curl->_multi_child = true;
                $curl->setOpt(CURLOPT_URL, $this->_buildURL($url, $data), $curl->curl);
                $curl->setOpt(CURLOPT_HTTPGET, true);
                $this->_call($this->_before_send, $curl);
                $this->curls[] = $curl;

                $curlm_error_code = curl_multi_add_handle($curl_multi, $curl->curl);
                if (!($curlm_error_code === CURLM_OK)) {
                    throw new \ErrorException('cURL multi add handle error: ' .
                        curl_multi_strerror($curlm_error_code));
                }
            }

            foreach ($this->curls as $ch) {
                foreach ($this->_options as $key => $value) {
                    $ch->setOpt($key, $value);
                }
            }

            do {
                $status = curl_multi_exec($curl_multi, $active);
            } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

            foreach ($this->curls as $ch) {
                $this->exec($ch);
            }
        } else {
            $this->setopt(CURLOPT_URL, $this->_buildURL($url_mixed, $data));
            $this->setopt(CURLOPT_HTTPGET, true);
            return $this->exec();
        }
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function post($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($data));
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function put($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function patch($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @return int|mixed
     */
    public function delete($url, $data = array())
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->exec();
    }

    /**
     * @param $username
     * @param $password
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $key . ': ' . $value;
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->_headers));
    }

    /**
     * @param $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    /**
     * @param $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setCookie($key, $value)
    {
        $this->_cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }

    /**
     * @param $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * @param $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * @param $option
     * @param $value
     * @param null $_ch
     * @return bool
     */
    public function setOpt($option, $value, $_ch = null)
    {
        $ch = is_null($_ch) ? $this->curl : $_ch;

        $required_options = array(
            CURLINFO_HEADER_OUT => 'CURLINFO_HEADER_OUT',
            CURLOPT_HEADER => 'CURLOPT_HEADER',
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->_options[$option] = $value;
        return curl_setopt($ch, $option, $value);
    }

    /**
     * @param bool $on
     */
    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    /**
     *
     */
    public function close()
    {
        if ($this->_multi_parent) {
            foreach ($this->curls as $curl) {
                curl_close($curl->curl);
            }
        }

        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * @param $function
     */
    public function beforeSend($function)
    {
        $this->_before_send = $function;
    }

    /**
     * @param $callback
     */
    public function success($callback)
    {
        $this->_success = $callback;
    }

    /**
     * @param $callback
     */
    public function error($callback)
    {
        $this->_error = $callback;
    }

    /**
     * @param $callback
     */
    public function complete($callback)
    {
        $this->_complete = $callback;
    }

    /**
     * @param $url
     * @param array $data
     * @return string
     */
    private function _buildURL($url, $data = array())
    {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    /**
     * @param $data
     * @return array|string
     */
    private function _postfields($data)
    {
        if (is_array($data)) {
            if (is_array_multidim($data)) {
                $data = http_build_multi_query($data);
            } else {
                foreach ($data as $key => $value) {
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param null $_ch
     * @return int|mixed
     */
    protected function exec($_ch = null)
    {
        $ch = is_null($_ch) ? $this : $_ch;

        if ($ch->_multi_child) {
            $ch->response = curl_multi_getcontent($ch->curl);
        } else {
            $ch->response = curl_exec($ch->curl);
        }

        $ch->curl_error_code = curl_errno($ch->curl);
        $ch->curl_error_message = curl_error($ch->curl);
        $ch->curl_error = !($ch->curl_error_code === 0);
        $ch->http_status_code = curl_getinfo($ch->curl, CURLINFO_HTTP_CODE);
        $ch->http_error = in_array(floor($ch->http_status_code / 100), array(4, 5));
        $ch->error = $ch->curl_error || $ch->http_error;
        $ch->error_code = $ch->error ? ($ch->curl_error ? $ch->curl_error_code : $ch->http_status_code) : 0;

        $ch->request_headers = preg_split('/\r\n/', curl_getinfo($ch->curl, CURLINFO_HEADER_OUT), null, PREG_SPLIT_NO_EMPTY);
        $ch->response_headers = '';
        if (!(strpos($ch->response, "\r\n\r\n") === false)) {
            list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            if ($response_header === 'HTTP/1.1 100 Continue') {
                list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            }
            $ch->response_headers = preg_split('/\r\n/', $response_header, null, PREG_SPLIT_NO_EMPTY);
        }

        $ch->http_error_message = $ch->error ? (isset($ch->response_headers['0']) ? $ch->response_headers['0'] : '') : '';
        $ch->error_message = $ch->curl_error ? $ch->curl_error_message : $ch->http_error_message;

        if (!$ch->error) {
            $ch->_call($this->_success, $ch);
        } else {
            $ch->_call($this->_error, $ch);
        }

        $ch->_call($this->_complete, $ch);

        return $ch->error_code;
    }

    /**
     * @param $function
     */
    private function _call($function)
    {
        if (is_callable($function)) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array($function, $args);
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }
    

}

