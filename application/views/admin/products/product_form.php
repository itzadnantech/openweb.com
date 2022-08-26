<script language="javascript" type="text/javascript">
$(document).ready(function() {

    $("#create_product_form").validate({
		rules:{
			name : {
			 required : true,
			 remote:{
					url : "<?php echo site_url('admin/validate_product_name2')?>",
					type : 'post',
					data :{ 
						product_name : function(){return $("#name").val();},
						product_id : function(){return $("#product_id").val();}
					}	
				  },
			},
			/*price :{
				required : true,
			},*/
			package_speed : {
				required : true,
			},
			service_level : {
				required : true,
			},
			recommended_use : {
				required : true,
			},
			global_backbone : {
				required : true, 
			},
			billing_occurs_on : {
				required : true, 
			},

		}
    });

    $('#create_product_form').submit(function(){

    	var discount = $('#price').val();
		var reg = new RegExp('^[0-9]*$');

        var toUpOption = $("#topup_active_element").val();
        var topUpList  = $("#topup_class_element_1").val();

        $('#topup_class_empty').hide();
        if (toUpOption == '1' && ((topUpList  == '') || (topUpList==null)) ){

            $('#topup_class_empty').show();
            return false;
        }


		if(discount && reg.test(discount)){
			$('#class_notice').hide();
			$('#price_notice').hide();
			return true;			
		}else{
			if(discount == ''){
				$('#price_notice').html('This field is required.');
				$('#price_notice').show();
			}else if(!reg.test(discount)){
				$('#price_notice').html('Please enter a valid number.');
				$('#price_notice').show();
			}
			

			return false;
		}
    });

    $('#type').change(function() {

        if($('#type').val() == 'reseller') {
            $('#parent').hide();
            $('#parent_r').show();
        }

        if($('#type').val() == 'client') {
            $('#parent').show();
            $('#parent_r').hide();
        }
    });

    if($('#type').val() == 'reseller') {
        $('#parent').hide();
        $('#parent_r').show();
    }

    if($('#type').val() == 'client') {
        $('#parent').show();
        $('#parent_r').hide();
    }

    $('#price').blur(function(){    	
		var discount = $('#price').val();
		var reg = new RegExp('^[0-9]*$');		

		if(reg.test(discount)){
			$('#price_notice').hide();
		}else{
			$('#price_notice').html('Please enter a valid number.');
			$('#price_notice').show();
			$('#price').focus();
		}
    });


    $('#name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#parent').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#class_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#package_speed').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#service_level').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#recommended_use').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#global_backbone').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#price').parent().parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#billing_occurs_on').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    //$('.form-group:eq(4)').css('display','none');
    //$('input:checkbox[name="billing_cycle[daily]"]').css('display','none');
    $('#billing_day').css('display','none');
});
	
</script>
<?php

// ----------------------------------------------------
$p_parents = array(); // 'none' => 'None' --> default
$mobile_traffic_options = array(

    'MB' => 'MB',
    'GB' => 'GB',
);

$mobile_data_enabled = $product_data['product_settings']['mobile_data_enabled'];
$mobile_data_amount  = $product_data['product_settings']['mobile_data_amount'];
$mobile_data_type    = $product_data['product_settings']['mobile_data_type'];



$mobile_data_checked = "";
if($mobile_data_enabled)
    $mobile_data_checked = "checked='checked'";


//list all categories-sub_categories
// ^ just a nicely formatted version of $potential_parents
if (!empty($potential_parents)) {
	foreach($potential_parents as $p) {
		$p_parents[$p['id']] = $p['name']; 
	}
}

if (!empty($categories_res)) {
    foreach($categories_res as $p) {
        $p_parents_res[$p['id']] = $p['name'];
    }
}

$local_topup_options['active'] = '0';
$local_topup_options['topup_id'] = '';
$local_topup_options['topup_id2'] = '';
$local_topup_options['topup_id3'] = '';

if (!empty($topup_options) ){

    if (!empty($topup_options['topup_id']))  // Can be NULL
         $local_topup_options['topup_id'] = $topup_options['topup_id'];

    if (!empty($topup_options['topup_id2']))  // Can be NULL
        $local_topup_options['topup_id2'] = $topup_options['topup_id2'];

    if (!empty($topup_options['topup_id3']))  // Can be NULL
        $local_topup_options['topup_id3'] = $topup_options['topup_id3'];


    $local_topup_options['active'] = $topup_options['topup_active'];

}


if ($this->uri->segment(2) == 'create_product') {
	$new_product = 1;
	$product_id = '';
	$default_comment_default = '[Name_Surname] (Client) ([amount] - [product_name]) (DEBIT ORDER)';
} elseif ($this->uri->segment(4) == 1) {
    $new_product = 1;
    $product_id = '';
}else {
	if (!empty($product_data['product_settings'])) {
		$product_id = $product_data['product_settings']['id'];
	}
	$new_product = 0;
	
	$default_comment_default = '';
}
if ($new_product) {
	$btn_label = 'Create this Product';
	
	$product_settings = array();
	foreach ($product_fields as $f) {
		$product_data['product_settings'][$f] = '';
	}
} else {
	$btn_label = 'Update Product Information';
}

$classes_list = array();
if (!empty($is_classes)){
	foreach ($is_classes as $i) {
		$classes_list[$i['table_id']] = "({$i['realm']}) - {$i['desc']}";
	}
}

$input_size = array (
	'package_speed' => 'col-lg-5',
	'recommended_use' => 'col-lg-5',
	'service_level' => 'col-lg-5',
	'global_backbone' => 'col-lg-5',
	'billing_occurs_on' => 'col-lg-5',
	'price' => 'col-lg-2',
);

$creation_modes = array (
	'0' => 'Manual',
	'1' => 'Auto-Create',
);

$month_checked = "";
$once_checked = "";
$day_checked = "";
if(!empty($billing_cycle)){
	foreach ($billing_cycle as $k => $v){
		if($v['billing_cycle'] == 'Monthly'){
			$month_checked = "checked";
		}elseif($v['billing_cycle'] == 'Once-Off'){
			$once_checked = "checked";
		}elseif($v['billing_cycle'] == 'Daily'){
			$day_checked = "checked";
		}
	}
}

if (isset($edit_product) && !empty($product_data['product_settings'])) {
	$product_settings = $product_data['product_settings'];
?>
<?php 
	echo form_open('admin/update_product', array('class' => 'form-horizontal','id' => 'create_product_form'));
	echo form_hidden('id', $product_id); 

	if(isset($product_id) && !empty($product_id)){
		$product_rand_num = $product_settings['random_num'];
		echo '<input type="hidden" value="'.$product_id.'" id="product_id" name="product_id"/>';			
?>
		<div class="form-group">
			<label class="control-label col-lg-3" for="name">Signup Link</label>
			<div class="col-lg-6">
				<label class="col-lg-9" for="name" style="padding-top: 5px;"><?php echo base_url().'client/order_product/'.$product_rand_num; ?></label>
			</div>
		</div>
<?php 
	}else{
		echo '<input type="hidden" value="" id="product_id" name="product_id"/>';
	}
?>
		<fieldset>
		<?php

        if  (!$new_product){

            $recived_class = $product_settings['class'];
            $product_settings['class'] = $product_settings['class_id'];

        }

		foreach ($product_fields as $f=>$l) {
			// Defaults:
			$id = $f;
			$input_class = 'col-lg-6';
			$group_class = 'control-group';
			$prepended = '';
			$textarea = FALSE;
			$small = '';
			$appended = '';

			if (isset($product_settings[$f])) {
				$v = $product_settings[$f];
			} else {
				if ($f == 'default_comment') {
					// I want to set a default here:
					$v = $default_comment_default;
				} else {
					$v = '';
				}
			}
		?>
		<?php
			if (isset($input_size[$f])) {
                $input_class = $input_size[$f];
            }
			if ($f == 'desc') {
				$small = '<br/><small>Optional</small>';
				$textarea = TRUE;
			}
			if ($f == 'class') {
				$small = '';
			}
			if ($f == 'price')  {
				$group_class = 'control-group input-prepend';
				$prepended = '<div class="input-group">
					<span class="input-group-addon">R</span>';
				$appended = '</div><div style="display:none;color:#F62B2B;width:500px;font-weight: bold;" id="price_notice"></div>';
			} else if ($f == 'discount_codes') {
				$textarea = TRUE;
				$small = '<br/><small>Comma Separated</small>';
			} else if ($f == 'billing_cycle') {
				$textarea = TRUE;
				//$small = '<br/><small>Comma Separated</small>';
			}
		?>
		<div class="form-group">
			<?php echo form_label($l . $small, $f, array ('class'=> 'control-label col-lg-3')); 
			?>
			<div class="<?php echo $input_class; ?>">
			<?php
			if ($f == 'automatic_creation') {
				if ($v == '') {
					$d = '0';
				} else {
					$d = $v;
				}
				echo form_dropdown('automatic_creation', $creation_modes, $d, 'class="form-control"');
			} else if ($f == 'parent') {
				if (trim($v) == '') {
					$d = 'none'; // default: none
				} else {
					$d = $v;
				}
				echo form_dropdown('parent', $p_parents, $d, 'class="form-control" id="parent"');
				echo form_dropdown('parent_r', $p_parents_res, $d, 'class="form-control" id="parent_r"');
			} else if ($f == 'active') {
				if (trim($v) == '') {
					$d = 'inactive'; // default: Client
				} else {
					$d = $v;
				}
				$states = array(
                  '1'  => 'Visible',
                  '0'    => 'Hidden',
                );
                echo form_dropdown('active', $states, $d, 'class="form-control"');
			} else if ($f == 'pro_rata_option') {
				if (trim($v) == '') {
					$d = 'percent'; // default: monthly
				} else {
					$d = $v;
				}
				if (!empty($pro_rata_options)) {
					echo form_dropdown('pro_rata_option', $pro_rata_options, $d, 'class="form-control" ');
				}
				
			} else if ($f == 'type') {
                echo form_dropdown('type', ['client' => 'Client', 'reseller' => 'Reseller'], $type, 'class="form-control" id="type"');

			} else if ($f == 'trial') {
				
				echo form_radio('trial','1').form_label('YES').'&nbsp;&nbsp;';
				//echo '&nbsp;';
				echo form_radio('trial','0').form_label('NO');
			
			} else if ($f == 'billing_cycle') {
				
				echo "<div id='billing_month'>".form_checkbox("billing_cycle[monthly]", "monthly", $month_checked). form_label('Monthly')."</div>";
				echo "  ";
				echo "<div id='billing_once'>".form_checkbox("billing_cycle[once]", "once", $once_checked). form_label('Once-Off')."</div>";
				echo "  ";
				
				echo "<div id='billing_day'>".form_checkbox("billing_cycle[daily]", "daily", $day_checked). form_label('Daily')."</div>";
				
				/* if (trim($v) == '') {
					$d = 'monthly'; // default: monthly
				} else {
					$d = $v;
				}
				echo form_dropdown('billing_cycle', $billing_cycles, $d, 'class="form-control"'); */
			} else if ($f == 'class') { 
				if (trim($v) == '') {
					$d = 'none'; // default: none
				} else {
					$d = $v;
				}
				echo form_multiselect('class[]', $classes_list, $d, 'class="form-control" style="height:200px" id="class_name"');
				echo '<div style="display:none;color:#F62B2B;width:500px;font-weight: bold;" id="class_notice"></div>';

                if (empty($product_settings['class_id']) && !$new_product) {

                    echo "<p style='margin-top : 20px; margin-left : 20px;'> Class for this product : ".  $recived_class . "</p>";
                }

			} else {
				echo $prepended;
		
				if ($textarea) {
					echo form_textarea(
						array(
							'class' => "form-control",
							'name' => $f,
							'placeholder' => '',
							'id' => $id,
							'value' => $v,
							'rows' => 5,
						)
					); 
				} else {
					echo form_input(
						array(
							'class' => "form-control $input_class",
							'name' => $f,
							'placeholder' => '',
							'id' => $id,
							'value' => $v
						)
					); 
				}
				echo $appended;
			}
			?>
			</div>
		</div>
	<?php }	 ?>
	<?php 




    $credit_card_comment = '';
    $credit_card_auto_comment = '';
    $debit_order_comment = '';

    if (isset($product_additional_comments)){

        if (isset($product_additional_comments['credit_card']))
            $credit_card_comment = $product_additional_comments['credit_card'];

        if (isset($product_additional_comments['credit_card_auto']))
            $credit_card_auto_comment= $product_additional_comments['credit_card_auto'];

        if (isset($product_additional_comments['debit_order']))
            $debit_order_comment = $product_additional_comments['debit_order'];

    }

	?>


     <hr/>

     <!--  TopUps -->

    <div class="form-group">
     <?php
        // array(2) { ["topup_id"]=> NULL ["topup_active"]=> string(1) "0" }


        $topup_option_array = array( '0' => 'No', '1' => 'Yes');
        echo form_label("TopUp active", "topup_option", array ('class'=> 'control-label col-lg-3'));
        echo "<div class='col-lg-6'>";
             echo form_dropdown('topup_option', $topup_option_array, $local_topup_options['active'], "class = 'form-control col-lg-6'  id='topup_active_element' autocomplete='off' ");
        echo "</div>";
     ?>
    </div>


            <div class="form-group">
                <label for="caldera_link" class="control-label col-lg-3">Caldera Form Link</label>
                <div class="col-lg-6">
                    <input type="text" name="caldera_link" value="<?php echo $product_data["product_settings"]['form_link']; ?>" class="form-control col-lg-6" placeholder="" id="caldera_link">
                </div>
            </div>
    </div>
    <br/>

	<?php if (!$new_product) { ?>
		<input type="hidden" name="username" value="<?php echo $edit_product; ?>" />
	<?php } ?>

		<div style="text-align:center; margin-top:20px;">
	<?php echo form_submit(array ('class' => 'btn btn-lg btn-primary', 'value' => $btn_label,)); ?>
		</div>
	</fieldset>
	<?php echo form_close(); ?>
	<?php
} else {

}
?>
