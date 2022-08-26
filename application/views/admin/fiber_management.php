<h3>Fibre Management</h3>

<?php

$stats_options = array (
	'fibre_coverage_map' => array (
		'title' => 'Fibre Coverage Map',
		'function' => 'fibre_coverage_map',
		'description' =>
			 'This option allows you open Universal Fibre Coverage Map.'
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