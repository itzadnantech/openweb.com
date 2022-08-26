<?php 
if(isset($product_data)){
	$current_id = $product_data['id'];
	$current_name = $product_data['name'];
	$current_price = $product_data['price'];
	$current_cycle = $product_data['billing_cycle'];
	$current_speed = $product_data['package_speed'];
	$current_level = $product_data['service_level'];
}
if(isset($class)){
	$current_class = "({$class['realm']}) - {$class['desc']}";
}
?>
<fieldset>
<legend>Current Service Information</legend>
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
<!-- <div class="form-group">
	<label class="control-label col-lg-4" for="name">Service Class</label>
	<div class="col-lg-6">
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_class;?>" disabled="disabled">
	</div>
</div> -->
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
		<input class="form-control col-lg-8" type="text" value="<?php echo $current_level;?>" disabled="disabled">		
	</div>
</div>
</fieldset>
<br/>
<br/>
<?php
if(isset($products)){
?>	
<fieldset>
<legend>Services List</legend>
<?php 
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Product Name', 'Price', 'Billing Cycle', 'Package Speed', 'Service Level', 'Action'));
	
	foreach ($products as $p){
		$id = $p['id'];
		$name = $p['name'];
		$price = isset($p['price']) ? $p['price'] : '';
		$billing = $p['billing_cycle'];
		$pack = isset($p['package_speed']) ? $p['package_speed']: '';
		$service  = isset($p['service_level']) ? $p['service_level'] : '';
		
		if($current_price < $price){
			$action = anchor("user/upgrade/$id", 'Upgrade', 'class="btn btn-sm btn-primary"');
		}else{
			$action = anchor("user/downgrade/$id", 'Downgrade', 'class="btn btn-sm btn-primary"');
		}
		
		$this->table->add_row( array($name, 'R'.$price, $billing, $pack, $service, $action));
	}
	echo $this->table->generate();	
?>
</fieldset>
<?php 
}
?>