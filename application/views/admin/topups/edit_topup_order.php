<style>
    .error{
        color:#f62b2b;
        display:none;
    }

    .shif-fix {

        padding-top : 6px;
    }




</style>
<script language="javascript" type="text/javascript">
    $(document).ready(function() {


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        function handlePaymentStatus(){

            //get status option
            var paymentStatusObj = $("select#payment-status-element");
            var paymentStatus = paymentStatusObj.val();
            revertClassOptions(paymentStatus);

        }

        function revertClassOptions(paymentStatus){

            if (paymentStatus == 'canceled'){
                // show revert class
                $("div#revert-div").show();

            } else {
                // hide revert class
                $("div#revert-div").hide();
            }

        }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


        handlePaymentStatus();

        $("select#payment-status-element").change(function() {
            handlePaymentStatus();
        });



    });

   // revert-div
</script>
<!--
<script language="javascript" type="text/javascript">
    $(function(){
        $( "#date" ).datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
        });

        $('#manage_order_form').submit(function (){
            var account_username = $('#account_username').val();
            var account_password = $('#account_password').val();

            if(account_password != '' && account_username != ''){
                $('#acc_name').hide();
                $('#acc_pwd').hide();
                return true;
            }else{
                if(account_password == ''){
                    $('#acc_pwd').show();
                }else{
                    $('#acc_pwd').hide();
                }

                if(account_username == ''){
                    $('#acc_name').show();
                }else{
                    $('#acc_name').hide();
                }
                return false;
            }
        });

        $('.active_order').click(function(){
            var account_username = $('#account_username').val();
            var account_password = $('#account_password').val();

            if(account_password != '' && account_username != ''){
                $('#acc_name').hide();
                $('#acc_pwd').hide();
                return true;
            }else{
                if(account_password == ''){
                    $('#acc_pwd').show();
                }else{
                    $('#acc_pwd').hide();
                }

                if(account_username == ''){
                    $('#acc_name').show();
                }else{
                    $('#acc_name').hide();
                }
                return false;
            }
        });
    });
</script> -->
<h3>Manage TopUp Order</h3>
<?php
/*
if (isset($messages['success_message']) && trim($messages['success_message']) != '') { ?>
    <div class="alert alert-success">
        <?php echo $messages['success_message'] ?>
    </div>
<?php }
*/
?>

<?php
if ( isset($topup_order_info) && !empty($topup_order_info) ) {

    /*
     *
     *  order_time
        user data (username, fullname)
        user old order data (product order)
              + adsl_username
              + [service_class_id] =>
              + [service_class_name] =>

        на моент заказа
              +    [real_class_id] =>
              +    [real_class_name] =>

        topup config info
             [topup_class_id] =>
             [topup_class_name] =>

       [schedule_api_status] =>
       [schedule_api_message] =>
       [already_scheduled_id] =>

       [api_status] =>
       [api_message] =>
        payment method
        payment status (select)

    price

     */
    // prepare_data
    // =======================================================================

    // pre

   // $data['service_order'] = $service_order_data;
   // $data['product_info']  = $product_info;
   // $data['topup_info']    = $topup_info;
   // $data['topup_order_info'] = $topup_order_info;

    /*
    echo "<pre>";
    print_r($topup_order_info);
    echo "</pre>";
    echo "<hr/>";

    echo "<pre>";
    print_r($topup_info);
    echo "</pre>";
    echo "<hr/>";

    echo "<pre>";
    print_r($service_order);
    echo "</pre>";
    echo "<hr/>";

    echo "<pre>";
    print_r($product_info);
    echo "</pre>";
    echo "<hr/>";
    */




    // =======================================================================

    $user_info  = ' - ';
    $user_link  = ' - ';
    $user_info  =  $full_user_name . " (" . $topup_order_info['username'] . ")";
    $user_link  = "/admin/edit_account/"  . $topup_order_info['user_id'];


    $order_time = '';
    $order_time = date('d/m/Y',strtotime($topup_order_info['order_time']));

    $order_service_class = ' - ';
    $schedule_api        = ' - ';
    $order_service_class = $topup_order_info['service_class_name'] . " (" . $topup_order_info['service_class_id'] . ")";
    $schedule_api        = $topup_order_info['schedule_api_status'] . " - " . $topup_order_info['schedule_api_message'];

    $order_link          = ' - ';
    $order_link          = "/admin/manage_order/" . $topup_order_info['order_id'];
    $product_link        = ' - ';
    $product_link        = '/admin/edit_product/' . $topup_order_info['product_id'];

    $adsl_username       = $topup_order_info['adsl_username'];


    $real_class          = ' - ';
    $real_class          = $topup_order_info['real_class_name'] . " (" . $topup_order_info['real_class_id'] . ")";

    $topup_class         = ' - ';
    $topup_api           = ' - ';
    $topup_class         = $topup_order_info['topup_class_name'] . " (" . $topup_order_info['topup_class_id'] . ")";
    $topup_api           = $topup_order_info['api_status'] . " - " . $topup_order_info['api_message'];



    $payment_status      = ' - ';
    $payment_status      = $topup_order_info['payment_status']; // select

    $payment_method      = ' - ';
    $payment_method      = trim($topup_order_info['payment_method']);

    $payment_method_real = ' - ';

    // credit_card_auto
    // debit_order
    // credit_card
    // eft

    /*
            if($type == 'credit_card_auto'){
                                $msg = 'Auto Billing using your Credit Card';
                            }elseif($type == 'credit_card'){
                                $msg = 'Once off payment from your Credit Card';
                            }elseif($type == 'debit_order'){
                                $msg = 'Debit Order';
                            }elseif($type == 'eft'){
                                $msg = 'EFT';
                            }

     */

    switch ($payment_method) {

        case 'credit_card_auto': $payment_method_real = 'Auto Billing using your Credit Card';  break;
        case 'credit_card':      $payment_method_real = 'Once off payment from your Credit Card';  break;
        case 'debit_order':      $payment_method_real = 'Debit Order';  break;
        case 'eft':              $payment_method_real = 'EFT';  break;
        default                : $payment_method_real = $payment_method; break;
    }


    $payment_method = $payment_method_real;


    $price               = ' - ';
    $price               = "R" . number_format(round($topup_order_info['price'], 2), 2);


    $topup_level         = $topup_order_info['topup_level'];

    // ==========================================================================================
    ?>


    <?php


    echo form_open('admin/update_topup_order', array('class' => 'form-horizontal','id' => 'manage_order_form'));
    ?>
    <input type="hidden" name="topup_order_id" value="<?php echo $topup_order_info['id']; ?>" />
    <br/>
    <div class="row">
        <div class="form-group">
            <label class="control-label col-lg-3">
                User :
            </label>
            <div  class="col-lg-6 shif-fix">
                <?php echo "<a href ='". $user_link ."' >" . $user_info . "</a>"; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                Time :
            </label>
            <div class="col-lg-6 shif-fix">
                <?php echo $order_time; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                TopUp config :
            </label>

            <div class="col-lg-6 shif-fix">

                <p><?php echo $topup_info['topup_name'];  ?></p>
                <p><?php echo $topup_info['class_name'] . " (" . $topup_info['class_id'] . ")"; ?> </p>
                <p><?php echo $topup_info['topup_description'];  ?> </p>
                <p><?php echo "<a href='/admin/edit_topup/" . $topup_info['topup_id'] . "' >Edit TopUp configuration</a>"; ?></p>

             </div>
        </div>


        <div class="form-group">
            <label class="control-label col-lg-3">
                Original service :
            </label>

            <div class="col-lg-6 shif-fix">

                <p> <?php echo $service_order['date']; ?>  </p>
                <p> <?php echo $service_order['billing_cycle']; ?>  </p>
                <p> <?php echo "<a href='/admin/manage_order/" . $service_order['id'] . "'>Edit Service order</a>" ?> </p>


            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                Product :
            </label>

            <div class="col-lg-6 shif-fix">

                <p> <?php echo $product_info['product_settings']['name']; ?>  </p>
                <p> <?php echo $product_info['product_settings']['class'] . " (" . $product_info['product_settings']['class_id']  . ")"; ?>  </p>
                <p> <?php echo "<a href='/admin/edit_product/" . $product_info['product_settings']['id'] . "'>Edit Product</a>"; ?> </p>

           </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                ISDSL Username :
            </label>
            <div class="col-lg-6 shif-fix">
                <?php echo $adsl_username; ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                ISDSL previous class :
            </label>
            <div class="col-lg-6 shif-fix">

                <p><?php echo  $order_service_class; ?></p>
                <p><?php echo $schedule_api; ?> </p>

            </div>
        </div>
        <!--
        <div class="form-group">
            <label class="control-label col-lg-3">
                Schedule status:
            </label>
            <div class="col-lg-6">
                <?php // echo $schedule_api; ?>
            </div>
        </div>
        -->


        <div class="form-group">
            <label class="control-label col-lg-3">
                TopUp assign process :
            </label>
            <div class="col-lg-6 shif-fix">
                <p><?php echo $topup_class; ?></p>
                <p><?php echo $topup_api; ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                TopUp level :
            </label>
            <div class="col-lg-6 shif-fix">
                <?php echo $topup_level; ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                Payment method :
            </label>
            <div class="col-lg-6 shif-fix">
                <?php echo $payment_method; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                Payment status :
            </label>
            <div class="col-lg-3">
                <?php
                     echo form_dropdown('payment_status', $payment_status_list, $payment_status, 'class="form-control col-lg-3" id="payment-status-element"');
                ?>
            </div>
        </div>
        <div class="form-group" hidden="hidden" id="revert-div">
            <label class="control-label col-lg-3">
                Revert class :
            </label>
            <div class="col-lg-3">
                <?php
                $revert_class_options = array('yes' => 'Yes', 'no' => 'No');
                echo form_dropdown('revert_flag', $revert_class_options, 'no', 'class="form-control col-lg-3"');
                ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                Payment price :
            </label>
            <div class="col-lg-6 shif-fix">
                <?php echo $price; ?>
            </div>
        </div>
    </div>

<?php

    if (!empty($topup_order_info['revert_time'])){
   ?>
        <div class="form-group">
            <label class="control-label col-lg-3">
                Revert info :
            </label>
            <div class="col-lg-6 shif-fix">
                <p><?php echo $topup_order_info['revert_time'] ?></p>
                <p><?php echo $topup_order_info['revert_class_name'] . " (" . $topup_order_info['revert_class_id'] . ")"; ?></p>
                <p><?php echo $topup_order_info['revert_type']; ?></p>
                <p><?php echo $topup_order_info['revert_api_status'] . " - " . $topup_order_info['revert_api_message']; ?></p>
            </div>
        </div>



<?php
    }


    /*

       [revert_time]         =>
       [revert_class_id]     =>
       [revert_class_name]   =>
       [revert_type]         =>
       [revert_api_status]   =>
       [revert_api_message]  =>

     */

    ?>








    <input type="hidden" name="id" value="<?php echo $order_id ?>" />
    <?php

   /*
    $order_data['realm'] = $order_realm;
    foreach ($order_data as $i=>$o) {
        if (!$o) {
            $o = '';
        }
        if ($i == 'price' || $i == 'pro_rata_extra') {
            $pre = '<div class="input-group"><span class="input-group-addon">R</span>';
            $post = '</div>';
        } else {
            $pre = '';
            $post = '';
        }
        if (isset( $order_key[$i])) {
            ?>
            <div class="form-group">
                <label class="control-label col-lg-3">
                    <?php echo $order_key[$i]; ?>
                </label>
                <div class="col-lg-7">
                    <?php if ($i == 'product') {
                        if (!empty($product_list)) {
                            $products = array();
                            foreach ($product_list as $p=>$l) {
                                $products[$l['id']] = $l['name'];
                            }
                            echo form_dropdown('product', $products, $o, 'class="form-control"');
                        }
                    }  elseif ($i == 'realm'){

                        if (!empty($realm_list)) {
                            $realms = array();
                            foreach ($realm_list as $p=>$l) {
                                $realms[$l['realm']] = $l['realm'];
                            }
                            if (empty($o)){
                                $realms[''] = '';
                            }
                            echo form_dropdown('realm', $realms, $o, 'class="form-control"');
                        }

                    } elseif ($i == 'realm'){

                        if (!empty($realm_list)) {
                            $realms = array();
                            foreach ($realm_list as $p=>$l) {
                                $realms[$l['realm']] = $l['realm'];
                            }
                            if (empty($o)){
                                $realms[''] = '';
                            }
                            echo form_dropdown('realm', $realms, $o, 'class="form-control"');
                        }


                    }   else if ($i == 'status') {
                        $statuses = array ('active','pending', 'deleted', 'suspended', 'expired', 'pending cancellation');
                        $status_list = array();
                        foreach ($statuses as $s) {
                            $status_list[$s] = ucfirst($s);
                        }
                        echo form_dropdown('status', $status_list, $o, 'class="form-control"');
                        ?>
                        <div class="help-block">This will superficially change the status.To cancel an order, click the button above; To activate an order click the button below.</div>
                    <?php
                    }elseif ($i == 'account_username'){
                        echo '<input type="text" name="account_username" id="account_username" value="'.$o.'" class="form-control"/>';
                        echo '<label class="error" id="acc_name">This field is required.</label>';
                    }elseif ($i == 'account_password'){
                        echo '<input type="text" name="account_password" id="account_password" value="'.$o.'" class="form-control"/>';
                        echo '<label class="error" id="acc_pwd">This field is required.</label>';
                    }elseif ($i == 'change_flag'){
                        echo '<input type="checkbox" name="change_flag" id="change_flag" value="1" ';
                        if($o == 1){
                            echo 'checked="checked"';
                        }
                        echo '> <b>Allow user to change his password .</b>';
                    }elseif ($i == 'display_usage'){
                        echo '<input type="checkbox" name="display_usage" id="display_usage" value="1" ';
                        if($o == 1){
                            echo 'checked="checked"';
                        }
                        echo '> <b>Display the usage stats in the user panel.</b>';
                    }elseif ($i == 'cancel_flage'){
                        echo '<input type="checkbox" name="cancel_flage" id="cancel_flage" value="1" ';
                        if($o == 1){
                            echo 'checked="checked"';
                        }
                        echo '> <b>Client can auto cancel a product.</b>';
                    }elseif ($i == 'payment_method'){
                        if($o == 'debit_order'){
                            echo "<label>Debit Order</label>";
                        }elseif($o == 'eft'){
                            echo "<label>EFT</label>";
                        }elseif ($o == 'credit_card'){
                            echo "<label>Credit Card</label>";
                        }elseif ($o == 'credit_card_auto'){
                            echo "<label>Auto Billing using your Credit Card</label>";
                        }
                    }else {
                        echo $pre;
                        $placeholder = '';
                        $help_text = '';
                        ?>
                        <input type="text" placeholder="<?php echo $placeholder ?>" name="<?php echo $i ?>" class="form-control" value="<?php echo $o ?>" id="<?php echo $i?>">
                        <?php echo $help_text; echo $post;
                    } ?>
                </div>
            </div>
        <?php
        }
    }

    */
    ?>
    <div style="text-align:center">
        <input type="submit" class="btn btn-large btn-primary" value="Update Order"/>
    </div>
    <?php


    echo form_close();

    if ($order_data['status'] == 'pending') { ?>
        <div class="well" style="margin-top:25px;text-align:center;">
            <strong>This order is currently pending, which means it hasn't applied the addRealmAccount function yet.</strong>
            <div style="margin-top: 10px;">
                <?php echo anchor("admin/activate_order/$order_id", 'Activate and Add Realm Account', 'class="btn btn-default active_order"') ?>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="alert alert-warning">
        <strong>TopUp Order not found.</strong> It seems that there is no order with that ID!
    </div>
<?php } ?>