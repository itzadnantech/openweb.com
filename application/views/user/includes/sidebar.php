 <div class="page-sidebar " id="main-menu">
    <!-- BEGIN MINI-PROFILE -->
    <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
        <div class="user-info-wrapper sm">
            <div class="profile-wrapper sm">
                <img src="<?php echo base_url('assets/img/user_logo.png') ?>" alt="" data-src="<?php echo base_url('assets/img/user_logo.png') ?>" data-src-retina="<?php echo base_url('assets/img/user_logo.png') ?>" width="69" height="69" />
                <div class="availability-bubble online"></div>
            </div>
            <div class="user-info sm">
                <div class="username"><?php echo $first_name . " " . $second_name ?> </div>
                <div class="status"><?php echo $ownumber ?></div>
            </div>
        </div>
        <!-- END MINI-PROFILE -->
        <!-- BEGIN SIDEBAR MENU -->
        <p class="menu-title sm">BROWSE</p>
        <ul>
            <li class="start  open active "> <a href="/user/dashboard"><i class="material-icons">home</i> <span class="title">Dashboard</span> <span class="selected"></span></a>
            </li>
            <li>
                <a href="/user/settings"> <i class="fa fa-user"></i> <span class="title">My Account</span>
                </a>
            </li>
            <li>
                <a href="/user/invoices"> <i class="material-icons">email</i> <span class="title">Invoices</span>
                </a>
            </li>
            <li>
                <a href="javascript:;"> <i class="material-icons">cloud</i> <span class="title">My Services</span> <span class=" arrow"></span> </a>
                <ul class="sub-menu">
                    <li> <a href="/user/active_orders">Active Services </a> </li>
                    <li> <a href="/user/inactive_orders">Inactive Services</a> </li>
                    <li> <a href="/user/orders">All Services</a> </li>
                </ul>
            </li>
            <li class="">
                <a href="javascript:;"> <i class="material-icons">shop</i> <span class="title">Order Products</span> <span class=" arrow"></span> </a>
                <ul class="sub-menu">
                    <?php
                    foreach ($categories as $cat) {
                        $substring = '';
                        foreach ($sub_categories[$cat['id']] as $sub) {

                            $substring .= '<li> <a href="/product/show_offerings/' . $sub['id'] . '">' . $sub['name'] . '</a> </li>';
                        }

                        echo '<li>
                        <a href="javascript:;"> <span class="title">' . $cat['name'] . '</span> <span class=" arrow"></span> </a>
                        <ul class="sub-menu">' . $substring . '</ul>
                    </li>';
                    }
                    ?>

                </ul>
            </li>


            <?php if ($role == 'reseller') : ?>
                <li class="">
                    <a href="javascript:;"> <i class="material-icons">map</i> <span class="title">Coverage Maps</span> <span class=" arrow"></span> </a>
                    <ul class="sub-menu">
                        <li class="">
                            <a href="/user/lte_coverage_map">Telkom Fixed LTE Coverage Map</a>
                        </li>
                        <li class="">
                            <a href="/user/fibre_coverage_map">Universal Fibre Coverage Map</a>
                        </li>
                        <li class="">
                            <a href="/user/mtn_fixed_lte_coverage_map">MTN Fixed LTE Coverage Map</a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <li class=""> <a href="/user/activity_log"><i class="material-icons">airplay</i> <span class="title">Activity Log</span> </a>
            </li>
        </ul>
        <div class="footer-widget">
            <div class=" transparent progress-small no-radius no-margin">
                <a href="http://openweb.co.za/support/">Contact us</a><br>
                <a href="http://openweb.co.za/terms-conditions/">Privacy policy</a>
            </div>
            <div class="pull-right">

            </div>
        </div>
        <div class="clearfix"></div>
        <!-- END SIDEBAR MENU -->
    </div>
</div>
<a href="#" class="scrollup">Scroll</a>