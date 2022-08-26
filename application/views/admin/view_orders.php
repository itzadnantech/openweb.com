 <script type="text/javascript" language="javascript">
	function check() {
		var p = $('#select_p').val();
		var s = $('#select_s').val();
		if (p || s) {
			$("#form_filter_service").submit();
		} else {
			return false;
		}
	}
</script>

<div class="container" style="margin: 20px 0 30px 0;">
	<fieldset>
		<div class="col-lg-2">
			<?php echo anchor("admin/assign_order/$id_user", 'Add Service', array('class' => 'form-control btn btn-default')); ?>
		</div>
	</fieldset>
</div>
<h3>List of Services</h3>

<?php

echo form_open('admin/filter_service', array('class' => 'form-inline', 'id' => 'form_filter_service')); ?>
<div class="form-group">
	<label class="control-label col-lg-2">Service Name:</label>
	<div class="col-lg-4">
		<select class="form-control valid" name="pro_id" id="select_p" onchange="check()">
			<option value="all">ALL</option>
			<?php
			if (isset($product)) {
				foreach ($product as $p => $r) {
					$name = ucfirst($r['product_name']);
					$id = $r['product_id'];
					if (isset($pro_id) && $id == $pro_id) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option $selected value='$id'>$name</option>";
				}
			}
			?>
		</select>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-1">Status:</label>
	<div class="col-lg-3">
		<select class="form-control valid" name="status" id="select_s" onchange="check()">
			<option value="all">ALL</option>
			<?php
			if (isset($status_data)) {
				foreach ($status_data as $p => $s) {
					$i = $s['status'];
					$s = ucfirst($s['status']);
					if (isset($status) && $i == $status) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option $selected value='$i'>$s</option>";
				}
			}
			?>
		</select>
	</div>
</div>
<?php echo form_close(); ?>
<br /><br />
<?php
$success_message = $this->session->flashdata('success_message');
$error_message = $this->session->flashdata('error_message');
if (isset($success_message) && (trim($success_message) != '')) {
	echo "<div class='alert alert-success'>$success_message</div>";
}

if (isset($error_message) && (trim($error_message) != '')) {
	echo "<div class='alert alert-danger'>$error_message</div>";
}

if (!empty($orders)) {
	echo "<div class='pull-right'>$showing</div>";

	$tmpl = array('table_open'  => '<table class="table">');
	$this->table->set_template($tmpl);
	// $this->table->set_heading(array('MSISDN', 'Service type', 'Network', 'Service Name', 'Price', 'Service Rata Extra', 'Date Ordered', 'Status', 'Actions'));
	$this->table->set_heading(array('MSISDN', 'SIM Serial', 'Service type', 'Network', 'Service Name', 'Price', 'Date Ordered', 'Status', 'Actions'));
	foreach ($orders as $product_id => $order_data) {

		//   var_dump($order_data);
		//echo "<hr/>";


		$status = ucfirst($order_data['status']);
		$clinet = $order_data['user'];
		$date = date('d/m/Y', strtotime($order_data['date']));
		$product_data = $order_data['product_data'];
		$name = '';
		if (isset($product_data['product_settings']['name']))
			$name = $product_data['product_settings']['name'];

		$id = $order_data['id'];
		$price = $order_data['price'];
		$pro_rata = $order_data['pro_rata_extra'];
		$sim_serial = $order_data['fibre']['sim_serial_no'];

		$service_type = $order_data['service_type'];
		$ac_type = $order_data['fibre']['lte_type'];
		$delete = anchor("admin/cancel_order/$id", 'Cancel', 'class="btn btn-danger btn-sm"');
		$edit = anchor("admin/edit_order/$id", 'Manage', 'class="btn btn-primary btn-sm"');
		$total_remove = anchor("admin/local_order_remove/$id", 'Local remove', 'class="btn btn-warning btn-sm"');
		$stats = anchor("admin/lte_order_stats/" . $id . '/' . $order_data['fibre']['lte_type'] . '/' . $order_data['fibre']['fibre_data_username'], 'Stats', 'class="btn btn-primary btn-sm"');
		$stats_mobile = anchor("admin/mobile_order_stats/" . $id . '/mobile/' . $order_data['fibre']['fibre_data_username'], 'Stats', 'class="btn btn-primary btn-sm"');

		if (trim($status) == 'Pending') {
			$active = anchor("admin/activate_order/$id", 'Activate', 'class="btn btn-info btn-sm"');
		} elseif (trim($status) == 'Pending cancellation' || trim($status) == 'Deleted' || trim($status) == 'Cancelled') {
			$delete = '';
			$active = '';
		} else {
			$active = '';
		}
		//$actions = "$edit $delete";

		$ac_username = '';
		if (isset($order_data['acc_username']))
			$ac_username = $order_data['acc_username'];
		$ac_password = $order_data['acc_password'];
		$realm       = $order_data['realm'];
		$account_info = "<div>Username: $ac_username</div>
						 <div>Password: $ac_password</div>";

		$clinet = array('data' => $clinet, 'style' => 'width:100px;');

		// $acc_view_info  = array('data' => $ac_username . "@" . $realm, 'style' => 'width:100px;');
		$acc_view_info  = array('data' => $ac_username, 'style' => 'width:100px;');

		$name = array('data' => $name, 'style' => 'width:250px;');
		$date = array('data' => $date, 'style' => 'width:120px;');
		$account_info = array('data' => $account_info, 'style' => 'width:200px;');
		$status =  array('data' => $status, 'style' => 'width:50px;');
		//$actions = array('data' => $actions, 'style' => 'width:160px;');		
		// old $this->table->add_row( array($clinet, $name, 'R'.$price, 'R'.$pro_rata, $date, $status, $edit, $delete));

		if (isset($order_data['service_type']) && ($order_data['service_type'] == 'fibre-data')) {

			$acc_view_info  = $order_data['fibre']['acc_username'];

			// $acc_view_info  = $order_data['fibre']['acc_username'] . '@' . $order_data['realm'] .
			// 	" (" . $order_data['fibre']['fibre_data_provider'] . ")";


			$name = $order_data['fibre']['product_name'];
			$edit = anchor("#", 'Manage', 'class="btn btn-primary btn-sm disabled"');
		}


		if (isset($order_data['service_type']) && ($order_data['service_type'] == 'fibre-line')) {

			$acc_view_info  = $order_data['fibre']['fibre_line_number'];
			$name = $order_data['product_data']['product_settings']['name'];
			$edit = anchor("#", 'Manage', 'class="btn btn-primary btn-sm disabled"');
		}

		if (isset($order_data['service_type']) && ($order_data['service_type'] == 'lte-a')) {

			$acc_view_info  = $order_data['acc_username'];
			// $acc_view_info  = $order_data['acc_username'] . '@' . $order_data['realm']
			// 	. " (" . $order_data['fibre']['fibre_type'] . ")";


			$name = $order_data['fibre']['product_name'];
			$edit = anchor("admin/edit_lte_order/$id", 'Edit', 'class="btn btn-primary btn-sm"');
		}
		if (isset($order_data['service_type']) && ($order_data['service_type'] == 'mobile')) {

			$acc_view_info  = $order_data['acc_username'];
			// $acc_view_info  = $order_data['acc_username'] . '@' . $order_data['realm']
			// 	. " (" . $order_data['fibre']['fibre_type'] . ")";
			$name = $order_data['fibre']['product_name'];
			$edit = anchor("admin/edit_mobile_order/$id", 'Edit', 'class="btn btn-primary btn-sm"');
		}


		/*
        if (isset($order_data["service_type"]) && ($order_data["service_type"] == "showmx-sub") ){

            $acc_view_info = "Activation code : " . $order_data["showmax_subscription"]["activation_code"];
            $name = "Showmax Subscription";
            $edit = anchor("admin/edit_showmax_order/$id", 'Manage', 'class="btn btn-primary btn-sm"');

            $delete = anchor("#", 'Cancel', 'class="btn btn-danger btn-sm disabled"');
            $total_remove = anchor("#", 'Local remove', 'class="btn btn-warning btn-sm  disabled"');
        }
*/
		// -------------------------------------------------------------------------


		$port_active = 0;
		if (isset($order_data['product_data']['product_settings']['port_active']))
			$port_active = $order_data['product_data']['product_settings']['port_active'];


		if ($port_active && $order_data['status'] == 'active') {
			$port_button =  anchor("admin/reset_port/$id", 'Reset port', 'class="btn btn-success btn-sm" style="margin-top:5px;" ');
			$edit .= "<br/>" . $port_button;
		}

		// $row = array($acc_view_info, $service_type, $ac_type, $name, 'R' . $price, 'R' . $pro_rata, $date, $status, $edit, $delete);
		$row = array($acc_view_info, $sim_serial, $service_type, $ac_type, $name, 'R' . $price, $date, $status, $edit, $delete);

		if (isset($enable_delete) && ($enable_delete == '1')) {
			array_push($row, $total_remove);
		}

		if (isset($order_data['service_type']) && ($order_data['service_type'] == 'lte-a')) {
			array_push($row, $stats);
		}

		if (isset($order_data['service_type']) && ($order_data['service_type'] == 'mobile')) {
			array_push($row, $stats_mobile);
		}

		$this->table->add_row($row);
	}

	echo $this->table->generate();

	echo "<div class='pull-right'>$pages</div>";
} else {
?>
	<div class="alert alert-warning">
		<strong>Order not found.</strong> It seems that there is no order find!
	</div>
<?php
}
?>