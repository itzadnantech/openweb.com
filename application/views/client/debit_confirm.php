<div id="page-content" class="container">
	<?php echo form_open('client/cofirm_debit', array('class' => 'form-horizontal', 'id' => 'credit_form'));?> 
	<div align="center" class="alert alert-info">
		To complete your order, and to avtivate your new ADSL account, kindly make a payment of:
	</div>
	<div class="row">
		<input type="hidden" value="<?php echo $client_id;?>" name="user_id">
		<div class="col-lg-2"></div>
		<div class="col-lg-3">
			<fieldset>
	      	<legend align="left">Your Order Information</legend>
	      	<?php if(isset($product_data)){ ?>
	      		<input type="hidden" value="<?php echo $product_data['id'];?>" name="product_id">
	      		<div style="font-size: 16px;color: #428bca;">
	      			<strong><?php echo $product_data['name'];?></strong>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong><?php echo ucfirst($product_data['billing_cycle']);?> Price:</strong> <label style="color: #428bca"><?php echo 'R '.$product_data['price'];?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Your Total Today:</strong> <label style="color: #428bca">R 0.00 - 100% off</label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Account will run from: </strong> <label style="color: #428bca"><?php echo date('Y/m/d', time());?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Payment Type: </strong><label style="color: #428bca"><?php echo ucfirst($product_data['billing_cycle']);?> Debit Order of <?php echo 'R '.$product_data['price'];?></label> 
	      		</div>
	      <?php }?> 
	      </fieldset>
		</div>
		<div class="col-lg-1"></div>
		<div class="col-lg-5">
		 	<fieldset>
	      	<legend align="left">Banking Details:</legend>
	      	<div style="font-size: 16px;">
      			<strong>Bank Name: </strong> <label style="color: #428bca"><?php echo $payment_data['bank_name'];?></label>
      		</div>
      		<br/>
      		<div style="font-size: 16px;">
      			<strong>Account Number: </strong> <label style="color: #428bca"><?php echo $payment_data['bank_account_number'];?></label>
      		</div>
      		<br/>
      		<div style="font-size: 16px;">
      			<strong>Account Type: </strong> <label style="color: #428bca"><?php echo $payment_data['bank_account_type'];?></label>
      		</div>
      		<br/>
      		<div style="font-size: 16px;">
      			<strong>Branch Code: </strong> <label style="color: #428bca"><?php echo $payment_data['bank_branch_code'];?></label>
      		</div>
      		<br/>
      		<div style="font-size: 16px;">
      			<strong>Reference: </strong> <label style="color: #428bca"><?php echo $reference;?></label>
      		</div>
	      </fieldset>
		</div>
		<div class="col-lg-1"></div>
	</div>
	<br/><br/>
	<div class="col-lg-12" align="center">
		<input type="submit" name="" value="I Accept & Confirm - Create Account" class="btn btn-primary">
	</div>
	<?php echo form_close();?>
</div>