<script type="text/javascript" language="javascript">

    /*
    $(document).ready(function() {
        $("#form_search").validate({
            rules: {
                pro_name : "required",
            }
        });
    });
    */

    function check(){
        var r = $('#request_filter_element').val();
        if(r){
            $("#form_filter_mobile_requests").submit();
        }else{
            return false;
        }
    }

</script>
<h3>All Mobile Data Requests</h3>
<!--
<fieldset>
    <legend>Search For Product</legend>

    <?php echo form_open('admin/search_for_product', array('class' => 'form-horizontal','id'=> 'form_search')); ?>
    <div class="form_group">
        <label class="control-label col-lg-2">Product Name:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="pro_name" id="pro_name">
        </div>
        <div class="col-lg-1">
            <input type="submit" class="btn btn-sm btn-primary" value="Search">
        </div>
    </div>
    <?php echo form_close();?>
</fieldset>
-->
<br/>

<fieldset>

    <?php echo form_open('/admin/all_mobile_requests', array('class' => '', 'id' => 'form_filter_mobile_requests')); ?>
        <br/>
        <div class="form-group">

            <label class="control-label col-lg-2">Visibility filter :</label>
            <div class="col-lg-3">
                <?php
                $options = array(
                    'all'         => 'ALL',
                    'processed'   => 'Processed',
                    'unprocessed' => 'Unprocessed',
                    'refused'     => 'Refused',
                );

                echo form_dropdown('request_filter', $options, $curr_request_filter, "class='form-control valid'  id='request_filter_element' onchange='check()'  ");

                ?>
            </div>
        </div>
        <br/>
        <br/>
    <?php echo form_close(); ?>

    <?php
    if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
        ?>
        <div class="alert alert-success">
            <?php echo $messages['success_message'] ?>
        </div>
    <?php
    }


    if ($num_per_page > $num_requests) {
        $num_per_page = $num_requests;
    }


    if (!empty($mobile_data_requests)) {
        echo "<div class='pull-right'>$showing</div>";
        $tmpl = array ( 'table_open'  => '<table class="table">' );
        $this->table->set_template($tmpl);


        $this->table->set_heading(array('Client', 'Product info', 'Account info', 'Request date','Status', 'Actions'));




        foreach ($mobile_data_requests as $request) {
           /*
            $product_id = $product_data['id'];//product id
            $name = $product_data['name'];//product name
            $active = $product_data['visibility'];  // visibility  hide or visable
            if($active == '1'){
                $active ='Visible';
            }else{
                $active ='Hidden';
            }
            $price = $product_data['price'];//price
            $parent = $product_data['product_parent'];//product class
            $billing_cycle = $product_data['billing_cycle'];//billing cycle monthly  yearly once-off
            $class = $product_data['product_class'];//product class
            $package_speed = $product_data['package_speed'];//package speed
            $service_level = $product_data['service_level'];//dervice level
            $recommended_use = $product_data['recommended_use'];//recommanded use
            $global_backbone = $product_data['global_backbone'];//global backbone
            $automatic_creation = $product_data['automatic_creation'];// 0 or 1 manual/auto-create
            if($automatic_creation == '0'){
                $automatic_creation = 'Manual';
            }else{
                $automatic_creation = 'Auto-Create';
            }
            $default_comment = $product_data['default_comment'];//default comment
   */
/*

            [request_id] => 6
            [request_date] => 2015-11-29 13:34:18
            [first_response_date] =>
            [last_modification_time] =>
            [user_id] => 8901
            [username] => test-vvv
            [order_id] => 5547
            [status] => pending
            [notice] =>
            [mobile_sim] =>
            [mobile_details] =>
            [product_id] => 44
            [account_username] => test-refresh112
            [account_realm] => openweb.adsl
            [product_name] => 1Mbps Home Uncapped ADSL
            [first_name] => test1
            [last_name] => test2

*/
            $client = $request['first_name'] . " " . $request['last_name'] . " (" . $request['username'] . ")";
            $product_info = $request['product_name'];
            $request_date = $request['request_date'];
            $account_info = $request['account_username'] . "@" . $request['account_realm'];
            $status = $request['status'];


            $manage_page = anchor("admin/edit_mobile_data_request/" . $request['request_id'] , 'Edit', 'class="btn btn-sm btn-primary"');
           // $delete_page = anchor("admin/delete_product/$product_id", 'Delete', 'class="btn btn-sm btn-danger"');
           // $view_page = anchor("admin/view_product/$product_id", 'View', 'class="btn btn-sm btn-primary"');
            $action = $manage_page;
            $action = array('data' => $action, 'style' => 'width:200px;');

            //  $this->table->set_heading(array('Client', 'Product info', 'Account info', 'Request date','Status', 'Actions'));



            $this->table->add_row( array( $client, $product_info, $account_info, $request_date, $status,  $action));
        }

        echo $this->table->generate();

        echo "<div class='pull-right'>$pages</div>";
    }else{
        ?>
        <div class="alert alert-warning">
            It seems that there is no Mobile Data requests.
        </div>
    <?php
    }
    ?>
</fieldset>