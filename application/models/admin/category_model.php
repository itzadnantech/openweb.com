<?php

class Category_model extends CI_Model {


	function get_categories() {
//		$cat_fields = $this->get_category_fields();
//		$get_fields = array();
//		foreach ($cat_fields as $f=>$n) {
//			array_push($get_fields, $f);
//		}
//		$get_fields_c = implode(',', $get_fields);
//		$this->db->select('id,' . $get_fields_c);
		$query = $this->db->get('product_categories');
		return $query->result_array();
	}

    function get_categories_reseller() {

        $query = $this->db->get('product_categories_reseller');
        return $query->result_array();
    }

	function get_category_data ( $category_slug ) {
		$cat_fields = $this->get_category_fields();
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->select('id,' . $get_fields_c);
		$this->db->where('slug', $category_slug);
		$query = $this->db->get('product_categories');
		$result = $query->first_row('array');
		return $result;
	}

	function get_category_fields() {
		$category_fields = array (
			'name' => 'Category Name',
			'slug' => 'URL Slug',
			'desc' => 'Category Description',
            'visible' => 'Product visibility',
            'type' => 'Type'
		);
		return $category_fields;
	}

	function get_subcategories() {
		$cat_fields = $this->get_category_fields();
		unset($cat_fields['type']);
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->select('id,' . $get_fields_c);
		$query = $this->db->get('product_subcategories');
		return $query->result_array();
	}

	function get_subcategory_data ( $category_slug ) {
		$cat_fields = $this->get_subcategory_fields();
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->select('id,' . $get_fields_c);
		$this->db->where('slug', $category_slug);
		$query = $this->db->get('product_subcategories');
		$result = $query->first_row('array');
		return $result;
	}
	
	function get_subcategory_data_by_id($subcategory_id, $type){

	    $table = 'product_subcategories';
	    if($type == 'r') {
	        $table = 'product_subcategories_reseller';
        }

		$this->db->where('id', $subcategory_id);
		$query = $this->db->get($table);
		$result = $query->first_row('array');
		return $result;
	}
	
	function get_category_data_by_id($category_id, $type){
//		$cat_fields = $this->get_category_fields();

		$table = 'product_categories';
		if($type == 'r') {
		    $table = 'product_categories_reseller';
        }
//		$get_fields = array();
//		foreach ($cat_fields as $f=>$n) {
//			array_push($get_fields, $f);
//		}
//		$get_fields_c = implode(',', $get_fields);
//		$this->db->select('id,' . $get_fields_c);
		$this->db->where('id', $category_id);
		$query = $this->db->get($table);
		$result = $query->first_row('array');
		return $result;
	}

	function get_subcategory_fields() {
		$category_fields = array (
			'name' => 'Sub-Category Name',
			'slug' => 'URL Slug',
			'parent' => 'Parent Category',
			'desc' => 'Category Description',
            'visible' => 'Product visibility',
            'type' => 'Type'
		);
		return $category_fields;
	}

	function get_children( $category_id ) {


	}

	function get_category_from_product( $product_id ) {


	}
	
	function get_category_name($category_id, $type = ''){

	    $table = 'product_categories';
	    if($type == 'r') {
	        $table = 'product_categories_reseller';
        }
		$this->db->select('name');
		$this->db->where('id',$category_id);
		$query = $this->db->get($table);
		if($query->num_rows == 1){
			$result = $query->row();
			return $result->name;
		}else{
			return 0;
		}
	}
	
	function  get_subcategory_name($subcategory_id, $type = ''){
	    $table = 'product_subcategories';
	    if($type == 'r') {
	        $table = 'product_subcategories_reseller';
        }
		$this->db->select('name');
		$this->db->where('id',$subcategory_id);
		$query = $this->db->get($table);
		if($query->num_rows == 1){
			$result = $query->row();
			return $result->name;
		}else{
			return 0;
		}
	}
	
	function validate_category_name($name){
		$this->db->select('id');
		$this->db->where('name',$name);
		$query = $this->db->get('product_categories');
		if($query->num_rows == 1){
			$result = $query->row();
			return 1;
		}else{
			return 0;
		}
	}
	
	function validate_subcategory_name($name){
		$this->db->select('id');
		$this->db->where('name',$name);
		$query = $this->db->get('product_subcategories');
		if($query->num_rows == 1){
			$result = $query->row();
			return 1;
		}else{
			return 0;
		}
	}
	
	function get_all_subcategory($num = 10, $start = 0, $reseller = 0){
	    
	  
	    if($this->uri->segment(2) == 'all_subcategory_reseller') {
	        $table = 'product_subcategories_reseller';
        }else{
              $table = 'product_subcategories';
        }
		$this->db->limit($num, $start);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}

    function get_all_subcategory_by_filter($num = 10, $start = 0, $filter, $reseller = 0){

        if($filter == 'visible')
            $filter = 1;

        if($filter == 'hidden')
            $filter = 0;
            
        if($this->uri->segment(2) == 'all_subcategory_reseller') {
	        $table = 'product_subcategories_reseller';
        }else{
              $table = 'product_subcategories';
        }
        

        $this->db->limit($num, $start);
        $this->db->order_by('id', 'desc');
        $this->db->where('visible', $filter);
        $query = $this->db->get($table);
        $result = $query->result_array();
        return $result;

    }
	
	function get_subcategory_count($filter = null, $reseller =0){
	    
	     if($this->uri->segment(2) == 'all_subcategory_reseller') {
	        $table = 'product_subcategories_reseller';
        }else{
              $table = 'product_subcategories';
        }
		$this->db->select('id');

        if(isset($filter) && $filter == 'visible')
            $this->db->where('visible', 1);

        if(isset($filter) && $filter == 'hidden')
            $this->db->where('visible', 0);

		$query = $this->db->get($table);
		return $query->num_rows();
	}
	
	
	
	
	
	
	
	
	
	function get_all_category($num = 10, $start = 0, $reseller = 0){
        
           if($this->uri->segment(2) == 'all_category_reseller') {
	        $table = 'product_categories_reseller';
        }else{
              $table = 'product_categories';
        }

		$this->db->limit($num, $start);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	function get_category_count($filter = null, $reseller = 0){

	    
           if($this->uri->segment(2) == 'all_category_reseller') {
	        $table = 'product_categories_reseller';
        }else{
              $table = 'product_categories';
        }

        $this->db->select('id');

	    if(isset($filter) && $filter == 'visible')
            $this->db->where('visible', 1);

        if(isset($filter) && $filter == 'hidden')
            $this->db->where('visible', 0);

		$query = $this->db->get($table);
		return $query->num_rows();
	}

    function get_all_category_by_filter($num = 10, $start = 0, $filter, $reseller = 0){

	    if($filter == 'visible')
	        $filter = 1;

	    if($filter == 'hidden')
	        $filter = 0;

        
           if($this->uri->segment(2) == 'all_category_reseller') {
	        $table = 'product_categories_reseller';
        }else{
              $table = 'product_categories';
        }

        $this->db->limit($num, $start);
        $this->db->order_by('id', 'desc');
        $this->db->where('visible', $filter);
        $query = $this->db->get($table);
        $result = $query->result_array();
        return $result;
    }
		
}

?>