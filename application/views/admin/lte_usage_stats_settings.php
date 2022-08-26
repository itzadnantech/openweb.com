<?php
    $fields = array (
        'data_type' => 'Data Type',
        'category' => 'Category',
        'title' => 'Title',
        'total_data' => 'Total Data',
        'remaining_data' => 'Remaining Data',
        'last_update' => 'Last Update',
        'activation_date' => 'Activation Date',
        'expire_date' => 'Expire Date'
    );
?>

<?php echo form_open('admin/save_lte_usage_stats_settings', array('class' => 'form-horizontal','id' => 'lte_usage_stats_settings')); ?>
    <fieldset>
        <legend>LTE Usage Stats Settings</legend>

        <?php
            foreach ($fields as $key => $field) {
        ?>
            <div class="form-group">
                <?php echo form_label($field, $key, array ('class'=> 'control-label col-lg-2')); ?>
                <div class="col-lg-1">
                    <?php
                        echo form_checkbox(
                            array(
                                'name' => $key,
                                'placeholder' => '',
                                'id' => $key,
                                'checked' => $lte_usage_stats_model->toDisplay($key)
                            )
                        );
                    ?>
                </div>
            </div>
        <?php
            }
        ?>

        <div style="text-align:center;letter-spacing: 150px;">
            <?php echo form_submit(array ('class' => 'btn btn-primary btn-lg', 'value' => 'Save',)); ?>
        </div>
    </fieldset>
<?php echo(form_close()); ?>