<h3>All Admin</h3>
<?php
if(isset($succ_message) && $succ_message != ''){
	echo "<div class='alert alert-success'>".$succ_message."</div>";
}

if(isset($error_message) && $error_message != ''){
	echo "<div class='alert alert-danger'>".$error_message."</div>";
}

if (!empty($admin_list)) {
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Fist Name', 'Last Name', 'Email','Mobile Number' ,'Username', 'Role', 'Date', 'Discount','Status','Actions'));
	
	foreach ($admin_list as $account_id => $account_data) {
		$account_id = $account_data['id'];
		$first_name = $account_data['first_name'];
		$last_name = $account_data['last_name'];
		$email = $account_data['email_address'];
		$mobile = $account_data['mobile_number'];
		$username = $account_data['username'];
		$role =$account_data['role'];
		$date = date('d/m/Y', strtotime($account_data['joined']));
		$discount = $account_data['discount'];
		$status = $account_data['status'];
		
		$manage_page = anchor("super_administrator/edit_admin/$account_id", 'Manage', 'class="btn btn-sm btn-primary"');
		
		$login_username = $this->session->userdata('username');
		
		if ($username == $login_username) {
			$delete_page = "<input text='button' value='Delete' class='btn btn-sm btn-danger' style='width:58px;height:30px;background-color:gray;border-color:gray;'  disabled='disabled'/>";			
		} else {		
			$delete_page = anchor("super_administrator/delete_admin/$account_id", 'Delete', 'class="btn btn-sm btn-danger"');
		}
		$this->table->add_row( array( $first_name, $last_name, $email, $mobile,$username, $role, $date, $discount.'%', $status, $manage_page, $delete_page));
	}
	
	echo $this->table->generate();	
}