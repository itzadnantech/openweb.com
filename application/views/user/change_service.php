<script language="javascript" type="text/javascript">
$(document).ready(function() {
	
	 $("#form_service").submit(function(){
		 var list= $('input:radio[name="grade"]:checked').val();	
		 if(list == null){
			 $("#error").show();
			return false;
		 }else{
			$("#error").hide();
			return true;
		 }
	 });

	 $('.grade').click(function(){
		 $("#error").hide();
	 });
	
});
</script>
<?php 
if(isset($title)){
	if($title == 'upgrade'){
		$curr_title = "Upgrade Service Information";
	}elseif($title == 'downgrade'){
		$curr_title = "Downgrade Service Information";
	}
}

if(isset($product_data)){
	$current_id = $product_data['id'];
	$current_name = $product_data['name'];
	$current_price = $product_data['price'];
	$current_cycle = $product_data['billing_cycle'];
	$current_speed = $product_data['package_speed'];
	$current_level = $product_data['service_level'];
	$current_type = $product_data['type'];
}

if(isset($class)){
	$curr_class= $class;
}
echo form_open('user/checkout', array('class' => 'form-horizontal','id' => 'form_service'));
?>
<fieldset>
<legend><?php echo $curr_title?></legend>
<input type="hidden" value="<?php echo $current_id;?>" name="service_id" id="service_id">
<input type="hidden" value="<?php echo $title;?>" name="title" id="title">
<div class="form-group">
	<label class="control-label col-lg-3" for="name">Service Name</label>
	<div class="col-lg-8">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_name;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-3" for="name">Service Price</label>
	<div class="col-lg-8">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_price;?>" disabled="disabled">
	</div>
</div>
<!-- <div class="form-group">
	<label class="control-label col-lg-3" for="name">Service Class</label>
	<div class="col-lg-8">
		<input class="form-control col-lg-8" type="text" value="<?php //echo $curr_class;?>" disabled="disabled">
	</div>
</div> -->
<div class="form-group">
	<label class="control-label col-lg-3" for="name">Service Billing Cycle</label>
	<div class="col-lg-8">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_cycle;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-3" for="name">Service Package Speed</label>
	<div class="col-lg-8">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_speed;?>" disabled="disabled">
	</div>
</div>
<div class="form-group">
	<label class="control-label col-lg-3" for="name">Service Level</label>
	<div class="col-lg-8">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_level;?>" disabled="disabled">		
	</div>
</div>
<?php 
if(isset($title) && $title == 'upgrade'){
?>
<div class="form-group">
	<label class="control-label col-lg-3">Upgrade Service</label>
	<div class="col-lg-8">
		<?php 
		if($current_type=='daily'){
		?>
		<input type="radio" name="grade" class="grade" checked="checked" value="0">Upgrade The Service Next Day
		<?php 
		}
		else{
		?>
		<input type="radio" value="1" name="grade" class="grade" checked="checked">Upgrade The Service Immediately <br/>
		<input type="radio" value="0" name="grade" class="grade">Upgrade The Service Next Month
		<?php 
		}
		?>
	</div>
</div>
<?php 
}elseif (isset($title) && $title == 'downgrade'){
?>
<div class="form-group">
	<label class="control-label col-lg-3">Downgrade Service</label>
	<div class="col-lg-8">
		<?php 
		if($current_type=='daily'){
		?>
		<input type="radio" name="grade" class="grade" checked="checked" value="0">Downgrade The Service Next Day
		<?php 
		}
		else{
		?>
		<input type="radio" name="grade" class="grade" checked="checked">Downgrade The Service Next Month
		<?php 
		}
		?>
	</div>
</div>
<?php 
}
?>
<div style="display: none;color: red;padding-left: 220px;" id="error">Please choice an change service option.</div>
<div style="text-align:center;">
<?php 
if(isset($title) && $title == 'upgrade'){
?>
	<input class="btn btn-lg btn-primary" type="submit" value="Continue To Upgrade">
<?php }else{
?>
	<input class="btn btn-lg btn-primary" type="submit" value="Continue To Downgrade">
<?php 	
}
if($current_type=='daily'){
?>
	<a class="btn btn-lg btn-primary" href='<?php echo base_url()?>user/add_account'>Give Up</a>
<?php }else{
?>
	<a class="btn btn-lg btn-primary" href='<?php echo base_url()?>user/active_orders'>Give Up</a>
<?php }?>
</div>
</fieldset>
<?php echo form_close();?>
