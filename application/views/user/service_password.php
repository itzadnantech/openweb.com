<script language="javascript" type="text/javascript">
$(document).ready(function() {	
	$("#change_pwd_form").validate({
	       rules: {		      
	    	   account_password: {
		    	  required : true,
		    	  minlength: 4,
		    	  maxlength: 32,  
			  },

    	}
    }); 
});
</script>
<h3 style='padding-bottom: 30px;'>Change Account Password</h3>

<?php if (isset($error_message) && trim($error_message) != '') {
	?><div class="alert alert-danger"><?php echo $error_message ?></div>
<?php } ?>
<?php if (isset($success_message) && trim($success_message) != '') {
	?><div class="alert alert-success"><?php echo $success_message ?></div>
<?php } ?>

<fieldset>
<?php 
if(isset($change_flag) && $change_flag == 1){
	echo form_open('user/update_order', array('class'=>'form-horizontal','id'=>'change_pwd_form')); ?>
<input type="hidden" value="<?php echo $service_id;?>" name= "id"/>
<div class="form-group">
	<label class="form-label col-lg-3">New Account Password</label>
	<div class="col-lg-6">
		<input type="text" class="form-control" name="account_password" id="account_password" placeholder="Current Account Password : <?php echo $acc_pwd;?>"/>
	</div>
</div>
<div align="center">
	<input type="submit" class="btn btn-primary" value="Update Password" />
</div>
<?php 
	echo form_close();
}else{
?>
<div class="alert alert-warning">
	<!-- <strong>You don't have a permission to modify your service password.</strong> -->
    <strong>This information is not available on the product you have chosen at this time.</strong>

</div>
<?php  }?>
</fieldset>