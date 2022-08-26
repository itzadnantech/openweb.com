
<h3>Change Password</h3>

<?php if (isset($error_message) && trim($error_message) != '') {
	?><div class="alert alert-danger"><?php echo $error_message ?></div>
<?php } ?>
<?php if (isset($success_message) && trim($success_message) != '') {
	?><div class="alert alert-success"><?php echo $success_message ?></div>
<?php } ?>

<?php echo form_open('user/update_password', array('class'=>'form-horizontal','id'=>'change_password_form')); ?>
<div class="form-group">
	<label class="form-label col-lg-4">New Password</label>
	<div class="col-lg-6">
		<input type="password" class="form-control" name="newPass" id="newPass" />
	</div>
</div>
<div class="form-group">
	<label class="form-label col-lg-4">Confirm New Password</label>
	<div class="col-lg-6">
		<input type="password" class="form-control" name="passConf" id="passConf" />
	</div>
</div>
<input type="submit" class="btn btn-primary" value="Update Password" />
<?php echo form_close();?>