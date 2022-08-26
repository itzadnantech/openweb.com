<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Port_autocheck extends CI_Controller {

    function __construct() {


        // initial models
        parent::__construct();
        $this->load->model('port_model');;
    }


    function not_index($check)
    {

        // debug => http://home-keoma.192.168.33.10.xip.io/port_autocheck/not_index/lkuinnnfpuoslcuy34xjnca324

        // check GET parameter
        if (empty($check)){
            show_404(); die;
        }

        // validate GET key
        if (!$this->port_model->check_cron_key($check)){
            show_404(); die;
        }

        // restore services
        $this->port_model->process_cron_restoration();
        show_404();

    }







}
?>