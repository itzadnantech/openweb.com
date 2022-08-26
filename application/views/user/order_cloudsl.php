<?php
if($account){
	echo form_open("user/order_cloudsl/".$id_product);
	$this->load->library('table');
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	
	echo $this->table->set_heading('Choose','Username','Password');
	 foreach ($account as $a){
	 	//$manage_page=anchor("user/order_cloudsl/".$id_product, 'Choose', 'class="btn btn-sm btn-primary"');
	 	$radio=form_radio('id',$a['id']);
	 	$this->table->add_row(array($radio,$a['account_username'], $a['account_password']));
	 }
	echo $this->table->generate();
	echo form_submit('order','submit','class="btn btn-sm btn-primary"');
	echo form_close();
}
else {
	
	echo anchor('user/add_account','Add account');
}