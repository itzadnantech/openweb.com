<?php
//var_dump($list);
//var_dump($cloudsl);
//var_dump($product_order);//die;
if($cloudsl){
	/* $this->load->library('jquery');
	$this->jquery->click('a.btn-primary',sumit()); */
	$this->load->library('table');
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	echo $this->table->set_heading('Product Nmae','Package Speed','Price','	Service Level','Action');
	if($credit>0){
		foreach ($list as $key=>$d){
			if($list[$key]['trial']==1)
				$list[$key]['trial']='Yes';
			else 
				$list[$key]['trial']='No';
			$status='';
			$manage_page=anchor("user/order_cloudsl/".$list[$key]['id'].'/'.$account_id, 'Order', 'class="btn btn-sm btn-primary"');
			$delete_page ='';
			$status='Not Order';
			$action=$manage_page.' '.$delete_page;
			//$this->table->add_row(array($list[$key]['name'], $list[$key]['price'], $list[$key]['trial'],$status,$action));
			$this->table->add_row(array($list[$key]['name'],$list[$key]['package_speed'], $list[$key]['price'],$list[$key]['service_level'],$action));
		}
	}
	else{
		foreach ($list as $key=>$d){

			if($list[$key]['trial']==1)
			{
				$list[$key]['trial']='Yes';
			}
			else {
				$list[$key]['trial']='No';
			}
				$status='';
				$manage_page="<input type='button' value='Order' disabled='true'class='btn btn-sm btn-primary'/>";
				$delete_page ='';
				$action=$manage_page.' '.$delete_page;
				$this->table->add_row(array($list[$key]['name'],$list[$key]['package_speed'], $list[$key]['price'],$list[$key]['service_level'],$action));
			//}
		}
	}
	echo $this->table->generate();
	if(isset($account_id)){
		echo form_open("user/order_cloudsl");
		echo form_hidden('account_id',$account_id);
		echo form_close();
	}
}
else 
	echo "please active your cloudsl!";
?>
