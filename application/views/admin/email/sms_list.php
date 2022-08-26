<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#eidt_email_form').validate({
            rules: {
                title :{
                    required : true,
                },
                content : {
                    required : true,
                },
                email :{
                    required : true,
                    email : true,
                },
            }
        });

        var text_max = $('#content').val().length;;
        $('#count_message').html(text_max + ' characters');

        $('#content').keyup(function() {
            var text_length = $('#content').val().length;

            $('#count_message').html(text_length + ' characters');

            if(text_length > 160)
                $('#count_message').css('color', 'yellow');
            if(text_length > 306)
                $('#count_message').css('color', 'red');
        });
    });

    function check(){
        var a = $('#select-e').val();
        if(a){
            $("#email_template_form").submit();
        }else{
            return false;
        }
    }

</script>
<h3>All SMS Templates</h3>
<div class="form-group" style="padding-bottom: 50px;padding-top: 15px;">
<?php
if(!empty($sms_list)){
    echo form_open('admin/select_sms', array('class' => 'form-inline','id'=>'email_template_form')); ?>
    <div class="form-group">
        <label class="col-lg-3">Select SMS Template</label>
        <div class="col-lg-3">
            <select class="form-control" id="select-e" name="purpose" onchange="check()">
                <option></option>
                <?php
                foreach ($sms_list as $e){
                    $purposes = $e['name'];
                    $name = ucfirst($purposes);

                    if (isset($current_purpose) && $purposes == $current_purpose) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option $selected value='$purposes'>$name</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <?php echo form_close();
}
?>
    </div>

<?php
if(isset($sms_detail) && !empty($sms_detail)) {

    echo form_open('admin/edit_sms', array('class' => 'form-horizontal', 'id' => 'eidt_email_form'));

    if (!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }

    if (!empty($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    }
    $guider = '';

        $purpose = $sms_detail['name'];
        ?>
        <input type="hidden" name="email_id" id="email_id" value="<?php echo $sms_detail['id']; ?>">
        <legend>SMS Details</legend>
        <fieldset>
            <?php if (!empty($sms_data['name'])) { ?>
                <div class="well" style="text-align:center;">
                    <strong><?php echo $sms_detail['name']; ?></strong>
                </div>
            <?php } ?>
            <div class="form-group">
                <label class="control-label col-lg-2" for="purpose">Name Purpose</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" value="<?php echo ucfirst($sms_detail['name']); ?>"
                           disabled="disabled">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="content">SMS Text<br>
                </label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="content" id="content"
                              rows="12" maxlength="459"><?php echo $sms_detail['body']; ?></textarea>
                    <h6 class="pull-right" id="count_message"></h6>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="content">Available Placeholders</label>
                <div class="col-lg-9">
                    <div class="well" style="line-height: 25px;">
                        <strong>
                            <?php echo $sms_detail['shortcodes']
                            ?>
                        </strong>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div style="text-align: center;">
                        <input type="submit" class="btn btn-large btn-primary" value="Update SMS Information" name="">
                    </div>
                </div>
            </div>
        </fieldset>
        <?php

    echo form_close();
} ?>