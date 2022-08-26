<h3><?php echo $order_type ?> Orders</h3>

<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
	<?php
}

if (isset($messages['error_message']) && trim($messages['error_message']) != '') {
    ?>
    <div class="alert alert-danger">
        <?php echo $messages['error_message'] ?>
    </div>
<?php
}

if ($num_per_page > $num_orders) {
	$num_per_page = $num_orders;
}

if (!empty($orders)) {
	echo "<div class='pull-right'>$showing</div>";
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Client', 'Product Ordered', 'Date Ordered', 'Cost', 'Status', 'Actions'));
	
	foreach ($orders as $product_id=>$order_data) {

        //var_dump($order_data);
        //die();

		$status = ucfirst($order_data['status']);
		$date = date('d/m/Y', strtotime($order_data['date']));
		$product_data = $order_data['product_data'];
		$user = $order_data['user'];
		$name = $product_data['name'];
		$cost = $order_data['price'];
		$order_id = $order_data['order_id'];
		$manage_page = anchor("admin/manage_order/$order_id", 'Manage', 'class="btn btn-sm btn-primary"');


        //var_dump($order_data['fibre']);
        //echo "<hr/>";

        if (($order_data['service_type'] == 'fibre-data')  || ($order_data['service_type'] == 'fibre-line' ) ) {

            $name = '';
            if (!empty($order_data['fibre']['product_name']))
                $name = $order_data['fibre']['product_name'] . "<br/>";

            $name .= "(fibre " . $order_data['fibre']['fibre_type'] . ")";
        }
		
		if ($status != 'Pending cancellation' && $status != 'Cancelled' && $status != 'Deleted') {
			$delete_page = anchor("admin/delete_order/$order_id", 'Cancel', 'class="btn btn-sm btn-danger"');
		} else {
			$delete_page = '';
		}
		$actions = $manage_page.' '.$delete_page;
		$actions = array('data' => $actions, 'style' => 'width:150px;');
		$this->table->add_row( array( $user, $name, $date, $cost, $status, $actions ));
	}
	
	echo $this->table->generate();
	
	echo "<div class='pull-right'>$pages</div>";

}else{
	echo '<div class="alert alert-warning">
			<strong>Order not found.</strong> It seems that there is no order!
		  </div>';
}
?>