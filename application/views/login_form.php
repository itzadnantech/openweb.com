<body class="error-body no-top" style="background-color: #fff">
<div class="container">
    <div class="row login-container">
        <div class="col-md-4 col-md-offset-4">
            <img class="img-responsive" src="<?php echo base_url('/img/main.png') ?>">
        </div>
    </div>
    <?php
        if(isset($error_message) && $error_message != '') {
            echo '<div class="row"><div class="col-md-8 col-md-offset-2"> <div class="alert alert-error">
                      <button class="close" data-dismiss="alert"></button>'.
                      $error_message.
                      '</div></div></div>';
        }
    ?>
        <?php
        if(isset($success_message) && $success_message != '') {
            echo '<div class="row"><div class="col-md-8 col-md-offset-2"> <div class="alert alert-success">
                      <button class="close" data-dismiss="alert"></button>'.
                      $success_message.
                      '</div></div></div>';
        }
    ?>
    <div class="row column-seperation">
        <div class="col-md-4 col-md-offset-2">
            <h2>
                Sign up
            </h2>
            <p>
                Don't have an OpenWeb profile yet? Create one now.
            </p>
            <br>
            <a href="/register" class="btn btn-block btn-info col-md-8" type="button"><span class="pull-left icon-facebook" style="font-style: italic"></span> <span class="bold">Create!</span></a>
        </div>
        <div class="col-md-5">
            <br>
            <form action="/login/validate_login" class="login-form validate" id="login-form" method="post" name="login-form">
                <div class="row">
                    <div class="form-group col-md-10">
                        <label class="form-label">Username</label>
                        <input class="form-control" id="txtusername" name="username" type="text" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-10">
                        <label class="form-label">Password</label> <span class="help"></span>
                        <input class="form-control" id="txtpassword" name="password" type="password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="control-group col-md-10">
                        <div class="checkbox checkbox check-success">
                            <a href="login/forgot_password">Forgot Password?</a>&nbsp;&nbsp;
                            <a href="/register">Signup</a>&nbsp;&nbsp;
                            <input id="checkbox1" type="checkbox" value="1" name="checkbox">
                            <label for="checkbox1">Remember Me</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <button class="btn btn-primary btn-cons pull-right" type="submit">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>