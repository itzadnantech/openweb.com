<div id="page-content" class="container">
	<?php echo form_open('client/cofirm_credit', array('class' => 'form-horizontal', 'id' => 'credit_form'));?> 
	<div class="row">
		<input type="hidden" value="<?php echo $client_id;?>" name="user_id">
		<div class="col-lg-1"></div>
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
	      			<strong>Price:</strong> <label style="color: #428bca"><?php echo 'R '.$product_data['price'];?></label>
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
	      	<legend align="left">Payment: Credit Card</legend>
	      	<div id="credit_card">
				<div class="form-group">
					<label for="contact_number" class="control-label col-lg-4">Name on Card:</label>
					<div class="col-lg-8">
						<input type="text" class="form-control valid" id="card_name" name="card_name" placeholder="Name on Card" value="">
					</div>
				</div>
				<div class="form-group">
					<label for="contact_number" class="control-label col-lg-4">Card Number:</label>
					<div class="col-lg-8">
						<input type="text" class="form-control valid" id="card_num" name="card_num" placeholder="Card Number" value="">
					</div>
				</div>
				<div class="form-group">
					<label for="contact_number" class="control-label col-lg-4">Expiry Date:</label>
					<div class="col-lg-8">
						<input type="text" class="form-control valid" id="expire_date" name="expire_date" placeholder="Expiry Date" value="" readonly="readyonly">
					</div>
				</div>
				<div class="form-group">
					<label for="contact_number" class="control-label col-lg-4">CVC:</label>
					<div class="col-lg-8">
						<input type="text" class="form-control valid" id="cvc" name="cvc" placeholder="CVC" value="">
					</div>
				</div>
				<div class="form-group" style="padding-left: 270px;">
					<label for="contact_number" class="control-label col-lg-4"></label>
					<input type="submit" name="" value="Next" class="btn btn-primary">
				    <input type="reset" name="" value="Reset" class="btn btn-primary">
				</div>
			</div> 
	      </fieldset>
		</div>
		<div class="col-lg-1"></div>
	</div>
	<br/><br/>
	<?php echo form_close();?>
</div>
<script>
$( "#expire_date" ).datepicker({
    // defaultDate: "+1w",
     changeMonth: true,
     changeYear: true,
     dateFormat: 'yy-mm',
});

$("#credit_form").validate({
	rules: {
       card_name : 'required',
       card_num : {
    	   number : true,
    	   required : true,
	   },
       expire_date : 'required',
       cvc : {
    	   number : true,
    	   required : true,
	   },
	}
});
</script>