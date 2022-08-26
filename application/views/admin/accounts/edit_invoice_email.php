<?php
if (empty($user_data['edit_user'])) {
    $edit_user = '';
} else {
    $edit_user = $user_data['edit_user'];
}

if (empty($user_data['account_id'])) {
    $account_id = '';
} else {
    $account_id = $user_data['account_id'];
}

if (isset($success_message) && trim($success_message) != '' ) {
    echo "<div class='alert alert-success'>$success_message</div>";
}
if (isset($warning_message) && trim($warning_message) != '' ) {
    echo "<div class='alert alert-error'>$warning_message</div>";
}
if (isset($info_message) && trim($info_message) != '' ) {
    echo "<div class='alert alert-info'>$info_message</div>";
}

$options = array(
    '1'  => 'On',
    '0'  => 'Off',
);
if (isset($user_data['invoice_param'])){

    $selected = $user_data['invoice_param'];
} else {

    $selected = '0';
}


?>
    <h3>Edit invoice email for <?php echo $edit_user; ?></h3>
    <br/>
    <div class="container" >
       <div class="row">
            <?php echo form_open('admin/invoice_email', array('class' => 'form-inline')); ?>
            <fieldset>
                <div class="col-lg-4">
                    <input type="hidden" value="<?php echo isset($account_id) ? $account_id : ''; ?>" id="account_id" name="account_id"/>
                    <?php  echo form_dropdown('invoice_dropdown', $options, $selected, "class='form-control'"); ?>

                    <br> <br>
                     <input type="submit" name="" value="Save" class="btn btn-primary btn-lg">
                     <a href="/admin/edit_account/<?php echo $account_id; ?>"  name=""  class="btn btn-primary btn-lg" style="margin-left : 25px;">Back</a>

                </div>
            </fieldset>
            <?php echo form_close(); ?>
        </div>
    </div>



<?php
$user_data['edit_user'] = $edit_user;
$this->load->view('admin/accounts/account_form', $user_data);
?>