<script type="text/javascript" language="javascript">
    function cancel() {
        location.href = "/admin/avios_stat";
    }

</script>
<legend>Edit Avios Awards</legend>
<fieldset>

    <?php echo form_open('admin/edit_award/'.$award_data['prep_id'], array('class' => 'form-horizontal','id' => 'form_filter_award')); ?>
    <div class="form-group">
        <label class="control-label col-lg-2">User ID Number:</label>
        <div class="col-lg-4">
            <?php echo $award_data['user_id'] ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">Order Id:</label>
        <div class="col-lg-4">
            <p><?php echo $award_data['order_id'] ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Reason:</label>
        <div class="col-lg-4">
            <select id="billing_code" name="billing_code">
                <?php
                foreach ($billingCodes as $code => $desc) {
                    if($code == $award_data['reason']) {
                        $options .= '<option value="' . $code . '" checked>' . $desc . '</option>';
                    } else {
                        $options .= '<option value="' . $code . '">' . $desc . '</option>';
                    }

                }
                echo $options;?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Username:</label>
        <div class="col-lg-4">
            <p><?php echo $award_data['username'] ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">First Name:</label>
        <div class="col-lg-4">
            <p><?php echo $award_data['first_name'] ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Last Name:</label>
        <div class="col-lg-4">
            <p><?php echo $award_data['last_name'] ?></>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Points:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="points" id="points" value="<?php echo $award_data['points'] ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Bonus Points:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="bonus" id="bonus" value="<?php echo $award_data['bonus_points'] ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Date:</label>
        <div class="col-lg-4">
            <p><?php echo $award_data['date_create'] ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2"></label>
        <div class="col-lg-2">
            <input type="submit" class="btn btn-sm btn-primary" value="Edit"/>
        </div>

        <label class="control-label col-lg-2"></label>
        <div class="col-lg-2">
            <input type="button" class="btn btn-sm btn-danger" value="Cancel" onclick="cancel()"/>
        </div>
    </div>

    <?php echo form_close();?>
</fieldset>