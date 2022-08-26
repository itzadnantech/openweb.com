<h3>Month Summary</h3>

<?php echo form_open('admin/avios_summary', array('class' => 'form-inline','id' => 'form_filter_award')); ?>

<div class="form-group">
    <label class="control-label col-lg-2">Month:</label>
    <div class="col-lg-2">
        <select class="form-control valid" name="month" id="select-r" onchange="check()">

            <?php
            if (isset($month_list)) {
                foreach($month_list as $id => $name) {
                    if (isset($prev_month) && $id == $prev_month) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option $selected value='$id'>$name</option>";
                }
            }
            ?>
        </select>
    </div>

    <label class="control-label col-lg-2">Year:</label>
    <div class="col-lg-2">
        <select class="form-control valid" name="year" id="select-r" onchange="check()">

            <?php
            if (isset($year_list)) {
                foreach($year_list as $year) {
                    if (isset($cur_year) && $year == $cur_year) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option $selected value='$year'>$year</option>";
                }
            }
            ?>
        </select>
    </div>
</div>
<?php
echo form_close();?>
<br/><br/>
<?php echo $this->table->generate($table_data); ?>