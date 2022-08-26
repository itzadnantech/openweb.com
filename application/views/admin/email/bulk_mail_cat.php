<h3>Manage Bulk Mailing</h3>

<?php
$category_options = array (
	'create_category' => array (
		'title' => 'Bulk Mailing',
		'function' => 'bulk_mail',
		//'description' => 'Create a parent category for clients products.'
	),
	'all_category' => array (
		'title' => 'Reseller Bulk Mailing',
		//'function' => 'manage_categories',
		'function' => 'reseller_bulk_mail',
		//'description' => 'You can select a category and edit its information.'
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