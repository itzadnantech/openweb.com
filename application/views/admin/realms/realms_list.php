<h3><?php echo $realms_type ?> Realms</h3>
<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
	<?php
}


if ($num_per_page > $num_realm) {
	$num_per_page = $num_realm;
}
echo "<div class='pull-right'>$showing</div>";

if (!empty($realms)) {
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Realm', 'User','Edit'));
	
	foreach ($realms as $realm_id => $realm_data) {
		$realm_id = $realm_data['id'];
		$realm = $realm_data['realm'];
		$user = $realm_data['user'];
		
		$manage_page = anchor("admin/edit_realm/$realm_id", 'Edit', 'class="btn btn-sm btn-primary"');
		
		$this->table->add_row( array( $realm, $user, $manage_page));
	}
	
	echo $this->table->generate();
	
	echo "<div class='pull-right'>$pages</div>";
}
?>