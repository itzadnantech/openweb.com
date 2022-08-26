<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$('#update_class_form').validate({
			rules: {
				name :{
					required : true,
				},
				desc : {
					required : true,
				},
				realm : {
					required : true,
				}
			}
		});
	});
</script>

<?php

if (empty($classes_data['class_id'])) {
	$class_id = '';
} else {
	$class_id = $classes_data['class_id'];
}

if (isset($messages['success_message']) && trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}

$class_data = $classes_data['classes_data'];
//var_dump($class_id);die();
?>
<?php 
if( isset($class_id) && !empty($class_data) ){
	echo form_open('admin/update_class', array('class' => 'form-horizontal','id' => 'update_class_form'));
?>
	<legend>Class Details</legend>
	<fieldset>	
		<?php foreach ($class_data as $f => $l){ ?>
			<input type="hidden" value="<?php echo $l['table_id']; ?>" id="class_id" name="class_id"/>
			<div class="form-group">
				<label class="control-label col-lg-3" for="name">Class Name</label>
				<div class="col-lg-5">
					<input type="text" id="name" placeholder="" class="form-control" value="<?php echo $l['id'];?>" name="name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="name">Description</label>
				<div class="col-lg-5">
					<input type="text" id="desc" placeholder="" class="form-control" value="<?php echo $l['desc'];?>" name="desc">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="name">Realm</label>
				<div class="col-lg-5">
					<?php if(!empty($realm_list)){
							echo "<select name='realm' id='realm' class='form-control'>";
							foreach ($realm_list as $ke => $v){
								if($v['realm'] == $l['realm']){
									$selected = 'selected';
								}else{
									$selected = '';
								}
								echo "<option value='".$v['realm']."' selected ='".$selected."'>".$v['realm']."</option>";
							}
							echo "</select>";
					}?>
					<!-- <input type="text" id="realm" placeholder="" class="form-control" value="<?php echo $l['realm'];?>" name="realm">
	 -->
				</div>
			</div>
			<div style="text-align: center">			
				<input type="submit" class="btn btn-large btn-primary" value="Update Class Information" name="">	
			</div>
		<?php } ?>
	</fieldset>
<?php 
}else{
	
}
?>
