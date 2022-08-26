<style>
    body {
        background-color: #fff;
    }
</style>
<?php

if (empty($repopulated_array)){

    foreach($form_fields as $field){

        $repopulated_array[$field] = '';
    }
}

?>
<div class="header navbar navbar-inverse">
    <div class="navbar-inner">
        <div class="header-seperation img-responsive" style="background-color: #fff">
            <a href="<?php echo base_url(); ?>">
                <img src="<?php echo base_url('img/main.png') ?>" class="logo" alt="" data-src="<?php echo base_url('img/main.png') ?>" data-src-retina="<?php echo base_url('img/main.png') ?>" width="106" height="30">
            </a>
        </div>
        <div class="header-quick-nav">
            
        </div>
    </div>
</div>

<div class="row reg-page">

    <div class="col-md-12">

        <div class="grid simple form-grid">
            <div class="grid-title no-border">
                <br>
                <?php
                if (!empty($error_message)) {
                    echo "<div class='alert alert-danger'>$error_message</div>";
                }

                $validation_error =  validation_errors();
                if (!empty($validation_error)) {

                    echo "<div class='alert alert-danger'>" . $validation_error . "</div>";
                }


                ?>
            </div>
            <div class="grid-body no-border">
                <form method="post" class="form-no-horizontal-spacing" id="form-condensed" novalidate="novalidate" action="/register/create_user" >
                    <div class="row column-seperation">
                        <div class="col-md-6">
                            <h4>Personal Information</h4>
                            <div class="row form-row">
                                <div class="col-md-5">
                                    <input name="first_name" id="first_name" class="form-control" placeholder="First Name" type="text">
                                </div>
                                <div class="col-md-7">
                                    <input name="last_name" id="last_name" class="form-control" placeholder="Last Name" type="text">
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-md-5">
                                    <input name="sa_id_number" id="sa_id_number" class="form-control" placeholder="SA ID" type="text">
                                </div>
                                <div class="col-md-7">
                                    <input placeholder="email@address" class="form-control" id="email_address" name="email_address" type="text">
                                </div>
                            </div>

                            <div class="row form-row">
                                <div class="col-md-12">
                                    <input name="br_a_id" id="br_a_id" class="form-control" placeholder="British Airways Loyalty Program Member ID" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Login Information</h4>
                            <div class="row form-row">
                                <div class="col-md-12">
                                    <input name="username" id="username" class="form-control" placeholder="Username" type="text">
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <input name="password" id="password" class="form-control" placeholder="Password" type="password">
                                </div>
                                <div class="col-md-6">
                                    <input name="re_password" id="re_password" class="form-control" placeholder="Confirm Password" type="password">
                                </div>
                            </div>

                            <div class="row form-row">
                                <div class="col-md-4">
                                    <input name="code" id="code" class="form-control" placeholder="+27" type="text" disabled="disabled">
                                </div>
                                <div class="col-md-8">
                                    <input name="mobile_number" id="phone-nomask" placeholder="0XXXXXXXXX" class="form-control" type="text">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="pull-left">
                            <div class="checkbox checkbox check-success 	">
                                <input value="1" id="chkTerms" type="checkbox" name="agree_check">
                                <label for="chkTerms" id="checkbox-reg">I agree with the <a href="http://openweb.co.za/terms-conditions/">Terms and Conditions</a> </label>
                            </div>
                        </div>
                        <div class="pull-right">
                            <button class="btn btn-danger btn-cons" type="submit" id="reg-submit-but"><i class="icon-ok"></i>Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
