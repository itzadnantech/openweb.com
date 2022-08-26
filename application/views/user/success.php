<?php
if(isset($type)){
	if($type == 'upgrade_next_month' || $type == 'downgrade'){
		$tips = "It will be started in the first day of next month.";
	}else{
		$tips = "It will be started immediately.";
	}
}
?>
<div class="lead">
Congratulations, your new <span class="text-info"><?php echo $product_name ?></span> account has been modified. <br/>
<?php echo $tips;?> <br/>
Here with your new Username and Password:
</div>
<div class="lead">
	<ul class="list-unstyled">
		<li>Username: <?php echo $username ?></li>
		<li>Password: <?php echo $password ?></li>
		<li>Comment: <?php echo $comment ?></li>
	</ul>
</div>