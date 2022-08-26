 <?php


class sms_model extends CI_Model
{

    private $url;
    private $username;
    private $password;

    public function __construct()
    {
        $this->url = 'http://www.mymobileapi.com/api5/http5.aspx';
        $this->username = 'keoma'; //your login username
        $this->password = 'maniac20'; //your login password
    }

    public function checkCredits()
    {
        $data = array(
            'Type' => 'credits',
            'Username' => $this->username,
            'Password' => $this->password
        );
        $response = $this->querySmsServer($data);
        // NULL response only if connection to sms server failed or timed out
        if ($response == NULL) {
            return '???';
        } elseif ($response->call_result->result) {

            return $response->data->credits;
        }
    }

    public function sendSms($mobile_number, $msg)
    {

        $data = array(
            'Type' => 'sendparam',
            'Username' => $this->username,
            'Password' => $this->password,
            'numto' => $mobile_number, //phone numbers (can be comma seperated)
            'data1' => $msg, //your sms message

        );

        $response = $this->querySmsServer($data);
        // $response = $this->querySmsServerGet($data);
        //return $response;
        return $this->returnResult($response);
    }

    public function newSendSms($mobile_number, $msg)
    {

        $data = array(
            'messages' => array(
                array(
                    'content' => $msg,
                    'destination' => $mobile_number
                )
            )
        );

        $response = $this->newQuerySmsServer(http_build_query($data));

        return $response;
    }

    function sendGroupSMS($group_id, $msg)
    {

        $xml = $this->createXML($group_id, $msg);

        $data = array(
            'Type' => 'send',
            'username' => $this->username,
            'password' => $this->password,
            'xmldata' => $xml

        );
        $response = $this->querySmsServer($data);
        // $response = $this->querySmsServerGet($data);
        //var_dump($xml); die;
        return $this->returnResult($response);
    }

    // query API server and return response in object format
    private function querySmsServer($data, $optional_headers = null)
    {

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // prevent large delays in PHP execution by setting timeouts while connecting and querying the 3rd party server
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2000); // response wait time
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000); // output response time
        $response = curl_exec($ch);
        if (!$response) return NULL;
        else return new SimpleXMLElement($response);
    }

    private function newQuerySmsServer($data)
    {
        $ch = curl_init('https://rest.mymobileapi.com/v1/Authentication');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode('762c5134-f371-4a77-9c0d-fa153874a18e:QiSYOzTZI0R2lrnXjJbgA4fiaEWikHoO'),
            'Content-type: application/json'
        ));
        $response = json_decode(curl_exec($ch), true);

        $ch = curl_init('https://rest.smsportal.com/v1/bulkmessages');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $response['token'],
            'Content-type: application/x-www-form-urlencoded'
        ));
        $response = curl_exec($ch);
        return $response;
    }


    private function querySmsServerGet($data, $optional_headers = null)
    {


        $encodedData = http_build_query($data);
        $ch = curl_init($this->url . "?" . $encodedData);
        //curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // prevent large delays in PHP execution by setting timeouts while connecting and querying the 3rd party server
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2000); // response wait time
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000); // output response time
        $response = curl_exec($ch);
        if (!$response) return NULL;
        else return new SimpleXMLElement($response);
    }

    // handle sms server response
    private function returnResult($response)
    {
        $return = new StdClass();
        $return->pass = NULL;
        $return->msg = '';
        if ($response == NULL) {
            $return->pass = FALSE;
            $return->msg = 'SMS connection error.';
        } elseif ($response->call_result->result) {
            $return->pass = 'CallResult: ' . TRUE . '</br>';
            $return->msg = 'EventId: ' . $response->send_info->eventid . '</br>Error: ' . $response->call_result->error;
        } else {
            $return->pass = 'CallResult: ' . FALSE . '</br>';
            $return->msg = 'Error: ' . $response->call_result->error;
        }
        //echo $return->pass;
        //echo $return->msg;
        return $return;
    }

    public function sms_activation_fibre_order($data_array, $number, $fiber_data)
    {

        $sms_response = false;
        $fibre_str = '';
        // $tmpl = $this->get_template($data_array['service'])["body"];
        $tmpl = $this->get_template($data_array['service_type']);



        $this->load->model('admin/user_model');
        $user_data = $this->user_model->get_user_data($data_array['user']);




        $tmpl = $this->messagePlaceholders($tmpl, $user_data);



        switch ($data_array['service_type']) {
            case 'fibre-data':
                $tmpl = str_replace('[Product_Name]', $data_array['product_name_fd'], $tmpl);
                $tmpl = str_replace('[Username]', $data_array['username_fd'], $tmpl);
                $tmpl = str_replace('[Password]', $data_array['password_fd'], $tmpl);
                break;
            case 'fibre-line':
                $tmpl = str_replace('[Line_Number]', $fiber_data['number_fl'], $tmpl);
                break;
            case 'lte-a':
                $tmpl = str_replace('[Product_Name]', $data_array['product_name'], $tmpl);
                $tmpl = str_replace('[Username]', $data_array['account_username'], $tmpl);
                // $tmpl = str_replace('[Password]', $data_array['password_la'], $tmpl);
                break;
            case 'adsl':
                $tmpl = str_replace('[Product_Name]', $data_array['product_name'], $tmpl);
                $tmpl = str_replace('[Username]', $data_array['prod_username'], $tmpl);
                $tmpl = str_replace('[Password]', $data_array['prod_pass'], $tmpl);
                break;
            case 'mobile':
                $tmpl = str_replace('[Product_Name]', $fiber_data['product_name'], $tmpl);
                $tmpl = str_replace('[Username]', $data_array['account_username'], $tmpl);
                break;
            default:
                return false;
                break;
        }

        $sms_content = $tmpl;





        if (!empty($number)) {

            $sms_response = $this->sendSms($number, $sms_content); //Send SMS


            //#$this->order_model->send_sms($real_mobile_number, $sms_content);
            $sms_dump = print_r($sms_response, true);
        }


        return $sms_response;
    }

    function messagePlaceholders($text, $user_data)
    {

        $text = str_replace('[First_Name]', $user_data["user_settings"]['first_name'], $text);
        $text = str_replace('[Last_Name]', $user_data["user_settings"]['last_name'], $text);

        return $text;
    }

    //Create XML String for Bulk SMS request
    function createXML($group_id, $text)
    {

        $date = date('d/M/Y');
        $time = date('H:i');
        $send_time = date('H:i', strtotime("+10 minutes"));

        $xml = new SimpleXMLElement('<senddata></senddata>');


        $settings = $xml->addChild('settings');

        $settings->addChild('live', "True");
        $settings->addChild('return_credits', "True");
        $settings->addChild('return_msgs_credits_used', "True");
        $settings->addChild('return_msgs_success_count', "True");
        $settings->addChild('return_msgs_failed_count', "False");
        $settings->addChild('return_entries_success_status', "True");
        $settings->addChild('return_entries_failed_status', "True");
        $settings->addChild('default_senderid', "False");
        $settings->addChild('default_date', $date);
        $settings->addChild('default_time', $send_time);
        $settings->addChild('default_curdate', $date);
        $settings->addChild('default_curtime', $time);
        $settings->addChild('default_data1', $text);
        $settings->addChild('default_type', "SMS");
        $settings->addChild('default_validityperiod', "5");
        $settings->addChild('send_groupid', $group_id);

        $string = $xml->asXML();
        $res = substr($string, strpos($string, '>') + 1);

        return $res;
    }

    function createXMLGroup()
    {

        $xml = new SimpleXMLElement('<options></options>');

        $settings = $xml->addChild('settings');

        $settings->addChild('cols_returned', "groupname");
        $settings->addChild('date_format', "dd/MM/yyyy");

        $string = $xml->asXML();
        $res = substr($string, strpos($string, '>') + 1);

        return $res;
    }

    function getGroups()
    {

        $xml = $this->createXMLGroup();

        $data = array(
            'Type' => 'groups_list',
            'username' => $this->username,
            'password' => $this->password,
            'xmldata' => $xml

        );
        $response = $this->querySmsServer($data);
        // $response = $this->querySmsServerGet($data);

        return $this->returnGroups($response);
    }

    function returnGroups($data)
    {

        foreach ($data->data as $group) {
            $res[$group->groupid->__toString()] = $group->groupname->__toString();
        }

        return $res;
    }

    function get_template($name)
    {

        $query = $this->db->get_where('sms_templates', ['name' => $name]);
        $res = $query->result_array();

        return $res[0];
    }

    function get_sms_list()
    {

        $query = $this->db->get('sms_templates');
        $res = $query->result_array();

        return $res;
    }
}
