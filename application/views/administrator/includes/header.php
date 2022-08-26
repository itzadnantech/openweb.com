<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    

    <title>OpenWeb Super Admin Panel</title>
	<script src="<?php echo base_url('js/jquery.min.js'); ?>"></script>
	
	<link href="<?php echo base_url('css/jquery-ui.css'); ?>" rel="stylesheet">
	<script src="<?php echo base_url('js/jquery-ui.js'); ?>"></script>	
	<script src="<?php echo base_url('js/jquery.validate.js'); ?>"></script>	
	
	<link href="<?php echo base_url('css/bootstrap3/bootstrap.min.css'); ?>" rel="stylesheet">		
	<script src="<?php echo base_url('js/bootstrap3/bootstrap.min.js'); ?>"></script>
		
	<link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>" type="text/css" media="screen" charset="utf-8">
	<link rel="icon" href="<?php echo base_url()?>img/favicon.gif" type="image/gif">
	
	<style type="text/css">
		.error{
			color: #f62b2b;
		}
	</style>
</head>
<body>
<div class="jumbotron" style="margin-bottom: ">
	<div class="container">
		<h2 class="logo">
			<img src="<?php echo base_url() ?>img/main.png">
			<!-- <?php
				if (isset($this->site_data['first_name'])){
					$first_name = $this->site_data['first_name'];
				}else{
					$first_name = '';
				}				
			?>	
			<div class="pull-right lead" style="color: #428bca;font-size: 18px;margin-top:10px;margin-right:40px;">
				<?php echo "Welcome, $first_name"; ?>.<br/>

				<?php 				
					if (isset($this->site_data['last_login_time'])){
						$last_login_time = $this->site_data['last_login_time'];
					}else{
						$last_login_time = '';
					}				
				?>
					Current Time :<?php echo date('Y-m-d',time());?><br/>
					Last Login Time :<?php echo $last_login_time;?>
			</div>	 -->	
		</h2>		
	</div>	
</div>	
	<?php
	$menu_items = array (
		'dashboard' => 'Dashboard',
		'manage_orders' => array (
			'name' => 'Manage Admin',
			'children' => array(
				'create_admin' => 'Create Admin',
				'admin_list' => 'Admin List',
			),
		),
	);
	$cur_page = $this->uri->rsegment(2);
	?>
<div class="navbar">
	<div class="container">
	    <ul class="nav navbar-nav">
		<?php
		foreach ($menu_items as $f=>$n) {
			if (is_array($n)) {
				$na = $n['name'];
				$cn = $n['children'];
				echo "<li class='dropdown'>";
				echo "<a class='dropdown-toggle' data-toggle='dropdown' href='#'>";
				echo $na . '<b class="caret"></b></a>';
				echo '<ul class="dropdown-menu">';
				foreach ($cn as $fn=>$nn) {
					if ($fn == $cur_page) {
						echo '<li class="active">';
					} else {
						echo '<li>';
					}
					echo anchor("super_administrator/$fn", $nn);
					echo '</li>';
				}
	  			echo '</ul></li>';
			} else {
			if ($f == $cur_page) {
				echo '<li class="active">';
			} else {
				echo '<li>';
			}
			
			echo anchor("super_administrator/$f", $n);
			echo '</li>';
			
			}		
		}
		echo "<li><a href='".base_url()."user/logout'>Log Out</a></li>";
		?>
		</ul>
	</div>
</div>

<div id="page-content" class="container">
    <div class="row">
   	 <div class="col-lg-2"></div>
      <!--Body content-->
      <div class="container">