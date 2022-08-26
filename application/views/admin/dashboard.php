<h3>Dashboard</h3>
<div style="float:right">
<?php if(is_button_accessable('ON-OFF LTE Stats Request Mail','Dashboard',$user_role)):?> 
<h6><strong>ON / OFF LTE Stats Request Mail</strong></h6>    
<input id="toggle-event" type="checkbox" data-toggle="toggle">
<?php endif;?>

</div>
<?php 
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
	<?php
}
if (isset($messages['warn_message']) && trim($messages['warn_message']) != '') {
	?>
	<div class="alert alert-danger">
		<?php echo $messages['warn_message'] ?>
	</div>
	<?php
}
?>
<div class="alert alert-success" id="notice" style="display: none;">The email have been send successfully.</div>
<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
<?php if(isset($user_list)){?>	
	<?php echo form_open('admin/send_invoices_individual', array('class' => 'form-horizontal','role' => 'form', 'id'=>'send-form')); ?>
		  <?php if(is_button_accessable('Send Invoices','Dashboard',$user_role)):?>
		<label class="col-lg-2">Select User</label>
		<div class="col-lg-4">
            <input type="text" class="form-control" name="user" id="user_auto"/>
		</div>
		<div class="col-lg-2">
			<input type="submit" class="btn btn-default" value="Send Invoices" style="height: 35px;" >
		</div>
		<?php endif;?>
	<?php echo form_close();?>
<?php }?>
	<div class="col-lg-4">
		<?php //$date = date('M Y',strtotime(date('Y',time()).'-'.(date('m',time())+1))); // TODO: BUGfix
                $date = date('M Y',strtotime('+1 month'));
        ?>
        <?php //var_dump($month_invs_log); ?>
		<?php if(empty($month_invs_log)){ ?>
		<input type="button" class="btn btn-default" disabled="disabled" value="Generate Invoices for <?php echo $date?>" style="height: 35px;" id="create_all_invoice" data-loading-text="Creating...">
		<input type="button" class="btn btn-success" disabled="disabled" value="Email Invoices to All Clients" style="height: 35px;display: none;" id="send_all_invoice" data-loading-text="Sending...">
		<div class='alert alert-info' style="display: none;">This month's invoice has been sent to the user</div>
		<?php }else{ 
				if($month_invs_log && $month_invs_log['create_status'] == 0){
					echo '<input type="button" class="btn btn-default" value="Generate Invoices for '.$date.'" style="height: 35px;" id="create_all_invoice" data-loading-text="Creating...">';
				}elseif ($month_invs_log && $month_invs_log['create_status'] == 1 && $month_invs_log['send_email_status'] == 0){
					echo '<input type="button" class="btn btn-success"  disabled="disabled" value="Email Invoices to All Clients" style="height: 35px;" id="send_all_invoice" data-loading-text="Sending...">';
				}else{
					echo "<div class='alert alert-info'>This month's invoice has been sent to the user</div>";
				}
		 }?>
	</div>
</div>
<div id="invoice-buttons">
    <?php
        $create_monthly_title = "Create monthly invoices ";
        $month = date('M/Y', strtotime('+1 month'));
        $create_monthly_title .= " ( " . $month . " ) ";
        $create_button_class = 'btn-default';

        if (!empty($month_invs_log) && ($month_invs_log['create_status'] == 1)){
            $create_monthly_title = "Check / Update montly invocies";
            $create_button_class = 'btn-success';
            $create_monthly_title .= " ( " . $month_invs_log['month_invoice'] . " ) ";
        }


        $send_disabled = '';
        $email_monthly_title = "Send invoices via email ( " . $month . " ) ";
        $send_button_class = 'btn-default';
        if (!empty($month_invs_log) && ($month_invs_log['send_email_status'] == 1)){
            $email_monthly_title =  "Invoices was sent ( " . $month . " ) ";
            $send_button_class = 'btn-success';
            $send_disabled = "disabled='disabled'";
        }



    ?>
    <?php if(is_button_accessable('Check-Update Monthly Invoice','Dashboard',$user_role)):?>
    <a href="<?php echo base_url(); ?>admin/create_next_invoice_page" class="btn <?php echo $create_button_class; ?>"><?php echo  $create_monthly_title; ?></a>
    <?php endif;?>
     <?php if(is_button_accessable('Invoice was Sent','Dashboard',$user_role)):?>
    <a href="<?php echo base_url(); ?>admin/send_invoices_page" class="btn <?php echo  $send_button_class; ?>" <?php echo $send_disabled; ?>><?php echo  $email_monthly_title ; ?></a>
     <?php endif;?>
      <?php if(is_button_accessable('Send LTE Usage stats','Dashboard',$user_role)):?>
    <a href="#" id="send_lte_usage_stats" class="btn btn-default">Send LTE Usage Stats</a>
     <img id="spinner" src="/assets/img/Spinner-1s-200px.gif" style="height: 25px; display: none;">
     <?php endif;?>
   

</div>
<br/>  <br/>

<div class="progress progress-striped active"  style="display: none;" id="bar_content">
    <div id="progress_bar" class="progress-bar"  role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" >
        <span class="sr-only">Loading...</span>
    </div>
</div>

<?php if (isset($notifications) && !empty($notifications)) { ?>
	<div class="lead">Your Notifications</div>
	<?php foreach ($notifications as $note) { ?>
		<div class="alert alert-<?php echo $note['type'] ?>">
		<?php echo $note['content'] ?>
		<div class="text-muted pull-right">
			On <?php echo date('D, M j Y', strtotime($note['date_created'])) ?>
		</div>
		</div>
	<?php
		}
     } else {
	?>
	<div class="lead">Welcome to your Home Client dashboard. You have no new notifications.</div>
<?php } ?>
<div id="month_div"></div>

<script>
 var progress_bar = $('#progress_bar'),
 progress_span = $(".sr-only");

 progress_bar.progressbar({
   value: false,
   change: function() {
	   progress_bar.css({"width": progress_bar.progressbar( "value" ) + "%"});
	   progress_span.text( progress_bar.progressbar( "value" ) + "%" );
   },
 });

 function progres_s() {
   var val = progress_bar.progressbar( "value" ) || 0;
   progress_bar.progressbar( "value", val + 1 );

   if ( val < 99 ) {
     setTimeout( progres_s, 100 );
   }
 }

$('#create_all_invoice').click(function(){
	$(this).button('loading');
	$.ajax({
	   type: "POST",
	   url: "<?php echo base_url('admin/create_next_invoice')?>",
	   dataType: "json",
	   beforeSend :function(){
		  // $('#bar_content').show();
		  // setTimeout(progres_s, 3000 );
		   $('#month_div').html("<div align='center'><img src='<?php echo base_url().'img/loading.gif';?>'></div>");
	   },
	   success : function(ret){
		   $('#month_div').hide();
		   $('#create_all_invoice').hide();
	       $('#send_all_invoice').show();
		   //progress_bar.progressbar({
		   //   complete: function() {
			//	   progress_span.text( "Complete!" );
			 //  }
		   //});
	   },
	 });
});

$('#send_all_invoice').click(function(){
	$('#bar_content').hide();
	$(this).button('loading');
	$.ajax({
	   type: "POST",
	   url: "<?php echo base_url('admin/send_invoices')?>",
	   dataType: "json", 
	   beforeSend :function(){
		   $('#month_div').html("<div align='center'><img src='<?php echo base_url().'img/loading.gif';?>'></div>");
	   },
	   success : function(ret){
		   $('#create_all_invoice').hide();
	       $('#send_all_invoice').hide();
	       $('#month_div').hide();
	       $('#notice').show();
	   },
   });
});

var users = <?php echo json_encode($user_list) ?>;
var userlist = [];
var user;
$.each(users, function (key, val) {
    userlist.push({
        value: val,
        data: key
    });
});

 $('#user_auto').autocomplete({
     source: userlist,
     select: function (event, suggestion) {
         user = suggestion.item.data;
     }
 });
 $('#send-form').submit(function (event) {
     $('#user_auto').val(user);
     return true;
 });

 $(document).on('click', '#send_lte_usage_stats', function(){
     $('#spinner').show();
     alert('This may take some time. Please wait.');
     $.ajax({
         url: '<?php echo base_url(); ?>/user/daily_usage_cron',
         success: function () {
             $('#spinner').hide();
             alert('LTE Usage Stats sent successfully.');
         }
     });
 });
</script>