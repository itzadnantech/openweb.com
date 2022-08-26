 <?php

/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 19.10.2017
 * Time: 14:03
 */

class Crons extends CI_Controller
{

    public function AviosFileParser()
    {
        if ($this->input->is_cli_request()) {
            $this->load->model("avios/avios_main");
            $this->avios_main->parseNewFiles();
            echo "OK";
        }
    }

    public function resetMonthAviosDate()
    {

        if ($this->input->is_cli_request()) {
            $this->load->model("admin/order_model");
            $this->order_model->reseteAviosDate();
            echo "OK";
        }
    }

    public function createFile($batch)
    {

        if ($this->input->is_cli_request()) {
            $this->load->model("avios/avios_main");
            $this->load->model("avios/avios_physical");
            $this->load->model("avios/avios_logs");

            $data = $this->avios_main->awardDataGathering();

            if ($this->avios_physical->createFile($data, $batch)) {
                $this->avios_logs->cleanPrepareTable();
                return true;
            }
        }
    }

    public function monthlyAviosAward()
    {

        if ($this->input->is_cli_request()) {

            $this->load->model("admin/order_model");
            $this->load->model("avios/avios_logs");
            $this->load->model("avios/avios_main");

            $orders = $this->order_model->get_orders_avios_awards();
            $billing_settings = $this->avios_logs->getRules();

            foreach ($orders as $order) {

                $award_data['order_id'] = $order['id'];
                $award_data['user_id'] = $order['id_user'];
                $award_data['billing-code'] = $order['avios_code'];

                //points
                $spend = $order['price'];
                $rule = $this->avios_main->findRule($billing_settings, $award_data['billing-code']);

                //monthly awarding
                if ($rule[0] == 1 && $spend > 0) {
                    $award_data['points'] = $spend / $rule[1];
                    $this->avios_main->giveAviosAward($award_data);
                }

                //once awarding
                if ($rule[0] != 1) {
                    $award_data['points'] = $rule[1];
                    $this->avios_main->giveAviosAward($award_data);
                }
            }

            //autocreate file but we need corrections now so it is off
            //$this->avios_main->dailyAwardFile(); //TODO: this method will create file with ALL pending awards, may be fix it
        }
    }

    public function getLTEUsage()
    {
        if ($this->input->is_cli_request()) {
            $this->load->model('lte_usage_stats_model');
            $this->lte_usage_stats_model->getLteUsageStats();
        }
    }


    ///access function using cronjobs
    function bulk_mail()
    {

        ///load user model
        $this->load->model('admin/user_model');

        //////get date
        $get_cron_states = $this->user_model->get_cron_bulk_mail_state('bulk_mail');


        if (isset($get_cron_states) && !empty($get_cron_states)) {
            $batch_id = $get_cron_states[0]['batch_id'];
            $cron_time = $get_cron_states[0]['cron_time'];


            ///update cron time; 
            $data['cron_time'] = $cron_time + 5;
            $check = $this->user_model->update_email_crons_table($data, $batch_id);



            if ($check) {

                ///get date
                $get_cron_states = $this->user_model->get_cron_bulk_mail_state('bulk_mail');

                $limit = $get_cron_states[0]['user_limit'];
                $total_sent_emails = $get_cron_states[0]['total_sent_emails'];
                $offset = $get_cron_states[0]['offset'];
                $time = $get_cron_states[0]['time'];
                $cron_time = $get_cron_states[0]['cron_time'];
                $total_users = $get_cron_states[0]['total_users'];
                $status = $get_cron_states[0]['status'];
                $batch_id = $get_cron_states[0]['batch_id'];

                ///set if all emails sent
                if ($total_sent_emails == $total_users) {
                    $update_data = array();
                    $update_data['status'] = 'inactive';
                    $this->user_model->update_email_crons_table($update_data, $batch_id);
                    ///get date
                    $get_cron_states = $this->user_model->get_cron_bulk_mail_state('bulk_mail');
                    // echo '<pre>';
                    // print_r($get_cron_states);
                    // echo '</pre>';
                    // die;
                    $status = $get_cron_states[0]['status'];
                }



                if ($status == 'active') {

                    if ($time == $cron_time) {
                        $email_users = $this->user_model->get_bulk_users_new($limit, $offset);

                        if (!empty($email_users)) {
                            ///save to database
                            $email_detail = $this->user_model->get_email_detail('bulk');

                            $template_id = $email_detail[0]['id'];
                            if ($email_detail) {
                                $data['email_detail'] = $email_detail;

                                $email_attach_data = $this->user_model->get_email_attach($template_id);
                                if ($email_attach_data) {
                                    $data['attach_data'] = $email_attach_data;
                                } else {
                                    $data['attach_data'] = '';
                                }
                            }
                            ///cron tables 
                            $post_data = array();
                            $post_data['offset'] = $offset + $limit;
                            $post_data['user_limit'] = $limit;
                            $post_data['time'] = $time;
                            $post_data['total_sent_emails'] = $total_sent_emails + count($email_users);
                            $post_data['cron_time'] = 0;

                            $check = $this->user_model->update_email_crons_table($post_data, $batch_id);


                            ///set crons
                            if ($check) {
                                // JS POST request AJAX in other action 
                                $this->message_model->send_bulk_email($email_users, $email_detail[0], $email_attach_data, $batch_id);
                                echo 'done';
                                die;
                                // $suc_msg = 'Success';
                            }
                        } else {
                            $post_data = array();
                            /////cron tables  
                            $post_data = array();
                            $post_data['offset'] = 0;
                            $post_data['user_limit'] = 0;
                            // $post_data['total_users'] = 0;
                            $post_data['time'] = 0;
                            $post_data['cron_time'] = 0;
                            // $post_data['total_sent_emails'] = 0;
                            $post_data['status'] = 'inactive';
                            $check = $this->user_model->update_email_crons_table($post_data, $batch_id);
                        }
                    }


                    if ($cron_time > $time) {
                        $post_data = array();
                        $post_data['cron_time'] = 0;
                        $check = $this->user_model->update_email_crons_table($post_data, $batch_id);
                    }
                }
            }
        }
    }

    ///access function using cronjobs
    function reseller_bulk_mail()
    {

        ///load user model
        $this->load->model('admin/user_model');

        //////get date
        $get_cron_states = $this->user_model->get_cron_bulk_mail_state('reseller_bulk_mail');




        if (isset($get_cron_states) && !empty($get_cron_states)) {
            $batch_id = $get_cron_states[0]['batch_id'];
            $cron_time = $get_cron_states[0]['cron_time'];


            ///update cron time; 
            $data['cron_time'] = $cron_time + 5;
            $check = $this->user_model->update_email_crons_table($data, $batch_id);




            if ($check) {

                ///get date
                $get_cron_states = array();
                $get_cron_states = $this->user_model->get_cron_bulk_mail_state('reseller_bulk_mail');


                $limit = $get_cron_states[0]['user_limit'];
                $total_sent_emails = $get_cron_states[0]['total_sent_emails'];
                $offset = $get_cron_states[0]['offset'];
                $time = $get_cron_states[0]['time'];
                $cron_time = $get_cron_states[0]['cron_time'];
                $total_users = $get_cron_states[0]['total_users'];
                $status = $get_cron_states[0]['status'];
                $batch_id = $get_cron_states[0]['batch_id'];

                ///set if all emails sent
                if ($total_sent_emails == $total_users) {
                    $update_data = array();
                    $update_data['status'] = 'inactive';
                    $this->user_model->update_email_crons_table($update_data, $batch_id);
                    ///get date
                    $get_cron_states = $this->user_model->get_cron_bulk_mail_state('reseller_bulk_mail');
                    // echo '<pre>';
                    // print_r($get_cron_states);
                    // echo '</pre>';
                    // die;
                    $status = $get_cron_states[0]['status'];
                }

                // echo '<pre>';
                // print_r($get_cron_states);
                // echo '</pre>';
                // die;



                if ($status == 'active') {

                    if ($time == $cron_time) {
                        $email_users = $this->user_model->get_reseller_bulk_users_new($limit, $offset);

                        if (!empty($email_users)) {
                            ///save to database
                            $email_detail = $this->user_model->get_email_detail('reseller_mail');

                            $template_id = $email_detail[0]['id'];
                            if ($email_detail) {
                                $data['email_detail'] = $email_detail;

                                $email_attach_data = $this->user_model->get_email_attach($template_id);
                                if ($email_attach_data) {
                                    $data['attach_data'] = $email_attach_data;
                                } else {
                                    $data['attach_data'] = '';
                                }
                            }
                            ///cron tables 
                            $post_data = array();
                            $post_data['offset'] = $offset + $limit;
                            $post_data['user_limit'] = $limit;
                            $post_data['time'] = $time;
                            $post_data['total_sent_emails'] = $total_sent_emails + count($email_users);
                            $post_data['cron_time'] = 0;

                            $check = $this->user_model->update_email_crons_table($post_data, $batch_id);
                            // echo '<pre>';
                            // print_r($check);
                            // echo '</pre>';
                            // die;

                            ///set crons
                            if ($check) {
                                // JS POST request AJAX in other action 
                                $this->message_model->send_bulk_email($email_users, $email_detail[0], $email_attach_data, $batch_id);
                                echo 'done';
                                die;
                                // $suc_msg = 'Success';
                            }
                        } else {
                            $post_data = array();
                            /////cron tables  
                            $post_data = array();
                            $post_data['offset'] = 0;
                            $post_data['user_limit'] = 0;
                            // $post_data['total_users'] = 0;
                            $post_data['time'] = 0;
                            $post_data['cron_time'] = 0;
                            // $post_data['total_sent_emails'] = 0;
                            $post_data['status'] = 'inactive';
                            $check = $this->user_model->update_email_crons_table($post_data, $batch_id);
                        }
                    }


                    if ($cron_time > $time) {
                        $post_data = array();
                        $post_data['cron_time'] = 0;
                        $check = $this->user_model->update_email_crons_table($post_data, $batch_id);
                    }
                }
            }
        }
    }
}
