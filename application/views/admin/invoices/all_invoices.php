<script type="text/javascript" language="javascript">
function check(){
	var a = $('#select-u').val();
	if(a){
		$("#form_select_user").submit();
	}else{
		return false;
	}
}
</script>
<?php if(isset($username)){ ?>
<div class="container" style="margin: 20px 0 30px 0;">
	<fieldset>								
		<div class="col-lg-2">
			<?php echo anchor("admin/create_invoice/$username", 'Add Invoice', array('class' => 'form-control btn btn-default')); ?>
		</div>
	</fieldset>
</div> 
<?php }?>
<h3>Invoices List</h3>
<?php 
if (!empty($error_message)) {
	echo "<div class='alert alert-danger'>$error_message</div>";
}
if (!empty($success_message)) {
	echo "<div class='alert alert-success'>$success_message</div>";
}
?>	
<fieldset>
<?php if(isset($username)){ ?>
<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
	<label class="col-lg-2">Username</label>
	<div class="col-lg-5">
		<?php echo $username;?>
	</div>
</div>
<?php }else{?>
<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
	<?php
	if (isset($user_list)) { ?>
	<?php echo form_open('admin/all_invoices', array('class' => 'form-inline','id' => 'form_select_user')); ?>
		<label class="col-lg-2">Select User</label>
		<div class="col-lg-5">
		<select  name="user" id="select-u" class="form-control" onchange="check()">
			<?php
			foreach($user_list as $u) {
				$n = "{$u['first_name']} {$u['last_name']} ({$u['username']})";
				$i = $u['username'];
				if (isset($user) && $i == $user) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				echo "<option $selected value='$i'>$n</option>";
			}
			?>
		</select>
		</div>
		<?php echo form_close();?>
	<?php }?>
</div>
<?php }?>
<?php 
	if(!empty($invoices)){
		$tmpl = array ( 'table_open'  => '<table class="table">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading(array('Username', 'Invoice ID','Invoice', 'Create Date', 'Actions'));
		
		foreach ($invoices as $invoices => $inv) {
			$invoice_id = $inv['id'];
			$invoice_title = $inv['invoice_name'];
			$user_name = $inv['user_name'];
			$date = date('Y-m-d', strtotime($inv['create_date']));
			$path = $inv['pdf_path'];
		
			$invoice_title = "<a href='".base_url().$path."' target='_blank'>".$invoice_title."</a>";
			$delete_page = anchor("admin/delete_invoice/$invoice_id", 'Delete', 'class="btn btn-sm btn-danger"');
			$send_invoice = anchor("admin/send_email/$invoice_id", 'Send Email', 'class="btn btn-sm btn-primary"');
			$actions = $delete_page.' '.$send_invoice;
			$this->table->add_row( array($user_name, $invoice_id, $invoice_title, $date, $actions));
		}
		
		echo $this->table->generate();
		echo "<div class='pull-right'>$pages</div>";
	}else{
?>
<div class="alert alert-warning">
	<strong>Invoice not found.</strong>
</div>
<?php 		
	}
?>
</fieldset>