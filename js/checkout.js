// Check out page
$("input[name='billing_cycle']").click(function(){

    // get current parameter
    $checkedElement = $("input[name='billing_cycle']:checked").val();
    if ($checkedElement == 'Monthly'){

        $('#span-order-message').show();
    } else {

        $('#span-order-message').hide();
    }

});


// submit button
$('#submit_button').click(function(){ console.log(";k;lkl;k");
    $("#u_exist_error").hide();
    var boolCheck = $('input:radio[name="billing_cycle"]').is(":checked");
    if(boolCheck == true){
        var val=$('input:radio[name="billing_cycle"]:checked').val();
        $('#choose_cycle').val(val);
        $('#cycle_error').hide();

        var create_username = $('input:checkbox[name="create_username"]:checked').val();
        if(create_username != null && create_username == 'create_client'){
            var acc_username = $('#username').val();
            var acc_pwd = $('#password').val();

            acc_username = $.trim(acc_username);
            acc_pwd = $.trim(acc_pwd);

            // get realm
            var acc_realm = $('#realm').val();
            if (acc_realm == null )
                return false;

            if(acc_username != "" && acc_pwd != "" ){
                $('#u_p_error').hide();


                // pre  -  check - ajax
                var ajax_obj = send_ajax_check(acc_username, acc_realm);
                var answer  = null;
                ajax_obj.success(function(answer){

                    if (answer == 'true'){
                        $("#u_exist_error").show();
                    } else {
                        $('#check_form').submit();
                    }
                });
                return false;
                // $('#check_form').submit();
            }else{
                $('#u_p_error').show();

            }
        }else{
            $('#check_form').submit();
        }
    }else{
        $('#cycle_error').show();
    }



});

$('.choose_u_p').click(function(){
    var  c_u= $('input:checkbox[name="create_username"]:checked').val();
    if(c_u == 'create_client'){
        $('#auto_create').show();
    }else{
        $('#auto_create').hide();
    }
});
$('#auto_create').show();

function  send_ajax_check(acc_username, acc_realm){


    var  ajax_url = '/user/check_local_username';
    return  $.ajax({
        type: "POST",
        url:  ajax_url,
        data: {
            acc_username : acc_username,
            acc_realm : acc_realm,

        },/*
             success: function (answer){

             var  answerObj = $.parseJSON(answer);

             } */

    });

    // return function_answer;
}