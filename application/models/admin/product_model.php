<?php

class Product_model extends CI_Model {


	function get_product_nice_list() {
		$prods = $this->get_product_list();
		$prod_list = array();
		if (!empty($prods)) {
			foreach ($prods as $prod) {
				$key = $prod['id'];
				$value = $prod['name'];
				$prod_list[$key] = $value;
			}
		}
		return $prod_list;
	}

    // array ('select' => '', fields => array())
    function get_product_nice_list_by_params($params) {
        $prods = $this->get_product_list_by_params($params);
        $prod_list = array();
        if (!empty($prods)) {
            foreach ($prods as $prod) {
                $key = $prod['id'];
                $value = $prod['name'];
                $prod_list[$key] = $value;
            }
        }
        return $prod_list;
    }


	function get_billing_cycles() {
		$billing_cycles = array (
			'monthly' => 'Monthly',
			'yearly' => 'Yearly',
			'once' => 'Once-Off'
		);

		return $billing_cycles;
	}
	
	function get_cycles(){
		//$query = $this->db->query('select distinct billing_cycle from products ');
		//$result = $query->result_array();
		
		$result = array(
			'0' => 'monthly',
			'1' => 'yearly',
			'2' => 'once-Off',		
		);
		return $result;
		
	}

	function get_is_classes($num = 10, $start = 0) {
		$this->db->limit($num, $start);
		$this->db->select('id, desc, realm,table_id');
		$query = $this->db->get('is_classes');		
		$result = $query->result_array();
		return $result;
		//return $query->result_array();
	}
	
	function get_classes() {
		$this->db->select('id, desc, realm,table_id');
		$query = $this->db->get('is_classes');
		$result = $query->result_array();
		return $result;
		//return $query->result_array();
	}
	
	function get_class_count(){
		$this->db->select('id');
		$this->db->from('is_classes');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function get_classes_data($class_id){
		$this->db->where('table_id', $class_id);
		$query = $this->db->get('is_classes');
		//$product_settings = $query->first_row('array');		
		//$data = array( 'product_settings' => $product_settings,);
		return $query->result_array();
	}
	
	function get_class_fields(){
		$class_fields = array (
				'id' => 'class Name',
				'desc' => 'description',
				'realm' => 'realm',
		);
		return $class_fields;
	}
	
	function get_class_name($class_id){
		$this->db->select('id');
		$this->db->where('table_id',$class_id);
		$query = $this->db->get('is_classes');
		if($query->num_rows >= 1){
			$result = $query->row();
			return $result->id;
		}else{
			return 0;
		}
	}
	
	function validate_class_name($name){
		$this->db->select('table_id');
		$this->db->where('id',$name);
		$query = $this->db->get('is_classes');
		if($query->num_rows == 1){
			$result = $query->row();
			return 1;
		}else{
			return 0;
		}
	}

/* 	function get_product_name($product_id) {
		$this->db->select('name');
		$this->db->where('id', $product_id);
		$query = $this->db->get('products');
		$result = $query->result_array();
		$name = $result[0]['name'];
		return $name;
	} */

	function get_default_comment($product_id) {
		$this->db->select('default_comment');
		$this->db->where('id', $product_id);
		$query = $this->db->get('products');
		$result = $query->result_array();
		$default_comment = $result[0]['default_comment'];
		return $default_comment;
	}

	function get_product_fields() {
		$product_fields = array (
			'name' => 'Product Name',
            'type' => 'Type',
			'parent' => 'Parent Sub-Category',
			'active' => 'Visibility',
			//'trial' => 'Trial',
			//'class' => 'Product Class',
			'package_speed' => 'Package Speed',
			'service_level' => 'Service Level',
			'recommended_use' => 'Recommended Use',
			'global_backbone' => 'Global Backbone',
			//'billing_cycle' => 'Billing Cycle',
			'price' => 'Price',
			'billing_code' => 'Avios Billing Code',
			//'pro_rata_option' => 'Pro-Rata Option',
			'features' => 'Features',
			'billing_occurs_on' => 'Billing Occurs On',
			'discount_codes' => 'Discount Codes',
			//'automatic_creation' => 'Creation Mode',
			//'default_comment' => 'Default Comment',
			'desc' => 'Longer Description',
		);
		return $product_fields;
	}

	function get_product_data($product, $type = '') {
	    $table = 'products';
	    if($type == 'r') {
	        $table ='products_reseller';
        }
		$this->db->where('id', $product);
		$query = $this->db->get($table);
		$product_settings = $query->first_row('array');
		$data = array( 'product_settings' => $product_settings,);
		return $data;
	}

    function get_product_data_for_port_reset($product) {
        $this->db->where('id', $product);
        $query = $this->db->get('products');
        $product_data = $query->first_row('array');
        return $product_data;
    }
	
	function get_payment_methods($product_id, $billing_cycle = null){
		$this->db->select('payment_method');
		$this->db->where('product_id', $product_id);
        if ($billing_cycle != null){

                $this->db->where('billing_cycle', $billing_cycle);
        } else {
            $this->db->where('billing_cycle', 'monthly');
        }

		$query = $this->db->get('product_payment_methods');
		if($query->result_array()){
			return $result = $query->result_array();
		}else{
			return $result = '';
		}
	}


    function get_full_payment_methods($product_id){
        $this->db->select('payment_method, billing_cycle');
        $this->db->where('product_id', $product_id);


        $query = $this->db->get('product_payment_methods');
        $result_array = $query->result_array();
        if(!empty($result_array)){

            $final_array = array();
            foreach ( $result_array as $row ){

                $final_array[$row['payment_method']][$row['billing_cycle']] = '1';

            }

            return $final_array;

           //  return $result = $query->result_array();

        }else{
            return $result = '';
        }
    }
	
	function get_credit_order($product_id, $method, $billing_cycle = null){
		$this->db->select('id');
		$this->db->where('product_id', $product_id);
		$this->db->where('payment_method', $method);
        if ($billing_cycle != null){
            $this->db->where('billing_cycle', $billing_cycle);
        } else {
            $this->db->where('billing_cycle', 'monthly');
        }
		$query = $this->db->get('product_payment_methods');
		if($query->result_array()){
			$result = $query->first_row('array');
			return $result['id'];
		}else{
			return $result = '';
		}
	}

    function get_all_credit_orders($product_id){


     /*
         $return_method_credit = $this->product_model->get_credit_order($product_id, 'credit_card');
         $return_method_credit_auto = $this->product_model->get_credit_order($product_id, 'credit_card_auto');
         $return_method_debit = $this->product_model->get_credit_order($product_id, 'debit_order');
         $return_method_eft = $this->product_model->get_credit_order($product_id, 'eft');
     */

        $this->db->select('');
        $this->db->where('product_id', $product_id);
        $query = $this->db->get('product_payment_methods');
        $result = $query->result_array();

        $final_array = array();
        if (!empty($result)){

            foreach ($result as $row){


                $key_method = $row['payment_method'];
                $billing_cycle = $row['billing_cycle'];

                $final_array[$key_method][$billing_cycle] = $row['id'];
                // -------------------------------------------------
                $key_method = '';
                $billing_cycle = '';

            }


        }

        return $final_array;

    }

    function handle_payment_update($product_id, $post_payment_array, $exist_methods){

/*
        echo "<pre>";
        print_r($product_id);
        echo "</pre><br/>";

        echo "<pre>";
        print_r($post_payment_array);
        echo "</pre><br/>";

        echo "<pre>";
        print_r($exist_methods);
        echo "</pre>";
*/

       // die;

        $payment_methods_array = array(
            'credit_card', 'credit_card_auto', 'debit_order', 'eft'
        );

        $billing_cycles_array = array(
            'daily', 'monthly', 'once-off'
        );


        foreach ($payment_methods_array as $payment_method){

            foreach ($billing_cycles_array as $billing_cycle){

                if( isset( $post_payment_array[$payment_method][$billing_cycle]) ){

                    if( !isset($exist_methods[$payment_method][$billing_cycle]) ){
                        $payment_checks_array = array(
                            'payment_method' => $payment_method,
                            'product_id' => $product_id,
                            'billing_cycle' => $billing_cycle
                        );
                        $this->db->insert('product_payment_methods',  $payment_checks_array);
                    }
                }else{

                    if( isset($exist_methods[$payment_method][$billing_cycle]) ){
                        $this->db->delete('product_payment_methods', array('id' => $exist_methods[$payment_method][$billing_cycle]));
                    }
                }

            }
        }


    }




	function get_pro_rata_options () {
		$rata = array (
			'standard' => '1-10 Full, 11-15 30%, 16-25 50%, 26-end Free',
			'basic' => '11-end 50%',
			'basic2' => '11-end Free',
			'full' => 'Full amount for current month',
			'none' => 'Free for current month',
		);
		return $rata;
	}

	function get_product_list () {
		$this->db->select('id, name');
		$this->db->where('parent !=', 'legacy');
		$this->db->where('status', 'active');
		$query = $this->db->get('products');
		return $query->result_array();
	}



    function get_product_list_by_params($params = array('select' => '', 'fields' => array())){

        $this->db->select($params['select']);
        foreach( $params['fields'] as $field => $value)
            $this->db->where($field, $value);

        $query = $this->db->get('products');
        return $query->result_array();
    }
	
	function delete_product($product_id, $type) {
		//$this->db->delete('products', array('id' => $product_id));
		$table = 'products';
		if($type == 'r') {
		    $table = 'products_reseller';
        }
		$result = $this->db->update($table, array('status' => 'deleted'),array('id' => $product_id));
		if($result){
			return true;
		}else{
			return false;
		}

	}
	
	function users_on_product ($product_id) {
		$this->db->select('user, date, account_username');
		$this->db->where('product', $product_id);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		if (!empty($result)) {
			$this->load->model('membership_model');
			foreach ($result as $i=>$res) {
				$user = $res['user'];
				$name = $this->membership_model->get_user_name_nice($user);
				$result[$i]['name'] = $name;
			}
		}
		return $result;
	}

	function get_product_categories() {
		$this->db->select('id, name, slug, parent');
		// Because it's admin, 
		// in this instance we're going to modify the name a bit.
		$query = $this->db->get('product_subcategories');
		$result = $query->result_array();
		foreach ($result as $i=>$res) {
			$sub_name = $res['name'];
			$parent = $res['parent'];
			
			//get the category name with the category id
			$category_name = $this->category_model->get_category_name($parent);
			
			$this->db->select('name');
			$this->db->where('id', $parent);//change the slug to name 
			$query = $this->db->get('product_categories');			
			$result2 = $query->first_row('array');

			$name = $category_name;//$result2['name'];			
			$result[$i]['sub_name'] = $sub_name;
			$result[$i]['name'] = "$name - $sub_name";
		}
		return $result;
	}

    function get_product_categories_reseller() {

        $this->db->select('product_subcategories_reseller.id, product_subcategories_reseller.name as sub, 
            product_categories_reseller.name as cat, product_subcategories_reseller.slug');
        $this->db->join('product_categories_reseller', 'product_categories_reseller.id = product_subcategories_reseller.parent');
        $this->db->where('product_subcategories_reseller.visible', '1');
        $query = $this->db->get('product_subcategories_reseller');
        $result = $query->result_array();

        $categories = [];
        foreach ($result as $value) {
            $categories[] = [
                'name' => $value['cat'] .' - '. $value['sub'],
                'id' => $value['id'],
                'slug' => $value['slug']
            ];
        }
        return $categories;
    }
	
	function validate_product_name($product_name){
		$this->db->select('id');
		$this->db->where('name',$product_name);
		$query = $this->db->get('products');
		if($query->num_rows == 1){
			$result = $query->row();
			return 1;
		}else{
			return 0;
		}
	}
	
	function get_product_name($product_id){
		$this->db->select('name');
		$this->db->where('id',$product_id);
		$query = $this->db->get('products');
		if($query->num_rows == 1){
			$result = $query->row();
			return $result->name;
		}else{
			return 0;
		}
	}
	
	function get_all_product($num=10, $start=0){
		$this->db->limit($num, $start);
		$this->db->where('status !=', 'deleted');
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('products');
		$result = $query->result_array();
		return $result;
		//var_dump($result);die();
	}

    function get_all_not_imported_product($num=10, $start=0){
        $this->db->limit($num, $start);
        $this->db->where('status !=', 'deleted');
        $this->db->where('imported_product', '0');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('products');
        $result = $query->result_array();
        return $result;
        //var_dump($result);die();
    }
	
	function get_product_count(){
		$this->db->select('id');
		$this->db->where('status !=', 'deleted');
		$this->db->from('products');
		$query = $this->db->get();
		return $query->num_rows();		
	}

    function get_product_count_without_import(){
        $this->db->select('id');
        $this->db->where('status !=', 'deleted');
        $this->db->where('imported_product', '0');
        $this->db->from('products');
        $query = $this->db->get();
        return $query->num_rows();
    }
	
	function get_search_product($name, $num=10, $start=0){
		$this->db->like('name', $name);
		$this->db->where('status !=', 'deleted');
		$this->db->limit($num, $start);		
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('products');
		$result = $query->result_array();
		return $result;
	}
	
	function get_search_product_count($name){
		$this->db->select('id');
		$this->db->like('name', $name);
		$this->db->where('status !=', 'deleted');
		$this->db->from('products');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function get_is_details($product_class) {
		// From here we will get the class's realm details!
		$this->db->select('realm');
		$this->db->where('id', $product_class);
		$this->db->limit(1);
		$query = $this->db->get('is_classes');
		$result = $query->result_array();
		$realm = $result[0]['realm'];
	
		if ($realm) {
			// Now we get realm user and password
			$this->db->select('user, pass');
			$this->db->where('realm', $realm);
			$query = $this->db->get('realms');
			$result = $query->result_array();
			$user = $result[0]['user'];
			$password = $result[0]['pass'];
	
			$data = array (
					'user' => "$user@$realm",
					'pass' => $password,
			);
			return $data;
		}
		return false;
	}
	
	// Get's product class from Product ID
	function get_is_class($product_id) {
		$this->db->select('class');
		$this->db->where('id', $product_id);
		$this->db->limit(1);
		$query = $this->db->get('products');
		$result = $query->result_array();
		$product_class = $result[0]['class'];
	
		return $product_class;
	}
	
	//get realm with product id
	function get_product_realm($product_id) {
		//$product_class = $this->get_is_class($product_id);

        $product_class_id =  $this->get_product_class_id($product_id);
		$this->db->select('realm');
		$this->db->where('table_id', $product_class_id );
		$query = $this->db->get('is_classes');
		$result = $query->first_row('array');
		return $result['realm'];


	}

    function get_order_data($order_id) {
        $this->db->where('id', $order_id);
        $query = $this->db->get('orders');
        $result = $query->first_row('array');
        return $result;
    }

    function get_class_by_product_id($product_id){

        // Then get product class
        $this->db->select('class');
        $this->db->where('id', $product_id);
        $this->db->limit(1);
        $query = $this->db->get('products');
        $result = $query->result_array();
        $product_class = $result[0]['class'];

        return $product_class;


    }
	
	function get_service_data(){
		$query = $this->db->query('select distinct product from orders');
		$result = $query->result_array();
		foreach ($result as $p){
			$id = $p['product'];
			$this->db->select('name');
			$this->db->where('id', $id);
			$this->db->limit(1);
			$query = $this->db->get('products');
			$result = $query->result_array();
			$product_name = $result[0]['name'];
			
			$product_data[] = array(
				'product_id' => $id,
				'product_name' => $product_name,	
			);
		}
		return $product_data;
	}
	
	function get_status_data(){
		$query = $this->db->query('select distinct status from orders');
		$result = $query->result_array();
		return $result;
	}
	
	function search_by_product($username, $pro_id, $num = 10, $start = 0){
		$this->db->where('user', $username);
		$this->db->where('product', $pro_id);
		$this->db->limit($num, $start);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_role_count($username, $pro_id){
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->where('product', $pro_id);
		$query = $this->db->get('orders');
		return $query->num_rows();
	}
	
	function search_by_status($username, $status, $num = 10, $start = 0){
		$this->db->where('user', $username);
		$this->db->where('status', $status);
		$this->db->limit($num, $start);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_status_count($username, $status){
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->where('status', $status);
		$query = $this->db->get('orders');
		return $query->num_rows();
	}
	
	function search_by_status_pro($username, $status, $pro_id, $num = 10, $start = 0){
		$this->db->where('user', $username);
		$this->db->where('status', $status);
		$this->db->where('product', $pro_id);
		$this->db->limit($num, $start);
		$query = $this->db->get('orders');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_status_pro_count($username, $status, $pro_id){
		$this->db->select('id');
		$this->db->where('user', $username);
		$this->db->where('status', $status);
		$this->db->where('product', $pro_id);
		$query = $this->db->get('orders');
		return $query->num_rows();
	}
	
	function get_cycle_product($cycle, $num = 10, $start){
		$this->db->where('billing_cycle', $cycle);
		$this->db->limit($num, $start);
		$query = $this->db->get('products');
		$result = $query->result_array();
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_cycle_count($cycle){
		$this->db->where('billing_cycle', $cycle);
		$query = $this->db->get('products');
		return $query->num_rows();
	}
	
	function get_product_rand_id()
	{

        do {
            $random = rand(100, 15000);
            $this->db->where('random_num', $random);
            $query = $this->db->get('products');
            $result = $query->num_rows();
        } while ( $result > 0);

        return $random;

        /*

		if($result >0 ){
			$this->validate_product_exist();
		}else{
			$this->db->where('random_num', $random);
			$rand_query = $this->db->get('products');
			$rand_result = $rand_query->num_rows();
			if($rand_result >0){
				$this->validate_product_exist();
			}else{
				return $random;
			}
		}

        */
	}


	
	function get_billing_cycle($product_id, $cycle)
	{
		$this->db->select('id');
		$this->db->where('product_id', $product_id);
		$this->db->where('billing_cycle', $cycle);
		$query = $this->db->get('billing_cycle');
		if($query->result_array()){
			$result = $query->first_row('array');
			return $result['id'];
		}else{
			return $result = '';
		}
	}
	
	function get_billing_cycle_exist($product_id)
	{
		$this->db->select('billing_cycle');
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('billing_cycle');
		if($query->result_array()){
			return $result = $query->result_array();
		}else{
			return $result = '';
		}
	}

    function get_billing_cycle_by_id($product_id)
    {
        $this->db->select('billing_cycle');
        $this->db->where('id', $product_id);
        $query = $this->db->get('billing_cycle');
        if($query->result_array()){
             $result = $query->result_array();
             return $result['billing_cycle'];
        }else{
            return $result = '';
        }
    }


    function get_all_product_list_with_classes() {
        $this->db->select('id, name, class_id, class');
        $query = $this->db->get('products');
        return $query->result_array();
    }

    function get_classes_data_by_name($class_name){
        $this->db->where('id', $class_name);
        $query = $this->db->get('is_classes');
        $query_result = $query->result_array();

        $return_array = false;
        if ( !empty($query_result) )
            $return_array = $query_result[0];


        return $return_array;

    }
    function update_product_class_id($product_id, $class_id){


        $this->db->where('id', $product_id);
        $update_result = $this->db->update('products', array(
                'class_id' => $class_id,
            )
        );

        return $update_result;
    }

    function get_product_class_id($product_id){

        $this->db->select('class_id');
        $this->db->where('id',$product_id);
        $query = $this->db->get('products');

        $class_id = $query->first_row('array');
        $result = false;
        if (!empty($class_id))
            $result = $class_id['class_id'];

        return $result;

    }

    function get_product_undef_nosvc_count(){
        $this->db->select('id');
        $this->db->where('class_id IS NULL');
        $this->db->where('class', 'nosvc');
        $this->db->from('products');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_all_undef_nosvc_product($num=10, $start=0){
        $this->db->limit($num, $start);
        //   $this->db->where('status !=', 'deleted');
        $this->db->where('class_id IS NULL');
        $this->db->where('class', 'nosvc');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('products');
        $result = $query->result_array();
        return $result;
        //var_dump($result);die();
    }

    function get_additional_default_comments($product_id){

        $this->db->select();
        $this->db->where('product_id', $product_id);
        $this->db->from('product_additional_comments');
        $query = $this->db->get();
        $result = $query->result_array();


        if (!empty($result)){

            // make assoc array
            //  "product_id" ,"payment_method" ,  "defualt_comment"
            $comments_array = array();

            foreach ($result as $row){

                $comments_array[$row['payment_method']] = $row['default_comment'];
            }
            return $comments_array;


        }

        return $result;

    }

    function save_additional_default_comment($product_id, $payment_method, $comment_string){

        $this->db->select();
        $this->db->where('product_id', $product_id);
        $this->db->where('payment_method', $payment_method);

        $this->db->from('product_additional_comments');
        $query = $this->db->get();
        $result = $query->result_array();

        $data = array(

            'product_id'      => $product_id,
            'payment_method'  => $payment_method,
            'default_comment' => $comment_string,

        );

        if (empty($result)){

            // insert comment
            $result = $this->db->insert('product_additional_comments',$data);

        } else {

            // update comment
            $this->db->where('id',$result[0]['id']);
            $result = $this->db->update('product_additional_comments', $data);
        }

        return $result;

    }


    // TopUp Functions
    // =================================================================================================
    // =================================================================================================
    // =================================================================================================



    // add_topup_configuration
    function topup_config_handler($data_arr){


        if (isset($data_arr['id']) && !empty($data_arr['id'])) {

            $result = $this->topup_update_config($data_arr['id'], $data_arr);
        } else {

            $result = $this->topup_insert_config($data_arr);
        }

        return $result;
    }

    function topup_get_list($limit = 5, $start = 0, $topup_name = null){

        $this->db->select();
        if (!empty($topup_name))
            $this->db->like('topup_name', $topup_name);

        $this->db->limit($limit, $start);
        $this->db->from('topup_list');
        $this->db->order_by('topup_id', 'desc');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function topup_get_full_list(){

        $this->db->select();
        $this->db->from('topup_list');
        $this->db->order_by('topup_name', 'asc');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    function get_filtered_product($num = 10, $start = 0, $params_array){

        $table = 'products';
        if(isset($params_array['type']) && $params_array['type'] == 'reseller') {
            $table = 'products_reseller';
        }
        // Fetch & reformat 'cycle' nad 'visability' from params
        $cycle = $params_array['cycle'];
        $visibility = $params_array['visibility'];
        $active = '';
        switch ($visibility) {
            case 'visible' : $active = '1'; break;
            case 'hidden'  : $active = '0'; break;
            default : $active = '3'; break;         // value='all'
        }

        // ------------------------------------------------------------


        $this->db->select();
        $this->db->limit($num, $start);

        if ($cycle != 'all') {  // if 'all' -> no matter which billing cycle was assigned

            $cycle = ucfirst($cycle);

            $this->db->from('billing_cycle');
            $this->db->where('billing_cycle.billing_cycle', $cycle);
            $this->db->join('products', 'products.id = billing_cycle.product_id');

        } else {

            $this->db->from($table);
        }


        // visability parameter
        if ($active != '3'){

            $this->db->where($table.'.active',$active);
        }
        $this->db->where($table.'.status !=', 'deleted');
        $this->db->where($table.'.imported_product', '0');
        $this->db->order_by($table.'.id', 'desc');

        $query = $this->db->get();
        $result_array = $query->result_array();


        return $result_array;
    }



    function get_filtered_product_total_count($params_array){

        $table = 'products';
        if(isset($params_array['type']) && $params_array['type'] == 'reseller') {
            $table = 'products_reseller';
        }

        $cycle = $params_array['cycle'];
        $visibility = $params_array['visibility'];
        $active = '';
        switch ($visibility) {
            case 'visible' : $active = '1'; break;
            case 'hidden'  : $active = '0'; break;
            default : $active = '3'; break;         // value='all'
        }


        $this->db->select();

        if ($cycle != 'all') {  // if 'all' -> no matter which billing cycle was assigned

            $cycle = ucfirst($cycle);

            $this->db->from('billing_cycle');
            $this->db->where('billing_cycle.billing_cycle', $cycle);
            $this->db->join('products', 'products.id = billing_cycle.product_id');

        } else {

            $this->db->from($table);
        }


        // visability parameter
        if ($active != '3'){

            $this->db->where($table.'.active',$active);
        }
        $this->db->where($table.'.status !=', 'deleted');
        $this->db->where($table.'.imported_product', '0');
        $this->db->order_by($table.'.id', 'desc');

        $query = $this->db->get();

        $result = $query->num_rows();
        return $result;


    }


    function topup_get_list_count($topup_name = null){

        if (!empty($topup_name)){

            $this->db->like('topup_name',$topup_name);
            $this->db->from('topup_list');

            $query = $this->db->get();
            $count = $query->num_rows();
        } else {

            $count =  $this->db->count_all('topup_list');
        }

        return $count;

    }


    function topup_get_config($id){

        $this->db->select();
        $this->db->where('topup_id', $id);
        $this->db->from('topup_list');

        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
    }

    // returns false if Username alredy exist, or DB error
    function topup_insert_config($data_arr){

        $insert_data = array(

            'topup_name'        => $data_arr['name'],
            'topup_description' => $data_arr['description'],
            'class_id'          => $data_arr['class_id'],
            'class_name'        => $data_arr['class_name'],
            'topup_price'       => $data_arr['price'],

        );

        $name_exist  = $this->topup_check_name($data_arr['name']);
        if ($name_exist){

            $insert_result  = false;
        } else {

            $this->db->insert('topup_list', $insert_data);
            $insert_result = $this->db->insert_id();
        }

        return $insert_result;
    }


    function topup_check_name($name, $topup_id = null){

        $name = trim($name);
        $this->db->select('topup_id');
        $this->db->where('topup_name',$name);

        $query = $this->db->get('topup_list');
        $rows_num = $query->num_rows();

        $answer = false;
        if ($rows_num > 0){

            $exist_id = $query->row_array();
            $answer = true;
            if ( !empty($topup_id)  && ($exist_id['topup_id'] == $topup_id))
                $answer = false;

        }

        return $answer;
    }

    function topup_update_config($topup_id, $data_arr){

        $update_data = array(

            'topup_name'        => $data_arr['name'],
            'topup_description' => $data_arr['description'],
            'class_id'          => $data_arr['class_id'],
            'class_name'        => $data_arr['class_name'],
            'topup_price'       => $data_arr['price'],
        );

        $this->db->where('topup_id', $topup_id);
        $update_result = $this->db->update('topup_list', $update_data);

        return $update_result;
    }

    function topup_remove_config($topup_id){

        // disable all TopUp flags in  products which exist
        $product_update_array = array(
            'topup_active' => '0'
        );
        $this->db->where('topup_id', $topup_id);
        $this->db->or_where('topup_id2', $topup_id);
        $this->db->or_where('topup_id3', $topup_id);
        $update_result = $this->db->update('products', $product_update_array);
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // remove config from DB
        $this->db->where('topup_id', $topup_id);
        $result = $this->db->delete('topup_list');
        return $result;
    }

    function topup_remove_order($topup_order_id){

        // remove config from DB
        $this->db->where('id', $topup_order_id);
        $result = $this->db->delete('topup_orders');
        return $result;
    }


    function topup_handle_payment_methods($topup_id, $data_arr){

        if (empty($topup_id))
            return false;

        // Credit card payment
        // -------------------------------------------
        if (isset($data_arr['credit_card_payment'])){

           $credit_card_exist =  $this->topup_payment_exist($topup_id, 'credit_card');
           if (!$credit_card_exist)
                $this->db->insert('topup_payment_methods',  array( 'topup_id' => $topup_id, 'payment_method' => 'credit_card' ));

        } else {
            $this->topup_payment_methods_delete($topup_id, 'credit_card');
        }


        // Credit_card_auto_payment
        // -------------------------------------------
        if (isset($data_arr['credit_card_auto_payment'])){

            $credit_card_auto_exist =  $this->topup_payment_exist($topup_id, 'credit_card_auto');
            if (!$credit_card_auto_exist)
                $this->db->insert('topup_payment_methods',  array( 'topup_id' => $topup_id, 'payment_method' => 'credit_card_auto' ));

        } else {
            $this->topup_payment_methods_delete($topup_id, 'credit_card_auto');
        }

        // Debit_order_payment
        // -------------------------------------------
        if (isset($data_arr['debit_order_payment'])){

            $debit_order_exist =  $this->topup_payment_exist($topup_id, 'debit_order');
            if (!$debit_order_exist)
                $this->db->insert('topup_payment_methods',  array( 'topup_id' => $topup_id, 'payment_method' => 'debit_order' ));

        } else {

            $this->topup_payment_methods_delete($topup_id, 'debit_order');
        }


        // Eft_payment
        // -------------------------------------------
        if (isset($data_arr['eft_payment'])){

            $eft_exist =  $this->topup_payment_exist($topup_id, 'eft');
            if (!$eft_exist)
                $this->db->insert('topup_payment_methods',  array( 'topup_id' => $topup_id, 'payment_method' => 'eft' ));

        } else {

            $this->topup_payment_methods_delete($topup_id, 'eft');
        }

        return true;

    }

    function topup_payment_methods_delete($topup_id, $method_str){

        $this->db->where('topup_id', $topup_id);
        $this->db->where('payment_method', $method_str);
        $result = $this->db->delete('topup_payment_methods');

        return $result;

    }

    function topup_payment_exist($topup_id, $method_str){

        $this->db->select('id');
        $this->db->where('topup_id', $topup_id);
        $this->db->where('payment_method', $method_str);

        $query = $this->db->get('topup_payment_methods');
        $rows_num = $query->num_rows();

        $answer = false; // row is not exist;
        if ($rows_num > 0 )
            $answer = true;

        return $answer;
    }


    function topup_get_payments($id){

        $this->db->select('payment_method');
        $this->db->where('topup_id', $id);

        $query = $this->db->get('topup_payment_methods');
        $rows = $query->result_array();

        // { [0]=> array(1) { ["payment_method"]=> string(11) "debit_order" } [1]=> array(1) { ["payment_method"]=> string(3) "eft" } }
        $result_array = array();
        foreach ($rows as $payment_method){

            $result_array[$payment_method['payment_method']] = true;
        }

        return $result_array;
    }


    function get_product_topup_options($product_id){

        if (empty($product_id))
            return false;


        $this->db->select('topup_id, topup_id2, topup_id3 , topup_active');
        $this->db->where('id',$product_id);
        $query = $this->db->get('products');

        $result = $query->row_array();
        if (empty($result))
            return false;

        return $result;
    }


    // Get TopUP orders - report
    function get_topup_report($limit = 5, $start = 0, $search_params){



        $this->db->select();
        $this->db->limit($limit, $start);
        $this->db->from('topup_orders');
        if (!empty($search_params)){
            if (!empty($search_params['topup_name'])){
                $this->db->join('topup_list', 'topup_list.topup_id = topup_orders.topup_config_id');
                $this->db->like('topup_list.topup_name',$search_params['topup_name']);
            }
            if (!empty($search_params['user_name'])){
                $this->db->like('topup_orders.username',$search_params['user_name']);
            }
            if (!empty($search_params['from_date_fix'])){
                $this->db->where('topup_orders.order_time >=',$search_params['from_date_fix'] );

            }
            if (!empty($search_params['to_date_fix'])){
                $this->db->where('topup_orders.order_time <=',$search_params['to_date_fix'] );
            }
        }

        $this->db->order_by('topup_orders.order_time', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    // Get TopUP orders - report count
    function get_topup_report_count($search_params){

        $this->db->select();
        $this->db->from('topup_orders');
        if (!empty($search_params)){
            if (!empty($search_params['topup_name'])){
                $this->db->join('topup_list', 'topup_list.topup_id = topup_orders.topup_config_id');
                $this->db->like('topup_list.topup_name',$search_params['topup_name']);
            }
            if (!empty($search_params['user_name'])){
                $this->db->like('topup_orders.username',$search_params['user_name']);
            }
            if (!empty($search_params['from_date_fix'])){
                $this->db->where('topup_orders.order_time >=',$search_params['from_date_fix'] );
            }
            if (!empty($search_params['to_date_fix'])){
                $this->db->where('topup_orders.order_time <=',$search_params['to_date_fix'] );
            }
        }
        $this->db->order_by('order_time', 'DESC');
        $query = $this->db->get();
        $reports_count = $query->num_rows();


        return $reports_count;

    }

    function get_single_topup_order($order_id){

        $this->db->select();
        $this->db->where('id',$order_id);
        $query = $this->db->get('topup_orders');

        $result = $query->row_array();
        return $result;

    }

    function process_product_request($field){

        $val = $this->input->get_post($field, TRUE);
        $val = strip_tags(mysql_real_escape_string($val));
        $val = trim($val);
        return $val;

    }

    function process_get_product_request($field){

        $val = $this->input->get($field, TRUE);
        $val = strip_tags(mysql_real_escape_string($val));
        $val = trim($val);
        return $val;

    }

    function update_topup_order($id,$data){

        $this->db->where('id', $id);
        $update_result = $this->db->update('topup_orders', $data);

        return $update_result;
    }

    function handle_search_array_for_orders(&$data_array){

        /*
         *  $search_array = array(

            'topup_name' => $topup_name,
            'user_name'  => $user_name,
            'from_date'  => $from_date,
            'to_date'    => $to_date,

        );
         */

        if (!empty($data_array['from_date'])){

            $data_array['from_date_fix'] = date("Y-m-d 00:00:00", strtotime($data_array['from_date']));
        }


        if (!empty($data_array['to_date']))
            $data_array['to_date_fix'] = date("Y-m-d 23:59:59", strtotime($data_array['to_date']));

        // base       2015-06-23 00:19:15
        // interface  dd-m-Y


    }

    function handle_base_url_for_paginator($data_array, $base_url){

        foreach($data_array as $key => $value ){

            if (!empty($value))
                $base_url .= '&' . $key . '=' . $value;
        }

        return $base_url;


    }

    // ------------------------------------------------------------------------------


    function get_previous_topup_class($current_topup_id){


        $return_array = array(

            'class_id'   => '',
            'class_name' => '',
            'type'       => '', // topup , service

        );


        if(empty($current_topup_id)){

            return $return_array;
        }

        // -----------------------------------------------------------------

        // get data about current TopUp order
        $topup_order_data = $this->get_single_topup_order($current_topup_id);

        $user_id = $topup_order_data['user_id'];
        $order_id = $topup_order_data['order_id'];
        $product_id = $topup_order_data['product_id'];
        $order_time = $topup_order_data['order_time'];

        $min_order_time   = date('Y-m-01 00:00:00', strtotime($order_time));
        $topup_level      = $topup_order_data['topup_level'];

        $service_class_id = $topup_order_data['service_class_id'];
        $service_class_name = $topup_order_data['service_class_name'];

        $return_array['class_id']   = $service_class_id;
        $return_array['class_name'] = $service_class_name;
        $return_array['type']       = 'service';


        // If topup level is smaller than 2 -> return original service class
        if ($topup_level < 2){

            return $return_array;
        }

        // --------------------------------------------------------------------

        // If there was other TopUp orders
        $this->db->select('');
        $this->db->where('user_id', $user_id);
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);
        $this->db->where('payment_status','completed');
        $this->db->where('order_time <', $order_time);
        $this->db->where('order_time >=', $min_order_time);
        $this->db->where('topup_level <', $topup_level);

        $query = $this->db->get('topup_orders');
        $result_row = $query->row_array();

        if (!empty($result_row)){

            $return_array['class_id']   = $result_row['topup_class_id'];
            $return_array['class_name'] = $result_row['topup_class_name'];
            $return_array['type']       = 'topup';

        }


        return $return_array;

    }


    function get_adsl_account_topup_order($topup_order_id){


        if (empty($topup_order_id))
            return false;

        $this->db->select('adsl_username');
        $this->db->where('id',$topup_order_id);

        $query = $this->db->get('topup_orders');
        $result_row = $query->row_array();

        $adsl_username = $result_row['adsl_username'];

        return $adsl_username;

    }


    function get_realm_from_topup_order($topup_order_id){


        if (empty($topup_order_id))
            return false;

        $adsl_username = $this->get_adsl_account_topup_order($topup_order_id);

        // - parse account -
        $parsed_array = explode('@', $adsl_username);
        $result_realm = $parsed_array[1];

        return $result_realm;

    }

    function get_original_service_by_topup_order($topup_order_id){

        if (empty($topup_order_id))
            return false;

        $this->db->select('*');
        $this->db->from('topup_orders');
        $this->db->join('orders', 'topup_orders.order_id = orders.id');
        $this->db->where('topup_orders.id',$topup_order_id);

        $query = $this->db->get();
        $result_row = $query->row_array();

        return $result_row;


    }

    function get_service_cycle_data_by_topup_order($topup_order_id){

        if (empty ($topup_order_id))
            return false;

        $info_array = $this->get_original_service_by_topup_order($topup_order_id);
        $payment_method = strtolower($info_array['billing_cycle']);
        $date_month = date('Y-m',strtotime($info_array['date']));
        $return_array = array(

            'payment_method' => $payment_method,
            'order_date'    => $date_month,
        );

        return $return_array;

    }

    function get_product_name_fibre($order_id){

        $this->db->select('product_name');
        $this->db->where('order_id', $order_id);
        $query = $this->db->get('fibre_orders');
        $res = $query->row_array();

        return $res['product_name'];
    }

    function productTypes() {

        $query = $this->db->query('SELECT DISTINCT service_type FROM orders');
        $row = $query->result_array();
        $res = ['All'];
        foreach ($row as $status) {
            array_push($res, $status["service_type"]);
        }

        return $res;
    }
}

