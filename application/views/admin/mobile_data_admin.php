<script language="javascript" type="text/javascript">
/*
    $(document).ready(function() {
        $("#mobile_data_form").validate({
            rules: {
                physical_delivery_address : "required",
                city : "required",
                postcode : "required",

                proof_of_residence: "required",
                id_or_passport : "required",

            }
        });

        $('#billing_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#address_1').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#city').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#province').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#postal_code').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#country').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#email').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#mobile').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#contact_number').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#sa_id_number').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    });
*/

</script>
<h3></h3>

<?php

    $back_url = $base_url . "admin/edit_account/" . $user_id;
?>
<a href="<?php echo $back_url; ?>" class="btn btn-default">Back to profile</a>
<?php

$mobile_data_fields = array(
    'physical_delivery_address' => 'Physical Delivery Address',
    'city'                      => 'City',
    'postcode'                  => 'Postcode',

);

$mobile_data_values = array(

    'physical_delivery_address' => $fields['delivery_address'],
    'city'                      => $fields['city'],
    'postcode'                  => $fields['postcode'],

);

$mobile_upload_fields = array(

    'proof_of_residence' => 'Proof of Residence, not more than 3 months old',
    'id_or_passport'  => 'Copy of your ID or Passport',

);
$mobile_upload_value = array(

    'proof_of_residence' => $residence,
    'id_or_passport'     => $passport,

);


$base_img_size = '500';

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
<?php echo  form_open_multipart('user/mobile_data_request', array('class' => 'form-horizontal','id' => 'mobile_data_form'));?>
<fieldset>
    <?php
    if (!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
    if (!empty($succ_message)) {
        echo "<div class='alert alert-success'>$succ_message</div>";
    }
    if (!empty($info_message)) {
        echo "<div class='alert alert-info'>$info_message</div>";
    }
    ?>
    <div class="row">

        <div class="lead">
            <?php echo $hello_message; ?>
        </div>
        <!-- <legend></legend> -->
        <?php
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
                        'readonly' => 'readonly',
                    ));

                    ?>
                </div>
            </div>
        <?php
        }
        ?>
        <br/>
        <!-- ----------------------------------------------------------->


        <?php



            /*

             ["file_name"]=> string(36) "be2fe2ad7fedd7443ee9fdd9f12db7a8.jpg"
             ["path"]=> string(89) "application/PDFdocs/9c627bbc12f480628df193413a0660f4/be2fe2ad7fedd7443ee9fdd9f12db7a8.jpg"
             ["create_date"]=> string(19) "2015-11-15 18:05:55"
             ["size"]=> string(6) "384.26"
             ["width"]=> string(4) "1366"
             ["height"]=> string(3) "768"
             ["image_type"]=> string(4) "jpeg"

             */

            foreach ($mobile_upload_fields as $field => $label) {

                /*
                 *   'proof_of_residence' => 'Proof of Residence, not more than 3 months old',
   'id_or_passport'  => 'Copy of your ID or Passport',
                 */
                        echo "<div class='form-group'>";
                        echo form_label($label, 'documents_element' , array ('class'=> 'control-label col-lg-3'));
                        echo "<div class='col-lg-6'>";


                   // var_dump($mobile_upload_value)
                        $img_path = $base_url . 'admin/get_mobile_document_admin/'. $mobile_upload_value[$field]['field_type'] . '/' . $user_id;
                        $current_img_width =  $mobile_upload_value[$field]['width'];
                        $current_img_height = $mobile_upload_value[$field]['height'];

                        $preview_height = '150';
                        $preview_width = '150';

                        if ($current_img_width > $current_img_height){

                            $coef =   $current_img_height / $current_img_width;
                            $preview_height = round($preview_height * $coef);

                        } else {

                            $coef =   $current_img_width / $current_img_height ;
                            $preview_width = round($preview_width * $coef);
                        }

                        echo "<a style='margin : 15px;' href='" . $img_path . "'><img  height='" . $preview_height . "' width='" . $preview_width . "' src='" . $img_path  . "' title='" . $label  . "'></a>";
                        echo "</div>";
                        echo "</div>";

            }

/*
        echo "<div class='form-group'>";

                echo form_label("Mobile Number", 'mobile_data_number_label' , array ('class'=> 'control-label col-lg-3'));
                echo "<div class='col-lg-6'>";
                    echo form_input(array(
                        'class' => 'form-control',
                        'name' => "mobile_data_number",
                        'placeholder' => '',
                        'id' => "mobile_data_number_element",
                        'value' => '',
                       // 'readonly' => 'readonly',
                    ));
                echo "</div>";

        echo "</div>";


        echo "<div class='form-group'>";

            echo form_label("Serial Number", 'mobile_data_number_label' , array ('class'=> 'control-label col-lg-3'));
            echo "<div class='col-lg-6'>";
                echo form_input(array(
                    'class' => 'form-control',
                    'name' => "mobile_data_serial_number",
                    'placeholder' => '',
                    'id' => "mobile_data_serial_number_element",
                    'value' => '',
                    // 'readonly' => 'readonly',
                ));
            echo "</div>";

        echo "</div>";
*/
        ?>
    </div>

    <br/><br/>


    <div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
        <?php // echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Process Mobile Data SIM',)); ?>
        <?php // echo form_reset(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Cancel',));?>
    </div>
</fieldset>
<?php echo form_close();?>