<script language="javascript" type="text/javascript">
$(document).ready(function() { 
	$("#create_account_form").validate({
	       rules: {
		      first_name: "required",
		      last_name: "required",
		      email_address: {
			      required : true,
			      email: true,
			      remote:{
					url : "<?php echo site_url('admin/validate_email')?>",
					type : 'post',
					data :{ 
						email_address : function(){return $("#email_address").val();},
						account_id : function(){return $("#account_id").val();}
					}	
				  },				 
			  },
			  user_mobile :{
            	  //required : true,
            	  number : true,
              },
		      username	:{
		    	  required : true,
		    	  minlength: 4,
		    	  remote:{
					url : "<?php echo site_url('admin/validate_username')?>",
					type : 'post',
					dataType: "json", 
					data :{ 
						username : function(){return $("#username").val();}
					}
				  }
	
			  },
			  password	   : {
		    	  required : true,
		    	  minlength: 4,
		    	  maxlength: 55,
			  },
		      re_password: {
		    	  required : true,
	              equalTo: "#password"
           	  },

              card_num :{
             	//creditcard : true,
            	  number : true,
              },
              /*expires_month : {
				number : true,
				max : 12,
				min : 1,
              },*/
              //cvv : "required",
             // bank_account_number : "required",
              email :"required",
              //mobile : "required",
              contact_number : "required",
              postal_code : "required",
              billing_name : "required", 
              address : "required", 
              city : "required", 
              province : "required", 
              postal_code : "required", 
              country : "required",
              email :  {
            	  required : true,
			      email: true,
              },
              contact_number : "required",
               br_a_id: {
                  number: true
               },
	   	},
	   	messages: {
	   		email_address:{
	   			remote: 'That email address is already registered on our system.  Please choose another.',	
		   	}
		}
    });
		
    });

    $('#first_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#last_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#address_1').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#email_address').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#username').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#password').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#re_password').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

    $('#billing_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#address').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#city').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#province').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#postal_code').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#country').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#email').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#mobile').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#contact_number').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

    if ($('select[name="2auth"]').val() == 'y') {
        $('select[name="2auth_type"]').parent().parent().show();
    } else {
        $('select[name="2auth_type"]').parent().parent().hide();
    }

    $(document).on('change', 'select[name="2auth"]', function (event) {
        if ($(this).val() == 'y') {
            $('select[name="2auth_type"]').parent().parent().show();
        } else {
            $('select[name="2auth_type"]').parent().parent().hide();
        }
    });
</script>

<?php
$user_fields = array (
	'ow' => 'OW Number',
	'first_name' => 'First Name',
	'last_name' => 'Last Name',
	'email_address' => 'Email Address',
	'mobile_number' => 'Mobile Number ',
	'role' => 'Account Role',
	'password' => 'Password',
    '2auth' => '2 Factor Authentication',
    '2auth_type' => 'Type of 2 Factor Authentication',
    'daily_lte_usage' => 'Send Daily LTE usage',
);
$billing_fields = array(
	'billing_name' => 'Billing Name',
	'address_1' => 'Address 1',
	'address_2' => 'Address 2',
	'city' => 'City',
	'province' => 'Province',
	'postal_code' => 'Postal Code',
	'country' => 'Country',
	'email' => 'Email',
	'contact_number' => 'Contact number'
);
$roles = array(
    'client' => 'Client',
    'admin' => 'Admin',
    'reseller' => 'Reseller',
    'super_admin' => 'Super Admin',
    'staff' => 'Staff'
);
/*
	If we're creating a new account, we need to fill all the user settings
		and billing settings with blank data!
*/

if ($this->uri->segment(2) == 'create_account') {
	$new_user = 1;
} else {
	$new_user = 0;
}
if ($new_user) {
	$btn_label = 'Create this User';
	$user_fields['username'] = 'Username';
	//$user_fields['password'] = 'Password';
	//$user_fields['re_password'] = 'Confirm Password';

	$user_settings = array();
	foreach ($user_fields as $f) {
		$user_data['user_settings'][$f] = '';
	}
} else {
	$btn_label = 'Update User Information';
}

if (isset($edit_user) && !empty($user_data['user_settings'])) {

	$user_settings = $user_data['user_settings'];

	if (!empty($user_data['user_billing'])) {
		$user_billing = $user_data['user_billing'];
	}else{
		$user_billing = '';
	}

//update_user
	echo form_open('admin/update_user', array('class' => 'form-horizontal','id' => 'create_account_form')); ?>
	
		<?php if (!empty($error_message)) {
			echo "<div class='alert alert-danger'>$error_message</div>";
		}?>	
		<div class='alert alert-danger' style="display: none;" id='notice'></div>
		<?php if(isset($account_id)){
			echo '<input type="hidden" value="'.$account_id.'" id="account_id" name="account_id"/>';
		}else{
			echo '<input type="hidden" value="" id="account_id" name="account_id"/>';
		}?>		
		<fieldset>
		<legend>User Account Information</legend>				
		<?php
			foreach ($user_fields as $f=>$l) { 
				if (isset($user_settings[$f])) {
					$v = $user_settings[$f];
				} else {
					$v = '';
				}
				if ($f == 'email_address' || $f == 'mobile') {
					$input_width = 'col-lg-6';
				} else if ($f == 'role') {
					$input_width = 'col-lg-3';
				} else if ($f == '2auth') {
                    $input_width = 'col-lg-2';
                } else if ($f == '2auth_type') {
                    $input_width = 'col-lg-3';
                } else if ($f == 'daily_lte_usage') {
                    $input_width = 'col-lg-2';
                } else {
					$input_width = 'col-lg-4';
				}
		?>
		<div class="form-group">
		<?php echo form_label($l, $f, array ('class'=> 'control-label col-lg-2')); ?>
			<div class="<?php echo $input_width ?>">
			<?php
				$input_class = 'form-control';
				if ($f == 'role') {
					if (trim($v) == '') {
						$d = 'client'; // default: client
					} else {
						$d = $v;
					}
					$class = 'class="form-control"';
					if($super_admin) {
					    $class = 'class="form-control"';
                    }

					echo form_dropdown('role', $roles, $d, $class);
				} elseif ($f == '2auth') {
                    if (trim($v) == '') {
                        $d = 'y'; // default: y
                    } else {
                        $d = $v;
                    }
                    $options = array(
                        'y' => 'Yes',
                        'n' => 'No'
                    );
                    echo form_dropdown('2auth', $options, $d, 'class="form-control"');
                } elseif ($f == '2auth_type') {
                    if (trim($v) == '') {
                        $d = 'sms'; // default: sms
                    } else {
                        $d = $v;
                    }
                    $options = array(
                        'sms' => 'SMS',
                        'email' => 'Email'
                    );
                    echo form_dropdown('2auth_type', $options, $d, 'class="form-control"');
                } elseif ($f == 'daily_lte_usage') {
                    if (trim($v) == '') {
                        $d = '1'; // default: 1
                    } else {
                        $d = $v;
                    }
                    $options = array(
                        '1' => 'Yes',
                        '0' => 'No'
                    );
                    echo form_dropdown('daily_lte_usage', $options, $d, 'class="form-control"');
                } else {
					if ($f == 'ow'){
						echo form_label($v, '', array(
							'class' => 'control-label col-lg-2',
						));
					}else{
						echo form_input(
							array(
								'class' => $input_class,
								'name' => $f,
								'placeholder' => '',
								'id' => $f,
								'value' => $v
							)
						);	
					}
				}
			?>
			</div>
		</div>		
		<?php
			}

     //   if (!empty($user_data['user_settings']['reason'])) {
            echo "<div class='form-group'>";
                echo form_label("Reason", 'reason_label', array ('class'=> 'control-label col-lg-2'));
                echo "<div class=' col-lg-4'>";

                echo form_textarea(
                    array(

                        'class'       => 'form-control',
                        'name'        => 'reason',
                        'placeholder' => '',
                        'id'          => 'reason_input_element',
                        'value'       => $user_data['user_settings']['reason'],
                        'rows'        => '3',
                        'cols'        => '4',
                     )
                );

                 echo "</div>";
            echo "</div>";
     //   }

		if(isset($user_settings['status']) && $user_settings['status'] == 'pending'){		
		?>
			<div class="well" style="margin-top:25px;text-align:center;">
				<strong>This account is currently pending, which means it can't login.</strong>
				<div style="margin-top: 10px;">
				<?php echo anchor("register/activate_account/$account_id", 'Activate Account', 'class="btn btn-default"') ?>
				</div>
			</div>
		<?php
		}			
		?>
	<?php 
		$cur_page = $this->uri->rsegment(2);
		if($cur_page == "edit_account"){
	?>
	<div class="col-lg-6">
		<legend>Billing User Information</legend>

        <?php
          //  var_dump($user_billing);
        ?>
        <div class="form-group">
            <?php echo form_label("SA ID number", 'sa_id_number_label', array ('class'=> 'control-label col-lg-4')); ?>
            <div class="col-lg-6">

                <?php echo form_input(
                    array(
                        'class' => 'form-control',
                        'name' => 'sa_id_number',
                        'placeholder' => '',
                        'id' => 'sa_id_number_element',
                        'value' => $user_billing['sa_id_number'],
                    )
                ); ?>
            </div>
        </div>

		<?php
			foreach ($billing_fields as $f=>$l) {
				if (isset($user_billing[$f])) {
					$v = $user_billing[$f];
				} else {
					$v = 'Please fill this field.';
					$user_billing[$f] = '';
				}
		?>
		<div class="form-group">
			<?php echo form_label($l, $f, array ('class'=> 'control-label col-lg-4')); ?>
			<div class="col-lg-6">
				<?php echo form_input(
					array(
						'class' => 'form-control',
						'name' => $f,
						'placeholder' => '',
						'id' => $f,
						'value' => $v
						
					)
				); ?>
			</div>
		</div>		
		<?php }
           // if (!empty($user_billing['adsl_number'])){ ?>

     

        <?php // check   } ?>
	</div>
<br/>
<legend>BA Information</legend>
    <div class="form-group">
        <label for="avios_id" class="control-label col-lg-5">British Airways Executive Club Membership Number:</label>
        <div class="col-lg-5">
            <input type="text" class="form-control"id="br_a_id" name="br_a_id"  value="<?php echo !empty($user_data['user_settings']['br_a_id']) ? $user_data['user_settings']['br_a_id'] : null;?>" >
        </div>
    </div>
<hr>

<?php }?>
	<?php if (!$new_user) { ?>
	<input type="hidden" name="username" value="<?php echo $edit_user; ?>" />
	<?php } ?>
	<div style="text-align:center;letter-spacing: 150px;">
	<?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => $btn_label,)); ?>
	<?php echo form_reset(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Cancel',));?>	
	</div>
</fieldset>
<?php
	 echo(form_close()); 
} else {

}
?>