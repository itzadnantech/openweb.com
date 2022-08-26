<script>
$(document).ready(function() {

    // Disable PayFast for a while
    function disablePayfastPayment(){
        $('input#payfast-id').attr("disabled", true);
    }
    // disablePayfastPayment();

    $('#credit_card_div').hide();
    $('#debit_order_div').hide();
    $('.radio').click(function () {

        var payment_type = $('input:radio[name="payment_method"]:checked').val();
        if ((payment_type == 'payfast-sandbox') || (payment_type == 'payfast-live')) {

            $('.virtual').show();
            $('.submit').hide();
            $('#credit_card_div').hide();
            $('#debit_order_div').hide();
            return;
        }

        if (payment_type == 'credit_card') {

            $('.virtual').show();
            $('.submit').hide();
            $('#credit_card_div').hide();
            $('#debit_order_div').hide();

        } else {

            if (payment_type == 'credit_card_auto') {
                $('#credit_card_div').show();
                $('#debit_order_div').hide();

            } else if (payment_type == 'debit_order') {

                $('#debit_order_div').show();
                $('#credit_card_div').hide();

            } else {

                $('#credit_card_div').hide();
                $('#debit_order_div').hide();

            }

            $('.submit').show();
            $('.virtual').hide();
        }
    });


    $('#topup_order_form').submit(function () {

        var val = $('input:radio[name="payment_method"]:checked').val();
        if (val == null) {
            $('.error').show();

            return false;
        } else {

            $('.error').hide();
            if (val == 'credit_card_auto') {

                var card_num = $('#card_num').val();
                var cvc = $('#cvc').val();
                if (card_num != "" && cvc != "") {

                    return true;
                } else if (card_num == "") {

                    $('#card_num_error').show();
                    return false;
                } else if (cvc == "") {

                    $('#cvc_error').show();
                    return false;
                }

            } else if (val == 'debit_order') {

                var bank_name = $('#bank_name').val();
                var bank_account_number = $('#bank_account_number').val();
                var bank_branch_code = $('#bank_branch_code').val();

                if (bank_name != "" && bank_account_number != "" && bank_branch_code != "") {

                    return true;
                } else if (bank_name == "") {

                    $('#bank_name_error').show();
                    return false;
                } else if (bank_account_number == "") {

                    $('#bank_account_number_error').show();
                    return false;
                } else if (bank_branch_code == "") {

                    $('#bank_branch_code_error').show();
                    return false;
                }

            } else {
                return true;

            }
        }

    });


    $('.virtual').click(function () {

        // $('#vcs_form').submit();
        // get amount
        var payfastAmount = $("input[name='amount']").val();

        if (payfastAmount == 0) {
            alert('This payment method is not available for R0 amount');
            return;
        }

        $("#payment_radio input.payment").attr('disabled', 'disabled');
        $(this).attr('disabled', 'disabled');

        var payment_type = $('input:radio[name="payment_method"]:checked').val();
        // var ajax_url = "";

        if (payment_type == 'payfast-sandbox') {

            var ajax_object = send_ajax('SANDBOX');
            ajax_object.success(function (answer) {

                $('#payfast_sandbox').submit();
            });
            //$('#payfast_sandbox').submit();

        } else {
            if (payment_type == 'payfast-live') {

                var ajax_result = send_ajax();
                ajax_result.success(function (answer) {

                    $('#payfast_live').submit();
                });
                // $('#payfast_live').submit();
            }
        }

        $("#payment_radio input.payment").removeAttr("disabled");
        $(this).removeAttr('disabled');
        // console.log("disabled off");
        return false;

    });


    function get_params(sandbox) {

        var id = 'payfast_live';
        if (sandbox == 'SANDBOX') {
            id = 'payfast_sandbox';
        }

        // Sandbox
        var param_children = $("form#" + id).children('input');
        var param_length = param_children.length;
        var param_array = {};

        for (var i = 0; i < param_length; i++) {

            param_array[$(param_children[i]).attr('name')] = param_children[i].value;
        }

        return param_array;

    }

    function get_ajax_url(url) {

        var ajax_url = url.substring(0, url.length - 6); // 6 - for 'notify'
        return ajax_url + "topup_prevalid";

    }

    function get_username() {

        var username = "<?php echo $username; ?>"
        return $.trim(username);
    }

    function get_order_signature() {

        var signature = "<?php echo $order_signature; ?>";
        return signature;
    }


    function get_order_object() {
        var order_obj = {};

        order_obj['adsl_username']   = "<?php echo $order_data_array['adsl_username']; ?>";
        order_obj['product_id']      = "<?php echo $order_data_array['product_id']; ?>";
        order_obj['order_id']        = "<?php echo $order_data_array['order_id']; ?>";
        order_obj['topup_config_id'] = "<?php echo $order_data_array['topup_config_id']; ?>";
        order_obj['topup_name']      = "<?php echo $order_data_array['topup_name']; ?>";
        order_obj['payment_type']    = "<?php echo $order_data_array['payment_type']; ?>";
        order_obj['topup_level']     = "<?php echo $order_data_array['topup_level']; ?>";

        return order_obj;

    }

    function send_ajax(sandbox) {

        var pre_signature = $.trim("<?php echo $pre_live; ?>");
        if (sandbox == 'SANDBOX') {
            pre_signature = $.trim("<?php echo $pre_sandbox; ?>");
        }

        var params = get_params(sandbox);
        var ajax_url = get_ajax_url(params['notify_url']);
        var username = get_username();



        var order_signature = get_order_signature();
        var order_object = get_order_object();



        return $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                params: JSON.stringify(params),
                order_params: JSON.stringify(order_object),
                user: username,
                pre_signature: pre_signature,
                order_signature: order_signature,

            }, /*
             success: function (answer){

             var  answerObj = $.parseJSON(answer);

             } */
        });

        // return function_answer;
    }
});
</script>

<h2>TopUp Checkout</h2>

<!--New service information  -->
<fieldset>
    <?php


    // TopUp information
    if(isset($topup_conf)) {

        $topup_id   = $topup_conf['topup_id'];
        $topup_iteration = $topup_conf['iteration'];

        $topup_name = $topup_conf['topup_name'];
        $topup_description = $topup_conf['topup_description'];
        $topup_class_id = $topup_conf['class_id'];
        $topup_class_name = $topup_conf['class_name'];
        $topup_price = number_format(round($topup_conf['topup_price'], 2), 2);
        $topup_payments = $topup_conf['payments'];

    }

        // PHP PART

        // Get account username
        if(!empty($account_username) && !empty($realm)){
            $acc_username_format = '<b>Username: </b>'.$account_username.'@'.$realm;
        }else{
            $acc_username_format = '';
        }

        // Product Description
        if(!empty($current_product_description)){
            $row_product_description = '<b>Current product : </b>' . $current_product_description;
        }else{
            $row_product_description = '';
        }

        $row_topup_name = '<b>TopUp name : </b>' . $topup_name;
        $row_topup_description = '<b>TopUp description : </b>'. $topup_description;
        $row_topup_price = '<b>TopUp price : </b> R'. $topup_price;

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //          Payfast Variables
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        // $sandbox_payfast_host;
        // $live_payfast_host;
        // $sandbox_payfast_data
        // $payfast_data;


/*
        if(isset($billing_data) && !empty($billing_data)){
            $billing_data = $billing_data;
        }else{
            $billing_data = "";
        }
*/




    echo form_open('user/order_topup_process', array('id'=>'topup_order_form', 'class' => 'form-horizontal'));

        ?>

    <div class="col-lg-12 well">

        <!-- Order Data -->
        <div class="col-lg-6">


            <legend>Product Information</legend>
        <input type="hidden" value="<?php echo $account_username;?>" name="acc_username">
        <input type="hidden" value="<?php echo $realm; ?>" name="acc_realm" >
        <input type="hidden" value="<?php echo $topup_id; ?>" name="topup_id" >
        <input type="hidden" value="<?php echo $topup_iteration; ?>" name="iteration" >

        <input type="hidden" value="<?php echo $order_id; ?>" name="order_id" >


         <?php
            echo '<div style="padding-left: 10px;font-size:16px;">'. $acc_username_format .'</div><br/>';
            echo '<div style="padding-left: 10px;font-size:16px;">'. $row_product_description .'</div><br/>';
            /*
                 TopUp which already assigned
             */
            echo '<div style="padding-left: 10px;font-size:16px;">' . $row_topup_name .'</div><br/>';
            echo '<div style="padding-left: 10px;font-size:16px;">' . $row_topup_description .'</div><br/>';
            echo '<div style="padding-left: 10px;font-size:16px;">' . $row_topup_price .'</div><br/>';
            /*

                    Price
             */
             ?>
            <input type="hidden" value="<?php echo $total_cost_this_month;?>" name="price">

        </div>

        <!-- Payments -->
        <div class="col-lg-6">
                <legend>Payment Information</legend>
                <div class="form-group">

                    <label for="contact_number" class="control-label col-lg-3">Payment Type</label>
                    <div class="col-lg-8" style="font-size: 18px;" id="payment_radio">
                    <?php
                        foreach ( $topup_payments as $type => $bool_value){

                            if($type == 'credit_card_auto'){
                                $msg = 'Auto Billing using your Credit Card';
                            }elseif($type == 'credit_card'){
                                $msg = 'Once off payment from your Credit Card';
                            }elseif($type == 'debit_order'){
                                $msg = 'Debit Order';
                            }elseif($type == 'eft'){
                                $msg = 'EFT';
                            }

                            if ($type != 'credit_card')
                                echo '<div class="radio"><label><input type="radio" name="payment_method" id="'. $type .'" value="'. $type .'" class="payment">'.$msg.'</label></div>';
                            if ($type == 'credit_card')
                                echo "<div class='radio'><label><input type='radio' name='payment_method' id='payfast-id' value='payfast-live' class='payment'> ". $msg . "</label></div>";

                        }


                        if ($username == 'test-vvv'){
                            echo "<div class='radio'><label><input type='radio' name='payment_method' id='payfast-id' value='payfast-sandbox' class='payment'>PayFast (test - sandbox)</label></div>";
                            // echo "<div class='radio'><label><input type='radio' name='payment_method' id='payfast-id' value='payfast-live' class='payment'>Once off payment from your Credit Card</label></div>";
                        }

                     ?>
                    </div>
                    <div style="color: #f62b2b; font-size: 25px;">*</div>

                </div>
                <div class="error" style="display: none;">Please choose a payment method.</div>

                <div id="credit_card_div">
                    <div class="form-group">
                        <label for="account_type" class="control-label col-lg-5">Name on Card</label>
                        <div class="col-lg-7">
                            <input type="text" class="form-control"id="name_on_card" name="name_on_card"  value="" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="account_type" class="control-label col-lg-5">Credit Card Number</label>
                        <div class="col-lg-7">
                            <input type="text" class="form-control" id="card_num" name="card_num"  value="" >
                            <label generated="true" class="error" id="card_num_error" style="display: none;">This field is required.</label>
                        </div>
                    </div>

                    <div class="form-group">

                        <label for="account_type" class="control-label col-lg-5">Expiry Date</label>
                        <div class="col-lg-3">

                            <select name="expires_month" class="form-control" id="expires_month">
                                <?php
                                    foreach (range('1', '12') as $month) {
                                        echo "<option value=$month>$month</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-4">

                            <select class="form-control" id="expires_year" name="expires_year">
                                <?php
                                    foreach (range(date("Y"), date("Y") + 10) as $year) {
                                        echo "<option value=$year>$year</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="account_type" class="control-label col-lg-5">CVC</label>
                        <div class="col-lg-7">
                            <input type="text" class="form-control" id="cvc" name="cvc"  value="" >
                            <label generated="true" class="error" id="cvc_error" style="display: none;">This field is required.</label>
                        </div>
                    </div>
                </div>

                <div id="debit_order_div">
                    <div class="form-group">
                        <label for="account_type" class="control-label col-lg-5">Bank Name</label>
                        <div class="col-lg-7">
                            <input type="text" class="form-control"id="bank_name" name="bank_name"  value="" >
                            <label generated="true" class="error" id="bank_name_error" style="display: none;">This field is required.</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="account_type" class="control-label col-lg-5">Account Number</label>
                        <div class="col-lg-7">
                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number"  value="" >
                            <label generated="true" class="error" id="bank_account_number_error" style="display: none;">This field is required.</label>

                        </div>
                    </div>
                    <div class="form-group">

                        <label for="account_type" class="control-label col-lg-5">Account Type</label>
                        <div class="col-lg-7">
                            <select class="form-control" id="bank_account_type" name="bank_account_type">
                                <?php
                                    $type = array('Cheque/Current'=>'Cheque / Current','Savings'=>'Savings','Transmission'=>'Transmission');
                                    foreach ($type as $v){
                                        echo "<option value=$v>$v</option>";
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="account_type" class="control-label col-lg-5">Branch Code</label>
                        <div class="col-lg-7">

                            <input type="text" class="form-control" id="bank_branch_code" name="bank_branch_code"  value="" >
                            <label generated="true" class="error" id="bank_branch_code_error" style="display: none;">This field is required.</label>

                        </div>
                    </div>
                </div>
        </div>
  </div>
  <?php
            echo "<a href='#' class='btn btn-large btn-success virtual' style='display:none;margin-right:5px;'>Proceed to Payment</a>";

            //echo anchor('javascript:void(0);', 'Proceed to Payment', 'class="btn btn-large btn-success virtual" style="display:none;margin-right:5px;"');
            echo form_submit('confirm','I Accept & Confirm','style="margin-right:5px;height:34px;" class="btn btn-large btn-success submit"');// glyphicon glyphicon-ok
            echo anchor('/user/orders', 'Cancel Order', 'class="btn btn-large btn-danger"');// glyphicon glyphicon-remove

        echo form_close();

  ?>
  <div style="display: none;">
    <?php

        if ($username == 'test-vvv') {
            echo form_open("https://" . $sandbox_payfast_host . "/eng/process", array('id' => 'payfast_sandbox'));
            foreach ($sandbox_payfast_data as $key => $value) {

                echo "<input name='" . $key . "' value='" . $value . "' type='hidden' >";
            }
            echo form_close();
        }
        // ===============================================================================
        // ===============================================================================
        echo form_open("https://" . $live_payfast_host . "/eng/process", array('id'=>'payfast_live'));
        foreach ($payfast_data as $key => $value){

            echo  "<input name='". $key . "' value='" . $value."' type='hidden' >";
        }
        echo form_close();

    ?>
  </div>
