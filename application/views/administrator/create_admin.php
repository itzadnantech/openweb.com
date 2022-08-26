<script language="javascript" type="text/javascript">
$(document).ready(function() {	
	$("#create_admin_form").validate({
	       rules: {
		      first_name	: "required",
		      last_name		: "required",
		      email_address		: {
			      required : true,
			      email: true,
			      remote:{
					url : "<?php echo site_url('admin/validate_email')?>",
					type : 'post',
					data :{ 
						email_address : function(){return $("#email_address").val();},
						account_id : function(){return $("#account_id").val();}
					}	
				  },				 
			  },
			  user_mobile :{
            	  number : true,
              },
		      username	:{
		    	  required : true,
		    	  minlength: 4,
		    	  remote:{
					url : "<?php echo site_url('admin/validate_username')?>",
					type : 'post',
					dataType: "json", 
					data :{ 
						username : function(){return $("#username").val();}
					}
				  }
	
			  },
			  password	   : {
		    	  required : true,
		    	  minlength: 4,
		    	  maxlength: 32,  
			  },
		      re_password: {
		    	  required : true,
	              equalTo: "#password"
           	  },
	   	}
    }); 
    $('#first_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#last_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#email_address').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#username').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#password').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#re_password').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
});
</script>

<?php 
$user_fields = array (
		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'email_address' => 'Email Address',
		'mobile_number' => 'Mobile Number ',
		'role' => 'Account Role',
		//'discount' => 'Discount Rate',
		'username' => 'Username',
		'password' => 'Password',
		're_password' => 'Confirm Password',
);
echo form_open('super_administrator/create_admin', array('class' => 'form-horizontal','id' => 'create_admin_form')); ?>
	
		<?php if (!empty($error_message)) {
			echo "<div class='alert alert-danger'>$error_message</div>";
		}?>	
		<div class='alert alert-danger' style="display: none;" id='notice'></div>
		<?php if(isset($account_id)){
			echo '<input type="hidden" value="'.$account_id.'" id="account_id" name="account_id"/>';
		}else{
			echo '<input type="hidden" value="" id="account_id" name="account_id"/>';
		}?>		
		<fieldset>
		<legend>Create Admin Information</legend>				
		<?php
			foreach ($user_fields as $f=>$l) { 
				if (isset($user_settings[$f])) {
					$v = $user_settings[$f];
				} else {
					$v = '';
				}
				if ($f == 'email_address' || $f == 'mobile') {
					$input_width = 'col-lg-6';
				} else if ($f == 'role') {
					$input_width = 'col-lg-3';
				} else {
					$input_width = 'col-lg-4';
				}
		?>
		<div class="form-group">
		<?php echo form_label($l, $f, array ('class'=> 'control-label col-lg-2')); ?>
			<div class="<?php echo $input_width ?>">
			<?php
				$input_class = 'form-control';
				if ($f == 'role') {
					if (trim($v) == '') {
						$d = 'client'; 
					} else {
						$d = $v;
					}
					$roles = array(
						'admin'  => 'Administrator',
					);
					echo form_dropdown('role', $roles, $d, 'class="form-control"');
				}else {
					if($f == 'password' || $f == 're_password'){
						echo form_password(
							array(
								'class' => $input_class,
								'name' => $f,
								'placeholder' => '',
								'id' => $f,
								'value' => ""
							)						
						);
					}else{
						echo form_input(
							array(
								'class' => $input_class,
								'name' => $f,
								'placeholder' => '',
								'id' => $f,
								'value' => $v
							)
						);	
					}
				}
			?>
			</div>
		</div>		
		<?php
			}
		?>
		<div style="letter-spacing: 100px;padding-left: 200px;">
			<?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Submit',)); ?>
			<?php echo form_reset(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Cancel',));?>	
		</div>
	</fieldset>
<?php echo form_close(); ?>