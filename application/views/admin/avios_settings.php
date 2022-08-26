<script language="JavaScript" type="text/javascript">
    $(document).ready(function () {
        $('#avios_id').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#br_a_id').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

        if($("#avios_id").val().length > 0) {
            $('#br_a_id').prop('disabled', true);
        } else {
            $('#br_a_id').prop('disabled', false);
        }

        if($("#br_a_id").val().length > 0) {
            $('#avios_id').prop('disabled', true);
        } else {
            $('#avios_id').prop('disabled', false);
        }

        $('#avios_id').keypress(function () {
            if($("#avios_id").val().length > 0) {
                $('#br_a_id').prop('disabled', true);
            } else {
                $('#br_a_id').prop('disabled', false);
            }
        });

        $('#avios_id').focusout(function () {
            var len = $("#avios_id").val().length;
            if(len === 0) {
                $('#br_a_id').prop('disabled', false);
            } else {
                $('#br_a_id').prop('disabled', true);
            }
        });

        $('#br_a_id').keypress(function () {
            if($("#br_a_id").val().length > 0) {
                $('#avios_id').prop('disabled', true);
            } else {
                $('#avios_id').prop('disabled', false);
            }
        });

        $('#br_a_id').focusout(function () {
            var brlen = $("#br_a_id").val().length;
            if(brlen === 0) {
                $('#avios_id').prop('disabled', false);
            } else {
                $('#avios_id').prop('disabled', true);
            }
        });

        $.validator.addMethod(
            "numbers",
            function (element, value) {
                var re = new RegExp('[0-9]');
                return this.optional(element) || re.test(value);
            },
            "Not valid Member Number"
        );

        $("#avios_form").validate({
            rules: {
                avios_id: "numbers",
                br_a_id: "nubers"
            }
        });

    });
</script>
<style>
    .text-notice {
        text-align: center;
    }
    .avios-form-header {
        font-size: small;
    }
    .footer-avios {
        color: #666;
        font-size: smaller;
        text-align: center;
    }
</style>
<h3>Avios Settings</h3>
<?php
$avios_fields = array(
    "user_name" => "Username",
    "avios_id" => "Avios Membership Number",
    "br_a_id" => "British Airways Membership Number"
);

echo form_open('admin/update_avios_settings', array('class' => 'form-horizontal','id' => 'avios_form'));?>
<fieldset>
    <?php
    if (!empty($err_mess)) {
        echo "<div class='alert alert-danger'>$err_mess</div>";
    }
    if (!empty($mess)) {
        echo "<div class='alert alert-success'>$mess</div>";
    }
    ?>


    <div class="row">
        <div class="col-lg-12">
            <legend class="avios-form-header">Update user info with Avios / British Airways information below</legend>
            <?php
            foreach ($avios_fields as $f=>$l) {
                if (isset($user_data['user_settings'][$f])) {
                    $v = $user_data['user_settings'][$f];
                } else {
                    $v = '';
                }
                ?>
                <div class="form-group">
                    <?php echo form_label($l, $f, array ('class'=> 'control-label col-lg-3')); ?>
                    <div class="col-lg-6">
                        <?php
                        echo form_input(
                            array(
                                'class' => 'form-control',
                                'name' => $f,
                                'placeholder' => '',
                                'id' => $f,
                                'value' => $v
                            )
                        );
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <p class="text-notice"><b>Please note, you can EITHER enter an Avios Membership Number or British Airlines Membership Number.</b></p>
            <p class="text-notice"><b>The system will not accept both.</b></p>
        </div>
    </div>
    <div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
        <?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => "Submit",)); ?>
    </div>
</fieldset>
<?php echo form_close();?>
<hr>
