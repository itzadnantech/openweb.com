<h3>Sent Awards</h3>

<?php echo form_open('admin/filter_sent_avios_award', array('class' => 'form-inline','id' => 'form_filter_award')); ?>
<div class="form-group">
    <label class="control-label col-lg-1">Award Type:</label>
    <div class="col-lg-2">
        <select class="form-control valid" name="billing" id="select-r" onchange="check()">
            <option value="all">All</option>
            <?php
            if (isset($billing_list)) {
                foreach($billing_list as $key => $r) {
                    $i = $r;
                    $s = ucfirst($r);
                    if (isset($billing) && $key == $billing) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option $selected value='$key'>$s</option>";
                }
            }
            ?>
        </select>
    </div>

    <label class="control-label col-lg-1">Status:</label>
    <div class="col-lg-2 ">
        <select class="form-control valid" name="status" id="select-s" onchange="check()">
            <option value="all">All</option>
            <?php
            if (isset($sent['status_list'])) {
                foreach($sent['status_list'] as $u => $r) {
                    $i = $r['status'];
                    $s = ucfirst($r['status']);
                    if (isset($status) && $i == $status) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option $selected value='$i'>$s</option>";
                }
            }
            ?>
        </select>
    </div>
    <label class="control-label col-lg-1">Month:</label>
    <div class="col-lg-2 ">
        <select class="form-control valid" name="month" id="select-m" onchange="check()">
            <?php
            if (isset($sent['month_list'])) {
                foreach($sent['month_list'] as $val => $name) {

                    if (isset($month) && $val == $month) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option $selected value='$val'>$name</option>";
                }
            }
            ?>
        </select>
    </div>
    <label class="control-label col-lg-1">Year:</label>
    <div class="col-lg-2 ">
        <select class="form-control valid" name="year" id="select-y" onchange="check()">
            <?php
            if (isset($sent['year_list'])) {
                foreach($sent['year_list'] as $year) {
                    if (isset($current_year) && $year == $current_year) {
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
<?php echo form_close(); ?>
<br/><br/>
<?php
if ($num_per_page > $sent['num_account']) {
    $num_per_page = $sent['num_account'];
}

if (!empty($sent['accounts'])) {
    echo "<div class='pull-right'>". $sent['showing'];
    echo "</div>";
    $tmpl = array ( 'table_open'  => '<table class="table">' );
    $this->table->set_template($tmpl);
    $this->table->set_heading(array('User ID', 'Points','Bonus points' ,'Date', 'Status', 'Type'));

    foreach ($sent['accounts'] as $account_id => $account_data) {
        $user_id = $account_data['user_id'];
        //$order_id = $account_data['order_id'];
        $points = $account_data['points'];
        $bonus = $account_data['bonus_points'];
        $date = date('d/m/Y', strtotime($account_data['date']));
        $status = $account_data['status'];

        $type = $account_data['billing_code'];

        if(array_key_exists($account_data['billing_code'], $billing_list)) {
            $type = $billing_list[$account_data['billing_code']];
        }

        $award_id = $account_data['award_id'];

        $this->table->add_row( array( $user_id, $points, $bonus, $date, $status, $type));
    }

    echo $this->table->generate();
    echo "<div class='pull-right'>".$sent['pages']."</div>";
}else{
    ?>
    <div class="alert alert-warning">
        <strong>The system could not find any Avios Awards issued within the chosen period.</strong> <?php echo $priority_flag['message']; ?>
    </div>
    <?php
}
?>