
<script src="<?php echo base_url('js/flat-ui/login-form.js'); ?>"></script>
<!--- -->
<body class="flat-ui-backgound">
    <div class="container"> 
            <div class="row">
                    <div class="col-lg-5 col-lg-offset-4">
                        <div class="flat-ui-login-form">
                            <form class="flat-ui-login-form text-center" >

                                <img class="" src="<?php echo base_url() ?>img/main-logo.png" style="width: 88px; height: 80px;" >
                                <p> <b>Login</b> </p>
                                <p>Related text can come here</p>
                                <input type="text" value="" placeholder="username" class="form-control flat-ui-form-inputs">
                                <input type="text" value="" palceholder="password"  class="form-control flat-ui-form-inputs">
                                <input type="submit" value="sign in" class="btn btn-default form-control">


                            </form>
                               <a href="#" class='pull-left'>forgot password ?</a>  <a href="#" class="pull-right">register now!</a>      

                        </div>
                               

                    </div>
            </div>



    </div>
</body>

<?php /*
<div id="login_form" style="text-align:center; margin-top: 10px">
    <?php
    echo form_open('login/validate_login', array('class' => 'form-horizontal','id' => 'form_login')); ?>
    <?php if (!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    } ?>
    <?php if (!empty($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    } ?>
    <div class="col-lg-10 col-lg-offset-1">
        <div class="form-group">
            <?php echo form_label('Username', 'username', array ('class'=> 'lead control-label col-lg-4')); ?>
            <div class="col-lg-5">
                <?php echo form_input(
                    array(
                        'class' => 'form-control input-lg',
                        'name' => 'username',
                        'placeholder' => 'Username/Email Address/OW Number',
                        'id' => 'username',
                    )
                ); ?>
            </div></div>
        <div class="form-group">
            <?php echo form_label('Password', 'password', array ('class'=> 'lead control-label col-lg-4')); ?>

            <div class="col-lg-5">
                <?php echo form_password(
                    array(
                        'class' => 'form-control input-lg',
                        'placeholder' => '',
                        'name' => 'password',
                        'id' => 'password'
                    )
                ); ?>
            </div></div>
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <a href="<?php base_url();?>login/forgot_password" style="text-decoration: none;font-size: 18px;">Forgot Password</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4"></div>
            <div class="col-lg-3">
                <?php echo form_submit(
                    array (
                        'class' => 'btn pull-right btn-lg btn-primary',
                        'value' => 'Sign me in',
                    )
                ); ?>

                <?php ///echo anchor('login/signup', 'Create an account', array('class' => 'btn')); ?>
            </div></div>
    </div>
</div>

*/?>