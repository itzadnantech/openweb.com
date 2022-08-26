<h3>Add a Product</h3><br><br>
<!--
<div class="lead" style="font-size:15px;line-height:normal;">
	<dl>
		<dt>Product hierarchy works as follows:</dt>
		<dd>There are product categories, such as "AlwaysOn WiFi" - you can create in the Add Category page.</dd>
		<dd>Products defined here are children of product categories. For example, the category "AlwaysOn WiFi" will have children, "Wi-Fi 1GB", "Wi-Fi 3GB", and "1GB Wi-Fi Top-up."</dd>
	</dl>
</div>
-->

<?php

$product_data['edit_product'] = '';
$this->load->view('admin/products/product_form', $product_data);

?>