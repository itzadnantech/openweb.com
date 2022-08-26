 <h3>Mobile Stats Requests</h3>
<div class="row">
    <div id="divResults" class='alert alert-success'></div>
    <button type="button" name="telkomClearBtn" id="telkomClearBtn" data-order-clear-all="mobile" value=" Clear Button" class="btn btn-warning" title="Note:After pressing this button all records are removed form page.">Clear Records</button>

    <table class="table table-striped" id="telkom-lte-req-tbl">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Request Date</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>OW Number</th>
                <th>Mobile SIM Username</th>
                <th>SIM Serial Number</th>
                <th>Request Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mob_stat_request as $trs) : ?>
                <tr>
                    <th><?= $trs['mobile_user_code'] ?></th>
                    <td><?= $trs['mobile_request_date'] ?></td>
                    <td><?= $trs['first_name'] ?></td>
                    <td><?= $trs['last_name'] ?></td>
                    <td><?= $trs['email_address'] ?></td>
                    <td><?= $trs['ow'] ?></td>
                    <td><?= $trs['fibre_data_username'] ?></td>
                    <td><?= $trs['sim_serial_no'] ?></td>
                    <td>
                        <?php
                        if ($trs['mobile_status'] == 'REQUESTED') {
                            echo "<strong style='color:blue'>" . $trs['mobile_status'] . "</strong>";
                        } elseif ($trs['mobile_status'] == 'RESETED') {
                            echo "<strong style='color:red'>" . $trs['mobile_status'] . "</strong>";
                        } elseif ($trs['mobile_status'] == 'MAILED') {
                            echo "<strong style='color:green'>" . $trs['mobile_status'] . "</strong>";
                        }
                        ?>
                    </td>
                    <td>
                        <div style="display:flex;">
                            <button type="button" class="btn btn-info btn-sm send_telkom_stats_btn" data-send-order-id="<?php echo $trs['mobile_user_code']; ?>" data-send-email-id="<?php echo $trs['email_address']; ?>" data-send-name-id="<?php echo $trs['first_name'] . ' ' . $trs['last_name']; ?>" data-toggle="modal" data-order-type="mobile" data-prev-simnumber="<?= $trs['sim_serial_no'] ?>" data-prev-minutes="<?= $trs['mobile_total_minutes'] ?>" data-prev-data="<?= $trs['mobile_total_data'] ?>" data-prev-sms="<?= $trs['mobile_total_sms'] ?>" data-target="#myModal">Send Stats</button>
                            <button type="button" class="btn btn-danger btn-sm order_reset_btn" data-reset-order-type="mobile" data-order-id="<?php echo $trs['mobile_user_code']; ?>">Reset</button>
                            <button type="button" onclick="return confirm('Are you sure you want to delete this record ?');" class="btn btn-warning btn-sm order_delete_btn" data-reset-order-type="mobile" data-order-id="<?php echo $trs['mobile_user_code']; ?>">Delete</button>
                        </div>
                        <?php if ($trs['mob_rec_status'] == 'Topup Request') : ?>
                            <button type="button" data-toggle="modal" data-target="#myModalTopupRequest" class="btn btn-success btn-sm order_topup_request_view_btn" style="margin-top:10px;" data-brought-topup="<?= $trs['mob_rec_topup_name']; ?>" data-brought-topup-price="<?= $trs['mob_rec_amount']; ?>" data-brought-topup-date="<?= $trs['mob_rec_date']; ?>" data-brought-topup-id="<?= $trs['tel_rec_id']; ?>" data-brought-send-email-id="<?php echo $trs['email_address']; ?>" data-brought-send-name-id="<?php echo $trs['first_name'] . ' ' . $trs['last_name']; ?>" data-brought-order-id="<?= $trs['mobile_user_code']; ?>">Topup Request</button>

                        <?php elseif ($trs['mob_rec_status'] == 'Topup Loaded') : ?>
                            <strong style="margin-top:5px;color:green;"><?= $trs['mob_rec_status'] ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Stats Details</h4>
                </div>
                <div class="modal-body">
                    <form id="mobile-stats-form">
                        <div class="form-group">
                            <input type="hidden" name="order_id" id="hidden_order_id" value="" />
                            <input type="hidden" name="user_email" id="hidden_user_email" value="" />
                            <input type="hidden" name="user_name" id="hidden_user_name" value="" />
                            <input type="hidden" name="order_type" id="hidden_order_type_name" value="mobile" />
                            <input type="hidden" name="simnumber" id="hidden_simnumber" value="" />
                        </div>
                        <div class="form-group">
                            <label>Minutes Used:</label>
                            <input type="number" name="minutes" class="form-control">
                            <strong>Previous Minutes Used :</strong> <strong id="prev_minutes"></strong>
                        </div>
                        <div class="form-group">
                            <label>GB Used:</label>
                            <input type="number" name="data" step="0.01" class="form-control">
                            <strong>Previous GB Used :</strong> <strong id="prev_data"></strong>
                        </div>
                        <div class="form-group">
                            <label>SMS Used:</label>
                            <input type="number" name="sms" class="form-control">
                            <strong>Previous SMS Used :</strong> <strong id="prev_sms"></strong>
                        </div>
                        <button type="submit" id="mobileSaveBtn" class="btn btn-primary btn-md">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <div id="myModalTopupRequest" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Requested Topup Details</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Topup Name</th>
                            <td id="table_topup_name"></td>
                        </tr>
                        <tr>
                            <th>Amount paid</th>
                            <td id="table_topup_price"> </td>
                        </tr>
                        <tr>
                            <th>Transaction Date</th>
                            <td id="table_transaction_date"> </td>
                        </tr>
                    </table>
                </div>
                <form id="mobile-topuploaded-form" style="margin-left: 25px;">
                    <input type="hidden" name="topuploaded_id" />
                    <input type="hidden" name="topuploaded_name" />
                    <input type="hidden" name="topuploaded_price" />
                    <input type="hidden" name="topuploaded_buyer_name" />
                    <input type="hidden" name="topuploaded_buyer_email" />
                    <input type="hidden" name="topuploaded_order_id" />

                    <button type="submit" id="telkomTopupSuccessBtn" class="btn btn-primary btn-md">Topup Loaded</button>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>