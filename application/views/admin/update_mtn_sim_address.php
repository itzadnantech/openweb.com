 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<h3 style="margin-bottom:30px;">Update MTN SIM Address</h3>
<div class="container">
    <div class="row">
     <?php
    echo form_open('admin/submit_update_mtn_sim_address', array('class' => 'form-horizontal','id' => 'create_new_lte_account')) ?>
    <!-- username -->
    <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" placeholder="username@realm" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Longitude</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="longitude" required paceholder="">
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Latitude</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="latitude" required>
        </div>
    </div>
 <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>

    <?php echo form_close();?>
    </div>
</div>