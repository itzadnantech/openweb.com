<style>
    #example_wrapper .row:first-child {
        display: flex;
    }

    #example_filter {
        margin-left: 34em;
    }

    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    .progress {
        height: 16px !important;
        margin-bottom: 5px !important;
        margin-right: 10px !important;
    }
</style>

<?php if (isset($single_batch) && !empty($single_batch)) { ?>
    <div class="row">
        <div class="col-md-12">
            <a href="<?php echo base_url('index.php/admin/client_bulk_email_dashboard') ?>" class="btn btn-primary" style="float: right;">View Batch List</a>
        </div>

    </div>
    <table id="example" class="data-table table display" style="width:100%">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Email</th>
                <th>Username</th>
                <th>Status</th>

            </tr>
        </thead>
        <tbody>
            <?php if (isset($single_batch) && !empty($single_batch)) { ?>
                <?php foreach ($single_batch as $key => $value) { ?>
                    <tr>
                        <td><?php echo $value['batch_id'] ?></td>
                        <td><?php echo $value['email'] ?></td>
                        <td><?php echo $value['username'] ?></td>
                        <td><?php echo $value['status'] ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <table id="example" class="data-table table display" style="width:100%">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Total Users</th>
                <th>Batch Size</th>
                <th>Batch Time</th>
                <th>Sent Emails</th>
                <th>Email Status</th>
                <th>Batch Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($batch_data) && !empty($batch_data)) { ?>
                <?php foreach ($batch_data as $key => $value) { ?>
                    <?php
                    $email_status = 0;
                    $email_status = round(($value['total_sent_emails'] / $value['total_users']) * 100)

                    ?>
                    <tr>
                        <td><?php echo $value['batch_id'] ?></td>
                        <td><?php echo $value['total_users'] . ' Users' ?></td>
                        <td><?php echo $value['user_limit'] . ' Mails' ?></td>
                        <td><?php echo 'After ' . $value['time'] . ' Mins' ?></td>
                        <td><?php echo $value['total_sent_emails'] . ' Mails' ?></td>
                        <!-- progress bar -->
                        <td>
                            <?php if ($email_status <= 25) { ?>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $email_status; ?>%" aria-valuenow="<?php echo $email_status; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $email_status . '%'; ?></div>
                                </div>
                            <?php } elseif ($email_status > 25 && $email_status <= 50) { ?>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $email_status; ?>%" aria-valuenow="<?php echo $email_status; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $email_status . '%'; ?></div>
                                </div>
                            <?php } elseif ($email_status > 50 && $email_status <= 75) { ?>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $email_status; ?>%" aria-valuenow="<?php echo $email_status; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $email_status . '%'; ?></div>
                                </div>
                            <?php } else { ?>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $email_status; ?>%" aria-valuenow="<?php echo $email_status; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $email_status . '%'; ?></div>
                                </div>
                            <?php } ?>
                        </td>
                        <!-- progress bar end -->

                        <td><?php echo $value['status'] ?></td>
                        <td><a href="<?php echo base_url('index.php/admin/client_bulk_email_dashboard?batch_id=') . $value['batch_id'] ?>">Batch Details</a></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>