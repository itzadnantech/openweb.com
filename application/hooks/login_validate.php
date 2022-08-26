<?php 
class Login {
	function is_logged_in() {
		$is_logged_in = $this->session->userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != true) {
			/* echo "You don't have permission to access this page3. ";
			echo '<a href="../login">Login</a>';
			die(); */
			redirect('login');
		}
	}
}

?>
