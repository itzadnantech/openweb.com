<?php

if($cloudsl)

	{

		if(isset($msg)&&$msg!=''){

			echo "<h3>".$msg."</h1>";

		}

		echo "<h3>My credit:".$cloudsl['credit']."</h1>";

		//echo $cloudsl['credit'];

		echo form_open('user/addcredit',array('class' => 'form-horizontal','id' => 'addcredit'));

?>

		<div class="form-group" >

			<label class="control-label col-lg-2">Add credit:</label>

			<div class="col-lg-6">

				<input type="text" class="form-control" name="credit" id="credit" />

			</div>

		</div>

		<div style="letter-spacing: 100px;padding-left: 100px;">

			<input type="button" class="btn btn-sm btn-primary virtual" value="Add" name='submit'/>

		</div>



<?php 

		/* echo 'your credit:';

		echo $cloudsl['credit'];

		echo form_open('user/addcredit');

		echo form_input('credit','');

		echo form_submit('submit','submit');*/

		echo form_close(); 

?>

<div style="display: none;">

<?php  echo form_open('https://www.vcs.co.za/vvonline/vcspay.aspx', array('id'=>'vcs_form'));

//echo form_open('virtual', array('id'=>'vcs_form'));

?>

	<!--VCS Terminal Id  test Terminal ID 9506 / live Terminal ID 6125-->

	<?php $terminalID = '9506'; ?>

	<input type="text" name="p1" value="<?php echo $terminalID; ?>">

	

	<!-- If you send a request to VCS and you do get a response then you cannot use that reference number again.  -->

	<!-- Reference Number ccyymmdd  must 15 chars -->

	<?php $reference = time().'12345';?>

	<input type="text" name="p2" value="<?php echo $reference;?>"> 

	

	<!-- Description of Goods  -->

	<?php $description = "addcredit";?>

	<input type="text" name="p3" value="<?php echo $description;?>">

	

	<!-- Amount -->

	<?php $amount = $total_cost_this_month;?>

	<input type="text" name="p4" value=""id='amount'>

	

	<input type="text" name="m_1" value=""id='reamount'>

	<input type="text" name="m_2" value="<?php echo $this->session->userdata('username');?>">

	<input type="text" name="m_6" value="login_user">

	<input type="text" name="m_8" value="cloudsl">

	

	<!-- hash code -->

	<?php $str = $terminalID.$reference.$amount.'Help';

		  $hash = md5($str);

	?>

	<input type="text" name="hash" value="<?php echo $hash?>">



<?php echo form_close();?>

</div>

<?php 

	}

else 

	if(isset($msg)&&$msg!=''){

		echo "<h3>".$msg."</h1>";

	}

	else{

		echo "please active your cloudsl!";

	}

?>

<script>

$('#credit').change(function(){

	$('#amount').attr('value',$("#credit").val());

	$('#reamount').attr('value',$("#credit").val());

});

$('.virtual').click(function(){

	$('#vcs_form').submit();

});

</script>