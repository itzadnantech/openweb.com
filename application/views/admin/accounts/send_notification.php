<?php
$types = array (
	'info' => 'Info',
	'warning' => 'Warning',
	'success' => 'Success',
	'danger' => 'Danger',
);
if (isset($messages['success_message']) 
	&& trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}
?>
<h3 style="margin-bottom:30px;">Send a Notification to a User</h3>
<div class="container">
	<?php
	echo form_open('admin/process_notification', array('class' => 'form-horizontal')) ?>
		<div class="form-group">
			<label class="col-lg-3">User</label>
			<div class="col-lg-6">
				<?php echo form_dropdown('username', $user_list, '', 'class="form-control"') ?>
			</div>
		</div>
			
		<div class="form-group">
			<label class="col-lg-3">Note Type</label>
			<div class="col-lg-6">
				<?php echo form_dropdown('noteType', $types, 'info', 'class="form-control"') ?>
			</div>
		</div>
			
		<div class="form-group">
			<label class="col-lg-3">Note Content</label>
			<div class="col-lg-6">
				<textarea name="content" class="form-control"></textarea>
			</div>
		</div>

		<div style="text-align:center">
			<input type="submit" class="btn btn-primary btn-lg" value="Send Note">
		</div>
	</form>
</div>

<div class="lead" style="margin-top:30px">What do these notifications look like?</div>
<div class="lead">Info<br/>
	<img src="<?php echo base_url() ?>img/info.png" style="width:600px;margin-left:15px;" />
</div>
<div class="lead">Warning<br/>
	<img src="<?php echo base_url() ?>img/warning.png" style="width:600px;margin-left:15px;" />
</div>
<div class="lead">Success<br/>
	<img src="<?php echo base_url() ?>img/success.png" style="width:600px;margin-left:15px;" />
</div>
<div class="lead">Danger<br/>
	<img src="<?php echo base_url() ?>img/danger.png" style="width:600px;margin-left:15px;" />
</div>