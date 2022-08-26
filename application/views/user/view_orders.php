 <div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3>Active Orders</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple">
                    <div class="grid-body no-border">


                        <fieldset>
                            <?php echo form_open('user/search_order', array('class' => 'form-horizontal', 'id' => 'serch_form', 'style' => "
    margin-top: 10px;")); ?>
                            <div class="form-group">
                                <label class="control-label col-lg-2">Username:</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" name="user_name" id="user_name" value="<?php if (isset($search_name)) {
                                                                                                                        echo $search_name;
                                                                                                                    } ?>" />
                                </div>
                                <div class="col-lg-1">
                                    <input type="submit" class="btn btn-sm btn-primary" value="Search" />
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </fieldset>
                        <br />
                        <div id="divResults" class='alert alert-success'></div>
                        <div id="divResults-stat" class='alert alert-success' style="display:none"></div>
                        <?php


                        $success_message = $this->session->flashdata('success_message');
                        if (trim($success_message) != '') {
                            echo "<div class='alert alert-success'>$success_message</div>";
                        }

                        if (isset($error_message) && (trim($error_message) != '')) {
                            echo "<div class='alert alert-danger'>$error_message</div>";
                        }

                        $hide_cancel_for_imported = '';
                        if (isset($imported_user) && ($imported_user == 1))
                            $hide_cancel_for_imported = "disabled = 'disabled'";


                        if (isset($info) && trim($info) != '') {
                            echo "<div class='alert alert-info'>$info</div>";
                        }


                        if (!empty($orders)) {


                            $tmpl = array('table_open'  => '<table class="table no-more-tables">');
                            $this->table->set_template($tmpl);
                            $this->table->set_heading(array('Service Name', 'Service Type', 'Date Ordered', 'Account Info', 'Status', 'Actions'));
                            //	echo "<pre>";print_r($orders);die();
                            // echo '<pre>';
                            // print_r($orders);
                            // echo '</pre>';
                            // die;
                            foreach ($orders as $product_id => $order_data) {
                                $status = ucfirst($order_data['status']);
                                $date = "" . date('d/m/Y', strtotime($order_data['date']));
                                $product_data = $order_data['product_data'];
                                $name = isset($product_data['name']) ? $product_data['name'] : null;
                                $id = $order_data['id'];
                                $service_type = $order_data['service_type'];


                                if (!empty($order_data['topup_info']) && ($username == 'test-vvv')) {

                                    //   $name .= "\n" . $order_data['topup_info']['topup_name'] . "(" . $order_data['topup_info']['topup_level'] . ")";
                                    $name .= "<br/>" . "(" . $order_data['topup_info']['topup_name'] . ")";
                                    $date .= " " . "(" . date('d/m/Y', strtotime($order_data['topup_info']['order_time'])) . ")";
                                }


                                $ac_username = $order_data['acc_username'];
                                $ac_password = $order_data['acc_password'];
                                $account_info = "<div>MSISDN : $ac_username</div>
						<div>Sim Serial Number:" . $order_data['fibre']['sim_serial_no'] . "</div>";



                                if ($status != 'Pending cancellation' && $status != 'Cancelled' && $status != 'Deleted') {
                                    $cancel = anchor("user/cancel_order/$id", 'Cancel', 'class="btn btn-sm btn-danger order-buttons" ' . $hide_cancel_for_imported);
                                } elseif ($status == 'Pending cancellation') {
                                    $cancel = anchor("user/revoke_order/$id", 'Revoke', 'class="btn btn-sm btn-primary order-buttons" ' . $hide_cancel_for_imported);
                                } else {
                                    $cancel = "<input text='button' value='Cancel' class='btn btn-sm btn-danger order-buttons' style='width:60px;height:30px;background-color:gray;border-color:gray;'  disabled='disabled'/>";
                                }

                                $topup_order = null;
                                $disable_flag = "disabled=disabled";
                                if (isset($topups[$id]) && ($topups[$id]['topup_config'] == true)) {
                                    $disable_flag  = '';
                                }

                                if (($status == 'Active' || $status == 'Pending cancellation' || $status == 'Pending') && ($order_data['realm'] != 'fastfast' ||
                                    $order_data['realm'] != 'fastfast2')) {
                                    $manage = anchor("user/edit_active_order/$id", 'Manage', 'class="btn btn-sm btn-primary"');
                                    $topup_order = '';
                                    if ($username == 'test-vvv')
                                        $topup_order = anchor("user/order_topup/$id", 'Topup', 'class="btn btn-sm btn-primary" ' . $disable_flag  . ' ');
                                } else {
                                    $manage = "<input text='button' value='Manage' class='btn btn-sm btn-danger' style='width:68px;height:30px;background-color:gray;border-color:gray;'  disabled='disabled'/>";
                                    //$manage = anchor("user/edit_order/$id", 'Manage', 'class="btn btn-sm btn-primary"');
                                }

                                // Fibre case
                                if (
                                    isset($order_data['service_type']) &&
                                    (($order_data['service_type'] != 'adsl') && $order_data['service_type'] != 'lte-a')
                                ) {
                                    $name = $order_data['fibre']['product_name'] . "<br/>" . "(fibre " . $order_data['fibre']['fibre_type'] . ")";


                                    $account_info = ' - ';
                                    switch ($order_data['fibre']['fibre_type']) {

                                        case 'data':
                                            $account_info  = "<div>MSISDN   : " . $order_data['fibre']['fibre_data_username']  . "</div>";
                                            $account_info .= "<div>Sim Serial Number  : " . $order_data['fibre']['sim_serial_no']  . "</div>";
                                            $account_info .= "<div>Provider : " . $order_data['fibre']['fibre_data_provider'] . "</div>";
                                            break;
                                        case 'line':
                                            $account_info = "<div>Line number : " . $order_data['fibre']['fibre_line_number'] . "</div>";
                                            break;
                                        default:
                                            break;
                                    }

                                    $manage = "<input text='button' value='Manage' class='btn btn-sm btn-danger' style='width:68px;height:30px;background-color:gray;border-color:gray;'  disabled='disabled'/>";
                                }

                                // LTE-A
                                if (isset($order_data['service_type']) && $order_data['service_type'] == 'lte-a') {
                                    $name = $order_data['fibre']['product_name'] . "<br/>" . $order_data['fibre']['fibre_type'];

                                    $account_info = "<div>MSISDN   : " . $order_data['fibre']['fibre_data_username'] . "</div>";
                                    $account_info .= "<div>Sim Serial Number : " . $order_data['fibre']['sim_serial_no'] . "</div>";

                                    $topup = '<a href="/user/topup_lte/" class="btn btn-sm btn-success" style="margin-left:4px"/>Top Up</a>';
                                    $topup_telkom = "<a href='/user/lte_telkom_topup_list/" . $id  . "' class='btn btn-sm btn-info' style='margin-left:4px' title='Telkom Top Up'/>Top Up</a>";
                                    $topup_mtn = "<a href='/user/mtn_topup_list/" . $id  . "' class='btn btn-sm btn-warning' style='margin-left:4px' title='MTN Top Up'/>Top Up</a>";
                                }


                                ///mobile service
                                if (isset($order_data['service_type']) && $order_data['service_type'] == 'mobile') {
                                    $name = $order_data['fibre']['product_name'] . "<br/>" . $order_data['fibre']['fibre_type'];
                                    $account_info = "<div>MSISDN   : " . $order_data['fibre']['fibre_data_username'] . "</div>";
                                    $account_info .= "<div>Sim Serial Number : " . $order_data['fibre']['sim_serial_no'] . "</div>";

                                    $topup_mobile = "<a href='/user/mobile_topup_list/" . $id  . "' class='btn btn-sm btn-warning' style='margin-left:4px' title='Mobile Top Up'/>Top Up</a>";
                                }
                                ///adsl service
                                if (isset($order_data['service_type']) && $order_data['service_type'] == 'adsl') {
                                    $name = $order_data['product_data']['name'] . "<br/>" . $order_data['service_type'];
                                    $account_info = "<div>MSISDN : " . $order_data['acc_username'] . "</div>";
                                    // $account_info .= "<div>Sim Serial Number : " . $order_data['fibre']['sim_serial_no'] . "</div>";
                                }



                                /*if($status == 'Pending'){
			$status_name = 'Acitve';
		}else{
			$status_name = $status;
		}
		;
		 if($status == 'Active'){
			$change_pwd = anchor("user/change_service_pwd/$id", 'Change Password', 'class="btn btn-sm btn-primary"');
		}else{
			$change_pwd = "<input text='button' value='Change Password' class='btn btn-sm btn-danger' style='width:126px;height:30px;background-color:gray;border-color:gray;'  disabled='disabled'/>";
		} */

                                $mobile_request_button = '';
                                if (isset($order_status) && ($order_status == 'active'))
                                    if (isset($order_data['product_data']['mobile_data_enabled']) && ($order_data['product_data']['mobile_data_enabled'] == '1') && empty($order_data['mobile_data'])) {


                                        $mobile_data_amount = $order_data['product_data']['mobile_data_amount'];
                                        $mobile_data_type = $order_data['product_data']['mobile_data_type'];

                                        $mobile_request_button = "<br/><a href='/user/mobile_data_request/" . $id  . "' class='btn btn-default btn-block'> Request my " . $mobile_data_amount . " " . $mobile_data_type . " Mobile Data</a>";
                                        $account_info .=  $mobile_request_button;
                                    }



                                if (!empty($order_data['mobile_data'])) {


                                    $mobile_data_message = "<br/> Mobile Data Status : " . $order_data['mobile_data']['status'];

                                    if (!empty($order_data['mobile_data']['mobile_sim']))
                                        $mobile_data_message .= "<br/>Mobile SIM : " . $order_data['mobile_data']['mobile_sim'];

                                    if (!empty($order_data['mobile_data']['mobile_details']))
                                        $mobile_data_message .= "<br/> Mobile Details : " . $order_data['mobile_data']['mobile_details'];

                                    if (!empty($order_data['mobile_data']['notice']))
                                        $mobile_data_message .= "<h6>" . $order_data['mobile_data']['notice'] . "</h6>";

                                    unset($order_data['mobile_data']);

                                    $account_info .= $mobile_data_message;
                                }

                                $actions = $manage . ' ' . $cancel;
                                if (isset($topup))
                                    $actions .= '' . $topup;
                                if (!empty($topup_order))
                                    $actions = $topup_order . ' ' . $actions;

                                if ($order_data['fibre']['lte_type'] == "telkom" || $order_data['fibre']['lte_type'] == "mtn") {

                                    if (
                                        empty($order_data['stats_button_status'][0]['mtn_status']) &&  $order_data['stats_button_status'][0]['telkom_status'] != 'REQUESTED' ||
                                        empty($order_data['stats_button_status'][0]['telkom_status'])  &&  $order_data['stats_button_status'][0]['mtn_status'] != 'REQUESTED'
                                        // empty($order_data['stats_button_status'][0]['mobile_status'])  &&  $order_data['stats_button_status'][0]['mobile_status'] != 'REQUESTED'
                                    ) {
                                        $request_stats = '<button type="button" class="telkom_request_stat_btn btn btn-sm btn-primary" style="margin-left:4px" data-order-id ="' . $id  . '"  data-order-type ="' . $order_data['fibre']['lte_type']  . '" data-username="' . $order_data['fibre']['fibre_data_username']  . '" 
                                        data-simnumber="' . $order_data['fibre']['sim_serial_no']  . '" data-lte_username="' . $order_data['fibre']['username']  . '"  data-network="' . $order_data['fibre']['fibre_type']  . '" />Request Stats</button>';
                                    } else {
                                        $request_stats = '<button type="button" class="btn btn-sm btn-default" disabled style="margin-left:4px"/>Stats Requested</button>';
                                    }

                                    if ($order_data['fibre']['lte_type'] == "telkom") {
                                        $actions = $request_stats . '' . $topup_telkom;
                                    } elseif ($order_data['fibre']['lte_type'] == "mtn") {
                                        $actions = $request_stats . '' . $topup_mtn;
                                    }
                                }


                                if ($order_data['service_type'] == 'mobile') {
                                    if (
                                        empty($order_data['stats_button_status'][0]['mobile_status'])  &&  $order_data['stats_button_status'][0]['mobile_status'] != 'REQUESTED'
                                    ) {
                                        $request_stats = '<button type="button" class="telkom_request_stat_btn btn btn-sm btn-primary" style="margin-left:4px" data-order-id ="' . $id  . '"  data-order-type ="' . $order_data['fibre']['lte_type']  . '" data-username="' . $order_data['fibre']['fibre_data_username']  . '" 
                                        data-simnumber="' . $order_data['fibre']['sim_serial_no']  . '" data-lte_username="' . $order_data['fibre']['username']  . '"  data-network="' . $order_data['fibre']['fibre_type']  . '" />Request Stats</button>';
                                    } else {
                                        $request_stats = '<button type="button" class="btn btn-sm btn-default" disabled style="margin-left:4px"/>Stats Requested</button>';
                                    }

                                    $actions = $request_stats . '' . $topup_mobile;
                                }





                                // Port button

                                if (isset($order_data['port_available']) && $order_data['service_type'] != 'lte-a') {
                                    $reset_port_button  = "<a  href='#' class='btn btn-success btn-block disabled' disabled=disabled> Reset Port </a>";


                                    if ($order_data['port_enabled'])
                                        $reset_port_button = "<a  href='/user/reset_port/" . $id  . "' class='btn btn-success btn-block'>Reset Port</a>";

                                    if (!empty($order_data['port_message']))
                                        $reset_port_button = "<br/>" . $order_data['port_message'] . $reset_port_button;


                                    // $account_info .= $reset_port_button;
                                    $account_info .= '';
                                }

                                /*
        // disabled Rest port
        $reset_port_button  = "<a  href='#' class='btn btn-success btn-block disabled' disabled=disabled> Reset Port </a>";
        if ($order_data['port_available']){

            $reset_port_button = "<a  href='/user/reset_port/" . $id  . "' class='btn btn-success btn-block'>Reset Port</a>";
        }

        if (isset($order_data['port_message']))
            $reset_port_button = "<br/>" . $order_data['port_message'] . "<br/>" . $reset_port_button;

       /* if (isset($order_data['port_countdown']))
            $reset_port_button .=
             "<br/><a  href='/user/reset_port/" . $id  . "' class='btn btn-success btn-block disabled' disabled=disabled>" . $order_data['port_countdown'] . "</a>";
        */

                                // $account_info .= $reset_port_button;
                                $actions = array('data' => $actions, 'style' => 'width:260px;');
                                $this->table->add_row(array($name, $service_type, $date, $account_info, $status, $actions));
                            }

                            echo $this->table->generate();
                            echo "<div class='pull-right'>$pages</div>";
                        } else {
                        ?>
                            <div class="alert alert-warning">
                                <strong>Services not found.</strong> You have no <?php echo $title ?>.
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>