$(document).ready(function () {
    $("#billing_info").hide();
    $("#plan_data_cell").hide();
    $("#plan_data_rain").hide();

    $("#order").change(function () {
        var orderId = $("#order").val();

        $.ajax({
            url: "/user/check_order_topup",
            data: {order: orderId}

        }).success(function (data) {
            var answ = JSON.parse(data);
            if(answ.response == "OK") {

                $("#payfast_live input[name=custom_str1").val(answ.username);

                if(answ.lte_type == 'cell_c') {
                    $("#plan_data_cell").show();
                    $("#plan_data_rain").hide();
                } else

                if(answ.lte_type == 'rain') {
                    $("#plan_data_rain").show();
                    $("#plan_data_cell").hide();
                } else {
                    $("#plan_data_rain").hide();
                    $("#plan_data_cell").hide();
                }
            }
        })
    });
    
    //telkom-edit
        $(".telkom-orderknowbtn").click(function () {
         var  $row= $(this).closest("td");    
             // Find the row
                var plan_name = $row.find(".plan-name").text();
                var plan_price = $row.find('.plan-price').text();
                //form fields 
                $('#telkon-model-plan-name').attr('value',plan_name);
                $('#telkon-model-plan-price').attr('value',plan_price);
                //plan_id
                var plan_id = $(this).attr('data-topup-id');
                
             setTimeout(function(){
                 
          $("#payfast_live input[name=amount").val( $.trim(plan_price.replace(/\R/g, "")));
          $("#payfast_live input[name=item_name").val(plan_name);
          $("#payfast_live input[name=item_description").val(plan_name);
     
            },100)
            
          var order_username = $("#payfast_live input[name=custom_str1").val();
          
        $.ajax({
            url: "/user/topup_signature_telkom",
            data: {id: plan_id, username: order_username}
        }).success(function (data) {
            var dataObj = JSON.parse(data);
            $(".amount").val("R" + dataObj.price);
            FillPayfastForm(dataObj);
            showCardForm();
        })
    });
    

    $("#plan_cell").change(function () {
        var plan_id = $("#plan_cell").val();
        var order_username = $("#payfast_live input[name=custom_str1").val();

        $.ajax({
            url: "/user/topup_signature",
            data: {id: plan_id, username: order_username}

        }).success(function (data) {

            var dataObj = JSON.parse(data);

            $(".amount").val("R"+dataObj.price);
            FillPayfastForm(dataObj);
            showCardForm();
        })
    });

    $("#plan_rain").change(function () {
        var plan_id = $("#plan_rain").val();
        var order_username = $("#payfast_live input[name=custom_str1").val();

        $.ajax({
            url: "/user/topup_signature",
            data: {id: plan_id, username: order_username}

        }).success(function (data) {

            var dataObj = JSON.parse(data);

            $(".amount").val("R" + dataObj.price);
            FillPayfastForm(dataObj);
            showCardForm();
        })
    });

    $("form#billing_form").submit(function (event) {

        event.preventDefault();
        add_order();
        //$("form#payfast_live").submit();
    });


    function get_params(){

        var id = 'payfast_live';

        // Sandbox
        var param_children = $("form#" + id).children('input');
        var param_length = param_children.length;
        var param_array = {};

        for (var i = 0; i < param_length; i++){

            param_array[$(param_children[i]).attr('name')] = param_children[i].value;
        }

        return param_array;

    }

    function get_ajax_url(){

        var url = $("#form-link").attr("href");
        return url;
    }

    function add_order(){

        var params   =  get_params();
        var ajax_url =  get_ajax_url();

        return  $.ajax({
            type: "GET",
            url:  ajax_url,
            data: {
                params : JSON.stringify(params),
                //user   : username

            },
            success: function (answer){
                if(answer == 1) {
                    $("form#payfast_live").submit();
                }

            }

        });

    }
});

function showCardForm() {

    $("#billing_info").show();
}

function FillPayfastForm(data) {

    $("#payfast_live input[name=amount").val(data.price);
    $("#payfast_live input[name=signature").val(data.sign);
    $("#payfast_live input[name=item_name").val(data.topup_name);
    $("#payfast_live input[name=item_description").val(data.topup_desc);
    $("#payfast_live input[name=return_url").val(data.return_url);
    $("#payfast_live input[name=cancel_url").val(data.cancel_url);
    $("#payfast_live input[name=notify_url").val(data.notify_url);
}