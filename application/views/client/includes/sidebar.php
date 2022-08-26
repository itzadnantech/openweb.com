<?php 
$username = $this->session->userdata('username');
if (!empty($this->site_data['first_name'])) {
	$first_name = $this->site_data['first_name'];
} else {
	$first_name = 'administrator';
}
$role = $this->session->userdata('role');
?>
<?php $cur_page = $this->uri->rsegment(2); ?>
<div class="well">
<ul class="nav nav-pills nav-stacked">
  <!-- <li><div class="text-muted">Account Settings Menu</div></li> -->
  <?php
$pages = array (
	// href => title
	'settings' => 'Account Settings',
	'billing' => 'Billing Settings',
	'change_password' => 'Change Password',
	'logout' => 'Log Out',
);
foreach ($pages as $h=>$d) {
	echo '<li ';
	if ($h == $cur_page) {
		echo 'class="active"';
	}
	echo ">";
	echo anchor('user/'.$h, $d);
	echo "</li>";
}
if ($role == 'admin') {
	echo '<li>' . anchor('admin/dashboard', 'Return to Admin') . '</li>';
}
?>
</ul>
</div>