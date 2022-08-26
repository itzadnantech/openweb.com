<script type="text/javascript" language="javascript">
$(document).ready(function() {
	$("#form_search").validate({
		rules: {
			pro_name : "required",
		}
	});
});

function check(){ 
	var r = $('#select-r').val();
	if(r){
		$("#form_filter_product").submit();
	}else{
		return false;
	}
}

</script>
<h3>All Products</h3>
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
<br/>

<fieldset>
    <legend>Product List</legend>


        <?php echo form_open('admin/filter_product', array('class' => '','id' => 'form_filter_product')); ?>

            <div class="form-group">
                <label class="control-label col-lg-2">Billing Cycle:</label>
                <div class="col-lg-3">
                    <select class="form-control valid" name="cycle" id="select-r" onchange="check()">
                        <option value="all">ALL</option>
                        <?php

                            if (isset($cycle)) {
                                foreach($cycle as $r) {
                                    //$i = $r['billing_cycle'];
                                    //$s = ucfirst($r['billing_cycle']);
                                    $i = $r;
                                    $s = ucfirst($r);
                                    if (isset($curr_cycle) && $i == $curr_cycle) {
                                        $selected = 'selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    echo "<option $selected value='$r'>$s</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            <br/>
            <br/>
            <div class="form-group">

                    <label class="control-label col-lg-2">Visibility filter :</label>
                    <div class="col-lg-3">
                        <?php
                            $options = array(
                                'visible'  => 'Visible',
                                'hidden'   => 'Hidden',
                                'all'      => 'ALL',

                            );

                         $dropdown_params =  "id='select-r' class='form-control valid' onchange='check()'";
                         echo form_dropdown('visibility', $options, $curr_visibility, $dropdown_params);

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


    if ($num_per_page > $num_product) {
        $num_per_page = $num_product;
    }


    if (!empty($products)) {
        echo "<div class='pull-right'>$showing</div>";
        $tmpl = array ( 'table_open'  => '<table class="table">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('Product Name', 'Visibility', 'Price', 'Product Parent','Actions'));

        $edit_additional_param = '';
        if (isset($nosvc_flag) && $nosvc_flag == true){
            $edit_additional_param = '/1';
        }


        foreach ($products as $product_id => $product_data) {
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

            if(isset($reseller) && $reseller == 1) {
                $delete_page = anchor("admin/delete_product/$product_id"."/r", 'Delete', 'class="btn btn-sm btn-danger"');
                $view_page = anchor("admin/view_product/$product_id"."/r", 'View', 'class="btn btn-sm btn-primary"');
                $manage_page = anchor("admin/edit_product/$product_id/0/r/" . $edit_additional_param, 'Edit', 'class="btn btn-sm btn-primary"');
                $duplicate_page = anchor("admin/edit_product/$product_id" .'/1/r', 'Duplicate', 'class="btn btn-sm btn-primary"');
            } else {
                $delete_page = anchor("admin/delete_product/$product_id", 'Delete', 'class="btn btn-sm btn-danger"');
                $view_page = anchor("admin/view_product/$product_id", 'View', 'class="btn btn-sm btn-primary"');
                $manage_page = anchor("admin/edit_product/$product_id" . $edit_additional_param, 'Edit', 'class="btn btn-sm btn-primary"');
                $duplicate_page = anchor("admin/edit_product/$product_id" .'/1', 'Duplicate', 'class="btn btn-sm btn-primary"');
            }


            $action =$view_page.' '.$manage_page.' '.$delete_page. ' ' .$duplicate_page;
            $action = array('data' => $action, 'style' => 'width:256px;');
            $this->table->add_row( array( $name, $active, $price, $parent,$action));
        }

        echo $this->table->generate();

        echo "<div class='pull-right'>$pages</div>";
    }else{
    ?>
    <div class="alert alert-warning">
        <strong>Product not found.</strong> It seems that there is no product with this product name!
    </div>
    <?php
    }
    ?>
</fieldset>