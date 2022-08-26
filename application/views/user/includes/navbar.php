<!-- BEGIN HEADER -->
<div class="header navbar navbar-inverse ">
<!-- BEGIN TOP NAVIGATION BAR -->
<div class="navbar-inner">
    <div class="header-seperation">
        <ul class="nav pull-left notifcation-center visible-xs visible-sm">
            <li class="dropdown">
                <a href="#main-menu" data-webarch="toggle-left-side">
                    <i class="material-icons">menu</i>
                </a>
            </li>
        </ul>
        <!-- BEGIN LOGO -->
        <a href="<?php echo base_url('user/dashboard') ?>">
            <img src="<?php echo base_url('img/white-logo.png') ?>" class="logo logout-message" alt="" data-src="<?php echo base_url('img/white-logo.png') ?>" data-src-retina="<?php echo base_url('img/white-logo.png') ?>" width="106" height="21" />
        </a>
        <!-- END LOGO -->
        <ul class="nav pull-right notifcation-center">
            <li class="dropdown hidden-xs hidden-sm">
                <a href="/" class="dropdown-toggle active" data-toggle="">
                    <i class="material-icons">home</i>
                </a>
            </li>
            <li class="dropdown visible-xs visible-sm">
                <a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">
                    <i class="material-icons">tune</i>
                </a>
                <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                    <li>
                        <a href="/user/settings"> My Account</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="/user/logout"><i class="material-icons">power_settings_new</i>&nbsp;&nbsp;Log Out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- END RESPONSIVE MENU TOGGLER -->
<div class="header-quick-nav">
    <!-- BEGIN TOP NAVIGATION MENU -->
    <div class="pull-left">
        <ul class="nav quick-section">
            <li class="quicklinks">
                <a href="#" class="" id="layout-condensed-toggle">
                    <i class="material-icons">menu</i>
                </a>
            </li>
        </ul>
        <ul class="nav quick-section">
            <li class="quicklinks">
                <a href="#" class="" id="my-task-list" data-placement="bottom" data-content='' data-toggle="dropdown" data-original-title="Notifications">
                    <i class="material-icons">notifications_none</i>
                    <span class="" id="new-notification"></span>
                </a>
            </li>
            <!--<li class="m-r-10 input-prepend inside search-form no-boarder">
                <span class="add-on"> <i class="material-icons">search</i></span>
                <input name="" type="text" class="no-boarder " placeholder="Search Dashboard" style="width:250px;">
            </li>-->
        </ul>
    </div>
    <div id="notification-list" style="display:none">
        <div style="width:300px">
            <?php foreach ($notifications as $not) { ?>
            <div class="notification-messages info">
                <div class="message-wrapper">
                    <div class="heading">
                        <?php echo $not['content'] ?>
                    </div>
                    <div class="description">
                        <span class="notification-new" id="<?php echo $not['id'] ?>"><?php echo $not['new'] ?></span>
                    </div>
                    <div class="date pull-left">
                        <?php echo $not['date_created'] ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php } ?>
        </div>
    </div>
    <!-- END TOP NAVIGATION MENU -->
    <!-- BEGIN CHAT TOGGLER -->
    <div class="pull-right">
        <div class="chat-toggler sm">
            <div class="profile-pic">
                <img src="<?php echo base_url('assets/img/user_logo.png')?>" alt="" data-src="<?php echo base_url('assets/img/user_logo.png')?>" data-src-retina="<?php echo base_url('assets/img/user_logo.png')?>" width="35" height="35" />
                <div class="availability-bubble online"></div>
            </div>
        </div>
        <ul class="nav quick-section ">
            <li class="quicklinks">
                <a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">
                    <i class="material-icons">tune</i>
                </a>
                <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                    <li>
                        <a href="/user/settings"> My Account</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="/user/logout"><i class="material-icons">power_settings_new</i>&nbsp;&nbsp;Log Out</a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
    <!-- END CHAT TOGGLER -->
</div>
<!-- END TOP NAVIGATION MENU -->
</div>
    <!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->