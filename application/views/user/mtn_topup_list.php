<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
  
        <div class="page-title">
<h3>Topup your LTE MTN account instantly:</h3></div>   
<p>Kindly select the Topup Plan you LTE MTN account you would like to topup with your credit card below.  For EFT topups, kindly email <span id="a-email">admin@openweb.co.za</span></p>
<table class="prod-table table table-striped">
					<tbody>
					    <tr>
					   <?php foreach($telkom_plan as $tp):?>     
					        <td>
					        <div>
					           <p class="plan-name" ><?= $tp['topup_name']?></p> 
					           <p><?= $tp['topup_description']?></p>
					           <p class="plan-price">R<?= $tp['topup_price']?></p>
					           <p><button type="button" class="btn btn-info btn-lg telkom-orderknowbtn" 
                data-order-id="<?= $order_id;?>" data-topup-id="<?= $tp['topup_id']?>" data-toggle="modal" data-target="#myModal">Order Now</button></p>
					        </div>    
					        </td>
					<?php endforeach;?>        
					    </tr>
				</tbody>
			</table>
<a href="<?php echo $ajax_url; ?>" id="form-link" hidden></a>
   <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Order Now</h4>
      </div>
      <div class="modal-body">
    <?php echo form_open("#", array('id'=>'billing_form')); ?>
          <div class="form-group">
  <label for="usr">Plan Name:</label>
        <input type="text" required class="form-control topup_name" id="telkon-model-plan-name" readonly />
</div>
<div class="form-group">
  <label for="pwd">Plan Price:</label>
  <input type="text" required class="form-control amount" id="telkon-model-plan-price" readonly />
</div>  
<input type="submit" name="confirm" value="Proceed with Payfast" class="btn btn-large btn-success submit text-center">
        </form> 
        <?php
        //remove sandbox_payfast_host by live_payfast_host
echo form_open("https://" .$live_payfast_host . "/eng/process", array('id'=>'payfast_live', 'class' => 'form-horizontal'));


    foreach ($payfast_data as $key => $value){

        echo  "<input name='". $key . "' value='" . $value."' type='hidden' >";
    }
echo form_close();   ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

</div>
</div>
</div>

<style>
    #a-email{
        color: #428bca;
    }
    .plan-name , .plan-price{

        font-weight:900;
    }
</style>