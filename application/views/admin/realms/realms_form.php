<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$('#create_realm_form').validate({
			rules: {
				name :{
					required : true,
					remote:{
						url : "<?php echo site_url('admin/validate_realm_name')?>",
						type : 'post',
						data :{ 
							name : function(){return $("#name").val();},
							realm_id : function(){return $('#realm_id').val();}
						}	
					  },
				},
				user : {
					required : true,
				},
				password : {
					required : true,
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
if ($this->uri->segment(2) == 'create_realm') {
	$new_realm = 1;
	$realm = '';
} else {
	if (!empty($realm_data)) {
		$realm_id = $realm_data['id'];
	}
	$new_realm = 0;
}

if ($new_realm) {
	$realm_settings = array();
	if (!empty($realm_fields)) {
		foreach ($realm_fields as $f) {
			$realm_data[$f] = '';
		}
	}
}
?>

<?php 
	if (isset($edit_realm) && !empty($realm_data)) {

		$realm_settings = $realm_data;
		//var_dump($realm_settings);die();
		echo form_open('admin/update_realm', array('class' => 'form-horizontal','id' => 'create_realm_form'));
?>
		<?php 
			if (!empty($error_message)) {
				echo "<div class='alert alert-danger'>$error_message</div>";
			}
		?>	
		<?php 
			if(isset($realm_id)){
				echo '<input type="hidden" value="'.$realm_id.'" id="realm_id" name="realm_id"/>';
			}else{
				echo '<input type="hidden" value="" id="realm_id" name="realm_id"/>';
			}
		?>			
	<legend>Realm Details</legend>
	<fieldset>
		
		<?php 
			foreach ($realm_fields as $f=>$l) {
		?>
		<div class="form-group">
			<label class="control-label col-lg-3" for="name">Realm Name</label>
			<div class="col-lg-5">
				<input type="text" id="name" placeholder="" class="form-control" value="<?php echo $f;?>" name="name">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="slug">User</label> 
				<div class="col-lg-5">
					<input type="text" id="user" placeholder="" class="form-control" value="" name="user">
				</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="parent">Password</label>
			<div class="col-lg-5">
				<input type="text" id="password" placeholder="" class="form-control" value="" name="password">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="desc">Confirm Password</label>
			<div class="col-lg-5">
				<input type="text" id="re_password" placeholder="" class="form-control" value="" name="re_password">
			</div>
		</div>
		
		<div style="text-align: center">
		
		<?php 
			}
		?>
		
		<?php  
			if(isset($realm_id)){			
		?>
			<input type="submit" class="btn btn-large btn-primary" value="Create this Realm" name="">	
		<?php 
			}else{
		?>
			<input type="submit" class="btn btn-large btn-primary" value="Update Realm Information" name="">
		<?php 
			}
		?>
		</div>
	</fieldset>
<?php
	echo  form_close();
}else{
	
}

?>