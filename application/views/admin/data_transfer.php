<h3>Transfer data</h3>
<h5>Transfer data from one LTE account to another.</h5>
<p><strong>Rules: Data transfer is only available with RAIN and MTN product types.
RAIN data and MTN data can only be transferred within their respective organisations.</strong></p>
<br/><br/>
 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<div class="container">
    <div class="row">
    <?php
    echo form_open('admin/data_transfer_submit', array('class' => 'form-horizontal','id' => 'data_transfer_submit')) ?>

    <div class="form-group">
        <label class="col-lg-3">Sender MSISDN</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="sender_MSISDN"  required  title="MSISDN - Can be prefixed with 0 or 27">
        </div>
    </div>
     <div class="form-group">
        <label class="col-lg-3">Recipient MSISDN</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="recipient_MSISDN"  required  title="MSISDN - Can be prefixed with 0 or 27">
        </div>
    </div>
       <div class="form-group">
        <label class="col-lg-3">Amount :  eg. 1024, 2048, 10240 </label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="amount" value="1024" required  title=" How much data in MB you would like to transfer  MTN:  Only day-time packages are transferable.
Only one transaction a month (sending or receiving of data) is permitted per account.
Only increments of 1GB are allowed for data transfers, eg. 1024, 2048, 10240, etc.
The maximum amount of data allowed to be transferred is 10GB per month, i.e 10240.
Data transfers have the lowest priority in the usage sequence.
Data transfers are anytime data, in the same way topup data works.
Data transfers are not reversible.
The data will automatically be added onto the day-time used data for the sender. This means
accounting will not match up.
On the usageSummary API call, you will see 'data_sent' and 'data_received' respectively. The
'data_sent' value will be negative, as it’s seen as used, unavailable data. RAIN: Only topup data can be transferred. Monthly package data cannot be transferred.
Data transfer amounts should match the available topup amounts in MB.">
        </div>
    </div>
        <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>

    <?= form_close();?>
    </div>
    </div>