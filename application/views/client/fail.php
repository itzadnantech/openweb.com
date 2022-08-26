<div id="page-content" class="container">
	<div class="lead">
		<?php if(isset($error) && !empty($error)){
			echo "<div class='alert alert-danger'>There have some errors about the payment, please try it again.<br>";
			echo "Error:$error</div>";
		}?>
	</div>
</div>
