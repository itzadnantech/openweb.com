<?php
	$class_list = $classes['class_list'];
	//print_r($class_list);
	
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Class Title', 'Class Description', 'Realm','Edit'));	
	
	if(!empty($class_list)) {
		foreach ($class_list as $c) {
			//var_dump($c);die();
			$class_id = $c['table_id'];
			$edit_page = anchor("admin/edit_class/$class_id", 'Edit', 'class="btn btn-sm btn-primary"');
			$this->table->add_row( array($c['id'], $c['desc'], $c['realm'],$edit_page));
		}
	}
	echo $this->table->generate();
?>