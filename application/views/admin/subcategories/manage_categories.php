<h3>Manage Sub-Categories</h3>
<?php

$subcategory_options = array (
	'create_subcategory' => array (
		'title' => 'Create a New Sub-Category',
		'function' => 'create_subcategory',
		'description' =>
			'Create a child category for your products.'
	),
	/* 'edit_subcategory' => array (
		'title' => 'Edit an Existing Sub-Category',
		'function' => 'edit_subcategory',
		'description' => 
			'Update an existing subcategory (use Update Product to change a product\'s category).',
	), */
	'all_subcategory' => array (
			'title' => 'List Existing Sub-Category',
			//'function' => 'manage_subcategories',
			'function' => 'all_subcategory',
			'description' =>
			'You can select a subcategory and edit its information.'
	),
);
?>
<ul>
<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
if (!empty($subcategory_options)) {
    $count = 0;
	foreach ($subcategory_options as $u=>$o) {
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