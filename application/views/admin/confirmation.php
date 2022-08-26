<h3>Confirmation</h3>
<?php

if (isset($order_id) && trim($order_id) != '') {
	if ($confirmation_type == 'delete') {
		?>
		<div class="well">
			<div class="lead">Cancel Request</div>
			<h4 style="margin-bottom:20px">Are you sure you want to cancel this order?</h4>
			<?php echo anchor("admin/delete_order/$order_id?confirm=true", 'Confirm Cancel', 'class="btn btn-danger"'); ?>
			<?php echo anchor("admin/manage_order/$order_id", 'Do Not Cancel', 'class="btn btn-primary"'); ?>
		</div>
		<?php
	}

}

if (isset($account_id) && trim($account_id) != '') {
	if ($confirmation_type == 'delete') {
		?>
		<div class="well">
			<div class="lead">Cancel Request</div>
			<h4 style="margin-bottom:20px">Are you sure you want to cancel this user?</h4>
			<?php echo anchor("admin/delete_user/$account_id?confirm=true", 'Confirm Cancel', 'class="btn btn-danger"'); ?>
			<?php echo anchor("admin/all_account", 'Do Not Cancel', 'class="btn btn-primary"'); ?>
		</div>
		<?php
	}
}


if (isset($topup_id) && trim($topup_id) != '') {
    if ($confirmation_type == 'topup_config_delete') {
        ?>
        <div class="well">
            <div class="lead">Cancel Request</div>
            <h4 style="margin-bottom:20px">Are you sure you want to delete this TopUp?</h4>
            <?php echo anchor("admin/delete_topup/$topup_id?confirm=true", 'Confirm Cancel', 'class="btn btn-danger"'); ?>
            <?php echo anchor("admin/all_topup", 'Do Not Cancel', 'class="btn btn-primary"'); ?>
        </div>
    <?php
    }

    if ($confirmation_type == 'topup_order_delete') {
        ?>
        <div class="well">
            <div class="lead">Cancel Request</div>
            <h4 style="margin-bottom:20px">Are you sure you want to delete this TopUp order?</h4>
            <?php echo anchor("admin/delete_topup_order/$topup_id?confirm=true", 'Confirm Cancel', 'class="btn btn-danger"'); ?>
            <?php echo anchor("admin/topup_reports", 'Do Not Cancel', 'class="btn btn-primary"'); ?>
        </div>
    <?php
    }
}

?>