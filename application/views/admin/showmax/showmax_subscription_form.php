<script language="javascript" type="text/javascript">
    $(document).ready(function() {

        jQuery.validator.addMethod(
            "money",
            function(value, element) {
                var isValidMoney = /^\d{0,4}(\.\d{0,2})?$/.test(value);
                return this.optional(element) || isValidMoney;
            }, "Price is not valid"
        );


        $("#create_top_up_form").validate({
            rules:{
                name_topup : {
                    required : true,
                    remote:{
                        url : "<?php echo site_url('admin/topup_name_validation')?>",
                        type : 'post',
                        data :{
                            topup_id : function(){return $("#id_element").val();},
                            topup_name : function(){return $("#name_element").val();}
                        }
                    },
                },
                /*price :{
                 required : true,
                 },*/
                description: {

                },
                class_topup : {
                    required : true,

                },
                price : {
                    required : true,
                    money    : true,


                },
                messages : {

                    name : {
                        remote: "This name is already in use."
                    },
                },
            },
            errorPlacement: function(error, element) {

                if (element[0].id == 'price_element'){
                    error.appendTo(element.parent().parent());

                } else {
                    error.insertAfter(element);

                }

                //return false;

            }
        });

        $('#create_top_up_form').submit(function(){

                $('#payment_methods_error').hide();
                var selected = [];
                $('#payment_methods_element input:checked').each(function() {
                    selected.push($(this).attr('name'));
                });

                if(selected.length <= 0){

                    // show error message
                    $('#payment_methods_error').show();
                    return false;
                }

                return true;

            }
        );


        /*
         $('#create_product_form').submit(function(){

         var class_name = $('#class_name').val();
         var discount = $('#price').val();
         var reg = new RegExp('^[0-9]*$');

         if(class_name && discount && reg.test(discount)){
         $('#class_notice').hide();
         $('#price_notice').hide();
         return true;
         }else{
         if(discount == ''){
         $('#price_notice').html('This field is required.');
         $('#price_notice').show();
         }else if(!reg.test(discount)){
         $('#price_notice').html('Please enter a valid number.');
         $('#price_notice').show();
         }

         if(!class_name){
         $('#class_notice').html('Please choose a class.');
         $('#class_notice').show();
         }
         return false;
         }
         });
         /*
         $('#price').blur(function(){
         var discount = $('#price').val();
         var reg = new RegExp('^[0-9]*$');

         if(reg.test(discount)){
         $('#price_notice').hide();
         }else{
         $('#price_notice').html('Please enter a valid number.');
         $('#price_notice').show();
         $('#price').focus();
         }
         });

         $('#class_name').blur(function(){
         var class_name = $('#class_name').val();

         if(class_name){
         $('#class_notice').hide();
         }else{
         $('#class_notice').html('Please choose a class.');
         $('#class_notice').show();
         $('#class_notice').focus();
         }
         });
         */
        $('#name_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#class_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
        $('#price_group_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");


    });

</script>
<div class="container">
    <div class="row">

        <?php

        $row_data['showmax_subscription_id'] = '';
        $row_data['user_id']                 = '';

        /*

            User select :
            Activate button :

         */

        if (isset($edit_flag) && $edit_flag && !empty($data_topup_row) ){

            $row_data['showmax_subscription_id']  = $data_topup_row['showmax_subscription_id'];
            $row_data['user_id']                  = $data_topup_row['user_id'];

        }


        $div_open = "<div class='form-group'>";
        $div_element_open = "<div class='col-lg-6'>";

        $div_close= "</div>";

        echo form_open('admin/update_showmax_subscription', array('class' => 'form-horizontal','id' => 'create_top_up_form'));


        // Subscription Id (hidden)
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        /*
        $local_topup_id = $row_data['topup_id'];

        echo form_input(
            array(
                'type' => "hidden",
                'name' => 'id_topup',
                'id' => 'id_element',
                'value' => $local_topup_id,

            )
        );
        */



        // User list
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $classes_list = array();
        if (!empty($data_classes)){
            foreach ($data_classes as $i) {
                $classes_list[$i['table_id']] = "({$i['realm']}) - {$i['desc']}";
            }
        }

        echo $div_open;
        echo form_label("User", "user_data", array ('class'=> 'control-label col-lg-3'));
        echo $div_element_open;
        echo form_dropdown('user_data', $user_list, $row_data['user_id'], "class = 'form-control col-lg-6' size='7' id='user_element' autocomplete='off' ");
        echo $div_close;
        echo $div_close;


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //  Bttons  'Submit & Cancel'
        $data_reset_button = array(
            'name'    => 'reset',
            'id'      => 'reset_element',
            //'value' => 'true',
            'type'    => 'reset',
            'content' => 'Reset',
            'class'   => 'btn btn-primary  btn-lg ',
            'style'   => 'margin-left : 75px;',
        );

        $data_submit_button = array(

            'name'  => 'submit_btn',
            'class' => 'btn btn-lg btn-primary',
            'value' => 'Activate',
            'style' => 'margin-left : 100px;',
            'id'    => 'submit_element',

        );

        echo "<div class='col-lg-6 col-lg-offset-2' style='margin-top : 25px;'>";

        echo form_submit($data_submit_button);
        echo form_button($data_reset_button);

        echo $div_close;



        echo form_close();

        ?>
    </div>
</div>


