<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$('#create_category_form').validate({
			rules: {
				name :{
					required : true,
					remote:{
						url : "<?php echo site_url('admin/validate_category_name')?>",
						type : 'post',
						data :{ 
							category_name : function(){return $("#name").val();},
							category_id : function(){return $('#category_id').val();}
						}	
					  },
				},
				slug : {
					required : true,
				}
			}
		});

		$('#name').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
		$('#slug').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");
	});
</script>
<?php

if ($this->uri->segment(2) == 'create_category') {
	$new_category = 1;
	$category_id = '';
} else {
	if (!empty($category_data)) {
		$category_id = $category_data['id'];
	}
	$new_category = 0;
}

if ($new_category) {
	$btn_label = 'Create this Category';
	
	$category_settings = array();
	if (!empty($category_fields)) {
		foreach ($category_fields as $f) {
			$category_data[$f] = '';
		}
	}
} else {
	$btn_label = 'Update Category Information';
}


$input_size = array (
	'name' => 'col-lg-4',
	'slug' => 'col-lg-3',
    'visible' => 'col-lg-3',
);
$input_small = array (
	'slug' => '<br/><small>E.g. wifi, business-dsl',
);

if (isset($edit_category) && !empty($category_data)) {

	$category_settings = $category_data;
	?>
	
	<?php
	echo form_open('admin/update_category', array('class' => 'form-horizontal','id' => 'create_category_form'));
	echo form_hidden('id', $category_id); 
	?>
	
		<?php if(isset($category_id)){
			echo '<input type="hidden" value="'.$category_id.'" id="category_id" name="category_id"/>';
		}else{
			echo '<input type="hidden" value="" id="category_id" name="category_id"/>';
		}?>
		
		<legend>Category Details</legend>
		<fieldset>
		<?php

        $visible_options = array(
            '1'  => 'Visible',
            '0'  => 'Not visible',
        );

        $type_options = [
            'reseller' => 'Reseller',
            'client' => 'Client'
        ];


		foreach ($category_fields as $f=>$l) {
			// Defaults:
			$id = $f;
			if (isset($input_size[$f])) {
				$input_class = $input_size[$f];
			} else {
				$input_class = 'col-lg-6';
			}
			if (isset($input_small[$f])) {
				$small = $input_small[$f];
			} else {
				$small = '';
			}
			$group_class = 'control-group';
			$prepended = '';
			$textarea = FALSE;
			$appended = '';


			
			if (isset($category_settings[$f])) {
				$v = $category_settings[$f];
			} else {
				$v = '';
			}
		?>
		<div class="form-group">
			<?php 
			echo form_label($l . $small, $f, array ('class'=> 'control-label col-lg-3')); 
			?>
			<div class="<?php echo $input_class ?>">
			<?php
			echo $prepended;
			if ($textarea) {
				echo form_textarea(
					array(
						'class' => 'form-control',
						'name' => $f,
						'placeholder' => '',
						'id' => $id,
						'value' => $v,
						'rows' => 3,
					)
				);
			} else {

                if ($f == 'visible'){

                    echo form_dropdown('visible', $visible_options, $v, "class='form-control' id='$f'");
                } elseif($f == 'type') {
                    echo form_dropdown('type', $type_options, $v, "class='form-control' id='$f'");
                } else {

                    echo form_input(
                        array(
                            'class' => 'form-control',
                            'name' => $f,
                            'placeholder' => '',
                            'id' => $id,
                            'value' => $v
                        )
                    );

                }

			}
			echo $appended;
			?>
			</div>
		</div>
		<?php
		}
		
		?>
	<div class="control-group">
		<div class="controls">
		<?php if (!$new_category) { ?>
		<input type="hidden" name="username" value="<?php echo $edit_category; ?>" />
		<?php } ?>
		<div style="text-align:center;">
		<?php echo form_submit(array ('class' => 'btn btn-large btn-primary', 'value' => $btn_label,)); ?>
		</div>
		</div>
	</div>
	</fieldset>
	
	<?php
} else {

}
?>