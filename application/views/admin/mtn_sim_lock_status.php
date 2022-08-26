 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<h3 style="margin-bottom:30px;"> MTN SIM Lock Status</h3>
<div class="container">
    <div class="row">
<dvi class="col-lg-12">
     <?php
    echo form_open('admin/submit_mtn_sim_lock_status', array('class' => 'form-horizontal','id' => 'create_new_lte_account')) ?>
    <!-- username -->
    <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" placeholder="username@realm" required>
        </div>
    </div>
 <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>

    <?php echo form_close();?>
</div> 
<div class="row">
<div class="col-lg-12">

 <?php if($this->session->flashdata('record_data')):?> 
    <h3 style="margin-bottom:30px;">MTN SIM Lock Status Result</h3>
 <?php
$data =$this->session->flashdata('record_data');
 ?>
 <table class="table">
     <thead>
         <tr style="color:green">
        <th>Username : <?= $data['Username'] ?></th>     
         <th>MSISDN : <?=  $data['MSISDN'] ?></th> 
          <th>Status : <?=  $data['Status'] ?></th> 
           <th>ID : <?=  $data['ID'] ?></th> 
         </tr>
         <th>ID</th>
          <th>Latitude</th>
           <th>Longitude</th>
            <th>ChangeDate</th>
     </thead>
     <tbody>
         <?php foreach($data['LocationUpdates'] as $d):?>
         <tr>
         <td><?= $d['ID']?></td>
                  <td><?= $d['Latitude']?></td>
                           <td><?= $d['Longitude']?></td>
                                    <td><?= $d['ChangeDate']?></td>
         </tr>
         <?php endforeach;?>
     </tbody>
 </table>
 
    <?php endif;?>
</div></div>
    </div>
</div>