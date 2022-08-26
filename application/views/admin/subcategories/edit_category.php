<?php

if (empty($subcategory_data['edit_subcategory'])) {
	$edit_subcategory = '';
} else {
	$edit_subcategory = $subcategory_data['edit_subcategory'];
}

if (isset($messages['success_message'])
	&& trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}

?>

	
<div class="container" style="margin: 20px 0 30px 0;">
	<div class="col-lg-12">
		<label for="" class="col-lg-3 control-label">Sub-Category Name:</label>
		<label class="col-lg-7 control-label" for="select-category"><?php echo $edit_subcategory;?></label><br/><br/>
	</div>	
	<div class="col-lg-3">
		<?php echo anchor('admin/all_subcategory', 'Subcategory List', array('class' => 'btn btn-default form-control')); ?>
	</div>
	<div class="col-lg-3">
		<?php echo anchor('admin/create_subcategory', 'Create New', array('class' => 'btn btn-default form-control')); ?>
	</div>
</div>
<?php
	$subcategory_data['edit_subcategory'] = $edit_subcategory;
	$this->load->view('admin/subcategories/category_form', $subcategory_data);
?>