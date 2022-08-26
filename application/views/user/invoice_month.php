<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="&lt;/head">
    <title>OpenWeb Client's Panel</title>
	<link href="<?php echo base_url('css/bootstrap3/bootstrap.min.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('css/bootstrap3/bootstrap-glyphicons.css'); ?>" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>" type="text/css" media="screen" charset="utf-8">
	<link rel="icon" href="<?php echo base_url()?>img/favicon.gif" type="image/gif">
</head>
<body>
<div class="container">
	<div class="col-lg-12">
		<h3 style="margin-top:20px">All Tax Invoices for <?php echo $user_name;?></h3>
		<?php 
			if (!empty($success_message)) {
				echo "<div class='alert alert-success'>$success_message</div>";
			}
		?>
			<div class="clearfix"></div>
			<?php if (isset($invoices) && !empty($invoices)) { ?>
			<table class="table" style="margin-top:10px;">
				<thead>
					<th>Invoice ID</th>
					<th>Invoice</th>
					<th>Date Ordered</th>
					<?php 
						$seg = $this->uri->segment(2, 0);
						if($seg == 'invoices'){
							echo "<th>Download</th>";
							echo "<th>Send Email</th>";
						}
					?>
				</thead>
				<tbody>
				<?php
					$total = 0;
					foreach ($invoices as $or) {
				?>
					<tr>
						<td><?php echo $or['id'] ?></td>
						<td><a href="<?php echo base_url().$or['pdf_path'];?>" target = "_black"><?php echo $or['invoice_name'] ?></a></td>
						<td><?php echo date('d/m/Y', strtotime($or['create_date'])) ?></td>
						<?php 
							$seg = $this->uri->segment(2, 0);
							if($seg == 'invoices'){
						?>
						<td><?php echo anchor('user/down_pdf/'.$or['id'], 'Download', 'class="btn btn-default" target = "_black"');?></td>
						<td><?php echo anchor('user/send_pdf/'.$or['id'], 'Send Email', 'class="btn btn-default" target = "_black"');?></td>
						<?php  } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div><?php echo INVOICE_VAT_ROW; ?></div>
		</div>
		<?php }else{
			echo "<div class='alert alert-info'>There has no oders record this month.</div>";
		} ?>
</div></div>
</div>
</body>
</html>
