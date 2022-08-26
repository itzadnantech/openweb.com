/* Webarch Admin Dashboard 
/* This JS is only for DEMO Purposes - Extract the code that you need
-----------------------------------------------------------------*/ 
$(document).ready(function() {				

    $('.select2', "#form_traditional_validation").change(function () {
        $('#form_traditional_validation').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
    });

    $("#avios_form").validate({
        rules: {
            br_a_id: {
                number: true,
                minlength: 8,
                maxlength: 9
            }
        }
    });

    $("#mobile_data_form").validate({
        rules: {
            physical_delivery_address : "required",
            city : "required",
            postcode : "required",

            //  proof_of_residence: "required",
            //  id_or_passport : "required",

        }
    });

    $("#change_password_form").validate({
        rules: {
            newPass: {
                required : true,
                minlength: 4,
                maxlength: 32,
            },
            passConf: {
                required : true,
                equalTo: "#newPass"
            },
        }
    });
	//Iconic form validation sample	
    $('#form_iconic_validation').validate({
        errorElement: 'span',
        errorClass: 'error',
        focusInvalid: false,
        ignore: "",
        rules: {
            form1Name: {
                minlength: 2,
                required: true
            },
            form1Email: {
                required: true,
                email: true
            },
            form1Url: {
                required: true,
                url: true
            },
            gendericonic:{
                required: true
            }
        },

        invalidHandler: function (event, validator) {
            //display error alert on form submit
        },

        errorPlacement: function (error, element) { // render error placement for each input type
            var icon = $(element).parent('.input-with-icon').children('i');
            var parent = $(element).parent('.input-with-icon');
            icon.removeClass('fa fa-check').addClass('fa fa-exclamation');
            parent.removeClass('success-control').addClass('error-control');
        },

        highlight: function (element) { // hightlight error inputs
            var parent = $(element).parent();
            parent.removeClass('success-control').addClass('error-control');
        },

        unhighlight: function (element) { // revert the change done by hightlight

        },

        success: function (label, element) {
            var icon = $(element).parent('.input-with-icon').children('i');
            var parent = $(element).parent('.input-with-icon');
            icon.removeClass("fa fa-exclamation").addClass('fa fa-check');
            parent.removeClass('error-control').addClass('success-control');
        },

        submitHandler: function (form) {

        }
    });
    $('.select2', "#form_iconic_validation").change(function () {
        $('#form_iconic_validation').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
    });
	//Form Condensed Validation
    $('#form-condensed').validate({
        errorElement: 'span',
        errorClass: 'error',
        focusInvalid: false,
        ignore: "",
        rules: {
            first_name: {
                minlength: 3,
                required: true
            },
            last_name: {
                minlength: 3,
                required: true
            },
            email_address: {
                required: true,
                email: true
            },
            mobile_number: {
                required: true,
                minlength: 10,
                maxlength: 10
            },
            username: {
                minlength: 5,
                required: true,
            },
            password: {
                minlength: 5,
                required: true,
            },
            re_password: {
                minlength: 5,
                required: true,
                equalTo: "#password"
            },

            sa_id_number: {
                required: true,
                minlength: 13,
                maxlength: 13,
                number: true
            },
            br_a_id: {
                number: true,
                minlength: 8,
                maxlength: 9
            },
            agree_check: {
                required: true
            }
        },

        invalidHandler: function (event, validator) {
            //display error alert on form submit
        },

        errorPlacement: function (label, element) { // render error placement for each input type

            if(element[0].id == "chkTerms") {
                $('<span class="error"></span>').insertAfter($("#checkbox-reg")).append(label);
            } else {
                $('<span class="error"></span>').insertAfter(element).append(label)
            }
        },

    });
	
	//Form Wizard Validations
	var $validator = $("#commentForm").validate({
		  rules: {
            email_address: {
		      required: true,
		      email: true,
		      minlength: 3
		    },
            username: {
		      required: true,
		      minlength: 3
		    },
			txtFirstName: {
		      required: true,
		      minlength: 3
		    },
			last_name: {
		      required: true,
		      minlength: 3
		    },
			txtCountry: {
		      required: true,
		      minlength: 3
		    },
			txtPostalCode: {
		      required: true,
		      minlength: 3
		    },
			txtPhoneCode: {
		      required: true,
		      minlength: 3
		    },
			txtPhoneNumber: {
		      required: true,
		      minlength: 3
		    },
		    urlfield: {
		      required: true,
		      minlength: 3,
		      url: true
		    },
            sa_id_number: {
                required: true,
                minlength: 3
            },
            first_name: {
                required: true,
                minlength: 3
		    },
            password: {
                required: true,
                minlength: 3
            },
            re_password: {
                required: true,
                minlength: 3
            },
            agree_check: {
                required: true
            }
		  },
		  errorPlacement: function(label, element) {
				$('<span class="arrow"></span>').insertBefore(element);
				$('<span class="error"></span>').insertAfter(element).append(label)
			}
		});

	$('#rootwizard').bootstrapWizard({
	  		'tabClass': 'form-wizard',
	  		'onNext': function(tab, navigation, index) {
	  			var $valid = $("#commentForm").valid();
	  			if(!$valid) {
	  				$validator.focusInvalid();
	  				return false;
	  			}
				else{
					$('#rootwizard').find('.form-wizard').children('li').eq(index-1).addClass('complete');
					$('#rootwizard').find('.form-wizard').children('li').eq(index-1).find('.step').html('<i class="fa fa-check"></i>');	
				}
	  		}
	 });

    $("#form_forgot_password").validate({

        rules: {
            email : "required"
        }
    });

    $("#billing_form").validate({
        rules: {
            email : {
                required : true,
                email: true
            },
            mobile : {
                required : true,
                number : true,
            },
            contact_number : {
                required : true,
                number : true,
            },
            postal_code : "required",
            billing_name : "required",
            address_1 : "required",
            city : "required",
            province : "required",
            country : "required",

            sa_id_number : {
                required : true,
                digits: true,
                minlength: 10
            },
        }
    });
});

function disableInput(field) {
    $(field).prop("disabled", true);
}

function enableInput(field) {
    $(field).prop("disabled", false);
}