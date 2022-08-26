<script language="javascript" type="text/javascript">
$(document).ready(function() {	
	$("#update_admin_form").validate({
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
						account_id : function(){return $("#admin_id").val();}
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
});
</script>

<?php 
if( isset($admin_data) && !empty($admin_data) ){
	echo form_open('super_administrator/update_admin', array('class' => 'form-horizontal','id' => 'update_admin_form'));
?>
	<fieldset>
		<legend>Admin account Information</legend>
		<?php foreach ($admin_data as $f => $l){ ?>
		<input type="hidden" value="<?php echo $l['id']; ?>" id="admin_id" name="admin_id"/>
		<div class="form-group">
			<label for="first_name" class="control-label col-lg-2">First Name</label>			
			<div class="col-lg-4">
				<input type="text" name="first_name" value="<?php echo $l['first_name']; ?>" class="form-control" placeholder="" id="first_name">			
			</div>
			<div style="color:#f62b2b;font-size:25px;">*</div>
		</div>		
		<div class="form-group">
			<label for="last_name" class="control-label col-lg-2">Last Name</label>			
			<div class="col-lg-4">
				<input type="text" name="last_name" value="<?php echo $l['last_name']; ?>" class="form-control" placeholder="" id="last_name">			
			</div>
			<div style="color:#f62b2b;font-size:25px;">*</div>
		</div>		
		<div class="form-group">
			<label for="email_address" class="control-label col-lg-2">Email Address</label>			
			<div class="col-lg-6">
				<input type="text" name="email_address" value="<?php echo $l['email_address']; ?>" class="form-control" placeholder="" id="email_address">			
			</div>
			<div style="color:#f62b2b;font-size:25px;">*</div>
		</div>		
		<div class="form-group">
			<label for="mobile_number" class="control-label col-lg-2">Mobile Number </label>			
			<div class="col-lg-4">
				<input type="text" name="mobile_number" value="<?php echo $l['mobile_number']; ?>" class="form-control" placeholder="" id="mobile_number">			
			</div>
		</div>		
		<div class="form-group">
			<label for="role" class="control-label col-lg-2">Account Role</label>			
			<div class="col-lg-3">
				<select name="role" class="form-control">
					<option value="admin">Administrator</option>
				</select>			
			</div>
		</div>		
		<div class="form-group">
			<label for="username" class="control-label col-lg-2">Username</label>			
			<div class="col-lg-4">
				<input type="text" name="username" value="<?php echo $l['username']; ?>" class="form-control" placeholder="" id="username">			
			</div>
			<div style="color:#f62b2b;font-size:25px;">*</div>
		</div>		
		<div class="form-group">
			<label for="password" class="control-label col-lg-2">Password</label>			
			<div class="col-lg-4">
				<input type="password" name="password" value="" class="form-control" placeholder="" id="password">			
			</div>
			<div style="color:#f62b2b;font-size:25px;">*</div>
		</div>		
		<div class="form-group">
			<label for="re_password" class="control-label col-lg-2">Confirm Password</label>			
			<div class="col-lg-4">
				<input type="password" name="re_password" value="" class="form-control" placeholder="" id="re_password">			
			</div>
			<div style="color:#f62b2b;font-size:25px;">*</div>
		</div>		
		<div style="letter-spacing: 100px;padding-left: 380px;">
			<input type="submit" name="" value="Update" class="btn btn-primary btn-lg">			
		</div>
		<?php }?>
	</fieldset>
<?php 
	echo form_close();
}
?>