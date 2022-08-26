<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		
		jQuery.validator.addMethod("isRealm", function(value, element) {   
		    var tel = /[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/;
		    return this.optional(element) || (tel.test(value));
		}, "Please enter a valid realm address.");
		
		$('#update_realm_form').validate({
			rules: {
				name :{
					required : true,
					isRealm : true, 
					remote:{
						url : "<?php echo site_url('admin/validate_realm_name')?>",
						type : 'post',
						data :{ 
							name : function(){return $("#name").val();},
							realm_id : function(){return $("#realm_id").val();}
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
	});
</script>

<?php
if (empty($realm_data['edit_realm'])) {
	$edit_realm = '';
} else {
	$edit_realm = $realm_data['edit_realm'];
}

if (isset($messages['success_message']) && trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}

$all_realms = $realm_data['all_realms'];
$realm_data = $realm_data['realm_data'];
$realm_list = array();
if (!empty($all_realms)) {
	foreach ($all_realms as $ct) {
		$realm_list[$ct['realm']] = $ct['realm'];
	}
}

?>
<h3>Edit Realm</h3>
<!-- <div class="container" style="margin: 20px 0 30px 0;">
	<?php
		echo form_open('admin/select_realm', array('class' => 'form-inline')); 
	?>
	<fieldset>	
		<label class="col-lg-1 control-label" for="select-category">Select Realm</label>
	<div class="col-lg-4">
	<?php echo form_dropdown('realm', $realm_list, '', 'id="select-realm" class="form-control"'); ?>
	</div>
	<div class="col-lg-3">
	<input type="submit" class="btn btn-primary form-control" value="Edit Realm">
	</div>
	<div class="col-lg-3">
	<?php echo anchor('admin/create_realm', 'Create New', array('class' => 'btn btn-default form-control')); ?>
	</div>
	</fieldset>
	<?php echo form_close(); ?>
</div> -->
	
	<div class="container" style="margin: 20px 0 30px 0;">
		<fieldset>	
			<label class="col-lg-1 control-label" for="select-category">Realm</label>
			<div class="col-lg-3">
				<label class="col-lg-1 control-label" for="select-category"><?php echo $edit_realm;?></label>
			</div>
			<div class="col-lg-3">
				<?php echo anchor('admin/all_realms', 'Realm List', array('class' => 'btn btn-default form-control')); ?>
			</div>
			<div class="col-lg-3">
				<?php echo anchor('admin/create_realm', 'Create New', array('class' => 'btn btn-default form-control')); ?>
			</div>
		</fieldset>
	</div>

<?php 
	//var_dump($edit_realm);die();
if( isset($edit_realm) && !empty($realm_data) ){
	echo form_open('admin/update_realm', array('class' => 'form-horizontal','id' => 'update_realm_form'));
?>
	<legend>Realm Details</legend>
	<fieldset>		
		<?php 
			foreach ($realm_data as $f => $l){
		?>
		<input type="hidden" value="<?php echo $l['id']; ?>" id="realm_id" name="realm_id"/>
		<div class="form-group">
			<label class="control-label col-lg-3" for="name">Realm Name</label>
			<div class="col-lg-5">
				<input type="text" id="name" placeholder="" class="form-control" value="<?php echo $l['realm'];?>" name="name">
				<p class="help-block">Eg: mynetwork.co.za</p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="slug">User Name</label> 
				<div class="col-lg-5">
					<input type="text" id="user" placeholder="" class="form-control" value="<?php echo $l['user'];?>" name="user">
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
			<input type="submit" class="btn btn-large btn-primary" value="Update Realm Information" name="">	
		</div>
		<?php 
			}
		?>
	</fieldset>
<?php 
echo form_close(); 
}
?>


