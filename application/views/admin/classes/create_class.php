<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		
		$('#create_class_form').validate({
			rules: {
				class_id :{
					required : true,
					remote:{
						url : "<?php echo site_url('admin/validate_class_id')?>",
						type : 'post',
						data :{ 
							class_id : function(){return $("#class_id").val();}
						}	
					  },					  
				},
				class_des : {
					required : true,
				},
				realm : {
					required : true,
				},
			}
		});
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
	echo form_open('admin/add_class', array('class' => 'form-horizontal','id' => 'create_class_form'));
?>
	<legend>Class Details</legend>
	<fieldset>
		<div class="form-group">
			<label class="control-label col-lg-3" for="name">Class ID</label>
			<div class="col-lg-5">
				<input type="text" id="class_id" placeholder="" class="form-control" value="" name="class_id">
			</div>
			<div style='color:#f62b2b;font-size:25px;'>*</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="slug">Class Description</label> 
			<div class="col-lg-5">
				<input type="text" id="class_des" placeholder="" class="form-control" value="" name="class_des">
			</div>
			<div style='color:#f62b2b;font-size:25px;'>*</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="parent">Realm</label>
			<div class="col-lg-5">
			<?php 
				if(!(empty($realm_list))){
					echo "<select name='realm' class='form-control' id='realm'>";	
					foreach ($realm_list as $k => $v){
						echo '<option value="'.$v['realm'].'">'.$v['realm'].'</option>';
					}
					echo "</select>";
				}
			?>
			</div>
			<div style='color:#f62b2b;font-size:25px;'>*</div>
		</div>
		<div class="form-group">
			<div style="text-align: center">
				<input type="submit" class="btn btn-large btn-primary" value="Create this Class" name="">	
			</div>
		</div>
	</fieldset>
<?php form_close();?>