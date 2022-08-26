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

if (isset($messages['success_message']) 
	&& trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}
if (isset($messages['warning_message']) 
	&& trim($messages['warning_message']) != '' ) {
	$m = $messages['warning_message'];
	echo "<div class='alert alert-error'>$m</div>";
}
if (isset($messages['info_message']) 
	&& trim($messages['info_message']) != '' ) {
	$m = $messages['info_message'];
	echo "<div class='alert alert-info'>$m</div>";
}
if (isset($messages['error_message'])
    && trim($messages['error_message']) != '' ) {
    $m = $messages['error_message'];
    echo "<div class='alert alert-info'>$m</div>";
}
?>
<h3>Edit Account</h3>
<div class='alert alert-success'>Please refresh your browser by (Ctrl+F5) once before updating your details.</div>
<div class="container" style="margin: 20px 0 30px 0;">
<?php echo form_open('admin/user_service', array('class' => 'form-inline')); ?>
	<fieldset>		
		<div class="col-lg-12">
			<label for="user" class="control-label col-lg-2">Username:</label>
		    <label name="user" id="user" class="control-label col-lg-2"><?php echo $edit_user;?></label>
			<input type="hidden" value="<?php echo isset($account_id) ? $account_id : '';?>" id="account_id" name="account_id"/>
			<div class="col-lg-2">
				<?php echo anchor('admin/user_service', 'All Services', array('class' => 'form-control btn btn-default')); ?>			 
			</div>
			<div class="col-lg-2">
				<?php echo anchor("admin/user_invoices", 'All Invoices', array('class' => 'form-control btn btn-default')); ?>
			</div>
            <div class="col-lg-3">
                <?php echo anchor("admin/invoice_email", 'Invoice email', array('class' => 'form-control btn btn-default')); ?>
            </div>

        </div>
	</fieldset>
<?php echo form_close(); ?>
</div>
<?php

$user_data['edit_user'] = $edit_user;
$this->load->view('admin/accounts/account_form', $user_data);
?>