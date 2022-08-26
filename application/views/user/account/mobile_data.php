
<h3></h3>
<?php

/*
 * form_fields
 * base_url
 * fields_error_message
 *
 * repopulated_array
 * id_or_passport_error_message
 * proof_of_residence_error_message
 *
 * user_id
 * username
 *
 * user_fields
   residence
   passport
   residence_data
   passport_data
   base_url

   from_mobile_data_request
   first_name
   physical_delivery_address



 *
 */



// Error/Validation messages
if (!empty($error_message)) {
    echo "<div class='alert alert-danger'>$error_message</div>";
}

$validation_error =  validation_errors();
if (!empty($validation_error)) {
    echo "<div class='alert alert-danger'>" . $validation_error . "</div>";
}

if (!empty($fields_error_message)){

    echo "<div class='alert alert-danger'>" . $fields_error_message . "</div>";
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~a

$notice_for_file_upload  = "<p style='margin-top : 5px'>If you want to change the current document you need choose another file via upload form.</p>";

$hello_message = "Excellent [Name], we need a few details from you before we can send you your complimentary Mobile Data SIM card.";
$hello_message = str_replace('[Name]', $first_name, $hello_message);

$mobile_data_fields = array(
   'physical_delivery_address' => 'Physical Delivery Address',
   'city'                      => 'City',
   'postcode'                  => 'Postcode',

);

$mobile_data_values = array(

    'physical_delivery_address' => "",
    'city'                      => "",
    'postcode'                  => "",

);

foreach ($mobile_data_fields as $key => $value ){

    if ( isset($user_fields[$key]) && !empty($user_fields[$key]) ) {

        $mobile_data_values[$key] = $user_fields[$key];

    } elseif( isset($repopulated_array[$key]) && !empty($repopulated_array[$key]) ) {

        $mobile_data_values[$key] = $repopulated_array[$key];
    }
}


$upload_message = 'We also require two documents from you so that we can RICA the SIM Card before we send it to you.';

$mobile_upload_fields = array(

   'proof_of_residence' => 'Proof of Residence, not more than 3 months old',
   'id_or_passport'  => 'Copy of your ID or Passport',

);
$mobile_upload_value = array(

    'proof_of_residence' => '',
    'id_or_passport'     => '',

);
$mobile_upload_full_data = array();


if ($passport){
    $mobile_upload_value['id_or_passport'] = $base_url . 'user/get_mobile_document/passport';
    $mobile_upload_full_data['id_or_passport'] = $passport_data;
}

if ($residence){
    $mobile_upload_value['proof_of_residence'] = $base_url . 'user/get_mobile_document/residence';
    $mobile_upload_full_data['proof_of_residence'] = $residence_data;
}


/*
if (!empty($user_data['user_billing'])) {
    $user_billing = $user_data['user_billing'];
    $btn_label = 'Update Billing Information';
}else{
    $btn_label = 'Create Billing Information';
}
*/
?>

<input type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
<?php echo  form_open_multipart('user/mobile_data_docs', array('class' => 'form-horizontal','id' => 'mobile_data_form'));?>
<fieldset>
    <?php
    if (!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
    if (!empty($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    }
    if (!empty($info_message)) {
        echo "<div class='alert alert-info'>$info_message</div>";
    }
    ?>
    <div class="row">

            <div class="lead">
                <?php

                    if ($from_mobile_data_request)
                        echo $hello_message;

                ?>
            </div>
            <!-- <legend></legend> -->
            <?php
            echo form_hidden('user_id', $user_id);
            foreach ($mobile_data_fields as $field => $label) {

                ?>
                <div class="form-group">
                    <?php echo form_label($label, $field , array ('class'=> 'control-label col-lg-3')); ?>
                    <div class="col-lg-6">
                        <?php

                            echo form_input(array(
                                'class' => 'form-control',
                                'name' => $field,
                                'placeholder' => '',
                                'id' => $field . "_element",
                                'value' => $mobile_data_values[$field],
                                'maxlength' => '255',
                                //'readonly' => 'readonly',
                            ));

                        ?>
                    </div>
                </div>
            <?php
            }
            ?>
            <br/>
            <div class="lead">
                <?php


                if ($from_mobile_data_request)
                    echo $upload_message;

                ?>
            </div>
            <br/><br/>

            <?php
// ----------------------------------------------------------------------------------
            // upload section
            foreach ($mobile_upload_fields as $field => $label) {

                ?>
                <div class="form-group">
                    <?php echo form_label($label, $field , array ('class'=> 'control-label col-lg-3')); ?>
                    <div class="col-lg-6">
                        <?php
                        $file_upload_message = '';
                        if (!empty($mobile_upload_value[$field])) {

                            $preview_height = '150';
                            $preview_width = '150';
                            $current_img_width = $mobile_upload_full_data[$field]['width'];
                            $current_img_height = $mobile_upload_full_data[$field]['height'];

                            if ($current_img_width > $current_img_height){

                                $coef =   $current_img_height / $current_img_width;
                                $preview_height = round($preview_height * $coef);

                            } else {

                                $coef =   $current_img_width / $current_img_height ;
                                $preview_width = round($preview_width * $coef);
                            }



                            echo "<a href='" . $mobile_upload_value[$field] . "'><img   height='" . $preview_height . "' width='" . $preview_width . "' src='" .  $mobile_upload_value[$field]  .  "'></a>";

                            $file_upload_message  = $notice_for_file_upload;

                        }

                        echo $file_upload_message;
                        /// Form input for Upload process
                        echo form_input(array(
                            'class' => 'btn btn-default',
                            'name' => $field,
                            'placeholder' => '',
                            'id' => $field . "_element",
                            'type'  => 'file',
                            'style' => 'width : 100%; margin-top: 5px;',

                        ));


                        if (!empty($id_or_passport_error_message) && ($field == 'id_or_passport')){

                            echo "<br/><div class='alert alert-danger'>" . $id_or_passport_error_message . "</div>";

                        }

                        if (!empty($proof_of_residence_error_message) && ($field == 'proof_of_residence')){
                            echo "<br/><div class='alert alert-danger'>" . $proof_of_residence_error_message . "</div>";

                        }


                        ?>
                        <center><h6>image (gif, jpg, png)</h6></center>
                    </div>
                </div>
            <?php
            }
            ?>


	</div>


    <div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
        <?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Submit',)); ?>
        <?php echo form_reset(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Cancel',));?>
    </div>
</fieldset>
<?php echo form_close();?>