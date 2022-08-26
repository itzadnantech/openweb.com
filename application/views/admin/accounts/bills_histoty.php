<script language="javascript" type="text/javascript">
$(function(){
	$( "#start_date" ).datepicker({
	      defaultDate: "+1w",
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
<h3>Bills History</h3>

<?php
if (isset($_GET['u'])) {
	$user = $_GET['u'];
} else {
	$user = '';
}
if (!empty($all_users)) { ?>
<fieldset>
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
</fieldset>
<?php }?>

<?php
if (!empty($bills)) {
	echo "<div class='pull-right'>$showing</div>";
	
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array( 'User', 'Date','Price','Price This Month'));
	
	foreach ($bills as $i=>$act) {
		$this->table->add_row( array( $act['user'], $act['date'],'R'.$act['price'],'R'.$act['pro_rata_extra'] ));
	}
	echo $this->table->generate();
	
	echo $pages;
}
?>
