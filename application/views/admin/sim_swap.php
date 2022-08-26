<script language="javascript" type="text/javascript">
    $(document).ready(function () {
        $('#info-button').click(function (e) {
            e.preventDefault();
            var username = $('#username').val();
            $.ajax({
                url: '/admin/getAccountInfoAPI',
                type: 'POST',
                data: {'username': username}
            }).done(function (data) {
                $('#info-group').show();
                data = JSON.parse(data);
                $('#info').val('MSISDSN: ' + data.WBS_LTE_MSISDSN + String.fromCharCode(13, 10) +'SIM: ' + data.WBS_LTE_SIM);
            });
        });
    });
</script>
<h3>Perform an LTE SIM Swap</h3>

<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '' ) {
    $m = $messages['success_message'];
    echo "<div class='alert alert-success'>$m</div>";
}

if (isset($messages['error_message']) && trim($messages['error_message']) != '' ) {
    $error_message = $messages['error_message'];
    echo "<div class='alert alert-danger'>$error_message </div>";
}
?>
 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<div class="container">
    <?php
    echo form_open('admin/submit_sim_swap', array('class' => 'form-horizontal','id' => 'submit_sim_swap')) ?>

    <div class="form-group" id="info-group" hidden>
        <label class="col-lg-3">Account Info</label>
        <div class="col-lg-8">
            <textarea class="form-control" id="info" disabled></textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" id="username" placeholder="username@realm">
        </div>
        <div class="col-lg-1">
            <button id="info-button" class="btn btn-default">Info</button>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Existing MSISDN</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="msisdn" id="msisdn" placeholder="0825454541">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">New ICCID</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="iccid" id="iccid" placeholder="991465165112">
        </div>
    </div>

    <h4>RICA:</h4>

    <div class="form-group">
        <label class="col-lg-3">Building</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="building" id="building" placeholder="Unit 1">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Street</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="street" id="street" placeholder="45 Street Road">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Suburb</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="suburb" id="suburb" placeholder="Suburbia">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">City</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="city" id="city" placeholder="Johannesburg">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">PostCode</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="postcode" id="postcode" placeholder="2090">
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-lg-3">ContactName</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="ContactName" id="ContactName" placeholder="Contact Name">
        </div>
    </div>
       <div class="form-group">
        <label class="col-lg-3">Tel Cell</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="TelCell" id="TelCell" placeholder="Tel Cell">
        </div>
    </div>
      <div class="form-group">
        <label class="col-lg-3">Id Number</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="idNumber" id="idNumber" placeholder="ID Number">
        </div>
    </div>
      <div class="form-group">
        <label class="col-lg-3">AddressType</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="AddressType" id="AddressType" placeholder="Address Type">
        </div>
    </div>
    
 <h4>AddressLocation:</h4>

    <div class="form-group">
        <label class="col-lg-3">Latitude:</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Latitude" id="Latitude" placeholder="-90.90">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Longitude:</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Longitude" id="Longitude" placeholder="70.00">
        </div>
    </div>
    <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Perform request">
    </div>

    <?php echo form_close();?>
</div>