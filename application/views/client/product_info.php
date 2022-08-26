<script language="javascript" type="text/javascript">
$(document).ready(function() {


    // Additional button check
    $("input[name='billing_cycle']").click(function(){

        // get current parameter
        $checkedElement = $("input[name='billing_cycle']:checked").val();
        if ($checkedElement == 'Monthly'){

            $('#span-order-message').show();
        } else {

            $('#span-order-message').hide();
        }

    });


    // # Submit for non-payfast payment methods

	$('#billing_form').submit(function(event){

            event.preventDefault();
            var currentBillingForm = this;
          //  currentBillingForm.unbind('submit');

          // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $("#u_exist_error").hide();

            // get username checkbox
			var create_username = $('input:checkbox[name="create_username"]:checked').val();

            //             BILLING CYCLE  -- VALIDATION
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            var boolCheck = $('input:radio[name="billing_cycle"]').is(":checked");
            if(boolCheck == true){
                var val=$('input:radio[name="billing_cycle"]:checked').val();
              //  $('#choose_cycle').val(val); for VCS
                $('#cycle_error').hide();

            }else{
                $('#cycle_error').show();
                return false;
            }

            //                    PAYMENT METHOD VAL
            // ##############################################################3

            var val = $('input:radio[name="payment_method"]:checked').val();
            if(val == null) {
                $('.error').show();  // ERROR : udef payment method
                return false;
            }
            $('.error').hide();  /// HIDE ERROR

            //                  PAYMENT FORM VALIDATION
            // #################################################################
            // CREDIT CARD AUTO -- VALIDATE BLOCK +++++++++++++++++
            if(val == 'credit_card_auto'){
                var card_num = $('#card_num').val();
                var cvc = $('#cvc').val();

                if(card_num == ""){
                    $('#card_num_error').show();
                    return false;
                }
                if(cvc == ""){
                    $('#cvc_error').show();
                    return false;
                }

                // +++++++++++++++++++++++++++++++++++++++++++++++++++++
                // DEBIT ORDER  -- VALIDATE BLOCK ++++++++++++++++++++++
            }else if(val == 'debit_order'){
                var bank_name = $('#bank_name').val();
                var bank_account_number = $('#bank_account_number').val();
                var bank_branch_code = $('#bank_branch_code').val();

               if(bank_name == ""){
                    $('#bank_name_error').show();
                    return false;
                }
                if(bank_account_number == ""){
                    $('#bank_account_number_error').show();
                    return false;
                }
                if(bank_branch_code == ""){
                    $('#bank_branch_code_error').show();
                    return false;
                }
                // +++++++++++++++++++++++++++++++++++++++++++++++++++++
            }

            //                   if CREATE USERNAME is ACTIVE
            // ############################################################################

            if(create_username != null && create_username == 'create_client'){


                // get Username and Pass
                var acc_username = $('#acc_username').val();
                var acc_pwd = $('#acc_password').val();
                acc_username = $.trim(acc_username);
                acc_pwd = $.trim(acc_pwd);


                // if Username and Pass are not EMPTY
                if(acc_username != "" && acc_pwd != ""){
                    $('#u_p_error').hide();
                    // USERNAME AJAX VALIDATION
                    // #USERNAME!AJAX

                    var acc_realm = $('input#realm').val();

                    //   pre  -  check - ajax
                    var ajax_obj = send_ajax_check(acc_username, acc_realm);
                    var answer  = null;
                    ajax_obj.success(function(answer){

                       // console.log(answer);
                        if (answer == 'true'){

                            $("#u_exist_error").show();
                            return false;
                        } else {

                            currentBillingForm.submit();
                        }
                    });

                }else{
                    $('#u_p_error').show();   // Username and Pass are EMPTY -> show ERROR
                    return false;
                }
            } else {

                currentBillingForm.submit();
            }

            return false;
	});


    // RADIO click -> cahange payment forms
	$('.radio').click(function(){
		$('.error').hide();
		var payment_type = $('input:radio[name="payment_method"]:checked').val();
	    //alert(payment_type);

		if(payment_type == 'credit_card'){
			$('.virtual').show();
			$('.submit').hide();
			$('#credit_card_div').hide();
			$('#debit_order_div').hide();
		}else{
			if(payment_type == 'credit_card_auto'){
				$('#credit_card_div').show();
				$('#debit_order_div').hide();
			}else if(payment_type == 'debit_order'){
				$('#debit_order_div').show();
				$('#credit_card_div').hide();
			}else{
				$('#credit_card_div').hide();
				$('#debit_order_div').hide();
			}
			$('.submit').show();
			$('.virtual').hide();
		}
	});

    // # Payfast method click
	$('.virtual').click(function(){

        /*
         $("#payment_radio input.payment").attr('disabled','disabled');
         $(this).attr('disabled','disabled');

         var payment_type = $('input:radio[name="payment_method"]:checked').val();
         */

        // get amount
        var payfastAmount = $("input[name='amount']").val();

        if (payfastAmount == 0){
            alert('This payment method is not available for R0 amount');
            return;
        }


        $("#u_exist_error").hide();
        //  !! button disable !!
        $("input.payment").attr('disabled','disabled');
        $(this).attr('disabled','disabled');

        var payment_type = $('input:radio[name="payment_method"]:checked').val();

        // check billing cycle
		var boolCheck = $('input:radio[name="billing_cycle"]').is(":checked");
		//console.log(boolCheck);return false;


        //                    CHECK BILLING CYCLE
        // #######################################################
		if(boolCheck == true){
			$('#cycle_error').hide();
			var val =$('input:radio[name="billing_cycle"]:checked').val();
			$('#choose_cycle').val(val);

            //                    IF CREATE USERNAME
            // #######################################################
			var create_username = $('input:checkbox[name="create_username"]:checked').val();
			if(create_username != null && create_username == 'create_client'){

                // get username and password form
				var acc_username = $('#acc_username').val();
				var acc_pwd = $('#acc_password').val();
                var acc_realm = $('input#realm').val();

                acc_username = $.trim(acc_username);
                acc_pwd = $.trim(acc_pwd);

                if(acc_username != "" && acc_pwd != ""){
                    $('#u_p_error').hide();

                    // ############################################################################
                    //                      SEND 1st PRE-AJAX
                    // #############################################################################
                    var ajax_obj = send_ajax_check(acc_username, acc_realm);
                    var answerLocal  = null;
                    ajax_obj.success(function(answerLocal){

                            console.log(answerLocal);
                            if (answerLocal == 'true'){

                                $("#u_exist_error").show();
                                return false;
                            } else {


                                // #################################################################
                                //              SEND  2nd PRE-AJAX and SUBMIT
                                // #################################################################
                                var payfast_live_case = 'credit_card'; // payfast-live
                                if (payment_type == payfast_live_case){

                                    // SANDBOX - LIVE PARAM
                                    <?php
                                    // ############################################################
                                    //       PHP - SANDBOX/LIVE - PAYFAST SIGNATURE PARAMs
                                    // ############################################################
                                    if ($sandbox_access){
                                             echo "var ajax_result = send_ajax('SANDBOX');";
                                    } else {
                                             echo "var ajax_result = send_ajax();";
                                    }
                                    ?>
                                    ajax_result.success(function(answer){
                                        <?php
                                             // ################################################
                                             //     PHP - SANDBOX/LIVE - PAYFAST SUBMIT
                                              // ###############################################
                                             if ($sandbox_access){
                                                 echo "$('#payfast_sandbox').submit();";
                                             } else {
                                                 echo "$('#payfast_live').submit();";
                                             } ?>
                                    });
                                }
                                // ##################################################################
                            }
                     });
                    // ##############################################################################

				}else{
					$('#u_p_error').show();
				}
			}else{
				//







			}
		}else{
			$('#cycle_error').show();
		}

        $("input.payment").removeAttr( "disabled" );
        $(this).removeAttr('disabled');
        return false;
	});



    function  send_ajax_check(acc_username, acc_realm){

        var  ajax_url = '/client/check_local_username';
        return  $.ajax({
            type: "POST",
            url:  ajax_url,
            data: {
                acc_username : acc_username,
                acc_realm : acc_realm,

            },
        });

    }

    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // additional support functions

    function get_params(sandbox){

        var id = 'payfast_live';
        if (sandbox == 'SANDBOX'){
            id = 'payfast_sandbox';
        }

        // Sandbox
        var param_children = $("form#" + id).children('input');
        var param_length = param_children.length;
        var param_array = {};

        for (var i = 0; i < param_length; i++){

            param_array[$(param_children[i]).attr('name')] = param_children[i].value;
        }

        return param_array;

    }


    function get_ajax_url(url){

        var ajax_url = url.substring(0, url.length - 6 ); // 6 - for 'notify'
        return ajax_url  + "client_prevalid";;
    }

    function get_username(){

        var username = "<?php echo $username; ?>"
        return $.trim(username);
    }

    function get_order_signature(){

        var signature = "<?php if (isset($order_signature)) echo $order_signature; ?>";
        return signature;
    }

    function get_order_object(){
        var order_obj = {};

        /*
        order_obj['account_username']  = "<?php // echo $order_data_array['account_username']; ?>";
        order_obj['account_password']  = "<?php // echo $order_data_array['account_password']; ?>";
        order_obj['realm']             = "<?php //echo $order_data_array['realm']; ?>";
         order_obj['choose_cycle']      = "<?php // echo $order_data_array['choose_cycle']; ?>";
        */
        var acc_username = $('#acc_username').val();
        var acc_pwd = $('#acc_password').val();
        var acc_realm = $('input#realm').val();

        order_obj['account_username']  = acc_username;
        order_obj['account_password']  = acc_pwd;
        order_obj['realm']             = acc_realm;

        var cycle =$('input:radio[name="billing_cycle"]:checked').val();

        order_obj['choose_cycle']      = cycle;
        order_obj['product_id']        = "<?php echo $order_data_array['product_id']; ?>";
        order_obj['payment_type']      = "<?php echo $order_data_array['payment_type']; ?>";


        return order_obj;

    }

    function  send_ajax(sandbox){

        var pre_signature = $.trim("<?php echo $pre_live; ?>");
        if (sandbox == 'SANDBOX') {
            pre_signature = $.trim("<?php echo $pre_sandbox; ?>");
        }

        var params   =  get_params(sandbox);
        var ajax_url =  get_ajax_url(params['notify_url']);
        var username =  get_username();
        // var

        var order_signature = get_order_signature();
        var order_object = get_order_object();

        return  $.ajax({
            type: "POST",
            url:  ajax_url,
            data: {
                params : JSON.stringify(params),
                order_params : JSON.stringify(order_object),
                user   : username,
                pre_signature : pre_signature,
                order_signature : order_signature,

            }, /*
             success: function (answer){

             var  answerObj = $.parseJSON(answer);

             } */

        });

        // return function_answer;

    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~ || ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


    $('.choose_u_p').click(function(){
		var  c_u= $('input:checkbox[name="create_username"]:checked').val();
		if(c_u == 'create_client'){
			$('#auto_create').show();
		}else{
			$('#auto_create').hide();
		}
	});

    $('#auto_create').show();

	$('.biling_cycle').click(function(){
		$('#cycle_error').hide();

        var currentBillingCycle = $(this).attr('value').toLowerCase();

        $("div.check-method").hide();
        $("input.payment").prop('checked', false);

        $('.virtual').hide();
        $('.submit').hide();
        $('#credit_card_div').hide();
        $('#debit_order_div').hide();

        $("div." + currentBillingCycle ).show();

	});


    var firstBillingCycle = $("input[name='billing_cycle']").attr('value').toLowerCase();
    $("div.check-method").hide();
    $("div." + firstBillingCycle ).show();


});
</script>
<div id="page-content" class="container">
	<?php echo form_open('client/cofirm_product', array('class' => 'form-horizontal', 'id' => 'billing_form'));?> 
	<div class="row">
        <input type="hidden" id="realm" name="acc_realm" value="<?php echo $realm;?>"/>
		<input type="hidden" value="<?php echo $client_id;?>" name="user_id">
		<div class="col-lg-2"></div>
		<div class="col-lg-4">
			<fieldset>
	      	<legend align="left">Your Order Information</legend>
	      	<?php if(isset($product_data)){ ?>
	      	<?php  $price = $product_data['price']; ?>
	      		<input type="hidden" value="<?php echo $product_data['id'];?>" name="product_id">
	      		<div style="font-size: 16px;color: #428bca;">
	      			<strong><?php echo $product_data['name'];?></strong>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;color: #333; font-size: 20px;">
	      			<strong>Your Price for the rest of <?php echo date('M').' '.date('Y')?>:</strong> <label><?php echo 'R '.$pro_rata;?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Price:</strong> <label style="color: #428bca"><?php echo 'R '.$price;?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Package Speed:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['package_speed']);?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Service Level:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['service_level']);?></label>
	      		</div>
                <br/>
                <div>
                   <?php echo ADDITIONAL_ORDER_MESSAGE; ?>
                </div>
	      		<br>
	      		<br>
	      <?php }?> 
	      </fieldset>
		</div>
		<div class="col-lg-5">
			<fieldset>
			<!-- <div style="display: none;color: #f62b2b;" id="error_choose_u_p">Please choose a Create username method.</div> -->
			<div style="display: none;color: #f62b2b;" id="u_p_error">Please input username and password.</div>
			<div style="display: none;color: #f62b2b;" id="cycle_error">Please choose a Billing Cycle.</div>
			<div class="error" style="display: none;">Please choose a payment method.</div>
			<legend align="left">Payment Information</legend>	
			<div style="font-size: 16px;">
      			<strong>Billing Cycle:</strong>

      			<?php if(isset($billing_cycle) && !empty($billing_cycle)){
      				foreach ($billing_cycle as $k=>$v){
      					echo "	<label style='color: #428bca'><input type='radio' name='billing_cycle' value='" .$v['billing_cycle']. "' class='biling_cycle'>" .$v['billing_cycle']. "</label>";
      					echo '  ';
					}
      			}else{
					echo '	<label style="color: #428bca"><input type="radio" name="billing_cycle" value="Monthly" >Monthly</label>';
      			}?>

      		</div>
      		<br>
      		<div class="form-group">
				<label for="contact_number" class="control-label" style="padding-left: 15px;">Payment Type : </label><br/>
				<div id="payment_radio_list" class="col-lg-10" style="font-size: 18px;">
				<?php

              //  echo "<pre>";
              //  print_r($payment_methods);
              //  echo "</pre>";

                    $once_off_string = '';
                    $monthly_string = '';

					if(isset($payment_methods) && !empty($payment_methods)){

						foreach ($payment_methods as $k => $v){


							if( $k == 'credit_card_auto'){
								$msg = 'Auto Billing using your Credit Card';
							}elseif( $k  == 'credit_card'){
								$msg = 'Once off payment from your Credit Card';
							}elseif( $k  == 'debit_order'){
								$msg = 'Debit Order';
							}elseif( $k  == 'eft'){
								$msg = 'EFT';
							}
							//echo '<div class="radio"><label><input type="radio" name="payment_method" id="'.$v['payment_method'].'" value="'.$v['payment_method'].'" class="payment">'.$msg.'</label></div>';

                            // once_off_payment , monthly_payment

                            if ( $k  != 'credit_card'){
                                if (isset($v['once-off']))
                                    $once_off_string .= "<div class='radio once-off check-method'><label><input type='radio' name='payment_method' id='" . $k  . "' value='". $k  ."' class='payment'>".$msg."</label></div>";

                                if (isset($v['monthly']))
                                    $monthly_string .=  "<div class='radio monthly check-method'><label><input type='radio' name='payment_method' id='" . $k  . "' value='". $k  ."' class='payment'>".$msg."</label></div>";


                            }

                            if ( $k  == 'credit_card'){
                                // payfast-live
                                if (isset($v['once-off']))
                                     $once_off_string .= "<div class='radio once-off check-method'><label><input type='radio' name='payment_method' id='payfast-id' value='credit_card' class='payment'> ". $msg . "</label></div>";

                                if (isset($v['monthly']))
                                    $monthly_string .=  "<div class='radio monthly check-method'><label><input type='radio' name='payment_method' id='payfast-id' value='credit_card' class='payment'> ". $msg . "</label></div>";

                            }
                        }

                        echo $monthly_string;
                        echo $once_off_string;


					}
				?>
				</div>
			</div>
			<?php if ($product_data['automatic_creation']) { ?>
			<div>
				<strong></strong>
				<label style="color: #428bca;font-size: 16px;">
					<input type="checkbox" name="create_username" value="create_client" class="choose_u_p" checked="checked"   disabled="disabled"> Username and password created by client.</input><br/>
					<!-- <input type="radio" name="create_username" value="create_sysetm" class="choose_u_p">System create the username and password.</input> -->
				</label>
			</div>
			<br/>
			<div class="well" id="auto_create" style="display: none;">
				<div class="lead">Please choose a username and password for this account</div>
				<input type="hidden" id="realm" name="realm" value="<?php echo $realm;?>"/>
				<div class="form-group col-lg-12">
                     <label class="col-lg-3">Username</label>
                     <div class="col-lg-9">
                         <div class="input-group ">
                             <input type="text" name="acc_username" class="form-control" id="acc_username"/>
                             <span class="input-group-addon" style="font-size:15px;line-height:1.4;">@<?php echo $realm; ?></span>
                         </div>
                         <div style="display: none;color: #f62b2b;" id="u_exist_error"><b>This username already exist, please choose another one.</b></div>
                     </div>
                </div>
				<div class="form-group col-lg-12">
					<label class="col-lg-3">Password</label>
					<div class="col-lg-9">
						<input type="text" name="acc_password" class="form-control" id="acc_password"/>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="alert alert-info" id="system_create" style="display: none;">
				<input type="hidden" id="realm" name="realm" value="<?php echo $realm;?>"/>
				A username and password will be created for you, and you will receive
				these details via email.
			</div>
			<?php } else { ?>
				<div class="alert alert-info">
					<input type="hidden" id="realm" name="realm" value="<?php echo $realm;?>"/>
					A username and password will be created for you, and you will receive
					these details via email.
				</div>
			<?php } ?>
			</fieldset>
			
			<div id="credit_card_div" style="display: none;">
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Name on Card</label>			
					<div class="col-lg-7">
						<input type="text" class="form-control" id="name_on_card" name="name_on_card"  value="" >		
					</div>
				</div>
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Credit Card Number</label>			
					<div class="col-lg-7">
						<input type="text" class="form-control" id="card_num" name="card_num"  value="" >		
						<label generated="true" class="error" id="card_num_error" style="display: none;">This field is required.</label>
					</div>
				</div>
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Expiry Date</label>			
					<div class="col-lg-3">
						<select class="form-control" id="expires_month" name="expires_month" >
						<?php
						foreach (range('1', '12') as $month) {
							echo "<option value=$month>$month</option>";
						}
						?>
					</select>
					</div>
					<div class="col-lg-4">
						<select class="form-control" id="expires_year" name="expires_year">
						<?php
						foreach (range(date("Y"), date("Y") + 10) as $year) {
							echo "<option value=$year>$year</option>";
						}
						?>
						</select>
					</div>		
				</div>
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">CVC</label>			
					<div class="col-lg-7">
						<input type="text" class="form-control" id="cvc" name="cvc"  value="" >		
						<label generated="true" class="error" id="cvc_error" style="display: none;">This field is required.</label>
					</div>
				</div>
			</div>
			<div id="debit_order_div" style="display: none;">
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Bank Name</label>			
					<div class="col-lg-7">
						<input type="text" class="form-control"id="bank_name" name="bank_name"  value="" >		
						<label generated="true" class="error" id="bank_name_error" style="display: none;">This field is required.</label>
					</div>
				</div>
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Account Number</label>			
					<div class="col-lg-7">
						<input type="text" class="form-control" id="bank_account_number" name="bank_account_number"  value="" >		
						<label generated="true" class="error" id="bank_account_number_error" style="display: none;">This field is required.</label>
					</div>
				</div>
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Account Type</label>			
					<div class="col-lg-7">
						<select class="form-control" id="bank_account_type" name="bank_account_type">
						<?php 
						$type = array('Cheque/Current'=>'Cheque / Current','Savings'=>'Savings','Transmission'=>'Transmission');
						foreach ($type as $v){
							echo "<option value=$v>$v</option>";
						}	
						?>
						</select>	
					</div>
				</div>
				<div class="form-group">
					<label for="account_type" class="control-label col-lg-5">Branch Code</label>			
					<div class="col-lg-7">
						<input type="text" class="form-control" id="bank_branch_code" name="bank_branch_code"  value="" >		
						<label generated="true" class="error" id="bank_branch_code_error" style="display: none;">This field is required.</label>
					</div>
				</div>
			</div>
			<div style="float: right;">
				<?php echo '<a href="javascript:void(0);" class="btn btn-large btn-primary virtual" style="display:none;margin-right:5px;">Proceed to Payment</a>';?>
	      		<?php echo form_submit('confirm','I Accept & Confirm - Create Account','style="margin-right:5px;height:34px;" class="btn btn-large btn-primary submit"'); ?>
      		</div>
		</div>
	</div>
	<?php echo form_close();?>
</div>
<div style="display: none;">
<?php
/*
    echo form_open('https://www.vcs.co.za/vvonline/vcspay.aspx', array('id'=>'vcs_form'));
	<!--VCS Terminal Id  test Terminal ID 9506 / live Terminal ID 6125-->
	<?php $terminalID = '6125'; ?>
	<input type="text" name="p1" value="<?php echo $terminalID; ?>">
	
	<!-- If you send a request to VCS and you do get a response then you cannot use that reference number again.  -->
	<!-- Reference Number ccyymmdd  must 15 chars -->
	<?php $reference = time().'12345';?>
	<input type="text" name="p2" value="<?php echo $reference;?>"> 
	
	<!-- Description of Goods  -->
	<?php $description = $product_data['name'];?>
	<input type="text" name="p3" value="<?php echo $description;?>">
	
	<!-- Amount -->
	<?php $amount = $price?>
	<input type="text" name="p4" value="<?php echo $amount;?>">
	
	<input type="text" name="m_1" value="<?php echo $product_data['id'];?>">
	<input type="text" name="m_2" value="<?php echo $user;?>">
	<input type="text" name="m_3" value="<?php echo !empty($acc_username) ? $acc_username : null;;?>" id="m_acc_user">
	<input type="text" name="m_4" value="<?php echo !empty($acc_password) ? $acc_password : null;?>" id="m_acc_pwd">
	<input type="text" name="m_5" value="<?php echo !empty($realm) ? $realm : null;?>">
	<input type="text" name="m_6" value="client">
	<input type="text" name="m_7" value="" id="choose_cycle">
	
	<!-- hash code -->
	<?php $str = $terminalID.$reference.$description.$amount.'Help';
		  $hash = md5($str);
	?>
	<input type="text" name="hash" value="<?php echo $hash?>">

     echo form_close();
*/?>
</div>
<div style="display: none;">
<?php
    //
if ($sandbox_access){

    echo form_open("https://" . $sandbox_payfast_host . "/eng/process", array('id'=>'payfast_sandbox'));
    foreach ($sandbox_payfast_data as $key => $value){
        echo  "<input name='". $key . "' value='" . $value."' type='hidden' >";
    }
    echo form_close();
}
    // ===============================================================================
    // ===============================================================================
    echo form_open("https://" . $live_payfast_host . "/eng/process", array('id'=>'payfast_live'));
    foreach ($payfast_data as $key => $value){
        echo  "<input name='". $key . "' value='" . $value."' type='hidden' >";
    }
    echo form_close();



?>
</div>
