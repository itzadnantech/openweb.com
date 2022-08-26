<?php
class Isdsl_model extends CI_Model
{
    const ENDPOINTS = [
        'dev' => 'https://soap.isdsl.net/api/rest/',
        'prod' => 'https://www.isdsl.net/api/rest/'
    ];

    const CREDENTIALS = [
        'dev' => [
            'username' => 'api@openwebmobile.co.za',
            'password' => 'kjhdkjsa6i213hjksa!'
        ],
        'prod' => [
            'username' => 'api@openwebmobile.co.za',
            'password' => 'oC3JRkyQ7q==123-'
        ]
    ];

    const MODE = 'prod'; // set 'prod' for production and 'dev' for development

    protected function getEndpoint ($service, $procedure) {

        if (array_key_exists(static::MODE, static::ENDPOINTS)) {
            $endpoint = static::ENDPOINTS[static::MODE];
        } else {
            return '';
        }

        $endpoint .= "$service/$procedure.php";

        return $endpoint;
    }

    protected function getCredentials ($delimiter = false) {

        if (array_key_exists(static::MODE, static::CREDENTIALS)) {
            $creds = static::CREDENTIALS[static::MODE];
        } else {
            $creds = [];
        }

        if ($delimiter) {
            if (empty($creds)) {
                return '';
            } else {
                return $creds['username'] . $delimiter . $creds['password'];
            }
        } else {
            return $creds;
        }
    }

    public function getLteUsages ($username, $realm) {
        $endpoint = $this->getEndpoint('lte', 'usageSummary');

        $user = $username . '@' . $realm;

        $endpoint .= "?username=" . $user;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getCredentials(':'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($return, true);

        if (json_last_error() != JSON_ERROR_NONE || !$json['ok']) {
            return [];
        }

        if (array_key_exists($user, $json['data'])){
            return $json['data'][$user];
        }

        return [];
    }

    public function getLteUsagesList ($usernames) {
        $endpoint = $this->getEndpoint('lte', 'usageSummary');

        $userList = '';
        foreach ($usernames as $username) {
            $userList .= $username. ',';
        }
        $userList = substr($userList, 0, strlen($userList)-1);

        $endpoint .= "?username=" . $userList;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getCredentials(':'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($return, true);

        if (json_last_error() != JSON_ERROR_NONE || !$json['ok']) {
            return [];
        }

        if (!empty($json['data'])){
            return $json['data'];
        }

        return [];
    }
}