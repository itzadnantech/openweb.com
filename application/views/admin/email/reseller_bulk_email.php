 <script type="text/javascript" language="javascript">
    var interval = null;
    $(document).ready(function() {
        $('#edit_email_form').validate({
            rules: {
                title: {
                    required: true,
                },
                content: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
            }
        });

        // $("#subm_resl").click(function(event) {
        //     event.preventDefault();
        //     $("#couter_mod").modal({
        //         backdrop: 'static'
        //     });
        //     var id = $("#email_id").val();
        //     console.log("id" + id)
        //     $.ajax({
        //         type: "POST",
        //         url: "/admin/reseller_bulk_mail",
        //         data: {
        //             email_idx: id
        //         }
        //     });

        //     interval = setInterval(getMessageCount, 1000);
        // });

        $("#subm_resl").click(function(event) {
            event.preventDefault();
            $("#couter_mod").modal({
                backdrop: 'static'
            });
            var id = $("#email_id").val();
            var n_users = $("#n_users").val();
            var email_time = $("#email_time").val();

            $.ajax({
                type: "POST",
                url: "/admin/reseller_bulk_mail",
                data: {
                    email_id: id,
                    n_users: n_users,
                    email_time: email_time
                },
                success: function(data) {
                    let res = JSON.parse(data);
                    switch (res.code) {
                        case 'success':
                            console.log('success');
                            getMessageCount();
                            setTimeout(function() {
                                $("#couter_mod").modal("hide");
                                window.location.href = 'https://home.openweb.co.za/index.php/admin/reseller_bulk_email_dashboard';
                            }, 3000)
                            break;
                        case 'warning':
                            $('#error-message').css('display', 'block');
                            $('#error-message').html(res.message);
                            setTimeout(function() {
                                $('#error-message').css('display', 'none');
                                $('#error-message').html('');
                            }, 15000)


                            break;
                        case 'error':
                            console.log('error');

                            break;


                    }
                }
            });

            // interval = setInterval(getMessageCount, 10000);
        });

    });

    function getMessageCount() {
        $.ajax({
            url: "/admin/get_message_count"
        }).success(function(data) {

            var all = $("#all").html();
            var bar = (data / all) * 100;

            $("#sent").html(data);

            $(".progress-bar").css("width", bar + "%");

            if (all == data) {
                $(".progress-bar").text("complete");
                clearInterval(interval);
                $("#ok_btn").show();

                $.ajax({
                    url: "/admin/get_bulk_email_result"
                }).success(function(data) {
                    data = JSON.parse(data);
                    $("#s_sent").html(data.success);
                    $("#e_sent").html(data.all - data.success);
                    $("#sent_result").show();
                })
            }
        });
    }

    function check() {
        var a = $('#select-e').val();
        if (a) {
            $("#email_template_form").submit();
        } else {
            return false;
        }
    }

    function hideModal() {
        $("#couter_mod").modal("hide");
    }
</script>

<h3>Reseller Bulk mailing</h3>
<br />
<label>Reseller Emails: Total: <?= $users ?></label>
<select class="form-control">
    <?php foreach ($user_email as $user_e) : ?>
        <option><?= $user_e['email_address'] ?></option>
    <?php endforeach; ?>
</select>
<?php

if (isset($email_detail) && !empty($email_detail)) {

    echo form_open('admin/reseller_bulk_mail', array('class' => 'form-horizontal', 'id' => 'edit_email_form'));

    if (!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }

    if (!empty($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    }
    $guider = '';
    foreach ($email_detail as $email => $email_data) {
        $purpose = $email_data['purpose'];
?>
        <div class='alert alert-danger' id="error-message" style="display: none;"></div>

        <input type="hidden" name="email_id" id="email_id" value="<?php echo $email_data['id']; ?>">
        <legend>Email Details</legend>
        <fieldset>
            <?php if (!empty($guider)) { ?>
                <div class="well" style="text-align:center;">
                    <strong><?php echo $guider; ?></strong>
                </div>
            <?php } ?>
            <div class="form-group">
                <label class="control-label col-lg-2" for="title">Email Title</label>
                <div class="col-lg-9">
                    <input type="text" id="title" placeholder="" class="form-control" value="<?php echo $email_data['title']; ?>" name="title" disabled="disabled">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="content">Email Content<br>
                </label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="content" id="content" disabled="disabled" rows="12"><?php echo $email_data['content']; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="content"></label>
                <div class="col-lg-9">
                    <div class="well" style="line-height: 25px;">
                        <strong>
                            [User_Name] :User's login username.<br />
                            [Password] :User's login password.<br />
                            [First_Name] :User's first name.<br />
                            [Last_Name] :User's last name. <br />
                            [Email_Address] :User's email address.<br />
                            <?php
                            if (isset($current_purpose) && $current_purpose == 'registration') {
                                echo "[Register_Date] :User's registration date. Eg:2013-1-1";
                                echo "<br/>";
                            }
                            ?>
                            [Current_Status]:The user's current status. <br />
                            If the user was actived by admin,his status is active.Else the status is pending.<br />

                            <?php if (isset($current_purpose) && $current_purpose == 'active_account') {
                                echo '[Account_username]: Account username which can login the ISDSL system.';
                                echo '[Account_password]: The password which can login the ISDSL system.';
                            } ?>
                        </strong>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="email">Admin Email Address</label>
                <div class="col-lg-9">
                    <input type="text" id="email" placeholder="" disabled="disabled" class="form-control" value="<?php echo $email_data['email_address']; ?>" name="email">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="email">Attachments</label>
                <div class="col-lg-9">
                    <?php if (!empty($attach_data)) {
                        $tmpl = array('table_open'  => '<table class="table">');
                        $this->table->set_template($tmpl);
                        foreach ($attach_data as $i => $att) {
                            $id = $att['id'];
                            $name = $att['name'];
                            $this->table->add_row(array($name));
                        }
                        echo $this->table->generate();
                    } ?>
                </div>
            </div>

            <!-- set number of users you want send email to at a time -->
            <div class="form-group">
                <label class="control-label col-lg-2" for="email">Select Number Of Users</label>
                <div class="col-lg-9">
                    <select name="n_users" id="n_users" class="form-control">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="1">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>

                    </select>
                </div>
            </div>

            <!-- set time duration for chuck emails -->
            <div class="form-group">
                <label class="control-label col-lg-2" for="email">Select Time You Want trigger Email for users</label>
                <div class="col-lg-9">
                    <select name="email_time" id="email_time" class="form-control">
                        <option value="5">5 Min</option>
                        <option value="10">10 Min</option>
                        <option value="15">15 Min</option>
                        <option value="30">30 Min</option>
                        <option value="45">45 Min</option>
                        <option value="60">60 Min</option>

                    </select>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <div style="text-align: center;">
                        <input id="subm_resl" type="submit" class="btn btn-large btn-primary" value="Send" name="">
                    </div>
                </div>
            </div>
        </fieldset>
    <?php
    }
    echo form_close();
    ?>
    <br />
    <br />
<?php
}
?>

<div id="couter_mod" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <p><span id="sent">0</span> sent from <span id="all"><?php echo $users ?></span></p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">

                    </div>
                </div>
                <div id="sent_result" hidden>
                    <p>Success: <span id="s_sent">0</span></p>
                    <p>Not sent: <span id="e_sent">0</span></p>
                </div>
                <p id="ok_btn" hidden><button class="btn btn-large text-center" value="Ok" onclick="hideModal()">Ok</button></p>
            </div>

        </div>
    </div>
</div>