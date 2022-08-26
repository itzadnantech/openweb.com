<style>
    .btn-mg {
        margin: 5px;
    }
</style>

<h2>Bulk SMS</h2>
<div class="row">
    <div class="col-lg-offset-9 col-lg-3">
        <p>Available Credits: <?php echo $credits ?></p>
    </div>
</div>
<div class="row">
    <p>1. You should create user list of users that you want to send SMS OR use existed group.</p>
</div>
<div class="row">
    <?php echo form_open('admin/create_users_list', array('method'=>'post', 'class' => 'form-horizontal','id' => 'sms_form'));?>
        <fieldset>
            <h4>Filters</h4>
            <div class="row">
                <div class="col-lg-4">
                <div class="form-group">
                    <label class="control-label col-lg-6">User Status:</label>
                    <div class="col-lg-6">
                        <select name="user_status">
                            <?php
                            foreach ($user_status as $staus) { ?>
                                <option value="<?php echo $staus ?>"><?php echo $staus ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-6">Product type:</label>
                    <div class="col-lg-6">
                        <select name="order_service_type">
                            <?php
                            foreach ($product_type as $type) { ?>
                                <option value="<?php echo $type ?>"><?php echo $type ?></option>
                            <?php } ?>

                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-6">Order Status:</label>
                    <div class="col-lg-6">
                        <select name="order_status">
                            <?php
                            foreach ($order_status as $status) { ?>
                                <option value="<?php echo $status ?>"><?php echo $status ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="control-label col-lg-6">Some filter:</label>
                        <div class="col-lg-6">
                            <select>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-6">Some filter:</label>
                        <div class="col-lg-6">
                            <select>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-6">Some filter:</label>
                        <div class="col-lg-6">
                            <select>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" class="btn btn-primary btn-mg" value="Download">
        </fieldset>
    <?php echo form_close()?>
</div>
    <div class="row">
        <p>2. Create group on <a href="smsportal.co.za">smsportal</a> and upload users list to those group OR use existed one</p>
    </div>
    <div class="row">
        <p>3. Update this page</p>
    </div>
    <div class="row">
        <p>4. We ready to send bulk SMS to all users from this group</p>
    </div>


    <div class="row">
        <?php echo form_open('admin/send_bulk_sms', array('method'=>'post', 'class' => 'form-horizontal','id' => 'sms_form'));?>
            <fieldset>
                <div class="form-group">
                    <label class="control-label col-lg-2">Group Name:</label>
                    <div class="col-lg-10">
                        <select name="group_name">
                            <?php foreach ($groups as $id => $name) { ?>
                                <option value="<?php echo $id ?>"><?php echo $name ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-2">Message Text:</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" name="message" id="text" rows="2"></textarea>
                        <h6 class="pull-right" id="count_message"></h6>
                    </div>
                </div>
                <div class="form-group">

                    <div class="col-lg-10">
                        <input type="submit" class="btn-primary btn btn-mg" value="Send SMS" >
                    </div>
                </div>
            </fieldset>
        <?php echo form_close()?>
    </div>

<script type="text/javascript">
    var text_max = 0;
    $('#count_message').html(text_max + ' characters');

    $('#text').keyup(function() {
        var text_length = $('#text').val().length;

        $('#count_message').html(text_length + ' characters');
    });
</script>