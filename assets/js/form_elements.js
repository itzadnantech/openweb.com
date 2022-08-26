/* Webarch Admin Dashboard 
/* This JS is only for DEMO Purposes - Extract the code that you need
-----------------------------------------------------------------*/	
//Cool ios7 switch - Beta version
//Done using pure Javascript
if(!$('html').hasClass('lte9')) {
    var email_switch = null;
    var mobile_switch = null;
    var Switch = require('ios7-switch')
        , checkbox = document.querySelector('.ios')
        , mySwitch = new Switch(checkbox);

    $.ajax({
        url: "email_param",

    }).success(function (data) {
        if(data == "1") {
            mySwitch.toggle();
            email_switch = 1;
        } else {
            email_switch = 0;
        }

    });

    mySwitch.el.addEventListener('click', function(e){
        e.preventDefault();
        var bulk_dropdown = 0;

        if(email_switch == 0) {
            bulk_dropdown = 1;
        }

        $.ajax({
          method: "POST",
          url: "email_param",
          data: {bulk_dropdown: bulk_dropdown}
        }).success(function (data) {
          if(data == "2") {
              mySwitch.toggle();
          }

        });
    }, false);
    //creating multiple instances
    var Switch2 = require('ios7-switch')
        , checkbox = document.querySelector('.iosblue')
        , mySwitch2 = new Switch2(checkbox);
    $.ajax({
        url: "invoice_email",

    }).success(function (data) {
        if(data == "1") {
            mySwitch2.toggle();
            mobile_switch = 1;
        } else {
            mobile_switch = 0;
        }

    });

    mySwitch2.el.addEventListener('click', function(ev){
        ev.preventDefault();
          var bulk_dropdown = 0;

          if(mobile_switch == 0) {
              bulk_dropdown = 1;
          }
          $.ajax({
              method: "POST",
              url: "invoice_email",
              data: {invoice_dropdown: bulk_dropdown}
          }).success(function (data) {
              if(data == "2") {
                  mySwitch2.toggle();
              }

          });

    }, false);
}

function showCardForm() {

    $("#billing_info").show();
}

function FillPayfastForm(data) {

    $("#payfast_live input[name=amount]").val(data.price);
    $("#payfast_live input[name=signature]").val(data.sign);
    $("#payfast_live input[name=item_name]").val(data.topup_name);
    $("#payfast_live input[name=item_description]").val(data.topup_desc);
    $("#payfast_live input[name=return_url]").val(data.return_url);
    $("#payfast_live input[name=cancel_url]").val(data.cancel_url);
    $("#payfast_live input[name=notify_url]").val(data.notify_url);
}

$(document).ready(function(){
	  //Dropdown menu - select2 plug-in
	$("#source").select2();
	  
	  //Multiselect - Select2 plug-in
	$("#multi").val(["Jim","Lucy"]).select2();
	  
	  //Date Pickers
	$('.input-append.date').datepicker({
		utoclose: true,
		odayHighlight: true
	});
	 
	$('#dp5').datepicker();

	$('#sandbox-advance').datepicker({
        format: "dd/mm/yyyy",
        startView: 1,
        daysOfWeekDisabled: "3,4",
        autoclose: true,
        todayHighlight: true
    });

	//Color pickers
	//$('.my-colorpicker-control').colorpicker()

	//Input mask - Input helper
	$(function($){
        $("#date").mask("99/99/9999");
        $("#phone").mask("(999) 999-9999");
        $("#tin").mask("99-9999999");
        $("#ssn").mask("999-99-9999");
	});

	
	//Drag n Drop up-loader
	$("div#myId").dropzone({ url: "/file/post" });

    $('#br_a_id').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

    $('#billing_name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#address_1').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#city').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#province').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#postal_code').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#country').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#email').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#mobile').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#contact_number').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#sa_id_number').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

    $('#physical_delivery_address_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#postcode_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#city_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#proof_of_residence_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
    $('#id_or_passport_element').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

    //Invoices Page
    $("#start_date").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm',
        showButtonPanel: true,
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month option:selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year option:selected").val();
            $('#start_date').val(year+'-'+(parseInt(month)+1));
        }
    });


});