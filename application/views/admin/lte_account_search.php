<?php 
  // get the lte account info by usernames
      $APIuri="https://www.isdsl.net/api/rest/lte/usernameInfo.php?usernames=All";

      /* These are the lyrics to Hello Dolly- */
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL =>$APIuri,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
      ),
    ));
    $response = curl_exec($curl);
    $data=json_decode($response, true);
    
    $usernames = array();
    foreach($data['data'] as $key=>$val){
       $usernames[] = $key;
    } 
    $usernames = json_encode($usernames);
?>
<h3>Search for an IS LTE Account (Cell C and MTN Fixed)</h3>
<style>
    .modal-dialog{
      width:800px !important;
    }
    form.user_comment_search {
        clear: both;
        max-width: 100%;
        width: 100%;
        float: right;
    }
    form.user_comment_search input.user-success {
        width: 78%;
        margin-right: 10px;
    }
    .user_comment_search input.form-control {
        max-width: 75%;
        margin-right: 2%;
    }
    .user_comment_search input#filter_by_user_cmmnts {
        background: #428bca;
        color: #fff;
        border-color: #428bca;
    }
</style>
<fieldset>
<?php echo form_open('admin/usernameinfo', array('class' => 'form-horizontal','id'=> 'lte_account_search')); ?> <!-- <form id="lte_account_search"> -->    
<div class="form-group">
    <label class="control-label col-lg-2">Usernames:</label>
    <div class="col-lg-4">
        <div class="autocomplete" >
        <input type="text" class="form-control" placeholder="Username1,Username2,...All" name="usernames" id="usernames" value=""/>
        </div>
    </div>
    <div class="col-lg-1" style="display:flex;">
		<input type="submit" name="submit" id="lte_account_search" class="btn btn-sm btn-primary" value="Search"/>
		<?php $image = base_url().'img/loading.gif'; ?>
        <img id="loading-image" src="<?php echo $image; ?>" style="display:none;width: 30px;height: 30px;margin-left: 10px;"/>
	</div>
</div>
<?php echo form_close();?>

</fieldset>

<hr/>
<fieldset>
    <div class="lte_accnt_informations">
     <?php 
     
            if (!empty($items)) {
                $actn = base_url() . 'admin/usercommentsinfo';
                $lteaccntinfo.="<div class='pull-right'><div>$showing</div>";
                $lteaccntinfo.='<form class="form-inline user_comment_search" action="'.$actn.'" method="post">';
                $lteaccntinfo.='<input class="form-control" type="text" name="search" value="" placeholder="Search...">';
                $lteaccntinfo.='<input class="btn btn-default" id="filter_by_user_cmmnts" type="submit" name="filter" value="Go">';
                $lteaccntinfo.='</form></div>';
                $tmpl = array ( 'table_open'  => '<table class="table">' );
                $lteaccntinfo.=$this->table->set_template($tmpl);
                $lteaccntinfo.=$this->table->set_heading(array('Username', 'Status', 'Class','Password' ,'User Comment', 'System Comment', 'Email Address','Action'));
                
                $i = 0;
               // print_r($items);exit();
                foreach ($items as $key=>$accnt) {
                    
                $i=$i+1;
                $username = !empty( $accnt['username'] ) ? $accnt['username'] : 'N/A';
                
                // api call for currentSessions
                $APIuri1="https://www.isdsl.net/api/rest/lte/currentSessions.php?usernames=".$username;
                /* These are the lyrics to Hello Dolly */
                $curl1 = curl_init();
                curl_setopt_array($curl1, array(
                CURLOPT_URL =>$APIuri1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, false),
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
                ),
                ));
                $response1 = curl_exec($curl1);
                $data1=json_decode($response1, true);
                
                $status = $accnt['status'];
                $class = $accnt['class'];
                $password = $accnt['password'];
                $user_comment = !empty( $accnt['user_comment'] ) ? $accnt['user_comment'] : 'N/A';
                $system_comment = !empty( $accnt['system_comment'] ) ? $accnt['system_comment'] : 'N/A';
                $email_address = !empty( $accnt['email_address'] ) ? $accnt['email_address'] : 'N/A';
                $stats1 = '<div style="display:flex"><button type="button" class="btn btn-info btn-sm statsBtnClick" data-toggle="modal" data-target="#myModal'.$i.'">Stats</button>'.'<button type="button" class="btn btn-warning btn-sm myuserStatusBtn" data-toggle="modal"  data-target="#myuserStatusModel" data-session-user="'.$username.'">Info</button><button type="button" class="btn btn-danger btn-sm deleteUserBtn" data-toggle="modal"  data-target="#deleteAccountModel" data-session-user="'.$username.'">Delete Account</button><button type="button" class="btn btn-success btn-sm restoreUserBtn" data-toggle="modal"  data-target="#restoreAccountModel" data-session-user="'.$username.'">Restore Account</button><div>';
                $stats2 .= '<div class="modal fade '.$username.'" id="myModal'.$i.'" role="dialog"><div class="modal-dialog model-lg" style="width:1250px;"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">User Stats</h4></div>';
                $stats2 .= '<div class="modal-body"><h4>Current active sessions for user '.$username.'</h4><p>';
                
                
                if(!empty($data1['data'])){
                foreach($data1['data'] as $key1=> $val1){
                    $connect_time = $val1['connect_time'];
                    $session_id = $val1['session_id'];
                    $subscriber_ip = $val1['subscriber_ip'];
                    $UserName = $val1['UserName'];
                    $MSISDSN = $val1['MSISDSN'];
                    $stats2.= '<ul><li>Connect Time : '.$connect_time.'</li><li>Session ID : '.$session_id.'</li><li>Subscriber IP : '.$subscriber_ip.'</li><li>Username : '.$UserName.'</li><li>MSISDSN : '.$MSISDSN.'</li></ul></p>';
                }
                }else{
                     $stats2.= '<div class="alert alert-warning"><strong>Data not found.</strong></div>';
                 }
                // api call for currentSessions                 
                
                // api call for month Usage
                $stats2.= '<h4>Month Usage</h4><p>';
                $stats2 .= '<input type="month" id="date_month_usage" class="form-control" name="date_month_usage" onchange="handler2(event);" >';
                $stats2 .='<div class="month_usage"></div>';
                // api call for month usage
                
                // api call for monthSummary
                $stats2.= '<h4>Month Summary</h4><p>';
                $stats2 .= '<input type="month" id="date_month_summary" class="form-control" name="date_month_summary" onchange="handler(event);"><input type="hidden" id="current_user" name="current_user" value="'.$username.'">';
                $stats2 .='<div class="month_summary"></div>';                
                // api call for monthSummary
                
                // api call for dayUsage
                $stats2.= '<h4>Day Usage</h4><p>';
                $stats2 .= '<input type="date" id="date_day_usage" class="form-control" name="date_day_usage" onchange="handler1(event);">';
                $stats2 .='<div class="day_usage"></div>';                
                // api call for dayUsage                
                
                // api call for account map
                $APIuri4="https://www.isdsl.net/api/rest/lte/accountMap.php?username=".$username;
                /* These are the lyrics to Hello Dolly */
                $curl4 = curl_init();
                curl_setopt_array($curl4, array(
                CURLOPT_URL =>$APIuri4,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                curl_setopt($curl4, CURLOPT_SSL_VERIFYPEER, false),
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization:Basic YXBpQG9wZW53ZWJtb2JpbGUuY28uemE6b2MzSlJreVE3cT09MTIzLQ==",
                ),
                ));
                $response4 = curl_exec($curl4);
                $data4=json_decode($response4, true);
                
                $stats2.= '<h4>Account Map</h4><p>'; 
                 
                if(!empty($data4['data'])){
                     $data4 = $data4['data'];
                     $stats2.= '<ul><li>WBS_LTE_MSISDSN" : '.$data4['WBS_LTE_MSISDSN'].'</li><li>WBS_LTE_ROUTER_SERIAL : '.$data4['WBS_LTE_ROUTER_SERIAL'].'</li><li>WBS_LTE_SIM : '.$data4['WBS_LTE_SIM'].'</li></ul></p>';                    
                }else{
                 $stats2.= '<div class="alert alert-warning"><strong>Data not found.</strong></div>';
                } 
                 $stats2.= '<h4>Topup Account</h4><p>';
                 $stats2.='<button type="button" class="btn btn-primary btn-sm mytopupBtn" data-toggle="modal"  data-target="#mytopupModel" data-session-user="'.$username.'">Topup</button>';
                $stats2.= '<h4>Usage Summary</h4><p>';
                 $stats2.='<button type="button" class="btn btn-info btn-sm mySummaryBtn" data-session-user="'.$username.'">Get Usage Summary</button>';
                 $stats2.='<br/><div id="jxLoadingusagesummary" style="display:none;"></div>';
                // api call chang ea/c
                $stats2.= '<h4>Change Account</h4><p>';    
                $stats2.='<div style="display:flex"><button type="button" class="btn btn-primary btn-sm mysetPendingUpdateBtn" data-toggle="modal"  data-target="#mysetPendingUpdateModel" data-session-user="'.$username.'">Schedule Account Change</button>';
                $stats2.='<button type="button" class="btn btn-info btn-sm myPendingUpdateBtn" data-session-user="'.$username.'">Check Pending Change</button>';
                $stats2.='<button type="button" class="btn btn-danger btn-sm myCancelUpdateBtn" data-session-user="'.$username.'">Cancel Scheduled Change</button></div>';
                 $stats2.='<div><strong><p id="jxLoadingPendingUpdate" style="display:none;color:green;margin-top:20px"></p></strong></div>';
                $stats2.= '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>';

                $lteaccntinfo.=$this->table->add_row( array( $username, $status, $class, $password,$user_comment, $system_comment, $email_address, $stats1.''.$stats2 ));
                }
                
                $lteaccntinfo.=$this->table->generate();	
                $lteaccntinfo.="<div class='pull-right'>$pages</div>";
            
            }else{
                	
                $lteaccntinfo.='<div class="alert alert-warning">';
                $lteaccntinfo.='<strong>Data not found.</strong>';
                $lteaccntinfo.='</div>';
                
            }
                 echo $lteaccntinfo;
     ?>
    </div>
</fieldset>
<div id="mysetPendingUpdateModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Schedule Account Change</h4>
      </div>
      <div class="modal-body">
             <p  id="jxLoadingsac" style="color:green;display:none">Loading...</p>
          <form>
                 <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" id="sacesusername" required placeholder="username@realm">
        </div>
    </div><br/>
                    <div class="form-group">
        <label class="col-lg-3">Class</label>
        <div class="col-lg-8">
            <select class="form-control" name="topup" id="sac_class_id" placeholder="Topup" required>
            </select>
        </div>
    </div><br/>
     
      <div style="text-align:center;margin-top:20px">
        <input type="button" class="btn btn-primary btn-sm" value="Submit" id="sacBtnInmodel">
    </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="mytopupModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Topup User Account</h4>
      </div>
      <div class="modal-body">
             <p  id="jxLoadingt" style="color:green;display:none">Loading...</p>
          <form>
                 <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" id="tesusername" required placeholder="username@realm">
        </div>
    </div><br/>
                    <div class="form-group">
        <label class="col-lg-3">Class</label>
        <div class="col-lg-8">
            <select class="form-control" name="topup" id="class_id" placeholder="Topup" required>
            </select>
        </div>
    </div><br/>
     
      <div style="text-align:center;margin-top:20px">
        <input type="button" class="btn btn-primary btn-sm" value="Submit" id="topupbtnInmodel">
    </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="restoreAccountModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Restore User Account</h4>
      </div>
      <div class="modal-body">
          <p>Please  note:  Parked  CellC  accounts  cannot  be  restored.  RAIN  accounts  Parked  for  more  than  3months cannot be restored.</p>
             <p  id="jxLoadingr" style="color:green;display:none">Loading...</p>
          <form>
                 <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" id="resusername" required placeholder="username@realm">
        </div>
    </div><br/>
      <div style="text-align:center;margin-top:20px">
        <input type="button" class="btn btn-primary btn-sm" value="Submit" id="resAccountBtnFrom">
    </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="deleteAccountModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete User Account</h4>
      </div>
      <div class="modal-body">
             <p  id="jxLoadingd" style="color:green;display:none">Loading...</p>
          <form>
                 <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" id="delusername" required placeholder="username@realm">
        </div>
    </div><br/>

    <div class="form-group">
        <label class="col-lg-3">Reason Code</label>
        <div class="col-lg-8">
            
            <select class="form-control"  name="reason_code" required id="delreason_code">
                <option value="1">Changing service providers due to a better deal</option>
<option value="2">Poor Customer Service</option>
<option value="3">Connectivity issues</option>
<option value="4">Moving to different connectivity medium (i.e Fiber
)</option>
<option value="5">Pricing</option>
<option value="6">Reseller termination - Administrative</option>
<option value="7">Reseller termination - Breach of AUP / FUP</option>
<option value="8">IS Terminaton - Beach of AUP</option>

                </select>
        </div>
    </div>
      <div style="text-align:center;margin-top:20px">
        <input type="button" class="btn btn-primary btn-sm" value="Submit" id="delAccountBtnFrom">
    </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="myuserStatusModel" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">User Details</h4>
      </div>
      <div class="modal-body">
          <p  id="jxLoading" style="color:green;display:none">Loading...</p>
          <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th id="jxuser"></th>
            </tr>
              <tr>
                <th>Status</th>
                <th id="jxstatus"></th>
            </tr>
              <tr>
                <th>Class</th>
                <th id="jxclass"></th>
            </tr>
              <tr>
                <th>Password</th>
                <th id="jxpassword"></th>
            </tr>
              <tr>
                <th>User Comment</th>
                <th id="jxuser_comment"></th>
            </tr>
              <tr>
                <th>System Comment</th>
                <th id="jxsystem_comment"></th>
            </tr>
              <tr>
                <th>Email Address</th>
                <th id="jxemail_address"></th>
            </tr>
            
        </thead>      
              
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
    var usernames = '<?php echo $usernames; ?>';
    usernames = JSON.parse(usernames);
    
    $( function() {
    var availableTags = usernames;
    
    $( "#usernames" ).autocomplete({
      source: availableTags
    });
   });
</script>
<script type="text/javascript" language="javascript">

  function handler(e){
      $(e.target).addClass("current_active_row");
      var username = $('.current_active_row').parent().parent().find('p').children('#current_user').val();
      var picked_date = e.target.value.split("-");
      var year = picked_date[0];
      var month = picked_date[1];
      var date = picked_date[2];
      
      var formData = {
       'usernames': username,
       'year': year,
       'month': month,
       'date': date,
      };      
      
        $.ajax({
        url: "/admin/monthsummaryinfo",
        type: "post",
        data: formData,
        success: function(d) {
            $('.current_active_row').parent().parent().children('.month_summary').empty();
            $('.current_active_row').parent().parent().children('.month_summary').append(d);
            $(e.target).removeClass("current_active_row");            
        } 
        });
        
  }
  
  function handler1(e){ 
      $(e.target).addClass("current_active_row");
      var username = $('.current_active_row').parent().parent().find('p').children('#current_user').val();
      var picked_date = e.target.value.split("-");
      var year = picked_date[0];
      var month = picked_date[1];
      var day = picked_date[2];
      var formData = {
       'usernames': username,
       'year': year,
       'month': month,
       'day': day,
      };
        $.ajax({
        url: "/admin/dayUsageinfo",
        type: "post",
        data: formData,
        success: function(d) {
            $('.current_active_row').parent().parent().children('.day_usage').empty();
            $('.current_active_row').parent().parent().children('.day_usage').append(d);
            $(e.target).removeClass("current_active_row");
        } 
        });      
  }
  
  function handler2(e){
      $(e.target).addClass("current_active_row");
      var username = $('.current_active_row').parent().parent().find('p').children('#current_user').val();
      var picked_date = e.target.value.split("-");
      var year = picked_date[0];
      var month = picked_date[1];
      var date = picked_date[2];
      var formData = {
       'usernames': username,
       'year': year,
       'month': month,
       'date': date,
      };
        $.ajax({
        url: "/admin/monthUsageinfo",
        type: "post",
        data: formData,
        success: function(d) {
            $('.current_active_row').parent().parent().children('.month_usage').empty();
            $('.current_active_row').parent().parent().children('.month_usage').append(d);
            $(e.target).removeClass("current_active_row");
        } 
        });      
  }  
  //Topup
  
 $(document).ready(function() {
     $('.mytopupBtn').on('click',function(){
         var username = $(this).attr('data-session-user'); 
      $('#tesusername').attr("value",username)
     })
      $('#topupbtnInmodel').on('click', function () {
          console.log('clicked')
          $('#jxLoadingt').show();
          var user =  $('#tesusername').val();
var classtype = $('#class_id option:selected').val()

    $.ajax({
                url: "/admin/topupAccountAjaxRequest",
                dataType: "json",
                data: {
                    username:user,isAjax:true,topup:classtype
                },
                success: function( data ) {
                     $('#jxLoadingt').html(data.msg);
                }
            });
      })
      

  })  
//   $(document).ready(function() {
    
//       $('form#lte_account_search').on('submit', function (e) {
//           e.preventDefault();
//       //$('#lte_account_search').click(function(){
//         var formData = {
//             'usernames': $('#usernames').val()
//         };
//         $.ajax({
//         url: "/admin/usernameinfo",
//         type: "post",
//         data: formData,
//         beforeSend: function() {
//               $("#loading-image").show();
//           },
//         success: function(d) {
//             $("#loading-image").hide();
//             $('.lte_accnt_informations').empty();
//             $('.lte_accnt_informations').append(d);
//         } 
//         });
//       })
      

//   })

 $(document).ready(function() {
     $('.statsBtnClick').on('click',function(){
         $('#jxLoadingusagesummary').html('');
     })
      $('.myuserStatusBtn').on('click', function () {
          $('#jxLoading').show();
            $('#jxuser').empty()
                $('#jxstatus').empty()
                $('#jxclass').empty()
                $('#jxpassword').empty()
                $('#jxuser_comment').empty()
                $('#jxsystem_comment').empty()
                $('#jxemail_address').empty()
          var username = $(this).attr('data-session-user');
    $.ajax({
                url: "/admin/usernameInfoAjaxRequest",
                dataType: "json",
                data: {
                    usernames:username,isAjax:true
                },
                success: function( data ) {
               
                       $('#jxLoading').hide();
                $('#jxuser').html(data.user)
                $('#jxstatus').html(data.status)
                $('#jxclass').html(data.class)
                $('#jxpassword').html(data.password)
                $('#jxuser_comment').html(data.user_comment)
                $('#jxsystem_comment').html(data.system_comment)
                $('#jxemail_address').html(data.email_address)
                }
            });
      })
      

  })
   $(document).ready(function() {
   $('.mySummaryBtn').on('click',function(){
          $('#jxLoadingusagesummary').show();
          var username = $(this).attr('data-session-user'); 
       $.ajax({
                url: "/admin/usageSummaryAjaxRequest",
                dataType: "json",
                data: {
                    usernames:username
                },
                success: function( data ) {
                  $('#jxLoadingusagesummary').html(data.msg);     
                }
            });     
    }) 
   })
 //Pending update
    $(document).ready(function() {
   $('.myPendingUpdateBtn').on('click',function(){
          $('#jxLoadingPendingUpdate').show();
          var username = $(this).attr('data-session-user'); 
       $.ajax({
                url: "/admin/pendingUpdateAjaxRequest",
                dataType: "json",
                data: {
                    usernames:username
                },
                success: function( data ) {
                  $('#jxLoadingPendingUpdate').html(data.msg);     
                }
            });     
    }) 
   })
   
   //Cancel Pending update
       $(document).ready(function() {
   $('.myCancelUpdateBtn').on('click',function(){
          $('#jxLoadingPendingUpdate').show();
          var username = $(this).attr('data-session-user'); 
       $.ajax({
                url: "/admin/deletePendingUpdateAjaxRequest",
                dataType: "json",
                data: {
                    usernames:username
                },
                success: function( data ) {
                  $('#jxLoadingPendingUpdate').html(data.msg);     
                }
            });     
    }) 
   })
   //
 $(document).ready(function() {
    $('.deleteUserBtn').on('click',function(){
          var username = $(this).attr('data-session-user'); 
      $('#delusername').attr("value",username)
    }) 
    
$('#delAccountBtnFrom').on('click',function(){
      $('#jxLoadingd').show();
var user =  $('#delusername').val();
var reason_code = $('#delreason_code').val();
    $.ajax({
                url: "/admin/deleteAccountAjaxRequest",
                dataType: "json",
                data: {
                    usernames:user,isAjax:true,reason_code:reason_code
                },
                success: function( data ) {
                       
                       $('#jxLoadingd').text(data.msg);
                    setTimeout(function(){  $('#jxLoadingd').text('') },5000);
                }
            });
})    
 })
   
 $(document).ready(function() {
    $('.restoreUserBtn').on('click',function(){
          var username = $(this).attr('data-session-user'); 
      $('#resusername').attr("value",username)
    }) 
    
$('#resAccountBtnFrom').on('click',function(){
      $('#jxLoadingr').show();
var user =  $('#resusername').val();
    $.ajax({
                method:'POST',
                url: "/admin/restoreAccountAjaxRequest",
                dataType: "json",
                data: {
                    usernames:user,isAjax:true
                },
                success: function( data ) {
                       $('#jxLoadingr').text(data.msg);
                      setTimeout(function(){  $('#jxLoadingr').text('') },5000);
                }
            });
})    
 })
  $(document).ready(function() {
    $('.mysetPendingUpdateBtn').on('click',function(){
          var username = $(this).attr('data-session-user'); 
      $('#sacesusername').attr("value",username)
    }) 
    
$('#sacBtnInmodel').on('click',function(){
      $('#jxLoadingsac').show();
var user =  $('#sacesusername').val();
var classtype = $('#sac_class_id option:selected').val();
    $.ajax({
                method:'POST',
                url: "/admin/setPendingUpdateAjaxRequest",
                dataType: "json",
                data: {
                    username:user,isAjax:true,topup:classtype
                },
                success: function( data ) {
                       $('#jxLoadingsac').text(data.msg);
                    setTimeout(function(){  $('#jxLoadingsac').text('') },5000);
                }
            });
})    
 })
 
 //sac
 
</script>
<script language="javascript" type="text/javascript">
    $( function() {
            $.ajax({
                url: "/admin/get_class_name",
                dataType: "json",
                data: {
                    user:'@openwebmobile'
                },
                success: function( data ) {
                    $('#class_id').empty();
                    $('#sac_class_id').empty();
                    for (let i = 0; i < data.data.classes.length; i++) {
                        if(data.data.classes[i]['Description'].includes('TOP UP')){
                        $('#class_id').append('<option value="' + data.data.classes[i]['ClassID'] + '">' + data.data.classes[i]['Description'] + '</option>');
                    }
                 if(data.data.classes[i]['Description'].includes('MTN LTE')){
                      if(!data.data.classes[i]['Description'].includes('TOP UP')){
                $('#sac_class_id').append('<option value="' + data.data.classes[i]['ClassID'] + '">' + data.data.classes[i]['Description'] + '</option>');    
                      }
                 }
                }
                }
            });
      
    } );
</script>
<style>
    #jxLoadingusagesummary {
    overflow-x: auto;
}
    
</style>
