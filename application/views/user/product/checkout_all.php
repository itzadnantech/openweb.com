<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h2>Final Checkout</h2>
        </div>
<?php if(empty($cart_product_data)) { ?>

    <div class="alert alert-warning">Your cart seems to be empty! Choose some products from the menu above to continue!</div>
<?php } else {

    echo form_open('product/process_cart', array('id'=>'billing_form', 'class' => 'form-horizontal'));
?>

<div class="col-lg-12 well">
<div class="col-lg-6">

    <legend>Product Information</legend>

    <?php

    if(!empty($account_username)){

        $account_username = $account_username;
        $acc_username_format = 'Username: '.$account_username.'@'.$realm;
        echo '<div style="font-weight: bold;padding-left: 10px;font-size:16px;">'.$acc_username_format.'</div><br/>';

    }else{

        $account_username = '';
    }


    if(!empty($account_password)){

        $account_password = $account_password;
    }else{
        $account_password = '';
    }



    if(isset($billing_data) && !empty($billing_data)){

        $billing_data = $billing_data;
    }else{
        $billing_data = "";
    }

    ?>

    <input type="hidden" value="<?php echo $account_username;?>" name="acc_username">
    <input type="hidden" value="<?php echo $account_password;?>" name="acc_password">
    <input type="hidden" value="<?php echo $choose_cycle;?>" name="choose_cycle">
    <input type="hidden" value="<?php echo $realm; ?>" name="acc_realm" >

    <?php

    $total_cost = 0;
    $total_cost_this_month = 0;


    if (!empty($cart_product_data)) {

        foreach ($cart_product_data as $product_data) {
            $product_id = $product_data['id'];

            if (isset($product_data['name'])) {

                $name = $product_data['name'];
            } else {
                $name = '';
            }

            /* if (isset($product_data['billing_cycle'])) {
                if (isset($billing_cycles[$product_data['billing_cycle']])) {

                    $billing_cycle = $billing_cycles[$product_data['billing_cycle']];
                } else {

                    $billing_cycle = ucwords($product_data['billing_cycle']);
                }
            } else {

                $billing_cycle = 'Monthly';
            } */

            if(isset($choose_cycle)){

                $billing_cycle = $choose_cycle;
            }else{

                $billing_cycle = 'Monthly';
            }



            if(isset($product_data['price'])){

                $price = $product_data['price'];
            }else{

                $price = 0;
            }



            $cost = 'R' . number_format($price, 2);
            $tmpl = array ( 'table_open'  => '<table class="table">' );
            $this->table->set_template($tmpl);
            $this->table->set_heading(array('Product Details', 'Pricing Structure', 'Cost'));


            if (isset($product_data['pro_rata_extra'])) {

                $pro_rata_extra = $product_data['pro_rata_extra'];
            } else {

                $pro_rata_extra = 0.00;
            }

            //print_r($pro_rata_extra);die;
            /*
            Apply discount
            */

            if (isset($this->site_data['discount']) && trim($this->site_data['discount']) != '') {

                $discount = $this->site_data['discount'];
            } else {

                $discount = '0';
            }

            $original_price = 'R' . number_format(round($price, 2), 2);

            if (trim($discount) != '') {

                $price_percent = 100 - $discount;
                $price_percent = $price_percent / 100;
                $discounted_price = ($discount/100) * $price;
                $user_price = $price * $price_percent;
                $user_price_out = 'R' . number_format(round($user_price, 2), 2) . '</span>';

                if ($pro_rata_extra != 0.00) {

                    $pro_rata_extra = number_format(round($pro_rata_extra * $price_percent, 2), 2);
                }

            } else {

                $discount = 0;
                $user_price = $price;
                $user_price_out = $cost;
            }


            $this->table->add_row( array($name, $billing_cycle, "$original_price" ));
            $total_cost = $total_cost + $user_price;
            $total_cost_this_month = $total_cost_this_month + $pro_rata_extra;

        }

        $total_cost =  number_format(round($total_cost, 2), 2);
        $total_cost_this_month =  number_format(round($total_cost_this_month, 2), 2);


        if ($discount != 0) {

            $discount_info =  "<div class='pull-right'>Your client discount is $discount%<div>";
            $this->table->add_row( array( '', $discount_info, "<strong>-R$discounted_price</strong>" ));
        } else {

            $discount_info = '';
        }

        $next_month = date("M Y",strtotime("+1 months"));
        $total_cost_out = "<div class='pull-right'>Total $billing_cycle Cost (Begins $next_month)</div>";

        $this_month_cost_out = "<div class='pull-right'>Total Cost this Month (Pro-Rata)</div>";

        if ($billing_cycle != 'Once-Off')
             $this->table->add_row( array( '', $total_cost_out, "<strong>R$total_cost</strong>" ));
        $this->table->add_row( array( '', $this_month_cost_out, "<strong>R$total_cost_this_month</strong>" ));


        $this->session->set_userdata('total_price', $total_cost_this_month);
        echo $this->table->generate();

        ?>

        <input type="hidden" value="<?php echo $total_cost_this_month;?>" name="price">
        </div>
        <div class="col-lg-6">
        <legend>Payment Information</legend>
        <div class="form-group">

            <label for="contact_number" class="control-label col-lg-3">Payment Type</label>
            <div class="col-lg-8" style="font-size: 18px;" id="payment_radio">
                <?php

                if(isset($payment_methods) && !empty($payment_methods)){

                    //var_dump($payment_methods);
                    foreach ($payment_methods as $k => $v){

                        if($v['payment_method'] == 'credit_card_auto'){

                            $msg = 'Auto Billing using your Credit Card';
                        }elseif($v['payment_method'] == 'credit_card'){

                            $msg = 'Once off payment from your Credit Card';
                        }elseif($v['payment_method'] == 'debit_order'){

                            $msg = 'Debit Order';
                        }elseif($v['payment_method'] == 'eft'){

                            $msg = 'EFT';
                        }

                       if ($v['payment_method'] != 'credit_card')
                         echo '<div class="radio"><label><input type="radio" name="payment_method" id="'.$v['payment_method'].'" value="'.$v['payment_method'].'" class="payment">'.$msg.'</label></div>';

                        if ($v['payment_method'] == 'credit_card')
                             echo "<div class='radio'><label><input type='radio' name='payment_method' id='payfast-id' value='payfast-live' class='payment'> ". $msg . "</label></div>";
                    }


                   if ($username == 'test-vvv'){

                        echo "<div class='radio'><label><input type='radio' name='payment_method' id='payfast-id' value='payfast-sandbox' class='payment'>PayFast (test - sandbox)</label></div>";
                       // echo "<div class='radio'><label><input type='radio' name='payment_method' id='payfast-id' value='payfast-live' class='payment'>Once off payment from your Credit Card</label></div>";

                  }


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
        echo form_submit('confirm','I Accept & Confirm - Create Account','style="margin-right:5px;height:34px;" class="btn btn-large btn-success submit"');// glyphicon glyphicon-ok
        echo anchor('product/clear_cart', 'Cancel Order', 'class="btn btn-large btn-danger"');// glyphicon glyphicon-remove

    }
    echo form_close();

}

?>


<div style="display: none;">
<?php

//
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
    </div>
</div>
</div>
