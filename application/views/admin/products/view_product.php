<?php 
	if (empty($product_data['product_id'])) {
		$product_id = '';
	} else {
		$product_id = $product_data['product_id'];
	}

	$payment_methods = $product_data['payment_methods'];
	$billing_cycle = $product_data['billing_cycle'];
	$product_datas = $product_data['product_data'];
	
	if( isset($product_id) && !empty($product_datas) ){
?>
<div class="col-lg-12">
	<div class="container">
		<h3>View Product</h3>
		<form id="view_product_form" class="form-horizontal" accept-charset="utf-8" novalidate="novalidate">			
			<legend>Product Information</legend>
			<fieldset>
				<?php 
					foreach ($product_datas as $f => $l){ 
						if($l['automatic_creation'] == 1){
							$creation_mode = 'Auto-Create';
						}else{
							$creation_mode = 'Manual';
						}					
						
						if($l['active'] == 1){
							$visable = 'Visible';
						}else{
							$visable = 'Hidden';
						}
						
						$parent_id = $l['parent'];//product class
						$parent = $this->category_model->get_subcategory_name($parent_id);
				?>
				
				<input type="hidden" value="<?php echo $l['id']; ?>" name="id"> 
				<div class="form-group">
					<label class="control-label col-lg-3" for="name">Signup Link</label>
					<div class="col-lg-9">
						<label class="col-lg-9" for="name" style="padding-top: 5px;"><?php echo base_url().'client/order_product/'.$l['random_num']; ?></label>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="name">Product Name</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['name']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="parent">Parent Sub-Category</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $parent; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="active">Visibility</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $visable; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="class">Product Class</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['class']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="package_speed">Package Speed</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['package_speed']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="service_level">Service Level</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['service_level']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="recommended_use">Recommended Use</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['recommended_use']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="global_backbone">Global Backbone</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['global_backbone']; ?>" class="form-control">
					</div>
				</div>
				<?php if(!empty($billing_cycle)){ ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="default_comment">Billing Cycle</label>
					<div class="col-lg-9">
					<?php foreach ($billing_cycle as $k => $v){
							echo ' <label class="checkbox">'.$v['billing_cycle'].'</label>';
					}?>
					</div>
				</div>
				<?php	} ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="price">Price</label>
					<div class="col-lg-2">
						<div class="input-group">
							<span class="input-group-addon">R</span>
							<input type="text" readonly="readonly" value="<?php echo $l['price']; ?>" class="form-control">
						</div>
					</div>
				</div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="points">Avios Billing Code</label>
                    <div class="col-lg-2">
                        <div class="input-group">
                            <input type="text" readonly="readonly" value="<?php echo $l['billing_code']; ?>" class="form-control">
                        </div>
                    </div>
                </div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="pro_rata_option">Pro-Rata Option</label>
					<div class="col-lg-6">
						 <input type="text" readonly="readonly" value="<?php echo $l['pro_rata_option']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="billing_occurs_on">Billing Occurs On</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $l['billing_occurs_on']; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="automatic_creation">Creation Mode</label>
					<div class="col-lg-6">
						<input type="text" readonly="readonly" value="<?php echo $creation_mode; ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="default_comment">Default Comment</label>
					<div class="col-lg-6">
						<textarea readonly="readonly" class="form-control" row ="3" ><?php echo $l['default_comment']; ?></textarea>
					</div>
				</div>
				<?php if(!empty($payment_methods)){ ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="default_comment">Payment Methods</label>
					<div class="col-lg-9">
					<?php foreach ($payment_methods as $k => $v){
							if($v['payment_method'] == 'credit_card'){
								$payment_method = "Once off payment from your Credit Card";
							}elseif($v['payment_method'] == 'credit_card_auto'){
								$payment_method = "Credit Card Auto Billing";
							}elseif($v['payment_method'] == 'debit_order'){
								$payment_method = "Debit Order";
							}elseif($v['payment_method'] == 'eft'){
								$payment_method = "EFT";
							}
							echo ' <label class="checkbox">'.$payment_method.'</label>';
					}?>
					</div>
				</div>
				<?php	} ?>
				<?php }?>
			</fieldset>
		</form>
	</div>
</div>
<?php }else{}?>
