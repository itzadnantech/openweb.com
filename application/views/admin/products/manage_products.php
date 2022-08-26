<h3>Manage Products</h3>

<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
if (isset($messages['success_message']) 
	&& trim($messages['success_message']) != '' ) {
	$m = $messages['success_message'];
	echo "<div class='alert alert-success'>$m</div>";
}

$product_options = array (
	'create_product' => array (
		'title' => 'Create a New Product',
		'function' => 'create_product',
		'description' =>
			'Create a new offering for your users.'
	),
	'all_product' => array (
			'title' => 'List Existing Product',
			'function' => 'manage_product',
			'description' =>
			'You can select a product and edit or delete its information.'
	),
    'all_nosvc_product' => array (
        'title' => 'List (undefined - NOSVC) Product',
        'function' => 'manage_nosvc_product',
        'description' =>
            'You can select a product and edit or delete it information.'
    ),
	/* 'edit_product' => array (
		'title' => 'Edit an Existing Offering',
		'function' => 'edit_product',
		'description' => 
			'Edit an existing product (price, discounts, etc.)'
	),
	'delete_product' => array (
		'title' => 'Remove an Existing Offering',
		'function' => 'delete_product',
		'description' => 
			'Deletes an existing product from the records',
	), */
);
?>
<ul>
<?php
if (!empty($product_options)) {
    $count = 0;
	foreach ($product_options as $p=>$o) {
		$t = $o['title'];
		$f = $o['function'];
		$d = $o['description'];
		if(check_acess($f,$role_data['role_code'])){
		    echo '<dl>';
    		echo "<dt><a href='$p' >$t</a></dt>";
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