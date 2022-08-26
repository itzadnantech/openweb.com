$(document).ready(function() {
    $("#change_pwd_form").validate({
        rules: {
            account_password: {
                required : true,
                minlength: 4,
                maxlength: 32,
            },

        }
    });

    // remove when functional will be finished
    $(".order-buttons").attr('disabled','disabled');
    //
    $(".chart-lte-div").hide();


    $("#this_month_title").click(function () {

        if($(".chart-lte-div").is(":visible")) {
            $(".chart-lte-div").hide();
        } else {
            $(".chart-lte-div").show();

            if($("#lte-chart").is(':empty')) {

                getChartData();

            }
        }

    });

});

function getChartData() {

    var order_id = $("#order-id").text();

    $.ajax({
        url: "/user/getLTEOrderMonthData",
        data: {order_id : order_id},
        success: function (data) {
            createChart(data);
        }
    });

}
//{y: '2006', a: 50},
function createChart(data) {

    $("#chart-spinner").hide();
    $("#lte-chart").show();

    Morris.Line({
        element: 'lte-chart',
        data: JSON.parse(data),
        xkey: 'y',
        ykeys: ['a'],
        labels: ['Megabytes'],
        lineColors:['#0aa699'],
    });


}