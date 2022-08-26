<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$('#create_subcategory_form').validate({
			rules: {
				name :{
					required : true,
					remote:{
						url : "<?php echo site_url('admin/validate_subcategory_name')?>",
						type : 'post',
						data :{ 
							subcategory_name : function(){return $("#name").val();},
							subcategory_id : function(){return $('#subcategory_id').val();}
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
		$('#parent').parent().parent().append("<div style='color:#f62b2b;font-size:25px;'>*</div>");

		$('#type').change(function() {

		    if($('#type').val() == 'reseller') {
                $('#parent').hide();
                $('#parent_r').show();
            }

            if($('#type').val() == 'client') {
                $('#parent').show();
                $('#parent_r').hide();
            }
        });

        if($('#type').val() == 'reseller') {
            $('#parent').hide();
            $('#parent_r').show();
        }

        if($('#type').val() == 'client') {
            $('#parent').show();
            $('#parent_r').hide();
        }
	});
</script>
<?php
if ($this->uri->segment(2) == 'create_subcategory') {
	$new_subcategory = 1;
	$subcategory_id = '';
} else {
	if (!empty($subcategory_data)) {
		$subcategory_id = $subcategory_data['id'];
	}
	$new_subcategory = 0;
}

//list all categories
$categories_list = array();
if (!empty($all_categories)) {
	foreach ($all_categories as $c) {
		$category_id = $c['id'];
		$s = $c['slug'];
		$n = $c['name'];
		$categories_list[$category_id] = $n; //parent = category_id
	}
}

$categories_list_res = array();
if (!empty($all_categories_reseller)) {
    foreach ($all_categories_reseller as $c) {
        $category_id = $c['id'];
        $s = $c['slug'];
        $n = $c['name'];
        $categories_list_res[$category_id] = $n; //parent = category_id
    }
}

if ($new_subcategory) {
	$btn_label = 'Create this Sub-Category';
	
	$subcategory_settings = array();
	if (!empty($subcategory_fields)) {
		foreach ($subcategory_fields as $f) {
			$subcategory_data[$f] = '';
		}
	}
} else {
	$btn_label = 'Update Sub-Category Information';
}

$type_options = [
    'client' => 'Client',
    'reseller' => 'Reseller'
];


$input_small = array(
	'slug' => '<br/><small>E.g. Vanilla Uncapped ADSL',
);
$input_size = array();

if (isset($edit_subcategory) && !empty($subcategory_data)) {

	$subcategory_settings = $subcategory_data;
	echo form_open('admin/update_subcategory', array('class' => 'form-horizontal','id' => 'create_subcategory_form'));
	echo form_hidden('id', $subcategory_id); 
?>
		<legend>Category Details</legend>
		
		<?php if(isset($subcategory_id)){
			echo '<input type="hidden" value="'.$subcategory_id.'" id="subcategory_id" name="subcategory_id"/>';
		}else{
			echo '<input type="hidden" value="" id="subcategory_id" name="subcategory_id"/>';
		}?>
		<fieldset>
		<?php

        $visible_options = array(
            '1'  => 'Visible',
            '0'  => 'Not visible',
        );

        $input_size = array (
            'visible' => 'col-lg-3',
        );

		foreach ($subcategory_fields as $f=>$l) {
			// Defaults:
			$id = $f;
			if (isset($input_size[$f])) {
				$input_class = $input_size[$f];
			} else {
				$input_class = 'col-lg-5';
			}
			$group_class = 'control-group';
			$prepended = '';
			$textarea = FALSE;
			if (isset($input_small[$f])) {
				$small = $input_small[$f];
			} else {
				$small = '';
			}
			if (isset($subcategory_settings[$f])) {
				$v = $subcategory_settings[$f];
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
			if ($f == 'parent') {
                echo form_dropdown('parent_r', $categories_list_res, $subcategory_data['parent_id'], 'class="form-control" id = "parent_r"');
                echo form_dropdown('parent', $categories_list, $subcategory_data['parent_id'], 'class="form-control" id = "parent"');
			} else {
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
                        echo form_dropdown('type', $type_options, $subcategory_settings['type'], "class='form-control' id='$f'");
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
			}
			?>
			</div>
		</div>
		<?php
		}
		?>
		<?php if (!$new_subcategory) { ?>
		<input type="hidden" name="username" value="<?php echo $edit_subcategory; ?>" />
		<?php } ?>
		<div style="text-align:center">
		<?php echo form_submit(array ('class' => 'btn btn-large btn-primary', 'value' => $btn_label,)); ?>
		</div>
	</fieldset>
	
	<?php
} else {

}
?>