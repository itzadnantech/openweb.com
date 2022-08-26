<script type="text/javascript" language="javascript">
function check(){ 
	var r = $('#select-r').val();
	var s = $('#select-s').val();
	if(r || s){
		$("#form_filter_user").submit();
	}else{
		return false;
	}
}

$(document).ready(function() {
	$("#serch_form").validate({
		rules: {
			//user_name : "required",
			user_id : {
          	  number : true,
            },
		}
	});


    function setCurrentPaginationButton(){

        var currentPage = $("#current_page").val();
        var rowsPerPage =  $("#rows_per_page").val();

        var pagiButtonVal  = Math.trunc((currentPage/rowsPerPage)) + 1;
        //alert(pagiButtonVal);
        // ul.pagination  a
        //   $("ul.pagiantion a[href='" + pagiButtonVal + "']").addClass('btn btn-primary');
        var ulPaginationElement = $("ul.pagiantion");


        return false;

        currentAElement.addClass('btn btn-primary');

      //  alert(currentPage);
      //  alert(rowsPerPage);

    }

    setCurrentPaginationButton();
});
</script>
<h3>All Accounts</h3>
<?php  
if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
	echo "<div class='alert alert-success'>".$messages['success_message']."</div>";
}
if (isset($messages['error_message']) && trim($messages['error_message']) != '') {
	echo "<div class='alert alert-danger'>".$messages['error_message']."</div>";
}

    $search_params = array(

        'user_name'     => '',
        'first_name'    => '',
        'last_name'     => '',
        'email_address' => '',
        'ow_num'        => '',
        'user_id'       => '',
        'sa_id_number'  => '',

    );

    foreach ($search_array as $key => $value ){

        if (!empty($value))
            $search_params[$key] = $value;
    }


// 0 - 1, 2 - 10, 3 - 20
?>
<input type="hidden" value="<?php echo $current_start_param;?>" id="current_page" >
<input type="hidden" value="<?php echo $num_per_page; ?>" id="rows_per_page">
<fieldset> 
<legend>Search For User</legend>
<?php echo form_open('admin/search_for_user', array('method'=>'get', 'class' => 'form-horizontal','id' => 'serch_form'));?>

    <div class="form-group">
        <label class="control-label col-lg-2">SA ID Number:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="sa_id_number" id="sa_id_number_element" value="<?php echo $search_params['sa_id_number']; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">User ID Number:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="user_id" id="user_id" value="<?php echo $search_params['user_id']; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">OW Number:</label>
        <div class="col-lg-4">
            <input type="text" class="form-control" name="ow_num" placeholder="e.g. : 42196" id="ow_num" value="<?php echo $search_params['ow_num']; ?>"/>
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

<div class="form-group">
	<label class="control-label col-lg-2"></label>
	<div class="col-lg-1">
		<input type="submit" class="btn btn-sm btn-primary" value="Search"/>
	</div>
</div>
<?php echo form_close();?>
</fieldset>

<fieldset>
<legend>User List</legend>
<?php echo form_open('admin/filter_user', array('class' => 'form-inline','id' => 'form_filter_user')); ?>
<div class="form-group"> 
	<label class="control-label col-lg-1">Role:</label>
	<div class="col-lg-3">
		<select class="form-control valid" name="role" id="select-r" onchange="check()">
			<option value="client">Client</option>
			<?php 
				/* if (isset($cycle)) { 
					foreach($cycle as $u => $r) {
						$i = $r['role'];
						$s = ucfirst($r['role']);
						if (isset($role) && $i == $role) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option $selected value='$i'>$s</option>";
					}			
				} */
			?>			
		</select>
	</div>
	
	<label class="control-label col-lg-1">Status:</label>
	<div class="col-lg-3 ">
		<select class="form-control valid" name="status" id="select-s" onchange="check()">
			<option value="all">ALL</option>
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
	$this->table->set_heading(array('Fist Name', 'Last Name', 'Email','Mobile Number' ,'Username', 'Role', 'Date', 'Status','Actions'));
	
	foreach ($accounts as $account_id => $account_data) {
		$account_id = $account_data['id'];
		$first_name = $account_data['first_name'];
		$last_name = $account_data['last_name'];
		$email = $account_data['email_address'];
		$mobile = $account_data['mobile_number'];
		$username = $account_data['username'];
		$role =$account_data['role'];
		$date = date('d/m/Y', strtotime($account_data['joined']));

		$status = $account_data['status'];
		
		$manage_page = anchor("admin/edit_account/$account_id", 'Manage', 'class="btn btn-sm btn-primary"');
		
		$login_username = $this->session->userdata('username');
		
		if ($username == $login_username) {
			$delete_page = "<input text='button' value='Delete' class='btn btn-sm btn-danger' style='width:58px;height:30px;background-color:gray;border-color:gray;'  disabled='disabled'/>";			
		} else {		
			$delete_page = anchor("admin/delete_user/$account_id", 'Delete', 'class="btn btn-sm btn-danger"');
		}
		//$actions = "$manage_page $delete_page";
		//$actions = array('data' => $actions, 'style' => 'width:500px;');
		$this->table->add_row( array( $first_name, $last_name, $email, $mobile,$username, $role, $date, $status, $manage_page, $delete_page));
	}
	
	echo $this->table->generate();	
	echo "<div class='pull-right'>$pages</div>";
}else{
?>	
<div class="alert alert-warning">
	<strong>User not found.</strong> <?php echo $priority_flag['message']; ?>
</div>
<?php 
}
?>
</fieldset>