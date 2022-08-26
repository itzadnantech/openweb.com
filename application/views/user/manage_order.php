<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3>Manage Order</h3>
<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
	<?php
}
?>
<br/><br/>
<!-- <div class="btn-group" style="margin-bottom:20px;">
<?php echo anchor("user/cancel_order/$order_id", 'Cancel Order', 'class="btn btn-sm btn-danger"'); ?>
</div> -->
<?php

if ($order_id && trim($order_id) != '') {
	echo form_open('user/update_order', array('class' => 'form-horizontal'));
	?>
	<input type="hidden" name="id" value="<?php echo $order_id ?>" />	
	<?php
	foreach ($order_data as $i=>$o) {
		if (!$o) {
			$o = '';
		}
		if ($i == 'price' || $i == 'pro_rata_extra') {
			$pre = '<div class="input-group"><span class="input-group-addon">R</span>';
			$post = '</div>';
		} else {
			$pre = '';
			$post = '';
		}
		if (isset( $order_key[$i])) {
			?>
			<div class="form-group">
			<label class="control-label col-lg-3">
			<?php echo $order_key[$i]; ?>
			</label>
			<div class="col-lg-7">
			<?php if ($i == 'product') {
				if (!empty($product_list)) {
					//print_r($product_list);
					$products = array();
					foreach ($product_list as $p=>$l) {
						$products[$l['id']] = $l['name'];
					}
					echo form_dropdown('product', $products, $o, 'class="form-control"');
				}
			} else if ($i == 'status') {
			 $statuses = array ('active', 'pending', 'deleted', 'suspended', 'expired', 'pending cancellation');
			 $status_list = array();
			 foreach ($statuses as $s) {
				 $status_list[$s] = ucfirst($s);
			 }
			 echo form_dropdown('status', $status_list, $o, 'class="form-control"');
			 ?>
			 <div class="help-block">This will superficially change the status. To cancel an order, click the button above; to activate an order click the button below.</div>
			 <?php
			} else {
				echo $pre;
				if ($i == 'account_password') {
					$help_text = "<p class='help-block'>Current Password: $o</p><input type='hidden' name='current_password' value='$o' />";
					$o = '';
					$placeholder= 'Leave blank to keep current password';
				} else {
					$placeholder = '';
					$help_text = '';
				}
				?>
				<input type="text" placeholder="<?php echo $placeholder ?>" name="<?php echo $i ?>" class="form-control" value="<?php echo $o ?>" >
				<?php echo $help_text; echo $post;
			} ?>
			</div>
			</div>
			<?php
		}	
	}
	?>
	<div style="text-align:center">
		<input type="submit" class="btn btn-large btn-success" value="Update Order" />
	</div>
	</form>
<?php } else {
	?>
	<div class="alert alert-warning">
		<strong>Order not found.</strong> It seems that there is no order with that ID!
	</div>
	<?php
}
?>
        </div>
    </div>
</div>
