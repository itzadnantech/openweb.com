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
<h3>All TopUps</h3>
<fieldset>
    <legend>Search For TopUp</legend>
    <?php
        $topup_val_name = '';
        if (!empty($topup_name))
            $topup_val_name = $topup_name;

    ?>

    <?php echo form_open('/admin/all_topup', array('method'=>'get', 'class' => 'form-horizontal','id'=> 'form_search')); ?>
    <div class="form_group">
        <label class="control-label col-lg-2">TopUp Name:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="topup_name" id="topup_name_element" value="<?php echo $topup_val_name; ?>">
        </div>
        <div class="col-lg-1">
            <input type="submit" class="btn btn-sm btn-primary" value="Search" >
        </div>
    </div>
    <?php echo form_close();?>
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
        $this->table->set_heading(array('TopUp Name', 'Class', 'Price', 'Actions'));

        foreach ($topup_list as $topup_row) {

            $id          = $topup_row['topup_id'];
            $name        = $topup_row['topup_name'];
            $class_name  = $topup_row['class_name'];
            $price       = 'R '. $topup_row['topup_price'];


            $manage_page = anchor("admin/edit_topup/$id", 'Edit', 'class="btn btn-sm btn-primary"');
            $delete_page = anchor("admin/delete_topup/$id", 'Delete', 'class="btn btn-sm btn-danger" ');


            $action = $manage_page.' '.$delete_page;
            $action = array('data' => $action, 'style' => 'width:200px;');
            $this->table->add_row( array( $name, $class_name, $price, $action));
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