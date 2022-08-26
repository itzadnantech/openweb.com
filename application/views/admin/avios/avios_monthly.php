
<h2>Active Orders</h2>
<div class="alert alert-danger" id="error_mes" hidden>
    Something goes wrong, this page will be reloaded
</div>
<?php
if ($num_per_page > $num_account) {
    $num_per_page = $num_account;
}
echo "<fieldset>";

if (!empty($orders)) {
    echo "<div class='pull-right'>$showing</div>";
    $tmpl = array ( 'table_open'  => '<table class="table">' );
    $this->table->set_template($tmpl);
    $this->table->set_heading(array('Order comment', 'Order ID', 'Username', 'Date', 'Billing Code', 'Actions'));

    foreach ($billingCodes as $code => $desc) {
        $options .= '<option value="'.$code.'">'.$desc.'</option>';
    }

    foreach ($orders as $ind => $order_data) {
        $comment = $order_data['account_comment'];
        $order_id = $order_data['id'];
        $username = $order_data['user'];
        $date = date('d/m/Y', strtotime($order_data['date']));
        //$points = '<input type="number" id="f_'.$order_id.'">';

        $billing = '<select id="billing_'.$order_id.'">'.$options.'</select>';

        $atr = 'class="btn btn-sm btn-danger btn_act" value="'.$order_data['id_user'].'" id="b_'.$order_id.'"';
        $atr .= ' data-loading-text="<i class=\'fa fa-spinner fa-spin \'></i> Wait..."';
        $submit = anchor("#", 'Add code', $atr);

        $this->table->add_row( array( $comment, $order_id, $username, $date, $billing, $submit));
    }

    echo $this->table->generate();
    echo "<div class='pull-right'>$pages</div>";
}else{
    ?>
    <div class="alert alert-warning">
        <strong>Awards not found.</strong> <?php echo $priority_flag['message']; ?>
    </div>
    <?php
}
?>
</fieldset>
<script type="text/javascript" language="javascript">

$(document).ready(function () {

    $(".btn_act").click(function (event) {
        event.preventDefault();

        var $this = $(this);
        var order_id = this.id.substr(2);
        //var points = $('#f_'+order_id).val();
        var user_id = $this.attr('value');
        var billing = $('#billing_'+order_id).find(":selected").attr('value');

        var valid = billingValidate(billing, order_id);

        if(valid == true) {

            $this.button('loading');

            $.ajax({
                type: "POST",
                url: "award_avios_ajax",
                data: {user_id: user_id, order_id: order_id, billing: billing},
                xhrFields: {
                    withCredentials: true
                }
            }).done(function (result) {

                if(result === "OK") {
                    $this.html('Added');
                    $this.addClass("disabled");
                    $this.parent().parent().css("background-color", "#eee");
                    $("#billing_"+order_id).prop("disabled", true);
                    updateBage();
                } else {
                    $("#error_mes").show();
                    location.reload();
                }
            }).fail(function () {
                console.log("Error");
                $this.button('reset');
            });
        }

    });

    function updateBage() {
        var bage = $("#new").html();
        $("#new").html(bage-1);
    }

    function billingValidate(billing, order) {

        if (billing === '') {
            $('#billing_'+order).css('border', 'solid 2px #FF0000');
            return false;
        } else {
            $('#billing_'+order).css('border', 'solid 1px #000000');
            return true;
        }
    }
});

</script>