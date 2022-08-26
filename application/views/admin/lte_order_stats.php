<h3>LTE Account Stats</h3>
<?php if(empty($lte_stats_data[0]['mtn_status']) && $lte_stats_data[0]['telkom_status'] !='REQUESTED'
                || empty($lte_stats_data[0]['telkom_status']) && $lte_stats_data[0]['mtn_status'] !='REQUESTED' || count($lte_stats_data) <=0)  :?>
                
  <button type="button" class="telkom_request_stat_btn btn btn-sm btn-primary" style="margin-left:4px;float:right;" data-order-id ="<?= $stats_btn['order_id'] ?>" 
 data-order-type ="<?= $stats_btn['order_type'] ?>" data-username="<?= $stats_btn['username'] ?>   "/>Request Stats on Behalf of User</button>
 <p style="float:right;color:green;" id="divResults-stat"></p>
 <?php else:?>
 <button type="button" class="btn btn-sm btn-default" disabled style="margin-left:4px;float:right;"/>Stats Requested</button>
 <?php endif?>
 <br/><br/><hr>              
<?php if($stats_btn['order_type'] =='telkom'):?>
<?php if(count($lte_stats_data) >0):?>
<?php if($lte_stats_data[0]['telkom_status'] =='MAILED'): ?>
<div class="panel panel-default">
  <div class="panel-heading">Username: <?= $lte_stats_data[0]['username']?> <span style="float:right;">Status updated By Admin: <?= $lte_stats_data[0]['admin_add_status_date']?></span>
  </div>
  <div class="panel-body">
    <h4><strong>Total Cap :<?= $lte_stats_data[0]['telkom_total_cap']?></strong></h4>  
    <h4><strong>Anytime Cap Used :<?= $lte_stats_data[0]['telkom_time_cap']?></strong></h4>  
    <h4><strong>Night Cap Used:<?= $lte_stats_data[0]['telkom_night_cap']?></strong></h4>  
  </div>
    <div class="panel-footer">Stats request generated on : <?= $lte_stats_data[0]['telkome_request_date']?></div>
</div>
<?php else :?>
<h4 style="color:green" class="boom-message">The user has requested their stats - our systems are waiting for the mobile networks to update the latest Stats upon which this user will receive an email.</h4>
<?endif?>
<?php else :?>
<h4 style="color:green" class="boom-message">This user has not yet requested their Usage Stats.</h4>
<?endif?>
<?php else : //MTN PART?>

<?php if(count($lte_stats_data) >0):?>
<?php if($lte_stats_data[0]['mtn_status'] == 'MAILED'): ?>
<div class="panel panel-default">
  <div class="panel-heading">Username: <?= $lte_stats_data[0]['username']?> <span style="float:right;">Status updated By Admin: <?= $lte_stats_data[0]['mtn_admin_add_status_date']?></span>
  </div>
  <div class="panel-body">
    <h4><strong>Total Cap :<?= $lte_stats_data[0]['mtn_total_cap']?></strong></h4>  
    <h4><strong>Anytime Cap Used :<?= $lte_stats_data[0]['mtn_time_cap']?></strong></h4>  
    <h4><strong>Night Cap Used:<?= $lte_stats_data[0]['mtn_night_cap']?></strong></h4>  
  </div>
    <div class="panel-footer">Stats request generated on : <?= $lte_stats_data[0]['mtn_request_date']?></div>
</div>
<?php else :?>
<h4 style="color:green" class="boom-message">The user has requested their stats - our systems are waiting for the mobile networks to update the latest Stats upon which this user will receive an email.</h4>
<?endif?>
<?php else :?>
<h4 style="color:green" class="boom-message">This user has not yet requested their Usage Stats.</h4>
<?endif?>
<?endif?>
<script>
$(document).ready(function(){
     $("#divResults").hide();
$('.telkom_request_stat_btn').on('click',function(){
var request_code = $(this).attr('data-order-id');
var order_type = $(this).attr('data-order-type');
var orderusername = $(this).attr('data-username');
$.ajax({
            type: "POST",
            url: "/user/request_telkom_stat",
            data: {request_code:request_code,order_type:order_type,order_username:orderusername},
            dataType: "json",
            success: function (response) {
                //alert(response);
                $("#divResults-stat").show();
                $("#divResults-stat").empty().append(response.msg);
                $(".boom-message").hide();
            }
        });
        
        
});
});
/*
telkom topup form feeder.ES
*/
</script>