<script>
    function check(){
        var r = $('#select-r').val();
       
        if(r){
            window.location.href = "/index.php/admin/all_subcategory/"+r;
        }else{
            return false;
        }
    }
</script>
<h3><?php echo $subcategory_type ?> Subcategory</h3>
<?php
echo form_open('admin/all_subcategory', array('class' => '','id' => 'form_filter_product')); ?>
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
if ($num_per_page > $num_subcategory) {
	$num_per_page = $num_subcategory;
}
echo "<div class='pull-right'>$showing</div>";

//var_dump($subcategories);die();

if (!empty($subcategories)) {
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('Subcategory Name', 'Description','Category Name','slug','Edit'));
	
	foreach ($subcategories as $subcategories_id => $subcategories_data) {
		$subcategory_id = $subcategories_data['id'];
		$subcategory_name = $subcategories_data['name'];
		$subcategory_desc = $subcategories_data['desc'];
		$subcategory_slug = $subcategories_data['slug'];
		$category_name = $subcategories_data['parent'];

		if(isset($type) && $type == 'reseller') {
            $manage_page = anchor("admin/edit_subcategory/$subcategory_id"."/r", 'Edit', 'class="btn btn-sm btn-primary"');
        } else {
            $manage_page = anchor("admin/edit_subcategory/$subcategory_id", 'Edit', 'class="btn btn-sm btn-primary"');
        }
		
		$this->table->add_row( array( $subcategory_name,$subcategory_desc,$category_name, $subcategory_slug, $manage_page));
	}
	
	echo $this->table->generate();
	
	echo "<div class='pull-right'>$pages</div>";
}
?>