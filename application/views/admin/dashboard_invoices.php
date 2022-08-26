<h3>Monthly invoices </h3>
<a href="<?php echo base_url(); ?>admin/dashboard" class="btn btn-default">Back to dashboard</a>
<?php
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
    ?>
    <div class="alert alert-success">
        <?php echo $messages['success_message'] ?>
    </div>
<?php
}
if (isset($messages['warn_message']) && trim($messages['warn_message']) != '') {
    ?>
    <div class="alert alert-danger">
        <?php echo $messages['warn_message'] ?>
    </div>
<?php
}
?>
<br/><br/>
<!-- ------------------------------------------------------------------------------------>
<?php
if (!empty($report_rows))
    echo $report_rows;