<?php 
if(isset($modify_service) && !empty($modify_service)){
	if(isset($modfiy_product) && !empty($modfiy_product)){
		$modfiy_product = $modfiy_product;
	}else{
		$modfiy_product = '';
	}
	if($modify_service =='Upgrading'){
		$msg = "This service will be upgraded to ".$modfiy_product." next month.<br/>
				If you cancel this order, it will not be upgraded in the next month.<br/>
				Are you sure you want to cancel this order?";
	}elseif ($modify_service =='Downgrading'){
		$msg = "This service will be downgraded to ".$modfiy_product." next month.<br/>
				If you cancel this order, it will not be upgraded in the next month.<br/>
				Are you sure you want to cancel this order?";
	}
}
?>
<?php
if ($order_id && trim($order_id) != '') {
	if($status && $status == 'pending'){
?>
<div class="well">
	<div class="lead">Notices</div>
	<h4 style="margin-bottom:20px">
		 If you want to cancel the order,please send email to <font style="color: #428bca">cancellations@openweb.co.za</font>.
	</h4>
</div>
<?php 
	}else{
		if ($confirmation_type == 'delete') {
			if($cancel_flage == 1){
?>
		<div class="well">
			<div class="lead">Cancellation Request</div>
			<h4 style="margin-bottom:20px">
			<?php if (isset($msg)){
				echo $msg;
			}else{
				echo "Are you sure you want to cancel this order?";
			}?>
			</h4>
			<?php echo anchor("user/cancel_order/$order_id?confirm=true", 'Confirm Cancellation', 'class="btn btn-danger"'); ?>
			<?php echo anchor("user/orders", 'Do Not Cancel', 'class="btn btn-primary"'); ?>
		</div>
		<?php
			}else{
		?>		
		<div class="well">
			<div class="lead">Notices</div>
			<h4 style="margin-bottom:20px">
				This is a custom created product.  To cancel, kindly email <font style="color: #428bca">admin@openweb.co.za.</font>
			</h4>
		</div>	
		<?php 
			}
		}
	}
}
?>