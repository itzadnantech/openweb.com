<?php
if($cloudsl)
	{
		//var_dump($cloudsl);
		echo "<h3>Credit:".$cloudsl['credit']."</h3>";
		echo "<h3>Account Number:".$cloudsl['account_num']."</h3>";
	}
else{
	echo form_open('user/activecloudsl');
	echo 'active:'.form_radio('active','yes').'yes';
	echo form_radio('active','no').'no';
	echo form_submit('submit','submit');
	echo form_close();
}