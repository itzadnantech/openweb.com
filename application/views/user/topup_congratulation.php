<div class="lead">
    <?php


            $ordered_topup_id = 0;
            if(isset($topup_id) && !empty($topup_id)){
                $ordered_topup_id = $topup_id;
            }
            if(isset($payment_method) && !empty($payment_method)){
               // echo $payment_method;

            }

            if(isset($fail_message) && !empty($fail_message)){
                echo $fail_message;
            }elseif(isset($success_message) && !empty($success_message)){
                echo $success_message;
            }else{
                echo ''; // Nothing is here
            }

            echo "<br/><br/><a href='/user/orders' class='btn btn-primary'>Back to Services</a>";
    ?>

</div>
<!-- Google Code for OpenWeb SIgnup Conversion Page -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 1071738246;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "PlEuCOKO0QEQhtuF_wM";
    var google_conversion_value = 1.00;
    var google_conversion_currency = "USD";
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1071738246/?value=1.00&amp;currency_code=USD&amp;label=PlEuCOKO0QEQhtuF_wM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
