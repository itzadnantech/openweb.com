<script language="javascript" type="text/javascript">
$(function(){
	$( "#start_date" ).datepicker({
	     // defaultDate: "+1w",
	      changeMonth: true,
	      changeYear: true,
	      dateFormat: 'yy-mm-dd',
	      onClose: function( selectedDate ) {
	        $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });
	    $( "#end_date" ).datepicker({
	      defaultDate: "+1w",
	      changeMonth: true,
	      changeYear: true,
	      dateFormat: 'yy-mm-dd',
	      onClose: function( selectedDate ) {
	        $( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
});
</script>
<h3>Activity Log</h3>

<?php
if (isset($_GET['u'])) {
	$user = $_GET['u'];
} else {
	$user = '';
}
if (!empty($all_users)) { ?>
<form class="form-horizontal" method="get">
	<div class="form-group">
		<label class="control-label col-lg-2" for="select-u">Select User</label>
		<div class="col-lg-4">
			<select  name="u" id="select-u" class="form-control">
				<option value=''>All Users</option>
				<?php
				foreach($all_users as $u) {
					$n = "{$u['first_name']} {$u['last_name']} ({$u['username']})";
					$i = $u['username'];
					if ($i == $user) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option $selected value='$i'>$n</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-2" for="select-u">Select Type</label>
		<div class="col-lg-4">
			<select  name="type" id="select-type" class="form-control">
				<option value=''></option>
				<option value='0'>Order Product</option>
				<option value='1'>User Change Credit Card Information</option>
			</select>
		</div>
	</div>
	<div class="form-group">			
		<label for="from" class="control-label col-lg-2">Start Date</label>		
		<div class="col-lg-4">
			<input type="text" id="start_date" name="start_date"  class="form-control" value="<?php if (isset($_GET['start_date'])){echo $_GET['start_date'];}?>"/>
		</div>
	</div>
		
	<div class="form-group">
		<label for="to" class="control-label col-lg-2">End Date</label>
		<div class="col-lg-4">
			<input type="text" id="end_date" name="end_date" class="form-control" value="<?php if (isset($_GET['end_date'])){echo $_GET['end_date'];}?>"/>
		</div>
	</div>
	
	<div class="form-group" style="padding-left: 360px;">
		<input type="submit" class="btn btn-primary" value="Submit">
	</div>
</form>
<?php } ?>
<?php
if (!empty($activity)) {
	echo "<div class='pull-right'>$showing</div>";
	
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array('User', 'Activity Details','Activity Date'));
	
	foreach ($activity as $i=>$act) {
		$a = '<ul><li>' . str_replace("\n", '</li><li>', $act['activity']) . '</ul>';
		$u = $act['user'];
		$date = $act['date'];
		$a = array('data' => $a, 'style' => 'width:600px;');
		$this->table->add_row( array($u, $a, $date));//#-->$num_activities - ($start + $i)),
	}
	echo $this->table->generate();
	
	echo $pages;
}else{
	echo '<div class="alert alert-warning">
			<strong>Activity Log not found.</strong>
		  </div>';
}
?>