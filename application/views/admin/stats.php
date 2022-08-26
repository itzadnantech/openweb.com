<h3>Usage Statistics</h3>


<div class="row-fluid">
	<div class="span6">
		<legend>Quantitative Data</legend>
		<table class="table">
			<thead>
				<th>Item</th>
				<th>Quantity</th>
			</thead>
			<tbody>
				<tr>
					<td>Users</td>
					<td><?php echo $num_users; ?></td>
				</tr>
				<tr>
					<td>Orders</td>
					<td><?php // echo $num_products_ordered; ?></td>
				</tr>
				<tr>
					<td>Pending Orders</td>
					<td><?php // echo $num_products_pending; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="span6">
		<legend>User Data</legend>
		<table class="table">
			<thead>
				<th>Criterion</th>
				<th>User</th>
			</thead>
			<tbody>
				<tr>
					<td>Last joined</td>
					<td><?php echo $last_joined; ?></td>
				</tr>
				<tr>
					<td>Last logged in</td>
					<td><?php echo $last_logged; ?></td>
				</tr>
				<tr>
					<td>Most orders</td>
					<td><?php // echo $num_products_ordered; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>