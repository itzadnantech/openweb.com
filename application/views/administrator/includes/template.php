
<?php
$this->load->view("administrator/includes/header");
if (empty($user_data)) {
	$data['user_data'] = array();
} else {
	$data['user_data'] = $user_data;
}
if (empty($product_data)) {
	$data['product_data'] = array();
} else {
	$data['product_data'] = $product_data;
}
if (empty($messages)) {
	$data['messages'] = array();
} else {
	$data['messages'] = $messages;
}
$this->load->view($main_content, $data);
$this->load->view("admin/includes/footer");

?>
