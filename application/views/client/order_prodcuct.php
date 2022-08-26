<div id="page-content" class="container">
  	<div class="row">
	    <div class="col-lg-3">
	      <!-- Product Information-->
	   	  <fieldset>
	      	<legend align="center">Product Information</legend>
	      	<?php
	      	if(isset($product_data)){ ?>
	      		<div style="font-size: 16px;color: #428bca;">
	      			<strong><?php echo $product_data['name'];?></strong>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;color: #333; font-size: 20px;">
	      			<strong>Your Price for the rest of <?php echo date('M').' '.date('Y')?>:</strong> <label><?php echo 'R '.$pro_rata;?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Price:</strong> <label style="color: #428bca"><?php echo 'R '.$product_data['price'];?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Billing Cycle:</strong> 
	      			<label style="color: #428bca">
	      			<?php if(isset($billing_cycle) && !empty($billing_cycle)){
	      				foreach ($billing_cycle as $k=>$v){
							echo $v['billing_cycle'];
							echo ', ';	      					
	      				}
	      			}else{
	      				echo 'Monthly';
	      			}?>
	      			</label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Package Speed:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['package_speed']);?></label>
	      		</div>
	      		<br>
	      		<div style="font-size: 16px;">
	      			<strong>Service Level:</strong> <label style="color: #428bca"><?php echo ucfirst($product_data['service_level']);?></label>
	      		</div>
	      <?php }?> 
	      </fieldset> 
	    </div>
	   <?php echo form_open('client/create_client', array('class' => 'form-horizontal','id' => 'billing_form'));?> 
	   <input type="hidden" value=<?php echo $product_data['id'];?> name="product_id" />
	    <div class="col-lg-9">
			<div class="col-lg-12">
	     		<!--Cilent Information Billing information..... -->
	     		<fieldset>
	     			<div class="row">
	     				<div class="col-lg-6">
	     					<legend>Account Information</legend>
	     					<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">First Name</label>			
								<div class="col-lg-6">
									<input type="text" value="" class="form-control valid" id="first_name"  name="first_name"  placeholder="First Name">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">Last Name</label>			
								<div class="col-lg-6">
									<input type="text" value="" class="form-control valid" id="last_name"  name="last_name"  placeholder="Last Name">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">User Name </label>			
								<div class="col-lg-6">
									<input type="text" value="" class="form-control valid" id="user_name"  name="user_name"  placeholder="User Name">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">Email Address</label>			
								<div class="col-lg-6">
									<input type="text" value="" class="form-control valid" id="email_address"  name="email_address"  placeholder="Email Address">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">Mobile Phone</label>			
								<div class="col-lg-6">
									<input type="text" value="" class="form-control valid" id="mobile"  name="mobile"  placeholder="Mobile Phone Number">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">Password</label>			
								<div class="col-lg-6">
									<input type="password" value="" class="form-control valid" id="pwd"  name="pwd"  placeholder="Password">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">Confirm Password</label>			
								<div class="col-lg-6">
									<input type="password" value="" class="form-control valid" id="re_pwd"  name="re_pwd"  placeholder="Confirm Password">			
								</div>
								<div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
                            <div class="form-group">
                                <label for="billing_name" class="control-label col-lg-5">ADSL Line Number</label>
                                <div class="col-lg-6">
                                    <input type="text" value="" class="form-control valid" id="adsl_number_input"  name="adsl_number"  placeholder="ADSL Line Number">
                                </div>
                            </div>



	     				</div>
	     				
						<div class="col-lg-6">
							<legend>Billing User Information</legend>
                            <div class="form-group">
                                <label for="billing_name" class="control-label col-lg-5">SA ID Number</label>
                                <div class="col-lg-6">
                                    <input type="text" name="sa_id_number" value="" class="form-control valid" id="sa_id_number_element" placeholder="SA ID Number">
                                </div>
                                <div style="color:#f62b2b; font-size: 25px;">*</div>
                            </div>
							<div class="form-group">
								<label for="billing_name" class="control-label col-lg-5">Billing Name</label>
								<div class="col-lg-6">
									<input type="text" name="billing_name" value="" class="form-control valid" id="billing_name" placeholder="Billing Name">
								</div>
								<div style="color:#f62b2b; font-size: 25px;">*</div>
							</div>
							<div class="form-group">
								<label for="address_1" class="control-label col-lg-5">Address 1</label>
								<div class="col-lg-6">
									<input type="text" name="address_1" value="" class="form-control" id="address_1" placeholder="Your Address">
								</div>
								<div style="color: #f62b2b; font-size: 25px;">*</div>
							</div>
							<div class="form-group">
								<label for="address_1" class="control-label col-lg-5">Address 2</label>
								<div class="col-lg-6">
									<input type="text" name="address_2" value="" class="form-control" id="address_2" placeholder="Your Address">
								</div>
							</div>
							<div class="form-group">
								<label for="city" class="control-label col-lg-5">City</label>
								<div class="col-lg-6">
									<input type="text" name="city" value="" class="form-control" id="city" placeholder="City">
								</div>
								<div style="color: #f62b2b; font-size: 25px;">*</div>
							</div>
							<div class="form-group">
								<label for="province" class="control-label col-lg-5">Province/State</label>
								<div class="col-lg-6">
									<input type="text" name="province" value="" class="form-control" id="province" placeholder="Province">
								</div>
								<div style="color: #f62b2b; font-size: 25px;">*</div>
							</div>
							<div class="form-group">
								<label for="country" class="control-label col-lg-5">Country</label>
								<div class="col-lg-6">
									<input type="text" name="country" value="South Africa" class="form-control" id="country" readonly>
								</div>
								<div style="color: #f62b2b; font-size: 25px;">*</div>
							</div>
							<div class="form-group">
								<label for="postal_code" class="control-label col-lg-5">Postal Code</label>
								<div class="col-lg-6">
									<input type="text" name="postal_code" value="" class="form-control" id="postal_code" placeholder="Postal Code Number">
								</div>
								<div style="color: #f62b2b; font-size: 25px;">*</div>
							</div>
							<div class="form-group">
								<label for="contact_number" class="control-label col-lg-5">Contact Number</label>
								<div class="col-lg-6">
									<input type="text" name="contact_number" value="" class="form-control valid" id="contact_number"  placeholder="Contact Number">
								</div>
                                <div style="color:#f62b2b;font-size:25px;">*</div>
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label for="contact_number" class="control-label col-lg-4">How did you hear about OpenWeb?</label>
							</div>
							<div class="form-group">
								<div class="col-lg-12">
									<textarea rows="5" cols="" style="box-sizing: border-box; resize: none;" class="form-control" name="reason" id="reason"></textarea>
								</div>
							</div>
						</div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <?php
                                    $terms_value;


                                    $data = array(
                                        'name'        => 'terms_texarea',
                                        'id'          => 'terms_texarea_element',
                                        'value'       => $terms_txt,
                                        'rows'        => '10',
                                        'cols'        => '',
                                        'style'       => 'box-sizing: border-box; resize: none;',
                                        'class'       => 'form-control',
                                        'readonly'    => ''
                                    );
                                    echo form_textarea($data);
                                    ?>
                                </div>
                            </div>
                        </div>

						<div class="col-lg-12">
							<div class="col-lg-12">
								<label>
									<input type="checkbox" id="agree_check" name="agree_check"> <b>I agree with the <a href="<?php echo TERMS_OF_SERVICE_LINK;?>" style="text-decoration: none;" target=_blank>Terms of Service</a>.</b>
								</label>
							</div>
						</div>
					</div>
	     		</fieldset>
	      	</div>
	  <div class="col-lg-12" style="letter-spacing: 100px;text-align:center;">
			<input type="submit" value="Next" class="btn btn-primary btn-lg" style="display: none;" id='sevice_div'>	
			<input type="reset"  value="Cancel" class="btn btn-primary btn-lg">	
	  </div>
	  <?php echo form_close(); ?>
	</div>
</div>

<script>
$('#agree_check').click(function(){
	if($('#agree_check').is(":checked")){
		$('#sevice_div').show();
	}else{
		$('#sevice_div').hide();
 	}
})

 if($('#agree_check').is(":checked")){
	$('#sevice_div').show();
 }else{
	$('#sevice_div').hide();
 }
 
$("#billing_form").validate({
	rules: {
       first_name : "required",
       last_name : "required",
       user_name : {
    	  required : true,
    	  minlength: 4,
    	  remote:{
			url : "<?php echo site_url('client/validate_username')?>",
			type : 'post',
			dataType: "json", 
			data :{ 
				username : function(){return $("#user_name").val();}
			}
		  },	
	  },
        sa_id_number : {
            required : true,
            digits: true,
            minlength: 10,
            /*
             remote:{
             url : "<?php echo site_url('register/validate_sa_id')?>",
             type : 'post',
             dataType: "json",
             data :{
             username : function(){return $("#sa_id_number").val();}
             }
             },
             */
        },
       email_address : {
	      required : true,
	      email: true,
	      remote:{
			url : "<?php echo site_url('client/validate_email')?>",
				type : 'post',
				data :{ 
					email_address : function(){return $("#email_address").val();},
				}	
			  },				 
		   },
	       mobile : {
	    	   number : true,
	    	   required : true,
		   },
	       pwd : 'required',
	       re_pwd  : {
		   		required : true,
	            equalTo  : "#pwd",
           },
	       
	       billing_name : "required", 
	       address_1 : "required",
	       city : "required", 
	       province : "required", 
	       postal_code : "required", 
	       country : "required",
	       contact_number : "required",
		},
	   	messages: {
	   		email_address:{
	   			remote: 'That email address is already registered on our system.  Please choose another.',	
		   	}
		}
	});
</script>

   <!----- ---->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1071738246;
        var google_custom_params = window.google_tag_params;
        var google_remarketing_only = true;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1071738246/?value=0&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>