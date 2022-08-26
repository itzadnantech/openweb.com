<script language="javascript" type="text/javascript">
    $(function() {
        $("#date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
        });
    });
</script>
<?php
if (isset($user_id)) {
    $account_id = $user_id;
}
?>
<div class="container" style="margin: 20px 0 30px 0;">
    <fieldset>
        <div class="col-lg-2">
            <?php echo anchor('admin/all_account', 'User List', array('class' => 'form-control btn btn-default')); ?>
        </div>
        <div class="col-lg-2">
            <?php echo anchor('admin/user_service/' . $account_id, 'Service List', array('class' => 'form-control btn btn-default')); ?>
        </div>
        <div class="col-lg-2">
            <?php echo anchor('admin/create_account', 'Create New', array('class' => 'form-control btn btn-default')); ?>
        </div>
    </fieldset>
</div>
<h3>Edit Mobile Order</h3>
<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
?>
    <div class="alert alert-success">
        <?php echo $messages['success_message'] ?>
    </div>
<?php
}
?>

<?php

if ($order_id && trim($order_id) != '') {
    echo form_open('admin/edit_mobile_order/' . $order_id, array('class' => 'form-horizontal', 'id' => 'manage_order_form'));
?>
    <input type="hidden" name="id" value="<?php echo $order_id ?>" />
    <?php

    foreach ($order_data as $title => $value) { ?>

        <div class="form-group">
            <label class="control-label col-lg-3">
                <?php if ($title == 'display_usage' || $title == 'change_flag' || $title == 'cancel_flage') { ?>
                <?php } else { ?>
                <?php echo $title;
                } ?>
            </label>
            <div class="col-lg-7">

                <?php
                if ($title == 'Product Name') { ?>
                    <input type="text" name="<?php echo strtolower(str_replace(' ', '_', $title)) ?>" class="form-control" value="<?php echo $value; ?>">


                <?php } elseif ($title == 'SIM Serial No') { ?>
                    <input type="text" name="<?php echo strtolower(str_replace(' ', '_', $title)) ?>" class="form-control" value="<?php echo $value; ?>">
                <?php } elseif ($title == 'Price') { ?>
                    <input type="text" name="<?php echo strtolower(str_replace(' ', '_', $title)) ?>" class="form-control" value="<?php echo $value; ?>">
                <?php } elseif ($title == 'Pro Rata Extra') { ?>
                    <input type="text" name="<?php echo strtolower(str_replace(' ', '_', $title)) ?>" class="form-control" value="<?php echo $value; ?>">

                <?php } elseif ($title == 'Status') {
                    $statuses = array('active', 'pending', 'deleted', 'suspended', 'expired', 'pending cancellation');
                    $status_list = array();
                    foreach ($statuses as $s) {
                        $status_list[$s] = ucfirst($s);
                    }
                    echo form_dropdown('status', $status_list, $value, 'class="form-control disabled"');
                } elseif ($title == 'Username') {
                    $name = strstr($value, '@', true);
                    if (empty($name)) {
                        $name = $value;
                    }
                ?>

                    <input type="text" name="<?php echo strtolower(str_replace(' ', '_', $title)) ?>" class="form-control" value="<?php echo $name; ?>">

                <?php } elseif ($title == 'display_usage') { ?>

                    <input type="checkbox" value="1" id="display_usage_fd_element" name="display_usage"> <b>Display the usage stats in the user panel.</b>

                <?php } elseif ($title == 'change_flag') { ?>

                    <input type="checkbox" value="1" id="change_flag_fd_element" name="change_flag" disabled> <b>Allow user to change his password.</b>

                <?php } elseif ($title == 'cancel_flage') { ?>

                    <input type="checkbox" checked="checked" value="1" id="cancel_flage" name="cancel_flage"> <b>Client can auto cancel a product.</b>

                <?php } ?>

            </div>
        </div>
    <?php
    }
    ?>

    <!--(ADSL, Fibre data) -->
    <div class="form-group">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" value="1" id="email_sms" name="email_sms" checked> <b>Send email and SMS to user</b>
        </div>
    </div>

    <!--(ADSL, Fibre data) -->
    <div class="form-group">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" value="1" id="write_to_log_id" name="write_to_log" checked> <b>Write to Active log</b>
        </div>
    </div>
    <div style="text-align:center">
        <input type="submit" class="btn btn-large btn-success" value="Update Order" />
    </div>

<?php form_close();
} else {
?>
    <div class="alert alert-warning">
        <strong>Order not found.</strong> It seems that there is no order with that ID!
    </div>
<?php
}
?>