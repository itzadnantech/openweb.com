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
            <div class="col-md-5 col-md-offset-4">
                <br>
                <form action="/login/add_mobile" class="login-form validate" id="login-form" method="post" name="login-form">
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label class="form-label">Mobile Number</label>
                            <input class="form-control" id="txtusername" name="mobile" type="text" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <button class="btn btn-primary btn-cons pull-right" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>