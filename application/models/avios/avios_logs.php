<?php

class Avios_logs extends CI_Model
{

    public function addAwardFile($fileName) {

        $date = $this->mysqlDateNow();

        $batchNumber = substr($fileName, 21, 4);
        $reissue_number = substr($fileName, 25, 1);

        $fileInfo = array(
            "batch_number" => $batchNumber,
            "file_name" => $fileName,
            "date_create" => $date,
            "status" => "creating",
            "reissue_number" => $reissue_number
        );

        $insert = $this->db->insert('avios_award_files', $fileInfo);

        return $insert;
    }

    public function addAwardRow($file_id, $user_id, $order_id, $reason, $amount, $loyalty_id, $bonus = 0, $billing) {

        $date = $this->mysqlDateNow();

        $awardInfo = array(
            "file_id" => $file_id,
            "order_id" => $order_id,
            "user_id" => $user_id,
            "reason" => $reason,
            "amount" => $amount,
            "bonus" => $bonus,
            "date_create" => $date,
            "status" => "pending",
            "loyalty_member_id" => $loyalty_id,
            "billing_code" => $billing
         );
        $insert = $this->db->insert('avios_awards', $awardInfo);

        return $insert;
    }

    public function fileStatusUpdate($status, $file_id) {

        $data = array(
            "status" => $this->db->escape_str($status)
        );
        $this->db->where('file_id', $file_id);
        $this->db->update('avios_award_files', $data);
    }

    public function awardStatusUpdate($status, $award_id) {

        $data = array(
            "status" => $this->db->escape_str($status)
        );
        $this->db->where('award_id', $award_id);
        $this->db->update('avios_awards', $data);

    }

    public function getLastIndexes() {

        $all = $this->db->query("SELECT * FROM avios_award_files");

        //in case table is cleared
        if($all == null) {
            $indexes = array(
                "batch_number" => 0,
                "file_id" => 0,
                "reissue_number" => 0
            );
            return $indexes;
        }

        $lastInd = $all->last_row();

        $batch = $lastInd->batch_number;
        $fileId = $lastInd->file_id;
        $reissue = $lastInd->reissue_number;

        $indexes = array(
            "batch_number" => $batch+1,
            "file_id" => $fileId+1,
            "reissue_number" => 0
        );

        return $indexes;
    }

    public function getIndexesReissue($reBatch) {

        $query = $this->db->get_where('avios_award_files', array('batch_number' => $reBatch));
        $row = $query->last_row();

        $lastInd = $this->getLastIndexes();

        $indexes = array(
            "batch_number" => $reBatch,
            "file_id" => $lastInd['file_id'],
            "reissue_number" => $row->reissue_number + 1
        );

        return $indexes;
    }

    //get datetime from MySQL
    public function mysqlDateNow() {
        $dateQ = $this->db->query("SELECT NOW() as now");
        $date = $dateQ->row()->now;

        return $date;
    }
//======================prepared part=======================
    public function addPrepareAward($order_id, $user_id, $points, $bonus, $billing, $bonus_billing = null) {

        $date = $this->mysqlDateNow();

        $data = array(
            "order_id" => $this->db->escape_str($order_id),
            "user_id"  => $this->db->escape_str($user_id),
            "points"   => $this->db->escape_str($points),
            "date_create" => $date,
            "status"   => "waiting",
            "bonus_points" => $this->db->escape_str($bonus),
            //"loyalty_member_id" => $this->db->escape_str($member_id),
            "billing_code" => $this->db->escape_str($billing),
            "bonus_billing_code" => $this->db->escape_str($bonus_billing),
            //"loyalty_program_name" => $this->db->escape_str($program_name)
        );

        $insert = $this->db->insert('avios_prepare_awards', $data);

        return $insert;
    }

    public function getPrepareAwards() {

        $query = $this->db->get_where('avios_prepare_awards', array("status" => "waiting"));

        if($query->result() == null) {
            return 0;
        }

        $result = array();

        foreach ($query->result() as $row) {

            //not required fields which can not exist
            $points = $row->points ? $row->points : 0;
            $bonus_points = $row->bonus_points ? $row->bonus_points : 0;
            $billing_code = $row->billing_code ? $row->billing_code : "";
            $bonus_billing = $row->bonus_billing_code ? $row->bonus_billing_code : "";

            $rowData = array(
                "user_id" => $row->user_id,
                "order_id" => $row->order_id,
                "points" => $points,
                "bonus-points" => $bonus_points,
                "loyalty-programme-member-id" => $row->loyalty_member_id,
                "loyalty-programme-name" => $row->loyalty_program_name,
                "billing-code" => $billing_code,
                "bonus-billing-code" => $bonus_billing
            );

            array_push($result, $rowData);

            $id = $row->prep_id;
            $this->updatePrepareStatus("in_file", $id);
        }

        return $result;
    }

    function getAllPrepAwards($num_per_page, $start, $status = null, $billing= null) {

        if(isset($status) && $status != 'all')
            $this->db->where('status', $status);

        if(isset($billing) && $billing != 'all')
            $this->db->where('billing_code', $billing);

        $this->db->limit($num_per_page, $start);

        $query = $this->db->get('avios_prepare_awards');

        if($query->result() == null) {
            return 0;
        }

        $data = [];

        foreach ($query->result() as $row) {

            $rowData = array(
                "prep_id" => $row->prep_id,
                "user_id" => $row->user_id,
                "order_id" => $row->order_id,
                "points" => $row->points,
                "bonus_points" => $row->bonus_points,
                "billing_code" => $row->billing_code,
                "date" => $row->date_create,
                "status" => $row->status
            );

            array_push($data, $rowData);
        }

        return $data;
    }

    function getAllSentAwards($num_per_page, $start, $status = null, $billing= null, $month = null, $year = null) {
        $this->db->limit($num_per_page, $start);
        $this->db->order_by('date_create', 'desc');
        $this->db->order_by('award_id', 'desc');

        if(isset($status) && $status != 'all') {
            $this->db->where('status', $status);
        }

        if(isset($billing) && $billing != 'all') {
            $this->db->where('billing_code', $billing);
        }

        if(isset($month) && $month != 'all')
            $this->db->where('MONTH(date_create)', $month);

        if(isset($year) && $year != 'all')
            $this->db->where('YEAR(date_create)', $year);

        $query = $this->db->get('avios_awards');

        if($query->result() == null) {
            return 0;
        }

        $data = [];

        foreach ($query->result() as $row) {

            $rowData = array(
                "award_id" => $row->award_id,
                "user_id" => $row->user_id,
                "order_id" => $row->order_id,
                "points" => $row->amount,
                "bonus_points" => $row->bonus,
                "billing_code" => $row->billing_code,
                "date" => $row->date_create,
                "status" => $row->status
            );

            array_push($data, $rowData);
        }

        return $data;
    }

    public function updatePrepareStatus($newStatus, $id) {

        $data = array(
            "status" => $newStatus
        );
        $this->db->where('prep_id', $id);
        $res = $this->db->update('avios_prepare_awards', $data);
        return $res;
    }

    public function updatePrepareStatusByUser($newStatus, $user_id) {

        $data = array(
            "status" => $newStatus
        );
        $this->db->where('user_id', $user_id);
        $this->db->update('avios_prepare_awards', $data);
    }

    public function cleanPrepareTable() {

        $this->db->where('status', 'in_file');
        $this->db->delete('avios_prepare_awards');
    }

    function get_status_data(){
        $query = $this->db->query('select distinct status from avios_prepare_awards');
        $result = $query->result_array();
        return $result;
    }

    function get_sent_statuses(){
        $query = $this->db->query('select distinct status from avios_awards');
        $result = $query->result_array();
        return $result;
    }

    function get_awards_count($type = 'prep'){

        $table_name = 'avios_prepare_awards';
        $id_name = 'prep_id';

        if($type == 'sent') {
            $table_name = 'avios_awards';
            $id_name = 'award_id';
        }

        $this->db->select($id_name);
        $this->db->from($table_name);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_count($cond) {
        $this->db->select('prep_id');

        foreach ($cond as $field => $val) {
            if(!empty($val) && $val != 'all')
                $this->db->where($field, $val);
        }

        $query = $this->db->get('avios_prepare_awards');
        return $query->num_rows();
    }

    function get_sent_count($cond, $month, $year){
        $this->db->select('award_id');

        foreach ($cond as $field => $val) {
            if(!empty($val) && $val != 'all')
                $this->db->where($field, $val);
        }

        if(isset($month) && $month != 'all')
            $this->db->where('MONTH(date_create)', $month);

        if(isset($year) && $year != 'all')
            $this->db->where('YEAR(date_create)', $year);

        $query = $this->db->get('avios_awards');
        return $query->num_rows();
    }

    function get_sent_awards_count(){
        $this->db->select('award_id');
        $this->db->from('avios_awards');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_billing_count($billing){
        $this->db->select('prep_id');
        $this->db->where('billing_code', $billing);
        $query = $this->db->get('avios_prepare_awards');
        return $query->num_rows();
    }

    function get_status_count($status, $type = null){

        $table_name = 'avios_prepare_awards';
        $id_name = 'prep_id';

        if($type == 'sent') {
            $table_name = 'avios_awards';
            $id_name = 'award_id';
        }

        $this->db->select($id_name);
        $this->db->where('status', $status);
        $query = $this->db->get($table_name);
        return $query->num_rows();
    }

    function get_billing_status_count($billing, $status, $type = null){

        $table_name = 'avios_prepare_awards';
        $id_name = 'prep_id';

        if($type == 'sent') {
            $table_name = 'avios_awards';
            $id_name = 'award_id';
        }

        $this->db->select($id_name);
        $this->db->where('status', $status);
        $this->db->where('billing_code', $billing);
        $query = $this->db->get($table_name);
        return $query->num_rows();
    }

    function get_award_data($award_id) {
        $this->db->where('prep_id', $award_id);
        $query = $this->db->get('avios_prepare_awards');
        return $query->result_array();
    }

    function edit_award($id, $points, $bonus, $billing) {
        $this->db->where('prep_id', $id);
        $data = [ 'points' => $points,
                  'bonus_points' => $bonus,
                  'billing_code' => $billing];
        $result = $this->db->update('avios_prepare_awards', $data);
        return $result;
    }

    function delete_prep_award($id) {

        $this->db->where('prep_id', $id);
        $data = [''];
        $result = $this->db->update('avios_prepare_awards', $data);
    }

//================--------------prepared part--------------======================

    public function updateFileConfirmation($code, $batch, $reissue) {

        $status = 0;

        if($code == "0000") {
            $status = 1;
        }
        //last file with this batch number
        $query = $this->db->get_where('avios_award_files', array('batch_number' => $batch, 'reissue_number' => $reissue));
        $row = $query->last_row();
        $id = $row->file_id;

        $data = array(
            "confirmation_status" => $status,
            "confirmation_code" => $code
        );

        $this->db->where('file_id', $id);
        $this->db->update('avios_award_files', $data);
    }

    public function confirmAwardStatus($award) {

        $this->load->model('admin/user_model');

        $message_type = $award["message-type"];
        $code = $award["message-code"];
        $member_id = $award["loyalty-programme-member-id"];
        $points = $award["total-loyalty-points-awarded"];
        $bonus_points = $award["total-bonus-loyalty-points-awarded"];
        $file_id = substr($award["partner-transaction-reference"], 0, strpos($award["partner-transaction-reference"], " "));
        $new_reference = trim(substr($award["partner-transaction-reference"],strpos($award["partner-transaction-reference"], " ")));

        $user_id = substr($new_reference, 0, strpos($new_reference, " "));

        //find award in our DB
        $award_id = $this->getAwardId($member_id, $points, $bonus_points, $file_id, $user_id);

        if($message_type == "I") {

            $this->awardStatusUpdate("confirmed", $award_id);
            //send confirmation to user
            $this->user_model->mailAboutAvios($user_id, $points, $bonus_points);

        }
        else if($message_type == "E") {
            $this->awardErrorStausUpdate("rejected", $award_id, $code);
            //add new error handler
            $error = new Avios_error($code);
            $error->handleError($user_id, $award_id);

        }
    }

    public function getAwardId($member_id, $points, $bonus_points, $file_id, $user_id) {

        $query = $this->db->get_where('avios_awards', array(
                                                            'loyalty_member_id' => $member_id,
                                                            'user_id' => $user_id,
                                                            'file_id' => $file_id));
        $row = $query->last_row();

        return $row->award_id;
    }

    public function awardErrorStausUpdate($status, $award_id, $code) {

        $data = array(
            "status" => $status,
            "error_code" => $code
        );
        $this->db->where('award_id', $award_id);
        $this->db->update('avios_awards', $data);
    }

    public function addNewAnswerFile($filename, $status) {
        $date = $this->mysqlDateNow();
        $data = [
            "name" => $filename,
            "status" => $status,
            "date_create" => $date
        ];
        $this->db->insert('avios_answer_files', $data);
    }

    public function getAnswerFiles() {

        $this->db->select('name');
        $query = $this->db->get('avios_answer_files');
        $res = $query->result_array();
        $res_array = [];
        foreach ($res as $row) {
            array_push($res_array, $row['name']);
        }

        return $res_array;
    }

    public function getRules() {
        $query = $this->db->get('avios_billing_settings');
        $result = $query->result_array();

        return $result;
    }

    public function setRules($rules) {

        foreach ($rules as $id=>$array) {
            $this->db->where('id', $id+1);
            $this->db->update('avios_billing_settings', $array);
        }

        return true;
    }

    function getAwardYears() {

        $text = "SELECT DISTINCT YEAR(date_create) FROM avios_awards";
        $query = $this->db->query($text);
        $res = $query->result_array();
        $years = [];

        foreach ($res as $k => $arr) {
            array_push($years, $arr["YEAR(date_create)"]);
        }

        return $years;
    }

    function getTotalsMonth($month, $year) {

        $this->db->where('MONTH(date_update)', $month);
        $this->db->where('YEAR(date_update)', $year);
        $query = $this->db->get('avios_awards');
        $month_data = $query->result_array();

        $confirmed = 0;
        $rejected = 0;
        $conf_bonus = 0;
        $rej_bonus = 0;
        $total_count = 0;
        $grand_total = 0;
        $grand_total_bonus = 0;
        $details = [];

        foreach ($month_data as $award) {

            if($award['status'] == 'confirmed') {
                $conf_bonus += $award['bonus'];
                $confirmed += $award['amount'];
            }

            if($award['status'] == 'rejected') {
                $rejected += $award['amount'];
                $rej_bonus += $award['bonus'];
            }

            if($award['status'] == 'confirmed' || $award['status'] == 'rejected') {
                $details[$award['billing_code']] = [$details[$award['billing_code']][0]+$award['amount'], $details[$award['billing_code']][1]+$award['bonus']];
                $grand_total += $award['amount'];
                $grand_total_bonus += $award['bonus'];
                $total_count++;
            }
        }

        $res = [
            ['<b>Confirmed</b>',$confirmed, $conf_bonus]
        ];

        $codes = $this->avios_main->billingCodes;

        foreach ($details as $key => $data) {

            foreach ($codes as $code => $name) {
                if($code == $key)
                    $key = ' - '.$name;
            }

            array_push($res, [$key, $data[0], $data[1]]);
        }

        array_push($res, ['Rejected', $rejected, $rej_bonus]);
        array_push($res, ['Grand Total', $grand_total, $grand_total_bonus]);

        return $res;
    }
}