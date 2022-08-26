<script type="text/javascript" language="javascript">
$(document).ready(function() {
	$('#eidt_email_form').validate({
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

	$('#mass_mailer').click(function(){
		$.ajax({
			url: '<?php echo base_url('admin/massMailer')?>',
			dataType: 'json',
			type : 'Post',
			success: function(){
				$('#success').show();
				setTimeout("$('#success').hide()", 5000);
			},
		});
	});
});

function check(){
	var a = $('#select-e').val();
	if(a){
		$("#email_template_form").submit();
	}else{
		return false;
	}
}
</script>

<h3>All Email Templates</h3>
<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
<?php
if(!empty($emails_list)){
	echo form_open('admin/select_email', array('class' => 'form-inline','id'=>'email_template_form')); ?>
<div class="form-group">
	<label class="col-lg-3">Select Email Template</label>
	<div class="col-lg-3">
		<select class="form-control" id="select-e" name="purpose" onchange="check()">
			<option></option>
			<?php
			foreach ($emails_list as $e){
				$purposes = $e['purpose'];
				$name = ucfirst($purposes);
				if($purposes == 'forgot_password'){
					$name = 'Forgot Password';
				}
				if($purposes == 'send_invoice'){
					$name = 'Send Invoice';
				}
				if($purposes == 'active_account'){
					$name = 'Active ISDSL Account';
				}
				if (isset($current_purpose) && $purposes == $current_purpose) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				echo "<option $selected value='$purposes'>$name</option>";
			}
			?>
		</select>
	</div>
	<?php if($current_purpose == 'subscribe'){?>
	<div class="col-lg-3">
		<input class="btn btn-primary" type="button" value="Mass Mailer" id="mass_mailer">
	</div>
	<?php }?>
</div>
<?php echo form_close();
}
?>
</div>
<div id="success" class='alert alert-success' style="display: none;">The Mass Mailer have been sent successfully.</div>
<?php
if(isset($email_detail) && !empty($email_detail)){

echo form_open('admin/edit_email', array('class' => 'form-horizontal','id' => 'eidt_email_form'));

if (!empty($error_message)) {
	echo "<div class='alert alert-danger'>$error_message</div>";
}

if (!empty($success_message)) {
	echo "<div class='alert alert-success'>$success_message</div>";
}
$guider = '';
foreach ($email_detail as $email=>$email_data) {
		$purpose = $email_data['purpose'];
?>
	<input type="hidden" name="email_id" id="email_id" value="<?php echo $email_data['id'];?>">
	<legend>Email Details</legend>
	<fieldset>
		<?php  if(!empty($email_data['reason'])){ ?>
		<div class="well" style="text-align:center;">
			<strong><?php echo $email_data['reason'];?></strong>
		</div>
		<?php } ?>
		<div class="form-group">
			<label class="control-label col-lg-2" for="purpose">Email Purpose</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" value="<?php echo ucfirst($email_data['purpose']);?>" disabled="disabled">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-2" for="title">Email Title</label>
			<div class="col-lg-9">
				<input type="text" id="title" placeholder="" class="form-control" value="<?php echo $email_data['title'];?>" name="title">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-2" for="content">Email Content<br>
			</label>
			<div class="col-lg-9">
				<textarea class="form-control" name="content" id="content" rows="12"><?php echo $email_data['content'];?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-2" for="content">Available Placeholders</label>
			<div class="col-lg-9">
				<div class="well" style="line-height: 25px;">
					<strong>
						<?php echo $email_data['placeholders']
                        ?>
					</strong>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-2" for="email">Admin Email Address</label>
			<div class="col-lg-9">
				<input type="text" id="email" placeholder="" class="form-control" value="<?php echo $email_data['email_address'];?>" name="email">
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<div style="text-align: center;">
					<input type="submit" class="btn btn-large btn-primary" value="Update Email Information" name="">
				</div>
			</div>
		</div>
	</fieldset>
<?php
}
echo form_close();
?>
<br/>
<br/>
<?php
echo form_open_multipart('admin/upload_file', array('class' => 'form-horizontal','method' => 'post'));
?>
<div class="form-group">
	<label class="control-label col-lg-2">Email Attachment</label>
	<div class="col-lg-4">
		<input type="file" class="btn btn-default" name="attachment_file"/>
	</div>
	<div class="col-lg-3">
		<input type="submit" class="btn btn-primary" value="Upload" />
	</div>
</div>
<?php
echo form_close();
?>
<?php if(!empty($attach_data)) {

 	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array( 'File Name','Delete'));

	foreach ($attach_data as $i=>$att) {
		$id = $att['id'];
		$name = $att['name'];
		$delete = anchor("admin/delete_attach/$id", 'Delete', 'class="btn btn-sm btn-danger"');
		$actions = $delete;
		$this->table->add_row( array($name,$actions));
	}
	echo $this->table->generate();
 }
}
?>

