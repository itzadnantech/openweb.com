 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<h3 style="margin-bottom:30px;">Rica MTN SIM</h3>
<div class="container">
    <div class="row">
    <?php
    echo form_open('admin/submit_rica_mtn_sim', array('class' => 'form-horizontal','id' => 'submit_rica_mtn_sim')) ?>
 <!-- username -->
    <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Username"  required >
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3">Id Number</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="IdNumber" required>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3">Contact Name</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="ContactName" required>
        </div>
    </div>
           <div class="form-group">
        <label class="col-lg-3">Tell Cell</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="TellCell" required>
        </div>
    </div>
               <div class="form-group">
        <label class="col-lg-3">Building</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Building" required>
        </div>
    </div>
               <div class="form-group">
        <label class="col-lg-3">Address Complex</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="AddressComplex" required>
        </div>
    </div>
     <div class="form-group">
        <label class="col-lg-3">Street</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Street" required>
        </div>
    </div>
     <div class="form-group">
        <label class="col-lg-3">Suburb</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="Suburb" required>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3">City</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="City" required>
        </div>
    </div>
     <div class="form-group">
        <label class="col-lg-3">PostCode</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="PostCode" required>
        </div>
        <input type="hidden" name="request_type" value="rica_mtn_sim"/>
    </div>
    <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>

    <?php echo form_close();?>
    </div>
    </div>