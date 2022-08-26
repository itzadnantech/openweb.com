<?php

if (empty($category_data['edit_category'])) {
	$edit_category = '';
} else {
	$edit_category = $category_data['edit_category'];
}

if (isset($messages['success_message']) 
	&& trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}

?>
<h3>Edit Category</h3>
<!-- <div class="container" style="margin: 20px 0 30px 0;">
	<?php //echo form_open('admin/select_category', array('class' => 'form-inline'));?>
	<fieldset>	
		<label class="col-lg-1 control-label" for="select-category">Select Category</label>
		<div class="col-lg-4">
			<?php //echo form_dropdown('category', $category_list, '', 'id="select-category" class="form-control"'); ?>
		</div>
		<div class="col-lg-3">
			<input type="submit" class="btn btn-primary form-control" value="Edit Category">
		</div>
		<div class="col-lg-3">
			<?php //echo anchor('admin/create_category', 'Create New', array('class' => 'btn btn-default form-control')); ?>
		</div>
	</fieldset>
	<?php //echo form_close();?>
</div> -->

<div class="container" style="margin: 20px 0 30px 0;">
	<div class="col-lg-12">
		<label for="" class="col-lg-3 control-label">Category Name:</label>
		<label class="col-lg-7 control-label" for="select-category"><?php echo $edit_category;?></label><br/><br/>
	</div>	
	<div class="col-lg-3">
		<?php echo anchor('admin/all_category', 'Category List', array('class' => 'btn btn-default form-control')); ?>
	</div>
	<div class="col-lg-3">
		<?php echo anchor('admin/create_category', 'Create New', array('class' => 'btn btn-default form-control')); ?>
	</div>
</div>

<?php
	$category_data['edit_category'] = $edit_category;
	$this->load->view('admin/categories/category_form', $category_data);
?>