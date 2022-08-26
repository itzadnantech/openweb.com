<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
<h3>Topup your LTE-A account instantly:</h3></div>
<p>Kindly select the account you would like to topup with your credit card below.  For EFT topups, kindly email <span id="a-email">admin@openweb.co.za</span></p>

<?php echo form_open("#", array('id'=>'billing_form', 'class' => 'form-horizontal')); ?>

<div class="form-group">
    <label for="order" class="control-label col-lg-2">Choose order:</label>
    <div class="col-lg-10">
        <select id="order" class="form-control valid"">
            <?php
            foreach ($orders as $order) {

                if($order['id'] == 0) {
                    echo '<option value="'.$order['id'].'" selected>'."Choose order".'</option>';
                } else {
                    echo '<option value="' . $order['id'] . '">' . $order['fibre']["product_name"] . '(' . $order['fibre']["fibre_data_username"] . ')' . '</option>';
                }
            }
            ?>
        </select>
    </div>
</div>
<div id="plan_data_cell">
    <div class="form-group">
        <label for="plan" class="control-label col-lg-2">Choose top up:</label>
        <div class="col-lg-4">
            <select id="plan_cell" class="form-control valid"">
            <?php
            foreach ($plans_cell as $plan) {

                echo '<option value="'.$plan['topup_id'].'">'.$plan['topup_name'].'</option>';
            }
            ?>
            </select>
        </div>

        <label for="amount" class="control-label col-lg-2">Amount:</label>
        <div class="col-lg-4">
            <input type="text" id="" class="form-control amount" disabled>
        </div>
    </div>

</div>

<div id="plan_data_rain">
    <div class="form-group">
        <label for="plan" class="control-label col-lg-2">Choose top up:</label>
        <div class="col-lg-4">
            <select id="plan_rain" class="form-control valid"">
            <?php
            foreach ($plans_rain as $plan) {

                echo '<option value="'.$plan['topup_id'].'">'.$plan['topup_name'].'</option>';
            }
            ?>
            </select>
        </div>

        <label for="amount" class="control-label col-lg-2">Amount:</label>
        <div class="col-lg-4">
            <input type="text" id="" class="form-control amount" disabled>
        </div>
    </div>

</div>

<div id="billing_info" >
    <div class="col-lg-12 text-center">
    <?php
        echo form_submit('confirm','Proceed with Payfast','class="btn btn-large btn-success submit text-center"');
    ?>
    </div>
</div>
<?php echo form_close();
echo form_open("https://" . $live_payfast_host . "/eng/process", array('id'=>'payfast_live', 'class' => 'form-horizontal'));
    foreach ($payfast_data as $key => $value){

        echo  "<input name='". $key . "' value='" . $value."' type='hidden' >";
    }

echo form_close();   ?>
        <a href="<?php echo $ajax_url; ?>" id="form-link" hidden></a>
</div>
</div>
</div>
<style>
    #a-email{
        color: #428bca;
    }
</style>