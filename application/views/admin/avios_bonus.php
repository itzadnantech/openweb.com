<h3>Avios Awards</h3>
<?php
$role = $this->session->userdata('role');
$role_data = get_role_id($role);
$stats_options = array (
	'award_user_form' => array (
		'title' => 'Award users',
		'function' => 'award_user_form',
		'description' => 'Avios Award System or search the user.'
	),
	 'avios_stat' => array (
        'title' => 'Statistic',
        'function' => 'avios_stat',
        'description' => 'Retrieve the statistic of the users'
    ),
    	'avios_monthly' => array (
        'title' => 'Order without billing',
        'function' => 'avios_monthly',
        'description' => 'Retrieve the active orders'
    ),
     'avios_rules' => array (
        'title' => 'Billing Codes Rules	',
        'function' => 'avios_rules',
        'description' => 'Edit Rules For Avios Billing Codes.'
    )
);
?>
<ul>
<?php
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