 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<h3 style="margin-bottom:30px;">Usage Summary</h3>
<div class="container">
    <div class="row">
<div class="col-lg-12">
      <?php
    echo form_open('admin/get_mtn_usage_summary', array('class' => 'form-horizontal','id' => 'create_new_lte_account')) ?>
        <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
             <input type="hidden" name="request_type" value="mtn_usage_summary"/>
            <input class="form-control" type="text" name="username" id="username" placeholder="Username" required>
        </div>
    </div>
      <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>
    <?= form_close();?>
</div> 
    </div>
    <div class="row">
        <div class="col-lg-12">
         <?php if($this->session->flashdata('res_data')):?>
         <?php
         $data = $this->session->flashdata('res_data');
         
         ?>
         <div class="table-responsive">
         <table class="table table-condensed" id="example-550">
             
        <thead>
        <?php foreach($data as $dz):?>    
        <tr>
        <th style="color:green">User : <?= $this->session->flashdata(res_data_user_name)?></th>    
        <th style="color:green">Service ID : <?= $dz['Service ID']?></th>    
        <th style="color:green">MSISDN : <?=$dz['MSISDN']?></th>
        </tr>    
        <?php endforeach;?>
          <th>ID</th>
      <th>Data Type</th>
      <th>Category</th>
      <th>Title</th>
      <th>Total Data</th>
      <th>Remaining Data</th>
      <th>Data Units</th>
      <th>Last Update</th>
      <th>Assigned Date</th>
      <th>Activation Date</th>
      <th>Expire Date</th>
      <th>Type ID</th>
      <th>Status</th>
        </thead>
         <?php foreach($data as $d):?>
         <?php foreach($d['Packages'] as $dp):?>
            <tr>
            <td><strong><?= $dp['ID']?></strong></td>    
            <td><?= $dp['Data Type']?></td>    
            <td><?= $dp['Category']?></td>   
                        <td><?= $dp['Title']?></td>   
            <td><?= $dp['Total Data']?></td>    
            <td><?= $dp['Remaining Data']?></td>    
            <td><?= $dp['Data Units']?></td>    
            <td><?= $dp['Last Update']?></td>   
            <td><?= $dp['Assigned Date']?></td>
                        <td><?= $dp['Activation Date']?></td>
                                    <td><?= $dp['Expire Date']?></td>   
                                    <td><?= $dp['Type ID']?></td>
                                    <td style="color:blue"><strong><?= $dp['Status']?></strong></td>
            </tr>
         <?php endforeach; ?>
         <?php endforeach;?>
         <?php endif;?>
         </table>
         </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#example-550').DataTable();
} );    
    
</script>