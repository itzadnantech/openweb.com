<script language="javascript" type="text/javascript">
$(function(){
    $( "#date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd',
    });
});
</script>
<?php 
if(isset($user_id)){
	$account_id = $user_id;
}
?>
<div class="container" style="margin: 20px 0 30px 0;">
	<fieldset>								
		<div class="col-lg-2">
			<?php echo anchor('admin/all_account', 'User List', array('class' => 'form-control btn btn-default')); ?>
		</div>
		<div class="col-lg-2">
			<?php echo anchor('admin/user_service/'.$account_id, 'Service List', array('class' => 'form-control btn btn-default')); ?>
		</div>
		<div class="col-lg-2">
			<?php echo anchor('admin/create_account', 'Create New', array('class' => 'form-control btn btn-default')); ?>
		</div>
	</fieldset>
</div>
<h3>Edit Order</h3>
<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
	<?php
}
?>

<?php

if ($order_id && trim($order_id) != '') {
	echo form_open('admin/save_order', array('class' => 'form-horizontal','id' => 'manage_order_form'));
	?>
	<input type="hidden" name="id" value="<?php echo $order_id ?>" />
	<input type="hidden" name="account_id" value="<?php echo $account_id ?>" />
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
			}elseif ($i == 'change_flag'){
				echo '<input type="checkbox" name="change_flag" id="change_flag" value="1" ';
				if($o == 1){
					echo 'checked="checked"';
				}
				echo '> <b>Allow user to change his passwords.</b>';
			}else {
				echo $pre;
				if ($i == 'account_password') {
					$help_text = "<input type='hidden' value='$o' name='hidden_password'></input>";					
					$placeholder= 'Current Password is '.trim($o);
					$o = '';
				}elseif($i == 'account_username'){
					$help_text = "<input type='hidden' value='$o' name='hidden_username'></input>";		
					$placeholder= 'Current Username is '.trim($o);
					$o = '';
				}else {
					$placeholder = '';
					$help_text = '';
				}					
				?>
				<input type="text" placeholder="<?php echo $placeholder ?>" name="<?php echo $i ?>" class="form-control" value="<?php echo $o ?>" id="<?php echo $i?>">
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
	
	<?php form_close(); ?>
	<?php
	if ($order_data['status'] == 'pending') {
		?>
		<div class="well" style="margin-top:25px;text-align:center;">
			<strong>This order is currently pending, which means it hasn't applied the addRealmAccount function yet.</strong>
			<div style="margin-top: 10px;">
			<?php echo anchor("admin/activate_order/$order_id", 'Activate and Add Realm Account', 'class="btn btn-default"') ?></div>
		</div>
		<?php
	}
	?>
	<h3 style="margin-bottom:15px;">Other Options</h3>	
<?php
} else {
	?>
	<div class="alert alert-warning">
		<strong>Order not found.</strong> It seems that there is no order with that ID!
	</div>
<?php
}
?>