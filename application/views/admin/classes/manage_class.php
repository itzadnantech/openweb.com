<h3>Manage Classes</h3>

<?php

$category_options = array (
	'create_class' => array(
		'title' => 'Create a New Class',
		'function' => 'create_class',
		'description' => 'Create a new class for your classes.'
	),
	'view_classes' => array (
		'title' => 'List Existing Classes',
		'function' => 'view_classes',
		'description' => 'You can select a class and edit its information.'
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
