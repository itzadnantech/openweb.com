<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3>Billing Settings</h3>
        </div>

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
<!--
<div class="col-lg-6" >
	<legend>Credit Card Information</legend>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Name on Card</label>			
		<div class="col-lg-7">
			<input type="text" class="form-control"id="name_on_card" name="name_on_card"  value="<?php if (isset($user_billing['name_on_card'])) {echo $user_billing['name_on_card']; } ?>" >		
		</div>
	</div>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Credit Card Number</label>			
		<div class="col-lg-7">
			<input type="text" class="form-control" id="card_num" name="card_num"  value="<?php if (isset($user_billing['card_num'])) { echo $user_billing['card_num']; }?>" >		
		</div>
	</div>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Expiry Date</label>			
		<div class="col-lg-3">
			<select name="expires_month" class="form-control" id="expires_month">
			<?php
			foreach (range('1', '12') as $month) {
				echo "<option value=$month";
				if(isset($user_billing['expires_month']) && $user_billing['expires_month'] == $month){
					echo " selected";
				}
				echo ">$month</option>";
			}
			?>
			</select>
		</div>
		<div class="col-lg-3">
			<select class="form-control" id="expires_year" name="expires_year">
			<?php
			foreach (range(date("Y"), date("Y") + 10) as $year) {
				echo "<option value=$year";
				if(isset($user_billing['expires_year']) && $user_billing['expires_year'] == $year){
					echo " selected";	
				}
				echo ">$year</option>";
			}
			?>
			</select>
		</div>		
	</div>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">CVC</label>			
		<div class="col-lg-7">
			<input type="text" class="form-control" id="cvc" name="cvc"  value="<?php if (isset($user_billing['cvc'])) { echo $user_billing['cvc'];}?>" >		
		</div>
	</div>
	
	<legend>Bank Details</legend>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Bank Name</label>			
		<div class="col-lg-7">
			<input type="text" class="form-control"id="bank_name" name="bank_name"  value="<?php if (isset($user_billing['bank_name'])) {echo $user_billing['bank_name'];}?>" >		
		</div>
	</div>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Account Number</label>			
		<div class="col-lg-7">
			<input type="text" class="form-control" id="bank_account_number" name="bank_account_number"  value="<?php  if (isset($user_billing['bank_account_number'])) { echo $user_billing['bank_account_number'];}?>" >		
		</div>
	</div>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Account Type</label>			
		<div class="col-lg-7">
			<select class="form-control" id="bank_account_type" name="bank_account_type">
				<?php 
					$type = array('Cheque/Current'=>'Cheque / Current','Savings'=>'Savings','Transmission'=>'Transmission');
					foreach ($type as $v){
						echo "<option value=$v";
						if(isset($user_billing['bank_account_type']) && $user_billing['bank_account_type'] == $v){
							echo " selected";
						}
						echo ">$v</option>";
					}	
				?>
			</select>	
		</div>
	</div>
	<div class="form-group">
		<label for="account_type" class="control-label col-lg-5">Branch Code</label>			
		<div class="col-lg-7">
			<input type="text" class="form-control" id="bank_branch_code" name="bank_branch_code"  value="<?php if (isset($user_billing['bank_branch_code'])) {echo $user_billing['bank_branch_code'];}?>" >		
		</div>
	</div>
</div>
-->
</div>
<div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
	<?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => $btn_label,)); ?>
	<?php echo form_reset(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Cancel',));?>	
</div>
</fieldset>
<?php echo form_close();?>
</div>
</div>
</div>
