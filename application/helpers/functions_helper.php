  <?php
    if (!defined('BASEPATH')) exit('No direct script access allowed');


    ///GetFibreOrdersData
    if (!function_exists('GetFibreOrdersData')) {
        function GetFibreOrdersData($order_id)
        {
            $thiz = &get_instance();
            $thiz->load->database();
            $query =  $thiz->db->select('*')->from('fibre_orders')
                ->where('order_id', $order_id)
                ->get();

            if ($query->num_rows() > 0) {
                $result = $query->result();
                return $result;
            } else {
                return 'xxxx';
            }
        }
    }
    ///GetBatchIdData
    if (!function_exists('GetBatchIdData')) {
        function GetBatchIdData($batch_id)
        {
            $thiz = &get_instance();
            $thiz->load->database();
            $query =  $thiz->db->select('*')->from('email_crons')
                ->where('batch_id', $batch_id)
                ->get();

            if ($query->num_rows() > 0) {
                $result = $query->result();
                return $result;
            } else {
                return 'xxxx';
            }
        }
    }
