<h3>Confirmation</h3>
<?php

if (isset($order_id) && trim($order_id) != '') {
        ?>
        <div class="well">
            <div class="lead">Remove Request</div>
            <h4 style="margin-bottom:20px">Are you sure you want to remove this order?</h4>
            <?php echo anchor("admin/local_order_remove/$order_id/true", 'Confirm Remove', 'class="btn btn-danger"'); ?>
            <?php echo anchor("admin/user_service/", 'Do Not Remove', 'class="btn btn-primary"'); ?>
        </div>
    <?php

}

?>