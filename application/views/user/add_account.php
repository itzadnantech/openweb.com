<?php
if($cloudsl){
	if($account){
	//var_dump($account);
	echo "<h3>Account Infomation</h1>";
	//echo form_open("user/add_account");
	$this->load->library('table');
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	echo $this->table->set_heading('username','password','Product','Status','Action');
	if($credit>0){
		foreach ($account as $key=>$a){
			//$manager_page=anchor("#", 'Load Service', 'class="btn btn-sm btn-primary"');
			if($a['product']==0)
			{
				$product_name[$key]='NONE';
				$manage_page=anchor("user/list_order_cloudsl/".$a['id'], 'Order', 'class="btn btn-sm btn-primary"');
			}
			else 
			{
				if($a['status']=='pending'){
					if($a['date_update']==NULL)
						$manage_page=anchor("user/active_service_cloudsl/".$a['id'], 'Active', 'class="btn btn-sm btn-primary"');
					else 
						$manage_page=anchor("user/edit_service_cloudsl/".$a['id'], 'Manage', 'class="btn btn-sm btn-primary"');
				}
					//$manage_page=$manage_page.' '.anchor("#", 'Cancle', 'class="btn btn-sm btn-primary"');
				
				else{
					//$product=$a['product'];
					$manage_page=anchor("user/edit_service_cloudsl/".$a['id'], 'Manage', 'class="btn btn-sm btn-primary"');
				}
				if($a['status']=='pending cancellation'){
					$manage_page=$manage_page.' '.anchor("user/revoke_order/".$a['id'], 'Revoke', 'class="btn btn-sm btn-primary"');
				}
				else {
					$manage_page=$manage_page.' '.anchor("user/cancel_order/".$a['id'], 'Cancle', 'class="btn btn-sm btn-primary"');
				}
			}
			if($a['account_username']==''){
				$manage_page='';
				$product_name[$key]='';
				$a['status']='';
			}
			$this->table->add_row(array($acc_username[$key], $a['account_password'],$product_name[$key],$a['status'],$manage_page));
		}
	}
	else{
		foreach ($account as $key=>$a){
			//$manager_page=anchor("#", 'Load Service', 'class="btn btn-sm btn-primary"');
			if($a['product']==0)
			{
				$product_name[$key]='NONE';
				//$manager_page=anchor("user/list_order_cloudsl/", 'Order', 'class="btn btn-sm btn-primary"','disabled=\'true\'');
				$manage_page="<input type='button'value='Order'disabled='true'class='btn btn-sm btn-primary'/>";
			}
			else
			{
				//$product=$a['product'];
				/* $manage_page="<input type='button'value='Manage'disabled='true'class='btn btn-sm btn-primary'/>";
				$manage_page=$manage_page.' '.anchor("#", 'Cancle', 'class="btn btn-sm btn-primary"'); */
				if($a['status']=='pending'){
					$manage_page="<input type='button' value='Active' disabled='true'class='btn btn-sm btn-primary'/>";
				}
					//$manage_page=$manage_page.' '.anchor("user/cancel_order/".$order_id[$keys], 'Cancle', 'class="btn btn-sm btn-primary"');
				
				else{
					//$product=$a['product'];
					$manage_page="<input type='button' value='Manage' disabled='true'class='btn btn-sm btn-primary'/>";
				}
				if($a['status']=='pending cancellation'){
					$manage_page=$manage_page.' '."<input type='button' value='Revoke' disabled='true'class='btn btn-sm btn-primary'/>";
				}
				else {
					$manage_page=$manage_page.' '.anchor("user/cancel_order/".$a['id'], 'Cancle', 'class="btn btn-sm btn-primary"');
				}
				
				
			}
			if($a['account_username']==''){
				$manage_page='';
				$product_name[$key]='';
				$a['status']='';
			}
			$this->table->add_row(array($acc_username[$key], $a['account_password'],$product_name[$key],$a['status'],$manage_page));
		}
	}
	echo $this->table->generate();
	}
	$num_acc=count($account);
	if($credit==0&&$num_acc>0&&$account[0]['account_username']!=''){
		echo "please load credit!";
	}
	else{
	
	echo "<h3>add account:</h1></br>";
	echo form_open('user/add_account',array('class' => 'form-horizontal'));
		?>
		<div class="form-group">
			<label class="control-label col-lg-2">Username:</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" name="account_username" id="account_username" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-2">Password:</label>
			<div class="col-lg-6">
				<input type="password" class="form-control" name="account_password" id="account_password" />
			</div>
		</div>
		<div style="letter-spacing: 100px;padding-left: 100px;">
			
				<input type="submit" class="btn btn-sm btn-primary" value="Add" name='account'/>
		
		</div>
		<?php 
	echo form_close();
	}
}
else{
	echo "please active your cloudsl!";
}
