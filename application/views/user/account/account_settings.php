<?php
    if(isset($current_page) && isset($tab_show)) {

        $$current_page = 'active';
        $$tab_show = 'true';

    } else {
        $tab1 = 'active';
    }

    if(isset($message))
        $msg = '<div class="alert"><button class="close" data-dismiss="alert"></button>'.$message.' </div>'

?>

<div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div id="portlet-config" class="modal hide">
        <div class="modal-header">
            <button data-dismiss="modal" class="close" type="button"></button>
            <h3>Modal</h3>
        </div>
        <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content sm-gutter">
        <div class="page-title">
            <h3>Account Settings</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tabbable tabs-left">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="<?php if(isset($tab1)){echo $tab1;} ?>">
                            <a href="#tab2hellowWorld" role="tab" data-toggle="tab" aria-expanded="<?php if(isset($showtab1)){echo $showtab1;} ?>">Contact Information</a>
                        </li>
                        <li class="<?php if(isset($tab2)){echo $tab2;} ?>">
                            <a href="#tab2FollowUs" role="tab" data-toggle="tab" aria-expanded="<?php if(isset($showtab2)){echo $showtab2;} ?>">Billing Settings</a>
                        </li>
                        <li class="<?php if(isset($tab3)){echo $tab3;} ?>">
                            <a href="#tab2Inspire" role="tab" data-toggle="tab" aria-expanded="<?php if(isset($showtab3)){echo $showtab3;} ?>">Personal Data</a>
                        </li>
                        <li class="<?php if(isset($tab4)){echo $tab4;} ?>">
                            <a href="#tab2avios" role="tab" data-toggle="tab" aria-expanded="<?php if(isset($showtab4)){echo $showtab4;} ?>">Avios Settings</a>
                        </li>
                        <li class="<?php if(isset($tab5)){echo $tab5;} ?>">
                            <a href="#tab-password" role="tab" data-toggle="tab" aria-expanded="<?php if(isset($showtab5)){echo $showtab5;} ?>">Change Password</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php if(isset($tab1)){echo $tab1;} ?>" id="tab2hellowWorld">
                            <div class="row column-seperation">
                                <div class="col-md-6">
                                    <?php if (!empty($suc_msg)) {
                                        echo "<div class='alert alert-success'>$suc_msg</div>";
                                    }?>
                                    <?php
                                    echo form_open('user/update_account', array('class'=>'form-horizontal', 'id'=>'updata_account_form'));
                                    ?>
                                    <fieldset>
                                        <input type="hidden" id="account_id" name="account_id" value="<?php echo $user_id;?>"/>
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">Email Address</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="email_address" class="form-control" value="<?php echo $email?>" id="email_address">
                                            </div>
                                            <div style="color: #f62b2b; font-size: 25px;">*</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">Mobile Number </label>
                                            <div class="col-lg-6">
                                                <input type="text" name="mobile_number" class="form-control" value="<?php echo $mobile?>" id="mobile_number">
                                            </div>
                                        </div>
                                        <div style="letter-spacing: 100px;padding-left: 100px;">
                                            <input type="submit" name="" value="Save" class="btn btn-primary btn-lg">
                                            <input type="reset" name="" value="Cancel" class="btn btn-primary btn-lg">
                                        </div>
                                    </fieldset>
                                    <?php echo form_close();?>
                                </div>
                                <div class="col-md-6">
                                    <h3>Mailing List</h3>
                                    <div class="slide-primary">
                                        <input type="checkbox" name="switch" class="ios" checked="" />
                                    </div>
                                    <h3>Invoice email</h3>
                                    <div class="slide-primary">
                                        <input type="checkbox" name="switch" class="iosblue" checked="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if(isset($tab2)){echo $tab2;} ?>" id="tab2FollowUs">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $billing ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if(isset($tab3)){echo $tab3;} ?>" id="tab2Inspire">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $mobile_data ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if(isset($tab4)){echo $tab4;} ?>" id="tab2avios">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $avios_settings_page ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if(isset($tab5)){echo $tab5;} ?>" id="tab-password">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if(isset($message)){
                                        echo $msg;
                                    } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $change_password_page ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



    </div>
</div>


<h3 style="padding-bottom: 30px;">Account Settings</h3>
