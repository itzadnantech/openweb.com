<script language="javascript" type="text/javascript">

</script>
<h3 style="padding-bottom: 30px;">Mailing List</h3>
<?php

if (!empty($success_message)) {
    echo "<div class='alert alert-success'>$success_message</div>";
}

if (!empty($error_message)) {

    echo "<div class='alert alert-danger'>$error_message</div>";
}

$options = array(
    '1'  => 'On',
    '0'  => 'Off',
);
$selected = '0';
if (isset($user_data['bulk_param'])){

    $selected = $user_data['bulk_param'];
}

echo form_open('user/email_param', array('class'=>'form-horizontal', 'id'=>'email_param_update'));
?>
<fieldset>
    <input type="hidden" id="account_id" name="account_id" value="<?php echo $user_id;?>"/>
    <div class="form-group">
        <div class="col-lg-3">
            <?php
                  echo form_dropdown('bulk_dropdown', $options, $selected, "class='form-control'");
            ?>
            <br/>
            <input type="submit" name="" value="Save" class="btn btn-primary btn-lg">
        </div>
    </div>
</fieldset>
<?php echo form_close();?>