<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
        </div>
<?php
$name = $product_data['name'];
$billing_cycle = '';
if(isset($billing_cycles) && !empty($billing_cycles)){
	foreach ($billing_cycles as $k=>$v){
		$radio = form_radio('billing_cycle', $v['billing_cycle']).$v['billing_cycle'];
		$billing_cycle .= ' '.$radio; 
		//echo '<pre>';print_r($v['billing_cycle']);die;
	}

}else{
	$billing_cycle = '<input type="radio" name="billing_cycle" value="Monthly" checked>Monthly';
}

/* if (isset($product_data['billing_cycle'])) {
	if (isset($billing_cycles[$product_data['billing_cycle']])) {
		$billing_cycle = $billing_cycles[$product_data['billing_cycle']];
	} else {
		$billing_cycle = ucwords($product_data['billing_cycle']);
	}
} else {
	$billing_cycle = 'Monthly';
} */
$cost = 'R' . $product_data['price'];
$realm = $product_data['realm'];
/*
	Apply discount
*/
if (isset($this->site_data['discount'])) {
	$discount = $this->site_data['discount'];
} else {
	$discount = '';
}
if (isset($product_data['pro_rata_extra'])) {
	$pro_rata_extra = $product_data['pro_rata_extra'];
} else {
	$pro_rata_extra = 0.00;
}

if (trim($discount) != '') {
	$price_percent = 100 - $discount;
	$price_percent = $price_percent / 100;
	$user_price = number_format(round($product_data['price'] * $price_percent, 2), 2);
	$user_price_out = "R$user_price</span>";
	
	if ($pro_rata_extra != 0.00) {
		$pro_rata_extra = number_format(round($pro_rata_extra * $price_percent, 2), 2);
	}
} else {
	$user_price_out = $cost;
}

$user_price_out .= "<br />Price for this month: R$pro_rata_extra";

$tmpl = array ( 'table_open'  => '<table class="table">' );
$this->table->set_template($tmpl);
$this->table->set_heading(array('Product', 'Pricing Structure', 'Cost'));
$this->table->add_row( array( $name, $billing_cycle, $user_price_out));
?>
<?php if(isset($payment_error) && !empty($payment_error)){
	echo "<div class='alert alert-danger'>There have some errors about the payment, please try it again.<br>";
	echo "Error:$payment_error</div>";
}?>

<h3><span style="font-weight: bold;">Order New Product:</span> </h3>
<h3>Add to cart: <span class="text-info"><?php echo $name; ?></span></h3>

<?php echo $this->table->generate(); ?>

<div style="display: none;color: #f62b2b;" id="create_error">Please choose a Create username method.</div>
<div style="display: none;color: #f62b2b;" id="cycle_error">Please choose a Pricing Structure.</div>
<div style="display: none;color: #f62b2b;" id="u_p_error">Please input username and password.</div>

<?php  
echo form_open('product/checkout', array('class'=>'form-horizontal', 'id'=>'check_form'));
echo '<input type="hidden" id="choose_cycle" name="choose_cycle" value="">'; ?>
<?php
if ($product_data['automatic_creation']) {
?>
	<div>
		<strong></strong>
		<label style="color: #428bca;font-size: 16px;">
			<input type="checkbox" name="create_username" value="create_client" class="choose_u_p" checked="checked"  disabled="disabled"> Username and password created by client.</input><br/>
			<!-- <input type="radio" name="create_username" value="create_sysetm" class="choose_u_p">System create the username and password.</input> -->
		</label>
	</div>
			
	<div class="well" id="auto_create" style="display: none;">
		<div class="lead">Please choose a username and password for this account</div>
		<input type="hidden" id="realm" name="realm" value="<?php echo $realm;?>"/>
		<div class="form-group col-lg-12">
			<label class="col-lg-2">Username</label>
			<div class="col-lg-6">
                <div class="input-group">
                    <input type="text" name="username" class="form-control" id="username"/>
                    <span class="input-group-addon" style="font-size:15px;line-height:1.4;">@<?php echo $realm; ?></span> <br/>
                </div>
                <div  style="display: none;color: #f62b2b;" id="u_exist_error"><b>This username already exist, please choose another one.</b></div>
            </div>
        </div>

        <div class="form-group col-lg-12">
			<label class="col-lg-2">Password</label>
			<div class="col-lg-6">
				<input type="text" name="password" class="form-control" id="password"/>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="alert alert-info" id="system_create" style="display: none;">
		<input type="hidden" id="realm" name="realm" value="<?php echo $realm;?>"/>
		A username and password will be created for you, and you will receive
		these details via email.
	</div>
<?php } else { ?>
	<div class="alert alert-info">
		<input type="hidden" id="realm" name="realm" value="<?php echo $realm;?>"/>
		A username and password will be created for you, and you will receive
		these details via email.
	</div>
<?php } ?>
<div>
    <?php echo ADDITIONAL_ORDER_MESSAGE; ?>
</div>
	<div style="text-align:center">
		<input  type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>" />
		<input type="button" value="Go to Checkout" class="btn btn-success btn-lg" id="submit_button"/>
	</div>
<?php  echo form_close(); ?>

    </div>
</div>
</div>
