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
	}); 
	
	function check(){
		var a = $('#select-e').val();
		if(a){
			$("#message_template_form").submit();
		}else{
			return false;
		}
	}

</script> 
<h3>All System Messages</h3>
<?php    
if(!empty($messages_category_list)){ 
	echo form_open('admin/all_messages', array('class' => 'form-inline','id'=>'message_template_form')); ?>
	<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
	<label class="col-lg-3">Select Message Page</label>
	<div class="col-lg-4">
		<select class="form-control" id="select-e" name="category" onchange="check()">
		<option></option>
			<?php  
			foreach ($messages_category_list as $m){
				$category = $m['category'];
				$name = ucfirst($category);
				if (isset($current_category) && $category == $current_category) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				echo "<option $selected value='$category'>$name</option>";
			}
			?>
		</select>
	</div>
	</div>
<?php echo form_close();   
}
 
if (!empty($error_message)) {
	echo "<div class='alert alert-danger'>$error_message</div>";
}

if (!empty($success_message)) {
	echo "<div class='alert alert-success'>$success_message</div>";
}

if(!empty($message_list)){
?>
<legend>Message Details</legend>
<?php 
	foreach ($message_list as $message) {
	echo form_open('admin/edit_message', array('class' => 'form-horizontal','id' => 'eidt_message_form'));	
?>
	<input type="hidden" name="message_id" id="message_id" value="<?php echo $message['id'];?>">	
	<fieldset>
		<div class="form-group">
			<label class="control-label col-lg-3" for="type">Message Description</label>
			<div class="col-lg-8">
				<font style="font-size: 18px;"><?php echo $message['description'];?></font>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="type">Message Type</label>
		    <div class="col-lg-8">
				<font style="font-size: 18px;"><?php echo ucfirst($message['message_type']);?></font>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="content">Message Content</label>
			<div class="col-lg-8">
				<textarea class="form-control" name="content" id="content" rows="10"><?php echo $message['content'];?></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<div style="text-align: center;">
					<input type="submit" class="btn btn-large btn-primary" value="Update Message Information" name="">
				</div>
			</div>
		</div>
	</fieldset>
	<hr>
<?php 	
	echo form_close();
	}
}?>

