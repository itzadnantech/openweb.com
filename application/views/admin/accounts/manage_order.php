<style>
.error{
	color:#f62b2b;
	display:none;
}
</style>
<script language="javascript" type="text/javascript">
$(function(){
    $( "#date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd',
    });

    $('#manage_order_form').submit(function (){

    	var account_username = $('#account_username').val();
    	var account_password = $('#account_password').val();

        var fibre_data_exist =  check_fibre_data_for_validation();

        if (fibre_data_exist)
            return true;


        // validation
    	if(account_password != '' && account_username != ''){
    		$('#acc_name').hide();
    		$('#acc_pwd').hide();
			return true;
        }else{
        	if(account_password == ''){
    			$('#acc_pwd').show();
            }else{
            	$('#acc_pwd').hide();
            }
            
            if(account_username == ''){
    			$('#acc_name').show();
            }else{
            	$('#acc_name').hide();
            }
        	return false;
        }


    });

    $('.active_order').click(function(){
    	var account_username = $('#account_username').val();
    	var account_password = $('#account_password').val();

        var fibre_data_exist = check_fibre_data_for_validation();
        if (fibre_data_exist)
            return true;

    	if(account_password != '' && account_username != ''){
			$('#acc_name').hide();
    		$('#acc_pwd').hide();
			return true;
		}else{
			if(account_password == ''){
    			$('#acc_pwd').show();
            }else{
            	$('#acc_pwd').hide();
            }
            
            if(account_username == ''){
    			$('#acc_name').show();
            }else{
            	$('#acc_name').hide();
            }
			return false;
		}


    });


    function check_fibre_data_for_validation(){

        var fibre_id = $("input#fibre_id_element").val();
        var response = true; // fibre doesn't exist

        if (fibre_id == null)
            response = false; // fibre exist

        return response;
    }


});
</script>
<h3>Manage Order</h3>
<?php if (isset($messages['success_message']) && trim($messages['success_message']) != '') { ?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
<?php } ?>

<?php


if ($order_id && trim($order_id) != '') {
	echo form_open('admin/update_order', array('class' => 'form-horizontal','id' => 'manage_order_form'));
	?>
	<input type="hidden" name="id" value="<?php echo $order_id ?>" />
	<?php

        // Fibre order section
        if (isset($order_data['fibre']) && !empty($order_data['fibre'])){
?>

            <input  type="hidden" placeholder="" name="fibre_id" class="form-control"
                    value="<?php echo $order_data['fibre']['id']; ?>"
                    id="fibre_id_element">

            <br/>
            <!-- Fibre product name -->
            <div class="form-group">
                <label class="control-label col-lg-3"><?php echo "Fibre Product Name"; ?> </label>
                <div class="col-lg-7">
                    <input  type="text" placeholder="" name="product_name_fd" class="form-control"
                            value="<?php echo $order_data['fibre']['product_name']; ?>"
                            id="product_name_fd_element">
                </div>
            </div>
    <?php

            if ($order_data['fibre']['fibre_type'] == 'data'){
    ?>
                <!-- Fibre user name -->
                <div class="form-group">
                    <label class="control-label col-lg-3"><?php echo "Fibre Username"; ?> </label>
                    <div class="col-lg-7">
                        <input  type="text" placeholder="" name="username_fd" class="form-control"
                                value="<?php echo $order_data['fibre']['fibre_data_username']; ?>"
                                id="username_fd_element">
                    </div>
                </div>

                <!-- Fibre password -->
                <div class="form-group">
                    <label class="control-label col-lg-3"><?php echo "Fibre Password"; ?> </label>
                    <div class="col-lg-7">
                        <input  type="text" placeholder="" name="password_fd" class="form-control"
                                value="<?php echo $order_data['fibre']['fibre_data_password']; ?>"
                                id="password_fd_element">
                    </div>
                </div>

                <!-- Fibre provider -->
                <div class="form-group">
                    <label class="control-label col-lg-3"><?php echo "Fibre Provider"; ?> </label>
                    <div class="col-lg-7">
                        <input  type="text" placeholder="" name="provider_fd" class="form-control"
                                value="<?php echo $order_data['fibre']['fibre_data_provider']; ?>"
                                id="provider_fd_element">
                    </div>
                </div>

            <?php
            }

            if ($order_data['fibre']['fibre_type'] == 'line'){
    ?>
                <!-- Fibre line -->
                <div class="form-group">
                    <label class="control-label col-lg-3"><?php echo "Fibre Line"; ?> </label>
                    <div class="col-lg-7">
                        <input  type="text" placeholder="" name="number_fl" class="form-control"
                                value="<?php echo $order_data['fibre']['fibre_line_number']; ?>"
                                id="number_fl_element">
                    </div>
                </div>
    <?php
            }
            echo "<hr/>";
        }
    // ---------------------------------------------------
    $order_data['realm'] = $order_realm;
	foreach ($order_data as $i=>$o) {
		if ($o === null) {
			$o = '';
		}
		if ($i == 'price' || $i == 'pro_rata_extra') {
			$pre = '<div class="input-group"><span class="input-group-addon">R</span>';
			$post = '</div>';
		} else {
			$pre = '';
			$post = '';
		}
		if (isset( $order_key[$i])) {
			?>
			<div class="form-group">
			<label class="control-label col-lg-3">
			<?php echo $order_key[$i]; ?>
			</label>
			<div class="col-lg-7">
			<?php if ($i == 'product') {
				if (!empty($product_list)) {
					$products = array();
					foreach ($product_list as $p=>$l) {
						$products[$l['id']] = $l['name'];
					}
                    if (empty($o)){
                        $products[''] = '';
                        $o = '';
                    }


					echo form_dropdown('product', $products, $o, 'class="form-control"');
				}
			}  elseif ($i == 'realm'){
			    //If lte order needs text field
                if($order_data['service_type'] == 'lte-a') {
                    echo form_input([
                        'type'  => 'text',
                        'name'  => 'realm',
                        'class' => 'form-control',
                        'value' => $order_data['realm']
                    ]);
                } else {
                    //else dropdown
                    if (!empty($realm_list)) {
                        $realms = array();
                        foreach ($realm_list as $p => $l) {
                            $realms[$l['realm']] = $l['realm'];
                        }
                        if (empty($o)) {
                            $realms[''] = '';
                        }
                        echo form_dropdown('realm', $realms, $o, 'class="form-control"');
                    }
                }
            } elseif ($i == 'realm'){

                if (!empty($realm_list)) {
                    $realms = array();
                    foreach ($realm_list as $p=>$l) {
                        $realms[$l['realm']] = $l['realm'];
                    }
                    if (empty($o)){
                        $realms[''] = '';
                    }
                    echo form_dropdown('realm', $realms, $o, 'class="form-control"');
                }


            }   else if ($i == 'status') {
			 $statuses = array ('active','pending', 'deleted', 'suspended', 'expired', 'pending cancellation');
			 $status_list = array();
			 foreach ($statuses as $s) {
				 $status_list[$s] = ucfirst($s);
			 }
             if (empty($o))
                 $status_list[''] = '';
			 echo form_dropdown('status', $status_list, $o, 'class="form-control"');
			 ?>
			 <div class="help-block">This will superficially change the status.To cancel an order, click the button above; To activate an order click the button below.</div>
			 <?php
			}elseif ($i == 'account_username'){
				echo '<input type="text" name="account_username" id="account_username" value="'.$o.'" class="form-control"/>';
				echo '<label class="error" id="acc_name">This field is required.</label>';
			}elseif ($i == 'account_password'){
				echo '<input type="text" name="account_password" id="account_password" value="'.$o.'" class="form-control"/>';
				echo '<label class="error" id="acc_pwd">This field is required.</label>';
			}elseif ($i == 'change_flag'){
				echo '<input type="checkbox" name="change_flag" id="change_flag" value="1" ';
				if($o == 1){
					echo 'checked="checked"';
				}
				echo '> <b>Allow user to change his password .</b>';
			}elseif ($i == 'display_usage'){
				echo '<input type="checkbox" name="display_usage" id="display_usage" value="1" ';
				if($o == 1){
					echo 'checked="checked"';
				}
				echo '> <b>Display the usage stats in the user panel.</b>';
			}elseif ($i == 'cancel_flage'){
				echo '<input type="checkbox" name="cancel_flage" id="cancel_flage" value="1" ';
				if($o == 1){
					echo 'checked="checked"';
				}
				echo '> <b>Client can auto cancel a product.</b>';
			}elseif ($i == 'payment_method'){
				if($o == 'debit_order'){
					echo "<label>Debit Order</label>";
				}elseif($o == 'eft'){
 					echo "<label>EFT</label>";
				}elseif ($o == 'credit_card'){
					echo "<label>Credit Card</label>";
				}elseif ($o == 'credit_card_auto'){
					echo "<label>Auto Billing using your Credit Card</label>";			
				}
			}else {
				echo $pre;
				$placeholder = '';
				$help_text = '';				
				?>
				<input type="text" placeholder="<?php echo $placeholder ?>" name="<?php echo $i ?>" class="form-control" value="<?php echo $o ?>" id="<?php echo $i?>">
				<?php echo $help_text; echo $post;				
			} ?>
			</div>
			</div>
			<?php
		}
	}

    // check fibre data
    /*
            [service_type] => fibre-line
            [fibre] => Array
                (
                    [id] => 12
                    [user_id] => 211
                    [username] => test-vv2
                    [order_id] => 12716
                    [product_name] => name3234
                    [fibre_data_username] =>
                    [fibre_data_password] =>
                    [fibre_data_provider] =>
                    [fibre_line_number] => 434543545
                    [fibre_type] => line
                )
     */

  //  if (isset($order_data['service_type']))
  //      if ( ($order_data['service_type'] == 'fibre-data') || ($order_data['service_type'] == 'fibre-line') )
   //         echo '!';




	?>
	<div style="text-align:center">
		<input type="submit" class="btn btn-large btn-primary" value="Update Order"/>
	</div>
	<?php echo form_close();?>
	<?php if ($order_data['status'] == 'pending') { ?>
		<div class="well" style="margin-top:25px;text-align:center;">
			<strong>This order is currently pending, which means it hasn't applied the addRealmAccount function yet.</strong>
			<div style="margin-top: 10px;">
			<?php echo anchor("admin/activate_order/$order_id", 'Activate and Add Realm Account', 'class="btn btn-default active_order"') ?>
			</div>
		</div>
	<?php } ?>
	<h3 style="margin-bottom:15px;">Other Options</h3>	
<?php } else { ?>
	<div class="alert alert-warning">
		<strong>Order not found.</strong> It seems that there is no order with that ID!
	</div>
<?php } ?>