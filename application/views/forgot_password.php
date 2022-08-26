<body class="error-body no-top" style="background-color: #fff">
<div class="container">
    <div class="row login-container">
        <div class="col-md-4 col-md-offset-4">
            <a href="<?php echo base_url()?>"><img class="img-responsive" src="<?php echo base_url('/img/main.png') ?>"></a>
        </div>
    </div>
    <div class="row">
<div id="login_form" class="col-md-12" style="text-align:center; margin-top: 10px">
<?php 
	echo form_open('login/get_password', array('id' => 'form_forgot_password'));
?>
<?php if (!empty($error_message)) {
	echo "<div class='alert alert-danger'>$error_message</div>";
} ?>
<?php if (!empty($success_message)) {
	echo "<div class='alert alert-success'>$success_message</div>";
} ?>
<div class="">
<!-- <div class="form-group">
	<?php echo form_label('Username', 'username', array ('class'=> 'lead control-label col-lg-4')); ?>
	<div class="col-lg-4">
	<?php echo form_input(
		array(
			'class' => 'form-control input-lg',
			'name' => 'username',
			'id' => 'username',
		)
	); ?>
	</div>
</div> -->
<div class="form-group col-md-12">
	<?php echo form_label('Email Address', 'email', array ('class'=> 'lead control-label col-md-2 col-md-offset-3')); ?>
	<div class="col-lg-4">
	<?php echo form_input(
		array(
			'class' => 'form-control input-lg',
			'name' => 'email',
			'id' => 'email',
		)
	); ?>
	</div>
</div>
<div class="form-group">
	<div class="col-lg-4"></div>
	<div class="col-lg-3">
	<?php echo form_submit(
		array (
			'class' => 'btn pull-right btn-lg btn-primary',
			'value' => 'Send Email',
		)
	); ?>
	</div>
</div>
</div>
<?php 	
	echo form_close();
?>
</div>
</div>
</div>