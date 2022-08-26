<?php

class Avios_error extends CI_Model {

    public $code = "";
    public $message = "";

    private $errorMessages = array(
        //file errors
        "0001" => "Header record missing",
        "0002" => "Missing or invalid Batch Number in header record",
        "0003" => "Missing or invalid Feed Type in header record",
        "0004" => "Missing or invalid Format Version in header record",
        "0005" => "Missing or invalid Partner Code in header record",
        "0006" => "Missing or invalid File Generation Date on header record",
        "0007" => "Missing or invalid Reissue Number in header record",
        "0008" => "Invalid Record Format or Line Length",
        "0101" => "Footer record missing from file",
        "0102" => "Incorrect physical record count in footer record",
        "0103" => "Incorrect logical record count in footer record",
        "0104" => "Invalid Record Format or Line Length",
        "0105" => "Incorrect Total Bonus Loyalty Points in footer record",
        "0106" => "Incorrect Total Loyalty Points in footer record",
        "0201" => "File has been successfully processed already",
        "0202" => "A problem was encountered processing the file.",
        "0203" => "A problem with encrypting or decrypting the feed file was encountered.",
        "0204" => "Invalid file name received",
        "0301" => "Invalid Record Format or Line Length",

        //award summary errors
        "1001" => "Missing or invalid Transaction Type",
        "1002" => "Missing or invalid Transaction Date",
        "1003" => "Missing Partner or Loyalty Programme Member ID ",
        "1004" => "Missing Partner Transaction Reference",
        "1005" => "Missing or invalid Campaign Code",
        "1006" => "Missing or invalid Surname",
        "1007" => "Missing or invalid Partner Location Code",
        "1008" => "Missing or invalid Credit/Debit Code",
        "1009" => "Missing or invalid Partner Capture Join Date",
        "1010" => "Missing or invalid Partner Capture Method",
        "1011" => "Duplicate Partner Transaction Reference",
        "1012" => "Invalid IAG Loyalty Programme Member ID",
        "1013" => "Invalid Partner Loyalty Programme Member ID",
        "1014" => "Transaction Date Exceeds Allowed Date Range",
        "1015" => "Missing or invalid Debit Type",
        "1016" => "Missing or invalid Debt Transaction Reference",
        "1017" => "Duplicate Debit Transaction Reference",
        "1018" => "Missing Business Name",
        "1019" => "Missing Partner Location Code 2",
        "1020" => "Missing or invalid Loyalty Programme Currency Stock Code",
        "1021" => "Missing or invalid Business Identifier Code",

        //award codes
        "1301" => "Maximum number of points/miles that can be exchanged in the agreed period has been reached",
        "1302" => "Duplicate Award",
        "1303" => "Missing or invalid Total Loyalty Points",
        "1304" => "Missing or invalid Billing Code(s)",
        "1305" => "Missing or invalid Total Bonus Loyalty Points",
        "1306" => "Invalid Qualifies for Bonus Value Provided",
        "1307" => "Debit award exceeds original award credited to the loyalty program account",
        "1308" => "Debit award exceeds amount of Avios available in the Loyalty Program account",
        "1309" => "Award exceeds maximum award limit",
        "1310" => "Missing or invalid Award Description",
        "1311" => "Member is not eligible to receive the award",
    );

    function __construct($errCode) {
        $this->__setCode($errCode);
        $this->load->model('message_model');

    }

    public function __getError() {

        return array($this->code, $this->message);
    }

    public function __setCode($errCode) {

        if($this->validateError($errCode)) {
            $this->code = $errCode;
            $this->message = $this->errorMessages[$errCode];
        }

    }

    function validateError($errCode) {

        foreach ($this->errorMessages as $error => $mes) {
            if($errCode == $error) {
                return true;
            }
        }
        return false;
    }

    public function mailAboutError($award_id, $name, $username) {

        $message = "This Avios awards was rejected <br> Award ID: ".$award_id."<br> Username: ". $username . "<br> Name: ". $name . "<br> Error code: " .
            $this->code . "<br> Reason: ". $this->message;

        $this->message_model->send_email('avio_error@openweb.co.za', 'sergey.gerashchenko@lamp-dev.com', 'Rejected award', $message, array());
    }

    public function handleError($user_id, $award_id) {

        $user = $this->user_model->get_user_data_by_id($user_id);
        $username = $user['user_settings']['username'];
        $name = $user['user_settings']['first_name']. " " . $user['user_settings']['last_name'];

        $this->mailAboutError($award_id, $name, $username);

        switch ($this->code) {
            case "1006":
                //inform user
                $tmpl = $this->message_model->get_email_template_by_purpose('avios_surname_error');
                $message = $tmpl['content'];
                $message = str_replace('[First_Name]', $user['user_settings']['first_name'], $message);

                $this->message_model->send_email($tmpl['email_address'], $user['user_settings']['email_address'], $tmpl['title'], $message, array());
                break;
        }
    }
}