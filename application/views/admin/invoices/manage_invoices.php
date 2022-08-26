<h3>Manage Invoices</h3>

<?php

$users_options = array (
	'create_invoice' => array (
		'title' => 'Create a New Invoice',
		'function' => 'create_invoice',
		'description' =>
			'You can create a new invoice for user.'
	),
	'all_invoices' => array (
		'title' => 'List Existing Invoices',
		'function' => 'all_invoices',
		'description' => 
			'You can choose a user and delete his/her invoices.'
	),
);
?>
<ul>
<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
if (!empty($users_options)) {
    $count = 0;
	foreach ($users_options as $u=>$o) {
		$t = $o['title'];
		$f = $o['function'];
		$d = $o['description'];
		if(check_acess($f,$role_data['role_code'])){
		    echo '<dl>';
		    echo "<dt><a href='$u' >$t</a></dt>";
		    echo "<dd>$d</dd>";
		    echo '</dl>'; 
		    $count++;
		}
	}
	if($count==0){
        echo "<h4>You are not authorized to access this page.</h4>";
	}
}
?>
</ul>