<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3>Activity Log</h3>

<?php

if (!empty($activity)) {
	echo "<div class='pull-right'>$showing</div>";
	
	$tmpl = array ( 'table_open'  => '<table class="table">' );
	$this->table->set_template($tmpl);
	$this->table->set_heading(array( '#', 'Activity Details'));
	
	foreach ($activity as $i=>$act) {

        $activity_str = $act['activity'];
        $string_length = strlen($activity_str);

        $link_pos = strpos($activity_str, "\nView the detail through the link below");
        if ($link_pos > 15)
            $activity_str = substr($activity_str,0 , $link_pos);


		$a = '<ul><li>' . str_replace("\n", '</li><li>',  $activity_str) . '</ul>';

		$this->table->add_row( array( ($num_activities - ($start + $i)), $a ));
	}
	echo $this->table->generate();
	echo "<div class='pull-right'>$pages</div>";
}else{
	echo '<div class="alert alert-warning">
			<strong>Activity Log not found.</strong>
		  </div>';
}

?>
        </div>
    </div>
</div>
