<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Month_auto_invoice extends CI_Controller {

    function __construct() {


        // initial models
        parent::__construct();
        $this->load->model('admin/order_model');;

    }



    function not_index($check)
    {

        if (empty($check)){
            show_404(); die;
        }

       $check_result =  $this->order_model->month_auto_cron_check($check);
       if ($check_result !== true) {

           show_404(); die;
       }


       $this->order_model->month_auto_invoice_with_email() ;


    }







}
?>