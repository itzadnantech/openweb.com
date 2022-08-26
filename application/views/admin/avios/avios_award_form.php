
<div class="text-center">
    <img src="https://home.openweb.co.za/img/avios-logo.png">
</div>
<h3>Avios Award System</h3>

<?php echo validation_errors(); ?>

<?php if(isset($ok_message)) { ?>

    <div class="alert alert-success">
		<?php echo $ok_message ?>
    </div>
<?php } ?>

<?php if(isset($er_message)) { ?>

    <div class="alert alert-danger">
        <?php echo $er_message ?>
    </div>
<?php } ?>

<?php echo form_open('admin/award_user', array('method'=>'post', 'class' => 'form-horizontal','id' => 'award_form'));?>
<fieldset>
    <div class="form-group">
        <label class="control-label col-lg-2">User ID Number:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="user_id" id="user_id" placeholder="Use search if don't know"/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">Points:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="points" id="points" placeholder="100 or etc."/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">Bonus:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="bonus" id="bonus" placeholder="0 or etc."/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">Award type:</label>
        <div class="col-lg-4">
            <select id="billing_code" name="billing_code">
                <?php
                    foreach ($billing_codes as $code => $desc) { ?>
                        <option value="<?php echo $code;?>"><?php echo $desc;?></option>
                    <?php }  ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2"></label>
        <div class="col-lg-1">
            <input type="submit" class="btn btn-sm btn-primary" value="Award"/>
        </div>
    </div>
    <hr>
<?php
$username = $this->session->userdata["username"];
if($username === "keoma") {
    echo '<div class="form-group">
        <label class="control-label col-lg-9">This button physically awards the Avios to the client.
            DO NOT, under ANY circumstances push this button unless you are Keoma.</label>
        <div class="col-lg-1">
            <input type="button" class="btn btn-sm btn-danger" value="Create and send awards" onclick="relocate()"/>
        </div>
    </div>';
}
?>
    <?php echo form_close();?>
</fieldset>

<fieldset>
    <legend>Search For User</legend>
    <?php echo form_open('admin/search_for_user', array('method'=>'get', 'class' => 'form-horizontal','id' => 'serch_form'));?>

    <div class="form-group">
        <label class="control-label col-lg-2">User ID Number:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="user_id" id="user_id" value="<?php echo $search_params['user_id']; ?>"/>
        </div>
    </div>

    <hr/>
    <div class="form-group">
        <label class="control-label col-lg-2">Username:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="user_name" id="user_name" value="<?php echo $search_params['user_name']; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">First Name:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $search_params['first_name']; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Last Name:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $search_params['last_name']; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">Email Address:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="email_address" id="email_address_element" value="<?php echo $search_params['email_address']; ?>"/>
        </div>
    </div>

    <div hidden>
        <input type="text" value="avios" name="avios" id="avios" class="form-control" hidden/>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2"></label>
        <div class="col-lg-1">
            <input type="submit" class="btn btn-sm btn-primary" value="Search"/>
        </div>
    </div>

    <?php echo form_close();?>
</fieldset>
<br/><br/>
<?php
if ($num_per_page > $num_account) {
    $num_per_page = $num_account;
}

if (!empty($accounts)) {
    echo "<div class='pull-right'>$showing</div>";
    $tmpl = array ( 'table_open'  => '<table class="table">' );
    $this->table->set_template($tmpl);
    $this->table->set_heading(array('ID', 'Fist Name', 'Last Name', 'Email','Mobile Number' ,'Username', 'Role', 'Status', 'Actions'));

    foreach ($accounts as $account_id => $account_data) {
        $account_id = $account_data['id'];
        $first_name = $account_data['first_name'];
        $last_name = $account_data['last_name'];
        $email = $account_data['email_address'];
        $mobile = $account_data['mobile_number'];
        $username = $account_data['username'];
        $role =$account_data['role'];

        $status = $account_data['status'];

        $manage_page = anchor("admin/award_user", 'Award', 'class="btn btn-sm btn-primary award" id="'.$account_id."\"");

        $login_username = $this->session->userdata('username');

        $this->table->add_row( array($account_id, $first_name, $last_name, $email, $mobile,$username, $role, $status, $manage_page));
    }

    echo $this->table->generate();
    echo "<div class='pull-right'>$pages</div>";
    echo "<div><p class='stat' hidden>Ok!!All done</p></div>";
}else{
    ?>
    <div class="alert alert-warning">
        <strong>User not found.</strong> <?php echo $priority_flag['message']; ?>
    </div>
    <?php
}
?>
<script language="JavaScript" type="text/javascript">
    $(document).ready(function(){

        //Award button event
        $('.award').click(function (event) {
            event.preventDefault();

            var id = $(this).attr('id');

            $('html,body').animate({
                    scrollTop: $("#award_form").offset().top},
                'slow');
            $('#user_id').val(id);
        });

        //Validate Awardform
        $("#award_form").validate({
            rules: {
                user_id: {
                    required : true,
                    number: true
                },
                points: {
                    required: true,
                    number: true
                },
                bonus: {
                    required: true,
                    number: true
                }
            }

        });

    });

    function relocate() {
        location.href = "/admin/create_avios_file";
    }
</script>
