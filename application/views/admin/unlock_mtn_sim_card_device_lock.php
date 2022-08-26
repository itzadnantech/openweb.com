 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<h3 style="margin-bottom:30px;">Unlock MTN SIM Card Device Lock</h3>
<div class="container">
    <div class="row">
<dvi class="col-lg-12">
     <?php
    echo form_open('admin/submit_unlock_mtn_sim_card_device_lock', array('class' => 'form-horizontal','id' => 'create_new_lte_account')) ?>
    <!-- username -->
    <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Username" placeholder="username@realm" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Type</label>
        <div class="col-lg-8">
            <select class="form-control" id="locationSelector"  name="type" required placeholder="">
                <option value="">Select Type</option>
                <option value="LocationUnlock">Location Unlock</option>
                <option value="DeviceUnlock">Device Unlock</option>
                <select>
        </div>
    </div>
<div id="locationBox" style="display:none;">
    <div class="form-group">
        <label class="col-lg-3">Latitude</label>
        <div class="col-lg-8">
           <input class="form-control" type="text" name="Latitude" placeholder="Latitude" id="lat-f">
        </div>
    </div>   
    <div class="form-group">
        <label class="col-lg-3">Longitude</label>
        <div class="col-lg-8">
           <input class="form-control" type="text" name="Longitude" placeholder="Longitude" id="long-f">
        </div>
    </div>   
</div>
    <div class="form-group">
        <label class="col-lg-3">Comment</label>
        <div class="col-lg-8">
            <textarea class="form-control" name="comment" required></textarea>
        </div>
    </div>
 <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>

    <?php echo form_close();?>
</div>        
    </div>
</div>