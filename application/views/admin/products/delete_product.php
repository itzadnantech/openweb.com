<h3>Delete Product</h3>
<!-- <div class="container" style="margin: 20px 0 30px 0;">
	<?php echo form_open('admin/select_delete_product', array('class' => 'form-inline')); ?>
		<label for="user" class="control-label col-lg-1">Select Product</label>
		<div class="col-lg-4">
			<select name="product" class="form-control">
			<?php
			if (!empty($product_data['all_products'])) {
				$all_products = $product_data['all_products'];
				foreach ($all_products as $p) {
					$n = $p['name'];
					$pn = $p['id'];
					echo "<option value='$pn'";
					if ($pn == $delete_product) {
						echo ' selected="selected"';
					}
					echo ">$n</option>";
				}
			}
			?>
			</select>
		</div>
		<input type="submit" class="btn btn-danger" value="Delete Product">
	<?php echo form_close(); ?>
</div> -->

<?php if (isset($product_id) && trim($product_id) != '') { ?>
<div class="well">
<div class="lead">Are you sure you want to delete this product?</div>
<?php
if(isset($type) && $type == 'r') {
    echo anchor("admin/confirm_delete/$product_id"."/r", 'Confirm Delete', 'class="btn btn-danger"');
} else {
    echo anchor("admin/confirm_delete/$product_id", 'Confirm Delete', 'class="btn btn-danger"');
}
?>&nbsp;&nbsp;&nbsp;
<?php echo anchor("admin/all_product", 'Do Not Delete', 'class="btn btn-primary"'); ?>
</div>
<?php } ?>

<?php	
if (isset($users) && !empty($users)) {
?>
	<div class="lead">There are <span class="text-info"><?php echo count($users) ?></span>
	orders of this product</div>
	<table class="table">
		<thead>
			<th>User</th>
			<th>IS username</th>
			<th>Date Created</th>
		</thead>
		<tbody>
		<?php foreach ($users as $user) { ?>
			<tr>
				<td><?php echo "{$user['name']} ({$user['user']})" ?></td>
				<td><?php echo $user['account_username'] ?></td>
				<td><?php echo $user['date'] ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php
} else  {		
}
?>