<?php

// Not we display a list of all of the products for the admin to select from.
if (empty($product_data['edit_product'])) {
	$edit_product = '';
} else {
	$edit_product = $product_data['edit_product'];
}

if (empty($product_data['payment_methods'])) {
	$payment_methods = '';
} else {
	$payment_methods = $product_data['payment_methods'];
}

if (isset($messages['success_message']) 
	&& trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}
?>
<h3>Update Product</h3>
<div class="container" style="margin: 20px 0 30px 0;">
<?php echo form_open('admin/select_product', array('class' => 'form-inline')); ?>
<label for="user" class="control-label col-lg-2">Product Name:</label>
<label name="user" id="user" class="control-label col-lg-2"><?php echo $edit_product;?></label>
<!-- <label for="user" class="control-label col-lg-1">Select Product</label>
		<div class="col-lg-4">
<select name="product" class="form-control">
<?php
if (!empty($product_data['all_products'])) {
	$all_products = $product_data['all_products'];
	foreach ($all_products as $p) {
		$n = $p['name'];
		$pn = $p['id'];
		echo "<option value='$pn'";
		if ($pn == $edit_product) {
			echo ' selected="selected"';
		}
		echo ">$n</option>";
	}
}
?>
</select> -->
<!-- <input type="submit" class="btn btn-primary" value="Edit Product"> -->
<?php echo anchor('admin/all_product', 'Product List', array('class' => 'btn btn-default')); ?>&nbsp;&nbsp;
<?php echo anchor('admin/create_product', 'Add New', array('class' => 'btn btn-default')); ?>
</div>

<?php echo form_close();?>
</div>
<?php
$product_data['edit_product'] = $edit_product;
$product_data['payment_methods'] = $payment_methods;



if (isset ($product_additional_comments))
    $product_data['product_additional_comments'] = $product_additional_comments;


if (isset($nosvc_redirect) && ($nosvc_redirect == true)){
    $product_data['nosvc_redirect'] = true;
}
$this->load->view('admin/products/product_form', $product_data);
?>