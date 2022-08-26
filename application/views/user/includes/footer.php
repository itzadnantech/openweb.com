<!-- END CONTAINER -->
<!-- BEGIN CORE JS FRAMEWORK-->
<script src="<?php echo base_url('assets/plugins/pace/pace.min.js')?>" type="text/javascript"></script>
<!-- BEGIN JS DEPENDECENCIES-->
<script src="<?php echo base_url('assets/plugins/jquery/jquery-1.11.3.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/bootstrapv3/js/bootstrap.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-block-ui/jqueryblockui.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-unveil/jquery.unveil.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-autonumeric/autoNumeric.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-validation/js/jquery.validate.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-select2/select2.min.js')?>" type="text/javascript"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- END CORE JS DEPENDECENCIES-->
<!-- BEGIN CORE TEMPLATE JS -->
<script src="<?php echo base_url('webarch/js/webarch.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/chat.js')?>" type="text/javascript"></script>
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-sparkline/jquery-sparkline.js')?>"></script>
<script src="<?php echo base_url('assets/plugins/skycons/skycons.js')?>"></script>
<script src="<?php echo base_url('assets/plugins/owl-carousel/owl.carousel.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/Mapplic/js/jquery.easing.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/Mapplic/js/jquery.mousewheel.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/Mapplic/js/hammer.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/Mapplic/mapplic/mapplic.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-flot/jquery.flot.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-metrojs/MetroJs.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/jquery-inputmask/jquery.inputmask.min.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/boostrap-form-wizard/js/jquery.bootstrap.wizard.min.js') ?>" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN CORE TEMPLATE JS -->

<script src="<?php echo base_url('assets/js/form_validations.js')?>" type="text/javascript"></script>

<?php
if(isset($aditional_scripts)) {
    foreach ($aditional_scripts as $script) { ?>
        <script src="<?php echo base_url($script) ?>" type="text/javascript"></script>
    <?php }
}?>

<script>
$(document).ready(function(){
    $("#divResults").hide();
$('.telkom_request_stat_btn').on('click',function(){
var request_code = $(this).attr('data-order-id');
var order_type = $(this).attr('data-order-type');
var orderusername = $(this).attr('data-username');
var simnumber = $(this).attr('data-simnumber');
var lte_username = $(this).attr('data-lte_username');
var network = $(this).attr('data-network');
//console.log(request_code + '  ' + order_type + '  '+ orderusername +'   '+ simnumber +'   '+ lte_username +'   '+ network)
$.ajax({
            type: "POST",
            url: "/user/request_telkom_stat",
            data: {request_code:request_code,order_type:order_type,order_username:orderusername,simnumber:simnumber,lte_username:lte_username,network:network},
            dataType: "json",
            success: function (response) {
                //alert(response);
                $("#divResults-stat").show();
                $("#divResults-stat").empty().append(response.msg);
            }
        });
        
        
});
});
/*
telkom topup form feeder.ES
*/
</script>

</body>
</html>