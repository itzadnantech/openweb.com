<div id="page-content" class="container">
	<?php echo form_open('client/cofirm_credit', array('class' => 'form-horizontal', 'id' => 'credit_form'));?> 
	<div align="center" class="alert alert-info">
		To complete your order, and to avtivate your new ADSL account, kindly make a payment of:
	</div>
	<div class="row">
		<input type="hidden" value="<?php echo $client_id;?>" name="user_id">
		<div class="col-lg-1"></div>
		<div class="col-lg-3">
			<fieldset>
	      	<legend align="left">Your Order Information</legend>
	      	<?php if(isset($product_data)){ ?>
	      	<?php  $price = $product_data['price']; ?>
	      		<input type="hidden" value="<?php echo $product_data['id'];?>" name="product_id">
	      		<div style="font-size: 16px;color: #428bca;">
	      			<strong><?php echo $product_data['name'];?></strong>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Price:</strong> <label style="color: #428bca"><?php echo 'R '.$price;?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Billing Cycle:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['billing_cycle']);?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Package Speed:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['package_speed']);?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Service Level:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['service_level']);?></label>
	      		</div>
	      <?php }?> 
	      </fieldset>
		</div>
		<div class="col-lg-1"></div>
		<div class="col-lg-5">
		 	<fieldset>
	      	<legend align="left">Payment:</legend>
	      	<div id="credit_card">
				<div class="form-group" >
					<label class="control-label col-lg-5">Amount: </label>
					<label class= "control-label col-lg-5" style="text-align: left;">R <?php echo $price;?></label>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-5">Bank: </label>
					<label class= "control-label col-lg-5" style="text-align: left;">ABSA</label>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-5">Account Number: </label>
					<label class= "control-label col-lg-5" style="text-align: left;">4064449626</label>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-5">Account Type: </label>
					<label class= "control-label col-lg-5" style="text-align: left;">Cheque</label>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-5">Branch Code: </label>
					<label class= "control-label col-lg-5" style="text-align: left;">632005</label>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-5">Reference : </label>
					<label class= "control-label col-lg-5" style="text-align: left;">45345 45345</label>
				</div>
			</div> 
			<div align="center" class="alert alert-info">
				Once your payment is processed, your details will be automatically sent
			</div>
	      </fieldset>
		</div>
		<div class="col-lg-1"></div>
	</div>
	<br/><br/>
	<?php echo form_close();?>
</div>