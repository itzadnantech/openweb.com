$('#credit_card_div').hide();
$('#debit_order_div').hide();


/*$("#billing_form").validate({
 ignore: [],
 rules: {
 card_num : {
 number : true,
 required : true,
 },
 cvc : {
 number : true,
 required : true,
 },
 bank_name : "required",
 bank_account_number : "required",
 bank_branch_code : "required",
 }
 });*/

$('.radio').click(function(){

    var payment_type = $('input:radio[name="payment_method"]:checked').val();
    if ( (payment_type == 'payfast-sandbox') || (payment_type == 'payfast-live') )  {

        $('.virtual').show();
        $('.submit').hide();
        $('#credit_card_div').hide();
        $('#debit_order_div').hide();

        return;
    }

    if(payment_type == 'credit_card'){

        $('.virtual').show();
        $('.submit').hide();
        $('#credit_card_div').hide();
        $('#debit_order_div').hide();

    }else{

        if(payment_type == 'credit_card_auto'){
            $('#credit_card_div').show();
            $('#debit_order_div').hide();

        }else if(payment_type == 'debit_order'){

            $('#debit_order_div').show();
            $('#credit_card_div').hide();

        }else{

            $('#credit_card_div').hide();
            $('#debit_order_div').hide();

        }

        $('.submit').show();
        $('.virtual').hide();
    }
});



$('#billing_form').submit(function(){

    var val = $('input:radio[name="payment_method"]:checked').val();
    if(val == null){
        $('.error').show();

        return false;
    }else{

        $('.error').hide();
        if(val == 'credit_card_auto'){

            var card_num = $('#card_num').val();
            var cvc = $('#cvc').val();
            if(card_num != "" && cvc != ""){

                return true;
            }else if(card_num == ""){

                $('#card_num_error').show();
                return false;
            }else if(cvc == ""){

                $('#cvc_error').show();
                return false;
            }

        }else if(val == 'debit_order'){

            var bank_name = $('#bank_name').val();
            var bank_account_number = $('#bank_account_number').val();
            var bank_branch_code = $('#bank_branch_code').val();

            if(bank_name != "" && bank_account_number != "" && bank_branch_code != ""){

                return true;
            }else if(bank_name == ""){

                $('#bank_name_error').show();
                return false;
            }else if(bank_account_number == ""){

                $('#bank_account_number_error').show();
                return false;
            }else if(bank_branch_code == ""){

                $('#bank_branch_code_error').show();
                return false;
            }

        }else{
            return true;

        }
    }

});




$('.virtual').click(function(){

    // $('#vcs_form').submit();


    // get amount
    var payfastAmount = $("input[name='amount']").val();

    if (payfastAmount == 0){
        alert('This payment method is not available for R0 amount');
        return;
    }

    $("#payment_radio input.payment").attr('disabled','disabled');
    $(this).attr('disabled','disabled');



    var payment_type = $('input:radio[name="payment_method"]:checked').val();
    // var ajax_url = "";

    if ( payment_type == 'payfast-sandbox')  {


        var ajax_object = send_ajax('SANDBOX');
        ajax_object.success(function(answer){

            //  console.log(answer);
            $('#payfast_sandbox').submit();
        });
        //$('#payfast_sandbox').submit();

    } else {
        if (payment_type == 'payfast-live'){

            // ajax to db with LIVe key
            var ajax_result = send_ajax();
            ajax_result.success(function(answer){


                $('#payfast_live').submit();
            });
            // $('#payfast_live').submit();
        }
    }

    $("#payment_radio input.payment").removeAttr( "disabled" );
    $(this).removeAttr('disabled');
    // console.log("disabled off");
    return false;

});


function get_params(sandbox){

    var id = 'payfast_live';
    if (sandbox == 'SANDBOX'){
        id = 'payfast_sandbox';
    }

    // Sandbox
    var param_children = $("form#" + id).children('input');
    var param_length = param_children.length;
    var param_array = {};

    for (var i = 0; i < param_length; i++){

        param_array[$(param_children[i]).attr('name')] = param_children[i].value;
    }

    return param_array;

}


function get_ajax_url(url){

    var ajax_url = url.substring(0, url.length - 6 ); // 6 - for 'notify'
    return ajax_url  + "prevalid";;
}

function get_username(){

    var username = "<?php echo $username; ?>"
    return $.trim(username);
}

function get_order_signature(){

    var signature = "<?php echo $order_signature; ?>";
    return signature;
}

function get_order_object(){
    var order_obj = {};

    order_obj['account_username']  = "<?php echo $order_data_array['account_username']; ?>";
    order_obj['account_password']  = "<?php echo $order_data_array['account_password']; ?>";
    order_obj['realm']             = "<?php echo $order_data_array['realm']; ?>";
    order_obj['choose_cycle']      = "<?php echo $order_data_array['choose_cycle']; ?>";
    order_obj['product_id']        = "<?php echo $order_data_array['product_id']; ?>";
    order_obj['payment_type']      = "<?php echo $order_data_array['payment_type']; ?>";


    return order_obj;

}

function  send_ajax(sandbox){

    var pre_signature = $.trim("<?php echo $pre_live; ?>");
    if (sandbox == 'SANDBOX') {
        pre_signature = $.trim("<?php echo $pre_sandbox; ?>");
    }

    var params   =  get_params(sandbox);
    var ajax_url =  get_ajax_url(params['notify_url']);
    var username =  get_username();
    // var

    var order_signature = get_order_signature();
    var order_object = get_order_object();

    return  $.ajax({
        type: "POST",
        url:  ajax_url,
        data: {
            params : JSON.stringify(params),
            order_params : JSON.stringify(order_object),
            user   : username,
            pre_signature : pre_signature,
            order_signature : order_signature,

        }, /*
            success: function (answer){

                 var  answerObj = $.parseJSON(answer);

            } */

    });

    // return function_answer;

}