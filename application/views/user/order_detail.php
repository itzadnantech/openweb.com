<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3>Order Detail</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple">
                    <div class="grid-body no-border">
                        <p id="order-id" hidden><?php echo $order_id ?></p>
                        <br/>
                        <br/>

                        <!-- Error Message -->
                        <?php
                        if(isset($error_message) && $error_message !=''){
                            echo "<div class='alert alert-error'>$error_message</div>";
                        }
                        ?>

                        <!-- LTE Top Up Button -->
                        <?php
                        if($order_type == 'lte-a') { ?>
                            <div>
                                <a href="/user/topup_lte/" class="btn btn-lg btn-success" style="padding-left: 60px; padding-right: 60px; margin-bottom: 10px" />Top Up</a>
                            </div>
                        <?php }

                        //Change Password
                        if(isset($change_flag) && $change_flag == 1){ ?>
                        <!--change service pwd  -->
                        <fieldset>
                        <legend>Change Service Password</legend>
                        <?php
    
	                    echo form_open('user/update_order', array('class'=>'','id'=>'change_pwd_form')); ?>
                            <input type="hidden" value="<?php echo $order_id;?>" name= "id"/>
                            <div class="row">

                                <label class="form-label col-md-2">New Password</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="account_password" id="account_password" placeholder="<?php echo $acc_pwd;?>"/>
                                </div>
                                <div class="col-md-2">
                                    <input type="submit" class="btn btn-sm btn-primary"  value="Update Service Password" />
                                </div>

                            </div>
                        <?php
                            echo form_close();
                        ?>
                        </fieldset>
                        <?php } ?>


                        <!-- reset port -->
                        <?php
                            // Not available for LTE orders
                            if (isset($port_available) && $order_type != 'lte-a'){
                        ?>
                            <fieldset>
                                <legend>Reset port</legend>
                                <div class="form-group">
                                    <?php if(!empty($port_message)) {
                                        echo "" . $port_message . $reset_port_button;
                                    } elseif($port_enabled) {
                                        echo "<a  href='/user/reset_port/" . $order_id  . "' class='btn btn-success'>Reset Port</a>";
                                    } else {
                                        echo "<a  href='#' class='btn btn-success btn-block disabled' disabled=disabled> Reset Port </a>";
                                    }?>
                                </div>
                            </fieldset>

                        <?php
                            }

                        //Display usage Block
                        if(isset($display_usage) && $display_usage == 1){
                        ?>

                        <!--session Info  -->
                        <fieldset>
                        <legend>Session Information</legend>
                        <?php
                        if(isset($session_error_message) && $session_error_message !=''){
                            echo "<div class='alert alert-error'>$session_error_message</div>";
                        }else{
                            if(isset($session_data)){

                                if($order_type != 'lte-a') {
                                    $tmpl = array('table_open' => '<table class="table no-more-tables">');
                                    $this->table->set_template($tmpl);
                                    $this->table->set_heading(array('Username', 'Login Time', 'Session Length(hh:mm:ss)', 'Session IP Address', 'Megabytes Sent', 'Megabytes Received', 'Total Megabytes'));

                                    $username = $session_data['Username'];
                                    $login_time = $session_data['LoginTime'];
                                    $sess_length = $session_data['SessionLength'];
                                    $IP = $session_data['SessionIPAddress'];
                                    $sent = $session_data['MegabytesSent'];
                                    $received = $session_data['MegabytesReceived'];
                                    $total = $session_data['Total'];

                                    $this->table->add_row(array($username, $login_time, $sess_length, $IP, $sent, $received, $total));
                                    echo $this->table->generate();
                                } else {
                                    $tmpl = array('table_open' => '<table class="table no-more-tables">');
                                    $this->table->set_template($tmpl);
                                    $this->table->set_heading(array('Username', 'Login Time', 'Session IP Address'));

                                    $username = $session_data['Username'];
                                    $login_time = $session_data['LoginTime'];
                                    $IP = $session_data['SessionIPAddress'];

                                    $this->table->add_row(array($username, $login_time, $IP));
                                    echo $this->table->generate();
                                }

                            }else{
                                echo "<div class='alert alert-info'>There are no current sessions at the moment.</div>";
                            }
                        }
                        ?>
                        </fieldset>
                        <br/>

                            Usage data is received directly from the upstream provider and not calculated by OpenWeb.  Usage stats can be delayed by up to 48 hours for various reasons

                        <!--Today Info  -->
                        <fieldset>
                            <legend id="today_title" style="cursor: pointer;">Today Usage Data</legend>
                        <?php

                        if(isset($today_error_message) && $today_error_message !=''){
                            echo "<div class='alert alert-error'>$today_error_message</div>";
                        }else{
                            if (isset($today_stats_data_total) && !empty($today_stats_data_total))  {
                                echo "Total : " . $today_stats_data_total . " MB";
                            }else{
                                echo "<div class='alert alert-info'>There are no usage data today.</div>";
                            }
                        }
                        ?>
                        </fieldset>
                        <br>

                        <!--This month usage  -->
                        <div id="this_month_div">
                            <fieldset>
                                <legend id="this_month_title" style="cursor: pointer;">Month Usage Data <i class="fa fa-plus-square-o"></i></legend>
                                <?php
                                if(isset($month_error_message) && $month_error_message !=''){
                                    echo "<div class='alert alert-error'>$month_error_message</div>";
                                }else{
                                    if (isset($month_stats_data_total['billed_total'])
                                        && !empty($month_stats_data_total['billed_total']))  {

                                        echo "Total billed : " . $month_stats_data_total['billed_total'] . " MB";

                                    }else{
                                        echo "<div class='alert alert-info'>There are no usage data this month.</div>";
                                    }
                                }
                                ?>
                            </fieldset>
                        </div>

                        <!-- Monthly Graphic -->
                        <div class="row chart-lte-div">
                            <div class="col-md-12 " >
                                <div class="grid simple">
                                    <div class="grid-title no-border">
                                        <h4><span class="semi-bold">Detailed Month Usage</span></h4>
                                        <div class="tools">
                                            <a href="javascript:;" onclick="reload('m')" class="reload"></a>
                                        </div>
                                    </div>
                                    <div class="grid-body no-border">
                                        <p id="chart-spinner"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw" ></i><span class="sr-only">Loading...</span></p>
                                        <div id="lte-chart" style="position: relative;" hidden></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <!--yearly Info  -->
                        <?php if($order_type != 'lte-a') {?>
                        <fieldset>
                            <legend id="year_title" style="cursor: pointer;">Year Usage Data <i class="fa fa-plus-square-o"></i></legend>
                            <?php
                            if(isset($year_error_message) && $year_error_message !=''){
                                echo "<div class='alert alert-error'>$year_error_message</div>";
                            }else{
                                if(isset($year_stats_data_total) && !empty($year_stats_data_total))  {
                                    echo "Total billed : " . $year_stats_data_total . " MB";
                                }else{
                                    echo "<div class='alert alert-info'>There are no usage data this year.</div>";
                                }
                            }
                            ?>
                        </fieldset>

                        <!-- Yearly Graphic -->
                        <div class="row chart-lte-year">
                            <div class="col-md-12 " >
                                <div class="grid simple">
                                    <div class="grid-title no-border">
                                        <h4><span class="semi-bold">Detailed Year Usage</span></h4>
                                        <div class="tools">
                                            <a onclick="reload('y')" class="reload"></a>
                                        </div>
                                    </div>
                                    <div class="grid-body no-border">
                                        <p id="chart-spinner-year"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw" ></i><span class="sr-only">Loading...</span></p>
                                        <div id="lte-chart-year" style="position: relative;" hidden></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                                <fieldset>
                                    <legend id="year_title" style="cursor: pointer;">Year Usage Data</legend>
                                    <?php
                                    if(isset($year_error_message) && $year_error_message !=''){
                                        echo "<div class='alert alert-error'>$year_error_message</div>";
                                    }else{
                                        if(isset($year_stats_data_total) && !empty($year_stats_data_total))  {
                                            echo "Total billed : " . $year_stats_data_total . " MB";
                                        }else{
                                            echo "<div class='alert alert-info'>There are no usage data this year.</div>";
                                        }
                                    }
                                    ?>
                                </fieldset>

                                <fieldset>
                                    <legend id="usage_summary_title" style="cursor: pointer;">Usage Summary Data</legend>
                                    <?php
                                        $fields = array (
                                            'data_type' => 'Data Type',
                                            'category' => 'Category',
                                            'title' => 'Title',
                                            'total_data' => 'Total Data',
                                            'remaining_data' => 'Remaining Data',
                                            'last_update' => 'Last Update',
                                            'activation_date' => 'Activation Date',
                                            'expire_date' => 'Expire Date'
                                        );
                                        if(isset($summary_lte_stats_data) && !empty($summary_lte_stats_data))  {
                                            foreach ($summary_lte_stats_data as $key => $data) {
                                                if ($key > 0) {
                                                    echo "<br>";
                                                }

                                                foreach ($fields as $slug => $field) {
                                                    if ($lte_usage_stats_model->toDisplay($slug)) {
                                                        switch ($field) {
                                                            case 'Total Data':
                                                                echo "Total Data : " . $data['Total Data'] . " " . $data['Data Units'] . '<br>';
                                                                break;

                                                            case 'Remaining Data':
                                                                echo "Remaining Data : " . $data['Remaining Data'] . " " . $data['Data Units'] . '<br>';
                                                                break;

                                                            default:
                                                                echo $field . " : " . $data[$field] . '<br>';
                                                                break;
                                                        }
                                                    }
                                                }
                                            }
                                        }else{
                                            echo "<div class='alert alert-info'>There are no usage data.</div>";
                                        }
                                    ?>
                                </fieldset>
                        <?php
                            }
                    }else{
                        ?>
                        <fieldset>
                            <legend>Usage Stats</legend>
                            <div class="alert alert-warning">
                              <strong>This information is not available on the product you have chosen at this time.</strong>
                            </div>
                        </fieldset>
                        <?php
                    }?>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>