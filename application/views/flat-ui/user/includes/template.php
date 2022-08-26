<!DOCTYPE html>
<html lang="en">
<?php

    $this->load->view("flat-ui/header");
    $this->load->view("flat-ui/" . $main_content);
    //$this->load->view("flat-ui/user/includes/footer");

?>
</html>







<?php /*
 ex- header

<body>
<div class="jumbotron">
    <div class="container">
        <h2 class="logo">
            <img src="<?php echo base_url() ?>img/main.png" style="width: 253px; height: 80px;">
            <?php
            if (isset($this->site_data['first_name'])){
                $first_name = $this->site_data['first_name'];
                ?>
                <div class="pull-right lead" style="color: #428bca;font-size: 18px;margin-top:10px;margin-right:40px;">
                    <?php echo "Welcome, $first_name"; ?>.<br/>

                    <?php
                    if (isset($this->site_data['last_login_time'])){
                        $last_login_time = $this->site_data['last_login_time'];
                    }else{
                        $last_login_time = '';
                    }
                    ?>
                    Current Time :<?php echo date('Y-m-d',time());?><br/>
                    Last Login Time :<?php echo $last_login_time;?><br/>
                    OW Number : <?php echo (isset($this->site_data['ow'])) ? $this->site_data['ow'] : '';?>
                </div>
            <?php }	?>
        </h2>
    </div>
</div>
<?php
if (isset($this->site_data['username'])) {
    $username = $this->site_data['username'];
} else {
    $username = '';
}

if ($username != '') {
    $this->load->view('user/includes/navbar');
} else {
    ?>
    <div class="navbar lead">
        <div class="container">
            <ul class="nav nav-pills navbar-nav">
                <li class="active"><?php echo anchor('login', 'Sign Into Start'); ?></li>
                <li class="active"><?php echo anchor('register', 'Signup'); ?></li>
            </ul>
        </div>
    </div>
<?php
}
?>
<div id="page-content" class="container">
    <div class="row">
        <?php if (!empty($sidebar)) { ?>
        <div class="col-lg-3">
            <!--Sidebar content-->
            <?php $this->load->view('user/includes/sidebar'); ?>
        </div>

        <div class="col-lg-9">
            <?php } else { ?>
            <div class="col-lg-12">
                <?php } ?>
                <!--Body content-->
 -->
 */ ?>
