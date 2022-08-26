<h3>Manage Systemt Messages</h3>

<?php
$category_options = array (
		'all_messages' => array (
				'title' => 'List All System Messages',
				'function' => 'all_messages',
				'description' =>
				"You can select a page,it will list all messages will show in this page and then you can customizable the message's contents."
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
