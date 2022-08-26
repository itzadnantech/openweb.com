<div class="col-lg-9">
	<div class="container">
	<h3>Create a New Email</h3>
	
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {
			$('#create_email_form').validate({
				rules: {
					title :{
						required : true,
					},
					content : {
						required : true,
					},
					email :{
						required : true,
						email : true,
					},					
				}
			});
		});
	</script>
<?php
	echo form_open('admin/add_email', array('class' => 'form-horizontal','id' => 'create_email_form'));
	
	if (!empty($error_message)) {
		echo "<div class='alert alert-danger'>$error_message</div>";
	}
?>		
	<legend>Email Details</legend>
	<fieldset>
		<div class="form-group">
			<label class="control-label col-lg-3" for="purpose">Email Purpose</label>
			<div class="col-lg-6">
				<select class="form-control" name="purpose">
					<option value="activation">Activation</option>
					<option value="registration">Registration</option>
					<option value="send_invoice">Send Invoice</option>
					<option value="active_account">Active ISDL Account</option>
					<option value="subscribe">Subscribe</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="title">Email Title</label>
			<div class="col-lg-9">
				<input type="text" id="title" placeholder="" class="form-control" value="" name="title">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="content">Email Content<br>
			</label>
			<div class="col-lg-9">
				<textarea class="form-control" name="content" id="content" rows="12"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="email">Admin Email Address</label>
			<div class="col-lg-9">
				<input type="text" id="email" placeholder="" class="form-control" value="" name="email">
			</div>
		</div>		
		<div class="control-group">
			<div class="controls">
				<div style="text-align: center;">
					<input type="submit" class="btn btn-large btn-primary" value="Create this Email" name="">
				</div>
			</div>
		</div>
	</fieldset>
<?php echo form_close();?>
</div>
</div>
