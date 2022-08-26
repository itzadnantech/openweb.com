<h3>Monthly Reports</h3>
<?php if (!isset($monthly_reports)) { ?>

<div class="lead">Welcome to the monthly reports page. Here you can calculate monthly reports for users, and see which statements have changes</div>
<?php echo anchor('admin/calculate_reports', 'Calculate Monthly  Reports', 'class="btn btn-lg btn-default"'); ?>


<?php } else {

	if (isset($monthly_reports['pro_ratas'])) { ?>
	
			<div class="lead">These orders were made this month:</div>
			<table class="table">
				<thead>
					<th>User</th>
					<th>Pro-Rata Required</th>
					<th>Date Ordered</th>
				</thead>
				<tbody>
				<?php if (!empty($monthly_reports['pro_ratas'])) {
			foreach ($monthly_reports['pro_ratas'] as $pr) { ?>
					<tr>
						<td><?php echo $pr['user'] ?></td>
						<td>R<?php echo $pr['pro_rata_extra'] ?></td>
						<td><?php echo $pr['date'] ?></td>
					</tr>
				<?php }
		} ?>

				</tbody>
			</table>
		<?php } else if (isset($monthly_reports['all_bills'])) { ?>

			<div class="lead">These are the costs for each user this month:</div>
			<table class="table">
				<thead>
					<th>User</th>
					<th>Total Cost this Month</th>
				</thead>
				<tbody>
				<?php if (!empty($monthly_reports['all_bills'])) {
				foreach ($monthly_reports['all_bills'] as $us=>$pr) { ?>
					<tr>
						<td><?php echo $us ?></td>
						<td>R<?php echo $pr ?></td>
					</tr>
				<?php }
			} ?>
				</tbody>
			</table>
		<?php } else if (isset($monthly_reports['changed_bills'])) { 
				if (!empty($monthly_reports['changed_bills'])) { ?>
			<div class="lead">These are the costs for your users that have changed since you last updated the bills. When you are done taking note of these, click "Update Bills."</div>
			<table class="table">
				<thead>
					<th>User</th>
					<th>Total Cost this Month</th>
					<th>Cost When Last Updated</th>
					<th>Last Updated</th>
				</thead>
				<tbody>
				<?php if (!empty($monthly_reports['changed_bills'])) {
				foreach ($monthly_reports['changed_bills'] as $us=>$data) { ?>
					<tr>
						<td><?php echo $us ?></td>
						<td><strong>R<?php echo $data['new_cost'] ?></strong></td>
						<td>R<?php echo $data['last_cost'] ?></td>
						<td><?php echo $data['last_updated'] ?></td>
					</tr>
				<?php } } ?>
				</tbody>
			</table>
			<?php echo anchor('admin/update_changed_bills', 'Update Bills', 'class="btn btn-lg btn-default"'); ?>
		<?php } else { ?>
			<div class="lead">All of your bills are up to date; there have been no changes.</div>
		<?php }
		}
}
?>