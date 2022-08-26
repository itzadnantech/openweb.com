<h3>Manage Categories</h3>

<?php
$category_options = array (
	'create_category' => array (
		'title' => 'Create a New Category for Clients',
		'function' => 'create_category',
		'description' =>
			'Create a parent category for clients products.'
	),
	/* 'edit_category' => array (
		'title' => 'Edit an Existing Category',
		'function' => 'edit_catgory',
		'description' => 
			'Update an existing category (use Update Product to change a product\'s category).',
	), */
	'all_category' => array (
		'title' => 'List Existing Category',
		//'function' => 'manage_categories',
		'function' => 'all_category',
		'description' =>
		'You can select a category and edit its information.'
	),
    'all_category_reseller' => array (
        'title' => 'List Existing Category for Resellers',
        //'function' => 'manage_categories',
        'function' => 'all_category_reseller',
        'description' =>
            'You can select a category and edit its information.'
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