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
            <h3>Dashboard</h3>
        </div>
        <?php if(isset($products)) { ?>
        <div class="row">
            <div class="col-md-5 col-sm-6 m-b-10">
                <div class="tiles blue ">
                    <div class="tiles-body">
                        <div class="tiles-title" ><h4 class="text-white">Active Products</h4> </div>
                        <div class="heading"> <span class="animate-number" data-value="<?php echo $products; ?>" data-animation-duration="1200"><?php echo $products; ?></span> </div>
                        <div class="description"><i class="icon-custom-up"></i><span class="text-white mini-description ">&nbsp; Click to review </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
