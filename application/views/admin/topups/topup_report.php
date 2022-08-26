<script type="text/javascript" language="javascript">
    $(document).ready(function() {

       /*
        $("#form_search").validate({
            rules: {
                topup_name : "required"
            }
        });
        */
    });


</script>
<h3>TopUp Orders Report</h3>
<fieldset>
    <legend>Search For TopUp</legend>
    <div class="row">
        <?php

            $search_params = array(

                'topup_name' => '',
                'user_name'  => '',
                'from_date'  => '',
                'to_date'    => '',
            );

            foreach ($search_array as $key => $value ){

                if (!empty($value))
                     $search_params[$key] = $value;
            }

        ?>
        <?php echo form_open('admin/topup_reports', array('method'=>'get', 'class' => 'form-horizontal','id'=> 'form_search')); ?>

        <div class="row">
            <div class="form_group">
                <label class="control-label col-lg-2">TopUp Name:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="topup_name" id="topup_name_element" value="<?php echo $search_params['topup_name']; ?>">
                </div>
            </div>
         </div>
        <div class="row">
            <div class="form_group">
                <label class="control-label col-lg-2">UserName:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="user_name" id="topup_username_element" value="<?php echo $search_params['user_name']; ?>">
               </div>
            </div>
        </div>
        <div class="row">
            <div class="form_group">
                <label class="control-label col-lg-2">Date 'from':</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="from_date" id="topup_date_from_element" placeholder="e.g. : 02-05-2015" value="<?php echo $search_params['from_date']; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form_group">
                <label class="control-label col-lg-2">Date 'to':</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="to_date" id="topup_date_to_element" value="<?php echo $search_params['to_date']; ?>">
                    <br/>
                    <input type="submit" class="btn btn-sm btn-primary" value="Search">
                </div>
            </div>
        </div>


        <?php echo form_close();?>
    </div>

</fieldset>
<br/>

<fieldset>
    <legend>TopUp List</legend>
    <?php

    // Messages

    if (isset($messages['success_message']) && trim($messages['success_message']) != '' ) {
        echo "<div class='alert alert-success'>";
        echo $messages['success_message'];
        echo "</div>";
    }

    if (isset($messages['error_message']) && trim($messages['error_message']) != '' ) {
        echo "<div class='alert alert-danger'>";
        echo $messages['error_message'];
        echo "</div>";
    }

    if ($num_per_page > $num_product) {
        $num_per_page = $num_product;
    }

    if (!empty($topup_list)) {
        echo "<div class='pull-right'>$showing</div>";
        $tmpl = array ( 'table_open'  => '<table class="table">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('Client', 'TopUp', 'Date',  'Price', 'Payment status', 'Actions'));

        foreach ($topup_list as $topup_row) {

            $row_id         = $topup_row['id'];
            $topup_id       = $topup_row['topup_config_id'];
            $client         = $user_names[$topup_row['user_id']] . " (" . $topup_row['username']  . ")";
            $topup_name     = $topup_names[$topup_id ];
            $order_date     = date('d/m/Y' ,strtotime($topup_row['order_time'])); // date("d-m-Y", strtotime($originalDate))
            $price          = 'R'. number_format(round($topup_row['price'], 2), 2);
            $payment_status = $topup_row['payment_status'];



            $manage_page = anchor("admin/edit_topup_order/$row_id", 'Manage', 'class="btn btn-sm btn-primary"');
            $delete_page = anchor("admin/delete_topup_order/$row_id", 'Delete', 'class="btn btn-sm btn-danger" ');


            $action = $manage_page.' '.$delete_page;
            $action = array('data' => $action, 'style' => 'width:200px;');
            $this->table->add_row( array( $client, $topup_name, $order_date, $price, $payment_status, $action));
        }

        echo $this->table->generate();

        echo "<div class='pull-right'>$pages</div>";
    }else{
        ?>
        <div class="alert alert-warning">
            <strong>TopUp not found.</strong>
        </div>
    <?php
    }
    ?>
</fieldset>