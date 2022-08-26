 <script type="text/javascript" language="javascript">
$(document).ready(function() {
	$("#add_line").click(function(){
		var a = $('#detail').children().last().attr('id');
		b = parseInt(a)+1;
		
		var list = '<br>';
		list += '<div class="form-group"><div style="cursor: pointer;color: #d2322d;" class="col-lg-offset-8 col-lg-4 delete_line">Delete Line</div>';
		list += '<label class="control-label col-lg-2">Description:</label>';
		list += '<div class="col-lg-8"><textarea rows="5" cols="6" class="form-control valid" name="invoices['+b+'][des]"" ></textarea></div>';
		list += '</div>';
		list += '<div class="form-group"><label class="control-label col-lg-2">Amount:</label>';
		list += '<div class="col-lg-3"><div class="input-group"><span class="input-group-addon">R</span>';
		list += '<input class="form-control valid" type="text" name="invoices['+b+'][price]"/></div></div></div>';
		$('#detail').append($('<div id="'+b+'">'+ list +'</div>'));
	});

	$(".delete_line").live("click", function(){
		var a = $(this).parent().parent().attr('id');
		$('#'+a+'').remove();
	});

	$('#form_create_invoice').submit(function(){
		var des = $('#des').val();
		var amount = $('#price').val();

		var reg = new RegExp('^[0-9]*$');
		if(amount != '' && des != '' && reg.test(amount)){
			return true;
		}else{
			if(des == ''){
				$('#error_des_null').show();
				$('#error_amount_num').hide();
				$('#error_amount_null').hide();
			}else if(amount == ''){
				$('#error_amount_null').show();
				$('#error_des_null').hide();
				$('#error_amount_num').hide();
			}else if(!reg.test(amount)){
				$('#error_amount_num').show();
				$('#error_des_null').hide();
				$('#error_amount_null').hide();
			}
			return false;
		}
	});
});
</script>
<h3>Create Invoice</h3>
<fieldset>
<legend></legend>
<?php  
if(isset($_GET['error']) && !empty($_GET['error']))
{ 
	switch ($_GET['error']) {
		case 'date-format': 
			$error_message = "Please Follow Correct Date Format (2014-10-25)";
			break;
		
		default:
			# code...
			break;
	}
	echo "<div class='alert alert-danger'>$error_message</div>";
}
?>

<?php  echo form_open('admin/insert_manuall_invoice', array('class' => 'form-horizontal','id' => 'form_create_invoice')); ?>
<div id="detail" class="detail">
	<div id="0">
		<div class="form-group">
			<label class="control-label col-lg-2">Description:</label>	
			<div class="col-lg-8">
				<textarea rows="5" cols="6" class="form-control valid" name="invoices[0][des]" id="des" ></textarea>
				<p class="text-warning"  style="display: none;color: #f62b2b;font-weight:bold;" id="error_des_null"><small>This field is required</small></p>
			</div>
		</div>
		<div class="form-group"> 
			<label class="control-label col-lg-2">Amount:</label>
			<div class="col-lg-3">	
				<div class="input-group">
					<span class="input-group-addon">R</span>
					<input class="form-control valid" type="text"  id="price" name="invoices[0][price]"/>
				</div>
				<p class="text-warning"  style="display: none;color: #f62b2b;" id="error_amount_null"><small>This field is required</small></p>
			<p class="text-warning"  style="display: none;color: #f62b2b;font-weight:bold;" id="error_amount_num"><small>This field must a number</small></p>
			</div>
		</div>
	</div>
</div>

<div class="form-group" align="right">	
	<div class="col-lg-10">
		<div  style="cursor: pointer;color: #428bca" id="add_line">ADD Line</div>
	</div>
</div>

<hr/>
<div class="form-group">
	<label class="control-label col-lg-2">Create Date : </label>
	<div class="col-lg-5">
        <input class="form-control valid" placeholder="example : 2014-04-11 11:23:15" type="text"  id="create_date" name="create_date"/>
	    <h6>This field can be left empty if you want to set current date.</h6>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-2">Custom invoice ID : </label>
    <div class="col-lg-5">
        <input class="form-control valid" placeholder="example : 33123" type="text"  id="invoice_manual_id_element" name="invoice_custom_id"/>
        <!-- <h6>This value will </h6> -->
        <h6>Left this field empty if you want to generate ID automatically</h6>
        <h6>If you fill the both 'Custom ID' fields, the priority will be given to this one.</h6>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">Custom invoice ID (skip value) : </label>
    <div class="col-lg-5">
        <input class="form-control valid" placeholder="example : 5" type="text"  id="invoice_skip_id_element" name="invoice_skip_id"/>
        <!-- <h6>This value will </h6> -->
        <h6>Left this field empty if you want to generate ID automatically</h6>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="control-label col-lg-2">Payment Method:</label>
    <div class="col-lg-5">
        <select name="Payment" class="form-control">
            <option></option>
            <option value="credit_card_auto">Credit Card Auto Billing</option>
            <option value="debit_order">Debit Order</option>
            <option value="eft">EFT</option>
        </select>
    </div>
</div>


<div class="form-group" style="padding-top: 15px;">
	<label class="control-label col-lg-2">Attach the Invoice to:</label>
	<div class="col-lg-5">
	<?php if(isset($username) && !empty($username)){?>
		<label class="control-label col-lg-2"><?php echo $username; ?></label>
		<input type="hidden" name="user" value="<?php echo $username?>">
	<?php }else{?>
			<?php if (isset($user_list)) { ?>
			<select  name="user" id="select-u" class="form-control">
				<?php
				foreach($user_list as $u) {
					$n = "{$u['first_name']} {$u['last_name']} ({$u['username']})";
					$i = $u['username'];
					echo "<option value='$i'>$n</option>";
				}
				?>
			</select>
			<?php }?>
	<?php }?>
	</div>
</div>

<div class="form-group">
	<div class="col-lg-5 col-lg-offset-2 checkbox">
		<label>
			<input type="checkbox" name='send_invoice'><p style="font-size: 15px;font-weight: bold;">Send Invoice to Client</p>
		</label>
	</div>
</div>

<div class="form-group" style="padding-left: 580px;">
	<div class="col-lg-8">
		<input type="submit" value="Create an Invoice" class="btn btn-large btn-primary"/>
	</div>
</div>
<?php echo form_close();?>
</fieldset>