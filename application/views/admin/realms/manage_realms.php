<h3>Manage Realms</h3>

<?php
$category_options = array (
	'create_realm' => array (
		'title' => 'Create a New Realm',
		'function' => 'create_realm',
		'description' =>
			'Create a new realm for your realms.'
	),
	/* 'edit_realm' => array (
		'title' => 'Edit an Existing Realm',
		'function' => 'edit_realm',
		'description' => 
			'You can select a realm and update it information.',
	), */
	'all_realms' => array (
			'title' => 'List Existing Realm',
			'function' => 'all_realms',
			'description' =>
			'You can select a realm and edit its information.'
	),
);
?>
<ul>
<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
if (!empty($category_options)) {
    $count = 0;
	foreach ($category_options as $u=>$o) {
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
