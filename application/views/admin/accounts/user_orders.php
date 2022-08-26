<script type="text/javascript" language="javascript">
function check(){
	var a = $('#select-u').val();
	if(a){
		$("#form_select_user").submit();
	}else{
		return false;
	}
}
</script>
<h3>User Orders</h3>
<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
<?php }
if (isset($messages['error_message']) && trim($messages['error_message']) != '') {
    ?>
    <div class="alert alert-danger">
        <?php echo $messages['error_message'] ?>
    </div>
<?php } ?>

<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
	<?php
	if (isset($user_list)) { ?>
	<?php echo form_open('admin/select_user_orders', array('class' => 'form-inline','id' => 'form_select_user')); ?>
		<label class="col-lg-2">Select User</label>
		<div class="col-lg-5">
		<select  name="user" id="select-u" class="form-control" onchange="check()">
			<?php
			foreach($user_list as $u) {
				$n = "{$u['first_name']} {$u['last_name']} ({$u['username']})";
				$i = $u['username'];
				if (isset($user) && $i == $user) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				echo "<option $selected value='$i'>$n</option>";
			}
			?>
		</select>
		</div>
		<div class="col-lg-3">
			<!-- <input type="submit" class="btn btn-primary" value="Select" > -->
		</div>
		<?php echo form_close();?>
	<?php }?>
</div>
<?php if (isset($user) && !empty($user_orders)) {
	echo "<div class='pull-right'>$showing</div>";
	
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Client','Product Name', 'Date Ordered', 'Account Info', 'Status', 'Actions'));
	
	foreach ($user_orders as $product_id=>$order_data) {
		$status = ucfirst($order_data['status']);
		$clinet = $order_data['user'];
		$date = date('d/m/Y', strtotime($order_data['date']));
		$product_data = $order_data['product_data'];
        $name = '';
        if (isset($product_data['product_settings']['name']))
		    $name = $product_data['product_settings']['name'];
		$id = $order_data['id'];		
		
		$delete = anchor("admin/delete_order/$id", 'Cancel', 'class="btn btn-danger btn-sm"');
		$edit = anchor("admin/manage_order/$id", 'Manage', 'class="btn btn-primary btn-sm"');
		
		if(trim($status) == 'Pending'){
			//$active = anchor("admin/activate_order/$id", 'Activate', 'class="btn btn-info btn-sm"');
		}elseif (trim($status) == 'Pending cancellation' || trim($status) == 'Deleted' || trim($status) == 'Cancelled'){
			$delete = '';
		}
		$actions = "$edit $delete";

        switch ($order_data['service_type']) {

            case 'fibre-data' : $account_info  = "<div>Fibre Username  : " . $order_data['fibre']['fibre_data_username']  . "</div>";
                                $account_info .= "<div>Password  : " . $order_data['fibre']['fibre_data_password']  . "</div>";
                                $account_info .= "<div>Provider : " . $order_data['fibre']['fibre_data_provider'] . "</div>";
                break;
            case 'fibre-line' : $account_info = "<div>Line number : " . $order_data['fibre']['fibre_line_number'] . "</div>";
                                break;
            default :
                    $ac_username = $order_data['acc_username'];
                    $ac_password = $order_data['acc_password'];
                    $account_info = "<div>Username: $ac_username</div>
                             <div>Password: $ac_password</div>";
                    break;
        }

        if (isset($order_data['service_type']) && ($order_data['service_type'] == 'fibre-data') ){

            // $acc_view_info  = $order_data['fibre']['fibre_data_username'] .
            //    " (" . $order_data['fibre']['fibre_data_provider'] . ")";
            $name = $order_data['fibre']['product_name'];
            $edit = anchor("#", 'Manage', 'class="btn btn-primary btn-sm disabled"');
        }

        if (isset($order_data['service_type']) && ($order_data['service_type'] == 'fibre-line') ){

            // $acc_view_info  = $order_data['fibre']['fibre_line_number'];
            $name = $order_data['fibre']['product_name'];
            $edit = anchor("#", 'Manage', 'class="btn btn-primary btn-sm disabled"');
        }

		
		$clinet = array('data' => $clinet, 'style' => 'width:13%');
		$name = array('data' => $name, 'style' => 'width:20%');
		$date = array('data' => $date, 'style' => 'width:10%');
		$account_info = array('data' => $account_info, 'style' => 'width:32%');
		$actions = array('data' => $actions, 'style' => 'width:20%');
		$status =  array('data' => $status, 'style' => 'width:5%');
		
		$this->table->add_row( array($clinet, $name, $date, $account_info, $status,$actions)); 
	}
	
	echo $this->table->generate();
	
	echo "<div class='pull-right'>$pages</div>";
?>
<?php }else{ ?>
	<div class="alert alert-warning">
		<strong>Order not found.</strong> It seems that there is no order for this user!
	</div>
<?php  }?>