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
                    for (let i = 0; i < data.data.classes.length; i++) {
                        if(data.data.classes[i]['Description'].includes('TOP UP')){
                        $('#class_id').append('<option value="' + data.data.classes[i]['ClassID'] + '">' + data.data.classes[i]['Description'] + '</option>');
                    }
                }
                }
            });
      
    } );
</script>
 <?php if($this->session->flashdata('error_message')):?>
 <div class='alert alert-danger'><?= $this->session->flashdata('error_message'); ?></div>
 <?php endif;?>
 <?php if($this->session->flashdata('success_message')):?>
 
  <div class='alert alert-success'><?= $this->session->flashdata('success_message'); ?></div>
 <?php endif;?>
<h3 style="margin-bottom:30px;">LTE Account Queue Topup</h3>
<div class="container">
    <div class="row">
<div class="col-lg-12">
    <?php
    echo form_open('admin/submit_queue_topup_lte_account', array('class' => 'form-horizontal','id' => 'create_new_lte_account')) ?>
    
        <div class="form-group">
        <label class="col-lg-3">Username</label>
        <div class="col-lg-8">
            <input class="form-control" type="text" name="username" id="username" placeholder="Username" required>
        </div>
    </div>
     <div class="form-group">
        <label class="col-lg-3">Topup</label>
        <div class="col-lg-8">
            <input type="hidden" name="request_type" value="queue_topup_request"/>
            <select class="form-control" name="topup" id="class_id" placeholder="Topup" required>
            </select>    
        </div>
    </div>
    <div style="text-align:center">
        <input type="submit" class="btn btn-primary btn-lg" value="Submit">
    </div>
    <?= form_close();?>  
</div> 
</div>
</div>