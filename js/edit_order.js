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
    $(".chart-lte-year").hide();

    //For Month
    $("#this_month_title").click(function () {

        if($(".chart-lte-div").is(":visible")) {
            $(".chart-lte-div").hide();
        } else {
            $(".chart-lte-div").show();

            if($("#lte-chart").is(':empty')) {

                getChartData('m');

            }
        }

    });

    //For Year
    $("#year_title").click(function () {

        if($(".chart-lte-year").is(":visible")) {
            $(".chart-lte-year").hide();
        } else {
            $(".chart-lte-year").show();

            if($("#lte-chart-year").is(':empty')) {

                getChartData('y');

            }
        }

    });
});

function getChartData(period) {

    var order_id = $("#order-id").text();
    var url = '/user/getMonthUsageADSL';

    if(period == 'y')
        url = '/user/getYearUsageADSL';

    $.ajax({
        url: url,
        data: {order_id : order_id},
        success: function (data) {
            createChart(data, period);
        }
    });

}
//{y: '2006', a: 50},
function createChart(data, period) {

    var element = 'none';

    if(period == 'y') {
        $("#chart-spinner-year").hide();
        $("#lte-chart-year").show();
        element = 'lte-chart-year';
    }

    if(period == 'm') {
        $("#chart-spinner").hide();
        $("#lte-chart").show();
        element = 'lte-chart';
    }

    Morris.Line({
        element: element,
        data: JSON.parse(data),
        xkey: 'y',
        ykeys: ['a'],
        labels: ['Megabytes'],
        lineColors:['#0aa699'],
    });

}

function reload(period) {

    if(period == 'm') {
        $("#lte-chart").hide();
        $("#chart-spinner").show();
        $("#lte-chart").html('');
    }

    if(period == 'y') {
        $("#lte-chart-year").hide();
        $("#chart-spinner-year").show();
        $("#lte-chart-year").html('');
    }

    getChartData(period);
}

