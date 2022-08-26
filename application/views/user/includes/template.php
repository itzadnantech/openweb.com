<?php	
	$this->load->view("user/includes/header");

	if(isset($sidebar) && $sidebar)
		$this->load->view("user/includes/sidebar");

	if(isset($navbar) && $navbar)
		$this->load->view("user/includes/navbar");

	$this->load->view($main_content);
	$this->load->view("user/includes/footer");

?>
