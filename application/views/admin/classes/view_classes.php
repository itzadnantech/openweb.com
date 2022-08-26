<h3><?php echo $class_type ?> Classes</h3>
<?php
	if (isset($messages['success_message']) && trim($messages['success_message']) != '' ) {
		$m = $messages['success_message'];
		echo "<div class='alert alert-success'>$m</div>";
	}
	$class_list = $classes['class_list'];
	//print_r($class_list);
	echo "<div class='pull-right'>$showing</div>";
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Class ID', 'Class Description', 'Realm','Edit'));	
	
	if(!empty($class_list)) {
		foreach ($class_list as $c) {
			$class_id = $c['table_id'];
			$edit_page = anchor("admin/edit_class/$class_id", 'Edit', 'class="btn btn-sm btn-primary"');
			$this->table->add_row( array($c['id'], $c['desc'], $c['realm'],$edit_page));
		}
	}
	echo $this->table->generate();
	echo "<div class='pull-right'>$pages</div>";
?>