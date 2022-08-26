

<h3>User Management</h3>

<?php

$stats_options = array (
	'user_role_rights' => array (
		'title' => 'User Roles and Rights',
		'function' => 'user_roles_and_rights',
		'description' =>'All Super Amdin to set Users Access Level Permisions.'
	),
		'button_logger_view' => array (
		'title' => 'Button Logger',
		'function' => 'button_logger_view',
		'description' =>'Allow you to view Power Buttons Logs.'
	),
		'set_permissions_on_buttons' => array (
		'title' => 'Button Permissions',
		'function' => 'set_permissions_on_buttons',
		'description' =>'Allow you to set Permissions on Power Buttons.'
	)
);
?>
<ul>
<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
if (!empty($stats_options)) {
    $count = 0;
	foreach ($stats_options as $u=>$o) {
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

