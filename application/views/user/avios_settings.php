
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
    "br_a_id" => "British Airways Executive Club Membership Number"
);

echo form_open('user/update_avios_settings', array('class' => 'form-horizontal','id' => 'avios_form'));?>
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
            <legend class="avios-form-header">Update your British Airways information below in order to collect the Avios that your Product
            / Package qualifies for: </legend>
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
    </div>
</div>
    <div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
        <?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => "Submit",)); ?>
    </div>
</fieldset>
<?php echo form_close();?>
<hr>
<div class="col-lg-12">
    <p class="footer-avios">If you do not have a British Airways Membership Number</p>
    <p class="footer-avios">You can signup for free below by clicking on the logo of the programme you would like to join.</p>
</div>
<div class="col-lg-6 col-lg-offset-1">
    <div class="text-center">
        <a href="https://www.britishairways.com/"><img src="../img/BA-logo.png"  class="img-responsive"></a>
    </div>
</div>