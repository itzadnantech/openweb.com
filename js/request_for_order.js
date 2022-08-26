var t_checked = 0;
var m_checked = 0;
$(document).ready(function () {
    $('.auto').autoNumeric('init');

    var form = $('#order_form');
    form.validate({
        rules: {
            password: {
                required: true,
                minlength: 5
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true
            }
        }
    });

    var terms = $("#terms");
    var man = $("#mandate");
    $('#check-err').hide();


    terms.click(function () {

        if (t_checked === 0) {
            t_checked = 1;
        } else {
            t_checked = 0;
        }

    });

    man.click(function () {
        if (m_checked === 0) {
            m_checked = 1;
        } else {
            m_checked = 0;
        }
    });

    $('#place').click(function (e) {
        if(!form.valid()) {
            e.preventDefault();

            if (t_checked === 0 || m_checked === 0) {
                $('#check-err').show();
            } else {
                $('#check-err').hide();
            }
        }
    });
});