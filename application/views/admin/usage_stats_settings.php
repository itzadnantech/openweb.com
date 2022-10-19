<h3>Usage Stats Settings</h3>

<?php

$stats_options = array (
	'lte_usage_stats_settings' => array (
		'title' => 'Manage LTE Usage Stats Settings',
		'function' => 'lte_usage_stats_settings',
		'description' =>
			'You can choose which items are displayed under Usage Summary.'
	),
	 'mtn_usage_summary' => array (
        'title' => 'MTN Usage Summary',
        'function' => 'mtn_usage_summary',
        'description' => 'Retrieve the current active sessions for users'
    ),
    	 'rica_mtn_sim' => array (
        'title' => 'RICA MTN SIM',
        'function' => 'rica_mtn_sim',
        'description' => 'RICA an MTN SIM that failed upon activation'
    ),
     'queue_topup_lte_account' => array (
        'title' => 'Queue Topup LTE account',
        'function' => 'queue_topup_lte_account',
        'description' => 'Queue a data topup for an LTE account.'
    )
    ,
	//  'add_new_lte_account' => array (
    //     'title' => 'Add new LTE account',
    //     'function' => 'add_new_lte_account',
    //     'description' => 'This is used to add a new LTE account.'
    // ),

    'create_new_lte_account' => array (
        'title' => 'Create new LTE Account with MTN SIM',
        'function' => 'create_new_lte_account',
        'description' => 'This option allows you to create a new LTE account on the IS system and assign an OpenWeb SIM in stock to the account.'
    ),
      'data_transfer' => array (
        'title' => 'Data Transfer',
        'function' => 'data_transfer',
        'description' => 'This option allows you to transfer data from one LTE account to another.'
    ),
    'unlock_mtn_sim_card_device_lock' => array (
        'title' => 'Unlock MTN SIM Card Device Lock',
        'function' => 'unlock_mtn_sim_card_device_lock',
        'description' => 'This option allows you to unlock MTN sim card device lock.'
    ),
    'mtn_sim_lock_status' => array (
        'title' => 'MTN SIM Lock Status',
        'function' => 'mtn_sim_lock_status',
        'description' => 'This option allows you to check MTN sim lock status.'
    ),
    'lte_usage_stats' => array(
        'title' => 'LTE Total Usage Stats Report',
        'function' => 'lte_stats_report',
        'description' => 'This option allows you to check usage info.'
    ),
    'sim_swap' => array(
        'title' => 'Perform an LTE SIM Swap',
        'function' => 'sim_swap',
        'description' => 'This option allows you perform LTE SIM Swap.'
    ),
    'telkom_coverage_map' => array(
        'title' => 'Telkom LTE Coverage Map',
        'function' => 'telkom_coverage_map',
        'description' => 'This option allows you open Telkom Coverage map .'
    ),
     'is_lte_account_search' => array(
        'title' => 'IS LTE Account (Cell C and MTN Fixed)',
        'function' => 'is_lte_account_search',
        'description' => 'This option allows you search an IS LTE Account (Cell C and MTN Fixed).'
    ),
    'telkom_lte_stats_requests' => array(
        'title' => 'Telkom LTE Account Stats Request)',
        'function' => 'telkom_lte_stats_requests',
        'description' => 'This option allows you to get Telkom LTE Account Stats Request Information.'
    ),  
    'mtn_stats_requests' => array(
        'title' => 'MTN LTE Account Stats Request)',
        'function' => 'mtn_stats_requests',
        'description' => 'This option allows you to get MTN LTE Account Stats Request Information.'
    ),  
    'cell_c_stats_requests' => array(
        'title' => 'CELL C LTE Account Stats Request)',
        'function' => 'cell_c_stats_requests',
        'description' => 'This option allows you to get CELL C LTE Account Stats Request Information.'
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