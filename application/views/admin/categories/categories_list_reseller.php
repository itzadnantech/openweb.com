<script>
    function check(){
        var r = $('#select-r').val();
        if(r){
            window.location.href = "/index.php/admin/all_category_reseller/"+r;
        }else{
            return false;
        }
    }
</script>
<h3>Reseller Categories</h3>
<?php

echo form_open('admin/all_category_reseller', array('class' => '','id' => 'form_filter_product')); ?>
<div class="form-group">

    <label class="control-label col-lg-2">Visibility filter :</label>
    <div class="col-lg-3">
        <?php
            $options = array(
                'all'      => 'ALL',
                'visible'  => 'Visible',
                'hidden'   => 'Hidden',
            );

         $dropdown_params =  "id='select-r' class='form-control valid' onchange='check()'";
         echo form_dropdown('visibility', $options, $selected_filter, $dropdown_params);
        ?>
    </div>
</div>
<?php echo form_close();

if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	?>
	<div class="alert alert-success">
		<?php echo $messages['success_message'] ?>
	</div>
	<?php
}
if ($num_per_page > $num_category) {
	$num_per_page = $num_category;
}
echo "<div class='pull-right'>$showing</div>";

if (!empty($categories)) {
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Category Name', 'Description','slug','Edit'));
	
	foreach ($categories as $categories_id => $categories_data) {
		$category_id = $categories_data['id'];
		$category_name = $categories_data['name'];
		$category_desc = $categories_data['desc'];
		$category_slug = $categories_data['slug'];

		if(isset($type) && $type == 'reseller') {
            $manage_page = anchor("admin/edit_category/$category_id"."/r", 'Edit', 'class="btn btn-sm btn-primary"');
        } else {
            $manage_page = anchor("admin/edit_category/$category_id", 'Edit', 'class="btn btn-sm btn-primary"');
        }

		$this->table->add_row( array( $category_name,$category_desc,$category_slug, $manage_page));
	}
	
	echo $this->table->generate();
	
	echo "<div class='pull-right'>$pages</div>";
}
?>