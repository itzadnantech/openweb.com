<script language="javascript" type="text/javascript">
    $(document).ready(function() {

        // TODO : add comments to functions

        /*
            suspend form block with selector (id#, classes with status suffix)
            inner select to get value "subscription_suspend_type"

            select doropdown with service status
         */

        var suspendTypeFormBlockSelector = "div#showmax_suspend_types_element";
        var baseClassFilterToHideSelector = ".showmax_types_";
        var subscriptionStatusSelector = "select[name='subscription_status']";
        //$(baseClassFilterToHideSelector + "suspended").hide();  // emulate suspended status and hide corresponding block

        function handleStatusChange(){

            // get current option from subscription type
            var currentService = $(subscriptionStatusSelector).val();
            toggleSuspendTypes(currentService);
        }

        function toggleSuspendTypes(currentService){

            $(suspendTypeFormBlockSelector).hide();
            $(baseClassFilterToHideSelector + currentService).show();
        };

        // process status change for showmax subscription.
        handleStatusChange();
        $(subscriptionStatusSelector).on("change", handleStatusChange);


    });
</script>

<?php

if(isset($user_id)){
    $account_id = $user_id;
}
?>
<div class="container" style="margin: 20px 0 30px 0;">
    <fieldset>
        <div class="col-lg-2">
            <?php echo anchor('admin/all_account', 'User List', array('class' => 'form-control btn btn-default')); ?>
        </div>
        <div class="col-lg-2">
            <?php echo anchor('admin/user_service/'.$account_id, 'Service List', array('class' => 'form-control btn btn-default')); ?>
        </div>
        <div class="col-lg-2">
            <?php echo anchor('admin/create_account', 'Create New', array('class' => 'form-control btn btn-default')); ?>
        </div>
    </fieldset>
</div>
<h3>Edit Order</h3>
<?php
// Show Success message if it exists
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
    ?>
    <div class="alert alert-success">
        <?php echo $messages['success_message'] ?>
    </div>
    <?php
}
?>

<?php
/*
 *
 * Debug section
 * ------------------------------
 *
   + username,
   + type of service (showmax subscription),
   + showmax activation code which was assigned
   + date of creation,
   + date of last change,
   + show current status ,


    show options


      $data['order_id'] = $order_id;
        $data['user_name'] = $showmax_subscription["user"];
        $data['user_id'] = $showmax_subscription["id_user"];
        $data['order_data'] = $showmax_subscription;

------- // ---------------------


echo "<hr/>";
echo "<pre> Order id :";
print_r($order_id);
echo "</pre>";

echo "<hr/>";
echo "<pre> User id :";
print_r($user_id);
echo "</pre>";


echo "<hr/>";
echo "<pre>";
print_r($order_data);
echo "</pre>";
echo '<br/><br/>';
*/
?>


<?php
    echo form_open('admin/update_showmax_order/' . $order_id, array('class' => 'form-horizontal','id' => 'manage_order_form'));
    ?>
    <input type="hidden" name="id" value="<?php echo $order_id ?>" />
    <input type="hidden" name="account_id" value="<?php echo $account_id ?>" />

    <!-- Service -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            Service
        </label>
        <div class="col-lg-7">
            <?php echo "ShowMax Subscription"; ?>
        </div>
    </div>


    <!-- Show Username -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            User
        </label>
        <div class="col-lg-7">
            <?php echo $order_data['user']; ?>
        </div>
    </div>

    <!-- Activation code -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            Activation code
        </label>
        <div class="col-lg-7">
            <input type="text" name="activation_code" value="<?php echo $order_data["showmax_subscription"]["activation_code"]; ?>" />
        </div>
    </div>

    <!-- Date of creation, -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            Creation time
        </label>
        <div class="col-lg-7">
            <?php echo $order_data["showmax_subscription"]["creation_time"]; ?>
        </div>
    </div>

    <!-- Date of last change -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            Last update
        </label>
        <div class="col-lg-7">
            <?php echo $order_data["showmax_subscription"]["last_update_time"]; ?>
        </div>
    </div>

    <!-- Current subscription_type  -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            Current subscription type
        </label>
        <div class="col-lg-7">
            <?php echo $order_data["showmax_subscription"]["subscription_type"]; ?>
        </div>
    </div>

    <!-- Current subscription_status  -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            Current subscription status
        </label>
        <div class="col-lg-7">
            <?php echo $order_data["showmax_subscription"]["subscription_status"]; ?>
        </div>
    </div>


    <!-- New Subscription_type -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            New Subscription type
        </label>
        <div class="col-lg-7">
            <?php
            echo form_dropdown("subscription_type", $subscription_types,
                $order_data["showmax_subscription"]["subscription_type"], "class='form-control'");
            ?>
        </div>
    </div>


    <!-- New subscription_status  -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            New subscription status
        </label>
        <div class="col-lg-7">
            <?php
            echo form_dropdown("subscription_status", $subscription_statuses,
                $order_data["showmax_subscription"]["subscription_status"],
                    "class='form-control'"); ?>
        </div>
    </div>

    <!-- Suspend options -->
    <!-- show only after admin chose 'suspend' status -->
    <div class="form-group showmax_types_suspended showmax_types_deleted" id="showmax_suspend_types_element">
        <label class="control-label col-lg-3">
            Suspend type :
        </label>
        <div class="col-lg-7">
            <?php
            echo form_dropdown("subscription_suspend_type", $subscription_termination,
                '', "class='form-control'");

            /*
            echo "<br/><p class='alert alert-info'>Only effective subscription when status is \"suspend\" <br/>
            Set \"Yes\" to immediately terminate users subscription (revoke access to content).</p>" ; */
            ?>
        </div>
    </div>


<?php

/*
Only effective when when status is "suspend"
Set "Yes" to immediately terminate users subscription (revoke access to content).
*/
?>




<?php





    /*
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
                            //print_r($product_list);
                            $products = array();
                            foreach ($product_list as $p=>$l) {
                                $products[$l['id']] = $l['name'];
                            }
                            echo form_dropdown('product', $products, $o, 'class="form-control"');
                        }
                    } else if ($i == 'status') {
                        $statuses = array ('active', 'pending', 'deleted', 'suspended', 'expired', 'pending cancellation');
                        $status_list = array();
                        foreach ($statuses as $s) {
                            $status_list[$s] = ucfirst($s);
                        }
                        echo form_dropdown('status', $status_list, $o, 'class="form-control"');
                        ?>
                        <div class="help-block">This will superficially change the status. To cancel an order, click the button above; to activate an order click the button below.</div>
                        <?php
                    }elseif ($i == 'change_flag'){
                        echo '<input type="checkbox" name="change_flag" id="change_flag" value="1" ';
                        if($o == 1){
                            echo 'checked="checked"';
                        }
                        echo '> <b>Allow user to change his passwords.</b>';
                    }else {
                        echo $pre;
                        if ($i == 'account_password') {
                            $help_text = "<input type='hidden' value='$o' name='hidden_password'></input>";
                            $placeholder= 'Current Password is '.trim($o);
                            $o = '';
                        }elseif($i == 'account_username'){
                            $help_text = "<input type='hidden' value='$o' name='hidden_username'></input>";
                            $placeholder= 'Current Username is '.trim($o);
                            $o = '';
                        }else {
                            $placeholder = '';
                            $help_text = '';
                        }
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
        <input type="submit" class="btn btn-large btn-success" value="Update Order" />
    </div>

    <?php form_close(); ?>
    <?php
    if ($order_data['status'] == 'pending') {
        ?>
        <div class="well" style="margin-top:25px;text-align:center;">
            <strong>This order is currently pending, which means it hasn't applied the addRealmAccount function yet.</strong>
            <div style="margin-top: 10px;">
                <?php echo anchor("admin/activate_order/$order_id", 'Activate and Add Realm Account', 'class="btn btn-default"') ?></div>
        </div>
        <?php
    }
    ?>
    <h3 style="margin-bottom:15px;">Other Options</h3>
    <?php

?>