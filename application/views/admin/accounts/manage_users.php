<h3>Manage Users</h3>

<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
$users_options = array (
	'create_account' => array (
		'title' => 'Create a New Account',
		'function' => 'create_account',
		'description' =>
			'You can create a new user, with personal and billing information.'
	),
	'all_account' => array (
		'title' => 'List Existing Account',
		'function' => 'all_account',
		'description' => 
			'You can select a user and update or delete his or her billing and personal settings.'
	),
);
?>
<ul>
<?php
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