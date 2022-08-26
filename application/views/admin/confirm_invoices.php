<div class="lead">Email invoices to all users for <?php 
	echo date('F o', strtotime('now'))
?></div>
<?php
echo anchor('admin/send_invoices/true', 'Email Invoices to All Clients', 'class="btn btn-success"')
?>