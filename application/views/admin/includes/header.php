<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">


	<title>OpenWeb Admin Panel</title>
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<script src="<?php echo base_url('js/jquery.min.js'); ?>"></script>
	<script src="<?php echo base_url('js/jquery.min.js'); ?>"></script>

	<link href="<?php echo base_url('css/jquery-ui.css'); ?>" rel="stylesheet">
	<script src="<?php echo base_url('js/jquery-ui.js'); ?>"></script>
	<script src="<?php echo base_url('js/jquery.validate.js'); ?>"></script>

	<link href="<?php echo base_url('css/bootstrap3/bootstrap.min.css'); ?>" rel="stylesheet">
	<script src="<?php echo base_url('js/bootstrap3/bootstrap.min.js'); ?>"></script>
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>" type="text/css" media="screen" charset="utf-8">
	<link rel="icon" href="<?php echo base_url() ?>img/favicon.gif" type="image/gif">
	<link rel"stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>


	<style type="text/css">
		.error {
			color: #f62b2b;
		}
	</style>
</head>

<body>
	<div class="jumbotron" style="margin-bottom: ">
		<div class="container">
			<h2 class="logo">
				<img src="<?php echo base_url() ?>img/main.png">
				<?php
				if (isset($this->site_data['first_name'])) {
					$first_name = $this->site_data['first_name'];
				} else {
					$first_name = '';
				}
				?>
				<div class="pull-right lead" style="color: #428bca;font-size: 18px;margin-top:10px;margin-right:40px;">
					<?php echo "Welcome, $first_name"; ?>.<br />

					<?php
					if (isset($this->site_data['last_login_time'])) {
						$last_login_time = $this->site_data['last_login_time'];
					} else {
						$last_login_time = '';
					}
					?>
					Current Time :<?php echo date('Y-m-d', time()); ?><br />
					Last Login Time :<?php echo $last_login_time; ?><br />
					OW Number : <?php echo (isset($this->site_data['ow'])) ? $this->site_data['ow'] : ''; ?>
				</div>
			</h2>
		</div>
	</div>
	<?php
	$menu_items = array(
		'dashboard' => 'Dashboard',
		'manage_orders' => array(
			'name' => 'Orders',
			'children' => array(
				'pending_orders' => 'Pending Orders',
				'user_orders' => 'User Orders and Statuses',
				'assign_order' => 'Assign an Order',
				'all_undef_orders' => 'All undefined Orders',
				'all_orders' => 'All Orders',
				'lte_orders_type' => 'Add type to LTE order',
				'manual_ordering_settings' => 'Manual Ordering Settings'
			),
		),
		'stats' => 'Statistics',
		'send_notification' => 'Send Notification',

		/* 'view_classes' => array(
			'name' => 'Classes',
			'children' => array(
				'view_classes' => 'View Classes',
				//'update_classes' => 'Update Classes',
			),
		), */
		'monthly_reports' => array(
			'name' => 'Monthly Reports',
			'children' => array(
				'new_orders' => 'Orders this Month',
				'all_reports' => 'Billing per User',
				'changed_bills' => 'Changed Bills',
				'bills_history' => 'Billing History',
			),
		),
		'activity_log' => 'Activity Log',
		'../user/dashboard' => 'View Users Panel',
	);
	$cur_page = $this->uri->rsegment(2);
	?>
	<div class="navbar">
		<div class="container">
			<ul class="nav navbar-nav">
				<?php
				foreach ($menu_items as $f => $n) {
					if (is_array($n)) {
						$na = $n['name'];
						$cn = $n['children'];
						echo "<li class='dropdown'>";
						echo "<a class='dropdown-toggle' data-toggle='dropdown' href='#'>";
						echo $na . '<b class="caret"></b></a>';
						echo '<ul class="dropdown-menu">';
						foreach ($cn as $fn => $nn) {
							if ($fn == $cur_page) {
								echo '<li class="active">';
							} else {
								echo '<li>';
							}
							echo anchor("admin/$fn", $nn);
							echo '</li>';
						}
						echo '</ul></li>';
					} else {
						if ($f == $cur_page) {
							echo '<li class="active">';
						} else {
							echo '<li>';
						}

						echo anchor("admin/$f", $n);
						echo '</li>';
					}
				}
				?>
			</ul>
		</div>
	</div>

	<div id="page-content" class="container">

		<!--Sidebar content-->
		<?php if (!empty($sidebar)) {
		?>
			<div class="row">
				<div class="col-lg-3">
					<?php
					$this->load->view('admin/includes/sidebar');
					?>
				</div>
				<div class="col-lg-9">
				<?php } ?>

				<!--Body content-->
				<div class="container">