<div style="padding-left: 100px;">
<h3>Manage Admins</h3>

<?php
$users_options = array (
	'create_admin' => array (
		'title' => 'Create a New Admin',
		'function' => 'create_account',
		'description' =>
			'You can create a new admin account.'
	),
	'admin_list' => array (
		'title' => 'List Existing Admin',
		'function' => 'all_account',
		'description' => 
			'You can select a admin and update or delete his or her personal settings.'
	),
);
?>
<ul>
<?php
if (!empty($users_options)) {
	foreach ($users_options as $u=>$o) {
		$t = $o['title'];
		$f = $o['function'];
		$d = $o['description'];
		echo '<dl>';
		echo "<dt><a href='$u' >$t</a></dt>";
		echo "<dd>$d</dd>";
		echo '</dl>';
	}
}
?>
</ul>

</div>