<script language="javascript" type="text/javascript">
$(document).ready(function() {	
	$("#change_pwd_form").validate({
	       rules: {		      
	    	   account_password: {
		    	  required : true,
		    	  minlength: 4,
		    	  maxlength: 32,  
			  },

    	}
    }); 
});
</script>
<h3>Order Detail</h3>
<br/>
<br/>
<?php
if(isset($error_message) && $error_message !=''){
	echo "<div class='alert alert-error'>$error_message</div>";
}
?>
<!--Current Service Info  -->
<fieldset>
<legend>Current Service Information</legend>
<?php 
if(isset($modify_service) && !empty($modify_service)){
	if(isset($modfiy_product) && !empty($modfiy_product)){
		$modfiy_product = $modfiy_product;
	}else{
		$modfiy_product = '';
	}
	if($modify_service =='Upgrading'){
		echo "<div class='alert alert-error' style='font-size:15px;'>This service will be upgraded to ".$modfiy_product." next month.</div>";
	}elseif ($modify_service =='Downgrading'){
		echo "<div class='alert alert-error' style='font-size:15px;'>This service will be downgraded to ".$modfiy_product." next month.</div>";
	}
}
 
if(isset($current_service)){
	$service = $current_service;
	
	echo form_open('user/edit_active_cloudsl', array('class' => 'form-horizontal'));
?>
	<input type="hidden" name="order_id" value="<?php echo $order_id ?>" id="order_id"/>
	<div class="form-group">
		<!-- <label class="control-label col-lg-2">Current Service</label> -->
		<div class="col-lg-7">
			<input class="form-control" type="text" value="<?php echo $service;?>" disabled="disabled">			
		</div>
		<?php 
			if(isset($order_status) && $order_status == 'active'){
		?>
		<div class="col-lg-2">
			<input class="btn btn-sm btn-primary" type="submit" value="Modify Service">
		</div>
		<?php } ?>
	</div>	
<?php 	
	echo form_close();
}
?>
</fieldset>

<!--change service pwd  -->
<fieldset>
<legend>Change Service Password</legend>
<?php 
if(isset($change_flag) && $change_flag == 1){
	echo form_open('user/update_order', array('class'=>'form-horizontal','id'=>'change_pwd_form')); ?>
<input type="hidden" value="<?php echo $order_id;?>" name= "id"/>
<div class="form-group">
	<label class="form-label col-lg-2">New Password</label>
	<div class="col-lg-5">
		<input type="text" class="form-control" name="account_password" id="account_password" placeholder="Current Account Password : <?php echo $acc_pwd;?>"/>
	</div>
	<div class="col-lg-5">
		<input type="submit" class="btn btn-sm btn-primary" value="Update Service Password" />
	</div>
</div>
<?php 
	echo form_close();
}else{
?>
<div class="alert alert-warning">
	<!-- <strong>You don't have a permission to modify your service password.</strong> -->
    <strong>This information is not available on the product you have chosen at this time.</strong>

</div>
<?php  }?>
</fieldset>
<?php 
if(isset($display_usage) && $display_usage == 1){
?>	

<!--session Info  -->
<fieldset>
<legend>Session Information</legend>
<?php 
if(isset($session_error_message) && $session_error_message !=''){
	echo "<div class='alert alert-error'>$session_error_message</div>";
}else{
	if(isset($session_data)){
?>
	<?php 
		$tmpl = array ( 'table_open'  => '<table class="table">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading(array( 'Username', 'Login Time', 'Session Length(hh:mm:ss)', 'Session IP Address', 'Megabytes Sent', 'Megabytes Received', 'Total Megabytes'));
		
		$username = $session_data['Username'];
		$login_time = $session_data['LoginTime'];
		$sess_length = $session_data['SessionLength'];
		$IP = $session_data['SessionIPAddress'];
		$sent = $session_data['MegabytesSent'];
		$received = $session_data['MegabytesReceived'];
		$total = $session_data['Total'];
		
		$this->table->add_row( array($username, $login_time, $sess_length, $IP, $sent, $received, $total));
		echo $this->table->generate();	
	?>
	
<?php 
	}else{
		echo "<div class='alert alert-info'>There are no current sessions at the moment.</div>";
	}
}
?>
</fieldset>
<br/>

<!--Today Info  -->
<fieldset>
<legend id="today_title" style="cursor: pointer;">Today Usage Data</legend>
<?php 
if(isset($today_error_message) && $today_error_message !=''){
	echo "<div class='alert alert-error'>$today_error_message</div>";
}else{
	if(isset($today_stats_data) && !empty($today_stats_data)){
?>
	<div id="today_div">
	<?php 
		$tmpl = array ( 'table_open'  => '<table class="table">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading(array('Start Time', 'Stop Time','Session Length', 'Megabytes Sent', 'Megabytes Received', 'Total Megabytes', 'Disconnect Reason'));
		
		foreach ($today_stats_data as $today => $t){
			$sent = round($t['BytesSent']/1000000,2);
			$received = round($t['BytesReceived']/1000000,2);
			$total = round($t['TotalUsageBytes']/1000000,2);
			$ESR = $t['ESR'];
			$session_id = $t['SessionID'];
			$start_time = $t['StartTime'];
			$stop_time = $t['StopTime'];
			$sess_length = date('h:i:s', $t['SessionLength']);
			$date_rate = $t['DataRate'];
			$call_id = $t['CallingStationID'];
			$session_ip = $t['SessionIP'];		
			$dis_rea = $t['DisconnectReason'];
			
			$this->table->add_row( array($start_time, $stop_time, $sess_length, $sent, $received, $total, $dis_rea));
		}
		echo $this->table->generate();
	?>
	</div>
<?php 
	}else{
		echo "<div class='alert alert-info'>There are no usage data today.</div>";
	}
}
?>
</fieldset>
<br>

<!--yearly Info  -->
<fieldset>
	<legend id="year_title" style="cursor: pointer;">Year Usage Data</legend>
<?php  
if(isset($year_error_message) && $year_error_message !=''){
	echo "<div class='alert alert-error'>$year_error_message</div>";
}else{
?>
	<?php 
		if(isset($year_stats_data) && !empty($year_stats_data)){
	?>
	<div id="year_div">
	<table class="table" frame=void>
	<tr>
		<th>YearMonth</th>
		<th>Total Time Connected</th>
		<th>Megabytes Sent</th>
		<th>Megabytes Received</th>
		<th>Total Megabytes</th>
	</tr> 
	<?php 
		foreach ($year_stats_data as $year => $y){
			$year = $y['YearMonth'];
			$time = date('h:i:s',$y['TotalTimeConnected']) ;
			$sent = round($y['BytesSent']/1000000,2);
			$received = round($y['BytesReceived']/1000000,2);
			$total = round($y['TotalUsageBytes']/1000000,2);
			
			$time = date('Y-m',strtotime($year));
			echo "<tr id='".$time."'  onclick='showMonth(this.id)'  style='cursor: pointer;' class='tr_ckick'><td>".$year."</td>";
			echo "<td>".$time."</td>";
			echo "<td>".$sent."</td>";
			echo "<td>".$received."</td>";
			echo "<td>".$total."</td>";
			echo "</tr>";		
		}
	?>
	</table> 
	<div onclick="graphEffect();" style="cursor: pointer; color: #428bca;">Click Here to view/hide the Usage Trend Chart</div>
	<div id="year_container" style="min-width: 870px; height: 500px; margin: 0 auto; display: none;"></div>
	<script type="text/javascript">
	function showMonth(row){
	
		$("#month_div").focus();
		$('#month_usage').show();
		$('#this_month_div').hide();
		
		var url = "<?php echo base_url('user/get_month_usage'); ?>";
		var order_id = "<?php echo $order_id; ?>";
		var list = "";
		$.ajax({
			   type: "POST",
			   url: url,
			   data: {  
	   				'date'   : row,
	   				'order_id'   : order_id,
			   },
			   dataType: "json",
			   beforeSend :function(){
				   $('#month_div').html("<div  align='center'><img src='<?php echo base_url();?>img/loading.gif'></div>");
			   },
			   success : function(data){ 
					list += "<table class='table' frame=void>";
					list += "<tr><th>Month</th><th>Total Time Connected</th><th>Megabytes Sent</th><th>Megabytes Received</th><th>Total Megabytes</th></tr>";
					for(var i=0; i < data.month_stats_data.length; i++){
						var date = data.month_stats_data[i]['Date'];
						var total_time = data.month_stats_data[i]['TotalTimeConnected'];
						var sent = data.month_stats_data[i]['BytesSent'];
						var received = data.month_stats_data[i]['BytesReceived'];
						var total = data.month_stats_data[i]['TotalUsageBytes'];
						list += "<tr><td>"+date+"</td>";
						list += "<td>"+total_time+"</td>";
						list += "<td>"+sent+"</td>";
						list += "<td>"+received+"</td>";
						list += "<td>"+total+"</td></tr>";
					}
					
					list += "</table>";
					list += "<div onclick='graphEffect_2();' style='cursor: pointer; color: #428bca;'>Click Here to view/hide the Usage Trend Chart</div>";
					$('#month_div').html("<div>"+list+"</div>");
					
					$('#month_container').highcharts({
			            title: {
			                text: 'Usage Data This Month'
			            },
			            subtitle: {
			                text: ''
			            },
			            xAxis: {
			                categories : data.day,
			            },
			            yAxis: {
			                title: {
			                    text: 'Megabytes(MB)'
			                },
			                labels: {
			                    formatter: function() {
			                        return this.value/1000000;                      
			                    }
			                }
			            },
			            tooltip: {
			                pointFormat: '{series.name} produced <b>{point.y:,.0f}</b><br/>warheads in {point.x}'
			            },
			            series: [{
			                name: 'Megabytes Sent',
			                data: arrayParseInt(data.sent),
			            },{
			                name: 'Megabytes Received',
			                data: arrayParseInt(data.received),
			            },{
			                name: 'Total Megabytes Sent and Received',
			                data: arrayParseInt(data.total),
			            }]
			        });
			   }
		});
	}
	
		
	$(function () {
		$(function () {
			$('.tr_ckick').mouseover(function(){
				$(this).css('background-color',' #BCE8F1');
			});
	
			$('.tr_ckick').mouseout(function(){
				$(this).css('background-color','');
			});
			
	        $('#year_container').highcharts({
	            title: {
	                text: 'Usage Data This Year'
	            },
	            subtitle: {
	                text: ''
	            },
	            xAxis: {
	                categories : [<?php  foreach ($year_stats_data as $year => $y){
	        								$day = date('M',strtotime($y['YearMonth']));             						
	        								echo '"'.$day.'",';
	        		  					}
	   		  					 ?>],
	            },
	            yAxis: {
	                title: {
	                    text: 'Megabytes(MB)'
	                },
	                labels: {
	                    formatter: function() {
	                        return this.value/1000000;                      
	                    }
	                }
	            },
	            tooltip: {
	                pointFormat: '{series.name} produced <b>{point.y:,.0f}</b><br/>warheads in {point.x}'
	            },
	            series: [{
	                name: 'Megabytes Sent',
	                data: [<?php  foreach ($year_stats_data as $year => $y){
		                			$sent = $y['BytesSent'];
		                			echo $sent.',';
		                		  }
	               		  ?>]
	            },{
	                name: 'Megabytes Received',
	                data: [<?php foreach ($year_stats_data as $year => $y){
	                			$received = $y['BytesReceived'];
	                			echo $received.',';
	                		}
	            	  ?>]
	            },{
	                name: 'Total Megabytes Sent and Received',
	                data: [<?php foreach ($year_stats_data as $year => $y){
	                			$total = $y['TotalUsageBytes'];
	                			echo $total.',';
	                		}
	                  ?>]
	            }]
	        });
		 });
	});	
	
	</script>
	</div>
	<?php }else{ 
			echo "<div class='alert alert-info'>There are no usage data this year.</div>";
		  }
}
?>
	</fieldset>
<br>

<!--monthly Info  -->
<div style="display: none;" id="month_usage">
<fieldset>
<legend id="month_title" style="cursor: pointer;">Month Usage Data</legend>
<div id="month_div"></div>	
<div id="month_container" style="min-width: 870px; height: 500px; margin: 0 auto; display: none;"></div>
</fieldset>
</div>

	<!--This month useage  -->
	<div id="this_month_div">
		<fieldset>
		<legend id="this_month_title" style="cursor: pointer;">Month Usage Data</legend>
	<?php  
	if(isset($month_error_message) && $month_error_message !=''){
		echo "<div class='alert alert-error'>$month_error_message</div>";
	}else{ ?>	
		<?php 
			if(isset($month_stats_data) && !empty($month_stats_data)){
		?>
		<div id="this_month_usage">
		<?php 
			$tmpl = array ( 'table_open'  => '<table class="table">' );
			$this->table->set_template($tmpl);
			$this->table->set_heading(array( 'Month', 'Total Time Connected', 'Megabytes Sent', 'Megabytes Received', 'Total Megabytes'));
		
			foreach ($month_stats_data as $month => $m){
				$date = date('Y-m-d', strtotime($m['Date']));
				$time = date('h:i:s', $m['TotalTimeConnected']);
				$sent = round($m['BytesSent']/1000000,2);
				$received = round($m['BytesReceived']/1000000,2);
				$total = round($m['TotalUsageBytes']/1000000,2);
				
				$this->table->add_row( array($date, $time, $sent, $received, $total));
			}	
			echo $this->table->generate();
		
		?>
		<div onclick="graphEffect_3();" style="cursor: pointer; color: #428bca;">Click Here to view/hide the Usage Trend Chart</div>
		<div id="this_month_container" style="min-width: 870px; height: 500px; margin: 0 auto;display: none;"></div>
		</div>
		
		<script type="text/javascript">
		$(function(){
			$(function () {
		        $('#this_month_container').highcharts({
		            title: {
		                text: 'Usage Data This Month'
		            },
		            subtitle: {
		                text: ''
		            },
		            xAxis: {
		                categories : [<?php  foreach ($month_stats_data as $month => $m){
		        								$day = date('d',strtotime($m['Date']));             						
		        								echo $day.',';
		        		  					}
		   		  					 ?>],
		            },
		            yAxis: {
		                title: {
		                    text: 'Megabytes(MB)'
		                },
		                labels: {
		                    formatter: function() {
		                        return this.value/1000000;                      
		                    }
		                }
		            },
		            tooltip: {
		                pointFormat: '{series.name} produced <b>{point.y:,.0f}</b><br/>warheads in {point.x}'
		            },
		            series: [{
		                name: 'Megabytes Sent',
		                data: [<?php  foreach ($month_stats_data as $month => $m){
			                			$sent = $m['BytesSent'];
			                			echo $sent.',';
			                		  }
		               		  ?>]
		            },{
		                name: 'Megabytes Received',
		                data: [<?php foreach ($month_stats_data as $month => $m){
			                			$received = $m['BytesReceived'];
			                			echo $received.',';
			                		}
			            	  ?>]
		            },{
		                name: 'Total Megabytes Sent and Received',
		                data: [<?php foreach ($month_stats_data as $month => $m){
			                			$total = $m['TotalUsageBytes'];
			                			echo $total.',';
			                		}
			                  ?>]
		            }]
		        });
		    });	
		});
		</script>
		<?php }else{
				echo "<div class='alert alert-info'>There are no usage data this month.</div>";
			  }
	}	   
	?>
		</fieldset>
	</div>

<script type="text/javascript">
$(function(){
	$('#today_div').hide();
	$('#this_month_usage').hide();
	$('#year_div').hide();
	
	$('#this_month_title').click(function(){
		$('#this_month_usage').toggle();
	});
	
	$('#today_title').click(function(){
		$('#today_div').toggle();
	});

	$('#month_title').click(function(){
		$('#month_div').toggle();
	});

	$('#year_title').click(function(){
		$('#year_div').toggle();
	});
});

function arrayParseInt(arr){
	for(var i=0;i<arr.length;i++)
	{
	    arr[i] = parseInt(arr[i]);
	}
	return arr;
}

function graphEffect(){
	$('#year_container').toggle('slow');
	$("#year_container").focus();
}

function graphEffect_2(){
	$('#month_container').toggle('slow');
	$("#month_container").focus();
}

function graphEffect_3(){
	$('#this_month_container').toggle('slow');
	$("#this_month_container").focus();
}
</script>
<?php 
}else{
?>
<fieldset>
<legend>Usage Stats</legend>
<div class="alert alert-warning">
	<!-- <strong>You don't have a permission to view your usage stat.</strong> -->
    <strong>This information is not available on the product you have chosen at this time.</strong>
</div>
</fieldset>
<?php }?>