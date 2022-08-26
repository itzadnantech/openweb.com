<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$('#user').val(' ');
		$('#pass').val('');

		jQuery.validator.addMethod("isRealm", function(value, element) {   
		    var tel = /[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/;
		    return this.optional(element) || (tel.test(value));
		}, "Please enter a valid realm address.");
		
		$('#create_realm_form').validate({
			rules: {
				name :{
					required : true,
					isRealm : true, 
					remote:{
						url : "<?php echo site_url('admin/validate_realm_name')?>",
						type : 'post',
						data :{ 
							name : function(){return $("#name").val();}
						}	
					  },					  
				},
				user : {
					required : true,
				},
				pass : {
					required : true,
				},
				re_password: {
		    	  required : true,
	              equalTo: "#pass"
	           	},
			}
		});

		$('#name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
		$('#user').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
		$('#pass').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
	});
</script>
<h3>Create a New Realm</h3>
<?php 

if (isset($messages['error_message']) && trim($messages['error_message']) != '' ) {
	$m = $messages['error_message'];
	echo "<div class='alert alert-error'>$m</div>";
}

?>
<?php
	echo form_open('admin/add_realm', array('class' => 'form-horizontal','id' => 'create_realm_form'));
//$realm_data['edit_realm'] = '';
//$this->load->view('admin/realms/realms_form', $realm_data);

?>
	<legend>Realm Details</legend>
	<fieldset>
		<div class="form-group">
			<label class="control-label col-lg-3" for="name">Realm Name</label>
			<div class="col-lg-5">
				<input type="text" id="name" placeholder="" class="form-control" value="" name="name">
				<p class="help-block">Eg: mynetwork.co.za</p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="slug">User Name</label> 
				<div class="col-lg-5">
					<input type="text" id="user" placeholder="" class="form-control" value="" name="user">
				</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="parent">Password</label>
			<div class="col-lg-5">
				<input type="password" id="pass" placeholder="" class="form-control" value="" name="pass">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="desc">Confirm Password</label>
			<div class="col-lg-5">
				<input type="password" id="re_password" placeholder="" class="form-control" value="" name="re_password">
			</div>
		</div>
		
		<div style="text-align: center">
		
		<input type="submit" class="btn btn-large btn-primary" value="Create this Realm" name="">	
		</div>
	</fieldset>
<?php form_close();?>