<h2>Final Confirmation </h2>

<!--Current service information  -->
<fieldset>
<legend>Current Service Information</legend>
<?php
if(isset($curr_service_data)){
	$current_id = $curr_service_data['id'];
	$current_name = $curr_service_data['name'];
	$current_price = $curr_service_data['price'];
	$current_cycle = $curr_service_data['billing_cycle'];
	$current_speed = $curr_service_data['package_speed'];
	$current_level = $curr_service_data['service_level'];
	$discounted_price = ($discount/100) * $current_price;
	$total_price = $current_price - $discounted_price;
}?>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Name</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_name;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Price</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="R<?php echo $current_price;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Billing Cycle</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_cycle;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Package Speed</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_speed;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Level</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_level;?>" disabled="disabled"><br/><br/><br/>	
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Account Username</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $account_username;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Your client discount is <?php echo $discount?>%</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="-R<?php echo $discounted_price;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Total Monthy Cost </label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="R<?php echo $total_price;?>" disabled="disabled">
	</div>
</div>
</fieldset>

<!--New service information  -->
<fieldset>
<legend>New Service Information</legend>
<?php
if(isset($new_service_data)){
	$new_ser_id = $new_service_data['id'];
	$new_ser_name = $new_service_data['name'];
	$new_ser_price = $new_service_data['price'];
	$new_ser_cycle = $new_service_data['billing_cycle'];
	$new_ser_speed = $new_service_data['package_speed'];
	$new_ser_level = $new_service_data['service_level'];
	$new_ser_type = $new_service_data['type'];
	$discounted_price = ($discount/100) * $new_ser_price;
	$total_price = $new_ser_price - $discounted_price;
	
	if($method == 'upgrade'){
		if($new_ser_type!='daily'){
			if($grade == '1'){
				$tips = 'The service will upgrade immediately';
			}elseif ($grade == '0'){
				$tips = 'The service will upgrade in the next month.';
			}
		}
		else{
			$tips = 'The service will upgrade in the next day.';
		}
	}elseif($method == 'downgrade'){
		if($new_ser_type!='daily')
			$tips = 'The service will downgrade in the next day.';
		else
			$tips = 'The service will downgrade in the next month.';
	}
}?>
<?php echo form_open('user/change_service', array('method'=>'post'));?>
<input type="hidden" value="<?php echo $new_ser_id;?>" id="service_id" name= "service_id"/>
<input type="hidden" value="<?php echo $grade;?>" id="grade" name= "grade"/>
<input type="hidden" value="<?php echo $method;?>" id="title" name= "title"/>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Name</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $new_ser_name;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Price</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="R<?php echo $new_ser_price;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Billing Cycle</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $new_ser_cycle;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Package Speed</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $new_ser_speed;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Level</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $new_ser_level;?>" disabled="disabled"><br/><br/><br/>		
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Account Username</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $account_username;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Your client discount is <?php echo $discount?>%</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="-R<?php echo $discounted_price;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-4" for="name">Total Monthy Cost </label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="R<?php echo $total_price;?>" disabled="disabled"><br/><br/><br/>
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-3" for="name">Tips </label>
	<div class="col-lg-9">
		<label class="control-label col-lg-9" for="name" style="font-size: 16px;"><?php echo $tips;?></label><br/><br/>
	</div>
</div>
<div class="form-group" style="text-align:center;">
	<input type="submit" value="Confirm <?php echo $method;?>" class="btn btn-lg btn-primary"/>
	<a href="<?php echo base_url()?>user/active_orders" class="btn btn-lg btn-primary">Cancel <?php echo $method;?></a>
</div>
<?php echo form_close();?>
</fieldset>

