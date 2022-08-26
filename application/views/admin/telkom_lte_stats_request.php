<h3>Telkom LTE Stats Requests</h3>
<div class="row">
    <div id="divResults" class='alert alert-success'></div>
    <button type="button" name="telkomClearBtn" id="telkomClearBtn" data-order-clear-all ="telkom"  value=" Clear Button" class="btn btn-warning" title="Note:After pressing this button all records are removed form page.">Clear Records</button> 
    
    <table class="table table-striped" id="telkom-lte-req-tbl">
        <thead>
        <tr>
            <th>Order Number</th>
            <th>Request Date</th>
            <th>First Name</th>
            <th>Last Name</th>
             <th>Email Address</th>
             <th>OW Number</th>
             <th>Telkom SIM Username</th>
               <th>SIM Serial Number</th>
              <th>Request Status</th>
             <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($telkom_stat_request as $trs):?>
        <tr>
            <th><?= $trs['telkom_user_code']?></th>
            <td><?= $trs['telkome_request_date']?></td>
            <td><?= $trs['first_name']?></td>
            <td><?= $trs['last_name']?></td>
            <td><?= $trs['email_address']?></td>
            <td><?= $trs['ow']?></td> 
            <td><?= $trs['fibre_data_username']?></td> 
              <td><?= $trs['sim_serial_no']?></td> 
            <td>
                <?php
                if($trs['telkom_status'] == 'REQUESTED'){
                echo "<strong style='color:blue'>".$trs['telkom_status']."</strong>";    
                }elseif($trs['telkom_status'] == 'RESETED'){
                echo "<strong style='color:red'>".$trs['telkom_status']."</strong>";        
                }elseif($trs['telkom_status'] == 'MAILED'){
                echo "<strong style='color:green'>".$trs['telkom_status']."</strong>";        
                }
                ?>
            </td>
            <td> 
            <div style="display:flex;">
            <button type="button"  class="btn btn-info btn-sm send_telkom_stats_btn" data-send-order-id="<?php echo $trs['telkom_user_code'];?>"
            data-send-email-id="<?php echo $trs['email_address'];?>"  data-send-name-id="<?php echo $trs['first_name'].' '. $trs['last_name'];?>"
            data-toggle="modal" data-order-type="telkom"
            data-prev-total-cap="<?= $trs['telkom_total_cap']?>" data-prev-simnumber="<?= $trs['sim_serial_no']?>" data-prev-night-cap="<?= $trs['telkom_night_cap']?>" data-prev-anytime-cap="<?= $trs['telkom_time_cap']?>"
            data-target="#myModal">Send Stats</button>    
             <button type="button" class="btn btn-danger btn-sm order_reset_btn"  data-reset-order-type="telkom" data-order-id="<?php echo $trs['telkom_user_code'];?>">Reset</button> 
             <button type="button" onclick="return confirm('Are you sure you want to delete this record ?');" class="btn btn-warning btn-sm order_delete_btn"  data-reset-order-type="telkom" data-order-id="<?php echo $trs['telkom_user_code'];?>">Delete</button> 
             </div>  
                 <?php if($trs['rel_rec_status'] == 'Topup Request'):?>
            <button type="button" data-toggle="modal"  data-target="#myModalTopupRequest" 
            class="btn btn-success btn-sm order_topup_request_view_btn" 
            style="margin-top:10px;" data-brought-topup="<?= $trs['rel_rec_topup_name'];?>" data-brought-topup-price="<?= $trs['rel_rec_amount'];?>"
            data-brought-topup-date="<?= $trs['rel_rec_date'];?>" data-brought-topup-id="<?= $trs['tel_rec_id'];?>" 
            data-brought-send-email-id="<?php echo $trs['email_address'];?>" data-brought-send-name-id="<?php echo $trs['first_name'].' '. $trs['last_name'];?>"
            data-brought-order-id="<?= $trs['telkom_user_code'];?>">Topup Request</button> 
               
            <?php elseif($trs['rel_rec_status'] == 'Topup Loaded'):?>
            <strong style="margin-top:5px;color:green;"><?= $trs['rel_rec_status'] ?></strong>
            <?php endif;?>
            </td>     
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Stats Details</h4>
      </div>
      <div class="modal-body">
<form id="telkom-stats-form">
  <div class="form-group">
    <label>Total Cap:</label>
    <input type="hidden" name="order_id" id="hidden_order_id" value=""/>
     <input type="hidden" name="user_email" id="hidden_user_email" value=""/>
      <input type="hidden" name="user_name" id="hidden_user_name" value=""/>
       <input type="hidden" name="order_type" id="hidden_order_type_name" value="telkom"/>
            <input type="hidden" name="simnumber" id="hidden_simnumber" value=""/>
    <input type="number" name="total_cap" class="form-control">
    <strong>Previous Total Cap :</strong> <strong id="prev_total_cap"></strong>
  </div>
  <div class="form-group">
    <label>Anytime Cap Used:</label>
    <input type="number" name="anytime_cap" class="form-control">
        <strong>Previous Anytime Cap :</strong>  <strong id="prev_anytime_cap"></strong>
  </div>
   <div class="form-group">
    <label>Night Cap Used:</label>
    <input type="number" name="night_cap" class="form-control">
       <strong>Previous Night Cap :</strong> <strong id="prev_night_cap"></strong>
  </div>
  <button type="button" id="telkomSaveBtn"  class="btn btn-primary btn-md">Submit</button>
</form>
      </div>
      <div class="modal-footer">
        <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="myModalTopupRequest" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Requested Topup Details</h4>
      </div>
      <div class="modal-body">
          <table class="table table-borderless">
        <tr>
        <th>Topup Name</th>    
        <td id="table_topup_name"></td> 
        </tr>   
        <tr>
            <th>Amount paid</th>
            <td id="table_topup_price"> </td>
        </tr>
        <tr>
            <th>Transaction Date</th>
            <td id="table_transaction_date"> </td>
        </tr>
          </table>
      </div>
      <form id="telkom-topuploaded-form" style="margin-left: 25px;">
         <input type="hidden" name="topuploaded_id" />
         <input type="hidden" name="topuploaded_name"/>
         <input type="hidden" name="topuploaded_price"/>
            <input type="hidden" name="topuploaded_buyer_name"/>
               <input type="hidden" name="topuploaded_buyer_email"/>
                <input type="hidden" name="topuploaded_order_id"/>
               
       <button type="button" id="telkomTopupSuccessBtn"  class="btn btn-primary btn-md">Topup Loaded</button>
      </form>
      <div class="modal-footer">
        <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
</div>