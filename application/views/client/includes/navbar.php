<?php
$menu_items = array (
	'dashboard' => 'Home',
	'orders' => array(
		'name' => 'My Services',
		'children' => array(
			'active_orders' => 'Active Services',
			'inactive_orders' => 'Inactive Services',
			'orders' => 'All Services'
		),
		'link' => 'orders',
	), 
	'invoices' => 'Invoices',
	'activity_log' => 'Activity Log',
);

if (isset($this->site_data['cart'])) {
	$cart = $this->site_data['cart'];
} else {
	$cart = '';
}

/* if (trim($cart) != '') {
	$cart = explode(',', $cart);
} */

if (!empty($this->site_data['subcategories_assoc'])) {
	$sub_assoc = $this->site_data['subcategories_assoc'];
} else {
	$sub_assoc = array();	
}

$product_items = array ();
$subcat_items = array();
$id_items = array();


if (!empty($this->site_data['category_list'])) {
	$category_list = $this->site_data['category_list'];	
	
	/* foreach ($category_list as $c) {
		if (!empty($sub_assoc[$c['slug']])) {
			$subcat_items[$c['slug']] = array();
			foreach ($sub_assoc[$c['slug']] as $sc) {
				$item = array($sc['slug'] => $sc['name']);
				$subcat_items[$c['slug']] += $item;
			}

		} else {
			$subcat_items[$c['slug']] = array();
		}
		$product_items[$c['slug']] = array (
			'name' => $c['name'],
			'children' => $subcat_items[$c['slug']],
			'link' => 'product/'.$c['slug'],
		);
	}
	 */
	foreach ($category_list as $c) {
		$subcat_items[$c['name']] = array();
		
		foreach ($sub_assoc as $s){	
			if($c['id'] == $s['parent']){
				$item = array($s['id'] => $s['name']);
				$subcat_items[$c['name']] += $item;
			}					
		}	
			
		$product_items[] = array (
				'name' => $c['name'],
				'children' => $subcat_items[$c['name']],
				'link' => 'product/show_offerings',//.$c['name']
		);
	}
}

if (!empty($cart)) {
	$cart_count = count($cart);
	$menu_items = $menu_items + array (
		'../product/checkout/'.$cart => "<span class='badge pull-right'>$cart_count</span> Checkout",
		'../product/clear_cart' => 'Clear Cart',
	);
}

$cur_page = $this->uri->rsegment(2);
?>
<div class="navbar">
    <ul class="nav nav-pills navbar-nav pull-right">
	<?php
		foreach ($menu_items as $f=>$n) {
			if (is_array($n)) {
				$na = $n['name'];
				$cn = $n['children'];
				$l = $n['link'];
				//if ($f == $cur_page) {
				//	echo "<li class='active'>";
				//} else {
					echo '<li>';
				//} 
				echo "<a class='dropdown-toggle' data-toggle='dropdown' href='#'>";
				echo $na . '<b class="caret"></b></a>';
				echo '<ul class="dropdown-menu">';
				foreach ($cn as $fn=>$nn) {
					//if ($fn == $cur_page) {
					//	echo '<li class="active">';
					//} else {
						echo '<li>';
					//}
					echo anchor("user/$fn", $nn);
					echo '</li>';
				}
				echo '</ul></li>';
			} else {
				$link = explode('/', $f);
				$link = array_pop($link);
				//if ($link == $cur_page) {
				//	echo '<li class="active">';
				//} else {
					echo '<li>';
				//} 
				echo anchor("user/$f", $n);
				echo '</li>';
			}
		}
	?>
	</ul>
	<div class="container">
		<ul class="nav navbar-nav">
			<font class="navbar-brand">Order Products</font>
			<?php
				foreach ($product_items as $f=>$n) {
					if (is_array($n)) {
						$na = $n['name'];
						$cn = $n['children'];
						$l = $n['link'];
						if ($f == $cur_page) {
							echo "<li class='dropdown active'>";
						} else {
							echo '<li>';
						}
						echo "<a class='dropdown-toggle' data-toggle='dropdown' href='#'>";
						echo $na . '<b class="caret"></b></a>';
						echo '<ul class="dropdown-menu">';
						foreach ($cn as $fn=>$nn) {
							if ($fn == $cur_page) {
								echo '<li class="active">';
							} else {
								echo '<li>';
							}
							echo anchor("$l/$fn", $nn);
							echo '</li>';
						}
						echo '</ul></li>';
					} else {
						if ($f == $cur_page) {
							echo '<li class="active">';
						} else {
							echo '<li>';
						}
						echo anchor("user/$f", $n);
						echo '</li>';
					}
				}
			?>
		</ul>
	</div>
</div>
