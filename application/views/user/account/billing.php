<?php
$billing_fields = array(
    'sa_id_number' => 'SA ID Number',
	'billing_name' => 'Billing Name',
	'address_1' => 'Address 1',
	'address_2' => 'Address 2',
	'city' => 'City',
	'province' => 'Province',
	'postal_code' => 'Postal Code',
	'country' => 'Country',
	'email' => 'Email',
	'contact_number' => 'Contact number'
);

if (!empty($user_data['user_billing'])) {
	$user_billing = $user_data['user_billing'];
	$btn_label = 'Update Billing Information';
}else{
	$btn_label = 'Create Billing Information';
}
?>

<input type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
<?php echo form_open('user/update_billing', array('class' => 'form-horizontal','id' => 'billing_form'));?>
<fieldset>
	<?php
		if (!empty($error_message)) {
			echo "<div class='alert alert-danger'>$error_message</div>";
		}
		if (!empty($succ_message)) {
			echo "<div class='alert alert-success'>$succ_message</div>";
		}
		if (!empty($info_message)) {
			echo "<div class='alert alert-info'>$info_message</div>";
		}
	?>	
<div class="row">
<div class="col-lg-12">
		<legend>Billing User Information</legend>
		<?php
			foreach ($billing_fields as $f=>$l) {
				if (isset($user_billing[$f])) {
					$v = $user_billing[$f];
				} else {
					$v = '';
				}
		?>
		<div class="form-group">
			<?php echo form_label($l, $f, array ('class'=> 'control-label col-lg-3')); ?>
			<div class="col-lg-6">
				<?php 
					if($f == 'country'){
						echo form_input(array(
							'class' => 'form-control',
							'name' => $f,
							'placeholder' => '',
							'id' => $f,
							'value' => 'South Africa',
							'readonly' => 'readonly',
						));
					}else{
						echo form_input(
							array(
								'class' => 'form-control',
								'name' => $f,
								'placeholder' => '',
								'id' => $f,
								'value' => $v
							)
						);
					}
			   ?>
			</div>
		</div>		
		<?php
			}
		?>
</div>


</div>
<div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
	<?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => $btn_label,)); ?>
	<?php echo form_reset(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Cancel',));?>	
</div>
</fieldset>
<?php echo form_close();?>