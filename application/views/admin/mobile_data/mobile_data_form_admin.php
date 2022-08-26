<?php


//echo "<pre>";
//print_r($mobile_request);
//echo "</pre>";


if (isset($success_message)){


    echo "<div class='alert alert-success'> " . $success_message .  " </div>";
}

if (isset($fail_message)){

    echo "<div class='alert alert-success'> " . $fail_message .  " </div>";
}


if (isset($mobile_request) && !empty($mobile_request)) {


    echo form_open('admin/update_mobile_data_request', array('class' => 'form-horizontal','id' => 'create_product_form'));

   // echo form_hidden('id', $product_id);

    echo '<input type="hidden" value="' . $mobile_request['request_id'] . '" id="request_id_element" name="request_id"/>';

    ?>
    <fieldset>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">Request Date</label>
            <div class="col-lg-6">
                <?php

                    echo "<p style='margin-top:5px;'>" .  $mobile_request['request_date'] . "</p>";
                ?>
            </div>
        </div>
        <!--
        <div class="form-group">
            <label for="" class="control-label col-lg-3">First Response Date</label>
            <div class="col-lg-6">
                <?php

                $response_date = $mobile_request['response_date'];
                if (empty($response_date))
                    $response_date = ' - ';

                echo "<p style='margin-top:5px;'>" .  $response_date . "</p>";

                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="control-label col-lg-3">Last Modification Time</label>
            <div class="col-lg-6">
                <?php

                $last_modification = $mobile_request['last_modification_time'];
                if (empty($last_modification))
                    $last_modification = ' - ';

                echo "<p style='margin-top:5px;'>" . $last_modification . "</p>";

                ?>
            </div>
        </div>
-->

        <div class="form-group">
            <label for="" class="control-label col-lg-3">User</label>
            <div class="col-lg-6">
                <?php

                $profile_link = base_url() . 'admin/edit_account/' . $mobile_request['user_id'];
                echo "<p style='margin-top:5px;'>" . "<a target='_blank' href='" . $profile_link . "'>" . $mobile_request['first_name'] . " " . $mobile_request['last_name'] . " (" . $mobile_request['username'] . ") </a>" . "</p>" ;


                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">ISDSL Account</label>
            <div class="col-lg-6">
                <?php

                $profile_link = base_url() . 'admin/manage_order/' . $mobile_request['order_id'];
                echo "<p style='margin-top:5px;'>" .  "<a target='_blank' href='" . $profile_link . "'>" . $mobile_request['account_username'] . "@" . $mobile_request['account_realm'] . "</a>" . "</p>" ;


                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">Product</label>
            <div class="col-lg-6">
                <?php

                $profile_link = base_url() . 'admin/edit_product/' . $mobile_request['product_id'];
                echo "<p style='margin-top:5px;'>" .  "<a target='_blank' href='" . $profile_link . "'>" . $mobile_request['product_name'] . "</a>" . "</p>";


                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">Status</label>
            <div class="col-lg-6">
                <?php

                    $options = array(
                        'processed'   => 'Processed',
                        'unprocessed' => 'Unprocessed',
                        'refused'     => 'Refused',
                    );

                    // ALL, Unprocessed and Processed
                    //$dropdown_params =  "id='select-r' class='form-control valid' onchange='check()'";
                    echo form_dropdown('status', $options, $mobile_request['status'], "class='form-control valid'");


                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">Notice (optional)</label>
               <div class="col-lg-6">
                <?php echo form_input(
                    array(
                        'class' => "form-control $input_class",
                        'name' => 'notice',
                        'placeholder' => '',
                        'id' => 'notice_element',
                        'value' => $mobile_request['notice'],
                    )
                ); ?>
                   <h6>This message will be displayed on the client side. For example it can be short explanation why Mobile Data request was refused.
                   You can leave this field empty if you don't want to show any messages for client. </h6>

               </div>

        </div>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">Mobile SIM</label>
            <div class="col-lg-6">
                <?php echo form_input(
                    array(
                        'class' => "form-control $input_class",
                        'name' => 'mobile_sim',
                        'placeholder' => '',
                        'id' => 'mobile_sim_element',
                        'value' => $mobile_request['mobile_sim'],
                    )
                ); ?>

            </div>

        </div>

        <div class="form-group">
            <label for="" class="control-label col-lg-3">Mobile Details</label>
            <div class="col-lg-6">
                <?php echo form_input(
                    array(
                        'class' => "form-control $input_class",
                        'name' => 'mobile_details',
                        'placeholder' => '',
                        'id' => 'mobile_details_element',
                        'value' => $mobile_request['mobile_details'],
                    )
                ); ?>
            </div>
        </div>
        <div style="text-align:center; margin-top:20px;">
            <?php
                $btn_label = 'Submit';
                echo form_submit(array('class' => 'btn btn-lg btn-primary', 'value' => 'Update'));
            ?>
        </div>
    </fieldset>
    <?php echo form_close(); ?>
<?php
} else {

}
