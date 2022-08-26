<h3>Manage Message Templates</h3>

<?php

$category_options = array (
	'all_email' => array (
		'title' => 'List Existing Email Templates',
		'function' => 'all_email',
		'description' =>
			"You can select an email template and customizable the email's contents."
	),
    'sms_templates' => array(
        'title' => 'List Existing SMS Templates',
        'function' => 'sms_templates',
        'description' =>
            "You can select an SMS template and customizable the SMS's contents."
    )
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
