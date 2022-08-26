<h3>Prepared</h3>

<?php echo form_open('admin/filter_avios_award', array('class' => 'form-inline','id' => 'form_filter_award')); ?>
<div class="form-group">
    <label class="control-label col-lg-1">Award Type:</label>
    <div class="col-lg-3">
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
    <div class="col-lg-3 ">
        <select class="form-control valid" name="status" id="select-s" onchange="check()">
            <option value="all">All</option>
            <?php
            if (isset($status_list)) {
                foreach($status_list as $u => $r) {
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
</div>
<?php echo form_close(); ?>
<br/><br/>
<?php
if ($num_per_page > $num_account) {
    $num_per_page = $num_account;
}

if (!empty($accounts)) {
    echo "<div class='pull-right'>$showing</div>";
    $tmpl = array ( 'table_open'  => '<table class="table">' );
    $this->table->set_template($tmpl);
    $this->table->set_heading(array('User ID', 'Order ID', 'Points','Bonus points' ,'Date', 'Status', 'Type', 'Actions'));

    foreach ($accounts as $account_id => $account_data) {
        $user_id = $account_data['user_id'];
        $order_id = $account_data['order_id'];
        $points = $account_data['points'];
        $bonus = $account_data['bonus_points'];
        $date = date('d/m/Y', strtotime($account_data['date']));
        $status = $account_data['status'];

        $type = $account_data['billing_code'];

        if(array_key_exists($account_data['billing_code'], $billing_list)) {
            $type = $billing_list[$account_data['billing_code']];
        }

        $award_id = $account_data['prep_id'];

        $manage_page = anchor("admin/edit_avios_award/$award_id", 'Edit', 'class="btn btn-sm btn-primary"');
        $delete_page = anchor("admin/delete_avios_award/$award_id", 'Delete', 'class="btn btn-sm btn-danger" id="d_"'.$award_id);

        //$actions = "$manage_page $delete_page";
        //$actions = array('data' => $actions, 'style' => 'width:500px;');
        $this->table->add_row( array( $user_id, $order_id, $points, $bonus, $date, $status, $type, $manage_page, $delete_page));
    }

    echo $this->table->generate();
    echo "<div class='pull-right'>$pages</div>";
}else{
    ?>
    <div class="alert alert-warning">
        <strong>Awards not found.</strong> <?php echo $priority_flag['message']; ?>
    </div>
    <?php
}
?>