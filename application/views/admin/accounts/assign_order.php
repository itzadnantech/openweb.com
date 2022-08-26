 <script language="javascript" type="text/javascript">
    $(document).ready(function() {

        $("#assign_order_form").validate({
            rules: {
                account_username: "required",
                account_password: "required",
            }
        });

        // hide all form fileds and show only selected
        handleServiceChange();

        // hide lte password for telcom lte type
        handleltetypeChange();

        $('#assign_order_form').submit(function() {
            var price = $('#price').val();
            var pro_rata = $('#proRata').val();
            var reg = new RegExp('^[0-9]*$');

            if (price != '' && reg.test(price) && pro_rata != '' && reg.test(pro_rata)) {
                $('#price_notice').hide();
                $('#pro_rata_notice').hide();
                return true;
            } else if (price == '' && pro_rata == '') {
                $('#price_notice').html('This field is required.');
                $('#price_notice').show();

                $('#pro_rata_notice').html('This field is required.');
                $('#pro_rata_notice').show();
                return false;
            } else if (price == '') {
                $('#price_notice').html('This field is required.');
                $('#price_notice').show();
                return false;
            } else if (pro_rata == '') {
                $('#pro_rata_notice').html('This field is required.');
                $('#pro_rata_notice').show();
                return false;
            } else if (!reg.test(price)) {
                $('#price_notice').html('Please enter a valid number.');
                $('#price_notice').show();
                return false;
            } else {
                $('#pro_rata_notice').html('Please enter a valid number.');
                $('#pro_rata_notice').show();
                return false;
            }
        });


        $('#price').blur(function() {
            var discount = $('#price').val();
            var reg = new RegExp('^[0-9]*$');

            if (reg.test(discount)) {
                $('#price_notice').hide();
            } else {
                $('#price_notice').html('Please enter a valid number.');
                $('#price_notice').show();
                $('#price').focus();
            }
        });

        $('#proRata').blur(function() {
            var discount = $('#proRata').val();
            var reg = new RegExp('^[0-9]*$');

            if (reg.test(discount)) {
                $('#pro_rata_notice').hide();
            } else {
                $('#pro_rata_notice').html('Please enter a valid number.');
                $('#pro_rata_notice').show();
                $('#proRata').focus();
            }
        });
        $("select[name='status']").change(function() {

            if ($(this).val() != 'active') {

                $("#email_sms").prop("disabled", true);
                $('#email_sms').attr('checked', false);

            } else {

                $("#email_sms").prop("disabled", false);
            }

        });

        function handleServiceChange() {

            /*
             fibre-data
             fibre-line
             adsl
             */
            // get current option
            var currentService = $("select#service_list_dropdown").val();
            //console.log(currentService);
            disableAllFields("form#assign_order_form");
            showServiceFields(currentService);


            if (currentService == 'fibre-line') {
                $('.lte_password_field').hide();
                $('.lte-a-telcom-service-password-group').hide()
            }
            if (currentService == 'mobile') {
                $(".lte-a-telcom-service-sim-serial-no").show();
            }



        }

        function handleltetypeChange() {
            var ltetype = $("select#lte_type").val();
            if (ltetype == "telkom" || ltetype == "mtn") {
                $(".lte-a-telcom-service").hide();
                $(".lte-a-telcom-service-sim-serial-no").show();
            } else {
                $(".lte-a-telcom-service").show();
                $(".lte-a-telcom-service-sim-serial-no").hide();

            }


        }

        $("select#lte_type").change(handleltetypeChange);

        $("select#service_list_dropdown").change(handleServiceChange);

        function disableAllFields() {

            $(".adsl-service").hide();
            $(".fibre-data-service").hide();
            $(".fibre-line-service").hide();
            $(".lte-a-service").hide();
            $(".mobile-service").hide();
            //$(".showmx-sub-service").hide();
            // hide fibre_data
            // hide fibre_line

        };


        function showServiceFields(service) {


            var selector = "." + service + "-service";
            $(selector).show();
            var ltetype = $("select#lte_type").val();
            if (ltetype == "telkom" || ltetype == "mtn") {
                $(".lte-a-telcom-service").hide();
                $(".lte-a-telcom-service-sim-serial-no").show();

            } else {
                $(".lte-a-telcom-service").show();
                $(".lte-a-telcom-service-sim-serial-no").hide();

            }
            //console.log(selector);
        };


        $(function() {

            $("#user_search").autocomplete({
                source: function(request, response) {

                    $.ajax({
                        url: "/admin/searchUser",
                        dataType: "jsonp",
                        data: {
                            string: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 3,
                select: function(event, ui) {
                    $('#username').val(ui.item.username);
                }
            });
        });

    });
</script>

<?php
$status = array(
    'active' => 'Active',
    'pending' => 'Pending',
    'deleted' => 'Deleted',
    'expired' => 'Expired',
);


$service_list = array(
    'adsl'       => 'ADSL',
    'fibre-data' => 'Fibre Data',
    'fibre-line' => 'Fibre Line',
    'lte-a'      => 'LTE-A',
    'mobile'      => 'Mobile',
    // 'showmx-sub' => 'ShowMax Subscription'
);

$billing_cycles = array(
    'Monthly' => 'Monthly',
    'Once-Off' => 'Once-Off',
);

// get this one from model
/*
$showmax_options = array();
if (!empty($showmax_subscription_types))
    foreach ($showmax_subscription_types as $type)
        $showmax_options[$type] = ucfirst($type);
*/

if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
    $m = $messages['success_message'];
    echo "<div class='alert alert-success'>$m</div>";
}

if (isset($messages['error_message']) && trim($messages['error_message']) != '') {
    $error_message = $messages['error_message'];
    echo "<div class='alert alert-danger'>$error_message </div>";
}
?>
<h3 style="margin-bottom:30px;">Assign a Service to User</h3>
<div class="container">
    <?php
    echo form_open('admin/process_assign_order', array('class' => 'form-horizontal', 'id' => 'assign_order_form')) ?>

    <!-- service select -->
    <div class="form-group">
        <label class="col-lg-3">Service</label>
        <div class="col-lg-8">
            <?php echo form_dropdown('service', $service_list, 'adsl', 'class="form-control" id="service_list_dropdown"') ?>
        </div>
    </div>

    <!-- (ADSL data, Fibre data, Fibre line, ShowMax Subscription) -->
    <!-- user select -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service showmx-sub-service lte-a-service mobile-service">
        <label class="col-lg-3">User</label>

        <?php if (isset($username)) { ?>
            <div class="col-lg-8">
                <?php echo $username; ?>
                <input type="hidden" name="username" value="<?php echo $username ?>">
            </div>
        <?php } else { ?>
            <div class="col-lg-8">
                <input id="user_search" class="form-control" placeholder="Type to Search by Name or Username">
                <input name="username" id="username" hidden>
            </div>
        <?php } ?>

    </div>


    <!-- (Fibre data, Fibre line) Name of Product: (Admin will type in the name of the product for each order) -->
    <div class="form-group fibre-data-service fibre-line-service lte-a-service adsl-service mobile-service">
        <label class="col-lg-3">Name of Product</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="product_name_fd" id="product_name_fd_element">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!-- (Fibre data, Fibre line) Billing Cycle : Monthly or Once-Off  -->
    <div class="form-group fibre-data-service fibre-line-service">
        <label class="col-lg-3">Billing Cycle</label>
        <div class="col-lg-8">
            <?php echo form_dropdown('billing_cycle', $billing_cycles, 'Monthly', 'class="form-control" id="billing_cycle_element"') ?>
        </div>
    </div>

    <!-- (ShowMax subscription) subscription types : select or premium (accroding to doc.)  -->
    <!--
    <div class="form-group showmx-sub-service">
        <label class="col-lg-3">Subscription type</label>
        <div class="col-lg-8">
            <?php echo form_dropdown('showmax_subscription_type', $showmax_options, 'premium', 'class="form-control" id="showmax_subscription_type_element"') ?>
        </div>
    </div> -->

    <!--(ADSL , Fibre data, Fibre line) -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service showmx-sub-service lte-a-service">
        <label class="col-lg-3">Billing Code</label>
        <div class="col-lg-4 ">

            <?php echo form_dropdown('avios_code', $avios_code, ' ', 'class="form-control" id="avios_code"') ?>

        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(LTE) -->
    <div class="form-group lte-a-service">
        <label class="col-lg-3">LTE Type</label>
        <div class="col-lg-4 ">
            <?php echo form_dropdown('lte_type', $lte_types, ' ', 'class="form-control" id="lte_type"') ?>
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(ADSL , Fibre data, Fibre line) -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service showmx-sub-service lte-a-service mobile-service">
        <label class="col-lg-3">Price Per Month</label>
        <div class="col-lg-2 ">
            <div class="input-group">
                <span class="input-group-addon">R</span>
                <input type="text" name="price" class="form-control" id="price">
            </div>
            <div style="display: none; color: rgb(246, 43, 43); width: 500px; font-weight: bold;" id="price_notice"></div>
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(ADSL , Fibre data, Fibre line) -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service showmx-sub-service lte-a-service mobile-service">
        <label class="col-lg-3">Price This Month (Pro-rata)</label>
        <div class="col-lg-2">
            <div class="input-group">
                <span class="input-group-addon">R</span>
                <input type="text" name="proRata" class="form-control" id="proRata">
            </div>
            <div style="display: none; color: rgb(246, 43, 43); width: 500px; font-weight: bold;" id="pro_rata_notice"></div>
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(ADSL , Fibre data) -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service showmx-sub-service lte-a-service mobile-service">
        <label class="col-lg-3">Status</label>
        <div class="col-lg-3">
            <?php echo form_dropdown('status', $status, 'active', 'class="form-control"') ?>
        </div>
    </div>

    <!--(ADSL) -->
    <div class="form-group adsl-service">
        <label class="col-lg-3">IS Account Username</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="account_username" id="account_username" placeholder="username@realm">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>
    <!--(ADSL) -->
    <div class="form-group adsl-service">
        <label class="col-lg-3">IS Account Password</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="account_password" id="account_password">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(Fibre data) -->
    <div class="form-group fibre-data-service">
        <label class="col-lg-3">Fibre Data Username</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="username_fd" id="username_fd_element">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>
    <!--(Fibre data) -->
    <div class="form-group fibre-data-service">
        <label class="col-lg-3">Fibre Data Password</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="password_fd" id="password_fd_element">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(LTE-A) -->
    <div class="form-group lte-a-service mobile-service">
        <label class="col-lg-3">Mobile Username</label>
        <div class="col-lg-8">
            <input type="number" class="form-control" name="username_la" id="username_fd_element">
                        <!--<input type="number" class="form-control" name="username_la" id="username_fd_element" placeholder="username@openwebmobile.co.za">-->

        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>
    <!--(Mobile data) -->
    <!-- <div class="form-group mobile-service">
        <label class="col-lg-3">Mobile Number</label>
        <div class="col-lg-8">
            <input type="number" class="form-control" name="username_la" id="username_la">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div> -->
    <!--(LTE-A) 13012020-->
    <div class="lte-a-service lte-a-telcom-service-sim-serial-no mobile-service">
        <div class="form-group lte-a-service mobile-service">
            <label class="col-lg-3">SIM Serial Number</label>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="sim_serial_number" id="sim_serial_number_fd_element">
            </div>
            <div style='color:#f62b2b;font-size:25px;'>*</div>
        </div>
    </div>
    <!--(LTE-A) -->
    <div class="form-group lte-a-service lte-a-telcom-service lte-a-telcom-service-password-group">
        <label class="col-lg-3" id="passLab">LTE Password</label>
        <div class="col-lg-8">
            <input type="text" class="form-control lte_password_field" name="password_la" id="password_fd_element">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(Fibre data) -->
    <div class="form-group fibre-data-service">
        <label class="col-lg-3">Fibre Provider</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="provider_fd" id="provaider_fd_element">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>


    <!--(Fibre line) -->
    <div class="form-group fibre-line-service">
        <label class="col-lg-3">Fibre Line Number</label>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="number_fl" id="number_fl_element">
        </div>
        <div style='color:#f62b2b;font-size:25px;'>*</div>
    </div>

    <!--(ADSL) -->
    <div class="form-group adsl-service ">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" checked="checked" value="1" id="change_flag" name="change_flag"> <b>Allow user to change his password.</b>
        </div>
    </div>

    <!-- LTE-A -->
    <div class="form-group lte-a-service mobile-service ">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" checked="checked" value="1" id="display_usage_fd" name="display_usage_fd"> <b>Display the usage stats in the user panel.</b>
        </div>
    </div>

    <!--(ADSL) -->
    <div class="form-group adsl-service ">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" checked="checked" value="1" id="display_usage" name="display_usage"> <b>Display the usage stats in the user panel.</b>
        </div>
    </div>

    <!--(Fibre data) -->
    <div class="form-group  fibre-data-service lte-a-service mobile-service">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" value="1" id="change_flag_fd_element" name="change_flag_fd" disabled="disabled"> <b>Allow user to change his password.</b>
        </div>
    </div>

    <!--(Fibre data) -->
    <div class="form-group  fibre-data-service ">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" value="1" id="display_usage_fd_element" name="display_usage_fd" disabled="disabled"> <b>Display the usage stats in the user panel.</b>
        </div>
    </div>


    <!--(ADSL, Fibre data) -->
    <div class="form-group adsl-service fibre-data-service lte-a-service mobile-service">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" checked="checked" value="1" id="cancel_flage" name="cancel_flage"> <b>Client can auto cancel a product.</b>
        </div>
    </div>

    <!--(ADSL, Fibre data) -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service lte-a-service mobile-service">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" value="1" id="email_sms" name="email_sms" checked> <b>Send email and SMS to user</b>
        </div>
    </div>

    <!--(ADSL, Fibre data) -->
    <div class="form-group adsl-service fibre-data-service fibre-line-service lte-a-service mobile-service">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-8">
            <input type="checkbox" value="1" id="write_to_log_id" name="write_to_log" checked> <b>Write to Active log</b>
        </div>
    </div>


    <div class="well adsl-service" style="margin-top: 15px;">
        <strong>Note: </strong>This process will not run the addRealmAccount() function.
    </div>
    <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Assign Order">
    </div>


    <!--



    Fibre Line Number:

    -->



    <?php echo form_close(); ?>
</div>