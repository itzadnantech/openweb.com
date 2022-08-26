<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="&lt;/head">

    <title>OpenWeb Home's Panel</title>
	<script src="<?php echo base_url('js/jquery.min.js'); ?>"></script>
	
	<link href="<?php echo base_url('css/jquery-ui.css'); ?>" rel="stylesheet">
	<script src="<?php echo base_url('js/jquery-ui.js'); ?>"></script>	
	<script src="<?php echo base_url('js/jquery.validate.js'); ?>"></script>
	<script src="<?php echo base_url('js/Highcharts/highcharts.js'); ?>"></script>
	<script src="<?php echo base_url('js/Highcharts/modules/exporting.js'); ?>"></script>
	
	<link href="<?php echo base_url('css/bootstrap3/bootstrap.min.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('css/bootstrap3/bootstrap-glyphicons.css'); ?>" rel="stylesheet">
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
<div class="jumbotron">
	<div class="container">
		<h2 class="logo">
			<img src="<?php echo base_url() ?>img/main.png" style="width: 253px; height: 80px;">				
		</h2>
	</div>
</div>
<div class="navbar lead" style="min-height: 20px;">
	<div class="container">
		<ul class="nav nav-pills navbar-nav">
    		<li class="active"><a href= "<?php echo base_url().'login';?>">Sign Into Start</a></li>
    	</ul>
	</div>
</div>      